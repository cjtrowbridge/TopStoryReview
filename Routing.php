<?php

include('PickTopStories.php');

Hook('Template Head','TopStoryReviewTemplateHead();');
function TopStoryReviewTemplateHead(){
  ?>
  <link rel="stylesheet" href="/plugins/TopStoryReview/style.css">
  <?php
}

Hook('FeedSync Fetch Service Done','TSRParser();');
function TSRParser(){
  include('ParseFetches.php');
  TSRParseFetches();
  
  /*
  include('TopStoryFeed.php');
  $Categories = GetTSRCategories();
  foreach($Categories as $Category){
    TopStoryFeed($Category['Path']);
  }
  */
  file_get_contents('https://topstoryreview.com/Build.php');
}


Hook('User Is Not Logged In - Before Presentation','PublicPageBefore();');
function PublicPageBefore(){
 /*$Categories = Query("SELECT * FROM FeedCategory WHERE Name NOT LIKE 'Featured'");
  foreach($Categories as $Category){
    if($Category['ParentID']==''){
      //Add Any Children
      $Children = array();
      foreach($Categories as $Child){
        if($Child['ParentID']==$Category['FeedCategoryID']){
          $Children[]=array(
            'text' => $Child['Name'],
            'link' => $Child['Path']
          );
        }
      }
      if(count($Children)>0){
       array_unshift($Children, array(
          'text' => $Category['Name'],
          'link' => '/'.$Category['Path']
        ), array(
          'text' => 'divider',
          'link' => ''
        ));

        $Type = 'dropdown';
      }else{
        $Type = 'link';
      }
      $Text = $Category['Name'];
      $Path = '/'.$Category['Path'];
      
      Nav('main-not-logged-in',$Type,$Text,$Path,$Children);
    }
  }
  */
  //Nav('main-not-logged-in','link','Explore','/explore');
  //Nav('main-not-logged-in','link','Login','/login');
}


Hook('User Is Not Logged In - Presentation','PublicPage();');
function PublicPage(){
  switch(path(0)){
    case 'word-scores':
      include('TopStoryFeed.php');
      ShowWords();
      break;
    case 'categories':
      include('TopStoryFeed.php');
      ListCategories();
      break;
    case 'feed':
      include('TopStoryFeed.php');
      TopStoryFeed(path(1));
      break;
    //case 'archive':
      //TODO
    case 'login':
      PromptForLogin();
      break;
    default:
      header('Location: /categories');
      break;
  }
}


Hook('User Is Logged In - Before Presentation','UserPageBefore();');

function UserPageBefore(){
  //Nav('main-logged-in','link','Explore','/explore');
}


Hook('User Is Logged In - Presentation','UserPage();');

function UserPage(){
  switch(path(0)){
    case 'source':
      include('source/Source.php');
      TemplateBootstrap4('Source','UserSourceBodyCallback();');
      break;
    default:
      include('UserHomepage.php');
      TemplateBootstrap4('Home','UserHomepageBodyCallback();');
      break;
  }
  
}
