<?php 
/************************************************************************/
/* CASASOFT ArcaWeb                               		        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2004 by Roberto Ceccarelli                        */
/* http://casasoft.supereva.it                                          */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

include("header.php");
include("db-utils.php");
include("../libs/doc-lib.php");
head();

//connect to database 
$connectionstring = db_connect($dbase); 

//SQL quyery  
$Query = "SELECT DATADOC,NUMERODOC FROM DOCTES WHERE ID = " . $_GET['id'];

//execute query 
$queryexe = db_query($connectionstring, $Query); 
$row = db_fetch_row($queryexe);
banner($str_dettfatt[$lang],"N." . $row[1] . " del " . format_date($row[0]) );

// righe dettaglio
$id_testa = doc_rows( $_GET['id'], $connectionstring);



// -------------------------------------------
// infine andiamo a cercare anche le scadenze
// -------------------------------------------
if(strlen($id_testa) > 0)
  doc_scad( $id_testa, $connectionstring);


//-----------------------
//diconnect from database 
db_close($connectionstring); 

footer();
?>
