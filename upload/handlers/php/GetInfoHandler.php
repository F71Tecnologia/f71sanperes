<?php
    $id = $_POST['sessionId'];
    $id = trim($id);
    session_name($id);
    session_start();

    if($_SESSION['value']==-1)
    {
    	echo 100;
	$_SESSION['value'] = -2;
    } else if($_SESSION['value']==-2)
    {
    	echo -1;
        session_destroy();
    } else {

	$info = uploadprogress_get_info($id);
	
	if ($info['bytes_total'] < 1) {
		$percent = 0;
	} else {
		$percent = round($info['bytes_uploaded'] / $info['bytes_total'] * 100, 0);
	}
	echo $percent;
    }	
?>

