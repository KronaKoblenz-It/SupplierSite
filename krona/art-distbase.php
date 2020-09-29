<?php 
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2014 by Roberto Ceccarelli                        */
/* http://strawberryfield.altervista.org								*/
/*                                                                      */
/************************************************************************/

include("header.php"); 
include("db-utils.php");

$conn = db_connect($dbase); 

include("../libs/distbase.php");
 
$rif = $_GET['id'];
$anno = current_year();

session_start();
$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
$maga = "F" . substr($cookie[0],2);

$inc = <<<EOT
  $('#tbl').dataTable().yadcf([
	    {column_number : 0, column_data_type: "html", html_data_type: "text", filter_type: "text"},
	    {column_number : 1, filter_type: "text"},
		]);
EOT;

head(dataTableInit($inc));


$Query = "SELECT CODICEARTI, DESCRIZION, QUANTITA, ESPLDISTIN, TIPODOC, NUMERODOC, DATADOC, CODICECF, UNMISURA FROM DOCRIG WHERE ID = $rif";
$queryexe = db_query($conn, $Query) or die(mysql_error()); 
$row = mysql_fetch_object($queryexe);
$articolo = $row->CODICEARTI;
$desc = $row->DESCRIZION;
$espl = $row->ESPLDISTIN;
$um = $row->UNMISURA;
$quantita = $row->QUANTITA;
$codcf = $row->CODICECF;
$ordine = $row->TIPODOC . " " . $row->NUMERODOC . _(" del ") . format_date($row->DATADOC); 

if ( checkDistinta($articolo) ) {
	banner(_("Distinta base")." $articolo<br>\n$desc", $ordine);
	if($espl == "P") {
		mostraDistintaEsplosa($articolo);
	} else {
		mostraDistinta($articolo);
	}
} else {	
	banner($articolo . " - ". _("Non trovato"));
	print("<h2>" . _("Articolo") . " " . $articolo . " " . _("non ha distinta") . "</h2>\n"); 
}


print("<br>\n");
print("<a class=\"bottommenu\" href=\"art-detail.php?id=$codcf\">");
print("<img style=\"border: none;\" src=\"../img/05_edit.gif\" alt=\"" . $str_eleord[$lang] . " per articolo\">" . $str_eleord[$lang] . " per articolo</a>\n");
print("<br>\n");
print("<a class=\"bottommenu\" href=\"art-doc_list.php?id=$codcf&art=" . urlencode($articolo) . "&um=" . urlencode($um) . "\">");
print("<img style=\"border: none;\" src=\"../img/05_edit.gif\" alt=\"" . $str_eleord[$lang] . " $articolo\">" . $str_eleord[$lang] . " $articolo</a>\n");
print("<br>\n");
goMain();
footer();

function tableHeader() {
	print("<table id=\"tbl\" class=\"list\">\n");
	print("<thead>\n<tr class=\"list\">\n");
	print("<th class=\"list\">" . _("Codice") . "</th>\n");
	print("<th class=\"list\">" . _("Descrizione") . "</th>\n");
	print("<th class=\"list\">" . _("U.M.") . "</th>\n");
	print("<th class=\"list\">" . _("Qta") . "</th>\n");
	print("</tr>\n</thead>\n");
	print("<tbody id=\"tblbody\">\n");
}

function tableFooter() {
	print("</tbody>\n</table>\n");
}

function tableRow($j, $codice, $um, $consumo) {
	global $conn;
	
	$consumo = xRound($consumo);
	print("<tr class=\"list\" id=\"riga$j\">\n");
	print("<td class=\"list\"><input type=\"text\" readonly=\"readonly\" size=\"16\" name=\"code$j\" id=\"code$j\" value=\"$codice\"></td>\n");
	
		// cerco l'ubicazione, la descrizione e la giacenza
	$Query = "SELECT MAGART.DESCRIZION, MAGART.LOTTI ";
	$Query .= "FROM MAGART ";
	$Query .= "WHERE MAGART.CODICE = \"$codice\" ";
	$rs = db_query($conn, $Query) or die(mysql_error()); 
	$row = mysql_fetch_object($rs);
	print("<td class=\"list\"><span id=\"desc$j\">" . $row->DESCRIZION . "</span></td>\n");
	
	// U.M.
	print("<td class=\"list\" style=\"text-align: center;\"> <span style=\"font-size: 9pt;\">$um</span></td>\n");

	// Quantita
	print("<td class=\"list\" style=\"text-align: center;\"><input readonly=\"readonly\" type=\"text\" size=\"10\" name=\"qta$j\" id=\"qta$j\" style=\"text-align: right;\" value=\"$consumo\"></td>\n");
	
	// chiusura riga
	print("</tr>\n");
}

function mostraDistintaEsplosa($articolo) {
	global $anno, $conn, $maga, $rif, $copy;

	tableHeader();
	$Query = "SELECT ID_TESTA FROM DOCRIG WHERE ID = $rif";
	$rs = db_query($conn, $Query) or die(mysql_error()); 
	$row = mysql_fetch_object($rs);
	$id_testa = $row->ID_TESTA;
	$Query = "SELECT CODICEARTI, QUANTITA, UNMISURA, ESPLDISTIN, ID FROM DOCRIG WHERE ID_TESTA = $id_testa AND ID >= $rif";
	$rs = db_query($conn, $Query) or die(mysql_error()); 
	$row = mysql_fetch_object($rs);
	$fatt = 1 / $row->QUANTITA;
	$righe = 0;
	$warning = false;
	while($row = mysql_fetch_object($rs) and $row->ESPLDISTIN == "C") {
		$righe++;
		tableRow($righe, $row->CODICEARTI, $row->UM, $row->QUANTITA * $fatt);
	}
	tableFooter();
}

function mostraDistinta($articolo) {
	global $anno, $conn, $maga, $rif, $copy;
	
    $aComp[] = array("codice" => "", "consumo" => 0, "um" => "");
	tableHeader();
	
	$Query = "SELECT DATADOC, U_DTESPLD FROM DOCRIG WHERE ID = $rif";
	$rs = db_query($conn, $Query) or die(mysql_error()); 
	$row = mysql_fetch_object($rs);
	// ROBERTO - 24.09.2020 - Vaccari ha di nuovo cambiato idea e adesso non vuole più i codici duplicati.
//	$nCompLen  = xEsplodiNR($articolo, $row->U_DTESPLD, 1, &$aComp, 0, 0);
	$nCompLen  = xEsplodi($articolo, $row->U_DTESPLD, 1, &$aComp, 0, 0);
	
	$warning = false;	
	for($j = 1; $j <= $nCompLen; $j++) {
		tableRow($j, $aComp[$j][codice], $aComp[$j][um], $aComp[$j][consumo]);
	}
	
	tableFooter();
} // fine funzione mostraDistinta


?>