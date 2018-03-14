<?php
session_start();

mysql_connect('localhost', 'ispv_netsorr', 'F71#0138@10_lagos')
    or die('Error connecting to database.');
mysql_select_db('ispv_netsorrindo')
    or die('Error selecting database');
