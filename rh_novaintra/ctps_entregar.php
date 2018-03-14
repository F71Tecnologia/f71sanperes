<?php
include ('../classes/ctpsClass.php'); 
include ('../classes/LogClass.php');
include ('../wfunction.php');
include ('../conn.php');
include('../empresa.php');

$img = new empresa();
$ctps = new ctps();
$log = new Log();
$usuario = carregaUsuario();

if(empty($_COOKIE['logado'])){
    print "Efetue o Login<br><a href='login.php'>Logar</a> ";exit;
}


$regiao = $_REQUEST['regiao'];
$id_ctps = $_REQUEST['id'];
$id_case = $_REQUEST['case'];
$id_user = $usuario['id_funcionario'];

$ctps->setIdControle($id_ctps);
$ctps->setIdUserCad($id_user);
$ctps->select();
$ctps->getRow();

$result_local = mysql_query("SELECT * FROM regioes where id_regiao = '$regiao'");
$row_local = mysql_fetch_array($result_local);

$data_hoje = date('d/m/Y');
$ano_hoje = date('Y');

if (isset($_REQUEST['entregar_ctps']) && !empty($_REQUEST['entregar_ctps'])) {

    $id_user = $_COOKIE['logado'];
    $data_ent = date('Y-m-d');

    $ctps->setIdControle($id_ctps);
    $ctps->setDataEnt($data_ent);
    $ctps->setIdUserEnt($id_user);
    $ctps->setAcompanhamento(2);
    
    if($ctps->update()){
        
        $msg = "Situação alterada para CTPS ($id_ctps): Entregue";


    }else{

        $msg = $ctps->getError();

    }

    $log->gravaLog('CTPS',$msg);

    print "
    <html>
        <head>
            <script src='../js/jquery-1.10.2.min.js'></script>
            <script>
                alert('$msg');
                $(document).ready(function() {
                    $('#frmEntregar').attr('action','\ctps.php');
                    $('#frmEntregar').submit();
                });
            </script>
        </head>
        <body>
            <form id='frmEntregar' method='POST'>
                <input type='hidden' value='<?=$regiao?>' name='regiao'>
        </form>
        </body>
    </html>
    ";
    
    exit;
    
}

if($id_case==3) {

    $id_user = '';
    $data_ent = '';

    $ctps->setIdControle($id_ctps);
    $ctps->setDataEnt($data_ent);
    $ctps->setIdUserEnt($id_user);
    $ctps->setAcompanhamento(1);
    
    if($ctps->update()){
        
        $msg = "Situação alterada para CTPS ($id_ctps): Entregua desfeita";


    }else{

        $msg = $ctps->getError();

    }

    $log->gravaLog('CTPS',$msg);

    print "
    <html>
        <head>
            <script src='../js/jquery-1.10.2.min.js'></script>
            <script>
                alert('$msg');
                $(document).ready(function() {
                    $('#frmEntregar').attr('action','\ctps.php');
                    $('#frmEntregar').submit();
                });
            </script>
        </head>
        <body>
            <form id='frmEntregar' method='POST'>
                <input type='hidden' value='<?=$regiao?>' name='regiao'>
        </form>
        </body>
    </html>
    ";
    
    exit;
    
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
       

        <script language="javascript"> 

          //o parâmentro form é o formulario em questão e t é um booleano 
          function ticar(form, t) { 
            campos = form.elements; 
            for (x=0; x<campos.length; x++) 
              if (campos[x].type == "checkbox") campos[x].checked = t; 
          } 

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
                            <!--<button type="button" id="voltar" class="btn btn-default navbar-btn"><i class="fa fa-reply"></i>Voltar</button>-->
                            <button type="button" id="imprimir" class="btn btn-success navbar-btn"><i class="fa fa-print"></i> Imprimir</button>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
 
        <div class="pagina">
            <div class="text-center">
                <?php $img->imagem(); ?>
            </div>
            <div>
                <p align="center">
                <span style="font-size: 13pt; font-family: Arial; color: #000000;">
                <strong>
                <br>
                <br>
                <br>
                PROTOCOLO DE ENTREGA DA CARTEIRA  PROFISSIONAL
                </strong></span><br>
                Decreto LEI N&ordm; 229, de 28/02/1967 ( Alterando o Art. 29 da Lei 5.452  - C.L.T. ) <br>
                </p>
                  <p align="center"><br>
                    <br>
                    DADOS DA CARTEIRA<br>
                    <br>
                    <br>
                  </p>
                  <p class="style35">Carteira Profissional N&ordm;:
                        <span class="style41"><?=$ctps->getNumero()?></span>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; S&eacute;rie&nbsp;: 
                        <span class="style41"><?=$ctps->getSerie()?></span>
                        &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; UF: <span class="style41"><?=$ctps->getUf()?></span>
                  </p>
                  <p class="style35">Nome: <span class="style41">
                    <?=$ctps->getNome()?>
                    </span><br>
                  </p>  
                  <p class="style35">Recebido Por:
                      <span class="style41">
                        <?=$ctps->getRowFuncionario()?>
                      </span>
                  </p>
                  <p class="style35"><br>
                    <br>
                  </p>
                <p align="center" class="style40">Recebi em devolu&ccedil;&atilde;o a Carteira Profissional  supra discriminada com as respectivas anota&ccedil;&otilde;es.<br>
                  <br>
                    <br>
                      <br>
                    <br>
                </p>
                <p align="center" class="style40"><span class="style41">
                  <?=$row_local['regiao']?>
                  ,</span> __________ de ________________________________ de <span class="style41">
                  <?=$ano_hoje?>
                  </span>.<br>
                  <br>
                  <br>
                  <br>
                  <br>
                  <br>
                  <br>
                    ____________________________________________<br>
                ASSINATURA DO RESPONS&Aacute;VEL PELO RECEBIMENTO<br>
                <br>
                <br>
                <br>
                <br>
                </p>
                <div  class="no-print">
                    <form action="ctps_entregar.php" method="post" name="form">
                    <p align="center">
                      <input type="submit" name="entregar_ctps" id="gravar" value="ENTREGAR CTPS">
                    <br>
                    <input type="hidden" name="regiao" value="<?=$regiao?>">
                    <input type="hidden" name="id" value="<?=$id_ctps?>">
                    <input type="hidden" name="case" value="2">
                    <br>
                    </p>
                    </form>
                </div>
                <div>
                    </br>
                    </br>
                    </br>
                    </br>
                    <?php
                    $rod = new empresa();
                    $rod -> rodape();
                    ?><br>
                </div>            
            </div>
        </div>
            
        <!-- javascript aqui -->
        <script src="../js/jquery-1.10.2.min.js" type="text/javascript"></script>
        <script src="../resources/js/print.js" type="text/javascript"></script>

</body>
</html>
