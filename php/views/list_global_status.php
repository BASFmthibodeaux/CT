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
 * 15/02/2013 JL V000
 * 	 -Creacion del archivo
 * -----------------------------------------------------
 *  
*/

/* PARAMETROS

cHash			hash de seguridad

-- campos:
username 

*/
require_once '../global/global_variables.php';
require_once '../global/database_functions.php';
require_once $functions_path . '/connect.php';

	header('Content-type: text/xml');
	header('Pragma: public');
	header('Cache-control: private');
	header('Expires: -1');

    import_request_variables("gP","rvar_");


	$NL = chr(13).chr(10);
	$link=connect_database($main_database);
	
	print '<?xml version="1.0" encoding="iso-8859-1"?>'. $NL;
	print ('<status>'.$NL);
	
	if ($rvar_cHash !="") {
		
		//TODO: revisar HASH primero!
		
		$usu_id = execute_sql ("select usu_id from users where usu_username = '".$rvar_username."'");
		
		$period = execute_sql ("select max(substr(pur_date,1,7)) from purchases,credit_cards, accounts "
										." where pur_cc_id = cc_id and acc_id = cc_acc_id and "
										." acc_bank_id in (select bank_id from banks where bank_usu_id = ".$usu_id." )");

		$total_periodo = execute_sql ("select sum(fp_value) from future_payments, credit_cards where fp_cc_id = cc_id and cc_usu_id = ".$usu_id
											." and fp_due_period = '".$period."'");
		
		
		print('<period>'.$period.'</period>'.$NL);
		print('<total_to_pay>'.$total_periodo.'</total_to_pay>'.$NL);
		
		$query = "select bank_id,bank_description, sum(fp_value) value from banks,future_payments,credit_cards,accounts "
						." where bank_id = acc_bank_id and cc_id = fp_cc_id and acc_id = cc_acc_id"
						." and bank_usu_id = ".$usu_id
						." and fp_due_period = '".$period."'"
						." group by bank_id,bank_description";
						
		$result = mysql_query($query) or die("list_global_status.php: Query failed (" . $query .") ". mysql_error());
		$item_index = 0;
		
		print('<banks>'.$NL);
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {  

			print('<bank>'.$NL);
			print('<bank_id>'.$row["bank_id"].'</bank_id>'.$NL);
			print('<description>'.$row["bank_description"].'</description>'.$NL);
			print('<value>'.$row["value"].'</value>'.$NL);
			print('<percentage>'.round($row["value"]/$total_periodo*100).'</percentage>'.$NL);
			print('</bank>'.$NL);
	    }
		print('</banks>'.$NL);
 		print('<where_clause>'.$where.'</where_clause>'.$NL);
   	} else {
   		$texto_error = "";
   		if ($rvar_cHash == "") {
   			$texto_error.= "Invalid HASH.";
   		} 
		print ('<error>'.$texto_error.'</error>'.$NL);;
	}	

	print ('</status>'. $NL);
?>