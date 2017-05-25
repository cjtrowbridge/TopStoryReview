<?php
 
Hook('Template Head','TopStoryReviewTemplateHead();');
function TopStoryReviewTemplateHead(){
  ?>
  <link rel="stylesheet" href="/plugins/TopStoryReview/style.css">
  <?php
}


Hook('User Is Not Logged In - Before Presentation','PublicPageBefore();');
function PublicPageBefore(){
  $Categories = Query("SELECT * FROM FeedCategory");
  foreach($Categories as $Category){
    if($Category['ParentID']==''){
      
      //Add Any Children
      $Children = array();
      
      foreach($Categories as $Child){
        if($Child['ParentID']==$Category['FeedCategoryID']){
          $Children[]=array(
            'text' => $Child['Name'],
            'link' => $Child['Path']
          );
        }
      }
      if(count($Children)>0){
       array_unshift($Children, array(
          'text' => $Category['Name'],
          'link' => '/'.$Category['Path']
        ), array(
          'text' => 'divider',
          'link' => ''
        ));

        $Type = 'dropdown';
      }else{
        $Type = 'link';
      }
      $Text = $Category['Name'];
      $Path = '/'.$Category['Path'];
      
      Nav('main-not-logged-in',$Type,$Text,$Path,$Children);
    }
  }
  //Nav('main-not-logged-in','link','Explore','/explore');
  //Nav('main-not-logged-in','link','Login','/login');
}


Hook('User Is Not Logged In - Presentation','PublicPage();');
function PublicPage(){
  switch(path(0)){
    case 'login':
      PromptForLogin();
      break;
    case 'explore':
      include('PublicExplore.php');
      TemplateBootstrap4('explore','PublicExploreBodyCallback();');
      break;
    default:
      include('PublicHomepage.php');
      TemplateBootstrap4('Home','PublicHomepageBodyCallback();');
      break;
  }
}


Hook('User Is Logged In - Before Presentation','UserPageBefore();');

function UserPageBefore(){
  //Nav('main-logged-in','link','Explore','/explore');
}


Hook('User Is Logged In - Presentation','UserPage();');

function UserPage(){
  switch(path(0)){
    case 'source':
      include('source/Source.php');
      TemplateBootstrap4('Source','UserSourceBodyCallback();');
      break;
    default:
      include('UserHomepage.php');
      TemplateBootstrap4('Home','UserHomepageBodyCallback();');
      break;
  }
  
}
