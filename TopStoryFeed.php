<?php

function TopStoryFeed($Category){
  VerifyDirectoryStructure();
  
  if(
    $Category=='all'||
    $Category==false
  ){
    $Path = Query("SELECT * FROM FeedCategory WHERE Name LIKE 'All'");
  }else{
    $SafePath = mysqli_real_escape_string($ASTRIA['databases']['astria']['resource'],$Category);
    $Path = Query("SELECT Path FROM FeedCategory WHERE Path LIKE '".$SafePath."'");
    if(!(isset($Path['0']))){
      echo 'Invalid Category. Try one of these...';
      foreach(GetTSRCategories() as $Category){
        echo '<p><a href="/feed/'.$Category['Path'].'">'.$Category['Name'].'</a></p>';
      }
      exit;
    }
    
  }
  
  if($Path[0]['Name']=='all'){
    $Path[0]['Path']='all';
  }
  
  $ArchivePath = 'archive/'.$Path[0]['Path'].'/'.date('Y').'/'.date('m').'/'.date('d').'/'.date('H:00:00').'.json';
  
  $Archive = ReadJSONArchive($ArchivePath);
  if($Archive){
    $Archive['message']='Fetched From Archive. Check back each hour for a fresh feed.';
    OutputJSON($Archive);
    return;
  }
  
  if(
    $Category=='all'||
    $Category==false
  ){
    $Data = Query("SELECT Headline FROM Story WHERE PubDate > DATE_SUB(NOW(), INTERVAL 24 HOUR)");
  }else{
    global $ASTRIA;
    $FeedPath = mysqli_real_escape_string($ASTRIA['databases']['astria']['resource'],$Category);
    $FeedPath = strtolower($FeedPath);
    $Data = Query("SELECT Headline FROM Story LEFT JOIN FeedCategory ON FeedCategory.FeedCategoryID = Story.FeedCategoryID WHERE FeedCategory.Path LIKE '".$FeedPath."' AND PubDate > DATE_SUB(NOW(), INTERVAL 24 HOUR)");
  }
  $Headlines = array();
  foreach($Data as $Headline){
    $Headlines[]=$Headline['Headline'];
  }

  $Headlines = PickBest2($Headlines,5);

  WriteJSONArchive($ArchivePath,$Headlines);
  
  $Headlines['message']='Made this fresh for you. Check back each hour for a fresh feed.';
  OutputJSON($Headlines);
  
}

function GetTSRCategories(){
  $Categories = Query('SELECT * FROM FeedCategory WHERE ParentID IS NULL');
  return $Categories;
}

function VerifyDirectoryStructure(){
  $Categories = GetTSRCategories();
  foreach($Categories as $Category){
    $Subpath = $Category['Path'];
    if($Category['Name']=='All'){
      $Subpath = 'all';
    }
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
  }
}

function WriteJSONArchive($Path,$Data){
  $Data = json_encode($Data,JSON_PRETTY_PRINT);
  return file_put_contents($Path,$Data);
}

function ReadJSONArchive($Path){
  if(!(file_exists($Path))){
    return false;
  }
    
  $Data = file_get_contents($Path);
  
  if($Data == false){
    return false; 
  }
  
  $Data = json_decode($Data,true);
  
  return $Data;
}
