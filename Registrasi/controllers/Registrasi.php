<?php 

/**
* 
*/
class Registrasi extends MX_Controller
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->helper(array('form', 'url'));
		$this->load->library(array('session', 'form_validation', 'email'));
		$this->load->database();
		$this->load->model('registrasi_model');
	}

	function index() {
        $this->load->view('layout/header');
        $this->load->view('layout/nav');
        $this->load->view('vMain');
        $this->load->view('layout/footer');
		// $this->load->view('vRegistrasi');
	}

	function register() {
        //set validation rules
        $this->form_validation->set_rules('nama_depan', 'Nama Depan', 'trim|required');
        $this->form_validation->set_rules('nama_belakang', 'Nama Belakang', 'trim|required');
        $this->form_validation->set_rules('email', 'Email ID', 'trim|required');
        $this->form_validation->set_rules('password', 'Password', 'trim|required|matches[cpassword]|md5');
        $this->form_validation->set_rules('cpassword', 'Confirm Password', 'trim|required');
        $this->form_validation->set_rules('id_tingkat', 'Tingkat', 'trim|required');

        //validate form input
        if ($this->form_validation->run() == FALSE) {
            // gagal
            // $this->load->view('vRegistrasi');
            $this->load->view('layout/header');
            $this->load->view('layout/nav');
            $this->load->view('vMain');
            $this->load->view('layout/footer');
        } else {
            //insert the user registration details into database
            $data = array(
                'nama_depan' => $this->input->post('nama_depan'),
                'nama_belakang' => $this->input->post('nama_belakang'),
                'email' => $this->input->post('email'),
                'password' => $this->input->post('password'),
                'id_tingkat' => $this->input->post('id_tingkat')
            );
            
            // insert form data into database
            if ($this->registrasi_model->insertUser($data))
            {
                // send email
                if ($this->registrasi_model->sendEmail($this->input->post('email')))
                {
                    // successfully sent mail
                    $this->session->set_flashdata('msg','<div class="alert alert-success text-center">You are Successfully Registered! Please confirm the mail sent to your Email-ID!!!</div>');
                    redirect('registrasi/register');
                }
                else
                {
                    // error
                    $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">Oops! Error.  Please try again later!!!</div>');
                    redirect('registrasi/register');
                }
            }
            else
            {
                // error
                $this->session->set_flashdata('msg','<div class="alert alert-danger text-center">Oops! Error.  Please try again later!!!</div>');
                redirect('registrasi/register');
            }
        }
    }

    function verify($hash=NULL)
    {
        if ($this->registrasi_model->verifyEmailID($hash))
        {
            $this->session->set_flashdata('verify_msg','<div class="alert alert-success text-center">Your Email Address is successfully verified! Please login to access your account!</div>');
            redirect('registrasi/register');
        }
        else
        {
            $this->session->set_flashdata('verify_msg','<div class="alert alert-danger text-center">Sorry! There is error verifying your Email Address!</div>');
            redirect('registrasi/register');
        }
    }
}

?>