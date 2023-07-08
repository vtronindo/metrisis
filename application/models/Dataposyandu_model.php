<?php defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Jakarta');
class Dataposyandu_model extends CI_Model
{

    function kodePosyandu($vare){
        $this->db->where($vare);
        $kono = $this->db->get('id_posyandu')->row();
        $idne = $kono->No_ID;
        $noUrut = (int) $idne;	
        return $noUrut;
    }

    function getLoginKader($varLogin,$pass){
        $statuse = "";
        $this->db->where($varLogin);
        $kono = $this->db->get('id_kader')->row();
        
        if($kono){
            if($pass == $kono->Kata_Sandi){ 
                $statuse = $kono;
            }
            else{
                $statuse = "Password Tidak Sesuai";
            }
        }
        else{
            $statuse = "Data Tidak Ditemukan";
        }

        return $statuse;
    }

    function getPosyanduByID($nile){
        $this->db->where('No_ID',$nile);
        return $this->db->get('id_posyandu')->result();
    }


    function getKaderByPosyandu($nile){
        $syarate = array('ID_Posyandu' => $nile, 'View' => '1',);
        $this->db->where($syarate);
        return $this->db->get('id_kader')->result();
    }

    function getKaderByID($idne,$nile){
        $syarate = array('No_ID' => $idne, 'ID_Posyandu' => $nile, 'View' => '1',);
        $this->db->where($syarate);
        return $this->db->get('id_kader')->result();
    }

    function insertPosyandu($datane){
        $kueri = $this->db->insert('id_posyandu', $datane);
        if($kueri){
            return "sukses";
        }
        else{
            return "gagal";
        }
    }

    function insertKader($idPost, $nama, $noTelp, $pass,$emaile){
        
        $syarate = array('ID_Posyandu' => $idPost, 'View' => '1',);
        $this->db->from('id_kader');
        $this->db->where($syarate);
        $jumlahe = $this->db->count_all_results();

        if($jumlahe == 0){
            $varAdmin = array(
                'Nama_Kader' => $nama,
                'No_Telpon' => $noTelp,
                'Kata_Sandi' => $pass,
                'Email' => $emaile,
                'ID_Posyandu' => $idPost,
                'Status_Admin' => 'Admin',
                'View' => '1',
            );    
            $kueri = $this->db->insert('id_kader', $varAdmin);
            if($kueri){
                return "sukses";
            }
            else{
                return "gagal";
            }
    
        }
        else if($jumlahe > 0 && $jumlahe <= 3){
            $varKader = array(
                'Nama_Kader' => $nama,
                'No_Telpon' => $noTelp,
                'Kata_Sandi' => $pass,
                'Email' => $emaile,
                'ID_Posyandu' => $idPost,
                'Status_Admin' => '',
                'View' => '1',
            );    
            $kueri = $this->db->insert('id_kader', $varKader);
            if($kueri){
                return "sukses";
            }
            else{
                return "gagal";
            }
    
        }
        else{
            return "Kader Maksimal 3 Orang";
        }

    }

    function updateKader($syrt,$datane){
        $this->db->set($datane);
        $this->db->where($syrt);
        $kueri = $this->db->update('id_kader');
        if($kueri){
            $status = "sukses";
        }
        else{
            $status = "gagal";
        }
        return $status;
    }

    function updatePosyandu($syrt,$datane){
        $this->db->set($datane);
        $this->db->where($syrt);
        $kueri = $this->db->update('id_posyandu');
        if($kueri){
            $status = "sukses";
        }
        else{
            $status = "gagal";
        }
        return $status;
    }

    function insertPengukuran($datane, $tgle, $kiane, $umure, $asine, $vita, $stAsi, $stVit, $stPMT, $vPMT){
        $statuse = "";
        $kuncine = "NIK_Anak = '$kiane' AND Tanggal_Ukur = '$tgle' AND View = '1' OR NIK_Anak = '$kiane' AND Usia_Ukur = '$umure' AND View = '1'";
        $this->db->where($kuncine);
        //        $this->db->order_by("No_ID", "desc");    
        //        $this->db->limit(1);
        $dtCheck = $this->db->get('report_ukur')->row();
        if(!$dtCheck){
            $kueri = $this->db->insert('report_ukur', $datane);
            if($stAsi == "1"){
                $kueri1 = $this->db->insert('id_asi_anak', $asine);
            }
            if($stVit == "1"){
                $kueri2 = $this->db->insert('id_vitamin_anak', $vita);
            }
            if($stPMT == "Ya"){
                $kueri3 = $this->db->insert('id_pmt', $vPMT);
            }
            if($kueri){
                $statuse = "sukses";
            }
            else{
                $statuse = "gagal";
            }
    
        }
        else{
            $statuse = "Sudah Melakukan Pengukuran Pada Tanggal " .  $dtCheck->Tanggal_Ukur;
        }


        return $statuse;
    }  
    
    function tambahkeVitamine($varVit){
        $kueri = $this->db->insert('id_vitamin_anak', $varVit);
        if($kueri){
            $statuse = "sukses";
        }
        else{
            $statuse = "gagal";
        }
        return $statuse;
    }

    function tambahkeLaporane($datane, $tgle, $kiane, $umure){
        $statuse = "";
        $kuncine = "NIK_Anak = '$kiane' AND Tanggal_Ukur = '$tgle' AND View = '1' OR NIK_Anak = '$kiane' AND Usia_Ukur = '$umure' AND View = '1'";
        $this->db->where($kuncine);
        $dtCheck = $this->db->get('report_ukur')->row();
        if(!$dtCheck){
            $kueri = $this->db->insert('report_ukur', $datane);
            if($kueri){
                $statuse = "sukses";
            }
            else{
                $statuse = "gagal";
            }    
        }
        else{
            $statuse = "gagal";
        }

        return $statuse;
    }  

    function tambahkeAsine($varAsi){
        $kueri = $this->db->insert('id_asi_anak', $varAsi);
        if($kueri){
            $statuse = "sukses";
        }
        else{
            $statuse = "gagal";
        }
        return $statuse;
    }

    function tambahkePMTne($varPmt){
        $kueri = $this->db->insert('id_pmt', $varPmt);
        if($kueri){
            $statuse = "sukses";
        }
        else{
            $statuse = "gagal";
        }
        return $statuse;
    }


    function getLapPengukuran($idne,$delok){
        $datane = array();
        $syarate = array('ID_Posyandu' => $idne, 'View' => $delok,);
        $this->db->where($syarate);
        $this->db->order_by("No_ID", "desc");
        $kueri = $this->db->get('report_ukur');
        foreach ($kueri->result() as $row)
        {
            $kodeAnak = $row->NIK_Anak;
            $kuncine = "NIK_Anak = '$kodeAnak'";
            $this->db->where($kuncine);
            $dtAnak = $this->db->get('id_anak')->row();

            $item['No_ID'] = $row->No_ID;
            $item['Tanggal_Ukur'] = $row->Tanggal_Ukur;
            $item['NIK_Anak'] = $dtAnak->NIK_Anak;
            $item['Nama_Anak'] = $dtAnak->Nama_Anak;
            $item['Jenis_Kelamin'] = $dtAnak->Jenis_Kelamin;
            $item['Berat'] = $row->Berat;
            $item['Tinggi'] = $row->Tinggi;
            $datane[] = $item;    
        }

        return $datane;
    }

    function sortLapPengukuranByKIA($idne, $kiane, $delok){
        $datane = array();
    
        $syarate = array('ID_Posyandu' => $idne, 'View' => $delok,);

        $this->db->where($syarate);
        $this->db->like('Nama_Anak', $kiane);
        $this->db->order_by("No_ID", "desc");
        $kueri = $this->db->get('report_ukur');
        foreach ($kueri->result() as $row)
        {
            $kodeAnak = $row->NIK_Anak;
            $kuncine = "NIK_Anak = '$kodeAnak'";
            $this->db->where($kuncine);
            $dtAnak = $this->db->get('id_anak')->row();

            $item['No_ID'] = $row->No_ID;
            $item['Tanggal_Ukur'] = $row->Tanggal_Ukur;
            $item['NIK_Anak'] = $dtAnak->NIK_Anak;
            $item['Nama_Anak'] = $dtAnak->Nama_Anak;
            $item['Jenis_Kelamin'] = $dtAnak->Jenis_Kelamin;
            $item['Berat'] = $row->Berat;
            $item['Tinggi'] = $row->Tinggi;
            $datane[] = $item;    
        }

        return $datane;
    }

    function filterLapPengukuranByDate($idne, $tglMulai, $tglSelesai){
        $datane = array();
    
        $kunci = "Tanggal_Ukur >= '$tglMulai' AND Tanggal_Ukur <= '$tglSelesai' AND ID_Posyandu = '$idne' AND View = '1'";
        $this->db->where($kunci);
        $this->db->order_by("No_ID", "desc");
        $kueri = $this->db->get('report_ukur');
        foreach ($kueri->result() as $row)
        {
            $kodeAnak = $row->NIK_Anak;
            $kuncine = "NIK_Anak = '$kodeAnak'";
            $this->db->where($kuncine);
            $dtAnak = $this->db->get('id_anak')->row();

            $item['No_ID'] = $row->No_ID;
            $item['Tanggal_Ukur'] = $row->Tanggal_Ukur;
            $item['NIK_Anak'] = $dtAnak->NIK_Anak;
            $item['Nama_Anak'] = $dtAnak->Nama_Anak;
            $item['Jenis_Kelamin'] = $dtAnak->Jenis_Kelamin;
            $item['Berat'] = $row->Berat;
            $item['Tinggi'] = $row->Tinggi;
            $datane[] = $item;    
        }

        return $datane;
    }

    function getDetailPengukuran($idne){
        $datane = array();
        $this->db->where('No_ID',$idne);
        $row = $this->db->get('report_ukur')->row();
        if ($row)
        {
            $kodeAnak = $row->NIK_Anak;
            $kuncine = "NIK_Anak = '$kodeAnak' and View = '1'";
            $this->db->where($kuncine);
            $dtAnak = $this->db->get('id_anak')->row();


            $item['No_ID'] = $row->No_ID;
            $item['NIK_Anak'] = $dtAnak->NIK_Anak;
            $item['Nama_Anak'] = $dtAnak->Nama_Anak;
            $item['Tanggal_Lahir'] = $dtAnak->Tanggal_Lahir;
            $item['Jenis_Kelamin'] = $dtAnak->Jenis_Kelamin;
            $item['Berat_Lahir'] = $dtAnak->Berat_Lahir;
            $item['Tinggi_Lahir'] = $dtAnak->Tinggi_Lahir;


            $item['Tanggal_Ukur'] = $row->Tanggal_Ukur;
            $item['Usia_Ukur'] = $row->Usia_Ukur;
            $item['Cara_Ukur'] = $row->Cara_Ukur;
            $item['ASI'] = $row->ASI;
            $item['PMT'] = $row->PMT;
            $item['Vitamin'] = $row->Vitamin;

            $item['Berat'] = $row->Berat;
            $item['Tinggi'] = $row->Tinggi;
            $item['Lila'] = $row->Lingkar_Kepala;
            $item['Lile'] = $row->Lingkar_Lengan;

            $usiane = $row->Usia_Ukur;
            $kunciPMT = "NIK_Anak = $row->NIK_Anak and Tanggal_Pemberian = '$row->Tanggal_Ukur' and View = '1'";
            $this->db->where($kunciPMT);
            $dtPMT = $this->db->get('id_pmt')->row();
            if($dtPMT){
                $item['Sumber_PMT'] = $dtPMT->Sumber_PMT;
                $item['Pem_Pusat'] = $dtPMT->Pemberian_Pusat;
                $item['Tahun_Produksi'] = $dtPMT->Tahun_Produksi;
                $item['Pem_Daerah'] = $dtPMT->Pemberian_Daerah;    
            }
            else{
                $item['Sumber_PMT'] = "";
                $item['Pem_Pusat'] = "";
                $item['Tahun_Produksi'] = "";
                $item['Pem_Daerah'] = "";
    
            }


            $usiane = $row->Usia_Ukur;
            $kunciTerakhir = "No_ID < '$idne' and NIK_Anak = $row->NIK_Anak and View = '1'";
            $this->db->where($kunciTerakhir);
            $this->db->order_by("Tanggal_Ukur", "desc");    
            $this->db->limit(1);
            $dtMbuh = $this->db->get('report_ukur')->row();
        
            if($dtMbuh){
                $item['Berat_Terakhir'] = $dtMbuh->Berat;
                $item['Tinggi_Terakhir'] = $dtMbuh->Tinggi;
                $item['Perubahan_Berat'] = $row->Berat - $dtMbuh->Berat;        
                $item['Perubahan_Tinggi'] = $row->Tinggi - $dtMbuh->Tinggi;                        
            }
            else{
                $item['Berat_Terakhir'] = $row->Berat;
                $item['Tinggi_Terakhir'] = $row->Tinggi;
                $item['Perubahan_Berat'] = $row->Berat - $dtAnak->Berat_Lahir;        
                $item['Perubahan_Tinggi'] = $row->Tinggi - $dtAnak->Tinggi_Lahir;        

            }



            $datane[] = $item;    
        }

        return $datane;
    }

    function getHistoryPengukuranByKIA($idne){
        $datane = array();
        $syarate = array('NIK_Anak' => $idne, 'View' => '1',);
        $this->db->where($syarate);
        $this->db->order_by("Tanggal_Ukur", "desc");
        $kueri = $this->db->get('report_ukur');
        foreach ($kueri->result() as $row)
        {
            $kodeAnak = $row->NIK_Anak;
            $kuncine = "NIK_Anak = '$kodeAnak'";
            $this->db->where($kuncine);
            $dtAnak = $this->db->get('id_anak')->row();


            $kodePosyandu = $row->ID_Posyandu;
            $kuncine = "No_ID = '$kodePosyandu'";
            $this->db->where($kuncine);
            $dtPos = $this->db->get('id_posyandu')->row();

            $kodeKader = $row->ID_Kader;
            $kuncine = "No_ID = '$kodeKader'";
            $this->db->where($kuncine);
            $dtKader = $this->db->get('id_kader')->row();

            $item['Posyandu'] = $dtPos->Nama_Posyandu;
            $item['Kader'] = $dtKader->Nama_Kader;
            $item['Jenis_Kelamin'] = $dtAnak->Jenis_Kelamin;

            $item['Tanggal_Ukur'] = $row->Tanggal_Ukur;
            $item['Usia_Ukur'] = $row->Usia_Ukur;
            $item['Cara_Ukur'] = $row->Cara_Ukur;
            $item['ASI'] = $row->ASI;
            $item['PMT'] = $row->PMT;
            $item['Vitamin'] = $row->Vitamin;

            $item['Berat'] = $row->Berat;
            $item['Tinggi'] = $row->Tinggi;
            $item['Lila'] = $row->Lingkar_Kepala;
            $item['Lile'] = $row->Lingkar_Lengan;

            $datane[] = $item;    
        }

        return $datane;
    }

    function deletePengukuran($idne){
        $syarate = array('No_ID' => $idne,);
        $this->db->where($syarate);
        $kueri = $this->db->get('report_ukur')->row();
        if($kueri){
            $varUkur = array(
                'View' => "0",
            );
            $kunciUkur = array('No_ID' => $kueri->No_ID,);    
            $this->db->set($varUkur);
            $this->db->where($kunciUkur);
            $dtUkur = $this->db->update('report_ukur');

            $kunciASI = array('NIK_Anak' => $kueri->NIK_Anak, 'Tanggal_Pengisian' => $kueri->Tanggal_Ukur, 'View' => '1',);    
            $cekAsi = $this->db->get('id_asi_anak')->row();
            if($cekAsi){
                $this->db->set($varUkur);
                $this->db->where($kunciASI);
                $dtAsi = $this->db->update('id_asi_anak');
            }

            $kunciVit = array('NIK_Anak' => $kueri->NIK_Anak, 'Tanggal_Pemberian' => $kueri->Tanggal_Ukur, 'View' => '1',);    
            $cekVit = $this->db->get('id_vitamin_anak')->row();
            if($cekVit){
                $this->db->set($varUkur);
                $this->db->where($kunciVit);
                $dtAsi = $this->db->update('id_vitamin_anak');
            }

            if($dtUkur){
                $status = "sukses";
            }
            else{
                $status = "gagal";
            }
    
        }
        else{
            $status = "gagal";
        }
 
        return $status;
    }

    function getCetakPengukuran($idne, $tglMulai, $tglSelesai){
        $datane = array();
        $kunci = "Tanggal_Ukur >= '$tglMulai' AND Tanggal_Ukur <= '$tglSelesai' AND ID_Posyandu = '$idne' AND View = '1'";
        $this->db->where($kunci);
        $this->db->order_by("Tanggal_Ukur", "desc");
        $kueri = $this->db->get('report_ukur');
        foreach ($kueri->result() as $row)
        {
            $kodeAnak = $row->NIK_Anak;
            $kuncine = "NIK_Anak = '$kodeAnak'";
            $this->db->where($kuncine);
            $dtAnak = $this->db->get('id_anak')->row();


            $kodePosyandu = $row->ID_Posyandu;
            $kuncine = "No_ID = '$kodePosyandu'";
            $this->db->where($kuncine);
            $dtPos = $this->db->get('id_posyandu')->row();

            $kodeKader = $row->ID_Kader;
            $kuncine = "No_ID = '$kodeKader'";
            $this->db->where($kuncine);
            $dtKader = $this->db->get('id_kader')->row();

            $kunciPMT = "NIK_Anak = $row->NIK_Anak and Tanggal_Pemberian = '$row->Tanggal_Ukur' and View = '1'";
            $this->db->where($kunciPMT);
            $dtPMT = $this->db->get('id_pmt')->row();


            $item['Posyandu'] = $dtPos->Nama_Posyandu;
            $item['Kader'] = $dtKader->Nama_Kader;
            $item['NIK_Anak'] = $dtAnak->NIK_Anak;
            $item['Nama_Anak'] = $dtAnak->Nama_Anak;


            $item['Tanggal_Ukur'] = $row->Tanggal_Ukur;
            $item['Usia_Ukur'] = $row->Usia_Ukur;
            $item['Cara_Ukur'] = $row->Cara_Ukur;
            $item['Vitamin'] = $row->Vitamin;
            $item['Berat'] = $row->Berat;
            $item['Tinggi'] = $row->Tinggi;
            $item['Lila'] = $row->Lingkar_Lengan;
            $item['Lile'] = $row->Lingkar_Kepala;


                        //======================================= BACA ASI ==================
 
                        for($in = 0; $in < 6; $in++){
                            $jml = $in + 1;
                            $kunciAsi = "NIK_Anak = '$dtAnak->NIK_Anak' AND Jenis_Asi = '$jml' AND View = '1'";
                            $this->db->where($kunciAsi);
                            $this->db->order_by('No_ID','desc');
                            $this->db->limit(1);
                            $dtAsi2 = $this->db->get('id_asi_anak')->row();
                            if($dtAsi2){
                                if($dtAsi2->Status == "1"){
                                    $asine = "Ya";
                                }
                                else{
                                    $asine = "Tidak";
                                }    
                                $item['Bulan_' . $jml] = $asine; 

                            }
                            else{
                                $item['Bulan_' . $jml] = " "; 
                            }

                        }
                        //=====================================================================
            
                        if($dtPMT){
                            $item['Sumber_PMT'] = $dtPMT->Sumber_PMT;
                            $item['Pem_Pusat'] = $dtPMT->Pemberian_Pusat;
                            $item['Tahun_Produksi'] = $dtPMT->Tahun_Produksi;
                            $item['Pem_Daerah'] = $dtPMT->Pemberian_Daerah;    
                        }
                        else{
                            $item['Sumber_PMT'] = "";
                            $item['Pem_Pusat'] = "";
                            $item['Tahun_Produksi'] = "";
                            $item['Pem_Daerah'] = "";    

                        }
            
            $datane[] = $item;    
        }

        return $datane;
    }


}