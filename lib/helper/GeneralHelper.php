<?php

/**
 * Get image tag to a static google maps image
 * 
 * @param Place $place
 * @param unknown_type $options
 */
function static_maps_image_tag($place, $options = array())
{
  if (!isset($options["size"])) {
    $options["size"] = "80x80";
  }
  if (!isset($options["zoom"])) {
    $options["zoom"] = 13;
  }
  
  $center = $place->getGeoLatitude() . "," . $place->getGeoLongitude();
  
  $uri = "http://maps.googleapis.com/maps/api/staticmap?center=#center#&zoom=#zoom#&size=#size#&sensor=false&q=#query#";
  $uri = str_replace("#center#", $center, $uri);
  $uri = str_replace("#size#", $options["size"], $uri);
  $uri = str_replace("#zoom#", $options["zoom"], $uri);
  
  $uri.= "&markers=color:red|" . $center;
  $img = image_tag($uri, array("class" => "google-maps-tile"));
  
  $address = $place->getFormattedAddress() ? $place->getFormattedAddress() : $place->getName();
  $href = "http://maps.google.com/maps?hl=en&ll={$center}&z={$options['zoom']}";
  $href.= "&q=" . urlencode($address);
    
  if (isset($options["linkify"])) {
    return link_to($img, $href, array("target" => "_blank"));
  } else if (isset($options["urls_only"])) {
    return array("img_url" => $uri, "url" => $href);
  } else {
    return $img;  
  }
}

/**
 * Returns an array containing formatted strings for start/stop dates and
 * start/stop times
 * 
 * @param EventToDate $event_to_date
 */
function event_dates_formatted(EventToDate $event_to_date, $tz = "UTC")
{
  $data = array(
    "date" => false,
    "time" => false
  );
  
  $start_date = $event_to_date->getLocalStartDate($tz);
  $end_date = $event_to_date->getLocalEndDate($tz);
  
  // start/end dates
  $sd_time = date("l jS F Y", strtotime($start_date));
  $ed_time = date("l jS F Y", strtotime($end_date));
  
  // start/end times
  $st_time = date("H:i", strtotime($start_date));
  $et_time = date("H:i", strtotime($end_date));
  
  if ($sd_time == $ed_time || $event_to_date->getHasEndDate() == false){
    $data["date"] = $sd_time;
  } else {
    $data["date"] = $sd_time . " to " . $ed_time;
  }
  
  if ($event_to_date->getIsAllDay()){
    $data["time"] = "All day";
  } else {
    if ($st_time == $et_time || $event_to_date->getHasEndDate() == false) {
      $data["time"] = $st_time;
    } else {
      $data["time"] = "{$st_time} to {$et_time}";
    }
  }
  
  return $data;
}

/**
 * Link to user
 * 
 * @param sfGuardUser $user
 * @param array $options
 */
function link_to_user($user, $options = array())
{
  if ($user instanceof sfGuardUser){
    $name = trim($user->getFullName());
    return link_to($name, "@member?username={$user->getUsername()}", $options);
  }
}

/**
 * Get the image tag for an Event's photo
 * 
 * @param Event $event
 * @return string
 */
function event_image_tag(Event $event, $options = array())
{
  $options["src"] = $event->getPhotoUrl($options);
  
  if (isset($options['size'])){
    list($options['width'], $options['height']) = explode('x', $options['size'], 2);
    unset($options['size']);
  }
  
  return tag("img", $options);
}

/**
 * Get the image tag for a user photo
 * 
 * @param sfGuardUser $user
 * @return string
 */
function user_image_tag($user, $options = array())
{
  $options["src"] = $user->getPhotoUrl($options);
  
  if (isset($options['size'])){
    list($options['width'], $options['height']) = explode('x', $options['size'], 2);
    unset($options['size']);
  }
  
  return tag("img", $options);
}

/**
* Truncate a html text to a new size, closing any open tags
*
* @param $text
* @param $length
* @param $ending
* @param $exact
* @param $considerHtml
*/
function truncate_html_text($text, $length = 100, $ending = '...', $exact = true, $considerHtml = false) 
{
  if ($considerHtml) {
    // if the plain text is shorter than the maximum length, return the whole text
    if (strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
      return $text;
    }
    // splits all html-tags to scanable lines
    preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
    $total_length = strlen($ending);
    $open_tags = array();
    $truncate = '';
    foreach ($lines as $line_matchings) {
      // if there is any html-tag in this line, handle it and add it (uncounted) to the output
      if (!empty($line_matchings[1])) {
        // if it's an "empty element" with or without xhtml-conform closing slash (f.e. <br/>)
        if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
          // do nothing
          // if tag is a closing tag (f.e. </b>)
        } else if (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
          // delete tag from $open_tags list
          $pos = array_search($tag_matchings[1], $open_tags);
          if ($pos !== false) {
            unset($open_tags[$pos]);
          }
          // if tag is an opening tag (f.e. <b>)
        } else if (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
          // add tag to the beginning of $open_tags list
          array_unshift($open_tags, strtolower($tag_matchings[1]));
        }
        // add html-tag to $truncate'd text
        $truncate .= $line_matchings[1];
      }
      // calculate the length of the plain text part of the line; handle entities as one character
      $content_length = strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
      if ($total_length+$content_length> $length) {
        // the number of characters which are left
        $left = $length - $total_length;
        $entities_length = 0;
        // search for html entities
        if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
          // calculate the real length of all entities in the legal range
          foreach ($entities[0] as $entity) {
            if ($entity[1]+1-$entities_length <= $left) {
              $left--;
              $entities_length += strlen($entity[0]);
            } else {
              // no more characters left
              break;
            }
          }
        }
        $truncate .= substr($line_matchings[2], 0, $left+$entities_length);
        // maximum lenght is reached, so get off the loop
        break;
      } else {
        $truncate .= $line_matchings[2];
        $total_length += $content_length;
      }
      // if the maximum length is reached, get off the loop
      if($total_length>= $length) {
        break;
      }
    }
  } else {
    if (strlen($text) <= $length) {
      return $text;
    } else {
      $truncate = substr($text, 0, $length - strlen($ending));
    }
  }
  // if the words shouldn't be cut in the middle...
  if (!$exact) {
    // ...search the last occurance of a space...
    $spacepos = strrpos($truncate, ' ');
    if (isset($spacepos)) {
      // ...and cut the text in this position
      $truncate = substr($truncate, 0, $spacepos);
    }
  }
  // add the defined ending to the text
  $truncate .= $ending;
  if($considerHtml) {
    // close all unclosed html-tags
    foreach ($open_tags as $tag) {
      $truncate .= '</' . $tag . '>';
    }
  }
  return $truncate;
}
