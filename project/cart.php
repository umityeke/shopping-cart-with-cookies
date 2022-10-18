<?php

include 'connect.php';

if(isset($_POST['update_qty'])){

   $update_id = $_POST['cart_id'];
   $update_id = filter_var($update_id, FILTER_SANITIZE_STRING);
   $qty = $_POST['qty'];
   $qty = filter_var($qty, FILTER_SANITIZE_STRING);

   $update_cart = $conn->prepare("UPDATE `cart` SET qty = ? WHERE id = ?");
   $update_cart->execute([$qty, $update_id]);

   $success_message[] = 'cart quantity updated!';

}

if(isset($_POST['remove_item'])){

   $delete_id = $_POST['cart_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

   $verify_item = $conn->prepare("SELECT * FROM `cart` WHERE id = ?");
   $verify_item->execute([$delete_id]);

   if($verify_item->rowCount() > 0){
      $delete_item = $conn->prepare("DELETE FROM `cart` WHERE id = ?");
      $delete_item->execute([$delete_id]);
      $success_message[] = 'cart item removed!';
   }else{
      $warning_message[] = 'cart item already deleted!';
   }

}

if(isset($_POST['delete_all'])){

   $verify_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
   $verify_cart->execute([$user_id]);

   if($verify_cart->rowCount() > 0){
      $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
      $delete_cart->execute([$user_id]);
      $success_message[] = 'deleted all from cart!';
   }else{
      $warning_message[] = 'cart items deleted already!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>shoppig cart</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<section class="products">

   <h1 class="heading">shopping cart</h1>

   <div class="box-container">

   <?php
      $grand_total = 0;
      $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
      $select_cart->execute([$user_id]);
      if($select_cart->rowCount() > 0){
         while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){
            $select_products = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
            $select_products->execute([$fetch_cart['product_id']]);
            $fetch_product = $select_products->fetch(PDO::FETCH_ASSOC);
   ?>
   <form action="" method="POST" class="box">
      <input type="hidden" name="cart_id" value="<?= $fetch_cart['id']; ?>">
      <img class="image" src="uploaded_files/<?= $fetch_product['image']; ?>" alt="">
      <h3 class="name"><?= $fetch_product['name']; ?></h3>
      <div class="flex">
         <span class="price"><?= $fetch_product['price']; ?>/-</span>
         <input type="number" name="qty" class="qty" max="99" min="1" maxlength="2" required value="<?= $fetch_cart['qty']; ?>">
         <button type="submit" name="update_qty" class="fas fa-edit"></button>
      </div>
      <p class="sub-total"><span>sub total :</span> <?= $sub_total = ($fetch_cart['qty'] * $fetch_product['price']); ?>/-</p>
      <input type="submit" value="remove from cart" name="remove_item" class="delete-btn"  onclick="return confirm('delete this from cart?');">
   </form>
   <?php
      $grand_total += $sub_total;
      }
   }else{
      echo '<p class="empty">your cart is empty!</p>';
   }
   ?>
   
   </div>

</section>

<section>

   <form action="" class="count-container" method="POST">
      <p>grand total : <span><?= $grand_total; ?>/-</span></p>
      <input type="submit" value="delete all" name="delete_all" onclick="return confirm('delete all from cart?');" class="inline-delete-btn <?= ($grand_total > 1)?'':'disabled'; ?>">
   </form>

   <div class="flex-btn" style="margin-top: 30px;">
      <a href="products.php" class="inline-option-btn">continue shopping</a>
      <a href="#" class="inline-btn <?= ($grand_total > 1)?'':'disabled'; ?>">proceed to checkout</a>
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