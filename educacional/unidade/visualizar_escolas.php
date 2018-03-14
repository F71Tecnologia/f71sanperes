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
include('../../wfunction.php');
include('../../classes/EduEscolasClass.php');

//$REG = new Regioes();
$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

//LISTA AS ESCOLAS CADASTRADAS
$escolasClass = new EduEscolasClass();
$row_escolas = $escolasClass->listEscolas();

//INATIVA AS ESCOLAS CADASTRADAS
//$status_escolas = $escolasClass->excluiEscolas();


$breadcrumb_config = array("nivel" => "../../", "key_btn" => "52", "area" => "Educacional", "ativo" => "Visualizar de Escolas", "id_form" => "form1");
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

        <title>::Intranet:: Visualizar de Escolas</title>
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="page-header box-educacional-header"><h2><span class="fa fa-graduation-cap"></span> - EDUCACIONAL <small> - Visualizar de Escolas</small></h2></div>
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">
                        <div class="col-sm-10">Lista Escolas</div>
                        <div class="col-sm-2"><a href="cadastro_escola.php" class="btn btn-success btn-sm" title="Cadastrar Escola"><span class="fa fa-plus"></span> Cadastrar Escola</a></div>    
                        <!--<div class="col-sm-1"><a href="../unidade_escolar.php" class="btn btn-default btn-sm" title="Voltar"><span class="fa fa-chevron-left"></span> Voltar</a></div>-->
                        <div class="clear"></div>
                    </div>
                        <div class="panel-body">
                            <?php if (count($row_escolas)>0) { ?>
                            
                            <table class="table table-bordered table-condensed text-sm valign-middle table-striped">
                                <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">Nome</th>
                                        <th class="text-center">Endereço</th>
                                        <th class="text-center">Localização</th>
                                        <th class="text-center">Profissionais</th>
                                        <th class="text-center">Alunos</th>
                                        <th class="text-center">Turmas</th>
                                        <th class="text-center">Ações Turma</th>
                                        <th class="text-center">Ações Escola</th>
<!--                                        <th class="text-center">Editar Escola</th>
                                        <th class="text-center">Cadastrar turma</th>
                                        <th class="text-center">Editar turma</th>-->
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach($row_escolas as $row) {
                                    if ($row['status'] > 0) {
                                    ?>
                                    
                                    <tr>
                                        <td class="text-center"><?php echo $row['id_escola']  ?></td>
                                        <td><a class="btnEdita" href="javascript:;" data-id="<?php echo $row['id_escola'] ?>"><?php echo $row['escola'] ?></a></td>
                                        <td><?php echo $row['endereco'] ?></td>
                                        <td><?php echo $row['cidade'] ?></td>
                                        <td><?php echo $row['qtd_max_profissional'] ?></td>
                                        <td><?php echo $row['qtd_max_aluno'] ?></td>
                                        <td><?php echo $row['qtd_turma'] ?></td>
                                        <td class="text-center">
                                            <a href="cadastro_turma.php?id_escola=<?php echo $row['id_escola'] ?>" class="btn btn-success" title="Cadastrar Turma"><span class="fa fa-plus"></span></a>
                                            <a href="visualizar_turmas.php?id_escola=<?php echo $row['id_escola'] ?>" class="btn btn-warning" title="Visualizar Turma"><span class="fa fa-eye"></span></a>
                                        </td>
                                        <td class="text-center">
                                            <!--<a href="cadastro_turma.php?id_escola=<?php echo $row['id_escola'] ?>" class="btn btn-success" title="Cadastrar Turma"><span class="fa fa-plus"></span></a>-->
                                            <!--<button type="button" onclick="remove(this)" data-id="<?php echo $row['id_escola']?>" class="btn btn-danger" title="Excluir Escola"><span class="fa fa-trash"></span></button>-->
                                            <a href="javascript:;" id="<?php echo $row['id_escola']?>" class="btn btn-danger delete" title="Remover Escola"><span class="fa fa-trash"></span></a>
                                        </td>
                                    </tr>
                                <?php 
                                    }
                                }?>
                                </tbody>
                                
                                <tfoot>
                                    <tr>
                                        <button class="btn btn-primary btn-sm margin_b10 pull-right">Total de Escolas <span class="badge"><?php echo count($row_escolas); ?></span></button>
                                    </tr>
                                </tfoot>
                            </table>
                            <?php } else { ?>
                                <div id='message-box' class='alert alert-danger'>
                                    <span class='fa fa-exclamation-triangle'></span>  Nenhuma escola cadastrada!
                                </div>
                            <?php } ?>
                            
                        </div>
                    
                    <form action="cadastro_escola.php" method="post" id="editaEscola">
                        <input type="hidden" id="id_escola" name="id_escola" />
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
               $("body").on("click", ".btnEdita", function(){
                  var btnThis = $(this); 
                  var btnData = btnThis.data("id");
                  $("#id_escola").val(btnData);
                  $("#editaEscola").submit();
               });
         
                //removendo escola
                $(".delete").click(function () {
                    var deletar = confirm("Deseja Realmente Excluir Essa Escola?");
                    if (deletar == true){
                        var del_id = $(this).prop('id');
                        console.del_id;
                        $.ajax({
                            type: 'POST',
                            url: '../deletes/remover_escola.php?id_escola='+del_id,
//                            data: 'delete_id=' + del_id,
                            success: function () {
                                alert("Escola removida com sucesso!");
                                location.reload();
                            }
                        });
                    }
                });
               
            });
        </script>
        
    </body>
</html>