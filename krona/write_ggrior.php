<?php 
/************************************************************************/
/* Project ArcaWeb                               		        		*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2018 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php");
include("db-utils.php");
head();

$codice = $_GET['codice'];
$ggrior = $_GET['ggrior'];

session_start();

//connect to database 
$connectionstring = db_connect($dbase); 
 
$Query = "update u_ggrior set deleted=1 where codice='$codice'";
$rs = db_query($connectionstring, $Query);
$Query = "insert into u_ggrior (codice, ggrior, timestamp, deleted) values ('$codice', $ggrior, NOW(), 0)";
$rs = db_query($connectionstring, $Query);
$Query = "update MAGART set GGRIOR = $ggrior where CODICE = '$codice'";
$rs = db_query($connectionstring, $Query);

//-----------------------
//diconnect from database 
db_close($connectionstring); 

header("location: magart_forn.php"); 
?>