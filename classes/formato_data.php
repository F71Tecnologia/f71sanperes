<?php
function formato_brasileiro($data) {
    $nova_data = implode('/', array_reverse(explode('-', $data)));
    return $nova_data;
}

function formato_americano($data) {
    $nova_data = implode('-', array_reverse(explode('/', $data)));
    return $nova_data;
}
?>