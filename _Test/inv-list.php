<?php 
/************************************************************************/
/* CASASOFT ArcaWeb                               		      			*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2013 by Roberto Ceccarelli                        */
/* http://strawberryfield.altervista.org								*/
/*                                                                      */
/************************************************************************/

include("header.php");
include("db-utils.php");
head();
session_start();
$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
$maga = "F" . substr($cookie[0],2);

$connectionstring = db_connect($dbase); 
$Query = "SELECT DESCRIZION FROM ANAGRAFE WHERE CODICE = \"" . $cookie[0] ."\"";
$queryexe = db_query($connectionstring, $Query) or die(mysql_error() ); 
$row = db_fetch_row($queryexe);
banner("Lista inventario di magazzino",$row[0]);


//print("<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"1\" bgcolor=\"#000000\">\n");
print("<table class=\"list\">\n");
print("<tr bgcolor=\"#CCFFFF\">\n");
print("<th class=\"list\"><b>Articolo</b></th>\n"); 
print("<th class=\"list\"><b>Descrizione</b></th>\n"); 
print("<th class=\"list\"><b>Quantit�</b></th>\n"); 
print("<th class=\"list\"><b>Lotto</b></th>\n"); 
print("</tr>\n");

//SQL query  
$Query = "SELECT u_invent.codicearti, MAGART.DESCRIZION, u_invent.quantita, u_invent.lotto ";
$Query .= "FROM u_invent inner join MAGART on MAGART.CODICE = u_invent.codicearti ";
$Query .= "WHERE u_invent.magazzino = \"$maga\" "; 
$Query .= "ORDER BY codicearti";

//execute query 
$queryexe = db_query($connectionstring, $Query) or die(mysql_error()); 

//query database 
while($row = db_fetch_row($queryexe)) { 
    //format results 
    print ("<tr bgcolor='#ccffcc'>\n"); 
    print ("<td class=\"list\">" . $row[0] . "</td>\n"); 
    print ("<td class=\"list\">" . $row[1] . "</td>\n"); 
    print ("<td class=\"list\">" . $row[2] . "</td>\n"); 
    print ("<td class=\"list\">" . $row[3] . "</td>\n"); 
    print ("</tr>\n"); 
    } 
print("</table>\n");

// Diamo la possibilt� di dichiarare completato l'inventario	
$Query = "select finito from u_invfine where magazzino=\"$maga\"";	
$queryexe = db_query($connectionstring, $Query) or die(mysql_error() ); 
if( !($row = db_fetch_row($queryexe)) ) {
	print("<form action=\"inv-setend.php\">\n");
	print("<input type=\"submit\" value=\"Dichiara completato l'inventario\">\n");
	print("</form>\n");
}
	
//diconnect from database 
db_close($connectionstring); 

goMain();
footer();
?>