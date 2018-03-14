<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='login.php'>Logar</a> ";
    exit;
}

require("../conn.php");
require("../wfunction.php");

$usuario = carregaUsuario();

$meses = mesesArray(null);
$anos = anosArray(null, null,array("-1"=>"« Selecione o ano »"));

$filtro = 0;
$total = 0;
if(validate($_REQUEST['gerar'])){
    $filtro = 1;
    
    $ini = $_REQUEST['ano']."-".$_REQUEST['mes']."-01";
    $fim = $_REQUEST['ano']."-".$_REQUEST['mes']."-31";
    
    //SELECIONANDO OS DADOS DO RELATÓRIO
    $qr = "SELECT A.nome,A.sexo,A.cpf,B.nome as funcao,B.area,B.especialidade,D.nome as escola,A.data_entrada,A.locacao,'INSTITUTO DATA RIO - IDR'as lo,
            DATE_FORMAT(A.data_entrada, '%d/%m/%Y') as data_entrada_br FROM rh_clt AS A 
            LEFT JOIN curso AS B ON (A.id_curso=B.id_curso)
            LEFT JOIN projeto AS C ON (A.id_projeto=C.id_projeto)
            LEFT JOIN escolaridade AS D ON(A.escolaridade=D.id)
            WHERE C.id_master = {$usuario['id_master']} AND A.data_entrada <= '$fim' AND 
            (A.data_saida = '0000-00-00' OR A.data_saida BETWEEN $fim AND $ini)
            ORDER BY A.id_projeto,A.nome";
    $result = mysql_query($qr);
    echo "<!-- \r\n $qr \r\n-->";
    $total = mysql_num_rows($result);
    $count = 0;
}

$mesR = (isset($_REQUEST['mes'])) ? $_REQUEST['mes'] : null;
$anoR = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : null;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <title>Relatório Geral de Funcionários com Área e Especialidade</title>
        <link href="../net1.css" rel="stylesheet" type="text/css"/>
        <script type="text/javascript" src="../jquery/jquery-1.4.2.min.js"></script>

        <style>
            .grid thead tr th {font-size: 12px!important;}
            .bt-edit{cursor: pointer;}
        </style>
    </head>

    <body class="novaintra">
        <form name="form1" atcion="" method="post" >
            <div id="content" style="width: 90%;">
                <div id="head">
                    <img src="../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="margin-right: 25px;"/>
                    <div class="fleft">
                        <h2>Todos os Projetos</h2>
                        <h3>Relatório Geral de Funcionários com Área e Especialidade</h3>
                    </div>
                    <div class="fright"> <?php include('../reportar_erro.php'); ?></div> 
                </div>
                <br class="clear"/>
                <br/>

                <fieldset>
                    <legend>Filtro</legend>
                    <p><label class="first">Mês:</label> <?php echo montaSelect($meses, $mesR, "id='mes' name='mes' class='validate[custom[select]]'") ?>  <?php echo montaSelect($anos, $anoR, "id='ano' name='ano' class='validate[custom[select]]'") ?></p>
                    <p class="controls">
                        <input type="submit" class="button" value="Gerar" name="gerar" /> 
                    </p>
                </fieldset>
                <br/><br/>
                <?php if($filtro && $total > 0){ ?>
                <table width="96%" align="center" cellpadding="0" cellspacing="0" border="0" class="grid">
                    <thead>
                        <tr>
                            <th>VINCULO</th>
                            <th>VINCULO EMPREGATICIO</th>
                            <th>NOME</th>
                            <th>SEXO</th>
                            <th>CPF</th>
                            <th>CARGO</th>
                            <th>ESPECIALIDADE</th>
                            <th>AREA DE ATUACAO</th>
                            <th>NIVEL DE ESCOLARIDADE</th>
                            <th>DATA DE ADMISSAO</th>
                            <th>LATACAO</th>
                            <th>LOTACAO GERAL</th>
                            <th>SITUACAO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysql_fetch_assoc($result)) {
                            $nbr_cpf = str_replace(array(".","-"), "", $row['cpf']);

                            $parte_um     = substr($nbr_cpf, 0, 3);
                            $parte_dois   = substr($nbr_cpf, 3, 3);
                            $parte_tres   = substr($nbr_cpf, 6, 3);
                            $parte_quatro = substr($nbr_cpf, 9, 2);
                            $cpf = $parte_um.".".$parte_dois.".".$parte_tres."-".$parte_quatro;
                            ?>
                            <tr class="<?php echo $count++ % 2 ? "even":"odd"?> ">
                                <td>CLT</td>
                                <td>OSS</td>
                                <td><?php echo $row['nome'] ?></td>
                                <td><?php echo $row['sexo'] ?></td>
                                <td><?php echo $cpf ?></td>
                                <td><?php echo $row['funcao'] ?></td>
                                <td><?php echo ($row['especialidade']=="0")?"SEM ESPECIALIDADE":$row['especialidade']; ?></td>
                                <td><?php echo $row['area'] ?></td>
                                <td><?php echo strtoupper($row['escola']) ?></td>
                                <td><?php echo $row['data_entrada_br'] ?></td>
                                <td><?php echo $row['locacao'] ?></td>
                                <td><?php echo $row['lo'] ?></td>
                                <td>
                                <?php 
                                if(strstr($row['funcao'], "12")){
                                    echo "0,5";
                                }elseif(strstr($row['funcao'], "24")){
                                    echo "1,0";
                                }elseif(strstr($row['funcao'], "36")){
                                    echo "1,5";
                                }else{
                                    echo "0,0";
                                }
                                ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="12" class="txright"><strong>Total de funcionários:</strong></td>
                            <td><?php echo $total ?></td>
                        </tr>
                    </tfoot>
                </table>
                <?php } ?>
            </div>
        </form>
    </body>
</html>