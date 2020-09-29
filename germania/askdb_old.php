<?php 
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2011 by Roberto Ceccarelli                        */
/* 																		*/
/************************************************************************/

include("header.php"); 
head();
$cookie = preg_split("/\|/",$_COOKIE['CodiceAgente']);
banner("Reso conto lavoro da ",$cookie[1]);
?>
<form action="esplodi.php" method="get" >
<table>
<tr><td>Articolo</td><td><input type="text" name="articolo" id="articolo" ></td></tr>
<tr><td>Lotto</td><td><input type="text" name="lotto" id="lotto" ></td></tr>
<tr><td>Quantita</td><td><input type="text" name="quantita" id="quantita" ></td></tr>
<!-- <tr><td>Data bolla</td><td><input type="text" name="data" id="data" /></td></tr> -->
<tr><td>Numero bolla</td><td><input type="text" name="numero" id="numero" ></td></tr>
</table>
<input type="submit" id="btnok" value="Ok" >
</form>
<?php
print ("<br><a href=\"menu-for.php\"><img border=\"0\" src=\"b_home.gif\" alt=\"Menu principale\">Menu principale</a>\n");
footer();
?>