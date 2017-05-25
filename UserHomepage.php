<?php

function UserHomepageBodyCallback(){
  include('user/UnimportantWords.php');
  include('user/Sources.php');
  include('user/Categories.php');
  ?>

<div class="container">
  <div class="row no-gutters">
    <div class="col-md-12">
      <h1>Control Center</h1>
      
      <h2>Unimportant Words</h2>
      <?php UserPageUnimportantWords(); ?>
      
      <h2>Sources</h2>
      <?php UserPageSources(); ?>
      
      <h2>Categories</h2>
      <?php UserPageCategories(); ?>
      
    </div>
  </div>
</div>

  <?php
}
