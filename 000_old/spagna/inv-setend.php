<?php 
/************************************************************************/
/* Project ArcaWeb                               		        		*/
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
 
$Query = "insert into u_invfine (magazzino, finito) values (\"$maga\", 1)";
$rs = db_query($connectionstring, $Query);

//-----------------------
//diconnect from database 
db_close($connectionstring); 

header("location: menu-for.php"); 
?>