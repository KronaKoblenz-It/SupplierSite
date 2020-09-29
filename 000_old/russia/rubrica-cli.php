<?php 

/************************************************************************/
/* CASASOFT ArcaWeb                               		 			    */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2010 by Roberto Ceccarelli                        */
/* http://casasoft.supereva.it                                          */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

include("header.php");
include("db-utils.php");
head();
$cookie = preg_split("/\|/",$_COOKIE['CodiceAgente']);
banner("Rubrica clienti",$cookie[1]);


print('<table width="100%" border="0" cellspacing="1" cellpadding="1" bgcolor="#000000"> ');
print('  <tr bgcolor="#CCFFFF">  ');
print('    <th height="22"><b>' . $str_nome[$lang] . '</b></th> ');
print('    <th height="22"><b>' . $str_indirizzo[$lang] . '</b></th> ');
print('    <th height="22"><b>' . $str_localita[$lang] . '</b></th> ');
print('    <th height="22"><b>' . $str_pr[$lang] . '</b></th> ');
print('    <th height="22"><b>' . $str_telefono[$lang] . '</b></th> ');
print('  </tr> '); 

 

//connect to database 
$connectionstring = db_connect($dbase); 

//SQL quyery  
$Query = "SELECT DESCRIZION,INDIRIZZO,LOCALITA,PROV,TELEFONO,CODICE FROM ANAGRAFE WHERE AGENTE ='";
$Query .= substr($cookie[0],1) . "' ORDER BY DESCRIZION"; 

//echo $Query;
//execute query 
$queryexe = db_query($connectionstring, $Query) or die(mysql_error()); 

//query database 
    while($row = db_fetch_row($queryexe)) 
    { 
    $name = $row[0]; 
    $addr = $row[1];
    $city = $row[2]; 
    $prov = $row[3]; 
    $phone = $row[4]; 
     
    //format results 
    print ("<tr bgcolor='#ccffcc'>"); 
    print ("<td><a href=\"cli-detail.php?id=" . $row[5] . "\">$name</a></td>"); 
    print ("<td>$addr</td>"); 
    print ("<td>$city</td>"); 
    print ("<td>$prov</td>"); 
    print ("<td>$phone</td>"); 
    print ("</tr>"); 
    } 

//diconnect from database 
db_close($connectionstring); 

echo "</table>";
footer();
?>