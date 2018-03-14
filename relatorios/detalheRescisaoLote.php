<?php

if(empty($_COOKIE['logado'])) {
    print 'Efetue o Login<br><a href="../login.php">Logar</a>';
    exit;
}
 
header("Content-Type: text/html; charset=ISO-8859-1",true);

include('../conn.php');
include('../funcoes.php');
include('../classes/clt.php');
include('../classes/abreviacao.php');
include('../classes/formato_valor.php');
include('../classes/formato_data.php');
include('../classes/valor_proporcional.php');
include('../wfunction.php');
include("../classes/global.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__);
$global = new GlobalClass();

$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form-lista", "ativo"=>"Relatório de Rescisão");
$breadcrumb_pages = array("Gestão de RH"=>"/intranet/rh/principalrh.php");
$dados_recisao = array();

if(isset($_REQUEST['filtrar']) && $_REQUEST['filtrar'] == "Filtrar"){
    
    $projeto = $_REQUEST['projeto'];
    $mes = $_REQUEST['mes'];
    $ano = $_REQUEST['ano'];
    
    $queryVerificaRescisao = "SELECT A.id_recisao FROM rh_recisao AS A 
                WHERE MONTH(A.data_demi) = '{$_REQUEST['mes']}' AND 
                      YEAR(A.data_demi) = '{$_REQUEST['ano']}' AND 
                      A.id_projeto = '{$_REQUEST['projeto']}' AND
                      A.`status` = 1 ORDER BY A.nome";
    $queryVerificaRescisao = mysql_query($queryVerificaRescisao) or die("Erro ao selecionar rescisao");                  
    $idsRescisoes = array();
    while($rowsRes = mysql_fetch_assoc($queryVerificaRescisao)){
        $idsRescisoes[] = $rowsRes['id_recisao'];
    }
} 
 
$id = $idsRescisoes;

// Consulta da Rescisão
$qr_rescisao = mysql_query("SELECT A.*, C.unidade AS locacao, (D.hora_semana * 5) AS hora_mensal
                                FROM rh_recisao AS A 
                                LEFT JOIN rh_clt AS B ON(A.id_clt = B.id_clt)
                                LEFT JOIN unidade AS C ON(B.id_unidade = C.id_unidade)
                                LEFT JOIN curso AS D ON(B.id_curso = D.id_curso)
                                WHERE A.id_recisao IN(" . implode(',', $id) . ") AND A.status = '1'");

while($row_rescisao = mysql_fetch_array($qr_rescisao)){
//    echo "<pre>";
//        print_r($row_rescisao);
//    echo "</pre>";
    
    $dados_recisao[$row_rescisao['id_recisao']]["id"] = $row_rescisao['id_clt'];
    $dados_recisao[$row_rescisao['id_recisao']]["nome"] = $row_rescisao['nome'];
    $dados_recisao[$row_rescisao['id_recisao']]["locacao"] = $row_rescisao['locacao'];
    $dados_recisao[$row_rescisao['id_recisao']]["sal_base"] = number_format($row_rescisao['sal_base'],2,',','.');
    $dados_recisao[$row_rescisao['id_recisao']]["hora_mensal"] = $row_rescisao['hora_mensal'];
    
    
    $complementar = ($row_rescisao['rescisao_complementar'] == 1) ? 'COMPLEMENTAR' : '';

    if ($row_rescisao['aviso'] == 'trabalhado') {

        $tipo_aviso = 'Aviso Prévio trabalhado';
    } else {
        $tipo_aviso = 'Aviso Prévio indenizado';
    }


    // Tipo da Rescisão
    $qr_motivo = mysql_query("SELECT * FROM rhstatus WHERE codigo = '{$row_rescisao['motivo']}'");
    $row_motivo = mysql_fetch_array($qr_motivo);

    /*****ARRAY DE CAMPOS DE MOVIMENTOS OBRIGATÓRIOS 22/05/2015*********/
    /******************MESMO COM VALOR ZERADO***************************/

    /**
     * DSR
     */
    $dados_recisao[$row_rescisao['id_recisao']]["dados"][58] = array(
        "movimento" => "Descanso Semanal Remunerado (DSR)",
        "tipo" => "CREDITO",
        "valor" => 0.00
    );


    /****************ARRAY DE MOVIMENTOS 20/05/2015*********************/
    //SALDO DE SALÁRIO
    $dados_recisao[$row_rescisao['id_recisao']]["dados"][50] = array(
        "movimento" => "Saldo de {$row_rescisao['dias_saldo']} dias Salário",
        "tipo" => "CREDITO",
        "valor" => $row_rescisao['saldo_salario']
    );

    //INSALUBRIDADE
    $dados_recisao[$row_rescisao['id_recisao']]["dados"][53] = array(
        "movimento" => "Adicional de Insalubridade",
        "tipo" => "CREDITO",
        "valor" => $row_rescisao['insalubridade']
    );


    //REFLEXO DO DSR 
    $dados_recisao[$row_rescisao['id_recisao']]["dados"][59] = array(
        "movimento" => 'Reflexo do "DSR" sobre Salário Variável',
        "tipo" => "CREDITO",
        "valor" => 0.00
    );

    //MULTA 477
    $dados_recisao[$row_rescisao['id_recisao']]["dados"][60] = array(
        "movimento" => 'Multa Art. 477, § 8º/CLT',
        "tipo" => "CREDITO",
        "valor" => $row_rescisao['a477']
    );

    //SALARIO FAMILIA
    $dados_recisao[$row_rescisao['id_recisao']]["dados"][62] = array(
        "movimento" => 'Salário-Família',
        "tipo" => "CREDITO",
        "valor" => $row_rescisao['sal_familia']
    );

    //DECIMO TERCEIRO
    $avos_dt = sprintf('%02d', $row_rescisao['avos_dt']);
    $dados_recisao[$row_rescisao['id_recisao']]["dados"][63] = array(
        "movimento" => "13º Salário Proporcional {$avos_dt}/12 avos",
        "tipo" => "CREDITO",
        "valor" => $row_rescisao['dt_salario']
    );

    //DECIMO TERCEIRO EXERCICIO
    $dados_recisao[$row_rescisao['id_recisao']]["dados"][64] = array(
        "movimento" => '13º Salário Exercício 0/12 avos',
        "tipo" => "CREDITO",
        "valor" => 0.00
    );

    // AVISO PREVIO
    if ($row_rescisao['fator'] == 'empregado' && $row_rescisao['aviso'] == 'PAGO pelo ') {
        $aviso_previo_debito = $row_rescisao['aviso_valor'];
        $dados_recisao[$row_rescisao['id_recisao']]["dados"][103] = array(
            "movimento" => "Aviso-Prévio Indenizado",
            "tipo" => "DEBITO",
            "valor" => $row_rescisao['aviso_valor']
        );
    } else {
        $aviso_previo_credito = $row_rescisao['aviso_valor'];
        $dados_recisao[$row_rescisao['id_recisao']]["dados"][69] = array(
            "movimento" => "Aviso-Prévio Indenizado",
            "tipo" => "CREDITO",
            "valor" => $row_rescisao['aviso_valor']
        );
    }

    ///DISPENSA ANTES DO TERMINO DE CONTRATO PELA EMPRESA
    if ($row_rescisao['motivo'] != '63' and $row_rescisao['motivo'] != '65') {
        $multa_479 = $row_rescisao['a479'];
        $dados_recisao[$row_rescisao['id_recisao']]["dados"][61] = array(
            "movimento" => "Multa Art. 479/CLT",
            "tipo" => "CREDITO",
            "valor" => $row_rescisao['a479']
        );
    } else {
        //INVERSO
        $multa_480 = $row_rescisao['a479'];
        $dados_recisao[$row_rescisao['id_recisao']]["dados"][104] = array(
            "movimento" => "Multa Art. 480/CLT",
            "tipo" => "DEBITO",
            "valor" => $row_rescisao['a480']
        );
    }

    //LEI 12.506
    $dados_recisao[$row_rescisao['id_recisao']]["dados"][95] = array(
        //"movimento" => "Lei 12.506 ({$row_rescisao['qnt_dias_lei_12_506']} dias)",
        "movimento" => "Lei 12.506 ({$qtd_dias_12506} dias)",
        "tipo" => "CREDITO",
        "valor" => $row_rescisao['lei_12_506']
    );

    /**
     * Pegando os valores dos moviemntos e 
     * inserindo nos campos de acordo com o ANEXO VIII da rescisão, 
     * o número do campo encontra-se na tabela rh_movimento
     */

    if($row_rescisao['rescisao_complementar'] == 1){
        $and_complementar = "AND A.complementar = 1";
    }

    

    //FÉRIAS PROPORCIONAIS
    $dados_recisao[$row_rescisao['id_recisao']]["dados"][65] = array(
        "movimento" => "Férias Proporcionais " . sprintf('%02d', $row_rescisao['avos_fp']) . "/12 avos<br>{$periodo_aquisitivo_fp}",
        "tipo" => "CREDITO",
        "valor" => $row_rescisao['ferias_pr']
    );

    //FÉRIAS VENCIDAS
    $texto_fv = "Férias Vencidas <br /> Per. Aquisitivo ";

    if ($row_rescisao['ferias_vencidas'] != '0.00') {
        $texto_fv .= '12/12 avos';
        $texto_fv .= 'de ' . formato_brasileiro($row_rescisao['fv_data_ini']) . " à " . formato_brasileiro($row_rescisao['fv_data_fim']) . "<br>";
    } else {
        $texto_fv .= '0/12 avos';
    }

    if (!empty($row_rescisao['qnt_faltas_ferias_fv'])) {
        $texto_fv .= "<span>( Faltas: " . $row_rescisao['qnt_faltas_ferias_fv'] . ")</span>";
    }

    $dados_recisao[$row_rescisao['id_recisao']]["dados"][66] = array(
        "movimento" => $texto_fv,
        "tipo" => "CREDITO",
        "valor" => $row_rescisao['ferias_vencidas']
    );

    //TERÇO CONSTITUCIONAL DE FERIAS
    $dados_recisao[$row_rescisao['id_recisao']]["dados"][68] = array(
        "movimento" => "Terço Constitucional de Férias",
        "tipo" => "CREDITO",
        "valor" => $row_rescisao['umterco_fv'] + $row_rescisao['umterco_fp']
    );

    //13° SALÁRIO (AVISO PREVIO INDENIZADO)
    $dados_recisao[$row_rescisao['id_recisao']]["dados"][70] = array(
        "movimento" => "13º Salário (Aviso-Prévio Indenizado 1/12 avos)",
        "tipo" => "CREDITO",
        "valor" => $row_rescisao['terceiro_ss']
    );

    //13° SALÁRIO (AVISO PREVIO INDENIZADO)
    $dados_recisao[$row_rescisao['id_recisao']]["dados"][71] = array(
        "movimento" => "Férias (Aviso-Prévio Indenizado 1/12 avos)",
        "tipo" => "CREDITO",
        "valor" => $row_rescisao['ferias_aviso_indenizado']
    );

    //FÉRIAS EM DOBRO
    $dados_recisao[$row_rescisao['id_recisao']]["dados"][72] = array(
        "movimento" => "Férias em dobro",
        "tipo" => "CREDITO",
        "valor" => $row_rescisao['fv_dobro']
    );

    //1/3 FÉRIAS EM DOBRO
    $dados_recisao[$row_rescisao['id_recisao']]["dados"][73] = array(
        "movimento" => "1/3 férias em dobro",
        "tipo" => "CREDITO",
        "valor" => $row_rescisao['um_terco_ferias_dobro']
    );

    //1/3 FÉRIAS EM DOBRO
    $dados_recisao[$row_rescisao['id_recisao']]["dados"][75] = array(
        "movimento" => "1/3 Férias (Aviso Prévio Indenizado)",
        "tipo" => "CREDITO",
        "valor" => $row_rescisao['umterco_ferias_aviso_indenizado']
    );

    //AJUSTE DE SALDO DEVEDOR
    $dados_recisao[$row_rescisao['id_recisao']]["dados"][99] = array(
        "movimento" => "Ajuste do Saldo Devedor",
        "tipo" => "CREDITO",
        "valor" => $row_rescisao['arredondamento_positivo']
    );

    //ADIANTAMENTO DE 13° SALÁRIO
    $dados_recisao[$row_rescisao['id_recisao']]["dados"][101] = array(
        "movimento" => "Adiantamento Salarial",
        "tipo" => "DEBITO",
        "valor" => $row_rescisao['adiantamento']
    );

    //ADIANTAMENTO DE 13° SALÁRIO
    $dados_recisao[$row_rescisao['id_recisao']]["dados"][102] = array(
        "movimento" => "Adiantamento de 13º Salário",
        "tipo" => "DEBITO",
        "valor" => $adiantamento_13
    );

    //ADIANTAMENTO DE 13° SALÁRIO
    $dados_recisao[$row_rescisao['id_recisao']]["dados"][105] = array(
        "movimento" => "Empréstimo em Consignação",
        "tipo" => "DEBITO",
        "valor" => 0.00
    );

    //PREVIDÊNCIA SOCIAL
    $dados_recisao[$row_rescisao['id_recisao']]["dados"]["112.1"] = array(
        "movimento" => "Previdência Social",
        "tipo" => "DEBITO",
        "valor" => $row_rescisao['inss_ss']
    );

    //PREVIDÊNCIA SOCIAL 13 SALARIO
    $dados_recisao[$row_rescisao['id_recisao']]["dados"]["112.2"] = array(
        "movimento" => "Previdência Social - 13º Salário",
        "tipo" => "DEBITO",
        "valor" => $row_rescisao['inss_dt']
    );

    //IRRF
    $dados_recisao[$row_rescisao['id_recisao']]["dados"]["114.1"] = array(
        "movimento" => "IRRF",
        "tipo" => "DEBITO",
        "valor" => $row_rescisao['ir_ss']
    );

    //IRRF
    $dados_recisao[$row_rescisao['id_recisao']]["dados"]["114.2"] = array(
        "movimento" => "IRRF sobre 13º Salário",
        "tipo" => "DEBITO",
        "valor" => $row_rescisao['ir_dt']
    );

    //IRRF
    $dados_recisao[$row_rescisao['id_recisao']]["dados"][116] = array(
        "movimento" => "IRRF Férias",
        "tipo" => "DEBITO",
        "valor" => $row_rescisao['ir_ferias']
    );

    //ORDERNANDO O ARRAY
    ksort($dados_recisao);
    
}    
    

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Relatório de Rescisão</title>
        <!--<link href="rescisao_1.css" rel="stylesheet" type="text/css" />-->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../resources/css/main.css" rel="stylesheet" media="all">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="all">
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="all">
        <link href="../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../resources/js/print.js" type="text/javascript"></script>
        <script type="text/javascript" src="/intranet/js/bootstrap-datepicker-1.4.0-dist/js/bootstrap-datepicker.js?8181"></script>
        <script type="text/javascript" src="/intranet/js/bootstrap-datepicker-1.4.0-dist/js/bootstrap-datepicker.min.js?8181"></script>
        <script type="text/javascript" src="/intranet/js/bootstrap-datepicker-1.4.0-dist/locales/bootstrap-datepicker.pt-BR.min.js?8181"></script>
        <style type="text/css" media="print">
            table.rescisao td.secao {
                background-color:#C0C0C0;
                text-align:center;
                font-size:14px;
                height:20px;
            }
            
            #imprimir{ display: none; }
            .print{ border: 0px !important;}
            
        </style>
        <script src="../js/printElement.js" type="text/javascript"></script>
        <script>
            $(function(){
                $("#imprimir").click(function(){
                    $('.print').printElement();
                });
            });
        </script>
    </head>
    <body>
        
        <?php include("../template/navbar_default.php"); ?>
        
        <div class="<?=($container_full) ? 'container-full' : 'container'?>">
            <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS<small> - Relatório de Rescisão</small></h2></div>
            <form action="" method="post" class="form-horizontal top-margin1" name="form1" id="form1">
                
                <?php if(isset($_SESSION['regiao'])){ ?>                
                <!--resposta de algum metodo realizado-->
                <div class="alert alert-<?php echo $_SESSION['MESSAGE_TYPE']; ?> msg_cadsuporte">
                    <button type="button" class="close" data-dismiss="alert">Ã—</button>
                    <p><?php echo $_SESSION['MESSAGE'];
                    session_destroy(); ?></p>
                </div>
                <?php } ?>
                
                <div class="panel panel-default hidden-print">
                    <div class="panel-heading">Relatório Detalhado</div>
                    <div class="panel-body">
                        <div class="form-group">
                            <label for="select" class="col-lg-2 control-label">Projeto:</label>
                            <div class="col-lg-8">
                                <?php echo montaSelect($global->carregaProjetosByRegiao($usuario['id_regiao']), $projeto, "id='projeto' name='projeto' class='required[custom[select]] form-control'"); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="select" class="col-lg-2 control-label">Mês:</label>
                            <div class="col-lg-8">
                                <div class="input-daterange input-group" id="bs-datepicker-range">
                                    <?php echo montaSelect(mesesArray(), $mes, "id='mes' name='mes' class='required[custom[select]] form-control'"); ?>
                                    <span class="input-group-addon">Ano</span>
                                    <?php echo montaSelect(AnosArray(null,null), 2016, "id='ano' name='ano' class='required[custom[select]] form-control'"); ?>                                
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
<!--                            <label for="box" class="col-lg-2 control-label"></label>
                            <div class="col-lg-2">
                                <input type="radio" name="filtroTipo" id="filtroTipo" value="1" checked="checked" /> Participantes
                            </div>
                            <div class="col-lg-2">
                                <input type="radio" name="filtroTipo" id="filtroTipo" value="2" /> Unidade
                            </div>-->
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <?php if(!empty($dados_recisao)){ ?>    
                            <button type="button" name="imprimir" id="imprimir" value="Imprimir"  class="btn btn-success"><span class="fa fa-print"></span> Imprimir</button>
                            <button type="button" form="formPdf" name="pdf" data-title="Relatório de Desconto de FGTS" data-id="table_excel" id="pdf" value="Gerar PDF" class="btn btn-danger" ><i class="fa fa-file-pdf-o"></i> Gerar PDF</button>
                        <?php } ?>
                            <button type="submit" name="filtrar" id="filt" value="Filtrar" class="btn btn-primary"><span class="fa fa-filter"></span> Gerar</button>
                    </div>
                </div>
            </form>
        </div>    
    
        <div class="<?=($container_full) ? 'container-full' : 'container'?>">
        
        <?php if(!empty($dados_recisao)){ ?>    
            <?php foreach ($dados_recisao as $key => $values){ ?>
            <div class="print" style="border-top: 2px dotted #ccc; padding: 20px 0px; page-break-after: always; clear: both;">
                <p><b>Nome: </b><?php echo $values['nome']; ?></p>
                <p><b>Unidade: </b><?php echo $values['locacao']; ?></p>
                <p><b>Salário Base: </b><?php echo "R$ " . $values['sal_base']; ?></p>
                <p><b>Hora/Mês: </b><?php echo $values['hora_mensal']; ?></p>
                </br>
                <table class="table table-bordered table-hover table-condensed text-sm valign-middle" id="table_excel">
                    <thead>
                        <tr>
                            <th>Rúbrica</th>
                            <th>Crédito</th>
                            <th>Débito</th>
                        </tr>
                    </thead>    
                    <tbody>
                        <?php $totalCredito = 0; $totalDebito = 0; ?>
                        <?php foreach ($values['dados'] as $key => $value){ ?>

                            <?php if($value['valor'] != 0){ ?>
                                <tr>
                                    <td><?php echo $value['movimento']; ?></td>
                                    <td>
                                        <?php if($value['tipo'] == "CREDITO"){ ?>
                                            <?php echo "R$ " . number_format($value['valor'],2,',','.'); ?>
                                            <?php $totalCredito += $value['valor']; ?>
                                        <?php } ?>
                                    </td>
                                    <td>
                                        <?php if($value['tipo'] == "DEBITO"){ ?>
                                            <?php echo "R$ " . number_format($value['valor'],2,',','.'); ?>
                                            <?php $totalDebito += $value['valor']; ?>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        <?php } ?>

                        <?php 

                        $sql_mov = "SELECT B.descicao, B.id_mov, A.valor,B.categoria, C.tipo_movimento, C.valor_movimento, B.campo_rescisao, B.percentual, A.tipo_qnt, A.qnt, C.qnt_horas
                                    FROM rh_movimentos_rescisao AS A
                                    LEFT JOIN rh_movimentos AS B ON(A.id_mov = B.id_mov)
                                    LEFT JOIN (SELECT * FROM rh_movimentos_clt AS A WHERE A.id_clt = '{$values['id']}' AND A.status = 1) AS C ON(B.id_mov = C.id_mov)
                                    WHERE A.id_clt = '{$values['id']}' 
                                    AND A.status = 1 {$and_complementar} GROUP BY A.id_mov"; //AND C.id_clt = '{$row_rescisao[id_clt]}' AND C.status = '1'

                        $qr_movimentos = mysql_query($sql_mov) or die(mysql_error());

                        ?>

                        <?php while($res_outros_movimentos = mysql_fetch_assoc($qr_movimentos)){ ?>

                            <tr>
                                <td><?php echo $res_outros_movimentos['descicao']; ?></td>
                                <td>
                                    <?php if($res_outros_movimentos['categoria'] == "CREDITO"){ ?>
                                        <?php echo "R$ " . number_format($res_outros_movimentos['valor'],2,',','.'); ?>
                                        <?php $totalCredito += $res_outros_movimentos['valor']; ?>
                                    <?php } ?>
                                </td>
                                <td>
                                    <?php if($res_outros_movimentos['categoria'] == "DEBITO"){ ?>
                                        <?php echo "R$ " . number_format($res_outros_movimentos['valor'],2,',','.'); ?>
                                        <?php $totalDebito += $res_outros_movimentos['valor']; ?>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                        <?php unset($qr_movimentos); ?>

                        <tr>
                            <td><span style="float: right">Totais: </span></td>
                            <td><?php echo number_format($totalCredito,2,',','.'); ?></td>
                            <td><?php echo number_format($totalDebito,2,',','.'); ?></td>
                        </tr>
                        <tr>
                            <td><span style="float: right">Líquido:</span></td>
                            <?php $liquido = (($totalCredito - $totalDebito)) < 0 ? 0 : $totalCredito - $totalDebito; ?>
                            <td colspan="2"><?php echo number_format($liquido,2,',','.'); ?></td>
                        </tr>
                    </tbody>
                </table>
                </div>
            <?php } ?>
        <?php }else{ ?>
            <div id="message-box" class="alert alert-danger">
                <span class="fa fa-exclamation-triangle"></span> Nenhuma rescisão encontrada.
            </div>
        <?php } ?>    
        <?php include('../template/footer.php'); ?>
            
        </div>
                    
    <script src="../js/jquery-1.10.2.min.js"></script>
    <script src="../js/jquery-ui-1.9.2.custom.min.js"></script>
    <script src="../resources/js/bootstrap.min.js"></script>
    <script src="../resources/js/tooltip.js"></script>
    <script src="../resources/js/main.js"></script>
    <script src="../js/global.js"></script>
    <script>
        $(function(){
           $("body").on("click","input[name='filtroTipo']",function(){
                var valor = $(this).val();
                if(valor == 2){
                    $(".linhasParticipantes").hide();
                }else{
                    $(".linhasParticipantes").show();
                }
           });
        });
    </script>
    </body>
    
</html>
    
