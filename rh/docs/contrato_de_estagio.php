<?php

    if(empty($_COOKIE['logado'])){
	print "Efetue o Login<br><a href='login.php'>Logar</a> ";
	exit;
}

include "../../conn.php";
include "../../funcoes.php";
include "../../classes/regiao.php";

$id_estagiario = $_REQUEST['est'];

$sql = "SELECT A.*, B.nome AS nome_instituicao, DATE_FORMAT(A.inicio_estagio, '%d/%m/%Y') AS inicio_estagio, DATE_FORMAT(A.fim_estagio, '%d/%m/%Y') AS fim_estagio, DATE_FORMAT(A.inicio_jornada, '%H:%i') AS inicio_jornada, DATE_FORMAT(A.fim_jornada, '%H:%i') AS fim_jornada
	FROM estagiario AS A
		LEFT JOIN instituicoes_estagiario AS B ON(B.id_instituicao = A.id_instituicao)
	WHERE id_estagiario = $id_estagiario";
$query = mysql_query($sql);
$row = mysql_fetch_assoc($query);

$sqlEmp = 'SELECT * FROM rhempresa WHERE id_empresa = 1';
$queryEmp = mysql_query($sqlEmp);
$rowEmp = mysql_fetch_assoc($queryEmp);

if($row['nivel'] == '1'){
    $ano_semestre = "Ano";
    $nivel = 'M�dio';
} elseif($row['nivel'] == '2') {
    $ano_semestre = "Semestre";
    $nivel = "Superior";
}

switch(date("m")){
    case '01':$mes="Janeiro";break;
    case '02':$mes="Fevereiro";break;
    case '03':$mes="Mar�o";break;
    case '04':$mes="Abril";break;
    case '05':$mes="Maio";break;
    case '06':$mes="Junho";break;
    case '07':$mes="Julho";break;
    case '08':$mes="Agosto";break;
    case '09':$mes="Setembro";break;
    case '10':$mes="Outubro";break;
    case '11':$mes="Novembro";break;
    case '12':$mes="Dezembro";break;
}

?>
<!DOCTYPE html>
<html lang="pt_br">
    <head>
        <title>Contrato de Est�gio</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
        <link rel="shortcut icon" href="favicon.ico">
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="all">
        <link href="../../resources/css/bootstrap-theme.css" rel="stylesheet" media="all">
        
        <style>
            .content-fluid{background-color:white; padding: 0 30px;}
            hr{border-top: 1px solid #000;}
            .container p{font-size:16px}
            p{text-align:justify}
        </style>
    </head>
    <body class="container">
        <div class="content-fluid">
            <div class="text-justify">
                <h3>Instrumento jur�dico de Termo de Compromisso de Est�gio e Conv�nio de Concess�o de Est�gio, previstos na Lei 11.788 de 25/09/2008 que regulamenta e disciplina a contrata��o de Estagi�rios.</h3>
                <p>As partes a seguir qualificadas,<br/>a</p>
            </div>
            <hr/>
            <div id="instituicao_ensino">
                <p style="text-decoration:underline; text-decoration:underline"><strong><i>Institui��o de Ensino</i></strong></p>
                <div style="float: left; width:60%">
                    <p><strong>Mantenedora: </strong><?= $row['instituicao_ensino']?></p>
                    <p><strong>Endere�o: </strong><?= $row['endereco_ensino']?></p>
                    <p><strong>Bairro: </strong><?= $row['bairro_ensino']?></p>
                    <p><strong>Estado: </strong><?= $row['estado_ensino']?></p>
                    <p><strong>CNPJ Escola: </strong><?= $row['cnpj_ensino']?></p>
                    <p><strong>Professor Orientador: </strong><?= $row['prof_orientador']?></p>
                </div>
                <div style="float: left; width:35%">
                    <br/>
                    <br/>
                    <p><strong>n�: </strong><?= $row['n_endereco_ensino']?></p>
                    <p><strong>Cidade: </strong><?= $row['cidade_ensino']?></p>
                    <p><strong>CEP: </strong><?= $row['cep_ensino']?></p>
                </div>
                <div style="clear:both"></div>
            </div>
            <hr>
            <p>a</p>
            <hr>
            <div id="parte_concedente">
                <p style="text-decoration:underline; text-decoration:underline"><strong><i>Parte Concedente</i></strong></p>
                <div style="float: left; width:60%">
                    <p><strong>Raz�o Social:</strong> <?= $rowEmp['razao']?></p>
                    <p><strong>Endere�o:</strong> <?= $rowEmp['logradouro']?></p>
                    <p><strong>Bairro:</strong> <?= $rowEmp['bairro']?></p>
                    <p><strong>Estado:</strong> <?= $rowEmp['uf']?></p>
                    <p><strong>CNPJ: </strong><?= $rowEmp['cnpj']?></p>
                </div>
                <div style="float: left; width:35%">
                    <br/>
                    <br/>
                    <p><strong>n�: </strong><?= $rowEmp['numero']?></p>
                    <p><strong>Cidade:</strong><?= $rowEmp['municipio']?></p>
                    <p><strong>CEP:</strong><?= $rowEmp['cep']?></p>
                </div>
                <div style="clear:both"></div>
            </div>
            <hr>
            <div id="estagiario">
                <p style="text-decoration:underline; text-decoration:underline"><strong><i>Estagi�rio</i></strong></p>
                <div style="float: left; width:65%">
                    <p><strong>Nome: </strong><?= $row['nome']?></p>
                    <p><strong>CPF/MF: </strong><?= $row['cpf']?></p>
                    <p><strong>RG: </strong><?= $row['rg']?></p>
                    <p><strong>Endere�o: </strong><?= $row['endereco']?></p>
                    <p><strong>Bairro: </strong><?= $row['bairro']?></p>
                    <p><strong>Estado: </strong><?= $row['uf']?></p>
                    <br>
                    <p><strong>Regularmente matriculado no (ano ou semestre):</strong> <?= $row['ano_semestre'] . '� ' . $ano_semestre ?></p>
                    <p><strong>Do curso de (nome do curso):</strong> <?= $row['curso']?></p>
                    <p><strong>De n�vel (m�dio ou superior):</strong> <?= $nivel?></p>
                </div>
                <div style="float: left; width:35%">
                    <br/>
                    <br/>
                    <br/>
                    <br/>
                    <p><strong>n�: </strong><?= $row['numero']?></p>
                    <p><strong>Cidade: </strong><?= $row['cidade']?></p>
                    <p><strong>CEP: </strong><?= $row['cep']?></p>
                </div>
                <div style="clear:both"></div>
            </div>
            <hr/>
            <p>celebram entre si o presente Conv�nio de Concess�o de Est�gio e o Termo de Compromisso de Est�gio, convencionando as cl�usulas e condi��es a seguir:</p>
            <p style="text-decoration:underline; text-decoration:underline"><strong><i>Quadro resumo</i></strong></p>
            <p><strong>1) Per�odo de vig�ncia deste instrumento:</strong></p>
            <p>de <?= $row['inicio_estagio']?> a <?= $row['fim_estagio']?> ,podendo ser rescindido unilateralmente por qualquer das partes, a qualquer momento, sem �nus, multas ou aviso-pr�vio, mediante formaliza��o do respectivo Termo de Rescis�o;</p>
            <p><strong>2) Jornada:</strong></p>
            <p>de segunda �s sextas-feiras das <?= $row['inicio_jornada']?> as <?= $row['fim_jornada']?>;</p>
            <p><strong>3) Atividade do Estagi�rio(a) / Nome e cargo do Supervisor(a) do est�gio:</strong></p>
            <p><?= $row['atividade']?> / <?= $row['prof_orientador'] . ' - ' . $row['cargo_supervisor'] ?></p>
            <p><strong>4) Valor da Bolsa-est�gio + aux�lio transporte (n�o se aplica ao benef�cio o desconto previsto na CLT):</strong></p>
            <p>No per�odo do est�gio o Estagi�rio receber�, diretamente da Parte Concedente, uma Bolsa-est�gio mensal no valor de R$ <?= $row['salario']?> mais R$ <?= $row['valor_vt'] ?>/dia relativos ao aux�lio transporte, pagos at� o 5� dia �til do m�s subsequente ao vencido;</p>
            <p><strong>Cl�usula 1� -</strong> O presente Termo de Compromisso de Est�gio estabelece as condi��es b�sicas para a consecu��o do est�gio, previsto nos Artigos 1�, 2�, 3� e 4� da Lei n� 11.788 de 25/09/2008, visando o exerc�cio pr�tico de compet�ncias pr�prias da atividade profissional e � contextualiza��o curricular, objetivando o desenvolvimento do educando para a vida cidad� e para o trabalho, proporcionadas pela aprendizagem social profissional e cultural no ambiente de trabalho.</p>
            <p><strong>Cl�usula 2� -</strong> O est�gio pode ser obrigat�rio ou n�o-obrigat�rio, conforme determina��o das diretrizes curriculares, modalidade e �rea de ensino e do projeto pedag�gico do curso.</p>
            <p><strong>Cl�usula 3� -</strong> O est�gio, tanto o obrigat�rio quanto o n�o-obrigat�rio, n�o cria v�nculo empregat�cio de qualquer natureza, observadas as disposi��es previstas no Artigo 3� da Legisla��o do Est�gio.</p>
            <p><strong>Cl�usula 4� -</strong> A Institui��o de Ensino comunicar� � parte concedente do est�gio, atrav�s do Aluno, as datas de realiza��o de avalia��es escolares ou acad�micas.</p>
            <p><strong>Cl�usula 5� -</strong> Se a Institui��o de Ensino adotar verifica��es de aprendizagem peri�dicas ou finais, no per�odo de est�gio do Estudante, a carga hor�ria do est�gio, nestas datas, poder� ser reduzida � metade para assegurar o bom desempenho do Estudante no curso.</p>
            <p><strong>Cl�usula 6� -</strong> Caber� � Empresa ou Organiza��o concedente do est�gio a apresenta��o peri�dica, em prazo n�o superior a seis meses, do relat�rio das atividades do Estagi�rio, na conclus�o do est�gio ou, se for o caso, na rescis�o antecipada do Termo de Compromisso de Est�gio.</p>
            <p><strong>Cl�usula 7� -</strong> O hor�rio do est�gio n�o dever�, em hip�tese alguma, prejudicar a frequ�ncia do Aluno �s aulas e provas do curso no qual est� matriculado.</p>
            <p><strong>Cl�usula 8� -</strong> A assiduidade do Estagi�rio ser� demonstrada pela marca��o de entrada e sa�da em cart�o de ponto ou qualquer outra modalidade de controle adotada pela Parte Concedente.</p>
            <p><strong>Cl�usula 9� -</strong> Em decorr�ncia do presente Termo de Compromisso de Est�gio celebra-se neste ato, entre a Parte Concedente e a Institui��o de Ensino, o Conv�nio de Concess�o de Est�gio, previsto no Artigo 8� da Legisla��o do Est�gio.</p>
            <p><strong>Cl�usula 10� -</strong> O est�gio, como ato educativo escolar supervisionado, dever� ter acompanhamento efetivo pelo professor orientador da Institui��o de Ensino e por supervisor da Parte Concedente, comprovado por vistos nos relat�rios referidos na Cl�usula 6� deste Instrumento.</p>
            <p><strong>Cl�usula 11� -</strong> O descumprimento das obriga��es previstas na Legisla��o do Est�gio caracteriza v�nculo de emprego do Educando com a Parte Concedente do est�gio para todos os fins da legisla��o trabalhista e previdenci�ria.</p>
            <p><strong>Cl�usula 12� -</strong> No per�odo de vig�ncia do presente Termo de Compromisso de Est�gio o Estagi�rio ter� cobertura de Seguro de Acidentes Pessoais com Capital Segurado de R$ 30.000,00 (trinta mil reais), contratada pela Parte Concedente atrav�s da inclus�o do Estagi�rio na Ap�lice Coletiva de Acidentes Pessoais n� 80.703 , garantido pela SEGURADORA GENERALI DO BRASIL , nos termos do Inciso IV , do Art. 9� da Lei n� 11.788 de 25/09/2008.</p>
            <p>12.1 - A - Proposta de Ades�o Individual devidamente aceita pelo Segurado, e/ou o respectivo Certificado Individual de Seguro de Acidentes Pessoais, s�o partes integrantes e obrigat�rias deste documento. As suas aus�ncias descaracterizam o regime de contrata��o e sujeitam a Parte Concedente ao disposto na Cl�usula 11� do presente Instrumento.</p>
            <p><strong>Cl�usula 13� -</strong> Ficam estabelecidas entre as partes as condi��es acordadas para a consecu��o do est�gio objeto deste Instrumento:</p>
            <p>13.1 - As atividades descritas no quadro resumo poder�o ser alteradas com o progresso do est�gio e do curr�culo escolar, objetivando, sempre, a compatibiliza��o e a complementa��o do curso;</p>
            <p>13.2 - O valor da bolsa-est�gio descrito no quadro resumo poder� variar em decorr�ncia do exposto na cl�usula 5� deste Instrumento ou se ocorrer, por parte do Estagi�rio - independentemente do motivo - o n�o cumprimento das obriga��es acordadas no presente Termo de Compromisso de Est�gio;</p>
            <p>13.3 - O valor da bolsa-est�gio est� sujeito � reten��o de imposto de renda, conforme tabela em vigor definida pela Secretaria da Receita Federal;</p>
            <p>13.4 - A concess�o da bolsa-est�gio, bem como o aux�lio-transporte, s�o compuls�rios nos casos de est�gios n�o obrigat�rios;</p>
            <p>13.5 - A import�ncia referente � bolsa-est�gio, por n�o ter natureza salarial, n�o estar� sujeita, a qualquer desconto trabalhista, previdenci�rio ou mesmo vinculado ao FGTS, exce��o feita a eventual desconto correspondente ao imposto de Renda, consoante a cl�usula 13.3;</p>
            <p>13.6 - � assegurado ao estagi�rio, sempre que o est�gio tenha dura��o igual ou superior a 1 (um) ano, per�odo de recesso de 30 (trinta) dias - ou o proporcional ao per�odo estagiado - a ser gozado preferencialmente durante suas f�rias escolares. O per�odo de recesso poder� ser gozado ou indenizado.</p>
            <p><strong>Cl�usula 14� -</strong> Caber� ao Estagi�rio a obriga��o de informar � Parte Concedente quaisquer altera��es ocorridas no transcurso da sua atividade escolar, tais como interrup��o de frequ�ncia �s aulas, mudan�a de curso ou transfer�ncia de Institui��o de Ensino.</p>
            <p>14.1 - � de responsabilidade do Estagi�rio preservar o sigilo e a confidencialidade das informa��es a que tiver acesso no decorrer do seu est�gio junto � Parte Concedente.</p>
            <p><strong>Cl�usula 15� -</strong> Ser�o motivos de rescis�o autom�tica do presente Instrumento Jur�dico:</p>
            <li style="list-style-type: none">a. o abandono ou interrup��o do curso pelo Aluno, trancamento de matr�cula ou conclus�o do curso;</li>
            <li style="list-style-type: none">b. o n�o cumprimento de quaisquer das cl�usulas previstas neste Instrumento Jur�dico</li>
            <p><strong>Cl�usula 16� -</strong> Aplica-se ao Estagi�rio a Legisla��o relacionada � sa�de e seguran�a no trabalho, sendo sua implementa��o de responsabilidade da <strong>Parte concedente do Est�gio</strong>.</p>
            <p><strong>Cl�usula 17� -</strong> O presente Instrumento poder� ser renovado na forma da Lei e denunciado, a qualquer tempo, mediante comunica��o escrita, pela Institui��o de Ensino, pela Parte Concedente ou pelo Estagi�rio.</p>
            <br>
            <p>As partes, por estarem de acordo quanto ao cumprimento dos termos mutuamente firmados, assinam o presente em tr�s vias de igual teor e conte�do.</p>
            <br><br>
            <p>S�o Paulo / SP, <?= date("d")?> de <?= $mes ?> de <?= date("Y")?></p>
            <br><br><br>
            <p>__________________________________________________________________________________________________________<br><?= $row['instituicao_ensino'] ?><br><span style="font-size: 10px">(assinatura e carimbo da Escola)</span></p>
            <br><br>
            <p>_______________________________________________________<br><?= $rowEmp['razao']?><br><span style="font-size: 10px">(assinatura e carimbo da Parte Concedente)</span></p>
            <br><br>
            <p>_______________________________________________________<br><?= $row['nome']?><br><span style="font-size: 10px">(assinatura do(a) Estagi�rio(a))</span></p>
        </div>
            
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js" type="text/javascript"></script>
    </body>
</html>
