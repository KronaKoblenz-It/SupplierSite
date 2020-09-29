<?php

/************************************************************************/
/* CASASOFT ArcaWeb                              				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2011 by Roberto Ceccarelli                        */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/
 
// File da includere in tutte le pagine
// Inizializza le variabili

// Ditta in cui si accede 
$dbase = "francia"; 
$lang  = "fr";

// Colori di sfondo del marchio
$dittacolor = array(
		 	"krona" => "#d12400",
	         	"koblenz" => "#ffffff",
	         	"spagna" => "#ffffff",  
	         	"francia" => "#ffffff"  
	         );
	             
// Titolo della pagina
$dittatitle = array( 
		     	"krona" => "Krona Koblenz",
	             	"koblenz" => "Koblenz - Sistemi scorrevoli",
	             	"spagna" => "Krona Koblenz" 
	             );
	             		
// Ragione sociale ditta
$dittaragsoc = array(
		      	"krona" => "Krona Koblenz S.p.A.",
	              	"koblenz" => "Koblenz S.p.A.",
	              	"spagna" => "Krona Koblenz s.l."
	               );
	             		
// Indirizzo ditta
$dittainfo = array( 
			"krona" => "via Romero, 1 - Coriano (RN) - Italy",
	             	"koblenz" => "via Romero, 1 - Coriano (RN) - Italy",
	             	"spagna" => "Pol. Ind. Rubi Sud, Prolongacion Av. Antonio Gaudi S/N, Nave 7 - 08191 Rubi (Barcelona) - frpana" 
	             	);
	             		
$str_dataord = array(
	"it" => "Data ordine",
	"fr" => "Date commande"
	);
$str_dataprevcons = array(
	"it" => "Data prev. di spedizione",
	"fr" => "Date prévue d'expédition"
	);
$str_stato = array(
	"it" => "Stato",
	"fr" => "Etat"
	);
$str_eleord = array(
	"it" => "Elenco Ordini",
	"fr" => "Liste commandes"
	);
$str_dettord = array(
	"it" => "Dettaglio ordine",
	"fr" => "Détail commande"
	);
$str_dettddt = array(
	"it" => "Dettaglio DDT",
	"fr" => "Détail DDT"
	);
$str_dettfatt = array(
	"it" => "Dettaglio fattura",
	"fr" => "Détail facture"
	);
$str_codice = array(
	"it" => "Codice",
	"fr" => "Code"
	);
$str_dfrc = array(
	"it" => "Descrizione",
	"fr" => "Description"
	);
$str_um = array(
	"it" => "UM",
	"fr" => "UM"
	);
$str_quantita = array(
	"it" => "Quantita'",
	"fr" => "Quantité"
	);
$str_prezzoun = array(
	"it" => "Prezzo Un",
	"fr" => "Prix Un"
	);
$str_sconti = array(
	"it" => "Sconti",
	"fr" => "Remises"
	);
$str_totale = array(
	"it" => "Totale",
	"fr" => "Total"
	);
$str_dataddt = array(
	"it" => "Data DDT",
	"fr" => "Date DDT"
	);
$str_numero = array(
	"it" => "Numero",
	"fr" => "Numéro"
	);
$str_sped = array(
	"it" => "Spedita a mezzo",
	"fr" => "Moyen de transport"
	);
$str_evasocon = array(
	"it" => "Evaso con",
	"fr" => "expédié avec"
	);
$str_nome = array(
	"it" => "Nome",
	"fr" => "Nom"
	);
$str_indirizzo = array(
	"it" => "Indirizzo",
	"fr" => "Adresse"
	);
$str_localita = array(
	"it" => "Localita'",
	"fr" => "Localité"
	);
$str_pr = array(
	"it" => "Pr.",
	"fr" => "Pr."
	);
$str_telefono = array(
	"it" => "Telefono",
	"fr" => "Téléphone"
	);
$str_fatturatocon = array(
	"it" => "Fattura",
	"fr" => "Facture"
	);
$str_data = array(
	"it" => "Data",
	"fr" => "Date"
	);
$str_scadenze = array(
	"it" => "Scadenze pagamenti",
	"fr" => "Echéances paiements"
	);
$str_scadenza = array(
	"it" => "Data scadenza",
	"fr" => "Date échéance"
	);
$str_importo = array(
	"it" => "Importo",
	"fr" => "Somme Totale"
	);
$str_pagato = array(
	"it" => "Pagato",
	"fr" => "Payé"
	);
$str_tipo = array(
	"it" => "Tipo",
	"fr" => "Type"
	);
$str_telefono = array(
	"it" => "Telefono vettore",
	"fr" => "Téléphone transporteur"
	);
$str_colli = array(
	"it" => "Colli",
	"fr" => "Colis"
	);
$str_peso = array(
	"it" => "Peso Kg.",
	"fr" => "Peso Kg."
	);
$str_entra = array(
	"it" => "Entra",
	"fr" => "Valider"
	);
$str_mainmenu = array(
	"it" => "Menu principale",
	"fr" => "Menu principal"
	);
$str_evaso = array(
	"it" => "Evaso",
	"fr" => "Traitèe"
	);
$str_nonevaso = array(
	"it" => "Da evadere",
	"fr" => "A traiter"
	);



// --------------------------------------------------------
// rfrtituisce una stringa dfrcrittiva del tipo di scadenza
function scad_tipo($tipo, $lang) {
switch($lang) {

// italiano
  case 'it':
    switch($tipo) {
      case 'D':
        return 'Rimessa diretta';
        break;
      case 'R':
        return 'Ricevuta bancaria';
        break;
      case 'T':
        return 'Tratta';
        break;
      case 'P':
        return 'Pagherò';
        break;
      case 'B':
        return 'Bonifico';
        break;
      case 'L':
        return 'Bollettino postale';
        break;
      case 'C':
        return 'Contrassegno';
        break;
      case 'A':
        return 'Altro';
        break;
    }
    break;    

// francese
  case 'fr':
    switch($tipo) {
      case 'D':
        return 'Traite directe';
        break;
      case 'R':
        return 'Recu bancaire';
        break;
      case 'T':
        return 'Traite';
        break;
      case 'P':
        return 'Je paierai';
        break;
      case 'B':
        return 'Virement bancaire';
        break;
      case 'L':
        return 'Bulletin postale';
        break;
      case 'C':
        return 'Contre remboursement';
        break;
      case 'A':
        return 'Autre';
        break;
    }
    break; 
  
  }
}

// Calcolo dell'anno corrente
function current_year() {
$lt = localtime(time(), true);
return ($lt[tm_year]+1900);
}
       
?>