<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='../login.php'>Logar</a> ";
    exit;
}

include "../conn.php";
include "../wfunction.php";
$usuario = carregaUsuario();
        
$id = (isset($_REQUEST['id'])) ? $_REQUEST['id']: 1;
$id_user = $usuario['id_funcionario'];
$regiao = $usuario['id_regiao'];

$rsProjetos = montaQuery("projeto", "id_projeto,nome", "id_regiao={$regiao}", "nome");
$projetos = array("-1" => "« Selecione »");
foreach ($rsProjetos as $pro) {
    $projetos[$pro["id_projeto"]] = $pro["id_projeto"] . " - " . $pro["nome"];
}


$rsUF = montaQuery("uf", "uf_sigla");
$ufs = array("-1" => "« Selecione »");
foreach ($rsUF as $UF) {
    $ufs[$UF["uf_sigla"]] =  $UF["uf_sigla"];
}
?>
<html>
    <head>
        <title><?= _SITENAME ?></title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1"/>
        <script language="javascript" src="../js/ramon.js"></script>
        <link href="../net1.css" rel="stylesheet" type="text/css"/>
        <style>
            #tab-cad tr td{height: 40px;}
        </style>
    </head>
    <body id="nova_intra">
    <?php
    switch ($id) {
        case 1:
            ?>

                <table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0" >
                    <tr>
                        <td align="center" valign="top"> 
                            <table width="750" border="0" cellpadding="0" cellspacing="0">                         
                                <tr>
                                    <td height="32" colspan="2" align="right" bgcolor="#FFF"> <?php include('../reportar_erro.php'); ?></td>
                                </tr>
                                <tr>
                                    <td height="32" colspan="2" align="center" class="show">CADASTRO DE EMPRESAS</td>
                                </tr>
                               
                                <tr>
                                    <td colspan="2" bgcolor="#FFFFFF"><br>
                                        <table width="95%" cellpadding="0" cellspacing="0" class="tabela_ramon" align="center">
                                            <tr class="titulo">
                                                <td colspan="3">EMPRESAS CADASTRADAS</div></td>
                                            </tr>
                                            <tr class="subtitulo">
                                                <td><div align="center">NOME</span></div></td>
                                                <td><div align="center">CNPJ</span></div></td>
                                                <td><div align="center">RESPONS&Aacute;VEL</span></div></td>
                                            </tr>
                                                <?php
                                                $result_empresas = mysql_query("SELECT * FROM rhempresa where id_regiao = '$regiao'");
                                                while ($row_empresas = mysql_fetch_array($result_empresas)) {
                                                    ?>
                                                <tr>
                                                    <td><a href='../rh/rh_empresa.php?empresa=<?= $row_empresas[0] ?>&id=3'>
                                                <?php
                                                if (strlen($row_empresas['nome']) > 25) {
                                                    echo substr($row_empresas['nome'], 0, 25) . '...';
                                                } else {
                                                    echo $row_empresas['nome'];
                                                }
                                                ?>


                                                        </a></td>
                                                    <td><?= $row_empresas[cnpj] ?></td>
                                                    <td><?= $row_empresas[responsavel] ?></td>
                                                </tr>
                                            <?php
                                        }
                                        ?>
                                        </table>
                                        <br/>
                                        <br/>
                                        <form action="rh_empresa.php" name="form1" method="post" enctype='multipart/form-data' onSubmit="return validaForm()">
                                            <table height="700" width="95%" border="0" align="center" cellspacing="0" id="tab-cad">
                                                <tr>
                                                    <td height="45" colspan="6" class="show">
                                                        <img src="imagensrh/dadosempresa.gif" alt="empresa" width="150" height="40">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td width="180" align="right"><div align="right" class="style40 style35"><strong>Projeto:</strong></div></td>
                                                    <td colspan="5"><?php echo montaSelect($projetos, "", "id='projeto' name='projeto' class='validate[required,custom[select]]'") ?></td>
                                                </tr>
                                                <tr>
                                                    <td align="right"><div align="right" class="style40 style35"><strong>Nome Fantasia:</strong></div></td>
                                                    <td colspan="5">
                                                        <input name="nome" type="text" id="nome" size="90" onFocus="document.all.nome.style.background='#CCFFCC'" onBlur="document.all.nome.style.background='#FFFFFF'" style="background:#FFFFFF;" onChange="this.value=this.value.toUpperCase()"></td>
                                                </tr>
                                                <tr>
                                                    <td align="right"><div align="right" class="style40 style35"><strong>Raz&atilde;o Social:</strong></div></td>
                                                    <td colspan="5">      
                                                        <input name="razao" type="text" id="razao" size="90" onFocus="document.all.razao.style.background='#CCFFCC'" onBlur="document.all.razao.style.background='#FFFFFF'" style="background:#FFFFFF;" onChange="this.value=this.value.toUpperCase()">                              </td>
                                                </tr>
                                                <tr>
                                                    <td align="right"><div align="right" class="style35">Endere&ccedil;o:</div></td>
                                                    <td colspan="5">
                                                        <input name="endereco" type="text" id="endereco" size="90" onFocus="document.all.endereco.style.background='#CCFFCC'" onBlur="document.all.endereco.style.background='#FFFFFF'" style="background:#FFFFFF;" onChange="this.value=this.value.toUpperCase()">
                                                         <font size="1" color="#999999">Ex: Av. teste, 32</font>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td align="right">Bairro:</td>
                                                    <td><input name="bairro" id="bairro" type="text"/></td>
                                                    <td align="right">Cidade:</td>
                                                    <td><input name="cidade" id="cidade" type="text"/></td>
                                                    <td align="right">UF:</td>
                                                    <td><?php echo montaSelect($ufs, "", "id='uf' name='uf' class='validate[required,custom[select]]'") ?></td>
                                                </tr>
                                                
                                                    
                                                <tr>
                                                      <td align="right">CEP:</td>
                                                     <td><input name="cep" type="text" id="cep" size="12" OnKeyPress="formatar('#####-###', this)" onKeyUp="pula(13,this.id,email.id)" onFocus="document.all.cep.style.background='#CCFFCC'" onBlur="document.all.cep.style.background='#FFFFFF'" style="background:#FFFFFF;"></td>
                                              
                                                    <td align="right">Inscri&ccedil;&atilde;o Municipal:</td>
                                                    <td><input name="im" type="text" id="im" size="10" onFocus="document.all.im.style.background='#CCFFCC'" onBlur="document.all.im.style.background='#FFFFFF'" style="background:#FFFFFF; text-transform:uppercase;"></td>
                                                    <td align="right">Inscri&ccedil;&atilde;o Estadual:</td>
                                                    <td><input name="ie" type="text" id="ie" size="10" onFocus="document.all.ie.style.background='#CCFFCC'" onBlur="document.all.ie.style.background='#FFFFFF'" style="background:#FFFFFF; text-transform:uppercase;"></td>
                                                  
                                                </tr>
                                                <tr>
                                                    <td align="right">Tipo de CNPJ:</td>
                                                    <td>
                                                        <select name="tipo_cnpj" id="tipo_cnpj">
                                                            <option>SEDE</option>
                                                            <option>REGIONAL</option>
                                                        </select>
                                                    </td>
                                                     <td align="right">CNPJ</td>
                                                   <td><input name="cnpj" type="text" id="cnpj" style="background:#FFFFFF; text-transform:uppercase;"
                                                               onFocus="document.all.cnpj.style.background='#CCFFCC'" onBlur="document.all.cnpj.style.background='#FFFFFF'" onKeyUp="pula(18,this.id,tipo_cnpj.id)"
                                                               OnKeyPress="formatar('##.###.###/####-##', this)" size="18" maxlength="18">
                                                        </td> 
                                                </tr>
                                                <tr>
                                                    <td align="right">Tel.:</td>
                                                    <td><input name='tel' type='text' id='tel' size='12' onKeyPress="return(TelefoneFormat(this,event))" onKeyUp="pula(13,this.id,fax.id)" onFocus="document.all.tel.style.background='#CCFFCC'" onBlur="document.all.tel.style.background='#FFFFFF'" style="background:#FFFFFF; text-transform:uppercase;"></td>    
                                                     <td align="right">Fax:</td>
                                                     <td><input name="fax" type="text" id="fax" size="12" onKeyPress="return(TelefoneFormat(this,event))" onKeyUp="pula(13,this.id,email.id)" onFocus="document.all.fax.style.background='#CCFFCC'" onBlur="document.all.fax.style.background='#FFFFFF'" style="background:#FFFFFF;" ></td>
                                                     <td align="right">E-mail:</td>
                                                     <td>   <input name="email" type="text" id="email" size="40" onFocus="document.all.email.style.background='#CCFFCC'" onBlur="document.all.email.style.background='#FFFFFF'" style="background:#FFFFFF; text-transform:lowercase;"></td>
                                                </tr>
                                                <tr>
                                                    <td align="right">Respons&aacute;vel:</td>
                                                    <td> <input name="responsavel" type="text" id="responsavel" size="50" onFocus="document.all.responsavel.style.background='#CCFFCC'" onBlur="document.all.responsavel.style.background='#FFFFFF'" style="background:#FFFFFF;" onChange="this.value=this.value.toUpperCase()"></td>
                                                    <td align="right">CPF</td>
                                                    <td ><input name="cpf" type="text" id="cpf" OnKeyPress="formatar('###.###.###-##', this)" size="20" maxlength="14" onKeyUp="pula(14,this.id,acid_trabalho.id)" onFocus="document.all.cpf.style.background='#CCFFCC'" onBlur="document.all.cpf.style.background='#FFFFFF'" style="background:#FFFFFF; text-transform:uppercase;"></td>
                                                     <td align="right">Site:</td>
                                                     <td><input name="site" type="text" id="site" size="40" onFocus="document.all.site.style.background='#CCFFCC'" onBlur="document.all.site.style.background='#FFFFFF'" style="background:#FFFFFF; text-transform:lowercase;"></td>
                                                 </tr>
                                                <tr>
                                                    <td align="right">C&oacute;d. Acidentes de Trabalho:</td>
                                                    <td><input name="acid_trabalho" type="text" id="acid_trabalho" size="9" onFocus="document.all.acid_trabalho.style.background='#CCFFCC'" onBlur="document.all.acid_trabalho.style.background='#FFFFFF'" style="background:#FFFFFF; text-transform:uppercase;"></td>
                                                    <td align="right">Atividade:</td>
                                                    <td>
                                                    <select name="atividade" id="atividade">
                                                     <option>91</option>
                                                     <option>69</option>
                                                    </select>
                                                    </td>
                                                    <td align="right">Grupo</td>
                                                    <td>
                                                        <select name="grupo" id="grupo">
                                                            <option>99500</option>
                                                            <option>20601</option>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                     <td align="right">Propriet&aacute;rios:</td>
                                                     <td> <input name="proprietarios" type="text" id="proprietarios" size="3" onFocus="document.all.proprietarios.style.background='#CCFFCC'" onBlur="document.all.proprietarios.style.background='#FFFFFF'" style="background:#FFFFFF; text-transform:uppercase;"></td>
                                                    <td align="right">Familiares:</td>
                                                    <td colspan="3"> <input name="familiares" type="text" id="familiares" size="3" onFocus="document.all.familiares.style.background='#CCFFCC'" onBlur="document.all.familiares.style.background='#FFFFFF'" style="background:#FFFFFF; text-transform:uppercase;"></td>
                                             </tr>
                                                <tr>
                                                    <td align="right">Tipo de Pagamento: </td>
                                                    <td>
                                                        <select name="tipo_pg" id="tipo_pg">
                                                            <option>Mensal</option>
                                                            <option>Semanal</option> 
                                                            <option>Quinzenal</option>
                                                        </select>
                                                    </td>
                                                    <td align="right">C&oacute;d. Munic&iacute;pio:</td>
                                                    <td><input name="municipio" type="text" id="municipio" size="15" onFocus="document.all.municipio.style.background='#CCFFCC'" onBlur="document.all.municipio.style.background='#FFFFFF'" style="background:#FFFFFF; text-transform:uppercase;"></td>
                                                    <td align="right">Ano do 1&ordm; Exerc&iacute;cio</td>
                                                    <td><input name="ano" type="text" id="ano" size="4" maxlength="4" onFocus="document.all.ano.style.background='#CCFFCC'" onBlur="document.all.ano.style.background='#FFFFFF'" style="background:#FFFFFF; text-transform:uppercase;">
                                                        <font size="1" color="#999999">(ex: 2006)</font>
                                                    </td>
                                                   
                                                </tr>
                                                <tr>
                                                    <td colspan="6" bgcolor="#FFFFFF"><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                            <table>
                                                            <tr>
                                                                <td class="style35" align="right">Logo marca: 
                                                                    <label>
                                                                        <input name="logo" type="checkbox" id="foto" onClick="document.all.logomarca.style.display = (document.all.logomarca.style.display == 'none') ? '' : 'none' ;" value="1"/>
                                                                        Sim</label>                    </td>
                                                                <td >
                                                                    <span class="style35" style="display:none" id="logomarca">
                                                                        <strong>selecione o arquivo:</strong>
                                                                        <input type="file" name="arquivo" id="arquivo">
                                                                        <font size="1" color="#999999">(apenas .jpg, .png, .gif, .jpeg)</font>                  </span>                  </td>
                                                            </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="6" bgcolor="#CCCCCC"><div align="center">DADOS PARA FGTS</span></div></td>
                                                </tr>
                                                <tr>
                                                    <td align="right">CNPJ Matriz: </td>
                                                    <td colspan="5"><input name="cnpj_matriz" type="text" id="cnpj_matriz" onFocus="document.all.cnpj_matriz.style.background='#CCFFCC'" onBlur="document.all.cnpj_matriz.style.background='#FFFFFF'" style="background:#FFFFFF; text-transform:uppercase;" onKeyUp="pula(18,this.id,banco.id)"
                                                                   OnKeyPress="formatar('##.###.###/####-##', this)" size="18">
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td align="right">Banco:</td>
                                                    <td><input name="banco" type="text" id="banco" size="10" onFocus="document.all.banco.style.background='#CCFFCC'" onBlur="document.all.banco.style.background='#FFFFFF'" style="background:#FFFFFF; text-transform:uppercase;"></td>
                                                    <td align="right"> Ag&ecirc;ncia:</td>
                                                    <td>    <input name="agencia" type="text" id="agencia" size="8" onFocus="document.all.agencia.style.background='#CCFFCC'" onBlur="document.all.agencia.style.background='#FFFFFF'" style="background:#FFFFFF; text-transform:uppercase;"></td>
                                                    <td align="right">Conta:</td>
                                                    <td><input name="conta" type="text" id="conta" size="12" onFocus="document.all.conta.style.background='#CCFFCC'" onBlur="document.all.conta.style.background='#FFFFFF'" style="background:#FFFFFF; text-transform:uppercase;"></td>
                                                </tr>

                                                <tr>
                                                    <td colspan="6" bgcolor="#CCCCCC"><div align="center" class="style35">DADOS PARA INSS / SEFIP</div></td>
                                                </tr>
                                                <tr>
                                                    <td align="right">FPAS: </td>
                                                    <td>     <select name="fpas" id="fpas">
                                                            <option>515</option>
                                                        </select>
                                                    </td>
                                                    <td align="right">Tipo:</td>
                                                    <td>
                                                        <select name="tipo_fpas" id="tipo_fpas">
                                                            <option>&Uacute;nico</option>
                                                            <option>Principal</option>
                                                            <option>Filial</option>
                                                            <option>Outros</option>
                                                        </select>
                                                    </td>
                                                    <td align="right">Porte:</td> 
                                                    <td>    <select name="porte" id="porte">
                                                            <option>Normal</option>
                                                            <option>Pequeno</option>
                                                            <option>Micro</option>
                                                        </select>
                                                    </td>
                                                  
                                                </tr>
                                                <tr>
                                                      <td align="right">Natureza Jur&iacute;dica:</td>
                                                    <td>
                                                        <select name="natureza" id="natureza">
                                                            <option>3999</option>
                                                            <option>2062</option>
                                                        </select>
                                                    </td>
                                                    <td align="right">Capital Social: </td>
                                                    <td><input name="capital" type="text" id="capital" size="20" onFocus="document.all.capital.style.background='#CCFFCC'" onBlur="document.all.capital.style.background='#FFFFFF'" style="background:#FFFFFF; text-transform:uppercase;"></td>
                                                    <td align="right">In&iacute;cio das Atividades:</td>
                                                    <td colspan="3"><input name="data_inicio" type="text" id="data_inicio" size="10" OnKeyUp="mascara_data(this)" maxlength="10" onFocus="document.all.data_inicio.style.background='#CCFFCC'" onBlur="document.all.data_inicio.style.background='#FFFFFF'" style="background:#FFFFFF; text-transform:uppercase;"></td>
                                                </tr>
                                                <tr>
                                                    <td align="right">Simples:</td>
                                                    <td>
                                                        <select name="simples" id="simples">
                                                            <option>N&atilde;o Optante</option>
                                                        </select>
                                                    </td>
                                                    <td align="right">PAT</td>
                                                    <td>    <input type="checkbox" name="pat" id="pat" value="1"></td>
                                                </tr>
                                                <tr>
                                                    <td align="right">Empresa: </td>
                                                    <td><input name="p_empresa" type="text" id="p_empresa" size="5" onFocus="document.all.p_empresa.style.background='#CCFFCC'" onBlur="document.all.p_empresa.style.background='#FFFFFF'" style="background:#FFFFFF; text-transform:uppercase;"></td>
                                                    <td>%Acidente de Trabalho:</td>
                                                    <td><input name="p_acid_trabalho" type="text" id="p_acid_trabalho" size="5" onFocus="document.all.p_acid_trabalho.style.background='#CCFFCC'" onBlur="document.all.p_acid_trabalho.style.background='#FFFFFF'" style="background:#FFFFFF; text-transform:uppercase;"></td>
                                                    <td>% Prolabore / Autônomo:</td>
                                                    <td colspan="2"> <input name="p_prolabora" type="text" id="p_prolabora" size="5" onFocus="document.all.p_prolabora.style.background='#CCFFCC'" onBlur="document.all.p_prolabora.style.background='#FFFFFF'" style="background:#FFFFFF; text-transform:uppercase;"></td>
                                                </tr>
                                                <tr>
                                                    <td align="right">C&oacute;d Terceiros:</td>
                                                    <td> <input name="terceiros" type="text" id="terceiros" size="5" onFocus="document.all.terceiros.style.background='#CCFFCC'" onBlur="document.all.terceiros.style.background='#FFFFFF'" style="background:#FFFFFF; text-transform:uppercase;"></td>
                                                   <td align="right">Terceiros:</td>
                                                    <td><input name="p_terceiros" type="text" id="p_terceiros" size="5" onFocus="document.all.p_terceiros.style.background='#CCFFCC'" onBlur="document.all.p_terceiros.style.background='#FFFFFF'" style="background:#FFFFFF; text-transform:uppercase;"></td>
                                                    <td align="right">% Isen. Emp. Filantr&oacute;picas:</td>
                                                   <td> <input name="p_filantropicas" type="text" id="poracidente4" size="5" onFocus="document.all.p_filantropicas.style.background='#CCFFCC'" onBlur="document.all.p_filantropicas.style.background='#FFFFFF'" style="background:#FFFFFF; text-transform:uppercase;"></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="6" bgcolor="#CCCCCC"><div align="center">DADOS PARA RAIS</span></div></td>
                                                </tr>
                                                <tr>
                                                    <td align="right">C&oacute;digo do Mun&iacute;cipio:</td>
                                                    <td> <input name="cod_municipio" type="text" id="cod_municipio" onFocus="document.all.cnpj_matriz.style.background='#CCFFCC'" onBlur="document.all.cnpj_matriz.style.background='#FFFFFF'" style="background:#FFFFFF; text-transform:uppercase;" onKeyUp="pula(17,this.id,banco.id)"
                                                                   OnKeyPress="formatar('###.###.####/#-##', this)" size="18" maxlength="17">
                                                    </td>
                                                    <td align="right">CNAE:</td>
                                                    <td><input name="cnae" type="text" id="cnae" size="12" onFocus="document.all.banco.style.background='#CCFFCC'" onBlur="document.all.banco.style.background='#FFFFFF'" style="background:#FFFFFF; text-transform:uppercase;"></td>
                                                    <td align="right">Natureza Jur&iacute;dica:</td>
                                                     <td><input name="nat_juridica" type="text" id="nat_juridica" size="8" onFocus="document.all.agencia.style.background='#CCFFCC'" onBlur="document.all.agencia.style.background='#FFFFFF'" style="background:#FFFFFF; text-transform:uppercase;"></td>
                                               </tr>
                                            </table>
                                            <div align="center" class="style35">
                                                <br>
                                                <input type="hidden" name="id" value="2">
                                                <input type="hidden" name="regiao" value="<?= $regiao ?>">                                                <br>
                                                <label>
                                                    <input type="submit" name="Submit" id="button" value="Cadastrar">
                                                </label>
                                            </div>
                                            <br>
                                            </form>


                                                            <script language="javascript">
                                                                function validaForm(){
                                                                    d = document.form1;

                                                                    if (d.nome.value == ""){
                                                                        alert("O campo Nome deve ser preenchido!");
                                                                        d.nome.focus();
                                                                        return false;
                                                                    }

                                                                    if (d.razao.value == ""){
                                                                        alert("O campo Razão Social deve ser preenchido!");
                                                                        d.razao.focus();
                                                                        return false;
                                                                    }
        		  
                                                                    if (d.endereco.value == ""){
                                                                        alert("O campo Endereço deve ser preenchido!");
                                                                        d.endereco.focus();
                                                                        return false;
                                                                    }

                                                                    if (d.im.value == ""){
                                                                        alert("O campo  Inscrição Municipal deve ser preenchido!");
                                                                        d.im.focus();
                                                                        return false;
                                                                    }
        		  
                                                                    if (d.ie.value == ""){
                                                                        alert("O campo Inscrição Estadual deve ser preenchido!");
                                                                        d.ie.focus();
                                                                        return false;
                                                                    }


                                                                    if (d.cnpj.value == ""){
                                                                        alert("O campo CNPJ deve ser preenchido!");
                                                                        d.cnpj.focus();
                                                                        return false;
                                                                    }

                                                                    if (d.responsavel.value == ""){
                                                                        alert("O campo Responsavel deve ser preenchido!");
                                                                        d.responsavel.focus();
                                                                        return false;
                                                                    }

                                                                    if (d.cpf.value == ""){
                                                                        alert("O campo CPF deve ser preenchido!");
                                                                        d.cpf.focus();
                                                                        return false;
                                                                    }


                                                                    return true;   }
                                                            </script>


                                                            <br>          </td>
                                                            </tr>

                                                            <tr>
                                                                <td width="155" bgcolor="#FFFFFF">&nbsp;</td>
                                                                <td width="549" bgcolor="#FFFFFF">&nbsp;</td>
                                                            </tr>

                                                            <tr valign="top"> 
                                                                <td height="37" colspan="4">      <?php
        include "../empresa.php";
        $rod = new empresa();
        $rod->rodape();
        ?></td>
                                                            </tr>
                                            </table>
                                    </td>
                                </tr>
                            </table>

        <?php
        break;
    case 2:  //INSERINDO AS INFORMAÇÕES
    
        $id_regiao = $_REQUEST['regiao'];
        $id_user_cad = $_COOKIE['logado'];
        $data_cad = date('Y-m-d');
        $projeto = $_REQUEST['projeto'];
        $nome = $_REQUEST['nome'];
        $razao = $_REQUEST['razao'];
        $endereco = $_REQUEST['endereco'];
        $im = $_REQUEST['im'];
        $ie = $_REQUEST['ie'];
        $cnpj = $_REQUEST['cnpj'];
        $tipo_cnpj = $_REQUEST['tipo_cnpj'];
        $tel = $_REQUEST['tel'];
        $fax = $_REQUEST['fax'];
        $cep = $_REQUEST['cep'];
        $email = $_REQUEST['email'];
        $site = $_REQUEST['site'];
        $responsavel = $_REQUEST['responsavel'];
        $cpf = $_REQUEST['cpf'];
        $acid_trabalho = $_REQUEST['acid_trabalho'];
        $atividade = $_REQUEST['atividade'];
        $grupo = $_REQUEST['grupo'];
        $proprietarios = $_REQUEST['proprietarios'];
        $familiares = $_REQUEST['familiares'];
        $tipo_pg = $_REQUEST['tipo_pg'];
        $municipio = $_REQUEST['municipio'];
        $ano = $_REQUEST['ano'];
        $logo = $_REQUEST['logo'];
        $cnpj_matriz = $_REQUEST['cnpj_matriz'];
        $banco = $_REQUEST['banco'];
        $agencia = $_REQUEST['agencia'];
        $conta = $_REQUEST['conta'];
        $fpas = $_REQUEST['fpas'];
        $tipo_fpas = $_REQUEST['tipo_fpas'];
        $porte = $_REQUEST['porte'];
        $natureza = $_REQUEST['natureza'];
        $capital = $_REQUEST['capital'];
        $data_inicio = $_REQUEST['data_inicio'];
        $simples = $_REQUEST['simples'];
        $pat = $_REQUEST['pat'];
        $p_empresa = $_REQUEST['p_empresa'];
        $p_acid_trabalho = $_REQUEST['p_acid_trabalho'];
        $p_prolabora = $_REQUEST['p_prolabora'];
        $terceiros = $_REQUEST['terceiros'];
        $p_terceiros = $_REQUEST['p_terceiros'];
        $p_filantropicas = $_REQUEST['p_filantropicas'];
        $cod_municipio = $_REQUEST['cod_municipio'];
        $cnae = $_REQUEST['cnae'];
        $nat_juridica = $_REQUEST['nat_juridica'];
        $bairro = $_REQUEST['bairro'];
        $cidade = $_REQUEST['cidade'];
        $uf = $_REQUEST['uf'];  
        $data_inicio_f = implode('-', array_reverse(explode('/',$data_inicio)));  
  
    
      
      
        
     

        if (!empty($logo)) {           //AQUI TEM ARQUIVO
            $arquivo = isset($_FILES['arquivo']) ? $_FILES['arquivo'] : FALSE;

            if ($arquivo[type] != "image/x-png" && $arquivo[type] != "image/pjpeg" && $arquivo[type] != "image/gif" && $arquivo [type] != "image/jpe") {     //aki a imagem nao corresponde com as extenções especificadas
                print "<center>
     <hr><font size=2><b>
     Tipo de arquivo não permitido, os únicos padrões permitidos são .gif, .jpg , .jpeg ou .png<br>
     $arquivo[type] <br><br>
     <a href='../rh_empresa.php?id=1&regiao=$regiao'>Voltar</a>
     </b></font>";

                exit;
            } else {  //aqui o arquivo é realente de imagem e vai ser carregado para o servidor
                $arr_basename = explode(".", $arquivo['name']);
                $file_type = $arr_basename[1];

                if ($file_type == "gif") {
                    $tipo_name = ".gif";
                } if ($file_type == "jpg" or $arquivo[type] == "jpeg") {
                    $tipo_name = ".jpg";
                } if ($file_type == "png") {
                    $tipo_name = ".png";
                }

                $logo = $tipo_name;

            
                mysql_query("
INSERT INTO rhempresa (id_projeto,id_regiao ,id_user_cad ,data_cad ,nome ,razao ,endereco ,im ,ie ,cnpj ,tipo_cnpj ,tel ,fax ,cep ,email ,site ,responsavel ,cpf ,acid_trabalho ,atividade ,grupo ,proprietarios ,familiares ,tipo_pg ,municipio ,ano ,logo ,cnpj_matriz ,banco ,agencia ,conta ,fpas ,tipo_fpas ,porte ,natureza ,capital ,data_inicio ,simples ,pat ,p_empresa ,p_acid_trabalho ,p_prolabora ,terceiros ,p_terceiros ,p_filantropicas,cod_municipio ,cnae ,nat_juridica, bairro, cidade, uf)
VALUES ('{$projeto}','$id_regiao', '$id_user_cad', '$data_cad', '$nome', '$razao', '$endereco', '$im', '$ie', '$cnpj', '$tipo_cnpj', '$tel', '$fax', '$cep', '$email', '$site', '$responsavel', '$cpf', '$acid_trabalho', '$atividade', '$grupo', '$proprietarios', '$familiares', '$tipo_pg', '$municipio', '$ano', '$logo', '$cnpj_matriz', '$banco', '$agencia', '$conta', '$fpas', '$tipo_fpas', '$porte', '$natureza', '$capital', '$data_inicio_f', '$simples', '$pat', '$p_empresa', '$p_acid_trabalho', '$p_prolabora', '$terceiros', '$p_terceiros', '$p_filantropicas', '$cod_municipio', '$cnae', '$nat_juridica','$bairro','$cidade', '$uf')")
or die(mysql_error());

                $id_insert = mysql_insert_id();

                // Resolvendo o nome e para onde o arquivo será movido
                $diretorio = "logo/";

                $nome_tmp = $regiao . "logo" . $id_insert . $tipo_name;
                $nome_arquivo = "$diretorio$nome_tmp";

                move_uploaded_file($arquivo['tmp_name'], $nome_arquivo) or die("Erro ao enviar o Arquivo: $nome_arquivo");
            } //aqui fecha o IF que verificar se o arquivo tem a extenção especificada
      
            
            
            } else {    //AQUI ESTÁ SEM A LOGO
            $logo = "0";

            mysql_query("
INSERT INTO rhempresa (id_projeto,id_regiao ,id_user_cad ,data_cad ,nome ,razao ,endereco ,im ,ie ,cnpj ,tipo_cnpj ,tel ,fax ,email ,site ,responsavel ,cpf ,acid_trabalho ,atividade ,grupo ,proprietarios ,familiares ,tipo_pg ,municipio ,ano ,logo ,cnpj_matriz ,banco ,agencia ,conta ,fpas ,tipo_fpas ,porte ,natureza ,capital ,data_inicio ,simples ,pat ,p_empresa ,p_acid_trabalho ,p_prolabora ,terceiros ,p_terceiros ,p_filantropicas, bairro, cidade, uf)
VALUES ('{$projeto}','$id_regiao', '$id_user_cad', '$data_cad', '$nome', '$razao', '$endereco', '$im', '$ie', '$cnpj', '$tipo_cnpj', '$tel', '$fax', '$email', '$site', '$responsavel', '$cpf', '$acid_trabalho', '$atividade', '$grupo', '$proprietarios', '$familiares', '$tipo_pg', '$municipio', '$ano', '$logo', '$cnpj_matriz', '$banco', '$agencia', '$conta', '$fpas', '$tipo_fpas', '$porte', '$natureza', '$capital', '$data_inicio_f', '$simples', '$pat', '$p_empresa', '$p_acid_trabalho', '$p_prolabora', '$terceiros', '$p_terceiros', '$p_filantropicas','$bairro','$cidade','$uf')") or die(mysql_error());
        }

        
        
        
       print "
<script>
alert (\"Empresa cadastrada!\"); 
location.href=\"rh_empresa.php?id=1&regiao=$regiao\"
</script>";


        break;
    case 3:  //MOTRANDO TODOS OS DADOS DA EMPRESA

        $id_empresa = $_REQUEST['empresa'];

        $result = mysql_query("SELECT *,date_format(data_inicio, '%d/%m/%Y')as data_inicio FROM rhempresa WHERE id_empresa = '$id_empresa'") or die("Erro no SELECT<BR>" . mysql_error());
        $row = mysql_fetch_array($result);
        ?>

                            <table width='750' border='0' cellpadding='0' cellspacing='0' bgcolor='#FFFFFF' align='center'>
                                <tr>
                                    <td colspan='4'><img src='../layout/topo.gif' width='750' height='38' /></td>
                                </tr>

                                <tr>
                                    <td width='21' rowspan='3' background='../layout/esquerdo.gif'>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td>&nbsp;</td>
                                    <td width='26' rowspan='3' background='../layout/direito.gif'>&nbsp;</td>
                                </tr>


                                <tr>
                                    <td colspan='2'><table height='656' width='95%' align='center' cellspacing='0' cellpadding="0">
                                            <tr class="novo_tr_dois">
                                                <td height="45" colspan="6" >
                                                    <img src='imagensrh/dadosempresa.gif' alt='empresa' width='150' height='40' />
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width='20%' class="secao">Nome Fantasia:</td>
                                                <td colspan='5'>&nbsp;&nbsp;<?= $row['nome'] ?></td>
                                            </tr>
                                            <tr>
                                                <td class="secao">Raz&atilde;o Social:</td>
                                                <td colspan='5'><span class='style43'>&nbsp;&nbsp;<?= $row['razao'] ?></span></td>
                                            </tr>
                                            <tr>
                                                <td class="secao">Endere&ccedil;o:</td>
                                                <td colspan='5'><span class='style43'>&nbsp;&nbsp;<?= $row['endereco'] ?></span></td>
                                            </tr>
                                            <tr>
                                                <td class="secao">Inscri&ccedil;&atilde;o Municipal:
                                                </td>
                                                <td width="16%">&nbsp;&nbsp;<?= $row['im'] ?></td>
                                                <td width="16%" class="secao">Insc. Estadual:</td>
                                                <td width="15%">&nbsp;&nbsp;<?= $row['ie'] ?></td>
                                                <td width="12%" class="secao">CNPJ:</td>
                                                <td width="21%">&nbsp;&nbsp;<?= $row['cnpj'] ?></td>
                                            </tr>
                                            <tr>
                                                <td class="secao">Tipo de CNPJ:</td>
                                                <td>&nbsp;&nbsp;<?= $row['tipo_cnpj'] ?></td>
                                                <td class="secao">Tel.:</td>
                                                <td>&nbsp;&nbsp;<?= $row['tel'] ?></td>
                                                <td class="secao">Fax:</td>
                                                <td>&nbsp;&nbsp;<?= $row['fax'] ?></td>
                                            </tr>
                                            <tr>
                                                <td class="secao">E-mail:</td>
                                                <td><?= $row['email'] ?></td>
                                                <td class="secao">Site:</td>
                                                <td colspan="3">&nbsp;&nbsp;<?= $row['site'] ?></td>
                                            </tr>
                                            <tr>
                                                <td class="secao">Respons&aacute;vel:</td>
                                                <td colspan="3">&nbsp;&nbsp;<?= $row['responsavel'] ?></td>
                                                <td class="secao">CPF:</td>
                                                <td>&nbsp;&nbsp;<?= $row['cpf'] ?></td>
                                            </tr>
                                            <tr>
                                                <td class="secao">C&oacute;d. Acidentes de Trabalho:</td>
                                                <td>&nbsp;&nbsp;<?= $row['ie'] ?></td>
                                                <td class="secao">Atividade:</td>
                                                <td>&nbsp;&nbsp;<?= $row['atividade'] ?></td>
                                                <td class="secao">Grupo:</td>
                                                <td>&nbsp;&nbsp;<?= $row['grupo'] ?></td>
                                            </tr>
                                            <tr>
                                                <td  class="secao">Propriet&aacute;rios:</td>
                                                <td>&nbsp;&nbsp;<?= $row['proprietarios'] ?></td>
                                                <td class="secao">Familiares:</td>
                                                <td>&nbsp;&nbsp;<?= $row['familiares'] ?></td>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <td height="40" class="secao">Tipo de Pagamento:</td>
                                                <td>&nbsp;&nbsp;<?= $row['tipo_pg'] ?></td>
                                                <td class="secao">C&oacute;d. Munic&iacute;pio:</td>
                                                <td>&nbsp;&nbsp;<?= $row['municipio'] ?></td>
                                                <td class="secao">Ano do 1&ordm; Exerc&iacute;cio:</td>
                                                <td>&nbsp;&nbsp;<?= $row['ano'] ?></td>
                                            </tr>

                                            <tr class="novo_tr_dois">
                                                <td colspan='6'>DADOS DO FGTS</td>
                                            </tr>
                                            <tr>
                                                <td colspan='6' class='style40'><table width="100%" border="0" cellspacing="0" cellpadding="0">
                                                        <tr>
                                                            <td width="14%" class="secao">CNPJ Matriz:</td>
                                                            <td width="14%">&nbsp;&nbsp;<?= $row['cnpj_matriz'] ?></td>
                                                            <td width="12%" class="secao">Banco:</td>
                                                            <td width="15%">&nbsp;&nbsp;<?= $row['banco'] ?></td>
                                                            <td width="9%" class="secao">Ag&ecirc;ncia:</td>
                                                            <td width="11%">&nbsp;&nbsp;<?= $row['agencia'] ?></td>
                                                            <td width="8%" class="secao">Conta</td>
                                                            <td width="17%"><?= $row['conta'] ?></td>
                                                        </tr>
                                                    </table></td>
                                            </tr>
                                            <tr class="novo_tr_dois">
                                                <td colspan='6' >DADOS DO INSS</td>
                                            </tr>
                                            <tr>
                                                <td class="secao">FPAS:</td>
                                                <td>&nbsp;&nbsp;<?= $row['fpas'] ?></td>
                                                <td class="secao">Tipo:</td>
                                                <td>&nbsp;&nbsp;<?= $row['tipo_fpas'] ?></td>
                                                <td class="secao">Porte:</td>
                                                <td>&nbsp;&nbsp;<?= $row['porte'] ?></td>
                                            </tr>
                                            <tr>
                                                <td height="39" class="secao">Natureza Jur&iacute;dica:</td>
                                                <td>&nbsp;&nbsp;<?= $row['natureza'] ?></td>
                                                <td class="secao">Capital Social:</td>
                                                <td>&nbsp;&nbsp;R$ <?= $row['capital'] ?></td>
                                                <td class="secao">In&iacute;cio das Atividades:</td>
                                                <td>&nbsp;&nbsp;<?= $row['data_inicio'] ?></td>
                                            </tr>
                                            <tr>
                                                <td class="secao">Simples:</td>
                                                <td>&nbsp;&nbsp;<?= $row['simples'] ?></td>
                                                <td class="secao">PAT:</td>
                                                <td>&nbsp;&nbsp;<?= $row['pat'] ?></td>
                                                <td class="secao">% Empresa:</td>
                                                <td>&nbsp;&nbsp;<?= $row['p_empresa'] ?></td>
                                            </tr>
                                            <tr>
                                                <td height="40" class="secao">% Acidente de Trabalho:</td>
                                                <td>&nbsp;&nbsp;<?= $row[p_acid_trabalho] ?></td>
                                                <td class="secao">% Prolabora / Aut&ocirc;nomo:</td>
                                                <td>&nbsp;&nbsp;<?= $row['p_prolabora'] ?></td>
                                                <td class="secao">% Terceiros:</td>
                                                <td>&nbsp;&nbsp;<?= $row[p_terceiros] ?></td>
                                            </tr>
                                            <tr>
                                                <td height="30" class="secao">C&oacute;d Terceiros:</td>
                                                <td>&nbsp;&nbsp;<?= $row['terceiros'] ?></td>
                                                <td class="secao">% Isen. Emp. Filantr&oacute;picas:</td>
                                                <td colspan="3">&nbsp;&nbsp;<?= $row[p_filantropicas] ?></td>
                                            </tr>
                                        </table>
                                        <div align='center' class='style35'>
                                            <br><br>
                                            <a href='javascript:history.go(-1)' class='link'><img src='../imagens/voltar.gif' border=0></a>
                                            <br><br>
                                        </div></td>
                                </tr>
                                <tr>
                                    <td width='155'>&nbsp;</td>
                                    <td width='549'>&nbsp;</td>
                                </tr>
                                <tr valign='top'>
                                    <td height='37' colspan='4' bgcolor="#E2E2E2"><img src='../layout/baixo.gif' width='750' height='38' />
                                        <div align='center' class='rodape'><strong>Intranet do Instituto Sorrindo Para a Vida</strong> - Acesso Restrito 
                                            a Funcion&aacute;rios <br />
                                        </div></td>
                                </tr>
                            </table>
                            </body>
                            </html>
        <?php
        break;
} // FECHANDO O   CASE

/* Liberando o resultado */
//mysql_free_result($result);

/* Fechando a conexão */
mysql_close($conn);
?>