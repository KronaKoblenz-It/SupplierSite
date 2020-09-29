<?php 
/************************************************************************/
/* CASASOFT ArcaWeb                               		 			    */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2013 by Roberto Ceccarelli                        */
/* http://strawberryfield.altervista.org								*/
/*                                                                      */
/************************************************************************/

include("header.php");
include("db-utils.php");
head_base();

$DB = "francia";
//banner("Import database ".$DB);
print("<b>INIZIO AGGIORNAMENTO DB ".$DB."</br></br></b>");

$nomeFile = $_GET['FileName'];
//connect to database 
$connectionstring = db_connect($DB);

doQuery($nomeFile);
//doQuery("private_ana.txt");
//doQuery("private_art.txt");
//doQuery("private_docrig.txt");
//doQuery("private_doctes.txt");
//doQuery("private_mag.txt");
//doQuery("private_rnc.txt");
//doQuery("private_scad.txt");

//diconnect from database 
db_close($connectionstring); 
echo("</br></br>");
echo("<b>Aggiornamento Effettuato con Successo alle ore:</b>  ");
echo date("d/m/y : H:i:s", time());
echo("</br></br>");
//footer();

function doQuery($myFile) {
  global $connectionstring;

  print("$myFile<br>");
  $bz = bzopen($myFile.".bz2", "r") or die("ERROR: Couldn't open $myFile");
  $of = fopen($myFile, "w") or die ("ERROR: Couldn't create $myFile");
  $j = 0;
  while (!feof($bz)) {
    $allQuery = bzread($bz, 4096);
	$err = bzerrno($bz);
	if($err !== 0) die("<br>ERROR: Compression Problem $err - ".bzerrstr($bz));
	fwrite($of, $allQuery);
	//print(".");
	$j++;
  }
  print("<br>"); 
  flush_buffers();
  bzclose($bz);
  fclose($of);
  //print("Estrazione Archivio Completata!</br> $j righe estrapolate... </br>Procedo Con ImportDB...</br>");
  print("Procedo Con ImportDB...</br>");
  
  $fh = fopen($myFile, 'r') or die("ERROR: Couldn't open $myFile");
  $i = 0;
  $lines = numRighe($myFile);
  while(!feof($fh)) {
    $Query = fgets($fh);
	if(trim($Query) != "") {
      //print($Query."<br>");
      $queryexe = db_query($connectionstring, $Query) or die("ERROR: ".mysql_error());
	}  
	$i++;
  }
  //Riporto la numerazione al valore corretto.
  $i--;
  flush_buffers();
  print("Aggiornamento DB Completata per '".$myFile."'!</br> Righe elaborate $i su $lines...</br></br>");
  fclose($fh);
  if ($i < $lines){
     print("<b>ERROR:</b> Non tutte le righe sono state eleborate!!!");
  }
}

function numRighe($txtFile) {
    $righe = file($txtFile);
    return count($righe);
}

function flush_buffers(){ 
    ob_end_flush(); 
    ob_flush(); 
    flush(); 
    ob_start(); 
} 

?>