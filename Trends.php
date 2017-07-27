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

ShowTrends($Period){
  $Subpath = str_replace($Period,' ','');
  
  $Paths=array(
    'archive/'.$Subpath,
    'archive/'.$Subpath.'/'.date('Y'),
    'archive/'.$Subpath.'/'.date('Y').'/'.date('m'),
    'archive/'.$Subpath.'/'.date('Y').'/'.date('m').'/'.date('d')
  );
  foreach($Paths as $Path){
    if(!(file_exists($Path))){
      mkdir($Path);
    }
  }
  
  $ArchivePath = 'archive/trends/'.$Subpath.'/'.date('Y').'/'.date('m').'/'.date('d').'/'.date('H').':00:00.json';
  
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
  exit;
}

