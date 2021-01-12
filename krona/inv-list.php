<?php 
/************************************************************************/
/* CASASOFT ArcaWeb                               		      			*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2017 by Roberto Ceccarelli                        */
/* http://strawberryfield.altervista.org								*/
/*                                                                      */
/************************************************************************/

include("header.php");
include("db-utils.php");
include("inv_lib.php");
$inc = <<<EOT
  $('#maintable').dataTable().yadcf([
	    {column_number : 0, filter_type: "text"},
	    {column_number : 1, filter_type: "text"},
	    {column_number : 3, filter_type: "text"}
		]);
EOT;

head(dataTableInit($inc));

session_start();
$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
$maga = "F" . substr($cookie[0],2);
include("inv_common.php");
if ($mode == 'sfridi') {
	$maga = "S" . substr($cookie[0], 2);
}

$connectionstring = db_connect($dbase); 
$Query = "SELECT DESCRIZION FROM ANAGRAFE WHERE CODICE = '$fornitore'";
$queryexe = db_query($connectionstring, $Query) or die(mysql_error() ); 
$row = db_fetch_row($queryexe);
if($mode == "attr") 
	banner("Lista inventario attrezzature",$row[0]);
else
	banner("Lista inventario di magazzino",$row[0]);


print("<table id=\"maintable\" class=\"list\">\n");
print("<thead>\n<tr class=\"list\">\n");
print("<th class=\"list\"><b>Articolo</b></th>\n"); 
print("<th class=\"list\"><b>Descrizione</b></th>\n"); 
print("<th class=\"list\"><b>Quantit&agrave;</b></th>\n"); 
print("<th class=\"list\"><b>Lotto</b></th>\n"); 
print("</tr>\n</thead>\n<tbody>\n");


//SQL query  
$from="";
if($mode == "attr") 
	$from = "u_inventa as";

$Query = <<<EOT
SELECT u_invent.codicearti, MAGART.DESCRIZION, u_invent.quantita, u_invent.lotto 
FROM $from u_invent inner join MAGART on MAGART.CODICE = u_invent.codicearti 
WHERE u_invent.magazzino = '$maga'
ORDER BY codicearti
EOT;


//execute query 
$queryexe = db_query($connectionstring, $Query) or die(mysql_error()); 

//query database 
while($row = db_fetch_row($queryexe)) { 
    //format results 
    print ("<tr class=\"list\">\n"); 
    print ("<td class=\"list\">" . $row[0] . "</td>\n"); 
    print ("<td class=\"list\">" . $row[1] . "</td>\n"); 
    print ("<td class=\"list\" style=\"text-align: right;\">" . $row[2] . "</td>\n"); 
    print ("<td class=\"list\">" . $row[3] . "</td>\n"); 
    print ("</tr>\n"); 
    } 
print("</tbody>\n</table>\n");

// Diamo la possibilt� di dichiarare completato l'inventario	
$from="";
if($mode == "attr") 
	$from = "u_invfinea as";
$Query = "select finito from $from u_invfine where magazzino='$maga'";	
$queryexe = db_query($connectionstring, $Query) or die(mysql_error() ); 
if( !($row = db_fetch_row($queryexe)) ) {
	print("<form action=\"inv-setend.php\">\n");
	hiddenField("mode", $mode);
	print("<input type=\"submit\" value=\"Dichiara completato l'inventario\">\n");
	print("</form>\n");
}
	
//diconnect from database 
db_close($connectionstring); 

goMenu(); 
footer();
?>