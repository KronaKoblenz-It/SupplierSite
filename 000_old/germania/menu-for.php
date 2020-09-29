<?php 
/************************************************************************/
/* CASASOFT ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2011 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php");
head();
$cookie = preg_split("/\|/",$_COOKIE['CodiceAgente']);
banner("Menu Principale",$cookie[1]);

print('<table width="100%" border="0" cellspacing="1" cellpadding="1" bgcolor="#000000"> ');
print('  <tr bgcolor="#CCFFFF">  ');
print('    <td height="22"><a href="askdb.php">Inserimento bolla</a></td> ');
print("  </tr> ");
print('  <tr bgcolor="#CCFFFF">  ');
print('    <td height="22"><a href="ddttoload.php">Bolle in attesa di acquisizione</a></td> ');
print("  </tr> ");
print('  <tr bgcolor="#CCFFFF">  ');
print('    <td height="22"><a href="inventario.php">Inserimento inventario</a></td> ');
print("  </tr> ");
print("</table>");

footer();
?>