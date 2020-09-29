<?php
header("Content-type: image/jpeg");
/************************************************************************/
/* CASASOFT ArcaWeb                               		        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2016 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/
include('Barcode.php');

$cLotto = trim($_GET['code']);

$fontSize = 9;
$marge    = 2;   // between barcode and hri in pixel
$height   = 25;   // barcode height in 1D ; module size in 2D
$width    = 1;    // barcode height in 1D ; not use in 2D
$angle    = 0;   // rotation in degrees : nb : non horizontable barcode might not be usable because of pixelisation

//  $code     = '123456789012'; // barcode, of course ;)
$type     = 'ean13';
$black    = '000000'; // color in hexa

// -------------------------------------------------- //
//            ALLOCATE GD RESSOURCE
// -------------------------------------------------- //

$bcwidth = 880;
$bcheight = 180;
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

}
imagepng($im);
?>