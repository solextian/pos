<?php   
    session_start();
    require_once('Transactions.php');

    if(isset($_POST["qtybarcode"]))
    {
       if(!empty($_POST["qtybarcode"])){
            $data = new Transactions();
            $arr = explode("/", trim($_POST["qtybarcode"]));
            $ornum = $_SESSION['or_num'];
            $transaction_id = ltrim($ornum, "OR");
            $data->createTempTable($ornum);
            $row = $data->getDetailedInventory($arr[1]);
            if(!empty($row)){
                if(empty($data->getTempTableByBcode($ornum, $row['barcode']))){
                    $data->insertTempTableDetails($ornum, $row['barcode'], $transaction_id, $row['item_id'], $row['price_code_id'], $row['price_per_piece'], $arr[0]);
                }else{
                    $data->updateTempTableDetails($ornum, $row['barcode'], $arr[0]);
                }
            }
            
       }
    }
    header("Location: index.php");
?>