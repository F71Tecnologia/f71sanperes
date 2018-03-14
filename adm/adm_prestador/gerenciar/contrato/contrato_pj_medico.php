<h2 class="titulo">CONTRATO DE PRESTAÇÃO DE SERVIÇOS MÉDICOS</h2> 
<?php
if (isset($_GET['dev'])) {
    echo '<pre>';
    print_r($prestador);
    echo '</pre>';
}
?>
<p>
    Pelo presente instrumento particular, de um lado, <strong><?= trim($prestador['contratante']); ?></strong>, 
    pessoa jurídica de direito privado, devidamente inscrita no CNPJ sob o nº <?= $prestador['cnpj_contratante']; ?>, 
    com sede na cidade de <?= trim($prestador['municipio_contratante']); ?> - <?= trim($prestador['uf_contratante']); ?>, 
    localizada na <?= trim($prestador['logradouro_contratante']); ?>, CEP <?= trim($prestador['cep_contratante']); ?>, 
    doravante denominada <strong>CONTRATANTE</strong>, e de outro, <strong><?= trim($prestador['nome_fantasia']); ?></strong>, 
    pessoa jurídica de direito privado, CNPJ <?= trim($prestador['cnpj']); ?>, com sede na <?= trim(strtoupper($prestador['endereco'])); ?>
    , neste ato representada, na forma de seu Contrato Social, por <?= trim(strtoupper($prestador['prestador_responsavel'])); ?>, 
    portador da Cédula de Identidade RG nº <?= trim(strtoupper($prestador['prestador_rg'])); ?> e inscrito no CPF/MF sob o nº 
    <?= trim(strtoupper($prestador['prestador_cpf'])); ?>,  doravante denominada <strong>CONTRATADA</strong>;  
</p>
<p>Firmam entre si, o presente contrato de prestação de serviços médicos, mediante as seguintes cláusulas e condições:</p>
<h4>Cláusula Primeira - DO OBJETO</h4>
<p>
    1.1 - O objeto do presente Contrato refere-se à disponibilização de mão de obra especializada na área médica para prestação de serviços, 
    em parceira com a Prefeitura Municipal de <?= $prestador['municipio']; ?>.
</p>
<p>
    1.2 - A CONTRATANTE mediante Contrato de Gestão celebrado com a Prefeitura Municipal de <?= $prestador['municipio']; ?>, pelo Departamento Municipal de Saúde, devendo em virtude disto, 
    atender aos padrões fixados pela política de atendimento público à saúde, comprometendo-se a CONTRATADA a colaborar para atingir tais objetivos, com os serviços descritos na cláusula 1.1.</p>



<?php
$total_cont = count($medicos_funcoes);
$final_especialidade = ($total_cont > 1) ? 's' : '';
?>
<p>Parágrafo único - Os serviços mencionados no caput, prestados pela CONTRATADA, serão realizados na<?= $final_especialidade; ?> área<?= $final_especialidade; ?> de especialidade<?= $final_especialidade; ?> de 

    <?php
    $cont = 1;
    foreach ($medicos_funcoes as $funcao) {
        $final = ($total_cont == $cont) ? '. ' : (($total_cont == ($cont + 1)) ? ' e ' : ', ');
        echo strtoupper($funcao['nome_curso']) . $final;
        $cont++;
    }
    ?>  

</p>
<h4>Cláusula Segunda - DA PRESTAÇÃO DE SERVIÇOS</h4>
<p>
    2.1 - Os serviços contratados poderão ser executados pelos profissionais médicos, sócios quotistas da empresa CONTRATADA ou por outro sócio ou empregado, 
    desde que de reconhecida idoneidade e qualificação técnica especializada.
</p>
<p>
    2.2 - A CONTRATADA indica para a realização dos serviços contratados, 
    <?php
    if (count($medicos_pj) > 1) {
        echo 'os médicos ';
    } else {
        echo 'o médico ';
    }
    $cont = 1;
    $total_cont = count($medicos_pj);
    foreach ($medicos_pj as $medico) {
        $final = ($total_cont == $cont) ? '. ' : (($total_cont == ($cont + 1)) ? ' e ' : '; ');
//        $final = ($total_cont==$cont) ? '. ' : ';';
        echo ' Dr.' . strtoupper($medico['nome']) . ', portador do CRM n° ' . $medico['crm'] . $final;
        $cont++;
    }
    ?>
</p>
<p>
    2.3 - Na hipótese da CONTRATADA substituir o profissional indicado no item 2.2, deverá por escrito comunicar o nome do novo profissional à CONTRATANTE, com uma antecedência de 30 (trinta) 
    dias antes da substituição. O prazo poderá ser alterado desde que de comum acordo entre as partes.
</p>
<p>
    2.4 - A CONTRATADA deverá prestar os serviços objeto da presente contratação, dentro das dependências da CONTRATANTE, em caráter não habitual.
</p>
<p>2.5 - Visando atender a Política de Atendimento Público à Saúde, a CONTRATADA se compromete a prestar seus serviços de acordo com os agendamentos dos procedimentos realizados pela CONTRATANTE.</p>
<p>
    2.6 - Quando por qualquer razão, a CONTRATADA por meio de seu profissional indicado, não puder atender os serviços contratados por algum período, se compromete a comunicar a 
    CONTRATANTE com 30 (trinta) dias de antecedência, para que haja tempo hábil para a contratação de outra empresa especializada.
</p>
<p>    
    2.6.1 - Na hipótese de não haver a prévia comunicação, deverá a CONTRATADA pagar uma multa no valor do dobro do plantão, que será descontada na próxima Nota Fiscal. 
</p>
<p>    
    2.7 - Caso haja considerável aumento no volume de serviços a serem prestados, a CONTRATANTE se compromete a comunicar a CONTRATADA, com 30 (trinta) 
    dias de antecedência, para que esta se adeque às necessidades da demanda.
</p>

<h4>Cláusula Terceira - PRAZO E RESCISÃO</h4>
<p>
    3.1 - O presente Contrato vigorará até o término do Contrato de Gestão celebrado com a Prefeitura Municipal de <?= $prestador['municipio']; ?>, iniciando sua vigência a partir do dia <?= $prestador['dia_contratado']; ?> de <?= $meses[str_pad($prestador['mes_contratado'], 2, '0', STR_PAD_LEFT)]; ?> de <?= $prestador['ano_contratado']; ?>, podendo ser rescindido por qualquer das Partes, a qualquer momento, sem justo motivo, desde que haja prévia comunicação expressa, com antecedência mínima de 30 (trinta) dias.    
</p>
<h4>3.2 - A CONTRATANTE poderá rescindir o presente Contrato nas seguintes hipóteses:</h4>
<p>
    3.2.1 - desídia da CONTRATADA no cumprimento das obrigações assumidas para com a CONTRATANTE e terceiros;
</p>
<p>
    3.2.2 - caso haja descumprimento do Código de Ética Médica, à Moral, Ética e boas práticas dos serviços de saúde;    
</p>
<p>
    3.2.3 - caso a CONTRATADA desrespeite as cláusulas previstas no presente contrato;    
</p>
<p>
    3.2.4 - caso a CONTRATADA por si ou por seus empregados, prepostos ou sócios, por qualquer ato, meio ou forma, interromper ou tentar suspender, 
    sem motivo justo e legal, ou prejudicar a eficaz e continua prestação de serviços;    
</p>
<p>
    3.2.5 - a qualquer tempo e por qualquer motivo, desde que comunique a CONTRATADA de tal intenção, por escrito, com antecedência mínima de 30 (trinta) dias.    
</p>
<p>
    3.3 - A CONTRATADA poderá rescindir o presente Contrato nas seguintes circunstâncias:    
</p>
<p>
    3.3.1 - quando a CONTRATANTE exigir da CONTRATADA atividade que exceda a prestação dos serviços objeto do presente contrato;    
</p>
<p>
    3.3.2 - caso a CONTRATANTE descumpra quaisquer das cláusulas previstas no presente Contrato;    
</p>
<p>
    3.3.3 - caso haja decretação de falência, concordata, insolvência ou recuperação judicial da CONTRATANTE;    
</p>
<p>
    3.3.4 - por motivos de força maior que inviabilizem a continuidade da prestação dos serviços em questão;    
</p>
<p>
    3.3.5 - a qualquer tempo e por qualquer motivo, desde que comunique a CONTRATANTE de tal intenção, por escrito, com antecedência mínima de 60 (sessenta) dias.    
</p>
<p>
    3.4 - A rescisão do presente Contrato não extingue os direitos e obrigações que as Partes tenham entre si e perante terceiros, adquiridas anteriormente.    
</p>
<h4>Cláusula Quarta - REMUNERAÇÃO</h4>
<p>
    4.1 - Em pagamento aos serviços prestados será pago à CONTRATADA o valor 

    <?php
    if (isset($_REQUEST['horista']) && $_REQUEST['horista'] == 1) {
        echo "por hora de ";
        $valor = (!empty($_REQUEST['valor'])) ? $_REQUEST['valor'] : $medico['valor_hora'];
    } else {
        echo "mensal de ";
        $valor = (!empty($_REQUEST['valor'])) ? $_REQUEST['valor'] : $medico['salario'];
    }
    foreach ($medicos_funcoes as $medico) {
        echo 'R$ ' . number_format($valor, 2, ',', '.') . ' (' . trim(valor_extenso(!empty($valor) ? $valor : '0.00' )) . ') na especialidade de ' . $medico['nome_curso'] . '; ';
    }
    ?>

    de efetivo serviço prestado até o quinto dia útil do mês subsequente ao vencido.
</p>
<p>
    Parágrafo Primeiro - Para que a CONTRATANTE possa proceder ao pagamento da remuneração prevista na cláusula 4.1, a CONTRATADA deverá encaminhar à CONTRATANTE a respectiva Nota Fiscal de Prestação de Serviços, 
    acompanhada de relatório detalhado contendo a descrição dos serviços prestados, até o dia 02 de cada mês, assim como as certidões negativas de FGTS, Previdenciária, Divida Ativa da União e Trabalhista.    
</p>
<h4>Cláusula Quinta- REAJUSTE ANUAL</h4>
<p>
    5.1 - Decorrido um prazo de 12 (doze) meses da presente contratação e, havendo interesse da CONTRATANTE na continuidade da prestação dos serviços da CONTRATADA, o valor da remuneração será reajustado de comum acordo.    
</p>
<h4>Cláusula Sexta- TRIBUTOS</h4>
<p>
    6.1 - Todos os tributos federais, estaduais ou municipais que incidam sobre a prestação dos serviços objeto do presente Contrato, serão de exclusiva responsabilidade da CONTRATADA, 
    cabendo-lhe apresentar os respectivos comprovantes de recolhimento sempre que solicitado pela CONTRATANTE.    
</p>
<h4>Cláusula Sétima - CONFIDENCIALIDADE</h4>
<p>
    7.1 - A CONTRATADA, por si, por seus prepostos e empregados, obriga-se a manter absoluto sigilo, durante toda a vigência do Contrato e pelo prazo de 5 (cinco) anos contados de seu encerramento, 
    sobre todas as informações confidenciais, de uso exclusivo da CONTRATANTE, obtidas em razão do exercício direto ou indireto de suas atividades.    
</p>
<p>    
    7.2 - Para os fins do termo mencionado na cláusula anterior, "Informação Confidencial" significa qualquer informação relacionada aos projetos e estudos da CONTRATANTE, incluindo, sem se limitar a: 
    pesquisas, relatórios, avaliações e pareceres elaborados com base em qualquer Informação tida como confidencial pela CONTRATANTE, senhas, estratégias, segredos comerciais e propriedade intelectual, 
    os quais a CONTRATADA possa ter acesso por e-mail, carta, correspondência, telefone, conference call ou em reuniões e encontros realizados em nome da CONTRATANTE.
</p>
<p>    
    7.3 - A CONTRATADA concorda que todos os segredos e informações confidenciais aos quais tenha tido acesso, em razão da prestação dos serviços ora contratados, 
    são de propriedade da CONTRATANTE, obrigando-se a devolvê-las imediatamente à CONTRATANTE, quando da rescisão do presente Contrato.
</p> 
<p>    
    7.4 - Caso a CONTRATADA descumpra a obrigação elencada na cláusula sétima, arcará com uma multa indenizatória em favor da CONTRATANTE, cujo valor será apurado pela CONTRATANTE, 
    no momento do conhecimento da infração, a seu exclusivo critério.
</p>
<h4>Cláusula Oitava - INDEPENDÊNCIA ENTRE AS PARTES</h4>
<p>
    8.1 - A CONTRATADA é a única responsável pelas reclamações trabalhistas, previdenciárias, fiscais e securitárias, incluindo-se aquelas decorrentes de modificações na legislação em vigor, 
    relativamente aos seus empregados e prepostos, ou terceiros por ela contratados, envolvidos direta ou indiretamente na prestação dos serviços objeto do presente Contrato.   
</p>
<p>    
    8.2 - O presente contrato não induz exclusividade entre as partes, podendo as mesmas se relacionar ou contratar terceiros, inclusive com o mesmo objeto.
</p>
<h4>Cláusula Nona - DA RESPONSABILIDADE</h4>
<p>
    9.1 - A CONTRATADA por si, bem como solidariamente na pessoa de seus sócios, prepostos, empregados, agentes e colaboradores, se responsabilizam pela qualidade dos serviços prestados, 
    respondendo em caso de descumprimento, pelos prejuízos causados à CONTRATANTE e à terceiros, inclusive por erro médico.
</p>
<p>
    9.2 - Como forma de identificação da experiência e qualificação, a CONTRATADA deverá apresentar local de atendimento, consultórios e referências de trabalho onde vem sendo prestado.    
</p>
<h4>Cláusula Décima - DISPOSIÇÕES GERAIS</h4>
<p>
    10.1 - Notificações: Todas as notificações e comunicações relativas a este Contrato serão feitas através dos gestores das Partes e enviadas para os endereços indicados no preâmbulo do presente Contrato.    
</p>
<p>
    10.2 - Novação: O não exercício, pelas Partes, de quaisquer dos direitos ou prerrogativas previstos neste Contrato, ou mesmo na legislação aplicável, 
    será tido como ato de mera liberalidade, não constituindo alteração ou novação das obrigações ora estabelecidas, cujo cumprimento poderá ser exigido a qualquer tempo, independentemente de comunicação prévia à Parte.    
</p>
<p>    
    10.3 - Caso Fortuito e Força Maior: Nenhuma das Partes será responsável por descumprimento de suas obrigações contratuais em conseqüência de caso fortuito ou força maior, 
    nos termos da legislação em vigor, devendo, para tanto, comunicar a ocorrência de tal fato de imediato à outra Parte e informar os efeitos danosos do evento. 
</p>
<p>Parágrafo único - Constatada a ocorrência de caso fortuito ou de força maior, ficarão suspensas, enquanto essa perdurar, as obrigações que as Partes ficarem impedidas de cumprir.</p>
<p>
    10.4 - Subcontratação e Cessão: É vedado à CONTRATADA a subcontratação ou cessão, total ou parcial, dos direitos e obrigações oriundos e/ou decorrentes deste Contrato, 
    inclusive seus créditos, sem a prévia e expressa autorização da CONTRATANTE.    
</p>
<p>
    10.5 - Aditivos: Este Contrato só poderá ser alterado, em qualquer de suas disposições, mediante a celebração, por escrito, de termo aditivo contratual assinado por ambas as Partes.    
</p>
<h4>Cláusula Décima Primeira - FORO</h4>
<p>11.1 - Elegem as partes o Foro da Comarca de Rio de Janeiro, Estado de Rio de Janeiro, para dirimir quaisquer controvérsias relacionadas ao presente Contrato, prevalecendo este sobre qualquer outro, por mais privilegiado que seja.</p>

<br><br><br><br>
<p class="center">Rio de Janeiro, <?= $prestador['dia_contratado']; ?> de <?= $meses[str_pad($prestador['mes_contratado'], 2, '0', STR_PAD_LEFT)]; ?> de <?= $prestador['ano_contratado']; ?>.</p>
<br><br><br><br>
<div class="f-left w-metade">
    <h4 class="underline">CONTRATANTE:</h4><br><br><br><br>
</div>
<div class="f-left">
    <h4 class="underline">CONTRATADA:</h4><br><br><br><br>
</div>
<div class="clear"></div>
<br>
<br>
<br>
<br>
<br>
<br>

<div class="textbold">
    <p>Testemunhas</p>
    <p>1.</p>
    <p>Nome: _____________________________</p>
    <p>RG:&nbsp;&nbsp;&nbsp; _____________________________</p>

    <h4>CONTRATADA</h4>
    <p>&nbsp;</p>
    <p>2.</p>
    <p>Nome: _____________________________</p>
    <p>RG:&nbsp;&nbsp;&nbsp; _____________________________</p>
</div>
<br>
<br>
<br>
<br>
<br>
<br>