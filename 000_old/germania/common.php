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
$dbase = "germania"; 
$lang  = "de";

// Colori di sfondo del marchio
$dittacolor = array(
		 	"krona" => "#d12400",
	         	"russia" => "#ffffff",
	         	"spagna" => "#ffffff",  
	         	"germania" => "#ffffff",  
	         	"francia" => "#ffffff"  
	         );
	             
// Titolo della pagina
$dittatitle = array( 
		     	"krona" => "Krona Koblenz",
	             	"germania" => "Krona Koblenz",
	             	"spagna" => "Krona Koblenz" 
	             );
	             		
// Ragione sociale ditta
$dittaragsoc = array(
		      	"krona" => "Krona Koblenz S.p.A.",
	              	"germania" => "Krona Koblenz DE",
	              	"spagna" => "Krona Koblenz s.l."
	               );
	             		
// Indirizzo ditta
$dittainfo = array( 
			"krona" => "via Romero, 1 - Coriano (RN) - Italy",
	             	"germania" => "via Romero, 1 - Coriano (RN) - Italy",
	             	"spagna" => "Pol. Ind. Rubi Sud, Prolongacion Av. Antonio Gaudi S/N, Nave 7 - 08191 Rubi (Barcelona) - frpana" 
	             	);
	             		
$str_dataord = array(
	"it" => "Data ordine",
	"de" => "Date commande"
	);
$str_dataprevcons = array(
	"it" => "Data prev. di spedizione",
	"de" => "Date prévue d'expédition"
	);
$str_stato = array(
	"it" => "Stato",
	"de" => "Etat"
	);
$str_eleord = array(
	"it" => "Elenco Ordini",
	"de" => "Liste commandes"
	);
$str_dettord = array(
	"it" => "Dettaglio ordine",
	"de" => "Détail commande"
	);
$str_dettddt = array(
	"it" => "Dettaglio DDT",
	"de" => "Détail DDT"
	);
$str_dettfatt = array(
	"it" => "Dettaglio fattura",
	"de" => "Détail facture"
	);
$str_codice = array(
	"it" => "Codice",
	"de" => "Code"
	);
$str_dfrc = array(
	"it" => "Descrizione",
	"de" => "Description"
	);
$str_um = array(
	"it" => "UM",
	"de" => "UM"
	);
$str_quantita = array(
	"it" => "Quantita'",
	"de" => "Quantité"
	);
$str_prezzoun = array(
	"it" => "Prezzo Un",
	"de" => "Prix Un"
	);
$str_sconti = array(
	"it" => "Sconti",
	"de" => "Remises"
	);
$str_totale = array(
	"it" => "Totale",
	"de" => "Total"
	);
$str_dataddt = array(
	"it" => "Data DDT",
	"de" => "Date DDT"
	);
$str_numero = array(
	"it" => "Numero",
	"de" => "Numéro"
	);
$str_sped = array(
	"it" => "Spedita a mezzo",
	"de" => "Moyen de transport"
	);
$str_evasocon = array(
	"it" => "Evaso con",
	"de" => "expédié avec"
	);
$str_nome = array(
	"it" => "Nome",
	"de" => "Nom"
	);
$str_indirizzo = array(
	"it" => "Indirizzo",
	"de" => "Adresse"
	);
$str_localita = array(
	"it" => "Localita'",
	"de" => "Localité"
	);
$str_pr = array(
	"it" => "Pr.",
	"de" => "Pr."
	);
$str_telefono = array(
	"it" => "Telefono",
	"de" => "Téléphone"
	);
$str_fatturatocon = array(
	"it" => "Fattura",
	"de" => "Facture"
	);
$str_data = array(
	"it" => "Data",
	"de" => "Date"
	);
$str_scadenze = array(
	"it" => "Scadenze pagamenti",
	"de" => "Echéances paiements"
	);
$str_scadenza = array(
	"it" => "Data scadenza",
	"de" => "Date échéance"
	);
$str_importo = array(
	"it" => "Importo",
	"de" => "Somme Totale"
	);
$str_pagato = array(
	"it" => "Pagato",
	"de" => "Payé"
	);
$str_tipo = array(
	"it" => "Tipo",
	"de" => "Type"
	);
$str_telefono = array(
	"it" => "Telefono vettore",
	"de" => "Téléphone transporteur"
	);
$str_colli = array(
	"it" => "Colli",
	"de" => "Colis"
	);
$str_peso = array(
	"it" => "Peso Kg.",
	"de" => "Peso Kg."
	);
$str_entra = array(
	"it" => "Entra",
	"de" => "Valider"
	);
$str_mainmenu = array(
	"it" => "Menu principale",
	"de" => "Menu principal"
	);
$str_evaso = array(
	"it" => "Evaso",
	"de" => "Traitèe"
	);
$str_nonevaso = array(
	"it" => "Da evadere",
	"de" => "A traiter"
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
  case 'de':
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