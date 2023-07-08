<?php defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Jakarta');
class Dataproduksi_model extends CI_Model
{

    function requestKodeLine(){
        $bulane = date('m');
        $tahune = date('Y');
        $query = $this->db->query("select max(Kode_Line) as maxKode from andon_line");
        $tmp = $query->row();
        $kodeBarang = $tmp->maxKode;
        $noUrut = (int) substr($kodeBarang, 8, 3);	
        $noUrut++;
        $char = "SAI/AND/";
        $noNota = $char .  $noUrut;	
        return $noNota;
    }

    function requestKodeProses($ln){
        $query = $this->db->query("select max(Proses) as maxKode from andon_proses where Line = '$ln'");
        $tmp = $query->row();
        $kodeBarang = $tmp->maxKode;
        $noUrut = (int)$kodeBarang;	
        $noUrut++;
        $noNota = $noUrut;	
        return $noNota;
    }

    function insertDataSetting($datane, $syarate){
        $this->db->where($syarate);
        $kueri1 = $this->db->get('andon_setting')->row();
        if($kueri1){
            return "data sudah ada";
        }
        else{
            $kueri2 = $this->db->insert('andon_setting', $datane);
            if($kueri2){
                return "sukses";
            }
            else{
                return "gagal";
            }    
        }
    }

    function insertProsesSet($datane, $syarate){
        $this->db->where($syarate);
        $kueri1 = $this->db->get('andon_proses')->row();
        if($kueri1){
            return "data sudah ada";
        }
        else{
            $kueri2 = $this->db->insert('andon_proses', $datane);
            if($kueri2){
                return "sukses";
            }
            else{
                return "gagal";
            }    
        }
    }

    function insertSubProsesSet($ln, $pr){
        $syarate = "Line = '$ln' AND Proses = '$pr'";
        $this->db->where($syarate);
        $kueri1 = $this->db->get('andon_proses')->row();
        if($kueri1){

            $knc = "Line = '$ln' AND Proses = '$pr'";
            $this->db->where($knc);
            $jml = $this->db->get('andon_proses')->num_rows();
            $newJml = $jml + 1;
            $ctCap = $kueri1->CT_Capacity;
            $name = $kueri1->Proses_Name;
            $newCtCap = $ctCap / $newJml;
            $variUpdate = array(
                'CT_Capacity' => $newCtCap,
            ); 


            $vari = array(
                'Line' => $ln,
                'Proses' => $pr,
                'Proses_Name' => $name,
                'CT_Capacity' => $newCtCap,
                'Sub_Proses' => $newJml,
            ); 

            $this->db->where($knc);
            $this->db->set($variUpdate);
            $kueri2 = $this->db->update('andon_proses');


            $kueri3 = $this->db->insert('andon_proses', $vari);
            if($kueri3){
                return "sukses";
            }
            else{
                return "gagal";
            }    


        }
        else{
            return "data belum ada";
        }
    }


    function deleteSubProsesSet($ln, $pr, $sb){
        $syarate = "Line = '$ln' AND Proses = '$pr' AND Sub_Proses = '$sb'";
        $this->db->where($syarate);
        $kueri1 = $this->db->get('andon_proses')->row();
        if($kueri1){

            $knc = "Line = '$ln' AND Proses = '$pr'";
            $this->db->where($knc);
            $jml = $this->db->get('andon_proses')->num_rows();

            if($sb > 1){
                $ctCap = $kueri1->CT_Capacity;
                $name = $kueri1->Proses_Name;
                $newCtCap = $ctCap * $jml;
                $variUpdate = array(
                    'CT_Capacity' => $newCtCap,
                );     
                $knc2 = "Line = '$ln' AND Proses = '$pr'";
                $this->db->where($knc2);
                $this->db->set($variUpdate);
                $kueri2 = $this->db->update('andon_proses');    

                $newSrt = "Line = '$ln' AND Proses = '$pr' AND Sub_Proses = '$sb'";
                $this->db->where($newSrt);
                $kueri3 = $this->db->delete('andon_proses');           
                if($kueri3){
                    return "sukses";
                }
                else{
                    return "gagal";
                }    
    
            }
            else if($sb == 1 && $jml > 1){
                return "harap hapus sub proses diatas 1";
            }

            else{
                $newSrt = "Line = '$ln' AND Proses = '$pr' AND Sub_Proses = '$sb'";
                $this->db->where($newSrt);
                $kueri3 = $this->db->delete('andon_proses');           
                if($kueri3){
                    return "sukses";
                }
                else{
                    return "gagal";
                }    
    
            }

        }
        else{
            return "data belum ada";
        }
    }


    

    function insertDataLine($datane, $syarate){
        $this->db->where($syarate);
        $kueri1 = $this->db->get('andon_line')->row();
        if($kueri1){
            return "data sudah ada";
        }
        else{
            $kueri2 = $this->db->insert('andon_line', $datane);
            if($kueri2){
                return "sukses";
            }
            else{
                return "gagal";
            }    
        }
    }

    function insertDataOperator($datane, $syarate){
        $this->db->where($syarate);
        $kueri1 = $this->db->get('andon_daftar_operator')->row();
        if($kueri1){
            return "data sudah ada";
        }
        else{
            $kueri2 = $this->db->insert('andon_daftar_operator', $datane);
            if($kueri2){
                return "sukses";
            }
            else{
                return "gagal";
            }    
        }
    }


    function updateBN($data1, $syrt){
        $this->db->set($data1);
        $this->db->where($syrt);
        $kueri = $this->db->update('andon_proses');
        if($kueri){
            $status = "sukses";
        }
        else{
            $status = "gagal";
        }
        return $status;
    }

    function updateLN($data1, $syrt){
        $this->db->set($data1);
        $this->db->where($syrt);
        $kueri = $this->db->update('andon_line');
        if($kueri){
            $status = "sukses";
        }
        else{
            $status = "gagal";
        }
        return $status;
    }

    function getAllLine(){
        $datane = array();
        $harine = date('w');
        //$kunci = "No_ID = 1";
        $kunci = "No_ID = '$harine'";

        $this->db->where($kunci);
        $checkTime = $this->db->get('andon_daytime')->row();
        
        $finishTime = $checkTime->Finish_Time;
        sscanf($finishTime, "%d:%d:%d", $hours, $minutes, $seconds);
        $tmFinish = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;    

        if($checkTime->Lama_Break1 == ""){
            $lmBrk1 = 0;
        }
        else{
            $lmBrk1 = (int)$checkTime->Lama_Break1 * 60;
        }

        if($checkTime->Lama_Break2 == ""){
            $lmBrk2 = 0;
        }
        else{
            $lmBrk2 = (int)$checkTime->Lama_Break2 * 60;
        }

        $lmBrk = $lmBrk1 + $lmBrk2;



        $this->db->order_by("No_ID", "desc");
        $kueri = $this->db->get('andon_line');
        foreach ($kueri->result() as $row)
        {
            $kodeLine = $row->Kode_Line;
            $ctStandart = $row->CT_Standart;

            
            $leader = $row->Leader;

            $ctMax = 0;
            $this->db->where("Line", $kodeLine);
            $this->db->order_by("Proses", "asc");
            $kueri1 = $this->db->get('andon_proses');
            foreach ($kueri1->result() as $row1)
            {
                $ctCapacity1 = $row1->CT_Capacity;
                if($ctCapacity1 > $ctMax){
                    $ctMax = $ctCapacity1;
                } 
                else{
                    $ctMax = $ctMax;
                }
            }
    

            $crProses = "Line = '$kodeLine'";
            $this->db->where($crProses);
            $prosesTotal = $this->db->get('andon_proses')->num_rows();    


            $item['Line'] = $kodeLine;
            $item['Line_Name'] = $row->Line_Name;
            if($row->Start_Time == ""){
                $strTime = "08:00:00";
            }
            else{
                $strTime = $row->Start_Time;
            }
            $item['Start_Time'] = $strTime;
            sscanf($strTime, "%d:%d:%d", $hours, $minutes, $seconds);
            $tmStart = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;    


            $item['Proses'] = $prosesTotal;

            $kncLd = "NIK = '$leader'";
            $this->db->where($kncLd);
            $nmLd = $this->db->get('andon_daftar_operator')->row();  
            if($nmLd){
                $item['Leader'] = $nmLd->Nama_Lengkap;
            }  
            else{
                $item['Leader'] = "";
            }


            $WorkHour = $tmFinish - ($tmStart + $lmBrk);     
            $item['Eff_Hour'] = $row->Efektif_Kerja;

            $effWH = ($row->Efektif_Kerja / 100) * $WorkHour;
            $qtyCap = ((int)$effWH) / ((int)$ctStandart);
            $item['Qty_Capacity'] = floor($qtyCap);
            $ctCap = gmdate("H:i:s", $ctStandart);
            $item['CT_Capacity'] = $ctCap;
            if($ctMax == "0"){
                $qtyTarget = 0;
                $ctMax = 0;
            }
            else{
                $qtyTarget = ((int)$effWH) / ((int)$ctMax);
            }
            $item['Qty_Target'] = floor($qtyTarget);
            $ctTarget = gmdate("H:i:s", $ctMax);
            $item['CT_Target'] = $ctTarget;
            if($prosesTotal == "0" || $prosesTotal == null){
                $item['Productive'] = 0;
            }
            else{
                $item['Productive'] = floor(floor($qtyTarget) / $prosesTotal);
            }

            $datane[] = $item;    
        }

        return $datane;
    }

    function getLineProduction(){
        $datane = array();
        $kunci = "Status = '1'";

        $this->db->where($kunci);
        $this->db->order_by("No_ID", "desc");
        $kueri = $this->db->get('andon_setting');

        foreach ($kueri->result() as $row)
        {
            $kodeLine = $row->Line;
            $ctTrgt = $row->CT_Target;
            $konone = array();
            $this->db->where('Line', $kodeLine);
            $this->db->order_by("Proses", "asc");
            $bufAndon = $this->db->get('andon_setting');
            foreach ($bufAndon->result() as $row2)
            {
                $item2['Proses'] = $row2->Proses;
                $item2['Proses_Name'] = $row2->Proses_Name;
                if($row2->CT_Target == ""){
                    $item2['Qty_Target'] = 0;    
                }
                else{
                    $ctTarget = $ctTrgt;
                    $qtyTargetPros = ((int)$row2->Work_Hour * 60) / ((int)$ctTarget);
                    $item2['Qty_Target'] = $qtyTargetPros;    
                }
                $konone[] = $item2;
            }
            $item['Line'] = $row->Line;
            $item['Line_Name'] = $row->Line_Name;
            $item['Start_Time'] = $row->Start_Time;
            $qtyTarget = ((int)$row->Work_Hour * 60) / ((int)$ctTrgt);
            $qtyCap = ((int)$row->Work_Hour * 60) / ((int)$row->CT_Capacity);
            $item['Qty_Target'] = $qtyTarget;
            $item['Qty_Capacity'] = $qtyCap;
            $ctTarget = gmdate("H:i:s", $ctTrgt);
            $ctCap = gmdate("H:i:s", $row->CT_Capacity);

            $item['CT_Target'] = $ctTarget;
            $item['CT_Capacity'] = $ctCap;
            $nt = array('result' => $konone);

            $item['Prosese'] = $nt;

            $datane[] = $item;    
        }

        return $datane;
    }

    function getProsesProduction($idne){
        $datane = array();
        $harine = date('w');
        $kunci = "No_ID = '$harine'";
        //$kunci = "No_ID = 1";

        $this->db->where($kunci);
        $checkTime = $this->db->get('andon_daytime')->row();
        
        $finishTime = $checkTime->Finish_Time;
        sscanf($finishTime, "%d:%d:%d", $hours, $minutes, $seconds);
        $tmFinish = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;    

        if($checkTime->Lama_Break1 == ""){
            $lmBrk1 = 0;
        }
        else{
            $lmBrk1 = (int)$checkTime->Lama_Break1 * 60;
        }

        if($checkTime->Lama_Break2 == ""){
            $lmBrk2 = 0;
        }
        else{
            $lmBrk2 = (int)$checkTime->Lama_Break2 * 60;
        }

        $lmBrk = $lmBrk1 + $lmBrk2;

        $kunci = "Line = '$idne'";
        $this->db->order_by("No_ID", "desc");
        $kueriLine = $this->db->get('andon_line')->row();
        if($kueriLine->Start_Time == ""){
            $strTime = "08:00:00";
        }
        else{
            $strTime = $row->Start_Time;
        }
        $item['Start_Time'] = $strTime;
        sscanf($strTime, "%d:%d:%d", $hours, $minutes, $seconds);
        $tmStart = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;    


        $WorkHour = $tmFinish - ($tmStart + $lmBrk);     
        $effWH = ($kueriLine->Efektif_Kerja / 100) * $WorkHour;



        $maxCT = 0;
        $this->db->where("Line", $idne);
        $this->db->order_by("Proses", "asc");
        $kueri1 = $this->db->get('andon_proses');
        foreach ($kueri1->result() as $row1)
        {
            $ctCapacity1 = $row1->CT_Capacity;
            if($ctCapacity1 > $maxCT){
                $maxCT = $ctCapacity1;
            } 
            else{
                $maxCT = $maxCT;
            }
        }


        $this->db->where("Line", $idne);
        $this->db->order_by("Proses", "asc");
        $kueri = $this->db->get('andon_proses');

        foreach ($kueri->result() as $row)
        {
            $ctCapacity = $row->CT_Capacity;
            $kode = $row->Line;
            $pros = $row->Proses;
            $subPros = $row->Sub_Proses;

            $item['Line'] = $row->Line;
            $item['Proses'] = $row->Proses;
            $item['Proses_Name'] = $row->Proses_Name;
            $item['Sub_Proses'] = $row->Sub_Proses;

            $kncCheckOP = "Line = '$kode' AND Proses = '$pros'";
            $this->db->where($kncCheckOP);
            $checkOP = $this->db->get('andon_proses')->num_rows();

            if($checkOP > 1){
                $item['Jenenge'] = "OP " . $pros . "." . $subPros;
            }
            else{
                $item['Jenenge'] = "OP " . $pros;
            }

            $kdOpr = $row->Operator;
            $kunciOp = "NIK = '$kdOpr'";
            $this->db->where($kunciOp);
            $kueriOpr = $this->db->get('andon_daftar_operator')->row();
            if($kueriOpr){
                $item['Operator'] = $kueriOpr->Nama_Lengkap;
            }
            else{
                $item['Operator'] = "";
            }

            $qtyCap = ((int)$effWH) / ((int)$ctCapacity);
            $item['Qty_Capacity'] = floor($qtyCap);
            $ctCap = gmdate("H:i:s", $ctCapacity);
            $item['CT_Capacity'] = $ctCap;

            $qtyTarget = ((int)$effWH) / ((int)$maxCT);
            $item['Qty_Target'] = floor($qtyTarget);
            $ctTarget = gmdate("H:i:s", $maxCT);
            $item['CT_Target'] = $ctTarget;

            $datane[] = $item;    
        }

        return $datane;
    }

    function getDetailProsesProduction($idne, $prs, $sb){
        $datane = array();
        $kunci = "Line = '$idne' AND Proses = '$prs' AND Sub_Proses = '$sb'";
        $this->db->where($kunci);
        $this->db->order_by("Proses", "asc");
        $kueri = $this->db->get('andon_proses');

        foreach ($kueri->result() as $row)
        {

            $item['Line'] = $row->Line;
            $item['Proses'] = $row->Proses;
            $item['Proses_Name'] = $row->Proses_Name;
            $item['Sub_Proses'] = $row->Sub_Proses;
            $item['CT_Capacity'] = $row->CT_Capacity;

            $datane[] = $item;    
        }

        return $datane;
    }

    function getDetailLine($idne){
        $datane = array();
        $kunci = "Kode_Line = '$idne'";
        $this->db->where($kunci);
        $this->db->order_by("Kode_Line", "asc");
        $kueri = $this->db->get('andon_line');

        foreach ($kueri->result() as $row)
        {

            $item['Kode_Line'] = $row->Kode_Line;
            $item['Line_Name'] = $row->Line_Name;
            $item['CT_Capacity'] = $row->CT_Standart;
            $item['Efektif_Kerja'] = $row->Efektif_Kerja;

            $datane[] = $item;    
        }

        return $datane;
    }

    function loadInformation($kode){
        $datane = array();
        $harine = date('w');
        $tgle = date('Y-m-d');
        $nowTime = date('H:i');
        $tglDis = date('d M Y');
        //$kunci = "No_ID = 1";
        $kunci = "No_ID = '$harine'";

        //============ using time =========//
        $taim = date('H:i:s');
        sscanf($taim, "%d:%d:%d", $hours, $minutes, $seconds);
        $tmNow = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;    
        

        $this->db->where($kunci);
        $checkTime = $this->db->get('andon_daytime')->row();
        $finishTime = $checkTime->Finish_Time;
        $brk1Time = $checkTime->Break1_Time;
        $brk2Time = $checkTime->Break2_Time;
        sscanf($finishTime, "%d:%d:%d", $hours, $minutes, $seconds);
        $tmFinish = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;    

        if($checkTime->Lama_Break1 == ""){
            $lmBrk1 = 0;
        }
        else{
            $lmBrk1 = (int)$checkTime->Lama_Break1 * 60;
        }

        if($checkTime->Lama_Break2 == ""){
            $lmBrk2 = 0;
        }
        else{
            $lmBrk2 = (int)$checkTime->Lama_Break2 * 60;
        }

        $lmBrk = $lmBrk1 + $lmBrk2;
        

        sscanf($brk1Time.":00", "%d:%d:%d", $hours, $minutes, $seconds);
        $tmBrk1Start = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;    
        $tmBrk1Finish = $tmBrk1Start + $lmBrk1;

        sscanf($brk2Time.":00", "%d:%d:%d", $hours, $minutes, $seconds);
        $tmBrk2Start = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;    
        $tmBrk2Finish = $tmBrk2Start + $lmBrk2;
        
        if($tmNow < $tmBrk1Start){
            $pengurangan = 0;
        }
        else if(($tmNow >= $tmBrk1Start) && ($tmNow <= $tmBrk1Finish)){
            $pengurangan = $tmNow - $tmBrk1Start;
        }            
        else if(($tmNow > $tmBrk1Finish) && ($tmNow < $tmBrk2Start)){
            $pengurangan = $lmBrk1;
        }
        else if(($tmNow >= $tmBrk2Start) && ($tmNow <= $tmBrk2Finish)){
            $pengurangan = ($tmNow - $tmBrk2Start) + $lmBrk1;
        }
        else{
            $pengurangan = $lmBrk1 + $lmBrk2;            
        }            



        //====================== informasi line ==============//
        $this->db->where('Kode_Line',$kode);
        $row = $this->db->get('andon_line')->row();
        $kodeLine = $row->Kode_Line;
        $leader = $row->Leader;

        $ctMax = 0;
        $prosesMax = 0;
        $this->db->where("Line", $kodeLine);
        $this->db->order_by("Proses", "asc");
        $kueri1 = $this->db->get('andon_proses');
        foreach ($kueri1->result() as $row1){
            $ctCapacity1 = $row1->CT_Capacity;
            if($ctCapacity1 > $ctMax){
                $ctMax = $ctCapacity1;
                $prosesMax = $row1->Proses;
            } 
            else{
                $ctMax = $ctMax;
                $prosesMax = $prosesMax;
            }
        }


        $crProses = "Line = '$kodeLine'";
        $this->db->where($crProses);
        $prosesTotal = $this->db->get('andon_proses')->num_rows();    


        $item['Line'] = $kodeLine;
        $item['Line_Name'] = $row->Line_Name;
        if($row->Start_Time == ""){
            $strTime = "08:00:00";
        }
        else{
            $strTime = $row->Start_Time;
        }


        sscanf($strTime, "%d:%d:%d", $hours, $minutes, $seconds);
        $tmStart = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;    
        $usingTime = $tmNow - ($tmStart + $pengurangan);


        $useTime = gmdate("H:i", $usingTime);

        $item['Start_Time'] = gmdate("H:i", $tmStart);

        $item['Using_Time'] = $useTime;
        $item['Date'] = $tglDis;
        $item['Now_Time'] = $nowTime;

        $item['Proses'] = $prosesTotal;

        $kncLD = "NIK = '$leader'";
        $this->db->where($kncLD);
        $nmLeader = $this->db->get('andon_daftar_operator')->row();    
        if($nmLeader){
            $item['Leader'] = $nmLeader->Nama_Lengkap;
        }
        else{
            $item['Leader'] = "";
        }


        $WorkHour = $tmFinish - ($tmStart + $lmBrk);     
        $effWH = ($row->Efektif_Kerja / 100) * $WorkHour;
        if($ctMax == "0"){
            $qtyTarget = 0;
            $ctMax = 0;
        }
        else{
            $qtyTarget = ((int)$effWH) / ((int)$ctMax);
        }
        $item['Qty_Target'] = floor($qtyTarget);
        $ctTarget = gmdate("H:i:s", $ctMax);
        $item['CT_Target'] = $ctTarget;
        if($prosesTotal == "0" || $prosesTotal == null){
            $item['Productive'] = 0;
        }
        else{
            $item['Productive'] = floor(floor($qtyTarget) / $prosesTotal);
        }



        //======== cari actual qty ===========//
        //======== cari proses terakhir ===========//
        $keyLastPros = "Line = '$kode'";
        $this->db->where($keyLastPros);
        $this->db->order_by("Proses", "desc");
        $this->db->limit(1);
        $qryLastPros = $this->db->get('andon_proses')->row();
        $lastPros = (int)$qryLastPros->Proses;


        $keyAct = "Tanggal = '$tgle' AND Line = '$kode' AND Proses = '$lastPros'";
        $this->db->where($keyAct);
        $qtyActual = $this->db->get('andon_production')->num_rows();
        $item['Qty_Actual'] = $qtyActual;

        //======== cari actual CT ===========//
        $lastCTPros = $lastPros-1;
        $keyAct1 = "Tanggal = '$tgle' AND Line = '$kode' AND Proses = '$lastCTPros'";
        $this->db->where($keyAct1);
        $this->db->order_by("No_ID", "asc");
        $this->db->limit(1);
        $qryCTActual = $this->db->get('andon_production')->row();
        if($qryCTActual){
            $startLastPros = $qryCTActual->Push_Time;
            sscanf($startLastPros, "%d:%d:%d", $hours, $minutes, $seconds);
            $tmStartLastPros = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;    
        }
        else{
            $tmStartLastPros = $tmStart;
        }
        $item['kk'] = gmdate("H:i:s", $tmStartLastPros);

        if($qtyActual == 0){
            if($tmStartLastPros >= $tmBrk2Finish){
                $vrCtAct = ($tmNow - $tmStartLastPros) / 1;
            }
            else{
                $vrCtAct = ($tmNow - ($tmStartLastPros + $pengurangan)) / 1;
            }
        }
        else{
            if($tmStartLastPros >= $tmBrk2Finish){
                $vrCtAct = ($tmNow - $tmStartLastPros) / $qtyActual;
            }
            else{
                $vrCtAct = ($tmNow - ($tmStartLastPros + $pengurangan)) / $qtyActual;
            }
        }

        if($vrCtAct < 0){$vrCtAct = 0;}

        $ctAct = gmdate("H:i:s", $vrCtAct);
        $item['CT_Actual'] = $ctAct;

        //======= cari balance =====//
        $item['Balance'] = floor($qtyActual - floor($qtyTarget));
        
        //======== cari actual Finish Good ===========//
        $keyFG = "Tanggal = '$tgle' AND Line = '$kode' AND Proses = '$lastPros' AND Status = '0'";
        $this->db->where($keyFG);
        $qtyFG = $this->db->get('andon_production')->num_rows();
        $item['Qty_Finish'] = $qtyFG;

        //======== cari actual Reject ===========//
        $keyNG = "Tanggal = '$tgle' AND  Line = '$kode' AND Status = '1'";
        $this->db->where($keyNG);
        $qtyNG = $this->db->get('andon_production')->num_rows();
        $item['Qty_NG'] = $qtyNG;


        $this->db->where("Line", $kode);
        $this->db->order_by("Proses", "asc");
        $kuery = $this->db->get('andon_proses');

        foreach ($kuery->result() as $row2)
        {
            $ctCapacity = $row2->CT_Capacity;
            $pros = $row2->Proses;
            $subPros = $row2->Sub_Proses;
            $item2['Proses'] = $row2->Proses;
            $item2['Proses_Name'] = $row2->Proses_Name;
            $item2['Sub_Proses'] = $row2->Sub_Proses;

            $kncCheckOP = "Line = '$kode' AND Proses = '$pros'";
            $this->db->where($kncCheckOP);
            $checkOP = $this->db->get('andon_proses')->num_rows();

            if($checkOP > 1){
                $item2['Jenenge'] = "OP " . $pros . "." . $subPros;
            }
            else{
                $item2['Jenenge'] = "OP " . $pros;
            }
    
            $oprtr = $row2->Operator;
            if($oprtr == ""){
                $item2['Operator'] = "";
            }
            else{
                $kncOpr = "NIK = '$oprtr'";
                $this->db->where('NIK' , $oprtr);
                $qryOpr = $this->db->get('andon_daftar_operator')->row();    
                $item2['Operator'] = $qryOpr->Nick_Name;
            }

            $qtyCap = ((int)$effWH) / ((int)$ctCapacity);
            $item2['Qty_Capacity'] = floor($qtyCap);
            $ctCap = gmdate("H:i:s", $ctCapacity);
            $item2['CT_Capacity'] = $ctCap;

            $qtyTrgtSet = ((int)$effWH) / ((int)$ctMax);
            $qtyTrgtSet = floor($qtyTrgtSet);




            $prosSeb = $pros - 1;
            $keyQty = "Tanggal = '$tgle' AND Line = '$kode' AND Proses = '$prosSeb' AND Status = '0'";
            $this->db->where($keyQty);
            $qtyLastPros = $this->db->get('andon_production')->num_rows();


            $this->db->where($keyQty);
            $this->db->order_by("No_ID", "asc");
            $this->db->limit(1);
            $qryCTActualPros = $this->db->get('andon_production')->row();
            if($qryCTActualPros){
                $startCTPros = $qryCTActualPros->Push_Time;
                sscanf($startCTPros, "%d:%d:%d", $hours, $minutes, $seconds);
                $tmStartPros = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;        
            }
            else{
                $tmStartPros = $tmNow;
            }

            if($pros == 1){
                $qtyTargetPros = ($tmNow - ($tmStart + $pengurangan)) / ((int)$ctCapacity);
            }
            else{
                if($tmStartPros >= $tmBrk2Finish){
                    $qtyTargetPros = ($tmNow - $tmStartPros) / ((int)$ctCapacity);
                }
                else{
                    $qtyTargetPros = ($tmNow - ($tmStartPros + $pengurangan)) / ((int)$ctCapacity);
                }
            }
            $ctTarAct = (int)$ctCapacity;
            
            if($qtyTargetPros < 0){ $qtyTargetPros = 0; }
            if($qtyTargetPros >= $qtyTrgtSet ) {$qtyTargetPros = $qtyTrgtSet;}                
            $item2['Qty_Target'] = floor($qtyTargetPros);    
            $ctTarget = gmdate("H:i:s", $ctTarAct);
            $item2['CT_Target'] = $ctTarget;    

            //======== aktual dikerjakan =============//
            $keyQty2 = "Tanggal = '$tgle' AND Line = '$kode' AND Proses = '$pros' AND Sub_Proses = '$subPros'";
            $this->db->where($keyQty2);
            $qtyActPr = $this->db->get('andon_production')->num_rows();
            $item2['Qty_Actual'] = $qtyActPr;
            if($qtyActPr == 0){
                $pemBagi = 1;
            }
            else{
                $pemBagi = $qtyActPr;
            }

            if($pros == 1){
                if($tmStartPros >= $tmBrk2Finish){
                    $vrCTPros = ($tmNow - $tmStart) / $pemBagi;
                }
                else{
                    $vrCTPros = ($tmNow - ($tmStart + $pengurangan)) / $pemBagi;
                }
            }
            else{
                if($tmStartPros >= $tmBrk2Finish){
                    $vrCTPros = ($tmNow - $tmStartPros) / $pemBagi;
                }
                else{
                    $vrCTPros = ($tmNow - ($tmStartPros + $pengurangan)) / $pemBagi;
                }
            }

            if($vrCTPros < 0){ $vrCTPros = 0;}
            $ctActualPros = gmdate("H:i:s", $vrCTPros);
            $item2['CT_Actual'] = $ctActualPros;    

            //======== aktual reject =============//
            $keyNG = "Tanggal = '$tgle' AND Line = '$kode' AND Proses = '$pros' AND Sub_Proses = '$subPros' AND Status = '1'";
            $this->db->where($keyNG);
            $qtyActNG = $this->db->get('andon_production')->num_rows();
            $item2['Qty_Reject'] = $qtyActNG;

            //================ tampilkan total downtime proses 2.1 ============
            $srat1DT_P2 = "Tanggal = '$tgle' AND Line = '$kode' AND Proses = '$pros' AND Kode_DT = '1' AND Start_Time != '' AND Finish_Time != ''"; 
            $this->db->where($srat1DT_P2);
            $jmlDT1 = $this->db->get('andon_downtime');
            $ctSum1 = 0; $jml1= 0; $total1 = 0;
            foreach ($jmlDT1->result() as $row4) {
                $ctPerPcs = $row4->Start_Time;    
                $ct2PerPcs = $row4->Finish_Time; 
                //====== data sekarang =======
                $ctn3 = date('H:i:s' , strtotime($ctPerPcs)); 
                sscanf($ctn3, "%d:%d:%d", $hours, $minutes, $seconds);
                $time_second3 = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;    
                
                //====== data terakhir =======
                $ctn4 = date('H:i:s' , strtotime($ct2PerPcs)); 
                sscanf($ctn4, "%d:%d:%d", $hours1, $minutes1, $seconds1);
                $time_second4 = isset($seconds1) ? $hours1 * 3600 + $minutes1 * 60 + $seconds1 : $hours1 * 60 + $minutes1;    
                
                $ctSum1 = $time_second4 - $time_second3;
                $total1 = $total1 + $ctSum1;   
                $jml1 = $jml1 + 1;
            }

            $syratDT1 = "Tanggal = '$tgle' AND Line = '$kode' AND Proses = '$pros' AND Kode_DT='1' AND Start_Time != '' AND Finish_Time = ''"; 
            $this->db->where($syratDT1);
            $vrDT = $this->db->get('andon_downtime')->row();
            if($vrDT){
                sscanf($vrDT->Start_Time, "%d:%d:%d", $hours1, $minutes1, $seconds1);
                $time_secondDT = isset($seconds1) ? $hours1 * 3600 + $minutes1 * 60 + $seconds1 : $hours1 * 60 + $minutes1;    
                $dtNow = $tmNow - $time_secondDT;
                $vrDT1 = gmdate("H:i:s", (int)$dtNow);
                $vrKdDt1 = 1;
            }
            else{
                $selisih = $qtyLastPros - $qtyActPr;
                if($pros == 1){
                    if($qtyActPr < $qtyTrgtSet){
                        $vrKdDt1 = 2;
                    }
                    else{
                        $vrKdDt1 = 0;
                    }
                }
                else{
                    if($selisih > 0 && $qtyActPr < $qtyTrgtSet){
                        $vrKdDt1 = 2;
                    }
                    else{
                        $vrKdDt1 = 0;
                    }    
                }
                if($jml1 == 0){$total1 = 0;} else{ $total1 = $total1;}
                $vrDT1 = gmdate("H:i:s", (int)$total1);
            }
                
            $item2['Kode1'] = $vrKdDt1;
            $item2['K1DT'] = $vrDT1;
            
            //================ tampilkan total downtime proses 2.2 ============
            $srat2DT_P2 = "Tanggal = '$tgle' AND Line = '$kode' AND Proses = '$pros' AND Kode_DT = '2' AND Start_Time != '' AND Finish_Time != ''"; 
            $this->db->where($srat2DT_P2);
            $jmlDT2 = $this->db->get('andon_downtime');
            $ctSum2 = 0; $jml2= 0; $total2 = 0;
                    
            foreach ($jmlDT2->result() as $row5) {
                $ctPerPcs = $row5->Start_Time;    
                $ct2PerPcs = $row5->Finish_Time; 
                    //====== data sekarang =======
                $ctn5 = date('H:i:s' , strtotime($ctPerPcs)); 
                sscanf($ctn5, "%d:%d:%d", $hours, $minutes, $seconds);
                $time_second5 = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;    
                    
                    //====== data terakhir =======
                $ctn6 = date('H:i:s' , strtotime($ct2PerPcs)); 
                sscanf($ctn6, "%d:%d:%d", $hours1, $minutes1, $seconds1);
                $time_second6 = isset($seconds1) ? $hours1 * 3600 + $minutes1 * 60 + $seconds1 : $hours1 * 60 + $minutes1;    
                    
                $ctSum2 = $time_second6 - $time_second5;
                $total2 = $total2 + $ctSum2;   
                $jml2 = $jml2 + 1;
            }
                    
            $syratDT2 = "Tanggal = '$tgle' AND Line = '$kode' AND Proses = '$pros' AND Kode_DT='2' AND Start_Time != '' AND Finish_Time = ''"; 
            $this->db->where($syratDT2);
            $vrDT2 = $this->db->get('andon_downtime')->row();
            if($vrDT2){
                sscanf($vrDT2->Start_Time, "%d:%d:%d", $hours1, $minutes1, $seconds1);
                $time_secondDT2 = isset($seconds1) ? $hours1 * 3600 + $minutes1 * 60 + $seconds1 : $hours1 * 60 + $minutes1;    
                $dtNow2 = $tmNow - $time_secondDT2;
                $vrDT2 = gmdate("H:i:s", (int)$dtNow2);
                $vrKdDt2 = 1;
            }
            else{
                if($jml2 == 0){$total2 = 0;} else{ $total2 = $total2;}
                $vrDT2 = gmdate("H:i:s", (int)$total2);
                $vrKdDt2 = 0;
            }

            $item2['Kode2'] = $vrKdDt2;
            $item2['K2DT'] = $vrDT2;

            $konone[] = $item2;
        }
        $nt = array('result' => $konone);
        $item['Prosese'] = $nt;
        $datane[] = $item;    
        return $datane;
    }

    function getDetailLineProduction($kode){
        $datane = array();
        $pengurangan = 0;
        $tgle = date('Y-m-d');
        $nowTime = date('H:i');
        $tglDis = date('d M Y');

        $taim = date('H:i:s');
        $kunci = "Line = '$kode' AND Status = '1'";
        $this->db->where($kunci);
        $kry = $this->db->get('andon_setting')->row();
        
        $ctTrgt = $kry->CT_Target;
        $mWorkHour = ((int)$kry->Work_Hour) * 60;
        $startTime = $kry->Start_Time;
        $brk1Time = $kry->Break1_Time .":00";
        $brk2Time = $kry->Break2_Time .":00";
        $lmBrk1 = ((int)$kry->Break1_Hour) * 60;
        $lmBrk2 = ((int)$kry->Break2_Hour) * 60;

        //============ using time =========//
        sscanf($taim, "%d:%d:%d", $hours, $minutes, $seconds);
        $tmNow = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;    


        sscanf($brk1Time.":00", "%d:%d:%d", $hours, $minutes, $seconds);
        $tmBrk1Start = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;    
        $tmBrk1Finish = $tmBrk1Start + $lmBrk1;

        sscanf($brk2Time.":00", "%d:%d:%d", $hours, $minutes, $seconds);
        $tmBrk2Start = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;    
        $tmBrk2Finish = $tmBrk2Start + $lmBrk2;
        
        if($tmNow < $tmBrk1Start){
            $pengurangan = 0;
        }
        else if(($tmNow >= $tmBrk1Start) && ($tmNow <= $tmBrk1Finish)){
            $pengurangan = $tmNow - $tmBrk1Start;
        }            
        else if(($tmNow > $tmBrk1Finish) && ($tmNow < $tmBrk2Start)){
            $pengurangan = $lmBrk1;
        }
        else if(($tmNow >= $tmBrk2Start) && ($tmNow <= $tmBrk2Finish)){
            $pengurangan = ($tmNow - $tmBrk2Start) + $lmBrk1;
        }
        else{
            $pengurangan = $lmBrk1 + $lmBrk2;            
        }            


        sscanf($startTime.":00", "%d:%d:%d", $hours, $minutes, $seconds);
        $tmStart = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;    
        $usingTime = $tmNow - ($tmStart + $pengurangan);
        if($usingTime < 0){$usingTime = 0;}

        $item['Line'] = $kry->Line;
        $item['Line_Name'] = $kry->Line_Name;
        $item['Date'] = $tglDis;
        $item['Now_Time'] = $nowTime;
        $item['Start_Time'] = $startTime;
        $useTime = gmdate("H:i", $usingTime);
        $item['Using_Time'] = $useTime;
        $item['Pengu'] = $pengurangan;



        $qtyTarget = ($mWorkHour) / ((int)$ctTrgt);
        $item['Qty_Target'] = $qtyTarget;
        $ctTarget = gmdate("H:i:s", $ctTrgt);
        $item['CT_Target'] = $ctTarget;


        //======== cari proses terakhir ===========//
        $keyLastPros = "Line = '$kode'";
        $this->db->where($keyLastPros);
        $this->db->order_by("Proses", "desc");
        $this->db->limit(1);
        $qryLastPros = $this->db->get('andon_setting')->row();
        $lastPros = (int)$qryLastPros->Proses;

        //======== cari actual qty ===========//
        $keyAct = "Tanggal = '$tgle' AND Line = '$kode' AND Proses = '$lastPros'";
        $this->db->where($keyAct);
        $qtyActual = $this->db->get('andon_production')->num_rows();
        $item['Qty_Actual'] = $qtyActual;

        //======== cari actual CT ===========//
        $lastCTPros = $lastPros-1;
        $keyAct1 = "Tanggal = '$tgle' AND Line = '$kode' AND Proses = '$lastCTPros'";
        $this->db->where($keyAct1);
        $this->db->order_by("No_ID", "asc");
        $this->db->limit(1);
        $qryCTActual = $this->db->get('andon_production')->row();
        if($qryCTActual){
            $startLastPros = $qryCTActual->Push_Time;
            sscanf($startLastPros, "%d:%d:%d", $hours, $minutes, $seconds);
            $tmStartLastPros = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;    
        }
        else{
            $tmStartLastPros = $tmStart;
        }
        $item['kk'] = gmdate("H:i:s", $tmStartLastPros);

        if($qtyActual == 0){
            if($tmStartLastPros >= $tmBrk2Finish){
                $vrCtAct = ($tmNow - $tmStartLastPros) / 1;
            }
            else{
                $vrCtAct = ($tmNow - ($tmStartLastPros + $pengurangan)) / 1;
            }
        }
        else{
            if($tmStartLastPros >= $tmBrk2Finish){
                $vrCtAct = ($tmNow - $tmStartLastPros) / $qtyActual;
            }
            else{
                $vrCtAct = ($tmNow - ($tmStartLastPros + $pengurangan)) / $qtyActual;
            }
        }

        if($vrCtAct < 0){$vrCtAct = 0;}

        $ctAct = gmdate("H:i:s", $vrCtAct);
        $item['CT_Actual'] = $ctAct;

        //======= cari balance =====//
        $item['Balance'] = $qtyActual - $qtyTarget;
        
        //======== cari actual Finish Good ===========//
        $keyFG = "Tanggal = '$tgle' AND Line = '$kode' AND Proses = '$lastPros' AND Status = '0'";
        $this->db->where($keyFG);
        $qtyFG = $this->db->get('andon_production')->num_rows();
        $item['Qty_Finish'] = $qtyFG;

        //======== cari actual Reject ===========//
        $keyNG = "Tanggal = '$tgle' AND  Line = '$kode' AND Status = '1'";
        $this->db->where($keyNG);
        $qtyNG = $this->db->get('andon_production')->num_rows();
        $item['Qty_NG'] = $qtyNG;


        $this->db->where('Line', $kode);
        $this->db->order_by("Proses", "asc");
        $kueri = $this->db->get('andon_setting');
        foreach ($kueri->result() as $row)
        {
                $pros = (int)$row->Proses;
                $item2['Proses'] = $pros;
                $item2['Proses_Name'] = $row->Proses_Name;
                $item2['Operator'] = $row->Operator;
                $ctCap = $row->CT_Capacity;
                if($ctCap == "0"){
                    $item2['Qty_Capacity'] = 0;    
                }
                else{
                    $qtyCapPros = ($mWorkHour) / ((int)$ctCap);
                    $item2['Qty_Capacity'] = $qtyCapPros;    
                }

                $ctCapacity = gmdate("H:i:s", $ctCap);
                $item2['CT_Capacity'] = $ctCapacity;   
                $ctTrgt = $row->CT_Target;
                $qtyTrgtSet = ($mWorkHour) / ((int)$ctTrgt);

                $prosSeb = $pros - 1;
                $keyQty = "Tanggal = '$tgle' AND Line = '$kode' AND Proses = '$prosSeb' AND Status = '0'";
                $this->db->where($keyQty);
                $this->db->order_by("No_ID", "asc");
                $this->db->limit(1);
                $qryCTActualPros = $this->db->get('andon_production')->row();
                if($qryCTActualPros){
                    $startCTPros = $qryCTActualPros->Push_Time;
                    sscanf($startCTPros, "%d:%d:%d", $hours, $minutes, $seconds);
                    $tmStartPros = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;        
                }
                else{
                    $tmStartPros = $tmNow;
                }

                $item2['test'] = gmdate("H:i:s", $tmStartPros);  


                if($pros == 1){
                    $qtyTargetPros = ($tmNow - ($tmStart + $pengurangan)) / ((int)$ctCap);
                    $ctTarAct = (int)$ctCap;
                }
                else{
                    if($tmStartPros >= $tmBrk2Finish){
                        $qtyTargetPros = ($tmNow - $tmStartPros) / ((int)$ctTrgt);
                    }
                    else{
                        $qtyTargetPros = ($tmNow - ($tmStartPros + $pengurangan)) / ((int)$ctTrgt);
                    }
                    $ctTarAct = (int)$ctTrgt;
                }
                if($qtyTargetPros < 0){ $qtyTargetPros = 0; }
                if($qtyTargetPros >= $qtyTrgtSet ) {$qtyTargetPros = $qtyTrgtSet;}                
                $item2['Qty_Target'] = floor($qtyTargetPros);    
                $ctTarget = gmdate("H:i:s", $ctTarAct);
                $item2['CT_Target'] = $ctTarget;    
                
                //======== aktual dikerjakan =============//
                $keyQty2 = "Tanggal = '$tgle' AND Line = '$kode' AND Proses = '$pros'";
                $this->db->where($keyQty2);
                $qtyActPr = $this->db->get('andon_production')->num_rows();
                $item2['Qty_Actual'] = $qtyActPr;
                if($qtyActPr == 0){
                    $pemBagi = 1;
                }
                else{
                    $pemBagi = $qtyActPr;
                }

                if($pros == 1){
                    if($tmStartPros >= $tmBrk2Finish){
                        $vrCTPros = ($tmNow - $tmStart) / $pemBagi;
                    }
                    else{
                        $vrCTPros = ($tmNow - ($tmStart + $pengurangan)) / $pemBagi;
                    }
                }
                else{
                    if($tmStartPros >= $tmBrk2Finish){
                        $vrCTPros = ($tmNow - $tmStartPros) / $pemBagi;
                    }
                    else{
                        $vrCTPros = ($tmNow - ($tmStartPros + $pengurangan)) / $pemBagi;
                    }
                }

                if($vrCTPros < 0){ $vrCTPros = 0;}
                $ctActualPros = gmdate("H:i:s", $vrCTPros);
                $item2['CT_Actual'] = $ctActualPros;    

                //======== aktual reject =============//
                $keyNG = "Tanggal = '$tgle' AND Line = '$kode' AND Proses = '$pros' AND Status = '1'";
                $this->db->where($keyNG);
                $qtyActNG = $this->db->get('andon_production')->num_rows();
                $item2['Qty_Reject'] = $qtyActNG;


                //================ tampilkan total downtime proses 2.1 ============
                $srat1DT_P2 = "Tanggal = '$tgle' AND Line = '$kode' AND Proses = '$pros' AND Kode_DT = '1' AND Start_Time != '' AND Finish_Time != ''"; 
                $this->db->where($srat1DT_P2);
                $jmlDT1 = $this->db->get('andon_downtime');
                $ctSum1 = 0; $jml1= 0; $total1 = 0;
                foreach ($jmlDT1->result() as $row4) {
                    $ctPerPcs = $row4->Start_Time;    
                    $ct2PerPcs = $row4->Finish_Time; 
                    //====== data sekarang =======
                    $ctn3 = date('H:i:s' , strtotime($ctPerPcs)); 
                    sscanf($ctn3, "%d:%d:%d", $hours, $minutes, $seconds);
                    $time_second3 = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;    
                
                    //====== data terakhir =======
                    $ctn4 = date('H:i:s' , strtotime($ct2PerPcs)); 
                    sscanf($ctn4, "%d:%d:%d", $hours1, $minutes1, $seconds1);
                    $time_second4 = isset($seconds1) ? $hours1 * 3600 + $minutes1 * 60 + $seconds1 : $hours1 * 60 + $minutes1;    
                
                    $ctSum1 = $time_second4 - $time_second3;
                    $total1 = $total1 + $ctSum1;   
                    $jml1 = $jml1 + 1;
                }

                $syratDT1 = "Tanggal = '$tgle' AND Line = '$kode' AND Proses = '$pros' AND Kode_DT='1' AND Start_Time != '' AND Finish_Time = ''"; 
                $this->db->where($syratDT1);
                $vrDT = $this->db->get('andon_downtime')->row();
                if($vrDT){
                    sscanf($vrDT->Start_Time, "%d:%d:%d", $hours1, $minutes1, $seconds1);
                    $time_secondDT = isset($seconds1) ? $hours1 * 3600 + $minutes1 * 60 + $seconds1 : $hours1 * 60 + $minutes1;    
                    $dtNow = $tmNow - $time_secondDT;
                    $vrDT1 = gmdate("H:i:s", (int)$dtNow);
                    $vrKdDt1 = 1;
                }
                else{
                    if($jml1 == 0){$total1 = 0;} else{ $total1 = $total1;}
                    $vrDT1 = gmdate("H:i:s", (int)$total1);
                    $vrKdDt1 = 0;
                }
                
                $item2['Kode1'] = $vrKdDt1;
                $item2['K1DT'] = $vrDT1;
                

                //================ tampilkan total downtime proses 2.2 ============
                $srat2DT_P2 = "Tanggal = '$tgle' AND Line = '$kode' AND Proses = '$pros' AND Kode_DT = '2' AND Start_Time != '' AND Finish_Time != ''"; 
                $this->db->where($srat2DT_P2);
                $jmlDT2 = $this->db->get('andon_downtime');
                $ctSum2 = 0; $jml2= 0; $total2 = 0;
                    
                foreach ($jmlDT2->result() as $row5) {
                    $ctPerPcs = $row5->Start_Time;    
                    $ct2PerPcs = $row5->Finish_Time; 
                    //====== data sekarang =======
                    $ctn5 = date('H:i:s' , strtotime($ctPerPcs)); 
                    sscanf($ctn5, "%d:%d:%d", $hours, $minutes, $seconds);
                    $time_second5 = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;    
                    
                    //====== data terakhir =======
                    $ctn6 = date('H:i:s' , strtotime($ct2PerPcs)); 
                    sscanf($ctn6, "%d:%d:%d", $hours1, $minutes1, $seconds1);
                    $time_second6 = isset($seconds1) ? $hours1 * 3600 + $minutes1 * 60 + $seconds1 : $hours1 * 60 + $minutes1;    
                    
                    $ctSum2 = $time_second6 - $time_second5;
                    $total2 = $total2 + $ctSum2;   
                    $jml2 = $jml2 + 1;
                }
                    
                $syratDT2 = "Tanggal = '$tgle' AND Line = '$kode' AND Proses = '$pros' AND Kode_DT='2' AND Start_Time != '' AND Finish_Time = ''"; 
                $this->db->where($syratDT2);
                $vrDT2 = $this->db->get('andon_downtime')->row();
                if($vrDT2){
                    sscanf($vrDT2->Start_Time, "%d:%d:%d", $hours1, $minutes1, $seconds1);
                    $time_secondDT2 = isset($seconds1) ? $hours1 * 3600 + $minutes1 * 60 + $seconds1 : $hours1 * 60 + $minutes1;    
                    $dtNow2 = $tmNow - $time_secondDT2;
                    $vrDT2 = gmdate("H:i:s", (int)$dtNow2);
                    $vrKdDt2 = 1;
                }
                else{
                    if($jml2 == 0){$total2 = 0;} else{ $total2 = $total2;}
                    $vrDT2 = gmdate("H:i:s", (int)$total2);
                    $vrKdDt2 = 0;
                }

                $item2['Kode2'] = $vrKdDt2;
                $item2['K2DT'] = $vrDT2;


                $konone[] = $item2;
        }
        $nt = array('result' => $konone);
        $item['Prosese'] = $nt;
        $datane[] = $item;    
        return $datane;
    }

    function insertDataAndon($datane, $line, $proses, $teme, $stb){
        $tgle = date('Y-m-d');
        $status="";
        $harine = date('w');
        $kunci = "No_ID = 1";
        //$kunci = "'$harine'";
        

        $this->db->where($kunci);
        $checkTime = $this->db->get('andon_daytime')->row();
        $finishTime = $checkTime->Finish_Time;
        $brk1Time = $checkTime->Break1_Time;
        $brk2Time = $checkTime->Break2_Time;
        sscanf($finishTime, "%d:%d:%d", $hours, $minutes, $seconds);
        $tmFinish = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;    

        if($checkTime->Lama_Break1 == ""){
            $lmBrk1 = 0;
        }
        else{
            $lmBrk1 = (int)$checkTime->Lama_Break1 * 60;
        }

        if($checkTime->Lama_Break2 == ""){
            $lmBrk2 = 0;
        }
        else{
            $lmBrk2 = (int)$checkTime->Lama_Break2 * 60;
        }

        $lmBrk = $lmBrk1 + $lmBrk2;

        //====================== informasi line ==============//
        $this->db->where('Kode_Line',$line);
        $row = $this->db->get('andon_line')->row();
        $kodeLine = $row->Kode_Line;
        $leader = $row->Leader;

        $ctMax = 0;
        $prosesMax = 0;
        $this->db->where("Line", $kodeLine);
        $this->db->order_by("Proses", "asc");
        $kueri1 = $this->db->get('andon_proses');
        foreach ($kueri1->result() as $row1){
            $ctCapacity1 = $row1->CT_Capacity;
            if($ctCapacity1 > $ctMax){
                $ctMax = $ctCapacity1;
                $prosesMax = $row1->Proses;
            } 
            else{
                $ctMax = $ctMax;
                $prosesMax = $prosesMax;
            }
        }
        if($row->Start_Time == ""){
            $strTime = "08:00:00";
        }
        else{
            $strTime = $row->Start_Time;
        }

        sscanf($strTime, "%d:%d:%d", $hours, $minutes, $seconds);
        $tmStart = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;    
        $WorkHour = $tmFinish - ($tmStart + $lmBrk);     
        $effWH = ($row->Efektif_Kerja / 100) * $WorkHour;
        if($ctMax == "0"){
            $qtyTarget = 0;
            $ctMax = 0;
        }
        else{
            $qtyTarget = ((int)$effWH) / ((int)$ctMax);
        }
        $qtyTrgt = floor($qtyTarget);

        $kunci = "Tanggal = '$tgle' AND Line = '$line' AND Proses = '$proses'";
        $this->db->where($kunci);
        $qtyAct = $this->db->get('andon_production')->num_rows();

        $newPros = $proses - 1;
        $afterPros = $proses + 1;

        if($stb == 0){
            $data = array(
                'Finish_Time' => $teme,
            );
            $syrat = "Tanggal = '$tgle' AND Line = '$line' AND Proses = '$afterPros' AND Kode_DT='1' AND Start_Time != '' AND Finish_Time = ''";
            $this->db->where($syrat);
            $kueri = $this->db->get('andon_downtime')->row();
            if($kueri){
                $this->db->set($data);
                $this->db->where($syrat);
                $this->db->update('andon_downtime');
            }    
        }

        if($proses == 1){
            if($qtyAct < $qtyTrgt){
                $kueri1 = $this->db->insert('andon_production', $datane);
                if($kueri1){
                    $status = "sukses";
                }
                else{
                    $status = "gagal";
                }  
            }
            else{
                $status = "gagal"; 
            } 
        }
        else{
            $kuncine = "Tanggal = '$tgle' AND Line = '$line' AND Proses = '$newPros' AND Status = '0'";
            $this->db->where($kuncine);
            $lastPros = $this->db->get('andon_production')->num_rows();    
            $difQty = (int)$lastPros - (int)$qtyAct;
            if($difQty >= 1 && $qtyAct < $qtyTrgt){
                if($difQty == 1){
                    $varine = array(
                        'Tanggal' => $tgle,
                        'Line' => $line,
                        'Proses' => $proses,
                        'Start_Time' => $teme,
                        'Kode_DT' => '1',
                    );
                    $kueri3 = $this->db->insert('andon_downtime', $varine);    
                }
                $kueri2 = $this->db->insert('andon_production', $datane);
                if($kueri2){
                    $status = "sukses";
                }
                else{
                    $status = "gagal";
                }     
            }
            else{
                $status = "gagal";
            }
        }
     

        return $status;

    }

    function insertStartDownTime($datane, $syarate){
        $this->db->where($syarate);
        $kueri1 = $this->db->get('andon_downtime')->row();
        if($kueri1){
            return "masih trouble";
        }
        else{
            $kueri2 = $this->db->insert('andon_downtime', $datane);
            if($kueri2){
                return "sukses";
            }
            else{
                return "gagal";
            }    
        }
    }

    function insertFinishDownTime($datane, $syarate){
        $this->db->where($syarate);
        $kueri1 = $this->db->get('andon_downtime')->row();
        if($kueri1){
            $this->db->set($datane);
            $this->db->where($syarate);
            $kueri = $this->db->update('andon_downtime');
            if($kueri){
                $status = "sukses";
            }
            else{
                $status = "gagal";
            }
        }
        else{
            $status = "not found";  
        }
        return $status;
    }

    function getOperatorProses($idne){
        $datane = array();
        $kunci = "Line = '$idne'";
        $this->db->where($kunci);
        $this->db->order_by("Proses", "asc");
        $kueri = $this->db->get('andon_proses');

        foreach ($kueri->result() as $row)
        {
            $kode = $row->Line;
            $pros = $row->Proses;
            $subPros = $row->Sub_Proses;

            $item['Line'] = $row->Line;
            $item['Proses'] = $row->Proses;
            $item['Proses_Name'] = $row->Proses_Name;
            $item['Sub_Proses'] = $row->Sub_Proses;

            $kncCheckOP = "Line = '$kode' AND Proses = '$pros'";
            $this->db->where($kncCheckOP);
            $checkOP = $this->db->get('andon_proses')->num_rows();

            if($checkOP > 1){
                $item['Jenenge'] = "OP " . $pros . "." . $subPros;
            }
            else{
                $item['Jenenge'] = "OP " . $pros;
            }


            $kodene = $row->Operator;
            if($kodene == ""){
                $item["Ld"] = 0;
            }
            else{
                $kunciLd = "Leader = '$kodene'";
                $this->db->where($kunciLd);
                $kueri2 = $this->db->get('andon_line')->row();
                if($kueri2){
                    $item["Ld"] = 1;
                }
                else{
                    $item["Ld"] = 0;
                }    
            }


            $kunciOp = "NIK = '$kodene'";
            $this->db->where($kunciOp);
            $kueri1 = $this->db->get('andon_daftar_operator')->row();
            if($kueri1){
                $item['Kode'] = $kodene;
                $item['Name'] = $kueri1->Nama_Lengkap;
                $item['Nick'] = $kueri1->Nick_Name;
                $item['Gambar'] = $kueri1->Photo;    
            }
            else{
                $item['Kode'] = "";
                $item['Name'] = "";
                $item['Nick'] = "";
                $item['Gambar'] = "";
    
            }

            $datane[] = $item;    
        }

        return $datane;
    }


    function getAllOperator(){
        $datane = array();
        $this->db->order_by("NIK", "asc");
        $kueri = $this->db->get('andon_daftar_operator');
        foreach ($kueri->result() as $row)
        {

            $item['NIK'] = $row->NIK;
            $item['Nama'] = $row->Nama_Lengkap;
            $item['Nick'] = $row->Nick_Name;

            $kodene = $row->NIK;
            
            $kunciOp = "Operator = '$kodene'";
            $this->db->where($kunciOp);
            $kueri1 = $this->db->get('andon_proses')->row();
            if($kueri1){
                $kdLine = $kueri1->Line;
                $kunciLn = "Kode_Line = '$kdLine'";
                $this->db->where($kunciLn);
                $kueri2 = $this->db->get('andon_line')->row();
                if($kueri2){
                    $item['Line'] = $kueri2->Line_Name ;
                    $item['KdLine'] = 1;
                }
                else{
                    $item['Line'] = "";
                    $item['KdLine'] = 0;
                }                
    
            }
            else{
                $item['Line'] = "";    
                $item['KdLine'] = 0;

            }

            $datane[] = $item;    
        }

        return $datane;
    }

    function updateSettOperator($data1, $syrt, $nm, $data2, $syrt2, $ld){
        $kunciOp = "Operator = '$nm'";
        $this->db->where($kunciOp);
        $kueri1 = $this->db->get('andon_proses')->row();
        if($kueri1){
            $status = "Operator Sudah Digunakan";
        }
        else{
            if($ld == "1"){
                $this->db->set($data2);
                $this->db->where($syrt2);
                $this->db->update('andon_line');    
            }

            $this->db->set($data1);
            $this->db->where($syrt);
            $kueri = $this->db->update('andon_proses');
            if($kueri){
                $status = "sukses";
            }
            else{
                $status = "gagal";
            }    
        }
        return $status;
    }

    function getDetailOperator($syarat){
        $datane = array();
        $this->db->where('NIK', $syarat);
        $row = $this->db->get('andon_daftar_operator')->row();

        $item['NIK'] = $row->NIK;
        $item['Nama'] = $row->Nama_Lengkap;
        $item['Nick'] = $row->Nick_Name;

        $kodene = $row->NIK;
            
        $kunciOp = "Operator = '$kodene'";
        $this->db->where($kunciOp);
        $kueri1 = $this->db->get('andon_proses')->row();
        if($kueri1){
            $kdLine = $kueri1->Line;
            $kunciLn = "Kode_Line = '$kdLine'";
            $this->db->where($kunciLn);
            $kueri2 = $this->db->get('andon_line')->row();
            if($kueri2){
                $item['Line'] = $kueri2->Line_Name ;
                $item['KdLine'] = 1;
            }
            else{
                $item['Line'] = "";
                $item['KdLine'] = 0;
            }                
    
        }
        else{
            $item['Line'] = "";    
            $item['KdLine'] = 0;
        }

        $datane[] = $item;    

        return $datane;
    }

    function updateDataOperator($data1, $syrt, $nik, $line){

        $this->db->set($data1);
        $this->db->where($syrt);
        $kueri = $this->db->update('andon_daftar_operator');
        if($kueri){
            $status = "sukses";
        }
        else{
            $status = "gagal";
        }    

        $varie = array("Leader" => "",);
        $kunciLd = "Leader = '$nik'";
        $this->db->set($varie);
        $this->db->where($kunciLd);
        $this->db->update('andon_line');


        $kunciOp = "Operator = '$nik'";
        $this->db->where($kunciOp);
        $kueri1 = $this->db->get('andon_proses')->row();
        if($line == 0){
            if($kueri1){
                $vari = array("Operator" => "",);
                $this->db->set($vari);
                $this->db->where($kunciOp);
                $this->db->update('andon_proses');
            }    
        }
        return $status;
    }

    function deleteOperator($nik){
        $syrate = "NIK = '$nik'"; 
        $this->db->where($syrate);
        $kueri = $this->db->delete('andon_daftar_operator');
        if($kueri){
            $status = "sukses";
        }
        else{
            $status = "gagal";
        }    


        $kunciOp = "Operator = '$nik'";
        $this->db->where($kunciOp);
        $kueri1 = $this->db->get('andon_proses')->row();
        if($kueri1){
            $vari = array("Operator" => "",);
            $this->db->set($vari);
            $this->db->where($kunciOp);
            $this->db->update('andon_proses');
        }    
        return $status;
    }

    function deleteLine($nik){
        $syrate = "Kode_Line = '$nik'"; 
        $this->db->where($syrate);
        $kueri = $this->db->delete('andon_line');
        if($kueri){
            $syrat = "Line = '$nik'"; 
            $this->db->where($syrat);
            $kueri1 = $this->db->delete('andon_proses');
            if($kueri1){
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


}