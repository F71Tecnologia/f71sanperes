<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='../login.php'>Logar</a> ";
    exit;
}

include "../conn.php";
include "../wfunction.php";
include "../classes/LogClass.php";

$log = new Log();

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
        
        $antigo = $log->getLinha('rh_clt', $id_clt);
        sqlUpdate("rh_clt", $updates, array("id_clt" => $id_clt));
        $novo = $log->getLinha('rh_clt', $id_clt);
        
        $log->log('2',"Transferência do CLT $id_clt desprocessada",'rh_clt');
        
        /* MUDAR O NOME DA FOTO */
        if ($clt[1]['foto'] == '1') {
            $dir = dirname(dirname(__FILE__)) . "/fotosclt/";
            $nomeOld = $trans2['id_regiao_para'] . "_" . $trans2['id_projeto_para'] . "_" . $id_clt . '.gif';
            $nomeNovo = $trans2['id_regiao_de'] . "_" . $trans2['id_projeto_de'] . "_" . $id_clt . '.gif';
            rename($dir . $nomeOld, $dir . $nomeNovo);
            $log->log('2',"Foto do CLT $id_clt renomeada para: $nomeNovo",'');
        }
        
        
        
// atualizar dependentes        
        $sql = "UPDATE dependentes SET id_projeto='{$trans2['id_projeto_de']}', id_regiao='{$trans2['id_regiao_de']}'  where id_bolsista=" . $id_clt;
//    echo 'update: '.$sql;
        $antigo = $log->getLinha('dependentes', $id_clt);
        mysql_query($sql) or die(mysql_error());
        $novo = $log->getLinha('dependentes', $id_clt);
        
        $log->log('2', "Dependentes do funcionário $id_clt alterados","rh_transferencias",$antigo,$novo);
        
// excluir linha da tabela rh_transferencia
        $delete = "DELETE FROM rh_transferencias WHERE id_transferencia = '{$trans2['id_transferencia']}'";
        mysql_query($delete) or die(mysql_error());
        $log->log('2', "Transferência ID {$id_transferencia} desprocessada","rh_transferencias");
    }
}
?>
<html>
    <head>
        <title>:: Intranet :: RH - Transferência de Unidade</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../favicon.ico" />
        <link href="../net1.css" rel="stylesheet" type="text/css" />
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../favicon.ico" rel="shortcut icon" />
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-pt.js" type="text/javascript"></script>
        <script src="../js/global.js" type="text/javascript"></script>
    </head>
    <body id="page-rh-trans" class="novaintra">
        <div id="content">
            <form action="<?= $_SERVER['PHP_SELF'].'?tela=2' ?>" method="post" name="form1" id="form1">
                <div id="head">
                    <img src="../imagens/logomaster<?php echo $master; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                    <div class="fleft">
                        <h2>RH - Desprocessar Transferência de funcionário</h2>
                    </div>
                    <div class="fright"> <?php include('../reportar_erro.php'); ?></div> 
                </div>
                <br/>
                <?php if ($bloqueio) { ?>
                    <div>
                        <h2>Atenção</h2>
                        <p>O funcionário selecionado encontra-se em uma folha em aberto, remova-o desta folha para proseguir com o desprocessamento da transferência!</p>
                    </div>
                <?php } else { ?>
                    <?php if ($tela == 1) { ?>
                        <h3>Dados da Última transferência</h3>
                        <p><label class="first"><strong>Data de Criação:</strong></label> <?php echo $trans['criado_em'] ?></p>
                        <fieldset>
                            <legend>Funcionário</legend>
                            <p><label class="first">Nome:</label> <?php echo $clt[1]['nome'] ?></p>
                            <p><label class="first">CPF:</label> <?php echo $clt[1]['cpf'] ?></p>
                            <input type="hidden" name="clt" id="clt" value="<?= $clt[1]['id_clt'] ?>" />
                            <input type="hidden" name="transferencia" id="tranferencia" value="<?= $trans['id_transferencia'] ?>" />
                        </fieldset>
                        <fieldset class="border-red">
                            <legend>Origem</legend>
                            <p><label class="first">Região:</label> <?= $trans['regiao_de'] ?></p>
                            <p><label class="first">Projeto:</label> <?= $trans['projeto_de'] ?></p>
                            <p><label class="first">Função:</label> <?= $trans['curso_de'] ?></p>
                            <p><label class="first">Sindicato:</label> <?= $trans['sindicato_de'] ?></p>
                            <p><label class="first">Horário:</label> <?= $trans['horario_de'] ?></p>
                            <p><label class="first">Unidade:</label> <?= $trans['unidade_de'] ?></p>
                            <p><label class="first">Banco:</label> <?php echo $trans['banco_de'] ?></p>
                            <p><label class="first">Tipo de Pagamento:</label> <?php echo $trans['tipopg_de'] ?></p>
                        </fieldset>
                        <br/>
                        <fieldset class="border-blue">
                            <legend>Destino</legend>
                            <p><label class="first">Região:</label> <?= $trans['regiao_para'] ?></p>
                            <p><label class="first">Projeto:</label> <?= $trans['projeto_para'] ?></p>
                            <p><label class="first">Função:</label> <?= $trans['curso_para'] ?></p>
                            <p><label class="first">Sindicato:</label> <?= $trans['sindicato_para'] ?></p>
                            <p><label class="first">Horário:</label> <?= $trans['horario_para'] ?></p>
                            <p><label class="first">Unidade:</label> <?= $trans['unidade_para'] ?></p>
                            <p><label class="first">Banco:</label> <?php echo $trans['banco_para'] ?></p>
                            <p><label class="first">Tipo de Pagamento:</label> <?php echo $trans['tipopg_para'] ?></p>
                        </fieldset>
                        <br/>
                        <p class="controls"> <input type="submit" class="button" value="Desprocessar" name="desprocessar" id="desprocessar" /> </p>
                        <?php } else { ?>
                        <h2>Transferência desprocessada com sucesso!</h2>
                        <br/><br/>
                        <p>feche a tela para continuar navegando</p>
                    <?php } ?>
                <?php } ?>
            </form>
        </div>
    </body>
</html>
<?php
