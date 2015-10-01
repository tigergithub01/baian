<?php

include_once dirname(__FILE__).DIRECTORY_SEPARATOR.'asset.php';


$data_file_path = dirname(__FILE__).DIRECTORY_SEPARATOR.'header.php';

$data = '';

/*
if(file_exists($data_file_path) && ((time()-filemtime($data_file_path))<60))
{
    $data = file_get_contents($data_file_path);
}
else
{
*/
    $cache = asset::get_category_page_html();

    $pos_start = strpos($cache, '<div data-sign="header-start" style="display:none;"></div>');
    $pos_end = strpos($cache, '<div data-sign="header-end" style="display:none;"></div>');

    $data = substr($cache, $pos_start, $pos_end-$pos_start);

    $data = asset::replace( $data );

    //file_put_contents($data_file_path, $data);
//}

echo $data;
?>