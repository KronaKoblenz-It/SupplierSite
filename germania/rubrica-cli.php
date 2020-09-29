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
include("../libs/doc-lib.php");
$inc = <<<EOT
  $('#maintable').dataTable().yadcf([
	    {column_number : 0, column_data_type: "html", html_data_type: "text", filter_type: "text"},
	    {column_number : 1, filter_type: "text"},
	    {column_number : 2, filter_type: "auto_complete"},
	    {column_number : 3, filter_type: "auto_complete"}
		]);
EOT;

head(dataTableInit($inc));

session_start();
$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
banner($str_rubrica[$lang],$cookie[1]);


print("<table class=\"list\" id=\"maintable\">\n");
print("<thead>\n<tr class=\"list\">\n");
print("<th class=\"list\">" . $str_nome[$lang] . "</th>\n");
print("<th class=\"list\">" . $str_indirizzo[$lang] . "</th>\n");
print("<th class=\"list\">" . $str_localita[$lang] . "</th>\n");
print("<th class=\"list\">" . $str_pr[$lang] . "</th>\n");
print("<th class=\"list\">" . $str_telefono[$lang] . "</th>\n");
print("</tr>\n</thead>\n<tbody>\n"); 

 

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
    print ("<tr class=\"list\">\n"); 
    print ("<td class=\"list\" ><a href=\"cli-detail.php?id=" . $row[5] . "\">$name</a></td>\n"); 
    print ("<td class=\"list\">$addr</td>\n"); 
    print ("<td class=\"list\">$city</td>\n"); 
    print ("<td class=\"list\">$prov</td>\n"); 
    print ("<td class=\"list\">$phone</td>\n"); 
    print ("</tr>\n"); 
    } 

//diconnect from database 
db_close($connectionstring); 

echo "</tbody>\n</table>\n";
footer();
?>