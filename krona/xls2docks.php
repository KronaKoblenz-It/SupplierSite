<?php
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2018 by Roberto Ceccarelli                        */
/* http://strawberryfield.altervista.org								*/
/*                                                                      */
/************************************************************************/

include("header.php");
include("db-utils.php");
include("../libs/distbase.php");
require_once '../phpexcel/PHPExcel/IOFactory.php';

session_start();
$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
$fornitore = $cookie[0];
$maga = "S" . substr($fornitore, -4);
define("UPLOAD_DIR", "./uploads/_ks/".$fornitore."/");

$conn = db_connect($dbase);
$anno = current_year();
//isset($_GET['file']) ? $_GET['file'] : isset($_POST['file']) ? $_POST['file'] : '';
$nameFile = isset($_GET['file']) ? $_GET['file'] : (isset($_POST['file']) ? $_POST['file'] : '');
//print($nameFile);

if ($nameFile !== ""){
	//print(UPLOAD_DIR.$nameFile);
	$file = UPLOAD_DIR.$nameFile;
} else {
	$file = $_FILES["file"]["tmp_name"];
}

//VARIABILI XLS
$C_ID_DOC 	= 0;
$C_CLIFOR 	= 1;
$C_DATAREG 	= 2;
$C_NUMREG 	= 3;
$C_COD		= 5;
//$C_DESC	= 3;
$C_LOTTO	= 6;
$C_UM		= 7;
$C_QTA		= 8;

//VARIABILI DI ERRORE
$ERR_COD 	= 0;
$ERR_LOTTO 	= 0;
$ERR_GIAC	= 0;
$ERR_QTA	= 0;

//VARIABILI di DATI

//TABELLA
$k=0;

head();
banner("Importazione file Excel Sfridi");

$err = false;
if ($file["error"] > 0) {
   echo "Errore: " . $file["error"] . "<br />";
   $err = true;
} else {
//	   echo "Upload: " . $_FILES["file"]["name"] . "<br />";
//	   echo "Type: " . $_FILES["file"]["type"] . "<br />";
//	   echo "Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br />";
//	   echo "Stored in: " . $_FILES["file"]["tmp_name"];
}

if(!$err) {
//$file = $_FILES["file"]["tmp_name"];
//$contents = file($file);
//$string = implode($contents);


	$objPHPExcel = PHPExcel_IOFactory::load($file);
	foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
		$worksheetTitle     = $worksheet->getTitle();
		$highestRow         = $worksheet->getHighestRow(); // e.g. 10
		$highestColumn      = $worksheet->getHighestColumn(); // e.g 'F'
		$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
		$nrColumns = ord($highestColumn) - 64;

		$lastbolla = "inizio";
		$isInizio = false;
		$tipodoc = "SK";
		$id_testa = (time() % 10000000) * 100;
		$id = ($id_testa % 1000000)*1000;
		$rifOrd = "";
		$tipoRifOrd = "";
		$numRifOrd = 0;
		$rifRiga = 0;

		for ($row = 2; $row <= $highestRow; ++ $row) {

			$ERR_COD 	= 0;
			$ERR_LOTTO 	= 0;
			$ERR_GIAC	= 0;
			$ERR_QTA	= 0;

			$idDocF = $worksheet->getCellByColumnAndRow($C_ID_DOC, $row)->getValue();
			$numReg = $worksheet->getCellByColumnAndRow($C_NUMREG, $row)->getValue();

			//scrittura testa (se serve)
			if($lastbolla != ($idDocF .'-'. $numReg)) {
				if($isInizio){
					print("</br><div style=\"text-align: center;\"><span id=\"Title$k\"><b>Caricato correttamente! </b></span></div> </br>\n");
				}
				print("</br><div style=\"text-align: center;\"><span id=\"Title$k\"><bBolla con Data Registrazione " . date("d-m-Y") . "</b></span></div> </br>\n");
				$isInizio = true;

				$id_testa++;

				$Query = "INSERT INTO U_BARDT ";
				$Query .= "(ID, DATADOC, ESERCIZIO, CODICECF, TIPODOC, NUMERODOC, NUMERODOCF, MAGPARTENZ, MAGARRIVO, RIF_ID, RIF_TIPODOC, RIF_NUMERODOC, RIF_DATADOC, DEL) VALUES ( ";
				$Query .= "$id_testa, ";
				$Query .= "'" . date("Y-m-d") . "', '" . date("Y") . "', ";
				$Query .= "\"$fornitore\", ";
				$Query .= "\"$tipodoc\", \"\", \"$bollaNum\", ";
				$Query .= "\"$maga\", \"SC\", 0, \"$tipoRifOrd\", \"$numRifOrd\", \"$dataRifOrd\", 0 )";

				$rs = db_query($conn, $Query) or die(mysql_error());


				$lastbolla = $idDocF .'-'. $numReg;
			}

			$art = trim($worksheet->getCellByColumnAndRow($C_COD, $row)->getValue());
			//$descArt = trim($worksheet->getCellByColumnAndRow($C_DESC, $row)->getValue());
			$qta = $worksheet->getCellByColumnAndRow($C_QTA, $row)->getValue();
			$lotto = trim($worksheet->getCellByColumnAndRow($C_LOTTO, $row)->getValue());
			$um = trim($worksheet->getCellByColumnAndRow($C_UM, $row)->getValue());

			//print($art);
			//CERCO DESCRIZIONE & UM
			$Query = "SELECT DESCRIZION, UNMISURA, LOTTI FROM MAGART WHERE MAGART.CODICE = \"$art\"";
			$rs = db_query($conn, $Query) or die(mysql_error());
			if (mysql_num_rows($rs)==0) {
				$descArt =  $art . " Sembra non essere presente nell'Angrafica Articoli";;
				$ERR_COD = 1;
			} else {
				$rw = db_fetch_row($rs);
				$descArt = trim($rw[0]);
				if ($um == 'N.'){
					$um = $rw[1];
				}
				$isLotto = $rw[2];
			}

			if (!$ERR_COD){
				//IDENTIFICO SE E' A LOTTO
				if ($isLotto && $lotto==''){
					$ERR_LOTTO = 1;
				}

				if (!$ERR_LOTTO){
					//CERCO GIACENZA RESIDUA
					if ($isLotto){
						$q = "SELECT PROGQTACAR-PROGQTASCA+PROGQTARET AS GIACENZA, LOTTO FROM MAGGIACL ";
						$q .= "WHERE ARTICOLO = \"" . $art . "\" ";
						$q .= "AND MAGAZZINO = \"$maga\" AND LOTTO = \"$lotto\" ";
						$q .= "ORDER BY LOTTO DESC ";
						$rs2 = db_query($conn, $q) or die(mysql_error());
						if($rwg = mysql_fetch_object($rs2)) {
							$varGiac=$rwg->GIACENZA;
						} else {
							$varGiac = 0;
							$ERR_GIAC = 1;
						}
					} else {
						$q = "SELECT GIACINI+PROGQTACAR-PROGQTASCA+PROGQTARET AS GIACENZA FROM MAGGIAC ";
						$q .= "WHERE ARTICOLO = \"" . $art. "\" ";
						$q .= "AND MAGAZZINO = \"$maga\" ";
						$q .= "AND ESERCIZIO = \"$anno\" ";
						$rg = db_query($conn, $q) or die(mysql_error());
						$varGiac=0;
						if($rwg = mysql_fetch_object($rg)) {
							$varGiac=$rwg->GIACENZA;
						} else {
							$varGiac = 0;
							$ERR_GIAC = 1;
						}
					}

					if (!$ERR_GIAC){
						if ($varGiac < $qta){
							$ERR_QTA = 1;
						}
					}
				}
			}

			$id++; //valorizzo id riga

			//VALORIZZO ERRORE FINALE
			if(!$err){
				if( $ERR_COD || $ERR_QTA || $ERR_LOTTO || $ERR_GIAC ){
					$err = true;
				}
			}

			if(!$err){
				$Query = "INSERT INTO U_BARDR ";
				$Query .= "(ID, ID_TESTA, ESPLDISTIN, DATADOC, CODICECF, TIPODOC, CODICEARTI, DESCRIZION, QUANTITA, LOTTO, NUMERODOC, MAGPARTENZ, MAGARRIVO, RIFFROMT, RIFFROMR, U_CLIVEN, DEL) VALUES ( ";
				$Query .= "$id, ";
				$Query .= "$id_testa, ";
				$Query .= "\" \", ";
				$Query .= "'" . date("Y-m-d") . "', ";
				$Query .= "\"$fornitore\", ";
				$Query .= "\"$tipodoc\", ";
				$Query .= "\"$art\", ";
				$Query .= "\"" . str_replace('"', '',$descArt) . "\", ";
				$Query .= $qta . ", ";
				$Query .= "\"$lotto\", ";
				$Query .= '"", ';
				$Query .= "\"$maga\", \"SC\", ";
				$Query .= "0, 0, ";
				$Query .= "\"C\", ";
				$Query .= " 0 )";

				$rs = db_query($conn, $Query) or die(mysql_error());

				webMovs::insWebMov($id_testa, $id, $id, $tipodoc, date('Y-m-d'), $art, $lotto, $qta, $maga, 'SC', $bollaNum);

			} else {
				print("<br><div style=\"text-align: center;\"><b>Errore nel caricamento DDT su Articolo: ". $art . " e Lotto: " . $lotto ."</b></div>\n");
				$Query = "DELETE FROM U_BARDR WHERE ID_TESTA = $id_testa";
				$rs = db_query($conn, $Query) or die(mysql_error());
				$Query = "DELETE FROM U_BARDT WHERE ID = $id_testa";
				$rs = db_query($conn, $Query) or die(mysql_error());

				$row = $highestRow;
			}

		}

	}

}



if( $err ) {
	print("<br><div style=\"text-align: center;\"><b>Errore nel caricamento. Procedura Interrotta. Si prega di correggere gli errori e riprovare a caricare o contattare il Ced Krona Koblenz.</b></div>\n");

} else {
	print("<br>FINE.\n");
}
print("<br>\n<br>\n");
print("<a href=\"ksimportxls.php\">");
print("<img border=\"0\" src=\"../img/05_edit.gif\" alt=\"Nuovo caricamento\">Nuovo caricamento</a>\n");

header('Refresh: 5; URL=ddttoload.php');

print("<br>\n");
goMain();
footer();


// FUNZIONI UTILI

/*function in_arrayMulti($value, $array, $campo)
{
	$trovato = false;
	foreach ($array as $key => $val) {
	   if ($val[$campo] == $value) {
			$trovato=true;
			return $key;
	   }
   }
   if (!$trovato){
		return -1;
   }
}*/

function in_arrayMulti($value, $array, $campo, $aKeyC)
{
	$trovato = false;
	$i=0;
	foreach ($array as $key => $val) {
	   if ($val[$campo] == $value) {
			$trovato=true;
			$aKeyC[$i]=$key;
			$i++;
	   }
	}
   if (!$trovato){
		return -1;
   } else {
		$n=count($aKeyC);
		return $n;
   }
}

function in_arrayMulti_L($value, $array, $campo, $aKeyL)
{
	$trovato = false;
	$i=0;
	foreach ($array as $key => $val) {
	   if ($val[$campo] == $value) {
			$trovato=true;
			$aKeyL[$i]=$key;
			$i++;
	   }
	}
   if (!$trovato){
		return -1;
   } else {
		$n=count($aKeyL);
		return $n;
   }
}

function chiudiTabella(){
	print("</tbody>\n</table>\n");
}

?>
