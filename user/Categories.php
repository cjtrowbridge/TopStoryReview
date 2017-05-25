<?php

function UserPageCategories(){
  $Categories = Query("SELECT * FROM FeedCategory");
  foreach($Categories as $Category){
    ?>
    
    <div class="category">
      <h3><a href="/category/<?php echo $Category['FeedCategoryID']; ?>"><?php echo $Category['Name']; ?></a></h3>
      <p><?php echo $Category['Description']; ?></p>
      <p><?php echo $Category['Path']; ?></p>
      <p><?php echo $Category['ParentID']; ?></p>
    </div>
    
    <?php
  }
}
