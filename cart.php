<?php
include "db.php";

$cart = app::cart();
if (isset($_REQUEST['cart']) && $_REQUEST['cart'] == 'add' && isset($_REQUEST['id'])) {
    $cart->add($_REQUEST['id']);
    unset($_REQUEST['id']);
    unset($_REQUEST['cart']);
    Header('Location: ./?'.http_build_query($_REQUEST));
    exit;
}
if (isset($_REQUEST['cart']) && $_REQUEST['cart'] == 'remove' && isset($_REQUEST['id'])) {
    $cart->remove($_REQUEST['id']);
    Header('Location: cart.php');
    exit;
}
if (isset($_REQUEST['cart']) && $_REQUEST['cart'] == 'clear') {
    $cart->clear();
    Header('Location: ./');
    exit;
}

?>

    <div><a href="./">Products</a> <a href="?cart=clear">Clear</a></div>
    <table border="1" width="100%"><tr><th>N</th><th>Name</th><th>Price</th><th>Amount</th><th>Sum</th><th width="1">&nbsp;</th></tr>
<?php


foreach($cart->rows() as $i=>$row) {
    echo '<tr><td>'.($i + 1).'</td><td>'.$row['name'].'</td><td>'.$row['price'].'</td><td>'.
        $row['amount'].'</td><td>'.($row['price']*$row['amount']).'</td><td><a href="cart.php?cart=remove&id='.$row['id'].'">Видалити</a></td></tr>';
}
echo '<tr><th></th><th colspan="3">TOTAL</th><th>'.$cart->sum().'</th></tr>';
echo '</table>';




