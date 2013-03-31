<?php
/*
 * -----------------------------------------------------
 * Funciones para bases de datos
 * -----------------------------------------------------
*/

function ceilpow10 ($val) {
   if ($val % 100 == 0) return $val;
   return $val + (100 - ($val % 100));
}


function execute_sql ($query) {
    /* Performing SQL query */
    $func_result = mysql_query($query) or die("EXECUTE_SQL:Query failed '".$query."'" . "<br>" .mysql_error());
    if ($row = mysql_fetch_array($func_result, MYSQL_NUM)) {
    	$return_code=$row[0];
    } else {
    	$return_code="";
    }
    mysql_free_result($func_result);
    return $return_code;
}

function dml_sql ($link,$query) {
    /* Performing SQL query */
    $return_code = "1";
    $func_result = mysql_query($query) or $return_code=mysql_error();

	if ($return_code == "1" && substr($query,0,7)=="insert") {
		$return_code=mysql_insert_id($link);
	}	

//    mysql_free_result($func_result);
    
    return $return_code;
}

function add_month($date) {
	$mes_operacion = substr($date,5,2);
	$anio_operacion = substr($date,0,4);
	
	$mes_operacion += 1;
	if ($mes_operacion > 12) {
		$mes_operacion = 1;
		$anio_operacion += 1;
	}	
	return $anio_operacion.'-'.str_pad($mes_operacion,2,'0',STR_PAD_LEFT).'-01';
}

?>