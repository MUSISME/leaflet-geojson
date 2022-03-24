<?php
class Gis extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('gis_m');
    }

    public function index()
    {
        $rs_data = $this->gis_m->get_geojson_list();
        $this->load->view('leaflet/map_v',$rs_data);
    }

    public function upload_file(){
        $this->gis_m->upload_file();
    }

    public function geojson_data(){
        $rs_data = $this->gis_m->get_geojson_data();
        echo json_encode($rs_data);
    }
}
