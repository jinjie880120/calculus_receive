<?php

require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
$subject = "測試測試";


$mail= new PHPMailer(true);
$mail->IsSMTP(); //設定使用SMTP方式寄信
$mail->SMTPAuth = true; //設定SMTP需要驗證
$mail->SMTPSecure = "ssl"; // Gmail的SMTP主機需要使用SSL連線
$mail->Host = "smtp.gmail.com"; //Gamil的SMTP主機
$mail->Port = 465; //Gamil的SMTP主機的埠號(Gmail為465)。
$mail->CharSet = "utf-8"; //郵件編碼
$mail->Username = "a1063321@mail.nuk.edu.tw"; //Gamil帳號
$mail->Password = "s124069936"; //Gmail密碼
$mail->From = "a1063321@mail.nuk.edu.tw"; //寄件者信箱
$mail->FromName = "劉晉杰"; //寄件者姓名
$mail->Subject = $subject; //郵件標題
$mail->Body = "This is test email!!!"; //郵件內容
$mail->IsHTML(true); //郵件內容為html
$mail->AddAddress("orange0010612@gmail.com"); //收件者郵件及名稱
$mail->AddReplyTo("orange0010612@gmail.com","For auto reply");
if(!$mail->Send()){ echo "Error: " . $mail->ErrorInfo; }else{ echo "發信成功"; }



?>