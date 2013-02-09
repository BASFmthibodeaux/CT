function graphAccountEvolution() {
	
	this.connectionString = "";
	this.account = "";
	this.destinationDiv = "";
	this.periodos = "";
	
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
		    						cHash: "nada1",
		    						account: this.account,
		    						period_from: this.periodos
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
                    theme: $(document).data("kendoSkin") || "blueOpal",
                    title: {
                        text: "Pagos a realizar",
                        visible: false
                    },
                    legend: {
                        visible: true,
                        position: "bottom"
                    },
                    categoryAxis: { 
                    	field: "month", 
                    	legend: "Mes"
                    },                        
                    tooltip: {
                        visible: true,
                        template: "#= series.value #, #= series.name #"
                    },
                    valueAxis: {
                        labels: {
                            template: "#= kendo.format('{0:N0}', value) # $"
                        }
                    },

                    series: [{
                    	type:"column",
                    	field: "one_payment",
                        name: "Un Pago",
                        labels: {
                        	visible: true,
                        	position: "center"
                        }
                    }, {
                    	type:"line",
                    	field: "more_payments",
                        name: "En cuotas",
                        labels: {
                        	visible: true
                        }
                    }],
                    seriesColors: ["#4db9e3","#cd1533" , "#dc5c71", "#e47f8f", "#eba1ad",
                                   "#009bd7", "#26aadd", "#4db9e3", "#73c8e9", "#99d7ef"]
                    
                });
            };
}