<?php

/**
 * Rotina para processamento de provisao de gastos em lote 
 * 
 * @file      provisao_de_gastos.php
 * @license   F71
 * @link      http://www.f71lagos.com/intranet/relatorios/provisao_de_gastos.php
 * @copyright 2016 F71
 * @author    Jacques <jacques@f71.com.br>
 * @package   
 * @access    
 * 
 * @version: 3.0.01687 - 31/07/2015 - Jacques - Implementacao de tabela temporária para geracao de provisao de gastos com compatibilidade retroativa
 * @version: 3.0.01734 - 26/08/2015 - Jacques - Implementado exclusao lógica para registros no header_recisao_lote
 * @version: 3.0.01735 - 26/08/2015 - Jacques - Adicionado log de inclusao e exclusao das provisões de gastos
 * @version: 3.0.01815 - 28/08/2015 - Jacques - Correcao de bug na montagem da query confirmar_rescisao a partir da linha 274
 * @version: 3.0.01817 - 28/08/2015 - Jacques - Correcao nos totalizadores quando o fator for o empregado, agrupamento por vetor dos grupos e liberacao do botao de confirmacao de efetivacao de recisao.
 * @version: 3.0.01879 - 01/08/2015 - Jacques - Adicionado set de variável $clt e $id_rescisao_lote vindo de post com (int) e definido no update rh_recisao id_recisao_lote > 0 para tentar sanar registros de rh_recisao que estao sendo ativados aleatoriamente
 * @version: 3.0.01885 - 01/08/2015 - Jacques - Adicionado exportacao para excel via javascript ao invéz de montagem direto por relatório (Para acelerar processo para geracao do relatório pela Lagos)
 * @version: 3.0.02581 - 30/09/2015 - Jacques - Alterado o método de exportacao para o excel para ver se resolve o problema de exportacao de grandes listas de dados
 * @version: 3.0.03415 - 23/10/2015 - Jacques - Desativado o botao de "Provisao Trabalhista" e alterado o valor do botao "Visualizar Rescisao" para "Visualizar Previsao de Gastos". Acao solicitada pelo Rogério.
 * @version: 3.0.03801 - 04/11/2015 - Jacques - Ativacao do botao Provisao Trabalhista a pedido de Ramon. 
 * @version: 3.0.03806 - 05/11/2015 - Jacques - Alteracao do método de exportacao da provisao trabalhista para o mesmo procedimento do provisao de gastos que já usa código que permite exportacao com muitos dados
 * @version: 3.0.03810 - 05/11/2015 - Jacques - Alteracao do código de página UTF-8 para ISO-8859-1 a ser usado nas exportacões de planilhas
 * @version: 3.0.03821 - 05/11/2015 - Jacques - Retirado todas as acentuações do código pois não foi encontrado de forma rápida uma solução
 * @version: 3.0.03898 - 09/11/2015 - Jacques - Segundo solicitação de acerto do Milton a base para calculo de INSS e FGTS = 13o + Aviso + Lei Com INSS = 26.92, PIS = 1%, FGTS = 8%.
 * @version: 3.0.04030 - 11/11/2015 - Jacques - Adicionado a seleção da região na listagem das provisões de gastos
 * @version: 3.0.05974 - 26/01/2016 - Jacques - Adicionado filtro para situação de Clt no processamento de provisão de gastos
 * @version: 3.0.06013 - 27/01/2016 - Jacques - Adicionado opção de lançamento de movimento em lote a pedido de Jeferson
 * @version: 3.0.06051 - 27/01/2016 - Jacques - Adicionado opção de exclusão e atualização de lançamento de movimento em lote 
 * @version: 3.0.06129 - 27/01/2016 - Jacques - Alterado o filtro de Visualizar Participantes, segundo orientação de Junior o status 200 deverá ficar em atividade normal ou período de experiência de acordo com a situação.
 * @version: 3.0.06170 - 04/02/2016 - Jacques - Adicionado o campo de situação nas provisões geradas, reposicionamento da div relatório, e dos combos para melhor utilização e visualização
 * @version: 3.0.06502 - 16/02/2016 - Jacques - input desnecessário na geração da planilha de cálculo do provisão trabalhista pois o primeiro lote a ser processado é que vai definir o valor
 *                                              do campo de forma indefinida gerando inconsistência no relatório <!--<input type="hidden" name="id_rescisao_lote" value="<?= $_REQUEST['id_rescisao_lote'] ?>">--> 
 * @version: 3.0.06857 - 22/02/2016 - Jacques - Correção do bug no lançamento de movimento individual gerado pela criação do movimento em lote
 * @version: 3.0.10611 - 16/08/2016 - Jacques - Retificação de retorno JSON em gerar rescisão e alinhamento de ampulheta e scroll de tela
 * @version: 3.0.10969 - 30/08/2016 - Jacques - Alterado método de efetivação de rescisões via cliente com disponibilização de barra de progresso
 * @version: 3.0.00000 - 25/11/2016 - Jacques - Implementado processo em lote, com validações barra de progresso e automatização em geral dos seletores. Foi criado um arquivo provisao_de_gastos.php para teste
 * @version: 3.0.00000 - 29/11/2016 - Jacques - Erro no JSON do campo 'situacao' quando com caracteres extendidos no retorno da chamada de getHeaderRescisao
 * @version: 3.0.00000 - 30/11/2016 - Jacques - Adicionado o código 81 do tipo de dispensa no grupo de códigos de dispensa em contrato de experiência
 * @version: 3.0.00350 - 09/12/2016 - Jacques - Correção de bug ao incluir movimento pois o seletor está sendo sobrescrito na carga dos registro e adição do botão de fechar o modal
 * @version: 3.0.00351 - 09/12/2016 - Jacques - Concatenação dos valores das rubricas 8003 e 10008 de desconto de vale refeição no código rescisório 109
 * @version: 3.0.00000 - 12/12/2016 - Jacques - Tunando a soma da multa de GRRF pois a mesma estava sendo feita de uma maneira não otimizada
 * @version: 3.0.00000 - 12/12/2016 - Jacques - Adição do calculo da multa do GRRF os valores de saldo de salário + 13º salário + Aviso Prévio Indenizado + Lei 12.506
 * @version: 3.0.00000 - 12/12/2016 - Jacques - Adição de movimentos de crédito que tem incidência de FGTS e novo calculo das bases de fgts, inss e pis
 * @version: 3.0.00000 - 12/12/2016 - Jacques - Alteração da margem de erro de 1% para 3% conforme solicitação de Dani e Michele
 * @version: 3.0.00540 - 12/12/2016 - Jacques - Adição do campo RAT para definição manual da taxa
 * @version: 3.0.00554 - 12/12/2016 - Jacques - Fix no retorno da criação do lote no retorno do campo criado_por quando do uso de caracter especial
 * @version: 3.0.00555 - 12/12/2016 - Jacques - Fix do valor da taxa RAT que não estava somando aos 20% de INSS da empresa
 * @version: 3.0.00611 - 16/12/2016 - Jacques - Correções do provisao_de_gastos.php apontadas pelo Jeferson executadas
 * 
 * 
 * 
 * @author Nao definido 
 * 
 * Obs.: 17/11/2015 - Resumo da conversa com Daniela, Shirley e Miltom
 *                    Totalizador geral estava dando valores dobrados (Provavelmente já corrigido na revisão )
 *                    Totalização INSS e IRRF do funcionário não está sendo computado no total geral.
 *                    Não pode ser apagado as previsões geradas para que o Miltom possa obter essas informações com defasagem de meses.
 *                    IRRF na provisão de gastos na linha do empregado parece não estar correto
 *                    Ao alterar o combox não se consegue listar os registros 
 *                    Existem caracteres estendidos saindo desconfigurados na exportação para o excel
 * 
 */


if (!empty($_REQUEST['data_xls'])) { 
    
    $dados = $_REQUEST['data_xls'];
    
    ob_end_clean();
    header("Content-Encoding: iso-8859-1");
    header("Pragma: private");
    header("Cache-control: private, must-revalidate");
    header("Expires: 0");    
    header("Content-type: application/vnd.ms-excel");
//    header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
//    header("Content-type: application/xls");
    header("Content-Disposition: attachment; filename=provisao-de-gastos.xls");
    
 
    echo "\xEF\xBB\xBF";    
    echo "<html xmlns:o='urn:schemas-microsoft-com:office:office' xmlns:x='urn:schemas-microsoft-com:office:excel/' xmlns='http://www.w3.org/TR/REC-html40'>";
    echo "  <head>";
    echo "  <title>RELATÓRIO DE PROVISÃO DE GASTOS</title>";
    echo "      <!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->";
    echo "  </head>";
    echo "  <body>";
    echo "      $dados";
    echo "  </body>";
    echo "</html>";
    exit;
    
}

//if (empty($_COOKIE['logado'])) {
//    print "<script>location.href = '../login.php?entre=true';</script>";
//    exit;
//}

    
if (isset($_REQUEST['exportar_xls'])) {
    
    include_once 'provisao_de_gastos_xls_generator.php';
    
} 

include "../conn.php";
include "../classes/funcionario.php";
include '../classes_permissoes/regioes.class.php';
include "../wfunction.php";
include "../classes_permissoes/acoes.class.php";
include "../classes/FolhaClass.php";
include "../classes/calculos.php";
include ('../classes/LogClass.php');

function footer($tipo,&$total,$index,$label,$default){
    
    switch ($tipo) {
        case 'provisao_trabalhista':
            
                    ?>
                        <tr class="footer">
                            <td align="right" colspan="3"><?php echo $label;?></td>
                            <td align="right"><?php echo "R$ " . number_format($total['valor_aviso'][$index], 2, ",", "."); ?></td>
                            <td align="right" ><?php echo "R$ " . number_format($total['dt_salario'][$index], 2, ",", "."); ?></td>
                            <td align="right" ><?php echo "R$ " . number_format($total['terceiro_exercicio'][$index], 2, ",", "."); ?></td>
                            <td align="right" ><?php echo "R$ " . number_format($total['terceiro_ss'][$index], 2, ",", "."); ?></td>
                            <td align="right" ><?php echo "R$ " . number_format($total['ferias_pr'][$index], 2, ",", "."); ?></td>    
                            <td align="right" ><?php echo "R$ " . number_format($total['umterco_fp'][$index], 2, ",", "."); ?></td>
                            <td align="right" ><?php echo "R$ " . number_format($total['ferias_vencidas'][$index], 2, ",", "."); ?></td>    
                            <td align="right" ><?php echo "R$ " . number_format($total['umterco_fv'][$index], 2, ",", "."); ?></td>
                            <td align="right" ><?php echo "R$ " . number_format($total['terco_constitucional'][$index], 2, ",", "."); ?></td>    


                            <td align="right" ><?php echo "R$ " . number_format($total['ferias_aviso_indenizado'][$index], 2, ",", "."); ?></td>
                            <td align="right" ><?php echo "R$ " . number_format($total['fv_dobro']['linha'], 2, ",", "."); ?></td>
                            <td align="right" ><?php echo "R$ " . number_format($total['um_terco_ferias_dobro'][$index], 2, ",", "."); ?></td>
                            <td align="right" ><?php echo "R$ " . number_format($total['umterco_ferias_aviso_indenizado'][$index], 2, ",", "."); ?></td>
                            <td align="right" ><?php echo "R$ " . number_format($total['lei_12_506'][$index], 2, ",", "."); ?></td>
                            <td align="right" ><?php echo "R$ " . number_format($total['aviso_indenizado'][$index], 2, ",", "."); ?></td> 
                            <!-- TOTAL DE DEDUCÃO -->

                <!--        <td align="right" ><?php echo "R$ " . number_format($total_adiantamento_13_salarial, 2, ",", "."); ?></td>
                            <td align="right" ><?php echo "R$ " . number_format($total_aviso_indenizado_debito, 2, ",", "."); ?></td>
                            <td align="right" ><?php echo "R$ " . number_format($total_inss_dt, 2, ",", "."); ?></td>
                            <td align="right" ><?php echo "R$ " . number_format($total_ir_dt, 2, ",", "."); ?></td>
                            <td align="right" ><?php echo "R$ " . number_format($total_adiantamento_13, 2, ",", "."); ?></td>
                            <td align="right" ><?php echo "R$ " . number_format($total_ir_ferias, 2, ",", "."); ?></td>-->


                            <!-- DETALHES IMPORTANTES -->
                            <!-- BASES -->    

                            <td align="right" style="background: #fff; border: 0px;"></td>                       
                            <td align="right"><?php echo "R$ " . number_format($total['pis'][$index], 2, ",", "."); ?></td>                       
                            <td align="right"><?php echo "R$ " . number_format($total['fgts_multa'][$index], 2, ",", "."); ?></td>                       
                            <td align="right"><?php echo "R$ " . number_format($total['inss_empresa'][$index], 2, ",", "."); ?></td> 
                            <td align="right"><?php echo "R$ " . number_format($total['inss_terceiro'][$index], 2, ",", "."); ?></td> 
                            <td align="right"><?php echo "R$ " . number_format($total['fgts_recolher'][$index], 2, ",", "."); ?></td> 

                            <?php
                            if(isset($default)) {
                                /*
                                 * Totalizadores Empregado
                                 */
                                $total['valor_aviso'][$index] = $default;
                                $total['dt_salario'][$index] = $default;
                                $total['terceiro_exercicio'][$index] = $default;
                                $total['terceiro_ss'][$index] = $default;
                                $total['ferias_pr'][$index] = $default;
                                $total['umterco_fp'][$index] = $default;
                                $total['ferias_vencidas'][$index] = $default;
                                $total['ferias_aviso_indenizado'][$index] = $default;
                                $total['fv_dobro']['linha'] = 0;
                                $total['um_terco_ferias_dobro'][$index] = $default;
                                $total['umterco_ferias_aviso_indenizado'][$index] = $default;
                                $total['lei_12_506'][$index] = $default;
                                $total['aviso_indenizado'][$index] = $default;

                                /*
                                 * Totalizadores Empresa
                                 */
                                $total['pis'][$index] = $default;               
                                $total['fgts_multa'][$index] = $default;
                                $total['inss_empresa'][$index] = $default;
                                $total['inss_rat'][$index] = $default;
                                $total['inss_terceiro'][$index] = $default;
                                $total['fgts_recolher'][$index] = $default;

                                /* 
                                 * Totalizadores Gerais 
                                 */
                                $total['decimo_a_pagar'][$index] = $default;
                                $total['ferias_a_pagar'][$index] = $default;  
                                $total['terco_constitucional'][$index] = $default;  
                                
                            }
                            ?>
                        </tr>
                        <tr>
                            <td colspan="37" style="border: 0px;"></td>
                        </tr>

                    <?php

            break;
        case 'previsao_gastos':
            
            break;
    }
    
    return 1;
}


$id_regiao = $_REQUEST['regiao'];
$id_projeto = ($_REQUEST['projeto'] != (-1)) ? $_REQUEST['projeto'] : $_REQUEST['projeto_oculto'];
$projeto = montaQueryFirst("projeto", "nome", array('id_projeto' => $id_projeto));
$usuario = carregaUsuario();
$optRegiao = getRegioes();
$ACOES = new Acoes();
$folha = new Folha();
$calculos = new calculos();
$log = new Log();
$sql = "";

   
$debug = TRUE; // set TRUE para imprimir querys

$criterio = empty($id_regiao) ? "" :  " AND A.id_regiao={$id_regiao} ";


$movimento_validos = array("5912,6007,9000,8080,9997,5012,5011,7001,6004,7003,8006,50249,80017");
$movs = array();

$movimentos = "
               SELECT id_mov,LPAD(cod,6,'0') AS cod_fmt,descicao,categoria 
               FROM rh_movimentos 
               WHERE incidencia IN('RESCISAO','FOLHA') AND cod IN(" . implode(",", $movimento_validos). ")
               GROUP BY cod 
               ORDER BY descicao, categoria 
               ";    

$sql_movimento = mysql_query($movimentos) or die("Erro ao selecionar tipos de movimentos");

while ($rows_mov = mysql_fetch_assoc($sql_movimento)) {
    $movs[$rows_mov['id_mov']] = $rows_mov['cod_fmt'] . " - " . $rows_mov['descicao'] . " « " . $rows_mov['categoria'] . " » ";
}

$historico_gerado = "SELECT A.*, 
                        (CASE A.situacao 
                            WHEN 0 THEN 'NÃO ESP.'
                            WHEN 1 THEN 'EXPERIÊNCIA'
                            WHEN 2 THEN 'NORMAL'
                            WHEN 3 THEN 'EVENTO'
                        END) AS status_situacao,
                        DATE_FORMAT(A.criado_em,'%d/%m/%Y - %H:%i:%s') AS data_formatada, 
                        CONCAT(B.id_projeto,' - ',B.nome) AS nome_projeto, 
                        CONCAT(C.codigo,' - ',C.especifica) especifica, 
                        D.nome as criado_por_nome,
                        (SELECT COUNT(P.id_clt) FROM rh_recisao_provisao_de_gastos P WHERE P.id_recisao_lote=A.id_header) AS total, 
                        (SELECT COUNT(R.id_clt) FROM rh_recisao R WHERE R.id_recisao_lote=A.id_header) As efetivada, 
                        (SELECT COUNT(L.id_clt) FROM header_recisao_lote_clt L WHERE L.id_header_lote=A.id_header) As tot_lote
                    FROM header_recisao_lote AS A
                        LEFT JOIN projeto AS B ON(A.id_projeto = B.id_projeto)
                        LEFT JOIN rhstatus AS C ON(A.tipo_dispensa = C.codigo)
                        LEFT JOIN funcionario AS D ON(A.criado_por = D.id_funcionario)
                    WHERE A.status = 1 {$criterio}
                    ORDER BY id_header DESC";
                    
$sql_historico = mysql_query($historico_gerado) or die($historico_gerado);

//echo $historico_gerado;

$tipo_dispensa = "SELECT * FROM rhstatus WHERE tipo = 'recisao' ORDER BY CAST(codigo AS UNSIGNED) ASC";
$sql_dispensa = mysql_query($tipo_dispensa) or die("Erro ao selecionar os tipos de dispensas");
$dispensa = array();
while ($linha = mysql_fetch_assoc($sql_dispensa)) {
    $dispensa[$linha['codigo']] = $linha['codigo'] . " - " . $linha['especifica'];
}


/**
 * RECUPERA TODO FGTS PAGO PARA O CLT, SOMA E CALCULA 50%
 */
if (isset($_REQUEST['method'])) {
    if ($_REQUEST['method'] == "soma_fgts") {
        $dados = $folha->getFgtsRecolhido($_REQUEST['clt']);
        exit(json_encode($dados));
    }
}

/**
 * VISUALIZA AS RESCISOES
 */
if (isset($_REQUEST['method']) && $_REQUEST['method'] == "visualizarRescisao") {
    
    $return = array("status" => 0);
    
    $sql = "
        SELECT *
        FROM
            (
            SELECT 
                A.id_recisao,
                A.id_regiao,
                A.id_projeto,
                A.id_projeto AS projeto_rescisao, 
                D.id_curso,
                D.nome as funcao, 
                A.nome, 
                A.aviso, 
                C.especifica, 
                A.sal_base, 
                C.codigo, 
                B.id_clt
            FROM rh_recisao AS A
                LEFT JOIN rh_clt AS B ON(A.id_clt = B.id_clt)
                LEFT JOIN rhstatus AS C ON(B.`status` = C.codigo)
                LEFT JOIN curso AS D ON(B.id_curso = D.id_curso)
                
            WHERE 
                A.id_recisao_lote = {$_REQUEST['header']} 
                AND A.recisao_provisao_de_calculo = 1 
                AND A.status = 0
            GROUP BY A.id_clt 

            UNION ALL

            SELECT 
                A.id_recisao,
                A.id_regiao,
                A.id_projeto,
                A.id_projeto AS projeto_rescisao, 
                D.id_curso,
                D.nome as funcao, 
                A.nome, 
                A.aviso, 
                C.especifica, 
                A.sal_base, 
                C.codigo, 
                B.id_clt
            FROM rh_recisao_provisao_de_gastos AS A
                LEFT JOIN rh_clt AS B ON(A.id_clt = B.id_clt)
                LEFT JOIN rhstatus AS C ON(B.`status` = C.codigo)
                LEFT JOIN curso AS D ON(B.id_curso = D.id_curso)
            WHERE 
                A.id_recisao_lote = {$_REQUEST['header']} 
                AND A.recisao_provisao_de_calculo = 1 
                AND A.status = 0
                AND A.id_clt NOT IN 
			(
			SELECT id_clt 
			FROM rh_recisao 
			WHERE 
                            id_recisao_lote = {$_REQUEST['header']}
                            AND recisao_provisao_de_calculo = 1 
                            AND A.status = 0
			)
            GROUP BY A.id_clt 
            ) AS r
        ORDER BY 
            codigo,
            nome
        ";
                            
    $rs = mysql_query($sql) or die($sql);
    
    $dados = array();
    
    if ($rs) {
        
        while ($linha = mysql_fetch_assoc($rs)) {
            
            $id_regiao = $linha['id_regiao'];
            $id_projeto = $linha['id_projeto'];
            $id_header = $linha['id_recisao'];
            
            $dados[] = array("id_recisao" => $linha['id_recisao'],"id_regiao" => $linha['id_regiao'], "id" => $linha['id_clt'], "id_projeto" => $linha['projeto_rescisao'], "id_curso" => $linha['id_curso'], "funcao" => utf8_encode($linha['funcao']), "nome" => utf8_encode($linha['nome']), "status" => utf8_encode($linha['especifica']), "aviso" => $linha['aviso'], "status_clt" => utf8_encode($linha['especifica']), "sal_base" => $linha['sal_base']);
            
        }
        
        $return = array("status" => 1, "id_regiao" => $id_regiao, "id_projeto" => $id_projeto, "id_header" => $id_header, "dados" => $dados);
        
    }
    
    
    exit(json_encode($return));
    
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] == "teste") {
    
   $return = array("status" => 1);
   
   exit(json_encode($return));
    
}

/**
 * VERIFICA SE EXISTE RESCISÃO DE PROVISÃO COM AS CARACTERISTICAS ESCOLHIDA
 */
if (isset($_REQUEST['method']) && $_REQUEST['method'] == "verificaRescisao") {
    
    $return = array("status" => 0);
    $criteria = "";
    if (isset($_REQUEST['regiao'])) {
        $regiao = $_REQUEST['regiao'];
        $criteria .= " AND A.id_regiao = '{$regiao}'";
    }

    if (isset($_REQUEST['projeto'])) {
        $projeto = $_REQUEST['projeto'];
        $criteria .= " AND A.id_projeto = '{$projeto}'";
    }

    if (isset($_REQUEST['dispensa'])) {
        $motivo = $_REQUEST['dispensa'];
        $criteria .= " AND A.motivo = '{$motivo}'";
    }

    if (isset($_REQUEST['fator'])) {
        $fator = $_REQUEST['fator'];
        $criteria .= " AND A.fator = '{$fator}'";
    }
    
    if (isset($_REQUEST['dataDemi']) && $_REQUEST['dataDemi'] != "") {
        $dataDemi = date("Y-m-d", strtotime(str_replace("/", "-", $_REQUEST['dataDemi'])));
        $criteria .= " AND A.data_demi = '{$dataDemi}'";
    }

    if (isset($_REQUEST['dataAviso']) && $_REQUEST['dataAviso'] != "") {
        $dataAviso = date("Y-m-d", strtotime(str_replace("/", "-", $_REQUEST['dataAviso'])));
        $criteria .= " AND A.data_aviso = '{$dataAviso}'";
    }
    
    if (isset($_REQUEST['id_clt']) && $_REQUEST['id_clt'] != "") {
        $count = count($_REQUEST['id_clt']);
        $id_clt = implode(',',$_REQUEST['id_clt']);
        $criteria .= " AND A.id_clt IN ({$id_clt})";
    }
    
    $sQuery = "
        SELECT id_regiao,id_projeto,motivo,fator,data_demi,data_aviso,id_clt 
        FROM rh_recisao_provisao_de_gastos AS A 
        WHERE status {$criteria} 
        GROUP BY id_regiao,id_projeto,motivo,fator,data_demi,data_aviso,id_clt
        ";
    
    
    $rs = mysql_query($sQuery) or die("Erro ao selecionar dados de rescisao");
    
    $tot_provisao = mysql_num_rows($rs);
    
    if ($count == $tot_provisao) {
            
        $return = array("status" => 1);
            
    } else {
        
        $return = array("status" => 2);
        
    }
    
    
    /**
     * @jacques - 25/08/2015
     * Adicionado falor fixo do json para liberar a geracao de rescisao nas mesmas condicões - metodo verificaRescisao
     * Solicitacao do leonardo do RH Lagos 
     * 
     * @jacques - 19/08/2016
     * Foi alegado pelo Fábio que o sistema não estava validando, então foi retornado ao que era antes
     * 
     */
    //$return = array("status" => 2);

    exit(json_encode($return));
    
}

if ((isset($_REQUEST['confirmar_rescisao']))) {
    
    $formImprimir = "<form action='../rh/recisao/imprimir_rescisao_lote.php' method='post' id='imprimir_confirmadas' accept-charset='iso-8859-1'>";
    $clt = $_REQUEST['id_clt'];
    $id_rescisao_lote = (int)$_REQUEST['id_rescisao_lote'];
   
//    if($_COOKIE['logado'] == 275){
//        echo "<pre>";   
//        print_array($_REQUEST);
//        echo "</pre>";
//    }
    
//    for($i=0;$i < count($clt);$i++) {
    
    
    
    foreach ($clt as $key => $id_clt) {
        
        $sQuery =   
            "
            SELECT 
                r.id_clt,
                (SELECT status FROM rh_clt c WHERE c.id_clt = r.id_clt LIMIT 1) AS status,            
                r.id_recisao, 
                r.motivo, 
                r.data_aviso, 
                r.data_demi,
                r.saldo_salario,
                r.total_liquido 
            FROM rh_recisao r
            WHERE 
                r.id_clt = $id_clt
                AND r.id_recisao_lote = $id_rescisao_lote 
                AND r.status = 0 
                
            UNION ALL
            
            SELECT 
                r.id_clt,
                (SELECT status FROM rh_clt c WHERE c.id_clt = r.id_clt LIMIT 1) AS status,            
                r.id_recisao, 
                r.motivo, 
                r.data_aviso, 
                r.data_demi,
                r.saldo_salario,
                r.total_liquido 
            FROM rh_recisao_provisao_de_gastos r
            WHERE 
                r.id_clt = $id_clt 
                AND r.id_recisao_lote = $id_rescisao_lote
                AND r.status = 0 
                AND r.id_clt NOT IN 
			(
			SELECT id_clt 
			FROM rh_recisao 
			WHERE 
                            id_clt = $id_clt 
                            AND id_recisao_lote = $id_rescisao_lote
                            AND status = 0 
			)
            ";
        
        $rsClt = mysql_query($sQuery);
        
        $rowClt = mysql_fetch_assoc($rsClt);
        
//        if ($rowClt['status'] == 10 || $rowClt['status'] == 200) {
        
        if ($rowClt[status] > 0) {
            
            $formImprimir .= "<input type='hidden' name='id_clts[]' value='{$rowClt['id_clt']}'>";
            
            /*
             * Efetiva as rescisões de lote incluindo-as na tabela rh_recisao
             */
                    
            $sQuery = "
                INSERT INTO rh_recisao (
                        id_clt, nome, 
                        id_regiao, 
                        id_projeto, 
                        id_curso, 
                        data_adm, 
                        data_demi, 
                        data_proc, 
                        dias_saldo, 
                        um_ano, 
                        meses_ativo, 
                        motivo, 
                        fator, 
                        aviso, 
                        aviso_valor, 
                        avos_dt, 
                        avos_fp, 
                        dias_aviso, 
                        data_aviso, 
                        data_fim_aviso, 
                        fgts8, 
                        fgts40, 
                        fgts_anterior, 
                        fgts_cod, 
                        fgts_saque, 
                        sal_base, 
                        saldo_salario, 
                        inss_ss, 
                        ir_ss, 
                        terceiro_ss, 
                        previdencia_ss, 
                        dt_salario, 
                        inss_dt, 
                        ir_dt, 
                        previdencia_dt, 
                        ferias_vencidas, 
                        umterco_fv, 
                        ferias_pr, 
                        umterco_fp, 
                        inss_ferias, 
                        ir_ferias, 
                        sal_familia, 
                        to_sal_fami, 
                        ad_noturno, 
                        adiantamento, 
                        insalubridade, 
                        ajuda_custo, 
                        vale_refeicao, 
                        debito_vale_refeicao, 
                        a480, 
                        a479, 
                        a477, 
                        comissao, 
                        gratificacao, 
                        extra, 
                        outros, 
                        movimentos, 
                        valor_movimentos, 
                        total_rendimento, 
                        total_deducao, 
                        total_liquido, 
                        arredondamento_positivo, 
                        devolucao, 
                        faltas, 
                        valor_faltas, 
                        user, 
                        folha, 
                        status, 
                        adicional_noturno, 
                        dsr, 
                        desc_auxilio_distancia, 
                        um_terco_ferias_dobro, 
                        fv_dobro, 
                        aux_distancia, 
                        reembolso_vale_refeicao, 
                        periculosidade, 
                        desconto_vale_alimentacao, 
                        diferenca_salarial, 
                        ad_noturno_plantao, 
                        desconto, 
                        desc_vale_transporte, 
                        pensao_alimenticia_15, 
                        pensao_alimenticia_20, 
                        pensao_alimenticia_30, 
                        lei_12_506, 
                        ferias_aviso_indenizado, 
                        umterco_ferias_aviso_indenizado, 
                        adiantamento_13, 
                        fp_data_ini, 
                        fp_data_fim, 
                        fv_data_ini, 
                        fv_data_fim, 
                        qnt_dependente_salfamilia, 
                        base_inss_ss, 
                        percentual_inss_ss, 
                        base_irrf_ss, 
                        percentual_irrf_ss, 
                        parcela_deducao_irrf_ss, 
                        qnt_dependente_irrf_ss, 
                        valor_ddir_ss, 
                        base_fgts_ss, 
                        base_inss_13, 
                        percentual_inss_13, 
                        base_irrf_13, 
                        percentual_irrf_13, 
                        parcela_deducao_irrf_13, 
                        base_fgts_13, 
                        qnt_dependente_irrf_13, 
                        valor_ddir_13, 
                        desconto_inss, 
                        salario_outra_empresa, 
                        desconto_inss_outra_empresa, 
                        vinculo_id_rescisao, 
                        rescisao_complementar, 
                        recisao_provisao_de_calculo, 
                        id_recisao_lote, 
                        reintegracao
                        ) 
                    SELECT 
                        id_clt, 
                        nome, 
                        id_regiao, 
                        id_projeto, 
                        id_curso, 
                        data_adm, 
                        data_demi, 
                        data_proc, 
                        dias_saldo, 
                        um_ano, 
                        meses_ativo, 
                        motivo, 
                        fator, 
                        aviso, 
                        aviso_valor, 
                        avos_dt, 
                        avos_fp, 
                        dias_aviso, 
                        data_aviso, 
                        data_fim_aviso, 
                        fgts8, 
                        fgts40, 
                        fgts_anterior, 
                        fgts_cod, 
                        fgts_saque, 
                        sal_base, 
                        saldo_salario, 
                        inss_ss, 
                        ir_ss, 
                        terceiro_ss, 
                        previdencia_ss, 
                        dt_salario, 
                        inss_dt, 
                        ir_dt, 
                        previdencia_dt, 
                        ferias_vencidas, 
                        umterco_fv, 
                        ferias_pr, 
                        umterco_fp, 
                        inss_ferias, 
                        ir_ferias, 
                        sal_familia, 
                        to_sal_fami, 
                        ad_noturno, 
                        adiantamento, 
                        insalubridade, 
                        ajuda_custo, 
                        vale_refeicao, 
                        debito_vale_refeicao, 
                        a480, 
                        a479, 
                        a477, 
                        comissao, 
                        gratificacao, 
                        extra, 
                        outros, 
                        movimentos,
                        valor_movimentos, 
                        total_rendimento, 
                        total_deducao, 
                        total_liquido, 
                        arredondamento_positivo, 
                        devolucao, 
                        faltas, 
                        valor_faltas, 
                        user, 
                        folha, 
                        1 AS status, 
                        adicional_noturno, 
                        dsr, 
                        desc_auxilio_distancia, 
                        um_terco_ferias_dobro, 
                        fv_dobro, 
                        aux_distancia, 
                        reembolso_vale_refeicao, 
                        periculosidade, 
                        desconto_vale_alimentacao, 
                        diferenca_salarial, 
                        ad_noturno_plantao, 
                        desconto, 
                        desc_vale_transporte, 
                        pensao_alimenticia_15, 
                        pensao_alimenticia_20, 
                        pensao_alimenticia_30, 
                        lei_12_506, 
                        ferias_aviso_indenizado, 
                        umterco_ferias_aviso_indenizado, 
                        adiantamento_13, 
                        fp_data_ini, 
                        fp_data_fim, 
                        fv_data_ini, 
                        fv_data_fim, 
                        qnt_dependente_salfamilia, 
                        base_inss_ss, 
                        percentual_inss_ss, 
                        base_irrf_ss, 
                        percentual_irrf_ss, 
                        parcela_deducao_irrf_ss, 
                        qnt_dependente_irrf_ss, 
                        valor_ddir_ss, 
                        base_fgts_ss, 
                        base_inss_13, 
                        percentual_inss_13, 
                        base_irrf_13, 
                        percentual_irrf_13, 
                        parcela_deducao_irrf_13, 
                        base_fgts_13, 
                        qnt_dependente_irrf_13, 
                        valor_ddir_13, 
                        desconto_inss, 
                        salario_outra_empresa, 
                        desconto_inss_outra_empresa, 
                        vinculo_id_rescisao, 
                        rescisao_complementar, 
                        recisao_provisao_de_calculo, 
                        id_recisao_lote, 
                        reintegracao 
                    FROM rh_recisao_provisao_de_gastos 
                    WHERE 
                        id_recisao_lote = {$id_rescisao_lote} 
                        AND id_clt={$rowClt['id_clt']}"; 
                        
            mysql_query($sQuery);
            
            /*
             * Importa os movimentos da tabela morta para efetivacao
             */
            $sQuery = "
                INSERT rh_movimentos_rescisao (
                    id_rescisao, 
                    id_mov, 
                    id_clt, 
                    nome_movimento, 
                    tipo_qnt, 
                    qnt, 
                    qnt_horas, 
                    valor, 
                    status, 
                    incidencia
                    )
                        SELECT 
                            (SELECT id_recisao FROM rh_recisao WHERE id_recisao_lote={$id_rescisao_lote}) AS id_rescisao, 
                            tmmrl.id_movimento AS id_mov, 
                            tmmrl.id_clt, 
                            rhm.descicao AS nome_movimento,
                            NULL AS tipo_qnt, 
                            NULL AS qnt, 
                            NULL AS qnt_horas, 
                            tmmrl.valor, 
                            tmmrl.status, 
                            CONCAT(CASE WHEN rhm.incidencia_inss > 0 THEN '5020,' ELSE '' END,
                                   CASE rhm.incidencia_irrf WHEN 1 THEN '5021,' ELSE '' END,
                                   CASE rhm.incidencia_fgts WHEN 1 THEN '5023' ELSE '' END) AS indidencia 
                        FROM tabela_morta_movimentos_recisao_lote tmmrl INNER JOIN rh_movimentos rhm ON tmmrl.id_movimento=rhm.id_mov
                        WHERE tmmrl.id_rescisao={$rowClt['id_recisao']} AND tmmrl.id_clt={$rowClt['id_clt']}
                    ";    
                        
            mysql_query($sQuery); 
            
            $sQuery = "
                UPDATE rh_clt 
                SET status={$rowClt['motivo']}, 
                    data_aviso='{$rowClt['data_aviso']}', 
                    data_demi='{$rowClt['data_demi']}', 
                    data_saida='{$rowClt['data_demi']}' 
                WHERE id_clt = {$rowClt['id_clt']}";
                
            mysql_query($sQuery);
            
            /*
             * Atualiza o status da rescisao com o valor líquido final
             */
            
            $contribuicao_sindical = 0;
            
            $liquido_final = $rowClt['total_liquido'] - $contribuicao_sindical;
            
            $sQuery = "
                UPDATE rh_recisao_provisao_de_gastos
                SET status=1, 
                    data_proc=NOW(), 
                    total_liquido = {$liquido_final} 
                WHERE 
                    id_clt = {$rowClt['id_clt']}
                    AND id_recisao_lote = $id_rescisao_lote
                    AND id_recisao_lote > 0
                        
                ";
                
            mysql_query($sQuery);
            
            $sQuery = "
                UPDATE rh_recisao 
                SET status=1, 
                    data_proc=NOW(),
                    total_liquido = {$liquido_final} 
                WHERE 
                    id_clt = {$rowClt['id_clt']}
                    AND id_recisao_lote = $id_rescisao_lote
                    AND id_recisao_lote > 0
                        
                ";

            mysql_query($sQuery);
            
        }
        
        
    }
    
    $formImprimir .= "<input type='hidden' name='enviar' value='enviar'></form>";
    
    mysql_query("INSERT INTO log (id_user, id_regiao, tipo_user, grupo_user, local, horario, ip, acao)
    VALUES ('$usuario[id_funcionario]', '$usuario[id_regiao]', '$usuario[tipo_usuario]', '$usuario[grupo_usuario]', 'Confirmacao de Rescisao em Lote', NOW(), '$ip', 'Confirmacao de Rescisao em Lote')") or die("Erro Inesperado<br><br>" . mysql_error());
}

if (isset($_REQUEST['method']) && $_REQUEST['method'] == "carregaFuncoes") {
    
    if(!empty($_REQUEST['id_rescisao_lote'])) {
        
        $sQuery = " AND id_curso IN (SELECT id_curso 
                                     FROM rh_recisao_provisao_de_gastos
                                     WHERE id_recisao_lote={$_REQUEST['id_rescisao_lote']}
                                     GROUP BY id_curso)
                  ";

    }

    $funcoes = array();
    
    $query = "SELECT A.id_curso, A.nome
        FROM curso AS A
        WHERE A.id_regiao = '{$_REQUEST['regiao']}' AND A.campo3 = '{$_REQUEST['projeto']}'  AND A.`status` = 1 $sQuery
        ORDER BY A.nome";
        
    //echo $query;
    $sqlFuncoes = mysql_query($query) or die('Erro ao selecionar funcao');
    
    while ($linhas = mysql_fetch_assoc($sqlFuncoes)) {
        
        $funcoes[] = array('id_curso' => $linhas['id_curso'],'nome' => utf8_decode($linhas['nome']));
        
    }
    
    exit(json_encode($funcoes));
    
}

/**
 * VERIFICA OS PARTICIPANTES DO PROJETO SELECIONADO
 */
if (isset($_REQUEST['method']) && $_REQUEST['method'] == "verificaParticipantes") {

    $return = array("status" => 0);
    $criteria = "";
    
    if (isset($_REQUEST['regiao'])) {
        $regiao = $_REQUEST['regiao'];
        $criteria .= "A.id_regiao = '{$regiao}' ";
    }

    if (isset($_REQUEST['projeto'])) {
        $projeto = $_REQUEST['projeto'];
        $criteria .= " AND A.id_projeto = '{$projeto}'";
    }
    
    $situacaoSel = (isset($_REQUEST['situacao'])) ? $_REQUEST['situacao'] : 2;
    
    
    $verifica_participantes = "
                SELECT *
                FROM 
                    (SELECT 
                        A.id_clt, 
                        A.nome, 
                        B.nome as funcao, 
                        B.id_curso, 
                        IF(CURDATE() < DATE_ADD(data_entrada, INTERVAL 90 DAY),'Período de Experiência',C.especifica) status, 
                        D.sallimpo, 
                        A.id_regiao, 
                        A.id_projeto,
                        DATE_ADD(data_entrada, INTERVAL 90 DAY) AS data_contratacao, 
                        CASE WHEN A.status != 10 AND A.status != 200 THEN 3
                             WHEN data_entrada <= DATE_SUB(CURDATE(), INTERVAL 90 DAY) THEN 2 
                             WHEN data_entrada > DATE_SUB(CURDATE(), INTERVAL 90 DAY) AND data_entrada <= CURDATE() THEN 1 
                        END AS situacao                         
                    FROM rh_clt AS A 
                        LEFT JOIN curso AS B ON(A.id_curso = B.id_curso)
                        LEFT JOIN rh_folha_proc AS D ON(D.id_clt = A.id_clt)
                        LEFT JOIN rhstatus AS C ON(A.`status` = C.codigo) 
                    WHERE {$criteria} AND (A.status < 60 || A.status = 200) 
                    GROUP BY A.id_clt ORDER BY A.nome) tmp
                WHERE situacao={$situacaoSel}    
                    ";
                
    $sql_verifica_participantes = mysql_query($verifica_participantes) or die("Erro ao selecionar participantes");
    $linhas_participantes = mysql_num_rows($sql_verifica_participantes);

    if ($linhas_participantes > 0) {
        
        while ($linha = mysql_fetch_assoc($sql_verifica_participantes)) {
            
            $dados[] = array("id" => $linha['id_clt'], "nome" => utf8_encode($linha['nome']), "funcao" => utf8_encode($linha['funcao']), "id_curso" => $linha['id_curso'], "id_projeto" => $linha['id_projeto'], "id_regiao" => $linha['id_regiao'], "status" => utf8_encode($linha['status']), "sal_base" => $linha['sallimpo'], "data_contratacao" => $linha['data_contratacao']);
            
        }
        
        $return = array("status" => 1, "id_projeto" => $_REQUEST['projeto'], "id_regiao" => $_REQUEST['regiao'], "dados" => $dados);
    }

    exit(json_encode($return));
    
}

/**
 * Lista os participantes de um lote que ainda não foram processados
 */
if (isset($_REQUEST['method']) && $_REQUEST['method'] == "visualizarLotePendente") {

    $return = array("status" => 0);
    
    $id_header = $_REQUEST['id_header'];
    
    $verifica_participantes = "
                    SELECT 
                        A.id_clt, 
                        A.nome, 
                        B.nome as funcao, 
                        B.id_curso, 
                        D.especifica AS status, 
                        C.sallimpo, 
                        A.id_regiao, 
                        A.id_projeto,
                        DATE_ADD(data_entrada, INTERVAL 90 DAY) AS data_contratacao, 
                        CASE WHEN A.status != 10 AND A.status != 200 THEN 3
                             WHEN data_entrada <= DATE_SUB(CURDATE(), INTERVAL 90 DAY) THEN 2 
                             WHEN data_entrada > DATE_SUB(CURDATE(), INTERVAL 90 DAY) AND data_entrada <= CURDATE() THEN 1 
                        END AS situacao                         
                    FROM rh_clt AS A 
                        LEFT JOIN curso AS B ON(A.id_curso = B.id_curso)
                        LEFT JOIN rh_folha_proc AS C ON(C.id_clt = A.id_clt)
                        LEFT JOIN rhstatus AS D ON(D.codigo = A.status) 
                        LEFT JOIN header_recisao_lote_clt E ON E.id_clt=A.id_clt
                    WHERE E.id_header_lote = {$id_header} AND A.id_clt NOT IN (SELECT g.id_clt FROM rh_recisao_provisao_de_gastos g WHERE g.id_recisao_lote={$id_header})
                    GROUP BY A.id_clt 
                    ORDER BY A.nome
                    ";
                
    $sql_verifica_participantes = mysql_query($verifica_participantes) or die("Erro ao selecionar participantes");
    $linhas_participantes = mysql_num_rows($sql_verifica_participantes);

    if ($linhas_participantes > 0) {
        
        while ($linha = mysql_fetch_assoc($sql_verifica_participantes)) {
            
            $dados[] = array("id" => $linha['id_clt'], "nome" => utf8_encode($linha['nome']), "funcao" => utf8_encode($linha['funcao']), "id_curso" => $linha['id_curso'], "id_projeto" => $linha['id_projeto'], "id_regiao" => $linha['id_regiao'], "status" => utf8_encode($linha['status']), "sal_base" => $linha['sallimpo']);
            
        }
        
        $return = array("status" => $linhas_participantes, "id_projeto" => $_REQUEST['projeto'], "id_regiao" => $_REQUEST['regiao'], "dados" => $dados);
    }

    exit(json_encode($return));
    
}


/**
 * DESPROCESSAR RESCISÃO
 */
if (isset($_REQUEST['method']) && $_REQUEST['method'] == "desprocessarRecisao") {
    
    $header = $_REQUEST['header'];
    
    
    /*
     * Verifica se exitem rescisões efetivadas
     */
    $query_check = 
        "
            
        SELECT
           BIT_OR(r.status) AS status
        FROM 
           (    
           SELECT 
              BIT_OR(status) AS status
           FROM rh_recisao
           WHERE 
               id_recisao_lote = {$header}

           UNION ALL

           SELECT 
              BIT_OR(status) AS status
           FROM rh_recisao_provisao_de_gastos
           WHERE 
               id_recisao_lote = {$header}
               AND id_clt NOT IN 
                       (
                       SELECT id_clt 
                       FROM rh_recisao 
                       WHERE id_recisao = {$header}
                       )
           ) r           
   
        ";   
            
    $rsCheck = mysql_query($query_check) or die("Erro ao verificar remocao de rescisao");  
    
    $linhaCheck = mysql_fetch_assoc($rsCheck);
    
    if($linhaCheck['status']){

        $return = array("status" => 2); // Informa que existem rescisões efetivadas
        
    }
    else {
        
//        $query_header = 
//            "
//            DELETE FROM header_recisao_lote 
//            WHERE 
//                id_header = {$_REQUEST['header']}
//            ";
        
        
        /*
         * @jacques - 26/08/2015
         * Alterado para exclusao lógica
         */        
        $query_header = 
            "
            UPDATE header_recisao_lote 
            SET status=0
            WHERE id_header = {$header}
            ";
                

//        $query_linhas_provisao_de_gastos = 
//            "
//            DELETE FROM rh_recisao_provisao_de_gastos 
//            WHERE 
//                id_recisao_lote = {$_REQUEST['header']}
//                AND status = 0
//            ";
//
//        $query_linhas_rh_rescisao = 
//            "
//            DELETE FROM rh_recisao
//            WHERE 
//                id_recisao_lote = {$_REQUEST['header']} 
//                AND status = 0
//            ";
        
//        $ok_del_provisao = mysql_query($query_linhas_provisao_de_gastos) or die("Erro ao remover registros da tabela rh_recisao_provisao_de_gastos");
//
//        $ok_del_rescisao = mysql_query($query_linhas_rh_rescisao) or die("Erro ao remover registros da tabela rh_recisao");

        $ok_del_provisao = 1;
        $ok_del_rescisao = 1;
                
        if($ok_del_provisao && $ok_del_rescisao){
            
            $ok_del_header_provisao = mysql_query($query_header) or die("Erro ao remover rescisao");
            
        }
                
        
        if ($ok_del_provisao && $ok_del_rescisao && $ok_del_header_provisao) {
            
            $return = array("status" => 1);
            
        }
        else {
            
            $return = array("status" => 0);
            
        }
        
        $log->gravaLog('Provisao de Gastos',($ok_del_header_provisao ? '' : 'Tentativa sem sucesso de ') . "Exclusao lógica do lote da provisao de gastos - {$header}");
        
        
    }

    exit(json_encode($return));
    
}

/**
 * CADASTRA MOVIMENTOS PARA RESCISÃO, A TABELA QUE FICA ESSES MOVIMENTOS NÃO É A MESMA DOS MOVIMENTOS VÁLIDO PARA O CLT
 */
if (isset($_REQUEST['method']) && $_REQUEST['method'] == "cadastraMovimentos") {
    
    $return = array("status" => 0);
    $id_mov = trim($_REQUEST['movimento']);
    
    $query_cad_movimentos = "INSERT INTO tabela_morta_movimentos_recisao_lote (id_rescisao,id_clt,id_movimento,id_rescisao_lote,tipo,valor,status) VALUES ('{$_REQUEST['id_rescisao']}','{$_REQUEST['id_clt']}','{$id_mov}','{$_REQUEST['id_rescisao_lote']}',(SELECT categoria FROM rh_movimentos WHERE id_mov='{$id_mov}'),'{$_REQUEST['valor_movimento']}','1')";
    
    $sql_movimentos = mysql_query($query_cad_movimentos) or die("Erro ao cadastrar movimentos");
    
    $ult_cad = mysql_insert_id();
    
    if ($sql_movimentos) {

        $query_movimentos = "SELECT A.*, B.descicao FROM tabela_morta_movimentos_recisao_lote AS A
                             LEFT JOIN rh_movimentos AS B ON(A.id_movimento = B.id_mov) WHERE A.id_mov = '{$ult_cad}'";
                        
        $sql_movs = mysql_query($query_movimentos) or die("Erro ao selecionar ultimo movimento");

        $dados = array();
        
        while ($linhas_movs = mysql_fetch_assoc($sql_movs)) {
            
            $dados[] = array("id_mov" => $linhas_movs['id_mov'], "id_rescisao" => $linhas_movs['id_rescisao'], "id_rescisao_lote" => $linhas_movs['id_rescisao_lote'], "id_clt" => $linhas_movs['id_clt'], "id_movimento" => $linhas_movs['id_movimento'], "tipo" => utf8_encode($linhas_movs['tipo']), "nome_movimento" => utf8_encode($linhas_movs['descicao']), "valor" => $linhas_movs['valor']);
            
        }
        
        $return = array("status" => 1, "dados" => $dados);
    }

    exit(json_encode($return));
}

/**
 * CADASTRA MOVIMENTOS PARA RESCISÃO EM LOTE, A TABELA QUE FICA ESSES MOVIMENTOS NÃO É A MESMA DOS MOVIMENTOS VÁLIDO PARA O CLT
 */
if (isset($_REQUEST['method']) && $_REQUEST['method'] == "cadastraMovimentosLote") {
    
    $return = array("status" => 0);
    
    $tipo_mov = "";

    $query_cad_movimentos = "INSERT INTO tabela_morta_movimentos_recisao_lote (id_rescisao,id_rescisao_lote,id_rescisao_lote_coletivo,id_clt,id_movimento,tipo,valor,status) VALUES ('{$_REQUEST['id_rescisao']}','{$_REQUEST['id_rescisao_lote']}','{$_REQUEST['id_rescisao_lote']}','{$_REQUEST['id_clt']}','{$_REQUEST['movimento']}',(SELECT categoria FROM rh_movimentos WHERE id_mov='{$_REQUEST['movimento']}'),'{$_REQUEST['valor_movimento']}','1')";
    
    $sql_movimentos = mysql_query($query_cad_movimentos) or die("Erro ao cadastrar movimentos");
    
    $ult_cad = mysql_insert_id();
    
    if ($sql_movimentos) {

        $q =   "
                SELECT 
                    A.id_rescisao_lote,
                    A.id_movimento,
                    A.tipo,
                    FORMAT(SUM(A.valor)/(SELECT COUNT(id_clt) FROM (SELECT id_clt FROM tabela_morta_movimentos_recisao_lote WHERE id_rescisao_lote = {$_REQUEST['id_rescisao_lote']} AND id_rescisao_lote_coletivo = {$_REQUEST['id_rescisao_lote']} GROUP BY id_clt) m),2) valor,
                    B.descicao
                FROM tabela_morta_movimentos_recisao_lote AS A LEFT JOIN rh_movimentos AS B ON(A.id_movimento = B.id_mov) 
                WHERE A.id_rescisao_lote = {$_REQUEST['id_rescisao_lote']} AND A.id_rescisao_lote_coletivo = {$_REQUEST['id_rescisao_lote']}
                GROUP BY A.id_rescisao_lote,A.id_movimento
                
               ";
                             
        $sql_movs = mysql_query($q) or die($q);

        $dados = array();
        
        while ($linhas_movs = mysql_fetch_assoc($sql_movs)) {
            
            $dados[] = array("id_rescisao_lote" => $linhas_movs['id_rescisao_lote'], "id_rescisao_lote" => $linhas_movs['id_rescisao_lote'], "id_movimento" => $linhas_movs['id_movimento'], "tipo" => utf8_encode($linhas_movs['tipo']), "nome_movimento" => utf8_encode($linhas_movs['descicao']), "valor" => $linhas_movs['valor']);
            
        }
        
        $return = array("status" => 1, "dados" => $dados);
    }

    exit(json_encode($return));
}


/**
 * ATUALIZA VALOR LANCADO PARA O MOVIMENTO
 */
if (isset($_REQUEST['method']) && $_REQUEST['method'] == "atualizaValorMovimento") {
    $return = array("status" => 0);
    $query_update_movimentos = "UPDATE tabela_morta_movimentos_recisao_lote SET valor = '{$_REQUEST['valor']}' WHERE id_mov = '{$_REQUEST['movimento']}'";
    $sql_movimentos = mysql_query($query_update_movimentos) or die("Erro ao atulizar valor do movimento");
    if ($sql_movimentos) {
        $return = array("status" => 1);
    }

    exit(json_encode($return));
}

/**
 * ATUALIZA VALOR LANCADO PARA O MOVIMENTO DE LOTE LANÇADO
 */
if (isset($_REQUEST['method']) && $_REQUEST['method'] == "atualizaValorMovimentoLote") {
    
    $return = array("status" => 0);
    
    $query_update_movimentos = "UPDATE tabela_morta_movimentos_recisao_lote SET valor = '{$_REQUEST['valor_new']}' WHERE id_rescisao_lote = '{$_REQUEST['id_rescisao_lote']}' AND id_movimento = '{$_REQUEST['id_movimento']}' AND FORMAT(valor, 2)=FORMAT({$_REQUEST['valor_old']}, 2)";
    
    $sql_movimentos = mysql_query($query_update_movimentos) or die("Erro ao atulizar valor do movimento");
    
    if ($sql_movimentos) {
        
        $return = array("status" => 1);
        
    }

    exit(json_encode($return));
}

/**
 * REMOVE MOVIMENTOS LANCADO PARA O MOVIMENTO
 */
if (isset($_REQUEST['method']) && $_REQUEST['method'] == "removerMovimento") {
    $return = array("status" => 0);
    $query_remove_mov = "DELETE FROM tabela_morta_movimentos_recisao_lote WHERE id_mov = '{$_REQUEST['movimento']}'";
    $sql_remove = mysql_query($query_remove_mov) or die("Erro ao remover movimentos");
    if ($sql_remove) {
        $return = array("status" => 1);
    }
    exit(json_encode($return));
}

/**
 * REMOVE MOVIMENTOS LANCADO PARA O MOVIMENTO
 */
if (isset($_REQUEST['method']) && $_REQUEST['method'] == "removerMovimentoLote") {
    
    $return = array("status" => 0);
    
    $query_remove_mov = "DELETE FROM tabela_morta_movimentos_recisao_lote WHERE id_rescisao_lote_coletivo = {$_REQUEST['id_rescisao_lote']} AND id_rescisao_lote = {$_REQUEST['id_rescisao_lote']} AND id_movimento = {$_REQUEST['id_movimento']}";
    
    $sql_remove = mysql_query($query_remove_mov) or die("Erro ao remover movimentos em lote");
    
    if ($sql_remove) {
        
        $return = array("status" => 1);
        
    }
    
    exit(json_encode($return));
}


/**
 * LISTA MOVIMENTOS JA CADASTRADO PARA O CLT 
 */
if (isset($_REQUEST['method']) && $_REQUEST['method'] == "carrega_movimentos") {
    
    $return = array("status" => 0);
    
    $query_movimentos = "SELECT 
                            A.*, 
                            LPAD(A.id_mov,6,'0') AS id_mov_fmt,
                            B.descicao
                        FROM tabela_morta_movimentos_recisao_lote AS A
                        LEFT JOIN rh_movimentos AS B ON(A.id_movimento = B.id_mov) 
                        WHERE A.id_rescisao = '{$_REQUEST['rescisao']}' 
                        GROUP BY A.id_mov";
                        
    $sql_movs = mysql_query($query_movimentos) or die("Erro ao selecionar movimentosxx");
    
    if ($sql_movs) {
        
        $dados = array();
        
        while ($linhas_movs = mysql_fetch_assoc($sql_movs)) {
            
            $dados[] = array("id_mov" => $linhas_movs['id_mov_fmt'], "id_rescisao" => $linhas_movs['id_rescisao'], "id_movimento" => $linhas_movs['id_movimento'], "tipo" => utf8_encode($linhas_movs['tipo']), "nome_movimento" => utf8_encode(trim($linhas_movs['descicao'])), "valor" => $linhas_movs['valor']);
            
        }
        
        $return = array("status" => 1, "dados" => $dados);
        
    }

    exit(json_encode($return));
}

/**
 * LISTA MOVIMENTOS JA CADASTRADO PARA O CLT 
 */
if (isset($_REQUEST['method']) && $_REQUEST['method'] == "carrega_movimentos_lote") {
    
    $return = array("status" => 0);
    
    $q =    "
            SELECT                             
                A.id_rescisao_lote,
                A.id_movimento,
                A.tipo,
                FORMAT(SUM(A.valor)/(SELECT COUNT(id_clt) FROM (SELECT id_clt FROM tabela_morta_movimentos_recisao_lote WHERE id_rescisao_lote = {$_REQUEST['id_rescisao_lote']} AND id_rescisao_lote_coletivo = {$_REQUEST['id_rescisao_lote']} GROUP BY id_clt) m),2) valor,
                B.descicao
            FROM tabela_morta_movimentos_recisao_lote AS A LEFT JOIN rh_movimentos AS B ON(A.id_movimento = B.id_mov) 
            WHERE A.id_rescisao_lote = {$_REQUEST['id_rescisao_lote']} AND A.id_rescisao_lote_coletivo = '{$_REQUEST['id_rescisao_lote']}'
            GROUP BY A.id_rescisao_lote,A.id_movimento
            ";
                        
                        
    $rs = mysql_query($q) or die($q);
    
    if(mysql_num_rows($rs) > 0) {
        
        $dados = array();
        
        while ($linhas_movs = mysql_fetch_assoc($rs)) {
            
            $dados[] = array("id_rescisao_lote" => $linhas_movs['id_rescisao_lote'], "id_movimento" => $linhas_movs['id_movimento'], "tipo" => utf8_encode($linhas_movs['tipo']), "nome_movimento" => utf8_encode($linhas_movs['descicao']), "valor" => $linhas_movs['valor']);
            
        }
        
        $return = array("status" => 1, "dados" => $dados);
        
    }

    exit(json_encode($return));
    
}

/**
 * ABRI O ARQUIVO DE RESCISÃO PARA GRAVAR NO BANCO, COM OS DEVIDOS CALCULOS
 */
if (isset($_REQUEST['method']) && $_REQUEST['method'] == "criarHeader") {
    
    $return = array("status" => 0);
    
    $query = "SELECT id_clt, nome FROM rh_clt WHERE id_projeto = '{$_REQUEST['projeto']}' AND id_clt IN(" . implode(",", $_REQUEST['id_clt']) . ") AND (status < 60 || status = 200)"; // id_clt = '53939' - (status < 60 || status = 200)
    $sql = mysql_query($query) or die("Erro ao selecionar participantes");
    
    $handle = fopen("log.txt", "a+");
    fwrite($handle, $query . "\r\n");
    fclose($handle);     
    
    $data_demi = (!empty($_REQUEST['dataDemi'])) ? date("Y-m-d", strtotime(str_replace("/", "-", $_REQUEST['dataDemi']))) : "0000-00-00";
    $data_aviso = (!empty($_REQUEST['dataAviso'])) ? date("Y-m-d", strtotime(str_replace("/", "-", $_REQUEST['dataAviso']))) : "0000-00-00";
    
    $query_header = "INSERT INTO header_recisao_lote (id_regiao,id_projeto,situacao,tipo_dispensa,fator,dias_de_saldo,data_demi,remuneracao_para_fins,quantidade_faltas,aviso_previo,dias_indenizados,data_aviso,devolucao_de_credito,criado_por,status) VALUES ('{$_REQUEST['regiao']}','{$_REQUEST['projeto']}','{$_REQUEST['situacao']}','{$_REQUEST['dispensa']}','{$_REQUEST['fator']}','{$_REQUEST['diasSaldo']}','{$data_demi}','{$_REQUEST['remuneracoesRescisorias']}','{$_REQUEST['quantFaltas']}','{$_REQUEST['aviso']}','{$_REQUEST['diasIndOuTrab']}','{$data_aviso}','{$_REQUEST['devolucaoDeCredito']}','{$_COOKIE['logado']}',1)";
    $sql_header = mysql_query($query_header) or die("erro ao cadastrar header");
    $id_header = mysql_insert_id();
    
    foreach ($_REQUEST['id_clt'] as $key => $value) {
        
        $query_header_lote = "INSERT INTO header_recisao_lote_clt (id_header_lote ,id_clt) VALUES ({$id_header},{$value})";
        mysql_query($query_header_lote) or die("erro ao cadastrar header de lote de clts");
        
    }
    
    $log->gravaLog('Provisao de Gastos',"Inclusao lógica do lote da provisao de gastos - {$id_header}");
    
    $return = array("status" => 1, "id_header" => $id_header);
    
    exit(json_encode($return));
    
}

/**
 * Cria um header de lote
 */
if (isset($_REQUEST['method']) && $_REQUEST['method'] == "criarLote") {
    
    //print_array($_REQUEST);
    
    $return = array("status" => 0);
    
    $handle = fopen("log.txt", "a+");
    fwrite($handle, $query . "\r\n");
    fclose($handle);     
    
    $taxaRAT = (float)str_replace(',','.',$_REQUEST['taxaRAT'])/100;
    
    $query_header = "INSERT INTO header_recisao_lote (id_regiao,id_projeto,situacao,tipo_dispensa,fator,dias_de_saldo,data_demi,remuneracao_para_fins,quantidade_faltas,aviso_previo,dias_indenizados,data_aviso,devolucao_de_credito,criado_por,status,taxaRAT) VALUES ('{$_REQUEST['regiao']}','{$_REQUEST['projeto']}','{$_REQUEST['situacao']}','{$_REQUEST['dispensa']}','{$_REQUEST['fator']}','{$_REQUEST['diasSaldo']}','{$_REQUEST['data_demi']}','{$_REQUEST['remuneracoesRescisorias']}','{$_REQUEST['quantFaltas']}','{$_REQUEST['aviso']}','{$_REQUEST['diasIndOuTrab']}','{$_REQUEST['data_aviso']}','{$_REQUEST['devolucaoDeCredito']}','{$_COOKIE['logado']}',1,{$taxaRAT})";
    
    $sql_header = mysql_query($query_header) or die("erro ao cadastrar header");
    $id_header = mysql_insert_id();
    
    $query = "
            SELECT 
                id_regiao,
                id_projeto,
                situacao,
                tipo_dispensa,
                fator,
                dias_de_saldo,
                data_demi,
                remuneracao_para_fins,
                quantidade_faltas,
                aviso_previo,
                dias_indenizados,
                data_aviso,
                devolucao_de_credito,
                taxaRAT,
                (SELECT nome FROM funcionario WHERE id_funcionario=criado_por) criado_por,
                DATE_FORMAT(criado_em,'%d/%m/%Y - %H:%i:%s') criado_em,
                status,
                DATE_FORMAT(data_demi,'%d/%m/%Y') data_demi_fmt,
                DATE_FORMAT(data_aviso,'%d/%m/%Y') data_aviso_fmt 
            FROM header_recisao_lote 
            WHERE id_header={$id_header}";
                
    $rs = mysql_query($query);
    
    $row = mysql_fetch_assoc($rs);
    
    $log->gravaLog('Provisao de Gastos',"Inclusao lógica do lote da provisao de gastos - {$id_header}");
    
    $return = array(
                "status" => 1, 
                "id_header" => $id_header, 
                "id_projeto" => $row['id_projeto'], 
                "situacao" => utf8_encode($row['situacao']), 
                "tipo_dispensa" => $row['tipo_dispensa'], 
                "fator" => $row['fator'], 
                "dias_de_saldo" => $row['dias_de_saldo'], 
                "data_demi_fmt" => $row['data_demi_fmt'], 
                "data_aviso_fmt" => $row['data_aviso_fmt'], 
                "taxaRAT" => $row['taxaRAT'], 
                "criado_por" => utf8_encode($row['criado_por']), 
                "criado_em" => $row['criado_em'],
                "total" => 0
            );
    
    exit(json_encode($return));
    
}

/**
 * Lista todas as reteções de FGTS de um clt
 */
if (isset($_REQUEST['method']) && $_REQUEST['method'] == "lista_recolhimento_fgts") {
    
    $dados = array();

    $id_clt = $_REQUEST['id_clt'];
    
    $q = "
         SELECT 
            a.ano,
            a.mes,
            IFNULL(a.fgts,0) fgts,
            (SELECT COUNT(p.mes) FROM rh_folha_proc p WHERE p.id_clt=a.id_clt AND p.ano=a.ano) rowspan
         FROM rh_folha_proc a 
         WHERE a.id_clt = {$id_clt}
         ORDER BY a.ano DESC, a.mes DESC
        ";
    
    $rs = mysql_query($q) or die($q);
    
    while ($row = mysql_fetch_assoc($rs)) {

        $dados[] = array("ano" => $row['ano'], "mes" => $row['mes'], "a5031" => $row['a5031'], "fgts" => $row['fgts'], "valor" => $row['valor'], "rowspan" => $row['rowspan']);

    }
        
    $return = array("status" => 1, "dados" => $dados);
    
    exit(json_encode($return));
    
}


/**
 * Adiciona clt a um header de lote
 */
if (isset($_REQUEST['method']) && $_REQUEST['method'] == "adLote") {
    
    //print_array($_REQUEST);
    
    $id_header = $_REQUEST['id_header'];
    $id_clt = $_REQUEST['id_clt'];
    
    $return = array("status" => 0);
    
    $query = "INSERT INTO header_recisao_lote_clt (id_header_lote ,id_clt) VALUES ({$id_header},{$id_clt})";
    
    mysql_query($query) or die($query);
    
    $query = "SELECT COUNT(id_clt) total FROM header_recisao_lote_clt WHERE id_header_lote={$id_header}";
    
    $rs = mysql_query($query) or die($query);
    
    $row = mysql_fetch_assoc($rs);
    
    $return = array("status" => 1, "id_header" => $id_header, "total" => $row['total']);
    
    exit(json_encode($return));
    
}


/**
 * Procedimento para consulta de um header de lote específico
 */
if (isset($_REQUEST['method']) && $_REQUEST['method'] == "getHeaderRescisao") {
    
    $return = array("status" => 0);
    
    $d = array();

    $ult_projeto = "
        SELECT 
            A.id_header, 
            B.id_projeto, 
            A.situacao,
            (CASE A.situacao 
                WHEN 0 THEN '".utf8_encode('NÃO ESP.')."'
                WHEN 1 THEN '".utf8_encode('EXPERIÊNCIA' )."'
                WHEN 2 THEN 'NORMAL'
                WHEN 3 THEN 'EVENTO'
            END) AS status_situacao,                
            A.fator, 
            0 diastrab,
            A.tipo_dispensa,
            A.aviso_previo, 
            DATE_FORMAT(A.data_demi, '%d/%m/%Y') AS data_saida, 
            DATE_FORMAT(A.data_aviso,'%d/%m/%Y') AS data_aviso,
            DATE_FORMAT(A.criado_em,'%d/%m/%Y - %H:%i:%s') AS data_formatada, 
            B.nome AS nome_projeto, 
            C.especifica AS dispensa,
            A.taxaRAT,
            D.nome as criado_por_nome,
            (SELECT COUNT(P.id_clt) FROM rh_recisao_provisao_de_gastos P WHERE P.id_recisao_lote=A.id_header) AS total, 
            (SELECT COUNT(R.id_clt) FROM rh_recisao R WHERE R.id_recisao_lote=A.id_header) As efetivada                 
        FROM header_recisao_lote AS A
            LEFT JOIN projeto AS B ON(A.id_projeto = B.id_projeto)
            LEFT JOIN rhstatus AS C ON(A.tipo_dispensa = C.codigo)
            LEFT JOIN funcionario AS D ON(A.criado_por = D.id_funcionario)
        WHERE A.id_header = {$_REQUEST['id_header']} AND A.status = 1";
        
    $rs_ult_projeto = mysql_query($ult_projeto) or die("Erro ao selecionar dados do ultimo header cadastrado");
        
    while ($linha = mysql_fetch_assoc($rs_ult_projeto)) {

        /*
         * @jacques 
         * adicionado totalizacao de ambas tabelas para compatibilidade entre provisões de gastos anteriores
         * a implementacao da tabela temporária.           
         */
        
        $d[] = array(
            "id_header" => $linha['id_header'],
            "id_projeto" => $linha['id_projeto'],
            "dispensa" => utf8_encode($linha['dispensa']),
            "fator" => $linha['fator'],
            "diastrab" => $linha['diastrab'],
            "tipo_dispensa" => $linha['tipo_dispensa'],
            "aviso_previo" => $linha['aviso_previo'],
            "data_saida" => $linha['data_saida'],
            "data_aviso" => $linha['data_aviso'],
            "status_situacao" => utf8_encode($linha['status_situacao']),
            "taxaRAT" => $linha['taxaRAT'],
            "projeto" => utf8_encode($linha['nome_projeto']),
            "criado_em" => $linha['data_formatada'],
            "criado_por" => utf8_encode($linha['criado_por_nome']),
            "total_participantes" => $linha['total']
        );
    }
    
    //aqui    
    $return = array("status" => 1, "dados_projeto" => $d);

    
    exit(json_encode($return));
    
}


/**
 * ARRUMANDO AINDA
 */
if ((isset($_REQUEST['mostrar_rescisao']) || isset($_REQUEST['mostrar_prov_trab']) || isset($_REQUEST['modelo_xls'])) && !empty($_REQUEST['id_clt'])) {

    $id_projeto = (!empty($_REQUEST['projeto_oculto'])) ? $_REQUEST['projeto_oculto'] : $_REQUEST['projeto'];
    
    
    if($_REQUEST['id_rescisao_lote']==0) {

    $sql = "
        
        SELECT * 
        FROM rh_recisao
        WHERE id_clt IN (SELECT id_clt FROM rh_recisao WHERE id_recisao_lote = 514)
        AND status = 1 

        ";   
    }
    else {
        
        $sql = "
                SELECT r.* FROM 
                    (
                    SELECT
                        B.desconto_inss, 
                        B.desconto_outra_empresa, 
                        D.nome as nome_funcao, 
                        C.especifica, 
                        C.codigo AS codigo, 
                        B.nome as bnome,
                        A.id_recisao,
                        A.id_clt,
                        A.nome,
                        A.id_regiao,
                        A.id_projeto,
                        A.id_curso,
                        A.data_adm,
                        A.data_demi,
                        A.data_proc,
                        A.dias_saldo,
                        A.um_ano,
                        A.meses_ativo,
                        A.motivo,
                        A.fator,
                        A.aviso,
                        A.aviso_valor,
                        A.avos_dt,
                        A.avos_fp,
                        A.dias_aviso,
                        A.data_aviso,
                        A.data_fim_aviso,
                        A.fgts8,
                        A.fgts40,
                        A.fgts_anterior,
                        A.fgts_cod,
                        A.fgts_saque,
                        A.sal_base,
                        A.saldo_salario,
                        A.inss_ss,
                        A.ir_ss,
                        A.terceiro_ss,
                        A.previdencia_ss,
                        A.dt_salario,
                        A.inss_dt,
                        A.ir_dt,
                        A.previdencia_dt,
                        A.ferias_vencidas,
                        A.umterco_fv,
                        A.ferias_pr,
                        A.umterco_fp,
                        A.inss_ferias,
                        A.ir_ferias,
                        A.sal_familia,
                        A.to_sal_fami,
                        A.ad_noturno,
                        A.adiantamento,
                        A.insalubridade,
                        A.ajuda_custo,
                        A.vale_refeicao,
                        A.debito_vale_refeicao,
                        A.a480,
                        A.a479,
                        A.a477,
                        A.comissao,
                        A.gratificacao,
                        A.extra,
                        A.outros,
                        A.movimentos,
                        A.valor_movimentos,
                        A.total_rendimento,
                        A.total_deducao,
                        A.total_liquido,
                        A.arredondamento_positivo,
                        A.devolucao,
                        A.faltas,
                        A.valor_faltas,
                        A.user,
                        A.folha,
                        A.status,
                        A.adicional_noturno,
                        A.dsr,
                        A.desc_auxilio_distancia,
                        A.um_terco_ferias_dobro,
                        A.fv_dobro,
                        A.aux_distancia,
                        A.reembolso_vale_refeicao,
                        A.periculosidade,
                        A.desconto_vale_alimentacao,
                        A.diferenca_salarial,
                        A.ad_noturno_plantao,
                        A.desconto,
                        A.desc_vale_transporte,
                        A.pensao_alimenticia_15,
                        A.pensao_alimenticia_20,
                        A.pensao_alimenticia_30,
                        A.lei_12_506,
                        A.ferias_aviso_indenizado,
                        A.umterco_ferias_aviso_indenizado,
                        A.adiantamento_13,
                        A.fp_data_ini,
                        A.fp_data_fim,
                        A.fv_data_ini,
                        A.fv_data_fim,
                        A.qnt_dependente_salfamilia,
                        A.base_inss_ss,
                        A.percentual_inss_ss,
                        A.base_irrf_ss,
                        A.percentual_irrf_ss,
                        A.parcela_deducao_irrf_ss,
                        A.qnt_dependente_irrf_ss,
                        A.valor_ddir_ss,
                        A.base_fgts_ss,
                        A.base_inss_13,
                        A.percentual_inss_13,
                        A.base_irrf_13,
                        A.percentual_irrf_13,
                        A.parcela_deducao_irrf_13,
                        A.base_fgts_13,
                        A.qnt_dependente_irrf_13,
                        A.valor_ddir_13,
                        A.salario_outra_empresa,
                        A.desconto_inss_outra_empresa,
                        A.vinculo_id_rescisao,
                        A.rescisao_complementar,
                        A.recisao_provisao_de_calculo,
                        A.id_recisao_lote,
                        A.reintegracao,
                        (SELECT taxaRAT FROM header_recisao_lote WHERE id_header = {$_REQUEST['id_rescisao_lote']}) taxaRAT
                    FROM rh_recisao AS A
                        LEFT JOIN rh_clt AS B ON(A.id_clt = B.id_clt)
                        LEFT JOIN rhstatus AS C ON(B.`status` = C.codigo)
                        LEFT JOIN curso AS D ON(D.id_curso = B.id_curso)
                    WHERE 
                        A.id_clt IN(" . implode(",", $_REQUEST['id_clt']) . ")
                        AND A.recisao_provisao_de_calculo = 1 
                        AND A.id_recisao_lote = '{$_REQUEST['id_rescisao_lote']}' 

                    UNION ALL

                    SELECT 
                        B.desconto_inss, 
                        B.desconto_outra_empresa, 
                        D.nome as nome_funcao, 
                        C.especifica, 
                        C.codigo AS codigo, 
                        B.nome as bnome,
                        A.id_recisao,
                        A.id_clt,
                        A.nome,
                        A.id_regiao,
                        A.id_projeto,
                        A.id_curso,
                        A.data_adm,
                        A.data_demi,
                        A.data_proc,
                        A.dias_saldo,
                        A.um_ano,
                        A.meses_ativo,
                        A.motivo,
                        A.fator,
                        A.aviso,
                        A.aviso_valor,
                        A.avos_dt,
                        A.avos_fp,
                        A.dias_aviso,
                        A.data_aviso,
                        A.data_fim_aviso,
                        A.fgts8,
                        A.fgts40,
                        A.fgts_anterior,
                        A.fgts_cod,
                        A.fgts_saque,
                        A.sal_base,
                        A.saldo_salario,
                        A.inss_ss,
                        A.ir_ss,
                        A.terceiro_ss,
                        A.previdencia_ss,
                        A.dt_salario,
                        A.inss_dt,
                        A.ir_dt,
                        A.previdencia_dt,
                        A.ferias_vencidas,
                        A.umterco_fv,
                        A.ferias_pr,
                        A.umterco_fp,
                        A.inss_ferias,
                        A.ir_ferias,
                        A.sal_familia,
                        A.to_sal_fami,
                        A.ad_noturno,
                        A.adiantamento,
                        A.insalubridade,
                        A.ajuda_custo,
                        A.vale_refeicao,
                        A.debito_vale_refeicao,
                        A.a480,
                        A.a479,
                        A.a477,
                        A.comissao,
                        A.gratificacao,
                        A.extra,
                        A.outros,
                        A.movimentos,
                        A.valor_movimentos,
                        A.total_rendimento,
                        A.total_deducao,
                        A.total_liquido,
                        A.arredondamento_positivo,
                        A.devolucao,
                        A.faltas,
                        A.valor_faltas,
                        A.user,
                        A.folha,
                        A.status,
                        A.adicional_noturno,
                        A.dsr,
                        A.desc_auxilio_distancia,
                        A.um_terco_ferias_dobro,
                        A.fv_dobro,
                        A.aux_distancia,
                        A.reembolso_vale_refeicao,
                        A.periculosidade,
                        A.desconto_vale_alimentacao,
                        A.diferenca_salarial,
                        A.ad_noturno_plantao,
                        A.desconto,
                        A.desc_vale_transporte,
                        A.pensao_alimenticia_15,
                        A.pensao_alimenticia_20,
                        A.pensao_alimenticia_30,
                        A.lei_12_506,
                        A.ferias_aviso_indenizado,
                        A.umterco_ferias_aviso_indenizado,
                        A.adiantamento_13,
                        A.fp_data_ini,
                        A.fp_data_fim,
                        A.fv_data_ini,
                        A.fv_data_fim,
                        A.qnt_dependente_salfamilia,
                        A.base_inss_ss,
                        A.percentual_inss_ss,
                        A.base_irrf_ss,
                        A.percentual_irrf_ss,
                        A.parcela_deducao_irrf_ss,
                        A.qnt_dependente_irrf_ss,
                        A.valor_ddir_ss,
                        A.base_fgts_ss,
                        A.base_inss_13,
                        A.percentual_inss_13,
                        A.base_irrf_13,
                        A.percentual_irrf_13,
                        A.parcela_deducao_irrf_13,
                        A.base_fgts_13,
                        A.qnt_dependente_irrf_13,
                        A.valor_ddir_13,
                        A.salario_outra_empresa,
                        A.desconto_inss_outra_empresa,
                        A.vinculo_id_rescisao,
                        A.rescisao_complementar,
                        A.recisao_provisao_de_calculo,
                        A.id_recisao_lote,
                        A.reintegracao,
                        (SELECT taxaRAT FROM header_recisao_lote WHERE id_header = {$_REQUEST['id_rescisao_lote']}) taxaRAT
                    FROM rh_recisao_provisao_de_gastos AS A
                        LEFT JOIN rh_clt AS B ON(A.id_clt = B.id_clt)
                        LEFT JOIN rhstatus AS C ON(B.`status` = C.codigo)
                        LEFT JOIN curso AS D ON(D.id_curso = B.id_curso)
                    WHERE 
                        A.id_clt IN(" . implode(",", $_REQUEST['id_clt']) . ")
                        AND A.recisao_provisao_de_calculo = 1 
                        AND A.id_recisao_lote = {$_REQUEST['id_rescisao_lote']} 
                        AND A.id_clt NOT IN 
                                (
                                SELECT id_clt 
                                FROM rh_recisao 
                                WHERE id_recisao_lote = {$_REQUEST['id_rescisao_lote']}
                                )
                    ) r
                ORDER BY r.especifica ASC
            ";
                            
    }
    
    
    $sql_status = 
            "
            SELECT 
                C.codigo, 
                C.especifica 
            FROM rh_recisao AS A
                LEFT JOIN rh_clt AS B ON(A.id_clt = B.id_clt)
                LEFT JOIN rhstatus AS C ON(B.`status` = C.codigo)
            WHERE 
                A.id_projeto = '{$id_projeto}' 
                AND A.id_clt IN(" . implode(",", $_REQUEST['id_clt']) . ")
                AND A.recisao_provisao_de_calculo = 1 
                AND A.id_recisao_lote = '{$_REQUEST['id_rescisao_lote']}' 
            GROUP BY B.`status`

            UNION ALL
            
            SELECT 
                C.codigo, 
                C.especifica 
            FROM rh_recisao_provisao_de_gastos AS A
                LEFT JOIN rh_clt AS B ON(A.id_clt = B.id_clt)
                LEFT JOIN rhstatus AS C ON(B.`status` = C.codigo)
            WHERE 
                A.id_projeto = '{$id_projeto}' 
                AND A.id_clt IN(" . implode(",", $_REQUEST['id_clt']) . ")
                AND A.recisao_provisao_de_calculo = 1 
                AND A.id_recisao_lote = '{$_REQUEST['id_rescisao_lote']}' 
                AND A.id_clt NOT IN 
                        (
                        SELECT id_clt 
                        FROM rh_recisao 
                        WHERE 
                            id_projeto = '{$id_projeto}' 
                            AND id_clt IN(" . implode(",", $_REQUEST['id_clt']) . ")
                            AND recisao_provisao_de_calculo = 1 
                            AND id_recisao_lote = '{$_REQUEST['id_rescisao_lote']}' 
                        )
            GROUP BY B.`status`

            ";
                            
                            
        
    /*
     * @jacques
     * adicionado totalizacao de ambas tabelas para compatibilidade entre provisões de gastos anteriores
     * a implementacao da tabela temporária.           
     */
        

    $sql_participantes = "
        
        SELECT COUNT(A.id_clt) AS total_participantes
        FROM rh_recisao AS A
            LEFT JOIN rh_clt AS B ON(A.id_clt = B.id_clt)
            LEFT JOIN rhstatus AS C ON(B.`status` = C.codigo)
        WHERE 
            A.id_projeto = '{$id_projeto}' 
            AND A.id_clt IN(" . implode(",", $_REQUEST['id_clt']) . ")
            AND A.recisao_provisao_de_calculo = 1 
            AND A.id_recisao_lote = '{$_REQUEST['id_rescisao_lote']}' 
        GROUP BY A.id_projeto 
                
        UNION ALL
        
        SELECT COUNT(A.id_clt) AS total_participantes
        FROM rh_recisao_provisao_de_gastos AS A
            LEFT JOIN rh_clt AS B ON(A.id_clt = B.id_clt)
            LEFT JOIN rhstatus AS C ON(B.`status` = C.codigo)
        WHERE 
            A.id_projeto = '{$id_projeto}' 
            AND A.id_clt IN(" . implode(",", $_REQUEST['id_clt']) . ")
            AND A.recisao_provisao_de_calculo = 1 
            AND A.id_recisao_lote = '{$_REQUEST['id_rescisao_lote']}' 
            AND id_clt NOT IN 
                    (
                    SELECT id_clt 
                    FROM rh_recisao 
                    WHERE 
                        id_projeto = '{$id_projeto}' 
                        AND id_clt IN(" . implode(",", $_REQUEST['id_clt']) . ")
                        AND recisao_provisao_de_calculo = 1 
                        AND id_recisao_lote = '{$_REQUEST['id_rescisao_lote']}' 
                    )
        GROUP BY A.id_projeto 
        
        ";

    $rsParticipantes = mysql_query($sql_participantes);
    $total_participantes = mysql_fetch_assoc($rsParticipantes);
}



if (!empty($sql)) {
    $qr_relatorio = mysql_query($sql) or die(mysql_error());
    
    $status = mysql_query($sql) or die(mysql_error());
    
    $num_rows = mysql_num_rows($qr_relatorio);
    
    if (isset($_REQUEST['mostrar_rescisao']) || isset($_REQUEST['mostrar_prov_trab']) || isset($_REQUEST['modelo_xls'])) {
        
        $status_array = array();
        $nome_status_array = array();
        $qr_status = mysql_query($sql_status);
        
        while ($linhas = mysql_fetch_array($qr_status)) {
            $status_array[] = $linhas["codigo"];
            $nome_status_array[$linhas["codigo"]] = $linhas["especifica"];
        }
        
    }
}


$fator = array("empregado" => "Empregado", "empregador" => "Empregador");
$aviso = array("trabalhado" => "Trabalhado", "indenizado" => "Indenizado");
$contratacao = array("1" => "Determinado", "2" => "Indeterminado");
$situacao = array(1 => "Período de Experiência", 2 => "Atividade Normal", 3 => "Em Evento");


$contratoSel = (isset($_REQUEST['contrato'])) ? $_REQUEST['contrato'] : "";
$regiaoSel = (isset($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : "";
$projetoSel = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : "";
$dispensaSel = (isset($_REQUEST['dispensa'])) ? $_REQUEST['dispensa'] : "";
$fatorSel = (isset($_REQUEST['fator'])) ? $_REQUEST['fator'] : "";
$avisoPrevioSel = (isset($_REQUEST['aviso'])) ? $_REQUEST['aviso'] : "";

$title = ":: Intranet :: Provisão de Gastos";

?>
<html>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">     
    <head>
        <title><?=$title?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../favicon.ico" />
        <link href="../net1.css" rel="stylesheet" type="text/css" />
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css?tag_rev" rel="stylesheet" type="text/css" />
        <link href="//code.jquery.com/ui/1.12.0/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
        <link href="../css/validationEngine.jquery.css?tag_rev" rel="stylesheet" type="text/css" />
        <link href="../favicon.ico" rel="shortcut icon" />
        <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
        <script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>        
        <script src="../js/jquery-1.8.3.min.js?tag_rev" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js?tag_rev" type="text/javascript"></script>
        <script src="../js/jquery-base64-master/jquery.base64.js" type="text/javascript"></script>
        <script src="../js/global.js?tag_rev" type="text/javascript"></script> 
        <script src="../js/ramon.js?tag_rev" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine_2.6.2.js?tag_rev" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js?tag_rev" type="text/javascript"></script>
        <script src="../js/tableExport.jquery.plugin-master/tableExport.js?tag_rev" type="text/javascript"></script>    
        <script src="/intranet/jquery/priceFormat.js" type="text/javascript"></script>        

        <script>
            
            var xhr = null;
            
            function scrollTo(hash) {

                location.hash = "#" + hash;

            }               
            
            $(function () {
                
                
                define_condicoes_rescisorias();
                
                $.each(['show', 'hide'], function (i, ev) {
                  var el = $.fn[ev];
                  $.fn[ev] = function () {
                    this.trigger(ev);
                    return el.apply(this, arguments);
                  };
                });                

                var progressTimer,
                    progressbar = $("#progressbar"),
                    progressLabel = $( ".progress-label" ),
                    dialogButtons = [{
                    text: "Continuar em segundo plano",
                    click: buttonClose
                  }],
                  dialog = $("#dialog").dialog({
                    autoOpen: false,
                    closeOnEscape: false,
                    resizable: false,
                    show: 'fade',
                    hide: { effect:"drop",duration:400,direction:"up" },
                    position: top,
                    title: "",
                    buttons: dialogButtons,
                    create: function(event, ui) {
                    },                    
                    open: function(event, ui) {
                    },
                    beforeClose: function() {
                      buttonStartProgressBar.button( "option", {
                        disabled: false,
                        label: "Processando..."
                      });
                    }
                  }),
                  buttonStartProgressBar = $("#buttonStartProgressBar")
                    .button()
                    .on("click", function() {
                      $(this).button( "option", {
                        disabled: true,
                        label: "Processando..."
                      });
                      dialog.dialog("open");
                    });

                progressbar.progressbar({
                  value: false,
                  change: function() {
                    progressLabel.text( "Processo atual: " + progressbar.progressbar("value") + "% (" + $("#clt_count").val() + "/" + $("input[name='id_clt[]']:checked").size() + ")");
                  },
                  complete: function() {
                    progressLabel.text( "Operação Finalizada!" );
                    dialog.dialog( "option", "buttons", [{
                      text: "Close",
                      click: buttonClose
                    }]);
                    $(".ui-dialog button").last().trigger( "focus" );
                  }
                });

                function progress() {
                  var val = progressbar.progressbar( "value" ) || 0;

                  progressbar.progressbar("value",val + Math.floor( Math.random() * 3 ));

                }

                function buttonClose() {
                    
                    dialog
                      .dialog( "option", "buttons", dialogButtons )
                      .dialog( "close" );
                    progressbar.progressbar( "value", false );
                    progressLabel
                      .text( "Fechando ..." );
                    buttonStartProgressBar.trigger("focus");

                    $(".carregando").hide();                  
                  
                }
                
                function define_condicoes_rescisorias(){
                    
                    situacao_tipo_dispensa();
                    tipo_aviso()
                    
                }
                
                function situacao_tipo_dispensa() {
                    
                    $("#dispensa, #fator").removeAttr("disabled");
                    
                    switch ($('#situacao').val()) {
                        case '3':
                        case '2':
                            $("#dispensa option").each(function(){

                                if($(this).val() != 63 && $(this).val() != 64 && $(this).val() != 66) {

                                    $(this).prop("disabled", false); 

                                }
                                else {

                                    $(this).attr("disabled", "disabled");

                                }

                                
                            });    
                            
                            $("#dataAviso").prop("disabled", false); 

                            $("#dataDemi").val('');
                            $('#dataDemi').prop("disabled", "disabled"); 
                            
                            $("#dispensa").val(61);
                            
                            $("#p_data_aviso").show();
                            $("#p_aviso").show();
                            
                            break;
                        case '1':
                            $("#dispensa option").each(function(){
                                
                                if($(this).val() != 63 && $(this).val() != 64 && $(this).val() != 66 && $(this).val() != 81) {

                                  $(this).attr("disabled", "disabled");

                                }
                                else {

                                  $(this).prop("disabled", false); 


                                }

                                $("#dataAviso").val('');
                                $("#dataAviso").prop("disabled", "disabled"); 

                                $("#dataDemi").val('');
                                $("#dataDemi").prop("disabled", false); 

                                $("#dispensa").val(66);

                                $("#p_data_aviso").hide();
                                $("#p_aviso").hide();
                              
                            });    
                            
                            break;
                    }
                    
                    $("#dispensa").trigger("change"); 
                }
                
                function tipo_aviso() {
                    
                    var dataAviso = $('#dataAviso'); 
                    var dataDemi = $('#dataDemi'); 
                    
                    switch ($('#aviso').val()) {
                        case 'trabalhado':
                            
                            var data = dataAviso.datepicker('getDate', '+29d'); 
                            
                            data.setDate(data.getDate()+29); 
                            
                            dataDemi.datepicker('setDate', data);                            
                            
                            //console.log(dataDemi.val());
                            //dataDemi.datepicker("option","minDate",dataDemi); 
                            //dataDemi.focus();                            
                            
                            break;
                        case 'indenizado':
                            
                            dataDemi.val(dataAviso.val());
                            
                            break;
                    }
                            
                    console.log('tipo_aviso()');
                    
                    $("#dispensa").trigger("change"); 
                }
                
                
                $(".scroll").click(function(event){
                       event.preventDefault();
                       var dest=0;
                       if($(this.hash).offset().top > $(document).height()-$(window).height()){
                            dest=$(document).height()-$(window).height();
                       }else{
                            dest=$(this.hash).offset().top;
                       }
                       $('html,body').animate({scrollTop:dest}, 1000,'swing');
               });                
               
                $("#exportarExcel").click(function (e) {
                    
                    $("#relatorio_exp img:last-child").remove();

                    var html = $("#relatorio_exp").html();
                    
                    $("#data_xls").val(html); 
                    $("#form").submit();
                    
                });    
                
                
//                $("#exportarExcel").click(function() { 
//                    var regex = "/<(.|\n)*?>/";
//                    var result = $('#content').html().replace(regex, "");
////                    'data:application/vnd.ms-excel'
////                    'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
//                    window.open('data:application/vnd.ms-excel,' + result);
//                    
//                });                

//                $("#form").validationEngine();

                $("#dataDemi").datepicker();
                $("#dataAviso").datepicker();

                var id_destination = "projeto";
                
                $('#regiao').ajaxGetJson("../methods.php", {method: "carregaProjetos"}, function (data) {
                    
                    removeLoading();
                    
                    $("#" + id_destination).html(data);
                    
                    var selected = $("input[name=hide_" + id_destination + "]").val();
                    
                    if (selected !== undefined) {
                        $("#" + id_destination).val(selected);
                    }
                    
                    $('#projeto').trigger('change');
                    
                }, "projeto");

                /****************************FILTRO DE FUNCÃO************************************/
                $("body").on("click", "#filtro_funcao", function () {
                    
                    // desmarca todos os CLTs
                    $('#id_clt_todos').attr("checked", false);
                    
                    $('.clts').attr("checked", false);
                    
                    $("#checkboxes").css('display', 'none'); // oculta as funcoes
                    
                    $("#tbRelatorio tbody tr").hide(); // esconde todas as linhas dos CLTs
                    
                    $('.checkFuncao:checked').each(function () {
                        
                        var funcao = $(this).val();
                        
                        $("#tbRelatorio tbody tr[data-curso='" + funcao + "']").show(); // exibe os CLTs pro funcao
                        
                    });
                });
                
                $("body").on('change', '.tudo', function () {
                    console.log('aloha');
                    if ($(this).is(":checked")) {
                        $(".checkFuncao").attr("checked", true);
                    } else {
                        $(".checkFuncao").attr("checked", false);
                    }
                });
                
                $("body").on('change', '#dataAviso', function () {
                    
                    define_condicoes_rescisorias();
                    
                });                
                
                
                /**
                 * 
                 */
                $("body").on('change', '#dataDemi', function () {
                    
                    var data_aviso = $("#dataAviso").val().toString().substring(6, 10)+'-'+$("#dataAviso").val().toString().substring(3, 5)+'-'+$("#dataAviso").val().toString().substring(0, 2);
                    var data_demi =  $("#dataDemi").val().toString().substring(6, 10)+'-'+$("#dataDemi").val().toString().substring(3, 5)+'-'+$("#dataDemi").val().toString().substring(0, 2);
                    
                    $("input[name='id_clt[]']").each(function (i){
                        
                        console.log(i);
                        
                        if((new Date(data_demi).getTime() > new Date($(this).data("contratacao")).getTime()) && $('#situacao').val()==1){
                            
                            $(this).parent().css("background-color","red");
                            
                        }                        
                        else {
                            
                            $(this).parent().css("background-color","white");
                            
                        }    
                        
                    });
                    
                });                
                
                
                $("body").on('change', '#situacao', function () {
                    
                    define_condicoes_rescisorias();
                    
                });                

                /****************************FILTRO DE FUNCÃO************************************/

                $("body").on("click", ".calcula_multa", function () {
                    var clt = $(this).data("key");
                    var nome = $(this).data("nome");
                    var html = "";
                    var ano = 0;
                    var tamanho = 260;
                    var tamanhoNovo = 0;

                    $.ajax({
                        type: "post",
                        dataType: "json",
                        data: {
                            clt: clt,
                            method: "soma_fgts"
                        },
                        success: function (data) {
                            var total_anos = 0;
                            $.each(data, function (k, v) {
                                var qntAnos = Object.keys(data).length;
                                tamanhoNovo = tamanho * qntAnos;
                                html += "<div class='lista_fgts'>";

                                if (ano != k) {
                                    ano = k;
                                    var total = 0;
                                    html += "<h3>" + k + "</h3>";
                                    $.each(v, function (mes, tipo) {
                                        $.each(tipo, function (k, valor) {
                                            if (k == "normal") {
                                                html += "<p>" + mes + "/" + ano + " - " + valor + "</p>";
                                            } else {
                                                html += "<p>" + mes + "/" + ano + " - " + valor + " (13°)" + "</p>";
                                            }
                                            total = total + parseFloat(valor);
                                            total_anos += parseFloat(valor);
                                        });
                                    });
                                    html += "<h2>" + total.toFixed(2) + "</h2>";
                                }
                                html += "</div>";
                            });

                            html += "<div id='total_anos'><p><span>Total: </span>" + total_anos.toFixed(2) + "</p><p><span>Valor Multa FGTS 50%: </span>" + (total_anos * 0.50).toFixed(2) + "</p></div>";
                            $("#fgts_folha").html(html);


                            thickBoxModal("Dados de FGTS - " + nome, "#fgts_folha", 700, tamanhoNovo);
                        }
                    });

                });

                $("body").on("click", ".visualizar", function () {
                
//                    $("#form").each(function(){
//                        this.reset();
//                    });                  
                
                    $("#relatorio_exp").remove();
                    $(".totalizador").remove();
                    $(".imprime").remove();
                    $("#filtro-relatorio").show();
                    $(".carregando").show();
                    
                    
                    var id_rescisao_lote = $(this).data("key");
                    var projeto = $(this).data("projeto");
                    
                    $("#projeto_oculto").val(projeto);
                    
                    $.ajax({
                        url: "provisao_de_gastos.php",
                        type: "POST",
                        dataType: "json",
                        contentType: "application/x-www-form-urlencoded;charset=ISO-8859-1",                        
                        data: {
                            method: "visualizarRescisao",
                            header: id_rescisao_lote
                        },
                        success: function (data) {
                            var html = "";
                            
                            if (data.status==1) {
                                $.ajax({
                                    url: "",
                                    data: {
                                        method: "carregaFuncoes",
                                        regiao: data.id_regiao,
                                        projeto: data.id_projeto,
                                        id_rescisao_lote: id_rescisao_lote
                                    },
                                    type: "POST",
                                    dataType: "json",
                                    success: function (funcao) {
                                        var html = "";
                                        html += "<table id='tbRelatorio' border='0' cellpadding='0' cellspacing='0' class='grid' width='100%' style='page-break-after:auto; margin-top: 20px;'><thead><tr><th colspan='6' style='height:90px; text-align:left; background:white; border-top: 1px solid #ccc'> ";
                                        html += "<p>Selecione uma Funcao:</p>";
                                        
                                        html += "<div class=\"multiselect\"><div class=\"selectBox\" onclick=\"showCheckboxes()\">";
                                        html += "<select >";
                                        html += "<option value='0'>« Selecione »</option>";
                                        html += "</select>";

                                        html += "<div class=\"overSelect\"></div></div>";
                                        html += "<div id=\"checkboxes\">";
                                        html += "<label for=\"a-0\"><input name='filtro_funcao[]' class='tudo' type=\"checkbox\" id=\"a-0\" value='0'/>« Todos »</label>";
                                        
                                        console.log(funcao);
                                        
                                        $.each(funcao, function (k, v) {
                                            
                                            html += "<label for=\"a-" + v.id_curso + "\"><input name='filtro_funcao[]' class='checkFuncao' type=\"checkbox\" id=\"a-" + v.id_curso + "\" value='" + v.id_curso + "'/>" + v.nome + "</label>";
                                            
                                        });
                                        
                                        html += "</div>";
                                        html += "</div>";
                                        html += "<div class=\"class_button\">";
                                        html += "   <button type='button' id='filtro_funcao' >Filtrar</button>";
                                        html += "   <button type='button' id='lanca_movimento_lote' class='class_button' data-id_rescisao_lote='" + id_rescisao_lote + "'>Lançar movimentos em lote</button>";
                                        html += "   <button type='submit' id='mostrar_rescisao' class='class_button' name='mostrar_rescisao' value='mostrar_rescisao' data-id_rescisao_lote='" + id_rescisao_lote + "'>Visualizar Previsão de Gastos</button>";
                                        //html += "   <button type='submit' id='mostrar_prov_trab' class='class_button' name='mostrar_prov_trab' value='mostrar_prov_trab' data-id_rescisao_lote='" + id_rescisao_lote + "'>Visualizar Provisão Trabalista</button>";
                                        html += "   <input type='hidden' name='id_rescisao_lote' id='id_rescisao_lote' value='" + id_rescisao_lote + "' />";
                                        html += "</div>";

                                        
                                        html += "</th></tr><tr style='font-size:10px !important;'><th rowspan='2'><input type='checkbox' name='id_clt_todos' id='id_clt_todos'/></th><th rowspan='2'>NOME</th><th rowspan='2'>FUNCÃO</th><th rowspan='2'>STATUS</th><th rowspan='2'>SALÁRIO BRUTO</th></tr></thead>";
                                        
                                        $.each(data.dados, function (k, v) {
                                            
                                            html += "<tr class='' style='font-size:11px;' data-id_recisao='" + v.id_recisao + "' data-curso='" + v.id_curso + "'><td align='center'><input type='checkbox' class='clts validate[minCheckbox[1]]' name='id_clt[]' id='id_clt_" + v.id + "' value='" + v.id + "' data-id_recisao='" + v.id_recisao + "' data-curso='" + v.id_curso + "'/></td><td align='left'><label for='id_clt_" + v.id + "'>" + v.nome + "</label></td><td align='left'>" + v.funcao + "</td><td align='left'>" + v.status + "</td><td align='right'>R$ " + v.sal_base + "</td></tr>";
                                            
                                        });
                                        
                                        html += "</table>";
                                        
                                        $("#lista_funcionarios").html(html);
                                        
                                        $("html,body").animate({scrollTop: $("#tbRelatorio").offset().top},"500");      
                                        
                                        $(".carregando").hide();
                                        
                                        
                                    }
                                });
                            }
                            else {
                            
                                alert('Nenhum registro retornado');
                                
                            }
                            
                        },
                        error: function(data) {
                             $(".carregando").hide();
                        
                             console.log(data);
                             alert("Erro ao selecionar recisao");
                }
                    });
                });

                $("#visualizar_participantes").click(function () {

                    var dados = $("#form").serialize();
                    var projeto = $("#projeto option:selected").text();
                    var situacao = $("#situacao option:selected").text();
                    
                    define_condicoes_rescisorias();
                    
//                    $("#form").each(function(){
//                        this.reset();
//                    });                            
                    
                    $(".carregando").show();
                    
                    $.ajax({
                        url: "provisao_de_gastos.php?method=verificaParticipantes&" + dados,
                        type: "POST",
                        dataType: "json",
                        success: function (data) {
                            
                            console.log(data);
                            
                            if (data.status == 1) {
                                $.ajax({
                                    url: "",
                                    data: {
                                        method: "carregaFuncoes",
                                        regiao: data.id_regiao,
                                        projeto: data.id_projeto,
                                        id_rescisao_lote: data.id_rescisao_lote
                                    },
                                    type: "POST",
                                    dataType: "json",
                                    success: function (funcao) { 
                                        
                                        var html = "";
                                        
                                        html += "<input type=\"hidden\" id=\"clt_count\" name=\"clt_count\" value=\"0\"/>";
                                        html += "<table id='tbRelatorio' border='0' cellpadding='0' cellspacing='0' class='grid' width='100%' style='page-break-after:auto; margin-top: 20px;'><thead><tr><th colspan='6' style='height:90px; text-align:left; background:white; border-top: 1px solid #ccc' onMouseOver='$(\".formError\").remove()'> ";
                                        html += "<h1>LISTAGEM DE CLTS DO PROJETO "+projeto+" EM "+situacao+"</h1>"
                                        html += "<p>Selecione uma Funcao:</p>";
                                        html += "<div class=\"multiselect\"><div class=\"selectBox\" onclick=\"showCheckboxes()\">";
                                        html += "<select >";
                                        html += "<option value='0'>« Selecione »</option>";
                                        html += "</select>";

                                        html += "<div class=\"overSelect\"></div></div>";
                                        html += "<div id=\"checkboxes\">";
                                        html += "<label for=\"a-0\"><input name='filtro_funcao[]' class='tudo' type=\"checkbox\" id=\"a-0\" value='0'/>« Todos »</label>";
                                        
                                        
                                        $.each(funcao, function (k, v) {
                                            
                                            html += "<label for=\"a-" + v.id_curso + "\"><input name='filtro_funcao[]' class='checkFuncao' type=\"checkbox\" id=\"a-" + v.id_curso + "\" value='" + v.id_curso + "'/>" + v.nome + "</label>";
                                            
                                        });
                                        
                                        html += "</div>";
                                        html += "</div>";
                                        html += "<div class=\"class_button\">";
                                        html += "   <button type='button' id='filtro_funcao' class='button'>Filtrar</button>";
                                        html += "</div>";
                                        //html += "<div class=\"class_button\">";
                                        //html += "   <button type='button' id='gerar' class='button'>Gerar Provisao de Gastos</button>";
                                        //html += "</div>";
                                        html += "<div class=\"class_button\">";
                                        html += "   <button type='button' id='criar-lote' class='button'>Criar Lote</button>";
                                        html += "</div>";

                                        html += "</th></tr><tr style='font-size:10px !important;'><th rowspan='2'><input type='checkbox' name='id_clt_todos' id='id_clt_todos'/></th><th rowspan='2'>NOME</th><th rowspan='2'>FUNCÃO</th><th rowspan='2'>STATUS</th><th rowspan='2'>SALÁRIO BRUTO</th></tr></thead>";
                                        
                                        $.each(data.dados, function (k, v) {
                                            
                                            html += "<tr id='tr_clt_" + v.id + "' class='clt_" + v.id + "' style='font-size:11px;' data-curso='" + v.id_curso + "'><td align='center'><input type='checkbox' class='clts validate[minCheckbox[1]]' name='id_clt[]' id='id_clt_" + v.id + "' value='" + v.id + "' data-contratacao='" + v.data_contratacao + "'/></td><td align='left'><label for='id_clt_" + v.id + "'>" + v.nome + "</label></td><td align='left'>" + v.funcao + "</td><td align='left'>" + v.status + "</td><td align='right'>R$ " + v.sal_base + "</td></tr>";
                                            
                                        });
                                        
                                        html += "</table>";

                                        $("#lista_funcionarios").html(html);
                                        //$("#gerar").remove();
                                        //$(".controls").append("<input type='button' name='gerar' value='Gerar' id='gerar'/>");
                                        //$("#dispensa, #fator, #dataAviso").removeAttr("disabled");
                                        

                                    }
                                });
                            }
                            
                            $(".carregando").hide();
                            $("#relatorio_exp").html('').hide();
                            
                        },
                        error: function(data) {
                             $(".carregando").hide();
                         }
                    });
                    

                });
                
                $("body").on("click", ".visualizarLotePendente", function () {
                
                    var id_header = $(this).data("key");
                    
                   $("#lista_funcionarios").html('');
                    
//                    $("#form").each(function(){
//                        this.reset();
//                    });                            
                    
                    $(".carregando").show();
                    
                    $.ajax({
                        type: "post",
                        dataType: "json",
                        data: {
                            method: "visualizarLotePendente",
                            id_header: id_header
                        },
                        success: function (data) {
                            
                            if(data.status){
                            
                                var html = "";

                                $("#filtro-relatorio").hide();

                                html += "<table id='tbRelatorio' border='0' cellpadding='0' cellspacing='0' class='grid' width='100%' style='page-break-after:auto; margin-top: 20px;'><thead><tr><th colspan='6' style='height:90px; text-align:left; background:white; border-top: 1px solid #ccc'> ";
                                html += "<h1>RESCISÕES NÃO PROCESSADAS DO LOTE SELECIONADO</h1>"
                                html += "<div class=\"class_button\">";
                                html += "   <button type='button' id='gerar-lote' class='button' data-id_header='"+id_header+"'>Gerar Provisão de Gastos do Lote</button>";
                                html += "</div>";

                                html += "</th></tr><tr style='font-size:10px !important;'><th rowspan='2'><div><input type='checkbox' name='id_clt_todos' id='id_clt_todos'/></div><div></th><th rowspan='2'>NOME</th><th rowspan='2'>FUNCÃO</th><th rowspan='2'>STATUS</th><th rowspan='2'>SALÁRIO BRUTO</th></tr></thead>";

                                $.each(data.dados, function (k, v) {
                                    html += "<tr id='clt_" + v.id + "' class='' style='font-size:11px;' data-curso='" + v.id_curso + "'><td align='center'><input type='checkbox' class='clts validate[minCheckbox[1]]' name='id_clt[]' id='id_clt_" + v.id + "' value='" + v.id + "' data-id-header='" + id_header + "'/></td><td align='left'><label for='id_clt_" + v.id + "'>" + v.nome + "</label></td><td align='left'>" + v.funcao + "</td><td align='left'>" + v.status + "</td><td align='right'>R$ " + v.sal_base + "</td></tr>";

                                });

                                html += "</table>";

                                $("#lista_funcionarios").html(html);
                                //$("#gerar").remove();
                                //$(".controls").append("<input type='button' name='gerar' value='Gerar' id='gerar'/>");
                                $("#dispensa, #fator, #dataDemi").removeAttr("disabled");

                                $("html,body").animate({scrollTop: $("#tbRelatorio").offset().top},"500");                                        
                                
                            }
                            
                            $(".carregando").hide();
                            
                            
                        }
                    });

                    
                });
                
                $("body").on("show", ".carregando", function () {
                
                    $.blockUI({ message: null });     
                
                })                

                $("body").on("hide", ".carregando", function () {
                    
                    $.unblockUI();
                    
                });                
                
                
                $("body").on("click", "#criar-lote", function () {

                    var isOk = 1;
                    var id_header = 0;
                    var id_regiao = $("#regiao").val();
                    var id_projeto = $("#projeto").val();
                    var situacao = $("#situacao").val();
                    var dispensa = $("#dispensa").val();
                    var fator = $("#fator").val();
                    var dias_trab = $("#distrab").val();
                    var aviso = $("#aviso").val();
                    var data_aviso_fmt = $("#dataAviso").val();
                    var data_saida_fmt = $("#dataDemi").val();
                    var data_aviso = $("#dataAviso").val().toString().substring(6, 10)+'-'+$("#dataAviso").val().toString().substring(3, 5)+'-'+$("#dataAviso").val().toString().substring(0, 2);
                    var data_demi =  $("#dataDemi").val().toString().substring(6, 10)+'-'+$("#dataDemi").val().toString().substring(3, 5)+'-'+$("#dataDemi").val().toString().substring(0, 2);
                    var taxaRAT = $("#taxaRAT").val();
                    var id_clt = [];
                    
                    if(!$("#form").validationEngine('validate')) {

                        return false;

                    }; 
                    
                    /**
                     * Verifica no caso de demissão em período de experiência se existe algum clt fora desse período na data da demissão
                     */
                    $("input[name='id_clt[]']:checked").each(function (){
                        
                        if((new Date(data_demi).getTime() > new Date($(this).data("contratacao")).getTime()) && situacao==1){
                            
                            $(this).parent().css("background-color","red");
                            
                            isOk = 0;
                            
                        }                        
                        else {
                            
                            $(this).parent().css("background-color","white");
                            
                        }    
                        
                    });
                    
                    if(!isOk) {
                        
                        alert('Existem Clts selecionados que não estarão mais em experiência na data de demissão');
                        
                        return false;
                        
                    }    
                    
                    $(".carregando").show('fast','linear',function(){
                    
                        
                        $("#clt_count").val(0);

                        var msg =  "<table>"
                                  +"    <tr>"
                                  +"        <td>Tipo</td>"
                                  +"        <td>"+$("#dispensa option[value="+dispensa+"]").text()+"</td>"
                                  +"    </tr>"
                                  +"    <tr>"
                                  +"        <td>Fator</td>"
                                  +"        <td>"+fator+"</td>"
                                  +"    <tr>"
                                  +"    <tr>"
                                  +"        <td>Aviso</td>"
                                  +"        <td>"+data_aviso_fmt+"</td>"
                                  +"    <tr>"
                                  +"    <tr>"
                                  +"        <td>Tipo</td>"
                                  +"        <td>"+aviso+"</td>"
                                  +"    <tr>"
                                  +"    <tr>"
                                  +"        <td>Demissão</td>"
                                  +"        <td>"+data_saida_fmt+"</td>"
                                  +"    <tr>"
                                  +"    <tr>"
                                  +"        <td>Taxa RAT </td>"
                                  +"        <td>"+taxaRAT+"%</td>"
                                  +"    <tr>"
                                  +"</table>";
                          
                        /**
                         * Tirando do box o totalizar de registro pois estava aumentando em muito o tempo de resposta do sistema para exibição da tela
                         * Necessário fazer loop para adição individual de cada registro do lote a fim de exibir a barra evolutiva.
                         * 
                         */  

                        thickBoxConfirm("Criação de Lote", "Deseja criar um lote com " + $("input[name='id_clt[]']:checked").size() + " registros para rescisão nas condições abaixo?<br><br>"+msg, 500, 350, function (data) {

                            /**
                             * Cria o cabeçário de lote
                             */
                            if (data) {
                                
                                buttonStartProgressBar.trigger("click");
                                
                                dialog.dialog({title: "Adicioando clt ao lote criado ..."});
                                
                                $.ajax({
                                    type: "post",
                                    dataType: "json",
                                    data: {
                                        method: "criarLote",
                                        regiao: id_regiao,
                                        projeto: id_projeto,
                                        situacao: situacao,
                                        dispensa: dispensa,
                                        fator: fator,
                                        diasSaldo: dias_trab,
                                        aviso: aviso,
                                        data_aviso: data_aviso,
                                        data_demi: data_demi,
                                        taxaRAT: taxaRAT
                                    },
                                    error:  function (data) {

                                        $(".carregando").hide();

                                    },    
                                    success: function (data) {

                                        console.log(data);

                                        if (data.status) {
                                            
                                            id_header = data.id_header;

                                            var html = "";
                                            
                                            html += "<tr class='tr_" + id_header + "'><td>" + $("#projeto option[value='"+id_projeto+"']").text() + "</td><td>" + $("#situacao option[value='"+data.situacao+"']").text() + "</td><td>" + $("#dispensa option[value='"+data.tipo_dispensa+"']").text() + "</td><td>" + data.fator + " </td><td>" + data.data_demi_fmt + "</td><td>" + aviso + "</td><td>" + data.data_aviso_fmt + " </td><td>" + data.criado_por + "</td><td>" + data.criado_em + "</td><td align='center'><span id='tot_" + data.id_header + "'>0</span> processados no total de <span id='tot_lote_" + data.id_header + "'>" + data.total + "</span></td><td colspan='2'><a href='javascript:;' data-key='" + data.id_header + "' class='desprocessar' style='text-decoration: none; color: #0000CC; font-weight: bold; cursor: pointer;'><img src='../imagens/icones/icon-delete.gif' title='desprocessar' /></a></td><td colspan='1'><a href='javascript:;' data-key='"+ data.id_header +"' data-projeto='"+ id_projeto +"' class='visualizarLotePendente' style='text-decoration: none; color: #0000CC; font-weight: bold; cursor: pointer;'><img src='../imagens/icones/icon-table-add.png' title='visualizar registros do lote pendente'/></a></td><td colspan='2'><a href='javascript:;' data-key='" + data.id_header + "'data-projeto='" + id_projeto + "' class='visualizar' style='text-decoration: none; color: #0000CC; font-weight: bold; cursor: pointer;'><img src='../imagens/icones/icon-view.gif' title='visualizar' /></a></td></tr>";

                                            $("#historico_gerado").prepend(html);

                                            
                                            /**
                                             * Adiciona todos os clts selecionados ao lote
                                             */
                                            $("input[name='id_clt[]']:checked").each(function (){
                                                
                                                $.ajax({
                                                    type: "post",
                                                    dataType: "json",
                                                    data: {
                                                        method: "adLote",
                                                        id_header: id_header,
                                                        id_clt: $(this).val()
                                                    },
                                                    error:  function (data) {

                                                        $(".carregando").hide();

                                                    },    
                                                    success: function (data) {

                                                        $("#tot_lote_"+data.id_header).text(data.total);
                                                        $("#clt_count").val(data.total);
                                                        
                                                        percent = parseInt((100*$("#clt_count").val())/$("input[name='id_clt[]']:checked").size());
                                                        
                                                        $("#progressbar").progressbar("value",percent);                                                

                                                        if($("#clt_count").val()==$("input[name='id_clt[]']:checked").size()) {
                                                            
                                                            $("input[name='id_clt[]']:checked").each(function(i,e) {

                                                                  id_clt = $(this).val();

                                                                  $('#tr_clt_'+id_clt).remove();

                                                            });    

                                                            $(".carregando").hide();

                                                            dialog.dialog( "close" );
                                                            
                                                            $("#progressbar").progressbar("value",0);  
                                                            
                                                            $("#filtro-relatorio").show();
                                                            
                                                            $("html,body").animate({scrollTop: $("#historico_gerado").offset().top},"500");    

                                                        }    
                                                        /**
                                                         * Atualizar aqui o contador de registro incluídos no lote
                                                         */
                                                    }
                                                });                                        

                                            });                                

                                        }

                                    }
                                });          

                                
                            }
                            else {
                            
                                $(".carregando").hide();

                            }

                        });
                    });     

                });

                $("body").on("click", "#gerar-lote", function () {
                
                    if($("input[name='id_clt[]']:checked").size()==0) return;

                    var id_header = $(this).data("id_header");

                    $(".carregando").show();

                    $.getJSON('provisao_de_gastos.php?method=getHeaderRescisao&id_header='+id_header, function (data) {  
                        
                        console.log(data);

                        if(data.status){

                            var id_regiao = data.dados_projeto[0].id_header;
                            var id_projeto = data.dados_projeto[0].id_projeto;
                            var dispensa = data.dados_projeto[0].dispensa;
                            var fator = data.dados_projeto[0].fator;
                            var diastrab = data.dados_projeto[0].diastrab;
                            var tipo_dispensa = data.dados_projeto[0].tipo_dispensa;
                            var aviso_previo = data.dados_projeto[0].aviso_previo;
                            var data_aviso_fmt = data.dados_projeto[0].data_aviso;
                            var data_saida_fmt = data.dados_projeto[0].data_saida;
                            var total_participantes = data.dados_projeto[0].total_participantes;
                            var id_clt = 0;
                            var percent = 0;

                            var data_aviso = data.dados_projeto[0].data_aviso.toString().substring(6, 10)+'-'+data.dados_projeto[0].data_aviso.toString().substring(3, 5)+'-'+data.dados_projeto[0].data_aviso.toString().substring(0, 2);
                            var data_saida = data.dados_projeto[0].data_saida.toString().substring(6, 10)+'-'+data.dados_projeto[0].data_saida.toString().substring(3, 5)+'-'+data.dados_projeto[0].data_saida.toString().substring(0, 2);
                            
                            $("#clt_count").val(0);

                            var msg =  "<table>"
                                      +"    <tr>"
                                      +"        <td>Tipo</td>"
                                      +"        <td>"+$("#dispensa option[value="+tipo_dispensa+"]").text()+"</td>"
                                      +"    </tr>"
                                      +"    <tr>"
                                      +"        <td>Fator</td>"
                                      +"        <td>"+fator+"</td>"
                                      +"    <tr>"
                                      +"    <tr>"
                                      +"        <td>Aviso</td>"
                                      +"        <td>"+data_aviso_fmt+"</td>"
                                      +"    <tr>"
                                      +"    <tr>"
                                      +"        <td>Tipo</td>"
                                      +"        <td>"+aviso_previo+"</td>"
                                      +"    <tr>"
                                      +"    <tr>"
                                      +"        <td>Demissão</td>"
                                      +"        <td>"+data_saida_fmt+"</td>"
                                      +"    <tr>"
                                      +"</table>";

                        }    
                        
                        $(".carregando").hide();
                        
                        thickBoxConfirm("Processar novas rescisões do lote", "Deseja processar os "+$("input[name='id_clt[]']:checked").size()+" registros marcados do lote pendente nas condições abaixo?<br><br>"+msg, 500, 350, function (data) {
                            
                            
                            if (data == true) {

                                buttonStartProgressBar.trigger("click");
                                
                                dialog.dialog({title: "Criando rescisões do lote ..."});

                                $(".carregando").show();
                                
                                $("input[name='id_clt[]']:checked").each(function(i,e) {
                                    
                                    id_clt = $(this).val();
                                    
                                    $.ajax({ 
                                        url: '/intranet/rh/recisao/recisao2.php?dispensa='+tipo_dispensa+'&fator='+fator+'&diastrab='+diastrab+'&valor=0,00&faltas=0&aviso='+aviso_previo+'&data_aviso='+data_aviso+'&tela=3&idclt='+id_clt+'&regiao='+id_regiao+'&logado=<?=$_COOKIE['logado']?>&data_demi='+data_saida+'&recisao_coletiva=1&id_header='+id_header, 
                                        type: "get",
                                        dataType: "json",
                                        async: true,
                                        error: function (data){
                                            
                                            $(".carregando").hide();
                                            
                                        },    
                                        success: function (data){
                                            
                                            $("#tot_"+id_header).html(data.total);
                                            
                                            $("#clt_count").val(data.total-total_participantes);
                                            
                                            $("input[name='id_clt[]']:checked").size()
                                            
                                            percent = parseInt((100*$("#clt_count").val())/$("input[name='id_clt[]']:checked").size());

                                            $("#progressbar").progressbar("value",percent);                                                

                                            if($("#clt_count").val()==$("input[name='id_clt[]']:checked").size()) {

                                                dialog.dialog("close");

                                                $("#progressbar").progressbar("value",0);  
                                                  
                                                $("input[name='id_clt[]']:checked").each(function(i,e) {

                                                      id_clt = $(this).val();

                                                      $('#clt_'+id_clt).remove();

                                                });    

                                                $("#filtro-relatorio").show();
                                                
                                                $(".carregando").hide();

                                                $("html,body").animate({scrollTop: $("#historico_gerado").offset().top},"500");    


                                            }    

                                        }    
                                    });                                     


                                });
                                
                            }
                            

                        });

                    });
                    
                    
                });

                $("body").on("click", "#gerar", function () {
                
                    var dados = $("#form").serialize();
                    var id_header = 0;
                    
                    var id_regiao = $("#regiao").val();
                    var id_projeto = $("#projeto").val();
                    var situacao = $("#situacao").val();
                    var dispensa = $("#dispensa").val();
                    var fator = $("#fator").val();
                    var dias_trab = $("#distrab").val();
                    var aviso = $("#aviso").val();
                    var data_aviso = $("#dataAviso").val().toString().substring(6, 10)+'-'+$("#dataAviso").val().toString().substring(3, 5)+'-'+$("#dataAviso").val().toString().substring(0, 2);
                    var data_demi = $("#dataDemi").val().toString().substring(6, 10)+'-'+$("#dataDemi").val().toString().substring(3, 5)+'-'+$("#dataDemi").val().toString().substring(0, 2);
                    
                    $("#projeto_oculto").val("");
                    //if invalid do nothing
                    if (!$("#form").validationEngine('validate')) {
                        return false;
                    }
                    
                    $.getJSON('provisao_de_gastos.php?method=verificaRescisao&'+dados, function (data) {
                    
                        var html = "";
                        var percent = 0;

                        if (data.status == 1) {

                            alert('Já existe rescisão gerada nas mesma condições selecionadas');
                            
                        } else if (data.status == 2) {
                            

                            thickBoxConfirm("Gerar novas rescisões", "Nao foi encontrado nenhuma rescisao com as configuracões selecionadas, deseja criar agora?", 500, 350, function (data) {

                                if (data == true) {
                                    
                                    buttonStartProgressBar.trigger("click"); 

                                    $.getJSON('provisao_de_gastos.php?method=criarHeader&'+dados, function (response) {

                                        id_header = response.id_header;
                                        
                                        $("input[name='id_clt[]']:checked").each(function(i) {
                                            
                                            $.get('/intranet/rh/recisao/recisao2.php?dispensa='+dispensa+'&fator='+fator+'&diastrab='+dias_trab+'&valor=0&faltas=0&aviso='+aviso+'&data_aviso='+data_aviso+'&tela=3&idclt='+$(this).val()+'&regiao='+id_regiao+'&logado=<?=$_COOKIE['logado']?>&data_demi='+data_demi+'&recisao_coletiva=1&id_header='+id_header, function (response) {
                                            })
                                            .done(function() {
                                                console.log("done");
                                            })
                                            .fail(function() {
                                                
                                                dialog.dialog( "close" );
                                                $("#progressbar").progressbar("value",0);  

                                            })
                                            .always(function() {
                                                
                                                $("#clt_count").val(i+1);
                                                
                                                console.log($("input[name='id_clt[]']:checked").size());
                                                console.log($("#clt_count").val());
                                                
                                                percent = parseInt((100*$("#clt_count").val())/$("input[name='id_clt[]']:checked").size());
                                                
                                                $("#progressbar").progressbar("value",percent);                                                
                                                
                                                if($("#clt_count").val()==$("input[name='id_clt[]']:checked").size()) {
                                                    
                                                    $.getJSON('provisao_de_gastos.php?method=getHeaderRescisao&id_header='+id_header, function (data) {

                                                        if (data.status == 1) {
                                                            
                                                            $.each(data.dados_projeto, function (k, v) {
                                                                console.log(v);
                                                                html += "<tr class='tr_" + v.id_header + "'><td>" + v.projeto + "</td><td>" + v.status_situacao + "</td><td>" + v.dispensa + "</td><td>" + v.fator + " </td><td>" + v.data_saida + "</td><td>" + v.aviso_previo + "</td><td>" + v.data_aviso + " </td><td>" + v.criado_por + "</td><td>" + v.criado_em + "</td><td align='center'>" + v.total_participantes + "</td><td colspan='2'><a href='javascript:;' data-key='" + v.id_header + "' class='desprocessar' style='text-decoration: none; color: #0000CC; font-weight: bold; cursor: pointer;'><img src='../imagens/icones/icon-delete.gif' title='desprocessar' /></a></td><td colspan='2'><a href='javascript:;' data-key='" + v.id_header + "'data-projeto='" + v.id_projeto + "' class='visualizar' style='text-decoration: none; color: #0000CC; font-weight: bold; cursor: pointer;'><img src='../imagens/icones/icon-view.gif' title='visualizar' /></a></td></tr>";
                                                            });
                                                            
                                                            $("#historico_gerado").prepend(html);

                                                            $("html,body").animate({scrollTop: $("#historico_gerado").offset().top},"500");    

                                                        }
                                                        
                                                        dialog.dialog( "close" );
                                                        $("#progressbar").progressbar("value",0);  
    
                                                    });                                        
                                                    
                                                }    

                                            });       
                                            
                                        });
                                        
                                    });                                        

                                }


                            });

                        }

                    });
                });

                $("body").on("click", "#id_clt_todos", function () {
                
                    var tudo = $(this).is(":checked");
                    $('.clts').each(function () {
                        var teste_funcao = $(this).closest('tr').css('display') !== 'none';
                        if (tudo && teste_funcao) {
                            $(this).attr("checked", true);
                        } else {
                            console.log('no');
                            $(this).attr("checked", false);
                        }

                    });
                    

//                    var checado = $(this).is(":checked");
//                    var funcao_valida = ($('#'));
//                    if (checado && funcao_valida) {
//                        $(".clts").attr("checked", true);
//                    } else {
//                        $(".clts").attr("checked", false);
//                    }
                });
                
                $("body").on("click", "#id_clt_300", function () {
                
                    var tudo = $(this).is(":checked");
                    
                    $('.clts').each(function (i) {
                        
                        var teste_funcao = $(this).closest('tr').css('display') !== 'none';
                        
                        if(i < 300){
                            if (tudo && teste_funcao) {
                                $(this).attr("checked", true);
                            } else {
                                console.log('no');
                                $(this).attr("checked", false);
                            }
                        }

                    });
                    

//                    var checado = $(this).is(":checked");
//                    var funcao_valida = ($('#'));
//                    if (checado && funcao_valida) {
//                        $(".clts").attr("checked", true);
//                    } else {
//                        $(".clts").attr("checked", false);
//                    }
                });
                

                $("body").on('click', ".xpandir", function () {
                    $(this).removeClass();
                    $(this).addClass("compactar");
                    $(".area-xpandir-1").attr({colspan: "10"});
                    $(".area-xpandir-2").attr({colspan: "30"});
                    $(".area-xpandir-3").attr({colspan: "1"});
                    $(".area-xpandir-4").attr({colspan: "1"});
                    $(".area-xpandir-5").attr({colspan: "1"});
                    $(".area-xpandir-6").attr({colspan: "1"});
                    $(".cabecalho_compactar").attr({colspan: "44"});
                    $(".area").css({display: "block"});
                    $(".esconder").show();
                    if ($('span').hasClass('compactarr') && $('span').hasClass('compactarrr')) {
                        $(".area-xpandir-1").attr({colspan: "10"});
                        $(".area-xpandir-2").attr({colspan: "30"});
                        $(".area-xpandir-3").attr({colspan: "1"});
                        $(".area-xpandir-4").attr({colspan: "17"});
                        $(".area-xpandir-5").attr({colspan: "1"});
                        $(".area-xpandir-6").attr({colspan: "4"});
                        $(".cabecalho_compactar").attr({colspan: "64"});
                    } else if ($('span').hasClass('compactarr')) {
                        $(".area-xpandir-4").attr({colspan: "17"});
                        $(".cabecalho_compactar").attr({colspan: "61"});
                    } else if ($('span').hasClass('compactarrr')) {
                        $(".area-xpandir-4").attr({colspan: "1"});
                        $(".area-xpandir-6").attr({colspan: "3"});
                        $(".cabecalho_compactar").attr({colspan: "47"});
                    }
                });

                $("body").on("click", ".compactar", function () {
                    $(this).removeClass();
                    $(this).addClass("xpandir");
                    $(".area-xpandir-2").attr({colspan: "1"});
                    $(".area-xpandir-3").attr({colspan: "1"});
                    $(".area-xpandir-4").attr({colspan: "1"});
                    $(".area-xpandir-5").attr({colspan: "1"});
                    $(".cabecalho_compactar").attr({colspan: "13"});
                    $(".area").css({display: "none"});
                    $(".esconder").css({display: "none"});
                    if ($('span').hasClass('compactarr') && $('span').hasClass('compactarrr')) {
                        $(".area-xpandir-1").attr({colspan: "10"});
                        $(".area-xpandir-2").attr({colspan: "1"});
                        $(".area-xpandir-3").attr({colspan: "1"});
                        $(".area-xpandir-4").attr({colspan: "17"});
                        $(".area-xpandir-5").attr({colspan: "1"});
                        $(".area-xpandir-6").attr({colspan: "8"});
                        $(".cabecalho_compactar").attr({colspan: "38"});
                    } else if ($('span').hasClass('compactarr')) {
                        $(".area-xpandir-1").attr({colspan: "10"});
                        $(".area-xpandir-2").attr({colspan: "1"});
                        $(".area-xpandir-3").attr({colspan: "1"});
                        $(".area-xpandir-4").attr({colspan: "17"});
                        $(".area-xpandir-5").attr({colspan: "1"});
                        $(".area-xpandir-6").attr({colspan: "1"});
                        $(".cabecalho_compactar").attr({colspan: "30"});
                    } else if ($('span').hasClass('compactarrr')) {
                        $(".area-xpandir-1").attr({colspan: "10"});
                        $(".area-xpandir-2").attr({colspan: "1"});
                        $(".area-xpandir-3").attr({colspan: "1"});
                        $(".area-xpandir-4").attr({colspan: "1"});
                        $(".area-xpandir-5").attr({colspan: "1"});
                        $(".area-xpandir-6").attr({colspan: "2"});
                        $(".cabecalho_compactar").attr({colspan: "16"});
                    }

                });

                $("body").on('click', ".xpandirr", function () {
                    $(this).removeClass();
                    $(this).addClass("compactarr");
                    $(".area-xpandir-1").attr({colspan: "10"});
                    $(".area-xpandir-2").attr({colspan: "1"});
                    $(".area-xpandir-3").attr({colspan: "1"});
                    $(".area-xpandir-4").attr({colspan: "17"});
                    $(".area-xpandir-5").attr({colspan: "1"});
                    $(".area-xpandir-6").attr({colspan: "1"});
                    $(".cabecalho_compactar").attr({colspan: "30"});
                    $(".areaa").css({display: "block"});
                    $(".esconderr").show();

                    if ($('span').hasClass('compactar') && $('span').hasClass('compactarrr')) {
                        $(".area-xpandir-1").attr({colspan: "10"});
                        $(".area-xpandir-2").attr({colspan: "30"});
                        $(".area-xpandir-3").attr({colspan: "1"});
                        $(".area-xpandir-4").attr({colspan: "17"});
                        $(".area-xpandir-5").attr({colspan: "1"});
                        $(".area-xpandir-6").attr({colspan: "4"});
                        $(".cabecalho_compactar").attr({colspan: "64"});
                    } else if ($('span').hasClass('compactar')) {
                        $(".area-xpandir-1").attr({colspan: "10"});
                        $(".area-xpandir-2").attr({colspan: "30"});
                        $(".area-xpandir-3").attr({colspan: "1"});
                        $(".area-xpandir-4").attr({colspan: "17"});
                        $(".area-xpandir-5").attr({colspan: "1"});
                        $(".area-xpandir-6").attr({colspan: "1"});
                        $(".cabecalho_compactar").attr({colspan: "61"});
                    } else if ($('span').hasClass('compactarrr')) {
                        $(".area-xpandir-1").attr({colspan: "10"});
                        $(".area-xpandir-2").attr({colspan: "1"});
                        $(".area-xpandir-3").attr({colspan: "1"});
                        $(".area-xpandir-4").attr({colspan: "17"});
                        $(".area-xpandir-5").attr({colspan: "1"});
                        $(".area-xpandir-6").attr({colspan: "3"});
                        $(".cabecalho_compactar").attr({colspan: "33"});
                    }
                });

                $("body").on("click", ".compactarr", function () {
                    $(this).removeClass();
                    $(this).addClass("xpandirr");
                    if ($('span').hasClass('compactar') && $('span').hasClass('compactarrr')) {
                        $(".area-xpandir-1").attr({colspan: "10"});
                        $(".area-xpandir-2").attr({colspan: "30"});
                        $(".area-xpandir-3").attr({colspan: "1"});
                        $(".area-xpandir-4").attr({colspan: "1"});
                        $(".area-xpandir-5").attr({colspan: "1"});
                        $(".area-xpandir-6").attr({colspan: "3"});
                        $(".cabecalho_compactar").attr({colspan: "47"});
                    } else if ($('span').hasClass('compactar')) {
                        $(".area-xpandir-1").attr({colspan: "10"});
                        $(".area-xpandir-2").attr({colspan: "30"});
                        $(".area-xpandir-3").attr({colspan: "1"});
                        $(".area-xpandir-4").attr({colspan: "1"});
                        $(".area-xpandir-5").attr({colspan: "1"});
                        $(".area-xpandir-6").attr({colspan: "1"});
                        $(".cabecalho_compactar").attr({colspan: "44"});
                    } else if ($('span').hasClass('compactarrr')) {
                        $(".area-xpandir-1").attr({colspan: "10"});
                        $(".area-xpandir-2").attr({colspan: "1"});
                        $(".area-xpandir-3").attr({colspan: "1"});
                        $(".area-xpandir-4").attr({colspan: "1"});
                        $(".area-xpandir-5").attr({colspan: "1"});
                        $(".area-xpandir-6").attr({colspan: "2"});
                        $(".cabecalho_compactar").attr({colspan: "16"});
                    } else {
                        $(".area-xpandir-2").attr({colspan: "1"});
                        $(".area-xpandir-3").attr({colspan: "1"});
                        $(".area-xpandir-4").attr({colspan: "1"});
                        $(".area-xpandir-5").attr({colspan: "1"});
                        $(".area-xpandir-6").attr({colspan: "1"});
                        $(".cabecalho_compactar").attr({colspan: "13"});
                    }

                    $(".areaa").css({display: "none"});
                    $(".esconderr").css({display: "none"});
                });

                $("body").on('click', ".xpandirrr", function () {
                    $(this).removeClass();
                    $(this).addClass("compactarrr");
                    $(".area-xpandir-1").attr({colspan: "10"});
                    $(".area-xpandir-2").attr({colspan: "1"});
                    $(".area-xpandir-3").attr({colspan: "1"});
                    $(".area-xpandir-4").attr({colspan: "1"});
                    $(".area-xpandir-5").attr({colspan: "1"});
                    $(".area-xpandir-6").attr({colspan: "2"});
                    $(".cabecalho_compactar").attr({colspan: "16"});
                    $(".areaaa").css({display: "block"});
                    $(".esconderrr").show();
                    if ($('span').hasClass('compactar') && $('span').hasClass('compactarr')) {
                        $(".area-xpandir-1").attr({colspan: "10"});
                        $(".area-xpandir-2").attr({colspan: "30"});
                        $(".area-xpandir-3").attr({colspan: "1"});
                        $(".area-xpandir-4").attr({colspan: "17"});
                        $(".area-xpandir-5").attr({colspan: "1"});
                        $(".area-xpandir-6").attr({colspan: "4"});
                        $(".cabecalho_compactar").attr({colspan: "64"});
                    } else if ($('span').hasClass('compactar')) {
                        $(".area-xpandir-1").attr({colspan: "10"});
                        $(".area-xpandir-2").attr({colspan: "30"});
                        $(".area-xpandir-3").attr({colspan: "1"});
                        $(".area-xpandir-4").attr({colspan: "1"});
                        $(".area-xpandir-5").attr({colspan: "1"});
                        $(".area-xpandir-6").attr({colspan: "3"});
                        $(".cabecalho_compactar").attr({colspan: "47"});
                    } else if ($('span').hasClass('compactarr')) {
                        $(".area-xpandir-1").attr({colspan: "10"});
                        $(".area-xpandir-2").attr({colspan: "1"});
                        $(".area-xpandir-3").attr({colspan: "1"});
                        $(".area-xpandir-4").attr({colspan: "17"});
                        $(".area-xpandir-5").attr({colspan: "1"});
                        $(".area-xpandir-6").attr({colspan: "3"});
                        $(".cabecalho_compactar").attr({colspan: "33"});
                    }
                });

                $("body").on("click", ".compactarrr", function () {
                    $(this).removeClass();
                    $(this).addClass("xpandirrr");
                    $(".area-xpandir-1").attr({colspan: "10"});
                    $(".area-xpandir-2").attr({colspan: "1"});
                    $(".area-xpandir-3").attr({colspan: "1"});
                    $(".area-xpandir-4").attr({colspan: "1"});
                    $(".area-xpandir-5").attr({colspan: "1"});
                    $(".area-xpandir-6").attr({colspan: "1"});
                    $(".cabecalho_compactar").attr({colspan: "13"});

                    if ($('span').hasClass('compactar') && $('span').hasClass('compactarr')) {
                        $(".area-xpandir-1").attr({colspan: "10"});
                        $(".area-xpandir-2").attr({colspan: "30"});
                        $(".area-xpandir-3").attr({colspan: "1"});
                        $(".area-xpandir-4").attr({colspan: "17"});
                        $(".area-xpandir-5").attr({colspan: "1"});
                        $(".area-xpandir-6").attr({colspan: "1"});
                        $(".cabecalho_compactar").attr({colspan: "61"});
                    } else if ($('span').hasClass('compactar')) {
                        $(".area-xpandir-1").attr({colspan: "10"});
                        $(".area-xpandir-2").attr({colspan: "30"});
                        $(".area-xpandir-3").attr({colspan: "1"});
                        $(".area-xpandir-4").attr({colspan: "1"});
                        $(".area-xpandir-5").attr({colspan: "1"});
                        $(".area-xpandir-6").attr({colspan: "1"});
                        $(".cabecalho_compactar").attr({colspan: "44"});
                    } else if ($('span').hasClass('compactarr')) {
                        $(".area-xpandir-1").attr({colspan: "10"});
                        $(".area-xpandir-2").attr({colspan: "1"});
                        $(".area-xpandir-3").attr({colspan: "1"});
                        $(".area-xpandir-4").attr({colspan: "17"});
                        $(".area-xpandir-5").attr({colspan: "1"});
                        $(".area-xpandir-6").attr({colspan: "1"});
                        $(".cabecalho_compactar").attr({colspan: "30"});
                    }

                    $(".areaaa").css({display: "none"});
                    $(".esconderrr").css({display: "none"});
                });

                $("#dispensa").change(function () {
                    var tipo = $(this).val();
                    if (tipo == 61 || tipo == 65) {
                        $("#diasIndOuTrab").removeAttr("disabled");
                        $("#aviso").removeAttr("disabled");
                        $("#dataAviso").removeAttr("disabled");
                    } else {
                        $("#diasIndOuTrab").attr({disabled: "disabled"});
                        $("#aviso").attr({disabled: "disabled"});
                        $("#dataAviso").attr({disabled: "disabled"});
                    }
                }).trigger("change");

                $("body").on("click", ".desprocessar", function () {
                    var header = $(this).data("key");
                    thickBoxConfirm("Desprocessar rescisões", "Deseja realmente desprocessar?", 500, 350, function (data) {
                        if (data == true) {
                            $.ajax({
                                type: "post",
                                dataType: "json",
                                data: {
                                    method: "desprocessarRecisao",
                                    header: header
                                },
                                success: function (data) {
                                    console.log(data.status);
                                    if(data.status==2) {
                                        alert("Exclusao nao permitida pois existem rescisões efetivadas");
                                    }    
                                    else {
                                        $(".tr_" + header).remove();
                                    }    
                                }
                            });
                        }
                    });

                    $("#lista_funcionarios").html("");
                });
                
                $('#confirmar_rescisao1').click(function () {
                    thickBoxConfirm("Confirmar Rescisao", "Voce deseja realmente realizar esta rescisao?", 300, 200, function (data) {
                    $("#regiao").removeClass('validate[required, custom[select]]');
                    $("#projeto").removeClass('validate[required, custom[select]]');
                        
                        if (data) {
                            if (data == true) {
                                $('#confirmar_rescisao').trigger('click');
                            }
                        }
                    });
                })
                
                $("#aviso").change(function () {
                    
                    define_condicoes_rescisorias();
                
                });
                

                $("#movimento").change(function () {
                    
                    var movimento = $("#movimento :selected").text();
                    
                    $("#nome_movimento").val(movimento);
                    
                });

                $("#movimento_lote").change(function () {
                    
                    var movimento = $("#movimento_lote :selected").text();
                    
                    $("#nome_movimento_lote").val(movimento);
                    
                });
                
                
                
                $(".lista_recolhimento_fgts").click(function () {
                    
                    var id_clt = $(this).data("id_clt");
                    var nome = $(this).data("nome");
                    var saldo_salario = parseFloat($(this).data("saldo_salario"));
                    var terceiro_ss = parseFloat($(this).data("terceiro_ss"));
                    var dt_salario = parseFloat($(this).data("dt_salario"));
                    var lei_12_506 = parseFloat($(this).data("lei_12_506"));
                    var aviso = parseFloat($(this).data("aviso"));
                    var total_movimentos_incide_fgts = parseFloat($(this).data("total_movimentos_incide_fgts"));
                    var valor_faltas = parseFloat($(this).data("valor_faltas"));
                    var movimento_falta = parseFloat($(this).data("movimento_falta"));
                    
                    $("#recolhimento_fgts").show();
                    $("#dados_historico_fgts").html('Carregando ...');
                    
                    thickBoxModal(id_clt + ' - ' + nome, "#recolhimento_fgts", 920, 700);


                    $.ajax({
                        type: "post",
                        dataType: "json",
                        data: {
                            method: "lista_recolhimento_fgts",
                            id_clt: id_clt

                        },
                        success: function (data) {
                            
                            console.log(data);
                            
                            if (data.status) {
                                
                                var html = "";
                                var td = "";
                                var ano = 0;
                                var rowspan = 0;
                                var count_mes = 0;
                                var sum_fgts = 0;
                                
                                html += "<table id='tab_movimentos' border='0' cellpadding='0' cellspacing='0' class='grid' width='100%'>";
                                html += "<thead><tr><td>ANO</td><td>MES</td><td style='width:60px'>FGTS</td></tr></thead><tbody>";
                                
                                $.each(data.dados, function (k, v) {
                                    
                                    if(v.ano!=ano) {
                                        
                                        ano = v.ano;
                                        rowspan = v.rowspan;                  
                                        td = "<td rowspan='" + rowspan + "'>" + v.ano + "</td>" ;
                                        
                                    }    
                                    else {
                                        
                                        rowspan = 0;
                                        td = "";
                                    }    
                                    
                                    count_mes++;
                                    sum_fgts+=parseFloat(v.fgts);
                                    
                                    html += "<tr style='height: 46px;'>" + td + "<td>" + v.mes + "</td><td style='text-align: right;'> " + v.fgts + " </td></tr>";
                                    
                                });
                                
                                html += "</tbody><tfoot>";
                                html += "<tr><td colspan='2' style='text-align: right;'>Foram " + count_mes + " meses de recolhimento no valor total de</td><td style='width:60px; text-align: right;'>" + sum_fgts.toFixed(2)+ "</td></tr>";
                                html += "</tfoot></table><br/>";
                                
//                                html += "<table id='tab_movimentos_2' border='0' cellpadding='0' cellspacing='0' class='grid' width='100%'>";
//                                html += "<thead><tr><td>DESCRIÇÃO</td><td>CRÉDITO</td><td style='width:60px'>DÉBITO</td></tr></thead><tbody>";
//                                html += "<tr><td style='text-align: right;'>Saldo de Salário</td><td style='width:60px; text-align: right;'>" + saldo_salario.toFixed(2) + "</td><td style='width:60px; text-align: right;'></td></tr>";
//                                html += "<tr><td style='text-align: right;'>Saldo de Salário de 13º</td><td style='width:60px; text-align: right;'>" + terceiro_ss.toFixed(2) + "</td><td style='width:60px; text-align: right;'></td></tr>";
//                                html += "<tr><td style='text-align: right;'>13º Salário</td><td style='width:60px; text-align: right;'>" + dt_salario.toFixed(2) + "</td><td style='width:60px; text-align: right;'></td></tr>";
//                                html += "<tr><td style='text-align: right;'>Lei 12.506</td><td style='width:60px; text-align: right;'>" + lei_12_506.toFixed(2) + "</td><td style='width:60px; text-align: right;'></td></tr>";
//                                html += "<tr><td style='text-align: right;'>Aviso</td><td style='width:60px; text-align: right;'>" + aviso.toFixed(2) + "</td><td style='width:60px; text-align: right;'></td></tr>";
//                                html += "<tr><td style='text-align: right;'>Movimentos com Incidência de FGTS</td><td style='width:60px; text-align: right;'>" + total_movimentos_incide_fgts.toFixed(2) + "</td><td style='width:60px; text-align: right;'></td></tr>";
//                                html += "<tr><td style='text-align: right;'>Desconto Por Faltas</td><td style='width:60px; text-align: right;'></td><td style='width:60px; text-align: right;'>" + valor_faltas.toFixed(2) + "</td></tr>";
//                                html += "<tr><td style='text-align: right;'>Desconto Por Faltas em Movimentos</td><td style='width:60px; text-align: right;'></td><td style='width:60px; text-align: right;'>" + movimento_falta.toFixed(2) + "</td></tr>";
//                                
//                                html += "</tbody><tfoot>";
//                                html += "<tr><td style='text-align: right;'>Total</td><td style='width:60px; text-align: right;'>" + (saldo_salario+terceiro_ss+dt_salario+lei_12_506+aviso).toFixed(2) + "</td><td style='width:60px; text-align: right;'>" + (valor_faltas+movimento_falta).toFixed(2) + "</td></tr>";
//                                html += "</tfoot></table><br/>";
//                                
//                                html += "<table id='tab_movimentos_2' border='0' cellpadding='0' cellspacing='0' class='grid' width='100%'>";
//                                html += "<thead><tr><td>RETENÇÃO</td><td>RESCISÃO</td><td style='width:60px'>GRRF</td></tr></thead><tbody>";
//                                html += "<tr><td style='text-align: right;'>" + sum_fgts.toFixed(2) + "</td><td style='text-align: right;'>" + (saldo_salario+terceiro_ss+dt_salario+lei_12_506+aviso)-(valor_faltas+movimento_falta) + "</td><td style='width:60px; text-align: right;'>" + ((sum_fgts+saldo_salario+terceiro_ss+dt_salario+lei_12_506+aviso)-(valor_faltas+movimento_falta)*0.5) + "</td></tr>";
//                                html += "</tbody><tfoot>";
//                                html += "</tfoot></table><br/>";
                                
                                $("#dados_historico_fgts").html(html);
                            }

                        }
                    });

                });
                
                
                $(".lanca_movimento").click(function() {
                
                    var id_rescisao_lote = $(this).data("id_rescisao_lote");
                    var id_rescisao = $(this).data("id_rescisao");
                    var id_clt = $(this).data("id_clt");
                    
                    $("#id_rescisao_lote").val(id_rescisao_lote);
                    $("#id_rescisao").val(id_rescisao);
                    $("#id_clt").val(id_clt);
                    
                    $("#lancamento_mov").show();
                    
                    thickBoxModal("Lancamento de movimentos", "#lancamento_mov", 920, 700);

//                    $("body").on("click", ".ui-icon-closethick", function () {
//                        
//                        location.reload();
//                        
//                    });

                    $.ajax({
                        type: "post",
                        dataType: "json",
                        data: {
                            method: "carrega_movimentos",
                            rescisao: id_rescisao

                        },
                        success: function (data) {
                            if (data) {
                                var html = "";
                                
                                html += "<table id='tab_movimentos' border='0' cellpadding='0' cellspacing='0' class='grid' width='100%'>";
                                html += "<thead><tr><td>COD</td><td>NOME</td><td>TIPO</td><td style='width:200px'>VALOR</td><td colspan='2'>ACOES</td></tr></thead><tbody>";
                                
                                $.each(data.dados, function (k, v) {
                                    
                                    html += "<tr style='height: 46px;' class='tr_" + v.id_mov + "'><td>" + v.id_movimento + "</td><td> " + v.nome_movimento + " </td><td> " + v.tipo + " </td><td><span class='valor_" + v.id_mov + "'> " + v.valor + " </span></td><td><a href='javascript:;' class='editar_valor movimento_" + v.id_mov + "' data-movimento='" + v.id_mov + "' data-valor='" + v.valor + "'><img src='../imagens/icones/icon-edit.gif' title='Editar Valor' /></a></td><td><a href='javascript:;' class='remover_valor' data-movimento='" + v.id_mov + "' data-valor='" + v.valor + "'><img src='../imagens/icones/icon-delete.gif' title='Deletar Valor' /></a></td></tr>";
                                    
                                });
                                
                                html += "</tbody></table>";
                                
                                $("#dados_historico").html(html);
                            }

                        }
                    });

                });
                
                $("body").on("click", "#lanca_movimento_lote", function () {
                
                    var id_rescisao_lote =  $(this).data("id_rescisao_lote");
                    
                    var chk = false;
                    
                    $("#id_rescisao_mov_lote").val(id_rescisao_lote);
                    
                    $("#lancamento_mov_lote").show();
                    
                    thickBoxModal("Lancamento de movimentos em lote", "#lancamento_mov_lote", 920, 700);

                    $.ajax({
                        type: "post",
                        dataType: "json",
                        data: {
                            id_rescisao_lote: id_rescisao_lote,
                            method: "carrega_movimentos_lote"

                        },
                        success: function (data) {
                            
                            var id = "";
                            
                            console.log(data);
                            
                            if (data.status) {
                                
                                var html = "";
                                
                                html += "<table id='tab_movimentos_lote' border='0' cellpadding='0' cellspacing='0' class='grid' width='100%'>";
                                html += "<thead><tr><td>COD</td><td>NOME</td><td>TIPO</td><td style='width:200px'>MÉDIA</td><td colspan='2'>ACOES</td></tr></thead><tbody>";
                                
                                $.each(data.dados, function (k, v) {
                                    
                                    id = k;

                                    html += "<tr style='height: 46px;' class='tr_" + id + "'><td>" + v.id_movimento + "</td><td> " + v.nome_movimento + " </td><td> " + v.tipo + " </td><td><span class='valor_" + id+ "'> " + v.valor + " </span></td><td><a href='javascript:;' class='editar_valor_lote movimento_lote_" + id + "' data-id='" + id + "' data-id_movimento='" + v.id_movimento + "' data-id_rescisao_lote='" + v.id_rescisao_lote + "' data-valor='" + v.valor + "'><img src='../imagens/icones/icon-edit.gif' title='Editar Valor' /></a></td><td><a href='javascript:;' class='remover_valor_lote' data-id='" + id + "' data-id_movimento='" + v.id_movimento + "' data-id_rescisao_lote='" + v.id_rescisao_lote + "' data-valor='" + v.valor + "'><img src='../imagens/icones/icon-delete.gif' title='Deletar Valor' /></a></td></tr>";
                                    
                                });
                                
                                html += "</tbody></table>";
                                
                                $("#dados_historico_lote").html(html);
                                
                            }

                        }
                    });

                });
                

                $("body").on("click", ".remover_valor", function () {
                    var movimento = $(this).data("movimento");
                    thickBoxConfirm("Remover Movimento", "Deseja realmente Remover esse movimento?", 500, 350, function (data) {
                        if (data == true) {
                            $.ajax({
                                type: "post",
                                dataType: "json",
                                data: {
                                    method: "removerMovimento",
                                    movimento: movimento
                                },
                                success: function (data) {
                                    
                                    $(".tr_" + movimento).remove();
                                    
                                }
                            });
                        }
                    });

                });
                
                $("body").on("click", ".remover_valor_lote", function () {
                    
                    var id = $(this).data("id");
                    var id_rescisao_lote = $(this).data("id_rescisao_lote");
                    var id_movimento = $(this).data("id_movimento");
                    var id_mov = $(this).data("id_mov");
                    var valor = $(this).data("valor");
                    
                    thickBoxConfirm("Remover Movimento", "Deseja realmente Remover esse movimento de todos do lote?", 500, 350, function (data) {
                        if (data == true) {
                            $.ajax({
                                type: "post",
                                dataType: "json",
                                data: {
                                    method: "removerMovimentoLote",
                                    id_rescisao_lote: id_rescisao_lote,
                                    id_movimento: id_movimento,
                                    valor: valor
                                },
                                success: function (data) {
                                    
                                    console.log(id);
                                    
                                    $(".tr_" + id).remove();
                                    
                                }
                            });
                        }
                    });

                });
                

                $("body").on("click", ".editar_valor", function () {
                    $(".valor_mov_edit").hide();
                    var movimento = $(this).data("movimento");
                    var valor_movimento = $(this).attr("data-valor");
                    $(".valor_" + movimento).html("<input type='text' name='valor_mov_edit' class='valor_mov_edit' data-mov_input='" + movimento + "' value='" + valor_movimento + "' class='input_edit' />");
                });
                
                $("body").on("click", ".editar_valor_lote", function () {
                    
                    $(".valor_mov_edit").hide();
                    
                    var id = $(this).data("id");
                    
                    var id_rescisao_lote = $(this).data("id_rescisao_lote");
                    
                    var id_movimento = $(this).data("id_movimento");
                    
                    var valor_old = $(this).data("valor");
                    
                    $(".valor_" + id).html("<input type='text' name='valor_mov_edit' class='valor_mov_edit' data-id_rescisao_lote='" + id_rescisao_lote + "' data-id_movimento='" + id_movimento + "' data-valor_old='" + valor_old + "' value='" + valor_old + "' class='input_edit' />");
                    
                });
                

                $("body").on("blur", ".valor_mov_edit", function () {
    
                    var id = $(this).data("id");
                    
                    var id_rescisao_lote = $(this).data("id_rescisao_lote");

                    var id_movimento = $(this).data("id_movimento");

                    var valor_old = $(this).data("valor_old");
                    
                    var valor_new = $(this).val();
                    
                    console.log('id_rescisao_lote = '+id_rescisao_lote);
                    
                    
                    
                    $.ajax({
                        type: "post",
                        dataType: "json",
                        data: {
                            method: "atualizaValorMovimentoLote",
                            id_rescisao_lote: id_rescisao_lote,
                            id_movimento: id_movimento,
                            valor_old: valor_old,
                            valor_new: valor_new
                        },
                        success: function (data) {
                            
                            if (data.status) {
                                
                                $(".mensagem").html("<span class='vermelho'>Movimento atualizado com sucesso</span>");
                                
                            }
                            
                        }
                    });
                    
                    $(".valor_" + id).text(valor_new);
                    
                    $(".movimento_" + id).attr({"valor": valor_new});
                    
                });

                $("#cadastrar_mov").click(function () {

                    var id_clt = $("#id_clt").val();
                    var id_rescisao_lote = $("#id_rescisao_lote").val();
                    var id_rescisao = $("#id_rescisao").val();
                    var movimento = $("#movimento").val();
                    var valor_movimento = $("#valor_movimento").val();
                    var html = "";
                    
                    $("#valor_movimento").val("");
                    
                    $.ajax({
                        url: "provisao_de_gastos.php",
                        type: "post",
                        dataType: "json",
                        data: {
                            method: "cadastraMovimentos",
                            movimento: movimento,
                            id_clt: id_clt,
                            id_rescisao_lote: id_rescisao_lote,
                            id_rescisao: id_rescisao,
                            valor_movimento: valor_movimento
                        },
                        success: function (data) {
                            if (data.status) {
                                
                                $(".mensagem").html("<span class='vermelho'>Movimento cadastrado com sucesso</span>");
                                
                                $.each(data.dados, function (k, v) {
                                    html += "<tr class='tr_" + v.id_mov + "'>";
                                    html += "<td>" + v.id_movimento + "</td><td> " + v.nome_movimento + " </td><td>" + v.tipo + "</td><td><span class='valor_" + v.id_mov + "'> " + v.valor + " </span></td><td><a href='javascript:;' class='editar_valor movimento_" + v.id_mov + "' data-movimento='" + v.id_mov + "' data-valor='" + v.valor + "'><img src='../imagens/icones/icon-edit.gif' title='Editar Valor' /></a></td><td><a href='javascript:;' class='remover_valor' data-movimento='" + v.id_mov + "' data-valor='" + v.valor + "'><img src='../imagens/icones/icon-delete.gif' title='Deletar Valor' /></a></td>";
                                    html += "</tr>";
                                });

                                $("#tab_movimentos").append(html);
                            }
                        }
                    });
                });
                
                $("#cadastrar_mov_lote").click(function () {
                
                    var chk = 0; 
                    var id_rescisao_lote = $("#id_rescisao_mov_lote").val();
                    var movimento = $("#movimento_lote").val();
                    var valor_movimento = $("#valor_movimento_lote").val();
                
                    $('.clts').each(function () {
                        
                        chk = chk || $(this).is(":checked");
                            
                    });
                   
                    if(!chk) {
                        
                        thickBoxAlert("Lote não selecionado", "Você precisa de selecionar ao menos um elemento do lote para fazer o lançamento", 500, 200, function (data) {
                            
                            thickBoxClose("#lancamento_mov_lote");
                            
                            return;
                            
                        });
                        
                    }    
                    
                    $('.clts').each(function () {
                        
                        if($(this).is(":checked")){

                            var id_clt = $(this).val();
                            var id_recisao = $(this).data("id_recisao");

                            $.ajax({
                                url: "provisao_de_gastos.php",
                                type: "post",
                                dataType: "json",
                                data: {
                                    method: "cadastraMovimentosLote",
                                    id_rescisao_lote: id_rescisao_lote,
                                    id_rescisao: id_recisao,
                                    id_clt: id_clt,
                                    movimento: movimento,
                                    valor_movimento: valor_movimento
                                },
                                success: function (data) {

                                    console.log(data);

                                    if (data.status) {

                                        $(".mensagem").html("<span class='vermelho'>Movimento de lote cadastrado com sucesso</span>");

                                        var html = "";

                                        html += "<table id='tab_movimentos_lote' border='0' cellpadding='0' cellspacing='0' class='grid' width='100%'>";
                                        html += "<thead><tr><td>COD</td><td>NOME</td><td>TIPO</td><td style='width:200px'>MÉDIA</td><td colspan='2'>ACOES</td></tr></thead><tbody>";

                                        $.each(data.dados, function (k, v) {

                                            console.log(data.dados);

                                            id = k;

                                            html += "<tr class='tr_" + id + "'>";
                                            html += "<td>" + v.id_movimento + "</td><td> " + v.nome_movimento + " </td><td>" + v.tipo + "</td><td><span class='valor_" + id + "'> " + v.valor + " </span></td><td><a href='javascript:;' class='editar_valor_lote movimento_" + id + "' data-id='" + id + "' data-id_rescisao_lote='" + v.id_rescisao_lote + "' data-id_movimento='" + v.id_movimento + "' data-valor='" + v.valor + "'><img src='../imagens/icones/icon-edit.gif' title='Editar Valor' /></a></td><td><a href='javascript:;' class='remover_valor_lote' data-id='" + id + "' data-id_rescisao_lote='" + v.id_rescisao_lote + "' data-id_movimento='" + v.id_movimento + "' data-valor='" + v.valor + "'><img src='../imagens/icones/icon-delete.gif' title='Deletar Valor' /></a></td>";
                                            html += "</tr>";

                                        });

                                        $("#dados_historico_lote").html(html);
                                    }

                                }
                            });

                        }
                    
                    });    
                        
                });
                
                
                $('body').on('click',"#mostrar_rescisao,#mostrar_prov_trab",function(){
                
                    $("#regiao").removeClass('validate[required, custom[select]]');
                    
                    $("#projeto").removeClass('validate[required, custom[select]]');
                    
                    $("#form").submit();
                    
                });
                

                // download do excel -------------------------------------------
//                $(".exportarExcel").click(function(){
////                    $("#form").attr("action","provisao_de_gastos_xls_generator.php");
//                    $("#form").submit();
//                });

            });

            // MULTI SELECT
            var expanded = false;
            function showCheckboxes() {
                var checkboxes = document.getElementById("checkboxes");
                if (!expanded) {
                    checkboxes.style.display = "block";
                    expanded = true;
                } else {
                    checkboxes.style.display = "none";
                    expanded = false;
                }
            }
            // FIM MULTI SELECT


        </script>
        <style>

            .input_edit{
                height: 19px;
                width: 46px;
                box-sizing: border-box;
                padding: 3px;
            }

            #total_anos{
                display: block;
                margin-top: 555px;
                margin-left: 10px;
                text-align: right;
                margin-right: 10px;
            }
            #total_anos p{
                font-family: arial;
                color: #333;
                font-size: 15px;
            }
            #total_anos span{
                font-weight: bold;
            }
            #fgts_folha{
                display: none;
            }
            
            .class_button{
                padding: 5px;
                display: inline;
            }
           
            #mostrar_rescisao, #mostrar_prov_trab {
                float: right;
            }
            
            .lista_fgts{
                border: 1px solid #ccc;
                padding: 5px;
                width: 207px;
                height: 535px;
                float: left;
                margin: 0px 10px;
                box-sizing: border-box;
            }
            .lista_fgts h3{
                border-bottom: 3px solid #333;
            }
            .lista_fgts h2{
                font-size: 16px;
                text-align: right;
                margin: 0px;
                background: #F5F3F3;
                width: 100%;
                padding: 5px;
                box-sizing: border-box;
            }
            .lista_fgts p{
                border-bottom: 1px dotted #ccc;
            }
            .header{
                font-weight: bold;
                background: #F3F3F3 !important;
                font-size: 11px !important;
                color: #333;
            }
            .footer{
                font-weight: bold;
                background: #F3F3F3;
            }

            .totalizador{
                border: 1px solid #ccc;
                padding: 5px;
                margin: 10px 10px;
                width: 347px;
                height: 424px;
                background: #f3f3f3;
                float: left;
            }
            .totalizador p{
                border-bottom: 1px dotted #ccc;
                padding-bottom: 2px;
            }
            .totalizador span{
                font-weight: bold;
                float: right;
            }
            .semborda{
                border: 0px !important;
            }
            .titulo{
                font-weight: bold;
                color: #000;
                text-align: center;
                font-size: 14px;
                margin: 5px 0px 20px 0px;
                border: 2px solid #B1A8A8 !important;
                padding: 1px 0px;
                background: #DFDFDF;
                height: 35px;
            }
            .compactar, .compactarr, .compactarrr, .xpandir, .compactarr, .xpandirr, .xpandirrr{
                float: right;
                font-family: verdana;
                font-size: 10px;
                font-weight: bold;
                color: #CA1E17;
                text-transform: uppercase;
                cursor: pointer;
            }

            .compactar:before, .compactarr:before, .compactarrr:before{
                content: " -";
                background: #1D1A1A;
                border-radius: 65%;
                padding: 1px 5px;
                font-weight: bold;
                color: #fff;
                margin-right: 5px;
            }

            .xpandir:before, .xpandirr:before, .xpandirrr:before{
                content: " +";
                background: #1D1A1A;
                border-radius: 65%;
                padding: 1px 3px;
                font-weight: bold;
                color: #fff;
                margin-right: 5px;
            }

            .esconder, .esconderr, .esconderrr{
                display: none;
            }

            .area, .areaa, .areaaa{
                border: 2px solid;
                height: 16px;
                width: 99%;
                margin-left: 5px;
                border-bottom: 0px;
                display: none;
            }

            .box{
                border: 0px solid #ccc;
                padding: 10px;
                box-sizing: border-box;
                margin: 5px;
                width: 1150px;
            }
            .col-esq, .col-dir{
                float: left;
                margin: 0px 0px;
                width: 540px;
            }

            .col-esq label, .col-dir label{
                width: 200px !important;
            }

            .inputPequeno{
                width: 324px;
                height: 27px;
                padding: 10px;
            }

            .selectPequeno{
                width: 324px;
                height: 28px;
                padding: 0px;
            }
        
            .carregando{
                width:100px;
                height:100px;
                position:fixed;
                top:50%;
                left:50%;
                margin-top:-50px;
                margin-left:-50px;
                opacity: 0.9;
                display: none;
            }
            .carregando img{
                width:100px;
                height:100px;
            }
/*            .carregando .box-message{
                position: absolute;
                background: #F8F8F8;
                padding: 15px;
                box-sizing: border-box;
                box-shadow: 5px 5px 80px #333;
            }
            .carregando .box-message p{
                font-family: arial;
                font-size: 14px;
                color: #333;
                font-weight: bold;
                text-align: center;
            }*/

            .historico{
                /*height: 436px;*/
                overflow: auto;
            }

            th > span{
                font-weight: bold !important;
                margin-right: 5px;
                color: #888;
            }

            th{
                font-weight: 500 !important;
                font-size: 12px !important; 
                text-transform: uppercase;
            }
            
            #recolhimento_fgts {
                display: none;
            }            

            .lancamento {
                display: none;
            }
            

            .lancamento label{
                display: block;
                margin: 5px 0px;
                text-align: left;
                width: 200px;
                text-transform: uppercase;
                font-size: 11px;
                color: #333;
            }

            .lancamento input[type='text']{
                width: 90px;
                padding: 5px;
            }

            .lancamento input[type='button']{
                width: 160px;
                padding: 9px;
                background: #f1f1f1;
                border: 1px solid #ccc;
                font-weight: bold;
                cursor: pointer;
            }

            .lancamento input[type='button']:hover{
                color: #999;
            }

            #box-1{
                box-sizing: border-box;
                padding: 15px 0px;
            }

            .lancamento fieldset{
                border: 0px;
                margin-left: 20px;
            }
            .descricao_box{
                font-family: arial;
                font-size: 14px;
                color: #666;
                text-transform: uppercase;
                border-bottom: 1px dotted #ccc;
                width: 670px;
                padding-bottom: 5px;
            }
            .texto_pequeno{
                font-size: 11px !important;
                text-transform: uppercase !important;
            }

            .vermelho{
                color: red;
            }

            #tab_movimentos td{
                padding: 8px !important;
            }


            /* MULTISELECT */
            .multiselect {
                display: inline-block;
                width: 400px;
            }
            .multiselect select{
                padding: 5px;
            }
            .selectBox {
                position: relative;
            }
            #filtro_funcao{
                padding: 5px;
            }
            #gerar {
                padding: 5px;
            }
            #criar-lote {
                padding: 5px;
            }
            
            #gerar-lote {
                padding: 5px;
            }
            
            .selectBox select {
                width: 100%;
                font-weight: bold;
            }
            .overSelect {
                position: absolute;
                left: 0; right: 0; top: 0; bottom: 0;
            }
            #checkboxes {
                overflow: auto;
                max-height: 300px;
                width: 400px;
                position: absolute;
                display: none;
                border: 1px #dadada solid;
                z-index: 100;
                background-color: #FFF;
            }
            #checkboxes label {
                display: block;
                text-align:left;
            }
            #checkboxes label:hover {
                background-color: #1e90ff;
            }
            
            .noscroll{overflow:hidden;}
            /* FIM MULTISELECT */
            
            #buttonStartProgressBar {
                display: none;
            }
            
            #dialog {
                display: none;
            }
            
            #progressbar {
                margin-top: 20px;
            }

            .progress-label {
              font-weight: bold;
              text-shadow: 1px 1px 0 #fff;
            }
            
            .count {
                text-align: justify;
            }
            
            .ui-datepicker {
                width: 318px;
            }
            
        </style>

    </head>
    <body class="novaintra" >
        
        <nav>
        </nav>
        <article>  
            <div id="dialog" title="Gerando ...">
              <div class="progress-label">Iniciando...</div>
              <div id="progressbar"></div>
            </div>  
            <button id="buttonStartProgressBar">Gerar Rescisão</button>            
            <div class="carregando">
                <div class="box-message">
                    <img src="../imagens/loading2.gif" />
                </div>
            </div>
            <div id="fgts_folha">
            </div>
            <div id="content" style="width: 1150px; display: table;">
               
                <?= $formImprimir ?>
                <form  name="form" action="" method="post" id="form" accept-charset="iso-8859-1">
                    <div id="head">
                        <img src="../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                        <div class="fleft">
                            <h2><?=$title?></h2>
                        </div>
                    </div>
                    <br class="clear">
                    <br/>
                    <div id="filtro-relatorio">
                        <fieldset class="noprint">
                            <legend>Relatório</legend>
                            <div class="fleft">
                                <div class="box"> 
                                    <div class="col-esq">
                                        <input type="hidden" name="hide_projeto" id="hide_projeto" value="<?php echo $projetoSel ?>" />
                                        <input type="hidden" name="hide_funcao" id="hide_funcao" value="<?php echo $funcaoSel ?>" />
                                        <input type="hidden" id="clt_count" name="clt_count" value="0"/>                            

                                        <p style="display: none;">
                                            <label class="first">Regiao:</label>
                                            <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao', 'class' => 'selectPequeno validate[required, custom[select]]')); ?> 
                                        </p>                        
                                        <p>
                                            <label class="first">Projeto:</label>
                                            <?php echo montaSelect(array("-1" => "« Selecione a Regiao »"), $projetoSel , array('name' => "projeto", 'id' => 'projeto', 'class' => 'selectPequeno validate[required, custom[select]]')); ?>
                                        </p>
                                        <p>
                                            <label class="first">Situação:</label>
                                            <?php echo montaSelect($situacao, $situacaoSel, array('name' => "situacao", 'id' => 'situacao', 'class' => 'selectPequeno validate[required, custom[select]]')); ?>
                                        </p>
                                        <p align="center">
                                            <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>
                                            <input type="hidden" name="projeto_oculto" id="projeto_oculto" />
                                            <input type="button" name="visualizar_participantes" value="Visualizar Participantes" id="visualizar_participantes" class="class_button"/>
                                        </p>


                                    </div>
                                    <div class="col-dir"> 
                                        <p id="p_tipo_dispensa">
                                            <label class="first">Tipo de Dispensa: </label>
                                            <?php echo montaSelect($dispensa, $dispensaSel=='' ? 61 : $dispensaSel, array('name' => "dispensa", 'id' => 'dispensa', 'class' => 'selectPequeno validate[required, custom[select]]', 'disabled' => 'disabled')); ?>
                                        </p>
                                        <p id="p_empregador">
                                            <label class="first">Fator:</label>
                                            <?php echo montaSelect($fator, $fatorSel=='' ? 'empregador' : $fatorSel, array('name' => "fator", 'id' => 'fator', 'class' => 'selectPequeno validate[required, custom[select]]', 'disabled' => 'disabled')); ?>
                                        </p>
                                        <p id="p_data_aviso">
                                            <label class="first">Data do Aviso:</label>
                                            <input type="text" name="dataAviso" id="dataAviso" class="inputPequeno validate[required]"  disabled="disabled" value="<?php echo (isset($_REQUEST['dataAviso'])) ? $_REQUEST['dataAviso'] : ""; ?>"/>
                                        </p>              
                                        <p id="p_aviso">
                                            <label class="first">Aviso previo:</label>
                                            <?php echo montaSelect($aviso, $avisoPrevioSel=='' ? 'indenizado' : $avisoPrevioSel, array('name' => "aviso", 'id' => 'aviso', 'class' => 'selectPequeno', 'disabled' => 'disabled')); ?>
                                        </p>
                                        <p id="p_data_demi">
                                            <label class="first">Data Demissao:</label>
                                            <input type="text" name="dataDemi" id="dataDemi" class="inputPequeno validate[required]" value="<?php echo $_REQUEST['dataDemi']; ?>" disabled/>
                                        </p>
                                        <p id="p_taxa_rat">
                                            <label class="first">Taxa RAT %:</label>
                                            <input type="text" name="taxaRAT" id="taxaRAT" class="inputPequeno validate[required]" value="<?php echo $_REQUEST['taxaRAT']; ?>"/>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <br class="clear"/>
                        </fieldset>
                    </div>    
                    <div id="lista_funcionarios"></div>
                    <fieldset class="noprint historico">
                        <p class="txt-red">Histórico dos últimos 30 dias.</p>
                        <table id="historico_gerado" border="0" cellpadding="0" cellspacing="0" class="grid" width="100%">
                            <thead>
                                <tr>
                                    <th>Projeto</th>
                                    <th>Situação</th>
                                    <th>Dispensa</th>
                                    <th>Fator</th>
                                    <th>Data Demissao</th>
                                    <th>Aviso Previo Indenizado</th>
                                    <th>Data do Aviso</th>
                                    <th>Criado Por</th>
                                    <th>Criado Em</th>
                                    <th>Total de Participantes</th>
                                    <th colspan="4">Acao</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($linha_header = mysql_fetch_assoc($sql_historico)) { ?>
                                    <tr class="tr_<?php echo $linha_header['id_header']; ?>">
                                        <td><?php echo $linha_header['nome_projeto']; ?></td>
                                        <td><?php echo $linha_header['status_situacao']; ?></td>                                        
                                        <td><?php echo $linha_header['especifica']; ?></td>
                                        <td><?php echo $linha_header['fator']; ?></td>
                                        <td><?php echo date("d/m/Y", strtotime(str_replace("/", "-", $linha_header['data_demi']))); ?></td>
                                        <td><?php echo $linha_header['aviso_previo']; ?></td>
                                        <td><?php echo ($linha_header['data_aviso'] != "0000-00-00") ? date("d/m/Y", strtotime(str_replace("/", "-", $linha_header['data_aviso']))) : ""; ?></td>
                                        <td><?php echo $linha_header['criado_por_nome']; ?></td>
                                        <td><?php echo ($linha_header['data_formatada'] != "00/00/0000 - 00:00:00") ? $linha_header['data_formatada'] : ""; ?></td>
                                        <td align="center"><spam id="tot_<?=$linha_header['id_header']?>" class="count"><?=$linha_header['total']?></spam> Processados<br>no total de <spam id="tot_lote_<?=$linha_header['id_header']?>" class="count"><?=$linha_header['tot_lote']?></spam></td>
                                        <?php
                                        if($linha_header['efetivada']) {
                                        ?>    
                                        <td><a target="_blank" href="../rh/recisao/rescisao_lote_finalizado.php?id=<?=$linha_header['id_header']?>" data-key='<?=$linha_header['id_header']?>' data-projeto="<?php echo $linha_header['id_projeto']; ?>" class="res_finalizada" style="text-decoration: none; color: #0000CC; font-weight: bold; cursor: pointer;"><img src="../imagens/icones/icon-filego.gif" width="16px;" height="16px;" title="Rescisões Finalizadas" /></a></td>
                                        <td><a target="_blank" href="../rh/recisao/imprimir_rescisao_lote.php?id=<?=$linha_header['id_header']?>" data-key='<?=$linha_header['id_header']?>' data-projeto="<?php echo $linha_header['id_projeto']; ?>" class="imprimir" style="text-decoration: none; color: #0000CC; font-weight: bold; cursor: pointer;"><img src="../imagens/impressora.png" width="16px;" height="16px;" title="Imprimir Rescisao" /></a></td>
                                        <?php
                                        }
                                        else {
                                        ?>    
                                        <td align="center" colspan="2"><a href="javascript:;" data-key='<?php echo $linha_header['id_header']?>' class="desprocessar" style="text-decoration: none; color: #0000CC; font-weight: bold; cursor: pointer;"><img src="../imagens/icones/icon-delete.gif" title="desprocessar" /></a></td>
                                        <?php
                                        }
                                        ?>
                                        <td colspan="1"><a href="javascript:;" data-key='<?php echo $linha_header['id_header']; ?>' data-projeto="<?php echo $linha_header['id_projeto']; ?>" class="visualizarLotePendente" style="text-decoration: none; color: #0000CC; font-weight: bold; cursor: pointer;"><img src="../imagens/icones/icon-table-add.png" title="visualizar registros do lote pendente" /></a></td>
                                        <td colspan="1"><a href="javascript:;" data-key='<?php echo $linha_header['id_header']; ?>' data-projeto="<?php echo $linha_header['id_projeto']; ?>" class="visualizar" style="text-decoration: none; color: #0000CC; font-weight: bold; cursor: pointer;"><img src="../imagens/icones/icon-view.gif" title="visualizar registros do lote processado" /></a></td>
                                    </tr>
                                <?php 
                                    } 
                                ?>
                            </tbody>
                        </table>
                    </fieldset>
                    <?php
                    include('inc_provisao_gastos.php');
                    include('inc_provisao_trabalhista.php');
                    ?>
                </form>
                <footer>
                    <div>
                        <p>Pay All Fast 3.0 build tag_rev - <?=date('d/m/Y - H:i')?></p>
                        <p>Todos os direitos reservados <a href="http://f71.com.br" rel="nofollow" target="_blank">F71 Sistemas</a>.</p>
                    </div>
                </footer>    
            </div>
            <div>
                <div id="recolhimento_fgts">
                    <div id="box-1">
                        <h3 class="descricao_box">» Retenções de FGTS</h3>
                        <div id="dados_historico_fgts"></div>
                    </div>
                </div>    

                <form name="form_movimento" action="" method="post" id="form_movimento">
                    <div class="lancamento">
                        <div id="lancamento_mov">
                            <div id="box-1">
                                <input type="hidden" id="id_clt" name="id_clt" value="" />
                                <input type="hidden" id="id_rescisao" name="id_rescisao" value="" />
                                <input type="hidden" id="id_rescisao_lote" name="id_rescisao_lote" value="" />
                                <h3 class="descricao_box">» Lancamento de Movimento Individual</h3>
                                <fieldset>
                                    <p>
                                        <label class="first">Selecione o movimento</label>
                                        <?php echo montaSelect($movs, $movSel, array('name' => "movimento", 'id' => 'movimento', 'class' => 'selectPequeno texto_pequeno')); ?> 
                                    </p>
                                    <p>
                                        <label class="first texto_pequeno">Valor do movimento</label>
                                        <input type="text" name="valor_movimento" id='valor_movimento'  /> 
                                    </p>
                                    <p>
                                        <input type="button" class="texto_pequeno" name="cadastrar_mov" id="cadastrar_mov" value="Cadastrar Movimento" />
                                    </p>
                                    <p class="mensagem"></p>
                                </fieldset>
                            </div>
                            <div id="box-2">
                                <h3 class="descricao_box">» Histórico</h3>
                                <div id="dados_historico"></div>
                            </div>
                        </div>    
                    </div>
                </form>
                <form name="form_movimento_lote" action="" method="post" id="form_movimento_lote">
                    <div class="lancamento">
                        <div id="lancamento_mov_lote">
                            <div id="box-3">
                                <input type="hidden" id="id_rescisao_mov_lote" name="id_rescisao_mov_lote" value="" />
                                <h3 class="descricao_box">» Lancamento Movimentos coletivos</h3>
                                <fieldset>
                                    <p>
                                        <label class="first">Selecione o movimento</label>
                                        <?php echo montaSelect($movs, $movSel, array('name' => "movimento_lote", 'id' => 'movimento_lote', 'class' => 'selectPequeno texto_pequeno')); ?> 
                                    </p>
                                    <p>
                                        <label class="first texto_pequeno">Valor do movimento</label>
                                        <input type="text" name="valor_movimento_lote" id='valor_movimento_lote'  /> 
                                    </p>
                                    <p>
                                        <input type="button" class="texto_pequeno" name="cadastrar_mov_lote" id="cadastrar_mov_lote" value="Cadastrar Movimento de Lote" />
                                    </p>
                                    <p class="mensagem"></p>
                                </fieldset>
                            </div>
                            <div id="box-4">
                                <h3 class="descricao_box">» Histórico</h3>
                                <div id="dados_historico_lote"></div>
                            </div>
                        </div>    
                    </div>
                </form>
            </div>
        </article>        
    </body>
</html>
