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
//banner("Import database");

$nomeFile = $_GET['FileName'];
//connect to database 
$connectionstring = db_connect("krona"); 

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

  print("<b><u>$myFile<br></u></b>");
  $bz = bzopen($myFile.".bz2", "r") or die("Couldn't open $myFile");
  $of = fopen($myFile, "w") or die ("Couldn't create $myFile");
  $j = 0;
  while (!feof($bz)) {
    $allQuery = bzread($bz, 4096);
	$err = bzerrno($bz);
	if($err !== 0) die("<br>Compression Problem $err - ".bzerrstr($bz));
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
  
  $fh = fopen($myFile, 'r') or die("Couldn't open $myFile");
  $i = 0;
  while(!feof($fh)) {
    $Query = fgets($fh);
	if(trim($Query) != "") {
      //print($Query."<br>");
      $queryexe = db_query($connectionstring, $Query) or die(mysql_error()); 
	}  
	$i++;
  }
  flush_buffers();
  print("Aggiornamento DB Completata per '".$myFile."'!</br> $i righe elaborate...</br></br>"); 
  fclose($fh);
}

function flush_buffers(){ 
    ob_end_flush(); 
    ob_flush(); 
    flush(); 
    ob_start(); 
} 

?>