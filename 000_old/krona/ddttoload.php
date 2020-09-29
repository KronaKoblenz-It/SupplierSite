<?php 
/************************************************************************/
/* CASASOFT ArcaWeb                               		      			*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2013 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php");
include("db-utils.php");
head();
$cookie = preg_split("/\|/",$_COOKIE['CodiceAgente']);
$connectionstring = db_connect($dbase); 
$Query = "SELECT DESCRIZION FROM ANAGRAFE WHERE CODICE = \"" . $cookie[0] ."\"";
$queryexe = db_query($connectionstring, $Query) or die(mysql_error() ); 
$row = db_fetch_row($queryexe);
banner("Bolle in attesa di acquisizione",$row[0]);


print("<table class=\"list\">\n");
print("<tr class=\"list\">\n");
print("<th class=\"list\">Data</th>\n"); 
print("<th class=\"list\">Numero bolla</th>\n");
print("<th class=\"list\">Tipo Doc. Rif.</th>\n");
print("<th class=\"list\">Numero Doc. Rif.</th>\n");
print("<th class=\"list\">Data Doc. Rif.</th>\n");
print("<th class=\"list\">Articolo</th>\n");
print("<th class=\"list\">Q.ta</th>\n");
print("<th class=\"list\">&nbsp;</th>\n");
print("</tr>\n");



//SQL query  
$Query = "SELECT U_BARDT.DATADOC, U_BARDT.ID, U_BARDT.NUMERODOCF, U_BARDT.RIF_TIPODOC, U_BARDT.RIF_NUMERODOC, U_BARDT.RIF_DATADOC, ";
$Query .= " U_BARDR.CODICEARTI, U_BARDR.DESCRIZION AS DESC_ART, U_BARDR.QUANTITA ";
$Query .= " FROM U_BARDT LEFT JOIN U_BARDR ON U_BARDT.ID = U_BARDR.ID_TESTA ";
$Query .= " WHERE U_BARDT.DEL=0 AND U_BARDR.ESPLDISTIN='P' AND U_BARDT.CODICECF = \"" . $cookie[0] ;
$Query .= "\" ORDER BY DATADOC DESC";

//execute query 
$queryexe = db_query($connectionstring, $Query) or die(mysql_error()); 

//query database 
    while($row = db_fetch_row($queryexe)) 
    { 
        $name = format_date($row[0]);
        $addr = $row[2];
        $rif_tipodoc = $row[3];
        $rif_numerodoc = $row[4];
        $articolo = $row[6] . " - " . $row[7];
        $quantita = $row[8];

        //format results
        print("<tr class=\"list\">\n");
        print("<td class=\"list\"><a href=\"moddb.php?id=" . $row[1] . "\" >$name</a></td>\n");
        print("<td class=\"list\">$addr</td>\n");
        print("<td class=\"list\">" . $rif_tipodoc . "</td>\n");
        print("<td class=\"list\">" . $rif_numerodoc . "</td>\n");
        print("<td class=\"list\">" . format_date($row[5]) . "</td>\n");
        print("<td class=\"list\">" . trim($articolo) . "</td>\n");
        print("<td class=\"list\">" . $quantita . "</td>\n");
        print("<td class=\"list\"><a href=\"deldoc.php?id=" . $row[1] . "\" ><img noborder src=\"../img/b_drop.png\"></a></td>\n");
        print("</tr>\n");
    } 

//diconnect from database 
db_close($connectionstring); 

print("</table>\n");
print("<br>\n");
goMain();
footer();
?>