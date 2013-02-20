<?php
/*
 * -----------------------------------------------------
 * Lista de aplicaciones
 * -----------------------------------------------------
 * Este PHP se utiliza para mostrar la lista de aplicaciones
 * 
 * PARAMETROS:
 * 
 * 
 * TODO: Agregar hash de seguridad para que el PHP sea llamado desde una conexion valida
 * 
 * Log de modificaciones
 * 
 * 18/02/2013 JL V000
 * 	 -Creacion del archivo
 * -----------------------------------------------------
 *  
*/

/* PARAMETROS

cHash			hash de seguridad

-- campos:
bank_id

*/
require_once '../global/global_variables.php';
require_once '../global/database_functions.php';
require_once $functions_path . '/connect.php';

	header('Content-type: text/xml');
	header('Pragma: public');
	header('Cache-control: private');
	header('Expires: -1');

    import_request_variables("gP","rvar_");


	$NL = chr(13);
	$link=connect_database($main_database);
	
	print '<?xml version="1.0" encoding="iso-8859-1"?>'. $NL;
	print ('<status>'.$NL);
	
	if ($rvar_cHash !="" and $rvar_bank_id != "" and $rvar_due_period !="" ) {
		
		//TODO: revisar HASH primero!
				
		$query = "select cc_id,acc_card_type,cc_number, cc_holder, sum(fp_value) value from credit_cards,accounts, future_payments"
                        ." where acc_id= cc_acc_id and fp_cc_id = cc_id and acc_bank_id = ".$rvar_bank_id
                        ." and fp_due_period = '".$rvar_due_period."'"
                        ." group by cc_id,acc_card_type,cc_number,cc_holder";
						
		$result = mysql_query($query) or die("list_cards_status.php: Query failed (" . $query .") ". mysql_error());
		$item_index = 0;
		
		print('<credit_cards>'.$NL);
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {  

			print('<credit_card>'.$NL);
			print('<cc_id>'.$row["cc_id"].'</cc_id>'.$NL);
			print('<card_type>'.$row["acc_card_type"].'</card_type>'.$NL);
			print('<cc_number>'.$row["cc_number"].'</cc_number>'.$NL);
			print('<cc_holder>'.$row["cc_holder"].'</cc_holder>'.$NL);
			print('<value>'.$row["value"].'</value>'.$NL);
			print('</credit_card>'.$NL);
	    }
		print('</credit_cards>'.$NL);
		print('<query>'.$query.'</query>'.$NL);
   	} else {
   		$texto_error = "";
   		if ($rvar_cHash == "") {
   			$texto_error.= "Invalid HASH.";
   		} 
   		if ($rvar_bank_id == "") {
   			$texto_error.= " Bank ID missing.";
   		} 
   		if ($rvar_due_period == "") {
   			$texto_error.= " Period Missing.";
   		} 
		print ('<error>'.$texto_error.'</error>'.$NL);;
	}	

	print ('</status>'. $NL);
?>