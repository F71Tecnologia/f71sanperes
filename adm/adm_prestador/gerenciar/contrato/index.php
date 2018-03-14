<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='login.php'>Logar</a> ";
    exit;
}
include('../../../../conn.php');
include('../../../../wfunction.php');
include('../funcoes.php');


$id_prestador = isset($_GET['id']) ? $_GET['id'] : NULL;
$prestador = getPrestador($id_prestador);

$medicos_pj = array();
//var_dump($prestador['prestador_tipo']);
if($prestador['prestador_tipo'] ==9){
    $medicos_pj = getMedicosPj($id_prestador);
    
    $medicos_funcoes = array();
    foreach($medicos_pj as $medico){
        $medicos_funcoes[$medico['id_curso']]['salario'] = $medico['salario'];
        $medicos_funcoes[$medico['id_curso']]['nome_curso'] = $medico['nome_curso'];
        $medicos_funcoes[$medico['id_curso']]['valor_hora'] = $medico['valor_hora'];
    }
    
}

$sql = mysql_query("SELECT * FROM prestador_medico;");
$result = mysql_query($sql);
$medicos = array();
while($row = mysql_fetch_array($result)){
    $medicos[] = $row;
}

if ($prestador['imprimir']<=0) {
    echo "<script>
        alert(\"Você não pode imprimir este CONTRATO DE PRESTAÇÃO DE SERVIÇOS sem ter feito a ABERTURA DE PROCESSO!\");
        window.history.go(-1);
//            window.location.href = '?id=$id_prestador&aba=1';
        </script>";
}

$contratado_em = explode('-', $prestador['contratado_em']);
$meses = array("", '01' => "janeiro", '02' => "fevereiro", '03' => "março", '04' => "abril", '05' => "maio", '06' => "junho", '07' => "julho", '08' => "agosto", '09' => "setembro", '10' => "outubro", '11' => "novembro", '12' => "dezembro");

?>
<html>
    <head>
        <title>CONTRATO DE PRESTAÇÃO DE SERVIÇOS</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link href="../../../../net1.css" rel="stylesheet" type="text/css" />
        <style>
            body{
                font-size: 14px;

                font-family: arial;
                font-weight: 0;
            }
            @media print {
                .bordaescura1px{border: none;}
            }
            .paginaA4 {
                background: #FFF;
                width: 800px;
                min-height: 20px;
                margin: 0 auto;
                padding: 35px;
            }
            h2.titulo { text-align: center; margin-bottom: 40px;}
            p { text-align: justify; }
            .f-left {float: left;}
            .clear {clear: both;}
            .w-metade {width: 50%; }
            .center { text-align: center; }
            .textbold { font-weight: bold; }
            ul { list-style: none; }
            .bg-yellow {background: yellow}
            .underline {text-decoration: underline;}
        </style>
    </head>
    <body leftmargin=0 topmargin=0>
        <div class="paginaA4">
            <h2 class="titulo"><img src="../../../../imagens/logomaster6.gif"></h2>

            <?php
            $tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 1;
            $anexo = isset($_GET['anexo']) ? $_GET['anexo'] : 1;
            
            //define o tipo de contrato
            if($prestador['prestador_tipo'] ==9){
                include 'contrato_pj_medico.php';
            }else{
                header('Location: /intranet/processo/contrato.php?regiao='.$prestador['id_regiao'].'&prestador='.$prestador['id_prestador']);
            }
            
//            switch ($tipo) {
//                case 1:
//                    
//                    if($prestador['prestador_tipo']=='9' || $prestador['nome_projeto']=='BEBEDOURO' ){
//                        include '1_2.php';                        
//                    }else{
//                        header('Location: /intranet/processo/contrato.php?regiao='.$prestador['id_regiao'].'&prestador='.$prestador['id_prestador']);
////                        include '1.php';               
//                    }
//                    break;
//                case 2:
//                    include '2.php';
//                    break;
//                case 3:
//                    include '1_2.php';
//                    break;
//                default:
//                    break;
//            }
            ?>
        </div>
    </body>
</html>
