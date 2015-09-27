<?
$props = json_decode(file_get_contents("config.js"));
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?><rss xmlns:boxee=\"http://boxee.tv/rss\" xmlns:media=\"http://search.yahoo.com/mrss/\" version=\"2.0\">";
$json = json_decode(file_get_contents("{$props->addr}/PVR/Records/getList?type=regular&avoidHD=0&tvOnly=0"));


foreach ($json as $key => $row) {
    $mid[$key]  = $row->name;
}
array_multisort($mid, SORT_ASC, $json);


//print_R($json);
//print_r($json);


?>
<channel>
	<title>EE TV Recordings</title>
	<ttl>5</ttl>
	<language>en</language>
	<? foreach($json as $rec) { 
	$title = $rec->event->name;
	$title .= isset($rec->event->text) ? " - {$rec->event->text}" : "";
	$start = date('d-M-Y',$rec->event->originalEvent->startTime);
	?>
		<item>
			<title><?=$title?> ([COLOR=FF00FF00]<?=$rec->event->channelName?>[/COLOR] [COLOR=FF00A5FF]<?=$start?>[/COLOR]) </title>
			<ttl>5</ttl>
			<link>http://<?=$props->html_root?>/download.php?redirect&addr=<?=rawurlencode("{$props->addr}")?>&id=<?=$rec->id?></link>
			<media:description type="plain"><![CDATA[<?=$rec->event->description?>]]></media:description>
			
			<media:thumbnail url="<?=$rec->event->icon?>" width="75" height="50" time="" />		
			
			<duration><?=$rec->event->duration?></duration>
		</item>
		<? } ?>
</channel>
</rss>