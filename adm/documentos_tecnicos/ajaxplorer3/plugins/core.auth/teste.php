<?php
include('../../../../../conn.php');
if(isset($_GET['teste'])){
	
$qr_funcionario = mysql_query("SELECT * FROM doc_tecnico_acesso WHERE funcionario_id = '$_COOKIE[logado]'");
$row_funcionario = mysql_fetch_assoc($qr_funcionario);
$json = array('usuario' => $row_funcionario['usuario'],
			  'senha'   => $row_funcionario['senha']
			  );
echo json_encode($json);

exit();
}
?>


<!-- Carrega o arquivo 'script.js' ao iniciar a página! //-->

<script type="text/javascript">
function ajax_login() {
	
var navegador = navigator.userAgent.toLowerCase(); //Cria e atribui à variável global 'navegador' (em caracteres minúsculos) o nome e a versão do navegador
var xmlhttp;
    if (navegador.indexOf('msie') != -1) { //Internet Explorer
        var controle = (navegador.indexOf('msie 5') != -1) ? 'Microsoft.XMLHTTP' : 'Msxml2.XMLHTTP'; //Operador ternário que adiciona o objeto padrão do seu navegador (caso for o IE) à variável 'controle'
        try {
            xmlhttp = new ActiveXObject(controle); //Inicia o objeto no IE
        } catch (e) { }
    } else { //Firefox, Safari, Mozilla
        xmlhttp = new XMLHttpRequest(); //Inicia o objeto no Firefox, Safari, Mozilla
    }

    if (!xmlhttp) {

        //Insere no 'elemento' o texto atribuído
       alert('Erro');     

    } else {
        //Insere no 'elemento' o texto atribuído
       alert('Carregando...');

    }

    xmlhttp.onreadystatechange = function () {
        //Se a requisição estiver completada
        if (xmlhttp.readyState == 4 || xmlhttp.readyState == 0) {
            //Se o status da requisição estiver OK
            if (xmlhttp.status == 200) {
                //Insere no 'elemento' a página postada
				var resposta = JSON.parse(xmlhttp.responseText);
				 var input = document.getElementById('teste');
				 input.value = resposta.senha;
            } else {
                //Insere no 'elemento' o texto atribuído
              alert('Página não encontrada!');

            }

        }

    }

    //Abre a página que receberá os campos do formulário
    xmlhttp.open('POST', 'teste.php?teste=1', true);

    //Envia o formulário com dados da variável 'campos' (passado por parâmetro)
    xmlhttp.send();

}
ajax_login();
</script>

<input type="text" name="teste" id="teste"  onload=""/>