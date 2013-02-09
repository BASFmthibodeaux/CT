/**
MoneyTrack objects


Created 31Jul12 JL

IMPORTANT:
on each HTML that this combo is created you need to create the following functions (events of the component):


*/
function comboPeriodos() {
	// PROPERTIES ----------------------------------------------------------------
	this.connectionString = "";
	this.destinationDiv = "";
	
	// ---------------------------------------------------------------------------
	this.createCombo = 
			function () {
		    	var ds = new kendo.data.DataSource({ 
		    			data: [
		    			       {value: "TODAY",label: "Hoy + 9 meses"},
		    			       {value: "-6",label: "Ultimos 6 meses"},
		    			       {value: "-9",label: "Ultimos 9 meses"},
		    			       {value: "-12",label: "Ultimos 12 meses"},
		    			]
		    		}
		    	);

		    	if (this.destinationDiv == "" || this.destinationDiv == undefined) {
		    		alert ("WARNING: comboPeriodos.js - destinationDiv is not defined.");
		    	}
				
		    	if ($(this.destinationDiv) == undefined) {
		    		alert ("WARNING: comboPeriodos.js -can't find "+this.destinationDiv+" div tag in HTML file.");
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

