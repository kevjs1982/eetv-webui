<html>
<head>
<style type="text/css">
html, body, embed
{
	padding:0;
	margin:0
}
</style>
<video width="100%" height="100%" controls>
  <source src="<?=$_GET['url']?>" type="video/mp4">
  <embed type="application/x-vlc-plugin" pluginspage="http://www.videolan.org"version="VideoLAN.VLCPlugin.2"  width="100%" 
height="100%" id="vlc" loop="yes"autoplay="yes" target="<?=$_GET['url']?>">
	VLC Not Found <br><br>
	Try <a href='<?=$_GET['url']?>'>Clicking Here</a></embed>
</video>
