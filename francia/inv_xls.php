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
session_start();
$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
$maga = "F" . substr($cookie[0],2);
head();

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
print("<input type=\"hidden\" value=\"$maga\" id=\"maga\">\n");
print("<input type=\"button\" value=\"Scarica il foglio da compilare\" onclick=\"getxml();\">\n");
print("<br><br><br>\n");

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