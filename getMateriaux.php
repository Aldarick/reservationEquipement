<?php

require_once "defaultincludes.inc";
require_once "mrbs_sql.inc";
	
	$materiaux = "";
	if ($_POST['room_id'] != null) {
        
		$sql = "SELECT id, materiau_name
            FROM $tbl_materiau
           WHERE room_id=" . $_POST['room_id'] ;
 
        $res = sql_query($sql);
        if (! $res)
        {
            trigger_error(sql_error(), E_USER_WARNING);
            fatal_error(TRUE, get_vocab("fatal_db_error"));
        }
        
        $res = sql_query($sql);
        if ($res && sql_count($res) > 0)
        {   
            for ($i = 0; ($row = sql_row_keyed($res, $i)); $i++)
            {
                $materiaux .= $row['id']."*".$row['materiau_name']."/";
            }
            $materiaux = substr($materiaux, 0, strlen($materiaux)-1);
        }
            
    }
	echo $materiaux;
?>