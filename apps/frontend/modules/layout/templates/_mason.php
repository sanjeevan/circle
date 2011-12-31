<div class="container stories-container mason-container">
  <?php foreach ($sections as $section): ?>
    <?php echo $section->getHtmlFragment(); ?>
  <?php endforeach; ?>
</div>


<script type="text/javascript">
  $(document).ready(function(){
    var storiesContainer = $(".stories-container");
    var options = {
      itemSelector: ".story",
      columnWidth: 310
    };
    storiesContainer.masonry(options);
    storiesContainer.imagesLoaded(function(){
      storiesContainer.masonry(options);
    });
  });
</script>


