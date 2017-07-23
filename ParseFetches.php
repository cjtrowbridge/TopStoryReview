<?php

function TSRParseFetches(){  
  Event('ParseFetches');
  ParseFetches();
}
function ParseFetches(){
  
  global $ASTRIA;
  $Data = Query("SELECT * FROM FeedFetch LEFT JOIN Feed ON Feed.FeedID = `FeedFetch`.`FeedID`");
  
  
  $Trasher='';
  $Insert="
    INSERT INTO `Story` (
      `FeedID`, `FeedCategoryID`, `SourceID`, `Headline`, `HeadlineSHA1`, `Author`, `Photo`, `Content`, `PubDate`, `FetchDate`,`Link`
    )VALUES 
  ";
  
  foreach($Data as $Fetch){
    
    $feed = new SimplePie();
    $feed->set_raw_data($Fetch['Content']);
    $feed->init();
    $feed->handle_content_type();
    
    
    //Parse only parseable feeds
    $Count = count($feed->get_items());
    $Count = intval($Count);
    if($Count>0){
      foreach ($feed->get_items() as $item){

        //Get all the fields we will need to insert
        $FeedID         = $Fetch['FeedID'];
        $FeedCategoryID = $Fetch['FeedCategoryID'];
        $SourceID       = $Fetch['FeedSourceID'];
        $Headline       = $item->get_title();
        $Author         = $item->get_author();
        
        //TODO this doesnt work in the current version.  come up with better solution later
        $Photo          = '';//$item->get_image_url(); 
        
        $Content        = $item->get_description();
        $PubDate        = $item->get_date('Y-m-d H:i:s');
        $FetchDate      = date('Y-m-d H:i:s');
        $Link           = $item->get_permalink();

        //Sanitize each input
        $FeedID         = mysqli_real_escape_string($ASTRIA['databases']['astria']['resource'],$FeedID);
        $FeedCategoryID = mysqli_real_escape_string($ASTRIA['databases']['astria']['resource'],$FeedCategoryID);
        $SourceID       = mysqli_real_escape_string($ASTRIA['databases']['astria']['resource'],$SourceID);
        $Headline       = mysqli_real_escape_string($ASTRIA['databases']['astria']['resource'],$Headline);
        $Author         = mysqli_real_escape_string($ASTRIA['databases']['astria']['resource'],$Author);
        $Photo          = mysqli_real_escape_string($ASTRIA['databases']['astria']['resource'],$Photo);
        $Content        = mysqli_real_escape_string($ASTRIA['databases']['astria']['resource'],$Content);
        $PubDate        = mysqli_real_escape_string($ASTRIA['databases']['astria']['resource'],$PubDate);
        $FetchDate      = mysqli_real_escape_string($ASTRIA['databases']['astria']['resource'],$FetchDate);
        $Link           = mysqli_real_escape_string($ASTRIA['databases']['astria']['resource'],$Link);

        $Matches = Query("SELECT COUNT(*) as 'Matches' FROM Story WHERE FeedID = ".$FeedID." AND HeadlineSHA1 = '".md5($Headline)."'");
        if($Matches[0]['Matches']==0){

          //Build insert query
          $Insert.= "
            (
              '".$FeedID."', 
              '".$FeedCategoryID."', 
              '".$SourceID."', 
              '".$Headline."', 
              '".md5($Headline)."', 
              '".$Author."', 
              '".$Photo."', 
              '".$Content."', 
              '".$PubDate."',
              '".$FetchDate."',
              '".$Link."'
            ),";
          

        }

        $Trasher.=" OR FetchID = ".$Fetch['FetchID']." ";

      }
    }
    
  }
  
  //Insert the new things
  $Insert = rtrim($Insert,',');
  Query($Insert);
  
  //Delete the fetches we are done with from cache.
  Query("DELETE FROM FeedFetch WHERE 1=2 ".$Trasher);
}
