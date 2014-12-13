<?
$props = json_decode(file_get_contents("config.js"));
echo "<?xml version=\"1.0\" encoding=\"utf-8\"?><rss xmlns:boxee=\"http://boxee.tv/rss\" xmlns:media=\"http://search.yahoo.com/mrss/\" version=\"2.0\">";
?>
<channel>
		<title>EE TV</title>
		<ttl>5</ttl>
		<link>rss://<?=$props->html_root?>/xbmc</link>
		<description>EE TV</description>
		<language>en</language>
		<item>
			<title>[COLOR=FF00A5FF]Live TV[/COLOR]</title>
			<description><![CDATA[Live Freeview Streams from the STB]]></description>
			<link>rss://<?=$props->html_root?>/live.rss?date=<?=date('YmdHis')?></link>
		</item>
				
		<item>
			<title>[COLOR=FF00A5FF]Arqiva Connect[/COLOR]</title>
			<description><![CDATA[Arqiva Connect Streams]]></description>
			<link>http://<?=$props->html_root?>/assetts/arqiva.m3u</link>
		</item>
		
		<item>
			<title>[COLOR=FF00A5FF]Recordings[/COLOR]</title>
			<description><![CDATA[Live Freeview Streams from the STB]]></description>
			<link>rss://<?=$props->html_root?>/recordings.rss?date=<?=date('YmdHis');?></link>
		</item>

		<item>
			<title>Reload <?=date('H:i:s');?></title>
			<description>Reload <?=date('H:i:s');?></description>
			<link>rss://<?=$props->html_root?>/xbmc.php?date=<?=date('YmdHis');?></link>
		</item>
						
	</channel>
</rss>

