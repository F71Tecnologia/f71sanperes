<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='login.php'>Logar</a>";
    exit;
} else {
    include "conn.php";
    
}
?>
<html>
    <head>
        <meta http-equiv=Content-Type content="text/html; charset=iso-8859-1">
        <title>Registro de Empregado</title>
        <link href="relatorios/css/estrutura.css" rel="stylesheet" type="text/css">
        <style>
            #content{width: 800px; margin: 0 auto;}
            #empresa table{width: 80%; margin-left: 5px; float: right;}
            #empresaAut{float: left; width: 120px; height: 135px; border: 1px solid #000;}
            #pessoal #foto{float: left; height: 140px; padding: 5px 4px 0 0; width: 120px;}

            #estrangeiro {float: left; width: 450px;}
            #recisao {float: right; width: 300px;}
            #polegar {float: left; width: 80px; height: 54px; margin-right: 5px;}
            #assinatura {float: left; width: 341px; height: 54px;}

            hr{border: none; border-top: 1px solid #333;}

            .box{border: 2px solid #000; padding: 10px; margin-top: 10px;}
            .txright{text-align: right;}
            .txcenter{text-align: center;}
            .legenda{font-size: 10px; padding: 0; margin: 0; float: left;}
            .clear{clear: both; padding: 0; margin: 0; line-height: 16px;}
            .txleft{text-align: left;}
            td{padding: 1px 5px;}
            td.b{border: 1px solid #333;}
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
        </style>
    </head>
    <body style="background-color:#FFF;">

        <div id="content">
            <div id="empresa" class="box">
                <div id="empresaAut"></div>
                <table border="0" cellspacing="0" cellpadding="0">
                    <tr>
                        <td colspan="2" class="txcenter"><p><strong>REGISTRO DE EMPREGADO</strong></p></td>
                        <td>
                            <p class="legenda">N° Legal:</p>
                            <p class="clear"> </p>
                        </td>
                        <td>
                            <p class="legenda">Data Emissão:</p>
                            <p class="clear"><?php echo date("d/m/Y H:i:s") ?></p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" class="b">
                            <p class="legenda">Empregador:</p>
                            <p class="clear"><?php echo $row_master['razao']; ?></p>
                        </td>
                        <td colspan="2" class="b bl">
                            <p class="legenda">CGC:</p>
                            <p class="clear"><?php echo $row_master['cnpj']; ?></p>
                        </td>
                    </tr>
                    <tr class="bt">
                        <td colspan="4" class="b">
                            <p class="legenda">Endereço:</p>
                            <p class="clear"><?php echo $row_master['logradouro']; ?></p>
                        </td>
                    </tr>
                    <tr class="bt">
                        <td colspan="2" class="b">
                            <p class="legenda">Bairro:</p>
                            <p class="clear"><?php echo $row_master['bairro']; ?></p>
                        </td>
                        <td class="b bl">
                            <p class="legenda">Cidade:</p>
                            <p class="clear"><?php echo $row_master['municipio']; ?></p>
                        </td>
                        <td class="b bl">
                            <p class="legenda">UF:</p>
                            <p class="clear"><?php echo $row_master['uf']; ?></p>
                        </td>
                    </tr>
                </table>
                <br class="clear"/>
            </div>

            <div id="pessoal" class="box">

                <table border="0" cellspacing="0" cellpadding="0" width="100%">
                    <tr>
                        <td colspan="5" class="b">
                            <p class="legenda">Empregado:</p>
                            <p class="clear"> <?php echo $row['nome'] ?></p>
                        </td>
                    </tr>
                    <tr class="bt">
                        <td colspan="3" class="b">
                            <p class="legenda">Residência:</p>
                            <p class="clear"> <?php echo $row['endereco']. ", ".$row['numero'] ?></p>
                        </td>
                        <td colspan="2" class="b bl">
                            <p class="legenda">Complemento:</p>
                            <p class="clear"> <?php echo $row['complemento'] ?></p>
                        </td>
                    </tr>
                    <tr class="bt">
                        <td class="b">
                            <p class="legenda">Bairro:</p>
                            <p class="clear"> <?php echo $row['bairro'] ?></p>
                        </td>
                        <td class="b bl">
                            <p class="legenda">Cidade:</p>
                            <p class="clear"> <?php echo $row['cidade'] ?></p>
                        </td>
                        <td class="b bl">
                            <p class="legenda">UF:</p>
                            <p class="clear"> <?php echo $row['uf'] ?></p>
                        </td>
                        <td class="b bl">
                            <p class="legenda">Nacionalidade:</p>
                            <p class="clear"> <?php echo $row['nacionalidade'] ?></p>
                        </td>
                        <td class="b bl">
                            <p class="legenda">País:</p>
                            <p class="clear"> </p>
                        </td>
                    </tr>
                </table>

                <div id="foto">
                    <?php
                    if ($row['foto'] == "1") {
                        $nome_imagem = $id_reg . "_" . $pro . "_" . $row['0'] . ".gif";
                    } else {
                        $nome_imagem = 'semimagem.gif';
                    }
                    print "<img src='fotosclt/$nome_imagem' width='100' height='130' border=1 align='absmiddle'>";
                    ?>
                </div>

                <table border="0" cellspacing="0" cellpadding="0" width="652px">
                    <tr class="bt">
                        <td class="b">
                            <p class="legenda">Sexo:</p>
                            <p class="clear"> <?php echo $row['sexo'] ?></p>
                        </td>
                        <td class="b bl">
                            <p class="legenda">Data Nasc.:</p>
                            <p class="clear"> <?php echo $row['data_nasci'] ?></p>
                        </td>
                        <td colspan="4" class="b bl">
                            <p class="legenda">Naturalidade:</p>
                            <p class="clear"> <?php echo $row['naturalidade'] ?></p>
                        </td>
                        <td class="b bl">
                            <p class="legenda">Estado Civil:</p>
                            <p class="clear"> <?php echo $row['civil'] ?></p>
                        </td>
                    </tr>
                    <tr class="bt">
                        <td colspan="2" class="b">
                            <p class="legenda">CIC/CPF:</p>
                            <p class="clear"> <?php echo $row['cpf'] ?></p>
                        </td>
                        <td colspan="2" class="b bl">
                            <p class="legenda">Cédula Identidade RG:</p>
                            <p class="clear"> <?php echo $row['rg'] ?></p>
                        </td>
                        <td class="b bl">
                            <p class="legenda">UF:</p>
                            <p class="clear"> <?php echo $row['uf_rg'] ?></p>
                        </td>
                        <td class="b bl">
                            <p class="legenda">Data de Emissão:</p>
                            <p class="clear"> <?php echo $row['data_rg'] ?></p>
                        </td>
                        <td class="b bl">
                            <p class="legenda">Orgão Emissor:</p>
                            <p class="clear"> <?php echo $row['orgao'] ?></p>
                        </td>
                    </tr>
                    <tr class="bt">
                        <td colspan="5" class="b">
                            <p class="legenda">Pai:</p>
                            <p class="clear"> <?php echo $row['pai'] ?></p>
                        </td>
                        <td colspan="2" class="b bl">
                            <p class="legenda">Nacionalidade:</p>
                            <p class="clear"> <?php echo $row['nacionalidade_pai'] ?></p>
                        </td>
                    </tr>
                    <tr class="bt">
                        <td colspan="5" class="b">
                            <p class="legenda">Mãe:</p>
                            <p class="clear"> <?php echo $row['mae'] ?></p>
                        </td>
                        <td colspan="2" class="b bl">
                            <p class="legenda">Nacionalidade:</p>
                            <p class="clear"> <?php echo $row['nacionalidade_mae'] ?></p>
                        </td>
                    </tr>
                    <tr class="bt bf">
                        <td colspan="2" class="b">
                            <p class="legenda">CTPS n°:</p>
                            <p class="clear"> <?php echo $row['campo1'] ?></p>
                        </td>
                        <td class="b bl bf">
                            <p class="legenda">Série:</p>
                            <p class="clear"> <?php echo $row['serie_ctps'] ?></p>
                        </td>
                        <td class="b bl bf">
                            <p class="legenda">Letra:</p>
                            <p class="clear"> - </p>
                        </td>
                        <td class="b bl">
                            <p class="legenda">UF Emissor:</p>
                            <p class="clear"> <?php echo $row['uf_ctps'] ?></p>
                        </td>
                        <td class="b bl">
                            <p class="legenda">Data Expedição:</p>
                            <p class="clear"> <?php echo $row['data_ctps'] ?></p>
                        </td>
                        <td class="b bl">
                            <p class="legenda">Data Validade:</p>
                            <p class="clear"> - </p>
                        </td>
                    </tr>
                </table>

                <table border="0" cellspacing="0" cellpadding="0" width="100%" class="clear">
                    <tr>
                        <td colspan="3" class="b">
                            <p class="legenda">Titulo de Eleitor N°:</p>
                            <p class="clear"><?php echo empty($row['titulo']) ? " - " : $row['titulo'] ?></p>
                        </td>
                        <td class="b bl">
                            <p class="legenda">Zona:</p>
                            <p class="clear"><?php echo empty($row['zona']) ? " - " : $row['zona'] ?></p>
                        </td>
                        <td class="b bl">
                            <p class="legenda">Seção:</p>
                            <p class="clear"><?php echo empty($row['secao']) ? " - " : $row['secao'] ?></p>
                        </td>
                        <td class="b bl">
                            <p class="legenda">Carat Habilitação:</p>
                            <p class="clear"> - </p>
                        </td>
                        <td class="b bl">
                            <p class="legenda">Categoria:</p>
                            <p class="clear"> - </p>
                        </td>
                    </tr>

                    <tr class="bt">
                        <td colspan="2" class="b">
                            <p class="legenda">Doc. Militar:</p>
                            <p class="clear"> <?php echo empty($row['reservista']) ? " - " : $row['reservista']?></p>
                        </td>
                        <td class="b bl">
                            <p class="legenda">Espécie:</p>
                            <p class="clear"> - </p>
                        </td>
                        <td colspan="3" class="b bl">
                            <p class="legenda">Escolaridade:</p>
                            <p class="clear"><?php
                                $qr_escolaridade = mysql_query("SELECT * FROM escolaridade WHERE id = '{$row['escolaridade']}' AND status = 'on'");
                                $escolaridade = mysql_fetch_assoc($qr_escolaridade);
                                echo $escolaridade['nome'];
                                ?></p>
                        </td>
                        <td class="b bl">
                            <p class="legenda">Data Admissão:</p>
                            <p class="clear"> <?php echo $row['data_entrada'] ?></p>
                        </td>
                    </tr>

                    <tr class="bt">
                        <td colspan="3" class="b">
                            <p class="legenda">Função:</p>
                            <p class="clear"> <?php echo $row_curso['nome'] ?></p>
                        </td>
                        <td  colspan="3" class="b bl">
                            <p class="legenda">Seção:</p>
                            <p class="clear"> <?php
                                $qr_projeto = mysql_query("SELECT nome FROM projeto WHERE id_projeto = '$_GET[pro]'");
                                $projeto = mysql_fetch_assoc($qr_projeto);
                                echo $projeto['nome'];?></p>
                        </td>
                        <td class="b bl">
                            <p class="legenda">Salário:</p>
                            <p class="clear"> <?php echo number_format($row_curso['salario'], 2, ',', '.'); ?></p>
                        </td>
                    </tr>

                    <tr class="bt">
                        <td colspan="2" class="b">
                            <p class="legenda">Regime:</p>
                            <p class="clear"> Mensalista </p>
                        </td>
                        <td colspan="2" class="b bl">
                            <p class="legenda">Horário Trabalho:</p>
                            <p class="clear"> <?php echo $row_horario['entrada_1br']." até ".$row_horario['saida_2br']; ?> </p>
                        </td>
                        <td class="b bl">
                            <p class="legenda">Intervalo de:</p>
                            <p class="clear"> <?php echo $row_horario['saida_1br']." até ".$row_horario['entrada_2br']; ?> </p>
                        </td>
                        <td class="b bl">
                            <p class="legenda">Horas Semanais:</p>
                            <p class="clear"> <?php echo $row_horario['horas_mes']/4; ?> </p>
                        </td>
                        <td class="b bl">
                            <p class="legenda">Horas Padrão:</p>
                            <p class="clear"> <?php echo $row_horario['horas_mes']; ?> </p>
                        </td>
                    </tr>

                    <tr class="bt">
                        <td colspan="2" class="b">
                            <p class="legenda">Sindicato Categoria:</p>
                            <p class="clear"> - </p>
                        </td>
                        <td colspan="3" class="b bl">
                            <p class="legenda">Sindicato Predominante:</p>
                            <p class="clear"> <?php 
                            $qr_sindi = mysql_query("SELECT * FROM rhsindicato WHERE id_sindicato = '{$row['rh_sindicato']}'");
                            $row_sindi = mysql_fetch_assoc($qr_sindi);
                            echo empty($row_sindi['entidade']) ? "-" : $row_sindi['entidade'];
                            ?> </p>
                        </td>
                        <td colspan="2" class="b bl">
                            <p class="legenda">Sindicalizado:</p>
                            <p class="clear"> - </p>
                        </td>
                    </tr>

                    <tr class="bt">
                        <td class="b" width="100">
                            <p class="clear">FGTS</p>
                            <p class="clear"><?php echo empty($row['fgts']) ? "-" : $row['fgts']; ?></p>
                        </td>
                        <td colspan="2" class="b bl">
                            <p class="legenda">Opção em:</p>
                            <p class="clear"> - </p>
                        </td>
                        <td colspan="2" class="b bl">
                            <p class="legenda">Conta Vinculada no banco:</p>
                            <p class="clear"> - </p>
                        </td>
                        <td colspan="2" class="b bl">
                            <p class="legenda">Data da Retratação:</p>
                            <p class="clear"> - </p>
                        </td>
                    </tr>

                    <tr class="bt">
                        <td class="b" width="80">
                            <p class="clear">CRÉDITO</p>
                        </td>
                        <td colspan="2" class="b bl">
                            <p class="legenda">Banco:</p>
                            <p class="clear"> <?php echo $row_banco['nome']?></p>
                        </td>
                        <td colspan="2" class="b bl">
                            <p class="legenda">Agência:</p>
                            <p class="clear"> <?php echo $row['agencia']?></p>
                        </td>
                        <td colspan="2" class="b bl">
                            <p class="legenda">Conta:</p>
                            <p class="clear"> <?php echo $row['conta']?></p>
                        </td>
                    </tr>

                </table>
            </div>

            <div id="pispasep" class="box">
                <p class="center"><strong>PIS/PASEP</strong></p>
                <table border="0" cellspacing="0" cellpadding="0" width="100%" class="clear">
                    <tr>
                        <td class="b">
                            <p class="legenda">Cadastrado em:</p>
                            <p class="clear"><?php echo $row['data_pis'] ?></p>
                        </td>
                        <td class="b bl">
                            <p class="legenda">Sob o:</p>
                            <p class="clear"><?php echo $row['pis'] ?></p>
                        </td>
                        <td class="b bl">
                            <p class="legenda">Data Expedição:</p>
                            <p class="clear"><?php echo $row['data_pis'] ?></p>
                        </td>
                        <td class="b bl">
                            <p class="legenda">Domicílio Bancário:</p>
                            <p class="clear"> - </p>
                        </td>
                    </tr>

                    <tr class="bt">
                        <td class="b">
                            <p class="legenda">N° Banco:</p>
                            <p class="clear"> - </p>
                        </td>
                        <td class="b bl">
                            <p class="legenda">Agência código:</p>
                            <p class="clear"> - </p>
                        </td>
                        <td colspan="2" class="b bl">
                            <p class="legenda">Endereço da Agencia:</p>
                            <p class="clear"> - </p>
                        </td>
                    </tr>

                </table>
            </div>

            <div id="estrangeiro" class="box">
                <p class="center"><strong>Quadro Estrangeiro</strong></p>
                <table border="0" cellspacing="0" cellpadding="0" width="100%" class="clear">
                    <tr>
                        <td colspan="2" class="b">
                            <p class="legenda">Registro Nacional de Estra.RNE:</p>
                            <p class="clear"> - </p>
                        </td>
                        <td colspan="2" class="b bl">
                            <p class="legenda">Casado (brasileira)</p>
                            <p class="clear"> - </p>
                        </td>
                    </tr>

                    <tr class="bt">
                        <td colspan="4" class="b">
                            <p class="legenda">Nome do cônjuge:</p>
                            <p class="clear"> - </p>
                        </td>
                    </tr>

                    <tr class="bt">
                        <td class="b">
                            <p class="legenda">Filhos Brasil:</p>
                            <p class="clear"> - </p>
                        </td>
                        <td class="b bl">
                            <p class="legenda">Data Chegada:</p>
                            <p class="clear"> - </p>
                        </td>
                        <td class="b bl">
                            <p class="legenda">Naturalizado:</p>
                            <p class="clear"> - </p>
                        </td>
                        <td class="b bl">
                            <p class="legenda">Decreto n.:</p>
                            <p class="clear"> - </p>
                        </td>
                    </tr>

                    <tr class="bt">
                        <td colspan="2" class="b">
                            <p class="legenda">Visto Permanente:</p>
                            <p class="clear"> - </p>
                        </td>
                        <td class="b bl">
                            <p class="legenda">Temporário:</p>
                            <p class="clear"> - </p>
                        </td>
                        <td class="b bl">
                            <p class="legenda">Com vencto. em:</p>
                            <p class="clear"> - </p>
                        </td>
                    </tr>
                </table>
            </div>

            <div id="recisao" class="box txleft">
                <p class="center"><strong>Rescisão do contrato de trabalho</strong></p>
                <hr/>
                <p>Data saída: <strong><?php echo $row_demi['data_saida'] ?></strong> homologação n°: <strong><?php echo $row_demi['homologacao'] ?></strong></p>
                <p>
                    Tipo de Desligamento: <br/>
                    <strong><?php echo $row_demi['desligamento'] ?></strong>
                </p>
                <p>Orgão onde foi feito a homologação: <?php echo $row_demi['local_homolog'] ?> </p>
                <br>
                <br>
                <table border="0" cellspacing="0" cellpadding="0" width="100%" class="clear">
                    <tr>
                        <td class="b" width="80">
                            <p class="legenda">Data:</p>
                            <p class="clear"> <br/> <br/> <br/> <br/></p>
                        </td>
                        <td class="b bl">
                            <p class="legenda">Carimbo e assinatura do empregador</p>
                            <p class="clear"> <br/> <br/> <br/> <br/></p>
                        </td>
                    </tr>
                </table>
            </div>

            <div id="polegar" class="box">
                <p class="legenda">Polegar Direito</p>
            </div>

            <div id="assinatura" class="box">
                <p class="center"><strong>Assinatura</strong></p>
                <p class="center">______________________________________________</p>
            </div>


            <br class="clear"/>
            <br class="quebra-aqui"/>

            <div class="box">
                <p class="center"><strong>CONTRIBUÍÇÃO SINDICAL</strong></p>
                <table border="0" cellspacing="0" cellpadding="0" width="100%" class="grid">
                    <thead>
                        <tr>
                            <th>Guia n°</th>
                            <th>Referência</th>
                            <th>Desconto</th>
                            <th>Valor</th>
                            <th>Nome do Sindicato</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($rowCSind = mysql_fetch_assoc($qr_cont_sind)){?>
                        <tr>
                            <td>-</td>
                            <td><?php echo $rowCSind['mes']."/".$rowCSind['ano'] ?></td>
                            <td><?php echo $rowCSind['mes']."/".$rowCSind['ano'] ?></td>
                            <td><?php echo $rowCSind['a5019']?></td>
                            <td><?php echo $rowCSind['nome']?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div class="box">
                <p class="center"><strong>ALTERAÇÕES DE FUNÇÃO</strong></p>
                <table border="0" cellspacing="0" cellpadding="0" width="100%" class="grid">
                    <thead>
                        <tr>
                            <th>Em</th>
                            <th>CBO</th>
                            <th>Função</th>
                            <th>Motivo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysql_num_rows($qr_alt_funcao) > 0){
                            while($row_alt_func = mysql_fetch_assoc($qr_alt_funcao)){ ?>
                        <tr>
                            <td><?php echo $row_alt_func['data']?></td>
                            <td><?php echo "DE: ".$row_alt_func['cbo_de'].", PARA: ".$row_alt_func['cbo_para']?></td>
                            <td><?php echo "DE: ".$row_alt_func['de'].", PARA: ".$row_alt_func['para']?></td>
                            <td><?php echo (empty($row_alt_func['motivo'])) ? "-" : $row_alt_func['motivo'];?></td>
                        </tr>
                        <?php } }else{ ?>
                        <tr>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                        </tr>
                        <?php }?>
                    </tbody>
                </table>
            </div>

            <div class="box">
                <p class="center"><strong>ALTERAÇÕES DE SALÁRIOS</strong></p>
                <table border="0" cellspacing="0" cellpadding="0" width="100%" class="grid">
                    <thead>
                        <tr>
                            <th>Em</th>
                            <th>Valor</th>
                            <th>Motivo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="box">
                <p class="center"><strong>FÉRIAS</strong></p>
                <table border="0" cellspacing="0" cellpadding="0" width="100%" class="grid">
                    <thead>
                        <tr>
                            <th>FÉRIAS - PERÍODO AQUISITIVO</th>
                            <th>FÉRIAS - PERÍODO DE GOZO</th>
                            <th>1/3 DAS FÉRIAS EM ABONO PECUNIÁRIO</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row_ferias = mysql_fetch_array($qr_ferias)){ ?>
                        <tr>
                            <td><?php echo $row_ferias['data_aquisitivo_ini']." - ".$row_ferias['data_aquisitivo_fim'] ?></td>
                            <td><?php echo $row_ferias['data_ini']." - ".$row_ferias['data_fim'] ?></td>
                            <td><?php echo "Em ".$row_ferias['data_proc']." Dias ".$row_ferias['dias_abono_pecuniario']; ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div class="box">
                <p class="center"><strong>OCORRÊNCIAS DE AFASTAMENTOS</strong></p>
                <table border="0" cellspacing="0" cellpadding="0" width="100%" class="grid">
                    <thead>
                        <tr>
                            <th>Data afastamento</th>
                            <th>Data retorno</th>
                            <th>Motivo</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row_afasta = mysql_fetch_array($qr_afasta)){ ?>
                        <tr>
                            <td><?php echo $row_afasta['databr']?></td>
                            <td><?php echo $row_afasta['data_retornobr']?></td>
                            <td><?php echo empty($row_afasta['nome_status']) ? "-" : $row_afasta['nome_status'];?></td>
                        </tr>
                        <?php } ?> 
                   </tbody>
                </table>
            </div>

            <div class="box">
                <p class="center"><strong>DEPENDENTES</strong></p>
                <table border="0" cellspacing="0" cellpadding="0" width="100%" class="grid">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Sexo</th>
                            <th>Parentesco</th>
                            <th>Data Nascimento</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($row_depen['nome1']!=""){ ?>
                        <tr>
                            <td><?php echo $row_depen['nome1'] ?></td>
                            <td>-</td>
                            <td>-</td>
                            <td><?php echo $row_depen['data1br'] ?></td>
                        </tr>
                        <?php } ?>
                        <?php if($row_depen['nome2']!=""){ ?>
                        <tr>
                            <td><?php echo $row_depen['nome2'] ?></td>
                            <td>-</td>
                            <td>-</td>
                            <td><?php echo $row_depen['data2br'] ?></td>
                        </tr>
                        <?php } ?>
                        <?php if($row_depen['nome3']!=""){ ?>
                        <tr>
                            <td><?php echo $row_depen['nome3'] ?></td>
                            <td>-</td>
                            <td>-</td>
                            <td><?php echo $row_depen['data3br'] ?></td>
                        </tr>
                        <?php } ?>
                        <?php if($row_depen['nome4']!=""){ ?>
                        <tr>
                            <td><?php echo $row_depen['nome4'] ?></td>
                            <td>-</td>
                            <td>-</td>
                            <td><?php echo $row_depen['data4br'] ?></td>
                        </tr>
                        <?php } ?>
                        <?php if($row_depen['nome5']!=""){ ?>
                        <tr>
                            <td><?php echo $row_depen['nome5'] ?></td>
                            <td>-</td>
                            <td>-</td>
                            <td><?php echo $row_depen['data5br'] ?></td>
                        </tr>
                        <?php } ?>
                        <?php if($row_depen['nome6']!=""){ ?>
                        <tr>
                            <td><?php echo $row_depen['nome6'] ?></td>
                            <td>-</td>
                            <td>-</td>
                            <td><?php echo $row_depen['data6br'] ?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div class="box">
                <p class="center"><strong>CARTEIRAS PROFISSIONAIS DO FUNCIONÁRIO</strong></p>
                <table border="0" cellspacing="0" cellpadding="0" width="100%" class="grid">
                    <thead>
                        <tr>
                            <th>Número</th>
                            <th>Série</th>
                            <th>Letra</th>
                            <th>Data Expedição</th>
                            <th>Data validade</th>
                            <th>Sigla</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><!--,serie_ctps,uj_ctps,campo3-->
                            <td><?php echo $row['campo1']?></td>
                            <td><?php echo $row['serie_ctps']?></td>
                            <td>-</td>
                            <td><?php echo $row['data_ctps']?></td>
                            <td>-</td>
                            <td><?php echo $row['uf_ctps']?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="box">
                <p class="center"><strong>HISTÓRICO CIPA</strong></p>
                <table border="0" cellspacing="0" cellpadding="0" width="100%" class="grid">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Descrição</th>
                            <th>Data início</th>
                            <th>Data fim</th>
                            <th>Observação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="box">
                <p class="center"><strong>GRADUAÇÃO DO FUNCIONÁRIO</strong></p>
                <table border="0" cellspacing="0" cellpadding="0" width="100%" class="grid">
                    <thead>
                        <tr>
                            <th>Instituíção</th>
                            <th>Profissão</th>
                            <th>Org. Classe</th>
                            <th>Data final</th>
                            <th>Num. Reg.</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php echo $row['instituicao']?></td>
                            <td><?php echo $row_curso['nome']?></td>
                            <td><?php echo $row['conselho']?></td>
                            <td><?php echo $row['data_escola']?></td>
                            <td><?php echo ($row['conselho']!="") ? $row['rg'] : "-"?></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="box">
                <p class="center"><strong>HISTÓRICO DE EXAMES</strong></p>
                <table border="0" cellspacing="0" cellpadding="0" width="100%" class="grid">
                    <thead>
                        <tr>
                            <th>Exame</th>
                            <th>Data pedido</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row_exame = mysql_fetch_assoc($qr_exame)){ ?>
                        <tr>
                            <td><?php echo $row_exame['documento']?></td>
                            <td><?php echo $row_exame['data']?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <div class="box">
                <p class="center"><strong>HISTÓRICO DE CURSOS</strong></p>
                <table border="0" cellspacing="0" cellpadding="0" width="100%" class="grid">
                    <thead>
                        <tr>
                            <th>Curso</th>
                            <th>Data início</th>
                            <th>Data término</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="box">
                <p class="center"><strong>TRANSFERÊNCIA DE UNIDADES</strong></p>
                <table border="0" cellspacing="0" cellpadding="0" width="100%" class="grid">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Descrição</th>
                            <th>Motivo</th>
                            <th>Data</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(mysql_num_rows($qr_transf) > 0){
                            while($row_transf = mysql_fetch_assoc($qr_transf)){ ?>
                        <tr>
                            <td><?php echo $row_transf['id_transferencia']?></td>
                            <td><?php echo "DE: ".$row_transf['de'].", PARA: ".$row_transf['para']?></td>
                            <td><?php echo (empty($row_transf['motivo'])) ? "-" : $row_transf['motivo'];?></td>
                            <td><?php echo $row_transf['data']?></td>
                        </tr>
                        <?php } }else{ ?>
                        <tr>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                        </tr>
                        <?php }?>
                    </tbody>
                </table>
            </div>

            <div class="box">
                <p class="center"><strong>AVERBAÇÕES</strong></p>
                <table border="0" cellspacing="0" cellpadding="0" width="100%" class="grid">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Campo</th>
                            <th>Conteúdo</th>
                            <th>Justificativa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                            <td>-</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="box">
                <p class="center"><strong>OBSERVAÇÕES</strong></p>
                <p class="txleft"><?php echo $row['observacao']?></p>
            </div>

        </div>
    </body>
</html>