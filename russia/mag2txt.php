<?php 
/************************************************************************/
/* Project ArcaWeb                               		 			    */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2012 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/
header("Content-Disposition: attachment; filename=giacenze.txt");
header('Content-Type: text/txt');
header('Content-Transfer-Encoding: binary'); 
header ('Cache-Control: no-cache');
header ('Cache-Control: no-store' , false);     // false => this header not override the previous similar header


include("header.php"); 
include("db-utils.php");

$art = $_GET['art'];
$maga = $_GET['maga'];

$conn = db_connect($dbase); 

//query database 
$Query = "SELECT ";
$Query = $Query . "SUM(QUANTITA*QTACAR) AS CARICO, ";
$Query = $Query . "SUM(QUANTITA*QTASCAR) AS SCARICO, ";
$Query = $Query . "SUM(QUANTITA*QTARET) AS RETTIFICHE, ";
$Query = $Query . "SUM(QUANTITA*QTACAR) - SUM(QUANTITA*QTASCAR) + SUM(QUANTITA*QTARET) AS GIACENZA, ";
$Query = $Query . "MAGAZZINO, ";
$Query = $Query . "IF(ISNULL(LOTTO),\"Senza lotto\", LOTTO) AS LOTTO, ";
$Query = $Query . "CODICEARTI ";
$Query = $Query . "FROM MAGMOV ";
if (empty($art) or isnull($art)) {
	$Query = $Query . "WHERE MAGAZZINO=\"$maga\" ";
}
else{
	$Query = $Query . "WHERE CODICEARTI=\"" . $art ."\" AND MAGAZZINO=\"$maga\" ";
}
$Query = $Query . "GROUP BY MAGAZZINO, CODICEARTI, LOTTO ";

$rs = db_query($conn, $Query) or die(mysql_error()); 

//print($Query . "\r\n");
while($row = mysql_fetch_object($rs)) {
	$string = "";
	$string = $string . $row->CARICO . ";";
	$string = $string . $row->SCARICO . ";";
	$string = $string . $row->RETTIFICHE . ";";
	$string = $string . $row->GIACENZA . ";";
	$string = $string . $row->MAGAZZINO . ";";
	$string = $string . $row->LOTTO . ";";
	$string = $string . $row->CODICEARTI . ";";
	$string = $string . "\r\n";
	print($string);
}
?>