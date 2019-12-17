<?php
set_time_limit(3600);
require_once('twitteroauth.php');
$oTwitter = new TwitterOAuth
(
	"8VcGcsdMxc5rfsGxJdC9KNR20",
	"Goh0Xp9kHJJwE6ZAOFE8pcVOf7fCFM0q0IJqU5bcnNkzz3qH0P",
	"33238900-zqm8AiCP8faDzZAk0UPEzjZcDqY1DSF7MO26tFwnb",
	"GIG0sb5C4jLffyGJLkX6mg8jXhns1u6QSMhj4ESiaUECM"
);
$e = 1;
$cursor = -1;
$full_followers = array();

do {
	$follows = $oTwitter->get("followers/ids.json?screen_name=CityOfLasVegas&cursor=".$cursor);
	$follow_array = (array)$follows;

	foreach ($follow_array['ids'] as $key => $val) {
		if ($e > 900) {
		$parameters = array( 'user_id' => $val );
		$user = $oTwitter->get('users/show', $parameters);

		$location = strtolower($user->location);
		if (preg_match('/vegas/',$location) == true) {
		 	$status = $oTwitter->post('friendships/create', $parameters);
		 	echo "Local:";
		}
		echo $location . "<br />";
		}
        $e++;
		if ($e >= 1200) die("done");
	}
	$cursor = $follows->next_cursor;
} while ($cursor > 0);


?>