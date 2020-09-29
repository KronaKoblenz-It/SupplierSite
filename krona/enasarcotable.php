<?php
/************************************************************************/
/* CASASOFT ArcaWeb                               		 			    */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2014 by Roberto Ceccarelli                        */
/* http://strawberryfield.altervista.org                                */
/*                                                                      */
/************************************************************************/

include("header.php");
include("db-utils.php");

head();

session_start();
$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
$anno = $_GET["anno"];
$fornitore = $_GET["forn"];
banner("Situazione Enasarco anno $anno",$cookie[1]);

//connect to database 
$connectionstring = db_connect($dbase); 

//SQL quyery  
$Query = "SELECT TIPOAGE, IMPMIN, IMPMAX FROM RIT_ANA WHERE CODFOR ='$fornitore'";
$queryexe = db_query($connectionstring, $Query) or die(mysql_error()); 
$row = mysql_fetch_object($queryexe);
$tipoAgente = ($row->TIPOAGE == 1 ? "Monomandatario" : "Plurimandatario");
$impmin = $row->IMPMIN;
$impmax = $row->IMPMAX;
print("<p>Agente: $tipoAgente</p>\n");
print("<p>Importo minimale: $impmin</p>\n");
print("<p>Importo massimale: $impmax</p>\n<br>\n");

$decimali = 2;
print("<table class=\"list\" id=\"maintable\">\n");
print("<thead>\n<tr class=\"list\">\n");
print("<th class=\"list\">Data doc.</th>\n");
print("<th class=\"list\">Numero</th>\n");
print("<th class=\"list\">Tot. fattura</th>\n");
print("<th class=\"list\">Imponibile</th>\n");
print("<th class=\"list\">% Ditta</th>\n");
print("<th class=\"list\">Importo Ditta</th>\n");
print("<th class=\"list\">% Agente</th>\n");
print("<th class=\"list\">Importo Agente</th>\n");
print("<th class=\"list\">Progressivo</th>\n");
print("<th class=\"list\">Residuo</th>\n");
print("</tr>\n</thead>\n<tbody>\n"); 

$Query = <<<EOT
SELECT FTDATADOC, FTNUMDOC, TOTFATTURA, COMPENSI, PERENDIT, IMPENDIT, PERENAGE, IMPENAGE
FROM RIT_MOV
WHERE CODFOR='$fornitore' AND YEAR(FTDATADOC) = $anno
ORDER BY FTDATADOC, FTNUMDOC
EOT;
$queryexe = db_query($connectionstring, $Query) or die(mysql_error()); 

$sum_totfattura=0;
$sum_compensi=0;
$sum_impendit=0;
$sum_impenage=0;
$sum_prog=0;
$sum_res=$impmax;

while($row = mysql_fetch_object($queryexe)) { 
     
	$totfattura = $row->TOTFATTURA;
	$sum_totfattura += $totfattura;
	$compensi = $row->COMPENSI;
	$sum_compensi += $compensi;
	$impendit = $row->IMPENDIT;
	$sum_impendit += $impendit;
	$sum_prog += $impendit;
	$sum_res -= $impendit;
	$impenage = $row->IMPENAGE;
	$sum_impenage += $impenage;
	$sum_prog += $impenage;
	$sum_res -= $impenage;
	
    //format results 
    print ("<tr class=\"list\">\n"); 
    print ("<td class=\"list\">" . format_date($row->FTDATADOC) . "</td>\n"); 
    print ("<td class=\"list\">{$row->FTNUMDOC}</td>\n"); 
    print ("<td class=\"list\" align=\"right\">" . number($totfattura) . "</td>\n"); 
    print ("<td class=\"list\" align=\"right\">" . number($compensi) . "</td>\n"); 
    print ("<td class=\"list\" align=\"right\">{$row->PERENDIT}</td>\n"); 
    print ("<td class=\"list\" align=\"right\">" . number($impendit) . "</td>\n"); 
    print ("<td class=\"list\" align=\"right\">{$row->PERENAGE}</td>\n"); 
    print ("<td class=\"list\" align=\"right\">" . number($impenage) . "</td>\n"); 
    print ("<td class=\"list\" align=\"right\">" . number($sum_prog) . "</td>\n"); 
    print ("<td class=\"list\" align=\"right\">" . number($sum_res) . "</td>\n"); 
    print ("</tr>\n"); 
} 

//diconnect from database 
db_close($connectionstring); 

echo "</tbody>\n</tfoot>\n";
    print ("<tr class=\"list\">\n"); 
    print ("<th class=\"list\">&nbsp;</th>\n"); 
    print ("<th class=\"list\">&nbsp;</th>\n"); 
    print ("<th class=\"list\" style=\"text-align: right;\">" . number($sum_totfattura) . "</th>\n"); 
    print ("<th class=\"list\" style=\"text-align: right;\">" . number($sum_compensi) . "</th>\n"); 
    print ("<th class=\"list\" style=\"text-align: right;\">&nbsp;</th>\n"); 
    print ("<th class=\"list\" style=\"text-align: right;\">" . number($sum_impendit) . "</th>\n"); 
    print ("<th class=\"list\" style=\"text-align: right;\">&nbsp;</th>\n"); 
    print ("<th class=\"list\" style=\"text-align: right;\">" . number($sum_impenage) . "</th>\n"); 
    print ("<th class=\"list\" style=\"text-align: right;\">" . number($sum_prog) . "</th>\n"); 
    print ("<th class=\"list\" style=\"text-align: right;\">" . number($sum_res) . "</th>\n"); 
    print ("</tr>\n"); 

echo "</tfoot>\n</table>\n<br>\n";
goMain();
footer();
?>