<?php

function TopStoryFeed($Category){
  if(
    $Category=='all'||
    $Category==false
  ){
    $Data = Query("SELECT Headline FROM Story WHERE PubDate > DATE_SUB(NOW(), INTERVAL 24 HOUR)");
  }else{
    $Feed = strtolower($Feed);
    global $ASTRIA;
    $Feed = mysqli_real_escape_string($ASTRIA['databases']['astria']['resource'],$Category);
    $Data = Query("SELECT Headline FROM Story WHERE Path LIKE '".$Feed."' AND PubDate > DATE_SUB(NOW(), INTERVAL 24 HOUR)");
  }
  $Headlines = array();
  foreach($Data as $Headline){
    $Headlines[]=$Headline['Headline'];
  }

  $Headlines = PickBest2($Headlines,5);

  OutputJSON($Headlines);
}
