function graphAccountEvolution() {
	
	this.connectionString = "";
	this.account = "";
	this.destinationDiv = "";
	this.hash = "Nada"; //default hasta que este bien...
	
	this.createChart = 
			function () {
				//VALIDACIONES internas de desarrollo, para avisar que no se esta configurando bien el control
		    	if (this.destinationDiv == "" || this.destinationDiv == undefined) {
		    		alert ("WARNING: comboAccounts.js - destinationDiv is not defined.");
		    	}
				
		    	if ($(this.destinationDiv) == undefined) {
		    		alert ("WARNING: comboAccounts.js -can't find "+this.destinationDiv+" div tag in HTML file.");
		    	}
    	
		    	var ds = new kendo.data.DataSource({ 
		    			transport: {
		    				read: {
		    					url: ConnectionString+"/views/graph.php",
		    					data: {
		    						cHash: this.hash,
		    						account: this.account
		    					}
		    				},
		    			},
	    				schema: {
	    					type: "xml",
	    					data: "/graph/period",
	    					model: {
	    						fields: {
	    							month: "month/text()",
	    							one_payment: "one_payment/text()",
	    							more_payments: "more_payments/text()"
	    						}
	    					}
	    				}
		    		}
		    	);

				
                $(this.destinationDiv).kendoChart({
                	dataSource: ds,
                    theme: $(document).data("kendoSkin") || "default",
                    title: {
                        text: "Pagos a realizar"
                    },
                    legend: {
                        visible: false
                    },
                    seriesDefaults: {
                        type: "column"
                    },
                    categoryAxis: { 
                    	field: "month" 
                    },
                    series: [{
                    	field: "one_payment",
                        name: "Un Pago",
                    }, {
                    	field: "more_payments",
                        name: "En cuotas",
                    }],
                    seriesColors: ["#cd1533", "#d43851", "#dc5c71", "#e47f8f", "#eba1ad",
                                   "#009bd7", "#26aadd", "#4db9e3", "#73c8e9", "#99d7ef"]
                    
                });
            };
}