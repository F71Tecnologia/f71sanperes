<?php


/**
 * @author Juarez Garritano
 * @criacao Criação da página solicitada por Sabino Junior.
 * @conteudo Página referente ao "Módulo Educacional.
 */

if(empty($_COOKIE['logado'])){
   header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
   exit;
} 

include('../../conn.php');
include('../../funcoes.php');
include('../../wfunction.php');
include('../../classes/regiao.php');
include "../../classes_permissoes/regioes.class.php";
include('../../classes/EduEscolasClass.php');
require_once "../../classes/LogClass.php";


$REG = new Regioes();
$LOG = new Log();
$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)


//LISTANDO TURNOS
$result_turno = mysql_query("SELECT * FROM edu_escola_turno"); //ISSO VAI MORRER
//$row_turno = mysql_fetch_array($sql_turno);
//$row_turno = mysql_fetch_array($result_turno);

//LISTANDO AS ÁREAS DA ESCOLA
$result_area = mysql_query("SELECT * FROM edu_escola_mapa ORDER BY area_nome"); //ISSO VAI MORRER
//$row_area = mysql_fetch_array($result_area);

$escolasClass = new EduEscolasClass();

if(isset($_REQUEST['enviar'])){

     //DADOS DA ESCOLA
        $id_escola = $_REQUEST['id_escola'];
    
        $cnpj = preg_replace('/[^[:digit:]]/', '',$_REQUEST['cnpj']);
        
        $arrayDados = array(
        'cnpj' => $cnpj,
        'numero_mec' => $_REQUEST['numero_mec'],
        'numero_municipal' => $_REQUEST['numero_municipal'],
        'escola' => $_REQUEST['escola'],
        'abreviado' => $_REQUEST['abreviado'],
        'cep' => $_REQUEST['cep'],
        'endereco' => $_REQUEST['endereco'],
        'numero' => $_REQUEST['numero'],
        'bairro' => $_REQUEST['bairro'],
        'cidade' => $_REQUEST['cidade'],
        'uf' => $_REQUEST['uf'],
        'complemento' => $_REQUEST['complemento'],
        'qtd_max_aluno' => $_REQUEST['qtd_max_aluno'],
        'qtd_max_profissional' => $_REQUEST['qtd_max_profissional'],
        'qtd_turma' => $_REQUEST['qtd_turma'],
        'qtd_andar' => $_REQUEST['qtd_andar']
    );
        //CADASTRO DOS DADOS DA ESCOLA
    if($_REQUEST['procedimento'] == "CADASTRAR"){
        
        //INSERINDO A ESCOLA NO BANCO
        $escolasClass->insereEscola($arrayDados);

        header('Location: visualizar_escolas.php');
    }
    
        //EDIÇÃO DOS DADOS DA ESCOLA
    if($_REQUEST['procedimento'] == "EDITAR"){
        
        //EDITANDO OS DADOS DA ESCOLA NO BANCO
        $escolasClass->editaEscola($id_escola, $arrayDados);
        
        header('Location: visualizar_escolas.php');
    }
    
}

//RESGATANDO OS DADOS PARA SEREM VISUALIZADOS ASSIM QUE ABRIR A PÁGINA DE CADASTRO/EDIÇÃO
if (isset($_REQUEST['id_escola'])) {
    $id_escola = $_REQUEST['id_escola'];
    $row_escolas = $escolasClass->verEscola($id_escola);
    
}
   
       
    //FOREACH PARA TRAZER AS ÁREAS CADASTRADAS
//    $dados_areas= "";
//    $id_areas = $_REQUEST['id_area'];
//    $quantidades = $_REQUEST['qtd'];
//    $andares = $_REQUEST['andar'];
//
//    foreach ($id_areas as $key => $id_area){
//        $dados_areas[$key]['id_area'] = $id_area; 
//    }    
//    foreach ($quantidades as $key => $quantidade){
//        $dados_areas[$key]['qtd'] = $quantidade; 
//    }    
//    foreach ($andares as $key => $andar){        
//        $dados_areas[$key]['andar'] = $andar;        
//    }


    //salvando o log INSERT
    //$LOG ->log(12, "Escola $row_id_escola Inserida com Sucesso", "escolas");
//
//    foreach($dados_areas as $key => $dado_area){
//        $query_areas = "INSERT INTO escola_area
//                        (id_area, id_escola, qtd, andar)
//                        VALUES
//                        ({$dado_area['id_area']}, {$row_id_escola}, {$dado_area['qtd']}, {$dado_area['andar']})";

        //                echo $query_areas;

//        $sql_areas = mysql_query($query_areas);

//        $row_id_area = mysql_insert_id();

        //salvando o log INSERT
        //$LOG ->log(12, "Área $row_id_area Inserida com Sucesso", "escolas_area");
//    }
    //fim foreach dados_area

//}
// fim do $_REQUEST['enviar]


$breadcrumb_config = array("nivel" => "../../", "key_btn" => "52", "area" => "Educacional", "ativo" => "Cadastro/Edição de Escolas", "id_form" => "form1");
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

        <title>::Intranet:: Cadastro/Edição de Escolas</title>
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="page-header box-educacional-header"><h2><span class="fa fa-graduation-cap"></span> - EDUCACIONAL <small> - Cadastro/Edição de Escolas</small></h2></div>
            <form action="cadastro_escola.php" method="post" name="form1" id="form1" class="form-horizontal top-margin1"
                  enctype="multipart/form-data" >
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Cadastrar/Editar</div>
                    <div class="panel-body">


                        <div class="form-group" >
                            <label class="col-sm-2 control-label hidden-print" > CNPJ</label>
                            <div class="col-sm-4">
                                <input type="text" name="cnpj" id="cnpj" class="form-control" value="<?php echo $row_escolas[1]['cnpj']?>"/>
                            </div>
                        </div>
                        
                        <div class="form-group" >
                            <label class="col-sm-2 control-label hidden-print" > Nº MEC</label>
                            <div class="col-sm-4">
                                <input type="text" name="numero_mec" id="numero_mec" class="form-control" value="<?php echo $row_escolas[1]['numero_mec'] ?>"/>
                            </div>

                            <label class="col-sm-2 control-label hidden-print" > Nº Municipal</label>
                            <div class="col-sm-3">
                                <input type="text" name="numero_municipal" id="numero_municipal" class="form-control" value="<?php echo $row_escolas[1]['numero_municipal'] ?>"/>
                            </div>
                        </div>
                        
                        <div class="form-group" >
                            <label class="col-sm-2 control-label hidden-print" > Nome da Escola</label>
                            <div class="col-sm-4">
                                <input type="text" name="escola" id="escola" class="form-control" value="<?php echo $row_escolas[1]['escola'] ?>"/>
                            </div>

                            <label class="col-sm-2 control-label hidden-print"> Nome Abreviado</label>
                            <div class="col-sm-3">
                                <input type="text" name="abreviado" id="abreviado" class="form-control" value="<?php echo $row_escolas[1]['abreviado'] ?>"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label hidden-print">CEP</label>
                            <div class="col-sm-4">
                                <input type="text" name="cep" id="cep" class="form-control" value="<?php echo $row_escolas[1]['cep'] ?>"/>
                            </div>
                        </div>

                        <div class="form-group" >
                            <label class="col-sm-2 control-label hidden-print">Endereço</label>
                            <div class="col-sm-7">
                                <input type="text" name="endereco" id="endereco" class="form-control" value="<?php echo $row_escolas[1]['endereco'] ?>"/>
                            </div>

                            <label class="col-sm-1 control-label hidden-print">Nº</label>
                            <div class="col-sm-1">
                                <input type="text" name="numero" id="numero" class="form-control" value="<?php echo $row_escolas[1]['numero'] ?>"/>
                            </div>
                        </div>

                        <div class="form-group" >
                            <label class="col-sm-2 control-label hidden-print">Bairro</label>
                            <div class="col-sm-3">
                                <input type="text" name="bairro" id="bairro" class="form-control" value="<?php echo $row_escolas[1]['bairro'] ?>"/>
                            </div>

                            <label class="col-sm-1 control-label hidden-print">Cidade</label>
                            <div class="col-sm-3">
                                <input type="text" name="cidade" id="cidade" class="form-control" value="<?php echo $row_escolas[1]['cidade'] ?>"/>
                            </div>

                            <label class="col-sm-1 control-label hidden-print">UF</label>
                            <div class="col-sm-1">
                                <input type="text" name="uf" id="uf" class="form-control" value="<?php echo $row_escolas[1]['uf'] ?>"/>
                            </div>
                        </div>

                        <div class="form-group" >
                            <label class="col-sm-2 control-label hidden-print">Complemento</label>
                            <div class="col-sm-4">
                                <input type="text" name="complemento" id="complemento" class="form-control" value="<?php echo $row_escolas[1]['complemento'] ?>"/>
                            </div>
                        </div>

<!--                        <div class="form-group">
                            <label class="col-sm-2 control-label">Turno</label>
                           <?php while ($row = mysql_fetch_array($result_turno)) { ?>
                                <div class="col-sm-2">
                                    <div class="input-group">
                                        <label class="input-group-addon">
                                            <input type="checkbox" name="turno" id="turno" value="<?php echo $row['id_turno']?>" checked/>
                                        </label>
                                        <label type="text" class="form-control"><?php echo $row['turno']?></label>
                                    </div>
                                </div>
                            <?php
                            }
                            ?>
                        </div>-->

<!--                        <div class="form-group" >
                            <label class="col-sm-2 control-label hidden-print">Horário de Abertura</label>  
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <div class="input-group-addon">Das</div>
                                    <input type="text" name="horario_entrada" id="horario_entrada" class="form-control" value="<?php echo $row_escolas[1]['horario_entrada'] ?>"/>
                                    <div class="input-group-addon">Até</div>
                                    <input type="text" name="horario_saida" id="horario_saida" class="form-control" value="<?php echo $row_escolas[1]['horario_saida'] ?>"/>
                                </div>
                            </div>
                        </div>-->

                        <hr>

                        <div class="form-group" >
                            <label class="col-sm-5 control-label hidden-print">Quantidade Máx de Alunos</label>
                            <div class="col-sm-1">
                                <input type="text" name="qtd_max_aluno" id="qtd_max_aluno" class="form-control" value="<?php echo $row_escolas[1]['qtd_max_aluno'] ?>"/>
                            </div>

                            <label class="col-sm-3 control-label hidden-print">Quantidade Máx de Profissionais</label>
                            <div class="col-sm-1">
                                <input type="text" name="qtd_max_profissional" id="qtd_max_profissional" class="form-control" value="<?php echo $row_escolas[1]['qtd_max_profissional'] ?>"/>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-5 control-label hidden-print">Quantidade de Turmas</label>
                            <div class="col-sm-1">
                                <input type="text" name="qtd_turma" id="qtd_turma" class="form-control" value="<?php echo $row_escolas[1]['qtd_turma'] ?>"/>
                            </div>

                            <label class="col-sm-3 control-label hidden-print">Quantidade de Andares</label>
                            <div class="col-sm-1">
                                <input type="text" name="qtd_andar" id="qtd_andar" class="form-control" value="<?php echo $row_escolas[1]['qtd_andar'] ?>"/>
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
                        if(mysql_num_rows($result_area)) {
                            while($row = mysql_fetch_array($result_area)) {
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
                        }
                        ?>  
                        </div>
                        
                        <div class="panel-footer text-right hidden-print controls">
                            <input type="hidden" name="id_escola" id="id_escola" value="<?php echo $_REQUEST['id_escola'] ?>" />
                            <?php if(isset($_REQUEST['id_escola'])) { ?>
                                <input type="hidden" name="procedimento" value="EDITAR"/>
                            <?php } else { ?>
                                <input type="hidden" name="procedimento" value="CADASTRAR"/>
                            <?php } ?>
                            
                                <?php //if(!isset($_REQUEST['id_escola'])) { ?>
                            <!--<input type="hidden" name="procedimento" value="CADASTRAR"/>-->
                            <?php //}?>
                            <button type="submit" name="enviar" id="enviar" value="ENVIAR" class="btn btn-primary"><span class="fa fa-save"></span> Salvar</button>
                        </div>
                    </div>
               
            </form>

            <div class="clear"></div>
            <?php include('../../template/footer.php'); ?>
        </div>

        <script src="../../js/jquery-1.8.3.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/main_bts.js"></script>
        <script src="../../resources/js/tooltip.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../jquery/mascara/jquery-maskedinput-1.4.1.js"></script>
        
        <script type="text/javascript">

                //Início function
                $(function () {
                    //botão área+
                    $("#area").click(function() {
                        $('.escola_area').toggle('');
                            $(this).find('span').toggleClass('fa-minus fa-plus');
                   });
                
                //preenchimento automático por cep
                    $( "#cep" ).change(function() {
                        var cep = $('#cep').val();
                        console.log(cep)

                        $.getJSON( "http://api.postmon.com.br/v1/cep/"+cep, function( data ) {
                            $('#endereco').val(data.logradouro);
                            $('#bairro').val(data.bairro);
                            $('#cidade').val(data.cidade);
                            $('#uf').val(data.estado);
                        });
                      });
                      
                //máscara para campos
                $("#cnpj").mask("99.999.999/9999-99");
                $("#horario_entrada, #horario_saida").mask("99:99");
   
                });
                //fim function
                    
    
        </script>
    </body>
</html>