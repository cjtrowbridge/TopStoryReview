<?php

function UserPageSources(){
  $Sources = Query('SELECT * FROM TSRFeedSource');
  pd($Sources);
}
