<?php

function PublicHomepage(){
  global $Title,$Category;
  if(path(0)==false){
    $Title    = "Here's The Latest";
    $Category = Query("SELECT * FROM FeedCategory WHERE Name LIKE 'All'")[0];
  }else{
    $Categories = Query('SELECT * FROM FeedCategory');
    $Category = null;
    //$Path  = mysqli_real_escape_string($ASTRIA['databases']['astria']['resource'],path(0));
    foreach($Categories as $ThisCategory){
      if(path(0)==$ThisCategory['Path']){
        $Category = $ThisCategory;
        break;
      }
    }
    if($Category==null){
      $Title = 'Not Found';
    }else{
      $Title = $Category['Name'];
    }
  }
  TemplateBootstrap4($Title,'PublicHomepageBodyCallback();');
}

function PublicHomepageBodyCallback(){
  global $Title,$Category;
  ?>

  <div class="container">
    <div class="row">
      <div class="col-xs-12 col-md-9">
        <h1><?php echo $Title; ?></h1>
        <?php
          echo PickTopStories($Category)['Content'];
        ?>
      </div>
      <div class="col-xs-12 col-md-3">
        <h2>Archive</h2>
        <?php
          $Data = Query("SELECT * FROM HeadlineArchive WHERE FeedCategoryID = ".$Category['FeedCategoryID']." ORDER BY DateTime DESC");
          foreach($Data as $Entry){
          ?>
            <p><a href="<?php echo $Entry['Permalink']; ?>"><?php echo $Entry['Preview']; ?></a><br><i><?php ago($Entry['DateTime']); ?></i></p>
          <?php
          }
        ?>
      </div>
    </div>
  </div>

  <?php
}
