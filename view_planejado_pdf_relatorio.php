<?php 

require_once('tcpdf/config/lang/bra.php');
require_once('tcpdf/tcpdf.php');

// create new PDF document
//$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf = new TCPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Evaldo de Souza Lima');
$pdf->SetTitle('Relatório completo do planejamento da DO');
$pdf->SetSubject('Relatório geral');
$pdf->SetKeywords('Relatório, DO');

// set default header data
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 006', PDF_HEADER_STRING);
$pdf->SetHeaderData('../../images/logo.png', PDF_HEADER_LOGO_WIDTH, 'Relatório geral da Divisão de Operações', 'Gerado em:'.date('d-m-Y h:i:s').' pelo usuário:  '.$usuario);

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

//set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

//set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

//set some language-dependent strings
$pdf->setLanguageArray($l);

// ---------------------------------------------------------

// set font
$pdf->SetFont('dejavusans', '', 10);

// add a page
$pdf->AddPage();



$cabecalho=<<<RELATORIO
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<style type="text/css">
<!--
body table tr th.titulogeral  {
	color: #FFF;
	font-family: "Arial Black", Gadget, sans-serif;
	font-size: 12px;
	font-weight: bold;
	background-color: #3888B4;
	text-transform: uppercase;
}
body table tr.titulocoluna  {
	color: #000;
	font-family: "Arial Black", Gadget, sans-serif;
	font-size: 10px;
	font-weight: bold;
	background-color: #9EC8E1;
	text-transform: uppercase;
}

body table tr.linhaimpar  {
	background-color: #E4EEBC;
	text-transform: uppercase;
}
body table tr.linhapar  {
	background-color: #FFF;
	text-transform: uppercase;
}
body table tr.total  {
	background-color: #00FF00;
	font-size: 10px;
	text-transform: uppercase;
}
body table tr.total td  {
	font-family: "Arial Black", Gadget, sans-serif;
	font-size: 10px;
	font-weight:bold;
	color: #000;
	text-transform: uppercase;
}
        
body table tr.linhapar td  {
	font-family: "Arial Black", Gadget, sans-serif;
	font-size: 10px;
	font-weight:normal;
	color: #000;
	text-transform: uppercase;
}
body table tr.linhaimpar td  {
	font-family: "Arial Black", Gadget, sans-serif;
	font-size: 10px;
	font-weight:normal;
	color: #000;
	text-transform: uppercase;
}

body table tr td.destaque  {
	color: #000;
	font-family: "Arial Black", Gadget, sans-serif;
	font-size: 10px;
	font-weight: bold;
	background-color: #C3DDED;
	text-transform: uppercase;
}
body table tr td.atencao  {
	color: #000;
	font-family: "Arial Black", Gadget, sans-serif;
	font-size: 10px;
	font-weight: bold;
	background-color: #FAFA5E;
	text-transform: uppercase;
}

-->
</style>
</head>

<body>
RELATORIO;

$htmltipo=<<<RELATORIO
<table width="100%" border="1" cellpadding="1" cellspacing="1">
  <tr>
    <th colspan="9" class="titulogeral">PLANEJAMENTO POR TIPO DA DIVISÃO DE OPERAÇÕES </th>
  </tr>
  <tr class="titulocoluna">
    <td rowspan="2">SUBDIVISÃO</td>
    <td colspan="2">ESSENCIAL</td>
    <td colspan="2">NECESSÁRIO</td>
    <td colspan="2">RECOMENDÁVEL</td>
    <td colspan="2">NÃO PLANEJADO</td>
  </tr>
  <tr class="titulocoluna">
    <td>DIÁRIA</td>
    <td>PASSAGEM</td>
    <td>DIÁRIA</td>
    <td>PASSAGEM</td>
    <td>DIÁRIA</td>
    <td>PASSAGEM</td>
    <td>DIÁRIA</td>
    <td>PASSAGEM</td>
   </tr>
  $orcamentoportipo
</table>
<p>&nbsp;</p>
<table width="100%" border="1" cellpadding="1" cellspacing="1">
  <tr>
    <th colspan="9" class="titulogeral">EXECUTADO POR TIPO DA DIVISÃO DE OPERAÇÕES </th>
  </tr>
  <tr class="titulocoluna">
    <td rowspan="2">SUBDIVISÃO</td>
    <td colspan="2">ESSENCIAL</td>
    <td colspan="2">NECESSÁRIO</td>
    <td colspan="2">RECOMENDÁVEL</td>
    <td colspan="2">NÃO PLANEJADO</td>
  </tr>
  <tr class="titulocoluna">
    <td>DIÁRIA</td>
    <td>PASSAGEM</td>
    <td>DIÁRIA</td>
    <td>PASSAGEM</td>
    <td>DIÁRIA</td>
    <td>PASSAGEM</td>
    <td>DIÁRIA</td>
    <td>PASSAGEM</td>
   </tr>
  $orcamentoportipoexecutado
</table>
<p>&nbsp;</p>
RELATORIO;


$html01=<<<RELATORIO
<table width="100%" border="1" cellpadding="1" cellspacing="1">
  <tr>
    <th colspan="5" class="titulogeral">RELATÓRIO GERAL DAS MISSÕES PLANEJADAS DA DIVISÃO DE OPERAÇÕES - DIÁRIAS</th>
  </tr>
  <tr class="titulocoluna">
    <td>SUBDIVISÃO</td>
    <td>TOTAL PLANEJADAS</td>
    <td>TOTAL EXECUTADAS</td>
    <td>SALDO</td>
    <td>PERCENTUAL EXECUTADO</td>
  </tr>
  $orcamentopordivisao
</table>
<br><br>
<p>&nbsp;</p>
<table width="100%" border="1" cellpadding="1" cellspacing="1">
  <tr>
    <th colspan="5" class="titulogeral">RELATÓRIO GERAL DAS MISSÕES PLANEJADAS DA DIVISÃO DE OPERAÇÕES - PASSAGENS</th>
  </tr>
  <tr class="titulocoluna">
    <td>SUBDIVISÃO</td>
    <td>TOTAL PLANEJADAS</td>
    <td>TOTAL EXECUTADAS</td>
    <td>SALDO</td>
    <td>PERCENTUAL EXECUTADO</td>
  </tr>
  $orcamentopordivisaopassagem
</table>
<p>&nbsp;</p>

RELATORIO;
//$html01 .=  dirname($_SERVER["SCRIPT_FILENAME"]);

// --- Method (A) ------------------------------------------
// the Image() method recognizes the alpha channel embedded on the image:
$titulogeral = 'style="color:#FFF;font-family:\'Arial Black\', Gadget, sans-serif;font-size: 30px;font-weight: bold;background-color: #3888B4;text-transform: uppercase;"';
$titulocoluna = 'style="color: #000;font-family:\'Arial Black\', Gadget, sans-serif;font-size: 26px;font-weight: bold;background-color: #9EC8E1;text-transform: uppercase;"';
$linhaimpar='style="background-color: #E4EEBC;text-transform: uppercase;font-size: 23px;font-weight:normal;	color: #000;"';
$linhapar='style="background-color: #FFF;text-transform: uppercase;font-size: 23px;font-weight:normal;	color: #000;"';
$destaque='style="color: #000;font-family:\'Arial Black\', Gadget, sans-serif;font-size: 23px;font-weight: bold;background-color: #C3DDED;text-transform: uppercase;"';
$atencao='style="color: #000;font-family:\'Arial Black\', Gadget, sans-serif;font-size: 26px;font-weight: bold;background-color: #FAFA5E;text-transform: uppercase;"';

$htmltipo = str_replace('class="destaque"', $destaque, $htmltipo);
$htmltipo = str_replace('class="atencao"', $atencao, $htmltipo);
$htmltipo = str_replace('class="linhaimpar"', $linhaimpar, $htmltipo);
$htmltipo = str_replace('class="linhapar"', $linhapar, $htmltipo);
$htmltipo = str_replace('class="titulogeral"', $titulogeral, $htmltipo);
$htmltipo = str_replace('class="titulocoluna"', $titulocoluna, $htmltipo);


$html01 = str_replace('class="destaque"', $destaque, $html01);
$html01 = str_replace('class="atencao"', $atencao, $html01);
$html01 = str_replace('class="linhaimpar"', $linhaimpar, $html01);
$html01 = str_replace('class="linhapar"', $linhapar, $html01);
$html01 = str_replace('class="titulogeral"', $titulogeral, $html01);
$html01 = str_replace('class="titulocoluna"', $titulocoluna, $html01);

$titulogeral = 'style="color:#FFF;font-family:\'Arial Black\', Gadget, sans-serif;font-size: 22px;font-weight: bold;background-color: #3888B4;text-transform: uppercase;"';
$titulocoluna = 'style="color: #000;font-family:\'Arial Black\', Gadget, sans-serif;font-size: 20px;font-weight: bold;background-color: #9EC8E1;text-transform: uppercase;"';
$linhaimpar='style="background-color: #E4EEBC;text-transform: uppercase;font-size: 16px;font-weight:normal;	color: #000;"';
$linhapar='style="background-color: #FFF;text-transform: uppercase;font-size: 16px;font-weight:normal;	color: #000;"';
$destaque='style="color: #000;font-family:\'Arial Black\', Gadget, sans-serif;font-size: 16px;font-weight: bold;background-color: #C3DDED;text-transform: uppercase;"';
$atencao='style="color: #000;font-family:\'Arial Black\', Gadget, sans-serif;font-size: 20px;font-weight: bold;background-color: #FAFA5E;text-transform: uppercase;"';


$html02=<<<RELATORIO
<table width="100%" border="1" cellpadding="1" cellspacing="1">
  <tr>
    <th colspan="14" class="titulogeral">RELATÓRIO GERAL DAS MISSÕES POR SUBDIVISÃO - DIÁRIA</th>
  </tr>
  <tr class="titulocoluna">
    <td>SUBDIVISÃO</td>
    <td>STATUS</td>
    <td>JAN</td>
    <td>FEV</td>
    <td>MAR</td>
    <td>ABR</td>
    <td>MAI</td>
    <td>JUN</td>
    <td>JUL</td>
    <td>AGO</td>
    <td>SET</td>
    <td>OUT</td>
    <td>NOV</td>
    <td>DEZ</td>
  </tr>
$relatoriopormes
</table>
		
<br><br>
RELATORIO;


$html02 = str_replace('class="destaque"', $destaque, $html02);
$html02 = str_replace('class="atencao"', $atencao, $html02);
$html02 = str_replace('class="linhaimpar"', $linhaimpar, $html02);
$html02 = str_replace('class="linhapar"', $linhapar, $html02);
$html02 = str_replace('class="titulogeral"', $titulogeral, $html02);
$html02 = str_replace('class="titulocoluna"', $titulocoluna, $html02);


$html021=<<<RELATORIO
<table width="100%" border="1" cellpadding="1" cellspacing="1">
  <tr>
    <th colspan="14" class="titulogeral">RELATÓRIO GERAL DAS MISSÕES POR SUBDIVISÃO - PASSAGEM</th>
  </tr>
  <tr class="titulocoluna">
    <td>SUBDIVISÃO</td>
    <td>STATUS</td>
    <td>JAN</td>
    <td>FEV</td>
    <td>MAR</td>
    <td>ABR</td>
    <td>MAI</td>
    <td>JUN</td>
    <td>JUL</td>
    <td>AGO</td>
    <td>SET</td>
    <td>OUT</td>
    <td>NOV</td>
    <td>DEZ</td>
  </tr>
$relatoriopormespassagem
</table>
<br><br>
RELATORIO;


$html021 = str_replace('class="destaque"', $destaque, $html021);
$html021 = str_replace('class="atencao"', $atencao, $html021);
$html021 = str_replace('class="linhaimpar"', $linhaimpar, $html021);
$html021 = str_replace('class="linhapar"', $linhapar, $html021);
$html021 = str_replace('class="titulogeral"', $titulogeral, $html021);
$html021 = str_replace('class="titulocoluna"', $titulocoluna, $html021);



















$html03=<<<RELATORIO
<p>&nbsp;</p><br><table width="100%" border="1" cellpadding="1" cellspacing="1">
$relatoriosubdivisaomes
</table>
	<p></p>
	<p>&nbsp;</p>
<p></p>
<p>&nbsp;</p>
</body>
</html>
RELATORIO;

$html03 = str_replace('class="destaque"', $destaque, $html03);
$html03 = str_replace('class="atencao"', $atencao, $html03);
$html03 = str_replace('class="linhaimpar"', $linhaimpar, $html03);
$html03 = str_replace('class="linhapar"', $linhapar, $html03);
$html03 = str_replace('class="titulogeral"', $titulogeral, $html03);
$html03 = str_replace('class="titulocoluna"', $titulocoluna, $html03);







//$html = strtoupper($html);

//echo $html;

$pdf->writeHTML($cabecalho, true, true, true, false, '');
//$pdf->Image($filename.'.png', 50, 50, 600, '', '', 'http://www.tcpdf.org', '', false, 300);
//$pdf->Image($filename.'.png',15,20);
//$pdf->AddPage('L');
$pdf->writeHTML($htmltipo, true, true, true, false, '');
$pdf->AddPage('L');
//$pdf->Image($filename02.'.png',15,20);
//$pdf->AddPage('L');
$pdf->writeHTML($html01, true, true, true, false, '');
$pdf->AddPage('L');
$pdf->writeHTML($html02, true, true, true, false, '');
$pdf->AddPage('L');
$pdf->writeHTML($html021, true, true, true, false, '');
$pdf->AddPage('P');
$pdf->writeHTML($html03, true, true, true, false, '');

$pdf->Output('relatorio_completo_'.date('Ymdhis').'.pdf', 'I');
unlink($filename.'.png');
unlink($filename.'.json');
?>