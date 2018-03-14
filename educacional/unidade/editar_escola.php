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

$REG = new Regioes();
$LOG = new Log();
$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$id_escola = $_GET['id_escola'];
$id_user = $_COOKIE['logado'];

$result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);

$query_escolas = mysql_query("SELECT *
                                FROM escolas AS A
                                LEFT JOIN escola_turno AS B ON(A.turno_escola = B.id_turno)
                                LEFT JOIN escola_area AS C ON(A.id_escola = C.id_escola)
                                LEFT JOIN escola_mapa AS D ON(C.id_area = D.id_area)
                                WHERE A.id_escola = $id_escola");
$row_escolas =  mysql_fetch_assoc($query_escolas);


if(isset($_REQUEST['atualizar'])){  
//
//    echo '<pre>';
//    print_r($_REQUEST);
//    echo '<pre>';

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
$cidade_escola = utf_decode($_REQUEST['cidade_escola']);
$uf_escola = $_REQUEST['uf_escola'];
$complemento_escola = utf_decode($_REQUEST['complemento_escola']);
//$turno_escola = $_REQUEST['turno_escola'];
$horario_entrada = $_REQUEST['horario_entrada'];
$horario_saida = $_REQUEST['horario_saida'];
$qtd_max_aluno = $_REQUEST['qtd_max_aluno'];
$qtd_max_profissional = $_REQUEST['qtd_max_profissional'];
$qtd_turma = $_REQUEST['qtd_turma'];
$qtd_andar = $_REQUEST['qtd_andar'];


//FOREACH PARA TRAZER AS ÁREAS CADASTRADAS
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

//pegando o valores na tabela escola
$update_escolas = mysql_query("UPDATE escolas SET 
                            cnpj = '{$cnpj}', numero_mec = '{$numero_mec}', numero_municipal = '{$numero_municipal}', nome_escola = '{$nome_escola}',
                            nome_abreviado = '{$nome_abreviado}', cep_escola = '{$cep_escola}', endereco_escola = '{$endereco_escola}',
                            numero_endereco_escola = '{$numero_endereco_escola}', bairro_escola = '{$bairro_escola}', cidade_escola = '{$cidade_escola}',
                            uf_escola = '{$uf_escola}', complemento_escola = '{$complemento_escola}',
                            horario_entrada = '{$horario_entrada}', horario_saida = '{$horario_saida}', qtd_max_aluno = {$qtd_max_aluno},
                            qtd_max_profissional = {$qtd_max_profissional}, qtd_turma = {$qtd_turma}, qtd_andar = {$qtd_andar} WHERE id_escola = $id_escola");

                            header('Location: editar_escola.php?id_escola=' . $id_escola);
                            
//echo $update_escolas;


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
$LOG ->log(12, "Área $id_area atualizada com Sucesso", "escola_area", $antigoArea, $novoArea);

}
// fim do $_REQUEST['atualizar']

$breadcrumb_config = array("nivel" => "../../", "key_btn" => "52", "area" => "Educacional", "ativo" => "Edição dos Dados da Escolas", "id_form" => "form1");
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

        <title>::Intranet:: Edição dos Dados da Escolas</title>
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="page-header box-educacional-header"><h2><span class="fa fa-graduation-cap"></span> - EDUCACIONAL <small> - Edição dos Dados da Escolas</small></h2></div>
            <form action="<?php echo $_SERVER['PHP_SELF']?>?id_escola=<?=$id_escola?>" method="post" name="form1" id="form1" class="form-horizontal top-margin1">
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Cadastrar</div>
                    <div class="panel-body">

                        <input type="hidden" name="id_escola" id="id_escola" />

                        <div class="form-group" >
                            <label class="col-sm-2 control-label hidden-print" > CNPJ</label>
                            <div class="col-sm-4">
                                <input type="text" name="cnpj" id="cnpj" class="form-control" value="<?php echo $row_escolas['cnpj'] ?>"/>
                            </div>
                        </div>
                        
                        <div class="form-group" >
                            <label class="col-sm-2 control-label hidden-print" > Nº MEC</label>
                            <div class="col-sm-4">
                                <input type="text" name="numero_mec" id="numero_mec" class="form-control" value="<?php echo $row_escolas['numero_mec'] ?>"/>
                            </div>

                            <label class="col-sm-2 control-label hidden-print" > Nº Municipal</label>
                            <div class="col-sm-3">
                                <input type="text" name="numero_municipal" id="numero_municipal" class="form-control" value="<?php echo $row_escolas['numero_municipal'] ?>"/>
                            </div>
                        </div>
                        
                        <div class="form-group" >
                            <label class="col-sm-2 control-label hidden-print" > Nome da Escola</label>
                            <div class="col-sm-4">
                                <input type="text" name="nome_escola" id="nome_escola" class="form-control" value="<?php echo $row_escolas['nome_escola'] ?>" />
                            </div>

                            <label class="col-sm-2 control-label hidden-print" > Nome Abreviado</label>
                            <div class="col-sm-3">
                                <input type="text" name="nome_abreviado" id="nome_abreviado" class="form-control" value="<?php echo $row_escolas['nome_abreviado'] ?>" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label hidden-print">CEP</label>
                            <div class="col-sm-4">
                                <input type="text" name="cep_escola" id="cep_escola" class="form-control" value="<?php echo $row_escolas['cep_escola'] ?>"/>
                            </div>
                        </div>

                        <div class="form-group" >
                            <label class="col-sm-2 control-label hidden-print">Endereço</label>
                            <div class="col-sm-7">
                                <input type="text" name="endereco_escola" id="endereco_escola" class="form-control" value="<?php echo $row_escolas['endereco_escola'] ?>"/>
                            </div>

                            <label class="col-sm-1 control-label hidden-print">Nº</label>
                            <div class="col-sm-1">
                                <input type="text" name="numero_endereco_escola" id="numero_endereco_escola" class="form-control" value="<?php echo $row_escolas['numero_endereco_escola'] ?>"/>
                            </div>
                        </div>

                        <div class="form-group" >
                            <label class="col-sm-2 control-label hidden-print">Bairro</label>
                            <div class="col-sm-3">
                                <input type="text" name="bairro_escola" id="bairro_escola" class="form-control" value="<?php echo $row_escolas['bairro_escola'] ?>"/>
                            </div>

                            <label class="col-sm-1 control-label hidden-print">Cidade</label>
                            <div class="col-sm-3">
                                <input type="text" name="cidade_escola" id="cidade_escola" class="form-control" value="<?php echo $row_escolas['cidade_escola'] ?>"/>
                            </div>

                            <label class="col-sm-1 control-label hidden-print">UF</label>
                            <div class="col-sm-1">
                                <input type="text" name="uf_escola" id="uf_escola" class="form-control" value="<?php echo $row_escolas['uf_escola'] ?>"/>
                            </div>
                        </div>

                        <div class="form-group" >
                            <label class="col-sm-2 control-label hidden-print">Complemento</label>
                            <div class="col-sm-4">
                                <input type="text" name="complemento_escola" id="complemento_escola" class="form-control" value="<?php echo $row_escolas['complemento_escola'] ?>"/>
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
                            <label class="col-sm-2 control-label hidden-print">Horário de Abertura</label>  
                            <div class="col-sm-4">
                                    <?php
//                                    $query_horario = mysql_query("SELECT * FROM escola_horarios AS A LEFT JOIN escola_turno AS B ON (A.id_horario = B.id_turno)");
//                                    $verifica_horario = mysql_num_rows($query_horario);
//                                    if(!empty($verifica_horario)) {
//                                        $idHorario = '';
//                                        echo '<select name="horario_escola" id="horario_escola" class="form-control" >
//                                                <option value="0" selected="selected"> Selecione um horário </option>';
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
//                                                <option value="0" selected="selected"> Selecione um horário </option>';
//                                        while ($row_horario = mysql_fetch_array($query_horario)){
//                                            echo "<option value='$row_horario[id_horario]'>" . $row_horario['horario'] . ' - ' . "(".$row_horario['turno'].")" . "</option>";
////                                            $idHorario = $row_horario['id_horario'];
//                                        }
//                                        echo '</select>';
//                                    }
//                                    ?>
                                <div class="input-group">
                                    <div class="input-group-addon">Das</div>
                                    <input type="text" name="horario_entrada" id="horario_entrada" class="form-control" value="<?php echo $row_escolas['horario_entrada'] ?>"/>
                                    <div class="input-group-addon">Até</div>
                                    <input type="text" name="horario_saida" id="horario_saida" class="form-control" value="<?php echo $row_escolas['horario_saida'] ?>"/>
                                </div>
                            </div>
                        </div>

                        <hr>

                            <div class="form-group" >
                                <label class="col-sm-5 control-label hidden-print">Quantidade Máx de Alunos</label>
                                <div class="col-sm-1">
                                    <input type="text" name="qtd_max_aluno" id="qtd_max_aluno" class="form-control" value="<?php echo $row_escolas['qtd_max_aluno'] ?>"/>
                                </div>

                                <label class="col-sm-3 control-label hidden-print">Quantidade Máx de Profissionais</label>
                                <div class="col-sm-1">
                                    <input type="text" name="qtd_max_profissional" id="qtd_max_profissional" class="form-control" value="<?php echo $row_escolas['qtd_max_profissional'] ?>"/>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-5 control-label hidden-print">Quantidade de Turmas</label>
                                <div class="col-sm-1">
                                    <input type="text" name="qtd_turma" id="qtd_turma" class="form-control" value="<?php echo $row_escolas['qtd_turma'] ?>"/>
                                </div>

                                <label class="col-sm-3 control-label hidden-print">Quantidade de Andares</label>
                                <div class="col-sm-1">
                                    <input type="text" name="qtd_andar" id="qtd_andar" class="form-control" value="<?php echo $row_escolas['qtd_turma'] ?>"/>
                                </div>
                            </div>
                    </div>

                    <div class="panel panel-default text-bold"><div class="panel-heading">Mapa da Escola</div></div>
                        <div class="panel-footer text-right">    
                            <a class="btn btn-success" id="area"><span class="fa fa-plus"></span> Área</a>
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
                                            <th colspan="3">Área</th>
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
                            <button type="submit" name="atualizar" id="atualizar" value="ATUALIZAR" class="btn btn-success"><span class="fa fa-refresh"></span> Atualizar</button>
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

                //Início function
                $(document).ready(function () {
                    //botão área+
                    $("#area").click(function() {
                        $('.escola_area').toggle('');
                            $(this).find('span').toggleClass('fa-minus fa-plus');
                   });
                
                //preenchimento automático por cep
                    $( "#cep_escola" ).change(function() {
                        var cep = $('#cep_escola').val();
                        console.log(cep)

                        $.getJSON( "http://api.postmon.com.br/v1/cep/"+cep, function( data ) {
                            $('#endereco_escola').val(data.logradouro);
                            $('#bairro_escola').val(data.bairro);
                            $('#cidade_escola').val(data.cidade);
                            $('#uf_escola').val(data.estado);
                        });
                      });
                      
                //validation engine 
//                $("#form1").validationEngine();
                
                //confirmação ou erro de cadastro
                $("#form1").submit(function () {
                    if ($("button[type='submit']").val() == "ATUALIZAR") {
                        alert("Dados cadastrados com sucesso!");
                    } 
                    else {
                        alert("Dados não cadastrados!");
                    }
                    
                    });
                    
                //máscara para campos
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

                    //alterando horário pelo turno
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

                    //alert para alteração no cadastro
//                    $("#form1").submit(function() {
//                        if($("button[type='submit']") == "atualizar"){
//                            alert("Dados Atualizados Com Sucesso");
////                            location.href="lista_escolas.php";
////                            bootConfirm("Confira os dados antes de atualizar as informações", "Os dados estão corretos?", null, "success");
//                        };
//                    });
                
                });
                //fim function
                    
    
        </script>
    </body>
</html>