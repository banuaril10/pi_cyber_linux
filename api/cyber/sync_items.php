<?php include "../../config/koneksi.php";
$connec->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$ll = "select * from ad_morg where isactived = 'Y'";
$query = $connec->query($ll);

while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
    $idstore = $row['ad_morg_key'];
}
function get($url)
{
    $curl = curl_init();
    curl_setopt_array(
        $curl,
        array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
        )
    );

    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}
$url = $base_url . '/store/items/get_items.php?idstore=' . $idstore;

$hasil = get($url);
$j_hasil = json_decode($hasil, true);

$s = array();

try {
    $truncate = "TRUNCATE pos_mproduct";
    $statement = $connec->prepare($truncate);
    $statement->execute();


    $values = array();
    $sql_items = "INSERT INTO pos_mproduct (ad_mclient_key, ad_morg_key, isactived, insertdate, insertby, postby, postdate, m_product_id, m_product_category_id, sku, 
            name, description, price, stockqty, shortcut, barcode, tag, idcat, idsubcat, idsubitem)
                    VALUES(:ad_mclient_key, :ad_morg_key, :isactived, :insertdate, :insertby, :postby, :postdate, :m_product_id, :m_product_category_id, :sku,
                    :name, :description, :price, :stockqty, :shortcut, :barcode, :tag, :idcat, :idsubcat, :idsubitem)";

    $stmt_items = $connec->prepare($sql_items);

    foreach ($j_hasil as $key => $value) {

        $itemkey = $value['itemkey'];
        $id = $value['id'];
        $sku = $value['sku'];
        $barcode = $value['barcode'];
        $shortcut = $value['shortcut'];
        $name = str_replace("'", "\'", $value['name']);
        $idcat = $value['idcat'];
        $idsubcat = $value['idsubcat'];
        $idsubitem = $value['idsubitem'];
        $panjang = $value['panjang'];
        $lebar = $value['lebar'];
        $tinggi = $value['tinggi'];
        $berat = $value['berat'];
        $imageurl = $value['imageurl'];
        $insertdate = $value['insertdate'];
        $updatedate = $value['updatedate'];
        $tag = $value['tag'];
        $category = $value['category'];
        $subcategory = $value['subcategory'];
        $subitem = $value['subitem'];
        $isactived = $value['isactived'];

        if ($idcat == "") {
            $idcat = 0;
        }

        if ($idsubcat == "") {
            $idsubcat = 0;
        }

        if ($idsubitem == "") {
            $idsubitem = 0;
        }

            $stmt_items->execute(array(
                ':ad_mclient_key' => $ad_mclient_key,
                ':ad_morg_key' => $idstore,
                ':isactived' => $isactived,
                ':insertdate' => date("Y-m-d H:i:s"),
                ':insertby' => 'SYSTEM',
                ':postby' => 'SYSTEM',
                ':postdate' => date("Y-m-d H:i:s"),
                ':m_product_id' => $id,
                ':m_product_category_id' => $idcat,
                ':sku' => $sku,
                ':name' => $name,
                ':description' => '',
                ':price' => 0,
                ':stockqty' => 0,
                ':shortcut' => $shortcut,
                ':barcode' => $barcode,
                ':tag' => $tag,
                ':idcat' => $idcat,
                ':idsubcat' => $idsubcat,
                ':idsubitem' => $idsubitem
            ));
        

    //     $s[] = "('" . $ad_mclient_key . "','" . $idstore . "', '" . $isactived . "', '" . date("Y-m-d H:i:s") . "', 'SYSTEM', 'SYSTEM', 
    // '" . date("Y-m-d H:i:s") . "', '" . $id . "', '" . $idcat . "', '" . $sku . "',
    // '" . $name . "', '', '0', '0', '" . $shortcut . "', '" . $barcode . "','" . $tag . "', '" . $idcat . "', '" . $idsubcat . "', '" . $idsubitem . "')";
    }

    if ($s == null) {
        $json = array(
            "status" => "FAILED",
            "message" => "Data Not Found",
        );
        echo json_encode($json);
        die();
    }

    //truncate

    // try {
        

    //     $values = implode(", ", $s);
    //     $insert = "insert into pos_mproduct (ad_mclient_key, ad_morg_key, isactived, insertdate, insertby, postby, postdate, m_product_id, m_product_category_id, sku, 
    //         name, description, price, stockqty, shortcut, barcode, tag, idcat, idsubcat, idsubitem)
    //         VALUES " . $values . ";";

    //     $statement = $connec->prepare($insert);
    //     $statement->execute();
    // } catch (PDOException $e) {
    //     $json = array(
    //         "status" => "FAILED",
    //         "message" => $e->getMessage(),
    //     );
    //     echo json_encode($json);
    //     die();
    // }



    $result = $statement->fetchAll(PDO::FETCH_ASSOC);

    if ($result) {
        $json = array(
            "status" => "OK",
            "message" => "Data Inserted",
        );
    } else {
        $json = array(
            "status" => "FAILED",
            "message" => "Data Not Inserted",
        );
    }
    echo json_encode($json);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}



?>