<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='../../login.php'>Logar</a> ";
    exit;
}

include("../../conn.php");
include("../../wfunction.php");
$usuario = carregaUsuario();
$dadosHeader = montaCabecalhoNovo(getRegioes(), getMasters(), $usuario, __FILE__); //PREPARA VARIAVEIS PARA FUNCIONAMENTO DO CABEÇALHO (TROCA DE MASTER E DE REGIÕES)

if (empty($_REQUEST['fornecedor1'])) {

    $regiao = $_REQUEST['regiao'];
    $pedido = $_REQUEST['compra'];

    $result = mysql_query("SELECT *,
                                    date_format(data_produto, '%d/%m/%Y')as data_produto, 
                                    date_format(data_requisicao, '%d/%m/%Y')as data_requisicao,
                                    date_format(prazo1, '%d/%m/%Y')as prazo1BR,
                                    date_format(prazo2, '%d/%m/%Y')as prazo2BR,
                                    date_format(prazo3, '%d/%m/%Y')as prazo3BR
                               FROM compra where id_compra = '$pedido'");
    
    $row = mysql_fetch_array($result);

    $result_reg = mysql_query("SELECT * FROM regioes where id_regiao = '$regiao'", $conn);
    $row_reg = mysql_fetch_array($result_reg);

    $result_user = mysql_query("SELECT nome1 FROM funcionario where id_funcionario = '$row[id_user_pedido]'", $conn);
    $row_user = mysql_fetch_array($result_user);

    $result_fornecedor = mysql_query("SELECT * FROM prestadorservico WHERE id_projeto=$row[id_projeto] AND status = 1 ORDER BY c_razao");
    
    $fornecedores = array();
    while ($row_for = mysql_fetch_array($result_fornecedor)) {
        $fornecedores[$row_for[0]] = $row_for['c_cnpj']." - ".$row_for['c_razao'];
    }
    
    ?>
    <!DOCTYPE html>
    <html lang="pt">
        <head>
            <meta charset="iso-8859-1">
            <meta http-equiv="X-UA-Compatible" content="IE=edge">
            <meta name="viewport" content="width=device-width, initial-scale=1">

            <title>:: Intranet :: Gestão de Compas da OS</title>

            <link href="../../favicon.png" rel="shortcut icon" />

            <!-- Bootstrap -->
            <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="all">
            <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
            <link href="../../resources/css/main.css" rel="stylesheet" media="screen">
            <link href="../../resources/css/font-awesome.css" rel="stylesheet" media="screen">
            <link href="../../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        </head>

        <body>
            <?php include("../../template/navbar_default.php"); ?>

            <div class="container">
                <div class="page-header box-compras-header"><h2><span class="glyphicon glyphicon-shopping-cart"></span> - COMPRAS E CONTRATOS <small> - Cotações</small></h2></div>
                
                <form action="cotacoes.php" method="post" name="form1">
                    
                    <!-- FOENECEDOR 1 -->
                    <div class="panel panel-default">
                        <div class="panel-heading text-bold hidden-print"><i class="fa fa-truck"></i> Fornecedor 1</div>
                        <div class="panel-body">
                            <table class="table table-striped table-hover table-condensed table-bordered">
                                <tr height="32">
                                    <td width="16%" height="30" align="right" bgcolor="#F7F7F7" class="style29"><span class="style29">SELECIONE</span>:</td>
                                    <td height="30" colspan="3" align="left" bgcolor="#F7F7F7" class="style29">
                                        <?=montaSelect($fornecedores, $row['fornecedor1'], "id='fornecedor1' name='fornecedor1' class='form-control'")?>
                                    </td>
                                </tr>
                                <tr height="32">
                                    <td width="16%" height="30" align="right" bgcolor="#F7F7F7" class="style29"><span class="style29">&Iacute;TEM</span>:</td>
                                    <td height="30" colspan="3" align="left" bgcolor="#F7F7F7" class="style29"><strong>&nbsp;&nbsp;&nbsp;<?php print "$row[nome_produto]"; ?></strong></td>
                                </tr>
                                <tr height="32">
                                    <td height="30" align="right" bgcolor="#F7F7F7" class="style29"><span class="style29">DESCRI&Ccedil;&Atilde;O:</span></td>
                                    <td height="30" colspan="3" align="left" bgcolor="#F7F7F7" class="style29"><strong>&nbsp;&nbsp;&nbsp;<?php print "$row[descricao_produto]"; ?></strong></td>
                                </tr>
                                <tr height="32">
                                    <td height="30" align="right" bgcolor="#F7F7F7" class="style29"><span class="style29">QUANTIDADE:</span></td>
                                    <td width="30%" height="30" align="left" bgcolor="#F7F7F7" class="style29"><strong>&nbsp;&nbsp;&nbsp;<?php print "$row[quantidade]"; ?></strong></td>
                                    <td width="21%" height="30" align="right" bgcolor="#F7F7F7" class="style29"><span class="style29">VALOR UNIT&Aacute;RIO:</span></td>
                                    <td width="33%" height="30" align="left" bgcolor="#F7F7F7" class="style29"><strong>
                                            &nbsp;&nbsp;
                                            <input name="valor_uni1" type="text" id="dataentrega2" size="15" OnKeyDown="FormataValor(this, event, 17, 2)" value="<?php echo $row['valor_uni1'] ?>"/>
                                        </strong></td>
                                </tr>
                                <tr height="32">
                                    <td height="30" align="right" valign="middle" bgcolor="#F7F7F7" class="style29"><span class="style29">DATA PARA ENTREGA:</span></td>
                                    <td height="30" align="left" valign="middle" bgcolor="#F7F7F7" class="style29"><strong>&nbsp;&nbsp;&nbsp;
                                            <input name="entrega1" type="text" id="dataentrega" size="10" OnKeyUp="mascara_data(this)" maxlength="10"  value="<?php echo $row['prazo1BR'] ?>"/>
                                        </strong></td>
                                    <td height="30" align="right" valign="middle" bgcolor="#F7F7F7" class="style29"><span class="style29">VALOR TOTAL:</span></td>
                                    <td height="30" align="left" valign="middle" bgcolor="#F7F7F7" class="style29"><strong>&nbsp;&nbsp;
                                            <input name="valor1" type="text" id="dataentrega3" size="15" OnKeyDown="FormataValor(this, event, 17, 2)"  value="<?php echo $row['preco1'] ?>"/>
                                        </strong></td>
                                </tr>
                                <tr height="32">
                                    <td height="30" align="right" bgcolor="#F7F7F7" class="style29">MARCA:</td>
                                    <td height="30" colspan="2" align="left" bgcolor="#F7F7F7" class="style29"><strong>&nbsp;&nbsp;&nbsp;
                                            <input name="marca1" type="text" id="dataentrega11" size="35"  value="<?php echo $row['marca1'] ?>" />
                                        </strong></td>
                                    <td height="30" align="center" valign="middle" bgcolor="#F7F7F7" class="style29">&nbsp;</td>
                                </tr>
                                <tr height="32">
                                    <td height="30" align="right" bgcolor="#F7F7F7" class="style29">OBSERVA&Ccedil;&Atilde;O:</td>
                                    <td height="30" colspan="3" align="left" bgcolor="#F7F7F7" class="style29"><strong>&nbsp;&nbsp;&nbsp;
                                            <input name="obs1" type="text" id="dataentrega12" size="70"  value="<?php echo $row['obs1'] ?>" />
                                        </strong></td>
                                </tr>
                                <tr height="32">
                                    <td height="30" colspan="4" align="center" valign="middle" bgcolor="#F7F7F7" class="style29">&nbsp;<span class="style29"><strong>Valor dos</strong>&nbsp;<strong>Impostos:
                                                <input name="imposto1" type="text" id="contato13" size="15" OnKeyDown="FormataValor(this, event, 17, 2)"  value="<?php echo $row['imposto1'] ?>"/>
                                                &nbsp;&nbsp;
                                                &nbsp;&nbsp;
                                                &nbsp;&nbsp;&nbsp;&nbsp;<strong>Valor do Frete:
                                                    <input name="frete1" type="text" id="contato14" size="15" OnKeyDown="FormataValor(this, event, 17, 2)"  value="<?php echo $row['frete1'] ?>"/>
                                                    &nbsp;&nbsp;
                                                    &nbsp;&nbsp;
                                                    &nbsp;&nbsp;<strong>Descontos:
                                                        <input name="desconto1" type="text" id="contato15" size="10" OnKeyDown="FormataValor(this, event, 17, 2)" value="<?php echo $row['desconto1'] ?>"/>
                                                    </strong></strong></strong></span></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <!-- FOENECEDOR 2 -->
                    <div class="panel panel-default">
                        <div class="panel-heading text-bold hidden-print"><i class="fa fa-truck"></i> Fornecedor 2</div>
                        <div class="panel-body">
                            <table class="table table-striped table-hover table-condensed table-bordered">
                                <tr height="32">
                                    <td width="16%" height="30" align="right" bgcolor="#F7F7F7" class="style29"><span class="style29">SELECIONE</span>:</td>
                                    <td height="30" colspan="3" align="left" bgcolor="#F7F7F7" class="style29">
                                        <?=montaSelect($fornecedores, $row['fornecedor2'], "id='fornecedor2' name='fornecedor2' class='form-control'")?>
                                    </td>
                                </tr>
                                <tr height="32">
                                    <td width="16%" height="30" align="right" bgcolor="#F7F7F7" class="style29">&Iacute;TEM:</td>
                                    <td height="30" colspan="3" align="left" bgcolor="#F7F7F7" class="style29"><strong>&nbsp;&nbsp;&nbsp;<?php print "$row[nome_produto]"; ?></strong></td>
                                </tr>
                                <tr height="32">
                                    <td height="30" align="right" bgcolor="#F7F7F7" class="style29">DESCRI&Ccedil;&Atilde;O:</td>
                                    <td height="30" colspan="3" align="left" bgcolor="#F7F7F7" class="style29"><strong>&nbsp;&nbsp;&nbsp;<?php print "$row[descricao_produto]"; ?></strong></td>
                                </tr>
                                <tr height="32">
                                    <td height="30" align="right" bgcolor="#F7F7F7" class="style29">QUANTIDADE:</td>
                                    <td width="30%" height="30" align="left" bgcolor="#F7F7F7" class="style29"><strong>&nbsp;&nbsp;&nbsp;<?php print "$row[quantidade]"; ?></strong></td>
                                    <td width="21%" height="30" align="right" bgcolor="#F7F7F7" class="style29">VALOR UNIT&Aacute;RIO:</td>
                                    <td width="33%" height="30" align="left" bgcolor="#F7F7F7" class="style29"><strong> &nbsp;&nbsp;
                                            <input name="valor_uni2" type="text" id="valor_uni2" size="15" OnKeyDown="FormataValor(this, event, 17, 2)" value="<?php echo $row['valor_uni2'] ?>"/>
                                        </strong></td>
                                </tr>
                                <tr height="32">
                                    <td height="30" align="right" valign="middle" bgcolor="#F7F7F7" class="style29">DATA PARA ENTREGA:</td>
                                    <td height="30" align="left" valign="middle" bgcolor="#F7F7F7" class="style29"><strong>&nbsp;&nbsp;&nbsp;
                                            <input name="entrega2" type="text" id="dataentrega5" size="10" OnKeyUp="mascara_data(this)" maxlength="10" value="<?php echo $row['prazo2BR'] ?>" />
                                        </strong></td>
                                    <td height="30" align="right" valign="middle" bgcolor="#F7F7F7" class="style29">VALOR TOTAL:</td>
                                    <td height="30" align="left" valign="middle" bgcolor="#F7F7F7" class="style29"><strong>&nbsp;&nbsp;
                                            <input name="valor2" type="text" id="dataentrega6" size="15" OnKeyDown="FormataValor(this, event, 17, 2)" value="<?php echo $row['preco2'] ?>"/>
                                        </strong></td>
                                </tr>
                                <tr height="32">
                                    <td height="30" align="right" bgcolor="#F7F7F7" class="style29">MARCA:</td>
                                    <td height="30" colspan="2" align="left" bgcolor="#F7F7F7" class="style29"><strong>&nbsp;&nbsp;&nbsp;
                                            <input name="marca2" type="text" id="dataentrega10" size="35"  value="<?php echo $row['marca2'] ?>"/>
                                        </strong></td>
                                    <td height="30" align="center" valign="middle" bgcolor="#F7F7F7" class="style29">&nbsp;</td>
                                </tr>
                                <tr height="32">
                                    <td height="30" align="right" bgcolor="#F7F7F7" class="style29">OBSERVA&Ccedil;&Atilde;O:</td>
                                    <td height="30" colspan="3" align="left" bgcolor="#F7F7F7" class="style29"><strong>&nbsp;&nbsp;&nbsp;
                                            <input name="obs2" type="text" id="dataentrega13" size="70"  value="<?php echo $row['obs2'] ?>" />
                                        </strong></td>
                                </tr>
                                <tr height="32">
                                    <td height="30" colspan="4" align="center" valign="middle" bgcolor="#F7F7F7" class="style29">&nbsp;<strong>Valor dos</strong>&nbsp;<strong>Impostos:
                                            <input name="imposto2" type="text" id="imposto2" size="15" OnKeyDown="FormataValor(this, event, 17, 2)" value="<?php echo $row['imposto2'] ?>"/>
                                            &nbsp;&nbsp;
                                            &nbsp;&nbsp;
                                            &nbsp;&nbsp;&nbsp;&nbsp;<strong>Valor do Frete:
                                                <input name="frete2" type="text" id="contato2" size="15" OnKeyDown="FormataValor(this, event, 17, 2)" value="<?php echo $row['frete2'] ?>"/>
                                                &nbsp;&nbsp;
                                                &nbsp;&nbsp;
                                                &nbsp;&nbsp;<strong>Descontos:
                                                    <input name="desconto2" type="text" id="contato3" size="10" OnKeyDown="FormataValor(this, event, 17, 2)" value="<?php echo $row['desconto2'] ?>"/>
                                                </strong></strong></strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <!-- FOENECEDOR 3 -->
                    <div class="panel panel-default">
                        <div class="panel-heading text-bold hidden-print"><i class="fa fa-truck"></i> Fornecedor 3</div>
                        <div class="panel-body">
                            <table class="table table-striped table-hover table-condensed table-bordered"> 
                                <tr height="32">
                                    <td width="16%" height="30" align="right" bgcolor="#F7F7F7" class="style29"><span class="style29">SELECIONE</span>:</td>
                                    <td height="30" colspan="3" align="left" bgcolor="#F7F7F7" class="style29">
                                        <?=montaSelect($fornecedores, $row['fornecedor3'], "id='fornecedor3' name='fornecedor3' class='form-control'")?>
                                    </td>
                                </tr>
                                <tr height="32">
                                    <td width="16%" height="30" align="right" bgcolor="#F7F7F7" class="style29">&Iacute;TEM:</td>
                                    <td height="30" colspan="3" align="left" bgcolor="#F7F7F7" class="style29"><strong>&nbsp;&nbsp;&nbsp;<?php print "$row[nome_produto]"; ?></strong></td>
                                </tr>
                                <tr height="32">
                                    <td height="30" align="right" bgcolor="#F7F7F7" class="style29">DESCRI&Ccedil;&Atilde;O:</td>
                                    <td height="30" colspan="3" align="left" bgcolor="#F7F7F7" class="style29"><strong>&nbsp;&nbsp;&nbsp;<?php print "$row[descricao_produto]"; ?></strong></td>
                                </tr>
                                <tr height="32">
                                    <td height="30" align="right" bgcolor="#F7F7F7" class="style29">QUANTIDADE:</td>
                                    <td width="31%" height="30" align="left" bgcolor="#F7F7F7" class="style29"><strong>&nbsp;&nbsp;&nbsp;<?php print "$row[quantidade]"; ?></strong></td>
                                    <td width="21%" height="30" align="right" bgcolor="#F7F7F7" class="style29">VALOR UNIT&Aacute;RIO:</td>
                                    <td width="32%" height="30" align="left" bgcolor="#F7F7F7" class="style29"><strong> &nbsp;&nbsp;
                                            <input name="valor_uni3" type="text" id="dataentrega7" size="15" OnKeyDown="FormataValor(this, event, 17, 2)" value="<?php echo $row['valor_uni3'] ?>"/>
                                        </strong></td>
                                </tr>
                                <tr height="32">
                                    <td height="30" align="right" valign="middle" bgcolor="#F7F7F7" class="style29">DATA PARA ENTREGA:</td>
                                    <td height="30" align="left" valign="middle" bgcolor="#F7F7F7" class="style29"><strong>&nbsp;&nbsp;&nbsp;
                                            <input name="entrega3" type="text" id="dataentrega8" size="10" OnKeyUp="mascara_data(this)" maxlength="10" value="<?php echo $row['prazo3BR'] ?>"/>
                                        </strong></td>
                                    <td height="30" align="right" valign="middle" bgcolor="#F7F7F7" class="style29">VALOR TOTAL:</td>
                                    <td height="30" align="left" valign="middle" bgcolor="#F7F7F7" class="style29"><strong>&nbsp;&nbsp;
                                            <input name="valor3" type="text" id="dataentrega9" size="15" OnKeyDown="FormataValor(this, event, 17, 2)" value="<?php echo $row['preco3'] ?>"/>
                                        </strong></td>
                                </tr>
                                <tr height="32">
                                    <td height="30" align="right" bgcolor="#F7F7F7" class="style29">MARCA:</td>
                                    <td height="30" colspan="2" align="left" bgcolor="#F7F7F7" class="style29"><strong>&nbsp;&nbsp;&nbsp;
                                            <input name="marca3" type="text" id="dataentrega4" size="35"  value="<?php echo $row['marca3'] ?>" />
                                        </strong></td>
                                    <td height="30" align="center" valign="middle" bgcolor="#F7F7F7" class="style29">&nbsp;</td>
                                </tr>
                                <tr height="32">
                                    <td height="30" align="right" bgcolor="#F7F7F7" class="style29">OBSERVA&Ccedil;&Atilde;O:</td>
                                    <td height="30" colspan="3" align="left" bgcolor="#F7F7F7" class="style29"><strong>&nbsp;&nbsp;&nbsp;
                                            <input name="obs3" type="text" id="dataentrega14" size="70"  value="<?php echo $row['obs3'] ?>" />
                                        </strong></td>
                                </tr>
                                <tr height="32">
                                    <td height="30" colspan="4" align="center" valign="middle" bgcolor="#F7F7F7" class="style29">&nbsp;<strong>Valor dos</strong>&nbsp;<strong>Impostos:
                                            <input name="imposto3" type="text" id="contato4" size="15" OnKeyDown="FormataValor(this, event, 17, 2)" value="<?php echo $row['imposto3'] ?>"/>
                                            &nbsp;&nbsp;
                                            &nbsp;&nbsp;
                                            &nbsp;&nbsp;&nbsp;&nbsp;<strong>Valor do Frete:
                                                <input name="frete3" type="text" id="contato5" size="15" OnKeyDown="FormataValor(this, event, 17, 2)" value="<?php echo $row['frete3'] ?>"/>
                                                &nbsp;&nbsp;
                                                &nbsp;&nbsp;
                                                &nbsp;&nbsp;<strong>Descontos:
                                                    <input name="desconto3" type="text" id="contato6" size="10" OnKeyDown="FormataValor(this, event, 17, 2)" value="<?php echo $row['desconto3'] ?>"/>
                                                </strong></strong></strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div align="center">
                        <button type="submit" name="GRAVAR3" id="GRAVAR3" value="CONTINUAR" class="btn btn-success"><i class="fa fa-save"></i> Salvar</button>
                        
                        <button type="button" name="voltar" id="voltar" value="Voltar" class="btn btn-danger" onclick="javascript:history.go(-1);"><i class="fa fa-arrow-left"></i> Cancelar</button>
                        
                        <input type="hidden" value="<?php print "$row[0]"; ?>" name="produto" />
                        <input type="hidden" value="<?php print "$regiao"; ?>" name="regiao" />
                    </div>
                </form>
                <?php include('../../template/footer.php'); ?>
            </div>


            <script src="../../js/jquery-1.10.2.min.js"></script>
            <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
            <script src="../../resources/js/bootstrap.min.js"></script>
            <script src="../../resources/js/bootstrap-datepicker.js"></script>
            <script src="../../resources/js/bootstrap-dialog.min.js"></script>
            <script src="../../resources/js/tooltip.js"></script>
            <script src="../../resources/js/main.js"></script>
            <script src="../../js/global.js"></script>        
            <script src="../../js/jquery.validationEngine-2.6.js" type="text/javascript"></script>
            <script src="../../js/jquery.validationEngine-pt_BR-2.6.js" type="text/javascript"></script>
            
            <script>
                function mascara_data(d){
                    var mydata = '';
                    var data = d.value;
                    var mydata = mydata + data;

                    if (mydata.length == 2){
                            mydata = mydata + '/';
                        d.value = mydata;
                    }

                    if (mydata.length == 5){
                        mydata = mydata + '/';
                        d.value = mydata;
                    }

                    if (mydata.length == 10){
                        verifica_data(d);
                    }
                }

                function verifica_data (d) {

                dia = (d.value.substring(0, 2));
                        mes = (d.value.substring(3, 5));
                        ano = (d.value.substring(6, 10));
                        situacao = \"\";  
                        // verifica o dia valido para cada mes  
                        if ((dia < 01) || (dia < 01 || dia > 30) && (mes == 04 || mes == 06 || mes == 09 || mes == 11) || dia > 31) {
                situacao = \"falsa\";  
                }

                // verifica se o mes e valido  
                if (mes < 01 || mes > 12) {
                situacao = \"falsa\";  
                }

                // verifica se e ano bissexto  
                if (mes == 2 && (dia < 01 || dia > 29 || (dia > 28 && (parseInt(ano / 4) != ano / 4)))) {
                situacao = \"falsa\";  
                }

                if (d.value == \"\") {  
                        situacao = \"falsa\";  
                }

                if (situacao == \"falsa\") {  
                    alert(\"Data digitada é inválida, digite novamente!\"); 
                            d.value = \"\";  
                            d.focus();
                }

                           //}

                function FormataValor(objeto, teclapres, tammax, decimais)
                {

                    var tecla = teclapres.keyCode;
                            var tamanhoObjeto = objeto.value.length;
                            if ((tecla == 8) && (tamanhoObjeto == tammax))
                    {
                    tamanhoObjeto = tamanhoObjeto - 1;
                    }



                    if ((tecla == 8 || tecla == 88 || tecla >= 48 && tecla <= 57 || tecla >= 96 && tecla <= 105) && ((tamanhoObjeto + 1) <= tammax))
                    {

                    vr = objeto.value;
                    vr = vr.replace(\"/\", \"\" );
                    vr = vr.replace(\"/\", \"\" );
                    vr = vr.replace(\",\", \"\" );
                    vr = vr.replace(\".\", \"\" );
                    vr = vr.replace(\".\", \"\" );
                    vr = vr.replace(\".\", \"\" );
                    vr = vr.replace(\".\", \"\" );
                            tam = vr.length;
                            if (tam < tammax && tecla != 8)
                    {
                    tam = vr.length + 1;
                    }

                    if ((tecla == 8) && (tam > 1))
                    {
                    tam = tam - 1;
                    vr = objeto.value;
                    vr = vr.replace(\"/\", \"\" );
                    vr = vr.replace(\"/\", \"\" );
                    vr = vr.replace(\",\", \"\" );
                    vr = vr.replace(\".\", \"\" );
                    vr = vr.replace(\".\", \"\" );
                    vr = vr.replace(\".\", \"\" );
                    vr = vr.replace(\".\", \"\" );
                    }

                    //Cálculo para casas decimais setadas por parametro
                    if (tecla == 8 || tecla >= 48 && tecla <= 57 || tecla >= 96 && tecla <= 105)
                    {
                    if (decimais > 0)
                    {
                    if ((tam <= decimais))
                    {
                    objeto.value = (\"0,\" + vr) ;
                    }
                    if ((tam == (decimais + 1)) && (tecla == 8))
                    {
                    objeto.value = vr.substr(0, (tam - decimais)) + ',' + vr.substr(tam - (decimais), tam);
                    }
                    if ((tam > (decimais + 1)) && (tam <= (decimais + 3)) && ((vr.substr(0, 1)) == \"0\"))
                    {
                    objeto.value = vr.substr(1, (tam - (decimais + 1))) + ',' + vr.substr(tam - (decimais), tam);
                    }
                    if ((tam > (decimais + 1)) && (tam <= (decimais + 3)) && ((vr.substr(0, 1)) != \"0\"))
                    {
                    objeto.value = vr.substr(0, tam - decimais) + ',' + vr.substr(tam - decimais, tam);
                    }
                    if ((tam >= (decimais + 4)) && (tam <= (decimais + 6)))
                    {
                    objeto.value = vr.substr(0, tam - (decimais + 3)) + '.' + vr.substr(tam - (decimais + 3), 3) + ',' + vr.substr(tam - decimais, tam);
                    }
                    if ((tam >= (decimais + 7)) && (tam <= (decimais + 9)))
                    {
                    objeto.value = vr.substr(0, tam - (decimais + 6)) + '.' + vr.substr(tam - (decimais + 6), 3) + '.' + vr.substr(tam - (decimais + 3), 3) + ',' + vr.substr(tam - decimais, tam);
                    }
                    if ((tam >= (decimais + 10)) && (tam <= (decimais + 12)))
                    {
                    objeto.value = vr.substr(0, tam - (decimais + 9)) + '.' + vr.substr(tam - (decimais + 9), 3) + '.' + vr.substr(tam - (decimais + 6), 3) + '.' + vr.substr(tam - (decimais + 3), 3) + ',' + vr.substr(tam - decimais, tam);
                    }
                    if ((tam >= (decimais + 13)) && (tam <= (decimais + 15)))
                    {
                    objeto.value = vr.substr(0, tam - (decimais + 12)) + '.' + vr.substr(tam - (decimais + 12), 3) + '.' + vr.substr(tam - (decimais + 9), 3) + '.' + vr.substr(tam - (decimais + 6), 3) + '.' + vr.substr(tam - (decimais + 3), 3) + ',' + vr.substr(tam - decimais, tam);
                    }
                    }
                    else if (decimais == 0)
                    {
                    if (tam <= 3)
                    {
                    objeto.value = vr;
                    }
                    if ((tam >= 4) && (tam <= 6))
                    {
                    if (tecla == 8)
                    {
                    objeto.value = vr.substr(0, tam);
                            window.event.cancelBubble = true;
                            window.event.returnValue = false;
                    }
                    objeto.value = vr.substr(0, tam - 3) + '.' + vr.substr(tam - 3, 3);
                    }
                    if ((tam >= 7) && (tam <= 9))
                    {
                    if (tecla == 8)
                    {
                    objeto.value = vr.substr(0, tam);
                            window.event.cancelBubble = true;
                            window.event.returnValue = false;
                    }
                    objeto.value = vr.substr(0, tam - 6) + '.' + vr.substr(tam - 6, 3) + '.' + vr.substr(tam - 3, 3);
                    }
                    if ((tam >= 10) && (tam <= 12))
                    {
                    if (tecla == 8)
                    {
                    objeto.value = vr.substr(0, tam);
                            window.event.cancelBubble = true;
                            window.event.returnValue = false;
                    }
                    objeto.value = vr.substr(0, tam - 9) + '.' + vr.substr(tam - 9, 3) + '.' + vr.substr(tam - 6, 3) + '.' + vr.substr(tam - 3, 3);
                    }
                    if ((tam >= 13) && (tam <= 15))
                    {
                    if (tecla == 8)
                    {
                    objeto.value = vr.substr(0, tam);
                            window.event.cancelBubble = true;
                            window.event.returnValue = false;
                    }
                    objeto.value = vr.substr(0, tam - 12) + '.' + vr.substr(tam - 12, 3) + '.' + vr.substr(tam - 9, 3) + '.' + vr.substr(tam - 6, 3) + '.' + vr.substr(tam - 3, 3);
                    }
                    }
                    }
                    }
                    else if ((window.event.keyCode != 8) && (window.event.keyCode != 9) && (window.event.keyCode != 13) && (window.event.keyCode != 35) && (window.event.keyCode != 36) && (window.event.keyCode != 46))
                    {
                    window.event.cancelBubble = true;
                            window.event.returnValue = false;
                    }
                }
            </script>
        </body>
    </html>
    <?php
} else {   //----------------- ALTERANDO OS REGISTRO NA BASE DE DADOS -----------------------//
    $regiao = $_REQUEST['regiao'];
    $pedido = $_REQUEST['produto'];
    $id_user = $_COOKIE['logado'];

    $fornecedor1 = $_REQUEST['fornecedor1'];
    $entrega1 = $_REQUEST['entrega1'];
    $valor_uni1 = $_REQUEST['valor_uni1'];
    $marca1 = $_REQUEST['marca1'];
    $valor1 = $_REQUEST['valor1'];
    $imposto1 = $_REQUEST['imposto1'];
    $frete1 = $_REQUEST['frete1'];
    $desconto1 = $_REQUEST['desconto1'];
    $obs1 = $_REQUEST['obs1'];

    $fornecedor2 = $_REQUEST['fornecedor2'];
    $entrega2 = $_REQUEST['entrega2'];
    $valor_uni2 = $_REQUEST['valor_uni2'];
    $marca2 = $_REQUEST['marca2'];
    $valor2 = $_REQUEST['valor2'];
    $imposto2 = $_REQUEST['imposto2'];
    $frete2 = $_REQUEST['frete2'];
    $desconto2 = $_REQUEST['desconto2'];
    $obs2 = $_REQUEST['obs2'];

    $fornecedor3 = $_REQUEST['fornecedor3'];
    $entrega3 = $_REQUEST['entrega3'];
    $valor_uni3 = $_REQUEST['valor_uni3'];
    $marca3 = $_REQUEST['marca3'];
    $valor3 = $_REQUEST['valor3'];
    $imposto3 = $_REQUEST['imposto3'];
    $frete3 = $_REQUEST['frete3'];
    $desconto3 = $_REQUEST['desconto3'];
    $obs3 = $_REQUEST['obs3'];

    $data = date('Y-m-d');

    function ConverteData($Data) {
        if (strstr($Data, "/")) {//verifica se tem a barra /
            $d = explode("/", $Data); //tira a barra
            $rstData = "$d[2]-$d[1]-$d[0]"; //separa as datas $d[2] = ano $d[1] = mes etc...
            return $rstData;
        } elseif (strstr($Data, "-")) {
            $d = explode("-", $Data);
            $rstData = "$d[2]/$d[1]/$d[0]";
            return $rstData;
        } else {
            return "Data invalida";
        }
    }

    $entrega1 = ConverteData($entrega1);
    $entrega2 = ConverteData($entrega2);
    $entrega3 = ConverteData($entrega3);

    mysql_query("UPDATE compra SET id_user_pesquisa='$id_user', fornecedor1='$fornecedor1', fornecedor2='$fornecedor2', 
fornecedor3='$fornecedor3',preco1='$valor1', preco2='$valor2', preco3='$valor3', prazo1='$entrega1', 
prazo2='$entrega2', prazo3='$entrega3', marca1='$marca1', marca2='$marca2', marca3='$marca3', 
valor_uni1='$valor_uni1', valor_uni2='$valor_uni2', valor_uni3='$valor_uni3', imposto1='$imposto1', 
imposto2='$imposto2', imposto3='$imposto3', frete1='$frete1', frete2='$frete2', frete3='$frete3',
desconto1='$desconto1', desconto2='$desconto2', desconto3='$desconto3', obs1='$obs1', obs2='$obs2', obs3='$obs3', 
data_pesquisa='$data', acompanhamento='4' where id_compra = '$pedido' LIMIT 1") or die("<center>ERRO!<br> tente novamente mais tarde<br><br>" . mysql_error());


    header("Location: cotacoes2.php?compra=$pedido&regiao=$regiao ");
}
?>