<?php 
session_start();

$ip = $_SERVER['REMOTE_ADDR'];
$host = $_SERVER['HTTP_HOST'];
$appID = '';
$status = '';



//echo $filtro;
	$status = 'OK';
	$mensagemstatus = 'Registros ';
	include("view_pagina_cabecalho.php");
	//include 'view_geral_mensagens.php';	
	include("view_usuario_login.php");
	//include("view_pagina_conteudo.php");
	include("view_pagina_rodape.php");



?>		
