<?php
class PagebarWidget extends Widget {
	 public function render($data){
	 	// 总页数
	 	$totalPage 		= 1;
	 	// 当前页码
	 	$curPage 		= 1;
	 	// 分页显示多少页码
	 	$count 			= 20;
	 	if(isset($data['totalPage'])) $totalPage = $data['totalPage'];
	 	if(isset($data['page'])) $curPage = $data['page'];
	 	if(isset($data['count'])) $count = $data['count'];
	 	
	 	if($curPage < 1) $curPage = 1;
	 	if($curPage > $totalPage) $curPage = $totalPage;
	 	
		$mod = $curPage % $count;
		if($mod != 0) {
		 	$start = ($curPage - $curPage % $count) + 1;
		 	$end = $start + $count - 1;
		}
		else {
			$start = $curPage - $count;
			$end = $curPage;
		}
	 	
	 	$pageNumbers = array();
	 	for($p = $start; $p <= $end; $p ++) {
	 		if($p < 1 || $p > $totalPage) continue;
	 		$pageNumbers[] = array('page' => $p);
	 	}
        $content = $this->renderFile('pagebar', array(
        	'firstPage' 	=> 1,
        	'prevPage' 		=> $curPage - 1 > 0 ? $curPage - 1 : 1,
        	'nextPage'		=> $curPage + 1 < $totalPage ? $curPage + 1 : $totalPage,
        	'lastPage'		=> $totalPage,
        	'curPage'		=> $curPage,
        	'pageNumbers'	=> $pageNumbers
        ));var_dump($content);

        if(preg_match_all('|\{(\w+)\}|Uis', $content, $matches, PREG_SET_ORDER)) {
            $replace = array();
            foreach($matches as $match) {
                if(isset($$match[1]))
                    $replace[$match[0]] = $$match[1];
            }
            if(!empty($replace))
                $content = str_replace(array_keys($replace), array_values($replace), $content);
        }
  		return $content;
    } 
}
?>