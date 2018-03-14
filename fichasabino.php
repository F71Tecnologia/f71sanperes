<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='login.php'>Logar</a>";
    exit;
} else {

    include "conn.php";

    $id_user = $_COOKIE['logado'];
    $result_user = mysql_query("SELECT * FROM funcionario where id_funcionario = '$id_user'");
    $row_user = mysql_fetch_array($result_user);

    $pro = $_REQUEST['pro'];
    $id_reg = $_REQUEST['id_reg'];
    $clt = $_REQUEST['clt'];

    $qr_regiao = mysql_query("SELECT * FROM regioes WHERE id_regiao = '$id_reg'");
    $row_regiao = mysql_fetch_assoc($qr_regiao);

    $qr_master = mysql_query("SELECT * FROM master WHERE id_master = '$row_regiao[id_master]'");
    $row_master = mysql_fetch_assoc($qr_master);

    $result_bol = mysql_query("SELECT *,date_format(data_entrada, '%d/%m/%Y')as data_entrada ,date_format(data_nasci, '%d/%m/%Y')as data_nasci ,date_format(data_cad, '%d/%m/%Y')as data_cad,date_format(data_ctps, '%d/%m/%Y')as data_ctps,date_format(data_rg, '%d/%m/%Y')as data_rg,date_format(dada_pis, '%d/%m/%Y')as data_pis,date_format(data_saida, '%d/%m/%Y')as data_saida,date_format(data_escola, '%d/%m/%Y')as data_escola FROM rh_clt where id_clt = '$clt'");
    $row = mysql_fetch_array($result_bol);
    
    ?>
    <html>
        <head>
            <meta http-equiv=Content-Type content="text/html; charset=iso-8859-1">
            <title>Documento de Cdastramento do NIS</title>
            <link href="relatorios/css/estrutura.css" rel="stylesheet" type="text/css">
            <style>
                #content{width: 800px; margin: 0 auto;}
                .logo{float: left; margin: 5px 0 0 5px;}
                #title{float: left; padding: 20px 0 0 25px;}
                
                hr{border: none; border-top: 1px solid #333;}
                
                .fleft{float: left;}
                .fright{float: right;}
                
                .box{border: 2px solid #000; padding: 10px; margin-top: 10px;}
                .txright{text-align: right;}
                .txcenter{text-align: center;}
                .legenda{font-size: 10px; padding: 0; margin: 0; float: left;}
                .clear{clear: both; padding: 0; margin: 0; line-height: 16px;}
                .txleft{text-align: left;}
                
                table{width: 100%; border-left: 1px solid #333; margin-bottom: 10px!important;}
                td{padding: 1px 5px; border-right: 1px solid #333; border-bottom: 1px solid #333;}
                
                td.bl{border-left: none !important;}
                tr.bf td{border-bottom: none !important;}
                tr.bt td{border-top: none !important;}
                p{font-size: 13px; padding: 5px;}
                
                table thead tr th{font-size: 14px; font-weight: bold;}
                table tbody tr td{padding: 1px 5px; font-size: 13px!important;}
                
                table.grid{border-top: 1px solid #333; border-left: 1px solid #333;}
                table.grid tr td{border-bottom: 1px solid #333; border-right: 1px solid #333;}
                table.grid tr th{border-bottom: 1px solid #333; background: #F0F0F0;}
                table.grid tr th:last-child{border-right: 1px solid #333;}
                
                
                #sigilo{width: 200px!important; float: right;}
            </style>
        </head>
        <body style="background-color:#FFF;">

            <div id="content">
                <div id="empresa" class="box txleft">
                    
                    <img class="logo" src="imagens/bancos/ficha_caixa.jpg" />
                    <p id="title"><strong>DCN - Documento de Cdastramento do NIS</strong></p>
                    
                    <table id="sigilo" border="0" cellspacing="0" cellpadding="0" class="clear">
                        <tr>
                            <td>
                                <p class="legenda">Grau de Sigilo:</p>
                                <p class="clear">#000 </p>
                            </td>
                        </tr>
                    </table>
                    
                    <table border="0" cellspacing="0" cellpadding="0" class="clear">
                        <tr>
                            <td>
                                <p class="legenda">[x] CNPJ  [ ] CEI:</p>
                                <p class="clear"> <?php echo $row_master['cnpj']; ?></p>
                            </td>
                            <td>
                                <p class="legenda">Nome do empregador:</p>
                                <p class="clear"><?php echo $row_master['razao']; ?></p>
                            </td>
                            <td>
                                <p class="legenda">Data de v�nculo:</p>
                                <p class="clear"><?php echo $row['data_entrada']; ?></p>
                            </td>
                        </tr>
                    </table>
                    
                    <table border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td>
                                <p class="legenda">Nome:</p>
                                <p class="clear"><?php echo $row['nome']; ?></p>
                            </td>
                        </tr>
                    </table>
                    
                    <table border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td>
                                <p class="legenda">Nome - continua��o:</p>
                                <p class="clear">-</p>
                            </td>
                            <td>
                                <p class="legenda">Data de nascimento:</p>
                                <p class="clear"><?php echo $row['data_nasci'] ?></p>
                            </td>
                            <td>
                                <p class="legenda">Sexo:</p>
                                <p class="clear"><?php echo $row['sexo'] ?></p>
                            </td>
                        </tr>
                    </table>
                    
                    <table border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td>
                                <p class="legenda">Nome do Pai:</p>
                                <p class="clear"><?php echo $row['pai']; ?></p>
                            </td>
                        </tr>
                    </table>
                    
                    <table border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td>
                                <p class="legenda">Nome da M�e:</p>
                                <p class="clear"><?php echo $row['mae']; ?></p>
                            </td>
                        </tr>
                    </table>
                    
                    <table border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td>
                                <p class="legenda">Nacionalidade:</p>
                                <p class="clear"><?php echo $row['nacionalidade']; ?></p>
                            </td>
                            <td>
                                <p class="legenda">Pa�s de origem:</p>
                                <p class="clear"><?php echo $row['pais']; ?></p>
                            </td>
                            <td>
                                <p class="legenda">UF e Munic�pio de Nascimento:</p>
                                <p class="clear"><?php echo $row['naturalidade']; ?></p>
                            </td>
                        </tr>
                    </table>
                    
                    <table border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td>
                                <p class="legenda">Cor:</p>
                                <p class="clear">
                                    <?php
                                    $qr_et = mysql_query("SELECT * FROM etnias WHERE id = '{$row['etnia']}' AND status = 'on'");
                                    $etnia = mysql_fetch_assoc($qr_et);
                                    echo $etnia['nome'];
                                    ?></p>
                            </td>
                            <td>
                                <p class="legenda">Estado civil:</p>
                                <p class="clear"><?php echo $row['civil']; ?></p>
                            </td>
                            <td>
                                <p class="legenda">N�vel de instru��o:</p>
                                <p class="clear"><?php
                                    $qr_escolaridade = mysql_query("SELECT * FROM escolaridade WHERE id = '{$row['escolaridade']}' AND status = 'on'");
                                    $escolaridade = mysql_fetch_assoc($qr_escolaridade);
                                    echo $escolaridade['nome'];
                                    ?></p>
                            </td>
                            <td>
                                <p class="legenda">Data de chegada ao Brasil:</p>
                                <p class="clear"> - </p>
                            </td>
                        </tr>
                    </table>
                    
                    <table border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td>
                                <p class="legenda">CPF:</p>
                                <p class="clear"><?php echo $row['cpf']; ?></p>
                            </td>
                            <td>
                                <p class="legenda">Identidade:</p>
                                <p class="clear"><?php echo $row['rg']; ?></p>
                            </td>
                            <td>
                                <p class="legenda">Complemento:</p>
                                <p class="clear"> - </p>
                            </td>
                            <td>
                                <p class="legenda">UF:</p>
                                <p class="clear"> <?php echo $row['uf_rg'] ?> </p>
                            </td>
                            <td>
                                <p class="legenda">Emissor:</p>
                                <p class="clear"> <?php echo $row['orgao'] ?></p>
                            </td>
                            <td>
                                <p class="legenda">Data Emiss�o:</p>
                                <p class="clear"> <?php echo $row['data_rg'] ?></p>
                            </td>
                        </tr>
                    </table>
                    
                    <table border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td>
                                <p class="legenda">CTPS:</p>
                                <p class="clear"><?php echo $row['campo1']; ?></p>
                            </td>
                            <td>
                                <p class="legenda">S�rie:</p>
                                <p class="clear"><?php echo $row['serie_ctps']; ?></p>
                            </td>
                            <td>
                                <p class="legenda">UF:</p>
                                <p class="clear"><?php echo $row['uf_ctps']; ?></p>
                            </td>
                            <td>
                                <p class="legenda">Data Emiss�o:</p>
                                <p class="clear"> <?php echo $row['data_ctps'] ?></p>
                            </td>
                        </tr>
                    </table>
                    
                    <table border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td>
                                <p class="legenda">Certid�o civil:</p>
                                <p class="clear"><?php echo "-"; ?></p>
                            </td>
                            <td>
                                <p class="legenda">Data de emiss�o:</p>
                                <p class="clear"><?php echo "-"; ?></p>
                            </td>
                            <td>
                                <p class="legenda">Termo/Matr�cula:</p>
                                <p class="clear"><?php echo "-"; ?></p>
                            </td>
                        </tr>
                    </table>
                    
                    <p class="legenda">Certid�o civil - continua��o</p>
                    <table border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td>
                                <p class="legenda">Livro:</p>
                                <p class="clear"><?php echo "-"; ?></p>
                            </td>
                            <td>
                                <p class="legenda">Folha:</p>
                                <p class="clear"><?php echo "-"; ?></p>
                            </td>
                            <td>
                                <p class="legenda">Cart�rio:</p>
                                <p class="clear"><?php echo "-"; ?></p>
                            </td>
                            <td>
                                <p class="legenda">UF:</p>
                                <p class="clear"><?php echo "-"; ?></p>
                            </td>
                            <td>
                                <p class="legenda">Munic�pio:</p>
                                <p class="clear"><?php echo "-"; ?></p>
                            </td>
                        </tr>
                    </table>
                    
                    <table border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td>
                                <p class="legenda">Passaporte N�mero:</p>
                                <p class="clear"><?php echo "-"; ?></p>
                            </td>
                            <td>
                                <p class="legenda">Emissor:</p>
                                <p class="clear"><?php echo "-"; ?></p>
                            </td>
                            <td>
                                <p class="legenda">UF:</p>
                                <p class="clear"><?php echo "-"; ?></p>
                            </td>
                            <td>
                                <p class="legenda">Data de emiss�o:</p>
                                <p class="clear"><?php echo "-"; ?></p>
                            </td>
                            <td>
                                <p class="legenda">Data de validade:</p>
                                <p class="clear"><?php echo "-"; ?></p>
                            </td>
                            <td>
                                <p class="legenda">Pa�s de emiss�o:</p>
                                <p class="clear"><?php echo "-"; ?></p>
                            </td>
                        </tr>
                    </table>
                    
                    <table border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td>
                                <p class="legenda">T�tulo de Eleitor:</p>
                                <p class="clear"><?php echo $row['titulo']; ?></p>
                            </td>
                            <td>
                                <p class="legenda">Zona:</p>
                                <p class="clear"><?php echo $row['zona']; ?></p>
                            </td>
                            <td>
                                <p class="legenda">Se��o:</p>
                                <p class="clear"><?php echo $row['secao']; ?></p>
                            </td>
                            <td>
                                <p class="legenda">Portaria de Naturaliza��o:</p>
                                <p class="clear"><?php echo "-"; ?></p>
                            </td>
                            <td>
                                <p class="legenda">Data de naturaliza��o:</p>
                                <p class="clear"><?php echo "-"; ?></p>
                            </td>
                        </tr>
                    </table>
                    
                    <table border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td>
                                <p class="legenda">RIC N�mero:</p>
                                <p class="clear"><?php echo "-"; ?></p>
                            </td>
                            <td>
                                <p class="legenda">UF:</p>
                                <p class="clear"><?php echo "-"; ?></p>
                            </td>
                            <td>
                                <p class="legenda">Emissor:</p>
                                <p class="clear"><?php echo "-"; ?></p>
                            </td>
                            <td>
                                <p class="legenda">Munic�pio:</p>
                                <p class="clear"><?php echo "-"; ?></p>
                            </td>
                            <td>
                                <p class="legenda">Data de expedi��o:</p>
                                <p class="clear"><?php echo "-"; ?></p>
                            </td>
                        </tr>
                    </table>
                    
                    <table border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td>
                                <p class="legenda">Tipo de Endere�o:</p>
                                <p class="clear"><?php echo "-"; ?></p>
                            </td>
                            <td>
                                <p class="legenda">CEP:</p>
                                <p class="clear"><?php echo $row['cep']; ?></p>
                            </td>
                            <td>
                                <p class="legenda">UF:</p>
                                <p class="clear"><?php echo $row['uf']; ?></p>
                            </td>
                            <td>
                                <p class="legenda">Munic�pio:</p>
                                <p class="clear"><?php echo $row['cidade']; ?></p>
                            </td>
                        </tr>
                    </table>
                    
                    <table border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td>
                                <p class="legenda">Bairro:</p>
                                <p class="clear"><?php echo $row['bairro']; ?></p>
                            </td>
                            <td>
                                <p class="legenda">Logradouro:</p>
                                <p class="clear"><?php echo $row['endereco']; ?></p>
                            </td>
                            <td>
                                <p class="legenda">N�mero:</p>
                                <p class="clear"><?php echo $row['numero']; ?></p>
                            </td>
                            <td>
                                <p class="legenda">Complemento:</p>
                                <p class="clear"><?php echo $row['complemento']; ?></p>
                            </td>
                        </tr>
                    </table>
                    
                    <table border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td>
                                <p class="legenda">Caixa Postal:</p>
                                <p class="clear"><?php echo "-"; ?></p>
                            </td>
                            <td>
                                <p class="legenda">Caixa Postal CEP:</p>
                                <p class="clear"><?php echo "-"; ?></p>
                            </td>
                            <td>
                                <p class="legenda">Telefone Fixo:</p>
                                <p class="clear"><?php echo $row['tel_fixo']; ?></p>
                            </td>
                            <td>
                                <p class="legenda">Telefone Celular:</p>
                                <p class="clear"><?php echo $row['tel_cel']; ?></p>
                            </td>
                        </tr>
                    </table>
                    
                    <table border="0" cellspacing="0" cellpadding="0">
                        <tr>
                            <td>
                                <p class="legenda">E-mail:</p>
                                <p class="clear"><?php echo ($row['email']=="") ? "-" : $row['email']; ?></p>
                            </td>
                        </tr>
                    </table>
                    <br/>
                    <p>____________________________________________, _________ de _________________________ de _____________</p>
                    <p class="legenda">Local/Data</p>
                    <br/>
                    <br/>
                    <br/>
                    <p class="fleft">________________________________________________</p>
                    <p class="fright">_________________________________________________</p>
                    
                    <p class="fleft clear">Assinatura do Solicitante</p>
                    <p class="fright">Assinatura do empregado CAIXA - Sob carimbo</p>
                    
                    
                    <br class="clear"/>
                </div>
                
                
                <br class="clear"/>
                <br class="quebra-aqui"/>
                
            </div>
        </body>
    </html>
<?php } ?>