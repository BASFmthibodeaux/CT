<?php
/*
 * -----------------------------------------------------
 * Administracion de entidad Evento
 * -----------------------------------------------------
 * Este PHP se utiliza para crear, editar y borrar Eventos,
 * genera como respuesta un archivo XML con el resultado de la operacion:
 * 
 * 1: OK (ver propiedad id para ver el nuevo objeto)
 * 0: Error (ver propiedad mensaje_error)
 * 
 * TODO: Agregar hash de seguridad para que el PHP sea llamado desde una conexion valida
 * 
 * Log de modificaciones
 * 
 * 12Dec11 JL 	 -Creacion del archivo
 * 14Aug12 JL	 Agregarle el control de duplicidad
 * -----------------------------------------------------
 *  
*/
require_once '../global/global_variables.php';
require_once $functions_path . '/connect.php';
require_once $functions_path . '/database_functions.php';


/* PARAMETROS

	cHash			hash de seguridad
	cAction			accion a realizar (ADD)
	value_type		tipo de valor que esta recibiendo (valor cuota o valor total)
	
	-- campos:
	date
	purchased_by	
	credit_card
	description
	value
	payments
	bypass_check    sacar la funcion de control de duplicacion 
	
*/

	header('Content-type: text/xml');
	header('Pragma: public');
	header('Cache-control: private');
	header('Expires: -1');

    import_request_variables("gP","rvar_");


	$NL = chr(10).chr(13);
	$link=connect_database($main_database);
	
	print '<?xml version="1.0" encoding="iso-8859-1"?>'. $NL;
	
	print ('<action>'.$NL);
	
	//TODO:Hacer que valide la conexion en el hash
	if ($rvar_cHash !="") {
		print ('<object>purchases</object>'.$NL);
		print ('<action_type>'.$rvar_cAction.'</action_type>'.$NL);

		switch ($rvar_cAction) {
			case "ADD":
				$duplicateVerified = true; 
				if ($rvar_bypass_check != "1") {
// 					if ($rvar_value_type == "MONTHLY_PAYMENT") {
// 						$rvar_value = $rvar_value * $rvar_payments;
// 					}
					$query = "select * from purchases where pur_cc_id = ".$rvar_credit_card." and  pur_date = '".$rvar_date."' and round(pur_value) ".
									"between ".(round($rvar_value)-1)." and ".(round($rvar_value)+1)." and pur_payments = ".$rvar_payments;	
//					print($query);
					
					$result = mysql_query($query) or die("list_accounts.php: Query failed (" . $query .") ". mysql_error());
					if ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
						$message = "Hay una compra de $".$row["pur_value"]." en ";
						if ($row["pur_payments"]>1) {
							$message = $message . $row["pur_payments"]." pagos ";
						} else {
							$message = $message . "en un pago ";
						}
						$message = $message . " hecha el ".$row["pur_date"] . " por ".$row["pur_purchased_by"];
						if ($row["pur_description"] != "") {
							$message = $message . " (".$row['pur_description'].")";
						}
						$duplicateVerified = false;
	//					print($message);
					} 
				
					
				}
				if ($duplicateVerified) {
					if ($rvar_value_type == "MONTHLY_PAYMENT") {
						$rvar_value = $rvar_value * $rvar_payments;
					}
	
					$query="insert into purchases (" 
									." pur_date "
									.", pur_purchased_by "
									.", pur_cc_id "
									.", pur_description "
									.", pur_value "
									.", pur_payments "
									." ) values (" 
									." '".$rvar_date."'" 
									.", '".$rvar_purchased_by."'" 
									.", ".$rvar_credit_card 
									.", '".$rvar_description."'" 
									.", ".$rvar_value 
									.", ".$rvar_payments
									.")";
					if (mysql_query ($query)) {
						$rvar_nId=mysql_insert_id($link);
						print("<code>1</code>". $NL);
						print('<response>Se cargo la compra con el id '.$rvar_nId.'</response>'. $NL);
					} else {
						print ('<error>MySQL error: '.mysql_error().'</error>'.$NL);;
					}	
					//tomar el dia de cierre para ver si entra en el cierre de la compra o en el siguiente
					
					$dia = execute_sql ('select cc_closing_date from credit_cards where cc_id = '.$rvar_credit_card);
					$dia_operacion = substr($rvar_date,8,2);
					$mes_operacion = substr($rvar_date,5,2);
					$anio_operacion = substr($rvar_date,0,4);
	
					if ($dia_operacion > $dia) {
						$due_date = add_month ($rvar_date);
						$due_date = substr($due_date,0,7)."-".str_pad($dia,2,"0",STR_PAD_LEFT);
					} else {
						$due_date = substr($rvar_date,0,7)."-".str_pad($dia,2,"0",STR_PAD_LEFT);
					}
					
					if ($rvar_payments > 1) {
						for ($x=1;$x<=$rvar_payments;$x++) {
	
						$query2 = "insert into future_payments ("
									."fp_cc_id,"
									."fp_due_date,"
									."fp_due_period,"
									."fp_value,"
									."fp_created_by,"
									."fp_pur_id"
									.") values ("
									." ".$rvar_credit_card 
									.",'". $due_date. "'" 
									.",'".substr($due_date,0,7)."'" 
									.", ".$rvar_value/$rvar_payments 
									.", '".$rvar_purchased_by."'" 
									.", ".$rvar_nId
									.")";
	
//						print ("<query>".$query2."</query>".$NL);
						mysql_query ($query2) or die("EXECUTE_SQL:Query failed '".$query2."'" . "<br>" .mysql_error());;								
	
						$due_date = add_month ($due_date);
						$due_date = substr($due_date,0,7)."-".str_pad($dia,2,"0",STR_PAD_LEFT);
	
						}
					} else {
						$query2 = "insert into future_payments ("
									."fp_cc_id,"
									."fp_due_date,"
									."fp_due_period,"
									."fp_value,"
									."fp_created_by,"
									."fp_pur_id"
									.") values ("
									." ".$rvar_credit_card 
									.",'". $due_date. "'" 
									.",'".substr($due_date,0,7)."'" 
									.", ".$rvar_value 
									.", '".$rvar_purchased_by."'" 
									.", ".$rvar_nId
									.")";
	
//						print ("<query>".$query2."</query>".$NL);
						mysql_query ($query2) or die("EXECUTE_SQL:Query failed '".$query2."'" . "<br>" .mysql_error());;								
					}
					
				} else {
					print ("<code>3</code>".$NL);
					print ("<error>$message</error>".$NL);
				}
				break;
				
				
			default:
				print('<code>0</code><response>La acción '.$rvar_cAction.' no es reconocida</response>'. $NL);
				break;
		} 
	} else {
   		if ($rvar_cHash == "") {
   			$texto_error.= "Invalid HASH.";
   		} 
		print ('<error>'.$texto_error.'</error>'.$NL);;
	}
	print ('</action>'. $NL);
?>