<div class="story default-section image-only">
  <h2><?php echo $story->getTitle(); ?></h2>
  <div class="source">
    <span class="label">Source:</span> <a target="_blank" href="<?php echo $story->getUrl(); ?>"><?php echo $story->getSourceHost(); ?></a>
  </div>
  <div class="photos">
    <?php if ($image): ?> 
      <?php if ($image->getMetaWidth() > 0): ?>
        <img src="<?php echo $image->getThumbnailUrl(280, 280, "adaptive"); ?>" />
      <?php else: ?>
        <!-- <img src="<?php echo $image->getUrl(); ?>" /> -->
      <?php endif; ?>
    <?php endif; ?>
  </div>
</div>
