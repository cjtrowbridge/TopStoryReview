<?php

function UserPageSources(){
  $Sources = Query('SELECT * FROM FeedSource');
  foreach($Sources as $Source){
    ?>
    
    <div class="source">
      <h3><a href="/source/<?php echo $Source['SourceID']; ?>"><?php echo $Source['Name']; ?></a></h3>
      <p><?php echo $Source['Description']; ?></p>
    </div>
    
    <?php
  }
}
