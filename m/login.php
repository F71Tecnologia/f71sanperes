<?php
if (isset($_REQUEST['login']) && isset($_REQUEST['senha'])) {
    setcookie("logado", $row2['0'], 0);
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <link rel="stylesheet" href="css/jquery.mobile.custom.structure.css" type="text/css" />
        <link rel="stylesheet" href="css/jquery.mobile.custom.theme.css" type="text/css" />
        <link rel="stylesheet" href="css/styles.css" type="text/css" />
        
        <script src="../js/jquery-1.10.2.min.js" type="text/javascript"></script>
        <script src="js/jquery.mobile.custom.min.js" type="text/javascript"></script>
        <title>Login</title>
    </head>
    <body>
        <div data-role="page">
            <div data-role="header">
                <h1>The tartanador</h1>
            </div>
            <div role="main" class="ui-content">
                <p>Teste lorem impulsen teste <strong>BELEZURA</strong>.</p>

                <form>
                    <input id="filter-for-listview" data-type="search" placeholder="Type to search...">
                </form>
                <ul data-role="listview" data-inset="true">
                    <li><a href="#">Acura</a></li>
                    <li><a href="#">Audi</a></li>
                    <li><a href="#">BMW</a></li>
                    <li><a href="#">Cadillac</a></li>
                    <li><a href="#">Ferrari</a></li>
                </ul>
            </div>
            <div data-role="footer" data-position="fixed">
                footer
            </div>
        </div>
    </body>
</html>