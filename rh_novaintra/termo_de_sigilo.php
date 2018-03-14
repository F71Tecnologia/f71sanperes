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
                EMPREGADO: <?= $arrClt[1]['nome'] ?> j� qualificado no Contrato de Trabalho, doravante denominado simplesmente EMPREGADO.
            </p>
            <p class="text-justify">
                EMPREGADOR: <?= $arrClt[1]['e_razao'] ?>, Pessoa jur�dica de direito privado, regularmente inscrita no CNPJ (MF) sob o n� <?= $arrClt[1]['e_cnpj'] ?>, sediada � <?= $arrClt[1]['e_endereco'] ?><?= (!empty($arrClt[1]['e_numero'])) ? ', '.$arrClt[1]['e_numero'] : ''  ?><?= (!empty($arrClt[1]['e_complemento'])) ? ', '.$arrClt[1]['e_complemento'] : ''  ?><?= (!empty($arrClt[1]['e_bairro'])) ? ', '.$arrClt[1]['e_bairro'] : ''  ?><?= (!empty($arrClt[1]['e_cidade'])) ? ', '.$arrClt[1]['e_cidade'] : ''  ?><?= (!empty($arrClt[1]['e_uf'])) ? ' - '.$arrClt[1]['e_uf'] : ''  ?>, doravante denominada simplesmente EMPREGADOR.
            </p>
            <p class="text-justify">
                Sempre que em conjunto referidas, doravante denominada (s) como PARTE(S).
            </p>
            <p class="text-justify">
                CONSIDERANDO que, em raz�o do contrato de trabalho celebrado entre as PARTES, doravante denominado CONTRATO, as mesmas ter�o acesso a informa��es confidenciais, as quais se constituem informa��o comercial confidencial.
            </p>
            <p class="text-justify">
                CONSIDERANDO que as PARTES desejam ajustar as condi��es de revela��o destas informa��es confidenciais j� disponibilizadas e aquelas que no futuro ser�o disponibilizadas para a execu��o do CONTRATO, bem como definir as regras relativas ao seu uso e prote��o:
            </p>
            <p class="text-justify">
                CONSIDERANDO que as PARTES declaram-se conhecedoras do art.482, "c", "g" da CLT:
            </p>
            <div class="row">
                <div class="col-xs-2"></div>
                <div class="col-xs-10">
                    <p class="text-justify">
                        Art. 482. Constituem justa causa para rescis�o do contrato de trabalho pelo empregador:<br />
                        (...) <em>omissis</em><br />
                        c) negocia��o habitual por conta pr�pria ou alheia sem permiss�o do empregador, e quando construir ato de concorr�ncia � empresa para a qual trabalha o empregado, ou for prejudicial ao servi�o;<br />
                        (...) <em>omissis</em><br />
                        g) viola��o de segredo da empresa;
                    </p>
                </div>
            </div>
            <p class="text-left assinatura small">
                Esta lauda � parte integrante do Termo de Sigilo e Confidencialidade.
            </p>
            <p class="text-justify">
                RESOLVEM AS PARTES acima qualificadas, celebrar o presente TERMO DE CONFIDENCIALIDADE, mediante as cl�usulas e condi��es que seguem:
            </p>
            <p class="text-justify">
                1. CL�USULA PRIMEIRA - DO OBJETO
            </p>
            <p class="text-justify">
                O objeto deste Termo � prover a necess�ria e adequada prote��o das informa��es confidenciais fornecidas pelo EMPREGADOR, ou pelo seus Clientes ao EMPREGADO, em raz�o do CONTRATO, a fim de que as mesmas possam desenvolver as atividades contempladas no CONTRATO, o qual vincular-se-� expressamente a este.
            </p>
            <div class="row">
                <div class="col-xs-1"></div>
                <div class="col-xs-11">
                    <p class="text-justify">
                        1.1.  As estipula��es e obriga��es constantes do presente instrumento ser�o aplicadas a toda e qualquer informa��o que seja revelada pela EMPREGADOR ou pelo seus Clientes.
                    </p>
                </div>
            </div>
            <br>
            <p class="text-justify">
                2.  CL�USULA SEGUNDA - DAS INFORMA��ES CONFIDENCIAIS
            </p>
        </div>
        <div class="pagina">
            <div class="row">
                <div class="col-xs-1"></div>
                <div class="col-xs-11">
                    <p class="text-justify">
                        2.1.  O EMPREGADO se obriga a manter o mais absoluto sigilo com rela��o a toda e qualquer informa��o, conforme abaixo definida, que tenha sido revelada anteriormente e tamb�m as que venham a ser, a partir desta data, fornecido pelo EMPREGADOR ou pelo seus Clientes, devendo ser tratada como informa��o sigilosa.
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-1"></div>
                <div class="col-xs-11">
                    <p class="text-justify">
                        2.2.  Dever� ser considerada como informa��o confidencial, toda e qualquer informa��o escrita ou oral, revelada ao EMPREGADO, contendo  ela  ou  n�o  a  express�o  "Confidencial". O  termo  "informa��o" abranger� toda informa��o escrita, verbal, digitalizada ou de qualquer outro modo apresentada, tang�vel ou intang�vel,  virtual  ou  f�sica,  podendo  incluir,  mas  n�o  se  limitando  a:  "know-how",  t�cnicas,  "designs", especifica��es, diagramas, fluxogramas, configura��es, solu��es, f�rmulas, modelos, desenhos, c�pias, amostras,  cadastro  de  clientes,  pre�os  e  custos,  contratos,  planos  de  neg�cios,  processos,  projetos, fotografias,  programas  de  computador,  discos,  disquetes,  fitas,  conceitos  de  produto, instala��es, infraestrutura,   especifica��es,  amostras  de  ideias,  defini��es  e  informa��es  mercadol�gicas,  inven��es  e ideias,  outras  informa��es  t�cnicas,  financeiras  ou  comerciais,  dentre  outros,  doravante  denominados "INFORMA��ES  CONFIDENCIAIS",  a  que,  diretamente  ou  atrav�s  de  seus  Diretores,  Clientes, empregados  e/ou  prepostos,  venha  o EMPREGADO,  ter  acesso,  conhecimento,  ou  que  venha   lhe  ser confiadas durante e em raz�o dos trabalhos realizados e do Contrato Principal celebrado entre as PARTES.
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-1"></div>
                <div class="col-xs-11">
                    <p class="text-justify">
                        2.3.  Compromete-se, outrossim, o EMPREGADO, a n�o revelar, reproduzir,  utilizar  ou  dar conhecimento, ou alugar ou vender, em hip�tese alguma, a terceiros as INFORMA��ES CONFIDENCIAIS. 
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-1"></div>
                <div class="col-xs-11">
                    <p class="text-justify">
                        2.4.  As PARTES dever�o cuidar para  que  as informa��es  confidenciais fiquem  restritas  a  sede  do EMPREGADOR durante as discuss�es, an�lises, reuni�es e neg�cios, trabalhos e projetos.
                    </p>
                </div>
            </div>
            <p class="text-justify">
                3. CL�USULA TERCEIRA - DOS DIREITOS E OBRIGA��ES
            </p>
            <div class="row">
                <div class="col-xs-1"></div>
                <div class="col-xs-11">
                    <p class="text-justify">
                        3.1. O EMPREGADO se compromete e se obriga a utilizar a informa��o confidencial revelado pelo EMPREGADOR exclusivamente para  os  prop�sitos  deste  Termo  e  da  execu��o  do  Contrato  Principal,  mantendo  sempre estrito sigilo acerca de tais informa��es.
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-1"></div>
                <div class="col-xs-11">
                    <p class="text-justify">
                        3.2.  O EMPREGADO se compromete a  n�o  efetuar  qualquer  c�pia  da  informa��o  confidencial  sem consentimento pr�vio e expresso do EMPREGADOR.
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-1"></div>
                <div class="col-xs-11">
                    <p class="text-justify">
                        3.3. O EMPREGADO obriga-se a tomar todas as  medidas  necess�rias  �  prote��o  da  informa��o confidencial da EMPREGADOR, bem como para evitar e prevenir revela��o a terceiros, exceto se devidamente autorizado  por  escrito  pelo EMPREGADOR.  De qualquer forma, a revela��o  �  permitida  para  empresas controladoras,  controladas  e/ou  coligadas,  assim  consideradas  as  empresas  que  direta  ou  indiretamente controlem ou sejam controlados pelo EMPREGADOR.
                    </p>
                </div>
            </div>
            <p class="text-left assinatura small">
                Esta lauda � parte integrante do Termo de Sigilo e Confidencialidade.
            </p>
        </div>
        <div class="pagina">
            <p class="text-justify">
                4. CL�USULA QUARTA - DA VIG�NCIA
            </p>
            <div class="row">
                <div class="col-xs-1"></div>
                <div class="col-xs-11">
                    <p class="text-justify">
                        4.1.   O presente Termo tem natureza irrevog�vel e irretrat�vel, permanecendo em vigor desde a data da revela��o das INFORMA��ES CONFIDENCIAS at� 2 (dois) anos ap�s o t�rmino do Contrato Principal, ao qual este � vinculado.
                    </p>
                </div>
            </div>
            <p class="text-justify">
                5. CL�USULA QUINTA - DAS PENALIDADES
            </p>
            <div class="row">
                <div class="col-xs-1"></div>
                <div class="col-xs-11">
                    <p class="text-justify">
                        5.1.   A quebra do sigilo profissional, devidamente comprovada, sem autoriza��o expressa do EMPREGADOR, possibilitar� a imediata rescis�o de qualquer contrato firmado entre as PARTES, sem qualquer �nus para o EMPREGADOR.  Neste caso, o EMPREGADO estar� sujeito,  por  a��o  ou  omiss�o,  ao  pagamento  ou recomposi��o  de  todas  as  perdas  e  danos  sofridos  pelo EMPREGADOR,  inclusive  as  de  ordem  moral  ou concorrencial, bem como as de responsabilidades civil e criminal respectivas, as quais ser�o apuradas em regular  processo  judicial  ou  administrativo,  mais  o  valor  de  eventuais  lucros  cessantes  resultantes  de INFORMA��ES  CONFIDENCIAIS  indevidamente  transferidas.
                    </p>
                </div>
            </div>
            <p class="text-justify">
                6. CL�USULA SEXTA - DAS DISPOSI��ES GERAIS
            </p>
            <div class="row">
                <div class="col-xs-1"></div>
                <div class="col-xs-11">
                    <p class="text-justify">
                        6.1.   O  presente  Termo  constitui  acordo  entre  as PARTES,  relativamente  ao  tratamento  das INFORMA��ES CONFIDENCIAIS, aplicando-se a todos os acordos, promessas, propostas, declara��es, entendimentos e negocia��es anteriores ou posteriores, escritas ou verbais, empreendidas pelas PARTES contratantes  no  que  diz  respeito  ao  Contrato  Principal,  sejam  estas  a��es  feitas  direta  ou  indiretamente pelas PARTES, em conjunto ou separadamente, e, ser� igualmente aplicado a todo e qualquer acordo ou entendimento futuro, que venha a ser firmado entre as PARTES.
                    </p>
                    <p class="text-justify">
                        6.2.   Este Termo de  Confidencialidade  constitui  termo  vinculado  ao  Contrato  Principal,  por�m, � parte independente e regulat�ria daquele.
                    </p>
                    <p class="text-justify">
                        6.3.   Surgindo diverg�ncias quanto � interpreta��o do pactuado neste Termo ou quanto � execu��o das obriga��es dele decorrentes, ou constatando-se nele a exist�ncia de lacunas, solucionar�o as PARTES tais diverg�ncias, de acordo com os princ�pios de boa-f�, da equidade, da razoabilidade, e da economicidade e, preencher�o as lacunas com estipula��es que, presumivelmente,  teriam  correspondido  �  vontade  das PARTES na respectiva ocasi�o.
                    </p>
                    <p class="text-justify">
                        6.4.   O disposto no presente  Termo  de  Confidencialidade  prevalecer�,  sempre,  em  caso  de  d�vida,  e salvo expressa determina��o em contr�rio, sobre eventuais disposi��es constantes de outros instrumentos conexos firmados entre as PARTES quanto ao sigilo de informa��es confidenciais, tal como aqui definidas.
                    </p>
                    <p class="text-justify">
                        6.5.   A omiss�o  ou  toler�ncia  do EMPREGADOR,  em  exigir  o  estrito  cumprimento  dos  termos  e  condi��es deste  contrato,  n�o  constituir�  nova��o  ou  ren�ncia,  nem  afetar�  os  seus  direitos,  que  poder�o  ser exercidos a qualquer tempo.
                    </p>
                </div>
            </div>
            <div class="row">
                <p class="text-left assinatura small">
                    Esta lauda � parte integrante do Termo de Sigilo e Confidencialidade.
                </p>
            </div>
        </div>
        <div class="pagina">
            <p class="text-justify">
                7. CL�USULA S�TIMA - DO FORO
            </p>
            <p class="text-justify">
                7.1. As PARTES elegem o foro da Comarca de Goi�nia - Goi�s, para dirimir quaisquer d�vidas originadas do presente Termo, com ren�ncia expressa a qualquer outro, por mais privilegiado que seja.
            </p>
            <p class="text-justify">
                E, por assim estarem justas e contratadas, as partes assinam o presente instrumento em 2 (duas) vias de igual teor e um s� efeito, na presen�a de duas testemunhas.
            </p>
            <p class="text-right">
                <?= ucfirst(strtolower($arrClt[1]['projeto_cidade'])) ?>, <?= date('d') ?> de <?= mesesArray(date('m')) ?> de  <?= date('Y'); ?>.
            </p>
            <p class="text-left assinatura small">
                Esta lauda � parte integrante do Termo de Sigilo e Confidencialidade.
            </p>
        </div>
        <!-- javascript aqui -->
        <script src="../js/jquery-1.10.2.min.js" type="text/javascript"></script>
        <script src="../resources/js/print.js" type="text/javascript"></script>
    </body>
</html>
