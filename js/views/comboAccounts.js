/**
MoneyTrack objects


Created 07May12 JL

IMPORTANT:
on each HTML that this combo is created you need to create the following functions (events of the component):

onComboAccountsChangeValue

*/
function comboAccounts() {
	// PROPERTIES ----------------------------------------------------------------
	this.connectionString = "";
	this.holder = "";
	this.destinationDiv = "";
	this.hash = "Nada";
	
	this.isInitialized = false;
	
	// ---------------------------------------------------------------------------
	this.createCombo = 
			function () {
		    	var ds = new kendo.data.DataSource({ 
		    			transport: {
		    				read: {
		    					url: ConnectionString+"/views/list_accounts.php",
		    					data: {
		    						cHash: this.hash,
		    						holder: this.holder
		    					}
		    				},
		    			},
		    			autoBind: true,
	    				schema: {
	    					type: "xml",
	    					data: "/accounts/account",
	    					model: {
	    						fields: {
	    							acc_id: "acc_id/text()",
	    							bank: "bank/text()",
	    							card_type: "card_type/text()",
	    							number: "number/text()"
	    						}
	    					}
	    				}
		    		}
		    	);

		    	if (this.destinationDiv == "" || this.destinationDiv == undefined) {
		    		alert ("WARNING: comboAccounts.js - destinationDiv is not defined.");
		    	}
				
		    	if ($(this.destinationDiv) == undefined) {
		    		alert ("WARNING: comboAccounts.js -can't find "+this.destinationDiv+" div tag in HTML file.");
		    	}
		    	
 		    	if (this.isInitialized) { 
 		    		var temp = $(this.destinationDiv).data("kendoDropDownList");
 		    		temp.setDataSource(ds);
 		    		temp.refresh();

 		    	} else {
 	                $(this.destinationDiv).kendoDropDownList({
 		            	dataSource: ds,
 				        dataTextField: "card_type",
 	        			dataValueField: "acc_id",
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
 		    	        },
 	        			template: '<div width="250"><b>${ data.card_type } ${ data.number }</b> - ${ data.bank } </div>'
 		            });
 		    		this.isInitialized= true;
 		    		
 		    	}
            };
            
}
	
