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

link_script();

$connectionstring = db_connect($dbase);

session_start();
$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
banner("Reso conto lavoro da ",$cookie[1]);
$codcf = $cookie[0];

$Query = <<<EOT
SELECT DOCRIG.CODICEARTI, MAX(DOCRIG.DESCRIZION) 
FROM DOCRIG INNER JOIN DOCTES ON DOCTES.ID = DOCRIG.ID_TESTA
INNER JOIN MAGART ON MAGART.CODICE = DOCRIG.CODICEARTI
WHERE DOCTES.TIPODOC IN ('FO', 'LO', 'OF', 'OL', 'OW')
AND DOCRIG.QUANTITARE > 0 AND DOCTES.CODICECF = '$codcf' 
GROUP BY DOCRIG.CODICEARTI
ORDER BY DOCRIG.CODICEARTI
EOT;

form_body($codcf, '', $Query, "esplodi.php");

goMain();
footer();
?>