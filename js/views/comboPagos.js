/**
MoneyTrack objects


Created 29Jul12 JL

IMPORTANT:
on each HTML that this combo is created you need to create the following functions (events of the component):

onComboAccountsChangeValue

*/
function comboPagos() {
	// PROPERTIES ----------------------------------------------------------------
	this.connectionString = "";
	this.account = "";
	this.destinationDiv = "";
	this.hash = "Nada";
	
	// ---------------------------------------------------------------------------
	this.createCombo = 
			function () {
		


		    	if (this.destinationDiv == "" || this.destinationDiv == undefined) {
		    		alert ("WARNING: comboPagos.js - destinationDiv is not defined.");
		    	}
				
		    	if ($(this.destinationDiv) == undefined) {
		    		alert ("WARNING: comboPagos.js -can't find "+this.destinationDiv+" div tag in HTML file.");
		    	}
		    	
		    	
                $(this.destinationDiv).kendoNumericTextBox({
                    value: 1,
                    min: 1,
                    max: 60,
                    step: 1,
                    format: "i",
                    decimals: 0,
                    placeholder: "Pagos",
                    upArrowText: "mas pagos",
                    downArrowText: "menos pagos"
	            });
            };
}

