// ***********************************************************************
// Project ArcaWeb                               				        
// ===========================                                          
//                                                                      
// Copyright (c) 2003-2012 by Roberto Ceccarelli                        
//                                                                      
// **********************************************************************
 
function checkBarcode39(obj)  {
	var cleanString = obj.value.replace(/[^A-Z0-9]/g, "-");
	if( cleanString != obj.value ) {
		alert("Il codice lotto verrà modificato in: "+cleanString);
		obj.value = cleanString;
	}	
}
	

	
