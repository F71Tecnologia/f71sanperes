<?php
session_start();
if (!isset($_SESSION['test']))
{
    echo "First activation: setting session variable";
    $_SESSION['test'] = false;
} else
{
    echo "SESSIONS ARE WORKING! activation: ", ( ++$_SESSION['test']);
    ?>
    <br><a href="http://dengueaki.com.br/sessao.php">Again</a>
    <?php
}
echo "<br>" . session_id();