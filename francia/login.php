<?php 
/************************************************************************/
/* CASASOFT ArcaWeb                              		 		        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2013 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

$pwderror = "";
$pwderrornumber = $_GET["error"];

include("header.php");

if(!empty($pwderrornumber)) {
	if($pwderrornumber == 1) {
		$pwderror = $str_loginerror[$lang];
	}
	if($pwderrornumber == 2) {
		$pwderror = $str_loginerror[$lang];
	}
}

head_base();
banner("Login","");

$labelStyle = "style=\"background-color: #CCFFFF; float: left; text-align: right; width: 150px;\"";
echo "<div style=\"text-align: center;\">\n";
echo "<div style=\"width: 320px; display: block; margin-left: auto; margin-right: auto;\">\n";
echo "<form action='enter.php' method='POST'>\n" ;
echo "<label for=\"codice\" $labelStyle>" . $str_codice[$lang] . "</label>\n";
echo "<input name=\"codice\" id=\"codice\" type=\"text\" size=\"15\">\n"; 
echo "<br>\n";
echo "<label for=\"password\" $labelStyle>Password</label>\n";
echo "<input name=\"password\" id=\"password\" type=\"password\" size=\"15\">\n"; 
if(!empty($pwderror))
{
	echo "<br>\n";
	echo "<p style=\"background-color: red; display: block; text-align: center;\">$pwderror</p>";
}
echo "<br>\n";
echo "<input type=\"submit\" value=\"".$str_entra[$lang]."\">\n";
echo "</form>\n" ;
echo "</div>\n</div>\n";
    
footer();
?>    
