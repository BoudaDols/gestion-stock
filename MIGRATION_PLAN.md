# Plan de Migration — GSF (Gestion de Stock & Facturation)

## Objectif

Moderniser l'application GSF en mettant à jour toutes les technologies vers leurs versions actuelles :

| Composant | Version actuelle | Version cible |
|-----------|-----------------|---------------|
| PHP | 5.6.3 | 8.3+ |
| MySQL | 5.6.21 | 8.0+ |
| Bootstrap | 3.3.2 | 5.3+ |
| jQuery | 2.1.3 | 3.7+ |
| AdminLTE | 2.x | 4.x (Bootstrap 5) |
| DataTables | 1.x | 2.x |
| Font Awesome | 4.3.0 | 6.x |
| Highcharts | ancienne | 11.x |

## Approche générale

- Modifications **in-place** (pas de nouveau projet)
- Migration **par phases** : chaque phase laisse l'application fonctionnelle
- Commit git **avant chaque phase** pour permettre un rollback
- Tests manuels après chaque phase

---

## Phase 1 — Sécurité & PHP 8.3 (PRIORITAIRE)

### 1.1 Correction des injections SQL

**Problème actuel** : Toutes les requêtes utilisent l'interpolation de variables directement dans les chaînes SQL. Exemple dans `php/fonction.php` :

```php
// AVANT (vulnérable)
$sql = "SELECT * FROM article WHERE codeArticle='$art'";
$req = $bdd->db->prepare($sql);
$req->execute();
```

**Solution** : Utiliser des requêtes préparées avec des paramètres liés.

```php
// APRÈS (sécurisé)
$sql = "SELECT * FROM article WHERE codeArticle = :code";
$req = $bdd->db->prepare($sql);
$req->execute([':code' => $art]);
```

**Fichiers à modifier** :
- `php/fonction.php` — toutes les fonctions (getQte, getCodeArt, getLibArt, getClient, refFact, getRemise, getDatef, getCmdf, getSumPaidC, getBrut, getTVA, getNextDate, getNom, getAdr, etc.)
- `index.php` — requête de login
- `accueil.php` — requêtes du dashboard
- `patterning.php` — requêtes du menu dynamique
- Tous les fichiers `vente*.php`, `caisse*.php`, `maj*.php`, `popup*.php`, `liste*.php`, `etat*.php`
- Tous les fichiers `ajax/*.php`

**Méthode** : Réécrire la fonction `SQLSelect()` pour accepter des paramètres :

```php
// Nouvelle signature
function SQLSelect(string $sql, array $params = []): array|false
{
    $bdd = new DB();
    $req = $bdd->db->prepare($sql);
    $req->execute($params);
    return $req->fetchAll(PDO::FETCH_OBJ) ?: false;
}
```

### 1.2 Remplacement du hachage MD5

**Problème actuel** : `index.php` utilise `md5()` pour hacher les mots de passe. MD5 est cassé depuis des années.

```php
// AVANT
$pwd = md5($_POST['pwd']);
$sql = "SELECT * FROM user WHERE loginUser='$login' AND mdpUser='$pwd'";
```

**Solution** : Utiliser `password_hash()` et `password_verify()` (disponibles depuis PHP 5.5).

```php
// APRÈS
$sql = "SELECT * FROM user WHERE loginUser = :login AND statutCompteUser = 1";
$repons = SQLSelect($sql, [':login' => $login]);

if (!empty($repons)) {
    $user = $repons[0];
    if (password_verify($_POST['pwd'], $user->mdpUser)) {
        // Connexion réussie
    }
}
```

**Migration des mots de passe existants** :
1. Ajouter un script `migration_passwords.php` qui :
   - Lit tous les utilisateurs
   - Pour chaque utilisateur, demande de saisir un nouveau mot de passe (ou utilise un mot de passe temporaire)
   - Stocke le hash avec `password_hash($pwd, PASSWORD_DEFAULT)`
2. Augmenter la colonne `mdpUser` à `varchar(255)` (déjà le cas dans le schéma actuel)
3. Alternative : garder temporairement le MD5 pour la transition, puis forcer le changement au premier login

**Fichiers à modifier** :
- `index.php` — authentification
- `majuser.php` — création/modification utilisateur
- `moncompte.php` — changement de mot de passe
- `bd_gestockevent24.sql` — mettre à jour les hash existants

### 1.3 Mise à jour de la connexion PDO

**Fichier** : `php/connexion.php`

```php
// AVANT
$this->db = new PDO('mysql:host='.$this->host.';dbname='.$this->db_name, $this->user, $this->pass, array(
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8',
    PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING
));
```

```php
// APRÈS
$this->db = new PDO(
    "mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4",
    $this->user,
    $this->pass,
    [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]
);
```

**Changements** :
- `charset=utf8mb4` dans le DSN (remplace `SET NAMES UTF8`)
- Mode erreur `EXCEPTION` au lieu de `WARNING`
- Désactiver l'émulation des requêtes préparées (sécurité)
- Fetch mode par défaut en objet

### 1.4 Sécurisation des sessions

**Fichier** : Créer `php/session.php` (inclus en haut de chaque page)

```php
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_secure'   => true,  // si HTTPS
        'cookie_samesite' => 'Strict',
        'use_strict_mode' => true,
    ]);
}
```

**Fichiers à modifier** : Tous les fichiers qui font `session_start()` :
- `index.php`
- `accueil.php`
- `patterning.php`
- Remplacer les blocs `if(!isset($_SESSION)) { session_start(); }` par `require_once('php/session.php');`

### 1.5 Compatibilité PHP 8.3

**Changements nécessaires** :

| Problème | Fichiers concernés | Solution |
|----------|-------------------|----------|
| `each()` supprimée en PHP 8.0 | Vérifier tous les fichiers | Remplacer par `foreach` |
| `create_function()` supprimée | Vérifier tous les fichiers | Remplacer par closures |
| `${}` interpolation dépréciée en 8.2 | Tous les fichiers avec `"...$var..."` | Utiliser `{$var}` ou concaténation |
| Passage `null` aux fonctions internes | Multiples fichiers | Ajouter des vérifications `?? ''` |
| Types implicites des paramètres | `php/connexion.php` | Ajouter les types explicites |
| `utf8_encode()`/`utf8_decode()` supprimées en 8.2 | Vérifier | Utiliser `mb_convert_encoding()` |

### 1.6 Extraction de la configuration

**Créer** `php/config.php` :

```php
<?php
return [
    'db' => [
        'host'    => 'localhost',
        'name'    => 'bd_gestockevent24',
        'user'    => 'root',
        'pass'    => '',
        'charset' => 'utf8mb4',
    ],
    'app' => [
        'name'    => 'GSF',
        'version' => '3.0',
    ],
];
```

**Modifier** `php/connexion.php` pour lire depuis ce fichier au lieu d'avoir les identifiants en dur dans la classe.

---

## Phase 2 — Frontend (Bootstrap 5 + AdminLTE 4)

### 2.1 Remplacement d'AdminLTE 2 par AdminLTE 4

AdminLTE 4 est basé sur Bootstrap 5. C'est le moyen le plus simple de migrer le thème sans tout refaire.

**Actions** :
1. Supprimer les dossiers `bootstrap/`, `dist/`, `plugins/`, `build/`
2. Télécharger AdminLTE 4 depuis https://github.com/ColorlibHQ/AdminLTE/releases
3. Copier les dossiers `dist/` et `plugins/` d'AdminLTE 4
4. Bootstrap 5 est inclus dans AdminLTE 4, pas besoin d'un dossier `bootstrap/` séparé

### 2.2 Mise à jour du template principal (`patterning.php`)

**Changements CSS Bootstrap 3 → 5** :

| Bootstrap 3 | Bootstrap 5 | Contexte |
|-------------|-------------|----------|
| `col-xs-*` | `col-*` | Grille (xs supprimé, c'est le défaut) |
| `col-sm-*` | `col-sm-*` | Inchangé |
| `pull-left` / `pull-right` | `float-start` / `float-end` | Flottants |
| `text-left` / `text-right` | `text-start` / `text-end` | Alignement texte |
| `hidden-xs` | `d-none d-sm-block` | Responsive visibility |
| `btn-default` | `btn-secondary` | Boutons |
| `panel` / `panel-body` | `card` / `card-body` | Panneaux |
| `well` | `card` ou `bg-light p-3` | Conteneurs |
| `label label-*` | `badge bg-*` | Labels/badges |
| `img-responsive` | `img-fluid` | Images |
| `img-circle` | `rounded-circle` | Images rondes |
| `data-toggle` | `data-bs-toggle` | Attributs JS |
| `data-dismiss` | `data-bs-dismiss` | Attributs JS |
| `form-group` | `mb-3` | Espacement formulaires |
| `glyphicon glyphicon-*` | Supprimé → utiliser Font Awesome 6 ou Bootstrap Icons | Icônes |

**Fichiers à modifier** :
- `patterning.php` (template principal — le plus gros changement)
- `index.php` (page de login)
- Tous les fichiers de contenu (`vente*.php`, `maj*.php`, `caisse*.php`, `popup*.php`, etc.)

### 2.3 Mise à jour de jQuery (2.1.3 → 3.7)

**Pourquoi garder jQuery** : DataTables et les appels AJAX existants en dépendent. On le conserve pour cette migration, on pourra le retirer plus tard.

**Actions** :
1. Remplacer `plugins/jQuery/jQuery-2.1.3.min.js` par jQuery 3.7.1 (CDN ou fichier local)
2. Vérifier les incompatibilités :
   - `.size()` → `.length`
   - `.andSelf()` → `.addBack()`
   - `.live()` / `.die()` → `.on()` / `.off()` (probablement déjà OK)
   - `$.ajax` avec `success` au lieu de `.done()` — toujours supporté
   - jQuery UI : mettre à jour vers 1.13+

**Fichiers à modifier** :
- `patterning.php` — inclusion du script
- `index.php` — inclusion du script
- Tous les fichiers `ajax/*.php` côté client

### 2.4 Mise à jour de DataTables (1.x → 2.x)

**Actions** :
1. Supprimer le dossier `datatables/`
2. Télécharger DataTables 2.x depuis https://datatables.net/download/
3. Inclure via CDN ou fichiers locaux :
   ```html
   <link href="https://cdn.datatables.net/2.0.0/css/dataTables.bootstrap5.min.css" rel="stylesheet">
   <script src="https://cdn.datatables.net/2.0.0/js/dataTables.min.js"></script>
   <script src="https://cdn.datatables.net/2.0.0/js/dataTables.bootstrap5.min.js"></script>
   ```
4. Inclure les extensions (Buttons, Export) si nécessaire

**Fichiers à modifier** :
- `patterning.php` — chargement des CSS/JS
- Tous les fichiers qui initialisent un DataTable (`liste*.php`, `etat*.php`, `maj*.php`, etc.)

### 2.5 Mise à jour de Font Awesome (4.3 → 6.x)

**Changements de syntaxe** :

| Font Awesome 4 | Font Awesome 6 |
|----------------|----------------|
| `fa fa-*` | `fa-solid fa-*` (ou `fas fa-*`) |
| `fa fa-money` | `fa-solid fa-money-bill` |
| `fa fa-bar-chart-o` | `fa-solid fa-chart-bar` |
| `fa fa-edit` | `fa-solid fa-pen-to-square` |
| `fa fa-laptop` | `fa-solid fa-laptop` |
| `fa fa-table` | `fa-solid fa-table` |

**Actions** :
1. Remplacer le CDN dans `patterning.php` et `index.php`
2. Chercher/remplacer toutes les classes `fa fa-` dans tous les fichiers PHP
3. Mettre à jour la table `menu` en base de données (colonne `iconeMenu`)

### 2.6 Mise à jour de Highcharts

**Actions** :
1. Supprimer `php/Highcharts/`
2. Charger Highcharts 11.x via CDN :
   ```html
   <script src="https://code.highcharts.com/highcharts.js"></script>
   <script src="https://code.highcharts.com/modules/exporting.js"></script>
   ```
3. L'API Highcharts est rétro-compatible — les graphiques existants dans `accueil.php` et `tabbord.php` devraient fonctionner sans modification majeure

### 2.7 Suppression des polyfills IE8/IE9

Supprimer de `patterning.php` et `index.php` :
```html
<!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
<![endif]-->
```

Supprimer aussi :
- `plugins/fastclick/` (inutile sur les navigateurs modernes)
- `plugins/sparkline/` (si non utilisé)
- `plugins/jvectormap/` (si non utilisé)
- `plugins/morris/` (remplacé par Highcharts)

---

## Phase 3 — Base de données (MySQL 8.0)

### 3.1 Changement de charset

**Problème actuel** : Toutes les tables utilisent `CHARSET=latin1`. Cela pose des problèmes avec les caractères spéciaux et les emojis.

**Actions** :

1. Modifier le dump SQL `bd_gestockevent24.sql` :
   - Remplacer `DEFAULT CHARSET=latin1` par `DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci`
   - Remplacer `SET NAMES utf8` par `SET NAMES utf8mb4`

2. Script de migration pour une base existante :
   ```sql
   ALTER DATABASE bd_gestockevent24 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

   ALTER TABLE access CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ALTER TABLE annuler_facture CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ALTER TABLE article CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ALTER TABLE client CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ALTER TABLE clientbck CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ALTER TABLE compte CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ALTER TABLE drop_entreestock CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ALTER TABLE entete CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ALTER TABLE entreestock CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ALTER TABLE facture CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ALTER TABLE fournisseur CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ALTER TABLE magasin CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ALTER TABLE menu CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ALTER TABLE menuitem CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ALTER TABLE modalite CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ALTER TABLE rebus CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ALTER TABLE reglement CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ALTER TABLE retour CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ALTER TABLE typearticle CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ALTER TABLE typefacture CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ALTER TABLE user CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

### 3.2 Compatibilité MySQL 8.0

**Vérifications** :

| Élément | Problème potentiel | Solution |
|---------|-------------------|----------|
| Plugin d'authentification | MySQL 8 utilise `caching_sha2_password` par défaut | Configurer PHP PDO ou utiliser `mysql_native_password` |
| Mots réservés | `rank`, `groups`, `system` sont réservés en MySQL 8 | Vérifier les noms de colonnes/tables (aucun conflit détecté dans ce projet) |
| `int(10)` display width | Déprécié en MySQL 8.0.17 | Retirer les tailles d'affichage : `int(10)` → `int` |
| `GROUP BY` implicite | `sql_mode` plus strict par défaut | Vérifier les requêtes avec `GROUP BY` |

### 3.3 Ajout de clés étrangères (recommandé)

Le schéma actuel n'a aucune contrainte de clé étrangère. Exemple d'ajout :

```sql
ALTER TABLE facture
  ADD CONSTRAINT fk_facture_client
  FOREIGN KEY (facture_codeClient) REFERENCES client(codeClient);

ALTER TABLE facture
  ADD CONSTRAINT fk_facture_article
  FOREIGN KEY (facture_codeArticle) REFERENCES article(codeArticle);

ALTER TABLE entreestock
  ADD CONSTRAINT fk_entree_article
  FOREIGN KEY (entree_codeArticle) REFERENCES article(codeArticle);

ALTER TABLE entreestock
  ADD CONSTRAINT fk_entree_fournisseur
  FOREIGN KEY (entree_codeFournisseur) REFERENCES fournisseur(codeFournisseur);

ALTER TABLE access
  ADD CONSTRAINT fk_access_compte
  FOREIGN KEY (access_codeCompte) REFERENCES compte(codeCompte);

ALTER TABLE menuitem
  ADD CONSTRAINT fk_menuitem_menu
  FOREIGN KEY (menu_idMenu) REFERENCES menu(idMenu);
```

**Note** : Nécessite d'abord de nettoyer les données orphelines existantes.

---

## Phase 4 — Améliorations d'architecture (optionnel mais recommandé)

### 4.1 Protection CSRF sur les formulaires

**Problème actuel** : Aucun token CSRF. Un attaquant peut forcer un utilisateur connecté à soumettre un formulaire à son insu.

**Solution** : Ajouter un token dans chaque formulaire.

```php
// Dans php/session.php — génération du token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Dans chaque formulaire
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

// Vérification côté serveur avant traitement
if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'] ?? '')) {
    die('Token CSRF invalide');
}
```

**Fichiers à modifier** : Tous les fichiers contenant `<form` et un traitement `$_POST` :
- `index.php`, `ventecomptant.php`, `ventecredit.php`, `venteavoir.php`
- `majstock.php`, `majarticle.php`, `majclient.php`, `majuser.php`, `majcompte.php`
- `majentete.php`, `majfournisseur.php`, `majtypearticle.php`
- `caissedepense.php`, `caisseentree.php`
- `moncompte.php`, `majacces.php`

### 4.2 Validation des entrées

**Problème actuel** : Aucune validation côté serveur. Les données sont insérées telles quelles.

**Solution** : Ajouter un fichier `php/validation.php` avec des fonctions de validation :

```php
<?php
function validateRequired(string $value, string $field): ?string
{
    if (trim($value) === '') {
        return "Le champ {$field} est obligatoire.";
    }
    return null;
}

function validateNumeric(mixed $value, string $field): ?string
{
    if (!is_numeric($value)) {
        return "Le champ {$field} doit être un nombre.";
    }
    return null;
}

function validateDate(string $value, string $field): ?string
{
    $d = DateTime::createFromFormat('Y-m-d', $value);
    if (!$d || $d->format('Y-m-d') !== $value) {
        return "Le champ {$field} doit être une date valide (AAAA-MM-JJ).";
    }
    return null;
}

function sanitizeString(string $value): string
{
    return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
}
```

### 4.3 Gestion centralisée des erreurs

Créer `php/error_handler.php` :

```php
<?php
set_error_handler(function (int $errno, string $errstr, string $errfile, int $errline) {
    error_log("[{$errno}] {$errstr} in {$errfile}:{$errline}");
    if (in_array($errno, [E_ERROR, E_USER_ERROR])) {
        http_response_code(500);
        echo "Une erreur est survenue. Veuillez réessayer.";
        exit;
    }
    return true;
});

set_exception_handler(function (Throwable $e) {
    error_log($e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
    http_response_code(500);
    echo "Une erreur est survenue. Veuillez réessayer.";
    exit;
});
```

---

## Ordre d'exécution et estimation

| Phase | Étape | Priorité | Complexité | Fichiers impactés |
|-------|-------|----------|------------|-------------------|
| 1.1 | Injections SQL | CRITIQUE | Élevée | ~50 fichiers |
| 1.2 | Hachage MD5 | CRITIQUE | Moyenne | 3 fichiers + BDD |
| 1.3 | Connexion PDO | Haute | Faible | 1 fichier |
| 1.4 | Sessions sécurisées | Haute | Faible | ~5 fichiers |
| 1.5 | Compatibilité PHP 8.3 | Haute | Moyenne | ~50 fichiers |
| 1.6 | Extraction config | Moyenne | Faible | 2 fichiers |
| 2.1 | AdminLTE 4 | Haute | Élevée | Dossiers entiers |
| 2.2 | Template Bootstrap 5 | Haute | Élevée | ~50 fichiers |
| 2.3 | jQuery 3.7 | Moyenne | Faible | 2 fichiers |
| 2.4 | DataTables 2.x | Moyenne | Moyenne | ~15 fichiers |
| 2.5 | Font Awesome 6 | Faible | Moyenne | ~10 fichiers + BDD |
| 2.6 | Highcharts 11 | Faible | Faible | 2-3 fichiers |
| 2.7 | Nettoyage polyfills | Faible | Faible | 2 fichiers |
| 3.1 | Charset utf8mb4 | Haute | Faible | BDD + 1 fichier |
| 3.2 | Compatibilité MySQL 8 | Haute | Faible | BDD + vérification |
| 3.3 | Clés étrangères | Faible | Moyenne | BDD uniquement |
| 4.1 | CSRF | Moyenne | Moyenne | ~20 fichiers |
| 4.2 | Validation entrées | Moyenne | Moyenne | ~20 fichiers |
| 4.3 | Gestion erreurs | Moyenne | Faible | 1 fichier + includes |

---

## Checklist de migration

### Avant de commencer
- [ ] Initialiser un dépôt git (si pas déjà fait)
- [ ] Faire un commit de l'état actuel
- [ ] Installer PHP 8.3 localement (MAMP Pro, Homebrew, ou Docker)
- [ ] Installer MySQL 8.0 localement
- [ ] Faire un backup de la base de données

### Phase 1
- [ ] Commit : "chore: backup before PHP modernization"
- [ ] 1.3 — Mettre à jour `php/connexion.php`
- [ ] 1.6 — Créer `php/config.php`
- [ ] 1.4 — Créer `php/session.php` et l'inclure partout
- [ ] 1.1 — Modifier `SQLSelect()` + toutes les requêtes fichier par fichier
- [ ] 1.2 — Remplacer MD5 par password_hash
- [ ] 1.5 — Corriger les incompatibilités PHP 8.3
- [ ] Tester toutes les fonctionnalités
- [ ] Commit : "feat: PHP 8.3 modernization + security fixes"

### Phase 2
- [ ] Commit : "chore: backup before frontend update"
- [ ] 2.1 — Télécharger et installer AdminLTE 4
- [ ] 2.2 — Réécrire `patterning.php` et `index.php`
- [ ] 2.3 — Mettre à jour jQuery
- [ ] 2.4 — Mettre à jour DataTables
- [ ] 2.2 — Mettre à jour chaque page de contenu (Bootstrap classes)
- [ ] 2.5 — Migrer Font Awesome
- [ ] 2.6 — Mettre à jour Highcharts
- [ ] 2.7 — Nettoyer les polyfills
- [ ] Tester toutes les pages visuellement
- [ ] Commit : "feat: frontend modernization Bootstrap 5 + AdminLTE 4"

### Phase 3
- [ ] Commit : "chore: backup before database migration"
- [ ] 3.1 — Convertir toutes les tables en utf8mb4
- [ ] 3.2 — Tester avec MySQL 8.0
- [ ] 3.3 — Ajouter les clés étrangères (optionnel)
- [ ] Mettre à jour `bd_gestockevent24.sql`
- [ ] Commit : "feat: database migration MySQL 8.0 + utf8mb4"

### Phase 4
- [ ] 4.1 — Ajouter protection CSRF
- [ ] 4.2 — Ajouter validation des entrées
- [ ] 4.3 — Ajouter gestion centralisée des erreurs
- [ ] Commit : "feat: security hardening (CSRF, validation, error handling)"

---

## Stack technique cible (après migration)

| Composant | Version |
|-----------|---------|
| PHP | 8.3+ |
| MySQL | 8.0+ |
| Bootstrap | 5.3 |
| jQuery | 3.7 |
| AdminLTE | 4.x |
| DataTables | 2.x |
| Highcharts | 11.x |
| Font Awesome | 6.x |
