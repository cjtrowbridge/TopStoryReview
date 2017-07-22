<?php

function TSRParseFetches(){  
  Event('ParseFetches');
  ParseFetches();
}
function ParseFetches(){
  $startTime = microtime(true);
  
  global $ASTRIA;
  //$Data = Query("SELECT * FROM FeedFetch LEFT JOIN Feed ON Feed.FeedID = `FeedFetch`.`FeedID` WHERE ItemCount > 0");
  $Data = Query("SELECT * FROM FeedFetch LEFT JOIN Feed ON Feed.FeedID = `FeedFetch`.`FeedID`");
  
  echo '<p>a '.(microtime(true)-$startTime).'</p>';
  
  foreach($Data as $Fetch){
    echo '<p>b '.(microtime(true)-$startTime).'</p>';
    $FetchParseStartTime = microtime(true);
    
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

        $Matches = Query("SELECT COUNT(*) as 'Matches' FROM Story WHERE FeedID = ".$FeedID." AND Headline LIKE '".$Headline."'");
        if($Matches[0]['Matches']==0){

          //Build insert query
          $Insert = "
            INSERT INTO `Story` (
              `FeedID`, `FeedCategoryID`, `SourceID`, `Headline`, `Author`, `Photo`, `Content`, `PubDate`, `FetchDate`,`Link`
            )VALUES(
              '".$FeedID."', 
              '".$FeedCategoryID."', 
              '".$SourceID."', 
              '".$Headline."', 
              '".$Author."', 
              '".$Photo."', 
              '".$Content."', 
              '".$PubDate."',
              '".$FetchDate."',
              '".$Link."'
            );
          ";
          Query($Insert);

        }

        //Delete this fetch from cache since we are done with it.
        Query("DELETE FROM FeedFetch WHERE FetchID = ".$Fetch['FetchID']);

      }
    }
    
    echo '<p>--Parsed FeedID: '.$FeedID.' in '.(microtime(true)-$FetchParseStartTime).' seconds</p>';
    
  }
  echo '<p>c '.(microtime(true)-$startTime).'</p>';
}
