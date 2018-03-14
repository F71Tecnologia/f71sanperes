<?php
if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='login.php'>Logar</a> ";exit;
}

include("conn.php");
include("wfunction.php");

$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

$id = $_REQUEST['id'];
$id_user = $_COOKIE['logado'];
$regiao = $usuario['id_regiao'];

$data = date('d/m/Y');

$result_local = mysql_query("SELECT * FROM regioes where id_regiao = '$regiao'");
$row_local = mysql_fetch_array($result_local);

$nome_pagina = 'RELATÓRIO DE PATRIMÔNIO';
$breadcrumb_config = array("nivel"=>"../", "key_btn"=>"38", "area"=>"Contabilidade", "id_form"=>"form1", "ativo"=>$nome_pagina);
$breadcrumb_pages = array("Controle de Patrimônio" => "contabil/patrimonio/");

$query = "SELECT 
            CAST( REPLACE(A.valor, ',', '.') as decimal(13,2)) AS valor2,G.nome as projeto,
            F.id_grupo,F.nome_grupo,
            E.id_subgrupo,A.data_vencimento,DATE_FORMAT(A.data_vencimento, '%d/%m/%Y') as data_vencimentoBR,
            A.id_saida,A.id_projeto,A.id_banco,A.id_regiao,A.nome,A.especifica,A.tipo,
            A.comprovante,A.id_bens,A.entradaesaida_subgrupo_id,A.n_documento as nota,
            D.c_razao,D.c_cnpj,D.especificacao,'Unidade' as localizacao,
            LPAD(C.id_bens,'2','0') as codbem,C.descricao,
            G.cod_sesrj,G.cod_contrato
            FROM saida AS A
            LEFT JOIN entradaesaida_nomes AS B ON (A.id_nome = B.id_nome)
            LEFT JOIN tipos_bens AS C ON (A.id_bens = C.id_bens)
            LEFT JOIN prestadorservico AS D ON (A.id_prestador=D.id_prestador)
            LEFT JOIN entradaesaida_subgrupo AS E ON (E.id=A.entradaesaida_subgrupo_id)
            LEFT JOIN entradaesaida_grupo AS F ON (F.id_grupo=E.entradaesaida_grupo)
            LEFT JOIN projeto AS G ON (G.id_projeto=A.id_projeto)
            WHERE A.id_bens != 0 AND A.`status` = 2 AND A.id_regiao = {$regiao}
            ORDER BY projeto,A.data_vencimento"

?>
<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: <?= $nome_pagina ?></title>
        <link href="favicon.png" rel="shortcut icon" />
        <!-- Bootstrap -->
        <link href="resources/css/bootstrap.css" rel="stylesheet" media="screen">
        <link href="resources/css/bootstrap-theme.css" rel="stylesheet" media="screen">
        <link href="resources/css/bootstrap-note.css" rel="stylesheet" media="screen">
        <link href="resources/css/ui-datepicker-theme.css" rel="stylesheet" media="screen">
        <link href="resources/css/main.css" rel="stylesheet" media="screen">
        <link href="resources/css/font-awesome.css" rel="stylesheet" media="screen">
        <link href="resources/dropzone/dropzone.css" rel="stylesheet" media="screen">
        <link href="resources/css/bootstrap-dialog.min.css" rel="stylesheet" type="text/css">
        <link href="css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
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
        <?php include("template/navbar_default.php"); ?>
        <div class="container">
            <div class="page-header box-contabil-header"><h2><span class="fa fa-bar-chart"></span> - Contabilidade<small> - <?= $nome_pagina ?></small></h2></div>

            <div class="panel panel-default">
                <div class="panel-body">
                    <table class="table table-striped table-condensed table-hover text-sm valign-middle">
                        <tr>
                          <td colspan="10" ><div align="left"><span class="style38"><br>
                            <span class="style25">
                <?php
                include "empresa.php";
                $img= new empresa();
                $img -> imagem();
                ?><!--<img src="imagens/certificadosrecebidos.gif" alt="img" width="120" height="86" align="absmiddle">-->Relatório de Patrimônio</span></span></div></td>
                        </tr>
                        <tr>
                          <td colspan="10">
                                <div align="center">
                                    <span class="style41">Local: <?=$row_local['regiao']?> - Data: <?=$data?> </span><br><br>
                                </div>
                          </td>
                        </tr>
                        <tr class="style7">
                            <td width="7%" bgcolor="#030"><div align="center" class="style27"><span class="style41">N&uacute;mero</span></div></td>
                            <td width="10%" bgcolor="#030"><div align="center" class="style27"><span class="style41">Data de Cadastro</span></div></td>
                            <td width="12%" bgcolor="#030"><div align="center" class="style27"><span class="style41">Descri&ccedil;&atilde;o ou nome</span></div></td>
                            <td width="9%" bgcolor="#030"><div align="center" class="style27"><span class="style41">Marca ou Modelo</span></div></td>
                            <td width="10%" bgcolor="#030"><div align="center" class="style27"><span class="style41">Localiza&ccedil;&atilde;o</span></div></td>
                            <td width="15%" bgcolor="#030"><div align="center" class="style27"><span class="style41">Valor Estimado</span></div></td>
                            <td width="14%" bgcolor="#030"><div align="center" class="style27"><span class="style41">Descri&ccedil;&atilde;o de defeito / ranhura ou marcas vis&iacute;veis</span></div></td>
                            <td width="18%" align="center" valign="middle" bgcolor="#030">Nota fiscal</td>
                            <td width="36%" align="center" valign="middle" bgcolor="#030">Data de Compra</td>
                            <td width="5%" bgcolor="#030"><div align="center" class="style27"><span class="style41">Foto</span></div></td>
                        </tr>
                        <tbody>
                        <?php
                        $result = mysql_query($query);
                        $projetoAnt = "";
                        while($row = mysql_fetch_array($result)){

                            /*if($row['foto'] == "0"){
                            $foto = "<img src='imagens/foto_n.gif' border=0>";
                            }else{
                            $link = "patrimonio/".$regiao."patrimonio".$row['0'].$row['foto'];
                            $foto = "<a href='$link' target='_blanck'><img src='imagens/foto.gif' border=0></a>";
                            }

                            $valor = $row['valor'];
                            $valor = str_replace(",",".", $valor);
                            $valor_F = number_format($valor,2,",",".");*/
                            
                            if($projetoAnt != $row['projeto']){
                                echo "<tr><th colspan='10'>{$row['projeto']}</th></tr>";
                                $projetoAnt = $row['projeto'];
                            }
                            
                            print "     
                            <tr>
                                <td>{$row['id_saida']}</td>
                                <td>{$row['data_vencimentoBR']}</td>
                                <td>{$row['especificacao']}</td>
                                <td></td>
                                <td>{$row['localizacao']}</td>
                                <td><div align='center'>R$ ".  number_format($row['valor2'],2,",",".")."</div></td>
                                <td></td>
                                <td><div align='center'>{$row['nota']}</div></td>
                                <td>{$row['data_vencimentoBR']}</td>
                                <td> - </td>
                            </tr>
                            ";

                            $soma += $row['valor2'];
                        }
                        $total_f = number_format($soma,2,",",".");
                        ?>
                        </tbody>
                        <tr>
                            <td colspan="10">
                                <center spry:hover="style41" class="style42"><? print"Valor Total Estimado: R$ $total_f" ?></center>          
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            <?php include('template/footer.php'); ?>
        </div>
        
        <script src="js/jquery-1.10.2.min.js"></script>
        <script src="js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="resources/js/bootstrap.min.js"></script>
        <script src="resources/js/bootstrap-dialog.min.js"></script>
        <script src="js/jquery.validationEngine-2.6.js"></script>
        <script src="js/jquery.validationEngine-pt_BR-2.6.js"></script>
        <script src="js/jquery.maskedinput-1.3.1.js"></script>
        <script src="js/jquery.maskMoney.js" type="text/javascript" ></script>
        <script src="resources/dropzone/dropzone.js"></script>
        <script src="resources/js/main.js"></script>
        <script src="js/global.js"></script>
        <script src="js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
        <script src="js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
        <script>
            $(function() {                
                $("#form1").validationEngine({promptPosition : "topRight"});
            });
        </script>
    </body>
</html>