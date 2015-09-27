<?
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en-GB">
<head>
	<meta charset="utf-8">
	<title>EE TV Web Control</title>
	<style type="text/css">
		body 
		{
			font-family: "Roboto","Trebuchet MS", "Helvetica", "Arial",  "Verdana", "sans-serif";
			font-size: 67.5%;
		}
		
		#channel_contents div.ch div.chname
		{
			background-color:white;
			margin:0px;
			padding:3px;
			position:absolute;
			bottom:0px;
			height:25px;
			left:0px;
			right:0px;
			font-family:Roboto;
			font-size:8pt;
		}
		#channel_contents div.ch div.logo
		{
			text-align:center;
			left:0px;
			position:absolute;
			top:0px;
			height:75px;
			right:0px;
		}
		#channel_contents div.ch
		{
			float:left;
			overflow:hidden;
			position:relative;
			width:100px;
			height:77px;
			border:1px solid black;
			background-color:#ddd;
			margin:3px;
			padding:0;
			cursor:pointer;
		}
		.image_type_epg
		{
			text-align:center;
			background-color:#218AB9;
		}
		
		.image_type_category
		{
			background-color:#6FB8D6;
			text-align:center;
		}
		#search_contents td.desc
		{
			font-size:8pt;
		}
		#search_contents td.desc img
		{
			height:15px;
		}
	</style>
	<link href='http://fonts.googleapis.com/css?family=Roboto:400,700,400italic,700italic&subset=latin,latin-ext,cyrillic,cyrillic-ext,greek-ext,greek,vietnamese' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/start/jquery-ui.css" />
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
	<script src="assetts/Date.class.js"></script>
	<script src="assetts/jquery.number.js"></script>
	<script src="assetts/jquery.ui.alert.js"></script>
	<script type="text/javascript">
		
		$(document).ready(function()
		{
			
			$( "#accordion" ).accordion(
			{
				beforeActivate: function(event, ui)
					{
						showSection(ui.newHeader.attr('id'));
					},
				heightStyle: "content"
			});
		}); //$(document).ready(function(){


</script>
</head>
<body>
<?
?>

<div id="accordion">
    
    <h3 id="playlists">Playlist</h3>
    <div id="playlist_contents">
<ul><?
$config = file_get_contents('config.js');
$config = json_decode($config);
if (substr($_GET['file'],0,7)!=='http://')
{
$f = file('http://'.$config->html_root."/".$_GET['file']);
}
else
{
$f = file($_GET['file']);
}
$label = false;
foreach($f as $line)
{
	if (stripos($line, "#EXTINF:-1,")!==false)
	{
		$label = trim(str_replace("#EXTINF:-1,","",$line));
	}
	elseif($label != false)
	{
		echo "<li><a href='{$line}'>{$label}</a></li>";
	}
}
?>
</ul>
    </div>
</div>


</body>
</html>


