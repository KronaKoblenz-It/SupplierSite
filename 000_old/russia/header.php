<?php

/************************************************************************/
/* CASASOFT ArcaWeb                               		        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2011 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

// File da includere in tutte le pagine
include("common.php");

// Inizio pagina base
function head()  {
global $dbase, $dittatitle, $dittaragsoc;
header('Content-Type: text/html; charset=utf-8');
header('Content-language: ru');
echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\">\n";
echo "<html>\n<head>\n";
include("meta.php");
echo "<title>";
echo $dittatitle[$dbase];
echo "</title>\n";
echo "<link rel=\"stylesheet\" href=\"style.css\" type=\"text/css\">\n";
echo "</head>\n";  
echo "<body>\n";
}

// Prepara il banner da mostrare nella testa della pagina
function banner($title, $utente) {
global $dbase, $dittacolor;
echo "<table cellspacing=\"0\" cellpadding=\"0\" border=\"0\" width=\"100%\">\n";
echo "<tr>\n";
echo "<td width=\"75\" bgcolor=\"" . $dittacolor[$dbase] ."\">\n<img ";
echo "src=\"$dbase.jpg\" alt=\"Logo$dbase\" title=\"$title\">";
echo "</td>\n<td>";
echo "<center>\n<h1 class=\"title\">$title</h1>\n";
echo "<h3 class=\"name\">$utente</h3>\n</center>";
echo "</td>\n</tr>\n</table>\n";
}

// Prepara il footer da mettere in coda alle pagine
function footer() {
global $dbase, $dittaragsoc, $dittainfo;
echo "<hr size=\"1\">\n";
echo "<div class=\"footmsg\"><center>\n";
echo '&copy; 2003-' . current_year() . ' ' . $dittaragsoc[$dbase] . "<br>\n";
echo $dittainfo[$dbase];
echo "</center></div>\n";
echo "<a href=\"http://validator.w3.org/check?uri=referer\">\n";
echo "<img border=\"0\" src=\"http://validator.w3.org/images/valid_icons/valid-html40\" ";
echo "alt=\"Valid HTML 4.0 Transitional\" title=\"Valid HTML 4.0 Transitional\">\n";
echo "</a>\n";
echo "<a href=\"http://jigsaw.w3.org/css-validator/check/referer\">\n";
echo "<img style=\"border:0;width:88px;height:31px\" src=\"http://jigsaw.w3.org/css-validator/images/vcss-blue\" alt=\"CSS Valido!\">\n";
echo "</a>\n";

echo "</body>\n</html>\n";
}

// Formatta le date in modo che siano piu' leggibili
function format_date($foxdate) {
return strftime("%d/%m/%Y", mktime(0,0,0,substr($foxdate,5,2),substr($foxdate,8,2),substr($foxdate,0,4) )) ;
}

?>