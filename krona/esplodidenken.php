<?php
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2019 by Roberto Ceccarelli                        */
/* http://strawberryfield.altervista.org								*/
/*                                                                      */
/************************************************************************/

function formButtons() {
	print("<input type=\"submit\" id=\"btnok\" value=\"" . _("Inserisci lancio") . "\" onclick=\"document.pressed=this.value;\">\n");
}

include("esplodicommon.php");

?>
<script type="text/javascript">
// <![CDATA[
var OnSubmitForm = function() {	
	if(checkRows()){
		if(document.pressed == 'Inserisci lancio') {
			var cCodice = document.getElementById("padre").value;
			var cLotto = document.getElementById("lottopadre").value;
			var nQta = document.getElementById("quantita").value;
			var cGruppo = document.getElementById("gruppo").value;
			var cLinea = "317100020";
			if(cGruppo == "B0631") {
				// Karacter
				cLinea = "317100250";
			}
			if(cGruppo == "B0630") {
				// Slim
				cLinea = "317100020";
			}
			var url="http://172.19.0.102/denken/writeordinelotto.php?codice="+encodeURIComponent(cCodice);
			url = url + "&lotto=" + encodeURIComponent(cLotto) + "&qta=" + nQta+"&linea="+cLinea;
			sendUrl(url);
			document.db.action = "creadocdenken.php";
		}
		return true;
	} else {
		alert("Impossibile Procedere!\nCorreggere prima i Lotti con Giacenza non corretta!");
		return false;
	}
};
// ]]>
</script>
<?php
goEdit("askdb-denken.php",_("Nuovo lancio di produzione"));
print("<br>\n");
goMain();
footer();

?>
