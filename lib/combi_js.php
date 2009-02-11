<?php

define('COMBI_FILENAME', 'js/the_beast.js');
define('JSMIN_COMPRESS', true);
define('JSMIN_COMMENTS', "Just A Grip of Code Compiled From Various Files\n// Check http://code.google.com/p/chrome-canopy/source/browse/ for something more human readable ;)");

// files to merge
$files = array(
    'js/the_bones.js',
    'js/Vec2.js',
    'js/Rect2.js',
    'js/Brush.js',
    'js/Leaf.js',
    'js/Branch.js',
    'js/the_meat.js',
    'js/the_ui.js',
    
    'js/lib/jquery-1.3.js'
);

function write($sFilename, $sCode)
{
    $oFile = fopen($sFilename, 'w');
    if(flock($oFile, LOCK_EX)) {
        fwrite($oFile, $sCode);
        flock($oFile, LOCK_UN);
    }
    fclose($oFile);
}

$libRoot = dirname(__FILE__);
$wwwRoot = dirname($libRoot)."/www";

$combiPath = $wwwRoot.'/'.COMBI_FILENAME;

if(file_exists($combiPath))
{
    $combiModified = filemtime($wwwRoot.'/'.COMBI_FILENAME);
    $lastModifieds = array();
    foreach($files as $file)
        $lastModifieds[] = filemtime("$wwwRoot/$file");
    rsort($lastModifieds);
}

if(!isset($lastModifieds) || $lastModifieds[0] > $combiModified)
{
    $combiCode = '';
    foreach($files as $file) {
        $combiCode .= "\n//\\/\\/\\/\\/ from ".$file." \\/\\/\\/\\/\\\\\n\n";
        $combiCode .= file_get_contents("$wwwRoot/$file")."\n";
    }
    
    write($combiPath, $combiCode);
    
    if(JSMIN_COMPRESS) {
        if(JSMIN_COMMENTS != '')
            $combiCode = shell_exec($libRoot."/jsmin < $combiPath '".JSMIN_COMMENTS."'");
        else
            $combiCode = shell_exec($libRoot."/jsmin < $combiPath");
        
        write($combiPath, $combiCode);
    }
}

echo COMBI_FILENAME;

?>
