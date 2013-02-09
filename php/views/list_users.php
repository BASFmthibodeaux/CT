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
 * 14Nov12 V000
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


	$NL = chr(13);
	$link=connect_database($main_database);
	
	print '<?xml version="1.0" encoding="iso-8859-1"?>'. $NL;
	print ('<people>'.$NL);
	
	if ($rvar_cHash !="") {
		
		//TODO: revisar HASH primero!
		
		$query="select distinct usu_username,usu_name from `users_groups`, users where usu_username=grp_username and grp_id in (select distinct grp_id from users_groups where grp_username='$rvar_username')"; 

		$result = mysql_query($query) or die("list_users.php: Query failed (" . $query .") ". mysql_error());
		$item_index = 0;

		while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
			print('<person>'.$NL);

			print('<username>'.$row["usu_username"].'</username>'.$NL);
			print('<name>'.$row["usu_name"].'</name>'.$NL);

			print('</person>'.$NL);
			$item_index ++;
	    }
   	} else {
   		$texto_error = "";
   		if ($rvar_cHash == "") {
   			$texto_error.= "Invalid HASH.";
   		} 
		print ('<error>'.$texto_error.'</error>'.$NL);;
	}	

	print ('</people>'. $NL);
?>