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
include('../../classes_permissoes/regioes.class.php');


$REG = new Regioes();
$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

//RECUPERANDO O ID DA TURMA
$id_turma = $_GET['id_turma'];
$id_user = $_COOKIE['logado'];

//SELCIONANDO O FUNCIONÁRIO
$result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);


//LISTANDO DADOS DAS TURMAS
$query_turmas = mysql_query("SELECT * 
                            FROM escolas_turmas AS A
                            LEFT JOIN turma_estrutura AS B ON(A.id_escola = B.id_escola) 
                            LEFT JOIN turma_mapa_estrutura AS C ON(B.id_estrutura = C.id_estrutura)
                            WHERE A.id_turma = $id_turma");

//MOSTRANDO OS DADOS DAS TURMAS
$row_turmas = mysql_fetch_assoc($query_turmas);

if(isset($_REQUEST['atualizar'])){

//DADOS DA TURMA
$nome_turma = utf8_decode($_REQUEST['nome_turma']);
$sala_aluno = utf8_decode($_REQUEST['sala_aluno']);
$sigla = $_REQUEST['sigla'];
$numero_turma = ($_REQUEST['numero_turma']);
$serie_turma = $_REQUEST['serie_turma'];
$curso_turma = utf8_decode($_REQUEST['curso_turma']);
$qtd_aluno = $_REQUEST['qtd_aluno'];
$turno_escola = $_REQUEST['turno_escola'];
$horario_entrada = ($_REQUEST['horario_entrada']);
$horario_saida = ($_REQUEST['horario_saida']);

//FOREACH PRA TRAZER AS ESTRUTURAS CADASTRADAS
$dados_estrutura= "";
$estruturas = $_REQUEST['id_estrutura'];
$quantidades = $_REQUEST['qtd'];

    foreach ($estruturas as $key => $estrutura){
        $dados_estrutura[$key]['id_estrutura'] = $estrutura; 
    }    
    foreach ($quantidades as $key => $quantidade){
        $dados_estrutura[$key]['qtd'] = $quantidade; 
    }    

    
//ATUALIZANDO TURMAS NO BANCO DE DADOS
$update_turmas = mysql_query("UPDATE escolas_turmas SET
                                nome_turma = '{$nome_turma}', sala_aluno = '{$sala_aluno}', sigla = '{$sigla}',
                                numero_turma = {$numero_turma}, serie_turma = {$serie_turma}, curso_turma = '{$curso_turma}', qtd_aluno = {$qtd_aluno},
                                turno_escola = {$turno_escola}, horario_entrada = '{$horario_entrada}', horario_saida = '{$horario_saida}'
                                WHERE id_turma = $id_turma ");
                                
                                //REFRESH PARA EDITAR_TURMA
                                header('Location: editar_turma.php?id_turma=' . $id_turma);


foreach($dados_estrutura as $key => $dado_estrutura){
    
//ATUALIZANDO AS ESTRUTURAS NO BANCO DE DADOS    
$query_estrutura = mysql_query("UPDATE turma_estrutura SET 
                   id_estrutura = {$dado_estrutura['id_estrutura']}, id_escola = $id_escola}, qtd = {$dado_estrutura['qtd']}");

}
// fim do foraech


}
// fim do $_REQUEST['atualizar']


$breadcrumb_config = array("nivel" => "../../", "key_btn" => "52", "area" => "Educacional", "ativo" => "Edição dos Dados da Turmas", "id_form" => "form1");
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

        <title>::Intranet:: Edição dos Dados das Turmas</title>
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="page-header box-educacional-header"><h2><span class="fa fa-graduation-cap"></span> - EDUCACIONAL <small> - Edição de Turmas</small></h2></div>
            <form action="" method="post" name="form1" id="form1" class="form-horizontal top-margin1"
                  enctype="multipart/form-data" >
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Editar Turma</div>
                    <div class="panel-body">

                        <input type="hidden" name="id_turma" id="id_escola" value="<?php echo $id_turma ?>"/>

                        <div class="form-group" >
                            <label class="col-sm-2 control-label hidden-print" > Nome da Turma</label>
                            <div class="col-sm-4">
                                <input type="text" name="nome_turma" id="nome_turma" class="form-control" value="<?php echo $row_turmas['nome_turma'] ?>"/>
                            </div>
                        </div>
                        
                        <div class="form-group" >
                            <label class="col-sm-2 control-label hidden-print" > Sala do Aluno</label>
                            <div class="col-sm-2">
                                <input type="text" name="sala_aluno" id="sala_aluno" class="form-control" value="<?php echo $row_turmas['sala_aluno'] ?>"/>
                            </div>

                            <label class="col-sm-1 control-label hidden-print" > Sigla</label>
                            <div class="col-sm-1">
                                <input type="text" name="sigla" id="sigla" class="form-control" value="<?php echo $row_turmas['sigla'] ?>" />
                            </div>
                            
                            <label class="col-sm-2 control-label hidden-print" > Número da Turma</label>
                            <div class="col-sm-1">
                                <input type="text" name="numero_turma" id="numero_turma" class="form-control" value="<?php echo $row_turmas['numero_turma'] ?>" />
                            </div>
                            
                            <label class="col-sm-1 control-label hidden-print" > Série</label>
                            <div class="col-sm-1">
                                <input type="text" name="serie_turma" id="serie_turma" class="form-control" value="<?php echo $row_turmas['serie_turma'] ?>" />
                            </div>
                        </div>
                        
                        <div class="form-group" >
                            <label class="col-sm-2 control-label hidden-print" > Curso</label>
                            <div class="col-sm-2">
                                <input type="text" name="curso_turma" id="curso_turma" class="form-control" value="<?php echo $row_turmas['curso_turma'] ?>" />
                            </div>
                            
                            <label class="col-sm-2 control-label hidden-print">Quantidade de Aluno</label>
                            <div class="col-sm-2">
                                <input type="text" name="qtd_aluno" id="qtd_aluno" class="form-control" value="<?php echo $row_turmas['qtd_aluno'] ?>" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Turno</label>
                           <?php //while ($row = mysql_fetch_array($result_turno)) { ?>
                                <div class="col-sm-2">
                                    <div class="input-group">
                                        <label class="input-group-addon">
                                            <input type="radio" name="turno_escola" class="turno_escola" value="1" <?php echo ($row_turmas['turno_escola'] == 1) ? "checked" : "" ?>/>
                                        </label>
                                        <label type="text" class="form-control">Manhã</label>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="input-group">
                                        <label class="input-group-addon">
                                            <input type="radio" name="turno_escola" class="turno_escola" value="2" <?php echo ($row_turmas['turno_escola'] == 2) ? "checked" : "" ?> />
                                        </label>
                                        <label type="text" class="form-control">Tarde</label>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="input-group">
                                        <label class="input-group-addon">
                                            <input type="radio" name="turno_escola" class="turno_escola" value="3" <?php echo ($row_turmas['turno_escola'] == 3) ? "checked" : "" ?>/>
                                        </label>
                                        <label type="text" class="form-control">Noite</label>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="input-group">
                                        <label class="input-group-addon">
                                            <input type="radio" name="turno_escola" class="turno_escola" value="4" <?php echo ($row_turmas['turno_escola'] == 4) ? "checked" : "" ?>/>
                                        </label>
                                        <label type="text" class="form-control">Integral</label>
                                    </div>
                                </div>
                            <?php
//                            }
                            ?>
                        
                        </div>

                        <div class="form-group" >
                            <label class="col-sm-2 control-label hidden-print">Horário da Turma</label>  
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <div class="input-group-addon">Das</div>
                                    <input type="text" name="horario_entrada" id="horario_entrada" class="form-control" value="<?php echo $row_turmas['horario_entrada'] ?>" />
                                    <div class="input-group-addon">Até</div>
                                    <input type="text" name="horario_saida" id="horario_saida" class="form-control" value="<?php echo $row_turmas['horario_saida'] ?>" />
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="panel panel-default text-bold"><div class="panel-heading">Estrutura</div></div>
                        <div class="panel-footer text-right">    
                            <a class="btn btn-success" id="quantidade_estrutura"><span class="fa fa-plus"></span> Quantidade</a>
                            <!--<input type="checkbox" id="entrada" name="entrada" />-->
                        </div>
                    
                    <div class="col-sm-12 margin_t10 quantidade_estrutura" style="display: none;">
                        <?php
//                        if(mysql_num_rows($result_estrutura)) {
                            while($row = mysql_fetch_array($query_turmas)) {
                        ?>
                            <div class="col-sm-4">
                                <table class="table table-condensed table-bordered table-responsive text-sm valign-middle">
                                    <thead>
                                        <tr>
                                            <th>Estrutura</th>
                                            <th>Quantidade</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <input type="hidden" name="id_estrutura[]" id="id_estrutura" value="<?php echo $row['id_estrutura'] ?>" />
                                            <td><?php echo $row['estrutura_nome'] ?></td>
                                            <td class="col-sm-2"><input type="text" name="qtd[]" id="qtd" value="<?php echo $row['qtd'] ?>" class="form-control"/></td>
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
                    //botão estrututra+
                    $("#quantidade_estrutura").click(function() {
                        $('.quantidade_estrutura').toggle('');
                            $(this).find('span').toggleClass('fa-minus fa-plus');
                   });
                   
                   //máscara para campos
                   $("#horario_entrada, #horario_saida").mask("99:99");
                   
                   //confirmação ou erro de cadastro
                    $("#form1").submit(function () {
                    if ($("button[type='submit']").val() == "ATUALIZAR") {
                        alert("Dados alterados com sucesso!");
                    } 
                    else {
                        alert("Dados não alterados!");
                    }
                    
                    });
                
                });
                //fim function
                    
    
        </script>
    </body>
</html>