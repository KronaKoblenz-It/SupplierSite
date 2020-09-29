<?php 
/************************************************************************/
/* CASASOFT ArcaWeb                               		      			*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2011 by Roberto Ceccarelli                        */
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


print('<table width="100%" border="0" cellspacing="1" cellpadding="1" bgcolor="#000000"> ');
print('  <tr bgcolor="#CCFFFF">  ');
print('    <th height="22"><b>Data</b></th>'); 
print('    <th height="22"><b>Numero bolla</b></th>'); 
print('    <th height="22">&nbsp;</th>'); 
print('  </tr> ');



//SQL query  
$Query = "SELECT DATADOC,ID,NUMERODOCF FROM U_BARDT WHERE CODICECF = \"" . $cookie[0] ;
$Query .= "\" ORDER BY DATADOC DESC";

//execute query 
$queryexe = db_query($connectionstring, $Query) or die(mysql_error()); 

//query database 
    while($row = db_fetch_row($queryexe)) 
    { 
    $name = format_date($row[0]); 
    $addr = $row[2]; 
     
    //format results 
    print ("<tr bgcolor='#ccffcc'>"); 
    print ("<td><a href=\"moddb.php?id=" . $row[1] . "\" >$name</a></td>"); 
    print ("<td>$addr</td>"); 
    print ("<td><a href=\"deldoc.php?id=" . $row[1] . "\" ><img noborder src=\"b_drop.png\"></a></td>"); 
    print ("</tr>"); 
    } 

//diconnect from database 
db_close($connectionstring); 


print("</table>");
print ("<br><a href=\"menu-for.php\"><img border=\"0\" src=\"b_home.gif\" alt=\"Menu principale\">Menu principale</a>\n");

footer();
?>