<?php
    require("classes.php");
    session_start();
    if (isset($_SESSION["currentGrid"]))
        $currentGrid = $_SESSION["currentGrid"];
    else
        $currentGrid = ".528.3....4.9.1...39.562......73.129...1.64.7...42.3656.13.5...28.6.4...4.5287...";
    header ("Content-type: image/png");
    if (isset($_GET['size']))
        $size = (int) $_GET['size'];
    else
        $size = 196;

    $thumbnail = imagecreate($size, $size);
    $transparent = imagecolorallocate($thumbnail, 1, 1, 1);
    imagecolortransparent($thumbnail, $transparent);
    $black = imagecolorallocate($thumbnail, 0, 0, 0);
    $grey = imagecolorallocate($thumbnail, 128, 128, 128);
    $blue = imagecolorallocate($thumbnail, 102, 102, 255);
    $white = imagecolorallocate($thumbnail, 255, 255, 255);
    
    if ($size <= 36) {
        $boxSize = floor(($size-4) / 9);
        $gridSize = 9*$boxSize + 4;
        $start = floor(($size-$gridSize) / 2);
        $end = $start + $gridSize;
        $lineStart = $start + 1;
        $lineEnd = $end - 2;
        for ($i = $start; $i < $end; $i += 3*$boxSize + 1) {
            ImageLine($thumbnail,  $lineStart,  $i, $lineEnd,  $i, $black);
            ImageLine($thumbnail,  $i,  $lineStart,  $i, $lineEnd, $black);
        }
        $x = $start;
        $y = $start;
        $boxSizeMinusOne = $boxSize - 1;
        foreach(str_split($currentGrid) as $i => $value) {
            if ($i % 3 == 0) $x++;
            if ($i % 27 == 0) $y++;
            if ($value == UNKNOWN) {
                $bgColor = $white;
            } else {
                $bgColor = $blue;
            }
            imagefilledrectangle($thumbnail, $x, $y, $x+$boxSizeMinusOne, $y+$boxSizeMinusOne, $bgColor);
            $x += $boxSize;
            if ($i % 9 == 8) {
                $y += $boxSize;
                $x = $start;
            }
        }
    } else if ($size < 82) {
        $boxSize = floor(($size-1) / 9);
        $gridSize = 9*$boxSize + 1;
        $start = floor(($size-$gridSize) / 2);
        $end = $start + $gridSize;
        $lineStart = $start + 1;
        $lineEnd = $end - 2;
        for ($i = $start + $boxSize; $i < $end - $boxSize; $i += $boxSize) {
            ImageLine($thumbnail,  $lineStart,  $i, $lineEnd,  $i, $grey);
            ImageLine($thumbnail,  $i,  $lineStart,  $i, $lineEnd, $grey);
        }
        for ($i = $start; $i < $end; $i += 3*$boxSize) {
            ImageLine($thumbnail,  $lineStart,  $i, $lineEnd,  $i, $black);
            ImageLine($thumbnail,  $i,  $lineStart,  $i, $lineEnd, $black);
        }
        $x = $start + 1;
        $y = $start + 1;
        $boxSizeMinusTwo = $boxSize - 2;
        foreach(str_split($currentGrid) as $i => $value) {
            if ($value == UNKNOWN) {
                $bgColor = $white;
            } else {
                $bgColor = $blue;
            }
            imagefilledrectangle($thumbnail, $x, $y, $x+$boxSizeMinusTwo, $y+$boxSizeMinusTwo, $bgColor);
            $x += $boxSize;
            if ($i % 9 == 8) {
                $y += $boxSize;
                $x = $start + 1;
            }
        }
    } else {
        $boxSize = floor(($size-1) / 9);
        $gridSize = 9*$boxSize + 1;
        $start = floor(($size-$gridSize) / 2);
        $end = $start + $gridSize;
        $lineStart = $start + 1;
        $lineEnd = $end - 2;
        $fontSize = floor($boxSize/2) - 4;
        $fdx = floor(($boxSize - imagefontwidth($fontSize)) / 2);
        $fdy = ceil(($boxSize - imagefontheight($fontSize)) / 2) - 1;
        $fontColor = $white;
        for ($i = $start + $boxSize; $i < $end - $boxSize; $i += $boxSize) {
            ImageLine($thumbnail,  $lineStart,  $i, $lineEnd,  $i, $grey);
            ImageLine($thumbnail,  $i,  $lineStart,  $i, $lineEnd, $grey);
        }
        for ($i = $start; $i < $end; $i += 3*$boxSize) {
            ImageLine($thumbnail,  $lineStart,  $i, $lineEnd,  $i, $black);
            ImageLine($thumbnail,  $i,  $lineStart,  $i, $lineEnd, $black);
        }
        $x = $start + 1;
        $y = $start + 1;
        $boxSizeMinusTwo = $boxSize - 2;
        foreach(str_split($currentGrid) as $i => $value) {
            if ($value == UNKNOWN) {
                $bgColor = $white;
            } else {
                $bgColor = $blue;
            }
            imagefilledrectangle($thumbnail, $x, $y, $x+$boxSizeMinusTwo, $y+$boxSizeMinusTwo, $bgColor);
            if ($value != UNKNOWN) imagestring($thumbnail, $fontSize, $x + $fdx, $y + $fdy, $value, $fontColor);
            $x += $boxSize;
            if ($i % 9 == 8) {
                $y += $boxSize;
                $x = $start + 1;
            }
        }
    }
    imagepng($thumbnail);
?>
