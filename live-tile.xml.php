<?header("Content-type: text/xml");?>
<?='<?xml version="1.0" encoding="utf-8"?>'?>
<?
$config = json_decode(file_get_contents('config.js'));
$root = "http://{$config->html_root}/";
?>
<tile>
  <visual lang="en-US" version="2">  
    <binding template="TileSquare150x150Image" branding="logo">
      <image id="1" src="<?=$root?>live-icon?medium&amp;idx=<?=$_GET['tile']?>"/> 
    </binding>

    <binding template="TileWide310x150ImageAndText01" branding="logo">
      <image id="1" src="<?=$root?>live-icon?wide&amp;idx=<?=$_GET['tile']?>"/> 
    </binding>
 
    <binding template="TileSquare310x310ImageAndText01" branding="logo">
      <image id="1" src="<?=$root?>live-icon?large&amp;idx=<?=$_GET['tile']?>"/>
      <text id="1">Coming Soon</text>
      <text id="2">To your EE TV Box</text>
    </binding>
  </visual>
</tile>

