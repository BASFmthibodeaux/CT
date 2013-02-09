/**
MoneyTrack objects


Created 07May12 JL

IMPORTANT:
on each HTML that this combo is created you need to create the following functions (events of the component):

onComboAccountsChangeValue

*/
function comboUsers() {
	// PROPERTIES ----------------------------------------------------------------
	this.connectionString = "";
	this.username = "";
	this.destinationDiv = "";
	this.hash = "Nada";
	
	// ---------------------------------------------------------------------------
	this.createCombo = 
			function () {
		    	var ds = new kendo.data.DataSource({ 
		    			transport: {
		    				read: {
		    					url: ConnectionString+"/views/list_users.php",
		    					data: {
		    						cHash: this.hash,
		    						username: this.username
		    					}
		    				},
		    			},

	    				schema: {
	    					type: "xml",
	    					data: "/people/person",
	    					model: {
	    						fields: {
	    							username: "username/text()",
	    							name: "name/text()"
	    						}
	    					}
	    				}
		    		}
		    	);

		    	if (this.destinationDiv == "" || this.destinationDiv == undefined) {
		    		alert ("WARNING: comboUsers.js - destinationDiv is not defined.");
		    	}
				
		    	if ($(this.destinationDiv) == undefined) {
		    		alert ("WARNING: comboUsers.js -can't find "+this.destinationDiv+" div tag in HTML file.");
		    	}
		    	
                $(this.destinationDiv).kendoDropDownList({
	            	dataSource: ds,
			        dataTextField: "name",
        			dataValueField: "username",
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
        			template: '<div width="250"><b>${ data.name }</b> - ${ data.username } </div>'
	            });
            };
}

