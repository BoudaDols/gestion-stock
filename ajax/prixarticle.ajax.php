<?php
	require_once(__DIR__ . '/../php/fonction.php');
	
	if(isset($_POST['idArt']))
	{
		$postart = $_POST['idArt'];
		$arts = SQLSelect("SELECT * FROM article WHERE codeArticle = :code AND statutArticle='ON'", [':code' => $postart]);
		if(!empty($arts)) {
			foreach($arts as $art):?>

			<!--quantité restante-->
						<div class="row col-lg-4">
							<div class="form-group">
								<div class="input-group">
									<input type="text" class="form-control" style="width:250px" name="designation" id="designation"  value="<?= htmlspecialchars($art->designationArticle);?>"   disabled="disabled" />
								</div>
							</div>
						</div>
						<!--quantité restante-->
			
			<!--quantité restante-->
						<div class="row col-lg-3">
							<div class="form-group">
								<div class="input-group">
									<input type="text" class="form-control" style="width:175px" name="qteRestante" id="qteRestante"  value=" Reste : <?= $art->qteStockArticle;?>"   disabled="disabled" />
								</div>
							</div>
						</div>
						<!--quantité restante-->
						<!--Prix unitaire-->
						<div class="row col-lg-3">
							<div class="form-group">
								<div class="input-group">
									<div class="input-group-addon">
										<i class="fa fa-money"></i>
									</div>
									<div>
										<input type="text" class="form-control" style="width:150px" name="prix" id="prix" value="<?= $art->prixMinArticle;?>" disabled="disabled"/>
									</div>
								</div>
							</div>
						</div>
						<!--Prix unitaire-->
		<?php endforeach;
		}
	}
?>
