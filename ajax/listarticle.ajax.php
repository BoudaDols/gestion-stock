<?php
	require_once('../php/fonction.php');
	
	$keyword = '%'.$_POST['keyword'].'%';
	// $search_param = "%{$keyword}%";
	
	// $req = "SELECT * FROM artcile WHERE codeArticle LIKE ? ORDER BY idArticle ASC LIMIT 0, 10";
	// $req->bind_param("s",$search_param);
	// $req->execute();
	
	// $result = $req->get_result();
	
	// if ($result->num_rows > 0) {
		// while($row = $result->fetch_assoc())
		// {
			// $tabcodes[] = $row["codeArticle"];
		// }
		// echo json_encode($tabcodes);
	// }
	
	$req = "SELECT * FROM artcile WHERE codeArticle LIKE '$keyword' ORDER BY idArticle ASC LIMIT 0, 10";
	$arts = SQLSelect($req);
	foreach($arts as $art):
		// $codeArt = $art->codeArticle;
		// echo json_encode($codeArt);
		$cod_art = str_replace($_POST['keyword'], '<b>'.$_POST['keyword'].'</b>', $art->codeArticle);
		 echo '<li onclick="set_item(\''.str_replace("'", "\'", $art->codeArticle).'\')">' .$cod_art.'</li>';
	endforeach;
	
?>