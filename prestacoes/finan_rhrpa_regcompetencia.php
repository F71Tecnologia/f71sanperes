<?php

if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}

include('../conn.php');
include('../funcoes.php');
include('../wfunction.php');
include('PrestacaoContas.class.php');

//$usuarioW = carregaUsuario();
//
//$regiao = $usuarioW['id_regiao'];
//$master = $usuarioW['id_master'];
//$usuario = $usuarioW['id_funcionario'];


$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$regioes = getRegioes();
$masters = getMasters();
//
$regiaoSelected = $regioes[$usuario['id_regiao']];
$masterSelected = $masters[$usuario['id_master']];
//
//unset($regioes[$usuario['id_regiao']]);
//unset($regioes['-1']);
//
//unset($masters[$usuario['id_master']]);
//unset($masters['-1']);
//
$regiao = $usuario['id_regiao'];
$master = $usuario['id_master'];
//
//$result = null;
//$btexportar = true;
//$btfinalizar = true;
//$dataMesIni = date("Y-m") . "-31";

// CASO TENHA PROJETO (EM TODOS OS CASOS DPS DO POST)
if (isset($_REQUEST['projeto'])) {

    $id_projeto = $_REQUEST['projeto'];
    $id_banco = $_REQUEST['banco'];
    $mes2d = sprintf("%02d", $_REQUEST['mes']); //mes com 2 digitos

    $anoMesReferencia = $_REQUEST['ano'] . "-" . $mes2d;
    $mesShow = mesesArray($_REQUEST['mes']) . "/" . $_REQUEST['ano'];
    $historico = false;

    if ((isset($_REQUEST['exportar']) && !empty($_REQUEST['exportar'])) || (isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar']))) {

        /* RECUPERANDO OS PROJETOS JA FINALIZADOS */
        //VERIFICA SE OUTRO PROJETO PRECISA PRESTAR CONTAS NO MES SELECIONADO
        $dataMesIni = "{$_REQUEST['ano']}-{$mes2d}-31";
        $dataMesRef = "{$_REQUEST['ano']}-{$mes2d}-01";
        $qr_verifica = PrestacaoContas::getQueryVerifica("rhrpa", $dataMesRef, $dataMesIni, $usuario['id_master']);
        $rs_verifica = mysql_query($qr_verifica);
        $total_verifica = mysql_num_rows($rs_verifica);
        $projetosFaltante = array();
        $contErro = 0;
        $finalizados = array();

        while ($rowVeri = mysql_fetch_assoc($rs_verifica)) {
            //VERIFICA SE OS OUTROS NÃO ESTÃO FINALIZADOS
            if ($rowVeri['gerado_embr'] == null && $rowVeri['id_banco'] != $id_banco) {
                $btexportar = false;
                $projetosFaltante[$contErro]['nome'] = $rowVeri['projeto'];
                $projetosFaltante[$contErro]['banco'] = " Banco: " . $rowVeri['id_banco'] . " AG: " . $rowVeri['agencia'] . " CC: " . $rowVeri['conta'];
                $contErro ++;
            } elseif ($rowVeri['gerado_embr'] != null && $rowVeri['id_projeto'] == $id_projeto && $rowVeri['id_banco'] == $id_banco) {  //VERIFICA SE O ATUAL ESTÁ FINALIZADO
                $btfinalizar = false;
            }

            //VERIFICA SE SÓ TEM 1 E SE JA FOI FINALIZADO
            if ($total_verifica == 1 && $rowVeri['id_projeto'] == $id_projeto && $rowVeri['gerado_embr'] != null) {
                $btfinalizar = false;
            }

            //PRESTAÇÕES FINALIZADAS PARA A EXPORTAÇÃO (NÃO É ENVIADO O PROJETO ADM)
            if ($rowVeri['gerado_embr'] != null && $rowVeri['administracao'] == 0) {
                $finalizados[] = $rowVeri['id_prestacao'];
            }

            //CASO A PESQUISADA ESTIVER FINALIZADA, PEGA DO HISTÓRICO
            if ($rowVeri['id_projeto'] == $id_projeto && $rowVeri['gerado_embr'] != null && $rowVeri['id_banco'] == $id_banco) {
                $historico = $rowVeri['id_prestacao'];
            }
        }

        if ($btfinalizar)
            $btexportar = false;

        $proj_faltantes = count($projetosFaltante);
    }

    //QUERY FILTRO E FINALIZAR
    if ($historico === false) {
        $qr = "SELECT A.id_autonomo, A.id_projeto, A.nome, A.cpf, A.conselho, B.id_rpa, B.valor, B.valor_inss, 
            B.valor_ir, B.valor_liquido,(B.valor_inss + B.valor_ir) AS encargos, B.data_geracao AS emissao,
            B.hora_mes AS hora_mensal_rpa, B.dias_horas, 
            C.nome AS funcao, D.cod, D.nome AS nomeCbo, F.ses_cnes AS cnes, E.horas_mes, A.rg, A.conselho, A.orgao,
            IF(A.orgao != 'DETRAN' && A.orgao != 'IFP',A.rg,null) as numconselho
            FROM autonomo AS A
            LEFT JOIN rpa_autonomo AS B ON (A.id_autonomo = B.id_autonomo)
            LEFT JOIN curso AS C ON (A.id_curso = C.id_curso)
            LEFT JOIN rh_cbo AS D ON (C.cbo_codigo = D.id_cbo)
            LEFT JOIN rh_horarios AS E ON (A.rh_horario = E.id_horario)
            LEFT JOIN rhempresa AS F ON (F.id_projeto = B.id_projeto_pag)
            WHERE A.id_projeto = {$id_projeto} AND id_rpa IS NOT NULL AND B.mes_competencia = {$_REQUEST['mes']} AND B.ano_competencia = {$_REQUEST['ano']}
            GROUP BY A.id_autonomo, B.valor, C.id_curso ORDER BY A.nome";
                
    } else {
        
        //RENOMEANDO OS CMAPOS, PARA APARECEREM NO RELATÓRIO SEM MODIFICAR O HTML
        $qr = "SELECT *, tipo_contra as tpcontrato,
                    DATE_FORMAT(data_entrada, '%d/%m/%Y') as data_entradaBr,
                    DATE_FORMAT(data_saida, '%d/%m/%Y') as data_saidaBr,
                    DATE_FORMAT(data_nasci, '%d/%m/%Y') as data_nasciBr
                    FROM prestacoes_contas_rhrpa WHERE id_prestacao = {$historico}";
    }

    //QUERY EXPORTAÇÃO
    if (isset($_REQUEST['exportar']) && !empty($_REQUEST['exportar'])) {
        $qr = "SELECT * FROM prestacoes_contas_equipe WHERE id_prestacao IN (" . implode(",", $finalizados) . ")";
    }

    $qr_projeto = mysql_query("SELECT A.nome AS nomeunidade, B.ses_cnes AS cnes
            FROM projeto AS A LEFT JOIN rhempresa AS B on (B.id_projeto = A.id_projeto)  
            WHERE A.id_projeto = {$_REQUEST['projeto']}");
    $projeto = mysql_fetch_assoc($qr_projeto);

    $qrMaster = "SELECT nome,cod_os FROM master WHERE id_master = {$master}";
    $reMaster = mysql_query($qrMaster);
    $roMaster = mysql_fetch_assoc($reMaster);
}

/* MONTA O ARQUIVO PARA BAIXAR */
if (isset($_REQUEST['exportar']) && !empty($_REQUEST['exportar'])) {
    error_reporting(E_ERROR);
    //echo $qr;exit;
    $result = mysql_query($qr);
    $linhas = mysql_num_rows($result);
    $linhasArquivo = ($linhas == 0) ? 5 : $linhas + 5; //CASO NÃO TENHA RESULTADO VAI CONTAR OS PROJETOS A ADD 5 LINHAS (CABEÇALHO)

    $folder = dirname(__FILE__) . "/arquivos/";
    $fname = "OS_{$roMaster['cod_os']}_EQUI_" . date("Ymd") . "_" . $mes2d . "{$_REQUEST['ano']}.CSV";
    $filename = $folder . $fname;

    /* ESCREVENDO NO ARQUIVO */
    /* HEADER */
    $handle = fopen($filename, "w");
    fwrite($handle, "H;COD_OS;DATA_GERACAO;LINHAS;TIPO;ANO_MES_REF;TIPO_ARQUIVO;VER_DOC;SECRETARIA\r\n");
    fwrite($handle, "H;{$roMaster['cod_os']};" . date("Y-m-d") . ";{$linhasArquivo};N;{$anoMesReferencia};EQUI;3.1;01.01.01.01\r\n");

    /* DETAIL */
    fwrite($handle, "D;COD_OS;COD_UNIDADE;COD_CONTRATO;NOME;CPF;RG;PIS;TIPO_CONTRATACAO;DATA_ENTRADA;DATA_SAIDA;");
    fwrite($handle, "N_PROCESSO;FOTO;TIPO_PG;SEXO;DATA_NASCIMENTO;FUNCAO\r\n");

    //ESCREVENDO AS LINHAS NO ARQUIVO CASO TENHA BENS
    while ($row = mysql_fetch_assoc($result)) {
        //$valor = str_replace(".", ",", $row['mes_atual']);
        $cpf = str_pad($row['cpf'], 11, "0", STR_PAD_LEFT); //SEM PONTOS 11 DIGITOS PREENCHIDOS COM ZERO
        $cpf = preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);

        //---- FOTO PARA ZIPAR E SALVAR O CSV ------
        //http://netsorrindo.com/intranet/fotosclt/45_3302_4735.gif
        $fotoNome = $regiaoFolha . "_" . $row['id_projeto'] . "_" . $row['id_clt'] . ".gif";
        if (!is_file("../fotosclt/" . $fotoNome)) {
            $fotoNome = "semfoto.gif";
        }

        fwrite($handle, "D;{$roMaster['cod_os']};{$row['cod_unidade']};{$row['cod_contrato']};{$row['nome']};{$cpf};{$row['rg']};{$row['pis']};{$row['tipo_contra']};");
        fwrite($handle, "{$row['data_entrada']};{$row['data_saida']};;{$fotoNome};{$row['tipopg']};{$row['sexo']};{$row['data_nasci']};{$row['funcao']}\r\n");
    }
    unset($row);

    fwrite($handle, "T;QUANTIDADE_REGISTROS\r\n");
    fwrite($handle, "T;{$linhas}");

    /* ------------- */
    fclose($handle);

    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header("Content-type: application/x-msdownload");
    header("Content-Length: " . filesize($filename));
    header("Content-Disposition: attachment; filename={$fname}");
    flush();

    readfile($filename);
    exit;
}

/* FILTRO PARA MOSTRAR O RELATÓRIO */
/* RECEBE AS INFORMÇÕES PRA MONTAR O SELECT */
if (isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar'])) {
    $result = mysql_query($qr);
    $linhas = mysql_num_rows($result);

    echo "<!-- " . $qr . " -->";
    echo "<!--VER " . $qr_verifica . " -->";
}

$attrPro = array("id" => "projeto", "name" => "projeto", "class" => "form-control validate[custom[select]]");
$meses = mesesArray(null);
$anos = anosArray(null, null);

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMULÁRIO SELECIONADO */
$projetoR = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
$mesR = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : date('m') - 1;
$anoR = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');
$erros = 0;
$idsErros = array();

$breadcrumb_config = array("nivel" => "../", "key_btn" => "36", "area" => "Gestão de Prestação de Contas", "ativo" => "Contratado por RPA", "id_form" => "form1");

?>

<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet ::</title>
        <link rel="shortcut icon" href="favicon.png" />
        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-pt.js" type="text/javascript"></script>
        <script src="../js/global.js" type="text/javascript"></script>
        <script>
            $(function() {
                $("#form1").validationEngine();
            });            
        </script>
        <style>
            @media print
            {
                fieldset{display: none;}
                #message-box{display: none;}
                input{display: none;}
            }
            .fontreduz {
                font-size: 0.65em;
                font-weight: bold;
            }
            .fonteduz{
                font-size: 0.7em;
            }
        </style>
    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-contas-header">
                        <h2><span class="glyphicon glyphicon-book"></span> - PRESTAÇÃO DE CONTAS <small>- RH CONTRATADO POR RPA - Regime de Competência</small></h2>
                    </div>
                </div>
            </div>
            <div class="panel panel-body">
                <form action="" method="post" name="form1" id="form1" class="form form-horizontal">
                    <input type="hidden" name="bancSel" id="bancSel" value="<?php echo $bancoR ?>"/>
                    <div class="panel panel-default">
                        <div class="panel-heading">Dados</div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label class="col-xs-2 control-label">Projeto: </label>
                                <div class="col-xs-7">
                                    <?php echo montaSelect(PrestacaoContas::carregaProjetos($master, "equipe"), $projetoR, $attrPro )?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-2 control-label">Mês: </label>
                                <div class="col-xs-4">
                                    <?php echo montaSelect($meses, $mesR, "id='mes' name='mes' class='validate[custom[select]] form-control'") ?>
                                </div>
                                <label class="col-xs-1 control-label">Ano: </label>
                                <div class="col-xs-2">
                                    <?php echo montaSelect($anos, $anoR, "id='ano' name='ano' class='validate[custom[select]] form-control'") ?>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer text-right">
                            <input type="submit" size="120" width="160px" class="btn btn-default text-left" value="Filtrar" name="filtrar"/>
                        </div>
                    </div>
                    <?php if (!empty($result) && mysql_num_rows($result) > 0) { ?>
                    <div class="panel-footer text-right">
                        <input type="button" onclick="tableToExcel('tbRelatorio', 'RH Contratado por RPA')" value="Exportar para Excel" class="btn btn-default exportarExcel">
                    </div>
                    <hr>
                    <table class="table table-striped table-condensed" id="tbRelatorio">
                        <thead class="text text-sm">
                            <tr>
                                <th colspan="13" class="text-center">Responsável: <?php echo $roMaster['nome'] ?></th>
                                <th class="text text-right"><?php echo $mesShow ?></th>
                            </tr>
                            <tr>
                                <th colspan="13" class="text-center">Unidade Gerenciada: <?php echo $projeto['nomeunidade'] ?></th>
                                <th></th>
                            </tr>
                            <tr>
                                <th colspan="13" class="text-center">Código Unidade Gerenciada: <?= $projeto['cnes']?></th>
                                <th></th>
                            </tr>
                            <tr>
                                <th colspan="13" class="text-center">RH CONTRATADO POR RPA - REGIME DE COMPETÊNCIA</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="14" class="info"></td>
                            </tr>
                        </tbody>
                        <tbody class="text text-uppercase">
                            <tr class="text text-center fontreduz">
                                <td width="20%">Nome Completo</td>
                                <td width="14%">CPF</td>
                                <td width="10%">Emissão</td>
                                <td width="6%">Nº do Conselho<br>Profissional</td>
                                <td width="4%">Nº Recibo RPA</td>
                                <td width="6%">Categoria Profissional</td>
                                <td width="6%">Especialidade<br>(médicos)</td>
                                <td width="4%">CBO</td>
                                <td width="4%">Forma de<br>Contratação</td>
                                <td width="6%">Descrição dos plantões<br>(Horário de Entrada/Saída)<br>Data de realização</td>
                                <td width="6%">Carga<br>Horária<br>Mensal</td>
                                <td width="6%">Valor (R$)</td>
                                <td width="6%">Encargos (R$)</td>
                                <td width="6%">Total</td>
                            </tr>

                            <?php while ($row = mysql_fetch_assoc($result)) { $cpf = preg_replace('/[^[:digit:]]/', '', $row['cpf']); ?>   
                            <tr class="text fonteduz">
                                    <td><?php echo $row['nome']; ?></td>
                                    <td><?php echo preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf); ?></td>
                                    <td><?= converteData($row['emissao'],"d/m/Y")?></td>
                                    <td><?php echo $row['numconselho']; ?></td>
                                    <td><?php echo $row['id_rpa']; ?></td>
                                    <td><?php echo $row['nomeCbo']; ?></td>
                                    <td><?php echo $row['funcao']; ?></td>
                                    <td><?php echo $row['cod']; ?></td>
                                    <td>RPA</td>
                                    <td><?php echo nl2br($row['dias_horas']);?></td>
                                    <td><?php echo $row['hora_mensal_rpa']; ?></td>
                                    <td class="text-right"><?php echo number_format($row['valor_liquido'], 2, ",", "."); ?></td>
                                    <td class="text-right"><?php echo number_format($row['encargos'], 2, ",", "."); ?></td>
                                    <td class="text-right"><?php echo number_format($row['valor'], 2, ",", "."); ?></td>                                                                       
                          
                            
                            </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="12" class="text-right">Total de participantes:</td>
                                <td colspan="2" class="text-center"> <?php echo $linhas ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </form>
                <div class="panel-footer">
                    <?php } else { ?>
                        <?php if ($projetoR !== null) { ?>
                        <br/>
                        <div id='message-box' class='alert alert-dismissable alert-warning text text-center'>
                            <p>Nenhum registro encontrado</p>
                        </div>
                    <?php } ?>
                <?php } ?>
                <?php if ($projetoR !== null) { ?>
                    <?php if ($btexportar) { ?>
                        <p class="controls">
                            <input type="submit" class="button" value="Exportar" name="exportar" />
                        </p>
                    <?php } ?>

                    <br/>
                    <?php if ($btfinalizar) { ?>
                        <?php if ($erros == 0) { ?>
                            <p class="controls"> 
                                <!-- <input type="submit" class="button" value="Finalizar Prestação" name="finalizar" /> -->
                            </p>
                            <?php } else { ?>
                            <div id='message-box' class='message-yellow'>
                                <p><?php echo $msgErro." "; echo (count($idsErros)>0) ? implode(", ",$idsErros):""; ?></p>
                            </div>
                            <?php } ?>
                        <?php } else { ?>
                        <div id='message-box' class='alert alert-dismissable alert-success text text-center'>
                            <p>Prestação finalizada.</p>
                        </div>
                        <?php } ?>
                            
                        <?php if ($proj_faltantes > 0) { ?>
                        <div id='message-box' class="panel-group bg-info">
                            <p>Foi verificado a existencia de <?php echo $contErro ?> projeto(s) para finalizar neste mês antes de gerar o arquivo de prestação de contas.</p>
                            <ul>
                            <?php foreach($projetosFaltante as $val){
                                echo "<li>".$val['nome'].$val['banco']."</li>";
                            }
                            ?>
                            </ul>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
            </div>
        </div>        
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/tooltip.js"></script>
        <script src="../resources/js/main.js"></script>
    </body>
</html>