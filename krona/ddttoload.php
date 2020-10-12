<?php

/************************************************************************/
/* CASASOFT ArcaWeb                               		      			*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2017 by Roberto Ceccarelli                        */
/* http://strawberryfield.altervista.org								*/
/*                                                                      */
/************************************************************************/

include("header.php");
include("db-utils.php");

$inc = <<<EOT
  $('#table1').dataTable().yadcf([
	    {column_number : 8, column_data_type: "html", html_data_type: "text", filter_type: "range_date", date_format: "dd/mm/yyyy"},
	    {column_number : 0, filter_type: "text"},
	    {column_number : 1, filter_type: "text"},
	    {column_number : 2, filter_type: "text"},
	    {column_number : 3, filter_type: "range_date", date_format: "dd/mm/yyyy"},
	    {column_number : 4, filter_type: "text"},
	    {column_number : 5, filter_type: "text"}
		]);

  $('#table2').dataTable().yadcf([
	    {column_number : 8, column_data_type: "html", html_data_type: "text", filter_type: "range_date", date_format: "dd/mm/yyyy"},
	    {column_number : 0, filter_type: "text"},
	    {column_number : 1, filter_type: "text"},
	    {column_number : 2, filter_type: "text"},
	    {column_number : 3, filter_type: "range_date", date_format: "dd/mm/yyyy"},
	    {column_number : 4, filter_type: "text"},
	    {column_number : 5, filter_type: "text"}
		]);
		
  $('#table3').dataTable().yadcf([
	    {column_number : 8, column_data_type: "html", html_data_type: "text", filter_type: "range_date", date_format: "dd/mm/yyyy"},
	    {column_number : 0, filter_type: "text"},
	    {column_number : 1, filter_type: "text"},
	    {column_number : 2, filter_type: "text"},
	    {column_number : 3, filter_type: "range_date", date_format: "dd/mm/yyyy"},
	    {column_number : 4, filter_type: "text"},
	    {column_number : 5, filter_type: "text"}
		]);
EOT;
head(dataTableInit($inc));
?>
<script type="text/javascript">
	function okTracciato(nBolla, id) {
		var r = false;
		r = confirm("Confermare Interamente Bolla n. " + nBolla + " ?");
		if (r) {
			var url = "okdoc.php?id=" + id;
			window.location.assign(url);
		} else {
			return false;
		}
	};

	function okTracciatoCT(nBolla, id, qta, qtaLav) {
		var r = false;
		if (qtaLav != qta) {
			r1 = confirm(" n. " + nBolla + " ?");
			if(r1) {

			}
		}

		r = confirm("Confermare Interamente Bolla n. " + nBolla + " ?");
		if (r) {
			var url = "okdoc.php?id=" + id;
			window.location.assign(url);
		} else {
			return false;
		}
	};
</script>
<?php
$cookie = preg_split("/\|/", $_SESSION['CodiceAgente']);
$connectionstring = db_connect($dbase);
$Query = "SELECT DESCRIZION FROM ANAGRAFE WHERE CODICE = \"" . $cookie[0] . "\"";
$queryexe = db_query($connectionstring, $Query) or die("$Query<br>" . mysql_error());
$row = db_fetch_row($queryexe);
banner("Bolle in attesa di acquisizione", $row[0]);


//print("$cookie[0]");
print("</br><div style=\"text-align: center;\"><span id=\"Title1\"><b>Bolle in Attesa di Conferma </b></span></div> </br>\n");

print("<table class=\"list\" id=\"table1\">\n");
common_header();
if ($cookie[0] == "F02884") {
	// Colonna quantit� richiesta, serve solo per le CT di eurService
	print("<th class=\"list\">Q.ta lanciata</th>\n");
}
print("<th class=\"list\">OK</th>\n");
print("<th class=\"list\">CANC</th>\n");
print("</tr>\n");
print("</thead>\n");
print("<tbody>\n");

//SQL query
// $Query = "SELECT U_BARDT.DATADOC, ";
// $Query .= "U_BARDT.ID, U_BARDT.NUMERODOCF, U_BARDT.RIF_TIPODOC, U_BARDT.RIF_NUMERODOC, U_BARDT.RIF_DATADOC, U_BARDR.CODICEARTI, MAGART.DESCRIZION AS DESART, U_BARDR.QUANTITA, U_BARDR.LOTTO ";
// $Query .= "FROM U_BARDT RIGHT JOIN U_BARDR ON U_BARDT.ID = U_BARDR.ID_TESTA LEFT JOIN MAGART ON MAGART.CODICE = U_BARDR.CODICEARTI ";
// $Query .= " WHERE U_BARDT.DEL=2 AND U_BARDR.DEL=2 AND U_BARDR.ESPLDISTIN = 'P' AND U_BARDT.CODICECF = \"" . $cookie[0] ;
// $Query .= "\" ORDER BY U_BARDT.DATADOC DESC, U_BARDT.RIF_NUMERODOC ASC ";

$Query = <<<EOT
SELECT U_BARDT.DATADOC, U_BARDT.ID, U_BARDT.NUMERODOCF, U_BARDT.TIPODOC AS BOTYPE, 
DOCTES.TIPODOC AS RIF_TIPODOC, DOCTES.NUMERODOC AS RIF_NUMERODOC, DOCTES.DATADOC AS RIF_DATADOC, 
U_BARDR.CODICEARTI, MAGART.DESCRIZION AS DESART, U_BARDR.QUANTITA, U_BARDR.LOTTO, U_BARDR.ID AS ID_RIGA, U_BARDR.QTAORIG 
FROM U_BARDT RIGHT JOIN U_BARDR ON U_BARDT.ID = U_BARDR.ID_TESTA LEFT JOIN MAGART ON MAGART.CODICE = U_BARDR.CODICEARTI 
LEFT OUTER JOIN DOCTES ON DOCTES.ID = U_BARDR.RIFFROMT 
WHERE U_BARDT.DEL=2 AND U_BARDR.DEL=2 AND U_BARDR.ESPLDISTIN = 'P' AND U_BARDT.CODICECF = '{$cookie[0]}'
ORDER BY U_BARDT.DATADOC DESC, DOCTES.NUMERODOC ASC
EOT;

//execute query 
$queryexe = db_query($connectionstring, $Query) or die("$Query<br>" . mysql_error());

//query database 
while ($row = mysql_fetch_object($queryexe)) {
	//format results
	common_body($row);
	if ($cookie[0] == "F02884") {
		// Colonna quantit� richiesta, serve solo per le CT di eurService
		print("<td class=\"list\">{$row->QTAORIG}</th>\n");
	}
	print("<td class=\"list\" align=\"center\">");
	//print("<a href=\"okdoc.php?id=" . $row->ID . "&id_riga=" . $row->ID_RIGA . "\" >");
	print("<button onclick='okTracciato(\"" . $row->BOTYPE . "_" . $row->NUMERODOCF . "\", " . $row->ID . ");'>");
	print("<img noborder src=\"../img/b_check.png\" height=\"20\">");
	print("</button></td>\n");
	//print("</a></td>\n");
	print("<td class=\"list\" align=\"center\">");
	print("<a href=\"deldoc.php?id=" . $row->ID . "\" >");
	print("<img noborder src=\"../img/b_drop.png\"></a></td>\n");
	print("</tr>\n");
}

print("</tbody>\n");
print("</table>\n");
print("<br>\n");

print("</br><div style=\"text-align: center;\"><span id=\"Title1\"><b>Bolle Confermate in Attesa di Acquisizione </b></span></div> </br>\n");

print("<table class=\"list\" id=\"table2\">\n");
common_header();
print("<th class=\"list\">CANC</th>\n");
print("</tr>\n");
print("</thead>\n");
print("<tbody>\n");

//SQL query
// $Query = "SELECT U_BARDT.DATADOC, ";
// $Query .= "U_BARDT.ID, U_BARDT.NUMERODOCF, U_BARDT.RIF_TIPODOC, U_BARDT.RIF_NUMERODOC, U_BARDT.RIF_DATADOC, U_BARDR.CODICEARTI, MAGART.DESCRIZION AS DESART, U_BARDR.QUANTITA, U_BARDR.LOTTO ";
// $Query .= "FROM U_BARDT RIGHT JOIN U_BARDR ON U_BARDT.ID = U_BARDR.ID_TESTA LEFT JOIN MAGART ON MAGART.CODICE = U_BARDR.CODICEARTI ";
// $Query .= " WHERE U_BARDT.DEL=0 AND U_BARDR.DEL=0 AND U_BARDR.ESPLDISTIN = 'P' AND U_BARDT.CODICECF = \"" . $cookie[0] ;
// $Query .= "\" ORDER BY U_BARDT.DATADOC DESC, U_BARDT.RIF_NUMERODOC ASC ";

$Query = "SELECT U_BARDT.DATADOC, U_BARDT.ID, U_BARDT.NUMERODOCF, U_BARDT.TIPODOC AS BOTYPE,  ";
$Query .= "DOCTES.TIPODOC AS RIF_TIPODOC, DOCTES.NUMERODOC AS RIF_NUMERODOC, DOCTES.DATADOC AS RIF_DATADOC, ";
$Query .= "U_BARDR.CODICEARTI, MAGART.DESCRIZION AS DESART, U_BARDR.QUANTITA, U_BARDR.LOTTO, U_BARDR.ID AS ID_RIGA ";
$Query .= "FROM U_BARDT RIGHT JOIN U_BARDR ON U_BARDT.ID = U_BARDR.ID_TESTA LEFT JOIN MAGART ON MAGART.CODICE = U_BARDR.CODICEARTI ";
$Query .= "LEFT OUTER JOIN DOCTES ON DOCTES.ID = U_BARDR.RIFFROMT ";
$Query .= "WHERE U_BARDT.DEL=0 AND U_BARDR.DEL=0 AND U_BARDR.ESPLDISTIN = 'P' AND U_BARDT.CODICECF = \"" . $cookie[0];
$Query .= "\" ORDER BY U_BARDT.DATADOC DESC, DOCTES.NUMERODOC ASC ";

//execute query 
$queryexe = db_query($connectionstring, $Query) or die("$Query<br>" . mysql_error());

//query database 
while ($row = mysql_fetch_object($queryexe)) {
	//format results
	common_body($row);
	print("<td class=\"list\" align=\"center\">");
	print("<a href=\"deldoc.php?id=" . $row->ID . "&id_riga=" . $row->ID_RIGA . "\" >");
	print("<img noborder src=\"../img/b_drop.png\"></a></td>\n");
	print("</tr>\n");
}


print("</tbody>\n");
print("</table>\n");
print("<br>\n");

print("</br><div style=\"text-align: center;\"><span id=\"Title1\"><b>Sfridi in Attesa di Acquisizione </b></span></div> </br>\n");

print("<table class=\"list\" id=\"table3\">\n");
common_header();
print("<th class=\"list\">CANC</th>\n");
print("</tr>\n");
print("</thead>\n");
print("<tbody>\n");

$Query = "SELECT U_BARDT.DATADOC, U_BARDT.ID, U_BARDT.NUMERODOCF, U_BARDT.TIPODOC AS BOTYPE,  ";
$Query .= "\"\" AS RIF_TIPODOC, \"\" AS RIF_NUMERODOC, \"\" AS RIF_DATADOC, ";
$Query .= "U_BARDR.CODICEARTI, MAGART.DESCRIZION AS DESART, U_BARDR.QUANTITA, U_BARDR.LOTTO, U_BARDR.ID AS ID_RIGA ";
$Query .= "FROM U_BARDT RIGHT JOIN U_BARDR ON U_BARDT.ID = U_BARDR.ID_TESTA LEFT JOIN MAGART ON MAGART.CODICE = U_BARDR.CODICEARTI ";
$Query .= "WHERE (U_BARDT.TIPODOC=\"RL\" OR U_BARDT.TIPODOC=\"KS\") AND U_BARDT.DEL=0 AND U_BARDR.DEL=0 AND U_BARDT.CODICECF = \"" . $cookie[0];
$Query .= "\"  ";

//execute query 
$queryexe = db_query($connectionstring, $Query) or die("$Query<br>" . mysql_error());

//query database 
while ($row = mysql_fetch_object($queryexe)) {
	//format results
	common_body($row);
	print("<td class=\"list\" align=\"center\">");
	print("<a href=\"deldoc.php?id=" . $row->ID . "&id_riga=" . $row->ID_RIGA . "\" >");
	print("<img noborder src=\"../img/b_drop.png\"></a></td>\n");
	print("</tr>\n");
}

//diconnect from database 
db_close($connectionstring);

print("</tbody>\n");
print("</table>\n");
print("<br>\n");

goMain();
footer();

function common_header()
{
	print("<thead>\n");
	print("<tr class=\"list\">\n");
	print("<th class=\"list\">Numero bolla</th>\n");
	print("<th class=\"list\">Tipo Doc. Rif.</th>\n");
	print("<th class=\"list\">Numero Doc. Rif.</th>\n");
	print("<th class=\"list\">Data Doc. Rif.</th>\n");
	print("<th class=\"list\">Articolo</th>\n");
	print("<th class=\"list\">Descrizione</th>\n");
	print("<th class=\"list\">Q.t&agrave</th>\n");
	print("<th class=\"list\">Lotto</th>\n");
	print("<th class=\"list\">Data</th>\n");
}

function common_body($row)
{
	print("<tr class=\"list\">\n");
	print("<td class=\"list\">" . $row->BOTYPE . " " . $row->NUMERODOCF . "</td>\n");
	print("<td class=\"list\">" . $row->RIF_TIPODOC . "</td>\n");
	print("<td class=\"list\">" . $row->RIF_NUMERODOC . "</td>\n");
	print("<td class=\"list\">" . format_date($row->RIF_DATADOC) . "</td>\n");
	print("<td class=\"list\">" . $row->CODICEARTI .  "</td>\n");
	print("<td class=\"list\">" . $row->DESART .  "</td>\n");
	print("<td class=\"list\">" . $row->QUANTITA .  "</td>\n");
	print("<td class=\"list\">" . $row->LOTTO .  "</td>\n");
	print("<td class=\"list\"><a href=\"moddb.php?id=$row->ID&id_riga=$row->ID_RIGA\" >");
	print(format_date($row->DATADOC) . "</a></td>\n");
	//        print("<td class=\"list\">" . format_date($row->DATADOC) . "</td>\n");
}
?>