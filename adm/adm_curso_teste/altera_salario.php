<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: http://www.netsorrindo.com/intranet/login.php?entre=true");
    exit;
}

include('../../conn.php');
include('../../wfunction.php');
include('../../classes/global.php');
include('../../classes/FolhaClass.php');
include("../../classes_permissoes/acoes.class.php");

$usuario = carregaUsuario();
$master = $usuario['id_master'];
$id_regiao = $usuario['id_regiao'];
$id_usuario = $_COOKIE['logado'];  

$row = getCursosID($_REQUEST['curso']);

$altera_funcao = alteraFuncao($usuario, $id_regiao, $id_usuario);
?>

<html>
    <head>
        <title>:: Intranet :: Funções</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        
        <link href="../../net1.css" rel="stylesheet" type="text/css" />
        <link href="cursos.css" rel="stylesheet" type="text/css" />
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />        
        
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery.maskMoney.js" type="text/javascript" ></script>
        <script src="../../js/global.js" type="text/javascript" ></script>
        <script>
            $(function() {                                
                //mascara                
                $("#salario_novo").maskMoney({prefix:'R$ ', allowNegative: true, thousands:'.', decimal:','});                                                                                                                
                
                //calculo de diferença salarial
                $(".bt-image").click(function() {
                    var antigo = $('#salario_antigo').val();
                    var novo = $('#salario_novo').val().replace('.', '');
                        novo = novo.replace(',', '.');
                    var total = (parseFloat(novo) - parseFloat(antigo)).toFixed(2);
                    
                    /*console.log(antigo);
                    console.log(novo);
                    console.log(total);*/
        
                    $("#diferenca").html(total);
                    $("#difere").val(total);
                    $("#salario_new").val(novo);
                });         
                
                $("#altera_salario").click(function() {                    
                    var novo = $('#salario_novo').val().replace('.', '');
                        novo = novo.replace(',', '.');
                    var data = $("#form2").serialize();
                    
                    if((novo === 0) || (novo === '')){
                        $("#erro").html('<strong>Preencha o Salário Novo</strong>').css({color: "#F00"});
                    }else if($("#difere").val() === ''){
                        $("#erro").html('<strong>Calcule a diferença</strong>').css({color: "#F00"});
                    }else{
                        $.post('edit_curso.php?method=alteraSalario&' + data, null, function(data){
                            if(data.status == 1){
                                $('#textVal').html(data.valor);                                
                                thickBoxClose();
                            }
                        },'json');                        
                    }                    
                });
                
            });
        </script>        
    </head>
    
    <body class="novaintra">                
        
        <form action="" method="post" name="form2" id="form2" autocomplete="off">
            
            <input type="hidden" name="id_curso" id="id_curso" value="<?php echo $row['id_curso']; ?>" />
            <input type="hidden" name="salario_antigo" id="salario_antigo" value="<?php echo $row['salario']; ?>" />
            <input type="hidden" name="salario_new" id="salario_new" value="" />
            <input type="hidden" name="difere" id="difere" value="" />
            
            <div id='erro'></div>
            
            <p>
                <label class='first'>Salário Antigo: <?php echo formataMoeda($row['salario']); ?></label>                
            </p>
            <p>
                <label class='first'>Salário Novo: R$ </label>
                <input type="text" name="salario_novo" id="salario_novo" size="20" />
                <img src="../../imagens/icones/icon-calculator.gif" title="Calcular Diferença" id="calculo_diferenca" class="edita_valor bt-image" />
            </p>
            <p>
                <label class='first'>Diferença:</label>
                R$: <strong id="diferenca"></strong>
            </p>
            <p class="controls">
                <input type="button" name="altera_salario" id="altera_salario" value="Atualizar" />
            </p>
        </form>
        
    </body>
</html>