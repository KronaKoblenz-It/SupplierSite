<?php 
/************************************************************************/
/* Project ArcaWeb                               		        		*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2017 by Roberto Ceccarelli                        */
/* http://strawberryfield.altervista.org								*/
/*                                                                      */
/************************************************************************/

include("header.php");
include("db-utils.php");
head();
$connectionstring = db_connect($dbase); 

session_start();
$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
$maga = "F" . substr($cookie[0],2);
include("inv_common.php");
if ($mode == 'sfridi') {
    $maga = "S" . substr($cookie[0], 2);
}


$table = ($mode == "attr" ? "u_invfinea" : "u_invfine"); 
$Query = "insert into $table (magazzino, finito, data) values (\"$maga\", 1, '" . date("Y-m-d") ."')";
$rs = db_query($connectionstring, $Query);

//-----------------------
//diconnect from database 
db_close($connectionstring); 

mail("inventari@k-group.com", "Inventario$mode_$cookie[0]_$cookie[1]",  "Inventario$attr Dichiarato Chiuso!", "From: automatico@k-group.com");
mail("ced-it@k-group.com", "Inventario$mode_$cookie[0]_$cookie[1]",  "Inventario$attr Dichiarato Chiuso!", "From: automatico@k-group.com");

header("location: menu-inv.php"); 
?>