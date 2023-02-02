<?php 
declare(strict_types=1);

//database server
define('DB_SERVER', "localhost");

//database login name
define('DB_USER', "super");
//database login password
define('DB_PASS', "naomexa");

//database name
define('DB_DATABASE', "estatistica");

//database debug
define('DB_DEBUG', false);


//session_start();




if(!isset($_SESSION['id'])){
    $usuario = 'lixo';

}else{
    $usuario = $_SESSION['usuario'];
}

    $senha = $_SESSION['senha'];


function is_number($var)
{
	if ($var == (string) (float) $var) {
		return (bool) is_numeric($var);
	}
	if ($var >= 0 && is_string($var) && !is_float($var)) {
		return (bool) ctype_digit($var);
	}
	return (bool) is_numeric($var);
}
//print_r($_SESSION);
function UrlEncodeNew($string) {
    $entities = array('%21', '%2A', '%27', '%28', '%29', '%3B', '%3A', '%40', '%26', '%3D', '%2B', '%24', '%2C', '%2F', '%3F', '%25', '%23', '%5B', '%5D');
    $replacements = array('!', '*', "'", "(", ")", ";", ":", "@", "&", "=", "+", "$", ",", "/", "?", "%", "#", "[", "]");
    return str_replace($entities, $replacements, urlencode($string));
}

    function real_escape_string(string $unescaped_string): string
    {
        $replacementMap = [
            "\0" => "\\0",
            "\n" => "\\n",
            "\r" => "\\r",
            "\t" => "\\t",
            chr(26) => "\\Z",
            chr(8) => "\\b",
            '"' => '\"',
            "'" => "\'",
            '_' => "\_",
            "%" => "\%",
            '\\' => '\\\\'
        ];

        return \strtr($unescaped_string, $replacementMap);
    }


function array_mapk($callback, $array) {
	$newArray = array();
	foreach ($array as $k => $v) {
		$newArray[$k] = call_user_func($callback, $k, $v);
	}
	return $newArray;
}

if(empty($usuario) && !isset($senha)){
	exit();
	
}

?>