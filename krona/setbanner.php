<?php
include_once "db-utils.php";

$idbanner = $_GET["idbanner"];
$codicecf = $_GET["codicecf"];
$dbase = $_GET["dbase"];

$connectionstring = db_connect($dbase);

$query = "INSERT INTO U_BANNERCH (codicecf, id_banner) VALUES ('$codicecf', $idbanner)";
$queryexe = db_query($connectionstring, $query) or die("$query<br>" . mysqli_error($connectionstring) );

db_close($connectionstring);
?>