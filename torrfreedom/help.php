<?php
require_once 'include/bittorrent.inc.php';
dbconn();
stdhead();
?>
<table id=wrapper class=help>
<tr><td>
<div>

<h3>How do I download a torrent?</h3>
<p>Choose the torrent you like and right click on the magnet or torrent icon and select <i>Copy Link Location</i>, paste the link into your I2P-capable BitTorrent client, and then start the torrent.</p>
<p>If you don't have an I2P-capable BitTorrent client, we recommend the <a href=i2psnark-standalone.zip>standalone version</a> of I2PSnark which can be used with i2pd or other I2P software that doesn't provide its own client.
<h3>How do I upload a torrent?</h3>
<ol id=howtoupload>
<li>Create a torrent file in your favorite I2P-capable BitTorrent client, optionally adding the tracker announce address indicated above, or upload an existing .torrent file you have downloaded. Note: if <span id=trackername><?=strtolower($tracker_title)?></span>'s tracker isn't already present in the torrent, it will be added automatically, so you can upload ANY torrent file.</li>
<li><a href=signup.php>Sign up</a> in order to upload torrents. Only a username and password is required to create an account. All passwords are encrypted on the server.</li>
<li><a href=upload.php>Upload</a> your torrent file!</li>
<li>Start seeding your torrent and others will see it!</li>
</ol>

<h3>Can I be notified of new torrents?</h3>
<p>Yes! Use the <a href=rss.php>RSS Feed</a> feature to track new uploads.</p>

<h3>What's the announce address for this tracker?</h3>
<p>You can use any of the following addresses:<br>
<div id=announcelist>
<?php
$tooltip = "Click this link and copy - the full url will be selected for pasting";
$spacer = "&nbsp;&nbsp;&nbsp;&bullet;&nbsp;&nbsp;";
print("<span class=spacer>" . $spacer . "</span><code title=\"" . $tooltip . "\">"  . $tracker_url_name. "/a</code><br>");
print("<span class=spacer>" . $spacer . "</span><code title=\"" . $tooltip . "\">"  . $tracker_url_name. "/announce</code><br>");
print("<span class=spacer>" . $spacer . "</span><code title=\"" . $tooltip . "\">"  . $tracker_url_name. "/announce.php</code><br>");
print("<span class=spacer>" . $spacer . "</span><code title=\"" . $tooltip . "\">"  . $tracker_url_key. "/a</code><br>");
print("<span class=spacer>" . $spacer . "</span><code title=\"" . $tooltip . "\">"  . $tracker_url_key. "/announce</code><br>");
print("<span class=spacer>" . $spacer . "</span><code title=\"" . $tooltip . "\">"  . $tracker_url_key. "/announce.php</code>");
?>
</div>
</p>
<p>Note: If you use the domain name in preference to the .b32 address, be sure you have the domain in your addressbook.</p>

<h3>Can I use html tags in the torrent descriptions and comments?</h3>
Yes you can! There is support for a limited number of <a href=https://www.w3schools.com/TAGs/ target=_blank>html tags</a> to allow better presentation of your submitted text. The following tags are permitted: <code>&lt;b&gt; &lt;strong&gt; &lt;i&gt; &lt;ul&gt; &lt;ol&gt; &lt;li&gt; &lt;hr&gt; &lt;br&gt; &lt;p&gt;</code>

<h3>Is the tracker software available to download?</h3>
<p>The tracker software (a fork of Byte Monsoon) is currently in heavy development. When it's ready for release, it will be available as a torrent on the site. Watch this space!</p>
</div>
</td></tr>
</table>
<?php stdfoot(); ?>
