<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
}
include "../../conn.php";
include "../../funcoes.php";
include "../../classes/funcionario.php";
include '../../classes_permissoes/regioes.class.php';
include("../../wfunction.php");

$usuario = carregaUsuario();

$enc = $_REQUEST['enc'];
$enc = str_replace("--","+",$enc);
$link = decrypt($enc); 

$decript = explode("&",$link);
$regiao = $decript[0];
$folha = $decript[1];
$link_voltar      = 'ver_folha.php?enc='.str_replace('+', '--', encrypt("$regiao&$folha"));


$qr_folha  = mysql_query("SELECT A.id_folha, B.nome_mes, DATE_FORMAT(A.data_inicio,'%d/%m/%Y') as data_inicio,
                         DATE_FORMAT(A.data_fim,'%d/%m/%Y') as data_fim
                         FROM rh_folha  as A 
                         INNER JOIN ano_meses as B
                         ON A.mes = B.num_mes 
                         WHERE id_folha = $folha");
$row_folha = mysql_fetch_array($qr_folha);



$qr_folha_proc = mysql_query("SELECT A.id_clt, A.nome, A.cpf,FORMAT(A.salliquido,2) as salliquido,A.financeiro,
                                    IFNULL(B.nome, C.nome_banco) as nome_banco,
                                    IFNULL(B.agencia,C.agencia) as agencia, 
                                    IFNULL(B.conta,C.conta)as conta,
                                    C.conta_dv, C.agencia_dv,

                                    CASE C.tipo_conta
                                            WHEN 'salario' THEN 'Conta Salário'
                                            WHEN 'corrente' THEN 'Conta Corrente'
                                            WHEN 'poupanca' THEN 'Conta Poupança'
                                    END as tipo_conta 
                                            -- IF(C.tipo_conta = 'salario', 'Conta Salário',IF(C.tipo_conta = 'corrente','Conta Corrente','')) 
                                    FROM rh_folha_proc  as A 
                                    LEFT JOIN bancos as B
                                    ON A.id_banco = B.id_banco 
                                    INNER JOIN rh_clt as C
                                    ON C.id_clt = A.id_clt
                                    WHERE A.id_folha = '$row_folha[id_folha]' AND A.status = 3 ORDER BY  A.financeiro DESC ,A.nome ASC");


///REGIÕES
$regioes = montaQuery('regioes', "id_regiao,regiao", "id_master = '$usuario[id_master]'");
$optRegiao = array();
foreach ($regioes as $valor) {
    $optRegiao[$valor['id_regiao']] = $valor['id_regiao'] . ' - ' . $valor['regiao'];
}
$regiaoSel = $regiao;




if (isset($_POST['gerar'])) {

   
}
?>
<html>
    <head>
        <title>Pagamento em lote</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link href="../../net1.css" rel="stylesheet" type="text/css">
         <link rel="stylesheet" type="text/css" href="../../js/highslide.css" />
        <link rel="stylesheet" type="text/css" href="../../jquery/datepicker-lite/jquery-ui-1.8.4.custom.css" />
        <script src="../../jquery/jquery-1.4.2.min.js" type="text/javascript"></script>
        <!--<script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>-->
        <script src="../../jquery/jquery.tools.min.js" type="text/javascript" ></script>
        <!--<script src="../../js/global.js" type="text/javascript"></script>-->
        <script type="text/javascript" src="../../js/highslide-with-html.js"></script>
       <script type="text/javascript" src="../../jquery/datepicker-lite/jquery-ui-1.8.4.custom.min.js" ></script>
       
        <script>
     hs.graphicsDir = '../../images-box/graphics/';
    hs.outlineType = 'rounded-white';
        $(function(){

        $('#todos').change(function(){    
            var checked = $(this).attr('checked');
            $('input[name=clts[]]').attr('checked',checked);  
        });
        
        
        
       $('#data_vencimento').datepicker({
		changeMonth: true,
	    changeYear: true
	});
        
       
        
        
        $('#regiao').change(function(){	
                var id_regiao = $(this).val();
                $('.loader_projeto').html('<img src="../../img_menu_principal/loader16.gif"/>');
                $.ajax({		
                        url : '../../folhaspg/actions/dados_gera_saida.php?regiao='+id_regiao,
                        
                        success :function(resposta){			
                                        $('#projeto').html(resposta);
                                         $('#banco').html('');
                                         $('.loader_projeto').html('');      
                                    }		
                        });
                   
                        
                });	
        
        $('#projeto').change(function(){	
                var id_projeto = $(this).val();
                 $('.loader_banco').html('<img src="../../img_menu_principal/loader16.gif"/>');
                $.ajax({                        
                        url : '../../folhaspg/actions/dados_gera_saida.php?projeto='+id_projeto,
                      
                        success :function(resposta){			
                                        $('#banco').html(resposta);
                                        $('.loader_banco').html('')
                                }		
                        });
              
                });	     
             
             
                $("#regiao").trigger("change");
                $('form').submit(function(){
                  
                    if($('#regiao').val() == ''){       $('.erro').html('Selecione a região.'); return false}
                    else if($('#projeto').val() == ''){ $('.erro').html('Selecione o projeto.'); return false}
                    else if($('#banco').val() == ''){ $('.erro').html('Selecione o banco.'); return false}
                    else if($('#data_vencimento').val() == ''){ $('.erro').html('Preencha a data de vencimento.'); return false}
                    else if($('input[name=clts[]]:checked').length == 0){ $('.erro').html('Selecione um ou mais participantes.'); return false}
                    else{
                       return true;  
                    }
                    
                    
                    
                   
                });
                
        });
        </script>
   


    </head>
    <body class="novaintra">       
      <form  name="form" action="gerar_saida_selecionado.php" method="post" id="form">
          <div id="content">
            <div class="link_voltar"><a href="<?=$link_voltar?>" title="Voltar"> <img src="../../imagens/voltar.png" width="88" height="33"/> </a> </div>
    
            <div id="head">
              <img src="../../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;" width="140" height="100"/>
               <div class="fleft">
                    <h2>Pagamento em lote</h2>
                    <p><strong>Folha:</strong> <?php echo $folha;?></p>
                    <p><strong>Mês: </strong><?php echo $row_folha['nome_mes'];?></p>
                    <p><strong>Período:</strong> <?php echo $row_folha['data_inicio']. ' a '.$row_folha['data_fim'];?></p>                 
                </div>
            </div>
            <br class="clear">
            <br/>

           
                <fieldset>
                    <legend>PAGAMENTO EM LOTE</legend>
                    <div class="fleft">  
                        <p><label class="first">Região:</label> <?php echo montaSelect($optRegiao, $regiaoSel, array('name' => "regiao", 'id' => 'regiao')); ?></p>
                        <p><label class="first">Projeto:</label> <?php echo montaSelect('', '', array('name' => "projeto", 'id' => 'projeto')); ?> <span class="loader_projeto"></span></p>
                        <p><label class="first">Banco:</label> <?php echo montaSelect('', '', array('name' => "banco", 'id' => 'banco')); ?><span class="loader_banco"></span></p>                       
                        <p><label class="first"> Data de vencimento:</label><input type="text" name="data_vencimento" id="data_vencimento"/></p>
                        
                    </div>
  
                    <br class="clear"/>
                   
                    <p class="controls" style="margin-top: 10px;"> 
                        <span class="erro fleft"></span>                  
                      <input type="hidden" name="id_master" value="<?php echo $id_master;?>"/>                     
                      <input type="hidden" name="id_folha" value="<?php echo $folha;?>"/>                     
                      <input type="hidden" name="regiao_folha" value="<?php echo $regiao;?>"/>                     
                       <input type="submit" name="enviar" value="Enviar" id="enviar"/>
                    </p>
                </fieldset>
               
            <div class="clear"></div>
               <table border="0" cellpadding="0" cellspacing="0" class="grid" width="100%">
               <thead>
                <tr>
                    <td class="txcenter">Todos <br>
                    <input type="checkbox" name="todos" id="todos"/></td>
                    <td class="txcenter">Código</td>
                    <td>Nome</td>
                    <td class="txcenter">Banco</td>
                    <td class="txcenter">Agência</td>
                    <td class="txcenter">Agência DV</td>
                    <td class="txcenter">Conta</td>
                    <td class="txcenter">Conta DV</td>
                    <td class="txcenter">CPF</td>
                    <td class="txcenter">Tipo de conta</td>
                    <td class="txcenter">Sal.Líquido</td>
                    <td class="txcenter">Status</td>
                </tr>
               </thead>
                <?php while($row_folha_proc = mysql_fetch_assoc($qr_folha_proc)) { 
                    
                  $cor = ($i++ % 2 == 0)? 'even':'odd';  
                    ?>
               <tbody>
                <tr class="<?php echo $cor; ?> select">
                    <td class="txcenter">
                   <?php if($row_folha_proc['salliquido'] <= 0){ echo "-"; }elseif($row_folha_proc['financeiro'] != 1 ) {?>
                        <input type="checkbox" name="clts[]" id="clts"  value="<?php echo $row_folha_proc['id_clt'];?>">
                    <?php } ?>
                    </td>
                    <td><?php echo $row_folha_proc['id_clt'];?></td>
                    <td><?php echo $row_folha_proc['nome'];?></td>
                    <td><?php echo $row_folha_proc['nome_banco'];?></td>
                    <td><?php echo $row_folha_proc['agencia'];?></td>
                    <td><?php echo $row_folha_proc['agencia_dv'];?></td>
                    <td><?php echo $row_folha_proc['conta'];?></td>
                    <td><?php echo $row_folha_proc['conta_dv'];?></td>
                    <td><?php echo $row_folha_proc['cpf'];?></td>
                    <td><?php echo $row_folha_proc['tipo_conta'];?></td>
                    <td class="txcenter"><?php echo $row_folha_proc['salliquido'];?></td>
                    <td class="center">
                    <?php if($row_folha_proc['salliquido'] == 0 ) {?>
                      -
                    <?php }elseif($row_folha_proc['financeiro'] == 1 ) {?>
                      <img src="../../imagens/bolha2.png" width="18" height="18" title="ENCAMINHADO PARA O FINANCEIRO"/>
                   <?php } else { ?>
                      <a href="action.gerar_saida_folha.php?id_trab=<?php echo $row['id_clt'];?>&folha=<?php echo $row['id_folha'];?>"   onclick="return hs.htmlExpand(this, { objectType: 'iframe' } )"><img src="../../imagens/bolha1.png" width="18" height="18" title="ENVIAR PARA O FINANCEIRO"/></a>
                   <?php } ?>
                        
                    </td>
                    
                </tr>
                
               </tbody>
                
                <?php } ?>
            </table>
            
            
            
        </div>
  
 </form>  
</body>
</html>