<?php
set_time_limit(600);
require_once('twitteroauth.php');
$message_total = 0;
$oTwitter = new TwitterOAuth
(
	"8VcGcsdMxc5rfsGxJdC9KNR20",
	"Goh0Xp9kHJJwE6ZAOFE8pcVOf7fCFM0q0IJqU5bcnNkzz3qH0P",
	"33238900-zqm8AiCP8faDzZAk0UPEzjZcDqY1DSF7MO26tFwnb",
	"GIG0sb5C4jLffyGJLkX6mg8jXhns1u6QSMhj4ESiaUECM"
);
$aFollowing = $oTwitter->get('friends/ids');
$aFollowing = $aFollowing->ids;

$aFollowers = $oTwitter->get('followers/ids');
$aFollowers = $aFollowers->ids;
$i=0;
foreach( $aFollowing as $iFollowing )
{
	$isFollowing = in_array( $iFollowing, $aFollowers );


	$i++;
	if ($i < 600) continue;
	else $message_total++;
		//echo "$iFollowing: ".( $isFollowing ? 'OK' : '!!!' )."<br/>";
	//if( !$isFollowing )
	//{
		$parameters = array( 'id' => $iFollowing );
		$name = $oTwitter->get('users/show', $parameters);
		
		//if ($name->followers_count > 500) {
			$parameters = array( 'status' => "Hey, @".$name->screen_name . " will you share my music with your friends? https://reverbnation.com/ciphercode" );
			$status = $oTwitter->post('statuses/update', $parameters);
       // }
	//} 
	if ($message_total === 50) break;
	
}

?>