<?php
require_once __DIR__ . '/compat.php';
require_once __DIR__ . '/connexion.php';

/**
 * Exécute une requête SELECT avec des paramètres liés (prepared statement)
 *
 * @param string $sql    Requête SQL avec des placeholders :nom ou ?
 * @param array  $params Paramètres à lier [':nom' => valeur] ou [valeur1, valeur2]
 * @return array|false   Tableau d'objets ou false si aucun résultat
 */
function SQLSelect(string $sql, array $params = []): array|false
{
    $bdd = new DB();
    $req = $bdd->db->prepare($sql);
    $req->execute($params);
    $result = $req->fetchAll(PDO::FETCH_OBJ);
    return !empty($result) ? $result : false;
}

/**
 * Exécute une requête INSERT/UPDATE/DELETE avec des paramètres liés
 *
 * @param string $sql    Requête SQL avec des placeholders
 * @param array  $params Paramètres à lier
 * @return bool          Succès ou échec
 */
function SQLExecute(string $sql, array $params = []): bool
{
    $bdd = new DB();
    $req = $bdd->db->prepare($sql);
    return $req->execute($params);
}

// --- Fonctions articles ---

/**
 * Quantité en stock d'un article
 */
function getQte(string $art): int
{
    $sql = "SELECT qteStockArticle FROM article WHERE codeArticle = :code";
    $articles = SQLSelect($sql, [':code' => $art]);
    if ($articles) {
        return (int) $articles[0]->qteStockArticle;
    }
    return 0;
}

/**
 * Code d'un article à partir de sa désignation
 */
function getCodeArt(string $desig): string
{
    $sql = "SELECT codeArticle FROM article WHERE designationArticle = :desig";
    $articles = SQLSelect($sql, [':desig' => $desig]);
    if ($articles) {
        return $articles[0]->codeArticle;
    }
    return '';
}

/**
 * Désignation d'un article à partir de son code
 */
function getLibArt(string $code): string
{
    $sql = "SELECT designationArticle FROM article WHERE codeArticle = :code";
    $articles = SQLSelect($sql, [':code' => $code]);
    if ($articles) {
        return $articles[0]->designationArticle;
    }
    return '';
}

// --- Fonctions clients ---

/**
 * Nom d'un client à partir de son code
 */
function getClient(string $code): string
{
    $sql = "SELECT nomClient FROM client WHERE codeClient = :code";
    $clients = SQLSelect($sql, [':code' => $code]);
    if ($clients) {
        return $clients[0]->nomClient;
    }
    return '';
}

// --- Fonctions factures ---

/**
 * Génère la prochaine référence de facture (FAC-YYYY-NNNN)
 */
function refFact(): string
{
    $sql = "SELECT codeFacture FROM facture ORDER BY idFacture DESC LIMIT 1";
    $fact = SQLSelect($sql);
    $year = date('Y');

    if (!$fact) {
        return "FAC-{$year}-0001";
    }

    $lastref = $fact[0]->codeFacture;
    $yearfact = substr($lastref, strpos($lastref, '-') + 1, 4);
    $lastnum = (int) substr($lastref, strrpos($lastref, '-') + 1) + 1;

    if ($year > $yearfact) {
        return "FAC-{$year}-0001";
    }

    $newnum = str_pad((string) $lastnum, 4, '0', STR_PAD_LEFT);
    return "FAC-{$yearfact}-{$newnum}";
}

/**
 * Remise d'une facture
 */
function getRemise(string $code): float
{
    $sql = "SELECT DISTINCT remiseFacture FROM facture WHERE codeFacture = :code";
    $facts = SQLSelect($sql, [':code' => $code]);
    if ($facts) {
        return (float) ($facts[0]->remiseFacture ?? 0);
    }
    return 0.0;
}

/**
 * Date de vente d'une facture
 */
function getDatef(string $code): string
{
    $sql = "SELECT DISTINCT dateFacture FROM facture WHERE codeFacture = :code";
    $facts = SQLSelect($sql, [':code' => $code]);
    if ($facts) {
        return $facts[0]->dateFacture;
    }
    return '';
}

/**
 * N° Commande d'une facture
 */
function getCmdf(string $code): string
{
    $sql = "SELECT DISTINCT cmdFacture FROM facture WHERE codeFacture = :code";
    $facts = SQLSelect($sql, [':code' => $code]);
    if ($facts) {
        return $facts[0]->cmdFacture ?? '';
    }
    return '';
}

/**
 * Total payé d'une facture crédit
 */
function getSumPaidC(string $code): float
{
    $prep = "SELECT codeFacture FROM facture WHERE facture_codeTypeF = 'CREDIT' AND codeFacture = :code";
    $prepas = SQLSelect($prep, [':code' => $code]);
    if (!$prepas) {
        return 0.0;
    }

    $cd = $prepas[0]->codeFacture;
    $sql = "SELECT SUM(montantReglement) AS paid FROM reglement WHERE reglement_codeFacture = :code";
    $facts = SQLSelect($sql, [':code' => $cd]);
    if ($facts && $facts[0]->paid !== null) {
        return (float) $facts[0]->paid;
    }
    return 0.0;
}

/**
 * Brut à payer d'une facture
 */
function getBrut(string $code): float
{
    $sql = "SELECT SUM(totalFacture) AS bap FROM facture WHERE codeFacture = :code";
    $facts = SQLSelect($sql, [':code' => $code]);
    if ($facts && $facts[0]->bap !== null) {
        return (float) $facts[0]->bap;
    }
    return 0.0;
}

/**
 * Net à payer d'une facture (brut - remise)
 */
function getNet(string $code): float
{
    $brut = getBrut($code);
    $remise = getRemise($code);
    return $brut - $remise;
}

/**
 * Montant TVA d'une facture
 */
function getTVA(string $code): float
{
    $sql = "SELECT DISTINCT tvaFacture FROM facture WHERE codeFacture = :code";
    $facts = SQLSelect($sql, [':code' => $code]);
    if ($facts) {
        $vtva = (int) $facts[0]->tvaFacture;
        if ($vtva === 1) {
            $net = getNet($code);
            $ctva = getcoefTVA();
            return round($net * $ctva);
        }
    }
    return 0.0;
}

/**
 * TTC d'une facture (net + TVA)
 */
function getTTC(string $code): float
{
    $net = getNet($code);
    $mtva = getTVA($code);
    return $net + $mtva;
}

/**
 * Prochaine date de paiement d'une facture crédit
 */
function getNextDate(string $code): string
{
    $sql = "SELECT DISTINCT facture_codeModalite FROM facture WHERE codeFacture = :code";
    $infos = SQLSelect($sql, [':code' => $code]);
    if (!$infos) {
        return '';
    }

    $mod = $infos[0]->facture_codeModalite;

    $sql1 = "SELECT * FROM reglement WHERE reglement_codeFacture = :code";
    $reglements = SQLSelect($sql1, [':code' => $code]);
    $nbr = $reglements ? count($reglements) : 0;

    $datef = getDatef($code);
    if (empty($datef)) {
        return '';
    }

    $coef = $nbr + 1;

    switch ($mod) {
        case '1M':
            $add = $coef . ' months';
            break;
        case '3M':
            $add = (3 * $coef) . ' months';
            break;
        case '6M':
            $add = (6 * $coef) . ' months';
            break;
        case '12M':
            $add = $coef . ' years';
            break;
        case 'CC':
            // 2 semaines comme échéance
            $add = ($coef * 14) . ' days';
            break;
        default:
            return '';
    }

    $conv = date_create($datef);
    $last = date_add($conv, date_interval_create_from_date_string($add));
    return date_format($last, 'Y-m-d');
}

// --- Fonctions informations société (table entete) ---

/**
 * Récupère un champ de la table entete (ligne id=1)
 */
function getEnteteField(string $field): string
{
    $sql = "SELECT * FROM entete WHERE id = 1";
    $infos = SQLSelect($sql);
    if ($infos && property_exists($infos[0], $field)) {
        return (string) $infos[0]->$field;
    }
    return '';
}

function getNom(): string
{
    return getEnteteField('nom');
}

function getAdr(): string
{
    return getEnteteField('adresse');
}

function getLogo(): string
{
    return getEnteteField('logo');
}

function getTel1(): string
{
    return getEnteteField('tel1');
}

function getTel2(): string
{
    return getEnteteField('tel2');
}

function getBank(): string
{
    return getEnteteField('banque');
}

function getRCCM(): string
{
    return getEnteteField('rccm');
}

function getIFU(): string
{
    return getEnteteField('ifu');
}

function getcoefTVA(): float
{
    $sql = "SELECT ctva FROM entete WHERE id = 1";
    $infos = SQLSelect($sql);
    if ($infos) {
        return (float) $infos[0]->ctva;
    }
    return 0.0;
}

/**
 * Dernier règlement d'une facture crédit
 */
function getLastVers(string $code): float
{
    $sql = "SELECT montantReglement FROM reglement WHERE reglement_codeFacture = :code ORDER BY idReglement DESC LIMIT 1";
    $infos = SQLSelect($sql, [':code' => $code]);
    if ($infos) {
        return (float) $infos[0]->montantReglement;
    }
    return 0.0;
}
