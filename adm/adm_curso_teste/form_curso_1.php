<?php
session_start();

if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../wfunction.php');
include('../../classes/global.php');
include('../../classes/FolhaClass.php');

$usuario = carregaUsuario();
$master = $usuario['id_master'];
$id_regiao = $usuario['id_regiao'];
$id_usuario = $_COOKIE['logado'];  

$curso_geral = getFuncoes($usuario, $id_regiao, $id_usuario);

?>

<html>
    <head>
        <title>:: Intranet :: Cursos</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../../favicon.ico" />        
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="cursos.css" rel="stylesheet" type="text/css" /> 
        <script src="../../js/global.js" type="text/javascript"></script>           
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />        
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>        
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>        
        
        <!--mascaras-->
        <script src="../../jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript" ></script>
        <script src="../../js/jquery.maskMoney.js" type="text/javascript" ></script>
        
        <!--validation engine-->
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <script src="../../js/jquery.validationEngine.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt.js" type="text/javascript"></script>
        
        <!--autocomplete-->
        <link href="jquery.autocomplete.css" rel="stylesheet" type="text/css" />        
        <script src="../../js/jquery.autocomplete.js" type="text/javascript"></script>                       
        
        <script>
            $(function() {                                
                //mascara
                $("#data_ini").mask("99/99/9999");
                $("#data_fim").mask("99/99/9999");
                $("#entrada, #ida_almoco, #volta_almoco, #saida").mask("99:99:99");
                $("#salario, #valor, #quota").maskMoney({prefix:'R$ ', allowNegative: true, thousands:'.', decimal:','});                                
                
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
                
                <!--resposta de algum metodo realizado-->
                <div id="message-box" class="<?php echo $_SESSION['MESSAGE_COLOR']; ?> alinha2">
                    <?php echo $_SESSION['MESSAGE'];
                    session_destroy(); ?>
                </div>
                                
                <label class='first alinha'>Tipo de Contratação:</label>                               
                <select name="contratacao" id="contratacao" class="validate[required]">
                    <option class="btn_cont1" value="1">Autônomo</option>
                    <option class="btn_cont2" value="2" selected="selected">CLT</option>
                    <option class="btn_cont3" value="3">Cooperado</option>
                </select>                                
                
                <div class="form_funcoes">                                        
                    
                    <fieldset id="func1">
                        <legend>Dados da Função</legend>
                        <p>
                            <label class='first'>Projeto:</label>
                            <?php echo montaSelect(getProjetos($id_regiao),null, "id='projeto' name='projeto'"); ?>
                        </p>
                        <p>
                            <label class='first'>Nome da Função:</label>
                            <input type="text" name="nome_funcao" id="nome_funcao" size="85" class="validate[required]" />
                        </p>
                        <p>
                            <label class='first'>Área:</label>
                            <input type="text" name="area" id="area" size="85" class="validate[required]" />
                        </p>
                        <p>
                            <label class='first'>CBO:</label>
                            <input type="text" name="cbo" id="cbo" size="85" class="validate[required]" />
                        </p>
                        <p>
                            <label class='first'>Local:</label>
                            <input type="text" name="local" id="local" size="85" class="validate[required]" />
                        </p>
                        
                        <div id="esquerda">
                            <p>
                                <label class='first'>Início:</label>
                                <input type="text" name="data_ini" id="data_ini" size="16" class="validate[required,custom[dateBr]]" />
                            </p>
                            <p>
                                <label class='first'>Final:</label>
                                <input type="text" name="data_fim" id="data_fim" size="16" class="validate[required,custom[dateBr]]" />
                            </p>                                                        
                            <p>
                                <label class='first'>Salário:</label>
                                <input type="text" name="salario" id="salario" size="38" />
                            </p>
                            <p>
                                <label class='first'>Projeto:</label>
                                <select name="projeto_a" id="projeto_a">
                                    <option value="">« Selecione »</option>
                                    <option value="SOE">SOE</option>
                                    <option value="LATINO">LATINO</option>
                                </select>
                            </p>
                            <p>
                                <label class='first'>Mês Abono:</label>
                                <?php echo montaSelect(mesesArray(),null,"id='mes_abono' name='mes_abono'"); ?>
                            </p>
                            <p>
                                <label class='first'>Insalubridade:</label>
                                <select name="insalubridade" id="insalubridade" class="validate[required]">
                                    <option value="-1">« Selecione »</option>
                                    <option value="1">Insalubridade 20%</option>
                                    <option value="2">Insalubridade 40%</option>                                
                                </select>
                            </p>
                            <p>
                                <label class='first'>Quantidade de Salários:</label>
                                <input type="text" name="qtd_salarios" id="qtd_salarios" size="16" maxlength="4" class="validate[required,custom[onlyNumber]]" />
                            </p>
                        </div>
                        
                        <div id="direita"> 
                            <p>
                                <label class='first'>Valor:</label>
                                <input type="text" name="valor" id="valor" size="38" class="validate[required]" />
                            </p>                            
                            <p>
                                <label class='first'>Parcelas:</label>
                                <input type="text" name="parcelas" id="parcelas" size="38" maxlength="4" class="validate[custom[onlyNumber]]" />
                            </p>
                            <p>
                                <label class='first'>Quota:</label>
                                <input type="text" name="quota" id="quota" size="38" />
                            </p>
                            <p>
                                <label class='first'>Parcela das Quotas:</label>
                                <input type="text" name="parcela_quotas" id="parcela_quotas" size="38" maxlength="4" class="validate[custom[onlyNumber]]" />
                            </p>
                            <p>
                                <label class='first'>Qtd. Máxima de Contratação:</label>
                                <input type="text" name="qtd_contratacao" id="qtd_contratacao" size="38" maxlength="4" class="validate[required,custom[onlyNumber]]" />
                            </p>
                            
                        </div>
                        
                        <div class="clear"></div>                        
                        
                        <p>
                            <label class='first' style="vertical-align: top!important;">Descrição:</label>
                            <textarea name="descricao" id="descricao" rows="5" cols="85"><?php echo $prestador['endereco']?></textarea>
                        </p>                                                                                                  
                        
                    </fieldset>
                    
                    <fieldset id="func2">
                        <legend>Dados do Horário (CLT)</legend>
                        <p>
                            <label class='first'>Nome do Horário:</label>
                            <input type="text" name="nome_horario" id="nome_horario" size="85" class="validate[required]" />
                        </p>
                        <p>
                            <label class='first'>Observações:</label>
                            <input type="text" name="obs" id="obs" size="85" />
                        </p>
                        <p>
                            <label class='first'>Preenchimento:</label>
                            Entrada <input type="text" name="entrada" id="entrada" size="10" class="preenchimento validate[required]" />
                            Saída Almoço <input type="text" name="ida_almoco" id="ida_almoco" size="10" class="preenchimento validate[required]" />
                            Retorno Almoço <input type="text" name="volta_almoco" id="volta_almoco" size="10" class="preenchimento validate[required]" />
                            Saída <input type="text" name="saida" id="saida" size="10" class="preenchimento validate[required]" />
                        </p>
                        
                        <div id="esquerda">
                            <p>
                                <label class='first'>Horas Mês:</label>
                                <input type="text" name="horas_mes" id="horas_mes" size="38" maxlength="4" class="validate[required,custom[onlyNumber]]" />
                            </p>
                            <p>
                                <label class='first'>Dias Mês:</label>
                                <input type="text" name="dias_mes" id="dias_mes" size="38" maxlength="4" class="validate[required,custom[onlyNumber]]" />
                            </p>
                        </div>
                        
                        <div id="direita">
                            <p>
                                <label class='first'>Dias Semana:</label>
                                <input type="text" name="dias_semana" id="dias_semana" size="38" maxlength="4" class="validate[required,custom[onlyNumber]]" />
                            </p>
                            <p>
                                <label class='first'>Folgas:</label>
                                <input type="checkbox" name="folga1" value="1"> Sábado
                                <input type="checkbox" name="folga2" value="2"> Domingo
                                <input type="checkbox" name="folga3" value="5"> Plantonista                            
                            </p>
                        </div>
                    </fieldset>
                
                <p class="controls">
                    <input type="submit" name="cadastrar" id="cadastrar" value="Cadastrar" />
                    <input type="button" name="voltar" id="voltar" value="Voltar" onclick="window.location = 'index.php';" />                    
                </p>
                
                </div><!--form_funcoes-->                             
                
            </form>            
        </div>
    </body>
</html>