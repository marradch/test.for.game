<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Parent_For_Comments extends CI_Migration {

    public function up()
    {
        $fields = array(
            'path' => array(
                'type' => 'varchar(20)',
            ),
        );
        $this->dbforge->add_column('comment', $fields);

        $data = App::get_ci()->s->from('comment')->order('assign_id ASC, time_created ASC')->many();

        $assignPrev = 0;
        $num = 0;

        foreach ($data as $row) {
            if($row['assign_id'] <> $assignPrev) {
                $num = 1;
            } else {
                $num = $num + 1;
            }
            $this->db->update('comment', ['path' => $num], array('id' => $row['id']));
            $assignPrev = $row['assign_id'];
        }
    }
}