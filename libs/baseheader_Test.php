<?php
/************************************************************************/
/* CASASOFT ArcaWeb                               		        		*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2013 by Roberto Ceccarelli                        */
/* http://strawberryfield.altervista.org								*/
/*                                                                      */
/************************************************************************/

// Inizio pagina base
function head_base()  {
	global $dbase, $dittatitle, $dittaragsoc, $lang;
	echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\">\n";
	//print("<!DOCTYPE html>");
	echo "<html lang=\"$lang\">\n<head>\n";
	include("../libs/meta.php");
	echo "<title>";
	echo $dittatitle[$dbase];
	echo "</title>\n";
	echo "<link rel=\"stylesheet\" href=\"../style_Test.css\" type=\"text/css\">\n";
	echo "</head>\n";  
	echo "<body style=\"margin: 0px;\">\n";
}

// Inizio pagina con gestione sessioni
function head() {
	session_start();
	if( !isset($_SESSION['CodiceAgente']) ) {
		header('Location: login.php');
	}
	head_base();
}

// Prepara il banner da mostrare nella testa della pagina
function banner($title, $utente) {
	global $dbase, $dittacolor;
	//echo "<header>\n";
	echo "<div class=\"header\">\n";
	echo "<div style=\"float:left;\">\n";
	echo "<img src=\"$dbase.jpg\" alt=\"Logo$dbase\" title=\"$title\">\n";
	echo "</div>\n";
	echo "<div style=\"height:120px; margin-top:30px;\">\n";
	echo "<h1 class=\"title\">$title</h1>\n";
	echo "<h3 class=\"name\">$utente</h3>\n";
	echo "</div>\n";
	echo "</div>\n";
	//echo "</header>\n";
	echo "<div class=\"body\">\n";
}

// Prepara il footer da mettere in coda alle pagine
function footer() {
	global $dbase, $dittaragsoc, $dittainfo, $dittatel, $dittafax, $dittamail;
	echo "</div>\n";
	//print("<footer>\n");
	echo "<div class=\"footer\">";
	echo "<hr style=\"height:1px;\">\n";
	echo "<div style=\"float:right;\">\n";
	echo "<br>\n";
	echo "<a href=\"http://validator.w3.org/check?uri=referer\" title=\"Valid HTML 4.0 Transitional\">\n";
	echo "<img style=\"border:0;width:88px;height:31px\" src=\"http://validator.w3.org/images/valid_icons/valid-html40\" ";
	echo "alt=\"Valid HTML 4.0 Transitional\" title=\"Valid HTML 4.0 Transitional\">\n";
	echo "</a>\n";
	echo "<a href=\"http://jigsaw.w3.org/css-validator/check/referer\" title=\"CSS Valido!\">\n";
	echo "<img style=\"border:0;width:88px;height:31px\" src=\"http://jigsaw.w3.org/css-validator/images/vcss-blue\" alt=\"CSS Valido!\">\n";
	echo "</a>\n";
	echo "<a href=\"http://www.anybrowser.org/campaign/\" onclick=\"window.open(this.href);return(false);\" title=\"Visualizzabile con ogni browser\">\n";
	echo "<img style=\"border:0;width:88px;height:31px\" src=\"../img/browserany.gif\" alt=\"Visualizzabile con ogni browser\">\n";
	echo "</a>\n";
	echo "<a href=\"http://microformats.org/\" onclick=\"window.open(this.href);return(false);\" title=\"Microformats enabled\">\n";
	echo "<img style=\"border:0;width:88px;height:31px\" src=\"../img/mfe_green.png\" alt=\"Microformats enabled\">\n";
	echo "</a>\n";
	echo "<br>\n</div>\n";
	echo "<div class=\"footmsg\" style=\"padding-right:270px;\">\n";
	echo "<div id=\"vcard\" class=\"vcard\">\n";
	echo "&copy; 2003-" . current_year() . " \n";
	echo "<a class=\"url org\" href=\"http://www.k-group.com\">" . $dittaragsoc[$dbase] . "</a>\n<br>\n";
	echo $dittainfo[$dbase];
	echo "<span class=\"tel\">\n";
	echo "<span class=\"type\">Tel.</span>\n";
	echo "<span class=\"value\">" . $dittatel[$dbase] . "</span>\n";
	echo "</span>&nbsp;\n";
	echo "<span class=\"tel\">\n";
	echo "<span class=\"type\">Fax</span>\n";
	echo "<span class=\"value\">" . $dittafax[$dbase] . "</span>\n";
	echo "</span>\n";
	echo "<br>\n<a class=\"email\" href=\"" . $dittamail[$dbase] . "\">" . $dittamail[$dbase] . "</a>\n";
	echo "</div>\n";
	echo "</div>\n";
	echo "</div>\n";
	//echo "</footer>\n";

	echo "</body>\n</html>\n";
}

// Formatta le date in modo che siano piu' leggibili
function format_date($foxdate) {
	return "" == $foxdate ? "&nbsp;" : strftime("%d/%m/%Y", mktime(0,0,0,substr($foxdate,5,2),substr($foxdate,8,2),substr($foxdate,0,4) )) ;
}
    
// Restituisce il tipo di utente:
// A = Agente
// C = Cliente
// F = Fornitore
function userType() {
  $cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
  return substr($cookie[0],0,1);
}

// formatta i numeri
$decimali = 4;
function number($n){
	global $decimali;
	$value = number_format($n, $decimali, ',', '.');
	return $value;
}

function xRound($n) {
	global $decimali;
	return round($n, $decimali);
}

// Richiamo del menu principale differenziato per tipo di utente
function goMain() {
	global $lang, $str_mainmenu;
	print("<a class=\"bottommenu\" href=\"");
	switch(userType()) {
	  case 'F': 
		print("menu-for.php");
		break;
	  case 'C': 
		print("menu-cli.php");
		break;
	  case 'A': 
		print("menu.php");
		break;
	  }
	print("\" title=\"" . $str_mainmenu[$lang] . "\">");
	print("<img style=\"border:none;\" src=\"../img/b_home.gif\" alt=\"" . $str_mainmenu[$lang] . "\">" . $str_mainmenu[$lang] . "</a>\n");
}

function goEdit($link, $text) {
	print("<a href=\"$link\"  title=\"$text\">\n");
	print("<img style=\"border:none;\" src=\"../img/05_edit.gif\" alt=\"$text\">$text</a>\n");
}
?>