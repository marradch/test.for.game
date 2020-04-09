<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Change_Password_Column extends CI_Migration {

    public function up()
    {
        $fields = array(
            'password' => array(
                'type' => 'varchar(60)',
            ),
        );
        $this->dbforge->modify_column('user', $fields);
    }
}