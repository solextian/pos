<?php 
    require_once('Transactions.php');
    if(isset($_POST["export"]))
    {
        $data = "items,,,,price code,,,inventory\n";
        $data .= "barcode,description,uom,qty_per_uom,price_per_piece,price_description,discount,starting_qty,expiry_date,batch_no,qty_on_hand\n";
        $export = new Transactions();
        $results = $export->getInventoryExport();
        foreach ($results as $row) {
            $data .= $row['barcode'].",".$row['description'].",".$row['uom'].",".$row['qty_per_uom'].",".$row['price_per_piece'].",".$row['price_description'].",".$row['discount'].",".$row['starting_qty'].",".$row['expiry_date'].",".$row['batch_no'].",".$row['qty_on_hand']."\n";
        }
    }
    header('Content-Type: application/csv');
    header('Content-Disposition: attachment; filename="filename-'.date('m/d/YHis').'.csv"');
    echo $data; exit();
?>