<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='../login.php'>Logar</a> ";
    exit;
}

include "../conn.php";
include "../wfunction.php";

$usuario = carregaUsuario();
$user = $usuario['id_funcionario'];
$regiao = $usuario['id_regiao'];
$master = $usuario['id_master'];
$id_clt = $_REQUEST['clt'];
$tela = $_REQUEST['tela'];
$bloqueio = false;

$clt = montaQuery('rh_clt', '*', "id_clt = $id_clt");

/* VERIFICANDO SE O FUNCIONÁRIO ESTÁ NA FOLHA DE PAGAMENTO */
$query_folha = "select a.id_clt,b.id_folha,b.projeto,b.regiao 
            from rh_folha_proc as a
            inner join rh_folha as b on (a.id_folha = b.id_folha)
            where id_clt = '$id_clt'
            and b.ano = year(now()) 
            and b.mes = month(now())
            and a.status = 2
            and b.status = 2";
$result_folha = mysql_query($query_folha);
if (mysql_num_rows($result_folha)) {
    $bloqueio = true;
} else {
    if ($tela == 1) {
        /*
         * Tela 1: exibe unidade de origem, unidade de destino e botão para  confirmação
         * do desprocessamento da transferência.
         */

        // consultar ultima transferência da pessoa
        $query_trans = "select *,DATE_FORMAT(a.criado_em,'%d/%m/%Y %T') as criado_em,
                        (select regiao from regioes where id_regiao = a.id_regiao_de) as regiao_de, 
                        (select nome from projeto where id_projeto = a.id_projeto_de) as projeto_de,
                        (select nome from curso where id_curso = a.id_curso_de) as curso_de,
                        (select concat(id_horario,' - ',nome,' (',entrada_1,' - ',saida_1,' - ',entrada_2,' - ',saida_2,')') from rh_horarios where id_horario = a.id_horario_de) as horario_de,
                        (select nome from bancos where id_banco = a.id_banco_de) as banco_de,
                        (select nome from rhsindicato where id_sindicato = a.id_sindicato_de) as sindicato_de,
                        (select tipopg from tipopg where id_tipopg = a.id_tipo_pagamento_de) as tipopg_de,
                        (select regiao from regioes where id_regiao = a.id_regiao_para) as regiao_para, 
                        (select nome from projeto where id_projeto = a.id_projeto_para) as projeto_para,
                        (select nome from curso where id_curso = a.id_curso_para) as curso_para,
                        (select concat(id_horario,' - ',nome,' (',entrada_1,' - ',saida_1,' - ',entrada_2,' - ',saida_2,')') from rh_horarios where id_horario = a.id_horario_para) as horario_para,
                        (select nome from bancos where id_banco = a.id_banco_para) as banco_para,
                        (select nome from rhsindicato where id_sindicato = a.id_sindicato_para) as sindicato_para,
                        (select tipopg from tipopg where id_tipopg = a.id_tipo_pagamento_para) as tipopg_para
                        from rh_transferencias as a
                        where id_clt = '$id_clt' order by data_proc,id_transferencia DESC limit 1;";
        //echo $query_trans;
        $result_trans = mysql_query($query_trans) or die(mysql_error());
        $trans = mysql_fetch_array($result_trans);
    } else if ($tela == 2) {
        /*
         * Tela 2: realiza o desprocessamento e informa ao usuário que a transferência 
         * foi desprocessada
         */

        $id_transferencia = $_REQUEST['transferencia'];
        
// consultar ultima transferência da pessoa
        $query_trans2 = "select * from rh_transferencias where id_clt = '$id_clt' AND id_transferencia = '$id_transferencia' order by id_transferencia DESC limit 1;";
        $result_trans2 = mysql_query($query_trans2) or die(mysql_error());
        $trans2 = mysql_fetch_array($result_trans2);
// atualizar rh_clt com informações de origem da tranferência trasferência
        $updates = array(
            "id_regiao" => $trans2['id_regiao_de'],
            "id_projeto" => $trans2['id_projeto_de'],
            "id_curso" => $trans2['id_curso_de'],
            "rh_horario" => $trans2['id_horario_de'],
            "tipo_pagamento" => $trans2['id_tipo_pagamento_de'],
            "banco" => $trans2['id_banco_de'],
            "locacao" => $trans2['unidade_de'],
            "id_unidade" => $trans2['id_unidade_de'],
            "rh_sindicato" => $trans2['id_sindicato_de']
        );

        sqlUpdate("rh_clt", $updates, array("id_clt" => $id_clt));

        /* MUDAR O NOME DA FOTO */
        if ($clt[1]['foto'] == '1') {
            $dir = dirname(dirname(__FILE__)) . "/fotosclt/";
            $nomeOld = $trans2['id_regiao_para'] . "_" . $trans2['id_projeto_para'] . "_" . $id_clt . '.gif';
            $nomeNovo = $trans2['id_regiao_de'] . "_" . $trans2['id_projeto_de'] . "_" . $id_clt . '.gif';
            rename($dir . $nomeOld, $dir . $nomeNovo);
        }

// atualizar dependentes
        $sql = "UPDATE dependentes SET id_projeto='{$trans2['id_projeto_de']}', id_regiao='{$trans2['id_regiao_de']}'  where id_bolsista=" . $id_clt;
//    echo 'update: '.$sql;
        mysql_query($sql) or die(mysql_error());

// criar log com dados excluidos
        $insert_log = "INSERT INTO rh_transferencias_log 
(id_transferencia,id_clt,id_regiao_de,id_projeto_de,id_curso_de,id_horario_de,id_tipo_pagamento_de,id_banco_de,unidade_de,
id_unidade_de,id_sindicato_de,id_regiao_para,id_projeto_para,id_curso_para,id_horario_para,id_tipo_pagamento_para,id_banco_para,
unidade_para,id_unidade_para,id_sindicato_para,motivo,data_proc,criado_em,id_usuario,status,id_usuario_exclusao,data_exclusao)
SELECT *,'$user' AS id_usuario_exclusao, NOW() AS data_exclusao FROM rh_transferencias WHERE id_transferencia = '$id_transferencia'";
        mysql_query($insert_log) or die(mysql_error());
        
// excluir linha da tabela rh_transferencia
        $delete = "DELETE FROM rh_transferencias WHERE id_transferencia = '{$trans2['id_transferencia']}'";
        mysql_query($delete) or die(mysql_error());
    }
}

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Desprocessar Transferência");
$breadcrumb_pages = array("Lista Transferências"=>"lista_desprocessar_transf2.php");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Desprocessar Transferência</title>
        <link href="../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/bootstrap-note.css" rel="stylesheet" type="text/css">
    </head>
    <body>
        <?php include("../template/navbar_default.php"); ?>
        <div class="container">
            <form action="<?= $_SERVER['PHP_SELF'].'?tela=2' ?>" method="post" name="form1" id="form1">
                <input type="hidden" name="home" id="home" />
                <div class="row">
                    <div class="col-lg-12">
                        <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small>Desprocessar Transferência</small></h2></div>
                    </div><!-- /.col-lg-12 -->
                </div><!-- /.row -->
                <?php if ($bloqueio) { ?>
                    <div class="alert alert-dismissable alert-danger">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        <p><strong>Atenção!</strong> O funcionário selecionado encontra-se em uma folha em aberto, remova-o desta folha para proseguir com o desprocessamento da transferência!</p>
                    </div>
                <?php } else { ?>
                    <?php if ($tela == 1) { ?>
                        <div class="row">
                            <div class="col-lg-12">
                                <h3>Dados da Última transferência</h3>
                                <p><label class="first"><strong>Data de Criação:</strong></label> <?php echo $trans['criado_em'] ?></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12 note note-warning">
                                <fieldset>
                                    <legend><h3>Funcionário</h3></legend>
                                    <p><label class="first">Nome:</label> <?php echo $clt[1]['nome'] ?></p>
                                    <p><label class="first">CPF:</label> <?php echo $clt[1]['cpf'] ?></p>
                                    <input type="hidden" name="clt" id="clt" value="<?= $clt[1]['id_clt'] ?>" />
                                    <input type="hidden" name="transferencia" id="tranferencia" value="<?= $trans['id_transferencia'] ?>" />
                                </fieldset>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12 note note-danger">
                                <fieldset class="border-red">
                                    <legend><h3>Origem</h3></legend>
                                    <p><label class="first">Região:</label> <?= $trans['regiao_de'] ?></p>
                                    <p><label class="first">Projeto:</label> <?= $trans['projeto_de'] ?></p>
                                    <p><label class="first">Função:</label> <?= $trans['curso_de'] ?></p>
                                    <p><label class="first">Sindicato:</label> <?= $trans['sindicato_de'] ?></p>
                                    <p><label class="first">Horário:</label> <?= $trans['horario_de'] ?></p>
                                    <p><label class="first">Unidade:</label> <?= $trans['unidade_de'] ?></p>
                                    <p><label class="first">Banco:</label> <?php echo $trans['banco_de'] ?></p>
                                    <p><label class="first">Tipo de Pagamento:</label> <?php echo $trans['tipopg_de'] ?></p>
                                </fieldset>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12 note note-info">
                                <fieldset>
                                    <legend><h3>Destino</h3></legend>
                                    <p><label class="first">Região:</label> <?= $trans['regiao_para'] ?></p>
                                    <p><label class="first">Projeto:</label> <?= $trans['projeto_para'] ?></p>
                                    <p><label class="first">Função:</label> <?= $trans['curso_para'] ?></p>
                                    <p><label class="first">Sindicato:</label> <?= $trans['sindicato_para'] ?></p>
                                    <p><label class="first">Horário:</label> <?= $trans['horario_para'] ?></p>
                                    <p><label class="first">Unidade:</label> <?= $trans['unidade_para'] ?></p>
                                    <p><label class="first">Banco:</label> <?php echo $trans['banco_para'] ?></p>
                                    <p><label class="first">Tipo de Pagamento:</label> <?php echo $trans['tipopg_para'] ?></p>
                                </fieldset>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <!--a class="btn btn-danger pull-right"></a-->
                                <button type="submit" class="btn btn-danger pull-right" value="Desprocessar" name="desprocessar" id="desprocessar"><span class="fa fa-ban"></span>&nbsp;&nbsp;Desprocessar</button>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="alert alert-dismissable alert-success">
                            <button type="button" class="close" data-dismiss="alert">×</button>
                            <h2>Transferência desprocessada com sucesso!</h2>
                                <br/><br/>
                            <p>feche a tela para continuar navegando</p>
                        </div>
                        <!--h2>Transferência desprocessada com sucesso!</h2>
                        <br/><br/>
                        <p>feche a tela para continuar navegando</p-->
                    <?php } ?>
                <?php } ?>
            </form>
        <?php include_once ('../template/footer.php'); ?>
        </div>
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../js/jquery.validationEngine-2.6.js"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js"></script>
    </body>
</html>