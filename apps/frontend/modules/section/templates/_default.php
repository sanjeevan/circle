<div class="story default-section">
  <h2><a target="_blank" href="<?php echo $story->getUrl(); ?>"><?php echo $story->getTitle(); ?></a></h2>
  <div class="summary">
    <?php echo truncate_html_text($story->getSummaryHtml(), 450, '...', true, true); ?>
  </div>
  <div class="photos">
    <?php $image = $story->getBiggestImage(); ?>
    <?php if ($image): ?>
      <img src="<?php echo $story->getBiggestImage()->getThumbnailUrl(280, 280, "normal"); ?>" />
    <?php endif; ?>
  </div>
  <div class="foot">
    from: <a target="_blank" href="<?php echo $story->getUrl(); ?>"><?php echo $story->getSourceHost(); ?></a> / 
    via: <?php echo $story->getVia(); ?> / 
    #<?php echo $story->getId(); ?>
  </div>
</div>
