<?php
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2019 by Roberto Ceccarelli                        */
/* http://strawberryfield.altervista.org								*/
/*                                                                      */
/************************************************************************/

include 'header.php';
include 'db-utils.php';

session_start();
$cookie = preg_split("/\|/", $_SESSION['CodiceAgente']);
$fornitore = $cookie[0];
$maga = 'F'.substr($fornitore, -4);

$conn = db_connect($dbase);

$count = strtoupper($_POST['count']);
$anno = current_year();

head();
banner($_POST['padre']);
$nDocF = $_POST['numerodocf'];
$cliven = $_POST['cliven'];

$idtesta = (isset($_POST['idtesta']) ? $_POST['idtesta'] : 0);
$idriga = (isset($_POST['idriga']) ? $_POST['idriga'] : 0);


// Scrittura documento CT per krona

$tipodoc="CT";
if ($idtesta > 0) {
    if ($idriga > 0) {
        $Query = "DELETE FROM U_BARDR WHERE ID_RIFRIGA=$idriga AND ID_TESTA=$idtesta";
        $rs = db_query($conn, $Query) or die("$Query<br>".mysql_error());
        $Query = 'SELECT COUNT(U_BARDR.ID) AS RIGHE FROM U_BARDR ';
        $Query .= "WHERE ID_TESTA=$idtesta AND DEL <> 1";
        $rs = db_query($conn, $Query) or die("$Query<br>".mysql_error());
        $row = mysql_fetch_object($rs);
        if ($row->RIGHE = 0) {
            $Query = "DELETE FROM U_BARDT WHERE ID=$idtesta";
            $rs = db_query($conn, $Query) or die("$Query<br>".mysql_error());
        }
    } else {
        $Query = "DELETE FROM U_BARDT WHERE ID=$idtesta";
        $rs = db_query($conn, $Query) or die("$Query<br>".mysql_error());
        $Query = "DELETE FROM U_BARDR WHERE ID_TESTA=$idtesta";
        $rs = db_query($conn, $Query) or die("$Query<br>".mysql_error());
    }
}

// riferimenti
$Query = 'SELECT TIPODOC, NUMERODOC, DATADOC FROM DOCTES WHERE ID = '.$_POST['rift'];
$rs = db_query($conn, $Query) or die($Query.mysql_error());
$row = mysql_fetch_object($rs);

// ROBERTO 05.05.2015
// Se esiste già una testa per il fornitore ed il documento la riutilizziamo
// senza creare un documento nuovo
$Query = 'SELECT ID_TESTA AS ID FROM U_BARDR WHERE ';
$Query .= "CODICECF = '$fornitore' AND TIPODOC = '$tipodoc' AND ";
$Query .= 'CODICEARTI = "'.$_POST['padre'].'" AND LOTTO="'. $_POST['lottopadre'] .'" AND ESPLDISTIN = "P" AND DEL <> 1';
$rs = db_query($conn, $Query) or die($Query.mysql_error());
if ($testa = mysql_fetch_object($rs) && 1>2) {
    $id_testa = $testa->ID;
    $Query = "SELECT MAX(ID) AS ID_RIGA FROM U_BARDR WHERE ID_TESTA = $id_testa";
    $rs = db_query($conn, $Query) or die($Query.mysql_error());
    $riga = mysql_fetch_object($rs);
    $id = $riga->ID_RIGA + 1;
	$Query = "DELETE FROM U_BARDR WHERE ID_TESTA=$id_testa";
    $rs = db_query($conn, $Query) or die("$Query<br>".mysql_error());

} else {
    $id_testa = (time() % 100000) + substr($fornitore, -4) * 100000;

    $Query = 'INSERT INTO U_BARDT ';
    $Query .= '(ID, DATADOC, ESERCIZIO, CODICECF, TIPODOC, NUMERODOC, NUMERODOCF, MAGPARTENZ, MAGARRIVO, DEL, RIF_TIPODOC, RIF_NUMERODOC, RIF_DATADOC ) VALUES ( ';
    $Query .= "$id_testa, ";
    $Query .= "'".date('Y-m-d')."', '".date('Y')."', ";
    $Query .= "'$fornitore', ";
    $Query .= "'$tipodoc', '', '".$_POST['padre']."', ";
    $Query .= "'$maga', '$maga', 2, ";
    $Query .= '"'.$row->TIPODOC.'", "'.$row->NUMERODOC.'", "'.$row->DATADOC.'" )';
    //print($Query."<br>");
    $rs = db_query($conn, $Query) or die($Query.mysql_error());

    $id = ($id_testa % 1000000) * 1000;
}
// riga di commento
$id = scriviRiga($id, $id_testa, $id + 1, '', $fornitore, '', 1, '', $maga, 'Rif. '.$row->TIPODOC.' '.$row->NUMERODOC.' del '.format_date($row->DATADOC), 'C', $_POST['rifr'], 'CT');
$id_rigapadre = $id;

// riga del padre
$qtapadre = $_POST['quantita'];
$id = scriviRiga($id, $id_testa, $id_rigapadre, 'P', $fornitore, $_POST['padre'], $qtapadre, $_POST['lottopadre'], $maga, '', $cliven, $_POST['rifr'], 'CT');

// righe componenti
for ($j = 1; $j <= $count; ++$j) {
    $id = scriviRiga($id, $id_testa, $id_rigapadre, 'C', $fornitore, $_POST["code$j"], $_POST["qta$j"], $_POST["lotto$j"], $maga, '', 'C', $_POST["rifr$j"], 'CT', $_POST["qta$j"]/$qtapadre);
}

db_close($conn);
 
/* 
// ----------------------------------
// Scrittura CE per kuantica
$conn = db_connect_kuantica();
$maga = "C001F";
$fornitore = "F00043";
$tipodoc = "CE";

if ($idtesta > 0) {
    if ($idriga > 0) {
        $Query = "DELETE FROM U_BARDR WHERE ID_RIFRIGA=$idriga AND ID_TESTA=$idtesta";
        $rs = db_query($conn, $Query) or die("$Query<br>".mysql_error());
        $Query = 'SELECT COUNT(U_BARDR.ID) AS RIGHE FROM U_BARDR ';
        $Query .= "WHERE ID_TESTA=$idtesta AND DEL <> 1";
        $rs = db_query($conn, $Query) or die("$Query<br>".mysql_error());
        $row = mysql_fetch_object($rs);
        if ($row->RIGHE = 0) {
            $Query = "DELETE FROM U_BARDT WHERE ID=$idtesta";
            $rs = db_query($conn, $Query) or die("$Query<br>".mysql_error());
        }
    } else {
        $Query = "DELETE FROM U_BARDT WHERE ID=$idtesta";
        $rs = db_query($conn, $Query) or die("$Query<br>".mysql_error());
        $Query = "DELETE FROM U_BARDR WHERE ID_TESTA=$idtesta";
        $rs = db_query($conn, $Query) or die("$Query<br>".mysql_error());
    }
}

// ROBERTO 05.05.2015
// Se esiste già una testa per il fornitore ed il documento la riutilizziamo
// senza creare un documento nuovo
$Query = 'SELECT ID FROM U_BARDT WHERE ';
$Query .= "CODICECF = '$fornitore' AND TIPODOC = '$tipodoc' AND ";
$Query .= 'NUMERODOCF = "'.$_POST['numerodocf'].'" AND DEL <> 1';
$rs = db_query($conn, $Query) or die($Query.mysql_error());
if ($testa = mysql_fetch_object($rs)) {
    $id_testa = $testa->ID;
    $Query = "SELECT MAX(ID) AS ID_RIGA FROM U_BARDR WHERE ID_TESTA = $id_testa";
    $rs = db_query($conn, $Query) or die($Query.mysql_error());
    $riga = mysql_fetch_object($rs);
    $id = $riga->ID_RIGA + 1;
} else {
    $id_testa = (time() % 100000) + substr($fornitore, -4) * 100000;

    $Query = 'INSERT INTO U_BARDT ';
    $Query .= '(ID, DATADOC, ESERCIZIO, CODICECF, TIPODOC, NUMERODOC, NUMERODOCF, MAGPARTENZ, MAGARRIVO, DEL, RIF_TIPODOC, RIF_NUMERODOC, RIF_DATADOC ) VALUES ( ';
    $Query .= "$id_testa, ";
    $Query .= "'".date('Y-m-d')."', '".date('Y')."', ";
    $Query .= "'$fornitore', ";
    $Query .= "'$tipodoc', '', '".$_POST['numerodocf']."', ";
    $Query .= "'$maga', '00001', 0, ";
    $Query .= '"'.$row->TIPODOC.'", "'.$row->NUMERODOC.'", "'.$row->DATADOC.'" )';
    //print($Query."<br>");
    $rs = db_query($conn, $Query) or die($Query.mysql_error());

    $id = ($id_testa % 1000000) * 1000;
}

// riga di commento
$id = scriviRiga($id, $id_testa, $id + 1, '', $fornitore, '', 1, '', $maga, 'Rif. '.$row->TIPODOC.' '.$row->NUMERODOC.' del '.format_date($row->DATADOC), 'C', $_POST['rifr'], $tipodoc);
$id_rigapadre = $id;

// riga del padre
$id = scriviRiga($id, $id_testa, $id_rigapadre, 'P', $fornitore, $_POST['padre'], $_POST['quantita'], $_POST['lottopadre'], $maga, '', $cliven, $_POST['rifr'], $tipodoc);

// righe componenti
for ($j = 1; $j <= $count; ++$j) {
    $id = scriviRiga($id, $id_testa, $id_rigapadre, 'C', $fornitore, $_POST["code$j"], $_POST["qta$j"], $_POST["lotto$j"], $maga, '', 'C', $_POST["rifr$j"], $tipodoc);
}

db_close($conn);
*/
// ---------------------------------------------
// Chiusura pagina
echo "<br>Documento caricato.\n";

echo "<br>\n";
// echo '<a href="askdb-eurservice.php">';
// echo "<img border=\"0\" src=\"../img/05_edit.gif\" alt=\"Nuovo lancio\">Nuovo lancio</a>\n";
// echo "<br>\n";
goMain();
footer();

// ------------------------------------------
// Funzioni
function scriviRiga($id, $id_testa, $id_rifriga, $espldistin, $fornitore, $codicearti, $qta, $lotto, $maga, $descrizion, $cliven, $rifr, $tipodoc, $qtadist=0)
{
    global $conn, $nDocF;
    $Query = 'INSERT INTO U_BARDR ';
    $Query .= '(ID, ID_TESTA, ID_RIFRIGA, ESPLDISTIN, DATADOC, CODICECF, TIPODOC, CODICEARTI, DESCRIZION, QUANTITA, LOTTO, NUMERODOC, MAGPARTENZ, MAGARRIVO, RIFFROMT, RIFFROMR, U_CLIVEN, DEL, QTADIST, QTAORIG) VALUES ( ';
    $Query .= "$id, ";
    $Query .= "$id_testa, ";
    $Query .= "$id_rifriga, ";
    $Query .= "\"$espldistin\", ";
    $Query .= "'".date('Y-m-d')."', ";
    $Query .= "\"$fornitore\", ";
    $Query .= "'$tipodoc', ";
    $Query .= "\"$codicearti\", ";
    if ($codicearti != '') {
        $q1 = "SELECT DESCRIZION FROM MAGART WHERE CODICE =\"$codicearti\"";
        //print($q1."<br>");
        $rs = db_query($conn, $q1) or die($Query.mysql_error());
        $row = mysql_fetch_object($rs);
        $Query .= '"'.str_replace('"', '""', $row->DESCRIZION).'", ';
    } else {
        $Query .= "\"$descrizion\", ";
    }
    $Query .= "0, ";
    //$Query .= "$qta, ";
    $Query .= "\"$lotto\", ";
    $Query .= "'', '$maga', '$maga',";
    $Query .= $_POST['rift'].', '.$_POST['rifr'].', ';
    $Query .= "\"$cliven\", ";
    $Query .= " 2, $qtadist, $qta )";
    //print($Query."<br>");
    $rs = db_query($conn, $Query) or die($Query.mysql_error());

    if(!empty($codicearti)){
      webMovs::insWebMov($id_testa, $id, $id_rifriga, $tipodoc, date('Y-m-d'), $codicearti, $lotto, $qta, $maga, '00001', $nDocF);
    }

    return $id + 1;
}

// Prepara la connessione al database kuantica
function db_connect_kuantica() {
$db_link = mysql_connect("213.152.198.49", "my_kuantica", "fK4000na");
if (!$db_link) {
    die('Could not connect: ' . mysql_error());
}
mysql_select_db("kuantica");
return $db_link;
}
