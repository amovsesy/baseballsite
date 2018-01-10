<?php

// Inialize session
if(!isset($_SESSION))
{
  session_start();
}

if (!isset($_SESSION['playerid'])) 
{
  header('Location: index.php');
}

require_once('game.php');

function getExtension($str)
{
  $i = strrpos($str,".");
  if (!$i) { return ""; }
  $l = strlen($str) - $i;
  $ext = substr($str,$i+1,$l);
  return $ext;
}

$id = $_POST['gameid'];
$numimages = $_POST['numimages'];
$goback = $_POST['goback'];

foreach($_FILES as $file)
{
  $filename = $file['name'];
  
  if($filename)
  {
    $extension = getExtension($filename);
    $extension = strtolower($extension);
    
    if (($extension != "jpg") && ($extension != "jpeg") && ($extension != "png") && ($extension != "gif"))
    {
      $_SESSION['error'] = "There were errors uploading some of your files.";
    }
    else
    {
      $size=filesize($file['tmp_name']);
      
      if ($size > MAX_SIZE*1024)
      {
        $_SESSION['error'] = "There were errors uploading some of your files.";
      }
      else 
      {
        $image_name=time().'_'.(substr($filename,0,strrpos($filename,"."))).'.'.$extension;
        $newname="images/".$image_name;
        $copied = copy($file['tmp_name'], $newname);
        
        if (!$copied)
        {
          $_SESSION['error'] = "There were errors uploading some of your files.";
        }
        else 
        {
          addImageToGame($id, $newname);
        }
      }
    }
  }
  
  unset($extension);
  unset($filename);
  unset($newname);
  unset($copied);
  unset($size);
}

if($goback == "gamepics")
{
  header('Location: gamepictures.php?id='.$id);
}
else 
{
  header('Location: pictures.php');
}

?>