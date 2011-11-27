<?php

class MediaEasyListBlockFilter extends MediaFilter
{
  protected $easy_list_rules = array();

  public function __construct()
  {
    $filepath = sfConfig::get("sf_root_dir") . "/data/easylist.txt";
    if (!is_readable($filepath)) {
      throw new Exception("Cannot read " . $filepath);
    }

    $rules = file($filepath, FILE_SKIP_EMPTY_LINES 
                                             | FILE_IGNORE_NEW_LINES);
    
    $this->easy_list_rules = $this->normalizeRules($rules);
  }

  protected function normalizeRules($rules = array())
  {
    $temp = array();
    $rules = array_slice($rules, 1);

    foreach ($rules as $rule) {
      if ($rule[0] == "!") {
        continue;
      }
      if (strpos($rule, "#") !== false) {
        continue;
      }
      $temp[] = $rule;
    }

    return $temp;
  }

  public function canKeep(File $file)
  {
    $file_to_url = Doctrine_Query::create()
      ->select("u.*")
      ->from("FileToUrl u")
      ->where("u.file_id = ?", $file->getId())
      ->fetchOne();

    foreach ($this->easy_list_rules as $rule) {
      $regex = preg_quote($rule);
      if (preg_match("#{$regex}#is", $file_to_url->getUrl())) {
        echo "Matched {$rule} with url: {$file_to_url->getUrl()}\n";
        return false;
      }
    }

    return true;
  }
}
