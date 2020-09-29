<?php
/************************************************************************/
/* CASASOFT ArcaWeb                               		 			    */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2013 by Roberto Ceccarelli                        */
/* http://strawberryfield.altervista.org								*/
/*                                                                      */
/************************************************************************/

include("../libs/common.php");
include("../libs/baseheader.php");
include("db-utils.php");
head_base();
banner("Import utenti");


//connect to database
$connectionstring = db_connect("awusers");

doQuery("usersc.txt");


//diconnect from database
db_close($connectionstring);

footer();

function doQuery($myFile) {
  global $connectionstring;

  print("$myFile<br>");
  $bz = bzopen($myFile.".bz2", "r") or die("Couldn't open $myFile");
  $of = fopen($myFile, "w") or die ("Couldn't create $myFile");
  while (!feof($bz)) {
    $allQuery = bzread($bz, 4096);
	$err = bzerrno($bz);
	if($err !== 0) die("<br>Compression Problem $err - ".bzerrstr($bz));
	fwrite($of, $allQuery);
	print(".");
  }
  print("<br>");
  bzclose($bz);
  fclose($of);

  $fh = fopen($myFile, 'r');
  while(!feof($fh)) {
    $Query = fgets($fh);
	if(trim($Query) != "") {
      // print($Query."<br>");
      $queryexe = db_query($connectionstring, $Query) or die(mysql_error());
	}
  }
  fclose($fh);
}
?>
