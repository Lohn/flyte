<?php
require_once "../include/bittorrent.inc.php";

class sql{
	const sqls = array(
		"getAllUsers"=>"SELECT users.username, users.added, users.last_login, users.last_access, 
		(SELECT COUNT(*) FROM torrents WHERE torrents.owner = users.id) AS cntt,
		(SELECT COUNT(*) FROM comments WHERE comments.user = users.id) AS cntc
		FROM users",
		"addUser"=>"INSERT INTO users (username, password, secret, status, added,admin) VALUES( '%s', '%s', '%s', 'confirmed'" . ", NOW(), '%s')",
		"delUser"=>"DELETE FROM users where username='%s'",

		"getTorrentByID"=>"SELECT * FROM torrents WHERE id = '%d'",
		"delTorrentByID"=>"DELETE FROM torrents WHERE id= '%d'",
		"delTorrentByX"=>"DELETE FROM torrents WHERE %s= '%s'",
		"delTorrentsByUserID"=>"DELETE FROM torrents WHERE owner= '%s'",
		"getUserByID"=>"SELECT * FROM users where id='%d'",
		"getUserByName"=>"SELECT * FROM users where username='%s'",

		"delCommentsWhereIS"=>"DELETE FROM comments where %s='%s'",
		"changeValueOfTorrentByID"=>"UPDATE torrents SET %s='%s' WHERE '%s'='%s'", //Update torrents set what is where a=b 
		"getCountOfTB"=>"select COUNT(*) AS count FROM %s",
		"getCountOfTBWhere"=>"select COUNT(*) AS count FROM %s WHERE %s='%s'",
		"isAdmin"=>"select * from users where username='%s' and admin='yes';",
		"getAllTorrents"=>"SELECT * FROM torrents",
		"getNameOfCategoryByID"=>"select * from categories where id='%d'"
	);
	function doSQL($sprintf, ...$arguments){
		$string = vsprintf($sprintf, $arguments );
		//printf("Debug info: %s\n\r<br>", $string);
		return $result = mysqli_query($this->con, $string ); 
	}
	function getSQLCon(){
			return $this->con;
	}
	function __construct(){
		dbconn(0);
		$this->con=$GLOBALS["___mysqli_ston"];
	}
	function getLastSQLError(){
		return mysqli_error($this->con);
	}
	function getCountOfTB($table){
		$r= $this->doSQL( sql::sqls['getCountOfTB'], $table );
		return mysqli_fetch_assoc($r)['count'];
	}
	function getCountOfTBWhere($table,$a,$b){
		$r= $this->doSQL( sql::sqls['getCountOfTBWhere'], $table,$a,$b );
		return mysqli_fetch_assoc($r)['count'];
	}
}

class comments extends sql{
	const commentfields=array(
		"id", "user", "torrent", "added", "text", "ori_text"
	);
//
	function countComments(){
		return $this->getCountOfTB("comments");
	}

	function delCommentIsWhere($is, $where="id"){
		$allowed=false;
		foreach(self::commentfields as $allow_fields)
		{
			if($where == $allow_fields){
				$allowed=true;
				break;
			}
		}
		if(!$allowed) return false;
		return $this->doSQL( sql::sqls['delCommentsWhereIS'], $where, $is);
	} 
	function delCommentByUserID($id){
		return $this->delCommentIsWhere($id, "user");
	}
}

class torrents extends comments{
	const torrentfields=array(
		//is much...
	);
//		"changeValueOfTorrentByID"=>"UPDATE torrents SET %s='%s' WHERE '%s'='%s'" //Update torrents set what is where a=b 
	function changeValueOfTorrentByID($what,$value,$where_a,$where_b){
		return $this->doSQL( sql::sqls['changeValueOfTorrentByID'], $what, $value, $where_a, $where_b );
	}
	function setBanTorrentByID($is_banned, $id){
		if( $is_banned === True ) $is_banned="yes";
		elseif( $is_banned === False ) $is_banned="no";

		$this->changeValueOfTorrentByID("banned",  "$is_anned", "id", "$id");
	}
	function getTorrentByID($id){
		//$res = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM torrents WHERE id = $id");
		$ret = $this->doSQL( sql::sqls['getTorrentByID'], $id );
		return mysqli_fetch_array($ret);
	}
	//"delTorrentByX"=>"DELETE FROM torrents WHERE %s= '%s'"
	function delTorrentByX($x,$value){
		$ret1 = $this->doSQL( sql::sqls['delTorrentByX'], $x, $v );
	}

	function delTorrentsByUserID($id, $withComments=True){
		$ret0= True;
		$ret1 = $this->doSQL( sql::sqls['delTorrentsByUserID'], $id );
		if($withComments) $ret0 = $this->delCommentIsWhere("user", $id);
		if ( !($ret1 && $ret0) ) return $this->getLastSQLError() ;
		return True;
	}

	function delTorrentByID($id,$withComments=True){ //maybeto delTorrentByX? or no...
		$ret0 = True;

		$ret1 = $this->doSQL( sql::sqls['delTorrentByID'], $id );
		if($withComments) $ret0 = $this->delCommentIsWhere("torrent", $id);

		if ( !($ret1 && $ret0) ) return $this->getLastSQLError() ;
		return True;
	}
	function countTorrents(){
		return $this->getCountOfTB("torrents");
	}
	function getAllTorrents(){
		return $this->doSQL( sql::sqls['getAllTorrents'] );
	}
}

class categories extends torrents{
	function countCategories(){
		return $this->getCountOfTB("categories");
	}
	function getNameOfCategoryByID($id){
		//getNameOfCategoryByID
		$category=$this->doSQL( sql::sqls['getNameOfCategoryByID'], $id );
		//$category=mysqli_fetch_array($category)['name'];
		return mysqli_fetch_array($category)['name'];
	}
}

class peers extends categories{
	function countPeers(){
		return $this->getCountOfTB("peers");
	}	
	function countOfSeeders(){
		return $this->getCountOfTBWhere("peers","seeder","yes");
	}	
	function countOfLeech(){
		return $this->getCountOfTBWhere("peers","seeder","no");
	}	
}

class users extends peers{
	function addUser($username, $password, $admin='no')
	    {
		if (strlen($password) > 64)
		    return("Sorry, password is too long (max is 63 chars)");
		if (!preg_match('/^[a-z][\w.-]*$/is', $username) || strlen($username) > 40)
		    return("Invalid username. Must not be more than 40 characters long and no weird characters");
		//if (!isset($this->link)) $this->ConnToDBByConfig();
		//print("Connected");
		$secret = mksecret();
		$hashpass = hash("sha256", $secret . $password . $secret); //JES NEED TO CHANGE sha3 to sha3-224 maybe 224.....
		
		$ret = $this->doSQL( sql::sqls['addUser'], $username, $hashpass, $secret, $admin );
		if ($ret !== True) return ( $this->getLastSQLError(). "(maybe user exist already?)" );
		//print("return true");
		return True;
	}
	function getUserByName($username){
		//print($username);
		$res=$this->doSQL( sql::sqls['getUserByName'], $username);
		$user=mysqli_fetch_array($res);
		return $user;
	}

	function getUserByID($id){
		$res=$this->doSQL( sql::sqls['getUserByID'], $id);
		$user=mysqli_fetch_array($res);
		return $user;
	}

	function delUserByUsername($username, $withTorrents=True, $withComments=True)
	{
		$ret0=True;
		//if withTorrents... DELETE FROM TORRENTS where ... username=... 
		//also with comments
		$user=$this->getUserByName($username);
		//var_dump($user);
		//exit(0);
		if($withTorrents){
			//print("DEL TORRENTS>". $withComments);
			//exit(0);	
			$ret0=$this->delTorrentsByUserID($user['id'], $withComments);
			//print("deleted?");
			//exit(0);
		}
		elseif( $withComments && !$withTorrents){
			$ret0 = $this->delCommentByUserID($user['id']);
		}
		$ret1=True;
		$ret1= $this->doSQL( sql::sqls['delUser'], $username);
		if ( !($ret1 && $ret0) ) return $this->getLastSQLError() ;
		return True;
	}
	function getAllUsers(){
		return $this->doSQL( sql::sqls['getAllUsers'] );
	}
	function countUsers(){
		return $this->getCountOfTB("users");
	}
	function isAdmin($nick){
		$r = $this->doSQL( sql::sqls['isAdmin'], $nick );
		$r=mysqli_fetch_assoc($r);
		if ( strlen($r['username'])  ) return True;
		return False;
	}
}

class admin extends users{
	const DEBUG = FALSE;
	function getServInfo(
		$indicesServer = array(
    		'SERVER_NAME',
    		'SERVER_ADDR',
    		'SERVER_PORT',
    		'SERVER_SIGNATURE',
    		'SERVER_SOFTWARE',
    		'SERVER_PROTOCOL',
		)
	){
		$returns = array();
		foreach ($indicesServer as $info) {
			//print("INFO:".$_SERVER[$info]);
			if (isset($_SERVER[$info])) $returns[$info] = $_SERVER[$info];
		}
		return $returns;
	}

	function __construct($moveIfNotAdmin=True, $page='../index.php'){
		sql::__construct();
		if($moveIfNotAdmin) {
			$is_admin = $this->checkAdmin();
			if(!$is_admin){
				
				if(!self::DEBUG){
					header("Location: ../index.php");
					exit(0);
				}else{
					//..
				}
			}
		}
		stdhead("Admin page");
		//include_once "../include/page_header.inc.php";
	}

	function checkAdmin(){
		global $CURUSER;
		$this->isAdmin = (isset($CURUSER) && $CURUSER["admin"] == "yes");
		return $this->isAdmin;
	}
};
