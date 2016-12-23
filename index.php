<?php
$to = 'gavin.jones@rising5th.co.uk';
$from = 'webmaster@web.com';
$subject = 'Contact form on ' . $_SERVER['HTTP_HOST'];
$txt = 'text';
$headers = 'headers';


mail($to,$subject,$txt,$headers,$from);
echo('done');
?>

