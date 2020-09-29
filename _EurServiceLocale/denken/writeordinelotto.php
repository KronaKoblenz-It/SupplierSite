<?php 
/************************************************************************/
/* Project ArcaWeb                               		 			    */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2019 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php");

$codicearti = $_GET['codice'];
$lotto = $_GET['lotto'];
$quantita = $_GET['qta'];
$linea = isset($_GET['linea']) ? trim($_GET['linea']) : '0000000000';

$conn = new COM("ADODB.Connection");
$conn->Open($connectionstring);

$Query = "spWriteOrdineLotto '$codicearti', '$lotto', '$quantita', '$linea'";
$rs1 = $conn->Execute($Query);

//disconnect from database
$conn->Close();
$conn = null;
 
?>