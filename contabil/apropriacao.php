<form>
    <div id="container">

        <div id="form">
            <div><label>Projeto</label><br><input type="text"  name="projeto" ><br>
                <label>M�s</label><br> <input type="text"  name="mes" ><br>
                <label>Ano</label><br> <input type="text"  name="ano" ><br><br>

                <input type="submit" name="salvar" value="Salvar">

            </div>
            <br><br>
            
            <?php
            include("../conn.php");
            include("../wfunction.php");
            include("../classes/ContabilLoteClass.php");

            $usuario = carregaUsuario();

            function formato_valor($valor, $dec_pt = '.') {
                if ($dec_pt == ',') {
                    if (strpos($valor, ',') == FALSE) {
                        return number_format($valor, 2, ',', '.');
                    } else {
                        return $valor;
                    }
                } else if ($dec_pt == '.') {
                    if (strpos($valor, ',') == FALSE) {
                        return $valor;
                    } else {
                        return str_replace(',', '.', str_replace('.', '', $valor));
                    }
                }
            }
            ?>
            <div class="bg-info">
                <label class="text text-info"><strong>FOLHA</strong><br><br>
                    <?php
                    // -- apropria��o da folha de pagamentos de sal�rios
                    $qry_movimentos = "SELECT * FROM rh_folha AS A WHERE A.projeto = '{$_REQUEST['projeto']}' AND A.mes = '{$_REQUEST['mes']}' AND A.ano = '{$_REQUEST['ano']}'  AND A.terceiro != 1";

                    $result = mysql_query($qry_movimentos) or die("Movimentos para Folha" . mysql_error());

                    $array_Movimento = mysql_fetch_assoc($result);
                    $id_folha = $array_Movimento['id_folha'];

                    $qry_movimentos_folha = "SELECT id_movimento, tipo_movimento, cod_movimento, nome_movimento historico, ROUND(SUM(valor_movimento),2) AS valor, user_cad id_user
                                FROM rh_movimentos_clt
                                WHERE id_movimento IN({$array_Movimento['ids_movimentos_estatisticas']})
                                GROUP BY cod_movimento";

                    $result = mysql_query($qry_movimentos_folha) or die("Lancamento dos movimentos na folha" . mysql_error());

                    while ($row = mysql_fetch_assoc($result)) {
                        $array_Folha[$row['cod_movimento']] = $row;
                    }

                    $qry_folha = "SELECT 
                    ROUND(SUM(A.sallimpo_real),2) '1', 
                    ROUND(SUM(A.a5019),2) '5019',
                    ROUND(SUM(A.a5020),2) '5020', 
                    ROUND(SUM(A.a5021),2) '5021', 
                    ROUND(SUM(A.a5035),2) '5035', 
                    ROUND(SUM(A.a5036),2) '5036', 
                    ROUND(SUM(A.a5037),2) '5037', 
                    ROUND(SUM(A.a5044),2) '5044', 
                    ROUND(SUM(A.a5049),2) '5049', 
                    ROUND(SUM(A.a6005),2) '6005', 
                    ROUND(SUM(A.a6007),2) '6007', 
                    ROUND(SUM(A.a7001),2) '7001', 
                    ROUND(SUM(A.a9000),2) '9000', 
                    ROUND(SUM(A.a9500),2) '9500', 
                    0.00 '1001',
                    0.00 'saldo_menor', 
                    0.00 'saldo_maior',
                    B.id_folha, 
                    B.user id_user
                    FROM rh_folha_proc A
                    INNER JOIN rh_folha B ON (B.id_folha = A.id_folha)
                    WHERE A.id_folha IN({$array_Movimento['id_folha']})";

                    $result = mysql_query($qry_folha) or die("Folha de pagamento salarios processada" . mysql_error());

                    while ($row = mysql_fetch_assoc($result)) {

                        $array_Folha['1'][id_movimento] = $row['id_folha'];
                        $array_Folha['1'][tipo_movimento] = 'CREDITO';
                        $array_Folha['1'][cod_movimento] = '1';
                        $array_Folha['1'][valor] += $row['1'];
                        $array_Folha['1'][historico] = 'SAL�RIO';
                        $array_Folha['1'][id_user] = $row['id_user'];

                        $array_Folha['5019'][id_movimento] = $row['id_folha'];
                        $array_Folha['5019'][tipo_movimento] = 'DEBITO';
                        $array_Folha['5019'][cod_movimento] = '5019';
                        $array_Folha['5019'][valor] += $row['5019'];
                        $array_Folha['5019'][historico] = 'CONTRIBUI��O SINDICAL';
                        $array_Folha['5019'][id_user] = $row['id_user'];

                        $array_Folha['5020'][id_movimento] = $row['id_folha'];
                        $array_Folha['5020'][tipo_movimento] = 'DEBITO';
                        $array_Folha['5020'][cod_movimento] = '5020';
                        $array_Folha['5020'][valor] += $row['5020'];
                        $array_Folha['5020'][historico] = 'INSS';
                        $array_Folha['5020'][id_user] = $row['id_user'];

                        $array_Folha['5021'][id_movimento] = $row['id_folha'];
                        $array_Folha['5021'][tipo_movimento] = 'DEBITO';
                        $array_Folha['5021'][cod_movimento] = '5021';
                        $array_Folha['5021'][valor] += $row['5021'];
                        $array_Folha['5021'][historico] = 'IR S/SAL�RIO';
                        $array_Folha['5021'][id_user] = $row['id_user'];

                        $array_Folha['6005'][id_movimento] = $row['id_folha'];
                        $array_Folha['6005'][tipo_movimento] = 'CREDITO';
                        $array_Folha['6005'][cod_movimento] = '6005';
                        $array_Folha['6005'][valor] += $row['6005'];
                        $array_Folha['6005'][historico] = 'SAL�RIO MATERNIDADE';
                        $array_Folha['6005'][id_user] = $row['id_user'];

                        $array_Folha['7001'][id_movimento] = $row['id_folha'];
                        $array_Folha['7001'][tipo_movimento] = 'DEBITO';
                        $array_Folha['7001'][cod_movimento] = '7001';
                        $array_Folha['7001'][valor] += $row['7001'];
                        $array_Folha['7001'][historico] = 'VALE TRANSPORTE';
                        $array_Folha['7001'][id_user] = $row['id_user'];
                    }

                    $qryArrayAssoc = "SELECT A.id_assoc, A.id_conta folha, A.id_contabil conta, A.`status`, A.id_projeto 
                        FROM contabil_contas_assoc A
                        WHERE A.folha = 1 AND A.status = 1 AND A.id_contabil IN(SELECT id_conta FROM contabil_planodecontas WHERE status = 1 AND id_projeto = A.id_projeto) AND A.id_projeto = '{$_REQUEST['projeto']}'";

                    $result1 = mysql_query($qryArrayAssoc) or die("Associa��o Folha X Contabilidade " . mysql_error());

                    while ($row = mysql_fetch_assoc($result1)) {
                        if (isset($array_Folha[$row['folha']])) {
                            $array_Folha[$row['folha']]['id_contabil'] = $row['conta'];
                        }

                        $assocs[$row['folha']] = $row['conta'];

                        if (!empty($array_Folha[$row['folha']])) {
                            $xN[] = $row;
                            if ($array_Folha[$row['folha']]['tipo_movimento'] === 'CREDITO') {
                                $arr_crdN[] = $array_Folha[$row['folha']][valor];
                            } else if ($array_Folha[$row['folha']]['tipo_movimento'] === 'DEBITO') {
                                $arr_debN[] = $array_Folha[$row['folha']][valor];
                            }
                        }
                    }

                    $array_Folha['1001'][id_movimento] = '1001';
                    $array_Folha['1001']['tipo_movimento'] = 'DEBITO';
                    $array_Folha['1001']['cod_movimento'] = '1001';
                    $array_Folha['1001'][valor] = number_format((array_sum($arr_crdN)) - (array_sum($arr_debN)), 2, '.', '');
                    $array_Folha['1001'][historico] = 'SAL�RIO � PAGAR';
                    $array_Folha['1001']['id_contabil'] = $assocs['1001'];


                    // gerando lancamento multiplo (SAL�RIOS)
                    $qry1 = mysql_query("SELECT * FROM contabil_lancamento WHERE id_folha = '{$id_folha}' AND folha = '1'");
                    if (mysql_num_rows($qry1) == 0) {
                        $insert = "INSERT INTO contabil_lancamento (id_projeto, id_usuario, id_folha, data_lancamento, historico, contabil, status, folha)
                       VALUES ('{$array_Movimento['projeto']}','{$usuario['id_funcionario']}','{$array_Movimento['id_folha']}','{$array_Movimento['data_proc']}','FOLHA DE PAGAMENTO COMP {$array_Movimento['mes']}/{$array_Movimento['ano']}', 1, 1, 1)";

                        echo $insert . 'apropria��o da folha<br>';
                        mysql_query($insert) or die(mysql_error());
                        $id_lanc = mysql_insert_id();
                    } else {
                        $row1 = mysql_fetch_assoc($qry1);
                        $id_lanc = $row1['id_lancamento'];
                    }

                    foreach ($array_Folha as $row) {

                        if ($row['tipo_movimento'] == 'CREDITO') {
                            $tipo = 2;
                        } else {
                            $tipo = 1;
                        }

                        $qryItens = mysql_query("SELECT * FROM contabil_lancamento_itens WHERE id_lancamento = $id_lanc");
                        $insertItens = "INSERT INTO contabil_lancamento_itens (id_lancamento, id_conta, valor, documento, historico, tipo, status)
                                VALUES ($id_lanc, '{$row['id_contabil']}', '{$row['valor']}', '', '{$row['historico']}', $tipo, '1')";

                        echo $insertItens . ' itens do lan�amento.' . $tipo . '.<br>';
                        mysql_query($insertItens) or die(mysql_error());
                        //  }
                    }
                    ?>
                </label>
            </div>
            <br><br>
            
            <div class="bg-info">
                <label><strong>F�RIAS</strong><br><br>
                    <?php
                    $qry_ferias = "SELECT SUM(A.total_remuneracoes) '5037', SUM(A.total_liquido) '80020', SUM(A.inss) '15038', SUM(A.ir) '5036', SUM(A.pensao_alimenticia) '15039', A.id_clt, A.projeto
                            FROM rh_ferias A 
                            WHERE A.projeto = '{$_REQUEST['projeto']}' AND A.mes = '{$_REQUEST['mes']}' AND A.ano ='{$_REQUEST['ano']}'";

                    $result_ferias = mysql_query($qry_ferias) or die("Folha de pagamento f�rias processada" . mysql_error());

                    while ($row = mysql_fetch_assoc($result_ferias)) {
                        $projeto = $row['projeto'];

                        //          $array_Ferias['5037'][id_movimento] = $row['id_ferias'];
                        $array_Ferias['5037'][tipo_movimento] = 'CREDITO';
                        $array_Ferias['5037'][cod_movimento] = '5037';
                        $array_Ferias['5037'][valor] += $row['5037'];
                        $array_Ferias['5037'][historico] = 'ADIANTAMENTO DE F�RIAS';
//            $array_Ferias[$row['id_clt']]['5037'][documento] = 'id_clt - '.$row['id_clt'];
                        $array_Ferias['5037'][id_projeto] = $row['projeto'];
                        $array_Ferias['5037'][id_user] = $row['id_user'];

                        //$array_Ferias['80020'][id_movimento] = $row['id_ferias'];
                        $array_Ferias['80020'][tipo_movimento] = ' DEBITO';
                        $array_Ferias['80020'][cod_movimento] = '80020';
                        $array_Ferias['80020'][valor] += $row['80020'];
                        $array_Ferias['80020'][historico] = 'PROVIS�O DE F�RIAS';
                        $array_Ferias['80020'][documento] = '';
                        $array_Ferias['80020'][id_user] = $row['id_user'];
                        $array_Ferias['80020'][id_projeto] = $row['projeto'];

//            $array_Ferias['5036'][id_movimento] = $row['id_ferias'];
                        $array_Ferias['5036'][tipo_movimento] = 'DEBITO';
                        $array_Ferias['5036'][cod_movimento] = '5036';
                        $array_Ferias['5036'][valor] += $row['5036'];
                        $array_Ferias['5036'][historico] = 'PROVIS�O DE F�RIAS';
                        $array_Ferias['5036'][documento] = 'IR S/F�RIAS';
                        $array_Ferias['5036'][id_projeto] = $row['projeto'];
                        $array_Ferias['5036'][id_user] = $row['id_user'];

                        //        $array_Ferias['15038'][id_movimento] = $row['id_ferias'];
                        $array_Ferias['15038'][tipo_movimento] = 'DEBITO';
                        $array_Ferias['15038'][cod_movimento] = '15038';
                        $array_Ferias['15038'][valor] += $row['15038'];
                        $array_Ferias['15038'][historico] = 'PROVIS�O DE F�RIAS';
                        $array_Ferias['15038'][documento] = 'INSS S/F�RIAS';
                        $array_Ferias['15038'][id_projeto] = $row['projeto'];
                        $array_Ferias['15038'][id_user] = $row['id_user'];
                        //        $array_Ferias['15038'][id_movimento] = $row['id_ferias'];
                        $array_Ferias['15039'][tipo_movimento] = 'DEBITO';
                        $array_Ferias['15039'][cod_movimento] = '15039';
                        $array_Ferias['15039'][valor] += $row['15039'];
                        $array_Ferias['15039'][historico] = 'PROVIS�O DE F�RIAS';
                        $array_Ferias['15039'][documento] = 'PENS�O ALIMENT�CIA';
                        $array_Ferias['15039'][id_projeto] = $row['projeto'];
                        $array_Ferias['15039'][id_user] = $row['id_user'];
                    }

                    $qryArrayAssoc = "SELECT A.id_assoc, A.id_conta folha, A.id_contabil conta, A.`status`, A.id_projeto
                                    FROM contabil_contas_assoc A
                                    WHERE A.folha = 2 AND A.status = 1 AND A.id_contabil IN(SELECT id_conta FROM contabil_planodecontas WHERE status = 1 AND id_projeto = A.id_projeto) AND A.id_projeto = '{$_REQUEST['projeto']}'";

                    $result2 = mysql_query($qryArrayAssoc) or die("Associa��o F�rias X Contabilidade " . mysql_error());

                    while ($row = mysql_fetch_assoc($result2)) {
                        if (isset($array_Ferias[$row['folha']])) {
                            $array_Ferias[$row['folha']]['id_contabil'] = $row['conta'];
                        }
                    }

                    // gerando lancamento multiplo (F�RIAS)
                    $qry1 = mysql_query("SELECT * FROM contabil_lancamento WHERE id_folha = '{$id_folha}' AND folha = '2'");
                    if (mysql_num_rows($qry1) == 0) {
                        $insert = "INSERT INTO contabil_lancamento (id_projeto, id_usuario, id_folha, data_lancamento, historico, contabil, status, folha)
                       VALUES ('{$projeto}','{$usuario['id_funcionario']}','{$array_Movimento['id_folha']}','{$array_Movimento['data_proc']}','FOLHA DE F�RIAS COMP {$array_Movimento['mes']}/{$array_Movimento['ano']}', 1, 1, 2)";

                        echo $insert . 'apropria��o da f�rias<br>';
                        mysql_query($insert) or die(mysql_error());
                        $id_lanc = mysql_insert_id();
                    } else {
                        $row1 = mysql_fetch_assoc($qry1);
                        $id_lanc = $row1['id_lancamento'];
                    }

                    foreach ($array_Ferias as $row) {
                        if ($row['tipo_movimento'] == 'CREDITO') {
                            $tipo = 2;
                        } else {
                            $tipo = 1;
                        }

                        $qryItens = mysql_query("SELECT * FROM contabil_lancamento_itens WHERE id_lancamento = $id_lanc");
                        if (mysql_num_rows($qryItens) == 0) {
                            $insertItens = "INSERT INTO contabil_lancamento_itens (id_lancamento, id_conta, valor, documento, historico, tipo, status)
                                VALUES ($id_lanc, '{$row['id_contabil']}', '{$row['valor']}', '{$row['documento']}', '{$row['historico']}', $tipo, '1')";

                            echo $insertItens . ' itens do lan�amento.' . $tipo . '.<br>';
                            mysql_query($insertItens) or die(mysql_error());
                        }
                    } ?>
                </label>
            </div>
            <br><br>

            <div class="bg-info">
                <label><strong>RESCIS�O</strong><br>
                    <?php
                    // -- RESCIS�O --
                    
                    $qry_rescisao = "SELECT * FROM rh_recisao WHERE id_projeto = '{$_REQUEST['projeto']}' AND status != 0 AND (data_demi BETWEEN '{$_REQUEST['ano']}-{$_REQUEST['mes']}-01' AND LAST_DAY('{$_REQUEST['ano']}-{$_REQUEST['mes']}-01'))";

                    $result = mysql_query($qry_rescisao) or die("CONSULTA RESCIS�O" . mysql_error());

                    while ($row = mysql_fetch_assoc($result)) {
                        $lista_id[] = $row['id_recisao'];
                        $lista_clt[] = $row['id_clt'];
                    }

                    $id_rescisao = implode(",", $lista_id);
                    $id_clt = implode(",", $lista_clt);

                    $qry_Rescisao1 = "SELECT A.data_demi, A.id_clt, A.id_recisao, A.aviso_valor '103', A.previdencia_ss '1121', A.inss_dt '1122' , A.ir_ss '1141',
                        A.saldo_salario '50', A.insalubridade '53', A.dt_salario '63', A.ferias_pr '65', A.ferias_vencidas '66', A.lei_12_506 '95', (A.umterco_fp + A.umterco_fv) '68'
                        FROM rh_recisao A 
                        WHERE A.id_projeto = '{$_REQUEST['projeto']}' AND A.status != 0 AND (A.data_demi BETWEEN '{$_REQUEST['ano']}-{$_REQUEST['mes']}-01' AND LAST_DAY('{$_REQUEST['ano']}-{$_REQUEST['mes']}-01'))";

                    $resultRescisao1 = mysql_query($qry_Rescisao1) or die("Folha de Rescis�o" . mysql_error());

                    while ($row = mysql_fetch_assoc($resultRescisao1)) {
                        
                        $array_Rescisao['50'][id_movimento] = $row['id_recisao'];
                        $array_Rescisao['50'][tipo_movimento] = 'DEBITO';
                        $array_Rescisao['50'][cod_movimento] = '50';
                        $array_Rescisao['50'][valor] = $row['50'];
                        $array_Rescisao['50'][historico] = 'SALDO DE SALARIO';
                        $array_Rescisao['50'][id_user] = $row['id_user'];

                        $array_Rescisao['103'][id_movimento] = $row['id_recisao'];
                        $array_Rescisao['103'][tipo_movimento] = 'DEBITO';
                        $array_Rescisao['103'][cod_movimento] = '103';
                        $array_Rescisao['103'][valor] = $row['103'];
                        $array_Rescisao['103'][historico] = 'AVISO-PR�VIO INDENIZADO';
                        $array_Rescisao['103'][id_user] = $row['id_user'];

                        $array_Rescisao['1121'][id_movimento] = $row['id_recisao'];
                        $array_Rescisao['1121'][tipo_movimento] = 'DEBITO';
                        $array_Rescisao['1121'][cod_movimento] = '1121';
                        $array_Rescisao['1121'][valor] = $row['1121'];
                        $array_Rescisao['1121'][historico] = 'PREVID�NCIA SOCIAL';
                        $array_Rescisao['1121'][id_user] = $row['id_user'];

                        $array_Rescisao['1122'][id_movimento] = $row['id_recisao'];
                        $array_Rescisao['1122'][tipo_movimento] = 'CREDITO';
                        $array_Rescisao['1122'][cod_movimento] = '1122';
                        $array_Rescisao['1122'][valor] = $row['1122'];
                        $array_Rescisao['1122'][historico] = 'PREVID�NCIA SOCIAL (13� SAL�RIO)';
                        $array_Rescisao['1122'][id_user] = $row['id_user'];

                        $array_Rescisao['1141'][id_movimento] = $row['id_recisao'];
                        $array_Rescisao['1141'][tipo_movimento] = 'CREDITO';
                        $array_Rescisao['1141'][cod_movimento] = '1141';
                        $array_Rescisao['1141'][valor] = $row['1141'];
                        $array_Rescisao['1141'][historico] = 'IRRF';
                        $array_Rescisao['1141'][id_user] = $row['id_user'];

                    }
                    if (!empty($id_rescisao)) {
                        $qry_Rescisao2 = "SELECT A.id_rescisao 'id_folha',  A.id_clt, A.valor, A.id_mov 'cod_movimento', A.nome_movimento 'historico'
                            FROM rh_movimentos_rescisao A WHERE `status` != 0 AND valor > 0 AND id_rescisao IN($id_rescisao) GROUP BY id_mov";

                        $resultRescisao2 = mysql_query($qry_Rescisao2) or die("RESCIS�O SEM  " . mysql_error());

                        while ($row = mysql_fetch_assoc($resultRescisao2)) {
                            $array_Rescisao[$row['cod_movimento']] = $row;
                        }
                    }

                    $qryArrayAssoc = "SELECT A.id_assoc, A.id_conta folha, A.id_contabil conta, A.`status`, A.id_projeto 
                                    FROM contabil_contas_assoc A
                                    WHERE A.folha = 3 AND A.status = 1 AND A.id_contabil IN(SELECT id_conta FROM contabil_planodecontas WHERE status = 1 AND id_projeto = A.id_projeto) AND A.id_projeto = '{$_REQUEST['projeto']}'";

                    $result2 = mysql_query($qryArrayAssoc) or die("Associa��o Rescis�o X Contabilidade " . mysql_error());

                    while ($row = mysql_fetch_assoc($result2)) {
                        if (isset($array_Rescisao[$row['folha']])) {
                            $array_Rescisao[$row['folha']]['id_contabil'] = $row['conta'];
                        }
                    }

                    // gerando lancamento multiplo (RESCIS�O)
                    $qry1 = mysql_query("SELECT * FROM contabil_lancamento WHERE id_folha = '{$id_folha}' AND folha = '3'");
                    if (mysql_num_rows($qry1) == 0) {
                        $insert = "INSERT INTO contabil_lancamento (id_projeto, id_usuario, id_folha, data_lancamento, historico, contabil, status, folha)
                       VALUES ('{$projeto}','{$usuario['id_funcionario']}','{$array_Movimento['id_folha']}','{$array_Movimento['data_proc']}','FOLHA DE RESCIS�O COMP {$array_Movimento['mes']}/{$array_Movimento['ano']}', 1, 1, 3)";

                        echo $insert . 'apropria��o da rescis�o<br>'; 
                        mysql_query($insert) or die(mysql_error());
                        $id_lanc = mysql_insert_id();
                    } else {
                        $row1 = mysql_fetch_assoc($qry1);
                        $id_lanc = $row1['id_lancamento'];
                    }
    
                    foreach ($array_Rescisao as $row) {
                        if ($row['tipo_movimento'] == 'CREDITO') {
                            $tipo = 2;
                        } else {
                            $tipo = 1;
                        }

                        $qryItens = mysql_query("SELECT * FROM contabil_lancamento_itens WHERE id_lancamento = $id_lanc");
                        $insertItens = "INSERT INTO contabil_lancamento_itens (id_lancamento, id_conta, valor, documento, historico, tipo, status)
                                VALUES ($id_lanc, '{$row['id_contabil']}', '{$row['valor']}', '{$row['documento']}', '{$row['historico']}', $tipo, '1')";

                        echo $insertItens . ' itens do lan�amento.' . $tipo . '.<br>';
                        mysql_query($insertItens) or die(mysql_error());
                    } ?>
                    
                </label>
            </div>
        </div>
    </div>
</form>
