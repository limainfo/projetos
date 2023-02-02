<?php
/**
 * PHPExcel
 *
 * Copyright (C) 2006 - 2014 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    1.8.0, 2014-03-02
 */

/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

date_default_timezone_set('America/Manaus');

/** Include PHPExcel */
require_once dirname(__FILE__) . '/Classes/PHPExcel.php';

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();
$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);

// Set document properties	
$objPHPExcel->getProperties()->setCreator('Evaldo de Souza Lima')
							 ->setLastModifiedBy('SIOP')
							 ->setTitle('Planejado')
							 ->setSubject('Planejamento do orçamento')
							 ->setDescription('Documento contendo as necessidades para o exercício atual.')
							 ->setKeywords('office PHPExcel php')
							 ->setCategory('Test result file');

// Create the worksheet
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()
			      ->setCellValue('A1', 'Ano')
			      ->setCellValue('B1', 'Subdivisão')
                              ->setCellValue('C1', 'Início')
                              ->setCellValue('D1', 'Término')
                              ->setCellValue('E1', 'Mês')
                              ->setCellValue('F1', 'Status')
                              ->setCellValue('G1', 'Tipo')
                              ->setCellValue('H1', 'Descrição')
                              ->setCellValue('I1', 'Local')
                              ->setCellValue('J1', 'Qtd Militar')
                              ->setCellValue('K1', 'Qtd Diárias')
                              ->setCellValue('L1', 'Adicional')
                              ->setCellValue('M1', 'Valor Diária')
                              ->setCellValue('N1', 'Valor Passagem')
                              ->setCellValue('O1', 'PLN Diária')
                              ->setCellValue('P1', 'PLN Passagem')
                              ->setCellValue('Q1', 'Real Diária')
                              ->setCellValue('R1', 'Real Passagem');

$dataArray = $registros;

$objPHPExcel->getActiveSheet()->fromArray($dataArray[0], NULL, 'A2');
$ultimo = count($dataArray)+1;

//$testArray = array("testcelltext1", "testcelltext2", "testcelltext3");
//$objPHPExcel->getActiveSheet()->fromArray($testArray[0], NULL, 'A2');

// Set title row bold
$objPHPExcel->getActiveSheet()->getStyle('A1:R1')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A1:R1')->getFont()->setSize(8);

// Set autofilter
// Always include the complete filter range!
// Excel does support setting only the caption
// row, but that's not a best practise...
$objPHPExcel->getActiveSheet()->setAutoFilter($objPHPExcel->getActiveSheet()->calculateWorksheetDimension());

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

$rendererName = PHPExcel_Settings::PDF_RENDERER_TCPDF;
//$rendererName = PHPExcel_Settings::PDF_RENDERER_MPDF;
//$rendererName = PHPExcel_Settings::PDF_RENDERER_DOMPDF;
$rendererLibrary = 'tcpdf';
//$rendererLibrary = 'mPDF5.4';
//$rendererLibrary = 'tcpdf.php';
$rendererLibraryPath = dirname(__FILE__) . '/'. $rendererLibrary;


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
//$objPHPExcel->setActiveSheetIndex(0);

if (!PHPExcel_Settings::setPdfRenderer(
		$rendererName,
		$rendererLibraryPath
)) {
	die(
			'NOTICE: Please set the $rendererName and $rendererLibraryPath values' .
			'<br />' .
			'at the top of this script as appropriate for your directory structure'
	);
}

$objPHPExcel->getActiveSheet()->getStyle('A2:Q'.$ultimo)->getFont()->setSize(8);
//print_r($dataArray);
//exit();
// Redirect output to a client’s web browser (PDF)
header('Content-Type: application/pdf');
header('Content-Disposition: attachment;filename="planejado'.date('Ymdhis').'.pdf"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'PDF');
$objWriter->setSheetIndex(0);
//$objWriter->getActiveSheet()->getStyle('A1:Q1')->getFont()->setSize(8);
$objWriter->save('php://output');
exit;

