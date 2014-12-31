<?
header('Content-Type: application/x-mpegurl; charset=utf-8');
?>
#EXTM3U
<?
$json = file_get_contents('arqiva.json');

$json = json_decode($json);

foreach($json as $channel)
{
	$enc = isset($channel->encrypted) ? (bool)$channel->encrypted : false;
	$enc = $enc == true ? ' (Encrypted)' : '';
	echo "#EXTINF:-1,{$channel->name}{$enc}\n{$channel->url}\n";
}
?>