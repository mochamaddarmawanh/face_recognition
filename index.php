<?php

error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));

include('models/global/base_url.php');

$pages = json_decode(file_get_contents('models/global/pages.json'), true);
$uri_segments = explode('/', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

if ($uri_segments[2] !== "") {
  if (in_array($uri_segments[2], array_column($pages['page'], 0))) {
      $title = $pages['page'][array_search($uri_segments[2], array_column($pages['page'], 0))][1];
      include "models/_$uri_segments[2].php";
      include "views/_top.php";
      include "views/$uri_segments[2].php";
      include "views/_bot.php";
  } else {
      $title = "404 Page Not Found";
      include "views/_top.php";
      include "views/404.php";
      include "views/_bot.php";
  }
} else {
  $title = "About Teachable Machine";
  include "views/_top.php";
  include "views/about.php";
  include "views/_bot.php";
}