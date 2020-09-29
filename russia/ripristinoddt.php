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
 
$connectionstring = db_connect($dbase); 
banner("Gestione bolle fornitori","");


print("<table width=\"100%\" border=\"0\" cellspacing=\"1\" cellpadding=\"1\" bgcolor=\"#000000\">\n");
print("<tr bgcolor=\"#CCFFFF\">\n");
print("<th height=\"22\"><b>Fornitore</b></th>\n"); 
print("<th height=\"22\"><b>Data</b></th>\n"); 
print("<th height=\"22\"><b>Numero bolla</b></th>\n"); 
print("<th height=\"22\">&nbsp;</th>\n"); 
print("</tr>");



//SQL query  
$Query = "SELECT ANAGRAFE.DESCRIZION, U_BARDT.DATADOC, U_BARDT.ID, U_BARDT.NUMERODOCF";
$Query .= " FROM U_BARDT INNER JOIN ANAGRAFE ON ANAGRAFE.CODICE = U_BARDT.CODICECF";
$Query .= " WHERE U_BARDT.DEL=1";
$Query .= " ORDER BY ANAGRAFE.DESCRIZION, U_BARDT.DATADOC DESC";

//execute query 
$queryexe = db_query($connectionstring, $Query) or die(mysql_error()); 

//query database 
    while($row = db_fetch_row($queryexe)) 
    { 
    $name = $row[0]; 
    $date = format_date($row[1]); 
    $addr = $row[3]; 
     
    //format results 
    print ("<tr bgcolor='#ccffcc'>"); 
    print ("<td>$name</td>"); 
    print ("<td>$date</td>"); 
    print ("<td>$addr</td>"); 
    print ("<td><a href=\"ripridoc.php?id=" . $row[2] . "\" ><img noborder src=\"trash.png\"></a></td>"); 
    print ("</tr>"); 
    } 

//diconnect from database 
db_close($connectionstring); 


print("</table>");

footer();
?>