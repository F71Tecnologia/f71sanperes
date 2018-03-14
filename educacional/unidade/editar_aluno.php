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


$REG = new Regioes();
$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

//RECUPERANDO O ID DA TURMA
$id_aluno = $_GET['id_aluno'];
$id_user = $_COOKIE['logado'];

//SELCIONANDO O FUNCIONÁRIO
$result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);


//LISTANDO DADOS DOS ALUNOS
$query_alunos = mysql_query("SELECT *
                            FROM escola_aluno AS A
                            LEFT JOIN escolas AS B ON(A.id_escola = B.id_escola)
                            WHERE A.id_turma = $d_turma");

//MOSTRANDO OS DADOS DAS TURMAS
$row_alunos = mysql_fetch_assoc($query_alunos);

if(isset($_REQUEST['atualizar'])){

//DADOS DA TURMA
$matricula_aluno = $_REQUEST['matricula_aluno'];
$nome_aluno = utf8_decode($_REQUEST['nome_aluno']);
$email_aluno = utf8_decode($_REQUEST['email_aluno']);
$data_nascimento = utf8_decode($_REQUEST['data_nascimento']);
$rg_aluno = utf8_decode($_REQUEST['rg_aluno']);
$cpf_aluno = utf8_decode($_REQUEST['cpf_aluno']);
$cep_aluno = utf8_decode($_REQUEST['cep_aluno']);
$endereco_aluno = utf8_decode($_REQUEST['endereco_aluno']);
$numero_endereco_aluno = $_REQUEST['numero_endereco_aluno'];
$complemento_aluno = utf8_decode($_REQUEST['complemento_aluno']);
$bairro_aluno = utf8_decode($_REQUEST['bairro_aluno']);
$cidade_aluno = utf8_decode($_REQUEST['cidade_aluno']);
$uf_aluno = utf8_decode($_REQUEST['uf_aluno']);
$telefone_aluno = utf8_decode($_REQUEST['telefone_aluno']);
$celular_aluno = $_REQUEST['celular_aluno'];
$responsavel_aluno = utf8_decode($_REQUEST['responsavel_aluno']);
$telefone_responsavel = $_REQUEST['telefone_responsavel'];

    
//ATUALIZANDO TURMAS NO BANCO DE DADOS
$update_alunos = mysql_query("UPDATE escola_aluno SET
                            matricula_aluno = '{$matricula_aluno}', nome_aluno = '{$nome_aluno}', email_aluno = '{$email_aluno}',
                            data_nascimento = {$data_nascimento}, rg_aluno = {$rg_aluno}, cpf_aluno = '{$cpf_aluno}', cep_aluno = {$cep_aluno},
                            endereco_aluno = {$endereco_aluno}, numero_endereco_aluno = '{$numero_endereco_aluno}', complemento_aluno = '{$complemento_aluno}',
                            bairro_aluno = '{$bairro_aluno}', cidade_aluno = '{$cidade_aluno}', uf_aluno = '{$uf_aluno}', telefone_aluno = '{$telefone_aluno}',
                            celular_aluno = '{$celular_aluno}', responsavel_aluno = '{$responsavel_aluno}', telefone_responsavel = {$telefone_responsavel} 
                            WHERE id_aluno = $id_aluno ");
                                
                            //REFRESH PARA EDITAR_ALUNO
                            header('Location: editar_aluno.php?id_aluno=' . $id_aluno);

}
// fim do $_REQUEST['atualizar']


$breadcrumb_config = array("nivel" => "../../", "key_btn" => "52", "area" => "Educacional", "ativo" => "Edição dos Dados dos Alunos", "id_form" => "form1");
$breadcrumb_pages = array("Visualizar Turmas" => "visualizar_turmas.php");
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

        <title>::Intranet:: Edição dos Dados dos Alunos</title>
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="page-header box-educacional-header"><h2><span class="fa fa-graduation-cap"></span> - EDUCACIONAL <small> - Edição dos Dados do Aluno</small></h2></div>
            <form action="" method="post" name="form1" id="form1" class="form-horizontal top-margin1"
                  enctype="multipart/form-data" >
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Editar Aluno</div>
                    <div class="panel-body">

                        <input type="hidden" name="id_aluno" id="id_aluno" value="<?php echo $id_aluno ?>"/>

                        <div class="col-sm-1">
                                <div class="form-group" >
    <!--                                <label class="col-sm-2 control-label hidden-print" > Foto</label>-->
                                    <div class="col-sm-4">
                                        <!--<input type="text" name="foto_aluno" id="foto_aluno"/>-->
                                        <img src="http://placehold.it/100x100" class="img-circle"/> 
                                    </div>
                                </div>
                            </div>
                        
                        <div class="col-sm-11">
                                <div class="form-group" >
                                    <label class="col-sm-2 control-label hidden-print" > Matrícula do Aluno</label>
                                    <div class="col-sm-1">
                                        <input type="text" name="matricula_aluno" id="matricula_aluno" class="form-control" value="<?php echo $row_alunos['matricula_aluno'] ?>"/>
                                    </div>
                                </div>

                                <div class="form-group" >
                                    <label class="col-sm-2 control-label hidden-print" > Nome do Aluno</label>
                                    <div class="col-sm-7">
                                        <input type="text" name="nome_aluno" id="nome_aluno" class="form-control" value="<?php $row_aluno['nome_aluno'] ?>"/>
                                    </div>
                                </div>

                                <div class="form-group" >
                                    <label class="col-sm-2 control-label hidden-print" > Email do Aluno</label>
                                    <div class="col-sm-3">
                                        <input type="text" name="email_aluno" id="email_aluno" class="form-control" value="<?php echo $row_alunos['email_aluno'] ?>"/>
                                    </div>

                                    <label class="col-sm-2 control-label hidden-print" > Data de Nascimento</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="data_nascimento" id="data_nascimento" class="form-control" value="<?php echo $row_alunos['data_nascimento'] ?>"/>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label hidden-print" > RG </label>
                                    <div class="col-sm-3">
                                        <input type="text" name="rg_aluno" id="rg_aluno" class="form-control" value="<?php echo $row_alunos['rg_aluno'] ?>"/>
                                    </div>

                                    <label class="col-sm-1 control-label hidden-print" > CPF </label>
                                    <div class="col-sm-3">
                                        <input type="text" name="cpf_aluno" id="cpf_aluno" class="form-control" value="<?php echo $row_alunos['cpf_aluno'] ?>"/>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-2 control-label hidden-print"> CEP</label>
                                    <div class="col-sm-3">
                                        <input type="text" name="cep_aluno" id="cep_aluno" class="form-control" value="<?php echo $row_alunos['cep_aluno'] ?>"/>
                                    </div>
                                </div>

                                <div class="form-group" >
                                    <label class="col-sm-2 control-label hidden-print"> Endereço</label>
                                    <div class="col-sm-5">
                                        <input type="text" name="endereco_aluno" id="endereco_aluno" class="form-control" value="<?php echo $row_alunos['endereco_aluno'] ?>"/>
                                    </div>

                                    <label class="col-sm-1 control-label hidden-print">Nº</label>
                                    <div class="col-sm-1">
                                        <input type="text" name="numero_endereco_aluno" id="numero_endereco_aluno" class="form-control" value="<?php echo $row_alunos['numero_endereco_aluno'] ?>"/>
                                    </div>
                                </div>

                                <div class="form-group" >
                                    <label class="col-sm-2 control-label hidden-print">Bairro</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="bairro_aluno" id="bairro_aluno" class="form-control" value="<?php echo $row_alunos['bairro_aluno'] ?>"/>
                                    </div>

                                    <label class="col-sm-1 control-label hidden-print">Cidade</label>
                                    <div class="col-sm-2">
                                        <input type="text" name="cidade_aluno" id="cidade_aluno" class="form-control" value="<?php echo $row_alunos['cidade_aluno'] ?>"/>
                                    </div>

                                    <label class="col-sm-1 control-label hidden-print">UF</label>
                                    <div class="col-sm-1">
                                        <input type="text" name="uf_aluno" id="uf_aluno" class="form-control" value="<?php echo $row_alunos['uf_aluno'] ?>"/>
                                    </div>
                                </div>

                                <div class="form-group" >
                                    <label class="col-sm-2 control-label hidden-print">Complemento</label>
                                    <div class="col-sm-4">
                                        <input type="text" name="complemento_aluno" id="complemento_aluno" class="form-control" value="<?php echo $row_alunos['complemento_aluno'] ?>"/>
                                    </div>
                                </div>
                                
                                <div class="form-group" >
                                    <label class="col-sm-2 control-label hidden-print">Telefone</label>
                                    <div class="col-sm-4">
                                        <input type="text" name="telefone_aluno" id="telefone_aluno" class="form-control" value="<?php echo $row_alunos['telefone_aluno'] ?>"/>
                                    </div>
                                </div>
                                
                                <div class="form-group" >
                                    <label class="col-sm-2 control-label hidden-print">Celular</label>
                                    <div class="col-sm-4">
                                        <input type="text" name="celular_aluno" id="celular_aluno" class="form-control" value="<?php echo $row_alunos['celular_aluno'] ?>"/>
                                    </div>
                                </div>
                                
                                <div class="form-group" >
                                    <label class="col-sm-2 control-label hidden-print">Responsável</label>
                                    <div class="col-sm-4">
                                        <input type="text" name="responsavel_aluno" id="responsavel_aluno" class="form-control" value="<?php echo $row_alunos['responsavel_aluno'] ?>"/>
                                    </div>
                                </div>
                               
                                <div class="form-group">
                                    <label class="col-sm-2 control-label hidden-print">Telefone do Responsável</label>
                                    <div class="col-sm-4">
                                        <input type="text" name="telefone_responsavel" id="telefone_responsavel" class="form-control" value="<?php echo $row_alunos['telefone_aluno'] ?>"/>
                                    </div>
                                </div>
                            </div>
                            </div>
               

                        <div class="panel-footer text-right hidden-print controls">
                            <button type="submit" name="atualizar" id="atualizar" class="btn btn-success"><span class="fa fa-refresh"></span> Atualizar</button>
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
        <!--<script type="text/javascript" src="../../js/ramon.js"></script>-->
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js"></script>
        <!--<script type="text/javascript" src="../../jquery/priceFormat.js" ></script>-->

        <script type="text/javascript">

                //Início function
                $(function () {
                 
                   
                   //máscara para campos
                   $("#horario_entrada, #horario_saida").mask("99:99");
                  
                
                });
                //fim function
                    
    
        </script>
    </body>
</html>