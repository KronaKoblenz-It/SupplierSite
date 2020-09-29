<?php 
/************************************************************************/
/* CASASOFT ArcaWeb                               		        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2004 by Roberto Ceccarelli                        */
/* http://casasoft.supereva.it                                          */
/*                                                                      */
/* This program is free software. You can redistribute it and/or modify */
/* it under the terms of the GNU General Public License as published by */
/* the Free Software Foundation; either version 2 of the License.       */
/************************************************************************/

function doc_rows($id,$connectionstring) {
global $lang, $str_codice, $str_desc, $str_um, $str_quantita, $str_prezzoun, $str_sconti, $str_totale ;
print('<table width="100%" border="0" cellspacing="1" cellpadding="1" bgcolor="#000000"> ');
print('  <tr bgcolor="#CCFFFF">  ');
print('    <th height="22"><b>' . $str_codice[$lang] . '</b></th> ');
print('    <th height="22"><b>' . $str_desc[$lang] . '</b></th> ');
print('    <th height="22"><b>' . $str_um[$lang] . '</b></th> ');
print('    <th height="22"><b>' . $str_quantita[$lang] . '</b></th> ');
print('    <th height="22"><b>' . $str_prezzoun[$lang] . '</b></th> ');
print('    <th height="22"><b>' . $str_sconti[$lang] . '</b></th> ');
print('    <th height="22"><b>' . $str_totale[$lang] . '</b></th> ');
print('  </tr> ');


//SQL quyery  
$Query = "SELECT DATADOC,NUMERODOC,CODICEARTI,DESCRIZION,UNMISURA,QUANTITA,PREZZOUN,SCONTI,PREZZOTOT,ID_TESTA FROM DOCRIG WHERE ID_TESTA = " . $id;
$queryexe = db_query($connectionstring, $Query); 

//query database 
    while($row = db_fetch_row($queryexe)) 
    { 

    $id_testa = $row[9];

    //format results 
    print ("<tr bgcolor='#ccffcc'>"); 
    print ("<td>" . $row[2] . "</td>"); 
    print ("<td>" . $row[3] . "</td>"); 
    print ("<td><center>" . $row[4] . "</center></td>"); 
    print ("<td align='right'>" . $row[5] . "</td>"); 
    print ("<td align='right'>" . $row[6] . "</td>"); 
    print ("<td><center>" . $row[7] . "</center></td>"); 
    print ("<td align='right'>" . $row[8] . "</td>"); 
    print ("</tr>"); 
    } 

print ("</table>");
return $id_testa;
}

// -------------------------------------------
// leggo le bolle che derivano da questa testa
// -------------------------------------------
function doc_ddt($id_testa,$connectionstring) {
global $lang, $str_evasocon, $str_dataddt, $str_numero, $str_colli, $str_peso, $str_sped, $str_telefono;

$Query = "SELECT DISTINCT DOCTES.DATADOC, DOCTES.NUMERODOC, VETTORI.DESCRIZION, DOCTES.ID, ";
$Query = $Query . "VETTORI.TELEFONO, DOCTES.COLLI, DOCTES.PESOLORDO ";
$Query = $Query . "FROM DOCTES INNER JOIN DOCRIG ON DOCTES.ID = DOCRIG.ID_TESTA ";
$Query = $Query . "LEFT OUTER JOIN VETTORI ON DOCTES.VETTORE1 = VETTORI.CODICE ";
$Query = $Query . "WHERE DOCRIG.RIFFROMT = $id_testa "; 
$Query = $Query . "AND DOCTES.TIPODOC != \"PL\" "; 
//$Query = $Query . "GROUP BY DOCTES.ID";
//execute query 
//echo $Query; 
$queryexe = db_query($connectionstring, $Query) or die(mysql_error()); 


print('<br><center><h3 class="name">' . $str_evasocon[$lang] . '</h3></center>');
print('<table width="100%" border="0" cellspacing="1" cellpadding="1" bgcolor="#000000"> ');
print('  <tr bgcolor="#CCFFFF">  ');
print('    <th height="22"><b>' . $str_dataddt[$lang] . '</b></th> ');
print('    <th height="22"><b>' . $str_numero[$lang] . '</b></th> ');
print('    <th height="22"><b>' . $str_colli[$lang] . '</b></th> ');
print('    <th height="22"><b>' . $str_peso[$lang] . '</b></th> ');
print('    <th height="22"><b>' . $str_sped[$lang] . '</b></th> ');
print('    <th height="22"><b>' . $str_telefono[$lang] . '</b></th> ');
print('  </tr>'); 



//query database 
    while($row = db_fetch_row($queryexe)) 
    { 
    $name = format_date($row[0]); 
    $addr = $row[1]; 
    $stato = $row[2];
    $id = $row[3];  
    $telefono = $row[4];
    $colli = $row[5];
    $peso = $row[6];
     
    //format results 
    print ("<tr bgcolor='#ccffcc'>"); 
    print ("<td><a href=\"ddt-detail.php?id=" . $row[3] . "\" >$name</a></td>"); 
    print ("<td>$addr</td>"); 
    print ("<td align='right'>$colli</td>"); 
    print ("<td align='right'>$peso</td>"); 
    print ("<td>$stato</td>"); 
    print ("<td>$telefono</td>"); 
    print ("</tr>"); 
    }

print ("</table>");
return $id;

}    


// -------------------------------------------
// leggo le fatture che derivano dalle bolle
// -------------------------------------------
function doc_fatt($id_testa,$connectionstring) {
global $lang, $str_fatturatocon, $str_data, $str_numero;
$Query = "SELECT DISTINCT DOCTES.DATADOC, DOCTES.NUMERODOC, DOCTES.ID ";
$Query = $Query . "FROM DOCTES INNER JOIN DOCRIG ON DOCTES.ID = DOCRIG.ID_TESTA ";
$Query = $Query . "WHERE DOCRIG.RIFFROMT = $id_testa "; 
//$Query = $Query . "GROUP BY DOCTES.NUMERODOC";
//execute query 
// echo $Query;
$queryexe = db_query($connectionstring, $Query) or die(mysql_error()); 


print('<br><center><h3 class="name">' . $str_fatturatocon[$lang] . '</h3></center>');
print('<table width="100%" border="0" cellspacing="1" cellpadding="1" bgcolor="#000000"> ');
print('  <tr bgcolor="#CCFFFF">  ');
print('    <th height="22"><b>' . $str_data[$lang] . '</b></th> ');
print('    <th height="22"><b>' . $str_numero[$lang] . '</b></th> ');
print('  </tr>'); 



//query database 
    while($row = db_fetch_row($queryexe)) 
    { 
    $name = format_date($row[0]); 
    $addr = $row[1]; 
    $id = $row[2];
     
    //format results 
    print ("<tr bgcolor='#ccffcc'>"); 
    print ("<td><a href=\"fatt-detail.php?id=" . $row[2] . "\" >$name</a></td>"); 
    print ("<td>$addr</td>"); 
    print ("</tr>"); 
    }

print ("</table>");
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
//execute query 
// echo $Query;
$queryexe = db_query($connectionstring, $Query) or die(mysql_error()); 


print('<br><center><h3 class="name">' . $str_scadenze[$lang] . '</h3></center>');
print('<table width="100%" border="0" cellspacing="1" cellpadding="1" bgcolor="#000000"> ');
print('  <tr bgcolor="#CCFFFF">  ');
print('    <th height="22"><b>' . $str_tipo[$lang] . '</b></th> ');
print('    <th height="22"><b>' . $str_scadenza[$lang] . '</b></th> ');
print('    <th height="22"><b>' . $str_importo[$lang] . '</b></th> ');
print('    <th height="22"><b>' . $str_pagato[$lang] . '</b></th> ');
print('  </tr>'); 



//query database 
    while($row = db_fetch_row($queryexe)) 
    { 
    $name = scad_tipo($row[4], $lang); 
    $addr = format_date($row[1]); 
    $importo = $row[2];
    $pagato = $row[3];
     
    //format results 
    print ("<tr bgcolor='#ccffcc'>"); 
    print ("<td>$name</td>"); 
    print ("<td>$addr</td>"); 
    print ("<td>$importo</td>"); 
    print ("<td>$pagato</td>"); 
    print ("</tr>"); 
    }

print ("</table>");
}

?>
