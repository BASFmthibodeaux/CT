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
	print ('<credit_cards>'.$NL);
	
	if ($rvar_cHash !="") {
		
		//TODO: revisar HASH primero!
		
		$query="select * from credit_cards,banks,accounts ";
		$where = "where bank_id = acc_bank_id and cc_acc_id = acc_id ";
		if ($rvar_username !="") {

			$usu_id = execute_sql ("select usu_id from users where usu_username = '".$rvar_username."'");
			$where .= " and bank_usu_id = ".$usu_id;

//			$where = $where . " and cc_holder = '".$rvar_username. "' ";
		}
		$and_operator = "";
		$order_by = " order by bank_description,cc_holder,acc_card_type";
		$query .= $where;
		$result = mysql_query($query) or die("list_cards.php: Query failed (" . $query .") ". mysql_error());
		$item_index = 0;

		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			print('<credit_card>'.$NL);

			print('<cc_id>'.$row["cc_id"].'</cc_id>'.$NL);
			print('<holder>'.$row["cc_holder"].'</holder>'.$NL);
			print('<cc_number>'.$row["cc_number"].'</cc_number>'.$NL);
			print('<bank>'.$row["bank_description"].'</bank>'.$NL);
			print('<card_type>'.$row["acc_card_type"].'</card_type>'.$NL);

			print('</credit_card>'.$NL);
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

	print ('</credit_cards>'. $NL);
?>