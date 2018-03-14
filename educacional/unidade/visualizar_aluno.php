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
include('../../classes/EduAlunosClass.php');
//include('../../classes/EduTurmasClass.php');
include "../../classes_permissoes/regioes.class.php";


//$REG = new Regioes();
$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

//RECUPERANDO O ID DA TURMA
$id_turma = $_GET['id_turma'];


//LISTA OS ALUNOS DE ACORDO COM A TURMA
$alunosClass = new EduAlunosClass();

if(isset($_REQUEST['id_turma'])){
    $row_alunos = $alunosClass->listAlunos($id_turma);
} else {
    header("Location: visualizar_escolas.php");
}

//LISTA AS TURMAS CADASTRADAS
//$turmasClass = new EduTurmasClass();
//$result_turmas = $turmasClass->listTurmas($id_escola);

$breadcrumb_config = array("nivel" => "../../", "key_btn" => "52", "area" => "Educacional", "ativo" => "Visualizar Alunos", "id_form" => "form1");
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

        <title>::Intranet:: Visualizar Alunos</title>
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="page-header box-educacional-header"><h2><span class="fa fa-graduation-cap"></span> - EDUCACIONAL <small> - Visualizar Alunos</small></h2></div>
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">
                        <div class="col-sm-10">Lista Alunos</div>
                        <!--<div class="col-sm-2"><a href="cadastro_aluno.php" class="btn btn-success btn-sm" title="Cadastrar Aluno"><span class="fa fa-plus"></span> Cadastrar Aluno</a></div>-->    
                        <!--<div class="col-sm-1"><a href="../unidade_escolar.php" class="btn btn-default btn-sm" title="Voltar"><span class="fa fa-chevron-left"></span> Voltar</a></div>-->
                        <div class="clear"></div>
                    </div>
                        <div class="panel-body">
                            <?php if(count($row_alunos)>0) { ?>
                            <table class="table table-bordered table-condensed text-sm valign-middle">
                                <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">Matrícula</th>
                                        <th class="text-center">Nome</th>
                                        <th class="text-center">Endereço</th>
                                        <th class="text-center">Localização</th>
                                        <!--<th class="text-center" colspan="2">Escola</th>-->
                                        <!--<th class="text-center">Turma</th>-->
                                        <th class="text-center" colspan="2">Ações</th>
    <!--                                        <th class="text-center">Editar Escola</th>
                                        <th class="text-center">Cadastrar turma</th>
                                        <th class="text-center">Editar turma</th>-->
                                    </tr>
                                </thead>
                                <tbody>
                                        <?php foreach ($row_alunos as $row) {
                                            if ($row['status'] > 0) {
                                                ?>
                                                <tr>
                                                    <td class="text-center"><?php echo $row['id_aluno'] ?></td>
                                                    <td class="text-center"><?php echo $row['matricula'] ?></td>
                                                    <td><a class="btnEdita" href="javascript:;" data-id="<?php echo $row['id_aluno'] ?>"><?php echo $row['aluno'] ?></a></td>
                                                    <td class="text-center"><?php echo $row['endereco'] ?></td>
                                                    <td class="text-center"><?php echo $row['cidade'] ?></td>
                                                    <td class="text-center">
                                                        <a href="javascript:;" id="<?php echo $row['id_aluno'] ?>" class="btn btn-danger delete" title="Remover Aluno"><span class="fa fa-trash"></span> </a>
                                                    </td>
                                                </tr>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </tbody>
                                <tfoot>
                                    <tr>
                                        <button class="btn btn-primary btn-sm margin_b10 pull-right">Total de Alunos <span class="badge"><?php echo count($row_alunos)?></span></button>
                                    </tr>
                                </tfoot>
                            </table>
                            <?php } else { ?>
                                <div id='message-box' class='alert alert-danger'>
                                    <span class='fa fa-exclamation-triangle'></span>  Nenhum aluno cadastrado!
                                </div>
                            <?php } ?>
                        </div>
                    
                     <form action="cadastro_aluno.php" method="post" id="editaAluno">
                        <input type="hidden" id="id_aluno" name="id_aluno" />
                        <input type="hidden" id="procedimento" name="procedimento" value="EDITAR"/>
                    </form>

                <div class="clear"></div>
            </div>
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
        
        <script>
            $(function(){
                //edição de alunos
               $("body").on("click", ".btnEdita", function(){
                 var btnThis = $(this);
                 var btnData = btnThis.data("id");
                 $("#id_aluno").val(btnData);
                 $("#editaAluno").submit();
               });
               
               //removendo aluno
               $(".delete").click(function () {
                   var deletar = confirm("Deseja realmente excluir esse aluno?");
                   if (deletar == true){
                       var del_id = $(this).prop('id');
                       console.del_id;
                       $.ajax({
                           type: 'POST',
                           url: '../deletes/remover_aluno.php?id_aluno='+del_id,
//                           data: 'delete_id=' + del_id,
                            success: function () {
                                alert("Aluno removido com sucesso!");
                                location.reload();
                            }
                       });
                   }
               });
            });
        </script>
        
    </body>
</html>