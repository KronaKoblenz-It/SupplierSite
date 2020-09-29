<?php 
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2012 by Roberto Ceccarelli                        */
/* 																		*/
/************************************************************************/

include("header.php"); 
include("db-utils.php");
head();
?>

<script type="text/javascript" src="../js/ajaxlib.js"></script>
<script type="text/javascript" src="../js/select_lib.js"></script>
<script type="text/javascript">
/* <![CDATA[ */

function listaRif(cCodCF, cCodArt)  {
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
	  oDoc = oList[j];
	  rif = oDoc.getElementsByTagName("id_testa")[0].firstChild.nodeValue;
	  str = "Bolla "+oDoc.getElementsByTagName("numerodocf")[0].firstChild.nodeValue;
	  str = str + " - Lotto " + oDoc.getElementsByTagName("lotto")[0].firstChild.nodeValue;
      appendOptionLast2("copy", str, rif);
	}
}

/* ]]> */
</script>

<?php
$connectionstring = db_connect($dbase); 

$cookie = preg_split("/\|/",$_COOKIE['CodiceAgente']);
banner("Reso conto lavoro da ",$cookie[1]);
$codcf = $cookie[0];

print("<form action=\"esplodi.php\" method=\"get\" >\n");
print("<table>\n");
print("<tr><td>Articolo</td>\n");
print("<td><select name=\"articolo\" id=\"articolo\" ");
print("onchange=\"listaRif('$codcf', this.options[this.selectedIndex].value);\" >\n");
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
print("</select></td></tr>\n");

print("<tr><td>Riferimento</td>\n");
print("<td><select name=\"rif\" id=\"rif\" >\n");
print("<option value=\"\">&nbsp;</option>\n");
print("</select></td></tr>\n");

print("<tr><td>Lotto</td><td><input type=\"text\" name=\"lotto\" id=\"lotto\" ></td></tr>\n");
print("<tr><td>Quantita</td><td><input type=\"text\" name=\"quantita\" id=\"quantita\" ></td></tr>\n");
print("<tr><td>Numero bolla</td><td><input type=\"text\" name=\"numero\" id=\"numero\" ></td></tr>\n");

print("<tr><td>Copia lotti da</td>\n");
print("<td><select name=\"copy\" id=\"copy\" >\n");
print("<option value=\"\">&nbsp;</option>\n");
print("</select></td></tr>\n");

print("</table>\n");
print("<input type=\"submit\" id=\"btnok\" value=\"Ok\" >\n");
print("</form>\n");

print ("<br>\n");
goMain();
footer();
?>