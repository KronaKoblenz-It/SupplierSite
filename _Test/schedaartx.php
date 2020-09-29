<?php 
$maga = $_GET['maga'];
header("Content-Disposition: attachment; filename=$maga.xml");
header('Content-Type: text/xml');
header('Content-Transfer-Encoding: binary'); 
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

$art = isset($_GET['art']) ? $_GET['art'] : "";
$eserc = $_GET['esercizio'];

$connectionstring = db_connect($dbase); 

print("<aw:mag xmlns:aw=\"http://www.Project-srl.it/ArcaWeb\">\n");


 
//query database 
$Query = "SELECT QUANTITA, QTACAR, QTASCAR, QTARET, DATAMOV, RIFDOC, LOTTO, CODICEARTI ";
$Query .= "FROM MAGMOV ";
$Query .= "WHERE MAGAZZINO = \"$maga\" ";
if($art != "") {
	$Query .= "AND CODICEARTI = \"$art\" ";
}
$Query .= "ORDER BY CODICEARTI, DATAMOV ";
$queryexe = db_query($connectionstring, $Query) or die(mysql_error());
$lastart = ""; 
$progr = 0;
while($row = db_fetch_row($queryexe)) {
	if( $row[7] != $lastart) {
		if( $lastart != "") {
			print("<aw:giacenza>$progr</aw:giacenza>\n");
			print("</aw:magart>\n");
		}
		$lastart = $row[7];
		$Query = "SELECT DESCRIZION, LOTTI FROM MAGART WHERE CODICE = \"$lastart\"";
		$qx1 = db_query($connectionstring, $Query) or die(mysql_error() ); 
		$rw1 = db_fetch_row($qx1);
		print("<aw:magart>\n");
		print("<aw:codice>$lastart</aw:codice>\n");
		print("<aw:descrizion>" . $rw1[0] . "</aw:descrizion>\n");
		print("<aw:lotti>" . $rw1[1] . "</aw:lotti>\n");

		$Query = "SELECT ALIAS FROM MAGALIAS WHERE IDPROG = 8 AND CODICEARTI = \"$lastart\"";
		$qx1 = db_query($connectionstring, $Query) or die(mysql_error() ); 
		$rw1 = db_fetch_row($qx1);
		print("<aw:alias>" . $rw1[0] . "</aw:alias>\n");

		// Giacenza iniziale
		$Query = "SELECT MAGGIAC.GIACINI ";
		$Query .= "FROM MAGGIAC ";
		$Query .= "WHERE MAGAZZINO = \"$maga\" AND ARTICOLO = \"$lastart\" AND ESERCIZIO = \"$eserc\"";
		$qx1 = db_query($connectionstring, $Query) or die(mysql_error()); 
		$rw1 = db_fetch_row($qx1);

		$progr = $rw1[0];
		print ("<aw:magmov>\n");
		print ("<aw:datamov>". current_year() . "-01-01</aw:datamov>\n"); 
		print ("<aw:rif>Giacenza iniziale</aw:rif>\n"); 
		print ("<aw:qtacar>" . $rw1[0] . "</aw:qtacar>\n"); 
		print ("<aw:qtasca>0</aw:qtasca>\n"); 
		print ("</aw:magmov>\n");
	}
	
    $progr += ($row[1] > 0 || $row[3] > 0 ? $row[0] : -$row[0]);
	print ("<aw:magmov>\n");
	print("<aw:codice>$lastart</aw:codice>\n");
	print ("<aw:datamov>" . $row[4] . "</aw:datamov>\n"); 
	print ("<aw:rif>" . $row[5] . "</aw:rif>\n"); 
	print ("<aw:qtacar>" . ($row[1] > 0 || $row[3] > 0 ? $row[0] : "0") . "</aw:qtacar>\n"); 
	print ("<aw:qtasca>" . ($row[2] > 0 || $row[3] < 0 ? $row[0] : "0") . "</aw:qtasca>\n"); 
	print ("<aw:lotto>" . $row[6] . "</aw:lotto>\n"); 
	print ("</aw:magmov>\n");
    } 

//diconnect from database 
db_close($connectionstring);

print("<aw:giacenza>$progr</aw:giacenza>\n");
print("</aw:magart>\n");
 
print("</aw:mag>\n");
?>