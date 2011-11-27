<div class="story default-section">
  <h2><?php echo $story->getTitle(); ?></h2>
  <div class="source">
    From: <a target="_blank" href="<?php echo $story->getUrl(); ?>"><?php $story->getSourceHost(); ?></a>
  </div>
  <div class="summary">
    <?php echo truncate_html_text($story->getSummaryHtml(), 450, '...', true, true); ?>
  </div>
  <div class="photos">
    <?php $image = $story->getBiggestImage(); ?>
    <?php if ($image): ?>
      <img src="<?php echo $story->getBiggestImage()->getThumbnailUrl(280, 280, "adaptive"); ?>" />
    <?php endif; ?>
  </div>
</div>
