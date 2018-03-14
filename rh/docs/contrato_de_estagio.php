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
    $nivel = 'Médio';
} elseif($row['nivel'] == '2') {
    $ano_semestre = "Semestre";
    $nivel = "Superior";
}

switch(date("m")){
    case '01':$mes="Janeiro";break;
    case '02':$mes="Fevereiro";break;
    case '03':$mes="Março";break;
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
        <title>Contrato de Estágio</title>
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
                <h3>Instrumento jurídico de Termo de Compromisso de Estágio e Convênio de Concessão de Estágio, previstos na Lei 11.788 de 25/09/2008 que regulamenta e disciplina a contratação de Estagiários.</h3>
                <p>As partes a seguir qualificadas,<br/>a</p>
            </div>
            <hr/>
            <div id="instituicao_ensino">
                <p style="text-decoration:underline; text-decoration:underline"><strong><i>Instituição de Ensino</i></strong></p>
                <div style="float: left; width:60%">
                    <p><strong>Mantenedora: </strong><?= $row['instituicao_ensino']?></p>
                    <p><strong>Endereço: </strong><?= $row['endereco_ensino']?></p>
                    <p><strong>Bairro: </strong><?= $row['bairro_ensino']?></p>
                    <p><strong>Estado: </strong><?= $row['estado_ensino']?></p>
                    <p><strong>CNPJ Escola: </strong><?= $row['cnpj_ensino']?></p>
                    <p><strong>Professor Orientador: </strong><?= $row['prof_orientador']?></p>
                </div>
                <div style="float: left; width:35%">
                    <br/>
                    <br/>
                    <p><strong>nº: </strong><?= $row['n_endereco_ensino']?></p>
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
                    <p><strong>Razão Social:</strong> <?= $rowEmp['razao']?></p>
                    <p><strong>Endereço:</strong> <?= $rowEmp['logradouro']?></p>
                    <p><strong>Bairro:</strong> <?= $rowEmp['bairro']?></p>
                    <p><strong>Estado:</strong> <?= $rowEmp['uf']?></p>
                    <p><strong>CNPJ: </strong><?= $rowEmp['cnpj']?></p>
                </div>
                <div style="float: left; width:35%">
                    <br/>
                    <br/>
                    <p><strong>nº: </strong><?= $rowEmp['numero']?></p>
                    <p><strong>Cidade:</strong><?= $rowEmp['municipio']?></p>
                    <p><strong>CEP:</strong><?= $rowEmp['cep']?></p>
                </div>
                <div style="clear:both"></div>
            </div>
            <hr>
            <div id="estagiario">
                <p style="text-decoration:underline; text-decoration:underline"><strong><i>Estagiário</i></strong></p>
                <div style="float: left; width:65%">
                    <p><strong>Nome: </strong><?= $row['nome']?></p>
                    <p><strong>CPF/MF: </strong><?= $row['cpf']?></p>
                    <p><strong>RG: </strong><?= $row['rg']?></p>
                    <p><strong>Endereço: </strong><?= $row['endereco']?></p>
                    <p><strong>Bairro: </strong><?= $row['bairro']?></p>
                    <p><strong>Estado: </strong><?= $row['uf']?></p>
                    <br>
                    <p><strong>Regularmente matriculado no (ano ou semestre):</strong> <?= $row['ano_semestre'] . 'º ' . $ano_semestre ?></p>
                    <p><strong>Do curso de (nome do curso):</strong> <?= $row['curso']?></p>
                    <p><strong>De nível (médio ou superior):</strong> <?= $nivel?></p>
                </div>
                <div style="float: left; width:35%">
                    <br/>
                    <br/>
                    <br/>
                    <br/>
                    <p><strong>nº: </strong><?= $row['numero']?></p>
                    <p><strong>Cidade: </strong><?= $row['cidade']?></p>
                    <p><strong>CEP: </strong><?= $row['cep']?></p>
                </div>
                <div style="clear:both"></div>
            </div>
            <hr/>
            <p>celebram entre si o presente Convênio de Concessão de Estágio e o Termo de Compromisso de Estágio, convencionando as cláusulas e condições a seguir:</p>
            <p style="text-decoration:underline; text-decoration:underline"><strong><i>Quadro resumo</i></strong></p>
            <p><strong>1) Período de vigência deste instrumento:</strong></p>
            <p>de <?= $row['inicio_estagio']?> a <?= $row['fim_estagio']?> ,podendo ser rescindido unilateralmente por qualquer das partes, a qualquer momento, sem ônus, multas ou aviso-prévio, mediante formalização do respectivo Termo de Rescisão;</p>
            <p><strong>2) Jornada:</strong></p>
            <p>de segunda às sextas-feiras das <?= $row['inicio_jornada']?> as <?= $row['fim_jornada']?>;</p>
            <p><strong>3) Atividade do Estagiário(a) / Nome e cargo do Supervisor(a) do estágio:</strong></p>
            <p><?= $row['atividade']?> / <?= $row['prof_orientador'] . ' - ' . $row['cargo_supervisor'] ?></p>
            <p><strong>4) Valor da Bolsa-estágio + auxílio transporte (não se aplica ao benefício o desconto previsto na CLT):</strong></p>
            <p>No período do estágio o Estagiário receberá, diretamente da Parte Concedente, uma Bolsa-estágio mensal no valor de R$ <?= $row['salario']?> mais R$ <?= $row['valor_vt'] ?>/dia relativos ao auxílio transporte, pagos até o 5º dia útil do mês subsequente ao vencido;</p>
            <p><strong>Cláusula 1ª -</strong> O presente Termo de Compromisso de Estágio estabelece as condições básicas para a consecução do estágio, previsto nos Artigos 1º, 2º, 3º e 4º da Lei nº 11.788 de 25/09/2008, visando o exercício prático de competências próprias da atividade profissional e à contextualização curricular, objetivando o desenvolvimento do educando para a vida cidadã e para o trabalho, proporcionadas pela aprendizagem social profissional e cultural no ambiente de trabalho.</p>
            <p><strong>Cláusula 2ª -</strong> O estágio pode ser obrigatório ou não-obrigatório, conforme determinação das diretrizes curriculares, modalidade e área de ensino e do projeto pedagógico do curso.</p>
            <p><strong>Cláusula 3ª -</strong> O estágio, tanto o obrigatório quanto o não-obrigatório, não cria vínculo empregatício de qualquer natureza, observadas as disposições previstas no Artigo 3º da Legislação do Estágio.</p>
            <p><strong>Cláusula 4ª -</strong> A Instituição de Ensino comunicará à parte concedente do estágio, através do Aluno, as datas de realização de avaliações escolares ou acadêmicas.</p>
            <p><strong>Cláusula 5ª -</strong> Se a Instituição de Ensino adotar verificações de aprendizagem periódicas ou finais, no período de estágio do Estudante, a carga horária do estágio, nestas datas, poderá ser reduzida à metade para assegurar o bom desempenho do Estudante no curso.</p>
            <p><strong>Cláusula 6ª -</strong> Caberá à Empresa ou Organização concedente do estágio a apresentação periódica, em prazo não superior a seis meses, do relatório das atividades do Estagiário, na conclusão do estágio ou, se for o caso, na rescisão antecipada do Termo de Compromisso de Estágio.</p>
            <p><strong>Cláusula 7ª -</strong> O horário do estágio não deverá, em hipótese alguma, prejudicar a frequência do Aluno às aulas e provas do curso no qual está matriculado.</p>
            <p><strong>Cláusula 8ª -</strong> A assiduidade do Estagiário será demonstrada pela marcação de entrada e saída em cartão de ponto ou qualquer outra modalidade de controle adotada pela Parte Concedente.</p>
            <p><strong>Cláusula 9ª -</strong> Em decorrência do presente Termo de Compromisso de Estágio celebra-se neste ato, entre a Parte Concedente e a Instituição de Ensino, o Convênio de Concessão de Estágio, previsto no Artigo 8º da Legislação do Estágio.</p>
            <p><strong>Cláusula 10ª -</strong> O estágio, como ato educativo escolar supervisionado, deverá ter acompanhamento efetivo pelo professor orientador da Instituição de Ensino e por supervisor da Parte Concedente, comprovado por vistos nos relatórios referidos na Cláusula 6ª deste Instrumento.</p>
            <p><strong>Cláusula 11ª -</strong> O descumprimento das obrigações previstas na Legislação do Estágio caracteriza vínculo de emprego do Educando com a Parte Concedente do estágio para todos os fins da legislação trabalhista e previdenciária.</p>
            <p><strong>Cláusula 12ª -</strong> No período de vigência do presente Termo de Compromisso de Estágio o Estagiário terá cobertura de Seguro de Acidentes Pessoais com Capital Segurado de R$ 30.000,00 (trinta mil reais), contratada pela Parte Concedente através da inclusão do Estagiário na Apólice Coletiva de Acidentes Pessoais nº 80.703 , garantido pela SEGURADORA GENERALI DO BRASIL , nos termos do Inciso IV , do Art. 9º da Lei nº 11.788 de 25/09/2008.</p>
            <p>12.1 - A - Proposta de Adesão Individual devidamente aceita pelo Segurado, e/ou o respectivo Certificado Individual de Seguro de Acidentes Pessoais, são partes integrantes e obrigatórias deste documento. As suas ausências descaracterizam o regime de contratação e sujeitam a Parte Concedente ao disposto na Cláusula 11ª do presente Instrumento.</p>
            <p><strong>Cláusula 13ª -</strong> Ficam estabelecidas entre as partes as condições acordadas para a consecução do estágio objeto deste Instrumento:</p>
            <p>13.1 - As atividades descritas no quadro resumo poderão ser alteradas com o progresso do estágio e do currículo escolar, objetivando, sempre, a compatibilização e a complementação do curso;</p>
            <p>13.2 - O valor da bolsa-estágio descrito no quadro resumo poderá variar em decorrência do exposto na cláusula 5ª deste Instrumento ou se ocorrer, por parte do Estagiário - independentemente do motivo - o não cumprimento das obrigações acordadas no presente Termo de Compromisso de Estágio;</p>
            <p>13.3 - O valor da bolsa-estágio está sujeito à retenção de imposto de renda, conforme tabela em vigor definida pela Secretaria da Receita Federal;</p>
            <p>13.4 - A concessão da bolsa-estágio, bem como o auxílio-transporte, são compulsórios nos casos de estágios não obrigatórios;</p>
            <p>13.5 - A importância referente à bolsa-estágio, por não ter natureza salarial, não estará sujeita, a qualquer desconto trabalhista, previdenciário ou mesmo vinculado ao FGTS, exceção feita a eventual desconto correspondente ao imposto de Renda, consoante a cláusula 13.3;</p>
            <p>13.6 - É assegurado ao estagiário, sempre que o estágio tenha duração igual ou superior a 1 (um) ano, período de recesso de 30 (trinta) dias - ou o proporcional ao período estagiado - a ser gozado preferencialmente durante suas férias escolares. O período de recesso poderá ser gozado ou indenizado.</p>
            <p><strong>Cláusula 14ª -</strong> Caberá ao Estagiário a obrigação de informar à Parte Concedente quaisquer alterações ocorridas no transcurso da sua atividade escolar, tais como interrupção de frequência às aulas, mudança de curso ou transferência de Instituição de Ensino.</p>
            <p>14.1 - É de responsabilidade do Estagiário preservar o sigilo e a confidencialidade das informações a que tiver acesso no decorrer do seu estágio junto à Parte Concedente.</p>
            <p><strong>Cláusula 15ª -</strong> Serão motivos de rescisão automática do presente Instrumento Jurídico:</p>
            <li style="list-style-type: none">a. o abandono ou interrupção do curso pelo Aluno, trancamento de matrícula ou conclusão do curso;</li>
            <li style="list-style-type: none">b. o não cumprimento de quaisquer das cláusulas previstas neste Instrumento Jurídico</li>
            <p><strong>Cláusula 16ª -</strong> Aplica-se ao Estagiário a Legislação relacionada à saúde e segurança no trabalho, sendo sua implementação de responsabilidade da <strong>Parte concedente do Estágio</strong>.</p>
            <p><strong>Cláusula 17ª -</strong> O presente Instrumento poderá ser renovado na forma da Lei e denunciado, a qualquer tempo, mediante comunicação escrita, pela Instituição de Ensino, pela Parte Concedente ou pelo Estagiário.</p>
            <br>
            <p>As partes, por estarem de acordo quanto ao cumprimento dos termos mutuamente firmados, assinam o presente em três vias de igual teor e conteúdo.</p>
            <br><br>
            <p>São Paulo / SP, <?= date("d")?> de <?= $mes ?> de <?= date("Y")?></p>
            <br><br><br>
            <p>__________________________________________________________________________________________________________<br><?= $row['instituicao_ensino'] ?><br><span style="font-size: 10px">(assinatura e carimbo da Escola)</span></p>
            <br><br>
            <p>_______________________________________________________<br><?= $rowEmp['razao']?><br><span style="font-size: 10px">(assinatura e carimbo da Parte Concedente)</span></p>
            <br><br>
            <p>_______________________________________________________<br><?= $row['nome']?><br><span style="font-size: 10px">(assinatura do(a) Estagiário(a))</span></p>
        </div>
            
        <script src="../../js/jquery-1.10.2.min.js"></script>
        <script src="../../js/jquery-ui-1.9.2.custom.min.js"></script>
        <script src="../resources/js/bootstrap.min.js"></script>
        <script src="../resources/js/main.js"></script>
        <script src="../js/global.js" type="text/javascript"></script>
    </body>
</html>
