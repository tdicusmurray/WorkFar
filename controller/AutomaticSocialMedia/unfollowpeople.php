<?php
set_time_limit(600);
require_once('twitteroauth.php');
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

foreach( $aFollowing as $iFollowing )
{
	$isFollowing = in_array( $iFollowing, $aFollowers );

	echo "$iFollowing: ".( $isFollowing ? 'OK' : '!!!' )."<br/>";

	if( !$isFollowing )
	{	
		$parameters = array( 'id' => $iFollowing );
		$name = $oTwitter->get('users/show', $parameters);
		//if ($name->followers_count < 500) {
			$parameters = array( 'user_id' => $iFollowing );
			$status = $oTwitter->post('friendships/destroy', $parameters);
		//}
	}
}

?>