<?php 
include 'parametros.php';
include 'db/Database.singleton.php';

$ip = $_SERVER['REMOTE_ADDR'];
$host = $_SERVER['HTTP_HOST'];
$maximoregistros = 30;
$controllernome = 'andamento';
$controllernomeplural = 'andamentos';
$appID = '';
$status = '';

//print_r($_GET);
$filtros = array('dt_maiorque'=>'Maior que','dt_menorque'=>'Menor que','dt_igual'=>'Igual a','dt_diferente'=>'Diferente de','txt_contem'=>'Contém','txt_naocontem'=>'Não contém','txt_igual'=>'Igual a','txt_diferente'=>'Diferente de','num_maiorque'=>'Maior que','num_menorque'=>'Menor que','num_igual'=>'Igual a','num_diferente'=>'Diferente de');
$filtrostipo = array('dt_maiorque'=>'Data','dt_menorque'=>'Data','dt_igual'=>'Data','dt_diferente'=>'Data','txt_contem'=>'Texto','txt_naocontem'=>'Texto','txt_igual'=>'Texto','txt_diferente'=>'Texto','num_maiorque'=>'Numero','num_menorque'=>'Numero','num_igual'=>'Numero','num_diferente'=>'Numero');
$campos = array('Ano'=>'ano','Planejado'=>'planejado','Meta'=>'meta','Subetapa'=>'subetapa','Resumo Serviço'=>'resumo_servico','OS'=>'os','Nome Militar'=>'nome_militar','Nome Guerra'=>'nome_guerra','Cidade'=>'cid','Data Saída'=>'saida_data', 'Data Regresso'=>'regresso_data', 'Qtd Diária'=>'diaria_qtd', 'Valor'=>'valor', 'Acréscimo'=>'acrescimo', 'Passagem'=>'passagem','O.S.'=>'os','Ent.OPG'=>'dt_entrada_opg', 'Ida OSEC(PAG)'=>'dt_ida_osec_pag', 'Ida D.O.'=>'dt_ida_chf_divisao', 'Ret. D.O.'=>'dt_retorno_chf_divisao', 'SIGADAER CSEC'=>'dt_entrada_sad_fax', 'Ida Cópia AFIN'=>'dt_ida_copias_financas', 'Apresentação'=>'dt_apresentacao_bilhete', 'Ida Proc. ACI'=>'dt_ida_processo_controle_interno', 'Ret. Proc. ACI'=>'dt_retorno_processo_controle_interno', 'Ida Proc. O.D.'=>'dt_ida_processo_ordenador', 'Ret. Proc. O.D.'=>'dt_retorno_processo_ordenador', 'Arq. OSEC'=>'dt_arquivamento_osec');
$tipos = array('ano'=>'num','planejado'=>'txt','meta'=>'txt','subetapa'=>'txt','resumo_servico'=>'txt','os'=>'txt','nome_militar'=>'txt','nome_guerra'=>'txt','cid'=>'txt','saida_data'=>'dt', 'regresso_data'=>'dt',  'diaria_qtd'=>'num', 'valor'=>'num', 'acrescimo'=>'num', 'passagem'=>'num','os'=>'txt', 'dt_entrada_opg'=>'dttime', 'dt_ida_osec_pag'=>'dttime', 'dt_ida_chf_divisao'=>'dttime', 'dt_retorno_chf_divisao'=>'dttime', 'dt_entrada_sad_fax'=>'dttime', 'dt_ida_copias_financas'=>'dttime', 'dt_apresentacao_bilhete'=>'dttime', 'dt_ida_processo_controle_interno'=>'dttime', 'dt_retorno_processo_controle_interno'=>'dttime', 'dt_ida_processo_ordenador'=>'dttime', 'dt_retorno_processo_ordenador'=>'dttime', 'dt_arquivamento_osec'=>'dttime');
$funcoes = array('planejado'=>'editsuspenso','meta'=>'vazio','subetapa'=>'vazio','resumo_servico'=>'vazio','os'=>'vazio','nome_militar'=>'vazio','nome_guerra'=>'vazio','cid'=>'vazio','saida_data'=>'editsuspenso', 'regresso_data'=>'editsuspenso',  'diaria_qtd'=>'vazio', 'valor'=>'vazio', 'acrescimo'=>'vazio', 'passagem'=>'vazio', 'os'=>'vazio','dt_entrada_opg'=>'editsuspenso', 'dt_ida_osec_pag'=>'editsuspenso', 'dt_ida_chf_divisao'=>'editsuspenso', 'dt_retorno_chf_divisao'=>'editsuspenso', 'dt_entrada_sad_fax'=>'editsuspenso', 'dt_ida_copias_financas'=>'editsuspenso', 'dt_apresentacao_bilhete'=>'editsuspenso', 'dt_ida_processo_controle_interno'=>'editsuspenso', 'dt_retorno_processo_controle_interno'=>'editsuspenso', 'dt_ida_processo_ordenador'=>'editsuspenso', 'dt_retorno_processo_ordenador'=>'editsuspenso', 'dt_arquivamento_osec'=>'editsuspenso');
$leitura = array('ano'=>1,'planejado'=>0,'meta'=>1,'subetapa'=>1,'resumo_servico'=>1,'os'=>1,'nome_militar'=>1,'nome_guerra'=>1,'cid'=>1,'saida_data'=>0, 'regresso_data'=>0, 'diaria_qtd'=>1, 'valor'=>1, 'acrescimo'=>1, 'passagem'=>1, 'os'=>1,'dt_entrada_opg'=>0, 'dt_ida_osec_pag'=>0, 'dt_ida_chf_divisao'=>0, 'dt_retorno_chf_divisao'=>0, 'dt_entrada_sad_fax'=>0, 'dt_ida_copias_financas'=>0, 'dt_apresentacao_bilhete'=>0, 'dt_ida_processo_controle_interno'=>0, 'dt_retorno_processo_controle_interno'=>0, 'dt_ida_processo_ordenador'=>0, 'dt_retorno_processo_ordenador'=>0, 'dt_arquivamento_osec'=>0);

$tamanhocampo = array('Ano'=>'4','Planejado'=>'15','Meta'=>'15','Subetapa'=>'23','Resumo Serviço'=>'30','OS'=>'10','Nome Militar'=>'10','Nome Guerra'=>'10','Cidade'=>'10','Data Saída'=>'8', 'Data Regresso'=>'8', 'Qtd Diária'=>'6', 'Valor'=>'6', 'Acréscimo'=>'3', 'Passagem'=>'6','OS'=>'10','Ent.OPG'=>'18', 'Ida OSEC(PAG)'=>'18', 'Ida D.O.'=>'18', 'Ret. D.O.'=>'18', 'SIGADAER CSEC'=>'18', 'Ida Cópia AFIN'=>'18', 'Apresentação'=>'18', 'Ida Proc. ACI'=>'18', 'Ret. Proc. ACI'=>'18', 'Ida Proc. O.D.'=>'18', 'Ret. Proc. O.D.'=>'18', 'Arq. OSEC'=>'18');
$campoautocompletar = array('Ano'=>1,'Planejado'=>1,'Meta'=>0,'Subetapa'=>0,'Resumo Serviço'=>0,'OS'=>0,'Nome Militar'=>0,'Nome Guerra'=>0,'Cidade'=>0,'Data Saída'=>0, 'Data Regresso'=>0,  'Qtd Diária'=>0, 'Valor'=>0, 'Acréscimo'=>0, 'Passagem'=>0, 'os'=>1,'dt_entrada_opg'=>0, 'dt_ida_osec_pag'=>0, 'dt_ida_chf_divisao'=>0, 'dt_retorno_chf_divisao'=>0, 'dt_entrada_sad_fax'=>0, 'dt_ida_copias_financas'=>0, 'dt_apresentacao_bilhete'=>0, 'dt_ida_processo_controle_interno'=>0, 'dt_retorno_processo_controle_interno'=>0, 'dt_ida_processo_ordenador'=>0, 'dt_retorno_processo_ordenador'=>0, 'dt_arquivamento_osec'=>0);
$campoautosomar = array('Ano'=>0,'Planejado'=>0,'Meta'=>0,'Subetapa'=>0,'Resumo Serviço'=>0,'OS'=>0,'Nome Militar'=>0,'Nome Guerra'=>0,'Cidade'=>0,'Data Saída'=>0, 'Data Regresso'=>0, 'Qtd Diária'=>1, 'Valor'=>1, 'Acréscimo'=>1, 'Passagem'=>1, 'os'=>1,'dt_entrada_opg'=>0, 'dt_ida_osec_pag'=>0, 'dt_ida_chf_divisao'=>0, 'dt_retorno_chf_divisao'=>0, 'dt_entrada_sad_fax'=>0, 'dt_ida_copias_financas'=>0, 'dt_apresentacao_bilhete'=>0, 'dt_ida_processo_controle_interno'=>0, 'dt_retorno_processo_controle_interno'=>0, 'dt_ida_processo_ordenador'=>0, 'dt_retorno_processo_ordenador'=>0, 'dt_arquivamento_osec'=>0);
$campoautosomarvalor = array('Ano'=>0,'Planejado'=>0,'Meta'=>0,'Subetapa'=>0,'Resumo Serviço'=>0,'OS'=>0,'Nome Militar'=>0,'Nome Guerra'=>0,'Cidade'=>0,'Data Saída'=>0, 'Data Regresso'=>0,  'Qtd Diária'=>0, 'Valor'=>0, 'Acréscimo'=>0, 'Passagem'=>0, 'os'=>1,'dt_entrada_opg'=>0, 'dt_ida_osec_pag'=>0, 'dt_ida_chf_divisao'=>0, 'dt_retorno_chf_divisao'=>0, 'dt_entrada_sad_fax'=>0, 'dt_ida_copias_financas'=>0, 'dt_apresentacao_bilhete'=>0, 'dt_ida_processo_controle_interno'=>0, 'dt_retorno_processo_controle_interno'=>0, 'dt_ida_processo_ordenador'=>0, 'dt_retorno_processo_ordenador'=>0, 'dt_arquivamento_osec'=>0);
$mensagemstatus = '';
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

$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE, DB_DEBUG);
$db->connect();

//$caracter = $db->fetch_array("SELECT USER(), CHARSET(USER()), COLLATION(USER());");

$filtro = '';
$formbusca = '';
$order = ' order by saida_data ASC ';


if(!empty($_GET['registrosporpagina'])&& is_numeric($_GET['registrosporpagina'])){
	$maximoregistros = $_GET['registrosporpagina'];
	$db->query('update orcamento_filtro set qtdregistros='.$maximoregistros.' where usuario="'.$usuario.'" and modelo="'.$controllernome.'" ');
}


$sqlfiltro = $db->fetch_array("select * from orcamento_filtro where usuario='$usuario' and modelo='$controllernome' ");
$db->query("SET NAMES 'utf8'");
$db->query("SET character_set_connection=utf8");
$db->query("SET character_set_client=utf8");
$db->query("SET character_set_results=utf8");
$db->query("SET NAMES 'utf8' COLLATE 'utf8_general_ci'");
$db->query("SET CHARACTER SET utf8");



if($sqlfiltro[0]['qtdregistros']>0){
	$maximoregistros = $sqlfiltro[0]['qtdregistros']; 	
}

    $vetorclassificacao = unserialize(iconv('latin1', 'utf8', $sqlfiltro[0]['classificacao']));
	if(!empty($vetorclassificacao)){
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
		$db->query('update orcamento_filtro set classificacao=\''.$ordenacaoserial.'\' where usuario="'.$usuario.'" and modelo="'.$controllernome.'" ');
	}
	if(count($ordenacao)>0){
		$order = ' order by '.implode(", ", array_mapk(function ($k, $v) { return "{$k}  " . mysql_real_escape_string($v); }, $ordenacao));
	}
	
//echo $order;



if(empty($sqlfiltro[0]['usuario'])&&empty($sqlfiltro[0]['modelo'])){
		$insere = $db->query("insert into orcamento_filtro (usuario, modelo, qtdregistros, criado)	values('$usuario','{$controllernome}', $maximoregistros, now() ) ");
		$filtro = '';
		$formbusca = '';
}

if(strlen($sqlfiltro[0]['sqlprocessado'])>2){
	$filtro = iconv('latin1', 'utf8', ' where  '.$sqlfiltro[0]['sqlprocessado']);
	$formbusca = unserialize(iconv('latin1', 'utf8', $sqlfiltro[0]['dadospostados']));
}



//echo $filtro;
if ( ($_GET['i']=='andamento') && ($_GET['acao']=='list') ){
	$status = 'OK';
	$mensagemstatus = 'Registros ';
	include("view_pagina_cabecalho.php");
	include("view_pagina_menu.php");
	$comeco = ($pagina-1) * $maximoregistros;
	$somatorio = $db->fetch_array("select count(*) as total from orcamento_andamento $filtro ");
	$conteudoconsultarecente = $db->fetch_array("select * from orcamento_andamento where date(created)=date(now()) ");
	
	$total = $somatorio[0]['total'];
	
	if(!empty($filtro)){
		$conteudoconsultaregistros = $db->fetch_array("select * from orcamento_andamento $filtro $order limit $comeco,$maximoregistros ");
		$conteudoconsulta = $conteudoconsultaregistros;
	}else{
		$conteudoconsultaregistros = $db->fetch_array("select * from orcamento_andamento $filtro $order limit $comeco,$maximoregistros ");
		$conteudoconsulta = $conteudoconsultarecente + $conteudoconsultaregistros;
	}
	
	if(!empty($status)){
		$status = '';
	}
	$paginas = ceil($total/$maximoregistros);
	if ($total != $paginas*maximoregistros){
		//$paginas++;
	}
	include 'view_geral_mensagens.php';	
	include("view_andamento_list.php");
	include 'view_geral_ajax.php';
	//include("view_pagina_conteudo.php");
	include("view_pagina_rodape.php");
}



if ( ($_POST['controller']=='andamento') && ($_POST['acao']=='ajaxmodifica') ){
	header('Content-type: application/x-json');
	$dados[$_POST['campo']]=$_POST['valor'];
	if(($tipos[$_POST['campo']] == 'dt')){
		if(strlen($_POST['campo'])>6){
		$exp = explode('/', $_POST['valor']);
		$dados[$_POST['campo']]=date('Y-m-d',mktime(0,0,0,$exp[1],$exp[0],$exp[2]));
		}else{
			$dados[$_POST['campo']]='';
		}
	}
//        echo $tipos['dt_entrada_opg'];
        
        if($tipos[$_POST['campo']]== 'dttime'){
                        if(strlen($_POST['campo'])>8){
                                $exp = explode(' ', $_POST['valor']);
                                $dadosdata = explode('/', $exp[0]);
                                $dadostime = explode(':', $exp[1]);
                                $dados[$_POST['campo']]=date('Y-m-d H:I',mktime($dadostime[0],$dadostime[1],0,$dadosdata[1],$dadosdata[0],$dadosdata[2]));
                        }else{
                                $dados[$_POST['campo']]='';
                        }
        }
        
	$dados['updated'] = 'now()';
	$dados['usuariomodificou'] = $usuario;
	$dados['ipmodificou'] = $ip;
	$dados['hostmodificou'] = $host;
	
        //print_r($dados);
        
	$where= ' id="'.$_POST['id'].'"';
	if($db->update('orcamento_andamento', $dados, $where)){
		echo '{"status":"OK", "mensagemstatus":"Registro modificado com sucesso!"}';
	}else{
		echo '{"status":"ERRO", "mensagemstatus":"Registro não foi modificado!"}';
	}
	
	if($_POST['campo']=='planejado'){
		$calculos = $db->fetch_array("select sum(valor+acrescimo) as diariareal, sum(passagem) as passagemreal from orcamento_andamento where planejado='{$dados[$_POST['campo']]}' group by planejado");
		$db->query("update orcamento_planejado set real_diaria='{$calculos[0]['diariareal']}', real_passagem='{$calculos[0]['passagemreal']}' where descricao='{$dados[$_POST['campo']]}' ");
	}
}


if ( ($_POST['controller']=='andamento') && ($_POST['acao']=='vercad') ){
	//header('Content-type: application/x-json');
	$autocomplete_planejado = $db->fetch_array("select descricao from orcamento_planejado group by descricao order by descricao asc ");
	$completaplanejado = '';
	foreach($autocomplete_planejado as $indica=>$valor){
		//$completasubdivisao .= "'".iconv('latin1', 'utf8', $valor['subdivisao'])."',"; 
		$completaplanejado .= "'".$valor['descricao']."',"; 
	}
	$completaplanejado .= "''";
	

	
	
	include("view_andamento_cad.php");
	//exit();
}


if ( ($_POST['controller']=='andamento') && ($_POST['acao']=='cad') ){
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
		if($tipos[$campo]== 'dttime'){
				if(strlen($_POST[$campo])>=10){
					$exp = explode(' ', $_POST[$campo]);
					$dadosdata = explode('/', $exp[0]);
					$dadostime = explode(':', $exp[0]);
					$_POST[$campo]=date('Y-m-d h:i',mktime($dadostime[0],$dadostime[1],0,$dadosdata[1],$dadosdata[0],$dadosdata[2]));
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
	//$db->insert('orcamento_andamento', $dados);
	
	if($db->insert('orcamento_andamento', $dados)){
		$status = 'OK';
		$mensagemstatus = "Registro incluído com sucesso!";
		ob_start();
			$somatorio = $db->fetch_array("select count(*) as total from orcamento_andamento $filtro ");
			$total = $somatorio[0]['total'];
			$conteudoconsultarecente = $db->fetch_array("select * from orcamento_andamento where date(created)=date(now())   ");
			$conteudoconsultaregistros = $db->fetch_array("select * from orcamento_andamento $filtro  $order   limit $comeco,$maximoregistros ");
			$conteudoconsulta = $conteudoconsultarecente + $conteudoconsultaregistros;
		
			$paginas = ceil($total/$maximoregistros);
			if ($total != $paginas*maximoregistros){
			}
			if(empty($_GET['i'])){
				$_GET['i'] = 'andamento';
			}
			if(empty($_GET['acao'])){
				$_GET['acao'] = 'list';
			}
			
			include 'view_geral_mensagens.php';
			include("view_andamento_list.php");
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
	//include("view_andamento_list.php");
	//exit();
}


if ( ($_POST['controller']=='andamento') && ($_POST['acao']=='busca') ){
	$status = 'OK';
	$mensagemstatus = 'Registros ';
	$comeco = ($pagina-1) * $maximoregistros;
	$comeco = 0;
	$somatorio = $db->fetch_array("select count(*) as total from orcamento_andamento $filtro ");
	$total = $somatorio[0]['total'];
	$maximoregistros = $total;
	$conteudoconsultarecente = $db->fetch_array("select * from orcamento_andamento where date(created)=date(now())  ");
	$conteudoconsultaregistros = $db->fetch_array("select * from orcamento_andamento  $filtro  $order  limit $comeco,$maximoregistros ");
	$conteudoconsulta = $conteudoconsultarecente + $conteudoconsultaregistros;
	if(!empty($status)){
		$status = '';
	}
	$paginas = ceil($total/$maximoregistros);
	if ($total != $paginas*maximoregistros){
	}
	if(empty($_GET['i'])){
		$_GET['i'] = 'andamento';
	}
	if(empty($_GET['acao'])){
		$_GET['acao'] = 'list';
	}
	include 'view_geral_mensagens.php';	
	include("view_andamento_list.php");
	include 'view_geral_ajax.php';
	//include("view_pagina_rodape.php");
}

if ( ($_POST['controller']=='andamento') && ($_POST['acao']=='exclui') ){
	$status = '';
	$mensagemstatus = '';
	$comeco = ($_POST['pagina']-1) * $maximoregistros;
	if($db->query("delete from orcamento_andamento where id=replace('{$_POST['id']}','#del','') ")){
		$status = 'OK';
		$mensagemstatus = 'Registro excluído com sucesso!';
	}else{
		$status = 'ERRO';
		$mensagemstatus = 'Houve problema para excluir o registro!';
	}
	
	/*
	$somatorio = $db->fetch_array("select count(*) as total from orcamento_andamento $filtro ");
	$total = $somatorio[0]['total'];
	
	$conteudoconsultarecente = $db->fetch_array("select * from orcamento_andamento where date(created)=date(now()) ");
	$conteudoconsultaregistros = $db->fetch_array("select * from orcamento_andamento  $filtro  $order limit $comeco,$maximoregistros ");
	$conteudoconsulta = $conteudoconsultarecente + $conteudoconsultaregistros;

	$paginas = ceil($total/$maximoregistros);
	$pagina = $_POST['pagina'];
	if ($total != $paginas*maximoregistros){
	}
	if(empty($_GET['i'])){
		$_GET['i'] = 'andamento';
	}
	if(empty($_GET['acao'])){
		$_GET['acao'] = 'list';
	}
	
	include 'view_geral_mensagens.php';
	include("view_andamento_list.php");
	*/
	header('Content-type: application/x-json');
	
	echo '{"status":"'.$status.'", "mensagemstatus":"'.$mensagemstatus.'", "conteudo":"'.($conteudo).'"}';
	
}
if ( ($_GET['controller']=='andamento') && ($_GET['acao']=='xls') ){
	$status = 'OK';
	$mensagemstatus = 'Registros ';
	$comeco = ($pagina-1) * $maximoregistros;
	$comeco = 0;

	$l = 0;

	$somatorio = $db->fetch_array("select count(*) as total from orcamento_andamento $filtro ");
	$total = $somatorio[0]['total'];
	$maximoregistros = $total;

	if(!empty($filtro)){
		$conteudoconsultaregistros = $db->fetch_array("select * from orcamento_andamento $filtro $order  ");
		$conteudoconsultarecente = $conteudoconsultaregistros;
	}else{
		$conteudoconsultaregistros = $db->fetch_array("select * from orcamento_andamento $filtro $order  ");
		$conteudoconsultarecente = $conteudoconsultaregistros;
	}


	foreach($conteudoconsultarecente as $indice=>$valor){
		$c =0;
		foreach($campos as $titulo=>$nomecampo){
			$registros[$l][$c]=$valor[$nomecampo];
			$c++;
		}
		$l++;
	}
	$conteudoconsulta = $conteudoconsultarecente ;
	include("view_andamento_excel.php");

}
if ( ($_GET['controller']=='andamento') && ($_GET['acao']=='pdf') ){
	$status = 'OK';
	$mensagemstatus = 'Registros ';
	$comeco = ($pagina-1) * $maximoregistros;
	$comeco = 0;
	$somatorio = $db->fetch_array("select count(*) as total from orcamento_andamento ");
	$total = $somatorio[0]['total'];
	$maximoregistros = $total;
	$conteudoconsultarecente = $db->fetch_array("select * from orcamento_andamento $order");
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
	include("view_andamento_pdf.php");
	//include 'view_geral_ajax.php';
	//include("view_pagina_rodape.php");
}

if ( ($_POST['controller']=='andamento') && ($_POST['acao']=='verfiltro') ){
	//header('Content-type: application/x-json');

	include("view_andamento_filtro.php");
	//exit();
}

if ( ($_POST['controller']=='andamento') && ($_POST['acao']=='filtro') ){
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
		if(!strlen($nomecampo)<1&&!strlen($nomecriterio)<1&&!strlen($nomevalor)<1){
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
	
	$insere = $db->query("insert into orcamento_filtro (usuario, modelo, sqlprocessado, dadospostados, criado) 
			values('$usuario','{$_POST['controller']}', '$sql', '$post', now() ) 
			on duplicate key update sqlprocessado='$sql', dadospostados='$post', atualizado=now() ");
	if($insere){
		if(strlen($sql)>3){
			$filtro = ' where '.$sql;
		}else{
			$filtro = '';
		}
		ob_start();
		$somatorio = $db->fetch_array("select count(*) as total from orcamento_andamento $filtro ");
		$total = $somatorio[0]['total'];
	
	if(!empty($filtro)){
		$conteudoconsultaregistros = $db->fetch_array("select * from orcamento_andamento $filtro $order limit $comeco,$maximoregistros ");
		$conteudoconsulta = $conteudoconsultaregistros;
	}else{
		$conteudoconsultaregistros = $db->fetch_array("select * from orcamento_andamento $filtro $order limit $comeco,$maximoregistros ");
		$conteudoconsulta = $conteudoconsultaregistros;
	}
			
		$paginas = ceil($total/$maximoregistros);
		if ($total != $paginas*maximoregistros){
		}
		if(empty($_GET['i'])){
			$_GET['i'] = 'andamento';
		}
		if(empty($_GET['acao'])){
			$_GET['acao'] = 'list';
		}
			
		include 'view_geral_mensagens.php';
		include("view_andamento_list.php");
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


?>		
