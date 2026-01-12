<?php

class NotePage extends Page
{
  public function url($options = null): string
  {
    return site()->url() . '/' . $this->slug();
  }
}
