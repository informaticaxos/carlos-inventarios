<?php
$type = 'core';
$name = 'Courier';
$up = -100;
$ut = 50;
$dw = 600;
$diff = '';
$originalsize = sizeof($enc);
for($i=0;$i<sizeof($enc);$i++)
$originalsize += $cw[$enc[$i]];
$size1 = $originalsize;
for($i=128;$i<256;$i++)
$size1 += $cw[chr($i)];
$size = $size1;
?>
