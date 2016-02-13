<?
include('class.upload.php');
include('RemoteFiles.class.php');

$width = 558;
$height = 558;

if (isset($_GET['medium']))
{
	$width = 150;
	$height = 150;
}
elseif (isset($_GET['wide']))
{
	$width = 310;
	$height = 150;
}
elseif (isset($_GET['large']))
{
	$width = 310;
	$height = 310;
}

$config = json_decode(file_get_contents('config.js'));


$url = urlencode($config->addr . "/EPG/Timers/getConfig");

$reply = json_decode( file_get_contents("http://{$config->html_root}/proxy.php?url=$url"));

foreach($reply as $r)
{
	$e = $r->param->event;
	//print_R($e);
	$suffix = str_pad($e->channelZap,4,"0",STR_PAD_LEFT);
	
	if(isset($e->icon))
	{

		$timers["{$e->startTime}.{$suffix}"] = array('text'=>$e->name,'icon'=>$e->icon);
		//echo "$suffix - {$e->text} \t {$e->startTime} \t {$e->icon} \n";
	}
}
ksort($timers);
$idx = isset($_GET['idx']) ? $_GET['idx'] : 1;
$i = 0;

/*?><pre><?=print_r($timers)?></pre><?*/
foreach($timers as $timer)
{
	$timer = (object) $timer;
	
	if (++$i == $idx)
	{
	
		$image = $timer->icon;
		$text = $timer->text;
	}
}

$temp = tempnam("/tmp/","thumb_").".jpg";
RemoteFiles::toFile($image,$temp);

$handle = new upload($temp);

$handle->image_convert = 'png';
$handle->image_default_color = '#009C9C';
$handle->image_background_color = '#009C9C';
$handle->image_text_padding = 5;


$handle->image_resize = true;
$handle->image_x = $width;
$handle->image_y = $height;
$handle->image_ratio_fill = true;
$handle->image_text = $text;
$handle->image_text_font = 3;
$handle->image_text_position = 'BL';

header('Content-type: image/png');
echo $handle->Process();
unlink($temp);
?>