<?php
	require_once(__DIR__ . '/../php/fonction.php');
	
	$keyword = isset($_POST['keyword']) ? $_POST['keyword'] : '';
	
	$arts = SQLSelect("SELECT * FROM article WHERE codeArticle LIKE :keyword ORDER BY idArticle ASC LIMIT 0, 10", 
		[':keyword' => "%{$keyword}%"]);
	if(!empty($arts)) {
		foreach($arts as $art):
			$cod_art = str_replace($keyword, '<b>'.$keyword.'</b>', $art->codeArticle);
			echo '<li onclick="set_item(\''.str_replace("'", "\'", $art->codeArticle).'\')">' .$cod_art.'</li>';
		endforeach;
	}
?>
