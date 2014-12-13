<?
$props = json_decode(file_get_contents("config.js"));
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?><rss xmlns:boxee=\"http://boxee.tv/rss\" xmlns:media=\"http://search.yahoo.com/mrss/\" version=\"2.0\">";
$json = json_decode(file_get_contents("{$props->addr}/PVR/Records/getList?type=regular&avoidHD=0&tvOnly=0"));
?>
<channel>
	<title>EE TV Recordings</title>
	<ttl>5</ttl>
	<language>en</language>
	<? foreach($json as $rec) { 
	$title = $rec->event->name;
	$title .= isset($rec->event->text) ? " - {$rec->event->text}" : "";
	?>
		<item>
			<title><?=$title?> (<?=$rec->event->channelName?>) </title>
			<ttl>5</ttl>
			<link>http://<?=$props->html_root?>/download.php?redirect&addr=<?=rawurlencode("{$props->addr}")?>&id=<?=$rec->id?></link>
			<media:description type="plain"><![CDATA[<?=$rec->event->description?>]]></media:description>
			<duration><?=$rec->event->duration?></duration>
		</item>
		<? } ?>
</channel>
</rss>