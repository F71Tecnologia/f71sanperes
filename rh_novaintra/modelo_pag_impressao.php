
<?php
if (empty($_COOKIE['logado'])) {
    print "<script>location.href = '../../login.php?entre=false';</script>";
}
include('../conn.php');
include('../empresa.php');
$img = new empresa();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="ISO-8859-9">
        <title>:: Intranet ::</title>
        <link rel="shortcut icon" href="../../favicon.png">
        <link href="../resources/css/bootstrap.css" rel="stylesheet">
        <link href="../resources/css/bootstrap-theme.css" rel="stylesheet">
        <link href="../resources/css/font-awesome.min.css" rel="stylesheet">
        <link href="../resources/css/style-print.css" rel="stylesheet">
    </head>
    <body>
        <div class="no-print">
            <nav class="navbar navbar-default navbar-fixed-top">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-3">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                    </div>
                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-3">
                        <div class="text-center">
                            <!--<button type="button" id="voltar" class="btn btn-default navbar-btn"><i class="fa fa-reply"></i>Voltar</button>-->
                            <button type="button" id="imprimir" class="btn btn-success navbar-btn"><i class="fa fa-print"></i> Imprimir</button>
                        </div>
                    </div>
                </div>
            </nav>
        </div>

        <div class="pagina">
            <div class="text-center">
                <?php $img->imagem(); ?>
            </div>

            <?php for ($i = 1; $i <= 6; $i++) { ?>
                <h<?= $i ?>>Titulo <?= $i ?></h<?= $i ?>>
            <?php } ?>
            <p class="text-justify">Lorem ipsum dolor sit amet, consectetur adipiscing elit. In sed massa nunc. In hac habitasse platea dictumst. Suspendisse velit neque, imperdiet at massa vel, varius porttitor ipsum. Curabitur rutrum felis ac dolor mattis euismod. Integer fringilla porttitor erat aliquet mattis. Nam id dolor non turpis accumsan dignissim ac nec eros. Praesent nisl justo, scelerisque sit amet euismod ac, scelerisque sed lectus. Aliquam eu vehicula est. Ut mattis fringilla neque quis porta. Integer accumsan et erat sed rutrum. Curabitur sagittis, purus tincidunt finibus porta, magna lectus consectetur magna, non accumsan risus elit sit amet ligula.</p>

            <p class="text-justify">Praesent a nulla dapibus, tempor orci at, suscipit eros. Quisque nisl tortor, fermentum a sodales nec, hendrerit sed mi. Donec aliquet luctus lacinia. Aliquam venenatis elementum turpis, eu vestibulum sapien convallis eu. Sed auctor leo quam, sed blandit nulla viverra sed. Aenean condimentum, erat quis egestas ultricies, mi lorem maximus dui, id pharetra augue massa id lectus. Phasellus tincidunt tellus sed interdum hendrerit. Sed placerat lorem felis, posuere tempor quam eleifend eu. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum magna erat, vulputate ut vehicula nec, molestie id ex.</p>

            <p class="text-justify">Nulla ut lacus a erat porta fermentum. Etiam lacinia auctor risus, et malesuada nunc commodo eu. Quisque hendrerit dolor lorem, in imperdiet ante commodo eget. Interdum et malesuada fames ac ante ipsum primis in faucibus. Aliquam dapibus ipsum nec enim luctus, id pulvinar neque convallis. Ut tristique velit neque, in dignissim nibh gravida eget. Nam leo sem, tempus quis metus eget, luctus commodo nisl. Sed feugiat at ligula sed semper. Cras in nisi pulvinar, accumsan velit sed, facilisis quam. Vestibulum et sapien leo. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Proin mattis libero magna, eu feugiat quam facilisis at.</p>

            <ol>
                <?php for ($i = 1; $i <= 6; $i++) { ?>
                    <li>Lista <?= $i ?></li>
                <?php } ?>
            </ol>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Col 1</th>
                        <th>Col 2</th>
                        <th>Col 3</th>
                    </tr>
                </thead>
                <tbody>
                    <?php for ($i = 1; $i <= 6; $i++) { ?>
                        <tr>
                            <td>Col 1</td>
                            <td>Col 2</td>
                            <td>Col 3</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- javascript aqui -->
        <script src="../js/jquery-1.10.2.min.js" type="text/javascript"></script>
        <script src="../resources/js/print.js" type="text/javascript"></script>
    </body>
</html>
