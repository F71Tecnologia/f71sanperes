<?php
session_start();

if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../wfunction.php');
include('../../classes/global.php');
include('../../classes/FuncoesClass.php');

$usuario = carregaUsuario();
$master = $usuario['id_master'];
$id_regiao = $usuario['id_regiao'];
$id_usuario = $_COOKIE['logado'];

$regiao_selecionada = $_REQUEST['hide_regiao'];
$projeto_selecionado = $_REQUEST['hide_projeto'];

$curso_geral = FuncoesClass::cadastraFuncao($usuario, $regiao_selecionada, $id_usuario);
$curso = FuncoesClass::getCursos($regiao_selecionada, $projeto_selecionado);

$sql_departamento = "SELECT * FROM departamentos ORDER BY nome";
$sql_departamento = mysql_query($sql_departamento);
$arrayDepartamentos[0] = 'Selecione';
while($row_departamento = mysql_fetch_assoc($sql_departamento)){
    $arrayDepartamentos[$row_departamento['id_departamento']] = $row_departamento['nome'];
}

$_SESSION['regiao_select'] = $regiao_selecionada;
$_SESSION['projeto_select'] = $projeto_selecionado;
session_write_close();

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form1", "ativo"=>"Cadastro de Funções");
$breadcrumb_pages = array(/*"Gestão de RH"=>"../../rh", */"Gestão de Funções"=>"index2.php");
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>:: Intranet :: Cadastro de Funções</title>
        <link href="../../favicon.png" rel="shortcut icon" />

        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../css/progress.css" rel="stylesheet" type="text/css">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <!--link href="../../resources/css/bootstrap-rh.css" rel="stylesheet" type="text/css"-->
        
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="jquery.autocomplete.css" rel="stylesheet" type="text/css" />  
        <style>
            .cooperado, #p_valor_hora{
                display: none;
            }
            .some_insa, #hide_noturno, #del_hor {
                display: none;
            }
        </style>
    </head>
    <body>
    <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS <small>Cadastro de Funções</small></h2></div>
                </div>
            </div>
            <div class="row">
                <form action="" class="form-horizontal" method="post" name="form1" id="form1" autocomplete="off">
                    <div class="col-lg-12 form_funcoes">
                        <div class="panel panel-default">
                            <div class="panel-heading">Dados da Função</div>
                            <div class="panel-body">
                                <?php if(isset($_SESSION['regiao'])){
                                    $regiao_selecionada = $_SESSION['regiao'];
                                } ?>
                                <input type="hidden" name="regiao_selecionada" id="regiao_selecionada" value="<?php echo $regiao_selecionada; ?>" />
                                <input type="hidden" name="regiao_logado" id="regiao_logado" value="<?php echo $id_regiao; ?>" />

                                <?php if(!empty($_SESSION['MESSAGE'])){ ?>
                                <!--resposta de algum metodo realizado-->
                                <div id="message-box" class="alert alert-dismissable alert-warning <?php echo $_SESSION['MESSAGE_COLOR']; ?> alinha2">
                                    <?php echo $_SESSION['MESSAGE']; session_destroy(); ?>
                                </div>
                                <?php } ?>

                                <fieldset id="func1">
                                    <div class="form-group">
                                        <label for="nome" class="col-lg-2 control-label">Tipo de Contratação:</label>
                                        <div class="col-lg-10">
                                            <select name="contratacao" id="contratacao" class="form-control validate[required] form-co">
                                                <option class="btn_cont1" value="1">Autônomo</option>
                                                <option class="btn_cont2" value="2" selected="selected">CLT</option>
                                                <option class="btn_cont3" value="3">Cooperado / Terceirizado</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="nome" class="col-lg-2 control-label">Projeto:</label>
                                        <div class="col-lg-10">
                                            <?php echo montaSelect(getProjetos($regiao_selecionada),null, "id='projeto' name='projeto' class='form-control validate[required,custom[select]]'"); ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="nome" class="col-lg-2 control-label">Nome da Função:</label>
                                        <div class="col-lg-10">
                                            <input type="text" name="nome_funcao" id="nome_funcao" size="93" class="form-control validate[required]" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="nome" class="col-lg-2 control-label">Área:</label>
                                        <div class="col-lg-10">
                                            <input type="text" name="area" id="area" size="93" class="form-control validate[required]" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="nome" class="col-lg-2 control-label">Departamento:</label>
                                        <div class="col-lg-10">
                                            <?php echo montaSelect($arrayDepartamentos, $value, 'name="departamento" id="departamento" class="form-control validate[required,custom[select]] departamento"'); ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="nome" class="col-lg-2 control-label">Nome do CBO:</label>
                                        <div class="col-lg-10">
                                            <input type="text" name="cbo" id="cbo" size="70" class="form-control validate[required]" placeholder="Ex: Assistente administrativo  - 4110.10" />
                                            <span id="selection"></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="nome" class="col-lg-2 control-label">Local:</label>
                                        <div class="col-lg-10">
                                            <input type="text" name="local" id="local" size="93" class="form-control validate[required]" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="nome" class="col-lg-2 control-label">Início:</label>
                                        <div class="col-lg-4">
                                            <input type="text" name="data_ini" id="data_ini" size="16" class="form-control validate[required,custom[dateBr]]" />
                                        </div>
                                        <label for="nome" class="col-lg-2 control-label">Final:</label>
                                        <div class="col-lg-4">
                                            <input type="text" name="data_fim" id="data_fim" size="16" class="form-control validate[required,custom[dateBr]]" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="nome" class="col-lg-2 control-label">Salário:</label>
                                        <div class="col-lg-4">
                                            <input type="text" name="salario" id="salario" size="30" class="form-control validate[required]" />
                                        </div>
                                        <label for="nome" class="col-lg-2 control-label">Mês Abono:</label>
                                        <div class="col-lg-4">
                                            <?php echo montaSelect(mesesArray(),null,"id='mes_abono' name='mes_abono' class='form-control'"); ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="nome" class="col-lg-2 control-label cooperado vali">Parcelas:</label>
                                        <div class="col-lg-4 cooperado vali">
                                            <input type="text" name="parcelas" id="parcelas" size="30" maxlength="4" class="form-control" />
                                        </div>
                                        <label for="nome" class="col-lg-2 control-label cooperado">Quota:</label>
                                        <div class="col-lg-4 cooperado">
                                            <input type="text" name="quota" id="quota" size="30" class="form-control" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="nome" class="col-lg-2 control-label cooperado vali">Parcela das Quotas:</label>
                                        <div class="col-lg-4 cooperado vali">
                                            <input type="text" name="parcela_quotas" id="parcela_quotas" size="30" maxlength="4" class="form-control" />
                                        </div>
                                        <label for="nome" class="col-lg-2 control-label">Qtd. Máxima de Contratação:</label>
                                        <div class="col-lg-4">
                                            <input type="text" name="qtd_contratacao" id="qtd_contratacao" size="30" maxlength="4" class="form-control validate[required,custom[onlyNumberSp]]" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="nome" class="col-lg-2 control-label">Horas:</label>
                                        <div class="col-lg-4">
                                            <input type="text" name="horas" id="horas" size="30" maxlength="4" class="form-control" />
                                        </div>
                                        <label for="nome" id="p_valor_hora" class="col-lg-2 control-label">Valor Hora:</label>
                                        <div class="col-lg-4" id="p_valor_hora">
                                            <input type="text" name="valor_hora_cooperado" id="valor_hora" class="form-control money" />
                                        </div>
                                    </div>
                                    <div class="form-group" id="peric_insal">
                                        <label for="nome" class="col-lg-2 control-label"></label>
                                        <div class="col-lg-2">
                                            <input type="radio" name="periculosidade" value="" id="insal" /> Insalubridade
                                        </div>
                                        <div class="col-lg-2">
                                            <input type="radio" name="periculosidade" value="1" id="peric" /> Periculosidade 30%
                                        </div>
                                    </div>
                                    <div class="form-group some_insa">
                                        <label for="nome" class="col-lg-2 control-label">Insalubridade:</label>
                                        <div class="col-lg-10">
                                            <select name="insalubridade" id="insalubridade" class="form-control">
                                                <option value="-1">« Selecione »</option>
                                                <option value="1">Insalubridade 20%</option>
                                                <option value="2">Insalubridade 40%</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group some_insa">
                                        <label for="nome" class="col-lg-2 control-label">Quantidade de Salários:</label>
                                        <div class="col-lg-10">
                                            <input type="text" name="qtd_salarios" id="qtd_salarios" class="form-control" maxlength="4" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="nome" class="col-lg-2 control-label">Descrição:</label>
                                        <div class="col-lg-10">
                                            <textarea name="descricao" id="descricao" class="form-control"><?php echo $prestador['endereco']?></textarea>
                                        </div>
                                    </div>
                                </fieldset>
                            </div>
                            <div class="panel-body">
                                <fieldset class="horario" data-position="0">
                                    <legend>Dados do Horário</legend>
                                    <div class="form-group">
                                        <div class="">
                                            <label class="col-lg-1 col-lg-offset-11 control-label pull-right pointer del_hor" id="del_hor"><img src="../../imagens/icones/icon-delete.gif" title="Deletar horário" /></label>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="nome" class="col-lg-2 control-label">Nome do Horário:</label>
                                        <div class="col-lg-10">
                                            <input type="text" name="nome_horario[]" id="nome_horario" class="form-control validate[required] limpa nome_horario" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="nome" class="col-lg-2 control-label">Observações:</label>
                                        <div class="col-lg-10">
                                            <input type="text" name="obs[]" id="obs" class="form-control limpa obs" />
                                        </div>
                                    </div>
                                    <div class="form-group remove">
                                        <label for="nome" class="col-lg-2 control-label">Preenchimento:</label>
                                        <div class="col-lg-2">
                                            Entrada <input type="text" name="entrada[]" id="entrada_0" class="form-control preenchimento validate[required] limpa entrada" data-ordem="0" />
                                        </div>
                                        <div class="col-lg-2">
                                            Saída Almoço <input type="text" name="ida_almoco[]" id="ida_almoco_0" class="form-control preenchimento validate[required] limpa ida_almoco" />
                                        </div>
                                        <div class="col-lg-2">
                                            Retorno Almoço <input type="text" name="volta_almoco[]" id="volta_almoco_0" class="form-control preenchimento validate[required] limpa volta_almoco" />
                                        </div>
                                        <div class="col-lg-2">
                                            Saída <input type="text" name="saida[]" id="saida_0" class="form-control preenchimento validate[required] limpa saida" />
                                        </div>
                                    </div>
                                    <div class="form-group esquerda" id="esquerda">
                                        <label for="nome" class="col-lg-2 control-label">Horas Mês:</label>
                                        <div class="col-lg-4">
                                            <input type="text" name="horas_mes[]" id="horas_mes" size="30" maxlength="4" class="form-control validate[required,custom[onlyNumberSp]] limpa horas_mes" />
                                        </div>
                                        <label for="nome" class="col-lg-2 control-label">Dias Mês:</label>
                                        <div class="col-lg-4">
                                            <input type="text" name="dias_mes[]" id="dias_mes" size="30" maxlength="4" class="form-control validate[required,custom[onlyNumberSp]] limpa dias_mes" />
                                        </div>
                                    </div>
                                    <div class="form-group direita" id="direita">
                                        <label for="nome" class="col-lg-2 control-label">Horas Semanais:</label>
                                        <div class="col-lg-4">
                                            <input type="text" name="horas_semana[]" id="horas_semana" size="30" maxlength="4" class="form-control validate[required,custom[onlyNumberSp]] limpa horas_semana" />
                                        </div>
                                        <label for="nome" class="col-lg-2 control-label">Dias Semana:</label>
                                        <div class="col-lg-4">
                                            <input type="text" name="dias_semana[]" id="dias_semana" size="30" maxlength="4" class="form-control validate[required,custom[onlyNumberSp]] limpa dias_semana" />
                                        </div>
                                    </div>
                                    <div class="form-group direita" id="direita">
                                        <label for="nome" class="col-lg-2 control-label">Folgas:</label>
                                        <div class="col-lg-2">
                                            <div class="checkbox">
                                                <label><input class="check" type="checkbox" name="folga[][0]" value="1" /> Sábado</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <div class="checkbox">
                                                <label><input class="check" type="checkbox" name="folga[][1]" value="2" /> Domingo</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <div class="checkbox">
                                                <label><input class="check" type="checkbox" name="folga[][2]" value="5" /> Plantonista</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="nome" class="col-lg-2 control-label">Adicional Noturno:</label>
                                        <div class="col-lg-2">
                                            <div class="radio">
                                                <label><input type="radio" name="noturno[]" value="1" id="n_sim" class="n_sim check_not" /> Sim</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <div class="radio">
                                                <label><input type="radio" name="noturno[]" value="0" id="n_nao" class="n_nao check_not" /> Não</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group" id="hide_noturno">
                                        <label for="nome" class="col-lg-2 control-label">Horas Noturno:</label>
                                        <div class="col-lg-10">
                                            <input type="text" name="horas_noturno[]" id="horas_noturno" maxlength="4" class="form-control horas_noturno" />
                                        </div>
                                    </div>
                                </fieldset>
                                <label class="col-lg-3 col-lg-offset-9 control-label pointer add_hor" id="add_hor">Adicionar outro Horário <img src="../../imagens/icones/icon-add.png" title="Adicionar outro horário" /></label>
                            </div>
                            <div class="panel-footer text-right">
                                <input type="submit" class="btn btn-primary" name="cadastrar" id="cadastrar" value="Cadastrar" />
                                <!--input type="button" name="voltar" id="voltar" value="Voltar" onclick="window.location = 'index.php';" /-->
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        <?php include_once '../../template/footer.php'; ?>
        </div><!-- /.content -->
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        
        <script src="../../js/jquery.price_format.2.0.min.js" type="text/javascript"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js" type="text/javascript"></script>
        <script src="../../js/jquery.maskMoney.js" type="text/javascript" ></script>
        <!--script src="../../js/jquery.validationEngine.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt.js" type="text/javascript"></script-->
        <script src="../../js/jquery.autocomplete.js" type="text/javascript"></script>
        <script>
            $(function(){
                //mascara
                $("#data_ini").mask("99/99/9999");
                $("#data_fim").mask("99/99/9999");
                $("#salario, #valor, #quota, #salario_novo").maskMoney({prefix:'R$ ', allowNegative: true, thousands:'.', decimal:','});
                $(".entrada, .ida_almoco, .volta_almoco, .saida").mask("99:99:99");
                
                //autocomplete
                $("#cbo").autocomplete("lista_cbo.php", {
                    width: 600,
                    matchContains: false,      
                    minChars: 3,
                    selectFirst: false                    
                });
                
                //validation engine
                $("#form1").validationEngine({promptPosition : "topRight"});
                
                //oculta/exibe dados do CLT
                window.func2 = $("#func2").clone();
                $('#contratacao').change(function(){                    
                    if(($(this).val() == "1") || ($(this).val() == "3")){
                        $("#func2").remove();
                    }else if($(this).val() == "2"){
                        if (!$("div.form_funcoes fieldset#func2").length) {
                           var fieldset =  $(document.createElement('fieldset')).append(window.func2.html()).prop('id','func2');
                           $("#func1").after(fieldset);
                        }
                    }
                });
                
                //chickbox
                $(".bt-image").on("click", function() {
                    var action = $(this).data("type");
                    
                    var txtVal = $("#textVal").html();
                    $("#salario_antigo").html("#salario_new");
                    $(".valorForm").html(txtVal);
                    $("#salario_novo").val("");
                    $("#diferenca").html("");
                    
                    if (action === "salario") {
                        //thickBoxIframe("Alteração Salarial", "altera_salario.php", {curso: key, method: "getDocs"}, "360-not", "240");
                        thickBoxModal("Alteração Salarial", "#box_salario", "240", "360", null, null).css({display: "block"});
                    }
                });
                
                //calculo de diferença salarial
                $(".bt-image2").click(function() {
                    var antigo = $('#salario_antigo').val();
                    var novo = $('#salario_novo').val().replace('.', '');
                        novo = novo.replace(',', '.');
                    var total = (parseFloat(novo) - parseFloat(antigo)).toFixed(2);
                    
                    console.log(antigo);
        
                    $("#diferenca").html(total);
                    $("#difere").val(total);
                    $("#salario_new").val(novo);
                    $("#salario").val(novo);
                });
                
                $("#altera_salario").click(function() {                    
                    var novo = $('#salario_novo').val().replace('.', '');
                        novo = novo.replace(',', '.');
                    var data = $("#form2").serialize();
                    
                    if((novo === 0) || (novo === '')){
                        $("#erro2").html('<strong>Preencha o Salário Novo</strong>').css({color: "#F00"});
                    }else if($("#difere").val() === ''){
                        $("#erro2").html('<strong>Calcule a diferença</strong>').css({color: "#F00"});
                    }else{
                        $.post('edit_curso.php?method=alteraSalario&' + data, null, function(data){
                            if(data.status == 1){
                                $('#textVal').html(data.valor);
                                //$(".ui-icon-closethick").trigger("click");
                                thickBoxClose();                                
                            }
                        },'json');
                    }
                });                               
                
                //clona o fieldset de horario
                $("#add_hor").click(function(){
                    var clone = $('.form_funcoes .horario:last').clone(false);
                    var next_position = parseFloat( clone.attr('data-position')) + 1;
                    clone.attr('data-position', next_position);                                       
                    clone.find("*[id]").andSelf().each(function() { 
                        $(this).attr("id", $(this).attr("id") + next_position); 
                    });
                    
                    clone.find(".check_not").each(function(){
                        $(this).attr({name:"noturno[" + next_position + "]"});
                    });
                    
                    $('.form_funcoes .horario:last').after(clone);
                    var p = $(this).prev().attr("data-position");
                    if(p == next_position){
                        $("fieldset[data-position = " + next_position + "] .check[value=1]").attr({name:"folga[" + next_position + "][0]"});
                        $("fieldset[data-position = " + next_position + "] .check[value=2]").attr({name:"folga[" + next_position + "][1]"});
                        $("fieldset[data-position = " + next_position + "] .check[value=5]").attr({name:"folga[" + next_position + "][2]"});
                    }
                                        
                    clone.find(".limpa").val("");
                    clone.find(".check").prop('checked', false);
                    //$("p").removeClass("preenchimento");
                    //$("#entrada, #ida_almoco, #volta_almoco, #saida").mask("99:99:99");
                    
                    $('.form_funcoes .horario:last').addClass("del");
                    $(".del .del_hor").css({display: 'block'});
                    
                    $(".entrada, .ida_almoco, .volta_almoco, .saida")
                        .unmask() //Desabilita a máscara. Se não fizer isso dá problema
                        .mask("99:99:99"); //Habilita novamente, pegando todos os campos criados 
                
                    $(".check_not").on('click', function (){
                        var hide_noturno = $(this).parent().parent().parent().parent().next();
                        var noturno = $(this).val();

                        if(noturno == 1){
                            hide_noturno.show();
                        }else{
                            hide_noturno.children().val('');
                            hide_noturno.hide();
                        }
                    });
                    
                    $(".n_sim").each(function(){
                        if($(".n_sim").is(':checked')){
                            $(this).parent().parent().parent().parent().next();
                        }
                    });
                    
                    $(".del_hor").on('click', function (){
                        $(this).parents("fieldset").remove();
                    });
                });
                
                $(".del_hor").on('click', function (){
                    $(this).parents("fieldset").remove();
                });
                
                //trata insalubridade/periculosidade
                $("#insal").click(function(){
                    $(".some_insa").show();                    
                    $("#insalubridade").addClass("validate[custom[select]]");
                    $("#qtd_salarios").addClass("validate[required,custom[onlyNumber]]");
                });
                
                $("#peric").click(function(){
                    $(".some_insa").hide();
                    $("#insalubridade").removeClass("validate[custom[select]]");
                    $("#qtd_salarios").removeClass("validate[required,custom[onlyNumber]]");
                });
                
                if($("#insal").is(':checked')){
                    $(".some_insa").show();
                }
                
                $(".check_not").on('click', function (){
                    var hide_noturno = $(this).parent().parent().parent().parent().next();
                    var noturno = $(this).val();
                    
                    if(noturno == 1){
                        hide_noturno.show();
                    }else{
                        hide_noturno.children().val('');
                        hide_noturno.hide();
                    }
                });
                
                $(".n_sim").each(function(){
                    if($(".n_sim").is(':checked')){
                        $(this).parent().parent().parent().parent().next();
                    }
                });
            });
        </script>
    </body>
</html>