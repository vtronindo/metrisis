<?php defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Jakarta');


use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\AutoFilter;

class AndroidDataPengukuran extends CI_Controller
{

 //   public $url = "https://app-metrisis.soloabadi.com/cetak/Report_Pengukuran.xlsx";
    public $url = "http://192.168.43.208/andon/cetak/Report_Pengukuran.xlsx";


    public function __construct()
    {
        parent::__construct();
        $this->load->model("datapengukuran_model");

    }

    public function index(){

    }

    public function daftarLaporan(){
        $post = $this->input->post();
        $data = $this->datapengukuran_model;
        $jose = $data->getLapPengukuran();
        $nt = array('result' => $jose);
        echo json_encode($nt);
    }
    
    public function tambahLapPengukuran(){
        $tgle = date('Y-m-d');
        $post = $this->input->post();
        $data = $this->datapengukuran_model;
        $varine = array(
            'Tanggal' =>$tgle,
            'NIK' => $post["nik_anak"],
            'Nama_Anak' => $post["nama"],
            'Berat_Badan' => $post["Berat"],
            'Panjang_Badan' => $post["Panjang"], 
            'Lingkar_Lengan' => $post["Lila"], 
            'Lingkar_Kepala' => $post["Like"], 
            'Vitamin' => $post["Vitamine"], 
            'Pemberian_ASI' => $post["Asine"], 
            'Kode_Posyandu' => $post["Posyandu"], 
            'Cara_Ukur' => $post["CaraUkur"], 
        );
        $jose = $data->insertPengukuran($varine);
        echo json_encode($jose);
    } 

    public function cetakLaporan(){
        $data = $this->datapengukuran_model;
        $dt = $data->getLapPengukuran();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->getStyle('A:J')->getAlignment()->setHorizontal('center');
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


		$sheet->setCellValue('A1', 'No');
		$sheet->setCellValue('B1', 'Tanggal Pengukuran');        
		$sheet->setCellValue('C1', 'Nama Anak');
		$sheet->setCellValue('D1', 'NIK Anak');        
		$sheet->setCellValue('E1', 'Jenis Kelamin');
		$sheet->setCellValue('F1', 'Berat Badan');
		$sheet->setCellValue('G1', 'Tinggi Badan');        
		$sheet->setCellValue('H1', 'Lingkar Lengan');
		$sheet->setCellValue('I1', 'Lingkar Kepala');        
		$sheet->setCellValue('J1', 'Posisi Pengukuran');

        $count = 2;
        foreach($dt as $jos){
            $sheet->setCellValue('A' . $count, $count-1);
            $sheet->setCellValue('B' . $count, $jos["Tanggal"]);
            $sheet->setCellValue('C' . $count, $jos["Nama_Anak"]);
            $sheet->setCellValue('D' . $count, $jos["NIK_Anak"]);
            $sheet->setCellValue('E' . $count, $jos["Jenis_Kelamin"]);
            $sheet->setCellValue('F' . $count, $jos["Berat_Badan"]);
            $sheet->setCellValue('G' . $count, $jos["Tinggi_Badan"]);
            $sheet->setCellValue('H' . $count, $jos["Lingkar_Lengan"]);
            $sheet->setCellValue('I' . $count, $jos["Lingkar_Kepala"]);
            $sheet->setCellValue('J' . $count, $jos["Cara_Ukur"]);
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
        $sheet->getStyle('A1:J'.$count)->applyFromArray($styleArray);

        $writer = new Xlsx($spreadsheet);		
        $writer->save('cetak/Report_Pengukuran.xlsx');

        $item["nama"] = "Report_Pengukuran.xlsx";
        $item["alamat"] = $this->url;
        $mbuh = array();
        $mbuh[] = $item;
        $datane = array('result' => $mbuh);
        echo json_encode($datane);
 

    }
   
    public function detailLaporan(){
        $post = $this->input->post();
        $data = $this->datapengukuran_model;
        $jose = $data->getDetailPengukuranByNoID($post["ID"]);
        $nt = array('result' => $jose);
        echo json_encode($nt);
    } 

    public function historyPengukuran(){
        $post = $this->input->post();
        $data = $this->datapengukuran_model;
        $jose = $data->getHistoryPengukuranByNIK($post["nik"]);
        $nt = array('result' => $jose);
        echo json_encode($nt);
    } 

    public function filterLaporan(){
        $post = $this->input->post();
        $data = $this->datapengukuran_model;
        $jose = $data->getFilterLapPengukuran($post["tglStart"], $post["tglFinish"]);
        $nt = array('result' => $jose);
        echo json_encode($nt);
    } 

    public function sortirLapByNama(){
        $post = $this->input->post();
        $data = $this->datapengukuran_model;
        $jose = $data->getSortLapPengukuranByName($post["Namane"]);
        $nt = array('result' => $jose);
        echo json_encode($nt);
    } 


    public function hapusPengukuranByNomor(){
        $post = $this->input->post();
        $data = $this->datapengukuran_model;
        $varine = array(
            'No_ID' => $post["No_ID"],
        );
        $jose = $data->deletePengukuranByID($varine);
        echo json_encode($jose);
    } 




}