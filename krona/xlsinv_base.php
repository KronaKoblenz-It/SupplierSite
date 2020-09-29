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
/* Copyright (c) 2003-2017 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/
include("header.php"); 
include("db-utils.php");
$maga = trim($_GET['maga']);
include("inv_common.php");
header("Content-Disposition: attachment; filename=\"inventario$mode-$maga-$anno.xls\"");

$codfor = substr($maga, 0, 1) . "0" . substr($maga, 1);
$connectionstring = db_connect($dbase); 
$Query = "SELECT DESCRIZION FROM ANAGRAFE WHERE CODICE='$codfor'";
$queryexe = db_query($connectionstring, $Query) or die(mysql_error() ); 
$rw = mysql_fetch_object($queryexe);
$descfor = $rw->DESCRIZION;

require_once '../phpexcel/PHPExcel.php';
$objPHPExcel = new PHPExcel();
// Set properties
$objPHPExcel->getProperties()->setCreator("KronaKoblenz SpA")
                ->setLastModifiedBy("CED")
                ->setTitle("Inventario$attr $maga $anno")
                ->setSubject("Inventario$attr deposito $maga anno $anno")
                ->setDescription("Inventario$attr deposito $maga anno $anno - $descfor")
                ->setKeywords("inventario$attr, $anno, krona, koblenz, $maga, $descfor")
                ->setCategory("Inventario$attr $maga $anno");
$objPHPExcel->getActiveSheet()->setTitle("Inv$mode-$maga-$anno");

$objPHPExcel->getActiveSheet()->mergeCells('A1:I1');
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
            ->setCellValue('E2', 'Quantita Inventariata')
            ->setCellValue('F2', 'Giacenza TOT. Attuale')
            ->setCellValue('G2', 'COD. LOTTO')
            ->setCellValue('H2', '')
            ->setCellValue('I2', 'VS. CODICE');
$objPHPExcel->getActiveSheet()->getStyle('A2:I2')->getFill()
	->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
	->getStartColor()->setARGB('FF00FFFF');



$whereAttr = "MAGART.NOINVENT = " . ($mode=="attr" ? "1" : "0");
$Query = <<<EOT
SELECT MAGGIAC.ARTICOLO, MAGART.DESCRIZION, MAGART.LOTTI, MAGART.UNMISURA
,CODALT.CODARTFOR
FROM MAGGIAC INNER JOIN MAGART ON MAGART.CODICE = MAGGIAC.ARTICOLO
LEFT OUTER JOIN CODALT ON CODALT.CODICEARTI = MAGGIAC.ARTICOLO 
AND CODALT.CODCLIFOR = '$codfor'
WHERE MAGGIAC.MAGAZZINO = '$maga' AND MAGGIAC.ESERCIZIO='$anno' AND $whereAttr
ORDER BY MAGGIAC.ARTICOLO
EOT;
$queryexe = db_query($connectionstring, $Query) or die(mysql_error() ); 
$row = 3;
/*
while($rw = mysql_fetch_object($queryexe)) {
	$art = $rw->ARTICOLO;
	$desc = $rw->DESCRIZION;
	$um = $rw->UNMISURA;
	if($rw->LOTTI > 0) {
		$isLotti = true;
	} else {
		$isLotti = false;
	}
	$Query = "SELECT (MAGGIAC.GIACINI+MAGGIAC.PROGQTACAR-MAGGIAC.PROGQTASCA+MAGGIAC.PROGQTARET) AS GIACENZA ";
	$Query .= "FROM MAGGIAC ";
	$Query .= "WHERE MAGGIAC.MAGAZZINO = \"$maga\" AND MAGGIAC.ESERCIZIO=\"$anno\" AND MAGGIAC.ARTICOLO=\"$art\" ";
	$qe = db_query($connectionstring, $Query) or die(mysql_error() ); 
	$rs = db_fetch_row($qe);
	writeRow($row, $maga, $art, $desc, $um, $rs[0], "", $isLotti);
	$row++;
}
*/
while($rw = mysql_fetch_object($queryexe)) {
	$art = $rw->ARTICOLO;
	$desc = $rw->DESCRIZION;
	$um = $rw->UNMISURA;
	$codalt = $rw->CODARTFOR;
	$totGiacLotti = 0;
	$isLotti = false;
	if($rw->LOTTI > 0) {
		$isLotti = true;
		$Query = "SELECT LOTTO, (PROGQTACAR-PROGQTASCA+PROGQTARET) AS GIACENZA FROM MAGGIACL WHERE MAGAZZINO = '$maga' AND ARTICOLO = '$art' ORDER BY LOTTO";
		$qe = db_query($connectionstring, $Query) or die(mysql_error() ); 
		while($rs = db_fetch_row($qe)) {
			writeRow($row, $maga, $art, $desc, $um, $rs[1], $rs[0], $isLotti, $codalt);
			$row++;
			$totGiacLotti += $rs[1];
		}
	} 
	$Query = "SELECT (MAGGIAC.GIACINI+MAGGIAC.PROGQTACAR-MAGGIAC.PROGQTASCA+MAGGIAC.PROGQTARET) AS GIACENZA ";
	$Query .= "FROM MAGGIAC ";
	$Query .= "WHERE MAGGIAC.MAGAZZINO = '$maga' AND MAGGIAC.ESERCIZIO='$anno' AND MAGGIAC.ARTICOLO='$art' ";
	$qe = db_query($connectionstring, $Query) or die(mysql_error() ); 
	$rs = db_fetch_row($qe);
	if(!$isLotti || ($rs[0] - $totGiacLotti) > 0) {
		writeRow($row, $maga, $art, $desc, $um, $rs[0], "", $isLotti, $codalt);
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
$objPHPExcel->setActiveSheetIndex(0)->getColumnDimension('I')->setAutoSize(true);

// Salvataggio 
require_once '../phpexcel/PHPExcel/IOFactory.php';
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');

function writeRow($row, $maga, $art, $desc, $um, $giac, $lotto, $isLotti, $codartfor) {
global $objPHPExcel, $mode;
// 15.12.2017 - ROBERTO
// Il fornitore non deve vedere la quantità attuale
$giac = 0;
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueExplicit("A$row", $maga, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueExplicit("B$row", $art, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("C$row", $desc);
	$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue("D$row", $um);		
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue("E$row", $mode=="attr" ? $giac : 0);
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
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueExplicit("I$row", $codartfor, PHPExcel_Cell_DataType::TYPE_STRING);	
	
}
?>