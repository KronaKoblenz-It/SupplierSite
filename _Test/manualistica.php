<?php 
/************************************************************************/
/* CASASOFT ArcaWeb                               		      			*/
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2012 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php");
head();

banner("Manualistica", "");

$dir = "./manuali";
$dh  = opendir($dir);
$nFiles = 0;
while (false !== ($filename = readdir($dh))) {
    $files[] = $filename;
}

print("<table class=\"list\">\n");
foreach ($files as &$value) {
	$extension = trim(substr($value, -4, 4));
	if($extension == '.pdf'){
		print("<tr class=\"list\">\n");
		print("<th class=\"menu\">");
		print("<a href=\"./manuali/" .$value. "\">");
		print("<img src=\"../img/10_pdf.gif\" alt=\"download\" style=\"border: none;\">");
		print("&nbsp;$value</a>");
		print("</th>\n"); 
		print("</tr>\n");
	}
}

print("</table>\n");

print("<br>\n");
goMain();
footer();
?>