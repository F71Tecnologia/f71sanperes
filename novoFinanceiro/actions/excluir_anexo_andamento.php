<?php 
include "../../conn.php";

$qr_delete = mysql_query("UPDATE proc_andamento_anexo SET andamento_anexo_status = 0 WHERE andamento_anexo_id  = '$_POST[id_anexo_andamento]' LIMIT 1");

if(!$qr_delete){
	echo '1';
}else {

}
?>