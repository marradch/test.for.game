<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Dept_For_Comments extends CI_Migration {

    public function up()
    {
        $fields = array(
            'dept' => array(
                'unsigned' => true,
                'type' => 'tinyint',
            ),
        );
        $this->dbforge->add_column('comment', $fields);

        $data = App::get_ci()->s->from('comment')->many();

        $this->db->update('comment', ['dept' => 1]);
    }
}