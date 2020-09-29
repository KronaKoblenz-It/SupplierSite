<?php 
/************************************************************************/
/* Project ArcaWeb                               		 			    */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2012 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/
function writeDoc($id_testa) {
	global $conn; 

	$Query =  "SELECT TIPODOC,DATADOC,NUMERODOC,DATADOCFOR,NUMERODOCF,DATACONSEG,CODICECF,COLLI,PESOLORDO,VETTORE1,MAGARRIVO ";
	$Query .= "FROM DOCTES WHERE ID = $id_testa";
	$rs = db_query($conn, $Query) or die(mysql_error()); 
	$row = mysql_fetch_object($rs);
	 
	print("<aw:doctes>\n");
	print("<aw:id_testa>$id_testa</aw:id_testa>\n");
	print("<aw:codicecf>" . $row->CODICECF . "</aw:codicecf>\n");
	print("<aw:tipodoc>" . $row->TIPODOC . "</aw:tipodoc>\n");
	print("<aw:datadoc>" . $row->DATADOC . "</aw:datadoc>\n");
	print("<aw:numerodoc>" . $row->NUMERODOC . "</aw:numerodoc>\n");
	print("<aw:datadocfor>" . $row->DATADOCFOR . "</aw:datadocfor>\n");
	print("<aw:numerodocf>" . $row->NUMERODOCF . "</aw:numerodocf>\n");
	print("<aw:dataconseg>" . $row->DATACONSEG . "</aw:dataconseg>\n");
	print("<aw:colli>" . $row->COLLI . "</aw:colli>\n");
	print("<aw:pesolordo>" . $row->PESOLORDO . "</aw:pesolordo>\n");
	print("<aw:vettore1>" . $row->VETTORE1 . "</aw:vettore1>\n");
	print("<aw:magpartenz></aw:magpartenz>\n");
	print("<aw:magarrivo>" . $row->MAGARRIVO . "</aw:magarrivo>\n");
	print("</aw:doctes>\n");


	$Query =  "SELECT ID,DATADOC,NUMERODOC,CODICEARTI,DESCRIZION,UNMISURA,QUANTITA,QUANTITARE,DATACONSEG, ";
	$Query .= "PREZZOUN,SCONTI,PREZZOTOT,DATAINIZIO,LOTTO ";
	$Query .= "FROM DOCRIG WHERE ID_TESTA = $id_testa";
	$rs = db_query($conn, $Query) or die(mysql_error()); 
	while($row = mysql_fetch_object($rs)) {

		print("<aw:docrig>\n");

		print("<aw:id_riga>" . $row->ID . "</aw:id_riga>\n");
		print("<aw:id_testa>$id_testa</aw:id_testa>\n");
		print("<aw:datadoc>" . $row->DATADOC . "</aw:datadoc>\n");
		print("<aw:numerodoc>" . $row->NUMERODOC . "</aw:numerodoc>\n");
		print("<aw:codicearti>" . $row->CODICEARTI . "</aw:codicearti>\n");
		print("<aw:descrizion>" . $row->DESCRIZION . "</aw:descrizion>\n");
		print("<aw:unmisura>" . $row->UNMISURA . "</aw:unmisura>\n");
		print("<aw:quantita>" . $row->QUANTITA . "</aw:quantita>\n");
		print("<aw:quantitare>" . $row->QUANTITARE . "</aw:quantitare>\n");
		print("<aw:lotto>" . $row->LOTTO . "</aw:lotto>\n");
		print("<aw:dataconseg>" . $row->DATACONSEG . "</aw:dataconseg>\n");
		print("<aw:dataprev>" . $row->DATAINIZIO . "</aw:dataprev>\n");
		print("<aw:prezzoun>" . $row->PREZZOUN . "</aw:prezzoun>\n");
		print("<aw:sconti>" . $row->SCONTI . "</aw:sconti>\n");
		print("<aw:prezzotot>" . $row->PREZZOTOT . "</aw:prezzotot>\n");

		print("</aw:docrig>\n");
	}
	print("</aw:doc>\n");
}
?>