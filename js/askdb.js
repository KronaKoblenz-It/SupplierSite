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
	var url = "getlistepx.php?codcf=" + encodeURIComponent(cCodCF);
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
	  str = "Lista del "+oDoc.getElementsByTagName("data")[0].firstChild.nodeValue;
	  str = str + " - Lotto " + oDoc.getElementsByTagName("lotto")[0].firstChild.nodeValue;
      appendOptionLast2("copy", str, rif);
	  }
	  catch(err) { }
	}

    //Inserisco il residuo nella casella
    setResiduo();
    setCliente();
};

var writeEtich = function(link, mode) {
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
	window.open(link+"?art="+encodeURIComponent(articolo)+"&lotto="+encodeURIComponent(lotto)+"&desc="+encodeURIComponent(desc)+"&code="+encodeURIComponent(barcode)+"&cliven="+encodeURIComponent(cliven)+"&mode="+mode );
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

var setLottoPadre = function(s) {
	var lotto = document.getElementById("lotto");
	if( lotto.value == "") {
		var p = s.indexOf("Lotto");
		var codlotto = s.substring(p+5).trim();
		lotto.value = codlotto;
	}
}