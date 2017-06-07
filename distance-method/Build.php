<?php

$WordScores = array();

foreach($FeedURLs as $FeedURL){
  $FeedContent = CacheURL($FeedURL);
  
  //Get word scores and add to the list.
  
  unset $FeedContent;
}




function CacheURL($URL){
  $Path = 'cache/'.md5($URL).'.json';
  if(file_exists($Path)){
    return file_get_contents($Path);
  }
  $Data = file_get_contents($URL);
  file_put_contents($Path,$Data);
  return $Data;
}

function pd($Var){
  echo '<pre>';
  var_dump($Var);
  echo '</pre>';
}

function ArrTabler($arr, $table_class = 'table tablesorter tablesorter-ice tablesorter-bootstrap', $table_id = null){
  $return='';
  if($table_id==null){
    $table_id=md5(uniqid(true));
  }
  if(count($arr)>0){
    $return.="\n			<div class=\"table-responsive\">\n";
    $return.= "\r\n".'			<table id="'.$table_id.'" class=" table'.$table_class.'">'."\n";
    $first=true;
    foreach($arr as $row){
      if($first){
        $return.= "				<thead>\n";
        $return.= "					<tr>\n";
        foreach($row as $key => $value){
          $return.= "						<th>".ucwords($key)."</th>\n";
        }
        $return.= "					</tr>\n";
        $return.= "				</thead>\n";
        $return.= "				<tbody>\n";
      }
      $first=false;
      if(isset($row['RSI14'])){
        if($row['RSI14']<30){
          $return.= "					<tr class=\"underbought\">\n";
        }else{
          if($row['RSI14']>70){
            $return.= "					<tr class=\"overbought\">\n";
          }else{
            $return.= "					<tr>\n";
          }
        }
      }else{
        $return.= "					<tr>\n";
      }
      foreach($row as $key => $value){
        $return.="						<td>".$value."</td>\n";
      }
      $return.= "					</tr>\n";
    }
    $return.= "				</tbody>\n";
    $return.= "			</table>\n";
    $return.= "		</div>\n";
  }
  return $return;
}

function MultidimensionalSort(&$arr, $col = 'RSI14', $dir = SORT_ASC) {
    $sort_col = array();
    foreach ($arr as $key=> $row) {
        $sort_col[$key] = $row[$col];
    }
    array_multisort($sort_col, $dir, $arr);
}
