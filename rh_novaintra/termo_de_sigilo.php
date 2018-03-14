<?php
if (!isset($_COOKIE['logado'])) {
    header("Location: /intranet/login.php?entre=true");
    exit;
}
include('../conn.php');
include('../empresa.php');
include('../wfunction.php');
include('../classes/regiao.php');
include('../classes/LogClass.php');

$usuario = carregaUsuario();

$id_clt = $_REQUEST['id_clt'];

//SELECT * FROM master WHERE id_master = {$usuario['id_master']} LIMIT 1

$arrClt = montaQuery("rh_clt A LEFT JOIN curso B ON A.id_curso = B.id_curso LEFT JOIN unidade C ON A.id_unidade = C.id_unidade LEFT JOIN rhempresa D ON A.id_projeto = D.id_projeto LEFT JOIN projeto E ON A.id_projeto = E.id_projeto", "A.*, B.nome curso, C.unidade, D.razao as e_razao, D.cnpj as e_cnpj, D.endereco as e_endereco, D.numero as e_numero, D.complemento as e_complemento, D.bairro as e_bairro, D.cidade as e_cidade, D.uf as e_uf, E.nome as projeto_setor, E.endereco as projeto_end, E.complemento as projeto_compl, E.bairro as projeto_bairro, E.cidade as projeto_cidade, E.estado as projeto_uf, E.cep as projeto_cep, E.id_projeto as projeto_id", "A.id_clt = '$id_clt'");
//print_r($arrClt);
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="ISO-8859-9">
        <title>:: Intranet ::</title>
        <link href="../favicon.png" rel="shortcut icon">
        <link href="../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../resources/css/style-print.css" rel="stylesheet" media="all">
    </head>
    <body class="font11">
        <div class="no-print">
            <nav class="navbar navbar-default navbar-fixed-top">
                <div class="container-fluid">
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-3">
                        <div class="text-center">
                            <button type="button" id="voltar" class="btn btn-default navbar-btn" onclick="window.close()"><i class="fa fa-reply"></i> Voltar</button>
                            <button type="button" id="imprimir" class="btn btn-success navbar-btn"><i class="fa fa-print"></i> Imprimir</button>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
        <div class="pagina">
            <br />
            <br />
            <p class="text-justify"><strong>TERMO DE SIGILO E CONFIDENCIALIDADE - ANEXO AO CONTRATO DE TRABALHO</strong></p>
            <p class="text-justify">
                EMPREGADO: <?= $arrClt[1]['nome'] ?> já qualificado no Contrato de Trabalho, doravante denominado simplesmente EMPREGADO.
            </p>
            <p class="text-justify">
                EMPREGADOR: <?= $arrClt[1]['e_razao'] ?>, Pessoa jurídica de direito privado, regularmente inscrita no CNPJ (MF) sob o nº <?= $arrClt[1]['e_cnpj'] ?>, sediada à <?= $arrClt[1]['e_endereco'] ?><?= (!empty($arrClt[1]['e_numero'])) ? ', '.$arrClt[1]['e_numero'] : ''  ?><?= (!empty($arrClt[1]['e_complemento'])) ? ', '.$arrClt[1]['e_complemento'] : ''  ?><?= (!empty($arrClt[1]['e_bairro'])) ? ', '.$arrClt[1]['e_bairro'] : ''  ?><?= (!empty($arrClt[1]['e_cidade'])) ? ', '.$arrClt[1]['e_cidade'] : ''  ?><?= (!empty($arrClt[1]['e_uf'])) ? ' - '.$arrClt[1]['e_uf'] : ''  ?>, doravante denominada simplesmente EMPREGADOR.
            </p>
            <p class="text-justify">
                Sempre que em conjunto referidas, doravante denominada (s) como PARTE(S).
            </p>
            <p class="text-justify">
                CONSIDERANDO que, em razão do contrato de trabalho celebrado entre as PARTES, doravante denominado CONTRATO, as mesmas terão acesso a informações confidenciais, as quais se constituem informação comercial confidencial.
            </p>
            <p class="text-justify">
                CONSIDERANDO que as PARTES desejam ajustar as condições de revelação destas informações confidenciais já disponibilizadas e aquelas que no futuro serão disponibilizadas para a execução do CONTRATO, bem como definir as regras relativas ao seu uso e proteção:
            </p>
            <p class="text-justify">
                CONSIDERANDO que as PARTES declaram-se conhecedoras do art.482, "c", "g" da CLT:
            </p>
            <div class="row">
                <div class="col-xs-2"></div>
                <div class="col-xs-10">
                    <p class="text-justify">
                        Art. 482. Constituem justa causa para rescisão do contrato de trabalho pelo empregador:<br />
                        (...) <em>omissis</em><br />
                        c) negociação habitual por conta própria ou alheia sem permissão do empregador, e quando construir ato de concorrência à empresa para a qual trabalha o empregado, ou for prejudicial ao serviço;<br />
                        (...) <em>omissis</em><br />
                        g) violação de segredo da empresa;
                    </p>
                </div>
            </div>
            <p class="text-left assinatura small">
                Esta lauda é parte integrante do Termo de Sigilo e Confidencialidade.
            </p>
            <p class="text-justify">
                RESOLVEM AS PARTES acima qualificadas, celebrar o presente TERMO DE CONFIDENCIALIDADE, mediante as cláusulas e condições que seguem:
            </p>
            <p class="text-justify">
                1. CLÁUSULA PRIMEIRA - DO OBJETO
            </p>
            <p class="text-justify">
                O objeto deste Termo é prover a necessária e adequada proteção das informações confidenciais fornecidas pelo EMPREGADOR, ou pelo seus Clientes ao EMPREGADO, em razão do CONTRATO, a fim de que as mesmas possam desenvolver as atividades contempladas no CONTRATO, o qual vincular-se-á expressamente a este.
            </p>
            <div class="row">
                <div class="col-xs-1"></div>
                <div class="col-xs-11">
                    <p class="text-justify">
                        1.1.  As estipulações e obrigações constantes do presente instrumento serão aplicadas a toda e qualquer informação que seja revelada pela EMPREGADOR ou pelo seus Clientes.
                    </p>
                </div>
            </div>
            <br>
            <p class="text-justify">
                2.  CLÁUSULA SEGUNDA - DAS INFORMAÇÕES CONFIDENCIAIS
            </p>
        </div>
        <div class="pagina">
            <div class="row">
                <div class="col-xs-1"></div>
                <div class="col-xs-11">
                    <p class="text-justify">
                        2.1.  O EMPREGADO se obriga a manter o mais absoluto sigilo com relação a toda e qualquer informação, conforme abaixo definida, que tenha sido revelada anteriormente e também as que venham a ser, a partir desta data, fornecido pelo EMPREGADOR ou pelo seus Clientes, devendo ser tratada como informação sigilosa.
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-1"></div>
                <div class="col-xs-11">
                    <p class="text-justify">
                        2.2.  Deverá ser considerada como informação confidencial, toda e qualquer informação escrita ou oral, revelada ao EMPREGADO, contendo  ela  ou  não  a  expressão  "Confidencial". O  termo  "informação" abrangerá toda informação escrita, verbal, digitalizada ou de qualquer outro modo apresentada, tangível ou intangível,  virtual  ou  física,  podendo  incluir,  mas  não  se  limitando  a:  "know-how",  técnicas,  "designs", especificações, diagramas, fluxogramas, configurações, soluções, fórmulas, modelos, desenhos, cópias, amostras,  cadastro  de  clientes,  preços  e  custos,  contratos,  planos  de  negócios,  processos,  projetos, fotografias,  programas  de  computador,  discos,  disquetes,  fitas,  conceitos  de  produto, instalações, infraestrutura,   especificações,  amostras  de  ideias,  definições  e  informações  mercadológicas,  invenções  e ideias,  outras  informações  técnicas,  financeiras  ou  comerciais,  dentre  outros,  doravante  denominados "INFORMAÇÕES  CONFIDENCIAIS",  a  que,  diretamente  ou  através  de  seus  Diretores,  Clientes, empregados  e/ou  prepostos,  venha  o EMPREGADO,  ter  acesso,  conhecimento,  ou  que  venha   lhe  ser confiadas durante e em razão dos trabalhos realizados e do Contrato Principal celebrado entre as PARTES.
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-1"></div>
                <div class="col-xs-11">
                    <p class="text-justify">
                        2.3.  Compromete-se, outrossim, o EMPREGADO, a não revelar, reproduzir,  utilizar  ou  dar conhecimento, ou alugar ou vender, em hipótese alguma, a terceiros as INFORMAÇÕES CONFIDENCIAIS. 
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-1"></div>
                <div class="col-xs-11">
                    <p class="text-justify">
                        2.4.  As PARTES deverão cuidar para  que  as informações  confidenciais fiquem  restritas  a  sede  do EMPREGADOR durante as discussões, análises, reuniões e negócios, trabalhos e projetos.
                    </p>
                </div>
            </div>
            <p class="text-justify">
                3. CLÁUSULA TERCEIRA - DOS DIREITOS E OBRIGAÇÕES
            </p>
            <div class="row">
                <div class="col-xs-1"></div>
                <div class="col-xs-11">
                    <p class="text-justify">
                        3.1. O EMPREGADO se compromete e se obriga a utilizar a informação confidencial revelado pelo EMPREGADOR exclusivamente para  os  propósitos  deste  Termo  e  da  execução  do  Contrato  Principal,  mantendo  sempre estrito sigilo acerca de tais informações.
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-1"></div>
                <div class="col-xs-11">
                    <p class="text-justify">
                        3.2.  O EMPREGADO se compromete a  não  efetuar  qualquer  cópia  da  informação  confidencial  sem consentimento prévio e expresso do EMPREGADOR.
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-1"></div>
                <div class="col-xs-11">
                    <p class="text-justify">
                        3.3. O EMPREGADO obriga-se a tomar todas as  medidas  necessárias  à  proteção  da  informação confidencial da EMPREGADOR, bem como para evitar e prevenir revelação a terceiros, exceto se devidamente autorizado  por  escrito  pelo EMPREGADOR.  De qualquer forma, a revelação  é  permitida  para  empresas controladoras,  controladas  e/ou  coligadas,  assim  consideradas  as  empresas  que  direta  ou  indiretamente controlem ou sejam controlados pelo EMPREGADOR.
                    </p>
                </div>
            </div>
            <p class="text-left assinatura small">
                Esta lauda é parte integrante do Termo de Sigilo e Confidencialidade.
            </p>
        </div>
        <div class="pagina">
            <p class="text-justify">
                4. CLÁUSULA QUARTA - DA VIGÊNCIA
            </p>
            <div class="row">
                <div class="col-xs-1"></div>
                <div class="col-xs-11">
                    <p class="text-justify">
                        4.1.   O presente Termo tem natureza irrevogável e irretratável, permanecendo em vigor desde a data da revelação das INFORMAÇÕES CONFIDENCIAS até 2 (dois) anos após o término do Contrato Principal, ao qual este é vinculado.
                    </p>
                </div>
            </div>
            <p class="text-justify">
                5. CLÁUSULA QUINTA - DAS PENALIDADES
            </p>
            <div class="row">
                <div class="col-xs-1"></div>
                <div class="col-xs-11">
                    <p class="text-justify">
                        5.1.   A quebra do sigilo profissional, devidamente comprovada, sem autorização expressa do EMPREGADOR, possibilitará a imediata rescisão de qualquer contrato firmado entre as PARTES, sem qualquer ônus para o EMPREGADOR.  Neste caso, o EMPREGADO estará sujeito,  por  ação  ou  omissão,  ao  pagamento  ou recomposição  de  todas  as  perdas  e  danos  sofridos  pelo EMPREGADOR,  inclusive  as  de  ordem  moral  ou concorrencial, bem como as de responsabilidades civil e criminal respectivas, as quais serão apuradas em regular  processo  judicial  ou  administrativo,  mais  o  valor  de  eventuais  lucros  cessantes  resultantes  de INFORMAÇÕES  CONFIDENCIAIS  indevidamente  transferidas.
                    </p>
                </div>
            </div>
            <p class="text-justify">
                6. CLÁUSULA SEXTA - DAS DISPOSIÇÕES GERAIS
            </p>
            <div class="row">
                <div class="col-xs-1"></div>
                <div class="col-xs-11">
                    <p class="text-justify">
                        6.1.   O  presente  Termo  constitui  acordo  entre  as PARTES,  relativamente  ao  tratamento  das INFORMAÇÕES CONFIDENCIAIS, aplicando-se a todos os acordos, promessas, propostas, declarações, entendimentos e negociações anteriores ou posteriores, escritas ou verbais, empreendidas pelas PARTES contratantes  no  que  diz  respeito  ao  Contrato  Principal,  sejam  estas  ações  feitas  direta  ou  indiretamente pelas PARTES, em conjunto ou separadamente, e, será igualmente aplicado a todo e qualquer acordo ou entendimento futuro, que venha a ser firmado entre as PARTES.
                    </p>
                    <p class="text-justify">
                        6.2.   Este Termo de  Confidencialidade  constitui  termo  vinculado  ao  Contrato  Principal,  porém, é parte independente e regulatória daquele.
                    </p>
                    <p class="text-justify">
                        6.3.   Surgindo divergências quanto à interpretação do pactuado neste Termo ou quanto à execução das obrigações dele decorrentes, ou constatando-se nele a existência de lacunas, solucionarão as PARTES tais divergências, de acordo com os princípios de boa-fé, da equidade, da razoabilidade, e da economicidade e, preencherão as lacunas com estipulações que, presumivelmente,  teriam  correspondido  à  vontade  das PARTES na respectiva ocasião.
                    </p>
                    <p class="text-justify">
                        6.4.   O disposto no presente  Termo  de  Confidencialidade  prevalecerá,  sempre,  em  caso  de  dúvida,  e salvo expressa determinação em contrário, sobre eventuais disposições constantes de outros instrumentos conexos firmados entre as PARTES quanto ao sigilo de informações confidenciais, tal como aqui definidas.
                    </p>
                    <p class="text-justify">
                        6.5.   A omissão  ou  tolerância  do EMPREGADOR,  em  exigir  o  estrito  cumprimento  dos  termos  e  condições deste  contrato,  não  constituirá  novação  ou  renúncia,  nem  afetará  os  seus  direitos,  que  poderão  ser exercidos a qualquer tempo.
                    </p>
                </div>
            </div>
            <div class="row">
                <p class="text-left assinatura small">
                    Esta lauda é parte integrante do Termo de Sigilo e Confidencialidade.
                </p>
            </div>
        </div>
        <div class="pagina">
            <p class="text-justify">
                7. CLÁUSULA SÉTIMA - DO FORO
            </p>
            <p class="text-justify">
                7.1. As PARTES elegem o foro da Comarca de Goiânia - Goiás, para dirimir quaisquer dúvidas originadas do presente Termo, com renúncia expressa a qualquer outro, por mais privilegiado que seja.
            </p>
            <p class="text-justify">
                E, por assim estarem justas e contratadas, as partes assinam o presente instrumento em 2 (duas) vias de igual teor e um só efeito, na presença de duas testemunhas.
            </p>
            <p class="text-right">
                <?= ucfirst(strtolower($arrClt[1]['projeto_cidade'])) ?>, <?= date('d') ?> de <?= mesesArray(date('m')) ?> de  <?= date('Y'); ?>.
            </p>
            <p class="text-left assinatura small">
                Esta lauda é parte integrante do Termo de Sigilo e Confidencialidade.
            </p>
        </div>
        <!-- javascript aqui -->
        <script src="../js/jquery-1.10.2.min.js" type="text/javascript"></script>
        <script src="../resources/js/print.js" type="text/javascript"></script>
    </body>
</html>
