<?php
  /************************************************************************/
  /* Project ArcaWeb                               				        */
  /* ===========================                                          */
  /*                                                                      */
  /* Copyright (c) 2003-2016 by Roberto Ceccarelli                        */
  /* http://strawberryfield.altervista.org								*/
  /*                                                                      */
  /************************************************************************/

  include("header.php");
  include("db-utils.php");

  $conn = db_connect($dbase);

  include("../libs/distbase.php");

  $articolo = strtoupper($_GET['articolo']);
  $anno = current_year();

  $id_testa = 0;
  $quantita = $_GET['quantita'];
  $rif = $_GET['rif'];
  $copy = $_GET['copy'];
  $cliven = $_GET['codcli'];

  session_start();
  $cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
  $maga = "F" . substr($cookie[0],2);

  //print($maga);

  head();
  include("../libs/dropdown_lib.php");
?>
<script type="text/javascript">
  // <![CDATA[
  var OnSubmitForm = function() {
  	if(checkRows()){
  		if(document.pressed == 'Inserisci bolla') {
  			document.db.action = "creadoc.php";
  		} else {
  			if(document.pressed == 'Estrai dati') {
  				document.db.action = "creadocxml.php";
  			}
  		}
  		return true;
  	} else {
  	alert("Impossibile Procedere!\nCorreggere prima i Lotti con Giacenza non corretta!");
  	return false;
  	}
  };

  var checkGiac = function(n){
  	var qta = document.getElementById("qta"+n).value;
  	var temp = document.getElementById("lotto"+n);
  	var temp2 = temp.options[temp.selectedIndex].innerHTML;
  	var giacL = temp2.split(":");
  	if (qta > parseInt(giacL[1])){
  		document.getElementById("giaclot"+n).innerHTML = "Giacenza Lotto inferiore a Qta";
  		checkRows();
  		return false;
  	} else {
  		document.getElementById("giaclot"+n).innerHTML = "";
  		checkRows();
  		return true;
  	}
  };

  var checkRows = function(){
  	var rows = document.getElementById("tbl").rows.length - 1;
  	var i = 0;
  	var error = false;
  	var msg = "";
  	var n = 0;
  	while (i<rows && !error){
  		i++;
  		msg = document.getElementById("giaclot"+i).innerHTML;
  		n = msg.length;
  		if (n > 0){
  			error = true;
  		}
  	}
  	if (error == true){
  		showHideDiv(1, "mess");
  		//showHideDiv(0,"ok1");
  		return false;
  	} else {
  		showHideDiv(0,"mess");
  		//showHideDiv(1,"ok1");
  		return true;
  	}
  };

  var showHideDiv = function(bool,id) {
  	var elm = document.getElementById(id);
  	if (bool){
  		elm.style.display = "block" ;
  	} else {
  		elm.style.display = "none" ;
  	}
  	elm.innerHTML = elm.innerHTML;
  	return true;
  };
  // ]]>
</script>
<?php

  if (checkDistinta($articolo) ) {
  	$Query = "SELECT DESCRIZION FROM MAGART WHERE CODICE = '$articolo'";
  	$queryexe = db_query($conn, $Query) or die(mysql_error());
  	$row = mysql_fetch_object($queryexe);
  	$desc = $row->DESCRIZION;

  	$Query = "SELECT ESPLDISTIN FROM DOCRIG WHERE ID = $rif";
  	$queryexe = db_query($conn, $Query) or die(mysql_error());
  	$row = mysql_fetch_object($queryexe);
    //	print("$rif<br>" . $row->ESPLDISTIN);
  	if($row->ESPLDISTIN == "P") {
  		mostraDistintaEsplosa($articolo, $quantita, $desc, $_GET['lotto']);
  	} else {
  		mostraDistinta($articolo, $quantita, $desc, $_GET['lotto']);
  	}
  } else {
  	banner($articolo . " - ". _("Non trovato"));
  	print("<h2>" . _("Articolo") . " " . $articolo . " " . _("non ha distinta") . "</h2>\n");
  }


  print("<br>\n");
  goEdit("askdb.php",_("Nuova bolla"));
  print("<br>\n");
  goMain();
  footer();

  function tableHeader($articolo, $descrizion) {
    print("<script type=\"text/javascript\" src=\"../js/ajaxlib.js\"></script>\n");
    print("<script type=\"text/javascript\" src=\"../js/dbedit.js\"></script>\n");
    banner("$articolo - $descrizion");
  	// scrittura tabella con i dati trovati
    //	print("<form id=\"db\" name=\"db\" method=\"POST\" action=\"creadoc.php\">\n");
  	print("<form id=\"db\" name=\"db\" method=\"POST\" onsubmit=\"return OnSubmitForm();\">\n");
  	print("<table id=\"tbl\" class=\"list\">\n");
  	print("<thead>\n<tr class=\"list\">\n");
  	print("<th class=\"list\">" . _("Codice") . "</th>\n");
  	print("<th class=\"list\">" . _("Descrizione") . "</th>\n");
  	print("<th class=\"list\">" . _("U.M.") . "</th>\n");
  	print("<th class=\"list\">" . _("Qta") . "</th>\n");
  	print("<th class=\"list\">" . _("Lotto") . "</th>\n");
  	print("</tr>\n</thead>\n");
  	print("<tbody id=\"tblbody\">\n");
  }

  function tableFooter($nCompLen, $articolo, $quantita, $lottopadre, $idRiga, $rif, $warning) {
  	global $anno, $conn, $maga, $copy, $cliven;

  	print("</tbody>\n</table>\n");
  	hiddenField("count", $nCompLen);
  	hiddenField("padre", $articolo);
  	hiddenField("lottopadre", $lottopadre);
  	hiddenField("quantita", $quantita);
  	hiddenField("idriga", $idRiga);
  	hiddenField("rifr", $rif);
    hiddenField("cliven", $cliven);

  	$Query = "SELECT ID_TESTA FROM DOCRIG WHERE ID = $rif";
  	$rs = db_query($conn, $Query) or die(mysql_error());
  	$rw = mysql_fetch_object($rs);
  	hiddenField("rift", $rw->ID_TESTA);

  	hiddenField("idtesta", "");
  	hiddenField("numerodocf", $_GET['numero']);

	print("<div id=\"mess\" style=\"display: " . ($warning ? "block" : "none") . ";\">\n");
	print("<b>". _("CORREGERE I LOTTI CON GIACENZA INFERIORE ALLA QUANTITA' NECESSARIA") ."</b>\n</div>\n");
  	print("<div id=\"ok1\" style=\"display: block;\">\n");
	print("<input type=\"submit\" id=\"btnok\" value=\"" . _("Inserisci bolla") . "\" onclick=\"document.pressed=this.value;\">\n");
  	print("<input type=\"submit\" id=\"btnxml\" value=\"" . _("Estrai dati") . "\" onclick=\"document.pressed=this.value;\">\n</div>\n");

  	print("</form>\n");
  }

  function tableRow($j, $codice, $um, $consumo, $rifr) {
  	global $anno, $conn, $maga, $rif, $copy;

  	$consumo = xRound($consumo);
  	print("<tr class=\"list\" id=\"riga$j\">\n");
  	print("<td class=\"list\"><input type=\"text\" readonly=\"readonly\" size=\"16\" name=\"code$j\" id=\"code$j\" onclick=\"popup('dettagli',$j);\" value=\"$codice\"></td>\n");

  		// cerco l'ubicazione, la descrizione e le unità di misura con i fattori di conversione
  	$Query = <<<EOT
SELECT MAGART.DESCRIZION, MAGART.LOTTI, MAGART.UNMISURA, MAGART.UNMISURA1, 
MAGART.UNMISURA2, MAGART.UNMISURA3, MAGART.FATT1, MAGART.FATT2, MAGART.FATT3, MAGART.LOTTI 
FROM MAGART 
WHERE MAGART.CODICE = '$codice' 
EOT;
  	$rs = db_query($conn, $Query) or die(mysql_error());
  	$row = mysql_fetch_object($rs);
  	print("<td class=\"list\"><input type=\"hidden\" name=\"rifr$j\" id=\"rifr$j\" value=\"$rifr\">\n");
  	print("<span id=\"desc$j\">" . $row->DESCRIZION . "</span></td>\n");

  	// U.M.
  	$fatt = 1;
  	$umP = $row->UNMISURA;
  	$um1 = $row->UNMISURA1;
  	$um2 = $row->UNMISURA2;
  	$um3 = $row->UNMISURA3;
  	$fatt1 = $row->FATT1;
  	$fatt2 = $row->FATT2;
  	$fatt3 = $row->FATT3;
    $isLotto = $row->LOTTI;
  	if ( $um != $umP ){
  		if ($um == $um1){
  			$fatt = $fatt1;
  		} else {
  			if ($um == $um2){
  				$fatt = $fatt2;
  			} else {
  				if ($um == $um3){
  					$fatt = $fatt3;
  				}
  			}
  		}
  	}
  	print("<td class=\"list\" style=\"text-align: center;\"> <span style=\"font-size: 9pt;\">$umP</span></td>\n");

  	// Quantita
  	$consumo = $consumo * $fatt;
  	print("<td class=\"list\" style=\"text-align: center;\"><input readonly=\"readonly\" type=\"text\" size=\"10\" name=\"qta$j\" id=\"qta$j\" style=\"text-align: right;\" value=\"$consumo\"></td>\n");

  	$cnt=0;
  	$found=false;
	$varGiac=0;
	$varGiacTOT=0;
  	$warning = false;
    $giacSelected = 0;
  	print("<td class=\"list\">\n");
  	//		print(ddBox1("lotto$j"));
  	// la riga sopra serve a rendere editabile la combobox
  	// 29.05.2012 i lotti non sono pi� modificabili
  	print("<select name=\"lotto$j\" id=\"lotto$j\" onchange=\"return checkGiac($j);\">\n");

    if($isLotto){
	  // cerco tra i lotti se c'� qualcosa
	  //prima di tutto prendo la giacenza TOTALE dell'articolo
	  $Query = <<<EOT
SELECT GIACINI+PROGQTACAR-PROGQTASCA+PROGQTARET AS GIACENZA FROM MAGGIAC
WHERE ARTICOLO = '$codice'
AND MAGAZZINO = '$maga'
AND ESERCIZIO = '$anno'
EOT;
    	$rg = db_query($conn, $Query) or die(mysql_error());
    	if($rwg = mysql_fetch_object($rg)) {
			$varGiacTOT=xRound($rwg->GIACENZA+webMovs::giacWebMov($maga, $codice, ''));
		} else {
			$varGiacTOT=0;
		}
    	$Query = <<<EOT
SELECT MAGGIACL.PROGQTACAR-MAGGIACL.PROGQTASCA+MAGGIACL.PROGQTARET AS GIACENZA, MAGGIACL.LOTTO
,LOTTI.U_NOCE 
FROM MAGGIACL INNER JOIN LOTTI ON LOTTI.CODICE = MAGGIACL.LOTTO AND LOTTI.CODICEARTI = MAGGIACL.ARTICOLO
WHERE MAGGIACL.ARTICOLO = '$codice'
AND MAGGIACL.MAGAZZINO = '$maga'
ORDER BY LOTTO DESC
EOT;
    	$rs = db_query($conn, $Query) or die(mysql_error());
    	// se c'� un id per la copia dei lotti faccio la ricerca nella bolla indicata
    	if( $copy > 0) {
    		$Query = "SELECT LOTTO FROM U_BARDR WHERE CODICEARTI = '$codice' AND ID_TESTA = $copy";
    		$ls = db_query($conn, $Query) or die(mysql_error());
    		$rw = mysql_fetch_object($ls);
    	}

    	while ($row = mysql_fetch_object($rs))	{
    		if($row->GIACENZA > 0) {
    			$cnt++;
    			print("<option value=\"" . $row->LOTTO . "\"");
				$varGiac=xRound($row->GIACENZA+webMovs::giacWebMov($maga, $codice, $row->LOTTO));
    			if( $copy > 0) {
    				//if( $rw->LOTTO == $row->LOTTO)
    				if( ($rw->LOTTO == $row->LOTTO)  || ($rw->LOTTO == str_replace("-","",$row->LOTTO)) ) {
    					print(" selected=\"selected\"");
    					$found=true;
						$giacSelected = $varGiac;
    				}
    			} else {
    				if( $cnt == 1) {
    					print(" selected=\"selected\"");
						$giacSelected = $varGiac;
    				}
    			}
    			print(">" . $row->LOTTO . ($row->U_NOCE == 1 ? _(" (non CEE)") : "") . _(" - Giac.:") . $varGiac ." ".$umP."</option>\n");
    		}
    	}
    	// if($copy > 0 and !$found and $rw->LOTTO == "0") {
    	// 	//print("<option value=\"" . $rw->LOTTO . "\" selected=\"selected\">" . $rw->LOTTO . "</option>\n");
    	// 	$Query = "SELECT GIACINI+PROGQTACAR-PROGQTASCA+PROGQTARET AS GIACENZA FROM MAGGIAC ";
    	// 	$Query .= "WHERE ARTICOLO = \"$codice\" ";
    	// 	$Query .= "AND MAGAZZINO = \"$maga\" ";
    	// 	$Query .= "AND ESERCIZIO = \"$anno\" ";
    	// 	$rg = db_query($conn, $Query) or die(mysql_error());
    	// 	print("<option value=\"0\" selected=\"selected\">0 - " . _("Generato - Giac.:"));
    	// 	if($rwg = mysql_fetch_object($rg)) {
    	// 		$varGiac=xRound($rwg->GIACENZA+webMovs::giacWebMov($maga, $codice, ''));
    	// 		print($varGiac);
    	// 	} else {
    	// 		print(0);
    	// 	}
    	// 	print(" ".$umP."</option>\n");
    	// } else {
  		if($copy > 0 and !$found){
  			print("<option value=\"" . $rw->LOTTO . "\" selected=\"selected\">" . $rw->LOTTO . _(" - Giac.:") . " 0 ".$umP."</option>\n");
        $varGiac=0;
        $giacSelected = $varGiac;
  		}
  	   // }
    }else{
    	// non ho trovato nessun lotto, propongo uno '0'
    	// if( $cnt == 0 and $copy == 0 )
    		$Query = <<<EOT
SELECT GIACINI+PROGQTACAR-PROGQTASCA+PROGQTARET AS GIACENZA FROM MAGGIAC
WHERE ARTICOLO = '$codice'
AND MAGAZZINO = '$maga'
AND ESERCIZIO = '$anno'
EOT;
    		$rg = db_query($conn, $Query) or die(mysql_error());
    		print("<option value=\"\" selected=\"selected\">" . _("Giac.:"));
    		if($rwg = mysql_fetch_object($rg)) {
    			$varGiac=xRound($rwg->GIACENZA+webMovs::giacWebMov($maga, $codice, ''));
		  $giacSelected = $varGiac;
				$varGiacTOT = $varGiac;
    			print($varGiac);
    		} else {
    			print(0);
    		}
    		print( " ".$umP."</option>\n");
  	}

    //		print(ddBox2("lotto$j"));
  	print("</select>\n");
  	if ($consumo > $giacSelected){
  		print("<div id=\"giaclot$j\" style=\"font-size: 9pt; color: red;\">". _("Giacenza Lotto inferiore a Qta") ."</div>\n");
  		if (!$warning){
  			$warning = true;
  		}
  	} else {
  		print("<div id=\"giaclot$j\" style=\"font-size: 9pt; color: red;\"></div>\n");
	  }
	if ($isLotto && ($consumo>$varGiacTOT)){
		  print("<div id=\"giacTot$j\" style=\"font-size: 9pt; color: red;\">". _("ATTENZIONE! Giacenza TOT. Articolo NON sufficente.") ."</br>
			<a href='http://intranet.krona.it/krona/giacArtDetail.php?art=$codice&maga=$maga&esercizio=$anno' target='_blank'>Controlla Movimenti </a> </div>\n");
  		if (!$warning){
  			$warning = true;
  		}
  	} else {
  		print("<div id=\"giacTot$j\" style=\"font-size: 9pt; color: red;\"></div>\n");
	  }
  	print("</td>\n");

  	// chiusura riga
  	print("</tr>\n");
  	return $warning;
  }

  function mostraDistintaEsplosa($articolo, $quantita, $descrizion, $lottopadre, $idRiga) {
  	global $anno, $conn, $maga, $rif, $copy;

  	tableHeader($articolo,$descrizion);
  	$Query = "SELECT ID_TESTA FROM DOCRIG WHERE ID = $rif";
  	$rs = db_query($conn, $Query) or die(mysql_error());
  	$row = mysql_fetch_object($rs);
  	$id_testa = $row->ID_TESTA;
  	$Query = "SELECT CODICEARTI, QUANTITA, UNMISURA, ESPLDISTIN, ID FROM DOCRIG WHERE ID_TESTA = $id_testa AND ID >= $rif";
  	$rs = db_query($conn, $Query) or die(mysql_error());
  	$row = mysql_fetch_object($rs);
  	$fatt = $quantita / $row->QUANTITA;
  	$righe = 0;
  	$warning = false;
  	while($row = mysql_fetch_object($rs) and $row->ESPLDISTIN == "C") {
  		$righe++;
  		if( tableRow($righe, $row->CODICEARTI, $row->UM, $row->QUANTITA * $fatt, $row->ID) ) {
  			$warning = true;
  		}
  	}
  	tableFooter($righe, $articolo, $quantita, $lottopadre, $idRiga, $rif, $warning);
  }

  function mostraDistinta($articolo, $quantita, $descrizion, $lottopadre, $idRiga) {
  	global $anno, $conn, $maga, $rif, $copy;

    $aComp[] = array("codice" => "", "consumo" => 0, "um" => "");
  	tableHeader($articolo,$descrizion);

  	$Query = "SELECT DATADOC, U_DTESPLD FROM DOCRIG WHERE ID = $rif";
  	$rs = db_query($conn, $Query) or die(mysql_error());
  	$row = mysql_fetch_object($rs);
  	$nCompLen  = xEsplodi($articolo, $row->U_DTESPLD, $quantita, &$aComp, 0, 0);

  	$warning = false;
  	for($j = 1; $j <= $nCompLen; $j++) {
  		if( tableRow($j, $aComp[$j][codice], $aComp[$j][um], $aComp[$j][consumo], $rif) ) {
  			$warning = true;
  		}
  	}

  	tableFooter($nCompLen, $articolo, $quantita, $lottopadre, $idRiga, $rif, $warning);
  } // fine funzione mostraDistinta


?>
