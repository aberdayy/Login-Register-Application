<?php
if (isset($_GET["AktivasyonKodu"])) { //Kullaniciya gonderilen mailde link yapisi uzerinden gelen aktivasyon kodu. Bu kod veritabanimzidada mevcut
    $GelenAktivasyonKodu           =    Guvenlik($_GET["AktivasyonKodu"]);
} else {
    $GelenAktivasyonKodu           =    "";
}

if (isset($_GET["Email"])) {
    $GelenEmail         =    Guvenlik($_GET["Email"]);
} else {
    $GelenEmail         =    "";
}


if (($GelenAktivasyonKodu != "") and ($GelenEmail != "")) {


    $KontrolSorgusu     = $DbConnect->prepare("SELECT * FROM uyeler WHERE EmailAdresi = ? AND AktivasyonKodu = ?");
    $KontrolSorgusu->execute([$GelenEmail, $GelenAktivasyonKodu]);
    $KontrolSayisi         = $KontrolSorgusu->rowCount();
    $KullaniciKaydi         = $KontrolSorgusu->fetch(PDO::FETCH_ASSOC);

    if ($KontrolSayisi > 0) {
        
?>
<td align="center">
    <table width="1065" cellspacing="0" cellpadding="0" border="0" align="center">
        <tbody>
            <tr>
                <td width="500" valign="top">
                    <form action="index.php?YYY=xxx&AktivasyonKodu=<?php echo $KullaniciKaydi["AktivasyonKodu"]; ?>&Email="<?php echo $KullaniciKaydi["EmailAdresi"]; ?> method="post">
                        <table width="500" cellspacing="0" cellpadding="0" border="0" align="center">
                            <tr height="50">
                                <td style="color:#927c59">
                                    <h3 style="border-bottom: 1px dashed #CCCCCC;">Sifre Sifirlama</h3>
                                </td>
                            </tr>
                            <!-- Sol taraf form alani -->

                            <tr height="40">
                                <td>
                                    Yeni Sifre (*)
                                </td>
                            </tr>
                            <tr height="40">
                                <td>
                                    <input class="inputalanlari" type="password" name="Sifre">
                                </td>
                            </tr>
                            <td>
                                Yeni Sifre Tekrar(*)
                            </td>
                            <tr height="40">
                                <td>
                                    <input class="inputalanlari" type="password" name="SifreTekrar">
                                </td>
                            </tr>
                           
                            <tr height="40">
                                <td><input style="height:auto; width:485px; margin-top: 25px;" class="SubmitGeneral" type="submit" value="Sifremi Guncelle"></td>
                            </tr>
                        </table>
                    </form>
                </td>

                <!-- UI icin orta bosluk -->
                <td width="20">
                </td>

                <!-- sag taraf sistem bilgisi -->
                <td width="500" valign="top">
                    <table width="500" cellspacing="0" cellpadding="0" border="0" align="center">
                        <tbody>
                            <tr height="50">
                                <td style="color:#927c59" colspan="2">
                                    <h3 style="border-bottom: 1px dashed #CCCCCC;">Nasil Calisir?</h3>
                                </td>
                            </tr>

                            <tr height="30">
                                <td width="30" align="left"><img src="Resimler/Bankalar/CarklarSiyah20x20.png" style="margin-top: 5px; " border="0"> </td>
                                <td align="left">Bilgi Kontrolu</td>
                            </tr>

                            <tr height="30">
                                <td colspan="2" align="left">Kullanicinin form alanina girmis oldugu deger veya degerler veritabaninimizda tam detayli olarak filtrelenerek kontrol edilir.</td>
                            </tr>

                            <tr>
                                <td height="30"></td>
                            </tr>

                            <tr height="30">
                                <td width="30" align="left"><img src="Resimler/Bankalar/CarklarSiyah20x20.png" style="margin-top: 5px; " border="0"> </td>
                                <td align="left">Email Gonderimi ve Icerik</td>
                            </tr>
                            <tr height="30">
                                <td colspan="2" align="left">Bilgi kontrolu basarili olur ise, kullanicinin veritabanimizda kayitli olan email adresine yeni sifre olusturma icerikli bir mail gonderilir.</td>
                            </tr>

                            <tr>
                                <td height="30"></td>
                            </tr>
                            <tr height="30">
                                <td width="30" align="left"><img src="Resimler/Bankalar/CarklarSiyah20x20.png" style="margin-top: 5px; " border="0"> </td>
                                <td align="left">Sifre sifirlama & Olusturma</td>
                            </tr>
                            <tr height="30">
                                <td colspan="2" align="left">Kullanici kendisine iletilen mail icerisindeki 'yeni sifre olustur' metinine tiklayacak olur ise site yeni sifresi olusturma sayfasi acilir ve kullanicidan
                                    yeni hesap sifresini olusturmasi istenir.
                                </td>
                            </tr>

                            <tr>
                                <td height="30"></td>
                            </tr>

                            <tr height="30">
                                <td width="30" align="left"><img src="Resimler/Bankalar/CarklarSiyah20x20.png" style="margin-top: 5px; " border="0"> </td>
                                <td align="left">Sonuc</td>
                            </tr>
                            <tr height="30">
                                <td colspan="2" align="left">Kullanici yeni olusturmus oldugu hesap sifresi ile siteye giris yapmaya hazirdir.</td>
                            </tr>
                        </tbody>
                    </table>
                    <br>
                </td>
            </tr>
        </tbody>
    </table>
</td>
<?php 
    }else{
        header("Location:xxxxx"); // Gelen bilgiler eslesmiyor kritik
        exit();
    }
}else{
    header("Location:xxxxxxx"); // Aktivasyon kodu veya email mevcut degil kritik
    exit();

}
?>