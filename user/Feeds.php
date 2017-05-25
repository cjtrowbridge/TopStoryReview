<?php

function UserPageFeeds(){
  $Feed = Query("SELECT * FROM Feed");
  pd($Feed);
}
