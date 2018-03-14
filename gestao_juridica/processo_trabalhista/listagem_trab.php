<?php
include ("../include/restricoes.php");
include('../../conn.php');
include('../../funcoes.php');

//include "../funcoes.php";

function formato_brasileiro($data) {

    if ($data != '0000-00-00') {
        echo implode('/', array_reverse(explode('-', $data)));
    }
}

$regiao = $_POST['regiao'];
$id_projeto = $_POST['projeto'];

$qr_regiao = mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '$regiao'");
$row_regiao = mysql_fetch_assoc($qr_regiao);

$qr_projeto = mysql_query("SELECT nome, regiao, id_regiao, id_projeto FROM projeto WHERE id_regiao = '$regiao' AND id_projeto= '$id_projeto'");
$row_projeto = mysql_fetch_assoc($qr_projeto);

$nome_proj = $row_projeto['nome'];


if (isset($_REQUEST['cadastrar']) && $_REQUEST['cadastrar'] == "Cadastrar") {
//    extract($_REQUEST);
    $codigo = $_REQUEST['codigo'];
    $nome = $_REQUEST['nome'];
    $atividade = $_REQUEST['atividade'];
    $id_regiao = $_REQUEST['id_regiao'];
    $unidade = $_REQUEST['unidade'];
    $id_projeto = $_REQUEST['id_projeto'];
    $cpf = str_replace(array(".", "-"), "", $_REQUEST['cpf']);
    $rg = str_replace(array(".", "-"), "", $_REQUEST['rg']);
    $nascimento = date("Y-m-d", strtotime(str_replace("/", "-", $_REQUEST['nascimento'])));
    $data_entrada = date("Y-m-d", strtotime(str_replace("/", "-", $_REQUEST['data_entrada'])));
    $data_saida = date("Y-m-d", strtotime(str_replace("/", "-", $_REQUEST['data_saida'])));
    $empresa = $_REQUEST['empresa'];
    $cad_proc = "INSERT INTO processos_juridicos_externo (codigo,nome,nascimento,rg,cpf,atividade,data_entrada,data_saida,regiao,projeto,unidade,empresa) VALUES 
        ('{$codigo}','{$nome}','{$nascimento}','{$rg}','{$cpf}','{$atividade}','{$data_entrada}','{$data_saida}','{$id_regiao}','{$id_projeto}','{$unidade}','{$empresa}')";
    if (mysql_query($cad_proc)) {
        header("location:index.php?regiao=");
    }
}
?>



<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"  />
        <link href="../../favicon.ico" rel="shortcut icon">
            <title>Listagem dos CLTs</title>
            <link href="../../adm/css/estrutura.css" rel="stylesheet" type="text/css" />
            <link rel="stylesheet" href="http://code.jquery.com/ui/1.9.0/themes/base/jquery-ui.css" />
            <link href="../../jquery/validationEngine/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
            <script src="../../jquery/jquery_ui/js/jquery-1.7.2.min.js"></script>
            <script src="../../jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript"></script>
            <script src="http://code.jquery.com/ui/1.9.0/jquery-ui.js"></script>
            <script src="../../js/abas_anos.js" type="text/javascript"></script>
            <script type="text/javascript" src="../../jquery/validationEngine/jquery.validationEngine.js" ></script>
            <script type="text/javascript" src="../../jquery/validationEngine/jquery.validationEngine-pt.js" ></script>
            <script>
                $(function () {
                    $("#cadProcesso").validationEngine();

                    $("#nascimento").mask("99/99/9999");
                    $("#data_entrada").mask("99/99/9999");
                    $("#data_saida").mask("99/99/9999");

                    $("#cpf").mask("999.999.999-99");
                    $("#nascimento").datepicker({dateFormat: 'dd/mm/yy', changeMonth: true, changeYear: true, minDate: "-100y",
                        maxDate: "-15y",yearRange:"-100y:-15y"});
                    $("#data_entrada").datepicker({dateFormat: 'dd/mm/yy', changeMonth: true, changeYear: true});
                    $("#data_saida").datepicker({dateFormat: 'dd/mm/yy', changeMonth: true, changeYear: true});

                    $(".cadastroProcesso").hide();
                    $('.show').click(function () {
                        $('.show').not(this).removeClass('seta_aberto');
                        $('.show').not(this).addClass('seta_fechado');

                        if ($(this).attr('class') == 'show seta_aberto') {
                            $(this).removeClass('seta_aberto');
                            $(this).addClass('seta_fechado');
                        } else {
                            $(this).removeClass('seta_fechado');
                            $(this).addClass('seta_aberto');
                        }

                        $('.show').not(this).next().hide();
                        $(this).next().css({'width': '100%'}).slideToggle('fast');
                    });

                    $('.azul').parent().css({'background-color': '#e2edfe'});
                    $('.vermelho').parent().css({'background-color': '#ffb8c0'});

                    $(".j_novo").click(function () {
                        $(this).hide();
                        $(".listaProcessos").hide();
                        $(".cadastroProcesso").show();
                    });


                });

            </script>

            <style>

                .voltar {
                    float:right;
                    font-size:12px;
                }

                .voltar a :hover{
                    float:right;
                    font-size:12px;
                    text-decoration:underline;
                }

                /***CSS DO GORDO**/
                .alignField{ text-align: left;}
                .boxColuna{
                    width: 869px;
                    height: 40px;
                    /*border: 1px solid #eee;*/
                    display: block;
                }
                .boxEsc, .boxDir{
                    padding: 10px 0px;
                    width: 260px; 
                    float: left;
                    /*border: 1px solid #ccc;*/
                }
                .boxDir{
                    margin-left: 20px;
                }
                fieldset{ 
                    padding: 20px;
                    border: 1px solid #ccc;
                }
                p label{
                    display: block;
                }
                .input_juridico_01{
                    width: 500px;
                    height: 20px;
                    padding: 5px; 
                    border: 1px solid #ccc;
                    margin-bottom: 5px;
                    margin-top: 3px;
                }
                .input_juridico_02{
                    width: 220px;
                    height: 20px;
                    padding: 5px; 
                    border: 1px solid #ccc;
                    margin-bottom: 5px;
                    margin-top: 3px;
                }
                .botao_juridico_03{
                    padding: 5px ;
                    background:#f3f3f3;
                    border: 1px solid #ccc;
                    float: right;
                    margin-right: 10px;
                }
                .select_juridico_01{
                    width: 200px;
                    height: 30px;
                    padding: 5px; 
                    border: 1px solid #ccc;
                    margin-bottom: 5px;
                    margin-top: 3px;
                }
            </style>


    </head>

    <body>
        <div id="corpo">
            <div id="conteudo">

                <a href="index.php?regiao=<?= $regiao ?>" style="float:left; font-size:13px;" >&laquo; Voltar</a>
                <div class="left"></div>
                <br />
                <br />

                <h3 class="titulo" style="margin-top:50px;"> <?php echo $row_projeto['regiao']; ?></h3>

                <?php
                $qr_projeto = mysql_query("SELECT projeto.id_projeto, projeto.nome,
                                rh_clt.id_clt, rh_clt.nome AS clt_nome, rh_clt.cpf, rh_clt.rg, rh_clt.data_nasci, rh_clt.id_curso, rh_clt.locacao, rh_clt.data_entrada, rh_clt.data_saida
                                FROM projeto
                            INNER JOIN rh_clt 
                            ON projeto.id_projeto = rh_clt.id_projeto
                                WHERE projeto.id_regiao = '$regiao' AND projeto.id_projeto = '$id_projeto' ORDER BY clt_nome") or die(mysql_error("Erro de consulta"));

//                echo "SELECT projeto.id_projeto, projeto.nome,
//                                rh_clt.id_clt, rh_clt.nome AS clt_nome, rh_clt.cpf, rh_clt.rg, rh_clt.data_nasci, rh_clt.id_curso, rh_clt.locacao, rh_clt.data_entrada, rh_clt.data_saida
//                                FROM projeto
//                            INNER JOIN rh_clt 
//                            ON projeto.id_projeto = rh_clt.id_projeto
//                                WHERE projeto.id_regiao = '$regiao' AND projeto.id_projeto = '$id_projeto' ORDER BY clt_nome"; exit;
//IMPRIMIR O NOME DA REGIAO
//if($row_regiao['id_regiao'] != $regiao_anterior) {


                if (mysql_num_rows($qr_projeto) != 0) {
                    ?>
                    <a href="#" class="titulo_ano">CLT</a>

                    <table width="100%" style="font-size:12px;display:none;" class="folhas"  >
                        <tr class="secao_nova">       
                            <td>COD</td>       
                            <td>NOME</td>
                            <td>CPF</td>
                            <td>RG</td>
                            <td>DATA DE NASCIMENTO</td>
                            <td>CARGO</td>
                            <td>UNIDADE</td>
                            <td>DATA DE ENTRADA</td>
                            <td>DATA DE SAIDA</td>
                        </tr>
                        <?php while ($row_projeto = mysql_fetch_assoc($qr_projeto)): ?>				


                            <tr  class="linha_<?php
                            if ($linha++ % 2 == 0) {
                                echo 'um';
                            } else {
                                echo 'dois';
                            }
                            ?> destaque" height="25">    
                                <td align="center"><?php echo $row_projeto['id_clt'] ?></td>   

                                <td align="left">        
                                    <a href="cad_processo.php?trab=<?= $row_projeto['id_clt'] ?>&projeto=<?= $row_projeto['id_projeto'] ?>&regiao=<?= $regiao ?>&tp=2" class="participante">
                                        <?php echo $row_projeto['clt_nome'] ?>
                                        <img src="../../rh/folha/sintetica/seta_<?php
                                        if ($seta++ % 2 == 0) {
                                            echo 'um';
                                        } else {
                                            echo 'dois';
                                        }
                                        ?>.gif">        
                                    </a>
                                </td>

                                <td><?php echo $row_projeto['cpf'] ?>	</td>
                                <td><?php echo $row_projeto['rg'] ?></td>
                                <td><?php formato_brasileiro($row_projeto['data_nasci']) ?></td>

                                <td>
                                    <?php
                                    //Pegando a atividade
                                    $q_atividade = mysql_query("SELECT * FROM curso WHERE id_curso = '$row_projeto[id_curso]'") or die(mysql_error());
                                    $row_atividade = mysql_fetch_assoc($q_atividade);
                                    echo $row_atividade['nome'];
                                    ?>            
                                </td>

                                <td><?php echo $row_projeto['unidade'] ?></td>
                                <td><?php formato_brasileiro($row_projeto['data_entrada']) ?></td>
                                <td><?php formato_brasileiro($row_projeto['data_saida']) ?></td>
                            </tr>

                            <?php
                            $projeto_anterior = $row_projeto['nome'];

                        endwhile; //FIM PROJETO
                        ?>  

                        <tr>
                            <td colspan="9">&nbsp;</td>
                        </tr>
                    </table>

                    <?php
                    //////////////////COOPERADOS, AUTONOMOS, AUTONOMOS/PJ          
                    $qr_contratacao = mysql_query("SELECT * FROM tipo_contratacao WHERE tipo_contratacao_id != 2");
                    while ($row_tp_contratacao = mysql_fetch_assoc($qr_contratacao)):


                        $qr_projeto = mysql_query("SELECT projeto.id_projeto, projeto.nome,
											autonomo.id_autonomo, autonomo.nome AS trab_nome, autonomo.cpf, autonomo.rg, autonomo.data_nasci, autonomo.id_curso, autonomo.locacao, autonomo.data_entrada, autonomo.data_saida						
											FROM projeto
										    INNER JOIN autonomo 
										    ON projeto.id_projeto = autonomo.id_projeto
										   	WHERE projeto.id_regiao = '$regiao' AND autonomo.tipo_contratacao = '$row_tp_contratacao[tipo_contratacao_id]' AND projeto.id_projeto = '$id_projeto' ORDER BY trab_nome") or die(mysql_error());
                        ?>



                        <?php
                        if (mysql_num_rows($qr_projeto) != 0) {
                            ?>

                            <a href="#" class="titulo_ano"><?php echo $row_tp_contratacao['tipo_contratacao_nome']; ?></a> <!-- AQUI FICA O ULTIMO ITEM DA LISTA -->

                            <table width="100%" style="font-size:12px;display:none;" class="folhas" >						   
                                <tr class="secao_nova">    
                                    <td>COD</td>       
                                    <td>NOME</td>
                                    <td>CPF</td>
                                    <td>RG</td>
                                    <td>DATA DE NASCIMENTO</td>
                                    <td>CARGO</td>
                                    <td>UNIDADE</td>
                                    <td>DATA DE ENTRADA</td>
                                    <td>DATA DE SAIDA</td>
                                </tr>
                                <?php
                                while ($row_projeto = mysql_fetch_assoc($qr_projeto)):
                                    ?>			


                                    <tr  class="linha_<?php
                    if ($linha++ % 2 == 0) {
                        echo 'um';
                    } else {
                        echo 'dois';
                    }
                                    ?> destaque" height="25">    
                                        <td align="cneter"><?php echo $row_projeto['id_autonomo'] ?></td>   

                                        <td align="left">        
                                            <a href="cad_processo.php?trab=<?= $row_projeto['id_autonomo'] ?>&projeto=<?= $row_projeto['id_projeto'] ?>&regiao=<?= $regiao ?>&tp=<?php echo $row_tp_contratacao['tipo_contratacao_id']; ?>" class="participante">
                                                <?php echo $row_projeto['trab_nome'] ?>
                                                <img src="../../rh/folha/sintetica/seta_<?php
                                                if ($seta++ % 2 == 0) {
                                                    echo 'um';
                                                } else {
                                                    echo 'dois';
                                                }
                                                ?>.gif">        
                                            </a>
                                        </td>

                                        <td><?php echo $row_projeto['cpf'] ?>	</td>
                                        <td><?php echo $row_projeto['rg'] ?></td>
                                        <td><?php formato_brasileiro($row_projeto['data_nasci']) ?></td>

                                        <td>
                                            <?php
                                            //Pegando a atividade
                                            $q_atividade = mysql_query("SELECT * FROM curso WHERE id_curso = '$row_projeto[id_curso]'") or die(mysql_error());
                                            $row_atividade = mysql_fetch_assoc($q_atividade);
                                            echo $row_atividade['nome'];
                                            ?>            
                                        </td>

                                        <td><?php echo $row_projeto['unidade'] ?></td>
                                        <td><?php formato_brasileiro($row_projeto['data_entrada']) ?></td>
                                        <td><?php formato_brasileiro($row_projeto['data_saida']) ?></td>
                                    </tr>

                                    <?php
                                    $projeto_anterior = $row_projeto['nome'];

                                endwhile; //FIM PROJETO
                            }
                            ?>
                            <tr>
                                <td colspan="9"></td>
                            </tr>
                        </table>



                    <?php endwhile; ?>

                <?php } unset($projeto_anterior); ?>

            </div>
            <a href="#" class="titulo_ano">Externos</a> <!-- AQUI FICA O ULTIMO ITEM DA LISTA -->
            <table width="100%" style="font-size:12px;display:none;" class="folhas" >
                <tr style="margin: 4px; width: 160px; height: 25px; padding: 3px; ">
                    <td colspan='9' style="text-align: left; "><p class="j_novo" style="text-align: center; border: 1px solid #ccc; background: #f2f2f2; padding: 7px 25px; width: 100px; margin: 10px 0px; cursor: pointer;">Novo</p></td>
                </tr>
                <?php
                $sql = mysql_query("SELECT * FROM processos_juridicos_externo WHERE regiao = '{$_REQUEST['regiao']}' AND projeto = '{$_REQUEST['projeto']}'");
                $rows = mysql_num_rows($sql);
                if ($rows > 0) {
                    ?>
                    <tr class="listaProcessos">    
                        <td style="width: 40px">COD</td>       
                        <td style="width: 240px">NOME</td>
                        <td style="width: 140px">CPF</td>
                        <td style="width: 140px">RG</td>
                        <td style="width: 140px">DATA DE NASCIMENTO</td>
                        <td style="width: 140px">ATIVIDADE</td>
                        <td style="width: 140px">UNIDADE</td>
                        <td style="width: 140px">DATA DE ENTRADA</td>
                        <td style="width: 140px">DATA DE SAIDA</td>
                    </tr>
                <?php } ?>

                <?php
                while ($qr_processos = mysql_fetch_assoc($sql)) {
                    ?>
                    <tr class="linha_<?php
                if ($linha++ % 2 == 0) {
                    echo 'um';
                } else {
                    echo 'dois';
                }
                    ?> destaque listaProcessos">    
                        <td><?php echo $qr_processos['codigo']; ?></td>       
                        <td><a href="cad_processo.php?trab=<?php echo $qr_processos['codigo']; ?>&projeto=<?php echo $_REQUEST['projeto']; ?>&regiao=<?php echo $_REQUEST['regiao']; ?>&tp=3&processo=<?php echo $qr_processos['id_processo_ext']; ?>&externo=1"><?php echo $qr_processos['nome']; ?></a></td>
                        <td><?php echo $qr_processos['cpf']; ?></td>
                        <td><?php echo $qr_processos['rg']; ?></td>
                        <td><?php echo $qr_processos['nascimento']; ?></td>
                        <td><?php echo $qr_processos['atividade']; ?></td>
                        <td><?php echo $qr_processos['unidade']; ?></td>
                        <td><?php echo $qr_processos['data_entrada']; ?></td>
                        <td><?php echo $qr_processos['data_saida']; ?></td>
                    </tr>
                <?php } ?>
                <tr>
                    <td colspan="9" class="cadastroProcesso">
                        <form name="cadProcesso" id="cadProcesso" method="POST" action="" >
                            <fieldset>
                                <legend style="margin-left: 10px; text-align: left; text-transform: uppercase; font-size: 14px;" >Cadastro de Processos para não funcionário</legend>

                                <p class="alignField"><label for="regiao">Região:</label>
                                    <input type="text" name="regiao" id="regiao" class="input_juridico_02" value="<?php echo $row_regiao['regiao']; ?>" readonly="readonly" />
                                    <input type="hidden" name="id_regiao" id="id_regiao" value="<?php echo $regiao; ?>" />
                                </p>

                                <p class="alignField"><label for="projeto">Projeto:</label>
                                    <input type="text" name="projeto" id="projeto" class="input_juridico_02" value="<?php echo $nome_proj; ?>" readonly="readonly" />
                                    <input type="hidden" name="id_projeto" id="id_projeto" value="<?php echo $id_projeto; ?>" />
                                </p>

                                <p class="alignField"><label for="unidade">Unidade:</label>
                                    <input type="text" name="unidade" id="unidade" class="input_juridico_02" />
                                </p>

                                <p class="alignField"><label for="codigo">Código:</label><input type="text" name="codigo" id="codigo" value="" class="input_juridico_02" /></p>
                                <p class="alignField"><label for="nome">Nome:</label><input type="text" name="nome" id="nome" value="" class="input_juridico_01 validate[required]"/></p>
                                <p class="alignField"><label for="nascimento">Data Nascimento:</label><input type="text" name="nascimento" id="nascimento" value="" class="input_juridico_02 validate[required]" /></p>

                                <div class="boxColuna">
                                    <div class="boxEsc">
                                        <p class="alignField"><label for="rg">RG:</label><input type="text" name="rg" id="rg" value="" class="input_juridico_02 validate[required]" /></p>
                                    </div>
                                    <div class="boxDir">
                                        <p class="alignField"><label for="cpf">CPF:</label><input type="text" name="cpf" id="cpf" value="" class="input_juridico_02 validate[required]" /></p>
                                    </div>
                                </div><br/><br/><br/>

                                <p class="alignField"><label for="atividade">Atividade:</label><input type="text" name="atividade" id="atividade" value=""  class="input_juridico_01"/></p>

                                <div class="boxColuna">
                                    <div class="boxEsc">
                                        <p class="alignField"><label for="data_entrada">Data Entrada:</label><input type="text" name="data_entrada" id="data_entrada" value="" class="input_juridico_02 validate[required]"/></p>
                                    </div>
                                    <div class="boxDir">
                                        <p class="alignField"><label for="data_saida">Data Saida:</label><input type="text" name="data_saida" id="data_saida" value="" class="input_juridico_02"/></p>
                                    </div>    
                                </div>
                                <br/><br/><br/>
                                <p class="alignField"><label for="nome">Empresa como 1&ordf; Reclamada:</label><input type="text" name="empresa" id="empresa" value="" class="input_juridico_01"/></p>


                                <p><input type="submit" name="cadastrar" id="cadastrar"  value="Cadastrar" class="botao_juridico_03" /></p>
                            </fieldset>
                        </form>
                    </td>
                </tr>
            </table>  
        </div>

    </body>
</html>
