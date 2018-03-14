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
//include('../../funcoes.php');
//include('../../classes/regiao.php');
include('../../wfunction.php');
include('../../classes/EduEscolasClass.php');
include('../../classes/EduTurmasClass.php');

//$REG = new Regioes();
$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

//RECUPERANDO O ID DA ESCOLA
$id_escola = $_REQUEST['id_escola'];

//LISTA AS TURMAS CADASTRADAS
$turmasClass = new EduTurmasClass();

if(isset($_REQUEST['id_escola'])){
    $row_turmas = $turmasClass->listTurmas($id_escola);
} else {
    header("Location: visualizar_escolas.php");
}

$breadcrumb_config = array("nivel" => "../../", "key_btn" => "52", "area" => "Educacional", "ativo" => "Visualizar de Turmas", "id_form" => "form1");
$breadcrumb_pages = array("Unidade Escolar" => "../unidade_escolar.php", "Visualizar Escolas" => "visualizar_escolas.php");

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

        <title>::Intranet:: Visualizar Turmas</title>
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="page-header box-educacional-header"><h2><span class="fa fa-graduation-cap"></span> - EDUCACIONAL <small> - Visualizar Turmas</small></h2></div>
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">
                        <div class="col-sm-10">Lista Turmas</div>
                        <!--<div class="col-sm-2"><a href="cadastro_turma.php?id_escola= <?php $id_escola ?>" class="btn btn-success btn-sm" title="Cadastrar Turma"><span class="fa fa-plus"></span> Cadastrar Turmas</a></div>-->
                        <!--<div class="col-sm-1"><a href="../unidade_escolar.php" class="btn btn-default btn-sm" title="Voltar"><span class="fa fa-chevron-left"></span> Voltar</a></div>-->
                        <div class="clear"></div>
                    </div>
                        <div class="panel-body">
                            <?php if(count($row_turmas)>0) { ?>
                                
                            <table class="table table-bordered table-condensed text-sm valign-middle table-striped">
                                    <thead>
                                        <tr>
                                            <th class="text-center">#</th>
                                            <th class="text-center">Nome</th>
                                            <th class="text-center">Sala do Aluno </th>
                                            <th class="text-center">Número da Turma</th>
                                            <th class="text-center">Quantidade de Alunos</th>
                                            <th class="text-center">Ações Alunos</th>
                                            <th class="text-center">Ações Turma</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach($row_turmas as $row) {
                                            if ($row['status'] > 0) {
                                            ?>
                                        <tr>
                                            <td><?php echo $row['id_turma'] ?></td>
                                            <!--<td> <a href="cadastro_turma.php?id_turma=<?php echo $row['id_turma'] . "&id_escola=" . $row['id_escola'] ?>"><?php echo $row['turma'] ?> </a></td>-->
                                            <td><a class="btnEdita" href="javascript:;" data-id="<?php echo $row['id_turma'] ?>"><?php echo $row['turma'] ?></a></td>
                                            <td><?php echo $row['sala'] ?></td>
                                            <td><?php echo $row['numero'] ?></td>
                                            <td><?php echo $row['qtd_aluno'] ?></td>
                                            <td class="text-center"> <a href="cadastro_aluno.php?id_turma=<?php echo $row['id_turma'] . "&id_escola=" . $row['id_escola'] ?>" class="btn btn-primary" title="Cadastrar Aluno"><span class="fa fa-plus"></span></a>
                                                                     <a href="visualizar_aluno.php?id_turma=<?php echo $row['id_turma'] ?>" class="btn btn-warning" title="Visualizar Aluno"><span class="fa fa-eye"></span></a>
                                            </td>
                                            <td class="text-center">
                                                <a href="javascript:;" id="<?php echo $row['id_turma']?>" class="btn btn-danger delete" title="Remover Turma"><span class="fa fa-trash"></span></a>
                                            </td>
                                        </tr>
                                    <?php
                                        }
                                    }
                                    ?>
                                    </tbody>
                                <tfoot>
                                    <tr>
                                        <button class="btn btn-primary btn-sm margin_b10 pull-right">Total de Turmas <span class="badge"><?php echo count($row_turmas)?></span></button>
                                    </tr>
                                </tfoot>
                                </table>
                            <?php } else { ?>
                                <div id='message-box' class='alert alert-danger'>
                                    <span class='fa fa-exclamation-triangle'></span> Nenhuma turma cadastrada!
                                </div>
                            <?php } ?>

                        </div>
                    
                    <form action="cadastro_turma.php" method="post" id="editaTurma">
                        <input type="hidden" id="id_turma" name="id_turma" />
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
        <!--<script type="text/javascript" src="../../js/ramon.js"></script>-->
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <!--<script src="../../js/jquery.maskedinput-1.3.1.js"></script>-->
        <!--<script type="text/javascript" src="../../jquery/priceFormat.js" ></script>-->
        
        <script>
            $(function(){
               //edição de turmas 
               $("body").on("click", ".btnEdita", function(){
                 var btnThis = $(this);
                 var btnData = btnThis.data("id");
                 $("#id_turma").val(btnData);
                 $("#editaTurma").submit();
               });
               
             //removendo escola
                $(".delete").click(function () {
                    var deletar = confirm("Deseja Realmente Excluir Essa Turma?");
                    if (deletar == true){
                        var del_id = $(this).prop('id');
                        console.del_id;
                        $.ajax({
                            type: 'POST',
                            url: '../deletes/remover_turma.php?id_turma='+del_id,
//                            data: 'delete_id=' + del_id,
                            success: function () {
                                alert("Turma removida com sucesso!");
                                location.reload();
                            }
                        });
                    }
                });
               
            });
        </script>

       
    </body>
</html>