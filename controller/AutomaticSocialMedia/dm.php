<?php
set_time_limit(600);
require_once('twitteroauth.php');
$message_total = 0;
$oTwitter = new TwitterOAuth
(
	"950Geb3viKctKPRHoXx7uJhoV",
	"36VB9AY9bjcyk1v8kTbxN0P7znALLNqlwWIbfobB0TN8x6z8Vu",
	"33238900-CuXAwyP5LnHdixoFndf8iKJG6mSxwBVZ6eTa6ESns",
	"yPvdm6ydR7JpN46Y6MQ4DAYx1ATGICjb9zKaF3guT50Ev"
);

$aFollowing = $oTwitter->get('friends/ids');
$aFollowing = $aFollowing->ids;

$aFollowers = $oTwitter->get('followers/ids');
$aFollowers = $aFollowers->ids;
$i = 0;
foreach( $aFollowing as $iFollowing )
{
	$i++;
	$isFollowing = in_array( $iFollowing, $aFollowers );

	if ( $i < 200 ) continue;

	if( $isFollowing )
	{
		$parameters = array( 'id' => $iFollowing );
		$name = $oTwitter->get('users/show', $parameters);
		$parameters = array( 'user_id' => $name->id, 'text' =>"can you retweet my featured song? Message me back I love to talk to fans."  );
		$status = $oTwitter->post('direct_messages/new', $parameters);
	} 
	if ($message_total === 300) break;
	$message_total++;
	
	echo $message_total . "<br />";
}

?>