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
$DB = "test";

//banner("Import database");
$nomeFile = $_GET['FileName'];

$connectionstring = db_connect($DB);
print("<b>INIZIO AGGIORNAMENTO DB ".$DB."</br></br></b>");
doQuery($nomeFile);
//connect to database
//$connectionstring = db_connect("test");

//doQueryPartial("private_ana");
//doQueryPartial("private_art");
//doQueryPartial("private_docrig");
//doQueryPartial("private_doctes");
//doQueryPartial("private_mag");
//doQueryPartial("private_rnc");
//doQueryPartial("private_scad");

//diconnect from database 
//db_close($connectionstring);

echo("</br></br>");
echo("<b>Aggiornamento Effettuato con Successo alle ore:</b>  ");
echo date("d/m/y : H:i:s", time());
echo("</br></br>");
footer();

function doQuery($myFile) {
  global $connectionstring;
  print("<b>$myFile<br></b>");
  $bz = bzopen($myFile.".bz2", "r") or die("ERROR: Couldn't open .bz2 $myFile");
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
  //flush_buffers();
  bzclose($bz);
  fclose($of);
  print("Estrazione Archivio Completata!</br> $j righe estrapolate... </br>Procedo Con ImportDB...</br>"); 
  
  $fh = fopen($myFile, 'r') or die("ERROR: Couldn't open $myFile");
  $i = 0;
  $lines = numRighe($myFile);
  while(!feof($fh)) {
    $Query = fgets($fh);
	if(trim($Query) != "") {
      //print($Query."<br>");
        //$queryexe = db_query($connectionstring, $Query) or die(mysql_error());
        $queryexe = db_query($connectionstring, $Query);
        if (!$queryexe)
        {
            print("<br>" . mysql_error());
        }
	}  
	$i++;
  }
  //Riporto la numerazione al valore corretto.
  $i--;
  //flush_buffers();
  print("Aggiornamento DB Completata per '".$myFile."'!</br> Righe elaborate $i su $lines...</br></br>");
  fclose($fh);
  db_close($connectionstring);
  if ($i < $lines){
      print("<b>ERROR:</b> Non tutte le righe sono state eleborate!!!");
  }
}

function numRighe($txtFile) {
    $righe = file($txtFile);
    return count($righe);
}

function doQueryPartial($myFile) {
    //global $connectionstring;
    //1) Creo 1 file ogni 1000 righe che scompatto

    print("<b>$myFile<br></b>");
    $bz = bzopen($myFile.".txt.bz2", "r") or die("Couldn't open $myFile");
    $of = fopen($myFile . ".txt", "w") or die ("Couldn't create $myFile");
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
    //flush_buffers();
    bzclose($bz);
    fclose($of);
    print("Estrazione Archivio Completata!</br> $j righe estrapolate... </br>");
    print("Inizio spezzattamento file</br>");
    $of = fopen($myFile . ".txt", "r") or die ("Couldn't open $myFile");
    $nRiga = 0;
    $nRigheFile = 1000;
    $nNumFile = 1;
    $hDest = fopen($myFile . $nNumFile . ".txt", "w") or die ("Couldn't create $myFile");
    while(!feof($of)){
        $allQuery = fgets($of);
        if(($nRiga % $nRigheFile) == 0)
        {
            fclose($hDest);
            $nNumFile++;
            $hDest = fopen($myFile . $nNumFile . ".txt", "w") or die ("Couldn't create $myFile.txt");
        }
        fputs($hDest, $allQuery);
        $nRiga++;
    }
    fclose($hDest);

    print("Fine spezzettamento file</br>");
    print("Inizio aggiornamento database ...</br>");



    $nFile = 1;
    $i = 0;
    while($nFile <= $nNumFile){
        $hsource = fopen($myFile . $nFile . ".txt", "r") or die ("Couldn't create " . $myFile . $nFile . ".txt");
        $connectionstring = db_connect("test");
        while(!feof($hsource)){
            $allQuery = fgets($hsource);
            if(trim($allQuery) != ""){
                print($allQuery."</br>");
                $queryexe = db_query($connectionstring, $allQuery);
                if(!$queryexe){
                    print("</br>" . mysql_error());
                }
            }
            $i++;
        }
        fclose($hsource);
        db_close($connectionstring);
        $nFile++;
    }

    print("Aggiornamento DB Completata per '".$myFile."'!</br>");
    print("$i righe elaborate...</br></br>");
//    $fh = fopen($myFile, 'r') or die("Couldn't open $myFile");
//    $i = 0;
//    while(!feof($fh)) {
//        $Query = fgets($fh);
//        if(trim($Query) != "") {
//            print($Query."<br>");
//            //$queryexe = db_query($connectionstring, $Query) or die(mysql_error());
//            $queryexe = db_query($connectionstring, $Query);
//            if (!$queryexe)
//            {
//                print("<br>" . mysql_error());
//            }
//        }
//        $i++;
//    }
//    flush_buffers();
//    print("Aggiornamento DB Completata per '".$myFile."'!</br> $i righe elaborate...</br></br>");
//    fclose($fh);
//    db_close($connectionstring);
}

function flush_buffers(){ 
    ob_end_flush(); 
    ob_flush(); 
    flush(); 
    ob_start(); 
} 

?>