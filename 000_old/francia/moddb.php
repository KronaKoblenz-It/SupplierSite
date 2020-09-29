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
 
$id_testa = $_GET['id'];
$anno = current_year();

mostraDistinta($id_testa);

print ("<br><a href=\"ddttoload.php\">Altra ricerca</a>\n");
print ("<br><a href=\"menu-for.php\">Menu principale</a>\n");
footer();


function mostraDistinta($id) {
	global $anno, $conn;
	
	head();
    print("<script type=\"text/javascript\" src=\"ajaxlib.js\"></script>\n");
    print("<script type=\"text/javascript\" src=\"dbedit.js\"></script>\n");
	
	$Query = "SELECT * FROM U_BARDT WHERE ID=$id";
	$queryexe = db_query($conn, $Query) or die(mysql_error()); 
    $testa = mysql_fetch_object($queryexe);

	$Query = "SELECT U_BARDR.*, MAGART.DESCRIZION ";
	$Query .= "FROM U_BARDR INNER JOIN MAGART ON MAGART.CODICE = U_BARDR.CODICEARTI ";
	$Query .= "WHERE U_BARDR.ID_TESTA=$id AND U_BARDR.ESPLDISTIN = \"P\"";
	$queryexe = db_query($conn, $Query) or die(mysql_error()); 
    $padre = mysql_fetch_object($queryexe);

	$Query = "SELECT U_BARDR.*, MAGART.DESCRIZION ";
	$Query .= "FROM U_BARDR INNER JOIN MAGART ON MAGART.CODICE = U_BARDR.CODICEARTI ";
	$Query .= "WHERE U_BARDR.ID_TESTA=$id AND U_BARDR.ESPLDISTIN = \"C\"";
	$righe = db_query($conn, $Query) or die(mysql_error()); 
	
	banner("$padre->CODICEARTI - $padre->DESCRIZION");
	
	// scrittura tabella con i dati trovati
	print("<form id=\"db\" method=\"POST\" action=\"creadoc.php\">\n");
	print("<table id=\"tbl\" border=\"1\">\n");
	print("<thead><tr><th>Codice</th><th>Descrizione</th><th>Qta</th><th>Lotto</th></tr></thead><tbody id=\"tblbody\">\n");
	$j = 0;
	while($row = mysql_fetch_object($righe)) {
		$j++;
		print("<tr id=\"riga$j\">\n");
		print("<td><input type=\"text\" readonly=\"readonly\" size=\"16\" name=\"code$j\" id=\"code$j\" value=\"$row->CODICEARTI\"></td>\n");
		print("<td><span id=\"desc$j\">" . $row->DESCRIZION . "</span></td>\n");

		// Quantita
		print("<td><input type=\"text\" size=\"3\" readonly=\"readonly\" name=\"qta$j\" id=\"qta$j\" value=\"$row->QUANTITA\"></td>\n");
		
		print("<td><input type=\"text\" size=\"12\" name=\"lotto$j\" id=\"lotto$j\" value=\"");
		print($row->LOTTO);
		print("\" onblur=\"validateLotto(this);\"></td>\n");
		
		// chiusura riga
		print("</tr>\n");
	}  
	print("</tbody></table>\n");
	print("<input type=\"hidden\" name=\"count\" id=\"count\" value=\"$j\">\n");
	print("<input type=\"hidden\" name=\"padre\" id=\"padre\" value=\"$padre->CODICEARTI\">\n");
	print("<input type=\"hidden\" name=\"lottopadre\" id=\"lottopadre\" value=\"$padre->LOTTO\">\n");
	print("<input type=\"hidden\" name=\"quantita\" id=\"quantita\" value=\"$padre->QUANTITA\">\n");
	print("<input type=\"hidden\" name=\"idriga\" id=\"idriga\" value=\"\">\n");
	print("<input type=\"hidden\" name=\"idtesta\" id=\"idtesta\" value=\"$testa->ID\">\n");
	print("<input type=\"hidden\" name=\"numerodocf\" id=\"numerodocf\" value=\"$testa->NUMERODOCF\">\n");
	print("<input type=\"submit\" id=\"btnok\" value=\"Ok\">\n");
	
	print("</form>\n");


} // fine funzione mostraDistinta

?>
