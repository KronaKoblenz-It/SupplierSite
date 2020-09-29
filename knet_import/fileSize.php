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

//TIME & mkDir
$t=time();
$year=date("Y");
$month=date("m");
$day=date("d");
$clock=date("H-i-s", $t);
$timeDir=$year."_".$month."_".$day."_".$clock;
print("$timeDir<br>");
$bkUpDir='./seed/kNet/_Ok/'.$timeDir.'/';
mkdir($bkUpDir, 0777);

foreach (glob("./seed/kNet/kk_*.bz2") as $filename) {

	$myFile = substr($filename,0,-4);
	$nameFile = substr($filename, strpos($filename, 'kk_'), -4);
	
	print("$myFile<br>$nameFile<br>");
	
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
	
	$size = filesize($myFile);
	print("$myFile = $size<br>");
	//rename($myFile, './seed/kNet/_Ok/'.$nameFile);
	copy($myFile.'.bz2', $bkUpDir.$nameFile.'bz2');
	print("<br>");
}

function flush_buffers(){
    ob_end_flush();
    ob_flush();
    flush();
    ob_start();
}

?>
