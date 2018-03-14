<h2 class="titulo">CONTRATO DE PRESTA��O DE SERVI�OS</h2> 
<p>
    Pelo presente instrumento particular, de um lado, <strong><?= trim($prestador['contratante']); ?></strong>, 
    pessoa jur�dica de direito privado, CNPJ n� <?= $prestador['cnpj_contratante']; ?>, 
    com sede na cidade de <?= trim($prestador['municipio_contratante']); ?> - <?= trim($prestador['uf_contratante']); ?>, 
    localizada na <?= trim($prestador['logradouro_contratante']); ?>, CEP <?= trim($prestador['cep_contratante']); ?> 
    doravante denominada <strong>CONTRATANTE</strong>, e de outro, <strong><?= trim($prestador['nome_fantasia']); ?></strong>, 
    pessoa jur�dica de direito privado, CNPJ <?= trim($prestador['cnpj']); ?>, com sede na 
    <?= trim(strtoupper($prestador['endereco'])); ?>, neste ato representada, na forma de seu Contrato Social, 
    por seu representante legal <?= trim(strtoupper($prestador['prestador_responsavel'])); ?>, 
    portador da C�dula de Identidade RG n� <?= trim(strtoupper($prestador['prestador_rg'])); ?> e inscrito no CPF/MF sob o n� 
    <?= trim(strtoupper($prestador['prestador_cpf'])); ?>, 
    doravante denominada <strong>CONTRATADA;</strong>
</p>
<p>Firmam entre si, o presente contrato de presta��o de servi�os m�dicos, mediante as seguintes cl�usulas e condi��es:</p>
<h4>Cl�usula Primeira - OBJETO</h4>
<p>
    <strong>1.1</strong> - O objeto do presente Contrato refere-se � disponibiliza��o de m�o de obra especializada 
    na �rea m�dica para presta��o de servi�os na unidade <?= $prestador['nome_projeto']; ?>, em parceira com a 
    Prefeitura de <?= $prestador['municipio']; ?>.</p>
<p>
    <strong>1.2</strong> - A Contratante mediante conv�nio celebrado com a Prefeitura Municipal de <?= $prestador['municipio']; ?>, 
    pela Secretaria Municipal de Sa�de, � gestora de uma Unidade de Atendimento M�dico denominado <?= $prestador['nome_projeto']; ?>, devendo em virtude disto, 
    atender aos padr�es fixados pela pol�tica de atendimento p�blico � sa�de, comprometendo-se a Contratada a colaborar para atingir 
    tais objetivos, com os servi�os descritos na cl�usula 1.1.
</p>
<p>
    <strong>Par�grafo �nico</strong> - Os servi�os mencionados no <i>caput</i>, 
    prestados pela Contratada, ser�o realizados na �rea de especialidade de <?= $prestador['especialidade']; ?>.
</p>
<h4>Cl�usula Segunda - DA PRESTA��O DE SERVI�OS</h4>
<p><strong>2.1</strong> - Os servi�os contratados poder�o ser executados pelos profissionais m�dicos, s�cios quotistas da empresa Contratada ou por outro s�cio ou empregado, desde que de reconhecida idoneidade e qualifica��o t�cnica especializada.</p>
<p><strong>2.2</strong> - A CONTRATADA indica para a realiza��o dos servi�os contratados, o m�dico Dr. <?= $prestador['nome_medico'] ?>, portador do CRM n� <?= $prestador['crm'] ?>.</p>
<p><strong>2.3</strong> - Na hip�tese da CONTRATADA substituir o profissional indicado no item 2.2, dever� por escrito comunicar o nome do novo profissional � CONTRATANTE, com uma anteced�ncia de 60 (sessenta) dias antes da substitui��o. O prazo poder� ser alterado desde que de comum acordo entre as partes.</p>
<p>2.4 - A CONTRATADA dever� prestar os servi�os objeto da presente contrata��o, dentro das depend�ncias da CONTRATANTE, em car�ter n�o habitual.</p>
<p><strong>2.5</strong> - Visando atender a Pol�tica de Atendimento P�blico � Sa�de, a CONTRATADA se compromete a prestar seus servi�os de acordo com os agendamentos dos procedimentos realizados pela CONTRATANTE.</p>
<p><strong>2.6</strong> - Quando por qualquer raz�o, a CONTRATADA por meio de seu profissional indicado, n�o puder atender os servi�os contratados por algum per�odo, se compromete a comunicar a CONTRATANTE com 60 (sessenta) dias de anteced�ncia, para que haja tempo h�bil para a contrata��o de outra empresa especializada.</p>
<p><strong>2.7</strong> Caso haja consider�vel aumento no volume de servi�os a serem prestados, a CONTRATANTE se compromete a comunicar a CONTRATADA, com 30 (trinta) dias de anteced�ncia, para que esta se adeque �s necessidades da demanda.</p>
<h4>Cl�usula Terceira - PRAZO E RESCIS�O</h4>
<p><strong>3.1</strong> - O presente Contrato ser� por prazo indeterminado, iniciando sua vig�ncia a partir do dia <?= date('d'); ?> de <?= $meses[date('m')]; ?> de <?= date('Y'); ?>, podendo ser rescindido por qualquer das Partes, a qualquer momento, sem justo motivo, desde que haja pr�via comunica��o expressa, com anteced�ncia m�nima de 30 (trinta) dias.</p>
<p>
    <strong>3.2</strong> - A <strong>CONTRATANTE</strong> poder� rescindir o presente Contrato nas seguintes hip�teses:
    <ul>
        <li><strong>3.2.1</strong> - des�dia da <strong>CONTRATADA</strong> no cumprimento das obriga��es assumidas para com a CONTRATANTE e terceiros;</li>
        <li><strong>3.2.2</strong> - caso haja descumprimento do C�digo de �tica M�dica, � Moral, �tica e boas pr�ticas dos servi�os de sa�de;</li>
        <li><strong>3.2.3</strong> - caso a <strong>CONTRATADA</strong> desrespeite as cl�usulas previstas no presente contrato;</li>
        <li><strong>3.2.4</strong> - caso a <strong>CONTRATADA</strong> por si ou por seus empregados, prepostos ou s�cios, por qualquer ato, meio ou forma, interromper ou tentar suspender, sem motivo justo e legal, ou prejudicar a eficaz e continua presta��o de servi�os;</li>
        <li><strong>3.2.5</strong> - a qualquer tempo e por qualquer motivo, desde que comunique a <strong>CONTRATADA</strong> de tal inten��o, por escrito, com anteced�ncia m�nima de 30 (trinta) dias.</li>  
    </ul>
</p>
<p><strong>3.3</strong> - A <strong>CONTRATADA</strong> poder� rescindir o presente Contrato nas seguintes circunst�ncias:
     <ul>
         <li><strong>3.3.1</strong> - quando a <strong>CONTRATANTE</strong> exigir da <strong>CONTRATADA</strong> atividade que exceda a presta��o dos servi�os objeto do presente contrato;</li>
         <li><strong>3.3.2</strong> - caso a <strong>CONTRATANTE</strong> descumpra quaisquer das cl�usulas previstas no presente Contrato;</li>
         <li><strong>3.3.3</strong> - caso haja decreta��o de fal�ncia, concordata, insolv�ncia ou recupera��o judicial da <strong>CONTRATANTE;</strong></li>
         <li><strong>3.3.4</strong> - por motivos de for�a maior que inviabilizem a continuidade da presta��o dos servi�os em quest�o;</li>
         <li><strong>3.3.5</strong> - a qualquer tempo e por qualquer motivo, desde que comunique a <strong>CONTRATANTE</strong> de tal inten��o, por escrito, com anteced�ncia m�nima de 30 (trinta) dias.</li>         
     </ul>
</p>
<p><strong>3.4</strong> - A rescis�o do presente Contrato n�o extingue os direitos e obriga��es que as Partes tenham entre si e perante terceiros, adquiridas anteriormente.</p>
<h4>Cl�usula Quarta - REMUNERA��O</h4>
<p><strong>4.1</strong> - Em pagamento aos servi�os prestados ser� pago � <strong>CONTRATADA</strong> o valor de at� R$ <?= !empty($prestador['valor_limite']) ? number_format($prestador['valor_limite'], 2, ',', '.') : '0,00'; ?> (<?= valor_extenso(!empty($prestador['valor_limite']) ? $prestador['valor_limite'] : '0.00' ); ?>)  por hora de efetivo servi�o prestado.</p>
<p><strong>Par�grafo Primeiro</strong> - Para que a <strong>CONTRATANTE</strong> possa proceder ao pagamento da remunera��o prevista na cl�usula 5.1, a <strong>CONTRATADA</strong> dever� encaminhar � <strong>CONTRATANTE</strong> a respectiva Nota Fiscal de Presta��o de Servi�os, acompanhada de relat�rio detalhado contendo a descri��o dos servi�os prestados, at� o dia 02 de cada m�s.</p>
<h4>Cl�usula Quinta - REAJUSTE ANUAL</h4>
<p><strong>5.1</strong> - Decorrido um prazo de 12 (doze) meses da presente contrata��o e, havendo interesse da <strong>CONTRATANTE</strong> na continuidade da presta��o dos servi�os da <strong>CONTRATADA</strong>, o valor da remunera��o ser� reajustado de comum acordo.</p>
<h4>Cl�usula Sexta - TRIBUTOS</h4>
<p><strong>6.1</strong> - Todos os tributos federais, estaduais ou municipais que incidam sobre a presta��o dos servi�os objeto do presente Contrato, ser�o de exclusiva responsabilidade da <strong>CONTRATADA</strong>, cabendo-lhe apresentar os respectivos comprovantes de recolhimento sempre que solicitado pela <strong>CONTRATANTE</strong>.</p>
<h4>Cl�usula S�tima - CONFIDENCIALIDADE</h4>
<p><strong>7.1</strong> - A <strong>CONTRATADA</strong>, por si, por seus prepostos e empregados, obriga-se a manter absoluto sigilo, durante toda a vig�ncia do Contrato e pelo prazo de 5 (cinco) anos contados de seu encerramento, sobre todas as informa��es confidenciais, de uso exclusivo da <strong>CONTRATANTE</strong>, obtidas em raz�o do exerc�cio direto ou indireto de suas atividades.</p>
<p><strong>7.2</strong> - Para os fins do termo mencionado na cl�usula anterior, "Informa��o Confidencial" significa qualquer informa��o relacionada aos projetos e estudos da <strong>CONTRATANTE</strong>, incluindo, sem se limitar a: pesquisas, relat�rios, avalia��es e pareceres elaborados com base em qualquer Informa��o tida como confidencial pela <strong>CONTRATANTE</strong>, senhas, estrat�gias, segredos comerciais e propriedade intelectual, os quais a <strong>CONTRATADA</strong> possa ter acesso por e-mail, carta, correspond�ncia, telefone, conference call ou em reuni�es e encontros realizados em nome da <strong>CONTRATANTE</strong>.</p>
<p><strong>7.3</strong> - A <strong>CONTRATADA</strong> concorda que todos os segredos e informa��es confidenciais aos quais tenha tido acesso, em raz�o da presta��o dos servi�os ora contratados, s�o de propriedade da <strong>CONTRATANTE</strong>, obrigando-se a devolv�-las imediatamente � <strong>CONTRATANTE</strong>, quando da rescis�o do presente Contrato.</p>
<p><strong>7.4</strong> - Caso a <strong>CONTRATADA</strong> descumpra a obriga��o elencada na cl�usula s�tima, arcar� com uma multa indenizat�ria em favor da <strong>CONTRATANTE</strong>, cujo valor ser� apurado pela <strong>CONTRATANTE</strong>, no momento do conhecimento da infra��o, a seu exclusivo crit�rio.</p>
<h4><strong>Cl�usula Oitav</strong> - INDEPEND�NCIA ENTRE AS PARTES</h4>
<p><strong>8.1</strong> - A <strong>CONTRATADA</strong> � a �nica respons�vel pelas reclama��es trabalhistas, previdenci�rias, fiscais e securit�rias, incluindo-se aquelas decorrentes de modifica��es na legisla��o em vigor, relativamente aos seus empregados e prepostos, ou terceiros por ela contratados, envolvidos direta ou indiretamente na presta��o dos servi�os objeto do presente Contrato.</p>
<p><strong>8.2</strong> - O presente contrato n�o induz exclusividade entre as partes, podendo as mesmas se relacionar ou contratar terceiros, inclusive com o mesmo objeto.</p>
<h4>Cl�usula Nona - DA RESPONSABILIDADE</h4>
<p><strong>9.1</strong> - A <strong>CONTRATADA</strong> por si, bem como solidariamente na pessoa de seus s�cios, prepostos, empregados, agentes e colaboradores, se responsabilizam pela qualidade dos servi�os prestados, respondendo em caso de descumprimento, pelos preju�zos causados � <strong>CONTRATANTE</strong> e � terceiros, inclusive por erro m�dico.</p>
<p><strong>9.2</strong> - Como forma de identifica��o da experi�ncia e qualifica��o, a <strong>CONTRATADA</strong> dever� apresentar local de atendimento, consult�rios e refer�ncias de trabalho onde vem sendo prestado.</p>
<h4>Cl�usula D�cima - DISPOSI��ES GERAIS</h4>
<p><strong>10.1 - Notifica��es:</strong> Todas as notifica��es e comunica��es relativas a este Contrato ser�o feitas atrav�s dos gestores das Partes e enviadas para os endere�os indicados no pre�mbulo do presente Contrato.</p>
<p><strong>10.2 - Nova��o:</strong> O n�o exerc�cio, pelas Partes, de quaisquer dos direitos ou prerrogativas previstos neste Contrato, ou mesmo na legisla��o aplic�vel, ser� tido como ato de mera liberalidade, n�o constituindo altera��o ou nova��o das obriga��es ora estabelecidas, cujo cumprimento poder� ser exigido a qualquer tempo, independentemente de comunica��o pr�via � Parte.</p>
<p><strong>10.3 - Caso Fortuito e For�a Maior:</strong> Nenhuma das Partes ser� respons�vel por descumprimento de suas obriga��es contratuais em conseq��ncia de caso fortuito ou for�a maior, nos termos da legisla��o em vigor, devendo, para tanto, comunicar a ocorr�ncia de tal fato de imediato � outra Parte e informar os efeitos danosos do evento.</p>
<p><strong>Par�grafo �nico</strong> - Constatada a ocorr�ncia de caso fortuito ou de for�a maior, ficar�o suspensas, enquanto essa perdurar, as obriga��es que as Partes ficarem impedidas de cumprir.</p>
<p><strong>10.4 - Subcontrata��o e Cess�o:</strong> � vedado � <strong>CONTRATADA</strong> a subcontrata��o ou cess�o, total ou parcial, dos direitos e obriga��es oriundos e/ou decorrentes deste Contrato, inclusive seus cr�ditos, sem a pr�via e expressa autoriza��o da <strong>CONTRATANTE</strong>.</p>
<p><strong>10.5 - Aditivos:</strong> Este Contrato s� poder� ser alterado, em qualquer de suas disposi��es, mediante a celebra��o, por escrito, de termo aditivo contratual assinado por ambas as Partes.</p>
<h4>Cl�usula D�cima Primeira - FORO</h4>
<p><strong>11.1</strong> - Elegem as partes o Foro da Comarca de Bebedouro, Estado de S�o Paulo, para dirimir quaisquer controv�rsias relacionadas ao presente Contrato, prevalecendo este sobre qualquer outro, por mais privilegiado que seja. </p>
<p>E por estarem assim justos e contratados, assinam o presente em duas vias de igual forma e teor, na presen�a de duas testemunhas, para que possa produzir todos os seus efeitos de direito.</p>

<br><br><br><br>
<p class="center">Bebedouro, <?= date('d'); ?> de <?= $meses[str_pad(date('m'),2,'0')]; ?> de <?= date('Y'); ?>.</p>
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

    <h4>Contratada</h4>
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