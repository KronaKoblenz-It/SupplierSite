<?php 
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2013 by Roberto Ceccarelli                        */
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
$maga = "F" . substr($fornitore, -4);

$conn = db_connect($dbase); 
$anno = current_year();

//VARIABILI XLS
$NUM_REG 		= 0;
$DT_REG 		= 1;
$COD_ART_P		= 2;
$QTA_ART_P		= 3;
$COD_LOT_P		= 4;
$RIF_ORD		= 5;
$RIF_RIGA_ORD 	= 6;
$TIPO_DIST		= 7;
$IS_LOT_C		= 8;
$COD_ART_C 		= 9;
$QTA_ART_C		= 10;
$COD_LOT_C		= 11;

//VARIABILI DI ERRORE
$ERR_ART_P = 0;
$ERR_ART_C = 0;
$ERR_QTA_C = 0;
$ERR_LOT_C = 0;
$ERR_GIAC = 0;
$ERR_RIF_ORD = 0;

//VARIABILI di DATI

//TABELLA
$k=0;

head();
banner("Importazione file Excel");

$err = false;
	if ($_FILES["file"]["error"] > 0) {
	   echo "Errore: " . $_FILES["file"]["error"] . "<br />";
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


	$objPHPExcel = PHPExcel_IOFactory::load($_FILES["file"]["tmp_name"]);
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
		$aComp[] = array("codice" => "", "consumo" => 0, "um" => "");
		
		for ($row = 2; $row <= $highestRow; ++ $row) {
						
			$bollaNum = $worksheet->getCellByColumnAndRow($NUM_REG, $row)->getValue();
			$dtBolla = date("d-m-Y", strtotime($worksheet->getCellByColumnAndRow($DT_REG , $row)->getValue()));
			
			$tipoDistin = ($worksheet->getCellByColumnAndRow($TIPO_DIST, $row)->getValue() == 0 ? "P" : "C");
			
			$artP = trim($worksheet->getCellByColumnAndRow($COD_ART_P, $row)->getValue());
			$qtaArtP = $worksheet->getCellByColumnAndRow($QTA_ART_P, $row)->getValue();
			$lottoP = trim($worksheet->getCellByColumnAndRow($COD_LOT_P, $row)->getValue());
			
			$art = trim($worksheet->getCellByColumnAndRow(($tipoDistin == "P" ? $COD_ART_P : $COD_ART_C), $row)->getValue());
			$lottoC = "";
			$qtaArtC = 0;
			$isLotto = 0;
			
			if ($tipoDistin == "C") {
				$isLotto = $worksheet->getCellByColumnAndRow($IS_LOT_C, $row)->getValue();
				if ($isLotto == 1){
					$lottoC = trim($worksheet->getCellByColumnAndRow($COD_LOT_C, $row)->getValue());
				}
				$qtaArtC = $worksheet->getCellByColumnAndRow($QTA_ART_C, $row)->getValue();
			}
			
			
			
			if ($art == $artP) {
				//INIZIALIZZO ERRORE PADRE
				$ERR_ART_P = 0;
				$ERR_ART_C = 0;
				$ERR_QTA_C = 0;
				$ERR_LOT_C = 0;
				$ERR_GIAC = 0;
				$ERR_RIF_ORD = 0;
				
				$rifOrd = explode(" ", $worksheet->getCellByColumnAndRow($RIF_ORD, $row)->getValue());
				$rifRiga = $worksheet->getCellByColumnAndRow($RIF_RIGA_ORD, $row)->getValue();
				
				// SE è Padre controllo che Esista!
				$q = "SELECT DESCRIZION FROM MAGART WHERE CODICE=\"$art\"";
				//print($q."<br>");
				$rs = db_query($conn, $q) or die(mysql_error()); 
				if (mysql_num_rows($rs)>0) {
					$rw = db_fetch_row($rs);
					$descArt = $rw[0];
				} else {
					$descArt = $artP . "Sembra non essere presente nell'Angrafica Articoli";
					$ERR_ART_P = 1;
				}
				
				if (!$ERR_ART_P){
					$tipoRifOrd = $rifOrd[0];
					$numRifOrd = $rifOrd[1];
					
					$q = "SELECT ID, ID_TESTA, U_DTESPLD FROM DOCRIG ";
					$q .= "WHERE TIPODOC=\"" . $rifOrd[0] . "\" AND NUMERODOC = \"";
					$q .= $rifOrd[1];
					$q .= "\" AND NUMERORIGA = $rifRiga AND CODICEARTI=\"" . trim($artP) . "\" AND QUANTITARE >= $qtaArtP";
					//print($q."<br>");
					$rs = db_query($conn, $q) or die(mysql_error()); 
					if (mysql_num_rows($rs)==0) {
						$rw = db_fetch_row($rs);
						$tipoRifOrd =  $rifOrd[0][1] . $rifOrd[0][0] ;
						$numRifOrd = $rifOrd[1];
						// non ho trovato il riferimento provo riga sotto
						$q = "SELECT ID, ID_TESTA, U_DTESPLD FROM DOCRIG ";
						$q .= "WHERE TIPODOC=\"" . $rifOrd[0][1] . $rifOrd[0][0] . "\" AND NUMERODOC = \"";
						$q .= $rifOrd[1];
						$rifRiga++;
						$q .= "\" AND NUMERORIGA = $rifRiga AND CODICEARTI=\"" . trim($artP) . "\" AND QUANTITARE >= $qtaArtP";
						//print($q."<br>");
						$rs = db_query($conn, $q) or die(mysql_error()); 
						// se ancora non ho trovato provo riga sopra
						if (mysql_num_rows($rs)==0) {
							$rw = db_fetch_row($rs);
							/*$q = "SELECT ID, ID_TESTA, U_DTESPLD FROM DOCRIG ";
							$q .= "WHERE TIPODOC=\"" . $rifOrd[0][1] . $rifOrd[0][0] . "\" AND NUMERODOC = \"";
							$q .= $rifOrd[1];
							$rifRiga = $rifRiga-2;
							$q .= "\" AND NUMERORIGA = $rifRiga AND CODICEARTI=\"" . trim($artP) . "\" AND QUANTITA >= $qtaArtP";
							//print($q."<br>");
							$rs = db_query($conn, $q) or die(mysql_error()); 
							// se ancora non ho trovato provo riga sopra
							if( !($rw = db_fetch_row($rs)) ) {*/
								$tipoRifOrd = "";
								$numRifOrd = 0;
								$rw[0] = 0;
								$rw[1] = 0;
								$ERR_RIF_ORD = 1;
							//}
						}
					}
					$rifFromT = $rw[1];
					$rifFromR = $rw[0];
					$dtEsplDist = $rw[2];
					
					
					$nCompLen  = xEsplodi($artP, $dtEsplDist, $qtaArtP, &$aComp, 0, 0);
				}
			}
			
			if (!$ERR_ART_P && ($art != $artP)){
				//INIZIALLIZZO ERRORI COMP
				$ERR_ART_C = 0;
				$ERR_QTA_C = 0;
				$ERR_LOT_C = 0;
				$ERR_GIAC = 0;
				
				//Controllo che il componente faccia parte della distinta
				if(($keyC = in_arrayMulti($art, $aComp, 'codice')) > -1){
					//Cerco descrizione in MagArt
					$q = "SELECT DESCRIZION FROM MAGART WHERE CODICE=\"$art\"";
					$rs = db_query($conn, $q) or die(mysql_error()); 
					$rw = db_fetch_row($rs);
					$descArt = $rw[0];
					
					$consumoArtC = $aComp[$keyC]['consumo'];
					if($qtaArtC > $consumoArtC+0.7 || $qtaArtC < $consumoArtC-0.7){
						$ERR_QTA_C = 1;
					}
					
				} else {
					$descArt = "Componente NON TROVATO in DISTBASE ".$artP;
					$ERR_ART_C = 1;
				}
				
				//SE è Componente && Esiste controllo il Lotto e la Giacenza
				if(!$ERR_ART_C){
					if ($isLotto){
						$q = "SELECT PROGQTACAR-PROGQTASCA+PROGQTARET AS GIACENZA, LOTTO FROM MAGGIACL ";
						$q .= "WHERE ARTICOLO = \"" . $art . "\" ";
						$q .= "AND MAGAZZINO = \"F1540\" ";
						$q .= "ORDER BY LOTTO DESC ";
						$rs2 = db_query($conn, $q) or die(mysql_error()); 
						
						$cntLot=0;
						$aLotti[] = array("codice" => "", "giac" => 0);
						$found=false;
						$varGiac=0;
						
						
						while ($row2 = mysql_fetch_object($rs2))	{ 
							if($row2->GIACENZA > 0) {
								$aLotti[$cntLot]['codice'] = $row2->LOTTO;
								$aLotti[$cntLot]['giac'] = $row2->GIACENZA;
								$cntLot++;
							}
						}
						if(($keyL = in_arrayMulti($lottoC, $aLotti, 'codice')) == -1){
							$ERR_LOT_C = 1;
						} else {
							$varGiac=$aLotti[$keyL]['giac'];
							if ($consumoArtC > $varGiac){
								$ERR_GIAC = 1;
							}
						}
					} else {
						$q = "SELECT GIACINI+PROGQTACAR-PROGQTASCA+PROGQTARET AS GIACENZA FROM MAGGIAC ";
						$q .= "WHERE ARTICOLO = \"" . $art. "\" ";
						$q .= "AND MAGAZZINO = \"F1540\" ";//$maga
						$q .= "AND ESERCIZIO = \"$anno\" ";
						$rg = db_query($conn, $q) or die(mysql_error()); 
						$varGiac=0;
						if($rwg = mysql_fetch_object($rg)) {
							$varGiac=$rwg->GIACENZA;
							if ($consumoArtC > $varGiac){
								$ERR_GIAC = 1;
							}
						} else {
							$ERR_GIAC = 1;
						}
					}
				}
				
				
			}
			
			//INIZIO A DISEGNARE TABELLA
					
			
			//scrittura testa (se serve)
			if($lastbolla != ($bollaNum . $artP . $lottoP . $tipoRifOrd . $numRifOrd . $rifRiga)) {
				if($isInizio){
					chiudiTabella();
					$k++;
				}
				$j=0;
				//print($lastbolla);
				print("</br><div style=\"text-align: center;\"><span id=\"Title$k\"><b>Bolla Num: ".$bollaNum." con Data Registrazione " . date("d-m-Y") . "</b></span></div> </br>\n");
				$isInizio = true;
				print("<table id=\"tbl$k\" class=\"list\" style=\"width:80%; style=margin-left:auto; margin-right:auto\"  align=\"center\">\n");
				print("<thead>\n<tr class=\"list\">\n");
				print("<th class=\"list\" style=\"width:2%;\">Tipo</th>\n");
				print("<th class=\"list\" style=\"width:18%;\">Articolo</th>\n");
				print("<th class=\"list\">Descrizione</th>\n");
				//print("<th class=\"list\">U.M.</th>\n");
				print("<th class=\"list\" style=\"width:8%;\">Qta</th>\n");
				print("<th class=\"list\" style=\"width:10%;\">Lotto</th>\n");
				print("<th class=\"list\" style=\"width:8%;\">Giacenza</th>\n");
				print("<th class=\"list\" style=\"width:8%;\">Rif. Ord.</th>\n");
				print("<th class=\"list\" style=\"width:8%;\">Rif. Riga</th>\n");
				print("</tr>\n</thead>\n");
				print("<tbody id=\"tblbody\">\n");
				$id_testa++;
				
				$lastbolla = $bollaNum . $artP . $lottoP . $tipoRifOrd . $numRifOrd . $rifRiga;
			}
			
			// scrittura riga
			
			$id++; //valorizzo id riga
			
			// scrivo la riga a monitor
			print("<tr class=\"list\" id=\"riga$j\">\n");
			print("<td class=\"list\" style=\"text-align:center;\"><span id=\"tipoDist$j\">" . $tipoDistin . "</span></td>\n");
			if($ERR_ART_C || $ERR_ART_P){
				print("<td class=\"error\" style=\"text-align:center;\"><span id=\"code$j\">" . $art . "</span></td>\n");
				print("<td class=\"list\"><span id=\"desc$j\" style=\"font-size: 9pt; color: red;\"><b>" . $descArt . "</b></span></td>\n");
			} else {
				print("<td class=\"list\" style=\"text-align:center;\"><span id=\"code$j\">" . $art . "</span></td>\n");
				print("<td class=\"list\"><span id=\"desc$j\">" . $descArt . "</span></td>\n");
			}
			
			if ($tipoDistin == "P"){
				print("<td class=\"list\"><span id=\"qta$j\">" . $qtaArtP . "</span></td>\n");
				print("<td class=\"list\"><span id=\"lotto$j\">" . $lottoP . "</span></td>\n");
				print("<td class=\"list\"><span id=\"giac$j\"></span></td>\n");
			} else {
				if($ERR_QTA_C){
					print("<td class=\"error\"><span id=\"qta$j\">" . $qtaArtC . " diverso da " .$consumoArtC ."</span></td>\n");
				} else {
					print("<td class=\"list\"><span id=\"qta$j\">" . $qtaArtC . "</span></td>\n");
				}
				if($ERR_LOT_C){
					print("<td class=\"error\"><span id=\"lotto$j\">" . $lottoC . "</span></td>\n");
				} else {
					print("<td class=\"list\"><span id=\"lotto$j\">" . $lottoC . "</span></td>\n");
				}
				
				if($ERR_GIAC){
					print("<td class=\"error\"><span id=\"giac$j\">" . $varGiac . "</span></td>\n");
				} else {
					print("<td class=\"list\"><span id=\"giac$j\">" . $varGiac . "</span></td>\n");
				}
			}
			print("<td class=\"list\"><span id=\"rifOrd$j\">" . $tipoRifOrd ." ". $numRifOrd . "</span></td>\n");
			print("<td class=\"list\"><span id=\"rifRiga$j\">" . $rifRiga . "</span></td>\n");
			
			echo "</tr>\n";
			$j++;
			
			if(!$err){
				if( $ERR_ART_C || $ERR_ART_P || $ERR_QTA_C || $ERR_LOT_C || $ERR_GIAC ){
					$err = true;
				}				
			}
			
			if ($row == $highestRow){
				chiudiTabella();
			}
			
		}
		
	}

}


if( $err ) {
	print("<br><div style=\"text-align: center;\"><b>Errore nel caricamento. Si prega di correggere gli errori prima di proseguire.</b></div>\n");
/*	$Query = "DELETE FROM U_BARDR WHERE ID_TESTA = $id_testa";
	$rs = db_query($conn, $Query) or die(mysql_error());
	$Query = "DELETE FROM U_BARDT WHERE ID = $id_testa";
	$rs = db_query($conn, $Query) or die(mysql_error()); */
	/*mail("ced@k-group.com", "Importazione_Bolle_$cookie[0]_$cookie[1]",  "KronaKoblenz Attenzione si è verificato un Errore nell'inserimento Bolla da Excel, l'importazione potrebbe causare problemi", "From: automatico@k-group.com");
	mail("ced-it@k-group.com", "ImportazioneBolle_$cookie[0]_$cookie[1]",  "KronaKoblenz Attenzione si è verificato un Errore nell'inserimento Bolla da Excel, l'importazione potrebbe causare problemi", "From: automatico@k-group.com");
	mail("spedizioni@koblenz.it", "ImportazioneBolle_$cookie[0]_$cookie[1]",  "KronaKoblenz Attenzione si è verificato un Errore nell'inserimento Bolla da Excel, l'importazione potrebbe causare problemi", "From: automatico@k-group.com");
	mail("b.vaccari@vmgroup.it", "Importazione_Bolle_$cookie[0]_$cookie[1]",  "Krona Koblenz - Attenzione si è verificato un Errore nell'inserimento Bolla da Excel.", "From: automatico@k-group.com");
*/
} else {
	print("<br>FINE.\n");
}
print("<br>\n<br>\n");
print("<a href=\"ddtimportxls.php\">");
print("<img border=\"0\" src=\"../img/05_edit.gif\" alt=\"Nuovo caricamento\">Nuovo caricamento</a>\n");


print("<br>\n");
goMain();
footer();


// FUNZIONI UTILI
function in_arrayMulti($value, $array, $campo) 
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
}

function chiudiTabella(){
	print("</tbody>\n</table>\n");
	/*print("<input type=\"hidden\" name=\"count\" id=\"count\" value=\"$nCompLen\">\n");
	print("<input type=\"hidden\" name=\"padre\" id=\"padre\" value=\"$articolo\">\n");
	print("<input type=\"hidden\" name=\"lottopadre\" id=\"lottopadre\" value=\"$lottopadre\">\n");
	print("<input type=\"hidden\" name=\"quantita\" id=\"quantita\" value=\"$quantita\">\n");
	print("<input type=\"hidden\" name=\"idriga\" id=\"idriga\" value=\"$idRiga\">\n");
	print("<input type=\"hidden\" name=\"rifr\" id=\"rifr\" value=\"$rif\">\n");
    print("<input type=\"hidden\" name=\"cliven\" id=\"cliven\" value=\"$cliven\">\n");
	print("<input type=\"hidden\" name=\"rift\" id=\"rift\" value=\"" . $rw->ID_TESTA . "\">\n");	
	print("<input type=\"hidden\" name=\"idtesta\" id=\"idtesta\" value=\"\">\n");
	print("<input type=\"hidden\" name=\"numerodocf\" id=\"numerodocf\" value=\"" . $_GET['numero'] . "\">\n</br>");*/
}


?>