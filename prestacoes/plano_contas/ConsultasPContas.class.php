<?php

class ConsultasPContas {    
    
    public function prestador ($projeto) {
        $qr = "SELECT if(B.prestador_tipo !=  3, 'J', 'F') AS tpTerceiro, C.cnpj, C.nome, C.apelido, B.c_cep, E.descricao_tp_logradouro, B.c_endereco, B.c_numero, B.c_complemento, B.c_bairro, B.c_uf, D.municipio, DATE_FORMAT( B.contratado_em,'%d%m%Y') AS contratado_em,B.c_tel, B.c_fax, B.c_email, B.c_site, B.c_ie, B.c_im, B.c_rg
               FROM prestadorServ_prestadorPro_assoc AS A
               LEFT JOIN prestadorservico AS B ON (A.id_prestador = B.id_prestador)
               LEFT JOIN prestador_prosoft AS C ON (A.id_prestador_prosoft = C.id_prestador_prosoft)
               LEFT JOIN municipios AS D ON (B.c_cod_cidade = D.id_municipio)
               LEFT JOIN tipos_de_logradouro AS E ON (B.c_id_tp_logradouro = E.id_tp_logradouro)
               WHERE B.id_projeto IN ($projeto);";
        $result = mysql_query($qr);
        return $result;        
    }
    
    public function functionName($data, $banco) { // Não está pronta
        $qr = "SELECT B.acesso, B.classificador, B.nome, CAST(SUM(REPLACE(D.valor,',','.')) AS decimal(13,2)) AS sValor, E.saldo
               FROM entradaesaida_plano_contas_assoc AS A
               LEFT JOIN plano_de_contas AS B ON (A.id_plano_contas = B.id_plano_contas)
               LEFT JOIN entradaesaida AS C ON (A.id_entradasaida = C.id_entradasaida)
               LEFT JOIN saida AS D ON (C.id_entradasaida = D.tipo)
               LEFT JOIN bancos AS E ON (D.id_banco = E.id_banco)
               WHERE D.`status` = 2 AND DATE_FORMAT(D.data_vencimento,'%Y-%m') = '$data' AND E.id_banco = $banco";
        $result = mysql_query($qr);
        return $result;     
    }
}
