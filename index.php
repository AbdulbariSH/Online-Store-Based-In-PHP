<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once 'class/class.Database.php';
$database = new Database();
$db = $database->getConnection();
require_once 'class/class.Cart.php';
require_once 'class/class.Products.php';
require_once 'class/class.Product.php';
require_once 'class/class.User.php';

$UserInfo = new User([
    // Database Connction 
    'conn' => $db,

]);
if(isset($_POST['login'])){
    $UserInfo->login($_POST['username'],$_POST['password']);
    if($UserInfo->getMessage() == 'null'){
        echo "Logged in successfully";
    }else{
        echo $UserInfo->getMessage();
    }
    
}
if(isset($_POST['register'])){
    $UserInfo->register($_POST['username'],$_POST['password'],$_POST['email']);
    if($UserInfo->getMessage() == 'null'){
        echo "New account registered successfully";
    }else{
        echo $UserInfo->getMessage();
    }
}
$cart = new Cart([
    // Maximum item can added to cart, 0 = Unlimited
    'cartMaxItem' => 0,

    // Maximum quantity of a item can be added to cart, 0 = Unlimited
    'itemMaxQuantity' => 5,

    // User info
    'UserInfo' => $UserInfo,
]);

if (isset($_GET['Category'])) {
    $Category = intval($_GET['Category']);
} else {
    $Category = -1;
}

$Products = new Products([
    // Database Connction 
    'conn' => $db,

    // Items type, 0 = Unavailable , -1 For All
    'available' => 1,
    /**
     * 1 = Apple
     * 2- Samsong
     * -1 For All
     */
    'Category' => $Category,

]);


$products = $Products->GetItems();


// Empty the cart
if (isset($_POST['empty'])) {
    $cart->clear();
}

// Add item
if (isset($_POST['AddToCart'])) {
    foreach ($products as $product) {
        if ($_POST['productId'] == $product->getId()) {
            break;
        }
    }
    $cart->add($product->getId(), $_POST['qty'], [
        'price' => $product->getPrice(),
        'color' => (isset($_POST['colors'])) ? $_POST['colors'] : '',
        'storage' => (isset($_POST['storage'])) ? $_POST['storage'] : '',
    ]);
    $_SESSION['success'] = "Item Added";
    $_SESSION['success_icon'] = "success";
    $_SESSION['success_title'] = "Success";
}

// Update item
if (isset($_POST['update'])) {
    foreach ($products as $product) {
        if ($_POST['id'] == $product->getId()) {
            break;
        }
    }

    $cart->update($product->getId(), $_POST['qty'], [
        'price' => $product->getPrice(),
        'color' => (isset($_POST['colors'])) ? $_POST['colors'] : '',
        'storage' => (isset($_POST['storage'])) ? $_POST['storage'] : '',
    ]);
    $_SESSION['success'] = "Item Updated";
    $_SESSION['success_icon'] = "success";
    $_SESSION['success_title'] = "Success";
}

// Remove item
if (isset($_POST['remove'])) {
    foreach ($products as $product) {
        if ($_POST['id'] == $product->getId()) {
            break;
        }
    }

    $cart->remove($product->getId(), [
        'price' => $product->getPrice(),
        'color' => (isset($_POST['colors'])) ? $_POST['colors'] : '',
        'storage' => (isset($_POST['storage'])) ? $_POST['storage'] : '',
    ]);
    
    $_SESSION['success'] = "Item Deleted";
    $_SESSION['success_icon'] = "success";
    $_SESSION['success_title'] = "Success";
}


$page = (isset($_REQUEST['page'])) ? $_GET['page'] : 'index';
if ($page == 'checkout') {
    $output = '
    <div class="container">
        <h1>Checkout</h1>

        <div class="row">
            <div class="col-md-14">
                <div class="table-responsive">
                    <pre>' . json_encode($cart->getItems()) . '</pre>
                </div>
            </div>
        </div>
    </div>
    
    ';
    $output = '
    <div class="container">
    <div class="card-body">
      <div class="container mb-5 mt-3">
        <div class="row d-flex align-items-baseline">
          <div class="col-xl-9">
            <p style="color: #7e8d9f;font-size: 20px;">Invoice >> <strong>ID: #123-123</strong></p>
          </div>
          <div class="col-xl-3 float-end">
            <a class="btn btn-light text-capitalize border-0" data-mdb-ripple-color="dark"><i
                class="fas fa-print text-primary"></i> Print</a>
            <a class="btn btn-light text-capitalize" data-mdb-ripple-color="dark"><i
                class="far fa-file-pdf text-danger"></i> Export</a>
          </div>
          <hr>
        </div>
  
        <div class="container">
          <div class="col-md-12">
            <div class="text-center">
              <i class="fa-solid fa-2x fa-receipt"></i>
              <p class="pt-0">SimpleStore.com</p>
            </div>
  
          </div>
  
  
          <div class="row">
            <div class="col-xl-8">
              <ul class="list-unstyled">
                <li class="text-muted">To: <span style="color:#5d9fc5 ;">Abdulbari Saigh</span></li>
                <li class="text-muted">King Fahad, Makkah</li>
                <li class="text-muted">Jeddah, Saudi Arabia</li>
                <li class="text-muted"><i class="fas fa-phone"></i> 123-456-789</li>
              </ul>
            </div>
            <div class="col-xl-4">
              <p class="text-muted">Invoice</p>
              <ul class="list-unstyled">
                <li class="text-muted"><i class="fas fa-circle" style="color:#84B0CA ;"></i> <span
                    class="fw-bold">ID:</span>#123-456</li>
                <li class="text-muted"><i class="fas fa-circle" style="color:#84B0CA ;"></i> <span
                    class="fw-bold">Creation Date: </span>Jun 23,2023</li>
                <li class="text-muted"><i class="fas fa-circle" style="color:#84B0CA ;"></i> <span
                    class="me-1 fw-bold">Status:</span><span class="badge bg-warning text-black fw-bold">
                    Unpaid</span></li>
              </ul>
            </div>
          </div>
  
          <div class="row my-2 mx-1 justify-content-center">
            <table class="table table-striped table-borderless">
              <thead style="background-color:#84B0CA ;" class="text-white">
                <tr>
                  <th scope="col">pic</th>
                  <th scope="col">Description</th>
                  <th scope="col">Qty</th>
                  <th scope="col">Unit Price</th>
                  <th scope="col">Amount</th>
                </tr>
              </thead>
              <tbody>
              ';
              if (!$cart->isEmpty()) {
                $allItems = $cart->getItems();
                foreach ($allItems as $id => $items) {
                    foreach ($items as $item) {
                        foreach ($products as $product) {
                            if ($id == $product->getId()) {
                                break;
                            }
                        }
                        $output.='
                        <tr>
                          <th scope="row"><img src="' . $product->getImage() . '" border="0" width="64" height="64" title="' . $product->getLabel() . '" /></th>
                          <td>' . $product->getLabel() . ((isset($item['options']['color'])) ? ('<p><strong>Color: </strong>' . $item['options']['color'] . '</p>') : '') . ((isset($item['options']['storage'])) ? ('<p><strong>storage: </strong>' . $item['options']['storage'] . '</p>') : '') . '</td>
                          <td>' . $item['quantity'] . '</td>
                          <td>$' . $product->getPrice() . '</td>
                          <td>$' . $item['options']['price']*$item['quantity'] . '</td>
                        </tr>';

                    }
                }
            }
              $output.='
              </tbody>
  
            </table>
          </div>
          <div class="row">
            <div class="col-xl-8">
              <p class="ms-3">Add additional notes and payment information</p>
              <input type="text" name="notes" id="notes" class="form-control" />
            </div>
            <div class="col-xl-3">
              <ul class="list-unstyled">
                <li class="text-muted ms-3"><span class="text-black me-4">SubTotal</span>$'.number_format($cart->getAttributeTotal('price'), 2, '.', ',').'</li>
                <li class="text-muted ms-3 mt-2"><span class="text-black me-4">Tax(15%)</span>$0</li>
              </ul>
              <p class="text-black float-start"><span class="text-black me-3"> Total Amount</span><span
                  style="font-size: 25px;">$'.number_format($cart->getAttributeTotal('price'), 2, '.', ',').'</span></p>
            </div>
          </div>
          <hr>
          <div class="row">
            <div class="col-xl-10">
              <p>Thank you for your purchase</p>
            </div>
            <div class="col-xl-2">
              <button type="button" class="btn btn-primary text-capitalize"
                style="background-color:#60bdf3 ;">Pay Now</button>
            </div>
          </div>
  
        </div>
      </div>
    </div>
  </div>
    
    ';
} elseif ($page == 'logout' && $UserInfo->userStatus()) {
    $UserInfo->Logout();
    if($UserInfo->getMessage() == 'null'){
        echo "Logout successfully";
    }else{
        echo $UserInfo->getMessage();
    }
    header('location: index.php?page=index');

} elseif ($page == 'login' && !$UserInfo->userStatus()) {
    $output = '
    <div class="container">
        <h1>Login</h1>

        <div class="row">
            <div class="col-md-4">
            <form action="" method="post">
            <!-- Username input -->
            <div class="form-outline mb-4">
            <label class="form-label" for="form2Example1">Username</label>
              <input type="text" name="username" id="form2Example1" class="form-control" />
              
            </div>
          
            <!-- Password input -->
            <div class="form-outline mb-4">
            <label class="form-label" for="form2Example2">Password</label>
              <input type="password" name="password" id="form2Example2" class="form-control" />
            </div>
          
            <!-- 2 column grid layout for inline styling -->
            <div class="row mb-4">
              <div class="col d-flex justify-content-center">
                <!-- Checkbox -->
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" value="" id="form2Example31" checked />
                  <label class="form-check-label" for="form2Example31"> Remember me </label>
                </div>
              </div>
          
            </div>
          
            <!-- Submit button -->
            <button type="submit" name="login" class="btn btn-primary btn-block mb-4">Sign in</button>
          


          </form>
            </div>
        </div>
    </div>
    
    ';
} elseif ($page == 'register' && !$UserInfo->userStatus()) {
    $output = '
    <div class="container">
        <h1>Register</h1>

        <div class="row">
            <div class="col-md-4">
            <form action="" method="post">
            <!-- Username input -->
            <div class="form-outline mb-4">
            <label class="form-label" for="form2Example1">Username</label>
              <input type="text" name="username" id="form2Example1" class="form-control" />
              
            </div>
          
            <!-- Email input -->
            <div class="form-outline mb-4">
            <label class="form-label" for="form2Example1">Email</label>
              <input type="email" name="email" id="form2Example1" class="form-control" />
              
            </div>
          
            <!-- Password input -->
            <div class="form-outline mb-4">
            <label class="form-label" for="form2Example2">Password</label>
              <input type="password" name="password" id="form2Example2" class="form-control" />
            </div>
          
            <!-- 2 column grid layout for inline styling -->
            <div class="row mb-4">
              <div class="col d-flex justify-content-center">
                <!-- Checkbox -->
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" value="" id="form2Example31" checked />
                  <label class="form-check-label" for="form2Example31"> Remember me </label>
                </div>
              </div>
          
            </div>
          
            <!-- Submit button -->
            <button type="submit" name="register" class="btn btn-primary btn-block mb-4">Sign Up</button>
          


          </form>
            </div>
        </div>
    </div>
    
    ';
} elseif ($page == 'cart') {
    $cartContents = '
	<div class="alert alert-warning">
		<i class="fa fa-info-circle"></i> There are no items in the cart.
	</div>';



    if (!$cart->isEmpty()) {
        $allItems = $cart->getItems();

        $cartContents = '
		<table class="table table-striped table-hover">
			<thead>
				<tr>
					<th class="col-md-2">pic</th>
					<th class="col-md-7">Product</th>
					<th class="col-md-3 text-center">Quantity</th>
					<th class="col-md-2 text-right">Price</th>
				</tr>
			</thead>
			<tbody>';

        foreach ($allItems as $id => $items) {
            foreach ($items as $item) {
                foreach ($products as $product) {
                    if ($id == $product->getId()) {
                        break;
                    }
                }

                $cartContents .= '
				<tr>
                <form action="" method="POST">
                <td><img src="' . $product->getImage() . '" border="0" width="120" height="120" title="' . $product->getLabel() . '" /> </td>
					<td>' . $product->getLabel() . ((isset($item['options']['color'])) ? ('<p><strong>Color: </strong>' . $item['options']['color'] . '</p>') : '') . ((isset($item['options']['storage'])) ? ('<p><strong>storage: </strong>' . $item['options']['storage'] . '</p>') : '') . '</td>
					<td class="text-center">
                    <div class="form-group">
                    <input name="qty" type="number" value="' . $item['quantity'] . '" class="form-control quantity pull-left" style="width:100px">
                    <div class="pull-right">
                    <input name="id" type="hidden" value="' . $id . '">
                    <input name="colors" type="hidden" value="' . $item['options']['color'] . '">
                    <input name="storage" type="hidden" value="' . $item['options']['storage'] . '">
                    <button type="submit" name="update" class="btn btn-default btn-update" data-id="' . $id . '" data-color="' . ((isset($item['options']['color'])) ? $item['options']['color'] : '') . '">
                    <i class="fa fa-refresh"></i> Update</button>
                    <button type="submit" name="remove" class="btn btn-danger btn-remove" data-id="' . $id . '" data-color="' . ((isset($item['options']['color'])) ? $item['options']['color'] : '') . '">
                    <i class="fa fa-trash"></i></button></div></div></td>
					<td class="text-right">$' . $item['options']['price'] . '</td>
                    </form>
				</tr>';
            }
        }

        $cartContents .= '
			</tbody>
		</table>

		<div class="text-right">
			<h3>Total:<br />$' . number_format($cart->getAttributeTotal('price'), 2, '.', ',') . '</h3>
		</div>

		<p>
			<div class="pull-left">
            <form action="" method="post">
				<button type="submit" name="empty" class="btn btn-warning btn-empty-cart">Empty Cart</button>
                </form>
			</div>
			<div class="pull-right text-right">
				<a href="?page=home" class="btn btn-info">Continue Shopping</a>
				<a href="?page=checkout" class="btn btn-danger">Checkout</a>
			</div>
		</p>';
    }

    $output = '
    <div class="container">
    <h1>Shopping Cart</h1>

    <div class="row">
        <div class="col-md-12">
             <div class="table-responsive">
                ' . $cartContents . '
             </div>
        </div>
    </div>
</div>
    ';
} else {

    $output = '
    <div class="container">
            <h1>Products</h1>
            <div class="row"> 
            <div class="pull-right text-right">
            <a href="?page=home&Category=1" class="btn btn-info">Iphone</a>
            <a href="?page=home&Category=2" class="btn btn-info">Samsung</a>
        </div>
            ';
    foreach ($products as $product) {
        $output .= '
                    
					<div class="col-md-3">
                    <form action="" method="POST">
						<h3>' . $product->getLabel() . '</h3>

						<div>
							<div class="pull-left">
								<img src="' . $product->getImage() . '" border="0" width="200" height="250" title="' . $product->getLabel() . '" />
							</div>
							<div class="pull-right">
								<h4>$' . $product->getPrice() . '</h4>
									<input type="hidden" name="productId" value="' . $product->getId() . '" class="product-id" />';

        if ($product->getOptions()) {
            foreach ($product->getOptions() as $key => $Option) {
                $output .= '
                <div class="form-group">
                    <label>' . $key . ':</label>
                    <select name="' . $key . '" class="form-control color">';

                foreach ($Option as $value) {
                    $output .= '
												<option value="' . $value . '"> ' . $value . '</option>';
                }

                $output .= '
											</select>
										</div>';
            }
        }

        $output .= '

									<div class="form-group">
										<label>Quantity:</label>
										<input type="number" value="1" name="qty" class="form-control quantity" />
									</div>
									<div class="form-group">
										<button type="submit" name="AddToCart" class="btn btn-info"><i class="fa fa-shopping-cart"></i> Add to Cart</button>
									</div>
							</div>
							<div class="clearfix"></div>
						</div>
                        </form>
					</div>
                   
                    ';
    }
?>
    </div>
    </div>

<?php } ?>




<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Cart - A Simple PHP Cart</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-w76AqPfDkMBDXo30jS1Sgez6pr3x5MlQ1ZAGC+nuZB+EYdgRZgiwxhTBTkF7CXvN" crossorigin="anonymous"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.1/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        body {
            padding-bottom: 20px;
        }

        .navbar {
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand" href="#">Simple Shop</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExample02" aria-controls="navbarsExample02" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarsExample02">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="?page=home">Home </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="?page=cart"><i class="fa fa-shopping-cart"></i> Cart (<?php echo $cart->getTotalItem(); ?>)</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="?page=checkout">Checkout</a>
                </li>
                <?php if(!$UserInfo->userStatus()){ ?>
                <li class="nav-item">
                    <a class="nav-link" href="?page=login">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="?page=register">Register</a>
                </li>
                <?php }else{ ?>
                    <li class="nav-item">
                    <a class="nav-link" href="?page=home">Welcome, <?php echo $UserInfo->getUsername() ?></a>
                </li>
                    <li class="nav-item">
                    <a class="nav-link" href="?page=logout">Logout</a>
                </li>
               <?php  } ?>
            </ul>
        </div>
    </nav>
    <?php echo $output ?>

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.1/dist/sweetalert2.all.min.js"></script>

    <?php
    if (isset($_SESSION['Error']) && $_SESSION['Error'] != '') {
    ?>
        <script>
            Swal.fire({
                icon: '<?php echo $_SESSION['Error_icon']; ?>',
                title: '<?php echo $_SESSION['Error_title']; ?>',
                text: '<?php echo $_SESSION['Error']; ?>',
            });
        </script>
    <?php
        unset($_SESSION['Error']);
        unset($_SESSION['Error_icon']);
        unset($_SESSION['Error_title']);
    }
    ?>
    <?php
    if (isset($_SESSION['success']) && $_SESSION['success'] != '') {
    ?>
        <script>
            Swal.fire({
                icon: '<?php echo $_SESSION['success_icon']; ?>',
                title: '<?php echo $_SESSION['success_title']; ?>',
                text: '<?php echo $_SESSION['success']; ?>',
            });
        </script>
    <?php
        unset($_SESSION['success']);
        unset($_SESSION['success_icon']);
        unset($_SESSION['success_title']);
    }
    ?>

</body>

</html>