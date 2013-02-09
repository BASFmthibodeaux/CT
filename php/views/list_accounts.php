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
require_once '../global/global_variables.php';
require_once $functions_path . '/connect.php';

	header('Content-type: text/xml');
	header('Pragma: public');
	header('Cache-control: private');
	header('Expires: -1');

    import_request_variables("gP","rvar_");

/* PARAMETROS

	cHash			hash de seguridad
	
*/

	$NL = chr(13);
	$link=connect_database($main_database);
	
	print '<?xml version="1.0" encoding="iso-8859-1"?>'. $NL;
	print ('<accounts>'.$NL);
	
	if ($rvar_cHash !="") {
		
		//TODO: revisar HASH primero!
		
		$query="select * from banks,accounts,credit_cards ";
		$where = "where bank_id = acc_bank_id and cc_acc_id = acc_id and cc_holder = '$rvar_holder'";
		$and_operator = "";
		$order_by = " order by bank_description,acc_card_type";
		$query .= $where;
		$result = mysql_query($query) or die("list_accounts.php: Query failed (" . $query .") ". mysql_error());
		$item_index = 0;

		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			print('<account>'.$NL);

			print('<acc_id>'.$row["acc_id"].'</acc_id>'.$NL);
			print('<bank>'.$row["bank_description"].'</bank>'.$NL);
			print('<card_type>'.$row["acc_card_type"].'</card_type>'.$NL);
			print('<holder>'.$row["cc_holder"].'</holder>'.$NL);
			print('<number>'.$row["cc_number"].'</number>'.$NL);
				
			print('</account>'.$NL);
			$item_index ++;
	    }
		print('<where_clause>'.$where.'</where_clause>'.$NL);
   	} else {
   		$texto_error = "";
   		if ($rvar_cHash == "") {
   			$texto_error.= "Invalid HASH.";
   		} 
		print ('<error>'.$texto_error.'</error>'.$NL);;
	}	

	print ('</accounts>'. $NL);
?>