<div class="container stories-container">
  <div class="row">
    <div class="span10 block bin-0">
      <?php echo $bins[0]['section']->getHtmlFragment("section/wide"); ?>  
    </div>
    <div class="span5">
      <?php echo $bins[1]['section']->getHtmlFragment(); ?> 
    </div>
  </div>

  <div class="row">
    <div class="span5">
      <?php echo $bins[2]['section']->getHtmlFragment(); ?>
    </div>
    <div class="span10"> 
      <?php echo $bins[3]['section']->getHtmlFragment("section/wide"); ?>
    </div>
  </div>

  <div class="row">
    <div class="span5"></div>
    <div class="span5"></div>
    <div class="span5"></div>
  </div>

  <div class="row">
    <div class="span5"></div>
    <div class="span5"></div>
    <div class="span5"></div>
  </div>

  <div class="row">
    <div class="span5"></div>
    <div class="span5"></div>
    <div class="span5"></div>
  </div>

</div>
