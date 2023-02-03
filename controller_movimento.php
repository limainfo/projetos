<?php 
//select concat('<option value="',subdivisao,'-',descricao,'-',tipo,'">',subdivisao,'-',descricao,'-',tipo,'</option>') from app_movimento ORDER BY subdivisao asc, descricao asc;
session_start();

include 'parametros.php';
include 'db/Database.singleton.php';
$ip = $_SERVER['REMOTE_ADDR'];
$host = $_SERVER['HTTP_HOST'];
$maximoregistros = 30;
$controllernome = 'movimento';
$controllernomeplural = 'movimento';
$nometitulo = 'Controle de Movimentos';

$appID = '';
$status = '';

$filtros = array('dt_maiorque'=>'Maior que','dt_menorque'=>'Menor que','dt_igual'=>'Igual a','dt_diferente'=>'Diferente de','txt_contem'=>'Contém','txt_naocontem'=>'Não contém','txt_igual'=>'Igual a','txt_diferente'=>'Diferente de','num_maiorque'=>'Maior que','num_menorque'=>'Menor que','num_igual'=>'Igual a','num_diferente'=>'Diferente de');
$filtrostipo = array('dt_maiorque'=>'Data','dt_menorque'=>'Data','dt_igual'=>'Data','dt_diferente'=>'Data','txt_contem'=>'Texto','txt_naocontem'=>'Texto','txt_igual'=>'Texto','txt_diferente'=>'Texto','num_maiorque'=>'Numero','num_menorque'=>'Numero','num_igual'=>'Numero','num_diferente'=>'Numero');
$campos = array('Data'=>'data','Operação'=>'operacao','Identificação'=>'identificacao','Proced'=>'proced','Destino'=>'destino','Tipo'=>'tipo','TV'=>'tv','R.Voo'=>'rvoo', 'RWY'=>'rwy', 'Operador'=>'operador');
$tipos = array('data'=>'dt','operacao'=>'txt','identificacao'=>'txt','proced'=>'txt','destino'=>'txt','tipo'=>'txt','tv'=>'txt','rvoo'=>'txt', 'rwy'=>'txt', 'operador'=>'txt');
$funcoes = array('data'=>'editsuspenso','operacao'=>'editsuspenso','identificacao'=>'editsuspenso','proced'=>'editsuspenso','destino'=>'editsuspenso','tipo'=>'editsuspenso','tv'=>'editsuspenso','rvoo'=>'editsuspenso','rwy'=>'editsuspenso', 'operador'=>'editsuspenso');
$leitura = array('data'=>0,'operacao'=>0,'identificacao'=>0,'proced'=>0,'destino'=>0,'tipo'=>0,'tipo'=>0,'tv'=>0, 'rvoo'=>0, 'operador'=>0);

$tamanhocampo = array('Data'=>'20','Operação'=>'40','Identificação'=>'40','Proced'=>'40','Destino'=>'20','Tipo'=>'40','TV'=>'20','R.Voo'=>'40', 'RWY'=>'20', 'Operador'=>'40');
$campoautocompletar = array('Data'=>0,'Operação'=>1,'Identificação'=>1,'Proced'=>1,'Destino'=>1,'Tipo'=>1,'TV'=>1,'R.Voo'=>1, 'RWY'=>1, 'Operador'=>1);
$campoautosomar = array('Data'=>0,'Operação'=>0,'Identificação'=>0,'Proced'=>0,'Destino'=>0,'Tipo'=>0,'TV'=>0,'R.Voo'=>0, 'RWY'=>0, 'Operador'=>0);
$campoautosomarvalor = array('Data'=>0,'Operação'=>0,'Identificação'=>0,'Proced'=>0,'Destino'=>0,'Tipo'=>0,'TV'=>0,'R.Voo'=>0, 'RWY'=>0, 'Operador'=>0);

$displaydel[0] = 'operacao';
$campochave[0] = 'data';
$campouuid = 'id';

$mensagemstatus = '';
$datas = '';

if(empty($comeco)){
	$comeco = 0;
}

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
	if(empty($_GET['i'])){
		$_GET['i'] = 'movimento';
	}

$filtro = '';
$formbusca = '';
$order = ' order by `data` ASC ';

$db = (new Database())->obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE, DB_DEBUG);
$db->connect();


if(!empty($_GET['registrosporpagina'])&& is_numeric($_GET['registrosporpagina'])){
	$maximoregistros = $_GET['registrosporpagina'];
	$db->query('update app_filtro set qtdregistros='.$maximoregistros.' where usuario="'.$usuario.'" and modelo="'.$controllernome.'" ');
}


$sqlfiltro = $db->fetch("select * from app_filtro where usuario='$usuario' and modelo='$controllernome' ");



if(isset($sqlfiltro['qtdregistros'])&&($sqlfiltro['qtdregistros']>0)){
	$maximoregistros = $sqlfiltro['qtdregistros']; 	
	$vetorclassificacao = unserialize($sqlfiltro['classificacao']);
	if(!empty($vetorclassificacao)){
		foreach($vetorclassificacao as $campo=>$ordem){
			$ordenacao[$campo]=$ordem;
		}
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

if(count($ordenacao)>0){
	$order = ' order by '.implode(", ", array_mapk(function ($k, $v) { return "{$k}  " . real_escape_string($v); }, $ordenacao));
}
	
//echo $order;



if(empty($sqlfiltro['usuario'])&&empty($sqlfiltro['modelo'])){
		$insere = $db->query("insert into app_filtro (usuario, modelo, qtdregistros, created)	values('$usuario','{$controllernome}', $maximoregistros, now() ) ");
		$filtro = '';
		$formbusca = '';
}

if(isset($sqlfiltro['sqlprocessado'])&&(strlen($sqlfiltro['sqlprocessado'])>2)){
	$filtro = UConverter::transcode(' where  '.$sqlfiltro['sqlprocessado'], 'UTF-8', 'latin1');
	$formbusca = unserialize($sqlfiltro['dadospostados']);
}



if ( isset($_GET['i']) &&($_GET['i']=='movimento') && isset($_GET['acao']) && ($_GET['acao']=='exportar') ){
	$status = 'OK';
	$mensagemstatus = 'Registros ';
	$listas = $db->fetch_array("select CONCAT(data,'-',operacao,'-',identificacao) as lista from app_movimento  order by data asc ");
	$options = '';
	foreach ($listas as $indice=>$valor){
		$dado = iconv('UTF-8','ISO-8859-1',$valor['lista']);
		$options .= '<option value="'.$dado.'">'.$dado.'</option>';
	}
	file_put_contents('/var/www/onix/planejadas.txt', $options);
	$_GET['acao']='list';
}


if ( isset($_GET['i']) &&($_GET['i']=='movimento') && isset($_GET['acao']) && ($_GET['acao']=='list') ){

	
	$status = 'OK';
	$mensagemstatus = 'Registros ';
	include("view_pagina_cabecalho.php");
	include("view_pagina_menu.php");
	$comeco = ($pagina-1) * $maximoregistros;
	$somatorio = $db->fetch_array("select count(*) as total from app_movimento $filtro ");
	$total = $somatorio[0]['total'];
	$conteudoconsultarecente = $db->fetch_array("select * from app_movimento where date(created)=date(now()) ");
	if(!empty($filtro)){
		$conteudoconsultaregistros = $db->fetch_array("select * from app_movimento $filtro $order limit $comeco,$maximoregistros ");
		$conteudoconsulta = $conteudoconsultaregistros;  
	}else{
		$conteudoconsultaregistros = $db->fetch_array("select * from app_movimento $filtro $order limit $comeco,$maximoregistros ");
		$conteudoconsulta = $conteudoconsultaregistros;
	}
	if(!empty($status)){
		$status = '';
	}
	$paginas = ceil($total/$maximoregistros);
	if ($total != $paginas*$maximoregistros){
	}
	include 'view_geral_mensagens.php';	
	include("view_movimento_list.php");
	include 'view_geral_ajax.php';
	include("view_pagina_rodape.php");
}


if ( isset($_POST['i']) &&($_POST['i']=='movimento') && ($_POST['acao']=='list_') ){
	$status = 'OK';
	$mensagemstatus = 'Registros ';
	include("view_pagina_cabecalho.php");
	include("view_pagina_menu.php");
	$comeco = ($pagina-1) * $maximoregistros;
	$somatorio = $db->fetch_array("select count(*) as total from app_movimento $filtro ");
	$total = $somatorio[0]['total'];
	$conteudoconsultarecente = $db->fetch_array("select * from app_movimento where date(created)=date(now()) ");
	if(!empty($filtro)){
		$conteudoconsultaregistros = $db->fetch_array("select * from app_movimento $filtro $order limit $comeco,$maximoregistros ");
		$conteudoconsulta = $conteudoconsultaregistros;
	}else{
		$conteudoconsultaregistros = $db->fetch_array("select * from app_movimento $filtro $order limit $comeco,$maximoregistros ");
		$conteudoconsulta = $conteudoconsultaregistros;
	}
	if(!empty($status)){
		$status = '';
	}
	$paginas = ceil($total/$maximoregistros);
	if ($total != $paginas*maximoregistros){
	}
	include 'view_geral_mensagens.php';
	include("view_movimento_list_.php");
	include 'view_geral_ajax.php';
	include("view_pagina_rodape.php");
}





if ( isset($_POST['controller']) &&($_POST['controller']=='movimento') && ($_POST['acao']=='ajaxmodifica') ){
	header('Content-type: application/x-json');
	$dados[$_POST['campo']]=$_POST['valor'];
	if(($tipos[$_POST['campo']] == 'dt')){
		if(strlen($dados[$_POST['campo']])>6){
			$exp = explode('/', $_POST['valor']);
			$dados[$_POST['campo']]=date('Y-m-d',mktime(0,0,0,$exp[1],$exp[0],$exp[2]));
		}else{
			$dados[$_POST['campo']]='';
		}
	}
	
	//Converte formato decimal do Brasil em USA
	if(($tipos[$_POST['campo']] == 'num')&&(!is_number($dados[$_POST['campo']]))){
		if(!empty($dados[$_POST['campo']])){
			$exp = str_replace('.','', $dados[$_POST['campo']]);
			$exp = str_replace(',','.', $exp);
			$dados[$_POST['campo']]=$exp;
		}else{
			$dados[$_POST['campo']]=0;
		}
	}
	if(($tipos[$_POST['campo']] == 'num')){
		$dados[$_POST['campo']]=round($dados[$_POST['campo']],2);
	}
	$dados['updated'] = 'now()';
	$dados['usuariomodificou'] = $usuario;
	$dados['ipmodificou'] = $ip;
	$dados['hostmodificou'] = $host;
	
	$where= ' id="'.$_POST['id'].'"';
	if($db->update('app_movimento', $dados, $where)){
		//$db->query("update app_movimento set pln_diaria=round(qtd_militar*qtd_diaria*valor_diaria+adicional,2), pln_passagem=qtd_militar*valor_passagem where id='{$_POST['id']}' ");
		echo '{"status":"OK", "mensagemstatus":"Registro modificado com sucesso!"}';
	}else{
		echo '{"status":"ERRO", "mensagemstatus":"Registro não foi modificado!"}';
	}
	exit();
}


if ( isset($_POST['controller']) &&($_POST['controller']=='movimento') && ($_POST['acao']=='vercad') ){

	include("view_movimento_cad.php");
	//exit();
}


if ( isset($_POST['controller']) &&($_POST['controller']=='movimento') && ($_POST['acao']=='cad') ){
	header('Content-type: application/x-json');
	unset($dados);
	foreach ($campos as $titulo=>$campo){
		if($tipos[$campo]== 'dt'){
				if(strlen($_POST[$campo])>6){
					$exp = explode('/', $_POST[$campo]);
					$_POST[$campo]=date('Y-m-d',mktime(0,0,0,$exp[1],$exp[0],$exp[2]));
				}else{
					$_POST[$campo]='';
				}
		}
				$dados[$campo]=$_POST[$campo];
	}
	$dados['created'] = 'now()';
	$dados['usuariocriou'] = $usuario;
	$dados['ipcriou'] = $ip;
	$dados['hostcriou'] = $host;
	$dados['id'] = 'uuid()';
	//print_r($dados);
	//$db->insert('app_movimento', $dados);
	unset($dados['']);
	if($db->insert('app_movimento', $dados)){
		$status = 'OK';
		$mensagemstatus = "Registro incluído com sucesso!";
		ob_start();
			$somatorio = $db->fetch_array("select count(*) as total from app_movimento $filtro ");
			$total = $somatorio[0]['total'];
			$conteudoconsultarecente = $db->fetch_array("select * from app_movimento where date(created)=date(now())   ");
			$conteudoconsultaregistros = $db->fetch_array("select * from app_movimento $filtro  $order   limit $comeco,$maximoregistros ");
			$conteudoconsulta = $conteudoconsultarecente + $conteudoconsultaregistros;
		
			$paginas = ceil($total/$maximoregistros);
			if ($total != $paginas*maximoregistros){
			}
			if(empty($_GET['i'])){
				$_GET['i'] = 'movimento';
			}
			if(empty($_GET['acao'])){
				$_GET['acao'] = 'list';
			}
			
			include 'view_geral_mensagens.php';
			include("view_movimento_list.php");
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
	//include("view_movimento_list.php");
	//exit();
}


if ( isset($_POST['controller']) &&($_POST['controller']=='movimento') && ($_POST['acao']=='busca') ){
	$status = 'OK';
	$mensagemstatus = 'Registros ';
	$comeco = ($pagina-1) * $maximoregistros;
	$comeco = 0;
	$somatorio = $db->fetch_array("select count(*) as total from app_movimento $filtro ");
	$total = $somatorio[0]['total'];
	$maximoregistros = $total;
	$conteudoconsultarecente = $db->fetch_array("select * from app_movimento where date(created)=date(now())  ");
	$conteudoconsultaregistros = $db->fetch_array("select * from app_movimento  $filtro  $order  limit $comeco,$maximoregistros ");
	$conteudoconsulta = $conteudoconsultarecente + $conteudoconsultaregistros;
	if(!empty($status)){
		$status = '';
	}
	$paginas = ceil($total/$maximoregistros);
	if ($total != $paginas*maximoregistros){
	}
	if(empty($_GET['i'])){
		$_GET['i'] = 'movimento';
	}
	if(empty($_GET['acao'])){
		$_GET['acao'] = 'list';
	}
	include 'view_geral_mensagens.php';	
	include("view_movimento_list.php");
	include 'view_geral_ajax.php';
	//include("view_pagina_rodape.php");
}

if ( isset($_POST['controller']) &&($_POST['controller']=='movimento') && ($_POST['acao']=='ver') ){	
	
	$filtros = array('dt_maiorque'=>'Maior que','dt_menorque'=>'Menor que','dt_igual'=>'Igual a','dt_diferente'=>'Diferente de','txt_contem'=>'Contém','txt_naocontem'=>'Não contém','txt_igual'=>'Igual a','txt_diferente'=>'Diferente de','num_maiorque'=>'Maior que','num_menorque'=>'Menor que','num_igual'=>'Igual a','num_diferente'=>'Diferente de');
	$filtrostipo = array('dt_maiorque'=>'Data','dt_menorque'=>'Data','dt_igual'=>'Data','dt_diferente'=>'Data','txt_contem'=>'Texto','txt_naocontem'=>'Texto','txt_igual'=>'Texto','txt_diferente'=>'Texto','num_maiorque'=>'Numero','num_menorque'=>'Numero','num_igual'=>'Numero','num_diferente'=>'Numero');
	$campos = array('Data'=>'data','Operação'=>'operacao','Identificação'=>'identificacao','Proced'=>'proced','Destino'=>'destino','Tipo'=>'tipo','TV'=>'tv','R.Voo'=>'rvoo', 'RWY'=>'rwy', 'Operador'=>'operador');
	$tipos = array('data'=>'dt','operacao'=>'txt','identificacao'=>'txt','proced'=>'txt','destino'=>'txt','tipo'=>'txt','tv'=>'txt','rvoo'=>'txt', 'rwy'=>'txt', 'operador'=>'txt');
	$funcoes = array('data'=>'editsuspenso','operacao'=>'editsuspenso','identificacao'=>'editsuspenso','proced'=>'editsuspenso','destino'=>'editsuspenso','tipo'=>'editsuspenso','tv'=>'editsuspenso','rvoo'=>'editsuspenso', 'operador'=>'editsuspenso');
	$leitura = array('data'=>0,'operacao'=>0,'identificacao'=>0,'proced'=>0,'destino'=>0,'tipo'=>0,'tipo'=>0,'tv'=>0, 'rvoo'=>0, 'operador'=>0);

	$tamanhocampo = array('Data'=>'20','Operação'=>'40','Identificação'=>'40','Proced'=>'40','Destino'=>'20','Tipo'=>'40','TV'=>'20','R.Voo'=>'40', 'RWY'=>'20', 'Operador'=>'40');
	$campoautocompletar = array('Data'=>0,'Operação'=>1,'Identificação'=>1,'Proced'=>1,'Destino'=>1,'Tipo'=>1,'TV'=>1,'R.Voo'=>1, 'RWY'=>1, 'Operador'=>1);
	$campoautosomar = array('Data'=>0,'Operação'=>0,'Identificação'=>0,'Proced'=>0,'Destino'=>0,'Tipo'=>0,'TV'=>0,'R.Voo'=>0, 'RWY'=>0, 'Operador'=>0);
	$campoautosomarvalor = array('Data'=>0,'Operação'=>0,'Identificação'=>0,'Proced'=>0,'Destino'=>0,'Tipo'=>0,'TV'=>0,'R.Voo'=>0, 'RWY'=>0, 'Operador'=>0);

	
	$status = 'OK';
	$mensagemstatus = 'Registros ';
	$comeco = ($pagina-1) * $maximoregistros;
	$comeco = 0;
	
	$comeco = ($pagina-1) * $maximoregistros;
	$somatorio = $db->fetch("select count(*) as total from app_movimento, orcamento_andamento where orcamento_andamento.planejado=app_movimento.descricao and app_movimento.id='{$_POST['id']}' ");
	$total = $somatorio['total'];
	$maximoregistros = $total;
	$conteudoconsultaregistros = $db->fetch_array("select * from app_movimento, orcamento_andamento where orcamento_andamento.planejado=app_movimento.descricao and app_movimento.id='{$_POST['id']}' ");
	$conteudoconsulta =$conteudoconsultaregistros;  
	$total = $somatorio['total'];
	if(!empty($status)){
		$status = '';
	}
	if($maximoregistros>0){
		$paginas = ceil($total/$maximoregistros);
	}else{
		$paginas = 1;
	}
	if ($total != $paginas*$maximoregistros){
		//$paginas++;
	}

	
	$totaldiaria = 0;
	$totalpassagem = 0;
	foreach($conteudoconsultaregistros as $campo=>$valor){
		$totaldiaria += ($valor['valor']+$valor['acrescimo']);
		$totalpassagem += $valor['passagem'];
	}

	$db->query("update app_movimento set real_diaria='{$totaldiaria}', real_passagem='{$totalpassagem}' where id='{$_POST['id']}' ");
	
	include 'view_geral_mensagens.php';
	include("view_movimento_list_.php");
	echo "
<script type=\"text/javascript\">
	$(function() {
		\$( \"#real_diaria{$_POST['id']}\").val('{$totaldiaria}');
		\$( \"#real_passagem{$_POST['id']}\").val('{$totalpassagem}');
	});	
			
</script>			
			
			";
//	include 'view_geral_ajax.php';
	//include("view_pagina_rodape.php");
}


if ( isset($_POST['controller']) &&($_POST['controller']=='movimento') && ($_POST['acao']=='exclui') ){
	$status = '';
	$mensagemstatus = '';
	$comeco = ($_POST['pagina']-1) * $maximoregistros;
	if($db->query("delete from app_movimento where id=replace('{$_POST['id']}','#del','') ")){
		$status = 'OK';
		$mensagemstatus = 'Registro excluído com sucesso!';
	}else{
		$status = 'ERRO';
		$mensagemstatus = 'Houve problema para excluir o registro!';
	}
	
	/*
	$somatorio = $db->fetch_array("select count(*) as total from app_movimento $filtro ");
	$total = $somatorio[0]['total'];
	
	$conteudoconsultarecente = $db->fetch_array("select * from app_movimento where date(created)=date(now()) ");
	$conteudoconsultaregistros = $db->fetch_array("select * from app_movimento  $filtro  $order limit $comeco,$maximoregistros ");
	$conteudoconsulta = $conteudoconsultarecente + $conteudoconsultaregistros;

	$paginas = ceil($total/$maximoregistros);
	$pagina = $_POST['pagina'];
	if ($total != $paginas*maximoregistros){
	}
	if(empty($_GET['i'])){
		$_GET['i'] = 'movimento';
	}
	if(empty($_GET['acao'])){
		$_GET['acao'] = 'list';
	}
	
	include 'view_geral_mensagens.php';
	include("view_movimento_list.php");
	*/
	header('Content-type: application/x-json');
	
	echo '{"status":"'.$status.'", "mensagemstatus":"'.$mensagemstatus.'", "conteudo":"'.($conteudo).'"}';
	
}

if ( isset($_GET['controller']) &&($_GET['controller']=='movimento') && ($_GET['acao']=='xls') ){
	$status = 'OK';
	$mensagemstatus = 'Registros ';
	$comeco = ($pagina-1) * $maximoregistros;
	$comeco = 0;
	/*
	$somatorio = $db->fetch_array("select count(*) as total from app_movimento ");
	$total = $somatorio[0]['total'];
	$maximoregistros = $total;
	$conteudoconsultarecente = $db->fetch_array("select * from app_movimento $order");
	*/
	$l = 0;
	
	$somatorio = $db->fetch_array("select count(*) as total from app_movimento $filtro ");
	$total = $somatorio[0]['total'];
	$maximoregistros = $total;
	
	if(!empty($filtro)){
		$conteudoconsultaregistros = $db->fetch_array("select * from app_movimento $filtro $order  ");
		$conteudoconsultarecente = $conteudoconsultaregistros;
	}else{
		$conteudoconsultaregistros = $db->fetch_array("select * from app_movimento $filtro $order  ");
		//$conteudoconsulta = $conteudoconsultarecente + $conteudoconsultaregistros;
		$conteudoconsultarecente = $conteudoconsultaregistros;
	}
	
	
	foreach($conteudoconsultarecente as $indice=>$valor){
		$c =0;
		foreach($campos as $titulo=>$nomecampo){
			//$registros[$l][$c]=iconv('latin1', 'utf8', $valor[$nomecampo]);
			$registros[$l][$c]=$valor[$nomecampo];
			$c++;
		}
		$l++;
	}
	$conteudoconsulta = $conteudoconsultarecente ;
	include("view_movimento_excel.php");
	//include 'view_geral_ajax.php';
	//include("view_pagina_rodape.php");
}
if ( isset($_GET['controller']) && ($_GET['controller']=='movimento') && ($_GET['acao']=='pdf') ){
	$status = 'OK';
	$mensagemstatus = 'Registros ';
	$comeco = ($pagina-1) * $maximoregistros;
	$comeco = 0;
	/*
	$somatorio = $db->fetch_array("select count(*) as total from app_movimento ");
	$total = $somatorio[0]['total'];
	$maximoregistros = $total;
	$conteudoconsultarecente = $db->fetch_array("select * from app_movimento $order");
	*/
	$l = 0;
	
	$somatorio = $db->fetch_array("select count(*) as total from app_movimento $filtro ");
	$total = $somatorio[0]['total'];
	$maximoregistros = $total;
	
	if(!empty($filtro)){
		$conteudoconsultaregistros = $db->fetch_array("select * from app_movimento $filtro $order  ");
		$conteudoconsultarecente = $conteudoconsultaregistros;
	}else{
		$conteudoconsultaregistros = $db->fetch_array("select * from app_movimento $filtro $order  ");
		//$conteudoconsulta = $conteudoconsultarecente + $conteudoconsultaregistros;
		$conteudoconsultarecente = $conteudoconsultaregistros;
	}
	
	
	foreach($conteudoconsultarecente as $indice=>$valor){
		$c =0;
		foreach($campos as $titulo=>$nomecampo){
			//$registros[$l][$c]=iconv('latin1', 'utf8', $valor[$nomecampo]);
			$registros[$l][$c]=$valor[$nomecampo];
			$c++;
		}
		$l++;
	}
	$conteudoconsulta = $conteudoconsultarecente ;
	include("view_movimento_pdf.php");
	//include 'view_geral_ajax.php';
	//include("view_pagina_rodape.php");
}

if (isset($_POST['controller']) && ($_POST['controller']=='movimento') && ($_POST['acao']=='verfiltro') ){
	//header('Content-type: text/html');
	include("view_movimento_filtro.php");
}

if (isset($_POST['controller']) && ($_POST['controller']=='movimento') && ($_POST['acao']=='filtro') ){
	header('Content-type: application/x-json');
	$status = 'OK';
	$mensagemstatus = "Filtro criado com sucesso!";
	$post= serialize($_POST);
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
	
	$insere = $db->query("insert into app_filtro (usuario, modelo, sqlprocessado, dadospostados, criado) 
			values('$usuario','{$_POST['controller']}', '$sql', '$post', now() ) 
			on duplicate key update sqlprocessado='$sql', dadospostados='$post', atualizado=now() ");
	if($insere){
		if(strlen($sql)>3){
			$filtro = ' where '.$sql;
		}else{
			$filtro = '';
		}
		ob_start();
		$somatorio = $db->fetch_array("select count(*) as total from app_movimento $filtro ");
		
		$total = $somatorio[0]['total'];
		$conteudoconsultarecente = $db->fetch_array("select * from app_movimento where date(created)=date(now()) ");
		if(!empty($filtro)){
			$conteudoconsultaregistros = $db->fetch_array("select * from app_movimento $filtro $order limit $comeco,$maximoregistros ");
			$conteudoconsulta = $conteudoconsultaregistros;
		}else{
			$conteudoconsultaregistros = $db->fetch_array("select * from app_movimento $filtro $order limit $comeco,$maximoregistros ");
			$conteudoconsulta = $conteudoconsultarecente + $conteudoconsultaregistros;
		}
		
	
		$paginas = ceil($total/$maximoregistros);
		if ($total != $paginas*maximoregistros){
		}
		if(empty($_GET['i'])){
			$_GET['i'] = 'movimento';
		}
		if(empty($_GET['acao'])){
			$_GET['acao'] = 'list';
		}
			
		include 'view_geral_mensagens.php';
		include("view_movimento_list.php");
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

if ( isset($_GET['controller']) &&($_GET['controller']=='movimento') && ($_GET['acao']=='upload') ){

	$json = array();
    if(!empty($_FILES['upl_file'])){ 
        $dir = "./uploads/"; 
        $allowTypes = array('jpg', 'png', 'jpeg', 'gif', 'pdf', 'doc', 'docx'); 
        $fileName = basename($_FILES['upl_file']['name']); 
        $filePath = $dir.$fileName; 
        // Check whether file type is valid 
        $fileType = pathinfo($filePath, PATHINFO_EXTENSION); 
        if(in_array($fileType, $allowTypes)){ 
            // Upload file to the server 
            if(move_uploaded_file($_FILES['upl_file']['tmp_name'], $filePath)){ 
                $json = 'success'; 
            } else {
                $json = 'failed';
            }
        } 
    }
    header('Content-Type: application/json');
    echo json_encode($json);

}

if ( isset($_GET['controller']) &&($_GET['controller']=='movimento') && ($_GET['acao']=='relcompleto') ){
	$status = 'OK';
	$mensagemstatus = 'Registros ';
	$comeco = 0;
	
	//$db->close();
	$db = null;
	$maximografico = 0;

	include 'parametrosonix.php';
	$db = new Database(DB_ONIX_SERVER, DB_ONIX_USER, DB_ONIX_PASS, DB_ONIX_DATABASE, DB_DEBUG);
	
	$db->connect();
	$db->query("SET NAMES 'utf8'");
	$db->query("SET character_set_connection=utf8");
	$db->query("SET character_set_client=utf8");
	$db->query("SET character_set_results=utf8");
	$db->query("SET NAMES 'utf8' COLLATE 'utf8_general_ci'");
	$db->query("SET CHARACTER SET utf8");
	
	$sql = "select round((valor_alocado),2) as alocado
	from recurso_alocado
	where id_exercicio=215 and id_etapa = 7354
	order by id_recurso_alocado desc
	limit 0,1	";
	$limiterecurso = $db->fetch_array($sql);
	//print_r($limiterecurso);
	$recursoalocado = 0;
	foreach($limiterecurso as $i=>$value){
		$recursoalocado = $value['alocado'];
	}
	
	if($maximografico<$recursoalocado){
		$maximografico = $recursoalocado;
	}
	//-------------------------------------------------------------------------------------------------------------------------------------------//
	date_default_timezone_set('America/Manaus');
	//error_reporting(E_ALL);
	//ini_set('display_errors', 1);
	
	$numeros = '';
	$inicio = 1;
	$diariasexecutadasmes = '';
	$diariasexecutadas = '';
	$planejado_diaria_somanecessario = '';
	$planejado_diaria_soma = '';
	$planejado_diaria_mes = '';
	$total_executado = 0;
	
	
	for($i=1;$i<=12;$i++){
		$sqlcompleta = '';
		$k=0;
		$k1 = 0;
		$k2 = 0;
		//Nova interpretação das tabelas
		$sql = "
		select os.id_os,os_debito.valor,os_debito.pago_valor, os_debito.pago_sn, os.os, os.id_fase , month(min(pernoite.saida_data)) dtinicio
		from os
		left join os_debito on (os_debito.id_os=os.id_os )
		inner join pernoite on  (os.id_os=pernoite.id_os and pernoite.id_nd=5 )
		inner join fase on (fase.id_fase=os.id_fase  and fase.id_fase not in (29,31) )
		inner join identificador on (identificador.id_identificador=os.id_identificador and identificador.id_identificador=3811)
		inner join exercicio on (exercicio.id_exercicio=identificador.id_exercicio and exercicio.id_exercicio=215)
		inner join evento on (evento.id_evento=os.id_evento)
		inner join subetapa on ( subetapa.id_subetapa=evento.id_subetapa )
		inner join etapa on (etapa.id_etapa=subetapa.id_etapa  and etapa.id_etapa = 7354  )
		inner join meta on ( meta.id_meta=etapa.id_meta and meta.id_meta in (2464))
		group by pernoite.id_os
		having dtinicio=$i
		order by pernoite.saida_data
		";
		
		
		$somageral = 0;
		
		$contagem =0;
		$buscasql = $db->fetch_array($sql);
		foreach($buscasql as $indice=>$db_look){
			
			$intermediario01 = $db_look['id_os']; // id_os
			$intermediario02 = $db_look['valor']; // valor
			$intermediario03 = $db_look['pago_valor']; // valor pago
			$intermediario04 = $db_look['pago_sn']; // pagn_sn
			$intermediario05 = $db_look['os']; // os.os
			$intermediario06 = $db_look['id_fase']; // os.id_fase
		
			$sql = "
			select round(sum((diaria_qtd * diaria_estimada + acrescimo_valor)),2) as calculado, pernoite.id_fase, desconto_aux_alimentacao, desconto_aux_transporte, fase.display as fasedisplay
			from pernoite
			inner join os on (os.id_os=pernoite.id_os)
			inner join identificador on (os.id_identificador=identificador.id_identificador and identificador.id_exercicio=215)
			inner join fase on fase.id_fase=pernoite.id_fase
			where  pernoite.id_os=$intermediario01
			";
			$soma = 0;
				
			$buscasql02 = $db->fetch_array($sql);
			foreach($buscasql02 as $indice=>$pernoites){
				$alimentacao = 0;
				$transporte = 0;
				$intermediario07 = $pernoites['calculado']; // valor calculado
				$fase = $pernoites['id_fase']; // pernoite.id_fase
				$alimentacao = $pernoites['desconto_aux_alimentacao']; // aux_alim
				$transporte = $pernoites['desconto_aux_transporte']; // aux_transporte
				$fasetxt = $pernoites['fasedisplay']; // fase
				$parcela = $intermediario07;
				$soma += $parcela - $alimentacao - $transporte;
				$somageral += $soma;
			}
			$contagem++;
		}		
		
		$executadas[$i] = $somageral;
		$diariasexecutadasmes .= $executadas[$i].', ';
		
		if($i>1){
			$executadas[$i] = $executadas[$i]+$executadas[$i-1];
		}
		$diariasexecutadas .= $executadas[$i].', ';
	}
	$executadasonixdiarias = $executadas[12];
	
//	print_r($executadas);exit();
	$passagemsomageral = 0;
	$passagensexecutadas = '';
	$passagensexecutadasmes = '';
	$passagens = '';
	for($i=1;$i<=12;$i++){
		$sqlcompleta = '';
		$sql = "
		select pernoite.id_os, round(pernoite.passagem_valor,2) as passagem , month(min(pernoite.saida_data)) dtinicio
		from os
		left join os_debito on (os_debito.id_os=os.id_os )
		inner join pernoite on  (os.id_os=pernoite.id_os and pernoite.id_nd=5 )
		inner join fase on (fase.id_fase=os.id_fase  and fase.id_fase not in (29,31) )
		left join identificador on (identificador.id_identificador=os.id_identificador and identificador.id_identificador=3811)
		left join exercicio on (exercicio.id_exercicio=identificador.id_exercicio and exercicio.id_exercicio=215)
		inner join evento on (evento.id_evento=os.id_evento)
		inner join subetapa on ( subetapa.id_subetapa=evento.id_subetapa )
		inner join etapa on (etapa.id_etapa=subetapa.id_etapa  and etapa.id_etapa = 7354  )
		inner join meta on ( meta.id_meta=etapa.id_meta and meta.id_meta in (2464))
		group by pernoite.id_os
		having dtinicio=$i
		order by pernoite.saida_data
		";
		$contagem =0;
		$buscasql = $db->fetch_array($sql);
		$passagemsomageral = 0;
		
		foreach($buscasql as $indice=>$db_look){
			$intermediario01 = $db_look['id_os']; 
			$intermediario02 = $db_look['passagem'];
			$passagemsomageral += $intermediario02;
		}
		$passagens[$i] = $passagemsomageral;
		if($i>1){
			$passagens[$i] = $passagens[$i]+$passagens[$i-1];
		}
		
		$passagensexecutadasmes .= $passagemsomageral.', ';
		$passagensexecutadas .= $passagens[$i].', ';
		
		if($maximografico<$passagemsomageral){
			$maximografico = $passagemsomageral;
		}
		if($maximografico<$passagens[$i]){
			$maximografico = $passagens[$i];
		}
		
}

$executadasonixpassagens = $passagens[12];

	
	$limiterecurso = '';
	for($i=1;$i<=13;$i++){
		$k=0;
	
		$somatoriop[$i] = $recursoalocado;
		$limiterecurso .= $recursoalocado.', ';
	}
	
	$i=0;
	
	$numerosv1 = '';
	$numerosv2 = '';
	$inicio = 1;
	
	$db = null;
	
	include 'parametros.php';
	$db = new Database(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE, DB_DEBUG);
	//$db->obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
	//echo '<br>('.DB_SERVER.', '.DB_USER.', '.DB_PASS.', '.DB_DATABASE.')';
	$db->connect(true);
	$db->query("SET NAMES 'utf8'");
	$db->query("SET character_set_connection=utf8");
	$db->query("SET character_set_client=utf8");
	$db->query("SET character_set_results=utf8");
	$db->query("SET NAMES 'utf8' COLLATE 'utf8_general_ci'");
	$db->query("SET CHARACTER SET utf8");

	$subdivisoes = $db->fetch_array("select subdivisao from app_movimento where  ano=2015 group by data order by data asc ");
	$descricoes = $db->fetch_array("select * from app_movimento inner join orcamento_andamento on planejado=descricao and meta='ADM09_OS' and app_movimento.ano=2015 order by subdivisao asc, inicio asc, descricao asc ");
	//echo '<pre>';print_r($descricoes);echo '</pre>';exit();
	$planejados = $db->fetch_array("select * from app_movimento where  ano=2015 order by subdivisao asc, inicio asc ");
	$andamentos = $db->fetch_array("select * from orcamento_andamento where meta='ADM09_OS' and  ano=2015 order by planejado asc, saida_data asc ");
	
	
	$c =0;
	$totalplanejadas = 0;
	$totalnecessario = 0;
	for($i=1;$i<=13;$i++){
		$ano = ' and ano=2015  ';
		$iniciomes = mktime(0,0,0,$i,1,2015);
		$posterior = $i+1;
		$fimmes = mktime(0,0,0,$posterior,0,2015);
		$mes = ' and month(inicio)='.$i;
		$mespassagem=' and month(saida_data)='.$i;;
		
		if($i==13){
			$ano = ' and ano=2016 ';
			$iniciomes = mktime(0,0,0,1,1,2016);
			$fimmes = mktime(0,0,0,2,0,2016);
			$mes = ' and month(inicio)=1';
			$mespassagem=' and month(saida_data)=1';;
		}
		$planejadosmes = $db->fetch_array("select round(sum(pln_diaria),2) as diaria from app_movimento where 1=1 $ano $mes  and status  not like '%CANCELAD%'  group by month(inicio) ");
		//echo "select round(sum(pln_diaria),2) as diaria from app_movimento where 1=1 $ano $mes  and status  not like '%CANCELAD%'  group by month(inicio) <br>";
		
		//echo "<br> select round(sum(pln_diaria),2) as diaria from app_movimento where 1=1 $ano $mes  and status  not like '%CANCELAD%'  group by month(inicio) <br>";
		//print_r($planejadosmes);
		if(empty($planejadosmes[0]['diaria'])){
			$planejadosmes[0]['diaria'] = 0;
		}
		$parcela = $planejadosmes[0]['diaria'];
		$diariaplanejadames .= $parcela.' ,'; 
		$totalplanejadas += $parcela;
		$diariaplanejadamessoma .= $totalplanejadas.' ,';

		
		
		$necessariomes = $db->fetch_array("select round(sum(pln_diaria),2) as diaria from app_movimento where 1=1 $ano $mes and status like '%NÃO INICIADA%'  and status  not like '%CANCELAD%' group by month(inicio) ");
		//echo "select round(sum(pln_diaria),2) as diaria from app_movimento where 1=1 $ano $mes and status like '%NÃO INICIADA%'  and status  not like '%CANCELAD%' group by month(inicio) <br>";
		if(empty($necessariomes[0]['diaria'])){
			$necessariomes[0]['diaria'] = 0;
		}
		
		$necessariomestxt .= $necessariomes[0]['diaria'].' ,';
		$totalnecessario += $necessariomes[0]['diaria'];
		//$necessariomessoma .= $totalnecessario.' ,';
		//echo "<br>".$$necessariomes[0]['diaria'];
		//echo "<br>".$totalnecessario;
		
		$passagemmes = $db->fetch_array("select round(sum(passagem),2) as passagem from orcamento_andamento where 1=1 $ano $mespassagem  group by month(saida_data) ");
		//echo "select round(sum(passagem),2) as passagem from orcamento_andamento where 1=1 $ano $mespassagem  group by month(data_saida) ";
		//print_r($passagemmes);
		if(empty($passagemmes[0]['passagem'])){
			$passagemmes[0]['passagem'] = 0;
		}
		
		$passagemmestxt .= $passagemmes[0]['passagem'].' ,';
		$totalpassagem += $passagemmes[0]['passagem'];
		
		
		$executadasmes = $db->fetch_array("select round(sum(valor+acrescimo),2) as executada from orcamento_andamento where 1=1 $ano $mespassagem  group by month(saida_data) ");
		//echo "select round(sum(passagem),2) as passagem from orcamento_andamento where 1=1 $ano $mespassagem  group by month(data_saida) ";
		//print_r($passagemmes);
		if(empty($executadasmes[0]['executada'])){
			$executadasmes[0]['executada'] = 0;
		}
		
		$executadasmestxt .= $executadasmes[0]['executada'].' ,';
		$totalexecutadas += $executadasmes[0]['executada'];
		$executadasmessoma .= $totalexecutadas.' ,';
		
		
		
		
	}
	//print_r($diariaplanejadames);exit();
	//echo $necessariomessoma.'<br>';
	//exit();
	if($maximografico<$totalexecutadas){
		$maximografico = $totalexecutadas;
	}
	if($maximografico<$totalplanejadas){
		$maximografico = $totalplanejadas;
	}
	if($maximografico<$totalnecessario){
		$maximografico = $totalnecessario;
	}
	if($maximografico<$totalpassagem){
		$maximografico = $totalpassagem;
	}
	
	if($maximografico<$passagensexecutadas){
		$maximografico = $passagensexecutadas;
	}
	$maximografico = ceil($maximografico);
	//$necessariomessoma='';
	$valornecessario=$totalplanejadas-$recursoalocado;
	
	for($i=1;$i<=13;$i++){
		$necessariomessoma .= $valornecessario.', ';
		$passagemmessoma .= $totalpassagem.' ,';
	}
		
	/*
	
echo '
{"name": "LIMITE_DIARIA","data": ['.$limiterecurso.']},<br>
{"name": "PLANEJADOS", "data": ['.$diariaplanejadamessoma.']},<br>
{"name": "EXECUTADOS", "data": ['.$diariasexecutadas.']},<br>
{"name": "PLANEJADOS_MES","data": ['.$diariaplanejadames.']},<br>
{"name": "EXECUTADAS_MES","data": ['.$diariasexecutadasmes.']},    <br>        
{"name": "NECESSÁRIO","data": ['.$necessariomessoma.']},<br>
{"name": "PASSAGENS","data": ['.$passagensexecutadas.']},<br>
{"name": "PASSAGENS_MES","data": ['.$passagensexecutadasmes.']}<br>
		
		';exit();	
	*/
$optionjson=<<<OPTIONJSON
{"chart": {"backgroundColor": {"linearGradient": { x1: 0, y1: 0, x2: 1, y2: 1 },"stops": [[0, 'rgb(48, 48, 96)'],[1, 'rgb(0, 0, 0)']]},"borderColor": '#000000',"borderWidth": 2, "className": 'dark-container',"plotBackgroundColor": 'rgba(255, 255, 255, .1)',"plotBorderColor": '#CCCCCC',"plotBorderWidth": 1,"renderTo": "planejamento", "type": "line", "marginRight": 30,"marginBottom": 25,"width": 1250,"height": 700},
"colors": ["#fffea0","#fffc00","#ff6600","green","#00ffc6","#ff0036","#f28f43","#77a1e5","#c42525","#a6c96a"],            
"title": {"style": {"color": '#C0C0C0',"font": 'bold 22px "Trebuchet MS", Verdana, sans-serif'},"text": "DIÁRIAS - DIVISÃO DE OPERAÇÕES - 2015","x": -20},
"subtitle": {"text": "Fonte: Dados do Onix","x": -20},
"xAxis": {"gridLineColor": '#333333',"lineColor": '#A0A0A0',"categories": ["Jan", "Fev", "Mar", "Abr", "Mai", "Jun","Jul", "Ago", "Set", "Out", "Nov", "Dez","S/PREV"],"plotLines": [{"value": 0,"width": 1,"color": "#333333"},{"value": 1,"width": 1,"color": "#333333"},{"value": 2,"width": 1,"color": "#333333"},{"value": 3,"width": 1,"color": "#333333"},{"value": 4,"width": 1,"color": "#333333"},{"value": 5,"width": 1,"color": "#333333"},{"value": 6,"width": 1,"color": "#333333"},{"value": 7,"width": 1,"color": "#333333"},{"value": 8,"width": 1,"color": "#333333"},{"value": 9,"width": 1,"color": "#333333"},{"value": 10,"width": 1,"color": "#333333"},{"value": 11,"width": 1,"color": "#333333"},{"value": 12,"width": 1,"color": "#333333"}]},
"yAxis": {"gridLineColor": '#333333',"labels": {"style": {"color": '#A0A0A0'}},	"lineColor": '#A0A0A0',	"minorTickInterval": null, "tickColor": '#A0A0A0', "tickWidth": 1, "title": {"style": {"color": '#CCC',	"fontWeight": 'bold', "fontSize": '12px', "fontFamily": 'Trebuchet MS, Verdana, sans-serif'	}},"max": $maximografico,"min": 0,"title": {"text": "Recursos de diárias em 2015"},"plotLines": [{"value": 0,"width": 1,"color": "#808080"}]},
"legend": {"layout": "vertical","align": "right","verticalAlign": "top","x": -10,"y": 100,"borderWidth": 0,"itemStyle":{"color": 'white',"fontSize": "14px", "fontWeight": "bold"}},
"plotOptions": {"line": {"dataLabels": {"enabled": true,rotation: 15,"style":{"font": 'bold "Trebuchet MS", Verdana, sans-serif', "fontSize":'12px'},"formatter":function() {return 'R$ '+ this.y +'';},
			"color": "white"}}},
"series": [
{"name": "LIMITE_DIARIA      -> R$ $recursoalocado","data": [$limiterecurso]},
{"name": "PLANEJADOS         -> R$ $totalplanejadas", "data": [$diariaplanejadamessoma]},
{"name": "PLANEJADOS_MES","data": [$diariaplanejadames]},
{"name": "EXECUTADAS_MES ","data": [$executadasmestxt]},            
{"name": "NECESSÁRIO         -> R$ $valornecessario","data": [$necessariomessoma]},
{"name": "EXECUTADOS         -> R$ $executadasonixdiarias", "data": [$executadasmessoma]},
{"name": "PASSAGENS          -> R$ $executadasonixpassagens","data": [$passagemmessoma]}
	]
}
OPTIONJSON;
        
//----------------------------------------------------------------------------------------------------------------------------------//
	

	$caminho = dirname($_SERVER["SCRIPT_FILENAME"]);
	$nomearquivo = 'options'.date('Ymdhis');
	$filename = $caminho.'/js/phantomjs/'.$nomearquivo;

	$optionjson = str_replace("\n", '', $optionjson);
	file_put_contents($filename.'.json', $optionjson);
	$protegido=escapeshellcmd('cd '.$caminho.'/js/phantomjs;./phantomjs '.$caminho.'/js/phantomjs/highcharts-convert.js -infile '.$nomearquivo.'.json -outfile '.$nomearquivo.'.png  -width 1024 ' );
	$protegido = 'cd '.$caminho.'/js/phantomjs;./phantomjs '.$caminho.'/js/phantomjs/highcharts-convert.js -infile '.$nomearquivo.'.json -outfile '.$nomearquivo.'.png  -width 950  ';
	$output = shell_exec($protegido);
	//echo "<pre>$output</pre>";
	
//---------------------------------------------------Planilha NOVA - PLANEJAMENTO TIPO	
	
	$c =0;
	$totalplanejadas = 0;
	
	$essencialpassagem = 0;
	$essencialdiaria = 0;
	
	$tmp01=0;
	$tmp02=0;
	
	$recomendavelpassagem = 0;
	$recomendaveldiaria = 0;
	
	$necessariopassagem = 0;
	$necessariodiaria = 0;
	
	$nppassagem = 0;
	$npdiaria = 0;
	//print_r($planejados);exit();
	
	foreach($subdivisoes as $indice=>$valor){
		$subdivisao[$c]['subdivisao']=$valor['subdivisao'];
	
		foreach($planejados as $indice02=>$valor02){
				$tmp01=0;
			if($valor02['subdivisao']==$valor['subdivisao'] && trim($valor02['tipo'])=='ESSENCIAL'){
				$tmp01 = $valor02['pln_diaria'];
				$tmp02 = (empty($subdivisao[$c]['essencial_diaria'])?0:$subdivisao[$c]['essencial_diaria']);
				$subdivisao[$c]['essencial_diaria'] =  $tmp01+$tmp02;
				$essencialdiaria += $tmp01;
	
				$tmp01=0;
				$tmp01 = $valor02['pln_passagem'];
				$tmp02 = (empty($subdivisao[$c]['essencial_passagem'])?0:$subdivisao[$c]['essencial_passagem']);
				$subdivisao[$c]['essencial_passagem'] =  $tmp01+$tmp02;
				$essencialpassagem += $tmp01;
			}
			if($valor02['subdivisao']==$valor['subdivisao'] && trim($valor02['tipo'])=='RECOMENDÁVEL'){
				$tmp01 = $valor02['pln_diaria'];
				$tmp02 = (empty($subdivisao[$c]['recomendavel_diaria'])?0:$subdivisao[$c]['recomendavel_diaria']);
				$subdivisao[$c]['recomendavel_diaria'] =  $tmp01+$tmp02;
				$recomendaveldiaria += $tmp01;
	
				$tmp01=0;
				$tmp01 = $valor02['pln_passagem'];
				$tmp02 = (empty($subdivisao[$c]['recomendavel_passagem'])?0:$subdivisao[$c]['recomendavel_passagem']);
				$subdivisao[$c]['recomendavel_passagem'] =  $tmp01+$tmp02;
				$recomendavelpassagem += $tmp01;
			}
			if($valor02['subdivisao']==$valor['subdivisao'] && trim($valor02['tipo'])=='NECESSÁRIO'){
				$tmp01 = $valor02['pln_diaria'];
				$tmp02 = (empty($subdivisao[$c]['necessario_diaria'])?0:$subdivisao[$c]['necessario_diaria']);
				$subdivisao[$c]['necessario_diaria'] =  $tmp01+$tmp02;
				$necessariodiaria += $tmp01;
	
				$tmp01=0;
				$tmp01 = $valor02['pln_passagem'];
				$tmp02 = (empty($subdivisao[$c]['necessario_passagem'])?0:$subdivisao[$c]['necessario_passagem']);
				$subdivisao[$c]['necessario_passagem'] =  $tmp01+$tmp02;
				$necessariopassagem += $tmp01;
			}
			if($valor02['subdivisao']==$valor['subdivisao'] && trim($valor02['tipo'])=='NÃO PLANEJADA'){
				$tmp01 = $valor02['pln_diaria'];
				$tmp02 = (empty($subdivisao[$c]['np_diaria'])?0:$subdivisao[$c]['np_diaria']);
				$subdivisao[$c]['np_diaria'] =  $tmp01+$tmp02;
				$npdiaria += $tmp01;
	
				$tmp01=0;
				$tmp01 = $valor02['pln_passagem'];
				$tmp02 = (empty($subdivisao[$c]['np_passagem'])?0:$subdivisao[$c]['np_passagem']);
				$subdivisao[$c]['np_passagem'] =  $tmp01+$tmp02;
				$nppassagem += $tmp01;
			}
		}
	
		$tmp01=0;
		$tmp02=0;
		$c++;
	}
	//echo $essencialdiaria.' '.$essencialpassagem;exit();
	$c =0;
	$orcamentoportipo = '';
	$essecial01=0;
	$essecial02=0;
	$necessario01=0;	
	$necessario02=0;
	$recomendavel01=0;
	$recomendavel02=0;
	$naoplanejada01=0;
	$naoplanejada02=0;
	
	//print_r($subdivisao);exit();
	
	foreach($subdivisao as $indice=>$valor){
		$classe = ' class="linhaimpar" ';
		if($c%2){
			$classe = ' class="linhapar" ';
		}
		$tmp01 = $valor['total_planejado'];
		$tmp02 = $valor['total_executado'];
		$orcamentoportipo .= '<tr '.$classe.'><td class="destaque">'.$valor['subdivisao'].'</td><td>R$ '.$valor['essencial_diaria'].'</td><td>R$ '.$valor['essencial_passagem'].'</td><td>R$ '.$valor['necessario_diaria'].'</td><td>R$ '.$valor['necessario_passagem'].'</td><td>R$ '.$valor['recomendavel_diaria'].'</td><td>R$ '.$valor['recomendavel_passagem'].'</td><td>R$ '.$valor['np_diaria'].'</td><td>R$ '.$valor['np_passagem'].'</td></tr>';
	
		$essecial01+=$valor['essencial_diaria'];
		$essecial02+=$valor['essencial_passagem'];
		$necessario01+=$valor['necessario_diaria'];	
		$necessario02+=$valor['necessario_passagem'];
		$recomendavel01+=$valor['recomendavel_diaria'];
		$recomendavel02+=$valor['recomendavel_passagem'];
		$naoplanejada01+=$valor['np_diaria'];
		$naoplanejada02+=$valor['np_passagem'];
			
		$c++;
	}

	//$orcamentoportipo .= '<tr '.$classe.'><td class="destaque">TOTAL GERAL</td><td '.($essencial01<0?'class="atencao"':'').'>R$ '.$essencial01.'</td><td  '.($essencial02<0?'class="atencao"':'').'>R$ '.$essencial02.'</td><td  '.($necessario01<0?'class="atencao"':'').'>R$ '.$necessario01.'</td><td  '.($necessario02<0?'class="atencao"':'').'>R$ '.$necessario02.'</td><td  '.($recomendavel01<0?'class="atencao"':'').'>R$ '.$recomendavel01.'</td><td  '.($recomendavel02<0?'class="atencao"':'').'>R$ '.$recomendavel02.'</td><td  '.($naoplanejada01<0?'class="atencao"':'').'>R$ '.$naoplanejada01.'</td><td  '.($naoplanejada02<0?'class="atencao"':'').'>R$ '.$naoplanejada02.'</td></tr>';
	$orcamentoportipo .= '<tr '.$classe.'><td class="destaque">TOTAL GERAL</td><td '.($essencialdiaria<0?'class="atencao"':'').'><b>R$ '.$essencialdiaria.'</b></td><td  '.($essencialpassagem<0?'class="atencao"':'').'><b>R$ '.$essencialpassagem.'</b></td><td  '.($necessariodiaria<0?'class="atencao"':'').'><b>R$ '.$necessariodiaria.'</b></td><td  '.($necessariopassagem<0?'class="atencao"':'').'><b>R$ '.$necessariopassagem.'</b></td><td  '.($recomendaveldiaria<0?'class="atencao"':'').'><b>R$ '.$recomendaveldiaria.'</b></td><td  '.($recomendavelpassagem<0?'class="atencao"':'').'><b>R$ '.$recomendavelpassagem.'</b></td><td  '.($npdiaria<0?'class="atencao"':'').'><b>R$ '.$npdiaria.'</b></td><td  '.($nppassagem<0?'class="atencao"':'').'><b>R$ '.$nppassagem.'</b></td></tr>';
	$orcamentoportipo .= '<tr><td colspan="9">&nbsp;</td></tr>';
	
	$orcamentoportipo .= '<tr '.$classe.'><td class="destaque">TOTAL DIÁRIAS</td><td><b>R$ '.($essencialdiaria+$recomendaveldiaria+$necessariodiaria+$npdiaria).'</b></td><td  class="destaque">TOTAL PASSAGENS</td><td><b>R$ '.($essencialpassagem+$recomendavelpassagem+$necessariopassagem+$nppassagem).'</b></td><td class="destaque">VALOR ALOCADO DIÁRIA</td><td><b>R$ '.$recursoalocado.'</b></td><td  class="destaque">VALOR ALOCADO PASSAGEM</td><td colspan="2"><b></b></td></tr>';
	
//---------------------------------------------------Planilha NOVA
	//print_r($descricoes);exit();
	
//---------------------------------------------------Planilha NOVA - EXECUTADA TIPO
	
	$c =0;
	$totalplanejadas = 0;
	
	$essencialpassagem = 0;
	$essencialdiaria = 0;
	
	$tmp01=0;
	$tmp02=0;
	
	$recomendavelpassagem = 0;
	$recomendaveldiaria = 0;
	
	$necessariopassagem = 0;
	$necessariodiaria = 0;
	
	$nppassagem = 0;
	$npdiaria = 0;
	$c=0;
	foreach($subdivisoes as $indice=>$valor){
		unset($subdivisao[$c]['essencial_diaria']);
		unset($subdivisao[$c]['essencial_passagem']);
		unset($subdivisao[$c]['recomendavel_diaria']);
		unset($subdivisao[$c]['recomendavel_passagem']);
		unset($subdivisao[$c]['necessario_diaria']);
		unset($subdivisao[$c]['necessario_passagem']);
		unset($subdivisao[$c]['np_diaria']);
		unset($subdivisao[$c]['np_passagem']);
		$c++;
	}
	$c=0;
	foreach($subdivisoes as $indice=>$valor){
		$subdivisao[$c]['subdivisao']=$valor['subdivisao'];
	
		foreach($descricoes as $indice02=>$valor02){
			$tmp01 = 0;
			if($valor02['subdivisao']==$valor['subdivisao'] && trim($valor02['tipo'])=='ESSENCIAL'){
				$tmp01 = $valor02['valor'];
				$tmp02 = (empty($subdivisao[$c]['essencial_diaria'])?0:$subdivisao[$c]['essencial_diaria']);
				$subdivisao[$c]['essencial_diaria'] =  $tmp01+$tmp02;
				$essencialdiaria += $tmp01;
	
				$tmp01=0;
				$tmp01 = $valor02['passagem'];
				$tmp02 = (empty($subdivisao[$c]['essencial_passagem'])?0:$subdivisao[$c]['essencial_passagem']);
				$subdivisao[$c]['essencial_passagem'] =  $tmp01+$tmp02;
				$essencialpassagem += $tmp01;
			}
			if($valor02['subdivisao']==$valor['subdivisao'] && trim($valor02['tipo'])=='RECOMENDÁVEL'){
				$tmp01 = $valor02['valor'];
				$tmp02 = (empty($subdivisao[$c]['recomendavel_diaria'])?0:$subdivisao[$c]['recomendavel_diaria']);
				$subdivisao[$c]['recomendavel_diaria'] =  $tmp01+$tmp02;
				$recomendaveldiaria += $tmp01;
	
				$tmp01=0;
				$tmp01 = $valor02['passagem'];
				$tmp02 = (empty($subdivisao[$c]['recomendavel_passagem'])?0:$subdivisao[$c]['recomendavel_passagem']);
				$subdivisao[$c]['recomendavel_passagem'] =  $tmp01+$tmp02;
				$recomendavelpassagem += $tmp01;
			}
			if($valor02['subdivisao']==$valor['subdivisao'] && trim($valor02['tipo'])=='NECESSÁRIO'){
				$tmp01 = $valor02['valor'];
				$tmp02 = (empty($subdivisao[$c]['necessario_diaria'])?0:$subdivisao[$c]['necessario_diaria']);
				$subdivisao[$c]['necessario_diaria'] =  $tmp01+$tmp02;
				$necessariodiaria += $tmp01;
	
				$tmp01=0;
				$tmp01 = $valor02['passagem'];
				$tmp02 = (empty($subdivisao[$c]['necessario_passagem'])?0:$subdivisao[$c]['necessario_passagem']);
				$subdivisao[$c]['necessario_passagem'] =  $tmp01+$tmp02;
				$necessariopassagem += $tmp01;
			}
			if($valor02['subdivisao']==$valor['subdivisao'] && trim($valor02['tipo'])=='NÃO PLANEJADA'){
				$tmp01 = $valor02['valor'];
				$tmp02 = (empty($subdivisao[$c]['np_diaria'])?0:$subdivisao[$c]['np_diaria']);
				$subdivisao[$c]['np_diaria'] =  $tmp01+$tmp02;
				$npdiaria += $tmp01;
	
				$tmp01=0;
				$tmp01 = $valor02['passagem'];
				$tmp02 = (empty($subdivisao[$c]['np_passagem'])?0:$subdivisao[$c]['np_passagem']);
				$subdivisao[$c]['np_passagem'] =  $tmp01+$tmp02;
				$nppassagem += $tmp01;
			}
		}
	
		$tmp01=0;
		$tmp02=0;
		$c++;
	}
	//print_r($subdivisao);exit();
	//echo $essencialdiaria.' '.$essencialpassagem;exit();
	$c =0;
	$orcamentoportipoexecutado = '';
	$essecial01=0;
	$essecial02=0;
	$necessario01=0;
	$necessario02=0;
	$recomendavel01=0;
	$recomendavel02=0;
	$naoplanejada01=0;
	$naoplanejada02=0;
	
	//print_r($subdivisao);exit();
	
	foreach($subdivisao as $indice=>$valor){
		$classe = ' class="linhaimpar" ';
		if($c%2){
			$classe = ' class="linhapar" ';
		}
		$orcamentoportipoexecutado .= '<tr '.$classe.'><td class="destaque">'.$valor['subdivisao'].'</td><td>R$ '.$valor['essencial_diaria'].'</td><td>R$ '.$valor['essencial_passagem'].'</td><td>R$ '.$valor['necessario_diaria'].'</td><td>R$ '.$valor['necessario_passagem'].'</td><td>R$ '.$valor['recomendavel_diaria'].'</td><td>R$ '.$valor['recomendavel_passagem'].'</td><td>R$ '.$valor['np_diaria'].'</td><td>R$ '.$valor['np_passagem'].'</td></tr>';
	
		$essecial01+=$valor['essencial_diaria'];
		$essecial02+=$valor['essencial_passagem'];
		$necessario01+=$valor['necessario_diaria'];
		$necessario02+=$valor['necessario_passagem'];
		$recomendavel01+=$valor['recomendavel_diaria'];
		$recomendavel02+=$valor['recomendavel_passagem'];
		$naoplanejada01+=$valor['np_diaria'];
		$naoplanejada02+=$valor['np_passagem'];
			
		$c++;
	}
	
	//$orcamentoportipo .= '<tr '.$classe.'><td class="destaque">TOTAL GERAL</td><td '.($essencial01<0?'class="atencao"':'').'>R$ '.$essencial01.'</td><td  '.($essencial02<0?'class="atencao"':'').'>R$ '.$essencial02.'</td><td  '.($necessario01<0?'class="atencao"':'').'>R$ '.$necessario01.'</td><td  '.($necessario02<0?'class="atencao"':'').'>R$ '.$necessario02.'</td><td  '.($recomendavel01<0?'class="atencao"':'').'>R$ '.$recomendavel01.'</td><td  '.($recomendavel02<0?'class="atencao"':'').'>R$ '.$recomendavel02.'</td><td  '.($naoplanejada01<0?'class="atencao"':'').'>R$ '.$naoplanejada01.'</td><td  '.($naoplanejada02<0?'class="atencao"':'').'>R$ '.$naoplanejada02.'</td></tr>';
	$orcamentoportipoexecutado .= '<tr '.$classe.'><td class="destaque">TOTAL GERAL</td><td '.($essencialdiaria<0?'class="atencao"':'').'><b>R$ '.$essencialdiaria.'</b></td><td  '.($essencialpassagem<0?'class="atencao"':'').'><b>R$ '.$essencialpassagem.'</b></td><td  '.($necessariodiaria<0?'class="atencao"':'').'><b>R$ '.$necessariodiaria.'</b></td><td  '.($necessariopassagem<0?'class="atencao"':'').'><b>R$ '.$necessariopassagem.'</b></td><td  '.($recomendaveldiaria<0?'class="atencao"':'').'><b>R$ '.$recomendaveldiaria.'</b></td><td  '.($recomendavelpassagem<0?'class="atencao"':'').'><b>R$ '.$recomendavelpassagem.'</b></td><td  '.($npdiaria<0?'class="atencao"':'').'><b>R$ '.$npdiaria.'</b></td><td  '.($nppassagem<0?'class="atencao"':'').'><b>R$ '.$nppassagem.'</b></td></tr>';
	$orcamentoportipoexecutado .= '<tr><td colspan="9">&nbsp;</td></tr>';
	
	$orcamentoportipoexecutado .= '<tr '.$classe.'><td class="destaque">TOTAL DIÁRIAS</td><td><b>R$ '.($essencialdiaria+$recomendaveldiaria+$necessariodiaria+$npdiaria).'</b></td><td  class="destaque">TOTAL PASSAGENS</td><td><b>R$ '.($essencialpassagem+$recomendavelpassagem+$necessariopassagem+$nppassagem).'</b></td><td class="destaque">VALOR ALOCADO DIÁRIA</td><td><b>R$ '.$recursoalocado.'</b></td><td  class="destaque">VALOR ALOCADO PASSAGEM</td><td colspan="2"><b></b></td></tr>';
	
	//---------------------------------------------------Planilha NOVA
	
	//echo $orcamentoportipoexecutado;exit();
	$percentagemessencial = round(100*$essencialdiaria/$recursoalocado,2);
	$percentagemnecessario = round(100*$necessariodiaria/$recursoalocado,2);
	$percentagemrecomendavel = round(100*$recomendaveldiaria/$recursoalocado,2);
	$percentagemnaoplanejado = round(100*$npdiaria/$recursoalocado,2);
	$saldo = $recursoalocado - ($essencialdiaria+$necessariodiaria+$recomendaveldiaria+$npdiaria);
	$percentagemsaldo = round(100*$saldo/$recursoalocado,2);
	
	$optionjson=<<<OPTIONJSON
{"chart": {"plotBackgroundColor": null,"plotShadow": null,"plotBorderWidth": 1,"width": 800,"height": 400},
"colors": ["yellow","cyan","blue","red","green"],
"title": {"style": {"color": '#C0C0C0',"font": 'bold 14px "Trebuchet MS", Verdana, sans-serif'},"text": "DIÁRIAS - EXECUTADAS - DIVISÃO DE OPERAÇÕES - 2015","x": -20},
"tooltip": {"headerFormat":'<span style="font-size:11px">{series.name}</span><br>',"pointFormat": '<span style="color:{point.color}">{point.name}</span>: <b>{point.y:.2f}%</b> of total<br/>'},
"plotOptions": {"pie": {"dataLabels": {"enabled": true,"format":'<b>{point.name}</b>: {point.percentage:.1f}%', "style":{"color":'black'} }}},
"series": [{
"type":'pie',
"name":'Executadas',
"data":[
["NECESSÁRIAS -> R$ $necessariodiaria", $percentagemnecessario],
["ESSENCIAIS -> R$ $essencialdiaria", $percentagemessencial],
["RECOMENDÁVEIS -> R$ $recomendaveldiaria",$percentagemrecomendavel],
["NÃO PLANEJADAS -> R$ $npdiaria", $percentagemnaoplanejado],
{"name":'SALDO -> R$ $saldo',"y":$percentagemsaldo,"sliced":true,"selected":true}
	]	
}]
}
OPTIONJSON;
//	"style":{"font": 'bold "Trebuchet MS", Verdana, sans-serif', "fontSize":'18px'},
	$caminho = dirname($_SERVER["SCRIPT_FILENAME"]);
	$nomearquivo = 'options'.date('Ymdhis').'02';
	$filename02 = $caminho.'/js/phantomjs/'.$nomearquivo;
	
	$optionjson = str_replace("\n", '', $optionjson);
	file_put_contents($filename02.'.json', $optionjson);
	$protegido=escapeshellcmd('cd '.$caminho.'/js/phantomjs;./phantomjs highcharts-convert.js -infile '.$nomearquivo.'.json -outfile '.$nomearquivo.'.png  -width 1024 ' );
	$protegido = 'cd '.$caminho.'/js/phantomjs;./phantomjs highcharts-convert.js -infile '.$nomearquivo.'.json -outfile '.$nomearquivo.'.png  -width 950  ';
	//echo $protegido;exit();
	$output = shell_exec($protegido);
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	$c=0;
	foreach($subdivisoes as $indice=>$valor){
		unset($subdivisao[$c]['essencial_diaria']);
		unset($subdivisao[$c]['essencial_passagem']);
		unset($subdivisao[$c]['recomendavel_diaria']);
		unset($subdivisao[$c]['recomendavel_passagem']);
		unset($subdivisao[$c]['necessario_diaria']);
		unset($subdivisao[$c]['necessario_passagem']);
		unset($subdivisao[$c]['np_diaria']);
		unset($subdivisao[$c]['np_passagem']);
		$c++;
	}
	
	$c =0;
	$totalplanejadas = 0;
	$totalplanejadaspassagem = 0;
	foreach($subdivisoes as $indice=>$valor){
		$subdivisao[$c]['subdivisao']=$valor['subdivisao'];
		
		foreach($planejados as $indice02=>$valor02){
			$tmp01=0;
			if($valor02['subdivisao']==$valor['subdivisao']){
				$tmp01 = $valor02['pln_diaria'];
				$tmp02 = (empty($subdivisao[$c]['total_planejado'])?0:$subdivisao[$c]['total_planejado']);
				$subdivisao[$c]['total_planejado'] =  $tmp01+$tmp02;
				$totalplanejadas += $tmp01;
				$tmp01=0;
				
				$tmp01 = $valor02['pln_passagem'];
				$tmp02 = (empty($subdivisao[$c]['total_planejado_passagem'])?0:$subdivisao[$c]['total_planejado_passagem']);
				$subdivisao[$c]['total_planejado_passagem'] =  $tmp01+$tmp02;
				$totalplanejadaspassagem += $tmp01;
				
			}	
		}
		$c++;
	}
	$c =0;
	foreach($subdivisoes as $indice=>$valor){	
		foreach($descricoes as $indice02=>$valor02){
			$tmp01=0;
			if($valor02['subdivisao']==$valor['subdivisao']){
				$tmp01 = $valor02['valor'];
				$tmp03 = $valor02['acrescimo'];
				$tmp02 = (empty($subdivisao[$c]['total_executado'])?0:$subdivisao[$c]['total_executado']);
				$subdivisao[$c]['total_executado'] =  $tmp01+$tmp02+$tmp03;
				
				$tmp01=0;
				$tmp04 = $valor02['passagem'];
				$tmp05 = (empty($subdivisao[$c]['total_executado_passagem'])?0:$subdivisao[$c]['total_executado_passagem']);
				$subdivisao[$c]['total_executado_passagem'] =  $tmp04+$tmp05;
				
			}
		}
	
		$c++;
	}

	$c =0;
	$total_executado = 0;
	$totalexecutadas = 0;
	$total_executadopassagem = 0;
	$totalexecutadaspassagem = 0;
	foreach($subdivisao as $indice=>$valor){
		$tmp01 = $valor['total_planejado'];
		$tmp02 = $valor['total_executado'];
		$totalexecutadas += $tmp02;
		$subdivisao[$c]['saldo'] =  $tmp01-$tmp02;
		$subdivisao[$c]['percentagem'] =  round($tmp02*100/$tmp01,2);
		
		$tmp03 = $valor['total_planejado_passagem'];
		$tmp04 = $valor['total_executado_passagem'];
		$totalexecutadaspassagem += $tmp04;
		$subdivisao[$c]['saldo_passagem'] =  $tmp03-$tmp04;
		$subdivisao[$c]['percentagem_passagem'] =  round($tmp04*100/$tmp03,2);
		
		$c++;
	}

	$c =0;
	$orcamentopordivisao = '';
	$orcamentopordivisaopassagem = '';
	foreach($subdivisao as $indice=>$valor){
		$classe = ' class="linhaimpar" ';
		if($c%2){
			$classe = ' class="linhapar" ';
		}
		$tmp01 = $valor['total_planejado'];
		$tmp02 = $valor['total_executado'];
		$orcamentopordivisao .= '<tr '.$classe.'><td class="destaque">'.$valor['subdivisao'].'</td><td>R$ '.$valor['total_planejado'].'</td><td>R$ '.$valor['total_executado'].'</td><td '.($valor['saldo']<0?'class="atencao"':'').'> R$ '.$valor['saldo'].'</td><td '.($valor['percentagem']<0?'class="atencao"':'').'>'.$valor['percentagem'].'%</td></tr>'; 

		$tmp03 = $valor['total_planejado_passagem'];
		$tmp04 = $valor['total_executado_passagem'];
		$orcamentopordivisaopassagem .= '<tr '.$classe.'><td class="destaque">'.$valor['subdivisao'].'</td><td>R$ '.$valor['total_planejado_passagem'].'</td><td>R$ '.$valor['total_executado_passagem'].'</td><td '.($valor['saldo_passagem']<0?'class="atencao"':'').'> R$ '.$valor['saldo_passagem'].'</td><td '.($valor['percentagem_passagem']<0?'class="atencao"':'').'>'.$valor['percentagem_passagem'].'%</td></tr>';
		
		$c++;
	}
	$saldo = $totalplanejadas-$totalexecutadas;
	$perce = round($totalexecutadas*100/$totalplanejadas,2);
	
	$orcamentopordivisao .= '<tr '.$classe.'><td class="destaque">TOTAL GERAL</td><td '.($totalplanejadas<0?'class="atencao"':'').'>R$ '.$totalplanejadas.'</td><td  '.($totalexecutadas<0?'class="atencao"':'').'>R$ '.$totalexecutadas.'</td><td  '.($saldo<0?'class="atencao"':'').'>R$ '.$saldo.'</td><td '.($perce<0?'class="atencao"':'').'>'.$perce.'%</td></tr>';
	$saldo = $recursoalocado - $totalplanejadas;
	$perce = round($totalplanejadas*100/$recursoalocado,2);
	$orcamentopordivisao .= '<tr '.$classe.'><td class="destaque">RECURSOS</td><td>R$ '.$recursoalocado.'</td><td '.($totalplanejadas<0?'class="atencao"':'').'>R$ '.$totalplanejadas.'</td><td '.($saldo<0?'class="atencao"':'').'>R$ '.$saldo.'</td><td '.($perce<0?'class="atencao"':'').'>'.$perce.'%</td></tr>';
	
	
	$saldopassagem = $totalplanejadaspassagem-$totalexecutadaspassagem;
	$percepassagem = round($totalexecutadaspassagem*100/$totalplanejadaspassagem,2);
	
	$orcamentopordivisaopassagem .= '<tr '.$classe.'><td class="destaque">TOTAL GERAL</td><td '.($totalplanejadaspassagem<0?'class="atencao"':'').'>R$ '.$totalplanejadaspassagem.'</td><td  '.($totalexecutadaspassagem<0?'class="atencao"':'').'>R$ '.$totalexecutadaspassagem.'</td><td  '.($saldopassagem<0?'class="atencao"':'').'>R$ '.$saldopassagem.'</td><td '.($percepassagem<0?'class="atencao"':'').'>'.$percepassagem.'%</td></tr>';
	$saldopassagem = $recursoalocadopassagem - $totalplanejadaspassagem;
	$percepassagem = round($totalplanejadaspassagem*100/$recursoalocadopassagem,2);
	$orcamentopordivisaopassagem .= '<tr '.$classe.'><td class="destaque">RECURSOS</td><td>R$ '.$recursoalocadopassagem.'</td><td '.($totalplanejadaspassagem<0?'class="atencao"':'').'>R$ '.$totalplanejadaspassagem.'</td><td '.($saldopassagem<0?'class="atencao"':'').'>R$ '.$saldopassagem.'</td><td '.($percepassagem<0?'class="atencao"':'').'>'.$percepassagem.'%</td></tr>';
	
	
	
	
	
	
	
	
	
	
	
	
	
	$c =0;
	foreach($descricoes as $indice=>$valor){
		$descricao[$c]['subdivisao']=$valor['subdivisao'];
		$descricao[$c]['descricao']=$valor['descricao'];
		$c++;
	}
	
	$c =0;
	$relatoriopormes = '';
        $plntotal[1]=0;
        $nplntotal[1]=0;
	foreach($subdivisao as $indice=>$valor){
		$classe = ' class="linhaimpar" ';
		if($c%2){
			$classe = ' class="linhapar" ';
		}
		$relatoriopormes .= '<tr '.$classe.'><td rowspan="4" class="destaque">'.$valor['subdivisao'].'</td><td class="destaque">PLN</td>';
		for($i=1;$i<=12;$i++){
			$somames = 0;
			$planejados = $db->fetch_array("select round(sum(pln_diaria),2) as planejado from app_movimento where ano=2015 and subdivisao='{$valor['subdivisao']}' and month(inicio)=$i and status<>'CANCELADA' and descricao not like '%NÃO PLANEJADA%' group by subdivisao");
			$somames = empty($planejados[0]['planejado'])?0:$planejados[0]['planejado'];
			$relatoriopormes .= "<td>{$somames}</td>"; 
			$plnmes[$i]['pln']= $somames;
                        $plntotal[$i] += $somames; 
		}
	
		$relatoriopormes .= '</tr><tr '.$classe.'><td class="destaque">N.PLN</td>';
		for($i=1;$i<=12;$i++){
			$somames = 0;
			$planejados = $db->fetch_array("select round(sum(real_diaria),2) as planejado from app_movimento where ano=2015 and  subdivisao='{$valor['subdivisao']}' and month(inicio)=$i and status<>'CANCELADA' and descricao  like '%NÃO PLANEJADA%' group by subdivisao");
			$somames = empty($planejados[0]['planejado'])?0:$planejados[0]['planejado'];
			$relatoriopormes .= "<td>{$somames}</td>";
			$plnmes[$i]['npln']= $somames;
                        $nplntotal[$i] += $somames; 
		}
		
		$relatoriopormes .= '</tr><tr '.$classe.'><td class="destaque">EXEC</td>';
		for($i=1;$i<=12;$i++){
			$somames = 0;
                        /*
			$planejados = $db->fetch_array("select round(sum(diaria_qtd*valor+acrescimo),2) as planejado from app_movimento
					inner join orcamento_andamento on (descricao=planejado) 
					where  orcamento_andamento.ano=2015 and subdivisao='{$valor['subdivisao']}' and month(inicio)=$i and status<>'CANCELADA' and descricao not  like '%NÃO PLANEJADA%' group by subdivisao");
                         * 
                         */
			$planejados = $db->fetch_array("select round(sum(orcamento_andamento.valor+orcamento_andamento.acrescimo),2) as planejado from app_movimento
					inner join orcamento_andamento on (descricao=planejado) 
					where  orcamento_andamento.ano=2015 and subdivisao='{$valor['subdivisao']}' and month(inicio)=$i and status<>'CANCELADA' and descricao not  like '%NÃO PLANEJADA%' group by subdivisao");
			$somames = empty($planejados[0]['planejado'])?0:$planejados[0]['planejado'];
			$relatoriopormes .= "<td>{$somames}</td>";
			$plnmes[$i]['exec']= $somames + $plnmes[$i]['exec'];
		}
		
		$relatoriopormes .= '</tr><tr '.$classe.'><td class="destaque">SALDO</td>';
		for($i=1;$i<=12;$i++){
			$saldo = $plnmes[$i]['pln']+$plnmes[$i]['npln']-$plnmes[$i]['exec'];
			$relatoriopormes .= '<td '.($saldo<0?'class="atencao"':'').'>R$ '.$saldo.'</td>';
		}
		$relatoriopormes .= '</tr>';
		$c++;
	}
        $somatorioexecutado = 0;
        for($i=1;$i<=12;$i++){
            $somatorioexecutado += $plnmes[$i]['exec'];
            
        }
        $somatorioplanejado = 0;
        for($i=1;$i<=12;$i++){
            $somatorioplanejado += $plntotal[$i];
            
        }
        $somatorionaoplanejado = 0;
        for($i=1;$i<=12;$i++){
            $somatorionaoplanejado += $nplntotal[$i];
            
        }
		$relatoriopormes .= '<tr class="titulocoluna"><th class="titulogeral">TOTAL</th><th class="atencao">R$ '.$somatorioexecutado.'</th><th class="titulogeral">R$ '.$plnmes[1]['exec'].'</th><th class="titulogeral">R$ '.$plnmes[2]['exec'].'</th><th class="titulogeral">R$ '.$plnmes[3]['exec'].'</th><th class="titulogeral">R$ '.$plnmes[4]['exec'].'</th><th class="titulogeral">R$ '.$plnmes[5]['exec'].'</th><th class="titulogeral">R$ '.$plnmes[6]['exec'].'</th><th class="titulogeral">R$ '.$plnmes[7]['exec'].'</th><th class="titulogeral">R$ '.$plnmes[8]['exec'].'</th><th class="titulogeral">R$ '.$plnmes[9]['exec'].'</th><th class="titulogeral">R$ '.$plnmes[10]['exec'].'</th><th class="titulogeral">R$ '.$plnmes[11]['exec'].'</th><th class="titulogeral">R$ '.$plnmes[12]['exec'].'</th></tr>';
		
                $relatoriopormes .= '<tr class="titulocoluna"><th class="titulogeral">NPLN</th><th class="atencao">R$ '.$somatorionaoplanejado.'</th><th class="titulogeral">R$ '.$nplntotal[1].'</th><th class="titulogeral">R$ '.$nplntotal[2].'</th><th class="titulogeral">R$ '.$nplntotal[3].'</th><th class="titulogeral">R$ '.$nplntotal[4].'</th><th class="titulogeral">R$ '.$nplntotal[5].'</th><th class="titulogeral">R$ '.$nplntotal[6].'</th><th class="titulogeral">R$ '.$nplntotal[7].'</th><th class="titulogeral">R$ '.$nplntotal[8].'</th><th class="titulogeral">R$ '.$nplntotal[9].'</th><th class="titulogeral">R$ '.$nplntotal[10].'</th><th class="titulogeral">R$ '.$nplntotal[11].'</th><th class="titulogeral">R$ '.$nplntotal[12].'</th></tr>';
		
                $relatoriopormes .= '<tr class="titulocoluna"><th class="titulogeral">PLN</th><th class="atencao">R$ '.$somatorioplanejado.'</th><th class="titulogeral">R$ '.$plntotal[1].'</th><th class="titulogeral">R$ '.$plntotal[2].'</th><th class="titulogeral">R$ '.$plntotal[3].'</th><th class="titulogeral">R$ '.$plntotal[4].'</th><th class="titulogeral">R$ '.$plntotal[5].'</th><th class="titulogeral">R$ '.$plntotal[6].'</th><th class="titulogeral">R$ '.$plntotal[7].'</th><th class="titulogeral">R$ '.$plntotal[8].'</th><th class="titulogeral">R$ '.$plntotal[9].'</th><th class="titulogeral">R$ '.$plntotal[10].'</th><th class="titulogeral">R$ '.$plntotal[11].'</th><th class="titulogeral">R$ '.$plntotal[12].'</th></tr>';
	
                $parcelamensal = $recursoalocado/12; 
                $jan=$parcelamensal-$plnmes[1]['exec'];
                $fev=$parcelamensal-$plnmes[2]['exec']+$jan;
                $mar=$parcelamensal-$plnmes[3]['exec']+$fev;
                $abr=$parcelamensal-$plnmes[4]['exec']+$mar;
                $mai=$parcelamensal-$plnmes[5]['exec']+$abr;
                $jun=$parcelamensal-$plnmes[6]['exec']+$mai;
                $jul=$parcelamensal-$plnmes[7]['exec']+$jun;
                $ago=$parcelamensal-$plnmes[8]['exec']+$jul;
                $set=$parcelamensal-$plnmes[9]['exec']+$ago;
                $out=$parcelamensal-$plnmes[10]['exec']+$set;
                $nov=$parcelamensal-$plnmes[11]['exec']+$out;
                $dez=$parcelamensal-$plnmes[12]['exec']+$nov;
		$relatoriopormes .= '<tr class="titulocoluna"><th class="titulogeral">ORÇAMENTO</th><th class="atencao">R$ '.$recursoalocado.'</th><th class="titulogeral">R$ '.$jan.'</th><th class="titulogeral">R$ '.$fev.'</th><th class="titulogeral">R$ '.$mar.'</th><th class="titulogeral">R$ '.$abr.'</th><th class="titulogeral">R$ '.$mai.'</th><th class="titulogeral">R$ '.$jun.'</th><th class="titulogeral">R$ '.$jul.'</th><th class="titulogeral">R$ '.$ago.'</th><th class="titulogeral">R$ '.$set.'</th><th class="titulogeral">R$ '.$out.'</th><th class="titulogeral">R$ '.$nov.'</th><th class="titulogeral">R$ '.$dez.'</th></tr>';
	
	
	

	
	
	
	
	
	
	
	
	
	
 	
	$c =0;
	$relatoriopormespassagem = '';
	foreach($subdivisao as $indice=>$valor){
		$classe = ' class="linhaimpar" ';
		if($c%2){
			$classe = ' class="linhapar" ';
		}
		$relatoriopormespassagem .= '<tr '.$classe.'><td rowspan="4" class="destaque">'.$valor['subdivisao'].'</td><td class="destaque">PLN</td>';
		for($i=1;$i<=12;$i++){
			$somamespassagem = 0;
			$planejadospassagem = $db->fetch_array("select round(sum(pln_passagem),2) as planejado from app_movimento where  ano=2015 and  subdivisao='{$valor['subdivisao']}' and month(inicio)=$i and status<>'CANCELADA' and descricao not like '%NÃO PLANEJADA%' group by subdivisao");
			$somamespassagem = empty($planejadospassagem[0]['planejado'])?0:$planejadospassagem[0]['planejado'];
			$relatoriopormespassagem .= "<td>{$somamespassagem}</td>";
			$plnmespassagem[$i]['plnpassagem']= $somamespassagem;
		}
	
		$relatoriopormespassagem .= '</tr><tr '.$classe.'><td class="destaque">N.PLN</td>';
			for($i=1;$i<=12;$i++){
				$somamespassagem = 0;
				$planejadospassagem = $db->fetch_array("select round(sum(real_passagem),2) as planejado from app_movimento where  ano=2015 and  subdivisao='{$valor['subdivisao']}' and month(inicio)=$i and status<>'CANCELADA' and descricao  like '%NÃO PLANEJADA%' group by subdivisao");
				$somamespassagem = empty($planejadospassagem[0]['planejado'])?0:$planejadospassagem[0]['planejado'];
				$relatoriopormespassagem .= "<td>{$somamespassagem}</td>";
				$plnmespassagem[$i]['nplnpassagem']= $somamespassagem;
			}
	
		$relatoriopormespassagem .= '</tr><tr '.$classe.'><td class="destaque">EXEC</td>';
			for($i=1;$i<=12;$i++){
				$somamespassagem = 0;
				$planejadospassagem = $db->fetch_array("select round(passagem,2) as planejado from app_movimento
						inner join orcamento_andamento on (descricao=planejado)
						where orcamento_andamento.ano=2015 and subdivisao='{$valor['subdivisao']}' and month(inicio)=$i and status<>'CANCELADA' and descricao not  like '%NÃO PLANEJADA%' group by subdivisao");
				$somamespassagem = empty($planejadospassagem[0]['planejado'])?0:$planejadospassagem[0]['planejado'];
				$relatoriopormespassagem .= "<td>{$somamespassagem}</td>";
				$plnmespassagem[$i]['execpassagem']= $somamespassagem;
			}
	
		$relatoriopormespassagem .= '</tr><tr '.$classe.'><td class="destaque">SALDO</td>';
		for($i=1;$i<=12;$i++){
			$saldopassagem = $plnmespassagem[$i]['plnpassagem']+$plnmespassagem[$i]['nplnpassagem']-$plnmespassagem[$i]['execpassagem'];
			$relatoriopormespassagem .= '<td '.($saldopassagem<0?'class="atencao"':'').'>R$ '.$saldopassagem.'</td>';
		}
	$relatoriopormespassagem .= '</tr>';
	$c++;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	$mesesdoano = array(1=>'JANEIRO',2=>'FEVEREIRO',3=>'MARÇO',4=>'ABRIL',5=>'MAIO',6=>'JUNHO',7=>'JULHO',8=>'AGOSTO',9=>'SETEMBRO',10=>'OUTUBRO',11=>'NOVEMBRO',12=>'DEZEMBRO');
	$subdivisoes = $db->fetch_array("select subdivisao from app_movimento group by data order by data asc ");
	
	$relatoriosubdivisaomes = '';
	
	foreach($subdivisoes as $indice=>$valor){
		
	$relatoriosubdivisaomes .= '
	  <tr>
	    <th colspan="10" class="titulogeral">RELATÓRIO GERAL DAS MISSÕES DA SUBDIVISÃO '.$valor['subdivisao'].'</th>
	  </tr>
	  <tr class="titulocoluna">
	    <td colspan="10">MISSÕES PLANEJADAS</td>
	  </tr>
	  <tr class="destaque">
	    <td class="destaque">MÊS</td>
	    <td class="destaque">PLANEJADO</td>
	    <td class="destaque">TIPO</td>
	    <td class="destaque">OS</td>
	    <td class="destaque">RESUMO SERVIÇO</td>
	    <td class="destaque">NOME</td>
	    <td class="destaque">SAÍDA</td>
	    <td class="destaque">REGRESSO</td>
	    <td class="destaque">DIÁRIA+AC</td>
	    <td class="destaque">PASSAGEM</td>
	  </tr>';
	
	$somageral_diaria_divisao = 0;
	$somageral_passagem_divisao = 0;
	
	for($i=1;$i<=12;$i++){
		$somames_diaria_divisao = 0;
		$somames_passagem_divisao = 0;
		$somames = 0;
		$total = $db->fetch_array("select count(*) as total from app_movimento
		inner join orcamento_andamento on (descricao=planejado) where orcamento_andamento.ano=2015 and  subdivisao='{$valor['subdivisao']}' and month(inicio)=$i and status<>'CANCELADA' and descricao not  like '%NÃO PLANEJADA%' group by subdivisao ");
		$qtdregistros = empty($total[0]['total'])?0:$total[0]['total'];
		
		
		$planejados = $db->fetch_array("select *, round((valor+acrescimo),2) as diaria from app_movimento
		inner join orcamento_andamento on (descricao=planejado)	where orcamento_andamento.ano=2015 and  subdivisao='{$valor['subdivisao']}' and month(inicio)=$i and status<>'CANCELADA' and descricao not  like '%NÃO PLANEJADA%' order by planejado asc, saida_data asc ");
		$somames = empty($planejados[0]['planejado'])?0:$planejados[0]['planejado'];
		
		$contagem = 0;
		foreach($planejados as $idx=>$dado){
			$classe = ' class="linhaimpar" ';
			if($contagem%2){
				$classe = ' class="linhapar" ';
			}
			if($contagem==0){
				$relatoriosubdivisaomes .= '  <tr '.$classe.'>
			    <td rowspan="'.$qtdregistros.'" class="destaque">'.$mesesdoano[$i].'</td>
			    <td class="destaque">'.$dado['planejado'].'</td>
			    <td class="destaque">'.$dado['tipo'].'</td>
				<td>'.$dado['os'].'</td>
			    <td>'.$dado['resumo_servico'].'</td>
			    <td>'.$dado['nome_militar'].'</td>
			    <td>'.$dado['saida_data'].'</td>
			    <td>'.$dado['regresso_data'].'</td>
			    <td>'.$dado['diaria'].'</td>
			    <td>'.$dado['passagem'].'</td>
			    </tr>	';
	
			}else{
				$relatoriosubdivisaomes .= '  <tr  '.$classe.'>
				<td class="destaque">'.$dado['planejado'].'</td>
			    <td class="destaque">'.$dado['tipo'].'</td>
				<td>'.$dado['os'].'</td>
			    <td>'.$dado['resumo_servico'].'</td>
			    <td>'.$dado['nome_militar'].'</td>
			    <td>'.$dado['saida_data'].'</td>
			    <td>'.$dado['regresso_data'].'</td>
			    <td>'.$dado['diaria'].'</td>
			    <td>'.$dado['passagem'].'</td>
			    </tr>	';
							}
				$somageral_diaria_divisao += $dado['diaria'];
				$somageral_passagem_divisao += $dado['passagem'];
				$somames_diaria_divisao += $dado['diaria'];
				$somames_passagem_divisao += $dado['passagem'];
				$contagem++;
		}
		if($qtdregistros>0){
			$relatoriosubdivisaomes .= '  <tr>
			<td>&nbsp;</td>
		    <td class="destaque">TOTAL EXECUTADO </td>
		    <td>&nbsp;</td>
		    <td>&nbsp;</td>
			<td>&nbsp;</td>
		    <td>&nbsp;</td>
		    <td>&nbsp;</td>
		    <td>&nbsp;</td>
		    <td class="atencao">'.$somames_diaria_divisao.'</td>
		    <td class="atencao">'.$somames_passagem_divisao.'</td>
		    </tr>';
		}
		
		
		
		
		
	}
	$relatoriosubdivisaomes .= '	<tr>
	<td>&nbsp;</td>
	<td class="destaque">TOTAL GERAL</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
	<td>&nbsp;</td>
    <td class="atencao">'.$somageral_diaria_divisao.'</td>
    <td class="atencao">'.$somageral_passagem_divisao.'</td>
	</tr>
    <tr style="background-color:#000;"><td colspan="10">&nbsp;</td></tr>
	';
	
	
	
	}
		//echo $relatoriosubdivisaomes;	exit();

	//print_r($subdivisao);
	include("view_movimento_pdf_relatorio.php");
	//include 'view_geral_ajax.php';
	//include("view_pagina_rodape.php");
}


?>		
