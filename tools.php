<? // WR-foto v 1.2  //  02.08.15 �.  //  Miha-ingener@yandex.ru

error_reporting (E_ALL); //error_reporting(0);
ini_set('register_globals','off');// ��� ������� �������� ��� ���� ��������� php

include "data/config.php";

// �������� ����� ���������� �� ������� ������. ����� ����� ������������.

$forum_lock="0"; // ��������� ���������� ���/���������
$random_name="0"; // ��� �������� ����� ������������ ��� ��� ��������� �������?
$repaaddmsg="1"; // ������� ����� ��������� ��������� �� ���������� ���������?
$repaaddtem="4"; // ������� ����� ��������� ��������� �� ���������� ����?
$repaaddfile="7"; // ������� ����� ��������� ��������� ��� �������� �����?
$sendmail="1"; // �������� �������� ���������? 1/0
$admin_send="0"; // ������ ������ ��������� � ����� ������������������ �������������? 1/0
$statistika="1"; // ���������� ���������� �� ������� ��������? 1/0
$antimat="0"; // �������� ������� ��/��� - 1/0
$antispam="0"; // ������������� ��������
$antispam2012="0"; // ������������� �������� 2012
$antispam2012v="����� ������ ���?"; // ������ ��������� 2012
$antispam2012o="2014"; // ����� ��������� 2012
$max_key="4"; // ���-�� �������� � ���� �����������
$rand_key="6855"; // ��������� ����� ��� �����������
$newmess="1"; // ��������� ���� � ������ ����������� ������?
$guest_name="�����"; // ��� �������� �� �����-�� �������������
$user_name="�������� WR-������"; // ��� �������� �����-��
$g_add_tema="1"; // ��������� ������ ��������� ����? 1/0
$g_add_msg="1"; // ��������� ������ ��������� ���������? 1/0
$activation="1"; // ��������� ��������� ����� ����� ��� �����������? 1/0
$maxname="35"; // ������������ ���-�� �������� � �����
$maxzag="70"; // ����������� ���-�� �������� � ��������� ����
$maxmsg="5000"; // ������������ ���������� �������� � ���������
$tem_onpage="16"; // ���-�� ������������ ��� �� �������� (15)
$msg_onpage="11"; // ���-�� ������������ ��������� �� ������ �������� (10)
$uq="50"; // �� ������� ������� �������� ������ ����������
$specblok1="1"; // �������� ���� 15-� ����� ����������� ���?
$specblok2="1"; // �������� ���� 10 ����� �������� �������������?
$nosssilki="0"; // ��������� ������ ��������� ��������� �� ��������?
$liteurl="0";// ������������ ���? 1/0
$max_f_size="102400"; // ������������ ������ ������� � ������
$datadir="./data"; // ����� � ������� ������
$showsmiles="1";// ��������/��������� ����������� ������
$can_up_file="1"; // ��������� �������� ���� 0 - ���, 1 - ������ ������������������
$filedir="./files"; // ������� ���� ����� ������� ����
$max_upfile_size="1048576"; // ������������ ������ ����� � ������
$forum_skin="images-green"; // ������� ���� ������

$avatardir="./avatars"; // ������� ���� ����������� �������
$maxfsize=round($max_file_size/10.24)/100;
$valid_types=array("gif","jpg","png","jpeg"); // ���������� ����������

// ���������� URL ������
$host=$_SERVER["HTTP_HOST"]; $self=$_SERVER["PHP_SELF"]; $forum_url=str_replace('tools.php','',"http://$host$self");

// ������� �������� ����������� �����. ����������: addtop();
function addtop() { global $wrfname,$forum_skin,$date,$time;

// ���� � ����� wrfcookies ����� ������� ���
if (isset($_COOKIE['wrfcookies'])) {$wrfc=$_COOKIE['wrfcookies']; $wrfc=htmlspecialchars($wrfc); $wrfc=stripslashes($wrfc); $wrfc=explode("|", $wrfc); $wrfname=$wrfc[0];} else {$wrfname=null; $wrfpass=null;}

echo'<TD align=right>';

if ($wrfname!=null) {
$codename=urlencode($wrfname); // �������� ��� � ����������, ��� ��������� ���������� �������� ����� ����� GET-������.
print "<a href='tools.php?event=profile&pname=$codename' class=mainmenu><img src=\"$forum_skin/icon_mini_profile.gif\" border=0 hspace=3 />��� �������</a>&nbsp;&nbsp;<a href='index.php?event=clearcooke' class=mainmenu><img src=\"$forum_skin/ico-login.gif\" border=0 hspace=3 />����� [<B>$wrfname</B>]</a>";}

else {print "<span class=mainmenu>
<a href='tools.php?event=reg' class=mainmenu><img src=\"$forum_skin/icon_mini_register.gif\" border=0 hspace=3 />�����������</a>&nbsp;&nbsp;
<a href='tools.php?event=login' class=mainmenu> <img src=\"$forum_skin/buttons_spacer.gif\" border=0 hspace=3>����</a></td>";}

if (is_file("$forum_skin/tiptop.html")) include("$forum_skin/tiptop.html"); // ���������� ���������� � ��������

print"</span></td></tr></table></td></tr></table><span class=gensmall>�������: $date - $time";
return true;}


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


function unreplacer($text) { // ������� ������ ������������ ����� ������ �� �������
$text=str_replace("&lt;br&gt;","<br>",$text); return $text;}


function nospam() { global $max_key,$rand_key,$antispam2012,$antispam2012v; // ������� �������� 2011+2012
if (array_key_exists("image", $_REQUEST)) { $num=replacer($_REQUEST["image"]);
for ($i=0; $i<10; $i++) {if (md5("$i+$rand_key")==$num) {imgwr($st,$i); die();}} }
$xkey=""; mt_srand(time()+(double)microtime()*1000000);
$dopkod=mktime(0,0,0,date("m"),date("d"),date("Y")); // ���.���: �������� ������ 24 ����
$stime=md5("$dopkod+$rand_key");// ���.���
echo'<table cellspacing=0 cellpadding=0><tr height=30><TD>�������� ���:</TD>';
$nummax=0; for ($i=0; $i<=$max_key; $i++) {
$snum[$i]=mt_rand(0,9); $psnum=md5($snum[$i]+$rand_key+$dopkod);
$secret=mt_rand(0,1); $styles='bgcolor=#FFFF00';
if ($nummax<3) { if ($secret==1 or $i==0) {$styles='bgcolor=#77C9FF'; $xkey=$xkey.$snum[$i]; $nummax++;}}
echo "<td width=20 $styles><img src=antispam2013.php?image=$psnum border=0 alt=''>\n<img src=antispam2013.php?image=$psnum height=1 width=1 border=0></td>\r\n";}
$xkey=md5("$xkey+$rand_key+$dopkod"); //����� + ���� �� data/config.php + ��� ���������� ����� 24 ����
print"<td><input name='usernum' class=post type='text' maxlength=$nummax size=6> (������� �����, ������� �� <font style='font-weight:bold'> ������� ����</font>)
<input name=xkey type=hidden value='$xkey'>
<input name=stime type=hidden value='$stime'>
</td></tr></table>";
if ($antispam2012==TRUE) print"����� �� ������: <input name='antispam2012o' class=post type='text' maxlength=20 size=10>($antispam2012v)";
return; }



// ������� ������������ ��� ����������� ��������
function get_dir($path = './', $mask = '*.php', $mode = GLOB_NOSORT) {
 if ( version_compare( phpversion(), '4.3.0', '>=' ) ) {if ( chdir($path) ) {$temp = glob($mask,$mode); return $temp;}}
return false;}



if (isset($_GET['rss'])) { // ���������� ����� RSS
$forum_name=replacer($forum_name); $forum_info=replacer($forum_info);
$forum_url.="index.php"; $adminemail=replacer($adminemail);

// ��������� RSS-���� 
echo "<?xml version=\"1.0\" encoding=\"windows-1251\"?>
<rss version=\"2.0\">
 <channel>
   <title>RSS ����� ��������: $forum_name</title>
   <link>$forum_url</link>
   <description>$forum_info</description>
   <language>Russian</language>
   <copyright>Rootman</copyright>
   <managingEditor>$adminemail</managingEditor>
   <webMaster>$adminemail</webMaster>
   <generator>WR-Forum 2.0 RSS-module</generator>
   <lastBuildDate>$date $time</lastBuildDate>
";

// ������ �������� � �� ����� �� �����
$lines=file("$datadir/news.dat"); $itogo=sizeof($lines); $x=$itogo-1;

do { $dt=explode("|",replacer($lines[$x]));
$xdate=date("r",$dt[4]); // ������������ ���� � ������ ����� RSS
$fid=$dt[2]; $id=$dt[3];
$zag=$dt[5];
$name=$dt[8];
$msg=$dt[14];
$msg=str_replace("&","&amp;",$msg); $msg=str_replace('\"','"',$msg);
$msg=str_replace("[b]","<p>",$msg); $msg=str_replace("[/b]","</p>",$msg);
$msg=str_replace("[RB]","<p>",$msg); $msg=str_replace("[/RB]","</p>",$msg);
$msg=str_replace("[Code]","<p>",$msg); $msg=str_replace("[/Code]","</p>",$msg);
$msg=str_replace("[Quote]","<p>",$msg); $msg=str_replace("[/Quote]","</p>",$msg);
$msg=str_replace("<br>","\r\n", $msg);
$msg=str_replace("&amp;lt;br&amp;gt;","<p></p>", $msg);
$msg="<![CDATA[$msg]]>";

echo "
<item>
 <title>$zag</title>
  <link>$forum_url?id=$fid$id</link>
   <description>&lt;b&gt;$name �����:&lt;/b&gt; &lt;br&gt;&lt;br&gt; $msg &lt;br&gt;&lt;br&gt;</description>
   <author>$name</author>
  <comments>$forum_url?id=$fid$id</comments>
 <pubDate>$xdate</pubDate>
</item>
";
$x--;
} while ($x>=0);
echo "
 </channel>
</rss>";
exit;} // ����� ����� RSS





// �Ѩ, ��� �������� ��� ������� ���������� $_GET['event']
if(isset($_GET['event'])) {



if ($_GET['event']=="login") { // ���� �� ����� ����������
$frname="���� �� ����� .:. "; $frtname="";
//include("$forum_skin/top.html"); addtop(); // ���������� ����� ������

echo '<BR><BR><BR><BR><center>
<table border=1 cellSpacing=1><TR><TD class=row2>
<TABLE class=bakfon cellPadding=4 cellSpacing=1>

<FORM action="tools.php?event=regenter" method=post>
<TR class=toptable><TD align=middle colSpan=2><B>���� �� �����</B></TD></TR>
<TR class=row1><TD>���:</TD><TD><INPUT name=name class=post></TD></TR>
<TR class=row2><TD>������:</TD><TD><INPUT type=password name=pass class=post></TD></TR>
<TR class=row1><TD colspan=2><center><INPUT type=submit class=button value=�����></TD></TR></TABLE></FORM> </TD></TR></TABLE>
<BR><BR><BR>
<table border=1 cellSpacing=1><TR><TD class=row2>
<TABLE class=bakfon cellPadding=3 cellSpacing=1>
<FORM action="tools.php?event=givmepassword" method=post>
<TR class=toptable><TD align=middle colSpan=3><B>������ ������? ������� �� �����:</B></TD></TR>
<TR class=row1><TD><B>��� �����:</B> <font color=red>*</font></TD><TD><INPUT name=myemail class=post style="width: 170px"></TD>
<TR class=row1><TD><B>��� (���):</B></TD><TD><INPUT name=myname class=post style="width: 170px"></TD></TR>
<TR><TD colspan=2 align=center><INPUT type=submit class=button style="width:150" value="������� ������"></TD></TR>
<TR><TD colspan=3><small><font color=red>*</font> �� ��� ����������� ����� ����� �������<br> ���������� ��� �������������� ������� ������.</TD></TR></TABLE>
</FORM></TD></TR></TABLE><BR><BR><BR><BR><BR>
</TD></TR></TABLE>
</TD></TR></TABLE>'; exit;}


// ��������� - ���� ������: ��� 1
if ($_GET['event']=="repa") {

if (!isset($_GET['name'])) exit("��� ������ ���������� name."); $name=replacer($_GET['name']);

// ���� ����� ��� - �����, ���� ���� ���� � ����� ����� ����� - �����.
if (!isset($_COOKIE['wrfcookies'])) exit("<html><head><title>��������� ���������</title></head><body><center><br><br><br>�������� � ��������� ��������� ����� ����������� ������ �������� ������!");
else { $wrfc=$_COOKIE['wrfcookies']; $wrfc=htmlspecialchars($wrfc); $wrfc=stripslashes($wrfc); $wrfc=explode("|", $wrfc); $wrfname=$wrfc[0];
if ($wrfname===$name) print"<B>$back. �� �������� ������<br> <font color=red>��������� ��������� ���� ���������!</font><br>";

else { print "<html><head><title>��������� ��������� ���������: $name</title></head><body leftMargin=0 topMargin=0 rightMargin=0>
<center><table cellpadding=0 cellspacing=8><TR><FORM action='tools.php?event=repasave' method=post>
<TD colspan=7 align=center><B>��������� ��������� ��������� $name</B></TD></TR><TR height=40>
<TD bgcolor=#880003><font size=+2 color=white>-5<INPUT name=repa type=radio value='-5'></TD>
<TD bgcolor=#FF2025><font size=+2 color=white>-2<INPUT name=repa type=radio value='-2'></TD>
<TD bgcolor=#FFB7B9><font size=+2 color=white>-1<INPUT name=repa type=radio value='-1'></TD>
<TD bgcolor=#FFFF00><font size=+2 color=#FF8040>0<INPUT name=repa checked type=radio value='0'></TD>
<TD bgcolor=#A4FFAA><font size=+2 color=white>+1<INPUT name=repa type=radio value='+1'></TD>
<TD bgcolor=#00C10F><font size=+2 color=white>+2<INPUT name=repa type=radio value='+2'></TD>
<TD bgcolor=#00880B><font size=+2 color=white>+5<INPUT name=repa type=radio value='+5'></TD></TR>
<INPUT type=hidden name=name value=$name>
<TR><TD colspan=7><B>�������:</B> <INPUT type=text name=pochemu size=45 value=''><INPUT type=submit value=���������></td></TR>
</TABLE></FORM>";}

if (is_file("$datadir/repa.dat")) { // ���� � ����� repa.dat ���� �� ���� ����� � �������, ���� ����
$file="$datadir/repa.dat"; $lines=file("$file"); $i=count($lines);
print"<table border=1 cellpadding=2 cellspacing=0 width=100%><TR><TD colspan=5 align=center><B>��������� ��������� ��������� $name</B></td></tr>
<TR align=center><TD>�����</TD><TD>���</TD><TD>����</TD><TD width=55%>�������</TD></TR>";
do {$i--; $dt=explode("|",$lines[$i]);
if (strlen($dt[3])>1) $dt[3]="<a href='tools.php?event=profile&pname=$dt[3]' target=_blank>$dt[3]</a>"; else $dt[3]="����� ������";
if ($dt[1]>0) $dt[1]="<TD align=center bgcolor=#B7FFB7><B>$dt[1]"; else $dt[1]="<TD align=center bgcolor=#FF9F9F><B>$dt[1]";
if ($dt[2]==$name) {$dt[0]=date("d.m.y � H:i",$dt[0]); print"<TR><TD align=center><small>$dt[0]</small></TD><TD align=center><B>$dt[3]</B></TD>$dt[1]</B></TD><TD><small>$dt[4]</small></TD></TR>";}
} while($i>0);
echo'</table>'; } // ���� ���� ���� repa.dat
exit; }}


// ��������� - ����������: ��� 2
if ($_GET['event']=="repasave") {

if (isset($_COOKIE['wrfcookies'])) {$wrfc=$_COOKIE['wrfcookies']; $wrfc=htmlspecialchars($wrfc); $wrfc=stripslashes($wrfc); $wrfc=explode("|", $wrfc); $wrfname=$wrfc[0];} else exit('������ ��������� ������ ����� �������� ���������!');
if (!isset($_POST['name'])) exit("��� ������ ���������� name."); $name=replacer($_POST['name']);
if (isset($_POST['repa'])) $repa=$_POST['repa']; else exit("��� ������ ���������� repa");
if (isset($_POST['pochemu'])) $pochemu=$_POST['pochemu']; else exit("������� ������� ����� ���������");

if (!is_numeric($repa)) exit("<B>$back. ������� ������. �� ��������, ����!");
if ($repa>5 or $repa<-5) exit("<B>$back. ������� ������. ���� ����� ������ ������ �� +-5 �������. �� ��������, ����!");
if (strlen($pochemu)<1 or strlen($pochemu)>150) exit("<B>$back. ����� ������� ������ ���� ������! � ���� �� ����� 150 ��������!");

$today=time();
// ���� ��������� + � ��������� �����
//���_�����|���|���������|���������|�������������� �/5|����� ��������� ��� ������ ������� � UNIX �������|||
$ufile="$datadir/userstat.dat"; $ulines=file("$ufile"); $ui=count($ulines)-1; $ulinenew=""; $username="";
$ip=$_SERVER['REMOTE_ADDR']; // ���������� IP �����
// ���� ����� �� ����� � ����� userstat.dat, ���� ������� ���������� �� ����, ���������
for ($i=0;$i<=$ui;$i++) {$udt=explode("|",$ulines[$i]);
if ($udt[2]==$name) {$udt[7]=$udt[7]+$repa; if (strlen($udt[1])>5) {$next=$today-$udt[1]; sleep(1); 
if ($ip==$udt[10]) exit("C ������ IP-������ ��� ���� ����������� �� ��������� ����� ���������. �������� ��������� ����� ��������� ��� ������ �� ��� ���, ���� ���-������ � ������ IP �� ����������� �� ����!");
if ($next<180) {$last=180-$next; exit("<B>$back. ������� ����� ���������<br> ��� ��� ������ ������ ���.<br> <font color=red>�������� $last ������.</font> </B>");}}
$ulines[$i]="$udt[0]|$today|$udt[2]|$udt[3]|$udt[4]|$udt[5]|$udt[6]|$udt[7]|$udt[8]|$udt[9]|$ip|$udt[11]|\r\n";
}
$ulinenew.="$ulines[$i]";}

// ���������� ������ � ����
$fp=fopen("$ufile","w");
flock ($fp,LOCK_EX);
fputs($fp,"$ulinenew");
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);

// ���������� ������ � ���� REPA.DAT
//���� � UNIX-�������|������� ������|���_����_������|���_�����|�������||||
$fp=fopen("$datadir/repa.dat","a+");
flock ($fp,LOCK_EX);
fputs($fp,"$today|$repa|$name|$wrfname|$pochemu||||\r\n");
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);

exit("<div align=center><BR><BR><BR>������� <B>�������</B> ����������.<BR><BR><BR><a href='' onClick='self.close()'><b>������� ����</b></a></div>");
}





// �������� ��������� �����
if ($_GET['event']=="mailto") {

if ($sendmail!=TRUE) exit("$back. <center><B>��������, �� ������� �������� ����� ������������� ���������������!<BR><BR><BR><a href='' onClick='self.close()'>������� ����</b></a></center>");

if (!isset($_POST['email'])) exit("��� ������ ���������� email.");
if (!isset($_POST['name'])) exit("��� ������ ���������� name.");
$uemail=replacer($_POST['email']); $uname=replacer($_POST['name']);
$id=""; $fid=""; if (isset($_POST['id'])) {$id=replacer($_POST['id']); if (strlen($id)>0) $fid=substr($id,0,3);}

print "<html><head><meta http-equiv='Content-Type' content='text/html; charset=windows-1251'><meta http-equiv='Content-Language' content='ru'>
<meta name=\"Robots\" content=\"noindex,nofollow\">
<title>����������� ��������� ������ ����������</title></head><body topMargin=5>
<center><TABLE bgColor=#aaaaaa cellPadding=2 cellSpacing=1 width=502>
<FORM action='tools.php?event=mailtogo' method=post>
<TBODY><TR><TD align=middle bgColor=#cccccc colSpan=2>���������� ���������: <B>$uname</B></TD></TR>

<TR bgColor=#ffffff><TD>&nbsp; ���� ���:<FONT color=#ff0000>*</FONT> <INPUT name=name style='FONT-SIZE: 14px; WIDTH: 150px'>

� E-mail:<FONT color=#ff0000>*</FONT> <INPUT name=email style='FONT-SIZE: 14px; WIDTH: 180px'></TD></TR>

<TR bgColor=#ffffff><TD>&nbsp; ���������:<FONT color=#ff0000>*</FONT><br>
<TEXTAREA name=msg style='FONT-SIZE: 14px; HEIGHT: 150px; WIDTH: 494px'></TEXTAREA></TD></TR>
<INPUT type=hidden name=uemail value=$uemail><INPUT type=hidden name=uname value=$uname>
<TR bgColor=#ffffff><TD>";

if ($antispam==TRUE and !isset($wrfname)) nospam(); // �������� !

if ($id!="") print"<INPUT type=hidden name=id value=$id><INPUT type=hidden name=fid value=$fid>";

echo'<TR><TD bgColor=#FFFFFF colspan=2><center><INPUT type=submit value=���������></TD></TR></TBODY></TABLE></FORM>'; 
exit; }


// ��� 2 �������� ��������� ������������
if ($_GET['event']=="mailtogo") {
$name=replacer($_POST['name']);
$email=replacer($_POST['email']);
$msg=replacer($_POST['msg']);
if (isset($_POST['fid'])) $fid=replacer($_POST['fid']);
if (isset($_POST['id'])) $id=replacer($_POST['id']);
$uname=replacer($_POST['uname']);
$uemail=replacer($_POST['uemail']);

//--�-�-�-�-�-�-�-�--�������� ����--
if ($antispam==TRUE) {
if (!isset($_POST['usernum']) or !isset($_POST['xkey']) or !isset($_POST['stime']) ) exit("������ �� ����� �� ���������!");
$usernum=replacer($_POST['usernum']); $xkey=replacer($_POST['xkey']); $stime=replacer($_POST['stime']);
$dopkod=mktime(0,0,0,date("m"),date("d"),date("Y")); // ���.���. �������� ������ 24 ����
$usertime=md5("$dopkod+$rand_key");// ���.���
$userkey=md5("$usernum+$rand_key+$dopkod");
if (($usertime!=$stime) or ($userkey!=$xkey)) exit("����� ��������� ���!");}

if (!preg_match('/^([0-9a-zA-Z]([-.w]*[0-9a-zA-Z])*@([0-9a-zA-Z][-w]*[0-9a-zA-Z].)+[a-zA-Z]{2,9})$/si',$email) and strlen($email)>30 and $email!="") exit("$back � ������� ���������� E-mail �����!</B></center>");
if (!preg_match('/^([0-9a-zA-Z]([-.w]*[0-9a-zA-Z])*@([0-9a-zA-Z][-w]*[0-9a-zA-Z].)+[a-zA-Z]{2,9})$/si',$uemail) and strlen($uemail)>30 and $uemail!="") exit("$back � ������������ ����� �������������� �����!</B></center>");
if ($name=="") exit("$back �� �� ����� ��� ���!</B></center>");
if ($msg=="") exit("$back �� �� ����� ���������!</B></center>");

$text="$name|$msg|$uname|$email|";
$text=str_replace("\r\n","<br>",$text);
$exd=explode("|",$text); $name=$exd[0]; $msg=$exd[1]; $uname=$exd[2]; $email=$exd[3];

$headers=null; // ��������� ��� �������� �����
$headers.="From: $name $email\n";
$headers.="X-Mailer: PHP/".phpversion()."\n";
$headers.="Content-Type: text/html; charset=windows-1251";

// �������� ��� ���������� � ���� ������

$allmsg="<html><head>
<meta http-equiv='Content-Type' content='text/html; charset=windows-1251'><meta http-equiv='Content-Language' content='ru'>
</head><body>
<BR><BR><center>$uname, ��� ��������� ���������� ��� �� ���������� ������ <BR><B>$forum_name</B><BR><BR>
<table cellspacing=0 width=700 bgcolor=navy><tr><td><table cellpadding=6 cellspacing=1 width='100%'>
<tr bgcolor=#F7F7F7><td width=130 height=24>���</td><td>$name</td></tr>
<tr bgcolor=#F7F7F7><td>E-mail:</td><td><font size='-1'>$email</td></tr>
<tr bgcolor=#F7F7F7><td> ���������:</td><td><BR>$msg<BR></td></tr>
<tr bgcolor=#F7F7F7><td>���� �������� ���������:</td><td>$time - <B>$date �.</B></td></tr>
<tr bgcolor=#F7F7F7><td>������� �� ������� ��������:</td><td><a href='$forum_url'>$forum_url</a></td></tr>
</table></td></tr></table></center><BR><BR>* ������ ������ ������������� � ���������� �������, �������� �� ���� �� �����.
</body></html>";

mail("$uemail", "��������� �� ���������� ������ ($forum_name) �� $name ", $allmsg, $headers);
exit('<div align=center><BR><BR><BR>���� ��������� <B>�������</B> ����������.<BR><BR><BR><a href="#" onClick="self.close()"><b>������� ����</b></a></div>'); }




// �������� �����/������ � ���� �� �����
if ($_GET['event']=="regenter") {
if (!isset($_POST['name']) & !isset($_POST['pass'])) exit("$back ������� ��� � ������!");
$name=str_replace("|","I",$_POST['name']); $pass=replacer($_POST['pass']);
$name=replacer($name); $name=strtolower($name);
if (strlen($name)<1 or strlen($pass)<1) exit("$back �� �� ����� ��� ��� ������!");

// �������� �� ���� ������������� � ������� ������
$lines=file("$datadir/user.php"); $i=count($lines); $regenter=FALSE;
$pass=md5("$pass");
do {$i--; $rdt=explode("|",$lines[$i]);
if (isset($rdt[1])) { // ���� ������� �� �����
if ($name===strtolower($rdt[2]) & $pass===$rdt[3]) {
if ($rdt[16]==FALSE) exit("$back. ���� ������� ������ �� <a href='tools.php?event=reg3'>������������</a>. ��� ��������� ��� ���������� ������� �� ������, ������� ������ ������ ��� �� �����.");
$regenter=TRUE;
$tektime=time();
$wrfcookies="$rdt[2]|$rdt[3]|$tektime|$tektime|";
setcookie("wrfcookies", $wrfcookies, time()+1728000);
}} // if-�

} while($i > "1");

if ($regenter==FALSE) exit("$back ��� ������ <B>�� �����</B>!</center>");
Header("Location: index.php");
}








// ����������� ����� ��� 2!! �������� �� ��� ������������� � ���������� � ��
if ($_GET['event']=="regnxt") {

if (!isset($_POST['name']) & !isset($_POST['pass'])) exit("$back ������� ��� � ������!");
$name=str_replace("|","I",$_POST['name']); $pass=str_replace("|","I",$_POST['pass']); $dayreg=$date;
$name=trim($name); // �������� ���������� ������� 

if (isset($_POST['email'])) $email=$_POST['email']; else $email="";
$email=strtolower($email);

//--�-�-�-�-�-�-�-�--�������� ����--
if ($antispam==TRUE) {
if (!isset($_POST['usernum']) or !isset($_POST['xkey']) or !isset($_POST['stime']) ) exit("������ �� ����� �� ���������!");
$usernum=replacer($_POST['usernum']); $xkey=replacer($_POST['xkey']); $stime=replacer($_POST['stime']);
$dopkod=mktime(0,0,0,date("m"),date("d"),date("Y")); // ���.���. �������� ������ 24 ����
$usertime=md5("$dopkod+$rand_key");// ���.���
$userkey=md5("$usernum+$rand_key+$dopkod");
if (($usertime!=$stime) or ($userkey!=$xkey)) exit("����� ��������� ���!");

// �������� 2012!
if ($antispam2012==TRUE) { $ao=replacer($_POST['antispam2012o']);
if (strtolower($antispam2012o)!=strtolower($ao) or strlen($ao)<1) exit("����� ��������� ����� �� ������!");}
}

if (preg_match("/[^(\\w)|(\\x7F-\\xFF)|(\\-)]/",$name)) exit("$back ���� ��� �������� ����������� �������. ��������� ������� � ���������� �����, ����� � �������������!!.");
if ($name=="" or strlen($name)>$maxname) exit("$back ���� ��� ������, ��� ��������� $maxname ��������!</B></center>");
if ($pass=="" or strlen($pass)<1 or strlen($pass)>$maxname) exit("$back �� �� ����� ������. ������ �� ������ ���� ������.</B></center>");
if(!preg_match("/^[a-z0-9\.\-_]+@[a-z0-9\-_]+\.([a-z0-9\-_]+\.)*?[a-z]+$/is", $email) or $email=="" or strlen($email)>40) exit("$back � ������� ���������� E-mail �����!</B></center>");
if (isset($_POST['pol'])) $pol=$_POST['pol']; else $pol=""; if ($pol!="1") $pol="0";

$email=str_replace("|","I",$email);

$key=mt_rand(100000,999999); if ($activation==FALSE) $key=""; // ������� ��������� ��� ���������? ���� �� ��������� - ��������

$rn=mt_rand(10000,99999); $tektime=time();
$pass=replacer($pass); $ps=md5("$pass");
$text="$rn|$tektime|$name|$ps|0|$email|$pol||0|||||||$key|0|";
$text=replacer($text); $exd=explode("|",$text); $name=$exd[2]; $email=$exd[5];
$ip=$_SERVER['REMOTE_ADDR']; // ���������� IP �����

if ($name===$pass) exit("$back. � ����� ����� ������������, <B>��������� ��������� ����� � ������!</B>");

// ���� ����� � ����� ������� ��� �������
$loginsm=strtolower($name);
$lines=file("$datadir/user.php"); $i=count($lines);
if ($i>"1") { do {$i--; $rdt=explode("|",$lines[$i]); 
$rdt[2]=strtolower($rdt[2]);
if ($rdt[2]===$loginsm) {$bad="1"; $er="�������";}
if ($rdt[5]===$email) {$bad="1"; $er="�������";}
} while($i > 1);
if (isset($bad)) exit("$back. �������� � ����� <B>$er ��� ��������������� �� ������</B>!"); }

// �������� ������������ ���� ���������
$headers=null; // ��������� ��� �������� �����
$headers.="From: $name <$email>\n";
$headers.="X-Mailer: PHP/".phpversion()."\n";
$headers.="Content-type: text/plain; charset=windows-1251";

// �������� ��� ���������� � ���� ������
if ($activation==TRUE) {
$allmsg=$forum_name.' (������������� �����������)'.chr(13).chr(10).
 '����������� ������������ �� ������, ��� ����� ��������� �� ������: '.$forum_url.'tools.php?event=reg3&email='.$email.'&key='.$key.chr(13).chr(10).
 '���� ���: '.$name.chr(13).chr(10).
 '��� ������: '.$pass.chr(13).chr(10).
 '��� E-mail: '.$email.chr(13).chr(10).
 '������������� ����: '.$key.chr(13).chr(10).chr(13).chr(10).
 '��������� ������ � ������� ��� ��������� ���.'.chr(13).chr(10).
 '������ �� ������ �������� � ������������� ����, ������� ������ ����������.'.chr(13).chr(10).
 '��� �������������� ������� � ������ ��� ������� ��������������� �������� �������������� ������.'.chr(13).chr(10);
 
} else { $allmsg=$forum_name.' (������ �����������)'.chr(13).chr(10). '�� ������� ���������������� �� ������: '.$forum_url.chr(13).chr(10). '���� ���: '.$name.chr(13).chr(10). '��� ������: '.$pass.chr(13).chr(10). '��� E-mail: '.$email.chr(13).chr(10); }

// ���������� ������ ������� �� �������� ;-)
mail("$email", "=?windows-1251?B?" . base64_encode("$forum_name (������������� �����������)") . "?=", $allmsg, $headers);
if ($admin_send==TRUE) {mail("$adminemail", "=?windows-1251?B?" . base64_encode("$forum_name (����� ��������)") . "?=", $allmsg, $headers);}

$file=file("$datadir/user.php");
$fp=fopen("$datadir/user.php","a+");
flock ($fp,LOCK_EX);
fputs($fp,"$text\r\n");
fflush ($fp);//�������� ��������� ������
flock ($fp,LOCK_UN);
fclose($fp);

// ���������� ������� � ������ � ���� �� �����������
$file=file("$datadir/userstat.dat");
$fp=fopen("$datadir/userstat.dat","a+");
flock ($fp,LOCK_EX);
fputs($fp,"$rn||$name|0||0|0|0|0||$ip||\r\n");
fflush ($fp);//�������� ��������� ������
flock ($fp,LOCK_UN);
fclose($fp);

if ($activation!=TRUE) { $tektime=time(); $wrfcookies="$name|$pass|$tektime|0|"; setcookie("wrfcookies", $wrfcookies, time()+1728000);
print"<html><head><link rel='stylesheet' href='$forum_skin/style.css' type='text/css'></head><body>
<script language='Javascript'>function reload() {location = \"index.php\"}; setTimeout('reload()', 2500);</script>
<table width=100% height=80%><tr><td><table border=1 cellpadding=10 cellspacing=0 bordercolor=#224488 align=center valign=center width=60%><tr><td><center>
<B>$name, �� ������� ����������������</B>.<BR><BR>����� ��������� ������ �� ������ ������������� ���������� �� ������� �������� ������.<BR><BR>
<B><a href='index.php'>������ >>></a></B></td></tr></table></td></tr></table></center></body></html>"; exit;}

print"<html><head><link rel='stylesheet' href='$forum_skin/style.css' type='text/css'></head><body>
<script language='Javascript'>function reload() {location = \"tools.php?event=reg3\"}; setTimeout('reload()', 2500);</script>
<table width=100% height=80%><tr><td><table border=1 cellpadding=10 cellspacing=0 bordercolor=#224488 align=center valign=center width=60%><tr><td><center>
<B>$name, �� ��������� ���� ����� ��� ������ ��� �������������.
��� ���� ����� ������������������ - ������� ��� �� ��������, ���� ��������� �� ������ - ��������� � ������</B>.<BR><BR>����� ��������� ������ �� ������ ������������� ���������� �� �������� ������������� �����������.<BR><BR>
<B><a href='tools.php?event=reg3'>������ >>></a></B></td></tr></table></td></tr></table></center></body></html>";
exit;
}






// ����������� ��� 3 - ���� ����� ���� ������������� �� ������
if ($_GET['event']=="reg3") {

if (isset($_GET['email']) and isset($_GET['key'])) {$key=$_GET['key']; $email=$_GET['email'];} else {
$frname=""; $frtname=""; include("$forum_skin/top.html"); addtop(); // ���������� ����� ������
exit('<center><span class=maintitle>������������� �����������*</span><br>
<br><form action="tools.php" method=GET>
<input type=hidden name=event value="reg3">
<table cellpadding=3 cellspacing=1 width=100% class=forumline><tr>
<th class=thHead colspan=2 height=25 valign=middle>���� ������ � �������������� �����</th>
</tr><tr><td class=row1><span class=gen>����� e-mail:</span><br><span class=gensmall></span></td><td class=row2><input type=text class=post style="width: 200px" name=email size=25 maxlength=50></td>
</tr><tr><td class=row1><span class=gen>������������� ����:</span><br><span class=gensmall></span></td><td class=row2><input type=text class=post style="width: 200px" name=key size=25 maxlength=6></td></tr><tr>
<td class=catBottom colspan=2 align=center height=28><input type=submit value="����������� �����������" class=mainoption></td>
</tr></table>
* �� ������ ���� ������ ����� � ����, ������� ������ �� �����, ���� ������� �� ������������� ������ � ������.
</form>');}

// ������ �� ������ �� ����� � ������
if (strlen($key)<6 or strlen($key)>6 or !ctype_digit($key)) exit("$back. �� �������� ��� ����� �����. ���� ����� ��������� ������ 6 ����.");
$email=replacer($email); $email=str_replace("|","I",$email); $email=str_replace("\r\n","<br>",$email);
if (strlen($email)>35) exit("������ ��� ����� ������");

// ���� ����� � ����� ������� � ������. ���� ���� - ������ ������ �� ������ ����.
$fnomer=null; $email=strtolower($email); unset($fnomer); unset($ok);
$lines=file("$datadir/user.php"); $ui=count($lines); $i=$ui;
do {$i--; $rdt=explode("|",$lines[$i]); 
$rdt[5]=strtolower($rdt[5]);
if ($rdt[5]===$email and $rdt[15]===$key) {$name=$rdt[2]; $pass=$rdt[3]; $fnomer=$i;}
if ($rdt[5]===$email and $rdt[16]==TRUE) $ok="1";
} while($i > 1);

if (isset($fnomer)) {
// ���������� ������ ����� � ��
$i=$ui; $dt=explode("|", $lines[$fnomer]);
$txtdat="$dt[0]|$dt[1]|$dt[2]|$dt[3]|$dt[4]|$dt[5]|$dt[6]|$dt[7]|$dt[8]|$dt[9]|$dt[10]|$dt[11]|$dt[12]|$dt[13]|$dt[14]|noavatar.gif|1|";
$fp=fopen("$datadir/user.php","a+");
flock ($fp,LOCK_EX); 
ftruncate ($fp,0);//������� ���������� �����
for ($i=0;$i<=(sizeof($lines)-1);$i++) { if ($i==$fnomer) fputs($fp,"$txtdat\r\n"); else fputs($fp,$lines[$i]); }
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);
// ������������� ����
$tektime=time(); $wrfcookies="$name|$pass|$tektime|0|";
setcookie("wrfcookies", $wrfcookies, time()+1728000);
}
if (!isset($fnomer) and !isset($ok)) exit("$back. �� �������� � ���� �������������� ����� ��� ������.</center>");
if (isset($ok)) $add="���� ������ ��� ������������"; else $add="$name, �� ������� ����������������";

print"<html><head><link rel='stylesheet' href='$forum_skin/style.css' type='text/css'></head><body>
<script language='Javascript'>function reload() {location = \"index.php\"}; setTimeout('reload()', 2500);</script>
<table width=100% height=80%><tr><td><table border=1 cellpadding=10 cellspacing=0 bordercolor=#224488 align=center valign=center width=60%><tr><td><center>
�������, <B>$add</B>.<BR><BR>����� ��������� ������ �� ������ ������������� ���������� �� ������� �������� ������.<BR><BR>
<B><a href='index.php'>������ >>></a></B></td></tr></table></td></tr></table></center></body></html>";
exit; }






// ��������� ������ ����������� - ���������� ������
if ($_GET['event']=="reregist") { // if ($event =="reregist")

if (!isset($_POST['name'])) exit("$back ������� ���� ���!");
$name=str_replace("|","I",$_POST['name']);
if ($name=="" or strlen($name)>$maxname) exit("$back ���� ��� ������, ��� ��������� $maxname ��������!</B></center>");
$name=trim($name); // �������� ���������� ������� 
if (preg_match("/[^(\\w)|(\\x7F-\\xFF)|(\\-)]/",$name)) exit("$back ���� ��� �������� ����������� �������. ��������� ������� � ���������� �����, ����� � �������������!!.");

if (!isset($_POST['pass'])) exit("$back �� �� ����� ������!");
$oldpass=$_POST['oldpass']; // ������ ������
$pass=trim($_POST['pass']);
if (strlen($_POST['newpassword'])<1 ) exit("$back ����������� ����� ������ ������� 1 ������!");
if ($_POST['newpassword']!="�����") {$pass=trim($_POST['newpassword']); 
if (strlen($pass)<1 or strlen($pass)>20) exit("$back �� �� ����� ������. ������ ������ ���� ������ �� 1 �� 20 ��������!</B></center>");
$pass=md5("$pass");}
$pass=replacer($pass); $pass=str_replace("|","I",$pass);

if (isset($_POST['email'])) $email=$_POST['email']; else $email=""; $email=strtolower($email);
if(!preg_match("/^[a-z0-9\.\-_]+@[a-z0-9\-_]+\.([a-z0-9\-_]+\.)*?[a-z]+$/is", $email) or $email=="" or strlen($email)>40) exit("$back � ������� ���������� E-mail �����!</B></center>");

if (isset($_POST['dayx'])) $dayx=replacer($_POST['dayx']); else $dayx="";
if (isset($_POST['pol'])) $pol=replacer($_POST['pol']); else $pol=""; if ($pol!="1") $pol="0";
if (isset($_POST['icq'])) $icq=replacer($_POST['icq']); else $icq="";
if (isset($_POST['www'])) $www=replacer($_POST['www']); else $www="";
if (isset($_POST['about'])) $about=replacer($_POST['about']); else $about="";
if (isset($_POST['work'])) $work=replacer($_POST['work']); else $work="";
if (isset($_POST['write'])) $write=replacer($_POST['write']); else $write="";
if (isset($_POST['avatar'])) $avatar=replacer($_POST['avatar']); else $avatar="";

if ($_FILES['file']['name']!="") { // ��������� ��� � ������ ������������� �����
$fotoname=replacer($_FILES['file']['name']); $fotosize=$_FILES['file']['size']; // ��� � ������ �����
} else {$fotoname=$avatar; $fotosize="";}

$notgood="$back ������� ������� �������� ���������� ";
if (strlen($dayx)>20) {$notgood.="���� ��������!"; exit("$notgood");}
if (strlen($icq)>10) {$notgood.="ICQ!"; exit("$notgood");}
if (strlen($www)>75) {$notgood.="URL �����!"; exit("$notgood");}
if (strlen($about)>75) {$notgood.="������!"; exit("$notgood");}
if (strlen($work)>75) {$notgood.="��������!"; exit("$notgood");}
if (strlen($write)>75) {$notgood.="�������!"; exit("$notgood");}

$email=str_replace("|","I",$email);
$dayx=str_replace("|","I",$dayx);
$icq=str_replace("|","I",$icq);
$www=str_replace("|","I",$www);
$about=str_replace("|","I",$about);
$work=str_replace("|","I",$work);
$write=str_replace("|","I",$write);
$avatar=str_replace("|","I",$avatar);

// �������� ������/������� ������
$ok=null; $lines=file("$datadir/user.php"); $i=count($lines); unset($ok);
do {$i--; $rdt=explode("|", $lines[$i]);
   if (strtolower($name)===strtolower($rdt[2]) & $oldpass===$rdt[3]) $ok="$i"; // ���� ����� �����/������
   else { if ($email===$rdt[5]) $bademail="1"; } // ����� � ������ ��� ���� ����� �����?
} while($i > "1");
if (isset($bademail)) exit("$back. �������� � ������� <B>$email ��� ���������������</B> �� ������!</center>");
if (!isset($ok)) {setcookie("wrfcookies","",time());
exit("$back ��� ����� ����� /������ / ����� �� ��������� �� � ����� �� ��. <BR><BR>
����� ������������ ������ <font color=red><B>���������</B></font><BR><BR>
<font color=red><B>������ ������� ��� ������� ������ - ���������� � ��������������!</B></font>");}
$udt=explode("|",$lines[$ok]); $dayreg=$udt[1]; $kolvomsg=$udt[4]; $status=$udt[16]; $rn=$udt[0];

// ������ ������ $text="$name|$pass|$kolvomsg|$email|$dayreg|$dayx|$pol|$icq|$www|$about|$work|$write|$fotoname|$status|";
$text="$rn|$dayreg|$name|$pass|$kolvomsg|$email|$pol|$dayx|0||$icq|$www|$about|$work|$write|$fotoname|$status|";
$text=replacer($text); $exd=explode("|",$text); $name=$exd[2]; $pass=$exd[3]; $email=$exd[5];

// ������ ���� �����
//$tektime=time(); $wrfcookies="$name|$pass|$tektime|$tektime|";
//setcookie("wrfcookies", $wrfcookies, time()+1728000);

if ($fotoname!=$avatar and $fotoname!="") { // ���� �������� �������

// ������ �� ������

// 1. ��������� ����������
$ext = strtolower(substr($fotoname, 1 + strrpos($fotoname, ".")));
if (!in_array($ext, $valid_types)) {echo "<B>���� �� ��������.</B> ��������� �������:<BR>
- ��������� �������� ������ ������ � ������ ������������: <B>";
$patern=""; foreach($valid_types as $v) print"$v, ";
print"</B><BR>
- �� ��������� ��������� ���� � ������� �����������;<BR>
- ������� ����� ����� ��� ������ ����������� ����;</B><BR>"; exit;}

// 1. ������� ���-�� ����� � ��������� - ���� ������� ����� - ��������!
$findtchka=substr_count($fotoname, "."); if ($findtchka>1) exit("����� ����������� � ����� ����� $findtchka ���(�). ��� ���������! <BR>\r\n");

// 2. ���� � ����� ���� .php, .html, .htm - ��������! 
$bag="��������, �� � ����� ����� <B>���������</B> ������������ .php, .html, .htm";
if (preg_match("/\.php/i",$fotoname)) exit("��������� <B>.php</B> �������. $bag");
if (preg_match("/\.html/i",$fotoname)) exit("��������� <B>.html</B> �������. $bag");
if (preg_match("/\.htm/i",$fotoname)) exit("��������� <B>.htm</B> �������. $bag");

// 3. �������� �� ������� ���� � ����� ����� � �������� ���������� ����� 
$patern=""; foreach($valid_types as $v) $patern.="$v|";
if (!preg_match("/^[a-z0-9\.\-_]+\.(".$patern.")+$/is",$fotoname)) exit("$fotoname - <br>��������� ������������ ������� ����� � ����� �����, � ����� ��������� ��������� ����� � ����������� �������� �� ��������!!");

// 4. ���������, ����� ���� ���� � ����� ������ ��� ���� �� �������
if (file_exists("$filedir/$fotoname")) exit("<br><br>$back. ���� � ����� ������ ��� ���������� �� �������! ���� �������� ��� �� ������, <br>���� �������� �������� - �������� �� ��������� �������� ��������� � ���� ��������!!");

// 5. ������ � ��. < �����������
$fotoksize=round($fotosize/10.24)/100; // ������ ������������ ���� � ��.
$fotomax=round($max_f_size/10.24)/100; // ������������ ������ ���� � ��.
if ($fotoksize>$fotomax) exit("�� ��������� ���������� ������ ����! <BR><B>����������� ����������</B> ������ ����: <B>$fotomax </B>��.<BR> <B>�� ���������</B> ��������� �����������: <B>$fotoksize</B> ��!");

// 6. "��������" ������� > 150 � 150 - �� ��������! :-)
$size=getimagesize($_FILES['file']['tmp_name']);
if ($size[0]>150 or $size[1]>150) exit("�� ���������� �������� �������. ��������� ���� 150 � 150 px!");

if ($fotosize>"0" and $fotosize<$max_f_size) {
   copy($_FILES['file']['tmp_name'], $avatardir."/".$fotoname);
   print "<br><br>���� ������� ���������: $fotoname (������: $fotosize ����)";}
else exit("<B>���� �� �������� - ������ �������!
���� �� ������ ��������� - [function.getimagesize]: Filename cannot be empty, ������ � ��� ���������� GD �����������, ���� ������ ������<br>
�����, ������ �� ����� ��� �������� ��������� ��������, ��� �� �������� �������� �������� ������ ����� http ��� ���������!
���������� � ��������������!<B>");
} // ����� ����� �������� �������


$file=file("$datadir/user.php");
$fp=fopen("$datadir/user.php","a+");
flock ($fp,LOCK_EX);
ftruncate ($fp,0);//������� ���������� ����� 
for ($i=0;$i< sizeof($file);$i++) { if ($ok!=$i) fputs($fp,$file[$i]); else fputs($fp,"$text\r\n"); }
fflush ($fp);//�������� ��������� ������
flock ($fp,LOCK_UN);
fclose($fp);

print"<html><head><link rel='stylesheet' href='$forum_skin/style.css' type='text/css'></head><body>
<script language='Javascript'>function reload() {location = \"index.php\"}; setTimeout('reload()', 1500);</script>
<table width=100% height=80%><tr><td><table border=1 cellpadding=10 cellspacing=0 bordercolor=#224488 align=center valign=center width=60%><tr><td><center>
�������, <B>$name, ���� ������ ������� ��������</B>.<BR><BR>����� ��������� ������ �� ������ ������������� ���������� �� ������� �������� ������.<BR><BR>
<B><a href='index.php'>������ >>></a></B></td></tr></table></td></tr></table></center></body></html>";
exit; }





if ($_GET['event'] =="givmepassword") { // �������� �������� ������ �� ����

// ������ �� ��������� ������
if (!isset($_POST['myemail']) or !isset($_POST['myname'])) exit("�� ����� �� ��������� ������!");
$myemail=strtolower($_POST['myemail']); $myemail=replacer($myemail);
$myname =strtolower($_POST['myname']); $myname =replacer($myname);
if (strlen($myemail)>40 or strlen($myname)>40) exit("����� ����� ��� ����� ������ ���� ����� 40 ��������!");

// ���������� ����� ������ �����
$len=8; // ���������� �������� � ����� ������
$base='ABCDEFGHKLMNPQRSTWXYZabcdefghjkmnpqrstwxyz123456789';
$max=strlen($base)-1; $pass=''; mt_srand((double)microtime()*1000000);
while (strlen($pass)<$len) $pass.=$base{mt_rand(0,$max)};

$lines=file("$datadir/user.php"); $record="<?die;?>\r\n"; $itogo=count($lines); $i=1; $regenter=FALSE;

do {$rdt=explode("|",$lines[$i]); // �������� �� ���� ������������� � ������� ������
if (isset($rdt[1])) { // ���� ������� ���������� � ������� (������ ������) - �� ������ � �� �������
$rdt[5]=strtolower($rdt[5]); $rdt[2]=strtolower($rdt[2]);
if ($myemail===$rdt[5] or $myname===$rdt[2]) {$regenter=TRUE; $myemail=$rdt[5]; $myname=$rdt[2]; $passmd5=md5("$pass"); $lines[$i]=str_replace("$rdt[3]","$passmd5",$lines[$i]);}
} //if isset
$record.=$lines[$i];
$i++; } while($i < $itogo);

// ����� IP-�������������� ������
$ip=""; $ip=(isset($_SERVER['REMOTE_ADDR']))?$_SERVER['REMOTE_ADDR']:0;

// ������������ ���� ���������� - ��������� ���� ����� ������
$fp=fopen("$datadir/user.php","a+");
flock ($fp,LOCK_EX);
ftruncate ($fp,0);
fputs($fp,"$record");
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);

// �������� ������������ ��� ����� � ������ �� ����
if ($regenter==TRUE) {
$headers=null; // ��������� ��� �������� �����
$headers.="From: ������������� <$adminemail>\n";
$headers.="X-Mailer: PHP/".phpversion()."\n";
$headers.="Content-Type: text/plain; charset=windows-1251";

// �������� ��� ���������� � ���� ������
$allmsg=$forum_name.' (������ ��� �������������� ������� � ������)'.chr(13).chr(10).
        '��, ���� ���-�� ������ � IP-������ '.$ip.' ��������� ������ ��� �������������� ������� � ������ �� ������: '.$forum_url.chr(13).chr(10).chr(13).chr(10).
        '���� ���: '.$myname.chr(13).chr(10).
        '��� ����� ������: '.$pass.chr(13).chr(10).chr(13).chr(10).
        '��� ����� �� ����� ��������� �� ������ � ������� ����� � ����� ������: '.$forum_url.'?event=login'.chr(13).chr(10).chr(13).chr(10).
        '�������� ��� ������ (������ ����� ���� ��� ������) ������ ����� �� ��������: '.$forum_url.'?event=profile&pname='.$myname.chr(13).chr(10).chr(13).chr(10).
        '* ��� ������ ������������� �������, �������� �� ���� �� �����.'.chr(13).chr(10);
// ���������� ������ ������� �� �������� ;-)
mail("$myemail", "=?windows-1251?B?" . base64_encode("$forum_name (������ ��� �������������� ������� � ������)") . "?=", $allmsg, $headers);
// ���� ���� �������� � �������� �������
$msgtoopr="<B>$myname</B>, �� ��� ����������� ����� ������� ��������� � ������ � ������� ������� � ������.";
}
// ���� ��� ������ ������ � ��
else $msgtoopr="<B>��������� � ����� ������� ��� �������</B><BR> �� ������ <B>�� ����������������!</B>";
print "<html><body><script language='Javascript'>function reload() {location = \"index.php\"}; setTimeout('reload()', 2000);</script>
<BR><BR><BR><center><table border=1 cellpadding=10 cellspacing=0 bordercolor=#224488 width=300><tr><td align=center>
<font style='font-size: 15px'>$msgtoopr ����� ��������� ������ �� ������ ������������� ���������� �� ������� ��������.
���� ����� �� ����������, ������� <B><a href='index.php'>�����</a></B></font>.</td></tr></table></center><BR><BR><BR></body></html>";
exit; }






if ($_GET['event']=="moresmiles") { // ���������� ���� ������� �� ���������� SMILE

$lines=null; unset($lines); if (!is_dir("smile/")) exit("����� smile �� ����������.");
$i=0; if ($handle = opendir("smile/")) {
while (($file = readdir($handle)) !== false)
if (!is_dir($file)) {$lines[$i]=$file; $i++;}
closedir($handle);
}
if (!isset($lines)) exit("� ����� smile ��� �������! ���������� � ������ - ����� ������.");
$itogo=count($lines); $k=0; $text=null;
print"<html><head><meta http-equiv='Content-Type' content='text/html; charset=windows-1251'><meta http-equiv='Content-Language' content='ru'></head><body>
<script language=\"JavaScript\">function setSmile(symbol) { obj = opener.document.REPLIER.msg; obj.value = obj.value + symbol; }</script>
<center><b>�������������� ������</b><br>";
do {
$rdt=explode(".",$lines[$k]);
if ($rdt[1]=="jpg" or $rdt[1]=="gif") {print"<a href=\"javascript:setSmile('[img]$forum_url/smile/$lines[$k][/img] ')\"><img src='smile/$lines[$k]' border=0></a>&nbsp; ";}
$k++;
} while ($k<$itogo);
exit("<BR><a href='' onClick='self.close()'><b>������� ����</b></a></center><small>P.S. �������������! ����� ����� ��������� ����� ������ - ������ ������� ����� ������ � �����".$forum_url."smile/</small></body></html>");}




// ----- ����� ��� ���� ������� ������

if (isset($_COOKIE['wrfcookies'])) {
$wrfc=$_COOKIE['wrfcookies']; $wrfc=htmlspecialchars($wrfc); $wrfc=stripslashes($wrfc);
$wrfc = explode("|", $wrfc);
$wrfname=$wrfc[0];$wrfpass=$wrfc[1];$wrftime1=$wrfc[2];$wrftime2=$wrfc[3];
if (time()>($wrftime1+50)) {$tektime=time();$wrfcookies="$wrfc[0]|$wrfc[1]|$tektime|$wrftime1";setcookie("wrfcookies", $wrfcookies, time()+1728000);}}
 else {unset($wrfname); unset($wrfpass);}

// -----

$frname=""; $frtname=""; //include("$forum_skin/top.html"); addtop(); // ���������� ����� ������









if ($_GET['event'] =="deletemsg") { // �������� ������ ��������� = 10.08.2012 �.

if (!isset($_GET['username']) or !isset($_GET['id'])) exit("� ������ ������ ������ ID ���� � ��� ���������! ������� ������.");

$username=urldecode($_GET['username']); // ����������� ��� ������������, ��������� �� GET-�������.
$id=replacer($_GET['id']); // �������� ������������� ����
if ((!ctype_digit($id)) or (strlen($id)!=7)) exit("<B>$back. ������� ������! ������������� ���� ������ ��������� ������ 7 ����!</B>");

$lines=file("$datadir/user.php"); $i=count($lines); $mlines="0"; $filname=null;

// ������� ��� � ������ � ����� � ������ � ������� � user.php
do {$i--; $rdt=explode("|",$lines[$i]);
if (isset($rdt[1])) { // ���� ������� ���������� � ������� (������ ������) - �� ������ � �� �������
if ($username===$rdt[2]) {

if (isset($wrfname) & isset($wrfpass)) { $wrfname=replacer($wrfname); $wrfpass=replacer($wrfpass);
if ($wrfname===$rdt[2] & $wrfpass===$rdt[3]) {

$mlines=file("$datadir/$id.dat"); $maxi=count($mlines)-1;

$dt=explode("|",$mlines[$maxi]);
// ���� ��������� ��������� � ���� ������� ��� ���������� ����, �� ������� ��������� ���������
// ����� �������� ������ � ������ �� ������ �����
if ($dt[8]==$username and $dt[6]==TRUE and $maxi>0) { $filname=$dt[13]; $zag=$dt[5]; unset($mlines[$maxi]); $maxi--;} else $mlines="0";
$i=1;
} } } }
} while($i > "1");

if ($mlines!="0") {
if (is_file("$filedir/$filname")) unlink("$filedir/$filname"); // ������� ������������ ����
$fp=fopen("$datadir/$id.dat","a+");
flock ($fp,LOCK_EX);
ftruncate ($fp,0);//������� ���������� �����
for ($i=0;$i<=$maxi;$i++) fputs($fp,$mlines[$i]);
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);

// -1 � ���� � -1 � ��������� �����!
$ufile="$datadir/userstat.dat"; $ulines=file("$ufile"); $ui=count($ulines)-1; $ulinenew=""; $fileadd=0;
if ($filname!=null) $fileadd=$repaaddfile; // ���� ���� ������� ����, �� ��� ��� -� � ����
$tektime=time(); $ip=$_SERVER['REMOTE_ADDR']; // ���������� IP �����
for ($i=0;$i<=$ui;$i++) {$udt=explode("|",$ulines[$i]);
if ($udt[2]==$username) { // ���� ����� �� ����� � ����� userstat.dat
$udt[6]--; $udt[7]=$udt[7]-$fileadd-$repaaddmsg;
$ulines[$i]="$udt[0]|$tektime|$udt[2]|$udt[3]|$udt[4]|$udt[5]|$udt[6]|$udt[7]|$udt[8]|$udt[9]|$ip|$udt[11]|\r\n";}
$ulinenew.="$ulines[$i]";}
// ����� ������ � ����
$fp=fopen("$ufile","w");
flock ($fp,LOCK_EX);
fputs($fp,"$ulinenew");
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);

// ���������� ������ � ���� REPA.DAT
$repa=-$fileadd; $repa=$repa-$repaaddmsg; $pochemu="�� �������� ��������� � ���� <a href='index.php?id=$id' target=_blank>$zag</a>";
$fp=fopen("$datadir/repa.dat","a+");
flock ($fp,LOCK_EX);
fputs($fp,"$tektime|$repa|$wrfname||$pochemu||||\r\n");
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);

$rezult="���� ������� �������.";} else $rezult="<font color=red><B>�� ���� �������, ��� ��� ��� ������������ � ����!</B></font>";

print "<script language='Javascript'>function reload() {location = \"index.php?id=$id\"}; setTimeout('reload()', 2500);</script>
<table width=100% height=80%><tr><td><table border=1 cellpadding=10 cellspacing=0 bordercolor=#224488 align=center valign=center width=60%><tr><td><center>
��������, <B>$username</B>, ���� ��������� $rezult<BR><BR>����� ��������� ������ �� ������ ������������� ���������� � ������� ����.<BR><BR>
<B><a href='index.php?id=$id'>������ >>></a></B></td></tr></table></td></tr></table></center></body></html>";
exit; }








if ($_GET['event']=="who") { // �������� ����������

// ���� ��������� - �� �������
if (!isset($_COOKIE['wrfcookies'])) exit("$back <center><table class=forumline width=700><tr><th class=thHead colspan=4 height=25>������ ���������</th></tr><tr class=row2><td class=row1><center><BR><BR><B><span style='FONT-SIZE: 14px'>��� ��������� ������ ������������� ���������� ������������������.</B></center></td></table></center>");

$t1="row1";
$alllines=file("$datadir/user.php"); $allslines=file("$datadir/userstat.dat");
unset($alllines[0]); unset($allslines[0]); // ������� ������ (��������� ������)
$alllines=array_values($alllines); $allslines=array_values($allslines);
$allmaxi=count($alllines); $i=0; $j=0; $flag=0;

if (isset($_GET['pol'])) $pol=replacer($_GET['pol']); else $pol="";
if (isset($_GET['interes'])) $interes=replacer($_GET['interes']); else $interes="";
if (isset($_GET['url'])) $url=replacer($_GET['url']); else $url="";
if (isset($_GET['from'])) $from=replacer($_GET['from']); else $from="";
if (isset($_GET['repa'])) $repa=replacer($_GET['repa']); else $repa="";
if (isset($_GET['dayreg'])) $dayreg=replacer($_GET['dayreg']); else $dayreg="";

$tektime=time();

if($pol!="" or $interes!="" or $url!="" or $from!="" or $repa!="" or $dayreg!="") {

do {$dt=explode("|",$alllines[$i]); $udt=explode("|",$allslines[$i]);

// ���� ���� ���������� � ������ - ����������� ����� �������� 1
if (strlen($repa)>0) {if ($udt[7]>$repa) $flag=1;}
if (strlen($pol)>0) {if ($dt[6]==$pol) $flag=1;}
//print"$dt[6]=$pol-$flag<br>";
if ($dayreg>0 and $dayreg<121) {$delta=$dt[1]+2592000*$dayreg; if ($delta<$tektime) $flag=1;} // ����� * dayreg

if ($dt[13]!="" and $interes!="") {if (stristr($dt[13],$interes)) $flag=1;}
if ($dt[11]!="" and $url!="") {if (stristr($dt[11],$url)) $flag=1;}
if ($dt[12]!="" and $from!="") {if (stristr($dt[12],$from)) $flag=1;}

// ���� ���� ���� ���� ����������, �������� ��������� � ������ ����������
if ($flag==1) {$lines[$j]=$alllines[$i]; $slines[$j]=$allslines[$i]; $flag=0; $j++;}
$i++; 
} while($i<$allmaxi);

//print"<PRE>"; print_r($slines); print_r($lines); exit;

$fadd="&pol=$pol&interes=$interes&url=$url&from=$from&repa=$repa&$dayreg=$dayreg";
} else {$fadd=""; $lines=$alllines; $slines=$allslines;} // ���� ����� �� �����, ����� ����������� ������

if (!isset($lines)) $maxi=0; else $maxi=count($lines);

// ��������� ������ ������ �������������� ��������
if (!isset($_GET['page'])) $page=1; else { $page=$_GET['page']; if (!ctype_digit($page)) $page=1; if ($page<1) $page=1; }
$maxpage=ceil(($maxi)/$uq); if ($page>$maxpage) $page=$maxpage;

print"
<form action='tools.php?event=who' method=GET><div align=right>
<input type=hidden name=event value='who'>
�������: <SELECT name=pol><option value=''>���</option><OPTION value='1'>�������</OPTION><OPTION value='0'>�������</OPTION></SELECT>

<SELECT name=repa><option value=''>���������</option><OPTION value='$userrepa[1]'> > $userrepa[1]</OPTION>
<OPTION value='$userrepa[2]'> > $userrepa[2]</OPTION><OPTION value='$userrepa[3]'> > $userrepa[3]</OPTION>
<OPTION value='$userrepa[4]'> > $userrepa[4]</OPTION><OPTION value='$userrepa[5]'> > $userrepa[5]</OPTION>
<OPTION value='$userrepa[6]'> > $userrepa[6]</OPTION><OPTION value='$userrepa[7]'> > $userrepa[7]</OPTION></SELECT>

<SELECT name=dayreg><option value=''>���� �����������</option>
<OPTION value='1'> > ������</OPTION><OPTION value='6'> > ���������</OPTION><OPTION value='12'> > ����</OPTION>
<OPTION value='36'> > 3 ���</OPTION><OPTION value='60'> > 5 ���</OPTION><OPTION value='120'>���� �� ���������!</OPTION></SELECT>

<B>���������:</B> <input type=text name=interes value='$interes' class=post maxlength=50 size=25>
<B>�����:</B> <input type=text name=url value='$url' class=post maxlength=50 size=25>
<B>������:</B> <input type=text name=from value='$from' class=post maxlength=50 size=25>
<input type=submit class=mainoption value='OK'></form></div><br>";

print"<table width=100% cellpadding=3 cellspacing=1 class=forumline><tr> 
<th class=thCornerL height=25 width=20>�</th>
<th class=thCornerL><small><a href='tools.php?event=who&page=$page&pol=1' style='text-decoration:none'>�</a> ��� / ���</small></th>
<th class=thTop><small>������</small></th>
<th class=thTop><small>����</small></th>
<th class=thTop><small>�� �� �-����</small></th>
<th class=thTop><small>�� �� �����</small></th>
<th class=thTop><small>�����������</small></th>
<th class=thTop><small>���� ��������</small></th>
<th class=thTop><small>��������</small></th>
<th class=thTop><small>����</small></th>
<th class=thCornerR><small>������</small></th>
</tr>";
if ($allmaxi=="0") {print"<TR><TD class=$t1 colspan=8 align=center>���������� �� ����������������</TD></TR>";
} else {

$fm=$uq*($page-1); if ($fm>$maxi) $fm=$maxi-$uq; //if ($fm<0) $fm=0;
$lm=$fm+$uq; if ($lm>=$maxi) $lm=$maxi-1;

if (isset($lines)) {

do { $dt=explode("|",$lines[$fm]); $udt=explode("|",$slines[$fm]);
$fm++; $num=$fm;

if (isset($dt[1])) { // ���� ������� �����, �� ������ � �� �������

$codename=urlencode($dt[2]); // �������� ��� � ����������, ��� ��������� ���������� �������� ����� ����� GET-������.
if (isset($wrfname)) {$wfn="<a href=\"tools.php?event=profile&pname=$codename\">$dt[2]</a>";
$mls="<form method='post' action='tools.php?event=mailto' target='email' onclick=\"window.open('tools.php?event=mailto','email','width=600,height=350,left=100,top=100');return true;\">
<input type=hidden name='email' value='$dt[5]'><input type=hidden name='name' value='$dt[2]'><input type=hidden name='id' value=''>
<input type=image src='$forum_skin/ico_pm.gif' alt='������ ���������'></form>";} else {$wfn="$dt[2]"; $mls="�������������";}

if (strlen($dt[16])!=TRUE) $dt[16]="<B><font color=red>�������� ���������</font></B>";
if ($dt[6]==TRUE) $add="polm.gif"; else $add="polg.gif";
$dt[1]=date("d.m.y",$dt[1]);
$codename=urlencode($dt[2]);

// ������ ����� � userstat.dat ���, � ����������� �� ����, � $userstatus[$i]
if (strlen($udt[9])>1) $status=$udt[9]; else { $si=0;
for ($si=0;$si<8;$si++) if ($udt[7]>=$userrepa[$si]) $stp=$si;
$status=$userstatus[$stp];}

print"<tr>
<td class=$t1>$num</td>
<td class=$t1 nowrap><b><img src='$forum_skin/$add' border=0> $wfn</b></td>
<td class=$t1 align=center>$status</td>
<td class=$t1 nowrap><B>$udt[7]</B> <A href='#m1' onclick=\"window.open('tools.php?event=repa&name=$dt[2]&who=1','repa','width=600,height=600,left=50,top=50,scrollbars=yes')\">&#177;</A></td>
<td class=$t1 align=center>$mls</td>
<td class=$t1 align=center><form action='pm.php?id=$codename' method=POST name=citata><input type=image border=0 src='data-pm/pm.gif' alt='��������� ������������ ���������'></form></td>
<td class=$t1 align=center>$dt[1]</td>
<td class=$t1 align=center>$dt[7]</td>
<td class=$t1>$dt[13]</td>
<td class=$t1><small>$dt[11]</small></td>
<td class=$t1>$dt[12]</td></tr>";
if ($t1=="row1") $t1="row2"; else $t1="row1";

} // ���� ������� ����������

} while($fm <= $lm);
} // if isset($lines)
} // ����� ���� ���� userdat.php ����

echo'</table><BR>';

$maxi--;
// ��������� ���������� $pageinfo - �� ������� �������
$pageinfo=""; $addpage=""; $maxpage=ceil(($maxi+1)/$uq); if ($page>$maxpage) $page=$maxpage;
$pageinfo.="<div style='padding:6px;' class=pgbutt>��������: &nbsp;";
if ($page>3 and $maxpage>5) $pageinfo.="<a href=tools.php?event=who$fadd>1</a> ... ";
$f1=$page+2; $f2=abs($page-2); if ($f2=="0") $f2=1; if ($page>=$maxpage-1) $f1=$maxpage;
if ($maxpage<=5) {$f1=$maxpage; $f2=1;}
for($i=$f2; $i<=$f1; $i++) { if ($page==$i) $pageinfo.="<B>$i</B> &nbsp;"; 
else {if ($i!=1) $addpage="&page=$i"; $pageinfo.="<a href=tools.php?event=who&page=$i$fadd>$i</a> &nbsp;";} }
if ($page<=$maxpage-3 and $maxpage>5) $pageinfo.="... <a href=tools.php?event=who&page=$maxpage$fadd>$maxpage</a>";
$pageinfo.='</div>';

print "$pageinfo
<div align=right>����� ���������������� ���������� - <B>$allmaxi</B></div><BR>";}





if ($_GET['event'] =="profile") { // �������������� �������

if (!isset($_GET['pname'])) exit("������� ������.");
$pname=urldecode($_GET['pname']); // ����������� ��� �����������if (!ctype_digit($userpn) or strlen($userpn)>4) exit("<B>$back. ������� ������. ������� ����� �� �����!");�, ��������� �� GET-�������.
$lines=file("$datadir/user.php"); $i = count($lines); $use="0"; $userpn="0";
do {$i--; $rdt=explode("|", $lines[$i]);

if (isset($rdt[1])) { // ���� ������� ���������� � ������� (������ ������) - �� ������ � �� �������

if (strlen($rdt[16])=="6" and ctype_digit($rdt[16])) $rdt[16]="<B><font color=red>�������� ���������</font></B>";

if ($pname===$rdt[2]) { $userpn=$i;

// ��������� ���������� ���������/���� �����
$jfile="$datadir/userstat.dat"; $jlines=file("$jfile"); $uj=count($jlines)-1; $msjitogo=0;
for ($j=0;$j<=$uj;$j++) {$udt=explode("|",$jlines[$j]); $msjitogo=$msjitogo+$udt[6]; if ($udt[2]==$rdt[2]) {$msguser=$udt[6]; $temaded=$udt[5]; $repa=$udt[7];}}
$msgaktiv=round(10000*$msguser/$msjitogo)/100;

$aktiv=$rdt[1]; $tekdt=time(); $aktiv=round(($tekdt-$aktiv)/86400);
if ($aktiv<=0) $aktiv=1; $aktiv=round(100*$msguser/$aktiv)/100;
$rdt[1]=date("d.m.Y �.",$rdt[1]);

if (isset($wrfname) & isset($wrfpass)) { $wrfname=replacer($wrfname); $wrfpass=replacer($wrfpass);
if ($rdt[6]==TRUE) $pol="�������"; else $pol="�������";

if ($wrfname===$rdt[2] & $wrfpass===$rdt[3]) {
print "<center><span class=maintitle>��������������� ������</span><br>

<br><form action='tools.php?event=reregist' name=creator method=post enctype=multipart/form-data>

<table border=1 cellpadding=2 cellspacing=0 width=100% class=forumline>
<tr><th class=thHead colspan=2 height=25 valign=middle>��������������� ����������</th></tr>
<tr><td class=row2 colspan=2><span class=gensmall>���� ���������� * ����������� � ����������, ���� �� ������� ��������</span></td></tr>
<tr><td class=row1 width=35%><span class=gen>��� ���������:</span><span class=gensmall><br>������� ���� ���������</span></td><td class=row2><span class=nav>$rdt[2]</span></td></tr>
<tr><td class=row1><span class=gen>��� ���:</span><br></td><td class=row2><span class=gen>$pol</span><input type=hidden value='$rdt[6]' name=pol></td></tr>
<tr><td class=row1><span class=gen>��� ������: *</span></td><td class=row2><input class=inputmenu type=text value='�����' maxlength=10 name=newpassword size=15><input type=hidden class=inputmenu value='$rdt[3]' name=pass>
(���� ������ �������, �� ������� ����� ������, ����� �������� ��� ����!)</td></tr>

<tr><td class=row1><span class=gen>����� e-mail: *</span><br><span class=gensmall>������� ������������ ����������� �����! ����� ������� �� �������-��������.</span></td>
<td class=row2> <input type=text class=post style='width: 200px' value='$rdt[5]' name=email size=25 maxlength=50></td></tr>

<tr><td class=row1><span class=gen>���� �����������:</span></td><td class=row2><span class=gen>$rdt[1]</td></tr>
<tr><td class=row1><span class=gen>���������: </span><br></td><td class=row2><B>$repa</B> [<A href='#1' onclick=\"window.open('tools.php?event=repa&name=$wrfname&who=$userpn','repa','width=600,height=600,left=50,top=50,scrollbars=yes')\">���������� ���������� ���������</A>]</td></tr>
<tr><td class=row1><span class=gen>����������:</span></td><td class=row2><span class=gen>��� �������: <B>$temaded</B>, ����� ���������: <B>$msguser</B> [<B>$msgaktiv%</B> �� ������ ����� / <B>$aktiv</B> ��������� � �����]</span></td></tr>

<td class=row1><span class=gen>������������ ���������</span><br><span class=gensmall><td class=row2>";

if (is_file("data-pm/$wrfname.dat")) {$linespm=file("data-pm/$wrfname.dat"); $pmi=count($linespm); print" <img src=\"$forum_skin/icon_mini_profile.gif\" border=0 hspace=3 />[<a href='pm.php?readpm&id=$wrfname'><font color=red><B>$pmi ��������� � ��</b></font></a>]";} else echo'��������� ���';


print"</span></td>
</tr><tr>
<td class=catSides colspan=2 height=28>&nbsp;</td>
</tr><tr>
<th class=thSides colspan=2 height=25 valign=middle>������� � ����</th>
</tr><tr>
<td class=row1><span class=gen>���� �������:</span><br><span class=gensmall>������� ���� �������� � �������: ��.��.�����, ���� �� ������.</span></td>
<td class=row2><input type=text name=dayx value='$rdt[7]' class=post style='width: 100px' size=10 maxlength=18></td>
</tr><tr>
<td class=row1><span class=gen>����� � ICQ:</span><br><span class=gensmall>������� ����� ICQ, ���� �� � ��� ����.</span></td>
<td class=row2><input type=text value='$rdt[10]' name=icq class=post style='width: 100px' size=10 maxlength=10></td>
</tr><tr>
<td class=row1><span class=gen>�������� ���������:</span><br><span class=gensmall>���� � ��� ���� �������� ��� ������� ��������� � ���������, ������� URL ���� ���������.</span></td>
<td class=row2><input type=text value='$rdt[11]' class=post style='width: 500px' name=www size=25 maxlength=70 value='http://' /></td>
</tr><tr>
<td class=row1><span class=gen>������:</span><br><span class=gensmall>������� ����� ���������� (������, �������, �����).</span></td>
<td class=row2><input type=text class=post style='width: 500px' value='$rdt[12]' name=about size=25 maxlength=70></td>
</tr><tr>
<td class=row1><span class=gen>��������:</span><br><span class=gensmall>�� ������ �������� � ����� ���������</span></td>
<td class=row2><input type=text class=post style='width: 500px' value='$rdt[13]' name=work size=35 maxlength=70></td>
</tr><tr>
<td class=row1><span class=gen>�������:</span><br><span class=gensmall>������� ���� �������, �� ����������� HTML</span></td>
<td class=row2><input type=text class=post style='width: 500px' value='$rdt[14]' name=write size=35 maxlength=70></td>
</tr><tr>
<td class=row1><span class=gen>������:</span><br><span class=gensmall>�������� ������� (��������), ������� ����� ������������ ����� � ����� ������.</span></td>
<td class=row2 height=120>";

$images=null; unset($images);
if (!is_file("avatars/$rdt[15]")) $rdt[15]="noavatar.gif";
$root = str_replace( '\\', '/', getcwd() ) . '/';
$dirtoopen = $root.'avatars';
if ( !($images = get_dir($dirtoopen,'*.{gif,png,jpeg,jpg}',GLOB_BRACE)) ) {
$images=array();
$handle=opendir($dirtoopen);
while ( false !== ($file = readdir($handle)) ) if (strstr($file,'.gif') || strstr($file,'.jpg')) $images[]=$file;
closedir($handle);
}
$selecthtml ="";
foreach ($images as $file) { if ($file==$rdt[15]) {$selecthtml .= '<option value="'.$file.'" selected>'.$file."</option>\n"; $currentface = $rdt[15];} else {$selecthtml .= '<option value="'.$file.'">'.$file."</option>\n";} }

print "<table><TR><TD>
<script language=javascript> function showimage() { document.images.avatar.src='./avatars/'+document.creator.avatar.options[document.creator.avatar.selectedIndex].value; } </script>
<select name='avatar' size=6 onChange='showimage()'>
$selecthtml
</select>
</td><td><img src='./avatars/$currentface' name=avatar border=0 hspace=15></td></tr></table>
</td></tr>";

print "
<td class=row1><span class=gen>��������� ���� ������:</span><br><span class=gensmall>������� ��������� ���� � ������ �������. <BR>����������� ������������ ��������: <BR> - ���������� �� ����� <B>120 � 120</B>, <BR>- ����������� ������ <B>gif, png, jpg ��� jpeg</B>, <BR> - �������� ����� <B>$maxfsize ��</B>. </B></span></td>
<td class=row2><input type=file name=file class=post style='width: 400px' size=35 maxlength=150></td>
</tr><tr><tr><td colspan=2>
<input type=hidden name=name value='$rdt[2]'>
<input type=hidden name=oldpass value='$rdt[3]'>
</td></tr><tr>
<td class=catBottom colspan=2 align=center height=28><input type=submit name=submit value='��������� ���������' class=mainoption /></td>
</tr></table></form>"; $use="1"; }


if ($use!="1") {

////////////// ����������  ������ �� ��������!!!! � $rdt[1] - ���� �����������!!!!!!!!!
//if (strlen($rdt[13])<2) $rdt[13]=$user_name;
if (is_file("avatars/$rdt[15]")) $avpr="$rdt[15]"; else $avpr="noavatar.gif";
if ($rdt[6]==TRUE) $pol="�������"; else $pol="�������";
print "<center><span class=maintitle>������� ���������</span><br><br><table cellpadding=5 cellspacing=1 width=100% class=forumline>
<tr><th class=thHead colspan=2 height=25 valign=middle>��������������� ����������</th></tr>
<tr><td class=row1 width=30%><span class=gen>��� ���������:</span></td><td class=row2><span class=nav>$rdt[2]</span></td></tr>
<tr><td class=row1><span class=gen>���������: </span><br></td><td class=row2><B>$repa</B> [<A href='#1' onclick=\"window.open('tools.php?event=repa&name=$rdt[2]&who=$userpn','repa','width=600,height=600,left=50,top=50,scrollbars=yes')\">������� &#177;</A>]</td></tr>
<tr><td class=row1><span class=gen>����������:</span></td><td class=row2><span class=gen>��� �������: <B>$temaded</B>, ����� ���������: <B>$msguser</B> [<B>$msgaktiv%</B> �� ������ ����� / <B>$aktiv</B> ��������� � �����]</span></td></tr>
<tr><td class=row1><span class=gen>��������� ������ ��������� �� e-mail: </span><br></td><td class=row2><form method='post' action='tools.php?event=mailto' target='email' onclick=\"window.open('tools.php?event=mailto','email','width=600,height=350,left=100,top=100');return true;\"><input type=hidden name='email' value='$rdt[5]'><input type=hidden name='name' value='$rdt[2]'><input type=hidden name='id' value=''><input type=image src='$forum_skin/ico_pm.gif' alt='������ ���������'></form></td></tr>
<tr><td class=row1><span class=gen>�������� ������������ ��������� (���� �� �����):</span><br></td><td class=row2><form action='pm.php?id=$rdt[2]' method=POST name=citata><input type=image border=0 src='data-pm/pm.gif' alt='��������� ������������ ���������'></form></span></td></tr>
<tr><td class=row1><span class=gen>���� �����������:</span></td><td class=row2><span class=gen>$rdt[1]</span></td></tr>
<tr><td class=row1><span class=gen>������:</span></td><td class=row2><span class=gen>$rdt[13]</span></td></tr>
<tr><td class=row1><span class=gen>���:</span></td><td class=row2><span class=gen>$pol</span></td></tr>
<tr><td class=row1><span class=gen>���� �������:</span><br></td><td class=row2><span class=gen>$rdt[7]</span></td></tr>
<tr><td class=row1><span class=gen>����� � ICQ:</span><br></td><td class=row2><span class=gen>$rdt[10]</td></tr>
<tr><td class=row1><span class=gen>�������� ���������:</span></td><td class=row2><span class=gen><a href='$rdt[11]' target='_blank'>$rdt[11]</a></td></tr>
<tr><td class=row1><span class=gen>������</span> (<span class=gensmall>����� ����������, �����, ������.):</span></td><td class=row2><span class=gen>$rdt[12]</td></tr>
<tr><td class=row1><span class=gen>��������:</span></td><td class=row2><span class=gen>$rdt[13]</td></tr>
<tr><td class=row1><span class=gen>�������:</span></td><td class=row2><span class=gen>$rdt[14]</td></tr>
<tr><td class=row1><span class=gen>������:</span></td><td class=row2 height=120><img src='./avatars/$avpr' border=0 hspace=15></td></tr>
</td></tr></table><BR>"; $use="1";}

}
}
} // if
} while($i > "1");

if (!isset($wrfname)) exit("<BR><BR><font size=+1><center>������ ������������������ ��������� ������ ����� ������������� ������ �������!");

if ($use!="1") { // � �� ������ ����� ��� - ��� ����� ������
print"<center><table width=600 height=300 class=forumline>
<tr><th class=thHead height=25 valign=middle>������������ �� ���������������</th></tr>
<tr><td class=row1 align=center><B>��������� ����������!</B><BR><BR> 
��������, �� �������� � ����� - <B>������� �� ������ �� ���������������.</B><BR><BR>
������ �����, <B>��� ������ �������������</B>.<BR><BR>
<B>���������� ������ ����������</B> ����� <B><a href='tools.php?event=who'>�����</a>.</B><br><br>
<B>������� �� �������</B> �������� ������ ����� �� <B><a href='$forum_url'>���� ������</a></B>
</TD></TR></TABLE>"; }
}






if ($_GET['event']=="reg") {
if (!isset($_POST['rulesplus'])) {
echo'
<form action="tools.php?event=reg" method=post>
<center><span class=maintitle>������� � ������� �����������</span><br><br>
<table cellpadding=8 cellspacing=1 width=100% class=forumline><tr><th class=thHead height=25 valign=middle>������� ������ � �������</th></tr><tr>
<td class=row1><span class=gen>';
if (is_file("$datadir/pravila.html")) include"$datadir/pravila.html";
echo'</td></tr><tr><td class=row2><INPUT type=checkbox name=rulesplus><B>� ����������� � ��������� � ���������, � �������� ��.</B></td></tr><tr>
<td class=catBottom align=center height=28><input type=submit value="���������� �����������" class=mainoption></td>
</tr></table>
</form>'; 
} else {

print"<center><span class=maintitle>����������� �� ������</span><br>
<br><form action='tools.php?event=regnxt' method=post>

<table cellpadding=3 cellspacing=1 width=100% class=forumline><tr>
<th class=thHead colspan=2 height=25 valign=middle>��������������� ����������</th>
</tr><tr>
<td class=row1 width=35%><span class=gen>��� ���������:</span><span class=gensmall><br>��������� ������������ ������ �������, ��������� �����, ����� � ���� �������������</span></td>
<td class=row2><input type=text class=post style='width:200px' name=name size=25 maxlength=$maxname></td>
</tr><tr>
<td class=row1><span class=gen>��� ������:</span></td>
<td class=row2><input type=password class=post style='width:200px' name=pass size=25 maxlength=25></td>
</tr><tr>
<td class=row1><span class=gen>����� e-mail:</span><br><span class=gensmall>������� ������������ ����������� �����! �� ��� ����� ����� ���������� ��������� � ����� ���������.</span></td>
<td class=row2><input type=text class=post style='width: 200px' name=email size=25 maxlength=50></td>
</tr><tr>
<td class=row1><span class=gen>��� ���:</span><br></td>
<td class=row2><input type=radio name=pol value='1'checked> �������&nbsp;&nbsp; <input type=radio name=pol value='0'> �������</td>
</tr><tr><TD class=row2>�������� ���</TD><TD class=row2>";

if ($antispam==TRUE) nospam(); // �������� !

echo'</td></tr><tr>
<td class=row2 colspan=2><span class=gensmall>* ��� ���� ����������� � ����������<BR>
** ��� ������ ����� ����� ��������� �� ����� ����������� �����, ������� �� ����������</span></td>
</tr><tr>
<td class=catBottom colspan=2 align=center height=28><input type=submit value="����������" class=mainoption></td>
</tr></table></form>';
}
}



if ($_GET['event']=="find") { // �����
$minfindme="3"; //����������� ���-�� �������� � ����� ��� ������
echo'<BR><form action="tools.php?event=go&find" method=POST>
<center><table class=forumline align=center width=700>
<tr><th class=thHead colspan=4 height=25>�����</th></tr>
<tr class=row2>
<td class=row1>������: <input type="text" style="width: 250px" class=post name=findme size=30></TD>
<TD class=row1>���: <select style="FONT-SIZE: 12px; WIDTH: 120px" name=ftype>
<option value="0">&quot�&quot
<option value="1" selected>&quot���&quot
<option value="2">��� ����� �������
</select></td>
<td class=row1><INPUT type=checkbox name=withregistr><B>� ������ ��������</B></TD>
<input type=hidden name=gdefinder value="1">
</tr>';

print"<TR><TD class=row1 colspan=4>��� ����� ��� ��������� ������������������� ������������: 
<SELECT name=user style='FONT-SIZE: 14px; WIDTH: 250px'><OPTION value='0' selected> - - ������� ������������ - -</OPTION>";
$slines = file("data/user.php"); $smax=count($slines); $i="1"; do {
$slines[$i]=replacer($slines[$i]); $dts=explode("|",$slines[$i]);
print "<OPTION value=\"$dts[2]\">$dts[2]</OPTION>\r\n"; $i++; } while($i < $smax);
echo'</SELECT></TD>


<tr class=row1>
<td class=row1 colspan=4 width="100%">
���� ��������:<br><UL>
<LI><B>&quot�&quot</B> - ������ �������������� ��� �����;</LI><br>
<LI><B>&quot���&quot</B> - ���� ���� �� ���� �� ����;</LI><br>
<LI><B>&quot��� ����� �������&quot</B> - � ������� ��������� ����� ����� �� 100% ��������������� ������ �������;</LI><BR><BR>
<LI><B>&quot� ������ ��������&quot</B> - ����� ������ � ������ ��������� ���� ��������;</LI><BR><BR>
</UL>������ ���� ��� ������, ������� ���������� � ��������� ���� ������. ��������, ��� ������� &quot�����&quot ����� ������� ����� &quot�����&quot, &quot������&quot, &quot�������&quot � ������ ������.
</td>
</tr><tr><td class=row1 colspan=4 align=center height=28><input type=submit class=post value="  �����  "></td></form>
</tr></table><BR><BR>';

print "����������� �� �����: <BR> - ����������� ���-�� ��������: <B>$minfindme</B>";
}





if (isset($_GET['find'])) {

//exit("����� �������� �� ��������!");
$minfindme="2"; //����������� ���-�� �������� � ����� ��� ������
$time=explode(' ', microtime()); $start_time=$time[1]+$time[0]; // ��������� ��������� ����� ������� ������

$gdefinder="1"; $ftype=$_POST['ftype']; 
if (!ctype_digit($ftype) or strlen($ftype)>2) exit("<B>$back. ������� ������. ������� ����� �� �����.</B>");
if (!isset($_POST['withregistr'])) $withregistr="0"; else $withregistr="1";

if ($_POST['user']!="0") {$findme=$_POST['user']; $gdefinder="3"; $ftype="2"; $withregistr="1";} //  ���� ������ ����� �� ����� �����
else $findme=$_POST['findme']; 

$findme=replacer($findme); // ������ �� ������
$findmeword=explode(" ",$findme); // ��������� $findme �� �����
$wordsitogo=count($findmeword);
$findme=trim($findme); // �������� ���������� ������� 
if ($findme == "" || strlen($findme) < $minfindme) exit("$back ��� ������ ����, ��� ����� $minfindme ��������!</B>");

// ��������� ���� � ������ ������� � ���������� ����� ������ � �����������

setlocale(LC_ALL,'ru_RU.CP1251'); // ! ��������� ������ �������, ���������� � ���������� � � �������� �������


// ������ ���� - ������� ���-�� ������� (���������� � ���������� $itogofid)
$mainlines=file("$datadir/wrforum.dat");$i=count($mainlines); $itogofid="0";$number="0"; $oldid="0"; $nump="0";
do {$i--; $dt=explode("|",$mainlines[$i]);
if ($dt[3]==FALSE) { $maxzd=$dt[9];
if (!ctype_digit($maxzd)) $maxzd=0;  // ��������� �¨��� ������� �� �����
if ($maxzd<1) {$itogofid++; $fids[$itogofid]=$dt[2]; }} // $itogofid - ����� ���-�� �������
} while($i > "0");


// ������ ���� - ��������� ���� � ������� (���� �� ����������) � ��������� � ���������� $topicsid ��� ����� ���
do { $fid=$fids[$itogofid];
if (is_file("$datadir/$fid.dat")) {
$msglines=file("$datadir/$fid.dat");

unset($topicsid); if (count($msglines)>0) { $lines=file("$datadir/$fid.dat"); $i=count($lines);
do {$i--; $dt=explode("|",$lines[$i]); $topicsid[$i]="$dt[2]$dt[3]";} while($i > "0"); }


// ������ ���� - ��������������� ��������� ������ ����
if (isset($topicsid)) {
$ii=count($topicsid);
do {$ii--;
$id = str_replace("\r\n","",$topicsid[$ii]);

if (is_file("$datadir/$id.dat")) { // ���� ���� ����? ������, ��� ����� � ����������� ������, ����� ��� ��������� ��� ���������.
$file=file("$datadir/$id.dat"); $iii=count($file);

// ���¨���� ���� - ��������������� ���� � ������ ���� ������� ���������
if ($iii>0) { // ���� ���� � ����������� �� ������
do {$iii--; 
$lines = file("$datadir/$id.dat");
$dt = explode("|", $lines[$iii]); if (!isset($dt[4])) $dt[4]=" ";

if ($gdefinder=="0") {$msgmass=array($dt[2],$dt[3],$dt[4]); $gi="3"; $add="�� <B>�����, �����, ���������</B> ";}
if ($gdefinder=="1") {$msgmass=array($dt[14]); $gi="1"; $add="� <strong>�����</strong> ";}
if ($gdefinder=="2") {$msgmass=array($dt[3],$dt[4]); $gi="2"; $add="�� <B>����� � ���������</B> ";}
if ($gdefinder=="3") {$msgmass=array($dt[8]); $gi="1"; $add="� <B>�����</B> ";}
if ($gdefinder=="4") {$msgmass=array($dt[3]); $gi="1"; $add="� <B>���������</B> ";}

// ���� �� ������ ������ (0,1,2,3,4)
do {$gi--;

$msg=$dt[14];
$msdat=$msgmass[$gi];
$stroka="0"; $wi=$wordsitogo;

// ���� �� ������� ����� ������� !
do {$wi--;


// ���� ������� ������
if ($withregistr!="1") // ������������������� ����� - c����� "i" ����� ������������ ������������ ������� - /
   {
    if ($ftype=="2") 
        { if (stristr($msdat,$findme)) // ����� �� "���� ����� �������" ��� ����� ��������
          { $stroka++; $msg=str_replace($findme," <b><u>$findme</u></b> ",$msg); }
        } else {
           $str1=strtolower($msdat);  
           $str2=strtolower($findmeword[$wi]); 
           if ($str2!="" and strlen($str2) >= $minfindme)
              { if (stristr($str1,$str2)) // ����� ��� ����� �������� ��� ������ ������ ��������
                { $stroka++; $msg=str_replace($findmeword[$wi]," <b><u>$findmeword[$wi]</u></b> ",$msg); }
              }
          }
        }

else  // if ($withregistr!="1")
   {
    if ($ftype=="2")
       {
        if (strstr($msdat,$findme)) // ����� �� "���� ����� �������" C ����� ��������
           {
            $stroka++;
            $msg=str_replace($findme," <b><u>$findme</u></b> ",$msg);
           }
       }
     else {
           if ($msdat!="" and strlen($findmeword[$wi]) >= $minfindme)
              {
               if (strstr($msdat,$findmeword[$wi])) // ����� � ������ �������� ��� ������ ������ ��������
                  {
                   $stroka++;
                   $msg=str_replace($findmeword[$wi]," <b><u>$findmeword[$wi]</u></b> ",$msg);
                  }
              }
          }

   }   // if ($withregistr!="1")

} while($wi > "0"); // ����� ����� �� ������� ����� �������


// �������������� �������������� ���������, � ���� ��������� ������������� �������� - ������� ���
if ($ftype=="0") { if ($stroka==$wordsitogo) $printflag="1"; }
if ($ftype=="1") { if ($stroka>"0") $printflag="1"; }
if ($ftype=="2") { if ($stroka==$wordsitogo) $printflag="1"; }

if (!isset($printflag)) $printflag="0";
    if ($printflag=="1")
       { $msg=str_replace("<br>", " &nbsp;&nbsp;", $msg); // �������� � ��������� <br> �� ���� ��������

if (strlen($msg)>150)
{
 $ma=strpos($msg,"<b>"); if ($ma > 50) $ma=$ma-50; else $ma=0;
 $mb=strrpos($msg,">b/<"); if (($mb+50) > strlen($msg)) $mb=strlen($msg); else $mb=$mb+50;
 $msgtowrite="..."; $msgtowrite.=substr($msg,$ma,$mb); $msgtowrite.="...";
 $msgtowrite=substr($msg,0,400);
}
else $msgtowrite=$msg;




if (!isset($m)) {
print"
<small><BR>�� ������� '<U><B>$findme</B></U>' � ���$add �������: <HR size=+2 width=99% color=navy>
<BR><form action='tools.php?event=go&find' method=POST>
<table class=forumline align=center width=700>
<tr><th class=thHead colspan=4 height=25>��������� ����� �� ���������</th></tr>
<tr class=row2>
<td class=row1>������: <input type='text' value='$findme' style='width: 250px' class=post name=findme size=30>
<INPUT type=hidden value='1' name=ftype>
<INPUT type=hidden value='0' name=user>
<input type=hidden name=gdefinder value='1'>
<input type=submit class=post value='  �����  '></td></table></form><br>
<table width=100% class=forumline><TR align=center class=small><TH class=thCornerL><B>�</B></TH><TH class=thCornerL width=35%><B>���������</B></TH><TH class=thCornerL width=70%><B>����� ���������</B></TH><TH class=thCornerL><B>����������<BR> � ����</B></TH></TR>"; $m="1"; }

$in=$iii+1; if ($in>$msg_onpage) {$page=ceil($in/$msg_onpage);} else $page="1"; // ����������� ������ �������� � ����� ���������

if ($oldid!=$id and $number<100) { $number++; $msgnumber=$iii;

if ($nump>1) $anp="$nump"; else $anp="1";
if ($number>1) print"<TD class=row1 align=center>$anp</TD></TR><TR height=25>";

print "<TD class=row1 align=center><B>$number</B></TD>
<TD class=row1><A class=listlink href='index.php?id=$id&page=$page#m$iii' target=_blank>$dt[5]</A></TD>
<TD class=row1>$msgtowrite</TD>";
$printflag="0"; $nump="0";

} else $nump++;

if ($number>=100) { print"</TR></TABLE> * ����� ���������������, ��� ���������� ����� 100 ���������!"; $gi=0; $iii=0; $ii=0; $itogofid=0;}

$oldid=$id;
} // if $printflag==1

} while($gi > "0"); // ����� ����� �� ����� ������

} while($iii > "0");
} // ���� ���� � ����������� ��������

} // if is_file("$datadir/$id.dat")
} while($ii > "0");

} // if isset($topicsid)

} // if ���� $fid.dat �� ����

$itogofid--;
} while($itogofid > "0");

if (!isset($m)) echo'<table width=80% align=center><TR><TD>�� ������ ������� ������ �� �������.</TD></TR></table>';

$time=explode(' ',microtime());
$seconds=($time[1]+$time[0]-$start_time);
echo "</TR></table><HR size=+2 width=99% color=navy><BR><p align=center><small>".str_replace("%1", sprintf("%01.3f", $seconds), "����� ������: <b>%1</b> ������.")."</small></p>";

}

} // if isset($_GET['event']) - ��, ��� �������� ��� ������� ���������� $event

?>

</td></tr></table>
<center><small>Powered by <a href="http://www.wr-script.ru" title="������ �����������" class="copyright">WR-Foto</a> &copy; 1.2<br></small></center>
</body>
</html>
