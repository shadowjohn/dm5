<!DOCTYPE html>
<html lang="zh-TW">
<head>
  <title> Menu </title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF8">
  <script language="javascript" src="inc/javascript/jquery/jquery-1.8.3.min.js"></script>
  <script language="javascript" src="inc/javascript/php/php.js"></script>
  <script language="javascript" src="inc/javascript/include.js"></script>
  <link href="css/reset.css" rel="stylesheet" type="text/css">
  <link href="css/style.css" rel="stylesheet" type="text/css">
  <script language="javascript">
  function reload_list(){
    var d = myAjax("api.php?mode=reload_list","");            
    var data = json_decode(d,true); 
    
    if(data=="") return;
    var table = print_table(data,"ltitle","漫畫列表","clist");
    $("#comic_list_td").html(table);
    $("#comic_list_td a").unbind("click");
    $("#comic_list_td a").click(function(){
      var req = $(this).attr('req');
      if(typeof(window['load_comic_interval'])!="undefined"){
        clearInterval(window['load_comic_interval']);
      }
      reload_comic_list(req);
      return false;
    });
  }
  function reload_comic_list(comic_name){
    var o = new Object();
    o['cname']=comic_name;
    var data = myAjax("api.php?mode=reload_comic_list",o);
    $("#comic_main_td").html(data);
  }
  function add_new_comic(){  
    var o = new Object();
    o['book_url']=trim($("#add_comic_text").val());
    var message="";
    if(o['book_url']=="")
    {
      message+="請輸入漫畫的路徑...";
    }
    if(message!="")
    {
      alert(message);
      return false;
    }
    else
    {
      var data = myAjax("api.php?mode=add_new_comic",o);
      //alert(data);
      reload_list();
      return true;
    }
  }
  </script>
  <script language="javascript">
    window['myWW']="";
    var cursorX;
    var cursorY;
    function show_small(URL){
      //comment("<img src='"+URL+"' width='80'>",100,100);
      $("#"+window['myWW']).remove();
      window['myWW']=myW("<img src='"+URL+"' width='150'>",function(id){    
        $("#"+id).css({
          'left':(cursorX+50)+'px',
          'top':(cursorY+50)+'px'        
        });
      },{});
    }
    $(document).ready(function(){
//       //load list reload_list    
//       document.onmousemove = function(e){
//           cursorX = e.pageX;
//           cursorY = e.pageY;
//       };
      reload_list();
      $("#add_new_comic_button").unbind("click");
      $("#add_new_comic_button").click(function(){
        add_new_comic();
      });
    });
  </script>
<style>
#output{
  height:150px;
  overflow:auto;
  border:1px solid #000;
}
</style>

<script>

$(document).ready(function() {

});
</script>
</head>
<body>
<center>
  DM5漫畫下載機程式<br>
  下載路徑：http://www.dm5.com/
</center>
  新漫畫網址：<input type="text" value="http://www.dm5.com/manhua-sangnv/" id="add_comic_text" size="50" placeholder="請輸入網址，如：http://www.dm5.com/manhua-sangnv/">
  <input type="button" id="add_new_comic_button" value="加入">
  <!--/form-->
  <hr>
  <table border="1" class="thetable">
    <tr>
      <td valign="top">
        <div align="right">
          <input type="button" value="Reload" onClick="reload_list();">
        </div>
      </td>
      <td>
      </td>
    </tr>
    <tr>
      <td valign="top" id="comic_list_td"></td>
      <td valign="top" id="comic_main_td"></td>
    </tr>
  </table>
  <br>  
  <div style="">
  </div>
</center>
</body>
</html>
