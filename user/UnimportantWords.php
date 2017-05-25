<?php

function UserPageUnimportantWords(){
  $Words = Query("SELECT * FROM UnimportantWords");
  foreach($Words as $Word){
    ?>
      
      <div class="word">
        <h3><a href="/word/<?php echo $Word['WordID']; ?>"><?php echo $Word['Word']; ?></a></h3>
      </div>
      
    <?php
  }
}
