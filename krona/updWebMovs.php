<?php

include '../libs/common.php';
include '../libs/baseheader.php';
include 'db-utils.php';
head_base();

$DB = 'krona';

echo '<b>AGGIORNO WebMovs in DB '.$DB.'</br></br></b>';

$nomeFile = $_GET['FileName'];
//connect to database
$connectionstring = db_connect($DB);

$Query = 'UPDATE U_BARDR SET DEL=1
            WHERE DEL!=1
              AND ID_TESTA IN (SELECT ID FROM U_BARDT WHERE DEL=1) ';

$queryexe = db_query($conn, $Query) or die(mysql_error());

$Query = 'UPDATE U_BARDT SET DEL=1
            WHERE DEL!=1
              AND ID IN (SELECT DISTINCT ID_TESTA FROM U_BARDR WHERE DEL=1)
              AND ID NOT IN (SELECT DISTINCT ID_TESTA FROM U_BARDR WHERE DEL!=1) ';

$queryexe = db_query($conn, $Query) or die(mysql_error());

echo '<b>Aggiornata U_BARDR </br></br></b>';

$Query = 'DELETE FROM U_WEBMOVS
            WHERE ID_TESTA IN (SELECT ID FROM U_BARDT WHERE DEL=1) ';

$queryexe = db_query($conn, $Query) or die(mysql_error());

echo '<b>Cancellati Movimenti WEB acquisiti in ARCA </br></br></b>';

db_close($connectionstring);
echo '</br></br>';
echo '<b>Aggiornamento Effettuato con Successo alle ore:</b>  ';
echo date('d/m/y : H:i:s', time());
echo '</br></br>';

echo '<b>ELIMINO FILE SEMAFORO</b>  ';
unlink("semaforo.txt");
