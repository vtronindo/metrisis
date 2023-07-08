<?php defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Jakarta');

class DataPosyandu extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("dataposyandu_model");

    }

    public function tambahPosyandu(){
        $post = $this->input->post();
        $data = $this->dataposyandu_model;
        $statusTambah = "";
        $varPosyandu = array(
            'Nama_Posyandu' => $post["Posyandu"],
            'Alamat' => $post["Alamat"],
            'RT' => $post["RT"],
            'RW' => $post["RW"],
            'ID_Provinsi' => $post["idProvinsi"], 
            'Nama_Provinsi' => $post["Provinsi"], 
            'ID_KabKota' => $post["idKota"], 
            'Nama_Kota' => $post["Kota"], 
            'ID_Kecamatan' => $post["idKecamatan"], 
            'Nama_Kecamatan' => $post["Kecamatan"], 
            'ID_Kelurahan' => $post["idKelurahan"], 
            'Nama_Kelurahan' => $post["Kelurahan"], 
            'ID_KodePos' => $post["KodePos"], 
            'Puskesmas' => $post["Puskesmas"], 
        );

        $jose = $data->insertPosyandu($varPosyandu);

        if($jose == "sukses"){
            $kodePosyandu = $data->kodePosyandu($varPosyandu);        
            $stKader = $data->insertKader($kodePosyandu, $post["NamaKader"],$post["NoTelpon"],$post["Password"],$post["Email"]);
            $statusTambah = $stKader;
        }
        else{
            $statusTambah = "gagal";
        }

        echo json_encode($statusTambah);
    } 

    public function tambahKader(){
        $post = $this->input->post();
        $data = $this->dataposyandu_model;
        $stKader = $data->insertKader($post["idPosyandu"], $post["NamaKader"],$post["NoTelpon"],$post["Password"],$post["Email"]);
        echo json_encode($stKader);
    } 


    public function loginKader(){
        $post = $this->input->post();
        $data = $this->dataposyandu_model;
        $vari = array('No_Telpon' => $post["NoTelpon"]);
        $jose = $data->getLoginKader($vari,$post["Password"]);
        echo json_encode($jose);
    }

    public function daftarKader(){
        $post = $this->input->post();
        $data = $this->dataposyandu_model;
        $jose = $data->getKaderByPosyandu($post["idPosyandu"]);
        $nt = array('result' => $jose);
        echo json_encode($nt);
    }

    public function detailKaderByID(){
        $post = $this->input->post();
        $data = $this->dataposyandu_model;
        $jose = $data->getKaderByID($post["idKader"], $post["idPosyandu"]);
        $nt = array('result' => $jose);
        echo json_encode($nt);
    }

    public function setStatusKader(){
        $post = $this->input->post();
        $data = $this->dataposyandu_model;
        if($post["vare"] == "1"){
            $varine = array(
                'Status_Admin' => 'Admin',
            );    
        }
        else{
            $varine = array(
                'Status_Admin' => '',
            );    
        }

        $syarate = array(
            'No_ID' => $post["idne"],
        );
        $jose = $data->updateKader($syarate,$varine);
        echo json_encode($jose);
    } 

    public function hapusDaftarKader(){
        $post = $this->input->post();
        $data = $this->dataposyandu_model;

            $varine = array(
                'View' => '0',
            );    

        $syarate = array(
            'No_ID' => $post["idne"],
        );
        $jose = $data->updateKader($syarate,$varine);
        echo json_encode($jose);
    } 


    public function cariPosyandu(){
        $post = $this->input->post();
        $data = $this->dataposyandu_model;
        $jose = $data->getPosyanduByID($post["idPosyandu"]);
        $nt = array('result' => $jose);
        echo json_encode($nt);
    }

    public function updateProfilKader(){
        $post = $this->input->post();
        $data = $this->dataposyandu_model;
        $varine = array(
            'Nama_Kader' => $post["NamaKader"],
            'No_Telpon' => $post["NoTelpon"],
            'Kata_Sandi' => $post["Password"],
            'Email' => $post["Email"],
        );

        $syarate = array(
            'No_ID' => $post["idne"],
        );
        $jose = $data->updateKader($syarate,$varine);
        echo json_encode($jose);
    } 

    public function updateDataPosyandu(){
        $post = $this->input->post();
        $data = $this->dataposyandu_model;
        
        $varPosyandu = array(
            'Nama_Posyandu' => $post["Posyandu"],
            'Alamat' => $post["Alamat"],
            'RT' => $post["RT"],
            'RW' => $post["RW"],
            'ID_Provinsi' => $post["idProvinsi"], 
            'Nama_Provinsi' => $post["Provinsi"], 
            'ID_KabKota' => $post["idKota"], 
            'Nama_Kota' => $post["Kota"], 
            'ID_Kecamatan' => $post["idKecamatan"], 
            'Nama_Kecamatan' => $post["Kecamatan"], 
            'ID_Kelurahan' => $post["idKelurahan"], 
            'Nama_Kelurahan' => $post["Kelurahan"], 
            'ID_KodePos' => $post["KodePos"], 
            'Puskesmas' => $post["Puskesmas"], 
        );

        $syarate = array(
            'No_ID' => $post["idne"],
        );

        $jose = $data->updatePosyandu($syarate, $varPosyandu);
        echo json_encode($jose);
    } 

    public function tambahPengukuran(){
        $tgle = date('Y-m-d');
        $post = $this->input->post();
        $data = $this->dataposyandu_model;

        
        $varUkur = array(
            'NIK_Anak' => $post["kiane"],
            'Nama_Anak' => $post["namane"],
            'Tanggal_Ukur' => $tgle,
            'Usia_Ukur' => $post["umur"],
            'Berat' => $post["berate"],
            'Tinggi' => $post["tinggine"], 
            'Lingkar_Lengan' => $post["lile"], 
            'Lingkar_Kepala' => $post["lila"], 
            'Cara_Ukur' => $post["caraUkur"], 
            'Vitamin' => $post["vitamin"], 
            'ASI' => $post["asi"], 
            'PMT' => $post["pmt"], 
            'ID_Kader' => $post["kader"], 
            'ID_Posyandu' => $post["posyandu"], 
            'View' => '1',

        );

        $varVitamin = array(
            'NIK_Anak' => $post["kiane"],
            'Jenis_Kapsul' => $post["kapsule"],
            'Usia_Pemberian' => $post["umur"],
            'Tanggal_Pemberian' => $tgle,
            'ID_Posyandu' => $post["posyandu"],
            'ID_Kader' => $post["kader"],
            'Status' => $post["trashVit"],
            'View' => '1',
        );

        $varASI = array(
            'NIK_Anak' => $post["kiane"],
            'Jenis_ASI' => $post["asine"],
            'Usia_Pemberian' => $post["umur"],
            'Tanggal_Pengisian' => $tgle,
            'ID_Posyandu' => $post["posyandu"],
            'ID_Kader' => $post["kader"],
            'Status' => $post["trashAsi"],
            'View' => '1',

        );

        $varPMT = array(
            'NIK_Anak' => $post["kiane"],
            'Tanggal_Pemberian' => $tgle,
            'Sumber_PMT' => $post["sumberPMT"],
            'Pemberian_Pusat' => $post["pusatPMT"],
            'Tahun_Produksi' => $post["thnPMT"],
            'Pemberian_Daerah' => $post["daerahPMT"],
            'ID_Posyandu' => $post["posyandu"],
            'ID_Kader' => $post["kader"],
            'View' => '1',
        );


        $jose = $data->insertPengukuran($varUkur, $tgle, $post["kiane"], $post["umur"],$varASI,$varVitamin,$post["stAsi"], $post["stVit"], $post["pmt"], $varPMT);
        echo json_encode($jose);
    } 

    public function ImportDataPengukuran(){
        $post = $this->input->post();
        $data = $this->dataposyandu_model;




        if($post["vitamin"] == "Ya"){
            $vrVit = "1";    
            $varVitamin = array(
                'NIK_Anak' => $post["nik"],
                'Jenis_Kapsul' => '',
                'Usia_Pemberian' => $post["umure"],
                'Tanggal_Pemberian' => $post["tanggal"],
                'ID_Posyandu' => $post["idPosyandu"],
                'ID_Kader' => $post["idKader"],
                'Status' => $vrVit,
                'View' => '1',
            );
          //  $data->tambahkeVitamine($varVitamin);    
        }


        for((int)$a=0; $a<6; $a++){
            switch($a){
                case 0: 
                    if($post["asi1"] == "Ya"){
                        $vrAsine = "1";    
                    }
                    else{
                        $vrAsine = "0";
                    }
                break;
                case 1: 
                    if($post["asi2"] == "Ya"){
                        $vrAsine = "1";    
                    }
                    else{
                        $vrAsine = "0";
                    }
                break;
                case 2: 
                    if($post["asi3"] == "Ya"){
                        $vrAsine = "1";    
                    }
                    else{
                        $vrAsine = "0";
                    }
                break;
                case 3: 
                    if($post["asi4"] == "Ya"){
                        $vrAsine = "1";    
                    }
                    else{
                        $vrAsine = "0";
                    }
                break;
                case 4: 
                    if($post["asi5"] == "Ya"){
                        $vrAsine = "1";    
                    }
                    else{
                        $vrAsine = "0";
                    }
                break;
                case 5: 
                    if($post["asi6"] == "Ya"){
                        $vrAsine = "1";    
                    }
                    else{
                        $vrAsine = "0";
                    }
                break;
            }
    
            $bne = $a + 1;
            $varASI = array(
                'NIK_Anak' => $post["nik"],
                'Jenis_ASI' => $bne,
                'Usia_Pemberian' => $post["umure"],
                'Tanggal_Pengisian' => $post["tanggal"],
                'ID_Posyandu' => $post["idPosyandu"],
                'ID_Kader' => $post["idKader"],
                'Status' => $vrAsine,
                'View' => '1',
            );
        //    $data->tambahkeAsine($varASI);
        }

        if($post["sumberPMT"] != ""){
            $varPMT = array(
                'NIK_Anak' => $post["nik"],
                'Tanggal_Pemberian' => $post["tanggal"],
                'Sumber_PMT' => $post["sumberPMT"],
                'Pemberian_Pusat' => $post["pemPusat"],
                'Tahun_Produksi' => $post["thnProduksi"],
                'Pemberian_Daerah' => $post["pemDaerah"],
                'ID_Posyandu' => $post["idPosyandu"],
                'ID_Kader' => $post["idKader"],
                'View' => '1',
            );
            //$data->tambahkePMTne($varPMT);    
        }

        $varUkur = array(
            'NIK_Anak' => $post["nik"],
            'Nama_Anak' => $post["nama"],
            'Tanggal_Ukur' => $post["tanggal"],
            'Usia_Ukur' => $post["umure"],
            'Berat' => $post["berat"],
            'Tinggi' => $post["tinggi"], 
            'Lingkar_Lengan' => $post["lila"], 
            'Lingkar_Kepala' => $post["lingkep"], 
            'Cara_Ukur' => $post["caraUkur"], 
            'Vitamin' => $vrVitUkur, 
            'ASI' => ' ', 
            'PMT' => ' ', 
            'ID_Kader' => $post["idKader"], 
            'ID_Posyandu' => $post["idPosyandu"], 
            'View' => '1',
        );

        $jose = $data->tambahkeLaporane($varUkur, $post["tanggal"], $post["nik"], $post["umure"]);
        echo json_encode($jose);
    } 


    public function daftarPengukuranByID(){
        $post = $this->input->post();
        $data = $this->dataposyandu_model;
        $jose = $data->getLapPengukuran($post["idPosyandu"],$post["status"]);
        $nt = array('result' => $jose);
        echo json_encode($nt);
    }

    public function cariPengukuranByIDandNIK(){
        $post = $this->input->post();
        $data = $this->dataposyandu_model;
        $jose = $data->sortLapPengukuranByKIA($post["idPosyandu"], $post["kiane"], $post["status"]);
        $nt = array('result' => $jose);
        echo json_encode($nt);
    }

    public function filterPengukuranByDate(){
        $post = $this->input->post();
        $data = $this->dataposyandu_model;
        $jose = $data->filterLapPengukuranByDate($post["idPosyandu"], $post["tglStart"], $post["tglFinish"]);
        $nt = array('result' => $jose);
        echo json_encode($nt);
    }


    public function detailPengukuranByNo(){
        $post = $this->input->post();
        $data = $this->dataposyandu_model;
        $jose = $data->getDetailPengukuran($post["ID"]);
        $nt = array('result' => $jose);
        echo json_encode($nt);
    }

    public function cariHistoryByNIK(){
        $post = $this->input->post();
        $data = $this->dataposyandu_model;
        $jose = $data->getHistoryPengukuranByKIA($post["kiane"]);
        $nt = array('result' => $jose);
        echo json_encode($nt);
    }

    public function hapusPengukuranByNomor(){
        $post = $this->input->post();
        $data = $this->dataposyandu_model;

        $jose = $data->deletePengukuran($post["No_ID"]);
        echo json_encode($jose);
    } 

    public function cetakLaporan(){
        $post = $this->input->post();
        $data = $this->dataposyandu_model;
        $dt = $data->getCetakPengukuran($post["idPosyandu"], $post["bufTglStart"], $post["bufTglEnd"]);
        $datane = array('result' => $dt);
        echo json_encode($datane);
 

    }


}