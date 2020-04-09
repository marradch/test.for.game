<?php

/**
 * Created by PhpStorm.
 * User: mr.incognito
 * Date: 10.11.2018
 * Time: 21:36
 */
class Main_page extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        App::get_ci()->load->model('User_model');
        App::get_ci()->load->model('Login_model');
        App::get_ci()->load->model('Post_model');

        if (is_prod())
        {
            die('In production it will be hard to debug! Run as development environment!');
        }
    }

    public function index()
    {
        $user = User_model::get_user();

        App::get_ci()->load->view('main_page', ['user' => User_model::preparation($user, 'default')]);
    }

    public function get_all_posts()
    {
        $posts =  Post_model::preparation(Post_model::get_all(), 'main_page');
        return $this->response_success(['posts' => $posts]);
    }

    public function get_post($post_id){ // or can be $this->input->post('news_id') , but better for GET REQUEST USE THIS

        $post_id = intval($post_id);

        if (empty($post_id)){
            return $this->response_error(CI_Core::RESPONSE_GENERIC_WRONG_PARAMS);
        }

        try
        {
            $post = new Post_model($post_id);
        } catch (EmeraldModelNoDataException $ex){
            return $this->response_error(CI_Core::RESPONSE_GENERIC_NO_DATA);
        }


        $posts =  Post_model::preparation($post, 'full_info');
        return $this->response_success(['post' => $posts]);
    }


    public function comment(){

        if (!User_model::is_logged()){
            return $this->response_error(CI_Core::RESPONSE_GENERIC_NEED_AUTH);
        }

        $path = $this->input->post('path', false);
        $postId = $this->input->post('postId', false);
        $text = $this->input->post('text', false);

        if (empty($postId) || empty($text)){
            return $this->response_error(CI_Core::RESPONSE_GENERIC_WRONG_PARAMS);
        }

        try
        {
            $post = new Post_model($postId);
        } catch (EmeraldModelNoDataException $ex){
            return $this->response_error(CI_Core::RESPONSE_GENERIC_NO_DATA);
        }

        $dept = $path ? count(explode('.', $path)) + 1 : 1;
        $insert_path = Comment_model::get_insert_path($postId, $path);

        Comment_model::create([
            'user_id' => User_model::get_session_id(),
            'assign_id' => $postId,
            'text' => $text,
            'path' => $insert_path,
            'dept' => $dept
        ]);

        $posts =  Post_model::preparation($post, 'full_info');
        return $this->response_success(['post' => $posts]);
    }


    public function login()
    {
        $login = $this->input->post('login', false);
        $password = $this->input->post('password');

        $user = User_model::get_by_email($login);

        if (!$user) {
            return $this->response_error('User not found', [], 401);
        }

        if (password_verify($password, $user['password']))
        {
            return $this->response_error('Password is wrong', [], 401);
        }
        Login_model::start_session($user['id']);

        return $this->response_success(['user' => $user['id']]);
    }


    public function logout()
    {
        Login_model::logout();
        redirect(site_url('/'));
    }

    public function add_money(){
        // todo: add money to user logic
        return $this->response_success(['amount' => rand(1,55)]);
    }

    public function buy_boosterpack(){
        // todo: add money to user logic
        return $this->response_success(['amount' => rand(1,55)]);
    }


    public function like(){
        if (!User_model::is_logged()){
            return $this->response_error(CI_Core::RESPONSE_GENERIC_NEED_AUTH);
        }

        $post_id = $this->input->get('post_id', false);
        $comment_id = $this->input->get('comment_id', false);

        if (empty($post_id) && empty($comment_id)){
            return $this->response_error(CI_Core::RESPONSE_GENERIC_WRONG_PARAMS);
        }

        if ($post_id) {
            $model = new Post_model($post_id);
        } else {
            $model = new Comment_model($comment_id);
        }

        $likes = $model->get_likes() + 1;
        $model->set_likes($likes);

        return $this->response_success(['likes' => $likes]);
    }

}
