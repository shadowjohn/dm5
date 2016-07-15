<?php
  include 'inc/config.php';  
  $PWD=dirname(__FILE__);
  function parse_http_head ($str) {

    $result = array();

    // Split into lines
    $lines = explode("\r\n", $str);

    // Handle response line
    $line = explode(' ', array_shift($lines), 3);
    $version = explode('/', $line[0]);
    $result['version'] = (float) $version[1];
    $result['code'] = (int) $line[1];
    $result['text'] = $line[2];

    // Parse headers
    $result['headers'] = array();
    while ($line = trim(array_shift($lines))) {
      list($name, $val) = explode(':', $line, 2);
      $name = strtolower(trim($name)); // Header names are case-insensitive, so convert them all to lower case so we can easily use isset()
      if (isset($result['headers'][$name])) { // Some headers (like Set-Cookie:) may appear more than once, so convert them to an array if necessary
        $result['headers'][$name] = (array) $result['headers'][$name];
        $result['headers'][$name][] = trim($val);
      } else {
        $result['headers'][$name] = trim($val);
      }
    }

    return $result;

  }  
  $SQL="
  SELECT
    *
  FROM `mycomic_dm5`
  WHERE
    `status`='1'
  LIMIT 1
  ";
  $ra=selectSQL($pdo,$SQL);
  if(count($ra)!=0)
  {
    exit();
  }
  $SQL="
  SELECT
    *
  FROM `mycomic_dm5`
  WHERE
    `status`='0'
  LIMIT 1
  ";
  $ra=selectSQL($pdo,$SQL);
  if(count($ra)==0)
  {
    exit();
  }
  $m=ARRAY();
  $m['status']='0';
  updateSQL($pdo,'mycomic_dm5',$m,"`id`='{$ra[0]['id']}'");
  $all_done=true;
  $failure_count=0;
  $big5_cname=utf8tobig5(dbcconv($ra[0]['cname'],0));
  $big5_cname = str_replace(":","_",$big5_cname);
  $big5_req = utf8tobig5(dbcconv($ra[0]['req'],0));
  $big5_req = str_replace(":","_",$big5_req);
  for($i=1;$i<=$ra[0]['total_pages'];$i++)
  {

    if(@file_get_contents("{$ini['COMIC_PATH']}{$SP}{$big5_cname}{$SP}{$big5_req}{$SP}{$i}.png")!="")
    {
      continue;
    }
    //(1) http://www.dm5.com/{$ra[0]['link']}-p{$i}
    //(2) http://www.dm5.com/m97094/history.ashx?cid=97094&mid=8165&page=1&uid=0&language=1
    //(3) http://www.dm5.com/m97094/chapterfun.ashx?cid=97094&page=1&key=&language=1&gtk=6
    //(4) http://www.dm5.com/userinfo.ashx?d=Sun%20Jun%2012%202016%2000:47:28%20GMT+0800
    //Sun Jun 12 2016 00:47:28 GMT+0800        
    //(5) http://css16.tel.cdndm.com/default/images/newloading1.gif
    //此拿到key，在js裡    
    ////http://manhua1020.104-250-136-234.cdndm5.com/9/8165/97094/1_6861.png?cid=97094&key=317e687f0aaf432a602d834edb07cc9c        
    echo $URL;
    echo "\n";
    $URL="http://www.dm5.com/{$ra[0]['link']}-p{$i}/";
    $data=`{$PWD}{$SP}bin{$SP}wget.exe -q --no-proxy -O - {$URL}`;
    $options=ARRAY();
    
    $C = curl_getPost_INIT($URL,"",$options);
        
    //$URL="http://css16.tel.cdndm.com/v201606091604/default/css/style2.css";  
    //$C = curl_getPost_INIT($URL,"",$options);  
    
    $m=ARRAY();
    $m['cid']=get_between_new($data,"var DM5_CID=",";"); //97094
    $m['mid']=get_between_new($data,"var DM5_MID=",";"); //8165
    $m['page']=$i;
    $m['uid']='0';
    $m['language']='1';
//     $b=http_build_query($m);
//     $URL="http://www.dm5.com/{$ra[0]['link']}/history.ashx?{$b}";
//     echo "{$URL}\n";
//     //$data=`/usr/bin/wget -O - {$URL}`;
//     $options['header']=ARRAY();
//     array_push($options['header'],"Content-Type:application/x-www-form-urlencoded");
//     array_push($options['header'],"DNT:1");
//     array_push($options['header'],"Referer:http://www.dm5.com/{$ra[0]['link']}/");
//     array_push($options['header'],"X-Requested-With:XMLHttpRequest");
//     array_push($options['header'],"Accept:application/json, text/javascript, */*");
//     array_push($options['header'],"Cache-Control:max-age=0");
//     //array_push($options['header'],"Cookie:view__arr=2; DM5_MACHINEKEY=77ef7dca-dfc4-4591-9265-dc7c957e9fb7; ComicHistoryitem_zh=History=20874,636014114212838098,226138,1,0,0,0,1|8165,636014132239641107,97094,1,0,0,0,8&ViewType=0; fastshow=true; readhistory_time=1-8165-97094-1; image_time_cookie=226138|636014114212798040|0,97094|636014132240982426|0; dm5imgpage=226138|1:1:46:0,97094|1:1:34:0; dm5cookieenabletest=1; dm5imgcooke=226138%7C2%2C97094%7C2");
//     array_push($options['header'],"If-Modified-Since:".date("D, d M Y ").sprintf("%02d",(date("H")-8)).date(":i:s")." GMT");
//     print_r($options['header']);
//     echo "History:{$URL}\n"; 
    //Mon, 13 Jun 2016 03:13:43 GMT"
    
    //$C = curl_getPost_continue($C['curl'],$URL,"",$options);
    //print_r($C);     
    unset($m['mid']);
    unset($m['uid']);
    $m['key']="";
    $b=http_build_query($m);    
    $URL="http://www.dm5.com/{$ra[0]['link']}/chapterfun.ashx?{$b}&gtk=6";
    
    echo "{$URL}\n";
    //exit();
    //$data=`/usr/bin/wget -O - {$URL}`;
    $options['header']=ARRAY();
    array_push($options['header'],"Content-Type:application/x-www-form-urlencoded");
    array_push($options['header'],"DNT:1");
    array_push($options['header'],"Referer:http://www.dm5.com/{$ra[0]['link']}/");
    array_push($options['header'],"X-Requested-With:XMLHttpRequest");
    $C = curl_getPost_continue($C['curl'],$URL,"",$options);
    //print_r($C);
    $pds = get_between_new($C['output'],",'|","'.split");

    //h://k.m-l-g-b.e.f/9
    $mpds = explode("|",$pds);
    $need_remove="var,pvalue,pix,key,http,function,for,return,length";
    $mneed_remove=explode(",",$need_remove);
    for($j=0,$max_j=count($mpds);$j<$max_j;$j++)
    {
      if(in_array($mpds[$j],$mneed_remove))
      {
        unset($mpds[$j]);
      }
      if(trim($mpds[$j])=="")
      {
        unset($mpds[$j]);
      }
    }
    $mpds=array_values($mpds);
    print_r($mpds);
    
    //http://manhua1020.104-250-136-234.cdndm5.com/9/8165/97094/1_6861.png?cid=97094&key=317e687f0aaf432a602d834edb07cc9c
    //http://manhua1020.104-250-136-234.cdndm5.com/9/8165/97094/1_6861.png?cid=97094&key=317e687f0aaf432a602d834edb07cc9c
    //http://manhua1020.104-250-136-234.cdndm5.com/9/8165/97094/1_6861.png?cid=97094&key=317e687f0aaf432a602d834edb07cc9c
    //http://manhua1020.104-250-136-234.cdndm5.com/9/8165/97094/1_6861.png?cid=97094&key=317e687f0aaf432a602d834edb07cc9c
    //http://manhua1020.104-250-136-234.cdndm5.com/9/8165/97094/1_6861.png?cid=97094&key=317e687f0aaf432a602d834edb07cc9c
    //array_push($options['header'],"If-None-Match:\"\"86ac939d23ecd1:0\"\"");
    
    $options['header']=ARRAY();
    array_push($options['header'],"User-Agent:Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:47.0) Gecko/20100101 Firefox/47.0");
    array_push($options['header'],"Accept:*/*");
    array_push($options['header'],"Accept-Language:zh-TW,zh;q=0.8,en-US;q=0.5,en;q=0.3");
    array_push($options['header'],"DNT:1");
    array_push($options['header'],"Referer:http://www.dm5.com/{$ra[0]['link']}/");    
    $nine = "";
    $URL="";
    if(is_string_like( $C['output'],"%h://g.l-k-b-c.f.e/%"))
    {
      $nine = get_between_new($C['output'],"h://g.l-k-b-c.f.e/","/p");
      /*
      Array
      (
          [0] => 97094
          [1] => png
          [2] => cid
          [3] => dm5imagefun
          [4] => 0826a74cab8e64000dbb2e24516c8527
          [5] => 136
          [6] => 234
          [7] => com
          [8] => cdndm5
          [9] => manhua1020
          [10] => 250
          [11] => 104
          [12] => 2_5388
          [13] => 8165
          [14] => 1_6861
      )
      */
      //http://manhua1020.104-250-136-234.cdndm5.com/9/8165/97094/1_6861.png?cid=97094&key=317e687f0aaf432a602d834edb07cc9c
      $URL = "http://{$mpds[9]}.{$mpds[11]}-{$mpds[10]}-{$mpds[5]}-{$mpds[6]}.{$mpds[8]}.{$mpds[7]}/{$nine}/{$mpds[13]}/{$mpds[0]}/{$mpds[14]}.{$mpds[1]}?cid={$mpds[0]}&key={$mpds[4]}";
    }
    else if(is_string_like( $C['output'],"%h://k.m-l-g-b.e.f/%"))
    {
      $nine = get_between_new($C['output'],"h://k.m-l-g-b.e.f/","/c");
       /*
       Array
      (
          [0] => png
          [1] => 97094
          [2] => dm5imagefun
          [3] => cid
          [4] => 0826a74cab8e64000dbb2e24516c8527
          [5] => 234
          [6] => 8165
          [7] => cdndm5
          [8] => com
          [9] => 136
          [10] => manhua1020
          [11] => 250
          [12] => 104
          [13] => 2_5388
          [14] => 6_8617
          [15] => 3_5388
          [16] => 4_8617
          [17] => 5_8617
      )
      
       */      
      //http://manhua1020.104-250-136-234.cdndm5.com/9/8165/97094/1_6861.png?cid=97094&key=317e687f0aaf432a602d834edb07cc9c
      $URL = "http://{$mpds[10]}.{$mpds[12]}-{$mpds[11]}-{$mpds[9]}-{$mpds[5]}.{$mpds[7]}.{$mpds[8]}/{$nine}/{$mpds[6]}/{$mpds[1]}/{$mpds[13]}.{$mpds[0]}?cid={$mpds[1]}&key={$mpds[4]}";      
    }
    else if(is_string_like( $C['output'],"%h://k.m-l-c-b.e.g/%"))
    {
      $nine = get_between_new($C['output'],"h://k.m-l-c-b.e.g/","/f");
      /*
      Array
      (
          [0] => png
          [1] => 97094
          [2] => dm5imagefun
          [3] => cid
          [4] => 0826a74cab8e64000dbb2e24516c8527
          [5] => 234
          [6] => 136
          [7] => cdndm5
          [8] => 8165
          [9] => com
          [10] => manhua1020
          [11] => 250
          [12] => 104
          [13] => 6_8617
          [14] => 3_5388
          [15] => 4_8617
          [16] => 5_8617
      )
      
      */
      //http://manhua1020.104-250-136-234.cdndm5.com/9/8165/97094/1_6861.png?cid=97094&key=317e687f0aaf432a602d834edb07cc9c
      $URL = "http://{$mpds[10]}.{$mpds[12]}-{$mpds[11]}-{$mpds[6]}-{$mpds[5]}.{$mpds[7]}.{$mpds[9]}/{$nine}/{$mpds[8]}/{$mpds[1]}/{$mpds[14]}.{$mpds[0]}?cid={$mpds[1]}&key={$mpds[4]}";
    }
    else if(is_string_like( $C['output'],"%h://l.k-g-c-b.f.e/%"))
    {
      $nine = get_between_new($C['output'],"h://l.k-g-c-b.f.e/","/m");
      /*
      Array
      (
          [0] => 97094
          [1] => png
          [2] => dm5imagefun
          [3] => cid
          [4] => 0826a74cab8e64000dbb2e24516c8527
          [5] => 234
          [6] => 136
          [7] => com
          [8] => cdndm5
          [9] => 250
          [10] => 104
          [11] => manhua1020
          [12] => 8165
          [13] => 6_8617
          [14] => 4_8617
          [15] => 5_8617
      )      
      */
      //http://manhua1020.104-250-136-234.cdndm5.com/9/8165/97094/1_6861.png?cid=97094&key=317e687f0aaf432a602d834edb07cc9c
      $URL = "http://{$mpds[11]}.{$mpds[10]}-{$mpds[9]}-{$mpds[6]}-{$mpds[5]}.{$mpds[8]}.{$mpds[7]}/{$nine}/{$mpds[12]}/{$mpds[0]}/{$mpds[14]}.{$mpds[1]}?cid={$mpds[0]}&key={$mpds[4]}";
    }
    else if(is_string_like( $C['output'],"%h://k.j-f-a-b.e.c/%"))
    {
      $nine = get_between_new($C['output'],"h://k.j-f-a-b.e.c/","/o");
      /*
      Array
      (
    [0] => 97094
    [1] => cid
    [2] => dm5imagefun
    [3] => 0826a74cab8e64000dbb2e24516c8527
    [4] => 136
    [5] => 234
    [6] => com
    [7] => cdndm5
    [8] => 250
    [9] => 104
    [10] => manhua1020
    [11] => png
    [12] => 8165
    [13] => 6_8617

      )      
      */
      //http://manhua1020.104-250-136-234.cdndm5.com/9/8165/97094/1_6861.png?cid=97094&key=317e687f0aaf432a602d834edb07cc9c
      $URL = "http://{$mpds[10]}.{$mpds[9]}-{$mpds[8]}-{$mpds[4]}-{$mpds[5]}.{$mpds[7]}.{$mpds[6]}/{$nine}/{$mpds[12]}/{$mpds[0]}/{$mpds[13]}.{$mpds[11]}?cid={$mpds[0]}&key={$mpds[3]}";
    }
    else if(is_string_like( $C['output'],"%l://k.o-n-m-e.b.h/%"))
    {
      $nine = get_between_new($C['output'],"l://k.o-n-m-e.b.h/","/c");
      /*
      Array
      (
    [0] => jpg
    [1] => 91325
    [2] => cid
    [3] => dm5imagefun
    [4] => 5683b35851a8635fc6cdd3e2fc95085f
    [5] => cdndm5
    [6] => 8165
    [7] => 74
    [8] => 9_3783
    [9] => 8_3783
    [10] => com
    [11] => manhua1019
    [12] => 152
    [13] => 250
    [14] => 104
    [15] => 10_5539
    [16] => 16_6511
    [17] => 15_6511
    [18] => 11_5539
    [19] => 12_7296
    [20] => 13_1525
    [21] => 14_3282

      )      
      */
      //http://manhua1019.104-250-152-74.cdndm5.com/9/8165/91325/8_3783.jpg?cid=91325&key=5683b35851a8635fc6cdd3e2fc95085f
      $URL = "http://{$mpds[11]}.{$mpds[14]}-{$mpds[13]}-{$mpds[12]}-{$mpds[7]}.{$mpds[5]}.{$mpds[10]}/{$nine}/{$mpds[6]}/{$mpds[1]}/{$mpds[9]}.{$mpds[0]}?cid={$mpds[1]}&key={$mpds[4]}";
    }
    else if(is_string_like( $C['output'],"%n://m.k-p-o-j.e.c/%"))
    {
      $nine = get_between_new($C['output'],"n://m.k-p-o-j.e.c/","/b");
      /*
      Array
      (
    [0] => jpg
    [1] => 91325
    [2] => cid
    [3] => dm5imagefun
    [4] => 5683b35851a8635fc6cdd3e2fc95085f
    [5] => 8165
    [6] => com
    [7] => cdndm5
    [8] => 9_3783
    [9] => 8_3783
    [10] => 7_3783
    [11] => 74
    [12] => 104
    [13] => manhua1019
    [14] => 152
    [15] => 250
    [16] => 16_6511
    [17] => 15_6511
    [18] => 14_3282
    [19] => 11_5539
    [20] => 10_5539
    [21] => 12_7296
    [22] => 13_1525
      )      
      */
      //http://manhua1019.104-250-152-74.cdndm5.com/9/8165/91325/8_3783.jpg?cid=91325&key=5683b35851a8635fc6cdd3e2fc95085f
      $URL = "http://{$mpds[13]}.{$mpds[12]}-{$mpds[15]}-{$mpds[14]}-{$mpds[11]}.{$mpds[7]}.{$mpds[6]}/{$nine}/{$mpds[5]}/{$mpds[1]}/{$mpds[9]}.{$mpds[0]}?cid={$mpds[1]}&key={$mpds[4]}";
    }
    else if(is_string_like( $C['output'],"%j://h.n-m-l-b.e.f/%"))
    {
      $nine = get_between_new($C['output'],"j://h.n-m-l-b.e.f/","/g");
      /*
      Array
      (
    [0] => jpg
    [1] => 91325
    [2] => dm5imagefun
    [3] => cid
    [4] => 5683b35851a8635fc6cdd3e2fc95085f
    [5] => 74
    [6] => 10_5539
    [7] => cdndm5
    [8] => com
    [9] => 8165
    [10] => manhua1019
    [11] => 152
    [12] => 250
    [13] => 104
    [14] => 11_5539
    [15] => 16_6511
    [16] => 15_6511
    [17] => 13_1525
    [18] => 12_7296
    [19] => 14_3282

      )      
      */
      //http://manhua1019.104-250-152-74.cdndm5.com/9/8165/91325/8_3783.jpg?cid=91325&key=5683b35851a8635fc6cdd3e2fc95085f
      $URL = "http://{$mpds[10]}.{$mpds[13]}-{$mpds[12]}-{$mpds[11]}-{$mpds[5]}.{$mpds[7]}.{$mpds[8]}/{$nine}/{$mpds[9]}/{$mpds[1]}/{$mpds[6]}.{$mpds[0]}?cid={$mpds[1]}&key={$mpds[4]}";
    }
    else if(is_string_like( $C['output'],"%m://l.o-r-q-p.k.e/%"))
    {
      $nine = get_between_new($C['output'],"m://l.o-r-q-p.k.e/","/b");
      /*
      Array
      (
    [0] => jpg
    [1] => 91325
    [2] => dm5imagefun
    [3] => cid
    [4] => 5683b35851a8635fc6cdd3e2fc95085f
    [5] => 8165
    [6] => 6_9553
    [7] => com
    [8] => 2_7796
    [9] => 4_6324
    [10] => 5_6324
    [11] => 3_7796
    [12] => cdndm5
    [13] => manhua1019
    [14] => 104
    [15] => 74
    [16] => 152
    [17] => 250
    [18] => 7_3783
    [19] => 16_6511
    [20] => 15_6511
    [21] => 14_3282
    [22] => 11_5539
    [23] => 8_3783
    [24] => 9_3783
    [25] => 13_1525
    [26] => 10_5539
    [27] => 12_7296
      )      
      */
      //http://manhua1019.104-250-152-74.cdndm5.com/9/8165/91325/8_3783.jpg?cid=91325&key=5683b35851a8635fc6cdd3e2fc95085f
      $URL = "http://{$mpds[13]}.{$mpds[14]}-{$mpds[17]}-{$mpds[16]}-{$mpds[15]}.{$mpds[12]}.{$mpds[7]}/{$nine}/{$mpds[5]}/{$mpds[1]}/{$mpds[8]}.{$mpds[0]}?cid={$mpds[1]}&key={$mpds[4]}";
    }
    else if(is_string_like( $C['output'],"%l://k.n-q-p-o.e.b/%"))
    {
      $nine = get_between_new($C['output'],"l://k.n-q-p-o.e.b/","/f");
      /*
      Array
      (
    [0] => jpg
    [1] => 91325
    [2] => dm5imagefun
    [3] => cid
    [4] => 5683b35851a8635fc6cdd3e2fc95085f
    [5] => com
    [6] => 7_3783
    [7] => cdndm5
    [8] => 8165
    [9] => 5_6324
    [10] => 6_9553
    [11] => 4_6324
    [12] => manhua1019
    [13] => 104
    [14] => 74
    [15] => 152
    [16] => 250
    [17] => 8_3783
    [18] => 16_6511
    [19] => 15_6511
    [20] => 14_3282
    [21] => 11_5539
    [22] => 9_3783
    [23] => 10_5539
    [24] => 12_7296
    [25] => 13_1525

      )      
      */
      //http://manhua1019.104-250-152-74.cdndm5.com/9/8165/91325/8_3783.jpg?cid=91325&key=5683b35851a8635fc6cdd3e2fc95085f
      $URL = "http://{$mpds[12]}.{$mpds[13]}-{$mpds[16]}-{$mpds[15]}-{$mpds[14]}.{$mpds[7]}.{$mpds[5]}/{$nine}/{$mpds[8]}/{$mpds[1]}/{$mpds[11]}.{$mpds[0]}?cid={$mpds[1]}&key={$mpds[4]}";
    } 
    else if(is_string_like( $C['output'],"%h://j.l-k-a-b.f.e/%"))
    {
      $nine = get_between_new($C['output'],"h://j.l-k-a-b.f.e/","/c");
      /*
      Array
      (
    [0] => 129597
    [1] => cid
    [2] => dm5imagefun
    [3] => c8efb62bd6eb0b7cdcfbf2864659fcb7
    [4] => 243
    [5] => 170
    [6] => 8165
    [7] => com
    [8] => cdndm5
    [9] => manhua1021
    [10] => 181
    [11] => 107
    [12] => png
    [13] => 1_2649
    [14] => jpg
    [15] => 2_5103
      )      
      */
      //http://manhua1021.107-181-243-170.cdndm5.com/9/8165/129597/1_2649.jpg?cid=129597&key=c8efb62bd6eb0b7cdcfbf2864659fcb7
      $URL = "http://{$mpds[9]}.{$mpds[11]}-{$mpds[10]}-{$mpds[4]}-{$mpds[5]}.{$mpds[8]}.{$mpds[7]}/{$nine}/{$mpds[6]}/{$mpds[0]}/{$mpds[13]}.{$mpds[14]}?cid={$mpds[0]}&key={$mpds[3]}";
    }
    else if(is_string_like( $C['output'],"%k://j.o-n-m-e.c.b/%"))
    {
      $nine = get_between_new($C['output'],"k://j.o-n-m-e.c.b/","/h");
      /*
      Array
      (
    [0] => png
    [1] => 131794
    [2] => dm5imagefun
    [3] => cid
    [4] => d0ea635eb35d0bbdbdd6568dc684e77d
    [5] => com
    [6] => cdndm5
    [7] => 122
    [8] => 3_3538
    [9] => 2_4792
    [10] => 8165
    [11] => manhua1021
    [12] => 249
    [13] => 181
    [14] => 107
    [15] => jpg
    [16] => 8_7414
    [17] => 5_7815
    [18] => 4_3527
    [19] => 6_6623
    [20] => 7_2604

      )      
      */
      //http://manhua1021.107-181-249-122.cdndm5.com/9/8165/129597/1_2649.jpg?cid=129597&key=c8efb62bd6eb0b7cdcfbf2864659fcb7
      $URL = "http://{$mpds[11]}.{$mpds[14]}-{$mpds[13]}-{$mpds[12]}-{$mpds[7]}.{$mpds[6]}.{$mpds[5]}/{$nine}/{$mpds[10]}/{$mpds[1]}/{$mpds[9]}.{$mpds[0]}?cid={$mpds[1]}&key={$mpds[4]}";
    }    
    else if(is_string_like( $C['output'],"%j://l.n-m-h-c.b.e/%"))
    {
      $nine = get_between_new($C['output'],"j://l.n-m-h-c.b.e/","/g");
      /*
      Array
      (
    [0] => png
    [1] => 131794
    [2] => dm5imagefun
    [3] => cid
    [4] => d0ea635eb35d0bbdbdd6568dc684e77d
    [5] => cdndm5
    [6] => 122
    [7] => com
    [8] => 3_3538
    [9] => 8165
    [10] => 249
    [11] => manhua1021
    [12] => 181
    [13] => 107
    [14] => 4_3527
    [15] => jpg
    [16] => 8_7414
    [17] => 5_7815
    [18] => 6_6623
    [19] => 7_2604


      )      
      */
      //http://manhua1021.107-181-249-122.cdndm5.com/9/8165/129597/1_2649.jpg?cid=129597&key=c8efb62bd6eb0b7cdcfbf2864659fcb7
      $URL = "http://{$mpds[11]}.{$mpds[13]}-{$mpds[12]}-{$mpds[10]}-{$mpds[6]}.{$mpds[5]}.{$mpds[7]}/{$nine}/{$mpds[9]}/{$mpds[1]}/{$mpds[8]}.{$mpds[0]}?cid={$mpds[1]}&key={$mpds[4]}";
    }
    else if(is_string_like( $C['output'],"%j://h.n-m-l-b.c.f/%"))
    {
      $nine = get_between_new($C['output'],"j://h.n-m-l-b.c.f/","/e");
      /*
      Array
      (
    [0] => png
    [1] => 131794
    [2] => cid
    [3] => dm5imagefun
    [4] => d0ea635eb35d0bbdbdd6568dc684e77d
    [5] => 122
    [6] => cdndm5
    [7] => 8165
    [8] => com
    [9] => 4_3527
    [10] => manhua1021
    [11] => 249
    [12] => 181
    [13] => 107
    [14] => jpg
    [15] => 8_7414
    [16] => 6_6623
    [17] => 5_7815
    [18] => 7_2604
      )      
      */
      //http://manhua1021.107-181-249-122.cdndm5.com/9/8165/129597/1_2649.jpg?cid=129597&key=c8efb62bd6eb0b7cdcfbf2864659fcb7
      $URL = "http://{$mpds[10]}.{$mpds[13]}-{$mpds[12]}-{$mpds[11]}-{$mpds[5]}.{$mpds[6]}.{$mpds[8]}/{$nine}/{$mpds[7]}/{$mpds[1]}/{$mpds[9]}.{$mpds[0]}?cid={$mpds[1]}&key={$mpds[4]}";
    }   
    else if(is_string_like( $C['output'],"%h://k.m-l-g-b.c.e/%"))
    {
      $nine = get_between_new($C['output'],"h://k.m-l-g-b.c.e/","/f");
      if(is_string_like( $C['output'],'%"/r.4","/m.4"%'))
      {
        //"/r.4","/m.4"
           /*
          Array
          (
        [0] => 131794
        [1] => png
        [2] => cid
        [3] => dm5imagefun
        [4] => d0ea635eb35d0bbdbdd6568dc684e77d
        [5] => 122
        [6] => cdndm5
        [7] => com
        [8] => 8165
        [9] => 249
        [10] => manhua1021
        [11] => 181
        [12] => 107
        [13] => 5_7815
        [14] => jpg
        [15] => 6_6623
        [16] => 7_2604
        [17] => 8_7414
    
          )      
          */
          //http://manhua1021.107-181-249-122.cdndm5.com/9/8165/129597/1_2649.jpg?cid=129597&key=c8efb62bd6eb0b7cdcfbf2864659fcb7
        $URL = "http://{$mpds[10]}.{$mpds[12]}-{$mpds[11]}-{$mpds[9]}-{$mpds[5]}.{$mpds[6]}.{$mpds[7]}/{$nine}/{$mpds[8]}/{$mpds[0]}/{$mpds[14]}.{$mpds[2]}?cid={$mpds[0]}&key={$mpds[4]}"; 
      }
      else if(is_string_like( $C['output'],'%"/n.4","/s.4","/t.4"%'))
      {
        /*
          Array
          (
        [0] => 132847
        [1] => png
        [2] => cid
        [3] => dm5imagefun
        [4] => 1ddc7c1ab65dc2e37b17f09d9e5b4827
        [5] => 234
        [6] => cdndm5
        [7] => com
        [8] => 8165
        [9] => 136
        [10] => manhua1021
        [11] => 250
        [12] => 104
        [13] => 2_6533
        [14] => jpg
        [15] => 3_8530
        [16] => 4_8226
        [17] => 5_4503   
          )      
          */
          //http://manhua1021.107-181-249-122.cdndm5.com/9/8165/129597/1_2649.jpg?cid=129597&key=c8efb62bd6eb0b7cdcfbf2864659fcb7
        $URL = "http://{$mpds[10]}.{$mpds[12]}-{$mpds[11]}-{$mpds[9]}-{$mpds[5]}.{$mpds[6]}.{$mpds[7]}/{$nine}/{$mpds[8]}/{$mpds[0]}/{$mpds[13]}.{$mpds[14]}?cid={$mpds[0]}&key={$mpds[4]}";
      }
      else if(is_string_like( $C['output'],'%"/r.6","/s.6","/t.o"%'))
      {
        /*
          Array
          (
    [0] => 132847
    [1] => cid
    [2] => png
    [3] => dm5imagefun
    [4] => 1ddc7c1ab65dc2e37b17f09d9e5b4827
    [5] => 234
    [6] => cdndm5
    [7] => com
    [8] => 8165
    [9] => 136
    [10] => manhua1021
    [11] => 250
    [12] => 104
    [13] => jpg
    [14] => 3_8530
    [15] => 4_8226
    [16] => 5_4503

          )      
          */
          //http://manhua1021.107-181-249-122.cdndm5.com/9/8165/129597/1_2649.jpg?cid=129597&key=c8efb62bd6eb0b7cdcfbf2864659fcb7
        $URL = "http://{$mpds[10]}.{$mpds[12]}-{$mpds[11]}-{$mpds[9]}-{$mpds[5]}.{$mpds[6]}.{$mpds[7]}/{$nine}/{$mpds[8]}/{$mpds[0]}/{$mpds[13]}.{$mpds[14]}?cid={$mpds[0]}&key={$mpds[4]}";      
      }
    } 
    else if(is_string_like( $C['output'],"%l://k.p-o-n-f.e.c/%"))
    {
      $nine = get_between_new($C['output'],"l://k.p-o-n-f.e.c/","/j");
      /*
      Array
      (
    [0] => png
    [1] => 137659
    [2] => cid
    [3] => dm5imagefun
    [4] => jpg
    [5] => 007d4b28cf208cd8253b52ae49421ffc
    [6] => com
    [7] => cdndm5
    [8] => 218
    [9] => 3_4135
    [10] => 2_1992
    [11] => 8165
    [12] => manhua1023
    [13] => 139
    [14] => 250
    [15] => 104
    [16] => 9_3527
    [17] => 8_9295
    [18] => 5_4037
    [19] => 4_1568
    [20] => 6_6699
    [21] => 7_9615

      )      
      */
      //http://manhua1023.104-250-139-218.cdndm5.com/9/8165/137659/2_1992.png?cid=137659&key=007d4b28cf208cd8253b52ae49421ffc
      $URL = "http://{$mpds[12]}.{$mpds[15]}-{$mpds[14]}-{$mpds[13]}-{$mpds[8]}.{$mpds[7]}.{$mpds[6]}/{$nine}/{$mpds[11]}/{$mpds[1]}/{$mpds[10]}.{$mpds[0]}?cid={$mpds[1]}&key={$mpds[5]}";
    }
    else if(is_string_like( $C['output'],"%k://l.j-o-n-c.e.h/%"))
    {
      $nine = get_between_new($C['output'],"k://l.j-o-n-c.e.h/","/g");
      /*
      Array
      (
    [0] => png
    [1] => 137659
    [2] => jpg
    [3] => cid
    [4] => dm5imagefun
    [5] => 007d4b28cf208cd8253b52ae49421ffc
    [6] => 218
    [7] => cdndm5
    [8] => 3_4135
    [9] => 8165
    [10] => com
    [11] => 104
    [12] => manhua1023
    [13] => 139
    [14] => 250
    [15] => 4_1568
    [16] => 9_3527
    [17] => 8_9295
    [18] => 6_6699
    [19] => 5_4037
    [20] => 7_9615


      )      
      */
      //http://manhua1023.104-250-139-218.cdndm5.com/9/8165/137659/2_1992.png?cid=137659&key=007d4b28cf208cd8253b52ae49421ffc
      $URL = "http://{$mpds[12]}.{$mpds[11]}-{$mpds[14]}-{$mpds[13]}-{$mpds[6]}.{$mpds[7]}.{$mpds[10]}/{$nine}/{$mpds[9]}/{$mpds[1]}/{$mpds[8]}.{$mpds[2]}?cid={$mpds[1]}&key={$mpds[5]}";
    }
    else if(is_string_like( $C['output'],"%j://h.n-m-l-c.g.f/%"))
    {
      $nine = get_between_new($C['output'],"j://h.n-m-l-c.g.f/","/e");
      /*
      Array
      (
    [0] => png
    [1] => 137659
    [2] => jpg
    [3] => cid
    [4] => dm5imagefun
    [5] => 007d4b28cf208cd8253b52ae49421ffc
    [6] => 218
    [7] => 8165
    [8] => com
    [9] => cdndm5
    [10] => manhua1023
    [11] => 139
    [12] => 250
    [13] => 104
    [14] => 5_4037
    [15] => 9_3527
    [16] => 6_6699
    [17] => 7_9615
    [18] => 8_9295
      )      
      */
      //http://manhua1023.104-250-139-218.cdndm5.com/9/8165/137659/2_1992.png?cid=137659&key=007d4b28cf208cd8253b52ae49421ffc
      $URL = "http://{$mpds[10]}.{$mpds[13]}-{$mpds[12]}-{$mpds[11]}-{$mpds[6]}.{$mpds[9]}.{$mpds[8]}/{$nine}/{$mpds[7]}/{$mpds[1]}/{$mpds[14]}.{$mpds[2]}?cid={$mpds[1]}&key={$mpds[5]}";
    }
    else if(is_string_like( $C['output'],"%h://k.m-l-g-b.f.c/%"))
    {
      $nine = get_between_new($C['output'],"h://k.m-l-g-b.f.c/","/e");
      /*
      Array
      (
    [0] => 137659
    [1] => jpg
    [2] => cid
    [3] => dm5imagefun
    [4] => 007d4b28cf208cd8253b52ae49421ffc
    [5] => 218
    [6] => com
    [7] => 8165
    [8] => cdndm5
    [9] => 139
    [10] => manhua1023
    [11] => 250
    [12] => 104
    [13] => 9_3527
    [14] => 7_9615
    [15] => png
    [16] => 8_9295

      )      
      */
      //http://manhua1023.104-250-139-218.cdndm5.com/9/8165/137659/2_1992.png?cid=137659&key=007d4b28cf208cd8253b52ae49421ffc
      $URL = "http://{$mpds[10]}.{$mpds[12]}-{$mpds[11]}-{$mpds[9]}-{$mpds[5]}.{$mpds[8]}.{$mpds[6]}/{$nine}/{$mpds[7]}/{$mpds[0]}/{$mpds[14]}.{$mpds[1]}?cid={$mpds[0]}&key={$mpds[4]}";
    }
    else if(is_string_like( $C['output'],"%g://f.k-j-a-b.e.c/%"))
    {
      $nine = get_between_new($C['output'],"g://f.k-j-a-b.e.c/","/q");
      /*
      Array
      (
    [0] => 264232
    [1] => jpg
    [2] => cid
    [3] => dm5imagefun
    [4] => da16d51fc0b054670bf1187ffb93672f
    [5] => 139
    [6] => 218
    [7] => com
    [8] => cdndm5
    [9] => manhua1025
    [10] => 250
    [11] => 104
    [12] => 11
    [13] => 1_2898
    [14] => 10684
    [15] => 2_7605


      )      
      */
      //http://manhua1025.104-250-139-218.cdndm5.com/11/10684/257335/1_4816.jpg?cid=257335&key=01ad79974af4dc0bdda14be987168107
      $URL = "http://{$mpds[9]}.{$mpds[11]}-{$mpds[10]}-{$mpds[5]}-{$mpds[6]}.{$mpds[8]}.{$mpds[7]}/{$mpds[12]}/{$mpds[14]}/{$mpds[0]}/{$mpds[13]}.{$mpds[1]}?cid={$mpds[0]}&key={$mpds[4]}";
    }
    
   $unpacker = new JavaScriptUnpacker();
   $unpacked = $unpacker->Unpack($C['output']);
   //var pix="http://manhua1025.104-250-139-218.cdndm5.com/11/10684/264232"
   /*
   var pvalue=["/2_7605.jpg","/3_1795.jpg","/4_1434.jpg","/5_8811.jpg","/6_8058.jpg",
   "/7_8872.jpg","/8_1742.jpg","/9_6306.jpg","/10_6576.jpg","/11_7631.jpg","/12_8909.jpg","/13_6032.jpg","/14_3436.jpg","/15_1419.jpg","/16_3062.jpg"];for(var i=0;i<pvalue.length;i++){
   pvalue[i]=pix+pvalue[i]+'?cid=264232&key=da16d51fc0b054670bf1187ffb93672f'}
   */
   $tmp_URL = get_between_new($unpacked,"var pix=\"","\";");
   $tmp_PAGE = get_between_new($unpacked,"var pvalue=[\"","\",");
   if($tmp_PAGE==""){
     $tmp_PAGE = get_between_new($unpacked,"var pvalue=[\"","\"]");
   }
   $tmp_CID_KEY = get_between_new($unpacked,"pix+pvalue[i]+'","'}");
   
   echo "unpacked:{$unpacked}\n\n";
   echo "tmp_URL:{$tmp_URL}\n";
   echo "tmp_PAGE:{$tmp_PAGE}\n";
   echo "tmp_CID_KEY:{$tmp_CID_KEY}\n";
   
   $URL = "{$tmp_URL}{$tmp_PAGE}{$tmp_CID_KEY}";  
    
    
    //sleep(1);
    echo "{$URL}\n";
    //exit();    
    
    $C = curl_getPost_INIT($URL,"",$options);
    //print_r($C);
    /*
     link 下網網址	cname 漫名	req 冊名	total_pages 總頁數
      m97094	喪女	喪女 外传：第1话 	6
      m104921	喪女	喪女 外传：第2话 	2
    */
    if($C['output']!="" && strlen($C['output'])>=8000)
    {
      file_put_contents("{$ini['COMIC_PATH']}{$SP}{$big5_cname}{$SP}{$big5_req}{$SP}{$i}.png",$C['output']);
      echo "Download success {$i}...\n";
      $failure_count=0;
    }
    else
    {
      $all_done=false;
      echo "Download failure {$i}...{$URL}\n";
      $failure_count++;
      if($failure_count>=4)
      {
        $m=ARRAY();
        if($all_done==true)
        {
          $m['status']='2';
        }
        else
        {
          $m['status']='0';
        }
        updateSQL($pdo,'mycomic_dm5',$m,"`id`='{$ra[0]['id']}'");
        exit();    
      }
      $i-=1;
    }
    //$h = get_headers($URL);
    //print_r($h);
    //$data=`/usr/bin/wget --no-proxy -O - {$URL}`;
    //echo "\n{$data}\n";
    sleep(2);
    //exit();
  }
  $m=ARRAY();
  if($all_done==true)
  {
    $m['status']='2';
  }
  else
  {
    $m['status']='0';
  }
  updateSQL($pdo,'mycomic_dm5',$m,"`id`='{$ra[0]['id']}'");