<?php

include 'connect.php';

if(isset($_POST['add_to_cart'])){

   $product_id = $_POST['product_id'];
   $product_id = filter_var($product_id, FILTER_SANITIZE_STRING);

   $verify_product = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
   $verify_product->execute([$product_id]);

   if($verify_product->rowCount() > 0){

      $verify_cart = $conn->prepare("SELECT * FROM `cart` WHERE product_id = ? AND user_id = ?");
      $verify_cart->execute([$product_id, $user_id]);

      if($verify_cart->rowCount() > 0){
         $warning_message[] = 'already added to cart!';
      }else{
         $id = create_unique_id();
         $qty = $_POST['qty'];
         $qty = filter_var($qty, FILTER_SANITIZE_STRING);

         $insert_cart = $conn->prepare("INSERT INTO `cart`(id, user_id, product_id, qty) VALUES(?,?,?,?)");
         $insert_cart->execute([$id, $user_id, $product_id, $qty]);
         $success_message[] = 'added to cart!';
      }

      
   }else{
      $error_message[] = 'something went wrong!';
   }

}

$count_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
$count_cart->execute([$user_id]);
$total_cart_items = $count_cart->rowCount();

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>products</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<section>

   <div class="count-container">
      <p>total cart items : <span><?= $total_cart_items; ?></span></p>
      <a href="cart.php" class="inline-btn <?= ($total_cart_items > 1)?'':'disabled'; ?>">view cart</a>
   </div>

</section>

<section class="products">

   <h1 class="heading">latest products</h1>

   <div class="box-container">

   <?php
      $select_products = $conn->prepare("SELECT * FROM `products`");
      $select_products->execute();
      if($select_products->rowCount() > 0){
         while($fetch_product = $select_products->fetch(PDO::FETCH_ASSOC)){
   ?>
   <form action="" class="box" method="POST">
      <input type="hidden" name="product_id" value="<?= $fetch_product['id']; ?>">
      <img class="image" src="uploaded_files/<?= $fetch_product['image']; ?>" alt="">
      <h3 class="name"><?= $fetch_product['name']; ?></h3>
      <div class="flex">
         <span class="price"><?= $fetch_product['price']; ?>/-</span>
         <input type="number" name="qty" class="qty" max="99" min="1" maxlength="2" required value="1">
      </div>
      <input type="submit" value="add to cart" name="add_to_cart" class="btn">
   </form>
   <?php
   }
   }else{
      echo '<p class="emtpy">no products added yet!</p>';
   }
   ?>

   </div>

</section>





















<!-- sweet alert cdn link  -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

<script>

   document.querySelectorAll('input[type="number"]').forEach(inputNumbmer => {
      inputNumbmer.oninput = () =>{
         if(inputNumbmer.value.length > inputNumbmer.maxLength) inputNumbmer.value = inputNumbmer.value.slice(0, inputNumbmer.maxLength);
      }
   });

</script>
   
<?php include 'message.php'; ?>

</body>
</html>