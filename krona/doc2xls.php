<?php
header('Content-Type: application/vnd.ms-excel');
header('Cache-Control: no-cache');
header('Cache-Control: no-store' , false);     // false => this header not override the previous similar header
header("Connection: Keep-Alive");
header("Keep-Alive: timeout=300");

include("header.php");
include("db-utils.php");

$id_testa = isset($_GET['id']) ? $_GET['id'] : 0;
//$mode = isset($_GET['mode']) ? $_GET['mode'] : "";
session_start();
//$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
//$cf = $cookie[0];
//$anno = current_year();

//header("Content-Disposition: attachment; filename=\"doc_.xls\"");

$connectionstring = db_connect($dbase);

$Query =  "SELECT DOCTES.TIPODOC, DOCTES.DATADOC, DOCTES.NUMERODOC, DOCTES.DATADOCFOR, DOCTES.NUMERODOCF, ";
$Query .= "ANAGRAFE.DESCRIZION as RAGSOC, DOCTES.COLLI, DOCTES.PESOLORDO, VETTORI.DESCRIZION as VETTORE, DOCTES.VOLUME, DOCTES.MAGARRIVO ";
$Query .= "FROM DOCTES LEFT OUTER JOIN ANAGRAFE ON ANAGRAFE.CODICE = DOCTES.CODICECF LEFT OUTER JOIN VETTORI ON VETTORI.CODICE = DOCTES.VETTORE1 ";
$Query .= "WHERE DOCTES.ID = $id_testa";
$rs = db_query($connectionstring, $Query) or die(mysql_error());
$row = mysql_fetch_object($rs);

$tipodoc = $row->TIPODOC;
$datadoc = $row->DATADOC;
$numdoc = $row->NUMERODOC;
if ($row->DATADOCFOR==""){
	$datadocf = $datadoc;
} else {
	$datadocf = $row->DATADOCFOR;
}
$numerodocf = $row->NUMERODOCF;
$fornitore = $row->RAGSOC;
$totcolli = $row->COLLI;
$totpeso = $row->PESOLORDO;
$totvolume = $row->VOLUME;
$vettore = $row->VETTORE;
$magarrivo = $row->MAGARRIVO;

header("Content-Disposition: attachment; filename=\"doc_".$tipodoc."_".$numdoc.".xls\"");

require_once '../phpexcel/PHPExcel.php';
$objPHPExcel = new PHPExcel();
// Set properties
$objPHPExcel->getProperties()->setCreator("KronaKoblenz SpA")
                ->setLastModifiedBy("CED")
                ->setTitle("Doc_$tipodoc_$numerodoc")
                ->setSubject("Doc_$tipodoc_$numerodoc")
                ->setDescription("Doc_$tipodoc_$numerodoc")
                ->setKeywords("doc $tipodoc krona koblenz $numerodoc")
                ->setCategory("Doc_$tipodoc_$numerodoc");
$objPHPExcel->getActiveSheet()->setTitle("Doc_$tipodoc_$numerodoc");

$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'TipoRiga')
            ->setCellValue('B1', 'TipoDoc')
            ->setCellValue('C1', 'NumDoc')
            ->setCellValue('D1', 'DataRegistrazione')
            ->setCellValue('E1', 'Fornitore')
            ->setCellValue('F1', 'DataDocForn')
            ->setCellValue('G1', 'NumeroDocF')
            ->setCellValue('H1', 'Mag.Arrivo')
            ->setCellValue('I1', 'Cod.Articolo')
            ->setCellValue('J1', 'U.M.')
            ->setCellValue('K1', 'Quantità')
            ->setCellValue('L1', 'Lotto')
            ->setCellValue('M1', 'GestLotto')
            ->setCellValue('N1', 'TotPesoLordo')
            ->setCellValue('O1', 'TotVolume')
            ->setCellValue('P1', 'TotColli')
            ->setCellValue('Q1', 'Vettore');
/*
$objPHPExcel->getActiveSheet()->getStyle('A1:P1')->getFill()
	->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
	->getStartColor()->setARGB('FF00FFFF');
*/

$row=2;
writeRow("TES", $row, $tipodoc, $numdoc, $datadoc, $fornitore, $datadocf, $numerodocf, $magarrivo, "", "", "", "", "", $totpeso, $TotVolume, $totcolli, $vettore);

$Query =  "SELECT DOCRIG.CODICEARTI, DOCRIG.UNMISURA, DOCRIG.QUANTITA, DOCRIG.LOTTO, ";
$Query .= "MAGART.LOTTI ";
$Query .= "FROM DOCRIG LEFT OUTER JOIN MAGART ON MAGART.CODICE = DOCRIG.CODICEARTI WHERE DOCRIG.ID_TESTA = $id_testa AND DOCRIG.CODICEARTI!='' AND DOCRIG.ESPLDISTIN!='C' ";
$rs = db_query($connectionstring, $Query) or die(mysql_error());
while($rw = mysql_fetch_object($rs)) {
	$art = $rw->CODICEARTI;
	$um = $rw->UNMISURA;
	$qta = $rw->QUANTITA;
	$lotto = $rw->LOTTO;
	$isLotti = $rw->lotti;
	$row++;

	writeRow("RIG", $row, $tipodoc, $numdoc, $datadoc, $fornitore, $datadocf, $numerodocf, $magarrivo, $art, $um, $qta, $lotto, $isLotti, $totpeso, $TotVolume, $totcolli, $vettore);

}

//Sola Lettura Alcune Colonne
	/*
	$objPHPExcel->getActiveSheet()->getProtection()->setSheet(true);
	//$objPHPExcel->getActiveSheet()->protectCells("A2:D$row", "PHP");
	$objPHPExcel->getActiveSheet()->getStyle("A2:D$row")
		->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_PROTECTED);

	$objPHPExcel->getActiveSheet()->getStyle("E2:E$row")
		->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);

	$objPHPExcel->getActiveSheet()->getStyle("F2:H$row")
		->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_PROTECTED);

	$objPHPExcel->getActiveSheet()->getStyle("G2:G$row")
		->getProtection()->setLocked(PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);
	*/
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
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('O')->setAutoSize(true);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('P')->setAutoSize(true);
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('Q')->setAutoSize(true);

//DATE
//$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('C')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);
//$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('E')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDDSLASH);

// Salvataggio
require_once '../phpexcel/PHPExcel/IOFactory.php';
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');

function writeRow($type, $row, $tipodoc, $numdoc, $datadoc, $fornitore, $datadocf, $numerodocf, $magarrivo, $art, $um, $qta, $lotto, $isLotti, $totpeso, $TotVolume, $totcolli, $vettore) {
	global $objPHPExcel;
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueExplicit("A$row", $type, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueExplicit("B$row", $tipodoc, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueExplicit("C$row", $numdoc, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("D$row", $datadoc);
	$objPHPExcel->setActiveSheetIndex(0)
			->setCellValueExplicit("E$row", $fornitore, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("F$row", $datadocf);
	if($numerodocf!=""){
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueExplicit("G$row", $numerodocf, PHPExcel_Cell_DataType::TYPE_STRING);
	} else {
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueExplicit("G$row", $tipodoc." ".$numdoc, PHPExcel_Cell_DataType::TYPE_STRING);
	}
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueExplicit("H$row", $magarrivo, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueExplicit("I$row", $art, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueExplicit("J$row", $um, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueExplicit("K$row", $qta, PHPExcel_Cell_DataType::TYPE_NUMERIC);
	$objPHPExcel->setActiveSheetIndex(0)
      		->setCellValueExplicit("L$row", $lotto, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueExplicit("M$row", ($lotto!="" ? 1 : 0), PHPExcel_Cell_DataType::TYPE_NUMERIC);
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueExplicit("N$row", $TotPesoLordo, PHPExcel_Cell_DataType::TYPE_NUMERIC);
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueExplicit("O$row", $TotVolume, PHPExcel_Cell_DataType::TYPE_NUMERIC);
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueExplicit("P$row", $TotColli, PHPExcel_Cell_DataType::TYPE_NUMERIC);
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueExplicit("Q$row", $Vettore, PHPExcel_Cell_DataType::TYPE_STRING);
	
}
?>