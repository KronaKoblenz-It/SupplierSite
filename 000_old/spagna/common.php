<?php

/************************************************************************/
/* CASASOFT ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2011 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/
 
// File da includere in tutte le pagine
// Inizializza le variabili

// Ditta in cui si accede 
$dbase = "spagna"; 
$lang  = "es";

// Colori di sfondo del marchio
$dittacolor = array(
		 	"krona" => "#d12400",
	         	"koblenz" => "#ffffff",
	         	"spagna" => "#ffffff"  
	         );
	             
// Titolo della pagina
$dittatitle = array( 
		     	"krona" => "Krona - Soluzioni per porte scorrevoli",
	             	"koblenz" => "Koblenz - Sistemi scorrevoli",
	             	"spagna" => "Krona Koblenz" 
	             );
	             		
// Ragione sociale ditta
$dittaragsoc = array(
		      	"krona" => "Krona I S.p.A.",
	              	"koblenz" => "Koblenz S.p.A.",
	              	"spagna" => "Krona Koblenz s.l."
	               );
	             		
// Indirizzo ditta
$dittainfo = array( 
			"krona" => "via Piane, 90 - Coriano (RN) - Italy",
	             	"koblenz" => "via Piane, 90 - Coriano (RN) - Italy",
	             	"spagna" => "P.I.MOLI D'EN XEC, 49/55 nave n. P3 - 08291 RIPOLLET (Barcelona) - Espana" 
	             	);
	             		
$str_dataord = array(
	"it" => "Data ordine",
	"es" => "Fecha pedido"
	);
$str_dataprevcons = array(
	"it" => "Data prev. consegna",
	"es" => "Fecha prevista de salida"
	);
$str_stato = array(
	"it" => "Stato",
	"es" => "Estado"
	);
$str_eleord = array(
	"it" => "Elenco Ordini",
	"es" => "Listado de Pedidos"
	);
$str_dettord = array(
	"it" => "Dettaglio ordine",
	"es" => "Descripcion del pedido"
	);
$str_dettddt = array(
	"it" => "Dettaglio DDT",
	"es" => "Descripcion albaran"
	);
$str_dettfatt = array(
	"it" => "Dettaglio fattura",
	"es" => "Descripcion factura"
	);
$str_codice = array(
	"it" => "Codice",
	"es" => "Codigo"
	);
$str_desc = array(
	"it" => "Descrizione",
	"es" => "Descripcion"
	);
$str_um = array(
	"it" => "UM",
	"es" => "UN"
	);
$str_quantita = array(
	"it" => "Quantita'",
	"es" => "Cantidad"
	);
$str_prezzoun = array(
	"it" => "Prezzo Un",
	"es" => "Precio Un"
	);
$str_sconti = array(
	"it" => "Sconti",
	"es" => "Descuento"
	);
$str_totale = array(
	"it" => "Totale",
	"es" => "Total"
	);
$str_dataddt = array(
	"it" => "Data DDT",
	"es" => "Fecha Albaran"
	);
$str_numero = array(
	"it" => "Numero",
	"es" => "Numero"
	);
$str_sped = array(
	"it" => "Spedita a mezzo",
	"es" => "Medio de transporte"
	);
$str_evasocon = array(
	"it" => "Evaso con",
	"es" => "Expedido con"
	);
$str_nome = array(
	"it" => "Nome",
	"es" => "Nombre"
	);
$str_indirizzo = array(
	"it" => "Indirizzo",
	"es" => "Direccion"
	);
$str_localita = array(
	"it" => "Localita'",
	"es" => "Localidad"
	);
$str_pr = array(
	"it" => "Pr.",
	"es" => "Pr."
	);
$str_telefono = array(
	"it" => "Telefono",
	"es" => "Telefono"
	);
$str_fatturatocon = array(
	"it" => "Fattura",
	"es" => "Factura"
	);
$str_data = array(
	"it" => "Data",
	"es" => "Fecha"
	);
$str_scadenze = array(
	"it" => "Scadenze pagamenti",
	"es" => "Vencimiento del pago"
	);
$str_scadenza = array(
	"it" => "Data scadenza",
	"es" => "Fecha de vencimiento"
	);
$str_importo = array(
	"it" => "Importo",
	"es" => "Importe"
	);
$str_pagato = array(
	"it" => "Pagato",
	"es" => "Pagado"
	);
$str_tipo = array(
	"it" => "Tipo",
	"es" => "Tipo"
	);
$str_telefono = array(
	"it" => "Telefono vettore",
	"es" => "Telefono del transportista"
	);
$str_colli = array(
	"it" => "Colli",
	"es" => "Bultos"
	);
$str_peso = array(
	"it" => "Peso Kg.",
	"es" => "Peso Kg."
	);
$str_entra = array(
	"it" => "Entra",
	"es" => "Ok"
	);
$str_mainmenu = array(
	"it" => "Menu principale",
	"es" => "Menu principal"
	);
$str_evaso = array(
	"it" => "Evaso",
	"es" => "Expedido"
	);
$str_nonevaso = array(
	"it" => "Da evadere",
	"es" => "Non expedido"
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
        return 'Pagarè';
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
  
  }
}


// Calcolo dell'anno corrente
function current_year() {
$lt = localtime(time(), true);
return ($lt[tm_year]+1900);
}
          
?>