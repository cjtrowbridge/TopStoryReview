<?php

function TSRParseFetches(){
  Event('CountItemsInFetches');
  CountItemsInFetches();
  
  Event('ParseFetches');
  ParseFetches();
  
  //TODO save output for each category to html files in archive folder
  
  Event('SaveAllHeadlinePages');
  SaveAllHeadlinePages();
}

function SaveAllHeadlinePages(){
  global $ASTRIA;
  $Categories = Query("SELECT * FROM FeedCategory");
  foreach($Categories as $Category){
    if($Category['Name']=='All'){
      $Data = Query("SELECT Headline FROM Story");
    }else{
      $Data = Query("SELECT Headline FROM Story WHERE FeedCategoryID = ".intval($Category['FeedCategoryID']));
    }
    $Headlines = array();
    foreach($Data as $Headline){
      $Headlines[]=$Headline['Headline'];
    }

    $Headlines = PickBest($Headlines,5);
    $Content='';
    $Preview='';
    foreach($Headlines as $Headline){
      $Content.="<p>".$Headline."</p>\n";
      $Preview.=$Headline.' ';
    }
    
    
    
    $Scores = CondenseGetWordScores($Preview);
    CondenseSortByScore($Scores, 'Score');
    $Preview=$Scores[0]['Word'].', '.$Scores[1]['Word'].', '.$Scores[2]['Word'];
    $Preview = mysqli_real_escape_string($ASTRIA['databases']['astria']['resource'],$Preview);
    
    
    $PreviewLink = $Scores[0]['Word'].'-'.$Scores[1]['Word'].'-'.$Scores[2]['Word'];
    $PreviewLink=strtolower($PreviewLink);
    $PreviewLink=urlencode($PreviewLink);
    
    $Permalink = '/archive/'.$Category['Path'].'/'.date('Y-m-d-H').'/'.$PreviewLink;
    
    Query("INSERT INTO HeadlineArchive (Permalink,Content,FeedCategoryID,DateTime)VALUES('".$Permalink."','".$Content."',".$Category['FeedCategoryID'].",NOW()),'".$Preview."'");
    
  }
}

function CountItemsInFetches(){
  //The purpose of this function is to first check whether a feed can be parsed, and then they will be parsed where possible.
  global $ASTRIA;
  $Data = Query("SELECT * FROM FeedFetch LEFT JOIN Feed ON Feed.FeedID = `FeedFetch`.`FeedID` WHERE ItemCount IS NULL");
  foreach($Data as $Fetch){
    $feed = new SimplePie();
    $feed->set_raw_data($Fetch['Content']);
    $feed->init();
    $feed->handle_content_type();
    
    $Count = count($feed->get_items());
    $Count = intval($Count);
    $SQL="UPDATE FeedFetch SET ItemCount = '".$Count."' WHERE FetchID = ".$Fetch['FetchID'].";\n";
    Query($SQL);
  }
}

function ParseFetches(){
  global $ASTRIA;
  $Data = Query("SELECT * FROM FeedFetch LEFT JOIN Feed ON Feed.FeedID = `FeedFetch`.`FeedID` WHERE ItemCount > 0");
  foreach($Data as $Fetch){
    $feed = new SimplePie();
    $feed->set_raw_data($Fetch['Content']);
    $feed->init();
    $feed->handle_content_type();
    
    //TODO check these and update if different
    //echo $feed->get_title();
    
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
      //$PubDate        = $item->get_date('j F Y | g:i a');
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
      
      //Build delete query
      $Delete = "DELETE FROM FeedFetch WHERE FetchID = ".$Fetch['FetchID'];
      Query($Delete);
      
    }

  }
}
