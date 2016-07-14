<?php
/*======================================================================*\
|| #################################################################### ||
|| #                  深藍UTF-8正體簡體轉換函數 1.0                   # ||
|| # 台灣深藍vBulletin技術論壇 http://www.twvbb.com 站長:ckmarkhsu    # ||
|| # ---------------------------------------------------------------- # ||
|| # Copyright 2006 台灣深藍vBulletin技術論壇 All Rights Reserved.    # ||
|| # 歡迎轉載，唯轉載請保留版權宣告，並請勿自行修改發布               # ||
|| # 2006/03/13                                                       # ||
|| #################################################################### ||
\*======================================================================*/

function dbcconv($text , $encode=1){
	if($encode == 0)
	{
		require("dbcconv_cht.php");
	}
	elseif($encode ==1)
	{
		require("dbcconv_chs.php");
	}

	for($i=0; $i < strlen($text);$i++ ){
		$str = substr($text,$i,3);
		if(dbcconv_isChinese($str))
		{
			$tmp .= $data[dbcconv_id($str)];
			$i=$i+2;
		}
		else
		{
			$tmp .= substr($text,$i,1);
		}
	}
	return $tmp;
}

function dbcconv_id($str)
{
    $tmp = ((ord($str[0]) - 228) * 4096) + ((ord($str[1]) - 184) * 64) + (ord($str[2]) - 128);
    return $tmp;
}

function dbcconv_isChinese($str)
{
	$id = dbcconv_id($str);
	if($id <= 20901 && $id >= 0)
	{
		return true;
	}
	else
	{
		return false;
	}
}



