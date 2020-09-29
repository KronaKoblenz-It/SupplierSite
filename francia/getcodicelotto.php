<?php 
/************************************************************************/
/* Project ArcaWeb                               		 			    */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2010 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php"); 
include("db-utils.php");

$conn = db_connect($dbase); 

$cCodice = $_GET['art'];
$cLotto = $_GET['lotto'];


/* cerchiamo se esiste un lotto con il codice richiesto */

$Query = "SELECT ARTICOLO, LOTTO FROM MAGGIACL WHERE ARTICOLO =\"$cCodice\" AND LOTTO =\"$cLotto\"";
$queryexe = db_query($conn, $Query) or die(mysql_error()); 
if(mysql_num_rows($queryexe) <= 0) {
  // non abbiamo trovato il lotto per l'articolo l'articolo
  print("*error*");	
}  else {
  print($cLotto);
}

 
?>