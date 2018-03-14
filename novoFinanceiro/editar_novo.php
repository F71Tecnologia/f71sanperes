<?php 

include ("include/restricoes.php");
include "../conn.php";
include "../funcoes.php";



$regiao  = $_REQUEST['regiao'];
$id_user  = $_COOKIE['logado'];
$id_saida = $_REQUEST['id']; 
$regiao_prestador = $regiao;


$qr_funcionario  = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$_COOKIE[logado]'");
$row_funcionario = mysql_fetch_assoc($qr_funcionario);

$qr_master = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$row_funcionario[id_regiao]'");
$row_master = mysql_fetch_assoc($qr_master);

if(isset($_REQUEST['id'])){
///////////////////////////////
///////////////// DADOS DA SAÍDA //////////////////////
//////////////////////////////////////
$qr_saida = mysql_query("SELECT  A.*, 
B.c_razao as prestador_nome,B.c_cnpj as prestador_cnpj, B.id_regiao as prestador_id_regiao, B.id_projeto as prestador_id_projeto,
C.nome as fornecedor_nome,C.cnpj as fornecedor_cnpj,C.id_regiao as fornecedor_id_regiao,C.id_projeto as fornecedor_id_projeto
 FROM saida as A
                        LEFT JOIN prestadorservico as B 
                        ON A.id_prestador = B.id_prestador
                        LEFT JOIN fornecedores as C
                        ON C.id_fornecedor = A.id_fornecedor
                        WHERE id_saida = '$id_saida'");

                        

$row_saida = mysql_fetch_assoc($qr_saida);

$qr_prestador_pg = mysql_query("SELECT * FROM prestador_pg as A  
                                INNER JOIN prestadorservico as B
                                ON A.id_prestador = B.id_prestador
                                 WHERE A.id_saida = '$id_saida'");
$row_prestador_pg = mysql_fetch_assoc($qr_prestador_pg);
$qr_tipos = mysql_query("SELECT * FROM entradaesaida WHERE id_entradasaida = '$row_saida[tipo]'") or die(mysql_error());
$row_tipo = mysql_fetch_assoc($qr_tipos);
$saida_grupo = $row_tipo['grupo'];
$regiao = $row_saida['id_regiao'];
$regiao_prestador  = $row_saida['prestador_id_regiao'];
$projeto_prestador = $row_saida['prestador_id_projeto'];

$regiao_fornecedor  = $row_saida['fornecedor_id_regiao'];
$projeto_fornecedor = $row_saida['fornecedor_id_projeto'];
$fornecedor_id      = $row_saida['id_fornecedor'];
$prestador_id       = $row_saida['id_prestador'];


if($row_saida['tipo_empresa'] == 1){
    $regiao_prest_forn  = $regiao_prestador;
    $projeto_prest_forn = $projeto_prestador;
} else {
    $regiao_prest_forn  = $regiao_fornecedor; 
    $projeto_prest_forn = $projeto_fornecedor;   

}


} 
////////////////////////////////////////////////////////

//ENCRIPTOGRAFANDO
$linkEnc = encrypt($regiao); 
$linkEnc = str_replace("+","--",$linkEnc);
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>

<head>

<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">

<title>Intranet - Financeiro</title>

<style type="text/css">

body {
	background-color: #F3F3F3;
	text-align:center;
        font-size: 12px;
}

a, a:link, a:active{

	margin:0px;

	font-family: Arial, Helvetica, sans-serif;

	font-size: 12px;

	color: #333;

	text-decoration: underline;

}

</style>

<!-- highslide -->

<link rel="stylesheet" type="text/css" href="../js/highslide.css" />

<script type="text/javascript" src="../js/highslide-with-html.js"></script>

<script type="text/javascript" >

	hs.graphicsDir = '../images-box/graphics/';

	hs.outlineType = 'rounded-white';

	hs.showCredits = false;

	hs.wrapperClassName = 'draggable-header';

</script>

<!-- highslide -->

<link href="style/estrutura.css" rel="stylesheet" type="text/css">

<link rel="stylesheet" href="css/validationEngine.jquery.css" type="text/css" media="screen" title="no title" charset="utf-8" />

<link href="../uploadfy/css/default.css" rel="stylesheet" type="text/css" />

<link href="../jquery/datepicker-lite/jquery-ui-1.8.4.custom.css" rel="stylesheet" type="text/css" />

<link href="../uploadfy/css/uploadify.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="../jquery/jquery-1.4.2.min.js"></script>

<script type="text/javascript" src="../uploadfy/scripts/swfobject.js"></script>

<script language="javascript" type="text/javascript" src="../uploadfy/scripts/jquery.uploadify.v2.1.0.js"></script>

<script type="text/javascript" src="../jquery/datepicker-lite/jquery-ui-1.8.4.custom.min.js"></script>
<script type="text/javascript" src="../jquery/mascara/jquery.maskedinput-1.2.2.js"></script>
<script type="text/javascript" src="../js/formatavalor.js"></script>
<script type="text/javascript" src="cadastro_saida.js"></script>
<script type="text/javascript">

</script>

</head>

<body>
<div id="loading">
	<img src="image/ajax-loader.gif" width="32" height="32" />
    Carregando...
</div>
<div id="base">
<a href="index.php?enc=<?php echo $linkEnc;?>" style="color: #069; float:left;"><img src="../img_menu_principal/voltar.png" title="VOLTAR" /></a>
<span style="clear:left;"></span>
<br><br>	

<form method="post" action="" name="Form" id="Form">
    <div>
        <img src="../imagens/logomaster<?php echo $row_master['id_master']; ?>.gif" width="150" heigth="90"/>
        <div>
            <h3>
                <?php if(isset($id_saida)) { ?> Saída: <?php echo $id_saida;?> - <?php echo $row_saida['nome']; }?>
            </h3>
        </div>
    </div>  
<!----CAMPOS VINDOS DO CADASRO DA SAIDA PARA EDIÇÃO--->
<input type="hidden" name="saida_subgrupo" id="saida_subgrupo" value="<?php echo $row_saida['entradaesaida_subgrupo_id']; ?>"/>
<input type="hidden" name="saida_tipo" id="saida_tipo" value="<?php echo $row_saida['tipo']; ?>"/>
<input type="hidden" name="prestador_pg_regiao" id="prestador_pg_regiao" value="<?php echo $regiao_prestador; ?>"/>
<input type="hidden" name="prestador_pg_projeto" id="prestador_pg_projeto" value="<?php echo $projeto_prestador;?>"/>
<input type="hidden" name="fornecedor_pg_regiao" id="fornecedor_pg_regiao" value="<?php echo $regiao_fornecedor; ?>"/>
<input type="hidden" name="fornecedor_pg_projeto" id="fornecedor_pg_projeto" value="<?php echo $projeto_fornecedor;?>"/>
<input type="hidden" name="prestador_id" id="prestador_id" value="<?php echo $prestador_id;?>"/>
<input type="hidden" name="fornecedor_id" id="fornecedor_id" value="<?php echo $fornecedor_id;?>"/>
<input type="hidden" name="id_nome" id="id_nome" value="<?php echo $row_saida['id_nome'];?>"/>




<!----------------------------------------------------->
    
    
    
	<fieldset class="Cadastro">
<legend>
    <?php if(isset($id_saida)) { echo 'EDITAR SAÍDA'; } else {echo 'CADASTRAR SAÍDA';}?>
</legend> 
<br>
            <div>
            	<label for="projeto">PROJETO:</label>
                <?php $query_projeto = mysql_query("SELECT * FROM projeto WHERE id_regiao = '$regiao' AND status_reg = '1'");?>
		
                <select name="projeto" class="validate[required]" id="projeto">
                 <?php while($row_projeto = mysql_fetch_assoc($query_projeto)){
			print '<option value="'.$row_projeto['id_projeto'].'">'.$row_projeto['id_projeto'] .' - '. $row_projeto['nome'].'</option>';
		  }
                 ?>
                </select>
            </div>

            <div>
            	<label for="banco">CONTA PARA D&Eacute;BITO:</label>
                <?php $result_banco = mysql_query("SELECT * FROM bancos WHERE id_regiao = '$regiao' and interno = '1' AND status_reg = '1' ORDER BY nome ASC");?>

                <select name="banco" class="validate[required]">
                	<?php while($row_banco = mysql_fetch_assoc($result_banco)):
				print '<option value="'.$row_banco['id_banco'].'">'.$row_banco['id_banco'].' - '.$row_banco['nome'].'</option>';
        		  endwhile;
    		?>
                </select>

            </div>

            <div> 
                <label for="grupo">GRUPO:</label>
				<?php 	
                                
                                ////////////////////////////////////////////
                                ////CONDIÇÃO PARA O SORRINDO E PARA A FAHJEL
                                ////////////////////////////////////////////
				  $qr_verifica = mysql_query("SELECT * FROM funcionario as a
                                INNER JOIN regioes as b
                                ON a.id_regiao = b.id_regiao
                                WHERE a.id_funcionario = '$_COOKIE[logado]'");
                                    $row_verifica = mysql_fetch_assoc($qr_verifica);

                                    if($row_verifica['id_master'] == 1 or $row_verifica['id_master'] == 4 ){
                                            
                                        $grupo = array(
                                       
                                       '1'=>'Folha','2'=>'Reserva','3'=>'Taxa administrativa','4'=>'Tranferências ISPV',
                                       '10'	=>'PESSOAL',
                                       '20'	=>'MATERIAL DE CONSUMO',
                                       '30'	=>'SERVIÇOS DE TERCEIROS',
                                       '40'	=>'TAXAS / IMPOSTOS / CONTRIBUIÇÕES',
                                       '50'	=>'SERVIÇOS PÚBLICOS',
                                       '60'	=>'DESPESAS BANCÁRIAS',
                                       '70'	=>'OUTRAS DESPESAS OPERACIONAIS',
                                       '80'	=>'INVESTIMENTOS');   
                                        
                                    } else {
                                        
                                        $grupo = array(                                 
                                        '10'	=>'PESSOAL',
                                        '20'	=>'MATERIAL DE CONSUMO',
                                        '30'	=>'SERVIÇOS DE TERCEIROS',
                                        '40'	=>'TAXAS / IMPOSTOS / CONTRIBUIÇÕES',
                                        '50'	=>'SERVIÇOS PÚBLICOS',
                                        '60'	=>'DESPESAS BANCÁRIAS',
                                        '70'	=>'OUTRAS DESPESAS OPERACIONAIS',
                                        '80'	=>'INVESTIMENTOS');
                                    }
                                  
				
				?>  

                                

                <select name="grupo" id="grupo" class="validate[required]">

                	<option value="" selected>Selecione</option>

                	<?php foreach($grupo as $chave => $valor):

                                  $selected =  ($chave == $saida_grupo )? 'selected="selected"':'';
				  print '<option value="'.$chave.'" '.$selected.'>'.$chave.' - '.$valor.'</option>';
				  endforeach;
					?>
                </select>

			</div> 

                <div id="campo_subgrupo"> 

		<label for="subgrupo">SUBGRUPO:</label>
                <select name="subgrupo" nome="subgrupo" id="subgrupo" class="validate[required]">
                	<option value="">Selecione um subgrupo</option>
                </select>

		</div> 


		<div> 

		<label for="tipo">TIPO:</label>
                <select name="tipo" nome="tipo" id="tipo" class="validate[required]">
                	<option value="">Selecione um tipo</option>
                </select>

		</div> 

              

            <div class="interno" style="display:none;">

            	<label for="interno">Regiao: </label>

                <select name="regiao-prestador"  id="regiao-prestador">

                    <?php 

						$regioes_prestador = mysql_query("SELECT regioes.id_regiao,regioes.regiao,master.nome FROM regioes INNER JOIN master ON regioes.id_master = master.id_master
						 WHERE regioes.status = '1'  AND master.status = '1'");
						  while($rw_regioes_prestador = mysql_fetch_array($regioes_prestador)){

							 if($regiao_prest_forn == $rw_regioes_prestador[0]){
							 	$selected = "selected=\"selected\"";
							 }else{
								$selected = "";
							 }

							 if($repeat != $rw_regioes_prestador[2]){

							 	echo '<optgroup label="'.$rw_regioes_prestador[2].'">'; 

							 }

							 $repeat = $rw_regioes_prestador[2];

							 echo '<option '.$selected.' value="'.$rw_regioes_prestador[0].'" >'.$rw_regioes_prestador[0].' - '.$rw_regioes_prestador[1].'</option>'; 

							 if($repeat != $rw_regioes_prestador[2] && !empty($repeat)){

								 echo '</optgroup>';

							 }

							 $repeat = $rw_regioes_prestador[2];

						  }

					?>
                </select>
            </div>

           
            <div class="interno" style="display:none;">

            	<label for="interno">Projeto: </label>
                <select name="Projeto-prestador"  id="Projeto-prestador">
                   <?php 
                    $qr_projeto = mysql_query("SELECT id_projeto,nome FROM projeto WHERE id_regiao = '$regiao_prest_forn'");
                    while($rw_projeto = mysql_fetch_array($qr_projeto)){                        

                            $selected = ($projeto_prest_forn == $rw_projeto['id_projeto'])?'selected="selected"': '';
                            echo '<option value="'.$rw_projeto[0].'" '.$selected.' >'.$rw_projeto[0].' - '.$rw_projeto[1].'</option>';
                    }
                    ?>
                </select> 
            </div>
             
                <div class="interno" style="display:none;">

                    <label>Tipo da empresa: </label>
                    <span style="text-align:left;">
                        <input name="tipo_empresa" type="radio" value="1" style="width:20px;" <?php echo ($row_saida['tipo_empresa'] == 1)? 'checked':'';?>/> PRESTADOR DE SERVIÇO
                        <input name="tipo_empresa" type="radio" value="2" style="width:20px;" <?php echo ($row_saida['tipo_empresa'] == 2)? 'checked':'';?>/> FORNECEDOR
                   </span>

                </div>
                



            <div class="prestador" style="display:none;">
            	<label for="interno">Prestador: </label>
                <select name="prestador"  id="interno">
                	<option value=""  selected="selected">Selecione um nome</option>

                        
                    <?php  $query_prestador = mysql_query("SELECT * FROM  prestadorservico WHERE id_regiao = '$regiao_prestador' AND status = '1'"); 

					while($row_prestador = mysql_fetch_assoc($query_prestador)){
                                            
                                            $selected = ($row_prestador['id_prestador'] == $row_saida['id_prestador'])? 'selected="selected"': '';
						echo '<option value="'.$row_prestador['id_prestador'].'"'.$selected.' >'.$row_prestador['id_prestador'].' - '.$row_prestador['c_fantasia'].'</option>';
					}
					?>
                </select>
                <a href="#" class="novoPrestador" target="_blank">Não esta na lista.</a>
            </div>


                <div class="fornecedor" style="display:none;">

                    <label for="fornecedor">Fornecedor: </label>
                    <select name="fornecedor"  id="fornecedor">
                       <option value=""  selected="selected">Selecione um fornecedor</option>
                        
                          <?php $query_fornecedor = mysql_query("SELECT * FROM  fornecedores WHERE id_regiao = '$regiao_fornecedor' AND status = '1'") or die(mysql_error()); 
                          
                        
                            while($row_fornecedor = mysql_fetch_assoc($query_fornecedor)){

                                    echo '<option value="'.$row_fornecedor['id_fornecedor'].'" >'.$row_fornecedor['id_fornecedor'].' - '.$row_fornecedor['nome'].'</option>';
                            }                            
                            ?>
                        
                    </select>
                    <!--<a href="#" class="novoPrestador" target="_blank">Não esta na lista.</a>-->

                </div>

            

           

		<div class="nomes-cad"> 

			  <label for="nome">NOME:</label>
                        <select name="nome"  id="nome">
                                <option value="">Selecione um nome</option>
                                                              
                             </select>
                        <a href="#" class="highslide" onClick="return false">      Adicionar </a>

		</div>

            <div>
            	<label for="descricao">DESCRIÇÃO:</label>
                <input name="descricao" type="text" id="descricao" size="500" value="<?php echo $row_saida['especifica']?>"/>
            </div>

             <div>
            	<label for="adicional">VALOR ADICIONAL:</label>
                <input name="adicional" type="text" id="adicional" onKeyDown="FormataValor(this,event,17,2)" value="<?php echo $row_saida['adicional']?>"/>
            </div>
        
            <div>
            	<label for="referencia">REFERÊNCIA:</label>
                <select name="referencia"id="referencia">   
                    <option value="">Selecione...</option>
                <?php
                 $qr_referencia = mysql_query("SELECT * FROM tipos_referencia WHERE status = 1");
                 while($row_ref = mysql_fetch_assoc($qr_referencia)):      
                     
                     $selected = ($row_ref['id_referencia'] == $row_saida['id_referencia'])? 'selected="seleceted"':'';
                     echo '<option value="'.$row_ref['id_referencia'].'"'.$selected.'>'.$row_ref['descricao'].'</option>';
                 endwhile;
                
                ?>
                
                </select>
            </div>
            
             <div id="campo_bens" style="display:none;">
            	<label for="bens">TIPOS DE BENS:</label>
                <select name="bens"id="bens">
                    <option value="">Selecione...</option>
                    <option value=""></option>
                <?php
                 $qr_bens = mysql_query("SELECT * FROM tipos_bens WHERE status = 1");
                 while($row_bens = mysql_fetch_assoc($qr_bens)):
                     
                       $selected = ($row_bens['id_bens'] == $row_saida['id_bens'])? 'selected="seleceted"':'';
                     echo '<option value="'.$row_bens['id_bens'].'" '.$selected.'>'.$row_bens['descricao'].'</option>';
                 endwhile;
                
                ?> 
                
                </select>
            </div>

            <div>
            	<label for="tipo_pagamento">TIPO PAGAMENTO:</label>
                <select name="tipo_pagamento"  id="tipo_pagamento" >
                <option value="">Selecione...</option>   
                <option value=""></option>
                    <?php
                    $qr_tipo_pg = mysql_query("SELECT * FROM tipos_pag_saida");
                    while($row_tp_pg = mysql_fetch_assoc($qr_tipo_pg)):
                         
                         $selected = ($row_tp_pg['id_tipo_pag'] == $row_saida['id_tipo_pag_saida'])? 'selected="selected"':'';
                        echo '<option value="'.$row_tp_pg['id_tipo_pag'].'" '.$selected.'>'.sprintf('%02d',$row_tp_pg['id_tipo_pag']).' - '.$row_tp_pg['descricao'].'</option> ';

                    endwhile;
                    ?>
                </select>
            </div>      

           <div id="campo_boleto">
            	<label for="tipo_boleto">TIPO BOLETO:</label>
                <select name="tipo_boleto"  id="tipo_boleto" >
                <option value="">Selecione...</option>   
                <option value=""></option>
                    <?php
                    $qr_tipo_boleto = mysql_query("SELECT * FROM tipo_boleto");
                    while($row_tp_boleto = mysql_fetch_assoc($qr_tipo_boleto)):
                        $selected = ($row_tp_boleto['id_tipo'] == $row_saida['tipo_boleto'])? 'selected="selected"':'';
                        echo '<option value="'.$row_tp_boleto['id_tipo'].'" '.$selected.' >'.$row_tp_boleto['nome'].'</option> ';

                    endwhile;
                    ?>
                </select>
            </div>   

            <?php
     
                
            $style1 =   ($row_saida['tipo_boleto'] == 1)? '': 'display:none;'; //campo_codigo_consumo
            $style2 =   ($row_saida['tipo_boleto'] == 2)? '': 'display:none;'; //campo_codigo_gerais
                
            
           ?>
            
            <div id="campo_nosso_numero" style="display: none;">
            	<label for="nosso_numero">NOSSO NÚMERO:</label>
                <input name="nosso_numero" type="text" id="nosso_numero" value="<?php echo $row_saida['nosso_numero'];?>"/>
            </div>



                       
             <div class="campo_codigo_consumo" style="<?php echo  $style1; ?>">
                 LINHA DIGITÁVEL/CÓDIGO DE BARRA
            </div>     
            <?php
            $cod_barra_consumo[] = substr($row_saida['cod_barra_consumo'],0,11);
            $cod_barra_consumo[] = substr($row_saida['cod_barra_consumo'],11,1);
            $cod_barra_consumo[] = substr($row_saida['cod_barra_consumo'],12,11);
            $cod_barra_consumo[] = substr($row_saida['cod_barra_consumo'],23,1);
            $cod_barra_consumo[] = substr($row_saida['cod_barra_consumo'],24,11);
            $cod_barra_consumo[] = substr($row_saida['cod_barra_consumo'],35,1);
            $cod_barra_consumo[] = substr($row_saida['cod_barra_consumo'],36,11);
            $cod_barra_consumo[] = substr($row_saida['cod_barra_consumo'],47,1);
            
            $cod_barra_gerais[]  = substr($row_saida['cod_barra_gerais'],0,5);
            $cod_barra_gerais[]  = substr($row_saida['cod_barra_gerais'],5,5);
            $cod_barra_gerais[]  = substr($row_saida['cod_barra_gerais'],10,5);
            $cod_barra_gerais[]  = substr($row_saida['cod_barra_gerais'],15,6);
            $cod_barra_gerais[]  = substr($row_saida['cod_barra_gerais'],21,5);
            $cod_barra_gerais[]  = substr($row_saida['cod_barra_gerais'],26,6);
            $cod_barra_gerais[]  = substr($row_saida['cod_barra_gerais'],32,1);
            $cod_barra_gerais[]  = substr($row_saida['cod_barra_gerais'],33,14);
            
            
            
            ?>
            
            <div class="campo_codigo_consumo" style="<?php echo  $style1; ?>">                
                  <input name="codigo_barra_consumo[]" type="text" id="codigo_barra_consumo1" style="width:100px;" maxlength="11" value="<?php echo $cod_barra_consumo[0];?>"/>-
                  <input name="codigo_barra_consumo[]" type="text" id="codigo_barra_consumo2" style="width:30px;" maxlength="1" value="<?php echo $cod_barra_consumo[1];?>"/>
                  <input name="codigo_barra_consumo[]" type="text" id="codigo_barra_consumo3" style="width:100px;" maxlength="11" value="<?php echo $cod_barra_consumo[2];?>"/>-
                  <input name="codigo_barra_consumo[]" type="text" id="codigo_barra_consumo4" style="width:30px;" maxlength="1" value="<?php echo $cod_barra_consumo[3];?>"/>
                  <input name="codigo_barra_consumo[]" type="text" id="codigo_barra_consumo5" style="width:100px;" maxlength="11" value="<?php echo $cod_barra_consumo[4];?>"/>-
                  <input name="codigo_barra_consumo[]" type="text" id="codigo_barra_consumo6" style="width:30px;" maxlength="1" value="<?php echo $cod_barra_consumo[5];?>"/>
                  <input name="codigo_barra_consumo[]" type="text" id="codigo_barra_consumo7" style="width:100px;" maxlength="11" value="<?php echo $cod_barra_consumo[6];?>"/>-
                  <input name="codigo_barra_consumo[]" type="text" id="codigo_barra_consumo8" style="width:30px;" maxlength="1" value="<?php echo $cod_barra_consumo[7];?>"/>                                    
            </div>


            <div class="campo_codigo_gerais" style="<?php echo  $style2; ?>"> 
                 LINHA DIGITÁVEL/CÓDIGO DE BARRA
            </div>     
          
            <div class="campo_codigo_gerais" style="<?php echo  $style2; ?>">                
                  <input name="campo_codigo_gerais[]" type="text" id="campo_codigo_gerais1" style="width:50px;" value="<?php echo $cod_barra_gerais[0];?>"/>.
                  <input name="campo_codigo_gerais[]" type="text" id="campo_codigo_gerais2" style="width:50px;" value="<?php echo $cod_barra_gerais[1];?>"/>.
                  <input name="campo_codigo_gerais[]" type="text" id="campo_codigo_gerais3" style="width:50px;" value="<?php echo $cod_barra_gerais[2];?>"/>
                  <input name="campo_codigo_gerais[]" type="text" id="campo_codigo_gerais4" style="width:60px;" value="<?php echo $cod_barra_gerais[3];?>"/>.
                  <input name="campo_codigo_gerais[]" type="text" id="campo_codigo_gerais5" style="width:50px;" value="<?php echo $cod_barra_gerais[4];?>"/>
                  <input name="campo_codigo_gerais[]" type="text" id="campo_codigo_gerais6" style="width:60px;" value="<?php echo $cod_barra_gerais[5];?>"/>.
                  <input name="campo_codigo_gerais[]" type="text" id="campo_codigo_gerais7" style="width:30px;" value="<?php echo $cod_barra_gerais[6];?>"/>
                  <input name="campo_codigo_gerais[]" type="text" id="campo_codigo_gerais8" style="width:130px;" value="<?php echo $cod_barra_gerais[7];?>"/>    
                 
            </div>  
           
             <div>

            	<label for="real">VALOR REAL:</label>
                <input name="real" type="text" class="validate[required]" id="real" onKeyDown="FormataValor(this,event,17,2)" value="<?php echo $row_saida['valor'];?>"/>

            </div>
<!---
            <div style="display:none;">

            	<label for="bruto">VALOR BRUTO:</label>

                <input name="bruto" type="text" class="validate[required]" id="bruto" onKeyDown="FormataValor(this,event,17,2)" value="0,00"/>

            </div>
---->
      <div>

            	<label for="data">DATA PARA DÉBITO:</label>
                <input type="text" name="data" id="data" class="date" value="<?php echo implode('/',array_reverse(explode('-',$row_saida['data_vencimento'])));?>"/>
            </div>

            <div id="barra_upload"></div>
            <center>
                <input type="file" id="FileUp"/>
            </center>

            <center>

      
       <?php if(isset($id_saida)) { ?>                
               <input type="submit" name="atualizar" id="button" class="submit-go" value="Atualizar"> 
               <input type="hidden" name="atualizar" value="atualizar">
               <input type="hidden" name="id_saida" value="<?php echo $id_saida;?>">
      <?php } else { ?>
               <input type="submit" class="submit-go" name="cadastrar" value="Cadastrar"/>
                <input type="hidden" name="cadastrar" value="cadastrar">
      
      <?php } ?>                 	

            	<input type="hidden" name="regiao" value="<?=$regiao?>" />
                <input type="hidden" name="logado" value="<?=$id_user?>" />

            </center>            

		</fieldset> 

</form>

</div> 
    

<div style="display:none">

    <div id="cadastro_nomes">
    <div style="height:20px; border-bottom: 1px solid silver"> 
	        <a href="#" onClick="return hs.close(this)" class="control">Fechar</a> 

	</div> 

    <form name="form2" method="post"  id="form2" action="">

    <table width="0" border="0" cellspacing="0" cellpadding="2">
    <tr>
      <td align="right">NOME:</td>
      <td>
        <input type="text" name="nome" id="nome"></td>
      </tr>

    <tr>

      <td align="right">CNPJ/CPF:</td>

      <td>

        <input type="text" name="cpf" id="cpf"></td>

      </tr>

    <tr>

      <td align="right">DESCRICAO:</td>
      <td>
        <input type="text" name="descricao" id="descricao"></td>
      </tr>
    <tr>
        
        
        

      <td colspan="2" align="center">

      <input type="hidden" name="tipo" id="tipo2">

      <input type="submit" name="button" id="button" class="submit-go" value="Cadastrar"></td>

    </tr>

    </table>

    </form>

    </div>  

</div>

</body>

</html>

