<?
// ===================================================================
// Sim Roulette -> AJAX
// License: GPL v3 (http://www.gnu.org/licenses/gpl.html)
// Copyright (c) 2016-2021 Xzero Systems, http://sim-roulette.com
// Author: Nikita Zabelin
// ===================================================================

include("_func.php");
$url=explode('/',$_SERVER['HTTP_REFERER']);
$url=$url[count($url)-1];
$txt=trim(file_get_contents('help.txt'));
while (1)
{
	$a=$txt;
	$txt=str_replace('====','===',$txt);
	if ($a==$txt){break;}
}
while (1)
{
	$a=$txt;
	$txt=str_replace('----','---',$txt);
	if ($a==$txt){break;}
}
$a=explode("===",$txt);
$out='';
for ($i=count($a)-1;$i>0;$i--)
{
	if ($a[$i])
	{
		$b=explode("---",$a[$i]);
		$c=explode("
",trim($b[0]));
		if (strpos($url,$c[0])!==false)
		{
			$out.='<h1>'.$c[1].'</h1>';
			$out.=str_replace("
",'<br>',$b[1]);
		}
	}
}
if (!$out){$out='Помощь по разделу готовится...';}
echo $out;
?>