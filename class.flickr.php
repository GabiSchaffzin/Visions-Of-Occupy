<?php
  //This class is a modified version of the PEAR::Flickr_API class written by Cal Henderson
  //It requires the installation of PEAR. For more information see http://code.iamcal.com/php/flickr/readme.htm
	ini_set('include_path', PATH_TO_PEAR);
	
	require_once 'Flickr/API.php';
	
	class Flickr   
	{
	
		function Flickr()
		{
			
		}
	
		public function getContent($entries, $log)
		{
			//Create a new api object

			$api =& new Flickr_API(array(
					'api_key'  => FLICKR_API_KEY,
					'api_secret' => FLICKR_API_SECRET
				));


			//Make sure you have all of your tags
      $tags = $this->multipleExplode( array(",", ";" ), $entries );

      $newTags = "";
      
      //Trim the tags of spaces
      foreach( $tags as $tag )
      {
        if( stripos($tag, "occupy" )===FALSE && stripos($tag, "OWS" )===FALSE && stripos($tag,"Occupons")===FALSE )
        {
          $newTags = $newTags."Occupy ".trim($tag).",";
        }else{
          $newTags = $newTags.trim($tag).",";
        }
      }
      
      //Call the search method on the Flickr API for the tags
			$response = $api->callMethod('flickr.photos.search', array(
					'tags' => $newTags,
					'per_page' => '100'
				));

			//Check the response
			$photo_id_list = array();
			$result = array();

			if ($response){

        //Make sure you have more than 0 photos
				$photo_list = $response->getNodeAt( "photos" )->children;
        $photo_count = count( $photo_list );

        $photo_id;
        $photo_id_grab_attempts = 0;
        
        if( $photo_count == 0 )
        {
          return false;
        }
        
        //Try to grab the photos. Sometimes, this fails. Try five times, then give up.
        while( !$photo_id )
        {
          $photo_id = $response->getElement( array( 1, rand(0, $photo_count-1) ) )->getAttribute( "id" );
          
          $photo_id_grab_attempts++;
          
          if( $photo_id_grab_attempts == 5 )
          {
            die( fwrite($log, "[".strftime("%c")."] COULD NOT GRAB PHOTO ID AFTER 5 ATTEMPTS\n" ) );
          }
        }
				
        //Get the owner of the photograph for proper credit in the final Tumblr post
        $photoObject = $this->getInfo( $photo_id, $api );
				
        return array(
          'source' => $this->getSizeSource( $photo_id, $api ),
          'link' => $photoObject["source_link"],
          'credit' => $photoObject["username"]
        );
		

			}else{
				$code = $api->getErrorCode();
				$message = $api->getErrorMessage();
			}
			
			
			
		}
		
		
		private function multipleExplode($delimiters = array(), $string = ''){ 

      $mainDelim=$delimiters[count($delimiters)-1]; // dernier 
      
      array_pop($delimiters); 
      
      foreach($delimiters as $delimiter){ 
      
          $string= str_replace($delimiter, $mainDelim, $string); 
      
      } 
  
      $result= explode($mainDelim, $string); 
      return $result; 
  
    } 
		
		private function getInfo( $photo_id, $api )
		{
			
		
			$info_response = $api->callMethod( 'flickr.photos.getInfo', array(
			
				'photo_id' => $photo_id
			
			));
		
			$infoObj;
		
			if( $info_response )
			{
						
				$infoObj[ "title" ] = $info_response->getNodeAt( "photo/title" )->content;
				$infoObj[ "date"] = strftime( "%c", strtotime( $info_response->getNodeAt( "photo/dates" )->getAttribute( "taken" ) ) );
				$infoObj[ "source_link" ] = $info_response->getNodeAt( "photo/urls/url" )->content;
				if( $info_response->getNodeAt( "photo/owner" )->getAttribute( "username" ) )
				{
          $infoObj[ "username" ] = str_replace( " ", "_", $info_response->getNodeAt( "photo/owner" )->getAttribute( "username" ) );
				}
			}else{
				# fetch the error
				$code = $api->getErrorCode();
				$message = $api->getErrorMessage();
			}
		
			return $infoObj;
		}
	
		private function getSizeSource( $photo_id, $api )
		{
			$sizes_response = $api->callMethod( 'flickr.photos.getSizes', array(
			
				'photo_id' => $photo_id
			
			));
		
			if( $sizes_response )
			{
				$size_list = $sizes_response->getNodeAt( "sizes" )->children;
		
				$man_width = 414;
				$use_width = 0;
				$use_url = "";
			
				for( $i = 0; $i < count( $size_list ); $i++ )
				{
					$photo_width = $sizes_response->getElement( array( 1, $i ) )->getAttribute( "width" );
				
					if( $photo_width )
					{
						$use_width = ( integer )$photo_width;
						$use_url = $sizes_response->getElement( array( 1, $i ) )->getAttribute( "source" );
					
						if( $use_width > $man_width )
						{
							return $use_url;
						}
					}

				}
			
			}else{
				$code = $api->getErrorCode();
				$message = $api->getErrorMessage();
			}
		}
	}
?>