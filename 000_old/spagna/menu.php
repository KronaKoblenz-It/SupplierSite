<?php 
/************************************************************************/
/* CASASOFT ArcaWeb                               		        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003 by Roberto Ceccarelli                             */
/* http://casasoft.supereva.it                                          */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

include("header.php");
head();
$cookie = preg_split("/\|/",$_COOKIE['CodiceAgente']);
banner("Menu Principale",$cookie[1]);
?>
<table width="100%" border="0" cellspacing="1" cellpadding="1" bgcolor="#000000"> 
  <tr bgcolor="#CCFFFF">  
    <td height="22"><a href="rubrica-cli.php">Rubrica Clienti</a></td> 
  </tr> 
</table>
<?php
footer();
?>