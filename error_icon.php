<?php
// Create a 70x70 image
$img = imagecreatetruecolor(70, 70);
$bg = imagecolorallocate($img, 200, 200, 200);
$textColor = imagecolorallocate($img, 80, 80, 80);
imagefill($img, 0, 0, $bg);
imagestring($img, 5, 15, 25, 'App Icon', $textColor);
imagepng($img, 'error_icon.png');
imagedestroy($img);
echo "Error icon created successfully.";
?>