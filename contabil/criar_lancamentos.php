
<form>        
<div id="container">
    <div id="form">
        <div><label>Projeto</label><br><input type="text"  name="projeto" ><br>
        <label>Mês</label><br> <input type="text"  name="mes" ><br>
        <label>Ano</label><br> <input type="text"  name="ano" ><br><br>
            
        <input type="submit" name="salvar" value="Salvar"><br><br><br>

        <?php
        include("../conn.php");
        include("../wfunction.php");
        include("../classes/ContabilLancamentoClass.php");
        include("../classes/ContabilLancamentoItemClass.php");
        
        $objLancamento = new ContabilLancamentoClass();
        $objLancamentoItens = new ContabilLancamentoItemClass;
         
        $sql = "SELECT 'saida' es, A.valor, B.nome, A.tipo cd, A.id_saida id_codigo, A.id_user, A.id_projeto, A.data_vencimento, A.id_banco, LTRIM(IF(A.especifica = '', A.nome, A.especifica)) historico, A.id_prestador
            FROM saida A
            INNER JOIN bancos B ON(B.id_banco = A.id_banco)
            WHERE A.data_vencimento BETWEEN '{$_REQUEST['ano']}-{$_REQUEST['mes']}-01' AND LAST_DAY('{$_REQUEST['ano']}-{$_REQUEST['mes']}-01') AND A.`status` = 2 AND A.id_projeto = '{$_REQUEST['projeto']}'
            UNION ALL
            SELECT 'entrada' es, A.valor, B.nome, A.tipo cd, A.id_entrada id_codigo, A.id_user, A.id_projeto, A.data_vencimento, A.id_banco, LTRIM(IF(A.especifica = '', A.nome, A.especifica)) historico, ''
            FROM entrada A
            INNER JOIN bancos B ON(B.id_banco = A.id_banco)
            WHERE A.data_vencimento BETWEEN '{$_REQUEST['ano']}-{$_REQUEST['mes']}-01' AND LAST_DAY('{$_REQUEST['ano']}-{$_REQUEST['mes']}-01') AND A.`status` = 2 AND A.id_projeto = '{$_REQUEST['projeto']}'
            ORDER BY data_vencimento";
        
        $qry = mysql_query($sql);
        
        while($row = mysql_fetch_assoc($qry)) {
            
            $historico = addslashes($row['historico']);            
            $valor = $row['valor'];
            $valor = str_replace(',', '.', $valor);
            $valor_str = number_format($valor, 2, ',', '.'); 
            if($row['es'] == 'saida') {
                $qry1 = mysql_query("SELECT * FROM contabil_lancamento WHERE id_saida = '{$row['id_codigo']}'");
                if(mysql_num_rows($qry1) == 0) {
                    $insert = "INSERT INTO contabil_lancamento (id_projeto, id_usuario, id_saida, data_lancamento, historico, contabil, status)
                            VALUES ('{$row['id_projeto']}','{$row['id_user']}','{$row['id_codigo']}','{$row['data_vencimento']}','$historico', 1, 1)";
                    echo $insert.' saida<br>';
                    mysql_query($insert) or die(mysql_error());
                    $id_lanc = mysql_insert_id();
                } else {
                   $row1 = mysql_fetch_assoc($qry1);
                   $id_lanc = $row1['id_lancamento'];
                }
                $qryLs = mysql_query("SELECT * FROM contabil_lancamento_itens WHERE id_lancamento = $id_lanc");
                
                if(mysql_num_rows($qryLs) == 0) {
                    $insertTipo1 = "INSERT INTO contabil_lancamento_itens (id_lancamento, id_conta, valor, documento, tipo, status, fornecedor)
                                    VALUES ($id_lanc, '{$row['cd']}', '{$valor}', '{$row['documento']}', '2', '1', '{$row['id_prestador']}')";
                                    echo $insertTipo1.'tipo 1<br>';
                                    
                                mysql_query($insertTipo1) or die (mysql_error());
                    
                    $insertTipo2 = "INSERT INTO contabil_lancamento_itens (id_lancamento, id_conta, valor, documento, tipo, status, fornecedor)
                                    VALUES ($id_lanc, '{$row['id_banco']}', '{$valor}', '{$row['documento']}', '1', '1', '{$row['id_prestador']}')";           
                                    echo $insertTipo2.'tipo 2<br>';
                                mysql_query($insertTipo2) or die (mysql_error());
                }
            } 
            if($row['es'] == 'entrada'){
                $qry1 = mysql_query("SELECT id_lancamento FROM contabil_lancamento WHERE id_entrada = '{$row['id_codigo']}'");
                if(mysql_num_rows($qry1) == 0) {
                    $insert = "INSERT INTO contabil_lancamento (id_projeto, id_usuario, id_entrada, data_lancamento, historico, contabil, status)
                               VALUES ('{$row['id_projeto']}','{$row['id_user']}','{$row['id_codigo']}','{$row['data_vencimento']}','$historico', 1, 1)";
                    echo $insert.'entrada<br>';
                    mysql_query($insert) or die(mysql_error());
                    $id_lanc = mysql_insert_id();
                } else {
                   $row1 = mysql_fetch_assoc($qry1);
                   $id_lanc = $row1['id_lancamento'];
                }
                $qryLs = mysql_query("SELECT id_lancamento FROM contabil_lancamento_itens WHERE id_lancamento = $id_lanc");
                if(mysql_num_rows($qryLs) == 0) {
                    $insertTipo1 = "INSERT INTO contabil_lancamento_itens (id_lancamento, id_conta, valor, documento, tipo, status)
                            VALUES ('{$id_lanc}', '{$row['id_banco']}', '{$valor}', '{$row['documento']}', '2', '1')";
                            echo $insertTipo1.'tipo 1<br>';
                            mysql_query($insertTipo1) or die (mysql_error());
                    $insertTipo2 = "INSERT INTO contabil_lancamento_itens (id_lancamento, id_conta, valor, documento, tipo, status)
                            VALUES ('{$id_lanc}', '{$row['cd']}', '{$valor}', '{$row['documento']}', '1', '1')";
                            echo $insertTipo2.'tipo 2<br>';
                            mysql_query($insertTipo2) or die (mysql_error());
                }
            } 
        }?>
    </div>
</div>
</form>

</body>