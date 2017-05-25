<?php

function PublicHomepage(){
  global $Title,$Category;
  if(path(0)==false){
    $Title    = "Here's The Latest";
    $Category = Query("SELECT * FROM FeedCategory WHERE Name LIKE 'Featured'")[0];
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
      <div class="col-xs-12">
        <h1><?php echo $Title; ?></h1>
        <?php
          if($Category==null){
            //TODO log these
            ?>
            <p>Sorry about that.</p>
            <?php
          }else{
            ?>
            <p>Here we go</p>
            <?php
            $Data = Query("SELECT * FROM FeedFetch LEFT JOIN Feed ON Feed.FeedID = `FeedFetch`.`FeedID` WHERE FeedCategoryID = ".$Category['FeedCategoryID']);
            foreach($Data as $Fetch){
              
              $feed = new SimplePie();
              $feed->set_raw_data($Fetch['Content']);
              $feed->init();
              $feed->handle_content_type();
              echo $feed->get_title();
              
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
                //TODO there is probably an easier way to do this. That method probably just uses php date but this is a bandaid.
                $PubDate        = date('Y-m-d H:i:s',strtotime($item->get_date('j F Y | g:i a')));
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
                
                //Build delete query
                $Delete = "DELETE FROM FeedFetch WHERE FetchID = ".$Fetch['FetchID'];
                
                echo '<p>'.$Insert.'</p>';
                echo '<p>'.$Delete.'</p>';
              }
              
            }
          }
        ?>
      </div>
    </div>
  </div>

  <?php
}
