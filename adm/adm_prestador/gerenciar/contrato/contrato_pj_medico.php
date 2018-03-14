<h2 class="titulo">CONTRATO DE PRESTA��O DE SERVI�OS M�DICOS</h2> 
<?php
if (isset($_GET['dev'])) {
    echo '<pre>';
    print_r($prestador);
    echo '</pre>';
}
?>
<p>
    Pelo presente instrumento particular, de um lado, <strong><?= trim($prestador['contratante']); ?></strong>, 
    pessoa jur�dica de direito privado, devidamente inscrita no CNPJ sob o n� <?= $prestador['cnpj_contratante']; ?>, 
    com sede na cidade de <?= trim($prestador['municipio_contratante']); ?> - <?= trim($prestador['uf_contratante']); ?>, 
    localizada na <?= trim($prestador['logradouro_contratante']); ?>, CEP <?= trim($prestador['cep_contratante']); ?>, 
    doravante denominada <strong>CONTRATANTE</strong>, e de outro, <strong><?= trim($prestador['nome_fantasia']); ?></strong>, 
    pessoa jur�dica de direito privado, CNPJ <?= trim($prestador['cnpj']); ?>, com sede na <?= trim(strtoupper($prestador['endereco'])); ?>
    , neste ato representada, na forma de seu Contrato Social, por <?= trim(strtoupper($prestador['prestador_responsavel'])); ?>, 
    portador da C�dula de Identidade RG n� <?= trim(strtoupper($prestador['prestador_rg'])); ?> e inscrito no CPF/MF sob o n� 
    <?= trim(strtoupper($prestador['prestador_cpf'])); ?>,  doravante denominada <strong>CONTRATADA</strong>;  
</p>
<p>Firmam entre si, o presente contrato de presta��o de servi�os m�dicos, mediante as seguintes cl�usulas e condi��es:</p>
<h4>Cl�usula Primeira - DO OBJETO</h4>
<p>
    1.1 - O objeto do presente Contrato refere-se � disponibiliza��o de m�o de obra especializada na �rea m�dica para presta��o de servi�os, 
    em parceira com a Prefeitura Municipal de <?= $prestador['municipio']; ?>.
</p>
<p>
    1.2 - A CONTRATANTE mediante Contrato de Gest�o celebrado com a Prefeitura Municipal de <?= $prestador['municipio']; ?>, pelo Departamento Municipal de Sa�de, devendo em virtude disto, 
    atender aos padr�es fixados pela pol�tica de atendimento p�blico � sa�de, comprometendo-se a CONTRATADA a colaborar para atingir tais objetivos, com os servi�os descritos na cl�usula 1.1.</p>



<?php
$total_cont = count($medicos_funcoes);
$final_especialidade = ($total_cont > 1) ? 's' : '';
?>
<p>Par�grafo �nico - Os servi�os mencionados no caput, prestados pela CONTRATADA, ser�o realizados na<?= $final_especialidade; ?> �rea<?= $final_especialidade; ?> de especialidade<?= $final_especialidade; ?> de 

    <?php
    $cont = 1;
    foreach ($medicos_funcoes as $funcao) {
        $final = ($total_cont == $cont) ? '. ' : (($total_cont == ($cont + 1)) ? ' e ' : ', ');
        echo strtoupper($funcao['nome_curso']) . $final;
        $cont++;
    }
    ?>  

</p>
<h4>Cl�usula Segunda - DA PRESTA��O DE SERVI�OS</h4>
<p>
    2.1 - Os servi�os contratados poder�o ser executados pelos profissionais m�dicos, s�cios quotistas da empresa CONTRATADA ou por outro s�cio ou empregado, 
    desde que de reconhecida idoneidade e qualifica��o t�cnica especializada.
</p>
<p>
    2.2 - A CONTRATADA indica para a realiza��o dos servi�os contratados, 
    <?php
    if (count($medicos_pj) > 1) {
        echo 'os m�dicos ';
    } else {
        echo 'o m�dico ';
    }
    $cont = 1;
    $total_cont = count($medicos_pj);
    foreach ($medicos_pj as $medico) {
        $final = ($total_cont == $cont) ? '. ' : (($total_cont == ($cont + 1)) ? ' e ' : '; ');
//        $final = ($total_cont==$cont) ? '. ' : ';';
        echo ' Dr.' . strtoupper($medico['nome']) . ', portador do CRM n� ' . $medico['crm'] . $final;
        $cont++;
    }
    ?>
</p>
<p>
    2.3 - Na hip�tese da CONTRATADA substituir o profissional indicado no item 2.2, dever� por escrito comunicar o nome do novo profissional � CONTRATANTE, com uma anteced�ncia de 30 (trinta) 
    dias antes da substitui��o. O prazo poder� ser alterado desde que de comum acordo entre as partes.
</p>
<p>
    2.4 - A CONTRATADA dever� prestar os servi�os objeto da presente contrata��o, dentro das depend�ncias da CONTRATANTE, em car�ter n�o habitual.
</p>
<p>2.5 - Visando atender a Pol�tica de Atendimento P�blico � Sa�de, a CONTRATADA se compromete a prestar seus servi�os de acordo com os agendamentos dos procedimentos realizados pela CONTRATANTE.</p>
<p>
    2.6 - Quando por qualquer raz�o, a CONTRATADA por meio de seu profissional indicado, n�o puder atender os servi�os contratados por algum per�odo, se compromete a comunicar a 
    CONTRATANTE com 30 (trinta) dias de anteced�ncia, para que haja tempo h�bil para a contrata��o de outra empresa especializada.
</p>
<p>    
    2.6.1 - Na hip�tese de n�o haver a pr�via comunica��o, dever� a CONTRATADA pagar uma multa no valor do dobro do plant�o, que ser� descontada na pr�xima Nota Fiscal. 
</p>
<p>    
    2.7 - Caso haja consider�vel aumento no volume de servi�os a serem prestados, a CONTRATANTE se compromete a comunicar a CONTRATADA, com 30 (trinta) 
    dias de anteced�ncia, para que esta se adeque �s necessidades da demanda.
</p>

<h4>Cl�usula Terceira - PRAZO E RESCIS�O</h4>
<p>
    3.1 - O presente Contrato vigorar� at� o t�rmino do Contrato de Gest�o celebrado com a Prefeitura Municipal de <?= $prestador['municipio']; ?>, iniciando sua vig�ncia a partir do dia <?= $prestador['dia_contratado']; ?> de <?= $meses[str_pad($prestador['mes_contratado'], 2, '0', STR_PAD_LEFT)]; ?> de <?= $prestador['ano_contratado']; ?>, podendo ser rescindido por qualquer das Partes, a qualquer momento, sem justo motivo, desde que haja pr�via comunica��o expressa, com anteced�ncia m�nima de 30 (trinta) dias.    
</p>
<h4>3.2 - A CONTRATANTE poder� rescindir o presente Contrato nas seguintes hip�teses:</h4>
<p>
    3.2.1 - des�dia da CONTRATADA no cumprimento das obriga��es assumidas para com a CONTRATANTE e terceiros;
</p>
<p>
    3.2.2 - caso haja descumprimento do C�digo de �tica M�dica, � Moral, �tica e boas pr�ticas dos servi�os de sa�de;    
</p>
<p>
    3.2.3 - caso a CONTRATADA desrespeite as cl�usulas previstas no presente contrato;    
</p>
<p>
    3.2.4 - caso a CONTRATADA por si ou por seus empregados, prepostos ou s�cios, por qualquer ato, meio ou forma, interromper ou tentar suspender, 
    sem motivo justo e legal, ou prejudicar a eficaz e continua presta��o de servi�os;    
</p>
<p>
    3.2.5 - a qualquer tempo e por qualquer motivo, desde que comunique a CONTRATADA de tal inten��o, por escrito, com anteced�ncia m�nima de 30 (trinta) dias.    
</p>
<p>
    3.3 - A CONTRATADA poder� rescindir o presente Contrato nas seguintes circunst�ncias:    
</p>
<p>
    3.3.1 - quando a CONTRATANTE exigir da CONTRATADA atividade que exceda a presta��o dos servi�os objeto do presente contrato;    
</p>
<p>
    3.3.2 - caso a CONTRATANTE descumpra quaisquer das cl�usulas previstas no presente Contrato;    
</p>
<p>
    3.3.3 - caso haja decreta��o de fal�ncia, concordata, insolv�ncia ou recupera��o judicial da CONTRATANTE;    
</p>
<p>
    3.3.4 - por motivos de for�a maior que inviabilizem a continuidade da presta��o dos servi�os em quest�o;    
</p>
<p>
    3.3.5 - a qualquer tempo e por qualquer motivo, desde que comunique a CONTRATANTE de tal inten��o, por escrito, com anteced�ncia m�nima de 60 (sessenta) dias.    
</p>
<p>
    3.4 - A rescis�o do presente Contrato n�o extingue os direitos e obriga��es que as Partes tenham entre si e perante terceiros, adquiridas anteriormente.    
</p>
<h4>Cl�usula Quarta - REMUNERA��O</h4>
<p>
    4.1 - Em pagamento aos servi�os prestados ser� pago � CONTRATADA o valor 

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

    de efetivo servi�o prestado at� o quinto dia �til do m�s subsequente ao vencido.
</p>
<p>
    Par�grafo Primeiro - Para que a CONTRATANTE possa proceder ao pagamento da remunera��o prevista na cl�usula 4.1, a CONTRATADA dever� encaminhar � CONTRATANTE a respectiva Nota Fiscal de Presta��o de Servi�os, 
    acompanhada de relat�rio detalhado contendo a descri��o dos servi�os prestados, at� o dia 02 de cada m�s, assim como as certid�es negativas de FGTS, Previdenci�ria, Divida Ativa da Uni�o e Trabalhista.    
</p>
<h4>Cl�usula Quinta- REAJUSTE ANUAL</h4>
<p>
    5.1 - Decorrido um prazo de 12 (doze) meses da presente contrata��o e, havendo interesse da CONTRATANTE na continuidade da presta��o dos servi�os da CONTRATADA, o valor da remunera��o ser� reajustado de comum acordo.    
</p>
<h4>Cl�usula Sexta- TRIBUTOS</h4>
<p>
    6.1 - Todos os tributos federais, estaduais ou municipais que incidam sobre a presta��o dos servi�os objeto do presente Contrato, ser�o de exclusiva responsabilidade da CONTRATADA, 
    cabendo-lhe apresentar os respectivos comprovantes de recolhimento sempre que solicitado pela CONTRATANTE.    
</p>
<h4>Cl�usula S�tima - CONFIDENCIALIDADE</h4>
<p>
    7.1 - A CONTRATADA, por si, por seus prepostos e empregados, obriga-se a manter absoluto sigilo, durante toda a vig�ncia do Contrato e pelo prazo de 5 (cinco) anos contados de seu encerramento, 
    sobre todas as informa��es confidenciais, de uso exclusivo da CONTRATANTE, obtidas em raz�o do exerc�cio direto ou indireto de suas atividades.    
</p>
<p>    
    7.2 - Para os fins do termo mencionado na cl�usula anterior, "Informa��o Confidencial" significa qualquer informa��o relacionada aos projetos e estudos da CONTRATANTE, incluindo, sem se limitar a: 
    pesquisas, relat�rios, avalia��es e pareceres elaborados com base em qualquer Informa��o tida como confidencial pela CONTRATANTE, senhas, estrat�gias, segredos comerciais e propriedade intelectual, 
    os quais a CONTRATADA possa ter acesso por e-mail, carta, correspond�ncia, telefone, conference call ou em reuni�es e encontros realizados em nome da CONTRATANTE.
</p>
<p>    
    7.3 - A CONTRATADA concorda que todos os segredos e informa��es confidenciais aos quais tenha tido acesso, em raz�o da presta��o dos servi�os ora contratados, 
    s�o de propriedade da CONTRATANTE, obrigando-se a devolv�-las imediatamente � CONTRATANTE, quando da rescis�o do presente Contrato.
</p> 
<p>    
    7.4 - Caso a CONTRATADA descumpra a obriga��o elencada na cl�usula s�tima, arcar� com uma multa indenizat�ria em favor da CONTRATANTE, cujo valor ser� apurado pela CONTRATANTE, 
    no momento do conhecimento da infra��o, a seu exclusivo crit�rio.
</p>
<h4>Cl�usula Oitava - INDEPEND�NCIA ENTRE AS PARTES</h4>
<p>
    8.1 - A CONTRATADA � a �nica respons�vel pelas reclama��es trabalhistas, previdenci�rias, fiscais e securit�rias, incluindo-se aquelas decorrentes de modifica��es na legisla��o em vigor, 
    relativamente aos seus empregados e prepostos, ou terceiros por ela contratados, envolvidos direta ou indiretamente na presta��o dos servi�os objeto do presente Contrato.   
</p>
<p>    
    8.2 - O presente contrato n�o induz exclusividade entre as partes, podendo as mesmas se relacionar ou contratar terceiros, inclusive com o mesmo objeto.
</p>
<h4>Cl�usula Nona - DA RESPONSABILIDADE</h4>
<p>
    9.1 - A CONTRATADA por si, bem como solidariamente na pessoa de seus s�cios, prepostos, empregados, agentes e colaboradores, se responsabilizam pela qualidade dos servi�os prestados, 
    respondendo em caso de descumprimento, pelos preju�zos causados � CONTRATANTE e � terceiros, inclusive por erro m�dico.
</p>
<p>
    9.2 - Como forma de identifica��o da experi�ncia e qualifica��o, a CONTRATADA dever� apresentar local de atendimento, consult�rios e refer�ncias de trabalho onde vem sendo prestado.    
</p>
<h4>Cl�usula D�cima - DISPOSI��ES GERAIS</h4>
<p>
    10.1 - Notifica��es: Todas as notifica��es e comunica��es relativas a este Contrato ser�o feitas atrav�s dos gestores das Partes e enviadas para os endere�os indicados no pre�mbulo do presente Contrato.    
</p>
<p>
    10.2 - Nova��o: O n�o exerc�cio, pelas Partes, de quaisquer dos direitos ou prerrogativas previstos neste Contrato, ou mesmo na legisla��o aplic�vel, 
    ser� tido como ato de mera liberalidade, n�o constituindo altera��o ou nova��o das obriga��es ora estabelecidas, cujo cumprimento poder� ser exigido a qualquer tempo, independentemente de comunica��o pr�via � Parte.    
</p>
<p>    
    10.3 - Caso Fortuito e For�a Maior: Nenhuma das Partes ser� respons�vel por descumprimento de suas obriga��es contratuais em conseq��ncia de caso fortuito ou for�a maior, 
    nos termos da legisla��o em vigor, devendo, para tanto, comunicar a ocorr�ncia de tal fato de imediato � outra Parte e informar os efeitos danosos do evento. 
</p>
<p>Par�grafo �nico - Constatada a ocorr�ncia de caso fortuito ou de for�a maior, ficar�o suspensas, enquanto essa perdurar, as obriga��es que as Partes ficarem impedidas de cumprir.</p>
<p>
    10.4 - Subcontrata��o e Cess�o: � vedado � CONTRATADA a subcontrata��o ou cess�o, total ou parcial, dos direitos e obriga��es oriundos e/ou decorrentes deste Contrato, 
    inclusive seus cr�ditos, sem a pr�via e expressa autoriza��o da CONTRATANTE.    
</p>
<p>
    10.5 - Aditivos: Este Contrato s� poder� ser alterado, em qualquer de suas disposi��es, mediante a celebra��o, por escrito, de termo aditivo contratual assinado por ambas as Partes.    
</p>
<h4>Cl�usula D�cima Primeira - FORO</h4>
<p>11.1 - Elegem as partes o Foro da Comarca de Rio de Janeiro, Estado de Rio de Janeiro, para dirimir quaisquer controv�rsias relacionadas ao presente Contrato, prevalecendo este sobre qualquer outro, por mais privilegiado que seja.</p>

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