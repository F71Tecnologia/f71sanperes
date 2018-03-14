<?php

// session_start();
if (empty($_COOKIE['logado'])) {
    print "<script location.href = '../../../login.php?entre=true'></script>";
}

include("../../conn.php");
include("../../wfunction.php");

function gerar_form($field){
    echo "&lt;div class=\"form-group\"&gt;<br>
    &lt;label for=\"{$field}\" class=\"col-sm-2 control-label\"&gt;<br>{$field}&lt;/label&gt;<br>
    &lt;div class=\"col-sm-9\"&gt;<br>
      &lt;input type=\"text\" class=\"form-control\" id=\"{$field}\" name=\"{$field}\" value=\"&lt;?= \$var['{$field}'] ?&gt;\"  &gt;<br>
    &lt;/div&gt;<br>
  &lt;/div&gt;<br>";
}

$table = 'entradaesaida_subgrupo';

$query = "SHOW COLUMNS FROM $table";

$result = mysql_query($query);

while ($row = mysql_fetch_array($result)) {
    gerar_form($row['Field']);
}

