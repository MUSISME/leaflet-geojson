<?php

function geojson_status($geojson_status){
    $row = '';
    
    if ($geojson_status == 1) {
        $row .= '<span class="badge bg-success">Active</span>';
    }elseif($geojson_status == 2){
        $row .= '<span class="badge bg-secondary">Inactive</span>';
    }

    return $row;
}