<?php 
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2017 by Roberto Ceccarelli                        */
/* http://strawberryfield.altervista.org								*/
/* 																		*/
/************************************************************************/

$anno = 2020;
$fornitore = $cookie[0];
define("UPLOAD_DIR", "./inv_dic_$anno/$fornitore/");
$mode = trim($_GET['mode']);
$attr = $mode=="attr" ? " attrezzature" : "";
$attr = $mode == "sfridi" ? " SFRIDI" : $attr;

?>