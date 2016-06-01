<? // WR-foto v 1.2  //  02.08.15 г.  //  Miha-ingener@yandex.ru

// ссылка razdel= не работет. ИСПРАВИТЬ!!!

error_reporting (E_ALL); // ВРЕМЕННО - на время тестирования и отладки скрипта!
// error_reporting(0); // РАЗКОМЕНТИРУЙТЕ для постоянной работы!!!
@ini_set('register_globals','off');// Все скрипты написаны для этой настройки php

$sendadmin=FALSE;
$flashupload=1; // ВКЛЮЧИТЬ FLASH-загрузчик? 1/0

include "data/config.php";

$smwidth="150"; // Ширина миниизображения
$smheight="120"; // Высота миниизображения
$valid_types=array("jpg","jpeg","gif","png");  // допустимые расширения загружаемых файлов

// Определяем URL галереи
$host=$_SERVER["HTTP_HOST"]; $self=$_SERVER["PHP_SELF"]; $furl=str_replace('index.php','',"http://$host$self");

// Временно, чтобы удалить из поисковиков лишние страницы (до 07.2016 г.)
if (isset($_GET['add'])) { if ($_GET['add']=="razdel") { header("HTTP/1.1 404 Moved Permanently"); header("Location: $furl"); } }

function replacer ($text) { // ФУНКЦИЯ очистки кода
$text=str_replace("&#032;",' ',$text);
$text=str_replace(">",'&gt;',$text);
$text=str_replace("<",'&lt;',$text);
$text=str_replace("\"",'&quot;',$text);
$text=preg_replace("/\n\n/",'<p>',$text);
$text=preg_replace("/\n/",'<br>',$text);
$text=preg_replace("/\\\$/",'&#036;',$text);
$text=preg_replace("/\r/",'',$text);
$text=preg_replace("/\\\/",'&#092;',$text);
// если magic_quotes включена - чистим везде СЛЭШи в этих случаях: одиночные (') и двойные кавычки ("), обратный слеш (\)
if (get_magic_quotes_gpc()) { $text=str_replace("&#092;&quot;",'&quot;',$text); $text=str_replace("&#092;'",'\'',$text); $text=str_replace("&#092;&#092;",'&#092;',$text); }
$text=str_replace("\r\n","<br> ",$text);
$text=str_replace("\n\n",'<p> ',$text);
$text=str_replace("\n",'<br> ',$text);
$text=str_replace("\t",'',$text);
$text=str_replace("\r",'',$text);
$text=str_replace('   ',' ',$text);
return $text; }


// Функция содержит ПРОДОЛЖЕНИЕ ШАПКИ. Вызывается: addtop();
function addtop() { global $wrfname,$fskin,$date,$time;
if (isset($_COOKIE['wrfcookies'])) {$wrfc=$_COOKIE['wrfcookies']; $wrfc=replacer($wrfc); $wrfc=explode("|", $wrfc);  $wrfname=$wrfc[0];} else {unset($wrfname); unset($wrfpass); $wrfpass="";}
if (isset($wrfname)) { // ищем КУКи и выводим ИМЯ
print"<table width=98% border=0 cellSpacing=0 cellPadding=2><tr><td class=catHead colspan=2 height=18><span class=cattitle>
<nobr><h2 style=\"padding: 5px; margin: 1px\">Учётная запись</h2></nobr></span></td></tr></table><br>
<a href='tools.php?event=profile&pname=$wrfname'>Ваш профиль</a>&nbsp; 
<a href='index.php?event=clearcooke' class=mainmenu>Выход [<B>$wrfname</B>]</a><br><br>";
} else { 
print "<span class=mainmenu>
<a href='tools.php?event=reg' class=mainmenu>Регистрация</a>&nbsp;&nbsp;
<a href='tools.php?event=login' class=mainmenu> Вход</a>";}
return true;}



function prcmp ($a, $b) {if ($a==$b) return 0; if ($a<$b) return -1; return 1;} // Функция сортировки


/* Функция img_resize($src,$dest,$width,$height,$rgb,$quality): генерация thumbnails
Обязательные параметры: имя исходного файла, имя генерируемого файла, ширина и высота генерируемого изображения, в пикселях
Необязательные параметры: цвет фона (по умолчанию - белый), качество генерируемого JPEG, по умолчанию - максимальное (100) */
function img_resize($src, $dest, $width, $height, $rgb=0xFFFFFF, $quality=95) {
  if (!file_exists($src)) return false;
  $size = getimagesize($src);
  if ($size === false) return false;
  // Определяем исходный формат по MIME-информации, предоставленной
  // функцией getimagesize, и выбираем соответствующую формату
  // imagecreatefrom-функцию.
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



function nospam() { global $max_key,$rand_key; // Функция АНТИСПАМ
if (array_key_exists("image", $_REQUEST)) { $num=replacer($_REQUEST["image"]);
for ($i=0; $i<10; $i++) {if (md5("$i+$rand_key")==$num) {imgwr($st,$i); die();}} }
$xkey=""; mt_srand(time()+(double)microtime()*1000000);
$dopkod=mktime(0,0,0,date("m"),date("d"),date("Y")); // доп.код: меняется каждые 24 часа
$stime=md5("$dopkod+$rand_key");// доп.код
echo'<TR><TD class=row2><span class=genmed><B>Защитный код</B></span>:</TD><TD class=row2>';
for ($i=0; $i<$max_key; $i++) {
$snum[$i]=mt_rand(0,9); $psnum=md5($snum[$i]+$rand_key+$dopkod);
echo "<img src=antispam.php?image=$psnum border='0' alt=''>\n";
$xkey=$xkey.$snum[$i];}
$xkey=md5("$xkey+$rand_key+$dopkod"); //число + ключ из config.php + код меняющийся кажые 24 часа
print" <input name='usernum' id='txt_usernum' class=post type='text' style='WIDTH: 70px;' maxlength=$max_key size=6> 
(введите число, указанное на картинке) <input name=xkey type=hidden value='$xkey'><input name=stime type=hidden value='$stime'></TD></TR>";
return; }


// БЛОК ЛОГИНЗА - сохранение логина,емайла и пароля антиспама для быстрой работы с фотоальбомом
if (isset($_GET['loginza'])) {

if (isset($_GET['save'])) {
if (isset($_POST['name'])) $name=replacer($_POST['name']); else exit();
if (isset($_POST['email'])) $email=replacer($_POST['email']); else exit();
if (isset($_POST['usernum'])) $usernum=replacer($_POST['usernum']); else exit();
setcookie("wrfotocookies", "$name|$email|$usernum|", time()+1728000);
Header("Location: index.php"); exit; }

print"<center><h1>Логинимся</h1>
<table border=0 width=450 align=center><form action='index.php?loginza&save' method=POST>
<tr><td class=row1 width=100 height=25><span class=gen><b>Имя</b></span></td><td class=row1><input type=text name=name maxlength=$maxname size=35></td></tr>
<tr><td class=row2 height=25><span class=gen>E-mail</span></td><td class=row2><input type=text name=email size=35></td></tr> 
<tr><td class=row2 height=25><span class=gen><B>Ответьте на вопрос:</B></span></td><td class=row2><input name='usernum' type='text' maxlength=20 size=25> ($antispam2012v)</td></tr>
</tr><tr><td class=row1 colspan=4 align=center height=28><input type=submit class=post value='  Сохранить данные  '></td></form>";
exit;}













// РЕПУТАЦИЯ - окно выбора: шаг 1
if (isset($_GET['addrepa'])) {

if (!isset($_GET['fotoname'])) exit("Нет данных переменной fotoname."); $fotoname=replacer($_GET['fotoname']);

/* черновик или вариант <table border=1 align=center>
<TR align=center><TD><img src='smile/sad.gif'></TD><TD>&nbsp;</TD><TD>&nbsp;</TD><TD>&nbsp;</TD><TD><img src='smile/smile.gif'></td></tr>
<TR align=center><TD width=30>1</td><TD>2</td><TD>3</td><TD>4</td><TD>5</td></tr></table> */

print "<html><head><title>Изменение РЕЙТИНГА фотографии:</title></head><body leftMargin=0 topMargin=0 rightMargin=0>
<center><table cellpadding=0 cellspacing=8><TR><FORM action='index.php?repasave' method=post>
<TD colspan=7 align=center><B>Изменение РЕЙТИНГА фотографии</B></TD></TR><TR height=40>
<TD bgcolor=#880003><font size=+2 color=white>-5<INPUT name=repa type=radio value='-5'></TD>
<TD bgcolor=#FF2025><font size=+2 color=white>-2<INPUT name=repa type=radio value='-2'></TD>
<TD bgcolor=#FFB7B9><font size=+2 color=white>-1<INPUT name=repa type=radio value='-1'></TD>
<TD bgcolor=#FFFF00><font size=+2 color=#FF8040>0<INPUT name=repa checked type=radio value='0'></TD>
<TD bgcolor=#A4FFAA><font size=+2 color=white>+1<INPUT name=repa type=radio value='+1'></TD>
<TD bgcolor=#00C10F><font size=+2 color=white>+2<INPUT name=repa type=radio value='+2'></TD>
<TD bgcolor=#00880B><font size=+2 color=white>+5<INPUT name=repa type=radio value='+5'></TD></TR>
<TR><TD colspan=7><B>Комментарий:</B>  <INPUT type=hidden name=fotoname value='$fotoname'><INPUT type=text name=pochemu size=45 value=''><INPUT type=submit value=Отправить></td></TR>
</TABLE></FORM>";

if (is_file("$datadir/rating.dat")) { // Ищем в файле инфу об этой фото и выводим, если есть
$file="$datadir/rating.dat"; $lines=file("$file"); $i=count($lines);
print"<table border=1 cellpadding=2 cellspacing=0 width=100%><TR><TD colspan=5 align=center><B>ЛОГ изменения РЕЙТИНГА фотографии</B></td></tr>
<TR align=center><TD>Когда</TD><TD>IP</TD><TD>Бал</TD><TD width=55%>Комментарий</TD></TR>";
$sbal=null; $itogo=null; $chislo=null;
if ($i>0) do {$i--; $dt=explode("|",$lines[$i]);
$chislo=$dt[1];
if ($dt[1]>0) $dt[1]="<TD align=center bgcolor=#B7FFB7><B>$dt[1]"; else $dt[1]="<TD align=center bgcolor=#FF9F9F><B>$dt[1]";
if ($dt[2]==$fotoname) {
$dt[0]=date("d.m.y в H:i",$dt[0]); 
print"<TR><TD align=center><small>$dt[0]</small></TD><TD align=center><B>$dt[3]</B></TD>$dt[1]</B></TD><TD><small>$dt[4]</small></TD></TR>";
$itogo++; $sbal=$sbal+$chislo;
}
} while($i>0);
if ($itogo>0) $sbal=round($sbal/$itogo,2);
print"</table><B>$sbal</B>"; } // есть есть файл
exit; }



if (isset($_GET['repasave'])) {  // РЕПУТАЦИЯ - сохранение: ШАГ - 2

// Считываем данные, включаем защиты
if (!isset($_POST['fotoname'])) exit("Нет данных переменной name."); $name=replacer($_POST['fotoname']);
if (isset($_POST['repa'])) $repa=replacer($_POST['repa']); else exit("Нет данных переменной repa");
if (isset($_POST['pochemu'])) $pochemu=replacer($_POST['pochemu']); else exit("Укажите причину смены репутации");
if (!is_numeric($repa)) exit("<B>$back. Попытка взлома. Не хулигань, друг!");
if ($repa>5 or $repa<-5) exit("<B>$back. Попытка взлома. Репу можно менять только на +-5 пунктов. Не хулигань, друг!");
if (strlen($pochemu)<1 or strlen($pochemu)>150) exit("<B>$back. Текст причины должен быть указан! И быть не более 150 символов!");

// Защита от накруток по фото. Ставим КУКи на фото, если уже есть куки с именем фото - то облом!
if (isset($_COOKIE[$name])) exit("<br><br><br><br><center><h3><font color=red>Вы уже голосовали за это фото!</font><br><br> Повторное голосование разрешено раз в сутки!<br> Накрутка голосований запрещена!</h3></center>");
//setcookie("$name", "+", time()+86400);
$today=time(); $ip=$_SERVER['REMOTE_ADDR']; // определяем IP юзера

// считываем последнее голосование в память
$file="$datadir/rating.dat"; $lines=file("$file"); $i=count($lines)-1; $dtt=explode("|",$lines[$i]);

// Проверяем IP последнего голосовавшего, если такой же как сейчас - облом
if ($dtt[2]==$name and $dtt[3]==$ip) exit("Голосовать с одного IP за одно и то же фото ЗАПРЕЩЕНО!");

// Если Вам нужно, чтобы можно было проголосовать с одного IP ТОЛЬКО ЗА ОДНО ФОТО, то, разкоментируйте строку!
// if ($dtt[3]==$ip) exit("Голосовать с одного IP за несколько фото ЗАПРЕЩЕНО!"); 

// Проверяем время последнего голосования (ФЛУД) Разрешено голосовать не чаще 1 раз в 60 секунд
if (($today-$dtt[0])<=30) exit("Включена защита от флуда. Голосовать за любое фото чаще 1 раз в 30 секунд запрещено!");

//дата в UNIX-формате|сколько балов|ИМЯ фотографии|IP-шник|комментарий||||
$fp=fopen("$datadir/rating.dat","a+");
flock ($fp,LOCK_EX);
fputs($fp,"$today|$repa|$name|$ip|$pochemu||||\r\n");
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);

exit("<div align=center><BR><BR><BR>Рейтинг <B>успешно</B> пересчитан.<BR><BR><BR><a href='' onClick='self.close()'><b>Закрыть окно</b></a></div>"); }






function addmsg($qm) { // ФУНКЦИЯ добавления темы/сообщения
global $wrfname,$maxname,$canupfile,$antispam,$max_key,$rand_key,$max_upfile_size,$smile,$smiles,$valid_types,$datadir,$flashupload,$id,$antispam2012v;

//Проверка ЗАПРЕТА IP-пользователя на добавление объявлений (файл bad_ip.dat)
$ip=$_SERVER['REMOTE_ADDR']; // определяем IP юзера
if (is_file("$datadir/bad_ip.dat")) { $lines=file("$datadir/bad_ip.dat"); $i=count($lines);
if ($i>0) {do {$i--; $idt=explode("|", $lines[$i]);
   if ($idt[0]===$ip) exit("<TR><TD colspan=2><center><br><br><B>Админитратор заблокировал для Вашего IP: $ip<br> возможность добавлять что-либо по следующей причине:<br><br> <font color=red><B>$idt[1].</B></font><br><br>Вам разрешено просматривать фотоальбом,<br> а вот ДОБАВЛЯТЬ разделы и фотографии категорически ЗАПРЕЩЕНО!</B><br><br><br><br>");
} while($i > "1");} unset($lines);}

$max=round($max_upfile_size/10485.76)/100;

print"<form id=\"form1\" action=\"index.php?event=addanswer\" method=\"post\" enctype=\"multipart/form-data\">";

$addrazdel=FALSE; if (isset($_GET['add'])) { if ($_GET['add']=="newrazdel") $addrazdel=TRUE;}

if ($addrazdel==TRUE) {
print"<TR><TD class=row1><span class=genmed><b>Выбирете ФОТО</B></span></TD><td colspan=2 class=row1 valign=top>Добавление <B>";
foreach($valid_types as $v) print "$v, ";
print"</B> максимально допустмый размер: <B>$max Мб.</B><br>
<input type=file name=file class=post size=70></td></tr>";
} // $addrazdel=FALSE

// Считываем КУКИ блока ЛОГИНЗА и упрощаем жизнь тому, кто добавляет фото!
if (isset($_COOKIE['wrfotocookies'])) {$wrfc=$_COOKIE['wrfotocookies']; $wrfc=replacer($wrfc); $wrfc=explode("|", $wrfc); $name=$wrfc[0]; $email=$wrfc[1]; $usernum=$wrfc[2]; } else {$name=""; $email=""; $usernum="";}
//if (isset($wrfc)) print_r($wrfc);


print"<tr><td class=row1 width=200 height=25><span class=gen><b>Имя</b></span></td><td class=row1>
<input type=text name=name id='txt_name' class=post value='$name' maxlength=$maxname size=65></td></tr>

<tr><td class=row2 width=200 height=25><span class=gen>E-mail</span></td><td class=row2>
<input type=text name=email id='txt_email' value='$email' class=post size=65></td></tr>

<tr><td class=row1 valign=top><span class=genmed><b>Название фото (серии фото)</b><br><br>
<small>* При добавлении нескольких фото к названию добавится порядковый номер фото в разделе.</small></td>
<td class=row1 valign=top><textarea name=msg id='txt_msg' cols=62 rows=2 class=post></textarea></td></tr>";


if ($flashupload==TRUE and $addrazdel==FALSE) { 

$max=round($max_upfile_size/10485.76)/100;

print"<tr><td class=row2 width=200 height=25><span class=gen><B>Ответьте на вопрос:</B></span></td><td class=row2>
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
file_types_description : 'Фотофайлы: ',
file_upload_limit : 200,
file_queue_limit : 0,
custom_settings : {
progressTarget : 'fsUploadProgress',
cancelButtonId : 'btnCancel'
},
debug: false,

// Настройки кнопки
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
<TR><TD class=row1><span class=genmed><b>Выбирете ФОТО</B><br><br> *
 Подсказка: <B>допустимые типы файлов: "; foreach($valid_types as $v) print "$v, "; print"</B><br> максимально допустмый размер каждой фото: <B>$max Мб.</B><br>
</span></TD><td colspan=2 class=row1 valign=top>
<div class=\"fieldset flash\" id=\"fsUploadProgress\"><span class=\"legend\">Очередь загрузки</span></div>
<div id=\"divStatus\">0 файлов загружено</div><div><span id=\"spanButtonPlaceHolder\"></span>
<input id=\"btnCancel\" type=\"button\" value=\"Отменить все загрузки\" onclick=\"swfu.cancelQueue();\" disabled=\"disabled\" style=\"margin-left: 2px; font-size: 8pt; height: 29px;\" /></div>

<!--Если у Вас возникли какие-либо проблемы с загрузкой фотографий, Вы также можете воспользоваться <a href=\"index.php?add=newrazdel&id=$id\">стандартным загрузчиком фотографий.</a>--></div>";
} // Если включен ФЛЭШ-загрузчик

if ($addrazdel==TRUE) {

if ($antispam==TRUE) nospam(); // АНТИСПАМ !
echo'<tr><td class=row1 colspan=2 align=center height=28><input type=submit tabindex=5 class=mainoption value=" Отправить ">&nbsp;&nbsp;&nbsp;<input type=reset tabindex=6 class=mainoption value=" Очистить "></td></tr></table></form>';}

echo'</tr></table></form>';

return;} // КОНЕЦ функции-формы ДОБАВЛЕНИЯ ТЕМЫ/ОТВЕТА



// Выбран ВЫХОД из галереи - очищаем куки
if(isset($_GET['event'])) {if ($_GET['event']=="clearcooke") {setcookie("wrfcookies","",time()); Header("Location: $furl"); exit;}}




// ПРАВИЛА скрипта
if(isset($_GET['rules'])) { 
$frtname=""; $frname="Правила »";
include("$fskin/top.html"); addtop();  // подключаем ШАПКУ
echo'
<center><span class=maintitle>Правила и условия использования ФОТАЛЬБОМА</span><br><br>
<table cellpadding=8 cellspacing=1 width=950 class=forumline><tr><th class=thHead height=25 valign=middle>Правила работы с фотоальбомом</th></tr><tr>
<td class=row1><span class=gen>';
if (is_file("$datadir/pravila.html")) include"$datadir/pravila.html";
echo'</tr></table>';  exit; }





// ФОРМА выбора рубрики для добавления фото
if(isset($_GET['addfoto'])) {
$frtname=""; $frname="Добавление разделов и фото »";
include("$fskin/top.html"); addtop();  // подключаем ШАПКУ
$mainlines=file("$datadir/wrfoto.dat"); $mmax=count($mainlines); $i=0; $kolvo=""; $cn=0;
echo'<form action="index.php?add=newrazdel" metod=post>
<tr class=row1><TD>Категория / Раздел:</TD><TD><SELECT name=id class=maxiinput><option>Выберите рубрику:</option>\r\n';

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
print"</select><input type=hidden name=add value=newrazdel><INPUT type=submit value=Добавить></form>";
exit; }













// ДОБАВЛЕНИЕ ТЕМЫ или ОТВЕТА - ШАГ 1
if(isset($_GET['event'])) {



if ($_GET['event']=="addanswer") {

if ($stop==TRUE) exit("Временно добавление тем и сообщений приостановлено!");

//Проверка ЗАПРЕТА IP-пользователя на добавление объявлений (файл bad_ip.dat)
$ip=$_SERVER['REMOTE_ADDR']; // определяем IP юзера
if (is_file("$datadir/bad_ip.dat")) { $lines=file("$datadir/bad_ip.dat"); $i=count($lines);
if ($i>0) {do {$i--; $idt=explode("|", $lines[$i]);
   if ($idt[0]===$ip) exit("<noindex><script language='Javascript'>function reload() {location = \"$furl\"}; setTimeout('reload()', 10000);</script><center><br><br><B>Админитратор заблокировал для Вашего IP: $ip<br> возможность добавлять что-либо по следующей причине:<br><br> <font color=red><B>$idt[1].</B></font><br><br>Вам разрешено просматривать сообщения,<br> а вот ДОБАВЛЯТЬ ТЕМЫ/СООБЩЕНИЯ категорически ЗАПРЕЩЕНО!</B></noindex>");
} while($i > "1");} unset($lines);}

if (isset($_GET['id'])) $id=$_GET['id']; if ((!ctype_digit($id)) or (strlen($id)!=7)) exit();
$fid=substr($id,0,3);

// Считываем данные: автор, емайл, описание, защитный код
if (isset($_POST['email'])) $email=replacer($_POST['email']); else $email="";
if (isset($_POST['name'])) $name=replacer($_POST['name']); else $name="";
if (isset($_POST['msg'])) $msg=replacer($_POST['msg']); else $msg="";
if (isset($_POST['usernum'])) $usernum=replacer($_POST['usernum']); else $usernum="";

// Заменяем символ | чтобы не повредилась структура данных
$name=str_replace("|","&#124;",$name); $email=str_replace("|","&#124;",$email); $msg=str_replace("|","&#124;",$msg);

//--А-Н-Т-И-С-П-А-М--проверка кода--
if ($antispam2012==TRUE) {if (strtolower($antispam2012o)!=strtolower($usernum) or strlen($usernum)<1) 
{header("HTTP/1.1 500 File Upload Error"); exit("введите имя и/или описание фото!");}}

if (strlen($name)<1 or strlen($msg)<3) {header("HTTP/1.1 500 File Upload Error"); exit("введите имя и/или описание фото!");}

// ЕСЛИ файл не загружен или есть какакая то проблема, то ВЫВОДИМ ОШИБКУ 500
if (!isset($_FILES["Filedata"]) || !is_uploaded_file($_FILES["Filedata"]["tmp_name"]) || $_FILES["Filedata"]["error"] != 0) {
header("HTTP/1.1 500 File Upload Error"); echo $_FILES["Filedata"]["error"]; exit; }


if (isset($_FILES)) { //если получен файл, то проверяем размер и тип файла

$ext = end(explode('.', strtolower($_FILES['Filedata']['name'])));
if (!in_array($ext, $valid_types)) return;

if ($max_upfile_size < $_FILES['Filedata']['size']) return;

if (is_uploaded_file($_FILES['Filedata']['tmp_name'])) {

// Переименовываем все файлы по маске: ХХХYYYYZZZZ, где ZZZZ - случайное число.
$key=mt_rand(1000,9999); $fileName = $filedir.'/'."$id$key.$ext";

move_uploaded_file($_FILES['Filedata']['tmp_name'], $fileName);

// Создаём миниатюрку изображения
$smallfoto="$filedir/sm-$id$key.jpg";
img_resize("$fileName", "$smallfoto", $smwidth, $smheight);
if (is_file("$datadir/$id.dat")) $idlines=file("$datadir/$id.dat"); $iid=count($idlines)+1; $msg.=" $iid";

$fsize=$_FILES['Filedata']['size'];
$fotoksize=round($fsize/1024); // размер ЗАГРУЖАЕМОГО файла в Кб.
$size=getimagesize($fileName); // Определяем габариты файла (ШИРИНА и ВЫСОТА)

$date=date("d.m.y"); // число.месяц.год
$time=date("H:i"); // часы:минуты:секунды
$today=time();
$ip=$_SERVER['REMOTE_ADDR']; // определяем IP юзера
$fileName=str_replace("$filedir/",'',$fileName);
if (!is_file("$smallfoto")) $smallfoto=$fileName;
$smallfoto=str_replace("$filedir/",'',$smallfoto);

//$usernum - проверять если верный код то сохранять иначе по бороде.

if (!is_file("$datadir/$id.dat")) $nlines2=1; else
{$nlinesdat=file("$datadir/$id.dat"); $nlines2=count($nlinesdat)+1; $ndt=explode("|", $nlinesdat[0]); $razdel_name=$ndt[3];}

header("Content-Type: text/html; charset=win1251");
$record="$name|$email||razdel_name|$msg|$date|$time|$id||$today|$name|razdel_name|1|$fileName|$fsize||$ip|$smallfoto|$size[0]|$size[1]|$fotoksize||||||\r\n";
$record=iconv("UTF-8","windows-1251",$record);
$record=str_replace("razdel_name","$razdel_name",$record);

if (strlen($id)==3) { // пишем данные в файл
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



// Прибавляем +1 при каждом загруженном фото!
$realfid=null; $fotodetali=null;
$realbase="1"; if (is_file("$datadir/wrfoto.dat")) $mainlines=file("$datadir/wrfoto.dat");
$i=count($mainlines);
do {$i--; $dt=explode("|", $mainlines[$i]);
if ($dt[0]==$fid) {$realfid=$i; if ($dt[1]=="razdel") exit("$back. Данной ветки фотоальбома не существует");} // присваиваем $realfid - № п/п строки
} while($i>0);

if ($realbase==TRUE) { // Если подключена рабочая база, а не копия
$lines=file("$datadir/wrfoto.dat"); $max=sizeof($lines)-1;
$dt=explode("|", $lines[$realfid]); $dt[5]++;
$main_id="$fid$id";
$txtdat="$dt[0]|$dt[1]|$dt[2]|$main_id|$dt[4]|$dt[5]|$smname|$date|$time|$tektime|$smzag|$dt[11]|$dt[12]||||";
$kategory_name="$dt[1]";
// запись данных на главную страницу
$fp=fopen("$datadir/wrfoto.dat","a+");
flock ($fp,LOCK_EX);
ftruncate ($fp,0);//УДАЛЯЕМ СОДЕРЖИМОЕ ФАЙЛА
for ($i=0;$i<=$max;$i++) {if ($i==$realfid) fputs($fp,"$txtdat\r\n"); else fputs($fp,$lines[$i]);}
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);
} // if ($realbase==TRUE)



if ($newmess==TRUE) { // запись в отдельный файл нового сообщения
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

// Блок проверяет, есть ли уже новое сообщение в этой теме. Если есть - отсеивает. На выходе - массив без этой строки.
for ($i=0;$i<=$ni;$i++) { $ndt=explode("|",$newlines[$i]);
if (isset($ndt[1])) {if ("$id"!=$ndt[1]) $newlineexit.="$newlines[$i]"; $i2++; } }

// Записываем свежее сообщение в массив и далее сохраняем его в файл
if ($maxzd<1) { // Если тема доступна для всех - нет ограничений по звёздам
if ($i2>0) { // Если есть такая тема, то пишем весь массив, иначе тока строку
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

if ($stop==TRUE) exit("Временно добавление тем и сообщений приостановлено!");

if (isset($_POST['name'])) $name=$_POST['name'];
$name=trim($name); // Вырезает ПРОБЕЛьные символы 
$zag=$_POST['zag']; $msg=$_POST['msg'];

$fid=$_GET['id']; 
if ($_GET['event']=="addanswer") $id=substr($fid,3,4);
if (strlen($fid)>3) $fid=substr($fid,0,3);

if (isset($_POST['who'])) $who=$_POST['who']; else $who="";
if (isset($_POST['email'])) $email=$_POST['email']; else $email="";
if (isset($_POST['page'])) $page=$_POST['page'];
if (isset($_POST['maxzd'])) $maxzd=$_POST['maxzd']; else $maxzd="0"; if ($maxzd==null) $maxzd="0";
if ((!ctype_digit($maxzd)) or (strlen($maxzd)>2)) exit("<B>$back. Попытка взлома по звёздам или ошибка в файле статистики</B>");

// защита по топику fid
if (!ctype_digit($fid) or strlen($fid)>3) exit("<B>$back. Попытка взлома через номер рубрики. Номер должен содержать только цифры и быть менее 4 символов</B>");

//--А-Н-Т-И-С-П-А-М--проверка кода--
if ($antispam==TRUE and !isset($_COOKIE['wrfcookies'])) {
if (!isset($_POST['usernum']) or !isset($_POST['xkey']) or !isset($_POST['stime']) ) exit("данные из формы не поступили!");
$usernum=replacer($_POST['usernum']); $xkey=replacer($_POST['xkey']); $stime=replacer($_POST['stime']);
$dopkod=mktime(0,0,0,date("m"),date("d"),date("Y")); // доп.код. Меняется каждые 24 часа
$usertime=md5("$dopkod+$rand_key");// доп.код
$userkey=md5("$usernum+$rand_key+$dopkod");
if (($usertime!=$stime) or ($userkey!=$xkey)) exit("введён ОШИБОЧНЫЙ код!");}

// проходим по всем разделам и топикам - ищем запращиваемый
// на тот случай, если wrfoto.dat - пуст, подключаем резервную копию

$realbase="1"; if (is_file("$datadir/wrfoto.dat")) $mainlines=file("$datadir/wrfoto.dat");
if (!isset($mainlines)) $datasize=0; else $datasize=sizeof($mainlines);
if ($datasize<=0) {if (is_file("$datadir/copy.dat")) {$realbase="0"; $mainlines=file("$datadir/copy.dat"); $datasize=sizeof($mainlines);}}
if ($datasize<=0) exit("$back. Проблемы с Базой данных, файл данных пуст - обратитесь к администратору");
$i=count($mainlines);

$realfid=null; $fotodetali=null;
do {$i--; $dt=explode("|", $mainlines[$i]);
if ($dt[0]==$fid) {$realfid=$i; if ($dt[1]=="razdel") exit("$back. Данной ветки фотоальбома не существует");} // присваиваем $realfid - № п/п строки
} while($i>0);

if (!isset($realfid)) exit("$back. Ошибка с номером рубрики. Она не существует в базе");

$dt=explode("|",$mainlines[$realfid]);
if (is_file("$datadir/topic$fid.dat")) {$tlines=file("$datadir/topic$fid.dat"); $tc=count($tlines)-2; $i=$tc+2; $ok=null;
// нужно пробежаться по топику, найти тему. Если есть - нормуль, нету - значит добавление сообщений ЗАПРЕЩЕНО!
if ($_GET['event']=="addanswer") {
do {$i--; $tdt=explode("|", $tlines[$i]);
if ($tdt[7]=="$fid$id") {$ok=1; if ($tdt[8]=="closed") exit("$back тема закрыта и добавление сообщений запрещено!"); }
} while($i>0);
if ($ok!=1) exit("$back тема закрыта и добавление сообщений запрещено!"); }

} else $tc="2";
if ($dt[11]>0) {if ($tc>=$dt[11]) exit("$back. Превышено ограничение на кол-во допустимых тем в данной рубрике! Не более <B>$dt[11]</B> тем!");}

print"<html><head><link rel='stylesheet' href='$fskin/style.css' type='text/css'></head><body>";

if ($_GET['event']=="addtopic" and $cangutema==FALSE and !isset($wrfname)) exit("<center>Администратор запретил гостям создавать темы!</center><BR><BR>");
if ($_GET['event']=="addanswer" and $cangumsg==FALSE and !isset($wrfname)) exit("<center>Администратор запретил гостям отвечать в темах!</center><BR><BR>");

// БЛОК ГЕНЕРИРУЕТ СЛЕДУЮЩИЙ ПО ПОРЯДКУ НОМЕР ТЕМЫ, начиная просмотр с 1000
// считываем весь файл с темами в память
if ($_GET['event']=="addtopic") { $id=1000; $id="$fid$id";
$allid=null; $records=file("$datadir/topic$fid.dat"); $imax=count($records); $i=$imax;
if ($i > 0) { do {$i--; $rd=explode("|",$records[$i]); $allid[$i]=$rd[7]; } while($i>0);
//natcasesort($allid); // сортируем по возрастанию
do $id++; while(in_array($id,$allid) or is_file("$datadir/$id.dat"));
} else $id=$fid."1000"; } // if (event==addtopic)

// генерируем имя файлу с темой - СТАРЫЙ механизм
//if ($_GET['event']=="addtopic") { if ($fid<10) $add="0"; else $add="";
//do $id=mt_rand(1000,9999); while (file_exists("$datadir/$add$fid$id.dat"));
//$id="$add$fid$id"; }

if (!isset($_FILES['file']['name'])) exit("Сделайте выбор файла для загрузки!");

if (isset($_FILES['file']['name'])) { // ЕСЛИ ДОБАВЛЯЕМ ФАЙЛ
$fotoname=replacer($_FILES['file']['name']); 
if (strlen($fotoname)<3) exit("ОШИБКА загрузки файла! Введите имя файла или сделайте выбор другого файла!");
else { $fotosize=$_FILES['file']['size']; // Имя и размер файла

//---- ЗАЩИТЫ от ВЗЛОМА -----

// 1. Проверяем РАСШИРЕНИЕ
$ext = strtolower(substr($fotoname, 1 + strrpos($fotoname, ".")));
if (!in_array($ext, $valid_types)) {echo "<B>ФАЙЛ НЕ загружен.</B> Возможные причины:<BR>
- разрешена загрузка только файлов с такими расширениями: <B>";
$patern=""; foreach($valid_types as $v) print"$v, ";
print"</B><BR>
- Вы пытаетесь загрузить файл с двойным расширением;<BR>
- неверно введён адрес или выбран испорченный файл;</B><BR>"; exit;}

// 2. считаем КОЛ-ВО ТОЧЕК в выражении - если большей одной - СВОБОДЕН!
$findtchka=substr_count($fotoname, "."); if ($findtchka>1) exit("ТОЧКА встречается в имени файла $findtchka раз(а). Это ЗАПРЕЩЕНО! <BR>\r\n");

// 3. если в имени есть .php, .html, .htm - свободен! 
$bag="Извините, но в имени ФАйла <B>запрещено</B> использовать .php, .html, .htm";
if (preg_match("/\.php/i",$fotoname)) exit("Вхождение <B>.php</B> найдено. $bag");
if (preg_match("/\.html/i",$fotoname)) exit("Вхождение <B>.html</B> найдено. $bag");
if (preg_match("/\.htm/i",$fotoname)) exit("Вхождение <B>.htm</B> найдено. $bag");

// 4. Размер файла
$fotoksize=round($fotosize/1024); // размер ЗАГРУЖАЕМОГО файла в Кб.
$fotomax=round($max_upfile_size/1024); // максимальный размер файла в Кб.
if ($fotoksize>$fotomax) exit("Вы превысили допустимый размер файла! <BR><B>Максимально допустимый</B> размер: <B>$fotomax </B>Кб.<BR> <B>Вы пытаетесь</B> загрузить файл размером: <B>$fotoksize</B> Кб!");

// ЕСЛИ включен порядок присвоения файлу случайного имени при загрузке - генерируем случайное имя
if ($_GET['event']!="addtopic") $numb="$fid$id"; else $numb=$id;
do $key=mt_rand(1000,9999); while (file_exists("$filedir/$numb$key.$ext")); $fotoname="$numb$key.$ext";

if (copy($_FILES['file']['tmp_name'], $filedir."/".$fotoname)) {print "<br><br>Файл УСПЕШНО загружен: $fotoname (Размер: $fotosize байт)"; $fotodetali="1|$fotoname|$fotosize|";}
else echo "ОШИБКА загрузки файла - $fotoname...\n"; }}

// Проверяем размер фото. Если "габариты" меньше заданный в админке 150 х 120 - то ничего с ним не делаем
// блок делает мальное изображение исходной фотки - в качестве превьюшки
$size=getimagesize($_FILES['file']['tmp_name']);
if ($size[0]>$smwidth or $size[1]>$smheight) {
$smallfoto="sm-$fotoname";
if (img_resize("$filedir/$fotoname", "$filedir/$smallfoto", $smwidth, $smheight))  echo 'Изображение масштабировано <B>успешно</B>.'; else  echo '<font color=red><B>Ошибка МАСШАБИРОВАНИЯ фото! Поблемы с GD-библиотекой!</B></font> Обратитесь к Администратору';
} else $smallfoto="$fotoname";

$tektime=time();
$name=wordwrap($name,30,' ',1); // разрываем длинные строки
$zag=wordwrap($zag,50,' ',1);
$name=str_replace("|","I",$name);
$who=str_replace("|","&#124;",$who);
$email=str_replace("|","&#124;",$email);
$zag=str_replace("|","&#124;",$zag);
$msg=str_replace("|","&#124;",$msg);

$smname=$name; if (strlen($name)>18) {$smname=substr($name,0,18); $smname.="..";}
$smzag=$zag; if (strlen($zag)>24) {$smzag=substr($zag,0,24); $smzag.="..";}

if (strlen($id)>8) exit("<B>$back. Номер темы должен быть числом. Критическая ошибка скрипта или попытка взлома</B>");
if (strlen(ltrim($zag))<3) exit("$back ! Ошибка в вводе данных заголовка!");

$ip=$_SERVER['REMOTE_ADDR']; // определяем IP юзера
$text="$name|$email|$who|$zag|$msg|$date|$time|$id||$tektime|$smname|$smzag|$fotodetali|$ip|$smallfoto|$size[0]|$size[1]|$fotoksize|||||";
$text=replacer($text); $exd=explode("|",$text); 
$name=$exd[0]; $zag=$exd[3]; $smname=$exd[10]; $smzag=$exd[11]; $smmsg=$exd[4];

if (!isset($name) || strlen($name) > $maxname || strlen($name) <1) exit("$back Ваше <B>Имя пустое, или превышает $maxname</B> символов!</B></center>");
if (preg_match("/[^(\\w)| |(\\x7F-\\xFF)|(\\-)]/",$name)) exit("$back Ваше имя содержит запрещённые символы. Разрешены русские и английские буквы, цифры, подчёркивание и тире.");
if (strlen(ltrim($zag))<3 || strlen($zag) > $maxzag) exit("$back Слишком короткое название темы или <B>название превышает $maxzag</B> символов!</B></center>");
if (strlen(ltrim($msg))<2 || strlen($msg) > $maxmsg) exit("$back Ваше <B>сообщение короткое или превышает $maxmsg</B> символов.</B></center>");
if (!preg_match('/^([0-9a-zA-Z]([-.w]*[0-9a-zA-Z])*@([0-9a-zA-Z][-w]*[0-9a-zA-Z].)+[a-zA-Z]{2,9})$/si',$email) and strlen($email)>30 and $email!="") exit("$back и введите корректный E-mail адрес!</B></center>");

// функция АНТИФЛУД здесь - повторное добавление сообщения/темы запрещено!
if (isset($tlines)) {
if ($tc<"-1") {$sdt[0]=null; $sdt[3]=null;} else {$last=$tlines[$tc+1]; $sdt=explode("|",$last);}

if ($_GET['event'] =="addtopic")  { // ЕСЛИ добавление ТЕМЫ: имя = имя в файле, тема = последняя тема в файле
if ($name==$sdt[0] and $exd[3]==$sdt[3]) exit("$back. Такая тема уже создана. Спамить на форуме запрещено!");

} else { // ЕСЛИ добавление сообщения: имя = имя в файле, сообщение = последнему сообщению в файле
if (is_file("$datadir/$fid$id.dat")) {$linesn=file("$datadir/$fid$id.dat"); $in=count($linesn)-1;
if ($in > 0) { $dtf=explode("|",$linesn[$in]);
if ($name==$dtf[0] and $exd[4]==$dtf[4]) exit("$back. Такое сообщение уже размещено в данной теме. Спамить на форуме запрещено!");}
}
}} // if $event=="addtopic"


$razdelname="";
if ($realbase==TRUE and $maxzd<1) { // Если подключена рабочая база, а не копия
$lines=file("$datadir/wrfoto.dat"); $max=sizeof($lines)-1;
$dt=explode("|", $lines[$realfid]); $dt[5]++;
if ($_GET['event']=="addtopic") {$main_id="$id"; $dt[4]++;} else $main_id="$fid$id";
$txtdat="$dt[0]|$dt[1]|$dt[2]|$main_id|$dt[4]|$dt[5]|$smname|$date|$time|$tektime|$smzag|$dt[11]|$dt[12]||||";
$razdelname=$dt[1];
// запись данных на главную страницу
$fp=fopen("$datadir/wrfoto.dat","a+");
flock ($fp,LOCK_EX);
ftruncate ($fp,0);//УДАЛЯЕМ СОДЕРЖИМОЕ ФАЙЛА
for ($i=0;$i<=$max;$i++) {if ($i==$realfid) fputs($fp,"$txtdat\r\n"); else fputs($fp,$lines[$i]);}
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);
} // if ($realbase==TRUE)

if ($newmess==TRUE and $maxzd<1) { // запись в отдельный файл нового сообщения
if (is_file("$datadir/topic$fid.dat")) $nlines=count(file("$datadir/topic$fid.dat")); else $nlines=1;

if (is_file("$datadir/$fid$id.dat")) $nlines2=count(file("$datadir/$fid$id.dat"))+1; else $nlines2=1;

$timestamp=time();
$newmessfile="$datadir/news.dat";
$newlines=file("$newmessfile"); $ni=count($newlines)-1; $i2=0; $newlineexit="";
$ntext="$fid|$main_id|$date|$time|$smname|$zag|$msg|$nlines|$nlines2|$razdelname|$timestamp|$fotoname|$smallfoto||";
$ntext=str_replace("
", "<br>", $ntext);

// Блок проверяет, есть ли уже новое сообщение в этой теме. Если есть - отсеивает. На выходе - массив без этой строки.
for ($i=0;$i<=$ni;$i++) { $ndt=explode("|",$newlines[$i]);
if (isset($ndt[1])) {if ("$fid$id"!=$ndt[1]) $newlineexit.="$newlines[$i]"; $i2++; } }

// Записываем свежее сообщение в массив и далее сохраняем его в файл
if ($maxzd<1) { // Если тема доступна для всех - нет ограничений по звёздам
if ($i2>0) { // Если есть такая тема, то пишем весь массив, иначе тока строку
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





if ($_GET['event'] =="addtopic")  { // Добавление ТЕМЫ - запись данных
// Пишем В ТОПИК
$fp=fopen("$datadir/topic$fid.dat","a+");
flock ($fp,LOCK_EX);
fputs($fp,"$text\r\n");
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);

// Пишем В ТЕМУ
$fp=fopen("$datadir/$id.dat","a+");
flock ($fp,LOCK_EX);
fputs($fp,"$text\r\n");
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);

print "<script language='Javascript'>function reload() {location = \"index.php?id=$id\"}; setTimeout('reload()', 1500);</script>
<table width=100% height=80%><tr><td><table border=1 cellpadding=10 cellspacing=0 bordercolor=#224488 align=center valign=center width=60%><tr><td><center>
Спасибо, <B>$name</B>, за добавление темы!<BR><BR>Через несколько секунд Вы будете автоматически перемещены в созданную тему.<BR><BR>
<B><a href='index.php?id=$id'>ДАЛЬШЕ >>></a></B></td></tr></table></td></tr></table></center></body></html>";
exit; }



if ($_GET['event'] =="addanswer")  { //ОТВЕТ В ТЕМЕ - запись данных
$timetek=time(); $timefile=filemtime("$datadir/$fid$id.dat"); 
$timer=$timetek-$timefile; // узнаем сколько прошло времени (в секундах) 
$fp=fopen("$datadir/$fid$id.dat","a+");
flock ($fp,LOCK_EX);
fputs($fp,"$text\r\n");
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);
if ($timer<0) {$viptime=strtotime("+2 year"); touch("$datadir/$fid$id.dat",$viptime);}

print "<script language='Javascript'>function reload() {location = \"index.php?id=$fid$id$pageadd#m$in\"}; setTimeout('reload()', 1500);</script>
<table width=100% height=80%><tr><td><table border=1 cellpadding=10 cellspacing=0 bordercolor=#224488 align=center valign=center width=60%><tr><td><center>
Спасибо, <B>$name</B>, Ваш ответ успешно добавлен.<BR><BR>Через несколько секунд Вы будете автоматически перемещены в текущую тему <BR><B>$zag</B>.<BR><BR>
<B><a href='index.php?id=$fid$id$pageadd#m$in'>ДАЛЬШЕ >>></a></B></td></tr></table></td></tr></table></center></body></html>";
exit;
}
} //event










// ПОИСК
if(isset($_GET['find'])) {
$frtname=""; $frname="Поиск фото »";
include("$fskin/top.html"); addtop();  // подключаем ШАПКУ

$minfindme="3"; //минимальное кол-во символов в слове для поиска
print"<center><span class=maintitle>Поиск фото</span><br><br>
<form action='index.php?event=go&findme' method=POST>
<center><table class=forumline align=center width=1000>
<tr><th class=thHead colspan=4 height=25>Поиск</th></tr>
<tr class=row2>
<td class=row1>Запрос: <input type='text' style='width: 250px' class=post name=findme size=30></TD>
<TD class=row1>Тип: <select style='FONT-SIZE: 12px; WIDTH: 120px' name=ftype>
<option value='0'>&quotИ&quot
<option value='1' selected>&quotИЛИ&quot
<option value='2'>Вся фраза целиком
</select></td>
<td class=row1><INPUT type=checkbox name=withregistr><B>С учётом РЕГИСТРА</B></TD>
<input type=hidden name=gdefinder value='1'>
</tr><tr class=row1>
<td class=row1 colspan=4 width=\"100%\">
Язык запросов:<br><UL>
<LI><B>&quotИ&quot</B> - должны присутствовать оба слова;</LI><br>
<LI><B>&quotИЛИ&quot</B> - есть ХОТЯ БЫ одно из слов;</LI><br>
<LI><B>&quotВся фраза целиком&quot</B> - в искомом документе ищите фразу на 100% соответствующую вашему запросу;</LI><BR><BR>
<LI><B>&quotС учётом РЕГИСТРА&quot</B> - поиск ведётся с учётом введённого ВАМИ РЕГИСТРА;</LI><BR><BR>
</UL>Скрипт ищет все данные, которые начинаются с введенной вами строки. Например, при запросе &quot фото &quot будут найдены слова &quot фото &quot, &quot фотоальбом &quot, &quot фотография &quot и многие другие.
</td>

</tr><tr><td class=row1 colspan=4 align=center height=28><input type=submit class=post value='  Поиск  '></td></form>
</tr></table><BR><BR>";
print "Ограничение на поиск: <BR> - минимальное кол-во символов: <B>$minfindme</B>";
exit; }



if (isset($_GET['findme']))  {

$frtname=""; $frname="Поиск фото »";
include("$fskin/top.html"); addtop();  // подключаем ШАПКУ

$minfindme="2"; //минимальное кол-во символов в слове для поиска
$time=explode(' ', microtime()); $start_time=$time[1]+$time[0];  // считываем начальное время запуска поиска

$gdefinder="1"; $ftype=$_POST['ftype'];
if (!ctype_digit($ftype) or strlen($ftype)>2) exit("<B>$back. Попытка взлома. Хакерам здесь не место.</B>");
if (!isset($_POST['withregistr'])) $withregistr="0"; else $withregistr="1";

// Защита от взлома
$text=$_POST['findme'];
$text=replacer($text);
$findmeword=explode(" ",$text); // Разбиваем $findme на слова
$wordsitogo=count($findmeword);
$findme=trim($text); // Вырезает ПРОБЕЛьные символы 
if ($findme == "" || strlen($findme) < $minfindme) exit("$back Ваш запрос пуст, или менее $minfindme символов!</B>");

// Открываем файл с темами формума и запоминаем имена файлов с сообщениями

setlocale(LC_ALL,'ru_RU.CP1251'); // ! РАЗРЕШАЕМ РАБОТУ ФУНКЦИЙ, работающих с регистором и с РУССКИМИ БУКВАМИ

// ПЕРВЫЙ цикл - считаем кол-во форумов (записываем в переменную $itogofid)
$mainlines = file("$datadir/wrfoto.dat");$i=count($mainlines); $itogofid="0";$number="0"; $oldid="0"; $nump="0";
do {$i--; $dt=explode("|", $mainlines[$i]);
if ($dt[1]!="razdel") { $maxzd=$dt[12]; if (!ctype_digit($maxzd)) $maxzd=0; } // считываем ЗВЁЗДы раздела из файла
if ($dt[1]!="razdel" and $maxzd<1) {$itogofid++; $fids[$itogofid]=$dt[0];} // $itogofid - общее кол-во форумов
} while($i > "0");

// ВТОРОЙ цикл - открываем файл с топиком (если он существует) и сохраняем в переменную $topicsid все имена тем
do { $fid=$fids[$itogofid];
if (is_file("$datadir/topic$fid.dat")) {
$msglines=file("$datadir/topic$fid.dat");

unset($topicsid); if (count($msglines)>0) { $lines=file("$datadir/topic$fid.dat"); $i=count($lines);
do {$i--; $dt=explode("|",$lines[$i]); $topicsid[$i]=$dt[7];} while($i > "0"); }


// ТРЕТИЙ цикл - последовательно открываем каждую тему

if (isset($topicsid)) {

$ii=count($topicsid);
do {$ii--;
$id = str_replace("\r\n","",$topicsid[$ii]);

if (is_file("$datadir/$id.dat")) { // Если файл есть? Бывает, что файлы с сообщениями бьются, тогда при пересчёте они удаляются.
$file=file("$datadir/$id.dat"); $iii=count($file);

// ЧЕТВЁРТЫЙ цикл - последовательно ищем в каждой теме искомое сообщение
if ($iii>0) { // если файл с сообщениями НЕ ПУСТОЙ
do {$iii--; 
$lines = file("$datadir/$id.dat");
$dt = explode("|", $lines[$iii]); if (!isset($dt[4])) $dt[4]=" ";

if ($gdefinder=="0") {$msgmass=array($dt[2],$dt[3],$dt[4]); $gi="3"; $add="ях <B>Автор, Текст, Заголовок</B> ";}
if ($gdefinder=="1") {$msgmass=array($dt[4]); $gi="1"; $add="е <B>Текст</B> ";}
if ($gdefinder=="2") {$msgmass=array($dt[3],$dt[4]); $gi="2"; $add="ях <B>Текст и Заголовок</B> ";}
if ($gdefinder=="3") {$msgmass=array($dt[2]); $gi="1"; $add="е <B>Автор</B> ";}
if ($gdefinder=="4") {$msgmass=array($dt[3]); $gi="1"; $add="е <B>Заголовок</B> ";}

// Цикл по местам поиска (0,1,2,3,4)
do {$gi--;

$msg=$dt[4];
$msdat=$msgmass[$gi];
$stroka="0"; $wi=$wordsitogo;
// ЦИКЛ по КАЖДОМУ слову запроса !
do {$wi--;



// БЛОК УСЛОВИЙ ПОИСКА
if ($withregistr!="1") // регистронезависимый поиск - cимвол "i" после закрывающего ограничителя шаблона - /
   {
    if ($ftype=="2") 
        {
        if (stristr($msdat,$findme))     // ПОИСК по "ВСЕЙ ФРАЗЕ ЦЕЛИКОМ" БЕЗ учёта регистра
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
               if (stristr($str1,$str2)) // ПОИСК БЕЗ учёта регистра при равных прочих условиях
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
        if (strstr($msdat,$findme))           // ПОИСК по "ВСЕЙ ФРАЗЕ ЦЕЛИКОМ" C учёта РЕГИСТРА
           {
            $stroka++;
            $msg=str_replace($findme," <b><u>$findme</u></b> ",$msg);
           }
       }
     else {
           if ($msdat!="" and strlen($findmeword[$wi]) >= $minfindme)
              {
               if (strstr($msdat,$findmeword[$wi]))     // ПОИСК С учётом РЕГИСТРА при равных прочих условиях
                  {
                   $stroka++;
                   $msg=str_replace($findmeword[$wi]," <b><u>$findmeword[$wi]</u></b> ",$msg);
                  }
              }
          }

   }   //  if ($withregistr!="1")



} while($wi > "0");  // конец ЦИКЛа по КАЖДОМУ слову запроса


// Подготавливаем результирующее сообщение, и если результат соответствует условиям - выводим его
if ($ftype=="0") { if ($stroka==$wordsitogo) $printflag="1"; }
if ($ftype=="1") { if ($stroka>"0") $printflag="1"; }
if ($ftype=="2") { if ($stroka==$wordsitogo) $printflag="1"; }


if (!isset($printflag)) $printflag="0";
    if ($printflag=="1")
       { $msg=str_replace("<br>", " &nbsp;&nbsp;", $msg); // заменяем в сообщении <br> на пару пробелов


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
<small><BR>По запросу '<U><B>$findme</B></U>' в пол$add найдено: <HR size=+2 width=950 color=navy>
<BR><form action='index.php?event=go&findme' method=POST>
<table class=forumline align=center width=950>
<tr><th class=thHead colspan=4 height=25>Повторить поиск</th></tr>
<tr class=row2>
<td class=row1>Запрос: <input type='text' value='$findme' style='width: 250px' class=post name=findme size=30>
<INPUT type=hidden value='1' name=ftype>
<input type=hidden name=gdefinder value='1'>
<input type=submit class=post value='  Поиск  '></td></table></form><br>
<table width=100% class=forumline><TR align=center class=small><TH class=thCornerL><B>№</B></TH><TH class=thCornerL width=35%><B>Заголовок</B></TH><TH class=thCornerL width=70%><B>часть сообщения</B></TH><TH class=thCornerL><B>Совпадений<BR> в теме</B></TH></TR>"; $m="1"; }

if ($iii>$qq) {$in=$iii+2; $page=ceil($in/$qq);} else $page="1";  // расчитываем верную страницу и номер сообщения

if ($oldid!=$id and $number<100) { $number++; $msgnumber=$iii;

if ($nump>1) $anp="$nump"; else $anp="1";
if ($number>1) print"<TD class=row1 align=center>$anp</TD></TR><TR height=25>";

print "<TD class=row1 align=center><B>$number</B></TD>
<TD class=row1><A class=listlink href='index.php?id=$id&page=$page#m$iii'>$dt[3]</A></TD>
<TD class=row1>$msgtowrite</TD>";
$printflag="0"; $nump="0";

} else $nump++;

if ($number>=100) { print"</TR></TABLE> * поиск останавливается, при нахождении более 100 вхождений!"; $gi=0; $iii=0; $ii=0; $itogofid=0;}

$oldid=$id;
} // if $printflag==1

} while($gi > "0");  // конец ЦИКЛа по МЕСТУ поиска

} while($iii > "0");
} // если файл с сообщениями НЕПУСТОЙ

} // if is_file("$datadir/$id.dat")
} while($ii > "0");

} // if isset($topicsid)

} // if файл topic$fid.dat НЕ пуст


$itogofid--;
} while($itogofid > "0");
if (!isset($m)) echo'<table width=80% align=center><TR><TD>По вашему запросу ничего не найдено.</TD></TR></table>';

$time=explode(' ',microtime());
$seconds=($time[1]+$time[0]-$start_time);
echo "</TR></table><HR size=+2 width=99% color=navy><BR><p align=center><small>".str_replace("%1", sprintf("%01.3f", $seconds), "Время поиска: <b>%1</b> секунд.")."</small></p>";
exit;
}




















// БЛОК подключает копию главного файла при повреждении
if (is_file("$datadir/wrfoto.dat")) {$mainlines=file("$datadir/wrfoto.dat"); $imax=count($mainlines); $i=$imax;}
if (!isset($mainlines)) $datasize=0; else $datasize=sizeof($mainlines);
if ($datasize<=0) {if (is_file("$datadir/copy.dat")) {$mainlines=file("$datadir/copy.dat"); $datasize=sizeof($mainlines);}}
if ($datasize<=0) exit("<center><b>Файл РУБРИК несуществует! Администратор! Зайдите в <a href='admin.php'>админку</a> и создайте рубрики!</b>");

$error=FALSE; $raz=""; $frname=null; $frtname=""; $rfid="";

// ДЛЯ ссылки типа razdel=
if (isset($_GET['razdel'])) {
do {$i--; $dt=explode("|", $mainlines[$i]);
if ($dt[0]==$_GET['razdel']) {$rfid=$i; $frname="$dt[2] »";}
} while($i >0);
$i=$imax;}









$maxtem=999;

if (isset($_GET['id'])) { // Блок выводит в статусной строке: ТЕМА » РАЗДЕЛ » ФОРУМ
$id=$_GET['id'];
if (strlen($id)<=3 and !is_file("$datadir/topic$id.dat")) $error="ый Вами раздел";
if (strlen($id)!=11 and strlen($id)> 3 and !is_file("$datadir/$id.dat")) $error="ая Вами рубрика";
if (!ctype_digit($id)) $error="ая Вами рубрика или раздел";
if (isset($_GET['quotemsg'])) $error=TRUE; $fulid=null;

if(strlen($id)>3) {$fulid=$id; $fid=substr($id,0,3); $id=substr($id,3,4);} else $fid=$id;
$imax=count($mainlines); $i=$imax;

// проходим по всем разделам и топикам - ищем запрашиваемый
do {$i--; $dt=explode("|", $mainlines[$i]);
if ($dt[0]==$fid) { $frname="$dt[1] »";
if (isset($dt[11])) { if($dt[11]>0) $maxtem=$dt[11]; else $maxtem="999";}}
} while($i >0);

//$frtname="1"; $frname="2"; $fname="3";
// Блок считывает название темы для отображения в шапке форума
if (strlen($id)>3 and is_file("$datadir/topic$fid.dat")) {
$lines=file("$datadir/topic$fid.dat"); $imax=count($lines); $i=$imax;
if ($i>1) {
do {$i--; $dt=explode("|",$lines[$i]);
if($dt[7]=="$fid$id") $frtname="$dt[3] »";
} while ($i>0); }}


// Если показывается фото крупным планом
if (strlen($fulid)==11) { $frname=""; $fname="";
if (is_file("$datadir/$fid$id.dat")) { $lines=file("$datadir/$fid$id.dat"); $imax=count($lines); $i=$imax;
do {$i--; $dt=explode("|",$lines[$i]);
$dt[13]=str_replace(".jpg","",$dt[13]); $dt[13]=str_replace(".jpeg","",$dt[13]); $dt[13]=str_replace(".png","",$dt[13]); $dt[13]=str_replace(".gif","",$dt[13]);
if($dt[13]==$fulid) {$frtname=""; $fname="$dt[4] » $dt[3]";}
//if($dt[13]==$fulid) {$frtname="$dt[4] » $frtname"; $fname="$frtname";}
} while ($i>0); } }


if ($error==TRUE) {  // ЗАПРЕЩАЕМ ИНДЕКСАЦИЮ страниц с цитированием / УДАЛЁННЫЕ РАЗДЕЛЫ / ТЕМЫ!
$topurl="$fskin/top.html";
ob_start(); include $topurl; $topurl=ob_get_contents(); ob_end_clean();
$topurl=str_replace("<meta name=\"Robots\" content=\"index,follow\">",'<meta name="Robots" content="noindex,follow">',$topurl);
print"$topurl";
if (strlen($error)>1) exit("</td></tr></table><div align=center><br>Извините, но запрашиваем$error отсутствует.<br>
Рекомендую перейти на главную страницу фотоальбома по <a href='$furl'>этой ссылке</a>,<br>
и найти интересующее Вас фото.<br></div>
</td></tr></table></td></tr></table></td></tr></table>"); }
} // if (isset($_GET['id']))

if (strlen($error==FALSE)) 





include("$fskin/top.html"); //addtop();  // подключаем ШАПКУ форума




// выводим ГЛАВНУЮ СТРАНИЦУ ФОТОАЛЬБОМА


// Выводим все РУБРИКИ НА ГЛАВНОЙ
$adminmsg=""; if (is_file("$datadir/wrfoto.dat")) $lines=file("$datadir/wrfoto.dat");
if (!isset($lines)) $datasize=0; else $datasize=sizeof($lines);
if ($datasize<=0) {if (is_file("$datadir/copy.dat")) {$lines=file("$datadir/copy.dat"); $datasize=sizeof($lines);} $adminmsg="<font color=red><B>Администратор, внимание!!!</B> Файл БД с рубриками повреждён. Восстановите его из резервной копии в админке!</font><br>";}
if ($datasize<=0) exit("Проблемы с Базой данных - обратитесь к администратору");
$i=count($lines); $n="0"; $a1=-1; $u=$i-1; $fid="0"; $itogotem="0"; $itogomsg="0"; $alt=""; $konec="";

print"<TABLE border=0 cellSpacing=0 cellPadding=0 align=center width='100%'><TR><TD vAlign=top>
<table border=0 cellSpacing=0 cellPadding=0 width=100%><TR><TD width=270 valign=top>

<table width=98% height=450 border=0 cellSpacing=0 cellPadding=2><tr><td class=catHead colspan=2 height=18><span class=cattitle><h2 style='padding: 5px; margin: 1px'>Категории</h2></span></td></tr>";

$itogofoto=""; $a1=$a1+$rfid;
do {$a1++; $dt=explode("|", $lines[$a1]);
if (isset($dt[1])) { // Если строчка потерялась в скрипте (пустая строка) - то просто её НЕ выводим

if ($dt[1]=="razdel" and isset($_GET['razdel'])) {
$frname=str_replace('»','',$frname);
if($a1==$rfid) print"<tr><td class=row1 valign=top><span class=genmed><li><B>$frname</B></li></span></TD></TR>";
$konec++;}  else {

// определяем тип: топик или заголовок
if ($dt[1]=="razdel")
print "<tr><td class=row1 valign=top><span class=genmed><li><a href='index.php?razdel=$dt[0]'><B>$dt[2]</B></a></li></span></TD></TR>";
 else {
//$dt[9] - дата размещения сообщения; $wrftime2 - последнее посещение
// Если $dt[9] раньше (т.е. больше) $wrftime2 значит раздел форума - новый
//$foldicon="folder.gif"; if (isset($wrfname)) {if (isset($dt[9])) {if ($dt[9]>$wrftime2) $foldicon="foldernew.gif";}}

if (is_file("$datadir/$dt[3].dat")) { $msgsize=sizeof(file("$datadir/$dt[3].dat")); // считаем кол-во страниц в файле
if ($msgsize>$qq) $page=ceil($msgsize/$qq); else $page=1;} else $page=1;
if ($page!=1) $pageadd="&page=$page"; else $pageadd="";

if ($dt[7]==$date) $dt[7]="сегодня";
$maxzvezd=null; if (isset($dt[12])) { if ($dt[12]>=1) {$maxzvezd="*Доступна участникам, имеющим <font color=red><B>$dt[12]</B> звезд";
$dt[4]=""; $dt[5]="";
if ($dt[12]==1) $maxzvezd.="у";
if ($dt[12]==2 or $dt[12]==3 or $dt[12]==4) $maxzvezd.="ы";
$maxzvezd.=" минимум</font>";}}
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

print"<TR><TD align=center class=row1><span class=genmed><B>Всего фото: [ $itogofoto ]</B><br><br>";

if (is_file("baner_200x200.php")) include("baner_200x200.php");

/* Форма входа ==== Друзья сайта
print"<TABLE border=0 cellSpacing=0 cellPadding=0 align=center width='100%'><TR><TD vAlign=top>
<table border=0 cellSpacing=0 cellPadding=0 width=100%><TR><TD width=270 valign=top>
<table width=98% height=450 border=0 cellSpacing=0 cellPadding=2><tr><td class=catHead colspan=2 height=18><span class=cattitle><h2 style='padding: 5px; margin: 1px'>Категории</h2></span></td></tr>";
*/

//if ($_GET['event']=="login") { // ВХОД на форум УЧАСТНИКОМ

if (!isset($_GET['id'])) { // БЛОК выводится ТОЛЬКО на главной!

if (isset($_COOKIE['wrfcookies'])) addtop();

else echo '<table width=98% border=0 cellSpacing=0 cellPadding=2><tr><td class=catHead colspan=2 height=18><span class=cattitle>
<nobr><h2 style="padding: 5px; margin: 1px">Форма входа</h2></nobr></span></td></tr></table><br>
<FORM action="tools.php?event=regenter" method=post>
<TABLE cellPadding=1 cellSpacing=1 border=0>
<TR><TD>Логин:</TD><TD><INPUT name=name></TD></TR>
<TR><TD>Пароль:</TD><TD><INPUT type=password name=pass></TD></TR>
<TR><TD colspan=2><center><INPUT type=submit value="ВОЙТИ »"></TD></TR></TABLE></FORM>
<a href="tools.php?event=login">Забыли пароль? </a> &nbsp;&nbsp;&nbsp; <a href="tools.php?event=reg">Регистрация</a><BR><br>';




// Фото с самым высоким рейтингом
$fotorandom=TRUE; // Вынести в настройку - формировать колонку с самыми популярными по рейтину фото?

if ($fotorandom==TRUE) {
$timetek=time(); $timefile=0;
if (is_file("$datadir/ratingtop.dat")) $timefile=filemtime("$datadir/ratingtop.dat"); // Проверяем дату создания файла best.dat
$timer=$timetek-$timefile; // узнаем сколько прошло времени (в секундах) 

if ($timer>=43200) { // запускаем создание файла с рейтингом фото
if (is_file("$datadir/rating.dat")) { $ffile="$datadir/rating.dat"; $flines=file("$ffile"); $fi=count($flines);} // считываем рейтинг фото

if ($fi>10) { // Если есть хотябы 10 голосов
$ni=0; $c=0;
do {$fi--; $fdt=explode("|",$flines[$fi]);
$fdt[1]=$fdt[1]+1-1;
if ($fdt[1]>=1) {$newflines[$ni]="$fdt[2]"; $ni++;} // выбираем только фото с положительными оценками
} while($fi!=0);

if ($ni>2) {
$data="<table width=98% border=0 cellSpacing=0 cellPadding=2><tr><td class=catHead colspan=2 height=18><span class=cattitle>
<nobr><h2 style='padding: 5px; margin: 1px'>Фото с рейтингом</h2></nobr></span></td></tr></table><br>";
$uniq_foto=array_count_values($newflines); arsort($uniq_foto); reset($uniq_foto); $ni=0;
while ($ifoto = current($uniq_foto) and $c<=3) {if ($ifoto >=1) {$c++; $data.='<a class="gallery" rel="group" title="" href="'.$filedir.'/'.key($uniq_foto).'.jpg"><img src="'.$filedir.'/sm-'.key($uniq_foto).'.jpg" border=0></a><br><br>';} next($uniq_foto); }
reset($uniq_foto);
} // if ($ni>0)

} // if ($fi>10)

// запись данных в файл
$fp=fopen("$datadir/ratingtop.dat","w+");
flock ($fp,LOCK_EX);
fputs($fp, $data);
flock ($fp,LOCK_UN);
fclose($fp);

} //if ($timer>=43200) 

$ffile=file_get_contents("$datadir/ratingtop.dat"); print"$ffile";
} // if $fotorandom==TRUE;

} //$id - печатаем только на главной





if (is_file("$datadir/msg.dat")) { // БЛОК выводит рекламу, счётчики, информацию из файла msg.dat
echo '<table width=98% border=0 cellSpacing=0 cellPadding=2><tr><td class=catHead colspan=2 height=18><span class=cattitle>
<nobr><h2 style="padding: 5px; margin: 1px">Друзья сайта</h2></nobr></span></td></tr></table><br>
<TABLE cellPadding=1 cellSpacing=1 border=0><TR><TD>';
include("$datadir/msg.dat"); 
echo'</TD></TR></TABLE></FORM><BR><br>'; }




echo'</span></TD></TR>
</table></TD><TD valign=top>';






if (!isset($_GET['id'])) { // БЛОК выводится ТОЛЬКО на главной!


if (is_file("../baner_728x90.php")) { // БЛОК выводит рекламу

print"<table border=0 height=100 cellSpacing=0 cellPadding=3 width=100%>
<tr><td class=catHead colspan=3 width=100% height=28><span class=cattitle>
<h2 style='padding: 5px; margin: 1px'>Интересная информация</h2></span></td></tr>
<tr align=center><td class=row1 valign=top><span class=genmed>";
include("../baner_728x90.php"); echo'</span></TD></TR></table>';
} // БЛОК выводит рекламу




if (is_file("$datadir/news.dat")) { // БЛОК Недавно добавленные фото

$stolb=0; $maxfoto=9; // сколько недавно добавленных фото показывать?
$nlines=file("$datadir/news.dat"); $nmax=count($nlines); if ($nmax<$maxfoto) $maxfoto=$nmax;

if ($nmax>0) { // печатаем блок ОДИН РАЗ если есть фото
print"<table border=0 width=100% height=340 cellSpacing=0 cellPadding=3 width=100%>
<tr><td class=catHead colspan=3 width=100% height=28><span class=cattitle>
<h2 style='padding: 5px; margin: 1px'>Недавно добавленные фото</h2></span></td></tr>";

do { $maxfoto--;
$ndt=explode("|",$nlines[$maxfoto]);
$file=replacer($ndt[12]); $big_file=replacer($ndt[11]);
//$foto=str_replace('sm-','',$file); $foto=str_replace('.jpg','',$foto); $foto=str_replace('.png','',$foto);
if ($stolb==0) echo'<tr align=center>';
print "<td class=row1 width=33% valign=top align=center><span class=genmed>Категория: <B>$ndt[9]</B><br>
<a class='gallery' rel='group' title='$ndt[6]' href='$filedir/$big_file'><img border=0 src='$filedir/$file'></a><br>
Раздел: <a href='index.php?id=$ndt[1]'><B>$ndt[5]</B> »</a> <nobr>[$ndt[8] фото]</nobr></span></TD>";
$stolb++;
if ($stolb>=3) {echo'</TR>'; $stolb=0; }
} while($maxfoto>0);
echo'</table>';
} // if is_file

} // if ($nmax>0)


// БЛОК Случайно выбранные фото

$maxfoto=9; // сколько последних добавленных фото показывать?
$allfoto=null; $p=0; $stolb=0;

if ($handle=opendir($filedir)) { while (($file=readdir($handle))!==false)
if (!is_dir($file) and strstr($file,'sm-')) {$allfoto[$p]=$file; $p++;} closedir($handle);}

if (count($allfoto)>9) {
$r_keys=array_rand($allfoto,$maxfoto);
//echo $allfoto[$r_keys[0]] . "\n"; echo $allfoto[$r_keys[1]] . "\n"; exit;

print"<table border=0 height=340 cellSpacing=0 cellPadding=3 width=100%>
<tr><td class=catHead colspan=3 width=100% height=28><span class=cattitle>
<h2 style='padding: 5px; margin: 1px'>Случайно выбранные фото</h2></span></td></tr>";

do {$maxfoto--;
$file=$allfoto[$r_keys[$maxfoto]];
$rubrika=substr($file,3,7);
$foto=str_replace('sm-','',$file);
if ($stolb==0) echo'<tr align=center>';
print "<td class=row1 valign=top><span class=genmed>
<a class='gallery' rel='group' title='$fname' href='$filedir/$foto'><img border=0 src='$filedir/$file'></a><br>
<a href='index.php?id=$rubrika'>Перейти в раздел »</a></span></TD>";
$stolb++;
if ($stolb>=3) {echo'</TR>'; $stolb=0; }

} while($maxfoto>0);
echo'</table>';
} // if count($allfoto)>9


} // КОНЕЦ - вывода новых, случайных фоток и вывода блока рекламы
















if (isset($_GET['id'])) { $id=$_GET['id'];


// страница С ТЕМАМИ выбранной РУБРИКИ
if (strlen($id)==3) { $fid=$id;

// Защиты
if (!ctype_digit($fid) or strlen($fid)>3) exit("<B>$back. Номер рубрики должен быть цифровым и содержать менее 4 символов</B>");
$imax=count($mainlines); if (($fid>999) or (strlen($fid)==0)) exit("<b>Данный раздел удалён или не существует.</b>");

// Исключаем ошибку вызова несуществующей страницы
if (!isset($_GET['page'])) $page=1; else {$page=$_GET['page']; if (!ctype_digit($page)) $page=1; if ($page<1) $page=1;}

if ($raz!="razdel") {

// Уточняем статус по кол-ву ЗВЁЗД юзера. Если меньше допустимых N в этой рубрике - то досвиданья!
$maxzd=null;
do {$imax--; $ddt=explode("|", $mainlines[$imax]); if ($ddt[0]==$fid) $maxzd=$ddt[12]; } while($imax>"0");
if ($maxzd>=1) {
if (!ctype_digit($maxzd)) exit("$back звёзды исчисляются в цифрах. а в файле данных - ерунда!");

$noacsess="<br><br><br><br><center><table class=forumline width=700><tr><th class=thHead colspan=4 height=25>Доступ в раздел ограничен</th></tr>
<tr class=row2><td class=row1><center><BR><BR><B><span style='FONT-SIZE: 14px'>Для просмотра данного раздела необходимо быть зарегистрированным и иметь рейтинг не менее $maxzd звёзд.";

// БЛОК проверяет логин и пароль юзера, считывает кол-во его звёзд и сравнивает с заданным в рубрике
if (isset($_COOKIE['wrfcookies'])) { // весь блок работает при наличии КУКИ
$text=$_COOKIE['wrfcookies']; 
$text=replacer($text);
$wrfc=explode("|",$text); $wrfname=$wrfc[0]; if (isset($wrfc[1])) $wrfpass=$wrfc[1]; else exit("$back попытка взлома - в куки нет пароля. Куда он делся ;-) ?");

// пробегаем файл с юзерами
$iu=$usercount; $ok=FALSE;
do {$iu--; $du=explode("|",$userlines[$iu]);
if (isset($du[1])) { $realname=strtolower($du[0]);
if (strtolower($wrfname)===$realname & $wrfpass===$du[1]) {$ok="$i"; if ($du[2]<$maxzd) exit("$noacsess У Вас всего $du[2] звёзд.</B></center><BR><BR>$back<BR><BR></td></table><br>"); }}
} while($iu > "0");
} else exit("$noacsess</B></center><BR><BR>$back<BR><BR></td></table><br>"); // если юзер тот, за кого себя выдаёт то его пускаем, иначе - обломаем
if ($ok!=FALSE) exit("$noacsess</B></center><BR><BR>$back<BR><BR></td></table><br>");
}


// определяем есть ли информация в файле с данными
if (is_file("$datadir/topic$fid.dat")) {
$msglines=file("$datadir/topic$fid.dat"); $maxi=count($msglines); $i=$maxi;

if (isset($_POST['findme']) or isset($_GET['findme'])) {
// ЕСЛИ есть фильтр по названию темы, то:
// - Считываем файл с темами и отбираем в отдельный массив только те, которые содаржат в названии искомую фразу
// - в $maxi записываем кол-во тем
// - в $msglines[$i] записываем данные
setlocale(LC_ALL,'ru_RU.CP1251'); // ! РАЗРЕШАЕМ РАБОТУ ФУНКЦИЙ, работающих с регистором и с РУССКИМИ БУКВАМИ
if (isset($_POST['findme'])) $findme=replacer($_POST['findme']);
if (isset($_GET['findme'])) { $findme=replacer($_GET['findme']); $findme=urldecode($findme);}
$stroka=strlen($findme); if($stroka<4 or $stroka>30) exit("разрешается поиск в количестве от 4-х до 30-и символов!");
$tmplines=$msglines; $msglines=null; $i=0;
foreach($tmplines as $v) {$dt=explode("|", $v); if (stristr($dt[3],$findme)) {$msglines[$i]=$v; $i++;}}
$maxi=$i-1;} else $findme="";

$frname=str_replace(' »','',$frname); //вырезаем лишние символы
print"
<table width=100% border=0 cellSpacing=0 cellPadding=3 height=45><tr><td class=catHead colspan=2><span class=cattitle><h2 style='padding: 5px; margin: 1px'>$frname »</h2></span></td></tr></table>";

print"<table border=0 width=100% cellpadding=2 cellspacing=2 class=forumline>
<tr><td colspan=2 align=center valign=top>"; $temp=0;

if (is_file("../baner_728x90.php")) include("../baner_728x90.php"); // ВСТАВЛЯЕМ баннер

$addbutton="<table width=100%><tr><td align=left valign=middle>";

if ($maxi>0) {

if ($maxi>$maxtem-1) $addbutton="<table width=100%><TR><TD>Количество допустимых тем в рубрике исчерпано.";


// БЛОК СОРТИРОВКИ: последние ответы ВВЕРХУ (по времени создания файла с темой)!
do {$i--; $dt=explode("|", $msglines[$i]);
   $filename="$dt[7].dat"; if (is_file("$datadir/$filename")) $ftime=filemtime("$datadir/$filename");  else $ftime="";
   $newlines[$i]="$ftime|$dt[7]|$i|";
} while($i > 0);
usort($newlines,"prcmp");
// $newlines  - массив с данными:  ДАТА | ИМЯ_ФАЙЛА_С_ТЕМОЙ | № п/п |
// $msglines - массив со всеми темами выбранной рубрики
$i=$maxi;
do {$i--; $dtn=explode("|", $newlines[$i]);
   $numtp="$dtn[2]"; $lines[$i]="$msglines[$numtp]";
} while($i > 0);
// КОНЕЦ блока сортировки

// Показываем QQ ТЕМ
$fm=$maxi-$qq*($page-1);
if ($fm<"0") $fm=$qq; $lm=$fm-$qq; if ($lm<"0") $lm="0";

do {$fm--; $num=$fm+2;
$dt=explode("|", $lines[$fm]);

// нужно для определения темы на VIP-статус
$dtn=explode("|", $newlines[$fm]);
$timer=time()-$dtn[0]; // узнаем сколько прошло времени (в секундах) 


$filename=$dt[7]; 

if (is_file("$datadir/$filename.dat")) { // если файл с темой существует - то показать тему в списке!
$msgsize=sizeof(file("$datadir/$filename.dat"));

if ($temp>0) print"</TD><TD>"; if ($temp==0) print"<TR><TD>"; // открываем главную по отношению к 2-м по 400 таблицу

print"
<table border=0 width=390 cellpadding=2 cellspacing=1 class=forumline>
<TR valign=middle height=50><TD width=160 align=center valign=midle bgcolor=white>
<a href=\"index.php?id=$dt[7]\" title='$dt[3]'><img border=0 src='$filedir/$dt[17]'></a></TD>
<td class=row1 align=left height=130><span class=forumlink>";

$dt[3]=replacer($dt[3]);

print"<b><a href=\"index.php?id=$dt[7]\" title='$dt[3]'>$dt[3]</a></b> [$msgsize фото]</span>";

if ($msgsize>$qq) { // ВЫВОДИМ СПИСОК ДОСТУПНЫХ СТРАНИЦ ТЕМЫ
$maxpaget=ceil($msgsize/$qq); $addpage="";
echo'<small>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<div style="padding:6px;" class=pgbutt>Страница: ';
if ($maxpaget<=5) $f1=$maxpaget; else $f1=5;
for($i=1; $i<=$f1; $i++) {if ($i!=1) $addpage="&page=$i"; print"<a href=\"index.php?id=$dt[7]$addpage\">$i</a> &nbsp;";}
if ($maxpaget>5) print "... <a href=\"index.php?id=$dt[7]&page=$maxpaget\">$maxpaget</a>"; }

print"<br>$dt[4]<br><br><span class=gensmall>Добавил(а) ";

$codename=urlencode($dt[0]);
if ($dt[1]=="да") {
if (!isset($wrfname)) print "$dt[0]"; else print "<small>($users)</small> <a href='tools.php?event=profile&pname=$codename':$dt[2]>$dt[0]</a>";} else  print"<small> $dt[0]</small>";

print"<br><br><a name='addf' href=\"index.php?add=newfoto&id=$dt[7]\"><img src='$fskin/add_foto.gif' border=0></a>&nbsp;
</TD></TR></TABLE>";

$temp++; if ($temp==2) {$temp=0; print"</td></tr>";} // Нужно чтобы делить на два столбика

// защита if (strlen...) только если файл есть и имеет верный формат - выводим
if ($msgsize>=2) {$linesdat=file("$datadir/$filename.dat"); $dtdat=explode("|", $linesdat[$msgsize-1]);
if (strlen($linesdat[$msgsize-1])>10) {$dt[0]=$dtdat[0]; $dt[1]=$dtdat[1]; $dt[2]=$dtdat[2]; $dt[5]=$dtdat[5]; $dt[6]=$dtdat[6];}}
} //if is_file

} while($lm < $fm);

if ($stop!=TRUE) $addbutton.="<br><span class=nav><a name='add' href=\"index.php?add=newrazdel&id=$fid\"><img src='$fskin/add_razdel.gif' border=0></a>&nbsp;";
else $addbutton.="Извините за неудобство, но администратор временно приостановил добавление разделов и фотографий!";

// формируем переменную $pageinfo - со СПИСКОМ СТРАНИЦ
if (strlen($findme)>1) $findadd="&findme=$findme"; else $findadd="";
$pageinfo=""; $addpage=""; $maxpage=ceil(($maxi+1)/$qq); if ($page>$maxpage) $page=$maxpage;
$pageinfo.="<small><div style='padding:6px;' class=pgbutt>Страница: &nbsp;";
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
if ($cangutema=="0" and !isset($wrfname)) print"<center><h5>Администратор запретил создавать гостям темы! Для регистрации пройдите по ссылке: <B><a href='tools.php?event=reg'>зарегистрироваться</a></B></h5></center><BR><BR>"; else {
$maxzag=$maxzag-10; // так нужно!!!



if ($_GET['add']=="newrazdel") {
print"<form action=\"index.php?event=addtopic&id=$fid\" method=post enctype=\"multipart/form-data\" name=REPLIER>";
//else print"<form action=\"index.php?event=addanswer&id=$fid\" method=post enctype=\"multipart/form-data\" name=REPLIER>";


print"<table border=0 width=100% class=forumline>
<tr><td class=catHead align=center colspan=2 height=28><span class=cattitle>Добавление раздела</span></td></tr>
<tr><td class=row1 valign=top><span class=genmed><B>Название раздела</B></span></TD><TD class=row2>
<input type=hidden name=maxzd value='$maxzd'><input type=text class=post name=zag maxlength=$maxzag size=70></TD></TR>";
addmsg("");
} } }



}
} //if ($raz!="razdel")








// показываем ФОТО выбранной рубрики
if (strlen($id)>6) { $fid=substr($id,0,3);

// определяем есть ли информация в файле с данными
if (!is_file("$datadir/$id.dat")) exit("<BR>$back. Извините, но такой темы на форуме не существует.<BR> Скорее всего её удалил администратор.");
$lines=file("$datadir/$id.dat"); $mitogo=count($lines); $i=$mitogo; $maxi=$i-1;

if ($mitogo>0) { $tblstyle="row1";  $printvote=null;

// Ищем тему в topicХХ.dat - проверяем не закрыта ли тема? и сразу же ищем есть ли в топике
$ok=FALSE; $closed=FALSE; if (is_file("$datadir/topic$fid.dat")) {
$msglines=file("$datadir/topic$fid.dat"); $mg=count($msglines);
do {$mg--; $mt=explode("|",$msglines[$mg]);
if ($mt[7]==$id and $mt[8]=="closed") $closed=TRUE;
if ($mt[7]==$id) $ok=1; // тема есть в указанном разделе?
} while($mg > "0");}

$realbase="1"; if (is_file("$datadir/wrfoto.dat")) $mainlines=file("$datadir/wrfoto.dat");
if (!isset($mainlines)) $datasize=0; else $datasize=sizeof($mainlines);
if ($datasize<=0) {if (is_file("$datadir/copy.dat")) {$realbase="0"; $mainlines=file("$datadir/copy.dat"); $datasize=sizeof($mainlines);}}
if ($datasize<=0) exit("$back. Проблемы с Базой данных - обратитесь к администратору");
$i=count($mainlines);





$maxzd=null;


// Исключаем ошибку вызова несуществующей страницы
if (!isset($_GET['page'])) $page=1; else {$page=$_GET['page']; if (!ctype_digit($page)) $page=1; if ($page<1) $page=1;}

$fm=$qq*($page-1); if ($fm>$maxi) $fm=$maxi-$qq;
$lm=$fm+$qq; if ($lm>$maxi) $lm=$maxi+1;


// формируем переменную $pageinfo - со СПИСКОМ СТРАНИЦ
$pageinfo=""; $addpage=""; $maxpage=ceil(($maxi+1)/$qq); if ($page>$maxpage) $page=$maxpage;
$pageinfo.="<div align=center style='padding:6px;' class=pgbutt>Страница: &nbsp;";
if ($page>3 and $maxpage>5) $pageinfo.="<a href=index.php?id=$id>1</a> ... ";
$f1=$page+2; $f2=abs($page-2); if ($f2=="0") $f2=1; if ($page>=$maxpage-1) $f1=$maxpage;
if ($maxpage<=5) {$f1=$maxpage; $f2=1;}
for($i=$f2; $i<=$f1; $i++) { if ($page==$i) $pageinfo.="<B>$i</B> &nbsp;"; 
else {if ($i!=1) $addpage="&page=$i"; $pageinfo.="<a href=index.php?id=$id$addpage>$i</a> &nbsp;";} }
if ($page<=$maxpage-3 and $maxpage>5) $pageinfo.="... <a href=index.php?id=$id&page=$maxpage>$maxpage</a>";
$pageinfo.='</div>';

if (is_file("$datadir/rating.dat")) { // СЧИТЫВАЕМ ФАЙЛ С РЕЙТИНГОМ ФОТО В ПАМЯТЬ
$file="$datadir/rating.dat"; $flines=file("$file"); $j=count($flines); $maxflines=$j;}

$qm=null; $flag=0;
do {$dt=explode("|", replacer($lines[$fm]));

$youwr=null; $fm++; $num=$maxi-$fm+2; $status="";

if (strlen($lines[$fm-1])>5) { // Если строчка потерялась в скрипте (пустая строка) - то просто её НЕ выводим

if (isset($_GET['quotemsg'])) {$quotemsg=replacer($_GET['quotemsg']); if(ctype_digit($quotemsg) and $quotemsg==$fm) $qm="[Quote][b]$dt[0] пишет:[/b]\r\n".$dt[4]."[/Quote]";}

$msg=str_replace("[b]","<b>",$dt[4]);
$msg=str_replace("[/b]","</b>", $msg);
$msg=str_replace("[RB]","<font color=red><B>", $msg);
$msg=str_replace("[/RB]","</B></font>", $msg);
$msg=str_replace("&lt;br&gt;","<br>",$msg);
$msg=preg_replace("#\[Quote\]\s*(.*?)\s*\[/Quote\]#is","<br><B><U>Цитата:</U></B><table width=80% border=0 cellpadding=5 cellspacing=1 style='padding: 5px; margin: 1px'><tr><td class=quote>$1</td></tr></table>",$msg);
$msg=preg_replace("#\[Code\]\s*(.*?)\s*\[/Code\]#is"," <br><B><U>PHP код:</U></B><table width=80% border=0 cellpadding=5 cellspacing=1 style='padding: 5px; margin: 1px'><tr><td class=code >$1</td></tr></table>",$msg);

if ($antimat==TRUE) { // АНТИМАТ
$pattern="/\w{0,5}[хx]([хx\s\!@#\$%\^&*+-\|\/]{0,6})[уy]([уy\s\!@#\$%\^&*+-\|\/]{0,6})[ёiлeеюийя]\w{0,7}|\w{0,6}[пp]([пp\s\!@#\$%\^&*+-\|\/]{0,6})[iие]([iие\s\!@#\$%\^&*+-\|\/]{0,6})[3зс]([3зс\s\!@#\$%\^&*+-\|\/]{0,6})[дd]\w{0,10}|[сcs][уy]([уy\!@#\$%\^&*+-\|\/]{0,6})[4чkк]\w{1,3}|\w{0,4}[bб]([bб\s\!@#\$%\^&*+-\|\/]{0,6})[lл]([lл\s\!@#\$%\^&*+-\|\/]{0,6})[yя]\w{0,10}|\w{0,8}[её][bб][лске@eыиаa][наи@йвл]\w{0,8}|\w{0,4}[еe]([еe\s\!@#\$%\^&*+-\|\/]{0,6})[бb]([бb\s\!@#\$%\^&*+-\|\/]{0,6})[uу]([uу\s\!@#\$%\^&*+-\|\/]{0,6})[н4ч]\w{0,4}|\w{0,4}[еeё]([еeё\s\!@#\$%\^&*+-\|\/]{0,6})[бb]([бb\s\!@#\$%\^&*+-\|\/]{0,6})[нn]([нn\s\!@#\$%\^&*+-\|\/]{0,6})[уy]\w{0,4}|\w{0,4}[еe]([еe\s\!@#\$%\^&*+-\|\/]{0,6})[бb]([бb\s\!@#\$%\^&*+-\|\/]{0,6})[оoаa@]([оoаa@\s\!@#\$%\^&*+-\|\/]{0,6})[тnнt]\w{0,4}|\w{0,10}[ё]([ё\!@#\$%\^&*+-\|\/]{0,6})[б]\w{0,6}|\w{0,4}[pп]([pп\s\!@#\$%\^&*+-\|\/]{0,6})[иeеi]([иeеi\s\!@#\$%\^&*+-\|\/]{0,6})[дd]([дd\s\!@#\$%\^&*+-\|\/]{0,6})[oоаa@еeиi]([oоаa@еeиi\s\!@#\$%\^&*+-\|\/]{0,6})[рr]\w{0,12}/i";
$msg=preg_replace("$pattern","<b><font color='red'>Цензура</font></b>",$msg); }

if ($smile==TRUE) { // СМАЙЛИКИ
$i=count($smiles)-1; for($k=0; $k<$i; $k=$k+2)
{$j=$k+1; $msg=str_replace("$smiles[$j]","<img src='smile/$smiles[$k].gif' border=0>",$msg);}}

// Если разрешена публикация УРЛов
if ($liteurl==TRUE) $msg=preg_replace ("#([^\[img\]])(http|https|ftp|goper):\/\/([a-zA-Z0-9\.\?&=\;\-\/_]+)([\W\s<\[]+)#i", "\\1<a href=\"\\2://\\3\" target=\"_blank\">\\2://\\3</a>\\4", $msg);

// запускать ТОЛЬКО после замены АДРЕСА URL!!!
$msg=preg_replace('#\[img\](.+?)\[/img\]#','<img src="$1" border="0">',$msg);

// считываем в память данные по пользователю
if ($dt[1]=="да")  { $iu=$usercount; $predup="0";
do {$iu--; $du=explode("|", $userlines[$iu]); if ($du[0]==$dt[0])
{ if (isset($du[12])) {$status=$du[13]; $reiting=$du[2]; $youavatar=$du[12]; $email=$du[3]; $icq=$du[7]; $site=$du[8]; $userpn=$iu;}
if (isset($_COOKIE['wrfcookies'])) $youwr=preg_replace("#(\[url=([^\]]+)\](.*?)\[/url\])|(http://(www.)?[0-9a-z\.-]+\.[a-z]{2,6}[0-9a-z/\?=&\._-]*)#","<a href=\"$4\" target='_blank'>$4</a> ",$du[11]); else $youwr=$du[11];}
} while($iu > "0");
}

if ($tblstyle=="row1") $tblstyle="row2"; else $tblstyle="row1";

if ($flag==FALSE) { // БЛОК ПЕЧАТАЕМ ОДИН РАЗ
$frname=str_replace(' »','',$frname); $frtname=str_replace(' »','',$frtname); //вырезаем лишние символы
$flag=TRUE; print "
<table width=100% border=0 cellSpacing=0 cellPadding=3 height=45><tr><td class=catHead colspan=2>
<span class=cattitle><h2 style='padding: 5px; margin: 1px'><a href=\"index.php?id=$fid\">$frname</a> »
$frtname</h2></span>
</td></tr></table>";

if (is_file("../baner_728x90.php")) include("../baner_728x90.php"); // ВСТАВЛЯЕМ баннер

print"<table border=0 class=forumline width=800 cellspacing=1 cellpadding=3><tr><div id='wrap'>";}

$teknum=$fm;

print"<td valign=top> 
<table border=0 align=center width=190 cellpadding=1 cellspacing=0 class=maintbl>
<tr><td valign=top align=center class=row1>

<font size=-1>Фото № $teknum</font><BR>
<table width=190 height=180 cellpadding=0 cellspacing=0><tr><td align=center height=120 colspan=2><br>";

// Если ПРИКРЕПЛЁН ФАЙЛ к сообщению - то показываем значёк и ссылку на него или картинку
if (isset($dt[12])) { if ($dt[12]!="" and is_file("$filedir/$dt[13]")) {
$fsize=round($dt[14]/10.24)/100;
$fotoname=explode(".",$dt[13]);


// Если есть файл с рейтингом фото, то ищем все оценки этой фото и печатаем средний бал
$j=$maxflines; $sbal=null; $itogo=null;
if ($j>0) do {$j--; $fdt=explode("|",$flines[$j]);
if ($fdt[2]==$fotoname[0]) {$itogo++; $sbal=$sbal+$fdt[1];}
} while($j>0);
if ($itogo!=null) $sbal=round($sbal/$itogo,2); else $rating="";
if ($sbal>0) $rating="<font color=green><B>$sbal</B></font>";
if ($sbal<0) $rating="<font color=red>$sbal</font>";





// НОВЫЙ вариант
$dt[20]=round(($dt[20]/1024),2); if ($dt[20]=="0") $dt[20]="0.01";
print"<a class=\"gallery\" rel=\"group\" title=\"$msg\" href=\"$filedir/$dt[13]\"><img src=\"$filedir/$dt[17]\" alt='$msg'/></a>
<br><B>$msg</B><br><br>
Разрешение: $dt[18] x $dt[19]<br>
Размер: <I>$dt[20] Мб.</I><br>

<br>Рейтинг: $rating <A href='#m$fm' style='text-decoration:none' onclick=\"window.open('index.php?addrepa&fotoname=$fotoname[0]','repa','width=600,height=600,left=50,top=50,scrollbars=yes')\">&#177;</A><br><br>
";
}}

$addpage=""; if ($page>1) $addpage="&page=$page"; // нужно для цитирования
print"<br>";

/* ПОКА что отключаем БЛОК ГОЛОСОВАНИЯ!!!!!
if (is_file("$datadir/$id.dat"))  {
$rlines=file("$datadir/$id.dat"); $ri=count($rlines); $bals=0; $all=0;
print"<TR><TD colspan=2 align=center>Комментарии [<B> $ri </B>]</TD></TR>";
do {$ri--; $edt=explode("|",$rlines[$ri]); $edt[3]=date("d.m.Y H:i:s",$edt[3]); if ($edt[4]!=0) {$bals=$bals+$edt[4]; $all++;} else {$edt[4]="-";} } while($ri>0);
if ($bals==0) {$itogobals="+</B>";} else {$itogobals=round($bals*10/$all)/10; $itogobals.="</B>";}
print "<TR><TD colspan=2 align=center>Оценка [<B><a href='index.php?event=formacoment&id=$id' class=gallery>$itogobals</a>]</TD></TR>";
} else {print"<TR><TD colspan=2 align=center>Комментарии [ <B><a href='index.php?event=formacoment&id=$id' class=gallery>+</a></B> ]</TD></TR>
<TR><TD colspan=2 align=center>Оценка [<B><a href='index.php?event=formacoment&id=$id' class=gallery>+</a></B>]</TD></TR>
";}
*/


if ($dt[2]!="") $dt[2]="<a href='mailto:$dt[2]' class=gallery>$dt[1]</a>"; else $dt[2]="$dt[0]";
print"<TR height=30><TD><i>$dt[2]</i></TD><TD align=right>
<small><i>$dt[5]</i><br>$dt[6]</small></td></tr>
</table>
</td></tr></table></div>
</td>";

$colrubperpage=3;
$cm=1; // ДЕЛИМ ВСЕ РУБРИКИ на столбцы
if ((round($fm/$colrubperpage))==($fm/$colrubperpage)) {$cm++; print "</TR><TR>";}


} // если строчка потерялась
} while($fm < $lm);

print" </tr></table> $pageinfo";


if ($cangumsg==FALSE and !isset($wrfname)) {print"<center>Администратор запретил отвечать гостям на сообщения! Для регистрации пройдите по ссылке: <B><a href='tools.php?event=reg'>зарегистрироваться</a></B></center><BR><BR>"; } else {
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
<tr><th class=thHead colspan=2 height=25><b>Добавление ФОТО <a href='index.php?loginza'>*</a></b></th></tr>";

addmsg($qm);
} else echo'<center>Извините за неудобство, но администратор временно приостановил добавление разделов и фотографий!';
} else echo'<center><font style="font-size: 16px;font-weight:bold;"><BR>Раздел закрыт для добавления фото!<BR><BR>';
}}

}
} // if isset($id)







if (is_file("$fskin/bottom.html")) include("$fskin/bottom.html");  // подключаем НИЖНИЙ БЛОК ФОТОАЛЬБОМА

print"</td></tr></table>
<center><font size=-2><small>Powered by WR-Foto &copy; 1.1М.2015<br></small></font></center>";

if (is_file("../bottom.html")) include ("../bottom.html");

?>
