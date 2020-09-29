<?php 

/************************************************************************/
/* CASASOFT ArcaWeb                               		 			    */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2011 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php");
include("db-utils.php");
head();
banner("Import database");


//connect to database 
$connectionstring = db_connect("russia"); 

//SQL query  
$myFile = "private.txt";
$fh = fopen($myFile, 'r');
while(!feof($fh))
  {
  $Query = fgets($fh);
 // print($Query."<br>");
  $queryexe = db_query($connectionstring, $Query) or die(mysql_error()); 
  }
fclose($fh);


//diconnect from database 
db_close($connectionstring); 

footer();
?>