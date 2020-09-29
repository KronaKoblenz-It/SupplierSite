<?php
/************************************************************************/
/* CASASOFT ArcaWeb                               		        		*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2012 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

// Ditta in cui si accede
$dbase = "krona";
$lang  = "it";

// File da includere in tutte le pagine
include("../libs/common.php");
include("../libs/baseheader.php");
include("../libs/k-webMov.php");

if (file_exists('semaforo.txt')==1) {
  header("Location: index.php");
}

?>
