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

//Avviso Temporaneo
if(in_array($cookie[0], array("F01021", "F00019", "F00051", "F00103", "F00255", "F00269", "F00276", "F00289", "F00331", "F00393", "F00496", "F00497", "F00499", "F00508", "F00715", "F00754", "F00833", "F00838", "F00866", "F00961", "F00963", "F01111", "F01280", "F01328", "F01338", "F01396", "F01420", "F01428", "F01487", "F01514", "F01538", "F01540", "F01559", "F01571", "F01584", "F01585", "F01606", "F01616", "F01618", "F01630", "F01726", "F01810", "F02015", "F02077", "F02253", "F02386", "F02513", "F02522", "F11196"))) {
	print("<div style=\"float: middle\" id=\"avviso\">\n");
	print("<fieldset style=\"width: 70%; float: center\"><legend><h3> AVVISO IMPORTANTE</h3></legend>\n");
	print("<p>Attenzione per problemi tecnici è stato evidenziato un problema in fase di caricamento del file di Inventario. 
	Pertanto preghiamo di inviare, per motivi precauzionali, tramite email il file del vs. inventario compilato all'indirizzo 
	<u><i>ced@k-group.com</i></u> firmando il messagio. <br>Rigranziamo per la disponibilità e ci scusiamo per il disagio. <br>
	Cogliamo l'occasione per rinnovare i nostri Auguri di buone Feste. </p></fieldset>\n");
	print("</div>\n");
}

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