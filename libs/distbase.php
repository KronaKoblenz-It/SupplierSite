<?php
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2019 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/


function checkDistinta($articolo) {
	global $conn;

	$Query = "SELECT CODPADRE FROM DISTBASE WHERE CODPADRE = '$articolo'";
	$queryexe = db_query($conn, $Query) or die(mysql_error());
	return (mysql_num_rows($queryexe) > 0);
}


function xEsplodi($codPadre, $dValida, $nQta, $aComp, $nCompLen, $nLevel)
{
	global $conn;

	$nLevel += 1;
	if($nlevel > 10) {
	   print("<h2>Troppi livelli - probabile ricorsione</h2>");
	}

	$Query = "SELECT CODCOMP, UNMISURA, QUANTITA, TIPOPARTE, DATAINIVAL, DATAFINVAL, FATT, QUANTALT ";
	$Query .= "FROM DISTBASE WHERE CODPADRE='$codPadre' ORDER BY NUMERORIGA";
	$rs = db_query($conn, $Query) or die(mysql_error());
	while($row = mysql_fetch_object($rs))
	{
		//        echo "DataIniVal = " . $row->DATAINIVAL;
		//        echo "DataFinVal = " . $row->DATAFINVAL;
		$nConsumo = ($row->QUANTALT) * $nQta;
		if( $dValida == "" ) {
			$today=strtotime(date("Y-m-d"));
		} else {
			$today=strtotime($dValida);
		}
		if( floatval($row->DATAINIVAL) == 0 ) {
			 $inidate = strtotime("-10000 day");
		} else {
			 $inidate = strtotime(str_replace("/","-",$row->DATAINIVAL));
		}
		if( floatval($row->DATAFINVAL) == 0 ) {
			 $findate = strtotime("+1000 day");
		} else {
			 $findate = strtotime(str_replace("/","-",$row->DATAFINVAL));
		}
		// print_r($row->CODCOMP . " - IniDate:" . $inidate . " - FinDate" . $findate . " - Today:" . $today . " - TipoParte: " . $row->TIPOPARTE . " /");
		// print("$findate - ");
		if ($findate >= $today and $inidate <= $today) {
			switch($row->TIPOPARTE ) {
				case "T":
					// fittizio: non faccio nulla
					break;
				case "F":
					// fantasma: scendo di un livello
					$nCompLen = xEsplodi($row->CODCOMP, $dValida, $nConsumo, &$aComp, $nCompLen, $nLevel);
					break;
				case "N":
				case "":
					$found = false;
					for($j=1; $j <= $nCompLen; $j++) {
						if( $aComp[$j][codice] == $row->CODCOMP) {
							$found = true;
							break;
						}
					}
					if( $found and $nConsumo <= 0) {
						$aComp[$j][consumo] += $nConsumo;
					} else {
						$nCompLen += 1;
						$aComp[$nCompLen] = array("codice" => $row->CODCOMP,
							"consumo" => $nConsumo,
							"um" => $row->UNMISURA );
					}
					// normale: archivio
				break;
			}
		}
		//		$rs->MoveNext();
	}

	return $nCompLen;
}


// ROBERTO 17.06.2020
// Esplodi distinta senza raggruppamento articoli (Vaccari)
function xEsplodiNR($codPadre, $dValida, $nQta, $aComp, $nCompLen, $nLevel)
{
	global $conn;

	$nLevel += 1;
	if($nlevel > 10) {
	   print("<h2>Troppi livelli - probabile ricorsione</h2>");
	}

	$Query = "SELECT CODCOMP, UNMISURA, QUANTITA, TIPOPARTE, DATAINIVAL, DATAFINVAL, FATT, QUANTALT ";
	$Query .= "FROM DISTBASE WHERE CODPADRE='$codPadre' ORDER BY NUMERORIGA";
	$rs = db_query($conn, $Query) or die(mysql_error());
	while($row = mysql_fetch_object($rs))
	{
		//        echo "DataIniVal = " . $row->DATAINIVAL;
		//        echo "DataFinVal = " . $row->DATAFINVAL;
		$nConsumo = ($row->QUANTALT) * $nQta;
		if( $dValida == "" ) {
			$today=strtotime(date("Y-m-d"));
		} else {
			$today=strtotime($dValida);
		}
		if( floatval($row->DATAINIVAL) == 0 ) {
			 $inidate = strtotime("-10000 day");
		} else {
			 $inidate = strtotime(str_replace("/","-",$row->DATAINIVAL));
		}
		if( floatval($row->DATAFINVAL) == 0 ) {
			 $findate = strtotime("+1000 day");
		} else {
			 $findate = strtotime(str_replace("/","-",$row->DATAFINVAL));
		}
		// print_r($row->CODCOMP . " - IniDate:" . $inidate . " - FinDate" . $findate . " - Today:" . $today . " - TipoParte: " . $row->TIPOPARTE . " /");
		// print("$findate - ");
		if ($findate >= $today and $inidate <= $today) {
			switch($row->TIPOPARTE ) {
				case "T":
					// fittizio: non faccio nulla
					break;
				case "F":
					// fantasma: scendo di un livello
					$nCompLen = xEsplodiNR($row->CODCOMP, $dValida, $nConsumo, &$aComp, $nCompLen, $nLevel);
					break;
				case "N":
				case "":
					$found = false;
					// for($j=1; $j <= $nCompLen; $j++) {
						// if( $aComp[$j][codice] == $row->CODCOMP) {
							// $found = true;
							// break;
						// }
					// }
					if( $found and $nConsumo <= 0) {
						$aComp[$j][consumo] += $nConsumo;
					} else {
						$nCompLen += 1;
						$aComp[$nCompLen] = array("codice" => $row->CODCOMP,
							"consumo" => $nConsumo,
							"um" => $row->UNMISURA );
					}
					// normale: archivio
				break;
			}
		}
		//		$rs->MoveNext();
	}

	return $nCompLen;
}

function xScorri($codPadre, $dValida, $nQta, $aComp, $nCompLen, $nLevel)
{
	global $conn;

	$nLevel += 1;
	if($nlevel > 10) {
	   print("<h2>Troppi livelli - probabile ricorsione</h2>");
	}

	$Query = "SELECT CODCOMP, UNMISURA, QUANTITA, TIPOPARTE, DATAINIVAL, DATAFINVAL, FATT, QUANTALT ";
	$Query .= "FROM DISTBASE WHERE CODPADRE='$codPadre' ORDER BY NUMERORIGA";
	$rs = db_query($conn, $Query) or die(mysql_error());
	while($row = mysql_fetch_object($rs))
	{
		//        echo "DataIniVal = " . $row->DATAINIVAL;
		//        echo "DataFinVal = " . $row->DATAFINVAL;
		$nConsumo = ($row->QUANTALT) * $nQta;
		if( $dValida == "" ) {
			$today=strtotime(date("Y-m-d"));
		} else {
			$today=strtotime($dValida);
		}
		if( floatval($row->DATAINIVAL) == 0 ) {
			 $inidate = strtotime("-10000 day");
		} else {
			 $inidate = strtotime(str_replace("/","-",$row->DATAINIVAL));
		}
		if( floatval($row->DATAFINVAL) == 0 ) {
			 $findate = strtotime("+1000 day");
		} else {
			 $findate = strtotime(str_replace("/","-",$row->DATAFINVAL));
		}
		// print_r($row->CODCOMP . " - IniDate:" . $inidate . " - FinDate" . $findate . " - Today:" . $today . " - TipoParte: " . $row->TIPOPARTE . " /");
		// print("$findate - ");
		if ($findate >= $today and $inidate <= $today) {
			$nCompLen += 1;
			$aComp[$nCompLen] = array("codice" => $row->CODCOMP,
				"consumo" => $nConsumo,
				"um" => $row->UNMISURA,
				"liv" => $nLevel,
				"tipoparte" => $row->TIPOPARTE  );
			$nCompLen = xScorri($row->CODCOMP, $dValida, $nConsumo, &$aComp, $nCompLen, $nLevel);
		}
	}

	return $nCompLen;
}
?>
