<?
$props = json_decode(file_get_contents("config.js"));
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?><rss xmlns:boxee=\"http://boxee.tv/rss\" xmlns:media=\"http://search.yahoo.com/mrss/\" version=\"2.0\">";
$json = json_decode(file_get_contents("{$props->addr}/Live/Channels/getList?tvOnly=0&avoidHD=0&allowHidden=0&fields=name,id,zap,isDVB,hidden,rank,isHD,logo"));
?>
<channel>
	<title>EE TV Live</title>
	<ttl>5</ttl>
	<language>en</language>
	<? foreach($json as $channel) { ?>		
		<? if ($channel->hidden == 0 && ( $channel->zap < 225 || $channel->zap > 300)) { ?>
		<item>
			<title><?=$channel->zap?> : <?=$channel->name?></title>
			<ttl>5</ttl>
			<link><?=$props->addr?>/Live/Channels/get?channelId=<?=$channel->id?></link>
			<media:thumbnail url="<?=$props->addr?>/Live/Channels/getLogo?zap=<?=$channel->id?>"/>
		</item>
		<? } ?>
	<? } ?>
</channel>
</rss>