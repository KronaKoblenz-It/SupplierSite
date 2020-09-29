<?php
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2020 by Roberto Ceccarelli                        */
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
			var cLinea = cGruppo;
			var url="http://172.18.0.102/denken/writeordinelotto.php?codice="+encodeURIComponent(cCodice);
			url = url + "&lotto=" + encodeURIComponent(cLotto) + "&qta=" + nQta+"&linea="+cLinea;
			alert(url);
			sendUrl(url);
			document.db.action = "creadoceurservice.php";
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
goEdit("askdb-eurservice.php?gruppo=$gruppo",_("Nuovo lancio di produzione"));
print("<br>\n");
goMain();
footer();

?>
