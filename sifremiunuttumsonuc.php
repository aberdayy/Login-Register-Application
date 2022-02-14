<?php
ob_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require "Frameworks/PHPMailer/src/Exception.php"; // dosyayi kurdugunuz alan 
require "Frameworks/PHPMailer/src/PHPMailer.php";// dosyayi kurdugunuz alan 
require "Frameworks/PHPMailer/src/SMTP.php";// dosyayi kurdugunuz alan 
require_once("Ayarlar/ayar.php");// ayar dosyasini kurdugunuz alan Site Email Adresi/Sifresi/HostAdresi gibi degiskenleri cekiyoruz
require_once("Ayarlar/fonksiyonlar.php"); // Guvenlik fonksiyonlarimizi cektigimiz alan 






if (isset($_POST["Email"])) {
    $GelenEmail           =    Guvenlik($_POST["Email"]);
} else {
    $GelenEmail           =    "";
}



if (($GelenEmail != "")){


    $KontrolSorgusu     = $DbConnect->prepare("SELECT * FROM uyeler WHERE EmailAdresi = ?");
    $KontrolSorgusu->execute([$GelenEmail]);
    $KontrolSayisi         = $KontrolSorgusu->rowCount();
    $KullaniciKaydi         = $KontrolSorgusu->fetch(PDO::FETCH_ASSOC);

    if ($KontrolSayisi > 0) {


            $MailIcerigiHazirla     =   "Merhaba Sayin " . $KullaniciKaydi["IsimSoyisim"] . "<br /><br /> Sitemizden olsuturdugunuz sifre sifirlama talebi tarafimiza ulasmistir. Lutfen sifrenizi asagidaki baglantidan yenileyiniz. 
            <a href='" . $SiteLinki . "/index.php?SK=43&AktivasyonKodu=" . $KullaniciKaydi["AktivasyonKodu"] . "&Email=" . $KullaniciKaydi["EmailAdresi"] . "'>BURAYA TIKLAYINIZ.</a><br /><br />Saygilarimizla, Iyi Gunler...<br />" . $SiteAdi;

            $mail = new PHPMailer(true);

            try {
                $mail->SMTPDebug            = 0;                                                                               //Enable verbose debug output
                $mail->isSMTP();                                                                                    //Send using SMTP
                $mail->Host                 = DonusumleriGeriDondur($SiteEmailHostAdresi);                                    //Set the SMTP server to send through
                $mail->SMTPAuth             = true;                                                                           //Enable SMTP authentication
                $mail->CharSet              = "UTF-8";                                                                         //Enable SMTP authentication
                $mail->Username             = DonusumleriGeriDondur($SiteEmailAdresi);                                        //SMTP username
                $mail->Password             = DonusumleriGeriDondur($SiteEmailSifresi);                                       //SMTP password
                $mail->SMTPSecure           = 'tls';                                                                          //Enable implicit TLS encryption
                $mail->Port                 = 587;                                                                            //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
                $mail->SMTPOptions          = array(
                    'ssl' => array(
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true,
                    )
                );
                $mail->setFrom(DonusumleriGeriDondur($SiteEmailAdresi), $SiteAdi);
                $mail->addAddress(DonusumleriGeriDondur($KullaniciKaydi["EmailAdresi"]), $KullaniciKaydi["IsimSoyisim"]);                               //Add a recipient
                $mail->addReplyTo(DonusumleriGeriDondur($SiteEmailAdresi), $SiteAdi);
                $mail->isHTML(true);                                                                                //Set email format to HTML
                $mail->Subject =  DonusumleriGeriDondur($SiteAdi) . " Sifre Sifirlama Talebi";
                $mail->msgHTML($MailIcerigiHazirla);
                $mail->send();
                header("Location:xxxxxxxxx"); // mail gonderildi
            } catch (Exception $e) {
                header("Location:xxxxxx"); // mail gonderilemedi
                exit();
            }


    } else {
        header("Location:xxxx"); // Email Adresi eslesmiyor
        exit();
    }
}else {
    header("Location:xxxxxx"); // Gelen email mevcut degil
    exit();
}
