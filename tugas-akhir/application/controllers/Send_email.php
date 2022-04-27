<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Send_email extends CI_Controller
{

    /**
     * Kirim email dengan SMTP Gmail
     */
    public function index()
    {
        // Konfigurasi email

        $config = [
            'mailtype'      => 'html',
            'charset'       => 'utf-8',
            'protocol'      => 'smtp',
            'smtp_host'     => 'smtp.gmail.com',
            'smtp_user'     => 'rofinaaulia11@gmail.com',
            'smtp_pass'     => '01aulia11',
            'smtp_crypto'   => 'ssl',
            'smtp_port'     => 465,
            'crlf'          => "\r\n",
            'newline'       => "\r\n"
        ];

        // Load library email dan konfigurasinya
        $this->load->library('email', $config);

        // Email dan nama pengirim
        $this->email->from('rofinaaulia11@gmail.com', 'Aulia');

        // Email Penerima
        $this->email->to('auliarofina11@gmail.com');

        // Lampiran email
        // $this->email->attach('...');

        // Subject email
        $this->email->subject('Tugas Akhir Fina');

        // Isi email
        $this->email->message('Seng Penting Yakin!');

        // Tampilkan Pesan sukses atau error
        if ($this->email->send()) {
            echo 'Sukses! email berhasil dikirim.';
        } else {
            echo 'Error! email tidak dapat dikirim.';
        }
    }
}
