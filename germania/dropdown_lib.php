<?php 
/************************************************************************/
/* Project ArcaWeb                               				        */
/* ===========================                                          */
/*                                                                      */
/* Copyright (c) 2003-2011 by Roberto Ceccarelli                        */
/* 																		*/
/************************************************************************/

print("<script type=\"text/javascript\" src=\"dropdown_lib.js\"></script>\n");

function ddBox($id) {
  $ret = "<select name=\"$id\" id=\"$id\" style=\"\" ";
  $ret .= "onKeyDown=\"fnKeyDownHandler_A(this, event);\" ";
  $ret .= "onKeyUp=\"fnKeyUpHandler_A(this, event); return false;\" ";
  $ret .= "onKeyPress=\"return fnKeyPressHandler_A(this, event);\" ";
  $ret .= "onChange=\"fnChangeHandler_A(this);\" ";
  $ret .= "onFocus=\"fnFocusHandler_A(this);\">\n";
// This is the Editable Option 
  $ret .= "<option value=\"\">--?--</option>\n";
  $ret .= "</select>\n";
  
  $ret .= "<input type=\"text\" id=\"txt$id\" style=\"visibility:hidden;display:none;width:150pt\" ";
  $ret .= "value=\"select option or type here\" ";
  $ret .= "onfocus=\"this.value = document.getElementById('$id').options[vEditableOptionIndex_A].text\" ";
  $ret .= "onKeyUp=\"document.getElementById('$id').options[vEditableOptionIndex_A].text=this.value; ";
  $ret .= "document.getElementById('$id').options[vEditableOptionIndex_A].value=this.value;\" ";
  $ret .= "onblur=\"document.getElementById('$id').options[vEditableOptionIndex_A].text=this.value; ";
  $ret .= "document.getElementById('$id').options[vEditableOptionIndex_A].value=this.value; ";
  $ret .= "document.getElementById('$id').focus();\">\n";
  return $ret;
}
?>