<?php
error_reporting(E_ALL);
session_start();

if(empty($_SESSION['usuario'])){
	$_SESSION['usuario']='';
	$_SESSION['senha']='';
	$_SESSION['id'] = $_COOKIE;
}
include 'parametros.php';

include 'db/Database.singleton.php';

$ip = $_SERVER['REMOTE_ADDR'];
$host = $_SERVER['HTTP_HOST'];
$maximoregistros = 30;
$nometitulo = 'Usuario';
$controllernome = 'usuario';
$controllernomeplural = 'usuario';
$appID = '';
$status = '';

$displaydel[0] = 'email';
$campochave[0] = 'usuario_id';
$campouuid = 'usuario_id';


//var_dump($_POST);


$filtros = array('dt_maiorque'=>'Maior que','dt_menorque'=>'Menor que','dt_igual'=>'Igual a','dt_diferente'=>'Diferente de','txt_contem'=>'Contém','txt_naocontem'=>'Não contém','txt_igual'=>'Igual a','txt_diferente'=>'Diferente de','num_maiorque'=>'Maior que','num_menorque'=>'Menor que','num_igual'=>'Igual a','num_diferente'=>'Diferente de');
$filtrostipo = array('dt_maiorque'=>'Data','dt_menorque'=>'Data','dt_igual'=>'Data','dt_diferente'=>'Data','txt_contem'=>'Texto','txt_naocontem'=>'Texto','txt_igual'=>'Texto','txt_diferente'=>'Texto','num_maiorque'=>'Numero','num_menorque'=>'Numero','num_igual'=>'Numero','num_diferente'=>'Numero');
$campos = array('Email'=>'email','Perfil'=>'perfil','Dt Cadastro'=>'created');
$tipos = array('email'=>'txt','perfil'=>'txt','created'=>'dttime');
$funcoes = array('email'=>'vazio','perfil'=>'vazio','created'=>'vazio');
$leitura = array('email'=>0,'perfil'=>0,'created'=>0);

$tamanhocampo = array('Email'=>100,'Perfil'=>20,'Dt Cadastro'=>20);
$campoautocompletar = array('Email'=>0,'Perfil'=>0,'Dt Cadastro'=>0);
$campoautosomar = array('Email'=>0,'Perfil'=>0,'Dt Cadastro'=>0);
$campoautosomarvalor = array('Email'=>0,'Perfil'=>0,'Dt Cadastro'=>0);
$mensagemstatus = '';
if(empty($_GET['pagina'])){
	$_GET['pagina']=1;
}
$pagina = $_GET['pagina'];
$datas = '';

if(empty($comeco)){
	$comeco = 0;
}

if(!empty($_GET['pagina'])){
	$pagina = $_GET['pagina'];
}else{
	$pagina = 1;
}

$filtro = '';
$formbusca = '';
$order = ' order by `created` DESC, `email` asc ';


$db = (new Database())->obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE, DB_DEBUG);
$db->connect();


$db->query("SET NAMES 'utf8'");
$db->query(("SET character_set_connection=utf8"));
$db->query(("SET character_set_client=utf8"));
$db->query(("SET character_set_results=utf8"));
$db->query(("SET NAMES 'utf8' COLLATE 'utf8_general_ci'"));
$db->query(("SET CHARACTER SET utf8"));

if ( isset($_GET['i']) && ($_GET['i']=='usuario') && ($_GET['acao']=='list') ){
	$status = 'OK';
	$mensagemstatus = 'Registros ';
	include("view_pagina_cabecalho.php");
	include("view_pagina_menu.php");
	$comeco = ($pagina-1) * $maximoregistros;
	$somatorio = $db->fetch_array("select count(*) as total from app_usuario $filtro ");
	$conteudoconsultarecente = $db->fetch("select * from app_usuario where date(created)=date(now()) ");
	$conteudoconsultaregistros = $db->fetch_array("select * from app_usuario $filtro $order limit $comeco,$maximoregistros ");
	$conteudoconsulta =$conteudoconsultaregistros; 

	$total = $somatorio[0]['total'];
	if(!empty($status)){
		$status = '';
	}
	$paginas = ceil($total/$maximoregistros);
	if ($total != $paginas*$maximoregistros){
		//$paginas++;
	}
	include 'view_geral_mensagens.php';	
	include("view_usuario_list.php");
	include 'view_geral_ajax.php';
	include("view_pagina_rodape.php");
}


	if(empty($_GET['pesquisa'])){
		$_GET['pesquisa']='';
	}


if ( isset($_POST['i']) &&($_POST['i']=='usuario') && ($_POST['acao']=='login') ){
	
	if(empty($_GET['i'])){
		$_GET['i']=$controllernome;
	}
	if(empty($_GET['acao'])){
		$_GET['acao']='list';
	}

	$sqlusuario = "select * from app_usuario where email='{$_POST['email']}' and senha='{$_POST['senha']}' ";
	
	//echo $sqlusuario;
	
	$usuarios_ = $db->fetch($sqlusuario);
	if( isset($usuarios_['perfil'])){
		$perfil  = $usuarios_['perfil'];
		$usuario = $usuarios_['email'];
		$status = 'OK';
		$mensagemstatus = 'Autenticação válida';

	}else{
		$perfil  = '';
		$status = 'ERRO';
		$mensagemstatus = 'Dados inválidos!';
	}
	

	$_SESSION['usuario']=$_POST['email'];
	$_SESSION['senha']=$_POST['senha'];
//print_r($_SESSION);
	
	if(!empty($perfil)){
		

		if(!empty($_GET['registrosporpagina'])&& is_numeric($_GET['registrosporpagina'])){
			$maximoregistros = $_GET['registrosporpagina'];
			$db->query('update app_filtro set qtdregistros='.$maximoregistros.' where usuario="'.$usuario.'" and modelo="'.$controllernome.'" ');
		}
		
		$sqlfiltro = $db->fetch("select * from app_filtro where usuario='$usuario' and modelo='$controllernome' ");

		if(!empty($sqlfiltro['qtdregistros'])){
			if($sqlfiltro['qtdregistros']>0){
				$maximoregistros = $sqlfiltro['qtdregistros'];
			}
		}else{
			$maximoregistros = 0;
		}
		$ordenacao['']='';
		if(!empty($sqlfiltro['classificacao'])){
			$vetorclassificacao = unserialize($sqlfiltro['classificacao']);
			if(!empty($vetorclassificacao)){
				foreach($vetorclassificacao as $campo=>$ordem){
					$ordenacao[$campo]=$ordem;
				}
			}
		}
		
		if(!empty($_GET['campo'])){
			$ordenacao[$_GET['campo']]=$_GET['ordem'];
			if(empty($_GET['ordem'])){
				unset($ordenacao[$_GET['campo']]);
				unset($vetorclassificacao[$_GET['campo']]);
			}else{
				$vetorclassificacao[$_GET['campo']]=$_GET['ordem'];
			}
			$ordenacaoserial = serialize($ordenacao);
			$db->query('update app_filtro set classificacao=\''.$ordenacaoserial.'\' where usuario="'.$usuario.'" and modelo="'.$controllernome.'" ');
		}
		if( (count($ordenacao)>0)&&($ordenacao['']!='')){
			$order = ' `order by` '.implode(", ", array_mapk(function ($k, $v) { return "`{$k}`  " . real_escape_string($v); }, $ordenacao));
		}
		if(empty($sqlfiltro['usuario'])&&empty($sqlfiltro['modelo'])){
			$insere = $db->query("insert into app_filtro (usuario, modelo, qtdregistros, criado)	values('$usuario','{$controllernome}', $maximoregistros, now() ) ");
			$filtro = '';
			$formbusca = '';
			//echo "insert into app_filtro (usuario, modelo, qtdregistros, criado)	values('$usuario','{$controllernome}', $maximoregistros, now() ) ";
		}
		if(!empty($sqlfiltro['sqlprocessado'])){
			if(strlen($sqlfiltro['sqlprocessado'])>2){
				$filtro = UConverter::transcode(' where  '.$sqlfiltro['sqlprocessado'], 'UTF-8', 'latin1');
				$formbusca = unserialize($sqlfiltro['dadospostados']);
			}
		}
		include("view_pagina_cabecalho.php");		
		include("view_pagina_menu.php");
		
		$comeco = ($pagina-1) * $maximoregistros;
		$somatorio = $db->fetch("select count(*) as total from app_usuario $filtro ");
		$conteudoconsultarecente = $db->fetch("select * from app_usuario where date(created)=date(now()) ");
		$param_sql=array(':comeco'=>$comeco, ':maximoregistros'=>$maximoregistros);
		$conteudoconsultaregistros = $db->fetch_array("select * from app_usuario $filtro $order limit $comeco , $maximoregistros");
		$conteudoconsulta =$conteudoconsultaregistros;
		$total = $somatorio['total'];
		if(empty($maximoregistros)){$maximoregistros=50;}
		
		if(!empty($status)){
			$status = '';
		}
		
		$paginas = ceil($total/$maximoregistros);
		
		if ($total != $paginas*$maximoregistros){
			//$paginas++;
		}
		include 'view_geral_mensagens.php';
		include("view_usuario_list.php");
		include 'view_geral_ajax.php';
		include("view_pagina_rodape.php");
		
		
	}else{
		
		$status = 'ERRO';
		$mensagemstatus = 'DADOS INCORRETOS - USUÁRIO NÃO AUTENTICADO';


		include("view_pagina_cabecalho.php");
		include 'view_geral_mensagens.php';
		include("view_usuario_login.php");
		include("view_pagina_rodape.php");		
	}
}

$usuario=$_SESSION['usuario'];




	if(!empty($_GET['registrosporpagina'])&& is_numeric($_GET['registrosporpagina'])){
		$maximoregistros = $_GET['registrosporpagina'];
		$db->query('update app_filtro set qtdregistros='.$maximoregistros.' where usuario="'.$usuario.'" and modelo="'.$controllernome.'" ');
	}


	$sqlfiltro = $db->fetch("select * from app_filtro where usuario='$usuario' and modelo='$controllernome' ");

	if($sqlfiltro['qtdregistros']>0){
		$maximoregistros = $sqlfiltro['qtdregistros']; 	
	}
	$filtrotxt = $sqlfiltro['classificacao'];
	if(!empty($filtrotxt)){
		$vetorclassificacao = unserialize(UConverter::transcode($filtrotxt, 'UTF-8', 'latin1'));
		//$vetorclassificacao = unserialize(iconv('latin1', 'utf8', $filtrotxt));
		foreach($vetorclassificacao as $campo=>$ordem){
			$ordenacao[$campo]=$ordem;
		}
	}
	
	if(!empty($_GET['campo'])){
		$ordenacao[$_GET['campo']]=$_GET['ordem'];
		if($_GET['ordem']==''){
			unset($ordenacao[$_GET['campo']]);
			unset($vetorclassificacao[$_GET['campo']]);
		}else{
			$vetorclassificacao[$_GET['campo']]=$_GET['ordem'];
		}
		$ordenacaoserial = serialize($ordenacao);
		$db->query('update app_filtro set classificacao=\''.$ordenacaoserial.'\' where usuario="'.$usuario.'" and modelo="'.$controllernome.'" ');
	}
	if(isset($ordenacao) && count($ordenacao)>0){
		$order = ' order by '.implode(", ", array_mapk(function ($k, $v) { return "{$k}  " . real_escape_string($v); }, $ordenacao));
	}

	if(empty($sqlfiltro['usuario'])&&empty($sqlfiltro['modelo'])){
			$insere = $db->query("insert into app_filtro (usuario, modelo, qtdregistros, criado)	values('$usuario','{$controllernome}', $maximoregistros, now() ) ");
			//echo "insert into app_filtro (usuario, modelo, qtdregistros, criado)	values('$usuario','{$controllernome}', $maximoregistros, now() ) ";exit();
			$filtro = '';
			$formbusca = '';
	}
	if(!empty($sqlfiltro['sqlprocessado'])){
		if(strlen($sqlfiltro['sqlprocessado'])>2){
			$filtro = ' where  '.$sqlfiltro['sqlprocessado'];
			$formbusca = unserialize(UConverter::transcode($sqlfiltro['dadospostados'], 'UTF-8', 'latin1'));
			//$filtro = iconv('latin1', 'utf8', ' where  '.$sqlfiltro['sqlprocessado']);
			//$formbusca = unserialize(iconv('latin1', 'utf8', $sqlfiltro['dadospostados']));
		}
	}





if ( isset($_POST['controller']) &&($_POST['controller']=='usuario') && ($_POST['acao']=='ajaxmodifica') ){
	header('Content-type: application/x-json');
	$dados[$_POST['campo']]=$_POST['valor'];
	if(($tipos[$_POST['campo']] == 'dt')){
		if(strlen($_POST['valor'])>6){
		$exp = explode('/', $_POST['valor']);
		if(count($exp)<2){
			$exp = explode('-', $_POST['valor']);
		}
		$dados[$_POST['campo']]=date('Y-m-d',mktime(0,0,0,$exp[1],$exp[0],$exp[2]));
		}else{
			$dados[$_POST['campo']]='';
		}
	}
	if($tipos[$_POST['campo']]== 'txtUpper'){
		$dados[$_POST['campo']]=strtoupper($_POST['valor']);
	}
	
	$dados['updated'] = 'now()';
	$dados['usuariomodificou'] = $usuario;
	$dados['ipmodificou'] = $ip;
	$dados['hostmodificou'] = $host;
	$camposcondicao = '';
	$condicao = '"'.$_POST[$campochave].'"';
	if(count($campochave)>1){
		$camposcondicao='concat('.implode(',', $campochave).')';
	}else{
		$camposcondicao=' '.$campochave[0].'';
	}
	$where= ' '.$camposcondicao.'="'.$condicao.'"';
	if($db->update('app_usuario', $dados, $where)){
		echo '{"status":"OK", "mensagemstatus":"Registro modificado com sucesso!"}';
	}else{
		echo '{"status":"ERRO", "mensagemstatus":"Registro não foi modificado!"}';
	}
	//exit();
}


if ( isset($_POST['controller']) &&($_POST['controller']=='usuario') && ($_POST['acao']=='vercad') ){
	//header('Content-type: application/x-json');
	$selectmateria = $db->fetch_array("select materia from questoes_materia order by materia asc ");
	$selectconcurso = $db->fetch_array("select concurso, concat(concurso,'-', concurso_banca,'-', date_format(concurso_data,'%d/%m/%Y') ) as label from questoes_concurso order by concurso asc, concurso_banca asc, concurso_data asc ");
	$selectdificuldade = array(1=>'FÁCIL',2=>'MODERADO',3=>'DIFÍCIL',4=>'MUITO DIFÍCIL');
	
	include("view_usuario_cad.php");
	//exit();
}

if ( isset($_POST['controller']) &&($_POST['controller']=='usuario') && ($_POST['acao']=='veredit') ){
	//header('Content-type: application/x-json');
	$dadosusuario = $db->fetch("select * from app_usuario where $campouuid='{$_POST['id']}' ");
	
	include("view_usuario_edit.php");
	//exit();
}




if ( isset($_POST['controller']) &&($_POST['controller']=='usuario') && ($_POST['acao']=='cad') ){
	header('Content-type: application/x-json');
	unset($dados);
	foreach ($campos as $titulo=>$campo){
		if($campo!='created'){
			$dados[$campo]=$_POST[$campo];
			if($tipos[$campo]== 'dt'){
				if(strlen($_POST[$campo])>6){
					$exp = explode('/', $_POST[$campo]);
					$_POST[$campo]=date('Y-m-d',mktime(0,0,0,$exp[1],$exp[0],$exp[2]));
				}else{
					$_POST[$campo]='';
				}
			}
			if(is_array($_POST[$campo]) && !empty(($_POST[$campo]))){
				$_POST[$campo]=implode(',',$_POST[$campo]);
			}
		}
	}
	$dados['senha']=$_POST['senha'];
	$dados['created'] = 'now()';
	$dados['usuariocriou'] = $usuario;
	$dados['ipcriou'] = $ip;
	$dados['hostcriou'] = $host;
	//unset($campouuid);
	if(isset($campouuid)){
		$dados[$campouuid] = 'uuid()';
	}
	//print_r($dados);
	//$db->insert('app_usuario', $dados);
	$db->debug=true;
	if($db->insert('app_usuario', $dados)){
		$status = 'OK';
		$mensagemstatus = "Registro incluído com sucesso!";
		ob_start();
			$somatorio = $db->fetch_array("select count(*) as total from app_usuario $filtro ");
			$total = $somatorio[0]['total'];
			$conteudoconsultarecente = $db->fetch_array("select * from app_usuario where date(created)=date(now())   ");
			$conteudoconsultaregistros = $db->fetch_array("select * from app_usuario $filtro  $order   limit $comeco,$maximoregistros ");
			$conteudoconsulta = $conteudoconsultarecente + $conteudoconsultaregistros;
		
			$paginas = ceil($total/$maximoregistros);
			if ($total != $paginas*maximoregistros){
			}
			if(empty($_GET['i'])){
				$_GET['i'] = 'usuario';
			}
			if(empty($_GET['acao'])){
				$_GET['acao'] = 'list';
			}
			
			include 'view_geral_mensagens.php';
			include("view_usuario_list.php");
		$conteudo = rawurlencode(ob_get_contents());
		ob_end_clean();
		//$saida = array("status"=>"OK","mensagemstatus"=>"Registro incluído com sucesso!", "conteudo"=>$conteudo);
		//echo json_encode($saida);
		echo '{"status":"OK", "mensagemstatus":"Registro incluído com sucesso!", "conteudo":"'.($conteudo).'"}';
		//echo "Registro incluído com sucesso!";
	}else{
		$status = 'ERRO';
		$mensagemstatus = "Registro não pode ser cadastrado!";
		echo '{"status":"ERRO", "mensagemstatus":"Registro não pode ser cadastrado!"}';
		//echo "Registro não pode ser cadastrado!";
	}
	
	//include 'view_geral_mensagens.php';
	//include("view_usuario_list.php");
	//exit();
}

if ( isset($_POST['controller']) &&($_POST['controller']=='usuario') && ($_POST['acao']=='edit') ){
	header('Content-type: application/x-json');
	unset($dados);
	foreach ($campos as $titulo=>$campo){
		if($campo!='created'){
			$dados[$campo]=$_POST[$campo];
			if($tipos[$campo]== 'dt'){
				if(strlen($_POST[$campo])>6){
					$exp = explode('/', $_POST[$campo]);
					$_POST[$campo]=date('Y-m-d',mktime(0,0,0,$exp[1],$exp[0],$exp[2]));
				}else{
					$_POST[$campo]='';
				}
			}
			if(is_array($_POST[$campo]) && !empty(($_POST[$campo]))){
				$_POST[$campo]=implode(',',$_POST[$campo]);
			}
		}
	}
	$dados['created'] = 'now()';
	$dados['usuariomodificou'] = $usuario;
	$dados['ipmodificou'] = $ip;
	$dados['hostmodificou'] = $host;
	
	
	if($db->update('app_usuario', $dados, "$campouuid='{$_POST[$campouuid]}'")){
		$status = 'OK';
		$mensagemstatus = "Registro modificado com sucesso!";
		ob_start();
		$somatorio = $db->fetch_array("select count(*) as total from app_usuario $filtro ");
		$total = $somatorio[0]['total'];
		$conteudoconsultarecente = $db->fetch_array("select * from app_usuario where date(created)=date(now())   ");
		$conteudoconsultaregistros = $db->fetch_array("select * from app_usuario $filtro  $order   limit $comeco,$maximoregistros ");
		$conteudoconsulta = $conteudoconsultarecente + $conteudoconsultaregistros;

		$paginas = ceil($total/$maximoregistros);
		if ($total != $paginas*$maximoregistros){
		}
		if(empty($_GET['i'])){
			$_GET['i'] = 'usuario';
		}
		if(empty($_GET['acao'])){
			$_GET['acao'] = 'list';
		}
			
		include 'view_geral_mensagens.php';
		include("view_usuario_list.php");
		$conteudo = rawurlencode(ob_get_contents());
		ob_end_clean();
		//$saida = array("status"=>"OK","mensagemstatus"=>"Registro incluído com sucesso!", "conteudo"=>$conteudo);
		//echo json_encode($saida);
		echo '{"status":"OK", "mensagemstatus":"Registro incluído com sucesso!", "conteudo":"'.($conteudo).'"}';
		//echo "Registro incluído com sucesso!";
	}else{
		$status = 'ERRO';
		$mensagemstatus = "Registro não pode ser cadastrado!";
		echo '{"status":"ERRO", "mensagemstatus":"Registro não pode ser cadastrado!"}';
		//echo "Registro não pode ser cadastrado!";
	}

	//include 'view_geral_mensagens.php';
	//include("view_usuario_list.php");
	//exit();
}


if ( isset($_POST['controller']) &&($_POST['controller']=='usuario') && ($_POST['acao']=='busca') ){
	$status = 'OK';
	$mensagemstatus = 'Registros ';
	$comeco = ($pagina-1) * $maximoregistros;
	$comeco = 0;
	$somatorio = $db->fetch_array("select count(*) as total from app_usuario $filtro ");
	$total = $somatorio[0]['total'];
	$maximoregistros = $total;
	$conteudoconsultarecente = $db->fetch("select * from app_usuario where date(created)=date(now())  ");
	$conteudoconsultaregistros = $db->fetch_array("select * from app_usuario  $filtro  $order  limit $comeco,$maximoregistros ");
	$conteudoconsulta = $conteudoconsultarecente + $conteudoconsultaregistros;
	if(!empty($status)){
		$status = '';
	}
	$paginas = ceil($total/$maximoregistros);
	if ($total != $paginas*maximoregistros){
	}
	if(empty($_GET['i'])){
		$_GET['i'] = 'usuario';
	}
	if(empty($_GET['acao'])){
		$_GET['acao'] = 'list';
	}
	include 'view_geral_mensagens.php';	
	include("view_usuario_list.php");
	include 'view_geral_ajax.php';
	//include("view_pagina_rodape.php");
}

if ( isset($_POST['controller']) &&($_POST['controller']=='usuario') && ($_POST['acao']=='exclui') ){
	$status = '';
	$mensagemstatus = '';
	$comeco = ($_POST['pagina']-1) * $maximoregistros;
	$camposcondicao = '';
	$condicao = '"'.$_POST[$campochave].'"';
	if(count($campochave)>1){
		$camposcondicao='concat('.implode(',', $campochave).')';
	}else{
		$camposcondicao=' '.$campochave[0].'';
	}
		
	if($db->query("delete from app_usuario where $camposcondicao=replace('{$_POST['id']}','#del','') ")){
		$status = 'OK';
		$mensagemstatus = 'Registro excluído com sucesso!';
	}else{
		$status = 'ERRO';
		$mensagemstatus = 'Houve problema para excluir o registro!';
	}
	
	/*
	$somatorio = $db->fetch_array("select count(*) as total from app_usuario $filtro ");
	$total = $somatorio[0]['total'];
	
	$conteudoconsultarecente = $db->fetch_array("select * from app_usuario where date(created)=date(now()) ");
	$conteudoconsultaregistros = $db->fetch_array("select * from app_usuario  $filtro  $order limit $comeco,$maximoregistros ");
	$conteudoconsulta = $conteudoconsultarecente + $conteudoconsultaregistros;

	$paginas = ceil($total/$maximoregistros);
	$pagina = $_POST['pagina'];
	if ($total != $paginas*maximoregistros){
	}
	if(empty($_GET['i'])){
		$_GET['i'] = 'usuario';
	}
	if(empty($_GET['acao'])){
		$_GET['acao'] = 'list';
	}
	
	include 'view_geral_mensagens.php';
	include("view_usuario_list.php");
	*/
	header('Content-type: application/x-json');
	
	echo '{"status":"'.$status.'", "mensagemstatus":"'.$mensagemstatus.'", "conteudo":"'.($conteudo).'"}';
	
}

if ( isset($_GET['controller']) &&($_GET['controller']=='usuario') && ($_GET['acao']=='xls') ){
	$status = 'OK';
	$mensagemstatus = 'Registros ';
	$comeco = ($pagina-1) * $maximoregistros;
	$comeco = 0;
	$somatorio = $db->fetch_array("select count(*) as total from app_usuario ");
	$total = $somatorio[0]['total'];
	$maximoregistros = $total;
	$conteudoconsultarecente = $db->fetch_array("select * from app_usuario $order");
	$l = 0;
	foreach($conteudoconsultarecente as $indice=>$valor){
		$c =0;
		foreach($campos as $titulo=>$nomecampo){
			$registros[$l][$c]=iconv('latin1', 'utf8', $valor[$nomecampo]);
			$c++;
		}
		$l++;
	}
	$conteudoconsulta = $conteudoconsultarecente ;
	include("view_questao_excel.php");
	//include 'view_geral_ajax.php';
	//include("view_pagina_rodape.php");
}
if ( isset($_GET['controller']) &&($_GET['controller']=='usuario') && ($_GET['acao']=='pdf') ){
	$status = 'OK';
	$mensagemstatus = 'Registros ';
	$comeco = ($pagina-1) * $maximoregistros;
	$comeco = 0;
	$somatorio = $db->fetch_array("select count(*) as total from app_usuario ");
	$total = $somatorio[0]['total'];
	$maximoregistros = $total;
	$conteudoconsultarecente = $db->fetch_array("select * from app_usuario $order");
	$l = 0;
	foreach($conteudoconsultarecente as $indice=>$valor){
		$c =0;
		foreach($campos as $titulo=>$nomecampo){
			$registros[$l][$c]=iconv('latin1', 'utf8', $valor[$nomecampo]);
			$c++;
		}
		$l++;
	}
	$conteudoconsulta = $conteudoconsultarecente ;
	include("view_questao_pdf.php");
	//include 'view_geral_ajax.php';
	//include("view_pagina_rodape.php");
}

if ( isset($_POST['controller']) &&($_POST['controller']=='usuario') && ($_POST['acao']=='verfiltro') ){
	//header('Content-type: application/x-json');

	include("view_geral_filtro.php");
	//exit();
}

if ( isset($_POST['controller']) &&($_POST['controller']=='usuario') && ($_POST['acao']=='filtro') ){
	header('Content-type: application/x-json');
	$status = 'OK';
	$mensagemstatus = "Filtro criado com sucesso!";
	//print_r($_POST);
	$post= UConverter::transcode(serialize($_POST), 'UTF-8', 'latin1');
	//print_r($post);
	$sql = '';
	$totalvalido = 0;
	$conectivoanterior = '';
	$estruturasql[0]['sentenca']='';
	$estruturasql[0]['conectivo']='';
	
	for($i=10;$i<=15;$i++){
		$nomecampo = $_POST['campo'.$i];
		$nomecriterio = $_POST['criterio'.$i];
		$nomevalor = $_POST['valor'.$i];
		$nomeconectivo = $_POST['conectivo'.$i];

		
		
		//echo $nomecampo.' '.$nomecriterio.' '.$nomevalor.' '.$nomeconectivo."\n";
		if(!empty($nomecampo)&&!empty($nomecriterio)&&!empty($nomevalor)){
			foreach($campos as $nomecampos=>$valorcampo){
				if($valorcampo==$nomecampo){
					foreach($filtros as $criterio=>$textocriterio){
						if($nomecriterio==$criterio){
							$nomevalor = str_replace('#.\'#', '', $nomevalor);
							switch ($nomecriterio){
								case 'dt_maiorque':
									if(strlen($nomevalor)>8){
										$nomevalor = str_replace('/', '-', $nomevalor);
										$nomevalor = str_replace('.', '-', $nomevalor);
										$nomevalor = substr($nomevalor, 6,9).'-'.substr($nomevalor, 3,2).'-'.substr($nomevalor, 0,2);
										$sql = " $nomecampo > \"$nomevalor\" ";
										$estruturasql[$totalvalido]['sentenca']= $sql;
										$estruturasql[$totalvalido]['conectivo']= $nomeconectivo;
										$totalvalido++;
									}else{
										$status = 'ERRO';
										$mensagemstatus .= "Data fora do padrão dd-mm-yyyy !";
									}
									break;
								case 'dt_menorque':
									if(strlen($nomevalor)>7){
										$nomevalor = str_replace('\/', '-', $nomevalor);
										$nomevalor = str_replace('\.', '-', $nomevalor);
										$nomevalor = substr($nomevalor, 6,9).'-'.substr($nomevalor, 3,2).'-'.substr($nomevalor, 0,2);
										
										$sql = " $nomecampo < \"$nomevalor\" ";
										$estruturasql[$totalvalido]['sentenca']= $sql;
										$estruturasql[$totalvalido]['conectivo']= $nomeconectivo;
										$totalvalido++;
									}else{
										$status = 'ERRO';
										$mensagemstatus .= "Data fora do padrão dd-mm-yyyy !";
									}
									break;
								case 'dt_igual':
									if(strlen($nomevalor)>8){
										$nomevalor = str_replace('/', '-', $nomevalor);
										$nomevalor = str_replace('.', '-', $nomevalor);
										$nomevalor = substr($nomevalor, 6,9).'-'.substr($nomevalor, 3,2).'-'.substr($nomevalor, 0,2);
										$sql = " $nomecampo = \"$nomevalor\" ";
										$estruturasql[$totalvalido]['sentenca']= $sql;
										$estruturasql[$totalvalido]['conectivo']= $nomeconectivo;
										$totalvalido++;
									}else{
										$status = 'ERRO';
										$mensagemstatus .= "Data fora do padrão dd-mm-yyyy !";
									}
									break;
								case 'dt_diferente':
									if(strlen($nomevalor)>8){
										$nomevalor = str_replace('/', '-', $nomevalor);
										$nomevalor = str_replace('.', '-', $nomevalor);
										$nomevalor = substr($nomevalor, 6,9).'-'.substr($nomevalor, 3,2).'-'.substr($nomevalor, 0,2);
										$sql = " $nomecampo <> \"$nomevalor\" ";
										$estruturasql[$totalvalido]['sentenca']= $sql;
										$estruturasql[$totalvalido]['conectivo']= $nomeconectivo;
										$totalvalido++;
									}else{
										$status = 'ERRO';
										$mensagemstatus .= "Data fora do padrão dd-mm-yyyy !";
									}
									break;
								case 'txt_contem':
									$sql = " $nomecampo like \"%$nomevalor%\" ";
									$estruturasql[$totalvalido]['sentenca']= $sql;
									$estruturasql[$totalvalido]['conectivo']= $nomeconectivo;
									$totalvalido++;
									break;
								case 'txt_naocontem':
									$sql = " $nomecampo not like \"%$nomevalor%\" ";
									$estruturasql[$totalvalido]['sentenca']= $sql;
									$estruturasql[$totalvalido]['conectivo']= $nomeconectivo;
									$totalvalido++;
									break;
								case 'txt_igual':
									$sql = " $nomecampo = \"$nomevalor\" ";
									$estruturasql[$totalvalido]['sentenca']= $sql;
									$estruturasql[$totalvalido]['conectivo']= $nomeconectivo;
									$totalvalido++;
									break;
								case 'txt_diferente':
									$sql = " $nomecampo <> \"$nomevalor\" ";
									$estruturasql[$totalvalido]['sentenca']= $sql;
									$estruturasql[$totalvalido]['conectivo']= $nomeconectivo;
									$totalvalido++;
									break;
								case 'num_maiorque':
									$sql = " $nomecampo > \"$nomevalor\" ";
									$estruturasql[$totalvalido]['sentenca']= $sql;
									$estruturasql[$totalvalido]['conectivo']= $nomeconectivo;
									$totalvalido++;
									break;
								case 'num_menorque':
									$sql = " $nomecampo < \"$nomevalor\" ";
									$estruturasql[$totalvalido]['sentenca']= $sql;
									$estruturasql[$totalvalido]['conectivo']= $nomeconectivo;
									$totalvalido++;
									break;
								case 'num_igual':
									$sql = " $nomecampo = \"$nomevalor\" ";
									$estruturasql[$totalvalido]['sentenca']= $sql;
									$estruturasql[$totalvalido]['conectivo']= $nomeconectivo;
									$totalvalido++;
									break;
								case 'num_diferente':
									$sql = " $nomecampo <> \"$nomevalor\" ";
									$estruturasql[$totalvalido]['sentenca']= $sql;
									$estruturasql[$totalvalido]['conectivo']= $nomeconectivo;
									$totalvalido++;
									break;
							}
						}
					}
					
				}		
			}
		}
	}
	$sentencas = count($estruturasql);
	$sentencapenultima = $sentencas-1;
	$sql = '';
	$conectivo = '';
	for($i=0;$i<$sentencas;$i++){
		if($i==0 && $sentencas==1){
			if(strlen($estruturasql[$i]['conectivo']>0 )){
				$sql .= $estruturasql[$i]['sentenca'].' '.$estruturasql[$i]['conectivo'].' ';
			}else{
				$sql .= $estruturasql[$i]['sentenca'].' ';
			}
		}
		if($i==0 && $sentencas>1){
			if(strlen($estruturasql[$i]['conectivo']>0 )){
				$sql .= $estruturasql[$i]['sentenca'].' '.$estruturasql[$i]['conectivo'].' ';
			}else{
				$sql .= $estruturasql[$i]['sentenca'].' AND ';
			}
		}
		if($i>0 && $i<$sentencapenultima ){
			if(strlen($estruturasql[$i]['conectivo']>0)){
				$sql .= $estruturasql[$i]['sentenca'].' '.$estruturasql[$i]['conectivo'].' ';
			}else{
				$sql .= $estruturasql[$i]['sentenca'].' AND ';
			}
		}
		if($i>0 && $sentencapenultima==$i  ){
			$sql .= $estruturasql[$i]['sentenca'].' ';
		}
	}
	
	$insere = $db->query("insert into app_filtro (usuario, modelo, sqlprocessado, dadospostados, created) 
			values('$usuario','{$_POST['controller']}', '$sql', '$post', now() ) 
			on duplicate key update sqlprocessado='$sql', dadospostados='$post', updated=now() ");
	if($insere){
		if(strlen($sql)>3){
			$filtro = ' where '.$sql;
		}else{
			$filtro = '';
		}
		ob_start();
		$somatorio = $db->fetch_array("select count(*) as total from app_usuario $filtro ");
		$total = $somatorio[0]['total'];
		//$conteudoconsultarecente = $db->fetch_array("select * from app_usuario where date(created)=date(now())  ");
		$conteudoconsultaregistros = $db->fetch_array("select * from app_usuario  $filtro  $order   limit $comeco,$maximoregistros ");
		$conteudoconsulta = $conteudoconsultaregistros;
	
		$paginas = ceil($total/$maximoregistros);
		if ($total != $paginas*$maximoregistros){
		}
		if(empty($_GET['i'])){
			$_GET['i'] = 'usuario';
		}
		if(empty($_GET['acao'])){
			$_GET['acao'] = 'list';
		}
			
		include 'view_geral_mensagens.php';
		include("view_usuario_list.php");
		$conteudo = rawurlencode(ob_get_contents());
		ob_end_clean();
		//$saida = array("status"=>"OK","mensagemstatus"=>"Registro incluído com sucesso!", "conteudo"=>$conteudo);
		//echo json_encode($saida);
		echo '{"status":"OK", "mensagemstatus":"Registro incluído com sucesso!", "conteudo":"'.($conteudo).'"}';
		//echo "Registro incluído com sucesso!";
	}else{
		$status = 'ERRO';
		$mensagemstatus = "Registro não pode ser cadastrado!";
		echo '{"status":"ERRO", "mensagemstatus":"Registro não pode ser cadastrado!"}';
		//echo "Registro não pode ser cadastrado!";
	}
}

if ( isset($_POST['controller']) &&($_POST['controller']=='usuario') && ($_POST['acao']=='select') ){
	//header('Content-type: application/x-json');
	$cola = implode("','", $_POST['valor']);
	//$db->debug=true;
	$selectmateria = $db->fetch_array("select {$_POST['campodestino']} from questoes_{$_POST['campodestino']} where {$_POST['campo']} in ('{$cola}') order by {$_POST['campodestino']} asc ");
	
	foreach($selectmateria as $indice=>$valor){
		 echo '<option value="'.$valor[$_POST['campodestino']].'">'.$valor['materia'].'-'.$valor[$_POST['campodestino']].'</option>';
	}
}

if ( isset($_POST['controller']) &&($_POST['controller']=='usuario') && ($_POST['acao']=='comuta') ){
	//header('Content-type: application/x-json');
	$cola = implode("','", $_POST['valor']);
	$db->debug=true;
	$selectmateria = $db->fetch_array("select {$_POST['resposta']} as resposta from questoes_{$_POST['campodestino']} where {$_POST['campo']} in ('{$cola}') order by {$_POST['ordem']} ");
	
	//echo "select {$_POST['resposta']} as resposta from questoes_{$_POST['campodestino']} where {$_POST['campo']} in ('{$cola}') order by {$_POST['ordem']} ";

	
	if($_POST['tipodestino']=='select'){
		foreach($selectmateria as $indice=>$valor){
			echo '<option value="'.$valor['resposta'].'">'.$valor['resposta'].'</option>';
		}
	}
	if($_POST['tipodestino']!='select'){
		echo $selectmateria[0]['resposta'];
	}
	
}


?>		
