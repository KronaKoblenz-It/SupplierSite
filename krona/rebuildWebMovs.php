<?php

include 'header.php';
include 'db-utils.php';

head();
session_start();

banner("</br> " . _("Ricostruzione della WebMovs"), $dbase);

echo '<b>Cancello tutte le movimentazioni precedenti </br></br></b>';
$del = webMovs::deleteAll();

echo '<b>Ricostruisco WebMovs</br></br></b>';
$row = webMovs::rebuildMovs();

print("<b>Generate $row righe</br></br></b>");

footer();
