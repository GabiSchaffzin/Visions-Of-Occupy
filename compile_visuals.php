<?php
  
  /* 
  
  Created by Gabi Schaffzin (http://www.barelyconcealednarcissism.com/) in March 2012.
  If you have any questions or comments, you can leave them on my GitHub page (https://github.com/GabiSchaffzin).
  No implied warranty here of any sort. See README.md for license.
  
  */
  
  include "config.php";
  include "class.flickr.php";
  include "tumblr_xauth.php";

  //For debugging purposes
  $log = fopen( "log.txt", a );
  
  fwrite($log, "\n\n----------\n[".strftime("%c")."] BEGIN Compiling Visuals\n" );

	getRandomLine();
  
  fwrite($log, "[".strftime("%c")."] END Compiling Visuals\n----------\n" );
  
  $zip;
  $why;
  
  function getRandomLine()
  {
    global $log, $zip, $why;
    
    //Make sure DB is loaded with included dataset in OccupyCOGSdata.sql file (see README.md)    
    $con = mysql_connect(DB_HOST, DB_USER, DB_PASS);
    
    //Find a random answer to the "why are you here?" question and the answer's corresponding location
    $q = "SELECT * FROM whycampdata INNER JOIN camplookup ON whycampdata.camp LIKE concat('%', camplookup.code, '%') ORDER BY RAND(NOW()) LIMIT 1;";
    
   	if( !mysql_select_db(DB_NAME, $con) )
  	{
  		 die( fwrite($log, "[".strftime("%c")."] Error connecting to database.\n" ) );
  	}
  	
  	$result = mysql_query($q, $con) or die(fwrite($log, "[".strftime("%c")."] Error querying database: ".$q."\n" ) );
  	
  	
  	while($r = mysql_fetch_assoc($result)) {
        $rows[] = $r;
    }
    
    //Package up the database result for Flickr search and Tumblr post  
    $entries = $rows[0]['entries'];
    $why = $rows[0]['why'];
    $city = $rows[0]['city'];
    
  	mysql_close($con);
  	
    $flickr = new Flickr();
    
    //Search Flickr for results
    $flickrResults = $flickr->getContent($entries, $log);
    $search_n = 0;
    
    //Give it 10 tries before calling it quits.
    while( !$flickrResults )
    {
      $flickrResults = $flickr->getContent($entries, $log);
      $search_n++;
      
      if( $search_n == 10 )
      {
        die( fwrite($log, "[".strftime("%c")."] No Results After 10.\n") );
      } 
    }
    
    //Post the compiled answer and photo to Tumblr
    if( postToTumblr( $flickrResults, $why, str_replace( "_", " ", $city ), $log ) )
    {
      fwrite($log, "[".strftime("%c")."] Posted to Tumblr\n" );
    }else{
      die( fwrite($log, "[".strftime("%c")."] Error posting to tumblr\n" ) );
    }
  }
  

?>