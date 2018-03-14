<?php
abstract class IDaoValeAlimentacao {

    function getProjetos() {
        $sql = "SELECT  A.id_projeto, A.nome AS nome_projeto, B.id_empresa, B.nome AS nome_empresa,  B.cnpj AS cnpj_empresa, A.cnpj  
                FROM projeto AS A 
                INNER JOIN rhempresa AS B ON (A.id_projeto = B.id_projeto) 
                WHERE B.id_regiao = A.id_regiao";
        $qr = mysql_query($sql);
        $projetos = array();
        while ($row = mysql_fetch_array($qr)) {
            $projetos[$row['id_projeto']] = $row['nome_projeto'] . ' ' . $row['cnpj_empresa'];
        }
        return $projetos;
    }
}