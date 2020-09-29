<?php
/************************************************************************/
/* CASASOFT ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2018 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php");
include("db-utils.php");
$connectionstring = db_connect($dbase);
head();
session_start();
$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
$maga = "F" . substr($cookie[0],2);
$forn = $cookie[0];
$isCdep = false;

$Query = "SELECT CODICEMAG FROM u_magcdep WHERE CODICECF=\"$forn\"";
$queryexe = db_query($connectionstring, $Query) or die("$Query<br>" . mysql_error() );
if($row = mysql_fetch_object($queryexe)) {
	$isCdep = true;
	$maga = $row->CODICEMAG;
}
banner("Menu Principale",$cookie[1]);
visualizza_novita($cookie[0], $dbase);
print("<table class=\"list\">\n");
if("F01021" == $cookie[0]) {
	menuItem("cli-detail.php?id=" . $cookie[0], _("Carichi di produzione & Ordini Lav. Interni"));
} else {
	menuItem("cli-detail.php?id=" . $cookie[0], $str_eleord[$lang]);
}
menuItem("art-detail.php?id=" . $cookie[0], _("Articoli in ordine"));
if(!$isCdep) {
	menuItem("askdb.php", _("Inserimento bolla"));
	menuItem("ddttoload.php", _("Bolle in attesa di acquisizione"));
	//menuItem("ddtimport.php", _("Caricamento bolle da file XML"));
	menuItem("ddtimportxls.php", _("Caricamento bolle da file Excel"));
	menuItem("bollecons.php?id=" . $cookie[0], _("DDT Materiale da lavorare"));
	menuItem("artcons.php?id=" . $cookie[0], _("Rettifica articoli consegnati"));
} else {
	menuItem("ddtContoDep.php?id=" . $cookie[0], _("DDT da Conto Deposito (CD)"));
	menuItem("cp-detail.php?id=" . $cookie[0], _("Carichi di Produzione (CP)"));
}
menuItem("magart_forn.php", _("Anagrafica articoli"));
menuItem("giornalemaga.php", _("Situazione magazzino"));

if("F02707" == $cookie[0]) {
	menuItem("cliGrass.php", _("Movimenti clienti conto deposito"));
}

if("F01540" == $cookie[0]) {
	menuItem("rlimportxls.php", _("Resi non lavorati da Excel"));
	menuItem("ksimportxls.php", _("Rettifiche / sfridi da Excel"));
}

menuItem("rnc.php", _("Rapporti di non conformit&agrave;"));

if(!$isCdep) {
	// print("<tr class=\"list\">\n");
	// $Query = "select data from u_invfine where magazzino=\"$maga\" and finito=1 ";
	// $queryexe = db_query($connectionstring, $Query) or die(mysql_error() );
	// if( !($row = db_fetch_row($queryexe))) {
		// print("<th class=\"menu\"><a href=\"inv_xls.php\">Caricamento inventario da Excel</a></th>\n");
	// } else {
		// print("<th class=\"menu\">" . _("Inventario chiuso il") . " " . format_date($row[0]) . "</th>\n");
	// }
	// print("</tr>\n");

	// menuItem("inv-list.php", _("Verifica inventario"));
	menuItem("menu-inv.php", _("Menu inventari"));
}
menuItem("manualistica.php", _("Manualistica"));
print("</table>\n");
// print("<a href=\"cli-detail-test.php?id=" . $cookie[0] ."\">test etichette</a><br>\n");

// Avviso Temporaneo
/*if(strcmp(substr($cookie[0],0,1),"F")==0){
	print("<div style='float: middle;' id='avviso'>
					<fieldset style='width: 70%; float: center'>
						<legend>	<h3>NUOVA DOCUMENTAZIONE</h3>	</legend>
						<p>
						Informiamo i Nostri Fornitori che sono presenti due nuovi Manuali nella sezione MANUALISTICA.<br />
						<ul>
							<li>
								01_Tracciato Magazzino
							</li>
							<li>
								02_Gestione Giacenze WEB
							</li>
						</ul>
						Preghiamo di prenderne visione. <br />
						Grazie. <br />
						Distinti Saluti
						</p>
					</fieldset>
				</div>");
}*/

//diconnect from database
db_close($connectionstring);

footer();
?>
