<?php 
  @error_reporting(E_ALL & ~E_NOTICE);
  @mkdir("C:\\tmp",0777,true);

  define('SP',DIRECTORY_SEPARATOR);
  $SP = DIRECTORY_SEPARATOR; 
  @ini_set("memory_limit","4096M");
  @ini_set('post_max_size', '100M');  
  @ini_set('user_agent','Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)');  
  $base_dir=dirname(__FILE__)."{$SP}..";
  $base_tmp="{$base_dir}/tmp";
  $base_bin="{$base_dir}/bin";
  @ini_set('memory_limit','2048M');
  @ini_set('post_max_size', '2048M');    
  @header("Content-Type: text/html; charset=utf-8");          
  date_default_timezone_set("Asia/Taipei");     
  @session_start();  
  @header("Content-Type: text/html; charset=utf-8");          
  date_default_timezone_set("Asia/Taipei");  
  if((strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'))
  {
    if(!is_dir("C:\\tmp"))
    {
      @mkdir("C:\\tmp");      
    }
    @ini_set('session.save_path', 'C:\\tmp');
  }
  else
  {
    //@ini_set('session.save_path', '/tmp');
  }

  try{        
    $pdo = new PDO("sqlite:{$base_dir}{$SP}inc{$SP}comic.sqlite"); 
  }catch(PDOException $Exception){
    echo "資料庫未連線...";
    //print_r($Exception);
    exit();
  }  
  mb_http_output("UTF-8");
  mb_internal_encoding('UTF-8'); 
  mb_regex_encoding("UTF-8");  
  
  // 羽山流，強制默認 magic_quotes_gpc = on，未來咱的 Code 就會乾淨了
  function sanitizeVariables(&$item, $key)
  {
    if (!is_array($item))
    {
      if (get_magic_quotes_gpc())
          $item = stripcslashes($item);
      $item = addslashes($item);
    }
  }
  // escaping and slashing all POST and GET variables. you may add $_COOKIE and $_REQUEST if you want them sanitized.
  array_walk_recursive($_POST, 'sanitizeVariables');
  array_walk_recursive($_GET, 'sanitizeVariables');
  array_walk_recursive($_COOKIE, 'sanitizeVariables');
  array_walk_recursive($_REQUEST, 'sanitizeVariables');
  

  require 'include.php';
  require 'convert/dbcconv.php';
  require 'simplehtmldom/simple_html_dom.php';
  require 'JavaScriptUnpacker.php';
  $ini=parse_ini_file("{$base_dir}{$SP}setting.ini");
  @mkdir("{$ini['COMIC_PATH']}",0777,true);