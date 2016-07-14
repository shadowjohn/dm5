<?php
  //include 'inc/config.php';    
  
  $URL = base64_decode(base64_decode($_GET['url']));
  //echo $URL;
  echo @file_get_contents($URL);