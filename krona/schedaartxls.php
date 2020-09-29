<?php
header('Content-Type: application/vnd.ms-excel');
header('Cache-Control: no-cache');
header('Cache-Control: no-store' , false);     // false => this header not override the previous similar header
header("Connection: Keep-Alive");
header("Keep-Alive: timeout=300");
/************************************************************************/
/* Project ArcaWeb                               		 			    */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2013 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/
include("header.php");
include("db-utils.php");
$maga = trim($_GET['maga']);
$anno = current_year();
if(date('n') <4) {
	$anno--;
}
$timestamp = date('mdY', time());
header("Content-Disposition: attachment; filename=\"Mag-$maga-$timestamp.xls\"");

require_once '../phpexcel/PHPExcel.php';
$objPHPExcel = new PHPExcel();
// Set properties
$objPHPExcel->getProperties()->setCreator("KronaKoblenz SpA")
                ->setLastModifiedBy("CED")
                ->setTitle("Situazione Magazzino $maga $timestamp")
                ->setSubject("Situazione Magazzino $maga $timestamp")
                ->setDescription("Situazione Magazzino $maga $timestamp")
                ->setKeywords("Situazione Magazzino $timestamp krona koblenz $maga")
                ->setCategory("Situazione Magazzino $maga $timestamp");
$objPHPExcel->getActiveSheet()->setTitle("Mag-$maga-$anno");

$objPHPExcel->getActiveSheet()->mergeCells('A1:H1');
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', "SITUAZIONE DI MAGAZZINO $maga aggiornata al $timestamp");
$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFill()
	->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
	->getStartColor()->setARGB('FFFFFF00');

$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A2', 'Cod.Magazzino')
            ->setCellValue('B2', 'Cod. Articolo')
            ->setCellValue('C2', 'Descrizione')
            ->setCellValue('D2', 'UM')
            ->setCellValue('E2', 'Esistenza')
            ->setCellValue('F2', 'Lotto')
            ->setCellValue('G2', 'isLotto')
            ->setCellValue('H2', 'PesoKG')
            ->setCellValue('I2', 'EAN 1')
            ->setCellValue('J2', 'UM EAN 1')
            ->setCellValue('K2', 'EAN 2')
            ->setCellValue('L2', 'UM EAN 2')
            ->setCellValue('M2', 'EAN 3')
            ->setCellValue('N2', 'UM EAN 3');
$objPHPExcel->getActiveSheet()->getStyle('A2:N2')->getFill()
	->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
	->getStartColor()->setARGB('FF00FFFF');

$connectionstring = db_connect($dbase);

$Query = "SELECT MAGGIAC.ARTICOLO, MAGART.DESCRIZION, MAGART.LOTTI, MAGART.UNMISURA, MAGART.PESOUNIT ";
$Query .= "FROM MAGGIAC INNER JOIN MAGART ON MAGART.CODICE = MAGGIAC.ARTICOLO ";
$Query .= "WHERE MAGGIAC.MAGAZZINO = \"$maga\" AND MAGGIAC.ESERCIZIO=\"$anno\" ORDER BY MAGGIAC.ARTICOLO";
$queryexe = db_query($connectionstring, $Query) or die(mysql_error() );
$row = 3;

while($rw = mysql_fetch_object($queryexe)) {
	$art = $rw->ARTICOLO;
	$desc = $rw->DESCRIZION;
	$um = $rw->UNMISURA;
	$peso = $rw->PESOUNIT;
	$Query = "SELECT ALIAS, UNMISURA FROM MAGALIAS WHERE CODICEARTI=\"$art\" AND idprog=8";
	$qe_alias = db_query($connectionstring, $Query); // or die(mysql_error() )
	$rw_alias = mysql_fetch_object($qe_alias);
	$alias8 = $rw_alias->ALIAS;
	$um8 = $um;
	$Query = "SELECT ALIAS, UNMISURA FROM MAGALIAS WHERE CODICEARTI=\"$art\" AND idprog=7";
	$qe_alias = db_query($connectionstring, $Query); // or die(mysql_error() )
	$rw_alias = mysql_fetch_object($qe_alias);
	$alias7 = $rw_alias->ALIAS;
	$um7 = $rw_alias->UNMISURA;
	$Query = "SELECT ALIAS, UNMISURA FROM MAGALIAS WHERE CODICEARTI=\"$art\" AND idprog=6";
	$qe_alias = db_query($connectionstring, $Query); // or die(mysql_error() )
	$rw_alias = mysql_fetch_object($qe_alias);
	$alias6 = $rw_alias->ALIAS;
	$um6 = $rw_alias->UNMISURA;
	if($rw->LOTTI > 0) {
		$isLotti = true;
		$Query = "SELECT LOTTO, (PROGQTACAR-PROGQTASCA+PROGQTARET) AS GIACENZA FROM MAGGIACL WHERE MAGAZZINO = \"$maga\" AND ARTICOLO = \"$art\" ORDER BY LOTTO";
		$qe = db_query($connectionstring, $Query) or die(mysql_error() );
		while($rs = db_fetch_row($qe)) {
			writeRow($row, $maga, $art, $desc, $um, xRound($rs[1]), $rs[0], $isLotti, $peso, $alias8, $um8, $alias7, $um7, $alias6, $um6);
			$row++;
		}
	} else {
		$isLotti = false;
		$Query = "SELECT (MAGGIAC.GIACINI+MAGGIAC.PROGQTACAR-MAGGIAC.PROGQTASCA+MAGGIAC.PROGQTARET) AS GIACENZA ";
		$Query .= "FROM MAGGIAC ";
		$Query .= "WHERE MAGGIAC.MAGAZZINO = \"$maga\" AND MAGGIAC.ESERCIZIO=\"$anno\" AND MAGGIAC.ARTICOLO=\"$art\" ";
		$qe = db_query($connectionstring, $Query) or die(mysql_error() );
		$rs = db_fetch_row($qe);
		writeRow($row, $maga, $art, $desc, $um, xRound($rs[0]), "", $isLotti, $peso, $alias8, $um8, $alias7, $um7, $alias6, $um6);
		$row++;
	}
}

//Sola Lettura Alcune Colonne

$objPHPExcel->getActiveSheet()->getProtection()->setSheet(true);
//$objPHPExcel->getActiveSheet()->protectCells("A2:D$row", "PHP");
$objPHPExcel->getActiveSheet()->getStyle("A2:N$row")
	->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_PROTECTED);

// Autosize delle colonne
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('A')->setAutoSize(true);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('B')->setAutoSize(true);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->setAutoSize(true);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('D')->setAutoSize(true);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->setAutoSize(true);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('F')->setAutoSize(true);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('G')->setAutoSize(true);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('H')->setAutoSize(true);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('I')->setAutoSize(true);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('J')->setAutoSize(true);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('K')->setAutoSize(true);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('L')->setAutoSize(true);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('M')->setAutoSize(true);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('N')->setAutoSize(true);

// Salvataggio
require_once '../phpexcel/PHPExcel/IOFactory.php';
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');

function writeRow($row, $maga, $art, $desc, $um, $giac, $lotto, $isLotti, $peso, $alias8, $um8, $alias7, $um7, $alias6, $um6) {
global $objPHPExcel;
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueExplicit("A$row", $maga, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueExplicit("B$row", $art, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("C$row", $desc);
	$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue("D$row", $um);
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("E$row", $giac);
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueExplicit("F$row", $lotto, PHPExcel_Cell_DataType::TYPE_STRING);
	if($isLotti){
		$objPHPExcel->setActiveSheetIndex(0)
            		->setCellValueExplicit("G$row", "1", PHPExcel_Cell_DataType::TYPE_STRING);
	} else {
		$objPHPExcel->setActiveSheetIndex(0)
            		->setCellValueExplicit("G$row", "0", PHPExcel_Cell_DataType::TYPE_STRING);
	}
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("H$row", $peso);
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueExplicit("I$row", $alias8, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue("J$row", $um8);
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueExplicit("K$row", $alias7, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue("L$row", $um7);
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueExplicit("M$row", $alias6, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue("N$row", $um6);

}
?>
