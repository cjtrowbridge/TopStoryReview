<?php

function UserSourceBodyCallback(){
  if(!(path(1))){
    echo '<h1>Sources</h1>';
    include('../user/Sources.php');
    UserPageSources();
    return;
  }
  $Source = Query('SELECT * FROM FeedSource WHERE FeedSourceID = '.intval(path(1)));
  if(!(isset($Source[0]))){
    echo '<p>Invalid Source</p>';
    return;
  }
  $Source=$Source[0];
  
  //TODO handle post
  
  echo "<h1>Source: ".$Source['Name']."</h1>\n";
  echo "<form action=\"/source/".$Source['FeedSourceID']."\" method=\"post\" class=\"form\">\n";
  echo "  <input type=\"hidden\" name=\"FeedSourceID\" value\"".$Source['FeedSourceID']."\">\n";
  $Categories = Query("SELECT * FROM FeedCategory");
  foreach($Categories as $Category){
    $Old = Query('SELECT * FROM Feed WHERE FeedSourceID = '.$Source['FeedSourceID'].' AND FeedCategoryID = '.$Category['FeedCategoryID']));
    if(isset($Old[0])){
      $Old=$Old[0];
      $Value = $Old['URL'];
    }else{
      $Value = '';
    }
    ?>
    
    <div class="form-group row">
      <label for="Category<?php echo $Category['FeedCategoryID']; ?>" class="col-2 col-form-label"><?php echo $Category['Name']; ?></label>
      <div class="col-10">
        <input class="form-control" type="text" value=<?php ehco $Value; ?>"" id="Category<?php echo $Category['FeedCategoryID']; ?>" name="Category<?php echo $Category['FeedCategoryID']; ?>">
      </div>
    </div>
    
    <?php
  }
  ?>
    <div class="form-group row">
      <div class="col-12">
        <input type="submit" class="btn btn-block btn-success" value="Save">
      </div>
    </div>
  <?php
}
