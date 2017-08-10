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
  global $FeedSyncFetchServiceDuration;
  include('ParseFetches.php');
  
  $tTSRParseFetches=microtime(true);
  ParseFetches();
  
  $tBuild=microtime(true);
  file_get_contents('https://topstoryreview.com/Build.php');
  
  echo '<p>-Fetching Took: '.$FeedSyncFetchServiceDuration.'</p>';
  echo '<p>-Parsing Took:&nbsp; '.($tBuild - $tTSRParseFetches).'</p>';
  echo '<p>-Building Took: '.(microtime(true) - $tBuild).'</p>';
  
}

Hook('User Is Not Logged In - Presentation','PublicPage();');
function PublicPage(){
  set_time_limit(0);
  switch(path(0)){
    case 'sources':
      include('TopStoryFeed.php');
      ListSources();
      break;
    case 'categories':
      include('TopStoryFeed.php');
      ListCategories();
      break;
    case 'feed':
      include('TopStoryFeed.php');
      TopStoryFeed(path(1));
      break;
    case 'trends':
      include('Trends.php');
      Trends(path(1));
      break;
    case 'login':
      PromptForLogin();
      break;
    default:
      header('Location: /categories');
      break;
  }
}

Hook('User Is Logged In - Presentation','UserPage();');
function UserPage(){
  switch(path(0)){
    case 'word-scores':
      set_time_limit(0);
      include('TopStoryFeed.php');
      ShowWords();
      break;
    case 'source':
      include('source/Source.php');
      TemplateBootstrap4('Source','UserSourceBodyCallback();');
      break;
    case false:
      include('UserHomepage.php');
      TemplateBootstrap4('Home','UserHomepageBodyCallback();');
      break;
  }
  
}
