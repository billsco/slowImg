<?php

/**
 * Small script to serve images slowly.  Useful when locally testing
 * a "loading" routine.
 *
 * Use by invoking URL with parameters "name=filename" and "time=timeInMilliseconds".
 * The filename should be in your PWD.
 *
 * May want to add "scream.enabled = off" to your php.ini file
 */

@$fileName = $_GET["name"];
@$totalTime = $_GET["time"];

if(!$fileName || !$totalTime) {
  echo "Usage: " . $_SERVER["PHP_SELF"] . "?name=fileName&time=timeInMilliseconds";
  die();
}

$CHUNK_SZ=128;

@$handle = fopen($fileName, "rb");

if(!$handle) {
  header( "HTTP/1.0 404 Not Found");
  echo "File " . $fileName . " not found";
  die();
}
else {

  set_time_limit(0);

  $pathParts = pathinfo($fileName);

  $fileSz = filesize($fileName);

  switch(strtolower($pathParts["extension"])) {
    case "gif":
      header("Content-type: image/gif");
      break;
    case "jpg":
    case "jpeg":
      header("Content-type: image/jpeg");
      break;
    case "png":
      header("Content-type: image/png");
      break;
  }
  header("Expires: Mon, 1 Jan 2099 05:00:00 GMT");
  header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
  header("Cache-Control: no-store, no-cache, must-revalidate");
  header("Cache-Control: post-check=0, pre-check=0", false);
  header("Pragma: no-cache");
  header("Content-Length: " . $fileSz);

  $buf = "";

  $numReads = round( ($fileSz/$CHUNK_SZ), 0, PHP_ROUND_HALF_DOWN);
  $sleepTime = round( $totalTime/$numReads) * 1000;

  while(!feof($handle)) {
    $buf = fread($handle, 128);
    echo $buf;
    ob_flush();
    flush();
    usleep($sleepTime );
  }
  @fclose($handle);

}

?>
