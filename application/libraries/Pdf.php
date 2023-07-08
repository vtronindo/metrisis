<?php
defined('BASEPATH') OR exit('No direct script access allowed');

//require('third_party/fpdf/fpdf.php');
require_once(APPPATH . '/third_party/fpdf/fpdf.php');

class PDF extends FPDF{

    function __construct(){
        parent::__construct();
    }
    
    function Header(){   
        $this->Image('http://192.168.8.102/new_lumintu/logo/wiweka.jpg',10,4,35,25,'JPG');
        $this->SetXY( 25, 5);
        
        $this->SetFont('times','B',18);
        $this->Cell(0,7,'PT. Wiweka Karya Segara',0,1,'C');
        $this->Ln(2);
        $this->SetFont('times','',11);
        $this->SetX(25);
        $this->Cell(0,5,'Jl. Raya Pantura Juwana Rembang, KM 1.5 Ds Trimulyo Juwana, Kab Pati, 59185',0,1,'C');
        $this->Ln(1);
        $this->SetX(86);
        $this->SetFont('times','',10);
        $this->Cell(21,5,'Telpon / Fax : ',0,0,'C');
        $this->Cell(52,5,'081222307612',0,1,'L');

        $this->SetX(71);
        $this->Cell(12,5,'Email :',0,0,'L');
        $this->SetFont( "courier", "I", 10);
        $this->Cell(40,5,'wiweka.karyasegara@gmail.com',0,0,'L');
        $this->SetFont('times','',11);
        $this->Cell(75,5,'Page : '.$this->PageNo().'',0,0,'R');
        $this->Ln(1);
        $this->SetLineWidth(0.5);
        $this->Line(10,31,200,31);
        $this->SetLineWidth(0.3);
        $this->Line(10,32,200,32);
        $this->Ln(10);
    }  

    function addJudulLaporan($namane){
        $y = 38;
        $x = 55;
        $this->SetXY( $x, $y);
        $this->SetFont('times','B',16);
        $this->SetDrawColor(0,80,180);
        $this->SetFillColor(230,230,0);
        $this->SetTextColor(0,0,0);
        $this->Cell(100,8,$namane,0,1,'C');
        $this->Ln(2);
    }

    function periodeLaporanBulanan($bulane){
        $y = 45;
        $x = 10;
        $this->SetXY( $x, $y);
        $this->SetTextColor(220,50,50);
        $this->SetFont('times','B',12);
        $this->Cell(0,7,"Periode : " . $bulane,0, 0 ,'C'); 
    }
    

    function TablePenjualan($mulai,$data){

        global $totalNota;
        $totalNota = 0;
        // Column widths
        $this->SetFont('Arial','B',10);
        $this->SetTextColor(0,0,0);

        $y = $mulai;
        $x = 5;
        
        $list = json_decode($data);
        if(count($list) != 0){

            $this->SetXY( $x, $y);
            $header =array( "Tanggal",
                        "Nomor Invoice",
                        "Nama Barang",
                        "Harga Satuan",
                        "Qty",
                        "Total Harga",
                    );
    
            $w =array( 20,
                    50,
                    55,
                    27,
                    15,
                    33,
                );
    
            for($i=0;$i<count($header);$i++) 
                $this->Cell($w[$i],7,$header[$i],1,0,'C');
            $this->Ln();
        }

        $this->SetFont('Arial','',10);
        foreach ($list as $row => $nilainya)
        {
            $this->SetX($x);            
            if($row == 0){
                $this->Cell($w[0],6,$nilainya->Tanggal,'LR');
                $this->Cell($w[1],6,$nilainya->No_Invoice,'LR');
            }
            else{
                $this->Cell($w[0],6," ",'LR');
                $this->Cell($w[1],6," ",'LR');
            }

            $this->Cell($w[2],6,$nilainya->Nama_Barang,'LR');
            $this->Cell($w[3],6,number_format($nilainya->Harga_Barang,2),'LR',0,'R');
            $this->Cell($w[4],6,$nilainya->Jumlahe,'LR',0,'C');
            $this->Cell($w[5],6,number_format($nilainya->Totale,2),'LR',0,'R');
            $this->Ln();

            $totalNota = $totalNota + $nilainya->Totale;
            
        }

        if(count($list) != 0){
            $this->SetX($x);
            $this->SetFont('Arial','B',10);
            $this->Cell(167,7,"Total Belanja",1,0,'C');
            $this->Cell(33,7,number_format($totalNota,2),1,0,'R');
            $this->Ln();
        }



    }

    function TableStokBarang($mulai, $data){
        // Column widths

        global $totalNota;
        $totalNota = 0; 
        $this->SetFont('Arial','B',10);
        $this->SetTextColor(0,0,0);

        $y = $mulai;
        $x = 5;

        $list = json_decode($data);
        if(count($list) != 0){
            $this->SetXY( $x, $y);
            $this->Cell(30,12,"Kode Barang", 1, 0,'C');
            $this->Cell(50,12,"Nama Barang", 1, 0,'C');
            $this->Cell(80,6,"Stok Barang", 1, 0,'C');
            $this->Cell(40,12,"Storadge", 1, 0,'C');
            $this->Cell(0,6,"", 0, 1,'C');

            $this->SetX($x);
            $this->Cell(80,6,"", 0, 0,'C');
            $this->Cell(20,6,"Awal", 1, 0,'C');
            $this->Cell(20,6,"Masuk", 1, 0,'C');
            $this->Cell(20,6,"Keluar", 1, 0,'C');
            $this->Cell(20,6,"Sisa", 1, 1,'C');
        }

        $this->SetFont('Arial','',10);
        foreach ($list as $row => $nilainya)
        {
            $this->SetX($x);            
            if($row == 0){
                $this->Cell(30,6,$nilainya->Kode_Barang,'LR');
                $this->Cell(50,6,$nilainya->Nama_Barang,'LR');
            }
            else{
                $this->Cell(30,6," ",'LR');
                $this->Cell(50,6," ",'LR');
            }

            $this->Cell(20,6,$nilainya->Awal,'LR', 0, 'C');
            $this->Cell(20,6,$nilainya->Masuk,'LR',0,'C');
            $this->Cell(20,6,$nilainya->Keluar,'LR',0,'C');
            $this->Cell(20,6,$nilainya->Sisa,'LR',0,'C');
            $this->Cell(40,6,$nilainya->Nama_Gudang,'LR',0,'L');
            $this->Ln();        
            $totalNota = $totalNota + $nilainya->Sisa;    
        }

        if(count($list) != 0){
            $this->SetX($x);
            $this->SetFont('Arial','B',10);
            $this->Cell(140,7,"Total Stok",1,0,'C');
            $this->Cell(20,7,$totalNota,1,0,'C');
            $this->Cell(40,7,"",1,0,'R');
            $this->Ln();
        }

    }


    function TablePemasukanLabaRugi($x , $y, $data){

        global $totalNota;
        $totalNota = 0;
        $this->SetFont('Arial','B',10);
        $this->SetTextColor(0,0,0);
        $this->SetXY($x , $y);
        $this->Cell(0,7,"Pendapatan Penjualan", 0, 1,'L');

        $x1 = $x + 10;
        $list = json_decode($data);
        $this->SetFont('Arial','',10);
        foreach ($list as $row => $nilainya)
        {
            $nilaine = $nilainya->Nominal;     
            $this->SetX($x1);            
            $this->Cell(150,6,$nilainya->Item,0,0, 'L');
            $this->Cell(30,6,number_format($nilaine,2), 0, 0, 'R');  
            $this->Ln();   
            $totalNota = $totalNota + $nilaine;
        }

        $x1 = $x + 10;
        if(count($list) != 0){
            $this->SetX($x1);
            $this->SetFont('Arial','B',10);
            $this->Cell(150,7,"Total Penjualan Barang",0,0,'L');
            $this->Cell(30,7,number_format($totalNota,2),'T',0,'R');
            $this->Ln();
        }

        return $totalNota;

    }

    function TableHPPLabaRugi($x , $y, $data){

        global $totalNota;
        $totalNota = 0;
        $this->SetFont('Arial','B',10);
        $this->SetTextColor(0,0,0);
        $this->SetXY($x , $y);
        $this->Cell(0,7,"Harga Pokok Penjualan", 0, 1,'L');

        $x1 = $x + 10;
        $list = json_decode($data);
        $this->SetFont('Arial','',10);
        foreach ($list as $row => $nilainya)
        {
            $nilaine = $nilainya->Nominal; 
            if((int)$nilaine != 0){
                $this->SetX($x1);            
                $this->Cell(100,6,$nilainya->Item,0,0, 'L');
                $this->Cell(30,6,number_format($nilaine,2), 0, 0, 'R');  
                $this->Ln(); 
            }    
            $totalNota = $totalNota + $nilaine;
        }

        $x1 = $x + 10;
        if(count($list) != 0){
            $this->SetX($x1);
            $this->SetFont('Arial','B',10);
            $this->Cell(100,7,"Total Harga Pokok Penjualan",0,0,'L');
            $this->Cell(30,7,"               ",'T',0,'R');
            $this->Cell(50,7,number_format($totalNota,2),0,0,'R');
            $this->Ln();
        }

        return $totalNota;

    }

    function TableTotalLabaKotor($x , $y, $data){

        $this->SetFont('Arial','B',10);
        $this->SetTextColor(0,0,0);
        $this->SetXY($x , $y);
        $this->Cell(160,6,"Total Laba Kotor",0,0, 'L');
        $this->Cell(30,6,number_format($data,2), 'T', 0, 'R');  
        $this->Ln(); 

        return $data;        
    }

    function TableBiayaOperasionalLabaRugi($x , $y, $data){

        global $totalNota;
        $totalNota = 0;
        $this->SetFont('Arial','B',10);
        $this->SetTextColor(0,0,0);
        $this->SetXY($x , $y);
        $this->Cell(0,7,"Biaya Operasional", 0, 1,'L');

        $x1 = $x + 10;
        $list = json_decode($data);
        $this->SetFont('Arial','',10);
        foreach ($list as $row => $nilainya)
        {
            $nilaine = $nilainya->Nominal; 
           // if((int)$nilaine != 0){
                $this->SetX($x1);            
                $this->Cell(100,6,$nilainya->Item,0,0, 'L');
                $this->Cell(30,6,number_format($nilaine,2), 0, 0, 'R');  
                $this->Ln(); 
           // }    
            $totalNota = $totalNota + $nilaine;
        }

        $x1 = $x + 10;
        if(count($list) != 0){
            $this->SetX($x1);
            $this->SetFont('Arial','B',10);
            $this->Cell(100,7,"Total Biaya Operasional",0,0,'L');
            $this->Cell(30,7,"               ",'T',0,'R');
            $this->Cell(50,7,number_format($totalNota,2),0,0,'R');
            $this->Ln();
        }

        return $totalNota;

    }

    function TableTotalLabaOperasional($x , $y, $data){

        $this->SetFont('Arial','B',10);
        $this->SetTextColor(0,0,0);
        $this->SetXY($x , $y);
        $this->Cell(160,6,"Total Laba Operasional",0,0, 'L');
        $this->Cell(30,6,number_format($data,2), 'T', 0, 'R');  
        $this->Ln(); 

        return $data;        
    }


    function TablePemasukanLainLainLabaRugi($x , $y, $data){

        global $totalNota;
        $totalNota = 0;
        $this->SetFont('Arial','B',10);
        $this->SetTextColor(0,0,0);
        $this->SetXY($x , $y);
        $this->Cell(0,7,"Pendapatan Selain Penjualan Barang", 0, 1,'L');

        $x1 = $x + 10;
        $list = json_decode($data);
        $this->SetFont('Arial','',10);
        foreach ($list as $row => $nilainya)
        {
            $nilaine = $nilainya->Nominal;     
            $this->SetX($x1);            
            $this->Cell(150,6,$nilainya->Item,0,0, 'L');
            $this->Cell(30,6,number_format($nilaine,2), 0, 0, 'R');  
            $this->Ln();   
            $totalNota = $totalNota + $nilaine;
        }


        return $totalNota;

    }

    function TableTotalLabaSebelumPajak($x , $y, $data){

        $this->SetFont('Arial','B',10);
        $this->SetTextColor(0,0,0);
        $this->SetXY($x , $y);
        $this->Cell(160,6,"Total Laba Sebelum Pajak",0,0, 'L');
        $this->Cell(30,6,number_format($data,2), 'T', 0, 'R');  
        $this->Ln(); 

        return $data;        
    }

    function TableTotalPajakPenghasilan($x , $y, $data){

        $this->SetFont('Arial','',10);
        $this->SetTextColor(0,0,0);
        $this->SetXY($x , $y);
        $this->Cell(160,6,"Total Pajak Penghasilan",0,0, 'L');
        $this->Cell(30,6,number_format($data,2), 0, 0, 'R');  
        $this->Ln(); 

        return $data;        
    }

    function TableTotalLabaBersih($x , $y, $data){

        $this->SetFont('Arial','B',10);
        $this->SetTextColor(0,0,0);
        $this->SetXY($x , $y);
        $this->Cell(160,6,"Total Laba Bersih",0,0, 'L');
        $this->Cell(30,6,number_format($data,2), 'T', 0, 'R');  
        $this->Ln(); 

        return $data;        
    }

    function TableAktivaLancar($x , $y, $data){

        global $totalNota;
        $totalNota = 0;
        $this->SetFont('Arial','B',10);
        $this->SetTextColor(0,0,0);
        $this->SetXY($x , $y);
        $this->Cell(0,7,"Aktiva Lancar", 0, 1,'L');

        $x1 = $x + 10;
        $list = json_decode($data);
        $this->SetFont('Arial','',10);
        foreach ($list as $row => $nilainya)
        {
            $nilaine = $nilainya->Nominal; 
            $this->SetX($x1);            
            $this->Cell(100,6,$nilainya->Item,0,0, 'L');
            $this->Cell(30,6,number_format($nilaine,2), 0, 0, 'R');  
            $this->Ln();  
            $totalNota = $totalNota + $nilaine;
        }

        $x1 = $x + 10;
        if(count($list) != 0){
            $this->SetX($x1);
            $this->SetFont('Arial','B',10);
            $this->Cell(100,7,"Total Aktiva Lancar",0,0,'L');
            $this->Cell(30,7,"               ",'T',0,'R');
            $this->Cell(50,7,number_format($totalNota,2),0,0,'R');
            $this->Ln();
        }

        return $totalNota;

    }


    function TableAktivaTetap($x , $y, $data){

        global $totalNota;
        $totalNota = 0;
        $this->SetFont('Arial','B',10);
        $this->SetTextColor(0,0,0);
        $this->SetXY($x , $y);
        $this->Cell(0,7,"Aktiva Tetap", 0, 1,'L');

        $x1 = $x + 10;
        $list = json_decode($data);
        $this->SetFont('Arial','',10);
        foreach ($list as $row => $nilainya)
        {
            $nilaine = $nilainya->Nominal; 
            $this->SetX($x1);            
            $this->Cell(100,6,$nilainya->Item,0,0, 'L');
            $this->Cell(30,6,number_format($nilaine,2), 0, 0, 'R');  
            $this->Ln();  
            $totalNota = $totalNota + $nilaine;
        }

        $x1 = $x + 10;
        if(count($list) != 0){
            $this->SetX($x1);
            $this->SetFont('Arial','B',10);
            $this->Cell(100,7,"Total Aktiva Tetap",0,0,'L');
            $this->Cell(30,7,"               ",'T',0,'R');
            $this->Cell(50,7,number_format($totalNota,2),0,0,'R');
            $this->Ln();
        }

        return $totalNota;

    }

    function TableAktivaPiutang($x , $y, $data){

        global $totalNota;
        $totalNota = 0;
        $this->SetFont('Arial','B',10);
        $this->SetTextColor(0,0,0);
        $this->SetXY($x , $y);
        $this->Cell(0,7,"Piutang", 0, 1,'L');

        $x1 = $x + 10;
        $list = json_decode($data);
        $this->SetFont('Arial','',10);
        foreach ($list as $row => $nilainya)
        {
            $nilaine = $nilainya->Nominal; 
            $this->SetX($x1);            
            $this->Cell(100,6,$nilainya->Item,0,0, 'L');
            $this->Cell(30,6,number_format($nilaine,2), 0, 0, 'R');  
            $this->Ln();  
            $totalNota = $totalNota + $nilaine;
        }

        $x1 = $x + 10;
        if(count($list) != 0){
            $this->SetX($x1);
            $this->SetFont('Arial','B',10);
            $this->Cell(100,7,"Total Piutang",0,0,'L');
            $this->Cell(30,7,"               ",'T',0,'R');
            $this->Cell(50,7,number_format($totalNota,2),0,0,'R');
            $this->Ln();
        }

        return $totalNota;

    }

    function TableTotalAktiva($x , $y, $data){

        $this->SetFont('Arial','B',10);
        $this->SetTextColor(0,0,0);
        $this->SetXY($x , $y);
        $this->Cell(160,6,"Total Aktiva",0,0, 'L');
        $this->Cell(30,6,number_format($data,2), 'T', 0, 'R');  
        $this->Ln(); 

        return $data;        
    }

    function TablePasivaUtang($x , $y, $data){

        global $totalNota;
        $totalNota = 0;
        $this->SetFont('Arial','B',10);
        $this->SetTextColor(0,0,0);
        $this->SetXY($x , $y);
        $this->Cell(0,7,"Utang", 0, 1,'L');

        $x1 = $x + 10;
        $list = json_decode($data);
        $this->SetFont('Arial','',10);
        foreach ($list as $row => $nilainya)
        {
            $nilaine = $nilainya->Nominal; 
            $this->SetX($x1);            
            $this->Cell(100,6,$nilainya->Item,0,0, 'L');
            $this->Cell(30,6,number_format($nilaine,2), 0, 0, 'R');  
            $this->Ln();  
            $totalNota = $totalNota + $nilaine;
        }

        $x1 = $x + 10;
        if(count($list) != 0){
            $this->SetX($x1);
            $this->SetFont('Arial','B',10);
            $this->Cell(100,7,"Total Utang",0,0,'L');
            $this->Cell(30,7,"               ",'T',0,'R');
            $this->Cell(50,7,number_format($totalNota,2),0,0,'R');
            $this->Ln();
        }

        return $totalNota;

    }

    function TablePasivaModal($x , $y, $data){

        global $totalNota;
        $totalNota = 0;
        $this->SetFont('Arial','B',10);
        $this->SetTextColor(0,0,0);
        $this->SetXY($x , $y);
        $this->Cell(0,7,"Ekuitas", 0, 1,'L');

        $x1 = $x + 10;
        $list = json_decode($data);
        $this->SetFont('Arial','',10);
        foreach ($list as $row => $nilainya)
        {
            $nilaine = $nilainya->Nominal; 
            $this->SetX($x1);            
            $this->Cell(100,6,$nilainya->Item,0,0, 'L');
            $this->Cell(30,6,number_format($nilaine,2), 0, 0, 'R');  
            $this->Ln();  
            $totalNota = $totalNota + $nilaine;
        }

        $x1 = $x + 10;
        if(count($list) != 0){
            $this->SetX($x1);
            $this->SetFont('Arial','B',10);
            $this->Cell(100,7,"Total Modal",0,0,'L');
            $this->Cell(30,7,"               ",'T',0,'R');
            $this->Cell(50,7,number_format($totalNota,2),0,0,'R');
            $this->Ln();
        }

        return $totalNota;

    }

    function TableTotalPasiva($x , $y, $data){

        $this->SetFont('Arial','B',10);
        $this->SetTextColor(0,0,0);
        $this->SetXY($x , $y);
        $this->Cell(160,6,"Total Pasiva",0,0, 'L');
        $this->Cell(30,6,number_format($data,2), 'T', 0, 'R');  
        $this->Ln(); 

        return $data;        
    }

    function addJudulInvoice(){
        $y = 40;
        $x = 80;
        $this->SetXY( $x, $y);
        $this->SetFont('times','B',16);
        $this->SetTextColor(0,0,0);
        $this->Cell(50,8,"I N V O I C E",'B',1,'C');
        $this->Ln(2);
    }

    function addKeteranganInvoice($x, $y, $data){
        $list = json_decode($data);
        foreach ($list as $row => $nilainya)
        {
        $this->SetXY( $x, $y);
        $this->SetFont('times','',11);
        $this->Cell(20,6,"Customer : ",0 , 0,'L');
        $this->Cell(90,6,$nilainya->Nama_User,0 , 0,'L');
        $this->Cell(26,6,"Nomor Invoice",0 , 0,'L');
        $this->Cell(5,6," : ",0 , 0,'C');
        $this->Cell(80,6,$nilainya->No_Invoice,0 , 1,'L');

        $y = $y + 6;
        $this->SetXY( $x, $y);
        $this->Cell(20,6,"",0 , 0,'L');
        $this->MultiCell(80, 6, $nilainya->Alamat, 0,"L");
        
        $this->SetXY( 120, $y);
        $this->Cell(26,6,"Date",0 , 0,'L');
        $this->Cell(5,6," : ",0 , 0,'C');
        $this->Cell(80,6,$nilainya->Tanggal,0 , 1,'L');

        $y = $y + 6;        
        $this->SetXY( 120, $y);
        $this->Cell(26,6,"Payment",0 , 0,'L');
        $this->Cell(5,6," : ",0 , 0,'C');
        $this->Cell(80,6,$nilainya->Payment,0 , 1,'L');

        $y = $y + 6;
        $this->SetXY( $x, $y);
        $this->Cell(20,6,"",0 , 0,'L');
        $this->Cell(80, 6, $nilainya->Telpon, 0,"L");
        
        $this->SetXY( 120, $y);
        $this->Cell(26,6,"Nomor Faktur",0 , 0,'L');
        $this->Cell(5,6," : ",0 , 0,'C');
        $this->Cell(80,6,"",0 , 1,'L');

        $y = $y + 6;
        $this->SetXY( $x, $y);
        $this->Cell(20,6,"",0 , 0,'L');
        $this->Cell(80, 6, "", 0,"L");
        
        $this->SetXY( 120, $y);
        $this->Cell(26,6,"Currency",0 , 0,'L');
        $this->Cell(5,6," : ",0 , 0,'C');
        $this->Cell(80,6,"IDR",0 , 1,'L');
        }
    }

    function TableInvoicePenjualan($x, $y, $data){
        // Column widths
        global $totalNota;
        $totalNota = 0;

        $this->SetFont('Arial','B',10);
        $this->SetTextColor(0,0,0);

        $list = json_decode($data);
        if(count($list) != 0){
            $this->SetXY( $x, $y);
            $this->Cell(30,7,"Kode Barang", 0, 0,'C');
            $this->Cell(70,7,"Nama Barang", 0, 0,'C');
            $this->Cell(20,7,"Qty", 0, 0,'C');
            $this->Cell(30,7,"Harga Satuan", 0, 0,'C');
            $this->Cell(40,7,"Total Harga", 0, 0,'C');
            $this->Cell(0,7,"", 0, 1,'C');
        }

        $this->SetFont('Arial','',10);
        foreach ($list as $row => $nilainya)
        {
            $this->SetX($x);            
            $this->Cell(30,7,$nilainya->Kode_Barang, 0 , 0, 'C');
            $this->Cell(70,7,$nilainya->Nama_Barang, 0, 0, 'L');
            $this->Cell(20,7,$nilainya->Jumlah, 0, 0, 'C');
            $this->Cell(30,7,number_format($nilainya->Harga_Barang,2), 0, 0,'R');
            $this->Cell(40,7,number_format($nilainya->Total_Harga,2), 0, 0,'R');
            $this->Ln();        
            $totalNota = $totalNota + $nilainya->Total_Harga;
        }

        return $totalNota;

    }

    function addColInvoiceJual($x,$y, $max){
        $r1  = $x;
        $r2  = $this->w - ($r1 * 2) ;
        $y1  = $y;
        $y2  = $this->h - $max - $y1;
        $this->SetXY( $r1, $y1 );
        $this->Rect( $r1, $y1, $r2, $y2, "D");
        $this->Line( $r1, $y1+7, $r1+$r2, $y1+7);
        $this->Line( 40 , $y1, 40 , $y1+$y2);
        $this->Line( 110 , $y1, 110 , $y1+$y2);
        $this->Line( 130 , $y1, 130 , $y1+$y2);
        $this->Line( 160 , $y1, 160 , $y1+$y2);
        return $this->h;
    }

    function TableTotalInvoicePenjualan($x , $y, $data){

        $this->SetFont('Arial','B',10);
        $this->SetTextColor(0,0,0);
        $this->SetXY($x , $y);
        $this->Cell(120,7," ",0,0, 'L');
        $this->Cell(30,7,"Sub Total",'LRB',0, 'C');
        $this->Cell(40,7,number_format($data,2), 'LRB', 1, 'R');  

        $ppne = $data * 0;
        $this->Cell(120,7," ",0,0, 'L');
        $this->Cell(30,7,"PPn",'LRB',0, 'C');
        $this->Cell(40,7,number_format($ppne,2), 'LRB', 1, 'R');  

        $totale = $data + $ppne;
        $this->Cell(120,7," ",0,0, 'L');
        $this->Cell(30,7,"TOTAL",'LRB',0, 'C');
        $this->Cell(40,7,number_format($totale,2), 'LRB', 1, 'R');  
        return $this->GetY();        
    }

    function addPJ($x, $y){
        $this->SetFont( "Arial", "", 10);
        $this->SetXY( $x, $y );
        $this->Cell(40,6, "Hormat Kami,", 0, 0, "C");

        $this->SetFont( "Arial", "B", 10);
        $this->SetXY( $x, $y + 20 );
        $this->Cell(40,6, "Si Kucluk", 'B', 1, "C");
        $this->Cell(40,6, "Manager Keuangan", 0, 1, "C");
    }

    function addJudulPengantarStoradge(){
        $y = 40;
        $x = 78;
        $this->SetXY( $x, $y);
        $this->SetFont('times','B',16);
        $this->SetTextColor(0,0,0);
        $this->Cell(65,8,"S U R A T     J A L A N",'B',1,'C');
        $this->Ln(2);
    }

    function addKeteranganPengantarStoradge($x, $y, $data){
        $list = json_decode($data);
        foreach ($list as $row => $nilainya)
        {
        $this->SetXY( $x, $y);
        $this->SetFont('times','',11);
        $this->Cell(20,6,"Storadge : ",0 , 0,'L');
        $this->Cell(90,6,$nilainya->Nama_Storadge,0 , 0,'L');
        $this->Cell(26,6,"Nomor Invoice",0 , 0,'L');
        $this->Cell(5,6," : ",0 , 0,'C');
        $this->Cell(80,6,$nilainya->No_Invoice,0 , 1,'L');

        $y = $y + 6;
        $this->SetXY( $x, $y);
        $this->Cell(20,6,"",0 , 0,'L');
        $this->MultiCell(80, 6, $nilainya->Alamat_Storadge, 0,"L");
        
        $this->SetXY( 120, $y);
        $this->Cell(26,6,"Date",0 , 0,'L');
        $this->Cell(5,6," : ",0 , 0,'C');
        $this->Cell(80,6,$nilainya->Tanggal_Kirim,0 , 1,'L');

        $y = $y + 6;        
        $this->SetXY( 120, $y);
        $this->Cell(26,6,"Sopir",0 , 0,'L');
        $this->Cell(5,6," : ",0 , 0,'C');
        $this->Cell(80,6, $nilainya->Nama_Pengirim ,0 , 1,'L');

        $y = $y + 6;
        $this->SetXY( $x, $y);
        $this->Cell(20,6,"",0 , 0,'L');
        $this->Cell(80, 6, $nilainya->Phone_Storadge, 0,"L");
        
        $this->SetXY( 120, $y);
        $this->Cell(26,6,"No. KTP",0 , 0,'L');
        $this->Cell(5,6," : ",0 , 0,'C');
        $this->Cell(80,6,$nilainya->No_KTP_Pengirim,0 , 1,'L');

        $y = $y + 6;
        $this->SetXY( $x, $y);
        $this->Cell(20,6,"Penerima ",0 , 0,'L');
        $this->Cell(80, 6, $nilainya->Pemilik_Storadge, 0,"L");
        
        $this->SetXY( 120, $y);
        $this->Cell(26,6,"No. Telpon",0 , 0,'L');
        $this->Cell(5,6," : ",0 , 0,'C');
        $this->Cell(80,6,$nilainya->Telpon_Pengirim,0 , 1,'L');

        $y = $y + 6;
        $this->SetXY( $x, $y);
        $this->Cell(20,6,"",0 , 0,'L');
        $this->Cell(80, 6, "", 0,"L");
        
        $this->SetXY( 120, $y);
        $this->Cell(26,6,"No. Kendaraan",0 , 0,'L');
        $this->Cell(5,6," : ",0 , 0,'C');
        $this->Cell(80,6,$nilainya->Plat_Kendaraan,0 , 1,'L');
        }
    }

    function addColPengantarStoradge($x,$y, $max){
        $r1  = $x;
        $r2  = $this->w - ($r1 * 2) ;
        $y1  = $y;
        $y2  = $this->h - $max - $y1;
        $this->SetXY( $r1, $y1 );
        $this->Rect( $r1, $y1, $r2, $y2, "D");
        $this->Line( $r1, $y1+7, $r1+$r2, $y1+7);
        $this->Line( 40 , $y1, 40 , $y1+$y2);
        $this->Line( 110 , $y1, 110 , $y1+$y2);
        $this->Line( 145 , $y1, 145 , $y1+$y2);
        return $this->h;
    }

    function TablePengantarStoradge($x, $y, $data){
        // Column widths
        global $totalNota;
        $totalNota = 0;

        $this->SetFont('Arial','B',10);
        $this->SetTextColor(0,0,0);

        $list = json_decode($data);
        if(count($list) != 0){
            $this->SetXY( $x, $y);
            $this->Cell(30,7,"Kode Barang", 0, 0,'C');
            $this->Cell(70,7,"Nama Barang", 0, 0,'C');
            $this->Cell(35,7,"Qty (Kg)", 0, 0,'C');
            $this->Cell(55,7,"Keterangan", 0, 0,'C');
            $this->Cell(0,7,"", 0, 1,'C');
        }


        $this->SetFont('Arial','',10);
        foreach ($list as $row => $nilainya)
        {
            $this->SetX($x);            
            $this->Cell(30,7,$nilainya->Kode_Barang, 0 , 0, 'C');
            $this->Cell(70,7,$nilainya->Nama_Barang, 0, 0, 'L');
            $this->Cell(35,7,$nilainya->Jumlah, 0, 0, 'C');
            $this->Cell(55,7," ", 0, 0,'R');
            $this->Ln();        
        }
        return $totalNota;

    }

    function addPJPengantarStoradge($x, $y, $data){
        $namaSopir;
        $list = json_decode($data);
        foreach ($list as $row => $nilainya)
        {
            $namaSopir = $nilainya->Nama_Pengirim;

        }

        $this->SetFont( "Arial", "", 10);
        $this->SetXY( $x, $y );
        $this->Cell(60,6, "Hormat Kami,", 0, 0, "C");
        $this->Cell(70,6, "Bagian Pengiriman", 0, 0, "C");
        $this->Cell(70,6, "Storadge", 0, 1, "C");



        $this->SetXY( $x + 10 , $y + 25 );
        $this->Cell(40,6, "Si Kucluk", 'B' , 0,  "C");
        $this->SetXY( $x + 75, $y + 25 );
        $this->Cell(40,6, $namaSopir , 'B' , 0, "C");
        $this->SetXY( $x + 145, $y + 25 );
        $this->Cell(40,6,  " " , 'B' ,  1, "C");

        $this->SetFont( "Arial", "B", 10);
        $this->SetX( $x );
        $this->Cell(60,6, "( Manager Keuangan )", 0, 0, "C");
        $this->Cell(70,6, "(       Sopir      )", 0, 0, "C");
        $this->Cell(70,6, "( Pengawas Lapangan )", 0, 1, "C");
    }

}

?>