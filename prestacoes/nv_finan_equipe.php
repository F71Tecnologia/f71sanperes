<?php
if (empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="login.php">Logar</a>';
    exit;
}

include('../conn.php');
include('../funcoes.php');
include('../wfunction.php');
include('PrestacaoContas.class.php');

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"36", "area"=>"Prestação de Contas", "ativo"=>"Equipe","id_form"=>"form1");

$regiao = $usuario['id_regiao'];
$master = $usuario['id_master'];
$usuario_id = $usuario['id_funcionario'];
$id_regiao = 0;

$result = null;
$btexportar = true;
$btfinalizar = true;
$dataMesIni = date("Y-m") . "-31";

//----- CARREGA OS BANCOS VIA AJAX, RETORNA UM JSON 
if (isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method'] == "loadbancos") {
    $return['status'] = 1;
    $qr_bancos = mysql_query("SELECT * FROM bancos WHERE id_projeto = '{$_REQUEST['projeto']}' AND status_reg=1");
    $num_rows = mysql_num_rows($qr_bancos);
    $bancos = array();
    if ($num_rows > 0) {
        while ($row = mysql_fetch_assoc($qr_bancos)) {
            $bancos[$row['id_banco']] = $row['id_banco'] . " - " . utf8_encode($row['nome']);
        }
    } else {
        $bancos["-1"] = "Banco não encontrado";
    }
    $return['options'] = $bancos;
    echo json_encode($return);
    exit;
}

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
        $qr_verifica = PrestacaoContas::getQueryVerifica("equipe", $dataMesRef, $dataMesIni);

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
        $qrFolha = "SELECT id_folha,regiao FROM rh_folha WHERE projeto = {$id_projeto} AND mes = '{$mes2d}' AND ano = '{$_REQUEST['ano']}' AND terceiro = 2 AND `status` = 3";
        $rsFolha = mysql_query($qrFolha);
        $rowFolha = mysql_fetch_assoc($rsFolha);
        $regiaoFolha = $rowFolha['regiao'];
        
        $gambi = "";
        if($rowFolha['id_folha'] == 1521){
            $gambi = " || A.id_folha = 1632";
        }else if($rowFolha['id_folha'] == 1544){
            $gambi = " || A.id_folha = 1634";
        }else if($rowFolha['id_folha'] == 1591){
            $gambi = " || A.id_folha = 1635";
        }        

        $qr = "SELECT A.id_clt,A.id_folha,A.id_folha_proc,
                B.nome,B.cpf,B.rg,B.pis,B.data_entrada,B.data_saida,B.data_nasci,IF(B.sexo='M','Masculino','Feminino') as sexo,
                'CLT' as tpcontrato, E.tipopg,C.nome as funcao,D.cod_sesrj,D.cod_contrato,D.id_projeto,
                DATE_FORMAT(B.data_entrada, '%d/%m/%Y') as data_entradaBr,
                DATE_FORMAT(B.data_saida, '%d/%m/%Y') as data_saidaBr,
                DATE_FORMAT(B.data_nasci, '%d/%m/%Y') as data_nasciBr
                FROM rh_folha_proc AS A
                INNER JOIN rh_clt AS B ON (A.id_clt=B.id_clt)
                INNER JOIN curso AS C ON (B.id_curso = C.id_curso)
                INNER JOIN projeto AS D ON (D.id_projeto = A.id_projeto)
                INNER JOIN tipopg AS E ON (E.id_tipopg = B.tipo_pagamento)
                WHERE A.id_folha = {$rowFolha['id_folha']} {$gambi}
                ORDER BY B.nome";
    } else {
        //RENOMEANDO OS CMAPOS, PARA APARECEREM NO RELATÓRIO SEM MODIFICAR O HTML
        $qr = "SELECT *,tipo_contra as tpcontrato,
                    DATE_FORMAT(data_entrada, '%d/%m/%Y') as data_entradaBr,
                    DATE_FORMAT(data_saida, '%d/%m/%Y') as data_saidaBr,
                    DATE_FORMAT(data_nasci, '%d/%m/%Y') as data_nasciBr
                    FROM prestacoes_contas_equipe WHERE id_prestacao = {$historico}";
    }

    //QUERY EXPORTAÇÃO
    if (isset($_REQUEST['exportar']) && !empty($_REQUEST['exportar'])) {
        $qr = "SELECT *
                FROM prestacoes_contas_equipe WHERE id_prestacao IN (" . implode(",", $finalizados) . ")";
    }

    $qr_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = {$_REQUEST['projeto']}");
    $projeto = mysql_fetch_assoc($qr_projeto);

    $qrMaster = "SELECT nome,cod_os FROM master WHERE id_master = {$master}";
    $reMaster = mysql_query($qrMaster);
    $roMaster = mysql_fetch_assoc($reMaster);
}

//FINALIZANDO A PRESTAÇÃO DESSE PROJETO
if (isset($_REQUEST['finalizar']) && !empty($_REQUEST['finalizar'])) {
    echo "<!-- " . $qr . " -->";
    $result = mysql_query($qr);
    $linhas = mysql_num_rows($result);

    $referencia = "{$_REQUEST['ano']}-{$mes2d}-01";

    $campos = "id_projeto, id_regiao, id_banco, id_folha, terceiro, tipo, data_referencia, gerado_em, gerado_por, linhas, erros, status";
    $valores = array(
        $_REQUEST['projeto'],
        $regiao,
        $_REQUEST['banco'],
        $rowFolha['id_folha'],
        "Nao",
        "equipe",
        $referencia,
        date("Y-m-d H:i:s"),
        $usuario_id,
        $linhas,
        "0",
        "1");

    sqlInsert("prestacoes_contas", $campos, $valores);
    $id = mysql_insert_id();

    if (empty($id))
        exit("Erro Grave na tabela prestacoes_contas <br/> ERRO: " . mysql_error());

    $matriz = array();
    $count = 0;
    if ($linhas > 0) {
        while ($row = mysql_fetch_assoc($result)) {
            $matriz[$count][] = $id;
            $matriz[$count][] = $row['id_folha'];
            $matriz[$count][] = $row['id_folha_proc'];
            $matriz[$count][] = $row['id_clt'];
            $matriz[$count][] = $row['id_projeto'];

            $matriz[$count][] = $roMaster['cod_os'];
            $matriz[$count][] = $row['cod_sesrj'];
            $matriz[$count][] = $row['cod_contrato'];
            $matriz[$count][] = $referencia;

            $matriz[$count][] = $row['nome'];
            $matriz[$count][] = preg_replace('/[^[:digit:]]/', '', $row['cpf']);
            $matriz[$count][] = $row['rg'];
            $matriz[$count][] = $row['pis'];
            $matriz[$count][] = $row['data_nasci'];
            $matriz[$count][] = $row['tpcontrato'];
            $matriz[$count][] = $row['data_entrada'];
            $matriz[$count][] = $row['data_saida'];
            $matriz[$count][] = $row['sexo'];
            $matriz[$count][] = $row['funcao'];
            $matriz[$count][] = $row['tipopg'];

            $count++;
        }
    }

    $campos = array(
        "id_prestacao",
        "id_folha",
        "id_folha_proc",
        "id_clt",
        "id_projeto",
        "cod_os",
        "cod_unidade",
        "cod_contrato",
        "ano_mes_ref",
        "nome",
        "cpf",
        "rg",
        "pis",
        "data_nasci",
        "tipo_contra",
        "data_entrada",
        "data_saida",
        "sexo",
        "funcao",
        "tipopg"
    );
    sqlInsert("prestacoes_contas_equipe", $campos, $matriz);
    echo "<script>location.href='finan_equipe.php'</script>";
    exit;
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

    /* GERANDO O ZIP COM AS IMAGENS */



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

$attrPro = array("id" => "projeto", "name" => "projeto", "class" => "validate[custom[select]] form-control");
$meses = mesesArray(null);
$anos = anosArray(null, null);

/* VARIAVEIS PARA MANTER OS CAMPOS DO FORMULÁRIO SELECIONADO */
$projetoR = (isset($_REQUEST['projeto'])) ? $_REQUEST['projeto'] : null;
$mesR = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : date('m') - 1;
$anoR = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');
$erros = 0;
$idsErros = array();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>:: Intranet :: EQUIPE</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../favicon.png" />                                                                
        
        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="screen">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen"> 
        
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" media="screen">                
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />                
        <!--<link href="../net1.css" rel="stylesheet" type="text/css" />VAI SAIR-->
        
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>        
        <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>        
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../resources/js/tooltip.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/jquery.validationEngine.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-pt.js" type="text/javascript"></script>
        <script src="../js/global.js" type="text/javascript"></script>

        <script>
            $(function() {
                $("#form1").validationEngine();

                $("#projeto").change(function() {
                    var $this = $(this);
                    if ($this.val() != "-1") {
                        showLoading($this, "../");
                        $.post('finan_equipe.php', {projeto: $this.val(), method: "loadbancos"}, function(data) {
                            removeLoading();
                            if (data.status == 1) {
                                var opcao = "";
                                var selected = "";
                                for (var i in data.options) {
                                    selected = "";
                                    if (i == $("#bancSel").val()) {
                                        selected = "selected=\"selected\" ";
                                    }
                                    opcao += "<option value='" + i + "' " + selected + ">" + data.options[i] + "</option>";
                                }
                                $("#banco").html(opcao);
                            }
                        }, "json");
                    }
                }).trigger("change");
            });
        </script>

        <style>
            @media print
            {
                fieldset{display: none;}
                .h2page{display: none;}
                .grAdm{display: none;}
                #message-box{display: none;}
                input{display: none;}
            }
            @media screen
            {
                #headerPrint{display: none;}
            }
        </style>
    </head>
    <body id="page-despesas" class="novaintra">
        
        <?php include("../template/navbar_default.php"); ?>
        
        <div class="container">
            <div class="page-header box-contas-header"><h2><span class="glyphicon glyphicon-list-alt"></span> - Prestação de Contas</h2></div>
        
        <div id="content">
            <form action="" method="post" name="form1" id="form1" class="form-horizontal top-margin1">
                <input type="hidden" name="bancSel" id="bancSel" value="<?php echo $bancoR ?>" />
                <input type="hidden" name="home" id="home" value="" />  
                
                <fieldset>
                    <legend>Equipe</legend>
                    <div class="form-group">
                        <label for="select" class="col-lg-2 control-label">Projeto</label>
                        <div class="col-lg-4">
                            <?php echo montaSelect(PrestacaoContas::carregaProjetos($master, "equipe"), $projetoR, $attrPro) ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="select" class="col-lg-2 control-label">Banco</label>
                        <div class="col-lg-4">
                            <?php echo montaSelect(array("-1" => "« Todos »"), null, "id='banco' name='banco' class='form-control'") ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="select" class="col-lg-2 control-label">Mês</label>
                        <div class="col-lg-4">
                            <div class="input-daterange input-group" id="bs-datepicker-range">
                                <?php echo montaSelect($meses, $mesR, "id='mes' name='mes' class='validate[custom[select]] form-control'") ?>                                
                                <span class="input-group-addon">Ano</span>
                                <?php echo montaSelect($anos, $anoR, "id='ano' name='ano' class='validate[custom[select]] form-control'") ?>                                                                
                            </div>
                            <p class="help-block">(Mês de contratação)</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="pull-right">
                            <input type="submit" name="filtrar" value="Filtrar" class="btn btn-primary" />
                        </div>
                    </div>
                </fieldset>
                
                <?php if (!empty($result) && mysql_num_rows($result) > 0) { ?>
                    <br/>                    
                    <p style="text-align: right; margin-top: 20px">                        
                        <button type="button" onclick="tableToExcel('tbRelatorio', 'Equipe')" class="btn btn-success exportarExcel"><span class="fa fa-file-excel-o"></span>&nbsp;&nbsp;Exportar para Excel</button>
                    </p>    
                    <br/>
                    
                    <div class="alert alert-dismissable alert-warning">                
                        <strong>Unidade Gerenciada: </strong> <?php echo $projeto['nome']; ?>
                        <strong class="borda_titulo">O responsável: </strong> <?php echo $roMaster['nome']; ?>
                        <strong class="borda_titulo">Mês Referente: </strong> <?php echo $mesShow; ?>
                    </div>
                    
                    <table id="tbRelatorio" class="grid table table-hover table-striped">
                        <thead>
                            <tr class="titulo">
                                <th>NOME</th>
                                <th>CPF</th>
                                <th>RG</th>
                                <th>PIS</th>
                                <th>DATA NASCIMENTO</th>
                                <th>TIPO CONTRATAÇÃO</th>
                                <th>DATA ENTRADA</th>
                                <th>DATA SAÍDA</th>
                                <th>SEXO</th>
                                <th>FUNÇÃO</th>
                                <th>FORMA DE PGTO.</th>
                            </tr>
                        </thead>
                        <tbody>                            
    <?php while ($row = mysql_fetch_assoc($result)) {
        $cpf = preg_replace('/[^[:digit:]]/', '', $row['cpf']); ?>                                
                                <tr>
                                    <td><?php echo $row['nome']; ?></td>
                                    <td><?php echo preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf); ?></td>
                                    <td><?php echo $row['rg']; ?></td>
                                    <td><?php echo $row['pis']; ?></td>
                                    <td class="txcenter"><?php echo $row['data_nasciBr']; ?></td>
                                    <td><?php echo $row['tpcontrato']; ?></td>
                                    <td class="txcenter"><?php echo $row['data_entradaBr']; ?></td>
                                    <td class="txcenter"><?php echo ($row['data_saidaBr'] == "00/00/0000") ? "" : $row['data_saidaBr']; ?></td>
                                    <td><?php echo $row['sexo']; ?></td>
                                    <td><?php echo $row['funcao']; ?></td>
                                    <td><?php echo $row['tipopg']; ?></td>
                                </tr>
    <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr class="info">
                                <td colspan="9" class="text-right">Total de participantes:</td>
                                <td colspan="2"> <?php echo $linhas ?></td>
                            </tr>
                        </tfoot>
                    </table>
<?php } else { ?>
    <?php if ($projetoR !== null) { ?>
                        <br/>
                        <div class='alert alert-danger top30'>
                            <p>Nenhum registro encontrado</p>
                        </div>
    <?php } ?>
<?php } ?>
                <?php if ($projetoR !== null) { ?>
                    <?php if ($btexportar) { ?>
                        <p class="controls">                            
                            <button type="submit" class="button btn btn-primary" name="exportar"><span class="fa fa-share-square-o"></span>&nbsp;&nbsp;Exportar</button>
                        </p>
    <?php } ?>

                    <br/>
                    <?php if ($btfinalizar) { ?>
        <?php if ($erros == 0) { ?>
                            <p class="controls pull-right">                                 
                                <button type="submit" class="button btn btn-warning" name="finalizar"><span class="fa fa-power-off"></span>&nbsp;&nbsp;Finalizar Prestação</button>
                            </p>                            
                            <div class="clear"></div>
        <?php } else { ?>
                            <div class='alert alert-warning top30'>
                                <p><?php echo $msgErro . " ";
            echo (count($idsErros) > 0) ? implode(", ", $idsErros) : ""; ?></p>
                            </div>
        <?php } ?>
                    <?php } else { ?>
                        <div class='alert alert-warning top30'>
                            <p>Prestação finalizada.</p>
                        </div>
    <?php } ?>


    <?php if ($proj_faltantes > 0) { ?>
                        <div class='alert alert-info'>
                            <p>Foi verificado a existencia de <?php echo $contErro ?> projeto(s) para finalizar neste mês antes de gerar o arquivo de prestação de contas.</p>
                            <br />
                            <ul>
        <?php
        foreach ($projetosFaltante as $val) {
            echo "<li>" . $val['nome'] . $val['banco'] . "</li>";
        }
        ?>
                            </ul>
                        </div>
                    <?php } ?>
                <?php } ?>
            </form>
            
            <?php include_once '../template/footer.php'; ?>
            
        </div>
        </div>
    </body>
</html>