/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2011 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

function validateQta(obj, orVal) {
  if (obj.value > orVal) {
    alert("Quantità superiore alla richiesta");
	return false;
  }
  if (obj.value < orVal) {
//    alert("Inferiore");
	obj.setAttribute('onblur','validateQta(this,'+obj.value+');');
	var rownum = obj.id.substr(3);
	var tbl = document.getElementById('tblbody');
	var maxrow = tbl.rows.length +1;
	var row = tbl.insertRow(rownum);
	var c1 = row.insertCell(0);
	var c2 = row.insertCell(1);
	var c3 = row.insertCell(2);
	var c4 = row.insertCell(3);
	var artobj = document.getElementById('code'+rownum);
	var descobj = document.getElementById('desc'+rownum);
	var newval = orVal - obj.value;
	c1.innerHTML = '<input type="text" readonly="readonly" size="16" name="code'+maxrow+'" id="code'+maxrow+'" value="' + artobj.value + '">';
	c2.innerHTML = descobj.innerHTML;
	c3.innerHTML = '<input type="text" size="3" name="qta'+maxrow+'" id="qta'+maxrow+'" onblur="validateQta(this,' + newval + ');" value="' + newval + '">';
	c4.innerHTML = '<input type="text" size="12" name="lotto'+maxrow+'" id="lotto'+maxrow+'" value="">';
	var count = document.getElementById('count');
//	alert(count.getAttribute('value'));
	count.setAttribute('value', (count.getAttribute('value') * 1) +1 );
	return false;
  }
  return true;
}

function validateLotto(obj) {
  if(obj.value == "") {
    return "";
  }	
  var rownum = obj.id.substr(5);
  var artobj = document.getElementById('code'+rownum);
  var url = "getcodicelotto.php?art=" + encodeURIComponent(artobj.value);
  url = url + "&lotto=" + encodeURIComponent(obj.value);
  makeHttpXml();
  httpXml.open("GET", url, false);
  httpXml.send(null);
  var cRet = httpXml.responseText;
  if ("*error*" == cRet)  {
    alert("Codice non riconosciuto");
	obj.value = "";
    cRet = "";
  }
  return cRet;
}