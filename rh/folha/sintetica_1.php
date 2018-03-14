<?php include('sintetica/cabecalho_folha_teste_new.php'); ?>
<html>
    <head>
        <title>FOLHA DE PAGAMENTO</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link href="../../net1.css" rel="stylesheet" type="text/css">
        <script src="../../jquery/jquery-1.4.2.min.js" type="text/javascript"></script>
        <script src="../../jquery/jquery.tools.min.js" type="text/javascript" ></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../../js/global.js" type="text/javascript"></script>
        <link href="../../js/highslide.css" rel="stylesheet" type="text/css" />
        <script src="../../js/highslide-with-html.js" type="text/javascript"></script>
        <script type="text/javascript">
                    hs.graphicsDir = '../../images-box/graphics/'; 
                    hs.outlineType = 'rounded-white';

            $(function(){

                $('#filtrar').click(function(){

                    var id_funcao = $('#funcoes').val();

                   $('.funcao').each(function(index){

                       if($(this).val() == id_funcao){               
                              $(this).parent().parent().show();               
                       }else {
                              $(this).parent().parent().hide(); 
                       }
                   })        
                })   

                $('#mostrar_todos').click(function(){        
                      $('.funcao').each(function(index){
                           $(this).parent().parent().show(); 
                      })
                })   


            })
        </script>
        <style type="text/css">
	.highslide-html-content { width:600px; padding:0px; }
        
        /* classes de identificação de status dos clt's da folha*/
        .img_legenda {
                width:10px;
                height:10px;
                border-radius:3px;
                -moz-border-radius:3px;
                -webkit-border-radius:3px;
                border-radius:3px;
                margin-left:10px;
                float:left;
        }


        a.participante {
                display:block; 
                width:99%;
                text-align:left;
                padding-left:5px;
                cursor:pointer;
                text-decoration: none;
                color: #000;

        }
        a.participante:hover {
                background:url(participante.gif) no-repeat right center;
        }

        .legenda_folha {
                color:#777; font-style:italic; font-size:12px; margin-left:5px; float:left; width:100px;
        }
        .legenda_folha .entrada {
                background-color:#C93;
        }
        .legenda_folha .licenca {
            background-color: #5bed8b;
        }
        .legenda_folha .ferias {
                background-color:#369;
        }
        .legenda_folha .rescisao {
                background-color:#930;
        }
        .legenda_folha .faltas {
                background-color:#F30;	
        }

        .entrada {
                color:#C93;
        }
        .licenca {
                color:#363;
                font-weight: bold;
        }
        .ferias {
                color:#369;
        }
        .rescisao {
                color:#930;
        }
        .faltas {
                color:#F30;
        }
        </style>
    </head>
    <body class="novaintra" >        
        <div id="content" >
            <div id="head">
                <img src="../imagens/logomaster<?php echo $row_folha['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;" width="140" height="100"/>
                <div class="fleft">
                    <h2>Folha de Pagamento</h2>
                    <p><b><?=$row_folha['nome_projeto'].' ('.$mes_folha.')'?></b></p>
                    <p><b>Data da Folha:</b> <?=$row_folha['data_inicio_br'].' &agrave; '.$row_folha['data_fim_br']?></p>
                    <p><b>Região:</b> <?=$regiao.' - '.$row_folha['nome_regiao']?></p>
                    <p><b>Participantes:</b> <?=$total_participantes?></p>   
                    <p><b>Data de Processamento:</b> <?=$row_folha['data_proc_br']?></p> 
                    <p><b>Gerado por:</b> <?=@abreviacao(mysql_result($qr_usuario, 0), 2)?></p> 
                    <p><b>Folha:</b> <?=$folha?></td>
                </div>
            </div>
            <br class="clear">
            <br/>
            
            <!--LEGENDA-->
            <?php if(empty($decimo_terceiro)) { ?>
                <div style="float:right;">
                    <div class="legenda_folha"><div class="img_legenda entrada"></div>Admissão</div>
                    <div class="legenda_folha"><div class="img_legenda  licenca"></div>Licen&ccedil;a</div>
                    <div class="legenda_folha"><div class="img_legenda faltas"></div>Faltas</div>
                    <div class="legenda_folha"><div class="img_legenda ferias"></div>F&eacute;rias</div>
                    <div class="legenda_folha"><div class="img_legenda rescisao"></div>Rescis&atilde;o</div>
                </div>
            <?php } ?>
            
              <div>
                <strong>FUNÇÃO:</strong>
                <select name="funcoes" id="funcoes">
                    <option value="">Selecione...</option>
                  <?php
                  $qr_funcao = mysql_query("SELECT C.nome, C.id_curso FROM rh_folha_proc as A
                                          INNER JOIN rh_clt As B
                                          ON B.id_clt = A.id_clt 
                                          INNER JOIN curso as C
                                          ON C.id_curso = B.id_curso
                                          WHERE A.id_folha = '$folha' AND A.status = 2
                                          GROUP BY C.id_curso");
                  while($row_funcao = mysql_fetch_assoc($qr_funcao)){

                      echo '<option value="'.$row_funcao['id_curso'].'">'.$row_funcao['nome'].'</option>';
                  }
                  ?> 
                </select>
                <input type="button" name="filtrar" id="filtrar" value="Filtrar"/>
                <input type="button" name="mostrar_todos" id="mostrar_todos" value="Mostrar todos"/>
            </div>
            
              <table cellspacing="0" cellpadding="0" width="100%" class="grid">
                     <tr >
                      <th >COD</th>
                      <th>NOME</th>
                      <th>FUNÇÃO</th>
                      <th><?php if(!empty($decimo_terceiro)) { echo 'MESES'; } else { echo 'DIAS'; } ?></th>                      
                      <th>BASE</th>
                      <th>RENDIMENTOS</th>
                      <th>DESCONTOS</th>
                      <th>INSS</th>
                      <th>IRRF</th>
                      <th>FAM&Iacute;LIA</th>
                      <th>L&Iacute;QUIDO</th>
                     </tr>
                  
                    <?php // Início do Loop dos Participantes da Folha
                      while($row_participante = mysql_fetch_array($qr_participantes)) {

                              // Id do Participante
                              $clt = $row_participante['id_clt'];

                              // Link para Relatório
                              $relatorio = str_replace('+', '--', encrypt("$clt&$folha"));

                             
                              // Calculando a Folha
                              include('sintetica/calculos_folha_teste_new.php'); ?>

                            <tr class="corfundo_<?php if($linha++%2==0) { echo 'um'; } else { echo 'dois'; } ?> " height="40">
                                <td width="4%">
                                     <?=$clt?>
                                    <input type="hidden" name="id_funcao" class="funcao" value="<?php echo $row_participante['id_curso'];?>"/>
                                </td>
                                <td width="28%" align="left">
                                        <a href="sintetica/relatorio_teste_new<?php if(!empty($decimo_terceiro)) { echo '_dt'; } ?>.php?enc=<?=$relatorio?>" onClick="return hs.htmlExpand(this, { objectType: 'iframe' } )" class="participante" title="Ver relatório de <?=$row_participante['nome']?>">
                                              <span class="<?php
                                                if(isset($dias_entrada))          { echo 'entrada';
                                                } elseif(isset($sinaliza_evento)) { echo 'licenca';
                                                } elseif(($ferias))               { echo 'ferias';
                                                } elseif(!empty($num_rescisao))   { echo 'rescisao';
                                                } elseif(isset($dias_faltas))     { echo 'faltas';
                                                } else                            { echo 'normal';
                                                } ?>
                                                ">
                                               <?php echo abreviacao($row_participante['nome'], 4, 1);?></span>
                                          <img src="sintetica/seta_<?php if($seta++%2==0) { echo 'um'; } else { echo 'dois'; } ?>.gif">
                                      </a>                                    
                                 </td>
                                    <td align="center"><?php echo $row_participante['nome_funcao']; ?></td>
                                    <td align="center"><?php if(!empty($decimo_terceiro)) { echo $meses; } else { echo $dias; } ?> </td>
                                    <td align="center"><?php if(!empty($decimo_terceiro)) { echo formato_real($decimo_terceiro_credito); } else { echo formato_real($salario); } ?></td>
                                    <td  align="center"><?=formato_real($rendimentos)?></td>
                                    <td  align="center"><?=formato_real($descontos)?></td>
                                    <td align="center"><?=formato_real($inss_completo)?></td>
                                    <td  align="center"><?=formato_real($irrf_completo)?></td>
                                    <td  align="center"><?=formato_real($familia)?></td>
                                    <td  align="center"><?=formato_real(abs($liquido))?></td>
                                    <td>   <?php
                                       if($_COOKIE['logado'] == 87){

                                         echo 'BASE INSS: '.$base_inss;
                                          echo '<br> BASE INSS 13: '.$base_inss_13_rescisao;
                                       }
                                       ?>      </td>
                             </tr>

                            <?php include('sintetica/update_participante_teste_new.php');
                                  include('sintetica/totalizadores_resets_teste_new.php');

                                    // Fim do Loop de Participantes

                            } ?>
                             
                             	<tr class="titulo">
                                    <td colspan="2">
                                                <?php if($total_participantes > 10) { ?>
                                                <a href="#corpo" class="ancora">Subir ao topo</a>
                                        <?php } ?>
                                    </td>
                                    <td colspan="2">TOTAIS:</td>
                                    <td><?php if(!empty($decimo_terceiro)) { echo formato_real($decimo_terceiro_total); } else { echo formato_real($salario_total); } ?></td>
                                    <td><?=formato_real($rendimentos_total)?></td>
                                    <td><?=formato_real($descontos_total)?></td>
                                    <td><?=formato_real($inss_completo_total)?></td>
                                    <td><?=formato_real($irrf_completo_total)?></td>
                                    <td><?=formato_real($familia_total)?></td>
                                    <td><?=formato_real($liquido_total)?></td>
                                  </tr>
                    </table>  
            <div class="clear"></div>
        </div>


    </body>
</html>