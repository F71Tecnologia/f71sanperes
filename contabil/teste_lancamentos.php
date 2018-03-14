<form>
<div id="container">
    <div id="form">
         <div><label>Projeto</label><br><input type="text"  name="projeto" ><br>
                <label>Mês</label><br> <input type="text"  name="mes" ><br>
                <label>Ano</label><br> <input type="text"  name="ano" ><br><br>

                <input type="submit" name="salvar" value="Salvar"><br>
         </div>
        <?php
        include("../conn.php");
        include("../wfunction.php");
            
        $sqlArray = "SELECT A.id_assoc, A.id_conta tipo, A.id_contabil conta, A.id_fornecedor, A.`status`
            FROM contabil_contas_assoc A WHERE A.status = 1 AND A.id_contabil IN(SELECT id_conta FROM contabil_planodecontas WHERE status = 1 AND id_projeto = '{$_REQUEST['projeto']}')";

        $qryArray = mysql_query($sqlArray);
        while ($rowArray = mysql_fetch_assoc($qryArray)) {
            $arrayC[$rowArray['tipo']] = $rowArray['conta'];
            $arrayF[$rowArray['id_fornecedor']] = $rowArray['conta'];
        }
        
        $sqlArray = "SELECT A.id_assoc, A.id_banco, A.id_conta FROM contabil_contas_assoc_banco A WHERE A.id_conta IN(SELECT id_conta FROM contabil_planodecontas WHERE status = 1)";
        $qryArray = mysql_query($sqlArray);
        while ($rowArray = mysql_fetch_assoc($qryArray)) {
            $arrayB[$rowArray['id_banco']] = $rowArray['id_conta'];
        }

            $sql = "SELECT A.tipo cd, A.id_lancamento_itens, A.id_conta, C.tipo id_entradasaida, D.id_banco, 'saida' es, B.contabil, C.data_vencimento, B.id_lancamento, B.data_lancamento, B.id_projeto, D.nome, C.id_prestador, C.valor valor
            FROM contabil_lancamento_itens A
            INNER JOIN contabil_lancamento B ON (A.id_lancamento = B.id_lancamento AND B.status != 0)
            INNER JOIN saida C ON (C.id_saida = B.id_saida)
            INNER JOIN bancos D ON (C.id_banco = D.id_banco)
            WHERE A.status != 0 AND B.id_projeto = '{$_REQUEST['projeto']}' AND B.data_lancamento BETWEEN '{$_REQUEST['ano']}-{$_REQUEST['mes']}-01' AND LAST_DAY('{$_REQUEST['ano']}-{$_REQUEST['mes']}-01')
            UNION ALL
            SELECT A.tipo cd, A.id_lancamento_itens, A.id_conta, C.tipo id_entradasaida, D.id_banco, 'entrada' es, B.contabil, C.data_vencimento, B.id_lancamento, B.data_lancamento, B.id_projeto, D.nome, '', C.valor valor
            FROM contabil_lancamento_itens A
            INNER JOIN contabil_lancamento B ON (A.id_lancamento = B.id_lancamento AND B.status != 0)
            INNER JOIN entrada C ON (C.id_entrada = B.id_entrada)
            INNER JOIN bancos D ON (C.id_banco = D.id_banco)
            WHERE A.status != 0 AND B.id_projeto = '{$_REQUEST['projeto']}' AND B.data_lancamento BETWEEN '{$_REQUEST['ano']}-{$_REQUEST['mes']}-01' AND LAST_DAY('{$_REQUEST['ano']}-{$_REQUEST['mes']}-01')
            ORDER BY data_vencimento, id_lancamento, cd ASC";

        $qry = mysql_query($sql);
        while ($row = mysql_fetch_assoc($qry)) {
            $row['valor'] = $row['valor'];
            $row['cd'] .' <br>';
            $data_vencimento = explode('-', $row['data_vencimento']);
            $ano_vencimento = $data_vencimento[0];
            $mes_vencimento = $data_vencimento[1];

//            $objLote = new ContabilLoteClass();
//            $objLote->setIdProjeto($row['id_projeto']);
//            $objLote->setMes($mes_vencimento);
//            $objLote->setAno($ano_vencimento);
//            $objLote->setStatus(1);
//            $objLote->setUsuarioCriacao($_COOKIE['logado']);
//            $objLote->setDataCriacao(date('Y-m-d H:i:s'));
//            $objLote->setLoteNumero("{$row['nome']}" . sprintf("%02d", $mes_vencimento)."{$ano_vencimento} FINANCEIRO");
//            $objLote->setTipo(6);
//            $objLote->verificaLote();
//            $id_lote = mysql_result(mysql_query("SELECT id_lote FROM contabil_lote WHERE id_lote != 5 AND id_projeto = {$row['id_projeto']} AND tipo = 6 AND ano = YEAR('{$row['data_vencimento']}') AND mes = MONTH('{$row['data_vencimento']}') AND status = 1"), 0);
            $updateData = "UPDATE contabil_lancamento SET data_lancamento = '{$row['data_vencimento']}' WHERE id_lancamento = {$row['id_lancamento']}";
            echo $updateData .'<br>';
            $updateData = mysql_query($updateData)or die(mysql_error());
            if ($row['es'] == 'saida') {
                if ($row['cd'] == 2) {
                    if (empty($row['id_prestador'])) {
                        if ($arrayC[$row['id_entradasaida']]) {
                            $update = "UPDATE contabil_lancamento_itens SET id_conta = '{$arrayC[$row['id_entradasaida']]}' WHERE id_lancamento_itens = '{$row['id_lancamento_itens']}'";
                            echo '1 '.$update.'<br>';
                            mysql_query($update) or die(mysql_error());
                        }
                    } else if (!empty($row['id_prestador'])) {
                        if ($arrayF[$row['id_prestador']]) {
                            $update = "UPDATE contabil_lancamento_itens SET id_conta = '{$arrayF[$row['id_prestador']]}' WHERE id_lancamento_itens = '{$row['id_lancamento_itens']}'";
                            echo '2 '.$update.'<br>';
                            mysql_query($update) or die(mysql_error());
                        }
                    } else {
                        $update = "UPDATE contabil_lancamento_itens SET id_conta = 0 WHERE id_lancamento_itens = '{$row['id_lancamento_itens']}'";
                        echo '3 '.$update.'<br>';
                        mysql_query($update) or die(mysql_error());
                    }
                } else if ($row['cd'] == 1) {
                    if ($arrayB[$row['id_banco']]) {
                        $update = "UPDATE contabil_lancamento_itens SET id_conta = {$arrayB[$row['id_banco']]} WHERE id_lancamento_itens = {$row['id_lancamento_itens']}";
                        echo $update.'<br>';
                        mysql_query($update) or die(mysql_error());
                    } else {
                        $update = "UPDATE contabil_lancamento_itens SET id_conta = 0 WHERE id_lancamento_itens = {$row['id_lancamento_itens']}";
                        mysql_query($update) or die(mysql_error());
                    }
                }
            } else if ($row['es'] == 'entrada') {
                if ($row['cd'] == 1) {
                    if ($array[$row['id_entradasaida']]) {
                        $update = "UPDATE contabil_lancamento_itens SET id_conta = {$array[$row['id_entradasaida']]} WHERE id_lancamento_itens = {$row['id_lancamento_itens']}";
                        echo $update.'<br>';
                        mysql_query($update) or die(mysql_error());
                    } else {
                        $update = "UPDATE contabil_lancamento_itens SET id_conta = 0 WHERE id_lancamento_itens = {$row['id_lancamento_itens']}";
                        echo $update.'<br>';
                        mysql_query($update) or die(mysql_error());
                    }
                } else if ($row['cd'] == 2) {
                    if ($arrayB[$row['id_banco']]) {
                        $update = "UPDATE contabil_lancamento_itens SET id_conta = {$arrayB[$row['id_banco']]} WHERE id_lancamento_itens = {$row['id_lancamento_itens']}";
                        echo $update.'<br>';
                        mysql_query($update) or die(mysql_error());
                    } else {
                        $update = "UPDATE contabil_lancamento_itens SET id_conta = 0 WHERE id_lancamento_itens = {$row['id_lancamento_itens']}";
                        echo $update.'<br>';
                        mysql_query($update) or die(mysql_error());
                    }
                }
            } else {
                echo 'foo<br>';
            }
        }
        ?>
    </div>
</div>
</form>