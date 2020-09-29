<?php

/************************************************************************/
/* CASASOFT ArcaWeb                               		 			    */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2013 by Roberto Ceccarelli                        */
/* http://strawberryfield.altervista.org								*/
/*                                                                      */
/************************************************************************/

// include("header.php");
include '../libs/common.php';
include '../libs/baseheader.php';
include 'db-utils.php';
head_base();

if (!file_exists('semaforo.txt')){
  $myfile = fopen("semaforo.txt", "w") or die("Unable to open file!");
  echo '<b>CREATO FILE SEMAFORO</br></br></b>';
  fclose($myfile);
}

$dbase='';
$ditta='';
$err=false;
if(isset($_GET['ditta'])){
	$ditta=$_GET['ditta'];
	switch ($ditta) {
		case "kNet":
			$dbase='kNet_it';
			break;
	    default:
	    	$dbase='';
	    	print("Errore! Db non trovato!");
	    	$err=true;
    }
} else {
	print("Errore su DB! Ritenta!");
	$err=true;
}

if(!$err){
	print("<b>INIZIO AGGIORNAMENTO DB ".$dbase."</br></br></b>");
	//connect to database
	$connectionstring = db_connect($dbase);
	//$nomeFile = $_GET['FileName'];
	foreach (glob("./seed/$ditta/kk_*.bz2") as $filename) {
		doQueryPartial(substr($filename,0,-4));
	}

	//diconnect from database
	db_close($connectionstring);
	echo '</br></br>';
	echo '<b>Aggiornamento Effettuato con Successo alle ore:</b>  ';
	echo date('d/m/y : H:i:s', time());
	echo '</br></br>';
}

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

function doQueryPartial($myFile) {
	
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
	
    print('Inizio spezzattamento file</br>');
	
    $of = fopen($myFile, 'r') or die("Couldn't open $myFile");
    $nRiga = 0;
    $nRigheFile = 50000;
    $nNumFile = 1;
    $hDest = fopen($myFile.$nNumFile.'.sql', 'w') or die("Couldn't create $myFile");
    while (!feof($of)) {
        $allQuery = fgets($of);
        if (($nRiga % $nRigheFile) == 0) {
            fclose($hDest);
            ++$nNumFile;
            $hDest = fopen($myFile.$nNumFile.'.sql', 'w') or die("Couldn't create $myFile.sql");
        }
        fputs($hDest, $allQuery);
        ++$nRiga;
    }
    fclose($hDest);

    print('Fine spezzettamento file</br>');
    print('Inizio aggiornamento database ...</br>');

    $nFile = 1;
    $i = 0;
    while ($nFile <= $nNumFile) {
        $hsource = fopen($myFile.$nFile.'.sql', 'r') or die("Couldn't create ".$myFile.$nFile.'.sql');
		
        while (!feof($hsource)) {
            $allQuery = fgets($hsource);
            if (trim($allQuery) != '') {
                //print($allQuery."</br>");
                $queryexe = db_query($connectionstring, $allQuery);
                if (!$queryexe) {
                    echo '</br>'.mysql_error();
                }
            }
            ++$i;
        }
        fclose($hsource);
        ++$nFile;
    }

    print("Aggiornamento DB Completata per '".$myFile."'!</br>");
    print("$i righe elaborate...</br></br>");
}


function flush_buffers(){
    ob_end_flush();
    ob_flush();
    flush();
    ob_start();
}

function numRighe($txtFile) {
    $righe = file($txtFile);
    return count($righe);
}

?>
