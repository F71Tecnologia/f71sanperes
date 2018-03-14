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
$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"36", "area"=>"Prestação de Contas", "ativo"=>"RH Contratado por RPA","id_form"=>"form1");

$regiao = $usuario['id_regiao'];
$master = $usuario['id_master'];
$usuario_id = $usuario['id_funcionario'];

$result = null;
$btexportar = true;
$btfinalizar = true;
$dataMesIni = date("Y-m") . "-31";
$query_exec = 0;

//ATUALIZANDO HORARIO MENSAL
if ($_REQUEST['method'] == "atualiza_horario_mensal") {

    $return = array("status" => false);
    $query = "UPDATE rpa_autonomo SET hora_mes = '{$_REQUEST['horas_mes']}' WHERE id_rpa = {$_REQUEST['id_rpa']}";
    $sql = mysql_query($query) or die("Erro ao atualizar hora mensal do RPA");
    if($sql){
        $return = array("status" => true);
    }
    
    echo json_encode($return);
    exit();
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
        $qr_verifica = PrestacaoContas::getQueryVerifica("rhrpa", $dataMesRef, $dataMesIni);
        
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
        $qr = "SELECT A.id_autonomo, A.id_projeto, B.id_rpa, G.cod_contrato, G.cod_sesrj, A.nome, A.cpf, 
                F.nome AS nomeCbo, E.nome AS funcao, E.cbo_codigo, D.id_saida, B.valor, (B.valor_inss + B.valor_ir) AS encargos, B.valor_liquido, 
                IF(A.orgao != 'DETRAN' && A.orgao != 'IFP',A.rg, NULL) AS numconselho,
                B.hora_mes AS hora_mensal_rpa,
                SUM(CAST(REPLACE(B.valor, ',', '.') as decimal(13,2))) as valor2,
                COUNT(E.id_curso) AS qnt
                FROM autonomo AS A
                LEFT JOIN rpa_autonomo AS B ON(A.id_autonomo = B.id_autonomo)
                INNER JOIN rpa_saida_assoc AS C ON(B.id_rpa = C.id_rpa)
                LEFT JOIN saida AS D ON (C.id_saida = D.id_saida)
                LEFT JOIN curso AS E ON (A.id_curso = E.id_curso)
                LEFT JOIN rh_cbo AS F ON (E.cbo_codigo = F.id_cbo)
                LEFT JOIN projeto AS G ON (A.id_projeto = G.id_projeto)
                WHERE D.id_projeto = '{$id_projeto}' AND MONTH(B.data_geracao) = '{$_REQUEST['mes']}' AND YEAR(B.data_geracao) = '{$_REQUEST['ano']}' AND C.tipo_vinculo = 1 AND D.status IN(1,2) AND D.estorno = 0
                GROUP BY A.id_autonomo, B.valor, E.id_curso ORDER BY A.nome";
                
    } else {
        $query_exec = 1;
        //RENOMEANDO OS CMAPOS, PARA APARECEREM NO RELATÓRIO SEM MODIFICAR O HTML
        $qr = "SELECT *, conselho AS numconselho, num_rpa AS id_rpa, categoria_prof AS nomeCbo, cargo AS funcao, cbo AS cod, valor AS valor_liquido, hora_mensal AS hora_mensal_rpa  FROM prestacoes_contas_rhrpa WHERE id_prestacao = {$historico}";
//        print_r($qr);exit;
    }

    //QUERY EXPORTAÇÃO
    if (isset($_REQUEST['exportar']) && !empty($_REQUEST['exportar'])) {
        $qr = "SELECT *
                FROM prestacoes_contas_rhrpa WHERE id_prestacao IN (" . implode(",", $finalizados) . ")";
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
    $result     = mysql_query($qr);
    $linhas     = mysql_num_rows($result);
    $regiao     = $usuario['id_regiao'];
    $bancoSave  = $_REQUEST['banco'];
    $referencia = "{$_REQUEST['ano']}-{$_REQUEST['mes']}-01";
       
    /*VALOR TOTAL*/
    $qrT = "SELECT SUM(valor2) AS total
                FROM (" . $qr . ") AS tab";

    echo "<!-- ".$qrT." -->";
    $resultT = mysql_query($qrT);
    $rowT = mysql_fetch_assoc($resultT);
    $val_total = $rowT['total'];
            
    //CASO TENHA PRESTAÇÃO DE CONTAS FINALIZADA COM ERRO PARA O PROJETO E O MES SELECIONADO, VAMOS ATUALIZAR
    $rsVerificaPrest = mysql_query("SELECT * FROM prestacoes_contas WHERE tipo = 'rhrpa' AND data_referencia = '{$referencia}' AND id_projeto = {$_REQUEST['projeto']} AND erros > 0 AND status = 1");
    $rowVerificaPrest = mysql_fetch_assoc($rsVerificaPrest);
    
    if(mysql_num_rows($rsVerificaPrest) > 0){
        $id = $rowVerificaPrest['id_prestacao'];
    }else{
        $campos = "id_projeto, id_regiao, id_banco, tipo, data_referencia, gerado_em, gerado_por, linhas, erros, valor_total,status";
        $valoress = array(
                $_REQUEST['projeto'],
                $regiao,
                $bancoSave,
                "rhrpa",
                $referencia,
                date("Y-m-d H:i:s"),
                $usuario_id,
                $linhas,
                "0",
                $val_total,
                "1");
           
        sqlInsert("prestacoes_contas",$campos,$valoress);
        $id = mysql_insert_id();
    }
    
    $matriz = array();
    if ($linhas > 0) {
        while ($row = mysql_fetch_assoc($result)) {
            $matriz[$count][] = $id;
            $matriz[$count][] = $roMaster['cod_os'];
            $matriz[$count][] = $row['cod_contrato'];
            $matriz[$count][] = $row['cod_sesrj'];
            $matriz[$count][] = $row['nome'];
            $matriz[$count][] = $row['cpf'];
            $matriz[$count][] = $row['numconselho'];
            $matriz[$count][] = $row['id_rpa'];
            $matriz[$count][] = $row['nomeCbo'];
            $matriz[$count][] = $row['funcao'];
            $matriz[$count][] = $row['cbo_codigo'];
            $matriz[$count][] = $row['qnt'];
            $matriz[$count][] = "RPA";
            $matriz[$count][] = $_REQUEST['hora_mes_'.$row['id_rpa']];
            $matriz[$count][] = $row['valor_liquido'];
            $matriz[$count][] = $row['encargos'];
            $matriz[$count][] = $row['valor_liquido'];
            
            $count++;
        }
    }

    $campos = array(
        "id_prestacao",
        "cod_os",
        "cod_contrato",
        "cod_unidade",
        "nome",
        "cpf",
        "conselho",
        "num_rpa",
        "categoria_prof",
        "cargo",
        "cbo",
        "qnt",
        "contratacao",
        "hora_mensal",
        "valor",
        "encargos",
        "total"
    );
    sqlInsert("prestacoes_contas_rhrpa", $campos, $matriz);
    echo "<script>location.href='finan_rhrpa.php'</script>";
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
    $fname = "OS_{$roMaster['cod_os']}_RHRPA_" . date("Ymd") . "_" . $mes2d . "{$_REQUEST['ano']}.CSV";
    $filename = $folder . $fname;

    /* ESCREVENDO NO ARQUIVO */
    /* HEADER */
    $handle = fopen($filename, "w");
    fwrite($handle, "H;COD_OS;DATA_GERACAO;LINHAS;TIPO;ANO_MES_REF;TIPO_ARQUIVO;VER_DOC;SECRETARIA\r\n");
    fwrite($handle, "H;{$roMaster['cod_os']};" . date("Y-m-d") . ";{$linhasArquivo};N;{$anoMesReferencia};RHRPA;3.1;01.01.01.01\r\n");

    /* DETAIL */
    fwrite($handle, "D;NOME;CPF;N_CONSELHO;N_RECIBO;CAT_PROF;CARGO;CBO;QTDE;FORMA_CONTRATACAO;CARGA_HORARIA_M;VALOR;ENCARGOS;TOTAL\r\n");

    //ESCREVENDO AS LINHAS NO ARQUIVO CASO TENHA BENS
    while ($row = mysql_fetch_assoc($result)) {
        //$valor = str_replace(".", ",", $row['mes_atual']);
        $cpf = str_pad($row['cpf'], 11, "0", STR_PAD_LEFT); //SEM PONTOS 11 DIGITOS PREENCHIDOS COM ZERO
        $cpf = preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);

        fwrite($handle, "D;{$row['nome']};{$cpf};{$row['conselho']};{$row['id_rpa']};{$row['categoria_prof']};{$row['cargo']};{$row['cbo']};{$row['qnt']};{$row['contratacao']};{$row['hora_mensal']};{$row['valor']};{$row['encargos']};{$row['total']};{$roMaster['cod_os']};\r\n");
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
        <title>:: Intranet :: RH CONTRATADO POR RPA</title>
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
                $("#projeto").change(function(){
                    var $this = $(this);
                    if($this.val() != "-1"){
                        showLoading($this,"../");
                        $.post('finan_equipe.php', { projeto: $this.val(), method: "loadbancos" }, function(data) {
                            removeLoading();
                            if(data.status==1){
                                var opcao = "";
                                var selected = "";
                                for (var i in data.options){
                                    selected = "";
                                    if(i==$("#bancSel").val()){
                                        selected = "selected=\"selected\" ";
                                    }
                                    opcao += "<option value='" + i + "' " + selected + ">" + data.options[i] + "</option>";
                                }
                                $("#banco").html(opcao);
                            }
                        },"json");
                    }
                }).trigger("change");
                
                $(".inputBlur").blur(function(){
                    var id_rpa = $(this).attr("data-key");
                    var horas_mes = $(this).val();
                    $.ajax({
                       type:"POST",
                       dataType:"json",
                       data:{
                           url:"controller_prestacao.php",
                           id_rpa: id_rpa,
                           horas_mes: horas_mes,
                           method:"atualiza_horario_mensal"
                       },
                       success: function(data){
                           if(data.status){
                               $("#hora_mes_" + id_rpa).html(horas_mes);
                           }
                       }
                    });
                });
                
            });
        </script>

        <style>
            .inputBlur{
                border: 1px solid #ccc;
                padding: 3px;
                width: 150px;
            }
            @media print
            {
                fieldset{display: none;}
                #message-box{display: none;}
                input{display: none;}
            }
        </style>
    </head>
    <body id="page-despesas" class="novaintra">
        
        <?php include("../template/navbar_default.php"); ?>
        
        <div class="container">
            <div class="page-header box-contas-header"><h2><span class="glyphicon glyphicon-list-alt"></span> - Prestação de Contas</h2></div>
            
        <div id="content">
            <form action="" method="post" name="form1" id="form1" class="form-horizontal top-margin1">

                <input type="hidden" name="home" id="home" value="" />
                <input type="hidden" name="bancSel" id="bancSel" value="<?php echo $bancoR ?>" />                                
                
                <fieldset>
                    <legend>RH Contratado por RPA</legend>
                    <div class="form-group">
                        <label for="select" class="col-lg-2 control-label">Projeto</label>
                        <div class="col-lg-4">
                            <?php echo montaSelect(PrestacaoContas::carregaProjetos($master, "equipe"), $projetoR, $attrPro); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="select" class="col-lg-2 control-label">Banco</label>
                        <div class="col-lg-4">
                            <?php echo montaSelect(array("-1" => "« Todos »"), null, "id='banco' name='banco' class='form-control'"); ?>
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
                    
                    <p style="text-align: right;">                        
                        <button type="button" onclick="tableToExcel('tabela', 'RPA CONTRATADO')" class="btn btn-success exportarExcel"><span class="fa fa-file-excel-o"></span>&nbsp;&nbsp;Exportar para Excel</button>
                    </p>
                    
                    <div class="alert alert-dismissable alert-warning">                
                        <strong>Unidade Gerenciada: </strong> <?php echo $projeto['nome']; ?>                        
                        <strong class="borda_titulo">O Responsável: </strong> <?php echo $roMaster['nome']; ?>
                        <strong class="borda_titulo">Mês Referente: </strong> <?php echo $mesShow; ?>
                    </div>
                    
                    <table id="tabela" class="grid table table-hover table-striped">
                        <thead>                            
                            <tr>
                                <th colspan="13" class="text-center fundo_titulo">RH CONTRATADO POR RPA</th>
                            </tr>
                            <tr class="titulo">
                                <th>NOME</th>
                                <th>CPF</th>
                                <th>Nº CONSELHO</th>
                                <th>Nº RECIBO RPA</th>
                                <th>CATEGORIA PROFISSIONAL</th>
                                <th>CARGO</th>
                                <th>CBO</th>
                                <th>Qthe.</th>
                                <th>FORMA DE CONTRATAÇÃO</th>
                                <th>CARGA HORÁRIA MENSAL</th>
                                <th>VALOR</th>
                                <th>ENCARGOS</th>
                                <th>TOTAL</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $total_valor = 0;
                                
                                while ($row = mysql_fetch_assoc($result)) {
                                $cpf = preg_replace('/[^[:digit:]]/', '', $row['cpf']);
                                $total_liquido += $row['valor_liquido'];
                                $total_encargos += $row['encargos'];
                                $total_valor += $row['valor'];
                                
                            ?>                                
                                <tr>
                                    <td><?php echo $row['nome']; ?></td>
                                    <td><?php echo preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf); ?></td>
                                    <td><?php echo $row['numconselho']; ?></td>
                                    <td><?php echo $row['id_rpa']; ?></td>
                                    <td><?php echo $row['nomeCbo']; ?></td>
                                    <td><?php echo $row['funcao']; ?></td>
                                    <td><?php echo $row['cbo_codigo']; ?></td>
                                    <td><?php echo $row['qnt']; ?></td>
                                    <td>RPA</td>
                                    <td>
                                        <?php if($row['hora_mensal_rpa'] == "" && $row['hora_mensal_rpa'] == 0){ ?>
                                            <input type="text" class="inputBlur form-control" name="hora_mes_<?php echo $row['id_rpa']; ?>"  id="hora_mes_<?php echo $row['id_rpa'] ?>" data-key="<?php echo $row['id_rpa'] ?>" />
                                        <?php }else{ ?>
                                            <?php echo $row['hora_mensal_rpa']; ?>
                                            <input type="hidden" class="inputBlur" name="hora_mes_<?php echo $row['id_rpa']; ?>"  id="hora_mes_<?php echo $row['id_rpa'] ?>" value="<?php echo $row['hora_mensal_rpa']; ?>" />
                                        <?php } ?>    
                                    </td>
                                    <td class="text-right"><?php echo number_format($row['valor_liquido'], 2, ",", "."); ?></td>
                                    <td class="text-right"><?php echo number_format($row['encargos'], 2, ",", "."); ?></td>
                                    <td class="text-right"><?php echo number_format($row['valor'], 2, ",", "."); ?></td>
                                </tr>
                            <?php } ?>
                                <tr>
                                    <td colspan="10"></td>
                                    <td class="text-right"><?php echo number_format($total_liquido, 2, ",", "."); ?></td>
                                    <td class="text-right"><?php echo number_format($total_encargos, 2, ",", "."); ?></td>
                                    <td class="text-right"><?php echo number_format($total_valor, 2, ",", "."); ?></td>                                    
                                </tr>
                        </tbody>
                        <tfoot>
                            <tr class="info">
                                <td colspan="11" class="text-right">Total de participantes:</td>
                                <td colspan="2" class="text-right"> <?php echo $linhas ?></td>
                            </tr>
                        </tfoot>
                    </table>
                    
                    <?php } else { ?>
                        <?php if ($projetoR !== null) { ?>
                        <br/>
                        <div class='alert alert-success'>
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
                            <div class='alert alert-warning'>
                                <p><?php
                            echo $msgErro . " ";
                            echo (count($idsErros) > 0) ? implode(", ", $idsErros) : "";
                            ?></p>
                            </div>
                                <?php } ?>
                        <?php } else { ?>
                        <div class='alert alert-warning'>
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