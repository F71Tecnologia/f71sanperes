<?php
include("../../conn.php");
include("../../wfunction.php");
include("../../classes/global.php");
include("../../classes/FuncionarioClass.php");

$objFuncionario = new FuncionarioClass();
$usuario = carregaUsuario();

if($_REQUEST['action'] == 'duplicar_funcionario'){
//    print_array($_REQUEST);
//    echo '---------------------------------------------------------------------';
    $objFuncionario->setIdFuncionario($_REQUEST['id_funcionario']);
    $objFuncionario->getFuncionarioById();
    $objFuncionario->getRow();
//    print_array($objFuncionario);
//    echo '---------------------------------------------------------------------';
    $nome1 = explode(' ', $_REQUEST['nome']);
    $nome1 = (count($nome1) > 1) ? "{$nome1[0]} {$nome1[1]}" : $nome1[0];
    $email2 = $_REQUEST['email2'];
    $host_domain = str_replace("www.", "", $_SERVER['SERVER_NAME']);
    $host_domain = preg_replace( "/\r|\n/", "", $host_domain);
    
    $objFuncionario->setIdFuncionario('');
    $objFuncionario->setNome($_REQUEST['nome']);
    $objFuncionario->setNome1($nome1);
    $objFuncionario->setLogin($_REQUEST['login']);
    $objFuncionario->setDataNasci(implode('-', array_reverse(explode('/',$_REQUEST['data_nasc']))));
    $objFuncionario->setDataCad(date('Y-m-d'));
    $objFuncionario->setUserCad($usuario['id_funcionario']);
    $objFuncionario->setEmail($email2);
    $objFuncionario->setSenha('123456');
    $objFuncionario->setAltSenha(1);
//    print_array($objFuncionario);
    
    $objFuncionario->insert();
    
    $to = $email2;
    $nome = $_REQUEST['nome'];
    $login = $_REQUEST['login'];
    $content_type = "html";
    $subject = 'Informações de Cadastro';
    if ($content_type == "plain") {
    $message = <<<EOT
Boa tarde, $nome.

O sistema pode ser acessado em qualquer computador ou dispositivo móvel, aconselhamos o uso do Google Chrome.

O acesso é feito em: "$host_domain/intranet"

Usuário: $login
Senha: 123456
Será necessário digitar a senha antiga: 123456 e escolher uma nova senha em seu primeiro acesso.

Esse e-mail é automático, favor não responder.
--
F71 Sistemas Web
EOT;
    }
    else {
        $message = 'Boa tarde, '.$nome.'.<br /><br />'.
        'O sistema pode ser acessado em qualquer computador ou dispositivo móvel, aconselhamos o uso do Google Chrome.<br /><br />'.
        'O acesso é feito em: "'.$host_domain.'/intranet"<br /><br />'.
        'Usuário: '.$login.'<br />'.
        'Senha: 123456<br /><br />'.
        'Será necessário digitar a senha antiga: 123456 e escolher uma nova senha em seu primeiro acesso.<br /><br />'.
        'Esse e-mail é automático, favor não responder<br />'.
        '--<br />'.
        /*
         '<img data:image/gif;base64,<?php echo base64_encode(file_get_contents("'.$host_domain.'/intranet/imagens/assinatura_instr.jpg")); ?> border="0" alt="" />'.
         */
        'F71 Sistemas Web';
    }
     $headers = "From: webmaster@".$host_domain. "\r\n".
     "Reply-To: webmaster@".$host_domain. "\r\n" .
     "Bcc: paulo.renato@f71.com.br,sabinojunior@f71.com.br,ramon@f71.com.br\r\n" .
     "MIME-Version: 1.0" . "\r\n".
     "Content-type: text/html; charset=iso-8859-1" . "\r\n";
     if($to) {
        mail($to, $subject, $message, $headers);
     }
    
    $sql = "SELECT * FROM botoes_assoc WHERE id_funcionario = {$_REQUEST['id_funcionario']}";
    $qry = mysql_query($sql);
    while($row = mysql_fetch_assoc($qry)){
        $array[] = "({$row['botoes_id']}, {$objFuncionario->getIdFuncionario()})";
    }
    if(count($array) > 0){
        $insert = "INSERT INTO botoes_assoc (botoes_id, id_funcionario) VALUES " . implode(', ', $array);
        mysql_query($insert);
    }
    
    $sql1 = "SELECT * FROM funcionario_acoes_assoc WHERE id_funcionario = {$_REQUEST['id_funcionario']}";
    $qry1 = mysql_query($sql1);
    while($row1 = mysql_fetch_assoc($qry1)){
        $row1['id_funcionario'] = $objFuncionario->getIdFuncionario();
        $array1[] = "(".implode(',', $row1).")";
    }
    if(count($array1) > 0){
        $insert1 = "INSERT INTO funcionario_acoes_assoc (id_funcionario,acoes_id,id_regiao,botoes_id) VALUES " . implode(', ', $array1);
        mysql_query($insert1);
    }
    
    echo $objFuncionario->getIdFuncionario();
//    header('Location: ../funcionario');
}
