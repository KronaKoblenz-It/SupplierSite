<?php
  /************************************************************************/
  /* Project ArcaWeb                               				          */
  /* ===========================                                          */
  /*                                                                      */
  /* Copyright (c) 2003-2019 by Roberto Ceccarelli                        */
  /* http://strawberryfield.altervista.org								  */
  /*                                                                      */
  /************************************************************************/

  include("header.php");
  include("db-utils.php");

  $conn = db_connect($dbase);

  include("../libs/distbase.php");

  $articolo = strtoupper($_GET['articolo']);
  $anno = current_year();

  $id_testa = 0;
  $quantita = isset($_GET['quantita']) ? $_GET['quantita'] : 1;
  $gruppo = isset($_GET["gruppo"]) ? $_GET["gruppo"] : "";

  session_start();
  $cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
  $maga = "F" . substr($cookie[0],2);

  head();

  if (checkDistinta($articolo) ) {
  	$Query = "SELECT DESCRIZION FROM MAGART WHERE CODICE = '$articolo'";
  	$queryexe = db_query($conn, $Query) or die(mysql_error());
  	$row = mysql_fetch_object($queryexe);
  	$desc = $row->DESCRIZION;

  	mostraDistinta($articolo, $quantita, $desc);
  } else {
  	banner($articolo . " - ". _("Non trovato"));
  	print("<h2>" . _("Articolo") . " " . $articolo . " " . _("non ha distinta") . "</h2>\n");
  }


  print("<br>\n");
  print("<br>\n");
  goMain();
  footer();

// ----------- funzioni ---------------------

  function tableHeader($articolo, $descrizion) {
    banner("$articolo - $descrizion");
  	print("<table id=\"tbl\" class=\"list\">\n");
  	print("<thead>\n<tr class=\"list\">\n");
  	print("<th class=\"list\">" . _("Liv") . "</th>\n");
  	print("<th class=\"list\">" . _("Tipo") . "</th>\n");
  	print("<th class=\"list\">" . _("Codice") . "</th>\n");
  	print("<th class=\"list\">" . _("Descrizione") . "</th>\n");
  	print("<th class=\"list\">" . _("U.M.") . "</th>\n");
  	print("<th class=\"list\">" . _("Qta") . "</th>\n");
  	print("</tr>\n</thead>\n");
  	print("<tbody id=\"tblbody\">\n");
  }

  function tableFooter() {

  	print("</tbody>\n</table>\n");
  }

  function tableRow($j, $codice, $um, $consumo, $liv, $tipo) {
  	global $anno, $conn;

	$warning = false;
  	$consumo = xRound($consumo);
  	print("<tr class=\"list\" id=\"riga$j\">\n");
	print("<td class=\"list\">$liv</td>\n");
	print("<td class=\"list\">$tipo</td>\n");
  	print("<td class=\"list\">");
	for($k = 1; $k < $liv; $k++) {
		print("&nbsp;&nbsp;&nbsp;");
	}
	print("\n<input type=\"text\" readonly=\"readonly\" size=\"16\" name=\"code$j\" id=\"code$j\" value=\"$codice\">");
	print("</td>\n");

  		// cerco l'ubicazione, la descrizione e le unità di misura con i fattori di conversione
  	$Query = <<<EOT
SELECT MAGART.DESCRIZION, MAGART.LOTTI, MAGART.UNMISURA, MAGART.UNMISURA1, 
MAGART.UNMISURA2, MAGART.UNMISURA3, MAGART.FATT1, MAGART.FATT2, MAGART.FATT3 
FROM MAGART 
WHERE MAGART.CODICE = '$codice' 
EOT;
  	$rs = db_query($conn, $Query) or die(mysql_error());
  	$row = mysql_fetch_object($rs);
  	print("<td class=\"list\">\n");
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


  	print("</td>\n");

  	// chiusura riga
  	print("</tr>\n");
  	return $warning;
  }

  function mostraDistinta($articolo, $quantita, $descrizion) {
  	global $anno, $conn;

    $aComp[] = array("codice" => "", "consumo" => 0, "um" => "");
  	tableHeader($articolo,$descrizion);

  	$nCompLen  = xScorri($articolo, date("Y-m-d"), $quantita, &$aComp, 0, 0);

  	$warning = false;
  	for($j = 1; $j <= $nCompLen; $j++) {
  		if( tableRow($j, $aComp[$j][codice], $aComp[$j][um], $aComp[$j][consumo],  $aComp[$j][liv], 
			$aComp[$j][tipoparte] == "T" ? "Fitt." : $aComp[$j][tipoparte] == "F" ? "Fant." : "") ) {
  			$warning = true;
  		}
  	}

  	tableFooter();
  } // fine funzione mostraDistinta


?>
