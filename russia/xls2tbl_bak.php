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
print("<script type=\"text/javascript\" src=\"./xlstotbl_util.js\"></script>\n");
print(' 
	<link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
	<script src="//code.jquery.com/jquery-1.10.2.js"></script>
	<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
	<link rel="stylesheet" href="/resources/demos/style.css">
	<script>
		$(function() {
			$( "#datepicker" ).datepicker({format: "Y-m-d"});
		});
	</script>
  ');

session_start();
$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
$fornitore = $cookie[0];
$maga = "F" . substr($fornitore, -4);
$date = date("d_m_y", time());
define("UPLOAD_DIR", "./uploads/".$date."/");

$conn = db_connect($dbase); 
$anno = current_year();

$nameFile = isset($_GET['file']) ? $_GET['file'] : (isset($_POST['file']) ? $_POST['file'] : '');

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
} else {
//	   echo "Upload: " . $_FILES["file"]["name"] . "<br />";
//	   echo "Type: " . $_FILES["file"]["type"] . "<br />";
//	   echo "Size: " . ($_FILES["file"]["size"] / 1024) . " Kb<br />";
//	   echo "Stored in: " . $_FILES["file"]["tmp_name"];
}

if(!$err) {
	$currency = getCurrency();
	
	#--INIZIO FORM PER INVIO DATI A XLS2DB
	print("<form action=\"xls2doc.php\" method=\"post\" enctype=\"multipart/form-data\">\n");
	print("<div name=\"pretable\" id=\"pretable\" style=\"padding: 30px; margin: 30px;\">");
	
		print("<label for=\"valuta\">Seleziona Valuta di Importazione:</label>\n");
		print("<select name=\"valuta\" id=\"valuta\" onchange=\"checkValuta(this);\">\n");
			print("<option selected=\"selected\" value=\"\"></option>\n");
			print("<option value=\"EUR\">EURO</option>\n");
			print("<option value=\"RUB\">RUBLO</option>\n");
		print("</select>\n</td>\n");
		print("<span name=\"hide\" id=\"hide\" style=\"display: none;\">");
			print("1 €  =  ");
			print("<input type=\"text\" size=\"4\" name=\"cambio\" id=\"cambio\" value=\"1\" onkeyup=\"soloNumeri('cambio');\" readonly> RUB  ");
			print("<input type=\"button\" name=\"yahoo\" id=\"yahoo\" value=\"Convert\" onclick=\"getCurrency(this);\"> <-- Yahoo Currency \n");
			print("<input type=\"hidden\" name=\"currency\" id=\"currency\" value=\"$currency\">\n");                     //Currency Valuta Yahoo Finance
		print("</span>");
		
		print('<p>Date: <input type="text" id="datepicker"></p>');
	print("</div>");
	
	#--SCRIVO TESTA TABLE	
	print("<table id=\"tbl\" class=\"list\" style=\"width:80%; style=margin-left:auto; margin-right:auto\"  align=\"center\">\n");
		print("<thead>\n<tr class=\"list\">\n");
			print("<th class=\"list\">Row</th>\n");
			print("<th class=\"list\" style=\"width:18%;\">Articolo</th>\n");
			print("<th class=\"list\">Descrizione</th>\n");
			print("<th class=\"list\">Gruppo</th>\n");
			print("<th class=\"list\" style=\"width:4%;\">U.M.</th>\n");
			print("<th class=\"list\" style=\"width:8%;\">Fatt</th>\n");
			print("<th class=\"list\" style=\"width:8%;\">Qta</th>\n");
			print("<th class=\"list\" style=\"width:8%;\">Valore</th>\n");
			print("<th class=\"list\" style=\"width:8%;\">Valore(+IVA)</th>\n");
			print("<th class=\"list\" style=\"width:8%;\">Costo</th>\n");
		print("</tr>\n</thead>\n");
	print("<tbody id=\"tblbody\">\n");

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
		$id_testa = (time() % 10000000) * 100;
		$id = ($id_testa % 1000000)*1000;
				
		for ($row = 9; $row <= $highestRow; ++ $row) {
				
			$codArt = $worksheet->getCellByColumnAndRow($COD_ART, $row)->getValue();
			$descrR = $worksheet->getCellByColumnAndRow($DESCR_RUS, $row)->getValue();
			$descrE = $worksheet->getCellByColumnAndRow($DESCR_ENG, $row)->getValue();
			$qtaArt = number($worksheet->getCellByColumnAndRow($QTA_ART, $row)->getValue());
			$umArt = $worksheet->getCellByColumnAndRow($UM_ART, $row)->getValue();
			$valArt = number($worksheet->getCellByColumnAndRow($FATT_ART, $row)->getValue());
			$valIvaArt = number($worksheet->getCellByColumnAndRow($FATT_IVA_ART, $row)->getValue());
			$costArt = number($worksheet->getCellByColumnAndRow($COST_ART, $row)->getValue());
			$fattArt = 1;
			
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
			
			$codArt = mb_strtoupper($codArt, 'UTF-8');
			if ($codArt=="EXP K 1000 CS DXSX")
			{
				$codArt = "CAMP.K 1000 CS";
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
				if (mysql_num_rows($rs)!=0)	{
				
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
					
					$id++; //valorizzo id riga
					
					#--SCRIVO RIGA IN TBL
					print("<tr class=\"list\" id=\"riga$j\">\n");
						
						print("<td class=\"list\"><span id=\"desc$j\">" . $row . "</span></td>\n");
						print("<td class=\"list\" style=\"text-align:center;\"><span id=\"code$j\">" . $codArt . "</span></td>\n");
						print("<td class=\"list\"><span id=\"desc$j\">" . $descrE . "</span></td>\n");
						print("<td class=\"list\"><span id=\"desc$j\">" . $codGruppo . "</span></td>\n");
						print("<td class=\"list\"><span id=\"desc$j\">" . $umArt . "</span></td>\n");
						print("<td class=\"list\"><span id=\"desc$j\">" . $fattArt . "</span></td>\n");
						print("<td class=\"list\"><span id=\"desc$j\">" . $qtaArt . "</span></td>\n");
						print("<td class=\"list\"><span id=\"desc$j\">" . $valArt . "</span></td>\n");
						print("<td class=\"list\"><span id=\"desc$j\">" . $valIvaArt . "</span></td>\n");
						print("<td class=\"list\"><span id=\"desc$j\">" . $costArt . "</span></td>\n");
						
					print("</tr>\n");
					
				} else if ((strpos($codArt, 'Л') !== FALSE)) {
					$codGruppo = "B0102";
							
					if( $descrE == "") {
						$descrE = "-- NO DESCRIPTION --";
					}
					
					$id++; //valorizzo id riga
					
					#--SCRIVO RIGA IN TBL
					print("<tr class=\"list\" id=\"riga$j\">\n");
					
						print("<td class=\"list\"><span id=\"desc$j\">" . $row . "</span></td>\n");
						print("<td class=\"list\" style=\"text-align:center;\"><span id=\"code$j\">" . $codArt . "</span></td>\n");
						print("<td class=\"list\"><span id=\"desc$j\">" . $descrE . "</span></td>\n");
						print("<td class=\"list\"><span id=\"desc$j\">" . $codGruppo . "</span></td>\n");
						print("<td class=\"list\"><span id=\"desc$j\">" . $umArt . "</span></td>\n");
						print("<td class=\"list\"><span id=\"desc$j\">" . $fattArt . "</span></td>\n");
						print("<td class=\"list\"><span id=\"desc$j\">" . $qtaArt . "</span></td>\n");
						print("<td class=\"list\"><span id=\"desc$j\">" . $valArt . "</span></td>\n");
						print("<td class=\"list\"><span id=\"desc$j\">" . $valIvaArt . "</span></td>\n");
						print("<td class=\"list\"><span id=\"desc$j\">" . $costArt . "</span></td>\n");
								
					echo "</tr>\n";
				} else {
					$codGruppo = "";
					
					if( $descrE == "") {
						$descrE = "-- NO DESCRIPTION --";
					}
					
					$id++; //valorizzo id riga
					
					#--SCRIVO RIGA IN TBL
					print("<tr class=\"list\" id=\"riga$j\">\n");
						
						print("<td class=\"list\"><span id=\"desc$j\">" . $row . "</span></td>\n");
						print("<td class=\"list\" style=\"text-align:center;\"><span id=\"desc$j\" style=\"font-size: 9pt; color: red;\"><b>" . $codArt . "</b></span></td>\n");
						print("<td class=\"list\"><span id=\"desc$j\">" . $descrE . "</span></td>\n");
						print("<td class=\"list\"><span id=\"desc$j\">" . $codGruppo . "</span></td>\n");
						print("<td class=\"list\"><span id=\"desc$j\">" . $umArt . "</span></td>\n");
						print("<td class=\"list\"><span id=\"desc$j\">" . $fattArt . "</span></td>\n");
						print("<td class=\"list\"><span id=\"desc$j\">" . $qtaArt . "</span></td>\n");
						print("<td class=\"list\"><span id=\"desc$j\">" . $valArt . "</span></td>\n");
						print("<td class=\"list\"><span id=\"desc$j\">" . $valIvaArt . "</span></td>\n");
						print("<td class=\"list\"><span id=\"desc$j\">" . $costArt . "</span></td>\n");
								
					echo "</tr>\n";
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
	/*mail("ced@k-group.com", "Importazione_Bolle_$cookie[0]_$cookie[1]",  "KronaKoblenz Attenzione si 衶erificato un Errore nell'inserimento Bolla da Excel, l'importazione potrebbe causare problemi", "From: automatico@k-group.com");
	mail("ced-it@k-group.com", "ImportazioneBolle_$cookie[0]_$cookie[1]",  "KronaKoblenz Attenzione si 衶erificato un Errore nell'inserimento Bolla da Excel, l'importazione potrebbe causare problemi", "From: automatico@k-group.com");
	mail("spedizioni@koblenz.it", "ImportazioneBolle_$cookie[0]_$cookie[1]",  "KronaKoblenz Attenzione si 衶erificato un Errore nell'inserimento Bolla da Excel, l'importazione potrebbe causare problemi", "From: automatico@k-group.com");
	mail("b.vaccari@vmgroup.it", "Importazione_Bolle_$cookie[0]_$cookie[1]",  "KronaKoblenz - Attenzione si 衶erificato un Errore nell'inserimento Bolla da Excel.", "From: automatico@k-group.com");
*/
} else {
	print("<br><div style=\"text-align: center;\"><b>Compilazione senza errori. Continua.</b></div>\n");
	print("<input type=\"hidden\" name=\"file\" id=\"file\" value=\"$nameFile\">\n"); 
	print("<br>\n");
 
	print("<input type=\"submit\" id=\"btnok\" value=\"Procedi!\" >\n");
}

#--END FORM
print("</form>\n");



print("<br>\n<br>\n");
print("<a href=\"ddtimportxls.php\">");
print("<img border=\"0\" src=\"../img/05_edit.gif\" alt=\"Nuovo caricamento\">Nuovo caricamento</a>\n");

print("<br>\n");
goMain();
footer();


// FUNZIONI UTILI

function chiudiTabella(){
	print("</tbody>\n</table>\n");
}

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

function getCurrency(){
	$from2 = 'EUR'; //US Dollar
	$to2 = 'RUB'; //to Australian Dollar
	$url2 = 'http://download.finance.yahoo.com/d/quotes.csv?e=.csv&f=sl1d1t1&s='. $from2 . $to2 .'=X';
	$handle2 = @fopen($url2, 'r');
	if ($handle2) 
	{
	$result2 = fgets($handle2, 4096);
	fclose($handle2);
	}
	$audex = explode(',',$result2);
	$aussie = $audex[1];
	return $aussie;
}

?>