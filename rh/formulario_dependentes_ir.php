<?php
if (empty($_COOKIE['logado'])) {
    print "Efetue o Login<br><a href='login.php'>Logar</a>";
    exit;
} else {
    include('../conn.php');
    include('../funcoes.php');
    include('../upload/classes.php');
    include('../classes/funcionario.php');
    include('../classes/formato_data.php');
    include('../classes/formato_valor.php');
    include('../classes_permissoes/acoes.class.php');
    
    $id_clt = intval($_GET["clt"]);
    $id_projeto = intval($_GET["pro"]);
    $id_regiao = intval($_GET["reg"]);
    
    $query = "select * ";
    $query .= "from rh_clt ";
    $query .= "where (id_clt = $id_clt or id_antigo = $id_clt) and id_projeto = $id_projeto ";
    
    $resultados = mysql_query($query);
    
    $resultado = mysql_fetch_assoc($resultados);
    
    $query_dependentes = "select * ";
    $query_dependentes .= "from dependentes ";
    $query_dependentes .= "where id_bolsista = $id_clt and id_regiao = $id_regiao and contratacao = {$resultado['tipo_contratacao']} ";
    
    $resultados_dependentes = mysql_query($query_dependentes);
    
    $dependente = mysql_fetch_assoc($resultados_dependentes);
}
?>
<html>
    <head>
        <meta http-equiv=Content-Type content="text/html; charset=iso-8859-1">
        <title>Formulário de Declaração de Dependentes</title>
        <link href="../resources/css/bootstrap.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet" type="text/css">
        <link href="../relatorios/css/estrutura.css" rel="stylesheet" type="text/css">
        <link href="../resources/css/font-awesome.min.css" rel="stylesheet">
        <link href="../resources/css/style-print.css" rel="stylesheet">
        <script src="../js/jquery-1.10.2.min.js" type="text/javascript"></script>
        <script src="../resources/js/print.js" type="text/javascript"></script>
        <style>
            #content {
                width: 800px;
                margin: 0 auto;
            }

            .logo {
                float: left;
                margin: 5px 0 0 5px;
            }

            #title {
                float: left;
                padding: 20px 0 0 25px;
            }

            hr {
                border: none;
            //   border-top: 1px solid #333;
            }

            .fleft {
                float: left;
            }

            .fright {
                float: right;
            }

            .box {
                border: 2px solid #000;
                padding: 10px;
                margin-top: 10px;
            }

            .txright {
                text-align: right;
            }

            .txcenter {
                text-align: center;
            }

            .legenda {
                font-size: 10px;
                padding: 0;
                margin: 0;
                float: left;
            }

            .clear {
                clear: both;
                padding: 0;
                margin: 0;
                line-height: 16px;
            }

            .txleft {
                text-align: left;
            }

            table {
                width: 100%;
            //border-left: 1px solid #333;
                margin-bottom: 10px !important;
            }

            td {
                padding: 1px 5px;
            //    border-right: 1px solid #333;
            //   border-bottom: 1px solid #333;
            }

            td.bl {
                border-left: none !important;
            }

            tr.bf td {
                border-bottom: none !important;
            }

            tr.bt td {
                border-top: none !important;
            }

            p {
                font-size: 13px;
                padding: 5px;
            }

            table thead tr th {
                font-size: 14px;
                font-weight: bold;
            }

            table tbody tr td {
                padding: 1px 5px;
                font-size: 13px !important;
            }

            table.grid {
                border-top: 1px solid #333;
            //     border-left: 1px solid #333;
            }

            table.grid tr td {
            //  border-bottom: 1px solid #333;
            // border-right: 1px solid #333;
            }

            table.grid tr th {
            //  border-bottom: 1px solid #333;
                background: #F0F0F0;
            }

            table.grid tr th:last-child {
            //  border-right: 1px solid #333;
            }

            #sigilo {
                width: 200px !important;
                float: right;
            }

            span {
            //  color: red;
            }
        </style>
    </head>
    <body>
    <nav class="navbar navbar-default navbar-fixed-top">
        <div class="container-fluid">
            <div class="text-center">
                <button type="button" id="imprimir" class="btn btn-success navbar-btn"><i class="fa fa-print"></i>
                    Imprimir
                </button>
                <a href="../" class="btn btn-info navbar-btn"><i class="fa fa-home"></i> Principal</a>
            </div>
        </div>
    </nav>
        <div id="content" class="pagina">
            <div id="pessoal" class="box">
                
                <div class="cabecalho">
                    FORMULÁRIO DE DECLARAÇÃO DE DEPENDENTES PARA FINS DE IMPOSTO DE RENDA
                </div>
                
                <table>
                    <tr>
                        <td colspan="6" class="b">
                            <p class="legenda">Nome do Declarante</p>
                            <p class="clear"> <?php echo $resultado['nome'] ?></p>
                        </td>
                    </tr>
                    <tr class="bt">
                        <td colspan="4" class="b">
                            <p class="legenda">C.P.F.</p>
                            <p class="clear"> <?php echo $resultado['cpf'] ?></p>
                        </td>
                        <td colspan="2" class="b bl">
                            <p class="legenda">Estado Civil</p>
                            <p class="clear"> <?php echo $resultado['civil'] ?></p>
                        </td>
                    </tr>
                    <tr class="bt">
                        <td colspan="4" class="b">
                            <p class="legenda">Endereço:</p>
                            <p class="clear"> <?php echo $resultado['endereco']?></p>
                        </td>
                        <td colspan="2" class="b bl">
                            <p class="legenda">CEP:</p>
                            <p class="clear"> <?php echo $resultado['cep'] ?></p>
                        </td>
                    </tr>
                    <tr class="bt">
                        <td colspan="2" class="b">
                            <p class="legenda">Bairro:</p>
                            <p class="clear"> <?php echo $resultado['bairro'] ?></p>
                        </td>
                        <td colspan="2" class="b bl">
                            <p class="legenda">Cidade:</p>
                            <p class="clear"> <?php echo $resultado['cidade'] ?></p>
                        </td>
                        <td colspan="1" class="b bl">
                            <p class="legenda">Telefone</p>
                            <p class="clear"> <?php echo $resultado['tel_fixo'] ?></p>
                        </td>
                    </tr>
                    <?php
                        for($cont = 1; $cont <= 6; $cont++) {
                            if(!empty($dependente["nome".$cont]))
                            {
                                $dia = substr($dependente["data".$cont], 8, 2);
                                $mes = substr($dependente["data".$cont], 5, 2);
                                $ano = substr($dependente["data".$cont], 0, 4);
                                $data = $dia . "/" . $mes . "/" . $ano;
                                if($ano >= (date("Y") - 21) || $mes <= date("m") || $dia < date("d"))
                                {
                    ?>
                        <tr>
                            <td colspan="2" class="b">
                                <p class="legenda">Nome completo dos Dependentes:</p>
                                <p class="clear"><?php echo $dependente["nome".$cont] ?></p>
                            </td>
                            <td colspan="2" class="b bl">
                                <p class="legenda">Relação Dependência:</p>
                                <p class="clear"> Filho </p>
                            </td>
                            <td colspan="1" class="b bl">
                                <p class="legenda">Data Nascimento:</p>
                                <p class="clear"><?php echo $data; ?></p>
                            </td>
                        </tr>
                    <?php
                                }
                            }
                            else
                            {
                                break;
                            }
                        }
                        if(!empty($dependente["ddir_pai"])) {
                            $dia = substr($dependente["data_nasc_pai"], 8, 2);
                            $mes = substr($dependente["data_nasc_pai"], 5, 2);
                            $ano = substr($dependente["data_nasc_pai"], 0, 4);
                            $data = $dia . "/" . $mes . "/" . $ano;
                    ?>
                        <tr>
                            <td colspan="2" class="b">
                                <p class="legenda">Nome completo dos Dependentes:</p>
                                <p class="clear"><?php echo $resultado["pai"] ?></p>
                            </td>
                            <td colspan="2" class="b bl">
                                <p class="legenda">Relação Dependência:</p>
                                <p class="clear"> Pai </p>
                            </td>
                            <td colspan="1" class="b bl">
                                <p class="legenda">Data Nascimento:</p>
                                <p class="clear"><?php echo $data; ?></p>
                            </td>
                        </tr>
                        <?php
                        }
                        if(!empty($dependente["ddir_mae"])) {
                            $dia = substr($dependente["data_nasc_mae"], 8, 2);
                            $mes = substr($dependente["data_nasc_mae"], 5, 2);
                            $ano = substr($dependente["data_nasc_mae"], 0, 4);
                            $data = $dia . "/" . $mes . "/" . $ano;
                        ?>
                            <tr>
                                <td colspan="2" class="b">
                                    <p class="legenda">Nome completo dos Dependentes:</p>
                                    <p class="clear"><?php echo $resultado["mae"] ?></p>
                                </td>
                                <td colspan="2" class="b bl">
                                    <p class="legenda">Relação Dependência:</p>
                                    <p class="clear"> Mãe </p>
                                </td>
                                <td colspan="1" class="b bl">
                                    <p class="legenda">Data Nascimento:</p>
                                    <p class="clear"><?php echo $data; ?></p>
                                </td>
                            </tr>
                        <?php
                        }
                        if(!empty($dependente["ddir_conjuge"])) {
                            $dia = substr($dependente["data_nasc_conjuge"], 8, 2);
                            $mes = substr($dependente["data_nasc_conjuge"], 5, 2);
                            $ano = substr($dependente["data_nasc_conjuge"], 0, 4);
                            $data = $dia . "/" . $mes . "/" . $ano;
                        ?>
                            <tr>
                                <td colspan="2" class="b">
                                    <p class="legenda">Nome completo dos Dependentes:</p>
                                    <p class="clear"><?php echo $resultado["nome_conjuge"] ?></p>
                                </td>
                                <td colspan="2" class="b bl">
                                    <p class="legenda">Relação Dependência:</p>
                                    <p class="clear"> Cônjuge </p>
                                </td>
                                <td colspan="1" class="b bl">
                                    <p class="legenda">Data Nascimento:</p>
                                    <p class="clear"><?php echo $data; ?></p>
                                </td>
                            </tr>
                        <?php
                        }
                        if(!empty($dependente["ddir_avo_h"])) {
                            $dia = substr($dependente["data_nasc_avo_h"], 8, 2);
                            $mes = substr($dependente["data_nasc_avo_h"], 5, 2);
                            $ano = substr($dependente["data_nasc_avo_h"], 0, 4);
                            $data = $dia . "/" . $mes . "/" . $ano;
                        ?>
                            <tr>
                                <td colspan="2" class="b">
                                    <p class="legenda">Nome completo dos Dependentes:</p>
                                    <p class="clear"><?php echo $resultado["nome_avo_h"] ?></p>
                                </td>
                                <td colspan="2" class="b bl">
                                    <p class="legenda">Relação Dependência:</p>
                                    <p class="clear"> Avô </p>
                                </td>
                                <td colspan="1" class="b bl">
                                    <p class="legenda">Data Nascimento:</p>
                                    <p class="clear"><?php echo $data; ?></p>
                                </td>
                            </tr>
                        <?php
                        }
                        if(!empty($dependente["ddir_avo_m"])) {
                            $dia = substr($dependente["data_nasc_avo_m"], 8, 2);
                            $mes = substr($dependente["data_nasc_avo_m"], 5, 2);
                            $ano = substr($dependente["data_nasc_avo_m"], 0, 4);
                            $data = $dia . "/" . $mes . "/" . $ano;
                        ?>
                            <tr>
                                <td colspan="2" class="b">
                                    <p class="legenda">Nome completo dos Dependentes:</p>
                                    <p class="clear"><?php echo $resultado["nome_avo_m"] ?></p>
                                </td>
                                <td colspan="2" class="b bl">
                                    <p class="legenda">Relação Dependência:</p>
                                    <p class="clear"> Avó </p>
                                </td>
                                <td colspan="1" class="b bl">
                                    <p class="legenda">Data Nascimento:</p>
                                    <p class="clear"><?php echo $data; ?></p>
                                </td>
                            </tr>
                        <?php
                        }
                        if(!empty($dependente["ddir_bisavo_h"])) {
                            $dia = substr($dependente["data_nasc_bisavo_h"], 8, 2);
                            $mes = substr($dependente["data_nasc_bisavo_h"], 5, 2);
                            $ano = substr($dependente["data_nasc_bisavo_h"], 0, 4);
                            $data = $dia . "/" . $mes . "/" . $ano;
                        ?>
                            <tr>
                                <td colspan="2" class="b">
                                    <p class="legenda">Nome completo dos Dependentes:</p>
                                    <p class="clear"><?php echo $resultado["nome_bisavo_h"] ?></p>
                                </td>
                                <td colspan="2" class="b bl">
                                    <p class="legenda">Relação Dependência:</p>
                                    <p class="clear"> Bisavô </p>
                                </td>
                                <td colspan="1" class="b bl">
                                    <p class="legenda">Data Nascimento:</p>
                                    <p class="clear"><?php echo $data; ?></p>
                                </td>
                            </tr>
                        <?php
                        }
                        if(!empty($dependente["ddir_bisavo_m"])) {
                            $dia = substr($dependente["data_nasc_bisavo_m"], 8, 2);
                            $mes = substr($dependente["data_nasc_bisavo_m"], 5, 2);
                            $ano = substr($dependente["data_nasc_bisavo_m"], 0, 4);
                            $data = $dia . "/" . $mes . "/" . $ano;
                        ?>
                            <tr>
                                <td colspan="2" class="b">
                                    <p class="legenda">Nome completo dos Dependentes:</p>
                                    <p class="clear"><?php echo $resultado["nome_bisavo_m"] ?></p>
                                </td>
                                <td colspan="2" class="b bl">
                                    <p class="legenda">Relação Dependência:</p>
                                    <p class="clear"> Bisavó </p>
                                </td>
                                <td colspan="1" class="b bl">
                                    <p class="legenda">Data Nascimento:</p>
                                    <p class="clear"><?php echo $data; ?></p>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                </table>
            </div>

            <div>
                <p>
                    Para fins do Imposto de Renda, declaro que é ou são meu(s) dependente(s) a(s) pessoa(s) acima relacionada(s).
                </p>
                <p>
                    Declaro, por fim, que não possuo cônjuge ou companheiro(a) que já deduz referidos dependentes em seu Imposto de Renda.
                </p>
                <p>
                    Declaro, ainda, que este(s) dependente(s) vive(m) sob minha dependência econômica, visto não perceber(em) rendimentos tributáveis ou não,
                    superiores ao limite de isenção mensal de R$ 1.566,61(um mil, quinhentos e sessenta e seis reais e sessenta e um centavos),
                    conforme art. 35, inciso VI da Lei nº 9.250/95, c/c art. 1º da Lei nº 11.119/2005.
                </p>
                <p>
                    Responsabilizo-me pela exatidão e veracidade das informações declaradas, ciente de que, se falsa a declaração, ficarei sujeito às penas da lei.
                </p>
                <br />
                <br />

                    <tr>
                        <td>
                            ____________________________________________________________________________,
                        </td>
                        <br>
                        <td>
                             _____ de _____________de________.
                        </td>
                    </tr>
                <br>
                    <tr>
                        <td style="text-align: center;">
                            LOCAL
                        </td>
                    </tr>

                <br />
                <br />

                    <tr>
                        <td>
                            _____________________________________________________________________________________________
                        </td>
                    </tr>
                <br>
                    <tr>
                        <td style="text-align: center;">
                        ASSINATURA DO SERVIDOR
                        </td>
                    </tr>

                <p>
                    CÓDIGO PENAL - ART. 299: Omitir em documento público ou particular, declaração que nele devia constar,
                    ou nele inserir ou fazer declaração falsa ou diversa da que devia ser escrita, com o fim de prejudicar direito,
                    criar obrigação ou alterar a verdade sobre o fato juridicamente relevante. Pena reclusão, de 1 5 (cinco) anos.
                </p>
                
                <p>
                    Obs. - Anexar documento(s) comprobatório(s)
                </p>
            </div>
        </div>
    </body>
</html>