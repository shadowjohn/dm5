<?php
  include 'inc/config.php';    
  $GETS_STRING="mode";
  $GETS=getGET_POST($GETS_STRING,'GET');   
  $PWD=dirname(__FILE__);
 
  function comic_log($data)
  {
    global $PWD;
    @mkdir("{$PWD}/tmp",0777,true);
    file_put_contents("{$PWD}/tmp/log.txt",$data);
  }
  switch($GETS['mode'])
  {
    case 'reload_list':
           
      $fd = new_glob("{$ini['COMIC_PATH']}{$SP}*");
      $output=ARRAY();      
      foreach($fd as $k)
      {
        $d = ARRAY();
        $bn = basename($k);        
        $d['etitle']=$bn;
        $big5k = dbcconv($k, 0);  
        $big5k = utf8tobig5($big5k);         
        $mta = file_get_contents("{$big5k}{$SP}metadata.txt");
           
        $jmta=json_decode($mta,true);
        $d['title']=$jmta['title'];
        $d['link']=$jmta['link'];
        $st = dbcconv($d['title'], 0);  
        $d['ltitle']="<a req='{$d['title']}' href='?id=".urlencode($d['title'])."'>{$st}</a>";
        foreach($jmta as $kk=>$vv)
        {
          $d[$kk]=$vv;          
        }
        array_push($output,$d);        
      }
      //print_r($output);
      echo json_encode($output,true);
      exit();
      break;
    case 'add_new_comic':
      $POSTS_STRING="book_url";
      $POSTS=getGET_POST($POSTS_STRING,'POST');
      $URL=$POSTS['book_url'];
      if(!is_string_like($URL,'http://www.dm5%'))
      {
        exit();
      }
      $C = curl_getPost_INIT($URL,"",$options=null);
      $data = $C['output'];
      $t = getDomHTML($data ,".inbt_title_h2");
      $title = strip_tags(trim($t[0]));
      if($title=="")
      {
        echo "No Title...";
        exit();
      }                
      @mkdir("{$ini['COMIC_PATH']}{$SP}{$title}",0777);
      $t_list = getDomHTML($data ,".tg");
      $metadata = ARRAY();
      $metadata['title']=$title;
      $big5_title = dbcconv($title, 0);
      $big5_title = utf8tobig5($big5_title);      
      $cs = ARRAY();
      //print_r($t_list);
      foreach($t_list as $v)
      {
         $d = ARRAY();
         //[26] => <a class="tg" href="/m200339/" title="喪女 第70话">第70话</a>
         preg_match_all("/href=\"\/(.*)\/\"\stitle=\"(.*)\">/",$v,$m);
         //print_r($m);
         $path = "{$ini['COMIC_PATH']}{$SP}{$title}{$SP}{$m[2][0]}";
         $path = dbcconv($path, 0);         
         $path = utf8tobig5($path);
         mkdir($path,0777,true);
         //mkdir("C:\\comic\\你好",0777);
         //echo `mkdir "{$path}"`;
         $d['link']=$m[1][0];
         $d['s_title']=$m[2][0];
         array_push($cs,$d);
      }
      $metadata['items']=$cs;
      file_put_contents("{$ini['COMIC_PATH']}{$SP}{$big5_title}{$SP}metadata.txt",json_encode($metadata,true));
      //print_r($t_list);
      //comic_log(print_r($t_list,true));
      exit();
      break;
    case 'reload_comic_list':
      $POSTS_STRING="cname";
      $POSTS=getGET_POST($POSTS_STRING,'POST');
      $big5cname = dbcconv($POSTS['cname'], 0); 
      $big5cname=utf8tobig5($big5cname);
      $data = file_get_contents("{$ini['COMIC_PATH']}{$SP}{$big5cname}{$SP}metadata.txt");
      $jmta=json_decode($data,true);
      //$jmta['items']=array_values(array_unique($jmta['items']));
      
      $jmta['items']=array_values_array_unique_arr($jmta['items']);
            
      natcasesort($jmta['items']);  
      $jmta['items']=array_sort_new($jmta['items'], 's_title', $order='SORT_ASC');
      $output = ARRAY();
      $output=$jmta['items'];
      //print_r($output);
      //exit();
      $SQL="
      SELECT * FROM `mycomic_dm5`
      WHERE
        1=1 
        AND `cname`='{$POSTS['cname']}'        
      ";
      $ra=selectSQL($pdo,$SQL);
      
      $select_opts=ARRAY();
      for($i=0,$max_i=count($output);$i<$max_i;$i++)
      {
        $st = dbcconv($output[$i]['s_title'],0); 
        array_push($select_opts,"<option link='{$output[$i]['link']}' cname='{$POSTS['cname']}' req='{$output[$i]['s_title']}' value='{$output[$i]['s_title']}'>{$st}</options>");
        //print_r($output[$i]);
        //print_r($output[$i]);
        //exit();        
        $output[$i]['options']="<a reqq='download' link='{$output[$i]['link']}' cname='{$POSTS['cname']}' req='{$output[$i]['s_title']}' href='javascript:;' class='download'>下載</a>";
        $big_s_title = utf8tobig5(dbcconv($output[$i]['s_title'],0));
        //$f_jpg = new_glob("{$ini['COMIC_PATH']}{$SP}{$big5cname}{$SP}{$big_s_title}{$SP}*.jpg");
        //$f_png = new_glob("{$ini['COMIC_PATH']}{$SP}{$big5cname}{$SP}{$big_s_title}{$SP}*.png");
        $f=ARRAY();
        $f_jpg=ARRAY();
        $f_png=ARRAY();
        $f=array_merge($f_jpg,$f_png);
        
        natcasesort($f);
        $f = array_values(array_unique($f));
        //echo "{$PWD}/comic/{$POSTS['cname']}/{$output[$i]['s_title']}/*.jpg";
        //exit();
        $pa = ARRAY();
        $step=1;
        $output[$i]['fake_s_title']="<a target='_blank' href='http://www.dm5.com/{$output[$i]['link']}'>{$output[$i]['s_title']}</a>";
        for($j=0,$max_j=count($ra);$j<$max_j;$j++)
        {
          if($output[$i]['s_title']==$ra[$j]['req'])
          {
            $output[$i]['total_pages']=$ra[$j]['total_pages'];
            $output[$i]['status']=$ra[$j]['status'];
            break;
          }
        }     
        if($output[$i]['total_pages']!="")
        {
          $output[$i]['fake_s_title'].="&nbsp;({$output[$i]['total_pages']})";
        }           
        foreach($f as $v)
        {
          $bn=basename($v);
          $mn=mainname($v);
          $d="<a target='_blank' onMouseOver=\"show_small(this.href);\" href='comic/{$POSTS['cname']}/{$output[$i]['s_title']}/{$bn}'>{$mn}</a>&nbsp;";
          array_push($pa,$d);
          $step++;
        }
        $output[$i]['page']=implode('',$pa);
        $output[$i]['page'].="<div reqq='page' req='{$output[$i]['link']}'></div>";
      }  
      //$output=array_values(array_unique($output));
      //echo print_table($output,'fake_s_title,options,page','冊,下載,頁','itemtable');            
      ?>
      請選擇：
      <select id="comic_lists">
      <option value=''>--請選擇--</option>
      <?php
      echo implode("\n",$select_opts);
      ?>
      </select>
      <input type="button" id="download_btn" disabled value="下載">
      <script language="javascript">
      function loadComic(obj)
      {
        myAjax_async("api.php?mode=load_comics",obj,function(data){
          //$("#download_status").html(data);
          //alert(data);
          execInnerScript(data);                      
        });
      }
      function loadComic_loop(obj)
      {
              
        window['load_comic_interval']=setTimeout(function(){
            myAjax_async("api.php?mode=load_comics",obj,function(data){            
              execInnerScript(data);                      
            });
            loadComic_loop(obj);                
        },2000);
        
      }
      $(document).ready(function(){
        $("#comic_lists").unbind("change");
        $("#comic_lists").change(function(){
          if($(this).val()=='')
          {
            $("#download_btn").prop('disabled',true);
            if(typeof(window['load_comic_interval'])!="undefined"){
              clearInterval(window['load_comic_interval']);
            }
          }
          else
          {
            $("#download_btn").prop('disabled',false);
            $("#comic_book div").remove();
            //讀圖
            if(typeof(window['load_comic_interval'])!="undefined"){
              clearInterval(window['load_comic_interval']);
            }
            var val= $("#comic_lists").val();   
            var cd = $("#comic_lists option[value='"+val+"']");
            //alert($("#comic_lists option[value='"+val+"']").attr('cname'));
            var link = cd.attr('link');
            var cname = cd.attr('cname');
            var req = cd.attr('req');
            var o=new Object();
            o['link']=link;
            o['cname']=cname;
            o['req']=req;
            loadComic(o);
            loadComic_loop(o);            
          } 
        });
        $("#download_btn").unbind("click");
        $("#download_btn").click(function(){
          //alert($("#comic_lists").val());
          var val= $("#comic_lists").val();   
          var cd = $("#comic_lists option[value='"+val+"']");
          //alert($("#comic_lists option[value='"+val+"']").attr('cname'));
          var link = cd.attr('link');
          var cname = cd.attr('cname');
          var req = cd.attr('req');
          var o=new Object();
          o['link']=link;
          o['cname']=cname;
          o['req']=req;
          //alert(print_r(o,true));
          //return;
         
           
          myAjax_async("api.php?mode=download",o,function(data){
            //$("#download_status").html(data);
            execInnerScript(data);                       
          });
           
          
        });        
      });
      </script>
      <br>
      <hr>
      <div id='download_status'></div>
      <div id='comic_book'></div>
      <?php
      exit();
      break;
    case 'download':
      $POSTS_STRING="link,cname,req";
      $POSTS=getGET_POST($POSTS_STRING,'POST');
      $URL = "http://www.dm5.com/";
      $C = curl_getPost_INIT($URL,"",$options=null);
      $URL = "http://www.dm5.com/{$POSTS['link']}-p1";
      
      //$C = curl_getPost_INIT($URL,"",$options=null);
      $C['output']=`{$PWD}{$SP}bin{$SP}wget.exe -q -O - {$URL} `;
      
      $totals = get_between_new($C['output']," var DM5_IMAGE_COUNT=",";");
     
      $m=ARRAY();
      $m['cname']=$POSTS['cname'];
      $m['link']=$POSTS['link'];
      $m['req']=$POSTS['req'];
      $m['total_pages']=$totals;
      $m['status']='0';
      $SQL="
      SELECT *
      FROM `mycomic_dm5`
      WHERE
        1=1
        AND `link`='{$POSTS['link']}'
        AND `cname`='{$POSTS['cname']}'
        AND `req`='{$POSTS['req']}'
      LIMIT 1
      ";
      $ra=selectSQL($pdo,$SQL);
      if(count($ra)==0)
      {
        insertSQL($pdo,'mycomic_dm5',$m);
      }
      else
      {
        updateSQL($pdo,'mycomic_dm5',$m,"`id`='{$ra[0]['id']}'");
      }
      
      runAsynchronously("{$PWD}{$SP}..{$SP}php{$SP}php.exe"," {$PWD}{$SP}download_comic.php");
      //$WshShell = new COM("WScript.Shell");
      //$oExec = $WshShell->Run("CMD /C {$PWD}{$SP}..{$SP}php{$SP}php-win.exe -f {$PWD}{$SP}download_comic.php", 7, false);
      //execInBackground("{$PWD}{$SP}download_comic.php");
      
//       <script language="javascript">
//       myAjax_async("download_comic.php","",function(data){
//         
//       });
//       </script>
      
      exit();
      break;
    case 'load_comics':
      $POSTS_STRING="link,cname,req";
      $POSTS=getGET_POST($POSTS_STRING,'POST');
      $SQL="
      SELECT 
        *
      FROM `mycomic_dm5`
      WHERE
        1=1
        AND `link`='{$POSTS['link']}'
        AND `cname`='{$POSTS['cname']}'
        AND `req`='{$POSTS['req']}'
      LIMIT 1
      ";
      $ra=selectSQL($pdo,$SQL);
      if(count($ra)==0)
      {
        exit();
      }
      ?>
      <script language="javascript">
      $(document).ready(function(){
        if($("#comic_book div").size()==0)
        {
          for(i=0;i<  <?=$ra[0]['total_pages'];?> ;i++)
          {
            $("#comic_book").append("<div page=\""+i+"\"></div>");
          }
        }                  
      
      <?php
      $big5_cname=utf8tobig5(dbcconv($ra[0]['cname'],0));
      $big5_req = utf8tobig5(dbcconv($ra[0]['req'],0));
      for($i=1;$i<=$ra[0]['total_pages'];$i++)
      {               
        
        $path = "{$ini['COMIC_PATH']}{$SP}{$big5_cname}{$SP}{$big5_req}{$SP}{$i}.png";
        //$b64 = @base64_encode(@file_get_contents($path));
        $b64_path=base64_encode(base64_encode($path));
        //if($b64!="")
        {                         
          ?>
          if($("#comic_book div[page='<?=$i;?>']").html()=="")
          {            
            var t=time();
            $("#comic_book div[page='<?=$i;?>']").html("<img req='comics' r='c_<?=$i;?>' src='photo.php?_t="+t+"&url=<?=$b64_path;?>'><br><br>");
          }          
          <?php
        }
      }                  
      ?>
        $("img[req='comics']").error(function(){
          $(this)[0].onerror = null;
          (function(x){  
            setTimeout(function(){
              var src = $(x).attr('src');
              var s = parse_url(src);             
              var sq = {};
              parse_str(s['query'],sq);
              sq['t']=time();
              var new_src="photo.php?"+http_build_query(sq);
              $(x).attr('src',new_src);
            },1000);
          })(this);
        });
      });
      </script>
      <?php
      exit();
      break;
  }