<?php
//This class is based on Jacob Budin's TumblrOAuth, available here: https://github.com/jacobbudin/tumblroauth
//Note that it sends a Tumblr user's username and password, stored in the config file,
//because there is only one Tumblr blog being accessed here. Store your user/pass in these PHP files at your own risk.

require_once('tumblroauth/tumblroauth.php');

function postToTumblr($imgData, $why, $city, $log)
{
  $tum_oauth = new TumblrOAuth(TUMBLR_KEY, TUMBLR_SECRET);
  
  $access_token = $tum_oauth->getXAuthToken(TUMBLR_USER, TUMBLR_PASS);
  
  if (200 == $tum_oauth->http_code) {
  } else {
    die(fwrite($log, "[".strftime("%c")."] Unable to authenticate\n" ) );
  }
  
  //Gain access to the Tumblr Blog
  $tum_oauth = new TumblrOAuth($consumer_key, $consumer_secret, $access_token['oauth_token'], $access_token['oauth_token_secret']);
  
  //Configure the post
  $type = "photo";
  $caption = "<i>".$why."</i> -- ".$city."<br><br>Photo by <a href='".$imgData['link']."' target='_blank'>".$imgData['credit']."</a>";
  $link = $imgData['link'];
  $source = $imgData['source'];
  
  //Because OWS was the original, and because it was one of the few referred to as a name different from its location,
  //make sure the proper tags are added to the Tumblr post
  if( $city == "New York" )
  {
    $tags = "Occupy,OWS,Occupy Wall Street";
  }else{
    $tags = "Occupy,OWS,Occupy ".$city;
  }
  
  //Confirm the post compilation in the log file
  fwrite($log, "[".strftime("%c")."] Posting: ".$imgData['source']." :: ".$caption."\n" );
  
  //Post to Tumblr
  $userinfo = $tum_oauth->post('http://api.tumblr.com/v2/blog/'.TUMBLR_BLOG.'/post', array('type'=>$type, 'caption'=>$caption,'link'=>htmlentities($link), 'source'=>htmlentities($source), 'tags'=>$tags ));

  //Make sure it worked. If so, note it in the log file. If not, um, note it in the log file.
  if (200 == $tum_oauth->http_code) {
    fwrite($log, "[".strftime("%c")."] Response Code: ".$tum_oauth->http_code."\n" );
    return true;
  } else {
    die( fwrite($log, "[".strftime("%c")."] Unable to post: ".$tum_oauth->http_code."\n") );
  }
}
?>
