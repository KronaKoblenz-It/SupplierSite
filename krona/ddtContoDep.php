<?php

include("header.php");
include("db-utils.php");
$inc = <<<EOT
  $('#maintable').dataTable({ "order": [[ 0, "desc" ]] }).yadcf([
	    {column_number : 0, column_data_type: "html", html_data_type: "text", filter_type: "range_date", date_format: "yyyy/mm/dd"},
	    {column_number : 1, filter_type: "text"},
	    {column_number : 2, filter_type: "auto_complete"},
	    {column_number : 3, filter_type: "auto_complete"},
	    {column_number : 4, filter_type: "text"},
	    {column_number : 5, column_data_type: "html", html_data_type: "text", filter_type: "range_date", date_format: "yyyy/mm/dd"}
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
print("<th class=\"list\">" . _("Destinazione") . "</th>\n");
print("<th class=\"list\">" . _("Rif. Doc. Sped.") . "</th>\n");
print("<th class=\"list\">" . _("Data Spedizione") . "</th>\n");
print("<th class=\"list\">&nbsp;</th>\n");
print("</tr>\n</thead>\n<tbody>\n");

//SQL query
$Query = <<<EOT
SELECT DOCTES.DATADOC, DOCTES.ID, DOCTES.NUMERODOCF, DOCTES.TIPODOC, DOCTES.NUMERODOC
, ANAGRAFE.DESCRIZION, DOCTES.CODICECF, DOCTES.DATADOCFOR, DESTINAZ.RAGIONESOC
 FROM DOCTES INNER JOIN ANAGRAFE ON ANAGRAFE.CODICE = DOCTES.CODICECF
 LEFT OUTER JOIN DESTINAZ ON DESTINAZ.CODICEDES = DOCTES.DESTDIV AND DESTINAZ.CODICECF = DOCTES.CODICECF
 WHERE DOCTES.CODICECF = '$cf'
 AND (TIPODOC='CD') ORDER BY DATADOC DESC
EOT;

//execute query
$queryexe = db_query($connectionstring, $Query) or die(mysql_error());

//query database
while($row = db_fetch_row($queryexe))
{
	$data = format_date_2($row[0]);
	$id = $row[1];
	$numeroDocF = $row[2];
	$addr = $row[3] . " " . $row[4];
	$dtSped = format_date_2($row[7]);
	$name = ($row[6] != $cf) ? "KRONA KOBLENZ S.P.A." : htmlentities($row[5]);
	$dest = (strlen($row[8]) == 0) ? "" : htmlentities($row[8]);
	
	//format results
	$html = <<<EOT
<tr class="list">
<td class="list">$data</td>
<td class="list"><a href="cdep-detail.php?id=$id" >$addr</a></td>
<td class="list">$name</td>
<td class="list">$dest</td>
<td class="list">$numeroDocF</td>
<td class="list">$dtSped</td>
<td class="list" style="text-align: center;"><a href="cd2xls.php?id=$id" >
<img src="../img/download.png" alt="download" style="border: none;">
</a></td>
</tr>
EOT;
	print("$html\n");
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
