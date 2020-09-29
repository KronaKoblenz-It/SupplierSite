<?php

include 'header.php';
include 'db-utils.php';

head();
session_start();
$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
$forn = $cookie[0];
if($forn!='F02707'){
  header("Location: http://intranet.krona.it/krona/");
}
$anno = current_year();
$codCli = $_GET['cli'];
$anno = $_GET['eserc'];

$connectionstring = db_connect($dbase);

?>
<style type="text/css">
	table {
		overflow:hidden;
		border:1px solid #d3d3d3;
		background:#ccffcc;
		width:70%;
		margin: 0 auto 0;
		-moz-border-radius:5px; /* FF1+ */
		-webkit-border-radius:5px; /* Saf3-4 */
		border-radius:5px;
		-moz-box-shadow: 0 0 4px rgba(0, 0, 0, 0.2);
		-webkit-box-shadow: 0 0 4px rgba(0, 0, 0, 0.2);
		font-family: verdana, helvetica;
		font-size: 10pt;
	}
	th, td {padding:5px 15px 5px; text-align:center;}
	th {padding-top:10px; text-shadow: 1px 1px 1px #fff; background:#e8eaeb; }
	th {
		background: -moz-linear-gradient(100% 30% 90deg, #ededed, #e8eaeb);
		background: -webkit-gradient(linear, 0% 0%, 0% 50%, from(#e8eaeb), to(#ededed));
	}
	td {border-top:1px solid #e0e0e0; border-right:1px solid #e0e0e0;}
	tr.odd-row td {background:#ccffcc;}
	td.first, th.first {text-align:left}
	td.last {border-right:none;}
	tr:first-child th.first {
		-moz-border-radius-topleft:5px;
		-webkit-border-top-left-radius:5px; /* Saf3-4 */
	}
	tr:first-child th.last {
		-moz-border-radius-topright:5px;
		-webkit-border-top-right-radius:5px; /* Saf3-4 */
	}
	tr:last-child td.first {
		-moz-border-radius-bottomleft:5px;
		-webkit-border-bottom-left-radius:5px; /* Saf3-4 */
	}
	tr:last-child td.last {
		-moz-border-radius-bottomright:5px;
		-webkit-border-bottom-right-radius:5px; /* Saf3-4 */
	}
</style>
<?php

$Query = "SELECT ANAGRAFE.DESCRIZION AS RAGSOC FROM ANAGRAFE WHERE ANAGRAFE.CODICE = '$codCli'";
$qx = db_query($connectionstring, $Query) or die(mysql_error());
$rw = mysql_fetch_object($qx);
$descrCli = $rw->RAGSOC;

banner("</br> " . _("Situazione Clienti"),$anno);

$Query = "SELECT DOCRIG.CODICEARTI, DOCRIG.TIPODOC, DOCRIG.NUMERODOC, MAX(DOCRIG.DATADOC) AS DATA,
              SUM(DOCRIG.QUANTITA*DOCRIG.FATT) AS QTA, MAX(MAGART.DESCRIZION) AS DESCART, MAX(MAGART.UNMISURA) AS UM,
              MAX(MAGART.UNMISURA2) AS UM2, MAX(MAGART.FATT2) AS FT2, MAX(MAGART.UNMISURA3) AS UM3, MAX(MAGART.FATT3) AS FT3
            FROM DOCRIG
              LEFT JOIN ANAGRAFE ON ANAGRAFE.CODICE = DOCRIG.CODICECF
              LEFT JOIN MAGART ON MAGART.CODICE = DOCRIG.CODICEARTI
            WHERE
              DOCRIG.TIPODOC IN ('BO', 'BV') AND
              YEAR(DOCRIG.DATADOC) = $anno AND
              LEFT(MAGART.GRUPPO,1) = 'C' AND
              DOCRIG.CODICECF = '$codCli'
            GROUP BY CODICEARTI, TIPODOC, NUMERODOC
            ORDER BY CODICEARTI ";

$qx = db_query($connectionstring, $Query) or die(mysql_error());

print("
  </br>
  <div style='text-align: center;'>
    <span id='Title1'> <b>" . _("Articoli Venduti a ") . "</br></br> $codCli - $descrCli </b> </span>
  </div>
  <div>
    </br>
      <table cellspacing='0' align='center'>\n
        <thead> \n
          <tr> \n
            <th> " . _("Cod. Articolo") . " </th> \n
            <th style='text-align: left;' > " . _("Descrizione") . " </th> \n
            <th> " . _("Qta") . " </th> \n
            <th> " . _("U.M.") . " </th> \n
            <th> " . _("Qta PZ") . " </th> \n
            <th> " . _("Rif. Doc.") . " </th> \n
            <th> " . _("Data Doc.") . " </th> \n
          </tr>\n
        </thead>\n
        <tbody>\n
  ");

$lastart = '';

while($rw = mysql_fetch_object($qx)){
  $descrCli = $rw->RAGSOC;
  $tipoDoc = $rw->TIPODOC;
  $numDoc = $rw->NUMERODOC;
  $dataDoc = $rw->DATA;
  $codArt = $rw->CODICEARTI;
  $descArt = $rw->DESCART;
  $qta = $rw->QTA;
  $unmisura = $rw->UM;
  $qtaPZ = ($rw->UM != 'PZ' ? ($rw->UM2 != 'PZ' ? ($rw->UM3 != 'PZ' ? '' : ($qta / $rw->FT3)) : ($qta / $rw->FT2)) : $qta );

  print ("
    <tr >\n
    ");

  if(strcmp($lastart, $codArt)!==0 && !empty($lastart)){
    print ("
        <td colspan=7> </td>\n
        </tr>\n
        <tr >\n
      ");
  }
  $lastart = $codArt;

  print ("
      <td > <a href='giacArtDetail.php?art=$codArt&maga=GRASS&esercizio=$anno' target=_blank >  <strong>" . $codArt . "</strong> </td>\n
      <td style='text-align: left;' >" . $descArt . "</td>\n
      <td > " . xRound2($qta) . " </td>\n
      <td > $unmisura </td>\n
      <td > " . xRound2($qtaPZ) . " </td>\n
      <td > " . $tipoDoc . " " . $numDoc . " </td>\n
      <td > " . format_date($dataDoc) . " </td>\n
    </tr>\n
  ");
}

print("
    </table>\n
    </br>\n
  </div>
  ");

print("
    </br>\n
    <a class='bottommenu' href='cliGrass.php'>
      <img style='border: 0px;' src='../img/05_edit.gif' alt='" . _("Menu precedente") ."'>" . _("Menu precedente") ."
    </a>\n
  ");

goMain();
footer();

?>
