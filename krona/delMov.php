<?php
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2015 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php");
include("db-utils.php");

$conn = db_connect($dbase);
$id = (isset($_GET["id"]) ? $_GET["id"] : 0);
$id_riga = (isset($_GET["id_riga"]) ? $_GET["id_riga"] : 0);

head();
banner("Documento cancellato");

if($id > 0) {
	if($id_riga > 0) {
		webMovs::delWebMov('', $id_riga);
	} else {
		webMovs::delWebMov($id, '');
	}
}

print("Tra 3 sec. sar&agrave; automaticamente reindirizzato...<br>\n");

//header('Refresh: 3; URL=ddttoload.php');
goMain();
footer();
?>
