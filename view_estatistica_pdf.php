<?php 

require_once('tcpdf/config/lang/bra.php');
require_once('tcpdf/tcpdf.php');

// create new PDF document
//$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Evaldo de Souza Lima');
$pdf->SetTitle('Relatório completo do planejamento da DO');
$pdf->SetSubject('Relatório geral');
$pdf->SetKeywords('Relatório, DO');

// set default header data
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 006', PDF_HEADER_STRING);
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', 7));
$pdf->SetHeaderData('../../images/logo.png', PDF_HEADER_LOGO_WIDTH, 'Processo de Missão da Divisão de Operações      AUTENTICADOR: [ EF09A4B15D18C10 ]', 'Pedido gerado em:'.date('d-m-Y h:i:s').' pelo usuário:  '.$usuario);

// set header and footer fonts
//$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
//$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);

$pdf->SetMargins(PDF_MARGIN_LEFT, 15, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
$pdf->setLanguageArray($l);

// set font
$pdf->SetFont('dejavusans', '', 10);

// add a page
$pdf->AddPage();

$conteudo = file_get_contents('pdf_template_pedidomissao.php');

$destaque_responsavel = 'style="font-family: Arial, Helvetica, sans-serif;color: #000;text-align: left;	background-color: #ccc;margin-top:5px;"';
$titulo = 'style="font-family: Arial, Helvetica, sans-serif;color: #FFF;text-align: center;	background-color: #000;"';
$campos='style="font-family: Arial, Helvetica, sans-serif;color: #000;font-size:26px;text-align: left;background-color: #FFF;"';
$campotxt='style="font-family: Arial, Helvetica, sans-serif;color: #000;font-size:26px;	text-align: left;	background-color: #FFF;	width:100%;"';

$conteudo = str_replace('class="destaque_responsavel"', $destaque_responsavel, $conteudo);
$conteudo = str_replace('class="titulo"', $titulo, $conteudo);
$conteudo = str_replace('class="campos"', $campos, $conteudo);
$conteudo = str_replace('class="campotxt"', $campotxt, $conteudo);


$recibos=<<<RECIBOS
<table width="100%" border="1">
    <tr class="destaque_responsavel">
      <td colspan="3"><p>RECIBO DA ENTRADA NO SETOR DE OS</p></td>
    </tr>
    <tr class="campos">
      <td rowspan="2">Recebi o pedido 130443293244932 do Chefe da Divisão no dia ____ de _______ de __________ às ___:___h. 
      </td>
      <td colspan="2"><br><br><br><br></td>
    </tr>
    <tr  class="campos">
      <td  colspan="2"><p align="right">Assinatura do Setor de OS</p></td>
    </tr>
</table>  
<font size="10px" style="top:0px;margin-bottom:10px;"><br>-----------------------------------------------------------------------------------------------------------------------------------------<br><br><br></font>
<table width="100%" border="1">
    <tr class="destaque_responsavel">
      <td colspan="3"><p>RECIBO DA ENTRADA DO CHEFE DA DIVISÃO</p></td>
    </tr>
    <tr class="campos">
      <td rowspan="2">Recebi o pedido 130443293244932 do Chefe da Divisão no dia ____ de _______ de __________ às ___:___h. 
      </td>
      <td colspan="2"><br><br><br><br></td>
    </tr>
    <tr  class="campos">
      <td  colspan="2"><p align="right">Assinatura do Chf Divisão</p></td>
    </tr>
</table>  
<font size="10px" style="top:0px;margin-bottom:10px;"><br>-----------------------------------------------------------------------------------------------------------------------------------------<br><br><br></font>
<table width="100%" border="1">
    <tr class="destaque_responsavel">
      <td colspan="3"><p>RECIBO DA ENTRADA NA OSEC</p></td>
    </tr>
    <tr class="campos">
      <td rowspan="2">Recebi o pedido 130443293244932 do Planejamento no dia ____ de _______ de __________ às ___:___h. 
      </td>
      <td colspan="2"><br><br><br><br></td>
    </tr>
    <tr  class="campos">
      <td  colspan="2"><p align="right">Assinatura da OSEC</p></td>
    </tr>
</table>  
<font size="10px" style="top:0px;margin-bottom:10px;"><br>-----------------------------------------------------------------------------------------------------------------------------------------<br><br><br></font>
<table width="100%" border="1">
    <tr class="destaque_responsavel">
      <td colspan="3"><p>RECIBO DA ENTRADA NO PLANEJAMENTO</p></td>
    </tr>
    <tr class="campos">
      <td rowspan="2">Recebi o pedido 130443293244932 da Subdivisão ABC no dia ____ de _______ de __________ às ___:___h. 
      </td>
      <td colspan="2"><br><br><br><br></td>
    </tr>
    <tr  class="campos">
      <td  colspan="2"><p align="right">Assinatura do Planejamento</p></td>
    </tr>
</table>  
<font size="10px" style="top:0px;margin-bottom:10px;"><br>-----------------------------------------------------------------------------------------------------------------------------------------<br><br><br></font>
RECIBOS;

$recibos = str_replace('class="destaque_responsavel"', $destaque_responsavel, $recibos);
$recibos = str_replace('class="titulo"', $titulo, $recibos);
$recibos = str_replace('class="campos"', $campos, $recibos);
$recibos = str_replace('class="campotxt"', $campotxt, $recibos);

//$html = strtoupper($html);

//echo $html;
$conteudoconsultaregistros = $db->fetch_array("select * from orcamento_planejado order by subdivisao asc , inicio asc , descricao asc ");

$planejados =<<<PLANEJADOS
<table width="100%" border="1">
    <tr class="destaque_responsavel">
      <td colspan="4"><p>ITENS PLANEJADOS PELA OPG</p></td>
    </tr>
    <tr class="campos">
      <th><b>SUBDIVISÃO</b></th>
      <th><b>DESCRIÇÃO</b></th>
      <th><b>DT INÍCIO</b></th>
      <th><b>QTD PREVISTA</b></th>
    </tr>
PLANEJADOS;

foreach($conteudoconsultaregistros as $chave=>$dados){
	$planejados .= '<tr class="campos"><td>'.$dados['subdivisao'].'</td><td>'.$dados['descricao'].'</td><td>'.$dados['inicio'].'</td><td>'.$dados['qtd_militar'].'</td></tr>';
	
}
$planejados .= '</table>';


$planejados = str_replace('class="destaque_responsavel"', $destaque_responsavel, $planejados);
$planejados = str_replace('class="titulo"', $titulo, $planejados);
$planejados = str_replace('class="campos"', $campos, $planejados);
$planejados = str_replace('class="campotxt"', $campotxt, $planejados);


$pdf->writeHTML($conteudo, true, true, true, false, '');
$pdf->AddPage('P');
$pdf->writeHTML($recibos, true, true, true, false, '');
$pdf->AddPage('P');
$pdf->writeHTML($planejados, true, true, true, false, '');
//$pdf->Image($filename.'.png', 50, 50, 600, '', '', 'http://www.tcpdf.org', '', false, 300);

$pdf->Output('relatorio_completo_'.date('Ymdhis').'.pdf', 'I');
unlink($filename.'.png');
unlink($filename.'.json');

?>