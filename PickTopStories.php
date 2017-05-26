<?php

function PickTopStories($Category = 'All',$NumberOfStories = 5){
  if($Category==null){
    //TODO log these
    ?>
    <p>Sorry about that.</p>
    <?php
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
    ?>
    <p><i>Out of <?php echo count($Data); ?> headlines in category '<?php echo $Category['Name']; ?>,' I picked these <?php echo $NumberOfStories; ?> for you.</i></p>
    <?php
    foreach($Headlines as $Headline){
      ?>

        <p><?php echo $Headline; ?></p>

      <?php
    }
}
