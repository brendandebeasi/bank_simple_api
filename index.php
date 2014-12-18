<?php
require_once('lib/Simple.php');
$simple = new Simple('username','password');
echo $simple->card();
echo $simple->balance();
echo $simple->linkedAccounts();
echo $simple->transactions();