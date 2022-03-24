<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_geojson_table extends CI_Migration {

	public function up()
	{
		$this->dbforge->add_field(array(
			'geojson_id' => array(
				'type' => 'INT',
				'constraint' => 11,
				'auto_increment' => TRUE
			),
			'data' => array(
				'type' => 'TEXT',
			),
			'shp_name' => array(
				'type' => 'TEXT',
			),
            'geojson_status' => array(
                    'type' => 'INT',
                    'default' => 1,
            ),
            'dt_added DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP',
		));
                
                $this->dbforge->add_key('geojson_id', TRUE);
		$this->dbforge->create_table('geojson', TRUE);
	}

	public function down()
	{
		$this->dbforge->drop_table('geojson');
	}
}
