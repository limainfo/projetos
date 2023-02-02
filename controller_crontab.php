<?php

//echo $usuario; print_r($_);exit();
error_reporting (E_ALL);
include 'parametrosonix.php';
include 'parametros.php';
include 'db/Database.singleton.php';
include 'db/Database.singleton02.php';

echo 'Acessei o arquivo';exit();

$usuario = 'evaldoesl';
$ip = $_SERVER['REMOTE_ADDR'];
$host = $_SERVER['HTTP_HOST'];


$dbonix = Database::obtain(DB_ONIX_SERVER, DB_ONIX_USER, DB_ONIX_PASS, DB_ONIX_DATABASE);
$dbonix->connect();
$dbonix->query("SET NAMES 'utf8'");
$dbonix->query("SET character_set_connection=utf8");
$dbonix->query("SET character_set_client=utf8");
$dbonix->query("SET character_set_results=utf8");
$dbonix->query("SET NAMES 'utf8' COLLATE 'utf8_general_ci'");
$dbonix->query("SET CHARACTER SET utf8");




$anoescolhido = $_POST['ano'];
//$anoescolhido = 2022;
switch($anoescolhido){
case '2021':
	$exercicio = 213;
	$meta = 2448;
	$etapa = '(7318,7332)';
	$nd = 5;
	$osdebitada=62;
	$data_debito='2021-01-01';
	break;
case '2022':
	$exercicio = 214;
	$meta = 2461;
	$etapa = '(7346,7348,7349)';
	$etapa = '(7346)';
	$nd = 5;
	$osdebitada=62;
	$data_debito='2022-01-01';
	break;
case '2023':
	$exercicio = 215;
	$meta = 2462;
	$etapa = '(7346,7349,7348)';
	$nd = 5;
	$osdebitada=62;
	$data_debito='2023-01-01';
	break;
	
}

$dtfim = $anoescolhido.'-12-31';
$sql = "
select pernoite.id_pernoite, os.id_os,meta.display as meta,
subetapa.display as subetapa, os.resumo_servico, os.os, concat_ws(' ', posto,especialidade, nome_completo) as nome_militar,  
group_concat(pernoite.trecho,'| ') as cid, pernoite.saida_data, pernoite.regresso_data, 
evento.display as evento, diaria_qtd, ((diaria_qtd * diaria_estimada)) as valor, acrescimo_valor as acrescimo, pernoite.passagem_valor as passagem, fase.display as fase
from pernoite
inner join os on (os.id_os=pernoite.id_os and os.id_fase not in (29,31)  )
inner join servidor on (servidor.id_servidor=pernoite.id_servidor)
left join posto on (posto.id_posto=servidor.id_posto)
left join especialidade on (especialidade.id_especialidade=servidor.id_especialidade)
inner join cidade on (cidade.id_cidade=os.id_cidade)
left join identificador on (identificador.id_identificador=os.id_identificador)
left join exercicio on (exercicio.id_exercicio=identificador.id_exercicio and exercicio.id_exercicio=$exercicio)
inner join evento on (evento.id_evento=os.id_evento)
inner join subetapa on (subetapa.id_subetapa=evento.id_subetapa)
left join etapa on (etapa.id_etapa=subetapa.id_etapa and etapa.id_etapa in $etapa )
inner join meta on (meta.id_meta=etapa.id_meta)
left  join os_debito on (os_debito.id_os=os.id_os )
inner join fase on (fase.id_fase=pernoite.id_fase)
where pernoite.id_nd=$nd 
group by os.id_os
order by saida_data asc, pernoite.passagem_valor desc
";

$sql = "
select exercicio.ano, pernoite.id_pernoite, os.id_os,meta.display as meta,
subetapa.display as subetapa, os.resumo_servico, posto.posto, posto.id_posto,os.os, concat_ws(' ', posto,especialidade, nome_completo) as nome_militar, nome_guerra,
pernoite.saida_data, pernoite.regresso_data, diaria_qtd, group_concat(pernoite.trecho,'| ') as cid, evento.display as evento,
 sum(diaria_qtd) as diaria_qtd, sum((diaria_qtd * diaria_estimada)) as valor, acrescimo_valor as acrescimo, pernoite.passagem_valor as passagem, fase.display as fase
from os
left join os_debito on (os_debito.id_os=os.id_os )
inner join pernoite on  (os.id_os=pernoite.id_os)
inner join fase on (fase.id_fase=os.id_fase and fase.id_fase not in (29) )
inner join servidor on (servidor.id_servidor=pernoite.id_servidor)
left join posto on (posto.id_posto=servidor.id_posto)
left join especialidade on (especialidade.id_especialidade=servidor.id_especialidade)
inner join cidade on (cidade.id_cidade=os.id_cidade)
inner join identificador on (identificador.id_identificador=os.id_identificador)
inner join exercicio on (exercicio.id_exercicio=identificador.id_exercicio) 
inner join evento on (evento.id_evento=os.id_evento)
inner join subetapa on (subetapa.id_subetapa=evento.id_subetapa)
inner join etapa on (etapa.id_etapa=subetapa.id_etapa and etapa.id_etapa in (7346,7349,7348,7354)  )
inner join meta on (meta.id_meta=etapa.id_meta and meta.id_meta in (2461,2462,2464))
where pernoite.id_nd=5
group by os.id_os
order by saida_data desc

";
$os = $dbonix->fetch_array($sql);

echo $sql;
print_r($os);
exit();


$db = Database02::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
$db->connect();
$db->query("SET NAMES 'utf8'");
$db->query("SET character_set_connection=utf8");
$db->query("SET character_set_client=utf8");
$db->query("SET character_set_results=utf8");
$db->query("SET NAMES 'utf8' COLLATE 'utf8_general_ci'");
$db->query("SET CHARACTER SET utf8");

$i=0;

foreach($os as $chave=>$valor){
      
                //echo $inserenovos;exit();
    $valor['resumo_servico'] = addslashes($valor['resumo_servico']);
	$insere = $db->query("insert into orcamento_andamento (id, id_pernoite, id_os, meta, subetapa, resumo_servico, os, 
		nome_militar, cid, saida_data, regresso_data, nome_guerra, diaria_qtd, valor, acrescimo, passagem, fase,
		created, usuariocriou, ipcriou, hostcriou, posto,ano)
		values(uuid(), '{$valor['id_pernoite']}', '{$valor['id_os']}', '{$valor['meta']}', '{$valor['subetapa']}', '{$valor['resumo_servico']}'
		, '{$valor['os']}', '{$valor['nome_militar']}', '{$valor['cid']}', '{$valor['saida_data']}', '{$valor['regresso_data']}'
		, '{$valor['nome_guerra']}', '{$valor['diaria_qtd']}', '{$valor['valor']}', '{$valor['acrescimo']}', '{$valor['passagem']}', '{$valor['fase']}', now(), '$usuario', '$ip', '$host', '{$valor['posto']}', '{$valor['ano']}' )
	 	on duplicate key update cid='{$valor['cid']}',fase='{$valor['fase']}',posto='{$valor['posto']}',  saida_data='{$valor['saida_data']}', regresso_data='{$valor['regresso_data']}', 
		diaria_qtd='{$valor['diaria_qtd']}', valor='{$valor['valor']}', nome_militar='{$valor['nome_militar']}', os='{$valor['os']}', acrescimo='{$valor['acrescimo']}', passagem='{$valor['passagem']}' , updated=now(), ano={$valor['ano']}  ");
	$i++;

       

}
//exit();

foreach($os as $chave=>$valor){
    $valor['resumo_servico'] = addslashes($valor['resumo_servico']);
	$insere = $db->query("update orcamento_andamento set diaria_qtd='{$valor['diaria_qtd']}', resumo_servico='{$valor['resumo_servico']}', os='{$valor['os']}',valor='{$valor['valor']}', acrescimo='{$valor['acrescimo']}', passagem='{$valor['passagem']}'
	where id_os='{$valor['id_os']}' and id_pernoite='{$valor['id_pernoite']}' ");

}
$totalonix = $i;
$excluicanceladas = $db->query("delete from orcamento_andamento where fase like '%Cancelada%' ");
//$atualizaano = $db->query("update SGBDO.orcamento_andamento set ano=year(saida_data) ");
/*
 * 
 * select os.id_os
from os
inner join identificador on (identificador.id_identificador=os.id_identificador)
inner join exercicio on (exercicio.id_exercicio=identificador.id_exercicio)
inner join evento on (evento.id_evento=os.id_evento)
inner join subetapa on (subetapa.id_subetapa=evento.id_subetapa)
inner join etapa on (etapa.id_etapa=subetapa.id_etapa and etapa.id_etapa in (7346,7349,7348,7354)  )
inner join meta on (meta.id_meta=etapa.id_meta and meta.id_meta in (2461,2462,2464))
group by os.id_os
order by id_os asc

 */
$sqlparaexcluir = "
   select os.id_os
from os
left join os_debito on (os_debito.id_os=os.id_os )
inner join pernoite on  (os.id_os=pernoite.id_os)
inner join fase on (fase.id_fase=os.id_fase and fase.id_fase not in (29) )
inner join servidor on (servidor.id_servidor=pernoite.id_servidor)
left join posto on (posto.id_posto=servidor.id_posto)
left join especialidade on (especialidade.id_especialidade=servidor.id_especialidade)
inner join cidade on (cidade.id_cidade=os.id_cidade)
inner join identificador on (identificador.id_identificador=os.id_identificador)
inner join exercicio on (exercicio.id_exercicio=identificador.id_exercicio) 
inner join evento on (evento.id_evento=os.id_evento)
inner join subetapa on (subetapa.id_subetapa=evento.id_subetapa)
inner join etapa on (etapa.id_etapa=subetapa.id_etapa and etapa.id_etapa in (7346,7349,7348,7354)  )
inner join meta on (meta.id_meta=etapa.id_meta and meta.id_meta in (2461,2462,2464))
where pernoite.id_nd=5
group by os.id_os
order by os.id_os asc

";
$osgeral = $dbonix->fetch_array($sqlparaexcluir);
$controle = date('Ymdhis');
foreach($osgeral as $chave=>$valor){
	$atualiza = $db->query("update orcamento_andamento set controle='{$controle}' where id_os='{$valor['id_os']}' ");
}

//$apaga = $db->query("delete from orcamento_andamento where controle not like '%{$controle}%' or controle is null ");
//echo "select * from orcamento_andamento where controle not in ('{$controle}') ";
//print_r($apaga);	
//exit();



$sql = " select * from onix.os_debito where data_debito>'2013-10-01' and data_debito<'2015-12-31' and valor>0	";
$debito = $dbonix->fetch_array($sql);
$i=0;
foreach($debito as $chave=>$valor){
	$insere = $db->query("update orcamento_andamento set updated=now(), valor='{$valor['valor']}'  where id_os={$valor['id_os']} ");
 	$i++;
	//echo $i."  update SGBDO.orcamento_andamento set updated=now(), valor='{$valor['valor']}'  where id_os={$valor['id_os']} ".'<br>';
}

$planejados = $db->fetch_array("select descricao from orcamento_planejado ");
$db->query("update orcamento_planejado set real_diaria='0', real_passagem='0' ");

foreach($planejados as $chave=>$valor){
	$calculos = $db->fetch_array("select sum(valor+acrescimo) as diariareal, sum(passagem) as passagemreal from SGBDO.orcamento_andamento where planejado='{$valor['descricao']}' group by planejado");
	//echo "select sum(valor+acrescimo) as diariareal, sum(passagem) as passagemreal from SGBDO.orcamento_andamento where planejado='$valor' group by planejado<br>";
	$db->query("update orcamento_planejado set real_diaria='{$calculos[0]['diariareal']}', real_passagem='{$calculos[0]['passagemreal']}' where descricao='{$valor['descricao']}' ");
	//echo "update orcamento_planejado set real_diaria='{$calculos[0]['diariareal']}', real_passagem='{$calculos[0]['passagemreal']}' where descricao='$valor' ";
}

/*
$sql = " select * from onix.os_debito where data_debito>'2013-10-01' and data_debito<'2015-03-01' and pago_valor>0	";
$debito = $dbonix->fetch_array($sql);
$i=0;
foreach($debito as $chave=>$valor){
	$insere = $db->query("update SGBDO.orcamento_andamento set updated=now(), valor='{$valor['pago_valor']}'  where id_os={$valor['id_os']} ");
	$i++;
	echo $i."  update SGBDO.orcamento_andamento set updated=now(), valor='{$valor['pago_valor']}'  where id_os={$valor['id_os']} ".'<br>';
}
*/



$usuario = 'evaldoesl';
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
$campos = array('Ano'=>'ano','Planejado'=>'planejado','Meta'=>'meta','Subetapa'=>'subetapa','Resumo Serviço'=>'resumo_servico','OS'=>'os','Nome Militar'=>'nome_militar','Nome Guerra'=>'nome_guerra','Cidade'=>'cid','Data Saída'=>'saida_data', 'Data Regresso'=>'regresso_data', 'Qtd Diária'=>'diaria_qtd', 'Valor'=>'valor', 'Acréscimo'=>'acrescimo', 'Passagem'=>'passagem');
$tipos = array('ano'=>'num','planejado'=>'txt','meta'=>'txt','subetapa'=>'txt','resumo_servico'=>'txt','os'=>'txt','nome_militar'=>'txt','nome_guerra'=>'txt','cid'=>'txt','saida_data'=>'dt', 'regresso_data'=>'dt',  'diaria_qtd'=>'num', 'valor'=>'num', 'acrescimo'=>'num', 'passagem'=>'num');
$funcoes = array('planejado'=>'editsuspenso','meta'=>'vazio','subetapa'=>'vazio','resumo_servico'=>'vazio','os'=>'vazio','nome_militar'=>'vazio','nome_guerra'=>'vazio','cid'=>'vazio','saida_data'=>'editsuspenso', 'regresso_data'=>'editsuspenso',  'diaria_qtd'=>'vazio', 'valor'=>'vazio', 'acrescimo'=>'vazio', 'passagem'=>'vazio');
$leitura = array('ano'=>1,'planejado'=>0,'meta'=>1,'subetapa'=>1,'resumo_servico'=>1,'os'=>1,'nome_militar'=>1,'nome_guerra'=>1,'cid'=>1,'saida_data'=>0, 'regresso_data'=>0, 'diaria_qtd'=>1, 'valor'=>1, 'acrescimo'=>1, 'passagem'=>1);

$tamanhocampo = array('Ano'=>'4','Planejado'=>'15','Meta'=>'15','Subetapa'=>'23','Resumo Serviço'=>'30','OS'=>'10','Nome Militar'=>'10','Nome Guerra'=>'10','Cidade'=>'10','Data Saída'=>'8', 'Data Regresso'=>'8', 'Qtd Diária'=>'6', 'Valor'=>'6', 'Acréscimo'=>'3', 'Passagem'=>'6');
$campoautocompletar = array('Ano'=>1,'Planejado'=>1,'Meta'=>0,'Subetapa'=>0,'Resumo Serviço'=>0,'OS'=>0,'Nome Militar'=>0,'Nome Guerra'=>0,'Cidade'=>0,'Data Saída'=>0, 'Data Regresso'=>0,  'Qtd Diária'=>0, 'Valor'=>0, 'Acréscimo'=>0, 'Passagem'=>0);
$campoautosomar = array('Ano'=>0,'Planejado'=>0,'Meta'=>0,'Subetapa'=>0,'Resumo Serviço'=>0,'OS'=>0,'Nome Militar'=>0,'Nome Guerra'=>0,'Cidade'=>0,'Data Saída'=>0, 'Data Regresso'=>0, 'Qtd Diária'=>1, 'Valor'=>1, 'Acréscimo'=>1, 'Passagem'=>1);
$campoautosomarvalor = array('Ano'=>0,'Planejado'=>0,'Meta'=>0,'Subetapa'=>0,'Resumo Serviço'=>0,'OS'=>0,'Nome Militar'=>0,'Nome Guerra'=>0,'Cidade'=>0,'Data Saída'=>0, 'Data Regresso'=>0,  'Qtd Diária'=>0, 'Valor'=>0, 'Acréscimo'=>0, 'Passagem'=>0);
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


$filtro = ' where ano=2015 or ano is null ';
$formbusca = '';
$order = ' order by ano DESC , planejado ASC ';


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

function array_mapk($callback, $array) {
	$newArray = array();
	foreach ($array as $k => $v) {
		$newArray[$k] = call_user_func($callback, $k, $v);
	}
	return $newArray;
}

if($sqlfiltro[0]['qtdregistros']>0){
	$maximoregistros = $sqlfiltro[0]['qtdregistros'];
}

$vetorclassificacao = unserialize(iconv('latin1', 'utf8', $sqlfiltro[0]['classificacao']));
if(!empty($vetorclassificacao)){
	foreach($vetorclassificacao as $campo=>$ordem){
		$ordenacao[$campo]=$ordem;
	}
}





if ( ($_GET['i']=='crontab') && ($_GET['acao']=='list') ){
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

$dbonix->close();
$db->close();


?>