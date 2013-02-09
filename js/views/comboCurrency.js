/**
MoneyTrack objects


Created 31Jul12 JL

IMPORTANT:
on each HTML that this combo is created you need to create the following functions (events of the component):


*/
function comboCurrency() {
	// PROPERTIES ----------------------------------------------------------------
	this.connectionString = "";
	this.destinationDiv = "";
	
	// ---------------------------------------------------------------------------
	this.createCombo = 
			function () {
		    	var ds = new kendo.data.DataSource({ 
		    			data: [
		    			       {value: "ARS",label: "$"},
		    			       {value: "USD",label: "USD"},
		    			       {value: "REA",label: "REA"},
		    			]
		    		}
		    	);

		    	if (this.destinationDiv == "" || this.destinationDiv == undefined) {
		    		alert ("WARNING: comboCurrency.js - destinationDiv is not defined.");
		    	}
				
		    	if ($(this.destinationDiv) == undefined) {
		    		alert ("WARNING: comboCurrency.js -can't find "+this.destinationDiv+" div tag in HTML file.");
		    	}
		    	
                $(this.destinationDiv).kendoDropDownList({
	            	dataSource: ds,
			        dataTextField: "label",
        			dataValueField: "value",
        			suggest: true,		    	        
        			animation: {
	    	             open: {
	    	                 effects: "fadeIn",
	    	                 duration: 300,
	    	                 show: true
	    	             },
	    	             close: {
	    	                 effects: "fadeOut",
	    	                 duration: 300,
	    	                 show: true
	    	             }
	    	        }
	            });
            };
}

