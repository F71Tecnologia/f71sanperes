<?php

class GRRFClass{
    
    public function listaRescindidos($request) {
        $sql = "
            SELECT 
                A.fgts_cod, A.id_recisao, A.id_clt, A.nome, A.total_liquido,
                B.id_regiao, B.regiao,
                C.id_projeto, C.nome as nome_projeto,
                D.cod_movimentacao, D.especifica,
                E.pis, E.data_entrada, E.data_demi,
                F.nome AS nome_funcao
            FROM 
                rh_recisao A
                INNER JOIN regioes B ON A.id_regiao = B.id_regiao
                INNER JOIN projeto C ON A.id_projeto = C.id_projeto
                INNER JOIN rhstatus AS D ON (A.motivo = D.codigo)
                INNER JOIN rh_clt AS E ON(A.id_clt = E.id_clt)
                INNER JOIN curso AS F ON(A.id_curso = F.id_curso)
            WHERE 
                MONTH(A.data_demi) = '{$request['mes']}'
                AND YEAR(A.data_demi) = '{$request['ano']}'
                AND A.status = '1'
                AND A.rescisao_complementar != 1
                AND B.id_regiao IN({$request['regiao']})
                AND A.id_projeto IN({$request['projeto']})
                AND A.motivo NOT IN(60,63,65)
            ORDER BY B.id_regiao, A.nome ASC";
                
        if($_COOKIE['debug'] == 666){
            echo "<br><br># listaRescindidos #<br><br>";
            echo $sql;
        }
        
        $qr = mysql_query($sql);
        $qtdRegistro = mysql_num_rows($qr);
        
        $dados = array(
            "total" => $qtdRegistro
        );
        
        while($res = mysql_fetch_assoc($qr)){
            $dados['list'][$res['id_recisao']] = $res;
        }
        
        return $dados;
    }
    
    public function insereGRRF($request) {
        $valor = valorBrtoUs($request['valor']);
        
        $verificacao = GRRFClass::consultaValorInformado($request['id']);
        
        if($verificacao['total'] > 0){
            $update = "UPDATE import_grrf_lote SET valor = '{$valor}', data_import = NOW(), user_import = '{$_COOKIE['logado']}' WHERE id_importacao = {$verificacao['dados']['id_importacao']} LIMIT 1";
            $alt = mysql_query($update) or die(mysql_error());
            
            if($alt){
                return true;
            } else {
                return false;
            }
        } else {
            $insert = "INSERT INTO import_grrf_lote (id_importacao, id_clt, valor, data_import, user_import)
            VALUES ('', '{$request['id']}', '{$valor}', NOW(), '{$_COOKIE['logado']}');";
            $ins = mysql_query($insert) or die(mysql_error());
            
            if($ins){
                return true;
            } else {
                return false;
            }
        }
    }
    
    public static function consultaValorInformado($clt) {
        $qryVerifi = "SELECT *
            FROM import_grrf_lote
            WHERE id_clt = '{$clt}'";
        $sqlVerifi = mysql_query($qryVerifi) or die(mysql_error());

        $res = mysql_fetch_assoc($sqlVerifi);
        $tot = mysql_num_rows($sqlVerifi);
        
        $dados = array(
            "total" => $tot,
            "dados" => $res
        );
            
        return $dados;
    }
    
}

?>