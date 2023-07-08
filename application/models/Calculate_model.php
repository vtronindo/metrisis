<?php defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('Asia/Jakarta');
class Calculate_model extends CI_Model
{
    function getTargetByProses($nile){
        $taim = date('H:i:s');
        $datane = array();
        $this->db->where($nile);
        $this->db->order_by("Proses","asc");
        $kueri = $this->db->get('andon_target');
        foreach ($kueri->result() as $row)
        {
            $item['Real_Time'] = $taim;
            $item['Line'] = $row->Line;
            $item['Proses'] = $row->Proses;
            $item['CT_Capacity'] = (int)$row->CT_Standart;
            $item['CT_Target'] = (int)$row->CT_Target;
            $item['Qty_Capacity'] = (int)$row->Working_Hour / (int)$row->CT_Standart;
            $item['Qty_Target'] = (int)$row->Working_Hour / (int)$row->CT_Target;
            $datane[] = $item;    
        }
        return $datane;
    }

    function getActualByProses($nile){
        $taim = date('H:i:s');
        $tgle = date('Y-m-d');
        
        $syrat1 = array(
            'Line' => $nile,
            'Status_Proses' => '1',
        );
        $this->db->where($syrat1);
        $kue = $this->db->get('andon_target')->row();
        $qtyTrgt = (int)$kue->Working_Hour / (int)$kue->CT_Target;
        $ctTrgt = (int)$kue->CT_Target;
        $brkStart = $kue->Rest_Time;
        $lmBrk = ((int)$kue->Break_Time * 60);
        $startTem = $kue->Start_Time;

        $syrat = array(
            'Line' => $nile,
        );

        $datane = array();
        $this->db->where($syrat);
        $this->db->order_by("Proses","asc");
        $kueri = $this->db->get('andon_target');
        $vrKrmDT_P2 = 0;


        foreach ($kueri->result() as $row)
        {
            sscanf($taim, "%d:%d:%d", $hours44, $minutes44, $seconds44);
            $RT_seconds = isset($seconds44) ? $hours44 * 3600 + $minutes44 * 60 + $seconds44 : $hours44 * 60 + $minutes44;    
            $item['Real_Time'] = $RT_seconds;

            sscanf($startTem, "%d:%d:%d", $hours34, $minutes34, $seconds34);
            $ST_seconds = isset($seconds44) ? $hours34 * 3600 + $minutes34 * 60 + $seconds34 : $hours34 * 60 + $minutes34;    
            $item['Start_Time'] = $ST_seconds;


            $item['Line'] = $row->Line;
            $proses = (int)$row->Proses;

            $item['Proses'] = $proses;
            $item['CT_Capacity'] = (int)$row->CT_Standart;
            $item['CT_Target'] = $ctTrgt;
            $item['Qty_Capacity'] = (int)$row->Working_Hour / (int)$row->CT_Standart;
            $item['Qty_Target'] = $qtyTrgt;

            sscanf($brkStart, "%d:%d:%d", $hours, $minutes, $seconds);
            $time_Brk_Start = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;    

            $time_Brk_Finish = $time_Brk_Start + $lmBrk;            

            if($proses == 1){
                
                $kuncine = "Tanggal = '$tgle' AND Line = '$nile' AND Proses = '$proses'";
                $this->db->where($kuncine);
                $vrAct_P1 = $this->db->get('andon_production')->num_rows();
                $item['Qty_Act'] = $vrAct_P1;

                $kunciNG = "Tanggal = '$tgle' AND Line = '$nile' AND Proses = '$proses' AND Status = '1'";
                $this->db->where($kunciNG);
                $vrNG_P1 = $this->db->get('andon_production')->num_rows();
                $item['Qty_NG'] = $vrNG_P1;
                $vrActTot_P1 = (int)($vrAct_P1 - $vrNG_P1);

                sscanf($taim, "%d:%d:%d", $hours, $minutes, $seconds);
                $time_seconds1 = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;    

                $ctne1 = date('H:i:s' , strtotime($startTem)); 
                sscanf($ctne1, "%d:%d:%d", $hours, $minutes, $seconds);
                $time_seconds2 = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;    
                
                    //================ proses belum dimulai ============
                    if((int)($time_seconds1 - $time_seconds2) <= 0){
                        $item['Qty_TargetAct'] = 0;
                        $item['CT_Act'] = 0;
                        $item['ST_Pros'] = 0;
                        $item['DT'] = 0;
                        $item['KodeDT'] = 0;   
                        $item['K1DT'] = 0;
                        $item['K2DT'] = 0;    
                    }
                    //================ proses sudah dimulai ============
                    else{
                        if($vrAct_P1 >= $qtyTrgt || (($time_seconds1 >= $time_Brk_Start) && ($time_seconds1 <= $time_Brk_Finish))){
                            //============ program downtime ===========//
                            $lastProd = "Tanggal = '$tgle' AND Line = '$nile' AND Proses = '$proses'"; 
                            $this->db->where($lastProd);
                            $this->db->order_by("No_ID", "desc");
                            $this->db->limit(1);
                            $checklastProd = $this->db->get('andon_production')->row(); 
                            if(!$checklastProd){$time_secondLast = $time_seconds2;}
                            else{
                                sscanf($checklastProd->Push_Time, "%d:%d:%d", $hours, $minutes, $seconds);
                                $time_secondLast = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;        
                            }  




                            $item['Qty_Act'] = $vrAct_P1;
                            $item['ST_Pros'] = 0;
                            $item['Qty_TargetAct'] = $qtyTrgt;
                            if($vrAct_P1 == 0){
                                $item['CT_Act'] = (int)(($time_secondLast - $time_seconds2) / 1);
                            }
                            else{
                                $item['CT_Act'] = (int)(($time_secondLast - $time_seconds2) / $vrAct_P1);
                            }
                            $item['KodeDT'] = 0;
                            //================ tampilkan total downtime proses 2 ============
                            $sratDT_P2 = "Tanggal = '$tgle' AND Line = '$nile' AND Proses = '$proses' AND Start_Time != '' AND Finish_Time != ''"; 
                            $this->db->where($sratDT_P2);
                            $jmlDT = $this->db->get('andon_downtime');
                            $ctSum = 0; $jml= 0; $total = 0;

                            foreach ($jmlDT->result() as $row3) {
                                    $ctPerPcs = $row3->Start_Time;    
                                    $ct2PerPcs = $row3->Finish_Time; 

                                    //====== data sekarang =======
                                    $ctn1 = date('H:i:s' , strtotime($ctPerPcs)); 
                                    sscanf($ctn1, "%d:%d:%d", $hours, $minutes, $seconds);
                                    $time_second1 = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;    

                                    //====== data terakhir =======
                                    $ctn2 = date('H:i:s' , strtotime($ct2PerPcs)); 
                                    sscanf($ctn2, "%d:%d:%d", $hours1, $minutes1, $seconds1);
                                    $time_second2 = isset($seconds1) ? $hours1 * 3600 + $minutes1 * 60 + $seconds1 : $hours1 * 60 + $minutes1;    

                                    $ctSum = $time_second2 - $time_second1;
                                    $total = $total + $ctSum;   
                                    $jml = $jml + 1;
                            }

                            if($jml == 0){$total = 0;} else{ $total = $total;}
                            $item['DT'] = (int) $total;


                            //================ tampilkan total downtime proses 2.1 ============
                            $srat1DT_P2 = "Tanggal = '$tgle' AND Line = '$nile' AND Proses = '$proses' AND Kode_DT = '1' AND Start_Time != '' AND Finish_Time != ''"; 
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
    
                            if($jml1 == 0){$total1 = 0;} else{ $total1 = $total1;}
                            $item['K1DT'] = (int)$total1;
    

                            //================ tampilkan total downtime proses 2.2 ============
                            $srat2DT_P2 = "Tanggal = '$tgle' AND Line = '$nile' AND Proses = '$proses' AND Kode_DT = '2' AND Start_Time != '' AND Finish_Time != ''"; 
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
        
                            if($jml2 == 0){$total2 = 0;} else{ $total2 = $total2;}
                            $item['K2DT'] = (int)$total2;

                        }
                        else{

                            if($time_seconds1 > $time_Brk_Finish){
                                $qtyTrgtAct = (int)(($time_seconds1 - ($time_seconds2 + ($lmBrk))) / ((int)$row->CT_Target * 60));
                                if($vrAct_P1 == 0){
                                    $item['CT_Act'] = ($time_seconds1 - ($time_seconds2 + ($lmBrk))) / 1;
                                }
                                else{
                                    $item['CT_Act'] = (int)(($time_seconds1 - ($time_seconds2 + ($lmBrk))) / $vrAct_P1);
                                }
    
                            }
                            else{
                                $qtyTrgtAct = (int)(($time_seconds1 - $time_seconds2) / ((int)$row->CT_Target * 60));
                                if($vrAct_P1 == 0){
                                    $item['CT_Act'] = ($time_seconds1 - $time_seconds2) / 1;
                                }
                                else{
                                    $item['CT_Act'] = (int)(($time_seconds1 - $time_seconds2) / $vrAct_P1);
                                }    
                            }

                            if($qtyTrgtAct >= $qtyTrgt){
                                $item['Qty_TargetAct'] = $qtyTrgt;
                            }
                            else{
                                $item['Qty_TargetAct'] = $qtyTrgtAct;
                            }
                            $item['Qty_Act'] = $vrAct_P1;
                            $item['ST_Pros'] = 1;
            
                            
                                                        //============ program downtime ===========//
                            $srtDT_P2 = "Tanggal = '$tgle' AND Line = '$nile' AND Proses = '$proses' AND Start_Time != '' AND Finish_Time = ''"; 
                            $this->db->where($srtDT_P2);
                            $this->db->order_by("No_ID", "desc");
                            $this->db->limit(1);
                            $checkDTPros1 = $this->db->get('andon_downtime')->row();   
                            //============= jika ada downtime ==========
                            if($checkDTPros1){
                                $startDT = $checkDTPros1->Start_Time;
                                sscanf($startDT, "%d:%d:%d", $hours, $minutes, $seconds);
                                $time_secon3 = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;    
                                $item['DT'] = ($time_seconds1 - $time_secon3);
                                $item['KodeDT'] = (int)$checkDTPros1->Kode_DT;    
                                if($checkDTPros1->Kode_DT == 1){
                                    $item['K1DT'] = ($time_seconds1 - $time_secon3);
                                        //================ tampilkan total downtime proses 2.2 ============
                                    $srat2DT_P2 = "Tanggal = '$tgle' AND Line = '$nile' AND Proses = '$proses' AND Kode_DT = '2' AND Start_Time != '' AND Finish_Time != ''"; 
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
                
                                    if($jml2 == 0){$total2 = 0;} else{ $total2 = $total2;}
                                    $item['K2DT'] = (int)$total2;
                

                                }
                                else{
                                    $item['K2DT'] = ($time_seconds1 - $time_secon3);
                                        //================ tampilkan total downtime proses 2.1 ============
                                    $srat1DT_P2 = "Tanggal = '$tgle' AND Line = '$nile' AND Proses = '$proses' AND Kode_DT = '1' AND Start_Time != '' AND Finish_Time != ''"; 
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

                                    if($jml1 == 0){$total1 = 0;} else{ $total1 = $total1;}
                                    $item['K1DT'] = (int)$total1;

                                }
                            }
                            //============ jika tidak ada downtime =======
                            else{
                                        $item['KodeDT'] = 0;
                                        //================ tampilkan total downtime proses 2 ============
                                        $sratDT_P2 = "Tanggal = '$tgle' AND Line = '$nile' AND Proses = '$proses' AND Start_Time != '' AND Finish_Time != ''"; 
                                        $this->db->where($sratDT_P2);
                                        $jmlDT = $this->db->get('andon_downtime');
                                        $ctSum = 0; $jml= 0; $total = 0;

                                        foreach ($jmlDT->result() as $row3) {
                                                $ctPerPcs = $row3->Start_Time;    
                                                $ct2PerPcs = $row3->Finish_Time; 

                                                //====== data sekarang =======
                                                $ctn1 = date('H:i:s' , strtotime($ctPerPcs)); 
                                                sscanf($ctn1, "%d:%d:%d", $hours, $minutes, $seconds);
                                                $time_second1 = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;    

                                                //====== data terakhir =======
                                                $ctn2 = date('H:i:s' , strtotime($ct2PerPcs)); 
                                                sscanf($ctn2, "%d:%d:%d", $hours1, $minutes1, $seconds1);
                                                $time_second2 = isset($seconds1) ? $hours1 * 3600 + $minutes1 * 60 + $seconds1 : $hours1 * 60 + $minutes1;    

                                                $ctSum = $time_second2 - $time_second1;
                                                $total = $total + $ctSum;   
                                                $jml = $jml + 1;
                                        }

                                        if($jml == 0){$total = 0;} else{ $total = $total;}
                                        $item['DT'] = (int) $total;


                                        //================ tampilkan total downtime proses 2.1 ============
                                        $srat1DT_P2 = "Tanggal = '$tgle' AND Line = '$nile' AND Proses = '$proses' AND Kode_DT = '1' AND Start_Time != '' AND Finish_Time != ''"; 
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
                
                                        if($jml1 == 0){$total1 = 0;} else{ $total1 = $total1;}
                                        $item['K1DT'] = (int)$total1;
                

                                        //================ tampilkan total downtime proses 2.2 ============
                                        $srat2DT_P2 = "Tanggal = '$tgle' AND Line = '$nile' AND Proses = '$proses' AND Kode_DT = '2' AND Start_Time != '' AND Finish_Time != ''"; 
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
                    
                                        if($jml2 == 0){$total2 = 0;} else{ $total2 = $total2;}
                                        $item['K2DT'] = (int)$total2;
                            }

                        }
  
                    }



                    



            }
            
            else if($proses == 2){
                $kuncine = "Tanggal = '$tgle' AND Line = '$nile' AND Proses = '$proses'";
                $this->db->where($kuncine);
                $vrAct_P2 = $this->db->get('andon_production')->num_rows();
                $item['Qty_Act'] = $vrAct_P2;

                $kunciNG = "Tanggal = '$tgle' AND Line = '$nile' AND Proses = '$proses' AND Status = '1'";
                $this->db->where($kunciNG);
                $vrNG_P2 = $this->db->get('andon_production')->num_rows();
                $item['Qty_NG'] = $vrNG_P2;
                $vrActTot_P2 = (int)($vrAct_P2 - $vrNG_P2);
    
                $newPros = $proses - 1;
                $kuncine = "Tanggal = '$tgle' AND Line = '$nile' AND Proses = '$newPros' AND Status = '0'";
                $this->db->where($kuncine);
                $this->db->order_by("No_ID", "asc");
                $this->db->limit(1);
                $startPros2 = $this->db->get('andon_production')->row();
                
                //============= proses belum dimulai, nunggu start dari proses 1 =========
                if(!$startPros2){
                    $item['Qty_TargetAct'] = 0;//(int)(($time_seconds1 - $time_seconds2) / ((int)$row->CT_Target * 60));
                    $item['CT_Act'] = 0;
                    $item['ST_Pros'] = 0;
                    $item['DT'] = 0;
                    $item['KodeDT'] = 0;   
                    $item['K1DT'] = 0;
                    $item['K2DT'] = 0;                 
                }
                else{

                    if($vrAct_P2 >= $qtyTrgt || (($time_seconds1 >= $time_Brk_Start) && ($time_seconds1 <= $time_Brk_Finish))){
                        //============ program downtime ===========//
                        $lastProd = "Tanggal = '$tgle' AND Line = '$nile' AND Proses = '$proses'"; 
                        $this->db->where($lastProd);
                        $this->db->order_by("No_ID", "desc");
                        $this->db->limit(1);
                        $checklastProd = $this->db->get('andon_production')->row();   

                        $firstProd = "Tanggal = '$tgle' AND Line = '$nile' AND Proses = '$proses'"; 
                        $this->db->where($firstProd);
                        $this->db->order_by("No_ID", "asc");
                        $this->db->limit(1);
                        $checkFirstProd = $this->db->get('andon_production')->row();   

                        sscanf($checklastProd->Push_Time, "%d:%d:%d", $hours, $minutes, $seconds);
                        $time_secondLast = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;    


                        sscanf($checkFirstProd->Push_Time, "%d:%d:%d", $hours1, $minutes1, $seconds1);
                        $time_secondFirst = isset($seconds1) ? $hours1 * 3600 + $minutes1 * 60 + $seconds1 : $hours1 * 60 + $minutes1;    



                        $item['Qty_Act'] = $vrAct_P2;
                        $item['ST_Pros'] = 0;
                        $item['Qty_TargetAct'] = $qtyTrgt;
                        $item['CT_Act'] = (int)(($time_secondLast - $time_secondFirst) / $vrAct_P2);
                        $item['KodeDT'] = 0;

                                //================ tampilkan total downtime proses 2 ============
                                $sratDT_P2 = "Tanggal = '$tgle' AND Line = '$nile' AND Proses = '$proses' AND Start_Time != '' AND Finish_Time != ''"; 
                                $this->db->where($sratDT_P2);
                                $jmlDT = $this->db->get('andon_downtime');
                                $ctSum = 0; $jml= 0; $total = 0;

                                foreach ($jmlDT->result() as $row3) {
                                        $ctPerPcs = $row3->Start_Time;    
                                        $ct2PerPcs = $row3->Finish_Time; 

                                        //====== data sekarang =======
                                        $ctn1 = date('H:i:s' , strtotime($ctPerPcs)); 
                                        sscanf($ctn1, "%d:%d:%d", $hours, $minutes, $seconds);
                                        $time_second1 = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;    

                                        //====== data terakhir =======
                                        $ctn2 = date('H:i:s' , strtotime($ct2PerPcs)); 
                                        sscanf($ctn2, "%d:%d:%d", $hours1, $minutes1, $seconds1);
                                        $time_second2 = isset($seconds1) ? $hours1 * 3600 + $minutes1 * 60 + $seconds1 : $hours1 * 60 + $minutes1;    

                                        $ctSum = $time_second2 - $time_second1;
                                        $total = $total + $ctSum;   
                                        $jml = $jml + 1;
                                }

                                if($jml == 0){$total = 0;} else{ $total = $total;}
                                $item['DT'] = (int) $total;

                                //================ tampilkan total downtime proses 2.1 ============
                                $srat1DT_P2 = "Tanggal = '$tgle' AND Line = '$nile' AND Proses = '$proses' AND Kode_DT = '1' AND Start_Time != '' AND Finish_Time != ''"; 
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

                                if($jml1 == 0){$total1 = 0;} else{ $total1 = $total1;}
                                $item['K1DT'] = (int)$total1;

                                //================ tampilkan total downtime proses 2.2 ============
                                $srat2DT_P2 = "Tanggal = '$tgle' AND Line = '$nile' AND Proses = '$proses' AND Kode_DT = '2' AND Start_Time != '' AND Finish_Time != ''"; 
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

                                if($jml2 == 0){$total2 = 0;} else{ $total2 = $total2;}
                                $item['K2DT'] = (int)$total2;
                    }
                    
                    else{


                        sscanf($taim, "%d:%d:%d", $hours, $minutes, $seconds);
                        $time_seconds1 = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;    
        
                        $ctne1 = $startPros2->Push_Time; 
                        sscanf($ctne1, "%d:%d:%d", $hours, $minutes, $seconds);
                        $time_seconds2 = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;    
                        
                        if($time_seconds1 > $time_Brk_Finish){
                            $qtyTrgtAct = (int)(($time_seconds1 - ($time_seconds2 + ($lmBrk))) / ((int)$row->CT_Target * 60));
                            $item['Qty_TargetAct'] = $qtyTrgtAct;
 
                            if($vrAct_P2 == 0){
                                $item['CT_Act'] = ($time_seconds1 - ($time_seconds2 + ($lmBrk))) / 1;
                            }
                            else{
                                $item['CT_Act'] = (int)(($time_seconds1 - ($time_seconds2 + ($lmBrk))) / $vrAct_P2);
                            }

                        }
                        else{
                            $qtyTrgtAct = (int)(($time_seconds1 - $time_seconds2) / ((int)$row->CT_Target * 60));
                            $item['Qty_TargetAct'] = $qtyTrgtAct;
 
                            if($vrAct_P2 == 0){
                                $item['CT_Act'] = ($time_seconds1 - $time_seconds2) / 1;
                            }
                            else{
                                $item['CT_Act'] = (int)(($time_seconds1 - $time_seconds2) / $vrAct_P2);
                            }    
                        }

                        $item['ST_Pros'] = 1;

                        //======== jika actual Proses 2 >= actual proses 1
                        if($vrAct_P2 >= $vrActTot_P1){
                            $kuncino = "Tanggal = '$tgle' AND Line = '$nile' AND Proses = '$proses'";
                            $this->db->where($kuncino);
                            $this->db->order_by("Push_Time", "desc");
                            $this->db->limit(1);
                            $startDTPros2 = $this->db->get('andon_production')->row();   
                            $startDT = $startDTPros2->Push_Time;
                    
                            $srtDT_P2 = "Tanggal = '$tgle' AND Line = '$nile' AND Proses = '$proses'"; 
                            $this->db->where($srtDT_P2);
                            $this->db->order_by("No_ID", "desc");
                            $this->db->limit(1);
                            $checkDTPros2 = $this->db->get('andon_downtime')->row();   
                            
                            if(!$checkDTPros2){
                                $vrDT_P2 = array('Tanggal' => $tgle, 'Line' => $nile, 'Proses' => $proses, 'Kode_DT' => '1', 'Start_Time' => $startDT,);
                                $this->db->insert('andon_downtime', $vrDT_P2);    
                            }
                            else{
                                if($checkDTPros2->Finish_Time != ''){
                                    $vrDT_P2 = array('Tanggal' => $tgle, 'Line' => $nile, 'Proses' => $proses, 'Kode_DT' => '1', 'Start_Time' => $startDT,);
                                    $this->db->insert('andon_downtime', $vrDT_P2);    
                                }    
                            }

                            sscanf($startDT, "%d:%d:%d", $hours, $minutes, $seconds);
                            $time_seconds3 = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;    
                            $item['DT'] = ($time_seconds1 - $time_seconds3);
                            $item['KodeDT'] = 1;
                            $item['K1DT'] = ($time_seconds1 - $time_seconds3);
                                //================ tampilkan total downtime proses 2.2 ============
                            $srat2DT_P2 = "Tanggal = '$tgle' AND Line = '$nile' AND Proses = '$proses' AND Kode_DT = '2' AND Start_Time != '' AND Finish_Time != ''"; 
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

                            if($jml2 == 0){$total2 = 0;} else{ $total2 = $total2;}
                            $item['K2DT'] = (int)$total2;


                            
                        }

                        //======== Proses Normal =========//
                        else{
                            $kuncino = "Tanggal = '$tgle' AND Line = '$nile' AND Proses = '$newPros'";
                            $this->db->where($kuncino);
                            $this->db->order_by("Push_Time", "desc");
                            $this->db->limit(1);
                            $finishDTPros2 = $this->db->get('andon_production')->row();   
                            $finishDT = $finishDTPros2->Push_Time;

                            $srtDT_P2 = "Tanggal = '$tgle' AND Line = '$nile' AND Proses = '$proses' AND Kode_DT = '1' AND Start_Time != '' AND Finish_Time = ''"; 
                            $vrKrmDT_P2 = array('Finish_Time' => $finishDT,);
                            $this->db->set($vrKrmDT_P2);
                            $this->db->where($srtDT_P2);
                            $this->db->update('andon_downtime');

                            $srt1DT_P2 = "Tanggal = '$tgle' AND Line = '$nile' AND Proses = '$proses' AND Kode_DT = '2' AND Start_Time != '' AND Finish_Time = ''"; 
                            $this->db->where($srt1DT_P2);
                            $kdDT = $this->db->get('andon_downtime');
                            //======= jika ada downtime mechine =========
                            if($kdDT->row()){
                                $item['KodeDT'] = 2;
                                $ctSum11 = 0; $jml11= 0; $total11 = 0;

                                foreach ($kdDT->result() as $row31) {
                                        $ctPerPcs = $row31->Start_Time;    

                                        //====== data mulai =======
                                        $ctn11 = date('H:i:s' , strtotime($ctPerPcs)); 
                                        sscanf($ctn11, "%d:%d:%d", $hours, $minutes, $seconds);
                                        $time_second11 = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;    

                                        //====== data sekarang =======
                                        sscanf($taim, "%d:%d:%d", $hours1, $minutes1, $seconds1);
                                        $time_second21 = isset($seconds1) ? $hours1 * 3600 + $minutes1 * 60 + $seconds1 : $hours1 * 60 + $minutes1;    

                                        $ctSum11 = $time_second21 - $time_second11;
                                        $total11 = $total11 + $ctSum11;   
                                        $jml11 = $jml11 + 1;
                                }

                                if($jml11 == 0){$total11 = 0;} else{ $total11 = $total11;}
                                $item['DT'] = (int) $total11;

                            }
                            //======= jika tidak ada downtime ===========
                            else{
                                $item['KodeDT'] = 0;
                                //================ tampilkan total downtime proses 2 ============
                                $sratDT_P2 = "Tanggal = '$tgle' AND Line = '$nile' AND Proses = '$proses' AND Start_Time != '' AND Finish_Time != ''"; 
                                $this->db->where($sratDT_P2);
                                $jmlDT = $this->db->get('andon_downtime');
                                $ctSum = 0; $jml= 0; $total = 0;

                                foreach ($jmlDT->result() as $row3) {
                                        $ctPerPcs = $row3->Start_Time;    
                                        $ct2PerPcs = $row3->Finish_Time; 

                                        //====== data sekarang =======
                                        $ctn1 = date('H:i:s' , strtotime($ctPerPcs)); 
                                        sscanf($ctn1, "%d:%d:%d", $hours, $minutes, $seconds);
                                        $time_second1 = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;    

                                        //====== data terakhir =======
                                        $ctn2 = date('H:i:s' , strtotime($ct2PerPcs)); 
                                        sscanf($ctn2, "%d:%d:%d", $hours1, $minutes1, $seconds1);
                                        $time_second2 = isset($seconds1) ? $hours1 * 3600 + $minutes1 * 60 + $seconds1 : $hours1 * 60 + $minutes1;    

                                        $ctSum = $time_second2 - $time_second1;
                                        $total = $total + $ctSum;   
                                        $jml = $jml + 1;
                                }

                                if($jml == 0){$total = 0;} else{ $total = $total;}
                                $item['DT'] = (int) $total;

                                //================ tampilkan total downtime proses 2.1 ============
                                $srat1DT_P2 = "Tanggal = '$tgle' AND Line = '$nile' AND Proses = '$proses' AND Kode_DT = '1' AND Start_Time != '' AND Finish_Time != ''"; 
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

                                if($jml1 == 0){$total1 = 0;} else{ $total1 = $total1;}
                                $item['K1DT'] = (int)$total1;

                                //================ tampilkan total downtime proses 2.2 ============
                                $srat2DT_P2 = "Tanggal = '$tgle' AND Line = '$nile' AND Proses = '$proses' AND Kode_DT = '2' AND Start_Time != '' AND Finish_Time != ''"; 
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

                                if($jml2 == 0){$total2 = 0;} else{ $total2 = $total2;}
                                $item['K2DT'] = (int)$total2;
                            }

                        }


                    }
                }



            }

            else if($proses == 3){
                $kuncine = "Tanggal = '$tgle' AND Line = '$nile' AND Proses = '$proses'";
                $this->db->where($kuncine);
                $vrAct_P3 = $this->db->get('andon_production')->num_rows();
                $item['Qty_Act'] = $vrAct_P3;

                $kunciNG = "Tanggal = '$tgle' AND Line = '$nile' AND Proses = '$proses' AND Status = '1'";
                $this->db->where($kunciNG);
                $vrNG_P3 = $this->db->get('andon_production')->num_rows();
                $item['Qty_NG'] = $vrNG_P3;
                $vrActTot_P3 = (int)($vrAct_P3 - $vrNG_P3);
    
                $newPros = $proses - 1;
                $kuncine = "Tanggal = '$tgle' AND Line = '$nile' AND Proses = '$newPros' AND Status = '0'";
                $this->db->where($kuncine);
                $this->db->order_by("No_ID", "asc");
                $this->db->limit(1);
                $startPros3 = $this->db->get('andon_production')->row();
                
                //============= proses belum dimulai, nunggu start dari proses 1 =========
                if(!$startPros3){
                    $item['Qty_TargetAct'] = 0;//(int)(($time_seconds1 - $time_seconds2) / ((int)$row->CT_Target * 60));
                    $item['CT_Act'] = 0;
                    $item['ST_Pros'] = 0;
                    $item['DT'] = 0;
                    $item['KodeDT'] = 0;
                    $item['K1DT'] = 0;
                    $item['K2DT'] = 0;                 
                    
                }
                else{


                    if($vrAct_P2 >= $qtyTrgt || (($time_seconds1 >= $time_Brk_Start) && ($time_seconds1 <= $time_Brk_Finish))){
                        //============ program downtime ===========//
                        $lastProd = "Tanggal = '$tgle' AND Line = '$nile' AND Proses = '$proses'"; 
                        $this->db->where($lastProd);
                        $this->db->order_by("No_ID", "desc");
                        $this->db->limit(1);
                        $checklastProd = $this->db->get('andon_production')->row();   

                        $firstProd = "Tanggal = '$tgle' AND Line = '$nile' AND Proses = '$proses'"; 
                        $this->db->where($firstProd);
                        $this->db->order_by("No_ID", "asc");
                        $this->db->limit(1);
                        $checkFirstProd = $this->db->get('andon_production')->row();   

                        sscanf($checklastProd->Push_Time, "%d:%d:%d", $hours, $minutes, $seconds);
                        $time_secondLast = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;    


                        sscanf($checkFirstProd->Push_Time, "%d:%d:%d", $hours1, $minutes1, $seconds1);
                        $time_secondFirst = isset($seconds1) ? $hours1 * 3600 + $minutes1 * 60 + $seconds1 : $hours1 * 60 + $minutes1;    



                        $item['Qty_Act'] = $vrAct_P2;
                        $item['ST_Pros'] = 0;
                        $item['Qty_TargetAct'] = $qtyTrgt;
                        $item['CT_Act'] = (int)(($time_secondLast - $time_secondFirst) / $vrAct_P2);
                        $item['KodeDT'] = 0;

                                //================ tampilkan total downtime proses 2 ============
                                $sratDT_P2 = "Tanggal = '$tgle' AND Line = '$nile' AND Proses = '$proses' AND Start_Time != '' AND Finish_Time != ''"; 
                                $this->db->where($sratDT_P2);
                                $jmlDT = $this->db->get('andon_downtime');
                                $ctSum = 0; $jml= 0; $total = 0;

                                foreach ($jmlDT->result() as $row3) {
                                        $ctPerPcs = $row3->Start_Time;    
                                        $ct2PerPcs = $row3->Finish_Time; 

                                        //====== data sekarang =======
                                        $ctn1 = date('H:i:s' , strtotime($ctPerPcs)); 
                                        sscanf($ctn1, "%d:%d:%d", $hours, $minutes, $seconds);
                                        $time_second1 = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;    

                                        //====== data terakhir =======
                                        $ctn2 = date('H:i:s' , strtotime($ct2PerPcs)); 
                                        sscanf($ctn2, "%d:%d:%d", $hours1, $minutes1, $seconds1);
                                        $time_second2 = isset($seconds1) ? $hours1 * 3600 + $minutes1 * 60 + $seconds1 : $hours1 * 60 + $minutes1;    

                                        $ctSum = $time_second2 - $time_second1;
                                        $total = $total + $ctSum;   
                                        $jml = $jml + 1;
                                }

                                if($jml == 0){$total = 0;} else{ $total = $total;}
                                $item['DT'] = (int) $total;

                                //================ tampilkan total downtime proses 2.1 ============
                                $srat1DT_P2 = "Tanggal = '$tgle' AND Line = '$nile' AND Proses = '$proses' AND Kode_DT = '1' AND Start_Time != '' AND Finish_Time != ''"; 
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

                                if($jml1 == 0){$total1 = 0;} else{ $total1 = $total1;}
                                $item['K1DT'] = (int)$total1;

                                //================ tampilkan total downtime proses 2.2 ============
                                $srat2DT_P2 = "Tanggal = '$tgle' AND Line = '$nile' AND Proses = '$proses' AND Kode_DT = '2' AND Start_Time != '' AND Finish_Time != ''"; 
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

                                if($jml2 == 0){$total2 = 0;} else{ $total2 = $total2;}
                                $item['K2DT'] = (int)$total2;
                    }
                    
                    else{


                        sscanf($taim, "%d:%d:%d", $hours, $minutes, $seconds);
                        $time_seconds1 = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;    
        
                        $ctne1 = $startPros2->Push_Time; 
                        sscanf($ctne1, "%d:%d:%d", $hours, $minutes, $seconds);
                        $time_seconds2 = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;    
                        
                        if($time_seconds1 > $time_Brk_Finish){
                            $qtyTrgtAct = (int)(($time_seconds1 - ($time_seconds2 + ($lmBrk))) / ((int)$row->CT_Target * 60));
                            if($vrAct_P3 == 0){
                                $item['CT_Act'] = ($time_seconds1 - ($time_seconds2 + ($lmBrk))) / 1;
                            }
                            else{
                                $item['CT_Act'] = (int)(($time_seconds1 - ($time_seconds2 + ($lmBrk))) / $vrAct_P3);
                            }

                        }
                        else{
                            $qtyTrgtAct = (int)(($time_seconds1 - $time_seconds2) / ((int)$row->CT_Target * 60));
                            if($vrAct_P3 == 0){
                                $item['CT_Act'] = ($time_seconds1 - $time_seconds2) / 1;
                            }
                            else{
                                $item['CT_Act'] = (int)(($time_seconds1 - $time_seconds2) / $vrAct_P3);
                            }    
                        }

                        $item['Qty_TargetAct'] = $qtyTrgtAct;
                        $item['ST_Pros'] = 1;

                        //======== jika actual Proses 2 >= actual proses 1
                        if($vrAct_P3 >= $vrActTot_P2){
                            $kuncino = "Tanggal = '$tgle' AND Line = '$nile' AND Proses = '$proses'";
                            $this->db->where($kuncino);
                            $this->db->order_by("Push_Time", "desc");
                            $this->db->limit(1);
                            $startDTPros2 = $this->db->get('andon_production')->row();   
                            $startDT = $startDTPros2->Push_Time;
                    
                            $srtDT_P2 = "Tanggal = '$tgle' AND Line = '$nile' AND Proses = '$proses'"; 
                            $this->db->where($srtDT_P2);
                            $this->db->order_by("No_ID", "desc");
                            $this->db->limit(1);
                            $checkDTPros2 = $this->db->get('andon_downtime')->row();   
                            
                            if(!$checkDTPros2){
                                $vrDT_P2 = array('Tanggal' => $tgle, 'Line' => $nile, 'Proses' => $proses, 'Kode_DT' => '1', 'Start_Time' => $startDT,);
                                $this->db->insert('andon_downtime', $vrDT_P2);    
                            }
                            else{
                                if($checkDTPros2->Finish_Time != ''){
                                    $vrDT_P2 = array('Tanggal' => $tgle, 'Line' => $nile, 'Proses' => $proses, 'Kode_DT' => '1', 'Start_Time' => $startDT,);
                                    $this->db->insert('andon_downtime', $vrDT_P2);    
                                }    
                            }

                            sscanf($startDT, "%d:%d:%d", $hours, $minutes, $seconds);
                            $time_seconds3 = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;    
                            $item['DT'] = ($time_seconds1 - $time_seconds3);
                            $item['KodeDT'] = 1;
                            $item['K1DT'] = ($time_seconds1 - $time_seconds3);
                                //================ tampilkan total downtime proses 2.2 ============
                            $srat2DT_P2 = "Tanggal = '$tgle' AND Line = '$nile' AND Proses = '$proses' AND Kode_DT = '2' AND Start_Time != '' AND Finish_Time != ''"; 
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

                            if($jml2 == 0){$total2 = 0;} else{ $total2 = $total2;}
                            $item['K2DT'] = (int)$total2;


                            
                        }

                        //======== Proses Normal =========//
                        else{
                            $kuncino = "Tanggal = '$tgle' AND Line = '$nile' AND Proses = '$newPros'";
                            $this->db->where($kuncino);
                            $this->db->order_by("Push_Time", "desc");
                            $this->db->limit(1);
                            $finishDTPros2 = $this->db->get('andon_production')->row();   
                            $finishDT = $finishDTPros2->Push_Time;

                            $srtDT_P2 = "Tanggal = '$tgle' AND Line = '$nile' AND Proses = '$proses' AND Kode_DT = '1' AND Start_Time != '' AND Finish_Time = ''"; 
                            $vrKrmDT_P2 = array('Finish_Time' => $finishDT,);
                            $this->db->set($vrKrmDT_P2);
                            $this->db->where($srtDT_P2);
                            $this->db->update('andon_downtime');

                            $srt1DT_P2 = "Tanggal = '$tgle' AND Line = '$nile' AND Proses = '$proses' AND Kode_DT = '2' AND Start_Time != '' AND Finish_Time = ''"; 
                            $this->db->where($srt1DT_P2);
                            $kdDT = $this->db->get('andon_downtime');
                            //======= jika ada downtime mechine =========
                            if($kdDT->row()){
                                $item['KodeDT'] = 2;
                                $ctSum11 = 0; $jml11= 0; $total11 = 0;

                                foreach ($kdDT->result() as $row31) {
                                        $ctPerPcs = $row31->Start_Time;    

                                        //====== data mulai =======
                                        $ctn11 = date('H:i:s' , strtotime($ctPerPcs)); 
                                        sscanf($ctn11, "%d:%d:%d", $hours, $minutes, $seconds);
                                        $time_second11 = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;    

                                        //====== data sekarang =======
                                        sscanf($taim, "%d:%d:%d", $hours1, $minutes1, $seconds1);
                                        $time_second21 = isset($seconds1) ? $hours1 * 3600 + $minutes1 * 60 + $seconds1 : $hours1 * 60 + $minutes1;    

                                        $ctSum11 = $time_second21 - $time_second11;
                                        $total11 = $total11 + $ctSum11;   
                                        $jml11 = $jml11 + 1;
                                }

                                if($jml11 == 0){$total11 = 0;} else{ $total11 = $total11;}
                                $item['DT'] = (int) $total11;

                            }
                            //======= jika tidak ada downtime ===========
                            else{
                                $item['KodeDT'] = 0;
                                //================ tampilkan total downtime proses 2 ============
                                $sratDT_P2 = "Tanggal = '$tgle' AND Line = '$nile' AND Proses = '$proses' AND Start_Time != '' AND Finish_Time != ''"; 
                                $this->db->where($sratDT_P2);
                                $jmlDT = $this->db->get('andon_downtime');
                                $ctSum = 0; $jml= 0; $total = 0;

                                foreach ($jmlDT->result() as $row3) {
                                        $ctPerPcs = $row3->Start_Time;    
                                        $ct2PerPcs = $row3->Finish_Time; 

                                        //====== data sekarang =======
                                        $ctn1 = date('H:i:s' , strtotime($ctPerPcs)); 
                                        sscanf($ctn1, "%d:%d:%d", $hours, $minutes, $seconds);
                                        $time_second1 = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;    

                                        //====== data terakhir =======
                                        $ctn2 = date('H:i:s' , strtotime($ct2PerPcs)); 
                                        sscanf($ctn2, "%d:%d:%d", $hours1, $minutes1, $seconds1);
                                        $time_second2 = isset($seconds1) ? $hours1 * 3600 + $minutes1 * 60 + $seconds1 : $hours1 * 60 + $minutes1;    

                                        $ctSum = $time_second2 - $time_second1;
                                        $total = $total + $ctSum;   
                                        $jml = $jml + 1;
                                }

                                if($jml == 0){$total = 0;} else{ $total = $total;}
                                $item['DT'] = (int) $total;

                                //================ tampilkan total downtime proses 2.1 ============
                                $srat1DT_P2 = "Tanggal = '$tgle' AND Line = '$nile' AND Proses = '$proses' AND Kode_DT = '1' AND Start_Time != '' AND Finish_Time != ''"; 
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

                                if($jml1 == 0){$total1 = 0;} else{ $total1 = $total1;}
                                $item['K1DT'] = (int)$total1;

                                //================ tampilkan total downtime proses 2.2 ============
                                $srat2DT_P2 = "Tanggal = '$tgle' AND Line = '$nile' AND Proses = '$proses' AND Kode_DT = '2' AND Start_Time != '' AND Finish_Time != ''"; 
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

                                if($jml2 == 0){$total2 = 0;} else{ $total2 = $total2;}
                                $item['K2DT'] = (int)$total2;
                            }

                        }


                    }





                }



            }

            else{

            }


            $datane[] = $item;    
        }
        return $datane;
    }

    function insertDataAndon($datane, $line, $proses){
        $tgle = date('Y-m-d');
        $taim = date('H:i:s');
        $status="";

        $syrat1 = array(
            'Line' => $line,
            'Status_Proses' => '1',
        );
        $this->db->where($syrat1);
        $kue = $this->db->get('andon_target')->row();
        $qtyTrgt = (int)$kue->Working_Hour / (int)$kue->CT_Target;

        $kunci = "Tanggal = '$tgle' AND Line = '$line' AND Proses = '$proses'";
        $this->db->where($kunci);
        $qtyAct = $this->db->get('andon_production')->num_rows();
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
            $newPros = $proses - 1;
            $kuncine = "Tanggal = '$tgle' AND Line = '$line' AND Proses = '$newPros' AND Status = '0'";
            $this->db->where($kuncine);
            $lastPros = $this->db->get('andon_production')->num_rows();    
            $difQty = (int)$lastPros - (int)$qtyAct;
            if($difQty >= 1 && $qtyAct < $qtyTrgt){
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

    function updateKodeDT($data1, $syrt){
        $this->db->set($data1);
        $this->db->where($syrt);
        $this->db->order_by("No_ID", "desc");    
        $this->db->limit(1);    
        $kueri = $this->db->update('andon_downtime');
        if($kueri){
            $status = "sukses";
        }
        else{
            $status = "gagal";
        }
        return $status;
    }




}