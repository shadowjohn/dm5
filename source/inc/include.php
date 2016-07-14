<?php
  function getGET_POST($inputs,$mode)
  {
    $mode=strtoupper(trim($mode));
    $data=$GLOBALS['_'.$mode];
        
    $data=array_htmlspecialchars($data);
    array_walk_recursive($data, "trim");
    
    $keys=array_keys($data);
    $filters = explode(',',$inputs);
    foreach($keys as $k)
    {
      if(!in_array($k,$filters))
      {
        unset($data[$k]);
      }
    }    
    return $data;
  } 
  function array_htmlspecialchars(&$input)
  {
      if (is_array($input))
      {
          foreach ($input as $key => $value)
          {
              if (is_array($value)) $input[$key] = array_htmlspecialchars($value);
              else $input[$key] = htmlspecialchars($value);
          }
          return $input;
      }
      return htmlspecialchars($input);
  }
  
  function array_htmlspecialchars_decode(&$input)
  {
      if (is_array($input))
      {
          foreach ($input as $key => $value)
          {
              if (is_array($value)) $input[$key] = array_htmlspecialchars_decode($value);
              else $input[$key] = htmlspecialchars_decode($value);
          }
          return $input;
      }
      return htmlspecialchars_decode($input);
  }  
  function pdo_resulttoassoc($res){    
      
    return $res->fetchAll(PDO::FETCH_ASSOC);    
  }   
  function updateSQL($pdo,$table,$fields_data,$WHERE_SQL)
  {
    $m_mix_SQL=array();
    foreach($fields_data as $k=>$v)
    {
      array_push($m_mix_SQL,sprintf("`%s`='%s'",$k,$v));
    }
    $SQL=sprintf("UPDATE `%s` SET %s WHERE %s",$table,@implode(',',$m_mix_SQL),$WHERE_SQL);
    //alert($SQL);
    $pdo->query($SQL) or die("寫入 {$table} 失敗:{$SQL}");
  }
  function insertSQL($pdo,$table,$fields_data)
  {
     $fields=ARRAY();
     $datas=ARRAY();
     $question_marks=ARRAY();
     foreach($fields_data as $k=>$v)
     {
        array_push($fields,$k);
        array_push($datas,$v);
        array_push($question_marks,'?');
     }
     //$SQL=sprintf("INSERT INTO `%s`(`%s`)VALUES('%s');",$table,@implode("`,`",$fields),@implode("','",$datas));
     $SQL = sprintf("INSERT INTO `%s`(`%s`)values(%s)",$table,@implode("`,`",$fields),@implode(",",$question_marks));
     $q = $pdo->prepare($SQL);
     for($i=0,$totals=count($question_marks);$i<$totals;$i++)
     {
   	   $q->bindParam(($i+1), $datas[$i]);
     }
     //$q->bindParam(3, $cover, PDO::PARAM_LOB);	 
     $q->execute();
     $arr = $q->errorInfo();
     if($arr[0]!="00000")
     {
       echo "\nPDOStatement::errorInfo():\n";
       print_r($arr);
       exit();
     }
     //$pdo->query($SQL) or die("寫入 {$table} 失敗:{$SQL}"); 
     //sqlite 也許會失敗    
     return $pdo->lastInsertId(); //seems failure
     
  }
  function selectSQL($pdo,$SQL)
  {
    $res=$pdo->query($SQL) or die("查詢失敗:{$SQL}");
    return pdo_resulttoassoc($res);
  }
  function selectSQL_SAFE($SQL,$data_arr,$hash_obj=null)
  {
    /*
    $HASH_QUERY = ARRAY();
    $HASH_QUERY['refresh_min']='5';
    $HASH_QUERY['which_file']=__FILE__;
    */  
    global $pdo;   
    $is_need_update_first_time_hash=false;
    $LAST_HASH_ID = "";
    /*
    $is_need_pass_cache = "";
    if (php_sapi_name() != "cli") {
      if(isset($_GET['nocache']))
      {
        $is_need_pass_cache=false;
      }
    }
    */
    if($hash_obj!=null )
    { 
      if(!isset($hash_obj['refresh_min']))
      {
        $hash_obj['refresh_min']='5';
      }
      else
      {
        $hash_obj['refresh_min']=(int)$hash_obj['refresh_min'];
      }
      
      //總之先查query_hash看看有沒有曾用過的
      $hashSQL="
          SELECT 
              `id`,
              `RESULT`,
              IFNULL(`last_update_datetime`,'') as `last_update_datetime` 
            FROM 
              `query_hash` 
            WHERE 
              1=1
              AND `SQL`=? 
              AND `PA`=? 
              AND `refresh_min`=?
              AND `which_file`=?              
            LIMIT 1";
      $PA_JSON=json_encode($data_arr,true);
      $QS = ARRAY( 
                                     $SQL,
                                     $PA_JSON,
                                     $hash_obj['refresh_min'],
                                     $hash_obj['which_file']);                                   
      $ra=selectSQL_SAFE($hashSQL,$QS);
      //如果不存在，就建立hash
      if(COUNT($ra)==0)
      {
        $m=ARRAY();
        $m['SQL']=$SQL;
        $m['PA']=$PA_JSON;
        $m['refresh_min']=$hash_obj['refresh_min'];
        $m['which_file']=$hash_obj['which_file'];
        $m['last_use_datetime']=date('Y-m-d H:i:s');
        $LAST_HASH_ID = insertSQL('query_hash',$m);
        $is_need_update_first_time_hash=true;
      }
      else
      {
        //如果最後刷新時間沒有值，仍要查詢
        if($ra[0]['last_update_datetime']!='')
        {          
          $m=ARRAY();
          $m['last_use_datetime']=date('Y-m-d H:i:s');
          updateSQL('query_hash',$m,"`id`='{$ra[0]['id']}'");
          return json_decode($ra[0]['RESULT'],true);
        }
      }
    }
    
    //找有幾個問號
    $questions = word_appear_times('?',$SQL);
    $max_i=count($data_arr);        
    if($questions!=$max_i)
    {
      echo "查詢條件無法匹配...:{$SQL} 
      <br>Questions:{$questions}
      <br>Arrays   :{$max_i}";
      exit();
    }    
    $q = $pdo->prepare($SQL);    
    for($i=0;$i<$max_i;$i++)
    {
      $q->bindParam(($i+1), $data_arr[$i]);
    }            
    $q->execute() or die("查詢失敗:...".print_r($pdo->errorInfo(),true));
    //echo $SQL;
    //$q->execute() or die(print_r($pdo->errorInfo(),true));    
    
    $ra = pdo_resulttoassoc($q);
    
    if($hash_obj!=null && $is_need_update_first_time_hash )
    {
      $m=ARRAY();
      $m['RESULT']=json_encode($ra,true);
      $m['last_update_datetime']=date('Y-m-d H:i:s');
      $m['last_use_datetime']=date('Y-m-d H:i:s');
      //pre_print_r($m);
      updateSQL('query_hash',$m,"`id`='{$LAST_HASH_ID}'");
    }                                      
    return $ra;
  }     
  function mainname($fname){
    $pathinfo=pathinfo($fname);
    return $pathinfo['filename'];       
  }  

  function is_string_like($data,$find_string){
/*
  is_string_like($data,$fine_string)

  $mystring = "Hi, this is good!";
  $searchthis = "%thi% goo%";

  $resp = string_like($mystring,$searchthis);


  if ($resp){
     echo "milike = VERDADERO";
  } else{
     echo "milike = FALSO";
  }

  Will print:
  milike = VERDADERO

  and so on...

  this is the function:
*/
    $tieneini=0;
    if($find_string=="") return 1;
    $vi = explode("%",$find_string);
    $offset=0;
    for($n=0,$max_n=count($vi);$n<$max_n;$n++){
        if($vi[$n]== ""){
            if($vi[0]== ""){
                   $tieneini = 1;
            }
        } else {
            $newoff=strpos($data,$vi[$n],$offset);
            if($newoff!==false){
                if(!$tieneini){
                    if($offset!=$newoff){
                        return false;
                    }
                }
                if($n==$max_n-1){
                    if($vi[$n] != substr($data,strlen($data)-strlen($vi[$n]), strlen($vi[$n]))){
                        return false;
                    }

                } else {
                    $offset = $newoff + strlen($vi[$n]);
                 }
            } else {
                return false;
            }
        }
    }
    return true;
  }  
  function curl_getPost_INIT($URL,$mPOST,$options=null)
  {                          
    $PWD=dirname(__FILE__);
    @mkdir(tmp,0777);
    $OUTPUT = ARRAY();
    $curl = curl_init();
    
    //curl_setopt($curl, CURLOPT_PROXY, $proxy);
    //curl_setopt($curl, CURLOPT_PROXYPORT,$proxy_port);
    if(is_array($options))
    {
      if(isset($options['proxy']))
      {
        curl_setopt($curl, CURLOPT_PROXY, $options['proxy']);
        curl_setopt($curl, CURLOPT_PROXYPORT,$options['proxy_port']);
      } 
      if(isset($options['login_id']))
      {                
        //curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        //curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY); 
        $UW="{$options['login_id']}:{$options['pwd']}";
        curl_setopt($curl, CURLOPT_USERPWD,$UW);
        $encodedAuth = base64_encode($UW);
        if(isset($options['header']))
        {
          array_push($options['header'],"Authentication : Basic ".$encodedAuth);
          curl_setopt($curl, CURLOPT_HTTPHEADER, $options['header']);
        }
        else
        {
          curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authentication : Basic ".$encodedAuth));
        }
        
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);                                
      }
      else
      {
        if(isset($options['header']))
        {        
          //print_r($options);
          curl_setopt($curl, CURLOPT_HTTPHEADER, $options['header']);
        }
      }      
    } 
    if(isset($options['timeout']))
    {
      curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $options['timeout']);
      curl_setopt($curl, CURLOPT_TIMEOUT, $options['timeout']);
    }       
    curl_setopt($curl, CURLOPT_URL, $URL);   
    //curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
    curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)");
    /*
    $headers = array(      
      "User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:40.0) Gecko/20100101 Firefox/40.0",
      "Accept-Language: zh-TW,zh;q=0.8,en-US;q=0.5,en;q=0.3",
      "Accept-Encoding: gzip, deflate",
      "Referer: https://sentinel.tksc.jaxa.jp/sentinel2/iccDsInformation.jsp",    
      "Content-Type: multipart/form-data;" 
    );
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
    */     
    curl_setopt($curl, CURLOPT_HEADER, 0);
    if(is_win())
    {
      @mkdir("C:\\temp",0777);
      curl_setopt($curl, CURLOPT_COOKIEJAR, "c:\\temp\\cookie.txt");                  //保存cookie
      curl_setopt($curl, CURLOPT_COOKIEFILE, "c:\\tmp\\cookie.txt");                   //讀取cookie
    }
    else
    {
      //$cookie = tempnam ( "/tmp" , "cookies_" );
      $cookie = "/tmp/cookies.txt";
      curl_setopt($curl, CURLOPT_COOKIEJAR, $cookie );                  //保存cookie
      curl_setopt($curl, CURLOPT_COOKIEFILE, $cookie);                   //讀取cookie
    }
    if($mPOST=="" || $mPOST==null)
    {
      curl_setopt($curl, CURLOPT_POST, 0);
    }
    else
    {
      curl_setopt($curl, CURLOPT_POST, 1);     
      if(is_array($mPOST))
      {
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($mPOST));
      }
      else
      {
        curl_setopt($curl, CURLOPT_POSTFIELDS, $mPOST);
      }
    }
    ob_start();
    curl_exec($curl);
    $data=ob_get_contents();
    ob_end_clean();
    $OUTPUT['curl']=$curl;
    $OUTPUT['output']=$data;
    return $OUTPUT;
  }  
  function is_win()
  {
    return (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');        
  }  
  function getDomHTML($html,$dom)
  {
    $dhtml = str_get_html($html);
    $OUTPUTS = ARRAY();
    foreach($dhtml->find($dom) as $k)
    {
      array_push($OUTPUTS,$k->outertext);
    }    
    return $OUTPUTS;
  }   
  function big5toutf8($str)
  {
    return mb_convert_encoding($str, 'UTF-8','BIG5');
  }
  function utf8tobig5($str)
  {
    return mb_convert_encoding($str, 'BIG5', 'UTF-8');
  }  
  function new_glob($sDir)
  {
    $sp = DIRECTORY_SEPARATOR;    
    if((strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN'))    
    {
      $sDir = escapeshellcmd($sDir);      
    }
    
    // Get the list of all matching files currently in the
    // directory.    
    $tmp = big5toutf8(trim(`dir /A/b {$sDir}`));         
    $aFiles = ARRAY();
    $d = dirname($sDir);  
    foreach(explode("\n",$tmp) as $v)
    {            
      if(trim($v)!=""){
        array_push($aFiles , "{$d}{$sp}{$v}");
      }
    }        
    return $aFiles;     
  }
/**
 * Recursive version of glob
 *
 * @return array containing all pattern-matched files.
 *
 * @param string $sDir      Directory to start with.
 * @param string $sPattern  Pattern to glob for.
 * @param int $nFlags       Flags sent to glob.
 */
  function globr($sDir, $sPattern, $nFlags = NULL)
  {
    $sp = DIRECTORY_SEPARATOR;    
    if((strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN'))    
    {
      $sDir = escapeshellcmd($sDir);      
    }
    
    // Get the list of all matching files currently in the
    // directory.
    $tmp = trim(`dir /A/b {$sDir}{$sp}{$sPattern}`);    
    $aFiles = ARRAY();
    foreach(explode("\n",$tmp) as $v)
    {
      //echo "\n\n\n{$v}\n\n\n";
      //exit();
      if(is_dir("{$sDir}{$sp}{$v}"))
      {
        $aSubFiles = globr("{$sDir}{$sp}{$v}", $sPattern, $nFlags);
        $aFiles = array_merge($aFiles, $aSubFiles);
      }
      else
      {
        array_push($aFiles , "{$sDir}{$sp}{$v}");
      }
    }
    //$aFiles = glob("{$sDir}{$sp}{$sPattern}", $nFlags);
    print_r($aFiles);
    // Then get a list of all directories in this directory, and
    // run ourselves on the resulting array.  This is the
    // recursion step, which will not execute if there are no
    // directories.
//     echo "\n1:GGGG\n{$sDir}\n";
//     foreach (glob("{$sDir}{$sp}*", GLOB_ONLYDIR) as $sSubDir)
//     {
//       echo "\n1:GGGG\n";
//       print_r($sSubDir);
//       echo "\n2:GGGGG\n";
//       $aSubFiles = globr($sSubDir, $sPattern, $nFlags);
//       $aFiles = array_merge($aFiles, $aSubFiles);
//     }
    // The array we return contains the files we found, and the
    // files all of our children found.
    return $aFiles;
  }  
  function array_values_array_unique_arr($arr)
  {
    $OUTPUT=ARRAY();    
    foreach($arr as $k=>$v)
    {
      $OUTPUT[$k]=json_encode($v,true);
      
    }
    
    $OUTPUT=array_values(array_unique($OUTPUT));
    foreach($OUTPUT as $k=>$v)
    {
      $OUTPUT[$k] = json_decode($v,true);
    }
    
    return $OUTPUT;
  }  
//以後排序用這支
    function array_sort_new($array, $on, $order='SORT_DESC')
    {
      $new_array = array();
      $sortable_array = array();
 
      if (count($array) > 0) {
          foreach ($array as $k => $v) {
              if (is_array($v)) {
                  foreach ($v as $k2 => $v2) {
                      if ($k2 == $on) {
                          $sortable_array[$k] = $v2;
                      }
                  }
              } else {
                  $sortable_array[$k] = $v;
              }
          }
 
          switch($order)
          {
              case 'SORT_ASC':   
                  //echo "ASC";
                  natcasesort($sortable_array);
              break;
              case 'SORT_DESC':
                  //echo "DESC";
                  //arsort($sortable_array);
                  natrsort($sortable_array);
              break;
          }
 
          foreach($sortable_array as $k => $v) {
              $new_array[] = $array[$k];
          }
      }
      return $new_array;
    }            
  function word_appear_times($find_word,$input)
  {
    //找一個字串在另一個字串出現的次數
    $found_times=0;
    $len = strlen($find_word);
    for($i=0,$max_i=strlen($input)-$len;$i<=$max_i;$i++)
    {
      if(substr($input,$i,$len)==$find_word)
      {
        $found_times++;
      }
    }
    return $found_times;
  }    
  function print_table($ra,$fields='',$headers='',$classname='')
  {    
    $classname=($classname=='')?'':" class='{$classname}' ";
    if($fields==''||$fields=='*')
    {      

        $tmp="<table {$classname} border='1' cellspacing='0' cellpadding='0'>";
        $tmp.="<thead><tr>";
        foreach($ra[0] as $k=>$v)
        {
          $v=strip_tags($v);
          $tmp.="<th field='{$v}'>{$k}</th>";
        }
        $tmp.="</tr></thead>";
        $tmp.="<tbody>";
        for($i=0,$max_i=count($ra);$i<$max_i;$i++)
        {
          $tmp.="<tr>";
          foreach($ra[$i] as $k=>$v)
          {                                 
            $tmp.="<td field='{$k}'>{$v}</td>";
          }
          $tmp.="</tr>";
        }
        $tmp.="</tbody>";
        $tmp.="</table>";
        return $tmp;
    }
    else
    {
      $tmp="<table {$classname} border='1' cellspacing='0' cellpadding='0'>";
      $tmp.="<thead><tr>";
      foreach(explode(',',$headers) as $k=>$v)
      {
        $field = strip_tags($v);
        $tmp.="<th field='{$field}'>{$v}</th>";
      }
      $tmp.="</tr></thead>";
      $tmp.="<tbody>";
      $m_fields=explode(',',$fields);
      for($i=0,$max_i=count($ra);$i<$max_i;$i++)
      {
        $tmp.="<tr>";
        foreach($m_fields as $k)
        {
          $tmp.="<td field='{$k}'>{$ra[$i][$k]}</td>";
        }
        $tmp.="</tr>";
      }
      $tmp.="</tbody>";
      $tmp.="</table>";
      return $tmp;
    }
  }  
  function get_between_new($source, $beginning, $ending, $init_pos=0) {
      $beginning_pos = strpos($source, $beginning, $init_pos);
      $middle_pos = $beginning_pos + strlen($beginning);
      $ending_pos = strpos($source, $ending, $beginning_pos + 1);
      $middle = substr($source, $middle_pos, $ending_pos - $middle_pos);
      return $middle;
  }    
  function curl_getPost_continue($curl,$URL,$mPOST,$options=null)
  {
    if(is_array($options))
    {                 
      if(isset($options['proxy']))
      {
        curl_setopt($curl, CURLOPT_PROXY, $options['proxy']);
        curl_setopt($curl, CURLOPT_PROXYPORT,$options['proxy_port']);
      }     
      if(isset($options['login_id']))
      {                
        //curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        //curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY); 
        $UW="{$options['login_id']}:{$options['pwd']}";
        curl_setopt($curl, CURLOPT_USERPWD,$UW);
        $encodedAuth = base64_encode($UW); 
        if(isset($options['header']))
        {
          array_push($options['header'],"Authentication : Basic ".$encodedAuth);
          curl_setopt($curl, CURLOPT_HTTPHEADER, $options['header']);
        }
        else
        {
          curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authentication : Basic ".$encodedAuth));
        }
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);                                
      }
      else
      {
        if(isset($options['header']))
        {        
          curl_setopt($curl, CURLOPT_HTTPHEADER, $options['header']);
        }
      }
    }   
    curl_setopt($curl, CURLOPT_URL, $URL);
    if(isset($options['timeout']))
    {
      curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $options['timeout']);
      curl_setopt($curl, CURLOPT_TIMEOUT, $options['timeout']);
    }       
    curl_setopt($curl, CURLOPT_POST, 1);
    /*$headers = array(    
      "Accept-Encoding: gzip",
      "Content-Type: application/json"
    );
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);*/

    curl_setopt($curl, CURLOPT_POSTFIELDS, $mPOST);

    ob_start();
    curl_exec($curl);
    $data=ob_get_contents();
    ob_end_clean();
    $OUTPUT['curl']=$curl;
    $OUTPUT['output']=$data;
    return $OUTPUT;
  }  
  function jsAddSlashes($str) {
    $pattern = array(
        "/\\\\/"  , "/\n/"    , "/\r/"    , "/\"/"    ,
        "/\'/"    , "/&/"     , "/</"     , "/>/"
    );
    $replace = array(
        "\\\\\\\\", "\\n"     , "\\r"     , "\\\""    ,
        "\\'"     , "\\x26"   , "\\x3C"   , "\\x3E"
    );
    return preg_replace($pattern, $replace, $str);
  }  
  function runAsynchronously($path,$arguments) {
      $WshShell = new COM("WScript.Shell");
      $oShellLink = $WshShell->CreateShortcut("temp.lnk");
      $oShellLink->TargetPath = $path;
      $oShellLink->Arguments = $arguments;
      $oShellLink->WorkingDirectory = dirname(__FILE__);
      $oShellLink->WindowStyle = 0;
      $oShellLink->Save();
      $oExec = $WshShell->Run("temp.lnk", 0, false);
      unset($WshShell,$oShellLink,$oExec);
      sleep(1);
      unlink("temp.lnk");
  }
  //runAsynchronously("{$PWD}\\..\\php\\php.exe"," run_ftp.php {$w}");  
  
  function execInBackground($cmd) { 
    if (is_win()){ 
        pclose(popen("start /B ". $cmd, "r"));  
    } 
    else { 
        exec($cmd . " > /dev/null &");   
    } 
  }