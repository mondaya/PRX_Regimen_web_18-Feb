<?php
	require_once("config-nct.php");
	$captchanumber = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890abcdefghijklmnopqrstuvwxyz'; // Initializing PHP variable with string
	$captchanumber = substr(str_shuffle($captchanumber), 0, 6); // Getting first 6 word after shuffle.
	$_SESSION["code"] = strtolower($captchanumber); // Initializing session variable with above generated sub-string
	$image = imagecreatefrompng(DIR_IMG."captcha_bg.png");
	$foreground = imagecolorallocate($image, 20, 20, 20); // Font Color
	imagettftext($image, 25, 0, 10, 38, $foreground, DIR_FONT.'larabiefont.ttf', $captchanumber);
	header('Content-type: image/png');
	imagepng($image);
?>