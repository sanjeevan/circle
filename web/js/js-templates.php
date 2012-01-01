<?php
  header("Content-type: application/javascript");
  $pattern = dirname(__FILE__) . "/app/templates/*";
  $files = array();
  $newlines = array("\r\n", "\n", "\r");

  foreach (glob($pattern) as $path) {
    $content = file_get_contents($path);
    $files[basename($path)] = str_replace($newlines, "", $content);
  }

  function esc_js_no_entities($value)
  {
    return str_replace(array("\\"  , "\n"  , "\r" , "\""  , "'"  ),
                       array("\\\\", "\\n" , "\\r", "\\\"", "\\'"),
                       $value);
  }
?>
var JST = [];
<?php foreach ($files as $name => $content): ?>
JST["<?php echo $name; ?>"] = "<?php echo esc_js_no_entities($content); ?>";
<?php endforeach; ?>
