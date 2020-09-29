<?php
/************************************************************************/
/* CASASOFT ArcaWeb                               		      			*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2015 by Roberto Ceccarelli                        */
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
$isCdep = false;

$Query = "SELECT CODICEMAG FROM u_magcdep WHERE CODICECF=\"$forn\"";
$queryexe = db_query($connectionstring, $Query) or die("$Query<br>" . mysql_error() );
if($row = mysql_fetch_object($queryexe)) {
	$isCdep = true;
	$maga = $row->CODICEMAG;
}
$finito = "";

$Query = "SELECT DESCRIZION FROM ANAGRAFE WHERE CODICE = \"$forn\"";
$queryexe = db_query($connectionstring, $Query) or die("$Query<br>" . mysql_error() );
$row = mysql_fetch_object($queryexe);
banner(_("Situazione di magazzino") . " ".current_year(),$row->DESCRIZION);

/*$Query = "SELECT finito FROM u_invfine WHERE magazzino = '" . $maga . "'";
$queryexe = db_query($connectionstring, $Query) or die(mysql_error());
$row = db_fetch_row($queryexe);
$finito = $row[0];
$eserc = $row[0] == 1 ? "2013" : "2012";*/
$eserc = current_year();

//echo $Query;
print("".$finito."");
//echo $eserc;

//Lista articoli da considerare
$Query = "SELECT MAGGIAC.ARTICOLO, (MAGGIAC.GIACINI+MAGGIAC.PROGQTACAR-MAGGIAC.PROGQTASCA+MAGGIAC.PROGQTARET) AS GIACENZA, ";
$Query .= "MAGOI.ORDINATO, MAGOI.IMPEGNATO, ";
$Query .= "MAGART.DESCRIZION, MAGART.PESOUNIT, MAGART.UNMISURA, MAGART.SCORTAMIN ";
$Query .= "FROM MAGGIAC INNER JOIN MAGART ON MAGART.CODICE = MAGGIAC.ARTICOLO ";
$Query .= "LEFT OUTER JOIN MAGOI ON MAGOI.ARTICOLO = MAGGIAC.ARTICOLO AND MAGOI.MAGAZZINO = MAGGIAC.MAGAZZINO ";
$Query .= "WHERE MAGGIAC.MAGAZZINO = \"$maga\" AND MAGGIAC.ESERCIZIO =\"$eserc\" ";
$Query .= "ORDER BY ARTICOLO";
$queryexe = db_query($connectionstring, $Query) or die(mysql_error());

//print("<table width=\"100%\" border=\"1\" cellspacing=\"1\" cellpadding=\"1\" bgcolor=\"#000000\">\n");
print("<table id=\"maintable\" class=\"list\">\n");
print("<thead>\n");
print("<tr class=\"list\">\n");
print("<th class=\"list\">" . _("Articolo") . "</th>\n");
print("<th class=\"list\">" . _("Descrizione") . "</th>\n");
if(!$isCdep){
  print("<th class=\"list\">" . _("Peso") . "</th>\n");
}
print("<th class=\"list\">" . _("U.M.") . "</th>\n");
print("<th class=\"list\">" . _("Giacenza") . "</th>\n");
print("<th class=\"list\">" . _("Ordinato") . "</th>\n");
print("<th class=\"list\">" . _("Impegnato") . "</th>\n");
print("<th class=\"list\">" . _("Disponibile") . "</th>\n");
if($isCdep){
  print("<th class=\"list\">" . _("Scorta Min.") . "</th>\n");
}
print("<th class=\"list\">&nbsp;</th>\n");
print("</tr>\n");
print("</thead>\n");
print("<tbody>\n");

//query database
    while($row = db_fetch_row($queryexe))
    {
    //format results
    $giac = $row[1]+webMovs::giacWebMov($maga, $row[0], '');
    print ("<tr class=\"list\">\n");
    print ("<td class=\"list\"><a href=\"giacArtDetail.php?art=" . urlencode($row[0]) . "&maga=$maga&esercizio=$eserc\" >" . $row[0] . "</a></td>\n");
    print ("<td class=\"list\">" . $row[4] . "</td>\n");
    if(!$isCdep){
      print ("<td class=\"list\" align=\"right\">" . number(xRound($row[5])) . " KG </td>\n");
    }
    print ("<td class=\"list\">" . $row[6] . "</td>\n");
    print ("<td class=\"list\" align=\"right\">" . number(xRound($giac)) . "</td>\n");
    print ("<td class=\"list\" align=\"right\">" . number(xRound($row[2])) . "</td>\n");
    print ("<td class=\"list\" align=\"right\">" . number(xRound($row[3])) . "</td>\n");
    print ("<td class=\"list\" align=\"right\">" . number(xRound($giac-$row[3])) . "</td>\n");
    if($isCdep){
      print ("<td class=\"list\" align=\"right\">" . number(xRound($row[7])) . "</td>\n");
    }
	print ("<td class=\"list\" style=\"text-align: center;\"><a href=\"schedaartx.php?art=" . urlencode($row[0]) . "&maga=$maga&esercizio=$eserc\" >" );
	print ("<img src=\"../img/download.png\" alt=\"download\" style=\"border: none;\">");
	print ("</a></td>\n");
    print ("</tr>\n");
    }

//diconnect from database
db_close($connectionstring);
print("</tbody>\n");
//print("</table>\n");

//print("<table class=\"list\">\n");
print("<tfoot>\n");

print("<tr class=\"list\">\n");
print("<td class=\"list\" colspan=\"9\" align=\"right\" valign=\"center\">
    <a href=\"schedaartx.php?maga=$maga&esercizio=$eserc\">
    <img src=\"../img/download.png\" alt=\"download\" style=\"border: none;\">
    <strong> Tracciato XML </strong> </a>
    <br />
    <a href=\"schedaartxls.php?maga=$maga&esercizio=$eserc\" >
    <img src=\"../img/download.png\" alt=\"download\" style=\"border: none;\">
    <strong> Tracciato XLS </strong> </a>
    </td>\n");

print("</tr>\n");

print("</tfoot>\n</table>\n");
print("<br>\n");
goMain();
footer();
?>
