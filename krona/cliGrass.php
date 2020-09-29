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

banner("</br> " . _("Situazione Clienti"),$anno);

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

$Query = "SELECT DISTINCT DOCRIG.CODICECF, ANAGRAFE.DESCRIZION
            FROM DOCRIG
              LEFT JOIN ANAGRAFE ON ANAGRAFE.CODICE = DOCRIG.CODICECF
              LEFT JOIN MAGART ON MAGART.CODICE = DOCRIG.CODICEARTI
            WHERE
              DOCRIG.TIPODOC IN ('BO', 'BV') AND
              YEAR(DOCRIG.DATADOC) = $anno AND
              LEFT(MAGART.GRUPPO,1) = 'C' AND
              ANAGRAFE.U_GRASS = 1
            ORDER BY CODICECF ";

$qx = db_query($connectionstring, $Query) or die(mysql_error());

print("
  </br>
  <div style='text-align: center;'>
    <span id='Title1'> <b>" . _("Elenco Clienti") . " </b> </span>
  </div>
  <div>
    </br>
      <table style='width:45%;' cellspacing='0' align='center'>\n
        <thead> \n
          <tr> \n
            <th> " . _("Cod. Cliente") . " </th> \n
            <th style='text-align: left;' > " . _("Rag. Sociale") . " </th> \n
          </tr>\n
        </thead>\n
        <tbody>\n
  ");

while($rw = mysql_fetch_object($qx)){
  $codCli = $rw->CODICECF;
  $descrCli = $rw->DESCRIZION;
  print ("
    <tr >\n
      <td > <a href='cliGrassDet.php?cli=$codCli&eserc=$anno' > " . $codCli . " </a> </td>\n
      <td style='text-align: left;' >" . $descrCli . "</td>\n
    </tr>\n
  ");
}

print("
    </table>\n
    </br>\n
  </div>
  ");

goMain();
footer();

?>
