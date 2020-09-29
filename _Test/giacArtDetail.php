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
banner("Scheda di magazzino","$art - " . $row[0]);
$isLotti = $row[1];
if($isLotti){
	//
	$Query = "SELECT LOTTO, MAGGIACL.PROGQTACAR, MAGGIACL.PROGQTASCA, MAGGIACL.PROGQTARET, PROGQTACAR-PROGQTASCA+PROGQTARET AS GIACENZA " ;
	$Query .= "FROM MAGGIACL ";
	$Query .= "WHERE ARTICOLO = \"" . trim($art). "\" ";
	$Query .= "AND MAGAZZINO = \"$maga\" ";
	//$Query .= "ORDER BY LOTTO DESC ";
	$queryexe = db_query($connectionstring, $Query) or die(mysql_error()); 
	
	print("</br><div style=\"text-align: center;\"><span id=\"Title1\"><b>Situazione Giacenza Lotti</b></span></div><div> </br>\n");	
	
		print("<table cellspacing=\"0\" align=\"center\">\n");
		print("<tr >\n");
		print("<th >Lotto</th>\n"); 
		print("<th >Prog. Carico</th>\n"); 
		print("<th >Prog. Scarico</th>\n"); 
		print("<th >Prog. Rettifica</th>\n");
		print("<th >Giacenza</th>\n");
		print("</tr>\n");
	
	while($row = db_fetch_row($queryexe)) { 

		$progr = $row[0];
		print ("<tr >\n"); 
		print ("<td >" . $row[0] . "</td>\n"); 
		print ("<td >$row[1]</td>\n"); 
		print ("<td >$row[2]</td>\n"); 
		print ("<td >$row[3]</td>\n");
		print ("<td >$row[4]</td>\n");
		print ("</tr>\n"); 
		
	}
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

print("</br><div style=\"text-align: center;\"><span id=\"Title2\"><b>Movimentazione Articolo </b></span></div><div> </br>\n");

print("<table cellspacing=\"0\" align=\"center\">\n");
print("<tr>\n");
print("<th >Data</th>\n"); 
print("<th>Riferimento</th>\n"); 
print("<th >Carico</th>\n"); 
print("<th >Scarico</th>\n");
print("<th>Rettifica</th>\n");
print("<th >Progressivo</th>\n"); 
print("<th>Lotto</th>\n"); 
print("</tr>\n");

$progr = $row[0];
print ("<tr >\n"); 
print ("<td >01/01/" . current_year() . "</td>\n"); 
print ("<td >Giacenza iniziale</td>\n"); 
print ("<td  style=\"text-align: right;\">" . $row[0] . "</td>\n"); 
print ("<td style=\"text-align: right;\">&nbsp;</td>\n");
print ("<td  style=\"text-align: right;\">&nbsp;</td>\n");
print ("<td style=\"text-align: right;\">$progr</td>\n"); 
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
	print ("<tr >\n"); 
	print ("<td >" . format_date($row[4]) . "</td>\n"); 
	print ("<td >" . $row[5] . "</td>\n"); 
	print ("<td style=\"text-align: right;\">" . ($row[1] > 0 ? $row[0] : "&nbsp;") . "</td>\n");
	print ("<td style=\"text-align: right;\">" . ($row[2] > 0 ? $row[0] : "&nbsp;") . "</td>\n");
    print ("<td style=\"text-align: right;\">" . ($row[3] > 0 ? $row[0] : $row[3] < 0 ? -$row[0] : "&nbsp;") . "</td>\n");
	print ("<td style=\"text-align: right;\">$progr</td>\n"); 
	print ("<td >" . $row[6] . "</td>\n"); 
	print ("</tr>\n"); 
    } 

//diconnect from database 
db_close($connectionstring); 
print("</table>\n<br>\n");
print("</div>");
print("<br>\n");
print("<a class=\"bottommenu\" href=\"giornalemaga.php\">");
print("<img style=\"border: 0px;\" src=\"../img/05_edit.gif\" alt=\"Menu precedente\">Menu precedente</a>\n");
goMain();
footer();
?>