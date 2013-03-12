<?php
/*
 * -----------------------------------------------------
 * Administracion de entidad Evento
 * -----------------------------------------------------
 * Este PHP se utiliza borrar compras
 * genera como respuesta un archivo XML con el resultado de la operacion:
 * 
 * 1: OK (ver propiedad id para ver el nuevo objeto)
 * 0: Error (ver propiedad mensaje_error)
 * 
 * TODO: Agregar hash de seguridad para que el PHP sea llamado desde una conexion valida
 * 
 * Log de modificaciones
 * 
 * 11Mar12 JL    creacion del archivo
 * -----------------------------------------------------
 *  
*/
require_once '../global/global_variables.php';
require_once $functions_path . '/connect.php';
require_once $functions_path . '/database_functions.php';


/* PARAMETROS

	cHash			hash de seguridad
    username        usuario	
	-- campos:
    purchase_id	
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
	if ($rvar_cHash !="" and $rvar_username != "" and $rvar_purchase_id != "") {
		print ('<object>purchases</object>'.$NL);
		print ('<action_type>DELETE</action_type>'.$NL);

		$usu_id = execute_sql ("select usu_id from users where usu_username = '".$rvar_username."'");

        $can_delete = execute_sql ('select * from purchases where pur_id = '.$rvar_purchase_id
                                    .' and pur_cc_id in (select distinct cc_id from credit_cards,accounts where acc_id = cc_acc_id '
                                    .' and acc_bank_id in (select bank_id from banks where `bank_usu_id` = '.$usu_id.') )' );
        if ($can_delete == $rvar_purchase_id) {
            
            $nada = execute_sql ('delete from future_payments where fp_pur_id = '.$rvar_purchase_id);
            $nada = execute_sql ('delete from purchases where pur_id = '.$rvar_purchase_id);
			print("<code>1</code>". $NL);
			print('<response>Deleted.</response>'. $NL);
        
        } else {
			print("<code>0</code>". $NL);
			print('<response>You have no permission to delete it or the record does not exist.</response>'. $NL);
            
        }
	} else {
   		if ($rvar_cHash == "") {
   			$texto_error.= "Invalid HASH.";
   		} 
   		if ($rvar_username == "") {
   			$texto_error.= "Username is missing.";
   		} 
   		if ($rvar_purchase_id == "") {
   			$texto_error.= "Purchase ID is missing.";
   		} 
		print("<code>0</code>". $NL);
		print ('<error>'.$texto_error.'</error>'.$NL);;
	}
	print ('</action>'. $NL);
?>