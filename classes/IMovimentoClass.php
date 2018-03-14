<?php

abstract class IMovimentoClass {
    
    
    /*
     * $categoria = Todos, Credito, Debito 
     */
    function getMovimentosLancaveis($categoria='TODOS'){
        if($categoria=='CREDITO'){
            $where_categoria = 'AND categoria = "CREDITO"';
        }elseif($categoria=='DEBITO'){
            $where_categoria = ' AND categoria != "CREDITO" ';
        }else{
           $where_categoria = ''; 
        }
//        $where_categoria = ($categoria=='CREDITO') ?  ' AND categoria = "CREDITO" ' : ($categoria=='DEBITO') ? ' AND categoria != "CREDITO" ' : '';
        $sql = "SELECT A.*, CONCAT(CONCAT( IF(incidencia_inss=1,'5020',FALSE), ',' ,IF(incidencia_irrf=1,'5021',FALSE) , ',',IF(incidencia_fgts=1,'5023',FALSE)))  AS incidencia FROM rh_movimentos AS A WHERE mov_lancavel = 1 $where_categoria;";
        //echo $sql."\n";
        $result = mysql_query($sql);
        $movimentos = array();
//        $cont = 0;
        while($resp = mysql_fetch_array($result)){
            $cont =  $resp['id_mov'];
            $movimentos[$cont]['id_mov'] = $resp['id_mov'];
            $movimentos[$cont]['cod'] = $resp['cod'];
            $movimentos[$cont]['descicao'] = $resp['descicao'];
            $movimentos[$cont]['categoria'] = $resp['categoria'];
            $movimentos[$cont]['incidencia'] = $resp['incidencia'];
            $movimentos[$cont]['faixa'] = $resp['faixa'];
            $movimentos[$cont]['v_ini'] = $resp['v_ini'];
            $movimentos[$cont]['v_fim'] = $resp['v_fim'];
            $movimentos[$cont]['percentual'] = $resp['percentual'];
            $movimentos[$cont]['fixo'] = $resp['fixo'];
            $movimentos[$cont]['anobase'] = $resp['anobase'];
            $movimentos[$cont]['data_ini'] = $resp['data_ini'];
            $movimentos[$cont]['data_fim'] = $resp['data_fim'];
            $movimentos[$cont]['piso'] = $resp['piso'];
            $movimentos[$cont]['teto'] = $resp['teto'];
            $movimentos[$cont]['user_cad'] = $resp['user_cad'];
            $movimentos[$cont]['user_alter'] = $resp['user_alter'];
            $movimentos[$cont]['ultima_alter'] = $resp['ultima_alter'];
            $movimentos[$cont]['mov_novo'] = $resp['mov_novo'];
            $movimentos[$cont]['ultima_alter'] = $resp['ultima_alter'];
            $movimentos[$cont]['mov_lancavel'] = $resp['mov_lancavel'];
            $movimentos[$cont]['campo_rescisao'] = $resp['campo_rescisao'];
            $movimentos[$cont]['incidencia_inss'] = $resp['incidencia_inss'];
            $movimentos[$cont]['incidencia_irrf'] = $resp['incidencia_irrf'];
            $movimentos[$cont]['incidencia_fgts'] = $resp['incidencia_fgts'];
            $movimentos[$cont]['cod_ses'] = $resp['cod_ses'];
            $movimentos[$cont]['incidencia'] = ($resp['incidencia']!='0,0,0') ? $resp['incidencia'] : '';
            $movimentos[$cont]['tipo_qnt_lancavel'] = $resp['tipo_qnt_lancavel'];
//            $cont++;
        }
        return $movimentos;
    }
}
