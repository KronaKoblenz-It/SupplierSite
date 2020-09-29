function listaLottix(cCodice, cMaga)  {
	var url = "getlottix.php?cod=" + encodeURIComponent(cCodice);
	url = url + "&mag="+cMaga;
	makeHttpXml();
	httpXml.open("GET", url, false);
	httpXml.send(null);
	var xRet = httpXml.responseXML;
    var oList = xRet.getElementsByTagName("codice");
	for( var j=0; j<oList.length; j++) {
      appendOptionLast("lotto", oList[j].firstChild.nodeValue);
	}
}
