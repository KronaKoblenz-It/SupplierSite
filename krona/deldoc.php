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
		$Query = "UPDATE U_BARDR SET DEL=1 WHERE ID_RIFRIGA=$id_riga AND ID_TESTA=$id";
		//echo $Query;
		$rs = db_query($conn, $Query) or die("$Query<br>" . mysql_error());
		$Query = "SELECT COUNT(U_BARDR.ID) AS RIGHE FROM U_BARDR ";
		$Query .= "WHERE ID_TESTA=$id AND DEL != 1";
		$rs = db_query($conn, $Query) or die("$Query<br>" . mysql_error());
		$row = mysql_fetch_object($rs);
		if($row->RIGHE = 0) {
			$Query = "UPDATE U_BARDT SET DEL=1 WHERE ID=$id";
			$rs = db_query($conn, $Query) or die("$Query<br>" . mysql_error());
		}
		webMovs::delWebMov('', $id_riga);
	} else {
		$Query = "UPDATE U_BARDT SET DEL=1 WHERE ID=$id";
		$rs = db_query($conn, $Query) or die("$Query<br>" . mysql_error());
		$Query = "UPDATE U_BARDR SET DEL=1 WHERE ID_TESTA=$id";
		$rs = db_query($conn, $Query) or die("$Query<br>" . mysql_error());
		webMovs::delWebMov($id, '');
	}
}

print("Tra 10 sec. sar&agrave; automaticamente reindirizzato...<br>\n");

header('Refresh: 10; URL=ddttoload.php');
goMain();
footer();
?>
