<?php
session_start();

if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../wfunction.php');
include('../../classes/global.php');
include('../../classes/FuncoesClassTeste.php');
include("../../classes_permissoes/acoes.class.php");

$acoes = new Acoes();

$usuario = carregaUsuario();
$master = $usuario['id_master'];
$id_regiao = $usuario['id_regiao'];
$id_usuario = $_COOKIE['logado'];

if(isset($_REQUEST['method']) && !empty($_REQUEST['method']) && $_REQUEST['method']=="alteraSalario"){
    $id_curso = $_REQUEST['id_curso'];
    $data_cad = date('Y-m-d');
    $salario_antigo = $_REQUEST['salario_antigo'];
    $salario_novo = $_REQUEST['salario_new'];
    $diferenca = $_REQUEST['difere'];
    
    mysql_query("INSERT INTO rh_salario (id_curso,data,salario_antigo,salario_novo,diferenca,user_cad,status) VALUES 
    ('$id_curso','$data_cad','$salario_antigo','$salario_novo','$diferenca','$id_usuario','1')") or die (mysql_error());
    
    mysql_query("UPDATE curso SET salario = '$salario_novo', valor = '$salario_novo' WHERE id_curso = '$id_curso' LIMIT 1") or die (mysql_error());
    
    $return = array('status'=>1);
    //$return = $_REQUEST;
    $return['valor'] = "R$ ".number_format($_REQUEST['salario_new'],2,",",".");
    //"R$ ".number_format($_REQUEST['salario_new'],2,",",".");
    echo json_encode($return);
    exit;
}

$sql = FuncoesClass::getRhHorario($_REQUEST['curso']);
$total_horario = mysql_num_rows($sql);

$row = FuncoesClass::getCursosID($_REQUEST['curso']);

$altera_funcao = FuncoesClass::alteraFuncao($usuario, $id_regiao, $id_usuario);

//dados para voltar no index com select preenchido
$regiao_selecionada = $_REQUEST['hide_regiao'];
$projeto_selecionado = $_REQUEST['hide_projeto'];

if($regiao_selecionada == ''){
    $_SESSION['regiao_select'];
    $_SESSION['projeto_select'];
    session_write_close();
}else{
    $_SESSION['regiao_select'] = $regiao_selecionada;
    $_SESSION['projeto_select'] = $projeto_selecionado;
    session_write_close();
}

?>

<html>
    <head>
        <title>:: Intranet :: Cursos</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        
        <link rel="shortcut icon" href="../../favicon.ico" />        
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="cursos.css" rel="stylesheet" type="text/css" /> 
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="jquery.autocomplete.css" rel="stylesheet" type="text/css" />        
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />        
        
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../js/global.js" type="text/javascript"></script>
        <script src="../../jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript" ></script>
        <script src="../../js/jquery.maskMoney.js" type="text/javascript" ></script>
        <script src="../../js/jquery.validationEngine.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt.js" type="text/javascript"></script>
        <script src="../../js/jquery.autocomplete.js" type="text/javascript"></script>
        <script>
            $(function(){
                //mascara
                $("#data_ini").mask("99/99/9999");
                $("#data_fim").mask("99/99/9999");
                $("#entrada, #ida_almoco, #volta_almoco, #saida").mask("99:99:99");
                $("#salario, #valor, #quota, #salario_novo").maskMoney({prefix:'R$ ', allowNegative: true, thousands:'.', decimal:','});
                
                //autocomplete
                $("#cbo").autocomplete("lista_cbo.php", {
                    width: 600,
                    matchContains: false,      
                    minChars: 3,
                    selectFirst: false                    
                });
                
                //validation engine
                //$("#form1").validationEngine({promptPosition : "topRight"});
                
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
                    var clone = $('.form_funcoes .horario:last').clone(true);
                    var next_position = parseFloat( clone.attr('data-position')) + 1;
                    clone.attr('data-position', next_position);                                       
                    
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
                    $(".del #del_hor").css({display: 'block'});
                });
                
                //deleta fieldset
                $("#del_hor img").click(function (){
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
            });
        </script>
     
        <style>
            .data{width: 80px;}
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
            .bt-image{                
                cursor: pointer;
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
                        <h2>Editar Função <?php echo $row['nome_funcao']; ?></h2>
                    </div>
                </div>
                
                <input type="hidden" name="id_curso" id="id_curso" value="<?php echo $row['id_curso']; ?>" />
                <input type="hidden" name="regiao" id="regiao" value="<?php echo $row['id_regiao']; ?>" />
                <input type="hidden" name="projeto" id="projeto" value="<?php echo $row['campo3']; ?>" />
                <input type="hidden" name="id_cbo" id="id_cbo" value="<?php echo $row['cbo_codigo']; ?>" />   
                <input type="hidden" name="contratacao_curso" id="contratacao_curso" value="<?php echo $row['tipo']; ?>" />                   
                
                <div class="form_funcoes">
                    
                    <fieldset id="func1">
                        <legend>Dados da Função</legend>                        
                        <p>
                            <label class='first'>Nome da Função:</label>
                            <input type="text" name="nome_funcao" id="nome_funcao" size="108" value="<?php echo $row['nome_funcao']; ?>" class="validate[required]" />
                        </p>
                        <p>
                            <label class='first'>Área:</label>
                            <input type="text" name="area" id="area" size="108" value="<?php echo $row['area']; ?>" class="validate[required]" />
                        </p>
                        <p>
                            <label class='first'>CBO:</label>
                            <input type="text" name="cbo" id="cbo" size="108" value="<?php echo $row['nome_cbo']; ?>" class="validate[required]" />
                        </p>
                        <p>
                            <label class='first'>Local:</label>
                            <input type="text" name="local" id="local" size="108" value="<?php echo $row['local']; ?>" />
                        </p>
                        
                        <div id="esquerda">
                            <p>
                                <label class='first'>Salário:</label>
                                
                                <?php
                                if($acoes->verifica_permissoes(84)){
                                ?>
                                <?php echo "<span id='textVal'>".formataMoeda($row['salario'])."</span>"; ?>
                                    <img src="../../imagens/icones/icon-edit.gif" title="Editar Valor" class="edita_valor bt-image" data-type="salario" data-key="<?php echo $row['id_curso']; ?>" />
                                <?php
                                }else{
                                    echo formataMoeda($row['valor']);
                                }
                                ?>
                                <input type="hidden" name="salario" id="salario" value="<?php echo $row['salario']; ?>" />
                            </p>
                            <p>
                                <label class='first'>Parcelas:</label>
                                <input type="text" name="parcelas" id="parcelas" size="30" maxlength="4" value="<?php echo $row['parcelas']; ?>" />
                            </p>
                            <p>
                                <label class='first'>Qtd. Máxima de Contratação:</label>
                                <input type="text" name="qtd_contratacao" id="qtd_contratacao" size="30" maxlength="4" value="<?php echo $row['qnt_maxima']; ?>" />
                            </p>
                            <p>
                                <label class='first'>Horas:</label>
                                <input type="text" name="horas" id="horas" size="30" maxlength="4" value="<?php echo $row['hora_mes']; ?>" />
                            </p>
                            <p>
                                <label class="first"></label>
                                <label>
                                    <input type="radio" name="periculosidade" value="" id="insal" 
                                    <?php 
                                    if($row['tipo_insalubridade'] != '0'){
                                        echo "checked";
                                    }
                                    ?> />
                                    Insalubridade
                                    
                                    <input type="radio" name="periculosidade" value="1" id="peric" 
                                    <?php 
                                    if($row['periculosidade_30'] == '1'){
                                        echo "checked";
                                    }
                                    ?> />
                                    Periculosidade 30%
                                </label>
                            </p>

                            <p class="some_insa">
                                <label class='first'>Insalubridade:</label>
                                <select name="insalubridade" id="insalubridade">
                                    <option value="-1">« Selecione »</option>                                    
                                    <option value="1" <?php echo selected(1, $row['tipo_insalubridade']); ?>>Insalubridade 20%</option>
                                    <option value="2" <?php echo selected(2, $row['tipo_insalubridade']); ?>>Insalubridade 40%</option>
                                </select>
                            </p>
                            <p class="some_insa">
                                <label class='first'>Quantidade de Salários:</label>
                                <input type="text" name="qtd_salarios" id="qtd_salarios" size="30" maxlength="4" value="<?php echo $row['qnt_salminimo_insalu']; ?>" />
                            </p>
                        </div>
                        
                        <div class="clear"></div>
                        
                        <p>
                            <label class='first' style="vertical-align: top!important;">Descrição:</label>
                            <textarea name="descricao" id="descricao" rows="5" cols="85"><?php echo $row['descricao']; ?></textarea>
                        </p>
                        
                    </fieldset>
                    
                    <?php
                    $posicao = 0;
                    $numRows = mysql_num_rows($sql);
                    if($numRows>0){
                        while ($rst = mysql_fetch_assoc($sql)) {
                            $folga = $rst['folga'];
                    ?>
                    
                    <input type="hidden" name="id_horario[]" id="id_horario" value="<?php echo $rst['id_horario']; ?>" />
                    
                    <fieldset id="func2" class="horario" data-position="<?php echo $posicao; ?>"  style="display:<?php echo $display; ?>">
                        <div id="del_hor"><img src="../../imagens/icones/icon-delete.gif" title="Deletar horário" /></div>
                        <legend>Dados do Horário</legend>   
                        <p>
                            <label class='first'>Nome do Horário:</label>
                            <input type="text" name="nome_horario[]" id="nome_horario" size="108" class="validate[required] limpa" value="<?php echo $rst['nome']; ?>" />
                        </p>
                        <p>
                            <label class='first'>Observações:</label>
                            <input type="text" name="obs[]" id="obs" class="limpa" size="108" value="<?php echo $rst['obs']; ?>" />
                        </p>
                        <p class="remove">
                            <label class='first'>Preenchimento:</label>
                            Entrada <input type="text" name="entrada[]" id="entrada" size="10" class="preenchimento validate[required] limpa" value="<?php echo $rst['entrada_1']; ?>" />
                            Saída Almoço <input type="text" name="ida_almoco[]" id="ida_almoco" size="10" class="preenchimento validate[required] limpa" value="<?php echo $rst['saida_1']; ?>" />
                            Retorno Almoço <input type="text" name="volta_almoco[]" id="volta_almoco" size="10" class="preenchimento validate[required] limpa" value="<?php echo $rst['entrada_2']; ?>" />
                            Saída <input type="text" name="saida[]" id="saida" size="10" class="preenchimento validate[required] limpa" value="<?php echo $rst['saida_2']; ?>" />
                        </p>
                        <div id="esquerda">
                            <p>
                                <label class='first'>Horas Mês:</label>
                                <input type="text" name="horas_mes[]" id="horas_mes" size="30" maxlength="4" value="<?php echo $rst['horas_mes']; ?>" class="limpa" />
                            </p>
                            <p>
                                <label class='first'>Dias Mês:</label>
                                <input type="text" name="dias_mes[]" id="dias_mes" size="30" maxlength="4" value="<?php echo $rst['dias_mes']; ?>" class="limpa" />
                            </p>
                        </div>
                        
                        <div id="direita">
                            <p>
                                <label class='first'>Dias Semana:</label>
                                <input type="text" name="dias_semana[]" id="dias_semana" size="30" maxlength="4" value="<?php echo $rst['dias_semana']; ?>" class="limpa" />
                            </p>
                            <p>
                                <label class='folgas'>Folgas:</label>
                                <input class="check" type="checkbox" name="folga[<?php echo $posicao; ?>][0]" value="1" 
                                    <?php 
                                    if(($folga == 1) || ($folga == 3)){
                                        echo "checked";
                                    }
                                    ?> />
                                Sábado
                                
                                <input class="check" type="checkbox" name="folga[<?php echo $posicao; ?>][1]" value="2" 
                                    <?php 
                                    if(($folga == 2) || ($folga == 3)){
                                        echo "checked";
                                    }
                                    ?> />
                                Domingo
                                
                                <input class="check" type="checkbox" name="folga[<?php echo $posicao; ?>][2]" value="5" 
                                    <?php 
                                    if(($folga == 5)){
                                        echo "checked";
                                    }
                                    ?> />
                                Plantonista
                            </p>
                        </div>
                    </fieldset>
                    
                    <?php 
                        $posicao++; 
                        }
                    }else{?>
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
                            Entrada <input type="text" name="entrada[]" id="entrada" size="10" class="preenchimento validate[required] limpa" data-ordem="0" />
                            Saída Almoço <input type="text" name="ida_almoco[]" id="ida_almoco" size="10" class="preenchimento validate[required] limpa" />
                            Retorno Almoço <input type="text" name="volta_almoco[]" id="volta_almoco" size="10" class="preenchimento validate[required] limpa" />
                            Saída <input type="text" name="saida[]" id="saida" size="10" class="preenchimento validate[required] limpa" />
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
                  <?php  }
                    ?>                                        
                    
                    <div id="add_hor">Adicionar outro Horário<img src="../../imagens/icones/icon-add.png" title="Adicionar outro horário" /></div>
                
                <p class="controls">
                    <input type="submit" name="atualizar" id="atualizar" value="Atualizar" />
                    <input type="button" name="voltar" id="voltar" value="Voltar" onclick="window.location = 'index.php';" />
                </p>
                
                </div><!--form_funcoes-->
                
            </form>
            
            <div id="box_salario">
                
                <form action="" method="post" name="form2" id="form2" autocomplete="off">
                    
                    <input type="hidden" name="id_curso" id="id_curso" value="<?php echo $row['id_curso']; ?>" />
                    <input type="hidden" name="salario_antigo" id="salario_antigo" value="<?php echo $row['salario']; ?>" />
                    <input type="hidden" name="salario_new" id="salario_new" value="" />
                    <input type="hidden" name="difere" id="difere" value="" />
                    
                    <div id='erro2'></div>
                    
                    <p>
                        <label class='first'>Salário Antigo: <span class="valorForm"><?php echo formataMoeda($row['salario']); ?></span></label>                
                    </p>
                    <p>
                        <label class='first'>Salário Novo: R$ </label>
                        <input type="text" name="salario_novo" id="salario_novo" size="20" />
                        <img src="../../imagens/icones/icon-calculator.gif" title="Calcular Diferença" id="calculo_diferenca" class="edita_valor bt-image2" />
                    </p>
                    <p>
                        <label class='first'>Diferença:</label>
                        R$: <strong id="diferenca"></strong>
                    </p>
                    <p class="controls">
                        <input type="button" name="altera_salario" id="altera_salario" value="Atualizar" />
                    </p>
                </form>
                
            </div>
            
        </div>
    </body>
</html>