<?php 
/************************************************************************/
/* CASASOFT ArcaWeb                               		       			*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2013 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

function du_table() {
  print("<table class=\"list\">\n");
  print("<tr class=\"list\">\n");
}

function du_th($desc) {
  print("<th class=\"list\">$desc</th>\n");
}

function du_tr() {
  print ("<tr class=\"list\">\n"); 
}

function print_label($link, $desc, $articolo, $lotto, $descart, $code, $cliven = "") {
	print("<td class=\"list\" style=\"text-align: center;\">"); 
	if($articolo == "") {
		print("&nbsp;");
	} else {
		print("<a target=\"_blank\" href=\"$link?art=" . urlencode(trim($articolo)) . "&amp;lotto=" . urlencode(trim($lotto)));
		print("&amp;desc=" . urlencode(trim($descart)) . "&amp;code=" . urlencode(trim($code)) . "&amp;cliven=" . $cliven);
		print("\" title=\"$desc\">\n<img style=\"border: none;\" src=\"../img/printer.png\" alt=\"$desc\"></a>\n"); 
	} 
	print("</td>\n"); 
}

function make_rnc($idriga,$articolo) { 
	print("<td class=\"list\" style=\"text-align: center;\">"); 
	if($articolo == "") {
		print("&nbsp;");
	} else {
		$desc = "Nuova RNC";
		print("<a href=\"rnc-make.php?id=$idriga\" title=\"$desc\">");
		print("<img style=\"border: none;\" src=\"../img/error.png\" alt=\"$desc\"></a>"); 
	} 
	print("</td>\n"); 
}


function doc_rows($id,$connectionstring) {
global $lang, $str_codice, $str_desc, $str_um, $str_quantita, $str_prezzoun, $str_sconti, $str_totale;
global $str_dataprevcons, $str_residuo, $str_dataevas ;
du_table();

$Query = "SELECT TIPODOC FROM DOCTES WHERE ID = $id";
$queryexe = db_query($connectionstring, $Query); 
$row = db_fetch_row($queryexe);
$isOF = in_array($row[0], array("FO", "LO", "OF", "OL"));
$isBollaCL = in_array($row[0], array("BT", "CE", "RL", "TL", "CP"));
$isOrd = ( $isOF or $row[0] == "OC");
du_th($str_codice[$lang]);
du_th($str_desc[$lang]);
du_th($str_um[$lang]);
du_th($str_quantita[$lang]);
du_th($str_residuo[$lang]);
if (!$isBollaCL) {
	du_th($str_prezzoun[$lang]);
	du_th($str_sconti[$lang]);
	du_th($str_totale[$lang]);
} 
if($isBollaCL) {
	du_th("Lotto");
	du_th("RNC");
}
if( $isOrd ) {
	du_th($str_dataprevcons[$lang]);
	du_th($str_dataevas[$lang]);
}
if( $isOF or $isBollaCL ) {
	du_th("A4");
	du_th("88x36 CF");
	du_th("88x36 SC");
    du_th("88x36 PZ");
	du_th("Cod. Cli.");
}
print("</tr>\n");


//SQL quyery  
$Query = "SELECT DOCRIG.DATADOC, DOCRIG.NUMERODOC, DOCRIG.CODICEARTI, DOCRIG.DESCRIZION, DOCRIG.UNMISURA, DOCRIG.QUANTITA, DOCRIG.QUANTITARE,";
$Query .= " DOCRIG.PREZZOUN, DOCRIG.SCONTI, DOCRIG.PREZZOTOT, DOCRIG.ID_TESTA, DOCRIG.DATACONSEG,";
$Query .= " DOCRIG.U_FM, DOCRIG.U_MISURAH, DOCRIG.U_MISURAL, DOCRIG.U_MISURAS, DOCRIG.U_INGOMH, DOCRIG.U_INGOML,";
$Query .= " DOCRIG.U_CASSSX, DOCRIG.U_CASSDX, DOCRIG.LOTTO, DOCRIG.ID, DOCRIG.DATAINIZIO, DOCRIG.U_CLIVEN, ";
$Query .= " CODALT.CODARTFOR, CODALT.U_BARCODE";
$Query .= " FROM DOCRIG LEFT OUTER JOIN CODALT ON CODALT.CODICEARTI = DOCRIG.CODICEARTI AND CODALT.CODCLIFOR = DOCRIG.U_CLIVEN";
$Query .= " WHERE ID_TESTA = " . $id;
$queryexe = db_query($connectionstring, $Query); 

//query database 
while($row = mysql_fetch_object($queryexe))  { 

	$id_testa = $row->ID_TESTA;

	//format results 
	du_tr(); 
	print ("<td class=\"list\">" . $row->CODICEARTI . "</td>\n"); 
	print ("<td class=\"list\">" . $row->DESCRIZION . "</td>\n"); 
	print ("<td class=\"list\" style=\"text-align: center;\">" . $row->UNMISURA . "</td>\n");
	print ("<td class=\"list\" style=\"text-align: right;\">" . $row->QUANTITA . "</td>\n"); 
	print ("<td class=\"list\" style=\"text-align: right;\">" . $row->QUANTITARE . "</td>\n"); 
    if( !$isBollaCL ) {	
		print ("<td class=\"list\" style=\"text-align: right;\">" . $row->PREZZOUN . "</td>\n"); 
		print ("<td class=\"list\" style=\"text-align: center;\">" . $row->SCONTI . "</td>\n"); 
		print ("<td class=\"list\" style=\"text-align: right;\">" . $row->PREZZOTOT . "</td>\n"); 
	} 
	if($isBollaCL)	{
		print ("<td class=\"list\" style=\"text-align: center;\">" . $row->LOTTO . "</td>\n"); 
		make_rnc ($row->ID, $row->CODICEARTI); 
	}
	if( $isOrd ) {
		print("<td class=\"list\" style=\"text-align: center;\">" . format_date($row->DATACONSEG) . "</td>\n"); 
		print("<td class=\"list\" style=\"text-align: center;\">" . format_date($row->DATAINIZIO) . "</td>\n"); 
	}
/*	if( $isOF or $isBollaCL ) {
		print("<td class=\"list\" style=\"text-align: center;\"><a target=\"_blank\" href=\"eticha4.php?art=" . urlencode($row->CODICEARTI));
		print("\"><img style=\"border: none;\" src=\"printer.png\" alt=\"Etichette A4\"></a></td>\n"); 
		print("<td class=\"list\" style=\"text-align: center;\"><a target=\"_blank\" href=\"etich1.php?art=" . urlencode($row->CODICEARTI));
		print("\"><img style=\"border: none;\" src=\"printer.png\" alt=\"Etichetta 88x36\"></a></td>\n"); 
	}  */
	if( $isOF ) {
		print_label("eticha4.php", "Etichette A4", (is_null($row->CODARTFOR) ? $row->CODICEARTI : $row->CODARTFOR), $row->LOTTO, $row->DESCRIZION, (is_null($row->U_BARCODE) ? "" : $row->U_BARCODE));
		print_label("etich1lotti_unificate.php", "Etichetta 88x36", (is_null($row->CODARTFOR) ? $row->CODICEARTI : $row->CODARTFOR), $row->LOTTO, $row->DESCRIZION, (is_null($row->U_BARCODE) ? "" : $row->U_BARCODE), $row->U_CLIVEN);
		print_label("etich1lotti_unificate_SC.php", "Etichetta 88x36", (is_null($row->CODARTFOR) ? $row->CODICEARTI : $row->CODARTFOR), $row->LOTTO, $row->DESCRIZION, (is_null($row->U_BARCODE) ? "" : $row->U_BARCODE), $row->U_CLIVEN);
        print_label("etich1lotti_unificate_PZ.php", "Etichetta 88x36", (is_null($row->CODARTFOR) ? $row->CODICEARTI : $row->CODARTFOR), $row->LOTTO, $row->DESCRIZION, (is_null($row->U_BARCODE) ? "" : $row->U_BARCODE), $row->U_CLIVEN);
		if (in_array($row->CODICEARTI, array("5000 KND 100", "5000 KND 125", "5001 KND 100", "5001 KND 125"))){
			print_label("etichcodalta4.php", "Etichetta Cod. Alt.", (is_null($row->CODARTFOR) ? $row->CODICEARTI : $row->CODARTFOR), $row->LOTTO, $row->DESCRIZION, (is_null($row->U_BARCODE) ? "" : $row->U_BARCODE));
		}
		if (in_array($row->CODICEARTI, array("5400 100 0860", "5400 100"))){
			print_label("etichcodaltFRa4.php", "Etichetta Cod. Alt.", (is_null($row->CODARTFOR) ? $row->CODICEARTI : $row->CODARTFOR), $row->LOTTO, $row->DESCRIZION, (is_null($row->U_BARCODE) ? "" : $row->U_BARCODE));
		}
	}
	if( $isBollaCL ) {
		print_label("eticha4lotti.php", "Etichette A4", (is_null($row->CODARTFOR) ? $row->CODICEARTI : $row->CODARTFOR), $row->LOTTO, $row->DESCRIZION, (is_null($row->U_BARCODE) ? "" : $row->U_BARCODE));
		print_label("etich1lotti_unificate.php", "Etichetta 88x36", (is_null($row->CODARTFOR) ? $row->CODICEARTI : $row->CODARTFOR), $row->LOTTO, $row->DESCRIZION, (is_null($row->U_BARCODE) ? "" : $row->U_BARCODE), $row->U_CLIVEN);
		print_label("etich1lotti_unificate_SC.php", "Etichetta 88x36", (is_null($row->CODARTFOR) ? $row->CODICEARTI : $row->CODARTFOR), $row->LOTTO, $row->DESCRIZION, (is_null($row->U_BARCODE) ? "" : $row->U_BARCODE), $row->U_CLIVEN);
        print_label("etich1lotti_unificate_PZ.php", "Etichetta 88x36", (is_null($row->CODARTFOR) ? $row->CODICEARTI : $row->CODARTFOR), $row->LOTTO, $row->DESCRIZION, (is_null($row->U_BARCODE) ? "" : $row->U_BARCODE), $row->U_CLIVEN);
	}
	if( $isOF or $isBollaCL ) {
		print ("<td class=\"list\" style=\"text-align: center;\">" . (is_null($row->CODARTFOR) ? "&nbsp;" : $row->CODARTFOR) . "</td>\n"); 
	}

    print ("</tr>\n"); 
	
	if( $row->U_FM > 0) {
		du_tr(); 
		print ("<td class=\"list\"><i>Dimensioni</i></td>\n"); 
		print ("<td class=\"list\"><i>H=" . $row->U_MISURAH . " L=" . $row->U_MISURAL . " Sp=" . $row->U_MISURAS . "</i></td>\n"); 
		print ("<td class=\"list\">&nbsp;</td>\n"); 
		print ("<td class=\"list\">&nbsp;</td>\n"); 
		print ("<td class=\"list\">&nbsp;</td>\n");  
		if( !$isBollaCL) { 
			print ("<td class=\"list\">&nbsp;</td>\n");  
			print ("<td class=\"list\">&nbsp;</td>\n");  
		}
		if( $isOF ) {
			print ("<td class=\"list\">&nbsp;</td>\n"); 
		}
		if( $isOF or $isBollaCL ) {
			print ("<td class=\"list\">&nbsp;</td>\n"); 
			print ("<td class=\"list\">&nbsp;</td>\n"); 
		}
		print ("</tr>\n"); 
	
		du_tr(); 
		print ("<td class=\"list\"><i>Ingombri</i></td>\n"); 
		print ("<td class=\"list\"><i>H=" . $row->U_INGOMH . " L=" . $row->U_INGOML . "</i></td>\n"); 
		print ("<td class=\"list\">&nbsp;</td>\n"); 
		print ("<td class=\"list\">&nbsp;</td>\n"); 
		print ("<td class=\"list\">&nbsp;</td>\n");  
		if( !$isBollaCL) { 
			print ("<td class=\"list\">&nbsp;</td>\n");  
			print ("<td class=\"list\">&nbsp;</td>\n");  
		}
		if( $isOF ) {
			print ("<td class=\"list\">&nbsp;</td>\n"); 
		}
		if( $isOF or $isBollaCL ) {
			print ("<td class=\"list\">&nbsp;</td>\n"); 
			print ("<td class=\"list\">&nbsp;</td>\n"); 
		}
		print ("</tr>\n"); 
	}
} 

print ("</table>\n");
return $id_testa;
}

// -------------------------------------------
// leggo le bolle che derivano da questa testa
// -------------------------------------------
function doc_ddt($id_testa,$connectionstring) {
global $lang, $str_evasocon, $str_dataddt, $str_numero, $str_colli, $str_peso, $str_sped, $str_telefono;

$Query = "SELECT DISTINCT DOCTES.DATADOC, DOCTES.NUMERODOC, VETTORI.DESCRIZION, DOCTES.ID, ";
$Query = $Query . "DOCTES.DATADOCFOR, DOCTES.NUMERODOCF, ";
$Query = $Query . "VETTORI.TELEFONO, DOCTES.COLLI, DOCTES.PESOLORDO, DOCTES.TIPODOC ";
$Query = $Query . "FROM DOCTES INNER JOIN DOCRIG ON DOCTES.ID = DOCRIG.ID_TESTA ";
$Query = $Query . "LEFT OUTER JOIN VETTORI ON DOCTES.VETTORE1 = VETTORI.CODICE ";
$Query = $Query . "WHERE DOCRIG.RIFFROMT = $id_testa "; 
$Query = $Query . "AND DOCTES.TIPODOC != \"PL\" "; 
$queryexe = db_query($connectionstring, $Query) or die(mysql_error()); 


print("<br>\n<h3 class=\"name\" style=\"text-align: center;\">" . $str_evasocon[$lang] . "</h3>\n");
du_table();
du_th($str_dataddt[$lang]);
du_th($str_numero[$lang]);
du_th($str_colli[$lang]);
du_th($str_peso[$lang]);
du_th($str_sped[$lang]);
du_th($str_telefono[$lang]);
print("</tr>\n"); 

$ut = userType();

//query database 
    while($row = db_fetch_row($queryexe)) 
    { 
    $name = format_date($ut == "F" ? $row[4] : $row[0]); 
    $addr = ($ut == "F" ? $row[5] : $row[1]); 
    $stato = $row[2];
    $id = $row[3];  
    $telefono = $row[6];
    $colli = $row[7];
    $peso = $row[8];
	$tipodoc = $row[9];
     
    //format results 
    du_tr(); 
    print ("<td class=\"list\"><a href=\"ddt-detail.php?id=" . $row[3] . "\" >$name</a></td>\n"); 
    print ("<td class=\"list\">$tipodoc $addr</td>\n"); 
    print ("<td class=\"list\" style=\"text-align: right;\">$colli</td>\n"); 
    print ("<td class=\"list\" style=\"text-align: right;\">$peso</td>\n"); 
    print ("<td class=\"list\">$stato</td>\n"); 
    print ("<td class=\"list\">$telefono</td>\n"); 
    print ("</tr>\n"); 
    }

print ("</table>\n");
return $id;

}    


// -------------------------------------------
// leggo le fatture che derivano dalle bolle
// -------------------------------------------
function doc_fatt($id_testa,$connectionstring) {
global $lang, $str_fatturatocon, $str_data, $str_numero;
$Query = "SELECT DISTINCT DOCTES.DATADOC, DOCTES.NUMERODOC, DOCTES.ID, ";
$Query = $Query . "DOCTES.DATADOCFOR, DOCTES.NUMERODOCF ";
$Query = $Query . "FROM DOCTES INNER JOIN DOCRIG ON DOCTES.ID = DOCRIG.ID_TESTA ";
$Query = $Query . "WHERE DOCRIG.RIFFROMT = $id_testa "; 
$queryexe = db_query($connectionstring, $Query) or die(mysql_error()); 


print("<br>\n<h3 class=\"name\" style=\"text-align: center;\">" . $str_fatturatocon[$lang] . "</h3>\n");
du_table();
du_th($str_data[$lang]);
du_th($str_numero[$lang]);
print("</tr>\n"); 

$ut = userType();

//query database 
    while($row = db_fetch_row($queryexe)) 
    { 
    $name = format_date($ut == "F" ? $row[3] : $row[0]); 
    $addr = ($ut == "F" ? $row[4] : $row[1]); 
    $id = $row[2];
     
    //format results 
    du_tr(); 
    print ("<td class=\"list\"><a href=\"fatt-detail.php?id=" . $row[2] . "\" >$name</a></td>\n"); 
    print ("<td class=\"list\">$addr</td>\n"); 
    print ("</tr>\n"); 
    }

print ("</table>\n");
return $id;
}

// -------------------------------------------
// infine andiamo a cercare anche le scadenze
// -------------------------------------------
function doc_scad($id_testa,$connectionstring) {
global $lang, $str_scadenze, $str_tipo, $str_scadenza, $str_importo, $str_pagato;
$Query = "SELECT DATAPAG, DATASCAD, IMPEFFVAL, IMPORTOPAG, TIPO ";
$Query = $Query . "FROM SCADENZE ";
$Query = $Query . "WHERE ID_DOC = $id_testa "; 
$Query = $Query . "ORDER BY DATASCAD";
$queryexe = db_query($connectionstring, $Query) or die(mysql_error()); 


print("<br>\n<h3 class=\"name\" style=\"text-align: center;\">" . $str_scadenze[$lang] . "</h3>\n");
du_table();
du_th($str_tipo[$lang]);
du_th($str_scadenza[$lang]);
du_th($str_importo[$lang]);
du_th($str_pagato[$lang]);
print("</tr>\n"); 



//query database 
    while($row = db_fetch_row($queryexe)) 
    { 
    $name = scad_tipo($row[4], $lang); 
    $addr = format_date($row[1]); 
    $importo = $row[2];
    $pagato = $row[3];
     
    //format results 
    du_tr(); 
    print ("<td class=\"list\">$name</td>\n"); 
    print ("<td class=\"list\">$addr</td>\n"); 
    print ("<td class=\"list\">$importo</td>\n"); 
    print ("<td class=\"list\">$pagato</td>\n"); 
    print ("</tr>"); 
    }

print ("</table>\n");
}

?>
