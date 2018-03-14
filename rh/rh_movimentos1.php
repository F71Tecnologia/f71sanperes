<?php
/*
 * 00/00/0000
 * 
 * Rotina para exibição de todos os movimentos de um Clt
 * 
 * Versão: 3.0.0000 - 00/00/0000 - 
 * Versão: 3.0.0001 - 14/08/2015 - Jacques - Adicionado checkbox para definição e lançamento de faltas justificadas a título de histórico
 * Versão: 3.0.0002 - 27/08/2015 - Jacques - Desativado o método de inibição dos checkbox (check_mes_anterior, check_falta_digitada e valor_falta_digitada) 
 *                                           para inclusão de valores.
 * 
 */

if(empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='../login.php'>Logar</a>";
    exit;
}

include "../conn.php";
include "../funcoes.php";
include "../wfunction.php";
include "../classes/global.php";

$global = new GlobalClass();

$usuario = carregaUsuario();

$regiao = (!empty($_REQUEST['regiao'])) ? $_REQUEST['regiao'] : $usuario['id_regiao'];
$tela = (!empty($_REQUEST['tela'])) ? $_REQUEST['tela'] : 1;

$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"3", "area"=>"Recursos Humanos", "id_form"=>"form-lista", "ativo"=>"Movimentos");
$breadcrumb_pages = array("Gestão de RH"=>"/intranet/rh/principalrh.php");

$filtro = false;

if ((isset($_REQUEST['filtrar']) && !empty($_REQUEST['filtrar']))){
    $filtro = true;
    
    $projeto = $_REQUEST['projeto'];
    $pesquisa = $_REQUEST['pesquisa'];
    
    if($pesquisa != ''){
        $and = "AND A.nome LIKE '%{$pesquisa}%'";
    }
    
    $result_clt = mysql_query("SELECT A.*, DATE_FORMAT(A.data_entrada, '%d/%m/%Y') AS dataentrada2, B.nome AS nome_curso
            FROM rh_clt AS A
            LEFT JOIN curso AS B ON(A.id_curso = B.id_curso)
            WHERE A.id_projeto = {$projeto} AND (A.status < '60' OR A.status = '200') {$and}
            ORDER BY A.nome ASC");
    $tot = mysql_num_rows($result_clt);
    
//    $qry = $centrocusto->getCentroCusto($regiao, $pesquisa);
//    $tot = mysql_num_rows($qry);
}

switch($tela) {
    case 2:
    // Recebendo a variável criptografada
    $enc = $_REQUEST['enc'];
    $enc = str_replace("--","+",$enc);
    $link1 = decrypt($enc); 

    $teste = explode("&",$link1);
    $regiao = $teste[0];
    $clt = $teste[1];
    $telaF = 3;
    $linkF = encrypt("$regiao&$telaF&$clt");
    $linkF = str_replace("+","--",$linkF);   
}

/**
 * FEITO POR: SINÉSIO LUIZ
 * 17/06/2015
 * CADASTRO DE MOVIMENTO DE REREMBOLSO
 */
if(isset($_REQUEST['method']) && $_REQUEST['method'] != ""){
    if($_REQUEST['method'] == "cadMovReembolso"){
        $return = array("status" => 0);
            $valor = number_format($_REQUEST['valor'],'2','.','');
            $query = "INSERT INTO rh_movimentos_clt (
                        id_clt,id_regiao,id_projeto,mes_mov,ano_mov,id_mov,cod_movimento,tipo_movimento, nome_movimento,
                        data_movimento,user_cad,valor_movimento,lancamento,incidencia,qnt,status,status_folha,status_ferias,
                        status_reg,qnt_horas) VALUES ( 
                        '{$_REQUEST['clt']}','{$_REQUEST['regiao']}','{$_REQUEST['projeto']}','{$_REQUEST['mes']}','{$_REQUEST['ano']}','229','50244','CREDITO','REEMBOLSO DE FALTAS',
                        NOW(),'{$_COOKIE['logado']}','{$valor}','1','5020,5021,5023','{$_REQUEST['qnt']}','1','0','1','1','')";
            if(mysql_query($query)){
                $return = array("status" => 1);
            }
        
        echo json_encode($return);
        exit();
    }
}



?>
<html>
<head>
    <title>:: Intranet :: Movimentos</title>
    <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
    <link rel="shortcut icon" href="../favicon.ico">

    <!-- Bootstrap -->
    <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css">
    <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
    <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
    <link href="../resources/css/main.css" rel="stylesheet" media="all">
    <link href="../resources/css/font-awesome.css" rel="stylesheet" media="all">
    <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" >
    <link href="../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="all">
    <link href="../css/progress.css" rel="stylesheet" type="text/css">
    <link href="../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">

    <script language="javascript" type="text/javascript" src="../js/ramon.js"></script>
    <script src="../js/jquery-1.10.2.min.js"></script>
    <script src="../resources/js/bootstrap.min.js"></script>
    <script src="../resources/js/bootstrap-dialog.min.js"></script>
    <script src="../js/jquery.validationEngine-2.6.js"></script>
    <script src="../js/jquery.validationEngine-pt_BR-2.6.js"></script>
    <script src="../resources/js/main.js"></script>
    <script language="javascript" type="text/javascript" src="../js/jquery-ui-1.9.2.custom.min.js"></script>
    <script language="javascript" type="text/javascript" src="../js/global.js?1"></script>    
    <script language="javascript" type="text/javascript" src="../js/jquery.maskMoney.js"></script>
                    
    <script language="javascript" type="text/javascript" >
        $(function(){
            
            $(".gestao_faltas_atrasos").on("click", function () {
                
                var t = $(this);
                var id_clt;
                
                id_clt = t.data("clt");
                
                $("#id_clt").val(id_clt);
                $("#form-lista")
                        .attr("action", "faltas_atrasos/")
                        .submit();
                
            });
            
            $('input[name=horas_faltas]').change(function(){
                if($(this).attr('checked')){
                    $('.quantidade_horas_falta').show();
                } else {
                    $('.quantidade_horas_falta').hide();
                }
            });

            $('input[name=mov1]').change(function(){

                var id_mov = $(this).val();
                if(id_mov == 149){
                    document.all.valor.style.display = 'none';
                    document.getElementById("mostrartexto3").innerText = "30% do salario";
                    document.all.valor.value = "0";	
                }else {
                    document.all.valor.style.display = '';
                    document.getElementById("mostrartexto3").innerText = "";
                    document.all.valor.value = "0";	
                }    

                ///QUANTIDADE
                if(id_mov == 152){
                    $('.quantidade').show();
                } else{  
                    $('.quantidade').hide();
                }

                if(id_mov == 66 || id_mov == 56 || id_mov == 150 || id_mov == 152 || id_mov == 66 || id_mov == 193 || id_mov == 14
                        || id_mov == 151 || id_mov == 196 || id_mov == 57 || id_mov == 149 || id_mov == 192 || id_mov == 197 || id_mov == 199 || id_mov == 202 || id_mov == 203 
                        || id_mov == 201 || id_mov == 204 || id_mov == 205 || id_mov == 206 || id_mov == 228 || id_mov == 232 || id_mov == 209 || id_mov == 247 || id_mov == 248|| id_mov == 254 || id_mov == 831 || id_mov == 229 || id_mov == 76 || id_mov == 832 || id_mov == 840 ){
                    document.getElementById("inc1").checked = true;
                    document.getElementById("inc2").checked = true;
                    document.getElementById("inc3").checked = true;
                    $("#mostrartexto1").html( "INSS - IRRF - FGTS");
                }else{
                    document.getElementById("inc1").checked = false;
                    document.getElementById("inc2").checked = false;
                    document.getElementById("inc3").checked = false;
                    $("#mostrartexto1").html("NENHUMA INCIDENCIA");
                }
            });

            $('input[name=mov2]').change(function(){

               var id_mov = $(this).val();

               if(id_mov == 253){
                    $('.dias_debito').show();               
                    $('.valor_digitado').hide();               
               } else {
                    $('.dias_debito').hide(); 
                    $('.dias_debito').val(''); 
                     $('.valor_digitado').show();    
               }
               /**
                * FEITO POR SINÉSIO LUIZ, 31/07/2015
                * REMOVI AS INCIDÊNCIAS DOS MOVIMENTOS DE PENSÃO ALIMENTÍCIA, A PEDIDO DA JOSIE.
                * 
                */
               if(id_mov == 224 ||  id_mov == 253 ){

                   document.getElementById("inc4").checked = true;
                    document.getElementById("inc5").checked = true;
                    document.getElementById("inc6").checked = true;
                    document.getElementById("mostrartexto2").innerText = "INSS - IRRF - FGTS";
                    document.all.valor2.style.display = '';
               } else {
                    document.getElementById("inc4").checked = false;
                    document.getElementById("inc5").checked = false;
                    document.getElementById("inc6").checked = false;
                    document.getElementById("mostrartexto2").innerText = "NENHUMA INCIDENCIA";
                    document.all.valor2.style.display = '';
               }
            });

            $('#check_falta_digitada').click(function(){

                if($(this).attr('checked')){
                    $('#falta_digitada').show();
                } else {
                    $('#falta_digitada').hide();
                    $('#valor_falta_digitada').val('');
                }

            });	

            $('#check_falta_justificada').click(function(){


                if($(this).attr('checked')){

    //                $('#check_mes_anterior').hide();  
    //                $('#label_mes_anterior').hide();  
    //                
    //                $('#check_falta_digitada').hide();
    //                $('#label_falta_digitada').hide();
    //                $('#check_falta_digitada').attr('checked',false);
    //                
    //                $('#falta_digitada').hide();
    //                $('#valor_falta_digitada').val('');

                } else {

    //                $('#check_mes_anterior').show();  
    //                $('#label_mes_anterior').show();  
    //                
    //                $('#check_falta_digitada').show();
    //                $('#label_falta_digitada').show();


                }

            });	



            /** DESCONTO DE INSS EM OUTRA EMPRESA **/
            $('input[name=desconto_inss]').change(function(){

               if($(this).val() == 1){
                   $('.tabela_desconto').show();
               } else {
                  $('.tabela_desconto').hide();
                    $('#salario_outra_empresa').val('');
                    $('#desconto_outra_empresa').val('');
                    $('select[name=tipo_desconto_inss]').val('');
                    $('.outra_empresa').hide();
                    $('input[name=trabalha_outra_empresa]').attr('checked',false);
               }       
            });

            $('input[name=trabalha_outra_empresa]').change(function(){

                if($(this).val() == 'sim'){
                    $('.outra_empresa').show();
                } else {
                      $('.outra_empresa').hide();
                      $('#salario_outra_empresa').val('');
                      $('#desconto_outra_empresa').val('');
                }
            });   

            $('input[name=calcular_inss]').click(function(){

                var valor = $('#salario_outra_empresa').val();  
                if(valor != 0) {  
                    $.ajax({                        
                        url : 'action.calculos.php?inss=1&valor='+valor,
                        success :function(resposta){			
                            $('#desconto_outra_empresa').val(resposta);
                        }		
                    });
                }
            });


            //******************** 17-06-2015 - MODAL DE REEMBOLSO********************//	

            $(".maskMoney").maskMoney({
                showSymbol:true, 
                symbol:"R$ ", 
                decimal:".", 
                thousands:""
            });

            /**
             * ABRINDO MODAL
             * @returns {Boolean}
             */
            $("input[name='mov1']").click(function(){
                //FLAG PARA CHAMAR MODAL
                var flag        = $(this).attr("data-modal");
                //MES SELECIONADO NO GRUPO CREDITO
                var mesSelected = $(".mesMovCredito :selected").val(); 
                $("input[name='mesMov']").val(mesSelected);
                //ANO SELECIONADO NO GRUPO CREDITO
                var anoSelected = $(".anoMovCredito :selected").val(); 
                $("input[name='anoMov']").val(anoSelected);
                //MODAL COM FORM DE CADASTRO
                if(flag == 1){
                    thickBoxModal("Lançar Movimento", "#modal_reembolso_faltas", 280, 500);
                }
            });

            /**
             * CADASTRO
             * @returns {Boolean}
             */
            $(".cadValorDiasReembolso").click(function(){
                var quant   = $("input[name='quant_dias_reembolso']").val();
                var valor   = $("input[name='valor_dias_reembolso']").val();
                var regiao  = $("input[name='regiaoMov']").val();
                var projeto = $("input[name='projetoMov']").val();
                var mes     = $("input[name='mesMov']").val();
                var ano     = $("input[name='anoMov']").val();
                var clt     = $("input[name='cltSelected']").val();

                if($("#formCadMov").validationEngine('validate')){
                    $.ajax({
                        url:"",
                        type:"POST",
                        dataType:"json",
                        data:{
                             qnt:quant,
                             valor:valor,
                             regiao:regiao,
                             projeto:projeto,
                             mes:mes,
                             ano:ano,
                             clt:clt,
                             method:"cadMovReembolso"
                        },
                        success: function(data){
                            if(data.status){
                                history.go();
                            }
                        }
                    });           
                }

            });
            //**********************************************************************//	
        });




        /**
         * 
         * @returns {Boolean}
         */
        function valida1(){
            d = document.form1;
            if($("input[name=mov1]:checked").length == 0) {
                alert ("Escolha um TIPO DE MOVIMENTO de CRÉDITO");
                document.getElementById('tabelacredito').className = "style7 linhastabela2";
                return false;
            } 
            if($('#valor').val() == ''){
                alert("O campo VALOR deve ser preenchido!");
                document.getElementById('tabelacredito').className = "style7 linhastabela2";
                d.valor.focus();
                return false;
            }	
        }

        /**
         * 
         * @returns {Boolean}
         */
        function valida2(){
            if($("input[name=mov2]:checked").length == 0) {
                alert ("Escolha um TIPO DE MOVIMENTO de DESCONTO");
                document.getElementById('tabeladebito').className = "style7 linhastabela2";
                return false;
            }

            if ($('#valor2').val() == ''){
                if($("input[name=mov2]:checked").val() != 253){
                    alert("O campo Valor deve ser preenchido!");
                    d.valor2.focus();
                    return false;
                }
            }	
            return true;   
        }

        /**
         * 
         * @returns {Boolean}
         */
        function validaFALTA(){
            d = document.frmfaltas;
            if (d.faltas.value == "" ){
                alert("O campo QUANTIDADE DE FALTAS deve ser preenchido!");
                d.faltas.focus();
                return false;
            }	
            return true;   
        }
    </script>
</head>
<body>
    
    <?php include("../template/navbar_default.php"); ?>
    
    <div class="container">
            
        <div class="row">
            <div class="col-lg-12">
                <div class="page-header box-rh-header"><h2><span class="fa fa-users"></span> - RECURSOS HUMANOS<small> - Movimentos</small></h2></div>
                
<!--                     Nav tabs 
                <ul class="nav nav-tabs" role="tablist" style="margin-bottom: 15px;">
                    <li role="presentation" class="active"><a href="#avisos" role="tab" data-toggle="tab">Avisos</a></li>
                    <li role="presentation"><a href="#lista" role="tab" data-toggle="tab">Lista de Funcionários</a></li>
                    <li role="presentation"><a href="#relatorio" role="tab" data-toggle="tab">Relatório de Férias</a></li>
                </ul>-->
                
                <!--resposta de algum metodo realizado-->
                <?php echo $global->getResposta($_SESSION['MESSAGE_TYPE'], $_SESSION['MESSAGE']); ?>
                
                <!-- Tab panes -->
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="lista">
                        
                        <form class="form-horizontal" role="form" id="form-lista" method="post" autocomplete="off">
                            <input type="hidden" name="home" id="home" value="" />
                            <input type="hidden" name="regiao" id="regiao" value="<?= $id_regiao ?>">
                            <input type="hidden" name="id_clt" id="id_clt" value="">
                            <div class="panel panel-default hidden-print">
                                <div class="panel-body">

                                    <div class="form-group">
                                        <label for="categoria_lista" class="col-lg-2 control-label">Projeto:</label>
                                        <div class="col-lg-9">                                                
                                            <?php echo montaSelect(getProjetos($regiao),$projeto, "id='projeto' name='projeto' class='form-control'"); ?>                                                
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="nome_centrocusto" class="col-lg-2 control-label">Filtro:</label>
                                        <div class="col-lg-9"><input type="text" name="pesquisa" id="pesquisa" class="form-control" placeholder="Nome do Funcionário" value="<?php echo $pesquisa; ?>"></div>
                                    </div>

                                </div><!-- /.panel-body -->

                                <div class="panel-footer text-right">
                                    <a href="importar_xls_movimentos.php" target="_blank" class="btn btn-warning">Importar Planilha</a>
                                    <input type="submit" value="Consultar" id="submit-lista" name="filtrar" class="btn btn-primary">
                                </div>

                            </div><!-- /.panel -->

                            <?php 
                            if($filtro){
                                if($tot > 0){
                            ?>
                            
                            <p class="pull-right">
                                <button type="button" onclick="tableToExcel('tbRelatorio', 'Movimentos')" class="btn btn-success"><span class="fa fa-file-excel-o"></span> Exportar para Excel</button>
                            </p>
                            
                            <table class="table table-striped table-hover table-condensed table-bordered" id="tbRelatorio" style="font-size: 14px;">
                                <thead>
                                    <tr class="bg-primary valign-middle">
                                        <th>COD</th>
                                        <th>NOME</th>
                                        <th>CARGO</th>                                        
                                        <th>DATA DE ADMISSÃO</th>
                                        <!--<th></th>-->
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    while($res = mysql_fetch_assoc($result_clt)){
                                        $link_ = "rh_movimentos_3.php?tela=2&pg=0&clt={$res['id_clt']}&regiao={$regiao}&projeto={$projeto}";
                                        
                                        if($res['status'] == '40') {
                                            $span = "&nbsp;<span class='label label-primary'>Em Férias</span>";
                                        } elseif($res['status'] == '200') {
                                            $span = "&nbsp;<span class='label label-danger'>Aguardando Demissão</span>";
                                        }
                                    ?>
                                    <tr class="valign-middle">
                                        <td><?php echo $res['id_clt']; ?></td>
                                        <td><a href="<?php echo $link_; ?>"><?php echo $res['nome']; ?></a><?php echo $span; ?></td>
                                        <td><?php echo $res['nome_curso']; ?></td>
                                        <td><?php echo $res['dataentrada2']; ?></td>
                                        <!--<td class="text-center"><button type="button" title="Gestão de Faltas e Atrasos" class="gestao_faltas_atrasos btn btn-info" data-clt="<?php echo $res['id_clt']; ?>"><i class="fa fa-calendar"></i></button></td>-->
                                    </tr>
                                    <?php
                                    unset($span);
                                    } ?>
                                </tbody>
                            </table>
                            
                            <?php
                                }else{
                                    echo $global->getResposta('danger', 'Nenhum cadastrado encontrado');
                                }
                            } ?>
                            
                        </form>
                        
                    </div><!-- /#lista -->                       
                </div>

            </div><!-- /.col-lg-12 -->
        </div><!-- /.row -->
        
        <?php include_once '../template/footer.php'; ?>
    </div><!-- /.container -->
    
    <?php exit(); ?>
    
<div id="corpo">
<div id="topo" style="width:95%; margin:0px auto; font-family:Arial;">

<div style="float:right; margin-right:7px;">

    	<?php include('../reportar_erro.php'); ?>
 
</div>
<div style="clear:right"></div>

	<div style="float:left; width:25%;">
    <?php
	switch($tela) {
	case 1:
		echo "<a href='../principalrh.php?regiao=$regiao'>";
	break;
	case 2:
		if(isset($_GET['ferias'])) {
    		echo "<a href='ferias/index.php?enc=$linkF'>";
    	} else {
    		echo "<a href='rh_movimentos.php?regiao=$regiao&tela=1'>";
    	}
	break;
	} ?>
        	<img src='../imagens/voltar.gif' border='0'>
        </a>
    </div>
    
	<div style="float:left; width:50%; text-align:center; font-size:24px; font-weight:bold; color:#000;">
    	MOVIMENTOS
    </div>
	<div style="float:right; width:25%; text-align:right; font-size:12px; color:#333;">
    	<br><b>Data:</b> <?=date('d/m/Y')?>&nbsp;
    </div>
	<div style="clear:both;"></div>
</div>

<?php
switch($tela) {
	case 1:
    
	$total_clt = NULL;
	$qr_projetos = mysql_query("SELECT * FROM projeto WHERE id_regiao = '$regiao' AND status_reg = '1' ORDER BY nome ASC");
	while($projetos = mysql_fetch_array($qr_projetos)) {
    
	$result_clt = mysql_query("SELECT *, date_format(data_entrada, '%d/%m/%Y') AS data_entrada2, date_format(data_saida, '%d/%m/%Y') AS data_saida2 FROM rh_clt WHERE id_projeto = '$projetos[0]' AND (status < '60' OR status = '200') ORDER BY nome ASC");
	$num_clt = mysql_num_rows($result_clt);
	
	if(!empty($num_clt)) {
	$total_clt++; ?>

    <table cellpadding="8" cellspacing="0" style="width:95%; border:0px; background-color:#f5f5f5; margin:20px auto;">
        <tr>
          <td colspan="6" class="show">
            &nbsp;<span style='color:#F90; font-size:32px;'>&#8250;</span> <?=$projetos['nome']?>
          </td>
        </tr>
        <tr class="novo_tr">
          <td width="5%">COD</td>
          <td width="32%">NOME</td>
          <td width="30%">CARGO</td>
          <td width="33%">UNIDADE</td>
          <td width="33%">DATA DE ADMISSÃO</td>
        </tr>
        
  <?php while($row_clt = mysql_fetch_array($result_clt)) {
		
		$result_curso = mysql_query("SELECT * FROM curso WHERE id_curso = '$row_clt[id_curso]'");
		$row_curso = mysql_fetch_array($result_curso);

		// Encriptografando a Variável
		$link2 = encrypt("$regiao&$row_clt[0]"); 
		$link3 = str_replace("+","--",$link2);
		//----------------------------
	
		if(isset($_GET['ferias'])) {
			$link4 = "rh_movimentos.php?ferias=true&tela=2&enc=$link3";
		} else {
			$link4 = "rh_movimentos.php?tela=2&enc=$link3";
		} ?>
	
      <tr style="background-color:<?php if($alternateColor++%2!=0) { echo "#F0F0F0"; } else { echo "#FDFDFD"; } ?>">
		 <td><?=$row_clt['id_clt']?></td>
		 <td><a href="<?=$link4?>"><?=$row_clt['nome']?></a> 
		 <?php if($row_clt['status'] == '40') { 
   					echo '<span style="color:#069; font-weight:bold;">(Em Férias)</span>';
   			   } elseif($row_clt['status'] == '200') {
	   				echo '<span style="color:red; font-weight:bold;">(Aguardando Demissão)</span>';
   			   } ?></td>
		 <td><?=$row_curso['nome']?></td>
		 <td><?=$row_clt['locacao']?></td>
		 <td><?=$row_clt['data_entrada2']?></td>
   	  </tr>
   
   <?php } 
} ?>
   
</table>
<?php }

	// Se não tem nenhum CLT na região
	if(empty($total_clt)) { ?>
    
      <META HTTP-EQUIV=Refresh CONTENT="2; URL=/intranet/principalrh.php?regiao=<?=$regiao?>&id=1">
      <p style="color:#C30; font-size:12px; font-weight:bold; margin:30px auto; width:50%; text-align:center;">
               Obs: A região não possui participantes CLTs.
      </p>
      
	<?php } else { ?>

        <div style="width:95%; margin:0px auto; font-size:13px; padding-bottom:4px; margin-top:15px; text-align:right;">
            <a href="#corpo" title="Subir navegação">Subir ao topo</a>
        </div>
    
    <?php }
	
break;
case 2:

$meses = array('-','JANEIRO','FEVEREIRO','MARÇO','ABRIL','MAIO','JUNHO','JULHO','AGOSTO','SETEMBRO','OUTUBRO','NOVEMBRO','DEZEMBRO','13º NOV.','13º DEZ.','13º INT.','RESCISÃO','RESCISÃO COMPLEMENTAR');

$ar_incidencia = array(5020=>'INSS',5021=>'IRRF',5023=>'FGTS',9999=>'ENCARGOS',7004=>'REP.REMUNERADO',5001=>'INF. RENDIMENTOS');

// Selecionando os dados do CLT
$result_clt = mysql_query("SELECT *, date_format(data_entrada, '%d/%m/%Y') AS data_entrada2, date_format(data_saida, '%d/%m/%Y') AS data_saida2 FROM rh_clt WHERE id_clt = '$clt'");
$row_clt = mysql_fetch_array($result_clt);
//----------------------------

// Selecionando os Dependentes
$result_depe = mysql_query("SELECT *,date_format(data1, '%d/%m/%Y') AS data1, date_format(data2, '%d/%m/%Y') AS data2, date_format(data3, '%d/%m/%Y') AS data3, date_format(data4, '%d/%m/%Y') AS data4, date_format(data5, '%d/%m/%Y') AS data5 FROM dependentes WHERE id_bolsista = '$clt' AND id_projeto = '$row_clt[id_projeto]'");
$row_depe = mysql_fetch_array($result_depe);
$num_row_depe = mysql_num_rows($result_depe);
//------------------------------------------------------------

// Verificando qual Folha entrará todos os Movimentos Lançados
$result_folhas = mysql_query("SELECT MAX(mes) FROM rh_folha WHERE regiao = '$regiao' AND status = '3' AND projeto = '$row_clt[id_projeto]'");
$row_folhas = mysql_fetch_array($result_folhas);
$mes_mov = $row_folhas['0'] + 1;
//---------------------

// Selecionando o Curso
$result_curso = mysql_query("SELECT * FROM curso WHERE id_curso = $row_clt[id_curso]");
$row_curso = mysql_fetch_array($result_curso);

$qr_prox_folha = mysql_query("SELECT  if(mes = 12, 1, (mes+1)) as proxima_folha, mes, ano FROM rh_folha WHERE regiao = '$regiao' AND projeto = '$row_clt[id_projeto]' AND status = 3 AND terceiro != 1 ORDER BY ano DESC, mes DESC LIMIT 1") or die(mysql_error());
$row_prox_folha = mysql_fetch_assoc($qr_prox_folha);

// Data Corrente
$data = date('d/m/Y');

//-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=- FUNCAO PARA CALCULAR IDADE

function CalcularIdade($nascimento) {

$hoje = date("d/m/Y"); //pega a data d ehoje
$aniv = explode("/", $nascimento); //separa a data de nascimento em array, utilizando o símbolo de - como separador
$atual = explode("/", $hoje); //separa a data de hoje em array
  
$idade = $atual[2] - $aniv[2];

//verifica se o mês de nascimento é maior que o mês atual
if($aniv[1] > $atual[1]) { 
    
	//tira um ano, já que ele não fez aniversário ainda
	$idade--; 

//verifica se o dia de hoje é maior que o dia do aniversário
} elseif($aniv[1] == $atual[1] && $aniv[0] > $atual[0]) { 
    
	//tira um ano se não fez aniversário ainda
	$idade--; 

}

//retorna a idade da pessoa em anos
return $idade; 
}

//------------------ FUNCAO PARA CALCULAR IDADE

//-- INICIANDO CALCULOS

$salario_calc = $row_curso['salario'];

// ------------ VERIFICANDO SE TEM FILHOS PARA CALCULAR A DEDUÇÃO DO IMPOSTO DE RENDA
if($num_row_depe != 0 && !empty($row_depe['data1'])) { 

$nomes_ar = array($row_depe['nome1'], $row_depe['nome2'], $row_depe['nome3'], $row_depe['nome4'], $row_depe['nome5']);
$cont_nomes_vazios = array_count_values($nomes_ar);
$cont_nomes_vazios = $cont_nomes_vazios[''];

$datas_ar = array($row_depe['data1'], $row_depe['data2'], $row_depe['data3'], $row_depe['data4'], $row_depe['data5']);

$num_row_depe = @array_count_values($datas_ar);

if($num_row_depe["00/00/0000"] == "5"){
	$tabela_depe = "display:none;";
	$mostra_depe = "0";
}else{
	$tabela_depe = NULL;
	$mostra_depe = "1";

for ($i = 0; $i <= 4; $i++) {
	if($datas_ar[$i] != "00/00/0000"){
		$style[$i] = "";
		$idade[$i] = CalcularIdade($datas_ar[$i]);
	}else{
		 $style[$i] = "style='display:none'";
	}
	
	//------------- DEDUÇÃO DO IMPOSTO DE RENDA ----------------//
	$contagem_menor_idade = "0";
	if($idade[$i] < "21" and $datas_ar[$i] != "00/00/0000") {
		$resposta[$i] = '<span style="color:#039;">Menor de 21 Anos</span>';
	} else {
		$resposta[$i] = 'Maior de 21 Anos';
	}			
}

$result_valor_deducao = mysql_query("SELECT * FROM rh_movimentos WHERE cod = '5049'");
$row_valor_deducao = mysql_fetch_array($result_valor_deducao);

$total_menor = @array_count_values($resposta); 
$totaldeducao = $total_menor['<font color=#993300>Menor de 21 Anos</font>'] * $row_valor_deducao['fixo'];

}

} else {
	$tabela_depe = "display:none;";
}

// ----------- TERMINA TUDO SOBRE DEPENDENTES
?>

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center">
  
    
    	<table cellpadding="0" cellspacing="0" style="width:95%; border:0px; background-color:#f5f5f5; margin-top:20px;">
  		  <tr>
    	    <td>
              <div align="center" style="font-family:Arial; font-size:18px; color:#FFF; background:#036">
                 <?php echo $clt." - ".$row_clt['nome']; ?>
              </div>
              <div align="center" style="font-family:Arial; font-size:13px; background:#efefef; padding:4px;">
                 <?php echo "<b>Unidade:</b> ".$row_clt['locacao']."<br><b>Atividade:</b> ".$row_curso['nome']."<br><b>Salário Contratual:</b> R$ ".number_format($row_curso['salario'], '2', ',', '.'); ?>
              </div>
              <div align="center" style="font-family:Arial; font-size:13px; background:#efefef; padding:0px; <?=$tabela_depe?>" id="tabeladepe">
                	<b>Dependentes</b>
      <table cellpadding="4" cellspacing="0" style="width:100%; border:0px; margin-top:3px;">
  		  <tr bgcolor="#DDDDDD">
            <td width="26%" height="22">Nome</td>
            <td width="17%">Data de Nascimento</td>
            <td width="18%">Idade</td>
            <td width="39%">Informa&ccedil;&atilde;o de DDIR</td>
  		  </tr>
          <tr <?=$style[0]?>>
            <td><?=$row_depe['nome1']?></td>
            <td><?=$row_depe['data1']?></td>
            <td><?=$idade[0].' anos'?></td>
            <td><?=$resposta[0]?></td>
          </tr>
          <tr <?=$style[1]?> style="background-color:#f0f0f0;">
            <td><?=$row_depe['nome2']?></td>
            <td><?=$row_depe['data2']?></td>
            <td><?=$idade[1].' anos'?></td>
            <td><?=$resposta[1]?></td>
          </tr>
          <tr <?=$style[2]?>>
            <td><?=$row_depe['nome3']?></td>
            <td><?=$row_depe['data3']?></td>
            <td><?=$idade[2].' anos'?></td>
            <td><?=$resposta[2]?></td>
          </tr>
          <tr <?=$style[3]?> style="background-color:#f0f0f0;">
            <td><?=$row_depe['nome4']?></td>
            <td><?=$row_depe['data4']?></td>
            <td><?=$idade[3].' anos'?></td>
            <td><?=$resposta[3]?></td>
          </tr>
          <tr <?=$style[4]?>>
            <td><?=$row_depe['nome5']?></td>
            <td><?=$row_depe['data5']?></td>
            <td><?=$idade[4].' anos'?></td>
            <td><?=$resposta[4]?></td>
          </tr>
	  </table>
              </div>
            </td>
          </tr>
 		</table>


    </td>
  </tr>
  <tr>
    <td align="center">
    
    
    <table cellpadding="0" cellspacing="0" width="95%" style="margin-top:50px;">
  	  <tr bgcolor="#cccccc">
    	<td height="30" colspan="7" align="center" bgcolor="#990000" id="falta">
        	<span class="style7">FALTAS</span>
        </td>
      </tr>
      <tr bgcolor="#cccccc">
      	<td align="center" bgcolor="#F1F1F1">
        <br>
        <form action="rh_movimentos.php" method="post" name="frmfaltas" onSubmit="return validaFALTA()">
        <table width="400" border="1" cellspacing="0" cellpadding="0"  >
          <tr>
      		<td class="escuro_claro" colspan="2">DATA DO MOVIMENTO</td>
          </tr>
          <tr>
            <td class="escuro"  colspan="2">
            <select id="mes_mov" name="mes_mov" class="campotexto">
			<?php
                        for($i=1; $i<=12; $i ++){                           
                            
                            $selected = ($i == $row_prox_folha['proxima_folha']) ?'selected="selected"':'';
                            echo '<option value="'.$i.'" '.$selected.'  >'.$meses[$i].'</option>';
                        }
                        
                        ?>
                            <option value="16">Rescisão</option>
                            <option value="17">Rescisão Complementar</option>
            		</select>
            		de 
            		<select id="ano_mov" name="ano_mov" class="campotexto">
						<?php
                        for($i=(date('Y')-3); $i<=(date('Y')+4); $i ++){
                            if($i == date('Y')){
                                echo '<option value="'.$i.'" selected>'.$i.'</option>';
                            }else{
                                echo '<option value="'.$i.'" >'.$i.'</option>';
                            }
                        }
                        ?>
            		</select>
                </td>
              </tr>
              <tr>
                <td class="escuro"  style="text-align:center;  " colspan="2">Quantidade de faltas:<input name="faltas" type="text" class="campotexto" id="faltas" size="3" maxlength="2" /></td>
                </td>
              </tr>          
              <!-- <tr>
                 <td class="escuro" style="text-align:center;" colspan="2"> Horas não trabalhadas:  <input type="text" name="qnt_horas_faltas"  id="qnt_horas_faltas"  size="2"/>  </td> 
                 </td>
              </tr>    -->      
              <tr>
                <td class="escuro" colspan="2" style="text-align:left;">
                    <input type="checkbox" name="falta_justificada" id="check_falta_justificada" value="1"/> Falta Justificada (Para Efeito de Histórico)<br>
                    <input type="checkbox" name="mes_anterior" id="check_mes_anterior" value="1"/><span id="label_mes_anterior">  Mês Anterior </span><br>             
                    <input type="checkbox" name="falta_digitada" id="check_falta_digitada" value="1"/><span id="label_falta_digitada"> Digitar o valor das faltas? </span>
                    <br><br><span style="margin: 5px; ">Motivos</span><br>
                    <textarea name="motivo" style="margin: 5px; height: 100px; width: 390px;"></textarea>
                </td> 
              </tr>
              
              
               <tr id="falta_digitada">
                   <td class="escuro"  style="text-align:left;" colspan="2" > Valor: 
                       <input type="text" name="valor_falta_digitada"  id="valor_falta_digitada" onkeydown="FormataValor(this,event,20,2)" size="10"/>  
                   </td> 
              </tr>
              <tr class="quantidade_horas_falta">
                   <td class="escuro" style="text-align:left;" colspan="2"> Horas não trabalhadas:  <input type="text" name="qnt_horas_faltas"  id="qnt_horas_faltas"  size="2"/>  </td> 
              </tr>
           
              
              <tr>
                <td class="claro" colspan="2">
                <input type="hidden" name="clt" value="<?=$clt?>" />
                <input type="hidden" name="regiao" value="<?=$regiao?>" />
                <input type="hidden" name="salario" value="<?=$row_curso['salario']?>" />
                <input type="hidden" name="projeto" value="<?=$row_clt['id_projeto']?>" />
                <?php if(isset($_GET['ferias'])) { ?>
                	<input name="ferias" type="hidden" value="true" />
                <?php } ?>
                <input type="hidden" name="tela" value="6" />
                <input type="submit" value="Lan&ccedil;ar Faltas">    
                </td>
              </tr>
            </table>
          </form>
           <br />
           <br /> 
      </td>
    </tr> 
    </table>
   
        
        <form action="rh_movimentos.php" method="post" name="frminss" >
          <table cellpadding="0" cellspacing="0" width="95%" style="margin-top:50px;">
  	  <tr bgcolor="#cccccc">
    	<td height="30" colspan="7" align="center" bgcolor="#990000" id="falta">
        	<span class="style7">DESCONTO DE INSS EM OUTRA EMPRESA </span>
        </td>
      </tr>  
      <tr bgcolor="#cccccc">
      	<td align="center" bgcolor="#F1F1F1">
        <br>
        
        <form action="rh_movimentos.php" method="post" name="frmfaltas">
        <table width="500" border="0" cellspacing="0" cellpadding="0"  > 
        
        <tr>
           <td class="secao" width="50%">Possui desconto de INSS em outra empresa? </td>
           <td>
               <input type="radio" name="desconto_inss" value="1"  <?php if($row_clt['desconto_inss'] == 1) echo 'checked="checked";'?>/>Sim
               <input type="radio" name="desconto_inss" value=""  <?php if($row_clt['desconto_inss'] == 0) echo 'checked="checked";'?>/>Não
           </td>
       </tr> 
        </table>
     
   <table width="500" border="0" cellspacing="0" cellpadding="0"  <?php if($row_clt['desconto_inss'] != 1) { echo 'style="display:none;"' ;}?> class="tabela_desconto">  
    <tr>
      <td class="secao">Tipo de Desconto:</td>
      <td>
          <select name="tipo_desconto_inss">
              <option value="">Selecione...</option>
              <option value="isento" <?php if($row_clt['tipo_desconto_inss'] == 'isento' ) { echo 'selected="selected"'; } ?>>Suspen&ccedil;&atilde;o de Recolhimento</option>
              <option value="parcial" <?php if($row_clt['tipo_desconto_inss'] == 'parcial' ) { echo 'selected="selected"'; } ?>> Parcial </option>
          </select>
      </td>
    </tr>   
    <tr>
        <td class="secao">Trabalha em outra empresa?</td>
        <td><input name="trabalha_outra_empresa" type="radio"  value="sim" <?php if($row_clt['trabalha_outra_empresa'] == 'sim') { echo 'checked'; } ?> >Sim
            <input name="trabalha_outra_empresa" type="radio" value="nao" <?php if($row_clt['trabalha_outra_empresa'] != 'sim') { echo 'checked'; } ?>>Não
        </td>
    </tr> 
   
  <tr class="outra_empresa"  <?php if($row_clt['trabalha_outra_empresa'] != 'sim') { echo 'style="display:none"'; } ?>>
   <td class="secao">Salário da outra empresa:</td>
   <td>
    <input name="salario_outra_empresa" type="text" size="12"  id="salario_outra_empresa"  value="<?=str_replace('.',',',$row_clt['salario_outra_empresa'])?>" OnKeyDown="FormataValor(this,event,20,2)">
    <input type="button" value="Calcular INSS" name="calcular_inss"/>
   </td>
  </tr>
  <tr class="outra_empresa"  <?php if($row_clt['trabalha_outra_empresa'] != 'sim') {  echo 'style="display:none"';  } ?> >
   <td class="secao">Desconto da outra empresa:</td>
   <td>
    <input name="desconto_outra_empresa" id ="desconto_outra_empresa" type="text" size="12"  value="<?=str_replace('.',',',$row_clt['desconto_outra_empresa'])?>" OnKeyDown="FormataValor(this,event,20,2)">
   </td>
  </tr>    
   </table>
            <table width="500" border="0" cellspacing="0" cellpadding="0" 
                        <tr>      
              <td colspan="2" align="center"> 
                  <input type="hidden"  name="id_regiao" value="<?php  echo $regiao;?>"/>
                  <input type="hidden"  name="id_clt" value="<?php  echo $clt;?>"/>
                  <input type="hidden"  name="tela" value="7"/>
                  <input type="submit" value="Concluir"  name="concluir"/> 
              </td>
          </tr>
          </table>        
            
          </form>
           <br />
           <br /> 
      </td>
    </tr> 
    </table>
        </form>
      
        
        
        
    </td>
  </tr>
  <tr>
    <td align="center">
    
    <?php $qr_faltas = mysql_query("SELECT * FROM rh_movimentos_clt WHERE tipo_movimento IN ('DEBITO','HISTORICO') AND id_clt = '$clt' AND (id_mov=62 OR id_mov=838) AND status = '1'");
		  $numero_faltas = mysql_num_rows($qr_faltas);
		  if(!empty($numero_faltas)) { ?>

     <table cellpadding="4" cellspacing="0" style="width:95%; border:0px; background-color:#F1F1F1; margin-bottom:30px; line-height:20px;">
        <tr>
          <td height="30" colspan="7" align="center" bgcolor="#990000" class="style7">
        	GERENCIAMENTO DE FALTAS
          </td>
        </tr>
        <tr style="text-align:center; background-color:#ddd">
          <td width="4%">COD</td>
          <td width="10%">VALOR</td>
          <td width="12%">LAN&Ccedil;AMENTO</td>
          <td width="10%">QUANTIDADE</td>
          <td width="10%">QNT. DE HORAS</td>
          <td width="8%">DELETAR</td>
        </tr>
        
    <?php
	while($faltas = mysql_fetch_array($qr_faltas)) {
	
            if($faltas['lancamento'] == '1') { 
                
                if($faltas['mes_anterior'] == 1){
                    
                    $lancamento = $meses[$faltas['mes_mov']]."/".$faltas['ano_mov'].' (Mês Anterior)';
                    
                } else {
                    
                    $lancamento = $meses[$faltas['mes_mov']]."/".$faltas['ano_mov'];
                    
                }
                
            } else {
                
                $lancamento = "Sempre"; 
                
            } 
            
        
    ?>
    
    <tr align="center" style="background-color:<?php echo ($alternateColor++%2!=0) ? "#ddd" : "#f0f0f0";  ?>" class="linha">
      <td><?=$faltas[0]?></td>
      <td><?php echo 'R$ '.number_format($faltas['valor_movimento'], '2', ',', '.'); ?></td>
      <td><?=$lancamento?></td>
	  <td><?=$faltas['qnt']?> <?=$faltas['tipo_movimento']=='HISTORICO' ? ' ('.$faltas['nome_movimento'].') ' : '' ?></td>
	  <td><?=$faltas['qnt_horas']?></td>
	  <?php if(isset($_GET['ferias'])) { ?>
	  		<td><a href="rh_movimentos.php?ferias=true&tela=5&clt=<?=$clt?>&regiao=<?=$regiao?>&movimento=<?=$faltas[0]?>"><img src="../imagens/deletar_usuario.gif" border="0"></a></td>
	  <?php } else { ?>
	  		<td><a href="rh_movimentos.php?tela=5&clt=<?=$clt?>&regiao=<?=$regiao?>&movimento=<?=$faltas[0]?>"><img src="../imagens/deletar_usuario.gif" border="0"></a></td>
	  <?php } ?>
    </tr>
  <?php } ?>
  
</table>
    
<?php } ?>
    
    </td>
  </tr>
  <tr>
    <td align="center">
    
    
<table cellpadding="0" cellspacing="0" style="width:95%; border:0px; margin-top:50px;">
  <tr>
    <td height="30" colspan="6" align="center" bgcolor="#003399" class="style7" id="credito">
    		MOVIMENTOS VARI&Aacute;VEIS PARA CR&Eacute;DITO
    </td>
  </tr>
    <tr bgcolor="#cccccc">
      <td colspan="6" align="center" valign="center" bgcolor="#F1F1F1">
      <form action="rh_movimentos.php" method="post" name="form1" onSubmit="return valida1()" id="credito">
      	<br>
        <table width="75%" border="0" cellspacing="0" cellpadding="0" class="bordaescura1px">
          <tr>
            <td class="escuro_claro">DATA DO MOVIMENTO</td>
          </tr>
          <tr>
            <td class="escuro">
              <select id="mes_mov" name="mes_mov" class="campotexto mesMovCredito">
                <?php
             for($i=1; $i<=12; $i ++){                           
                            
                            $selected = ($i == $row_prox_folha['proxima_folha']) ?'selected="selected"':'';
                            echo '<option value="'.$i.'" '.$selected.'  >'.$meses[$i].'</option>';
                        }
                        
			echo '<option value="13">13º Primeira parcela</option>';
			echo '<option value="14">13º Segunda parcela</option>';
			echo '<option value="15">13º Integral</option>';
			echo '<option value="16">Rescisão</option>';
			echo '<option value="17">Rescisão Complementar</option>';
            ?>
              </select> de <select id="ano_mov" name="ano_mov" class="campotexto anoMovCredito">
  	    <?php for($i=(date('Y')-3); $i<=(date('Y')+4); $i ++){
				if($i == date('Y')){
					echo '<option value="'.$i.'" selected>'.$i.'</option>';
				}else{
					echo '<option value="'.$i.'" >'.$i.'</option>';
				}
			}
            ?>
</select></td>
          </tr>
          <tr>
            <td class="escuro_claro">TIPO DO MOVIMENTO</td>
          </tr>
          <tr>
            <td class="escuro">
                <table width="97%" border="0" cellspacing="0" cellpadding="0" class="style7 linhastabela1" id="tabelacredito" align="center">
                    <?php
                    $linha = 0;
                    $cont_total = 0;

                    $qr_mov = mysql_query("SELECT * FROM rh_movimentos WHERE id_mov IN(66, 13,57,56,14,94,150,151,149,152,172,192,196,197,198,199,201,202,203,204,205,206,207,228,229,230,232,209,247,248,252, 254,15,55,831,835,832) ORDER BY descicao");
                    while($row_mov = mysql_fetch_assoc($qr_mov)):
                        $linha++;
                        $cont_total++;

                        if($linha == 1  ) {echo '<tr>'; } 
                    ?>
                        <td height="35">
                            <label>
                                <?php $modalReembolsoFaltas = 0; if($row_mov['id_mov'] == 229){$modalReembolsoFaltas = 1;} ?>
                                <input type="radio" name="mov1" id="1" align="absmiddle" value="<?php echo $row_mov['id_mov']?>" data-modal ='<?php echo $modalReembolsoFaltas; ?>' >
                                <?php echo $row_mov['descicao']; ?> 
                            </label>
                        </td>
                        <td>
                            <label>
                                <?php
                                     if( $linha > 2 or ( $cont_total == mysql_num_rows($qr_mov) )) {
                                         echo '</tr>';
                                         $linha = 0; 
                                    } 
                                ?>    
                    <?php  endwhile; ?>
                </table>
           </td>
          </tr>
          <tr>
            <td class="escuro_claro">VALOR E LAN&Ccedil;AMENTO DO MOVIMENTO</td>
          </tr>
          <tr>
            <td class="escuro">
            <input name="valor" type="text" id="valor" size="20" OnKeyDown="FormataValor(this,event,20,2)"/>
            <span id="mostrartexto3" class="style7"></span>&nbsp;&nbsp;
            <select name="lancamento1" id="lancamento1" onChange="verifica(1,3)">
              <option value="1">Pr&oacute;xima Folha</option>
              <option value="2">Sempre</option>
            </select>
           </td>
          </tr>
          <tr class="quantidade">
            <td class="escuro_claro">QUANTIDADE</td>
          </tr>
          <tr class="quantidade">
            <td class="escuro" >
           QUANTIDADE: <input name="quantidade" type="text" id="quantidade" size="10" />
           </td>
          </tr>
          
          
          <tr>
            <td class="escuro_claro">INCID&Ecirc;NCIA</td>
          </tr>
          <tr>
            <td class="escuro">
            <table width="300" border="0" cellspacing="0" cellpadding="0" class="style7" style="border:solid 1px #FFF;" align="center">
              <tr>
                <td height="35" align="center">
                  <input type="checkbox" name="inc1" id="inc1" align="absmiddle" value="5020" style="display:none">
                  <input type="checkbox" name="inc2" id="inc2" align="absmiddle" value="5021" style="display:none">
                  <input type="checkbox" name="inc3" id="inc3" align="absmiddle" value="5023" style="display:none">
                  <span id="mostrartexto1" class="style7"></span>
                </td>
              </tr>
          </table>
             
              </td>
          </tr>
          <tr>
            <td class="claro">
              <input type="hidden" name="clt" value="<?=$clt?>" />
              <input type="hidden" name="regiao" value="<?=$regiao?>" />
              <input type="hidden" name="projeto" value="<?=$row_clt['id_projeto']?>" />
              <?php if(isset($_GET['ferias'])) { ?>
              <input name="ferias" type="hidden" value="true" />
              <?php } ?>
              <input type="hidden" name="tela" value="3" />
              <input type="submit" value="Lan&ccedil;ar Movimento"/></td>
          </tr>
        </table>
        <br>
        <span id='testescript' class="style7"></span>
		<script language="javascript">
        /*
        function verifica(a,b){
						
			if(a == 1){			
			
			
			}else{
				
				
			var lancamento2 = document.getElementById("lancamento2").value;
			var inc2 = b;

			if(document.all.mov2[0].checked){
				var mov2 = "1";
			}else if(document.all.mov2[1].checked){
				var mov2 = "2";
			}else if(document.all.mov2[2].checked){
				var mov2 = "3";
			}else if(document.all.mov2[3].checked){
				var mov2 = "4";
			}else if(document.all.mov2[4].checked){
				var mov2 = "5";
			}else {
				var mov2 = "";
			}
			
			if(mov2 == 3 || mov2 == 4){
				document.getElementById("inc4").checked = true;
				document.getElementById("inc5").checked = true;
				document.getElementById("inc6").checked = true;
				document.getElementById("mostrartexto2").innerText = "INSS - IRRF - FGTS";
				document.all.valor2.style.display = '';
			
				
			}else if(mov2 == 1 || mov2 == 2){
				document.getElementById("inc4").checked = false;
				document.getElementById("inc5").checked = false;
				document.getElementById("inc6").checked = false;
				document.getElementById("mostrartexto2").innerText = "NENHUMA INCIDENCIA";
				document.all.valor2.style.display = '';
			}else if(mov2 == 5)  {
			
                        document.getElementById("inc4").checked = false;
                        document.getElementById("inc5").checked = false;
                        document.getElementById("inc6").checked = false;
                        document.getElementById("mostrartexto2").innerText = "NENHUMA INCIDENCIA";
			document.all.valor2.style.display = '';
                        document.getElementById("mostrartexto2").innerText = "NENHUMA INCIDENCIA";
				
			}
				

			}
			
		}
        */
        </script>
        <br />
        </form>
      </td>
    </tr>
</table>
    
    
    </td>
  </tr>
  <tr>
    <td align="center">
    
    <?php $qr_creditos = mysql_query("SELECT * FROM rh_movimentos_clt WHERE tipo_movimento = 'CREDITO' AND id_clt = '$clt' AND status = '1'");
	      $numero_creditos = mysql_num_rows($qr_creditos);
		  if(!empty($numero_creditos)) { ?>
          
 		<table cellpadding="4" cellspacing="0" style="width:95%; border:0px; background-color:#F1F1F1; line-height:20px;">
  		  <tr>
            <td height="30" colspan="6" align="center" bgcolor="#003399" class="style7">
    			GERENCIAMENTO DE MOVIMENTOS VARIÁVEIS PARA CR&Eacute;DITO
            </td>
          </tr>
  		  <tr style="text-align:center; background-color:#ddd;">
              <td width="4%">COD</td>
              <td width="26%">MOVIMENTO</td>
              <td width="10%">VALOR</td>
              <td width="12%">LAN&Ccedil;AMENTO</td>
              <td width="30%">INCID&Ecirc;NCIA</td>
              <td width="8%">DELETAR</td>
          </tr>
    
	<?php
	while($creditos = mysql_fetch_array($qr_creditos)) {
	
	if($creditos['lancamento'] == '1') {
		$lancamento = $meses[$creditos['mes_mov']]."/".$creditos['ano_mov'];
	} else { 
		$lancamento = 'Sempre';
	} ?>
    
    <tr align="center" style="background-color:<?php if($alternateColor2++%2!=0) { echo "#ddd"; } else { echo "#f0f0f0"; } ?>" class="linha">
      <td><?=$creditos[0]?></td>
      <td><?=$creditos['nome_movimento']?></td>
      <td><?php echo 'R$ '.number_format($creditos['valor_movimento'], '2', ',', '.'); ?></td>
      <td><?=$lancamento?></td>
      <td> 
	 <?php for($i=0; $i<=2; $i++) {
		  
			  $numero_in = $creditos['incidencia'];
			  $numero_in = explode(",",$numero_in);
			  
			  echo $ar_incidencia[$numero_in[$i]];
			  
			  if(!empty($numero_in[$i]) and $i != 2) {
					echo " - ";
			  }
			  
	  	   } ?>
           </td>
		   
	  <?php if(isset($_GET['ferias'])) { ?>
		   <td align="center"><a href="rh_movimentos.php?ferias=true&tela=5&clt=<?=$clt?>&regiao=<?=$regiao?>&movimento=<?=$creditos[0]?>"><img src="../imagens/deletar_usuario.gif" border="0"></a></td>
	  <?php } else { ?>
		   <td align="center"><a href="rh_movimentos.php?tela=5&clt=<?=$clt?>&regiao=<?=$regiao?>&movimento=<?=$creditos[0]?>"><img src="../imagens/deletar_usuario.gif" border="0"></a></td>
	  <?php } ?>
      
     </tr>
   <?php } ?>
</table>
<?php } ?> 
    
    </td>
  </tr>
  <tr>
    <td align="center">
    
    
    <table cellpadding="0" cellspacing="0" width="95%" style="margin-top:80px;">
  	  <tr bgcolor="#cccccc">
    	<td height="30" colspan="7" align="center" bgcolor="#990000" id="debito">
        	<span class="style7">MOVIMENTOS VARI&Aacute;VEIS PARA DESCONTO</span>
        </td>
      </tr>
    <tr bgcolor="#cccccc">
      <td colspan="7" align="center" valign="middle" bgcolor="#F1F1F1">
      <form action="rh_movimentos.php" method="post" name="form2" onSubmit="return valida2()">
      <div align="center">
      <br>
      <table width="75%" border="0" cellspacing="0" cellpadding="0" class="bordaescura1px">
      	  <tr>
            <td class="escuro_claro">DATA DO MOVIMENTO</td>
          </tr>
          <tr>
          	<td class="escuro">
          <select id="mes_mov" name="mes_mov" class="campotexto">
              <?php
             for($i=1; $i<=12; $i ++){                           
                            
                            $selected = ($i == $row_prox_folha['proxima_folha']) ?'selected="selected"':'';
                            echo '<option value="'.$i.'" '.$selected.'  >'.$meses[$i].'</option>';
                        }
                        
			echo '<option value="13">13º Primeira parcela</option>';
			echo '<option value="14">13º Segunda parcela</option>';
			echo '<option value="15">13º Integral</option>';
			echo '<option value="16">Rescisão</option>';
			echo '<option value="17">Rescisão Complementar</option>';
            ?>
            </select> 
            de
			<select id="ano_mov" name="ano_mov" class="campotexto">
  			<?php
            for($i=(date('Y')-3); $i<=(date('Y')+4); $i ++){
				if($i == date('Y')){
					echo '<option value="'.$i.'" selected>'.$i.'</option>';
				}else{
					echo '<option value="'.$i.'" >'.$i.'</option>';
				}
			}
            ?>
			</select>
         </td>
        </tr>
          <tr>
            <td class="escuro_claro">TIPO DO MOVIMENTO</td>
          </tr>
        <tr>
          <td class="escuro">
          <table width="97%" border="0" cellspacing="0" cellpadding="0" class="style7 linhastabela1" id="tabeladebito" align="center">
            <tr height="35"> 
              <td width="25%" height="35" align="left"><label>
                <input type="radio" name="mov2" id="5" align="absmiddle" value="60">
                ADIANTAMENTO </label></td>
              <td width="17%" height="35" align="left"><label>
                <input type="radio" name="mov2" id="6" align="absmiddle" value="76" >
                DESCONTO</label></td>
              <td width="31%" height="35" align="left"><label>
                <input type="radio" name="mov2" id="7" align="absmiddle" value="54" >
                PENS&Atilde;O ALIMENTICIA 15%</label></td>
              <td width="27%" align="left"><label>
                <input type="radio" name="mov2" id="" align="absmiddle" value="250" >
                PENS&Atilde;O ALIMENTICIA 25%</label></td>
            </tr>
            <tr height="35">
                
                  <td width="27%" align="left"><label>
                <input type="radio" name="mov2" id="8" align="absmiddle" value="63" >
                PENS&Atilde;O ALIMENTICIA 30%</label></td>
           
            	  <td width="27%" align="left">
                  <label>
                <input type="radio" name="mov2" id="8" align="absmiddle" value="195">
               DESCONTO VALE TRANSPORTE </label>
                     
                </td>
                   <td width="17%" height="35" align="left"><label>
                <input type="radio" name="mov2" id="6" align="absmiddle" value="208">
                PAGAMENTO INDEVIDO</label></td>
                   <td width="17%" height="35" align="left"><label>
                <input type="radio" name="mov2" id="6" align="absmiddle" value="224" >
                ATRASO / SAÍDA ANTECIPADA</label></td>
                
              
            </tr>
            <tr height="35">
                   <td width="27%" align="left">
                  <label>
                <input type="radio" name="mov2" id="8" align="absmiddle" value="231">
               REEBOLSO VALE TRANSPORTE </label>                     
                </td>
                   <td width="27%" align="left">
                  <label>
                <input type="radio" name="mov2" id="9" align="absmiddle" value="244">
              ADIANTAMENTO DE FÉRIAS </label>                     
                </td>
                   <td width="27%" align="left">
                  <label>
                    <input type="radio" name="mov2" id="10" align="absmiddle" value="21">
                    CONTRIBUIÇÂO SINDICAL </label>                     
                </td>      
                 <td width="27%" align="left"><label>
                    <input type="radio" name="mov2" id="11" align="absmiddle" value="253">
                   SUSPENÇÃO </label>                     
                </td>
            </tr >
           
            <tr height="35">
               <td width="27%" align="left"><label>
                <input type="radio" name="mov2" id="8" align="absmiddle" value="255" >
                PENS&Atilde;O ALIMENTICIA 20%</label></td>
                
                 <td width="27%" align="left"><label>
                <input type="radio" name="mov2" id="8" align="absmiddle" value="256" >
                PENS&Atilde;O ALIMENTICIA 5%</label></td>
                
                 <td width="27%" align="left"><label>
                <input type="radio" name="mov2" id="8" align="absmiddle" value="834" >
                PENS&Atilde;O ALIMENTICIA 39%</label></td>
                 
                <td width="27%" align="left"><label>
                <!--input type="radio" name="mov2" id="8" align="absmiddle" value="50260" -->
                <input type="radio" name="mov2" id="8" align="absmiddle" value="824" >
                INSS SOBRE 13 SALARIO</label></td>
                
            </tr >
           
            <tr height="35">
                <td width="27%" align="left"><label>
                <!--input type="radio" name="mov2" id="8" align="absmiddle" value="50261" -->
                <input type="radio" name="mov2" id="8" align="absmiddle" value="825" >
                IR SOBRE 13 SALARIO</label></td>

                <td width="27%" align="left"><label>
                <!--input type="radio" name="mov2" id="8" align="absmiddle" value="50261" -->
                <input type="radio" name="mov2" id="8" align="absmiddle" value="840" >
                PENSÃO ALIMENTÍCIA NAS FÉRIAS</label></td>
            </tr>
            
          </table>
                
              
          </td>
          </tr>
        <tr>
          <td class="escuro_claro">VALOR E LAN&Ccedil;AMENTO DO MOVIMENTO</td>
        </tr>
        <tr>
          <td class="escuro">
           <span class="valor_digitado">
              <span class="valor_desconto_digitado"> <input type="checkbox" name="digitar_valor_desconto" value='1'/> Digitar o valor do desconto? </span>   
                <input name="valor2" type="text" id="valor2" size="20" OnKeyDown="FormataValor(this,event,20,2)" />
           </span>
           
            <span>
                <select name="lancamento2" id="lancamento2" onChange="verifica(2,5)">
                  <option value="1">Pr&oacute;xima Folha</option>
                  <option value="2">Sempre</option>
                </select>
            </span>
        
            </td>
          </tr>
          <tr class="dias_debito">
              <td class="escuro">
                  Digite a quantidade de dias:
                  <input type="text" name="dias_debito" size="2"/>
              </td>
          </tr>
        <tr>
          <td class="escuro_claro">INCID&Ecirc;NCIA</td>
        </tr>
        <tr>
          <td class="escuro">
          <table width="300" border="0" cellspacing="0" cellpadding="0" class="style7" style="border:solid 1px #FFF;" align="center">
            <tr>
              <td height="35" align="center">
                <input type="checkbox" name="inc4" id="inc4" align="absmiddle" value="5020" style="display:none">
                <input type="checkbox" name="inc5" id="inc5" align="absmiddle" value="5021" style="display:none">
                <input type="checkbox" name="inc6" id="inc6" align="absmiddle" value="5023" style="display:none">
                <span id="mostrartexto2" class="style7"></span>
                </td>
              </tr>
            </table>
            </td>
          </tr>
          <tr>
            <td class="claro">
            <input name="tela" type="hidden" id="tela" value="4" />
            <input name="clt" type="hidden" id="clt" value="<?=$clt?>" />
            <input name="regiao" type="hidden" id="regiao" value="<?=$regiao?>" />
            <input name="projeto" type="hidden" value="<?=$row_clt['id_projeto']?>" />
            <?php if(isset($_GET['ferias'])) { ?>
            <input name="ferias" type="hidden" value="true" />
            <?php } ?>
            <input type="submit" value="Lan&ccedil;ar Movimento" /></td>
          </tr>
      </table>
      <br>
      </div></form>
	  </td>
    </tr>
  </table>
    
    
    </td>
  </tr>
  <tr>
    <td align="center">
    
    <?php 
    //echo "SELECT * FROM rh_movimentos_clt WHERE  (tipo_movimento = 'DEBITO' AND id_clt = '$clt' AND id_mov != '62' AND status = '1') OR (id_mov = '195' AND status = '1' AND id_clt = '$clt' ) ";exit;
    $qr_debitos = mysql_query("SELECT * FROM rh_movimentos_clt WHERE  (tipo_movimento = 'DEBITO' AND id_clt = '$clt' AND id_mov != '62' AND status = '1') OR (id_mov = '195' AND status = '1' AND id_clt = '$clt' ) ");
	      $numero_debitos = mysql_num_rows($qr_debitos);
		  if(!empty($numero_debitos)) { ?>
    
     <table cellpadding="4" cellspacing="0" style="width:95%; border:0px; background-color:#F1F1F1; margin-bottom:30px; line-height:20px;">
        <tr>
          <td height="30" colspan="7" align="center" bgcolor="#990000" class="style7">
        		GERENCIAMENTO DE MOVIMENTOS VARI&Aacute;VEIS PARA DESCONTO
          </td>
        </tr>
        <tr style="text-align:center; background-color:#ddd;">
          <td width="4%">COD</td>
          <td width="26%">MOVIMENTO</td>
          <td width="10%">VALOR</td>
          <td width="10%">DIAS</td>
          <td width="12%">LAN&Ccedil;AMENTO</td>
          <td width="30%">INCID&Ecirc;NCIA</td>
          <td width="8%">DELETAR</td>
        </tr>
    <?php
	while($debitos = mysql_fetch_array($qr_debitos)) {
		
	if($debitos['lancamento'] == '1') {
		$lancamento = $meses[$debitos['mes_mov']]."/".$debitos['ano_mov']; 
	} else { 
		$lancamento = 'Sempre'; 
	} ?>
    <tr style="background-color:<?php if($alternateColor3++%2!=0) { echo "#ddd"; } else { echo "#f0f0f0"; } ?>" class='linha' align='center'>
      <td><?=$debitos[0]?></td>
      <td><?=$debitos['nome_movimento']?></td>
      <td><?php echo 'R$ '.number_format($debitos['valor_movimento'], '2', ',', '.'); ?></td>
      <td><?=$debitos['qnt']?></td>
      <td><?=$lancamento?></td>
      <td>
	<?php for($i=0; $i<=2; $i++) {
		  
			  $numero_in = $debitos['incidencia'];
			  $numero_in = explode(",",$numero_in);
			  
			  echo $ar_incidencia[$numero_in[$i]];
			  
			  if(!empty($numero_in[$i]) and $i != 2) {
					echo " - ";
			  }
		  
	  	  } ?>
	  </td>
	  <?php if(isset($_GET['ferias'])) { ?>
	  		<td align='center'><a href='rh_movimentos.php?ferias=true&tela=5&clt=<?=$clt?>&regiao=<?=$regiao?>&movimento=<?=$debitos[0]?>'><img src='../imagens/deletar_usuario.gif' border='0'></a></td>
	  <?php } else { ?>
	  		<td align='center'><a href='rh_movimentos.php?tela=5&clt=<?=$clt?>&regiao=<?=$regiao?>&movimento=<?=$debitos[0]?>'><img src='../imagens/deletar_usuario.gif' border='0'></a></td>
	  <?php } ?>
    </tr>
   <?php } ?>
</table>
<?php } ?>
    
    
    </td>
  </tr>
</table>


<?php
break;
case 3:  //GRAVANDO RENDIMENTOS


    
$regiao = $_REQUEST['regiao'];
$projeto = $_REQUEST['projeto'];
$mes_mov = $_REQUEST['mes_mov'];
$ano_mov = $_REQUEST['ano_mov'];
$clt = $_REQUEST['clt'];
$data = date('Y-m-d');
$user = $_COOKIE['logado'];

$cod_movimento = $_REQUEST['mov1'];
$valor = $_REQUEST['valor'];
$valor = str_replace(".","",$valor);
$valor = str_replace(",",".",$valor);
$lancamento = $_REQUEST['lancamento1'];
$inc1 = $_REQUEST['inc1'];
$inc2 = $_REQUEST['inc2'];
$inc3 = $_REQUEST['inc3'];
$incidencia = "$inc1,$inc2,$inc3";
$quantidade = $_REQUEST['quantidade'];


    


$RSClt = mysql_query("SELECT id_clt,id_curso FROM rh_clt WHERE id_clt = '$clt'");
$RowCLT = mysql_fetch_array($RSClt);

$RSCurso = mysql_query("SELECT salario FROM curso WHERE id_curso = '$RowCLT[id_curso]'");
$RowCurso = mysql_fetch_array($RSCurso);

$result_movimento = mysql_query("SELECT * FROM rh_movimentos WHERE id_mov = '$cod_movimento'");
$row_movimento = mysql_fetch_array($result_movimento);


switch($cod_movimento){
    
    case 149: $valor = $RowCurso['0'] * $row_movimento['percentual'];
        break;
    case 13: $metade = $RowCurso['salario'] / 2;
                if($valor > $metade){
                        $incidencia = "5020,5021,5023";
                }
   break;
   
   case 209:
       break;
                
}



$sql_2 = "INSERT INTO rh_movimentos_clt(id_clt,id_regiao,id_projeto,mes_mov,ano_mov,id_mov,cod_movimento,tipo_movimento,nome_movimento,
data_movimento,user_cad,valor_movimento,percent_movimento,lancamento,incidencia, qnt) VALUES 
('$clt','$regiao','$projeto','$mes_mov','$ano_mov','$cod_movimento','$row_movimento[cod]','$row_movimento[categoria]','$row_movimento[descicao]','$data','$user','$valor','$percentual','$lancamento','$incidencia', '$quantidade')";

echo $sql_2.'<br>';

mysql_query($sql_2);


//-- ENCRIPTOGRAFANDO A VARIAVEL
$link = encrypt("$regiao&$clt"); 
$link = str_replace("+","--",$link);
// -----------------------------

if(empty($_POST['ferias']) and empty($_GET['ferias'])) {
	print "<script>location.href=\"rh_movimentos.php?tela=2&enc=$link#credito\"</script>";
} else {
	print "<script>location.href=\"rh_movimentos.php?ferias=true&tela=2&enc=$link#credito\"</script>";
}


break;
case 4:

    
    
    
    
$regiao = $_REQUEST['regiao'];
$projeto = $_REQUEST['projeto'];
$mes_mov = $_REQUEST['mes_mov'];
$ano_mov = $_REQUEST['ano_mov'];


$clt            = $_REQUEST['clt'];
$cod_movimento  = $_REQUEST['mov2'];
$valor          = $_REQUEST['valor2'];
$lancamento     = $_REQUEST['lancamento2'];
$dias_debito    = $_REQUEST['dias_debito'];

$inc1 = $_REQUEST['inc4'];
$inc2 = $_REQUEST['inc5'];
$inc3 = $_REQUEST['inc6']; 


//  Salário Limpo
$RSClt = mysql_query("SELECT id_clt,id_curso,id_regiao FROM rh_clt WHERE id_clt = '$clt'");
$RowCLT = mysql_fetch_array($RSClt);

$qr_curso       = mysql_query("SELECT salario FROM curso WHERE id_curso = '$RowCLT[id_curso]'") or die(mysql_error());
@$salario_limpo = mysql_result($qr_curso, 0, 0);
$valor_dia = $salario_limpo/30;

$incidencia = "$inc1,$inc2,$inc3";

$data = date('Y-m-d');
$user = $_COOKIE['logado'];

if($cod_movimento == 253){
     $valor = $valor_dia * $dias_debito;
    $qnt = $dias_debito;
}  else {
    $valor = str_replace(".","",$valor);
    $valor = str_replace(",",".",$valor);
}


 /* 
if($cod_movimento == 195){ /////DESCONTO VALE TRANSPORTE

      if($RowCLT['id_regiao'] == 1){

             $qnt_dias = cal_days_in_month(CAL_GREGORIAN, $mes_mov, $ano_mov);
             for($i=0; $i<$qnt_dias;$i++){

                 $date = mktime(0, 0, 0, $mes_mov, $ano_mov, $i);  
                 if(date('w', $date) != 0 and date('w', $date) != 6){
                     $cont_dias++;            
                 }
             }      

             $valor = (($salario_limpo/30) * $cont_dias) * 0.06;     


        } else {
            $valor = $salario_limpo * 0.06;
        }	
}
  */



$result_movimento = mysql_query("SELECT * FROM rh_movimentos WHERE id_mov = '$cod_movimento'");
$row_movimento = mysql_fetch_array($result_movimento);

$sql_3 = "INSERT INTO rh_movimentos_clt(id_clt,id_regiao,id_projeto,mes_mov,ano_mov,id_mov,cod_movimento,tipo_movimento,nome_movimento,
data_movimento,user_cad,valor_movimento,qnt,percent_movimento,lancamento,incidencia) VALUES 
('$clt','$regiao','$projeto','$mes_mov','$ano_mov','$cod_movimento','$row_movimento[cod]','$row_movimento[categoria]','$row_movimento[descicao]','$data','$user','$valor','$qnt','$percentual',
'$lancamento','$incidencia')";

mysql_query($sql_3);


//-- ENCRIPTOGRAFANDO A VARIAVEL
$link = encrypt("$regiao&$clt"); 
$link = str_replace("+","--",$link);
// -----------------------------

if(empty($_POST['ferias']) and empty($_GET['ferias'])) {
	print "<script>location.href=\"rh_movimentos.php?tela=2&enc=$link#debito\"</script>";
} else {
	print "<script>location.href=\"rh_movimentos.php?ferias=true&tela=2&enc=$link#debito\"</script>";
}

break;
case 5:

    
$regiao = $_REQUEST['regiao'];
$clt = $_REQUEST['clt'];
$movimento = $_REQUEST['movimento'];

$qr_tipo_movimento = mysql_query("SELECT tipo_movimento, id_mov FROM rh_movimentos_clt WHERE id_movimento = '$movimento'");
$row_tipo_movimento = mysql_fetch_assoc($qr_tipo_movimento);
$tipo_movimento = $row_tipo_movimento['tipo_movimento'];
$id_mov = $row_tipo_movimento['id_mov']; 

if($tipo_movimento == 'CREDITO') {
	$ancora = '#credito';
} elseif($tipo_movimento == 'DEBITO') {
	if($id_mov == '62') {
		$ancora = '#falta';
	} else {
		$ancora = '#debito';
	}
}

mysql_query("UPDATE rh_movimentos_clt SET status = '0' WHERE id_movimento = '$movimento'");

//-- ENCRIPTOGRAFANDO A VARIAVEL
$link = encrypt("$regiao&$clt"); 
$link = str_replace("+","--",$link);
// -----------------------------

if(empty($_POST['ferias']) and empty($_GET['ferias'])) {
	print "<script>location.href=\"rh_movimentos.php?tela=2&enc=$link$ancora\"</script>";
} else {
	print "<script>location.href=\"rh_movimentos.php?ferias=true&tela=2&enc=$link$ancora\"</script>";
}

break;
case 6:


$clt            = $_REQUEST['clt'];
$regiao         = $_REQUEST['regiao'];
$projeto        = $_REQUEST['projeto'];
$mes_mov        = $_REQUEST['mes_mov'];
$ano_mov        = $_REQUEST['ano_mov'];
$mes_anterior   = $_REQUEST['mes_anterior'];
$motivo         = $_REQUEST['motivo'];


$faltas = $_REQUEST['faltas'];
$salario = $_REQUEST['salario'];

$verifica_falta_digitada = $_REQUEST['falta_digitada'];
$valor_falta_digitada    = $_REQUEST['valor_falta_digitada'];
$falta_justificada       = $_REQUEST['falta_justificada'];


$horas_faltas = $_REQUEST['qnt_horas_faltas'];




if(empty($verifica_falta_digitada)){
    $valorDia    = $salario / 30;
    $valorFaltas = $faltas * $valorDia;
} else {
    $valorFaltas = str_replace(',','.',str_replace('.','', $valor_falta_digitada));
}


$valorFaltasF = number_format($valorFaltas,2,",",".");
$valorFaltasF2 = number_format($valorFaltas,2,".","");

if(empty($falta_justificada)){
    
    $cod = '8000';
    $incidencia = '5020,5021,5023';
            
}
else {
    
    $cod = '50300';
    $incidencia = '5020,5021,5023';
    
}

$result_movimento = mysql_query("SELECT * FROM rh_movimentos WHERE cod = '{$cod}'");

$row_movimento = mysql_fetch_array($result_movimento);

$data = date('Y-m-d');
$user = $_COOKIE['logado'];



if($mes_anterior == 1){    
    $nome_mov = $row_movimento[descicao].' (Mês anterior)';
}else {
    $nome_mov = $row_movimento[descicao];
}




$sql_1 = "INSERT INTO rh_movimentos_clt(id_clt,id_regiao,id_projeto,mes_mov,ano_mov,id_mov,cod_movimento,tipo_movimento,nome_movimento,data_movimento,user_cad,valor_movimento,lancamento,qnt, mes_anterior,qnt_horas, incidencia,motivo) VALUES 
('$clt','$regiao','$projeto','$mes_mov','$ano_mov','$row_movimento[id_mov]','$row_movimento[cod]','$row_movimento[categoria]','$nome_mov',
'$data','$user','$valorFaltas','1','$faltas', '$mes_anterior', '$horas_faltas', '$incidencia','$motivo')";

mysql_query($sql_1);


//-- ENCRIPTOGRAFANDO A VARIAVEL
$link = encrypt("$regiao&$clt"); 
$link = str_replace("+","--",$link);
// -----------------------------

if(empty($_POST['ferias']) and empty($_GET['ferias'])) {
	print "<script>location.href=\"rh_movimentos.php?tela=2&enc=$link#falta\"</script>";
} else {
	print "<script>location.href=\"rh_movimentos.php?ferias=true&tela=2&enc=$link#falta\"</script>";	
}

break;



case 7:
    $id_clt                 = $_POST['id_clt'];
    $regiao                 = $_POST['id_regiao'];
    $desconto_inss          = $_POST['desconto_inss'];
    $tipo_desconto_inss     = $_POST['tipo_desconto_inss'];
    $trabalha_outra_empresa = $_POST['trabalha_outra_empresa'];
    $salario_outra_empresa  = str_replace(',','.', str_replace('.','',$_POST['salario_outra_empresa']));
    $desconto_outra_empresa = str_replace(',','.', str_replace('.','',$_POST['desconto_outra_empresa']));
    $mes = date('m');
    $ano = date('Y');
    if($tipo_desconto_inss == 'parcial' ){
        
    }
    
   if($_COOKIE['logado'] == 1){
       
       mysql_query("INSERT INTO desconto_inss_outra_empresa (id_clt, mes, ano, salario_outra_empresa, desconto_outra_empresa, status )
                                                            VALUES
                                                            ('$id_clt','$mes', '$ano', '$salario_outra_empresa', '$desconto_outra_empresa', 1 )");
   } else {
       
       
   
    
   mysql_query("UPDATE rh_clt SET desconto_inss = '$desconto_inss', tipo_desconto_inss = '$tipo_desconto_inss', trabalha_outra_empresa = '$trabalha_outra_empresa',
                salario_outra_empresa = '$salario_outra_empresa', desconto_outra_empresa = '$desconto_outra_empresa' WHERE id_clt = $id_clt LIMIT 1 ") or die(mysql_error());
   }
            //-- ENCRIPTOGRAFANDO A VARIAVEL
         $link = encrypt("$regiao&$id_clt"); 
         $link = str_replace("+","--",$link);
         // -----------------------------

         if(empty($_POST['ferias']) and empty($_GET['ferias'])) {
                 print "<script>location.href=\"rh_movimentos.php?tela=2&enc=$link\"</script>";
         } else {
                 print "<script>location.href=\"rh_movimentos.php?ferias=true&tela=2&enc=$link\"</script>";	
         }

    break;


}
?>
</div>
<!-- ******************** 17-06-2015 - MODAL DE REEMBOLSO********************** -->	
<div id="modal_reembolso_faltas">
    <form action="" name="formCadMov" id="formCadMov" method="post"> 
        <fieldset style="border: 1px solid #ccc; padding: 20px;">
            <legend style="font-size: 14px;">MOVIMENTO DE REEMBOLSO DE FALTAS</legend>
            <p style="margin-bottom: 5px; font-size: 12px;">
                <label style="display: block; text-transform: uppercase;">Quantidade de dias: </label>
                <input class="validate[required,custom[number]]" type="text" name="quant_dias_reembolso" value="" style="padding: 5px;"  />
            </p>
            <p style="margin-bottom: 5px; font-size: 12px;">
                <label  style="display: block; text-transform: uppercase;">Valor: </label>
                <input class="validate[required] maskMoney" type="text" name="valor_dias_reembolso" value="" style="padding: 5px;" />
            </p>
            <p  style="margin: 10px 0px; font-size: 12px;">
                <input type="button" name="cadastrar" value="Cadastrar" class='cadValorDiasReembolso' style="padding: 8px 15px; text-transform: uppercase;"/>
            </p>
            <input type="hidden" name="regiaoMov" value="<?php echo $regiao; ?>" /> 
            <input type="hidden" name="projetoMov" value="<?php echo $row_clt[id_projeto]; ?>" /> 
            <input type="hidden" name="mesMov" value="" /> 
            <input type="hidden" name="anoMov" value="" /> 
            <input type="hidden" name="cltSelected" value="<?php echo $clt; ?>" /> 
        </fieldset>
    </form>
</div>
    
</body>
</html>