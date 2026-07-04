<?php
require_once('php/session.php');
require_once('php/fonction.php');

$pagetitle = "GSF | Entrée en caisse";
$pagestitle = "Mise à jour des entrées en caisse";
$bcrumb = "Caisse > Entrée en Caisse";
$display = "style='display:none'";

$btnaction = "insert";
$disabled = "";
$tableau = "entier";
$msg = "";
$classmsg = "";
$button = "";
$action = "";

$montant = "";
$objet = "";

//Pagination
$parpage = 10;
$countResult = SQLSelect("SELECT * FROM reglement WHERE statutReglement = 'C' AND reglement_codeFacture = ''");
$nblignes = $countResult ? count($countResult) : 0;
$nbpages = ceil($nblignes / $parpage);

if (isset($_GET['action'])) {
    $getaction = $_GET['action'];
    $getid = $_GET['id'];

    if ($getaction == "edit") {
        $edits = SQLSelect("SELECT * FROM reglement WHERE idReglement = :id", [':id' => $getid]);
        if ($edits) {
            $montant = $edits[0]->montantReglement;
            $objet = $edits[0]->objetReglement;
        }
        $btnaction = "update";
        $disabledc = "disabled";
        $display = "style='display:inline'";
    }
}

if (isset($_POST['btnsubmit'])) {
    $btnaction = $_POST['btnaction'];
    $montant = $_POST['montant'];
    $objet = $_POST['objet'];

    if ($btnaction == "insert") {
        if (!is_numeric($montant)) {
            $msg = "Vérifiez la saisie du montant de la dépense!<br>
            <input type='button' value='Retour' class='btn btn-info' onClick='history.back()'";
            $classmsg = "alert alert-warning";
            $button = "<button type='button' class='close' data-dismiss='alert' 
            aria-hidden='true'><i class='glyphicon glyphicon-off'></i></button>";
        } else {
            $result = SQLExecute("INSERT INTO reglement (dateReglement, montantReglement, objetReglement,
                statutReglement, reglement_codeFacture) VALUES (:date, :mtt, :obj, :stat, :fac)", [
                ':date' => date("Y-m-d"), ':mtt' => $montant, ':obj' => $objet,
                ':stat' => 'E', ':fac' => ''
            ]);
            if ($result) {
                $msg = "Caisse créditée!";
                $classmsg = "alert alert-success";
                $action = "<br><br><br><a href='caisseentree.php'><input type='button' 
                class='btn btn-primary' value='NOUVEAU'></a>";
                $disabled = "disabled";
            } else {
                $msg = "Erreur lors de l'opération de caisse";
                $classmsg = "alert alert-warning";
                $action = "<br><br><br><a href='caisseentree.php'><input type='button' 
                class='btn btn-primary' value='NOUVEAU'></a>";
                $disabled = "disabled";
            }
        }
    } else {
        if (!is_numeric($montant)) {
            $msg = "Vérifiez la saisie du montant de la dépense!<br>
            <input type='button' value='Retour' class='btn btn-info' onClick='history.back()'";
            $classmsg = "alert alert-warning";
            $button = "<button type='button' class='close' data-dismiss='alert' 
            aria-hidden='true'><i class='glyphicon glyphicon-off'></i></button>";
        } else {
            $result = SQLExecute("UPDATE reglement SET montantReglement = :mtt,
                objetReglement = :obj WHERE idReglement = :id", [
                ':mtt' => $montant, ':obj' => $objet, ':id' => $getid
            ]);
            if ($result) {
                $msg = "MAJ effectuée!";
                $classmsg = "alert alert-success";
                $action = "<br><br><br><a href='caisseentree.php'><input type='button' 
                class='btn btn-primary' value='NOUVEAU'></a>";
                $disabled = "disabled";
                $display = "style='display:none'";
            } else {
                $msg = "Erreur lors de l'opération de caisse";
                $classmsg = "alert alert-warning";
                $action = "<br><br><br><a href='caisseentree.php'><input type='button' 
                class='btn btn-primary' value='NOUVEAU'></a>";
                $disabled = "disabled";
            }
        }
    }
}

//Navigation pagination
if (isset($_GET['page'])) {
    $pactu = intval($_GET['page']);
    if ($pactu > $nbpages) {
        $pactu = $nbpages;
    }
} else {
    $pactu = 1;
}
$numligne = ($pactu * $parpage) - $parpage + 1;
$first = ($pactu - 1) * $parpage;

if (isset($_POST['btnresearch'])) {
    $rech = $_POST['research'];
    if ($rech == "") {
        $sqlrech = "SELECT * FROM reglement WHERE statutReglement = 'E' AND reglement_codeFacture = '' LIMIT :offset, :limit";
        $sqlrechParams = [':offset' => $first, ':limit' => $parpage];
        $tableau = "entier";
    } else {
        $sqlrech = "SELECT * FROM reglement WHERE statutReglement = 'E' AND reglement_codeFacture = '' AND objetReglement LIKE :rech LIMIT :offset, :limit";
        $sqlrechParams = [':rech' => "%{$rech}%", ':offset' => $first, ':limit' => $parpage];
        $tableau = "rechercher";
    }
}

ob_start();
?>
	<div class="row col-lg-12">
		<div class="box box-primary">
			<div class="box-header">
				<i class="fa fa-adjust"></i>
				<h3 class="box-title">Entrée en caisse</h3>
			</div>
			<form name="entreecaisse" method="POST">
				<div class="box-body">
					<div class="row col-lg-4">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-dollar"></i>
								</div>
								<input type="text" class="form-control" style="width:200px" name="montant" placeholder="Montant" value="<?= $montant;?>" <?= $disabled; ?> required/>
							</div>
						</div>
					</div>
					<div class="row col-lg-8">
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-font"></i>
								</div>
								<input type="text" class="form-control" style="width:450px" name="objet" placeholder="Objet" value="<?= $objet;?>" <?= $disabled; ?> required/>
							</div>
						</div>
					</div>
					<div class="row col-lg-3"></div>
					<div class="row col-lg-3">
						<input type="hidden" name="btnaction" value="<?= $btnaction; ?>">
						<input type="submit" name="btnsubmit" class="btn btn-primary" value="VALIDER" <?= $disabled; ?>>
					</div>
					<div class="row col-lg-3" <?= $display;?> >
						<a href="caisseentree.php">
							<input type="button" name="btncancel" class="btn btn-info" value="ANNULER MODIFICATION ">
						</a>
					</div>
					<div class="row col-lg-3"></div>
				</div>
			</form>
		</div>
	</div>
	
	<div class="row">
		<div class="col-lg-3"></div>
		<div class="col-lg-6">
			<div class="<?=$classmsg; ?>" role="alert">
				<?=$button; ?>
				<?=$msg; ?>
				<?=$action; ?>
			</div>
		</div>
		<div class="col-lg-3"></div>
	</div>
	
	<?php
		if ($tableau == "entier") {
			$entrees = SQLSelect("SELECT * FROM reglement WHERE statutReglement = 'E' AND 
			reglement_codeFacture = '' LIMIT :offset, :limit", [':offset' => $first, ':limit' => $parpage]);
		} else {
			$entrees = SQLSelect($sqlrech, $sqlrechParams);
		}
	?>
	
	<div class="row col-lg-12">
		<div class="box box-primary">
			<div class="box-header">
				<i class="fa fa-filter"></i>
				<h3 class="box-title">Recettes</h3>
			</div>
			<div class="row">
					<div class="col-lg-4"></div>
					<div class="col-lg-4"></div>
					<div class="col-lg-4">
						<form role="form" class="form-inline" name="rechentree" action="" method="post">
							<input type="text" name="research" placeholder="Objet" class="form-control">
							<button class="btn btn-info btn-flat" name="btnresearch" type="submit">Lister</button>
						</form>
					</div>
			</div>
			<div class="box-body">
				<table class="table table-bordered" name="tabentree" id="tabentree">
					<thead>
						<tr>
							<th style="width:50px">#</th>
							<th style="width:100px">DATE</th>
							<th style="width:100px">MONTANT</th>
							<th>OBJET</th>
							<th style="width:100px"></th>
						</tr>
					</thead>
					<tbody>
						<?php
							if(empty($entrees))
							{
						?>
								<tr>
									<td colspan="4">Aucune entrée dans la caisse.</td>
								</tr>
						<?php
							}
							else
							{
								foreach($entrees as $ent):
						?>
									<tr>
										<td><?= $numligne++;?></td>
										<td><?= date_format(date_create($ent->dateReglement),'d/m/Y'); ?></td>
										<td><?= number_format($ent->montantReglement, 0, ',', ' '); ?></td>
										<td><?= $ent->objetReglement; ?></td>
										<td>
											<a href="caisseentree.php?action=edit&id=<?= $ent->idReglement; ?>">
												<button class='btn bg-orange'>EDITER</button>
											</a>
										</td>
									</tr>
						<?php
								endforeach;
							}
						?>
					</tbody>
					<tfoot>
						<tr>
							<th style="width:50px">#</th>
							<th style="width:100px">DATE</th>
							<th style="width:100px">MONTANT</th>
							<th>OBJET</th>
							<th style="width:100px"></th>
						</tr>
					</tfoot>
				</table>
				<br>
				<?php
					if($tableau=="entier")
					{
				?>
					<ul class="pagination pagination-sm no-margin pull-right">
						<?php
							for($i=1; $i<=$nbpages; $i++)
							{
						?>
								<li><a href="caissentree.php?page=<?= $i;?>"><?= $i;?></a></li>
				<?php
						}
					}
				?>
				</ul>
			</div>
		</div>
	</div>
	
<?php
	$content = ob_get_clean();
	require_once('patterning.php');
?>