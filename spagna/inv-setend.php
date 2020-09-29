<?php 
/************************************************************************/
/* Project ArcaWeb                               		        		*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2013 by Roberto Ceccarelli                        */
/* http://strawberryfield.altervista.org								*/
/*                                                                      */
/************************************************************************/

include("header.php");
include("db-utils.php");
head();

session_start();
$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
$maga = "F" . substr($cookie[0],2);
 
$connectionstring = db_connect($dbase); 
 
$Query = "insert into u_invfine (magazzino, finito, data) values (\"$maga\", 1, '" . date("Y-m-d") ."')";
$rs = db_query($connectionstring, $Query);

//-----------------------
//diconnect from database 
db_close($connectionstring); 

header("location: menu-for.php"); 
?>