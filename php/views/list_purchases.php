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
 * 11/12/11 JL V000
 * 	 -Creacion del archivo
 * -----------------------------------------------------
 *  
*/

/* PARAMETROS

cHash			hash de seguridad

-- campos:
credit_card
limit			cantidad de registros
purchased_by	usuario
order_by
due_period		fecha de pago


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
	print ('<purchases>'.$NL);
	
	if ($rvar_cHash !="") {
		
		//TODO: revisar HASH primero!
		
		$query="select * from purchases, credit_cards, accounts ";
		$where = " where pur_cc_id = cc_id and acc_id = cc_acc_id "  ;
		if ($rvar_credit_card !="") {
			$where = $where . " and pur_cc_id= ".$rvar_credit_card;
		}
		if ($rvar_period !="") {
			$where .= " and  substr(pur_date,1,7) = '".$rvar_period."'";
		}
		if ($rvar_username != "") {
			$where = $where . " and pur_purchased_by= '".$rvar_username."'";
		}
		if ($rvar_due_period !="") {
			
			if ($rvar_due_period == "LAST_MONTH") {
				$period = execute_sql ("select max(substr(pur_date,1,7)) from purchases,credit_cards, accounts ".$where);
			} else {
				$period = $rvar_due_period;
			}
			$where .= " and pur_id in (select fp_pur_id from future_payments where fp_due_period = '".$period."' )";
		}


		$and_operator = "";
		if ($rvar_order_by == "") {
			$order_by = " order by pur_date";
		} 
		if ($rvar_order_by == "TIMESTAMP") {
			$order_by = " order by pur_timestamp desc";
		} 
		if ($rvar_limit != ""){
			$order_by.=" limit ".$rvar_limit;
		}
		$query .= $where;
		$query .= $order_by;
		$result = mysql_query($query) or die("list_purchases.php: Query failed (" . $query .") ". mysql_error());
		$item_index = 0;

		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {  
			print('<purchase>'.$NL);

			print('<pur_id>'.$row["pur_id"].'</pur_id>'.$NL);
			print('<date>'.$row["pur_date"].'</date>'.$NL);
			print('<purchased_by>'.$row["pur_purchased_by"].'</purchased_by>'.$NL);
			print('<credit_card_id>'.$row["cc_id"].'</credit_card_id>'.$NL);  
			print('<credit_card>'.$row["cc_number"].'</credit_card>'.$NL);
			print('<card_type>'.$row["acc_card_type"].'</card_type>'.$NL);
			print('<description>'.$row["pur_description"].'</description>'.$NL);
			print('<value>'.$row["pur_value"].'</value>'.$NL);
			print('<payments>'.$row["pur_payments"].'</payments>'.$NL);
			if ($row["pur_payments"] != "" &&  $row["pur_payments"] != 0) {
				print('<payment_value>'.round($row["pur_value"]/$row["pur_payments"],2).'</payment_value>'.$NL);
			} else {
				print('<payment_value>'.$row["pur_value"].'</payment_value>'.$NL);
			}
			if ($rvar_due_period != "") {
				$remaining = execute_sql ("select count(*) from future_payments where fp_pur_id = ".$row["pur_id"].
											" and fp_due_period >'".$period."'");
				print('<remaining_payments>'.$remaining.'</remaining_payments>'.$NL);
			}
			print('<timestamp>'.$row["pur_timestamp"].'</timestamp>'.$NL);

			print('</purchase>'.$NL);
			$item_index ++;
	    }
 		print('<query>'.$query.'</query>'.$NL);
 		print('<query>'.$rvar_order_by.'</query>'.$NL);
 		print('<where_clause>'.$where.'</where_clause>'.$NL);
   	} else {
   		$texto_error = "";
   		if ($rvar_cHash == "") {
   			$texto_error.= "Invalid HASH.";
   		} 
		print ('<error>'.$texto_error.'</error>'.$NL);;
	}	

	print ('</purchases>'. $NL);
?>