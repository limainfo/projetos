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


$objRichText = new PHPExcel_RichText();
$objRichText->createText('Tabela gerada às ');

$objPayable = $objRichText->createTextRun(date('h:i:s do dia d-m-Y'));
$objPayable->getFont()->setBold(true);
$objPayable->getFont()->setItalic(true);
$objPayable->getFont()->setColor( new PHPExcel_Style_Color( PHPExcel_Style_Color::COLOR_DARKGREEN ) );

$objRichText->createText(' => válida somente para filtro. Os dados devem ser inseridos no sistema.');

$objPHPExcel->getActiveSheet()->getCell('A1')->setValue($objRichText);


$objPHPExcel->getActiveSheet()->setCellValue('A2', 'Ano')
							  ->setCellValue('B2', 'Subdivisão')
                              ->setCellValue('C2', 'Início')
                              ->setCellValue('D2', 'Término')
                              ->setCellValue('E2', 'Mês')
                              ->setCellValue('F2', 'Status')
                              ->setCellValue('G2', 'Tipo')
                              ->setCellValue('H2', 'Descrição')
                              ->setCellValue('I2', 'Local')
                              ->setCellValue('J2', 'Qtd Militar')
                              ->setCellValue('K2', 'Qtd Diárias')
                              ->setCellValue('L2', 'Adicional')
                              ->setCellValue('M2', 'Valor Diária')
                              ->setCellValue('N2', 'Valor Passagem')
                              ->setCellValue('O2', 'PLN Diária')
                              ->setCellValue('P2', 'PLN Passagem')
                              ->setCellValue('Q2', 'Real Diária')
                              ->setCellValue('R2', 'Real Passagem');

$dataArray = $registros;
$ultimo = count($registros)+2;
$intervalo = 'A2:R'.($ultimo);
$objPHPExcel->getActiveSheet()->getStyle($intervalo)->getFont()->setSize(8);

$objPHPExcel->getActiveSheet()->fromArray($dataArray, NULL, 'A3');

// Set title row bold
$objPHPExcel->getActiveSheet()->getStyle('A2:R2')->getFont()->setBold(true);

// Set autofilter
// Always include the complete filter range!
// Excel does support setting only the caption
// row, but that's not a best practise...
//$objPHPExcel->getActiveSheet()->setAutoFilter($objPHPExcel->getActiveSheet()->calculateWorksheetDimension());
$objPHPExcel->getActiveSheet()->setAutoFilter($intervalo);

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);




header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="planejado'.date('Ymdhis').'.xls"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;
