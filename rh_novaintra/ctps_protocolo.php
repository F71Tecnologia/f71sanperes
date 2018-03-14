<?php
include('../classes/ctpsClass.php'); 
include('../classes/LogClass.php');
include('../wfunction.php');
include('../conn.php');
include('../empresa.php');

$img = new empresa();
$ctps = new CtpsClass();
$log = new Log();
$usuario = carregaUsuario();

if(empty($_COOKIE['logado'])){
    print "Efetue o Login<br><a href='login.php'>Logar</a> ";exit;
}


$regiao = $_REQUEST['regiao'];
$id_ctps = $_REQUEST['id_ctps'];
$id_case = $_REQUEST['case'];
$id_user = $usuario['id_funcionario'];

$ctps->setIdControle($id_ctps);
$ctps->select();
$ctps->getRow();

$result_local = mysql_query("SELECT * FROM regioes where id_regiao = ".$ctps->getIdRegiao());
$row_local = mysql_fetch_array($result_local);

$data_hoje = date('d/m/Y');
$ano_hoje = date('Y');

$id_user = $_COOKIE['logado'];


if (isset($_REQUEST['entregar_ctps']) && !empty($_REQUEST['entregar_ctps'])) {

    $msg_protocolo = array(titulo => "ENTREGA", texto => "Entregue em devolu&ccedil;&atilde;o", funcionario => $ctps->getEntreguePor(), assinatura => "ENTREGA");
    
}
else {
    
    $msg_protocolo = array(titulo => "RECEBIMENTO", texto => "Recebido", funcionario => $ctps->getRecebidoPor() , assinatura => "RECEBIMENTO");

}


?>

<!DOCTYPE html>

<html>
    <head>
        <title>:: Intranet ::</title>
        <meta charset="ISO-8859-9">
        <title>:: Intranet ::</title>
        <link rel="shortcut icon" href="../../favicon.png">
        <link href="../resources/css/bootstrap.css" rel="stylesheet">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet">
        <link href="../resources/css/font-awesome.min.css" rel="stylesheet">
        <link href="../resources/css/style-print.css" rel="stylesheet">
       
        <script src="../js/jquery-1.10.2.min.js"></script>
        <script src="../uploadfy/scripts/jquery.uploadify.v2.1.0.js"></script>

        <script language="javascript"> 

          //o parâmentro form é o formulario em questão e t é um booleano 
          function ticar(form, t) { 
            campos = form.elements; 
            for (x=0; x<campos.length; x++) 
              if (campos[x].type == "checkbox") campos[x].checked = t; 
          } 
          
          $(function () {
              
            $('#voltar').click(function() {
                
                window.location.replace("http://f71lagos.com/intranet/rh_novaintra/ctps.php");
                
            });                
            
          });


        self.print()

        </script> 
    </head>

    <body>
    
        <div class="no-print">
            <nav class="navbar navbar-default navbar-fixed-top">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-3">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                    </div>
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-3">
                        <div class="text-center">
                            <button type="button" id="voltar" class="btn btn-default navbar-btn"><i class="fa fa-reply"></i>Voltar</button>
                            <button type="button" id="imprimir" class="btn btn-success navbar-btn"><i class="fa fa-print"></i> Imprimir</button>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
 
        <div class="pagina">
            <?php 
            for ($i = 1; $i <= 2; $i++) {
                
                if($i==2){
            ?>
            <div style="border-style: solid; border-bottom-width: 3px; border-top-width: 0; border-right-width: 0; border-left-width: 0; border-style: dashed;">
            </div>     
            <?php   
                }
            ?>
            
            <div class="text-center" style="height: 120px">
                <?php $img->imagem(); ?>
            </div>
            <div>
                <p align="center">
                <span style="font-size: 13pt; font-family: Arial; color: #000000;">
                <strong>
                PROTOCOLO DE <?=$msg_protocolo['titulo']?> DA CARTEIRA  PROFISSIONAL
                </strong>
                </span></br>
                Decreto LEI N&ordm; 229, de 28/02/1967 ( Alterando o Art. 29 da Lei 5.452  - C.L.T. ) <br>
                </p>
                <p align="center">
                  DADOS DA CARTEIRA
                </p>
                <p class="style35">Carteira Profissional N&ordm;:
                      <span class="style41"><?=$ctps->getNumero()?></span>
                      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; S&eacute;rie&nbsp;: 
                      <span class="style41"><?=$ctps->getSerie()?></span>
                      &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; UF: <span class="style41"><?=$ctps->getUf()?></span>
                </p>
                <p class="style35">Nome: <span class="style41">
                  <?=$ctps->getNome()?>
                  </span>
                </p>  
                <p class="style35"><?=$msg_protocolo["texto"]?> Por:
                    <span class="style41">
                      <?=$msg_protocolo["funcionario"]?>
                    </span>
                </p>
                <p align="center" class="style40">
                      <?=$msg_protocolo["texto"]?> a Carteira Profissional  supra discriminada com as respectivas anota&ccedil;&otilde;es.<br>
                </p>
            </div>
            <div style="height: 50px;">
            </div>
            <div>
                <p align="center" class="style40"><span class="style41">
                  <?=$row_local['regiao']?>
                  ,</span> __________ de ________________________________ de <span class="style41">
                  <?=$ano_hoje?>
                  </span>.
                  </br>
                  </br>
                  </br>
                  _________________________________________________
                </p>
            </div>
            <div      
                ASSINATURA DO RESPONS&Aacute;VEL PELO <?=$msg_protocolo["assinatura"]?>
                </p>
                <div  class="no-print">
                </div>
                <div>
                    <?php
                    $rod = new empresa();
                    $rod -> rodape();
                    ?>
                </div>        
            </div>
            <?php
            }
            ?>
        </div>

            
        <!-- javascript aqui -->
        <script src="../js/jquery-1.10.2.min.js" type="text/javascript"></script>
        <script src="../resources/js/print.js" type="text/javascript"></script>

</body>
</html>
