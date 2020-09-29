<?php 
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2013 by Roberto Ceccarelli                        */
/* 																		*/
/************************************************************************/

include("header.php"); 
include("db-utils.php");
head();
banner("Caricamento report Excel");

session_start();
$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
$fornitore = $cookie[0];
$maga = "F" . substr($fornitore, -4);
$date = date("d_m_y", time());
define("UPLOAD_DIR", "./uploads/".$date."/");

//print(UPLOAD_DIR);

if(isset($_POST['action']) and $_POST['action'] == "upload")
{
    if(isset($_FILES['file']))
    {
		if(!mkdir(UPLOAD_DIR, 0777, true)){
			print("Error directory");
		}
		$file = $_FILES['file'];
				
		if($file['error'] == UPLOAD_ERR_OK and is_uploaded_file($file['tmp_name']))
		{
			$allowedExts = array("xls", "xlsx", "ods");
			$temp = explode(".", $file['name']);
			$extension = end($temp);
			if ((($file['type'] == 'application/vnd.oasis.opendocument.spreadsheet') || ($file['type'] == 'application/vnd.ms-excel')) && in_array($extension, $allowedExts)) {				
				$var = date("H_i_s", time());
				move_uploaded_file($file['tmp_name'], UPLOAD_DIR.$var."_Russia.xls");
				$fileName = $var."_Russia.xls";  //$file['name'].$var;
				print("Caricato!");
				header("Location: xls2tbl.php?file=$fileName");
			}
			else {
				print("Formato File non consentito!");
			}
		}
    }
} else {
	print("<form action=\"importxls.php\" method=\"post\" enctype=\"multipart/form-data\">\n");
	print("<input type=\"hidden\" name=\"action\" value=\"upload\" />\n");
	print("<label for=\"file\">Carica il tuo file:</label>\n");
	print("<input type=\"file\" name=\"file\" id=\"file\">\n"); 
	print("</br>\n");
	 
	print("<input type=\"submit\" id=\"btnok\" value=\"Carica\" >\n");
	print("</form>\n");
}
print("<br>\n");
goMain();
footer();
?>