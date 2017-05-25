<?php

function UserPageFeeds(){
  $Feed = Query("SELECT * FROM Feeds");
  pd($Feeds);
}
