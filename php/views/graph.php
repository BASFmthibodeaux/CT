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
	type			PAYMENTS or blank / 
	username
	bank_id
	credit_card		
	account
	period_from
	
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
	print ('<graph>'.$NL);
	
	if ($rvar_cHash !="" && $rvar_username != "") {
		
		//TODO: revisar HASH primero!
		$usu_id = execute_sql ("select usu_id from users where usu_username = '".$rvar_username."'");
		
		if ($rvar_type == "PAYMENTS" || $rvar_type == "") {

			// Buscar en funcion de los pagos
			$query="select fp_due_period, sum(if (pur_payments=null or pur_payments = 1 ,fp_value,0)) one_payment, sum(if (pur_payments > 1 ,fp_value,0)) more_payments from future_payments, purchases, credit_cards, accounts";
			$where = " where fp_pur_id = pur_id and pur_cc_id = cc_id "
			          ." and cc_acc_id = acc_id and acc_bank_id in (select bank_id from banks where bank_usu_id = ".$usu_id.") ";
			if ($rvar_credit_card !="") {
				$where = $where . " and pur_cc_id= ".$rvar_credit_card;
			}
			if ($rvar_account !="") {
				$where = $where . " and cc_acc_id= ".$rvar_account;
			}
			if ($rvar_bank_id != "") {
			     $where .= " and fp_cc_id in (select cc_id from accounts,credit_cards where acc_id=cc_acc_id and acc_bank_id = ".$rvar_bank_id." )";
			}
			//determinar el periodo 
			
			$order_by = " group by fp_due_period order by fp_due_period";

			if ($rvar_period_from == "TODAY" || $rvar_period_from =="") {
				$from_date = date("Y-m-d",mktime(0, 0, 0, date("m"), "01",   date("Y")));
				$to_date = date("Y-m-d", mktime(0, 0, 0, date("m")+12,"01",   date("Y")));
			} 
			if ($rvar_period_from == "-6") {
				$from_date = date("Y-m-d",mktime(0, 0, 0, date("m")-6, "01",   date("Y")));
				$to_date = date("Y-m-d", mktime(0, 0, 0, date("m")+6,"01",   date("Y")));
			}
			if ($rvar_period_from == "-9") {
				$from_date = date("Y-m-d",mktime(0, 0, 0, date("m")-9, "01",   date("Y")));
				$to_date = date("Y-m-d", mktime(0, 0, 0, date("m")+3,"01",   date("Y")));
			}
			if ($rvar_period_from == "-12") {
				$from_date = date("Y-m-d",mktime(0, 0, 0, date("m")-12, "01",   date("Y")));
				$to_date = date("Y-m-d", mktime(0, 0, 0, date("m"),"01",   date("Y")));
				$order_by .= " desc";
			}
				
			$where .= " and fp_due_date >= '".$from_date."' and fp_due_date <'".$to_date."' ";
			
			$query .= $where;
			$query .= $order_by;
			$result = mysql_query($query) or die("graph.php: Query failed (" . $query .") ". mysql_error());
			$item_index = 0;
			
			$max_value = 0;
			$max_value_total = 0;
			$min_value = 0;
	
			while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
				print('<period>'.$NL);
				print('<period>'.$row["fp_due_period"].'</period>'.$NL);
				print('<month>'.date("M y",strtotime($row["fp_due_period"]."-01 00:00:00")).'</month>'.$NL);
				print('<one_payment>'.round($row["one_payment"]).'</one_payment>'.$NL);
				print('<more_payments>'.round($row["more_payments"]).'</more_payments>'.$NL);
				print('<total>'.round($row["more_payments"]+$row["one_payment"]).'</total>'.$NL);


                if (round($row["one_payment"]) > $max_value ) {
                    $max_value = round($row["one_payment"]);
                }
                if (round($row["more_payments"]) > $max_value ) {
                    $max_value = round($row["more_payments"]);
                }

                if ($min_value == 0){
                    $min_value = $max_value;
                }


                if (round($row["one_payment"])!=0 and round($row["one_payment"]) < $min_value ) {
                    $min_value = round($row["one_payment"]);
                }
                if (round($row["more_payments"])!=0 and round($row["more_payments"]) < $min_value ) {
                    $min_value = round($row["more_payments"]);
                }

                if (round($row["more_payments"]+$row["one_payment"]) > $max_value_total) {
                    $max_value_total =round($row["more_payments"]+$row["one_payment"]);
                }

				print('</period>'.$NL);
				$item_index ++;
		    } 
		    
		    print ('<max_value>'.ceilpow10(round($max_value*1.1)).'</max_value>');
		    print ('<max_value_total>'.ceilpow10(round($max_value_total*1.1)).'</max_value_total>');
		    print ('<min_value>'.ceilpow10(round($min_value*0.9)).'</min_value>');
			print('<query><![CDATA['.$query.']]></query>'.$NL);
			print('<where_clause><![CDATA['.$where.']]></where_clause>'.$NL);
		}
		
	}

	print ('</graph>'. $NL);
?>