<?php

function Trends($Period){
  switch($Period){
    case '24hr':
      ShowTrends('24 HOUR');
    case '1wk':
      ShowTrends('1 WEEK');
    default:
      
      die('add something about the options');
  }
}

function ShowTrends($Period){
  include_once('TopStoryFeed.php');
  $Subpath = str_replace($Period,' ','');
  $Subpath = 'trends';
  
  $Paths=array(
    'archive/trends',
    'archive/trends/'.date('Y'),
    'archive/trends/'.date('Y').'/'.date('m'),
    'archive/trends/'.date('Y').'/'.date('m').'/'.date('d')
  );
  foreach($Paths as $Path){
    if(!(file_exists($Path))){
      mkdir($Path);
    }
  }
  
  if($Period == '24 HOUR'){
    $ArchivePath = 'archive/trends/'.date('Y').'/'.date('m').'/'.date('d').'/day.json';
  }else{
    $ArchivePath = 'archive/trends/'.date('Y').'/'.date('m').'/'.date('d').'/week.json';
  }
  
  $Archive = ReadJSONArchive($ArchivePath);
  if($Archive){
    OutputJSON($Archive);
    return;
  }
  
  $Stories = Query('SELECT Headline FROM Story WHERE FetchDate > date_sub(now(),INTERVAL '.$Period.')');
  $Text = '';
  foreach($Stories as $Story){
    $Text.=$Story['Headline'].' ';
  }
  $Words = ScoreWords($Text);
  $Words = array_slice($Words,0,10);
  OutputJSON($Words);
  WriteJSONArchive($ArchivePath,$Words);
  echo 'wrote archive to: '.$ArchivePath;
  exit;
}

