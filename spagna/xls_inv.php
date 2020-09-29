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
header("Content-Disposition: attachment; filename=\"inventario-$maga-$anno.xls\"");

require_once '../phpexcel/PHPExcel.php';
$objPHPExcel = new PHPExcel();
// Set properties
$objPHPExcel->getProperties()->setCreator("KronaKoblenz SpA")
                ->setLastModifiedBy("CED")
                ->setTitle("Inventario $maga $anno")
                ->setSubject("Inventario deposito $maga anno $anno")
                ->setDescription("Inventario deposito $maga anno $anno")
                ->setKeywords("inventario $anno krona koblenz $maga")
                ->setCategory("Inventario $maga $anno");
$objPHPExcel->getActiveSheet()->setTitle("Inv-$maga-$anno");

$objPHPExcel->getActiveSheet()->mergeCells('A1:H1');
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', "ATTENZIONE: inserire le quantita' esclusivamente nell'unita' di misura indicata");
$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$objPHPExcel->getActiveSheet()->getStyle('A1')->getFill()
	->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
	->getStartColor()->setARGB('FFFFFF00');

$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A2', 'Cod.Magazzino')
            ->setCellValue('B2', 'Cod. Articolo')
            ->setCellValue('C2', 'Descrizione')
            ->setCellValue('D2', 'UM')
            ->setCellValue('E2', 'Quantita')
            ->setCellValue('F2', 'Giacenza Attuale')
            ->setCellValue('G2', 'Lotto')
            ->setCellValue('H2', 'Lotto Obbligatorio');
$objPHPExcel->getActiveSheet()->getStyle('A2:H2')->getFill()
	->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
	->getStartColor()->setARGB('FF00FFFF');

$connectionstring = db_connect($dbase); 

$Query = "SELECT MAGGIAC.ARTICOLO, MAGART.DESCRIZION, MAGART.LOTTI, MAGART.UNMISURA ";
$Query .= "FROM MAGGIAC INNER JOIN MAGART ON MAGART.CODICE = MAGGIAC.ARTICOLO ";
$Query .= "WHERE MAGGIAC.MAGAZZINO = \"$maga\" AND MAGGIAC.ESERCIZIO=\"$anno\" ORDER BY MAGGIAC.ARTICOLO";
$queryexe = db_query($connectionstring, $Query) or die(mysql_error() ); 
$row = 3;

while($rw = mysql_fetch_object($queryexe)) {
	$art = $rw->ARTICOLO;
	$desc = $rw->DESCRIZION;
	$um = $rw->UNMISURA;
	if($rw->LOTTI > 0) {
		$isLotti = true;
		$Query = "SELECT LOTTO, (PROGQTACAR-PROGQTASCA+PROGQTARET) AS GIACENZA FROM MAGGIACL WHERE MAGAZZINO = \"$maga\" AND ARTICOLO = \"$art\" ORDER BY LOTTO";
		$qe = db_query($connectionstring, $Query) or die(mysql_error() ); 
		while($rs = db_fetch_row($qe)) {
			writeRow($row, $maga, $art, $desc, $um, $rs[1], $rs[0], $isLotti);
			$row++;
		}
	} else {
		$isLotti = false;
		$Query = "SELECT (MAGGIAC.GIACINI+MAGGIAC.PROGQTACAR-MAGGIAC.PROGQTASCA+MAGGIAC.PROGQTARET) AS GIACENZA ";
		$Query .= "FROM MAGGIAC ";
		$Query .= "WHERE MAGGIAC.MAGAZZINO = \"$maga\" AND MAGGIAC.ESERCIZIO=\"$anno\" AND MAGGIAC.ARTICOLO=\"$art\" ";
		$qe = db_query($connectionstring, $Query) or die(mysql_error() ); 
		$rs = db_fetch_row($qe);
		writeRow($row, $maga, $art, $desc, $um, $rs[0], "", $isLotti);
		$row++;
	}
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

// Salvataggio 
require_once '../phpexcel/PHPExcel/IOFactory.php';
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');

function writeRow($row, $maga, $art, $desc, $um, $giac, $lotto, $isLotti) {
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
            ->setCellValue("E$row", 0);
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("F$row", $giac);
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueExplicit("G$row", $lotto, PHPExcel_Cell_DataType::TYPE_STRING);	
	if($isLotti){	
		$objPHPExcel->setActiveSheetIndex(0)
            		->setCellValueExplicit("H$row", "LOTTO OBBLIGATORIO", PHPExcel_Cell_DataType::TYPE_STRING);	
	} else {
		$objPHPExcel->setActiveSheetIndex(0)
            		->setCellValueExplicit("H$row", "", PHPExcel_Cell_DataType::TYPE_STRING);	
	}
	
}
?>