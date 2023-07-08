<?php defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Jakarta');
class Datapengukuran_model extends CI_Model
{


    function insertPengukuran($datane){

        $kueri = $this->db->insert('report_ukur', $datane);
        if($kueri){
            return "sukses";
        }
        else{
            return "gagal";
        }
    }

    function getLapPengukuran(){
        $datane = array();
        $this->db->select('No_ID, NIK, Tanggal, Berat_Badan, Panjang_Badan, Lingkar_Lengan, Lingkar_Kepala, Cara_Ukur');
        //$this->db->group_by(array("Tanggal", "NIK"));
        $this->db->order_by("No_ID", "desc");
        $kueri = $this->db->get('report_ukur');
        foreach ($kueri->result() as $row)
        {
            $kodeAnak = $row->NIK;
            $kuncine = "NIK_Anak = '$kodeAnak'";
            $this->db->select('NIK_Anak, Nama_Anak, Tanggal_Lahir, Jenis_Kelamin');
            $this->db->where($kuncine);
            $dtbase = $this->db->get('list_akun')->row();
            $namane = $dtbase->Nama_Anak;
            $tanggale = $dtbase->Tanggal_Lahir;
            $item['No_ID'] = $row->No_ID;
            $item['Tanggal'] = $row->Tanggal;
            $item['NIK_Anak'] = $dtbase->NIK_Anak;
            $item['Nama_Anak'] = $namane;
            $item['Tanggal_Lahir'] = $tanggale;
            $item['Jenis_Kelamin'] = $dtbase->Jenis_Kelamin;
            $item['Berat_Badan'] = $row->Berat_Badan;
            $item['Tinggi_Badan'] = $row->Panjang_Badan;
            $item['Lingkar_Lengan'] = $row->Lingkar_Lengan;
            $item['Lingkar_Kepala'] = $row->Lingkar_Kepala;
            $item['Cara_Ukur'] = $row->Cara_Ukur;

            $datane[] = $item;    
        }

        return $datane;
    }

    function getFilterLapPengukuran($tglMulai,$tglSelesai){
        $datane = array();
        $kunci = "Tanggal >= '$tglMulai' AND Tanggal <= '$tglSelesai'";
        $this->db->select('No_ID, NIK, Tanggal, Berat_Badan, Panjang_Badan, Lingkar_Lengan, Lingkar_Kepala, Cara_Ukur');
        $this->db->where($kunci);
        //$this->db->group_by(array("Tanggal", "NIK"));
        $this->db->order_by("No_ID", "desc");
        $kueri = $this->db->get('report_ukur');
        foreach ($kueri->result() as $row)
        {
            $kodeAnak = $row->NIK;
            $kuncine = "NIK_Anak = '$kodeAnak'";
            $this->db->select('NIK_Anak, Nama_Anak, Tanggal_Lahir, Jenis_Kelamin');
            $this->db->where($kuncine);
            $dtbase = $this->db->get('list_akun')->row();
            $namane = $dtbase->Nama_Anak;
            $tanggale = $dtbase->Tanggal_Lahir;
            $item['No_ID'] = $row->No_ID;
            $item['Tanggal'] = $row->Tanggal;
            $item['NIK_Anak'] = $dtbase->NIK_Anak;
            $item['Nama_Anak'] = $namane;
            $item['Tanggal_Lahir'] = $tanggale;
            $item['Jenis_Kelamin'] = $dtbase->Jenis_Kelamin;
            $item['Berat_Badan'] = $row->Berat_Badan;
            $item['Tinggi_Badan'] = $row->Panjang_Badan;
            $item['Lingkar_Lengan'] = $row->Lingkar_Lengan;
            $item['Lingkar_Kepala'] = $row->Lingkar_Kepala;
            $item['Cara_Ukur'] = $row->Cara_Ukur;

            $datane[] = $item;    
        }

        return $datane;
    }

    function getSortLapPengukuranByName($nile){
        $datane = array();
        $this->db->select('No_ID, NIK, Nama_Anak, Tanggal, Berat_Badan, Panjang_Badan, Lingkar_Lengan, Lingkar_Kepala, Cara_Ukur');
        $this->db->like('Nama_Anak', $nile);
        //$this->db->group_by(array("Tanggal", "NIK"));
        $this->db->order_by("No_ID", "desc");
        $kueri = $this->db->get('report_ukur');
        foreach ($kueri->result() as $row)
        {
            $kodeAnak = $row->NIK;
            $kuncine = "NIK_Anak = '$kodeAnak'";
            $this->db->select('NIK_Anak, Nama_Anak, Tanggal_Lahir, Jenis_Kelamin');
            $this->db->where($kuncine);
            $dtbase = $this->db->get('list_akun')->row();
            $namane = $dtbase->Nama_Anak;
            $tanggale = $dtbase->Tanggal_Lahir;
            $item['No_ID'] = $row->No_ID;
            $item['Tanggal'] = $row->Tanggal;
            $item['NIK_Anak'] = $dtbase->NIK_Anak;
            $item['Nama_Anak'] = $namane;
            $item['Tanggal_Lahir'] = $tanggale;
            $item['Jenis_Kelamin'] = $dtbase->Jenis_Kelamin;
            $item['Berat_Badan'] = $row->Berat_Badan;
            $item['Tinggi_Badan'] = $row->Panjang_Badan;
            $item['Lingkar_Lengan'] = $row->Lingkar_Lengan;
            $item['Lingkar_Kepala'] = $row->Lingkar_Kepala;
            $item['Cara_Ukur'] = $row->Cara_Ukur;

            $datane[] = $item;    
        }

        return $datane;
    }
    
    function getDetailPengukuranByNoID($idne){
        $datane = array();
        $kunci = "No_ID = '$idne'";

        $this->db->select('NIK, Tanggal, Berat_Badan, Panjang_Badan, Lingkar_Lengan, Lingkar_Kepala, Cara_Ukur, Vitamin, Pemberian_ASI');
        $this->db->where($kunci);
        $this->db->order_by("Tanggal", "desc");

        $kueri = $this->db->get('report_ukur');

        foreach ($kueri->result() as $row)
        {
            $kodeAnak = $row->NIK;
            $kuncine = "NIK_Anak = '$kodeAnak'";
            $this->db->select('NIK_Anak, Nama_Anak, Tanggal_Lahir, Jenis_Kelamin, Nama_Ortu, Alamat');
            $this->db->where($kuncine);
            $dtbase = $this->db->get('list_akun')->row();
            $namane = $dtbase->Nama_Anak;
            $tanggale = $dtbase->Tanggal_Lahir;

            $item['Tanggal'] = $row->Tanggal;
            $item['NIK_Anak'] = $dtbase->NIK_Anak;
            $item['Nama_Anak'] = $namane;
            $item['Tanggal_Lahir'] = $tanggale;
            $item['Jenis_Kelamin'] = $dtbase->Jenis_Kelamin;
            $item['Nama_Ortu'] = $dtbase->Nama_Ortu;
            $item['Alamat'] = $dtbase->Alamat;
            $item['Berat_Badan'] = $row->Berat_Badan;
            $item['Tinggi_Badan'] = $row->Panjang_Badan;
            $item['Lingkar_Lengan'] = $row->Lingkar_Lengan;
            $item['Lingkar_Kepala'] = $row->Lingkar_Kepala;
            $item['Cara_Ukur'] = $row->Cara_Ukur;
            $item['Vitamin'] = $row->Vitamin;
            $item['ASI'] = $row->Pemberian_ASI;
            $datane[] = $item;    
        }

        return $datane;
    }

    function getHistoryPengukuranByNIK($idne){
        $datane = array();
        $kunci = "NIK = '$idne'";

        $this->db->select('NIK, Tanggal, Berat_Badan, Panjang_Badan, Lingkar_Lengan, Lingkar_Kepala, Cara_Ukur');
        $this->db->where($kunci);
        $this->db->order_by("Tanggal", "desc");
        $kueri = $this->db->get('report_ukur');
        foreach ($kueri->result() as $row)
        {
            $kodeAnak = $row->NIK;
            $kuncine = "NIK_Anak = '$kodeAnak'";
            $this->db->select('NIK_Anak, Nama_Anak, Tanggal_Lahir, Jenis_Kelamin, Nama_Ortu, Alamat');
            $this->db->where($kuncine);
            $dtbase = $this->db->get('list_akun')->row();
            $namane = $dtbase->Nama_Anak;
            $tanggale = $dtbase->Tanggal_Lahir;

            $item['Tanggal'] = $row->Tanggal;
            $item['NIK_Anak'] = $dtbase->NIK_Anak;
            $item['Nama_Anak'] = $namane;
            $item['Tanggal_Lahir'] = $tanggale;
            $item['Jenis_Kelamin'] = $dtbase->Jenis_Kelamin;
            $item['Nama_Ortu'] = $dtbase->Nama_Ortu;
            $item['Alamat'] = $dtbase->Alamat;
            $item['Berat_Badan'] = $row->Berat_Badan;
            $item['Tinggi_Badan'] = $row->Panjang_Badan;
            $item['Lingkar_Lengan'] = $row->Lingkar_Lengan;
            $item['Lingkar_Kepala'] = $row->Lingkar_Kepala;
            $item['Cara_Ukur'] = $row->Cara_Ukur;

            $datane[] = $item;    
        }

        return $datane;
    }

    function deletePengukuranByID($datane){
        $this->db->where($datane);
        $kueri = $this->db->delete('report_ukur');
        if($kueri){
            return "sukses";
        }
        else{
            return "gagal";
        }
    }


}