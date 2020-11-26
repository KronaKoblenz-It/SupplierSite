<?php 
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2017 by Roberto Ceccarelli                        */
/* http://strawberryfield.altervista.org								*/
/* 																		*/
/************************************************************************/

$connectionstring = db_connect($dbase);

$help="04_Inventario_di_magazzino_rel_01_14_del_10_12_14.pdf";

$maga="";
$cookie="";
$fornitore="";
//mode è per attrezzature o normale
$mode="";

function pagestart() {
	global $cookie, $fornitore, $maga, $connectionstring, $mode;
	session_start();
	$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
	$fornitore = $cookie[0];
	//magazzino
	$Query = "SELECT CODICEMAG FROM u_magcdep WHERE CODICECF=\"$fornitore\"";
	$queryexe = db_query($connectionstring, $Query) or die("$Query<br>" . mysql_error() );
	if($row = mysql_fetch_object($queryexe)) {
		$maga = $row->CODICEMAG;
	} else {
	  $maga = "F" . substr($fornitore,2);
	}	
	if( isset($_GET["mode"]) ) {
		$mode = $_GET["mode"];
	}
}

function script() {
	global $mode; 
	$script = <<<EOT
<script type="text/javascript">
//<![CDATA[
var downloadURL = function downloadURL(url) {
    var iframe;
    var hiddenIFrameID = 'hiddenDownloader';
    iframe = document.getElementById(hiddenIFrameID);
    if (iframe === null) {
        iframe = document.createElement('iframe');  
        iframe.id = hiddenIFrameID;
        iframe.style.display = 'none';
        document.body.appendChild(iframe);
    }
    iframe.src = url;   
};

var getxml = function() {
	var maga = document.getElementById("maga").value;
//	downloadURL("xmlinv_base.php?maga="+maga);
	downloadURL("xlsinv_base.php?mode=$mode&maga="+maga);
};
//]]>
</script>
EOT;
	print("$script\n");
}

function download() {
	global $maga;
	$download = <<<EOT
<br>
<input type="hidden" value="$maga" id="maga">
<input type="button" value="Scarica il foglio da compilare" onclick="getxml();" >
<br><br>
<div id="warning">
<span style='font-size: 20px'><b>Si prega di consultare la descrizione della procedura inventariale</b> (<a href="$help" target="_blank" title="ManualeInventario">clicca qui</a>)</br></span>
<i>In caso di necessit� contattare l'Ufficio Ced. (<a href="./mailto.php" target="_blank" title="CompilaMail">inventari@k-group.com</a>)</i>
</div>
<br><br>
EOT;
	print("$download\n");
}

//NUOVO METODO
function upload() {
	global $mode;
	if(isset($_POST['action']) and $_POST['action'] == "upload")
	{
		if(isset($_FILES['file']))
		{
			mkdir(UPLOAD_DIR, 0777, true);
			$file = $_FILES['file'];
			if($file['error'] == UPLOAD_ERR_OK and is_uploaded_file($file['tmp_name']))
			{
			$var = "_".date("H_i_s", time());
				move_uploaded_file($file['tmp_name'], UPLOAD_DIR.$file['name'].$var);
				$fileName = $file['name'].$var;
				header("Location: xls2inv.php?mode=$mode&file=$fileName");
			}
		}
	} else {
		print("<form action=\"inv_xls.php?mode=$mode\" method=\"post\" enctype=\"multipart/form-data\">\n");
		print("<input type=\"hidden\" name=\"action\" value=\"upload\" />\n");
		print("<label for=\"file\">Carica il tuo file:</label>\n");
		print("<input type=\"file\" name=\"file\" id=\"file\">\n"); 
		print("<br><br>\n");
		 
		print("<input type=\"submit\" id=\"btnok\" value=\"Carica il foglio compilato\" >\n");
		print("</form>\n");
	}		
}


// Richiamo del menu principale differenziato per tipo di utente
function goMenu() {
	$desc="Menu inventari";
	print("<a class=\"bottommenu\" href=\"menu-inv.php\"");
	print(" title=\"$desc\">");
	print("<img style=\"border:none;\" src=\"../img/b_home.gif\" alt=\"$desc\">$desc</a>\n");
}

?>
