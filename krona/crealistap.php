<?php 
/************************************************************************/
/* Project ArcaWeb                               		 			    */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2020 by Roberto Ceccarelli                        */
/* http://strawberryfield.altervista.org								*/
/*                                                                      */
/************************************************************************/
include("header.php"); 
include("db-utils.php");

session_start();
$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
$fornitore = $cookie[0];
$maga = "F" . substr($fornitore, -4);

$conn = db_connect($dbase); 
 
$count = strtoupper($_POST['count']);
$anno = current_year();
$padre = isset($_POST["padre"]) ? trim($_POST["padre"]) : "";
$lottopadre = isset($_POST["lottopadre"]) ? trim($_POST["lottopadre"]) : "";
head();
banner($_POST['padre']);


$Query = "select max(ID) as ID from U_TESTE_LISTEP";
$rs = db_query($conn, $Query) or die(mysql_error());
$row = mysql_fetch_object($rs);
$id = $row->ID +1;


$Query = "insert into U_TESTE_LISTEP (ID, FORNITORE, ARTICOLO, LOTTO, DATA) values ($id, '$fornitore', '$padre', '$lottopadre', CURDATE())";
$rs = db_query($conn, $Query) or die(mysql_error());

// righe componenti
for($j = 1; $j <= $count; $j++) {
  $articolo = isset($_POST["code$j"]) ? trim($_POST["code$j"]) : "";	
  $lotto = isset($_POST["lotto$j"]) ? trim($_POST["lotto$j"]) : "";	
  $Query = "insert into U_RIGHE_LISTEP (ID_TESTA, ARTICOLO, LOTTO) values ($id, '$articolo', '$lotto')";
  $rs = db_query($conn, $Query) or die(mysql_error());
} 

echo "<br>Documento caricato.\n";

echo "<br>\n";
echo '<a href="askdb_bc.php">';
echo "<img border=\"0\" src=\"../img/05_edit.gif\" alt=\"Nuova lista prelievo\">Nuova lista prelievo</a>\n";
echo "<br>\n";
goMain();
footer();
?>