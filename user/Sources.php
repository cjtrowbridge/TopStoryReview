<?php

function UserPageSources(){
  $Sources = Query('SELECT * FROM TSRFeedSource');
  foreach($Sources as $Source){
    ?>
    
    <div class="source">
      <?php pd($Source); ?>
    </div>
    
    <?php
  }
}
