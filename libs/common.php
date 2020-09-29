<?php
/************************************************************************/
/* CASASOFT ArcaWeb                              				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2012 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/
 
// File da includere in tutte le pagine
// Inizializza le variabili

// Colori di sfondo del marchio
$dittacolor = array(
		 	"krona" => "#d12400",
         	"spagna" => "#ffffff",
			"francia" => "#ffffff",
			"germania" => "#ffffff"
	         );
	             
// Titolo della pagina
$dittatitle = array( 
		     	"krona" => "Krona Koblenz",
             	"spagna" => "Krona Koblenz",
				"francia" => "Krona Koblenz",
				"germania" => "Krona Koblenz"
	             );
	             		
// Ragione sociale ditta
$dittaragsoc = array(
		      	"krona" => "Krona Koblenz S.p.A.",
              	"spagna" => "Krona Koblenz s.l.",
				"francia" => "Krona Koblenz",
				"germania" => "Krona Koblenz DE"
	               );
	             		
// Indirizzo ditta
$dittainfo = array( 
			"krona" => "<div class=\"adr\">\n<span class=\"street-address\">via Romero 1</span> - \n<span class=\"locality\">Coriano</span> (<span class=\"region\">RN</span>) - \n<span class=\"country-name\">Italia</span>\n</div>\n",
           	"spagna" => "<div class=\"adr\">\n<span class=\"street-address\">Pol. Ind. Rubi Sud, Prolongacion Av. Antonio Gaudi S/N, Nave 7</span> - \n<span class=\"locality\">Rubi</span> (<span class=\"region\">Barcelona</span>) - \n<span class=\"country-name\">Espana</span>\n</div>\n",
			"francia" => "<div class=\"adr\">\n<span class=\"street-address\">635 Boulevard Napol�on Bullukian</span> - \n<span class=\"locality\">Saint Georges de Reneins</span> (<span class=\"region\">69830</span>) - \n<span class=\"country-name\">France</span>\n</div>\n",
			"germania" => "<div class=\"adr\">\n<span class=\"street-address\">Am Pfahlgraben 4-10</span> - \n<span class=\"locality\">D-35415 Pohlheim-Garbenteich</span> (<span class=\"region\"></span>) - \n<span class=\"country-name\">Allemagne</span>\n</div>\n"
	             	);
$dittatel = array( 
			"krona" => "+39 0541 657040",
			"spagna" => "+34 93 591 0410",
			"francia" => "+33 04 74 09 09 39",
			"russia" => "",
			"germania" => "+49 (0) 6404-69775-0"
			);
	             		
$dittafax = array( 
			"krona" => "+39 0541 658211",
			"spagna" => "+34 93 591 0498",
			"francia" => "+33 04 74 09 09 32",
			"russia" => "",
			"germania" => "+49 (0) 6404-69775-11"
			);
	             		
$dittamail = array( 
			"krona" => "info@k-group.com",
			"spagna" => "info@kronakoblenz.es",
			"francia" => "info@kronakoblenz.fr",
			"russia" => "info@kronakoblenz.ru",
			"germania" => "info@kronakoblenz.de"
			);
	             		
$str_dataord = array(
	"it" => "Data ordine",
	"es" => "Fecha pedido",
	"fr" => "Date commande",
	"de" => "Auftragsdatum"
	);
$str_dataprevcons = array(
	"it" => "Data richiesta consegna",
	"es" => "Fecha prevista de salida",
	"fr" => "Date pr&eacute;vue d'exp&eacute;dition",
	"de" => "Vorraussichtliches Versanddatum"
	);
$str_dataevas = array(
	"it" => "Data prev. evasione",
	"es" => "Fecha prevista de expedicion",
	"fr" => "",
	"de" => "Vorraussichtliches Versanddatum"
	);
$str_stato = array(
	"it" => "Stato",
	"es" => "Estado",
	"fr" => "Etat",
	"de" => "Status"
	);
$str_eleord = array(
	"it" => "Elenco Ordini",
	"es" => "Listado de Pedidos",
	"fr" => "Liste commandes",
	"de" => "Auftragsliste"
	);
$str_rubrica = array(
    "it" => "Rubrica Clienti",
    "es" => "Ficha Clientes",
    "fr" => "Liste des Clients",
    "de" => "Kunden Adressbuch"
);
$str_dettord = array(
	"it" => "Dettaglio ordine",
	"es" => "Descripcion del pedido",
	"fr" => "D&eacute;tail commande",
	"de" => "Auftragsdetails"
	);
$str_dettddt = array(
	"it" => "Dettaglio DDT",
	"es" => "Descripcion albaran",
	"fr" => "D&eacute;eferscheindetails"
	);
$str_dettfatt = array(
	"it" => "Dettaglio fattura",
	"es" => "Descripcion factura",
	"fr" => "D&eacute;tail facture",
	"de" => "Rechnungsdetails"
	);
$str_codice = array(
	"it" => "Codice",
	"es" => "Codigo",
	"fr" => "Code",
	"de" => "Code"
	);
$str_desc = array(
	"it" => "Descrizione",
	"es" => "Descripcion",
	"fr" => "Description",
	"de" => "Beschreibung"
	);
$str_um = array(
	"it" => "UM",
	"es" => "UN",
	"fr" => "UM",
	"de" => "UM"
	);
$str_quantita = array(
	"it" => "Quantita'",
	"es" => "Cantidad",
	"fr" => "Quantit&eacute;",
	"de" => "Menge"
	);
$str_residuo = array(
	"it" => "Q.ta res.",
	"es" => "Residuo",
	"fr" => "Residuo",
	"de" => "R�ckstand"
	);
$str_prezzoun = array(
	"it" => "Prezzo Un",
	"es" => "Precio Un",
	"fr" => "Prix Un",
	"de" => "Preis Un"
	);
$str_sconti = array(
	"it" => "Sconti",
	"es" => "Descuento",
	"fr" => "Remises",
	"de" => "Rabatt"
	);
$str_totale = array(
	"it" => "Totale",
	"es" => "Total",
	"fr" => "Total",
	"de" => "Gesamtsumme"
	);
$str_dataddt = array(
	"it" => "Data DDT",
	"es" => "Fecha Albaran",
	"fr" => "Date DDT",
	"de" => "Lieferscheindatum"
	);
$str_numero = array(
	"it" => "Numero",
	"es" => "Numero",
	"fr" => "Num&eacute;ro",
	"de" => "Nummer"
	);
$str_sped = array(
	"it" => "Spedita a mezzo",
	"es" => "Medio de transporte",
	"fr" => "Moyen de transport",
	"de" => "Verschickt mit"
	);
$str_evasocon = array(
	"it" => "Evaso con",
	"es" => "Expedido con",
	"fr" => "Expedie avec",
	"de" => "Verschickt mit"
	);
$str_nome = array(
	"it" => "Nome",
	"es" => "Nombre",
	"fr" => "Nom",
	"de" => "Name"
	);
$str_indirizzo = array(
	"it" => "Indirizzo",
	"es" => "Direccion",
	"fr" => "Adresse",
	"de" => "Adresse"
	);
$str_localita = array(
	"it" => "Localita'",
	"es" => "Localidad",
	"fr" => "Localit&eacute;",
	"de" => "Ort"
	);
$str_pr = array(
	"it" => "Pr.",
	"es" => "Pr.",
	"fr" => "Pr.",
	"de" => "Pr."
	);
$str_telefono = array(
	"it" => "Telefono",
	"es" => "Telefono",
	"fr" => "T&eacute;l&eacute;phone",
	"de" => "Telefon"
	);
$str_fatturatocon = array(
	"it" => "Fattura",
	"es" => "Factura",
	"fr" => "Facture",
	"de" => "Rechnung"
	);
$str_data = array(
	"it" => "Data",
	"es" => "Fecha",
	"fr" => "Date",
	"de" => "Datum"
	);
$str_scadenze = array(
	"it" => "Scadenze pagamenti",
	"es" => "Vencimiento del pago",
	"fr" => "Ech&eacute;ances paiements",
	"de" => "Zahlungs F�lligkeit"
	);
$str_scadenza = array(
	"it" => "Data scadenza",
	"es" => "Fecha de vencimiento",
	"fr" => "Date &eacute;ch&eacute;ance",
	"de" => "F�lligkeitsdatum"
	);
$str_importo = array(
	"it" => "Importo",
	"es" => "Importe",
	"fr" => "Somme Totale",
	"de" => "Summe"
	);
$str_pagato = array(
	"it" => "Pagato",
	"es" => "Pagado",
	"fr" => "Pay&eacute;",
	"de" => "Bezahlt"
	);
$str_tipo = array(
	"it" => "Tipo",
	"es" => "Tipo",
	"fr" => "Type",
	"de" => "Typ"
	);
$str_telefono = array(
	"it" => "Telefono vettore",
	"es" => "Telefono del transportista",
	"fr" => "T&eacute;l&eacute;phone transporteur",
	"de" => "Transporteurtelefonnummer"
	);
$str_colli = array(
	"it" => "Colli",
	"es" => "Bultos",
	"fr" => "Colis",
	"de" => "Packst�cke"
	);
$str_peso = array(
	"it" => "Peso Kg.",
	"es" => "Peso Kg.",
	"fr" => "Peso Kg.",
	"de" => "Gewicht KG"
	);
$str_entra = array(
	"it" => "Entra",
	"es" => "Entrada",
	"fr" => "Valider",
	"de" => "Enter"
	);
$str_mainmenu = array(
	"it" => "Menu principale",
	"es" => "Menu principale",
	"fr" => "Menu principal",
	"de" => "Hauptmen�"
	);
$str_evaso = array(
	"it" => "Evaso",
	"es" => "Expedido",
	"fr" => "Trait&eacute;e",
	"de" => "Geliefert"
	);
$str_nonevaso = array(
	"it" => "Da evadere",
	"es" => "Non expedido",
	"fr" => "A traiter",
	"de" => "Zum ausliefern"
	);
$str_loginerror = array(
	"it" => "Nome utente o password non corretta",
	"es" => "Nome utente o password non corretta",
	"fr" => "Nome utente o password non corretta",
	"de" => "Nome utente o password non corretta"
	);



// --------------------------------------------------------
// restituisce una stringa descrittiva del tipo di scadenza
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
        return 'Pagher�';
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

// spagnolo
  case 'es':
    switch($tipo) {
      case 'D':
        return 'Remesa directa';
        break;
      case 'R':
        return 'Recibo bancario';
        break;
      case 'T':
        return 'Giro sin domiciliar';
        break;
      case 'P':
        return 'Pagar�';
        break;
      case 'B':
        return 'Transferencia bancaria';
        break;
      case 'L':
        return 'Boletin de correo';
        break;
      case 'C':
        return 'Pagado en efectivo';
        break;
      case 'A':
        return 'Transferencia';
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

// tedesco
  case 'de':
    switch($tipo) {
      case 'D':
        return 'Sorfortzahlunge';
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
        return 'Bank�berweisung';
        break;
      case 'L':
        return 'Bulletin postale';
        break;
      case 'C':
        return 'Zahlung bei Warenempfang';
        break;
      case 'A':
        return 'Weiteres';
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