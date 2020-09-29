<?php 
header('Content-Type: application/vnd.ms-excel');
header('Cache-Control: no-cache');
header('Cache-Control: no-store' , false);     // false => this header not override the previous similar header
header("Connection: Keep-Alive"); 
header("Keep-Alive: timeout=300");  
/************************************************************************/
/* Project ArcaWeb                               		 			    */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2012 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/
include("header.php"); 
include("db-utils.php");
$maga = trim($_GET['maga']);
$anno = current_year();
if(date('n') <4) {
	$anno--;
}

header("Content-Disposition: attachment; filename=\"inventario-$maga-$anno.xml\"");
print("<?xml version=\"1.0\" encoding=\"Windows-1252\"?>");

$str1 = <<<EOT
<?mso-application progid="Excel.Sheet"?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:o="urn:schemas-microsoft-com:office:office"
 xmlns:x="urn:schemas-microsoft-com:office:excel"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:html="http://www.w3.org/TR/REC-html40">
 <DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">
  <Author>roberto</Author>
  <LastAuthor>roberto</LastAuthor>
  <Created>2012-11-20T13:44:30Z</Created>
  <LastSaved>2012-11-20T13:45:33Z</LastSaved>
  <Company>Kronakoblenz</Company>
  <Version>12.00</Version>
 </DocumentProperties>
 <ExcelWorkbook xmlns="urn:schemas-microsoft-com:office:excel">
  <WindowHeight>8325</WindowHeight>
  <WindowWidth>14220</WindowWidth>
  <WindowTopX>240</WindowTopX>
  <WindowTopY>135</WindowTopY>
  <ProtectStructure>False</ProtectStructure>
  <ProtectWindows>False</ProtectWindows>
 </ExcelWorkbook>
 <Styles>
  <Style ss:ID="Default" ss:Name="Normal">
   <Alignment ss:Vertical="Bottom"/>
   <Borders/>
   <Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Color="#000000"/>
   <Interior/>
   <NumberFormat/>
   <Protection/>
  </Style>
  <Style ss:ID="s62">
   <NumberFormat ss:Format="Fixed"/>
  </Style>
 </Styles>
 <Worksheet ss:Name="Inventario">
EOT;

$str2 = <<<EOT
   <Column ss:AutoFitWidth="0" ss:Width="66"/>
   <Column ss:Width="116.25"/>
   <Column ss:Width="282.75"/>
   <Column ss:StyleID="s62" ss:AutoFitWidth="0" ss:Width="79.5"/>
   <Column ss:AutoFitWidth="0" ss:Width="116.25"/>
   <Row ss:AutoFitHeight="0">
    <Cell><Data ss:Type="String">Magazzino</Data></Cell>
    <Cell><Data ss:Type="String">Codice</Data></Cell>
    <Cell><Data ss:Type="String">Descrizione</Data></Cell>
    <Cell><Data ss:Type="String">Quantita</Data></Cell>
    <Cell><Data ss:Type="String">Lotto</Data></Cell>
   </Row>
EOT;

$str3 = <<<EOT
  </Table>
  <WorksheetOptions xmlns="urn:schemas-microsoft-com:office:excel">
   <PageSetup>
    <Header x:Margin="0.3"/>
    <Footer x:Margin="0.3"/>
    <PageMargins x:Bottom="0.75" x:Left="0.7" x:Right="0.7" x:Top="0.75"/>
   </PageSetup>
   <Unsynced/>
   <Selected/>
   <Panes>
    <Pane>
     <Number>3</Number>
     <ActiveRow>0</ActiveRow>
     <ActiveCol>0</ActiveCol>
    </Pane>
   </Panes>
   <ProtectObjects>False</ProtectObjects>
   <ProtectScenarios>False</ProtectScenarios>
  </WorksheetOptions>
 </Worksheet>
</Workbook>
EOT;


$connectionstring = db_connect($dbase); 

$out = "";
$Query = "SELECT MAGGIAC.ARTICOLO, MAGART.DESCRIZION, MAGART.LOTTI ";
$Query .= "FROM MAGGIAC INNER JOIN MAGART ON MAGART.CODICE = MAGGIAC.ARTICOLO ";
$Query .= "WHERE MAGGIAC.MAGAZZINO = \"$maga\" AND MAGGIAC.ESERCIZIO=\"$anno\"";
$queryexe = db_query($connectionstring, $Query) or die(mysql_error() ); 
$row = 1;
while($rw = db_fetch_row($queryexe)) {
	$art = $rw[0];
	$desc = $rw[1];
	if($rw[2] > 0) {
		$Query = "SELECT LOTTO FROM MAGGIACL WHERE MAGAZZINO = \"$maga\" AND ARTICOLO = \"$art\" ";
		$qe = db_query($connectionstring, $Query) or die(mysql_error() ); 
		while($rs = db_fetch_row($qe)) {
			$row++;
			$out .= writeRow($maga, $art, $desc, $rs[0]);
		}
	} else {
		$row++;
		$out .= writeRow($maga, $art, $desc, "");
	}
} 
$strn = "<Table ss:ExpandedColumnCount=\"5\" ss:ExpandedRowCount=\"$row\" x:FullColumns=\"1\"
   x:FullRows=\"1\" ss:DefaultRowHeight=\"15\">\n";
   
print($str1 . $strn . $str2 . $out . $str3); 

function writeRow($maga, $art, $desc, $lotto) {
	$desc = xmlentities($desc);
	$art = xmlentities($art);
	$lotto = xmlentities($lotto);
	$out = "<Row ss:AutoFitHeight=\"0\">\n";
	$out .= "<Cell><Data ss:Type=\"String\">$maga</Data></Cell>\n";
	$out .= "<Cell><Data ss:Type=\"String\">$art</Data></Cell>\n";
	$out .= "<Cell><Data ss:Type=\"String\">$desc</Data></Cell>\n";
	$out .= "<Cell><Data ss:Type=\"Number\">0</Data></Cell>\n";
	$out .= "<Cell><Data ss:Type=\"String\">$lotto</Data></Cell>\n";
	$out .= "</Row>\n";
	return $out;
}

function xmlentities($string) {
    return str_replace(array("&", "<", ">", "\"", "'", "“", "”"),
        array("&amp;", "&lt;", "&gt;", "&quot;", "&apos;", "&quot;", "&quot;"), $string);
}
?>