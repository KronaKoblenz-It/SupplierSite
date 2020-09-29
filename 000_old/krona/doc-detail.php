<?php 
/************************************************************************/
/* CASASOFT ArcaWeb                               		       			*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2013 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php");
include("db-utils.php");
include("../libs/doc-lib.php");
head();
$id = $_GET['id'];

//connect to database 
$connectionstring = db_connect($dbase); 

//SQL quyery  
$Query = "SELECT DATADOC, NUMERODOC, CODICECF, TIPODOC FROM DOCTES WHERE ID = $id";

//execute query 
$queryexe = db_query($connectionstring, $Query); 
$row = db_fetch_row($queryexe);
$isBollaCL = in_array($row[3], array("BT", "CE", "RL", "TL", "CP"));

banner( ("CP" == $row[3] ? "Carico di produzione" : ($isBollaCL ? $str_dettddt[$lang] : $str_dettord[$lang])),"N." . $row[1] . " del " . format_date($row[0]) );
$cf = $row[2];

// righe ordine
$id_testa = doc_rows( $id, $connectionstring);

// se sto mostrando le bolle del conto lavoro non serve altro
if( !$isBollaCL ) {
	// -------------------------------------------
	// leggo le bolle che derivano da queste righe
	// -------------------------------------------
	if(strlen($id_testa) > 0) {
	  $id_testa = doc_ddt( $id_testa, $connectionstring);
	}	

	// -------------------------------------------
	// leggo le fatture che derivano dalle bolle
	// -------------------------------------------
	if(strlen($id_testa) > 0) {
	  $id_testa = doc_fatt( $id_testa, $connectionstring);
	}

	// -------------------------------------------
	// infine andiamo a cercare anche le scadenze
	// -------------------------------------------
	if(strlen($id_testa) > 0) {
	  doc_scad( $id_testa, $connectionstring);
	}
}

//-----------------------
//diconnect from database 
db_close($connectionstring); 

print("<br>\n");

if( $isBollaCL ) {
	if("CP" == $row[3]) {
		print("<a class=\"bottommenu\" href=\"cli-detail.php?id=$cf\">");
		print("<img style=\"border: none;\" src=\"../img/05_edit.gif\" alt=\"Elenco CP\">Elenco CP</a>\n");
	} else {
		print("<a class=\"bottommenu\" href=\"bollecons.php?id=$cf\">");
		print("<img style=\"border: none;\" src=\"../img/05_edit.gif\" alt=\"Elenco DDT conto lavoro\">Elenco DDT conto lavoro</a>\n");
	}
} else {
	print("<a class=\"bottommenu\" href=\"cli-detail.php?id=$cf\">");
	print("<img style=\"border: none;\" src=\"../img/05_edit.gif\" alt=\"" . $str_eleord[$lang] . "\">" . $str_eleord[$lang] . "</a>\n");
}
goMain();

footer();
?>
