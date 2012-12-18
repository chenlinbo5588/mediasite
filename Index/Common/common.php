<?php
    function showEnable($key) {
        $key = strval($key);
        $show = '';
        switch($key) {
            case '0':
                $show = 'paused';
                break;
            case '1':
                $show = 'valid';
                break;
            case '2':
                $show = 'deleted';
                break;
            default:
                $show = 'valid';
                break;
        }
        return $show;
    }
?>