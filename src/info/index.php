/**
* @author Sergio Gamboa
*/

<?php
class Info {
    public $width = "";
    public $height = "";
    public $strategies = "";
}

$u = new Info();
$u->width = 7;
$u->height = 6;
$u->strategies = array("Random", "Smart");
echo json_encode($u);
?>