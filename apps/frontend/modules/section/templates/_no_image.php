<div class="story no-image-section">
  <h2><a target="_blank" href="<?php echo $story->getUrl(); ?>"><?php echo $story->getTitle(); ?></a></h2>
  <div class="source">
    <span class="label">Source:</span> <a target="_blank" href="<?php echo $story->getUrl(); ?>"><?php echo $story->getSourceHost(); ?></a>
  </div>
  <div class="summary">
    <?php echo truncate_html_text($story->getSummaryHtml(), 450, '...', true, true); ?>
  </div>
</div>
