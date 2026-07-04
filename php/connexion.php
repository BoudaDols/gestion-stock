<?php
	
class DB
{
	private $host = 'localhost';
	private $user = 'root';
	private $pass = '';
	private $db_name = 'bd_gestockevent24';
	public $db;
	
	public function __construct($host=null, $user=null, $pass=null, $db_name=null)
	{
		if($host != null)
		{
			$this->host = $host;
			$this->user = $user;
			$this->pass = $pass;
			$this->db_name = $db_name;
		}
		try
		{
			$this->db = new PDO ('mysql:host='.$this->host.';dbname='.$this->db_name, $this->user, $this->pass, array(
				PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8',
				PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
				//retirer "PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING" avant de déployer
		}
		catch(PDOException $e)
		{
			die("<font color=\"red\">Impossible de se connecter à la base de données!</font>");
		}
	}
} 