<?php
/**
 * -----------------------------------------------------
 * Lista de aplicaciones
 * -----------------------------------------------------
 * Este PHP se utiliza para mostrar la situacion crediticia de una cuenta
 * 
 * PARAMETROS:
 * 
 * 
 * TODO: Agregar hash de seguridad para que el PHP sea llamado desde una conexion valida
 * 
 * Log de modificaciones
 * 
 * 11/07/12 JL V000
 * 	 -Creacion del archivo
 * -----------------------------------------------------
 *  
*/


/* PARAMETROS

	cHash			hash de seguridad
	account
	period_from     periodo desde 
	
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
	
	if ($rvar_cHash !="") {
		
		//TODO: revisar HASH primero!
		$limit = execute_sql("select acc_credit_limit from accounts where acc_id = ".$rvar_account) or die ("</graph>");
		
		if ($rvar_period_from == "TODAY" || $rvar_period_from =="") {
			$from_date = date("Y-m-d",mktime(0, 0, 0, date("m"), "01",   date("Y")));
		}
		
		$usage = execute_sql("select sum(pur_value) from purchases,credit_cards,accounts".
					" where pur_cc_id = cc_id and cc_acc_id = acc_id and acc_id = ".$rvar_account.
					" and pur_date >= '".$from_date."' ");
		
		
		print('<situation>'.$NL);

		print('<taken>'.round($usage).'</taken>'.$NL);
		
		if ($usage > $limit) $usage = $limit;
		
		print('<available_percentual>'.round(($limit - $usage)/$limit*100).'</available_percentual>'.$NL);
		print('<available>'.round($limit - $usage).'</available>'.$NL);
		print('<limit>'.round($limit).'</limit>'.$NL);
		print('</situation>'.$NL);
	}	

	print ('</graph>'. $NL);
?>