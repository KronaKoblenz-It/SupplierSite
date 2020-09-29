<?php
header('Content-Type: text/xml');
header('Cache-Control: no-cache');
header('Cache-Control: no-store' , false);     // false => this header not override the previous similar header
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

$cCodice = trim($_GET['cod']);
$cCodice = str_pad($cCodice, 5, "0", STR_PAD_LEFT);
$cCF = (isset($_GET['cf']) ? $_GET['cf'] : "F");


// $conn = new COM("ADODB.Connection");
// $conn->Open($connectionstring);

/* cerchiamo se esiste il fornitore esiste nell'elenco dei fornitori */

$Query = "SELECT * FROM ANAGRAFE WHERE CODICE = '$cCF$cCodice'";
$rs = db_query($conn, $Query) or die(mysql_error()); 
if (mysql_num_rows($rs) > 0)
{
    //Ho trovato il fornitore
	$row = mysql_fetch_assoc($rs);
	$Codice = trim($row['CODICE']);
    $RagSoc = str_replace('&', 'e', trim($row['DESCRIZION']));
    $Indirizzo = trim($row['INDIRIZZO']);
    $Cap = trim($row['CAP']);
    $Localita = trim($row['LOCALITA']);
    if(trim($row['PROV'])==''){
		$Prov = 'none';
	} else {
		$Prov = trim($row['PROV']);
	}
}
else
{
    // Non abbiamo trovato nulla
	$Codice = "*error*";
}



$out = "<forinfo>";
$out .= "<codice>$Codice</codice>";
if($Codice != "*error*") {
    $out .= "<ragsoc>$RagSoc</ragsoc>";
    $out .= "<indirizzo>$Indirizzo</indirizzo>";
    $out .= "<cap>$Cap</cap>";
    $out .= "<localita>$Localita</localita>";
    $out .= "<prov>$Prov</prov>";
}
$out .= "</forinfo>";
print($out);


?>