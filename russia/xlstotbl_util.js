// ***********************************************************************
// Project ArcaWeb
// ===========================
//
// Copyright (c) 2003-2013 by Roberto Ceccarelli
//
// **********************************************************************

//CheckNumeri
var soloNumeri = function(id) {
    var valore=document.getElementById(id).value;
    valore=valore.replace (/[^\d]/g,'');
    document.getElementById(id).value=valore;
};

//Mostra Nascondi campi
var showHideText = function(bool,id) {
	var elm = document.getElementById(id);
	elm.style.display = bool ? "block" : "none";
};

function checkValuta (obj){
	var val = obj.value;
	if (val == "EUR") {
		document.getElementById('cambio').value = 1;
		document.getElementById('cambio').setAttribute('readonly', 'readonly');
		showHideText(false, 'hide');
	} else if (val == "RUB") {
		document.getElementById('cambio').value = 0;
		document.getElementById('cambio').removeAttribute('readonly');
		showHideText(true, 'hide');
	}
};

function getCurrency (obj){
	document.getElementById('cambio').value = document.getElementById('currency').value;	
};
