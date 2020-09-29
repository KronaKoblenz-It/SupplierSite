<?php 
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2020 by Roberto Ceccarelli                        */
/* http://strawberryfield.altervista.org								*/
/* 																		*/
/************************************************************************/

function link_script() {
	$text = <<<EOT
<script type="text/javascript" src="../js/checkbarcode.js"></script>
<script type="text/javascript" src="../js/ajaxlib.js"></script>
<script type="text/javascript" src="../js/select_lib.js"></script>
<script type="text/javascript" src="../js/askdb.js"></script>
EOT;
print("$text\n");
}

function writeEtich($link, $desc, $id, $hidden) {
    if($hidden==false){
	    print("&nbsp;<a href=\"javascript:writeEtich('$link');\" title=\"$desc\" id='$id'>");
    }
    else{
        print("&nbsp;<a href=\"javascript:writeEtich('$link');\" title=\"$desc\" id='$id' style='visibility: hidden'>");
    }
	print("<img style=\"border: none;\" src=\"../img/printer.png\" alt=\"$desc\">$desc</a>\n");
}

function form_body($codcf, $gruppoFiltro, $Query, $target) {
$text = <<<EOT
<form action="$target" method="get" >
<table>
<tr><td>Articolo</td>
<td><select name="articolo" id="articolo" 
onchange="listaRif('$codcf', this.options[this.selectedIndex].value);" >
<option value="">&nbsp;</option>
EOT;
print("$text\n");

$connectionstring = db_connect($dbase);
$queryexe = db_query($connectionstring, $Query) or die(mysql_error()); 
while($row = db_fetch_row($queryexe)) {
	print("<option value=\"" . $row[0] . "\">" . $row[0] . " - " . $row[1] . "</option>\n");
}
$text = <<<EOT
</select>
<label id='labeldoppiocollo' style='color: red; font-style: italic; visibility: hidden'>     Attenzione!!!. L'articolo prevede 2 colli. Stampare l'etichetta corrispondente.</label>
</td></tr>

<tr><td>Riferimento</td>
<td><select name="rif" id="rif" onblur='setResiduo(); setCliente(); setLotto();' onchange='setResiduo(); setCliente(); setLotto();'>
<option value="">&nbsp;</option>
</select></td></tr>
<tr><td>Cliente</td><td><input type='text' name='cliente' id='cliente' readonly></td></tr>

<tr><td>Lotto</td><td><input type="text" name="lotto" id="lotto" onblur="checkBarcode39(this);">
<tr><td></td><td>

EOT;
print("$text\n");
writeEtich("etich48.php", "Etichetta 89x48 PZ", "", false, 'PZ');
writeEtich("etich48.php", "Etichetta 89x48 SC", "", false, 'SC');
writeEtich("etich48.php", "Etichetta 89x48 CF", "", false, 'CF');
//writeEtich("etich1lotti.php", "Etichetta 88x36");
writeEtich("eticha4lotti.php", "Etichette su A4", "", false);
writeEtich("etich1lotti_unificate_collo2.php", "Etichetta 88x36 PZ - Collo Supplementare", "etich2collo", true);
writeEtich("etich-porta.php", "Etichetta Porta 88x36" , "etichporta", true);
writeEtich("etich1_weight.php", "Etichetta Attenzione peso 73x110", "", false);
$text = <<<EOT
</td></tr>
</td></tr>
<tr><td>Quantit&agrave</td><td><input type="text" name="quantita" id="quantita" onblur='checkQuantita()'>
<label id='labelquantita' style='color: red; font-style: italic; visibility: hidden'>     
Attenzione!!!. Le quantit&agrave inserite superano del 10% il residuo dell'ordine selezionato. Correggere le quantit&agrave o selezionare un altro ordine.</label></td></tr>
<tr><td>Residuo</td><td><input type="text" name="residuo" id="residuo" readonly></td></tr>

<tr><td>Copia lotti da</td>
<td><select name="copy" id="copy" onchange="setLottoPadre(this.options[this.selectedIndex].text);" >
<option value="">&nbsp;</option>
</select></td></tr>

</table>
<input type='text' name='codcli' id='codcli' readonly hidden='hidden'>
<input type='hidden' name='gruppo' id='gruppo' value='$gruppoFiltro'>
<input type='hidden' name='mode' id='mode' value='L'>
<input type="submit" id="btnok" value="Ok">
</form>

<br>
EOT;
print("$text\n");	
}
?>
