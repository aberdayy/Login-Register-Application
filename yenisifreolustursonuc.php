<?php
ob_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require "Frameworks/PHPMailer/src/Exception.php"; // dosyayi kurdugunuz alan 
require "Frameworks/PHPMailer/src/PHPMailer.php";// dosyayi kurdugunuz alan 
require "Frameworks/PHPMailer/src/SMTP.php";// dosyayi kurdugunuz alan 
require_once("Ayarlar/ayar.php");// ayar dosyasini kurdugunuz alan Site Email Adresi/Sifresi/HostAdresi gibi degiskenleri cekiyoruz
require_once("Ayarlar/fonksiyonlar.php"); // Guvenlik fonksiyonlarimizi cektigimiz alan 


if (isset($_GET["EmailAdresi"])) {
    $GelenEmail         =    Guvenlik($_GET["EmailAdresi"]);
} else {
    $GelenEmail         =    "";
}
if (isset($_GET["AktivasyonKodu"])) {
    $GelenAktivasyonKodu         =    Guvenlik($_GET["AktivasyonKodu"]);
} else {
    $GelenAktivasyonKodu         =    "";
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

$MD5liSifre =   md5($GelenSifre);


if (($GelenEmail != "") or ($GelenAktivasyonKodu != "") and ($GelenSifre != "") and ($GelenSifreTekrar != "")) {

        if ($GelenSifre != $GelenSifreTekrar) {
            header("Location:xxxxx");// sifreler eslesmiyor
            exit();
        } else {
            $UyeGuncellemeSorgusu     = $DbConnect->prepare("UPDATE uyeler SET Sifre = ? WHERE EmailAdresi = ? OR AktivasyonKodu = ? LIMIT 1");
            $UyeGuncellemeSorgusu->execute([$MD5liSifre, $GelenEmail, $GelenAktivasyonKodu]);
            $KayitKontrol         = $UyeGuncellemeSorgusu->rowCount();
    
            if ($KayitKontrol > 0) {
                header("Location:xxxxxxxx"); // sifre guncelleme basarili   
                exit();
    
            } else {
                header("Location:xxxxxxx"); // Guncelleme basarili degil
                exit();
            }

        }
} else {
    header("Location:xxxxxxxx"); // gelen degerler eksik
    exit();
}
