<?php 
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2012 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php"); 
include("db-utils.php");

$conn = db_connect($dbase); 
 
$id_testa = $_GET['id'];
$anno = current_year();

mostraDistinta($id_testa);

print("<br>\n");
goEdit("ddttoload.php","Altra ricerca");
print("<br>\n");
goMain();
footer();


function mostraDistinta($id) {
	global $anno, $conn;

	$cookie = preg_split("/\|/",$_COOKIE['CodiceAgente']);
	$maga = "F" . substr($cookie[0],2);
	
	head();
	include("../libs/dropdown_lib.php");
    print("<script type=\"text/javascript\" src=\"../js/ajaxlib.js\"></script>\n");
    print("<script type=\"text/javascript\" src=\"../js/dbedit.js\"></script>\n");
	
	$Query = "SELECT * FROM U_BARDT WHERE ID=$id";
	$queryexe = db_query($conn, $Query) or die(mysql_error()); 
    $testa = mysql_fetch_object($queryexe);

//	$Query = "SELECT U_BARDR.*, MAGART.DESCRIZION ";
//	$Query .= "FROM U_BARDR INNER JOIN MAGART ON MAGART.CODICE = U_BARDR.CODICEARTI ";
//	$Query .= "WHERE U_BARDR.ID_TESTA=$id AND U_BARDR.ESPLDISTIN = \"P\"";
	$Query = "SELECT U_BARDR.* ";
	$Query .= "FROM U_BARDR ";
	$Query .= "WHERE U_BARDR.ID_TESTA=$id AND U_BARDR.ESPLDISTIN = \"P\"";
	$queryexe = db_query($conn, $Query) or die(mysql_error()); 
    $padre = mysql_fetch_object($queryexe);

//	$Query = "SELECT U_BARDR.*, MAGART.DESCRIZION ";
//	$Query .= "FROM U_BARDR INNER JOIN MAGART ON MAGART.CODICE = U_BARDR.CODICEARTI ";
//	$Query .= "WHERE U_BARDR.ID_TESTA=$id AND U_BARDR.ESPLDISTIN = \"C\"";
	$Query = "SELECT U_BARDR.* ";
	$Query .= "FROM U_BARDR ";
	$Query .= "WHERE U_BARDR.ID_TESTA=$id AND U_BARDR.ESPLDISTIN = \"C\"";
	$righe = db_query($conn, $Query) or die(mysql_error()); 
	
	banner("$padre->CODICEARTI - $padre->DESCRIZION");
	
	// scrittura tabella con i dati trovati
	print("<form id=\"db\" method=\"POST\" action=\"creadoc.php\">\n");
	print("<table id=\"tbl\" class=\"list\">\n");
	print("<thead>\n<tr class=\"list\">\n");
	print("<th class=\"list\">Codice</th>\n");
	print("<th class=\"list\">Descrizione</th>\n");
	print("<th class=\"list\">Qta</th>\n");
	print("<th class=\"list\">Lotto</th>\n");
	print("</tr>\n</thead>\n");
	print("<tbody id=\"tblbody\">\n");
	$j = 0;
	while($row = mysql_fetch_object($righe)) {
		$j++;
		print("<tr class=\"list\" id=\"riga$j\">\n");
		print("<td class=\"list\"><input type=\"text\" readonly=\"readonly\" size=\"16\" name=\"code$j\" id=\"code$j\" value=\"$row->CODICEARTI\"></td>\n");
		print("<td class=\"list\"><span id=\"desc$j\">" . $row->DESCRIZION . "</span></td>\n");

		// Quantita
		print("<td class=\"list\" style=\"text-align: center;\"><input type=\"text\" size=\"3\" readonly=\"readonly\" name=\"qta$j\" id=\"qta$j\" value=\"$row->QUANTITA\"></td>\n");
		
		// cerco tra i lotti se c'è qualcosa
		$Query = "SELECT PROGQTACAR-PROGQTASCA+PROGQTARET AS GIACENZA, LOTTO FROM MAGGIACL ";
		$Query .= "WHERE ARTICOLO = \"" . $row->CODICEARTI. "\" ";
		$Query .= "AND MAGAZZINO = \"$maga\" ";
		$Query .= "ORDER BY LOTTO ASC ";
		$rs = db_query($conn, $Query) or die(mysql_error()); 
		print("<td class=\"list\">\n");
		print(ddBox1("lotto$j"));
		$found=false;
		while ($rw = mysql_fetch_object($rs))	{
		    if($rw->GIACENZA > 0) { 
				print("<option value=\"" . $rw->LOTTO . "\"");
				if( $rw->LOTTO == $row->LOTTO) {
					print(" selected=\"selected\"");
					$found=true;
				}
				print(">" . $rw->LOTTO . " - Giac." . $rw->GIACENZA . "</option>\n");
			}  
		}
		if(!$found) {
			print("<option value=\"" . $row->LOTTO . "\" selected=\"selected\">" . $row->LOTTO . "</option>\n");
		}

//		print("</select></td>\n");
		print(ddBox2("lotto$j"));
		print("</td>\n");

		
		// chiusura riga
		print("</tr>\n");
	}  
	print("</tbody>\n</table>\n");
	print("<input type=\"hidden\" name=\"count\" id=\"count\" value=\"$j\">\n");
	print("<input type=\"hidden\" name=\"padre\" id=\"padre\" value=\"$padre->CODICEARTI\">\n");
	print("<input type=\"hidden\" name=\"lottopadre\" id=\"lottopadre\" value=\"$padre->LOTTO\">\n");
	print("<input type=\"hidden\" name=\"rift\" id=\"rift\" value=\"$padre->RIFFROMT\">\n");
	print("<input type=\"hidden\" name=\"rifr\" id=\"rifr\" value=\"$padre->RIFFROMR\">\n");
	print("<input type=\"hidden\" name=\"quantita\" id=\"quantita\" value=\"$padre->QUANTITA\">\n");
	print("<input type=\"hidden\" name=\"idriga\" id=\"idriga\" value=\"\">\n");
	print("<input type=\"hidden\" name=\"idtesta\" id=\"idtesta\" value=\"$testa->ID\">\n");
	print("<input type=\"hidden\" name=\"numerodocf\" id=\"numerodocf\" value=\"$testa->NUMERODOCF\">\n");
	print("<input type=\"submit\" id=\"btnok\" value=\"Ok\">\n");
	
	print("</form>\n");


} // fine funzione mostraDistinta

?>
