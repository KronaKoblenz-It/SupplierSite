<?php 
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2011 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php"); 
include("db-utils.php");

$conn = db_connect($dbase); 
 
$articolo = strtoupper($_GET['articolo']);
$anno = current_year();

$id_testa = 0;
$quantita = $_GET['quantita'];


if ( checkDistinta($articolo) ) {
	$Query = "SELECT DESCRIZION FROM MAGART WHERE CODICE = \"$articolo\""; 
	$queryexe = db_query($conn, $Query) or die(mysql_error()); 
	$row = mysql_fetch_object($queryexe);
	mostraDistinta($articolo, $quantita, $row->DESCRIZION, $_GET['lotto']);
} else { 
	head();
	banner($articolo . " - Non trovato");
	print("<h2>Articolo " . $articolo . " non ha distinta</h2>\n"); 
}


print ("<br><a href=\"askdb.php\">Altra ricerca</a>\n");
footer();

function checkDistinta($articolo) {
	global $connectionstring;
	
	$Query = "SELECT CODPADRE FROM DISTBASE WHERE CODPADRE = \"$articolo\""; 
	$queryexe = db_query($conn, $Query) or die(mysql_error()); 
	if(mysql_num_rows($queryexe) > 0)
	{
		return true;
	} else {
		return false;
	}
}


function mostraDistinta($articolo, $quantita, $descrizion, $lottopadre, $idRiga) {
	global $anno, $conn;
	
    $aComp[] = array("codice" => "", "consumo" => 0, "um" => "");
	head();
    print("<script type=\"text/javascript\" src=\"ajaxlib.js\"></script>\n");
    print("<script type=\"text/javascript\" src=\"dbedit.js\"></script>\n");
//    print("<script type=\"text/javascript\" src=\"csspopup.js\"></script>\n");
	banner("$articolo - $descrizion");
	
	$nCompLen  = xEsplodi($articolo, date('Y-m-d'), $quantita, &$aComp, 0, 0);
	// iframe per dettagli riga
/*	print("<div id=\"dettagli\" style=\"display:none;\">\n");
	print("<input type=\"hidden\" name=\"currentid\" id=\"currentid\" value=\"0\">\n");
	print("<table class=\"detail\" border=\"1\">\n");
	print("<tr><td>Codice</td>\n");
	print("<td><input type=\"text\" class=\"detail\" readonly=\"readonly\" size=\"16\" name=\"codicearti\" id=\"codicearti\" ></td></tr>\n");
	print("<tr><td>Descr.</td>\n");
	print("<td><input type=\"text\" class=\"detail\" readonly=\"readonly\" size=\"36\" name=\"descrizion\" id=\"descrizion\" ></td></tr>\n");
	print("<tr><td>Qta</td>\n");
	print("<td><input type=\"text\" class=\"detail\" size=\"16\" name=\"qtariga\" id=\"qtariga\" ></td></tr>\n");
	print("<tr><td>Lotto</td>\n");
	print("<td><input type=\"text\" class=\"detail\" size=\"16\" name=\"codicelotto\" id=\"codicelotto\" ></td></tr>\n");
	print("<tr><td>Ubic.</td>\n");
	print("<td><input type=\"text\" class=\"detail\" readonly=\"readonly\" size=\"36\" name=\"ubicazione\" id=\"ubicazione\" ></td></tr>\n");
	print("<tr><td>Giac.</td>\n");
	print("<td><input type=\"text\" class=\"detail\" readonly=\"readonly\" size=\"16\" name=\"giacenza\" id=\"giacenza\" ></td></tr>\n");
	print("</table>\n");
	// campi per la navigazione 
	print("<table class=\"navbar\" border=\"0\" width=\"100%\" ><tr>\n");
    print("<td class=\"navbar\" width=\"33%\" align=\"left\"><a href=\"#\" onclick=\"prevRow();\"><img noborder src=\"b_prevpage.gif\"/>Prec.</a></td>\n"); 
	print("<td class=\"navbar\" width=\"33%\" align=\"center\"><a href=\"#\" onclick=\"popup('dettagli',-1);\"><img noborder src=\"05_edit.gif\"/>Chiudi</a></td>\n");
    print("<td class=\"navbar\" width=\"33%\" align=\"right\"><a href=\"#\" onclick=\"nextRow();\">Succ.<img noborder src=\"b_nextpage.gif\"/></a></td>\n"); 
	print("</tr></table>\n");
	print("</div>\n");
*/	
	// scrittura tabella con i dati trovati
	print("<form id=\"db\" method=\"POST\" action=\"creadoc.php\">\n");
	print("<table id=\"tbl\" border=\"1\">\n");
	print("<thead><tr><th>Codice</th><th>Descrizione</th><th>Qta</th><th>Lotto</th></tr></thead><tbody id=\"tblbody\">\n");
	$msg = "I seguenti articoli non presentano giacenza sufficiente:\\n";
	$lmsg = false;
	for($j = 1; $j <= $nCompLen; $j++) {
		print("<tr id=\"riga$j\">\n");
		print("<td><input type=\"text\" readonly=\"readonly\" size=\"16\" name=\"code$j\" id=\"code$j\" onclick=\"popup('dettagli',$j);\" value=\"" . $aComp[$j][codice] . "\"></td>\n");
		
		// cerco l'ubicazione, la descrizione e la giacenza
		$Query = "SELECT MAGART.DESCRIZION ";
		$Query .= "FROM MAGART ";
		$Query .= "WHERE MAGART.CODICE = \"" . $aComp[$j][codice]. "\" ";
		$rs = db_query($conn, $Query) or die(mysql_error()); 
		$row = mysql_fetch_object($rs);
		print("<td><span id=\"desc$j\">" . $row->DESCRIZION . "</span></td>\n");

		// Quantita
//		print("<td><input type=\"text\" size=\"3\" name=\"qta$j\" id=\"qta$j\" onblur=\"validateQta(this," . $aComp[$j][consumo] . ");\" value=\"" . $aComp[$j][consumo] . "\"></td>\n");
		print("<td><input type=\"text\" size=\"3\" name=\"qta$j\" id=\"qta$j\" value=\"" . $aComp[$j][consumo] . "\"></td>\n");
		
		// cerco tra i lotti se c'è qualcosa
		$Query = "SELECT PROGQTACAR-PROGQTASCA+PROGQTARET AS GIACENZA, LOTTO FROM MAGGIACL ";
		$Query .= "WHERE ARTICOLO = \"" . $aComp[$j][codice]. "\" ";
		$Query .= "AND MAGAZZINO = \"00001\" ";
		$Query .= "ORDER BY LOTTO ASC ";
		$rs = db_query($conn, $Query) or die(mysql_error()); 
		print("<td><select name=\"lotto$j\" id=\"lotto$j\" onblur=\"validateLotto(this);\">");
		while ($row = mysql_fetch_object($rs))	{
			print("<option value=\"" . $row->LOTTO . "\">" . $row->LOTTO . "</option>");
		}
		print("</select></td>\n");
		
		// chiusura riga
		print("</tr>\n");
	}  
	print("</tbody></table>\n");
	print("<input type=\"hidden\" name=\"count\" id=\"count\" value=\"$nCompLen\">\n");
	print("<input type=\"hidden\" name=\"padre\" id=\"padre\" value=\"$articolo\">\n");
	print("<input type=\"hidden\" name=\"lottopadre\" id=\"lottopadre\" value=\"$lottopadre\">\n");
	print("<input type=\"hidden\" name=\"quantita\" id=\"quantita\" value=\"$quantita\">\n");
	print("<input type=\"hidden\" name=\"idriga\" id=\"idriga\" value=\"$idRiga\">\n");
	print("<input type=\"hidden\" name=\"idtesta\" id=\"idtesta\" value=\"\">\n");
	print("<input type=\"hidden\" name=\"numerodocf\" id=\"numerodocf\" value=\"" . $_GET['numero'] . "\">\n");
	print("<input type=\"submit\" id=\"btnok\" value=\"Ok\" >\n");
	
	print("</form>\n");


} // fine funzione mostraDistinta


function xEsplodi($codPadre, $dValida, $nQta, $aComp, $nCompLen, $nLevel)
{
	global $conn;
	
	$nLevel += 1;
	if($nlevel > 10) {
	   print("<h2>Troppi livelli - probabile ricorsione</h2>");
	}
	
	$Query = "SELECT CODCOMP, UNMISURA, QUANTITA, TIPOPARTE, DATAINIVAL, DATAFINVAL ";
	$Query .= "FROM DISTBASE WHERE CODPADRE=\"$codPadre\" ORDER BY NUMERORIGA";
	$rs = db_query($conn, $Query) or die(mysql_error()); 	
	while($row = mysql_fetch_object($rs))
	{
		$nConsumo = $row->QUANTITA * $nQta;
		$today=strtotime(date("Y-m-d"));
		if( floatval($row->DATAINIVAL) == 0 ) {
			 $inidate = strtotime("-1 day");
		} else {
			 $inidate = strtotime(str_replace("/","-",$row->DATAINIVAL));
		}  
		if( floatval($row->DATAFINVAL) == 0 ) {
			 $findate = strtotime("+1 day");
		} else {
			 $findate = strtotime(str_replace("/","-",$row->DATAFINVAL));
		}  
	//	print("$findate - ");
		if ($findate >= $today and $inidate <= $today) {
			switch($row->TIPOPARTE ) {
				case "T":
					// fittizio: non faccio nulla
					break;
				case "F":
					// fantasma: scendo di un livello
					$nCompLen = xEsplodi($row->CODCOMP, $dValida, $nConsumo, &$aComp, $nCompLen, $nLevel);
					break;
				case "N":
					$nCompLen += 1;
					$aComp[$nCompLen] = array("codice" => $row->CODCOMP,
						"consumo" => $nConsumo, 
						"um" => $row->UNMISURA );
					// normale: archivio
				break;
			}
		}
//		$rs->MoveNext();
	}	
	
	return $nCompLen;
}  
?>