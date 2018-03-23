<?php
function preProc(){
    $result = array('vars'=>array(),'css'=>array(),'js'=>array());

    //
    //  Default files and values which will be used at every page.
    //
    //


    //Default CSS files
      $result['css'][] = '/res/css/main.css';

    //Default JS files
      $result['js'][] = '/res/js/jquery.min.js';
      $result['js'][] = '/res/js/common.js';

    //Default variables
    $result['vars']['Ver'] = 1;




    return $result;
}
?>
