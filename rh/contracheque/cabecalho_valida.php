<!DOCTYPE html>
<html lang="pt">
    <head>
        <meta charset="iso-8859-1">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>:: Intranet :: Gestão de RH</title>
        <link href="../../favicon.png" rel="shortcut icon" />
        <link href="../../resources/css/bootstrap.css" rel="stylesheet" media="all">
    </head>
    <body>        
        <div class="container">
            <div id="content">
                <h3><?php echo "{$row_projeto['nome']} - {$dadosFolha['mes']}/{$dadosFolha['ano']}"; ?></h3>
                <table class="table table-striped table-hover table-condensed table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>NOME</th>
                            <th>STATUS</th>
                            <th>LÌQ. CC</th>
                            <th>LÌQ. FP</th>
                            <th>DIFERENÇA</th>
                        </tr>
                    </thead>
                    <tbody>