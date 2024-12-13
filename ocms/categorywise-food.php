<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1); // Enable error reporting for debugging

include('includes/dbconnection.php');

// Check if 'catid' is present in the URL
if (isset($_GET['catid'])) {
    $cid = $_GET['catid'];
} else {
    echo "Category ID is missing!";
    exit; // Exit if no category ID is provided
}

// Code for Cart
if (!empty($_GET["action"])) {
    switch ($_GET["action"]) {
        // Code for adding product to cart
        case "add":
            if (!empty($_POST["quantity"])) {
                $pid = $_GET["pid"];

                // Fetch product details once
                $sql = $dbh->prepare("SELECT * FROM tblfood WHERE ID=:pid");
                $sql->execute(array(':pid' => $pid));
                $productByCode = $sql->fetch(PDO::FETCH_ASSOC);

                if ($productByCode) {
                    $itemArray = array(
                        $productByCode["ID"] => array(
                            'name' => $productByCode["PackageName"],
                            'code' => $productByCode["ID"],
                            'quantity' => $_POST["quantity"],
                            'price' => $productByCode["Price"],
                            'image' => $productByCode["ItemImage"]
                        )
                    );

                    // Update cart session
                    if (!empty($_SESSION["cart_item"])) {
                        if (array_key_exists($productByCode["ID"], $_SESSION["cart_item"])) {
                            $_SESSION["cart_item"][$productByCode["ID"]]["quantity"] += $_POST["quantity"];
                        } else {
                            $_SESSION["cart_item"] = array_merge($_SESSION["cart_item"], $itemArray);
                        }
                    } else {
                        $_SESSION["cart_item"] = $itemArray;
                    }
                }
            }
            header('Location: cart.php');
            exit(); // Ensure script terminates after redirect
            break;

        // Code for removing product from cart
        case "remove":
            if (!empty($_SESSION["cart_item"])) {
                foreach ($_SESSION["cart_item"] as $k => $v) {
                    if ($_GET["code"] == $k) {
                        unset($_SESSION["cart_item"][$k]);
                    }
                    if (empty($_SESSION["cart_item"])) {
                        unset($_SESSION["cart_item"]);
                    }
                }
            }
            header('Location: cart.php');
            exit(); // Ensure script terminates after redirect
            break;

        // Code for emptying the cart
        case "empty":
            unset($_SESSION["cart_item"]);
            header('Location: cart.php');
            exit(); // Ensure script terminates after redirect
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="zxx">

<head>
    <title>Online Catering Management System | Food Packages</title>
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
    <!-- Page Preloader -->
    <div id="preloder" style="display:none;"> <!-- Disable preloader -->
        <div class="loader"></div>
    </div>

    <!-- Header Section Begin -->
    <?php include_once('includes/header.php'); ?>
    <!-- Header Section End -->

    <!-- Breadcrumb Section Begin -->
    <section class="breadcrumb-section set-bg" data-setbg="img/breadcrumb.jpg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="breadcrumb__text">
                        <h2>Food Packages</h2>
                        <div class="breadcrumb__option">
                            <a href="index.php">Home</a>
                            <span>Packages</span>
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
                                $sql2 = "SELECT * FROM tblcategory";
                                $query2 = $dbh->prepare($sql2);
                                $query2->execute();
                                $result2 = $query2->fetchAll(PDO::FETCH_OBJ);

                                foreach ($result2 as $row) { ?>
                                    <li><a href="categorywise-food.php?catid=<?php echo htmlentities($row->CatName); ?>"><?php echo htmlentities($row->CatName); ?></a></li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-lg-9 col-md-7">
                    <div class="product__discount">
                        <div class="section-title product__discount__title">
                            <h2>Food Packages</h2>
                        </div>
                        <div class="row">
                            <div class="product__discount__slider owl-carousel">
                                <?php
                                $sql = "SELECT * FROM tblfood ORDER BY rand() LIMIT 6";
                                $query = $dbh->prepare($sql);
                                $query->execute();
                                $results = $query->fetchAll(PDO::FETCH_OBJ);

                                if ($query->rowCount() > 0) {
                                    foreach ($results as $row) { ?>
                                        <div class="col-lg-4">
                                            <div class="product__discount__item">
                                                <div class="product__discount__item__pic set-bg" data-setbg="admin/itemimages/<?php echo htmlspecialchars($row->ItemImage); ?>" width="100" height="100"></div>
                                                <div class="product__discount__item__text">
                                                    <span><?php echo htmlspecialchars($row->PackageName); ?></span>
                                                    <h5><a href="#"><?php echo htmlspecialchars($row->Category); ?></a></h5>
                                                    <div class="product__item__price">₱<?php echo htmlspecialchars($row->Price); ?><span></span></div>
                                                </div>
                                            </div>
                                        </div>
                                <?php }
                                } ?>
                            </div>
                        </div>
                    </div>

                    <div class="filter__item">
                        <div class="row">
                            <div class="col-lg-12 col-md-5">
                                <h3 style="color:blue"><?php echo htmlentities($cid); ?> Category Items</h3>
                                <hr />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <?php
                        // Pagination logic
                        $pageno = isset($_GET['pageno']) ? $_GET['pageno'] : 1;
                        $no_of_records_per_page = 6;
                        $offset = ($pageno - 1) * $no_of_records_per_page;

                        // Get total records
                        $ret = "SELECT ID FROM tblfood WHERE Category=:cid";
                        $query1 = $dbh->prepare($ret);
                        $query1->bindParam(':cid', $cid, PDO::PARAM_STR);
                        $query1->execute();
                        $total_rows = $query1->rowCount();
                        $total_pages = ceil($total_rows / $no_of_records_per_page);

                        // Fetch paginated records
                        $sql = "SELECT * FROM tblfood WHERE Category=:cid LIMIT $offset, $no_of_records_per_page";
                        $query = $dbh->prepare($sql);
                        $query->bindParam(':cid', $cid, PDO::PARAM_STR);
                        $query->execute();
                        $results = $query->fetchAll(PDO::FETCH_OBJ);

                        if ($query->rowCount() > 0) {
                            foreach ($results as $row) { ?>
                                <div class="col-lg-4 col-md-6 col-sm-6">
                                    <div class="product__item">
                                        <form method="post" action="products.php?action=add&pid=<?php echo $row->ID; ?>">
                                            <div class="product__item__pic set-bg" data-setbg="admin/itemimages/<?php echo htmlspecialchars($row->ItemImage); ?>"></div>
                                            <div class="product__item__text">
                                                <h6><a href="food-details.php?pid=<?php echo $row->ID; ?>"><?php echo htmlspecialchars($row->PackageName); ?></a></h6>
                                                <h5>₱<?php echo htmlspecialchars($row->Price); ?></h5>
                                                <div class="cart_add">
                                                    <input type="number" name="quantity" value="1" min="1" max="10">
                                                    <input type="submit" name="submit" class="primary-btn" value="Add to Cart">
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                        <?php }
                        } else { ?>
                            <h3 style="color:red" align="center"> No record found against this category </h3>
                        <?php } ?>
                    </div>

                    <div class="product__pagination">
                        <a href="?pageno=1">First</a>
                        <a href="<?php if ($pageno > 1) {
                                        echo "?pageno=" . ($pageno - 1);
                                    } else {
                                        echo '#';
                                    } ?>">Prev</a>
                        <a href="?pageno=<?php echo $pageno; ?>"><?php echo $pageno; ?></a>
                        <a href="<?php if ($pageno < $total_pages) {
                                        echo "?pageno=" . ($pageno + 1);
                                    } else {
                                        echo '#';
                                    } ?>">Next</a>
                        <a href="?pageno=<?php echo $total_pages; ?>">Last</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Product Section End -->

    <!-- Footer Section Begin -->
    <?php include_once('includes/footer.php'); ?>
    <!-- Footer Section End -->

    <!-- Js Plugins -->
    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.nice-select.min.js"></script>
    <script src="js/jquery-ui.min.js"></script>
    <script src="js/jquery.slicknav.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/main.js"></script>
</body>

</html>