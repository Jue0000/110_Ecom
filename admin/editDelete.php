<?php
require_once "dbconnect.php";
if(!isset($_SESSION))
{
session_start();
}
if(isset($_GET['eid']))
{
echo "edit button clicked";

}else if(isset($_GET["did"]))
{
try{
    $productID= $_GET["did"];
$sql="delete from product where productID=?";
$stmt=$conn->prepare($sql);
$status=$stmt->execute([$productID]);
if($status)
{
    $_SESSION['deleteSuccess']="product ID $productID has been deleted.";
    header("Location:viewProduct.php");
}
} catch(PDOException $e) {
    echo $e->getMessage();
}

}


?>
