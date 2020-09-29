<?php
include('../libs/Barcode.php');
require('../libs/fpdf.php');
include("header.php");
include("db-utils.php");

// -------------------------------------------------- //
//                      USEFUL
// -------------------------------------------------- //
class eFPDF extends FPDF{
  function TextWithRotation($x, $y, $txt, $txt_angle, $font_angle=0)
  {
      $font_angle+=90+$txt_angle;
      $txt_angle*=M_PI/180;
      $font_angle*=M_PI/180;

      $txt_dx=cos($txt_angle);
      $txt_dy=sin($txt_angle);
      $font_dx=cos($font_angle);
      $font_dy=sin($font_angle);

      $s=sprintf('BT %.2F %.2F %.2F %.2F %.2F %.2F Tm (%s) Tj ET',$txt_dx,$txt_dy,$font_dx,$font_dy,$x*$this->k,($this->h-$y)*$this->k,$this->_escape($txt));
      if ($this->ColorFlag){
          $s='q '.$this->TextColor.' '.$s.' Q';
        }
      $this->_out($s);
  }
}

session_start();

$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
$conn = db_connect($dbase);
$umEtich = "CF";  // TIPOLOGIA DI ETICHETTA

$cCodice = $_GET['art'];
$cLotto = $_GET['lotto'];
$cDesc = (isset($_GET['desc']) ? $_GET['desc'] : "");
$code = (isset($_GET['code']) ? $_GET['code'] : "");
$cliven = (isset($_GET['cliven']) ? $_GET['cliven'] : "");
$clidest = (isset($_GET['clidest']) ? $_GET['clidest'] : "");
$dataEvas = (isset($_GET['devas']) ? $_GET['devas'] : "");
$dataStampa = date("d/m/Y", time());
$codForn = $cookie[0];

$numpack = 0; //Numero Pezzi su Etichetta
$barcode = ""; // CONTIENE UN BARCODE PERSONALIZZATO
$codkrona = ""; // E' UN VECCHIO CODICE PARAMETRIZZABILE NEI CODICI ALTERNATIVI
$codlingua = "IT"; // IL CODICE DELLA LINGUA PUO ESSERE PARAMETRIZZATO NEL COD ALT
$pathlogo = "../img/loghi/LOGO-KRONA.JPG"; // IL LOGO STANDARD
$isWurth=false; // LA WURTH HA UN ETICHETTATURA PARTICOLARE

// CERTIFICAZIONI
$lCE = "";
$lPEFC = "";

// -------------------------------------------------- //
//                  PROPERTIES
// -------------------------------------------------- //

//Come prima cosa verifico se il codice articolo esiste o è un codice alternativo
$cCodiceAlt = "";
if($cCodice != ""){
  $Query = "SELECT CODICE, UNMISURA FROM MAGART WHERE CODICE = \"$cCodice\"";
  $queryexe = db_query($conn, $Query);
  $row = mysql_fetch_object($queryexe);
  $cUMPr = $row->UNMISURA;
  if($row->CODICE == ""){
    //Vuol dire che il codice è un codice alternativo
    $Query = "SELECT CODICEARTI FROM CODALT WHERE CODARTFOR = \"$cCodice\" AND CODCLIFOR = \"$cliven\"";
    $queryexe = db_query($conn, $Query);
    $row = mysql_fetch_object($queryexe);
    $cCodiceAlt = $cCodice;
    $cCodice = $row->CODICEARTI;
  }
}

if("" == $cDesc) {
	$Query = "SELECT DESCRIZION FROM MAGART WHERE CODICE = \"$cCodice\"";
	$queryexe = db_query($conn, $Query) or die(mysql_error());
	$row = mysql_fetch_object($queryexe);
	$cDesc = $row->DESCRIZION;
}

$Query = "SELECT QTACONF, U_CE, UNMISURA1, FATT1, UNMISURA2, FATT2, UNMISURA3, FATT3, U_PEFC FROM MAGART WHERE CODICE = '" . $cCodice . "'";
$queryexe = db_query($conn, $Query) or die(mysql_error());
$row = mysql_fetch_object($queryexe);
if($cUMPr.trim() == $umEtich && $umEtich != "PZ") {
	if("PZ" == $row->UNMISURA1) {
		$numpack = floor(1/$row->FATT1);
	} else if("PZ" == $row->UNMISURA2) {
		$numpack = floor(1/$row->FATT2);
	} else if("PZ" == $row->UNMISURA3) {
		$numpack = floor(1/$row->FATT3);
	} else {
		$numpack = $row->QTACONF;
	}
} else {
	if($umEtich == $row->UNMISURA1) {
		$numpack = $row->FATT1;
	} else if($umEtich == $row->UNMISURA2) {
	   $numpack = $row->FATT2;
	} else if($umEtich == $row->UNMISURA3) {
		$numpack = $row->FATT3;
	} else {
		$numpack = $row->QTACONF;
	}
}
$lCE = $row->U_CE;
$lPEFC = $row->U_PEFC;

if("" == $code) {
	if($cUMPr.trim() == $umEtich) {
		$Query = "SELECT ALIAS FROM MAGALIAS WHERE IDPROG = 8 AND CODICEARTI = \"$cCodice\" ";
		$queryexe = db_query($conn, $Query) or die(mysql_error());
		$row = mysql_fetch_object($queryexe);
		$code = $row->ALIAS;
	} else {
		$Query = "SELECT ALIAS FROM MAGALIAS WHERE CODICEARTI = \"$cCodice\" AND UNMISURA = \"$umEtich\" ";
		$queryexe = db_query($conn, $Query) or die(mysql_error());
		$row = mysql_fetch_object($queryexe);
		$code = $row->ALIAS;
	}
}

if("" != $cliven && $clidest != ""){
    $Query = "SELECT CODARTFOR, LINGUA, LOGO, BARCODE FROM U_CODALT
                WHERE CODICEARTI = '$cCodice' AND CODCLIFOR = '$cliven' AND CODICEDES = '$clidest' ";
    $queryexe = db_query($conn, $Query) or die(mysql_error());
    $row = mysql_fetch_object($queryexe);
    $cCodiceAlt = $row->CODARTFOR;
    $codkrona = "";
    $codlingua = $row->LINGUA;
    $pathlogo = $row->LOGO;
    $barcode = $row->BARCODE;
} else {
  if("" != $cliven){
      $Query = "SELECT U_BARCODE FROM CODALT WHERE CODICEARTI = '" . $cCodice . "' AND CODCLIFOR = '" . $cliven . "'";
      $queryexe = db_query($conn, $Query) or die(mysql_error());
      $row = mysql_fetch_object($queryexe);
      $barcode = $row->U_BARCODE;

      $Query = "SELECT U_CODKRONA, LINGUA, U_LOGO FROM ANAGRAFE WHERE CODICE = '" . $cliven . "'";
      $queryexe = db_query($conn, $Query) or die(mysql_error());
      $row = mysql_fetch_object($queryexe);
      $codkrona = $row->U_CODKRONA;
      $codlingua = $row->LINGUA;
      $pathlogo = $row->U_LOGO;
  }
}

if(trim($codlingua) == "IN"){
  // LA LINGUA INDIANA HA UNA PARAMETRIZZAZIONE PARTICOLARE PER LE SOLE etich_india
	$codlingua = "UK";
}

if(trim($codlingua) != "IT"){
    $Query = "SELECT DESCRIZION FROM MAGLANG WHERE CODICEARTI = '" . $cCodice ."' AND CODLINGUA = '" . trim($codlingua) . "'";
    $queryexe = db_query($conn, $Query) or die(mysql_error());
    $row = mysql_fetch_object($queryexe);
    if($row->DESCRIZION != ""){
        //Almeno se la descrizione è vuota prendo la descrizione passata (che è quella in italiano)
        $cDesc = $row->DESCRIZION;
    }
}

if($pathlogo == ""){
  $pathlogo = "../img/loghi/LOGO-KRONA.JPG";
}
if($pathlogo != "../img/loghi/LOGO-KRONA.JPG"){
  $pathlogo = str_replace("D:\\ARCA\\ARCA_ITALIA\\LOGHI_SITO\\", "../img/loghi/", $pathlogo);
}
if(stripos($pathlogo, "WURTH")>0){
  $isWurth=true;
  if ($numpack==0 && $umEtich=="CF") {
    $numpack = 1;
  }
}

// -------------------------------------------------- //
//            INIZIO LA STAMPA DELL'ETICHETTA
// -------------------------------------------------- //
$fontSize = 9;
$marge    = 2;   // between barcode and hri in pixel
$height   = 25;   // barcode height in 1D ; module size in 2D
$width    = 1;    // barcode height in 1D ; not use in 2D
$angle    = 0;   // rotation in degrees : nb : non horizontable barcode might not be usable because of pixelisation
$type     = 'ean13';
$black    = '000000'; // color in hexa

// -------------------------------------------------- //
//            ALLOCATE GD RESSOURCE
// -------------------------------------------------- //
$bcwidth = 880;
$bcheight = 180;
$im     = imagecreatetruecolor($bcwidth, $bcheight);
$black  = ImageColorAllocate($im,0x00,0x00,0x00);
$white  = ImageColorAllocate($im,0xff,0xff,0xff);
$red    = ImageColorAllocate($im,0xff,0x00,0x00);
$blue   = ImageColorAllocate($im,0x00,0x00,0xff);
imagefilledrectangle($im, 0, 0, $bcwidth, $bcheight, $white);

// -------------------------------------------------- //
//            ALLOCATE FPDF RESSOURCE
// -------------------------------------------------- //
$pdf = new eFPDF("L","pt",array(360*72/254, 880*72/254));
$pdf->AddPage();

$x0 = $ex*4*72;
$x = $x0 + 2.9*72;	 // barcode center
$y0 = $ey*360/254*72;
$y = $y0 + 50;	// barcode center

if($isWurth && $umEtich=="CF"){
 //STAMPO ETICHETTE STILE WURTH
 if("" == $barcode){
   //prendo l'alias
   $data = Barcode::gd($im, $black, $bcwidth/2, $bcheight/2, $angle, $type, array('code'=>$code), 9, $bcheight);
 } else {
   //altrimenti prendo il barcode alternativo
   $data = Barcode::gd($im, $black, $bcwidth/2, $bcheight/2, $angle, $type, array('code'=>$barcode), 9, $bcheight);
 }
 //Creo immagine con il codice a barre
 imagejpeg($im, "etichetta.jpg");
 imagedestroy($im);
 $pdf->Image("etichetta.jpg", 15, 7, 100 );

 // -------------------------------------------------- //
 //                      HRI
 // -------------------------------------------------- //
 $ean13_1 = substr($data['hri'], 0, 1);
 $ean13_2 = substr($data['hri'], 1, 6);
 $ean13_3 = substr($data['hri'], 7, 6);
 $pdf->SetFont('Arial','',$fontSize);
 $pdf->SetTextColor(0, 0, 0);
 $len = $pdf->GetStringWidth($data['hri']);

 $pdf->SetFillColor(255,255,255);
 $pdf->Rect(20,22,43,8,'F');
 $pdf->Rect(67,22,43,8,'F');
 $pdf->Text(9, 30, $ean13_1);
 $pdf->Text(27, 30, $ean13_2);
 $pdf->Text(73, 30, $ean13_3);
 $pdf->SetFillColor(0,0,0);

 // -------------------------------------------------- //
 //                      BARCODE LOTTO
 // -------------------------------------------------- //
 $bclottolen = strlen($cLotto);
 $charwidth = 55;
 $bclottowidth = $bclottolen * $charwidth + 120;
 $bclottoheight = 60;
 $im     = imagecreatetruecolor($bclottowidth, $bclottoheight);
 $black  = ImageColorAllocate($im,0x00,0x00,0x00);
 $white  = ImageColorAllocate($im,0xff,0xff,0xff);
 $red    = ImageColorAllocate($im,0xff,0x00,0x00);
 $blue   = ImageColorAllocate($im,0x00,0x00,0xff);
 imagefilledrectangle($im, 0, 0, $bclottowidth, $bclottoheight, $white);

 if("" != $cLotto){
   $data = Barcode::gd($im, $black, $bclottowidth/2, $bclottoheight/2, $angle, 'code39', array('code'=>$cLotto), 4, $bclottoheight);
   //Creo immagine con il codice a barre
   imagejpeg($im, "etichettalotto.jpg");
   imagedestroy($im);

   $pdf->Image("etichettalotto.jpg", 7, 38, 130 );
   $pdf->SetFillColor(255,255,255);
   $pdf->Rect(0,31,200,8, 'F');
   $pdf->Rect(0,51,200,25, 'F');

   $pdf->SetFont('Arial','B','6');
   $pdf->Text(13, 57, "Lotto: $cLotto");
 } else {
   // INSERISCO DATA DI PRODUZIONE
   $pdf->SetFont('Arial', '', '6');
   $pdf->Text(125, 12, "$dataStampa");
 }

 // -------------------------------------------------- //
 //               LOGO KK o CLIVEN
 // -------------------------------------------------- //
 $pdf->Image($pathlogo, 185, 7 ,55);

 // -------------------------------------------------- //
 //                      LOGO CE
 // -------------------------------------------------- //
 if($lCE == true) {
   $pdf->Image("../img/loghi/Ce-logo.jpg", 220, 64, 24 );
 }

 // -------------------------------------------------- //
 //                      LOGO PEFC
 // -------------------------------------------------- //
 if($lPEFC == true) {
   $pdf->SetFont('Arial','B','8');
   $pdf->Text(20,78,"100% PEFC Certified-ICILA-PEFC COC-002700");
   $pdf->Image("../img/loghi/LOGO-PEFC.JPG", 135, 2, 49 );
 }

 // -------------------------------------------------- //
 //                      LOGO PACKAGE
 // -------------------------------------------------- //
 if ($umEtich!="PZ") {
   $pdf->SetFont('Arial','B','6');
   $pdf->Text(170, 94, $umEtich);
   $pdf->Image("../img/loghi/emblem_package.jpg", 180, 84, 10 );
 }
 // -------------------------------------------------- //
 //                      QTA x CONF
 // -------------------------------------------------- //
 $pdf->SetFont('Arial','B','6');
 $pdf->Text(195, 94, 'pz');
 $pdf->SetFont('Arial','B','12');
 $pdf->Text(210, 94, $numpack);

 // -------------------------------------------------- //
 //                DESCRIZIONE ARTICOLO
 // -------------------------------------------------- //
 $pdf->SetFont('Arial','','8');
 $pdf->Text(9, 70, ucfirst(strtolower($cDesc)));

 // -------------------------------------------------- //
 //                CODICE ARTICOLO
 // -------------------------------------------------- //
 $pdf->SetFont('Arial', 'B', '6');
 $pdf->Text(162, 70, 'Art.');
 $pdf->SetFont('Arial', 'B', '11');
 if ($cCodiceAlt == '') {
     $pdf->Text(178, 70, $cCodice);
 } else {
     $pdf->Text(178, 70, $cCodiceAlt);
 }

} else {

  //STAMPO ETICHETTE NORMALI
  if($numpack != 0){
    if("" == $barcode){
      //prendo l'alias
      $data = Barcode::gd($im, $black, $bcwidth/2, $bcheight/2, $angle, $type, array('code'=>$code), 9, $bcheight);
    } else {
      //altrimenti prendo il barcode alternativo
      $data = Barcode::gd($im, $black, $bcwidth/2, $bcheight/2, $angle, $type, array('code'=>$barcode), 9, $bcheight);
    }
    //Creo immagine con il codice a barre
    imagejpeg($im, "etichetta.jpg");
    imagedestroy($im);
    $pdf->Image("etichetta.jpg", 15, 7, 100 );

    // -------------------------------------------------- //
    //                      HRI
    // -------------------------------------------------- //
    $ean13_1 = substr($data['hri'], 0, 1);
    $ean13_2 = substr($data['hri'], 1, 6);
    $ean13_3 = substr($data['hri'], 7, 6);
    $pdf->SetFont('Arial','',$fontSize);
    $pdf->SetTextColor(0, 0, 0);
    $len = $pdf->GetStringWidth($data['hri']);

    $pdf->SetFillColor(255,255,255);
    $pdf->Rect(20,22,43,8,'F');
    $pdf->Rect(67,22,43,8,'F');
    $pdf->Text(9, 30, $ean13_1);
    $pdf->Text(27, 30, $ean13_2);
    $pdf->Text(73, 30, $ean13_3);

    $pdf->SetFillColor(0,0,0);

    // -------------------------------------------------- //
    //                      BARCODE LOTTO
    // -------------------------------------------------- //
    $bclottolen = strlen($cLotto);
    $charwidth = 55;
    $bclottowidth = $bclottolen * $charwidth + 120;
    $bclottoheight = 60;
    $im     = imagecreatetruecolor($bclottowidth, $bclottoheight);
    $black  = ImageColorAllocate($im,0x00,0x00,0x00);
    $white  = ImageColorAllocate($im,0xff,0xff,0xff);
    $red    = ImageColorAllocate($im,0xff,0x00,0x00);
    $blue   = ImageColorAllocate($im,0x00,0x00,0xff);
    imagefilledrectangle($im, 0, 0, $bclottowidth, $bclottoheight, $white);

    if("" != $cLotto){
      $data = Barcode::gd($im, $black, $bclottowidth/2, $bclottoheight/2, $angle, 'code39', array('code'=>$cLotto), 4, $bclottoheight);
      //Creo immagine con il codice a barre
      imagejpeg($im, "etichettalotto.jpg");
      imagedestroy($im);

      $pdf->Image("etichettalotto.jpg", 7, 38, 130 );
      $pdf->SetFillColor(255,255,255);
      $pdf->Rect(0,31,200,8, 'F');
      $pdf->Rect(0,51,200,25, 'F');

      $pdf->SetFont('Arial','B','6');
      $pdf->Text(13, 57, "Lotto: $cLotto");
      // INSERISCO DATA DI PRODUZIONE
      $pdf->Text(100, 57, "$dataStampa");
    } else {
      // INSERISCO DATA DI PRODUZIONE
      $pdf->SetFont('Arial', '', '6');
      $pdf->Text(13, 57, "$dataStampa");
    }
    // -------------------------------------------------- //
    //               COD KRONA E FORNITORE
    // -------------------------------------------------- //
    if($codkrona!='' || $codForn!=''){
    	$pdf->SetFont('Arial','B','6');
    	$pdf->Text(125, 22, "For.: $codkrona");
    	$pdf->Text(125, 28, "$codForn");
    }

    // -------------------------------------------------- //
    //               LOGO KK o CLIVEN
    // -------------------------------------------------- //
    if($pathlogo == "../img/loghi/LOGO-KRONA.jpg"){
      $pdf->Image($pathlogo, 205, 7, 35 );
    } else {
      $pdf->Image($pathlogo, 185, 7 ,55);
    }

    // -------------------------------------------------- //
    //                      LOGO CE
    // -------------------------------------------------- //
    if($lCE == true) {
    	$pdf->Image("../img/loghi/Ce-logo.jpg", 220, 64, 24 );
    }

    // -------------------------------------------------- //
    //                      LOGO PEFC
    // -------------------------------------------------- //
    if($lPEFC == true) {
    	$pdf->SetFont('Arial','B','8');
    	$pdf->Text(20,78,"100% PEFC Certified-ICILA-PEFC COC-002700");
    	$pdf->Image("../img/loghi/LOGO-PEFC.JPG", 135, 2, 49 );
    }

    // -------------------------------------------------- //
    //                      LOGO PACKAGE
    // -------------------------------------------------- //
    if ($umEtich!="PZ") {
      $pdf->SetFont('Arial','B','6');
      $pdf->Text(170, 94, $umEtich);
      $pdf->Image("../img/loghi/emblem_package.jpg", 180, 84, 10 );
    }

    // -------------------------------------------------- //
    //                      QTA x CONF
    // -------------------------------------------------- //
    $pdf->SetFont('Arial','B','6');
    $pdf->Text(195, 94, 'pz');
    $pdf->SetFont('Arial','B','12');
    $pdf->Text(210, 94, $numpack);

    // -------------------------------------------------- //
    //                DESCRIZIONE ARTICOLO
    // -------------------------------------------------- //
    $pdf->SetFont('Arial','','8');
    $pdf->Text(9,70,$cDesc);

    // -------------------------------------------------- //
    //                CODICE ARTICOLO
    // -------------------------------------------------- //
    $pdf->SetFont('Arial','B','6');
    $pdf->Text(9, 94, "Art.");
    $pdf->SetFont('Arial','B','12');
    if($cCodiceAlt == ""){
      $pdf->Text(25, 94, $cCodice);
    }
    else{
      $pdf->Text(25, 94, $cCodiceAlt);
    }
  } else {
  	$pdf->SetFont('Arial','B','32');
  	$pdf->Text(30, 62, "X");
  	$pdf->SetFont('Arial','B','12');
  	$pdf->Text(55, 55, "Nessun Etichetta per CF");
  }
}
$pdf->Output();

// -------------------------------------------------- //
//                      END
// -------------------------------------------------- //

?>
