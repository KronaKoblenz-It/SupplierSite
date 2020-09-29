<?php 
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2014 by Roberto Ceccarelli                        */
/* http://strawberryfield.altervista.org								*/
/*                                                                      */
/************************************************************************/

include("header.php"); 
include("db-utils.php");

session_start();
$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
$fornitore = $cookie[0];
$maga = "F" . substr($fornitore, -4);

$conn = db_connect($dbase); 
 
$count = strtoupper($_POST['count']);
$anno = current_year();

$Query =  "SELECT CODICEARTI, DESCRIZION FROM DOCRIG WHERE ID =" . $_POST["r1"];
$rs = db_query($conn, $Query) or die(mysql_error()); 
$row = mysql_fetch_object($rs);
head();
banner(_("Rettifiche per") . " " .$row->CODICEARTI, $row->DESCRIZION);

$id_testa = ((time() % 10000) + substr($fornitore, -4)*10000) *100;
for($n = 1; $n <= $count; $n++) {
	$Query = "SELECT DOCRIG.ID, DOCRIG.ID_TESTA, DOCRIG.CODICEARTI, DOCRIG.DESCRIZION, DOCRIG.QUANTITA, DOCRIG.LOTTO, ";
	$Query .= "DOCRIG.TIPODOC, DOCRIG.NUMERODOC, DOCRIG.DATADOC, DOCRIG.U_CLIVEN ";
	$Query .= "FROM DOCRIG ";
	$Query .= "WHERE ID = " . $_POST["r$n"];
	$rs = db_query($conn, $Query) or die(mysql_error()); 
	$row = mysql_fetch_object($rs);
	if($row->QUANTITA != $_POST["qta$n"]) {
		$qta = $row->QUANTITA - $_POST["qta$n"];
		if ($qta < 0) {
			$qta = -$qta;
			$tipodoc = "KS";
		} else {
			$tipodoc = "SK";
		}
		scriviTesta($fornitore, $tipodoc, $maga, $row->CODICEARTI, $id_testa);
		// riga di commento
		scriviRiga($id_testa, $id_testa, $tipodoc, "", $fornitore, "", 1, "", $maga, "Rif. " . $row->TIPODOC . " " . $row->NUMERODOC . " del " . format_date($row->DATADOC), "C", $row->ID, $row->ID_TESTA);
		// riga articolo
		scriviRiga($id_testa+1, $id_testa, $tipodoc, "", $fornitore, $row->CODICEARTI,  $qta, $row->LOTTO, $maga, "", $row->U_CLIVEN, $row->ID, $row->ID_TESTA);
		$id_testa += 2;
	}
}

if(0 == ($id_testa % 100)) {
	$esito = _("Nessun documento da caricare");
} else {
	$esito = _("Documento caricato");
	mail("spedizioni@koblenz.it;ced@k-group.com;ced-it@k-group.com", "Generato documento sfridi da $fornitore",  "Il fornitore $fornitore ha creato documenti di sfrido per l'articolo ".$row->CODICEARTI, "From: automatico@k-group.com");
}
print("<br>$esito.\n<br<\n>");

print("<br>\n");
goEdit("artcons.php", _("Altra rettifica"));
goMain();
footer();

// ----------------


function scriviTesta($fornitore, $tipodoc, $maga, $rif, $id_testa) {
global $conn;
$Query = "INSERT INTO U_BARDT ";
$Query .= "(ID, DATADOC, ESERCIZIO, CODICECF, TIPODOC, NUMERODOC, NUMERODOCF, MAGPARTENZ, MAGARRIVO, DEL ) VALUES ( ";
$Query .= "$id_testa, ";
$Query .= "'" . date("Y-m-d") . "', '" . date("Y") . "', ";
$Query .= "\"$fornitore\", ";
$Query .= "\"$tipodoc\", \"\", \"$rif\", ";
if("SK" == $tipodoc) {
	$Query .= "\"$maga\", \"SC\", 0 )";
} else {
	$Query .= "\"SC\", \"$maga\", 0 )";
}

//print($Query."<br>");
$rs = db_query($conn, $Query) or die(mysql_error()); 
return $id_testa;
}


function scriviRiga($id, $id_testa, $tipodoc, $espldistin, $fornitore, $codicearti, $qta, $lotto, $maga, $descrizion, $cliven, $rifr, $rift) {
global $conn;
$Query = "INSERT INTO U_BARDR ";
$Query .= "(ID, ID_TESTA, ESPLDISTIN, DATADOC, CODICECF, TIPODOC, CODICEARTI, DESCRIZION, QUANTITA, LOTTO, NUMERODOC, MAGPARTENZ, MAGARRIVO, RIFFROMT, RIFFROMR, U_CLIVEN, DEL) VALUES ( ";
$Query .= "$id, ";
$Query .= "$id_testa, ";
$Query .= "\"$espldistin\", ";
$Query .= "'" . date("Y-m-d") . "', ";
$Query .= "\"$fornitore\", ";
$Query .= "\"$tipodoc\", ";
$Query .= "\"$codicearti\", ";
if( $codicearti != "") {
  $q1 = "SELECT DESCRIZION FROM MAGART WHERE CODICE =\"$codicearti\"";
//print($q1."<br>");
  $rs = db_query($conn, $q1) or die(mysql_error()); 
  $row = mysql_fetch_object($rs);
  $Query .= "\"" . str_replace('"', '""', $row->DESCRIZION) . "\", ";
} else {
  $Query .= "\"$descrizion\", ";
}
$Query .= "$qta, ";
$Query .= "\"$lotto\", ";
$Query .= '"", ';
if($tipodoc == "SK") {
	$Query .= "\"$maga\", \"SC\", ";
} else {
	$Query .= "\"SC\", \"$maga\", ";
}
$Query .= "$rift , $rifr , ";
$Query .= "\"$cliven\", ";
$Query .= " 0 )";
//print($Query."<br>");
$rs = db_query($conn, $Query) or die(mysql_error()); 
return $id + 1;  
}


?>