<?php
    session_start();
    require_once('Transactions.php');
    $data = new Transactions();
    $or_num = 'OR' . $data->getNextId();
    if(isset($_POST["new_trans"]))
    {
        $_SESSION['or_num'] = $or_num; 
        $data->insertTransaction($_SESSION['or_num']);
        $_SESSION['new'] = 1;
    }


    if(isset($_POST["done_trans"]))
    {
        session_destroy();
        $results = $data->getTempTable($_SESSION['or_num']);
        foreach ($results as $row) {
            $data->deductQtyOnhand($row['item_id'], $row['order_qty']);
            $data->insertTransactionDetails($row['transaction_id'], $row['price_code_id'], $row['order_qty'], $row['price']);
        }
        $data->dropTable($_SESSION['or_num']);
        session_start();
        $_SESSION['or_num'] = '';
        $_SESSION['new'] = '';
        $or_num = 'OR' . $data->getNextId(); 
    }

    header("Location: index.php");

?>