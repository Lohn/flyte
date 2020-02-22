<?php
require 'admin_class.php';
$admin = new admin();

$result = "";
$limit = 60;
$offset=0;

if(isset($_GET['offset'])){
   $offset=$_GET['offset']*$limit;
}

if(!isset($_GET['user'])){
   $result = $admin->getAllTorrents($offset);
}
else{
   $result = $admin->getTorrentsByUserNick($_GET['user'], $offset);
}


echo "<div id=server class=torrents>\n<table>
<tr><th>Category</th><!--<th>id</th>--><th>Name</th><th>Info hash</th><th>Visible</th><!--<th>Filename</th><th>Descr</th><th>ori_descr</th>--><th>Downloads</th><th>Views</th><th>Seeders</th><th>Leechers</th><th>Banned</th><!--<th>hits</th>--><th>Nuke</th></tr>\n";

while($row = mysqli_fetch_array($result))
{
echo "<tr>";
$torid=$row['id'];
echo "<td>" . $admin->getNameOfCategoryByID($row['category']).  "</td>";
//echo "<td>" . $torid.  "</td>";
echo "<td>" . $row['name'].  "</td>";
//https://stackoverflow.com/questions/14674834/php-convert-string-to-hex-and-hex-to-string
echo "<td><code>" . implode(unpack("H*", $row['info_hash'])) .  "</code></td>";
//echo "<td><a href=vistorrent.php?torid=$torid>" . $row['visible'].  "</a></td>";
echo "<td><form action=vistorrent.php method=GET><input type=checkbox name=visible><input type=submit value=Apply></td>"; //TODO apply in situ, not via vistorrent.php
/**
echo "<td>" . $row['filename'].  "</td>";
echo "<td>" . $row['descr'].  "</td>";
echo "<td>" . $row['ori_descr'].  "</td>";
**/
echo "<td>" . $row['times_completed'].  "</td>";
echo "<td>" . $row['views'].  "</td>";
echo "<td>" . $row['seeders'].  "</td>";
echo "<td>" . $row['leechers'].  "</td>";
//echo "<td><a href=bantorrent.php?torid=$torid>" . $row['banned'].  "</a></td>";
echo "<td>
<form action=bantorrent.php method=GET><input type=checkbox name=banned><input type=submit value=Apply></td>"; //TODO apply in situ, not via bantorrent.php
//echo "<td>" . $row['hits'].  "</td>";
echo "<td><a href='delTorrent.php?wdel_id=".$torid."' class=button><span class=no></span></a></td>";
echo "</tr>\n";
/*
   foreach($row as $key=>$val){
      if($key =="category") $val=$admin->getNameOfCategoryByID($val);
      print( $key . "=>" . $val."<br/>" );
   }
*/
}

echo "</table></div>";
stdfoot();