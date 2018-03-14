<?php
/*
 * metodos chamados via ajax pelas férias
 */

header('Content-Type: text/html; charset=utf-8');

if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../classes/global.php');
include('../../classes/clt.php');
//include('../../classes/calculos.php');
//include("../../classes/CalculoFeriasClass.php");
include("../../classes/FeriasClass.php");
include('../../wfunction.php');

$objFerias = new Ferias();

// calcula datas das ferias
if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'calc_data') {

    $data_entrada = $_REQUEST['data_entrada'];
    $id_clt = $_REQUEST['id_clt'];
    $periodo_aquisitivo = explode('/', $_REQUEST['periodo_aquisitivo']);

    $array = $objFerias->calc_data($id_clt, $data_entrada, $periodo_aquisitivo);

    echo utf8_encode(json_encode($array));
    exit();
}

// cacula as ferias e retorna o resumo dos calculos, para o usuário ver antes de
// concluir o processo.
if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'calc_ferias') {

    $qr_clt = "SELECT a.id_clt,a.nome,a.id_projeto,a.id_unidade,id_regiao,
                date_format(a.data_entrada, '%d/%m/%Y') AS data_entrada2, date_format(a.data_saida, '%d/%m/%Y') AS data_saida2, 
                (SELECT nome FROM projeto WHERE id_projeto = a.id_projeto) AS nome_projeto, 
                (SELECT unidade FROM unidade WHERE id_unidade = a.id_unidade) AS nome_unidade,
                b.id_curso,b.nome AS nome_curso, b.salario
                FROM rh_clt AS a
                INNER JOIN curso AS b ON (a.id_curso = b.id_curso)
                WHERE a.id_clt = '$id_clt'";
    $result_clt = mysql_query($qr_clt);
    $row_clt = mysql_fetch_assoc($result_clt);

    $dados = array(
        'id_clt' => $_REQUEST['id_clt'],
        'id_projeto' => $_REQUEST['id_projeto'],
        'id_regiao' => $_REQUEST['id_regiao'],
        'direito_dias' => $_REQUEST['direito_dias'],
        'despreza_faltas' => $_REQUEST['despreza_faltas'],
        'faltas' => $_REQUEST['faltas'],
        'faltas_real' => $_REQUEST['faltas_real'],
        'update_movimentos_clt' => $_REQUEST['update_movimentos_clt'],
        'data_inicio' => $_REQUEST['data_ini'],
        'quantidade_dias' => $_REQUEST['dias'],
        'periodo_aquisitivo' => $_REQUEST['periodo_aquisitivo'],
        'periodo_abono' => $_REQUEST['periodo_abono'],
    );

    $arrFerias = $objFerias->calc_ferias($dados);

    if ($arrFerias['verifica_dobrado'] <= $arrFerias['data_inicio']) {
        ?>
        <div class="alert alert-danger" role="alert">
            <i class="fa fa-warning"></i> <strong>Férias em dobro</strong>.
        </div>
    <?php } ?>

    <table class="table">
        <thead>
            <tr>
                <th colspan="2" class="text-center" id="tb-nome-clt">Locação</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="width: 50%;" class="text-right text-bold">Projeto:</td>
                <td style="width: 50%;" id="tb-unidade"><?= "{$row_clt['id_projeto']} - {$row_clt['nome_projeto']}" ?></td>
            </tr>
            <tr>
                <td style="width: 50%;" class="text-right text-bold">Unidade:</td>
                <td style="width: 50%;" id="tb-unidade"><?= "{$row_clt['id_unidade']} - {$row_clt['nome_unidade']}" ?></td>
            </tr>
            <tr>
                <td class="text-right text-bold">Atividade:</td>
                <td id="tb-atividade"><?= "{$row_clt['id_curso']} - {$row_clt['nome_curso']}" ?></td>
            </tr>
            <tr>
                <td class="text-right text-bold">Salário Contratual:</td>
                <td>
                    R$ <span id="tb-salario-contratual"><?= number_format($row_clt['salario'], 2, ',', '.') ?></span>
                </td>
            </tr>
        </tbody>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th colspan="2" class="text-center">Resumo do Período de Férias</th>
            </tr>
        </thead>
        <tbody>

            <tr>
                <td style="width: 50%;" class="text-right text-bold">Período Aquisitivo:</td>
                <td style="width: 50%;" id="tb-aquisitivo"><?= $arrFerias['aquisitivo_iniT'] ?> &agrave; <?= $arrFerias['aquisitivo_fimT'] ?></td>
            </tr>

            <?php if (!empty($arrFerias['faltas'])) { ?>
                <tr>
                    <td class="text-right text-bold">Faltas no Per&iacute;odo:</td>
                    <td><?= $arrFerias['faltas'] ?> dias</td>
                </tr>
            <?php } ?>

            <tr>
                <td class="text-right text-bold">Período de Férias:</td>
                <td id="tb-periodo-ferias"><?= $arrFerias['data_inicioT'] ?> &agrave; <?= $arrFerias['data_fimT'] ?></td>
            </tr>
            <tr>
                <td class="text-right text-bold">Quantidade de Dias:</td>
                <td><?= $arrFerias['quantidade_dias'] ?> dias</td>
            </tr>
            
            <?php if (!empty($dias_abono_pecuniario)) { ?>
                <tr>
                    <td class="text-right text-bold">
                        Dias de Abono Pecuni&aacute;rio:
                    </td>
                    <td>
                        <?= $arrFerias['dias_abono_pecuniario'] ?> dias
                    </td>
                </tr>
            <?php } ?>
                
            <tr>
                <td class="text-right text-bold">Data de Retorno:</td>
                <td id="tb-retorno"><?= $arrFerias['data_retornoT'] ?></td>
            </tr>
        </tbody>
    </table>
    <table class="table">
        <thead>
            <tr>
                <th colspan="4" class="text-center">Resumo do Pagamento de Férias</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="width: 25%;" class="text-right text-bold">Salário:</td>
                <td style="width: 25%;">
                    R$ <?= $arrFerias['salarioT'] ?>
                </td>
                <td style="width: 25%;" class="text-right text-bold">Salário Variável:</td>
                <td style="width: 25%;">
                    R$ <?= $arrFerias['salario_variavelT'] ?>  <a href="#" id="confere_movimentos" class="pull-right">Ver <i class="fa fa-external-link-square"></i></a>
                </td>
            </tr>
            <tr>
                <td class="text-right text-bold">1/3 do Salário:</td>
                <td>
                    R$ <span id="tb-1-3-salario"></span>
                </td>
                <td class="text-right text-bold">Remuneração:</td>
                <td>
                    R$ <span id="tb-remuneracao"></span>
                </td>
            </tr>
            <tr class="tr-abono hidden">    
                <td class="text-right text-bold">
                    Abono Pecuni&aacute;rio:
                </td>
                <td>
                    R$ <span id="tb-pecuniario"></span>
                </td>
                <td class="text-right text-bold">
                    1/3 Abono Pecuni&aacute;rio:
                </td>
                <td>
                    R$ <span id="tb-1-3-pecuniario"></span>
                </td>
            </tr>
            <tr>
                <td class="text-right text-bold">INSS:</td>
                <td>
                    R$ <span id="tb-inss"></span>
                </td>
                <td class="text-right text-bold">IRRF:</td>
                <td>
                    R$ <span id="tb-irrf"></span>
                </td>
            </tr>
            <tr>
                <td class="text-right text-bold">Pensão Alimentícia:</td>
                <td>
                    R$ <span id="tb-pensao"></span>
                </td>
                <td class="text-right text-bold">Descontos:</td>
                <td>
                    R$ <span id="tb-desconto"></span>
                </td>
            </tr>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" class="text-right text-bold">Liquido a Receber:</td>
                <td colspan="2" class="text-bold">R$ <span id="tb-liquido"></span></td>
            </tr>
        </tfoot>
    </table>

    <input type="hidden" name="nome" value="<?= $row_clt['nome'] ?>">
    <input type="hidden" name="mes" value="">
    <input type="hidden" name="ano" value="">
    <input type="hidden" name="data_aquisitivo_ini" value="">
    <input type="hidden" name="data_aquisitivo_fim" value="">
    <input type="hidden" name="data_fim" value="">
    <input type="hidden" name="data_retorno" value="">
    <input type="hidden" name="salario" value="">
    <input type="hidden" name="salario_variavel" value="">
    <input type="hidden" name="remuneracao_base" value="">
    <input type="hidden" name="dias_ferias" value="">
    <input type="hidden" name="valor_dias_ferias" value="">
    <input type="hidden" name="valor_total_ferias" value="">
    <input type="hidden" name="umterco" value="">
    <input type="hidden" name="total_remuneracoes" value="">
    <input type="hidden" name="pensao_alimenticia" value="">
    <input type="hidden" name="inss" value="">
    <input type="hidden" name="inss_porcentagem" value="">
    <input type="hidden" name="ir" value="">
    <input type="hidden" name="fgts" value="">
    <input type="hidden" name="total_descontos" value="">
    <input type="hidden" name="total_liquido" value="">
    <input type="hidden" name="abono_pecuniario" value="">
    <input type="hidden" name="umterco_abono_pecuniario" value="">
    <input type="hidden" name="dias_abono_pecuniario" value="">
    <input type="hidden" name="faltas" value="">
    <input type="hidden" name="faltasano" value="">
    <input type="hidden" name="dias_mes" value="">
    <input type="hidden" name="dias_ferias1" value="">
    <input type="hidden" name="dias_ferias2" value="">
    <input type="hidden" name="valor_total_ferias1" value="">
    <input type="hidden" name="acrescimo_constitucional1" value="">
    <input type="hidden" name="total_remuneracoes1" value="">
    <input type="hidden" name="valor_total_ferias2" value="">
    <input type="hidden" name="acrescimo_constitucional2" value="">
    <input type="hidden" name="total_remuneracoes2" value="">
    <input type="hidden" name="ferias_dobradas" value="">
    <input type="hidden" name="base_inss" value="">
    <input type="hidden" name="base_irrf" value="">
    <input type="hidden" name="percentual_irrf" value="">
    <input type="hidden" name="valor_ddir" value="">
    <input type="hidden" name="qnt_dependente_irrf" value="">
    <input type="hidden" name="parcela_deducao_irrf" value="">
    <input type="hidden" name="status" value="1">
    <input type="hidden" name="update_movimentos_clt" value="">

    <?php
//    echo utf8_encode(json_encode($array));
    exit();
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] == 'calc_dobrado') {
    $data_ini = implode('-', array_reverse(explode('/', $_REQUEST['data_ini'])));
    $periodo_aquisitivo = explode('/', $_REQUEST['periodo_aquisitivo']);

    $periodo_concessivo = $objFerias->calcFerias->getPeriodoConcessivo($periodo_aquisitivo[1]);

//    print_r($periodo_concessivo);
    $array['status'] = (strtotime($data_ini) > strtotime($periodo_concessivo['fim'])) ? FALSE : TRUE;
    $array['periodo_consessivo'] = $periodo_concessivo;
    echo utf8_encode(json_encode($array));
}
?>

