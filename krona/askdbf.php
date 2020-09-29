<?php 
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2019 by Roberto Ceccarelli                        */
/* 																		*/
/************************************************************************/

include("header.php");
include("db-utils.php");
$connectionstring = db_connect($dbase);
head();
?>
<style>
label	{float: left; width: 150px;}
body	{width: 350px;}
</style>
<script type="text/javascript">
//<![CDATA[
var decode = function(obj) {
  $.get("getcodicearti.php", {cod : obj.value}, null, "text")
	.done(function(data) {
		if(data == "*error*") {
			alert("Articolo non trovato!");
			data = "";
		}
		obj.value = data;
		var tbox = document.getElementById('articolo');
		tbox.value = obj.value;
	} );
};

var decodecli = function(obj) {
  $.get("getCodForx.php", {cod : obj.value, cf : "F"}, null, "xml")
	.done(function(data) {
		var codice = data.getElementsByTagName("codice")[0].firstChild.nodeValue;
		var descrizione = "";
		if(codice == "*error*") {
			alert("Fornitore non trovato!");
		}
		else {
			descrizione = data.getElementsByTagName("ragsoc")[0].firstChild.nodeValue;
		}
		desccli.value = descrizione;			
	} );	
};
//]]>
</script>
<label for="code">Articolo / Ordine:</label> 
<input type="text" name="code" id="code" onblur="decode(this);"/>
<?php
setFocus("code");
?>

<form name="input" action="esplodi.php" method="get" >
<input type="hidden" name="mode" id="mode" value="CE" />
<input type="hidden" name="articolo" id="articolo" />
<label for="qta">Quantita:</label>
<input type="input" name="qta" id="qta" /><br>
<label for="codcli">Cliente:</label>
<input type="input" name="codcli" id="codcli" onblur="decodecli(this)" /><br>
<input type="readonly" id="desccli" size="50" /><br>
<?php
print("<input type=\"submit\" id=\"btnok\" value=\"Ok\" />\n");
print("</form>\n");

goMain();
footer_pistole();
?>