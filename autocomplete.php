<?php

require_once "defaultincludes.inc";
require_once "mrbs_sql.inc";


	if ($_POST['value'] != null) {
        $sql = "select projet from $tbl_entry";

        
           $res = sql_query($sql);
        if (! $res)
        {
            trigger_error(sql_error(), E_USER_WARNING);
            fatal_error(TRUE, get_vocab("fatal_db_error"));
        }

        $row = sql_row_keyed($res, 0);
        sql_free($res);
        
        
        $res = sql_query($sql);
        
        $s = "";
        if ($res)
        {   
            for ($i = 0; ($row = sql_row_keyed($res, $i)); $i++)
            {
                $t = row['projet'];
                for ($i = 0; $i < count($t)-1; $i++) {
				$s .= $t[$i].'<b style="text-decoration: underline">'.$_POST['value']."</b>";
			}
            $s .= $t[count($t)-1];
			$s .= "_";       
            }
            $s = substr($s, 0, strlen($s)-1);
		    echo $s;
        }     
	}
?>