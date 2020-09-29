<?php 
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2013 by Roberto Ceccarelli                        */
/* http://strawberryfield.altervista.org								*/
/* 																		*/
/************************************************************************/

include("header.php"); 
head();

session_start();
$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
$maga = "F" . substr($cookie[0],2);

banner("Caricamento inventario da Excel",$cookie[1]);

?>
<script type="text/javascript">
//<![CDATA[
var downloadURL = function downloadURL(url) {
    var iframe;
    var hiddenIFrameID = 'hiddenDownloader';
    iframe = document.getElementById(hiddenIFrameID);
    if (iframe === null) {
        iframe = document.createElement('iframe');  
        iframe.id = hiddenIFrameID;
        iframe.style.display = 'none';
        document.body.appendChild(iframe);
    }
    iframe.src = url;   
};

var getxml = function() {
	var maga = document.getElementById("maga").value;
//	downloadURL("xmlinv_base.php?maga="+maga);
	downloadURL("xlsinv_base.php?maga="+maga);
};
//]]>
</script>
<?php
print("<br>\n");
print("<input type=\"hidden\" value=\"$maga\" id=\"maga\">\n");
print("<input type=\"button\" value=\"Scarica il foglio da compilare\" onclick=\"getxml();\">\n");
print("</br></br>\n");
print("<div id=\"warning\"\">\n");
print("<b>Si prega di consultare la descrizione della procedura inventariale</b> (<a href=\"http://intranet.krona.it/krona/manuali/Inventario_di_magazzino_%20rel_0612_del_06_12_12.pdf\" target=”_blank” title=”ManualeInventario”>clicca qui</a>)</br>\n");
print("<i>In caso di necessità contattare l'Ufficio Ced. (<a href=\"./mailto.php\" target=”_blank” title=”CompilaMail”>ced@k-group.com</a>)</i>\n");
print("</div>\n");
print("<br><br>\n");

//print("<form action=\"xml2inv.php\" method=\"post\" enctype=\"multipart/form-data\">\n");
print("<form action=\"xls2inv.php\" method=\"post\" enctype=\"multipart/form-data\">\n");
print("<label for=\"file\">Filename:</label>\n");
//print("<input type=\"file\" name=\"file\" id=\"file\" accept=\"text/xml\">\n"); 
print("<input type=\"file\" name=\"file\" id=\"file\">\n"); 
print("&nbsp;<input type=\"submit\" id=\"btnok\" value=\"Carica il foglio compilato\" >\n");
print("</form>\n");

print("<br>\n");
goMain();
footer();
?>