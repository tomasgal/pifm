<?php 
$myTextFileHandler = @fopen("pifm.log.txt","r+");
@ftruncate($myTextFileHandler, 0);
header("Location: /");
die();
?>
