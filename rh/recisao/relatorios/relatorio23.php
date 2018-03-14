<?php
if (empty($_COOKIE['logado'])) {
print "Efetue o Login<br><a href='../login.php'>Logar</a>";
exit;
} else {

include "../conn.php";

$id_user = $_COOKIE['logado'];
$result_user = mysql_query("SELECT * FROM funcionario WHERE id_funcionario = '$id_user'");
$row_user = mysql_fetch_array($result_user);
$result_master = mysql_query("SELECT * FROM master WHERE id_master = '{$row_user['id_master']}'");
$row_master = mysql_fetch_array($result_master);

$projeto = $_REQUEST['pro'];

$result_projeto = mysql_query("SELECT * FROM projeto WHERE id_projeto = '$projeto'");
$row_projeto = mysql_fetch_array($result_projeto);
?>
<html>
    <head>
        <meta http-equiv=Content-Type content="text/html; charset=iso-8859-1">
        <title>Relat&oacute;rio de Conferência em Ordem Alfabética</title>
        <link href="css/estrutura.css" rel="stylesheet" type="text/css">

        <style>
            table tr.linha_um:hover {

                background-color: #E1F0FF;

            }
            table tr.linha_dois:hover {

                background-color: #E1F0FF;

            }


            table tr#duplicado {
                background-color:#FF8080;


            }

            table tr#duplicado:hover {

                background-color: #F66;


            }

        </style>

    </head>
    <body style="background-color:#FFF; margin-top:30px; margin-bottom:30px;">
        <table cellspacing="0" cellpadding="0"  style="width:auto; border:0px; margin-left:30px;">



            <tr>
                <td width="80%" align="left" colspan="2">


                    <table width="500" border="0" align="left" cellpadding="4" cellspacing="1" style="font-size:12px;margin-left:30px;">

                        <tr> 
                            <td width="20%" align="left" colspan="3">
                                <img src='../imagens/logomaster<?= $row_user['id_master'] ?>.gif' alt="" width='120' height='86' />
                            </td>
                        </tr>
                        <tr>
                            <td width="20%" align="left" colspan="3">
                                <strong>RELAT&Oacute;RIO DE PARTICIPANTES DO PROJETO EM ORDEM ALFAB&Eacute;TICA</strong><br>
                                <?= $row_master['razao'] ?>
                            </td>

                        </tr>


                        <tr style="color:#FFF;">
                            <td width="150" height="22" class="top">PROJETO</td>
                            <td width="150" class="top">REGIÃO</td>
                            <td width="200" class="top">TOTAL DE PARTICIPANTES</td>
                        </tr>
                        <tr style="color:#333; background-color:#efefef;">
                            <td height="20" align="center"><b><?= $row_projeto['nome'] ?></b></td>
                            <td align="center"><b><?= $row_projeto['regiao'] ?></b></td>
                            <td align="center"><b><?php echo $num_clt ?></b></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr> 
                <td colspan="3">

                    <div class="descricao" style="text-align:left;font-weight:bold;">Relat&oacute;rio de CLTs do Projeto em Ordem Alfabética</div> 
                    <table class="relacao" width="100%" cellpadding="3" cellspacing="1"  style="margin-left:30px;margin-right:30px;">
                        <tr class="secao">
                            <td align="center">Nome</td> 
                            <td align="center">Unidade</td>  
                            <td align="center">Data de Entrada</td>
                            
                            <td align="center">PIS</td>
                            <td align="center">CTPS</td>
                            <td align="center">Série (CTPS)</td>
                            <td align="center">UF (CTPS)</td>          
                            
                            <td align="center">CPF</td>
                            <td align="center">Banco</td>
                            <td align="center">Agência</td>
                            <td align="center">C.C.</td>
                            <td align="center">Tipo de Conta</td>
                        </tr>

                        <?php
                        ////EXIBE OS REGITROS DUPLICADOS SE EXISTIREM (CLT)

                        $clt = mysql_query("SELECT A.id_clt,
                                    A.nome as nome, 
                                    A.locacao, 
                                    date_format(A.data_entrada, '%d/%m/%Y') as data,
                                    A.pis,
                                    A.campo1,
                                    A.serie_ctps,
                                    A.uf_ctps,
                                    A.cpf,	      
                                    A.agencia,
                                    A.conta,	      
                                    A.tipo_conta,
                                    A.status,
                                    B.nome as banco
                                    FROM rh_clt AS A
                                    LEFT JOIN bancos AS B ON (A.banco=B.id_banco)
                                    WHERE  A.status < 60 AND A.id_projeto ='$projeto'

                                    ORDER BY A.locacao,A.nome");

                        $num_clt = mysql_num_rows($clt);

                        if ($num_clt != 0)
                        while ($row_clt = mysql_fetch_assoc($clt)){
                        ?>
                        <tr>

                            
                            <td><?= $row_clt['nome'] ?></td>
                            <td><?= $row_clt['locacao'] ?></td>
                            <td align="center"><?= $row_clt['data'] ?></td>
                            
                            <td align="center"><?= $row_clt['pis'] ?></td>
                            <td align="center"><?= $row_clt['campo1'] ?></td>
                            <td align="center"><?= $row_clt['serie_ctps'] ?></td>
                            <td align="center"><?= $row_clt['uf_ctps'] ?></td>
                            
                            <td align="center"><?= $row_clt['cpf'] ?></td>      
                            <td align="center"><?= $row_clt['banco'] ?></td>
                            <td align="center"><?= $row_clt['agencia'] ?></td>
                            <td align="center"><?= $row_clt['conta'] ?></td>
                            <td align="center"><?= $row_clt['tipo_conta'] ?></td>
                        </tr>

                        <?php
                        }
                        ?>

                        <tr class="secao">
                            <td colspan="56" align="left">TOTAL DE CLTS: <?php echo $num_clt; ?></td>
                        </tr>
                    </table>

                    

                </td>
            </tr>
        </table>
    </body>
</html>
<?php } ?>