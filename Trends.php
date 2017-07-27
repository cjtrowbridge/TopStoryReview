<?php

function Trends($Period){
  switch($Period){
    case '24hr':
      $Stories = Query('SELECT Headline FROM Story WHERE FetchDate > date_sub(now(),INTERVAL 24 HOUR)');
      $Text = '';
      foreach($Stories as $Story){
        $Text.=$Story['Headline'].' ';
      }
      $Words = ScoreWords($Text);
      $Words = array_slice($Words,0,10);
      OutputJSON($Words);
      exit;
    case '1wk':
      $Stories = Query('SELECT Headline FROM Story WHERE FetchDate > date_sub(now(),INTERVAL 1 WEEK)');
      $Text = '';
      foreach($Stories as $Story){
        $Text.=$Story['Headline'].' ';
      }
      $Words = ScoreWords($Text);
      $Words = array_slice($Words,0,10);
      OutputJSON($Words);
      exit;
    default:
      
      break;
  }
}

