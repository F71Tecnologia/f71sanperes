<?php

class AutonomoClass {

    public function getBancos($regiao, $projeto) {

        $sqlBanco = "SELECT id_banco, CONCAT(razao, ' - ', conta, ' - ', agencia) nome FROM bancos WHERE id_regiao = '$regiao' AND id_projeto = '$projeto' AND status_reg = '1'";
        $queryBanco = mysql_query($sqlBanco);

        $arrBanco[0] = (object) ['id_banco' => 0, 'nome' => "Nenhum Banco"];

        while ($rowBanco = mysql_fetch_object($queryBanco)) {

            $arrBanco[$rowBanco->id_banco] = $rowBanco;
        }

        $arrBanco[9999] = (object) ['id_banco' => 9999, 'nome' => "Outro"];

        return (object) $arrBanco;
    }

}
