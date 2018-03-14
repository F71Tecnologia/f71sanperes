<?php
include('../../conn.php');
?>

<html>
    <head>
        <title>:: Intranet :: Cursos</title>
        <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
        <link href="jquery.autocomplete.css" rel="stylesheet" type="text/css" />
        <script src="../../js/jquery-1.8.3.min.js" type="text/javascript"></script>
        <script src="../../js/jquery.autocomplete.js" type="text/javascript"></script>
        
        <!--auto complete -->
        <script type="text/javascript">
        $().ready(function() {
            $("#cbo").autocomplete("lista_cbo.php", {
                    width: 400,
                    matchContains: false,                    
                    //mustMatch: true,
                    minChars: 3,
                    //multiple: true,
                    //highlight: false,
                    //multipleSeparator: ",",
                    selectFirst: false
            });
        });
        </script>
    </head>
    
    <body>
        <form autocomplete="off">
            <p>
                Digite um nome:
                <input type="text" name="cbo" id="cbo" />
            </p>
        </form>
    </body>
    
</html>
