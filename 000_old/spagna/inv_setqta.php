<?php 
/************************************************************************/
/* Project ArcaWeb                               		        		*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2011 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php");
include("db-utils.php");
head();

$articolo = $_GET['articolo'];
$qtanew = $_GET['qtanew'];
if(array_key_exists('somma', $_GET)) {
  $somma = $_GET['somma'];
} else {
  $somma = "off";
}  
$lotto = $_GET['lotto'];
$cookie = preg_split("/\|/",$_COOKIE['CodiceAgente']);
$maga = "F" . substr($cookie[0],2);
 
$where = "where codicearti = \"$articolo\" and lotto =\"$lotto\" and magazzino = \"$maga\"";
//connect to database 
$connectionstring = db_connect($dbase); 
 
/* cerchiamo se la riga c'e' gia' */
$Query = "select * from u_invent $where";
$rs = db_query($connectionstring, $Query);
if($row = db_fetch_row($rs))
{
	if("on" == $somma) {
	  $Query = "update u_invent set quantita = quantita+$qtanew $where";
	} else {  
	  $Query = "update u_invent set quantita = $qtanew $where";
	}
	$rs = db_query($connectionstring, $Query);
}
else
{
	$Query = "insert into u_invent (codicearti, lotto, magazzino, quantita) values (\"$articolo\", \"$lotto\", \"$maga\", $qtanew)";
	$rs = db_query($connectionstring, $Query);
}
//-----------------------
//diconnect from database 
db_close($connectionstring); 

header("location: inventario.php"); 
?>