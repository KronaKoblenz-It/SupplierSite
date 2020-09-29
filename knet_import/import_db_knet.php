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

//Creo File semaforo
if (!file_exists('semaforo.txt')){
  $myfile = fopen("semaforo.txt", "w") or die("Unable to open file!");
  echo '<b>CREATO FILE SEMAFORO</br></br></b>';
  fclose($myfile);
}

//TIME & mkDir
$t=time();
$year=date("Y");
$month=date("m");
$day=date("d");
$clock=date("H-i-s", $t);
$timeDir=$year."_".$month."_".$day."_".$clock;
print("$timeDir<br>");
//$bkUpDir='./seed/kNet/_Ok/'.$timeDir.'/';
//mkdir($bkUpDir, 0777);

//Apro connessione DB
$dbase='';
$ditta='';
$err=false;
if(isset($_GET['ditta'])){
	$ditta=$_GET['ditta'];
	switch ($ditta) {
		case "kNet_it":
			$dbase='kNet_it';
			$bkUpDir='./seed/kNet_it/_Ok/'.$timeDir.'/';
			mkdir($bkUpDir, 0777);
			break;
		case "kNet_es":
			$dbase='kNet_es';
			$bkUpDir='./seed/kNet_es/_Ok/'.$timeDir.'/';
			mkdir($bkUpDir, 0777);
			break;
		case "kNet_fr":
			$dbase='kNet_fr';
			$bkUpDir='./seed/kNet_fr/_Ok/'.$timeDir.'/';
			mkdir($bkUpDir, 0777);
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
//////////////////////////////////////////////////////////////////
//MAIN
/////////////////////////////////////////////////////////////////
if(!$err){
	print("<b>INIZIO AGGIORNAMENTO DB ".$dbase."</br></br></b>");
	//connect to database
	$connectionstring = db_connect($dbase);
	//$nomeFile = $_GET['FileName'];
	foreach (glob("./seed/$ditta/kk_*.bz2") as $filename) {
		//Nome File
		$myFile = substr($filename,0,-4); //restituisce intero path
		$nameFile = substr($filename, strpos($filename, 'kk_'), -4);
	
		print("$myFile<br>");
		
		//Esplodo Archivio
		unarchiveFile($myFile);
		
		//Controllo Dimensioni File
		$size = filesize($myFile);
		print("$nameFile = $size<br>");
		
		if ($size>=30000000){
			$nNumFile=doMultiFile($myFile);
			doQuery($myFile, $nNumFile);
		} else {
			doQuery($myFile, 0);
		}
		rename($myFile.'.bz2', $bkUpDir.$nameFile.'.bz2');
		print("<br>");
	}

	//diconnect from database
	db_close($connectionstring);
	
	echo '</br></br>';
	echo '<b>Aggiornamento Effettuato con Successo alle ore:</b>  ';
	echo date('d/m/y : H:i:s', time());
	echo '</br></br>';
}

//////////////////////////////////////////////////////////////////////
//// FUNZIONI
//////////////////////////////////////////////////////////
function unarchiveFile($myFile){
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
	flush_buffers();
	bzclose($bz);
	fclose($of);
}

function doMultiFile($myFile){
	print('Inizio spezzattamento file</br>');
	
    $of = fopen($myFile, 'r') or die("Couldn't open $myFile");
    $nRiga = 0;
    $nRigheFile = 50000;
    $nNumFile = 0;
    //$hDest = fopen($myFile.$nNumFile.'.sql', 'w') or die("Couldn't create $myFile");
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
	return $nNumFile;
}

function doQuery($myFile, $nNumFile){
	print('Inizio aggiornamento database ...</br>');
	if($nNumFile>0){
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
						if(strpos(mysql_error(), 'Duplicate')>0) {
							echo '</br>WARNING: '.mysql_error();
						} else {
							echo '</br>ERROR: '.mysql_error();
						}
					}
				}
				++$i;
			}
			fclose($hsource);
			++$nFile;
		}
	} else {
		$fh = fopen($myFile, 'r') or die("ERROR: Couldn't open $myFile");
		$i = 0;
		$lines = numRighe($myFile);
		while(!feof($fh)) {
			$Query = fgets($fh);
			if(trim($Query) != "") {
				//print($Query."<br>");
				$queryexe = db_query($connectionstring, $Query); // or die("ERROR: ".mysql_error());
				if (!$queryexe) {
					if(strpos(mysql_error(), 'Duplicate')>0) {
						echo '</br>WARNING: '.mysql_error();
					} else {
						echo '</br>ERROR: '.mysql_error();
					}
				}
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
    print("Aggiornamento DB Completata per '".$myFile."'!</br>");
    print("$i righe elaborate...</br></br>");
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
