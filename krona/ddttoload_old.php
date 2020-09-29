<?php 
/************************************************************************/
/* CASASOFT ArcaWeb                               		      			*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2013 by Roberto Ceccarelli                        */
/* http://strawberryfield.altervista.org								*/
/*                                                                      */
/************************************************************************/

include("header.php");
include("db-utils.php");
head();
?>
<script type="text/javascript" src="../js/jquery-1.10.1.min.js"></script>
<script type="text/javascript" src="../js/jquery.tablesorter.min.js"></script>
<script type="text/javascript">
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
                0:{sorter:'datetime'},
                4:{sorter:'datetime'}
            } 
    }); 
} ); 
</script>
<?php
$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
$connectionstring = db_connect($dbase); 
$Query = "SELECT DESCRIZION FROM ANAGRAFE WHERE CODICE = \"" . $cookie[0] ."\"";
$queryexe = db_query($connectionstring, $Query) or die(mysql_error() ); 
$row = db_fetch_row($queryexe);
banner("Bolle in attesa di acquisizione",$row[0]);


print("<table class=\"list\" id=\"maintable\">\n");
print("<thead>\n");
print("<tr class=\"list\">\n");
print("<th class=\"list\">Data</th>\n"); 
print("<th class=\"list\">Numero bolla</th>\n");
print("<th class=\"list\">Tipo Doc. Rif.</th>\n");
print("<th class=\"list\">Numero Doc. Rif.</th>\n");
print("<th class=\"list\">Data Doc. Rif.</th>\n");
print("<th class=\"list\">Articolo</th>\n");
print("<th class=\"list\">Descrizione</th>\n");
print("<th class=\"list\">Q.t&agrave</th>\n");
print("<th class=\"list\">Lotto</th>\n");
print("<th class=\"list\">&nbsp;</th>\n");
print("</tr>\n");
print("</thead>\n");
print("<tbody>\n");

//print("$cookie[0]");

//SQL query
$Query = "SELECT U_BARDT.DATADOC, ";
$Query .= "U_BARDT.ID, U_BARDT.NUMERODOCF, U_BARDT.RIF_TIPODOC, U_BARDT.RIF_NUMERODOC, U_BARDT.RIF_DATADOC, U_BARDR.CODICEARTI, MAGART.DESCRIZION AS DESART, U_BARDR.QUANTITA, U_BARDR.LOTTO ";
$Query .= "FROM U_BARDT RIGHT JOIN U_BARDR ON U_BARDT.ID = U_BARDR.ID_TESTA LEFT JOIN MAGART ON MAGART.CODICE = U_BARDR.CODICEARTI ";
$Query .= " WHERE U_BARDT.DEL=0 AND U_BARDR.DEL=0 AND U_BARDR.ESPLDISTIN = 'P' AND U_BARDT.CODICECF = \"" . $cookie[0] ;
$Query .= "\" ORDER BY U_BARDT.DATADOC DESC, U_BARDT.RIF_NUMERODOC ASC ";

//execute query 
$queryexe = db_query($connectionstring, $Query) or die(mysql_error()); 

//query database 
    while($row = db_fetch_row($queryexe)) 
    { 
        $name = format_date($row[0]);
        $addr = $row[2];
        $rif_tipodoc = $row[3];
        $rif_numerodoc = $row[4];

        //format results
        print("<tr class=\"list\">\n");
        print("<td class=\"list\"><a href=\"moddb.php?id=" . $row[1] . "\" >$name</a></td>\n");
        print("<td class=\"list\">$addr</td>\n");
        print("<td class=\"list\">" . $rif_tipodoc . "</td>\n");
        print("<td class=\"list\">" . $rif_numerodoc . "</td>\n");
        print("<td class=\"list\">" . format_date($row[5]) . "</td>\n");
        print("<td class=\"list\">" . $row[6] .  "</td>\n");
        print("<td class=\"list\">" . $row[7] .  "</td>\n");
        print("<td class=\"list\">" . $row[8] .  "</td>\n");
        print("<td class=\"list\">" . $row[9] .  "</td>\n");
        print("<td class=\"list\"><a href=\"deldoc.php?id=" . $row[1] . "\" ><img noborder src=\"../img/b_drop.png\"></a></td>\n");
        print("</tr>\n");
    } 

//diconnect from database 
db_close($connectionstring); 

print("</tbody>\n");
print("</table>\n");
print("<br>\n");
goMain();
footer();
?>