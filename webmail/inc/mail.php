<?php
class email
{
    static public function enviaEmail($de, $para, $assunto, $texto, $html = "", $anexo = "")
    {
        require_once 'Mail.php';
        require_once 'Mail/mime.php';

        $from = $de;
        $to = $para;
        $subject = $assunto;

        $host = "ip-173-201-191-20.ip.secureserver.net";
        $port = "465";
        $username = "notasfiscais@institutolagosrio.com.br";
        $password = "heZ8notas";

        $headers = array('From' => $from,
            'To' => $to,
            'Subject' => $subject);

        $mime_params = array(
            'text_encoding' => '7bit',
            'text_charset' => 'UTF-8',
            'html_charset' => 'UTF-8',
            'head_charset' => 'UTF-8',
            'eol' => "\n"
        );

        $mime = new Mail_mime($mime_params);
        $mime->setTXTBody($texto);

        if (trim($html) != '')
        {
            $mime->setHTMLBody($html);
        }

        if ($anexo)
        {
            $anexo = preg_replace('/^[a-z]+\//i', '', $anexo);
            $mime->addAttachment($anexo, self::MimeFile($anexo));
        }

        $body = $mime->get();
        $headers = $mime->headers($headers);

        $smtp = Mail::factory('smtp', array('host' => $host,
                    'port' => $port,
                    'auth' => true,
                    'username' => $username,
                    'password' => $password));

        $mail = $smtp->send($to, $headers, $body);

        if (PEAR::isError($mail))
        {
            return(1); //erro
        } else
        {
            return(0); //sucesso
        }
    }

    private static function MimeFile($filename)
    {
        $filename = escapeshellcmd($filename);
        $command = "file -b -i {$filename}";
        $mimeType = shell_exec($command);
        return trim($mimeType);
    }

}
