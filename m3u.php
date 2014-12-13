<?
header('Content-Type: text/html; charset=utf-8');
?>
<ul><?

$f = file($_GET['file']);
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