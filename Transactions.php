<?php

require_once('DbConnection.php');
Class Transactions{
    function getInventory() {
        $Dbobj = new DbConnection();
        $qstring = "SELECT SUM(tbl_inventory.qty_on_hand) as qty_on_hand, tbl_items.description, tbl_items.barcode FROM tbl_inventory 
                    LEFT JOIN tbl_items ON (tbl_inventory.item_id = tbl_items.item_id) 
                    GROUP BY tbl_items.item_id";
        $query = mysqli_query($Dbobj->get_db_connection(), $qstring);
        $posts = array();
        while($row = mysqli_fetch_array($query))
        {
            $posts[] = $row;
        }
        return $posts;
    }

    function getInventoryExport() {
        $Dbobj = new DbConnection();
        $qstring = "SELECT tbl_inventory.inventory_id, tbl_inventory.item_id, tbl_inventory.starting_qty, tbl_inventory.qty_on_hand, tbl_inventory.expiry_date, tbl_inventory.batch_no, tbl_price_code.price_per_piece, tbl_price_code.description as price_description, tbl_price_code.price_code_id, tbl_price_code.discount, tbl_items.description, tbl_items.barcode, tbl_items.uom, tbl_items.qty_per_uom 
                    FROM tbl_inventory 
                    LEFT JOIN tbl_items ON (tbl_inventory.item_id = tbl_items.item_id) 
                    LEFT JOIN tbl_price_code ON (tbl_items.item_id = tbl_price_code.item_id)";
        $query = mysqli_query($Dbobj->get_db_connection(), $qstring);
        $posts = array();
        while($row = mysqli_fetch_array($query))
        {
            $posts[] = $row;
        }
        return $posts;
    }

    function insertItemData($barcode, $description, $uom, $qty_per_uom){
        $Dbobj = new DbConnection();
        $con = $Dbobj->get_db_connection();
        $query = mysqli_query($con, "INSERT INTO tbl_items (`barcode`, `description`, `uom`, `qty_per_uom`) VALUES ('".$barcode."','".$description."', '".$uom."', '".$qty_per_uom."')");
        return mysqli_insert_id($con);
    }

    function insertPriceCodeData($item_id, $price_per_piece, $price_description, $discount = 0){
        $Dbobj = new DbConnection();
        $con = $Dbobj->get_db_connection();
        $query = mysqli_query($con, "INSERT INTO tbl_price_code (`item_id`, `price_per_piece`, `description`, `discount`) VALUES ('".$item_id."','".$price_per_piece."', '".$price_description."', '".$discount."')");
    }

    function insertInventory($item_id, $starting_qty, $expiry_date, $batch_no){
        $Dbobj = new DbConnection();
        $con = $Dbobj->get_db_connection();
        $query = mysqli_query($con, "INSERT INTO tbl_inventory (`item_id`, `starting_qty`, `qty_on_hand`, `expiry_date`, `batch_no`) VALUES ('".$item_id."','".$starting_qty."', '".$starting_qty."', '".$expiry_date."', '".$batch_no."')");
    }

    function insertTransaction($ornum){
        try {
            $Dbobj = new DbConnection();
            $con = $Dbobj->get_db_connection();
            $query = mysqli_query($con, "INSERT INTO tbl_transaction (`or_num`) VALUES ('".$ornum."')");
        } catch (\Exception $ex) {
            //do nothing
        }
    }

    function getNextId()
    {
        $Dbobj = new DbConnection();
        $con = $Dbobj->get_db_connection();
        $qstring = "SELECT AUTO_INCREMENT as id
                    FROM information_schema.TABLES
                    WHERE TABLE_SCHEMA = 'pos'
                    AND TABLE_NAME = 'tbl_transaction'";
        $query = mysqli_query($con, $qstring);
        $id = mysqli_fetch_assoc($query);
        return $id['id'];
    }
    function createTempTable($ornum)
    {
        $Dbobj = new DbConnection();
        $con = $Dbobj->get_db_connection();
        $sql = "CREATE TABLE {$ornum} (
            `id` int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `barcode` bigint(20) NOT NULL,
            `transaction_id` int(11) NOT NULL,
            `item_id` int(11) NOT NULL,
            `price_code_id` int(11) NOT NULL,
            `price` float NOT NULL,
            `order_qty` int(11) NOT NULL,
            `reg_date` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
             UNIQUE (`barcode`)
          )";
        mysqli_query($con, $sql);
    }

    function insertTempTableDetails($ornum, $barcode, $transaction_id, $item_id, $price_code_id, $price, $order_qty)
    {
        $Dbobj = new DbConnection();
        $con = $Dbobj->get_db_connection();
        $query = mysqli_query($con, "INSERT INTO {$ornum} (`barcode`, `transaction_id`, `item_id`, `price_code_id`, `price`, `order_qty`) VALUES ('".$barcode."','".$transaction_id."','".$item_id."','".$price_code_id."','".$price."','".$order_qty."')");
    }

    function updateTempTableDetails($ornum, $barcode, $order_qty)
    {
        $Dbobj = new DbConnection();
        $con = $Dbobj->get_db_connection();
        $query = mysqli_query($con, "UPDATE {$ornum} SET `order_qty` = `order_qty` + {$order_qty} WHERE barcode = {$barcode}");
    }
    
    function getDetailedInventory($barcode)
    {
        $Dbobj = new DbConnection();
        $con = $Dbobj->get_db_connection();
        $qstring = "SELECT tbl_inventory.inventory_id, 
                    tbl_inventory.item_id, tbl_inventory.starting_qty, 
                    tbl_inventory.qty_on_hand, tbl_inventory.expiry_date, 
                    tbl_inventory.batch_no, tbl_price_code.price_per_piece, 
                    tbl_price_code.description as price_description,
                    tbl_price_code.price_code_id,
                    tbl_price_code.discount, tbl_items.description, tbl_items.barcode
                    FROM tbl_inventory 
                    LEFT JOIN tbl_items ON (tbl_inventory.item_id = tbl_items.item_id) 
                    LEFT JOIN tbl_price_code ON (tbl_items.item_id = tbl_price_code.item_id)
                    WHERE tbl_items.barcode = '".$barcode."'";
        $query = mysqli_query($con, $qstring);
        $result= mysqli_fetch_assoc($query);
        return $result;
    }

    function getTempTable($ornum)
    {
        $posts = array();
        try {
            $Dbobj = new DbConnection();
            $qstring = "SELECT * FROM {$ornum}";
            $query = mysqli_query($Dbobj->get_db_connection(), $qstring);
            while($row = mysqli_fetch_array($query))
            {
                $posts[] = $row;
            }
            
        } catch (\Throwable $th) {
            //do nothing
        }
        return $posts;
    }

    function getTempTableByBcode($ornum, $bcode)
    {
        $posts = array();
        try {
            $Dbobj = new DbConnection();
            $qstring = "SELECT * FROM {$ornum} WHERE barcode = '".$bcode."'";
            $query = mysqli_query($Dbobj->get_db_connection(), $qstring);
            while($row = mysqli_fetch_array($query))
            {
                $posts[] = $row;
            }
            
        } catch (\Throwable $th) {
            //do nothing
        }
        return $posts;
    }

    function getItemName($item_id)
    {
        $Dbobj = new DbConnection();
        $con = $Dbobj->get_db_connection();
        $qstring = "SELECT `description` FROM tbl_items WHERE item_id = '".$item_id."'";
        $query = mysqli_query($con, $qstring);
        $id = mysqli_fetch_assoc($query);
        return $id['description'];
    }

    function dropTable($ornum)
    {
        $Dbobj = new DbConnection();
        $con = $Dbobj->get_db_connection();
        $sql = "DROP TABLE {$ornum}";
        mysqli_query($con, $sql);
    }

    function insertTransactionDetails($transaction_id, $price_code_id, $qty, $amount)
    {
        $Dbobj = new DbConnection();
        $con = $Dbobj->get_db_connection();
        $query = mysqli_query($con, "INSERT INTO tbl_transaction_details (`transaction_id`, `price_code_id`, `qty`, `amount`) VALUES ('".$transaction_id."','".$price_code_id."','".$qty."','".$amount."')");
    }

    function deductQtyOnhand($item_id, $qty)
    {
        $Dbobj = new DbConnection();
        $con = $Dbobj->get_db_connection();

        $qstring = "SELECT * FROM tbl_inventory WHERE item_id = {$item_id} AND 
        qty_on_hand = (SELECT min(qty_on_hand) as qty_on_hand FROM tbl_inventory WHERE item_id = {$item_id} and qty_on_hand != 0 ORDER by expiry_date ASC)";
        
        $query = mysqli_query($con, $qstring);
        $result = mysqli_fetch_assoc($query);

        if($qty > $result['qty_on_hand']){
            $lessqty = $qty - $qty_on_hand;
            $update = mysqli_query($con, "UPDATE tbl_inventory SET qty_on_hand = qty_on_hand - {$result['qty_on_hand']} WHERE inventory_id = {$result['inventory_id']}");
            $this->deductQtyOnhand($result['item_id'], $lessqty);
        }else{
            $update = mysqli_query($con, "UPDATE tbl_inventory SET qty_on_hand = qty_on_hand - {$qty} WHERE inventory_id = {$result['inventory_id']}");
        }
    }

}
?>