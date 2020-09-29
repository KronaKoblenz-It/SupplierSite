<?php 

/************************************************************************/
/* CASASOFT ArcaWeb                               		 			    */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2013 by Roberto Ceccarelli                        */
/* http://strawberryfield.altervista.org                                */
/*                                                                      */
/************************************************************************/

include("header.php");
include("db-utils.php");
include("../libs/doc-lib.php");
head();
session_start();
$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
banner($str_rubrica[$lang],$cookie[1]);


print('<table class="list" width="100%" border="0" cellspacing="1" cellpadding="1" bgcolor="#000000"> ');
print('  <tr class="list" bgcolor="#CCFFFF">  ');
print('    <th class="list" height="22"><b>' . $str_nome[$lang] . '</b></th> ');
print('    <th class="list" height="22"><b>' . $str_indirizzo[$lang] . '</b></th> ');
print('    <th class="list" height="22"><b>' . $str_localita[$lang] . '</b></th> ');
print('    <th class="list" height="22"><b>' . $str_pr[$lang] . '</b></th> ');
print('    <th class="list" height="22"><b>' . $str_telefono[$lang] . '</b></th> ');
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
    print ("<tr class='list' bgcolor='#ccffcc'>"); 
    print ("<td class='list' ><a href=\"cli-detail.php?id=" . $row[5] . "\">$name</a></td>"); 
    print ("<td class='list' >$addr</td>"); 
    print ("<td class='list' >$city</td>"); 
    print ("<td class='list' >$prov</td>"); 
    print ("<td class='list' >$phone</td>"); 
    print ("</tr>"); 
    } 

//diconnect from database 
db_close($connectionstring); 

echo "</table>";
footer();
?>