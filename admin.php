<? // WR-foto v 1.2  //  02.08.15 �.  //  Miha-ingener@yandex.ru

error_reporting (E_ALL); //error_reporting(0);
ini_set('register_globals','off');// ��� ������� �������� ��� ���� ��������� php

include "data/config.php";

$skey="657567"; // !!! ��������� ���� !!! 
// ��������� �� ���� � ��� ��� ������� ������� :-)
// !!! ����� ����� - ������ �������������� � ���������� ���������� ����������!
// ��� ��������� ������ ������ �������������� ������ �������� 89:
//$qq=md5("$pass+$skey"); print"$qq"; exit; // ������������� ��� ��������� MD5 ������ ������!�
// �������� ���������� ��� � config.php � ���������� $password � $moderpass

// �����������
$adminname="admin|moder|"; // �� ������� ���������!!! ��� ��������. ��� �������������� � ����� ���� | ��� ���������� � � ����� |
$adminpass=$password;


function replacer ($text) { // ������� ������� ����
$text=str_replace("&#032;",' ',$text);
$text=str_replace(">",'&gt;',$text);
$text=str_replace("<",'&lt;',$text);
$text=str_replace("\"",'&quot;',$text);
$text=preg_replace("/\n\n/",'<p>',$text);
$text=preg_replace("/\n/",'<br>',$text);
$text=preg_replace("/\\\$/",'&#036;',$text);
$text=preg_replace("/\r/",'',$text);
$text=preg_replace("/\\\/",'&#092;',$text);
// ���� magic_quotes �������� - ������ ����� ����� � ���� �������: ��������� (') � ������� ������� ("), �������� ���� (\)
if (get_magic_quotes_gpc()) { $text=str_replace("&#092;&quot;",'&quot;',$text); $text=str_replace("&#092;'",'\'',$text); $text=str_replace("&#092;&#092;",'&#092;',$text); }
$text=str_replace("\r\n","<br> ",$text);
$text=str_replace("\n\n",'<p> ',$text);
$text=str_replace("\n",'<br> ',$text);
$text=str_replace("\t",'',$text);
$text=str_replace("\r",'',$text);
$text=str_replace('   ',' ',$text);
return $text; }


function unreplacer ($text) { // ������� ������ ������������ ����� ������ �� �������
$text=str_replace("&lt;br&gt;","<br>",$text);
$text=str_replace("&#124;","|",$text);
return $text;}


function nospam() { global $max_key,$rand_key; // ������� ��������
if (array_key_exists("image", $_REQUEST)) { $num=replacer($_REQUEST["image"]);
for ($i=0; $i<10; $i++) {if (md5("$i+$rand_key")==$num) {imgwr($st,$i); die();}} }
$xkey=""; mt_srand(time()+(double)microtime()*1000000);
$dopkod=mktime(0,0,0,date("m"),date("d"),date("Y")); // ���.���: �������� ������ 24 ����
$stime=md5("$dopkod+$rand_key");// ���.���
echo'�������� ���: ';
for ($i=0; $i<$max_key; $i++) {
$snum[$i]=mt_rand(0,9); $psnum=md5($snum[$i]+$rand_key+$dopkod);
echo "<img src=antispam.php?image=$psnum border='0' alt=''>\n";
$xkey=$xkey.$snum[$i];}
$xkey=md5("$xkey+$rand_key+$dopkod"); //����� + ���� �� config.php + ��� ���������� ����� 24 ����
print" <input name='usernum' class=post type='text' style='WIDTH: 70px;' maxlength=$max_key size=6>
<input name=xkey type=hidden value='$xkey'>
<input name=stime type=hidden value='$stime'>";
return; }


// ������ ����� - ������� ����
if(isset($_GET['event'])) { if ($_GET['event']=="clearcooke") { setcookie("wrforumm","",time()-3600); Header("Location: index.php"); exit; } }

if (isset($_COOKIE['wrforumm'])) { // ������� ���/������ �� ���� � �������� � ������ �����
$text=$_COOKIE['wrforumm'];
$text=trim($text); // �������� ���������� ������� 
if (strlen($text)>60) exit("������� ������ - ����� ���������� ���� ������ �������!");
$text=replacer($text);
$exd=explode("|",$text); $name1=$exd[0]; $pass1=$exd[1];
$adminname=explode("|",$adminname);

if ($name1!=$adminname[0] and $name1!=$adminname[1] or $pass1!=$adminpass) 
{sleep(1); setcookie("wrforumm", "0", time()-3600); Header("Location: admin.php"); exit;} // ������� �������� ����!!!

} else { // ���� ���� ���� ����

if (isset($_POST['name']) & isset($_POST['pass'])) { // ���� ���� ���������� �� ����� ����� ������
$name=str_replace("|","I",$_POST['name']); $pass=str_replace("|","I",$_POST['pass']);
$text="$name|$pass|";
$text=trim($text); // �������� ���������� ������� 
if (strlen($text)<4) exit("$back �� �� ����� ��� ��� ������!");
$text=replacer($text);
$exd=explode("|",$text); $name=$exd[0]; $pass=$exd[1];

//$qq=md5("$pass+$skey"); print"$qq"; exit; // ������������� ��� ��������� MD5 ������ ������!

//--�-�-�-�-�-�-�-�--�������� ����--
if ($antispam==TRUE) {
if (!isset($_POST['usernum']) or !isset($_POST['xkey']) or !isset($_POST['stime']) ) exit("������ �� ����� �� ���������!");
$usernum=replacer($_POST['usernum']); $xkey=replacer($_POST['xkey']); $stime=replacer($_POST['stime']);
$dopkod=mktime(0,0,0,date("m"),date("d"),date("Y")); // ���.���. �������� ������ 24 ����
$usertime=md5("$dopkod+$rand_key");// ���.���
$userkey=md5("$usernum+$rand_key+$dopkod");
if (($usertime!=$stime) or ($userkey!=$xkey)) exit("����� ��������� ���!");}


// ������� �������� ���/������ � �������� � ������ �����
$adminname=explode("|",$adminname);
// �������������� ������������� ����
if ($name==$adminname[0] & md5("$pass+$skey")==$adminpass) 
{$tektime=time(); $wrforumm="$adminname[0]|$adminpass|$tektime|";
setcookie("wrforumm", $wrforumm, time()+18000); Header("Location: admin.php"); exit;}
// ���������� ������������� ����
if ($name==$adminname[1] & md5("$pass+$skey")==$moderpass) 
{$tektime=time(); $wrforumm="$adminname[1]|$adminpass|$tektime|";
setcookie("wrforumm", $wrforumm, time()+18000); Header("Location: admin.php"); exit;}

exit("���� ������ <B>��������</B>!</center>");

} else { // ���� ���� ������, �� ������� ����� ����� ������

echo "<html><head><META HTTP-EQUIV='Pragma' CONTENT='no-cache'><META HTTP-EQUIV='Cache-Control' CONTENT='no-cache'><META content='text/html; charset=windows-1251' http-equiv=Content-Type><style>input, textarea {font-family:Verdana; font-size:12px; text-decoration:none; color:#000000; cursor:default; background-color:#FFFFFF; border-style:solid; border-width:1px; border-color:#000000;}</style></head><body>
<BR><BR><BR><center>
<table border=#C0C0C0 border=1  cellpadding=3 cellspacing=0 bordercolor=#959595>
<form action='admin.php' method=POST name=pswrd>
<TR><TD bgcolor=#C0C0C0 align=center>����������������� ������</TD></TR>
<TR><TD align=right>������� �����: <input size=17 name=name value=''></TD></TR>
<TR><TD align=right>������� ������: <input type=password size=17 name=pass></TD></TR>
<TR><TD align=right>";

if ($antispam==TRUE) nospam(); // �������� !

print"<TR><TD align=center><input type=submit style='WIDTH: 120px; height:20px;' value='�����'>
<SCRIPT language=JavaScript>document.pswrd.name.focus();</SCRIPT></TD></TR></table>
<BR><BR><center><small>Powered by <a href=\"http://www.wr-script.ru\" title=\"������ �����������\" class='copyright'>WR-Foto</a> 1.1<br></small></center></body></html>";
exit;}

} // ����������� ��������!

$gbc=$_COOKIE['wrforumm']; $gbc=explode("|", $gbc); $gbname=$gbc[0];$gbpass=$gbc[1];$gbtime=$gbc[2];







// ���������� IP-����� � ���
if (isset($_GET['badip']))  {
if (isset($_POST['ip'])) {$ip=$_POST['ip']; $badtext=$_POST['text'];}
if (isset($_GET['ip_get'])) {$ip=$_GET['ip_get']; $badtext="�� ���������� ������������� ��������� �� �����! �� ����!!!";}
if (strlen($ip)<8) exit("������� IP �� ������� X.X.X.X, ��� � - ����� �� 1 �� 255! ������ ������ ���� ��� IP �� ������!");
$text="$ip|$badtext|"; $text=stripslashes($text); $text=htmlspecialchars($text); $text=str_replace("\r\n", "<br>", $text);
$fp=fopen("$datadir/bad_ip.dat","a+");
flock ($fp,LOCK_EX);
fputs($fp,"$text\r\n");
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);
Header("Location: admin.php?event=blockip"); exit; }



// �������� IP-����� �� ����
if (isset($_GET['delip']))  { $xd=$_GET['delip'];
$file=file("$datadir/bad_ip.dat"); $dt=explode("|",$file[$xd]); 
$fp=fopen("$datadir/bad_ip.dat","w");
flock ($fp,LOCK_EX);
for ($i=0;$i< sizeof($file);$i++) { if ($i==$xd) unset($file[$i]); }
fputs($fp, implode("",$file));
flock ($fp,LOCK_UN);
fclose($fp);
Header("Location: admin.php?event=blockip"); exit; }









// ���� �����ר�� ���-�� ��� � ��������� -------------------- �� ��������, ��������!!!!!!!!!!!!!
if(isset($_GET['event'])) { if ($_GET['event'] =="revolushion") {

$lines=file("$datadir/wrfoto.dat");
$countmf=count($lines)-1;
$i="-1";$u=$countmf-1;$k="0";

do {$i++; $dt=explode("|", $lines[$i]);

if (!isset($dt[12])) {$dt[12]=""; $dt[11]="";}

if ($dt[1]!="razdel") {
$fid=$dt[0];
if ((is_file("$datadir/topic$fid.dat")) && (sizeof("$datadir/topic$fid.dat")>0))
{
$fl=file("$datadir/topic$fid.dat");
$kolvotem=count($fl);
$kolvomsg="0";
for ($itf=0; $itf<$kolvotem; $itf++) 
{$forumdt = explode("|", $fl[$itf]);
$cd=$forumdt[7];


$msgfile=file("$datadir/$cd.dat");
$countmsg=count($msgfile); $kolvomsg=$kolvomsg+$countmsg;}
if ($kolvotem=="0") $dt[8]="";
$lines[$i]="$dt[0]|$dt[1]|$dt[2]|$dt[3]|$kolvotem|$kolvomsg|$dt[6]|$dt[7]|$dt[8]|$dt[9]|$dt[10]|$dt[11]|$dt[12]|\r\n";
}

else {$kolvotem="0"; $kolvomsg="0"; $lines[$i]="$dt[0]|$dt[1]|$dt[2]|$dt[3]|$kolvotem|$kolvomsg|$dt[6]|$dt[7]|$dt[8]||$dt[10]|$dt[11]|$dt[12]|\r\n";}
}
else $lines[$i]="$dt[0]|$dt[1]|$dt[2]|\r\n";

} while($i < $countmf);

//print"<PRE>"; print_r($lines); exit();
// ��������� ���������� ������ � ���-�� ��� � ��������� � �����
$file=file("$datadir/wrfoto.dat");
$fp=fopen("$datadir/wrfoto.dat","w");
flock ($fp,LOCK_EX); 
for ($i=0;$i< sizeof($file);$i++) fputs($fp,$lines[$i]);
flock ($fp,LOCK_UN);
fclose($fp);

print "<center><BR><BR><BR>�� ������� �����������.</center><script language='Javascript'><!--
function reload() {location = \"admin.php\"}; setTimeout('reload()', 1000);
--></script>";
exit; }}






// ���� ����������� �����/���� ������� ��� ������
if(isset($_GET['movetopic'])) { if ($_GET['movetopic'] !="") {
$move1=$_GET['movetopic']; $where=$_GET['where']; 
if ($where=="0") $where="-1";
$move2=$move1-$where;
$file=file("$datadir/wrfoto.dat"); $imax=sizeof($file);
if (($move2>=$imax) or ($move2<"0")) exit(" ���� ���� �������!");
$data1=$file[$move1]; $data2=$file[$move2];

$fp=fopen("$datadir/wrfoto.dat","a+");
flock ($fp,LOCK_EX); 
ftruncate ($fp,0);//������� ���������� ����� 
// ������ ������� ��� �������� �������
for ($i=0; $i<$imax; $i++) {if ($move1==$i) fputs($fp,$data2); else  {if ($move2==$i) fputs($fp,$data1); else fputs($fp,$file[$i]);}}
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);
Header("Location: admin.php"); exit; }}




// ���� �������� ���������� ������� ��� ������
if(isset($_GET['fxd'])) {
$id=replacer($_GET['fxd']); if ($id=="" or strlen($id)>3) exit("������, �������� ������� ��� ��������, ���� ������ �������!");

// ��������� ��� ����� � ����� data ���������, ������ ��, ������� ���������� �� $id,
// (����� � ������, ������������ -vote, IP-������� ����������� -ip, topic$id - � ������)
if ($handle=opendir($datadir)) {
while (($file = readdir($handle)) !== false)
if (!is_dir($file)) { 
$tema=substr($file,0,3);
if($tema==$id) unlink ("$datadir/$file");
if($file=="topic$id.dat") unlink ("$datadir/topic$id.dat");
} closedir($handle); } else echo'������!';

// ������� ������, ��������������� ���� � ����� �� ����� ������
$file=file("$datadir/wrfoto.dat");
$fp=fopen("$datadir/wrfoto.dat","w");
flock ($fp,LOCK_EX);
for ($i=0;$i< sizeof($file);$i++) {$dt=explode("|",$file[$i]); if ($dt[0]==$id) unset($file[$i]);}
fputs($fp, implode("",$file));
flock ($fp,LOCK_UN);
fclose($fp);
Header("Location: admin.php"); exit; }









// ���� �������� ��������� ����
if (isset($_GET['xd'])) { if ($_GET['xd'] !="") {
if (isset($_GET['page'])) $page=$_GET['page']; else $page="0";
$xd=$_GET['xd']; $fid=$_GET['fid']; $id=$_GET['id'];
$file=file("$datadir/topic$fid.dat");

$minmsg=1; $delf=null; if (isset($file[$xd])) {
$dt=explode("|", $file[$xd]);
$delf = str_replace("\r\n", "", $dt[7]);
$mlines=file("$datadir/$delf.dat"); $minmsg=count($mlines);
unlink ("$datadir/$delf.dat");} // ������� ���� � �����
if (is_file("$datadir/$delf-vote.dat")) unlink("$datadir/$delf-vote.dat"); // ������� ���� � ������������
if (is_file("$datadir/$delf-ip.dat")) unlink("$datadir/$delf-ip.dat"); // ������� ���� � ������������� IP

// ������� ������, ��������������� ���� � ����� � �������� ������
$fp=fopen("$datadir/topic$fid.dat","w");
$kolvotem=sizeof($file)-1; // ���-�� ��� ��� ��������� �� �������
flock ($fp,LOCK_EX); 
for ($i=0;$i< sizeof($file);$i++) {if ($i==$xd) unset($file[$i]);}
fputs($fp, implode("",$file));
flock ($fp,LOCK_UN);
fclose($fp);


// ���� �������� 1-�� �� ���-�� ��� � �������� ���-�� ���������
$lines = file("$datadir/wrfoto.dat"); $i=count($lines);
// ������� �� fid ����� ������
for ($ii=0;$ii< sizeof($lines);$ii++) {$kdt=explode("|",$lines[$ii]); if ($kdt[0]==$fid) $mnumer=$ii;}
$dt=explode("|",$lines[$mnumer]);
$dt[5]=$dt[5]-$minmsg;
if ($kolvotem=="0") $dt[5]="0";
if ($dt[5]<0) $dt[5]="0";
if ($dt[4]<0) $dt[4]="0";
// ���� ��������� ���� ����� �� ������� ��� ���������, �� ������� � � �������
if ($dt[3]==$delf or $dt[5]==0) {$dt[6]="";$dt[7]="";$dt[8]="";$dt[9]="";$dt[10]="";}
$text="$dt[0]|$dt[1]|$dt[2]|$dt[3]|$kolvotem|$dt[5]|$dt[6]|$dt[7]|$dt[8]|$dt[9]|$dt[10]|$dt[11]|$dt[12]||";
$file=file("$datadir/wrfoto.dat");
$fp=fopen("$datadir/wrfoto.dat","a+");
flock ($fp,LOCK_EX);
ftruncate ($fp,0);
for ($ii=0;$ii< sizeof($file);$ii++) { if ($mnumer!=$ii) fputs($fp,$file[$ii]); else fputs($fp,"$text\r\n"); }
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);


// ������� ���������� �� 10-�� ���������
$file=file("$datadir/news.dat");
$fp=fopen("$datadir/news.dat","w");
flock ($fp,LOCK_EX);
for ($i=0; $i< sizeof($file); $i++) { $dt=explode("|",$file[$i]); if ($dt[1]==$id) unset($file[$i]); }
fputs($fp, implode("",$file));
flock ($fp,LOCK_UN);
fclose($fp);


Header("Location: admin.php?fid=$fid&page=$page"); exit; } }





// ���� �������� ���������� ����
if (isset($_GET['topicxd'])) { if ($_GET['topicxd'] !="") {
$fid=$_GET['fid']; $id=$_GET['id']; $topicxd=$_GET['topicxd']-1;
if (isset($_GET['page'])) $page=$_GET['page']; else $page="1";
$file=file("$datadir/$id.dat");
if (count($file)==1) exit("� ���� ������ �������� ������ <B>���� ���������!</B>");
$fp=fopen("$datadir/$id.dat","w");
flock ($fp,LOCK_EX);
for ($i=0;$i< sizeof($file);$i++) { if ($i==$topicxd) unset($file[$i]); }
fputs($fp, implode("",$file));
flock ($fp,LOCK_UN);
fclose($fp);
$topicxd--;

$file=file("$datadir/$id.dat");
//������������ ������ ���������� ��������� � ����
$dt=explode("|",$file[count($file)-1]); $avtor=$dt[0]; $data=$dt[5]; $time=$dt[6];


// ���� �������� 1-�� �� ���-�� ��������� �� �������
$lines = file("$datadir/wrfoto.dat"); $i=count($lines);
// ������� �� fid ����� ������
for ($ii=0;$ii< sizeof($lines);$ii++) { $kdt=explode("|",$lines[$ii]); if ($kdt[0]==$fid) $mnumer=$ii; }
$dt=explode("|",$lines[$mnumer]);
$dt[5]--; if ($dt[5]<0) $dt[5]="0";
$text="$dt[0]|$dt[1]|$dt[2]|$dt[3]|$dt[4]|$dt[5]|$avtor|$data|$time|$dt[9]|$dt[10]|$dt[11]||$dt[12]||";
$file=file("$datadir/wrfoto.dat");
$fp=fopen("$datadir/wrfoto.dat","a+");
flock ($fp,LOCK_EX);
ftruncate ($fp,0);
for ($ii=0;$ii< sizeof($file);$ii++) { if ($mnumer!=$ii) fputs($fp,$file[$ii]); else fputs($fp,"$text\r\n"); }
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);
Header("Location: admin.php?fid=$fid&id=$id&page=$page#m$topicxd"); exit; } }






// ���������� ������ / �������

if(isset($_GET['event'])) { if ($_GET['event'] =="addmainforum") {
$ftype=$_POST['ftype']; $zag=$_POST['zag']; $msg=$_POST['msg']; $id="101";
if ($zag=="") exit("$back <B>� ������� ���������!</B>");

// ��������� �� ����� � �������� ��������/������� - ���� ���������� � ��������� +1
if (is_file("$datadir/wrfoto.dat")) { $lines=file("$datadir/wrfoto.dat"); 
$imax=count($lines); $i=0;
do {$dt=explode("|", $lines[$i]); if ($id<$dt[0]) {$id=$dt[0];} $i++; } while($i<$imax);
$id++; }
if ($id<101) $id=101; if ($id>999) exit("����� �� ����� ���� ����� 999");
$zag=str_replace("|","I",$zag); $msg=str_replace("|","I",$msg);
if ($ftype=="") $record="$id|$zag|$msg||0|0||$date|$time||||||"; else $record="$id|$ftype|$zag|";
$record=replacer($record);

// ������ ������ ���� � ���������
if ($ftype=="") { $fp=fopen("$datadir/topic$id.dat","a+");
flock ($fp,LOCK_EX); fputs($fp,""); fflush ($fp); flock ($fp,LOCK_UN); fclose($fp); }

// ������ ������ �� ������� ��������
$fp=fopen("$datadir/wrfoto.dat","a+");
flock ($fp,LOCK_EX); fputs($fp,"$record\r\n"); fflush ($fp); flock ($fp,LOCK_UN); fclose($fp);
Header("Location: admin.php"); exit; }







// �������������� ������ / �������
if ($_GET['event'] =="frdmainforum") {
$nextnum=$_POST['nextnum'];
$frd=$_POST['frd'];
$ftype=$_POST['ftype'];
$zag=$_POST['zag'];
if ($zag=="") exit("$back <B>� ������� ���������!</B>");
$zag=str_replace("|","I",$zag);

if ($ftype == "") { $addmax=$_POST['addmax']; $zvezdmax=$_POST['zvezdmax'];
$msg=$_POST['msg'];$idtemka=$_POST['idtemka'];$kt=$_POST['kt'];$km=$_POST['km'];$namem=$_POST['namem'];$datem=$_POST['datem'];$timem=$_POST['timem'];$temka=$_POST['temka'];$timetk=$_POST['timetk'];
$msg=str_replace("|","I",$msg); $msg=str_replace("\r\n", "<br>", $msg);
$txtmf="$nextnum|$zag|$msg|$idtemka|$kt|$km|$namem|$datem|$timem|$timetk|$temka|$addmax|$zvezdmax||";}
else $txtmf="$nextnum|$ftype|$zag|";

$txtmf=htmlspecialchars($txtmf); $txtmf=stripslashes($txtmf); $txtmf=str_replace("\r\n","<br>",$txtmf);

$file=file("$datadir/wrfoto.dat");
$fp=fopen("$datadir/wrfoto.dat","a+");
flock ($fp,LOCK_EX);
ftruncate ($fp,0);//������� ���������� ����� 
for ($i=0;$i< sizeof($file);$i++) { if ($frd!=$i) fputs($fp,$file[$i]); else fputs($fp,"$txtmf\r\n"); }
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);
Header("Location: admin.php"); exit; }




if ($_GET['event']=="rdtema") { // ������� �������������� ����
$rd=$_POST['rd']; // - ����� ������, ������� ���������� ��������
$fid=$_POST['fid']; $changefid=$_POST['changefid'];
if (isset($_GET['page'])) $page=$_GET['page']; else $page="0";

$name=$_POST['name']; $who=$_POST['who']; $email=$_POST['email'];
$zag=$_POST['zag']; $msg=$_POST['msg']; $datem=$_POST['datem'];
$timem=$_POST['timem']; $id=$_POST['id']; $timetk=$_POST['timetk'];
$status=$_POST['status']; $goto=$_POST['goto'];

if ($goto==1) $goto="admin.php?fid=$changefid"; else $goto="admin.php?fid=$fid&page=$page";

if ($zag=="") exit("$back <B>� ������� ����, ��� ������!</B>");
$text="$name|$who|$email|$zag|$msg|$datem|$timem|$id|$status|$timetk|";
$text=htmlspecialchars($text); $text=stripslashes($text); $text=str_replace("\r\n","<br>",$text);


if ($changefid==$fid) { // ���� ������� ������� �����
$file=file("$datadir/topic$fid.dat");
$fp=fopen("$datadir/topic$fid.dat","a+");
flock ($fp,LOCK_EX); 
ftruncate ($fp,0);//������� ���������� ����� 
for ($i=0;$i<sizeof($file);$i++) { if ($rd!=$i) fputs($fp,$file[$i]); else fputs($fp,"$text\r\n"); }
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);

} else { // ���� ������ ������� ����

// $fid - �������, �  $changefid - ��� ����� ��� ������.

// 1. ������ ����� ���� � ����� ������

touch("$datadir/topic$changefid.dat");
$file=file("$datadir/topic$changefid.dat");
$kolvotem1=sizeof($file)+1; // ���-�� ��� ��� ��������� �� �������
$fp=fopen("$datadir/topic$changefid.dat","a+");
flock ($fp,LOCK_EX);
fputs($fp,"$text\r\n");
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);

// 2. ������� ���� � ������� ������

touch("$datadir/topic$fid.dat");
$file=file("$datadir/topic$fid.dat");
$fp=fopen("$datadir/topic$fid.dat","w+");
$kolvotem2=sizeof($file)-1; // ���-�� ��� ��� ��������� �� �������
flock ($fp,LOCK_EX); 
for ($i=0;$i< sizeof($file);$i++) { if ($i==$rd) unset($file[$i]); }
fputs($fp, implode("",$file));
flock ($fp,LOCK_UN);
fclose($fp);

// 3. ��������� �������� �� ���� ��� � ����� ����������

// �������� � ��������� ������!!!
// ��������� ��� ����� ���������� � ����. ������� ������� �� �������,
// ��������������� � ����������� ������ � ����� ������
// � ����������� ��� ������ � ���� wrfoto.dat

// ���� �������� 1-�� �� ���-�� ��� � �������� ���-�� ���������
$file=file("$datadir/$id.dat"); $minmsg=count($file);
$lines=file("$datadir/wrfoto.dat"); $i=count($lines);
// ������� �� $changefid ����� ������

for ($ii=0;$ii< sizeof($lines);$ii++) { $kdt=explode("|",$lines[$ii]); if ($kdt[0]==$fid) $mnumer=$ii; }
$dt=explode("|",$lines[$mnumer]);
$dt[5]=$dt[5]-$minmsg;
if ($kolvotem2=="0") $dt[5]="0";
if ($dt[5]<0) $dt[5]="0";
if ($dt[4]<0) $dt[4]="0";
// ���� ��������� ���� ����� �� ������� ��� ���������, �� ������� � � �������
if ($dt[3]==$id or $dt[5]==0) {$dt[6]="";$dt[7]="";$dt[8]="";$dt[9]="";$dt[10]="";}
$text="$dt[0]|$dt[1]|$dt[2]|$dt[3]|$kolvotem2|$dt[5]|$dt[6]|$dt[7]|$dt[8]|$dt[9]|$dt[10]|$dt[11]|$dt[12]||";
$file=file("$datadir/wrfoto.dat");
$fp=fopen("$datadir/wrfoto.dat","a+");
flock ($fp,LOCK_EX);
ftruncate ($fp,0);
for ($ii=0;$ii< sizeof($file);$ii++)
{ if ($mnumer!=$ii) fputs($fp,$file[$ii]); else fputs($fp,"$text\r\n"); }
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);

// ���� ���������� 1-�� � ���-�� ��� � ��������� ���-�� ���������
for ($ii=0;$ii< sizeof($lines);$ii++) { $kdt=explode("|",$lines[$ii]); if ($kdt[0]==$changefid) $mnumer=$ii; }
$dt=explode("|",$lines[$mnumer]);
$dt[5]=$dt[5]+$minmsg;
if ($kolvotem1=="0") $dt[5]="0";
if ($dt[5]<0) $dt[5]="0";
if ($dt[4]<0) $dt[4]="0";
// ���� ��������� ���� ����� �� ������� ��� ���������, �� ������� � � �������
if ($dt[3]==$id or $dt[5]==0) {$dt[6]="";$dt[7]="";$dt[8]="";$dt[9]="";$dt[10]="";}
$text="$dt[0]|$dt[1]|$dt[2]|$dt[3]|$kolvotem1|$dt[5]|$dt[6]|$dt[7]|$dt[8]|$dt[9]|$dt[10]|$dt[11]||$dt[12]||";
$file=file("$datadir/wrfoto.dat");
$fp=fopen("$datadir/wrfoto.dat","a+");
flock ($fp,LOCK_EX);
ftruncate ($fp,0);
for ($ii=0;$ii< sizeof($file);$ii++)
{ if ($mnumer!=$ii) fputs($fp,$file[$ii]); else fputs($fp,"$text\r\n"); }
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);

// 4. ������� news.dat. ���� ���� ������� ����� (������� ���������� �� 10-�� ���������)

$file=file("$datadir/news.dat");
$fp=fopen("$datadir/news.dat","w");
flock ($fp,LOCK_EX);
for ($i=0; $i< sizeof($file); $i++) {
$dt=explode("|",$file[$i]); if ($dt[1]==$id) unset($file[$i]); }
fputs($fp, implode("",$file));
flock ($fp,LOCK_UN);
fclose($fp);
Header("Location: $goto"); exit; }


// ������� ����� �������� ������� � ������ ������ ����� � �����������
$linesrdt=file("$datadir/$id.dat");
$fp=fopen("$datadir/$id.dat","a+");
flock ($fp,LOCK_EX);
ftruncate ($fp,0);//������� ����������
for ($i=0;$i< sizeof($linesrdt);$i++) {$drdt=explode("|", $linesrdt[$i]); $text1="$drdt[0]|$drdt[1]|$drdt[2]|$zag|$drdt[4]|$drdt[5]|$drdt[6]|$drdt[7]|$drdt[8]|$drdt[9]|"; $text1=str_replace("\r\n", "", $text1); fputs($fp,"$text1\r\n");}
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);
// ���� ����� ��VIP��� ���� - ���������� � ��� 2 ���� � ������������ ���
if ($_POST['viptema']==="1") { $viptime=strtotime("+2 year"); touch("$datadir/$id.dat",$viptime);}
Header("Location: $goto"); exit; }


} // if $event==rdtema












if(isset($_GET['event'])) {


// ������� ����� ��
if ($_GET['event']=="makecopy")  {
if (is_file("$datadir/wrfoto.dat")) $lines=file("$datadir/wrfoto.dat");
if (!isset($lines)) $datasize=0; else $datasize=sizeof($lines);
if ($datasize<=0) exit("�������� � ����� ������ - ���� ����������. ������ = 0!");
if (copy("$datadir/wrfoto.dat", "$datadir/copy.dat")) exit("<center><BR>����� ���� ������ �������.<BR><BR><h3>$back</h3></center>"); else exit("������ �������� ����� ���� ������. ���������� ������� ������� ���� copy.dat � ����� $datadir � ��������� ��� ����� �� ������ - 666 ��� ������ ����� 777 � ��������� �������� �������� �����!"); }

// ������������ �� ����� ��
if ($_GET['event']=="restore")  {
if (is_file("$datadir/copy.dat")) $lines=file("$datadir/copy.dat");
if (!isset($lines)) $datasize=0; else $datasize=sizeof($lines);
if ($datasize<=0) exit("�������� � ������ ���� ������ - ��� ����������. �������������� ����������!");
if (copy("$datadir/copy.dat", "$datadir/wrfoto.dat")) exit("<center><BR>�� ������������� �� �����.<BR><BR><h3>$back</h3></center>"); else exit("������ �������������� �� ����� ���� ������. ���������� ������� ������ copy.dat � wrfoto.dat � ����� $datadir ��������� ����� �� ������ - 666 ��� ������ ����� 777 � ��������� �������� ��������������!"); }



// ����������������, ��� 2: ���������� ������
if ($_GET['event']=="config")  {

// ��������� ����� ������ ������/����������
if (strlen($_POST['newpassword'])<1 or strlen($_POST['newmoderpass'])<1) exit("$back ����������� ����� ������ ������� 1 ������!");
if ($_POST['newpassword']!="�����") {$pass=trim($_POST['newpassword']); $_POST['password']=md5("$pass+$skey");}
if ($_POST['newmoderpass']!="�����") {$pass=trim($_POST['newmoderpass']); $_POST['moderpass']=md5("$pass+$skey");}

// ������ �� ������. ��������, ��� � ������� ������ ���������� �������...
$fd=stripslashes($_POST['fdesription']); $fd=str_replace("\\","/",$fd); $fd=str_replace("?>","? >",$fd); $fd=str_replace("\"","'",$fd); $fdesription=str_replace("\r\n","<br>",$fd);

mt_srand(time()+(double)microtime()*1000000); $rand_key=mt_rand(1000,9999); // ���������� ��������� ����� ��� �����������

$gmttime=($_POST['deltahour'] * 60 * 60);  // ������� ��������

$newsmiles=$_POST['newsmiles'];

$i=count($newsmiles); $smiles="array(";
for($k=0; $k<$i; $k=$k+2) {
  $j=$k+1; $s1=replacer($newsmiles[$k]); $s2=replacer($newsmiles[$j]);
  $smiles.="\"$s1\", \"$s2\""; if ($k!=($i-2)) $smiles.=",";
} $smiles.=");";

$configdata="<? // WR-foto v 1.1  //  21.08.13 �.  //  Miha-ingener@yandex.ru\r\n".
"$"."fname=\"".$_POST['fname']."\"; // �������� ������� ������������ � ���� TITLE � ���������\r\n".
"$"."fdesription=\"".$fdesription."\"; // ������� �������� ������\r\n".
"$"."password=\"".$_POST['password']."\"; // ������ ������ ���������� md5()\r\n".
"$"."moderpass=\"".$_POST['moderpass']."\"; // ������ ���������� ���������� md5()\r\n".
"$"."adminemail=\"".$_POST['newadminemail']."\"; // �-���� ��������������\r\n".
"$"."stop=\"".$_POST['stop']."\"; // ��������� ���������� �������� / ����������\r\n".
"$"."antimat=\"".$_POST['antimat']."\"; // �������� ������� ��/��� - 1/0\r\n".
"$"."random_name=\"".$_POST['random_name']."\"; // ��� �������� ����� ������������ ��� ��� ��������� �������?\r\n".
"$"."repaaddfile=\"".$_POST['repaaddfile']."\"; // ������� ����� ��������� ��������� ��� �������� �����?\r\n".
"$"."repaaddmsg=\"".$_POST['repaaddmsg']."\"; // ������� ����� ��������� ��������� �� ���������� ���������?\r\n".
"$"."repaaddtem=\"".$_POST['repaaddtem']."\"; // ������� ����� ��������� ��������� �� ���������� ����?\r\n".
"$"."sendmail=\"".$_POST['sendmail']."\"; // �������� �������� ���������? 1/0\r\n".
"$"."sendadmin=\"".$_POST['sendadmin']."\"; // ������ ������ ��������� ���������� �������������? 1/0\r\n".
"$"."statistika=\"".$_POST['statistika']."\"; // ���������� ���������� �� ������� ��������? 1/0\r\n".
"$"."antispam=\"".$_POST['antispam']."\"; // ������������� ��������\r\n".
"$"."antispam=\"1\"; // ������������� �������� 2013\r\n".
"$"."antispam2012v=\"".$_POST['antispam2012v']."\"; // ������ ��������� 2012\r\n".
"$"."antispam2012o=\"".$_POST['antispam2012o']."\"; // ����� ��������� 2012\r\n".
"$"."max_key=\"".$_POST['max_key']."\"; // ���-�� �������� � ���� �����������\r\n".
"$"."rand_key=\"".$rand_key."\"; // ��������� ����� ��� �����������\r\n".
"$"."newmess=\"".$_POST['newmess']."\"; // ��������� ���� � ������ ����������� ������?\r\n".
"$"."guest=\"".$_POST['newguest']."\"; // ��� �������� �� �����-�� �������������\r\n".
"$"."users=\"".$_POST['newusers']."\"; // ��� �������� �����-��\r\n".
"$"."cangutema=\"".$_POST['cangutema']."\"; // ��������� ������ ��������� ����? 1/0\r\n".
"$"."cangumsg=\"".$_POST['cangumsg']."\"; // ��������� ������ ��������� ���������? 1/0\r\n".
"$"."useactkey=\"".$_POST['useactkey']."\"; // ��������� ��������� ����� ����� ��� �����������? 1/0\r\n".
"$"."maxname=\"".$_POST['newmaxname']."\"; // ������������ ���-�� �������� � �����\r\n".
"$"."maxzag=\"".$_POST['maxzag']."\"; // ����������� ���-�� �������� � ��������� ����\r\n".
"$"."maxmsg=\"".$_POST['newmaxmsg']."\"; // ������������ ���������� �������� � ���������\r\n".
"$"."qqmain=\"".$_POST['newqqmain']."\"; // ���-�� ������������ ��� �� �������� (15)\r\n".
"$"."qq=\"".$_POST['newqq']."\"; // ���-�� ������������ ��������� �� ������ �������� (10)\r\n".
"$"."uq=\"".$_POST['uq']."\"; // �� ������� ������� �������� ������ ����������\r\n".
"$"."liteurl=\"".$_POST['liteurl']."\";// ������������ ���? 1/0\r\n".
"$"."max_file_size=\"".$_POST['max_file_size']."\"; // ������������ ������ ������� � ������\r\n".
"$"."datadir=\"".$_POST['datadir']."\"; // ����� � ������� ������\r\n".
"$"."smile=\"".$_POST['smile']."\";// ��������/��������� ����������� ������\r\n".
"$"."canupfile=\"".$_POST['canupfile']."\"; // ��������� �������� ���� 0 - ���, 1 - ������ ������������������\r\n".
"$"."filedir=\"".$_POST['filedir']."\"; // ������� ���� ����� ������� ����\r\n".
"$"."max_upfile_size=\"".$_POST['max_upfile_size']."\"; // ������������ ������ ����� � ������\r\n".
"$"."fskin=\"".$_POST['fskin']."\"; // ������� ���� ������\r\n".
"$"."back=\"<html><head><meta http-equiv='Content-Type' content='text/html; charset=windows-1251'><meta http-equiv='Content-Language' content='ru'></head><body><center>��������� <a href='javascript:history.back(1)'><B>�����</B></a>\"; // ������� ������\r\n".
"$"."smiles=".$smiles."// �������� (��� �����, ������ ��� �������, -//-)\r\n".
"$"."date=date(\"d.m.y\", time()+$gmttime); // �����.�����.���\r\n".
"$"."deltahour=\"".$_POST['deltahour']."\"; // ��������� ���-�� ����� �� ��������� ������������ �������� �� �������: �� * 3600\r\n".
"$"."time=date(\"H:i\",time()+$gmttime); // ����:������:�������\r\n?>";
$file=file("data/config.php");
$fp=fopen("data/config.php","a+");
flock ($fp,LOCK_EX); 
ftruncate ($fp,0);//������� ���������� ����� 
fputs($fp,$configdata);
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);
Header("Location: admin.php?event=configure"); exit;}


} // ����� if isset($event)




// ����� ��� ���� �������

if (isset($_COOKIE['wrfcookies'])) {
$wrfc=$_COOKIE['wrfcookies']; $wrfc = explode("|", $wrfc);
$wrfname=$wrfc[0];$wrfpass=$wrfc[1];$wrftime1=$wrfc[2];$wrftime2=$wrfc[3];
if (time()>($wrftime1+50)) { $tektime=time();
$wrfcookies="$wrfc[0]|$wrfc[1]|$tektime|$wrftime1|";
setcookie("wrfcookies", $wrfcookies, time()+1728000);
$wrfc=$_COOKIE['wrfcookies']; $wrfc = explode("|", $wrfc);
$wrfname=$wrfc[0];$wrfpass=$wrfc[1];$wrftime1=$wrfc[2];$wrftime2=$wrfc[3]; }}

if (is_file("$datadir/wrfoto.dat")) $mainlines=file("$datadir/wrfoto.dat");
if (!isset($mainlines)) $datasize=0; else $datasize=sizeof($mainlines);
if ($datasize<=0) {if (is_file("$datadir/copy.dat")) {$mainlines=file("$datadir/copy.dat"); $datasize=sizeof($mainlines);}}
if ($datasize<=0) exit("<center><h3>���� ������ ������������! �������� �������!</h3>");

// ���� ������� � ��������� ������: ���� -> ������ -> �����
if (isset($_GET['fid'])) { $fid=$_GET['fid'];
if (!ctype_digit($fid) or strlen($fid)>3) exit("<B>$back. ������� ������. ������� ����� �� �����.</B>");
$imax=count($mainlines); $i=count($mainlines);
// �������� �� ���� �������� � ������� - ���� �������������
$raz=""; $rfid=""; $frname=""; do {$i--; $dt=explode("|", $mainlines[$i]);
if ($dt[0]==$fid) {$rfid=$i; $raz="$dt[1]"; $frname="$dt[1]"; if (isset($dt[11])) { if($dt[11]>0) $maxtem=$dt[11]; else $maxtem="100"; }}
} while($i >0);


if (isset($_GET['id'])) { $id=$_GET['id'];
if (!ctype_digit($id) or strlen($id)>15) exit("<B>$back. ������� ������. ������� ����� �� �����.</B>");
if (is_file("$datadir/$id.dat")) {
 $lines=file("$datadir/$id.dat");
  if (count($lines)>4) {$dtt=explode("|",$lines[0]); $frtname=$dtt[3];} else $frtname="";
 $ft=$frname; $frname="-> $ft ->";} else {$frtname=""; $frname="";}} else {$frtname="";  $frname.="->";} } else {$frname=""; $frtname="";}



 
 


// ����� ��� ����� - ����� ���������� ,������� ����� ����� ������������
if ($gbname==$adminname[0]) $ktotut="1"; else $ktotut="2";


// �������� �������� ����������� ���� ���� ����
?>
<html>
<head>
<title>������� :: <?print"$frtname $frname $fname";?></title>
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Cache-Control" CONTENT="no-cache">
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<meta http-equiv="Content-Language" content="ru">
<meta name="description" content="<? print"$fdesription - $fname";?>">
<meta http-equiv="keywords" content="<? print"$frtname $frname $fname";?>">
<meta name="Resource-type" content="document">
<meta name="document-state" content="dynamic">
<meta name="Robots" content="index,follow">
<link rel="stylesheet" href="<?=$fskin?>/style.css" type="text/css">
<SCRIPT language=JavaScript>
<!--
function x () {return;}
function FocusText() {
 document.REPLIER.msg.focus();
 document.REPLIER.msg.select();
 return true; }
function DoSmilie(addSmilie) {
 var revisedMessage;
 var currentMessage=document.REPLIER.msg.value;
 revisedMessage=currentMessage+addSmilie;
 document.REPLIER.msg.value=revisedMessage;
 document.REPLIER.msg.focus();
 return;
}
function DoPrompt(action) { var revisedMessage; var currentMessage=document.REPLIER.msg.value; }
//-->
</SCRIPT>
</head>

<body bgcolor="#E5E5E5" text="#000000" link="#006699" vlink="#5493B4" bottomMargin=0 leftMargin=0 topMargin=0 rightMargin=0 marginheight="0" marginwidth="0">

<table width=100% cellspacing=0 cellpadding=10 align=center><tr><td class=bodyline>
<table width=100% cellspacing=0 cellpadding=0>
<tr>
<td><a href="index.php">�����������</a>
<br><div align=center>�� ����� ��� <B><font color=red><?print"$gbname";?></font></B></td>
<td align="center" valign="middle"><span class="maintitle"><a href=admin.php><h3><font color=red>������ �����������������<br></font> <?=$fname?></h3></a></span>
<table width=80%><TR><TD align=center><span class="gen"><?=$fdesription?><br><br></span></TD></TR></TABLE>
<table cellspacing=0 cellpadding=2><tr><td align=center valign=middle>
<a href='admin.php?event=makecopy' class=mainmenu><img src="<?=$fskin?>/go.gif">������� ����� ��</a> 
<a href='admin.php?event=restore' class=mainmenu><img src="<?=$fskin?>/go.gif">������������ �� �����</a> 
<a href='admin.php?event=blockip' class=mainmenu><img src="<?=$fskin?>/go.gif">IP-����������</a>
<a href='admin.php?event=revolushion' class=mainmenu><img src="<?=$fskin?>/go.gif">�����������</a>


<? if ($ktotut==1) print"<a href='admin.php?event=configure' class=mainmenu><img src='$fskin/go.gif' width='12' height='13' border='0' alt='' hspace='3' />���������</a>";
print"<a href='admin.php?event=clearcooke' class=mainmenu><img src='$fskin/go.gif' width='12' height='13' border='0' alt='�����' hspace='3'>����� �� �������</a>";

if (is_file("$datadir/copy.dat")) {
if (count(file("$datadir/copy.dat"))<1) $a2="<font color=red size=+1>�� ���� ����� ����! ������ ������������!</font><br> (�������� ����� �������, ���� �� ��������� �����������)"; else $a2="";
$a1=round((time()-filemtime("$datadir/copy.dat"))/86400); if ($a1<1) $a1="�������</font>, ��� ���� ���!"; else $a1.="</font> ���� �����.";
$add="<br><B><center>����� ���� ������� <font color=red size=+1>".$a1." $a2</B>"; if ($a1>90) $add.="�� ��, ������ 3-� ������� ������� ����� �� ������. ����������� ������? ������� �����!"; if ($a1>10) $add.="�� ���! ������ ������� �����! � ����� ����? ��� ������ ������ ���������������?!!"; if ($a1>5) $add.="���� ������ �����. �������� ���� �����. ����� ���� ��������� ��� ���� ;-)"; $add.="</center>";} else $add="";

print"</span>
</td></tr></table>
</td></tr></table>
$add<table width=100% cellspacing=0 cellpadding=2>
<tr><td><span class=gensmall>�������: $date - $time</td></tr></table>";






// ������� ������� �������� ������
if (!isset($_GET['event']))  {

if (!isset($_GET['fid'])) {
echo'
<table width=100% cellpadding=2 cellspacing=1 class=forumline>
<tr><th width=60% colspan=2 class=thCornerL height=25 nowrap=nowrap>���������</th>
<th width=10% class=thTop nowrap=nowrap>��������</th>
<th width=7% class=thCornerR nowrap=nowrap>����������</th>
<th width=28% class=thCornerR nowrap=nowrap>����������</th></tr>';

// ������� qq ��������� �� ������� ��������

$addform="<form action='admin.php?event=addmainforum' method=post name=REPLIER1><table width=100% cellpadding=4 cellspacing=1 class=forumline><tr> <td class=catHead colspan=2 height=28><span class=cattitle>���������� ������������ / ���������</span></td></tr><tr><td class=row1 align=right><b><span class=gensmall>��� ������������ ������</span></b></td><td class=row1><input type=radio name=ftype value='razdel'> ������������ &nbsp;&nbsp;<input type=radio name=ftype value=''checked> ���������</tr></td><tr><td class=row1 align=right valign=top><span class=gensmall><B>��������</B></td><td class=row1 align=left valign=middle><input type=text  class=post value='' name=zag size=70></td></tr><tr><td class=row1 align=right valign=top><span class=gensmall>��������</td><td class=row1 align=left valign=middle><textarea cols=100 rows=6 size=500 class=post name=msg></textarea></td></tr><tr><td class=row1 colspan=2><center><input type=submit class=mainoption value='     ��������     '></td></span></tr></table></form>";

if (!is_file("$datadir/wrfoto.dat")) exit("<h3>������������ �� �� �����. ���� wrfoto.dat ������������ ��� �������� �����/������.</h3>$addform"); 

$lines = file("$datadir/wrfoto.dat"); $datasize = sizeof($lines);

if ($datasize==0) exit("<h3>���� wrfoto.dat ���� - �������� ����� ��� ������.</h3>$addform");

$i=count($lines);
$n="0"; $a1="-1"; $u=$i-1;
$fid="0"; $itogotem="0"; $itogomsg="0";

do {$a1++; $dt = explode("|", $lines[$a1]);
$fid=$dt[0];


echo'<tr height=30><td class=row1>';

if ($ktotut==1) { // ������ ����� ����� ��������� ���������
print"<table><TR>
<td width=10 bgcolor=#A6D2FF><B><a href='admin.php?movetopic=$a1&where=1' title='����������� �����'>��</a></B></td>
<td width=10 bgcolor=#DEB369><B><a href='admin.php?movetopic=$a1&where=0' title='����������� ����'>��</a></B></td>
<td width=10 bgcolor=#22FF44><B><a href='admin.php?frd=$a1' title='�������������'>.P.</a></B></td>
<td width=10 bgcolor=#FF2244><B><a href='admin.php?fxd=$dt[0]' title='�������' onclick=\"return confirm('����� ����� ������ � ��� ���� � ͨ�! �������? �������?')\" >.X.</a></B></td>
</tr></table>"; }

echo'</td>';

// ���������� ���: ����� ��� ���������
if ($dt[1]=="razdel") print "<td class=catLeft colspan=1><span class=cattitle><center>$dt[2]</td><td class=rowpic colspan=4 align=right>&nbsp;</td></tr>";

else {

if (is_file("$datadir/$dt[3].dat")) { $msgsize=sizeof(file("$datadir/$dt[3].dat")); // ������� ���-�� ������� � �����
if ($msgsize>$qq) $page=ceil($msgsize/$qq); else $page=1; } else {$msgsize=""; $page=1;}

if ($dt[7]==$date) $dt[7]="�������";
$maxzvezd=null; if (isset($dt[12])) { if ($dt[12]>0) {$maxzvezd="*�������� ����������, ������� <font color=red><B>$dt[12]</B> �����";
$dt[4]=""; $dt[5]="";
if ($dt[12]==1) $maxzvezd.="�";
if ($dt[12]==2 or $dt[12]==3 or $dt[12]==4) $maxzvezd.="�"; $maxzvezd.=" �������</font>";}}

print "
<td width=60% class=row1 valign=middle><span class=forumlink><a href=\"admin.php?fid=$fid\">$dt[1]</a> $maxzvezd<BR></span><small>$dt[2]</small></td>
<td width=7% class=row2 align=center><small>$dt[4] / $dt[11]</small></td>
<td width=7% class=row2 align=center valign=middle><small>$dt[5]</small></td>
<td width=28% class=row2 valign=middle><span class=gensmall>
����: <a href=\"admin.php?fid=$fid&id=$dt[3]&page=$page#m$msgsize\">$dt[10]</a><BR>
�����: <B>$dt[6]</B><BR>
����: <B>$dt[7]</B> - $dt[8]</span></td></tr>";

$itogotem=$itogotem+$dt[4]; $itogomsg=$itogomsg+$dt[5]; }
} while($a1 < $u);
echo'</table><BR>';

// ������� �������������� ������
if (isset($_GET['frd'])) { if ($_GET['frd'] !="") { $frd=$_GET['frd'];
$lines = file("$datadir/wrfoto.dat");
$dt = explode("|", $lines[$frd]);
if (isset($dt[11])) { if ($dt[11]>0) $addmax=$dt[11]; else $addmax="100"; }
if (isset($dt[12])) {if ($dt[12]<=0) $dt[12]="0";}
$dt[2]=str_replace("<br>","\r\n",$dt[2]);
print "<form action='admin.php?event=frdmainforum' method=post name=REPLIER1><table width=100% cellpadding=4 cellspacing=1 class=forumline><tr> <td class=catHead colspan=2 height=28><span class=cattitle>�������������� ������� / ������</span></td></tr>
<tr><td class=row1 align=right>��� �������������� ������</td><td class=row1><input type=hidden name=nextnum value='$dt[0]'>";
if ($dt[1]=="razdel") print "<input type=hidden name=ftype value='razdel'>������</tr></td><tr><td class=row1 align=right valign=top><span class=gensmall><B>���������</B></td><td class=row1 align=left valign=middle><input type=text value='$dt[2]' name=zag size=70></td></tr>";
else {print "
<input type=hidden name=ftype value=''>�����</tr></td><tr><td class=row1 align=right valign=top><B>���������</B></td><td class=row1 align=left valign=middle><input class=post type=text value='$dt[1]' name=zag size=70></td></tr>
<tr><td class=row1 align=right valign=top>��������</td><td class=row1 align=left valign=middle><textarea cols=80 rows=6 class=post size=500 name=msg>$dt[2]</textarea>
<input type=hidden name=idtemka value='$dt[3]'>
<input type=hidden name=kt value='$dt[4]'>
<input type=hidden name=km value='$dt[5]'>
<input type=hidden name=namem value='$dt[6]'>
<input type=hidden name=datem value='$dt[7]'>
<input type=hidden name=timem value='$dt[8]'>
<input type=hidden name=timetk value='$dt[9]'>
<input type=hidden name=temka value='$dt[10]'>
</td></tr>
<TR><TD align=right class=row1>������������  ���-�� ��� � ������</TD><TD class=row1><input type=text class=post name=addmax value='$addmax'></TD></TR>
<input type=hidden name=zvezdmax value='$dt[12]'>
<TR><TD align=right class=row1>������������� �� ������</TD><TD class=row1><input type=text class=post size=5 maxlength=1 name=zvezdmax value='$dt[12]'>
(������ ��������� � ��������� ���-��� ���� ����� ��������� ���� �����)</TD></TR>";}

print"<tr><td colspan=2 class=row1><input type=hidden name=frd value='$frd'><SCRIPT language=JavaScript>document.REPLIER1.zag.focus();</SCRIPT><center><input type=submit class=mainoption value='     ��������     '></td></span></tr></table></form><BR>";
} } // ����� �������������� ������

else { if ($ktotut==1) print "$addform"; }


if ($statistika==TRUE)  {
print"<table width=100% cellpadding=3 cellspacing=1 class=forumline><tr><td class=catHead colspan=2 height=28><span class=cattitle>����������</span></td></tr><tr>
<td class=row1 align=center valign=middle rowspan=2>&nbsp;</td>
<td class=row1 align=left width=95%><span class=gensmall>����������: <b>$itogomsg</b><br>��������: <b>$itogotem</b></span></td>
</tr></table>"; 

// ���������� -= ��������� ��������� � ������ =-

if (is_file("$datadir/news.dat")) { $newmessfile="$datadir/news.dat";
$lines=file($newmessfile); $i=count($lines); //if ($i>10) $i=10; (������������� - ��� ��� ����!!! ;-))
if ($i>1) {
echo('<br><table width=100% cellpadding=3 cellspacing=1 class=forumline><tr><td class=catHead colspan=2 height=28><span class=cattitle>��������� ����������� ����</span></td></tr><tr>
<td class=row1 align=center valign=middle rowspan=2><img src="'.$fskin.'/whosonline.gif"></td>
<td class=row1 align=left width=95%><span class=gensmall>');

$a1=$i-1;$u="-1"; // ������� ������ �� ����������� ��� ��������
do {$dt=explode("|", $lines[$a1]); $a1--;

if (isset($dt[1])) { // ���� ������� ���������� � ������� (������ ������) - �� ������ � �� �������
$dt[6]=htmlspecialchars($dt[6]);
$dt[6]=str_replace("[b] "," ",$dt[6]);
$dt[6]=str_replace("[/b]"," ",$dt[6]);
$dt[6]=str_replace("[RB] "," ",$dt[6]);
$dt[6]=str_replace("[/RB]"," ",$dt[6]);
$dt[6]=str_replace("[Code] "," ",$dt[6]);
$dt[6]=str_replace("[/Code]"," ",$dt[6]);
$dt[6]=str_replace("[Quote] "," ",$dt[6]);
$dt[6]=str_replace("[/Quote]"," ",$dt[6]);
$dt[6]=str_replace("<br>","\r\n", $dt[6]);
$dt[2]=str_replace(".201",".1", $dt[2]);
$dt[2]=substr($dt[2],0,8);
$dt[3]=substr($dt[3],0,5);
if ($dt[8]>$qq) $page=ceil($dt[8]/$qq); else $page=1; // ������� ��������

if ($dt[10]=="��") {$codename=urlencode($dt[4]); $name="<B><a href='tools.php?event=profile&pname=$codename'>$dt[4]</a></B>";} else $name="����� $dt[4]";
print"$dt[2] - $dt[3]: <B><a href='admin.php?fid=$dt[0]'>$dt[9]</a></B> -> <B><a href='admin.php?fid=$dt[0]&id=$dt[1]&page=$page#m$dt[8]' title='$dt[6] \r\n\r\n ���������� $dt[3], $dt[2] �.'>$dt[5]</a></B> - $name.<br>";
} // ���� ������� ����������
$a11=$u; $u11=$a1;
} while($a11 < $u11);
echo'</span></td></tr></table>';}

} // ����� ����� ��������� ���������
}

} // ����� ������� ��������









// ������� �������� � ������ ��������� �������
if (isset($_GET['fid']) and !isset($_GET['id'])) { $fid=$_GET['fid'];


$maxzd=null; // �������� ������ �� ���-�� �¨�� � ����
do {$imax--; $ddt=explode("|", $mainlines[$imax]); if ($ddt[0]==$fid) $maxzd=$ddt[12]; } while($imax>"0");
if (!ctype_digit($maxzd)) $maxzd=0;

print "
<table><tr><td><span class=nav>&nbsp;&nbsp;&nbsp;<a href=admin.php class=nav>$fname</a> -> <a href=admin.php?fid=$fid class=nav>$frname</a></span></td></tr></table>
<table width=100% cellpadding=2 cellspacing=1 class=forumline><tr>
<th width=3% class=thCornerL height=25 nowrap=nowrap>X/P</th>
<th width=57% colspan=2 class=thCornerL height=25 nowrap=nowrap>����</th>
<th width=10% class=thTop nowrap=nowrap>C��������</th>
<th width=12% class=thCornerR nowrap=nowrap>�����</th>
<th width=18% class=thCornerR nowrap=nowrap>����������</th></tr>";

$addbutton="<table width=100%><tr><td align=left valign=middle><span class=nav><a href=\"admin.php?fid=$fid&newtema=add\"><img src='$fskin/add_foto.gif' border=0></a>&nbsp;</span></td>";


// ���������� ���� �� ���������� � ����� � �������
if (is_file("$datadir/topic$fid.dat"))
{
$msglines=file("$datadir/topic$fid.dat");
if (count($msglines)>0) {

if (count($msglines)>$maxtem-1) $addbutton="<table width=100%><TR><TD>���������� ���������� ��� � ������� ���������.";

// ������� qqmain ��������� �� ������� ��������
$lines=file("$datadir/topic$fid.dat");
$i=count($lines); $maxi=count($lines)-1; $n="0";

// ��������� ������ ������ �������������� ��������
if (!isset($_GET['page'])) $page=1; else { $page=$_GET['page']; if (!ctype_digit($page)) $page=1; if ($page<1) $page=1; }


// ���������� QQ ���
$fm=$maxi-$qq*($page-1); if ($fm<"0") $fm=$qq;
$lm=$fm-$qq; if ($lm<"0") $lm="-1";

$timetek=time();

do {$dt=explode("|", $lines[$fm]);

// ����� ��� ����������� ���� �� VIP-������
if (is_file("$datadir/$dt[7].dat")) $ftime=filemtime("$datadir/$dt[7].dat"); else $ftime="";
$timer=$timetek-$ftime; // ������ ������� ������ ������� (� ��������) 

$fm--; $num=$fm+2; $numid=$fm+1;

$filename=$dt[7]; if (is_file("$datadir/$filename.dat")) { // ���� ���� � ����� ���������� - �� �������� ����
$msgsize=sizeof(file("$datadir/$filename.dat"));

print "<tr height=50>
<td width=3% class=row1><table>

<!--
������� � ��������� ������!
<tr><td width=10 bgcolor=#22FF44><B><a href='admin.php?fid=$fid&rd=$numid&page=$page' title='�������������'>.P.</a></B></td></tr><tr>-->

<td width=10 bgcolor=#FF2244><B><a href='admin.php?fid=$fid&xd=$numid&id=$dt[7]&page=$page' title='�������'>.X.</a></B></td>
</tr></table></td>
<td width=3% class=row1 align=center valign=middle></td>
<td width=57% class=row1 valign=middle><span class=forumlink><b>";

if ($timer<0) echo'<font color=red>VIP </font>';

print"<a href=\"admin.php?fid=$fid&id=$dt[7]\">$dt[3]</a>";

if ($msgsize>$qq) { // ������� ������ ��������� ������� ����
$maxpaget=ceil($msgsize/$qq); $addpage="";
echo'</b></span><small>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<div style="padding:6px;" class=pgbutt>��������: ';
if ($maxpaget<=5) $f1=$maxpaget; else $f1=5;
for($i=1; $i<=$f1; $i++) {if ($i!=1) $addpage="&page=$i"; print"<a href=admin.php?fid=$fid&id=$dt[7]$addpage>$i</a> &nbsp;";}
if ($maxpaget>5) print "... <a href=admin.php?fid=$fid&id=$dt[7]&page=$maxpaget>$maxpaget</a>"; }

print"</div></td><td class=row2 align=center>$msgsize</td><td class=row2><span class=gensmall>";

$codename=urlencode($dt[0]);
if ($dt[1]=="��") print "<a href='tools.php?event=profile&pname=$codename':$dt[2]>$dt[0]</a><BR><small>$users</small>"; else  print"$dt[0]<BR><small>$guest</small>";


if ($msgsize>=2) {$linesdat=file("$datadir/$filename.dat"); $dtdat=explode("|", $linesdat[$msgsize-1]);
if (strlen($linesdat[$msgsize-1])>10) {$dt[0]=$dtdat[0]; $dt[1]=$dtdat[1]; $dt[2]=$dtdat[2]; $dt[5]=$dtdat[5]; $dt[6]=$dtdat[6];}} // ������ if (strlen...) ������ ���� ���� ���� � ����� ������ ������ - �������

$dt[6]=substr($dt[6],0,-3);
if ($dt[5]===$date) $dt[5]="<B>�������</B>";
print "</span></td><td width=15% height=50 class=row2 align=left valign=middle nowrap=nowrap><span class=gensmall>&nbsp;
�����: $dt[0]<BR>&nbsp;
����: $dt[5]<BR>&nbsp;
�����: $dt[6]</font>
</td></tr>";
} //if (is_file)

} while($lm < $fm);


// ��������� ���������� $pageinfo - �� ������� �������
$pageinfo=""; $addpage=""; $maxpage=ceil(($maxi+1)/$qq); if ($page>$maxpage) $page=$maxpage;
$pageinfo.="<div style='padding:6px;' align=right class=pgbutt>��������: &nbsp;";
if ($page>3 and $maxpage>5) $pageinfo.="<a href=admin.php?fid=$fid>1</a> ... ";
$f1=$page+2; $f2=abs($page-2); if ($f2=="0") $f2=1; if ($page>=$maxpage-1) $f1=$maxpage;
if ($maxpage<=5) {$f1=$maxpage; $f2=1;}
for($i=$f2; $i<=$f1; $i++) { if ($page==$i) $pageinfo.="<B>$i</B> &nbsp;"; 
else {if ($i!=1) $addpage="&page=$i"; $pageinfo.="<a href=admin.php?fid=$fid$addpage>$i</a> &nbsp;";} }
if ($page<=$maxpage-3 and $maxpage>5) $pageinfo.="... <a href=admin.php?fid=$fid&page=$maxpage>$maxpage</a>";
$pageinfo.='</div>';

print "</table>$pageinfo";
}}



// ------------ ������� �������������� ����
if (isset($_GET['rd'])) { if ($_GET['rd'] !="")  { $rd=$_GET['rd']; $dt=explode("|", $lines[$rd]);

$moddate=filemtime("$datadir/$dt[7].dat"); $tektime=time();
if ($moddate<$tektime) {$vt1="checked"; $vt2="";} else {$vt2="checked"; $vt1="";}
if ($dt[8]=="closed") {$ct2="checked"; $ct1="";} else {$ct1="checked"; $ct2="";}

print "<form action='admin.php?event=rdtema&page=$page' method=post name=REPLIER1><table cellpadding=4 cellspacing=1 width=100% class=forumline><tr> <td class=catHead colspan=2 height=28><span class=cattitle>�������������� ����</span></td></tr>
<tr><td class=row1 align=right valign=top>�������� ����</td>
<td class=row1 align=left valign=top><input type=text class=post value='$dt[3]' name=zag size=70>
<input type=radio name=status value=''$ct1/> <font color=blue><B>�������</B></font>&nbsp;&nbsp; <input type=radio name=status value='closed'$ct2/> <font color=red><B>�������</B></font>
<input type=hidden name=rd value='$rd'>
<input type=hidden name=name value='$dt[0]'>
<input type=hidden name=who value='$dt[1]'>
<input type=hidden name=email value='$dt[2]'>
<input type=hidden name=msg value=\"$dt[4]\"><!-- ������� � ���� ������ �������!-->
<input type=hidden name=datem value='$dt[5]'>
<input type=hidden name=timem value='$dt[6]'>
<input type=hidden name=id value='$dt[7]'>
<input type=hidden name=timetk value='$dt[9]'>
<input type=hidden name=fid value='$fid'></TD></TR>

<tr><td class=row1 align=right valign=top>����������� � ������ ������ ?</TD><TD class=row1>
<select style='width=440' name='changefid'>
<option value='$fid'>���. �������� � �������</option><br><br>";

$mainlines=file("$datadir/wrfoto.dat");
$mainsize=sizeof($mainlines); if($mainsize<1) exit("$back ���� ������ �������� ��� � ��� ����� ���� �������!");
$ii=count($mainlines); $cn=0; $i=0;
do {$mdt=explode("|", $mainlines[$i]);
if ($mdt[1]=="razdel") {if ($cn!=0) {echo'</optgroup>'; $cn=0;} $cn++; print"<optgroup label='$mdt[2]'>";}  else {print" <option value='$mdt[0]' >|-$mdt[1]</option>";}
$i++; } while($i <$ii);
$s2=""; $s1="checked"; // ��������� � ����� �� ��������� ������� � ����� �������
print"</optgroup></select>

<input type=radio name=viptema value='0'$vt1/> <font color=gray><B>������� ����</B></font>&nbsp;&nbsp; <input type=radio name=viptema value='1'$vt2/> <font color=black><B>VIP-����</B></font>

</TD></TR><tr><td class=row1 align=right valign=top>����� �������� ��������� � ����� ������ ?</TD><TD class=row1>
<input type=radio name=goto value='0'$s1> � ������� ������� &nbsp;&nbsp; <input type=radio name=goto value='1'$s2> ���� ���� ��������� ����
</td></tr><tr><td colspan=2 class=row1>
<SCRIPT language=JavaScript>document.REPLIER1.zag.focus();</SCRIPT><center><input type=submit class=mainoption value='     ��������     '></td></span></tr></table></form>";
}

} else  {

}
// --------------

}
}







// ������� ��������� � ������� ����
if (isset($_GET['fid']) and isset($_GET['id'])) {$id=$_GET['id']; $fid=$_GET['fid'];

// ���������� ���� �� ���������� � ����� � �������
if (!is_file("$datadir/$id.dat")) exit("<BR><BR>$back. ��������, �� ����� ���� �� ������ �� ����������.<BR> ������ ����� � ������ �������������.");
$lines=file("$datadir/$id.dat"); $mitogo=count($lines); $i=$mitogo; $maxi=$i-1;

if ($mitogo>0) { $tblstyle="row1"; $printvote=null;

// ���� ���� � topic��.dat - ��������� �� ������� �� ����?
$msglines=file("$datadir/topic$fid.dat"); $mg=count($msglines); $closed="no";
do {$mg--; $mt=explode("|",$msglines[$mg]);
if ($mt[7]==$id and $mt[8]=="closed") $closed="yes";
} while($mg > "0");

$maxzd=null; // �������� ������ �� ���-�� �¨�� � ����
do {$imax--; $ddt=explode("|", $mainlines[$imax]); if ($ddt[0]==$fid) $maxzd=$ddt[12]; } while($imax>"0");
if (!ctype_digit($maxzd)) $maxzd=0;

// ��������� ������ ������ �������������� ��������
if (!isset($_GET['page'])) $page=1; else {$page=$_GET['page']; if (!ctype_digit($page)) $page=1; if ($page<1) $page=1;}

// ��������� ���������� $pageinfo - �� ������� �������
$pageinfo=""; $addpage=""; $maxpage=ceil(($maxi+1)/$qq); if ($page>$maxpage) $page=$maxpage;
$pageinfo.="<div align=center style='padding:6px;' class=pgbutt>��������: &nbsp;";
if ($page>3 and $maxpage>5) $pageinfo.="<a href=admin.php?fid=$fid&id=$id>1</a> ... ";
$f1=$page+2; $f2=abs($page-2); if ($f2=="0") $f2=1; if ($page>=$maxpage-1) $f1=$maxpage;
if ($maxpage<=5) {$f1=$maxpage; $f2=1;}
for($i=$f2; $i<=$f1; $i++) { if ($page==$i) $pageinfo.="<B>$i</B> &nbsp;"; 
else {if ($i!=1) $addpage="&page=$i"; $pageinfo.="<a href=admin.php?fid=$fid&id=$id$addpage>$i</a> &nbsp;";} }
if ($page<=$maxpage-3 and $maxpage>5) $pageinfo.="... <a href=admin.php?fid=$fid&id=$id&page=$maxpage>$maxpage</a>";
$pageinfo.='</div>';

print"$pageinfo";

$fm=$qq*($page-1); if ($fm>$maxi) $fm=$maxi-$qq;
$lm=$fm+$qq; if ($lm>$maxi) $lm=$maxi+1;

do {$dt=explode("|", $lines[$fm]);

$fm++; $num=$maxi-$fm+2; $status=""; unset($youwr);

if (strlen($lines[$fm-1])>5) { // ���� ������� ���������� � ������� (������ ������) - �� ������ � �� �������

$msg=str_replace("[b]","<b>", $dt[4]);
$msg=str_replace("[/b]","</b>",$msg);
$msg=str_replace("[RB]","<font color=red><B>",$msg);
$msg=str_replace("[/RB]","</B></font>",$msg);
$msg=preg_replace("#\[Quote\]\s*(.*?)\s*\[/Quote\]#is","<br><B><U>������:</U></B><table width=80% border=0 cellpadding=5 cellspacing=1 style='padding: 5px; margin: 1px'><tr><td class=quote>$1</td></tr></table>",$msg);
$msg=preg_replace("#\[Code\]\s*(.*?)\s*\[/Code\]#is"," <br><B><U>PHP ���:</U></B><table width=80% border=0 cellpadding=5 cellspacing=1 style='padding: 5px; margin: 1px'><tr><td class=code >$1</td></tr></table>",$msg);

if ($smile==TRUE) {$i=count($smiles)-1; // �������� ��������� �������� �� ����������� ���� ���������
for($k=0; $k<$i; $k=$k+2) {$j=$k+1; $msg=str_replace("$smiles[$j]","<img src='smile/$smiles[$k].gif' border=0>",$msg);}}

$msg=str_replace("&lt;br&gt;","<br>",$msg);
$msg=preg_replace('#\[img(.*?)\](.+?)\[/img\]#','<img src="$2" border="0" $1>',$msg);

// ���� ��������� ���������� �����
if ($liteurl==TRUE) $msg = preg_replace ("/([\s>\]]+)(http|https|ftp|goper):\/\/([a-zA-Z0-9\.\?&=\;\-\/_]+)([\W\s<\[]+)/", "\\1<a href=\"\\2://\\3\" target=\"_blank\">\\2://\\3</a>\\4", $msg);

if ($tblstyle=="row1") $tblstyle="row2"; else $tblstyle="row1";

if (!isset($m1)) {
print "<table><tr><td><span class=nav>&nbsp;&nbsp;&nbsp;<a href=admin.php class=nav>$fname</a> <a href=admin.php?fid=$fid class=nav>$frname</a> <a href='admin.php?fid=$fid&id=$dt[7]' class=nav><strong>$dt[3]</strong></a></span></td></tr></table>";

print"<table class=forumline width=100% cellspacing=1 cellpadding=3><tr>
<th class=thLeft width=150 height=26 nowrap=nowrap>�����</th>
<th class=thRight nowrap=nowrap>���������</th>"; $m1="1"; }

print"</tr><tr height=150><td class=$tblstyle valign=top><span class=name><BR><center>";


// ���������: ��� �����?
if (!isset($youwr)) {if (strlen($dt[2])>5) print "$dt[0] "; else print"$dt[0] ";
$kuda=$fm-1; print" <a href='javascript:%20x()' onclick=\"DoSmilie('[b]$dt[0][/b], ');\" class=nav>".chr(149)."</a><BR><br>
<a name='m$fm' href='#m$kuda' onclick=\"window.open('tools.php?event=mailto&email=$dt[2]&name=$dt[0]','email','width=520,height=300,left=170,top=100')\"><img src='$fskin/ico_pm.gif' border=0 alt='��'></a><br><BR><small>$guest</small>";}


else {
$codename=urlencode($dt[0]);
print "<a name='m$fm' href='tools.php?event=profile&pname=$codename' class=nav>$dt[0]</a> <a href='javascript:%20x()' onclick=\"DoSmilie('[b]$dt[0][/b], ');\" class=nav>".chr(149)."</a><BR><BR><small>";
if (strlen($status)>2 & $dt[1]=="��" & isset($youwr)) print "$status"; else print"$users";
if (isset($reiting)) {if ($reiting>0) {echo'<BR>'; if (is_file("$fskin/star.gif")) {for ($ri=0;$ri<$reiting;$ri++) {print"<img src='$fskin/star.gif' border=0>";} } }}

if (isset($youavatar)) { if (is_file("avatars/$youavatar")) $avpr="$youavatar"; else $avpr="noavatar.gif";
print "<BR><BR><img src='avatars/$avpr'><BR> <!--
<a href='tools.php?event=profile&pname=$dt[0]'><img src='$fskin/profile.gif' alt='�������' border=0></a>
<a href='$site'><img src='$fskin/www.gif' alt='www' border=0></a><BR>
<a href='$icq'><img src='$fskin/icq.gif' alt='ICQ' border=0></a>
<a href='#' onclick=\"window.open('tools.php?event=mailto&email=$dt[3]&name=$dt[0]','email','width=520,height=300,left=170,top=100')\"><img src='$fskin/ico_pm.gif' alt='��'></a>-->";}
} // isset($youwr)

if (isset($youwr) and is_file("$datadir/userstat.dat")) { // ������ ��������� ����� ��� ���������! ;-)
if (isset($ulines[$userpn])) {
if (strlen($ulines[$userpn])>5) {
$ddu=explode("|",$ulines[$userpn]);
print"</small></span><br>
<div style='PADDING-LEFT: 17px' align=left class=gensmall>��� �������: $ddu[1]<br>
���������: $ddu[2]<br>
���������: $ddu[3] <A href='#' onclick=\"window.open('tools.php?event=repa&name=$dt[0]&who=$userpn','repa','width=500,height=190,left=100,top=100')\">-+</A><br>
��������������: $ddu[4]<br></span>"; }}}

print "
<br><br>IP: $dt[16] <br><a href='admin.php?badip&ip_get=$dt[16]'><B><font color=red>��� �� IP</font></B></a><br>
</span></td><td class=$tblstyle width=100% height=28 valign=top><table width=100% height=100%><tr valign=center><td><span class=postbody>$msg</span>";


//  ���� ����������� - ���� ���� �� ������� !!!
if ($fm==1 and is_file("$datadir/$id-vote.dat")) { // ���� �������� ���� ���
$vlines=file("$datadir/$id-vote.dat");
if (sizeof($vlines)>0) {$vitogo=count($vlines); $vi=1; $vdt=explode("|",$vlines[0]);

print"<FORM name=wrvote action='vote.php' method=POST target='WRGolos'>
<TABLE class=forumline cellSpacing=1 cellPadding=0 align=center border=0>
<TR><Th colspan=3 class=thHead><B>�����������: &nbsp;$vdt[0]&nbsp;</B></Th></TR>
<TR class=$tblstyle><TD class=$tblstyle>";

do {$vdt=explode("|",$vlines[$vi]);
print"&nbsp;&nbsp;&nbsp;&nbsp; <INPUT name='votec' type=radio value='$vi'> &nbsp; <B>$vdt[0]</B><br><br>";
$vi++; } while($vi<$vitogo);

print "<center><INPUT name='id' type=hidden value='$id'>
<INPUT type=submit value='�������������' onclick=\"window.open('vote.php','WRGolos','width=650,height=300,left=200,top=200,toolbar=0,status=0,border=0,scrollbars=0')\" border=0>
<br><br><A href='#' onclick=\"window.open('vote.php?rezultat&id=$id','WRGolos','width=650,height=300,left=200,top=200,toolbar=0,status=0,border=0,scrollbars=0')\" target='WRRezultGolos'>����������</A></center></FORM>
<TD align=right><table><tr><td width=10 bgcolor=#FF2244><B><a href='admin.php?fid=$fid&id=$id&vote=delete' title='������� �����������'>.X.</a></B></td></tr></table></TD><TR>
</TD></TR></TABLE>"; }} // ����� ����� �����������


print"</td></tr><TR><TD>";

// ���� �������˨� ���� � ��������� - �� ���������� ������ � ������ �� ���� ��� ��������
if ($dt[17]!="" and is_file("$filedir/$dt[17]")) {
$fsize=round($dt[14]/10.24)/100; echo'<fieldset style="width:30%; color:#008000"><legend>����:</legend>';
if (preg_match("/.(jpg|jpeg|bmp|gif|png)+$/is",$dt[17]))
print"<a href='$filedir/$dt[13]'><img border=0 src='$filedir/$dt[17]'></a><br> ($fsize ��.)</fieldset>"; }

// �������� ������� ���������
if (isset($youwr)) {if (strlen($youwr)>3) {print "<tr><td valign=bottom><span class=postbody>--------------------------------------------------<BR><small>$youwr</small>";}}

print"</td></tr></table></td></tr><tr>
<td class=row3 valign=middle align=center ><span class=postdetails>
<table><tr>
<!--
<td width=10 bgcolor=#22FF44><B><a href='admin.php?fid=$fid&id=$id&topicrd=$fm&page=$page#m$lm' title='�������������'>.P.</a></B></td>-->
<td width=10 bgcolor=#FF2244><B><a href='admin.php?fid=$fid&id=$id&topicxd=$fm&page=$page' title='�������'>.X.</a></B></td></tr></table>
<I>��������� # <B>$fm.</B></I></span></td>
<td class=row3 width=100% height=28 nowrap=nowrap><span class=postdetails>����������: <b>$dt[5]</b> - $dt[6]</span></td>
</tr><tr><td class=spaceRow colspan=2 height=1><img src=\"$fskin/spacer.gif\" width=1 height=1></td>";

} // ���� ������� ����������

} while($fm < $lm);

print"</tr></table> $pageinfo </span></td></tr></table>";



}
} // else if event !=""







if (isset($_GET['event'])) {


// ���������������� ������ - ����� ��������
if ($_GET['event']=="configure") {

if ($ktotut!=1) {exit("$back! ����������� ��������� �������� ��������� ������! ���� ����� ������� ������ - ����������� � ������!");}

if ($sendmail==TRUE) {$s1="checked"; $s2="";} else {$s2="checked"; $s1="";}
if ($sendadmin==TRUE) {$sa1="checked"; $sa2="";} else {$sa2="checked"; $sa1="";}
if ($statistika==TRUE) {$st1="checked"; $st2="";} else {$st2="checked"; $st1="";}
if ($antispam==TRUE) {$as1="checked"; $as2="";} else {$as2="checked"; $as1="";}
if ($newmess==TRUE) {$n1="checked"; $n2="";} else {$n2="checked"; $n1="";}
if ($cangutema==TRUE) {$ct1="checked"; $ct2="";} else {$ct2="checked"; $ct1="";}
if ($cangumsg==TRUE) {$cm1="checked"; $cm2="";} else {$cm2="checked"; $cm1="";}
if ($useactkey==TRUE) {$u1="checked"; $u2="";} else {$u2="checked"; $u1="";}
if ($liteurl==TRUE) {$lu1="checked"; $lu2="";} else {$lu2="checked"; $lu1="";}
if ($canupfile==TRUE) {$cs1="checked"; $cs2="";} else {$cs2="checked"; $cs1="";}
if ($smile==TRUE) {$sm1="checked"; $sm2="";} else {$sm2="checked"; $sm1="";}

if ($stop==TRUE) {$sp1="checked"; $sp2="";} else {$sp2="checked"; $sp1="";}
if ($antimat==TRUE) {$am1="checked"; $am2="";} else {$am2="checked"; $am1="";}
if ($random_name==TRUE) {$rn1="checked"; $rn2="";} else {$rn2="checked"; $rn1="";}

print "<center><B>����������������</b></font>
<form action=admin.php?event=config method=post name=REPLIER>
<table width=900 cellpadding=2 cellspacing=1 class=forumline><tr> 
<th class=thCornerL height=25 nowrap=nowrap>��������</th>
<th class=thTop nowrap=nowrap>��������</th></tr>
<tr><td class=row1>�������� �������</td><td class=row1><input type=text value='$fname' name=fname class=post maxlength=50 size=50></tr></td>
<tr><td class=row2 valign=top>��������<BR><B><small>������������ HTML-���� ���������!</small></td><td class=row2><textarea cols=55 rows=6 size=700 class=post name=fdesription>$fdesription</textarea></tr></td>
<tr><td class=row1>�-���� ��������������</td><td class=row1><input type=text value='$adminemail' class=post name=newadminemail maxlength=40 size=25></tr></td>
<tr><td class=row2>������ �������������� / ���������� *</td><td class=row1><input name=password type=hidden value='$password'><input class=post type=text value='�����' maxlength=10 name=newpassword size=15> &nbsp; .:. &nbsp; <input name=moderpass type=hidden value='$moderpass'><input class=post type=text value='�����' maxlength=10 name=newmoderpass size=15>(���������� � �����)</td></tr>
<tr><td class=row1><FONT COLOR=RED>���������� �����������: ��������� ������ ����������� �� ���������� ��������/����������?</FONT></td><td class=row1><input type=radio name=stop value=\"1\"$sp1/><B><font color=red> �� </font>&nbsp;&nbsp; <input type=radio name=stop value=\"0\"$sp2/> <font color=gren>���</font></B></tr></td>
<tr><td class=row2>�������� �������� ���������?</td><td class=row1><input type=radio name=sendmail value=\"1\"$s1/> ��&nbsp;&nbsp; <input type=radio name=sendmail value=\"0\"$s2/> ���</tr></td>
<tr><td class=row1>������ ������ ��������� � ����� ������������������ �������������?</td><td class=row1><input type=radio name=sendadmin value=\"1\"$sa1/> ��&nbsp;&nbsp; <input type=radio name=sendadmin value=\"0\"$sa2/> ���</tr></td>
<tr><td class=row2>���������� ���������� �� ������� ��������?</td><td class=row1><input type=radio name=statistika value=\"1\"$st1/> ��&nbsp;&nbsp; <input type=radio name=statistika value=\"0\"$st2/> ���</tr></td>
<tr><td class=row1>��������� ���� � ������ ������������?</td><td class=row1><input type=radio name=newmess value=\"1\"$n1/> ��&nbsp;&nbsp; <input type=radio name=newmess value=\"0\"$n2/> ���</tr></td>
<tr><td class=row2>����. ����� ����� / �������� ������� / �������� ����</td><td class=row1><input type=text value='$maxname' class=post name=newmaxname maxlength=2 size=10> &nbsp;&nbsp; .:. &nbsp;&nbsp; <input type=text value='$maxzag' class=post name=maxzag maxlength=2 size=10> &nbsp;&nbsp; .:. &nbsp;&nbsp; <input type=text value='$maxmsg' class=post maxlength=4 name=newmaxmsg size=10></tr></td>
<tr><td class=row1>������������� �������� / ����� ����</td><td class=row2><input type=radio name=antispam value=\"1\"$as1/> ��&nbsp;&nbsp; <input type=radio name=antispam value=\"0\"$as2/> ��� &nbsp;&nbsp; .:. &nbsp;&nbsp; <input type=text class=post value='$max_key' name=max_key size=4 maxlength=1> (�� 1 �� 9) ����</td></tr>
<tr><td class=row2>��������� ������ ��������� 2013</td><td class=row1>������: <input type=text value='$antispam2012v' class=post name=antispam2012v maxlength=40 size=20> &nbsp;&nbsp; .:. &nbsp;&nbsp; �����: <input type=text value='$antispam2012o' class=post name=antispam2012o maxlength=30 size=20></tr></td>
<tr><td class=row2>�������� / ���������� �� ��������</td><td class=row2><input type=text value='$qqmain' class=post maxlength=2 name=newqqmain size=11> &nbsp; .:. &nbsp; <input type=text value='$qq' class=post maxlength=2 name=newqq size=11></tr></td>
<tr><td class=row1>��������� ������� / ��������� ��������� ������ �����?</td><td class=row1>�: <input type=radio name=cangutema value=\"1\"$ct1/> ��&nbsp;&nbsp; <input type=radio name=cangutema value=\"0\"$ct2/> ��� .:. �: <input type=radio name=cangumsg value=\"1\"$cm1/> ��&nbsp;&nbsp; <input type=radio name=cangumsg value=\"0\"$cm2/> ��� </tr></td>
<tr><td class=row1>�������� / ��������� ������������ ������?</td><td class=row1><input type=radio name=smile value=\"1\"$sm1/> �������� &nbsp;&nbsp; <input type=radio name=smile value=\"0\"$sm2/> ���������</td></tr>
<tr><td class=row1>�������� GMT ������������ ������� ��������</td><td class=row1><input class=post type=text value='$deltahour' maxlength=2 name=deltahour size=15> (GMT + XX �����)</td></tr>
<tr><td class=row2>����� � ������� �����������</td><td class=row1><input type=text value='$datadir' class=post maxlength=20 name='datadir' size=10> &nbsp;&nbsp; �� ��������� - <B>./data</B></td></tr>
<tr><td class=row2>����� ��� �������� ������</td><td class=row1><input type=text value='$filedir' class=post maxlength=20 name='filedir' size=10> &nbsp;&nbsp; �� ��������� - <B>./load</B></td></tr>
<tr><td class=row1>������������ ������ ����� � ������</td><td class=row1><input type=text value='$max_upfile_size' class=post maxlength=6 name='max_upfile_size' size=10></td></tr>
<tr><td class=row2>���� �����������</td><td class=row1><select class=input name=fskin>";

$path = '.'; // ���� �� �����. '.' - ������� �����
if ($handle = opendir($path)) {
while (($file = readdir($handle)) !== false)
if (is_dir($file)) { 
$stroka=stristr($file, "images"); if (strlen($stroka)>"6") 
{print "$stroka - str $file <BR>";
$tskin=str_replace("images-", "���� - ", $file);
if ($fskin==$file) $marker="selected"; else $marker="";
print"<option $marker value=\"$file\">$tskin</option>";}
}
closedir($handle); } else echo'������!';

echo"</select></td></tr>
<input type=hidden name=random_name value=\"1\"$rn1/>
<input type=hidden value='$repaaddfile' class=post name=repaaddfile maxlength=2 size=6>
<input type=hidden value='$repaaddmsg' class=post name=repaaddmsg maxlength=2 size=6>
<input type=hidden value='$repaaddtem' class=post name=repaaddtem maxlength=2 size=6>
<input type=hidden name=antimat value=\"1\"$am1/>
<input type=hidden value='$uq' maxlength=2 class=post name=uq size=11>
<input type=hidden value='$guest' class=post maxlength=25 name=newguest size=22>
<input type=hidden value='$users' class=post maxlength=25 name=newusers size=22>
<input type=hidden name=useactkey value=\"1\"$u1/>
<input type=hidden name=liteurl value=\"1\"$lu1/>
<input type=hidden value='$max_file_size' class=post maxlength=6 name='max_file_size' size=10>
<input type=hidden name=canupfile value=\"1\"$cs1/>
<tr><td class=row1>������ (����������� � ���)<br> - ������� ��� ������ ***</td><td class=row1><table width=300><TR><TD>\r\n";
if (isset($smiles) and $smile==TRUE) {$i=count($smiles);
for($k=0; $k<$i; $k=$k+2) {
$j=$k+1; if ($k!=($i-1) and is_file("smile/$smiles[$k].gif"))
print"<img src='smile/$smiles[$k].gif' border=0> <input type=hidden name=newsmiles[$k] value='$smiles[$k]'><input type=text value='$smiles[$j]' maxlength=15 name=newsmiles[$j] size=5> \r\n"; } }


echo'
</td></tr></table>
</td></tr><tr><td class=row1 colspan=2><BR><center><input type=submit class=mainoption value="��������� ������������"></form></td></tr></table>
<center><br>* ���� ������ �������� ������ - ������� ����� <B>"�����"</B> � ������� ����� ������.<br> ���������� ������������ ������ ����� �/��� �����.';
}







}




if (isset($_GET['event'])) { if ($_GET['event']=="blockip") { // - ���������� �� IP, ������� �����

$itogo=0;
if (is_file("$datadir/bad_ip.dat")) { $lines=file("$datadir/bad_ip.dat"); $i=count($lines); $itogo=$i;
if ($i>0) {

echo'<table width=100% border=0 cellpadding=1 cellspacing=0><TR><TD>
<table border=0 width=100% cellpadding=2 cellspacing=1 class=forumline><tr> 
<th class=thCornerL width=50 height=25 nowrap=nowrap>.X.</th>
<th class=thCornerL width=150>IP</th>
<th class=thCornerL >������������</th>
</tr>';

do {$i--; $idt=explode("|", $lines[$i]);
   print"<TR bgcolor=#F7F7F7><td width=10 align=center><table><tr><td width=10 bgcolor=#FF2244><B><a href='admin.php?delip=$i'>.X.</a></B></td></tr></table></td><td>$idt[0]</td><td>$idt[1]</td></tr>";
} while($i > "0");
} else print"<br><br><H2 align=center>��������������� IP-������ �����������</H2><br>";
} else print"<br><br><H2 align=center>��������������� IP-������ �����������</H2><br>";
print"</table><br><CENTER><form action='admin.php?badip' method=POST>
������ IP �������! &nbsp; <input type=text style='FONT-SIZE: 14px; WIDTH: 110px' maxlength=15 name=ip> ������������: <input type=text style='FONT-SIZE: 14px; WIDTH: 420px' value='�� ���������� ������������� ���������! �� ����!' maxlength=50 name=text> 
<input type=submit value=' �������� '></form><br><br>*������� IP ���������, �� ������� ������ ������� � ������ ��������.
<br><BR>����� �������� ������������� - <B>$itogo</B><BR><BR></td></tr></table>"; exit;}}



?><br>
<center><font size=-2><small>Powered by <a href="http://www.wr-script.ru" title="������ �����������" class="copyright">WR-Foto</a> &copy; 1.1<br></small></font></center>
</body>
</html>
