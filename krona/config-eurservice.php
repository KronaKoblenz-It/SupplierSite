<?php
/************************************************************************/
/* CASASOFT ArcaWeb                               		      			*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2020 by Roberto Ceccarelli                        */
/* http://strawberryfield.altervista.org								*/
/*                                                                      */
/************************************************************************/

include("header.php");
include("db-utils.php");
$inc = <<<EOT
  $('#maintable').dataTable().yadcf([
	    {column_number : 0, filter_type: "text"},
	    {column_number : 1, filter_type: "text"}
		]);
EOT;

head(dataTableInit($inc));
session_start();
$connectionstring = db_connect($dbase);

$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
$maga = "F" . substr($cookie[0],2);
$forn = $cookie[0];
$macchina = trim($_GET["gruppo"]);

$Query = "SELECT DESCRIZION FROM ANAGRAFE WHERE CODICE = \"$forn\"";
$queryexe = db_query($connectionstring, $Query) or die("$Query<br>" . mysql_error() );
$row = mysql_fetch_object($queryexe);
banner(_("Configurazione $macchina"), $row->DESCRIZION);

$eserc = current_year();



//Lista articoli da considerare
$Query = <<<EOT
select GESTIONE_ARTICOLI.CODICEARTI, MAGART.DESCRIZION, GESTIONE_ARTICOLI.TIPODOC
from GESTIONE_ARTICOLI inner join MAGART on MAGART.CODICE = GESTIONE_ARTICOLI.CODICEARTI
where GESTIONE_ARTICOLI.MACCHINA = '$macchina'
order by GESTIONE_ARTICOLI.CODICEARTI
EOT;
$queryexe = db_query($connectionstring, $Query) or die(mysql_error());

print("<table id=\"maintable\" class=\"list\">\n");
print("<thead>\n");
print("<tr class=\"list\">\n");
print("<th class=\"list\">" . _("Articolo") . "</th>\n");
print("<th class=\"list\">" . _("Descrizione") . "</th>\n");
print("<th class=\"list\">" . _("Tipo Doc.") . "</th>\n");
print("<th class=\"list\">&nbsp;</th>\n");
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
<td class="list">
<form action="macchine_updtipodoc.php">
<input type="hidden" name="codicearti" value="{$row[0]}">
<input type="hidden" name="macchina" value="$macchina">
<input type="input" size="3" name="tipodoc" value="{$row[2]}">
&nbsp;<input type="submit" value="Aggiorna">
</form>
</td>
<td class="list">
<form action="macchine_delarticolo.php">
<input type="hidden" name="codicearti" value="{$row[0]}">
<input type="hidden" name="macchina" value="$macchina">
&nbsp;<input type="submit" value="Cancella">
</td>
</tr>
</form>
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
