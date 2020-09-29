/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2011 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/
function toggle(div_id) {
	var el = document.getElementById(div_id);
	if ( el.style.display == 'none' ) {	el.style.display = 'block';}
	else {el.style.display = 'none';}
}
function blanket_size(popUpDivVar) {
	if (typeof window.innerWidth != 'undefined') {
		viewportheight = window.innerHeight;
	} else {
		viewportheight = document.documentElement.clientHeight;
	}
	if ((viewportheight > document.body.parentNode.scrollHeight) && (viewportheight > document.body.parentNode.clientHeight)) {
		blanket_height = viewportheight;
	} else {
		if (document.body.parentNode.clientHeight > document.body.parentNode.scrollHeight) {
			blanket_height = document.body.parentNode.clientHeight;
		} else {
			blanket_height = document.body.parentNode.scrollHeight;
		}
	}
	var blanket = document.getElementById(popUpDivVar);
	blanket.style.height = blanket_height + 'px';
}
function popup(windowname,index) {
	blanket_size(windowname);
	if(index >= 0) { aggiornaCampi(index);	}
	toggle(windowname);		
}
function aggiornaCampi(index) {
	var codicearti = document.getElementById('codicearti');
	var descrizion = document.getElementById('descrizion');
	var qtariga = document.getElementById('qtariga');
	var codicelotto = document.getElementById('codicelotto');
	var ubicazione = document.getElementById('ubicazione');
	var giacenza = document.getElementById('giacenza');
	var currentid = document.getElementById('currentid');
	currentid.value = index;
	codicearti.value = document.getElementById('code'+index).value;
	qtariga.value = document.getElementById('qta'+index).value;
	codicelotto.value = document.getElementById('lotto'+index).value;
	ubicazione.value = document.getElementById('ubic'+index).innerText;
	descrizion.value = document.getElementById('desc'+index).innerText;
	giacenza.value = document.getElementById('giac'+index).innerText;
}
function prevRow() {
	var currentid = document.getElementById('currentid');
	if (currentid.value > 1) {
		aggiornaCampi(currentid.value -1);
	} else {
		alert("Non ci sono altre righe.");
	}
}
function nextRow() {
	var currentid = document.getElementById('currentid');
	var count = document.getElementById('count');
	if (currentid.value < count.value) {
		aggiornaCampi(currentid.value*1 +1);
	} else {
		alert("Non ci sono altre righe.");
	}
}