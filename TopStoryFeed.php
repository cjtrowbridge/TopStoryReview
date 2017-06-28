<?php

function TopStoryFeed($Category){
  if($Category=='All'){
    $Data = Query("SELECT Headline FROM Story WHERE PubDate > DATE_SUB(NOW(), INTERVAL 24 HOUR)");
  }else{
    $Data = Query("SELECT Headline FROM Story WHERE FeedCategoryID = ".intval($Category['FeedCategoryID'])." AND PubDate > DATE_SUB(NOW(), INTERVAL 24 HOUR)");
  }
  $Headlines = array();
  foreach($Data as $Headline){
    $Headlines[]=$Headline['Headline'];
  }

  $Headlines = PickBest2($Headlines,$NumberOfStories);

  OutputJSON($Headlines);
}
