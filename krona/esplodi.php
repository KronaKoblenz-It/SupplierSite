<?php
  /************************************************************************/
  /* Project ArcaWeb                               				          */
  /* ===========================                                          */
  /*                                                                      */
  /* Copyright (c) 2003-2020 by Roberto Ceccarelli                        */
  /* http://strawberryfield.altervista.org								  */
  /*                                                                      */
  /************************************************************************/

function formButtons() {
	global $mode;
	if($mode == 'P') {
		print("<input type=\"submit\" id=\"btnok\" value=\"" . _("Inserisci lista") . "\" onclick=\"document.pressed=this.value;\">\n");		
	}
	else {
		print("<input type=\"submit\" id=\"btnok\" value=\"" . _("Inserisci bolla") . "\" onclick=\"document.pressed=this.value;\">\n");
	}
  	print("<input type=\"submit\" id=\"btnxml\" value=\"" . _("Estrai dati") . "\" onclick=\"document.pressed=this.value;\">\n");
}

include("esplodicommon.php");

?>
<script type="text/javascript">
// <![CDATA[
var OnSubmitForm = function() {
if(checkRows()){
	if(document.pressed == 'Inserisci bolla') {
		document.db.action = "creadoc.php";
	} else {
		if(document.pressed == 'Estrai dati') {
			document.db.action = "creadocxml.php";
		} else {
			if(document.pressed == 'Inserisci lista') {
				document.db.action = "crealistap.php";
			}
		}
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
if($mode == 'P') {
	goEdit("askdb_bc.php",_("Nuova lista prelievo"));
}
else  if($mode == 'L') {
	goEdit("askdb_lp.php",_("Nuova bolla"));
}
else {
	goEdit("askdb.php",_("Nuova bolla"));
}
print("<br>\n");
goMain();
footer();

?>
