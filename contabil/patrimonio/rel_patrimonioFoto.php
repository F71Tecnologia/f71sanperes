<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";exit;
}

include("../../conn.php");
include("../../wfunction.php");
include("../../classes/global.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$id = $_REQUEST['id'];
$id_user = $_COOKIE['logado'];
$regiao = $usuario['id_regiao'];

$data = date('d/m/Y');

$id_projeto = $_REQUEST['id_projeto'];

$result_local = mysql_query("SELECT * FROM regioes where id_regiao = '$regiao'");
$row_local = mysql_fetch_array($result_local);

$nome_pagina = 'Relatório de Patrimonio (Foto)';
$breadcrumb_config = array("nivel"=>"../../", "key_btn"=>"38", "area"=>"Contabilidade", "id_form"=>"form1", "ativo"=>$nome_pagina);
$breadcrumb_pages = array("Controle de Patrimônio" => "index.php");
?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: <?= $nome_pagina ?></title>
        <link href="../../favicon.png" rel="shortcut icon" />
        <!-- Bootstrap -->
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="../../resources/dropzone/dropzone.css" rel="stylesheet" media="screen">
        <link href="../../resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <script language="javascript"> 

  //o parâmentro form é o formulario em questão e t é um booleano 
  function ticar(form, t) { 
    campos = form.elements; 
    for (x=0; x<campos.length; x++) 
      if (campos[x].type == "checkbox") campos[x].checked = t; 
  } 

        </script> 
    </head>
    <body>
        <?php include("../../template/navbar_default.php"); ?>
        <div class="container">
            <div class="page-header box-contabil-header"><h2><span class="fa fa-bar-chart"></span> - Contabilidade<small> - <?= $nome_pagina ?></small></h2></div>

            <div class="panel panel-default">
                  <div class="panel-heading">Relatório de Patrimonio - Local: <?=$row_local['regiao']?> - Data: <?=$data?></div>
                <div class="panel-body">
                    <form>
                    <div class="form-group">
                            <label class="col-xs-2 control-label">Projeto:</label>
                            <div class="col-xs-4">
                                <?php echo montaSelect(GlobalClass::carregaProjetosByRegiao($usuario['id_regiao']), null, "id='id_projeto' name='id_projeto' class='validate[custom[select]] form-control'") ?>
                            </div><input type="submit" class="btn btn-info" value="Filtrar">
                        </div>
                    <br>
                    <hr>
                    
                    
                    <?php
                    $result = mysql_query("SELECT *,
                                        A.nome AS A_nome,
                                        A.descricao AS A_descricao,
                                        A.id_patrimonio AS A_id_patrimonio,
                                        B.nome AS categoria_nome,
                                        C.nome AS patrimonio_setor_nome,
                                        D.nome AS projeto_nome,
                                        D.id_projeto AS Did_projeto,
                                        date_format(A.data, '%d/%m/%Y') as data,
                                        date_format(A.data_compra, '%d/%m/%Y') as data_compra 
                                        FROM patrimonio AS A 
                                        LEFT JOIN categoria_p AS B ON (B.id_categoria_p = A.id_categoria_p)
                                        LEFT JOIN setor_patrimonio AS C ON (A.id_setor = C.id_setor_patrimonio)
                                        LEFT JOIN projeto AS D ON (A.id_projeto = D.id_projeto)
                                        WHERE A.id_regiao =  '$regiao'");
                    $cont = 0;
                    while($row = mysql_fetch_array($result)){

                    if($row['foto'] == "0"){
                    $foto = "SEM FOTO";
                    }else{
                    $link = "fotos/".$regiao."patrimonio".$row['0'].$row['foto'];
                    $foto = "<img src='$link' width='188' height='161'>";
                    }

                    $valor = $row['valor'];
                    $valor = str_replace(",",".", $valor);
                    $valor_F = number_format($valor,2,",",".");
                    
                    if($row['Did_projeto'] == $id_projeto){
                    ?>
                    <table class="table table-condensed text-sm valign-middle">
                        <tr>
                            <td width="140" class="bg-success text-right"><strong>Projeto:</td>
                            <td width="315" class="linha"><?=$row['projeto_nome']?></td>
                        </tr>
                        <tr>
                            <td width="140" class="bg-success text-right"><strong>N&uacute;mero:</strong></td>
                            <td width="315" class="linha"><?=$row['A_id_patrimonio']?></td>
                            <td width="233" rowspan="9" align="center" valign="top"><p align="center"><span class="linha">Visualiza&ccedil;&atilde;o:</span></p>
                              <table width="189" height="162" border="1" cellpadding="2" cellspacing="2" bgcolor="#D2EBCF">
                                <tr>
                                  <td height="150" align="center" valign="middle"><?=$foto?></td>
                                </tr>
                              </table>
                            </td>
                        </tr>
                        <tr>
                            <td width="140" class="bg-success text-right"><strong>Data do Cadastro:</td>
                            <td width="315" class="linha"><?=$row['data']?></td>
                        </tr>
                        <tr>
                            <td width="140" class="bg-success text-right"><strong>Descri&ccedil;&atilde;o ou nome:</strong></td>
                            <td width="315" class="linha"><?=$row['A_nome']?></td>
                        </tr>
                        <tr>
                            <td width="140" class="bg-success text-right"><strong>Marca ou Modelo:</strong></td>
                            <td width="315" class="linha"><?=$row['marca']?></td>
                        </tr>
                        <tr>
                            <td width="140" class="bg-success text-right"><strong>Valor Estimado:</strong></td>
                            <td width="315" class="linha"><?=$valor_F?></td>
                        </tr>
                        <tr>
                            <td width="140" class="bg-success text-right"><strong>Descri&ccedil;&atilde;o de defeito:</strong></td>
                            <td width="315" class="linha"><?=$row['A_descricao']?></td>
                        </tr>
                        
                        <tr>
                            <td width="140" class="bg-success text-right"><strong>Categoria:</strong></td>
                           
                            <td  class="linha"><?=$row['categoria_nome']?></td>
                            
                        </tr>
                        <tr>
                            <td width="140" class="bg-success text-right"><strong>Setor:</strong></td>
                            
                            <td class="linha"><?=$row['patrimonio_setor_nome']?></td>
                            
                        </tr>
                        
                        
                        <tr>
                            <td align="right" class="bg-success text-right">Data da Compra:</td>
                            <td class="linha"><?=$row['data_compra']?></td>
                        </tr>
                        <tr>
                            <td align="right" class="bg-success text-right">Nota fiscal:</td>
                            <td class="linha"><?=$row['nota']?></td>
                        </tr>
                    </table>
                    <?php
                    
                    $soma = $soma + $valor;
                    $cont = $cont + 1;
                    $mod = $cont %4;

                        if ($mod == 0){
                            print "<p class='quebra-aqui'><!-- Quebra de página --></p>";
                            echo '<br>';
                        }
                    }

                    $total_f = number_format($soma,2,",","."); ?>
                    <div class="alert alert-default text-right">
                        Valor Total Estimado: R$ <?= $total_f ?>
                    </div>
                    </form>  
                </div> 
            </div>
            <?php } include('template/footer.php'); ?>
        </div>
        
        <script src="../../js/jquery-1.10.2.min.js"></script>
       <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../../resources/js/bootstrap.min.js"></script>
        <script src="../../resources/js/bootstrap-dialog.min.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js"></script>
        <script src="../../js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="../../js/jquery.maskedinput-1.3.1.js"></script>
        <script src="../../js/jquery.maskMoney.js" type="text/javascript" ></script>
        <script src="../../resources/dropzone/dropzone.js"></script>
        <script src="../../resources/js/main.js"></script>
        <script src="../../js/global.js"></script>
        <script src="../../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
       <script src="../../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script>
            $(function() {                
                $("#form1").validationEngine({promptPosition : "topRight"});
            });
        </script>
    </body>
</html>