<?php

/************************************************************************/
/* CASASOFT ArcaWeb                               		        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003 by Roberto Ceccarelli                             */
/* http://casasoft.supereva.it                                          */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

// Variabili globali per gestione database

// Seleziono il tipo di connessione
//$db_type = 'odbc';
 $db_type = 'mysql';


// ---------------------------------------
// Prepara la connessione al database odbc
function db_connect_odbc($dbase) {
return odbc_connect($dbase, "", ""); 
}

// Prepara la connessione al database mysql
function db_connect_mysql($dbase) {
$db_link = mysql_connect("localhost", "my_krona", "fK4000na"); 
mysql_select_db($dbase);
return $db_link;
}

// Prepara la connessione al database
function db_connect($dbase) {
global $db_type;
switch($db_type) {
  case 'odbc':
    return db_connect_odbc($dbase);
    break;
  case 'mysql':
    return db_connect_mysql($dbase);
    break;
  }  
}

// ---------------------------------------
// Esegue la query sul database odbc
function db_query_odbc($link,$query) {
return odbc_do($link, $query); 
}

// Esegue la query sul database mysql
function db_query_mysql($link,$query) {
return mysql_query($query); 
}

// Esegue la query
function db_query($link,$query) {
global $db_type;
switch($db_type) {
  case 'odbc':
    return db_query_odbc($link,$query);
    break;
  case 'mysql':
    return db_query_mysql($link,$query);
    break;
  }  
}

// ---------------------------------------
// Preleva la riga da odbc
function db_fetch_row_odbc($result) {
if (odbc_fetch_row($result))  {
  odbc_fetch_into($result, $array);
  return $array;
}
else  {
  return FALSE;
} 
}

// Preleva la riga da mysql
function db_fetch_row_mysql($result) {
return mysql_fetch_row($result); 
}

// Preleva la riga
function db_fetch_row($result) {
global $db_type;
switch($db_type) {
  case 'odbc':
    return db_fetch_row_odbc($result);
    break;
  case 'mysql':
    return db_fetch_row_mysql($result);
    break;
  }  
}

// ---------------------------------------
// Chiude il database odbc
function db_close_odbc($link) {
return odbc_close($link); 
}

// Chiude il database mysql
function db_close_mysql($link) {
return mysql_close($link); 
}

// Chiude il database
function db_close($link) {
global $db_type;
switch($db_type) {
  case 'odbc':
    return db_close_odbc($link);
    break;
  case 'mysql':
    return db_close_mysql($link);
    break;
  }  
}

?>