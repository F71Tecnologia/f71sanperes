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

// aviso previo - está inativo pq o campo data_fim_aviso só tem dados zerados
/*$sql_previo = mysql_query("SELECT id_clt, nome, DATE_FORMAT(data_aviso, '%d/%m/%Y') AS inicio_aviso, DATE_FORMAT(data_fim_aviso, '%d/%m/%Y') AS fim_aviso
                        FROM rh_recisao
                        WHERE id_regiao = {$id_regiao} AND data_fim_aviso > CURRENT_DATE
                        GROUP BY data_fim_aviso") or die(mysql_error());
$total_previo = mysql_num_rows($sql_previo);*/

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

?>

<html>

    <head>
        
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>GERENCIAMENTO DE NOTIFICA&Ccedil;&Otilde;ES</title>
        <link href="../../favicon.ico" rel="shortcut icon" />
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery.ui.datepicker.js" type="text/javascript"></script>
        <script src="../../js/jquery.ui.datepicker-pt-BR.js" type="text/javascript"></script>
        <script src="../../js/global.js" type="text/javascript"></script>
        
        <script>
            $(function() {

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

            });

        </script>

    </head>

    <body class="novaintra">
        
        <div id="corpo_avisos">
            
            <div id="topo_avisos">
                
                <div class="logo_empresa">
                    <?php
                    include "../../empresa.php";
                    $img = new empresa();
                    $img->imagemCNPJ();
                    ?>                   
                </div><!--logo_empresa-->
                
                <h1>GERENCIAMENTO DE AVISOS E NOTIFICA&Ccedil;&Otilde;ES</h1>
                
                <div id="reporta_erro">
                    <?php include('../../reportar_erro.php'); ?>
                </div><!--reporta_erro-->
                
                <div class="clear"></div>
                
            </div><!--topo_avisos-->  
            
            <div id="conteudo_avisos">
                
                <div id="menu">
                    
                    <ul>                                                

                        <li>
                            <a href="javascript:;" class="a_menu a_borda bt-menu j_verifica aselected" data-item="2">
                                Observações Individuais                                
                                <?php if ($total_obs != '0') { ?>
                                    <span class="cor_aviso">(<?php echo $total_obs; ?>)</span>
                                <?php } ?>
                            </a>                        
                        </li>

                        <li>
                            <a href="javascript:;" class="a_menu a_borda j_verifica bt-menu" data-item="3">
                                Acidente de Trabalho
                                <?php if ($total_acidente != '0') { ?>
                                    <span class="cor_aviso">(<?php echo $total_acidente; ?>)</span>
                                <?php } ?>
                            </a>
                        </li>

                        <li>
                            <a href="javascript:;" class="a_menu a_borda j_verifica bt-menu" data-item="4">
                                Licenças
                                <?php if ($total_licencas != '0') { ?>
                                    <span class="cor_aviso">(<?php echo $total_licencas; ?>)</span>
                                <?php } ?>
                            </a>
                        </li>

                        <li>
                            <a href="javascript:;" class="a_menu a_borda bt-menu j_verifica" data-item="5">
                                Aquisição de Férias                                                                
                                <?php if ($total_aquisicao != '0') { ?>
                                    <span class="cor_aviso">(<?php echo $total_aquisicao; ?>)</span>
                                <?php } ?>
                            </a>
                        </li>

                        <li>
                            <a href="javascript:;" class="a_menu a_borda bt-menu j_verifica" data-item="6">
                                Retorno de Férias
                                <?php if ($total_retorno != '0') { ?>
                                    <span class="cor_aviso">(<?php echo $total_retorno; ?>)</span>
                                <?php } ?>
                            </a>
                        </li>

                        <li>
                            <a href="javascript:;" class="a_menu a_borda bt-menu j_verifica" data-item="7">
                                Contribuição sindical
                                <?php if ($total_contribuicao != '0') { ?>
                                    <span class="cor_aviso">(<?php echo $total_contribuicao; ?>)</span>
                                <?php } ?>
                            </a>
                        </li>
                        
                        <li>
                            <a href="javascript:;" class="a_menu a_borda bt-menu j_verifica" data-item="8">
                                Contratos em experiência
                                <?php if ($total_experiencia != '0') { ?>
                                    <span class="cor_aviso">(<?php echo $total_experiencia; ?>)</span>
                                <?php } ?>
                            </a>
                        </li>
                        
                        <li>
                            <a href="javascript:;" class="a_menu a_borda bt-menu j_verifica" data-item="14">
                                CBO
                                <?php if ($total_cbo != '0') { ?>
                                    <span class="cor_aviso">(<?php echo $total_cbo; ?>)</span>
                                <?php } ?>
                            </a>
                        </li>
                        
                        <li>
                            <a href="javascript:;" class="a_menu a_borda bt-menu j_verifica" data-item="13">
                                Erros de Cadastro    
                                
                                <?php $total_cpf = "<span id='resposta'></span>"; ?>
                                
                                <?php //if ($total_cpf != '0') { ?>
                                    <span class="cor_aviso">(<?php echo $total_cpf; ?>)</span>
                                <?php //} ?>
                            </a>
                        </li>
                        
                        <li>
                            <a href="javascript:;" class="a_menu a_borda bt-menu j_verifica" data-item="9">
                                Horários trabalhados
                                <?php if ($total_horarios != '0') { ?>
                                    <span class="cor_aviso">(<?php echo $total_horarios; ?>)</span>
                                <?php } ?>
                            </a>
                        </li>
                        
                        <li>
                            <a href="javascript:;" class="a_menu bt-menu j_verifica" data-item="10">
                                Não Finalizados
                                <?php if ($total_nao_finalizados != '0') { ?>
                                    <span class="cor_aviso">(<?php echo $total_nao_finalizados; ?>)</span>
                                <?php } ?>
                            </a>
                        </li>
                        
                    </ul>

                </div>

                <div id="dados_avisos" style="float: left; margin: 0 0 0 20px;">

                    <div id="item1">

                    <?php if ($total_previo == '0') { ?>

                            <span id="message-box" class="message-yellow">
                                Nenhuma notificação encontrada
                            </span>

                    <?php } else { ?>

                            <table class="grid table_res">

                                <thead>

                                    <tr>
                                        <td colspan="4" class="td_titulo">PROJETO</td>
                                    </tr>                                                                

                                    <tr class="tr_avisos">
                                        <td>COD</td>
                                        <td>NOME</td>
                                        <td>IN&Iacute;CIO DO AVISO</td>
                                        <td>T&Eacute;RMINO DO AVISO</td>
                                    </tr>

                                </thead>

                                <?php while ($res_aviso = mysql_fetch_assoc($sql_previo)) { ?>

                                        <tbody>

                                            <tr>
                                                <td><?php echo $res_aviso['id_clt']; ?></td>
                                                <td><?php echo $res_aviso['nome']; ?></td>
                                                <td><?php echo $res_aviso['inicio_aviso']; ?></td>
                                                <td><?php echo $res_aviso['fim_aviso']; ?></td>
                                            </tr>

                                        </tbody>

                                <?php } ?>
                                        
                            </table>
                        
                    <?php } ?>

                    </div><!--item1-->

                    <div id="item2">

                    <?php if ($total_obs == '0') { ?>

                            <span id="message-box" class="message-yellow">
                                Nenhuma notificação encontrada
                            </span>

                    <?php } else { ?>

                            <p id="legenda_item2"><span class="marca_azul">*</span> Clique sobre o nome para editar o cadastro de CLT</p>                                                

                            <table id="table_res"  name="table_res" class="grid table_res">

                                <thead>

                                    <tr>
                                        <td colspan="3" class="td_titulo">PROJETO</td>
                                    </tr>                                                                    

                                    <tr class="tr_avisos">
                                        <td>COD</td>
                                        <td>NOME</td>
                                        <td>OBSERVA&Ccedil;&Otilde;ES</td>
                                    </tr>

                                </thead>

                                <tbody>

                                    <?php
                                    while ($rowOBS = mysql_fetch_array($resultOBS)) {
                                        print "<tr>";
                                        print "<td>{$rowOBS['id_clt']}</td>";
                                        print "<td><a href='../alter_clt.php?clt=$rowOBS[id_clt]&pro=$rowOBS[id_projeto]&pagina=clt' class='linkBlack'>$rowOBS[nome]</a></td>";
                                        print "<td>{$rowOBS['observacao']}</td>";
                                        print "</tr>";
                                    }
                                    ?>       

                                </tbody>

                            </table>        

                    <?php } ?>

                    </div><!--item2-->

                    <div id="item3">       

                    <?php if ($total_acidente == '0') { ?>
                        
                            <span id="message-box" class="message-yellow">
                                Nenhuma notificação encontrada
                            </span>

                    <?php } else { ?>

                            <table class="grid table_res">

                                <thead>

                                    <tr>
                                        <td colspan="4" class="td_titulo">PROJETO</td>
                                    </tr>                                                                

                                    <tr class="tr_avisos">
                                        <td>COD</td>
                                        <td>NOME</td>
                                        <td>DATA SA&Iacute;DA</td>
                                        <td>DATA RETORNO</td>
                                    </tr>

                                </thead>

                                <?php while ($res_acidente = mysql_fetch_assoc($sql_acidente)) { ?>

                                    <tbody>

                                        <tr>
                                            <td><?= $res_acidente['id_clt'] ?></td>
                                            <td><?= $res_acidente['nome'] ?></td>
                                            <td><?= $res_acidente['data_saida'] ?></td>
                                            <td><?= $res_acidente['data_retorno'] ?></td>
                                        </tr>

                                    </tbody>

                                <?php } ?>

                            </table>

                    <?php } ?>

                    </div><!--item3-->

                    <div id="item4">

                    <?php if ($total_licencas == '0') { ?>

                            <span id="message-box" class="message-yellow">
                                Nenhuma notificação encontrada
                            </span>

                    <?php } else { ?>

                            <table class="grid table_res">

                                <thead>

                                    <tr>
                                        <td colspan="5" class="td_titulo">PROJETO</td>
                                    </tr>                                                                

                                    <tr class="tr_avisos">
                                        <td>COD</td>
                                        <td>NOME</td>
                                        <td>TIPO DE LICEN&Ccedil;A</td>
                                        <td>IN&Iacute;CIO DA LICEN&Ccedil;A</td>
                                        <td>T&Eacute;RMINO DA LICEN&Ccedil;A</td>
                                    </tr>

                                </thead>

                                <?php while ($res_licensa = mysql_fetch_array($sql_licencas)) { ?>

                                    <tbody>

                                        <tr>
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

                    </div><!--item4-->

                    <div id="item5">           
                        
                        <?php if ($total_aquisicao == '0') { ?>

                            <span id="message-box" class="message-yellow">
                                Nenhuma notificação encontrada
                            </span>

                        <?php } else { ?>

                        <table class="grid table_res">

                            <thead>

                                <tr>
                                    <td colspan="6" class="td_titulo">PROJETO</td>
                                </tr>

                                <tr class="tr_avisos">
                                    <td>COD</td>
                                    <td>NOME</td>
                                    <td>SITUA&Ccedil;&Atilde;O ATUAL</td>                                    
                                    <td>DATA DE AQUISI&Ccedil;&Atilde;O</td>
                                    <td>VENCIMENTO DO PERÍODO</td>
                                </tr>

                            </thead>

                            <?php while ($res_aquisicao = mysql_fetch_assoc($sql_aquisicao)) { ?>                                

                                <tbody>

                                    <tr>
                                        <td><?php echo $res_aquisicao['id_clt'] ?></td>
                                        <td><?php echo $res_aquisicao['nome'] ?></td>
                                        <td><?php echo $res_aquisicao['situacao'] ?></td>
                                        <td><?php echo $res_aquisicao['data_ini'] ?></td>
                                        <td><?php echo $res_aquisicao['data_fim'] ?></td>
                                    </tr>

                                </tbody>

                            <?php } ?>

                        </table>
                        
                        <?php } ?>

                    </div><!--item5-->

                    <div id="item6">    
                        
                        <?php if ($total_retorno == '0') { ?>

                            <span id="message-box" class="message-yellow">
                                Nenhuma notificação encontrada
                            </span>

                        <?php } else { ?>

                        <table class="grid table_res">

                            <thead>

                                <tr>
                                    <td colspan="4" class="td_titulo">PROJETO</td>
                                </tr>

                                <tr class="tr_avisos">
                                    <td>COD</td>
                                    <td>NOME</td>
                                    <td>DATA SAIDA</td>
                                    <td>DATA RETORNO</td>
                                </tr>

                            </thead>                                                        

                            <?php while ($res_retorno = mysql_fetch_assoc($sql_retorno)) { ?>

                                <tbody>

                                    <tr>
                                        <td><?php echo $res_retorno['id_clt']; ?></td>
                                        <td><?php echo $res_retorno['nome']; ?></td>
                                        <td><?php echo $res_retorno['data_ini']; ?></td>
                                        <td><?php echo $res_retorno['data_retorno']; ?></td>
                                    </tr>

                                </tbody>

                            <?php } ?>

                        </table>    
                        
                        <?php } ?>

                    </div><!--item6-->

                    <div id="item7">

                    <?php if ($total_contribuicao == '0') { ?>

                            <span id="message-box" class="message-yellow">
                                Nenhuma notificação encontrada
                            </span>

                    <?php } else { ?>

                            <table class="grid table_res">

                                <thead>

                                    <tr>
                                        <td colspan="2" class="td_titulo">SINDICATOS</td>
                                    </tr>

                                    <tr class="tr_avisos">
                                        <td>SINDICATO</td>
                                        <td>PAGAMENTO (M&Ecirc;S)</td>
                                    </tr>

                                </thead>

                                            <?php while ($sindicatos = mysql_fetch_assoc($sql_contribuicao)) { ?>

                                    <tbody>

                                        <tr>

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
                                                }
                                                ?>
                                            </td> 

                                        </tr>

                                    </tbody>

                            <?php } ?>

                            </table>

                    <?php } ?>

                    </div><!--item7-->

                    <div id="item8">
                        
                        <?php if ($total_experiencia == '0') { ?>

                            <span id="message-box" class="message-yellow">
                                Nenhuma notificação encontrada
                            </span>

                        <?php } else { ?>

                        <table class="grid table_res">

                            <thead>

                                <tr>
                                    <td colspan="6" class="td_titulo">PROJETO</td>
                                </tr>

                                <tr class="tr_avisos">
                                    <td>COD</td>
                                    <td>NOME</td>
                                    <td>CONTRATADO EM</td>
                                    <td>FIM DA EXPERI&Ecirc;NCIA (45 dias)</td>
                                    <td>FIM DA EXPERI&Ecirc;NCIA (60 dias)</td>
                                    <td>FIM DA EXPERI&Ecirc;NCIA (90 dias)</td>
                                </tr>

                            </thead>

                            <?php while ($res_experiencia = mysql_fetch_assoc($sql_experiencia)) { ?>

                                    <tbody>

                                        <tr>
                                            <td><?php echo $res_experiencia['id_clt']; ?></td>
                                            <td><?php echo $res_experiencia['nome']; ?></td>
                                            <td><?php echo $res_experiencia['data_entrada']; ?></td>
                                            <td><?php echo $res_experiencia['experiencia_a']; ?></td>
                                            <td><?php echo $res_experiencia['experiencia_b']; ?></td>
                                            <td><?php echo $res_experiencia['experiencia_d']; ?></td>
                                        </tr>

                                    </tbody>

                            <?php } ?>

                        </table>
                        
                        <?php } ?>

                    </div><!--item8-->   
                    
                    <div id="item9">
                        
                        <?php if ($total_horarios == '0') { ?>

                            <span id="message-box" class="message-yellow">
                                Nenhuma notificação encontrada
                            </span>

                        <?php } else { ?>

                        <table class="grid table_res">

                            <thead>                               

                                <tr class="tr_avisos">
                                    <td>COD</td>
                                    <td>NOME</td>
                                    <td>PROJETO</td>                                    
                                </tr>

                            </thead>

                            <?php while ($res_horarios = mysql_fetch_assoc($sql_horarios)) { ?>

                                    <tbody>

                                        <tr>
                                            <td><?php echo $res_horarios['id_clt']; ?></td>
                                            <td><?php echo "<a href='../alter_clt.php?clt={$res_horarios['id_clt']}&pro={$res_horarios['projeto']}&pagina=clt' class='linkBlack'>{$res_horarios['nome_clt']}</a>" ?></td>
                                            <td><?php echo $res_horarios['nome_projeto']; ?></td>
                                        </tr>

                                    </tbody>

                            <?php } ?>

                        </table>
                        
                        <?php } ?>
                        
                    </div>
                    
                    <div id="item10">
                        
                        <?php if ($total_nao_finalizados == '0') { ?>

                            <span id="message-box" class="message-yellow">
                                Nenhuma notificação encontrada
                            </span>

                        <?php } else { ?>

                        <table class="grid table_res">

                            <thead>                               

                                <tr class="tr_avisos">
                                    <td>COD</td>
                                    <td>NOME</td>
                                    <td>TIPO DE LICENÇA</td>                                    
                                    <td>DATA RETORNO</td>
                                </tr>

                            </thead>

                            <?php while ($res_nao_finalizados = mysql_fetch_assoc($sql_nao_finalizados)) { ?>

                                    <tbody>

                                        <tr>
                                            <td><?php echo $res_nao_finalizados['id_clt']; ?></td>
                                            <td><?php echo "<a href='../alter_clt.php?clt={$res_nao_finalizados['id_clt']}&pro={$res_nao_finalizados['projeto']}&pagina=clt' class='linkBlack'>{$res_nao_finalizados['nome']}</a>" ?></td>
                                            <td><?php echo $res_nao_finalizados['nome_status']; ?></td>
                                            <td><?php echo $res_nao_finalizados['data_retorno']; ?></td>
                                        </tr>

                                    </tbody>

                            <?php } ?>

                        </table>
                        
                        <?php } ?>
                        
                    </div><!--item10-->

                    <div id="item13">
                        
                        <?php if ($total_cpf == '0') { ?>

                            <span id="message-box" class="message-yellow">
                                Nenhuma notificação encontrada
                            </span>

                        <?php } else { ?>
                        
                        <p id="legenda_item2"><span class="marca_azul">*</span> Clique sobre o nome para editar o cadastro de CLT</p>

                        <table class="grid table_res">

                            <thead>

                                <tr class="tr_avisos">
                                    <td>DOCUMENTO</td>
                                    <td>NOME</td>
                                    <td>NÚMERO</td>
                                    <td>PROJETO</td>
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
                                        $num_cpf++;

                                ?>
                                <tr>
                                    <td><?php echo $doc_cpf; ?></td>
                                    <td><?php echo "<a href='../alter_clt.php?clt={$id}&pro={$projeto}&pagina=clt' class='linkBlack'>{$nome}</a>" ?></td>
                                    <td class="cpf_vermelho"><?php echo $cpf_prov; ?></td>     
                                    <td><?php echo $projeto_cpf; ?></td>
                                </tr>
                            <?php }} ?>
                                
                            <?php
                                while ($res_pis = mysql_fetch_array($sql_pis)) {

                                    $nome = strtoupper($res_pis['nome']);
                                    $pis_prov = $res_pis['pis'];
                                    $id = $res_pis['id_clt'];
                                    $projeto = $res_pis['id_projeto'];
                                    $pis = validaPIS($pis_prov);
                                    $projeto_pis = $res_pis['nome_projeto'];

                                    // Verifica a resposta da funÃ§Ã£o e exibe na tela
                                    if ($pis == false) {
                                        $num_pis++;

                                ?>
                                <tr>
                                    <td><?php echo $doc_pis; ?></td>
                                    <td><?php echo "<a href='../alter_clt.php?clt={$id}&pro={$projeto}&pagina=clt' class='linkBlack'>{$nome}</a>" ?></td>
                                    <td class="cpf_vermelho"><?php echo $pis_prov; ?></td>
                                    <td><?php echo $projeto_pis; ?></td>
                                </tr>
                            <?php }} ?>
                                
                            </tbody>
                        </table>
                        <?php } ?>
                        
                        <?php $total_cadastro = $num_cpf + $num_pis ?>
                        
                    <input type='hidden' name='valorTotal' id='valorTotal' value='<?php echo $total_cadastro; ?>' />
                    </div><!--item13-->

                    <div id="item14">

                        <table class="grid table_res">                                                        

                            <thead>

                                <tr class="tr_avisos">
                                    <td>FUNÇÃO</td>
                                    <td>REGIÃO</td>                             
                                </tr>

                            </thead>

                            <?php while ($res_cbo = mysql_fetch_array($sql_cbo)) { ?>

                                <tbody>

                                    <tr>
                                        <td><?php echo $res_cbo['nome']; ?></td>
                                        <td><?php echo $res_cbo['regiao']; ?></td>
                                    </tr>

                                </tbody>

                            <?php } ?>

                        </table>

                    </div>

                </div><!--dados_avisos-->

                <div class="clear"></div>

            </div><!--conteudo_avisos-->                        

        </div><!--corpo_avisos-->                

    </body>
    
    <script>
        $(function(){
            var valor = $("#valorTotal").val();
            $("#resposta").html(valor);
        });
    </script>

</html>