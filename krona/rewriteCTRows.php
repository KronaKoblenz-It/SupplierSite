<?php



include 'header.php';

include 'db-utils.php';



head();

session_start();



banner("</br> " . _("Ricostruzione della Righe CT F2884"), $dbase);



$conx = db_connect($GLOBALS['dbase']);

$n = 0;



$Query = "SELECT ID_RIGA, ID_TESTA, QUANTITA 

                    FROM U_WEBMOVS 

                    WHERE ID_TESTA IN (288467664)";

$result = db_query($conx, $Query) or die(mysql_error());



while ($rw = mysql_fetch_object($result)) {

    ++$n;

    $Query = "UPDATE U_BARDR SET QUANTITA = $rw->QUANTITA WHERE ID = $rw->ID_RIGA ";

    $qx = db_query($conx, $Query) or die(mysql_error());

}

mysqli_free_result($result);

db_close($conx);



print("<b>SISTEMATE $n righe</br></br></b>");



footer();

