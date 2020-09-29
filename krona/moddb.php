<?php
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2016 by Roberto Ceccarelli                        */
/* http://strawberryfield.altervista.org								*/
/*                                                                      */
/************************************************************************/

include("header.php");
include("db-utils.php");

$conn = db_connect($dbase);

$id_testa = $_GET['id'];
$id_riga = (isset($_GET["id_riga"]) ? $_GET["id_riga"] : 0);
$anno = current_year();

mostraDistinta($id_testa, $id_riga);

print("<br>\n");
goEdit("ddttoload.php",_("Altra ricerca"));
print("<br>\n");
goMain();
footer();


function mostraDistinta($id, $id_riga) {
	global $anno, $conn;

    session_start();
	$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
	$maga = "F" . substr($cookie[0],2);

	head();
	include("../libs/dropdown_lib.php");
    print("<script type=\"text/javascript\" src=\"../js/ajaxlib.js\"></script>\n");
    print("<script type=\"text/javascript\" src=\"../js/dbedit.js\"></script>\n");

	$Query = "SELECT * FROM U_BARDT WHERE ID=$id";
	$queryexe = db_query($conn, $Query) or die("$Query<br>" . mysql_error());
    $testa = mysql_fetch_object($queryexe);

	if($id_riga != 0) {
		$Query = "SELECT U_BARDR.* FROM U_BARDR WHERE U_BARDR.ID=$id_riga";
	} else {
		$Query = "SELECT U_BARDR.* FROM U_BARDR WHERE U_BARDR.ID_TESTA=$id AND U_BARDR.ESPLDISTIN = 'P'";
	}
	$queryexe = db_query($conn, $Query) or die("$Query<br>" . mysql_error());
    $padre = mysql_fetch_object($queryexe);

	if($id_riga != 0) {
		$Query = "SELECT U_BARDR.* FROM U_BARDR WHERE U_BARDR.ID_TESTA=$id AND U_BARDR.ID_RIFRIGA = $id_riga AND U_BARDR.ESPLDISTIN = 'C'";
	} else {
		$Query = "SELECT U_BARDR.* FROM U_BARDR WHERE U_BARDR.ID_RIFRIGA=$id_riga AND U_BARDR.ESPLDISTIN = 'C'";
	}
	$righe = db_query($conn, $Query) or die("$Query<br>" . mysql_error());

	banner("$padre->CODICEARTI - $padre->DESCRIZION");

	// scrittura tabella con i dati trovati
	print("<form id=\"db\" method=\"POST\" action=\"creadoc.php\">\n");
	print("<table id=\"tbl\" class=\"list\">\n");
	print("<thead>\n<tr class=\"list\">\n");
	print("<th class=\"list\">" . _("Codice") . "</th>\n");
	print("<th class=\"list\">" . _("Descrizione") . "</th>\n");
	print("<th class=\"list\">" . _("Qta") . "</th>\n");
	print("<th class=\"list\">" . _("Lotto") . "</th>\n");
	print("</tr>\n</thead>\n");
	print("<tbody id=\"tblbody\">\n");
	$j = 0;
	while($row = mysql_fetch_object($righe)) {
		$j++;
		$art = $row->CODICEARTI;
		print("<tr class=\"list\" id=\"riga$j\">\n");
		print("<td class=\"list\"><input type=\"text\" readonly=\"readonly\" size=\"16\" name=\"code$j\" id=\"code$j\" value=\"$art\"></td>\n");
		print("<td class=\"list\"><span id=\"desc$j\">" . $row->DESCRIZION . "</span></td>\n");

		// Quantita
		print("<td class=\"list\" style=\"text-align: center;\"><input type=\"text\" size=\"3\" readonly=\"readonly\" name=\"qta$j\" id=\"qta$j\" value=\"$row->QUANTITA\"></td>\n");

		// cerco tra i lotti se c'� qualcosa
		$Query = <<<EOT
SELECT MAGGIACL.PROGQTACAR-MAGGIACL.PROGQTASCA+MAGGIACL.PROGQTARET AS GIACENZA, MAGGIACL.LOTTO 
,LOTTI.U_NOCE
FROM MAGGIACL inner join LOTTI on LOTTI.CODICE = MAGGIACL.LOTTO and LOTTI.CODICEARTI = MAGGIACL.ARTICOLO
WHERE MAGGIACL.ARTICOLO = '$art' AND MAGGIACL.MAGAZZINO = '$maga'
ORDER BY LOTTO DESC 
EOT;
		$rs = db_query($conn, $Query) or die("$Query<br>" . mysql_error());
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
				print(">" . $rw->LOTTO . ($rw->U_NOCE == 1 ? _(" (non CEE)") : "") . _(" - Giac.:") . $rw->GIACENZA . "</option>\n");
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
	hiddenField("count", $j);
	hiddenField("padre", $padre->CODICEARTI);
	hiddenField("lottopadre", $padre->LOTTO);
	hiddenField("rift", $padre->RIFFROMT);
	hiddenField("rifr", $padre->RIFFROMR);
	hiddenField("quantita", $padre->QUANTITA);
	hiddenField("idriga", $id_riga);
	hiddenField("idtesta", $testa->ID);
	hiddenField("numerodocf", $testa->NUMERODOCF);
    hiddenField("returntoddttoload", "true");
	//print("<input type=\"submit\" id=\"btnok\" value=\"Ok\">\n");

	print("</form>\n");

} // fine funzione mostraDistinta

?>
