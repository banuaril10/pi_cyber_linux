<?php include "../../config/koneksi.php";
//get data product

$arrproduct = $_POST['arrproduct'];
$data = array();


$arr = json_encode($arrproduct);

foreach ($arrproduct as $r) {
    $data[] = $r;
}

// print_r($data);

$get_store_code = $connec->query("select value from ad_morg");
foreach ($get_store_code as $r) {
    $storecode = $r['value'];
}


$implode = implode("','", $data);

$qq = "select a.*, (a.price - b.discount) afterdiscount, b.todate from pos_mproduct a
inner join (select * from pos_mproductdiscount where date(now()) between fromdate and todate) b on a.sku = b.sku
where a.sku in ('" . $implode . "')";
$statement = $connec->query($qq);

$date_now = date('d/m/Y');

$products = array();
foreach ($statement as $r) {

    $products[] = $r['sku']."|".$r['name']."|".$r['price']."|".$date_now."|".$r['rack']."|".$r['afterdiscount']."|".$r['todate']."|".$r['barcode'];
}

$json = array(
    "status" => "SUCCESS",
    "products" => $products,
);
echo json_encode($json);




// print_r($qq);



// $query = "select * from pos_mproduct ";


?>