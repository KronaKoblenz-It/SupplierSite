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

if(!empty($pwderrornumber))
{
	if($pwderrornumber == 1)
	{
		$pwderror = $str_loginerror[$lang];
	}
	if($pwderrornumber == 2)
	{
		$pwderror = $str_loginerror[$lang];
	}
}
head();
banner("Login","");

$labelStyle = "style=\"background-color: #CCFFFF; float: left; text-align: right; width: 150px;\"";
echo "<div style=\"text-align: center;\">\n";
echo "<div style=\"width: 320px; display: block; margin-left: auto; margin-right: auto;\">\n";
echo "<form action='enter.php' method='POST'>\n" ;
//    echo "<table style=\"margin-left: auto; margin-right: auto;\">\n<tr>\n" ;   
// echo "<div style=\"align: center;\">\n";
    // creating form with data 
echo "<label for=\"codice\" $labelStyle>" . $str_codice[$lang] . "</label>\n";
echo "<input name=\"codice\" id=\"codice\" type=\"text\" size=\"15\">\n"; 
echo "<br>\n";
 //   echo "</tr>\n<tr>\n";
echo "<label for=\"password\" $labelStyle>Password</label>\n";
//	echo "<td style='background-color: #ccffFF;'><input name='password' type='password' size='15'></td>\n";
echo "<input name=\"password\" id=\"password\" type=\"password\" size=\"15\">\n"; 
//echo "</tr>\n";
if(!empty($pwderror))
{
	echo "<br>\n";
	echo "<p style=\"background-color: red; display: block; text-align: center;\">$pwderror</p>";
//	echo "</tr>\n";
}
echo "<br>\n";
echo "<input type=\"submit\" value=\"".$str_entra[$lang]."\">\n";
//echo "</tr>\n";
//echo "</table>\n";
echo "</form>\n" ;
echo "</div>\n</div>\n";
    
footer();
?>    
