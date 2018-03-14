<?php
/**
 * Procedimento para desprocessamento de férias
 * 
 * @file      rh_ferias_desprocessar.php
 * @license   http://www.gnu.org/licenses/gpl-3.0.txt GNU General Public License
 * @link      http://des.f71iabassp.com/intranet/rh/ferias/rh_ferias_desprocessar.php
 * @copyright 2016 F71
 * @author    Jacques <jacques@f71.com.br>
 * @package   
 * @access    public  
 * @version:  I3.0.0000 - ??/??/???? - N/D     - Versão Inicial 
 * @version:  I3.0.9999 - 25/08/2016 - Jacques - Comentado o código de update de rh_movimentos_clt por não possuir o campo movimento
 * @version:  I3.0.9101 - 14/10/2016 - Jacques - Na ferificação se status do clt para exclusão, não é mais feito o bloqueio de clt com folha aberta etc. Mas uma mensagem de alerta é exibida. 
 * 
 * @todo 
 * @example:  http://www.f71iabassp.com/intranet/rh/ferias/rh_ferias_desprocessar.php?tela=3&clt=3117&ferias=7402
 * 
 * 
 */

if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='../login.php'>Logar</a> ";
    exit;
}

include "../../conn.php";
include "../../wfunction.php";

$usuario = carregaUsuario();

$user1 = $usuario['id_funcionario'];
$regiao = $usuario['id_regiao'];
$master = $usuario['id_master'];
$id_clt = $_REQUEST['clt'];
$id_ferias = $_REQUEST['ferias'];
$tela = $_REQUEST['tela'];
$bloqueio = false;

//print_r($_REQUEST);

$clt = montaQuery('rh_clt', '*', "id_clt = $id_clt");

/* verifica se o funcionário está demitido ou aguardando demição */
if ($clt['status'] >= 60) {
    $bloqueio = true;
}

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
$num_rows_folha = mysql_num_rows($result_folha);

// Verificar se ao desprocessar uma férias pode ocasionar algum erro de calculo 
// na rescisão, verificar se tem rescisão aberta pra pessoa...
// VERIFICA SE EXISTE RESCISAO PARA O FUNCIONÁRIO
$query_rescisao = "SELECT * FROM rh_recisao
                    WHERE id_clt = '$id_clt'
                    AND `status` = 1";

$result_rescisao = mysql_query($query_rescisao);
$num_rows_rescisao = mysql_num_rows($result_rescisao);

if ($num_rows_folha || $num_rows_rescisao) $bloqueio = true;

/******************************************************************************/
if ($tela == 1) {
    /*
     * Tela 1: exibe unidade de origem, unidade de destino e botão para  confirmação
     * do desprocessamento da férias.
     */

    // consultar ultima férias da pessoa
    $query_ferias = "SELECT 
                      a.id_ferias,
                      DATE_FORMAT(a.data_ini, '%d/%m/%Y') AS data_ini,
                      DATE_FORMAT(a.data_fim, '%d/%m/%Y') AS data_fim,
                      DATE_FORMAT(a.data_retorno, '%d/%m/%Y') AS data_retorno
                    FROM rh_ferias AS a
                    WHERE a.id_clt = {$id_clt}
                    AND a.status = 1
                    AND a.id_ferias = {$id_ferias}
                    ORDER BY a.id_ferias DESC
                    LIMIT 1";
                    
    $rs = mysql_query($query_ferias) or die(mysql_error($query_ferias));
    
    $ferias = mysql_fetch_assoc($rs);
    
}

/******************************************************************************/
if ($tela == 2) {
    /* Tela 2: Verificar se foi lançado o pagamento das férias na tabela de 
     * pagamentos / saída 
     */
    /*
     * saída_status = 1 : aguardando pagamento (alerta sobre a exitencia da 
     *                    saída e pergunta se quer continuar... caso sim,
     *                    excluir saída)
     * saída_status = 2 : pago (não deixa apagar)
     * saída_status = 0 : excluido
     */
    $data = $_REQUEST['data_ini'];
    $data = explode('/', $data);
    $ano = $data[2];

    $qr_verifica_saida = mysql_query("SELECT MAX(B.status) as status, MAX(B.id_saida) as id FROM pagamentos_especifico as A 
                                        INNER JOIN saida as B
                                        ON B.id_saida = A.id_saida
                                        WHERE A.id_clt = '$id_clt' AND (B.tipo = 76 OR B.tipo = 156) AND A.ano = $ano") or die(mysql_error());

//        extract(mysql_fetch_assoc($qr_verifica_saida), EXTR_PREFIX_ALL, 'saida');
    $row_verifica_saida = mysql_fetch_assoc($qr_verifica_saida);
    if (!empty($row_verifica_saida['status']) || $row_verifica_saida['status'] == 0) {
        header('location:' . $_SERVER['PHP_SELF'] . '?tela=3&clt=' . $id_clt . '&ferias=' . $id_ferias);
    }

    /*****************************************************************************/
} 

if ($tela == 3) {
        
    /*
     * Tela 3: realiza o desprocessamento e informa ao usuário que a férias 
     * foi desprocessada
     */
    if (isset($_REQUEST['id_saida'])) {
        $saida_id = $_REQUEST['id_saida'];
        $delete_saida = "UPDATE saida SET `status`='0' WHERE id_saida = '$saida_id'";
        $delete_pagamento = "DELETE FROM pagamentos_especifico WHERE id_saida = '$saida_id'";

//            echo "$delete_saida<br>\n";
//            echo "$delete_pagamento<br>\n";

        mysql_query($delete_saida) or die(mysql_error($delete_saida));
        mysql_query($delete_pagamento) or die(mysql_error($delete_pagamento));
    }

    // consultar ultima férias da pessoa
    $query_ferias2 = "select * from rh_ferias where id_clt = '$id_clt' and id_ferias='$id_ferias'";
    $result_ferias2 = mysql_query($query_ferias2);
    $ferias2 = mysql_fetch_assoc($result_ferias2);

//        print_r($ferias2);
//        exit();
//        extract($ferias2);
//        
    // Colocar na tabela de férias o status 0
    $update_ferias = "UPDATE rh_ferias
                      SET status=0
                      WHERE id_ferias = {$id_ferias}";
    //echo "$update_ferias<br>\n";
    mysql_query($update_ferias) or die(mysql_error($update_ferias));


    // Remover o Evento gerado para as férias na tabela de eventos
//        $delete_evento = "DELETE FROM rh_eventos
//                            WHERE id_clt='$id_clt' AND
//                            id_regiao='$regiao' AND
//                            id_projeto='$projeto' AND
//                            nome_status='Férias' AND
//                            cod_status='40' AND
//                            id_status='1' AND
//                            `data`='$data_ini' AND
//                            data_retorno='$data_retorno' AND
//                            dias='$dias_ferias' AND
//                            `status`='1' AND
//                            status_reg='1'";
    $update_eventos = " UPDATE rh_eventos
                        SET `status` = '0',status_reg = '0'
                        WHERE id_clt='$id_clt' AND
                        id_regiao='{$ferias2['regiao']}' AND
                        id_projeto='{$ferias2['projeto']}' AND
                        nome_status='Férias' AND
                        cod_status='40' AND
                        id_status='1' AND
                        `data`='{$ferias2['data_ini']}' AND
                        data_retorno='{$ferias2['data_retorno']}' AND
                        dias='{$ferias2['dias_ferias']}' AND
                        `status`='1' AND
                        status_reg='1'";


    //echo "$update_eventos<br>\n";
    mysql_query($update_eventos) or die($update_eventos);



    // Verificar se alterou algum campo no RH CLT ( verificar o campo status )
    $update_clt = "UPDATE rh_clt 
                    SET status='10' 
                    WHERE id_clt = '$id_clt'";
    mysql_query($update_clt) or die($update_clt);

    // altera status dos movimentos
    $movimentos = mysql_result(mysql_query("SELECT movimentos FROM rh_ferias WHERE id_ferias = '" . $id_ferias . "'"), 0);
    $total_movimentos = (int) count(explode(',', $movimentos));
    $update_movimentos = "UPDATE rh_movimentos_clt 
                            SET status_ferias = '0' 
                            WHERE id_movimento IN($movimentos)";
//        echo "$update_movimentos<br>\n"; exit;
//        echo "$movimentos";
    //echo "SELECT movimentos FROM rh_ferias WHERE id_ferias = '" . $id_ferias . "'";
    //mysql_query($update_movimentos) or die($update_movimentos);

    // salvar log do desprocessamento
    $insert_ferias_desproc = "INSERT INTO rh_ferias_log (id_ferias,id_usuario,data_mod, status_log_ferias, id_clt) VALUES ('$id_ferias','$user1',NOW(), 0,$id_clt)";
//        echo $insert_ferias_desproc; exit;
    mysql_query($insert_ferias_desproc) or die(mysql_error('Erro no log'));

}
/*     * *************************************************************************** */

?>
<html>
    <head>
        <title>:: Intranet :: RH - Férias</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../../favicon.ico" />
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../../favicon.ico" rel="shortcut icon" />
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt.js" type="text/javascript"></script>
        <script src="../../js/global.js" type="text/javascript"></script>
        <script>
            $(document).ready(function() {
                $('.voltar-lista').click(function() {
                    window.location = '../../rh_novaintra/ferias';
                });
            });
        </script>
    </head>
    <body id="page-rh-trans" class="novaintra">
        <div id="content">
            <form action="<?= $_SERVER['PHP_SELF'] ?>" method="post" name="form1" id="form1">
                <div id="head">
                    <img src="../../imagens/logomaster<?php echo $master; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                    <div class="fleft">
                        <h2>RH - Desprocessar Férias de funcionário</h2>
                    </div>
                    <div class="fright"> <?php include('../reportar_erro.php'); ?></div> 
                </div>
                <br/>
                <?php if ($tela == 1) { ?>
                
                    <?php 
                    if ($bloqueio) { 
                    ?>
                        <div>
                            <h2>Atenção!</h2>
                            <?php if ($num_rows_folha) { ?>
                                <p>O funcionário selecionado encontra-se em uma folha em aberto, remova-o desta folha para proseguir com o desprocessamento da férias!</p>
                            <?php } ?>
                            <?php if ($num_rows_rescisao) { ?>
                                <p>O funcionário selecionado encontra-se em uma rescisão em aberto.</p>
                            <?php } ?>
                            <?php if ($clt['status'] >= 60) { ?>
                                <p>O funcionário foi demitido ou está aguardando demição.</p>
                            <?php } ?>
                        </div>
                    <?php 
                    } 
                    ?>
                

                    <h3>Dados das Últimas Férias</h3>
                    <p><label class="first"><strong>Data de Criação:</strong></label> <?php echo $ferias['data_proc'] ?></p>
                    <fieldset>
                        <legend>Informações sobre as férias a serem desprocessadas</legend>

                        <p><label class="first">Nome:</label> <?php echo $clt[1]['nome'] ?></p>
                        <p><label class="first">CPF:</label> <?php echo $clt[1]['cpf'] ?></p>
                        <input type="hidden" name="clt" id="clt" value="<?=$id_clt?>" />
                        <input type="hidden" name="tela" id="tela" value="3"/>
                        <input type="hidden" name="ferias" id="ferias" value="<?=$ferias['id_ferias']?>" />
                        <input type="hidden" name="data_ini" id="data_ini" value="<?= $ferias['data_ini'] ?>" />
                        <p><label class="first">Data de Início:</label><?= $ferias['data_ini'] ?></p>
                        <p><label class="first">Data de Encerramento:</label><?= $ferias['data_fim'] ?></p>
                        <p><label class="first">Data de Retorno:</label><?= $ferias['data_retorno'] ?></p>
                    </fieldset>
                    <br/>
                <p class="controls"><?php if($bloqueio) echo "Deseja continuar mesmo assim por sua conta e risco? ";?><input type="submit" class="button" value="Desprocessar" name="desprocessar" id="desprocessar" /> </p>

                <?php } else if ($tela == 2) { ?>
                    <?php if ($row_verifica_saida['status'] == 1) { ?>

                        <h2>Atenção!</h2>
                        <h3>Existe um pagamento agendado para essas férias. Deseja continuar o desprocessamento e excluir esse agendamento?</h3>
                        <input type="hidden" name="clt" id="clt" value="<?= $id_clt ?>" />
                        <input type="hidden" name="tela" id="tela" value="3"/>
                        <input type="hidden" name="ferias" id="ferias" value="<?= $id_ferias ?>" />
                        <input type="hidden" name="id_saida" id="id_saida" value="<?= $saida_id ?>" />
                        <p><input type="submit" value="Sim, Continuar Desprocessamento e Excluir o Agendamento">
                            <input type="button" value="Não" class="voltar-lista" /></p>

                    <?php } else if ($row_verifica_saida['status'] == 2) { ?>

                        <h2>Atenção!</h2>
                        <h3>Foi realizado um pagamento para essas férias. Não será possível desprocessá-la.</h3>
                        <p><input type="button" value="Ok" class="voltar-lista" /></p>

                        <?php } ?>
                    <?php } else if ($tela == 3) { ?>
                    <h2>Férias desprocessada com sucesso!</h2>
                    <br/><br/>
                    <p><input type="button" value="Ok" class="voltar-lista" /></p>
                <?php } ?>
            </form>
        </div>
    </body>
</html>

<?php
