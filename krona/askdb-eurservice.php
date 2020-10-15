<?php 
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2019 by Roberto Ceccarelli                        */
/* http://strawberryfield.altervista.org								*/
/* 																		*/
/************************************************************************/

include("header.php"); 
include("db-utils.php");
head();
//$gruppoFiltro = "B0631";
$gruppoFiltro = $_GET["gruppo"];
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
    setLotto();
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
    if(descliven.trim() == ""){
        descliven = "KRONA";
    }
    document.getElementById('cliente').value = descliven;
    document.getElementById('codcli').value = cliven;

};

var setLotto = function(){
    var idField = document.getElementById("rif");
    var id = idField.options[idField.selectedIndex].value;
    var url = "getlottoeurservice.php?id=" + id;
    makeHttpXml();
    httpXml.open("GET", url, false);
    httpXml.send(null);
    var xRet = httpXml.responseXML;
    var lotto = xRet.getElementsByTagName("lotto")[0].firstChild.nodeValue;
	// var m = new Date();
	// var lotto = 'ES'+  m.getUTCFullYear() + 
    // ("0" + (m.getUTCMonth()+1)).slice(-2) + 
    // ("0" + m.getUTCDate()).slice(-2) + 
    // ("0" + m.getUTCHours()).slice(-2) + 
    // ("0" + m.getUTCMinutes()).slice(-2) + 
    // ("0" + m.getUTCSeconds()).slice(-2);
    document.getElementById('lotto').value = lotto;
 
};

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
// ]]>
</script>

<?php
$connectionstring = db_connect($dbase);

$descMacchina = ""; 
if($gruppoFiltro == "MAT.AZ.0352" )  {$descMacchina = "Lancio produzione MACCHINA X ASSEMB BOCCOLE SU BIELLE 'S0068' FASE 1  - K6360";} 
if($gruppoFiltro == "MAT.AZ.0353" )  {$descMacchina = "Lancio produzione MACCHINA X ASSEMB. CERNIERA 'S0068' FASE 2-4-5-6-7 - K6360";}
if($gruppoFiltro == "MAT.AZ.0354" )  {$descMacchina = "Lancio produzione MACCHINA X AVVITATURA CERN. 'S0068' FASE 9-10-11- K6360";}
if($gruppoFiltro == "MAT.AZ.0363" )  {$descMacchina = "Lancio produzione MACCHINA X ASSEMB BOCCOLE SU BIELLE 'S0076' FASE 1- K2760";}
if($gruppoFiltro == "MAT.AZ.0364" )  {$descMacchina = "Lancio produzione MACCHINA X ASSEMB. CERNIERA 'S0076' FASE 2-4-5-6-7 – K2760";}
if($gruppoFiltro == "MAT.AZ.0365" )  {$descMacchina = "Lancio produzione MACCHINA X AVVITATURA CERN. 'S0076' FASE 9-10-11 –K2760";}
if($gruppoFiltro == "UT25024A00300") {$descMacchina = "Lancio produzione ATT. ASSEMBLAGGIO. PERNO/CARCASSA 'S0068' FASE 8 K6360/K2760/K2460/ABSU";}
if($gruppoFiltro == "MAT.AZ.0415" )  {$descMacchina = "Lancio produzione MACCHINA ASSEMBLAGGIO";}

$listaArticoli = ""; 
if($gruppoFiltro == "MAT.AZ.0352" )  {$listaArticoli = "'GFS0068A00400'";}
if($gruppoFiltro == "MAT.AZ.0353" )  {$listaArticoli = "'GFS0068A00700'";}
if($gruppoFiltro == "MAT.AZ.0354" )  {$listaArticoli = "'GFS0068A00100','GFS0068A00200'";}
if($gruppoFiltro == "MAT.AZ.0363" )  {$listaArticoli = "'GFS0076A00100'";}
if($gruppoFiltro == "MAT.AZ.0364" )  {$listaArticoli = "'GFS0076A00200'";}
if($gruppoFiltro == "MAT.AZ.0365" )  {$listaArticoli = "'GFS0076A00500'";}
if($gruppoFiltro == "UT25024A00300") {$listaArticoli = "'GFS0068A00800','GFS0068A00900','GFS0076A00400','GFS0066A00100','GFS0501A028GG','GFS0501A030GG','GFS0501A03100','GFS0501A01900','GFS0501A03600','GFS0501A06500','GFS0501A06600','GFS0501A06700','GFS0501A06800','GFS0501A08000'";}
if($gruppoFiltro == "MAT.AZ.0415" )  {$listaArticoli = "'GFS0501A05700','GFS0501A05800','GFS0501A05900','GFS0501A04000','GFS0501A04100','GFS0501A04200','GFS0501A07400'";}



session_start();
$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
banner($descMacchina,$cookie[1]);
$codcf = $cookie[0];

$text = <<<EOT
<form action="esplodieurservice.php" method="get" >
<table>
<tr><td>Articolo</td>
<td><select name="articolo" id="articolo" 
onchange="listaRif('$codcf', this.options[this.selectedIndex].value);" >
<option value="">&nbsp;</option>
EOT;
print("$text\n");

$Query = <<<EOT
SELECT DOCRIG.CODICEARTI, MAX(DOCRIG.DESCRIZION) 
FROM DOCRIG INNER JOIN DOCTES ON DOCTES.ID = DOCRIG.ID_TESTA
INNER JOIN MAGART ON MAGART.CODICE = DOCRIG.CODICEARTI
WHERE DOCTES.TIPODOC IN ('FO', 'LO', 'OF', 'OL', 'OW', 'WO')
AND DOCRIG.QUANTITARE > 0 AND DOCTES.CODICECF = '$codcf' 
AND DOCRIG.CODICEARTI IN ($listaArticoli)
GROUP BY DOCRIG.CODICEARTI
ORDER BY DOCRIG.CODICEARTI
EOT;

$queryexe = db_query($connectionstring, $Query) or die(mysql_error()); 
while($row = db_fetch_row($queryexe)) {
	print("<option value=\"" . $row[0] . "\">" . $row[0] . " - " . $row[1] . "</option>\n");
}
$text = <<<EOT
</select>
<label id='labeldoppiocollo' style='color: red; font-style: italic; visibility: hidden'>     Attenzione!!!. L'articolo prevede 2 colli. Stampare l'etichetta corrispondente.</label>
</td></tr>

<tr><td>Riferimento</td>
<td><select name="rif" id="rif" onblur='setResiduo(); setCliente(); setLotto();' onchange='setResiduo(); setCliente(); setLotto();'>
<option value="">&nbsp;</option>
</select></td></tr>
<tr><td>Cliente</td><td><input type='text' name='cliente' id='cliente' readonly></td></tr>

<tr><td>Lotto</td><td><input type="text" name="lotto" id="lotto" onblur="checkBarcode39(this);">
<tr><td></td><td>
&nbsp;
</td></tr>
</td></tr>
<tr><td>Quantit&agrave</td><td><input type="text" name="quantita" id="quantita" onblur='checkQuantita()'>
<label id='labelquantita' style='color: red; font-style: italic; visibility: hidden'>     
Attenzione!!!. Le quantit&agrave inserite superano del 10% il residuo dell'ordine selezionato. Correggere le quantit&agrave o selezionare un altro ordine.</label></td></tr>
<tr><td>Residuo</td><td><input type="text" name="residuo" id="residuo" readonly></td></tr>

<tr><td>Copia lotti da</td>
<td><select name="copy" id="copy">
<option value="">&nbsp;</option>
</select></td></tr>

</table>
<input type='text' name='codcli' id='codcli' readonly hidden='hidden'>
<input type='hidden' name='gruppo' id='gruppo' value='$gruppoFiltro'>
<input type="submit" id="btnok" value="Ok">
</form>

<br>
EOT;
print("$text\n");

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