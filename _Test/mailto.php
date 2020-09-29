<?php 
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2013 by Luca Ciotti		                        */
/* 																		*/
/* 																		*/
/************************************************************************/

include("header.php"); 
head();

session_start();
$cookie = preg_split("/\|/",$_SESSION['CodiceAgente']);
$maga = "F" . substr($cookie[0],2);

banner("Invio Email Automatico",$cookie[1]);


if (isset($_REQUEST['message']))
//if "email" is filled out, send email
  {
  //send email
  $email = $_REQUEST['email'] ;
  $subject = $_REQUEST['subject'] ;
  $message = $_REQUEST['message'] ;
  mail("ced@k-group.com", $subject,
  $message, "From:" . $email);
  echo "Email Inviata Correttamente";
  }
else
//if "email" is not filled out, display the form
  {
  echo "<form method='post' action='mailto.php'>
  Vs. Email: <input name='email' type='text'><br>
  Message:<br>
  <textarea name='message' rows='15' cols='40'>
  </textarea><br>
  <input type='hidden' name='subject' value='Problemi Inventario: $cookie[0] - $cookie[1]'>
  <input type='submit'>
  </form>";
  }
 print("<br>\n");
goMain();
footer();
?>
