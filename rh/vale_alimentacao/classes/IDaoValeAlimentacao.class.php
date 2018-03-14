<?php
abstract class IDaoValeAlimentacao {

    function getProjetos($id_master = NULL,$id_regiao = NULL) {
        $cond_regiao = (!empty($id_regiao))?" AND A.id_regiao = $id_regiao":'';
        $cond_master = (!empty($id_master))?" AND A.id_master = $id_master":'';
        $sql = "SELECT  A.id_projeto, A.nome AS nome_projeto, B.id_empresa, B.nome AS nome_empresa,  B.cnpj AS cnpj_empresa, A.cnpj  
                FROM projeto AS A 
                INNER JOIN rhempresa AS B ON (A.id_projeto = B.id_projeto) 
                WHERE B.id_regiao = A.id_regiao $cond_regiao $cond_master
                ORDER BY nome_projeto";
        $qr = mysql_query($sql);
        $projetos = array();
        while ($row = mysql_fetch_array($qr)) {
            $projetos[$row['id_projeto']] = $row['id_projeto'] . ' - ' . $row['nome_projeto'] . ' ' . $row['cnpj_empresa'];
        }
        return $projetos;
    }
}