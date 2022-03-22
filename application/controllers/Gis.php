<?php

// Register autoloader
require_once('vendor/gasparesganga/php-shapefile/src/Shapefile/ShapefileAutoloader.php');
Shapefile\ShapefileAutoloader::register();

// Import classes
use Shapefile\Shapefile;
use Shapefile\ShapefileException;
use Shapefile\ShapefileReader;

class Gis extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        // Your own constructor code
    }

    public function index()
    {
        $this->load->view('leaflet/map_v');
    }

    public function convert_to_geo_json()
    {
        try {
            // Open Shapefile
            $Shapefile = new ShapefileReader('shape/test.shp');
            
            // Read all the records
            while ($Geometry = $Shapefile->fetchRecord()) {
                // Skip the record if marked as "deleted"
                if ($Geometry->isDeleted()) {
                    continue;
                }
                
                // // Print Geometry as an Array
                // print_r($Geometry->getArray());
                
                // // Print Geometry as WKT
                // print_r($Geometry->getWKT());
                
                // Print Geometry as GeoJSON
                print_r($Geometry->getGeoJSON());
                
                // Print DBF data
                // print_r($Geometry->getDataArray());
            }

        } catch (ShapefileException $e) {
            // Print detailed error information
            echo "Error Type: " . $e->getErrorType()
                . "\nMessage: " . $e->getMessage()
                . "\nDetails: " . $e->getDetails();
        }
    }
}
