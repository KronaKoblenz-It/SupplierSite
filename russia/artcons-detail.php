<?php 
/************************************************************************/
/* CASASOFT ArcaWeb                               		      			*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2014 by Roberto Ceccarelli                        */
/* http://strawberryfield.altervista.org								*/
/*                                                                      */
/************************************************************************/

include("header.php");
include("db-utils.php");
head();

jsStart();
print("var testoPerc = \"" . _("Attenzione: rettifica superiore al") . " " . "\";\n");
jsEnd();
jsInclude("../js/jquery-1.10.1.min.js");
jsInclude("../js/jquery.tablesorter.min.js");
?>
<script type="text/javascript">
//<![CDATA[
$(document).ready(function() { 
	$.tablesorter.addParser({
		id: "datetime",
		is: function(s) {
			return false; 
		},
		format: function(s,table) {
			s = s.replace(/\-/g,"/");
			s = s.replace(/(\d{1,2})[\/\-](\d{1,2})[\/\-](\d{4})/, "$3/$2/$1");
			return $.tablesorter.formatFloat(new Date(s).getTime());
		},
		type: "numeric"
	});
	
	$("#maintable").tablesorter( {   dateFormat: 'dd/mm/yyyy', 
        headers: 
            {
                0:{sorter:'datetime'}
            } 
    }); 
} ); 

var modalWin = function(url, name) {
	window.open(url, name, "height=600,width=800,toolbar=no,directories=no,status=no, linemenubar=no,scrollbars=yes,resizable=yes");
};

var allowedDelta = 0.03;
var chkDelta = function(current, reference, id_riga) {
	if(reference == 0) {
		return true;
	}
	if( Math.abs((current - reference) / reference) > allowedDelta) {
		alert(testoPerc + allowedDelta*100 + "%");
		modalWin("rnc-make.php?id="+id_riga+"&close=1", "Inserimento RNC"); 
	}
	return true;
};
//]]>
</script>
<?php

$connectionstring = db_connect($dbase);

session_start();
$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
$cf = $cookie[0];
$art = $_GET["art"];
$Query = "SELECT DESCRIZION FROM MAGART WHERE CODICE = \"$art\"";
$queryexe = db_query($connectionstring, $Query) or die(mysql_error() ); 
$row = db_fetch_row($queryexe);
banner(_("Bolle ricevute"),htmlentities("$art - " . $row[0]));
$maga = "F" . substr($cf,2);

print("<form action=\"artcons-rett.php\" method=\"post\">\n"); 
print("<table class=\"list\" id=\"maintable\">\n<thead>\n");
trList();
thList( _("Data bolla") ); 
thList( _("Numero bolla") ); 
thList( _("Fornitore") ); 
thList( _("Quantit&agrave;") ); 
thList( _("Effettiva") ); 
thList( _("Lotto") ); 
print("</tr>\n</thead>\n<tbody>\n");

//SQL query  
$Query = "SELECT DOCRIG.ID, DOCRIG.DATADOC, DOCRIG.TIPODOC, DOCRIG.NUMERODOC, DOCRIG.CODICECF, ";
$Query .= " DOCRIG.LOTTO, DOCRIG.QUANTITA, ANAGRAFE.DESCRIZION ";
$Query .= " FROM DOCRIG INNER JOIN DOCTES ON DOCRIG.ID_TESTA = DOCTES.ID";
$Query .= " INNER JOIN ANAGRAFE ON ANAGRAFE.CODICE = DOCRIG.CODICECF";
$Query .= " WHERE DOCTES.MAGARRIVO = \"$maga\"";
$Query .= " AND (DOCRIG.TIPODOC=\"BT\" or DOCRIG.TIPODOC=\"CE\" or DOCRIG.TIPODOC=\"RL\" or DOCRIG.TIPODOC=\"TL\") ";
$Query .= " AND DOCRIG.CODICEARTI = \"$art\" ";
$Query .= " ORDER BY DATADOC";

//execute query 
$queryexe = db_query($connectionstring, $Query) or die(mysql_error()); 

//query database 
$n = 0;
while($row = mysql_fetch_object($queryexe)) 
{ 
	$n++;
	trList(); 
	tdList( format_date($row->DATADOC) ); 
	tdList( $row->TIPODOC . " " . $row->NUMERODOC ); 
	if($row->CODICECF == $cf) {
		$name = "KRONA KOBLENZ S.P.A.";
	} else {
		$name = htmlentities($row->DESCRIZION);
	}
	tdList($name); 
	tdList($row->QUANTITA, "right"); 
	print ("<td class=\"list\" style=\"text-align: right\">\n");
	print ("<input type=\"text\" id=\"qta$n\" name=\"qta$n\" size=\"6\" style=\"text-align: right;\" ");
	print ("value=\"" . $row->QUANTITA . "\" onChange=\"chkDelta(this.value, " . $row->QUANTITA . ", " . $row->ID . ");\">\n");
	hiddenField("r$n",$row->ID);
	print ("</td>\n"); 
	tdList($row->LOTTO); 
	print ("</tr>\n"); 
} 

//diconnect from database 
db_close($connectionstring); 

print("</tbody>\n<tfoot>\n");
print("<tr class=\"list\">\n<th class=\"list\" colspan=\"6\">\n");
print("<input type=\"submit\" value=\"" . _("Invia rettifiche") . "\">\n");
hiddenField("count",$n);
print("</th>\n</tr>\n");
print("</tfoot>\n</table>\n");
print("</form>\n");

print("<br>\n");
goEdit("artcons.php?id=$cf", _("Elenco materiali consegnati"));
goMain();
footer();
?>