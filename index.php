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
		var ee_url = {
			'recordings':'/PVR/Records/getList?type=regular&avoidHD=0&tvOnly=0',
			'channels':'/Live/Channels/getList?tvOnly=0&avoidHD=0&allowHidden=0&fields=name,id,zap,isDVB,hidden,rank,isHD,logo',
			'timers':'/EPG/Timers/getConfig',
			'info':'/UPnP/Device/getInfo',
			'zap':'/Live/Channels/zap?zap=',
			'play_recording':'/PVR/Records/play?recordId=',
			'get_download_session_id' : '/PVR/Records/session?recordId=',
			'download_file_by_session_id' :"/PVR/Records/getVideo?sessionId=",
			'find_programme' : "/EPG/Programs/find?fieldName=name&fieldValue=", // &zap=
			'play_video' : '/Live/External/play?position=0&url=',
			'channel_logo' : '/Live/Channels/getLogo?zap=',
			
			};
		var ee_tv_box = false;

		function eeUpdateStatus()
		{
			getEE(ee_url.info,function(data)
			{
				var ee_clock = new Date(data.system.time)
				$( "#time" ).html(ee_clock.format('dd-mmm-yyyy HH:MM:ss'));
				$( "#disk" ).progressbar({value:data.pvr.status.disk.percent});
				$( "#tuners" ).html(data.system.tuners + " tuners");
				var used = data.pvr.status.disk.usedSpace / 1024 / 1024
				var total = data.pvr.status.disk.totalSpace / 1024 / 1024
				$( "#usage" ).html("<i>" + $.number(used,2) + " of " + $.number(total,2) + " GiB used</i>");
			});
			setTimeout(eeUpdateStatus,10*1000); // Update Every 10 Seconds...
		}
		function eeFindProgramme(name,chnumber)
		{
			name = $(name).val();
			chnumber = $(chnumber).val();
			getEE(
				ee_url.find_programme + encodeURIComponent(name) + "&zap=" + chnumber,
				function(data)
				{
					var html = "<table>";
					html += "<colgroup><col style='width:100px;' /><col style='width:100px;' /><col style='width:100px;' /><col style='width:200px;' /></colgroup>";
					html += "<tr class='ui-tabs-nav ui-helper-reset ui-widget-header ui-corner-all'>";
					html += "<td>ID<br>Programme ID<br>Series ID</td>"
					html += "<td></td>";
					html += "<td>Show and Series</td>"
					html += "<td>Channel and Times</td>"
					html += "<td>Synopsis</td>"
					html += "</tr>";
					// Group By Show then Series
					var series = Array();
					/*
					for(i=0;i<data.length;i++)
					{
						var ent = data[i];
						var showID = ent.serieId.replace("/","s");
						var programmeID = ent.programCRID.replace("/","p");
						var pos = 0;
						if (typeof(series[showID])=='undefined')
						{
							series[showID] = Array();
							series[showID][programmeID] = Array();
						}
						else if(typeof(series[showID][programmeID])=='undefined')
						{
							series[showID][programmeID] = Array();
						}
						else
						{
							pos = series[showID][programmeID].length;
						}
						series[showID][programmeID][pos] = data[i];
					}
					console.log(series);
					
					$.each(series, function( showID, show )
					{
						console.log("FE Series");
					});*/
			

					for(i=0;i<data.length;i++)
					{
						var ent = data[i];
						
						var start = new Date(ent.startTime*1000);
						var end = new Date(ent.endTime*1000);
						var dur = ent.duration / 60;
						var epNum = ent.episodeNum; // XML TV format - i.e. series.episode X or Y (where numbers start at 0)
						var images = "";
						var episodeInfo = typeof(ent.episodeInfo) == 'undefined' ? '' : ent.episodeInfo;
						images += ent.subtitle == true ? '<img src="assetts/subtitles.png">' : '';
						images += ent.audioDescription == true ? '<img src="assetts/ad.png">' : '';
						html += "<tr>";
						html += "<td>" + ent.id + "<br>" +ent.programCRID   +"<br>" +ent.serieId+ "</td>"
						html += "<td class='image_type_"+ent.imageType+"'><img style='height:75px;' src='" + ee_tv_box.addr + ent.image + "'></td>"
						html += "<td>" + ent.name + "<br>" + episodeInfo + "<br><i>"+ent.category+"</i>" + "</td>"
						html += "<td><img height='25' src='"+ee_url.channel_logo+ent.channelZap +"'><br>" + start.format('dd-mmm-yyyy HH:MM') + " to " + end.format('HH:MM') + 
							" ("+ dur +" minutes)</td>"
						html += "<td class='desc'>" + ent.description + '<br>' + images + "</td>"
						
						//html += "<td><b>" + ent.programName + "</b><br>" + ent.programDescription + "</td>"
						//html += "<td><b>" + ent.seasonName + "</b><br>" + ent.seasonDescription + "</td>"
						html += "</tr>";
						
						//console.log(ent);
						if (typeof(ent.episodeInfo!='undefined'))
						{
							//html += ent.episodeInfo + "<br>";
						}
						
					}
					html += "</table>";
					$('#search_results').html(html);
					
					});
			
		}
		
		function eePlayVideo(url_field)
		{
			var url = $(url_field).val();
			getEE(ee_url.play_video + encodeURIComponent(url),false);
			
		}
		function eeSwitchChannel(number)
		{
			getEE(ee_url.zap+number,false);
		}
		function init(reply)
		{
			ee_tv_box = reply;
			ee_url.channel_logo = ee_tv_box.addr + ee_url.channel_logo;
			eeUpdateStatus();
		}
		
		function showSection(section_id)
		{
			switch(section_id)
			{
				case 'recordings':
					getEE(ee_url.recordings,function(data)
					{
						var html = "<table>";
						for(i = 0;i<data.length;i++)
						{
							var rec = data[i];
							html += "<tr>";
							if (typeof(rec.event.icon)  == 'undefined')
							{
							html += "<td>" + "X" + "</td>";
							}
							else
							{
							html += "<td>" + "<img height='25' src='"+rec.event.icon +"'>" + "</td>";
							}
							html += "<td>" + rec.event.name + "</td>";
							html += "<td>" + rec.event.channelName + "</td>";
							html += "<td>" + rec.event.text + "<br>" + rec.event.episodeInfo + "</td>";
							html += "<td onclick='eePlayRecording(\""+rec.id+"\")'>Play</td>";
							html += "<td onclick='eeDownloadRecording(\""+rec.id+"\")'>Download</td>";
							html += "<td>" + $.number(rec.duration / 60) + " mins</td>";
							html += "</tr>";
						}
						html += "</table>";
						$('#recording_contents').html(html);
					});
					break;				case 'channels':
					getEE(ee_url.channels,function(data)
					{
						var html = "";
						for(i = 0;i<data.length;i++)
						{
							var ch = data[i];
							if(ch.hidden==false)
							{
								html += "<div class='ch' onclick='eeSwitchChannel(\""+ch.zap+"\")'>";
								if (typeof(ch.logo)  == 'undefined')
								{
									html += "<div class='logo'><img width='100' src='"+ee_url.channel_logo+ch.zap +"'></div>";
								
								
								}
								else
								{
								html += "<div class='logo'><img width='100' src='"+ch.logo +"'></div>";
								}
								html += "<div class='chname'>" + ch.zap + ". " + ch.name + "</div></div>";
							}

						}
						$('#channel_contents').html(html);
					});
					break;
				case 'search':
					getEE(ee_url.channels,function(data)
					{
						var html = "";
						$('#search_zap').find('option').remove().end();
						for(i = 0;i<data.length;i++)
						{
							var ch = data[i];
							if(ch.hidden==false)
							{
								$('#search_zap').append($('<option>').text(ch.name).attr('value', ch.zap));
							}

						}
						$('#channel_results').html("");
					});
					break;
			}
		}
		$(document).ready(function()
		{
			getContent('config.js',init);
			$( "#accordion" ).accordion(
			{
				beforeActivate: function(event, ui)
					{
						showSection(ui.newHeader.attr('id'));
					},
				heightStyle: "content"
			});
		}); //$(document).ready(function(){


		function getContent(url,callback)
		{
			$.ajax({
				url: url,
				type: 'GET',
				dataType: 'json',
				beforeSend: function() {
					//$('#'+id+' .contentarea').html('<img src="/function-demos/functions/ajax/images/loading.gif" />');
				},
				success: function(data, textStatus, xhr) 
				{
					callback(data);
				},
				error: function(xhr, textStatus, errorThrown) {
					//$('#'+id+' .contentarea').html(textStatus);
					console.log(textStatus);
				}
			});
		}

		function getEE(url,callback)
		{
				
			$.ajax({
				url: 'proxy.php?url=' + encodeURIComponent(ee_tv_box.addr) + encodeURIComponent(url),
				type: 'GET',
				dataType: 'json',
				beforeSend: function() {
					//$('#'+id+' .contentarea').html('<img src="/function-demos/functions/ajax/images/loading.gif" />');
				},
				success: function(data, textStatus, xhr) 
				{
					callback(data);
				},
				error: function(xhr, textStatus, errorThrown) {
					//$('#'+id+' .contentarea').html(textStatus);
					console.log(textStatus);
				}
			});
		}

		function showStats(reply)
		{
			ee_properties.INFO
		}
		function press(button)
		{
			$.ajax( "remote.php?button="+button )
		}

		function eePlayRecording(recording)
		{
			url = ee_url.play_recording + recording;
			getEE(url,false);
		}
		
		function eeDownloadRecording(recording)
		{
			var url = ee_url.get_download_session_id + recording;
			getEE(url,function(data)
				{ 
					var link = ee_tv_box.addr + ee_url.download_file_by_session_id  + data.id;
					$.alert('To Download Video <a href="' +link + '">Right Click and Save Link As</a>"','Video Download');
				});
		}
</script>
</head>
<body>
<?
?>
<div id="info">
	<div id="time"></div>
	<b>Disk Usage :</b><br>
	<div id="disk"></div>
	<div id="usage"></div>
	<div id="tuners"></div>
</div>

<div id="accordion">
    <h3 id="remote">Remote</h3>
    <div id="remote_contents"><p>
      <a href="remote_control">Remote</a><br>
    </p></div>
    <h3 id="channels">Channels</h3>
    <div id="channel_contents">
      <a href="channels">Channels</a><br>
    </div>
    <h3 id="timer">Timer</h3>
    <div id="timer_contents"><p>
		<a href="timers">Timer</a><br>
    </p></div>
    <h3 id="recordings">Recordings</h3>
    <div style="height:250px;" id="recording_contents"><p>
      <a href="recordings">Recordings</a><br>
    </p></div>
	<h3 id="search">Search</h3>
    <div id="search_contents"><p>
		<input type="text" id="search_desc" value="Top Gear">
		<select id="search_zap"></select>
		<input type="button" onclick="eeFindProgramme('#search_desc','#search_zap')" value="Search">
		<div id="search_results"></div>
    </p></div>
	<h3 id="video">Play Video (Experimental)</h3>
    <div id="video_contents"><p>
		<input type="text" id="play_video" value="">
		<input type="button" onclick="eePlayVideo('#play_video')" value="Play!">
      
    </p></div>
    <h3 id="playlists">Playlists</h3>
    <div id="playlist_contents">
		<ul>
			<li><a href="<?=$ip_address?>/Live/Channels/getPlaylist">M3U Playlist</a></li>
			<li><a href="/assetts/arqiva.m3u">Arqiva Playlist</a></li>
		</ul>
    </div>
</div>

</body>
</html>