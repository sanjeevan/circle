<?php

class ParameterHolder
{
  protected $params = array();

  public function hasParameter($key)
  {
    return isset($params[$key]);
  }

  public function setParameter($key, $value)
  {
    $this->params[$key] = $value;
  }

  public function getParameter($key)
  {
    return $this->params[$key];
  }

  public function getParameters()
  {
    return $this->params;
  }

  public function clear()
  {
    $this->params = array();
  }
}
