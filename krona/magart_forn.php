<?php
/************************************************************************/
/* CASASOFT ArcaWeb                               		      			*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2018 by Roberto Ceccarelli                        */
/* http://strawberryfield.altervista.org								*/
/*                                                                      */
/************************************************************************/

include("header.php");
include("db-utils.php");
$inc = <<<EOT
  $('#maintable').dataTable().yadcf([
	    {column_number : 0, column_data_type: "html", html_data_type: "text", filter_type: "text"},
	    {column_number : 1, filter_type: "text"}
		]);
EOT;

head(dataTableInit($inc));
session_start();
$connectionstring = db_connect($dbase);

$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
$maga = "F" . substr($cookie[0],2);
$forn = $cookie[0];

$Query = "SELECT DESCRIZION FROM ANAGRAFE WHERE CODICE = \"$forn\"";
$queryexe = db_query($connectionstring, $Query) or die("$Query<br>" . mysql_error() );
$row = mysql_fetch_object($queryexe);
banner(_("Articoli di magazzino"), $row->DESCRIZION);

$eserc = current_year();



//Lista articoli da considerare
$Query = <<<EOT
select CODICE, DESCRIZION, UNMISURA, PESOUNIT, GGRIOR, LOTTOMIN, LOTTORIOR
from MAGART
where FORNSTD = '$forn'
and STATOART not in ('2', '3', '5')
order by CODICE
EOT;
$queryexe = db_query($connectionstring, $Query) or die(mysql_error());

print("<table id=\"maintable\" class=\"list\">\n");
print("<thead>\n");
print("<tr class=\"list\">\n");
print("<th class=\"list\">" . _("Articolo") . "</th>\n");
print("<th class=\"list\">" . _("Descrizione") . "</th>\n");
print("<th class=\"list\">" . _("Peso") . "</th>\n");
print("<th class=\"list\">" . _("U.M.") . "</th>\n");
print("<th class=\"list\">" . _("Giorni riordino") . "</th>\n");
print("<th class=\"list\">" . _("Lotto minimo") . "</th>\n");
print("<th class=\"list\">" . _("Multipli del lotto") . "</th>\n");
print("</tr>\n");
print("</thead>\n");
print("<tbody>\n");

//query database
while($row = db_fetch_row($queryexe))
    {
    //format results
	$htmlrow = <<<EOT
<tr class="list">
<td class="list">{$row[0]}</td>
<td class="list">{$row[1]}</td>
<td class="list" align="right">{$row[3]}</td>
<td class="list">{$row[2]}</td>
<td class="list" align="right">
<form action="write_ggrior.php">
<input type="hidden" name="codice" value="{$row[0]}">
<input type="number" name="ggrior" value="{$row[4]}">
&nbsp;<input type="submit" value="Aggiorna">
</form></td>
<td class="list" align="right">{$row[5]}</td>
<td class="list" align="right">{$row[6]}</td>
</tr>
EOT;
    print ("$htmlrow\n");
    }

//diconnect from database
db_close($connectionstring);
print("</tbody>\n");
print("</table>\n");

print("<br>\n");
goMain();
footer();
?>
