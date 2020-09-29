// ***********************************************************************
// Project ArcaWeb                               				        
// ===========================                                          
//                                                                      
// Copyright (c) 2003-2011 by Roberto Ceccarelli                        
//                                                                      
// **********************************************************************

function insertOptionBefore(id, s)
{
  var elSel = document.getElementById(id);
  if (elSel.selectedIndex >= 0) {
    var elOptNew = document.createElement('option');
    elOptNew.text = s;
    elOptNew.value = s;
    var elOptOld = elSel.options[elSel.selectedIndex];  
    try {
      elSel.add(elOptNew, elOptOld); // standards compliant; doesn't work in IE
    }
    catch(ex) {
      elSel.add(elOptNew, elSel.selectedIndex); // IE only
    }
  }
}

function removeOptionSelected(id)
{
  var elSel = document.getElementById(id);
  var i;
  for (i = elSel.length - 1; i>=0; i--) {
    if (elSel.options[i].selected) {
      elSel.remove(i);
    }
  }
}

function appendOptionLast(id, s)
{
  var elOptNew = document.createElement('option');
  elOptNew.text = s;
  elOptNew.value = s;
  var elSel = document.getElementById(id);

  try {
    elSel.add(elOptNew, null); // standards compliant; doesn't work in IE
  }
  catch(ex) {
    elSel.add(elOptNew); // IE only
  }
}

function appendOptionLast2(id, s, v)
{
  var elOptNew = document.createElement('option');
  elOptNew.text = s;
  elOptNew.value = v;
  var elSel = document.getElementById(id);

  try {
    elSel.add(elOptNew, null); // standards compliant; doesn't work in IE
  }
  catch(ex) {
    elSel.add(elOptNew); // IE only
  }
}

function removeOptionLast(id)
{
  var elSel = document.getElementById(id);
  if (elSel.length > 0)
  {
    elSel.remove(elSel.length - 1);
  }
}
