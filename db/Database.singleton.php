<?php
# Name: Database.singleton.php
# File Description: MySQL Singleton Class to allow easy and clean access to common mysql commands
# Author: ricocheting
# Web: http://www.ricocheting.com/
# Update: 2010-07-19
# Version: 3.1.4
# Copyright 2003 ricocheting.com


/*
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/


//require("config.inc.php");
//$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);

//$db = Database::obtain();


###################################################################################################
###################################################################################################
###################################################################################################
class Database{

	// debug flag for showing error messages
	public	$debug = false;

	// Store the single instance of Database
	private static $instance;

	private	$server   = ""; //database server
	private	$user     = ""; //database login name
	private	$pass     = ""; //database login password
	private	$database = ""; //database name

	private	$error = "";

	#######################
	//number of rows affected by SQL query
	public	$affected_rows = 0;

	private static $db;
	private static $stm;
	private static $result;
	
	private readonly mixed	$link_id;
	private readonly mixed	$query_id;


#-#############################################
# desc: constructor
public function __construct($server=null, $user=null, $pass=null, $database=null, $debug=false){
	// error catching if not passed in
	if($server==null || $user==null || $database==null){
		$this->oops("Database information must be passed in when the object is first created.");
	}

	$this->server=$server;
	$this->user=$user;
	$this->pass=$pass;
	$this->database=$database;
	$this->debug=$debug;
}#-#constructor()


#-#############################################
# desc: singleton declaration
//public static function obtain($server=null, $user=null, $pass=null, $database=null){
public function obtain($server=null, $user=null, $pass=null, $database=null, $debug=null){
	
	if (!self::$instance){ 
		self::$instance = new  Database($server, $user, $pass, $database, $debug);
	} 
	return self::$instance; 
}#-#obtain()

#-#############################################
# desc: connect and select database using vars above
# Param: $new_link can force connect() to open a new link, even if mysql_connect() was called before with the same parameters
public function connect($new_link=false){
	self::$db = new PDO( "mysql: host=".$this->server.";port=3306;dbname=".$this->database, "$this->user", "$this->pass") or die("Could not connect to server: <b>$this->server</b>.");

	//$this->link_id = @mysqli_connect($this->server,$this->user,$this->pass, $this->database);
	//echo '<br>Conectou em '.$this->database;
	//if (mysqli_connect_errno()){ $this->oops("Could not connect to server: <b>$this->server</b>.");	}
		
		//@mysql_set_charset("latin1", $this->link_id);


	// unset the data so it can't be dumped
	$this->server='';
	$this->user='';
	$this->pass='';
	$this->database='';
}#-#connect()



#-#############################################
# desc: close the connection
public function close(){
	//mysqli_close($this->link_id);
}#-#close()

public function  __destruct() {
	$this->close();
}

#-#############################################
# Desc: escapes characters to be mysql ready
# Param: string
# returns: string
public function escape($string){
	return self::$db->quote($string);
}#-#escape()


#-#############################################
# Desc: executes SQL query to an open connection
# Param: (MySQL query) to execute
# returns: (query_id) for fetching results etc
public function query($sql){
	// do query
	//$this->query_id = @mysqli_query($sql, $this->link_id);

try {
	$db = self::$db;
	$stm = $db->prepare($sql);
	$stm->execute();
	if($this->debug){
		echo "SQL:$sql<br>";
	}

  }
  catch (PDOException $e) {
    print $e->getMessage();
  }	

	
	return self::$result;
}#-#query()


#-#############################################
# desc: does a query, fetches the first row only, frees resultset
# param: (MySQL query) the query to run on server
# returns: array of fetched results
public function query_first($query_string){
	/*
	$query_id = $this->query($query_string);
	$out = $this->fetch($query_id);
	$this->free_result($query_id);
	*/
	$out = false;
	return $out;
}#-#query_first()


#-#############################################
# desc: fetches and returns results one line at a time
# param: query_id for mysql run. if none specified, last used
# return: (array) fetched record(s)
public function fetch($query){
	// retrieve row

try {
	$db = self::$db;
	$stm = $db->prepare($query);
	$stm->execute();
	self::$result = $stm->fetch(PDO::FETCH_ASSOC);
	if($this->debug){
		echo "SQL:$query<br>";
	}
  }
  catch (PDOException $e) {
    print $e->getMessage();
  }	

	return self::$result;
}#-#fetch()


#-#############################################
# desc: returns all the results (not one row)
# param: (MySQL query) the query to run on server
# returns: assoc array of ALL fetched results
public function fetch_array($sql){
try {
	$db = self::$db;
	$stm = $db->prepare($sql);
	$stm->execute();
	self::$result = $stm->fetchAll(PDO::FETCH_ASSOC);
	if($this->debug){
		echo "SQL:$sql<br>";
		//print_r(self::$result);
	}
	
  }
  catch (PDOException $e) {
    print $e->getMessage();
  }	

	return self::$result;
}#-#fetch_array()


public function fetch_array_condition($sql, $condition){
try {
	$db = self::$db;
	$stm = $db->prepare($sql);
	
	//$stm->bindParam(':filtro', $condition[':filtro'], PDO::PARAM_STR);
	//$stm->bindParam(':order', $condition[':order'], PDO::PARAM_STR);	
	$stm->bindParam(':comeco', $condition[':comeco'], PDO::PARAM_INT);	
	$stm->bindParam(':maximoregistros', $condition[':maximoregistros'], PDO::PARAM_INT);	
	$stm->execute();
	self::$result = $stm->fetch(PDO::FETCH_ASSOC);
	if($this->debug){
		echo "SQL:$sql<br>Parametros:".print_r($condition,true);
	}
	
  }
  catch (PDOException $e) {
    print $e->getMessage();
  }	

	return self::$result;
}

#-#############################################
# desc: does an update query with an array
# param: table, assoc array with data (not escaped), where condition (optional. if none given, all records updated)
# returns: (query_id) for fetching results etc
public function update($table, $data, $where='1'){
	$q="UPDATE `$table` SET ";

	foreach($data as $key=>$val){
		if(strtolower($val)=='null') $q.= "`$key` = NULL, ";
		elseif(strtolower($val)=='now()') $q.= "`$key` = NOW(), ";
        elseif(preg_match("/^increment\((\-?\d+)\)$/i",$val,$m)) $q.= "$`key` = $key + $m[1], "; 
		else $q.= "`$key`=".$this->escape($val).", ";
	}

	$q = rtrim($q, ', ') . ' WHERE '.$where.';';
	if($this->debug){
		echo "SQL:$q<br>";
	}
try {	
	$db = self::$db;
	$stm = $db->prepare($q);
	$stm->execute();
	$count = $stm->rowCount();
  }
  catch (PDOException $e) {
	$count = 0;
  }	

//echo $q;
	return $count;
}#-#update()


#-#############################################
# desc: does an insert query with an array
# param: table, assoc array with data (not escaped)
# returns: id of inserted record, false if error
public function insert($table, $data){
	$q="INSERT INTO `$table` ";
	$v=''; $n='';

	foreach($data as $key=>$val){
		$n.="`$key`, ";
		if(strtolower($val)=='null') $v.="NULL, ";
		elseif(strtolower($val)=='now()') $v.="NOW(), ";
		elseif(strtolower($val)=='uuid()') $v.="UUID(), ";
		else $v.= "'".$this->escape($val)."', ";
	}

	$q .= "(". rtrim($n, ', ') .") VALUES (". rtrim($v, ', ') .");";

	//echo $q;
	if($this->debug){
		echo "SQL:$sql<br>";
	}
	
	if($this->query($q)){
		//return mysql_insert_id($this->link_id);
		return true;
	}else return false;

}#-#insert()


#-#############################################
# desc: frees the resultset
# param: query_id for mysql run. if none specified, last used
private function free_result($query_id=-1){
	/*
	if ($query_id!=-1){
		$this->query_id=$query_id;
	}
	if($this->query_id!=0 && !@mysqli_free_result($this->query_id)){
		$this->oops("Result ID: <b>$this->query_id</b> could not be freed.");
	}*/
}#-#free_result()


#-#############################################
# desc: throw an error message
# param: [optional] any custom error to display
private function oops($msg=''){

		$msg="<b>WARNING:</b> No link_id found. Likely not be connected to database.<br />$msg";

	// if no debug, done here
	if(!$this->debug) return;
	?>
		<table align="center" border="1" cellspacing="0" style="background:white;color:black;width:80%;">
		<tr><th colspan=2>Database Error</th></tr>
		<tr><td align="right" valign="top">Message:</td><td><?php echo $msg; ?></td></tr>
		<?php if(!empty($this->error)) echo '<tr><td align="right" valign="top" nowrap>MySQL Error:</td><td>'.$this->error.'</td></tr>'; ?>
		<tr><td align="right">Date:</td><td><?php echo date("l, F j, Y \a\\t g:i:s A"); ?></td></tr>
		<?php if(!empty($_SERVER['REQUEST_URI'])) echo '<tr><td align="right">Script:</td><td><a href="'.$_SERVER['REQUEST_URI'].'">'.$_SERVER['REQUEST_URI'].'</a></td></tr>'; ?>
		<?php if(!empty($_SERVER['HTTP_REFERER'])) echo '<tr><td align="right">Referer:</td><td><a href="'.$_SERVER['HTTP_REFERER'].'">'.$_SERVER['HTTP_REFERER'].'</a></td></tr>'; ?>
		</table>
	<?php
}#-#oops()


}//CLASS Database
###################################################################################################

?>