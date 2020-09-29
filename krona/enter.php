<?php 
/************************************************************************/
/* CASASOFT ArcaWeb                     		          		        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2014 by Roberto Ceccarelli                        */
/* http://strawberryfield.altervista.org                                */
/*                                                                      */
/************************************************************************/

include("header.php"); 
include("db-utils.php");
// da questo punto gestione a sessioni
session_start();

//connect to database 
$connectionstring = db_connect("awusers", "", ""); 

//GESTIONE SPECIALE FORNITORE GRASS CHIUSO 01-05-2018
if( trim($_POST['codice']) == 'F02707') {
	ScriviAccesso($_POST['codice'], "VIETATO", "F", $dbase);
	session_write_close();
	Header("Location: login.php?error=1");
}

//SQL quyery  
$Query = "SELECT CODICE,DESCRIZION,TIPO,PASSWORD FROM ";
$Query = $Query . strtoupper($dbase);
$Query = $Query . " WHERE CODICE='" . $_POST['codice']. "'"; 

//execute query 
$queryexe = db_query($connectionstring, $Query) or die(mysql_error()); 

//query database 
if( $row = db_fetch_row($queryexe) ) { 
    $codice = $row[0]; 
    $name = $row[1];
     $tipo = $row[2];
    if( $row[3] == $_POST['password']) {
       session_start();
       $_SESSION["CodiceAgente"] = "$codice|$name|$tipo";
       switch($tipo) {
		 case "C":
           Header("Location: menu-cli.php");
           ScriviAccesso($codice, $name, $tipo, $dbase);
		   break;
		 case "A":
           Header("Location: menu.php");
           ScriviAccesso($codice, $name, $tipo, $dbase);
		   break;
		 case "F":
           Header("Location: menu-for.php");
           ScriviAccesso($codice, $name, $tipo, $dbase);
		   break;
		 default:  
           session_write_close();
       } 
     } else {
      session_write_close();
	  Header("Location: login.php?error=2");
     }    
} else {
	session_write_close();
	Header("Location: login.php?error=1");
}

//disconnect from database
db_close($connectionstring); 


function ScriviAccesso($codice, $name, $tipo, $dbase) {
    //connect to database
    $connectionstring = db_connect($dbase);

    //SQL quyery
    $Query = "INSERT INTO log_agenti (DESCRIZIONE, TIPO) VALUES(\"$codice - $name\", \"$tipo\")";

    //execute query
    $queryexe = db_query($connectionstring, $Query);

    //diconnect from database
    db_close($connectionstring);
}
?>
