<?php


if($_COOKIE['logado'] == 179){
//    echo "<pre>";
//        print_r($_REQUEST);
//    echo "</pre>";
}

/*ALLMX*/
$cookie = false;
//if($_COOKIE['logado'] == 179){
//    $cookie = true;
//}

if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
    exit;
}
session_start();
include "../conn.php";
include "../funcoes.php";
include "../classes/funcionario.php";
include "../classes/calculos.php";
include '../classes_permissoes/regioes.class.php';
include '../classes/CalculoFolhaClass.php';
include_once('../classes/LogClass.php');
include '../classes/MovimentoClass.php';
include("../wfunction.php");

error_reporting(0);
$Fun = new funcionario();
$Fun->MostraUser(0);
$Master = $Fun->id_master;
$REGIOES = new Regioes();
$objCalcFolha = new Calculo_Folha();
$objCalcFolha->CarregaTabelas(date('Y'));
if (isset($_POST['excluir'])) {
    $id_movimento = $_POST['id_movimento'];
    mysql_query("UPDATE rh_movimentos_clt SET status = '0' WHERE id_movimento = '$id_movimento' LIMIT 1");
    exit;
}



// Recebendo a vari�vel criptografada
$enc = $_REQUEST['enc'];
$encPagina = $enc;
$enc = str_replace("--", "+", $enc);
$link1 = decrypt($enc);
$teste = explode("&", $link1);
//$regiao = $teste[0];

$regiao = $_REQUEST['regiao'];


$clt =$_REQUEST['clt'];
$projeto = $teste[1];
$pagina_atual = $_REQUEST['pg'];
///MOVIMENTOS DE D�bito
$array_horistas = array("5425", "5426", "5512");

$objMovimento = new Movimentos();
$movLancado = $objMovimento->getMovimentosLancadosPorClt($clt);
$objMovimento->carregaMovimentos(date('Y'));

/*ALLMX*/
if($cookie){
    //$calc = new calculos();    
}

/*
  echo $pagina_atual;
  $qr_paginacao = mysql_query("SELECT *
  FROM rh_clt as A
  WHERE A.id_projeto = '$projeto'
  AND (A.status < '60' OR A.status = '200');");
 */


$qr_clt = mysql_query("SELECT A.*,B.nome as funcao, B.id_curso, B.salario ,B.cbo_codigo, D.regiao as nome_regiao,E.nome as nome_projeto,A.id_projeto, B.tipo_insalubridade, B.qnt_salminimo_insalu,
                       B.periculosidade_30, F.adicional_noturno, F.horas_noturnas, F.nome as nome_horario, F.id_horario, F.horas_mes AS hora_mess
                        FROM rh_clt as A 
                       LEFT JOIN curso as B ON A.id_curso = B.id_curso
                       LEFT JOIN rhstatus as C ON C.codigo = A.status
                       LEFT JOIN regioes as D ON D.id_regiao = A.id_regiao
                       LEFT JOIN projeto as E ON E.id_projeto = A.id_projeto
                       LEFT JOIN rh_horarios as F ON F.id_horario = A.rh_horario
                       WHERE A.id_clt = $clt");
$row_clt = mysql_fetch_assoc($qr_clt);

if($row_clt['periculosidade_30']){
    $periculosidade = $objCalcFolha->getPericulosidade($row_clt['salario'],30,12);
}
$insalubridade = $objCalcFolha->getInsalubridade(30, $row_clt['tipo_insalubridade'], $row_clt['qnt_salminimo_insalu'], date('Y'));

//echo $row_clt['adicional_noturno'];

if($row_clt['adicional_noturno']){
    $baseCalcAdiconal = $row_clt['salario']+ $insalubridade['valor_integral'] + $periculosidade['valor_integral'];   
    $adicional_noturno = $objCalcFolha->getAdicionalNoturno($baseCalcAdiconal, $row_clt['hora_mess'], $row_clt['horas_noturnas']);
    $dsr = $objCalcFolha->getDsr($adicional_noturno['valor_integral']);
}


$baseCalc = $row_clt['salario']+ $insalubridade['valor_integral'] + $periculosidade['valor_integral'] + $adicional_noturno['valor_integral'] + $dsr['valor_integral'];
$valor_diario  = ($baseCalc) / 30;
$valor_hora    = ($baseCalc)/$row_clt['horas_mes'];

$baseCalcAtraso =  $row_clt['salario']+ $insalubridade['valor_integral'] + $periculosidade['valor_integral'] ;
$valor_diarioAtraso  = ($baseCalcAtraso) / 30;
$valor_horaAtraso    = ($baseCalcAtraso)/$row_clt['horas_mes'];

$qr_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
$row_user = mysql_fetch_assoc($qr_user);
$id_master = $row_user['id_master'];

/*
 * Verifica��o se h� folha aberta. caso haja, o m�s selecionado ser� o m�s da folha.
 * Altera��o feita pq o pessoal do RH estava se confundindo quando o m�s virava.
 */
$query = "SELECT mes,ano FROM rh_folha WHERE projeto = '{$row_clt['id_projeto']}' AND status = 2";
$mes_folha_aberta = mysql_query($query);
$data_folha = mysql_fetch_assoc($mes_folha_aberta);
$mesSel = "";
if(mysql_num_rows($mes_folha_aberta) > 0){
    $mesSel = $data_folha['mes'];
}else{
    $mesSel = date('m');
}


//ANO
$optAnos = array();
for ($i = 2009; $i <= date('Y'); $i++) {
    $optAnos[$i] = $i;
}

if(mysql_num_rows($mes_folha_aberta) > 0){
    $anoSel = $data_folha['ano'];
}else{
    $mesSel = date('Y');
}



///REGI�ES
$regioes = montaQuery('ano_meses', "num_mes,nome_mes", "1");
$optMes = array();
foreach ($regioes as $valor) {
    $optMes[$valor['num_mes']] = $valor['num_mes'] . ' - ' . $valor['nome_mes'];
}
$optMes[13] = '13� Primeira Parcela';
$optMes[14] = '13� Segunda Parcela';
$optMes[15] = '13� Integral';
$optMes[16] = 'Rescis�o';



/////////////////////////////////////////////////////////      
/////////////// GRAVA��O NO BANCO DE DADOS///////////////      
/////////////////////////////////////////////////////////      
if (isset($_POST['confirmar'])) {
    
    $mes = $_POST['mes'];
    $ano = $_POST['ano'];
    $id_regiao = $_POST['regiao'];
    
    if($_COOKIE['logado'] == 179){
//        echo "<pre>";
//            print_r($_REQUEST);
//        echo "</pre>";
//        exit();
    }
    
    
    $id_projeto         = $_POST['projeto'];
    $id_clt             = $_POST['clt'];
    $movimentos         = $_POST['mov_valor'];
    $movimentos_sempre  = $_POST['mov_sempre'];
    $quant              = $_POST['mov_qtd'];
    $tipos              = $_POST['tipo_quantidade'];
    $parcela            = $_POST['mov_parc'];
    $obs                = $_POST['obs'];
    $obs2               = $_POST['obs2'];
    
    /*ALLMX*/
    if($cookie){
//        $mes_final = $mes;
//        $calculo = $calc->Calc_qnt_meses_13_ferias('2015-'.$mes.'-01', '2015-'.$mes_final.'-30');
//        
//        echo "<pre>";
//        print_r($calculo);
//        echo "</pre>";                   
    }
    
    $qr_funcao = mysql_query("SELECT B.salario FROM rh_clt as A 
                              INNER JOIN curso as B
                              ON A.id_curso = B.id_curso 
                              WHERE A.id_clt = '$id_clt'");
    $row_funcao = mysql_fetch_assoc($qr_funcao);

    ///PEGANDO AS INFORMA��ES DOS MOVIMENTOS
    $qr_mov = mysql_query("SELECT * FROM rh_movimentos WHERE mov_lancavel = 1");
    while ($row_mov = mysql_fetch_assoc($qr_mov)) {
        $codigo_movimento[$row_mov['id_mov']] = $row_mov['cod'];
        $tipo_movimento[$row_mov['id_mov']] = ($row_mov['categoria'] == 'DESCONTO' or $row_mov['categoria'] == 'DEBITO') ? 1 : 2;
        $nome_movimento[$row_mov['id_mov']] = $row_mov['descicao'];
        $percentual_movimento[$row_mov['id_mov']] = $row_mov['percentual'];
        $incidencia_inss[$row_mov['id_mov']] = $row_mov['incidencia_inss'];
        $incidencia_irrf[$row_mov['id_mov']] = $row_mov['incidencia_irrf'];
        $incidencia_fgts[$row_mov['id_mov']] = $row_mov['incidencia_fgts'];
    }

    foreach ($movimentos as $id_mov => $valor) {
        
        //echo $mes = $_POST['mes']; die();

        $incidencia = array();
        if (!empty($valor) and $valor != '0,00') {

            $lancamento = ($movimentos_sempre[$id_mov] == 2) ? 2 : 1;
            $incidencia[0] = ($incidencia_inss[$id_mov] == 1) ? '5020' : '';
            $incidencia[1] = ($incidencia_irrf[$id_mov] == 1) ? '5021' : '';
            $incidencia[2] = ($incidencia_fgts[$id_mov] == 1) ? '5023' : '';
            $incidencia = implode(',', $incidencia);
            $valorf = str_replace(',', '.', str_replace('.', '', $valor));
            $tipo_mov = ($tipo_movimento[$id_mov] == 1) ? 'DEBITO' : 'CREDITO';
//      echo "<pre>
//            SELECT * FROM rh_movimentos_clt 
//            WHERE 
//            ((mes_mov = $mes AND ano_mov = $ano) OR (lancamento = 2 AND mes_mov NOT IN(13,14,15,16)))
//            AND status = 1 AND id_clt = $id_clt AND id_mov = $id_mov</pre>";
            ////VERIFICA MOVIMENTO LAN�ADO
            $qr_verifica = "SELECT * FROM rh_movimentos_clt WHERE ((mes_mov = '{$mes}' AND ano_mov = '{$ano}') OR (lancamento = '2' AND mes_mov NOT IN(13,14,15,16))) AND status = '1' AND id_clt = '{$id_clt}' AND id_mov = '{$id_mov}'";
            mysql_query($qr_verifica) or die("Erro ao selecionar movimentos para o Clt");
            
            if($_COOKIE['logado'] == 179){
//                echo "<pre>";
//                    print_r($qr_verifica);
//                echo "</pre>";

            }
      
            if(mysql_num_rows($qr_verifica) != 0){
                $row_verifica = mysql_fetch_assoc($qr_verifica);
                $mov_cadastrados[$id_mov] = $row_verifica['nome_movimento'];
            } else {
             



                // VERIFICANDO SE O VALOR DA AJUDA DE CUSTO PASSA DE 50% DO SALARIO DO CARA, PARA COLOCAR INCIDENCIA EM INSS,IRRF,FGTS
                if ($id_mov == 13) {
                    $metade = $row_funcao['salario'] / 2;
                    if ($valor > $metade) {
                        $incidencia = "5020,5021,5023";
                    }
                }





                $tp = (isset($tipos[$id_mov])) ? $tipos[$id_mov]:"(NULL)";

                if(isset($quant[$id_mov])) {

                    if($tp == 1){
                        $qnt_horas = $quant[$id_mov];
                        $qnt = '';
                    } else {
                        $qnt = $quant[$id_mov];
                        $qnt_horas = '';
                    }


                } else {
                    $qnt = "(NULL)";
                }
                
                $obs = $obs . $obs2;

                $sql_mov[] = "('$id_clt','$id_regiao','$id_projeto','$mes','$ano','$id_mov','" . $codigo_movimento[$id_mov] . "',
                            '$tipo_mov','" . $nome_movimento[$id_mov] . "',NOW(),'$_COOKIE[logado]','$valorf','" . $percentual_movimento[$id_mov] . "',
                            '$lancamento','$incidencia','$qnt','$tp', '$qnt_horas','$obs')";
            }
        }
        unset($incidencia);
        
    }
    
    /*ALLMX*/
//    if($cookie){
//        exit();
//    }
                  
    if (sizeof($sql_mov) > 0) {
        $_SESSION['mov_cadastrados'] = $mov_cadastrados;
        $sql_mov = implode(',', $sql_mov);
        
        /*ALLMX*/
        if($cookie){
//            echo "<pre>";
//                print_r("INSERT INTO rh_movimentos_clt(id_clt,id_regiao,id_projeto,mes_mov,ano_mov,id_mov,cod_movimento,tipo_movimento,nome_movimento,
//                        data_movimento,user_cad,valor_movimento,percent_movimento,lancamento,incidencia,qnt,tipo_qnt, qnt_horas) VALUES
//                        $sql_mov");
//            echo "</pre>";
            die();
        }
        
        mysql_query("INSERT INTO rh_movimentos_clt(id_clt,id_regiao,id_projeto,mes_mov,ano_mov,id_mov,cod_movimento,tipo_movimento,nome_movimento,
                        data_movimento,user_cad,valor_movimento,percent_movimento,lancamento,incidencia,qnt,tipo_qnt, qnt_horas, obs) VALUES
                        $sql_mov");                
    }
    
     header('Location: rh_movimentos_1.php?tela=2&pg=0&clt='.$id_clt.'&enc='.$encPagina);
    exit;
    
}
?>
<html>
    <head>
        <title>Movimentos</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link href="../net1.css" rel="stylesheet" type="text/css">
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../js/highslide-with-html.js" type="text/javascript"></script>
        <script src="../jquery/priceFormat.js" type="text/javascript"></script>
        <script src="../js/global.js" type="text/javascript"></script>
        <script src="../jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine_2.6.2.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script>
            
            
            $(function(){
                
                $('.sonumeros').keypress(function (event) {
                    var tecla = (window.event) ? event.keyCode : event.which;
                    if ((tecla > 47 && tecla < 58))
                        return true;
                    else {
                        if (tecla != 8)
                            return false;
                        else
                            return true;
                    }
                });
                
                $('.hora_mask').mask("999:99");
                
                $('#form').validationEngine();
                
                $('.cred,.desc').priceFormat({
                    prefix: '',
                    centsSeparator: ',',
                    thousandsSeparator: '.'
                });

                $('.mov').click(function(){                    
                    $('.mov').css('border-color','#E2E2E2')
                    $(this).css('border-color','#9bd4f8')                    
                })               
                
                $('.excluir').click(function(){
                    
                  
                    var id_movimento = $(this).attr('rel');
                    var linha = $(this).parent().parent();
                    
                    if(confirm("Excluir este movimento?")){
                        $.post('rh_movimentos_1.php',{ excluir : 1, id_movimento: id_movimento },
                        function(data){

                          //  alert('O movimento foi exclu�do!');   
                            linha.fadeOut();
                        })
                    }
                    return false;
                })
                
                
                $('.tipo_qnt').change(function(){
                    
                    var elemento = $(this);
                    var div =  elemento.parent().parent().find('.calculo');
                    if(elemento.val() == 1){
                        div.mask('99:99')
                        div.val('');
                    }else if(elemento.val() == 2){
                        div.unmask('99:99')
                        div.val('');
                    }
                    
                });
                
                
               
                
                $(".calculo").change(function(){
                    var quant          = $(this).val();
                    var elemento       = $(this).parent().parent();
                    var key            = $(this).data("key");
                    var tipo_contagem  = elemento.find('.tipo_qnt').val();
                    var id_clt = $('#clt').val(); 
                    
                    console.log("quant:"+quant+", elemento:"+elemento+", key:"+key+", tipo_qnt:"+tipo_contagem+", id_clt:"+id_clt);
                    
                    $.post('action_calcula_movimento.php', {id_clt: id_clt, id_mov :key, tipo_qnt: tipo_contagem, qnt: quant  }, function(data){
                        console.log(data);
                        $(".result_" + key).val(parseFloat(data).formatMoney("2",",","."));
                    });
                });
                
//                $(".aux-distancia").blur(function(){
//                    var salbase = $("#salario_base").val();
//                    var auxDistancia = $(this).val();
//                    var minAuxilio = salbase * 0.25;
//                    minAuxilio = minAuxilio.toFixed(2);
//                    auxDistancia = auxDistancia.replace(".", "");
//                    auxDistancia = auxDistancia.replace(",", ".");
//                   
//                    if(minAuxilio < auxDistancia){
//                        $(".aux-distancia").parents("fieldset").css({background:"#999"});
//                        //console.log("Valor esta abaixo dos 25%, obrigat�rio para o aux�lio dest�ncia");
//                    }
//                    
//                });        
                
                $(".result_60").click(function(){
                    $("#tooltip_mov").html("\
                        <a href='#' class='tooltip'>\n\
                            Tooltip\n\
                            <span>\n\
                                <img class='callout' src='../imagens/callout.gif' />\n\
                                <strong>Most Light-weight Tooltip</strong><br />\n\
                                This is the easy-to-use Tooltip driven purely by CSS.\n\
                            </span>\n\
                        </a>"                        
                    );
                });
                
            });
            
            function auxDistancia(fiel, rules, i, options){
                var salbase = $("#salario_base").val();
                var auxDistancia = fiel.val();
                var minAuxilio = salbase * 0.25;
                minAuxilio = minAuxilio.toFixed(2);
                auxDistancia = auxDistancia.replace(".", "");
                auxDistancia = auxDistancia.replace(",", ".");
                if(parseFloat(auxDistancia) < parseFloat(minAuxilio)){
                    return options.allrules.auxDistancia.alertText;
                }
            }
            
        </script>
        <style>
            fieldset.mov{ border: 1px solid  #666;
                          width: 300px;
                          float:left;
                          margin-left: 10px;
                          margin-top: 15px;
                          padding-left: 20px !important;
            }
            h3.credito{ text-align: center;
                        background-color:  #c8e5f8;
            }           
            h3.debito{ text-align: center;
                       background-color:      #e3d8c5;
            }
            .botao_enviar{ text-align: center; 
                           border-top: 1px solid  #edeae4;
                           border-bottom: 1px solid  #edeae4;
                           padding-top: 5px;
                           padding-bottom: 5px;} 

            input[name="mov_desc_qnt"]{  width: 50px;}
            .grid{ float: left;
                   margin-top: 30px;
                   margin-bottom: 30px;}
            .right{ float: right;}
            
            a.tooltip {
                outline:none; 
                text-align: left
            }
            a.tooltip strong {
                line-height:30px;
            }
            a.tooltip:hover {
                text-decoration:none;
            } 
            a.tooltip span {
                z-index:10;
                display:none; 
                padding:14px 20px;
                margin-top:-30px; 
                margin-left:28px;
                width:200px; 
                line-height:16px;
            }
            a.tooltip:hover span{
                display:inline; 
                position:absolute; 
                color:#111;
                border:1px solid #DCA; 
                background:#fffAF0;
            }
            .callout {
                z-index:20;
                position:absolute;
                top:30px;
                border:0;
                left:-12px;
            }

            /*CSS3 extras*/
            a.tooltip span
            {
                border-radius:4px;
                box-shadow: 5px 5px 8px #CCC;
            }

        </style>

    </head>
    <body class="novaintra">

        <div id="content">
            <div  style="margin-bottom:20px; text-align: left;">
                <a href="rh_movimentos.php?regiao=<?php echo $regiao ?>&amp;tela=1">    
                    <img src="../imagens/voltar.gif" border="0">
                </a>
            </div>    
            <div id="head">

                <img src="../imagens/logomaster<?php echo $id_master; ?>.gif" class="fleft" style="margin-right: 25px;" width="140" height="100"/>               
                
                <div class="fleft" style="width: 450px;">
                    <h2>Movimentos</h2>
                    <p>Gerenciar movimentos de cr�dito e desconto</p>
                    <p><strong>Nome: </strong> <?php echo $row_clt['nome'] ?></p>
                    <p><strong>Fun��o: </strong> <?php echo $row_clt['id_curso'].' - '.$row_clt['funcao'] ?></p>
                    <p><strong>Hor�rio: </strong> <?php echo $row_clt['id_horario']. ' - '.$row_clt['nome_horario'] ?></p>
                    <p><strong>Regi�o: </strong> <?php echo $row_clt['nome_regiao'] ?></p>
                    <p><strong>Projeto: </strong> <?php echo $row_clt['nome_projeto'] ?></p>
                
                </div>
                
                <div class="fleft">     
                    <br> <br> <br> <br>
<!--                    <p><strong>Sal�rio Contratual: </strong> <?php echo "R$ " . number_format($row_clt['salario'],"2",",","."); ?></p>
                    <p><strong>Insalubridade: </strong> <?php echo 'R$ '.number_format($insalubridade['valor_integral'],2,',','.');?></p>
                    <p><strong>Periculosidade: </strong> <?php echo 'R$ '.number_format($periculosidade['valor_integral'],2,',','.');?></p>
                    <p><strong>Adicional Noturno: </strong> <?php echo 'R$ '.number_format($adicional_noturno['valor_integral'],2,',','.');?></p>
                    <p><strong>DSR: </strong> <?php echo 'R$ '.number_format($dsr['valor_integral'],2,',','.');?></p>              
                    <p><strong>Valor diario: </strong> <?php echo "R$ " . number_format($valor_diario,"2",",","."); ?> <span style="font-style: italic; color:  #cdcdcd"> **Sal�rio + adicionais</span></p>
                    <p><strong>Valor Hora: </strong> <?php echo "R$ " . number_format($valor_hora,"2",",","."); ?> <span style="font-style: italic; color: #cdcdcd"> **Sal�rio + adicionais</span></p>
                    <p><strong>Horas no m�s: </strong> <?php echo $row_clt['horas_mes'].' horas';?></p>
                                      -->
                    <input type="hidden" name="salario_base" id="salario_base" value="<?php echo $row_clt['salario']; ?>" />
                    <input type="hidden" name="valor_hora" id="valor_hora" value="<?php echo $valor_hora; ?>" />                    
                    <input type="hidden" name="valor_dia" id="valor_dia" value="<?php echo $valor_diario; ?>" />        
                    <input type="hidden" name="valor_horaAtraso" id="valor_horaAtraso" value="<?php echo $valor_horaAtraso; ?>" />                    
                    <input type="hidden" name="valor_diaAtraso" id="valor_diaAtraso" value="<?php echo $valor_diarioAtraso; ?>" />        
                 </div>

            </div>  
            <br class="clear">
            <br/>

            <form  name="form" action="" method="post" id="form">
                <fieldset>
                    <legend>COMPET�NCIA</legend>
                    <div class="fleft">
                        <p><label class="first">M�s:</label>  <?php echo montaSelect($optMes, $mesSel, array('name' => "mes", 'id' => 'mes')); ?>
                            <label class="first">Ano:</label> <?php echo montaSelect($optAnos, $anoSel, array('name' => "ano", 'id' => 'ano')); ?></p>
                    </div>

                    <br class="clear"/>     

                </fieldset>
                <?php
                if (sizeof($_SESSION['mov_cadastrados']) > 0) {
                    echo '<div id="message-box" class="message-yellow"><p>O(s) movimento(s) ' . implode(', ', $_SESSION['mov_cadastrados']) . ' n�o foram cadastrados pois
                    j� existe para esta compet�ncia!</p></div>';
                    $_SESSION['mov_cadastrados'] = null;
                }                     
                
                /*ALLMX*/
                // NUMERO DE PARCELAS                
                if($cookie){
                    $colspan = 6;
                }else{
                    $colspan = 5;
                }
                
                ?>    
                  <!-----CREDITO--->   
                <div class="clear"></div> 
                <table width="48%" align="center" cellpadding="0" cellspacing="0" border="0" class="grid">
                        <thead class="titulo" >
                            <tr style="background-color:   #8cc3f2">
                                <th colspan="<?php echo $colspan; ?>">CR�DITO</th>
                            </tr>
                        </thead>
                        <thead class="titulo" >
                            <tr style="background-color: #8cc3f2">
                                <th>Movimento</th>
                                <th>Valor</th>
                                <th>Sempre</th>
                                <th>Tipo</th>
                                <th>Quantidade</th>
                                
                                <?php if($cookie){ /*ALLMX*/ ?>
                                <th>N� Parcelas</th>
                                <?php } ?>
                            </tr>
                        </thead>                      
                            <?php
                            ///MOVIMENTOS DE CR�DITO
                            $qr_mov = mysql_query("SELECT * FROM rh_movimentos WHERE categoria = 'CREDITO' AND mov_lancavel = 1 ORDER BY descicao");              
                            while ($row_mov = mysql_fetch_assoc($qr_mov)) {                    
                          // $classAuxDistancia = ($row_mov[id_mov] == 193)?"validate[funcCall[auxDistancia]]" :'' ;    
                           
                                    if ($row_mov['tipo_qnt_lancavel']) {
                                        $i++; $checked1 = 'checked="checked"';
                                       $campoTipoQnt = "<select name='tipo_quantidade[{$row_mov['id_mov']}]' class='tipo_qnt'>
                                                            <option value='1'>Horas</option>
                                                            <option value='2'>Dias</option>
                                                         </select>  ";
                                        $campoQnt = '<input type="text" name="mov_qtd['.$row_mov['id_mov'] . ']" class="calculo hora_mask" data-key="'.$row_mov['id_mov'].'" style="width: 50px;" />';                              
                                    } else {
                                        $campoTipoQnt = '';
                                        $campoQnt = '';
                                    }
                                    
                                    if ($row_mov['parcelamento']) {
                                        $campoParcelamento = '<input type="text" name="mov_parc['.$row_mov['id_mov'] . ']" class="sonumeros" data-key="'.$row_mov['id_mov'].'" style="width: 50px;" />';
                                    }else{
                                        $campoParcelamento = '';
                                    }
                           
                           ?>  
                        <tr>
                         <td><?php echo $row_mov['cod'].' -  '.$row_mov['descicao']; ?></td>
                            <td align="center"><input type='text' size="5" name='mov_valor[<?php echo $row_mov['id_mov']?>]'  id='mov_valor[<?php echo $row_mov[id_mov]?>]'  class='cred <?php echo $classAuxDistancia?> result_<?=$row_mov[id_mov]?>' rel='<?php echo $row_mov[id_mov]?>'/></td>
                            <td align="center"><input type='checkbox' name='mov_sempre[<?php echo $row_mov['id_mov']?>]' value='2' rel='<?php echo $row_mov[id_mov]?>'/></td>
                           <td align="center"><?php echo $campoTipoQnt; ?></td>
                           <td align="center"><?php echo $campoQnt; ?></td>                           
                           <?php if($cookie){ /*ALLMX*/?>
                           <td align="center"><?php echo $campoParcelamento; ?></td>
                           <?php } ?>
                        </tr>
                            <?php   
                           }
                           ?>
                </table>  
                <!-----DEBITO --->
                <table width="48%" align="center" cellpadding="0" cellspacing="0" border="0" class="grid" style="margin-left: 10px;">
                    <thead class="titulo">
                        <tr style="background-color:    #eca287">
                            <th colspan="<?php echo $colspan; ?>">D�BITO</th>
                        </tr>

                    </thead>
                    <thead class="titulo">
                        <tr style="background-color:    #eca287">
                            <th>Movimento</th>
                            <th>Valor</th>
                            <th>Sempre</th>
                            <th>Tipo</th>
                            <th>Quantidade</th>
                            <?php if($cookie){ /*ALLMX*/?>
                            <th>N� Parcelas</th>
                            <?php } ?>
                        </tr>
                    </thead>
                   <?php                
                   $qr_mov = mysql_query("SELECT * FROM rh_movimentos WHERE categoria = 'DEBITO' AND mov_lancavel = 1 ORDER BY descicao");
                   while ($row_mov = mysql_fetch_assoc($qr_mov)) {                    

                       if ($row_mov['tipo_qnt_lancavel']) {
                           $i++; $checked1 = 'checked="checked"';
                          $campoTipoQnt = "<select name='tipo_quantidade[{$row_mov['id_mov']}]' class='tipo_qnt'>
                                               <option value='1'>Horas</option>
                                               <option value='2'>Dias</option>
                                            </select>  ";
                           $campoQnt = '<input type="text" name="mov_qtd['.$row_mov['id_mov'] . ']" class="calculo" data-key="'.$row_mov['id_mov'].'" style="width: 50px;" />';                              
                       } else {
                           $campoTipoQnt = '';
                           $campoQnt = '';
                       }                    
                       
                        if ($row_mov['parcelamento']) {
                            $campoParcelamento = '<input type="text" name="mov_parc['.$row_mov['id_mov'] . ']" id="mov_parc['.$row_mov[id_mov].']" class="sonumeros" maxlength="2" data-key="'.$row_mov['id_mov'].'" style="width: 50px;" />';
                        }else{
                            $campoParcelamento = '';
                        }
                  ?>                                                                                    
                    
                       <tr>
                          <td><?php echo $row_mov['cod'].' -  '.$row_mov['descicao']; ?></td>
                           <td align="center">
                                <?php if($row_mov['id_mov'] == 60){ ?>
                                <a href="#" class="tooltip">
                                    <input type='text' size="5" name='mov_valor[<?php echo $row_mov['id_mov']?>]'  id='mov_valor[<?php echo $row_mov[id_mov]?>]'  class='desc result_<?php echo $row_mov[id_mov]?>' rel='<?php echo $row_mov[id_mov]?>'/>
                                    <span>
                                        <img class="callout" src="../imagens/callout.gif" />
                                        <strong>Sugerimos que o valor da parcela</strong><br />
                                        N�o ultrapasse 30% do valor total
                                    </span>
                                </a>
                                <?php }else{ ?>
                                <input type='text' size="5" name='mov_valor[<?php echo $row_mov['id_mov']?>]'  id='mov_valor[<?php echo $row_mov[id_mov]?>]'  class='desc result_<?php echo $row_mov[id_mov]?>' rel='<?php echo $row_mov[id_mov]?>'/>
                                <?php if($row_mov['id_mov'] == 232){ ?>
                                    <br /><label for="obs" style="float: left; margin-left: 16px; margin-top: 10px;">Dias Referentes a Falta </label><br />   
                                    <textarea name="obs" style="margin: 0px; width: 176px; max-width: 176px; min-width: 176px; height: 52px; max-height: 52px; min-height: 52px; "></textarea>
                                <?php } ?>
                                <?php if($row_mov['id_mov'] == 293){ ?>
                                    <br /><label for="obs2" style="float: left; margin-left: 16px; margin-top: 10px;">Dias Referentes a Falta </label><br />   
                                    <textarea name="obs2" style="margin: 0px; width: 176px; max-width: 176px; min-width: 176px; height: 52px; max-height: 52px; min-height: 52px; "></textarea>
                                <?php } ?>
                                <?php } ?>
                           </td>
                           <td align="center"><input type='checkbox' name='mov_sempre[<?php echo $row_mov['id_mov']?>]' value='2' rel='<?php echo $row_mov[id_mov]?>'/></td>
                           <td align="center"><?php echo $campoTipoQnt; ?></td>
                           <td align="center"><?php echo $campoQnt; ?></td>
                           <?php if($cookie){ /*ALLMX*/?>
                           <td align="center"><?php echo $campoParcelamento; ?></td>
                           <?php } ?>
                       </tr>
                    <?php   
                   }
                   ?>
                </table>
                
                
                <div class="clear"></div>   
                <input type="hidden" name="clt"  id="clt" value="<?php echo $clt; ?>"/>
                <input type="hidden" name="regiao" value="<?php echo $regiao; ?>"/>
                <input type="hidden" name="projeto" value="<?php echo $row_clt['id_projeto']; ?>"/>
                <div class="botao_enviar"><input type="submit" name="confirmar" value="Confirmar"/></div>
            </form>

            <!-----CREDITO--->            
            <table border="0" cellpadding="0" cellspacing="0" class="grid essatb" width="48%" id="tabela" style="font-size: 11px;" align="center">
                <tr>
                    <td colspan="7" style="background-color:  #bbdaf7; text-align: center;font-weight: bold;">MOVIMENTOS DE CR�DITO</td>
                </tr>
                <tr class="titulo">
                    <td>COD.</td>
                    <td>NOME</td>
                    <td>QUANT.</td>
                    <td>VALOR</td>
                    <td>COMPET�NCIA</td>
                    <td>INCID�NCIA</td>
                    <td>DELETAR</td>                        
                </tr>
                <?php
                foreach ($movLancado['CREDITO'] as $movimentos) {

                    $cor = ($i++ % 2 == 0) ? '#f6f5f5' : '#ecebea';
                    ?> 
                    <tr  style="background-color: <?php echo $cor; ?>">
                        <td align="center"><?php echo $movimentos['id_movimento']; ?></td>
                        <td><?php echo $movimentos['nome']; ?></td>
                         <td align="center"><?php echo $movimentos['qnt'].' '.$movimentos['tipo_qnt']; ?></td>
                        <td align="center"> R$ <?php echo number_format($movimentos['valor'], 2, ',', '.'); ?></td>
                        <td align="center"><?php echo $movimentos['competencia']; ?></td>
                        <td align="center"><?php echo $movimentos['incidencia']; ?></td>
                        <td align="center"><a href="#" rel="<?php echo $movimentos['id_movimento']; ?>" class="excluir"><img src="../imagens/deletar_usuario.gif" border="0"></a></td>
                    <tr>                     
                        <?php
                    }
                    ?>
            </table>


            <!-----DEBITO--->  
            <table border="0" cellpadding="0" cellspacing="0" class="grid right" width="48%" id="tabela" style="font-size: 11px;" align="center">
                <tr>
                    <td colspan="8" style="background-color:  #e3d8c5   ; text-align: center;font-weight: bold;">MOVIMENTOS DE D�BITO</td>
                </tr>
                <tr class="titulo">
                    <td>COD.</td>
                    <td>NOME</td>
                    <td>QUANT.</td>
                    <td>VALOR</td>
                    <td>COMPET�NCIA</td>
                    <td>INCID�NCIA</td>
                    <td>Obs</td>
                    <td>DELETAR</td>                        
                </tr>
                <?php
                foreach ($movLancado['DEBITO'] as $movimentos) {

                    $cor = ($i++ % 2 == 0) ? '#f6f5f5' : '#ecebea';
                    ?> 
                    <tr  style="background-color: <?php echo $cor; ?>">
                        <td align="center"><?php echo $movimentos['id_movimento']; ?></td>
                        <td><?php echo $movimentos['nome']; ?></td>
                        <td align="center"><?php echo $movimentos['qnt'].' '.$movimentos['tipo_qnt']; ?></td>
                        <td>R$ <?php echo number_format($movimentos['valor'], 2, ',', '.'); ?></td>
                        <td align="center"><?php echo $movimentos['competencia']; ?></td>
                        <td align="center"><?php echo $movimentos['incidencia']; ?></td>
                        <td align="center"><?php echo $movimentos['obs']; ?></td>
                        <td align="center"><a href="#" rel="<?php echo $movimentos['id_movimento']; ?>" class="excluir"><img src="../imagens/deletar_usuario.gif" border="0"></a></td>
                    <tr>                     
                        <?php
                    }
                    ?>
            </table>
            <div class="clear"></div>      
        </div>
    </div>
</body>
</html>