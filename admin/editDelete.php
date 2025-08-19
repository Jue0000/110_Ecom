<?php
require_once "dbconnect.php";
if (!isset($_SESSION)) {
    session_start();
}
try {
    $sql = "select * from category";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $categories = $stmt->fetchAll();
} catch (PDOException $e) {
    echo $e->getMessage();
}
if (isset($_GET['eid'])) { // true when edit button is clicked
    $productID = $_GET['eid'];
    try {
        $sql = "SELECT p.productID,p.productName,
c.catName, p.category,
p.price, p.description,
p.qty, p.imgPath
from product p, category c
where p.category =c.catID AND
p.productID=?";

        $statement = $conn->prepare($sql);
        $statement->execute([$productID]);
        $product = $statement->fetch();
        $_SESSION['product'] = $product;
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
} else if (isset($_GET["did"])) {
    try {
        $productID = $_GET["ditd"];
        $sql = "delete from product where productID=?";
        $stmt = $conn->prepare($sql);
        $status = $stmt->execute([$productID]);
        if ($status) {
            $_SESSION['deleteSuccess'] = "product ID $productID has been deleted.";
            header("Location:viewProduct.php");
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}
else if (isset ($_POST['updateBtn']))
{ $productName =$_POST['pname'];
    $category=$_POST['category'];
    $price=$_POST['price'];
    $description=$_POST['description'];
    $qty=$_POST['qty'];
    $fileImg=$_FILES['file'];//size, type, name, tmp_name

    //specify directory or folder to store product image 
    $filePath="productImage/$fileImg[name]";
    $status=move_uploaded_file($fileImg['tmp_name'],$filePath);

    if ($status == true)
    {
        try{ $pid=$_POST['pid'];
            // DML data manipulate language, update, delete, insert
            $sql="update product set productName=?, category=?, price=?, qty=?, description=?, imgPath=? where productID=?";
            $stmt=$conn->prepare($sql);
            $status=$stmt->execute([$productName, $category, $price,$qty,$description,$filePath, $pid]);

            if($status)
            {$_SESSION['updateMessage']="Product with product id $pid is updated!!!";
                header("Location:viewProduct.php");
            }

        }catch(PDOException $e)
        {
            echo $e-> getMessage();
        }
    }

}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>

</head>

<body>

    <div class="container-fluid">
        <div class="row">
            <?php require_once "navbarcopy.php"; ?>
        </div>
        <div class="row">
            <div class="col-md-2">

            </div>
            <div class="col-md-10">
                
                    
                        <form action="editDelete.php" class="form card p-4" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="pid" value="<?php echo $product['productID']; ?>">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="pname" class="form-label">Product Name</label>
                                <input type="text" class="form-control" name="pname" id="pname"
                                    value="<?php if (isset($product)) {
                                                echo $product['productName'];
                                            }
                                            ?>">


                            </div>

                            <div class="mb-3">
                                <p class="alert alert-info"><?php echo "Previous selected category $product[catName]"; ?></p>
                                <select name="category" id="category" class="form-select">
                                    <option value="0">Choose Category</option>
                                    <?php
                                    if (isset($categories)) {
                                        foreach ($categories as $category) {
                                            echo "<option value=$category[catId]> $category[catName] </option>";
                                        }
                                    }
                                    ?>

                                </select>

                            </div>
                            <div class="mb-2">
                                <label for="price" class="form-label">Price</label>
                                <input type="number" id="price"class="form-control" name="price" value="<?php
                                            if (isset($product)) {
                                                echo $product['price'];
                                            } ?>">
                            </div>

                        </div>

                        <div class="col-md-4">
                            <div class="mb-2">
                                <label for="desc" class="form-label">Description</label>
                                <textarea name="description" id="desc" class="form-control" placeholder="write description here">

                                </textarea>
                            </div>
                            <div class="mb-2">
                                <label for="qty" class="form-label">Quantity</label>
                                <input type="number" class="form-control" name="qty" id="qty"value="<?php
                                            if (isset($product)) {
                                                echo $product['qty'];
                                            } ?>">
                            </div>
                            <div class="mb-2">
                                <?php
                                            if (isset($product)) {
                                               echo "<img class='img-responsive' style=width:100px; height:100px; src=$product[imgPath]>";
                                            } ?>
                                <label for="img" class="form-label">Product Image</label>
                                <input type="file" class="form-control" name="file" id="img">
                            </div>
                            <div class="mb-2">
                                <button type="submit" class="btn btn-primary" name="updateBtn">Update</button>
                            </div>

                        </div>



                    </form>
                </div>
            </div>

        </div>





    </div>




</body>

</html>