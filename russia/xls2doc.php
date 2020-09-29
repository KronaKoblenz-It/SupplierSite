<?php 

include("header.php"); 
include("db-utils.php");
include("../libs/distbase.php");
require_once '../phpexcel/PHPExcel/IOFactory.php';
print("<script type=\"text/javascript\" src=\"./xlstotbl_util.js\"></script>\n");

session_start();
$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
$fornitore = $cookie[0];
$maga = "F" . substr($fornitore, -4);
$date = date("d_m_y", time());
define("UPLOAD_DIR", "./uploads/".$date."/");

$conn = db_connect($dbase); 
$anno = current_year();

#--PARAMETRI ARRIVATI IN POST/GET
$nameFile = isset($_GET['file']) ? $_GET['file'] : (isset($_POST['file']) ? $_POST['file'] : '');
$valuta = 'RUR';
$cambio = 60;
$datadoc = date("Y-m-d", strtotime('2015-11-30'));
$eserc = '2015';
$datadoc2 = isset($_GET['datepicker']) ? $_GET['datepicker'] : (isset($_POST['datepicker']) ? $_POST['datepicker'] : '');
print($datadoc2);

if ($nameFile !== ""){
	$file = UPLOAD_DIR.$nameFile;
} else {
	$file = $_FILES["file"]["tmp_name"];
}
//print($file);
#--VARIABILI XLS
$EMPTY_1 		= 0;
$DESCR_RUS 		= 1;
$DESCR_ENG 		= 2;
$COD_ART		= 3;
$UM_ART			= 4;
$QTA_ART		= 5;
$FATT_ART	 	= 6;
$FATT_IVA_ART	= 7;
$COST_ART		= 8;

#--VARIABILI DI ERRORE
$ERR_ART_P = 0;
$ERR_ART_C = 0;
$ERR_QTA_C = 0;
$ERR_LOT_C = 0;
$ERR_GIAC = 0;
$ERR_RIF_ORD = 0;
$ERR_DIST = 0;

$k=0;

head();
banner("Importazione file Excel");

$err = false;

if ($file["error"] > 0) {
   echo "Errore: " . $file["error"] . "<br />";
   $err = true;
}

if(!$err) {
	#--INIZIO LETTURA FILE EXCEL
	$objPHPExcel = PHPExcel_IOFactory::load($file);
	foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
		$worksheetTitle     = $worksheet->getTitle();
		$highestRow         = $worksheet->getHighestRow(); // e.g. 10
		$highestColumn      = $worksheet->getHighestColumn(); // e.g 'F'
		$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
		$nrColumns = ord($highestColumn) - 64;
		
		$lastart = "inizio";
		$isInizio = false;
		$id_testa_FT = (time() % 10000000) * 100;
		$id_testa_FF = $id_testa_FT++;
		
		//Variabili Statiche
		$magPartenza = '00001';
		$magArrivo = '00001';
		$cliente = 'C00001';
		$fornitore = 'F01021';
		$docVen = 'FT';
		$docAcq = 'FF';
		$rifFromT = 0;
		$rifFromR = 0;
		$nDocF = 0;
		$rifId = 0;
		$rifTipoDoc = 0;
		$rifNumDoc = 0;
		$rifDataDoc = 0;
		$numerodoc = "DOC ".$datadoc;
		
		#--SCRIVO TESTA FT -- VENDITA
		$id_FT = scriviTesta($id_testa_FT, $datadoc, $docVen, $cliente, $numerodoc, $magPartenza, $magArrivo, $eserc, $nDocF, $valuta, $cambio, $rifId, $rifTipoDoc, $rifNumDoc, $rifDataDoc);
		#--SCRIVO TESTA FF -- ACQUISTO
		$id_FF = scriviTesta($id_testa_FF, $datadoc, $docAcq, $fornitore, $numerodoc, $magPartenza, $magArrivo, $eserc, $nDocF, $valuta, $cambio, $rifId, $rifTipoDoc, $rifNumDoc, $rifDataDoc);
		
		for ($row = 9; $row <= $highestRow; ++ $row) {
				
			$codArt = $worksheet->getCellByColumnAndRow($COD_ART, $row)->getValue();
			$descrR = $worksheet->getCellByColumnAndRow($DESCR_RUS, $row)->getValue();
			$descrE = $worksheet->getCellByColumnAndRow($DESCR_ENG, $row)->getValue();
			$qtaArt = $worksheet->getCellByColumnAndRow($QTA_ART, $row)->getValue();
			$umArt = $worksheet->getCellByColumnAndRow($UM_ART, $row)->getValue();
			$valArt = $worksheet->getCellByColumnAndRow($FATT_ART, $row)->getValue();
			$valIvaArt = $worksheet->getCellByColumnAndRow($FATT_IVA_ART, $row)->getValue();
			$costArt = $worksheet->getCellByColumnAndRow($COST_ART, $row)->getValue();
			$fattArt = 1;
			
			if($qtaArt == ""){
				$qtaArt = 0;
			}
			if($valArt == ""){
				$valArt = 0;
			}
			if($costArt == ""){
				$costArt = 0;
			}
			
			#--CONVERTO Unità Misura
			if (strpos($umArt, 'шт') !== FALSE)	{
				$umArt = 'PZ';
			} else if (strpos($umArt, 'упак') !== FALSE) {
				$umArt = 'CF';
			} else if (strpos($umArt, 'к-т') !== FALSE) {
				$umArt = 'PZ';  //'KIT';
			} else if (strpos($umArt, 'пог. м') !== FALSE) {
				$umArt = 'MT';  //MT Lineare
			} else if (strpos($umArt, 'м') !== FALSE) {
				$umArt = 'MT';
			}
			
			#--AGGIUSTO CODART
			$codArt = str_replace('х', 'X', $codArt);
			$codArt = str_replace('А', 'A', $codArt);
			$codArt = str_replace('В', 'B', $codArt);
			$codArt = str_replace('К', 'K', $codArt);
			$codArt = str_replace('DXCX', 'DXSX', $codArt);
			$codArt = str_replace('БЕЛАЯ', 'BIANCO', $codArt);
			$codArt = str_replace('Л', '', $codArt);
			
			$codArt = mb_strtoupper($codArt, 'UTF-8');
			if ($codArt=="EXP K 1000 CS DXSX")
			{
				$codArt = "CAMP.K 1000 CS";
			}
			if ($codArt.trim() == "" && $umArt.trim() != ""){
				$codArt = "0500 1 GR. CURV.";
			}
			if ($codArt.trim() == "A 804 5,2") {
				$codArt = "0500 520 GR.";
			}
			if ($codArt.trim() == "A 804 5,2 A") {
				$codArt = "0500 520";
			}
			if ($codArt.trim() == "A 804 5,4") {
				$codArt = "0500 540 GR.";
			}
			if ($codArt.trim() == "A 804 6,0") {
				$codArt = "0500 1 GR.";
			}
			if ($codArt.trim() == "A 804 6,0 A") {
				$codArt = "0500 1";
			}
			if ($codArt.trim() == "A 914 6,0") {
				$codArt = "0400 2G";
			}
			if ($codArt.trim() == "A 915 6,0") {
				$codArt = "3600 1 GR.";
			}
			if ($codArt.trim() == "DX1369") {
				$codArt = "1700 2";
			}
			if ($codArt.trim() == "0910 7 БЕЛАЯ") {
				$codArt = "0910 7 BIANCO";
			}
			#--END

			#--INIZIO INSERIMENTO RIGHE
			if ($codArt.trim() != ""){
				$Query = "SELECT MAGART.CODICE, MAGART.GRUPPO, ";
				$Query .= "MAGART.UNMISURA1, MAGART.FATT1, ";
				$Query .= "MAGART.UNMISURA2, MAGART.FATT2, ";
				$Query .= "MAGART.UNMISURA3, MAGART.FATT3 ";
				$Query .= "FROM MAGART WHERE MAGART.CODICE=\"$codArt\"";
				$rs = db_query($conn, $Query) or die(mysql_error()); 
				$rw = db_fetch_row($rs);
				if (mysql_num_rows($rs)!=0)	{							#--RIGHE RICONOSCIUTE
				
					$codGruppo = trim($rw[1]);
					//Cerco Unità di Misura e Fattore di Conversione
					if($umArt == trim($rw[2])){
						$fattArt = $rw[3];
					} else if ($umArt == trim($rw[4])) {
						$fattArt = $rw[5];
					} else if ($umArt == trim($rw[6])) {
						$fattArt = $rw[7];
					}
					
					if( $descrE == "") {
						$q1 = "SELECT DESCRIZION FROM MAGLANG WHERE CODICEARTI =\"$codArt\" AND CODLINGUA = \"UK\"";
						$rs2 = db_query($conn, $q1) or die(mysql_error());
						$rw2 = db_fetch_row($rs2);
						$descrE = $rw2[0];
					}
					
					if( $descrE == "") {
						$descrE = "-- NO DESCRIPTION --";
					}
					
					#--SCRIVO RIGA FT -- VENDITA
					$id_FT = scriviRiga($id_FT, $id_testa_FT, $datadoc, $docVen, $cliente, $codArt, $descrE, '', $qtaArt, $valArt, '', $numerodoc, $magPartenza, $magArrivo, $rifFromT, $rifFromR, $umArt, $fattArt);
					#--SCRIVO RIGA FF -- ACQUISTO
					if ($costArt != "" && $costArt != 0){
						$id_FF = scriviRiga($id_FF, $id_testa_FF, $datadoc, $docAcq, $fornitore, $codArt, $descrE, '', $qtaArt, $costArt, '', $numerodoc, $magPartenza, $magArrivo, $rifFromT, $rifFromR, $umArt, $fattArt);
					}
					
				} else if ((strpos($codArt, 'Л') !== FALSE)) {			#--RIGHE SPECIALI
					$codGruppo = "B0102";
					$codArt = str_replace('Л', '', $codArt);
					
					if( $descrE == "") {
						$descrE = "-- NO DESCRIPTION --";
					}
					
					#--SCRIVO RIGA FT -- VENDITA
					$id_FT = scriviRiga($id_FT, $id_testa_FT, $datadoc, $docVen, $cliente, $codArt, $descrE, '', $qtaArt, $valArt, '', $numerodoc, $magPartenza, $magArrivo, $rifFromT, $rifFromR, $umArt, $fattArt);
					#--SCRIVO RIGA FF -- ACQUISTO
					if ($costArt != "" && $costArt != 0){
						$id_FF = scriviRiga($id_FF, $id_testa_FF, $datadoc, $docAcq, $fornitore, $codArt, $descrE, '', $qtaArt, $costArt, '', $numerodoc, $magPartenza, $magArrivo, $rifFromT, $rifFromR, $umArt, $fattArt);
					}
					
				} else {												#--RIGHE IGNOTE
					$codGruppo = "";
					
					if( $descrE == "") {
						$descrE = "-- NO DESCRIPTION --";
					}
					
					#--SCRIVO RIGA FT -- VENDITA
					$id_FT = scriviRiga($id_FT, $id_testa_FT, $datadoc, $docVen, $cliente, $codArt, $descrE, '', $qtaArt, $valArt, '', $numerodoc, $magPartenza, $magArrivo, $rifFromT, $rifFromR, $umArt, $fattArt);
					#--SCRIVO RIGA FF -- ACQUISTO
					if ($costArt != "" && $costArt != 0){
						$id_FF = scriviRiga($id_FF, $id_testa_FF, $datadoc, $docAcq, $fornitore, $codArt, $descrE, '', $qtaArt, $costArt, '', $numerodoc, $magPartenza, $magArrivo, $rifFromT, $rifFromR, $umArt, $fattArt);
					}
				}
			}
			if ($row == $highestRow){
				print("Fatto!");
			}
			
		}
		
	}

}

function scriviRiga($id, $id_testa, $datadoc, $tipodoc, $codicecf, $codicearti, $descrizion, $lotto, $qta, $prezzo, $espldistin, $numerodoc, $magpartenza, $magarrivo, $rift, $rifr, $um, $fatt) {

	global $conn;

	$Query = "INSERT INTO U_BARDR ";
	$Query .= "(ID, ID_TESTA, DATADOC, TIPODOC, CODICECF, CODICEARTI, DESCRIZION, LOTTO, QUANTITA, PREZZOTOT, ESPLDISTIN, NUMERODOC, MAGPARTENZ, MAGARRIVO, RIFFROMT, RIFFROMR, UNMISURA, FATT, DEL) VALUES ( ";
	$Query .= "$id, ";
	$Query .= "$id_testa, ";
	$Query .= "\"$datadoc\", ";
	$Query .= "\"$tipodoc\", ";
	$Query .= "\"$codicecf\", ";
	$Query .= "\"$codicearti\", ";
	$Query .= "\"" . str_replace('"', '""', $descrizion) . "\", ";
	$Query .= "\"$lotto\", ";
	$Query .= "$qta, ";
	$Query .= "$prezzo, ";
	$Query .= "\"$espldistin\", ";
	$Query .= "\"$numerodoc\", ";
	$Query .= "\"$magpartenza\", \"$magarrivo\", ";
	$Query .= "$rift, ";
	$Query .= "$rifr, ";
	$Query .= "\"$um\", ";
	$Query .= "$fatt, ";
	$Query .= " 0 )";
	print($Query."<br>");
	$rs = db_query($conn, $Query) or die(mysql_error()); 
	
	return $id + 1;  
}

	
function scriviTesta($id_testa, $datadoc, $tipodoc, $codicecf, $numerodoc, $magpartenza, $magarrivo, $esercizio, $nDocF, $valuta, $cambio, $rifId, $rifTipoDoc, $rifNumDoc, $rifDataDoc) {

	global $conn;
	
	$Query = "INSERT INTO U_BARDT ";
	$Query .= "(ID, DATADOC, TIPODOC, CODICECF, NUMERODOC, MAGPARTENZ, MAGARRIVO, ESERCIZIO, NUMERODOCF, VALUTA, CAMBIO, RIF_ID, RIF_TIPODOC, RIF_NUMERODOC, RIF_DATADOC, DEL ) VALUES ( ";
	$Query .= "$id_testa, ";
	$Query .= "\"$datadoc\", ";
	$Query .= "\"$tipodoc\", ";
	$Query .= "\"$codicecf\", ";
	$Query .= "\"$numerodoc\", ";
	$Query .= "\"$magpartenza\", \"$magarrivo\", \"$esercizio\", ";
	$Query .= "$nDocF, ";
	$Query .= "\"$valuta\", ";
	$Query .= "$cambio, ";
	$Query .= "$rifId, ";
	$Query .= "\"$rifTipoDoc\", ";
	$Query .= "$rifNumDoc, ";
	$Query .= "$rifDataDoc, ";
	$Query .= " 0 )";
	//print($Query."<br>");
	$rs = db_query($conn, $Query) or die(mysql_error()); 
	
	return ($id_testa % 1000000)*1000;
}

?>