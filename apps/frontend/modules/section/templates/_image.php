<div class="story image-section">
  <h2><a target="_blank" href="<?php echo $story->getUrl(); ?>"><?php echo $story->getTitle(); ?></a></h2>
  <div class="photos">
    <?php if ($image): ?> 
      <?php if ($image->getMetaWidth() > 0): ?>
        <img src="<?php echo $image->getThumbnailUrl(270, 280, "adaptive"); ?>" />
      <?php else: ?>
        <!-- <img src="<?php echo $image->getUrl(); ?>" /> -->
      <?php endif; ?>
    <?php endif; ?>
  </div>
  <div class="foot">
    <?php echo $image->getMetaWidth() . "x" . $image->getMetaHeight(); ?>px /  
    from: <a target="_blank" href="<?php echo $story->getUrl(); ?>"><?php echo $story->getSourceHost(); ?></a> / 
    via: <?php echo $story->getVia(); ?> / 
    #<?php echo $story->getId(); ?>
  </div>
</div>
