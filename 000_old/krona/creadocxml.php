<?php 
$id_testa = time();
header("Content-Disposition: attachment; filename=$id_testa.xml");
header('Content-Type: text/xml');
header('Content-Transfer-Encoding: binary'); 
header ('Cache-Control: no-cache');
header ('Cache-Control: no-store' , false);     // false => this header not override the previous similar header
/************************************************************************/
/* Project ArcaWeb                               		 			    */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2012 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/
print("<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n");

include("header.php"); 
include("db-utils.php");

$cookie = preg_split("/\|/",$_COOKIE['CodiceAgente']);
$fornitore = $cookie[0];
$maga = "F" . substr($fornitore, -4);

$conn = db_connect($dbase); 
 
$count = strtoupper($_POST['count']);
$anno = current_year();

print("<aw:doc xmlns:aw=\"http://www.Project-srl.it/ArcaWeb\">\n");
print("<aw:doctes>\n");
print("<aw:id_testa>$id_testa</aw:id_testa>\n");
print("<aw:codicecf>$fornitore</aw:codicecf>\n");
print("<aw:tipodoc>CE</aw:tipodoc>\n");
print("<aw:numerodocf/>\n");
print("<aw:magpartenz>$maga</aw:magpartenz>\n");
print("<aw:magarrivo>00001</aw:magarrivo>\n");
print("</aw:doctes>\n");

$id = ($id_testa % 1000000)*100;

// riga di commento
$Query = "SELECT TIPODOC, NUMERODOC, DATADOC FROM DOCTES WHERE ID = " . $_POST["rift"];
$rs = db_query($conn, $Query) or die(mysql_error()); 
$row = mysql_fetch_object($rs);
$id = scriviRiga($id, $id_testa, "", $fornitore, "", 1, "", $maga, "Rif. " . $row->TIPODOC . " " . $row->NUMERODOC . " del " . format_date($row->DATADOC));


// riga del padre
$id = scriviRiga($id, $id_testa, "P", $fornitore, $_POST["padre"], $_POST["quantita"], $_POST["lottopadre"], $maga, "");

// righe componenti
for($j = 1; $j <= $count; $j++) {
  $id = scriviRiga($id, $id_testa, 'C', $fornitore, $_POST["code$j"], $_POST["qta$j"], $_POST["lotto$j"], $maga, "");
} 
print("</aw:doc>\n");


function scriviRiga($id, $id_testa, $espldistin, $fornitore, $codicearti, $qta, $lotto, $maga, $descrizion) {
global $conn;
print("<aw:docrig>\n");

print("<aw:id_riga>$id</aw:id_riga>\n");
print("<aw:id_testa>$id_testa</aw:id_testa>\n");
print("<aw:espldistin>$espldistin</aw:espldistin>\n");
print("<aw:codicearti>$codicearti</aw:codicearti>\n");
if( $codicearti != "") {
  $q1 = "SELECT DESCRIZION FROM MAGART WHERE CODICE =\"$codicearti\"";
  $rs = db_query($conn, $q1) or die(mysql_error()); 
  $row = mysql_fetch_object($rs);
  print("<aw:descrizion>" . str_replace('"', '""', $row->DESCRIZION) . "</aw:descrizion>\n");
} else {
  print("<aw:descrizion>$descrizion</aw:descrizion>\n");
}

print("<aw:quantita>$qta</aw:quantita>\n");
print("<aw:lotto>$lotto</aw:lotto>\n");
print("<aw:magpartenz>$maga</aw:magpartenz>\n");
print("<aw:magarrivo>00001</aw:magarrivo>\n");
print("<aw:riffromt>" . $_POST["rift"] . "</aw:riffromt>\n");
print("<aw:riffromr>" . $_POST["rifr"] . "</aw:riffromr>\n");

print("</aw:docrig>\n");

return $id + 1;  
}
?>