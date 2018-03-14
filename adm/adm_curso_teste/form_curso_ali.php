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

$_SESSION['regiao_select'] = $regiao_selecionada;
$_SESSION['projeto_select'] = $projeto_selecionado;
session_write_close();
?>

<html>
    <head>
        <title>:: Intranet :: Cursos</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../../favicon.ico" />        
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="cursos.css" rel="stylesheet" type="text/css" />         
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="jquery.autocomplete.css" rel="stylesheet" type="text/css" />  
        
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../js/global.js" type="text/javascript"></script>
        <script src="../../jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript" ></script>
        <script src="../../js/jquery.maskMoney.js" type="text/javascript" ></script>
        <script src="../../js/jquery.validationEngine.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt.js" type="text/javascript"></script>
        <script src="../../js/jquery.autocomplete.js" type="text/javascript"></script>
        
        <script>
            $(function() {
                //mascara
                $("#data_ini").mask("99/99/9999");
                $("#data_fim").mask("99/99/9999");
                $("#entrada_0, #ida_almoco_0, #volta_almoco_0, #saida_0").mask("99:99:99");
                $("#salario, #quota").maskMoney({prefix:'R$ ', allowNegative: true, thousands:'.', decimal:','});
                
                //autocomplete
                $("#cbo").autocomplete("lista_cbo.php", {
                    width: 600,
                    minChars: 3,
                    select: function( event, ui ) {
                        console.log(event, 'even');
                        console.log(ui, 'ui');
                    }
                });
                
                //validation engine
                //$("#form1").validationEngine({promptPosition : "topRight"});
                
                //valida periodo de data (provisorio), usar validate engine
                $("#cadastrar").click(function(){
                    var dataIni = $("#data_ini").val().split('/');
                    var dataFim = $("#data_fim").val().split('/');
                    var calcDataIni = dataIni[2]+'-'+dataIni[1]+'-'+dataIni[0];
                    var calcDataFim = dataFim[2]+'-'+dataFim[1]+'-'+dataFim[0];                                        
                    
                    if(Date.parse(calcDataIni) > Date.parse(calcDataFim)){
                        $("#erro").html('<strong>Inicio maior que Final</strong>').css({color: "#F00"}); 
                        return false;
                    }else if(Date.parse(calcDataIni) == Date.parse(calcDataFim)){
                        $("#erro").html('<strong>Inicio igual ao Final</strong>').css({color: "#F00"});                        
                        return false;
                    }else{
                        $("#erro").css({display: "none"});
                    }
                });
                
                //atribui ao campo nome horario o nome da funcao
                $("#nome_funcao").change(function(){
                    $("#nome_horario").val($("#nome_funcao").val());
                });
                
                //oculta/exibe dados do CLT
                window.func2 = $(".horario").clone();
                $('#contratacao').change(function(){
                    if(($(this).val() == "3") || ($(this).val() == "1")){
                        $(".horario").remove();
                        $("#add_hor").hide();
                    }else if($(this).val() == "2"){
                        if (!$("div.form_funcoes fieldset.horario").length) {
                           var fieldset =  $(document.createElement('fieldset')).append(window.func2.html()).prop('class','horario');
                           $("#func1").after(fieldset);
                           $("#add_hor").show();
                        }
                    }
                    
                    if($(this).val() == "3"){
                        $(".cooperado").css({display: 'block'});
                        $(".vali input").addClass("validate[custom[onlyNumber]]");
                    }else if(($(this).val() == "1") || ($(this).val() == "2")){
                        $(".cooperado").css({display: 'none'});                        
                    }
                });
                
                //clona o fieldset de horario
                $(".form_funcoes").on("click","#add_hor",function(){
                    $("#entrada_0, #ida_almoco_0, #volta_almoco_0, #saida_0").unmask();
                    var clone = $('.form_funcoes .horario:last').clone(true);
                    var next_position = parseFloat( clone.attr('data-position')) + 1;
                    clone.attr('data-position', next_position);
                    
                    var contador = next_position - 1;
                    clone.find("#entrada_" + contador).attr({"id":"entrada_" + next_position,"data-Ordem":next_position});
                    clone.find("#ida_almoco_" + contador).attr({"id":"ida_almoco_" + next_position,"data-Ordem":next_position});
                    clone.find("#volta_almoco_" + contador).attr({"id":"volta_almoco_" + next_position,"data-Ordem":next_position});
                    clone.find("#saida_" + contador).attr({"id":"saida_" + next_position,"data-Ordem":next_position});
                    
                    //$("#entrada_" + contador).unmask("99:99:99");
                    
                    
                    $('.form_funcoes .horario:last').after(clone);
                    var p = $(this).prev().attr("data-position");
                    if(p == next_position){
                        $("fieldset[data-position = " + next_position + "] .check[value=1]").attr({name:"folga[" + next_position + "][0]"});
                        $("fieldset[data-position = " + next_position + "] .check[value=2]").attr({name:"folga[" + next_position + "][1]"});
                        $("fieldset[data-position = " + next_position + "] .check[value=5]").attr({name:"folga[" + next_position + "][2]"});                                
                    }
                    
                    clone.find(".limpa").val("");
                    clone.find(".check").prop('checked', false);
                    //$("#entrada:last").removeClass("preenchimento");
                    //$("p").removeClass("preenchimento");
                    //$("#entrada").mask("99:99:99");
                    $('.form_funcoes .horario:last').addClass("del");
                    $(".del #del_hor").css({display: 'block'});
                    
                    $("#entrada_" + next_position).mask("99:99:99");
                    $("#ida_almoco_" + next_position).mask("99:99:99");
                    $("#volta_almoco_" + next_position).mask("99:99:99");
                    $("#saida_" + next_position).mask("99:99:99");
                });
                
                //deleta fieldset
                $("#del_hor").click(function (){
                    $(this).parents("fieldset").remove();                    
                });
                
                $("#insal").click(function(){
                    $(".some_insa").show();                    
                    $("#insalubridade").addClass("validate[custom[select]]");
                });
                
                $("#peric").click(function(){
                    $(".some_insa").hide();
                    $("#insalubridade").removeClass("validate[custom[select]]");
                });
                
            });
        </script>
        
        <style>
            .data{
                width: 80px;
            }
            .colEsq{
                float: left;
                width: 57%;
                margin-top: -10px;
            }
            fieldset{
                margin-top: 10px;
                
            }
            fieldset legend{
                font-family: 'Exo 2', sans-serif;
                font-size: 16px!important;
                font-weight: bold;
            }
            .first{
                vertical-align: 0!important;
            }
            .first-2{
                vertical-align: 0!important;
            }
            .cooperado{
                display: none;
            }
            .some_insa{
                display: none;
            }
        </style>
        
    </head>
    <body class="novaintra">
        <div id="content" style="width: 850px;">
            <form action="" method="post" name="form1" id="form1" autocomplete="off">
                <div id="head">
                    <img src="../../imagens/logomaster<?php echo $usuario['id_master'] ?>.gif" class="fleft" style="margin-right: 25px;">
                    <div class="fleft">
                        <h2>Cadastro de Função</h2>
                    </div>
                </div>
                
                <?php
                if(isset($_SESSION['regiao'])){
                    $regiao_selecionada = $_SESSION['regiao'];
                }
                ?>
                
                <input type="hidden" name="regiao_selecionada" id="regiao_selecionada" value="<?php echo $regiao_selecionada; ?>" />
                <input type="hidden" name="regiao_logado" id="regiao_logado" value="<?php echo $id_regiao; ?>" />
                
                <!--resposta de algum metodo realizado-->
                <div id="message-box" class="<?php echo $_SESSION['MESSAGE_COLOR']; ?> alinha2">
                    <?php echo $_SESSION['MESSAGE'];
                    session_destroy(); ?>
                </div>                                                                                
                
                <div class="form_funcoes">                                        
                    
                    <fieldset id="func1">
                        <legend>Dados da Função</legend>
                        <p>
                            <label class='first alinha'>Tipo de Contratação:</label>                               
                            <select name="contratacao" id="contratacao" class="validate[required]">
                                <option class="btn_cont1" value="1">Autônomo</option>
                                <option class="btn_cont2" value="2" selected="selected">CLT</option>
                                <option class="btn_cont3" value="3">Cooperado</option>
                            </select>
                        </p>
                        <p>
                            <label class='first'>Projeto:</label>
                            <?php echo montaSelect(getProjetos($regiao_selecionada),null, "id='projeto' name='projeto'"); ?>
                        </p>
                        <p>
                            <label class='first'>Nome da Função:</label>
                            <input type="text" name="nome_funcao" id="nome_funcao" size="108" class="validate[required]" />
                        </p>
                        <p>
                            <label class='first'>Área:</label>
                            <input type="text" name="area" id="area" size="108" class="validate[required]" />
                        </p>
                        <p>
                            <label class='first'>CBO:</label>
                            <input type="text" name="cbo" id="cbo" size="70" class="validate[required]" />
                            <span id="selection"></span>
                        </p>
                        <p>
                            <label class='first'>Local:</label>
                            <input type="text" name="local" id="local" size="108" class="validate[required]" />
                        </p>
                        
                        <div id="esquerda">
                            <p>
                                <label class='first'>Início:</label>
                                <input type="text" name="data_ini" id="data_ini" size="16" class="validate[required,custom[dateBr]]" />
                            </p>
                            <p>                                   
                                <div id="erro"></div>
                            </p>
                            <p>
                                <label class='first'>Final:</label>
                                <input type="text" name="data_fim" id="data_fim" size="16" class="validate[required,custom[dateBr]]" />
                            </p>
                            <p>
                                <label class='first'>Salário:</label>
                                <input type="text" name="salario" id="salario" size="30" class="validate[required]" />
                            </p>                            
                            <p>
                                <label class='first'>Mês Abono:</label>
                                <?php echo montaSelect(mesesArray(),null,"id='mes_abono' name='mes_abono'"); ?>
                            </p>                                                        
                        </div>
                        
                        <div id="direita">                                                                                    
                            <p class="cooperado vali">
                                <label class='first'>Parcelas:</label>
                                <input type="text" name="parcelas" id="parcelas" size="30" maxlength="4" />
                            </p>
                            <p class="cooperado">
                                <label class='first'>Quota:</label>
                                <input type="text" name="quota" id="quota" size="30" />
                            </p>
                            <p class="cooperado vali">
                                <label class='first'>Parcela das Quotas:</label>
                                <input type="text" name="parcela_quotas" id="parcela_quotas" size="30" maxlength="4" />
                            </p>
                            <p>
                                <label class='first'>Qtd. Máxima de Contratação:</label>
                                <input type="text" name="qtd_contratacao" id="qtd_contratacao" size="30" maxlength="4" class="validate[required,custom[onlyNumber]]" />
                            </p>
                            <p>
                                <label class='first'>Horas:</label>
                                <input type="text" name="horas" id="horas" size="30" maxlength="4" class="validate[custom[onlyNumber]]" />
                            </p>                            
                        </div>
                        
                        <div class="clear"></div> 
                        
                        <p>
                            <label class="first"></label>
                            <label>
                                <input type="radio" name="periculosidade" value="" id="insal" />Insalubridade
                                <input type="radio" name="periculosidade" value="1" id="peric" />Periculosidade 30%
                            </label>
                        </p>
                        
                        <p class="some_insa">
                            <label class='first'>Insalubridade:</label>
                            <select name="insalubridade" id="insalubridade">
                                <option value="-1">« Selecione »</option>
                                <option value="1">Insalubridade 20%</option>
                                <option value="2">Insalubridade 40%</option>
                            </select>
                        </p>
                        <p class="some_insa">
                            <label class='first'>Quantidade de Salários:</label>
                            <input type="text" name="qtd_salarios" id="qtd_salarios" size="30" maxlength="4" /> <!--class="validate[required,custom[onlyNumber]]"-->
                        </p>
                        
                        <p>
                            <label class='first' style="vertical-align: top!important;">Descrição:</label>
                            <textarea name="descricao" id="descricao" rows="5" cols="85"><?php echo $prestador['endereco']?></textarea>
                        </p>
                        
                    </fieldset>
                    
                    <fieldset class="horario" data-position="0">
                        <div id="del_hor"><img src="../../imagens/icones/icon-delete.gif" title="Deletar horário" /></div>
                        <legend>Dados do Horário</legend>
                        <p>
                            <label class='first'>Nome do Horário:</label>
                            <input type="text" name="nome_horario[]" id="nome_horario" size="108" class="validate[required] limpa" />
                        </p>
                        <p>
                            <label class='first'>Observações:</label>
                            <input type="text" name="obs[]" id="obs" class="limpa" size="108" />
                        </p>
                        <p class="remove">
                            <label class='first'>Preenchimento:</label>
                            Entrada <input type="text" name="entrada[]" id="entrada_0" size="10" class="preenchimento validate[required] limpa" data-ordem="0" />
                            Saída Almoço <input type="text" name="ida_almoco[]" id="ida_almoco_0" size="10" class="preenchimento validate[required] limpa" />
                            Retorno Almoço <input type="text" name="volta_almoco[]" id="volta_almoco_0" size="10" class="preenchimento validate[required] limpa" />
                            Saída <input type="text" name="saida[]" id="saida_0" size="10" class="preenchimento validate[required] limpa" />
                        </p>
                        
                        <div id="esquerda">
                            <p>
                                <label class='first'>Horas Mês:</label>
                                <input type="text" name="horas_mes[]" id="horas_mes" size="30" maxlength="4" class="validate[required,custom[onlyNumber]] limpa" />
                            </p>
                            <p>
                                <label class='first'>Dias Mês:</label>
                                <input type="text" name="dias_mes[]" id="dias_mes" size="30" maxlength="4" class="validate[required,custom[onlyNumber]] limpa" />
                            </p>
                        </div>
                        
                        <div id="direita">
                            <p>
                                <label class='first'>Dias Semana:</label>
                                <input type="text" name="dias_semana[]" id="dias_semana" size="30" maxlength="4" class="validate[required,custom[onlyNumber]] limpa" />
                            </p>
                            <p>
                                <label class='first'>Folgas:</label>
                                <input class="check" type="checkbox" name="folga[][0]" value="1" /> Sábado
                                <input class="check" type="checkbox" name="folga[][1]" value="2" /> Domingo
                                <input class="check" type="checkbox" name="folga[][2]" value="5" /> Plantonista
                            </p>
                        </div>
                    </fieldset>
                    
                    <div id="add_hor">Adicionar outro Horário<img src="../../imagens/icones/icon-add.png" title="Adicionar outro horário" /></div>
                    
                    <div class="clear"></div>
                    
                <p class="controls">
                    <input type="submit" name="cadastrar" id="cadastrar" value="Cadastrar" />
                    <input type="button" name="voltar" id="voltar" value="Voltar" onclick="window.location = 'index.php';" />
                </p>
                
                </div><!--form_funcoes-->                             
                
            </form>
        </div>
    </body>
</html>