<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../login.php?entre=true';</script>";
    exit;
}

include "../conn.php";
include "../funcoes.php";
include "../classes/funcionario.php";
include '../classes_permissoes/regioes.class.php';
include '../classes/CalculoFolhaClass.php';
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



// Recebendo a variável criptografada
$enc = $_REQUEST['enc'];
$enc = str_replace("--", "+", $enc);
$link1 = decrypt($enc);
$teste = explode("&", $link1);
$regiao = $teste[0];
$clt = $_REQUEST['clt'];
$projeto = $teste[1];
$pagina_atual = $_REQUEST['pg'];
///MOVIMENTOS DE Débito
$array_horistas = array("5425", "5426", "5512");



/*
  echo $pagina_atual;
  $qr_paginacao = mysql_query("SELECT *
  FROM rh_clt as A
  WHERE A.id_projeto = '$projeto'
  AND (A.status < '60' OR A.status = '200');");
 */


$qr_clt = mysql_query("SELECT A.*,B.nome as funcao, B.id_curso, B.salario ,B.cbo_codigo, F.horas_mes, D.regiao as nome_regiao,E.nome as nome_projeto,A.id_projeto, B.tipo_insalubridade, B.qnt_salminimo_insalu,
                       B.periculosidade_30, F.adicional_noturno, F.horas_noturnas, F.nome as nome_horario, F.id_horario
                        FROM rh_clt as A 
                       LEFT JOIN curso as B ON A.id_curso = B.id_curso
                       LEFT JOIN rhstatus as C ON C.codigo = A.status
                       LEFT JOIN regioes as D ON D.id_regiao = A.id_regiao
                       LEFT JOIN projeto as E ON E.id_projeto = A.id_projeto
                       LEFT JOIN rh_horarios as F ON F.id_horario = A.rh_horario
                       WHERE A.id_clt = $clt");
$row_clt = mysql_fetch_assoc($qr_clt);

if($row_clt['periculosidade_30']){
    $periculosidade = $objCalcFolha->getPericulosidade($row_clt['salario']);
}
$insalubridade = $objCalcFolha->getInsalubridade(30, $row_clt['tipo_insalubridade'], $row_clt['qnt_salminimo_insalu'], date('Y'));

//echo $row_clt['adicional_noturno'];

if($row_clt['adicional_noturno']){
 $baseCalcAdiconal = $row_clt['salario']+ $insalubridade['valor_integral'] + $periculosidade['valor_integral'];   
$adicional_noturno = $objCalcFolha->getAdicionalNoturno($baseCalcAdiconal, $row_clt['horas_mes'], $row_clt['horas_noturnas']);
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
 * Verificação se há folha aberta. caso haja, o mês selecionado será o mês da folha.
 * Alteração feita pq o pessoal do RH estava se confundindo quando o mês virava.
 */
$query = "SELECT mes,ano FROM rh_folha WHERE projeto = '{$projeto}' AND status = 2";
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
$anoSel = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');



///REGIÕES
$regioes = montaQuery('ano_meses', "num_mes,nome_mes", "1");
$optMes = array();
foreach ($regioes as $valor) {
    $optMes[$valor['num_mes']] = $valor['num_mes'] . ' - ' . $valor['nome_mes'];
}
$optMes[13] = '13º Primeira Parcela';
$optMes[14] = '13º Segunda Parcela';
$optMes[15] = '13º Integral';
$optMes[16] = 'Rescisão';



/////////////////////////////////////////////////////////      
/////////////// GRAVAÇÃO NO BANCO DE DADOS///////////////      
/////////////////////////////////////////////////////////      
if (isset($_POST['confirmar'])) {

    $mes = $_POST['mes'];
    $ano = $_POST['ano'];
    $id_regiao = $_POST['regiao'];
    $id_projeto = $_POST['projeto'];
    $id_clt = $_POST['clt'];
    $movimentos = $_POST['mov_valor'];
    $movimentos_sempre = $_POST['mov_sempre'];
    $quant = $_POST['mov_qtd'];
    $tipos = $_POST['tipo_quantidade'];

    $qr_funcao = mysql_query("SELECT B.salario FROM rh_clt as A 
                              INNER JOIN curso as B
                              ON A.id_curso = B.id_curso 
                              WHERE A.id_clt = '$id_clt'");
    $row_funcao = mysql_fetch_assoc($qr_funcao);


    ///PEGANDO AS INFORMAÇÔES DOS MOVIMENTOS
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

        $incidencia = array();
        if (!empty($valor)) {

            $lancamento = ($movimentos_sempre[$id_mov] == 2) ? 2 : 1;
            $incidencia[0] = ($incidencia_inss[$id_mov] == 1) ? '5020' : '';
            $incidencia[1] = ($incidencia_irrf[$id_mov] == 1) ? '5021' : '';
            $incidencia[2] = ($incidencia_fgts[$id_mov] == 1) ? '5023' : '';
            $incidencia = implode(',', $incidencia);
            $valorf = str_replace(',', '.', str_replace('.', '', $valor));
            $tipo_mov = ($tipo_movimento[$id_mov] == 1) ? 'DEBITO' : 'CREDITO';

            ////VERIFICA MOVIMENTO LANÇADO
            /* $qr_verifica = mysql_query("SELECT * FROM rh_movimentos_clt 
              WHERE ((mes_mov = $mes AND ano_mov = $ano) OR (lancamento = 2 AND mes_mov NOT IN(13,14,15,16)))
              AND status = 1  AND id_clt =  $id_clt AND id_mov = $id_mov") or die(mysql_error());
              if(mysql_num_rows($qr_verifica) != 0){
              $row_verifica = mysql_fetch_assoc($qr_verifica);
              $mov_cadastrados[] = $row_verifica['nome_movimento'];
              continue;
              }
             */



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
            
            
            $sql_mov[] = "('$id_clt','$id_regiao','$id_projeto','$mes','$ano','$id_mov','" . $codigo_movimento[$id_mov] . "',
                        '$tipo_mov','" . $nome_movimento[$id_mov] . "',NOW(),'$_COOKIE[logado]','$valorf','" . $percentual_movimento[$id_mov] . "',
                        '$lancamento','$incidencia','$qnt','$tp', '$qnt_horas')";

        }
        unset($incidencia);
    }


    if (sizeof($sql_mov) > 0) {

        $sql_mov = implode(',', $sql_mov);
        mysql_query("INSERT INTO rh_movimentos_clt(id_clt,id_regiao,id_projeto,mes_mov,ano_mov,id_mov,cod_movimento,tipo_movimento,nome_movimento,
                        data_movimento,user_cad,valor_movimento,percent_movimento,lancamento,incidencia,qnt,tipo_qnt, qnt_horas) VALUES
                        $sql_mov");
    }
}


///////////////////////////////////////////////
///////////MOVIMENTOS LANÇADOS/////////////////
///////////////////////////////////////////////
$qr_verifica_mov = mysql_query("SELECT *
             FROM rh_movimentos_clt
             WHERE id_clt = '$clt' AND STATUS = '1' AND lancamento = '2'   
             UNION
             SELECT *
             FROM rh_movimentos_clt
             WHERE id_clt = '$clt' AND STATUS = '1' AND lancamento = '1' ");
while ($row_verifica_mov = mysql_fetch_assoc($qr_verifica_mov)) {

    $tipo_movimento = ($row_verifica_mov['tipo_movimento'] == 'DESCONTO' or $row_verifica_mov['tipo_movimento'] == 'DEBITO') ? 'DEBITO' : 'CREDITO';


    if ($row_verifica_mov['mes_mov'] == 13) {
        $competencia = "13º Primeira Parcela";
    } elseif ($row_verifica_mov['mes_mov'] == 14) {
        $competencia = "13º Segunda Parcela";
    } elseif ($row_verifica_mov['mes_mov'] == 15) {
        $competencia = "13º Integral";
    } elseif ($row_verifica_mov['mes_mov'] == 16) {
        $competencia = "Rescisão";
    } elseif ($row_verifica_mov['lancamento'] == 2) {
        $competencia = "SEMPRE";
    } else {
        $competencia = mesesArray($row_verifica_mov['mes_mov']) . '/' . $row_verifica_mov['ano_mov'];
    }


    $mov_lancado[$tipo_movimento][$row_verifica_mov['id_movimento']]['id_movimento'] = $row_verifica_mov['id_movimento'];
    $mov_lancado[$tipo_movimento][$row_verifica_mov['id_movimento']]['nome'] = $row_verifica_mov['nome_movimento'];
    $mov_lancado[$tipo_movimento][$row_verifica_mov['id_movimento']]['valor'] = $row_verifica_mov['valor_movimento'];
    $mov_lancado[$tipo_movimento][$row_verifica_mov['id_movimento']]['competencia'] = $competencia;
    $mov_lancado[$tipo_movimento][$row_verifica_mov['id_movimento']]['tipo'] = $tipo;
    $mov_lancado[$tipo_movimento][$row_verifica_mov['id_movimento']]['incidencia'] = ($row_verifica_mov['incidencia'] == '5020,5021,5023') ? 'INSS,IRRF,FGTS' : '';
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
                
                $('.hora_mask').mask('99:99');
                
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

                          //  alert('O movimento foi excluído!');   
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
                     var tipo_contagem  = elemento.find('input:checked').val();
                     var valor_hora, valor_dia;
                    if(tipo_contagem == 1 ){    
                      
                      var tempo = quant.split(':');
                      var total_tempo = parseFloat(tempo[0]) + (parseFloat(tempo[1])/60);
                      if(key == 236){
                         valor_hora = parseFloat($('#valor_horaAtraso').val());
                      } else {
                             valor_hora = parseFloat($('#valor_hora').val());
                      }
                      
                        
                      valor_hora = (valor_hora) * total_tempo;
                     
                    var total = valor_hora;
                      
                        
                    }else
                    if(tipo_contagem == 2){
                        
                  if(key == 236){
                         valor_dia = parseFloat($('#valor_diaAtraso').val());
                      } else {
                         valor_dia = parseFloat($('#valor_dia').val());
                      }
                     var total =  valor_dia *quant;
                      
                    }
                    
                     $(".result_" + key).val(total.formatMoney("2",",","."));
                    
                  /* 
                    var valor = $(".v_calculo").val();
                    var total = quant * parseFloat(valor);
                    var key   = $(this).data("key");
                    */
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
//                        //console.log("Valor esta abaixo dos 25%, obrigatório para o auxílio destância");
//                    }
//                    
//                });
                
             
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
                    <p>Gerenciar movimentos de crédito e desconto</p>
                    <p><strong>Nome: </strong> <?php echo $row_clt['nome'] ?></p>
                    <p><strong>Função: </strong> <?php echo $row_clt['id_curso'].' - '.$row_clt['funcao'] ?></p>
                    <p><strong>Horário: </strong> <?php echo $row_clt['id_horario']. ' - '.$row_clt['nome_horario'] ?></p>
                    <p><strong>Região: </strong> <?php echo $row_clt['nome_regiao'] ?></p>
                    <p><strong>Projeto: </strong> <?php echo $row_clt['nome_projeto'] ?></p>
                
                </div>
                
                <div class="fleft">     
                    <br> <br> <br> <br>
                    <p><strong>Salário Contratual: </strong> <?php echo "R$ " . number_format($row_clt['salario'],"2",",","."); ?></p>
                    <p><strong>Insalubridade: </strong> <?php echo 'R$ '.number_format($insalubridade['valor_integral'],2,',','.');?></p>
                    <p><strong>Periculosidade: </strong> <?php echo 'R$ '.number_format($periculosidade['valor_integral'],2,',','.');?></p>
                    <p><strong>Adicional Noturno: </strong> <?php echo 'R$ '.number_format($adicional_noturno['valor_integral'],2,',','.');?></p>
                    <p><strong>DSR: </strong> <?php echo 'R$ '.number_format($dsr['valor_integral'],2,',','.');?></p>              
                    <p><strong>Valor diario: </strong> <?php echo "R$ " . number_format($valor_diario,"2",",","."); ?> <span style="font-style: italic; color:  #cdcdcd"> **Salário + adicionais</span></p>
                    <p><strong>Valor Hora: </strong> <?php echo "R$ " . number_format($valor_hora,"2",",","."); ?> <span style="font-style: italic; color: #cdcdcd"> **Salário + adicionais</span></p>
                    <p><strong>Horas no mês: </strong> <?php echo $row_clt['horas_mes'].' horas';?></p>
                                      
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
                    <legend>COMPETÊNCIA</legend>
                    <div class="fleft">
                        <p><label class="first">Mês:</label>  <?php echo montaSelect($optMes, $mesSel, array('name' => "mes", 'id' => 'mes')); ?>
                            <label class="first">Ano:</label> <?php echo montaSelect($optAnos, $anoSel, array('name' => "ano", 'id' => 'ano')); ?></p>
                    </div>

                    <br class="clear"/>     

                </fieldset>
                <?php
                if (sizeof($mov_cadastrados) > 0) {
                       echo '<div id="message-box" class="message-yellow"><p>O(s) movimento(s) ' . implode(', ', $mov_cadastrados) . ' não foram cadastrados pois
                      já existe para esta competência!</p></div>';
                }
                ?>

                <h3 class="credito" >CRÉDITO</h3>
                <div class="clear"></div> 
                <?php
///MOVIMENTOS DE CRÉDITO
                $qr_mov_cred = mysql_query("SELECT * FROM rh_movimentos WHERE categoria = 'CREDITO' AND mov_lancavel = 1");
                $classAuxDistancia = "";
                while ($row_cred = mysql_fetch_assoc($qr_mov_cred)) {
                    if($row_cred[id_mov] == 193){
                        $classAuxDistancia = "validate[funcCall[auxDistancia]]";
                    }else{
                        $classAuxDistancia = "";
                    }
                    echo '<fieldset class="mov">
                        <legend>' . $row_cred['descicao'] . '</legend>';
                    echo "<div>
                            Valor: <input type='text' name='mov_valor[$row_cred[id_mov]]'  id='mov_valor[$row_cred[id_mov]]'  class='cred {$classAuxDistancia}' rel='$row_cred[id_mov]'/>
                      </div>
                            <div>
                              <span><input type='checkbox' name='mov_sempre[$row_cred[id_mov]]' value='2' rel='$row_cred[id_mov]'/> Sempre </span> 
                            </div>";


                    echo '</fieldset>';
                }
                ?>                    
                <div class="clear"></div>   
                <h3 class="debito">DÉBITO</h3>               
                <?php
                
                $checked = '';
                $i = 0;
                $qr_mov_desc = mysql_query("SELECT * FROM rh_movimentos WHERE (categoria = 'DESCONTO' OR categoria = 'DEBITO') AND mov_lancavel = 1");
                while ($row_desc = mysql_fetch_assoc($qr_mov_desc)) {
                    $campo_qnt = '';
                    $verifica_funcao = mysql_query("SELECT C.id_cbo FROM rh_clt AS A
                    LEFT JOIN curso AS B ON(A.id_curso = B.id_curso)
                    LEFT JOIN rh_cbo AS C ON(B.cbo_codigo = C.id_cbo)
                    WHERE A.id_clt = '{$_REQUEST['clt']}'");
                    while ($rows_funcao = mysql_fetch_assoc($verifica_funcao)) {
                        if ($row_desc['id_mov'] == 232 or $row_desc['id_mov'] == 236) {
                            $i++;
                          
                                $checked1 = 'checked="checked"';
                           
                            
                            
                            
                            $campo_qnt = '<input type="radio" name="tipo_quantidade[' . $row_desc['id_mov'] . ']" value="1" class="tipo_qnt" ' . $checked1 . ' />Hora
                            <input type="radio" name="tipo_quantidade[' . $row_desc['id_mov'] . ']" class="tipo_qnt" value="2"  />Dias |                                
                            Quantidade: <input type="text" name="mov_qtd[' . $row_desc['id_mov'] . ']" class="calculo hora_mask" data-key="'.$row_desc['id_mov'].'" style="width: 50px;" />';                              
                        }
                    }

                    echo '<fieldset class="mov">
                        <legend>' . $row_desc['descicao'] . '</legend>';
                    echo "<div>
                            Valor: <input type='text' name='mov_valor[$row_desc[id_mov]]' class='desc result_$row_desc[id_mov]' rel='rel='$row_cred[id_mov]'/>
                           </div> 
                        <div> <span><input type='checkbox' name='mov_sempre[$row_desc[id_mov]]' value='2' rel='rel='$row_cred[id_mov]''/> Sempre </span></div>  
                         <div>    $campo_qnt</div>
                     ";

                    echo '</fieldset>';
                    unset($campo);
                }
                ?>
                <div class="clear"></div>   
                <input type="hidden" name="clt" value="<?php echo $clt; ?>"/>
                <input type="hidden" name="regiao" value="<?php echo $regiao; ?>"/>
                <input type="hidden" name="projeto" value="<?php echo $row_clt['id_projeto']; ?>"/>
                <div class="botao_enviar"><input type="submit" name="confirmar" value="Confirmar"/></div>
            </form>

            <!-----CREDITO--->            
            <table border="0" cellpadding="0" cellspacing="0" class="grid essatb" width="700" id="tabela" style="font-size: 11px;" align="center">
                <tr>
                    <td colspan="6" style="background-color:  #bbdaf7; text-align: center;font-weight: bold;">MOVIMENTOS DE CRÉDITO</td>
                </tr>
                <tr class="titulo">
                    <td>COD.</td>
                    <td>NOME</td>
                    <td>VALOR</td>
                    <td>COMPETÊNCIA</td>
                    <td>INCIDÊNCIA</td>
                    <td>DELETAR</td>                        
                </tr>
                <?php
                foreach ($mov_lancado['CREDITO'] as $movimentos) {

                    $cor = ($i++ % 2 == 0) ? '#f6f5f5' : '#ecebea';
                    ?> 
                    <tr  style="background-color: <?php echo $cor; ?>">
                        <td><?php echo $movimentos['id_movimento']; ?></td>
                        <td><?php echo $movimentos['nome']; ?></td>
                        <td> R$ <?php echo number_format($movimentos['valor'], 2, ',', '.'); ?></td>
                        <td><?php echo $movimentos['competencia']; ?></td>
                        <td><?php echo $movimentos['incidencia']; ?></td>
                        <td align="center"><a href="#" rel="<?php echo $movimentos['id_movimento']; ?>" class="excluir"><img src="../imagens/deletar_usuario.gif" border="0"></a></td>
                    <tr>                     
                        <?php
                    }
                    ?>
            </table>


            <!-----DEBITO--->  
            <table border="0" cellpadding="0" cellspacing="0" class="grid right" width="700" id="tabela" style="font-size: 11px;" align="center">
                <tr>
                    <td colspan="6" style="background-color:  #e3d8c5   ; text-align: center;font-weight: bold;">MOVIMENTOS DE DÉBITO</td>
                </tr>
                <tr class="titulo">
                    <td>COD.</td>
                    <td>NOME</td>
                    <td>VALOR</td>
                    <td>COMPETÊNCIA</td>
                    <td>INCIDÊNCIA</td>
                    <td>DELETAR</td>                        
                </tr>
                <?php
                foreach ($mov_lancado['DEBITO'] as $movimentos) {

                    $cor = ($i++ % 2 == 0) ? '#f6f5f5' : '#ecebea';
                    ?> 
                    <tr  style="background-color: <?php echo $cor; ?>">
                        <td><?php echo $movimentos['id_movimento']; ?></td>
                        <td><?php echo $movimentos['nome']; ?></td>
                        <td>R$ <?php echo number_format($movimentos['valor'], 2, ',', '.'); ?></td>
                        <td><?php echo $movimentos['competencia']; ?></td>
                        <td><?php echo $movimentos['incidencia']; ?></td>
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