<?
set_time_limit(4*60*60); // four hours
$session = json_decode(file_get_contents("{$_GET['addr']}/PVR/Records/session?recordId={$_GET['id']}"));
$name = $session->event->name . " - " . $session->event->episodeInfo ." - " . $session->event->channelName;
$name = preg_replace('/[^a-zA-Z0-9]/', '_', str_replace(" ","_",$name)) . ".ts";
$url = "{$_GET['addr']}/PVR/Records/getVideo?sessionId={$session->id}";
header('Content-Description: File Transfer');
if (isset($_GET['view']))
{
	header("Location: {$url}");
	/*
	Original View Method - the redirect method seems to work for BS Player and XBMC though
	header("Content-Type: video/mpeg");
	header('Expires: 0');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	readfile($url);
	*/
}
elseif(isset($_GET['redirect']))
{
	header("Location: {$url}");
}
else
{
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename='.$name);
	header('Expires: 0');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	readfile($url);
}
