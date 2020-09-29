<?php
/************************************************************************/
/* CASASOFT ArcaWeb                               		      			*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2012 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php");
include("db-utils.php");
head();

$maga = $_GET['maga'];
$art = $_GET['art'];
$eserc = $_GET['esercizio'];
$connectionstring = db_connect($dbase);
?>
<style type="text/css">
	table {
		overflow:hidden;
		border:1px solid #d3d3d3;
		background:#ccffcc;
		width:70%;
		margin: 0 auto 0;
		-moz-border-radius:5px; /* FF1+ */
		-webkit-border-radius:5px; /* Saf3-4 */
		border-radius:5px;
		-moz-box-shadow: 0 0 4px rgba(0, 0, 0, 0.2);
		-webkit-box-shadow: 0 0 4px rgba(0, 0, 0, 0.2);
		font-family: verdana, helvetica;
		font-size: 10pt;
	}

	th, td {padding:5px 15px 5px; text-align:center;}

	th {padding-top:10px; text-shadow: 1px 1px 1px #fff; background:#e8eaeb; }

	th {
		background: -moz-linear-gradient(100% 30% 90deg, #ededed, #e8eaeb);
		background: -webkit-gradient(linear, 0% 0%, 0% 50%, from(#e8eaeb), to(#ededed));
	}

	td {border-top:1px solid #e0e0e0; border-right:1px solid #e0e0e0;}

	tr.odd-row td {background:#ccffcc;}

	td.first, th.first {text-align:left}

	td.last {border-right:none;}

	tr:first-child th.first {
		-moz-border-radius-topleft:5px;
		-webkit-border-top-left-radius:5px; /* Saf3-4 */
	}

	tr:first-child th.last {
		-moz-border-radius-topright:5px;
		-webkit-border-top-right-radius:5px; /* Saf3-4 */
	}

	tr:last-child td.first {
		-moz-border-radius-bottomleft:5px;
		-webkit-border-bottom-left-radius:5px; /* Saf3-4 */
	}

	tr:last-child td.last {
		-moz-border-radius-bottomright:5px;
		-webkit-border-bottom-right-radius:5px; /* Saf3-4 */
	}
</style>
<?php
$Query = "SELECT DESCRIZION, LOTTI FROM MAGART WHERE CODICE = \"$art\"";
$queryexe = db_query($connectionstring, $Query) or die(mysql_error() );
$row = db_fetch_row($queryexe);
$descrArt = $row[0];
banner(_("Scheda di magazzino"),"$art - " . $descrArt);
$isLotti = $row[1];
if($isLotti){
	//
	$Query = "SELECT LOTTO, MAGGIACL.PROGQTACAR, MAGGIACL.PROGQTASCA, MAGGIACL.PROGQTARET, PROGQTACAR-PROGQTASCA+PROGQTARET AS GIACENZA " ;
	$Query .= "FROM MAGGIACL ";
	$Query .= "WHERE ARTICOLO = \"" . trim($art). "\" ";
	$Query .= "AND MAGAZZINO = \"$maga\" ";
	//$Query .= "ORDER BY LOTTO DESC ";
	$queryexe = db_query($connectionstring, $Query) or die(mysql_error());

	print("</br><div style=\"text-align: center;\"><span id=\"Title1\"><b>" . _("Situazione Giacenza Lotti") . "</b></span></div><div> </br>\n");

		print("<table cellspacing=\"0\" align=\"center\">\n");
		print("<tr >\n");
		print("<th width='20.25%'>" . _("Lotto") . "</th>\n");
		print("<th width='12.2%'>" . _("Prog. Carico") . "</th>\n");
		print("<th width='12.2%'>" . _("Prog. Scarico") . "</th>\n");
		print("<th width='12.2%'>" . _("Prog. Rettifica") . "</th>\n");
		print("<th width='12.2%'>" . _("Prog. Mov. WEB") . "</th>\n");
		print("<th width='12.2%'>" . _("Giacenza") . "</th>\n");
		print("<th width='6.25%'>" . _("Etich. PZ") . "</th>\n");
		print("<th width='6.25%'>" . _("Etich. CF") . "</th>\n");
		print("<th width='6.25%'>" . _("Etich. SC") . "</th>\n");
		print("</tr>\n");

	while($row = db_fetch_row($queryexe)) {

		$lotto = $row[0];
		$carico = number($row[1]);
		$scarico = number($row[2]);
		$rettifica = number($row[3]);
		$nGiacWeb = webMovs::giacWebMov($maga, $art, $lotto);
		$giacWeb = number($nGiacWeb);
		$giacenza = number($row[4]+$nGiacWeb);
		print ("<tr >\n");
		print ("<td ><b>" . $lotto . "</b></td>\n");
		print ("<td >$carico</td>\n");
		print ("<td >$scarico</td>\n");
		print ("<td >$rettifica</td>\n");
		print ("<td >$giacWeb</td>\n");
		print ("<td ><b>$giacenza</b></td>\n");
		print_label('etich1lotti_unificate_PZ.php', '88x36 - Pezzi', $art, $lotto, '', '', '');
		print_label('etich1lotti_unificate.php', '88x36 - Confezioni', $art, $lotto, '', '', '');
		print_label('etich1lotti_unificate_SC.php', '88x36 - Scatole', $art, $lotto, '', '', '');
		print ("</tr>\n");

	}
	print("</table>\n<br>\n");
	print("</div>");
} else {

	print("</br><div style=\"text-align: center;\"><span id=\"Title1\"><b>" . _("Stampa Etichette") . "</b></span></div><div> </br>\n");
	print("<table style='width:25%;' cellspacing=\"0\" align=\"center\">\n");
	print("<tr >\n");
	print("<th width='3'>" . _("Etich. PZ") . "</th>\n");
	print("<th width='3'>" . _("Etich. CF") . "</th>\n");
	print("<th width='3'>" . _("Etich. SC") . "</th>\n");
	print("</tr>\n");

	print ("<tr >\n");
	print_label('etich1lotti_unificate_PZ.php', '88x36 - Pezzi', $art, '', '', '', '');
	print_label('etich1lotti_unificate.php', '88x36 - Confezioni', $art, '', '', '', '');
	print_label('etich1lotti_unificate_SC.php', '88x36 - Scatole', $art, '', '', '', '');
	print ("</tr>\n");


	print("</table>\n<br>\n");
	print("</div>");
}
// Giacenza iniziale
$Query = "SELECT MAGGIAC.GIACINI ";
$Query .= "FROM MAGGIAC ";
$Query .= "WHERE MAGAZZINO = \"$maga\" AND ARTICOLO = \"$art\" AND ESERCIZIO = \"$eserc\"";
$queryexe = db_query($connectionstring, $Query) or die(mysql_error());
$row = db_fetch_row($queryexe);
//echo $Query;

print("</br><div style=\"text-align: center;\"><span id=\"Title2\"><b>" . _("Movimentazione Articolo") . " </b></span></div><div> </br>\n");

print("<table cellspacing=\"0\" align=\"center\">\n");
print("<tr>\n");
print("<th width='12.5%'>" . _("Data") . "</th>\n");
print("<th width='18.75%'>" . _("Riferimento") . "</th>\n");
print("<th width='12.5%'>" . _("Carico") . "</th>\n");
print("<th width='12.5%'>" . _("Scarico") . "</th>\n");
print("<th width='12.5%'>" . _("Rettifica") . "</th>\n");
print("<th width='12.5%'>" . _("Progressivo") . "</th>\n");
print("<th width='18.75%'>" . _("Lotto") . "</th>\n");
print("</tr>\n");

$progr = $row[0];
$progrV = number($progr);
print ("<tr >\n");
print ("<td >01/01/" . current_year() . "</td>\n");
print ("<td >" . _("Giacenza iniziale") . "</td>\n");
print ("<td  style=\"text-align: right;\">" . number($row[0]) . "</td>\n");
print ("<td style=\"text-align: right;\">&nbsp;</td>\n");
print ("<td  style=\"text-align: right;\">&nbsp;</td>\n");
print ("<td style=\"text-align: right;\"><b>$progrV</b></td>\n");
print ("<td>&nbsp;</td>\n");
print ("</tr>\n");

//query database
$Query = "SELECT QUANTITA, QTACAR, QTASCAR, QTARET, DATAMOV, RIFDOC, LOTTO ";
$Query .= "FROM MAGMOV ";
$Query .= "WHERE CODICEARTI = \"$art\" AND MAGAZZINO = \"$maga\" ";
$Query .= "ORDER BY DATAMOV ";

$queryexe = db_query($connectionstring, $Query) or die(mysql_error());
while($row = db_fetch_row($queryexe)) {
    $progr += ($row[1] > 0 || $row[3] > 0 ? $row[0] : -$row[0]);
	$progrV = number($progr);
	print ("<tr >\n");
	print ("<td >" . format_date($row[4]) . "</td>\n");
	print ("<td >" . $row[5] . "</td>\n");
	print ("<td style=\"text-align: right;\">" . ($row[1] > 0 ? number($row[0]) : "&nbsp;") . "</td>\n");
	print ("<td style=\"text-align: right;\">" . ($row[2] > 0 ? number($row[0]) : "&nbsp;") . "</td>\n");
    print ("<td style=\"text-align: right;\">" . ($row[3] > 0 ? number($row[0]) : $row[3] < 0 ? number(-$row[0]) : "&nbsp;") . "</td>\n");
	print ("<td style=\"text-align: right;\"><b>$progrV</b></td>\n");
	print ("<td >" . $row[6] . "</td>\n");
	print ("</tr>\n");
    }

//diconnect from database
db_close($connectionstring);
print("</table>\n<br>\n");
print("</div>");



print("</br><div style=\"text-align: center;\"><span id=\"Title2\"><b>" . _("Movimentazione WEB") . "<br />
						<a href=\"ddttoload.php\">DDT Non Ancora Acquisite</a> </b></span></div><div> </br>\n");

print("<table cellspacing=\"0\" align=\"center\">\n");
print("<tr>\n");
print("<th width='12.5%'>" . _("Data") . "</th>\n");
print("<th width='18.75%'>" . _("Riferimento") . "</th>\n");
print("<th width='12.5%'>" . _("Carico") . "</th>\n");
print("<th width='12.5%'>" . _("Scarico") . "</th>\n");
print("<th width='12.5%'>" . _("Rettifica") . "</th>\n");
print("<th width='12.5%'>" . _("Progressivo") . "</th>\n");
print("<th width='18.75%'>" . _("Lotto") . "</th>\n");
print("</tr>\n");

$aWebMov = webMovs::getWebMovs($maga, $art);
$nWebMov = count($aWebMov);
$i=0;
while($i<$nWebMov){
	$progr += $aWebMov[$i]['CARICO']-$aWebMov[$i]['SCARICO'];
	$progrV = number($progr);
	print ("<tr >\n");
	print ("<td >" . format_date($aWebMov[$i]['DATAMOV']) . "</td>\n");
	print ("<td >" . $aWebMov[$i]['RIFDOC'] . "</td>\n");
	print ("<td style=\"text-align: right;\">" . ($aWebMov[$i]['CARICO'] > 0 ?  number($aWebMov[$i]['CARICO']) : "&nbsp;") . "</td>\n");
	print ("<td style=\"text-align: right;\">" . ($aWebMov[$i]['SCARICO'] > 0 ?  number($aWebMov[$i]['SCARICO']) : "&nbsp;") . "</td>\n");
  print ("<td style=\"text-align: right;\">". "&nbsp;" ." </td>\n");
	print ("<td style=\"text-align: right;\"><b>$progrV</b></td>\n");
	print ("<td >" . $aWebMov[$i]['LOTTO'] . "</td>\n");
	print ("</tr>\n");
	$i++;
}

print("</table>\n<br>\n");
print("</div>");

print("<br>\n");
print("<a class=\"bottommenu\" href=\"giornalemaga.php\">");
print("<img style=\"border: 0px;\" src=\"../img/05_edit.gif\" alt=\"" . _("Menu precedente") ."\">" . _("Menu precedente") ."</a>\n");
goMain();
footer();

function print_label($link, $desc, $articolo, $lotto, $descart, $code, $cliven = '', $dest = '', $dEvas = '')
{
    print('<td style="text-align: center;">');
    if ($articolo == '') {
        print('&nbsp;');
    } else {
        print("<a target=\"_blank\" href=\"$link?art=".urlencode(trim($articolo)).'&amp;lotto='.urlencode(trim($lotto)));
        print('&amp;desc='.urlencode(trim($descart)).'&amp;code='.urlencode(trim($code)).'&amp;cliven='.$cliven.'&amp;clidest='.$dest.'&amp;devas='.$dEvas);
        print("\" title=\"$desc\">\n<img style=\"border: none;\" src=\"../img/printer.png\" alt=\"$desc\"></a>\n");
    }
    print("</td>\n");
}

?>
