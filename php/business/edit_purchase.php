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
 * 05Apr13 JL / Creacion del archivo
 * -----------------------------------------------------
 *  
*/
require_once '../global/global_variables.php';
require_once $functions_path . '/connect.php';
require_once $functions_path . '/database_functions.php';


/* PARAMETROS

	cHash			hash de seguridad
	
	-- campos:
	purchase_id
	date
	description
	value
	payments
    due_period
    currency_id
*/
	header('Content-type: text/xml');
	header('Pragma: public');
	header('Cache-control: private');
	header('Expires: -1');
	
    import_request_variables("gP","rvar_");


	$NL = chr(10).chr(13);
	$link=connect_database($main_database);
	
	print '<?xml version="1.0" encoding="iso-8859-1"?>'. $NL;

//    mysql_autocommit ($link,FALSE);	
    
	print ('<action>'.$NL);
	
	//TODO:Hacer que valide la conexion en el hash
	if ($rvar_cHash !="" and $rvar_purchase_id != "") {
		print ('<object>purchases</object>'.$NL);
		print ('<action_type>EDIT</action_type>'.$NL);
        $set_query = "";
        $separator = "";
        $flag = "0";
        if ($rvar_date != "") {
            $set_query .= $separator . "pur_date = '".$rvar_date."'";
            $separator = " ,";
            $flag = "1";
        }
        if ($rvar_description != "") {
            $set_query .= $separator . "pur_description = '".$rvar_description."'";
            $separator = " ,";
        }
        if ($rvar_value != "") {
            $set_query .= $separator . "pur_value = ".$rvar_value;
            $separator = " ,";
            $flag = "1";
        }
        if ($rvar_payments != "") {
            $set_query .= $separator . "pur_payments = ".$rvar_payments;
            $separator = " ,";
            $flag = "1";
        }
        if ($rvar_currency_id != "") {
            $set_query .= $separator . "pur_currency_id = ".$rvar_currency_id;
            $separator = " ,";
        }
        
        $result = execute_sql ("update purchases set ".$set_query." where pur_id = ".$rvar_purchase_id);

        // recalcular los pagos futuros si se toco la fecha, el valor o la cantidad de pagos.
        if ($flag == "1") {
            
            $rvar_credit_card = execute_sql ("select pur_cc_id from purchases where pur_id = ".$rvar_purchase_id);
            $rvar_value = execute_sql ("select pur_value from purchases where pur_id = ".$rvar_purchase_id);

    		$dia = execute_sql ('select cc_closing_date from credit_cards where cc_id = '.$rvar_credit_card);
    		
    		// Si no se cambio el due period -> entonces calcular el vencimiento con la nueva fecha
    		if ($rvar_due_period == "") {
        		$dia_operacion = substr($rvar_date,8,2);
        		$mes_operacion = substr($rvar_date,5,2);
        		$anio_operacion = substr($rvar_date,0,4);
        
        		if ($dia_operacion > $dia) {
        			$due_date = add_month ($rvar_date);
        			$due_date = substr($due_date,0,7)."-".str_pad($dia,2,"0",STR_PAD_LEFT);
        		} else {
        			$due_date = substr($rvar_date,0,7)."-".str_pad($dia,2,"0",STR_PAD_LEFT);
        		}
    		} else {
    		  $due_date = $rvar_due_period."-".str_pad($dia,2,"0",STR_PAD_LEFT);
    		}
    		
    		$payments = execute_sql ("select pur_payments from purchases where pur_id = ".$rvar_purchase_id);
    		
    		mysql_query ("delete from future_payments where fp_pur_id = ".$rvar_purchase_id ) 
            		                          or die("EXECUTE_SQL:delete failed " .mysql_error());
                
			if ($payments > 1) {
				for ($x=1;$x<=$payments;$x++) {

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
							.", ".$rvar_value/$payments 
							.", '".$rvar_purchased_by."'" 
							.", ".$rvar_purchase_id
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
							.", ".$rvar_purchase_id
							.")";

				mysql_query ($query2) or die("EXECUTE_SQL:Query failed '".$query2."'" . "<br>" .mysql_error());;								
			}
        }        
	} else {
   		if ($rvar_cHash == "") {
   			$texto_error.= "Invalid HASH.";
   		} 
   		if ($rvar_purchase_id == "") {
   		    $texto_error.= "Invalid purchase ID.";
   		}
   		  
		print ('<error>'.$texto_error.'</error>'.$NL);;
	}
	print ('</action>'. $NL);
//    mysql_commit ($link);	
	
?>