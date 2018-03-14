<?php

if(empty($_COOKIE['logado'])){
print "Efetue o Login<br><a href='../login.php'>Logar</a> ";
}else{

include('../conn.php');

$acao = $_REQUEST['acao'];
$id_user = $_COOKIE['logado'];
$regiao = $_REQUEST['regiao'];
$sindicato = $_REQUEST['sindicato'];

$query_master      = mysql_query("SELECT master.id_master, master.razao FROM regioes 
									INNER JOIN master 
									ON regioes.id_master = master.id_master
									WHERE regioes.id_regiao = '$regiao'") or die (mysql_error());

$row_master = mysql_fetch_assoc($query_master);

$mes = date('m');

$meses = array('Erro','Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro');

if(!empty($sindicato)) {
    $result = mysql_query("SELECT * FROM rhsindicato WHERE id_sindicato = '$sindicato'");
    $row = mysql_fetch_array($result);
    
}else{
    $row = array();
}

$mes_desconto = $meses[$row['mes_desconto']];
$mes_dissidio = $meses[$row['mes_dissidio']];

switch($acao){

case 1:
?>
<html>
<head>
        <title>:: Intranet :: Prestador de Serviço</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../../favicon.ico" />
        <link href="../net1.css" rel="stylesheet" type="text/css" />
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />        
        <link href="../favicon.ico" rel="shortcut icon" />
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-pt.js" type="text/javascript"></script>
        <script src="../jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript" ></script>
        <script src="../js/global.js" type="text/javascript"></script>
        
        
        <script>
            $(function() {
                $(".data").mask("99/99/9999");
            });
        </script>
        <style>
            .data{width: 80px;}
            .colEsq{
                float: left;
                width: 55%;
                margin-top: -10px;
            }
            fieldset{
                margin-top: 10px;
            }
            fieldset legend{
                font-family: 'Exo 2', sans-serif;
                font-size: 16px!important;
                font-weight: bold;
            }
            .first{
                vertical-align: 0!important;
            }
            .first-2{
                vertical-align: 0!important;
            }            
        </style>
    </head>
    <body class="novaintra">
        <div id="content" style="width: 850px;">
            <form action="rh_sindicatos_form.php" method="post" name="form1" onSubmit="return validaForm()">
                <div id="head">
                    <img src="../imagens/logomaster<?php echo $row_master['id_master'] ?>.gif" class="fleft" style="margin-right: 25px;">
                    <div class="fleft">
                        <h2>Cadastrar Sindicatos</h2>
                    </div>
                </div>

                <fieldset>
                    <legend>Dados do sindicato</legend>
                    <p><label class='first'>Nome:</label><input type="text" name="nome" id="nome" size="97" onChange="this.value=this.value.toUpperCase()"/></p>
                    <p><label class='first'>Endereço:</label><input type="text" name="endereco" id="endereco" size="97" onChange="this.value=this.value.toUpperCase()"/></p>
                    <div class="esq" style="margin-top:0;">
                        <p><label class='first'>CNPJ:</label><input type="text" name="cnpj" id="cnpj" size="20" /></p>
                        <p><label class='first'>Fax:</label><input type="text" name="fax" id="fax" size="20" /></p>
                        <p><label class='first'>Celular:</label><input type="text" name="cel" id="cel" size="20" /></p>
                    </div>
                    <div class="dir">
                        <p><label class='first'>Telefone:</label><input type="text" name="tel" id="tel" size="20" /></p>
                        <p><label class='first'>Contato:</label><input type="text" name="contato" id="contato" size="20" /></p>
                        <p><label class='first'>E-mail:</label><input type="text" name="email" id="email" size="20" /></p>
                    </div>
                    <p class="clear"><label class='first'>Site:</label><input type="text" name="site" id="site" size="97" /></p>
                </fieldset>
                    
                <fieldset>
                    <legend>Dados da categoria</legend>
                    <div class="esq" style="margin-top:0;">
                        <p><label class='first'>Mês de desconto:</label>
                            <select name="mes_desconto" id="mes_desconto">
                                <?php for ($i2 = 01; $i2 <= 12; $i2 ++) { print "<option value=$i2>$meses[$i2]</option>"; } ?>
                            </select>
                        </p>
                        <p><label class='first'>Piso Salarial:</label><input type="text" name="piso" id="piso" size="20" /></p>
                        <p><label class='first'>Férias (meses):</label><input type="text" name="ferias" id="ferias" size="20" /></p>
                        <p><label class='first'>13 (meses):</label><input type="text" name="decimo_terceiro" id="decimo_terceiro" size="20" /></p>
                        <p><label class='first'>Patronal:</label>
                            <select name="pratonal" id="pratonal">
                                <option value="1">SIM</option>
                                <option value="2">NÃO</option>
                            </select>
                        </p>
                        <p><label class='first'>Entidade Sindical:</label><input type="text" name="entidade" id="entidade" size="20" /></p>
                    </div>
                    <div class="dir">
                        <p><label class='first'>Mês de Dissídio:</label>
                            <select name="mes_dissidio" id="mes_dissidio">
                                <?php for ($i2 = 01; $i2 <= 12; $i2 ++) { print "<option value=$i2>$meses[$i2]</option>"; } ?>
                            </select>
                        </p>
                        <p><label class='first'>Multa do FGTS:</label><input type="text" name="multa" id="multa" size="20" />%</p>
                        <p><label class='first'>Fração:</label><input type="text" name="fracao" id="fracao" size="20" /></p>
                        <p><label class='first'>Recisão:</label><input type="text" name="recisao" id="recisao" size="20" /></p>
                        <p><label class='first'>Evento Relacionado:</label>
                            <select name="evento" id="evento">
                                <option value="5019">CONTRIBUIÇÃO SINDICAL</option>
                            </select>
                        </p>
                    </div>
                </fieldset>

                <input type="hidden" name="acao" value="2">
                <input type="hidden" name="regiao" value="<?=$regiao?>">
                
                <p class="controls"> 
                    <input type="submit" name="cadastrar" id="cadastrar" value="Cadastrar">
                </p>
            </form>
            <script language="javascript">
            function validaForm(){
                       d = document.form1;

                       if (d.nome.value == ""){
                                 alert("O campo Nome deve ser preenchido!");
                                 d.nome.focus();
                                 return false;
                      }

                       if (d.endereco.value == ""){
                                 alert("O campo Endereco deve ser preenchido!");
                                 d.endereco.focus();
                                 return false;
                      }

                       if (d.cnpj.value == ""){
                                 alert("O campo CNPJ deve ser preenchido!");
                                 d.cnpj.focus();
                                 return false;
                      }

                       if (d.contato.value == ""){
                                 alert("O campo Contato deve ser preenchido!");
                                 d.contato.focus();
                                 return false;
                      }


                            return true;   }
            </script>
        </div>
    </body>
</html>
<?php
break;
case 2:
    
    $regiao = $_REQUEST['regiao'];
    $id_user_cad = $_COOKIE['logado'];
    $data_cad = date('Y-m-d');

    $nome = $_REQUEST['nome'];
    $endereco = $_REQUEST['endereco'];
    $cnpj = $_REQUEST['cnpj'];
    $tel = $_REQUEST['tel'];
    $fax = $_REQUEST['fax'];
    $contato = $_REQUEST['contato'];
    $cel = $_REQUEST['cel'];
    $email = $_REQUEST['email'];
    $site = $_REQUEST['site'];
    $mes_desconto = $_REQUEST['mes_desconto'];
    $mes_dissidio = $_REQUEST['mes_dissidio'];
    $piso = $_REQUEST['piso'];
    $multa = $_REQUEST['multa'];
    $ferias = $_REQUEST['ferias'];
    $fracao = $_REQUEST['fracao'];
    $decimo_terceiro = $_REQUEST['decimo_terceiro'];
    $recisao = $_REQUEST['recisao'];
    $pratonal = $_REQUEST['pratonal'];
    $evento = $_REQUEST['evento'];
    $entidade = $_REQUEST['entidade'];
    
    mysql_query("INSERT INTO rhsindicato(id_regiao ,id_user_cad ,data_cad ,nome ,endereco ,cnpj ,tel ,fax ,contato ,cel ,email ,site ,mes_desconto ,mes_dissidio ,piso ,multa ,ferias ,fracao ,decimo_terceiro ,recisao ,pratonal ,evento ,entidade ) VALUES ('$regiao', '$id_user_cad', '$data_cad', '$nome', '$endereco', '$cnpj', '$tel', '$fax', '$contato', '$cel', '$email', '$site', '$mes_desconto', '$mes_dissidio', '$piso', '$multa', '$ferias', '$fracao', '$decimo_terceiro', '$recisao', '$pratonal', '$evento', '$entidade')") or die ("ERRO<BR>".mysql_error());

print "
<script>
alert (\"Sindicato cadastrado!\"); 
location.href=\"../rh/rh_sindicatos.php?regiao=$regiao\"
</script>";


break;
case 3:  //MOSTRANDO OS DADOS
?>

<html>
<head>
        <title>:: Intranet :: Prestador de Serviço</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../../favicon.ico" />
        <link href="../net1.css" rel="stylesheet" type="text/css" />
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />        
        <link href="../favicon.ico" rel="shortcut icon" />
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-pt.js" type="text/javascript"></script>
        <script src="../jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript" ></script>
        <script src="../js/global.js" type="text/javascript"></script>
        
        
        <script>
            $(function() {
                $(".data").mask("99/99/9999");
            });
        </script>
        <style>
            .data{width: 80px;}
            .colEsq{
                float: left;
                width: 55%;
                margin-top: -10px;
            }
            fieldset{
                margin-top: 10px;
            }
            fieldset legend{
                font-family: 'Exo 2', sans-serif;
                font-size: 16px!important;
                font-weight: bold;
            }
            .first{
                vertical-align: 0!important;
            }
            .first-2{
                vertical-align: 0!important;
            }
        </style>
    </head>
    <body class="novaintra">
        <div id="content" style="width: 850px;">
            <div id="head">
                <img src="../imagens/logomaster<?php echo $row_master['id_master'] ?>.gif" class="fleft" style="margin-right: 25px;">
                <div class="fleft">
                    <h2>Cadastrar Sindicatos</h2>
                </div>
            </div>

            <fieldset>
                <legend>Dados do sindicato</legend>
                <p><label class='first'>Nome:</label><label><?php echo $row['nome'] ?></label></p>
                <p><label class='first'>Endereço:</label><label><?php echo $row['endereco'] ?></label></p>
                <div class="esq" style="margin-top:0;">
                    <p><label class='first'>CNPJ:</label><label><?php echo $row['cnpj'] ?></label></p>
                    <p><label class='first'>Fax:</label><label><?php echo $row['fax'] ?></label></p>
                    <p><label class='first'>Celular:</label><label><?php echo $row['cel'] ?></label></p>
                </div>
                <div class="dir">
                    <p><label class='first'>Telefone:</label><label><?php echo $row['tel'] ?></label></p>
                    <p><label class='first'>Contato:</label><label><?php echo $row['contato'] ?></label></p>
                    <p><label class='first'>E-mail:</label><label><?php echo $row['email'] ?></label></p>
                </div>
                <p><label class='first'>Site:</label><label><?php echo $row['site'] ?></label></p>
            </fieldset>

            <fieldset>
                <legend>Dados da categoria</legend>
                <div class="esq" style="margin-top:0;">
                    <p><label class='first'>Mês de desconto:</label><label><?php echo $mes_desconto ?></label></p>
                    <p><label class='first'>Piso Salarial:</label><label><?php echo $row['piso'] ?></label></p>
                    <p><label class='first'>Férias (meses):</label><label><?php echo $row['ferias'] ?></label></p>
                    <p><label class='first'>13 (meses):</label><label><?php echo $row['decimo_terceiro'] ?></label></p>
                    <p><label class='first'>Patronal:</label><label><?php if($row['pratonal'] == 1) { echo 'SIM'; } else { echo 'NÃO';}?></label></p>
                    <p><label class='first'>Entidade Sindical:</label><label><?php echo $row['entidade'] ?></label></p>
                </div>
                <div class="dir">
                    <p><label class='first'>Mês de Dissídio:</label><label><?php echo $mes_dissidio ?></label></p>
                    <p><label class='first'>Multa do FGTS:</label><label><?php echo $row['multa'] ?>%</label></p>
                    <p><label class='first'>Fração:</label><label><?php echo $row['fracao'] ?></label></p>
                    <p><label class='first'>Recisão:</label><label><?php echo $row['recisao'] ?></label></p>
                    <p><label class='first'>Evento Relacionado:</label><label><?php echo $row['evento'] ?></label></p>
                </div>
            </fieldset>
            <form action="rh_sindicatos_form.php" method="post" name="form1" onSubmit="return validaForm()">
                <input type="hidden" name="acao" value="4">
                <input type="hidden" name="regiao" value="<?=$regiao?>">
                <input type="hidden" name="sindicato" value="<?=$sindicato?>">
                
                <p class="controls"> 
                    <input type="submit" name="editar" id="editar" value="Editar Sindicato">
                </p>
            </form>
        </div>
    </body>
</html>
<?php
break;

case 4:
?>
<html>
<head>
        <title>:: Intranet :: Prestador de Serviço</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link rel="shortcut icon" href="../../favicon.ico" />
        <link href="../net1.css" rel="stylesheet" type="text/css" />
        <link href="../css/cupertino/jquery-ui-1.9.2.custom.css" rel="stylesheet" type="text/css" />
        <link href="../css/validationEngine.jquery.css" rel="stylesheet" type="text/css" />
        <link href="../favicon.ico" rel="shortcut icon" />
        <script src="../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../js/jquery-ui-1.9.2.custom.min.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine.js" type="text/javascript"></script>
        <script src="../js/jquery.validationEngine-pt.js" type="text/javascript"></script>
        <script src="../jquery/mascara/jquery.maskedinput-1.2.2.js" type="text/javascript" ></script>
        <script src="../js/global.js" type="text/javascript"></script>
        
        
        <script>
            $(function() {
                $(".data").mask("99/99/9999");
            });
        </script>
        <style>
            .data{width: 80px;}
            .colEsq{
                float: left;
                width: 55%;
                margin-top: -10px;
            }
            fieldset{
                margin-top: 10px;
            }
            fieldset legend{
                font-family: 'Exo 2', sans-serif;
                font-size: 16px!important;
                font-weight: bold;
            }
            .first{
                vertical-align: 0!important;
            }
            .first-2{
                vertical-align: 0!important;
            }
        </style>
    </head>
    <body class="novaintra">
        <div id="content" style="width: 850px;">
            <form action="rh_sindicatos_form.php" method="post" name="form1" onSubmit="return validaForm()">
                <div id="head">
                    <img src="../imagens/logomaster<?php echo $row_master['id_master'] ?>.gif" class="fleft" style="margin-right: 25px;">
                    <div class="fleft">
                        <h2>Cadastrar Sindicatos</h2>
                    </div>
                </div>

                <fieldset>
                    <legend>Dados do sindicato</legend>
                    <p><label class='first'>Nome:</label><input type="text" name="nome" id="nome" size="97" onChange="this.value=this.value.toUpperCase()" value="<?php echo $row['nome'];?>" /></p>
                    <p><label class='first'>Endereço:</label><input type="text" name="endereco" id="endereco" size="97" onChange="this.value=this.value.toUpperCase()" value="<?php echo $row['endereco'];?>" /></p>
                    <div class="esq" style="margin-top:0;">
                        <p><label class='first'>CNPJ:</label><input type="text" name="cnpj" id="cnpj" size="20" value="<?php echo $row['cnpj'];?>" /></p>
                        <p><label class='first'>Fax:</label><input type="text" name="fax" id="fax" size="20" value="<?php echo $row['fax'];?>" /></p>
                        <p><label class='first'>Celular:</label><input type="text" name="cel" id="cel" size="20" value="<?php echo $row['cel'];?>" /></p>
                    </div>
                    <div class="dir">
                        <p><label class='first'>Telefone:</label><input type="text" name="tel" id="tel" size="20" value="<?php echo $row['tel'];?>" /></p>
                        <p><label class='first'>Contato:</label><input type="text" name="contato" id="contato" size="20" value="<?php echo $row['contato'];?>" /></p>
                        <p><label class='first'>E-mail:</label><input type="text" name="email" id="email" size="20" value="<?php echo $row['email'];?>" /></p>
                    </div>
                    <p class="clear"><label class='first'>Site:</label><input type="text" name="site" id="site" size="97" value="<?php echo $row['site'];?>" /></p>
                </fieldset>
                
                <fieldset>
                    <legend>Dados da categoria</legend>
                    <div class="esq" style="margin-top:0;">
                        <p><label class='first'>Mês de desconto:</label>
                            <select name="mes_desconto" id="mes_desconto">
                                <?php for ($i2 = 01; $i2 <= 12; $i2 ++) { ($i2 == $row['mes_desconto']) ? print "<option value=$i2 selected>$meses[$i2]</option>" : "<option value=$i2>$meses[$i2]</option>"; } ?>
                            </select>
                        </p>
                        <p><label class='first'>Piso Salarial:</label><input type="text" name="piso" id="piso" size="20" value="<?php echo $row['piso'];?>" /></p>
                        <p><label class='first'>Férias (meses):</label><input type="text" name="ferias" id="ferias" size="20" value="<?php echo $row['ferias'];?>" /></p>
                        <p><label class='first'>13 (meses):</label><input type="text" name="decimo_terceiro" id="decimo_terceiro" size="20" value="<?php echo $row['decimo_terceiro'];?>" /></p>
                        <p><label class='first'>Patronal:</label>
                            <select name="pratonal" id="pratonal">
                                <?php if($row['pratonal'] == 1) { echo '<option value="1" selected>SIM</option> <option value="2">NÃO</option>'; } else { echo '<option value="1">SIM</option> <option value="2" selected>NÃO</option>';} ?>
                            </select>
                        </p>
                        <p><label class='first'>Entidade Sindical:</label><input type="text" name="entidade" id="entidade" size="20" value="<?php echo $row['entidade'];?>" /></p>
                    </div>
                    <div class="dir">
                        <p><label class='first'>Mês de Dissídio:</label>
                            <select name="mes_dissidio" id="mes_dissidio">
                                <?php for ($i2 = 01; $i2 <= 12; $i2 ++) { ($i2 == $row['mes_dissidio']) ? print "<option value=$i2 selected>$meses[$i2]</option>" : "<option value=$i2>$meses[$i2]</option>"; } ?>
                            </select>
                        </p>
                        <p><label class='first'>Multa do FGTS:</label><input type="text" name="multa" id="multa" size="20" value="<?php echo $row['multa'];?>" />%</p>
                        <p><label class='first'>Fração:</label><input type="text" name="fracao" id="fracao" size="20" value="<?php echo $row['fracao'];?>" /></p>
                        <p><label class='first'>Recisão:</label><input type="text" name="recisao" id="recisao" size="20" value="<?php echo $row['recisao'];?>" /></p>
                        <p><label class='first'>Evento Relacionado:</label>
                            <select name="evento" id="evento">
                                <option value="5019">CONTRIBUIÇÃO SINDICAL</option>
                            </select>
                        </p>
                    </div>
                </fieldset>

                <input type="hidden" name="acao" value="5">
                <input type="hidden" name="regiao" value="<?=$regiao?>">
                <input type="hidden" name="sindicato" value="<?=$sindicato?>">
                
                <p class="controls"> 
                    <input type="submit" name="editar" id="editar" value="Editar Sindicato">
                </p>
            </form>
            <script language="javascript">
            function validaForm(){
                       d = document.form1;

                       if (d.nome.value == ""){
                                 alert("O campo Nome deve ser preenchido!");
                                 d.nome.focus();
                                 return false;
                      }

                       if (d.endereco.value == ""){
                                 alert("O campo Endereco deve ser preenchido!");
                                 d.endereco.focus();
                                 return false;
                      }

                       if (d.cnpj.value == ""){
                                 alert("O campo CNPJ deve ser preenchido!");
                                 d.cnpj.focus();
                                 return false;
                      }

                       if (d.contato.value == ""){
                                 alert("O campo Contato deve ser preenchido!");
                                 d.contato.focus();
                                 return false;
                      }


                            return true;   }
            </script>
        </div>
    </body>
</html>
<?php
break;
case 5:
    
    $regiao = $_REQUEST['regiao'];
    $id_user_cad = $_COOKIE['logado'];
    $data_cad = date('Y-m-d');

    $nome = $_REQUEST['nome'];
    $endereco = $_REQUEST['endereco'];
    $cnpj = $_REQUEST['cnpj'];
    $tel = $_REQUEST['tel'];
    $fax = $_REQUEST['fax'];
    $contato = $_REQUEST['contato'];
    $cel = $_REQUEST['cel'];
    $email = $_REQUEST['email'];
    $site = $_REQUEST['site'];
    $mes_desconto = $_REQUEST['mes_desconto'];
    $mes_dissidio = $_REQUEST['mes_dissidio'];
    $piso = $_REQUEST['piso'];
    $multa = $_REQUEST['multa'];
    $ferias = $_REQUEST['ferias'];
    $fracao = $_REQUEST['fracao'];
    $decimo_terceiro = $_REQUEST['decimo_terceiro'];
    $recisao = $_REQUEST['recisao'];
    $pratonal = $_REQUEST['pratonal'];
    $evento = $_REQUEST['evento'];
    $entidade = $_REQUEST['entidade'];
    
    mysql_query("UPDATE rhsindicato SET nome = '$nome' ,endereco ='$endereco' ,cnpj = '$cnpj',tel ='$tel',fax = '$fax',contato = '$contato',cel = '$cel',email = '$email',site = '$site',mes_desconto = '$mes_desconto',mes_dissidio = '$mes_dissidio',piso = '$piso',multa = '$multa',ferias = '$ferias',fracao = '$fracao',decimo_terceiro = '$decimo_terceiro',recisao = '$recisao',pratonal = '$pratonal',evento = '$evento',entidade = '$entidade' WHERE id_sindicato = $sindicato AND id_regiao = $regiao") or die ("ERRO<BR>".mysql_error());

print "
<script>
alert (\"Sindicato alterado!\"); 
location.href=\"../rh/rh_sindicatos.php?regiao=$regiao\"
</script>";
}
}
?>
