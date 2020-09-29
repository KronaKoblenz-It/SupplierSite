<?php 
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2018 by Roberto Ceccarelli                        */
/* 																		*/
/************************************************************************/

include("header.php"); 
include("db-utils.php");


if(isset($_POST['action']) and $_POST['action'] == "upload")
{
	if(isset($_FILES['file']))
	{
		session_start();
		$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
		$fornitore = $cookie[0];
		$tipodoc = strtoupper($_POST["tipodoc"]);
		define("UPLOAD_DIR", "./uploads/_$tipodoc/".$fornitore."/");

		mkdir(UPLOAD_DIR, 0777, true);
		$file = $_FILES['file'];
		if($file['error'] == UPLOAD_ERR_OK and is_uploaded_file($file['tmp_name']))
		{
		$var = "_".date("H_i_s", time());
			move_uploaded_file($file['tmp_name'], UPLOAD_DIR.$file['name'].$var);
			$fileName = $file['name'].$var;
			header("Location: base_xls2doc.php?file=$fileName&tipodoc=$tipodoc&mode=TEST");
		}
	}
}

function file_request($tipodoc, $descrizione) {
	head();

	session_start();
	$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
	$fornitore = $cookie[0];
	define("UPLOAD_DIR", "./uploads/_$tipodoc/".$fornitore."/");

	banner($descrizione,"");

		print("<form action=\"base_importxls.php\" method=\"post\" enctype=\"multipart/form-data\">\n");
		hiddenField("tipodoc", $tipodoc);
		hiddenField("mode", "TEST");
		hiddenField("action", "upload");
		print("<label for=\"file\">Carica il tuo file:</label>\n");
		print("<input type=\"file\" name=\"file\" id=\"file\">\n"); 
		print("</br>\n");
		 
		print("<input type=\"submit\" id=\"btnok\" value=\"Carica\" >\n");
		print("</form>\n");
	
	print("<br>\n");
	goMain();
	footer();
}
?>