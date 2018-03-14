<?php

if(empty($_COOKIE['logado'])){
   header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
   exit;
} 

include('../../conn.php');
include('../../funcoes.php');
include('../../classes/regiao.php');
include('../../wfunction.php');
include "../../classes_permissoes/regioes.class.php";
require_once "../../classes/LogClass.php";
require_once "../../classes/EduEscolasClass.php";

$REG = new Regioes();
$LOG = new Log();
$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABE�ALHO (TROCA DE MASTER E DE REGI�ES)

$id_escola = $_GET['id_escola'];
$id_user = $_COOKIE['logado'];

$escolasClass = new EduEscolasClass();
$result_escolas = $escolasClass->verEscola($id_escola);

if(isset($_REQUEST['atualizar'])){  

//VISUALIZANDO DADOS DA ESCOLA
$cnpj = $_REQUEST['cnpj'];
$numero_mec = $_REQUEST['numero_mec'];
$numero_municipal = $_REQUEST['numero_municipal'];
$nome_escola = utf8_decode($_REQUEST['nome_escola']);
$nome_abreviado = utf8_decode($_REQUEST['nome_abreviado']);
$cep_escola = $_REQUEST['cep_escola'];
$endereco_escola = utf8_decode($_REQUEST['endereco_escola']);
$numero_endereco_escola = $_REQUEST['numero_endereco_escola'];
$bairro_escola = utf8_decode($_REQUEST['bairro_escola']);
$cidade_escola = utf8_decode($_REQUEST['cidade_escola']);
$uf_escola = $_REQUEST['uf_escola'];
$complemento_escola = utf8_decode($_REQUEST['complemento_escola']);
//$turno_escola = $_REQUEST['turno_escola'];
$horario_entrada = $_REQUEST['horario_entrada'];
$horario_saida = $_REQUEST['horario_saida'];
$qtd_max_aluno = $_REQUEST['qtd_max_aluno'];
$qtd_max_profissional = $_REQUEST['qtd_max_profissional'];
$qtd_turma = $_REQUEST['qtd_turma'];
$qtd_andar = $_REQUEST['qtd_andar'];


//FOREACH PARA TRAZER AS �REAS CADASTRADAS
$dados_areas= "";
$id_areas = $_REQUEST['id_area'];
$quantidades = $_REQUEST['qtd'];
$andares = $_REQUEST['andar'];

    foreach ($id_areas as $key => $id_area){
        $dados_areas[$key]['id_area'] = $id_area; 
    }    
    foreach ($quantidades as $key => $quantidade){
        $dados_areas[$key]['qtd'] = $quantidade; 
    }    
    foreach ($andares as $key => $andar){        
        $dados_areas[$key]['andar'] = $andar;        
    }

    

$antigoEscola = $LOG -> getLinha("escolas", $id_escola);

header('Location: ver_escola.php?id_escola=' . $id_escola);
                            

$novoEscola = $LOG -> getLinha("escolas", $id_escola);
//salvando log no UPDATE escola
$LOG ->log(12, "Escola $id_escola atualizada com Sucesso", "escolas", $antigoEscola, $novoEscola);


foreach($dados_areas as $key => $dado_area){
    
$antigoArea = $LOG -> getLinha("escola_area", $id_area);
$update_areas = mysql_query("UPDATE escola_area SET
                id_area = {$dado_area['id_area']}, id_escola = {$id_escola}, qtd = {$dado_area['qtd']}, andar = {$dado_area['andar']}");

}
//fim foreach dados_area

$novoArea = $LOG -> getLinha("escolas_area", $id_area);
//salvando log no UPDATE area
$LOG ->log(12, "�rea $id_area atualizada com Sucesso", "escola_area", $antigoArea, $novoArea);

}
// fim do $_REQUEST['atualizar']

$breadcrumb_config = array("nivel" => "../../", "key_btn" => "52", "area" => "Educacional", "ativo" => "Cadastro/Edi��o de Dados da Escolas", "id_form" => "form1");
$breadcrumb_pages = array("Unidade Escolar" => "../unidade_escolar.php");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head>
        <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
        <!--<link href="../../rh/css/estrutura_cadastro.css" rel="stylesheet" type="text/css"/>-->

        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="all"/>
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="all"/>
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen"/>
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="screen"/>
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen"/>
        <!--<link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />-->
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen"/>
        <link href="../../jquery/validationEngine/validationEngine.jquery.css" rel="stylesheet" type="text/css"/>

        <title>::Intranet:: Cadastro/Edi��o de Escolas</title>
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="page-header box-educacional-header"><h2><span class="fa fa-graduation-cap"></span> - EDUCACIONAL <small> - Cadastro/Edi��o de Dados da Escolas</small></h2></div>
            <form action="<?php echo $_SERVER['PHP_SELF']?>?id_escola=<?=$id_escola?>" method="post" name="form1" id="form1" class="form-horizontal top-margin1">
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Cadastrar</div>
                    <div class="panel-body">

                        <input type="hidden" name="id_escola" id="id_escola" />

                        <div class="form-group" >
                            <label class="col-sm-2 control-label hidden-print" > CNPJ</label>
                            <div class="col-sm-4">
                                <input type="text" name="cnpj" id="cnpj" class="form-control" value="<?php echo $result_escolas[1]['cnpj'] ?>"/>
                            </div>
                        </div>
                        
                        <div class="form-group" >
                            <label class="col-sm-2 control-label hidden-print" > N� MEC</label>
                            <div class="col-sm-4">
                                <input type="text" name="numero_mec" id="numero_mec" class="form-control" value="<?php echo $result_escolas[1]['numero_mec'] ?>"/>
                            </div>

                            <label class="col-sm-2 control-label hidden-print" > N� Municipal</label>
                            <div class="col-sm-3">
                                <input type="text" name="numero_municipal" id="numero_municipal" class="form-control" value="<?php echo $result_escolas[1]['numero_municipal'] ?>"/>
                            </div>
                        </div>
                        
                        <div class="form-group" >
                            <label class="col-sm-2 control-label hidden-print" > Nome da Escola</label>
                            <div class="col-sm-4">
                                <input type="text" name="escola" id="escola" class="form-control" value="<?php echo $result_escolas[1]['escola'] ?>" />
                            </div>

                            <label class="col-sm-2 control-label hidden-print" > Nome Abreviado</label>
                            <div class="col-sm-3">
                                <input type="text" name="abreviado" id="abreviado" class="form-control" value="<?php echo $result_escolas[1]['abreviado'] ?>" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label hidden-print">CEP</label>
                            <div class="col-sm-4">
                                <input type="text" name="cep" id="cep" class="form-control" value="<?php echo $result_escolas[1]['cep'] ?>"/>
                            </div>
                        </div>

                        <div class="form-group" >
                            <label class="col-sm-2 control-label hidden-print">Endere�o</label>
                            <div class="col-sm-7">
                                <input type="text" name="endereco" id="endereco" class="form-control" value="<?php echo $result_escolas[1]['endereco'] ?>"/>
                            </div>

                            <label class="col-sm-1 control-label hidden-print">N�</label>
                            <div class="col-sm-1">
                                <input type="text" name="numero" id="numero" class="form-control" value="<?php echo $result_escolas[1]['numero'] ?>"/>
                            </div>
                        </div>

                        <div class="form-group" >
                            <label class="col-sm-2 control-label hidden-print">Bairro</label>
                            <div class="col-sm-3">
                                <input type="text" name="bairro" id="bairro" class="form-control" value="<?php echo $result_escolas[1]['bairro'] ?>"/>
                            </div>

                            <label class="col-sm-1 control-label hidden-print">Cidade</label>
                            <div class="col-sm-3">
                                <input type="text" name="cidade" id="cidade" class="form-control" value="<?php echo $result_escolas[1]['cidade'] ?>"/>
                            </div>

                            <label class="col-sm-1 control-label hidden-print">UF</label>
                            <div class="col-sm-1">
                                <input type="text" name="uf" id="uf" class="form-control" value="<?php echo $result_escolas[1]['uf'] ?>"/>
                            </div>
                        </div>

                        <div class="form-group" >
                            <label class="col-sm-2 control-label hidden-print">Complemento</label>
                            <div class="col-sm-4">
                                <input type="text" name="complemento" id="complemento" class="form-control" value="<?php echo $result_escolas[1]['complemento'] ?>"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <!--<label class="col-sm-2 control-label">Turno</label>-->
                           <?php //while ($row = mysql_fetch_array($query_escolas)) { ?>
<!--                                <div class="col-sm-2">
                                    <div class="input-group">
                                        <label class="input-group-addon">
                                            <input type="checkbox" name="turno_escola" id="turno_escola" value="<?php echo $row['id_turno']?>" checked/>
                                        </label>
                                        <label type="text" class="form-control" value="<?php echo $row['turno'] ?>"></label>
                                    </div>
                                </div>-->
                            <?php
//                            }
                            ?>
                          
                            <!-- <div class="col-sm-2">
                                <div class="input-group">
                                    <label class="input-group-addon">
                                        <input type="radio" name="turno" value="2" />
                                    </label>
                                    <label type="text" class="form-control">Tarde</label>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="input-group">
                                    <label class="input-group-addon">
                                        <input type="radio" name="turno" value="3" />
                                    </label>
                                    <label type="text" class="form-control">Noite</label>
                                </div>
                            </div>-->
                        </div>

                        <div class="form-group" >
                            <label class="col-sm-2 control-label hidden-print">Hor�rio de Abertura</label>  
                            <div class="col-sm-4">
                                    <?php
//                                    $query_horario = mysql_query("SELECT * FROM escola_horarios AS A LEFT JOIN escola_turno AS B ON (A.id_horario = B.id_turno)");
//                                    $verifica_horario = mysql_num_rows($query_horario);
//                                    if(!empty($verifica_horario)) {
//                                        $idHorario = '';
//                                        echo '<select name="horario_escola" id="horario_escola" class="form-control" >
//                                                <option value="0" selected="selected"> Selecione um hor�rio </option>';
//                                        while ($row_horario = mysql_fetch_array($query_horario)){
//                                            echo "<option value='$row_horario[id_horario]'>" . $row_horario['horario'] . ' - ' . "(".$row_horario['turno'].")" . "</option>";
////                                            $idHorario = $row_horario['id_horario'];
//                                        }
//                                        echo '</select>';
//                                    }
                                   // $query_horario = mysql_query("SELECT * FROM escola_horarios AS A LEFT JOIN escola_turno AS B ON (A.id_horario = B.id_turno)");
//                                    $verifica_horario = mysql_num_rows($query_horario);
//                                    if(!empty($verifica_horario)) {
//                                        $idHorario = '';
//                                        echo '<select name="horario_escola" id="horario_escola" class="form-control" >
//                                                <option value="0" selected="selected"> Selecione um hor�rio </option>';
//                                        while ($row_horario = mysql_fetch_array($query_horario)){
//                                            echo "<option value='$row_horario[id_horario]'>" . $row_horario['horario'] . ' - ' . "(".$row_horario['turno'].")" . "</option>";
////                                            $idHorario = $row_horario['id_horario'];
//                                        }
//                                        echo '</select>';
//                                    }
//                                    ?>
                                <div class="input-group">
                                    <div class="input-group-addon">Das</div>
                                    <input type="text" name="horario_entrada" id="horario_entrada" class="form-control" value="<?php echo $result_escolas['horario_entrada'] ?>"/>
                                    <div class="input-group-addon">At�</div>
                                    <input type="text" name="horario_saida" id="horario_saida" class="form-control" value="<?php echo $result_escolas['horario_saida'] ?>"/>
                                </div>
                            </div>
                        </div>

                        <hr>

                            <div class="form-group" >
                                <label class="col-sm-5 control-label hidden-print">Quantidade M�x de Alunos</label>
                                <div class="col-sm-1">
                                    <input type="text" name="qtd_max_aluno" id="qtd_max_aluno" class="form-control" value="<?php echo $result_escolas[1]['qtd_max_aluno'] ?>"/>
                                </div>

                                <label class="col-sm-3 control-label hidden-print">Quantidade M�x de Profissionais</label>
                                <div class="col-sm-1">
                                    <input type="text" name="qtd_max_profissional" id="qtd_max_profissional" class="form-control" value="<?php echo $result_escolas[1]['qtd_max_profissional'] ?>"/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-5 control-label hidden-print">Quantidade de Turmas</label>
                                <div class="col-sm-1">
                                    <input type="text" name="qtd_turma" id="qtd_turma" class="form-control" value="<?php echo $result_escolas[1]['qtd_turma'] ?>"/>
                                </div>

                                <label class="col-sm-3 control-label hidden-print">Quantidade de Andares</label>
                                <div class="col-sm-1">
                                    <input type="text" name="qtd_andar" id="qtd_andar" class="form-control" value="<?php echo $result_escolas[1]['qtd_turma'] ?>"/>
                                </div>
                            </div>
                    </div>

                    <div class="panel panel-default text-bold"><div class="panel-heading">Mapa da Escola</div></div>
                        <div class="panel-footer text-right">    
                            <a class="btn btn-success" id="area"><span class="fa fa-plus"></span> �rea</a>
                            <!--<input type="checkbox" id="entrada" name="entrada" />-->
                        </div>
                    
                        <div class="col-sm-12 margin_t10 escola_area" style="display: none;">
                        <?php
//                        if(mysql_num_rows($query_escolas)) {
                            while($row = mysql_fetch_assoc($query_escolas)) {
                                
                        ?>
                            <div class="col-sm-6">
                                
                                <table class="table table-condensed table-bordered table-responsive text-sm valign-middle">
                                    <thead>
                                        <tr>
                                            <th colspan="3">�rea</th>
                                            <th>Quantidade</th>
                                            <th>Andar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <input type="hidden" name="id_area[]" id="id_area" value="<?php echo $row['id_area'] ?>" />
                                            <td colspan="3"><?php echo $row['area_nome'] ?></td>
                                            <td class="col-sm-2"><input type="text" name="qtd[]" id="qtd" value="<?php echo $row['qtd'] ?>" class="form-control"/></td>
                                            <td class="col-sm-2"><input type="text" name="andar[]" id="andar" value="<?php echo $row['andar'] ?>" class="form-control"/></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <?php 
                            } 
//                        }
                        ?>  
                        </div>
               

                        <div class="panel-footer text-right hidden-print controls">
                            <button type="submit" name="atualizar" id="atualizar" value="atualizar" class="btn btn-success"><span class="fa fa-refresh"></span> Atualizar</button>
                        </div>
                    </div>
               
            </form>

            <div class="clear"></div>
            <?php include('../../template/footer.php'); ?>
        </div>

        <script src="../../js/jquery-1.8.3.min.js"></script>
        <!--<script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>-->
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <!--<script src="../../resources/js/main_bts.js"></script>-->
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <!--<script type="text/javascript" src="../../js/ramon.js"></script>-->
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js"></script>
        <!--<script type="text/javascript" src="../../jquery/priceFormat.js" ></script>-->

        <script type="text/javascript">

                //In�cio function
                $(document).ready(function () {
                    //bot�o �rea+
                    $("#area").click(function() {
                        $('.escola_area').toggle('');
                            $(this).find('span').toggleClass('fa-minus fa-plus');
                   });
                
                //preenchimento autom�tico por cep
                    $("#cep").change(function() {
                        var cep = $('#cep').val();
                        console.log(cep)

                        $.getJSON( "http://api.postmon.com.br/v1/cep/"+cep, function( data ) {
                            $('#endereco').val(data.logradouro);
                            $('#bairro').val(data.bairro);
                            $('#cidade').val(data.cidade);
                            $('#uf').val(data.estado);
                        });
                      });
                      
                //validation engine 
//                $("#form1").validationEngine();
                
                //confirma��o ou erro de cadastro
//                $("#form1").submit(function () {
//                    if ($("button[type='submit']").val() == "ATUALIZAR") {
//                        alert("Dados cadastrados com sucesso!");
//                    } 
//                    else {
//                        alert("Dados n�o cadastrados!");
//                    }
//                    
//                    });
                    
                //m�scara para campos
                $("#cnpj").mask("99.999.999/9999-99");
                $("#horario_entrada, #horario_saida").mask("99:99");
//                $("#cep").mask("99.999-999");

//                $("input[type='radio']").change(function () {
//                    var select = "";
//                    $("select option:selected").each(function(){
//                        select += $(this).val + "";
//                    })
//                });
//                
//                $("#turno_escola").click(function () {
//                    var horarioEscola = $('#horario_escola').val();
//                    $('#horario_escola option[value="' + horarioEscola + '"]').attr({selected : "selected"});
//                });

                    //alterando hor�rio pelo turno
//                    $(function(){
//                        $("input[name='turno_escola']").click(function() {
//                                alert("Teste");
//                            var valorRadio = $(this).val();
//                                    alert(valorRadio);

//                                if(valorRadio == 1){
//                                        $bootAlert("Selecione primeiro o campo turno", "Selecione o campo turno", null, "warning");
//                                    $("#horario_escola:option:nth-child(2)").attr{(selected : "selected")};
//                                }
//                                if(valorRadio == 2){
//                                        bootAlert("Selecione primeiro o campo turno", "Selecione o campo turno", null, "warning");
//                                }
//                                if(valorRadio == 3){
//                                        bootAlert("Selecione primeiro o campo turno", "Selecione o campo turno", null, "warning");
//                                }

//                        });

                    //alert para altera��o no cadastro
//                    $("#form1").submit(function() {
//                        if($("button[type='submit']") == "atualizar"){
//                            alert("Dados Atualizados Com Sucesso");
////                            location.href="lista_escolas.php";
////                            bootConfirm("Confira os dados antes de atualizar as informa��es", "Os dados est�o corretos?", null, "success");
//                        };
//                    });
                
                });
                //fim function
                    
    
        </script>
    </body>
</html>