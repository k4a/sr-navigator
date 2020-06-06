<?
// ===================================================================
// Sim Roulette -> Connection with SIM Roulette
// License: GPL v3 (http://www.gnu.org/licenses/gpl.html)
// Copyright (c) 2016-2020 Xzero Systems, http://sim-roulette.com
// Author: Nikita Zabelin
// ===================================================================

$root="[path]";
include($root.'_func.php');

if (!$_GET['token'] || strlen($_GET['token'])!=10){exit();} // Token verification | Проверка токена

// Getting the device ID | Получение ID устройства
if ($result = mysqli_query($db, "SELECT * FROM `devices` WHERE `token_remote`='".(int)$_GET['token']."'")) 
{
	if ($row = mysqli_fetch_assoc($result))
	{
		$id=$row['id'];
	}
	else
	{
		setlog('device:'.$id.' Device not identified!','link');  // Устройство не опознано
		exit(); 
	}
}

// Receiving a command that should be send to the device | Получение команды, которую надо отправить на устройство
if ($result = mysqli_query($db, 'SELECT *,unix_timestamp(time) AS time FROM `link_outgoing` WHERE `device`='.(int)$id." ORDER BY `id` LIMIT 1")) 
{
	if ($row = mysqli_fetch_assoc($result))
	{
		mysqli_query($db,"DELETE FROM `link_outgoing` WHERE `id`=".$row['id']);
		$out=$row['step'].'#!#'.$row['command'];
	}
	else
	{
		$out='0#!#REQUEST';
	}
	$time=file_get_contents('time.dat');
	echo '{data}'.$out;
}

// Saving the response received from the device | Сохранение полученного от устройства ответа
if ($_GET['data']!='REQUEST')
{
	if (!$_GET['step']){$uniq=",`uniq`='".rand(1111,9999).rand(1111,9999)."'";} else {$uniq="";} // If there is an out-of-order response from the device, we generate a random number to save as a unique response in the table | Если внеочередной ответ устройства - генерируем случайное число, чтобы сохранить в таблице как уникальный ответ
	$qry="INSERT `link_incoming` SET
	`device`='".$id."',
	`step`=".(int)$_GET['step'].",
	`answer`='".$_GET['data']."'".$uniq;
	mysqli_query($db,$qry);
	file_put_contents('time.dat',time());
}

setlog('device:'.$id.' IN > '.$_GET['step'].' | '.stripslashes($_GET['data']).' OUT > '.$step.' | '.$out,'link');
?>