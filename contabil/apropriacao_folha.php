<div id="container">
    <div id="form">
        <?php
        include("../conn.php");
        include("../wfunction.php");
        include("../classes/ContabilLoteClass.php");
        include("../classes/ContabilLancamentoClass.php");
        include("../classes/ContabilLancamentoItemClass.php");
        
        function formato_valor($valor) {
            if (strpos($valor, ',') == FALSE) {
                return number_format($valor, 2, ',', '.');
            } else {
                return $valor;
            }
        } 

        $objLancamento = new ContabilLancamentoClass();
        $objLancamentoItens = new ContabilLancamentoItemClass;
         
        $qry_movimentos = "SELECT A.id_folha, A.ids_movimentos_estatisticas FROM rh_folha AS A WHERE A.projeto = '3303' AND A.mes = '10' AND A.ano = '2016' AND A.terceiro != 1";
        
        $result = mysql_query($qry_movimentos) or die("Movimentos para Folha" . mysql_error());
        
        $array_Movimento = mysql_fetch_assoc($result);
        
        $qry_movimentos_folha = "SELECT id_movimento, tipo_movimento, cod_movimento, nome_movimento historico, ROUND(SUM(valor_movimento),2) AS valor FROM rh_movimentos_clt
                                WHERE id_movimento IN({$array_Movimento['ids_movimentos_estatisticas']}) GROUP BY cod_movimento";
                    
        $result = mysql_query($qry_movimentos_folha) or die("Lancamento dos movimentos na folha" . mysql_error());

        while ($row = mysql_fetch_assoc($result)) {
            $array_Folha[$row['cod_movimento']] = $row;
        }
        
        $qry_folha = "SELECT 
                    ROUND(SUM(A.sallimpo_real),2) '0001', ROUND(SUM(A.a5019),2) '5019', ROUND(SUM(A.a5020),2) '5020', ROUND(SUM(A.a5021),2) '5021', 
                    ROUND(SUM(A.a5035),2) '5035', ROUND(SUM(A.a5036),2) '5036', ROUND(SUM(A.a5037),2) '5037', ROUND(SUM(A.a5044),2) '5044', 
                    ROUND(SUM(A.a5049),2) '5049', ROUND(SUM(A.a6005),2) '6005', ROUND(SUM(A.a6007),2) '6007', ROUND(SUM(A.a7001),2) '7001', 
                    ROUND(SUM(A.a9000),2) '9000', ROUND(SUM(A.a9500),2) '9500', 0.00 'f000', 0.00 'saldo_menor', 0.00 'saldo_maior'
                    FROM rh_folha_proc A
                    INNER JOIN rh_folha B ON (B.id_folha = A.id_folha)
                    WHERE A.id_folha IN({$array_Movimento['id_folha']})";

        $result = mysql_query($qry_folha) or die("Folha de pagamento salarios processada" . mysql_error());

        while ($row = mysql_fetch_assoc($result)) {
            $array_Folha['0001'][tipo_movimento] = 'CREDITO';
            $array_Folha['0001'][cod_movimento] = '0001';
            $array_Folha['0001'][valor] += $row['0001'];
            $array_Folha['0001'][historico] = 'SALÁRIO';

            $array_Folha['5019'][tipo_movimento] = 'DEBITO';
            $array_Folha['5019'][cod_movimento] = '5019';
            $array_Folha['5019'][valor] += $row['5019'];
            $array_Folha['5019'][historico] = 'CONTRIBUIÇÃO SINDICAL';

            $array_Folha['5020'][tipo_movimento] = 'DEBITO';
            $array_Folha['5020'][cod_movimento] = '5020';
            $array_Folha['5020'][valor] += $row['5020'];
            $array_Folha['5020'][historico] = 'INSS';

            $array_Folha['5021'][tipo_movimento] = 'DEBITO';
            $array_Folha['5021'][cod_movimento] = '5021';
            $array_Folha['5021'][valor] += $row['5021'];
            $array_Folha['5021'][historico] = 'IR S/SALÁRIO';
            
            $array_Folha['6005'][tipo_movimento] = 'CREDITO';
            $array_Folha['6005'][cod_movimento] = '6005';
            $array_Folha['6005'][valor] += $row['6005'];
            $array_Folha['6005'][historico] = 'SALÁRIO MATERNIDADE';
            
            $array_Folha['7001'][tipo_movimento] = 'DEBITO';
            $array_Folha['7001'][cod_movimento] = '7001';
            $array_Folha['7001'][valor] += $row['7001'];
            $array_Folha['7001'][historico] = 'VALE TRANSPORTE';
        }  
        
        foreach ($array_Folha as $row) {
            
            $valor = $row['valor'];
            if($row['folha'] == 'salarios') {
                $qry1 = mysql_query("SELECT * FROM contabil_lancamento WHERE id_folha = '{$row['id_codigo']}'");
                if(mysql_num_rows($qry1) == 0) {
                    $insert = "INSERT INTO contabil_lancamento (id_projeto, id_usuario, id_folha, data_lancamento, historico, contabil, status)
                               VALUES ('{$row['id_projeto']}','{$row['id_user']}','{$row['id_codigo']}','{$row['data_vencimento']}','{$row['historico']}', 1, 1)";
                    echo $insert.' apropriação<br>';
                    mysql_query($insert) or die(mysql_error());
                    $id_lanc = mysql_insert_id();
                } else {
                   $row1 = mysql_fetch_assoc($qry1);
                   $id_lanc = $row1['id_lancamento'];
                }
                
                $qryLs = mysql_query("SELECT * FROM contabil_lancamento_itens WHERE id_lancamento = $id_lanc");
                
                if(mysql_num_rows($qryLs) == 0) {
                    if($row['tipo'] == 1) {
                        echo 'apropriação devedora<br>';
                       
                        $insertTipo1 = "INSERT INTO contabil_lancamento_itens (id_lancamento, id_conta, valor, documento, historico, tipo, status)
                                        VALUES ($id_lanc, '{$row['id_contabil']}', '{$valor}', '{$row['documento']}', '{$row['historico']}', '1', '1')";
                                        echo $insertTipo1.'<br>';
                                    
                        mysql_query($insertTipo1) or die (mysql_error());
                        
                        $insertTipo2 = "INSERT INTO contabil_lancamento_itens (id_lancamento, id_conta, valor, documento, historico, tipo, status)
                                        VALUES ($id_lanc, '1180', '{$valor}', '{$row['documento']}', '{$row['historico']}', '2', '1')";           
                                        echo $insertTipo2.'<br>';
                        
                        mysql_query($insertTipo2) or die (mysql_error());
                        
                    }
                    
                    if ($row['tipo'] == 2) { 
                        echo 'apropriação credora <br>';
                        
                        $insertTipo1 = "INSERT INTO contabil_lancamento_itens (id_lancamento, id_conta, valor, documento, historico, tipo, status)
                                        VALUES ($id_lanc, '1180', '{$valor}', '{$row['documento']}', '{$row['historico']}', '1', '1')";
                                        echo $insertTipo1.'<br>';
                                    
                        mysql_query($insertTipo1) or die (mysql_error());
                        
                        $insertTipo2 = "INSERT INTO contabil_lancamento_itens (id_lancamento, id_conta, valor, documento, historico, tipo, status)
                                        VALUES ($id_lanc, '{$row['id_contabil']}', '{$valor}', '{$row['documento']}', '{$row['historico']}', '2', '1')";           
                                        echo $insertTipo2.'<br>';
                    
                        mysql_query($insertTipo2) or die (mysql_error());
                    }
                }
            } 
            if($row['folha'] == 'ferias') {
                $qry1 = mysql_query("SELECT * FROM contabil_lancamento WHERE id_saida = '{$row['id_codigo']}'");
                if(mysql_num_rows($qry1) == 0) {
                    $insert = "INSERT INTO contabil_lancamento (id_projeto, id_usuario, id_saida, data_lancamento, historico, contabil, status)
                            VALUES ('{$row['id_projeto']}','{$row['id_user']}','{$row['id_codigo']}','{$row['data_vencimento']}','{$row['historico']}', 1, 1)";
                    echo $insert.'saida<br>';
                    mysql_query($insert) or die(mysql_error());
                    $id_lanc = mysql_insert_id();
                } else {
                   $row1 = mysql_fetch_assoc($qry1);
                   $id_lanc = $row1['id_lancamento'];
                }
                $qryLs = mysql_query("SELECT * FROM contabil_lancamento_itens WHERE id_lancamento = $id_lanc");
                
                if(mysql_num_rows($qryLs) == 0) {
                    $insertTipo1 = "INSERT INTO contabil_lancamento_itens (id_lancamento, id_conta, valor, documento, historico, tipo, status, fornecedor)
                                    VALUES ($id_lanc, '0', '{$valor}', '{$row['documento']}', '{$row['historico']}', '1', '1', '{$row['id_prestador']}')";
                                    echo $insertTipo1.'tipo 1<br>';
                                    
                                mysql_query($insertTipo1) or die (mysql_error());
                    
                    $insertTipo2 = "INSERT INTO contabil_lancamento_itens (id_lancamento, id_conta, valor, documento, historico, tipo, status, fornecedor)
                                    VALUES ($id_lanc, '0', '{$valor}', '{$row['documento']}', '{$row['historico']}', '2', '1', '{$row['id_prestador']}')";           
                                    echo $insertTipo2.'tipo 2<br>';
                                    
                                mysql_query($insertTipo2) or die (mysql_error());
                }
            } 
            if($row['folha'] == 'rescisoes') {
                $qry1 = mysql_query("SELECT * FROM contabil_lancamento WHERE id_saida = '{$row['id_codigo']}'");
                if(mysql_num_rows($qry1) == 0) {
                    $insert = "INSERT INTO contabil_lancamento (id_projeto, id_usuario, id_saida, data_lancamento, historico, contabil, status)
                            VALUES ('{$row['id_projeto']}','{$row['id_user']}','{$row['id_codigo']}','{$row['data_vencimento']}','{$row['historico']}', 1, 1)";
                    echo $insert.'saida<br>';
                    mysql_query($insert) or die(mysql_error());
                    $id_lanc = mysql_insert_id();
                } else {
                   $row1 = mysql_fetch_assoc($qry1);
                   $id_lanc = $row1['id_lancamento'];
                }
                $qryLs = mysql_query("SELECT * FROM contabil_lancamento_itens WHERE id_lancamento = $id_lanc");
                
                if(mysql_num_rows($qryLs) == 0) {
                    $insertTipo1 = "INSERT INTO contabil_lancamento_itens (id_lancamento, id_conta, valor, documento, historico, tipo, status, fornecedor)
                                    VALUES ($id_lanc, '0', '{$valor}', '{$row['documento']}', '{$row['historico']}', '1', '1', '{$row['id_prestador']}')";
                                    echo $insertTipo1.'CREDORA<br>';
                                    
                                mysql_query($insertTipo1) or die (mysql_error());
                    
                    $insertTipo2 = "INSERT INTO contabil_lancamento_itens (id_lancamento, id_conta, valor, documento, historico, tipo, status, fornecedor)
                                    VALUES ($id_lanc, '0', '{$valor}', '{$row['documento']}', '{$row['historico']}', '2', '1', '{$row['id_prestador']}')";           
                                    echo $insertTipo2.'DEVEDORA<br>';
                                    
                                mysql_query($insertTipo2) or die (mysql_error());
                }
            } 
        }?>
    </div>
</div>
</body>