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

session_start();
$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
$fornitore = $cookie[0];
define("UPLOAD_DIR", "./uploads/".$fornitore."/");

banner("Caricamento bolle da file Excel","");
//print(UPLOAD_DIR);

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
			header("Location: xls2tbl.php?file=$fileName");
        }
    }
} else {
	print("<form action=\"ddtimportxls.php\" method=\"post\" enctype=\"multipart/form-data\">\n");
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