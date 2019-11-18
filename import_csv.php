<?php
    require_once('Transactions.php');
    if(isset($_POST["submit"]))
    {
        $filename = $_FILES['file']['name'];
        $path_parts = pathinfo($filename);
        $ext = $path_parts['extension'];
        if($ext == 'csv'){
            $file = $_FILES['file']['tmp_name'];
            if (($handle = fopen($file, "r")) !== FALSE) {
                 $index = 0;
                while (($data = fgetcsv($handle)) !== FALSE) {
                    if ($index > 1) {
                        $trans = new Transactions();
                        $item_id = $trans->insertItemData($data[0], $data[1], $data[2], $data[3]);
                        $trans->insertPriceCodeData($item_id, $data[4], $data[5], $data[6]);
                        $trans->insertInventory($item_id, $data[7], $data[8], $data[9]);
                    }
                    $index++;
                }
                fclose($file);
            }
        }
    }
    header("Location: index.php");
?>

