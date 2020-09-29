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

$conn = db_connect($dbase); 

include("../libs/distbase.php");
 
$articolo = strtoupper($_GET['articolo']);
$anno = current_year();

$id_testa = 0;
$quantita = $_GET['quantita'];
$rif = $_GET['rif'];
$copy = $_GET['copy'];
$cliven = $_GET['codcli'];

session_start();
$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
$maga = "F" . substr($cookie[0],2);

//print($maga);

head();
include("../libs/dropdown_lib.php");
?>
<script type="text/javascript">
var OnSubmitForm = function() {
	if(document.pressed == 'Inserisci bolla') {
		document.db.action = "creadoc.php";
	} else {
		if(document.pressed == 'Estrai dati') {
			document.db.action = "creadocxml.php";
		}
	}
	return true;
};
</script>
<?php

if ( checkDistinta($articolo) ) {
	$Query = "SELECT DESCRIZION FROM MAGART WHERE CODICE = \"$articolo\""; 
	$queryexe = db_query($conn, $Query) or die(mysql_error()); 
	$row = mysql_fetch_object($queryexe);
	mostraDistinta($articolo, $quantita, $row->DESCRIZION, $_GET['lotto']);
} else { 
	banner($articolo . " - Non trovato");
	print("<h2>Articolo " . $articolo . " non ha distinta</h2>\n"); 
}


print("<br>\n");
goEdit("askdb.php","Nuova bolla");
print("<br>\n");
goMain();
footer();


function mostraDistinta($articolo, $quantita, $descrizion, $lottopadre, $idRiga) {
	global $anno, $conn, $maga, $rif, $copy;
	
    $aComp[] = array("codice" => "", "consumo" => 0, "um" => "");
    print("<script type=\"text/javascript\" src=\"../js/ajaxlib.js\"></script>\n");
    print("<script type=\"text/javascript\" src=\"../js/dbedit.js\"></script>\n");
	banner("$articolo - $descrizion");
	
	$Query = "SELECT DATADOC, U_DTESPLD FROM DOCRIG WHERE ID = $rif";
	$rs = db_query($conn, $Query) or die(mysql_error()); 
	$row = mysql_fetch_object($rs);
	$nCompLen  = xEsplodi($articolo, $row->U_DTESPLD, $quantita, &$aComp, 0, 0);
//    echo $articolo;

	// scrittura tabella con i dati trovati
//	print("<form id=\"db\" name=\"db\" method=\"POST\" action=\"creadoc.php\">\n");
	print("<form id=\"db\" name=\"db\" method=\"POST\" onsubmit=\"return OnSubmitForm();\">\n");
	print("<table id=\"tbl\" class=\"list\">\n");
	print("<thead>\n<tr class=\"list\">\n");
	print("<th class=\"list\">Codice</th>\n");
	print("<th class=\"list\">Descrizione</th>\n");
	print("<th class=\"list\">Qta</th>\n");
	print("<th class=\"list\">Lotto</th>\n");
	print("</tr>\n</thead>\n");
	print("<tbody id=\"tblbody\">\n");
	$msg = "I seguenti articoli non presentano giacenza sufficiente:\\n";
	$lmsg = false;
	for($j = 1; $j <= $nCompLen; $j++) {
		print("<tr class=\"list\" id=\"riga$j\">\n");
		print("<td class=\"list\"><input type=\"text\" readonly=\"readonly\" size=\"16\" name=\"code$j\" id=\"code$j\" onclick=\"popup('dettagli',$j);\" value=\"" . $aComp[$j][codice] . "\"></td>\n");
		
		// cerco l'ubicazione, la descrizione e la giacenza
		$Query = "SELECT MAGART.DESCRIZION ";
		$Query .= "FROM MAGART ";
		$Query .= "WHERE MAGART.CODICE = \"" . $aComp[$j][codice]. "\" ";
		$rs = db_query($conn, $Query) or die(mysql_error()); 
		$row = mysql_fetch_object($rs);
		print("<td class=\"list\"><span id=\"desc$j\">" . $row->DESCRIZION . "</span></td>\n");

		// Quantita
		print("<td class=\"list\" style=\"text-align: center;\"><input type=\"text\" size=\"3\" name=\"qta$j\" id=\"qta$j\" value=\"" . $aComp[$j][consumo] . "\"></td>\n");
		
		// cerco tra i lotti se c'� qualcosa
		$Query = "SELECT PROGQTACAR-PROGQTASCA+PROGQTARET AS GIACENZA, LOTTO FROM MAGGIACL ";
		$Query .= "WHERE ARTICOLO = \"" . $aComp[$j][codice]. "\" ";
		$Query .= "AND MAGAZZINO = \"$maga\" ";
		$Query .= "ORDER BY LOTTO DESC ";
		$rs = db_query($conn, $Query) or die(mysql_error()); 
		// se c'� un id per la copia dei lotti faccio la ricerca nella bolla indicata
		if( $copy > 0) {
			$Query = "SELECT LOTTO FROM U_BARDR WHERE CODICEARTI = \"" . $aComp[$j][codice]. "\" AND ID_TESTA = $copy";
			$ls = db_query($conn, $Query) or die(mysql_error()); 
			$rw = mysql_fetch_object($ls);
		}
		$cnt=0;
		$found=false;
		print("<td class=\"list\">\n");
//		print(ddBox1("lotto$j"));
// la riga sopra serve a rendere editabile la combobox
// 29.05.2012 i lotti non sono pi� modificabili
		print("<select name=\"lotto$j\" id=\"lotto$j\">\n");
		while ($row = mysql_fetch_object($rs))	{
		    if($row->GIACENZA > 0) {
				$cnt++;
				print("<option value=\"" . $row->LOTTO . "\"");
				if( $copy > 0) {
					if( $rw->LOTTO == $row->LOTTO) {
						print(" selected=\"selected\"");
						$found=true;
					}
				} else {
					if( $cnt == 1) {
						print(" selected=\"selected\"");
					}			
				}
				print(">" . $row->LOTTO . " - Giac." . $row->GIACENZA . "</option>\n");
			}  
		}
		if($copy > 0 and !$found) {
			print("<option value=\"" . $rw->LOTTO . "\" selected=\"selected\">" . $rw->LOTTO . "</option>\n");
		}
		// non ho trovato nessun lotto, propongo uno '0'
		if( $cnt == 0 and $copy == 0 ) {
			$Query = "SELECT GIACINI+PROGQTACAR-PROGQTASCA+PROGQTARET AS GIACENZA FROM MAGGIAC ";
			$Query .= "WHERE ARTICOLO = \"" . $aComp[$j][codice]. "\" ";
			$Query .= "AND MAGAZZINO = \"$maga\" ";
			$Query .= "AND ESERCIZIO = \"$anno\" ";
			$rg = db_query($conn, $Query) or die(mysql_error()); 
			print("<option value=\"0\" selected=\"selected\">0 - Generato - Giac.");
			if($rwg = mysql_fetch_object($rg)) {
				print($rwg->GIACENZA);
			} else {
				print(0);
			}
			print("</option>\n");
		}
//		print(ddBox2("lotto$j"));
		print("</select>\n");
		print("</td>\n");
		
		// chiusura riga
		print("</tr>\n");
	}  
	print("</tbody>\n</table>\n");
	print("<input type=\"hidden\" name=\"count\" id=\"count\" value=\"$nCompLen\">\n");
	print("<input type=\"hidden\" name=\"padre\" id=\"padre\" value=\"$articolo\">\n");
	print("<input type=\"hidden\" name=\"lottopadre\" id=\"lottopadre\" value=\"$lottopadre\">\n");
	print("<input type=\"hidden\" name=\"quantita\" id=\"quantita\" value=\"$quantita\">\n");
	print("<input type=\"hidden\" name=\"idriga\" id=\"idriga\" value=\"$idRiga\">\n");
	print("<input type=\"hidden\" name=\"rifr\" id=\"rifr\" value=\"$rif\">\n");
    print("<input type=\"hidden\" name=\"cliven\" id=\"cliven\" value=\"$cliven\">\n");
	
	$Query = "SELECT ID_TESTA FROM DOCRIG WHERE ID = $rif";
	$rs = db_query($conn, $Query) or die(mysql_error()); 
	$rw = mysql_fetch_object($rs);
	print("<input type=\"hidden\" name=\"rift\" id=\"rift\" value=\"" . $rw->ID_TESTA . "\">\n");
	
	print("<input type=\"hidden\" name=\"idtesta\" id=\"idtesta\" value=\"\">\n");
	print("<input type=\"hidden\" name=\"numerodocf\" id=\"numerodocf\" value=\"" . $_GET['numero'] . "\">\n");
//	print("<input type=\"submit\" id=\"btnok\" value=\"Ok\" >\n");
	print("<input type=\"submit\" id=\"btnok\" value=\"Inserisci bolla\" onclick=\"document.pressed=this.value;\">\n");
	print("<input type=\"submit\" id=\"btnxml\" value=\"Estrai dati\" onclick=\"document.pressed=this.value;\">\n");
	
	print("</form>\n");


} // fine funzione mostraDistinta


?>