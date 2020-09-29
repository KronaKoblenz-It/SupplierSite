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
$cookie = preg_split("/\|/",$_COOKIE['CodiceAgente']);

$connectionstring = db_connect($dbase); 
$Query = "SELECT DESCRIZION FROM ANAGRAFE WHERE CODICE = \"" . $cookie[0] ."\"";
$queryexe = db_query($connectionstring, $Query) or die(mysql_error() ); 
$row = db_fetch_row($queryexe);
banner("Lista rapporti non conformit&agrave;",$row[0]);

print("<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"1\" bgcolor=\"#000000\">\n");
print("<tr bgcolor=\"#CCFFFF\">\n");
print("<th height=\"22\">Data</th>\n"); 
print("<th height=\"22\">Riferimento</th>\n"); 
print("<th height=\"22\">Descrizione</th>\n"); 
print("<th height=\"22\">Tipo</th>\n"); 
print("<th height=\"22\">Stato</th>\n"); 
print("</tr>\n");

//SQL query  
$Query = "SELECT ISORNC.DATAREG,ISORNC.DOCNMOV,ISORNC.DOCTIP,ISORNC.DATAEND,ISORNC.DESCRIZION,ISORNC.ID, ";
$Query .= "ISOCAUSE.DESCRIZION AS TIPONC ";
$Query .= "FROM ISORNC LEFT OUTER JOIN ISOCAUSE ON ISOCAUSE.CODICE = ISORNC.CAUSA ";
$Query .= "WHERE CODFOR = \"" . $cookie[0] ."\" " ;
$Query .= "ORDER BY ISORNC.DATAREG DESC ";

//execute query 
$queryexe = db_query($connectionstring, $Query) or die(mysql_error()); 

//query database 
    while($row = db_fetch_row($queryexe)) 
    { 
    $datareg = format_date($row[0]); 
    $stato = $row[3] >0 ? "Chiusa" : "Aperta";
     
    //format results 
    print ("<tr bgcolor=\"#ccffcc\">\n"); 
    print ("<td><a href=\"rnc-detail.php?id=" . $row[5] . "\" >$datareg</a></td>\n"); 
    print ("<td>" . $row[2] . " " . $row[1] . "</td>\n"); 
    print ("<td>" . $row[4] . "</td>\n"); 
    print ("<td>" . $row[6] . "</td>\n"); 
    print ("<td>$stato</td>\n"); 
    print ("</tr>\n"); 
    } 

//diconnect from database 
db_close($connectionstring); 


print("</table>\n");

print("<br>\n");
goMain();
footer();
?>