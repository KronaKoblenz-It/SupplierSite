<?php 
/************************************************************************/
/* Project ArcaWeb                               		        		*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2011 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php");
include("db-utils.php");
head();
 
$articolo = strtoupper($_GET['articolo']);
$lotto = strtoupper($_GET['lotto']);
$anno = current_year();
session_start();
$cookie = preg_split("/\|/",$_COOKIE['CodiceAgente']);
$maga = "F" . substr($cookie[0],2);

//connect to database 
$connectionstring = db_connect($dbase); 


$Query = "SELECT DESCRIZION, UNMISURA FROM MAGART WHERE CODICE = \"$articolo\""; 
$rs1 = db_query($connectionstring, $Query); 
if($row1 = db_fetch_row($rs1))
{
	banner($articolo . " - " . $row1[0]);
	$um = $row1[1];
	print("<form method=\"get\" action=\"inv_setqta.php\">\n");
	print("<table border=\"1\">\n");
	print ("<tr><th>$articolo<input type=\"hidden\" id=\"articolo\" name=\"articolo\" value=\"$articolo\"></th>");
	print ("<th>" . $row1[0] . "</th></tr>\n"); 
	print ("<tr><th>Lotto</th>");
	print ("<th>$lotto&nbsp;<input type=\"hidden\" id=\"lotto\" name=\"lotto\" value=\"$lotto\"></th></tr>\n"); 

	$Query = "SELECT quantita FROM u_invent WHERE codicearti = \"$articolo\" and lotto = \"$lotto\" and magazzino = \"$maga\""; 
	$rs = db_query($connectionstring, $Query); 
	$exist = "none";
	$giac = 0;
	if($row = db_fetch_row($rs))
	{
		$giac = $row[0];
		$exist = "block";
	}	
	print ("<tr style=\"display: $exist;\"><td><label for=\"qtaold\">Q.ta attuale ($um)</label></td>");
	print ("<td><input type=\"text\" id=\"qtaold\" size=\"10\" value=\"$giac\" disabled=\"disabled\"></td></tr>\n"); 
	print ("<tr style=\"display: $exist;\"><td><label for=\"somma\">Somma q.ta</label></td>"); 
    print ("<td><input type=\"checkbox\" id=\"somma\" name=\"somma\"></td></tr>\n");
	print ("<tr><td><label for=\"qtanew\">Q.ta rilevata ($um)</label></td>");
	print ("<td><input type=\"text\" id=\"qtanew\" name=\"qtanew\" size=\"10\">");
	print ("<input type=\"submit\" value=\"Ok\" ></td></tr>\n"); 
 
	print ("</table>\n</form>\n");
}
else
{
	banner($articolo . " - Non trovato");
	print("<h2>Articolo $articolo non trovato</h2>");
}
//-----------------------
//diconnect from database 
db_close($connectionstring); 

footer();
?>