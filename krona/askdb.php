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
include("db-utils.php");
head();
?>

<script type="text/javascript" src="../js/checkbarcode.js"></script>

<script type="text/javascript" src="../js/ajaxlib.js"></script>
<script type="text/javascript" src="../js/select_lib.js"></script>
<script type="text/javascript">
// <![CDATA[
var checkdoppiocollo = function(cCodArt){
    //Eseguo il controllo per verificare se l'articolo selezionato ha il collo doppio
    var url = "getdoppiocollox.php?codart=" + encodeURIComponent(cCodArt);
    makeHttpXml();
    httpXml.open("GET", url, false);
    httpXml.send(null);
    var xRet = httpXml.responseXML;
    var oList = xRet.getElementsByTagName("doppiocollo");
    var cCodice;
    var cDescrizione;
    var cArtCollo;
    var cDesColloExtra;
    var nColli;
    var cCodCollo;
    var cDesCollo;

    for(var j=0; j<oList.length; j++){
        if(oList[j].getElementsByTagName("codice")[0].firstChild == null){cCodice = "";}
        else {cCodice = oList[j].getElementsByTagName("codice")[0].firstChild.nodeValue;}

        if(oList[j].getElementsByTagName("descrizione")[0].firstChild == null){cDescrizione = "";}
        else{cDescrizione = oList[j].getElementsByTagName("descrizione")[0].firstChild.nodeValue;}

        if(oList[j].getElementsByTagName("artcollo")[0].firstChild == null){cArtCollo = "";}
        else{cArtCollo = oList[j].getElementsByTagName("artcollo")[0].firstChild.nodeValue;}

        if(oList[j].getElementsByTagName("descolloextra")[0].firstChild == null){cDesColloExtra = "";}
        else{cDesColloExtra = oList[j].getElementsByTagName("descolloextra")[0].firstChild.nodeValue;}

        if(oList[j].getElementsByTagName("ncolli")[0].firstChild == null) {nColli = 1;}
        else{nColli = oList[j].getElementsByTagName("ncolli")[0].firstChild.nodeValue;}

        if(oList[j].getElementsByTagName("codcollo")[0].firstChild == null) {cCodCollo = "";}
        else{cCodCollo = oList[j].getElementsByTagName("codcollo")[0].firstChild.nodeValue;}

        if(oList[j].getElementsByTagName("descollo")[0].firstChild == null) {cDesCollo = "";}
        else{cDesCollo = oList[j].getElementsByTagName("descollo")[0].firstChild.nodeValue;}
    }
    if(nColli > 1){
        alert("L'articolo prevede 2 colli. \nStampare anche la seconda etichetta!!!");
        document.getElementById("labeldoppiocollo").style.visibility = "visible";
        document.getElementById("etich2collo").style.visibility = "visible";
    }
    else{
        document.getElementById("labeldoppiocollo").style.visibility = "hidden";
        document.getElementById("etich2collo").style.visibility = "hidden";
    }
}

var listaRif = function(cCodCF, cCodArt)  {
    document.getElementById("rif").innerHTML = "";
	var url = "getopenordx.php?codcf=" + encodeURIComponent(cCodCF);
	url = url + "&codart="+encodeURIComponent(cCodArt);
	makeHttpXml();
	httpXml.open("GET", url, false);
	httpXml.send(null);
	var xRet = httpXml.responseXML;
	var oList = xRet.getElementsByTagName("ordine");
	var oDoc, rif, str;
	for( var j=0; j<oList.length; j++) {
	  oDoc = oList[j];
	  rif = oDoc.getElementsByTagName("id")[0].firstChild.nodeValue;
	  str = oDoc.getElementsByTagName("tipodoc")[0].firstChild.nodeValue;
	  str = str + " " + oDoc.getElementsByTagName("numerodoc")[0].firstChild.nodeValue;
	  str = str + " del " + oDoc.getElementsByTagName("datadoc")[0].firstChild.nodeValue;
	  str = str + " consegna " + oDoc.getElementsByTagName("dataconseg")[0].firstChild.nodeValue;
      str = str + " residuo " + oDoc.getElementsByTagName("quantitare")[0].firstChild.nodeValue;
      appendOptionLast2("rif", str, rif);
	}
	// ripetiamo il giro per vedere se ci sono bolle da cui copiare i lotti
    document.getElementById("copy").innerHTML = "";
	appendOptionLast2("copy", "", "0");
	var url = "getopenddtx.php?codcf=" + encodeURIComponent(cCodCF);
	url = url + "&codart="+encodeURIComponent(cCodArt);
	makeHttpXml();
	httpXml.open("GET", url, false);
	httpXml.send(null);
	var xRet = httpXml.responseXML;
    var oList = xRet.getElementsByTagName("ordine");
	var oDoc, rif, str;
	for( var j=0; j<oList.length; j++) {
	  // ROBERTO - 06.11.2018
	  // catturiamo eventuali errori
	  try {	
	  oDoc = oList[j];
	  rif = oDoc.getElementsByTagName("id_testa")[0].firstChild.nodeValue;
	  str = "Bolla "+oDoc.getElementsByTagName("numerodocf")[0].firstChild.nodeValue;
	  str = str + " - Lotto " + oDoc.getElementsByTagName("lotto")[0].firstChild.nodeValue;
      appendOptionLast2("copy", str, rif);
	  }
	  catch(err) { }
	}

    //Inserisco il residuo nella casella
    setResiduo();
    setCliente();
};

var writeEtich = function(link) {
	var idField = document.getElementById("rif");
	var id = idField.options[idField.selectedIndex].value;
	var url = "getaltcodex.php?id=" + id;
	makeHttpXml();
	httpXml.open("GET", url, false);
	httpXml.send(null);
	var xRet = httpXml.responseXML;
	var articolo = xRet.getElementsByTagName("codice")[0].firstChild.nodeValue;
	var desc = xRet.getElementsByTagName("descrizion")[0].firstChild.nodeValue;
	try {
		var barcode = xRet.getElementsByTagName("barcode")[0].firstChild.nodeValue;
	} catch(e) {
		var barcode = "";
	}
    try {
        var cliven = xRet.getElementsByTagName("cliven")[0].firstChild.nodeValue;
    } catch(e) {
        var cliven = "";
    }

	var lotto = document.getElementById("lotto").value;
	window.open(link+"?art="+encodeURIComponent(articolo)+"&lotto="+encodeURIComponent(lotto)+"&desc="+encodeURIComponent(desc)+"&code="+encodeURIComponent(barcode)+"&cliven="+encodeURIComponent(cliven) );
};

var setCliente = function(){
    var idField = document.getElementById("rif");
    var id = idField.options[idField.selectedIndex].value;
    var url = "getaltcodex.php?id=" + id;
    makeHttpXml();
    httpXml.open("GET", url, false);
    httpXml.send(null);
    var xRet = httpXml.responseXML;
    var articolo = xRet.getElementsByTagName("codice")[0].firstChild.nodeValue;
    var desc = xRet.getElementsByTagName("descrizion")[0].firstChild.nodeValue;
    try {
        var barcode = xRet.getElementsByTagName("barcode")[0].firstChild.nodeValue;
    } catch(e) {
        var barcode = "";
    }
    try {
        var cliven = xRet.getElementsByTagName("cliven")[0].firstChild.nodeValue;
    } catch(e) {
        var cliven = "C";
    }
    try {
        var descliven = xRet.getElementsByTagName("descliven")[0].firstChild.nodeValue;
    } catch(e) {
        var descliven = "KRONA";
    }
    var lotto = document.getElementById("lotto").value;
    if(descliven.trim() == ""){
        descliven = "KRONA";
    }
    document.getElementById('cliente').value = descliven;
    document.getElementById('codcli').value = cliven;
    if (cliven=='C02068'){
        document.getElementById('etichporta').style.visibility = "visible"
    }
    else {
        document.getElementById('etichporta').style.visibility = "hidden";
    }
}
// ]]>

var setResiduo = function(){
    var residuo = document.getElementById('rif')[document.getElementById('rif').selectedIndex].innerHTML;
    var pos = residuo.indexOf("residuo")+7;
    residuo = residuo.substring(pos).trim();
    document.getElementById('residuo').value = residuo;
};

var checkQuantita = function(){
    var quantita = parseFloat(document.getElementById('quantita').value);
    var residuo = parseFloat(document.getElementById('residuo').value);
    var diff = quantita - (residuo + residuo*10/100);
    if(diff < 0){
        document.getElementById('labelquantita').style.visibility = "hidden";
        document.getElementById('btnok').style.visibility = "visible";
    }
    else{
        document.getElementById('labelquantita').style.visibility = "visible";
        document.getElementById('btnok').style.visibility = "hidden"
    }
};
</script>

<?php
$connectionstring = db_connect($dbase);

session_start();
$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
banner("Reso conto lavoro da ",$cookie[1]);
$codcf = $cookie[0];

//print("F" . substr($cookie[0],2));
print("<form action=\"esplodi.php\" method=\"get\" >\n");
print("<table>\n");
print("<tr><td>Articolo</td>\n");
print("<td><select name=\"articolo\" id=\"articolo\" ");
print("onchange=\"listaRif('$codcf', this.options[this.selectedIndex].value); checkdoppiocollo(this.options[this.selectedIndex].value);\" >\n");
print("<option value=\"\">&nbsp;</option>\n");

$Query = "SELECT DOCRIG.CODICEARTI, MAX(DOCRIG.DESCRIZION) ";
$Query .= " FROM DOCRIG INNER JOIN DOCTES ON DOCTES.ID = DOCRIG.ID_TESTA";
$Query .= " WHERE (DOCTES.TIPODOC=\"FO\" OR DOCTES.TIPODOC=\"LO\" OR DOCTES.TIPODOC=\"OF\" OR DOCTES.TIPODOC=\"OL\")";
$Query .= " AND DOCRIG.QUANTITARE > 0 AND DOCTES.CODICECF = \"$codcf\" AND DOCRIG.CODICEARTI != \"\"";
$Query .= " GROUP BY DOCRIG.CODICEARTI";
$Query .= " ORDER BY DOCRIG.CODICEARTI";

$queryexe = db_query($connectionstring, $Query) or die(mysql_error()); 
while($row = db_fetch_row($queryexe)) {
	print("<option value=\"" . $row[0] . "\">" . $row[0] . " - " . $row[1] . "</option>\n");
}
print("</select>");
print("<label id='labeldoppiocollo' style='color: red; font-style: italic; visibility: hidden'>     Attenzione!!!. L'articolo prevede 2 colli. Stampare l'etichetta corrispondente.</label>");
print("</td></tr>\n");

print("<tr><td>Riferimento</td>\n");
print("<td><select name=\"rif\" id=\"rif\" onblur='setResiduo(); setCliente();' onchange='setResiduo(); setCliente();'>\n");
print("<option value=\"\">&nbsp;</option>\n");
print("</select></td></tr>\n");
print("<tr><td>Cliente</td><td><input type='text' name='cliente' id='cliente' readonly></td></tr>");

print("<tr><td>Lotto</td><td><input type=\"text\" name=\"lotto\" id=\"lotto\" onblur=\"checkBarcode39(this);\" >\n");
print("<tr><td></td><td>");
writeEtich("etich1lotti_unificate_PZ.php", "Etichetta 88x36 PZ", "", false);
writeEtich("etich1lotti_unificate_SC.php", "Etichetta 88x36 SC", "", false);
writeEtich("etich1lotti_unificate.php", "Etichetta 88x36 CF", "", false);
//writeEtich("etich1lotti.php", "Etichetta 88x36");
writeEtich("eticha4lotti.php", "Etichette su A4", "", false);
writeEtich("etich1lotti_unificate_collo2.php", "Etichetta 88x36 PZ - Collo Supplementare", "etich2collo", true);
writeEtich("etich-porta.php", "Etichetta Porta 88x36" , "etichporta", true);
writeEtich("etich1_weight.php", "Etichetta Attenzione peso 73x110", "", false);
print("</td></tr>");
print("</td></tr>\n");
print("<tr><td>Quantit&agrave</td><td><input type=\"text\" name=\"quantita\" id=\"quantita\" onblur='checkQuantita()'><label id='labelquantita' style='color: red; font-style: italic; visibility: hidden'>     Attenzione!!!. Le quantit&agrave inserite superano del 10% il residuo dell'ordine selezionato. Correggere le quantit&agrave o selezionare un altro ordine.</label></td></tr>\n");
print("<tr><td>Residuo</td><td><input type=\"text\" name=\"residuo\" id=\"residuo\" readonly></td></tr>\n");
print("<tr><td>Numero bolla</td><td><input type=\"text\" name=\"numero\" id=\"numero\" ></td></tr>\n");

print("<tr><td>Copia lotti da</td>\n");
print("<td><select name=\"copy\" id=\"copy\" >\n");
print("<option value=\"\">&nbsp;</option>\n");
print("</select></td></tr>\n");

print("</table>\n");
print("<input type='text' name='codcli' id='codcli' readonly hidden='hidden'>");
//print("PROBLEMI TECNICI! TORNEREMO OPERATIVI A BREVE");
print("<input type=\"submit\" id=\"btnok\" value=\"Ok\">\n");
print("</form>\n");

print ("<br>\n");
goMain();
footer();

function writeEtich($link, $desc, $id, $hidden) {
    if($hidden==false){
	    print("&nbsp;<a href=\"javascript:writeEtich('$link');\" title=\"$desc\" id='$id'>");
    }
    else{
        print("&nbsp;<a href=\"javascript:writeEtich('$link');\" title=\"$desc\" id='$id' style='visibility: hidden'>");
    }
	print("<img style=\"border: none;\" src=\"../img/printer.png\" alt=\"$desc\">$desc</a>\n");
}
?>