<?php 
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2020 by Roberto Ceccarelli                        */
/* http://strawberryfield.altervista.org								*/
/* 																		*/
/************************************************************************/

include("header.php"); 
include("db-utils.php");
include("askdb_common.php");

head();
$gruppoFiltro = $_GET["gruppo"];

link_script();

$connectionstring = db_connect($dbase);

session_start();
$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
banner("Lancio produzione Macchine",$cookie[1]);
$codcf = $cookie[0];

$Query = <<<EOT
SELECT DOCRIG.CODICEARTI, MAX(DOCRIG.DESCRIZION) 
FROM DOCRIG INNER JOIN DOCTES ON DOCTES.ID = DOCRIG.ID_TESTA
INNER JOIN MAGART ON MAGART.CODICE = DOCRIG.CODICEARTI
WHERE (DOCTES.TIPODOC='FO' OR DOCTES.TIPODOC='LO' OR DOCTES.TIPODOC='OF' OR DOCTES.TIPODOC='OL')
AND DOCRIG.QUANTITARE > 0 AND DOCTES.CODICECF = '$codcf' 
AND MAGART.GRUPPO = '$gruppoFiltro' 
GROUP BY DOCRIG.CODICEARTI
ORDER BY DOCRIG.CODICEARTI
EOT;

form_body($codcf, $gruppoFiltro, $Query "esplodidenken.php");

goMain();
footer();
?>