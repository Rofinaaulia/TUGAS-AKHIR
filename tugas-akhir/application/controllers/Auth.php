<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Auth extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
    }
    public function index()
    {
        if ($this->session->userdata('email')) {
            redirect('user');
        }

        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');

        if ($this->form_validation->run() == false) {
            $data['title'] = 'Login Page';
            $this->load->view('templates/auth_header', $data);
            $this->load->view('auth/login');
            $this->load->view('templates/auth_footer');
        } else {
            // validasinya sukses
            $this->_login();
        }
    }



    private function _login()
    {
        $email = $this->input->post('email');
        $password = $this->input->post('password');

        $user = $this->db->get_where('user', ['email' => $email])->row_array();
        //var_dump($user);
        //die;
        //untuk ngecek data yg benar

        //jika usernya ada
        if ($user) {
            // jika usernya aktif
            if ($user['is_active'] == 1) {
                // cek password 
                if (password_verify($password, $user['password'])) {
                    $data = [
                        'email' => $user['email'],
                        'role_id' => $user['role_id']
                    ];
                    $this->session->set_userdata($data);
                    if ($user['role_id'] == 1) {
                        redirect(base_url('admin'));
                    } else {

                        redirect(base_url('user'));
                    }
                } else {
                    $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Wrong Password!</div');
                    redirect(base_url('auth'));
                }
            } else {
                $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">This Email has not been activated!</div');
                redirect(base_url('auth'));
            }
        } else {
            $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Email is not registered!</div');
            redirect(base_url('auth'));
        }
    }


    public function registration()
    {
        if ($this->session->userdata('email')) {
            redirect('user');
        }

        $this->form_validation->set_rules('name', 'Name', 'required|trim');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|valid_email|is_unique[user.email]', [
            'is_unique' => 'this email has already registered!'
        ]);
        $this->form_validation->set_rules('password1', 'password', 'required|trim|min_length[3]|matches[password2]', [
            'matches' => 'password dont match!',
            'min_length' => 'password too short!'
        ]);
        $this->form_validation->set_rules('password2', 'password', 'required|trim|matches[password1]');

        if ($this->form_validation->run() == false) {
            $data['title'] = 'E-Survei Kepuasan Pelayanan';
            $this->load->view('templates/auth_header', $data);
            $this->load->view('auth/registration');
            $this->load->view('templates/auth_footer');
        } else {
            $data = [
                'name' => htmlspecialchars($this->input->post('name', true)),
                'email' => htmlspecialchars($this->input->post('email', true)),
                'image' => 'default.jpg',
                'password' => password_hash(
                    $this->input->post('password1'),
                    PASSWORD_DEFAULT
                ),
                'role_id' => 2,
                'is_active' => 1,
                'date_created' => time()
            ];
            // ini buat ngecek data yang masuk udah bener/ngga
            // kalo ngerasa tadi datanya udah bener code dibawah ini tadi dihapus gapapa
            // kalo divardump trus exit, itu ngga masuk ke database, jadi pure buat ngecek aja
            // var_dump($data);
            // exit;

            $this->db->insert('user', $data);

            //$this->_sendEmail();

            //$this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Congratulation! yaur account has been
            //created. Please Login</div>');
            //redirect(base_url('auth'));
        }
    }

    // private function _sendEmail()
    // {
    // $config = [
    //   'protocol'  => 'smtp',
    // 'smtp_host' => 'ss://smtp.googlemail.com',
    // 'smtp_user' => 'rofinaaulia11@gmail.com',
    // 'smtp_pass' => '321321',
    // 'smtp_port' => 25,
    // 'mailtype'  => 'html',
    // 'charset'   => 'utf-8',
    // 'newline'   => "\r\n"
    // ];

    // $this->load->library('email', $config);

    // $this->email->from('rofinaaulia11@gmail.com', 'Rofina Aulia');
    //  $this->email->to('auliarofina11@gmail.com');
    // $this->email->subject('Testing Tugas Akhir');
    // $this->email->message('Seng Penting Yakin!');

    // if ($this->email->send()) {
    //   return true;
    // } else {
    // echo $this->email->print_debugger();
    // die;
    // }
    // }



    public function logout()
    {
        $this->session->unset_userdata('email');
        $this->session->unset_userdata('role_id');

        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">You have been logged out!</div>');
        redirect(base_url('auth'));
    }


    public function blocked()
    {
        $this->load->view('auth/blocked');
    }


    public function forgotpassword()
    {
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');

        if ($this->form_validation->run()  == false) {
            $data['title'] = 'Forgot Password';
            $this->load->view('templates/auth_header', $data);
            $this->load->view('auth/forgot-password');
            $this->load->view('templates/auth_footer');
        } else {
            $email = $this->input->post('email');
            $user = $this->db->get_where('user', ['email' => $email, 'is_active' => 1])->row_array();

            if ($user) {
                $token = base64_encode(random_bytes(32));
                $user_token = [
                    'email' => $email,
                    'token' => $token,
                    'date_created' => time()
                ];

                $this->db->insert('user_token', $user_token);
                // $this->_sendEmail($token, 'forgot');

                $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Please chek your email to reset your password!</div>');
                redirect(base_url('auth/forgotpassword'));
            } else {
                $this->session->set_flashdata('message', '<div class="alert alert-danger" role="alert">Email is not registration or activated!</div>');
                redirect(base_url('auth/forgotpassword'));
            }
        }
    }
}
