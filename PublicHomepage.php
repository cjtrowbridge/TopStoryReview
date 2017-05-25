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
              
              
              $feed = new DOMDocument();
              $feed->loadHtml($Fetch['Content']);
              $json = array();
              $json['title'] = $feed->getElementsByTagName('channel')->item(0)->getElementsByTagName('title')->item(0)->firstChild->nodeValue;
              $json['description'] = $feed->getElementsByTagName('channel')->item(0)->getElementsByTagName('description')->item(0)->firstChild->nodeValue;
              $json['link'] = $feed->getElementsByTagName('channel')->item(0)->getElementsByTagName('link')->item(0)->firstChild->nodeValue;
              $items = $feed->getElementsByTagName('channel')->item(0)->getElementsByTagName('item');

              $json['item'] = array();
              $i = 0;

              foreach($items as $key => $item) {
              $title = $item->getElementsByTagName('title')->item(0)->firstChild->nodeValue;
              $description = $item->getElementsByTagName('description')->item(0)->firstChild->nodeValue;
              $pubDate = $item->getElementsByTagName('pubDate')->item(0)->firstChild->nodeValue;
              $guid = $item->getElementsByTagName('guid')->item(0)->firstChild->nodeValue;

              $json['item'][$key]['title'] = $title;
              $json['item'][$key]['description'] = $description;
              $json['item'][$key]['pubdate'] = $pubDate;
              $json['item'][$key]['guid'] = $guid; 
              }

              echo json_encode($json);
              
              pd($array);
              /*
              //insert
              $FeedID         = 
              $FeedCategoryID =  
              $SourceID       = 
              $Headline       = 
              $Author         = 
              $Photo          = 
              $Content        = 
              $PubDate        = 
              $FetchDate      = 
              
              Query("
                INSERT INTO `Story` (
                  `FeedID`, `FeedCategoryID`, `SourceID`, `Headline`, `Author`, `Photo`, `Content`, `PubDate`, `FetchDate`
                )VALUES(
                  '".$FeedID."', 
                  '".$FeedCategoryID."', 
                  '".$SourceID."', 
                  '".$Headline."', 
                  '".$Author."', 
                  '".$Photo."', 
                  '".$Content."', 
                  '".$PubDate."',
                  '".$FetchDate."'
                );
              ");
              */
            }
          }
        ?>
      </div>
    </div>
  </div>

  <?php
}
