<?php
    require("classes.php");
    if (isset($_GET["grid"]))
        $currentGrid = $_GET["grid"];
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
    $darkerBorder = imagecolorallocate($thumbnail, 150, 155, 160);
    $lighterBorder = imagecolorallocate($thumbnail, 210, 225, 230);
    $emptyBoxBC = imagecolorallocate($thumbnail, 255, 255, 255);
    $clueBC = imagecolorallocate($thumbnail, 255, 255, 255);
    $clueFC = imagecolorallocate($thumbnail, 150, 155, 160);
    
    if ($size <= 36) {
        $boxSize = floor(($size-4) / 9);
        $gridSize = 9*$boxSize + 4;
        $start = floor(($size-$gridSize) / 2);
        $end = $start + $gridSize;
        $lineStart = $start + 1;
        $lineEnd = $end - 2;
        for ($i = $start; $i < $end; $i += 3*$boxSize + 1) {
            ImageLine($thumbnail,  $lineStart,  $i, $lineEnd,  $i, $darkerBorder);
            ImageLine($thumbnail,  $i,  $lineStart,  $i, $lineEnd, $darkerBorder);
        }
        $x = $start;
        $y = $start;
        $boxSizeMinusOne = $boxSize - 1;
        foreach(str_split($currentGrid) as $i => $value) {
            if ($i % 3 == 0) $x++;
            if ($i % 27 == 0) $y++;
            if ($value == UNKNOWN) {
                $bgColor = $emptyBoxBC;
            } else {
                $bgColor = $clueFC;
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
            ImageLine($thumbnail,  $lineStart,  $i, $lineEnd,  $i, $lighterBorder);
            ImageLine($thumbnail,  $i,  $lineStart,  $i, $lineEnd, $lighterBorder);
        }
        for ($i = $start; $i < $end; $i += 3*$boxSize) {
            ImageLine($thumbnail,  $lineStart,  $i, $lineEnd,  $i, $darkerBorder);
            ImageLine($thumbnail,  $i,  $lineStart,  $i, $lineEnd, $darkerBorder);
        }
        $x = $start + 1;
        $y = $start + 1;
        $boxSizeMinusTwo = $boxSize - 2;
        foreach(str_split($currentGrid) as $i => $value) {
            if ($value == UNKNOWN) {
                $bgColor = $emptyBoxBC;
            } else {
                $bgColor = $clueFC;
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
        $fontColor = $emptyBoxBC;
        for ($i = $start + $boxSize; $i < $end - $boxSize; $i += $boxSize) {
            ImageLine($thumbnail,  $lineStart,  $i, $lineEnd,  $i, $lighterBorder);
            ImageLine($thumbnail,  $i,  $lineStart,  $i, $lineEnd, $lighterBorder);
        }
        for ($i = $start; $i < $end; $i += 3*$boxSize) {
            ImageLine($thumbnail,  $lineStart,  $i, $lineEnd,  $i, $darkerBorder);
            ImageLine($thumbnail,  $i,  $lineStart,  $i, $lineEnd, $darkerBorder);
        }
        $x = $start + 1;
        $y = $start + 1;
        $boxSizeMinusTwo = $boxSize - 2;
        foreach(str_split($currentGrid) as $i => $value) {
            if ($value == UNKNOWN) {
                $bgColor = $emptyBoxBC;
            } else {
                $bgColor = $clueBC;
            }
            imagefilledrectangle($thumbnail, $x, $y, $x+$boxSizeMinusTwo, $y+$boxSizeMinusTwo, $bgColor);
            if ($value != UNKNOWN) imagestring($thumbnail, $fontSize, $x + $fdx, $y + $fdy, $value, $clueFC);
            $x += $boxSize;
            if ($i % 9 == 8) {
                $y += $boxSize;
                $x = $start + 1;
            }
        }
    }
    imagepng($thumbnail);
?>
