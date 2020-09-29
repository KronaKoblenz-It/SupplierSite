<?php 

/************************************************************************/
/* CASASOFT ArcaWeb                               		 			    */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2018 by Roberto Ceccarelli                        */
/* http://strawberryfield.altervista.org                                */
/*                                                                      */
/************************************************************************/

include("header.php");
include("db-utils.php");

head();

session_start();
$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
$codAgente = substr($cookie[0],1);
banner("Situazione Enasarco",$cookie[1]);
$anno = current_year();


//connect to database 
$connectionstring = db_connect($dbase); 

//SQL quyery  
$Query = "SELECT FORNITORE FROM AGENTI WHERE CODICE ='$codAgente'";
$queryexe = db_query($connectionstring, $Query) or die(mysql_error()); 

//echo $Query;
//execute query 
$queryexe = db_query($connectionstring, $Query) or die(mysql_error()); 
if($row = db_fetch_row($queryexe)) {
	$fornitore = trim($row[0]);
	$form = <<<EOT
<form method="GET" action="enasarcotable.php">
<label for="anno">Anno</label><br>
<input type="text" value="$anno" name="anno" id="anno" size="4">&nbsp;
<input type="hidden" name="forn" id="forn" value="$fornitore">
<input type="submit" id="btnok" value="Ok" ><br><br>
EOT;
	print("$form\n");
} else {
	print("<h1>Funzione non disponibile.</h1>");
}
	

goMain();
footer();
?>