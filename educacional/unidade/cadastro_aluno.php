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
include('../../classes/regiao.php');
include('../../wfunction.php');
include "../../classes_permissoes/regioes.class.php";
include('../../classes/EduAlunosClass.php');


$REG = new Regioes();
$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$alunosClass = new EduAlunosClass();

if(isset($_POST['id_aluno'])){
    $id_aluno = $_POST['id_aluno'];
    $row_alunos = $alunosClass->verAluno($id_aluno);
}

//CADASTRO DO ALUNO
if(isset($_REQUEST['enviar'])){
    
//TRATANDO (RETIRA) OS CARACTERES DO CNPJ PARA INSERIR NO BANCO DE DADOS
$rg = preg_replace('/[^[:digit:]]/', '',$_REQUEST['rg']);
$cpf = preg_replace('/[^[:digit:]]/', '',$_REQUEST['cpf']);

$id_escola = $_GET['id_escola'];
$id_turma = $_GET['id_turma'];
                
    //DADOS DO ALUNO
    $arrayDados = array(
        'id_escola' => $id_escola,
        'id_turma' => $id_turma,
        'matricula' => $_REQUEST['matricula'],
        'aluno' => $_REQUEST['aluno'],
        'email' => $_REQUEST['email'],
        'data_nascimento' => converteData($_REQUEST['data_nascimento']),
        'rg' => $rg,
        'cpf' => $cpf,
        'cep' => $_REQUEST['cep'],
        'endereco' => $_REQUEST['endereco'],
        'numero' => $_REQUEST['numero'],
        'complemento' => $_REQUEST['complemento'],
        'bairro' => $_REQUEST['bairro'],
        'cidade' => $_REQUEST['cidade'],
        'uf' => $_REQUEST['uf'],
        'telefone' => $_REQUEST['telefone'],
        'celular' => $_REQUEST['celular'],
        'responsavel' => $_REQUEST['responsavel'],
        'celular_responsavel' => $_REQUEST['celular_responsavel']
    );

    //CADASTRO DE DADOS DO ALUNO
    if($_REQUEST['procedimento'] == "CADASTRAR"){
        //INSERINDO O ALUNO NO BANCO
        $alunosClass->insereAluno($arrayDados);

    header('location: visualizar_aluno.php?id_turma=' . $arrayDados['id_turma']);
}

    //EDIÇÃO DOS DADOS DO ALUNO
    if($_REQUEST['procedimento'] == "EDITAR"){
        $id_aluno = $_POST['id_aluno'];
        $dadosAluno = $alunosClass->verAluno($id_aluno);
       
        $arrayDados['id_turma'] = $dadosAluno[1]['id_turma']; 
        $arrayDados['id_escola'] = $dadosAluno[1]['id_escola']; 

        //EDITANDO O ALUNO NO BANCO
        $alunosClass->editaAluno($id_aluno, $arrayDados);

        header('location: visualizar_aluno.php?id_turma=' . $arrayDados['id_turma']);
    }

} // fim do $_REQUEST['enviar]

$breadcrumb_config = array("nivel" => "../../", "key_btn" => "52", "area" => "Educacional", "ativo" => "Cadastro/Edição de Aluno", "id_form" => "form1");

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

        <title>::Intranet:: Cadastro de Alunos</title>
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="page-header box-educacional-header"><h2><span class="fa fa-graduation-cap"></span> - EDUCACIONAL <small> - Cadastro/Edição de Alunos</small></h2></div>
            <form action="" method="post" name="form1" id="form1" class="form-horizontal top-margin1"
                  enctype="multipart/form-data" >
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Cadastrar/Editar</div>
                    <div class="panel-body">

                        <input type="hidden" name="id_aluno" id="id_aluno" value="<?php echo $id_aluno ?>"/>
                        <input type="hidden" name="id_escola" id="id_escola" value="<?php echo $id_escola ?>"/>
                        <input type="hidden" name="id_turma" id="id_turma" value="<?php echo $id_turma ?>"/>
                        
                            <div class="col-sm-2">
                                <div class="form-group" >
    <!--                                <label class="col-sm-2 control-label hidden-print" > Foto</label>-->
                                    <div class="col-sm-4">
                                        <!--<input type="text" name="foto_aluno" id="foto_aluno"/>-->
                                        <img src="http://placehold.it/100x100" /> 
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-10">
                                <div class="form-group" >
                                    <label class="col-sm-2 control-label hidden-print" > Matrícula do Aluno</label>
                                    <div class="col-sm-1">
                                        <input type="text" name="matricula" id="matricula" class="form-control" value="<?php echo $row_alunos[1]['matricula'] ?>"/>
                                    </div>
                                </div>

                                <div class="form-group" >
                                    <label class="col-sm-2 control-label hidden-print" > Nome do Aluno</label>
                                    <div class="col-sm-7">
                                        <input type="text" name="aluno" id="aluno" class="form-control" value="<?php echo $row_alunos[1]['aluno'] ?>"/>
                                    </div>
                                </div>

                                <div class="form-group" >
                                    <label class="col-sm-2 control-label hidden-print" > Email do Aluno</label>
                                    <div class="col-sm-3">
                                        <input type="text" name="email" id="email" class="form-control" value="<?php echo $row_alunos[1]['email'] ?>"/>
                                    </div>

                                    <label class="col-sm-2 control-label hidden-print" > Data de Nascimento</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="data_nascimento" id="data_nascimento" class="form-control" value="<?php echo converteData($row_alunos[1]['data_nascimento'], "d/m/Y") ?>"/>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label hidden-print" > RG </label>
                                    <div class="col-sm-3">
                                        <input type="text" name="rg" id="rg" class="form-control" value="<?php echo $row_alunos[1]['rg'] ?>"/>
                                    </div>

                                    <label class="col-sm-1 control-label hidden-print" > CPF </label>
                                    <div class="col-sm-3">
                                        <input type="text" name="cpf" id="cpf" class="form-control" value="<?php echo $row_alunos[1]['cpf'] ?>"/>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label hidden-print"> CEP</label>
                                    <div class="col-sm-3">
                                        <input type="text" name="cep" id="cep" class="form-control" value="<?php echo $row_alunos[1]['cep'] ?>"/>
                                    </div>
                                </div>

                                <div class="form-group" >
                                    <label class="col-sm-2 control-label hidden-print"> Endereço</label>
                                    <div class="col-sm-5">
                                        <input type="text" name="endereco" id="endereco" class="form-control" value="<?php echo $row_alunos[1]['endereco'] ?>"/>
                                    </div>

                                    <label class="col-sm-1 control-label hidden-print">Nº</label>
                                    <div class="col-sm-1">
                                        <input type="text" name="numero" id="numero" class="form-control" value="<?php echo $row_alunos[1]['numero'] ?>"/>
                                    </div>
                                </div>

                                <div class="form-group" >
                                    <label class="col-sm-2 control-label hidden-print">Bairro</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="bairro" id="bairro" class="form-control" value="<?php echo $row_alunos[1]['bairro'] ?>"/>
                                    </div>

                                    <label class="col-sm-1 control-label hidden-print">Cidade</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="cidade" id="cidade" class="form-control" value="<?php echo $row_alunos[1]['cidade'] ?>"/>
                                    </div>

                                    <label class="col-sm-1 control-label hidden-print">UF</label>
                                    <div class="col-sm-1">
                                        <input type="text" name="uf" id="uf" class="form-control" value="<?php echo $row_alunos[1]['uf'] ?>"/>
                                    </div>
                                </div>

                                <div class="form-group" >
                                    <label class="col-sm-2 control-label hidden-print">Complemento</label>
                                    <div class="col-sm-4">
                                        <input type="text" name="complemento" id="complemento" class="form-control" value="<?php echo $row_alunos[1]['complemento'] ?>"/>
                                    </div>
                                </div>
                                
                                <div class="form-group" >
                                    <label class="col-sm-2 control-label hidden-print">Telefone</label>
                                    <div class="col-sm-3">
                                        <input type="text" name="telefone" id="telefone" class="form-control" value="<?php echo $row_alunos[1]['telefone'] ?>"/>
                                    </div>
                                    
                                    <label class="col-sm-1 control-label hidden-print">Celular</label>
                                    <div class="col-sm-3">
                                        <input type="text" name="celular" id="celular" class="form-control" value="<?php echo $row_alunos[1]['celular'] ?>"/>
                                    </div>
                                </div>
                                                             
                                <div class="form-group" >
                                    <label class="col-sm-2 control-label hidden-print">Responsável</label>
                                    <div class="col-sm-3">
                                        <input type="text" name="responsavel" id="responsavel" class="form-control" value="<?php echo $row_alunos[1]['responsavel'] ?>"/>
                                    </div>
                                    
                                    <label class="col-sm-1 control-label hidden-print">Celular</label>
                                    <div class="col-sm-3">
                                        <input type="text" name="celular_responsavel" id="celular_responsavel" class="form-control" value="<?php echo $row_alunos[1]['celular_responsavel'] ?>"/>
                                    </div>
                                </div>
                         
                            </div>
                        </div>
                    
                        <?php if (isset($_POST['id_aluno'])) { ?>
                            <input type="hidden" name="procedimento" value="EDITAR"/>
                        <?php } else { ?>
                            <input type="hidden" name="procedimento" value="CADASTRAR" />
                        <?php } ?>
                    
                        <div class="panel-footer text-right hidden-print controls">
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
        <script src="../../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../../resources/js/tapatar/src/tapatar.js"></script>

        <script type="text/javascript">

                //Início function
                $(function () {
                    
                    
                    
                    //avatar
//                    $('input.tapatar').tapatar({
//                        sources: {
//                          gravatar: {
//                            email: 'foo@bar.com'
//                          },
//                          facebook: {
//                            appId: 1657455811156921
//                          }
//                        }
//                      });
                    
                    //preenchimento automático por cep
                    $( "#cep" ).change(function() {
                        var cep = $("#cep").val();
                        console.log(cep)

                        $.getJSON( "http://api.postmon.com.br/v1/cep/"+cep, function( data ) {
                            $('#endereco').val(data.logradouro);
                            $('#bairro').val(data.bairro);
                            $('#cidade').val(data.cidade);
                            $('#uf').val(data.estado);
                        });
                      });
                                      
                   //máscara para campos
                   $("#rg").mask("99.999.999-9");
                   $("#cpf").mask("999.999.999-99");
                   $("#telefone").mask("(99)9999-9999");
                   $("#celular").mask("(99)99999-9999");
                   $("#celular_responsavel").mask("(99)99999-9999");
                   $("#data_nascimento").mask("99/99/9999");
                 
                 
                
                });
                //fim function
                    
    
        </script>
    </body>
</html>