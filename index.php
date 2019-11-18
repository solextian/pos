<?php
    session_start();
    require_once('Transactions.php');
    $data = new Transactions();

    if (!isset($_SESSION['or_num']) || $_SESSION['or_num'] == '') {
        $or_num = 'OR' . $data->getNextId();
    }else{
        $or_num = $_SESSION['or_num'];
    }
?> 

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>POS</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col">
            <form enctype="multipart/form-data" method="post" action="import_csv.php">
                <input type="file" name="file" id="file"> 
                <input type="submit" name="submit" value="Import CSV">
            </form>
            <form enctype="multipart/form-data" method="post" action="export_csv.php">
                <input type="submit" name="export" value="Export CSV">
            </form>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <form class="form-inline" action="done.php" method="post">
                    <div class="form-group mb-2">
                        <input type="text" class="form-control" name="or_num" value="<?php echo $or_num; ?>" readonly>
                    </div>
                    <?php if(empty($_SESSION['new'])) : ?>
                        <input type="submit" class="btn btn-primary mb-2" name="new_trans" value="NEW TRANSACTION">
                    <?php else: ?>
                        <input type="submit" class="btn btn-success mb-2" name="done_trans" value="DONE TRANSACTION">
                    <?php endif; ?>
                </form>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <table border="1">
                    <thead>
                    <tr>
                        <th>BARCODE</th>
                        <th>QTY</th>
                        <th>ITEM</th>
                        <th>UNIT PRICE</th>
                        <th>SUBTOTAL</th>
                    </tr>
                    </thead>
                    <tbody>
                   <?php 
                    $sum = 0;
                    foreach ($data->getTempTable($_SESSION['or_num']) as $row) : 
                        $sum += $row['order_qty'] * $row['price'];
                   ?>
                        <tr>
                            <td><?php echo $row['barcode']; ?></td>
                            <td><?php echo $row['order_qty']; ?></td>
                            <td><?php echo $data->getItemName($row['item_id']); ?></td>
                            <td><?php echo $row['price']; ?></td>
                            <td><?php echo $row['order_qty'] * $row['price']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4">TOTAL</td>
                            <td><?php echo $sum; ?></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="col">
                <table border="1">
                    <tr>
                        <th>BARCODE</th>
                        <th>ITEM</th>
                        <th>QTY ON HAND</th>
                    </tr>
                    <?php foreach ($data->getInventory() as $row) : ?>
                        <tr>
                            <td><?php echo $row['barcode']; ?></td>
                            <td><?php echo $row['description']; ?></td>
                            <td><?php echo $row['qty_on_hand']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <form action="trigger.php" method="post">
                    <label for="">QTY/BARCODE</label>
                    <input name="qtybarcode" onmouseover="this.focus();" type="text" <?php echo empty($_SESSION['new']) != 0 ? 'disabled' : '' ?>>
                </form>
            </div>
        </div>
    </div>

</body>
</html>