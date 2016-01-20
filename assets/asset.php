<?php
abstract class asset
{

    public static function replace($data)
    {
        $matches = array();
        preg_match_all('/ src="([^"]+)"/', $data, $matches, PREG_PATTERN_ORDER);
        $data = self::replace_matches( $data, $matches, ' src="/', '"' );

        $matches = array();
        preg_match_all("/ src='([^']+)'/", $data, $matches, PREG_PATTERN_ORDER);
        $data = self::replace_matches( $data, $matches, ' src="/', '"' );

        $matches = array();
        preg_match_all('/ href="([^"]+)"/', $data, $matches, PREG_PATTERN_ORDER);
        $data = self::replace_matches( $data, $matches, ' href="/', '"' );

        $matches = array();
        preg_match_all("/ href='([^']+)'/", $data, $matches, PREG_PATTERN_ORDER);
        $data = self::replace_matches( $data, $matches, ' href="/', '"' );
        
        $matches = array();
        preg_match_all("/url\(([^\)]+)\)/", $data, $matches, PREG_PATTERN_ORDER);
        $data = self::replace_matches( $data, $matches, 'url(/', ')' );
        //print_r($matches);
        return $data;
    }
    
    private static function replace_matches( $data, $matches, $tag_start, $tag_end )
    {
        $len = count($matches[0]);
        for($i=0; $i<$len; $i++)
        {
            $src = $matches[0][$i];
            $url = trim($matches[1][$i]);
            $url = trim($url, '"');
            $url = trim($url, "'");
            
            if(strlen($url)>0 && substr($url, 0, 1)!='/')
            {
                if(substr($url, 0, 7)=='http://') continue;
                if(substr($url, 0, 8)=='https://') continue;
                if(substr($url, 0, 10)=='tencent://') continue;
                if(substr($url, 0, 11)=='javascript:') continue;

                $data = str_replace($src, $tag_start.$url.$tag_end, $data);
            }
        }
        
        return $data;
    }

    
    private static $home_page_html = null;
    public static function get_home_page_html()
    {
        if(self::$home_page_html==null)
        {
//             $url = 'http://www.123121.com/sales-promotion.html';
            self::$home_page_html = self::http($url);
        }
        return self::$home_page_html;
    }
    private static $category_page_html = null;
    public static function get_category_page_html()
    {
        if(self::$category_page_html==null)
        {
//             $url = 'http://www.123121.com/sales-promotion.html';
            self::$category_page_html = self::http($url);
        }
        return self::$category_page_html;
    }

    private static $goods_detail_page_html = null;
    public static function get_goods_detail_page_html()
    {
        if(self::$goods_detail_page_html==null)
        {
//             $url = 'http://www.123121.com/goods-1059.html';
            self::$goods_detail_page_html = self::http($url);
        }
        return self::$goods_detail_page_html;
    }
    
    public static function http( $url )
    {
        $handle = curl_init();
        curl_setopt( $handle, CURLOPT_URL, $url);
        curl_setopt( $handle, CURLOPT_COOKIE, 'ECS_ID='.$_COOKIE['ECS_ID'].'&_real_ip='.$_COOKIE['_real_ip']);
        curl_setopt( $handle, CURLOPT_HEADER, false );	//������ͷ��Ϣ
        curl_setopt( $handle, CURLOPT_RETURNTRANSFER, 1);
        $reponse = curl_exec( $handle );
        curl_close( $handle );
        return $reponse;
    }
    
}
?>