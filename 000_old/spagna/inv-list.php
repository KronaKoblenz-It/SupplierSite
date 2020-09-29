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
$maga = "F" . substr($cookie[0],2);

$connectionstring = db_connect($dbase); 
$Query = "SELECT DESCRIZION FROM ANAGRAFE WHERE CODICE = \"" . $cookie[0] ."\"";
$queryexe = db_query($connectionstring, $Query) or die(mysql_error() ); 
$row = db_fetch_row($queryexe);
banner("Lista inventario di magazzino",$row[0]);


print("<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"1\" bgcolor=\"#000000\">\n");
print("<tr bgcolor=\"#CCFFFF\">\n");
print("<th height=\"22\"><b>Articolo</b></th>\n"); 
print("<th height=\"22\"><b>Descrizione</b></th>\n"); 
print("<th height=\"22\"><b>Quantità</b></th>\n"); 
print("<th height=\"22\"><b>Lotto</b></th>\n"); 
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
    print ("<td>" . $row[0] . "</td>\n"); 
    print ("<td>" . $row[1] . "</td>\n"); 
    print ("<td>" . $row[2] . "</td>\n"); 
    print ("<td>" . $row[3] . "</td>\n"); 
    print ("</tr>\n"); 
    } 
print("</table>\n");

// Diamo la possibiltà di dichiarare completato l'inventario	
$Query = "select finito from u_invfine where magazzino=\"$maga\"";	
$queryexe = db_query($connectionstring, $Query) or die(mysql_error() ); 
if( !($row = db_fetch_row($queryexe)) ) {
	print("<form action=\"inv-setend.php\">\n");
	print("<input type=\"submit\" value=\"Dichiara completato l'inventario\">\n");
	print("</form>\n");
}
	
//diconnect from database 
db_close($connectionstring); 

print ("<br><a href=\"menu-for.php\"><img border=\"0\" src=\"b_home.gif\" alt=\"Menu principale\">Menu principale</a>\n");

footer();
?>