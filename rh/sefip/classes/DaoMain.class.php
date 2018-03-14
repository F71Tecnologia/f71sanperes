<?php

class DaoMain {
    
    function mysqlQueryToArray($qr) {
        $arr = array();
        while ($res = mysql_fetch_assoc($qr)) {
            $arr[] = $res;
        }
        return $arr;
    }

}