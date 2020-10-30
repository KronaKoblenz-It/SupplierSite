<?php 
/************************************************************************/
/* Project ArcaWeb                               		        		*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2020 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php");
include("db-utils.php");
head();

$codicearti = $_GET['codicearti'];
$macchina = $_GET['macchina'];
$tipodoc = $_GET['tipodoc'];

session_start();

//connect to database 
$connectionstring = db_connect($dbase); 
 
$Query = "update GESTIONE_ARTICOLI set TIPODOC='$tipodoc' where CODICEARTI='$codicearti' and MACCHINA='$macchina'";
$rs = db_query($connectionstring, $Query);

//-----------------------
//diconnect from database 
db_close($connectionstring); 

header("location: config-eurservice.php?gruppo=$macchina"); 
?>