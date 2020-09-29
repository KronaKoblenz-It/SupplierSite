<?php
header('Content-Type: text/xml');
header ('Cache-Control: no-cache');
header ('Cache-Control: no-store' , false);     // false => this header not override the previous similar header
/************************************************************************/
/* Project ArcaWeb                               		 			    */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2012 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/
print("<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n");

include("header.php");
include("db-utils.php");

$conn = db_connect($dbase);

$cCodiceArt = $_GET['codart'];

$Query = "SELECT MAGART.CODICE, MAGART.DESCRIZION, MAGART.U_ARTCOLLO, MAGART.U_DESCOLLO as DESCOLLOEXTRA, MAGART.U_NCOLLI, COLLO.CODICE as CODCOLLO, COLLO.DESCRIZION as  DESCOLLO, MAGART.LOTTI ";
$Query .=" FROM MAGART LEFT JOIN MAGART as COLLO ON COLLO.CODICE = MAGART.U_ARTCOLLO";
$Query .= " WHERE MAGART.CODICE =\"$cCodiceArt\"";
$queryexe = db_query($conn, $Query) or die(mysql_error());

print("<doppiocollo>\n");
while($row = db_fetch_row($queryexe)){
    print("<articolo>\n");
        print("<codice>" . (is_null($row[0]) ? "" : $row[0] ). "</codice>\n");
        print("<descrizione>" . (is_null($row[1]) ? "" : $row[1]) . "</descrizione>\n");
        print("<artcollo>" . (is_null($row[2]) ? "" : $row[2]) . "</artcollo>");
        print("<descolloextra>" . (is_null($row[3]) ? "" : $row[3]) . "</descolloextra>\n");
        print("<ncolli>" . (is_null($row[4]) ? "" : $row[4]) . "</ncolli>\n");
        print("<codcollo>" . (is_null($row[5]) ? "" : $row[5]) . "</codcollo>\n");
        print("<descollo>" . (is_null($row[6]) ? "" : $row[6]) . "</descollo>\n");        
	 print("<islotto>" . (is_null($row[7]) ? "" : $row[7]) . "</islotto>\n");
    print("</articolo>\n");
}
print("</doppiocollo>\n");


//-----------------------
//diconnect from database 
db_close($conn);
?>