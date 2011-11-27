<div class="topbar">
  <div class="fill">
    <div class="container">
      <a href="#" class="brand">Circle</a>
      <ul class="nav">
        <li class="active"><a href="#">Home</a></li>
      </ul>
    </div>
  </div>
</div>


<div class="container">
  <?php $idx = 1; ?>
  <?php foreach ($sections as $section): ?>
    
    <?php if ($idx == 1): ?> 
      <div class="row">
    <?php endif; ?>

    <div class="span5">
      <?php echo $section->getHtmlFragment(); ?>
    </div>

    <?php if ($idx % 3 == 0): ?> 
      </div>
      <div class="row">
    <?php endif; ?>

    <?php $idx++; ?>

  <?php endforeach; ?>
    </div>
</div>
