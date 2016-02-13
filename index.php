<!DOCTYPE html>
<html lang="en-GB"  prefix="og: http://ogp.me/ns#">
<head>
	<meta charset="utf-8">
	<title>EE TV Web Control</title>
	<meta name="application-name" content="EE TV" />
	<meta name="msapplication-starturl" content="http://<?=$_SERVER['SERVER_NAME']?>:<?=$_SERVER['SERVER_PORT']?><?=$_SERVER['REQUEST_URI']?>" />
	<meta name="msapplication-navbutton-color" content="#009C9C" />
	<meta name="msapplication-window" content="width=1024;height=768" />


	<meta name="theme-color" content="#009C9C">
	<meta property="og:title" content="EE TV Web Control" />
	<meta property="og:description" content="Control and view programmes from the EE TV Box" />
	<meta property="og:url" content="http://<?=$_SERVER['SERVER_NAME']?>:<?=$_SERVER['SERVER_PORT']?><?=$_SERVER['REQUEST_URI']?>" />
	<meta property="og:image" content="http://<?=$_SERVER['SERVER_NAME']?>:<?=$_SERVER['SERVER_PORT']?><?=$_SERVER['REQUEST_URI']?>ee-icon-4x.png" />
	<link rel="manifest" href="manifest.json">
	<meta name="msapplication-config" content="live-tiles.xml.php?v=<?=date('YmdHis')?>" />
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
			top:0px;
			height:27px;
			left:0px;
			right:0px;
			font-family:Roboto;
			font-size:7pt;
		}
		
		#channel_contents div.ch div.chcontrols
		{
			background-color:white;
			margin:0px;
			padding:3px;
			position:absolute;
			bottom:0px;
			height:16px;
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
			top:27px;
			height:75px;
			right:0px;
		}
		#channel_contents div.ch
		{
			float:left;
			overflow:hidden;
			position:relative;
			width:100px;
			height:97px;
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
			'cancel_timer' : '/EPG/Timers/delete?timerId='			
			
			};
		var ee_tv_box = false;
		var arqiva_channels = new Array;
		var arqiva_connect_channels = new Array;
		function compare(a,b) 
		{
			if (a.event.name < b.event.name)
				return -1;
			if (a.event.name > b.event.name)
				return 1;
			return 0;
		}

		function timersort(a,b) 
		{
			if (a.param.name < b.param.name)
				return -1;
			if (a.param.name > b.param.name)
				return 1;
			return 0;
		}


		function cancelTimer(timerId)
		{
			getEE(
				ee_url.cancel_timer + timerId,
				function(data)
				{
					showSection('timers');
				},'text');
		}
		function setTimer(zap,startTime,series)
		{
			series = series == true ? 1 : 0;
			url = '/EPG/Timers/recordEvent?zap='+zap+'&time='+startTime+'&isSeries='+series;
			getEE(url,function(data)
			{
				showSection('timers');
			},
			'text'
			);
			
		}
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
					html += "<td>Record</td>"
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
						html += "<td>";
						html += "<a href='javascript:setTimer("+ent.channelZap+","+ent.startTime+",false);'><img src='assetts/rec.png' title='Record Once'></a>";
						if (typeof(ent.serieId) != 'undefined')
						{
							html += " <a href='javascript:setTimer("+ent.channelZap+","+ent.startTime+",true);'><img src='assetts/recs.png' title='Record Series'></a>";
						}
						html += "</td>";
						//html += "<td><b>" + ent.programName + "</b><br>" + ent.programDescription + "</td>"
						//html += "<td><b>" + ent.seasonName + "</b><br>" + ent.seasonDescription + "</td>"
						html += "</tr>";
						
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
			$('#m3u').attr('href',ee_tv_box.addr + "/Live/Channels/getPlaylist");
			$('#am3u').attr('href',"m3u.php?file=" + encodeURIComponent(ee_tv_box.addr + "/Live/Channels/getPlaylist"));
			
			
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
						html += "<colgroup><col style='width:77px;' /><col style='width:150px;' /><col style='width:120px;' /><col style='width:300px;' /></colgroup>";
						html += "<tr class='ui-tabs-nav ui-helper-reset ui-widget-header ui-corner-all'>";
						html += "<td>&nbsp;</td>"
						html += "<td>Title</td>";
						html += "<td>Channel</td>"
						html += "<td>Episode</td>"
						html += "<td>EE</td>"
						html += "<td>D/L</td>"
						html += "<td>VLC</td>"
						html += "<td>Duration</td>"
						html += "</tr>";
						data.sort(compare);
						for(i = 0;i<data.length;i++)
						{
							var rec = data[i];
							html += "<tr>";
							if (typeof(rec.event.icon)  == 'undefined')
							{
								html += "<td>" + "<img height='25' src='assetts/no.png'>" + "</td>";
							}
							else
							{
								html += "<td>" + "<img height='25' src='"+rec.event.icon +"'>" + "</td>";
							}
							
							epInfo = typeof(rec.event.episodeInfo)  == 'undefined' ? '' : rec.event.episodeInfo;
							
							html += "<td>" + rec.event.name + "</td>";
							html += "<td>" + rec.event.channelName + "</td>";
							html += "<td>" + rec.event.text + "<br>" + epInfo + "</td>";
							html += "<td><a href='javascript:eePlayRecording(\""+rec.id+"\")'><img src='assetts/play_tv.png' title='Play on EE TV'></a></td>";
							//html += "<td onclick='eeDownloadRecording(\""+rec.id+"\")'>Download</td>";
							html += "<td><a href='download.php?addr="+encodeURIComponent(ee_tv_box.addr) +"&id=" + rec.id + "'><img src='assetts/download.png' title='Download'></a></td>";
							html += "<td><a target='eetv' href='download.php?view&addr="+encodeURIComponent(ee_tv_box.addr) +"&id=" + rec.id + "'><img src='assetts/play.png' title='Play in New Window'></a></td>";
							html += "<td>" + $.number(rec.duration / 60) + " mins</td>";
							html += "</tr>";
						}
						html += "</table>";
						$('#recording_contents').html(html);
					});
					break;
				case 'timers':
					
					getEE(ee_url.timers,function(data)
					{
						var html = "<table>";
						$.each(data, function( id, timer )
						{
							tmr = timer.param
							$.each(tmr.instance, function( iid, inst )
							{
	//console.log(inst);
	
								var start = new Date(inst.startTime);
								var end = new Date(inst.endTime);
								
								
								html += "<tr>";
								if (typeof(tmr.event.icon)  == 'undefined')
								{
									html += "<td>" + "<img height='25' src='assetts/no.png'>" + "</td>";
								}
								else
								{
									html += "<td>" + "<img height='25' src='"+tmr.event.icon +"'>" + "</td>";
								}
								
								var series = timer.param.repeat == 'series' ? true : false;
								
								html += "<td>" + tmr.event.name + "</td>";
								html += "<td>" + tmr.event.channelName + "</td>";
								html += "<td>" + tmr.event.text + "<br>" + tmr.event.episodeInfo + "</td>";
								html += "<td>" + start.format('dd-mmm-yyyy HH:MM') + "</td>";
								html += "<td>" + end.format('HH:MM')+ "</td>";
								duration = (inst.endTime - inst.startTime) / 1000 ; 
								html += "<td>" + $.number(duration / 60) + " mins</td>";
								html += "<td><a href='javascript:cancelTimer(" + id + ");'>Cancel"+ ( series == true ? ' Series Link ' : ' Timer'  )+"</a></td>";
								html += "</tr>";
							});
						});
						html += "</table>";
						$('#timer_contents').html(html);
						
						

					});
					break;
				case 'channels':
					getEE(ee_url.channels,function(data)
					{
						var html = "";
						var added_arqiva = false;
						for(i = 0;i<data.length;i++)
						{
							var ch = data[i];
							if (ch.zap >= 300 && added_arqiva == false)
							{
								added_arqiva = true;
								for(i = 0;i<arqiva_connect_channels.length;i++)
								{
									var ch = arqiva_connect_channels[i];
									html += "<div class='ch' >";
									html += "<div class='chname'>"
									html += ch.name ;
									
									html += "</div>"
									html += "<div class='logo'><img style='max-width:100px;max-height:45px;' src='assetts/arqiva_connect/"+ch.logo +"'></div>";

									html += "<div class='chcontrols'>"
									html += "<a style='float:right' target='eetv' href='vlc.php?url="+encodeURIComponent(ch.url)+ "'><img src='assetts/play.png' title='Play in Browser'></a>";
									
									html += "</div>"
									
									html += "</div>";
								}
							}
							
							if(ch.hidden==false)
							{
								html += "<div class='ch' >";
								html += "<div class='chname'>"
								html += ch.zap + ". " + ch.name ;
								arqiva_connect = (ch.zap >= 225 && ch.zap < 300) ? true : false;
								
								html += "</div>"
								if (typeof(ch.logo)  == 'undefined')
								{
									html += "<div class='logo'><img width='100' src='"+ee_url.channel_logo+ch.zap +"'></div>";
								
								
								}
								else
								{
									html += "<div class='logo'><img width='100' src='"+ch.logo +"'></div>";
								}
								html += "<div class='chcontrols'>"
								html += "<a style='float:left' href='javascript:eeSwitchChannel(\""+ch.zap+"\")'><img src='assetts/play_tv.png' title='Play on EE TV'></a>";
								if ( arqiva_connect == false)
								{
									html += "<a style='float:right' target='eetv' href='"+ee_tv_box.addr+"/Live/Channels/get?zap=" + ch.zap + "'><img src='assetts/play.png' title='Play in Browser'></a>";
								}
								else if(typeof(arqiva_channels[ch.zap]) != 'undefined')
								{
									html += "<a style='float:right' target='eetv' href='vlc.php?url="+encodeURIComponent(arqiva_channels[ch.zap])+ "'><img src='assetts/play.png' title='Play in Browser'></a>";
								}
								
								html += "</div>"
								
								html += "</div>";
								
								
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
			getContent('assetts/arqiva.json',arqiva);
			$( "#accordion" ).accordion(
			{
				beforeActivate: function(event, ui)
					{
						showSection(ui.newHeader.attr('id'));
					},
				heightStyle: "content"
			});
		}); //$(document).ready(function(){

		function arqiva(data)
		{
			var idx = 0;
			for(i = 0;i<data.length;i++)
			{
				
				if (data[i].zap != false)
				{
					//console.log(data[i]);
					arqiva_channels[data[i].zap] = data[i].url;
				}
				else
				{
					if (data[i].encrypted != true)
					{
						arqiva_connect_channels[idx++] = data[i];
					}
				}
			}
		}

		function getContent(url,callback)
		{
			//alert("Retirned");
			$.ajax({
				url: url,
				type: 'GET',
				dataType: 'json',
				beforeSend: function() {
					//$('#'+id+' .contentarea').html('<img src="/function-demos/functions/ajax/images/loading.gif" />');
				},
				success: function(data, textStatus, xhr) 
				{
				//	alert("Success");
					callback(data);
				},
				error: function(xhr, textStatus, errorThrown) {
					//$('#'+id+' .contentarea').html(textStatus);
					console.log(textStatus);
				}
			});
		}

		function getEE(url,callback,method)
		{
			var method = (typeof(method) == 'undefined') ? 'json' : 'text';
			$.ajax({
				url: 'proxy.php?url=' + encodeURIComponent(ee_tv_box.addr) + encodeURIComponent(url),
				type: 'GET',
				dataType: method,
				beforeSend: function() {
					//$('#'+id+' .contentarea').html('<img src="/function-demos/functions/ajax/images/loading.gif" />');
				},
				success: function(data, textStatus, xhr) 
				{
					if (callback!=false)
					{
						callback(data);
					}
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
			if (button == "vol_down")
			{
				getEE('/RemoteControl/Volume/get',function(reply)
					{
						level = reply.volume - 7;
						if (level <0)
						{
							level = 0;
						}	
						getEE('/RemoteControl/Volume/set?volume='+level,false,'text');
					});
			}
			else if(button == "vol_up")
			{
				getEE('/RemoteControl/Volume/get',function(reply)
				{
					level = reply.volume + 7;
					if (level >100)
					{
						level = 100;
					}
					getEE('/RemoteControl/Volume/set?volume='+level,false,'text');
				});
			}
			else
			{
				getEE('/RemoteControl/KeyHandling/sendKey?avoidLongPress=1&key='+button,false,'text');
				//file_get_contents("{$ip_address}/RemoteControl/KeyHandling/sendKey?avoidLongPress=1&key={$button}");
			}
			//$.ajax( "remote.php?button="+button )
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
      <img src="assetts/remote.jpg" usemap="remote_control">
    </p></div>
    <h3 id="channels">Channels</h3>
    <div id="channel_contents">
      <a href="channels">Channels</a><br>
    </div>
    <h3 id="timers">Timer</h3>
    <div id="timer_contents"><p>
		<a href="timers">Timer</a><br>
    </p></div>
    <h3 id="recordings">Recordings</h3>
    <div  id="recording_contents"><p>
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
			<li><a id="am3u" href="#">EE TV Live Channels</a> .::. <a href="#" id="m3u">(M3U)</a> || </li>
			<li><a href="m3u.php?file=assetts/arqiva.m3u">Arqiva Connect</a> .::. <a href="assetts/arqiva.m3u">(M3U)</a> || </li>
		</ul>
    </div>
</div>

<map id="remote_control" name="remote_control">
	<area shape="circle" title="Power"  coords="137,41,12" href="javascript:press('on_off')"  >
	<area shape="rect" title="2"  coords="65,92,106,115" href="javascript:press('2')"  >
	<area shape="rect" title="1"  coords="14,92,56,117" href="javascript:press('1')"  >
	<area shape="rect" title="3"  coords="116,92,154,115" href="javascript:press('3')"  >
	<area shape="rect" title="4"  coords="14,126,56,148" href="javascript:press('4')"  >
	<area shape="rect" title="5"  coords="66,126,105,148" href="javascript:press('5')"  >
	<area shape="rect" title="6"  coords="116,126,154,148" href="javascript:press('6')"  >
	<area shape="rect" title="7"  coords="14,158,56,179" href="javascript:press('7')"  >
	<area shape="rect" title="8"  coords="65,158,102,179" href="javascript:press('8')"  >
	<area shape="rect" title="9"  coords="114,158,153,179" href="javascript:press('9')"  >
	<area shape="rect" title="Delete / Subtitle"  coords="14,191,56,213" href="javascript:press('delete')"  >
	<area shape="rect" title="0"  coords="63,190,103,211" href="javascript:press('0')"  >
	<area shape="rect" title="Text"  coords="114,191,153,209" href="javascript:press('text')"  >
	<area shape="rect" title="Previous"  coords="19,234,56,260" href="javascript:press('prev')"  >
	<area shape="rect" title="Play / Pause"  coords="57,234,110,260" href="javascript:press('play_pause')"  >
	<area shape="rect" title="Next"  coords="111,234,153,260" href="javascript:press('next')"  >
	<area shape="rect" title="Guide"  coords="17,415,79,435" href="javascript:press('guide')"  >
	<area shape="rect" title="Record"  coords="93,415,155,435" href="javascript:press('rec')"  >
	<area shape="rect" title="Back"  coords="20,365,58,393" href="javascript:press('back')"  >
	<area shape="rect" title="Menu"  coords="60,365,115,393" href="javascript:press('menu')"  >
	<area shape="rect" title="Info"  coords="114,365,157,393" href="javascript:press('info')"  >
	<area shape="circle" title="Green"  coords="66,459,12" href="javascript:press('green')"  >
	<area shape="circle" title="Yellow"  coords="107,459,12" href="javascript:press('yellow')"  >
	<area shape="circle" title="Blue"  coords="146,459,12" href="javascript:press('blue')"  >
	<area shape="circle" title="Red"  coords="26,459,12" href="javascript:press('red')"  >
	<area shape="circle" title="OK"  coords="85,312,17" href="javascript:press('ok')"  >
	<area shape="rect" title="Vol+"  coords="11,275,39,314" href="javascript:press('vol_up')"  >
	<area shape="rect" title="Vol-"  coords="11,315,39,350" href="javascript:press('vol_down')"  >
	<area shape="rect" title="Left"  coords="42,294,66,327" href="javascript:press('left')"  >
	<area shape="rect" title="Down"  coords="66,328,104,358" href="javascript:press('down')"  >
	<area shape="rect" title="Up"  coords="66,270,104,294" href="javascript:press('up')"  >
	<area shape="rect" title="Right"  coords="104,294,130,327" href="javascript:press('right')"  >
	<area shape="rect" title="Channel -"  coords="133,315,161,350" href="javascript:press('ch_down')"  >
	<area shape="rect" title="Channel +"  coords="133,275,161,314" href="javascript:press('ch_up')"  >
</map>
</body>
</html>
