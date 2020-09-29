<?php 
/************************************************************************/
/* CASASOFT ArcaWeb                               		       			*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2012 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

function du_table() {
  print("<table class=\"list\">\n");
  print("<tr class=\"list\">\n");
}

function du_th() {
  return "<th class=\"list\">";
}

function du_tr() {
  print ("<tr class=\"list\">\n"); 
}

function print_label($link, $desc, $articolo, $lotto) { 
	print("<td class=\"list\" style=\"text-align: center;\">"); 
	if($articolo == "") {
		print("&nbsp;");
	} else {
		print("<a target=\"_blank\" href=\"$link?art=" . urlencode($articolo) . "&amp;lotto=" . urlencode($lotto));
		print("\"><img style=\"border: none;\" src=\"printer.png\" alt=\"$desc\"></a>"); 
	} 
	print("</td>\n"); 
}

function doc_rows($id,$connectionstring) {
global $lang, $str_codice, $str_desc, $str_um, $str_quantita, $str_prezzoun, $str_sconti, $str_totale, $str_dataprevcons ;
du_table();

$Query = "SELECT TIPODOC FROM DOCTES WHERE ID = $id";
$queryexe = db_query($connectionstring, $Query); 
$row = db_fetch_row($queryexe);
$isOF = in_array($row[0], array("FO", "LO", "OF", "OL"));
$isBollaCL = in_array($row[0], array("BT", "CE", "RL", "TL"));

print(du_th() . $str_codice[$lang] . "</th>\n");
print(du_th() . $str_desc[$lang] . "</th>\n");
print(du_th() . $str_um[$lang] . "</th>\n");
print(du_th() . $str_quantita[$lang] . "</th>\n");
if (!$isBollaCL) {
	print(du_th() . $str_prezzoun[$lang] . "</th>\n");
	print(du_th() . $str_sconti[$lang] . "</th>\n");
	print(du_th() . $str_totale[$lang] . "</th>\n");
} else {
	print(du_th() . "Lotto</th>\n");
}
if( $isOF ) {
	print(du_th() . $str_dataprevcons[$lang] . "</th>\n");
}
if( $isOF or $isBollaCL ) {
	print(du_th() . "A4</th>\n");
	print(du_th() . "88x36</th>\n");
}
print("</tr>\n");


//SQL quyery  
$Query = "SELECT DATADOC,NUMERODOC,CODICEARTI,DESCRIZION,UNMISURA,QUANTITA,PREZZOUN,SCONTI,PREZZOTOT,ID_TESTA,DATACONSEG,";
$Query .= " U_FM,U_MISURAH,U_MISURAL,U_MISURAS,U_INGOMH,U_INGOML,U_CASSSX,U_CASSDX,LOTTO";
$Query .= " FROM DOCRIG WHERE ID_TESTA = " . $id;
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
    if( !$isBollaCL ) {	
		print ("<td class=\"list\" style=\"text-align: right;\">" . $row->PREZZOUN . "</td>\n"); 
		print ("<td class=\"list\" style=\"text-align: center;\">" . $row->SCONTI . "</td>\n"); 
		print ("<td class=\"list\" style=\"text-align: right;\">" . $row->PREZZOTOT . "</td>\n"); 
	} else {
		print ("<td class=\"list\" style=\"text-align: center;\">" . $row->LOTTO . "</td>\n"); 
	}
	if( $isOF ) {
		print("<td class=\"list\" style=\"text-align: center;\">" . format_date($row->DATACONSEG) . "</td>\n"); 
	}
/*	if( $isOF or $isBollaCL ) {
		print("<td class=\"list\" style=\"text-align: center;\"><a target=\"_blank\" href=\"eticha4.php?art=" . urlencode($row->CODICEARTI));
		print("\"><img style=\"border: none;\" src=\"printer.png\" alt=\"Etichette A4\"></a></td>\n"); 
		print("<td class=\"list\" style=\"text-align: center;\"><a target=\"_blank\" href=\"etich1.php?art=" . urlencode($row->CODICEARTI));
		print("\"><img style=\"border: none;\" src=\"printer.png\" alt=\"Etichetta 88x36\"></a></td>\n"); 
	}  */
	if( $isOF ) {
		print_label("eticha4.php", "Etichette A4", $row->CODICEARTI, $row->LOTTO);
		print_label("etich1.php", "Etichetta 88x36", $row->CODICEARTI, $row->LOTTO);
	}
	if( $isBollaCL ) {
		print_label("eticha4lotti.php", "Etichette A4", $row->CODICEARTI, $row->LOTTO);
		print_label("etich1lotti.php", "Etichetta 88x36", $row->CODICEARTI, $row->LOTTO);
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
$Query = $Query . "VETTORI.TELEFONO, DOCTES.COLLI, DOCTES.PESOLORDO ";
$Query = $Query . "FROM DOCTES INNER JOIN DOCRIG ON DOCTES.ID = DOCRIG.ID_TESTA ";
$Query = $Query . "LEFT OUTER JOIN VETTORI ON DOCTES.VETTORE1 = VETTORI.CODICE ";
$Query = $Query . "WHERE DOCRIG.RIFFROMT = $id_testa "; 
$Query = $Query . "AND DOCTES.TIPODOC != \"PL\" "; 
$queryexe = db_query($connectionstring, $Query) or die(mysql_error()); 


print("<br>\n<h3 class=\"name\" style=\"text-align: center;\">" . $str_evasocon[$lang] . "</h3>\n");
du_table();
print(du_th() . $str_dataddt[$lang] . "</th>\n");
print(du_th() . $str_numero[$lang] . "</th>\n");
print(du_th() . $str_colli[$lang] . "</th>\n");
print(du_th() . $str_peso[$lang] . "</th>\n");
print(du_th() . $str_sped[$lang] . "</th>\n");
print(du_th() . $str_telefono[$lang] . "</th>\n");
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
     
    //format results 
    du_tr(); 
    print ("<td class=\"list\"><a href=\"ddt-detail.php?id=" . $row[3] . "\" >$name</a></td>\n"); 
    print ("<td class=\"list\">$addr</td>\n"); 
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
print(du_th() . $str_data[$lang] . "</th>\n");
print(du_th() . $str_numero[$lang] . "</th>\n");
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
print(du_th() . $str_tipo[$lang] . "</th>\n");
print(du_th() . $str_scadenza[$lang] . "</th>\n");
print(du_th() . $str_importo[$lang] . "</th>\n");
print(du_th() . $str_pagato[$lang] . "</th>\n");
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
