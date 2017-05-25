<?php

function UserPageCategories(){
  $Category = Query("SELECT * FROM FeedCategory");
  pd($Category);
}
