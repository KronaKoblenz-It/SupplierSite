<?php 
/************************************************************************/
/* Project ArcaWeb                               		 			    */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2019 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php");
include("db-utils.php");

$conn = db_connect($dbase); 


/* $stringa = str_replace("(", "", $stringa);  ESEMPIO DI REPLACE di ( */
$cCodice = $_GET['cod'];
$cCodice= str_replace("*", "", $cCodice);



$cCF = $_GET['cf'];
$cCF = str_replace("*", "", $cCF);

/* cerchiamo se esiste un articolo con il codice richiesto */

$Query = "SELECT CODICE FROM MAGART WHERE CODICE ='$cCodice'";
$rs = db_query($conn, $Query) or die(mysql_error()); 
if (mysql_num_rows($rs) == 0)
{
  // Non abbiamo trovato l'articolo, vediamo se e' un alias
  $Query = "SELECT CODICEARTI FROM MAGALIAS WHERE ALIAS = '$cCodice'";
  $rs = db_query($conn, $Query) or die(mysql_error());   
  if (mysql_num_rows($rs) == 0)
  {
    // Non abbiamo trovato nemmeno l'alias
	//provo con il codalt
	$Query = "SELECT CODICEARTI FROM CODALT WHERE U_BARCODE = '$cCodice'";
	$rs = db_query($conn, $Query) or die(mysql_error());   
	if (mysql_num_rows($rs) == 0)
	{
		// Non abbiamo trovato NULLA
		print("*error*");	
	} else 
	{
		$row = mysql_fetch_assoc($rs);
		print(trim($row['CODICEARTI']));
	}
  }
  else
  {
	  $row = mysql_fetch_assoc($rs);
	  print(trim($row['CODICEARTI']));
  }
}
else
{
  print(trim($cCodice));
}

//diconnect from database 
// $rs->Close();
// $conn->Close();
// $rs = null;
// $conn = null;
 
?>