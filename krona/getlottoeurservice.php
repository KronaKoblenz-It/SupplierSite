<?php 
header('Content-Type: text/xml');
header ('Cache-Control: no-cache');
header ('Cache-Control: no-store' , false);     // false => this header not override the previous similar header
/************************************************************************/
/* Project ArcaWeb                               		 			    */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2019 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/
print("<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>");

include("header.php");
include("db-utils.php");

$conn = db_connect($dbase); 

$id = $_GET['id'];

$Query = "SELECT TIPODOC, NUMERODOC, YEAR(DATADOC) AS ANNO, CODICEARTI, NUMERORIGA FROM DOCRIG WHERE ID = $id";
$queryexe = db_query($conn, $Query) or die(mysql_error()); 
$row = db_fetch_row($queryexe);
$now = new DateTime('NOW'); 
$ini = new DateTime($row[2]."-01-01T00:00:00");
//$slot = round(($now->getTimestamp() - $ini->getTimestamp()) / 60);
$slot = $row[4];

$lotto = "ES" . $row[2] . "-" . str_pad(trim($row[1]), 6, "0", STR_PAD_LEFT) . $row[0] . "-$slot";

$out = "<lottoproposto>";
$out .= "<lotto>$lotto</lotto>";
$out .= "</lottoproposto>";

print($out); 

//-----------------------
//diconnect from database 
db_close($conn); 
?>