<?php

// Register autoloader
require_once('vendor/gasparesganga/php-shapefile/src/Shapefile/ShapefileAutoloader.php');
Shapefile\ShapefileAutoloader::register();

// Import classes
use Shapefile\Shapefile;
use Shapefile\ShapefileException;
use Shapefile\ShapefileReader;

class Gis_m extends CI_Model{

    public function upload_file(){

        $config['upload_path'] = './'.PATH_SHAPEFILE;
        $config['allowed_types'] = '*';
        $config['max_size'] = 2048; /* max 2MB */
        $config['encrypt_name'] = FALSE;

        $this->load->library('upload', $config);

        $files = array();
        foreach ($_FILES['files']['name'] as $key => $file) {
            $_FILES['file']['name']= $file;
            $_FILES['file']['type']= $_FILES['files']['type'][$key];
            $_FILES['file']['tmp_name']= $_FILES['files']['tmp_name'][$key];
            $_FILES['file']['error']= $_FILES['files']['error'][$key];
            $_FILES['file']['size']= $_FILES['files']['size'][$key];

            $file_name = 'shapefile' .'_'.date('YmdHis');

            $files[] = $file_name;

            $config['file_name'] = $file_name;

            $this->upload->initialize($config);

            if ($this->upload->do_upload('file')) {
                $raw_name = $this->upload->data('raw_name');
            } else {
                return false;
            }
        }
        $this->convert_to_geo_json($raw_name);

        return $files;
    }

    public function convert_to_geo_json($file_name)
    {
        $geojson = '';

        try {
            // Open Shapefile
            $Shapefile = new ShapefileReader(PATH_SHAPEFILE.$file_name.'.shp');
            
            // Read all the records
            while ($Geometry = $Shapefile->fetchRecord()) {
                // Skip the record if marked as "deleted"
                if ($Geometry->isDeleted()) {
                    continue;
                }
                
                $geojson .= $Geometry->getGeoJSON();
            }
            $this->save_geojson($geojson,$file_name);

        } catch (ShapefileException $e) {
            // Print detailed error information
            echo "Error Type: " . $e->getErrorType()
                . "\nMessage: " . $e->getMessage()
                . "\nDetails: " . $e->getDetails();
        }
    }

    private function save_geojson($geojson,$file_name){
        $insert['data'] = $geojson;
        $insert['dt_added'] = date('Y-m-d H:i:s');
        $insert['shp_name'] = $file_name;

        $this->db->insert('geojson',$insert);

        return redirect('gis');
    }

    public function get_geojson_data(){
        $geo_data = '';

        $query = $this->db->get('geojson');

        if ($query->num_rows()>0) {
            $res = $query->result_array();

            foreach ($res as $data) {
                $geo_data .= $data['data'];
            }
        }
        return $geo_data;
    }

    public function get_geojson_list(){
        $res = [];
        $query = $this->db->get('geojson');

        if ($query->num_rows()>0) {
            $res['geojson_data'] = $query->result_array();
        }

        return $res;
    }
}