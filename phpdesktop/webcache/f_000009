//複製URL地址
var userAgent = navigator.userAgent.toLowerCase();
var is_opera = userAgent.indexOf('opera') != -1 && opera.version();
var is_moz = (navigator.product == 'Gecko') && userAgent.substr(userAgent.indexOf('firefox') + 8, 3);
var is_ie = (userAgent.indexOf('msie') != -1 && !is_opera) && userAgent.substr(userAgent.indexOf('msie') + 5, 3);
var is_safari = (userAgent.indexOf('webkit') != -1 || userAgent.indexOf('safari') != -1);
/*
  //iframe包含
  if (top.location != location) {
  	top.location.href = location.href;
  }

  function $(id) {
	 return document.getElementById(id);
  }
*/

function setCopy(_sTxt){
	if(is_ie) {
		clipboardData.setData('Text',_sTxt);
		alert ("網址「"+_sTxt+"」\n已經複製到您的剪貼板中\n您可以使用Ctrl+V快捷鍵粘貼到需要的地方");
	} else {
		prompt("請複製網站地址:",_sTxt); 
	}
}


String.prototype.trim=function(){return this.replace(/(^\s*)|(\s*$)/g,"")};

function handleEnter (field, event) {
   // 按 enter 不會 submit onkeypress="return handleEnter(this, event);"         
		var keyCode = event.keyCode ? event.keyCode : event.which ? event.which : event.charCode;
		if (keyCode == 13) {
			var i;
			for (i = 0; i < field.form.elements.length; i++)
				if (field == field.form.elements[i])
					break;
			i = (i + 1) % field.form.elements.length;
			field.form.elements[i].focus();
			return false;
		} 
		else
		return true;
	}  


function  execInnerScript(innerhtml)
{
  var  temp=innerhtml.replace(/\n|\r/g,"");
  var  regex=/<script.+?<\/script>/gi;
  var  arr=temp.match(regex);
  if(arr)
  {
    for(var iiiiiiiiii_iii=0;iiiiiiiiii_iii<arr.length;iiiiiiiiii_iii++)
    {
      var  temp1=arr[iiiiiiiiii_iii];
      var  reg=new  RegExp("^<script(.+?)>(.+)<\/script>$","gi");
      reg.test(temp1);
      eval(RegExp.$2);
    }
  }
}


  
function getWindowSize(){
  var myWidth = 0, myHeight = 0;
  if( typeof( window.innerWidth ) == 'number' ) {
    //Non-IE
    myWidth = window.innerWidth;
    myHeight = window.innerHeight;
  } else if( document.documentElement && ( document.documentElement.clientWidth || document.documentElement.clientHeight ) ) {
    //IE 6+ in 'standards compliant mode'
    myWidth = document.documentElement.clientWidth;
    myHeight = document.documentElement.clientHeight;
  } else if( document.body && ( document.body.clientWidth || document.body.clientHeight ) ) {
    //IE 4 compatible
    myWidth = document.body.clientWidth;
    myHeight = document.body.clientHeight;      
  }
  var a=new Object();
  a['width']=myWidth;
  a['height']=myHeight;
  return a;
}
 
/*
	//給jQuery擴充的 cookie 功能
		http://www.stilbuero.de/2006/09/17/cookie-plugin-for-jquery/

	jQuery操作cookie的插件,大概的使用方法如下

	设置cookie的值
			$.cookie('the_cookie', ‘the_value');
	新建一个cookie 包括有效期 路径 域名等
			$.cookie('the_cookie', ‘the_value', {expires: 7, path: ‘/', domain: ‘jquery.com', secure: true});
	新建cookie
		  $.cookie('the_cookie', ‘the_value');
	删除一个cookie
		  $.cookie('the_cookie', null);

*/
jQuery.cookie = function(name, value, options) {
    if (typeof value != 'undefined') { // name and value given, set cookie
        options = options || {};
        if (value === null) {
            value = '';
            options.expires = -1;
        }
        var expires = '';
        if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
            var date;
            if (typeof options.expires == 'number') {
                date = new Date();
                date.setTime(date.getTime() + (options.expires * 24 * 60 * 60 * 1000));
            } else {
                date = options.expires;
            }
            expires = '; expires=' + date.toUTCString(); // use expires attribute, max-age is not supported by IE
        }
        var path = options.path ? '; path=' + options.path : '';
        var domain = options.domain ? '; domain=' + options.domain : '';
        var secure = options.secure ? '; secure' : '';
        document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
    } else { // only name given, get cookie
        var cookieValue = null;
        if (document.cookie && document.cookie != '') {
            var cookies = document.cookie.split(';');
            for (var i = 0; i < cookies.length; i++) {
                var cookie = jQuery.trim(cookies[i]);
                // Does this cookie string begin with the name we want?
                if (cookie.substring(0, name.length + 1) == (name + '=')) {
                    cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                    break;
                }
            }
        }
        return cookieValue;
    }
};

//我的ajax
function myAjax(url,postdata)
{
  var tmp = $.ajax({
      url: url,
      type: "POST",
      data: postdata,
      async: false
   }).responseText;
  return tmp;
}
function myAjax_async(url,postdata,func)
{
  $.ajax({
      url: url,
      type: "POST",
      data: postdata,
      async: true,
      success: function(html){
        func(html);        
      }
  });  
}
//自然滑動機
function div_motion(domid)
{
	var pre_name="3WA_"+new Date().getTime();
	
    window[pre_name+'paperdown']=0;
    window[pre_name+'motion']=0;
    $("#"+domid).mousedown(function(event){
      if(window[pre_name+'paperdown']!=0)
      {		
		  $("#"+domid).stop();
	  }
      window[pre_name+'startmovetime']=new Date().getTime();
      window[pre_name+'paperdown']=1;
      window[pre_name+'moveX']=event.pageX;
      window[pre_name+'moveY']=event.pageY;
      window[pre_name+'motion']=0;                          
    });
    $("#"+domid).mouseup(function(){
	   window[pre_name+'endmovetime']=new Date().getTime();
	   window[pre_name+'lastX']=this.scrollLeft;
	   window[pre_name+'lastY']=this.scrollTop;
	   var orz=window[pre_name+'endmovetime']-window[pre_name+'startmovetime'];       
	   if(orz>=15){
		 orz=0;    
		 window[pre_name+'paperdown']=0; 
		 window[pre_name+'motion']=0;    
	   }
	   else
	   {
		 orz=15-orz;                                
		 window[pre_name+'motion']=1;
		 $("#"+domid).animate({ 
		   'scrollLeft':(window[pre_name+'lastX']+(window[pre_name+'lastX']-window[pre_name+'moveSX'])/0.1),
		   'scrollTop':(window[pre_name+'lastY']+(window[pre_name+'lastY']-window[pre_name+'moveSY'])/0.05)
		 },{
			duration:orz*100,
			query:false,
			complete:function(){
			window[pre_name+'paperdown']=0;
			window[pre_name+'motion']=0;			                 					 	
		   }
		 });
	   }   
    });
    $("#"+domid).mousemove(function(event){
      if(window[pre_name+'paperdown']==1&&window[pre_name+'motion']==0)
      {
        window[pre_name+'startmovetime']=new Date().getTime();
        window[pre_name+'moveSX']=this.scrollLeft;
        window[pre_name+'moveSY']=this.scrollTop;
                
        this.scrollLeft-=(event.pageX-window[pre_name+'moveX']);
        this.scrollTop-=(event.pageY-window[pre_name+'moveY']);
        window[pre_name+'moveX']=event.pageX;
        window[pre_name+'moveY']=event.pageY;
      }
    });
}

function disableEnterKey(e)
{
     var key;

     if(window.event)
          key = window.event.keyCode;     //IE
     else
          key = e.which;     //firefox

     if(key == 13)
          return false;
     else
          return true;
}

function comment(input,width,height)
{  
  $("#mycolorbox").html(input).dialog({
    'width':width,
    'height':height
  });
}

function ValidEmail(emailtoCheck)
{
  //  email
  //  規則:  1.只有一個  "@"
  //              2.網址中,  至少要有一個".",  且不能連續出現
  //              3.不能有空白
  var  regExp = /^[^@^\s]+@[^\.@^\s]+(\.[^\.@^\s]+)+$/;
  if(emailtoCheck.match(regExp))
    return true;
  else
    return false;
}
function ValidPhone(phonenum)
{
  //格式為 09xx-xxxxxx
  var tel=/^(09)\d{2}-\d{6}$/;
  if(!tel.test(phonenum))
  {    
    return false;
  } 
  else
  {
    return true;
  }
}
function ValidChineseEnglish(input)
{
  //格式為 中文、英文
  var check=/^[\u4E00-\u9FA5A-Za-z]+$/;
  if(!check.test(input))
  {    
    return false;
  } 
  else
  {
    return true;
  }
}
function ValidEnglishDigital(input)
{
  //格式為 英數字
  var check=/^[a-zA-Z0-9]+$/;
  if(!check.test(input))
  {    
    return false;
  } 
  else
  {
    return true;
  }
}
function ValidEnglishDigitalWordNotSpaceAnd(input)
{
  //格式為 任何英數字符號，但不能為　空白、&
  var check=/^[^ ^&\fa-zA-Z0-9]+$/;
  if(!check.test(input))
  {    
    return false;
  } 
  else
  {
    return true;
  }
}
jQuery.fn.outerHTML = function(s) {
  return (s) ? $(this).replaceWith(s) : $(this).clone().wrap('<p>').parent().html();
} 

//scroll page to id , 如 #id
function Animate2id(dom){
    var animSpeed=800; //animation speed
    var easeType="easeInOutExpo"; //easing type
    if($.browser.webkit){ //webkit browsers do not support animate-html
        $("body").stop().animate({scrollTop: $(dom).offset().top}, animSpeed, easeType,function(){
          $(dom).focus();
        }
        );
    } else {
        $("html").stop().animate({scrollTop: $(dom).offset().top}, animSpeed, easeType,function(){
          $(dom).focus();
        }
        );
    }
}
  function size_hum_read($size){
      /* Returns a human readable size */
  	$size = parseInt($size);
    var $i=0;
    var $iec = new Array();
    var $iec_kind="B,KB,MB,GB,TB,PB,EB,ZB,YB";
    $iec=explode(',',$iec_kind);
    while (($size/1024)>1) {
      $size=$size/1024;
      $i++;
    }
    return sprintf("%s%s",substr($size,0,strpos($size,'.')+4),$iec[$i]);
  }
	$(document).ready(function() {
		//mouse_init();
		
	});
  function plus_or_minus_one_month($year_month,$kind)
  {
    //$year_month 傳入值格式為 2011-01
    //$kind 看是 '+' or '-'
    //回傳格式為 2011-01  
    switch($kind)
    {
      case '+':
          return date('Y-m',strtotime("+1 month",strtotime($year_month)));
        break;
      case '-':
          return date('Y-m',strtotime("-1 month",strtotime($year_month)));
        break;
    }  
  }
  function my_ids_mix(ids)
  {
    var m=new Array();
    m=explode(",",ids);
    var data=new Array();    
    for(i=0,max_i=m.length;i<max_i;i++)
    {
      array_push(data,m[i]+"="+encodeURIComponent($("#"+m[i]).val()));
    }
    return implode('&',data);
  }  
  function my_names_mix(indom)
  {
    var m=new Array();
    var names=$(indom).find('*[req="group[]"]');    
    for(i=0,max=names.length;i<max;i++)
    {
      array_push(m,$(names[i]).attr('name')+"="+encodeURIComponent($(names[i]).val()));
    }
    return implode('&',m);
  }

function anti_right_click()
{
  //鎖右鍵防盜
  document.onselectstart = function(){return false;}
  document.ondragstart = function(){return false;}
  document.oncontextmenu = function(){return false;}
  if (document.all)
      document.body.onselectstart = function() { return false; };
  else {
      $('body').css('-moz-user-select', 'none');
      $('body').css('-webkit-user-select', 'none');
  }
  document.onmousedown = clkARR_;
  document.onkeydown = clkARR_;
  document.onkeyup = clkARRx_;
  window.onmousedown = clkARR_;
  window.onkeydown = clkARR_;
  window.onkeyup=clkARRx_;
  
  var clkARRCtrl = false;
  
  function clkARRx_(e) {
      var k = (e) ? e.which : event.keyCode;
      if (k==17) clkARRCtrl = false;
  }
  
  function clkARR_(e) {
      var k = (e) ? e.which : event.keyCode;
      var m = (e) ? (e.which==3) : (event.button==2);
      if (k==17) clkARRCtrl = true;
      if (m || clkARRCtrl && (k==67 || k==83))
          alert((typeof(clkARRMsg)=='string') ? clkARRMsg : '-版權所有-請勿複製-');
  }   
}
function dialogOn(message,functionAction)
{  
  $.mybox({
    is_background_touch_close:false,
    message : message,
    css : {
      'border' : '2px solid #fff',
      'background-color' : '#ded',
      'color' : '#000',
      'padding':'15px'
    },    
    onBlock : function() {            
      functionAction();
      //$("*[id^='mybox_div']").corner();      
    }           
  });
}
function dialogQueryTOn(message,functionAction)
{  
  $.mybox({
    is_background_touch_close:true,
    message : message,
    css : {
      'border' : '2px solid #fff',
      'background-color' : '#ded',
      'color' : '#000',
      'padding':'15px'
    },    
    onBlock : function() {            
      functionAction();
      $("*[id^='mybox_div']").corner();      
    }           
  });
}
function dialogQueryOn(message,functionAction)
{         
  
  $.mybox({
    is_background_touch_close:false,
    message : message,
    css : {
      // 'border' : '2px solid #fff',
      // 'color' : '#000',
      // 'padding':'15px',
      //'width' : 100,
      //'height' : 100 
      'top' : 160 ,
      'z-index' : 1 ,
      'width' : 880 ,
      'height' : $(window).height() - 200  ,
      'margin-top' : 95 ,
      'margin-left' : '-150px' ,
      'background' : '#181D2D url(images/loading-circle.gif) 50% 50% no-repeat',
      'overflow' : 'hidden' ,
      'white-space' : 'nowrap' ,
      'text-indent' : '900%'
    },    
    beforeBlock : function(){
      $("*[id^='mybox_div']").hide(); 
      $(".data-result .list *[id!='for_loading']").addClass("hide");
      $(".data-result .list").prepend("<div id='for_loading'></div>");
      var offset = $(".data-result").offset();
      $("#for_loading").css({
        'position':'absolute',
        'z-index':1,
        'width' : 850 ,
        'height' : $(window).height()-offset.top ,        
        'margin-left' : '0px' ,
        'background' : '#181D2D url(images/loading-circle.gif) 50% 50% no-repeat',
        'overflow' : 'hidden'
      });       
    },
    onBlock : function() {
      $("*[id^='mybox_div']").hide();         
      functionAction();
      //$("*[id^='mybox_div']").corner(); 
    },
    unBlock : function() {
      $(".data-result .list *[id!='for_loading']").removeClass("hide");
      $("#for_loading").remove();
      
    }
  });
  $("*[id^='mybox_background']").css({
    'top' : 76 ,
    'bottom' : 100 ,
    'background' : 'none',
    'cursor' : 'wait'
  });
}
function dialogOffQuery(){
  $.unmybox();
  $(".data-result .list *[id!='for_loading']").removeClass("hide");
  $("#for_loading").remove(); 
}
function dialogTOn(message,functionAction)
{  
  $.mybox({
    is_background_touch_close: true,
    message : message,
    css : {      
      backgroundColor : '#fff'
    },  
    onBlock : function() {      
      functionAction();      
    }           
  });
}

function dialogOff()
{
  $.unmybox();
}

function basename(filepath)
{
  m=explode("/",filepath);
  mdata = explode("?",end(m));  
  return mdata[0];
}
function mainname(filepath)
{
  filepath = basename(filepath);
  mdata=explode(".",filepath);
  return mdata[0];
}
function subname(filepath)
{
  filepath = basename(filepath);
  m=explode(".",filepath);
  return end(m);
}                
function getext($s){	return strtolower(subname($s));}
function isvideo($file){	if(in_array(getext($file),new Array('mpg','mpeg','avi','rm','rmvb','mov','wmv','mod','asf','m1v','mp2','mpe','mpa','flv','3pg','vob'))){		return true;	}	return false;} 
function isdocument($file){	if(in_array(getext($file),new Array('docx','odt','odp','ods','odc','csv','doc','txt','pdf','ppt','pps','xls'))){		return true;	}	return false;} 
function isimage($file){	if(in_array(getext($file),new Array('jpg','bmp','gif','png','jpeg','tiff','tif','psd'))){		return true;	}	return false;} 
function isspecimage($file){	if(in_array(getext($file),new Array('tiff','tif','psd'))){		return true;	}	return false;}
function isweb($file){	if(in_array(getext($file),new Array('htm','html'))){		return true;	}	return false;} 
function iscode($file){	if(in_array(getext($file),new Array('c','cpp','h','pl','py','php','phps','asp','aspx','css','jsp','sh','shar'))){		return true;	}	return false;}
function dialogColorBoxOn(message,isTouchOutSideClose,func){
  isTouchOutSideClose=(isTouchOutSideClose==='boolean')?false:isTouchOutSideClose;
  if($("#mycolorbox").size()==0)
  {
    $("body").append("<div id='mycolorbox' style='display:none;'></div>");
  }
  $("#mycolorbox").colorbox({
    html:message,
    open:true,
    overlayClose:isTouchOutSideClose,
    onComplete: func 
  });
}

function get_between($data,$s_begin,$s_end)
{
  /*
    $a = "abcdefg";
    echo get_between($a, "cde", "g");
    // get "f"
  */
  $s = $data;
  $start = strpos($s,$s_begin);
  $new_s = substr($s,$start + strlen($s_begin));
  $end = strpos($new_s,$s_end);
  return substr($s,$start + strlen($s_begin), $end);
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
function getRadioBox_val(dom_name)
{
  //return array
  var arr=new Array();
  for(var i=0,max_i=$($("*[name='"+dom_name+"']")).size();i<max_i;i++)
  {
    if($($("*[name='"+dom_name+"']")[i]).prop('checked'))
    {
      array_push(arr,$($("*[name='"+dom_name+"']")[i]).val());
    }
  }
  return arr;
}
function getCheckBox_req(dom_name)
{
  //return array
    var arr = new Array();
    var doms = $("input[name='"+dom_name+"']:checked");
    for (i = 0, max_i = doms.size(); i < max_i; i++) {
        array_push(arr, doms.eq(i).attr('req'));
    }    
    return arr;
}
function getCheckBox_val(dom_name) {
    //return array
    /*var arr = new Array();
    for (var i = 0, max_i = $($("*[name='" + dom_name + "']")).size(); i < max_i; i++) {
        if ($($("*[name='" + dom_name + "']")[i]).prop('checked')) {
            array_push(arr, $($("*[name='" + dom_name + "']")[i]).val());
        }
    }*/

    var arr = new Array();
    var doms = $("input[name='"+dom_name+"']:checked");
    for (i = 0, max_i = doms.size(); i < max_i; i++) {
        array_push(arr, doms.eq(i).val());
    }    
    return arr;
}

Date.prototype.addDays = function(days) {
    var dat = new Date(this.valueOf());
    dat.setDate(dat.getDate() + days);
    return dat;
}
function isLeapYear(year) { 
  return (((year % 4 === 0) && (year % 100 !== 0)) || (year % 400 === 0)); 
}
Date.prototype.getDaysInMonth = function (year, month) {    
    return [31, (isLeapYear(year) ? 29 : 28), 31, 30, 31, 30, 31, 31, 30, 31, 30, 31][month];
};
Date.prototype.addMonths = function (months) {
    var dat = new Date(this.valueOf());
    var n = this.getDate();
    dat.setDate(1);
    dat.setMonth(this.getMonth() + months);
    dat.setDate(Math.min(n, this.getDaysInMonth(dat.getFullYear(),dat.getMonth())));
    return dat;
};
function getMonthsBetween2Dates($sdate,$edate)
{
  //從二個日期取得之間的月份
  //http://stackoverflow.com/questions/18742998/how-to-list-all-months-between-two-dates
  //http://stackoverflow.com/questions/5645058/how-to-add-months-to-a-date-in-javascript
  var dateArray = new Array();
  var currentDate = new Date($sdate);
  while (currentDate <= new Date($edate)) {
      
      var year = currentDate.getFullYear();
      var month = sprintf("%02d",currentDate.getMonth()+1);      
      dateArray.push( sprintf("%s-%s",year,month));
      currentDate = currentDate.addMonths(1);     
  }          
  if(dateArray[dateArray.length-1]!=date('Y-m',strtotime($edate)))
  {
    dateArray.push(date('Y-m',strtotime($edate)));
  }
  return dateArray;
}
function getYearAndMonthObject(YM_ARRAY)
{
  //將Year-Month 拆成 Year Object, 含Month Array
  var output = new Object();
  for(var i=0,max_i=count(YM_ARRAY);i<max_i;i++)
  { 
    var d=explode("-",YM_ARRAY[i]);
    if(output[d[0]]==null)
    {
      output[d[0]]=new Array();
    }
    array_push(output[d[0]],d[1]);    
  }
  return output;
}
function getDaysBetween2Dates($sdate,$edate) {
  //從二個日期取得之間的日
  //http://stackoverflow.com/questions/18742998/how-to-list-all-months-between-two-dates
  var dateArray = new Array();
  var currentDate = new Date($sdate);
  while (currentDate <= new Date($edate)) {
      var year = currentDate.getFullYear();
      var month = sprintf("%02d",currentDate.getMonth()+1);
      var day = sprintf("%02d",currentDate.getDate());
      dateArray.push( sprintf("%s-%s-%s",year,month,day));
      currentDate = currentDate.addDays(1);
  }
  return dateArray;
}
function smallComment(message,is_need_motion,cssOptions)
{
	//畫面的1/15	
	if($("#mysmallComment").size()==0)
	{
		$("body").append("<div id='mysmallComment'><span class='' id='mysmallCommentContent'></span></div>");
		$("#mysmallComment").css({
			'display':'none',
			'position':'fixed',
			'left':'0px',
			'right':'0px',
      'padding':'15px',
			'bottom':'4em',
			'z-index':new Date().getTime(),
			'text-align':'center',
			'opacity':0.8
		});
		$("#mysmallCommentContent").css({									
      'color':'#fff',
			'background-color':'#000'				
		});
    $("#mysmallCommentContent").css(cssOptions);
		/*
		$("#mysmallComment").css({
			'left': (wh['width']-$("#mysmallComment").width())/2+'px' 
		});
		*/

		//$("#mysmallComment").corner();
	}		
	$("#mysmallCommentContent").html(message);
	if(is_need_motion==true)
	{
		$("#mysmallComment").stop();
		$("#mysmallComment").fadeIn("slow");
		setTimeout(function(){
			$("#mysmallComment").fadeOut('fast');
		},2500);
	}
	else
	{
		$("#mysmallComment").show();
		setTimeout(function(){
			$("#mysmallComment").hide();
		},2500);
	}
}

/**
* Returns the zoom level at which the given rectangular region fits in the map view. 
* The zoom level is computed for the currently selected map type. 
* @param {google.maps.Map} map
* @param {google.maps.LatLngBounds} bounds 
* @return {Number} zoom level
**/
function getZoomByBounds( map, bounds ){
  var MAX_ZOOM = map.mapTypes.get( map.getMapTypeId() ).maxZoom || 21 ;
  var MIN_ZOOM = map.mapTypes.get( map.getMapTypeId() ).minZoom || 0 ;

  var ne= map.getProjection().fromLatLngToPoint( bounds.getNorthEast() );
  var sw= map.getProjection().fromLatLngToPoint( bounds.getSouthWest() ); 

  var worldCoordWidth = Math.abs(ne.x-sw.x);
  var worldCoordHeight = Math.abs(ne.y-sw.y);

  //Fit padding in pixels 
  var FIT_PAD = 40;

  for( var zoom = MAX_ZOOM; zoom >= MIN_ZOOM; --zoom ){ 
      if( worldCoordWidth*(1<<zoom)+2*FIT_PAD < $(map.getDiv()).width() && 
          worldCoordHeight*(1<<zoom)+2*FIT_PAD < $(map.getDiv()).height() )
          return zoom;
  }
  return 0;
}

//http://stackoverflow.com/questions/4459379/preview-an-image-before-it-is-uploaded
/*
$("#imgInp").change(function(){
    readURL(this);
});

<form id="form1" runat="server">
    <input type='file' id="imgInp" />
    <img id="blah" src="#" alt="your image" />
</form>

*/
function readURL(outputdom,input_js_dom) {

    if (input_js_dom.files && input_js_dom.files[0]) {
        var reader = new FileReader();

        reader.onload = function (e) {
            outputdom.attr('src', e.target.result);
        }

        reader.readAsDataURL(input_js_dom.files[0]);
    }
}
function secondtodhis($time)
{
  //秒數轉成　天時分秒
  //Create by 羽山 
  // 2010-02-07
  $days=sprintf("%02d",$time/(24*60*60));
  $days=($days>=1)?$days+' 天 ':'';
  $hours=sprintf("%02d",($time % (60 * 60 * 24)) / (60 * 60));
  $hours=($days==''&&$hours=='0')?'':$hours+":";
  $mins=sprintf("%02d",($time % (60 * 60)) / (60));
  $mins=($days==''&&$hours==''&&$mins=='0')?'':$mins+":";
  $seconds=sprintf("%02d",($time%60));
  $output=sprintf("%s%s%s%s",$days,$hours,$mins,$seconds);
  return $output;
}
function print_table($ra,$fields,$headers,$classname)
{    
  $classname=(typeof($classname)=="undefined"||$classname=='')?'':" class='"+$classname+"' ";
  if(typeof($fields)=="undefined"||$fields==''||$fields=='*')
  {      

      $tmp=sprintf("<table %s border='1' cellspacing='0' cellpadding='0'>",$classname);
      $tmp+="<thead><tr>";
      for(var k in $ra[0])
      {
        $tmp+=sprintf("<th>%s</th>",k);
      }
      $tmp+="</tr></thead>";
      $tmp+="<tbody>";
      for($i=0,$max_i=count($ra);$i<$max_i;$i++)
      {
        $tmp+="<tr>";
        for(var k in $ra[$i])
        {
          $tmp+=sprintf("<td field=\"%s\">%s</td>",k,$ra[$i][k]);
        }
        $tmp+="</tr>";
      }
      $tmp+="</tbody>";
      $tmp+="</table>";
      return $tmp;
  }
  else
  {
    $tmp=sprintf("<table %s border='1' cellspacing='0' cellpadding='0'>",$classname);
    $tmp+="<thead><tr>";
    $mheaders=explode(',',$headers);
    for(var k in $mheaders)
    {
      $tmp+=sprintf("<th>%s</th>",$mheaders[k]);
    }
    $tmp+="</tr></thead>";
    $tmp+="<tbody>";
    $m_fields=explode(',',$fields);
    for($i=0,$max_i=count($ra);$i<$max_i;$i++)
    {
      $tmp+="<tr>";
      for(var k in $m_fields)
      {
        $tmp+=sprintf("<td field=\"%s\">%s</td>",k,$ra[$i][$m_fields[k]]);
      }
      $tmp+="</tr>";
    }
    $tmp+="</tbody>";
    $tmp+="</table>";
    return $tmp;
  }
}
function myW(html,func,cssOption){
  if( typeof(window['myW_t'])=="undefined")
  {
     window['myW_t']=0;
  }
  $.fn.center = function () {
      this.css("position","absolute");
      this.css("top", ( $(window).height() - this.height() ) / 2+$(window).scrollTop() + "px");
      this.css("left", ( $(window).width() - this.width() ) / 2+$(window).scrollLeft() + "px");
      return this;
  }  
  var t = time()+"_"+window['myW_t']++;
  var id = "myW_"+t;
  $("body").append("<div id='"+id+"'></div>");  
  $("#"+id).css({
    'z-index':time(),
    'padding':'3px',
    'background-color':'#fff',
    'color':'black',
    'border':'2px solid #00f'
  });
  if(typeof(cssOption)!="undefined" && typeof(cssOption)=="object"){
    for(var k in cssOption)
    {
      $("#"+id).css(k,cssOption[k]);
    }
  }
  html = str_replace("{myW_id}",id,html);  
  $("#"+id).html(html);
  $(window).bind("scroll",{id:id},function(event){
    $("#"+event.data.id).center();
  });  
  $("#"+id).center();
  func(id);
  return id;
}