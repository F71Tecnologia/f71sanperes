<?php
//if (!isset($_COOKIE['logado'])) {
//    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
//    exit;
//}
include ("../include/restricoes.php");
include("../../conn.php");
include("../../classes/global.php");
include("../../wfunction.php");
include('../../funcoes.php');
include('../../classes_permissoes/regioes.class.php');

$usuario = carregaUsuario();
$id_regiao = $usuario['id_regiao'];

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$projeto1 = montaSelect(array("-1" => "« Selecione a Região »"), $projetoR, "id='projeto1' name='projeto' class='form-control validate[required,custom[select]]'");
$global = new GlobalClass();

$breadcrumb_config = array("nivel" => "../", "key_btn" => "24", "area" => "JURÍDICO", "ativo" => "Consultar Processos", "id_form" => "consulta_processo");

$regiao = mysql_real_escape_string($_GET['regiao']);
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet - Listagem dos CLTs::</title>

        <link rel="shortcut icon" href="../favicon.png">

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-compras.css" rel="stylesheet" type="text/css">

        <!-- Jquery-->
        <script src="../../jquery/jquery-1.4.2.min.js" type="text/javascript"></script>
        <link href="../../jquery/autocomplete/autocomplete.css" rel="stylesheet" type="text/css">
        <script src="../../jquery/autocomplete/autocomplete.js" type="text/javascript"></script>
        <script src="../../jquery/autocomplete/dimensions.js" type="text/javascript"></script>

        <script type="text/javascript">
            $(function () {
                /* setAutoComplete("searchField", "results", "action.autocomplete.php?part=");*/
                $('#pesquisa').keypress(function () {
                    if (event.which == 13) {

                        var valor = $(this).val();
                        if (valor != '' || valor != ' ') {
                            $("#resultado").show();
                            $('#resultado').html('Aguarde<br><img src="../../imagens/1-carregando.gif"/>');
                            $.ajax({
                                url: 'action.pesquisa_trabalhador.php?pesquisa=' + valor,
                                type: 'GET',
                                dataType: 'html',
                                success: function (resposta) {
                                    $("#resultado").html('');
                                    $("#resultado").html(resposta);
                                }
                            });
                        }
                    }
                });

                $('#regiao').change(function () {
                    var regiao = $(this).val();
                    $.ajax({
                        url: '../action.preenche_select.php?regiao=' + regiao,
                        type: "GET",
                        success: function (resposta) {
                            $('#projeto').html(resposta);
                        }
                    });
                });


                $("#clt").click(function () {
                    if ($(this).is(":checked")) {
                        $("#resultado").css({width: "0px", height: "0px", display: "none"});
                    } else {
                        $("#resultado").css({display: "block", width: "600px", height: "600px"});
                    }
                });

            });


        </script>
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            
            <div class="page-header box-financeiro-header"><h2><span class="glyphicon glyphicon-usd"></span> - Financeiro<small> - CATEGORIA DO TRABALHADOR</small></h2></div>  
            
            <div class="panel panel-default">
                <div class="panel-heading"> CATEGORIA DO TRABALHADOR</div>
                    <div id="corpo"><br>


                                                    <form name="form" action="listagem_trab.php" method="post" class="form-horizontal">  
                                                        
                                                        
                                                            <div class="form-group">
                                                                <label class="col-sm-2 control-label">Região:</label>
                                                                <div class="col-sm-4">
                                                                    <select name="regiao" id="regiao" class="form-control">
                                                                        <option value="">Selecione a região...</option>
                                                                        <?php
                                                                        $REGIOES = new Regioes();
                                                                        $REGIOES->Preenhe_select_sem_master_prestador_servico();
                                                                        ?>
                                                                    </select> 
                                                                </div>
                                                                
                                                            </div>
                                                                                                           
                                                           
                                                            <div class="form-group">
                                                                <label class="col-sm-2 control-label">Projeto: </label>
                                                                <div class="col-sm-4">
                                                                    <select name="projeto" id="projeto" class="form-control">
                                                                        <option value="">Selecione o projeto</option>
                                                                        <?php ?>
                                                                    </select>
                                                                    <br>
                                                                </div>
                                                                
                                                            </div>
                                                       
                                                    
                                                        <div class="form-group">
                                                           <label class="col-sm-2 control-label"> Pesquisar por nome:</label>
                                                           <div class="col-sm-4">
                                                           <input name="pesquisa" type="text" id="pesquisa" size="50" class="form-control"/>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="panel-footer">
                                                            <div class="text-right">
                                                                <input name="enviar" type="submit" class="btn btn-primary" value=" OK "/>
                                                            </div>
                                                        </div>
                                                        
                                                    </form>

                                                    
                                                       
                                                    
                                                    


                                        <table width="100%" align="center">
                                            <tr>
                                                <td>
                                                    <div id="resultado" style="font-size:12px;text-align:center; display: none">
                                                        <form name="cadProcesso" id="cadProcesso" method="POST" action="" >
                                                            <fieldset>
                                                                <legend style="margin-left: 10px; text-align: left; text-transform: uppercase; font-size: 14px;" >Cadastro de Processos para não funcionário</legend>
                                                                <p class="alignField"><label for="codigo">Código:</label><input type="text" name="codigo" id="codigo" value="" class="input_juridico_02" /></p>
                                                                <p class="alignField"><label for="nome">Nome:</label><input type="text" name="nome" id="nome" value="" class="input_juridico_01"/></p>
                                                                <p class="alignField"><label for="nascimento">Data Nascimento:</label><input type="text" name="nascimento" id="nascimento" value="" class="input_juridico_02" /></p>

                                                                <div class="boxColuna">
                                                                    <div class="boxEsc">
                                                                        <p class="alignField"><label for="rg">RG:</label><input type="text" name="rg" id="rg" value="" class="input_juridico_02" /></p>
                                                                    </div>
                                                                    <div class="boxDir">
                                                                        <p class="alignField"><label for="cpf">CPF:</label><input type="text" name="cpf" id="cpf" value="" class="input_juridico_02" /></p>
                                                                    </div>
                                                                </div><br/><br/><br/>

                                                                <p class="alignField"><label for="atividade">Atividade:</label><input type="text" name="atividade" id="atividade" value=""  class="input_juridico_01"/></p>

                                                                <div class="boxColuna">
                                                                    <div class="boxEsc">
                                                                        <p class="alignField"><label for="data_entrada">Data Entrada:</label><input type="text" name="data_entrada" id="data_entrada" value="" class="input_juridico_02"/></p>
                                                                    </div>
                                                                    <div class="boxEsc">
                                                                        <p class="alignField"><label for="data_saida">Data Saida:</label><input type="text" name="data_saida" id="data_saida" value="" class="input_juridico_02"/></p>
                                                                    </div>    
                                                                </div><br/><br/><br/>

                                                                <p class="alignField"><label for="regiao">Região:</label><input type="text" name="regiao" id="regiao" value="" class="input_juridico_02"/></p>
                                                                <p class="alignField"><label for="projeto">Projeto:</label><input type="text" name="projeto" id="projeto" value="" class="input_juridico_02"/></p>
                                                                <p class="alignField"><label for="unidade">Unidade:</label><input type="text" name="unidade" id="unidade" value="" class="input_juridico_02"/></p>
                                                                <p class="alignField"><label for="data_cadastro_processo">Data de cadastro do processo:</label><input type="text" name="data_cadastro_processo" id="data_cadastro_processo" value="" class="input_juridico_02"/></p>
                                                                <p class="alignField"><label for="pedido_acao">Pedidos de Ação:</label><textarea name="pedido_acao" id="pedido_acao"></textarea></p>
                                                                <p class="alignField"><label for="vara">Vara:</label><input type="text" name="vara" id="vara" value="" class="input_juridico_02"/></p>
                                                                <p class="alignField"><label for="valor_pedido">Valor Pedido:</label><input type="text" name="valor_pedido" id="valor_pedido" value="" class="input_juridico_02"/></p>
                                                                <p class="alignField"><label for="n_da_vara">Nº da Vara:</label><input type="text" name="numero_vara" id="numero_vara" value="" class="input_juridico_02"/></p>

                                                                <p><input type="submit" name="cadastrar" id="cadastrar"  value="Cadastrar"  /></p>
                                                            </fieldset>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>


                                        </table>     
                                        </form>

                                    </div>
                                </div>
                    <?php include_once '../../template/footer.php'; ?>
                </div>
     
        
        </div>
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery.form.js"></script>
        <script src="../../js/jquery.maskMoney_3.0.2.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../resources/js/bootstrap-filestyle.min.js" type="text/javascript"></script>
        <script src="../../js/jquery.form.js" type="text/javascript"></script>


    </body>
</html>

