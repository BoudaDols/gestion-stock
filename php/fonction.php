<?php
	require 'connexion.php';
	function SQLSelect($sql)
	{		
		$bdd = new DB();
		
		$req = $bdd->db->prepare($sql);
		$req->execute();
		if($req)
			return $req->fetchAll(PDO::FETCH_OBJ);
		else
			return false;
	}
	
	//Qté d'un article
	function getQte($art)
	{
		$sql = "SELECT * FROM  article WHERE codeArticle='$art'";
		$articles = SQLSelect($sql);
		if(!empty($articles))
		{
			foreach ($articles as $article) 
			{
				$qte=$article->qteStockArticle;
			}
			return $qte;
		}
	}
	
	//Code d'un article
	function getCodeArt($desig)
	{
		$sql = "SELECT * FROM  article WHERE designationArticle='$desig'";
		$articles = SQLSelect($sql);
		if(!empty($articles))
		{
			foreach ($articles as $article) 
			{
				$code=$article->codeArticle;
			}
			return $code;
		}
	}
	//Désignation d'un article
	function getLibArt($code)
	{
		$sql = "SELECT * FROM  article WHERE codeArticle='$code'";
		$articles = SQLSelect($sql);
		if(!empty($articles))
		{
			foreach ($articles as $article) 
			{
				$desig=$article->designationArticle;
			}
			return stripslashes($desig);
		}
	}
	
	//Nom d'un client
	function getClient($code)
	{
		$sql = "SELECT * FROM  client WHERE codeClient='$code'";
		$clients = SQLSelect($sql);
		if(!empty($clients))
		{
			foreach ($clients as $clt) 
			{
				$nom=$clt->nomClient;
			}
			return stripslashes($nom);
		}
	}
	
	//Reférence d'une facture
	function refFact()
	{
		$req = "SELECT * FROM facture ORDER BY idFacture DESC LIMIT 0,1";
		$fact = SQLSelect($req);
		$year = date('Y');
		
		if(empty($fact))
		{
			$newref = "FAC-".$year."-"."0001";
		}
		else
		{
			foreach($fact as $fac):
				$lastref = $fac->codeFacture;
			endforeach;
			$yearfact = substr($lastref,strpos($lastref,'-')+1,4);
			$lastnum = substr($lastref,strripos($lastref,'-')+1)+1;
			if(strlen($lastnum)<4)
			{
				if (4-strlen($lastnum)==1)
					$newnum = "0".$lastnum;
				if(4-strlen($lastnum)==2)
					$newnum = "00".$lastnum;
				if(4-strlen($lastnum)==3)
					$newnum = "000".$lastnum;
			}
			else
			{
				$newnum = $lastnum;
			}
			if($year>$yearfact)
			{
				$newref = "FAC-".$year."-"."0001";
			}
			else
			{
				$newref = "FAC-".$yearfact."-".$newnum;
			}
		}
		return $newref;
	}

	//Remise d'une facture
	function getRemise($code)
	{
		$sql = "SELECT DISTINCT remiseFacture FROM  facture WHERE codeFacture='$code'";
		$facts = SQLSelect($sql);
		if(!empty($facts))
		{
			foreach ($facts as $fact) 
			{
				$remise=$fact->remiseFacture;
			}
			if(empty($remise))
			{
				return 0;
			}
			else
			{
				return $remise;
			}
		}
	}

	//Date de vente d'une facture
	function getDatef($code)
	{
		$sql = "SELECT DISTINCT dateFacture FROM  facture WHERE codeFacture='$code'";
		$facts = SQLSelect($sql);
		if(!empty($facts))
		{
			foreach ($facts as $fact) 
			{
				$datef=$fact->dateFacture;
			}
			return $datef;
		}
	}

	//N° Commande d'une facture
	function getCmdf($code)
	{
		$sql = "SELECT DISTINCT cmdFacture FROM  facture WHERE codeFacture='$code'";
		$facts = SQLSelect($sql);
		if(!empty($facts))
		{
			foreach ($facts as $fact) 
			{
				$cmdf=$fact->cmdFacture;
			}
			return stripslashes($cmdf);
		}
	}

	//Total payé d'une facture crédit
	function getSumPaidC($code)
	{
		$cd="";
		$prep = "SELECT * FROM facture WHERE facture_codeTypeF='CREDIT' AND codeFacture='$code'";
		$prepas = SQLSelect($prep);
		foreach ($prepas as $prepa) 
		{
			$cd = $prepa->codeFacture;
		}
		$sql = "SELECT SUM(montantReglement) paid FROM reglement
		WHERE reglement_codeFacture='$cd'";
		$facts = SQLSelect($sql);
		if(!empty($facts))
		{
			foreach ($facts as $fact) 
			{
				$cdtpaye=$fact->paid;
			}
			if(empty($cdtpaye))
			{
				return 0;
			}
			else
			{
				return $cdtpaye;
			}
		}
	}

	//Brut à payer d'une facture
	function getBrut($code)
	{
		$sql = "SELECT SUM(totalFacture) bap FROM facture WHERE codeFacture='$code'";
		$facts = SQLSelect($sql);
		if(!empty($facts))
		{
			foreach ($facts as $fact) 
			{
				$brut=$fact->bap;
			}
			if(empty($brut))
			{
				return 0;
			}
			else
			{
				return $brut;
			}
		}
	}
	
	//Net à payer d'une facture
	function getNet($code)
	{
		$brut = getBrut($code);
		$remise = getRemise($code);
		
		$net = $brut - $remise;
		return $net;
	}
	
	//TVA d'une facture
	function getTVA($code)
	{
		$sql = "SELECT DISTINCT tvaFacture FROM facture WHERE codeFacture='$code'";
		$facts = SQLSelect($sql);
		if(!empty($facts))
		{
			foreach($facts as $fact):
				$vtva = $fact->tvaFacture;
			endforeach;
			if($vtva==1)
			{
				$net = getNet($code);
				$ctva = getcoefTVA();
				$mtva = round($net * $ctva);
			}
			else
			{
				$mtva = 0;
			}
		}
		return $mtva;
	}
	
	//TTC d'une facture
	function getTTC($code)
	{
		$net = getNet($code);
		$mtva = getTVA($code);
		
		$ttc = $net + $mtva;
		return $ttc;
	}
	
	//Prochainde date de paiement d'une facture
	function getNextDate($code)
	{
		$sql = "SELECT DISTINCT facture_codeModalite FROM facture WHERE codeFacture='$code'";
		$infos = SQLSelect($sql);
		if(!empty($infos))
		{
			foreach ($infos as $info) 
			{
				$mod=$info->facture_codeModalite;
			}
		}
		$sql1 = "SELECT * FROM reglement WHERE reglement_codeFacture='$code'";
		$nbr = Count(SQLSelect($sql1));
		$datef = getDatef($code);
		switch($mod)
		{
			case "1M":
				$coef = $nbr+1;
				$add = $coef." months";
				$conv = date_format(date_create($datef), 'Y-m-d');
				$conv1 = date_create($conv);
				$last = date_add($conv1, date_interval_create_from_date_string($add));
				$lastdate = date_format($last, 'Y-m-d');
			break;
			case "3M":
				$coef = $nbr+1;
				$add = (3*$coef)."3 months";
				$conv = date_format(date_create($datef), 'Y-m-d');
				$conv1 = date_create($conv);
				$last = date_add($conv1, date_interval_create_from_date_string($add));
				$lastdate = date_format($last, 'Y-m-d');
			break;
			case "6M":
				$coef = $nbr+1;
				$add = (6*$coef)." months";
				$conv = date_format(date_create($datef), 'Y-m-d');
				$conv1 = date_create($conv);
				$last = date_add($conv1, date_interval_create_from_date_string($add));
				$lastdate = date_format($last, 'Y-m-d');
			break;
			case "12M":
				$coef = $nbr+1;
				$add = $coef." years";
				$conv = date_format(date_create($datef), 'Y-m-d');
				$conv1 = date_create($conv);
				$last = date_add($conv1, date_interval_create_from_date_string($add));
				$lastdate = date_format($last, 'Y-m-d');
			break;
			case "CC":
				//2 semainess comme échéance
				$coef = $nbr+1;
				$add = ($coef*14)." days";
				$conv = date_format(date_create($datef), 'Y-m-d');
				$conv1 = date_create($conv);
				$last = date_add($conv1, date_interval_create_from_date_string($add));
				$lastdate = date_format($last, 'Y-m-d');
			break;
		}
		return $lastdate;
	}
	
	//infos de la société
	function getNom()
	{
		$sql = "SELECT * FROM entete WHERE id=1";
		$infos = SQLSelect($sql);
		if(!empty($infos))
		{
			foreach ($infos as $info) 
			{
				$nom=$info->nom;
			}
			return $nom;
		}
	}
	function getAdr()
	{
		$sql = "SELECT * FROM entete WHERE id=1";
		$infos = SQLSelect($sql);
		if(!empty($infos))
		{
			foreach ($infos as $info) 
			{
				$adr=$info->adresse;
			}
			return $adr;
		}
	}
	function getLogo()
	{
		$sql = "SELECT * FROM entete WHERE id=1";
		$infos = SQLSelect($sql);
		if(!empty($infos))
		{
			foreach ($infos as $info) 
			{
				$logo=$info->logo;
			}
			return $logo;
		}
	}
	function getTel1()
	{
		$sql = "SELECT * FROM entete WHERE id=1";
		$infos = SQLSelect($sql);
		if(!empty($infos))
		{
			foreach ($infos as $info) 
			{
				$tel1=$info->tel1;
			}
			return $tel1;
		}
	}
	function getTel2()
	{
		$sql = "SELECT * FROM entete WHERE id=1";
		$infos = SQLSelect($sql);
		if(!empty($infos))
		{
			foreach ($infos as $info) 
			{
				$tel2=$info->tel2;
			}
			return $tel2;
		}
	}
	function getBank()
	{
		$sql = "SELECT * FROM entete WHERE id=1";
		$infos = SQLSelect($sql);
		if(!empty($infos))
		{
			foreach ($infos as $info) 
			{
				$bank=$info->banque;
			}
			return $bank;
		}
	}
	function getRCCM()
	{
		$sql = "SELECT * FROM entete WHERE id=1";
		$infos = SQLSelect($sql);
		if(!empty($infos))
		{
			foreach ($infos as $info) 
			{
				$rccm=$info->rccm;
			}
			return $rccm;
		}
	}
	function getIFU()
	{
		$sql = "SELECT * FROM entete WHERE id=1";
		$infos = SQLSelect($sql);
		if(!empty($infos))
		{
			foreach ($infos as $info) 
			{
				$ifu=$info->ifu;
			}
			return $ifu;
		}
	}
	function getcoefTVA()
	{
		$sql = "SELECT * FROM entete WHERE id=1";
		$infos = SQLSelect($sql);
		if(!empty($infos))
		{
			foreach ($infos as $info) 
			{
				$ctva=$info->ctva;
			}
			return $ctva;
		}
	}
	//dernier règlement d'une facture crédit
	function getLastVers($code)
	{
		$sql = "SELECT * FROM reglement WHERE reglement_codeFacture='$code'";
		$infos = SQLSelect($sql);
		if(!empty($infos))
		{
			foreach ($infos as $info) 
			{
				$lastV=$info->montantReglement;
			}
			return $lastV;
		}
	}
	