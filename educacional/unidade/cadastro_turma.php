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
include('../../classes/EduTurmasClass.php');


$REG = new Regioes();
$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)


//LISTANDO TURNOS
$result_turno = mysql_query("SELECT * FROM edu_escola_turno"); //VAI MORRER

//LISTANDO AS ESTRUTURAS DAS TURMAS
$result_estrutura = mysql_query("SELECT * FROM edu_turma_mapa_estrutura ORDER BY estrutura_nome"); //VAI MORRER

$turmasClass = new EduTurmasClass();

$id_escola = $_GET['id_escola'];

//RESGATANDO OS DADOS PARA SEREM VISUALIZADOS ASSIM QUE ABRIR A PÁGINA DE CADASTRO/EDIÇÃO
if(isset($_REQUEST['id_turma'])){
    $id_turma = $_REQUEST['id_turma'];
    $row_turmas = $turmasClass->verTurma($id_turma);
}

//CADASTRO DA ESCOLA
if(isset($_REQUEST['enviar'])){

    //DADOS DA TURMA
    $arrayDados = array(
        'id_escola' => $id_escola,
        'turma' => $_REQUEST['turma'],
        'sala' => $_REQUEST['sala'],
        'sigla' => $_REQUEST['sigla'],
        'numero' => $_REQUEST['numero'],
        'serie' => $_REQUEST['serie'],
        'curso' => $_REQUEST['curso'],
        'qtd_aluno' => $_REQUEST['qtd_aluno']
    );

    //CADASTRO DE DADOS DA TURMA
    if($_REQUEST['procedimento'] == "CADASTRAR"){
        //INSERINDO A TURMA NO BANCO
        $turmasClass->insereTurma($arrayDados);

        header('Location: visualizar_turmas.php?id_escola=' . $_GET['id_escola']);
    }

    //EDIÇÃO DOS DADOS DA ESCOLA
    if($_REQUEST['procedimento'] == "EDITAR"){
        //EDITANDO OS DADOS DA ESCOLA NO BANCO
        $turmasClass->editaTurma($id_escola, $arrayDados);

        header('Location: visualizar_turmas.php?=id_escola' . $_GET['id_escola']);
    }
} //FIM REQUEST['enviar']


//FOREACH PRA TRAZER AS ESTRUTURAS CADASTRADAS
//$dados_estrutura= "";
//$estruturas = $_REQUEST['id_estrutura'];
//$quantidades = $_REQUEST['qtd'];
//
//    foreach ($estruturas as $key => $estrutura){
//        $dados_estrutura[$key]['id_estrutura'] = $estrutura; 
//    }    
//    foreach ($quantidades as $key => $quantidade){
//        $dados_estrutura[$key]['qtd'] = $quantidade; 
//    }    
    
//foreach($dados_estrutura as $key => $dado_estrutura){
//    
//$query_estrutura = ("INSERT INTO turma_estrutura (id_estrutura, id_escola, qtd)
//                    VALUES
//                    ({$dado_estrutura['id_estrutura']}, {$idEscola}, {$dado_estrutura['qtd']})");
//                
//$sql_estrutura = mysql_query($query_estrutura);
//
//}

//} // fim do $_REQUEST['enviar]


$breadcrumb_config = array("nivel" => "../../", "key_btn" => "52", "area" => "Educacional", "ativo" => "Cadastro/Edição de Turmas", "id_form" => "form1");
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

        <title>::Intranet:: Cadastro/Edição de Turmas</title>
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="page-header box-educacional-header"><h2><span class="fa fa-graduation-cap"></span> - EDUCACIONAL <small> - Cadastro/Edição de Turmas</small></h2></div>
            <form action="" method="post" name="form1" id="form1" class="form-horizontal top-margin1" enctype="multipart/form-data" >
                <div class="panel panel-default">
                    <div class="panel-heading text-bold hidden-print">Cadastrar/Editar</div>
                    <div class="panel-body">

                        <input type="hidden" name="id_escola" id="id_escola" <?php $id_escola ?>/>

                        <div class="form-group" >
                            <label class="col-sm-2 control-label hidden-print" > Nome da Turma</label>
                            <div class="col-sm-4">
                                <input type="text" name="turma" id="turma" class="form-control" value="<?php echo $row_turmas[1]['turma'] ?>"/>
                            </div>
                        </div>
                        
                        <div class="form-group" >
                            <label class="col-sm-2 control-label hidden-print" > Sala do Aluno</label>
                            <div class="col-sm-2">
                                <input type="text" name="sala" id="sala" class="form-control" value="<?php echo $row_turmas[1]['sala'] ?>"/>
                            </div>

                            <label class="col-sm-1 control-label hidden-print" > Sigla</label>
                            <div class="col-sm-1">
                                <input type="text" name="sigla" id="sigla" class="form-control" value="<?php echo $row_turmas[1]['sigla'] ?>"/>
                            </div>
                            
                            <label class="col-sm-2 control-label hidden-print" > Número da Turma</label>
                            <div class="col-sm-1">
                                <input type="text" name="numero" id="numero" class="form-control" value="<?php echo $row_turmas[1]['numero'] ?>"/>
                            </div>
                            
                            <label class="col-sm-1 control-label hidden-print" > Série</label>
                            <div class="col-sm-1">
                                <input type="text" name="serie" id="serie" class="form-control" value="<?php echo $row_turmas[1]['serie'] ?>"/>
                            </div>
                        </div>
                        
                        <div class="form-group" >
                            <label class="col-sm-2 control-label hidden-print" > Curso</label>
                            <div class="col-sm-2">
                                <select name="curso" id="curso" class="form-control">
                                    <option value="<?php echo $row_turmas[1]['id_curso'] ?>"><?php echo $row_turmas[1]['curso'] ?></option>
                                </select>
                                <!--<input type="text" name="curso" id="curso" class="form-control" value="<?php echo $row_turmas[1]['curso'] ?>"/>-->
                            </div>
                            
                            <label class="col-sm-2 control-label hidden-print">Quantidade de Aluno</label>
                            <div class="col-sm-2">
                                <input type="text" name="qtd_aluno" id="qtd_aluno" class="form-control" value="<?php echo $row_turmas[1]['qtd_aluno'] ?>"/>
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

                        <div class="form-group" >
                            <label class="col-sm-2 control-label hidden-print">Horário da Turma</label>  
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <div class="input-group-addon">Das</div>
                                    <input type="text" name="horario_entrada" id="horario_entrada" class="form-control" />
                                    <div class="input-group-addon">Até</div>
                                    <input type="text" name="horario_saida" id="horario_saida" class="form-control" />
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
                        if(mysql_num_rows($result_estrutura)) {
                            while($row = mysql_fetch_array($result_estrutura)) {
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
                        }
                        ?>  
                        </div>
                        
                        <?php if(isset($_REQUEST['id_turma'])) { ?>
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
                });
                //fim function
                    
    
        </script>
    </body>
</html>