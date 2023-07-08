<?php defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Jakarta');

class DataProduksi extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("dataproduksi_model");

    }

    public function index(){
    }

    public function getSettingLine(){
        $post = $this->input->post();
        $data = $this->dataproduksi_model;
        $jose = $data->getAllLine();
        $nt = array('result' => $jose);
        echo json_encode($nt);
    }

    public function getDetailSettLine(){
        $post = $this->input->post();
        $data = $this->dataproduksi_model;
        $jose = $data->getDetailLine($post["ln"]);
        $nt = array('result' => $jose);
        echo json_encode($nt);
    }

    public function editSettLine(){
        $post = $this->input->post();
        $data = $this->dataproduksi_model;
        $kode = $post["ln"];

        $syrate = "Kode_Line = '$kode'"; 
        $vari = array(
            'CT_Standart' => $post["ctCap"],
            'Line_Name' => $post["name"],
            'Efektif_Kerja' => $post["efv"],
        );      
        $jose = $data->updateLN($vari, $syrate);
        echo json_encode($jose);
    } 

    public function deleteSettLine(){
        $post = $this->input->post();
        $data = $this->dataproduksi_model;
        $kode = $post["ln"];
        $jose = $data->deleteLine($kode);
        echo json_encode($jose);
    } 

    public function tambahLine(){
        $post = $this->input->post();
        $data = $this->dataproduksi_model;
        $kode = $data->requestKodeLine();
        $varine = array(
            'Kode_Line' => $kode,
            'Line_Name' => $post["name"],
            'Efektif_Kerja' => $post["Efv"],
            'CT_Standart' => $post["ctStd"],
        );    
        $syrat = "Kode_Line = '$kode'";    
        $jose = $data->insertDataLine($varine, $syrat);
        echo json_encode($jose);
    } 


    public function getLine(){
        $post = $this->input->post();
        $data = $this->dataproduksi_model;
        $jose = $data->getLineProduction();
        $nt = array('result' => $jose);
        echo json_encode($nt);
    }

    public function addSettProses(){
        $post = $this->input->post();
        $data = $this->dataproduksi_model;
        $kode = $data->requestKodeProses($post["ln"]);
        $ln = $post["ln"];

        $syrate = "Line = '$ln' AND Proses = '$kode'"; 
        $vari = array(
            'Line' => $post["ln"],
            'Proses' => $kode,
            'Proses_Name' => $post["name"],
            'CT_Capacity' => $post["ctCap"],
            'Sub_Proses' => $post["sub"],
        );      
        $jose = $data->insertProsesSet($vari, $syrate);
        echo json_encode($jose);
    } 

    public function addSettSubProses(){
        $post = $this->input->post();
        $data = $this->dataproduksi_model;
        $ln = $post["ln"];
        $kd = $post["kd"];
        $jose = $data->insertSubProsesSet($ln, $kd);
        echo json_encode($jose);
    } 

    public function deleteSettSubProses(){
        $post = $this->input->post();
        $data = $this->dataproduksi_model;
        $ln = $post["ln"];
        $kd = $post["kd"];
        $sb = $post["sb"];

        $jose = $data->deleteSubProsesSet($ln, $kd, $sb);
        echo json_encode($jose);
    } 


    public function getDetailLine(){
        $post = $this->input->post();
        $data = $this->dataproduksi_model;
        $jose = $data->getDetailLineProduction($post["ln"]);
        $nt = array('result' => $jose);
        echo json_encode($nt);
    }

    public function getProses(){
        $post = $this->input->post();
        $data = $this->dataproduksi_model;
        $jose = $data->getProsesProduction($post["ln"]);
        $nt = array('result' => $jose);
        echo json_encode($nt);
    }

    public function getDetailProses(){
        $post = $this->input->post();
        $data = $this->dataproduksi_model;
        $jose = $data->getDetailProsesProduction($post["ln"],$post["pr"], $post["sb"]);
        $nt = array('result' => $jose);
        echo json_encode($nt);
    }

    public function editSettProses(){
        $post = $this->input->post();
        $data = $this->dataproduksi_model;
        $kode = $post["ln"];
        $kodeKey = $post["kd"];
        $sub = $post["sb"];

        $syrate = "Line = '$kode' AND Proses = '$kodeKey' AND Sub_Proses = '$sub'"; 
        $vari = array(
            'CT_Capacity' => $post["ctCap"],
            'Proses_Name' => $post["name"],
            'Sub_Proses' => $post["sb"],
        );      
        $jose = $data->updateBN($vari, $syrate);
        echo json_encode($jose);
    } 

    public function tambahOperator(){
        $post = $this->input->post();
        $data = $this->dataproduksi_model;
        $kode = $post["nip"];
        $varine = array(
            'NIK' => $kode,
            'Nama_Lengkap' => $post["name"],
            'Nick_Name' => $post["nick"],
        );    
        $syrat = "NIK = '$kode'";    
        $jose = $data->insertDataOperator($varine, $syrat);
        echo json_encode($jose);
    } 

    public function getAllOperator(){
        $post = $this->input->post();
        $data = $this->dataproduksi_model;
        $jose = $data->getAllOperator();
        $nt = array('result' => $jose);
        echo json_encode($nt);
    }


    public function getSettOperator(){
        $post = $this->input->post();
        $data = $this->dataproduksi_model;
        $jose = $data->getOperatorProses($post["ln"]);
        $nt = array('result' => $jose);
        echo json_encode($nt);
    }

    public function editSettOperator(){
        $post = $this->input->post();
        $data = $this->dataproduksi_model;
        $kode = $post["ln"];
        $kodeKey = $post["kd"];
        $sub = $post["sb"];
        $name = $post["name"];
        $ld = $post["ld"];

        $syrate = "Line = '$kode' AND Proses = '$kodeKey' AND Sub_Proses = '$sub'"; 
        $srLD = "Kode_Line = '$kode'"; 

        $vari = array(
            'Operator' => $post["name"],
        );      
        $vari2 = array(
            'Leader' => $post["name"],
        ); 
        $jose = $data->updateSettOperator($vari, $syrate, $name, $vari2, $srLD, $ld);
        echo json_encode($jose);
    } 

    public function tambahData(){
        $tgle = date('Y-m-d');
        $post = $this->input->get();
        $data = $this->dataproduksi_model;
        $varine = array(
            'Tanggal' => $tgle,
            'Line' => $post["line"],
            'Proses' => $post["pros"],
            'Sub_Proses' => $post["sub"],
            'Push_Time' => $post["str"],
            'Status' => $post["stb"],
        );
        $jose = $data->insertDataAndon($varine, $post["line"],$post["pros"], $post["str"], $post["stb"]);
        echo json_encode($jose);
    } 

    public function startDT(){
        $tgle = date('Y-m-d');
        $post = $this->input->get();
        $data = $this->dataproduksi_model;
        $line = $post["line"];
        $pros = $post["pros"];
        $sub = $post["sub"];
        $kdDT = $post["kdn"];
        $syrat = "Tanggal = '$tgle' AND Line = '$line' AND Proses = '$pros' AND Sub_Proses = '$sub' AND Kode_DT='$kdDT' AND Start_Time != '' AND  Finish_Time != ''";

        $varine = array(
            'Tanggal' => $tgle,
            'Line' => $line,
            'Proses' => $pros,
            'Sub_Proses' => $sub,
            'Start_Time' => $post["str"],
            'Kode_DT' => $kdDT,
        );

        $jose = $data->insertStartDownTime($varine, $syrat);
        echo json_encode($jose);
    } 

    public function finishDT(){
        $tgle = date('Y-m-d');
        $post = $this->input->get();
        $data = $this->dataproduksi_model;
        $line = $post["line"];
        $pros = $post["pros"];
        $sub = $post["sub"];
        $kdDT = $post["kdn"];
        $syrat = "Tanggal = '$tgle' AND Line = '$line' AND Proses = '$pros' AND Sub_Proses = '$sub'  AND Kode_DT = '$kdDT' AND Start_Time != '' AND Finish_Time = ''";
        $varine = array(
            'Finish_Time' => $post["str"],
        );
        $jose = $data->insertFinishDownTime($varine, $syrat);
        echo json_encode($jose);
    } 

    public function getInformationProduction(){
        $post = $this->input->post();
        $data = $this->dataproduksi_model;
        $jose = $data->loadInformation($post["ln"]);
        $nt = array('result' => $jose);
        echo json_encode($nt);
    }

    public function getDetailOperator(){
        $post = $this->input->post();
        $data = $this->dataproduksi_model;
        $jose = $data->getDetailOperator($post["nip"]);
        $nt = array('result' => $jose);
        echo json_encode($nt);
    }

    public function editDaftarOperator(){
        $post = $this->input->post();
        $data = $this->dataproduksi_model;
        $kode = $post["lastNip"];
        $line = $post["line"];

        $syrate = "NIK = '$kode'"; 
        $vari = array(
            'NIK' => $post["nip"],
            'Nama_Lengkap' => $post["nama"],
            'Nick_Name' => $post["nick"],
        );      
        $jose = $data->updateDataOperator($vari, $syrate, $kode, $line);
        echo json_encode($jose);
    } 

    public function hapusDaftarOperator(){
        $post = $this->input->post();
        $data = $this->dataproduksi_model;
        $kode = $post["nip"];
        $jose = $data->deleteOperator($kode);
        echo json_encode($jose);
    } 

}