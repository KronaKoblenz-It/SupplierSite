// ***********************************************************************
// Project ArcaWeb                               				        
// ===========================                                          
//                                                                      
// Copyright (c) 2003-2012 by Roberto Ceccarelli                        
//                                                                      
// **********************************************************************

   var httpXml = false;
   function makeHttpXml() {
      httpXml = false;
      if (window.XMLHttpRequest) { // Mozilla, Safari,...
         httpXml = new XMLHttpRequest();      
      } else if (window.ActiveXObject) { // IE
         try {
            httpXml = new ActiveXObject("Msxml2.XMLHTTP");
         } catch (e) {
            try {
               httpXml = new ActiveXObject("Microsoft.XMLHTTP");
            } catch (e) {}
         }
      }
      if (!httpXml) {
         alert('Cannot create XMLHTTP instance');
         return false;
	  }
	}
	
	function sendUrl(url) {
	makeHttpXml();
    httpXml.open("GET", url, true);
    httpXml.send(null);
	}
	
	function sendpick()  {
	var idriga = document.getElementById("idriga").value;
	var quantita = document.getElementById("quantita").value;
	var url = "writepick.php?idriga=" + idriga + "&quantita=" + quantita;
	document.getElementById("check").checked = true;
	// alert('Dato inviato');
	sendUrl(url); }

	
	function sendpickCheck()  {
	var chkcodice = document.getElementById("chkcodice").value;
	var reqcodice = document.getElementById("reqcodice").value;
	if (chkcodice == reqcodice) {
	  var idriga = document.getElementById("idriga").value;
	  var quantita = document.getElementById("quantita").value;
	  var url = "writepick.php?idriga=" + idriga + "&quantita=" + quantita;
	  document.getElementById("check").checked = true;
	  sendUrl(url); 
	  // se  see ci sono altre righe ordine passo automaticamente alla successiva
	  var idnextriga = document.getElementById("idnextriga").value;
	  if (idnextriga != "0") {
	    window.location = idnextriga; }
	  else {
        alert("Non ci sono altre righe."); } 	  
	  }	
	else {
	  alert("Codice non corretto!");  }
	}  
	  	
	function senduser(id_testa)  {
	var user = document.getElementById("user"+id_testa).value;
	var url = "writeuser.php?id=" + id_testa + "&user=" + user;
	sendUrl(url); }
 
	function checkCodiceArti(cCodice, cCF)  {
	if (cCodice.substring(0,3) != "292" && cCodice.substring(0,3) != "293") {
	  var url = "getcodicearti.php?cod=" + encodeURIComponent(cCodice);
	  if (cCF != "") {
	    url = url + "&cf=" + cCF;
	  }
	  makeHttpXml();
      httpXml.open("GET", url, false);
      httpXml.send(null);
	  var cRet = httpXml.responseText;
	  if ("*error*" == cRet)  {
	    alert("Codice non riconosciuto");
	    cRet = "";
	  }
	  return cRet;
	} else {
	  return cCodice;
	}
	}
	

	
