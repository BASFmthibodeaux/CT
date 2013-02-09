/**
MoneyTrack objects


Created 07May12 JL

IMPORTANT:
on each HTML that this combo is created you need to create the following functions (events of the component):

onComboAccountsChangeValue

*/
function gridPurchases() {
	// PROPERTIES ----------------------------------------------------------------
	this.connectionString = "";
	this.credit_card = "";
	this.purchased_by = "";
	this.destinationDiv = "";
	this.limit="";
	this.orderBy = "TIMESTAMP";
	this.hash = "Nada";
	
	// ---------------------------------------------------------------------------
	this.createGrid = 
			function () {
		    	var ds = new kendo.data.DataSource({ 
		    			transport: {
		    				read: {
		    					url: ConnectionString+"/views/list_purchases.php",
		    					data: {
		    						cHash: this.hash,
		    						credit_card: this.credit_card,
		    						order_by: this.orderBy,
		    						purchased_by: this.purchased_by,
		    						limit: this.limit
		    					}
		    				},
		    			},

	    				schema: {
	    					type: "xml",
	    					data: "/purchases/purchase",
	    					model: {
	    						fields: {
	    							pur_id: "pur_id/text()",
	    							description: "description/text()",
	    					 		date: "date/text()",
	    							purchased_by: "purchased_by/text()",
	    							credit_card: "credit_card/text()",
	    							card_type: "card_type/text()",
	    							value: "value/text()",
	    							payments: "payments/text()",
	    							timestamp: "timestamp/text()"
	    						}
	    					}
	    				}

		    		}
		    	);

	    	
		    	if (this.destinationDiv == "" || this.destinationDiv == undefined) {
		    		alert ("WARNING: gridPurchases.js - destinationDiv is not defined.");
		    	}
				
		    	if ($(this.destinationDiv) == undefined) {
		    		alert ("WARNING: gridPurchases.js -can't find "+this.destinationDiv+" div tag in HTML file.");
		    	}

		    	
                $(this.destinationDiv).kendoGrid({ 
                	dataSource: ds,
        		    columns: [
        		               {
        		            	   title: "Fecha",
        		                   field: "date",
        		                   width: 90
        		               },
        		               {
        		            	   title: "Descripcion",
        		                   field: "description", 		                	   
        		                   template: "<div id='title'>${ card_type } ${ credit_card } ${ description }</div>"
        		              },
        		              {
        		            	  title: "Valor",
       		                   	  field: "value",
       		                   	  template: "<div  style='text-align:right'>${ value }</div>",
       		                   	  width: 80
       		                   		
        		              }

        		           ]
	            });
                
            };
}

