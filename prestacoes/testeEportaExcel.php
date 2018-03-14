<?php

require_once('../classes/Spreadsheet/Excel/Writer.php');

$file = new Spreadsheet_Excel_Writer('teste.xls');
$planilha = $file->addWorksheet("Teste de gerador de excel");
$planilha->write(0, 0, "Nome");
$planilha->write(0, 1, "Telefone");
$planilha->write(1, 0, "Maria");
$planilha->write(1, 1, "3333-3333");
$planilha->close("teste.xls");
