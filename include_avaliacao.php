<?php



if(empty($_COOKIE['logado'])){

print "Efetue o Login<br><a href='login.php'>Logar</a> ";

}else{




/*
$radio1 = $_REQUEST['radio1'];

$radio2 = $_REQUEST['radio2'];

$radio3 = $_REQUEST['radio3'];

$radio4 = $_REQUEST['radio4'];

$radio5 = $_REQUEST['radio5'];

$radio6 = $_REQUEST['radio6'];
*/




switch ($radio1) {

    case 1:

    $msg1 = "O funcion�rio demonstrou compet�ncia apenas para cumprir sua rotina, dentro do que era esperado. O funcion�rio n�o realizou tarefas ou n�o teve a iniciativa de a��es que demonstrassem claramente sua compet�ncia.";

    break;



    case 2:

	$msg1 = "S� voc� como superior imediato teve condi��es de avaliar os conhecimentos, habilidades e compet�ncia demonstrados pelo funcion�rio no per�odo. O funcion�rio ainda n�o teve oportunidade de realizar tarefas que demonstrassem para outras pessoas a sua compet�ncia. O reconhecimento da compet�ncia do funcion�rio ficou restrito a seu superior imediato.";

    break;



    case 3:

	$msg1 = "Os conhecimentos, habilidades e compet�ncia demonstrados pelo funcion�rio no per�odo foram reconhecidos pelos colegas da sua equipe.  O reconhecimento pode ter vindo na forma de elogio ou coment�rios sobre a qualidade do servi�o ou das tarefas realizadas pelo funcion�rio. O reconhecimento da compet�ncia do funcion�rio ficou restrito ao seu departamento.";

    break;



    case 4:

	$msg1 = "Os conhecimentos, habilidades e compet�ncia demonstrados pelo funcion�rio no per�odo foram reconhecidos por pelo menos um Diretor, Gerente ou Supervisor de outra �rea. O reconhecimento pode ter vindo na forma de elogio, coment�rio sobre um trabalho realizado, solicita��o para que o funcion�rio fizesse determinada tarefa ou qualquer fato que indicasse o reconhecimento da compet�ncia do avaliado por pessoas de fora do seu departamento. ";

    break;



    }



switch ($radio2) {

    case 1:

    $msg2 = "O funcion�rio s� tomou a iniciativa para adquirir novos conhecimentos ou habilidades quando fortemente incentivado, ou direta ou indiretamente pressionado pelo superior ou outras pessoas para realizar algum curso ou aprender novas tarefas.";

    break;



    case 2:

	$msg2 = "O funcion�rio s� tomou a iniciativa de aprender coisas novas quando apareceu uma oportunidade �nica ou recebeu alguma sugest�o do superior ou outras pessoas para aproveitar determinada oportunidade de fazer um curso fora, participar de um treinamento interno, ler um livro ou aprender novas tarefas. O funcion�rio responde bem �s oportunidades de desenvolvimento profissional apenas quando recebe um leve empurr�o do superior ou outras pessoas.";

    break;



    case 3:

	$msg2 = "O funcion�rio �s vezes tomou a iniciativa para melhorar sua compet�ncia profissional. Isso ficou comprovado pelo grande interesse demonstrado pelo funcion�rio em determinado assunto relacionado com seu trabalho ou sua evolu��o profissional. Pode-se dizer que o funcion�rio responde bem �s oportunidades que a empresa oferece ou sugest�es de outras pessoas sobre o seu desenvolvimento profissional.";

    break;



    case 4:

	$msg2 = "O funcion�rio esteve sempre procurando adquirir novos conhecimentos e habilidades e se desenvolver profissionalmente. Isso ficou comprovado pelos coment�rios fundamentados do funcion�rio sobre leituras e pesquisas que realizou no per�odo, pelos cursos que realizou por iniciativa pr�pria, pelo aproveitamento e assimila��o de conte�dos de treinamentos realizados ou patrocinados pela empresa. Pode-se dizer que o funcion�rio cuida do seu pr�prio desenvolvimento profissional, independente do apoio da empresa.";

    break;



    }



switch ($radio3) {

    case 1:

    $msg3 = "O funcion�rio precisar�, al�m do treinamento espec�fico, de uma prepara��o especial ou amadurecimento profissional para ser promovido.  No momento, n�o existem elementos que garantam que o funcion�rio tem potencial para ser promovido para um cargo maior.";

    break;



    case 2:

	$msg3 = "O funcion�rio depender� de treinamento espec�fico para ser promovido. O funcion�rio ainda n�o demonstrou estar preparado para tarefas que exijam conhecimentos superiores aos exigidos pelo seu cargo.  O funcion�rio ainda n�o demonstrou que tem o estilo e o potencial necess�rio para uma promo��o para um cargo maior.";

    break;



    case 3:

	$msg3 = "O funcion�rio precisar� de pouco treinamento ou orienta��o para ser promovido. No per�odo ele realizou algumas das tarefas de responsabilidade de seu superior com grau de compet�ncia satisfat�rio, ou realizou tarefas que exigiam conhecimentos acima dos exigidos para o seu cargo.  O funcion�rio � reconhecido como tendo potencial para um dia suceder seu superior imediato ou ocupar um cargo maior em outra �rea.";

    break;



    case 4:

	$msg3 = "O funcion�rio est� plenamente capacitado a ser promovido para um cargo maior. Isso foi comprovado pelo n�mero de vezes que o funcion�rio realizou tarefas que exigiam conhecimentos e habilidades acima daquelas exigidas pelo seu cargo. Por exemplo, ele pode ter realizado tarefas importantes, de responsabilidade do seu superior imediato com o mesmo grau de compet�ncia.  Al�m dos aspectos t�cnicos, o funcion�rio tem perfil para o cargo do seu superior imediato e � reconhecido como o sucessor natural deste.";

    break;



    }



switch ($radio4) {

    case 1:

    $msg4 = "O funcion�rio n�o conseguiu apresentar os resultados esperados, mesmo tendo recebido ajuda dos demais membros da equipe.  Ou s� conseguiu apresentar os resultados esperados com a ajuda de outras pessoas da equipe e constante orienta��o superior.";

    break;



    case 2:

	$msg4 = "Para conseguir os resultados esperados, o funcion�rio dependeu de significativa participa��o da equipe como um todo.  O m�rito de sua participa��o n�o ficou evidente dentro da equipe.  Mesmo sem se destacar, foi importante como membro da equipe.";

    break;



    case 3:

	$msg4 = "Para conseguir os resultados esperados, o funcion�rio em alguns momentos dependeu de uma aten��o especial de outras pessoas para ajud�-lo a completar seu trabalho. Foi poss�vel reconhecer o m�rito da sua participa��o no resultado do trabalho, mesmo no contexto de um trabalho de equipe.  Em v�rios momentos sua atua��o foi destacada.";

    break;



    case 4:

	$msg4 = "Para conseguir os resultados esperados, o funcion�rio dependeu essencialmente do seu pr�prio esfor�o e compet�ncia. A participa��o de outras pessoas foi apenas subsidi�ria, dentro dos padr�es normais do trabalho em equipe. O funcion�rio foi facilmente reconhecido como uma das pessoas que fizeram a diferen�a no grupo. Foi um dos principais destaques na equipe.";

    break;



    }



switch ($radio5) {

    case 1:

    $msg5 = "O funcion�rio soube resolver apenas os problemas de rotina, com precedentes bem conhecidos e contando com o apoio constante do superior imediato. As habilidades demonstradas pelo funcion�rio n�o foram suficientes para solucionar os problemas que sa�ram da estrita rotina do seu cargo. As habilidades de an�lise e julgamento demonstradas pelo funcion�rio foram apenas para fazer escolhas �bvias, em situa��es com precedentes bem conhecidos.";

    break;



    case 2:

	$msg5 = "O funcion�rio conseguiu resolver os problemas de rotina, contando com o apoio do superior quando necess�rio. Em algumas situa��es, o funcion�rio precisou fazer escolhas entre alternativas de solu��o j� conhecidas, mas sem necessidade de uma reflex�o mais profunda. As habilidades de racioc�nio demonstradas pelo funcion�rio s�o compat�veis com os requisitos do seu cargo, ou seja, s�o habilidades que constam do perfil do seu cargo.";

    break;



    case 3:

	$msg5 = "O funcion�rio conseguiu resolver os problemas mais complexos que surgiram, descritos no item anterior, com alguma ajuda e orienta��o do seu superior imediato.  O funcion�rio conseguiu demonstrar habilidades para analisar e encontrar a solu��o para alguns dos problemas mais complexos, por�m a participa��o do seu superior foi decisiva na escolha das alternativas de solu��o analisadas.";

    break;



    case 4:

	$msg5 = "O funcion�rio conseguiu resolver os problemas mais complexos que surgiram, cuja solu��o exigiu um grau de racioc�nio l�gico ou abstrato claramente mais alto do que aquele exigido pelo seu cargo. O racioc�nio exercido envolveu alguma criatividade ou uma abordagem original para encontrar a melhor solu��o para esses problemas. O funcion�rio soube resolver os problemas mais complexos que surgiram, praticamente sem orienta��o superior.";

    break;



    }



switch ($radio6) {

    case 1:

    $msg6 = "Praticamente n�o contribu�ram para a qualidade ou resultados do trabalho do funcion�rio ou para melhorar a compet�ncia e desempenho do funcion�rio. O desempenho do funcion�rio ficou abaixo do esperado, ou foi minimamente aceit�vel.";

    break;



    case 2:

	$msg6 = "Contribu�ram para melhorar a compet�ncia do funcion�rio, por�m n�o tiveram reflexos significativos na qualidade ou resultados do trabalho do funcion�rio.  O desempenho do funcion�rio ficou dentro dos padr�es j� apresentados por ele anteriormente.";

    break;



    case 3:

	$msg6 = "Contribu�ram para melhorar a compet�ncia e desempenho do funcion�rio.  Tiveram alguns reflexos positivos na qualidade ou resultados do trabalho do funcion�rio.  Os resultados do trabalho do funcion�rio foram ligeiramente superiores �queles apresentados antes da aquisi��o dos novos conhecimentos e habilidades.  Para notar a melhora no desempenho do funcion�rio, � necess�ria uma an�lise mais atenta e detalhada do trabalho realizado no per�odo.  Geralmente s� o superior do funcion�rio conseguiria notar essa melhora de desempenho.";

    break;



    case 4:

	$msg6 = "Foram decisivos para melhorar a sua compet�ncia e desempenho.  Os novos conhecimentos e habilidades tiveram reflexos positivos vis�veis na qualidade ou resultados do trabalho do funcion�rio.  Os resultados do trabalho do funcion�rio foram claramente superiores �queles apresentados antes da aquisi��o dos novos conhecimentos e habilidades. A diferen�a de antes e depois, em termos de desempenho do funcion�rio, s�o percebidas mesmo numa an�lise superficial.";

    break;



    }





}

?>