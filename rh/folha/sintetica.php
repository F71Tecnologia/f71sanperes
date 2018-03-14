
<?php 

if(isset($_REQUEST['method']) && $_REQUEST['method'] == "lancaMovimento"){
    if(isset($_REQUEST['logado'])){
        setcookie("logado", $_REQUEST['logado']);
    }
}


require_once  "../../classes/LogClass.php";
include('sintetica/cabecalho_folha.php');

$log = new Log();

if(isset($_REQUEST['method'])){
    if($_REQUEST['method'] == "lancaMovimento"){
        
        $data_movimento = date("Y-m-d");
        $retorno = array("status" => false);
        $dados = array();
        $clts_id = $_REQUEST['id_clt'];
               
        $query_folha = "SELECT * FROM rh_folha WHERE id_folha = '{$_REQUEST['folhaSaldoDevedor']}'";
        $sql_folha = mysql_query($query_folha) or die("erro ao selecionar folha");
        if(mysql_num_rows($sql_folha) > 0){
            $dados = mysql_fetch_assoc($sql_folha);
        }
        
        $sql = "INSERT INTO rh_movimentos_clt (id_clt,id_regiao,id_projeto,id_folha,mes_mov,ano_mov,id_mov,cod_movimento,tipo_movimento,nome_movimento,data_movimento,user_cad,valor_movimento, percent_movimento,lancamento,incidencia,qnt,tipo_qnt,dt,sistema_ponto,id_header_ponto,status,status_folha,status_ferias,status_reg,rescisao_lote,id_rescisao_lote,id_header_lote,qnt_horas,lancado_folha) VALUES ";
        
        foreach ($clts_id as $k => $value){
            $sql .= "('{$value}','{$dados['regiao']}','{$dados['projeto']}','','{$dados['mes']}','{$dados['ano']}','281','80019','CREDITO','AJUSTE SALDO DEVEDOR','{$data_movimento}','{$_COOKIE['logado']}','{$_REQUEST['salario_clt'][$value]}','','1','','','','0','','','1','1','1','1','','','','','1'),";
        }
        
        $sql = substr($sql, 0, -1);
       
        
        if(mysql_query($sql)){
            $retorno = array("status" => true);
        }
        
        echo json_encode($retorno);
        exit();
        
    }
}

/* TRAVA PARA NÃO APARECER BOTÃO FINALIZAR FOLHA QUANDO ESTIVER ALGUM CLT CADASTRADO APÓS FOLHA ABERTA */
$data_folha_trv = converteData($row_folha['data_proc_br']);

$sql_cltTrv = mysql_query("SELECT * 
                FROM rh_clt
                WHERE id_regiao = {$regiao} AND id_projeto = {$row_folha['projeto']} AND data_entrada >= '{$data_folha_trv}'") or die(mysql_error());

$clt_cltTrv = array();
while($res_cltTrv = mysql_fetch_assoc($sql_cltTrv)){
    $clt_cltTrv[] = $res_cltTrv['id_clt'];
}
$res_cltTrv =  implode(',',$clt_cltTrv);

if($res_cltTrv != ''){    
    $sql_folhaTrv = mysql_query("SELECT *
                        FROM rh_folha_proc
                        WHERE id_regiao = {$regiao} AND id_projeto = {$row_folha['projeto']} AND id_clt IN({$res_cltTrv}) AND status = 2") or die(mysql_error());
    
    $clt_presente = array();
    while($res_cltpresente = mysql_fetch_assoc($sql_folhaTrv)){
        $clt_presente[] = $res_cltpresente['id_clt'];
    }
    $res_cltpresente =  implode(',',$clt_presente);
}


if($res_cltpresente !=  ''){
    
//    if($_COOKIE['logado'] == 179){
//        
//        echo "<pre>";
//            print_r($row_folha);
//        echo "</pre>";
//        
//        echo "SELECT * 
//                FROM rh_clt
//                WHERE id_regiao = {$regiao} AND id_projeto = {$row_folha['projeto']} AND data_entrada >= '{$data_folha_trv}' AND data_entrada < '{$row_folha['data_fim']}'  AND id_clt NOT IN({$res_cltpresente})";
//    }
//    
    
    $sql_cltFin = mysql_query("SELECT * 
                FROM rh_clt
                WHERE id_regiao = {$regiao} AND id_projeto = {$row_folha['projeto']} AND data_entrada >= '{$data_folha_trv}' AND data_entrada < '{$row_folha['data_fim']}' AND id_clt NOT IN({$res_cltpresente})") or die(mysql_error());
    $qtd_cltFaltando = mysql_num_rows($sql_cltFin);
}



?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>:: Intranet :: Folha Sint&eacute;tica de CLT (<?=$folha?>)</title>
<link href="sintetica/folha.css" rel="stylesheet" type="text/css">
<link href="../../favicon.ico" rel="shortcut icon">
<link href="../../js/highslide.css" rel="stylesheet" type="text/css" />
<link href="../../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
<script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
<script src="../../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
<script src="../../js/global.js" type="text/javascript"></script>
<script src="../../js/highslide-with-html.js" type="text/javascript"></script>
<script type="text/javascript">
	hs.graphicsDir = '../../images-box/graphics/'; 
	hs.outlineType = 'rounded-white';
        
$(function(){
       
    $('#filtrar').click(function(){
        var id_funcao = $('#funcoes').val();
        var nome = $('#pesquisa').val().toLowerCase();
        if(id_funcao != ''){
            $('.funcao').each(function(index){
                if($(this).val() == id_funcao){               
                    $(this).parent().parent().show();
                    if(nome != ''){
                        if($(this).next().val().toLowerCase().search(nome) >= 0){               
                            $(this).next().parent().parent().show();               
                        }else {
                            $(this).next().parent().parent().hide(); 
                        }
                    }
                }else {
                    $(this).parent().parent().hide(); 
                }
            })        
        }
        if(nome != '' && id_funcao == ''){
            $('.nome').each(function(index){
                if($(this).val().toLowerCase().search(nome) >= 0){               
                    $(this).parent().parent().show();               
                }else {
                    $(this).parent().parent().hide(); 
                }
            })        
        }
    })   
       
    $('#mostrar_todos').click(function(){        
          $('.funcao').each(function(index){
               $(this).parent().parent().show(); 
          });
          $(".totais").show();
          $("#estatisticas").show();
    })   
   
    /****************MOSTRA TODOS *****************************/
    /*$(".legenda .mostrar_todos").click(function(){
        history.go(0);
    });*/

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
    
    /*********AÇÃO DE LANÇAR MOVIMENTO DE AJUST DE SALDO DEVEDOR**************/
//    $(".valida_saldo_devedor").click(function(){
//        var link = $(this).data("link");
//        if($("#valor_negativo").val() == 1){
//            $("#ajusteSaldoDevedor").show();
//            thickBoxModal("Atenção","#ajusteSaldoDevedor",500,700);
//        }else{
//            $(this).attr({href:link}).trigger("click");
//        }
//    });
    //CLIQUE DO NÃO
    $("body").on("click","#nao",function(){
        thickBoxClose();
    });
    
    
    //CLIQUE DO SIM
    $("body").on("click","#sim",function(){
        var dados = $("#form").serialize();
        link = $(".valida_saldo_devedor").data("link");
        var enc = $(".url").val();
        $.ajax({
           url:"sintetica.php?method=lancaMovimento&enc="+enc+"&" + dados,
           type:"POST",
           dataType:"json",
           success: function(data){
               if(data.status){
                   window.location.href = "http://f71iabassp.com/intranet/rh/folha/sintetica.php?enc="+enc+"&finaliza=" + link;
               }
           }
        });
    });
             
});
   
//IMPRESSAO POR ELEMENTO
function printDiv(divName, divExclusao) {
	var printContents = document.getElementById(divName).innerHTML;
	var originalContents = document.body.innerHTML;
	document.body.innerHTML = printContents;
	$(divExclusao).css('display', 'none');
	window.print();
	$(divExclusao).css('display', 'block');
	document.body.innerHTML = originalContents;
}
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
        .destaque_vermelho{
            background: rgb(253, 193, 193);
        }
        .destaque_vermelho:hover{
            background: rgb(253, 193, 193) !important;
        }
        .icon_lancamento{
            position: absolute;
            top: 10px;
            right: 5px;
            cursor: pointer;
        }
        #ajusteSaldoDevedor{
            display: none;
        }
        
        #ajusteSaldoDevedor p{
            font-family: arial;
            font-size: 13px;
            text-align: left;
            color: #5C5555;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 35px;
            margin-bottom: 10px;
        }
        
        input[type='button']{
            float: left;
            margin: 10px 5px;
            cursor: pointer;
        }
        
        .grid thead th{
            background: #555;
            height: 30;
            text-align: left;
            color: #fff;
            padding: 8px;
            font-size: 12px;
            box-sizing: border-box;
        }
        .grid tbody td{
            height: 30;
            text-align: left;
            padding: 8px;
            font-size: 12px;
            box-sizing: border-box;
            border-bottom: 1px solid #ccc;
        }
        #msg_red{
            /* width: 773px; */
            margin: 20px 18px;
            background-image: linear-gradient(to bottom, #f2dede 0%, #e7c3c3 100%);
            background-repeat: repeat-x;
            border-color: #dca7a7;
            text-shadow: 0 1px 0 rgba(255, 255, 255, .2);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .25), 0 1px 2px rgba(0, 0, 0, .05);
            color: #a94442;
            background-color: #f2dede;
            /* padding-right: 35px; */
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
            text-align: left;
            font-size: 14px;
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
     
    	<table cellpadding="0" cellspacing="1" id="folha" width='100%'>
            <tr>
              <td colspan="2">
                <a href="<?=$link_voltar?>" class="voltar">Voltar</a>
              </td>
              <td colspan="8">
              <?php if(empty($decimo_terceiro)) { ?>
                <div style="float:right;">
                    <div class="legenda"><div class="nota mostrar_todos" id="mostrar_todos"></div>Todos</div>
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
              <td colspan="13">
              		
                  <div>
                      <strong>FUNÇÃO:</strong>
                      <select name="funcoes" id="funcoes">
                          <option value="">Selecione...</option>
                        <?php
                        $qr_funcao = mysql_query("SELECT C.nome, C.id_curso, C.letra, C.numero FROM rh_folha_proc as A
                                                INNER JOIN rh_clt As B
                                                ON B.id_clt = A.id_clt 
                                                INNER JOIN curso as C
                                                ON C.id_curso = B.id_curso
                                                WHERE A.id_folha = '$folha' AND A.status = 2
                                                GROUP BY C.id_curso");
                        while($row_funcao = mysql_fetch_assoc($qr_funcao)){
                         
                            echo '<option value="'.$row_funcao['id_curso'].'">'.$row_funcao['letra'].$row_funcao['numero'] . " " . $row_funcao['nome'].'</option>';
                        }
                        ?> 
                      </select>
                      <input type="text" name="pesquisa" id="pesquisa" placeholder="Nome, Matricula, CPF" value="">
                      <input type="button" name="filtrar" id="filtrar" value="Filtrar"/>
                      <input type="button" name="mostrar_todos" id="mostrar_todos" value="Mostrar todos"/>
                      <?php //<p style="text-align: right; margin-top: 20px"><input type="button" onclick="tableToExcel('folha', 'Folha de Pagamento')" value="Exportar para Excel" class="exportarExcel"></p>  ?>  
                  </div>
                  
                  
                    
                     
                    
<!--                    <table cellspacing="0" cellpadding="0" width="100%">
                     <tr class="secao">
                      <td width="4%">COD</td>
                      <td width="24%" align="left" style="padding-left:5px;">NOME</td>
                      <td width="8%" align="left" style="padding-left:5px;">ADMISSÃO</td>
                      <td width="6%" align="left" style="padding-left:5px;">SALÁRIO</td>
                      <td width="6%"><?php if(!empty($decimo_terceiro)) { echo 'MESES'; } else { echo 'DIAS'; } ?></td>
                      <td width="8%">BASE</td>
                      <td width="15%">SOMA DAS MÉDIAS DE 13°</td>
                      <td width="10%">RENDIMENTOS</td>
                      <td width="10%">DESCONTOS</td>
                      <td width="8%">INSS</td>
                      <td width="8%">IRRF</td>
                      <td width="14%">FAM&Iacute;LIA</td>
                      <td width="10%">L&Iacute;QUIDO</td>
                     </tr>
                    </table>-->
                    
                  <!--BOTÃO EXPORTA PARA EXCEL -->
                  
                    
              </td>
            </tr>
            <tr class="secao">
                <td >COD</td>
                <td align="left" style="padding-left:5px;">NOME</td>
                <td align="left" style="padding-left:5px;">ADMISSÃO</td>
                <td align="left" style="padding-left:5px;">SALÁRIO</td>
                <td ><?php if(!empty($decimo_terceiro)) { echo 'MESES'; } else { echo 'DIAS'; } ?></td>
                <?php if(!empty($decimo_terceiro)){ ?><td >SOMA DAS MÉDIAS DE 13°</td><?php } ?>
                <td >BASE</td>
                <td >RENDIMENTOS</td>
                <td >DESCONTOS</td>
                <td >INSS</td>
                <td >IRRF</td>
                <td >FAM&Iacute;LIA</td>
                <td >L&Iacute;QUIDO</td>
            </tr>
 
<?php   
   

    $saldosNegativos = array();    
    // Início do Loop dos Participantes da Folha
    while($row_participante = mysql_fetch_array($qr_participantes)) {
    
    unset($rendimentos);
//    if($_COOKIE['logado'] == 179){
//        echo "<pre> *********************<br>";
//            print_r($row_participante);
//        echo "</pre>*********************<br>";
//    } 
        
    /***
     * ATUALIZANDO CAMPOS DE agencia_dv e conta_dv 
     * no Folha_proc sempre que atualizar a folha
     * FEITO POR: SINESIO LUIZ
     * EM: 19/08/2016
     */    
    $updateDVContaAgencia = "UPDATE rh_folha_proc SET agencia_dv = '{$row_participante['agencia_dv']}', conta_dv = '{$row_participante['conta_dv']}' WHERE id_folha = '{$row_participante['id_folha']}' AND id_clt = '{$row_participante['id_clt']}';";
    $sqlUpdateDVContaAgencia = mysql_query($updateDVContaAgencia) or die('Erro ao atualizar DV agencia e conta');
    
    
    /**
     * ID CLT 
     */
    $clt = $row_participante['id_clt'];

    /**
     * LINK DE RELATÓRIO
     */
    $relatorio = str_replace('+', '--', encrypt("$clt&$folha"));
    
    include('sintetica/calculos_folha.php'); 

    //CRIANDO UM ARRAY DE SALÁRIOS LIQUIDOS NEGATIVOS
    if($liquido < 0){
        $saldosNegativos[$folha][$clt]["nome"] = $row_clt['nome'];
        $saldosNegativos[$folha][$clt]["salario"] = $liquido;
    }
    
    if($liquido < 0){
        $liquido = 0;
    }
    
    $arraySalarioIncompativel[$clt]['liquido'] = $liquido;
    $arraySalarioIncompativel[$clt]['nome'] = $row_clt['nome'];
    
?>
    
            
    <tr class="linha_<?php if($linha++%2==0) { echo 'um'; } else { echo 'dois'; } ?> destaque ">
            <td >
                 <?=$clt?>
                <input type="hidden" name="url" class="url" value="<?php echo $_REQUEST['enc']; ?>" />
                <input type="hidden" name="id_funcao" class="funcao" value="<?php echo $row_clt['id_curso'];?>"/>
                <input type="hidden" name="nome" class="nome" value="<?php echo $row_clt['nome'];?> <?php echo $row_clt['matricula'];?> <?php echo $row_clt['cpf'];?> <?php echo str_replace('-','',str_replace('.','',$row_clt['cpf']));?>"/>
            </td>
            <td  align="left">
                <?php if(empty($num_rescisao)){ ?>
                    <a style="text-align: center" href="sintetica/relatorio<?php if(!empty($decimo_terceiro)) { echo '_dt'; } ?>.php?enc=<?=$relatorio?>" onClick="return hs.htmlExpand(this, { objectType: 'iframe' } )" class="participante" >
                        <span title="Ver relatório de <?=$row_clt['nome']?>" class="
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
                                } elseif(isset($dias_faltas)) {
                                    echo 'faltas';
                                    $array_totais["faltas"]["base"] += $row_participante['sallimpo_real'] + $row_participante['valor_dt'];
                                    $array_totais["faltas"]["rendimento"] += $row_participante[$indice];
                                } else { 
                                    echo 'normal';
                                } 
                            ?>
                        "><?php echo abreviacao($row_clt['nome'], 4, 1);?>
                        </span>
                        <img src="sintetica/seta_<?php if($seta++%2==0) { echo 'um'; } else { echo 'dois'; } ?>.gif">
                    </a>
                <?php }else{ ?>
                
                    <?php
                    
                    
                        $qr_rescisao = mysql_query("SELECT *, date_format(data_demi, '%d/%m/%Y') AS data_demi2 FROM rh_recisao WHERE id_clt = '{$clt}' AND rescisao_complementar != 1   AND status = '1'");
                        $row_rescisao = mysql_fetch_array($qr_rescisao);
                        $link = str_replace('+', '--', encrypt("$row_participante[id_regiao]&$clt&$row_rescisao[0]"));

                        if (substr($row_rescisao['data_proc'], 0, 10) >= '2013-04-04') {
                            $link_nova_rescisao = "nova_rescisao_2.php?enc=$link";
                        } else {
                            $link_nova_rescisao = "nova_rescisao.php?enc=$link";
                        }
                        
                    
                    ?>
                    
                    <a style="text-align:center" href="../recisao/<?php echo $link_nova_rescisao; ?>" onClick="return hs.htmlExpand(this, { objectType: 'iframe' } )" class="participante" >
                        <span title="Ver relatório de <?=$row_clt['nome']?>" class="
                            <?php 		
                                if(!empty($num_rescisao)) {
                                    echo 'rescisao';
                                    $array_totais["rescisao"]["base"] += $row_participante['sallimpo_real'] + $row_participante['valor_dt'];
                                    $array_totais["rescisao"]["rendimento"] += $row_participante[$indice];
                                }
                            ?>
                        "><?php echo abreviacao($row_clt['nome'], 4, 1); ?>
                        </span>
                        <img src="sintetica/seta_<?php if($seta++%2==0) { echo 'um'; } else { echo 'dois'; } ?>.gif">
                    </a>   
                <?php } ?>
                
            <?php
            if($_COOKIE['logado'] == 87 || $_COOKIE['logado'] == 255){    

              echo $salario_teste;  
              echo 'BASE INSS: '.$base_inss;
              echo '<br> BASE 13 rescisao: '.$base_inss_13_rescisao;
              echo '<br> BASE FGTS '.$base_fgts;
              echo '<br> BASE FÉRIAS '.$base_fgts_ferias;
            }
            ?>        
        </td>
        <td ><?php echo $row_participante['entrada']; ?> </td>
        <td ><?php echo number_format($row_participante['salario'],2,",","."); ?> </td>
        <td ><?php if(!empty($decimo_terceiro)) { echo $meses; } else { echo $dias; } ?> </td>
        <?php if(!empty($decimo_terceiro)){ ?><td ><?=formato_real($movMedias['total_media_13'])?></td><?php  } ?>
        <td ><?php if(!empty($decimo_terceiro)) { echo formato_real($decimo_terceiro_credito); } else { echo formato_real($salario); } ?></td>
        <td ><?=formato_real($rendimentos)?></td>
        <td ><?=formato_real($descontos)?></td>
        <td><?=formato_real($inss_completo)?></td>
        <td ><?=formato_real($irrf_completo)?></td>
        <td ><?=formato_real($familia)?></td>
        <td ><?php echo formato_real($liquido);  ?></td>
   </tr>
    
    <?php include('sintetica/update_participante.php');
        include('sintetica/totalizadores_resets.php');

        // Fim do Loop de Participantes
        unset($sem_mov_sempre);
        
//        if($_COOKIE['logado'] == 179){
//            //exit();
//        }
        
     } ?>

    <?php
        if(count($saldosNegativos) > 0){
            
            if($_COOKIE['debug'] == 666){
                
                echo '/////////////////ARRAY SALDO DEVEDOR ($saldosNegativos)/////////////////';
                echo '<pre>';
                print_array($saldosNegativos);
                echo '</pre>';
                
            }
            
            echo "<input type='hidden' name='valor_negativo' id='valor_negativo' value='1' />";
        }
    ?>
                
<?php foreach ($array_totais as $key => $total){ ?>
    <?php if($_COOKIE['logado'] == 204){print_r($array_totais);} ?>
    <tr class="totais_<?php echo $key; ?> esconde_geral">
        <td colspan="7">
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
        <?php $colunas = 0; if($decimo_terceiro === 1){ $colunas = 5;}else{$colunas = 4;} ?>        
      	<tr class="totais">
        <td colspan="<?php echo $colunas; ?>">
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
    <div id="ajusteSaldoDevedor">
        <form name="form" id="form">
            <input type="hidden" name="folhaSaldoDevedor" id="folhaSaldoDevedor" value="<?php echo key($saldosNegativos); ?>" />  
            <p>Foram encontrados os seguintes CLTs com saldo negativo.</p>
            <?php foreach($saldosNegativos as $key => $values){ ?>
                <table id="tbClts" border="0" cellpadding="0" cellspacing="0" class="grid" width="100%" >
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        <?php foreach($values as $k => $clt){  ?>
                            <tr>
                                <td>
                                <input type="hidden" name="id_clt[]" value="<?php echo $k; ?>" />
                                <input type="hidden" name="salario_clt[<?php echo $k ?>]" value="<?php echo abs($clt['salario']); ?>" />
                                <?php echo $k; ?>
                                </td>
                                <td><?php echo $clt['nome']; ?></td>
                                <td><?php echo "R$ " . number_format($clt['salario'],2,',','.'); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            <?php } ?>
            <p>Deseja lançar um movimento de Ajuste de Saldo Devedor para finalizar a Folha ?</p>
            <input type="button" name="sim" id="sim" value="Sim" />
            <input type="button" name="nao" id="nao" value="Não" />
        </form>
    </div>
    <?php include('sintetica/estatisticas_folha.php'); ?>
</div>
<?php include('sintetica/updates.php'); 


?>
<?php
    if(isset($_REQUEST['finaliza'])){
        echo "<script>window.location.href ='" . $_REQUEST['finaliza'] . "'</script>";
    }
?>

</body>
</html>
