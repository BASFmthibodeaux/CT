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
	print ('<results>'.$NL);
	
	if ($rvar_cHash !="" and $rvar_search != "" and $rvar_username != "" ) {
		
		//TODO: revisar HASH primero!
		
		$usu_id = execute_sql ("select usu_id from users where usu_username = '".$rvar_username."'");
				
		$query = "select * from purchases,credit_cards,accounts,banks "
		              ." where pur_cc_id = cc_id and cc_acc_id = acc_id and acc_bank_id = bank_id"
		              ." and (upper(pur_description) like upper('%".$rvar_search."%') "
		              ." or pur_date like upper('%".$rvar_search."%')) "
		              ." and pur_cc_id in (select cc_id from credit_cards, accounts where acc_id = cc_id "
		              ." and acc_bank_id in (select bank_id from banks where bank_usu_id = ".$usu_id."))"; 
		              /// hay que cambiar para que pueda ver los items a los que estoy autorizado
						
		$result = mysql_query($query) or die("search.php: Query failed (" . $query .") ". mysql_error());
		$item_index = 0;
		
		print('<purchases>'.$NL);
		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {  

			print('<purchase>'.$NL);

			print('<pur_id>'.$row["pur_id"].'</pur_id>'.$NL);
			print('<date>'.$row["pur_date"].'</date>'.$NL);
			print('<purchased_by>'.$row["pur_purchased_by"].'</purchased_by>'.$NL);
			print('<credit_card_id>'.$row["pur_cc_id"].'</credit_card_id>'.$NL);  
			print('<credit_card>'.$row["cc_number"].'</credit_card>'.$NL);
			print('<card_type>'.$row["acc_card_type"].'</card_type>'.$NL);
			print('<description>'.$row["pur_description"].'</description>'.$NL);
			print('<value>'.$row["pur_value"].'</value>'.$NL);
			print('<payments>'.$row["pur_payments"].'</payments>'.$NL);
			print('<bank_name>'.$row["bank_name"].'</bank_name>'.$NL);
			if ($row["pur_payments"] != "" &&  $row["pur_payments"] != 0) {
				print('<payment_value>'.round($row["pur_value"]/$row["pur_payments"],2).'</payment_value>'.$NL);
			} else {
				print('<payment_value>'.$row["pur_value"].'</payment_value>'.$NL);
			}
			print('<timestamp>'.$row["pur_timestamp"].'</timestamp>'.$NL);


			print('</purchase>'.$NL);
	    }
		print('</purchases>'.$NL);
		print('<query>'.$query.'</query>'.$NL);
   	} else {
   		$texto_error = "";
   		if ($rvar_cHash == "") {
   			$texto_error.= "Invalid HASH.";
   		} 
   		if ($rvar_search == "") {
   			$texto_error.= " Search phrase missing.";
   		} 
   		if ($rvar_username == "") {
   			$texto_error.= " Username missing.";
   		} 
		print ('<error>'.$texto_error.'</error>'.$NL);;
	}	

	print ('</results>'. $NL);
?>