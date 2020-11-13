<?php
    require("classes.php");
    
    $grid = new Grid();
    $grid->generate();
    header("Location: " . $_SERVER["REQUEST_SCHEME"] . "://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["DOCUMENT_URI"]) . "/" . $grid->toString());
    exit();
?>
