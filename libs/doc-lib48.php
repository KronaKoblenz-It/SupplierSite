<?php
/************************************************************************/
/* CASASOFT ArcaWeb                               		       			*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2020 by Roberto Ceccarelli                        */
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

function print_label($link, $desc, $articolo, $lotto, $descart, $code, $cliven = "", $dest = "", $dEvas = "", $mode="CF") {
	print("<td class=\"list\" style=\"text-align: center;\">");
	if($articolo == "") {
		print("&nbsp;");
	} else {
		print("<a target=\"_blank\" href=\"$link?art=" . urlencode(trim($articolo)) . "&amp;lotto=" . urlencode(trim($lotto)));
		print("&amp;desc=" . urlencode(trim($descart)) . "&amp;code=" . urlencode(trim($code)) . "&amp;cliven=$cliven&amp;clidest=$dest&amp;devas=$dEvas&amp;mode=$mode");
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


function doc_rows($id, $connectionstring, $compAllowed = true) {
  global $lang, $str_codice, $str_desc, $str_um, $str_quantita, $str_prezzoun, $str_sconti, $str_totale;
  global $str_dataprevcons, $str_residuo, $str_dataevas ;

  $Query = "SELECT TIPODOC FROM DOCTES WHERE ID = $id";
  $queryexe = db_query($connectionstring, $Query);
  $row = db_fetch_row($queryexe);

  $isOF = in_array($row[0], array("FO", "LO", "OF", "OL", "OI", "GC", "OM", "OW", "MO", "WO", "AM"));
  $isBollaCL = in_array($row[0], array("BT", "CE", "RL", "TL", "CP", "BC"));
  $isOrd = ( $isOF or $row[0] == "OC");

  $Query = "SELECT DOCRIG.U_CLIVEN, ANAGRAFE.CODNAZIONE, IFNULL(DESTINAZ.U_NAZIONE,'') AS U_NAZIONE ";
    $Query .= "FROM DOCRIG INNER JOIN ANAGRAFE ON ANAGRAFE.CODICE = DOCRIG.U_CLIVEN ";
    $Query .= "LEFT OUTER JOIN DESTINAZ ON DESTINAZ.CODICECF = DOCRIG.U_CLIVEN AND DESTINAZ.CODICEDES = DOCRIG.U_DESTCLI ";
    $Query .= "WHERE DOCRIG.ID_TESTA = $id";
  $queryexe = db_query($connectionstring, $Query);
  $isIndia = false;
  $isCliPorta = false;
  while($row = mysql_fetch_object($queryexe)) {
    //	echo $row->CODNAZIONE . "<br>\n";
	  if(trim($row->CODNAZIONE) == "IN" || trim($row->U_NAZIONE) == "IN" || trim($row->U_CLIVEN) == "C07833" || trim($row->U_CLIVEN) == "C06000" || trim($row->U_CLIVEN) == "C07795") {
		  $isIndia = true;
	  }
	  if(trim($row->U_CLIVEN) == "C02068") {
		  $isCliPorta = true;
	  }
  }

  $Query = "SELECT MAGART.U_NCOLLI FROM DOCRIG INNER JOIN MAGART ON MAGART.CODICE = DOCRIG.CODICEARTI WHERE DOCRIG.ID_TESTA = $id";
  $queryexe = db_query($connectionstring, $Query);
  $isCollo2 = false;
  while($row = mysql_fetch_object($queryexe)) {
    if($row->U_NCOLLI > 1) {
		  $isCollo2 = true;
	  }
  }

  du_table();
  if( $isOrd ) {
	  du_th("R");
  }
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
  	du_th("89x48 CF");
  	du_th("89x48 SC");
    du_th("89x48 PZ");
  	if($isIndia) {
  	  du_th("INDIA PZ");
      du_th("INDIA CF");
      du_th("INDIA SC");
      du_th("INDIA 2nd Collo");
 	}
	if($isCliPorta) {
		du_th("Cli. PORTA");
  	}
  	if($isCollo2) {
  		du_th("88x36\n2nd Collo");
  	}
  	du_th("Cod. Cli.");
    du_th("Cli. Vend.");
  }
  print("</tr>\n");


  //SQL quyery
  $Query = "SELECT DOCRIG.DATADOC, DOCRIG.NUMERODOC, DOCRIG.CODICEARTI, DOCRIG.DESCRIZION, DOCRIG.UNMISURA, DOCRIG.QUANTITA, DOCRIG.QUANTITARE,";
    $Query .= " DOCRIG.PREZZOUN, DOCRIG.SCONTI, DOCRIG.PREZZOTOT, DOCRIG.ID_TESTA, DOCRIG.DATAINIZIO AS DATACONSEG, DOCRIG.NUMERORIGA, ";
    $Query .= " DOCRIG.U_FM, DOCRIG.U_MISURAH, DOCRIG.U_MISURAL, DOCRIG.U_MISURAS, DOCRIG.U_INGOMH, DOCRIG.U_INGOML,";
    $Query .= " DOCRIG.U_CASSSX, DOCRIG.U_CASSDX, DOCRIG.LOTTO, DOCRIG.ID, DOCRIG.DATACONSEG AS DATAINIZIO, DOCRIG.U_CLIVEN, DOCRIG.U_DESTCLI, ";
    $Query .= " CODALT.CODARTFOR, CODALT.U_BARCODE, CODALT.U_BARCODE1, CODALT.U_BARCODE2, CODALT.U_BARCODE3, ANAGRAFE.CODNAZIONE, MAGART.U_NCOLLI, ";
	$Query .= " MAGART.UNMISURA1, MAGART.UNMISURA2, MAGART.UNMISURA3, MAGART.UNMISURA AS UNMISURA0 ";
    $Query .= " FROM DOCRIG LEFT OUTER JOIN CODALT ON CODALT.CODICEARTI = DOCRIG.CODICEARTI AND CODALT.CODCLIFOR = DOCRIG.U_CLIVEN";
    $Query .= " LEFT OUTER JOIN ANAGRAFE ON ANAGRAFE.CODICE = DOCRIG.u_CLIVEN";
    $Query .= " LEFT OUTER JOIN MAGART ON MAGART.CODICE = DOCRIG.CODICEARTI";
    $Query .= " WHERE ID_TESTA = " . $id;
  if(!$compAllowed) {
  	$Query .= " and DOCRIG.ESPLDISTIN != \"C\"";
  }
  $queryexe = db_query($connectionstring, $Query);
  //query database
  while($row = mysql_fetch_object($queryexe))  {
    $id_testa = $row->ID_TESTA;
    //format results
    $isWurth = false;
    // if(in_array(trim($row->U_CLIVEN), array("C01166", "C07211", "C07221", "C07882", "C07539", "C08440", "C07901", "C07386", "C07220", "C02405", "C05264", "C07219", "C09041", "C07214", "C08491", "C07393", "C07209", "C07216", "C07217", "C07215", "C07224", "C07222", "C07213", "C072120", "C07218"))){
    //   $isWurth = true;
    // }
	  du_tr();
	  if( $isOrd ) {
      print("<td class=\"list\" style=\"text-align: center;\">" . $row->NUMERORIGA . "</td>\n");
	  }
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
	  
	  // useful vars
	  $unmisura[0] = $row->UNMISURA0;
	  $unmisura[1] = $row->UNMISURA1;
	  $unmisura[2] = $row->UNMISURA2;
	  $unmisura[3] = $row->UNMISURA3;
	  
	  $art = (is_null($row->CODARTFOR) ? $row->CODICEARTI : $row->CODARTFOR);
	  $barcode = (is_null($row->U_BARCODE) ? "" : $row->U_BARCODE);
	  $barcode[1] = (is_null($row->U_BARCODE1) ? "" : $row->U_BARCODE1);
	  $barcode[2] = (is_null($row->U_BARCODE2) ? "" : $row->U_BARCODE2);
	  $barcode[3] = (is_null($row->U_BARCODE3) ? "" : $row->U_BARCODE3);
	  $barcode[0] = $barcode;
	  
	  for($j = 1; $j <= 3; $j++)
		$barcode[$j] = ($barcode[$j] == "" ? $barcode : $barcode[$j]);
	  
	  // riassegnamo il barcode sulla base delle unità di misura
	  for($j = 0; $j <= 3; $j++)
		if($unmisura[$j] == "CF")
		  $barcodeCF = $barcode[$j];
	  for($j = 0; $j <= 3; $j++)
		if($unmisura[$j] == "SC")
		  $barcodeSC = $barcode[$j];
	  for($j = 0; $j <= 3; $j++)
		if($unmisura[$j] == "PZ")
		  $barcodePZ = $barcode[$j];
	  
	  if( $isOF ) {
		if($row->U_CLIVEN == "C05332") {
			print_label("eticha4.php", "Etichette A4", '');
		} else {			
			print_label("eticha4.php", "Etichette A4", $art, $row->LOTTO, $row->DESCRIZION, $barcode);
		}
		if($isWurth){
			print_label("etich1lotti_wurth.php", "Etich. Wurth 88x36", $art, $row->LOTTO, $row->DESCRIZION, $barcodeSC, $row->U_CLIVEN, $row->U_DESTCLI);
				print("<td class=\"list\" style=\"text-align: center;\">&nbsp;</td>\n");
				print("<td class=\"list\" style=\"text-align: center;\">&nbsp;</td>\n");
		 } else {
			if($row->U_CLIVEN == "C05332") {
				// Etichette onward
				print_label("etich-onward.php", "Etichetta OnWard 110x73", $art, $row->LOTTO, $row->DESCRIZION, $barcode, $row->U_CLIVEN, $row->U_DESTCLI);
				print_label("etich-onward.php", "Etichetta OnWard 110x73 - Scatole", $art, $row->LOTTO, $row->DESCRIZION, $barcode, $row->U_CLIVEN, $row->U_DESTCLI, "", "SC");
				print_label("etich-onward.php", "Etichetta OnWard 110x73 - Pezzi", "", $row->LOTTO, $row->DESCRIZION, $barcode, $row->U_CLIVEN, $row->U_DESTCLI, "", "PZ");	
			} else {
				print_label("etich48.php", "Etichetta 88x36", $art, $row->LOTTO, $row->DESCRIZION, $barcodeCF, $row->U_CLIVEN, $row->U_DESTCLI);
				print_label("etich48.php", "Etichetta 88x36 - Scatole", $art, $row->LOTTO, $row->DESCRIZION, $barcodeSC, $row->U_CLIVEN, $row->U_DESTCLI, "", "SC");
				print_label("etich48.php", "Etichetta 88x36 - Pezzi", $art, $row->LOTTO, $row->DESCRIZION, $barcodePZ, $row->U_CLIVEN, $row->U_DESTCLI, "", "PZ");					
			}
		}
		if($isIndia) {
			if(trim($row->CODNAZIONE) == "IN" || trim($row->U_CLIVEN) == "C07833" || trim($row->U_CLIVEN) == "C06000" || trim($row->U_CLIVEN) == "C07795") {
				print_label("etich_india_PZ.php", "110x73 Etich INDIA PZ", $row->CODICEARTI, $row->LOTTO, $row->DESCRIZION, $barcode, $row->U_CLIVEN, $row->U_DESTCLI, $row->DATACONSEG);
				print_label("etich_india_CF.php", "110x73 Etich INDIA CS", $row->CODICEARTI, $row->LOTTO, $row->DESCRIZION, $barcode, $row->U_CLIVEN, $row->U_DESTCLI, $row->DATACONSEG);
				print_label("etich_india_SC.php", "110x73 Etich INDIA SC", $row->CODICEARTI, $row->LOTTO, $row->DESCRIZION, $barcode, $row->U_CLIVEN, $row->U_DESTCLI, $row->DATACONSEG);
			} else {
				print("<td class=\"list\" style=\"text-align: center;\">&nbsp;</td>\n");
				print("<td class=\"list\" style=\"text-align: center;\">&nbsp;</td>\n");
				print("<td class=\"list\" style=\"text-align: center;\">&nbsp;</td>\n");
			 }
			if($isCollo2 && ($row->U_NCOLLI > 1)) {
				print_label("etich_india_collo2.php", "110x73 Etich INDIA 2collo", $row->CODICEARTI, $row->LOTTO, $row->DESCRIZION, $barcode, $row->U_CLIVEN, $row->U_DESTCLI, $row->DATACONSEG);
			} else {
				print("<td class=\"list\" style=\"text-align: center;\">&nbsp;</td>\n");
			}
		}
		if($row->U_CLIVEN == "C02068") {
			// Etichette Porta
			print_label("etich-porta.php", "Etichetta Porta 36x88", $art, $row->LOTTO, $row->DESCRIZION, $barcode, $row->U_CLIVEN, $row->U_DESTCLI);
		} 
		if($isCollo2 && ($row->U_NCOLLI > 1)) print_label("etich1lotti_unificate_collo2.php", "Etichetta per 2°Collo", $art, $row->LOTTO, $row->DESCRIZION, $barcode, $row->U_CLIVEN);
		if (in_array($row->CODICEARTI, array("5000 KND 100", "5000 KND 125", "5001 KND 100", "5001 KND 125"))){
		    print_label("etichcodalta4.php", "Etichetta Cod. Alt.", $art, $row->LOTTO, $row->DESCRIZION, $barcode);
		}
			  //if (in_array($row->CODICEARTI, array("5400 100 0860", "5400 100"))){
				//  print_label("etichcodaltFRa4.php", "Etichetta Cod. Alt.", $art, $row->LOTTO, $row->DESCRIZION, $barcode);
			  //}
	  }
	  if( $isBollaCL ) {
		  if($row->U_CLIVEN == "C05332") {
			  // Etichette onward
			  print_label("eticha4lotti.php", "Etichette A4", "", "", "", "");
			  print_label("etich-onward.php", "Etichetta OnWard 110x73", $art, $row->LOTTO, $row->DESCRIZION, $barcode, $row->U_CLIVEN);
			  print_label("etich-onward.php", "Etichetta OnWard 110x73 - Scatole", $art, $row->LOTTO, $row->DESCRIZION, $barcode, $row->U_CLIVEN, "", "", "SC");
			  print_label("etich-onward.php", "Etichetta OnWard 110x73 - Pezzi", "", $row->LOTTO, $row->DESCRIZION, $barcode, $row->U_CLIVEN, "", "", "PZ");			  
		  } else {
			  print_label("eticha4lotti.php", "Etichette A4", $art, $row->LOTTO, $row->DESCRIZION, $barcode);
			  print_label("etich48.php", "Etichetta 88x36", $art, $row->LOTTO, $row->DESCRIZION, $barcodeCF, $row->U_CLIVEN);
			  print_label("etich48.php", "Etichetta 88x36 - Scatole", $art, $row->LOTTO, $row->DESCRIZION, $barcodeSC, $row->U_CLIVEN, "", "", "SC");
			  print_label("etich48.php", "Etichetta 88x36 - Pezzi", $art, $row->LOTTO, $row->DESCRIZION, $barcodePZ, $row->U_CLIVEN, "", "", "PZ");
		  }
		  if($isIndia) print_label("etich_india.php", "Etichetta per INDIA", $row->CODICEARTI, $row->LOTTO, $row->DESCRIZION, $barcode, $row->U_CLIVEN, $row->DATACONSE);
		  if($row->U_CLIVEN == "C02068") {
				// Etichette Porta
				print_label("etich-porta.php", "Etichetta Porta 36x88", $art, $row->LOTTO, $row->DESCRIZION, $barcode, $row->U_CLIVEN, $row->U_DESTCLI);
			} else {
				print("<td class=\"list\" style=\"text-align: center;\">&nbsp;</td>\n");			
			}
		  if($isCollo2) print_label("etich1lotti_unificate_collo2.php", "Etichetta per 2°Collo", $art, $row->LOTTO, $row->DESCRIZION, $barcode, $row->U_CLIVEN);			  
    }
	  if( $isOF or $isBollaCL ) {
		  print ("<td class=\"list\" style=\"text-align: center;\">" . (is_null($row->CODARTFOR) ? "&nbsp;" : $row->CODARTFOR) . "</td>\n");
      print ("<td class=\"list\" style=\"text-align: left;\">" . $row->U_CLIVEN . "</td>\n");
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

  $Query =  <<<EOT
SELECT DISTINCT DOCTES.DATADOC, DOCTES.NUMERODOC, VETTORI.DESCRIZION, DOCTES.ID, 
DOCTES.DATADOCFOR, DOCTES.NUMERODOCF, 
VETTORI.TELEFONO, DOCTES.COLLI, DOCTES.PESOLORDO, DOCTES.TIPODOC 
,DOCTES.VETTORE1, DOCTES.PATRASF AS TRACKING
FROM DOCTES INNER JOIN DOCRIG ON DOCTES.ID = DOCRIG.ID_TESTA 
LEFT OUTER JOIN VETTORI ON DOCTES.VETTORE1 = VETTORI.CODICE 
WHERE DOCRIG.RIFFROMT = $id_testa 
AND DOCTES.TIPODOC != 'PL'
EOT;
  $queryexe = db_query($connectionstring, $Query) or die(mysql_error());

  print("<br>\n<h3 class=\"name\" style=\"text-align: center;\">" . $str_evasocon[$lang] . "</h3>\n");
  du_table();
  du_th($str_dataddt[$lang]);
  du_th($str_numero[$lang]);
  du_th($str_colli[$lang]);
  du_th($str_peso[$lang]);
  du_th($str_sped[$lang]);
  du_th($str_telefono[$lang]);
  du_th("&nbsp;");

  print("</tr>\n");

  $ut = userType();

  //query database
  while($row = mysql_fetch_object($queryexe)) {
    $data = format_date($ut == "F" ? $row->DATADOCFOR : $row->DATADOC);
    $numero = ($ut == "F" ? $row->NUMERODOCF : $row->NUMERODOC);
    $vettore = $row->DESCRIZION;
    $id = $row->ID;
    $telefono = $row->TELEFONO;
    $colli = $row->COLLI;
    $peso = $row->PESO;
	$tipodoc = $row->TIPODOC;

	$tracking = "&nbsp;";
	// gestione tracking
	$id_collo = trim($row->TRACKING);
	if($dbase == "krona" && $ut != "F" && $id_collo != "") {
		switch (trim($row->VETTORE1)) {
			case "10":
				// Bartolini
				$tracking="<a target=\"_blank\" href=\"https://vas.brt.it/vas/sped_det_show.hsm?referer=sped_numspe_par.htm&ChiSono=$id_collo&ClienteMittente=&DataInizio=&DataFine=&RicercaChiSono=Ricerca\">Tracking</a>";
				break;
			case "Z01":
				// GLS
				$tracking="<a target=\"_blank\" href=\"https://www.gls-italy.com/?option=com_gls&view=track_e_trace&mode=search&numero_spedizione=$id_collo&tipo_codice=id_collo\">Tracking</a>";
				break;
		}  
	}

    //format results
    du_tr();
	$text = <<<EOT
<td class="list"><a href="ddt-detail.php?id=$id" >$data</a></td>
<td class="list">$tipodoc $numero</td>
<td class="list" style="text-align: right;">$colli</td>
<td class="list" style="text-align: right;">$peso</td>
<td class="list">$vettore</td>
<td class="list">$telefono</td>
<td class="list">$tracking</td>
</tr>\n");
EOT;
	print("$text\n");
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
  while($row = db_fetch_row($queryexe)) {
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
  while($row = db_fetch_row($queryexe)) {
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

// -------------------------------------------
// DOC Conto Deposito CD
// -------------------------------------------
function cd_rows($id, $connectionstring, $compAllowed = true) {
	global $lang, $str_codice, $str_desc, $str_um, $str_quantita, $str_prezzoun, $str_sconti, $str_totale;
	global $str_dataprevcons, $str_residuo, $str_dataevas ;
	du_table();

	du_th("R");
	du_th($str_codice[$lang]);
	du_th($str_desc[$lang]);
	du_th($str_um[$lang]);
	du_th($str_quantita[$lang]);
	du_th($str_residuo[$lang]);
	du_th("Qta PZ");
	du_th($str_prezzoun[$lang]);
	du_th($str_sconti[$lang]);
	du_th($str_totale[$lang]);

	print("</tr>\n");


	//SQL quyery
	$Query = "SELECT DOCRIG.DATADOC, DOCRIG.NUMERODOC, DOCRIG.CODICEARTI, DOCRIG.DESCRIZION, DOCRIG.UNMISURA, DOCRIG.QUANTITA, DOCRIG.QUANTITARE,";
	$Query .= " DOCRIG.PREZZOUN, DOCRIG.SCONTI, DOCRIG.PREZZOTOT, DOCRIG.ID_TESTA, DOCRIG.DATACONSEG, DOCRIG.NUMERORIGA, DOCRIG.OMMERCE, ";
	$Query .= " MAGART.UNMISURA2, MAGART.FATT2, MAGART.UNMISURA3, MAGART.FATT3, DOCTES.SCONTI AS SCONTOMERCE ";
	$Query .= " FROM DOCRIG INNER JOIN DOCTES ON DOCTES.ID = DOCRIG.ID_TESTA ";
	$Query .= " LEFT OUTER JOIN MAGART ON MAGART.CODICE = DOCRIG.CODICEARTI";
	$Query .= " WHERE DOCRIG.ID_TESTA = " . $id;
	$queryexe = db_query($connectionstring, $Query);

	//query database
	while($row = mysql_fetch_object($queryexe))  {
		$id_testa = $row->ID_TESTA;
		$qtaPZ = ($row->UNMISURA!='PZ' ? ($row->UNMISURA2!='PZ' ? ($row->UNMISURA3!='PZ' ? "" : ($row->QUANTITA/$row->FATT3) ) : ($row->QUANTITA/$row->FATT2) ) : "");

    //format results
		du_tr();
		print("<td class=\"list\" style=\"text-align: center;\">" . $row->NUMERORIGA . "</td>\n");
		print ("<td class=\"list\">" . $row->CODICEARTI . "</td>\n");
		print ("<td class=\"list\">" . $row->DESCRIZION . "</td>\n");
		print ("<td class=\"list\" style=\"text-align: center;\">" . $row->UNMISURA . "</td>\n");
		print ("<td class=\"list\" style=\"text-align: right;\">" . $row->QUANTITA . "</td>\n");
		print ("<td class=\"list\" style=\"text-align: right;\">" . $row->QUANTITARE . "</td>\n");
		print ("<td class=\"list\" style=\"text-align: right;\">" . xRound2($qtaPZ) . "</td>\n");
		if(!$row->OMMERCE){
			print ("<td class=\"list\" style=\"text-align: right;\">" . $row->PREZZOUN . "</td>\n");
			print ("<td class=\"list\" style=\"text-align: center;\">" . $row->SCONTOMERCE . "</td>\n");
			print ("<td class=\"list\" style=\"text-align: right;\">" . xRound2($row->PREZZOTOT * (100 - $row->SCONTOMERCE) / 100 ) . "</td>\n");
		} else {
			print ("<td class=\"list\" colspan='3' style=\"text-align: center;\"><strong>OMMAGGIO</strong></td>\n");
		}
		print ("</tr>\n");
	}

	print ("</table>\n");
	return $id_testa;
}

// -------------------------------------------
// DOC Conto Deposito CP
// -------------------------------------------
function cp_rows($id, $connectionstring, $compAllowed = true) {
	global $lang, $str_codice, $str_desc, $str_um, $str_quantita, $str_prezzoun, $str_sconti, $str_totale;
	global $str_dataprevcons, $str_residuo, $str_dataevas ;
	du_table();

	du_th("R");
	du_th("Tipo");
	du_th($str_codice[$lang]);
	du_th($str_desc[$lang]);
	du_th($str_um[$lang]);
	du_th($str_quantita[$lang]);
	du_th($str_residuo[$lang]);
	du_th("Qta PZ");
	du_th($str_prezzoun[$lang]);
	du_th($str_sconti[$lang]);
	du_th($str_totale[$lang]);

	print("</tr>\n");

	//SQL quyery
	$Query = "SELECT DOCRIG.DATADOC, DOCRIG.NUMERODOC, DOCRIG.CODICEARTI, DOCRIG.DESCRIZION, DOCRIG.UNMISURA, DOCRIG.QUANTITA, DOCRIG.QUANTITARE,";
	$Query .= " DOCRIG.PREZZOUN, DOCRIG.SCONTI, DOCRIG.PREZZOTOT, DOCRIG.ID_TESTA, DOCRIG.DATACONSEG, DOCRIG.NUMERORIGA, DOCRIG.ESPLDISTIN, ";
	$Query .= " MAGART.UNMISURA2, MAGART.FATT2, MAGART.UNMISURA3, MAGART.FATT3 ";
	$Query .= " FROM DOCRIG ";
	$Query .= " LEFT OUTER JOIN MAGART ON MAGART.CODICE = DOCRIG.CODICEARTI";
	$Query .= " WHERE ID_TESTA = " . $id;
	$queryexe = db_query($connectionstring, $Query);

	//query database
	while($row = mysql_fetch_object($queryexe))  {
		$id_testa = $row->ID_TESTA;
		$qtaPZ = ($row->UNMISURA!='PZ' ? ($row->UNMISURA2!='PZ' ? ($row->UNMISURA3!='PZ' ? "" : ($row->QUANTITA/$row->FATT3) ) : ($row->QUANTITA/$row->FATT2) ) : "");

    //format results
		du_tr();
		print("<td class=\"list\" style=\"text-align: center;\">" . $row->NUMERORIGA . "</td>\n");
		print("<td class=\"list\" style=\"text-align: center;\">" . $row->ESPLDISTIN . "</td>\n");
		print ("<td class=\"list\">" . $row->CODICEARTI . "</td>\n");
		print ("<td class=\"list\">" . $row->DESCRIZION . "</td>\n");
		print ("<td class=\"list\" style=\"text-align: center;\">" . $row->UNMISURA . "</td>\n");
		print ("<td class=\"list\" style=\"text-align: right;\">" . $row->QUANTITA . "</td>\n");
		print ("<td class=\"list\" style=\"text-align: right;\">" . $row->QUANTITARE . "</td>\n");
		print ("<td class=\"list\" style=\"text-align: right;\">" . xRound2($qtaPZ) . "</td>\n");
		print ("<td class=\"list\" style=\"text-align: right;\">" . $row->PREZZOUN . "</td>\n");
		print ("<td class=\"list\" style=\"text-align: center;\">" . $row->SCONTI . "</td>\n");
		print ("<td class=\"list\" style=\"text-align: right;\">" . $row->PREZZOTOT . "</td>\n");
		print ("</tr>\n");
	}
	print ("</table>\n");
	return $id_testa;
}

?>
