<?php
header('Content-Type:text/html;charset=utf-8;');
require_once '../../../local/xunsearch/sdk/php/lib/XS.php';
include "./mysql_conn.php";
try{
$xs = new XS('njw');
//$data = array('id'=>3,'title'=>'3测试平滑重建索引','norms'=>'测试规格','picture'=>'sdsf.jpg');
//更新添加文档

/*$doc = new XSDocument($data,'utf-8');
$res = $xs->index->add($doc);
print_r($res);*/
//删除文档
//$xs->index->del('7101');

//清空索引
//$xs->index->clean();

//平滑重建索引
//宣布开始重建索引
$xs->index->beginRebuild();

$sql = "select g.id id,g.title title,g.norms norms,i.picture picture from b2b_goods g INNER JOIN b2b_goods_images i ON g.id=i.goods_id limit 10  ";
$result = $db->query($sql);
while( $row = $result -> fetch_assoc ()) {
    //print_r($row);
    $doc = new XSDocument;
    $doc->setFields($row);
    //添加到索引数据库中
    $xs->index->add($doc);
    $xs->index->update($doc);
}

//告诉服务器重建索引完成
$xs->index->endRebuild();

}catch(XSException $e){
    echo $e;
}

?>
