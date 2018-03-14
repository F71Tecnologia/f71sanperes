<?php

include('../../conn.php');
include './../../wfunction.php';

if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='login.php'>Logar</a>";
    exit;
    
}

$usuario = carregaUsuario();
$master = $usuario['id_master'];
$id_regiao = $usuario['id_regiao'];

// observacoes individuais
$resultOBS = mysql_query("SELECT *
                        FROM rh_clt
                        WHERE id_regiao = {$id_regiao} AND observacao != '' AND STATUS < '60'
                        ORDER BY nome ASC") or die(mysql_error());
$total_obs = mysql_num_rows($resultOBS);

// acidente de trabalho
$sql_acidente = mysql_query("SELECT B.id_clt, B.nome, DATE_FORMAT(A.data, '%d/%m/%Y') AS data_saida, 
                        DATE_FORMAT(A.data_retorno, '%d/%m/%Y') AS data_retorno
                        FROM rh_eventos AS A
                        LEFT JOIN rh_clt AS B ON (B.id_clt = A.id_clt)
                        WHERE B.id_regiao = {$id_regiao} AND A.cod_status = 70 AND data_retorno > CURRENT_DATE
                        LIMIT 5") or die(mysql_error());
$total_acidente = mysql_num_rows($sql_acidente);

// licencas
$sql_licencas = mysql_query("SELECT DATE_FORMAT(A.data, '%d/%m/%Y') AS inicio_licenca, 
                        DATE_FORMAT(A.data_retorno, '%d/%m/%Y') AS fim_licenca, B.nome, B.id_clt, A.nome_status
                        FROM rh_eventos AS A
                        LEFT JOIN rh_clt AS B ON (A.id_clt = B.id_clt)
                        WHERE B.id_regiao = {$id_regiao} AND B.status IN('20','30','50','51','52','90','100','80','110')
                        AND A.data_retorno > CURRENT_DATE()") or die(mysql_error());
$total_licencas = mysql_num_rows($sql_licencas);

//aquisicao de ferias
$sql_aquisicao = mysql_query("SELECT B.id_clt, A.nome, A.id_clt, B.`status`, A.regiao, 
                        DATE_FORMAT(A.data_ini, '%d/%m/%Y') AS data_ini, 
                        DATE_FORMAT(A.data_fim, '%d/%m/%Y') AS data_fim,
                        (SELECT C.especifica FROM rhstatus C WHERE C.codigo = B.status) AS situacao
                        FROM rh_ferias AS A
                        LEFT JOIN rh_clt AS B ON (A.id_clt = B.id_clt)
                        WHERE B.status < '60'
                        AND (LEFT(A.data_ini, 7) >= LEFT(CURRENT_DATE(), 7))
                        AND (MONTH(A.data_ini) <= MONTH(CURDATE()) + 3)
                        AND A.regiao = {$id_regiao}
                        ORDER BY A.data_ini") or die(mysql_error());
$total_aquisicao = mysql_num_rows($sql_aquisicao);        

//retorno de ferias
$sql_retorno = mysql_query("SELECT B.id_clt, A.nome, A.id_clt, 
                        DATE_FORMAT(A.data_ini, '%d/%m/%Y') AS data_ini, 
                        DATE_FORMAT(A.data_retorno, '%d/%m/%Y') AS data_retorno
                        FROM rh_ferias AS A
                        LEFT JOIN rh_clt AS B ON (A.id_clt = B.id_clt)
                        WHERE B.status = '40' AND A.data_fim >= CURRENT_DATE 
                        AND MONTH(A.data_fim) = MONTH(CURDATE()) AND A.regiao = {$id_regiao}
                        ORDER BY A.data_fim") or die(mysql_error());
$total_retorno = mysql_num_rows($sql_retorno);

//contribuicao sindical
$sql_contribuicao = mysql_query("SELECT *
                        FROM rhsindicato
                        WHERE id_regiao = '$id_regiao' AND STATUS = '1'") or die(mysql_error());
$total_contribuicao = mysql_num_rows($sql_contribuicao);

//contratos em experiencia
$sql_experiencia = mysql_query("SELECT *, if(experiencia_c >= CURRENT_DATE(),'ok','false') as Intervalo, 
                        DATE_FORMAT(experiencia_c,'%d/%m/%Y') AS experiencia_d FROM (
                        SELECT id_clt, nome, DATE_FORMAT(data_entrada, '%d/%m/%Y') AS data_entrada,
                        DATE_FORMAT(DATE_ADD(data_entrada, INTERVAL 45 DAY), '%d/%m/%Y') AS experiencia_a,
                        DATE_FORMAT(DATE_ADD(data_entrada, INTERVAL 60 DAY), '%d/%m/%Y') AS experiencia_b,
                        DATE_ADD(data_entrada, INTERVAL 90 DAY) AS experiencia_c
                        FROM rh_clt
                        WHERE STATUS < '60' AND id_regiao = 48
                        ) AS sub
                        HAVING Intervalo = 'ok'") or die(mysql_error());
$total_experiencia = mysql_num_rows($sql_experiencia);

//cbo
$sql_cbo = mysql_query("SELECT A.nome, C.regiao
                        FROM curso AS A
                        LEFT JOIN rh_cbo B ON (B.id_cbo = A.cbo_codigo)
                        LEFT JOIN regioes C ON (A.id_regiao = C.id_regiao)
                        WHERE (A.cbo_codigo IS NULL OR A.cbo_codigo = '')
                        AND C.id_regiao = {$id_regiao}
                        GROUP BY A.nome
                        ORDER BY A.nome") or die(mysql_error());
$total_cbo = mysql_num_rows($sql_cbo);

//erros de cadastro
$sql_cpf = mysql_query("SELECT A.*, B.nome AS nome_projeto
                        FROM rh_clt AS A
                        LEFT JOIN projeto AS B ON A.id_projeto = B.id_projeto
                        WHERE A.id_regiao = {$id_regiao}
                        ORDER BY A.nome") or die(mysql_error());
                        
$sql_pis = mysql_query("SELECT A.*, B.nome AS nome_projeto
                        FROM rh_clt AS A
                        LEFT JOIN projeto AS B ON A.id_projeto = B.id_projeto
                        WHERE A.id_regiao = {$id_regiao}
                        ORDER BY A.nome") or die(mysql_error());
$num_cpf = 0;
$num_pis = 0;
$doc_cpf = 'CPF';
$doc_pis = 'PIS';

//horarios trabalhados
$sql_horarios = mysql_query("SELECT A.*, B.*, A.nome AS nome_clt, C.nome AS nome_projeto
                        FROM rh_clt AS A
                        LEFT JOIN rh_horarios AS B ON (A.rh_horario = B.id_horario)
                        LEFT JOIN projeto AS C ON (A.id_projeto = C.id_projeto)
                        WHERE A.id_regiao = {$id_regiao} AND (B.horas_semanais = 0 OR B.id_horario IS NULL)
                        ORDER BY A.nome") or die(mysql_error());
$total_horarios = mysql_num_rows($sql_horarios);

//eventos não finalizados
$sql_nao_finalizados = mysql_query("SELECT A.id_clt, A.nome, B.nome_status, B.data_retorno, A.id_projeto AS projeto
                                        FROM rh_clt AS A
                                        LEFT JOIN rh_eventos AS B
                                        ON B.id_clt = A.id_clt
                                        WHERE A.status NOT IN (10, 60, 61, 62, 63, 64, 65, 66, 81, 101)
                                        AND B.data_retorno <= NOW()
                                        AND B.nome_status IS NOT NULL
                                        AND B.status = 1
                                        AND B.cod_status NOT IN (10, 60, 61, 62, 63, 64, 65, 66, 81, 101, 991, 992, 994)
                                        AND A.data_demi IS NULL
                                        AND B.id_evento IN (SELECT MAX(id_evento) FROM rh_eventos WHERE id_clt IN (A.id_clt))
                                        AND A.id_regiao = {$id_regiao}
                                        GROUP BY A.id_clt
                                        ORDER BY A.nome
                                    ");

$total_nao_finalizados = mysql_num_rows($sql_nao_finalizados);

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Gerenciamento de Avisos e Notificações");
$breadcrumb_pages = array("Gestão de RH" => "../../rh");
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: Gerenciamento de Avisos e Notificações</title>
        <link href="../../favicon.png" rel="shortcut icon" />
        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-xs-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS<small> - Gerenciamento de Avisos e Notificações</small></h2></div>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-3">
                    <div class="panel-group" id="accordion">
                        <div class="panel panel-default">
                            <div class="panel-heading pointer">
                                <h4 class="panel-title" data-toggle="collapse" data-parent="#accordion" href="#collapse2">
                                    Observações Individuais <?php if ($total_obs != '0') { ?><span class="badge badge-info pull-right"><?=$total_obs?></span><?php } ?>
                                </h4>
                            </div>
                            <div class="panel-heading pointer">
                                <h4 class="panel-title" data-toggle="collapse" data-parent="#accordion" href="#collapse3">
                                    Acidente de Trabalho <?php if ($total_acidente != '0') { ?><span class="badge badge-info pull-right"><?=$total_acidente?></span><?php } ?>
                                </h4>
                            </div>
                            <div class="panel-heading pointer">
                                <h4 class="panel-title" data-toggle="collapse" data-parent="#accordion" href="#collapse4">
                                    Licenças <?php if ($total_licencas != '0') { ?><span class="badge badge-info pull-right"><?=$total_licencas?></span><?php } ?>
                                </h4>
                            </div>
                            <div class="panel-heading pointer">
                                <h4 class="panel-title" data-toggle="collapse" data-parent="#accordion" href="#collapse5">
                                    Aquisição de Férias <?php if ($total_aquisicao != '0') { ?><span class="badge badge-info pull-right"><?=$total_aquisicao?></span><?php } ?>
                                </h4>
                            </div>
                            <div class="panel-heading pointer">
                                <h4 class="panel-title" data-toggle="collapse" data-parent="#accordion" href="#collapse6">
                                    Retorno de Férias <?php if ($total_retorno != '0') { ?><span class="badge badge-info pull-right"><?=$total_retorno?></span><?php } ?>
                                </h4>
                            </div>
                            <div class="panel-heading pointer">
                                <h4 class="panel-title" data-toggle="collapse" data-parent="#accordion" href="#collapse7">
                                    Contribuição Sindical <?php if ($total_contribuicao != '0') { ?><span class="badge badge-info pull-right"><?=$total_contribuicao?></span><?php } ?>
                                </h4>
                            </div>
                            <div class="panel-heading pointer">
                                <h4 class="panel-title" data-toggle="collapse" data-parent="#accordion" href="#collapse8">
                                    Contratos em Experiência <?php if ($total_experiencia != '0') { ?><span class="badge badge-info pull-right"><?=$total_experiencia?></span><?php } ?>
                                </h4>
                            </div>
                            <div class="panel-heading pointer">
                                <h4 class="panel-title" data-toggle="collapse" data-parent="#accordion" href="#collapse14">
                                    CBO <?php if ($total_cbo != '0') { ?><span class="badge badge-info pull-right"><?=$total_cbo?></span><?php } ?>
                                </h4>
                            </div>
                            <div class="panel-heading pointer">
                                <h4 class="panel-title" data-toggle="collapse" data-parent="#accordion" href="#collapse9">
                                    Horários trabalhados <?php if ($total_horarios != '0') { ?><span class="badge badge-info pull-right"><?=$total_horarios?></span><?php } ?>
                                </h4>
                            </div>
                            <div class="panel-heading pointer">
                                <h4 class="panel-title" data-toggle="collapse" data-parent="#accordion" href="#collapse10">
                                    Não Finalizados <?php if ($total_nao_finalizados != '0') { ?><span class="badge badge-info pull-right"><?=$total_nao_finalizados?></span><?php } ?>
                                </h4>
                            </div>
                            <div class="panel-heading pointer">
                                <h4 class="panel-title" data-toggle="collapse" data-parent="#accordion" href="#collapse13">
                                    Erros de Cadastro <?php $total_cpf = "<span id='resposta'></span>"; ?><span class="badge badge-info pull-right"><?=$total_cpf?></span>
                                </h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-9">
                    <div id="collapse1" class="panel-collapse collapse">
                        <div class="panel-body">
                            <?php if ($total_previo == '0') { ?>
                                <div id="message-box" class="alert alert-warning">Nenhuma Notificação Encontrada</div>
                            <?php } else { ?>
                                <table class="table table-condensed table-hover table-striped">
                                    <thead>
                                        <tr class="bg-primary valign-middle text-center">
                                            <th>COD</th>
                                            <th>NOME</th>
                                            <th>IN&Iacute;CIO DO AVISO</th>
                                            <th>T&Eacute;RMINO DO AVISO</th>
                                        </tr>
                                    </thead>
                                    <?php while ($res_aviso = mysql_fetch_assoc($sql_previo)) { ?>
                                        <tbody>
                                            <tr>
                                                <td><?=$res_aviso['id_clt']?></td>
                                                <td><?=$res_aviso['nome']?></td>
                                                <td><?=$res_aviso['inicio_aviso']?></td>
                                                <td><?=$res_aviso['fim_aviso']?></td>
                                            </tr>
                                        </tbody>
                                    <?php } ?>
                                </table>
                            <?php } ?>
                        </div>
                    </div>
                    <div id="collapse2" class="panel-collapse collapse in">
                        <div class="panel-body">
                            <?php if ($total_obs == '0') { ?>
                                <div id="message-box" class="alert alert-warning">Nenhuma Notificação Encontrada</div>
                            <?php } else { ?>
                                <p id="legenda_item2"><span class="marca_azul">*</span> Clique sobre o nome para editar o cadastro de CLT</p>
                                <table id="table_res"  name="table_res" class="table table-condensed table-hover table-striped">
                                    <thead>
                                        <tr class="bg-primary valign-middle text-center">
                                            <th>COD</th>
                                            <th>NOME</th>
                                            <th>OBSERVA&Ccedil;&Otilde;ES</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($rowOBS = mysql_fetch_array($resultOBS)) { ?>
                                            <tr class="valign-middle">
                                                <td><?=$rowOBS['id_clt']?></td>
                                                <td><a href='../alter_clt.php?clt=$rowOBS[id_clt]&pro=$rowOBS[id_projeto]&pagina=clt' class='linkBlack'><?=$rowOBS[nome]?></a></td>
                                                <td><?=$rowOBS['observacao']?></td>
                                            </tr>
                                        <?php } ?>       
                                    </tbody>
                                </table>
                            <?php } ?>
                        </div>
                    </div>
                    <div id="collapse3" class="panel-collapse collapse">
                        <div class="panel-body">
                            <?php if ($total_acidente == '0') { ?>
                                <div id="message-box" class="alert alert-warning">Nenhuma Notificação Encontrada</div>
                            <?php } else { ?>
                                <table class="table table-condensed table-hover table-striped">
                                    <thead>
                                        <tr class="bg-primary valign-middle text-center">
                                            <th>COD</th>
                                            <th>NOME</th>
                                            <th>DATA SA&Iacute;DA</th>
                                            <th>DATA RETORNO</th>
                                        </tr>
                                    </thead>
                                    <?php while ($res_acidente = mysql_fetch_assoc($sql_acidente)) { ?>
                                        <tbody>
                                            <tr class="valign-middle">
                                                <td><?= $res_acidente['id_clt'] ?></td>
                                                <td><?= $res_acidente['nome'] ?></td>
                                                <td><?= $res_acidente['data_saida'] ?></td>
                                                <td><?= $res_acidente['data_retorno'] ?></td>
                                            </tr>
                                        </tbody>
                                    <?php } ?>
                                </table>
                            <?php } ?>
                        </div>
                    </div>
                    <div id="collapse4" class="panel-collapse collapse">
                        <div class="panel-body">
                            <?php if ($total_licencas == '0') { ?>
                                <div id="message-box" class="alert alert-warning">Nenhuma Notificação Encontrada</div>
                            <?php } else { ?>
                                <table class="table table-condensed table-hover table-striped">
                                    <thead>
                                        <tr class="bg-primary valign-middle text-center">
                                            <th>COD</th>
                                            <th>NOME</th>
                                            <th>TIPO DE LICEN&Ccedil;A</th>
                                            <th>IN&Iacute;CIO DA LICEN&Ccedil;A</th>
                                            <th>T&Eacute;RMINO DA LICEN&Ccedil;A</th>
                                        </tr>
                                    </thead>
                                    <?php while ($res_licensa = mysql_fetch_array($sql_licencas)) { ?>
                                        <tbody>
                                            <tr class="valign-middle">
                                                <td><?= $res_licensa['id_clt'] ?></td>
                                                <td><?= $res_licensa['nome'] ?></td>
                                                <td><?= $res_licensa['nome_status'] ?></td>
                                                <td><?= $res_licensa['inicio_licenca'] ?></td>
                                                <td><?= $res_licensa['fim_licenca'] ?></td>
                                            </tr>
                                        </tbody>
                                    <?php } ?>
                                </table>
                            <?php } ?>
                        </div>
                    </div>
                    <div id="collapse5" class="panel-collapse collapse">
                        <div class="panel-body">
                            <?php if ($total_aquisicao == '0') { ?>
                                <div id="message-box" class="alert alert-warning">Nenhuma Notificação Encontrada</div>
                            <?php } else { ?>
                                <table class="table table-condensed table-hover table-striped">
                                    <thead>
                                        <tr class="bg-primary valign-middle text-center">
                                            <th>COD</th>
                                            <th>NOME</td>
                                            <th>SITUA&Ccedil;&Atilde;O ATUAL</th>                                    
                                            <th>DATA DE AQUISI&Ccedil;&Atilde;O</th>
                                            <th>VENCIMENTO DO PERÍODO</th>
                                        </tr>
                                    </thead>
                                    <?php while ($res_aquisicao = mysql_fetch_assoc($sql_aquisicao)) { ?>                                
                                        <tbody>
                                            <tr class="valign-middle">
                                                <td><?=$res_aquisicao['id_clt'] ?></td>
                                                <td><?=$res_aquisicao['nome'] ?></td>
                                                <td><?=$res_aquisicao['situacao'] ?></td>
                                                <td><?=$res_aquisicao['data_ini'] ?></td>
                                                <td><?=$res_aquisicao['data_fim'] ?></td>
                                            </tr>
                                        </tbody>
                                    <?php } ?>
                                </table>
                            <?php } ?>
                        </div>
                    </div>
                    <div id="collapse6" class="panel-collapse collapse">
                        <div class="panel-body">
                            <?php if ($total_retorno == '0') { ?>
                                <div id="message-box" class="alert alert-warning">Nenhuma Notificação Encontrada</div>
                            <?php } else { ?>
                                <table class="table table-condensed table-hover table-striped">
                                    <thead>
                                        <tr class="bg-primary valign-middle text-center">
                                            <th>COD</th>
                                            <th>NOME</th>
                                            <th>DATA SAIDA</th>
                                            <th>DATA RETORNO</th>
                                        </tr>
                                    </thead>                                                        
                                    <?php while ($res_retorno = mysql_fetch_assoc($sql_retorno)) { ?>
                                        <tbody>
                                            <tr class="valign-middle">
                                                <td><?=$res_retorno['id_clt']; ?></td>
                                                <td><?=$res_retorno['nome']; ?></td>
                                                <td><?=$res_retorno['data_ini']; ?></td>
                                                <td><?=$res_retorno['data_retorno']; ?></td>
                                            </tr>
                                        </tbody>
                                    <?php } ?>
                                </table>
                            <?php } ?>
                        </div>
                    </div>
                    <div id="collapse7" class="panel-collapse collapse">
                        <div class="panel-body">
                            <?php if ($total_contribuicao == '0') { ?>
                                <div id="message-box" class="alert alert-warning">Nenhuma Notificação Encontrada</div>
                            <?php } else { ?>
                                <table class="table table-condensed table-hover table-striped">
                                    <thead>
                                        <tr class="bg-primary valign-middle text-center">
                                            <th>SINDICATO</th>
                                            <th>PAGAMENTO (M&Ecirc;S)</th>
                                        </tr>
                                    </thead>
                                    <?php while ($sindicatos = mysql_fetch_assoc($sql_contribuicao)) { ?>
                                        <tbody>
                                            <tr class="valign-middle">
                                                <td><?= $sindicatos['nome'] ?></td>
                                                <td>                                    
                                                    <?php
                                                    $qr_clt_sindicatos = mysql_query("SELECT * FROM rh_clt WHERE rh_sindicato = '$sindicatos[id_sindicato]' AND status IN('10','40','50','51','52','30','110')");
                                                    $numero_clt_sindicatos = mysql_num_rows($qr_clt_sindicatos);
                                                    $ContribuicaoFinal = 0;

                                                    while ($clt_sindicatos = mysql_fetch_assoc($qr_clt_sindicatos)) {
                                                        $GetSalario = mysql_query("SELECT salario FROM curso WHERE id_curso = '$clt_sindicatos[id_curso]'");
                                                        $Salario = mysql_fetch_array($GetSalario);
                                                        $SalarioCalc = $Salario['salario'];
                                                        $SalarioSoma = $SalarioCalc / 30;
                                                        $ContribuicaoFinal = $SalarioSoma + $ContribuicaoFinal;
                                                    }

                                                    echo "R$ " . number_format($ContribuicaoFinal, 2, ",", "") . "";
                                                    $meses = array("", "Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro");

                                                    for ($i = 0; $i <= 12; $i++) {
                                                        if ($sindicatos['mes_desconto'] == $i) {
                                                            echo " ($meses[$i])";
                                                        }
                                                    } ?>
                                                </td> 
                                            </tr>
                                        </tbody>
                                    <?php } ?>
                                </table>
                            <?php } ?>
                        </div>
                    </div>
                    <div id="collapse8" class="panel-collapse collapse">
                        <div class="panel-body">
                            <?php if ($total_experiencia == '0') { ?>
                                <div id="message-box" class="alert alert-warning">Nenhuma Notificação Encontrada</div>
                            <?php } else { ?>
                                <table class="table table-condensed table-hover table-striped">
                                    <thead>
                                        <tr class="bg-primary valign-middle text-center">
                                            <th>COD</th>
                                            <th>NOME</th>
                                            <th>CONTRATADO EM</th>
                                            <th>FIM DA EXPERI&Ecirc;NCIA (45 dias)</th>
                                            <th>FIM DA EXPERI&Ecirc;NCIA (60 dias)</th>
                                            <th>FIM DA EXPERI&Ecirc;NCIA (90 dias)</th>
                                        </tr>
                                    </thead>
                                    <?php while ($res_experiencia = mysql_fetch_assoc($sql_experiencia)) { ?>
                                        <tbody>
                                            <tr class="valign-middle">
                                                <td><?=$res_experiencia['id_clt']; ?></td>
                                                <td><?=$res_experiencia['nome']; ?></td>
                                                <td><?=$res_experiencia['data_entrada']; ?></td>
                                                <td><?=$res_experiencia['experiencia_a']; ?></td>
                                                <td><?=$res_experiencia['experiencia_b']; ?></td>
                                                <td><?=$res_experiencia['experiencia_d']; ?></td>
                                            </tr>
                                        </tbody>
                                    <?php } ?>
                                </table>
                            <?php } ?>
                        </div>
                    </div>
                    <div id="collapse9" class="panel-collapse collapse">
                        <div class="panel-body">
                            <?php if ($total_horarios == '0') { ?>
                                <div id="message-box" class="alert alert-warning">Nenhuma Notificação Encontrada</div>
                            <?php } else { ?>
                                <table class="table table-condensed table-hover table-striped">
                                    <thead>
                                        <tr class="bg-primary valign-middle text-center">
                                            <th>COD</th>
                                            <th>NOME</th>
                                            <th>PROJETO</th>                                    
                                        </tr>
                                    </thead>
                                    <?php while ($res_horarios = mysql_fetch_assoc($sql_horarios)) { ?>
                                        <tbody>
                                            <tr class="valign-middle">
                                                <td><?=$res_horarios['id_clt']; ?></td>
                                                <td><?="<a href='../alter_clt.php?clt={$res_horarios['id_clt']}&pro={$res_horarios['projeto']}&pagina=clt' class='linkBlack'>{$res_horarios['nome_clt']}</a>" ?></td>
                                                <td><?=$res_horarios['nome_projeto']; ?></td>
                                            </tr>
                                        </tbody>
                                    <?php } ?>
                                </table>
                            <?php } ?>
                        </div>
                    </div>
                    <div id="collapse10" class="panel-collapse collapse">
                        <div class="panel-body">
                            <?php if ($total_nao_finalizados == '0') { ?>
                                <div id="message-box" class="alert alert-warning">Nenhuma Notificação Encontrada</div>
                            <?php } else { ?>
                                <table class="table table-condensed table-hover table-striped">
                                    <thead>
                                        <tr class="bg-primary valign-middle text-center">
                                            <th>COD</th>
                                            <th>NOME</th>
                                            <th>TIPO DE LICENÇA</th>                                    
                                            <th>DATA RETORNO</th>
                                        </tr>
                                    </thead>
                                    <?php while ($res_nao_finalizados = mysql_fetch_assoc($sql_nao_finalizados)) { ?>
                                        <tbody>
                                            <tr class="valign-middle">
                                                <td><?=$res_nao_finalizados['id_clt']; ?></td>
                                                <td><?="<a href='../alter_clt.php?clt={$res_nao_finalizados['id_clt']}&pro={$res_nao_finalizados['projeto']}&pagina=clt' class='linkBlack'>{$res_nao_finalizados['nome']}</a>" ?></td>
                                                <td><?=$res_nao_finalizados['nome_status']; ?></td>
                                                <td><?=$res_nao_finalizados['data_retorno']; ?></td>
                                            </tr>
                                        </tbody>
                                    <?php } ?>
                                </table>
                            <?php } ?>
                        </div>
                    </div>
                    <div id="collapse13" class="panel-collapse collapse">
                        <div class="panel-body">
                            <?php if ($total_cpf == '0') { ?>
                                <div id="message-box" class="alert alert-warning">Nenhuma Notificação Encontrada</div>
                            <?php } else { ?>
                            <p id="legenda_item2"><span class="marca_azul">*</span> Clique sobre o nome para editar o cadastro de CLT</p>
                            <table class="table table-condensed table-hover table-striped">
                                <thead>
                                    <tr class="bg-primary valign-middle text-center">
                                        <th>DOCUMENTO</th>
                                        <th>NOME</th>
                                        <th>NÚMERO</th>
                                        <th>PROJETO</th>
                                    </tr>
                                    </thead>
                                    <tbody class="box_erro">
                                        <?php 
                                        while ($res_cpf = mysql_fetch_array($sql_cpf)) {

                                            $nome = strtoupper($res_cpf['nome']);
                                            $cpf_prov = $res_cpf['cpf'];
                                            $id = $res_cpf['id_clt'];
                                            $projeto = $res_cpf['id_projeto'];
                                            $cpf = validaCPF($cpf_prov);
                                            $projeto_cpf = $res_cpf['nome_projeto'];

                                            // Verifica a resposta da funÃ§Ã£o e exibe na tela
                                            if ($cpf == false) {
                                                $num_cpf++; ?>
                                                <tr class="valign-middle">
                                                    <td><?=$doc_cpf; ?></td>
                                                    <td><?="<a href='../alter_clt.php?clt={$id}&pro={$projeto}&pagina=clt' class='linkBlack'>{$nome}</a>" ?></td>
                                                    <td class="cpf_vermelho"><?=$cpf_prov; ?></td>     
                                                    <td><?=$projeto_cpf; ?></td>
                                                </tr>
                                            <?php }
                                        }
                                        while ($res_pis = mysql_fetch_array($sql_pis)) {
                                            $nome = strtoupper($res_pis['nome']);
                                            $pis_prov = $res_pis['pis'];
                                            $id = $res_pis['id_clt'];
                                            $projeto = $res_pis['id_projeto'];
                                            $pis = validaPIS($pis_prov);
                                            $projeto_pis = $res_pis['nome_projeto'];

                                            // Verifica a resposta da funÃ§Ã£o e exibe na tela
                                            if ($pis == false) {
                                                $num_pis++; ?>
                                                <tr class="valign-middle">
                                                    <td><?=$doc_pis; ?></td>
                                                    <td><?="<a href='../alter_clt.php?clt={$id}&pro={$projeto}&pagina=clt' class='linkBlack'>{$nome}</a>" ?></td>
                                                    <td class="cpf_vermelho"><?=$pis_prov; ?></td>
                                                    <td><?=$projeto_pis; ?></td>
                                                </tr>
                                            <?php }
                                        } ?>
                                    </tbody>
                                </table>
                            <?php } ?>
                            <?php $total_cadastro = $num_cpf + $num_pis ?>
                            <input type='hidden' name='valorTotal' id='valorTotal' value='<?=$total_cadastro; ?>' />
                        </div>
                    </div>
                    <div id="collapse14" class="panel-collapse collapse">
                        <div class="panel-body">
                            <table class="table table-condensed table-hover table-striped">
                                <thead>
                                    <tr class="bg-primary valign-middle text-center">
                                        <th>FUNÇÃO</th>
                                        <th>REGIÃO</th>                             
                                    </tr>
                                </thead>
                                <?php while ($res_cbo = mysql_fetch_array($sql_cbo)) { ?>
                                    <tbody>
                                        <tr class="valign-middle">
                                            <td><?=$res_cbo['nome']; ?></td>
                                            <td><?=$res_cbo['regiao']; ?></td>
                                        </tr>
                                    </tbody>
                                <?php } ?>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php include_once '../../template/footer.php'; ?>
        </div>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script>
            $(function(){
                var valor = $("#valorTotal").val();
                $("#resposta").html(valor);

                // oculta e exibe divs
                $("div[id!=item2]", "#dados_avisos").hide();

                $(".bt-menu").click(function() {
                    var $bt = $(this);
                    var id = '#item' + $bt.attr("data-item");
                    $("div[id^=item]").hide();

                    $(id).show();
                    $(".bt-menu").removeClass("aselected");
                    $bt.addClass("aselected");

                });                                

                $(".bt-menu").click(function() {
                    $(".j_verifica").each(function(){
                        if ($(this).hasClass("aberto")) {
                            $(this).css({color: "#333", background: "transparent"}).removeClass("aberto");
                        }
                    });
                    $(this).css({color: "#333", background: "#DDD"}).addClass("aberto");

                    // efeito zebra na tabela (global)
                    gridZebra(".table_res");
                });
                
                $(".panel-title").on("click", function(){
                    $(".collapse").removeClass('in');
                });
            });
        </script>
    </body>
</html>