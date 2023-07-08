<?php defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Jakarta');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter;

class DataAnak extends CI_Controller
{

 //   public $url = "https://app-metrisis.soloabadi.com/cetak/Report_Pengukuran.xlsx";
    public $url = "http://192.168.8.102/weightscale/";


    public function __construct()
    {
        parent::__construct();
        $this->load->model("dataanak_model");

    }

    public function daftarAnakByPosyandu(){
        $post = $this->input->post();
        $data = $this->dataanak_model;
        $jose = $data->getDaftarAnak($post["idPosyandu"]);
        $nt = array('result' => $jose);
        echo json_encode($nt);
    }

    public function daftarAnakPindahan(){
        $post = $this->input->post();
        $data = $this->dataanak_model;
        $jose = $data->getDaftarAnakPindahan();
        $nt = array('result' => $jose);
        echo json_encode($nt);
    }

    public function sortirAnakByPosyandu(){
        $post = $this->input->post();
        $data = $this->dataanak_model;
        $jose = $data->getSortirAnak($post["Namane"],$post["idne"]);
        $nt = array('result' => $jose);
        echo json_encode($nt);
    }

    public function tambahDataAnak(){
        $post = $this->input->post();
        $data = $this->dataanak_model;
        $varAnak = array(
            'No_KK' => $post["NoKK"],
            'NIK_Anak' => $post["KIA"],
            'Nama_Anak' => $post["NamaAnak"],
            'Tanggal_Lahir' => $post["TTL"],
            'Berat_Lahir' => $post["BBLahir"],
            'Tinggi_Lahir' => $post["TBLahir"], 
            'Jenis_Kelamin' => $post["Gender"], 
            'IMD' => $post["ImdAnak"],
            'Anak_Ke' => $post["AnakKe"],
            'ID_Posyandu' => $post["idPosyandu"], 
            'Status' => 'Aktif',
            'View' => '1',
        );

        
        $varOrtu = array(
            'No_KK' => $post["NoKK"], 
            'NIK_Ortu' => $post["NIK"],
            'Nama_Ayah' => $post["NamaOrtu"],
            'No_Telpon' => $post["NoTelpon"],
            'KIA' => $post["bukuKIA"],
            'Alamat' => $post["Alamat"], 
            'RT' => $post["RT"],
            'RW' => $post["RW"],
            'ID_Provinsi' => $post["idProvinsi"], 
            'ID_KabKota' => $post["idKota"], 
            'ID_Kecamatan' => $post["idKecamatan"], 
            'ID_Kelurahan' => $post["idKelurahan"], 
            'ID_KodePos' => $post["KodePos"], 
            'Nama_Provinsi' => $post["Provinsi"], 
            'Nama_Kota' => $post["Kota"], 
            'Nama_Kecamatan' => $post["Kecamatan"], 
            'Nama_Kelurahan' => $post["Kelurahan"], 
            'ID_Posyandu' => $post["idPosyandu"], 
            'View' => '1',
        );

        $jose = $data->insertDataAnak($varAnak, $varOrtu, $post["NoKK"], $post["KIA"]);
        echo json_encode($jose);
    } 

    public function importDataAnak(){
        $post = $this->input->post();
        $data = $this->dataanak_model;
        $varAnak = array(
            'No_KK' => $post["NoKK"],
            'NIK_Anak' => $post["KIA"],
            'Nama_Anak' => $post["NamaAnak"],
            'Tanggal_Lahir' => $post["TTL"],
            'Berat_Lahir' => $post["BBLahir"],
            'Tinggi_Lahir' => $post["TBLahir"], 
            'Jenis_Kelamin' => $post["Gender"], 
            'IMD' => $post["ImdAnak"],
            'Anak_Ke' => $post["AnakKe"],
            'ID_Posyandu' => $post["idPosyandu"], 
            'Status' => 'Aktif',
            'View' => '1',
        );

        
        $varOrtu = array(
            'No_KK' => $post["NoKK"], 
            'NIK_Ortu' => $post["NIK"],
            'Nama_Ayah' => $post["NamaOrtu"],
            'No_Telpon' => $post["NoTelpon"],
            'KIA' => $post["bukuKIA"],
            'Alamat' => $post["Alamat"], 
            'RT' => $post["RT"],
            'RW' => $post["RW"],
            'ID_Posyandu' => $post["idPosyandu"], 
            'View' => '1',
        );

        $jose = $data->insertDataAnak($varAnak, $varOrtu, $post["NoKK"], $post["KIA"]);
        echo json_encode($jose);
    } 


    public function pilihAnakPindahanDari(){
        $post = $this->input->post();
        $data = $this->dataanak_model;
        $jose = $data->updateAnakPindahanDari($post["kiane"], $post["idPosyandu"]);
        echo json_encode($jose);
    }

    public function pilihAnakPindahanKe(){
        $post = $this->input->post();
        $data = $this->dataanak_model;
        $jose = $data->updateAnakPindahanKe($post["kiane"]);
        echo json_encode($jose);
    }

    public function cariAnakPindahByKIA(){
        $post = $this->input->post();
        $data = $this->dataanak_model;
        $jose = $data->getCariAnakPindahanByKIA($post["kiane"]);
        $nt = array('result' => $jose);
        echo json_encode($nt);
    }

    public function detailAnakByKIA(){
        $post = $this->input->post();
        $data = $this->dataanak_model;
        $jose = $data->getNamaAnakByKIA($post["kiane"]);
        $nt = array('result' => $jose);
        echo json_encode($nt);
    }

    public function updateDataAnak(){
        $post = $this->input->post();
        $data = $this->dataanak_model;
        
        $varAnak = array(
            'Nama_Anak' => $post["NamaAnak"],
            'Tanggal_Lahir' => $post["TTL"],
            'Berat_Lahir' => $post["BBLahir"],
            'Tinggi_Lahir' => $post["TBLahir"], 
            'Jenis_Kelamin' => $post["Gender"], 
            'IMD' => $post["ImdAnak"],
            'Anak_Ke' => $post["AnakKe"],
        );

        $syarate = array(
            'NIK_Anak' => $post["KIA"],
            'ID_Posyandu' => $post["idPosyandu"], 
            'View' => '1',
            'Status' => 'Aktif',

        );

        $jose = $data->editDataAnak($syarate, $varAnak);
        echo json_encode($jose);
    } 

    public function hapusDataAnak(){
        $post = $this->input->post();
        $data = $this->dataanak_model;
        
        $varAnak = array(
            'View' => '0',
            'Status' => 'Non Aktif',
        );

        $syarate = array(
            'NIK_Anak' => $post["kiane"],
        );

        $jose = $data->editDataAnak($syarate, $varAnak);
        echo json_encode($jose);
    } 


    public function insertASIAnak(){
        $post = $this->input->post();
        $data = $this->dataanak_model;

        $varASI = array(
            'NIK_Anak' => $post["kiane"],
            'Jenis_ASI' => $post["jenise"],
            'Usia_Pemberian' => $post["usiane"],
            'Tanggal_Pengisian' => $post["tanggale"],
            'ID_Posyandu' => $post["idPosyandu"],
            'ID_Kader' => $post["idKader"],
            'Status' => $post["trash"],
            'View' => '1',

        );

        $jose = $data->insertDataASI($varASI);
        echo json_encode($jose);

    }

    public function insertVitaminAnak(){
        $post = $this->input->post();
        $data = $this->dataanak_model;

        $varVitamin = array(
            'NIK_Anak' => $post["kiane"],
            'Jenis_Kapsul' => $post["jenise"],
            'Usia_Pemberian' => $post["usiane"],
            'Tanggal_Pemberian' => $post["tanggale"],
            'ID_Posyandu' => $post["idPosyandu"],
            'ID_Kader' => $post["idKader"],
            'Status' => $post["trash"],
            'View' => '1',

        );

        $jose = $data->insertDataVitamin($varVitamin);
        echo json_encode($jose);

    }

    public function deleteVitaminAnak(){

        $post = $this->input->post();
        $data = $this->dataanak_model;
        

        $syarate = array(
            'NIK_Anak' => $post["kiane"],
            'Jenis_Kapsul' => $post["jenise"],
        );


        $jose = $data->deleteDataVitamin($syarate);
        echo json_encode($jose);
    } 

    public function detailOrtuByKK(){
        $post = $this->input->post();
        $data = $this->dataanak_model;
        $jose = $data->getOrtuByKK($post["noKK"]);
        $nt = array('result' => $jose);
        echo json_encode($nt);
    }

    public function updateDataOrtu(){
        $post = $this->input->post();
        $data = $this->dataanak_model;
        
        $varOrtu = array(
            'No_KK' => $post["NoKK"], 
            'NIK_Ortu' => $post["NIK"],
            'Nama_Ayah' => $post["NamaOrtu"],
            'No_Telpon' => $post["NoTelpon"],
            'KIA' => $post["bukuKIA"],
            'Alamat' => $post["Alamat"], 
            'RT' => $post["RT"],
            'RW' => $post["RW"],
            'ID_Provinsi' => $post["idProvinsi"], 
            'ID_KabKota' => $post["idKota"], 
            'ID_Kecamatan' => $post["idKecamatan"], 
            'ID_Kelurahan' => $post["idKelurahan"], 
            'ID_KodePos' => $post["KodePos"], 
            'Nama_Provinsi' => $post["Provinsi"], 
            'Nama_Kota' => $post["Kota"], 
            'Nama_Kecamatan' => $post["Kecamatan"], 
            'Nama_Kelurahan' => $post["Kelurahan"], 
            'ID_Posyandu' => $post["idPosyandu"], 
            'View' => '1',
        );

        $syarate = array(
            'No_KK' => $post["NoKKLama"],
            'ID_Posyandu' => $post["idPosyandu"], 
            'View' => '1',

        );

        $jose = $data->editDataOrtu($syarate, $varOrtu);
        echo json_encode($jose);
    } 

    public function hapusDataOrtu(){
        $post = $this->input->post();
        $data = $this->dataanak_model;
        
        $varAnak = array(
            'View' => '0',
            'Status' => 'Non Aktif',
        );

        $varOrtu = array(
            'View' => '0',
        );

        $syarate = array(
            'No_KK' => $post["noKK"],
            'ID_Posyandu' => $post["idPosyandu"], 
            'View' => '1',
        );

        $jose = $data->deleteDataOrtu($syarate, $varAnak, $varOrtu);
        echo json_encode($jose);
    } 

    public function getAsiAnakByKIA(){
        $post = $this->input->post();
        $data = $this->dataanak_model;
        $jose = $data->getAsiAnakByKIA($post["kiane"]);
        $nt = array('result' => $jose);
        echo json_encode($nt);
    }

    public function getVitaminAnakByKIA(){
        $post = $this->input->post();
        $data = $this->dataanak_model;
        $jose = $data->getVitaminAnakByKIA($post["kiane"]);
        $nt = array('result' => $jose);
        echo json_encode($nt);
    }

    public function cetakLaporan(){
        $count = 2;
        $judule = date('His_dmy');

        $post = $this->input->post();
        $data = $this->dataanak_model;
        $dt = $data->getLapIdentitasAnak($post["idPosyandu"]);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getStyle('A:X')->getAlignment()->setHorizontal('center');
        // Set column A width automatically
        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->getColumnDimension('H')->setAutoSize(true);
        $sheet->getColumnDimension('I')->setAutoSize(true);
        $sheet->getColumnDimension('J')->setAutoSize(true);
        $sheet->getColumnDimension('K')->setAutoSize(true);
        $sheet->getColumnDimension('L')->setAutoSize(true);
        $sheet->getColumnDimension('M')->setAutoSize(true);
        $sheet->getColumnDimension('N')->setAutoSize(true);
        $sheet->getColumnDimension('O')->setAutoSize(true);
        $sheet->getColumnDimension('P')->setAutoSize(true);
        $sheet->getColumnDimension('Q')->setAutoSize(true);
        $sheet->getColumnDimension('R')->setAutoSize(true);
        $sheet->getColumnDimension('S')->setAutoSize(true);
        $sheet->getColumnDimension('T')->setAutoSize(true);
        $sheet->getColumnDimension('U')->setAutoSize(true);
        $sheet->getColumnDimension('V')->setAutoSize(true);
        $sheet->getColumnDimension('W')->setAutoSize(true);
        $sheet->getColumnDimension('X')->setAutoSize(true);


		$sheet->setCellValue('A1', 'No');
		$sheet->setCellValue('B1', 'anak_ke');        
		$sheet->setCellValue('C1', 'tgl_lahir');
		$sheet->setCellValue('D1', 'jenis_kelamin');        
		$sheet->setCellValue('E1', 'nomor_KK');
		$sheet->setCellValue('F1', 'NIK');
		$sheet->setCellValue('G1', 'nama_anak');        
		$sheet->setCellValue('H1', 'berat_lahir');
		$sheet->setCellValue('I1', 'tinggi');        
		$sheet->setCellValue('J1', 'kia');
		$sheet->setCellValue('K1', 'imd');        
		$sheet->setCellValue('L1', 'nama_ortu');
		$sheet->setCellValue('M1', 'nik_ortu');
		$sheet->setCellValue('N1', 'hp_ortu');
		$sheet->setCellValue('O1', 'alamat');
		$sheet->setCellValue('P1', 'rt');
		$sheet->setCellValue('Q1', 'rw');
		$sheet->setCellValue('R1', 'Prov');
		$sheet->setCellValue('S1', 'Kab/Kota');
		$sheet->setCellValue('T1', 'Kec');
		$sheet->setCellValue('U1', 'Puskesmas');
		$sheet->setCellValue('V1', 'Desa/Kel');
		$sheet->setCellValue('W1', 'Posyandu');
		$sheet->setCellValue('X1', 'hapus');


        foreach($dt as $jos){
            $numb = $count - 1;
            $sheet->setCellValue('A' . $count, "$numb");
        
            $sheet->setCellValue('B' . $count, $jos["Anak_Ke"]);
            $sheet->setCellValue('C' . $count, $jos["Tanggal_Lahir"]);
            $sheet->setCellValue('D' . $count, $jos["Jenis_Kelamin"]);
            $sheet->setCellValue('E' . $count, $jos["No_KK"]);
            $sheet->setCellValue('F' . $count, $jos["NIK_Anak"]);
            $sheet->setCellValue('G' . $count, $jos["Nama_Anak"]);
            $sheet->setCellValue('H' . $count, $jos["Berat_Lahir"]);
            $sheet->setCellValue('I' . $count, $jos["Tinggi_Lahir"]);
            $sheet->setCellValue('J' . $count, $jos["Buku_KIA"]);
            $sheet->setCellValue('K' . $count, $jos["IMD"]);
            $sheet->setCellValue('L' . $count, $jos["Nama_Ortu"]);
            $sheet->setCellValue('M' . $count, $jos["NIK_Ortu"]);
            $sheet->setCellValue('N' . $count, $jos["No_Telpon"]);
            $sheet->setCellValue('O' . $count, $jos["Alamat"]);
            $sheet->setCellValue('P' . $count, $jos["RT"]);
            $sheet->setCellValue('Q' . $count, $jos["RW"]);
            $sheet->setCellValue('R' . $count, $jos["Provinsi"]);
            $sheet->setCellValue('S' . $count, $jos["KabKota"]);
            $sheet->setCellValue('T' . $count, $jos["Kecamatan"]);
            $sheet->setCellValue('U' . $count, $jos["Puskesmas"]);
            $sheet->setCellValue('V' . $count, $jos["Kelurahan"]);
            $sheet->setCellValue('W' . $count, $jos["Posyandu"]);
            $sheet->setCellValue('X' . $count, "");
            
            $count++;

        }



        $styleArray = [
			'borders' => [
				'allBorders' => [
					'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
				],
			],
		];
        $count = $count - 1;
        $sheet->getStyle('A1:X'.$count)->applyFromArray($styleArray);

        $writer = new Xlsx($spreadsheet);		
        $bufNama = "Metrisis_" . $judule . "_" . $post['idPosyandu'] . "_IdentitasAnak_.xlsx";
        $bufDir = "cetak/" . $bufNama; 
        $writer->save($bufDir);

        $item["nama"] = $bufNama;
        $item["alamat"] = $this->url . $bufDir;
        $mbuh = array();
        $mbuh[] = $item;
        $datane = array('result' => $mbuh);
        echo json_encode($datane);
 

    }

    public function cetakLaporan2(){
        $post = $this->input->post();
        $data = $this->dataanak_model;
        $dt = $data->getLapIdentitasAnak($post["idPosyandu"]);
        $datane = array('result' => $dt);
        echo json_encode($datane);
 

    }

}