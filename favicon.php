<?php
    header ("Content-type: image/png");
    const UNKNOWN = ".";
    $gridStr = strip_tags($_GET['grid']);
    $size = (int) $_GET['size'];
    $icon = imagecreate($size, $size);
    $transparent = imagecolorallocate($icon, 1, 1, 1);
    imagecolortransparent($icon, $transparent);
    $gridBorder = imagecolorallocate($icon, 0, 0, 0);
    $known = imagecolorallocate($icon, 102, 102, 255);
    $unknown = imagecolorallocate($icon, 255, 255, 255);
    
    if ($size == 16) {
        ImageLine($icon,  2,  1, 12,  1, $gridBorder);
        ImageLine($icon,  2,  5, 12,  5, $gridBorder);
        ImageLine($icon,  2,  9, 12,  9, $gridBorder);
        ImageLine($icon,  2, 13, 12, 13, $gridBorder);
        ImageLine($icon,  1,  2,  1, 12, $gridBorder);
        ImageLine($icon,  5,  2,  5, 12, $gridBorder);
        ImageLine($icon,  9,  2,  9, 12, $gridBorder);
        ImageLine($icon, 13,  2, 13, 12, $gridBorder);
        $x = 1;
        $y = 0;
        foreach(str_split($gridStr) as $i => $value) {
            $x++;
            if ($i % 3 == 0) $x++;
            if ($i % 9 == 0) {
                $y++;
                $x = 2;
            }
            if ($i % 27 == 0) $y++;
            if ($value == UNKNOWN) $pixelColor = $unknown;
            else $pixelColor = $known;
            ImageSetPixel($icon, $x, $y, $pixelColor);
        }
    } else {
        $boxSize = floor(($size-5) / 9);
        $start = 1;
        $end = 9*$boxSize + 2;
        for ($y=0; $y < $size; $y += 3*$boxSize + 1)
            ImageLine($icon,  $start,  $y, $end,  $y, $gridBorder);
        for ($x=0; $x < $size; $x += 3*$boxSize +1)
            ImageLine($icon,  $x,  $start,  $x, $end, $gridBorder);
        $x = 0;
        $y = 0;
        $boxSizeMinusOne = $boxSize - 1;
        foreach(str_split($gridStr) as $i => $value) {
            if ($i % 3 == 0) $x++;
            if ($i % 27 == 0) $y++;
            if ($value == UNKNOWN) $color = $unknown;
            else $color = $known;
            imagefilledrectangle($icon, $x, $y, $x+$boxSizeMinusOne, $y+$boxSizeMinusOne, $color);
            $x += $boxSize;
            if ($i % 9 == 8) {
                $y += $boxSize;
                $x = 0;
            }
        }
    }
    imagepng($icon);
?>
