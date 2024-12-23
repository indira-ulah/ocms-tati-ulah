<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

////code for Cart
if(!empty($_GET["action"])) {
switch($_GET["action"]) {
    //code for adding product in cart
    case "add":
        if(!empty($_POST["quantity"])) {
            echo $pid=$_GET["pid"];

 //$sql="SELECT * FROM tblproduct WHERE ID=:pid ";
$sql = $dbh->prepare("SELECT * FROM tblfood WHERE ID=:pid ");
//$stckdta=$dbh->query($sql);
$sql->execute(array(':pid' => $pid));
 while($productByCode=$sql->fetch(PDO::FETCH_ASSOC))
 {


            $itemArray = array($productByCode["ID"]=>array('name'=>$productByCode["PackageName"], 'code'=>$productByCode["ID"], 'quantity'=>$_POST["quantity"], 'price'=>$productByCode["Price"], 'image'=>$productByCode["ItemImage"]));
        
            if(!empty($_SESSION["cart_item"])) {
                if(in_array($productByCode["ID"],array_keys($_SESSION["cart_item"]))) {
                    foreach($_SESSION["cart_item"] as $k => $v) {
                            if($productByCode["ID"] == $k) {
                                if(empty($_SESSION["cart_item"][$k]["quantity"])) {
                                    $_SESSION["cart_item"][$k]["quantity"] = 0;
                                }
                                $_SESSION["cart_item"][$k]["quantity"] += $_POST["quantity"];
                            }
                    }
                
                } else {
                    $_SESSION["cart_item"] = array_merge($_SESSION["cart_item"],$itemArray);
                    
                }
            }  else {
                $_SESSION["cart_item"] = $itemArray;

            }
        }
    }
        header('location:cart.php');
    break;

    // code for removing product from cart
    case "remove":
        if(!empty($_SESSION["cart_item"])) {
            foreach($_SESSION["cart_item"] as $k => $v) {
                    if($_GET["code"] == $k)
                        unset($_SESSION["cart_item"][$k]);              
                    if(empty($_SESSION["cart_item"]))
                        unset($_SESSION["cart_item"]);
            }
        }
        header('location:cart.php');
    break;
    // code for if cart is empty
    case "empty":
        unset($_SESSION["cart_item"]);
            header('location:cart.php');
    break;  
}
}

?>
<!DOCTYPE html>
<html lang="zxx">

<head>
    
    <title>Online Catering Management  System | Food Packages</title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;600;900&display=swap" rel="stylesheet">

    <!-- Css Styles -->
    <link rel="stylesheet" href="css/bootstrap.min.css" type="text/css">
    <link rel="stylesheet" href="css/font-awesome.min.css" type="text/css">
    <link rel="stylesheet" href="css/elegant-icons.css" type="text/css">
    <link rel="stylesheet" href="css/nice-select.css" type="text/css">
    <link rel="stylesheet" href="css/jquery-ui.min.css" type="text/css">
    <link rel="stylesheet" href="css/owl.carousel.min.css" type="text/css">
    <link rel="stylesheet" href="css/slicknav.min.css" type="text/css">
    <link rel="stylesheet" href="css/style.css" type="text/css">
</head>

<body>
    <!-- Page Preloder -->
    <div id="preloder">
        <div class="loader"></div>
    </div>

    <!-- Humberger Begin -->
<?php include_once('includes/header.php');?>
    <!-- Header Section End -->

    <!-- Hero Section End -->

    <!-- Breadcrumb Section Begin -->
    <section class="breadcrumb-section set-bg" data-setbg="img/breadcrumb.jpg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="breadcrumb__text">
                        <h2>Search Result</h2>
                        <div class="breadcrumb__option">
                            <a href="index.php">Home</a>
                            <span>Search Result</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Breadcrumb Section End -->

    <!-- Product Section Begin -->
    <section class="product spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-md-5">
                    <div class="sidebar">
                        <div class="sidebar__item">
                            <h4>Food Category</h4>
                            <ul>
                            <?php 


$sql2 = "SELECT * from   tblcategory ";
$query2 = $dbh -> prepare($sql2);
$query2->execute();
$result2=$query2->fetchAll(PDO::FETCH_OBJ);

foreach($result2 as $row)
{          
    ?>       
                                <li><a href="categorywise-food.php?catid=<?php echo htmlentities($row->CatName)?>"><?php echo htmlentities($row->CatName);?></a>
                                 <?php } ?>  
                            </ul>
                        </div>
                    
                   
               
                 
                    </div>
                </div>
                <div class="col-lg-9 col-md-7">
                   
                    <div class="filter__item">
                        <div class="row">
                            <div class="col-lg-12 col-md-5">
                           <h3 style="color:blue">Search Result againt "<?php echo $_POST['category'];?>" keyword</h3>
                           <hr />
                            </div>
                            
                        </div>
                    </div>
                    <div class="row">
                        <?php
                      
$category=$_POST['category'];
$sql="SELECT * from  tblfood WHERE Category=:category || PackageName=:category order by rand() limit 6";
$query = $dbh -> prepare($sql);
$query->bindParam(':category',$category,PDO::PARAM_STR);
$query->execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
$cnt=1;
if($query->rowCount() > 0)
{
foreach($results as $row)
{               ?>
    
                        <div class="col-lg-4 col-md-6 col-sm-6">
                            
                            <div class="product__item">
                                <form method="post" action="products.php?action=add&pid=<?php echo $row->ID; ?>">
                                <div class="product__item__pic set-bg" data-setbg="admin/itemimages/<?php echo $row->ItemImage;?>">
                                    <ul class="product__item__pic__hover">
                                        <li><a href="#"><i class="fa fa-heart"></i></a></li>
                                        <li><a href="#"><i class="fa fa-retweet"></i></a></li>
                                        <li><a href="#"><i class="fa fa-shopping-cart"></i></a></li>
                                    </ul>
                                </div>
                                <div class="product__item__text">
                                    <h6><a href="food-details.php?pid=<?php echo $row->ID;?>"><?php echo $row->PackageName;?></a></h6>
                                    <h5>₱<?php echo $row->Price;?></h5>
                                </div>
                               <p style="padding-top: 20px"> <input type="text" class="item_quantity" name="quantity" value="1" /></p>
                    
<input type="submit" value="Add to Cart" class="btnAddAction" />
                            </div>
                            
                        </div>
</form>
                        <?php $cnt=$cnt+1;}}?>
                     
                    </div>
             
                </div>
            </div>
        </div>
    </section>
    <!-- Product Section End -->

    <!-- Footer Section Begin -->
<?php include_once('includes/footer.php');?>
    <!-- Footer Section End -->

    <!-- Js Plugins -->
    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.nice-select.min.js"></script>
    <script src="js/jquery-ui.min.js"></script>
    <script src="js/jquery.slicknav.js"></script>
    <script src="js/mixitup.min.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/main.js"></script>



</body>

</html>