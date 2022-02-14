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
if (isset($_POST["SifreTekrar"])) {
    $GelenSifreTekrar         =    Guvenlik($_POST["SifreTekrar"]);
} else {
    $GelenSifreTekrar         =    "";
}

if (isset($_POST["IsimSoyisim"])) {
    $GelenIsimSoyisim     =    Guvenlik($_POST["IsimSoyisim"]);
} else {
    $GelenIsimSoyisim     =    "";
}

if (isset($_POST["Telefon"])) {
    $GelenTelefon         =    Guvenlik($_POST["Telefon"]);
} else {
    $GelenTelefon         =    "";
}


if (isset($_POST["Cinsiyet"])) {
    $GelenCinsiyet           =    Guvenlik($_POST["Cinsiyet"]);
} else {
    $GelenCinsiyet           =    "";
}


if (isset($_POST["SozlesmeOnay"])) {
    $GelenSozlesmeOnay           =    Guvenlik($_POST["SozlesmeOnay"]);
} else {
    $GelenSozlesmeOnay           =    "";
}
$AktivasyonKodu         =   AktivasyonKoduUret(); // Database uzerinde sakladigimiz ve kullaniciya gonderecek oldugumuz mailden get yontemiyle aktivasyon durumunu degistirecegimiz aktivasyon kodu.
$MD5liSifre =   md5($GelenSifre);


if (($GelenEmail != "") and ($GelenSifre != "") and ($GelenSifreTekrar != "") and ($GelenIsimSoyisim != "") and ($GelenTelefon != "") and ($GelenCinsiyet != "")) {
    if ($GelenSozlesmeOnay == 0) {
        header("Location:xxxxxxx"); // ONAYSIZ SOZLESME
        exit();
    } else {
        if ($GelenSifre != $GelenSifreTekrar) {
            header("Location:xxxxxxxxxx");// UYUSMAYAN SIFRE
            exit();
        } else {
            $KontrolSorgusu     = $DbConnect->prepare("SELECT * FROM uyeler WHERE EmailAdresi = ?");
            $KontrolSorgusu->execute([$GelenEmail]);
            $KontrolSayisi         = $KontrolSorgusu->rowCount();
            if ($KontrolSayisi > 0) {
                header("Location:xxxxxxxx"); // TEKRARLANAN UYE KAYDI
                exit();
            } else {

                $UyeEklemeSorgusu     = $DbConnect->prepare("INSERT INTO uyeler (EmailAdresi, Sifre, IsimSoyisim, TelefonNumarasi, Cinsiyet, Durumu, KayitTarihi, KayitIpAdresi, AktivasyonKodu) values (?, ?, ?, ?, ?, ?, ?, ?, ?) ");
                $UyeEklemeSorgusu->execute([$GelenEmail, $MD5liSifre, $GelenIsimSoyisim, $GelenTelefon, $GelenCinsiyet, 0, $ZamanDamgasi, $IPAdresi, $AktivasyonKodu]);
                $KayitKontrol         = $UyeEklemeSorgusu->rowCount();
                if ($KayitKontrol > 0) {
                    $mail = new PHPMailer(true);
                    $MailIcerigiHazirla     =   "Merhaba Sayin " . $GelenIsimSoyisim . "<br /><br /> Sitemize yapmis oldugunuz uyelik kaydini tamamlamak icin lutfen 
                    <a href='" . $SiteLinki . "/aktivasyon.php?AktivasyonKodu=" . $AktivasyonKodu . "&Email=" . $GelenEmail . "'>BURAYA TIKLAYINIZ.</a><br /><br />Saygilarimizla, Iyi Gunler...<br />" . $SiteAdi;

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
                        $mail->addAddress(DonusumleriGeriDondur($GelenEmail), $SiteAdi);                               //Add a recipient
                        $mail->addReplyTo(DonusumleriGeriDondur($SiteEmailAdresi), $SiteAdi);
                        $mail->isHTML(true);                                                                                //Set email format to HTML
                        $mail->Subject =  DonusumleriGeriDondur($SiteAdi) . " Aktivasyon Kodu";
                        $mail->msgHTML($MailIcerigiHazirla);
                        $mail->send();
                        header("Location:xxxxxxxxxxx"); // Mail gonderildi
                    } catch (Exception $e) {
                            echo $e->getMessage(); // mail gonderilemedi
                        exit();
                    }

                    header("Location:xxxxxxxxxx"); //islem basarili
                    exit();
                } else {
                    header("Location:xxxxxxxxxx"); //islem basarisiz
                    exit();
                }
            }
        }
    }
} else {
    header("Location:xxxxxxxxx"); // formdaki veriler eksik
    exit();
}
