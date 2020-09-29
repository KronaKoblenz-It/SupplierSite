<?php

include("header.php");
include("db-utils.php");
$inc = <<<EOT
  $('#maintable').dataTable({ "order": [[ 0, "desc" ]] }).yadcf([
	    {column_number : 0, column_data_type: "html", html_data_type: "text", filter_type: "range_date", date_format: "yyyy/mm/dd"},
	    {column_number : 1, filter_type: "text"},
	    {column_number : 2, filter_type: "auto_complete"},
	    {column_number : 3, filter_type: "text"},
	    {column_number : 4, column_data_type: "html", html_data_type: "text", filter_type: "range_date", date_format: "yyyy/mm/dd"}
		]);
EOT;

head(dataTableInit($inc));
$connectionstring = db_connect($dbase);

session_start();
$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
$cf = $cookie[0];
$Query = "SELECT DESCRIZION FROM ANAGRAFE WHERE CODICE = \"$cf\"";
$queryexe = db_query($connectionstring, $Query) or die(mysql_error() );
$row = db_fetch_row($queryexe);
banner(_("DDT Conto Deposito "),htmlentities($row[0]));

//magazzino
$Query = "SELECT CODICEMAG FROM u_magcdep WHERE CODICECF=\"$cf\"";
$queryexe = db_query($connectionstring, $Query) or die("$Query<br>" . mysql_error() );
if($row = mysql_fetch_object($queryexe)) {
	$maga = $row->CODICEMAG;
} else {
  $maga = "F" . substr($cf,2);
}

print("<table class=\"list\" id=\"maintable\">\n<thead>\n");
print("<tr class=\"list\">\n");
print("<th class=\"list\">" . _("Data") . "</th>\n");
print("<th class=\"list\">" . _("Ns. riferimento") . "</th>\n");
print("<th class=\"list\">" . _("Fornitore") . "</th>\n");
print("<th class=\"list\">" . _("Rif. Doc. Sped.") . "</th>\n");
print("<th class=\"list\">" . _("Data Spedizione") . "</th>\n");
print("<th class=\"list\">&nbsp;</th>\n");
print("</tr>\n</thead>\n<tbody>\n");

//SQL query
$Query = "SELECT DOCTES.DATADOC, DOCTES.ID, DOCTES.NUMERODOCF, DOCTES.TIPODOC, DOCTES.NUMERODOC ";
$Query .= ", ANAGRAFE.DESCRIZION, DOCTES.CODICECF, DOCTES.DATADOCFOR ";
$Query .= " FROM DOCTES INNER JOIN ANAGRAFE ON ANAGRAFE.CODICE = DOCTES.CODICECF";
$Query .= " WHERE MAGPARTENZ = \"$maga\"";
$Query .= " AND (TIPODOC=\"CP\") ORDER BY DATADOC DESC";

//execute query
$queryexe = db_query($connectionstring, $Query) or die(mysql_error());

//query database
while($row = db_fetch_row($queryexe))
{
  $data = format_date_2($row[0]);
  $addr = $row[3] . " " . $row[4];
  $dtSped = format_date_2($row[7]);
  if($row[6] != $cf) {
		$name = "KRONA KOBLENZ S.P.A.";
	} else {
		$name = htmlentities($row[5]);
	}
    //format results
  print ("<tr class=\"list\">\n");
  print ("<td class=\"list\">$data</td>\n");
  print ("<td class=\"list\"><a href=\"cp-rows.php?id=" . $row[1] . "\" >$addr</a></td>\n");
  print ("<td class=\"list\">$name</td>\n");
  print ("<td class=\"list\">" . $row[2] . "</td>\n");
  print ("<td class=\"list\">$dtSped</td>\n");
  print("<td class=\"list\" style=\"text-align: center;\"><a href=\"cd2xls.php?id=" . $row[1] . "\" >");
	print("<img src=\"../img/download.png\" alt=\"download\" style=\"border: none;\">");
	print("</a></td>\n");
  print ("</tr>\n");
}

//diconnect from database
db_close($connectionstring);

print("</tbody>\n<tfoot>\n<tr class=\"list\">\n");
/*print("<td class=\"list\" colspan=\"5\" align=\"right\" valign=\"center\"><a href=\"doc2xml.php?mode=tl\" >");
print("<h3>Scarica Tutto <img src=\"../img/download.png\" alt=\"download\" style=\"border: none;\">");
print("</h3></a></td>\n");*/
print("</tr>\n</tfoot>\n");

print("</table>\n");

print("<br>\n");
goMain();
footer();

?>
