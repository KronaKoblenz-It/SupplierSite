<?php

include 'header.php';
include 'db-utils.php';

$art = isset($_GET['art']) ? $_GET['art'] : '';
$eserc = $_GET['esercizio'];
$maga = $_GET['maga'];
$codForn = "F0" . substr($maga,1);

$connectionstring = db_connect($dbase);

$xml = new SimpleXMLElement('<aw:mag></aw:mag>');
$xml->addAttribute('timestamp', date('m/d/Y h:i:s', time()));
$xml->addAttribute('magazzino', $maga);
//query database
if ($art != '') {
    $Query = "SELECT DISTINCT ARTICOLO as CODART FROM MAGGIAC
              WHERE MAGAZZINO = '$maga' AND ESERCIZIO ='$eserc'
              AND ARTICOLO = '$art' ORDER BY ARTICOLO ";
} else {
    $Query = "SELECT DISTINCT ARTICOLO as CODART FROM MAGGIAC
              WHERE MAGAZZINO = '$maga' AND ESERCIZIO ='$eserc'
              ORDER BY ARTICOLO ";
}
//print($Query);
$queryexe = db_query($connectionstring, $Query) or die(mysql_error());

$codArt = '';
$id = 0;
$progr = 0;
$lotti = 0;

while ($row = mysql_fetch_object($queryexe)) {
    $codArt = trim($row->CODART);
    $lotti = 0;
    ++$id;
  // Ricavo Descrizione, GestioneLotto, UnMisura, Peso, Certificazione
  $Query = "SELECT DESCRIZION, LOTTI,
              UNMISURA, UNMISURA1, UNMISURA2, UNMISURA3,
              FATT1, FATT2, FATT3, PESOUNIT,
              U_PEFC, U_CE
              FROM MAGART WHERE CODICE = '$codArt' ";
    $qx = db_query($connectionstring, $Query) or die(mysql_error());
    $rw = mysql_fetch_object($qx);

    $descArt = trim($rw->DESCRIZION);
    $lotti = $rw->LOTTI;
    $unMis = $rw->UNMISURA;
    $unMis1 = $rw->UNMISURA1;
    $unMis2 = $rw->UNMISURA2;
    $unMis3 = $rw->UNMISURA3;
    $fatt1 = xRound($rw->FATT1);
    $fatt2 = xRound($rw->FATT2);
    $fatt3 = xRound($rw->FATT3);
    $pesoArt = xRound($rw->PESOUNIT);
    $pefc = $rw->U_PEFC;
    $ce = $rw->U_CE;
    // $ulc = $rw->U_ULC;

    // Ricavo CodAlternativo
    $Query = "SELECT CODARTFOR FROM CODALT WHERE CODCLIFOR = '$codForn' AND CODICEARTI = '$codArt'";
    $qx = db_query($connectionstring, $Query) or die(mysql_error());
    $rw = mysql_fetch_object($qx);
    $codAlt = trim($rw->CODARTFOR);

  // INIZIO CREAZIONE RECORD XML
  $magart = $xml->addChild('aw:magart');
    $magart->addAttribute('id', $id);
    $magart->addChild('aw:codice', $codArt);
    $magart->addChild('aw:descrizion', $descArt);
    $magart->addChild('aw:codArtForn', $codAlt);
    $magart->addChild('aw:umPr', $unMis);
    $magart->addChild('aw:pesoKG', $pesoArt);
    $magart->addChild('aw:isLotto', $lotti);

    $prodUm = $magart->addChild('aw:prodUm');
    if ($unMis1 != $unMis && $fatt1 != 0 && $fatt1 != 1) {
        $um1 = $prodUm->addChild('aw:umAlt');
        $um1->addChild('aw:um', $unMis1);
        $um1->addChild('aw:fatt', $fatt1);
    }
    if ($unMis2 != $unMis && $fatt2 != 0 && $fatt2 != 1) {
        $um2 = $prodUm->addChild('aw:umAlt');
        $um2->addChild('aw:um', $unMis2);
        $um2->addChild('aw:fatt', $fatt2);
    }
    if ($unMis3 != $unMis && $fatt3 != 0 && $fatt3 != 1) {
        $um3 = $prodUm->addChild('aw:umAlt');
        $um3->addChild('aw:um', $unMis3);
        $um3->addChild('aw:fatt', $fatt3);
    }

  // Ricavo Barcodes
  $Query = "SELECT IDPROG, ALIAS, UNMISURA FROM MAGALIAS WHERE IDPROG IN (8, 7, 6) AND CODICEARTI = '$codArt'";
    $qx = db_query($connectionstring, $Query) or die(mysql_error());
  // Apro TAG Barcodes
  $barcodes = $magart->addChild('aw:barcodes');
    while ($rw = mysql_fetch_object($qx)) {
        $alias = $barcodes->addChild('aw:alias');
        if ($rw->IDPROG == 8) {
            $alias->addChild('aw:um', $unMis);
        } else {
            $alias->addChild('aw:um', $rw->UNMISURA);
        }
        $alias->addChild('aw:ean', $rw->ALIAS);
    }

  // Ricavo Situazione GIACENZA Tot
  $Query = "SELECT MAGGIAC.GIACINI,
              MAGGIAC.PROGQTACAR as CARICO,
              MAGGIAC.PROGQTASCA as SCARICO,
              MAGGIAC.PROGQTARET as RETTIFICA
              FROM MAGGIAC
              WHERE ARTICOLO = '$codArt' AND MAGAZZINO = '$maga' AND ESERCIZIO ='$eserc' ";
    $qx = db_query($connectionstring, $Query) or die(mysql_error());
    $rw = mysql_fetch_object($qx);

    $giacini = xRound($rw->GIACINI);
    $carico = xRound($rw->CARICO);
    $scarico = xRound($rw->SCARICO);
    $rettifica = xRound($rw->RETTIFICA);
    $giacTot = $giacini + $carico - $scarico + $rettifica;

    $giacArt = $magart->addChild('aw:giacArt');
    $giacArt->addChild('aw:giacini', $giacini);
    $giacArt->addChild('aw:carico', $carico);
    $giacArt->addChild('aw:scarico', $scarico);
    $giacArt->addChild('aw:rettifica', $rettifica);
    $giacArt->addChild('aw:esistenza', $giacTot);

    $giacProg = $giacini;

  // INIZIO TAG MOVIMENTI
    $magmovs = $magart->addChild('aw:magmovs');
    $magmov = $magmovs->addChild('aw:magmov');
    $magmov->addChild('aw:datamov', current_year().'-01-01');
    $magmov->addChild('aw:rif', 'Giacenza Iniziale');
    $magmov->addChild('aw:lotto', '');
    $magmov->addChild('aw:qta', $giacini);
    $magmov->addChild('aw:giacProg', $giacProg);


  // Ricavo Situazione MOVIMENTI
  $Query = "SELECT QUANTITA, QTACAR, QTASCAR, QTARET, DATAMOV, RIFDOC, LOTTO
              FROM MAGMOV
              WHERE MAGAZZINO = '$maga' AND CODICEARTI = '$codArt'
              ORDER BY DATAMOV ";
    $qx = db_query($connectionstring, $Query) or die(mysql_error());

    while ($rw = mysql_fetch_object($qx)) {
        $dataMov = $rw->DATAMOV;
        $rifDoc = $rw->RIFDOC;
        $carMov = ($rw->QTACAR > 0 || $rw->QTARET > 0 ? xRound($rw->QUANTITA) : 0);
        $scarMov = ($rw->QTASCAR > 0 || $rw->QTARET < 0 ? xRound($rw->QUANTITA) : 0);
        $codLot = trim($rw->LOTTO);

        $giacProg = $giacProg + $carMov - $scarMov;

        $magmov = $magmovs->addChild('aw:magmov');
        $magmov->addChild('aw:datamov', $dataMov);
        $magmov->addChild('aw:rif', $rifDoc);
        $magmov->addChild('aw:lotto', $codLot);
        $magmov->addChild('aw:qta', ($carMov > 0 ? $carMov : -$scarMov));
        $magmov->addChild('aw:giacProg', $giacProg);
    }

    if ($lotti) {
        $maglots = $magart->addChild('aw:lotti');
        $Query = "SELECT LOTTO, MAGGIACL.PROGQTACAR, MAGGIACL.PROGQTASCA, MAGGIACL.PROGQTARET
                  FROM MAGGIACL
                  WHERE ARTICOLO = '$codArt' AND MAGAZZINO = '$maga' AND (PROGQTACAR-PROGQTASCA+PROGQTARET) != 0 ";
        $qx = db_query($connectionstring, $Query) or die(mysql_error());

        while ($rw = mysql_fetch_object($qx)) {
            $codLotto = $rw->LOTTO;
            $carLot = xRound($rw->PROGQTACAR);
            $scarLot = xRound($rw->PROGQTASCA);
            $rettLot = xRound($rw->PROGQTARET);
            $giacLot = $carLot - $scarLot + $rettLot;

            $maglot = $maglots->addChild('aw:lotto');
            $maglot->addChild('aw:codLotto', $codLotto);
            $maglot->addChild('aw:carLot', $carLot);
            $maglot->addChild('aw:scarLot', $scarLot);
            $maglot->addChild('aw:rettLot', $rettLot);
            $maglot->addChild('aw:giacLot', $giacLot);
        }
    }
}

// diconnect from database
db_close($connectionstring);

if ($art != '') {
  Header("Content-Disposition: attachment; filename=$maga-$art.xml");
} else {
  Header("Content-Disposition: attachment; filename=$maga.xml");
}
Header('Content-type: text/xml');
echo $xml->asXML();

// $newsXML = new SimpleXMLElement("<news></news>");
// $newsXML->addAttribute('newsPagePrefix', 'value goes here');
// $newsIntro = $newsXML->addChild('content');
// $newsIntro->addAttribute('type', 'latest');
// Header('Content-type: text/xml');
// echo $newsXML->asXML();
