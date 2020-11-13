<?php
    require("classes.php");
    session_start();
    $grid = new Grid();
    $grid->generate();
    $currentGrid = $grid->toString();
    $_SESSION[$currentGrid] = "checked";
    header("Location: ".$_SERVER["REQUEST_SCHEME"]."://".$_SERVER["HTTP_HOST"].dirname($_SERVER["DOCUMENT_URI"])."/".$currentGrid);
    exit();
?>
