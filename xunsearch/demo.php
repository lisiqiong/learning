<?php
header('Content-Type:text/html;charset=utf-8;');
require_once("../../../local/xunsearch/sdk/php/lib/XS.php");
$xs = new XS('njw');
$search = $xs->search;
$index =  $xs->index;
//接收关键字

if(isset($_GET['q'])){    
    $q = trim($_GET['q']);
}else{
    $q = '';
}


$hot = $search->getHotQuery();
$related = array();
$docs = array();
if(!empty($q)){

    //接收分页
    if(isset($_GET['p'])){    
        $p = trim($_GET['p']);
    }else{
        $p = 1;
    }

    //接收搜索方式
    $type = !isset($_GET['type'])?'title':$_GET['type'];

    $n = XSSearch::PAGE_SIZE;
    $search->setLimit($n,($p-1)*$n);
    if($type=='norms'){
        $count = $search->count($type.":".$q);//关键字查询出来匹配总数据
        $docs = $search->search($type . ':' . $q);
    }else{        
       $count = $search->count($q);//关键字查询出来匹配总数据
       $docs = $search->search($q);
    }
    
    $total = $search->count();//数据库总的数据量
    $corrected = $search->getCorrectedQuery();//拼写纠错
    $related = $search->getRelatedQuery($q);

    //构造分页链接
    $baseurl = $_SERVER['SCRIPT_NAME'] . '?q=' . urlencode($q);
    // gen pager;
    if ($count > $n) {
        $pagenum = max($p - 5, 1);
        $pe = min($pagenum + 10, ceil($count / $n) + 1);
        $pager = '';
        do {
            $pager .= ($pagenum == $p) ? '<li class="disabled"><a>' . $p . '</a></li>' : '<li><a href="' . $baseurl . '&p=' . $pagenum . '">' . $pagenum . '</a></li>';
        } while (++$pagenum < $pe);
    }
}
include dirname(__FILE__) . '/search.tpl';

?>
