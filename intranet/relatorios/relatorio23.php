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
        <title>Relat&oacute;rio de Participantes do Projeto em Ordem Alfabética</title>
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
                            <td align="center">Cod.</td> 
                            <td align="center">Nome</td> 
                            <td align="center">Atividade</td>
                            <td align="center">Unidade</td>  
                            <td width="100">Salário</td>
                            <td width="140">Data de Nascimento</td>
                            <td align="center">Estado Civil</td>
                            <td align="center">Sexo</td>
                            <td align="center">Nacionalidade</td>
                            <td align="center">Endereço</td>
                            <td align="center">Bairro</td>
                            <td align="center">Cidade</td>
                            <td align="center">Uf</td>
                            <td align="center">CEP</td>
                            <td align="center">Naturalidade</td>
                            <td align="center">Estudante</td>
                            <td align="center">Terminou em</td>
                            <td align="center">Escolaridade</td>
                            <td align="center">Tipo de Formação(curso)</td>
                            <td align="center">Instituição de ensino</td> 
                            <td align="center">Telefone Fixo</td>
                            <td align="center">Celular</td>
                            <td align="center">Pai</td>
                            <td align="center">Nacionalidade do pai</td>
                            <td align="center">Mãe</td>
                            <td align="center">Nacionalidade da mãe</td>
                            <td align="center">Número de filhos</td>
                            <td align="center">Cabelos</td>
                            <td align="center">Olhos</td>
                            <td align="center">Peso</td>
                            <td align="center">Altura</td>
                            <td align="center">Etnia</td>  
                            <td align="center">Marcas ou Cicatriz</td> 
                            <td align="center">Deficiência:</td>
                            <td align="center">CPF</td>
                            <td align="center">Série (CTPS)</td>
                            <td align="center">UF (CTPS)</td>          
                            <td align="center">Data da carteira de trabalho</td>
                            <td align="center">Certificado de reservista</td>
                            <td align="center">PIS</td>
                            <td align="center">Data do PIS</td>
                            <td align="center">FGTS</td>
                            <td align="center">Título de eleitor</td>
                            <td align="center">Zona</td>
                            <td align="center">Seção</td>             
                            <td align="center">RG</td>             
                            <td align="center">Orgão expedidor (RG)</td>
                            <td align="center">UF (RG)</td>
                            <td align="center">Data de expedição (RG)</td>             
                            <td align="center">Data de entrada</td>
                            <td align="center">Data do exame admissional</td>             
                            <td align="center">Local de pagamento</td>

                            <td align="center">Observações</td>       
                            <td align="center">Banco</td>
                            <td align="center">Agência</td>
                            <td align="center">C.C.</td>
                        </tr>

                        <?php
                        ////EXIBE OS REGITROS DUPLICADOS SE EXISTIREM (CLT)

                        $clt = mysql_query("SELECT A.id_clt,
                                    A.nome, 
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
                                    B.nome
                                    FROM rh_clt AS A
                                    LEFT JOIN bancos AS B ON (A.banco=B.id_banco)
                                    WHERE  A.status < 60 AND A.id_projeto ='4'

                                    ORDER BY A.locacao,A.nome");

                        $num_clt = mysql_num_rows($clt);

                        if ($num_clt != 0)
                        while ($row_clt = mysql_fetch_assoc($clt)){
                        ?>
                        <tr>

                            
                            <td align="center"><?= $row_clt['nome'] ?></td>
                            <td align="center"><?= $row_clt['locacao'] ?></td>
                            <td align="center"><?= $row_clt['data'] ?></td>
                            
                            <td align="center"><?= $row_clt['pis'] ?></td>
                            <td align="center"><?= $row_clt['campo1'] ?></td>
                            <td align="center"><?= $row_clt['serie_ctps'] ?></td>
                            <td align="center"><?= $row_clt['uf_ctps'] ?></td>
                            <td align="center"><?= $row_clt['cpf'] ?></td>
                                  
                            <td align="center"><?= $row_banco['nome'] ?></td>
                            <td align="center"><?= $row_clt['agencia'] ?></td>
                            <td align="center"><?= $row_clt['conta'] ?></td>
                        </tr>

                        <?php
                        }
                        ?>

                        <tr class="secao">
                            <td colspan="56" align="left">TOTAL DE CLTS: <?php echo $num_clt; ?></td>
                        </tr>
                    </table>

                    <?php } ?>

                </td>
            </tr>
            <tr>
                <td colspan="3">

                    <?php
                    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////// COOPERADO  //////////////////////////////////////////////////////
                    if (!empty($num_cooperado)) {
                    ?>

                    <div class="descricao" style="text-align:left;font-weight:bold;">Relat&oacute;rio de Colaboradores do Projeto em Ordem Alfabética</div> 
                    <table class="relacao" width="100%" cellpadding="3" cellspacing="1"  style="margin-left:30px;margin-right:30px;">
                        <tr class="secao">

                        <tr class="secao" align="center">

                            <td align="center">Cod.</td> 
                            <td align="center">Nome</td> 
                            <td align="center">Atividade</td>
                            <td align="center">Unidade</td>  
                            <td align="center"width="100">Salário</td>
                            <td align="center"width="140">Data de Nascimento</td>
                            <td align="center">Estado Civil</td>
                            <td align="center">Sexo</td>
                            <td align="center">Nacionalidade</td>
                            <td align="center">Endereço</td>
                            <td align="center">Bairro</td>
                            <td align="center">Cidade</td>
                            <td align="center">Uf</td>
                            <td align="center">CEP</td>
                            <td align="center">Naturalidade</td>
                            <td align="center">Estudante</td>
                            <td align="center">Terminou em</td>
                            <td align="center">Escolaridade</td>
                            <td align="center">Tipo de Formação(curso)</td>
                            <td align="center">Instituição de ensino</td> 
                            <td align="center">Telefone Fixo</td>
                            <td align="center">Celular</td>
                            <td align="center">Pai</td>
                            <td align="center">Nacionalidade do pai</td>
                            <td align="center">Mãe</td>
                            <td align="center">Nacionalidade da mãe</td>
                            <td align="center">Número de filhos</td>
                            <td align="center">Cabelos</td>
                            <td align="center">Olhos</td>
                            <td align="center">Peso</td>
                            <td align="center">Altura</td>
                            <td align="center">Etnia</td>  
                            <td align="center">Marcas ou Cicatriz</td> 
                            <td align="center">Deficiência:</td>
                            <td align="center">CPF</td>
                            <td align="center">Série (CTPS)</td>
                            <td align="center">UF (CTPS)</td>          
                            <td align="center">Data da carteira de trabalho</td>
                            <td align="center">Certificado de reservista</td>
                            <td align="center">PIS</td>
                            <td align="center">Data do PIS</td>
                            <td align="center">FGTS</td>
                            <td align="center">Título de eleitor</td>
                            <td align="center">Zona</td>
                            <td align="center">Seção</td>             
                            <td align="center">RG</td>             
                            <td align="center">Orgão expedidor (RG)</td>
                            <td align="center">UF (RG)</td>
                            <td align="center">Data de expedição (RG)</td>             
                            <td align="center">Data de entrada</td>
                            <td align="center">Data do exame admissional</td>             
                            <td align="center">Local de pagamento</td>

                            <td align="center">Observações</td>           
                            <td align="center">Banco</td>
                            <td align="center">Agência</td>
                            <td align="center">C.C.</td>
                        </tr>

                        <?php
                        ////EXIBE OS REGITROS DUPLICADOS SE EXISTIREM (COOPERADO)
                        $qr_duplicado_cooperado = mysql_query("SELECT id_autonomo,
												     nome,
													 rg,
													 pis,
													 cpf,
													 serie_ctps,
													 titulo,
													 COUNT(nome) as total_nome, 
													COUNT(rg) as total_rg, 
													COUNT(cpf) as total_cpf ,
													COUNT(serie_ctps) as total_ctps,
													COUNT(pis) as total_pis,
													COUNT(titulo) as total_titulo						
													FROM autonomo
																								
													WHERE status = '1'  AND tipo_contratacao ='3' AND id_regiao ='$regiao' AND id_projeto ='$projeto' 
													GROUP BY nome HAVING  total_nome>1 or total_rg>1 or total_cpf>1   or  total_ctps>1  or  total_pis>1  or total_titulo>1 ");

                        $num_duplicado_coop = mysql_num_rows($qr_duplicado_cooperado);

                        if ($num_duplicado_coop != 0)
                        while ($row_duplicado_coop = mysql_fetch_assoc($qr_duplicado_cooperado)):



                        $result_atividade = mysql_query("SELECT * FROM curso where id_curso = '$row_duplicado_coop[id_curso]'");
                        $row_atividade = mysql_fetch_array($result_atividade);
                        $result_banco = mysql_query("SELECT * FROM bancos where id_banco = '$row_duplicado_coop[banco]'");
                        $row_banco = mysql_fetch_array($result_banco);

                        ///PEGA A ESCOLARIDADE
                        $qr_escolaridade = mysql_query("SELECT * FROM escolaridade WHERE id = '$row_duplicado_coop[escolaridade]';");
                        $num_escolaridade = mysql_num_rows($qr_escolaridade);

                        if ($num_escolaridade == 0) {

                        $escolaridade = $row_duplicado_coop['escolaridade'];
                        } else {

                        $row = mysql_fetch_assoc($qr_escolaridade);

                        $escolaridade = $row['nome'];
                        }
                        //////////////////////////
                        ///////PEGA ETNIAS
                        $qr_etnia = mysql_query("SELECT * FROM etnias WHERE id = '$row_duplicado_coop[etnia]';");
                        $etnia = mysql_fetch_assoc($qr_etnia);
                        /////////////////

                        $nome = str_split($row_duplicado_coop['nome'], 30);
                        $nomeT = sprintf("% -30s", $nome[0]);

                        $Atividade = str_replace("CAPACITANDO ", "CAP. ", $row_atividade['nome']);
                        $Escola = str_replace("ESCOLA ", "E. ", $row_duplicado_coop['locacao']);
                        $Escola = str_replace("MUNICIPAL ", "M. ", $Escola);
                        $Escola = str_replace("MUNICIPALIZADA ", "Mzd. ", $Escola);
                        ?>
                        <tr id="duplicado">
                            <td align="center"><?= $row_duplicado_coop['campo3'] ?></td>
                            <td align="center"><?= $nomeT ?></td>
                            <td align="center"><?= $Atividade ?></td>
                            <td align="center"><?= $Escola ?></td>
                            <td align="center"><?= 'R$ ' . valor_real($row_atividade['valor']) ?></td>
                            <td align="center"><?= data($row_duplicado_coop['data_nasci']); ?></td>
                            <td align="center"><?= $row_duplicado_coop['civil'] ?></td>
                            <td align="center"><?= $row_duplicado_coop['sexo'] ?></td>
                            <td align="center"><?= $row_duplicado_coop['nacionalidade'] ?></td>
                            <td align="center"><?= $row_duplicado_coop['endereco'] ?></td>
                            <td align="center"><?= $row_duplicado_coop['bairro'] ?></td>
                            <td align="center"><?= $row_duplicado_coop['cidade'] ?></td>
                            <td align="center"><?= $row_duplicado_coop['uf'] ?></td>
                            <td align="center"><?= $row_duplicado_coop['cep'] ?></td>
                            <td align="center"><?= $row_duplicado_coop['naturalidade'] ?></td>
                            <td align="center"><?= $row_duplicado_coop['estuda'] ?></td>
                            <td align="center"><?= data($row_duplicado_coop['data_escola']) ?></td>
                            <td align="center"><?= $escolaridade ?></td>
                            <td align="center"><?= $row_duplicado_coop['curso'] ?></td>

                            <td align="center"><?= $row_duplicado_coop['instituicao'] ?></td>
                            <td align="center"><?= $row_duplicado_coop['tel_fixo'] ?></td>
                            <td align="center"><?= $row_duplicado_coop['tel_cel'] ?></td>
                            <td align="center"><?= $row_duplicado_coop['pai'] ?></td>
                            <td align="center"><?= $row_duplicado_coop['nacionalidade_pai'] ?></td>
                            <td align="center"><?= $row_duplicado_coop['mae'] ?></td>
                            <td align="center"><?= $row_duplicado_coop['nacionalidade_mae'] ?></td>
                            <td align="center"><?= $row_duplicado_coop['num_filhos'] ?></td>
                            <td align="center"><?= $row_duplicado_coop['cabelos'] ?></td>
                            <td align="center"><?= $row_duplicado_coop['olhos'] ?></td>
                            <td align="center"><?= $row_duplicado_coop['peso'] ?></td>
                            <td align="center"><?= $row_duplicado_coop['altura'] ?></td>
                            <td align="center"><?= $etnia['nome'] ?></td>      	
                            <td align="center"><?= $row_duplicado_coop['defeito'] ?></td>
                            <td align="center"><?= $row_duplicado_coop['deficiencia'] ?></td>
                            <td align="center"><?= $row_duplicado_coop['cpf'] ?></td> 
                            <td align="center"><?= $row_duplicado_coop['serie_ctps'] ?></td>
                            <td align="center"><?= $row_duplicado_coop['uf_ctps'] ?></td>
                            <td align="center"><?= data($row_duplicado_coop['data_ctps']) ?></td>
                            <td align="center"><?= $row_duplicado_coop['reservista'] ?></td>
                            <td align="center"><?= $row_duplicado_coop['pis'] ?></td>
                            <td align="center"><?= data($row_duplicado_coop['dada_pis']) ?></td>
                            <td align="center"><?= $row_duplicado_coop['fgts'] ?></td>
                            <td align="center"><?= $row_duplicado_coop['titulo'] ?></td>
                            <td align="center"><?= $row_duplicado_coop['zona'] ?></td>
                            <td align="center"><?= $row_duplicado_coop['secao'] ?></td>
                            <td align="center"><?= $row_duplicado_coop['rg'] ?></td>
                            <td align="center"><?= $row_duplicado_coop['orgao'] ?></td>
                            <td align="center"><?= $row_duplicado_coop['uf_rg'] ?></td>
                            <td align="center"><?= data($row_duplicado_coop['data_rg']) ?></td>
                            <td align="center"><?= data($row_duplicado_coop['data_entrada']) ?></td>
                            <td align="center"><?= data($row_duplicado_coop['data_exame']) ?></td>
                            <td align="center"><?= $row_duplicado_coop['localpagamento'] ?></td>
                            <td align="center"><?= $row_duplicado_coop['observacao'] ?></td>        
                            <td align="center"><?= $row_banco['nome'] ?></td>
                            <td align="center"><?= $row_duplicado_coop['agencia'] ?></td>
                            <td align="center"><?= $row_duplicado_coop['conta'] ?></td>
                        </tr>




                        <?php
                        $nomes_duplicados[] = trim($row_duplicado_coop['nome']);
                        $rg_duplicados[] = trim($row_duplicado_coop['rg']);
                        $cpf_duplicados[] = trim($row_duplicado_coop['cpf']);
                        $ctps_duplicados[] = trim($row_duplicado_coop['serie_ctps']);
                        $pis_duplicados[] = trim($row_duplicado_coop['pis']);
                        $titulo_duplicados[] = trim($row_duplicado_coop['titulo']);
                        endwhile;
                        /////////////////////////////////////////////////////  	FIM DUPLICADO     ////////////////////////////////////////////////////		






                        while ($row_cooperado = mysql_fetch_array($result_cooperado)) {


                        ///	CONDIÇÃO PARA NÃO EXIBIR OS REGISTROS DUPLICADOS		  
                        if ($num_duplicado_coop != 0) {

                        if (in_array(trim($row_cooperado['nome']), $nomes_duplicados) or in_array(trim($row_cooperado['rg']), $rg_duplicados) or in_array(trim($row_cooperado['cpf']), $cpf_duplicados) or in_array(trim($row_cooperado['serie_ctps']), $ctps_duplicados) or in_array(trim($row_cooperado['pis']), $pis_duplicados) or in_array(trim($row_cooperado['titulo']), $titulo_duplicados))
                        continue;
                        }

                        $qr_escolaridade = mysql_query("SELECT * FROM escolaridade WHERE id = '$row_cooperado[escolaridade]';");
                        $num_escolaridade = mysql_num_rows($qr_escolaridade);

                        if ($num_escolaridade == 0) {

                        $escolaridade = $row_cooperado['escolaridade'];
                        } else {

                        $row = mysql_fetch_assoc($qr_escolaridade);

                        $escolaridade = $row['nome'];
                        }



                        ///////PEGA ETNIAS

                        $qr_etnia = mysql_query("SELECT * FROM etnias WHERE id = '$row_cooperado[etnia]';");
                        $etnia = mysql_fetch_assoc($qr_etnia);


                        /////////////////

                        $result_atividade3 = mysql_query("SELECT * FROM curso where id_curso = '$row_cooperado[id_curso]'");
                        $row_atividade3 = mysql_fetch_array($result_atividade3);
                        $result_banco3 = mysql_query("SELECT * FROM bancos where id_banco = '$row_cooperado[banco]'");
                        $row_banco3 = mysql_fetch_array($result_banco3);
                        ?>

                        <tr class="<?php
                        if ($alternateColor++% 2 == 0) {
                        echo "linha_um";
                        } else {
                        echo "linha_dois";
                        }
                        ?>" >



                            <td align="center"><?= $row_cooperado['campo3'] ?></td>
                            <td align="center"><?= $row_cooperado['nome'] ?></td>
                            <td align="center"><?= $row_atividade3['nome'] ?></td>
                            <td align="center"><?= $row_cooperado['locacao'] ?></td>
                            <td align="center"><?= 'R$ ' . valor_real($row_atividade['valor']) ?></td>
                            <td align="center"><?= data($row_cooperado['data_nasci']); ?></td>
                            <td align="center"><?= $row_cooperado['civil'] ?></td>
                            <td align="center"><?= $row_cooperado['sexo'] ?></td>
                            <td align="center"><?= $row_cooperado['nacionalidade'] ?></td>
                            <td align="center"><?= $row_cooperado['endereco'] ?></td>
                            <td align="center"><?= $row_cooperado['bairro'] ?></td>
                            <td align="center"><?= $row_cooperado['cidade'] ?></td>
                            <td align="center"><?= $row_cooperado['uf'] ?></td>
                            <td align="center"><?= $row_cooperado['cep'] ?></td>
                            <td align="center"><?= $row_cooperado['naturalidade'] ?></td>
                            <td align="center"><?= $row_cooperado['estuda'] ?></td>
                            <td align="center"><?= data($row_cooperado['data_escola']) ?></td>
                            <td align="center"><?= $escolaridade ?></td>
                            <td align="center"><?= $row_cooperado['curso'] ?></td>
                            <td align="center"><?= $row_cooperado['instituicao'] ?></td>
                            <td align="center"><?= $row_cooperado['tel_fixo'] ?></td>
                            <td align="center"><?= $row_cooperado['tel_cel'] ?></td>
                            <td align="center"><?= $row_cooperado['pai'] ?></td>
                            <td align="center"><?= $row_cooperado['nacionalidade_pai'] ?></td>
                            <td align="center"><?= $row_cooperado['mae'] ?></td>
                            <td align="center"><?= $row_cooperado['nacionalidade_mae'] ?></td>
                            <td align="center"><?= $row_cooperado['num_filhos'] ?></td>
                            <td align="center"><?= $row_cooperado['cabelos'] ?></td>
                            <td align="center"><?= $row_cooperado['olhos'] ?></td>
                            <td align="center"><?= $row_cooperado['peso'] ?></td>
                            <td align="center"><?= $row_cooperado['altura'] ?></td>
                            <td align="center"><?= $etnia['nome'] ?></td>      	
                            <td align="center"><?= $row_cooperado['defeito'] ?></td>
                            <td align="center"><?= $row_cooperado['deficiencia'] ?></td>
                            <td align="center"><?= $row_cooperado['cpf'] ?></td> 
                            <td align="center"><?= $row_cooperado['serie_ctps'] ?></td>
                            <td align="center"><?= $row_cooperado['uf_ctps'] ?></td>
                            <td align="center"><?= data($row_cooperado['data_ctps']) ?></td>
                            <td align="center"><?= $row_cooperado['reservista'] ?></td>
                            <td align="center"><?= $row_cooperado['pis'] ?></td>
                            <td align="center"><?= data($row_cooperado['dada_pis']) ?></td>
                            <td align="center"><?= $row_cooperado['fgts'] ?></td>
                            <td align="center"><?= $row_cooperado['titulo'] ?></td>
                            <td align="center"><?= $row_cooperado['zona'] ?></td>
                            <td align="center"><?= $row_cooperado['secao'] ?></td>
                            <td align="center"><?= $row_cooperado['rg'] ?></td>
                            <td align="center"><?= $row_cooperado['orgao'] ?></td>
                            <td align="center"><?= $row_cooperado['uf_rg'] ?></td>
                            <td align="center"><?= data($row_cooperado['data_rg']) ?></td>
                            <td align="center"><?= data($row_cooperado['data_entrada']) ?></td>
                            <td align="center"><?= data($row_cooperado['data_exame']) ?></td>
                            <td align="center"><?= $row_cooperado['localpagamento'] ?></td>
                            <td align="center"><?= $row_cooperado['observacao'] ?></td>        
                            <td align="center"><?= $row_banco['nome'] ?></td>
                            <td align="center"><?= $row_cooperado['agencia'] ?></td>
                            <td align="center"><?= $row_cooperado['conta'] ?></td>
                        </tr>


                        <?php
                        }



                        unset($nomes_duplicados, $rg_duplicados, $cpf_duplicados, $titulo_duplicados, $pis_duplicados, $ctps_duplicados);
                        ?>

                        <tr class="secao">
                            <td colspan="56" align="left">TOTAL DE COLABORADORES: <?php echo $num_cooperado; ?></td>
                        </tr>
                    </table>

<?php } ?>

                </td>
            </tr>
            <tr>
                <td colspan="3">

                    <?php
                    ///////////////////////////////////////////////////////////////////////////////////////////// AUTONOMO PJ  //////////////////////////////////////////////////////

                    if (!empty($num_pj)) {
                    ?>

                    <div class="descricao" style="text-align:left; font-weight:bold;">Relat&oacute;rio de Autônomo / PJ do Projeto em Ordem Alfabética</div> 
                    <table class="relacao" width="100%" cellpadding="3" cellspacing="1"  style="margin-left:30px;margin-right:30px;">
                        <tr class="secao">
                            <td align="center">Cod.</td> 
                            <td align="center">Nome</td> 
                            <td align="center">Atividade</td>
                            <td align="center">Unidade</td>  
                            <td align="center"width="100">Salário</td>
                            <td align="center"width="140">Data de Nascimento</td>
                            <td align="center">Estado Civil</td>
                            <td align="center">Sexo</td>
                            <td align="center">Nacionalidade</td>
                            <td align="center">Endereço</td>
                            <td align="center">Bairro</td>
                            <td align="center">Cidade</td>
                            <td align="center">Uf</td>
                            <td align="center">CEP</td>
                            <td align="center">Naturalidade</td>
                            <td align="center">Estudante</td>
                            <td align="center">Terminou em</td>
                            <td align="center">Escolaridade</td>
                            <td align="center">Tipo de Formação(curso)</td>
                            <td align="center">Instituição de ensino</td> 
                            <td align="center">Telefone Fixo</td>
                            <td align="center">Celular</td>
                            <td align="center">Pai</td>
                            <td align="center">Nacionalidade do pai</td>
                            <td align="center">Mãe</td>
                            <td align="center">Nacionalidade da mãe</td>
                            <td align="center">Número de filhos</td>
                            <td align="center">Cabelos</td>
                            <td align="center">Olhos</td>
                            <td align="center">Peso</td>
                            <td align="center">Altura</td>
                            <td align="center">Etnia</td>  
                            <td align="center">Marcas ou Cicatriz</td> 
                            <td align="center">Deficiência:</td>
                            <td align="center">CPF</td>
                            <td align="center">Série (CTPS)</td>
                            <td align="center">UF (CTPS)</td>          
                            <td align="center">Data da carteira de trabalho</td>
                            <td align="center">Certificado de reservista</td>
                            <td align="center">PIS</td>
                            <td align="center">Data do PIS</td>
                            <td align="center">FGTS</td>
                            <td align="center">Título de eleitor</td>
                            <td align="center">Zona</td>
                            <td align="center">Seção</td>             
                            <td align="center">RG</td>             
                            <td align="center">Orgão expedidor (RG)</td>
                            <td align="center">UF (RG)</td>
                            <td align="center">Data de expedição (RG)</td>             
                            <td align="center">Data de entrada</td>
                            <td align="center">Data do exame admissional</td>             
                            <td align="center">Local de pagamento</td>
                            <td align="center">Observações</td>           
                            <td align="center">Banco</td>
                            <td align="center">Agência</td>
                            <td align="center">C.C.</td>
                        </tr>

                        <?php
                        $qr_duplicado_pj = mysql_query("SELECT id_autonomo,
												     nome,
													 rg,
													 pis,
													 cpf,
													 serie_ctps,
													 titulo,
													 COUNT(nome) as total_nome, 
													COUNT(rg) as total_rg, 
													COUNT(cpf) as total_cpf ,
													COUNT(serie_ctps) as total_ctps,
													COUNT(pis) as total_pis,
													COUNT(titulo) as total_titulo						
													FROM autonomo
																								
													WHERE status = '1'  AND tipo_contratacao ='4' AND id_regiao ='$regiao' 
													GROUP BY nome HAVING  total_nome>1 or total_rg>1 or total_cpf>1   or  total_ctps>1  or  total_pis>1  or total_titulo>1 ");
                        $num_duplicado_pj = mysql_num_rows($qr_duplicado_pj);
                        $row_duplicado_pj = mysql_fetch_assoc($qr_duplicado_pj);

                        if ($num_duplicado_pj != 0)
                        while ($row_duplicado_pj = mysql_fetch_assoc($qr_duplicado_pj)):



                        $result_atividade = mysql_query("SELECT * FROM curso where id_curso = '$row_duplicado_pj[id_curso]'");
                        $row_atividade = mysql_fetch_array($result_atividade);
                        $result_banco = mysql_query("SELECT * FROM bancos where id_banco = '$row_duplicado_pj[banco]'");
                        $row_banco = mysql_fetch_array($result_banco);

                        ///PEGA A ESCOLARIDADE
                        $qr_escolaridade = mysql_query("SELECT * FROM escolaridade WHERE id = '$row_duplicado_pj[escolaridade]';");
                        $num_escolaridade = mysql_num_rows($qr_escolaridade);

                        if ($num_escolaridade == 0) {

                        $escolaridade = $row_duplicado_pj['escolaridade'];
                        } else {

                        $row = mysql_fetch_assoc($qr_escolaridade);

                        $escolaridade = $row['nome'];
                        }
                        //////////////////////////
                        ///////PEGA ETNIAS
                        $qr_etnia = mysql_query("SELECT * FROM etnias WHERE id = '$row_duplicado_pj[etnia]';");
                        $etnia = mysql_fetch_assoc($qr_etnia);
                        /////////////////

                        $nome = str_split($row_duplicado_pj['nome'], 30);
                        $nomeT = sprintf("% -30s", $nome[0]);

                        $Atividade = str_replace("CAPACITANDO ", "CAP. ", $row_atividade['nome']);
                        $Escola = str_replace("ESCOLA ", "E. ", $row_duplicado_pj['locacao']);
                        $Escola = str_replace("MUNICIPAL ", "M. ", $Escola);
                        $Escola = str_replace("MUNICIPALIZADA ", "Mzd. ", $Escola);
                        ?>
                        <tr id="duplicado">
                            <td align="center"><?= $row_duplicado_pj['campo3'] ?></td>
                            <td align="center"><?= $nomeT ?></td>
                            <td align="center"><?= $Atividade ?></td>
                            <td align="center"><?= $Escola ?></td>
                            <td align="center"><?= 'R$ ' . valor_real($row_atividade['valor']) ?></td>
                            <td align="center"><?= data($row_duplicado_pj['data_nasci']); ?></td>
                            <td align="center"><?= $row_duplicado_pj['civil'] ?></td>
                            <td align="center"><?= $row_duplicado_pj['sexo'] ?></td>
                            <td align="center"><?= $row_duplicado_pj['nacionalidade'] ?></td>
                            <td align="center"><?= $row_duplicado_pj['endereco'] ?></td>
                            <td align="center"><?= $row_duplicado_pj['bairro'] ?></td>
                            <td align="center"><?= $row_duplicado_pj['cidade'] ?></td>
                            <td align="center"><?= $row_duplicado_pj['uf'] ?></td>
                            <td align="center"><?= $row_duplicado_pj['cep'] ?></td>
                            <td align="center"><?= $row_duplicado_pj['naturalidade'] ?></td>
                            <td align="center"><?= $row_duplicado_pj['estuda'] ?></td>
                            <td align="center"><?= data($row_duplicado_pj['data_escola']) ?></td>
                            <td align="center"><?= $escolaridade ?></td>
                            <td align="center"><?= $row_duplicado_pj['curso'] ?></td>

                            <td align="center"><?= $row_duplicado_pj['instituicao'] ?></td>
                            <td align="center"><?= $row_duplicado_pj['tel_fixo'] ?></td>
                            <td align="center"><?= $row_duplicado_pj['tel_cel'] ?></td>
                            <td align="center"><?= $row_duplicado_pj['pai'] ?></td>
                            <td align="center"><?= $row_duplicado_pj['nacionalidade_pai'] ?></td>
                            <td align="center"><?= $row_duplicado_pj['mae'] ?></td>
                            <td align="center"><?= $row_duplicado_pj['nacionalidade_mae'] ?></td>
                            <td align="center"><?= $row_duplicado_pj['num_filhos'] ?></td>
                            <td align="center"><?= $row_duplicado_pj['cabelos'] ?></td>
                            <td align="center"><?= $row_duplicado_pj['olhos'] ?></td>
                            <td align="center"><?= $row_duplicado_pj['peso'] ?></td>
                            <td align="center"><?= $row_duplicado_pj['altura'] ?></td>
                            <td align="center"><?= $etnia['nome'] ?></td>      	
                            <td align="center"><?= $row_duplicado_pj['defeito'] ?></td>
                            <td align="center"><?= $row_duplicado_pj['deficiencia'] ?></td>
                            <td align="center"><?= $row_duplicado_pj['cpf'] ?></td> 
                            <td align="center"><?= $row_duplicado_pj['serie_ctps'] ?></td>
                            <td align="center"><?= $row_duplicado_pj['uf_ctps'] ?></td>
                            <td align="center"><?= data($row_duplicado_pj['data_ctps']) ?></td>
                            <td align="center"><?= $row_duplicado_pj['reservista'] ?></td>
                            <td align="center"><?= $row_duplicado_pj['pis'] ?></td>
                            <td align="center"><?= data($row_duplicado_pj['dada_pis']) ?></td>
                            <td align="center"><?= $row_duplicado_pj['fgts'] ?></td>
                            <td align="center"><?= $row_duplicado_pj['titulo'] ?></td>
                            <td align="center"><?= $row_duplicado_pj['zona'] ?></td>
                            <td align="center"><?= $row_duplicado_pj['secao'] ?></td>
                            <td align="center"><?= $row_duplicado_pj['rg'] ?></td>
                            <td align="center"><?= $row_duplicado_pj['orgao'] ?></td>
                            <td align="center"><?= $row_duplicado_pj['uf_rg'] ?></td>
                            <td align="center"><?= data($row_duplicado_pj['data_rg']) ?></td>
                            <td align="center"><?= data($row_duplicado_pj['data_entrada']) ?></td>
                            <td align="center"><?= data($row_duplicado_pj['data_exame']) ?></td>
                            <td align="center"><?= $row_duplicado_pj['localpagamento'] ?></td>
                            <td align="center"><?= $row_duplicado_pj['observacao'] ?></td>        
                            <td align="center"><?= $row_banco['nome'] ?></td>
                            <td align="center"><?= $row_duplicado_pj['agencia'] ?></td>
                            <td align="center"><?= $row_duplicado_pj['conta'] ?></td>
                        </tr>




                        <?php
                        $nomes_duplicados[] = trim($row_duplicado_pj['nome']);
                        $rg_duplicados[] = trim($row_duplicado_pj['rg']);
                        $cpf_duplicados[] = trim($row_duplicado_pj['cpf']);
                        $ctps_duplicados[] = trim($row_duplicado_pj['serie_ctps']);
                        $pis_duplicados[] = trim($row_duplicado_pj['pis']);
                        $titulo_duplicados[] = trim($row_duplicado_pj['titulo']);
                        endwhile;
                        /////////////////////////////////////////////////////  	FIM DUPLICADO     ////////////////////////////////////////////////////		


                        while ($row_pj = mysql_fetch_array($result_pj)) {


                        ///	CONDIÇÃO PARA NÃO EXIBIR OS REGISTROS DUPLICADOS		  
                        if ($num_duplicado_pj != 0) {

                        if (in_array(trim($row_pj['nome']), $nomes_duplicados) or in_array(trim($row_pj['rg']), $rg_duplicados) or in_array(trim($row_pj['cpf']), $cpf_duplicados) or in_array(trim($row_pj['serie_ctps']), $ctps_duplicados) or in_array(trim($row_pj['pis']), $pis_duplicados) or in_array(trim($row_pj['titulo']), $titulo_duplicados))
                        continue;
                        }


                        $qr_escolaridade = mysql_query("SELECT * FROM escolaridade WHERE id = '$row_pj[escolaridade]';");
                        $num_escolaridade = mysql_num_rows($qr_escolaridade);

                        if ($num_escolaridade == 0) {

                        $escolaridade = $row_pj['escolaridade'];
                        } else {

                        $row = mysql_fetch_assoc($qr_escolaridade);

                        $escolaridade = $row['nome'];
                        }


                        ///////PEGA ETNIAS		   
                        $qr_etnia = mysql_query("SELECT * FROM etnias WHERE id = '$row_pj[etnia]';");
                        $etnia = mysql_fetch_assoc($qr_etnia);


                        /////////////////
                        $result_atividade4 = mysql_query("SELECT * FROM curso where id_curso = '$row_pj[id_curso]'");
                        $row_atividade4 = mysql_fetch_array($result_atividade4);
                        $result_banco4 = mysql_query("SELECT * FROM bancos where id_banco = '$row_pj[banco]'");
                        $row_banco4 = mysql_fetch_array($result_banco4);
                        ?>

                        <tr class="<?php
                        if ($alternateColor++% 2 == 0) {
                        echo "linha_um";
                        } else {
                        echo "linha_dois";
                        }
                        ?>" >




                            <td align="center"><?= $row_cooperado['campo3'] ?></td>
                            <td align="center"><?= $row_pj['nome'] ?></td>
                            <td align="center"><?= $row_atividade4['nome'] ?></td>
                            <td align="center"><?= $row_pj['locacao'] ?></td>
                            <td align="center"><?= 'R$ ' . valor_real($row_atividade['valor']) ?></td>
                            <td align="center"><?= data($row_pj['data_nasci']); ?></td>
                            <td align="center"><?= $row_pj['civil'] ?></td>
                            <td align="center"><?= $row_pj['sexo'] ?></td>
                            <td align="center"><?= $row_pj['nacionalidade'] ?></td>
                            <td align="center"><?= $row_pj['endereco'] ?></td>
                            <td align="center"><?= $row_pj['bairro'] ?></td>
                            <td align="center"><?= $row_pj['cidade'] ?></td>
                            <td align="center"><?= $row_pj['uf'] ?></td>
                            <td align="center"><?= $row_pj['cep'] ?></td>
                            <td align="center"><?= $row_pj['naturalidade'] ?></td>
                            <td align="center"><?= $row_pj['estuda'] ?></td>
                            <td align="center"><?= data($row_pj['data_escola']) ?></td>
                            <td align="center"><?= $escolaridade ?></td>
                            <td align="center"><?= $row_pj['curso'] ?></td>
                            <td align="center"><?= $row_pj['instituicao'] ?></td>
                            <td align="center"><?= $row_pj['tel_fixo'] ?></td>
                            <td align="center"><?= $row_pj['tel_cel'] ?></td>
                            <td align="center"><?= $row_pj['pai'] ?></td>
                            <td align="center"><?= $row_pj['nacionalidade_pai'] ?></td>
                            <td align="center"><?= $row_pj['mae'] ?></td>
                            <td align="center"><?= $row_pj['nacionalidade_mae'] ?></td>
                            <td align="center"><?= $row_pj['num_filhos'] ?></td>
                            <td align="center"><?= $row_pj['cabelos'] ?></td>
                            <td align="center"><?= $row_pj['olhos'] ?></td>
                            <td align="center"><?= $row_pj['peso'] ?></td>
                            <td align="center"><?= $row_pj['altura'] ?></td>
                            <td align="center"><?= $etnia['nome'] ?></td>      	
                            <td align="center"><?= $row_pj['defeito'] ?></td>
                            <td align="center"><?= $row_pj['deficiencia'] ?></td>
                            <td align="center"><?= $row_pj['cpf'] ?></td> 
                            <td align="center"><?= $row_pj['serie_ctps'] ?></td>
                            <td align="center"><?= $row_pj['uf_ctps'] ?></td>
                            <td align="center"><?= data($row_pj['data_ctps']) ?></td>
                            <td align="center"><?= $row_pj['reservista'] ?></td>
                            <td align="center"><?= $row_pj['pis'] ?></td>
                            <td align="center"><?= data($row_pj['dada_pis']) ?></td>
                            <td align="center"><?= $row_pj['fgts'] ?></td>
                            <td align="center"><?= $row_pj['titulo'] ?></td>
                            <td align="center"><?= $row_pj['zona'] ?></td>
                            <td align="center"><?= $row_pj['secao'] ?></td>
                            <td align="center"><?= $row_pj['rg'] ?></td>
                            <td align="center"><?= $row_pj['orgao'] ?></td>
                            <td align="center"><?= $row_pj['uf_rg'] ?></td>
                            <td align="center"><?= data($row_pj['data_rg']) ?></td>
                            <td align="center"><?= data($row_pj['data_entrada']) ?></td>
                            <td align="center"><?= data($row_pj['data_exame']) ?></td>
                            <td align="center"><?= $row_pj['localpagamento'] ?></td>        
                            <td align="center"><?= $row_pj['observacao'] ?></td>        
                            <td align="center"><?= $row_banco['nome'] ?></td>
                            <td align="center"><?= $row_pj['agencia'] ?></td>
                            <td align="center"><?= $row_pj['conta'] ?></td>
                        </tr>

<?php } ?>

                        <tr class="secao">
                            <td colspan="56  " align="left">TOTAL DE AUTÔNOMO / PJ: <?php echo $num_pj; ?></td>
                        </tr>
                    </table>

<?php } ?>
                </td>
            </tr>
        </table>
    </body>
</html>
<?php } ?>
<!-- Vasco, Botafogo -- >