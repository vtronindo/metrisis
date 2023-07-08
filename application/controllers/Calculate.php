<?php defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Jakarta');

class Calculate extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("calculate_model");

    }

    public function getTarget(){
        $post = $this->input->get();
        $data = $this->calculate_model;
        $jose = $data->getActualByProses($post["line"]);
        $nt = array('result' => $jose);
        echo json_encode($jose);
    }

    public function tambahData(){
        $tgle = date('Y-m-d');
        $post = $this->input->get();
        $data = $this->calculate_model;
        $varine = array(
            'Tanggal' => $tgle,
            'Line' => $post["line"],
            'Proses' => $post["pros"],
            'Push_Time' => $post["str"],
            'Status' => $post["stb"],
        );
        $jose = $data->insertDataAndon($varine, $post["line"],$post["pros"]);
        echo json_encode($jose);
    } 

    public function startDT(){
        $tgle = date('Y-m-d');
        $post = $this->input->get();
        $data = $this->calculate_model;
        $line = $post["line"];
        $pros = $post["pros"];

        $syrat = "Tanggal = '$tgle' AND '$line' AND Proses = '$pros' AND Start_Time != '' AND  Finish_Time = ''";

        $varine = array(
            'Tanggal' => $tgle,
            'Line' => $post["line"],
            'Proses' => $post["pros"],
            'Start_Time' => $post["str"],
            'Kode_DT' => $post["kdn"],
        );

        $jose = $data->insertStartDownTime($varine, $syrat);
        echo json_encode($jose);
    } 

    public function finishDT(){
        $tgle = date('Y-m-d');
        $post = $this->input->get();
        $data = $this->calculate_model;
        $line = $post["line"];
        $pros = $post["pros"];
        $syrat = "Tanggal = '$tgle' AND '$line' AND Proses = '$pros' AND Start_Time != '' AND Finish_Time = ''";
        $varine = array(
            'Finish_Time' => $post["str"],
        );

        $jose = $data->insertFinishDownTime($varine, $syrat);
        echo json_encode($jose);
    } 

    public function setStartProd(){
        $post = $this->input->get();
        $data = $this->calculate_model;
        $syrat = array(
            'Status_Proses' => $post["stp"],
            'Line' => $post["line"],
        );

        $varine = array(
            'Start_Time' => $post["str_time"],
            'Rest_Time' => $post["rst_time"],
        );
        $jose = $data->updateDataTarget($varine, $syrat);
        echo json_encode($jose);
    } 



}