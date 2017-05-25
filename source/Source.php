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
  
  if(isset($_POST['FeedSourceID'])){
    $Categories = Query("SELECT * FROM FeedCategory");
    foreach($Categories as $Category){
      if(isset($_POST['Category'.$Category['FeedCategoryID']])){
        global $ASTRIA;
        $URL = mysqli_real_escape_string($ASTRIA['databases']['astria']['resource'],$_POST['Category'.$Category['FeedCategoryID']]);
        $Old = Query('SELECT * FROM Feed WHERE FeedSourceID = '.intval($_POST['FeedSourceID']).' AND FeedCategoryID = '.$Category['FeedCategoryID']);
        if(isset($Old[0])){
          //Check if this needs to be updated
          if(!($Old[0]['URL']==$URL)){
            echo 'Update';
            Query("UPDATE `Feed` SET `URL` = '".$URL."' WHERE `Feed`.`FeedID` = 7;");
          }
        }else{
          echo 'Insert';
          if(!(trim($URL)=='')){
            Query("
              INSERT INTO `Feed` 
              (
                  `FeedSourceID`,`FeedCategoryID`,`URL`
              ) VALUES (
                  '".intval($_POST['FeedSourceID'])."', '".$Category['FeedCategoryID']."', '".$URL."'
              );
            ");
          }
        }
      }
    }
    //TODO this should be a redirect if it becomes public-facing.
  }
  
  echo "<h1>Source: ".$Source['Name']."</h1>\n";
  echo "<form action=\"/source/".$Source['FeedSourceID']."\" method=\"post\" class=\"form\">\n";
  echo "  <input type=\"hidden\" name=\"FeedSourceID\" value\"".$Source['FeedSourceID']."\">\n";
  $Categories = Query("SELECT * FROM FeedCategory");
  foreach($Categories as $Category){
    $Old = Query('SELECT * FROM Feed WHERE FeedSourceID = '.$Source['FeedSourceID'].' AND FeedCategoryID = '.$Category['FeedCategoryID']);
    if(isset($Old[0])){
      $Old=$Old[0];
      $Value = $Old['URL'];
    }else{
      $Value = '';
    }
    if($Category['ParentID']==''){
      $Name = '/'.$Category['Path'];
    }else{
      $Parent = Query('SELECT Path FROM FeedCategory WHERE FeedCategoryID = '.$Category['ParentID']);
      $Name = '/'.$Parent[0]['Path'].'/'.$Category['Path'];
      //TODO nest indefinitely
    }
    
    ?>
    
    <div class="form-group row">
      <label for="Category<?php echo $Category['FeedCategoryID']; ?>" class="col-2 col-form-label"><?php echo $Name; ?></label>
      <div class="col-10">
        <input class="form-control" type="text" value="<?php echo $Value; ?>" id="Category<?php echo $Category['FeedCategoryID']; ?>" name="Category<?php echo $Category['FeedCategoryID']; ?>">
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
