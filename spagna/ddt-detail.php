<?php 
/************************************************************************/
/* CASASOFT ArcaWeb                               		        		*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2012 by Roberto Ceccarelli                        */
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
if(userType() == "F") {
  $Query = "SELECT DATADOCFOR,NUMERODOCF,CODICECF FROM DOCTES WHERE ID = $id";
} else {
  $Query = "SELECT DATADOC,NUMERODOC,CODICECF FROM DOCTES WHERE ID = $id";
}

//execute query 
$queryexe = db_query($connectionstring, $Query); 
$row = db_fetch_row($queryexe);
banner($str_dettddt[$lang],"N." . $row[1] . " del " . format_date($row[0]) );
$cf = $row[2];

// righe dettaglio
$id_testa = doc_rows( $id, $connectionstring);

// -------------------------------------------
// leggo le fatture che derivano dalle bolle
// -------------------------------------------
if(strlen($id_testa) > 0)
  $id_testa = doc_fatt( $id_testa, $connectionstring);


// -------------------------------------------
// infine andiamo a cercare anche le scadenze
// -------------------------------------------
if(strlen($id_testa) > 0)
  doc_scad( $id_testa, $connectionstring);


//-----------------------
//diconnect from database 
db_close($connectionstring); 

print("<br>\n");
print("<a href=\"cli-detail.php?id=$cf\">");
print("<img border=\"0\" src=\"05_edit.gif\" alt=\"" . $str_eleord[$lang] . "\">" . $str_eleord[$lang] . "</a>\n");

print("<br>\n");
goMain();

footer();
?>
