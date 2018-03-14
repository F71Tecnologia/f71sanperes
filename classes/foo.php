<?php
class foo {
    var $value=1;
    
    // Função "plugin" da classe "foo"
    function plugin($nome_plugin) {
        $conteudo = file("/home/ispv/public_html/intranet/classes/{$nome_plugin}.php");

        // A primeira linha do arquivo deve conter a tag que abre o código PHP ex. '<?php'
        // A última linha deve conter a tag que fecha o código PHP

        $total_linhas = count($conteudo);

        unset($conteudo[0]); // exclui primeira linha
        unset($conteudo[$total_linhas-1]); // exclui última linha

        $conteudo = implode("",$conteudo); // conteúdo do arquivo

        $this->extend($conteudo);
    }
    
    // Função "extend" da classe "foo"
    function extend($codigo) {
        $nome_classe = get_class($this);
        $nome_nova_classe = $nome_classe . "_ext";

        eval('class '. $nome_nova_classe .' extends '. $nome_classe .' { '. $codigo .' }');

        $this = new $nome_nova_classe();
    }
}


$foo = new foo;
$foo->plugin('bar');
$foo->bar(7); // retorna 8
$foo->bar(9); // retorna 17 (8+9)

