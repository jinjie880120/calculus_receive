<?php

function decode_text_string($text)
{
    $decode_elements = imap_mime_header_decode($text);
    $combination = "";    
    for($i = 0; $i < count($decode_elements); $i++){
        $combination .= $decode_elements[$i]->text;
    }
    return $combination;
}

function dirToArray($dir) 
{  
    $result = array(); 
    $cdir = scandir($dir);
    foreach($cdir as $key => $value)
    {
        if(!in_array($value,array(".","..")))
        {
            $result[] = $value;
        }
    }
   
    return $result;
}

function catch_student_id($text)
{
    $result = explode("-", $text);
    $student_id = $result[3];
    return $student_id;
}

function compare_subject($text)
{
    $result = explode("-", $text);
    $c_subject = "Limits&Continuity";
    $mail_subject = $result[2];
    if($c_subject != $mail_subject)
    {
        return false;
    }
    else
    {
        return true;
    }
}

/* connect to gmail */
$hostname = '{imap.gmail.com:993/imap/ssl}INBOX';
$username = 'a1063321@mail.nuk.edu.tw';
$password = 's124069936';

/* try to connect */
$inbox = imap_open($hostname,$username,$password) or die('Cannot connect to Gmail: ' . imap_last_error());

/* grab emails */
$emails = imap_search($inbox,'SUBJECT "Calculus-HW" SINCE "5 March 2021"');

/*get all files name */
// $dir = "/home/orange/Calculus_student/hw1/";
// $result_dir = dirToArray($dir);

/* if emails are returned, cycle through each... */
if($emails) {
	
	/* begin output var */
	$output = '';
	
	/* put the newest emails on top */
	rsort($emails);
	
	/* for every email... */
    // $overview = imap_fetch_overview($inbox, 0, 0);
    // $message = imap_fetchbody($inbox, 0, 2);

    // echo $overview;
    /* create student_id array */
    $student_id = array();


	foreach($emails as $email_number)
    {
		
		/* get information specific to this email */
		$overview = imap_fetch_overview($inbox,$email_number,0);
		$message = imap_fetchbody($inbox,$email_number,2);
        
        // print_r($overview);
        $subject = $overview[0]->subject;
        $subject = decode_text_string($subject);
        echo "信件標題:". $subject;
        
        echo "<br>";
        /* if any attachments found... */
        $structure = imap_fetchstructure($inbox, $email_number);
        $attachments = array();

        if(isset($structure->parts) && count($structure->parts))
        {
            for($i = 0; $i < count($structure->parts); $i++)
            {
                $attachments[$i] = array(
                    'is_attachment' => false,
                    'filename' => '',
                    'name' => '',
                    'attachment' => ''
                );

                if($structure->parts[$i]->ifdparameters) 
                {
                    foreach($structure->parts[$i]->dparameters as $object) 
                    {
                        if(strtolower($object->attribute) == 'filename') 
                        {
                            $attachments[$i]['is_attachment'] = true;
                            $attachments[$i]['filename'] = $object->value;
                        }
                        if($structure->parts[$i]->ifparameters) 
                        {
                            foreach($structure->parts[$i]->parameters as $object) 
                            {
                                if(strtolower($object->attribute) == 'name') 
                                {
                                    $attachments[$i]['is_attachment'] = true;
                                    $attachments[$i]['name'] = $object->value;
                                }
                            }
                        }

                        if($attachments[$i]['is_attachment']) 
                        {
                            $attachments[$i]['attachment'] = imap_fetchbody($inbox, $email_number, $i+1);

                            /* 3 = BASE64 encoding */
                            if($structure->parts[$i]->encoding == 3) 
                            { 
                                $attachments[$i]['attachment'] = base64_decode($attachments[$i]['attachment']);
                            }
                            /* 4 = QUOTED-PRINTABLE encoding */
                            elseif($structure->parts[$i]->encoding == 4) 
                            { 
                                $attachments[$i]['attachment'] = quoted_printable_decode($attachments[$i]['attachment']);
                            }
                        }
                    }
                }
            }
        }
        $correct_subject = compare_subject($subject);
        $check_num = 0;
        /* download files to dir */
        if($correct_subject)
        {
            foreach($attachments as $attachment)
            {
                if($attachment['is_attachment'] == 1)
                {
                    $filename = $attachment['name'];
                    if(empty($filename)) $filename = $attachment['filename'];
                    $filename = decode_text_string($filename);
                    echo "偵測到檔案: ".$filename."<br>";
                    $this_student_id = catch_student_id($subject);
                    echo "檢查學號".$this_student_id."是否已經存在<br>";
                    
                    if(in_array($this_student_id, $student_id))
                    {
                        $check_num -= 1;
                        if($check_num < 0)
                        {
                            echo "檢查不通過,學號已經存在<br>";
                        }
                        else
                        {
                            echo "檢查通過, 準備下載檔案<br>";
                        }
                        
                    }
                    else
                    {
                        $check_num += 1;
                        array_push($student_id, $this_student_id);
                        echo "檢查通過, 準備下載檔案<br>";
                    }
                    if($check_num >= 0)
                    {
                        $download_dir = "Calculus_student/hw2/";
                        if(!is_dir($download_dir))
                        {
                            mkdir($download_dir,0777,true);
                        }
                        $fattach = fopen("./".$download_dir.$filename, "w+");
                        fwrite($fattach, $attachment['attachment']);
                        fclose($fattach);
                        echo "下載完畢<br>";
                    }
                
                    // $fp = fopen("./". $folder ."/". $email_number . "-" . $filename, "w+");
                    // fwrite($fp, $attachment['attachment']);
                    // fclose($fp);
                }
            }
        }
         

		// /* output the email header information */
		// $output.= '<div class="toggler '.($overview[0]->seen ? 'read' : 'unread').'">';
		// $output.= '<span class="subject">'.$overview[0]->subject.'</span> ';
		// $output.= '<span class="from">'.$overview[0]->from.'</span>';
		// $output.= '<span class="date">on '.$overview[0]->date.'</span>';
		// $output.= '</div>';
		
		// /* output the email body */
		// $output.= '<div class="body">'.$message.'</div>';
        
        echo "<br>-----------------------------------------------------<br>";
        
	}

	// echo imap_utf8($output);
} 

/* close the connection */
imap_close($inbox);


?>