<?php 
/************************************************************************/
/* CASASOFT ArcaWeb                              		 		        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2011 by Roberto Ceccarelli                        */
/*                                                                      */
/************************************************************************/

include("header.php");
head();
banner("Login","");

    echo "<center>";
    echo "<form action='enter.php' method='POST'>" ;
    echo "<table  border='0' cellspacing='3' cellpadding='1' ><tr>" ;   
    // creating form with data 
    echo "<td bgcolor='#CCFFFF'>" . $str_codice[$lang] . "</td><td bgcolor='#ccffcc'><input name='codice' type='text' size='15'></td>"; 
    echo "</tr><tr>";
    echo "<td bgcolor='#CCFFFF'>Password</td><td bgcolor='#ccffcc'><input name='password' type='password' size='15'></td>"; 
    echo "</tr></table><input type='submit' value='Entra'></form>" ;
    echo "</center>";
    
footer();
?>    
