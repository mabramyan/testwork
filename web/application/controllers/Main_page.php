<?php

use Model\Boosterpack_model;
use Model\Post_model;
use Model\User_model;
use Model\Login_model;
use Model\Comment_model;

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
        if (is_prod()) {
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
        $posts = Post_model::preparation_many(Post_model::get_all(), 'default');
        return $this->response_success(['posts' => $posts]);
    }

    public function get_boosterpacks()
    {
        $posts = Boosterpack_model::preparation_many(Boosterpack_model::get_all(), 'default');
        return $this->response_success(['boosterpacks' => $posts]);
    }

    public function get_post(int $post_id)
    {
        $post = new Post_model($post_id);
        if (!$post->get_id()) {
            return $this->response_error(\System\Libraries\Core::RESPONSE_GENERIC_UNAVAILABLE);
        }
        $post = Post_model::preparation($post, 'full_info');
        return $this->response_success(['post' => $post]);


        //TODO получения поста по id
    }


    public function comment()
    {
        $app = App::get_ci();

        if (!User_model::is_logged()) {
            return $this->response_error(System\Libraries\Core::RESPONSE_GENERIC_NEED_AUTH);
        }

        $user = User_model::get_user();

        $app->load->library('form_validation');

        $data = $app->input->post();
        $config = [
            [
                'field' => 'postId',
                'rules' => 'required',
            ],
            [
                'field' => 'commentText',
                'rules' => 'required',
            ],
        ];

        $app->form_validation->set_data($data);
        $app->form_validation->set_rules($config);


        if ($app->form_validation->run() == FALSE) {
            return $this->response_error(\System\Libraries\Core::RESPONSE_GENERIC_WRONG_PARAMS);
        }


        Comment_model::create(
            ['assign_id' => $data['postId'],
                'text' => $data['commentText'],
                'reply_id' => (int)(!empty($data['reply_id']) ? $data['reply_id'] : 0),
                'user_id' => $user->get_id(),
            ]);


        return $this->response_success();
        //TODO логика комментирования поста
    }


    public function login()
    {
        //TODO
        $app = App::get_ci();

        if ($app->input->server('REQUEST_METHOD') != "POST") {
            return $this->response_error(\System\Libraries\Core::RESPONSE_GENERIC_UNAVAILABLE);
        }
        if (User_model::is_logged()) {
            return $this->response_error(System\Libraries\Core::RESPONSE_GENERIC_WRONG_PARAMS);
        }

        $app->load->library('form_validation');
        $config = [
            [
                'field' => 'login',
                'rules' => 'required',
            ],
            [
                'field' => 'password',
                'rules' => 'required',
            ],
        ];

        $data = $app->input->post();

        $app->form_validation->set_data($data);
        $app->form_validation->set_rules($config);

        if ($app->form_validation->run() == FALSE) {
            return $this->response_error(\System\Libraries\Core::RESPONSE_GENERIC_WRONG_PARAMS);
        }
        $user = Login_model::login($data['login'], $data['password']);
        if (!$user) {
            return $this->response_error(\System\Libraries\Core::RESPONSE_GENERIC_WRONG_PARAMS);
        }

        return $this->response_success(['user' => $user]);
    }


    public function logout()
    {
        //TODO
        Login_model::logout();
        redirect('/');
    }

    public function add_money()
    {
        if (!User_model::is_logged()) {
            return $this->response_error(System\Libraries\Core::RESPONSE_GENERIC_NEED_AUTH);
        }

        $sum = (float)App::get_ci()->input->post('sum');

        //TODO логика добавления денег
    }

    public function buy_boosterpack()
    {
        // Check user is authorize
        if (!User_model::is_logged()) {
            return $this->response_error(System\Libraries\Core::RESPONSE_GENERIC_NEED_AUTH);
        }

        //TODO логика покупки и открытия бустерпака по алгоритмку профитбанк, как описано в ТЗ
    }


    /**
     *
     * @return object|string|void
     */
    public function like_comment(int $comment_id)
    {
        // Check user is authorize
        if (!User_model::is_logged()) {
            return $this->response_error(System\Libraries\Core::RESPONSE_GENERIC_NEED_AUTH);
        }

        //TODO логика like comment(remove like у юзерa, добавить лай к комменту)
    }

    /**
     * @param int $post_id
     *
     * @return object|string|void
     */
    public function like_post(int $post_id)
    {
        // Check user is authorize
        if (!User_model::is_logged()) {
            return $this->response_error(System\Libraries\Core::RESPONSE_GENERIC_NEED_AUTH);
        }

        //TODO логика like post(remove like у юзерa, добавить лай к посту)
    }


    /**
     * @return object|string|void
     */
    public function get_boosterpack_info(int $bootserpack_info)
    {
        // Check user is authorized
        if (!User_model::is_logged()) {
            return $this->response_error(System\Libraries\Core::RESPONSE_GENERIC_NEED_AUTH);
        }


        //TODO получить содержимое бустерпак
    }
}
