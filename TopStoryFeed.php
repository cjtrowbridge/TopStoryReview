<?php

function TopStoryFeed($Category){
  $CacheKey = md5('TopStoryReviewFeed '.date('Y-m-d H'));
  $Cache = readDiskCache($CacheKey);
  if($Cache){
    $Cache['message']='Fetched From Cache. Check back each hour for a fresh feed.';
    OutputJSON($Cache);
    return;
  }
  
  if(
    $Category=='all'||
    $Category==false
  ){
    $Data = Query("SELECT Headline FROM Story WHERE PubDate > DATE_SUB(NOW(), INTERVAL 24 HOUR)");
  }else{
    global $ASTRIA;
    $FeedPath = mysqli_real_escape_string($ASTRIA['databases']['astria']['resource'],$Category);
    $FeedPath = strtolower($FeedPath);
    $Data = Query("SELECT Headline FROM Story LEFT JOIN FeedCategory ON FeedCategory.FeedCategoryID = Story.FeedCategoryID WHERE FeedCategory.Path LIKE '".$FeedPath."' AND PubDate > DATE_SUB(NOW(), INTERVAL 24 HOUR)");
  }
  $Headlines = array();
  foreach($Data as $Headline){
    $Headlines[]=$Headline['Headline'];
  }

  $Headlines = PickBest2($Headlines,5);

  writeDiskCache($CacheKey,$Headlines);
  $Headlines['message']='Made this fresh for you. Check back each hour for a fresh feed.';
  OutputJSON($Headlines);
  
}
