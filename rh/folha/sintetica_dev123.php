
<?php include('sintetica/cabecalho_folha_dev123.php'); ?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>:: Intranet :: Folha Sint&eacute;tica de CLT (<?=$folha?>)</title>
<link href="sintetica/folha.css" rel="stylesheet" type="text/css">
<link href="../../favicon.ico" rel="shortcut icon">
<script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
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
   
    /****************MOSTRA TODOS *****************************/
    $(".legenda .mostrar_todos").click(function(){
        history.go(0);
    });

    /*****************ESCONDE OS TOTAIS************************/
    $(".legenda").click(function(){
        $(".totais").hide();
    });

    /****************MOSTRA TODOS QUE ENTRARAM NA FOLHA********/
    $(".legenda .entrada").click(function(){
        $(".destaque").each(function(){
            $(this).find("span").parents("tr").show();
            $(this).find("span").not(".entrada").parents("tr").hide();
        });
        $(".esconde_geral").hide();
        $(".totais_entrada").show();
        $("#estatisticas").hide();
    });

    /****************MOSTRA TODOS COM EVENTOS LANÇADOS*********/
    $(".legenda .evento").click(function(){
        $(".destaque").each(function(){
            $(this).find("span").parents("tr").show();
            $(this).find("span").not(".evento").parents("tr").hide();
        });
        $(".esconde_geral").hide();
        $(".totais_linceca").show();
        $("#estatisticas").hide();
    });

    /****************MOSTRA TODOS COM FALTAS*********/
    $(".legenda .faltas").click(function(){
        $(".destaque").each(function(){
            $(this).find("span").parents("tr").show();
            $(this).find("span").not(".faltas").parents("tr").hide();
        });
        $(".esconde_geral").hide();
        $(".totais_faltas").show();
        $("#estatisticas").hide();
    });

    /****************MOSTRA TODOS COM FALTAS*********/
    $(".legenda .ferias").click(function(){
        $(".destaque").each(function(){
            $(this).find("span").parents("tr").show();
            $(this).find("span").not(".ferias").parents("tr").hide();
        });
        $(".esconde_geral").hide();
        $(".totais_ferias").show();
        $("#estatisticas").hide();
    });

    /****************MOSTRA TODOS EM RESCISAO*********/
    $(".legenda .rescisao").click(function(){
        $(".destaque").each(function(){
            $(this).find("span").parents("tr").show();
            $(this).find("span").not(".rescisao").parents("tr").hide();
        });
        $(".esconde_geral").hide();
        $(".totais_rescisao").show();
        $("#estatisticas").hide();
    });
                
             
});
   
   
</script>
<style type="text/css">
	.highslide-html-content { width:600px; padding:0px; }
        .rendimentos{
            background-color:  #033;	
        }
        #tabela tr{
            font-size:10px;
        }	
        #folha .sem_borda td {
            border:0;
        }
        .mostrar_todos{
            background: #000;
        }
        .nota{
            cursor: pointer;
        }
        .totais_entrada, .totais_linceca, .totais_faltas, .totais_ferias, .totais_rescisao{
            display: none;
            font-weight: bold;
            text-align: center;
        }
</style>
</head>
<body>
<div id="corpo">
    <table cellspacing="4" cellpadding="0" id="topo">
      <tr>
        <td width="15%" rowspan="3" valign="middle" align="center">
          <img src="../../imagens/logomaster<?=mysql_result($qr_projeto, 0, 2)?>.gif" width="110" height="79">
        </td>
        <td colspan="3" style="font-size:12px;">
        	<b><?=mysql_result($qr_projeto, 0, 1).' ('.$mes_folha.')'?></b>
        </td>
      </tr>
      <tr>
        <td width="35%"><b>Data da Folha:</b> <?=$row_folha['data_inicio_br'].' &agrave; '.$row_folha['data_fim_br']?></td>
        <td width="30%"><b>Região:</b> <?=$regiao.' - '.mysql_result($qr_regiao, 0, 1)?></td>
        <td width="20%"><b>Participantes:</b> <?=$total_participantes?></td>
      </tr>
      <tr>
        <td><b>Data de Processamento:</b> <?=$row_folha['data_proc_br']?></td>
        <td><b>Gerado por:</b> <?=@abreviacao(mysql_result($qr_usuario, 0), 2)?></td>
        <td><b>Folha:</b> <?=$folha?></td>
      </tr>
    </table>
     
    	<table cellpadding="0" cellspacing="1" id="folha">
            <tr>
              <td colspan="2">
                <a href="<?=$link_voltar?>" class="voltar">Voltar</a>
              </td>
              <td colspan="8">
              <?php if(empty($decimo_terceiro)) { ?>
                <div style="float:right;">
                    <div class="legenda"><div class="nota entrada"></div>Admissão</div>
                    <div class="legenda"><div class="nota evento"></div>Licen&ccedil;a</div>
                    <div class="legenda"><div class="nota faltas"></div>Faltas</div>
                    <div class="legenda"><div class="nota ferias"></div>F&eacute;rias</div>
                    <div class="legenda"><div class="nota rescisao"></div>Rescis&atilde;o</div>
                </div>
              <?php } ?>
              </td>
            </tr>
            <tr>
              <td colspan="10">
              		
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
                  
                  
                    <table cellspacing="0" cellpadding="0" width="100%">
                     <tr class="secao">
                      <td width="4%">COD</td>
                      <td width="28%" align="left" style="padding-left:5px;">NOME</td>
                      <td width="6%"><?php if(!empty($decimo_terceiro)) { echo 'MESES'; } else { echo 'DIAS'; } ?></td>
                      <td width="8%">BASE</td>
                      <td width="10%">RENDIMENTOS</td>
                      <td width="10%">DESCONTOS</td>
                      <td width="8%">INSS</td>
                      <td width="8%">IRRF</td>
                      <td width="8%">FAM&Iacute;LIA</td>
                      <td width="10%">L&Iacute;QUIDO</td>
                     </tr>
                    </table>
                    
              </td>
            </tr>
            
       
<?php   
        
        // Início do Loop dos Participantes da Folha
        while($row_participante = mysql_fetch_array($qr_participantes)) {
		  
		  // Id do Participante
		  $clt = $row_participante['id_clt'];
		  
		  // Link para Relatório
		  $relatorio = str_replace('+', '--', encrypt("$clt&$folha"));
                  
                  include('sintetica/calculos_folha_dev123.php'); 
                  
?>
         
		 <tr class="linha_<?php if($linha++%2==0) { echo 'um'; } else { echo 'dois'; } ?> destaque">
            <td width="4%">
                 <?=$clt?>
                <input type="hidden" name="id_funcao" class="funcao" value="<?php echo $row_clt['id_curso'];?>"/>
            </td>
		    <td width="28%" align="left">
				<a href="sintetica/relatorio<?php if(!empty($decimo_terceiro)) { echo '_dt'; } ?>.php?enc=<?=$relatorio?>" onClick="return hs.htmlExpand(this, { objectType: 'iframe' } )" class="participante" title="Ver relatório de <?=$row_clt['nome']?>">
                	
                                    <?php
                                    var_dump($row_participante['sallimpo_real']);
                                    echo '<br>';
                                    var_dump($row_participante['valor_dt']);
                                    echo '<br>';
                                    var_dump($row_participante[$indice]);
                                    echo '<br>';
                                    ?>
                                    
                                    <span class="
                    <?php 		
                          if(isset($dias_entrada)) { 
                              echo 'entrada';
                              $array_totais["entrada"]["base"] += $row_participante['sallimpo_real'] + $row_participante['valor_dt'];
                              $array_totais["entrada"]["rendimento"] += $row_participante[$indice];
                          } elseif(isset($sinaliza_evento)) {
                              echo 'evento';
                              $array_totais["linceca"]["base"] += $row_participante['sallimpo_real'] + $row_participante['valor_dt'];
                              $array_totais["linceca"]["rendimento"] += $row_participante[$indice];
                          } elseif(($ferias)) {
                              echo 'ferias';
                              $array_totais["ferias"]["base"] += $row_participante['sallimpo_real'] + $row_participante['valor_dt'];
                              $array_totais["ferias"]["rendimento"] += $row_participante[$indice];
                          } elseif(!empty($num_rescisao)) {
                              echo 'rescisao';
                              $array_totais["rescisao"]["base"] += $row_participante['sallimpo_real'] + $row_participante['valor_dt'];
                              $array_totais["rescisao"]["rendimento"] += $row_participante[$indice];
                          } elseif(isset($dias_faltas)) {
                              echo 'faltas';
                              $array_totais["faltas"]["base"] += $row_participante['sallimpo_real'] + $row_participante['valor_dt'];
                              $array_totais["faltas"]["rendimento"] += $row_participante[$indice];
                          } else { 
                              echo 'normal';
                          } 
                     ?>
                          ">
                              
                              <?php echo abreviacao($row_clt['nome'], 4, 1);?></span>
                         
                    <img src="sintetica/seta_<?php if($seta++%2==0) { echo 'um'; } else { echo 'dois'; } ?>.gif">
                </a>
                 <?php
                 if($_COOKIE['logado'] == 87){                     
                   echo 'BASE INSS: '.$base_inss;
                   echo '<br> BASE 13 rescisao: '.$base_inss_13_rescisao;
                 }
                 ?>        
            </td>
                <td width="6%"><?php if(!empty($decimo_terceiro)) { echo $meses; } else { echo $dias; } ?> </td>
                    <td width="8%"><?php if(!empty($decimo_terceiro)) { echo formato_real($decimo_terceiro_credito); } else { echo formato_real($salario); } ?></td>
                    <td width="10%"><?=formato_real($rendimentos)?></td>
                    <td width="10%"><?=formato_real($descontos)?></td>
                    <td width="8%"><?=formato_real($inss_completo)?></td>
                    <td width="8%"><?=formato_real($irrf_completo)?></td>
                    <td width="8%"><?=formato_real($familia)?></td>
                    <td width="10%"><?=formato_real(abs($liquido)); ?> # <?php var_dump($liquido); ?></td>
                </tr>

		<?php include('sintetica/update_participante.php');
			  include('sintetica/totalizadores_resets.php');

		 	// Fim do Loop de Participantes
                        unset($sem_mov_sempre);
	  	 } ?>
                
                
                <?php 
                
                foreach ($array_totais as $key => $total){ ?>
                        <?php if($_COOKIE['logado'] == 204){print_r($array_totais);} ?>
                        <tr class="totais_<?php echo $key; ?> esconde_geral">
                            <td colspan="2">
                                <?php if ($total_participantes > 10) { ?>
                                    <a href="#corpo" class="ancora">Subir ao topo</a>
                                <?php } ?>
                            </td>
                            <td>TOTAIS:</td>
                            <!-- ********************** TOTAIS DE ENTRADAS ***************************** -->
                            <td><?= formato_real($total["base"]) ?></td>
                            <td></td>
                            <td><?= formato_real($total["rendimento"]) ?></td>
                            <td><?= formato_real($total["desconto"]) ?></td>
                            <td><?= formato_real($total["liquido"]) ?></td>
                            
                        </tr>
                    
                    <?php  }  ?>
                
      	<tr class="totais">
        <td colspan="2">
            <?php if($total_participantes > 10) { ?>
                <a href="#corpo" class="ancora">Subir ao topo</a>
            <?php } ?>
        </td>
        <td>TOTAIS:</td>
        <td><?php if(!empty($decimo_terceiro)) { echo formato_real($decimo_terceiro_total); } else { echo formato_real($salario_total); } ?></td>
        <td><?=formato_real($rendimentos_total)?></td>
        <td><?=formato_real($descontos_total)?></td>
        <td><?=formato_real($inss_completo_total)?></td>
        <td><?=formato_real($irrf_completo_total)?></td>
        <td><?=formato_real($familia_total)?></td>
        <td><?=formato_real($liquido_total)?></td>
        </tr>
    </table> 
    <?php include('sintetica/estatisticas_folha.php'); ?>
</div>
<?php include('sintetica/updates.php'); 


?>
</body>
</html>