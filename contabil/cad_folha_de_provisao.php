<?php
    ini_set('display_errors', 1);
    ini_set('display_startup_erros', 1);
    error_reporting(E_ALL);

    if (empty($_COOKIE['logado'])) {
        print "<script>location.href = '../login.php?entre=false';</script>";
    }

    include("../conn.php");
    include("../wfunction.php");
    include("../classes/BotoesClass.php");
    include("../classes/global.php");
    include("../classes/ContabilFolhaProvisaoClass.php");
    include("../classes/ContabilFolhaProvisaoProcClass.php");
    include("../classes/ContabilLancamentoClass.php");
    include("../classes/ContabilLancamentoItemClass.php");
    include("../classes_permissoes/acoes.class.php");

    $usuario = carregaUsuario();
    $id_regiao = $usuario['id_regiao'];

    $botoes = new BotoesClass("../img_menu_principal/");
    $icon = $botoes->iconsModulos;

    $objAcao = new Acoes();
    $objProvisao = new ContabilFolhaProvisaoClass();
    $objProvisaoProc = new ContabilFolhaProvisaoProcClass();

    $objLancamento = new ContabilLancamentoClass();
    $objLancamentoItem = new ContabilLancamentoItemClass();

    $projeto = ($_REQUEST['projeto'] > 0) ? $_REQUEST['projeto'] : null;

    if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'salvar' && !empty($_REQUEST['provisao'])) {

        list($id_folha, $mes, $ano) = explode('|',$_REQUEST['id_folha']);
        $titulo = $id_folha.' - '.$mes.' / '.$ano;  
        $objProvisao->setDefault();
        $objProvisao->setIdFolha($id_folha);
        $objProvisao->setIdRegiao($usuario['id_regiao']);
        $objProvisao->setIdProjeto($_REQUEST['id_projeto']);
        $objProvisao->setData(date('Y-m-d H:i:s'));
        $objProvisao->setQtd(count($_REQUEST['provisao']));
        $objProvisao->setIdFuncionario($usuario['id_funcionario']);
        $objProvisao->setTitulo($titulo);
        $objProvisao->setRescisao($_REQUEST['rescisao_tot']);
        $objProvisao->setMulta50($_REQUEST['multa50_tot']);
        $objProvisao->setFerias($_REQUEST['ferias_tot']);
        $objProvisao->setUmTerco($_REQUEST['umterco_tot']);
        $objProvisao->setDecimoTereiro($_REQUEST['dterceiro_tot']);
        $objProvisao->setPis($_REQUEST['pis1_tot']);
        $objProvisao->setFgts($_REQUEST['fgts8_tot']);
        $objProvisao->setInss($_REQUEST['inss20_tot']);
        $objProvisao->setOutras($_REQUEST['outros_tot']);
        $objProvisao->setLei12506($_REQUEST['lei12506_tot']);
        $objProvisao->setRat($_REQUEST['rat_tot']);
        $objProvisao->setRatPercent($_REQUEST['rat']);
        $objProvisao->setOutrosPercent($_REQUEST['outros']);
        $objProvisao->setStatus(1);
        $objProvisao->insert();

        foreach ($_REQUEST['provisao'] as $key => $value) {
            $objProvisaoProc->setDefault();
            $objProvisaoProc->setIdProvisao($objProvisao->getIdProvisao());
            $objProvisaoProc->setIdClt($value['id_clt']);
            $objProvisaoProc->setPis($value['pis']);
            $objProvisaoProc->setRescisao($value['rescisao']);
            $objProvisaoProc->setRescisao50($value['multa_rescisao']);
            $objProvisaoProc->setRescisaoParcela($value['rescisao_parcela']);
            $objProvisaoProc->setFerias($value['ferias']);
            $objProvisaoProc->setUmTerco($value['um_terco']);
            $objProvisaoProc->setDecimoTereiro($value['decimo_tereiro']);
            $objProvisaoProc->setRat($value['rat_valor']);
            $objProvisaoProc->setLei12506Valor($value['lei12506_valor']);
            $objProvisaoProc->setFgts($value['fgts_8']);
            $objProvisaoProc->setInss($value['inss_20']);
            $objProvisaoProc->setOutrasEntidades($value['outros']);
            $objProvisaoProc->setStatus(1);
            $objProvisaoProc->insert();
        }

        // lancamento contabil da provisao
        $objLancamento->setIdLote(0);
        $objLancamento->setIdProjeto($_REQUEST['id_projeto']);
        $objLancamento->setIdUsuario($usuario['id_funcionario']);
        $objLancamento->setIdSaida(0);
        $objLancamento->setIdEntrada(0);
        $objLancamento->setIdFolha($_REQUEST['id_folha']);
        $objLancamento->setFolha(4);
        $objLancamento->setDataLancamento($ano.'-'.$mes.'-01');
        $objLancamento->setHistorico('PROVISÃO DA FOLHA '.$titulo);
        $objLancamento->setContabil(1);
        $objLancamento->setStatus(1);
        $objLancamento->insert();
        
        $objLancamento->salvar(); // salva
        $id_lancamento = $objLancamento->getIdLancamento(); // substitui o idss
        echo $id_lancamento; 
        $objLancamento->setDefault();
        
        
        $query_lan_prov = "INSERT INTO contabil_lancamentos_provisao_folha_assoc (id_lancamento,id_provisao_folha) VALUES ('$id_lancamento','$id_provisao');";
        echo "INSERT INTO contabil_lancamentos_provisao_folha_assoc (id_lancamento,id_provisao_folha) VALUES ('$id_lancamento','$id_provisao')";
        while ($row = mysql_fetch_array($result_get_assoc)) {

            // conta passivo
            $objLancamentoItens->setIdLancamento($id);
            $objLancamentoItens->setIdConta(checkEmpty($row['id_conta_passivo']));
            $objLancamentoItens->setValor($arr_valores[$row['nome_coluna']]);
            $objLancamentoItens->setTipo(checkEmpty(1));
            $objLancamentoItens->setStatus(1);
            $objLancamentoItens->setHistorico('');
            $status = $status && $objLancamentoItens->salvar();
            $objContas->verificaSaldo2($objLancamentoItens->getIdLancamentoItens());

            $objLancamentoItens->setDefault(); // limpa campos
            // conta dre
            $objLancamentoItens->setIdLancamento($id);
            $objLancamentoItens->setIdConta(checkEmpty($row['id_conta_dre']));
            $objLancamentoItens->setValor($arr_valores[$row['nome_coluna']]);
            $objLancamentoItens->setTipo(checkEmpty(2));
            $objLancamentoItens->setStatus(1);
            $objLancamentoItens->setHistorico('');
            $status = $status && $objLancamentoItens->salvar();
            $objContas->verificaSaldo2($objLancamentoItens->getIdLancamentoItens());

            $objLancamentoItens->setDefault(); // limpa campos
        }

        header("Location: folha_de_provisao.php?s");
        exit;
    }

    if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'filtrar') {
        $sqlFolhas = "
                SELECT CONCAT(A.id_folha, ' - ', B.nome, ' ', A.mes, '/', A.ano) titulo_folha, A.id_folha, A.mes, A.ano
                FROM rh_folha A
                LEFT JOIN projeto B ON (A.projeto = B.id_projeto)
                WHERE A.regiao = {$usuario['id_regiao']} AND id_projeto = {$_REQUEST['id_projeto']} AND A.status = 3 AND A.terceiro = 2
                AND id_folha NOT IN (SELECT id_folha FROM contabil_folha_provisao WHERE id_regiao = {$usuario['id_regiao']} AND status = 1)
                ORDER BY A.projeto, A.ano DESC, A.mes DESC";

        $qryFolhas = mysql_query($sqlFolhas);
        echo "<!-- " . mysql_error() . " -->";

        if (mysql_num_rows($qryFolhas) > 0) { ?>

            <table class="table table-condensed text-sm valign-middle">
                <tbody>
                    <?php
                    while ($rowFolha = mysql_fetch_assoc($qryFolhas)) {

                        if ($auxAno != $rowFolha['ano']) { ?>
                            <tr class="bg-primary pointer ano_folha" data-key="<?= $rowFolha['ano'] ?>">
                                <td colspan="2" class="text-center"><?= $rowFolha['ano'] ?></td>
                            </tr>
                        <?php } ?>
                        <tr class="folha <?= $rowFolha['ano'] ?>" style="display: none;">
                            <td class="text-center">
                                <input type="radio" name="id_folha" class="radio" value="<?= $rowFolha['id_folha'] ?>|<?= $rowFolha['mes'] ?>|<?= $rowFolha['ano'] ?>" data-titulo="<?= utf8_encode($rowFolha['titulo_folha']) ?>">
                            </td>
                            <td><?= utf8_encode($rowFolha['titulo_folha']) ?></td>
                        </tr>
                        <?php
                        $auxAno = $rowFolha['ano'];
                    }
                    ?>
                </tbody>
            </table>

        <?php } else { ?>
            <div class="alert alert-info">Nenhuma Folha para Provisar</div>
        <?php }
        exit;
    }

    if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'lista_clt') {
        list($id_folha, $mes, $ano) = explode('|', $_REQUEST['id_folha']);
        $sqlEmp = "SELECT * FROM rhempresa WHERE id_projeto = {$_REQUEST['id_projeto']} LIMIT 1";
        $qryEmp = mysql_query($sqlEmp);
        $rowEmp = mysql_fetch_assoc($qryEmp);
        $percent_outras_entidades = $rowEmp['outras_entidades'];
        echo "<!-- " . mysql_error() . " -->";

        $sqlFerias = "SELECT id_clt, data_retorno AS retorno FROM rh_ferias WHERE projeto = {$_REQUEST['id_projeto']} AND mes = '{$mes}' AND ano = '{$ano}' AND status = 1";
        $result_f = mysql_query($sqlFerias) or die(mysql_error());

        while ($row = mysql_fetch_assoc($result_f)) {
            $array_ferias[$row['id_clt']] = $row;
        }

        $sqlClts = "SELECT A.id_clt id_clt , A.id_projeto id_projeto, '$ano' AS ano, '$mes' AS mes, A.nome nome, A.id_curso, B.id_curso, B.nome funcao, B.tipo_insalubridade, B.periculosidade_30, B.salario AS salariobase, A.data_entrada admissao,
                    CASE WHEN B.tipo_insalubridade = 1 THEN ROUND((SELECT v_ini FROM rh_movimentos WHERE cod = '0001' AND anobase = '{$ano}') * 0.20, 2) ELSE 0 END AS insalubridade_20,
                    CASE WHEN B.tipo_insalubridade = 2 THEN ROUND((SELECT v_ini FROM rh_movimentos WHERE cod = '0001' AND anobase = '{$ano}') * 0.40, 2) ELSE 0 END AS insalubridade_40, 
                    CASE WHEN B.periculosidade_30 = 1 THEN ROUND((SELECT v_ini FROM rh_movimentos WHERE cod = '0001' AND anobase = '{$ano}') * 0.30, 2) ELSE 0 END AS periculosidade_30,
                    ROUND(B.salario + 
                    CASE WHEN B.tipo_insalubridade = 1 THEN ROUND((SELECT v_ini FROM rh_movimentos WHERE cod = '0001' AND anobase = '{$ano}') * 0.20, 2) ELSE 0 END +
                    CASE WHEN B.tipo_insalubridade = 2 THEN ROUND((SELECT v_ini FROM rh_movimentos WHERE cod = '0001' AND anobase = '{$ano}') * 0.40, 2) ELSE 0 END + 
                    CASE WHEN B.periculosidade_30 = 1 THEN ROUND((SELECT v_ini FROM rh_movimentos WHERE cod = '0001' AND anobase = '{$ano}') * 0.30, 2) ELSE 0 END , 2) salario
                    FROM rh_clt A
                    INNER JOIN curso B ON(B.id_curso = A.id_curso)
                    WHERE A.id_projeto = '{$_REQUEST['id_projeto']}' AND A.data_entrada <= ('$ano-$mes-31') AND A.`status` NOT IN(81,80,66,60,61,63,64,65,67,68,101)
                    ORDER BY A.nome";


    //    $sqlClts = "SELECT A.id_clt id_clt , A.id_projeto id_projeto, A.nome nome, B.id_curso, D.nome funcao, A.sallimpo salariobase, C.valor_movimento complemento, C.cod_movimento, C.nome_movimento nomecomplemento, C.percent_movimento prectualmovimento, IF(C.valor_movimento IS NULL, A.sallimpo, C.valor_movimento + A.sallimpo) salario, A.ano ano, A.valor_ferias, A.mes mes, B.data_entrada admissao, A.t_inss inss, C.valor_movimento 
    //                    FROM rh_folha_proc A 
    //                    INNER JOIN rh_clt B ON(B.id_clt = A.id_clt AND B.data_saida = '0000-00-00') 
    //                    INNER JOIN curso D ON(D.id_curso = B.id_curso)
    //                    LEFT JOIN rh_movimentos_clt C ON(C.id_clt = A.id_clt AND C.id_folha = '{$id_folha}' AND C.cod_movimento IN('6006','6007','50251')) 
    //                    WHERE A.id_folha = '{$id_folha}' AND A.status = 3 ORDER BY nome";

        $result = mysql_query($sqlClts) or die(mysql_error());

        $sqlUltimoSalario = "SELECT * FROM contabil_folha_provisao_proc WHERE id_provisao = (SELECT max(id_provisao) FROM contabil_folha_provisao WHERE id_provisao AND id_projeto = {$_REQUEST['id_projeto']} AND status = 1 ORDER BY id_provisao DESC LIMIT 1)";
        $qryUltimoSalario = mysql_query($sqlUltimoSalario);
        $rowUltimoSalario = mysql_fetch_assoc($qryUltimoSalario);

        if (mysql_num_rows($result) > 0) { ?>

            <table class="table table-condensed table-hover table-striped valign-middle tbRelatorio">

                <?php
                $total = array();
                while ($row = mysql_fetch_assoc($result)) {
                    if (empty($total)) { ?>
                        <thead class="text small">
                            <tr>
                                <th  class="text text-center bg-info" colspan="13"><?= utf8_encode($rowEmp['nome']). ' ' .$row['mes']. ' /' .$row['ano'] ?></th>
                            </tr>
    <!--                        <tr>
                                <th class="text-left">COLABORADOR</th>
                                <th class="text-center"><?php echo utf8_encode('SALÁRIO') ?></th>
                                <th class="text-center">AVISO</th>
                                <th class="text-center">MULTA 50%</th>
                                <th class="text-center"><?php echo utf8_encode('FÉRIAS') ?></th>
                                <th class="text-center"><?php echo utf8_encode('1/3 FÉRIAS') ?></th>
                                <th class="text-center"><?php echo utf8_encode('13º SALÁRIO') ?></th>
                                <th class="text-center">LEI 12.506</th>
                                <th class="text-center">FGTS 8%</th>
                                <th class="text-center">PIS 1%</th>
                                <th class="text-center">INSS 20%</th>
                                <th class="text-center">RAT <?= $_REQUEST['rat'] ?>%</th>
                                <th class="text-center">TERCEIROS <?= $_REQUEST['outros'] ?>%</th>
                                <th class="text-center">TOTAL R$</th>
                            </tr>-->
                        </thead>
                        <tbody class="text tx-sm"> 
                    <?php } ?>

                        <?php
                        if (!empty($array_ferias[$row['id_clt']])) {
                            $array_clt = array_merge($row, $array_ferias[$row['id_clt']]);
                        } else {
                            $array_clt = $row;
                        }
                        $lei_12506 = 0; // a cada 12 meses e 1 dia acrescentar 3 dias em (R$ valor,00) 
                        $projeto = $row['id_projeto'];
                        $inss = 0.20;
                        $inicio_ano = date('Y') . '-01-01';
                        $final_ano = date('Y') . '-12-31';
                        $data_folha = $row['ano'] . '-' . $row['mes'] . '-30';
                        $data_admissao = $row['admissao'];
                        $data_atual = date('Y-m-d');
                        $arrayAdmissao = explode('-', $data_admissao);
                        $dia1 = $arrayAdmissao[2];
                        $mes1 = $arrayAdmissao[1];
                        $ano1 = $arrayAdmissao[0];

                        $arrayDataFolha = explode('-', $data_folha);
                        $dia2 = $arrayDataFolha[2];
                        $mes2 = $arrayDataFolha[1];
                        $ano2 = $arrayDataFolha[0];

                        $array_InicioAno = explode('-', $inicio_ano);
                        $dia3 = $array_InicioAno[2];
                        $mes3 = $array_InicioAno[1];
                        $ano3 = $array_InicioAno[0];

                        $arrayDataAtual = explode('-', $data_atual);
                        $dia4 = $arrayDataAtual[2];
                        $mes4 = $arrayDataAtual[1];
                        $ano4 = $arrayDataAtual[0];

                        $a1 = ($ano2 - $ano1) * 12;
                        $m1 = ($mes2 - $mes1) + 1;
                        $ma = ($m1 + $a1);

                        $a2 = ($ano2 - $ano3) * 12;
                        $m2 = ($mes2 - $mes3) + 1;
                        $mb = ($m2 + $a2);

                        $a3 = ($ano4 - $ano1) * 12;
                        $m3 = ($mes4 - $mes1) + 1;
                        $mc = ($m3 + $a3);

                        // < CALCULO DECIMO TERCEIRO ...  
                        if ($data_admissao < $inicio_ano) {
                            $avos = $mb;
                            $decimo = round(($row['salario'] * $avos / 12), 2);
                            $decimo_fgts = round($decimo * 0.08, 2);
                            $decimo_pis = round($decimo * 0.01, 2);
                            $decimo_inss = round($decimo * $inss, 2);
                            $decimo_outros = round($decimo * ($percent_outras_entidades / 100), 2);
                        }
                        if ($data_admissao >= $inicio_ano) {
                            $avos = $ma;
                            $decimo = round(($row['salario'] * $avos / 12), 2);
                            $decimo_fgts = round($decimo * 0.08, 2);
                            $decimo_pis = round($decimo * 0.01, 2);
                            $decimo_inss = round($decimo * $inss, 2);
                            $decimo_outros = round($decimo * ($percent_outras_entidades / 100), 2);
                        }

                        // CALCULO LEI 12506
                        $data1 = new DateTime($row['admissao']);
                        $data2 = new DateTime($row['ano'] . '-' . $row['mes'] . '-30');
                        $time_dif = $data1->diff($data2);
                        $dif = $time_dif->format('%y');
                        $dif_mes = $time_dif->format('%m');
                        $dif_dia = $time_dif->format('%d');
                        if ($data1->format('m') == $row['mes'] && $dif > 0) {
                            $lei_12506_dia[$row['id_clt']] = $dif * 3;
                            $lei_12506_valor[$row['id_clt']] = round(($row['salario'] / 30) * 3, 2);
                            $total[12] += $lei_12506_valor[$row['id_clt']];
                            $somarLei12506 = $lei_12506_valor[$row['id_clt']];
                        } else {
                            $lei_12506_valor[$row['id_clt']] = 0;
                            $lei_12506_dia[$row['id_clt']] = 0;
                            $somarLei12506 = $lei_12506_valor[$row['id_clt']] = 0;
                        }
                        // aviso
    //                    if ($dif_mes <= 11 && $dif == 0) {
    //                        $rescisao_parcela[$row['id_clt']] = $dif_mes + 1;
    //                        $rescisao = round($row['salario'] / 12, 2);
    //                        $total[3] += $rescisao;
    //                    } else {
    //                        $rescisao_parcela[$row['id_clt']] = 12;
    //                        $rescisao = 0;
    //                    }
                        // aviso calculo refeito (conforme orientação da Michelle RH)
                        if ($dif_mes == 3 && $dif == 0) {
                            $rescisao = round($row['salario'], 2);
                            $total[3] += $rescisao;
                            $rescisao_str = number_format($rescisao, 2, ',', '.');
                        } elseif ($dif_mes <= 2 && $dif == 0 ){
                            $rescisao = 0.00;
                            $rescisao_str =  utf8_encode("<h6><small>EXPERIÊNCIA</small></h6>");
                        }else {
                            $rescisao = 0.00;
                            $rescisao_str =  utf8_encode("<h6><small>PROVISIONADO</small></h6>");
                        }

                        $total[0] += round($row['salario'], 2);
                        $salario_fgts = round($row['salario'] * .08, 2);
                        $total[1] += round($salario_fgts, 2);
                        $salario_inss = round($row['salario'] * $inss, 2);
                        $total[2] += round($salario_inss, 2);
                        $fgts_rescisao = round($rescisao * .08, 2);
                        $total[4] += round($fgts_rescisao, 2);
                        $ferias = round($row['salario'] / 12, 2);
                        $total[6] += round($ferias, 2);
                        $um_terco = round(($ferias / 3), 2);
                        $total[7] += round($um_terco, 2);
                        $decimo_terceiro = round($row['salario'] / 12, 2);
                        $total[8] += round($decimo_terceiro, 2);
                        $dterceiro_fgts = round($decimo_terceiro * 0.08, 2);
                        $total[9] += $dterceiro_fgts;
                        $dterceiro_inss = round($decimo_terceiro * $inss, 2);
                        $total[10] += $dterceiro_inss;
                        $dterceiro_pis = round($decimo_terceiro * $_REQUEST['pis'] / 100, 2);
                        $total[11] += $dterceiro_pis;
    //                      $lei12506_valor = round($lei_12506_valor[$row['id_clt']], 2);
    //                      $total[12] += $lei12506_valor;
    //                      $lei12506_dia = $lei_12506_dia[$row['id_clt']];
                        $provisao = round($decimo_terceiro + $ferias + $um_terco, 2);
                        $total[13] += round($provisao, 2);
                        $fgts = round($prov_mes * 0.08, 2);
                        $total[14] += $fgts;
                        $fgtss = round($fgts_rescisao + $dterceiro_fgts, 2);
                        $total[16] += $fgts_1;
                        $fgts_8 = round(($somarLei12506 + $rescisao + $ferias + $um_terco + $decimo_terceiro) * 0.08, 2);
                        $total[17] += round($fgts_8, 2);
                        $multa_rescisao = round($fgts_8 * 0.50, 2);
                        $total[5] += $multa_rescisao;
                        $pis_1 = round(($somarLei12506 + $rescisao + $ferias + $um_terco + $decimo_terceiro) * $_REQUEST['pis'] / 100, 2);
                        $total[18] += $pis_1;
                        $inss_20 = round(($ferias + $um_terco + $decimo_terceiro) * 0.20, 2);
                        $total[19] += $inss_20;
                        $rat = round(($somarLei12506 + $rescisao + $ferias + $um_terco + $decimo_terceiro) * $_REQUEST['rat'] / 100, 2);
                        $total[22] += $rat;
                        $outros = round(($somarLei12506 + $rescisao + $ferias + $um_terco + $decimo_terceiro) * $_REQUEST['outros'] / 100, 2);
                        $total[20] += $outros;

                        $totalInd = round($rescisao + $multa_rescisao + $ferias + $um_terco + $decimo_terceiro + $fgts_8 + $lei_12506_valor[$row['id_clt']] + $pis_1 + $inss_20 + $rat + $outros, 2);
                        $total[99] += $totalInd;

                        if(!empty($row['periculosidade_30'])) {
                            $complemento = $row['periculosidade_30'];
                            $complementonome =  utf8_encode('<span style="font-size:0.8em">PERICULOSIDADE 30%</span>');
                            $complemento1 =  utf8_encode('PERICULOSIDADE 30%');
                        } elseif (!empty($row['insalubridade_20'])) {
                            $complemento = $row['insalubridade_20'];
                            $complementonome =  utf8_encode("<span style='font-size:0.8em'>INSALUBRIDADE 20%</span>");
                            $complemento1 =  utf8_encode('INSALUBRIDADE 20%');
                        } elseif (!empty($row['insalubridade_40'])) {
                            $complemento = $row['insalubridade_40'];
                            $complementonome =  utf8_encode("<span style='font-size:0.8em'>INSALUBRIDADE 40%</span>");
                            $complemento1 =  utf8_encode('INSALUBRIDADE 40%');

                        } else { } ?>                    

                        <tr id="<?= 'id_clt_' . $row['id_clt'] ?>" class="text text-sm">
                            <input type="hidden" name="provisao[<?= $row['id_clt'] ?>][rescisao_parcela]" value="<?= $rescisao_parcela[$row['id_clt']] ?>">
                            <input type="hidden" name="provisao[<?= $row['id_clt'] ?>][id_clt]" value="<?= $row['id_clt'] ?>">
                            <td colspan="8">
                                <?= '<h5><small>Colaborador</small></h5>'.$row['nome'] ?>
                            </td>
                            <td colspan="4">
                                <?= '<h5><small>Cargo / Função</small></h5>'.$row['funcao'] ?>
                            </td>
                            <td>
                                <?= '<h5><small>Admissão</small></h5>'.ConverteData($row['admissao'], 'd/m/Y') ?>
                            </td>
                        </tr>
                        <tr class="text text-sm">
                            <td class="text-right text-info">
                                <i style="cursor:pointer" title="(SALÁRIO) <?= number_format($row['salariobase'], 2, ',', '.').' + ('.$complemento1.') '.number_format($complemento, 2, ',', '.').' = '.number_format($row['salariobase'] + $complemento, 2, ',', '.')?> ">
                                    <?= '<h5><small>Salário</small></h5>'.number_format($row['salariobase'] + $complemento, 2, ',', '.') ?>
                                </i>
                                <input type="hidden" name="provisao[<?= $row['id_clt'] ?>][salario]" value="<?= $row['salario'] ?>">
                            </td>
                            <td class="text-right"><?= '<h5><small>Aviso</small></h5>'.$rescisao_str ?><input type="hidden" name="provisao[<?= $row['id_clt'] ?>][rescisao]" value="<?= $rescisao ?>"></td>
                            <td class="text-right"><?= '<h5><small>Férias</small></h5>'.number_format($ferias, 2, ',', '.') ?><input type="hidden" name="provisao[<?= $row['id_clt'] ?>][ferias]" value="<?= $ferias ?>"></td>
                            <td class="text-right"><?= '<h5><small>Férias 1/3</small></h5>'.number_format($um_terco, 2, ',', '.') ?><input type="hidden" name="provisao[<?= $row['id_clt'] ?>][um_terco]" value="<?= $um_terco ?>"></td>
                            <td class="text-right"><?= '<h5><small>13º Salário</small></h5>'.number_format($decimo_terceiro, 2, ',', '.') ?><input type="hidden" name="provisao[<?= $row['id_clt'] ?>][decimo_tereiro]" value="<?= $decimo_terceiro ?>"></td>
                            <td class="text-right"><?= '<h5><small>Lei 12.506</small></h5>'.number_format($lei_12506_valor[$row['id_clt']], 2, ',', '.') ?>
                                <input type="hidden" name="provisao[<?= $row['id_clt'] ?>][lei12506_valor]" value="<?= $lei_12506_valor[$row['id_clt']] ?>">
                                <input type="hidden" name="provisao[<?= $row['id_clt'] ?>][lei12506_dia]" value="<?= $lei_12506_dia[$row['id_clt']] ?>">
                            </td>
                            <td class="text-right text-info"><i style="cursor:pointer" title="AVISO + FÉRIAS + 1/3 FÉRIAS + 13º SALÁRIO + LEI 12.506 = <?= number_format($fgts_8, 2, ',', '.') ?>"><?= '<h5><small>FGTS 8%</small></h5>'.number_format($fgts_8, 2, ',', '.') ?></i><input type="hidden" name="provisao[<?= $row['id_clt'] ?>][fgts_8]" value="<?= $fgts_8 ?>"></td>
                            <td class="text-right"><?= '<h5><small>Multa 50%</small></h5>'.number_format($multa_rescisao, 2, ',', '.') ?><input type="hidden" name="provisao[<?= $row['id_clt'] ?>][multa_rescisao]" value="<?= $multa_rescisao ?>"></td>
                            <td class="text-right"><?= '<h5><small>pis 1%</small></h5>'.number_format($pis_1, 2, ',', '.') ?><input type="hidden" name="provisao[<?= $row['id_clt'] ?>][pis]" value="<?= $pis_1 ?>"></td>
                            <td class="text-right text-info">
                                <i style="cursor:pointer" title="FÉRIAS + 1/3 FÉRIAS + 13º SALÁRIO = <?= number_format($inss_20, 2, ',', '.') ?>">
                                    <?= '<h5><small>INSS 20%</small></h5>'.number_format($inss_20, 2, ',', '.') ?>
                                </i>
                                <input type="hidden" name="provisao[<?= $row['id_clt'] ?>][inss_20]" value="<?= $inss_20 ?>">
                            </td>
                                <input type="hidden" name="provisao[<?= $row['id_clt'] ?>][lei12506_valor]" value="<?= $lei_12506_valor[$row['id_clt']] ?>">
                                <input type="hidden" name="provisao[<?= $row['id_clt'] ?>][lei12506_dia]" value="<?= $lei_12506_dia[$row['id_clt']] ?>">

                            <td class="text-right"><?= '<h5><small>RAT '.$_REQUEST['rat'].'%</small></h5>'.number_format($rat, 2, ',', '.') ?><input type="hidden" name="provisao[<?= $row['id_clt'] ?>][rat_valor]" value="<?= $rat ?>"></td>
                            <td class="text-right"><?= '<h5><small>Outros '.$_REQUEST['outros'].'%</small></h5>'.number_format($outros, 2, ',', '.') ?><input type="hidden" name="provisao[<?= $row['id_clt'] ?>][outros]" value="<?= $outros ?>"></td>
                            <td class="text-right"><?= '<h5><small>Total R$</small></h5>'.number_format($totalInd, 2, ',', '.') ?></td>
                        </tr>

                <?php } ?>
                    </tbody>
                    <td colspan="13"></td>
                    <tfoot class="text-right">
                        <tr class="info">                        
                            <td></td>
                            <td width="8.340%"><h5><small class="text text-black text-right">AVISO</small></h5></td>
                            <td width="8.340%"><h5><small class="text text-black text-right">MULTA 50%</small></h5></td>
                            <td width="8.340%"><h5><small class="text text-black text-right">F&Eacute;RIAS</small></h5></td>
                            <td width="8.340%"><h5><small class="text text-black text-right">1/3 F&Eacute;RIAS</small></h5></td>
                            <td width="8.340%"><h5><small class="text text-black text-right">13&ordm; SAL&Aacute;RIO</small></h6></td>
                            <td width="8.340%"><h5><small class="text text-black text-right">LEI 12.506</small></h5></td>
                            <td width="8.340%"><h5><small class="text text-black text-right">FGTS 8%</small></h5></td>
                            <td width="8.340%"><h5><small class="text text-black text-right">PIS 1%</small></h5></td>
                            <td width="8.340%"><h5><small class="text text-black text-right">INSS 20%</small></h5></td>
                            <td width="8.340%"><h5><small class="text text-black text-right">RAT <?= $_REQUEST['rat'] ?> %</small></h5></td>
                            <td width="8.340%"><h6><small class="text text-black text-right">OUTROS <?= $_REQUEST['outros'] ?> %</small></h6></td>
                            <td width="8.340%"><h5><small class="text text-black text-right">TOTAL R$</small></h5></td>

                        </tr>
                        <tr class="text text-sm info text-info">
                        <td></td>
                        <td><?= number_format($total[3], 2, ',', '.') ?><input type="hidden" name="rescisao_tot" value="<?= $total[3] ?>"></td>
                        <td><?= number_format($total[5], 2, ',', '.') ?><input type="hidden" name="multa50_tot" value="<?= $total[5] ?>"></td>
                        <td><?= number_format($total[6], 2, ',', '.') ?><input type="hidden" name="ferias_tot" value="<?= $total[6] ?>"></td>
                        <td><?= number_format($total[7], 2, ',', '.') ?><input type="hidden" name="umterco_tot" value="<?= $total[7] ?>"></td>
                        <td><?= number_format($total[8], 2, ',', '.') ?><input type="hidden" name="dterceiro_tot" value="<?= $total[8] ?>"></td>
                        <td><?= number_format($total[12], 2, ',', '.') ?><input type="hidden" name="lei12506_tot" value="<?= $total[12] ?>"></td>
                        <td><?= number_format($total[17], 2, ',', '.') ?><input type="hidden" name="fgts8_tot" value="<?= $total[17] ?>"></td>
                        <td><?= number_format($total[18], 2, ',', '.') ?><input type="hidden" name="pis1_tot" value="<?= $total[18] ?>"></td>
                        <td><?= number_format($total[19], 2, ',', '.') ?><input type="hidden" name="inss20_tot" value="<?= $total[19] ?>"></td>
                        <td><?= number_format($total[22], 2, ',', '.') ?><input type="hidden" name="rat_tot" value="<?= $total[22] ?>"></td>
                        <td><?= number_format($total[20], 2, ',', '.') ?><input type="hidden" name="outros_tot" value="<?= $total[20] ?>"></td>
                        <td><?= number_format($total[99], 2, ',', '.') ?></td>
                    </tr>
                    <?php $total_provisao = round($total[3] + $total[5] + $total[6] + $total[7] + $total[8] + $total[12] + $total[17] + $total[18] + $total[19] + $total[20] + $total[22], 2) ?>
                </tfoot>
            </table>
        <?php } else { ?>
            <div class="alert alert-info">Nenhuma Participante nesta Folha</div>
        <?php }
        exit;
    }

    $nome_pagina = "Cadastrar Folha de Provisão";
    $dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
    $breadcrumb_config = array("nivel" => "../", "key_btn" => "38", "area" => "Gestão Contabil", "ativo" => $nome_pagina, "id_form" => "frmplanodeconta");
    $breadcrumb_pages = array("Folha de Provisão" => "folha_de_provisao.php");
?>

<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: <?= $nome_pagina ?></title>
        <link rel="shortcut icon" href="../favicon.png">
        <!-- Bootstrap -->
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-compras.css" rel="stylesheet" media="all">
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/ui-autocomplete-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/main.css" rel="stylesheet" media="all">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="all">
    </head>
    <body>

        <?php include("../template/navbar_default.php"); ?> 
        
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-header box-contabil-header hidden-print">
                        <h2><?php echo $icon['38'] ?> - Contabilidade <small>- <?= $nome_pagina ?></small></h2>
                    </div>
                    <?php if (isset($_GET['s'])) { ?>
                        <div class="alert alert-success text-bold">Provisão criada com sucesso!</div>
                    <?php } ?>
                    <form action="" method="post" id="form_provisao" class="form-horizontal">
                        <div class="panel panel-default">
                            <div class="panel panel-body div-1">
                                <div class="form-group">
                                    <label class="control-label col-lg-2 ">Projeto</label>
                                    <div class="col-sm-8">
                                        <?= montaSelect(getProjetos($usuario['id_regiao']), $projetoR, "id='id_projeto' name='id_projeto' class='form-control validate[required,custom[select]]'"); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <label class="control-label col-lg-2 text-sm">PIS</label>
                                        <div class="col-sm-4">
                                            <div class="checkbox">
                                                <label class="control-label">
                                                    <input type="checkbox" name="pis" value="1">Alíquota 1%
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <label class="control-label col-lg-2 text-sm">SAT / RAT</label>
                                        <div class="col-sm-2">
                                            <input type="text" class="money form-control text-right" name="rat">
                                            <!--<label class="radio-inline"><input type="radio" name="rat" value="1">Risco mínimo (1%)</label>
                                            <label class="radio-inline"><input type="radio" name="rat" value="2">Risco médio (2%)</label>
                                            <label class="radio-inline"><input type="radio" name="rat" value="3">Risco grave (3%)</label>-->
                                        </div>                                    
                                    </div>
                                </div>                            
                                <div class="form-group">
                                    <div class="col-sm-12">
                                        <label class="control-label col-lg-2 text-sm">OUTROS %</label>
                                        <div class="col-sm-2">
                                            <input type="text" name="outros" value="" maxlength="5" class="money form-control text-right">
                                        </div>
                                    </div>                            
                                </div>
                            </div>
                            <div class="panel-footer div-1 text-right ">
                                <button type="button" class="btn btn-primary btn-sm" id="filtrar"><i class="fa fa-filter"></i> Filtrar</button>
                            </div>
                            
                            <div class="panel-body div-2 hidden">
                                <legend>Selecionar Folha de Pagamento</legend>
                                <div class="form-group form-horizontal" id="lista-folhas"></div>
                            </div>
                            <div class="panel-footer div-2 text-right hidden">
                                <button type="button" class="btn btn-default btn-sm back" data-show="div-1" data-lista="lista-folhas"><i class="fa fa-reply-all"></i> Voltar</button>
                                <button type="button" class="btn btn-primary btn-sm" id="exibe">Próximo <i class="fa fa-angle-double-right"></i></button>
                            </div>
                            
                            <div class="panel-body border-t div-3 hidden" id="lista-clts"></div>
                            <div class="panel-footer div-3 text-right hidden">
                                <button style="width: 100px" type="button" class="btn btn-default btn-sm back" data-show="div-2" data-lista="lista-clts"><i class="fa fa-reply-all"></i> Voltar</button>
                                <button style="width: 100px" type="button" name="pdf" data-title="Folha de Provisão" data-id="tbRelatorio" id="pdf" value="Gerar PDF" class="btn btn-danger btn-sm"><i class="fa fa-file-pdf-o"></i> Gerar PDF</button>
                                <button style="width: 100px" type="submit" class="btn btn-primary btn-sm" id="salvar" name="method" value="salvar"><i class="fa fa-check-square-o" aria-hidden="true"></i> Ok</button>
                            </div>
                        </div>
                    </form>
                    <?php include_once '../template/footer.php'; ?>
                </div>
            </div>
        </div>
        <script type="text/javascript" src="wz_tooltip.js"></script>
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../js/jquery.maskMoney_3.0.2.js"></script>
        <script src="../js/jquery.form.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
        <script src="../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../resources/js/financeiro/saida.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../js/jquery.form.js" type="text/javascript"></script>
        <script src="../resources/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
        <script src="js/folha_de_provisao.js" type="text/javascript"></script>
    </body>
</html>
