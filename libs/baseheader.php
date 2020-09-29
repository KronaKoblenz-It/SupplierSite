<?php
/************************************************************************/
/* CASASOFT ArcaWeb                               		        		*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2017 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

// Inizio pagina base
function head_base($inc)  {
	global $dbase, $dittatitle, $dittaragsoc, $lang;
	echo "<!DOCTYPE HTML PUBLIC \"-//W3C//DTD HTML 4.0 Transitional//EN\">\n";
	//print("<!DOCTYPE html>");
	echo "<html lang=\"$lang\">\n<head>\n";
	include("../libs/meta.php");
	echo "<title>";
	echo $dittatitle[$dbase];
	echo "</title>\n";
	echo "<link rel=\"stylesheet\" href=\"../style.css\" type=\"text/css\">\n";
    echo "<link rel=\"stylesheet\" href=\"../bootstrap/css/bootstrap.css\" type=\"text/css\">\n";
    echo "<script type=\"text/javascript\" src=\"../js/jquery-1.12.4.min.js\"></script>";
    echo "<script type=\"text/javascript\" src=\"../bootstrap/js/bootstrap.js\"></script>";
    echo "$inc\n";
	echo "</head>\n";  
	echo "<body style=\"margin: 0px;\">\n";
}

// Inizio pagina con gestione sessioni
function head($inc) {
	session_start();
	if( !isset($_SESSION['CodiceAgente']) ) {
		header('Location: login.php');
	}
	head_base($inc);
}

// Prepara il banner da mostrare nella testa della pagina
function banner($title, $utente) {
	global $dbase, $dittacolor;
	//echo "<header>\n";
	//echo "<div class=\"header\">\n";
    echo "<div class='jumbotron text-center' style='height: 120px; padding-top: 10px'>";
	echo "<div style=\"float:left;\">\n";
	echo "<img src=\"$dbase.jpg\" alt=\"Logo$dbase\" title=\"$title\" height=\"75\">\n";
	echo "</div>\n";
//	echo "<div style=\"height:60px; margin-top:3px;\">\n";
//	echo "<h1 class=\"title\">$title</h1>\n";
//	echo "<h3 class=\"name\">$utente</h3>\n";
//	echo "</div>\n";
    echo "<h2>" . $title . "</h2>\n";
    echo "<p>$utente</p>\n";
	echo "</div>\n";
	//echo "</header>\n";
	echo "<div class=\"body\">\n";
}

function visualizza_novita($utente, $dbase){

    $connectionstring = db_connect($dbase);
    $query = "SELECT id, oggetto, messaggio, codicecf FROM U_BANNER " .
        "WHERE (codicecf ='$utente' or NULLIF(codicecf, ' ') IS NULL or codicecf = 'F')" .
        " and not exists (select id_banner from U_BANNERCH where U_BANNER.id = U_BANNERCH.id_banner and U_BANNERCH.codicecf = '$utente') ";
    $queryexe = db_query($connectionstring, $query) or die("$query<br>" . mysqli_error($connectionstring) );

    while($row = db_fetch_row($queryexe)){
        $i = $row[0];
        echo "<div class='alert alert-success' id='alert$i'>";
        echo "<strong>" . $row[1] . "</strong><p>" . $row[2] . "</p>";
        echo "<hr>";
        echo "<button type='button' class='btn btn-sm btn-info' onclick='$(\"#alert$i\").hide();'>Chiudi</button>";
        echo "<button type='button' class='btn btn-sm btn-danger' onclick='$(\"#alert$i\").hide();
                                                                            request = $.ajax({
                                                                                    url: \"setbanner.php?idbanner=$i&codicecf=$utente&dbase=$dbase\",
                                                                                    type: \"get\"
                                                                                }); 
                                                                            ' style='float: right'>Non visualizzare pi&ugrave</button>";
        echo "</div>";
    }

    db_close($connectionstring);
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

// Formatta le date in modo che siano piu' leggibili
function format_date_2($foxdate) {
	return "" == $foxdate ? "&nbsp;" : strftime("%Y/%m/%d", mktime(0,0,0,substr($foxdate,5,2),substr($foxdate,8,2),substr($foxdate,0,4) )) ;
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

function xRound2($n) {
	return round($n, 2);
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

function menuItem($link, $text) {
	print("<tr class=\"list\">\n");
	print("<th class=\"menu\"><a href=\"$link\">$text</a></th>\n");
	print("</tr>\n");
}

function goEdit($link, $text) {
	print("<a class=\"bottommenu\" href=\"$link\"  title=\"$text\">\n");
	print("<img style=\"border:none;\" src=\"../img/05_edit.gif\" alt=\"$text\">$text</a>\n");
}

// Campi hidden per passaggio parametri
function hiddenField($id, $val) {
	print("<input type=\"hidden\" id=\"$id\" name=\"$id\" value=\"$val\">\n");
}

// tabelle
function trList() {
	print("<tr class=\"list\">\n");
}

function thList($text, $align="") {
	print("<th class=\"list\"");
	if($align != "") {
		print(" style=\"text-align: $align;\"");
	}
	print(">$text</th>\n");
}

function tdList($text, $align="") {
	print("<td class=\"list\"");
	if($align != "") {
		print(" style=\"text-align: $align;\"");
	}
	print(">$text</td>\n");
}

// javascript
function jsStart() {
	print("<script type=\"text/javascript\">\n");
	print("//<![CDATA[\n");
}

function jsEnd() {
	print("//]]>\n");
	print("</script>\n");
}

function jsInclude($js) {
	print("<script type=\"text/javascript\" src=\"$js\"></script>\n");
}

// jQuery
$jQueryInc = <<<EOT
<link href="../css/datatables.min.css" rel="stylesheet" type="text/css">
<link href="../css/jquery-ui.min.css" rel="stylesheet" type="text/css" />
<link href="../css/jquery.dataTables.yadcf.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="../js/jquery-1.12.4.min.js"></script>
<script type="text/javascript" src="../js/datatables.min.js"></script>
<script type="text/javascript" src="../js/jquery.dataTables.yadcf.js"></script>
<script type="text/javascript" src="../js/jquery-ui.min.js"></script>
<!--<script type="text/javascript" src="../js/datepicker-$lang.js"></script>-->

EOT;

function dataTableInit($inc) {
	global $jQueryInc, $lang;
	return <<< EOT
$jQueryInc
<script type="text/javascript">
//<![CDATA[
$(document).ready(function(){
$inc
});
//]]>
</script>
EOT;
}
?>