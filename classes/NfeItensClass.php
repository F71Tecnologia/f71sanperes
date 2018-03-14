<?php

class NfeItens {

    public function inserir(array $array) {
        foreach ($array as $key => $value) {
            $arr_campos[] = $key;
            $arr_values[] = "'$value'";
        }
        $_campos = implode(',', $arr_campos);
        $_values = implode(',', $arr_values);
        $query = "INSERT INTO nfe_itens ($_campos) VALUES ($_values)";
        return mysql_query($query) or die($query . " " . mysql_error());
    }

    public function editar($id, array $array) {
        foreach ($array as $key => $value) {
            $arr[] = "$key = '$value'";
        }
        $_campos = implode(',', $arr);
        $query = "UPDATE nfe_itens  SET $_campos WHERE id_item = $id";
        return mysql_query($query) or die($query . " " . mysql_error());
    }

    public function salvar(array $array) {
        if (isset($array['id_item'])) {
            $id = $array['id_item'];
            unset($array['id_item']);
            return $this->editar($id, $array);
        } else {
            return $this->inserir($array);
        }
    }

    public function selectById($id) {
        $query = "SELECT * FROM nfe_itens WHERE id_item = $id";
        $result = mysql_query($query) or die($query . " " . mysql_error());
        return mysql_fetch_assoc($result);
    }

    public function consultar($array = NULL) {
        if (!empty($array)) {
            foreach ($array as $key => $value) {
                $arr[] = "$key = '$value'";
            }
            $_campos = implode(',', $arr);
        } else {
            $_campos = '';
        }
        while ($row = mysql_fetch_assoc($result)) {
            $arr[] = $row;
        }
        return $arr;
    }

}
