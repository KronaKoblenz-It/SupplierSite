<?php 
$maga = $_GET['maga'];
//header("Content-Disposition: attachment; filename=$maga.xml");
header('Content-Type: text/xml');
//header('Content-Transfer-Encoding: binary'); 
header ('Cache-Control: no-cache');
header ('Cache-Control: no-store' , false);     // false => this header not override the previous similar header
/************************************************************************/
/* Project ArcaWeb                               		 			    */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2014 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/
print("<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?>\n");

include("header.php"); 
include("db-utils.php");

$art = isset($_GET['art']) ? $_GET['art'] : "";
$eserc = $_GET['esercizio'];
$eserc = current_year();

$connectionstring = db_connect($dbase); 

print("<aw:mag xmlns:aw=\"http://www.Project-srl.it/ArcaWeb\">\n");


 
//query database 
$Query = "SELECT DISTINCT ARTICOLO FROM MAGGIAC ";
$Query .= "WHERE MAGAZZINO = \"$maga\"  AND ESERCIZIO =\"$eserc\" ";
if($art != "") {
	$Query .= "AND ARTICOLO = \"$art\" ";
}
$Query .= "ORDER BY ARTICOLO ";
//print($Query);
$queryexe = db_query($connectionstring, $Query) or die(mysql_error());
$lastart = ""; 
$progr = 0;
$lotti = 0;
while($row = db_fetch_row($queryexe)) {
	if( $row[0] != $lastart) {
		if( $lastart != "") {
			print ("\t\t</aw:magmovs>\n");
			if ($lotti){
				print ("\t\t<aw:lotti>\n");
				$Query = "SELECT LOTTO, MAGGIACL.PROGQTACAR, MAGGIACL.PROGQTASCA, MAGGIACL.PROGQTARET, PROGQTACAR-PROGQTASCA+PROGQTARET AS GIACENZA " ;
				$Query .= "FROM MAGGIACL ";
				$Query .= "WHERE ARTICOLO = \"" . trim($lastart). "\" ";
				$Query .= "AND MAGAZZINO = \"$maga\" AND LOTTO NOT LIKE \"%IC\" ";
				//$Query .= "ORDER BY LOTTO DESC ";
				$queryexe = db_query($connectionstring, $Query) or die(mysql_error()); 
				while($row2 = db_fetch_row($queryexe)) { 
						$lotto = $row2[0];
						$carico = number($row2[1]);
						$scarico = number($row2[2]);
						$rettifica = number($row2[3]);
						$giacenza = number($row2[4]);
						print ("\t\t\t<aw:lotto>\n");
						print ("\t\t\t\t<aw:codiceLotto>" . $lotto . "</aw:codiceLotto>\n"); 
						print ("\t\t\t\t<aw:caricoLotto>$carico</aw:caricoLotto>\n"); 
						print ("\t\t\t\t<aw:scaricoLotto>$scarico</aw:scaricoLotto>\n"); 
						print ("\t\t\t\t<aw:rettificaLotto>$rettifica</aw:rettificaLotto>\n");
						print ("\t\t\t\t<aw:giacLotto>$giacenza</aw:giacLotto>\n");
						print ("\t\t\t</aw:lotto>\n");						
					}
				print ("\t\t</aw:lotti>\n");
			}
			print("\t\t<aw:giacArt>" . xRound($progr) . "</aw:giacArt>\n");
			print("\t</aw:magart>\n");
		}
		$lastart = $row[0];
		$lotti = 0;
		$Query = "SELECT DESCRIZION, LOTTI, UNMISURA FROM MAGART WHERE CODICE = \"$lastart\"";
		$qx1 = db_query($connectionstring, $Query) or die(mysql_error() ); 
		$rw1 = db_fetch_row($qx1);
		$lotti = $rw1[1];
		$um = $rw1[2];
		print("\t<aw:magart>\n");
		print("\t\t<aw:codice>$lastart</aw:codice>\n");
		print("\t\t<aw:descrizion>" . $rw1[0] . "</aw:descrizion>\n");
		print("\t\t<aw:islotto>" . $lotti . "</aw:islotto>\n");
		print("\t\t<aw:unmisura>" . $um . "</aw:unmisura>\n");

		$Query = "SELECT ALIAS, UNMISURA FROM MAGALIAS WHERE IDPROG IN (8, 7, 6) AND CODICEARTI = \"$lastart\"";
		$qx1 = db_query($connectionstring, $Query) or die(mysql_error() ); 
		//$rw1 = db_fetch_row($qx1);
		print("\t\t<aw:barcodes>\n");
		while($rw1 = db_fetch_row($qx1)) {
			print("\t\t\t<aw:alias>\n");
			if($rw1[1] == ""){
				print("\t\t\t\t<aw:um>" . $um . "</aw:um>\n");
			} else {
				print("\t\t\t\t<aw:um>" . $rw1[1] . "</aw:um>\n");
			}
			print("\t\t\t\t<aw:code>" . $rw1[0] . "</aw:code>\n");
			print("\t\t\t</aw:alias>\n");
		}
		print("\t\t</aw:barcodes>\n");
		
		// Giacenza iniziale
		$Query = "SELECT MAGGIAC.GIACINI ";
		$Query .= "FROM MAGGIAC ";
		$Query .= "WHERE MAGAZZINO = \"$maga\" AND ARTICOLO = \"$lastart\" AND ESERCIZIO = \"$eserc\"";
		$qx1 = db_query($connectionstring, $Query) or die(mysql_error()); 
		$rw1 = db_fetch_row($qx1);

		$progr = $rw1[0];
		print ("\t\t<aw:magmovs>\n");
		print ("\t\t\t<aw:magmov>\n");
		print ("\t\t\t\t<aw:datamov>". current_year() . "-01-01</aw:datamov>\n"); 
		print ("\t\t\t\t<aw:rif>Giacenza iniziale</aw:rif>\n"); 
		print ("\t\t\t\t<aw:qtacar>" . xRound($rw1[0]) . "</aw:qtacar>\n"); 
		print ("\t\t\t\t<aw:qtasca>0</aw:qtasca>\n"); 
		print ("\t\t\t</aw:magmov>\n");
	}
	$Query = "SELECT QUANTITA, QTACAR, QTASCAR, QTARET, DATAMOV, RIFDOC, LOTTO, CODICEARTI ";
	$Query .= "FROM MAGMOV ";
	$Query .= "WHERE MAGAZZINO = \"$maga\" ";
	$Query .= "AND CODICEARTI = \"$lastart\" ";
	$Query .= "ORDER BY CODICEARTI, DATAMOV ";
    $qx1 = db_query($connectionstring, $Query) or die(mysql_error());
	while($rw1 = db_fetch_row($qx1)) {
		$progr += ($row[1] > 0 || $row[3] > 0 ? $row[0] : -$row[0]);
		print ("\t\t\t<aw:magmov>\n");
		print("\t\t\t\t<aw:codice>$lastart</aw:codice>\n");
		print ("\t\t\t\t<aw:datamov>" . $row[4] . "</aw:datamov>\n"); 
		print ("\t\t\t\t<aw:rif>" . $row[5] . "</aw:rif>\n"); 
		print ("\t\t\t\t<aw:qtacar>" . ($row[1] > 0 || $row[3] > 0 ? xRound($row[0]) : "0") . "</aw:qtacar>\n"); 
		print ("\t\t\t\t<aw:qtasca>" . ($row[2] > 0 || $row[3] < 0 ? xRound($row[0]) : "0") . "</aw:qtasca>\n"); 
		print ("\t\t\t\t<aw:lotto>" . $row[6] . "</aw:lotto>\n"); 
		print ("\t\t\t</aw:magmov>\n");
	}
} 

print ("\t\t</aw:magmovs>\n");

if ($lotti){
	print ("\t\t<aw:lotti>\n");
	$Query = "SELECT LOTTO, MAGGIACL.PROGQTACAR, MAGGIACL.PROGQTASCA, MAGGIACL.PROGQTARET, PROGQTACAR-PROGQTASCA+PROGQTARET AS GIACENZA " ;
	$Query .= "FROM MAGGIACL ";
	$Query .= "WHERE ARTICOLO = \"" . trim($art). "\" ";
	$Query .= "AND MAGAZZINO = \"$maga\" AND LOTTO NOT LIKE \"%IC\" ";
	//$Query .= "ORDER BY LOTTO DESC ";
	$queryexe = db_query($connectionstring, $Query) or die(mysql_error()); 
	while($row = db_fetch_row($queryexe)) { 

			$lotto = $row[0];
			$carico = number($row[1]);
			$scarico = number($row[2]);
			$rettifica = number($row[3]);
			$giacenza = number($row[4]);
			print ("\t\t\t<aw:lotto>\n");
			print ("\t\t\t\t<aw:codiceLotto>" . $lotto . "</aw:codiceLotto>\n"); 
			print ("\t\t\t\t<aw:caricoLotto>$carico</aw:caricoLotto>\n"); 
			print ("\t\t\t\t<aw:scaricoLotto>$scarico</aw:scaricoLotto>\n"); 
			print ("\t\t\t\t<aw:rettificaLotto>$rettifica</aw:rettificaLotto>\n");
			print ("\t\t\t\t<aw:giacLotto>$giacenza</aw:giacLotto>\n");
			print ("\t\t\t</aw:lotto>\n");
			
		}
	print ("\t\t</aw:lotti>\n");
}

//diconnect from database 
db_close($connectionstring);

print("\t\t<aw:giacArt>" . xRound($progr) ."</aw:giacArt>\n");
print("\t</aw:magart>\n");

print("</aw:mag>\n");
?>