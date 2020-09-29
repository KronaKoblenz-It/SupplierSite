<?php
header('Content-Type: application/vnd.ms-excel');
header('Cache-Control: no-cache');
header('Cache-Control: no-store' , false);     // false => this header not override the previous similar header
header("Connection: Keep-Alive");
header("Keep-Alive: timeout=300");

include("header.php");
include("db-utils.php");
require_once '../phpexcel/PHPExcel.php';
require_once '../phpexcel/PHPExcel/IOFactory.php';

$id_testa = isset($_GET['id']) ? $_GET['id'] : 0;

session_start();

$connectionstring = db_connect($dbase);

$tipodoc = "";
$numdoc = "";
$datadoc = "";
$fornitore = "";
$datadocf = "";
$numerodocf = "";
$magarrivo = "";
$dest = "";

$objPHPExcel = new PHPExcel();
getTesta($id_testa, $tipodoc, $numdoc, $datadoc, $fornitore, $datadocf, $numerodocf, $magarrivo, $dest);
createXLS($objPHPExcel, "$tipodoc_$numdoc");
$row=2;
writeRow($objPHPExcel, "TES", $row, $tipodoc, $numdoc, $datadoc, $fornitore, $datadocf, $numerodocf, $magarrivo, "", "", 0, 0, "", 0, 0, $dest);
getRows($objPHPExcel, $id_testa, $row, $tipodoc, $numdoc, $datadoc, $fornitore, $datadocf, $numerodocf, $magarrivo);
saveXLS($objPHPExcel);

function getTesta($id_testa, &$tipodoc, &$numdoc, &$datadoc, &$fornitore, &$datadocf, &$numerodocf, &$magarrivo, &$dest) {
	global $connectionstring;
	
	$Query = <<<EOT
SELECT DOCTES.TIPODOC, DOCTES.DATADOC, DOCTES.NUMERODOC, DOCTES.DATADOCFOR, DOCTES.NUMERODOCF
, ANAGRAFE.DESCRIZION as RAGSOC, DOCTES.COLLI, DOCTES.PESOLORDO, VETTORI.DESCRIZION as VETTORE, DOCTES.VOLUME, DOCTES.MAGARRIVO 
, DESTINAZ.RAGIONESOC
FROM DOCTES LEFT OUTER JOIN ANAGRAFE ON ANAGRAFE.CODICE = DOCTES.CODICECF LEFT OUTER JOIN VETTORI ON VETTORI.CODICE = DOCTES.VETTORE1
LEFT OUTER JOIN DESTINAZ ON DESTINAZ.CODICEDES = DOCTES.DESTDIV AND DESTINAZ.CODICECF = DOCTES.CODICECF
WHERE DOCTES.ID = $id_testa
EOT;
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
	$magarrivo = $row->MAGARRIVO;
	$dest = $row->RAGIONESOC;
}

function createXLS(&$objPHPExcel, $name) {
	header("Content-Disposition: attachment; filename=\"doc_$name.xls\"");

	// Set properties
	$objPHPExcel->getProperties()->setCreator("KronaKoblenz SpA")
					->setLastModifiedBy("CED")
					->setTitle("Doc_$name")
					->setSubject("Doc_$name")
					->setDescription("Doc_$name")
					->setKeywords("doc $name krona koblenz")
					->setCategory("Doc_$name");
	$objPHPExcel->getActiveSheet()->setTitle("Doc_KronaKoblenz");

	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A1', 'TipoRiga')
				->setCellValue('B1', 'TipoDoc')
				->setCellValue('C1', 'NumDoc')
				->setCellValue('D1', 'DataRegistrazione')
				->setCellValue('E1', 'Fornitore')
				->setCellValue('F1', 'DataSpedizione')
				->setCellValue('G1', 'NumeroDocF')
				->setCellValue('H1', 'Mag.Arrivo')
				->setCellValue('I1', 'Cod.Articolo')
				->setCellValue('J1', 'U.M.')
				->setCellValue('K1', 'Quantità')
				->setCellValue('L1', 'Qta PZ')
				->setCellValue('M1', 'Lotto')
				->setCellValue('N1', 'GestLotto')
				->setCellValue('O1', 'OMAGGIO')
				->setCellValue('P1', 'Destinazione');
}

function getRows($objPHPExcel, $id_testa, &$row, $tipodoc, $numdoc, $datadoc, $fornitore, $datadocf, $numerodocf, $magarrivo) {
	global $connectionstring;
	
	$Query =  "SELECT DOCRIG.CODICEARTI, DOCRIG.UNMISURA, DOCRIG.QUANTITA, DOCRIG.LOTTO, DOCRIG.OMMERCE, ";
	$Query .= "MAGART.LOTTI, MAGART.UNMISURA2, MAGART.FATT2, MAGART.UNMISURA3, MAGART.FATT3 ";
	$Query .= "FROM DOCRIG LEFT OUTER JOIN MAGART ON MAGART.CODICE = DOCRIG.CODICEARTI WHERE DOCRIG.ID_TESTA = $id_testa AND DOCRIG.CODICEARTI!='' ";
	$rs = db_query($connectionstring, $Query) or die(mysql_error());
	while($rw = mysql_fetch_object($rs)) {
		$art = $rw->CODICEARTI;
		$um = $rw->UNMISURA;
		$qta = $rw->QUANTITA;
		$lotto = $rw->LOTTO;
		$isLotti = $rw->lotti;
		$isOmaggio = $rw->OMMERCE;
		$qtaPZ = ($rw->UNMISURA!='PZ' ? ($rw->UNMISURA2!='PZ' ? ($rw->UNMISURA3!='PZ' ? "" : ($rw->QUANTITA/$rw->FATT3) ) : ($rw->QUANTITA/$rw->FATT2) ) : "");
		$row++;

		writeRow($objPHPExcel, "RIG", $row, $tipodoc, $numdoc, $datadoc, $fornitore, $datadocf, $numerodocf, $magarrivo, $art, $um, $qta, $qtaPZ, $lotto, $isLotti, $isOmaggio, "");
	}
}

function saveXLS(&$objPHPExcel) {
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

	// Salvataggio
	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');
}

function writeRow(&$objPHPExcel, $type, $row, $tipodoc, $numdoc, $datadoc, $fornitore, $datadocf, $numerodocf, $magarrivo, $art, $um, $qta,$qtaPZ, $lotto, $isLotti, $isOmaggio, $dest) {
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
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueExplicit("G$row", $numerodocf, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueExplicit("H$row", $magarrivo, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueExplicit("I$row", $art, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueExplicit("J$row", $um, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueExplicit("K$row", $qta, PHPExcel_Cell_DataType::TYPE_NUMERIC);
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueExplicit("L$row", xRound2($qtaPZ), PHPExcel_Cell_DataType::TYPE_NUMERIC);
	$objPHPExcel->setActiveSheetIndex(0)
      		->setCellValueExplicit("M$row", $lotto, PHPExcel_Cell_DataType::TYPE_STRING);
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueExplicit("N$row", ($lotto!="" ? 1 : 0), PHPExcel_Cell_DataType::TYPE_NUMERIC);
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValueExplicit("O$row", ($isOmaggio ? 1 : 0), PHPExcel_Cell_DataType::TYPE_NUMERIC);
	$objPHPExcel->setActiveSheetIndex(0)
      		->setCellValueExplicit("P$row", $dest, PHPExcel_Cell_DataType::TYPE_STRING);
		
}
?>