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

if (isset($_POST["Sifre"])) {
    $GelenSifre         =    Guvenlik($_POST["Sifre"]);
} else {
    $GelenSifre         =    "";
}
$MD5liSifre =   md5($GelenSifre);


if (($GelenEmail != "") and ($GelenSifre != "")) {


    $KontrolSorgusu     = $DbConnect->prepare("SELECT * FROM uyeler WHERE EmailAdresi = ? AND Sifre = ?");
    $KontrolSorgusu->execute([$GelenEmail, $MD5liSifre]);
    $KontrolSayisi         = $KontrolSorgusu->rowCount();
    $KullaniciKaydi         = $KontrolSorgusu->fetch(PDO::FETCH_ASSOC);

    if ($KontrolSayisi > 0) {
        if ($KullaniciKaydi["Durumu"] == 1) { //Database uzerinden uye aktivasyon durumu kontrolu 
            $_SESSION["Kullanici"]  =   $GelenEmail;
            if ($_SESSION["Kullanici"] == $GelenEmail) {

                header("Location:xxxxxx"); // Giris basarili
                exit();
            } else {
                header("Location:xxxxxxxxx"); //session hatasi
                exit();
            }
        } else {

            $MailIcerigiHazirla     =   "Merhaba Sayin " . $KullaniciKaydi["IsimSoyisim"] . "<br /><br /> Sitemize yapmis oldugunuz uyelik kaydini Tamamlamadiginiz icin bu maili tekrar gonderdik lutfen 
            <a href='" . $SiteLinki . "/aktivasyon.php?AktivasyonKodu=" . $KullaniciKaydi["AktivasyonKodu"] . "&Email=" . $KullaniciKaydi["EmailAdresi"] . "'>BURAYA TIKLAYINIZ.</a><br /><br />Saygilarimizla, Iyi Gunler...<br />" . $SiteAdi;

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
                $mail->Subject =  DonusumleriGeriDondur($SiteAdi) . " Aktivasyon Kodu";
                $mail->msgHTML($MailIcerigiHazirla);
                $mail->send();
                header("Location:xxxxx"); // Mail Gonderildi
            } catch (Exception $e) {
                header("Location:xxxxx"); // Mail Gonderilemedi
                exit();
            }
        }
    } else {
        header("Location:xxxxxxx"); // Yanlis Veri girisi
        exit();
    }
} else {
    header("Location:xxxxxxxxx"); // Bos deger
    exit();
}
