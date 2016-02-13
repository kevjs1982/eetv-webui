<?header("Content-type: text/xml");?>
<?='<?xml version="1.0" encoding="utf-8"?>'?>
<browserconfig>
  <msapplication>
    <tile>
      <square70x70logo src="small-tile.png?v=<?=date('YmdHis');?>"/>
      <square150x150logo src="medium-tile.png?v=<?=date('YmdHis');?>"/>
      <wide310x150logo src="wide-tile.png?v=<?=date('YmdHis');?>"/>
      <square310x310logo src="large-tile.png?v=<?=date('YmdHis');?>"/>
      <TileColor>#009900</TileColor>
    </tile>
	<notification>
      <polling-uri  src="live-tile.xml.php?tile=1&amp;v=<?=date('YmdHis');?>"/>
      <polling-uri2 src="live-tile.xml.php?tile=2&amp;v=<?=date('YmdHis');?>"/>
      <polling-uri3 src="live-tile.xml.php?tile=3&amp;v=<?=date('YmdHis');?>"/>
	  <polling-uri4 src="live-tile.xml.php?tile=4&amp;v=<?=date('YmdHis');?>"/>
	  <polling-uri5 src="live-tile.xml.php?tile=5&amp;v=<?=date('YmdHis');?>"/>
      <frequency>30</frequency>
      <cycle>1</cycle>
    </notification>
  </msapplication>
</browserconfig>
