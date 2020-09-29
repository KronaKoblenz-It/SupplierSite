<?php

/************************************************************************/
/* CASASOFT ArcaWeb                               		 			    */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2013 by Roberto Ceccarelli                        */
/* http://strawberryfield.altervista.org                                */
/*                                                                      */
/************************************************************************/
if (file_exists('semaforo.txt') == 1) {
    include '_wip.php';
} else {
    header('Location: login.php');
}
