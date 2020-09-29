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
                0:{sorter:'datetime'}
            } 
    }); 
} ); 
</script>
<?php
session_start();
$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);

$connectionstring = db_connect($dbase); 
banner("Lista rapporti non conformit&agrave;",$cookie[1]);

print("<table class=\"list\" id=\"maintable\">\n<thead>\n");
print("<tr class=\"list\">\n");
print("<th class=\"list\">Data</th>\n"); 
print("<th class=\"list\">Riferimento</th>\n"); 
print("<th class=\"list\">Descrizione</th>\n"); 
print("<th class=\"list\">Tipo</th>\n"); 
print("<th class=\"list\">Stato</th>\n"); 
print("</tr>\n</thead>\n<tbody>\n");

//SQL query  
$Query = "SELECT ISORNC.DATAREG,ISORNC.DOCNMOV,ISORNC.DOCTIP,ISORNC.DATAEND,ISORNC.DESCRIZION,ISORNC.ID, ";
$Query .= "ISOCAUSE.DESCRIZION AS TIPONC ";
$Query .= "FROM ISORNC LEFT OUTER JOIN ISOCAUSE ON ISOCAUSE.CODICE = ISORNC.CAUSA ";
$Query .= "WHERE CODFOR = \"" . $cookie[0] ."\" " ;
$Query .= "ORDER BY ISORNC.DATAREG DESC ";

//execute query 
$queryexe = db_query($connectionstring, $Query) or die(mysql_error()); 

//query database 
    while($row = db_fetch_row($queryexe)) 
    { 
    $datareg = format_date($row[0]); 
    $stato = $row[3] >0 ? "Chiusa" : "Aperta";
     
    //format results 
    print ("<tr class=\"list\">\n"); 
    print ("<td class=\"list\"><a href=\"rnc-detail.php?id=" . $row[5] . "\" >$datareg</a></td>\n"); 
    print ("<td class=\"list\">" . $row[2] . " " . $row[1] . "</td>\n"); 
    print ("<td class=\"list\">" . $row[4] . "</td>\n"); 
    print ("<td class=\"list\">" . $row[6] . "</td>\n"); 
    print ("<td class=\"list\">$stato</td>\n"); 
    print ("</tr>\n"); 
    } 

//diconnect from database 
db_close($connectionstring); 


print("</tbody>\n</table>\n");

print("<br>\n");
goMain();
footer();
?>