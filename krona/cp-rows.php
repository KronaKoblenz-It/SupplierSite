<?php 
/************************************************************************/
/* CASASOFT ArcaWeb                               		      			*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2014 by Roberto Ceccarelli                        */
/* http://strawberryfield.altervista.org								*/
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
$tipodoc = $row[3];

banner($str_dettddt[$lang], "N." . $row[1] . " del " . format_date($row[0]) );
$cf = $row[2];

// righe ordine
$id_testa = cp_rows( $id, $connectionstring);

// -------------------------------------------
// leggo le fatture che derivano dalle bolle
// -------------------------------------------
/*if(strlen($id_testa) > 0) {
  $id_testa = doc_fatt( $id_testa, $connectionstring);
}*/

//-----------------------
//diconnect from database 
db_close($connectionstring); 

print("<br>\n");
goMain();

footer();
?>