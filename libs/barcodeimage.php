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

$code = trim($_GET['code']);

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
$im     = imagecreatetruecolor($bcwidth, $bcheight);
$black  = ImageColorAllocate($im,0x00,0x00,0x00);
$white  = ImageColorAllocate($im,0xff,0xff,0xff);
$red    = ImageColorAllocate($im,0xff,0x00,0x00);
$blue   = ImageColorAllocate($im,0x00,0x00,0xff);
imagefilledrectangle($im, 0, 0, $bcwidth, $bcheight, $white);

$data = Barcode::gd($im, $black, $bcwidth/2, $bcheight/2, $angle, $type, array('code'=>$code), 9, $bcheight);
imagepng($im);
?>