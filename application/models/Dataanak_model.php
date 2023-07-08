<?php defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Jakarta');
class Dataanak_model extends CI_Model
{

    function getDaftarAnak($dt){
        $syarat = array('ID_Posyandu' => $dt, 'Status' => 'Aktif', 'View' => '1',);
        $this->db->where($syarat);
        return $this->db->get('id_anak')->result();
    }

    function getDaftarAnakPindahan(){
        $syarat = array('Status' => 'Pindah',);
        $this->db->where($syarat);
        return $this->db->get('id_anak')->result();
    }

    function getCariAnakPindahanByKIA($kiane){
        $syarat = array('NIK_Anak' => $kiane, 'Status' => 'Pindah',);
        $this->db->where($syarat);
        return $this->db->get('id_anak')->result();
    }


    function insertDataAnak($dtAnak, $dtOrtu, $noKKne, $noKiane){
        $syarate = array('NIK_Anak' => $noKiane, 'View' => '1',);
        $this->db->where($syarate);
        $cek = $this->db->get('id_anak')->row();
        if(!$cek){
            $syaratOrtu = array('No_KK' => $noKKne, 'View' => '1',);
            $this->db->where($syaratOrtu);
            $kono = $this->db->get('id_ortu')->row();
            if(!$kono){
                $kueri2 = $this->db->insert('id_ortu', $dtOrtu);
            }
    
            $kueri = $this->db->insert('id_anak', $dtAnak);
    
            if($kueri){
                return "sukses";
            }
            else{
                return "gagal";
            }
        } 
        else{
            return "data sudah ada";
        }

    }


    function deleteDataAnak($datane){
        $this->db->where($datane);
        $kueri = $this->db->delete('list_akun');
        if($kueri){
            $status = "sukses";
        }
        else{
            $status = "gagal";
        }
        return $status;
    }

    function editDataAnak($syrt,$datane){
        $this->db->set($datane);
        $this->db->where($syrt);
        $kueri = $this->db->update('id_anak');
        if($kueri){
            $status = "sukses";
        }
        else{
            $status = "gagal";
        }
        return $status;
    }

    function editDataOrtu($syrt,$datane){
        $this->db->set($datane);
        $this->db->where($syrt);
        $kueri = $this->db->update('id_ortu');
        if($kueri){
            $status = "sukses";
        }
        else{
            $status = "gagal";
        }
        return $status;
    }

    function deleteDataOrtu($syrt,$dtAnak, $dtOrtu){
        $this->db->set($dtAnak);
        $this->db->where($syrt);
        $kueri = $this->db->update('id_anak');

        $this->db->set($dtOrtu);
        $this->db->where($syrt);
        $kueri2 = $this->db->update('id_ortu');

        if($kueri && $kueri2){
            $status = "sukses";
        }
        else{
            $status = "gagal";
        }
        return $status;
    }

    function getSortirAnak($nile,$idne){
        $syarate = array('ID_Posyandu' => $idne, 'View' => '1',);
        $this->db->like('Nama_Anak', $nile);
        $this->db->where($syarate);
        $this->db->order_by('Nama_Anak','ASC');
        return $this->db->get('id_anak')->result();
    }

    function getNamaAnakByKIA($idne){
        $datane = array();
        $kunci = "NIK_Anak = '$idne' AND View = '1'";

        $this->db->where($kunci);
        $row = $this->db->get('id_anak')->row();

        if($row){
            $noKK = $row->No_KK;
            $kuncine = "No_KK = '$noKK' AND View = '1'";
            $this->db->where($kuncine);
            $dtOrtu = $this->db->get('id_ortu')->row();

            $noPosyandu = $row->ID_Posyandu;
            $kunciPos = "No_ID = '$noPosyandu'";
            $this->db->where($kunciPos);
            $dtPosyandu = $this->db->get('id_posyandu')->row();




            $item['No_KK'] = $noKK;
            $item['NIK_Anak'] = $row->NIK_Anak;
            $item['Nama_Anak'] = $row->Nama_Anak;
            $item['Anak_Ke'] = $row->Anak_Ke;

            $item['Tanggal_Lahir'] = $row->Tanggal_Lahir;
            $item['Jenis_Kelamin'] = $row->Jenis_Kelamin;
            $item['Berat_Lahir'] = $row->Berat_Lahir;
            $item['Tinggi_Lahir'] = $row->Tinggi_Lahir;
            $item['IMD'] = $row->IMD;


            $item['Nama_Ortu'] = $dtOrtu->Nama_Ayah;
            $item['NIK_Ortu'] = $dtOrtu->NIK_Ortu;
            $item['Buku_KIA'] = $dtOrtu->KIA;
            $item['No_Telpon'] = $dtOrtu->No_Telpon;

            $item['Alamat'] = $dtOrtu->Alamat;
            $item['RT_RW'] = $dtOrtu->RT . "/" . $dtOrtu->RW;
            $item['Provinsi'] = $dtOrtu->Nama_Provinsi;
            $item['KabKota'] = $dtOrtu->Nama_Kota;
            $item['Kecamatan'] = $dtOrtu->Nama_Kecamatan;
            $item['Kelurahan'] = $dtOrtu->Nama_Kelurahan;
            $item['Kodepos'] = $dtOrtu->ID_KodePos;

            $item['Posyandu'] = $dtPosyandu->Nama_Posyandu;
            $item['Puskesmas'] = $dtPosyandu->Puskesmas;

            $noNIKAnak = $row->NIK_Anak;


            $kunciVitBiru = "NIK_Anak = '$noNIKAnak' AND Jenis_Kapsul = '0' AND View = '1'";
            $this->db->where($kunciVitBiru);
            $this->db->order_by('No_ID','desc');
            $this->db->limit(1);
            $dtVitBiru = $this->db->get('id_vitamin_anak')->row();

            if($dtVitBiru){
                if($dtVitBiru->Status == "0"){
                    $item['Kapsul_Biru'] = "Belum";
                }
                else{
                    $item['Kapsul_Biru'] = "Ya";
                }
                $item['Umur_Biru'] = $dtVitBiru->Usia_Pemberian;    
                $item['Tanggal_Biru'] = $dtVitBiru->Tanggal_Pemberian;    
            }
            else{
                $item['Kapsul_Biru'] = "Belum";
                $item['Umur_Biru'] = "0";    
                $item['Tanggal_Beri'] = "0";    
            }

            $kunciVitMerah = "NIK_Anak = '$noNIKAnak' AND Jenis_Kapsul = '1' AND View = '1'";
            $this->db->where($kunciVitMerah);
            $this->db->order_by('No_ID','desc');
            $this->db->limit(1);

            $dtVitMerah = $this->db->get('id_vitamin_anak')->row();

            if($dtVitMerah){
                if($dtVitMerah->Status == "0"){
                    $item['Kapsul_Merah'] = "Belum";
                }
                else{
                    $item['Kapsul_Merah'] = "Ya";
                }
                $item['Umur_Merah'] = $dtVitMerah->Usia_Pemberian;    
                $item['Tanggal_Merah'] = $dtVitMerah->Tanggal_Pemberian;    
            }
            else{
                $item['Kapsul_Merah'] = "Belum";
                $item['Umur_Merah'] = "0";    
                $item['Tanggal_Merah'] = "0";    
            }



                        //======================================= BACA ASI ==================
 
                        for($in = 0; $in < 6; $in++){
                            $jml = $in + 1;
                            $kunciAsi = "NIK_Anak = '$noNIKAnak' AND Jenis_Asi = '$jml' AND View = '1'";
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
            



            $datane[] = $item;    
        }

        return $datane;
    }

    function insertDataVitamin($datane){
        $kueri = $this->db->insert('id_vitamin_anak', $datane);
        if($kueri){
            $status = "sukses";
        }
        else{
            $status = "gagal";
        }
        return $status;
    }

    function insertDataASI($datane){
        $kueri = $this->db->insert('id_asi_anak', $datane);
        if($kueri){
            $status = "sukses";
        }
        else{
            $status = "gagal";
        }
        return $status;
    }


    function getOrtuByKK($dt){
        $syarat = array('No_KK' => $dt, 'View' => '1',);
        $this->db->where($syarat);
        return $this->db->get('id_ortu')->result();
    }

    function getCaraPenggunaan(){
        return $this->db->get('cara_penggunaan')->result();
    }

    function getAsiAnakByKIA($dt){
        $datane = array();
                        //======================================= BACA ASI ==================
 
                        for($in = 0; $in < 6; $in++){
                            $jml = $in + 1;
                            $kunciAsi = "NIK_Anak = '$dt' AND Jenis_Asi = '$jml' AND View = '1'";
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
            $datane[] = $item;    


        return $datane;
    }

    function getVitaminAnakByKIA($dt){
        $datane = array();
            $kunciVitBiru = "NIK_Anak = $dt AND Jenis_Kapsul = '0' AND View = '1'";
            $this->db->where($kunciVitBiru);
            $this->db->order_by('No_ID','desc');
            $this->db->limit(1);
            $dtVitBiru = $this->db->get('id_vitamin_anak')->row();

            if($dtVitBiru){
                if($dtVitBiru->Status == "0"){
                    $item['Kapsul_Biru'] = "Belum";
                }
                else{
                    $item['Kapsul_Biru'] = "Ya";
                }
                $item['Umur_Biru'] = $dtVitBiru->Usia_Pemberian;    
                $item['Tanggal_Biru'] = $dtVitBiru->Tanggal_Pemberian;    
            }
            else{
                $item['Kapsul_Biru'] = "Belum";
                $item['Umur_Biru'] = "0";    
                $item['Tanggal_Beri'] = "0";    
            }

            $kunciVitMerah = "NIK_Anak = $dt AND Jenis_Kapsul = '1' AND View = '1'";
            $this->db->where($kunciVitMerah);
            $this->db->order_by('No_ID','desc');
            $this->db->limit(1);

            $dtVitMerah = $this->db->get('id_vitamin_anak')->row();

            if($dtVitMerah){
                if($dtVitMerah->Status == "0"){
                    $item['Kapsul_Merah'] = "Belum";
                }
                else{
                    $item['Kapsul_Merah'] = "Ya";
                }
                $item['Umur_Merah'] = $dtVitMerah->Usia_Pemberian;    
                $item['Tanggal_Merah'] = $dtVitMerah->Tanggal_Pemberian;    
            }
            else{
                $item['Kapsul_Merah'] = "Belum";
                $item['Umur_Merah'] = "0";    
                $item['Tanggal_Merah'] = "0";    
            }

        $datane[] = $item;   
        
        return $datane;
    }

    function updateAnakPindahanDari($kiane,$posyandu){
        $syrt = array('NIK_Anak' => $kiane,);
        $datane = array('Status' => 'Aktif', 'ID_Posyandu' => $posyandu,);
        $this->db->set($datane);
        $this->db->where($syrt);
        $kueri = $this->db->update('id_anak');
        if($kueri){
            $status = "sukses";
        }
        else{
            $status = "gagal";
        }
        return $status;
    }

    function updateAnakPindahanKe($kiane){
        $syrt = array('NIK_Anak' => $kiane,);
        $datane = array('Status' => 'Pindah',);
        $this->db->set($datane);
        $this->db->where($syrt);
        $kueri = $this->db->update('id_anak');
        if($kueri){
            $status = "sukses";
        }
        else{
            $status = "gagal";
        }
        return $status;
    }

    function getLapIdentitasAnak($idne){
        $datane = array();
        $kunci = "ID_Posyandu = '$idne' AND Status='Aktif'";

        $this->db->where($kunci);
        $kueri = $this->db->get('id_anak');
        foreach ($kueri->result() as $row){
            $noKK = $row->No_KK;
            $kuncine = "No_KK = '$noKK'";
            $this->db->where($kuncine);
            $dtOrtu = $this->db->get('id_ortu')->row();

            $noPosyandu = $row->ID_Posyandu;
            $kunciPos = "No_ID = '$noPosyandu'";
            $this->db->where($kunciPos);
            $dtPosyandu = $this->db->get('id_posyandu')->row();


            $item['Anak_Ke'] = $row->Anak_Ke;
            $item['Tanggal_Lahir'] = $row->Tanggal_Lahir;
            $item['Jenis_Kelamin'] = $row->Jenis_Kelamin;
            $item['No_KK'] = $noKK;
            $item['NIK_Anak'] = $row->NIK_Anak;
            $item['Nama_Anak'] = $row->Nama_Anak;
            $item['Berat_Lahir'] = $row->Berat_Lahir;
            $item['Tinggi_Lahir'] = $row->Tinggi_Lahir;
            $item['Buku_KIA'] = $dtOrtu->KIA;
            $item['IMD'] = $row->IMD;


            $item['Nama_Ortu'] = $dtOrtu->Nama_Ayah;
            $item['NIK_Ortu'] = $dtOrtu->NIK_Ortu;
            $item['No_Telpon'] = $dtOrtu->No_Telpon;

            $item['Alamat'] = $dtOrtu->Alamat;
            $item['RT'] = $dtOrtu->RT;
            $item['RW'] = $dtOrtu->RW;
            $item['Provinsi'] = $dtOrtu->Nama_Provinsi;
            $item['KabKota'] = $dtOrtu->Nama_Kota;
            $item['Kecamatan'] = $dtOrtu->Nama_Kecamatan;
            $item['Puskesmas'] = $dtPosyandu->Puskesmas;
            $item['Kelurahan'] = $dtOrtu->Nama_Kelurahan;
            $item['Posyandu'] = $dtPosyandu->Nama_Posyandu;
            $item['Kodepos'] = $dtOrtu->ID_KodePos;


            
            $datane[] = $item;    
        }

        return $datane;
    }



}