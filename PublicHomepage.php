<?php

function PublicHomepage(){
	global $Title;
	if(path(0)==false){
		$Title = "Here's The Latest";
	}else{
		
	}
	TemplateBootstrap4($Title,'PublicHomepageBodyCallback();');
}

function PublicHomepageBodyCallback(){
	global $Title;
?>

<div class="container">
	<div class="row">
		<div class="col-xs-12">
			<h1><?php echo $Title; ?></h1>
			
		</div>
	</div>
</div>
<?php
}
