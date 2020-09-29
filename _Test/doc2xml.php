<?php 
$id_testa = isset($_GET['id']) ? $_GET['id'] : 0;
$mode = isset($_GET['mode']) ? $_GET['mode'] : "";
session_start();
$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
$cf = $cookie[0];
if($id_testa == 0) {
	header("Content-Disposition: attachment; filename=$cf$mode.xml");
} else {
	header("Content-Disposition: attachment; filename=$id_testa.xml");
}
header('Content-Type: text/xml');
header('Content-Transfer-Encoding: binary'); 
header ('Cache-Control: no-cache');
header ('Cache-Control: no-store' , false);     // false => this header not override the previous similar header
/************************************************************************/
/* Project ArcaWeb                               		 			    */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2013 by Roberto Ceccarelli                        */
/* http://strawberryfield.altervista.org								*/
/*                                                                      */
/************************************************************************/
print("<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n");

include("header.php"); 
include("db-utils.php");
include("../libs/doc2xml_lib.php");

session_start();
$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
$maga = "F" . substr($cookie[0],2);
$cf = $cookie[0];
$conn = db_connect($dbase); 

if($id_testa == 0) {
	switch($mode) {
		case "tl":
			$Query = "SELECT DOCTES.ID ";
			$Query .= " FROM DOCTES ";
			$Query .= " WHERE MAGARRIVO = \"$maga\"";
			$Query .= " AND (TIPODOC=\"BT\" or TIPODOC=\"CE\" or TIPODOC=\"RL\" or TIPODOC=\"TL\") ";
			$Query .= "ORDER BY DATADOC DESC";
			$queryexe = db_query($conn, $Query) or die(mysql_error()); 
			print("<aw:doclist xmlns:aw=\"http://www.Project-srl.it/ArcaWeb\">\n");
			while($row = db_fetch_row($queryexe)) {
				print("<aw:doc>\n");
				writeDoc($row[0]);
			}
			print("</aw:doclist>\n");
			break;
		case "of":
			$Query = "SELECT ID FROM DOCTES WHERE CODICECF = \"$cf\"" ;
			$Query .= " AND (TIPODOC=\"OC\" or TIPODOC=\"FO\" or TIPODOC=\"LO\" or TIPODOC=\"OF\" or TIPODOC=\"OL\")";
			$Query .= " ORDER BY DATADOC DESC";
			$queryexe = db_query($conn, $Query) or die(mysql_error()); 
			print("<aw:doclist xmlns:aw=\"http://www.Project-srl.it/ArcaWeb\">\n");
			while($row = db_fetch_row($queryexe)) {
				print("<aw:doc>\n");
				writeDoc($row[0]);
			}
			print("</aw:doclist>\n");
			break;
	}
} else {
	print("<aw:doc xmlns:aw=\"http://www.Project-srl.it/ArcaWeb\">\n");
	writeDoc($id_testa);
}

?>