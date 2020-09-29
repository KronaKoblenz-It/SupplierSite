<?php 
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2013 by Roberto Ceccarelli                        */
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
$anno = current_year();

$q = "SELECT PROGQTACAR-PROGQTASCA+PROGQTARET AS GIACENZA, LOTTO FROM MAGGIACL ";
$q .= "WHERE ARTICOLO = \"K 6200 20 CS DXSX\" ";
$q .= "AND MAGAZZINO = \"00001\" ";
$q .= "ORDER BY LOTTO DESC ";
$rs = db_query($conn, $q) or die(mysql_error()); 
						
$cntLot=0;
$aLotti[] = array("codice" => "", "giac" => 0);

while ($row = mysql_fetch_object($rs))	{ 
	if($row->GIACENZA > 0) {
		$aLotti[$cntLot]['codice'] = $row->LOTTO;
		$aLotti[$cntLot]['giac'] = $row->GIACENZA;
		echo $cntLot." ".$aLotti[$cntLot]['codice']. " ";
		echo $cntLot." ".$aLotti[$cntLot]['giac']."<br>";
		$cntLot++;
	}
}

if (($key = multi_in_array("13-E065",$aLotti, 'codice')) > -1){
	echo "trovvato".$key;
}

function multi_in_array($value, $array, $campo) 
{ 
	$trovato = false;
	foreach ($array as $key => $val) {
	   if ($val[$campo] == $value) {
			$trovato=true;
			return $key;
	   }
   }
   if (!$trovato){
		return -1;
   }
}

?>