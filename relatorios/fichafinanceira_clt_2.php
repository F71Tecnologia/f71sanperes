<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=true';</script>";
    exit;
}

include "../conn.php";
include "../classes/funcionario.php";
include '../classes_permissoes/regioes.class.php';
include '../classes/FolhaClass.php';
include("../wfunction.php");

function formata_numero($num) {
    if (strstr($num, '.') and !empty($num)) {
        return number_format($num, 2, ',', '.');
    } else {
        return $num;
    }
}

//OBJETO
$folha = new Folha();

//VARIÁVEIS
$id_clt = $_REQUEST['id'];
$ano = $_REQUEST['ano'];
$anoSel = (isset($_REQUEST['ano'])) ? $_REQUEST['ano'] : date('Y');
$total = 0;

//DADOS DE FICHA FINANCEIRA POR CLT
$dados = $folha->getDadosClt($id_clt);
$d = mysql_fetch_assoc($dados);


//CARREGA DADOS DO USUÁRIO (NESSE CASO, LOGO DA EMPRESA VINCULADA AO USUÁRIO)
$usuario = carregaUsuario();

//ARRAY DE ANOS
for ($i = 2010; $i <= date('Y'); $i++) {
    $optAnos[$i] = $i;
}

//GERARA
if (isset($_POST['gerar'])) {

    //DADOS PESSOAIS
    $cabecalho = $folha->getCabecalho();
    //MONTA MATRIZ
    $ficha = $folha->getFichaFinanceira($id_clt, $ano);
    //ITENS FICHA
    $itensFicha = $folha->getDadosFicha();
}
?>
<html>
    <head>
        <title>Ficha Financeira - Teste</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link href="../net1.css" rel="stylesheet" type="text/css">
        <script src="../jquery/jquery-1.4.2.min.js" type="text/javascript"></script>
        <!--<script src="../jquery/jquery.tools.min.js" type="text/javascript" ></script>-->
        <!--<script src="../js/global.js" type="text/javascript" ></script>-->
        <script>
            $(function() {
            });
        </script>
        <style media="print">
            fieldset{ display: none;}
            body{ background-color: #FFF;}
            tr{ padding: 20px; background: #333;}
        </style>
    </head>
    <body class="novaintra">       
        <div id="content">
            <div id="head">
                <img src="../imagens/logomaster<?php echo $usuario['id_master']; ?>.gif" class="fleft" style="height: 83; width: 163px; " width="140" height="100"/>
                <div class="fleft" style="width:86.3%">
                    <h2 style="margin-left: 80px;">FICHA FINANCEIRA CLT</h2>
                    <p></p>
                    <table class="grid" border="1" cellspacing="0" cellpadding="0" width="95%" style="margin-left:59px; border: 1px solid #ccc; background: #f1f1f1;"> 
                        <tr>
                            <td align="right"><strong>COD.:</strong></td>
                            <td colspan="5"><?php echo $d['id_clt']; ?></td>
                        </tr>
                        <tr>
                            <td align="right"><strong>Nome:</strong></td>
                            <td colspan="5"><?php echo $d['nome']; ?></td>
                        </tr>
                        <tr>
                            <td align="right"><strong>Data de Nascimento:</strong></td>
                            <td><?php echo $d['data_nasci']; ?></td>
                            <td align="right"><strong>Nacionalidade:</strong></td>
                            <td ><?php echo $d['nacionalidade']; ?></td>
                            <td align="right"><strong>Naturalidade:</strong></td>
                            <td><?php echo $d['naturalidade']; ?></td>
                        </tr>
                        <tr>
                            <td align="right"><strong>CPF:</strong></td>
                            <td><?php echo $d['cpf']; ?></td>
                            <td align="right"><strong>RG:</strong></td>
                            <td><?php echo $d['rg']; ?></td>
                            <td align="right"><strong>Título:</strong></td>
                            <td><?php echo $d['titulo']; ?></td>
                        </tr>
                        <tr>
                            <td align="right"><strong>CTPS:</strong></td>
                            <td><?php echo $d['ctps']; ?></td>
                            <td align="right"><strong>PIS/PASEP:</strong></td>
                            <td colspan="3"><?php echo $d['pis']; ?></td>
                        </tr>
                        <tr>
                            <td align="right"><strong>Função:</strong></td>
                            <td><?php echo $d['nome_curso']; ?></td>
                            <td align="right"><strong>Admissão:</strong></td>
                            <td><?php echo $d['data_entrada']; ?></td>
                            <td align="right"><strong>Afastamento:</strong></td>
                            <td><?php echo $d['data_demis']; ?></td>
                        </tr>
                        <tr>
                            <td align="right"><strong>Tipo de Pag.:</strong></td>
                            <td><?php echo $d['tipo_conta']; ?></td>
                            <td align="right"><strong>Salário:</strong></td>
                            <td><?php echo $d['salario']; ?></td>
                            <td align="right"><strong>Agência:</strong></td>
                            <td><?php echo $d['agencia']; ?></td>
                        </tr>
                        <tr>
                            <td align="right"><strong>Conta:</strong></td>
                            <td><?php echo $d['nome_banco']; ?></td>
                            <td align="right"><strong>Banco:</strong></td>
                            <td colspan="5"><?php echo $d['conta']; ?></td>                          
                        </tr>

                    </table>
                </div>
            </div>
            <br class="clear">
            <br/>

            <form  name="form" action="" method="post" id="form">
                <fieldset>
                    <legend>FICHA FINANCEIRA</legend>
                    <div class="fleft">
                        <p><label class="first" style='margin-top: 10px;'>Ano:</label> <?php echo montaSelect($optAnos, $anoSel, array('name' => "ano", 'id' => 'ano', 'style' => 'padding: 4px; width: 200px; border: 1px solid #ccc;')); ?></p>
                    </div>

                    <br class="clear"/>

                    <p class="controls" style="margin-top: 10px;">
                        <span class="fleft erro"><?php if ($verifica_dirf != 0) echo 'Arquivo já existente!'; ?></span>
                        <input type="hidden" name="id_master" value="<?php echo $id_master; ?>"/>
                        <input type="hidden" name="id_clt" value="<?php echo $id_clt; ?>"/>
                        <!--<input type="submit" name="historico" value="Exibir histórico" id="historico"/>-->
                        <input type="submit" name="gerar" value="Gerar" id="gerar" style=" padding: 7px 35px; border: 1px solid #ccc;"/>
                    </p>
                </fieldset>
            </form>

            <p></p> 
            <table cellspacing="0" cellpadding="0" class="" border="1" width="100%" >
                <tr  style="background-color: #686868; color: #fff;">
                    <td align="center" style=" padding: 5px; font-size: 11px;" >COD</td>
                    <td align="center" >NOME</td>
                    <?php foreach ($cabecalho as $cab) { ?>
                        <td align="center"><?php echo $cab; ?></td>
                    <?php } ?>
                    <td align="center">TOTAL</td>
                </tr>
                <?php foreach ($itensFicha as $k => $values) { ?>
                    <tr style=" padding: 5px; font-size: 11px; height: 25px; padding: 1px;" >

                        <td><?php echo $k; ?></td>
                        <td><?php echo $values["nome"] ." - ". $values['tipo_movimento']; ?></td>
                        <?php foreach ($cabecalho as $key => $cab) { ?> 
                            <?php $total += $values[$key]; ?>
                            <?php $resultado = (!empty($values[$key])) ? "R$ " . number_format($values[$key], "2", ",", ".") : " - " ?>
                            <td align="center"><?php echo $resultado; ?></td>
                        <?php } ?>
                        <td align="center"><?php echo "R$ " . number_format($total, "2", ",", "."); ?></td>    


                    </tr>
                    <?php $total = 0; ?>
                <?php } ?>
            </table>    
            <div class="clear"></div>
        </div>
    </body>
</html>