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
$tipodoc = strtoupper(isset($_GET['tipodoc']) ? $_GET['tipodoc'] : (isset($_POST['tipodoc']) ? $_POST['tipodoc'] : ''));
$mode = strtoupper(isset($_GET['mode']) ? $_GET['mode'] : (isset($_POST['mode']) ? $_POST['mode'] : ''));

if($tipodoc == "KS") {
	$maga = "S" . substr($fornitore, -4);
	$magarrivo = "SC";
} else {
	$maga = "F" . substr($fornitore, -4);
	$magarrivo = "S" . substr($fornitore, -4);
}

define("UPLOAD_DIR", "./uploads/_$tipodoc/".$fornitore."/");

$conn = db_connect($dbase); 
$anno = current_year();
$data = date("Y-m-d");
$nameFile = isset($_GET['file']) ? $_GET['file'] : (isset($_POST['file']) ? $_POST['file'] : '');

if ($nameFile !== ""){
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
if($tipodoc == "KS") {
	banner("Importazione file Excel Sfridi");
} else {
	banner("Importazione file Excel Resi non Lavorati");
}


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
				if($mode == "TEST") {
					if($isInizio){
						chiudiTabella();
						$k++;
					}
					$j=0;
				} else {
					if($isInizio){
						print("</br><div style=\"text-align: center;\"><span id=\"Title$k\"><b>Caricato correttamente! </b></span></div> </br>\n");
					}		
				}
				//print($lastbolla);
				//TESTA DOC
				print("</br><div style=\"text-align: center;\"><span id=\"Title$k\"><b>Bolla con Data Registrazione " . date("d-m-Y") . "</b></span></div> </br>\n");
				
				$isInizio = true;
				$id_testa++;
				
				if($mode == "TEST") {
					print("<table id=\"tbl$k\" class=\"list\" style=\"width:80%; style=margin-left:auto; margin-right:auto\"  align=\"center\">\n");
					print("<thead>\n<tr class=\"list\">\n");
					print("<th class=\"list\" style=\"width:18%;\">Articolo</th>\n");
					print("<th class=\"list\">Descrizione</th>\n");
					print("<th class=\"list\" style=\"width:4%;\">U.M.</th>\n");
					print("<th class=\"list\" style=\"width:8%;\">Qta</th>\n");
					print("<th class=\"list\" style=\"width:10%;\">Lotto</th>\n");
					print("<th class=\"list\" style=\"width:10%;\">Giacenza</th>\n");
					print("<th class=\"list\" style=\"width:10%;\">Dettagli</th>\n");
					print("</tr>\n</thead>\n");
					print("<tbody id=\"tblbody\">\n");
				} else {
					$Query = <<<EOT
INSERT INTO U_BARDT 
(ID, DATADOC, ESERCIZIO, CODICECF, TIPODOC, NUMERODOC, NUMERODOCF, MAGPARTENZ, MAGARRIVO, RIF_ID, RIF_TIPODOC, RIF_NUMERODOC, RIF_DATADOC, DEL) 
VALUES ( 
$id_testa, 
'$data', '$anno', 
'$fornitore', 
'$tipodoc', '', '$bollaNum', 
'$maga', '$magarrivo', 0, '$tipoRifOrd', '$numRifOrd', '$dataRifOrd', 0 )
EOT;
					$rs = db_query($conn, $Query) or die(mysql_error());
				}
				
				$lastbolla = $idDocF .'-'. $numReg;
			}			

			$art = trim($worksheet->getCellByColumnAndRow($C_COD, $row)->getValue());
			//$descArt = trim($worksheet->getCellByColumnAndRow($C_DESC, $row)->getValue());
			$qta = $worksheet->getCellByColumnAndRow($C_QTA, $row)->getValue();
			$lotto = trim($worksheet->getCellByColumnAndRow($C_LOTTO, $row)->getValue());
			$um = trim($worksheet->getCellByColumnAndRow($C_UM, $row)->getValue());
			
			//print($art);
			//CERCO DESCRIZIONE & UM
			$Query = "SELECT DESCRIZION, UNMISURA, LOTTI FROM MAGART WHERE MAGART.CODICE = '$art'";
			$rs = db_query($conn, $Query) or die(mysql_error());
			if (mysql_num_rows($rs)==0) {
				$descArt =  $art . " Sembra non essere presente nell'Angrafica Articoli";;
				$ERR_COD = 1;
			} else {
				$rw = db_fetch_row($rs);
				$descArt = str_replace('"', '', trim($rw[0]));
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
						$q = <<<EOT
SELECT PROGQTACAR-PROGQTASCA+PROGQTARET AS GIACENZA, LOTTO FROM MAGGIACL 
WHERE ARTICOLO = '$art'
AND MAGAZZINO = '$maga' AND LOTTO = '$lotto'
ORDER BY LOTTO DESC
EOT;
						$rs2 = db_query($conn, $q) or die(mysql_error()); 
						if($rwg = mysql_fetch_object($rs2)) {
							$varGiac=$rwg->GIACENZA;
						} else {
							$varGiac = 0;
							$ERR_GIAC = 1;
						}
					} else {
						$q = <<<EOT
SELECT GIACINI+PROGQTACAR-PROGQTASCA+PROGQTARET AS GIACENZA FROM MAGGIAC 
WHERE ARTICOLO = '$art'
AND MAGAZZINO = '$maga'
AND ESERCIZIO = '$anno'
EOT;
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
			
			if($mode == "TEST") {
				// scrivo la riga a monitor	
				print("<tr class=\"list\" id=\"riga$j\">\n");
				if($ERR_COD){
					print("<td class=\"error\" style=\"text-align:center;\"><span id=\"code$j\">$art</span></td>\n");
					print("<td class=\"list\"><span id=\"desc$j\" style=\"font-size: 9pt; color: red;\"><b>$descArt</b></span></td>\n");
				} else {
					print("<td class=\"list\" style=\"text-align:center;\"><span id=\"code$j\">$art</span></td>\n");
					print("<td class=\"list\"><span id=\"desc$j\">$descArt</span></td>\n");
				}
				print("<td class=\"list\"><span id=\"um$j\">" . $um . "</span></td>\n");
				
				if($ERR_QTA){
					print("<td class=\"error\"><span id=\"qta$j\">" . number($qta) . "</span></td>\n");
				} else {
					print("<td class=\"list\"><span id=\"qta$j\">" . number($qta) . "</span></td>\n");
				}
				
				if($ERR_LOTTO){
					print("<td class=\"error\"><span id=\"lotto$j\">$lotto</span></td>\n");
				}else{
					print("<td class=\"list\"><span id=\"lotto$j\">$lotto</span></td>\n");
				}
				
				if($ERR_GIAC){
					print("<td class=\"error\"><span id=\"giac$j\">$varGiac</span></td>\n");
				}else{
					print("<td class=\"list\"><span id=\"giac$j\">$varGiac</span></td>\n");
				}
							
				if( $ERR_COD || $ERR_QTA || $ERR_LOTTO || $ERR_GIAC ){
					if( $ERR_COD ){
						print("<td class=\"error\"><span id=\"detail$j\">Codice non trovato in Anagrafica Articoli!</span></td>\n");
					}
					if( $ERR_QTA ){
						print("<td class=\"error\"><span id=\"detail$j\">Qta Maggiore a Giacenza. Controllare Movimenti <br/> -> (<a href=\"http://intranet.krona.it/krona/giacArtDetail.php?art=". $art ."&maga=".$maga."&esercizio=".$anno."\" target='_blank' title='Movimenti'>clicca qui</a>)</span></td>\n");
					}	
					if( $ERR_LOTTO ){
						print("<td class=\"error\"><span id=\"detail$j\">Codice Lotto non Trovato!</span></td>\n");
					}	
					if( $ERR_GIAC ){
						print("<td class=\"error\"><span id=\"detail$j\">Giacenza Nulla...Contattare Amministratore!</span></td>\n");
					}	
				} else {
					print("<td class=\"list\"><span id=\"detail$j\"></span></td>\n");
				}
							
				echo "</tr>\n";
				$j++;
				
				//VALORIZZO ERRORE FINALE
				if(!$err){
					if( $ERR_COD || $ERR_QTA || $ERR_LOTTO || $ERR_GIAC ){
						$err = true;
					}				
				}
				
				if ($row == $highestRow){
					chiudiTabella();
				}
			} else {
				// scrittura dati
				//VALORIZZO ERRORE FINALE
				if(!$err){
					if( $ERR_COD || $ERR_QTA || $ERR_LOTTO || $ERR_GIAC ){
						$err = true;
					}
				}

				if(!$err){
					$Query = <<<EOT
INSERT INTO U_BARDR 
(ID, ID_TESTA, ESPLDISTIN, DATADOC, CODICECF, TIPODOC, CODICEARTI, DESCRIZION, QUANTITA, LOTTO, NUMERODOC, MAGPARTENZ, MAGARRIVO, RIFFROMT, RIFFROMR, U_CLIVEN, DEL) 
VALUES ( 
$id, 
$id_testa, 
' ', 
'$data', 
'$fornitore', 
'$tipodoc', 
'$art', 
'$descArt', 
$qta, 
'$lotto', 
'', 
'$maga', '$magarrivo', 
0, 0, 
'C', 
0 )
EOT;
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
}

if($mode == "TEST") {
	if( $err ) {
		print("<br><div style=\"text-align: center;\"><b>Errore nel caricamento. Si prega di correggere gli errori prima di proseguire.</b></div>\n");
	} else {
		print("<br><div style=\"text-align: center;\"><b>Compilazione senza errori. Continua.</b></div>\n");
		print("<form action=\"base_xls2doc.php\" method=\"post\" enctype=\"multipart/form-data\">\n");
		hiddenField("file", $nameFile);
		hiddenField("tipodoc", $tipodoc);
		hiddenField("mode", "IMPORT");
		print("<br>\n");
	 
		print("<input type=\"submit\" id=\"btnok\" value=\"Procedi!\" >\n");
		print("</form>\n");
	}
} else {
	if( $err ) {
		print("<br><div style=\"text-align: center;\"><b>Errore nel caricamento. Procedura Interrotta. Si prega di correggere gli errori e riprovare a caricare o contattare il Ced Krona Koblenz.</b></div>\n");

	} else {
		print("<br>FINE.\n");
	}	
}


print("<br>\n<br>\n");
print("<a href=\"{$tipodoc}importxls.php\">");
print("<img border=\"0\" src=\"../img/05_edit.gif\" alt=\"Nuovo caricamento\">Nuovo caricamento</a>\n");


print("<br>\n");
goMain();
footer();


// FUNZIONI UTILI

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