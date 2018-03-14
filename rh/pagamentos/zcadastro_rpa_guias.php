<?php
include('../../conn.php');
include("../../wfunction.php");
include("../../funcoes.php");

$id_rpa         = $_REQUEST['id_rpa'];
$tipo_guia      = $_REQUEST['tipo_guia']; /// TIPO: 2- GPS, 3 - IR
$id_autonomo    = $_REQUEST['id_autonomo'];
$usuario = carregaUsuario();

$qr_rpa = mysql_query("SELECT A.id_rpa, B.nome, B.id_regiao, B.id_projeto, A.mes_competencia,  A.ano_competencia, A.valor_inss, A.valor_liquido, A.valor_ir
                        FROM rpa_autonomo  as A
                        INNER JOIN autonomo as B
                        ON A.id_autonomo = B.id_autonomo                   
                        WHERE A.id_rpa = $id_rpa;");

$row_rpa    = mysql_fetch_assoc($qr_rpa);
$regiao     = $row_rpa['id_regiao'];
$projeto    = $row_rpa['id_projeto'];
$mes        = $row_rpa['mes_competencia'];
$ano        = $row_rpa['ano_competencia'];

$mes_consulta= $_REQUEST['mes_consulta'];
$ano_consulta= $_REQUEST['ano_consulta'];



$nomeTipo   = array("1"=> "RPA", 2=>"GPS","3"=>"IR");



switch($tipo_guia){    
      case 1:
        $texto = "RPA";
        $tipo_saida = 260;       
        $subgrupo = 41;
         $valor = $row_rpa['valor_liquido'];
    break;
    case 2:
        $texto = "GPS";
        $tipo_saida = 260;       
        $subgrupo = 41;
        $valor = $row_rpa['valor_inss'];
    break;
    case 3:
        $texto = "IR";
        $tipo_saida = 260;      
        $subgrupo = 41;
        $valor = $row_rpa['valor_ir'];
    break;    
}

$query_banco = mysql_query("SELECT id_banco FROM bancos WHERE id_regiao = '{$regiao}' AND id_projeto = '{$projeto}' ");
$banco = @mysql_result($query_banco, 0);
if ($banco == 0) {
    echo "ESSE PROJETO NÃO TEM BANCO.";
    exit;
}





$regioes  = mysql_result(mysql_query(" SELECT  GROUP_CONCAT(id_regiao) as regioes FROM regioes WHERE id_master = (SELECT id_master FROM regioes WHERE id_regiao = {$regiao});"), 0);
$nome_mes = mesesArray($mes);

$query_regiao = mysql_query("SELECT regiao FROM regioes WHERE id_regiao = '{$regiao}'");
$nome_regiao  = @mysql_result($query_regiao, 0);

$nome_completo = "RPA - ".htmlentities($row_rpa[nome])." - $texto ".  mesesArray($mes).'/'.$ano;

  
if(isset($_REQUEST['acao'])){

        $id_rpa           = $_POST['id_rpa'];
        $id_autonomo      = $_POST['id_autonomo'];
        $tipo_saida       = $_POST['tipo_saida'];
        $subgrupo         = $_POST['subgrupo'];
        $valor            = str_replace('.','',$_POST['valor']);
        $data             = implode('-',array_reverse(explode('/',$_POST['data'])));
        $banco            = $_POST['bancos'];
        $cod_barra_gerais = ($_POST['cod_barra_gerais'] != 'NaN') ? $_POST['cod_barra_gerias']:'';
        $tipo_guia        = $_POST['tipo_guia'];
        $arquivo          = $_FILES['arquivo']; 
                          
        $qr_rpa = mysql_query("SELECT A.id_rpa, A.id_autonomo, A.valor_liquido , C.nome as nome_projeto, D.regiao as nome_regiao
                               FROM rpa_autonomo as A
                               INNER JOIN  autonomo as B
                               ON A.id_autonomo = B.id_autonomo
                               INNER JOIN projeto as C
                               ON B.id_projeto = C.id_projeto
                               INNER JOIN  regioes as D
                               ON D.id_regiao = B.id_regiao
                               WHERE A.id_rpa = $id_rpa") or die (mysql_error());
        $row_rpa = mysql_fetch_assoc($qr_rpa);
        
        //DADOS DO BANCO
        $qr_banco  = mysql_query("SELECT * FROM bancos WHERE id_banco = $banco");
        $row_banco = mysql_fetch_assoc($qr_banco);

        if($data == ''){
            echo 'DATA NÃO PODE SER VAZIA';
            return false;
        }elseif($banco == ''){
            echo 'SELECIONE UM BANCO';
            return false;
        }elseif($valor == '') {
            echo 'VALOR NÃO PODE SER VAZIO';
            return false;
        }elseif($row_banco['id_projeto'] == ''){
            echo 'ERRO NO CADASTRO DO ID DO PROJETO';
            return false;
        }                
        
        $especifica = $nome_completo.' - PROJETO: '.$row_rpa['nome_projeto'].' REGIÃO: '.$row_rpa['nome_regiao'];

        $sql = "INSERT INTO saida (id_regiao, id_projeto, id_banco, id_user, nome, id_nome, especifica, tipo, adicional, valor, data_proc, data_vencimento, status,comprovante, nosso_numero, tipo_boleto, cod_barra_gerais, id_referencia, id_tipo_pag_saida, entradaesaida_subgrupo_id, mes_competencia, ano_competencia,id_autonomo)
                VALUES ('$row_banco[id_regiao]', '$row_banco[id_projeto]', '$banco', '$_COOKIE[logado]', '$nome_completo','', '$especifica', '$tipo_saida', '', '$valor',NOW(), '$data',  '1', '2', '$nosso_numero', '2', '$cod_barra_gerais','1', '1', '$subgrupo', '$row_rpa[mes_competencia]', '$row_rpa[ano_competencia]','$id_autonomo') ";

        
      
        if(mysql_query($sql)){
            
            $id_saida = mysql_insert_id();
            
           ///ASSOCIANDO O RPA A SAÍDA
            $qr_rpa_assoc = mysql_query("INSERT INTO rpa_saida_assoc (id_rpa, id_saida, tipo_vinculo) VALUES ('$id_rpa', '$id_saida', '$tipo_guia')");
            
            ///INSERINDO ANEXO DA GUIA
            $qr_anexo       = mysql_query("INSERT INTO saida_files (id_saida, tipo_saida_file) VALUES ('$id_saida', '.pdf')");
            $id_saida_files = mysql_insert_id();
            
            $nome_arquivo = $id_saida_files.'.'.$id_saida.'.pdf'; 
       
            
       
            
            
              if($tipo_guia == 1 ){
               
                  $arquivo_origem  = '../../autonomo/arquivo_rpa_pdf/'.$id_rpa."_".$id_autonomo.'.pdf';
                  $arquivo_destino = '../../comprovantes/'.$nome_arquivo;
                 
                  
                 if(copy($arquivo_origem, $arquivo_destino)){
                       echo 'Envio concluído...';            
                                echo "<script> 
                                  setTimeout(function(){
                                    window.parent.location.href = 'http://".$_SERVER['HTTP_HOST']."/intranet/rh/pagamentos/index.php?id=1&regiao=$regiao&mes=$mes_consulta&ano=$ano_consulta&filtrar=1&tipo_pagamento=4';
                                    parent.eval('tb_remove()')
                                    },3000)    
                            </script>";                                            
                 } else {
                        echo 'Erro no cadastro.';
                 }
                 
                 
              } else {
            
                    if(move_uploaded_file($arquivo['tmp_name'], '../../comprovantes/'.$nome_arquivo)){

                             echo 'Envio concluído...';            
                                echo "<script> 
                                  setTimeout(function(){
                                    window.parent.location.href = 'http://".$_SERVER['HTTP_HOST']."/intranet/rh/pagamentos/index.php?id=1&regiao=$regiao&mes=$mes_consulta&ano=$ano_consulta&filtrar=1&tipo_pagamento=4';
                                    parent.eval('tb_remove()')
                                    },3000)    
                            </script>";               

                        } else {
                            echo 'Erro ao enviar o arquivo.';
                        }
                
              }
                
            
        }     
            exit;
}
?>
<html>
    <head>
        <title>RH - Pagamentos</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" type="text/css" href="../../js/highslide.css" />
        
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>        
        <script src="../../jquery/priceFormat.js" type="text/javascript"></script>        
        <script src="../../js/highslide-with-html.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine.js" type="text/javascript"></script>
        <script src="../../js/jquery.validationEngine-pt.js" type="text/javascript"></script>
        
        <style>
            .aviso { color: #f96a6a;
                     font-weight: bold;
            }
            h3{ color: #0bbfe7; } 
        </style>
        
        <script>
             hs.graphicsDir = '../../images-box/graphics/';
             hs.outlineType = 'rounded-white';
            
       $(function(){
           
           
           $('input[name=recalculo]').change(function(){
               
               if($(this).val() == 1){
                   
                   $('.valor_recalculo').show();
               } else {
                    $('.valor_recalculo').hide();
               }
               
               
           })
           
           
            //validation engine
            $("#form_rpa").validationEngine({promptPosition : "topRight"});
           
           
        $('#arquivo_rpa').change(function(){
             
             var aviso = $('.aviso');
             var arquivo = $(this);
             var extensao_arquivo = (arquivo.val().substring(arquivo.val().lastIndexOf("."))).toLowerCase();            
           
            
            if(arquivo.val() != '' && extensao_arquivo == '.pdf'){
               arquivo.css('background-color', '#51b566')
                .css('color','#FFF');
                 aviso.html('');
            } 
            
            if(extensao_arquivo != '.pdf') {
                 arquivo.css('background-color', ' #f96a6a')
                .css('color','#FFF');
                aviso.html('Este arquivo não é um PDF.');
            }
            
            
        })
           
        $('#enviar').click(function(){
            
            var aviso = $('.aviso');
            var arquivo = $('#arquivo_rpa');
            var extensao_arquivo = (arquivo.val().substring(arquivo.val().lastIndexOf("."))).toLowerCase();            
            $(this).attr('disabled','disabeld');
            if($('#valor').val() == ''){                
                aviso.html('Digite o valor.');
                $('#enviar').removeAttr('disabled');
                return false;
            }            
            
            if($('#data').val() == ''){                
                aviso.html('Digite a data.');
                $('#enviar').removeAttr('disabled');
                return false;
            }            
            
            if($('#bancos').val() == ''){                
                aviso.html('Selecione o banco.');
                $('#enviar').removeAttr('disabled');
                return false;
            }            
            
            if($('#tipo_guia').val() != 1 ){
                if(arquivo.val() == ''){                
                    aviso.html('O arquivo não foi anexado'); 
                    $('#enviar').removeAttr('disabled');
                    return false;
                }     
            
            }
            
            if(extensao_arquivo != '.pdf'){                
               aviso.html('Este arquivo não é um PDF.');
               $('#enviar').removeAttr('disabled');
                return false;
            }     
          $('#form_rpa').submit();
        });   
           
           
        $('#valor').priceFormat({
               prefix: '',
               centsSeparator: ',',
               thousandsSeparator: '.'
           });  
           
        $('#data').datepicker({
                  dateFormat: 'dd/mm/yy',
                  changeMonth: true,
                  changeYear: true
              });
   
           
           
        $('input[name=cod_barra]').change(function(){
            
          if($(this).val() == 1){              
              $('.campo_codigo_gerais').show();
          }else {
               $('.campo_codigo_gerais').hide();
          } 
        })   
           
      
           
           
        $('input[name=cod_barra]').change(function(){
                    if($(this).val() == 1){        
                        $('.campo_codigo_gerais').show();
                    } else{
                        $('.campo_codigo_gerais').hide();
                        $('#campo_codigo_gerais1, #campo_codigo_gerais2, #campo_codigo_gerais3, #campo_codigo_gerais5 , #campo_codigo_gerais4, #campo_codigo_gerais6, #campo_codigo_gerais7, #campo_codigo_gerais8').val(''); 
                    }
                });

                $('#campo_codigo_gerais1, #campo_codigo_gerais2, #campo_codigo_gerais3, #campo_codigo_gerais5') .keyup(function(){ limita_caractere($(this), 5, 1) });
                $('#campo_codigo_gerais4, #campo_codigo_gerais6').keyup(function(){ limita_caractere($(this), 6, 1) });
                $('#campo_codigo_gerais7').keyup(function(){ limita_caractere($(this), 1, 1) });  

                $('#campo_codigo_gerais8').keyup(function(){
                    if ($(this).val().length >= 14){
                        $(this).blur(); 
                        var valor = $(this).val().substr(0, limite);
                        $(this).val(valor) ; 

                    }    
                });

                function limita_caractere(campo, limite, muda_campo){
                    var tamanho = campo.val().length;   

                    if(tamanho >= limite ){
                        campo.next().focus();
                        var valor = campo.val().substr(0, limite);
                        campo.val(valor);
                    }
                }
       }) 
    
       </script>
        
    </head>
    <body>
            <form action="zcadastro_rpa_guias.php" name="form1" id="form_rpa" method="post" enctype="multipart/form-data">

            <input type="hidden" name="id_rpa"      id="id_rpa"      value="<?php echo $id_rpa ?>" />
            <input type="hidden" name="id_autonomo" id="id_autonomo" value="<?php echo $id_autonomo ?>" />
            <input type="hidden" name="tipo_saida"  id="tipo_saida"     value="<?php echo $tipo_saida ?>" />
            <input type="hidden" name="tipo_guia"   id="tipo_guia"      value="<?php echo $tipo_guia ?>" />
            <input type="hidden" name="subgrupo"    id="subgrupo"    value="<?php echo $subgrupo ?>" />
            <input type="hidden" name="mes_consulta"    id="mes_consulta"    value="<?php echo $mes_consulta; ?>" />
            <input type="hidden" name="ano_consulta"    id="ano_consulta"    value="<?php echo $ano_consulta; ?>" />

            <table width="90%" border="0" align="center" cellpadding="5" cellspacing="0">
                <tr>
                    <td colspan="4" align="center">
                        <h3> <?= $nome_completo ?> </h3>
                    </td>
                </tr>
                <tr>
                    <td width="269">&nbsp;</td>
                    <td width="205" align="right"><span style="font-size:12px;">Valor Líquido: </span></td>
                    <td width="1081">                        
                        R$ <?php echo number_format($valor,2,',','.'); ?>   
                        <input name="valor" type="hidden" id="valor" size="13" value="<?php echo $valor;?>"/>  
                    </td>                    
                    <td width="36">&nbsp;</td>
                </tr>
                
                  
                        <?php
                        if($_COOKIE['logado'] == 87){
                            
                            if($tipo_guia ==2 or $tipo_guia = 3)
                            ?>
                            <tr>
                                <td></td>
                                <td align="right">
                                    <span style="font-size:12px;">Possui recálculo ?: </span>
                                </td> 
                                <td style="font-size:12px;">
                                    <input type="radio" name="recalculo"  value="1"/> SIM
                                    <input type="radio" name="recalculo"  value="2" checked="checked"/> NÃO
                                </td>                           
                                <td></td>
                            </tr>        
                            <tr style="display:none;" class="valor_recalculo">
                                <td></td>
                                <td align="right">
                                        <span style="font-size:12px;">Valor do recálculo: </span>
                                </td>
                                <td colspan="2"><input type="text" name="valor_recalculo"  value=""/></td>
                            </tr>
            
                   <?php      }       ?>
                            
                <tr>
                    <td>&nbsp;</td>
                    <td align="right"><span style="font-size:12px;">Data :</span></td>
                    <td><input name="data" type="text" id="data" size="13" class="validate[required,custom[dateBr]]" /></td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td align="right"><span style="font-size:12px;">Banco :</span></td>
                    <td><label for="bancos"></label>
                        <select name="bancos" id="bancos" class="validate[required,custom[select]]">
                            <option value="">Selecione...</option>
                            <?php 
                            $query_banco = mysql_query("SELECT * FROM bancos WHERE id_regiao = '{$usuario['id_regiao']}' AND status_reg = '1'");
                            while($banco = mysql_fetch_array($query_banco)){ ?>
                            <option  value="<?= $banco['id_banco'] ?>" ><?= $banco['id_banco'].' - '.$banco['nome']; ?></option>
                            <?php } ?>
                        </select></td>
                    <td>&nbsp;</td>
                </tr>
                <?php if ($texto == 'GPS') { ?>
                    <tr>
                        <td></td>
                        <td align="right">C&oacute;digo de barras:</td>
                        <td colspan="3">
                            <input name="cod_barra" type="radio" value="1"/> Sim<br>
                            <input name="cod_barra" type="radio" value="0"/> N&atilde;o <br>
                        </td>
                    </tr>
                    <tr class="campo_codigo_gerais" style="display:none;"> 
                        <td></td>
                        <td></td>
                        <td colspan="2">
                            <input name="campo_codigo_gerais[]" type="text" id="campo_codigo_gerais1" style="width:50px;"/>.
                            <input name="campo_codigo_gerais[]" type="text" id="campo_codigo_gerais2" style="width:50px;"/>.
                            <input name="campo_codigo_gerais[]" type="text" id="campo_codigo_gerais3" style="width:50px;"/>
                            <input name="campo_codigo_gerais[]" type="text" id="campo_codigo_gerais4" style="width:60px;"/>.
                            <input name="campo_codigo_gerais[]" type="text" id="campo_codigo_gerais5" style="width:50px;"/>
                            <input name="campo_codigo_gerais[]" type="text" id="campo_codigo_gerais6" style="width:60px;"/>.
                            <input name="campo_codigo_gerais[]" type="text" id="campo_codigo_gerais7" style="width:30px;"/>
                            <input name="campo_codigo_gerais[]" type="text" id="campo_codigo_gerais8" style="width:130px;"/>    
                        </td>    
                    </tr>
                <?php } ?>
                    <?php if($tipo_guia != 1){ ?>
                        <tr>
                            <td colspan="2" align="right" valign="midlle"><?php if ($texto == "GPS") { ?> GPS<?php } ?></td>
                            <td><input type="file" name="arquivo" id="arquivo_rpa" /> 
                                <span style="color:    #828788; ">* .pdf </span>
                            </td>
                            <td>&nbsp;</td>
                        </tr>               
                <?php } else { ?>
                       <tr>
                            <td colspan="2" align="right" valign="midlle">Visualizar RPA:</td>
                            <td> 
                                <a id="ver_rpa" href="http://<?php echo $_SERVER['HTTP_HOST'];?>/intranet/autonomo/arquivo_rpa_pdf/<?php echo $id_rpa; ?>_<?php echo $id_autonomo;?>.pdf"  target="iframe">     <img border="0px" src="../folha/imagens/verfolha.gif" width="18" height="18" /></a>
                            </td>
                        </tr>
                <?php } ?>  
                
                <tr>
                    <td colspan="4" align="center"> 
                        <p class="aviso"></p>
                            <input type="hidden" name="acao" value="cadastrar"/>
                            <input type="button" value="Enviar" name="enviar" id="enviar"/>
                    </td>
                </tr>
            </table>
            </form>
      <!--  <iframe src="" name="iframe" width="100%"></iframe>-->
    </body>
</html>