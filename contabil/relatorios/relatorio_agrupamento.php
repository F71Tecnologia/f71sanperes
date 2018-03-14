<?php

include("../../conn.php");
?>
  <!-- Bootstrap -->        
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css">
        <!--<link href="../../resources/css/bootstrap.css" rel="stylesheet" media="all">-->
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-compras.css" rel="stylesheet" media="all">
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="all">
        <link href="../../resources/css/ui-autocomplete-theme.css" rel="stylesheet" media="all">
        <link href="../../resources/css/main.css" rel="stylesheet" media="all">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="all">
	 <link href="bootstrap.css" rel="stylesheet" media="all">
	  <link href="../../resources/css/style-print.css" rel="stylesheet" media="print">
	
	   <style>
            @media print {
                .show_print {
                    display: table-row!important;
                }
            }
	    
	    /*.imgs{
		max-width:200px; 
	    }*/
        </style>
	
	
	
	
<?php
$idAgrupamento= $_REQUEST['id_agrupamento'];



$SaidaPai= "SELECT * FROM saida where id_saida= '$idAgrupamento'";
$querySaidaPai= mysql_query($SaidaPai);
	$result= mysql_fetch_assoc($querySaidaPai);
	$idBanco= $result['id_banco'];
	
    $BancoPai= "SELECT * from bancos where id_banco= '$idBanco'";
    //print $BancoPai;
    $queryBancoPai= mysql_query($BancoPai);
    $resultBancoPai= mysql_fetch_assoc($queryBancoPai);
    


//print $idAgrupamento;
    /*
 $RetornoAgrupa= "SELECT A.*, B.nome, B.n_documento FROM saida_agrupamento_assoc AS A 
 LEFT JOIN saida AS B ON (A.id_saida= B.id_saida)  WHERE A.id_saida= '$idAgrupamento'";*/
 $RetornoAgrupa= "SELECT * FROM saida where id_saida= '$idAgrupamento'";
 //print $RetornoAgrupa;
 $queryRetornoAgrupa= mysql_query($RetornoAgrupa); 


?>
	<div style="margin-top: 60px;"></div>
<div class="container">
     <button type="button" id="imprimir" class="btn btn-success navbar-btn"><i class="fa fa-print"></i>
                        Imprimir
                    </button>
    <!--
    <div class="container-fluid">
	<h4 align="center" style="color:blue;">SANPERES</h4>
    </div></br>
   <div class="container-fluid">
	<h4 align="center" >Relatório de agrupamento Nº (<?php 	
	print $result['id_saida'];
      ?>)
	</h4>
    </div></br>-->
      <div class="text-center">
                <?php
                include('../../empresa.php');
                $img = new empresa();
                $img->imagem();
                $row_master = mysql_fetch_assoc($img->re);
//                print_array($row_master);
                ?>
            </div>
    
<table class="table table-striped">
  <thead>
       <tr>
	    <td scope="col"><strong>Agencia:</strong></td><td scope="col"> <?php print $resultBancoPai['agencia']?></td>
	    <td scope="col"><strong>Conta:</strong> <?php print $resultBancoPai['conta']?></td>
	    <td scope="col"><strong>Data emissão:</strong> <?php 
	    
	    //print $result['data_proc']
		echo date_format(new DateTime($result['data_proc']), "d/m/Y");    
		    ?>
	    
	    </td>
	    <td scope="col"><strong>Data Vencimento:</strong> <?php print  date_format(new DateTime($result['data_vencimento']), "d/m/Y");?></td>      
       </tr>
       <tr>
	    <td scope="col"><strong>Titular:</strong></td>
	    <td scope="col" colspan="5"> SANPERES AVALIAÇÃO E VISTORIAS EM VEICULOS LTDA-ME</td>
       </tr>
       <tr>
	   <td scope="col"><strong>Valor:</strong></td>
	    <td scope="col" colspan="4"> <?php print $result['valor'];?></td>
	    
       </tr>
        <tr>
	    <td scope="col"> &nbsp;</td>
	    <td scope="col"> &nbsp;</td>
	    
       </tr>
       <tr>
	    <th scope="col">CODIGO</th>
	    <th scope="col">DATA</th>
	    <th scope="col">DESCRIÇÃO</th>
	    <th scope="col">NOTA</th>
	    <th scope="col">VALOR</th>
       </tr>
  </thead>
  <tbody>
      <?php 
	while($array= mysql_fetch_array($queryRetornoAgrupa)){
	    
      ?>
    <tr>
      <th scope="row"><?=$array['id_saida']?></th>
      <td><?=$array['data_proc']?></td>
      <td><?=$array['nome']?></td>
      <td><?=$array['n_documento']?></td>
      <td><?=$array['valor']?></td>
    </tr>   
	<?php }?>
  </tbody>
</table>
</div>
	<script src="../../js/jquery-1.10.2.min.js"></script>
            <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
	      <script>
       
      $(function() {	 
	    $('#imprimir').click(function () {
		 $('#imprimir').hide();
	    })
	})
	</script>
	 <script src="../../resources/js/print.js" type="text/javascript"></script>