<?php

function TopStoryFeed($Category){
  VerifyDirectoryStructure();
  
  if(
    $Category=='all'||
    $Category==false
  ){
    $Path = Query("SELECT * FROM FeedCategory WHERE Name LIKE 'All'");
  }else{
    global $ASTRIA;
    $SafePath = mysqli_real_escape_string($ASTRIA['databases']['astria']['resource'],$Category);
    $Path = Query("SELECT Path FROM FeedCategory WHERE Path LIKE '".$SafePath."'");
    if(!(isset($Path['0']))){
      header('Location: /categories');
      exit;
    }
    
  }
  
  if(
    isset($Path[0])&&
    isset($Path[0]['Name'])&&
    $Path[0]['Name']=='All'
  ){
    $Path[0]['Path']='all';
  }
  
  $ArchivePath = 'archive/'.$Path[0]['Path'].'/'.date('Y').'/'.date('m').'/'.date('d').'/'.date('H').':00:00.json';
  
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
  
  foreach($Headlines as &$Headline){
    global $ASTRIA;
    $CleanHeadline = mysqli_real_escape_string($ASTRIA['databases']['astria']['resource'],$Headline['element']);
    
    $SQL="SELECT * FROM Story LEFT JOIN FeedCategory ON FeedCategory.FeedCategoryID = Story.FeedCategoryID LEFT JOIN FeedSource ON FeedSource.FeedSourceID = Story.SourceID WHERE `Headline` LIKE '%".$CleanHeadline."%' ORDER BY StoryID DESC LIMIT 1";
    pd($SQL);
    $Story = Query($SQL);
    pd($Story);
    if(isset($Story[0])){
      $Headline['element']=array(
        'Headline'   => $Story['Headline'],
        'PubDate'    => strtotime($Story['PubDate']),
        'Link'       => $Story['Link'],
        'SourceName' => $Story['Name'],
        'SourceLogo' => $Story['LogoURL']
        
      );
    }
  }
  
  WriteJSONArchive($ArchivePath,$Headlines);
  
  $Headlines['message']='Made this fresh for you. Check back each hour for a fresh feed.';
  OutputJSON($Headlines);
  
}

function ListCategories(){
  $Categories = GetTSRCategories();
  $Output = array();
  
  foreach($Categories as $Category){
    $Output[$Category['FeedCategoryID']]=array(
      'Name'        => $Category['Name'],
      'Description' => $Category['Description'],
      'FeedLink'    => 'https://api.topstoryreview.com/feed/'.$Category['Path']
    );
  }
  
  OutputJSON($Output);
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
