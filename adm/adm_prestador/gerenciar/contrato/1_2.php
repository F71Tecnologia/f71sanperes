<h2 class="titulo">CONTRATO DE PRESTAÇÃO DE SERVIÇOS</h2> 
<p>
    Pelo presente instrumento particular, de um lado, <strong><?= trim($prestador['contratante']); ?></strong>, 
    pessoa jurídica de direito privado, CNPJ nº <?= $prestador['cnpj_contratante']; ?>, 
    com sede na cidade de <?= trim($prestador['municipio_contratante']); ?> - <?= trim($prestador['uf_contratante']); ?>, 
    localizada na <?= trim($prestador['logradouro_contratante']); ?>, CEP <?= trim($prestador['cep_contratante']); ?> 
    doravante denominada <strong>CONTRATANTE</strong>, e de outro, <strong><?= trim($prestador['nome_fantasia']); ?></strong>, 
    pessoa jurídica de direito privado, CNPJ <?= trim($prestador['cnpj']); ?>, com sede na 
    <?= trim(strtoupper($prestador['endereco'])); ?>, neste ato representada, na forma de seu Contrato Social, 
    por seu representante legal <?= trim(strtoupper($prestador['prestador_responsavel'])); ?>, 
    portador da Cédula de Identidade RG nº <?= trim(strtoupper($prestador['prestador_rg'])); ?> e inscrito no CPF/MF sob o nº 
    <?= trim(strtoupper($prestador['prestador_cpf'])); ?>, 
    doravante denominada <strong>CONTRATADA;</strong>
</p>
<p>Firmam entre si, o presente contrato de prestação de serviços médicos, mediante as seguintes cláusulas e condições:</p>
<h4>Cláusula Primeira - OBJETO</h4>
<p>
    <strong>1.1</strong> - O objeto do presente Contrato refere-se à disponibilização de mão de obra especializada 
    na área médica para prestação de serviços na unidade <?= $prestador['nome_projeto']; ?>, em parceira com a 
    Prefeitura de <?= $prestador['municipio']; ?>.</p>
<p>
    <strong>1.2</strong> - A Contratante mediante convênio celebrado com a Prefeitura Municipal de <?= $prestador['municipio']; ?>, 
    pela Secretaria Municipal de Saúde, é gestora de uma Unidade de Atendimento Médico denominado <?= $prestador['nome_projeto']; ?>, devendo em virtude disto, 
    atender aos padrões fixados pela política de atendimento público à saúde, comprometendo-se a Contratada a colaborar para atingir 
    tais objetivos, com os serviços descritos na cláusula 1.1.
</p>
<p>
    <strong>Parágrafo único</strong> - Os serviços mencionados no <i>caput</i>, 
    prestados pela Contratada, serão realizados na área de especialidade de <?= $prestador['especialidade']; ?>.
</p>
<h4>Cláusula Segunda - DA PRESTAÇÃO DE SERVIÇOS</h4>
<p><strong>2.1</strong> - Os serviços contratados poderão ser executados pelos profissionais médicos, sócios quotistas da empresa Contratada ou por outro sócio ou empregado, desde que de reconhecida idoneidade e qualificação técnica especializada.</p>
<p><strong>2.2</strong> - A CONTRATADA indica para a realização dos serviços contratados, o médico Dr. <?= $prestador['nome_medico'] ?>, portador do CRM n° <?= $prestador['crm'] ?>.</p>
<p><strong>2.3</strong> - Na hipótese da CONTRATADA substituir o profissional indicado no item 2.2, deverá por escrito comunicar o nome do novo profissional à CONTRATANTE, com uma antecedência de 60 (sessenta) dias antes da substituição. O prazo poderá ser alterado desde que de comum acordo entre as partes.</p>
<p>2.4 - A CONTRATADA deverá prestar os serviços objeto da presente contratação, dentro das dependências da CONTRATANTE, em caráter não habitual.</p>
<p><strong>2.5</strong> - Visando atender a Política de Atendimento Público à Saúde, a CONTRATADA se compromete a prestar seus serviços de acordo com os agendamentos dos procedimentos realizados pela CONTRATANTE.</p>
<p><strong>2.6</strong> - Quando por qualquer razão, a CONTRATADA por meio de seu profissional indicado, não puder atender os serviços contratados por algum período, se compromete a comunicar a CONTRATANTE com 60 (sessenta) dias de antecedência, para que haja tempo hábil para a contratação de outra empresa especializada.</p>
<p><strong>2.7</strong> Caso haja considerável aumento no volume de serviços a serem prestados, a CONTRATANTE se compromete a comunicar a CONTRATADA, com 30 (trinta) dias de antecedência, para que esta se adeque às necessidades da demanda.</p>
<h4>Cláusula Terceira - PRAZO E RESCISÃO</h4>
<p><strong>3.1</strong> - O presente Contrato será por prazo indeterminado, iniciando sua vigência a partir do dia <?= date('d'); ?> de <?= $meses[date('m')]; ?> de <?= date('Y'); ?>, podendo ser rescindido por qualquer das Partes, a qualquer momento, sem justo motivo, desde que haja prévia comunicação expressa, com antecedência mínima de 30 (trinta) dias.</p>
<p>
    <strong>3.2</strong> - A <strong>CONTRATANTE</strong> poderá rescindir o presente Contrato nas seguintes hipóteses:
    <ul>
        <li><strong>3.2.1</strong> - desídia da <strong>CONTRATADA</strong> no cumprimento das obrigações assumidas para com a CONTRATANTE e terceiros;</li>
        <li><strong>3.2.2</strong> - caso haja descumprimento do Código de Ética Médica, à Moral, Ética e boas práticas dos serviços de saúde;</li>
        <li><strong>3.2.3</strong> - caso a <strong>CONTRATADA</strong> desrespeite as cláusulas previstas no presente contrato;</li>
        <li><strong>3.2.4</strong> - caso a <strong>CONTRATADA</strong> por si ou por seus empregados, prepostos ou sócios, por qualquer ato, meio ou forma, interromper ou tentar suspender, sem motivo justo e legal, ou prejudicar a eficaz e continua prestação de serviços;</li>
        <li><strong>3.2.5</strong> - a qualquer tempo e por qualquer motivo, desde que comunique a <strong>CONTRATADA</strong> de tal intenção, por escrito, com antecedência mínima de 30 (trinta) dias.</li>  
    </ul>
</p>
<p><strong>3.3</strong> - A <strong>CONTRATADA</strong> poderá rescindir o presente Contrato nas seguintes circunstâncias:
     <ul>
         <li><strong>3.3.1</strong> - quando a <strong>CONTRATANTE</strong> exigir da <strong>CONTRATADA</strong> atividade que exceda a prestação dos serviços objeto do presente contrato;</li>
         <li><strong>3.3.2</strong> - caso a <strong>CONTRATANTE</strong> descumpra quaisquer das cláusulas previstas no presente Contrato;</li>
         <li><strong>3.3.3</strong> - caso haja decretação de falência, concordata, insolvência ou recuperação judicial da <strong>CONTRATANTE;</strong></li>
         <li><strong>3.3.4</strong> - por motivos de força maior que inviabilizem a continuidade da prestação dos serviços em questão;</li>
         <li><strong>3.3.5</strong> - a qualquer tempo e por qualquer motivo, desde que comunique a <strong>CONTRATANTE</strong> de tal intenção, por escrito, com antecedência mínima de 30 (trinta) dias.</li>         
     </ul>
</p>
<p><strong>3.4</strong> - A rescisão do presente Contrato não extingue os direitos e obrigações que as Partes tenham entre si e perante terceiros, adquiridas anteriormente.</p>
<h4>Cláusula Quarta - REMUNERAÇÃO</h4>
<p><strong>4.1</strong> - Em pagamento aos serviços prestados será pago à <strong>CONTRATADA</strong> o valor de até R$ <?= !empty($prestador['valor_limite']) ? number_format($prestador['valor_limite'], 2, ',', '.') : '0,00'; ?> (<?= valor_extenso(!empty($prestador['valor_limite']) ? $prestador['valor_limite'] : '0.00' ); ?>)  por hora de efetivo serviço prestado.</p>
<p><strong>Parágrafo Primeiro</strong> - Para que a <strong>CONTRATANTE</strong> possa proceder ao pagamento da remuneração prevista na cláusula 5.1, a <strong>CONTRATADA</strong> deverá encaminhar à <strong>CONTRATANTE</strong> a respectiva Nota Fiscal de Prestação de Serviços, acompanhada de relatório detalhado contendo a descrição dos serviços prestados, até o dia 02 de cada mês.</p>
<h4>Cláusula Quinta - REAJUSTE ANUAL</h4>
<p><strong>5.1</strong> - Decorrido um prazo de 12 (doze) meses da presente contratação e, havendo interesse da <strong>CONTRATANTE</strong> na continuidade da prestação dos serviços da <strong>CONTRATADA</strong>, o valor da remuneração será reajustado de comum acordo.</p>
<h4>Cláusula Sexta - TRIBUTOS</h4>
<p><strong>6.1</strong> - Todos os tributos federais, estaduais ou municipais que incidam sobre a prestação dos serviços objeto do presente Contrato, serão de exclusiva responsabilidade da <strong>CONTRATADA</strong>, cabendo-lhe apresentar os respectivos comprovantes de recolhimento sempre que solicitado pela <strong>CONTRATANTE</strong>.</p>
<h4>Cláusula Sétima - CONFIDENCIALIDADE</h4>
<p><strong>7.1</strong> - A <strong>CONTRATADA</strong>, por si, por seus prepostos e empregados, obriga-se a manter absoluto sigilo, durante toda a vigência do Contrato e pelo prazo de 5 (cinco) anos contados de seu encerramento, sobre todas as informações confidenciais, de uso exclusivo da <strong>CONTRATANTE</strong>, obtidas em razão do exercício direto ou indireto de suas atividades.</p>
<p><strong>7.2</strong> - Para os fins do termo mencionado na cláusula anterior, "Informação Confidencial" significa qualquer informação relacionada aos projetos e estudos da <strong>CONTRATANTE</strong>, incluindo, sem se limitar a: pesquisas, relatórios, avaliações e pareceres elaborados com base em qualquer Informação tida como confidencial pela <strong>CONTRATANTE</strong>, senhas, estratégias, segredos comerciais e propriedade intelectual, os quais a <strong>CONTRATADA</strong> possa ter acesso por e-mail, carta, correspondência, telefone, conference call ou em reuniões e encontros realizados em nome da <strong>CONTRATANTE</strong>.</p>
<p><strong>7.3</strong> - A <strong>CONTRATADA</strong> concorda que todos os segredos e informações confidenciais aos quais tenha tido acesso, em razão da prestação dos serviços ora contratados, são de propriedade da <strong>CONTRATANTE</strong>, obrigando-se a devolvê-las imediatamente à <strong>CONTRATANTE</strong>, quando da rescisão do presente Contrato.</p>
<p><strong>7.4</strong> - Caso a <strong>CONTRATADA</strong> descumpra a obrigação elencada na cláusula sétima, arcará com uma multa indenizatória em favor da <strong>CONTRATANTE</strong>, cujo valor será apurado pela <strong>CONTRATANTE</strong>, no momento do conhecimento da infração, a seu exclusivo critério.</p>
<h4><strong>Cláusula Oitav</strong> - INDEPENDÊNCIA ENTRE AS PARTES</h4>
<p><strong>8.1</strong> - A <strong>CONTRATADA</strong> é a única responsável pelas reclamações trabalhistas, previdenciárias, fiscais e securitárias, incluindo-se aquelas decorrentes de modificações na legislação em vigor, relativamente aos seus empregados e prepostos, ou terceiros por ela contratados, envolvidos direta ou indiretamente na prestação dos serviços objeto do presente Contrato.</p>
<p><strong>8.2</strong> - O presente contrato não induz exclusividade entre as partes, podendo as mesmas se relacionar ou contratar terceiros, inclusive com o mesmo objeto.</p>
<h4>Cláusula Nona - DA RESPONSABILIDADE</h4>
<p><strong>9.1</strong> - A <strong>CONTRATADA</strong> por si, bem como solidariamente na pessoa de seus sócios, prepostos, empregados, agentes e colaboradores, se responsabilizam pela qualidade dos serviços prestados, respondendo em caso de descumprimento, pelos prejuízos causados à <strong>CONTRATANTE</strong> e à terceiros, inclusive por erro médico.</p>
<p><strong>9.2</strong> - Como forma de identificação da experiência e qualificação, a <strong>CONTRATADA</strong> deverá apresentar local de atendimento, consultórios e referências de trabalho onde vem sendo prestado.</p>
<h4>Cláusula Décima - DISPOSIÇÕES GERAIS</h4>
<p><strong>10.1 - Notificações:</strong> Todas as notificações e comunicações relativas a este Contrato serão feitas através dos gestores das Partes e enviadas para os endereços indicados no preâmbulo do presente Contrato.</p>
<p><strong>10.2 - Novação:</strong> O não exercício, pelas Partes, de quaisquer dos direitos ou prerrogativas previstos neste Contrato, ou mesmo na legislação aplicável, será tido como ato de mera liberalidade, não constituindo alteração ou novação das obrigações ora estabelecidas, cujo cumprimento poderá ser exigido a qualquer tempo, independentemente de comunicação prévia à Parte.</p>
<p><strong>10.3 - Caso Fortuito e Força Maior:</strong> Nenhuma das Partes será responsável por descumprimento de suas obrigações contratuais em conseqüência de caso fortuito ou força maior, nos termos da legislação em vigor, devendo, para tanto, comunicar a ocorrência de tal fato de imediato à outra Parte e informar os efeitos danosos do evento.</p>
<p><strong>Parágrafo único</strong> - Constatada a ocorrência de caso fortuito ou de força maior, ficarão suspensas, enquanto essa perdurar, as obrigações que as Partes ficarem impedidas de cumprir.</p>
<p><strong>10.4 - Subcontratação e Cessão:</strong> É vedado à <strong>CONTRATADA</strong> a subcontratação ou cessão, total ou parcial, dos direitos e obrigações oriundos e/ou decorrentes deste Contrato, inclusive seus créditos, sem a prévia e expressa autorização da <strong>CONTRATANTE</strong>.</p>
<p><strong>10.5 - Aditivos:</strong> Este Contrato só poderá ser alterado, em qualquer de suas disposições, mediante a celebração, por escrito, de termo aditivo contratual assinado por ambas as Partes.</p>
<h4>Cláusula Décima Primeira - FORO</h4>
<p><strong>11.1</strong> - Elegem as partes o Foro da Comarca de Bebedouro, Estado de São Paulo, para dirimir quaisquer controvérsias relacionadas ao presente Contrato, prevalecendo este sobre qualquer outro, por mais privilegiado que seja. </p>
<p>E por estarem assim justos e contratados, assinam o presente em duas vias de igual forma e teor, na presença de duas testemunhas, para que possa produzir todos os seus efeitos de direito.</p>

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