<? // WR-foto v 1.2  //  02.08.15 �.  //  Miha-ingener@yandex.ru

// ������ razdel= �� �������. ���������!!!

error_reporting (E_ALL); // �������� - �� ����� ������������ � ������� �������!
// error_reporting(0); // ��������������� ��� ���������� ������!!!
@ini_set('register_globals','off');// ��� ������� �������� ��� ���� ��������� php

$sendadmin=FALSE;
$flashupload=1; // �������� FLASH-���������? 1/0

include "data/config.php";

$smwidth="150"; // ������ ���������������
$smheight="120"; // ������ ���������������
$valid_types=array("jpg","jpeg","gif","png");  // ���������� ���������� ����������� ������

// ���������� URL �������
$host=$_SERVER["HTTP_HOST"]; $self=$_SERVER["PHP_SELF"]; $furl=str_replace('index.php','',"http://$host$self");

// ��������, ����� ������� �� ����������� ������ �������� (�� 07.2016 �.)
if (isset($_GET['add'])) { if ($_GET['add']=="razdel") { header("HTTP/1.1 404 Moved Permanently"); header("Location: $furl"); } }

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


// ������� �������� ����������� �����. ����������: addtop();
function addtop() { global $wrfname,$fskin,$date,$time;
if (isset($_COOKIE['wrfcookies'])) {$wrfc=$_COOKIE['wrfcookies']; $wrfc=replacer($wrfc); $wrfc=explode("|", $wrfc);  $wrfname=$wrfc[0];} else {unset($wrfname); unset($wrfpass); $wrfpass="";}
if (isset($wrfname)) { // ���� ���� � ������� ���
print"<table width=98% border=0 cellSpacing=0 cellPadding=2><tr><td class=catHead colspan=2 height=18><span class=cattitle>
<nobr><h2 style=\"padding: 5px; margin: 1px\">������� ������</h2></nobr></span></td></tr></table><br>
<a href='tools.php?event=profile&pname=$wrfname'>��� �������</a>&nbsp; 
<a href='index.php?event=clearcooke' class=mainmenu>����� [<B>$wrfname</B>]</a><br><br>";
} else { 
print "<span class=mainmenu>
<a href='tools.php?event=reg' class=mainmenu>�����������</a>&nbsp;&nbsp;
<a href='tools.php?event=login' class=mainmenu> ����</a>";}
return true;}



function prcmp ($a, $b) {if ($a==$b) return 0; if ($a<$b) return -1; return 1;} // ������� ����������


/* ������� img_resize($src,$dest,$width,$height,$rgb,$quality): ��������� thumbnails
������������ ���������: ��� ��������� �����, ��� ������������� �����, ������ � ������ ������������� �����������, � ��������
�������������� ���������: ���� ���� (�� ��������� - �����), �������� ������������� JPEG, �� ��������� - ������������ (100) */
function img_resize($src, $dest, $width, $height, $rgb=0xFFFFFF, $quality=95) {
  if (!file_exists($src)) return false;
  $size = getimagesize($src);
  if ($size === false) return false;
  // ���������� �������� ������ �� MIME-����������, ���������������
  // �������� getimagesize, � �������� ��������������� �������
  // imagecreatefrom-�������.
  $format = strtolower(substr($size['mime'], strpos($size['mime'], '/')+1));
  $icfunc = "imagecreatefrom" . $format;
  if (!function_exists($icfunc)) return false;

  $x_ratio = $width / $size[0];
  $y_ratio = $height / $size[1];

  $ratio       = min($x_ratio, $y_ratio);
  $use_x_ratio = ($x_ratio == $ratio);

  $new_width   = $use_x_ratio  ? $width  : floor($size[0] * $ratio);
  $new_height  = !$use_x_ratio ? $height : floor($size[1] * $ratio);
  $new_left    = $use_x_ratio  ? 0 : floor(($width - $new_width) / 2);
  $new_top     = !$use_x_ratio ? 0 : floor(($height - $new_height) / 2);

  $isrc = $icfunc($src);
  $idest = imagecreatetruecolor($width, $height);

  imagefill($idest, 0, 0, $rgb);
  imagecopyresampled($idest, $isrc, $new_left, $new_top, 0, 0, 
    $new_width, $new_height, $size[0], $size[1]);

  imagejpeg($idest, $dest, $quality);

  imagedestroy($isrc);
  imagedestroy($idest);
return true; }



function nospam() { global $max_key,$rand_key; // ������� ��������
if (array_key_exists("image", $_REQUEST)) { $num=replacer($_REQUEST["image"]);
for ($i=0; $i<10; $i++) {if (md5("$i+$rand_key")==$num) {imgwr($st,$i); die();}} }
$xkey=""; mt_srand(time()+(double)microtime()*1000000);
$dopkod=mktime(0,0,0,date("m"),date("d"),date("Y")); // ���.���: �������� ������ 24 ����
$stime=md5("$dopkod+$rand_key");// ���.���
echo'<TR><TD class=row2><span class=genmed><B>�������� ���</B></span>:</TD><TD class=row2>';
for ($i=0; $i<$max_key; $i++) {
$snum[$i]=mt_rand(0,9); $psnum=md5($snum[$i]+$rand_key+$dopkod);
echo "<img src=antispam.php?image=$psnum border='0' alt=''>\n";
$xkey=$xkey.$snum[$i];}
$xkey=md5("$xkey+$rand_key+$dopkod"); //����� + ���� �� config.php + ��� ���������� ����� 24 ����
print" <input name='usernum' id='txt_usernum' class=post type='text' style='WIDTH: 70px;' maxlength=$max_key size=6> 
(������� �����, ��������� �� ��������) <input name=xkey type=hidden value='$xkey'><input name=stime type=hidden value='$stime'></TD></TR>";
return; }


// ���� ������� - ���������� ������,������ � ������ ��������� ��� ������� ������ � ������������
if (isset($_GET['loginza'])) {

if (isset($_GET['save'])) {
if (isset($_POST['name'])) $name=replacer($_POST['name']); else exit();
if (isset($_POST['email'])) $email=replacer($_POST['email']); else exit();
if (isset($_POST['usernum'])) $usernum=replacer($_POST['usernum']); else exit();
setcookie("wrfotocookies", "$name|$email|$usernum|", time()+1728000);
Header("Location: index.php"); exit; }

print"<center><h1>���������</h1>
<table border=0 width=450 align=center><form action='index.php?loginza&save' method=POST>
<tr><td class=row1 width=100 height=25><span class=gen><b>���</b></span></td><td class=row1><input type=text name=name maxlength=$maxname size=35></td></tr>
<tr><td class=row2 height=25><span class=gen>E-mail</span></td><td class=row2><input type=text name=email size=35></td></tr> 
<tr><td class=row2 height=25><span class=gen><B>�������� �� ������:</B></span></td><td class=row2><input name='usernum' type='text' maxlength=20 size=25> ($antispam2012v)</td></tr>
</tr><tr><td class=row1 colspan=4 align=center height=28><input type=submit class=post value='  ��������� ������  '></td></form>";
exit;}













// ��������� - ���� ������: ��� 1
if (isset($_GET['addrepa'])) {

if (!isset($_GET['fotoname'])) exit("��� ������ ���������� fotoname."); $fotoname=replacer($_GET['fotoname']);

/* �������� ��� ������� <table border=1 align=center>
<TR align=center><TD><img src='smile/sad.gif'></TD><TD>&nbsp;</TD><TD>&nbsp;</TD><TD>&nbsp;</TD><TD><img src='smile/smile.gif'></td></tr>
<TR align=center><TD width=30>1</td><TD>2</td><TD>3</td><TD>4</td><TD>5</td></tr></table> */

print "<html><head><title>��������� �������� ����������:</title></head><body leftMargin=0 topMargin=0 rightMargin=0>
<center><table cellpadding=0 cellspacing=8><TR><FORM action='index.php?repasave' method=post>
<TD colspan=7 align=center><B>��������� �������� ����������</B></TD></TR><TR height=40>
<TD bgcolor=#880003><font size=+2 color=white>-5<INPUT name=repa type=radio value='-5'></TD>
<TD bgcolor=#FF2025><font size=+2 color=white>-2<INPUT name=repa type=radio value='-2'></TD>
<TD bgcolor=#FFB7B9><font size=+2 color=white>-1<INPUT name=repa type=radio value='-1'></TD>
<TD bgcolor=#FFFF00><font size=+2 color=#FF8040>0<INPUT name=repa checked type=radio value='0'></TD>
<TD bgcolor=#A4FFAA><font size=+2 color=white>+1<INPUT name=repa type=radio value='+1'></TD>
<TD bgcolor=#00C10F><font size=+2 color=white>+2<INPUT name=repa type=radio value='+2'></TD>
<TD bgcolor=#00880B><font size=+2 color=white>+5<INPUT name=repa type=radio value='+5'></TD></TR>
<TR><TD colspan=7><B>�����������:</B>  <INPUT type=hidden name=fotoname value='$fotoname'><INPUT type=text name=pochemu size=45 value=''><INPUT type=submit value=���������></td></TR>
</TABLE></FORM>";

if (is_file("$datadir/rating.dat")) { // ���� � ����� ���� �� ���� ���� � �������, ���� ����
$file="$datadir/rating.dat"; $lines=file("$file"); $i=count($lines);
print"<table border=1 cellpadding=2 cellspacing=0 width=100%><TR><TD colspan=5 align=center><B>��� ��������� �������� ����������</B></td></tr>
<TR align=center><TD>�����</TD><TD>IP</TD><TD>���</TD><TD width=55%>�����������</TD></TR>";
$sbal=null; $itogo=null; $chislo=null;
if ($i>0) do {$i--; $dt=explode("|",$lines[$i]);
$chislo=$dt[1];
if ($dt[1]>0) $dt[1]="<TD align=center bgcolor=#B7FFB7><B>$dt[1]"; else $dt[1]="<TD align=center bgcolor=#FF9F9F><B>$dt[1]";
if ($dt[2]==$fotoname) {
$dt[0]=date("d.m.y � H:i",$dt[0]); 
print"<TR><TD align=center><small>$dt[0]</small></TD><TD align=center><B>$dt[3]</B></TD>$dt[1]</B></TD><TD><small>$dt[4]</small></TD></TR>";
$itogo++; $sbal=$sbal+$chislo;
}
} while($i>0);
if ($itogo>0) $sbal=round($sbal/$itogo,2);
print"</table><B>$sbal</B>"; } // ���� ���� ����
exit; }



if (isset($_GET['repasave'])) {  // ��������� - ����������: ��� - 2

// ��������� ������, �������� ������
if (!isset($_POST['fotoname'])) exit("��� ������ ���������� name."); $name=replacer($_POST['fotoname']);
if (isset($_POST['repa'])) $repa=replacer($_POST['repa']); else exit("��� ������ ���������� repa");
if (isset($_POST['pochemu'])) $pochemu=replacer($_POST['pochemu']); else exit("������� ������� ����� ���������");
if (!is_numeric($repa)) exit("<B>$back. ������� ������. �� ��������, ����!");
if ($repa>5 or $repa<-5) exit("<B>$back. ������� ������. ���� ����� ������ ������ �� +-5 �������. �� ��������, ����!");
if (strlen($pochemu)<1 or strlen($pochemu)>150) exit("<B>$back. ����� ������� ������ ���� ������! � ���� �� ����� 150 ��������!");

// ������ �� �������� �� ����. ������ ���� �� ����, ���� ��� ���� ���� � ������ ���� - �� �����!
if (isset($_COOKIE[$name])) exit("<br><br><br><br><center><h3><font color=red>�� ��� ���������� �� ��� ����!</font><br><br> ��������� ����������� ��������� ��� � �����!<br> �������� ����������� ���������!</h3></center>");
//setcookie("$name", "+", time()+86400);
$today=time(); $ip=$_SERVER['REMOTE_ADDR']; // ���������� IP �����

// ��������� ��������� ����������� � ������
$file="$datadir/rating.dat"; $lines=file("$file"); $i=count($lines)-1; $dtt=explode("|",$lines[$i]);

// ��������� IP ���������� �������������, ���� ����� �� ��� ������ - �����
if ($dtt[2]==$name and $dtt[3]==$ip) exit("���������� � ������ IP �� ���� � �� �� ���� ���������!");

// ���� ��� �����, ����� ����� ���� ������������� � ������ IP ������ �� ���� ����, ��, ��������������� ������!
// if ($dtt[3]==$ip) exit("���������� � ������ IP �� ��������� ���� ���������!"); 

// ��������� ����� ���������� ����������� (����) ��������� ���������� �� ���� 1 ��� � 60 ������
if (($today-$dtt[0])<=30) exit("�������� ������ �� �����. ���������� �� ����� ���� ���� 1 ��� � 30 ������ ���������!");

//���� � UNIX-�������|������� �����|��� ����������|IP-����|�����������||||
$fp=fopen("$datadir/rating.dat","a+");
flock ($fp,LOCK_EX);
fputs($fp,"$today|$repa|$name|$ip|$pochemu||||\r\n");
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);

exit("<div align=center><BR><BR><BR>������� <B>�������</B> ����������.<BR><BR><BR><a href='' onClick='self.close()'><b>������� ����</b></a></div>"); }






function addmsg($qm) { // ������� ���������� ����/���������
global $wrfname,$maxname,$canupfile,$antispam,$max_key,$rand_key,$max_upfile_size,$smile,$smiles,$valid_types,$datadir,$flashupload,$id,$antispam2012v;

//�������� ������� IP-������������ �� ���������� ���������� (���� bad_ip.dat)
$ip=$_SERVER['REMOTE_ADDR']; // ���������� IP �����
if (is_file("$datadir/bad_ip.dat")) { $lines=file("$datadir/bad_ip.dat"); $i=count($lines);
if ($i>0) {do {$i--; $idt=explode("|", $lines[$i]);
   if ($idt[0]===$ip) exit("<TR><TD colspan=2><center><br><br><B>������������ ������������ ��� ������ IP: $ip<br> ����������� ��������� ���-���� �� ��������� �������:<br><br> <font color=red><B>$idt[1].</B></font><br><br>��� ��������� ������������� ����������,<br> � ��� ��������� ������� � ���������� ������������� ���������!</B><br><br><br><br>");
} while($i > "1");} unset($lines);}

$max=round($max_upfile_size/10485.76)/100;

print"<form id=\"form1\" action=\"index.php?event=addanswer\" method=\"post\" enctype=\"multipart/form-data\">";

$addrazdel=FALSE; if (isset($_GET['add'])) { if ($_GET['add']=="newrazdel") $addrazdel=TRUE;}

if ($addrazdel==TRUE) {
print"<TR><TD class=row1><span class=genmed><b>�������� ����</B></span></TD><td colspan=2 class=row1 valign=top>���������� <B>";
foreach($valid_types as $v) print "$v, ";
print"</B> ����������� ��������� ������: <B>$max ��.</B><br>
<input type=file name=file class=post size=70></td></tr>";
} // $addrazdel=FALSE

// ��������� ���� ����� ������� � �������� ����� ����, ��� ��������� ����!
if (isset($_COOKIE['wrfotocookies'])) {$wrfc=$_COOKIE['wrfotocookies']; $wrfc=replacer($wrfc); $wrfc=explode("|", $wrfc); $name=$wrfc[0]; $email=$wrfc[1]; $usernum=$wrfc[2]; } else {$name=""; $email=""; $usernum="";}
//if (isset($wrfc)) print_r($wrfc);


print"<tr><td class=row1 width=200 height=25><span class=gen><b>���</b></span></td><td class=row1>
<input type=text name=name id='txt_name' class=post value='$name' maxlength=$maxname size=65></td></tr>

<tr><td class=row2 width=200 height=25><span class=gen>E-mail</span></td><td class=row2>
<input type=text name=email id='txt_email' value='$email' class=post size=65></td></tr>

<tr><td class=row1 valign=top><span class=genmed><b>�������� ���� (����� ����)</b><br><br>
<small>* ��� ���������� ���������� ���� � �������� ��������� ���������� ����� ���� � �������.</small></td>
<td class=row1 valign=top><textarea name=msg id='txt_msg' cols=62 rows=2 class=post></textarea></td></tr>";


if ($flashupload==TRUE and $addrazdel==FALSE) { 

$max=round($max_upfile_size/10485.76)/100;

print"<tr><td class=row2 width=200 height=25><span class=gen><B>�������� �� ������:</B></span></td><td class=row2>
<input name='usernum' id='txt_usernum' class=post type='text' value='$usernum' maxlength=20 size=25> ($antispam2012v)</td></tr>";

print"
<script type=\"text/javascript\" src=\"flashupload/swfupload.js\"></script>
<script type=\"text/javascript\" src=\"flashupload/swfupload.queue.js\"></script>
<script type=\"text/javascript\" src=\"flashupload/fileprogress.js\"></script>
<script type=\"text/javascript\" src=\"flashupload/handlers.js\"></script>

<script type=\"text/javascript\">
var swfu;

window.onload = function() {
var settings = {
flash_url : 'flashupload/swfupload.swf',
upload_url: 'index.php?event=addanswer&id=$id',
file_post_name : 'Filedata',
post_params: {'PHPSESSID' : '".session_id()."',
'msg':'txt_msg',
'name':'txt_name',
'email':'txt_email',
'usernum':'txt_usernum'},
file_size_limit : '".$max." MB',
file_types : '*.jpg;*.png;*.gif;*.jpeg;',
file_types_description : '���������: ',
file_upload_limit : 200,
file_queue_limit : 0,
custom_settings : {
progressTarget : 'fsUploadProgress',
cancelButtonId : 'btnCancel'
},
debug: false,

// ��������� ������
button_image_url: 'flashupload/uploadbtn199x36.png',
button_width: '199',
button_height: '36',
button_placeholder_id: 'spanButtonPlaceHolder',
file_queued_handler : fileQueued,
file_queue_error_handler : fileQueueError,
file_dialog_complete_handler : fileDialogComplete,
upload_start_handler : uploadStart,
upload_progress_handler : uploadProgress,
upload_error_handler : uploadError,
upload_success_handler : uploadSuccess,
upload_complete_handler : uploadComplete,
queue_complete_handler : queueComplete
};

swfu = new SWFUpload(settings)
}
</script>";


print"
<TR><TD class=row1><span class=genmed><b>�������� ����</B><br><br> *
 ���������: <B>���������� ���� ������: "; foreach($valid_types as $v) print "$v, "; print"</B><br> ����������� ��������� ������ ������ ����: <B>$max ��.</B><br>
</span></TD><td colspan=2 class=row1 valign=top>
<div class=\"fieldset flash\" id=\"fsUploadProgress\"><span class=\"legend\">������� ��������</span></div>
<div id=\"divStatus\">0 ������ ���������</div><div><span id=\"spanButtonPlaceHolder\"></span>
<input id=\"btnCancel\" type=\"button\" value=\"�������� ��� ��������\" onclick=\"swfu.cancelQueue();\" disabled=\"disabled\" style=\"margin-left: 2px; font-size: 8pt; height: 29px;\" /></div>

<!--���� � ��� �������� �����-���� �������� � ��������� ����������, �� ����� ������ ��������������� <a href=\"index.php?add=newrazdel&id=$id\">����������� ����������� ����������.</a>--></div>";
} // ���� ������� ����-���������

if ($addrazdel==TRUE) {

if ($antispam==TRUE) nospam(); // �������� !
echo'<tr><td class=row1 colspan=2 align=center height=28><input type=submit tabindex=5 class=mainoption value=" ��������� ">&nbsp;&nbsp;&nbsp;<input type=reset tabindex=6 class=mainoption value=" �������� "></td></tr></table></form>';}

echo'</tr></table></form>';

return;} // ����� �������-����� ���������� ����/������



// ������ ����� �� ������� - ������� ����
if(isset($_GET['event'])) {if ($_GET['event']=="clearcooke") {setcookie("wrfcookies","",time()); Header("Location: $furl"); exit;}}




// ������� �������
if(isset($_GET['rules'])) { 
$frtname=""; $frname="������� �";
include("$fskin/top.html"); addtop();  // ���������� �����
echo'
<center><span class=maintitle>������� � ������� ������������� ����������</span><br><br>
<table cellpadding=8 cellspacing=1 width=950 class=forumline><tr><th class=thHead height=25 valign=middle>������� ������ � ������������</th></tr><tr>
<td class=row1><span class=gen>';
if (is_file("$datadir/pravila.html")) include"$datadir/pravila.html";
echo'</tr></table>';  exit; }





// ����� ������ ������� ��� ���������� ����
if(isset($_GET['addfoto'])) {
$frtname=""; $frname="���������� �������� � ���� �";
include("$fskin/top.html"); addtop();  // ���������� �����
$mainlines=file("$datadir/wrfoto.dat"); $mmax=count($mainlines); $i=0; $kolvo=""; $cn=0;
echo'<form action="index.php?add=newrazdel" metod=post>
<tr class=row1><TD>��������� / ������:</TD><TD><SELECT name=id class=maxiinput><option>�������� �������:</option>\r\n';

do { $dt=explode("|",$mainlines[$i]); $itogo="";
if (is_file("data/topic$dt[0].dat"))  {
$files=file("data/topic$dt[0].dat");
if (sizeof($files)>0) {
$j=count($files)-1; $submenu="<PRE><OPTION value=\"$dt[0]\"> - $dt[1]</OPTION>\r\n";

do { $mt=explode("|",$files[$j]);
$ctema=file("data/$mt[7].dat"); $kolvo=count($ctema);
if ($kolvo!="") {$kolvo="[$kolvo]"; $add1="";} else $kolvo="";
$submenu.="<OPTION value=\"$dt[3]\">&nbsp;&nbsp;&nbsp;&nbsp; - $mt[3] $kolvo</OPTION>\r\n";
$j--;
} while ($j>=0);
}//if (sizeof($files)>0)
}//if (is_file("data/topic$dt[0].dat"))

if ($dt[1]!="razdel" and $kolvo=="") print "<OPTION value=\"$dt[0]\"> - $dt[1]</OPTION>\r\n";
else {if ($cn==0) {$cn++; print "<optgroup label=' - $dt[2]'>";}}
if ($kolvo!="") print"$submenu";
$i++; $kolvo=0;
} while($i<$mmax);
if ($cn>0) echo'</optgroup>';
print"</select><input type=hidden name=add value=newrazdel><INPUT type=submit value=��������></form>";
exit; }













// ���������� ���� ��� ������ - ��� 1
if(isset($_GET['event'])) {



if ($_GET['event']=="addanswer") {

if ($stop==TRUE) exit("�������� ���������� ��� � ��������� ��������������!");

//�������� ������� IP-������������ �� ���������� ���������� (���� bad_ip.dat)
$ip=$_SERVER['REMOTE_ADDR']; // ���������� IP �����
if (is_file("$datadir/bad_ip.dat")) { $lines=file("$datadir/bad_ip.dat"); $i=count($lines);
if ($i>0) {do {$i--; $idt=explode("|", $lines[$i]);
   if ($idt[0]===$ip) exit("<noindex><script language='Javascript'>function reload() {location = \"$furl\"}; setTimeout('reload()', 10000);</script><center><br><br><B>������������ ������������ ��� ������ IP: $ip<br> ����������� ��������� ���-���� �� ��������� �������:<br><br> <font color=red><B>$idt[1].</B></font><br><br>��� ��������� ������������� ���������,<br> � ��� ��������� ����/��������� ������������� ���������!</B></noindex>");
} while($i > "1");} unset($lines);}

if (isset($_GET['id'])) $id=$_GET['id']; if ((!ctype_digit($id)) or (strlen($id)!=7)) exit();
$fid=substr($id,0,3);

// ��������� ������: �����, �����, ��������, �������� ���
if (isset($_POST['email'])) $email=replacer($_POST['email']); else $email="";
if (isset($_POST['name'])) $name=replacer($_POST['name']); else $name="";
if (isset($_POST['msg'])) $msg=replacer($_POST['msg']); else $msg="";
if (isset($_POST['usernum'])) $usernum=replacer($_POST['usernum']); else $usernum="";

// �������� ������ | ����� �� ����������� ��������� ������
$name=str_replace("|","&#124;",$name); $email=str_replace("|","&#124;",$email); $msg=str_replace("|","&#124;",$msg);

//--�-�-�-�-�-�-�-�--�������� ����--
if ($antispam2012==TRUE) {if (strtolower($antispam2012o)!=strtolower($usernum) or strlen($usernum)<1) 
{header("HTTP/1.1 500 File Upload Error"); exit("������� ��� �/��� �������� ����!");}}

if (strlen($name)<1 or strlen($msg)<3) {header("HTTP/1.1 500 File Upload Error"); exit("������� ��� �/��� �������� ����!");}

// ���� ���� �� �������� ��� ���� ������� �� ��������, �� ������� ������ 500
if (!isset($_FILES["Filedata"]) || !is_uploaded_file($_FILES["Filedata"]["tmp_name"]) || $_FILES["Filedata"]["error"] != 0) {
header("HTTP/1.1 500 File Upload Error"); echo $_FILES["Filedata"]["error"]; exit; }


if (isset($_FILES)) { //���� ������� ����, �� ��������� ������ � ��� �����

$ext = end(explode('.', strtolower($_FILES['Filedata']['name'])));
if (!in_array($ext, $valid_types)) return;

if ($max_upfile_size < $_FILES['Filedata']['size']) return;

if (is_uploaded_file($_FILES['Filedata']['tmp_name'])) {

// ��������������� ��� ����� �� �����: ���YYYYZZZZ, ��� ZZZZ - ��������� �����.
$key=mt_rand(1000,9999); $fileName = $filedir.'/'."$id$key.$ext";

move_uploaded_file($_FILES['Filedata']['tmp_name'], $fileName);

// ������ ���������� �����������
$smallfoto="$filedir/sm-$id$key.jpg";
img_resize("$fileName", "$smallfoto", $smwidth, $smheight);
if (is_file("$datadir/$id.dat")) $idlines=file("$datadir/$id.dat"); $iid=count($idlines)+1; $msg.=" $iid";

$fsize=$_FILES['Filedata']['size'];
$fotoksize=round($fsize/1024); // ������ ������������ ����� � ��.
$size=getimagesize($fileName); // ���������� �������� ����� (������ � ������)

$date=date("d.m.y"); // �����.�����.���
$time=date("H:i"); // ����:������:�������
$today=time();
$ip=$_SERVER['REMOTE_ADDR']; // ���������� IP �����
$fileName=str_replace("$filedir/",'',$fileName);
if (!is_file("$smallfoto")) $smallfoto=$fileName;
$smallfoto=str_replace("$filedir/",'',$smallfoto);

//$usernum - ��������� ���� ������ ��� �� ��������� ����� �� ������.

if (!is_file("$datadir/$id.dat")) $nlines2=1; else
{$nlinesdat=file("$datadir/$id.dat"); $nlines2=count($nlinesdat)+1; $ndt=explode("|", $nlinesdat[0]); $razdel_name=$ndt[3];}

header("Content-Type: text/html; charset=win1251");
$record="$name|$email||razdel_name|$msg|$date|$time|$id||$today|$name|razdel_name|1|$fileName|$fsize||$ip|$smallfoto|$size[0]|$size[1]|$fotoksize||||||\r\n";
$record=iconv("UTF-8","windows-1251",$record);
$record=str_replace("razdel_name","$razdel_name",$record);

if (strlen($id)==3) { // ����� ������ � ����
$fp=fopen("$datadir/topic$fid.dat","a+");
flock ($fp,LOCK_EX);
fputs($fp,$record);
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);}

$fp=fopen("$datadir/$id.dat","a+");
flock ($fp,LOCK_EX);
fputs($fp,$record);
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);



// ���������� +1 ��� ������ ����������� ����!
$realfid=null; $fotodetali=null;
$realbase="1"; if (is_file("$datadir/wrfoto.dat")) $mainlines=file("$datadir/wrfoto.dat");
$i=count($mainlines);
do {$i--; $dt=explode("|", $mainlines[$i]);
if ($dt[0]==$fid) {$realfid=$i; if ($dt[1]=="razdel") exit("$back. ������ ����� ����������� �� ����������");} // ����������� $realfid - � �/� ������
} while($i>0);

if ($realbase==TRUE) { // ���� ���������� ������� ����, � �� �����
$lines=file("$datadir/wrfoto.dat"); $max=sizeof($lines)-1;
$dt=explode("|", $lines[$realfid]); $dt[5]++;
$main_id="$fid$id";
$txtdat="$dt[0]|$dt[1]|$dt[2]|$main_id|$dt[4]|$dt[5]|$smname|$date|$time|$tektime|$smzag|$dt[11]|$dt[12]||||";
$kategory_name="$dt[1]";
// ������ ������ �� ������� ��������
$fp=fopen("$datadir/wrfoto.dat","a+");
flock ($fp,LOCK_EX);
ftruncate ($fp,0);//������� ���������� �����
for ($i=0;$i<=$max;$i++) {if ($i==$realfid) fputs($fp,"$txtdat\r\n"); else fputs($fp,$lines[$i]);}
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);
} // if ($realbase==TRUE)



if ($newmess==TRUE) { // ������ � ��������� ���� ������ ���������
if (is_file("$datadir/topic$fid.dat")) $nlines=count(file("$datadir/topic$fid.dat")); else $nlines=1;

$timestamp=time();
$newmessfile="$datadir/news.dat";
$newlines=file("$newmessfile"); $ni=count($newlines)-1; $i2=0; $newlineexit="";
$ntext="$fid|$id|$date|$time|$name|razdel_name|$msg|$nlines|$nlines2|kategory_name|$timestamp|$fileName|$smallfoto||";
$ntext=iconv("UTF-8","windows-1251",$ntext);
$ntext=str_replace("razdel_name","$razdel_name",$ntext);
$ntext=str_replace("kategory_name","$kategory_name",$ntext);
$ntext=str_replace("
", "<br>", $ntext);
//$newlineexit=$ntext;

// ���� ���������, ���� �� ��� ����� ��������� � ���� ����. ���� ���� - ���������. �� ������ - ������ ��� ���� ������.
for ($i=0;$i<=$ni;$i++) { $ndt=explode("|",$newlines[$i]);
if (isset($ndt[1])) {if ("$id"!=$ndt[1]) $newlineexit.="$newlines[$i]"; $i2++; } }

// ���������� ������ ��������� � ������ � ����� ��������� ��� � ����
if ($maxzd<1) { // ���� ���� �������� ��� ���� - ��� ����������� �� ������
if ($i2>0) { // ���� ���� ����� ����, �� ����� ���� ������, ����� ���� ������
$newlineexit.=$ntext;
$fp=fopen("$newmessfile","w");
flock ($fp,LOCK_EX);
fputs($fp,"$newlineexit\r\n");
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);
} else {
$fp=fopen("$newmessfile","a+");
flock ($fp,LOCK_EX);
fputs($fp,"$ntext\r\n");
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);}

$file=file($newmessfile);$i=count($file);
if ($i>="15") {
$fp=fopen($newmessfile,"w");
flock ($fp,LOCK_EX);
unset($file[0]);
fputs($fp, implode("",$file));
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);}
}
} // if ($newmess==TRUE)


// msg' : 'msg', 'name':'name','email':'email','usernum':'usernum
}
}
exit(0);
} // answer




if ($_GET['event']=="addtopic") {

if ($stop==TRUE) exit("�������� ���������� ��� � ��������� ��������������!");

if (isset($_POST['name'])) $name=$_POST['name'];
$name=trim($name); // �������� ���������� ������� 
$zag=$_POST['zag']; $msg=$_POST['msg'];

$fid=$_GET['id']; 
if ($_GET['event']=="addanswer") $id=substr($fid,3,4);
if (strlen($fid)>3) $fid=substr($fid,0,3);

if (isset($_POST['who'])) $who=$_POST['who']; else $who="";
if (isset($_POST['email'])) $email=$_POST['email']; else $email="";
if (isset($_POST['page'])) $page=$_POST['page'];
if (isset($_POST['maxzd'])) $maxzd=$_POST['maxzd']; else $maxzd="0"; if ($maxzd==null) $maxzd="0";
if ((!ctype_digit($maxzd)) or (strlen($maxzd)>2)) exit("<B>$back. ������� ������ �� ������ ��� ������ � ����� ����������</B>");

// ������ �� ������ fid
if (!ctype_digit($fid) or strlen($fid)>3) exit("<B>$back. ������� ������ ����� ����� �������. ����� ������ ��������� ������ ����� � ���� ����� 4 ��������</B>");

//--�-�-�-�-�-�-�-�--�������� ����--
if ($antispam==TRUE and !isset($_COOKIE['wrfcookies'])) {
if (!isset($_POST['usernum']) or !isset($_POST['xkey']) or !isset($_POST['stime']) ) exit("������ �� ����� �� ���������!");
$usernum=replacer($_POST['usernum']); $xkey=replacer($_POST['xkey']); $stime=replacer($_POST['stime']);
$dopkod=mktime(0,0,0,date("m"),date("d"),date("Y")); // ���.���. �������� ������ 24 ����
$usertime=md5("$dopkod+$rand_key");// ���.���
$userkey=md5("$usernum+$rand_key+$dopkod");
if (($usertime!=$stime) or ($userkey!=$xkey)) exit("����� ��������� ���!");}

// �������� �� ���� �������� � ������� - ���� �������������
// �� ��� ������, ���� wrfoto.dat - ����, ���������� ��������� �����

$realbase="1"; if (is_file("$datadir/wrfoto.dat")) $mainlines=file("$datadir/wrfoto.dat");
if (!isset($mainlines)) $datasize=0; else $datasize=sizeof($mainlines);
if ($datasize<=0) {if (is_file("$datadir/copy.dat")) {$realbase="0"; $mainlines=file("$datadir/copy.dat"); $datasize=sizeof($mainlines);}}
if ($datasize<=0) exit("$back. �������� � ����� ������, ���� ������ ���� - ���������� � ��������������");
$i=count($mainlines);

$realfid=null; $fotodetali=null;
do {$i--; $dt=explode("|", $mainlines[$i]);
if ($dt[0]==$fid) {$realfid=$i; if ($dt[1]=="razdel") exit("$back. ������ ����� ����������� �� ����������");} // ����������� $realfid - � �/� ������
} while($i>0);

if (!isset($realfid)) exit("$back. ������ � ������� �������. ��� �� ���������� � ����");

$dt=explode("|",$mainlines[$realfid]);
if (is_file("$datadir/topic$fid.dat")) {$tlines=file("$datadir/topic$fid.dat"); $tc=count($tlines)-2; $i=$tc+2; $ok=null;
// ����� ����������� �� ������, ����� ����. ���� ���� - �������, ���� - ������ ���������� ��������� ���������!
if ($_GET['event']=="addanswer") {
do {$i--; $tdt=explode("|", $tlines[$i]);
if ($tdt[7]=="$fid$id") {$ok=1; if ($tdt[8]=="closed") exit("$back ���� ������� � ���������� ��������� ���������!"); }
} while($i>0);
if ($ok!=1) exit("$back ���� ������� � ���������� ��������� ���������!"); }

} else $tc="2";
if ($dt[11]>0) {if ($tc>=$dt[11]) exit("$back. ��������� ����������� �� ���-�� ���������� ��� � ������ �������! �� ����� <B>$dt[11]</B> ���!");}

print"<html><head><link rel='stylesheet' href='$fskin/style.css' type='text/css'></head><body>";

if ($_GET['event']=="addtopic" and $cangutema==FALSE and !isset($wrfname)) exit("<center>������������� �������� ������ ��������� ����!</center><BR><BR>");
if ($_GET['event']=="addanswer" and $cangumsg==FALSE and !isset($wrfname)) exit("<center>������������� �������� ������ �������� � �����!</center><BR><BR>");

// ���� ���������� ��������� �� ������� ����� ����, ������� �������� � 1000
// ��������� ���� ���� � ������ � ������
if ($_GET['event']=="addtopic") { $id=1000; $id="$fid$id";
$allid=null; $records=file("$datadir/topic$fid.dat"); $imax=count($records); $i=$imax;
if ($i > 0) { do {$i--; $rd=explode("|",$records[$i]); $allid[$i]=$rd[7]; } while($i>0);
//natcasesort($allid); // ��������� �� �����������
do $id++; while(in_array($id,$allid) or is_file("$datadir/$id.dat"));
} else $id=$fid."1000"; } // if (event==addtopic)

// ���������� ��� ����� � ����� - ������ ��������
//if ($_GET['event']=="addtopic") { if ($fid<10) $add="0"; else $add="";
//do $id=mt_rand(1000,9999); while (file_exists("$datadir/$add$fid$id.dat"));
//$id="$add$fid$id"; }

if (!isset($_FILES['file']['name'])) exit("�������� ����� ����� ��� ��������!");

if (isset($_FILES['file']['name'])) { // ���� ��������� ����
$fotoname=replacer($_FILES['file']['name']); 
if (strlen($fotoname)<3) exit("������ �������� �����! ������� ��� ����� ��� �������� ����� ������� �����!");
else { $fotosize=$_FILES['file']['size']; // ��� � ������ �����

//---- ������ �� ������ -----

// 1. ��������� ����������
$ext = strtolower(substr($fotoname, 1 + strrpos($fotoname, ".")));
if (!in_array($ext, $valid_types)) {echo "<B>���� �� ��������.</B> ��������� �������:<BR>
- ��������� �������� ������ ������ � ������ ������������: <B>";
$patern=""; foreach($valid_types as $v) print"$v, ";
print"</B><BR>
- �� ��������� ��������� ���� � ������� �����������;<BR>
- ������� ����� ����� ��� ������ ����������� ����;</B><BR>"; exit;}

// 2. ������� ���-�� ����� � ��������� - ���� ������� ����� - ��������!
$findtchka=substr_count($fotoname, "."); if ($findtchka>1) exit("����� ����������� � ����� ����� $findtchka ���(�). ��� ���������! <BR>\r\n");

// 3. ���� � ����� ���� .php, .html, .htm - ��������! 
$bag="��������, �� � ����� ����� <B>���������</B> ������������ .php, .html, .htm";
if (preg_match("/\.php/i",$fotoname)) exit("��������� <B>.php</B> �������. $bag");
if (preg_match("/\.html/i",$fotoname)) exit("��������� <B>.html</B> �������. $bag");
if (preg_match("/\.htm/i",$fotoname)) exit("��������� <B>.htm</B> �������. $bag");

// 4. ������ �����
$fotoksize=round($fotosize/1024); // ������ ������������ ����� � ��.
$fotomax=round($max_upfile_size/1024); // ������������ ������ ����� � ��.
if ($fotoksize>$fotomax) exit("�� ��������� ���������� ������ �����! <BR><B>����������� ����������</B> ������: <B>$fotomax </B>��.<BR> <B>�� ���������</B> ��������� ���� ��������: <B>$fotoksize</B> ��!");

// ���� ������� ������� ���������� ����� ���������� ����� ��� �������� - ���������� ��������� ���
if ($_GET['event']!="addtopic") $numb="$fid$id"; else $numb=$id;
do $key=mt_rand(1000,9999); while (file_exists("$filedir/$numb$key.$ext")); $fotoname="$numb$key.$ext";

if (copy($_FILES['file']['tmp_name'], $filedir."/".$fotoname)) {print "<br><br>���� ������� ��������: $fotoname (������: $fotosize ����)"; $fotodetali="1|$fotoname|$fotosize|";}
else echo "������ �������� ����� - $fotoname...\n"; }}

// ��������� ������ ����. ���� "��������" ������ �������� � ������� 150 � 120 - �� ������ � ��� �� ������
// ���� ������ ������� ����������� �������� ����� - � �������� ���������
$size=getimagesize($_FILES['file']['tmp_name']);
if ($size[0]>$smwidth or $size[1]>$smheight) {
$smallfoto="sm-$fotoname";
if (img_resize("$filedir/$fotoname", "$filedir/$smallfoto", $smwidth, $smheight))  echo '����������� �������������� <B>�������</B>.'; else  echo '<font color=red><B>������ �������������� ����! ������� � GD-�����������!</B></font> ���������� � ��������������';
} else $smallfoto="$fotoname";

$tektime=time();
$name=wordwrap($name,30,' ',1); // ��������� ������� ������
$zag=wordwrap($zag,50,' ',1);
$name=str_replace("|","I",$name);
$who=str_replace("|","&#124;",$who);
$email=str_replace("|","&#124;",$email);
$zag=str_replace("|","&#124;",$zag);
$msg=str_replace("|","&#124;",$msg);

$smname=$name; if (strlen($name)>18) {$smname=substr($name,0,18); $smname.="..";}
$smzag=$zag; if (strlen($zag)>24) {$smzag=substr($zag,0,24); $smzag.="..";}

if (strlen($id)>8) exit("<B>$back. ����� ���� ������ ���� ������. ����������� ������ ������� ��� ������� ������</B>");
if (strlen(ltrim($zag))<3) exit("$back ! ������ � ����� ������ ���������!");

$ip=$_SERVER['REMOTE_ADDR']; // ���������� IP �����
$text="$name|$email|$who|$zag|$msg|$date|$time|$id||$tektime|$smname|$smzag|$fotodetali|$ip|$smallfoto|$size[0]|$size[1]|$fotoksize|||||";
$text=replacer($text); $exd=explode("|",$text); 
$name=$exd[0]; $zag=$exd[3]; $smname=$exd[10]; $smzag=$exd[11]; $smmsg=$exd[4];

if (!isset($name) || strlen($name) > $maxname || strlen($name) <1) exit("$back ���� <B>��� ������, ��� ��������� $maxname</B> ��������!</B></center>");
if (preg_match("/[^(\\w)| |(\\x7F-\\xFF)|(\\-)]/",$name)) exit("$back ���� ��� �������� ����������� �������. ��������� ������� � ���������� �����, �����, ������������� � ����.");
if (strlen(ltrim($zag))<3 || strlen($zag) > $maxzag) exit("$back ������� �������� �������� ���� ��� <B>�������� ��������� $maxzag</B> ��������!</B></center>");
if (strlen(ltrim($msg))<2 || strlen($msg) > $maxmsg) exit("$back ���� <B>��������� �������� ��� ��������� $maxmsg</B> ��������.</B></center>");
if (!preg_match('/^([0-9a-zA-Z]([-.w]*[0-9a-zA-Z])*@([0-9a-zA-Z][-w]*[0-9a-zA-Z].)+[a-zA-Z]{2,9})$/si',$email) and strlen($email)>30 and $email!="") exit("$back � ������� ���������� E-mail �����!</B></center>");

// ������� �������� ����� - ��������� ���������� ���������/���� ���������!
if (isset($tlines)) {
if ($tc<"-1") {$sdt[0]=null; $sdt[3]=null;} else {$last=$tlines[$tc+1]; $sdt=explode("|",$last);}

if ($_GET['event'] =="addtopic")  { // ���� ���������� ����: ��� = ��� � �����, ���� = ��������� ���� � �����
if ($name==$sdt[0] and $exd[3]==$sdt[3]) exit("$back. ����� ���� ��� �������. ������� �� ������ ���������!");

} else { // ���� ���������� ���������: ��� = ��� � �����, ��������� = ���������� ��������� � �����
if (is_file("$datadir/$fid$id.dat")) {$linesn=file("$datadir/$fid$id.dat"); $in=count($linesn)-1;
if ($in > 0) { $dtf=explode("|",$linesn[$in]);
if ($name==$dtf[0] and $exd[4]==$dtf[4]) exit("$back. ����� ��������� ��� ��������� � ������ ����. ������� �� ������ ���������!");}
}
}} // if $event=="addtopic"


$razdelname="";
if ($realbase==TRUE and $maxzd<1) { // ���� ���������� ������� ����, � �� �����
$lines=file("$datadir/wrfoto.dat"); $max=sizeof($lines)-1;
$dt=explode("|", $lines[$realfid]); $dt[5]++;
if ($_GET['event']=="addtopic") {$main_id="$id"; $dt[4]++;} else $main_id="$fid$id";
$txtdat="$dt[0]|$dt[1]|$dt[2]|$main_id|$dt[4]|$dt[5]|$smname|$date|$time|$tektime|$smzag|$dt[11]|$dt[12]||||";
$razdelname=$dt[1];
// ������ ������ �� ������� ��������
$fp=fopen("$datadir/wrfoto.dat","a+");
flock ($fp,LOCK_EX);
ftruncate ($fp,0);//������� ���������� �����
for ($i=0;$i<=$max;$i++) {if ($i==$realfid) fputs($fp,"$txtdat\r\n"); else fputs($fp,$lines[$i]);}
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);
} // if ($realbase==TRUE)

if ($newmess==TRUE and $maxzd<1) { // ������ � ��������� ���� ������ ���������
if (is_file("$datadir/topic$fid.dat")) $nlines=count(file("$datadir/topic$fid.dat")); else $nlines=1;

if (is_file("$datadir/$fid$id.dat")) $nlines2=count(file("$datadir/$fid$id.dat"))+1; else $nlines2=1;

$timestamp=time();
$newmessfile="$datadir/news.dat";
$newlines=file("$newmessfile"); $ni=count($newlines)-1; $i2=0; $newlineexit="";
$ntext="$fid|$main_id|$date|$time|$smname|$zag|$msg|$nlines|$nlines2|$razdelname|$timestamp|$fotoname|$smallfoto||";
$ntext=str_replace("
", "<br>", $ntext);

// ���� ���������, ���� �� ��� ����� ��������� � ���� ����. ���� ���� - ���������. �� ������ - ������ ��� ���� ������.
for ($i=0;$i<=$ni;$i++) { $ndt=explode("|",$newlines[$i]);
if (isset($ndt[1])) {if ("$fid$id"!=$ndt[1]) $newlineexit.="$newlines[$i]"; $i2++; } }

// ���������� ������ ��������� � ������ � ����� ��������� ��� � ����
if ($maxzd<1) { // ���� ���� �������� ��� ���� - ��� ����������� �� ������
if ($i2>0) { // ���� ���� ����� ����, �� ����� ���� ������, ����� ���� ������
$newlineexit.=$ntext;
$fp=fopen("$newmessfile","w");
flock ($fp,LOCK_EX);
fputs($fp,"$newlineexit\r\n");
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);
} else {
$fp=fopen("$newmessfile","a+");
flock ($fp,LOCK_EX);
fputs($fp,"$ntext\r\n");
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);}

$file=file($newmessfile);$i=count($file);
if ($i>="15") {
$fp=fopen($newmessfile,"w");
flock ($fp,LOCK_EX);
unset($file[0]);
fputs($fp, implode("",$file));
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);}
}
}
} // if ($newmess==TRUE)





if ($_GET['event'] =="addtopic")  { // ���������� ���� - ������ ������
// ����� � �����
$fp=fopen("$datadir/topic$fid.dat","a+");
flock ($fp,LOCK_EX);
fputs($fp,"$text\r\n");
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);

// ����� � ����
$fp=fopen("$datadir/$id.dat","a+");
flock ($fp,LOCK_EX);
fputs($fp,"$text\r\n");
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);

print "<script language='Javascript'>function reload() {location = \"index.php?id=$id\"}; setTimeout('reload()', 1500);</script>
<table width=100% height=80%><tr><td><table border=1 cellpadding=10 cellspacing=0 bordercolor=#224488 align=center valign=center width=60%><tr><td><center>
�������, <B>$name</B>, �� ���������� ����!<BR><BR>����� ��������� ������ �� ������ ������������� ���������� � ��������� ����.<BR><BR>
<B><a href='index.php?id=$id'>������ >>></a></B></td></tr></table></td></tr></table></center></body></html>";
exit; }



if ($_GET['event'] =="addanswer")  { //����� � ���� - ������ ������
$timetek=time(); $timefile=filemtime("$datadir/$fid$id.dat"); 
$timer=$timetek-$timefile; // ������ ������� ������ ������� (� ��������) 
$fp=fopen("$datadir/$fid$id.dat","a+");
flock ($fp,LOCK_EX);
fputs($fp,"$text\r\n");
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);
if ($timer<0) {$viptime=strtotime("+2 year"); touch("$datadir/$fid$id.dat",$viptime);}

print "<script language='Javascript'>function reload() {location = \"index.php?id=$fid$id$pageadd#m$in\"}; setTimeout('reload()', 1500);</script>
<table width=100% height=80%><tr><td><table border=1 cellpadding=10 cellspacing=0 bordercolor=#224488 align=center valign=center width=60%><tr><td><center>
�������, <B>$name</B>, ��� ����� ������� ��������.<BR><BR>����� ��������� ������ �� ������ ������������� ���������� � ������� ���� <BR><B>$zag</B>.<BR><BR>
<B><a href='index.php?id=$fid$id$pageadd#m$in'>������ >>></a></B></td></tr></table></td></tr></table></center></body></html>";
exit;
}
} //event










// �����
if(isset($_GET['find'])) {
$frtname=""; $frname="����� ���� �";
include("$fskin/top.html"); addtop();  // ���������� �����

$minfindme="3"; //����������� ���-�� �������� � ����� ��� ������
print"<center><span class=maintitle>����� ����</span><br><br>
<form action='index.php?event=go&findme' method=POST>
<center><table class=forumline align=center width=1000>
<tr><th class=thHead colspan=4 height=25>�����</th></tr>
<tr class=row2>
<td class=row1>������: <input type='text' style='width: 250px' class=post name=findme size=30></TD>
<TD class=row1>���: <select style='FONT-SIZE: 12px; WIDTH: 120px' name=ftype>
<option value='0'>&quot�&quot
<option value='1' selected>&quot���&quot
<option value='2'>��� ����� �������
</select></td>
<td class=row1><INPUT type=checkbox name=withregistr><B>� ������ ��������</B></TD>
<input type=hidden name=gdefinder value='1'>
</tr><tr class=row1>
<td class=row1 colspan=4 width=\"100%\">
���� ��������:<br><UL>
<LI><B>&quot�&quot</B> - ������ �������������� ��� �����;</LI><br>
<LI><B>&quot���&quot</B> - ���� ���� �� ���� �� ����;</LI><br>
<LI><B>&quot��� ����� �������&quot</B> - � ������� ��������� ����� ����� �� 100% ��������������� ������ �������;</LI><BR><BR>
<LI><B>&quot� ������ ��������&quot</B> - ����� ������ � ������ ��������� ���� ��������;</LI><BR><BR>
</UL>������ ���� ��� ������, ������� ���������� � ��������� ���� ������. ��������, ��� ������� &quot ���� &quot ����� ������� ����� &quot ���� &quot, &quot ���������� &quot, &quot ���������� &quot � ������ ������.
</td>

</tr><tr><td class=row1 colspan=4 align=center height=28><input type=submit class=post value='  �����  '></td></form>
</tr></table><BR><BR>";
print "����������� �� �����: <BR> - ����������� ���-�� ��������: <B>$minfindme</B>";
exit; }



if (isset($_GET['findme']))  {

$frtname=""; $frname="����� ���� �";
include("$fskin/top.html"); addtop();  // ���������� �����

$minfindme="2"; //����������� ���-�� �������� � ����� ��� ������
$time=explode(' ', microtime()); $start_time=$time[1]+$time[0];  // ��������� ��������� ����� ������� ������

$gdefinder="1"; $ftype=$_POST['ftype'];
if (!ctype_digit($ftype) or strlen($ftype)>2) exit("<B>$back. ������� ������. ������� ����� �� �����.</B>");
if (!isset($_POST['withregistr'])) $withregistr="0"; else $withregistr="1";

// ������ �� ������
$text=$_POST['findme'];
$text=replacer($text);
$findmeword=explode(" ",$text); // ��������� $findme �� �����
$wordsitogo=count($findmeword);
$findme=trim($text); // �������� ���������� ������� 
if ($findme == "" || strlen($findme) < $minfindme) exit("$back ��� ������ ����, ��� ����� $minfindme ��������!</B>");

// ��������� ���� � ������ ������� � ���������� ����� ������ � �����������

setlocale(LC_ALL,'ru_RU.CP1251'); // ! ��������� ������ �������, ���������� � ���������� � � �������� �������

// ������ ���� - ������� ���-�� ������� (���������� � ���������� $itogofid)
$mainlines = file("$datadir/wrfoto.dat");$i=count($mainlines); $itogofid="0";$number="0"; $oldid="0"; $nump="0";
do {$i--; $dt=explode("|", $mainlines[$i]);
if ($dt[1]!="razdel") { $maxzd=$dt[12]; if (!ctype_digit($maxzd)) $maxzd=0; } // ��������� �¨��� ������� �� �����
if ($dt[1]!="razdel" and $maxzd<1) {$itogofid++; $fids[$itogofid]=$dt[0];} // $itogofid - ����� ���-�� �������
} while($i > "0");

// ������ ���� - ��������� ���� � ������� (���� �� ����������) � ��������� � ���������� $topicsid ��� ����� ���
do { $fid=$fids[$itogofid];
if (is_file("$datadir/topic$fid.dat")) {
$msglines=file("$datadir/topic$fid.dat");

unset($topicsid); if (count($msglines)>0) { $lines=file("$datadir/topic$fid.dat"); $i=count($lines);
do {$i--; $dt=explode("|",$lines[$i]); $topicsid[$i]=$dt[7];} while($i > "0"); }


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
if ($gdefinder=="1") {$msgmass=array($dt[4]); $gi="1"; $add="� <B>�����</B> ";}
if ($gdefinder=="2") {$msgmass=array($dt[3],$dt[4]); $gi="2"; $add="�� <B>����� � ���������</B> ";}
if ($gdefinder=="3") {$msgmass=array($dt[2]); $gi="1"; $add="� <B>�����</B> ";}
if ($gdefinder=="4") {$msgmass=array($dt[3]); $gi="1"; $add="� <B>���������</B> ";}

// ���� �� ������ ������ (0,1,2,3,4)
do {$gi--;

$msg=$dt[4];
$msdat=$msgmass[$gi];
$stroka="0"; $wi=$wordsitogo;
// ���� �� ������� ����� ������� !
do {$wi--;



// ���� ������� ������
if ($withregistr!="1") // ������������������� ����� - c����� "i" ����� ������������ ������������ ������� - /
   {
    if ($ftype=="2") 
        {
        if (stristr($msdat,$findme))     // ����� �� "���� ����� �������" ��� ����� ��������
            { 
             $stroka++;
             $msg=str_replace($findme," <b><u>$findme</u></b> ",$msg);
            }
        }
     else {
           $str1=strtolower($msdat);  
           $str2=strtolower($findmeword[$wi]); 
           if ($str2!="" and strlen($str2) >= $minfindme)
              {
               if (stristr($str1,$str2)) // ����� ��� ����� �������� ��� ������ ������ ��������
                  {
                   $stroka++;
                   $msg=str_replace($findmeword[$wi]," <b><u>$findmeword[$wi]</u></b> ",$msg);
                  }
              }
          }
        }

else  //  if ($withregistr!="1")
   {
    if ($ftype=="2")
       {
        if (strstr($msdat,$findme))           // ����� �� "���� ����� �������" C ����� ��������
           {
            $stroka++;
            $msg=str_replace($findme," <b><u>$findme</u></b> ",$msg);
           }
       }
     else {
           if ($msdat!="" and strlen($findmeword[$wi]) >= $minfindme)
              {
               if (strstr($msdat,$findmeword[$wi]))     // ����� � ������ �������� ��� ������ ������ ��������
                  {
                   $stroka++;
                   $msg=str_replace($findmeword[$wi]," <b><u>$findmeword[$wi]</u></b> ",$msg);
                  }
              }
          }

   }   //  if ($withregistr!="1")



} while($wi > "0");  // ����� ����� �� ������� ����� �������


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
<small><BR>�� ������� '<U><B>$findme</B></U>' � ���$add �������: <HR size=+2 width=950 color=navy>
<BR><form action='index.php?event=go&findme' method=POST>
<table class=forumline align=center width=950>
<tr><th class=thHead colspan=4 height=25>��������� �����</th></tr>
<tr class=row2>
<td class=row1>������: <input type='text' value='$findme' style='width: 250px' class=post name=findme size=30>
<INPUT type=hidden value='1' name=ftype>
<input type=hidden name=gdefinder value='1'>
<input type=submit class=post value='  �����  '></td></table></form><br>
<table width=100% class=forumline><TR align=center class=small><TH class=thCornerL><B>�</B></TH><TH class=thCornerL width=35%><B>���������</B></TH><TH class=thCornerL width=70%><B>����� ���������</B></TH><TH class=thCornerL><B>����������<BR> � ����</B></TH></TR>"; $m="1"; }

if ($iii>$qq) {$in=$iii+2; $page=ceil($in/$qq);} else $page="1";  // ����������� ������ �������� � ����� ���������

if ($oldid!=$id and $number<100) { $number++; $msgnumber=$iii;

if ($nump>1) $anp="$nump"; else $anp="1";
if ($number>1) print"<TD class=row1 align=center>$anp</TD></TR><TR height=25>";

print "<TD class=row1 align=center><B>$number</B></TD>
<TD class=row1><A class=listlink href='index.php?id=$id&page=$page#m$iii'>$dt[3]</A></TD>
<TD class=row1>$msgtowrite</TD>";
$printflag="0"; $nump="0";

} else $nump++;

if ($number>=100) { print"</TR></TABLE> * ����� ���������������, ��� ���������� ����� 100 ���������!"; $gi=0; $iii=0; $ii=0; $itogofid=0;}

$oldid=$id;
} // if $printflag==1

} while($gi > "0");  // ����� ����� �� ����� ������

} while($iii > "0");
} // ���� ���� � ����������� ��������

} // if is_file("$datadir/$id.dat")
} while($ii > "0");

} // if isset($topicsid)

} // if ���� topic$fid.dat �� ����


$itogofid--;
} while($itogofid > "0");
if (!isset($m)) echo'<table width=80% align=center><TR><TD>�� ������ ������� ������ �� �������.</TD></TR></table>';

$time=explode(' ',microtime());
$seconds=($time[1]+$time[0]-$start_time);
echo "</TR></table><HR size=+2 width=99% color=navy><BR><p align=center><small>".str_replace("%1", sprintf("%01.3f", $seconds), "����� ������: <b>%1</b> ������.")."</small></p>";
exit;
}




















// ���� ���������� ����� �������� ����� ��� �����������
if (is_file("$datadir/wrfoto.dat")) {$mainlines=file("$datadir/wrfoto.dat"); $imax=count($mainlines); $i=$imax;}
if (!isset($mainlines)) $datasize=0; else $datasize=sizeof($mainlines);
if ($datasize<=0) {if (is_file("$datadir/copy.dat")) {$mainlines=file("$datadir/copy.dat"); $datasize=sizeof($mainlines);}}
if ($datasize<=0) exit("<center><b>���� ������ ������������! �������������! ������� � <a href='admin.php'>�������</a> � �������� �������!</b>");

$error=FALSE; $raz=""; $frname=null; $frtname=""; $rfid="";

// ��� ������ ���� razdel=
if (isset($_GET['razdel'])) {
do {$i--; $dt=explode("|", $mainlines[$i]);
if ($dt[0]==$_GET['razdel']) {$rfid=$i; $frname="$dt[2] �";}
} while($i >0);
$i=$imax;}









$maxtem=999;

if (isset($_GET['id'])) { // ���� ������� � ��������� ������: ���� � ������ � �����
$id=$_GET['id'];
if (strlen($id)<=3 and !is_file("$datadir/topic$id.dat")) $error="�� ���� ������";
if (strlen($id)!=11 and strlen($id)> 3 and !is_file("$datadir/$id.dat")) $error="�� ���� �������";
if (!ctype_digit($id)) $error="�� ���� ������� ��� ������";
if (isset($_GET['quotemsg'])) $error=TRUE; $fulid=null;

if(strlen($id)>3) {$fulid=$id; $fid=substr($id,0,3); $id=substr($id,3,4);} else $fid=$id;
$imax=count($mainlines); $i=$imax;

// �������� �� ���� �������� � ������� - ���� �������������
do {$i--; $dt=explode("|", $mainlines[$i]);
if ($dt[0]==$fid) { $frname="$dt[1] �";
if (isset($dt[11])) { if($dt[11]>0) $maxtem=$dt[11]; else $maxtem="999";}}
} while($i >0);

//$frtname="1"; $frname="2"; $fname="3";
// ���� ��������� �������� ���� ��� ����������� � ����� ������
if (strlen($id)>3 and is_file("$datadir/topic$fid.dat")) {
$lines=file("$datadir/topic$fid.dat"); $imax=count($lines); $i=$imax;
if ($i>1) {
do {$i--; $dt=explode("|",$lines[$i]);
if($dt[7]=="$fid$id") $frtname="$dt[3] �";
} while ($i>0); }}


// ���� ������������ ���� ������� ������
if (strlen($fulid)==11) { $frname=""; $fname="";
if (is_file("$datadir/$fid$id.dat")) { $lines=file("$datadir/$fid$id.dat"); $imax=count($lines); $i=$imax;
do {$i--; $dt=explode("|",$lines[$i]);
$dt[13]=str_replace(".jpg","",$dt[13]); $dt[13]=str_replace(".jpeg","",$dt[13]); $dt[13]=str_replace(".png","",$dt[13]); $dt[13]=str_replace(".gif","",$dt[13]);
if($dt[13]==$fulid) {$frtname=""; $fname="$dt[4] � $dt[3]";}
//if($dt[13]==$fulid) {$frtname="$dt[4] � $frtname"; $fname="$frtname";}
} while ($i>0); } }


if ($error==TRUE) {  // ��������� ���������� ������� � ������������ / ���˨���� ������� / ����!
$topurl="$fskin/top.html";
ob_start(); include $topurl; $topurl=ob_get_contents(); ob_end_clean();
$topurl=str_replace("<meta name=\"Robots\" content=\"index,follow\">",'<meta name="Robots" content="noindex,follow">',$topurl);
print"$topurl";
if (strlen($error)>1) exit("</td></tr></table><div align=center><br>��������, �� �����������$error �����������.<br>
���������� ������� �� ������� �������� ����������� �� <a href='$furl'>���� ������</a>,<br>
� ����� ������������ ��� ����.<br></div>
</td></tr></table></td></tr></table></td></tr></table>"); }
} // if (isset($_GET['id']))

if (strlen($error==FALSE)) 





include("$fskin/top.html"); //addtop();  // ���������� ����� ������




// ������� ������� �������� �����������


// ������� ��� ������� �� �������
$adminmsg=""; if (is_file("$datadir/wrfoto.dat")) $lines=file("$datadir/wrfoto.dat");
if (!isset($lines)) $datasize=0; else $datasize=sizeof($lines);
if ($datasize<=0) {if (is_file("$datadir/copy.dat")) {$lines=file("$datadir/copy.dat"); $datasize=sizeof($lines);} $adminmsg="<font color=red><B>�������������, ��������!!!</B> ���� �� � ��������� ��������. ������������ ��� �� ��������� ����� � �������!</font><br>";}
if ($datasize<=0) exit("�������� � ����� ������ - ���������� � ��������������");
$i=count($lines); $n="0"; $a1=-1; $u=$i-1; $fid="0"; $itogotem="0"; $itogomsg="0"; $alt=""; $konec="";

print"<TABLE border=0 cellSpacing=0 cellPadding=0 align=center width='100%'><TR><TD vAlign=top>
<table border=0 cellSpacing=0 cellPadding=0 width=100%><TR><TD width=270 valign=top>

<table width=98% height=450 border=0 cellSpacing=0 cellPadding=2><tr><td class=catHead colspan=2 height=18><span class=cattitle><h2 style='padding: 5px; margin: 1px'>���������</h2></span></td></tr>";

$itogofoto=""; $a1=$a1+$rfid;
do {$a1++; $dt=explode("|", $lines[$a1]);
if (isset($dt[1])) { // ���� ������� ���������� � ������� (������ ������) - �� ������ � �� �������

if ($dt[1]=="razdel" and isset($_GET['razdel'])) {
$frname=str_replace('�','',$frname);
if($a1==$rfid) print"<tr><td class=row1 valign=top><span class=genmed><li><B>$frname</B></li></span></TD></TR>";
$konec++;}  else {

// ���������� ���: ����� ��� ���������
if ($dt[1]=="razdel")
print "<tr><td class=row1 valign=top><span class=genmed><li><a href='index.php?razdel=$dt[0]'><B>$dt[2]</B></a></li></span></TD></TR>";
 else {
//$dt[9] - ���� ���������� ���������; $wrftime2 - ��������� ���������
// ���� $dt[9] ������ (�.�. ������) $wrftime2 ������ ������ ������ - �����
//$foldicon="folder.gif"; if (isset($wrfname)) {if (isset($dt[9])) {if ($dt[9]>$wrftime2) $foldicon="foldernew.gif";}}

if (is_file("$datadir/$dt[3].dat")) { $msgsize=sizeof(file("$datadir/$dt[3].dat")); // ������� ���-�� ������� � �����
if ($msgsize>$qq) $page=ceil($msgsize/$qq); else $page=1;} else $page=1;
if ($page!=1) $pageadd="&page=$page"; else $pageadd="";

if ($dt[7]==$date) $dt[7]="�������";
$maxzvezd=null; if (isset($dt[12])) { if ($dt[12]>=1) {$maxzvezd="*�������� ����������, ������� <font color=red><B>$dt[12]</B> �����";
$dt[4]=""; $dt[5]="";
if ($dt[12]==1) $maxzvezd.="�";
if ($dt[12]==2 or $dt[12]==3 or $dt[12]==4) $maxzvezd.="�";
$maxzvezd.=" �������</font>";}}
$fid="$dt[0]"; 

$alt="";

$dt[8]=substr($dt[8],0,-3);
$dt[10]=replacer($dt[10]);
$url="<a href=\"index.php?id=$fid\">$dt[1]</a>"; 
if (isset($_GET['id'])) {if ($dt[0]==$_GET['id']) $url="$dt[1]";}
$itogofoto=$itogofoto+$dt[5];
if ($dt[5]>0) $skolko="[$dt[5]]"; else $skolko="";
print "<tr><td class=row1 valign=top><span class=genmed><li style='PADDING-LEFT: 3px;'><B>$url $skolko</B></li></span></TD></TR>";

//print"<DIV style='PADDING-LEFT: 15px; PADDING-TOP: 4px'><li><a href=\"index.php?id=$fid\">$dt[1]</a></li></div> ";

$itogotem=$itogotem+$dt[4];$itogomsg=$itogomsg+$dt[5]; }}  if ($konec==2) $a1=$u;
} // if isset($dt[1]
} while($a1 < $u);

print"<TR><TD align=center class=row1><span class=genmed><B>����� ����: [ $itogofoto ]</B><br><br>";

if (is_file("baner_200x200.php")) include("baner_200x200.php");

/* ����� ����� ==== ������ �����
print"<TABLE border=0 cellSpacing=0 cellPadding=0 align=center width='100%'><TR><TD vAlign=top>
<table border=0 cellSpacing=0 cellPadding=0 width=100%><TR><TD width=270 valign=top>
<table width=98% height=450 border=0 cellSpacing=0 cellPadding=2><tr><td class=catHead colspan=2 height=18><span class=cattitle><h2 style='padding: 5px; margin: 1px'>���������</h2></span></td></tr>";
*/

//if ($_GET['event']=="login") { // ���� �� ����� ����������

if (!isset($_GET['id'])) { // ���� ��������� ������ �� �������!

if (isset($_COOKIE['wrfcookies'])) addtop();

else echo '<table width=98% border=0 cellSpacing=0 cellPadding=2><tr><td class=catHead colspan=2 height=18><span class=cattitle>
<nobr><h2 style="padding: 5px; margin: 1px">����� �����</h2></nobr></span></td></tr></table><br>
<FORM action="tools.php?event=regenter" method=post>
<TABLE cellPadding=1 cellSpacing=1 border=0>
<TR><TD>�����:</TD><TD><INPUT name=name></TD></TR>
<TR><TD>������:</TD><TD><INPUT type=password name=pass></TD></TR>
<TR><TD colspan=2><center><INPUT type=submit value="����� �"></TD></TR></TABLE></FORM>
<a href="tools.php?event=login">������ ������? </a> &nbsp;&nbsp;&nbsp; <a href="tools.php?event=reg">�����������</a><BR><br>';




// ���� � ����� ������� ���������
$fotorandom=TRUE; // ������� � ��������� - ����������� ������� � ������ ����������� �� ������� ����?

if ($fotorandom==TRUE) {
$timetek=time(); $timefile=0;
if (is_file("$datadir/ratingtop.dat")) $timefile=filemtime("$datadir/ratingtop.dat"); // ��������� ���� �������� ����� best.dat
$timer=$timetek-$timefile; // ������ ������� ������ ������� (� ��������) 

if ($timer>=43200) { // ��������� �������� ����� � ��������� ����
if (is_file("$datadir/rating.dat")) { $ffile="$datadir/rating.dat"; $flines=file("$ffile"); $fi=count($flines);} // ��������� ������� ����

if ($fi>10) { // ���� ���� ������ 10 �������
$ni=0; $c=0;
do {$fi--; $fdt=explode("|",$flines[$fi]);
$fdt[1]=$fdt[1]+1-1;
if ($fdt[1]>=1) {$newflines[$ni]="$fdt[2]"; $ni++;} // �������� ������ ���� � �������������� ��������
} while($fi!=0);

if ($ni>2) {
$data="<table width=98% border=0 cellSpacing=0 cellPadding=2><tr><td class=catHead colspan=2 height=18><span class=cattitle>
<nobr><h2 style='padding: 5px; margin: 1px'>���� � ���������</h2></nobr></span></td></tr></table><br>";
$uniq_foto=array_count_values($newflines); arsort($uniq_foto); reset($uniq_foto); $ni=0;
while ($ifoto = current($uniq_foto) and $c<=3) {if ($ifoto >=1) {$c++; $data.='<a class="gallery" rel="group" title="" href="'.$filedir.'/'.key($uniq_foto).'.jpg"><img src="'.$filedir.'/sm-'.key($uniq_foto).'.jpg" border=0></a><br><br>';} next($uniq_foto); }
reset($uniq_foto);
} // if ($ni>0)

} // if ($fi>10)

// ������ ������ � ����
$fp=fopen("$datadir/ratingtop.dat","w+");
flock ($fp,LOCK_EX);
fputs($fp, $data);
flock ($fp,LOCK_UN);
fclose($fp);

} //if ($timer>=43200) 

$ffile=file_get_contents("$datadir/ratingtop.dat"); print"$ffile";
} // if $fotorandom==TRUE;

} //$id - �������� ������ �� �������





if (is_file("$datadir/msg.dat")) { // ���� ������� �������, ��������, ���������� �� ����� msg.dat
echo '<table width=98% border=0 cellSpacing=0 cellPadding=2><tr><td class=catHead colspan=2 height=18><span class=cattitle>
<nobr><h2 style="padding: 5px; margin: 1px">������ �����</h2></nobr></span></td></tr></table><br>
<TABLE cellPadding=1 cellSpacing=1 border=0><TR><TD>';
include("$datadir/msg.dat"); 
echo'</TD></TR></TABLE></FORM><BR><br>'; }




echo'</span></TD></TR>
</table></TD><TD valign=top>';






if (!isset($_GET['id'])) { // ���� ��������� ������ �� �������!


if (is_file("../baner_728x90.php")) { // ���� ������� �������

print"<table border=0 height=100 cellSpacing=0 cellPadding=3 width=100%>
<tr><td class=catHead colspan=3 width=100% height=28><span class=cattitle>
<h2 style='padding: 5px; margin: 1px'>���������� ����������</h2></span></td></tr>
<tr align=center><td class=row1 valign=top><span class=genmed>";
include("../baner_728x90.php"); echo'</span></TD></TR></table>';
} // ���� ������� �������




if (is_file("$datadir/news.dat")) { // ���� ������� ����������� ����

$stolb=0; $maxfoto=9; // ������� ������� ����������� ���� ����������?
$nlines=file("$datadir/news.dat"); $nmax=count($nlines); if ($nmax<$maxfoto) $maxfoto=$nmax;

if ($nmax>0) { // �������� ���� ���� ��� ���� ���� ����
print"<table border=0 width=100% height=340 cellSpacing=0 cellPadding=3 width=100%>
<tr><td class=catHead colspan=3 width=100% height=28><span class=cattitle>
<h2 style='padding: 5px; margin: 1px'>������� ����������� ����</h2></span></td></tr>";

do { $maxfoto--;
$ndt=explode("|",$nlines[$maxfoto]);
$file=replacer($ndt[12]); $big_file=replacer($ndt[11]);
//$foto=str_replace('sm-','',$file); $foto=str_replace('.jpg','',$foto); $foto=str_replace('.png','',$foto);
if ($stolb==0) echo'<tr align=center>';
print "<td class=row1 width=33% valign=top align=center><span class=genmed>���������: <B>$ndt[9]</B><br>
<a class='gallery' rel='group' title='$ndt[6]' href='$filedir/$big_file'><img border=0 src='$filedir/$file'></a><br>
������: <a href='index.php?id=$ndt[1]'><B>$ndt[5]</B> �</a> <nobr>[$ndt[8] ����]</nobr></span></TD>";
$stolb++;
if ($stolb>=3) {echo'</TR>'; $stolb=0; }
} while($maxfoto>0);
echo'</table>';
} // if is_file

} // if ($nmax>0)


// ���� �������� ��������� ����

$maxfoto=9; // ������� ��������� ����������� ���� ����������?
$allfoto=null; $p=0; $stolb=0;

if ($handle=opendir($filedir)) { while (($file=readdir($handle))!==false)
if (!is_dir($file) and strstr($file,'sm-')) {$allfoto[$p]=$file; $p++;} closedir($handle);}

if (count($allfoto)>9) {
$r_keys=array_rand($allfoto,$maxfoto);
//echo $allfoto[$r_keys[0]] . "\n"; echo $allfoto[$r_keys[1]] . "\n"; exit;

print"<table border=0 height=340 cellSpacing=0 cellPadding=3 width=100%>
<tr><td class=catHead colspan=3 width=100% height=28><span class=cattitle>
<h2 style='padding: 5px; margin: 1px'>�������� ��������� ����</h2></span></td></tr>";

do {$maxfoto--;
$file=$allfoto[$r_keys[$maxfoto]];
$rubrika=substr($file,3,7);
$foto=str_replace('sm-','',$file);
if ($stolb==0) echo'<tr align=center>';
print "<td class=row1 valign=top><span class=genmed>
<a class='gallery' rel='group' title='$fname' href='$filedir/$foto'><img border=0 src='$filedir/$file'></a><br>
<a href='index.php?id=$rubrika'>������� � ������ �</a></span></TD>";
$stolb++;
if ($stolb>=3) {echo'</TR>'; $stolb=0; }

} while($maxfoto>0);
echo'</table>';
} // if count($allfoto)>9


} // ����� - ������ �����, ��������� ����� � ������ ����� �������
















if (isset($_GET['id'])) { $id=$_GET['id'];


// �������� � ������ ��������� �������
if (strlen($id)==3) { $fid=$id;

// ������
if (!ctype_digit($fid) or strlen($fid)>3) exit("<B>$back. ����� ������� ������ ���� �������� � ��������� ����� 4 ��������</B>");
$imax=count($mainlines); if (($fid>999) or (strlen($fid)==0)) exit("<b>������ ������ ����� ��� �� ����������.</b>");

// ��������� ������ ������ �������������� ��������
if (!isset($_GET['page'])) $page=1; else {$page=$_GET['page']; if (!ctype_digit($page)) $page=1; if ($page<1) $page=1;}

if ($raz!="razdel") {

// �������� ������ �� ���-�� �¨�� �����. ���� ������ ���������� N � ���� ������� - �� ����������!
$maxzd=null;
do {$imax--; $ddt=explode("|", $mainlines[$imax]); if ($ddt[0]==$fid) $maxzd=$ddt[12]; } while($imax>"0");
if ($maxzd>=1) {
if (!ctype_digit($maxzd)) exit("$back ����� ����������� � ������. � � ����� ������ - ������!");

$noacsess="<br><br><br><br><center><table class=forumline width=700><tr><th class=thHead colspan=4 height=25>������ � ������ ���������</th></tr>
<tr class=row2><td class=row1><center><BR><BR><B><span style='FONT-SIZE: 14px'>��� ��������� ������� ������� ���������� ���� ������������������ � ����� ������� �� ����� $maxzd ����.";

// ���� ��������� ����� � ������ �����, ��������� ���-�� ��� ���� � ���������� � �������� � �������
if (isset($_COOKIE['wrfcookies'])) { // ���� ���� �������� ��� ������� ����
$text=$_COOKIE['wrfcookies']; 
$text=replacer($text);
$wrfc=explode("|",$text); $wrfname=$wrfc[0]; if (isset($wrfc[1])) $wrfpass=$wrfc[1]; else exit("$back ������� ������ - � ���� ��� ������. ���� �� ����� ;-) ?");

// ��������� ���� � �������
$iu=$usercount; $ok=FALSE;
do {$iu--; $du=explode("|",$userlines[$iu]);
if (isset($du[1])) { $realname=strtolower($du[0]);
if (strtolower($wrfname)===$realname & $wrfpass===$du[1]) {$ok="$i"; if ($du[2]<$maxzd) exit("$noacsess � ��� ����� $du[2] ����.</B></center><BR><BR>$back<BR><BR></td></table><br>"); }}
} while($iu > "0");
} else exit("$noacsess</B></center><BR><BR>$back<BR><BR></td></table><br>"); // ���� ���� ���, �� ���� ���� ����� �� ��� �������, ����� - ��������
if ($ok!=FALSE) exit("$noacsess</B></center><BR><BR>$back<BR><BR></td></table><br>");
}


// ���������� ���� �� ���������� � ����� � �������
if (is_file("$datadir/topic$fid.dat")) {
$msglines=file("$datadir/topic$fid.dat"); $maxi=count($msglines); $i=$maxi;

if (isset($_POST['findme']) or isset($_GET['findme'])) {
// ���� ���� ������ �� �������� ����, ��:
// - ��������� ���� � ������ � �������� � ��������� ������ ������ ��, ������� �������� � �������� ������� �����
// - � $maxi ���������� ���-�� ���
// - � $msglines[$i] ���������� ������
setlocale(LC_ALL,'ru_RU.CP1251'); // ! ��������� ������ �������, ���������� � ���������� � � �������� �������
if (isset($_POST['findme'])) $findme=replacer($_POST['findme']);
if (isset($_GET['findme'])) { $findme=replacer($_GET['findme']); $findme=urldecode($findme);}
$stroka=strlen($findme); if($stroka<4 or $stroka>30) exit("����������� ����� � ���������� �� 4-� �� 30-� ��������!");
$tmplines=$msglines; $msglines=null; $i=0;
foreach($tmplines as $v) {$dt=explode("|", $v); if (stristr($dt[3],$findme)) {$msglines[$i]=$v; $i++;}}
$maxi=$i-1;} else $findme="";

$frname=str_replace(' �','',$frname); //�������� ������ �������
print"
<table width=100% border=0 cellSpacing=0 cellPadding=3 height=45><tr><td class=catHead colspan=2><span class=cattitle><h2 style='padding: 5px; margin: 1px'>$frname �</h2></span></td></tr></table>";

print"<table border=0 width=100% cellpadding=2 cellspacing=2 class=forumline>
<tr><td colspan=2 align=center valign=top>"; $temp=0;

if (is_file("../baner_728x90.php")) include("../baner_728x90.php"); // ��������� ������

$addbutton="<table width=100%><tr><td align=left valign=middle>";

if ($maxi>0) {

if ($maxi>$maxtem-1) $addbutton="<table width=100%><TR><TD>���������� ���������� ��� � ������� ���������.";


// ���� ����������: ��������� ������ ������ (�� ������� �������� ����� � �����)!
do {$i--; $dt=explode("|", $msglines[$i]);
   $filename="$dt[7].dat"; if (is_file("$datadir/$filename")) $ftime=filemtime("$datadir/$filename");  else $ftime="";
   $newlines[$i]="$ftime|$dt[7]|$i|";
} while($i > 0);
usort($newlines,"prcmp");
// $newlines  - ������ � �������:  ���� | ���_�����_�_����� | � �/� |
// $msglines - ������ �� ����� ������ ��������� �������
$i=$maxi;
do {$i--; $dtn=explode("|", $newlines[$i]);
   $numtp="$dtn[2]"; $lines[$i]="$msglines[$numtp]";
} while($i > 0);
// ����� ����� ����������

// ���������� QQ ���
$fm=$maxi-$qq*($page-1);
if ($fm<"0") $fm=$qq; $lm=$fm-$qq; if ($lm<"0") $lm="0";

do {$fm--; $num=$fm+2;
$dt=explode("|", $lines[$fm]);

// ����� ��� ����������� ���� �� VIP-������
$dtn=explode("|", $newlines[$fm]);
$timer=time()-$dtn[0]; // ������ ������� ������ ������� (� ��������) 


$filename=$dt[7]; 

if (is_file("$datadir/$filename.dat")) { // ���� ���� � ����� ���������� - �� �������� ���� � ������!
$msgsize=sizeof(file("$datadir/$filename.dat"));

if ($temp>0) print"</TD><TD>"; if ($temp==0) print"<TR><TD>"; // ��������� ������� �� ��������� � 2-� �� 400 �������

print"
<table border=0 width=390 cellpadding=2 cellspacing=1 class=forumline>
<TR valign=middle height=50><TD width=160 align=center valign=midle bgcolor=white>
<a href=\"index.php?id=$dt[7]\" title='$dt[3]'><img border=0 src='$filedir/$dt[17]'></a></TD>
<td class=row1 align=left height=130><span class=forumlink>";

$dt[3]=replacer($dt[3]);

print"<b><a href=\"index.php?id=$dt[7]\" title='$dt[3]'>$dt[3]</a></b> [$msgsize ����]</span>";

if ($msgsize>$qq) { // ������� ������ ��������� ������� ����
$maxpaget=ceil($msgsize/$qq); $addpage="";
echo'<small>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<div style="padding:6px;" class=pgbutt>��������: ';
if ($maxpaget<=5) $f1=$maxpaget; else $f1=5;
for($i=1; $i<=$f1; $i++) {if ($i!=1) $addpage="&page=$i"; print"<a href=\"index.php?id=$dt[7]$addpage\">$i</a> &nbsp;";}
if ($maxpaget>5) print "... <a href=\"index.php?id=$dt[7]&page=$maxpaget\">$maxpaget</a>"; }

print"<br>$dt[4]<br><br><span class=gensmall>�������(�) ";

$codename=urlencode($dt[0]);
if ($dt[1]=="��") {
if (!isset($wrfname)) print "$dt[0]"; else print "<small>($users)</small> <a href='tools.php?event=profile&pname=$codename':$dt[2]>$dt[0]</a>";} else  print"<small> $dt[0]</small>";

print"<br><br><a name='addf' href=\"index.php?add=newfoto&id=$dt[7]\"><img src='$fskin/add_foto.gif' border=0></a>&nbsp;
</TD></TR></TABLE>";

$temp++; if ($temp==2) {$temp=0; print"</td></tr>";} // ����� ����� ������ �� ��� ��������

// ������ if (strlen...) ������ ���� ���� ���� � ����� ������ ������ - �������
if ($msgsize>=2) {$linesdat=file("$datadir/$filename.dat"); $dtdat=explode("|", $linesdat[$msgsize-1]);
if (strlen($linesdat[$msgsize-1])>10) {$dt[0]=$dtdat[0]; $dt[1]=$dtdat[1]; $dt[2]=$dtdat[2]; $dt[5]=$dtdat[5]; $dt[6]=$dtdat[6];}}
} //if is_file

} while($lm < $fm);

if ($stop!=TRUE) $addbutton.="<br><span class=nav><a name='add' href=\"index.php?add=newrazdel&id=$fid\"><img src='$fskin/add_razdel.gif' border=0></a>&nbsp;";
else $addbutton.="�������� �� ����������, �� ������������� �������� ������������ ���������� �������� � ����������!";

// ��������� ���������� $pageinfo - �� ������� �������
if (strlen($findme)>1) $findadd="&findme=$findme"; else $findadd="";
$pageinfo=""; $addpage=""; $maxpage=ceil(($maxi+1)/$qq); if ($page>$maxpage) $page=$maxpage;
$pageinfo.="<small><div style='padding:6px;' class=pgbutt>��������: &nbsp;";
if ($page>3 and $maxpage>5) $pageinfo.="<a href=index.php?id=$fid$findadd>1</a> ... ";
$f1=$page+2; $f2=abs($page-2); if ($f2=="0") $f2=1; if ($page>=$maxpage-1) $f1=$maxpage;
if ($maxpage<=5) {$f1=$maxpage; $f2=1;}
for($i=$f2; $i<=$f1; $i++) { if ($page==$i) $pageinfo.="<B>$i</B> &nbsp;"; 
else {if ($i!=1) $addpage="&page=$i"; $pageinfo.="<a href=index.php?id=$fid$addpage$findadd>$i</a> &nbsp;";} }
if ($page<=$maxpage-3 and $maxpage>5) $pageinfo.="... <a href=index.php?id=$fid&page=$maxpage$findadd>$maxpage</a>";
$pageinfo.='</div></small>';

print"</td></tr></table>
$addbutton<TD><table width=100%><tr><td align=right colspan=3>
$pageinfo</b></span></td></tr></table>";

} else print"$addbutton<br><span class=nav><a name='add' href=\"index.php?add=newrazdel&id=$fid\"><img src='$fskin/add_razdel.gif' border=0></a></td></tr></table>";

} else print"$addbutton";

echo'</tr></table><BR>';


if (isset($_GET['add'])) { 
if ($cangutema=="0" and !isset($wrfname)) print"<center><h5>������������� �������� ��������� ������ ����! ��� ����������� �������� �� ������: <B><a href='tools.php?event=reg'>������������������</a></B></h5></center><BR><BR>"; else {
$maxzag=$maxzag-10; // ��� �����!!!



if ($_GET['add']=="newrazdel") {
print"<form action=\"index.php?event=addtopic&id=$fid\" method=post enctype=\"multipart/form-data\" name=REPLIER>";
//else print"<form action=\"index.php?event=addanswer&id=$fid\" method=post enctype=\"multipart/form-data\" name=REPLIER>";


print"<table border=0 width=100% class=forumline>
<tr><td class=catHead align=center colspan=2 height=28><span class=cattitle>���������� �������</span></td></tr>
<tr><td class=row1 valign=top><span class=genmed><B>�������� �������</B></span></TD><TD class=row2>
<input type=hidden name=maxzd value='$maxzd'><input type=text class=post name=zag maxlength=$maxzag size=70></TD></TR>";
addmsg("");
} } }



}
} //if ($raz!="razdel")








// ���������� ���� ��������� �������
if (strlen($id)>6) { $fid=substr($id,0,3);

// ���������� ���� �� ���������� � ����� � �������
if (!is_file("$datadir/$id.dat")) exit("<BR>$back. ��������, �� ����� ���� �� ������ �� ����������.<BR> ������ ����� � ������ �������������.");
$lines=file("$datadir/$id.dat"); $mitogo=count($lines); $i=$mitogo; $maxi=$i-1;

if ($mitogo>0) { $tblstyle="row1";  $printvote=null;

// ���� ���� � topic��.dat - ��������� �� ������� �� ����? � ����� �� ���� ���� �� � ������
$ok=FALSE; $closed=FALSE; if (is_file("$datadir/topic$fid.dat")) {
$msglines=file("$datadir/topic$fid.dat"); $mg=count($msglines);
do {$mg--; $mt=explode("|",$msglines[$mg]);
if ($mt[7]==$id and $mt[8]=="closed") $closed=TRUE;
if ($mt[7]==$id) $ok=1; // ���� ���� � ��������� �������?
} while($mg > "0");}

$realbase="1"; if (is_file("$datadir/wrfoto.dat")) $mainlines=file("$datadir/wrfoto.dat");
if (!isset($mainlines)) $datasize=0; else $datasize=sizeof($mainlines);
if ($datasize<=0) {if (is_file("$datadir/copy.dat")) {$realbase="0"; $mainlines=file("$datadir/copy.dat"); $datasize=sizeof($mainlines);}}
if ($datasize<=0) exit("$back. �������� � ����� ������ - ���������� � ��������������");
$i=count($mainlines);





$maxzd=null;


// ��������� ������ ������ �������������� ��������
if (!isset($_GET['page'])) $page=1; else {$page=$_GET['page']; if (!ctype_digit($page)) $page=1; if ($page<1) $page=1;}

$fm=$qq*($page-1); if ($fm>$maxi) $fm=$maxi-$qq;
$lm=$fm+$qq; if ($lm>$maxi) $lm=$maxi+1;


// ��������� ���������� $pageinfo - �� ������� �������
$pageinfo=""; $addpage=""; $maxpage=ceil(($maxi+1)/$qq); if ($page>$maxpage) $page=$maxpage;
$pageinfo.="<div align=center style='padding:6px;' class=pgbutt>��������: &nbsp;";
if ($page>3 and $maxpage>5) $pageinfo.="<a href=index.php?id=$id>1</a> ... ";
$f1=$page+2; $f2=abs($page-2); if ($f2=="0") $f2=1; if ($page>=$maxpage-1) $f1=$maxpage;
if ($maxpage<=5) {$f1=$maxpage; $f2=1;}
for($i=$f2; $i<=$f1; $i++) { if ($page==$i) $pageinfo.="<B>$i</B> &nbsp;"; 
else {if ($i!=1) $addpage="&page=$i"; $pageinfo.="<a href=index.php?id=$id$addpage>$i</a> &nbsp;";} }
if ($page<=$maxpage-3 and $maxpage>5) $pageinfo.="... <a href=index.php?id=$id&page=$maxpage>$maxpage</a>";
$pageinfo.='</div>';

if (is_file("$datadir/rating.dat")) { // ��������� ���� � ��������� ���� � ������
$file="$datadir/rating.dat"; $flines=file("$file"); $j=count($flines); $maxflines=$j;}

$qm=null; $flag=0;
do {$dt=explode("|", replacer($lines[$fm]));

$youwr=null; $fm++; $num=$maxi-$fm+2; $status="";

if (strlen($lines[$fm-1])>5) { // ���� ������� ���������� � ������� (������ ������) - �� ������ � �� �������

if (isset($_GET['quotemsg'])) {$quotemsg=replacer($_GET['quotemsg']); if(ctype_digit($quotemsg) and $quotemsg==$fm) $qm="[Quote][b]$dt[0] �����:[/b]\r\n".$dt[4]."[/Quote]";}

$msg=str_replace("[b]","<b>",$dt[4]);
$msg=str_replace("[/b]","</b>", $msg);
$msg=str_replace("[RB]","<font color=red><B>", $msg);
$msg=str_replace("[/RB]","</B></font>", $msg);
$msg=str_replace("&lt;br&gt;","<br>",$msg);
$msg=preg_replace("#\[Quote\]\s*(.*?)\s*\[/Quote\]#is","<br><B><U>������:</U></B><table width=80% border=0 cellpadding=5 cellspacing=1 style='padding: 5px; margin: 1px'><tr><td class=quote>$1</td></tr></table>",$msg);
$msg=preg_replace("#\[Code\]\s*(.*?)\s*\[/Code\]#is"," <br><B><U>PHP ���:</U></B><table width=80% border=0 cellpadding=5 cellspacing=1 style='padding: 5px; margin: 1px'><tr><td class=code >$1</td></tr></table>",$msg);

if ($antimat==TRUE) { // �������
$pattern="/\w{0,5}[�x]([�x\s\!@#\$%\^&*+-\|\/]{0,6})[�y]([�y\s\!@#\$%\^&*+-\|\/]{0,6})[�i�e�����]\w{0,7}|\w{0,6}[�p]([�p\s\!@#\$%\^&*+-\|\/]{0,6})[i��]([i��\s\!@#\$%\^&*+-\|\/]{0,6})[3��]([3��\s\!@#\$%\^&*+-\|\/]{0,6})[�d]\w{0,10}|[�cs][�y]([�y\!@#\$%\^&*+-\|\/]{0,6})[4�k�]\w{1,3}|\w{0,4}[b�]([b�\s\!@#\$%\^&*+-\|\/]{0,6})[l�]([l�\s\!@#\$%\^&*+-\|\/]{0,6})[y�]\w{0,10}|\w{0,8}[�][b�][����@e���a][���@���]\w{0,8}|\w{0,4}[�e]([�e\s\!@#\$%\^&*+-\|\/]{0,6})[�b]([�b\s\!@#\$%\^&*+-\|\/]{0,6})[u�]([u�\s\!@#\$%\^&*+-\|\/]{0,6})[�4�]\w{0,4}|\w{0,4}[�e�]([�e�\s\!@#\$%\^&*+-\|\/]{0,6})[�b]([�b\s\!@#\$%\^&*+-\|\/]{0,6})[�n]([�n\s\!@#\$%\^&*+-\|\/]{0,6})[�y]\w{0,4}|\w{0,4}[�e]([�e\s\!@#\$%\^&*+-\|\/]{0,6})[�b]([�b\s\!@#\$%\^&*+-\|\/]{0,6})[�o�a@]([�o�a@\s\!@#\$%\^&*+-\|\/]{0,6})[�n�t]\w{0,4}|\w{0,10}[�]([�\!@#\$%\^&*+-\|\/]{0,6})[�]\w{0,6}|\w{0,4}[p�]([p�\s\!@#\$%\^&*+-\|\/]{0,6})[�e�i]([�e�i\s\!@#\$%\^&*+-\|\/]{0,6})[�d]([�d\s\!@#\$%\^&*+-\|\/]{0,6})[o��a@�e�i]([o��a@�e�i\s\!@#\$%\^&*+-\|\/]{0,6})[�r]\w{0,12}/i";
$msg=preg_replace("$pattern","<b><font color='red'>�������</font></b>",$msg); }

if ($smile==TRUE) { // ��������
$i=count($smiles)-1; for($k=0; $k<$i; $k=$k+2)
{$j=$k+1; $msg=str_replace("$smiles[$j]","<img src='smile/$smiles[$k].gif' border=0>",$msg);}}

// ���� ��������� ���������� �����
if ($liteurl==TRUE) $msg=preg_replace ("#([^\[img\]])(http|https|ftp|goper):\/\/([a-zA-Z0-9\.\?&=\;\-\/_]+)([\W\s<\[]+)#i", "\\1<a href=\"\\2://\\3\" target=\"_blank\">\\2://\\3</a>\\4", $msg);

// ��������� ������ ����� ������ ������ URL!!!
$msg=preg_replace('#\[img\](.+?)\[/img\]#','<img src="$1" border="0">',$msg);

// ��������� � ������ ������ �� ������������
if ($dt[1]=="��")  { $iu=$usercount; $predup="0";
do {$iu--; $du=explode("|", $userlines[$iu]); if ($du[0]==$dt[0])
{ if (isset($du[12])) {$status=$du[13]; $reiting=$du[2]; $youavatar=$du[12]; $email=$du[3]; $icq=$du[7]; $site=$du[8]; $userpn=$iu;}
if (isset($_COOKIE['wrfcookies'])) $youwr=preg_replace("#(\[url=([^\]]+)\](.*?)\[/url\])|(http://(www.)?[0-9a-z\.-]+\.[a-z]{2,6}[0-9a-z/\?=&\._-]*)#","<a href=\"$4\" target='_blank'>$4</a> ",$du[11]); else $youwr=$du[11];}
} while($iu > "0");
}

if ($tblstyle=="row1") $tblstyle="row2"; else $tblstyle="row1";

if ($flag==FALSE) { // ���� �������� ���� ���
$frname=str_replace(' �','',$frname); $frtname=str_replace(' �','',$frtname); //�������� ������ �������
$flag=TRUE; print "
<table width=100% border=0 cellSpacing=0 cellPadding=3 height=45><tr><td class=catHead colspan=2>
<span class=cattitle><h2 style='padding: 5px; margin: 1px'><a href=\"index.php?id=$fid\">$frname</a> �
$frtname</h2></span>
</td></tr></table>";

if (is_file("../baner_728x90.php")) include("../baner_728x90.php"); // ��������� ������

print"<table border=0 class=forumline width=800 cellspacing=1 cellpadding=3><tr><div id='wrap'>";}

$teknum=$fm;

print"<td valign=top> 
<table border=0 align=center width=190 cellpadding=1 cellspacing=0 class=maintbl>
<tr><td valign=top align=center class=row1>

<font size=-1>���� � $teknum</font><BR>
<table width=190 height=180 cellpadding=0 cellspacing=0><tr><td align=center height=120 colspan=2><br>";

// ���� �������˨� ���� � ��������� - �� ���������� ������ � ������ �� ���� ��� ��������
if (isset($dt[12])) { if ($dt[12]!="" and is_file("$filedir/$dt[13]")) {
$fsize=round($dt[14]/10.24)/100;
$fotoname=explode(".",$dt[13]);


// ���� ���� ���� � ��������� ����, �� ���� ��� ������ ���� ���� � �������� ������� ���
$j=$maxflines; $sbal=null; $itogo=null;
if ($j>0) do {$j--; $fdt=explode("|",$flines[$j]);
if ($fdt[2]==$fotoname[0]) {$itogo++; $sbal=$sbal+$fdt[1];}
} while($j>0);
if ($itogo!=null) $sbal=round($sbal/$itogo,2); else $rating="";
if ($sbal>0) $rating="<font color=green><B>$sbal</B></font>";
if ($sbal<0) $rating="<font color=red>$sbal</font>";





// ����� �������
$dt[20]=round(($dt[20]/1024),2); if ($dt[20]=="0") $dt[20]="0.01";
print"<a class=\"gallery\" rel=\"group\" title=\"$msg\" href=\"$filedir/$dt[13]\"><img src=\"$filedir/$dt[17]\" alt='$msg'/></a>
<br><B>$msg</B><br><br>
����������: $dt[18] x $dt[19]<br>
������: <I>$dt[20] ��.</I><br>

<br>�������: $rating <A href='#m$fm' style='text-decoration:none' onclick=\"window.open('index.php?addrepa&fotoname=$fotoname[0]','repa','width=600,height=600,left=50,top=50,scrollbars=yes')\">&#177;</A><br><br>
";
}}

$addpage=""; if ($page>1) $addpage="&page=$page"; // ����� ��� �����������
print"<br>";

/* ���� ��� ��������� ���� �����������!!!!!
if (is_file("$datadir/$id.dat"))  {
$rlines=file("$datadir/$id.dat"); $ri=count($rlines); $bals=0; $all=0;
print"<TR><TD colspan=2 align=center>����������� [<B> $ri </B>]</TD></TR>";
do {$ri--; $edt=explode("|",$rlines[$ri]); $edt[3]=date("d.m.Y H:i:s",$edt[3]); if ($edt[4]!=0) {$bals=$bals+$edt[4]; $all++;} else {$edt[4]="-";} } while($ri>0);
if ($bals==0) {$itogobals="+</B>";} else {$itogobals=round($bals*10/$all)/10; $itogobals.="</B>";}
print "<TR><TD colspan=2 align=center>������ [<B><a href='index.php?event=formacoment&id=$id' class=gallery>$itogobals</a>]</TD></TR>";
} else {print"<TR><TD colspan=2 align=center>����������� [ <B><a href='index.php?event=formacoment&id=$id' class=gallery>+</a></B> ]</TD></TR>
<TR><TD colspan=2 align=center>������ [<B><a href='index.php?event=formacoment&id=$id' class=gallery>+</a></B>]</TD></TR>
";}
*/


if ($dt[2]!="") $dt[2]="<a href='mailto:$dt[2]' class=gallery>$dt[1]</a>"; else $dt[2]="$dt[0]";
print"<TR height=30><TD><i>$dt[2]</i></TD><TD align=right>
<small><i>$dt[5]</i><br>$dt[6]</small></td></tr>
</table>
</td></tr></table></div>
</td>";

$colrubperpage=3;
$cm=1; // ����� ��� ������� �� �������
if ((round($fm/$colrubperpage))==($fm/$colrubperpage)) {$cm++; print "</TR><TR>";}


} // ���� ������� ����������
} while($fm < $lm);

print" </tr></table> $pageinfo";


if ($cangumsg==FALSE and !isset($wrfname)) {print"<center>������������� �������� �������� ������ �� ���������! ��� ����������� �������� �� ������: <B><a href='tools.php?event=reg'>������������������</a></B></center><BR><BR>"; } else {
if ($closed==FALSE) {

if (isset($_COOKIE['wrfcookies'])) {$wrfc=$_COOKIE['wrfcookies']; $wrfc=htmlspecialchars($wrfc); $wrfc=stripslashes($wrfc); $wrfc=explode("|", $wrfc);  $wrfpass=replacer($wrfc[1]);} else {unset($wrfpass); $wrfpass="";}

if ($stop==FALSE) {

print "
<!--<form action=\"index.php?event=addanswer&id=$id\" method=post name=REPLIER enctype=\"multipart/form-data\">-->
<input type=hidden name=userpass value=\"$wrfpass\">
<input type=hidden name=page value='$page'>
<input type=hidden name=zag value=\"$dt[3]\">
<input type=hidden name=maxzd value='$maxzd'>
<table cellpadding=3 cellspacing=1 width=100% class=forumline>
<tr><th class=thHead colspan=2 height=25><b>���������� ���� <a href='index.php?loginza'>*</a></b></th></tr>";

addmsg($qm);
} else echo'<center>�������� �� ����������, �� ������������� �������� ������������ ���������� �������� � ����������!';
} else echo'<center><font style="font-size: 16px;font-weight:bold;"><BR>������ ������ ��� ���������� ����!<BR><BR>';
}}

}
} // if isset($id)







if (is_file("$fskin/bottom.html")) include("$fskin/bottom.html");  // ���������� ������ ���� �����������

print"</td></tr></table>
<center><font size=-2><small>Powered by WR-Foto &copy; 1.1�.2015<br></small></font></center>";

if (is_file("../bottom.html")) include ("../bottom.html");

?>
