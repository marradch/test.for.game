<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_Post_And_Comments_Likes extends CI_Migration {

    public function up()
    {
        $fields = array(
            'likes' => array(
                'unsigned' => true,
                'type' => 'int',
                'default' => 0,
            ),
        );
        $this->dbforge->add_column('comment', $fields);
        $this->dbforge->add_column('post', $fields);
    }
}