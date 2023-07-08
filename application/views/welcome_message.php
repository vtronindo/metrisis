<?php

$kalimat = "VT01}VR01}perbekalan entek bos, kirimi yo}";
 
// memisahkan string menjadi array
$data = explode("}" , $kalimat);
echo $data[2];
?>