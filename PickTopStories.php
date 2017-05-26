<?php

function PickTopStories($Category = 'All',$NumberOfStories = 5){
  $Output = '';
  if($Category==null){
    //TODO log these
    $Output.='<p>Sorry about that.</p>';
  }else{
    if($Category['Name']=='All'){
      $Data = Query("SELECT Headline FROM Story WHERE PubDate > DATE_SUB(NOW(), INTERVAL 24 HOUR)");
    }else{
      $Data = Query("SELECT Headline FROM Story WHERE FeedCategoryID = ".intval($Category['FeedCategoryID'])." AND PubDate > DATE_SUB(NOW(), INTERVAL 24 HOUR)");
    }
    $Headlines = array();
    foreach($Data as $Headline){
      $Headlines[]=$Headline['Headline'];
    }

    $NumberOfStories=5;
    $Headlines = PickBest($Headlines,$NumberOfStories);
    $Output.='<p><i>Out of '.count($Data).' headlines in category \''. $Category['Name'].',\' I picked these '.$NumberOfStories.' for you.</i></p>';
    foreach($Headlines as $Headline){
      $Output.='<p>'.$Headline.'</p>';
    }
  }
  $Preview=array();
  return array(
    'Preview' => $Preview,
    'Content' => $Output
  );
}
