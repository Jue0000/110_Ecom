<?php
if (!isset($_SESSION)) {
    session_start();
}
require_once "dbconnect.php";

 try{
    $sql="select * from category";
  $stmt=$conn->prepare($sql);
  $stmt->execute();
  $categories=$stmt->fetchAll();

 }catch(PDOException $e){
    echo $e->getMessage();
 }

try {
    $sql = "SELECT p.productID, p.productName, p.price, 
    p.description, p.qty, p.imgPath, c.catname as category
    FROM product p, category c WHERE
    p.category = c.catId";
   $stmt = $conn->prepare($sql);
    $stmt->execute();
    $products = $stmt->fetchAll();


} catch (Exception $e) {
    echo $e->getMessage();
}
if(isset($_GET['bsearch']))
{
    $text=$_GET['tsearch'];
    try{
        $sql="SELECT p.productID, p.productName, p.price,
         p.description, p.qty, p.imgPath, c.catname as category
    FROM product p, category c WHERE
    p.category = c.catId and
    p.productName like ?";
   $stmt= $conn->prepare($sql);
   $stmt->execute(["%".$text."%"]);
   $products=$stmt->fetchAll();

    }catch(PDOException $e)
    {
        echo $e->getMessage();
    }
}
else if(isset($_GET['cSearch']))
{
    $cid=$_GET['category'];
     try{
        $sql="SELECT p.productID, p.productName, p.price,
         p.description, p.qty, p.imgPath, c.catname as category
    FROM product p, category c WHERE
    p.category = c.catId and
    c.catId=?";
   $stmt= $conn->prepare($sql);
   $stmt->execute([$cid]);
   $products=$stmt->fetchAll();

    }catch(PDOException $e)
    {
        echo $e->getMessage();
    }
}
else if(isset($_POST['radioBtn'])){//radio search
    $price=$_POST['price'];
    if($price=="first")
    {
        $lower=200;
        $upper=300;
    }
    else if($price=="second"){
        $lower=301;
        $upper=500;
    }
    
    try{
        $sql="select p.productID,p.productName,p.price,p.qty,
        p.description,p.imgPath, c.catName as category
        from product p, category c 
        where p.price BETWEEN ? and ?
        AND c.catId=p.category;";

        $stmt=$conn->prepare($sql);
        $stmt->execute([$lower,$upper]);
        $products=$stmt->fetchAll();



    }catch(PDOException $e)
    {
        echo $e->getMessage();
    }
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>

</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php require_once "navbarcopy.php" ?>

        </div>
        <div class="row"><!-- content    -->
            <div class="col-md-2 py-5">
                <div class="card">
                <a href="insertprouct.php" class="btn btn-outline-primary card-link">New Product</a>
            </div>
            <div class="card">
                <div class="card-title">Category Search</div>
                <div class="card-body">
                    <form class="form" method="get" action="viewProduct.php">
                    <select name="category" id="" class="form-select mb-2">
                        <?php
                        foreach($categories as $category)
                        {
                            echo "<option value=$category[catId]>$category[catName]</option>";
                        }


                        ?>

                    </select>
                <button name="cSearch" type="submit" class="btn btn-outline-primary rounded-pill">Search</button>
                    </form>
                </div>
            </div>
            <div class="card mb-3">
                <form class="form" action="viewProduct.php" method="post">
                <div class="form-check">
                        <input type="radio" name="price" value="first" class="form-check-input">
                        <label for="" class="form-check-label">$200-$300</label>
                </div>

                <div class="form-check mb-2">
                        <input type="radio" name="price" value="second" class="form-check-input">
                        <label for="" class="form-check-label">$301-$400</label>
                </div>
                <div class="mb-2">
                    <button name="radioBtn" class="btn btn-outline-primary rounded-pill">Search</button>
                </div>


                        </form>
            </div>
            </div>
            <div class="col-md-10 py-5"><!-- content    -->
                <?php
                if (isset($_SESSION["message"])) {
                    echo "<p class='alert alert-success' style = width: 500px>$_SESSION[message] </p>";
                    unset($_SESSION["message"]);
                }
                else if (isset($_SESSION['deleteSuccess'])) {
                    echo "<p class='alert alert-success'>$_SESSION[deleteSuccess] </p>";
                    unset($_SESSION['deleteSuccess']);
                }
                else if(isset($_SESSION['updateMessage']))
                {
                    echo "<p class='alert alert-success'> $_SESSION[updateMessage] </p>";
                    unset($_SESSION['updateMessage']);
                }
                ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <td>Name</td>
                            <td>Category</td>
                            <td>Price</td>
                            <td>Quantity</td>
                            <td>Description</td>
                            <td>Image</td>
                        </tr>
                    </thead>
                    <tbody>
                         <?php
                         foreach($products as $product) {
                            $desc = substr($product['description'],0,80);
                            echo "<tr>
                            <td>$product[productName]</td>
                            <td>$product[category]</td>
                            <td>$product[price]</td>
                            <td>$product[qty]</td>
                            <td class='text-wrap'>$desc</td>
                            <td><img src = $product[imgPath] style = width:100px; height:100px;></td>
                            <td><a href=editDelete.php?eid=$product[productID] class='btn btn-primary rounded pill'>Edit</a></td>
                            <td><a href=editDelete.php?did=$product[productID] class='btn btn-danger rounded pill'>Delete</a></td>
                            </tr>";
                         }
                         ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>