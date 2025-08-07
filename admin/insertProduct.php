<?php
 require_once "dbconnect.php";

 if(!isset($_SESSION))
 {//session will be opened when it does not exist.
    session_start();
 }
 try{
    $sql="select * from category";
  $stmt=$conn->prepare($sql);
  $stmt->execute();
  $categories=$stmt->fetchAll();

 }catch(PDOException $e){
    echo $e->getMessage();
 }


 if (isset($_POST['insertBtn'])) 
    {
$name=$_POST['name'];
$price=$_POST['price'];
$category=$_POST['category'];
$qty=$_POST['qty'];
$description=$_POST['description'];
$fileImage=$_FILES['productImage'];
// echo $name. "<br>";
// echo $price."<br>";
// echo $category."<br>";
// echo $qty."<br>";
// echo $description."<br>";
// echo $fileImage['name']."<br>";
// echo "$fileImage[size]<br>";
// echo "$fileImage[type]<br>";

$filePath="productImage/".$fileImage['name'];
$status=move_uploaded_file($fileImage['tmp_name'],$filePath);

if($status){
try {// inserting data into database
    //	productID	productName	category	price	description	qty	imgPath	

 $sql ="Insert into product values (?,?,?,?,?,?,?)";
 $stmt=$conn->prepare($sql);
 $flag=$stmt->execute([null,$name, $category, $price, $description, $qty, $filePath]);
 $id=$conn->lastInsertId();
 if ($flag)
 {
    $message="new product with id $id has been inserted succeccfully!";
    $_SESSION["message"] = $message;
    header("Location:viewProduct.php");
 }

} catch(PDOException $e) {
    echo $e->getMessage();
}
} else
{
    echo "file upload fail";
}
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insert Product</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <?php require_once("navbarcopy.php");?>
  </div>
        <div class="row mt-3 mx-auto">
    <div class="col-md-12 mx-auto">
<h4>Insert Product</h4>
            <form class="form border border-dark rounded p-4" action="insertProduct.php" method="post" enctype="multipart/form-data">
                <div class="row px-5">
                <div class="col-md-5 mx-3 px-5">
                    <div class="mb-1">
                    <label for="pname" class="form-label">Product Name</label>
                    <input type="text" class="form-control" name="name">
                </div>

                    <div class="mb-1">
                    <label for="price" class="form-label">Price</label>
                    <input type="number" class="form-control" name="price">
                </div>

                <select name="category" class="form-select">
                    <option value="">Choose Category</option>
                    <?php
                    if(isset($categories)){
                       
                            foreach($categories as $category){
                                echo"<option value=$category[catId]>$category[catName]</option>";
                        } 
                }
                    ?>
                </select>
                </div>

                <div class="col-md-5 mx-3 px-5">
                    <div class="mb-1">
                    <label class="form-label">Quantity</label>
                    <input type="number" class="form-control" name="qty">
                </div>

                    <div class="mb-1">
                        <textarea name="description" id="" class="form-control">

                        </textarea>
                </div>
                <div class="mb-1">
                    <label for="" class="form-label">Choose Product Image</label>
                    <input type="file" class="form-control" name="productImage">
            </div>
            <button type="submit" name="insertBtn"class="btn btn-primary rounded-pill mt-3">Insert Product</button>
                </div>

                </div>
               
         

            </form>
       </div>
 
 </div>
    </div>

    
</body>
</html>