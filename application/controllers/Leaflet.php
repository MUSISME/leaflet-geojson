<?php

class Leaflet extends CI_Controller
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
}
