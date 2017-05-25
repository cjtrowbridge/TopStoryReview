<?php

function UserPageCategories(){
  $Categories = Query("SELECT * FROM FeedCategories");
  pd($Categories);
}
