<? // WR-foto v 1.2  //  02.08.15 г.  //  Miha-ingener@yandex.ru

error_reporting (E_ALL); //error_reporting(0);
ini_set('register_globals','off');// Все скрипты написаны для этой настройки php

include "data/config.php";

$skey="657567"; // !!! Секретный ключ !!! 
// Поменяйте на свой и фиг кто вскроет админку :-)
// !!! ПОСЛЕ СМЕНЫ - пароли администратора и модератора становятся ошибочными!
// для получения нового пароля разкоменируйте строку примерно 89:
//$qq=md5("$pass+$skey"); print"$qq"; exit; // РАЗБЛОКИРУЙТЕ для получения MD5 своего пароля!с
// вставьте полученный код в config.php В ПЕРЕМЕННЫЕ $password и $moderpass

// Авторизация
$adminname="admin|moder|"; // НЕ МЕНЯЙТЕ покамисть!!! Ещё тестирую. имя администратора и через знак | имя модератора и в конце |
$adminpass=$password;


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


function unreplacer ($text) { // ФУНКЦИЯ замены спецсимволов конца строки на обычные
$text=str_replace("&lt;br&gt;","<br>",$text);
$text=str_replace("&#124;","|",$text);
return $text;}


function nospam() { global $max_key,$rand_key; // Функция АНТИСПАМ
if (array_key_exists("image", $_REQUEST)) { $num=replacer($_REQUEST["image"]);
for ($i=0; $i<10; $i++) {if (md5("$i+$rand_key")==$num) {imgwr($st,$i); die();}} }
$xkey=""; mt_srand(time()+(double)microtime()*1000000);
$dopkod=mktime(0,0,0,date("m"),date("d"),date("Y")); // доп.код: меняется каждые 24 часа
$stime=md5("$dopkod+$rand_key");// доп.код
echo'Защитный код: ';
for ($i=0; $i<$max_key; $i++) {
$snum[$i]=mt_rand(0,9); $psnum=md5($snum[$i]+$rand_key+$dopkod);
echo "<img src=antispam.php?image=$psnum border='0' alt=''>\n";
$xkey=$xkey.$snum[$i];}
$xkey=md5("$xkey+$rand_key+$dopkod"); //число + ключ из config.php + код меняющийся кажые 24 часа
print" <input name='usernum' class=post type='text' style='WIDTH: 70px;' maxlength=$max_key size=6>
<input name=xkey type=hidden value='$xkey'>
<input name=stime type=hidden value='$stime'>";
return; }


// Выбран ВЫХОД - очищаем куки
if(isset($_GET['event'])) { if ($_GET['event']=="clearcooke") { setcookie("wrforumm","",time()-3600); Header("Location: index.php"); exit; } }

if (isset($_COOKIE['wrforumm'])) { // Сверяем имя/пароль из КУКИ с заданным в конфиг файле
$text=$_COOKIE['wrforumm'];
$text=trim($text); // Вырезает ПРОБЕЛьные символы 
if (strlen($text)>60) exit("Попытка взлома - длина переменной куки сильно большая!");
$text=replacer($text);
$exd=explode("|",$text); $name1=$exd[0]; $pass1=$exd[1];
$adminname=explode("|",$adminname);

if ($name1!=$adminname[0] and $name1!=$adminname[1] or $pass1!=$adminpass) 
{sleep(1); setcookie("wrforumm", "0", time()-3600); Header("Location: admin.php"); exit;} // убаваем НЕВЕРНУЮ КУКУ!!!

} else { // ЕСЛИ ваще нету КУКИ

if (isset($_POST['name']) & isset($_POST['pass'])) { // Если есть переменные из формы ввода пароля
$name=str_replace("|","I",$_POST['name']); $pass=str_replace("|","I",$_POST['pass']);
$text="$name|$pass|";
$text=trim($text); // Вырезает ПРОБЕЛьные символы 
if (strlen($text)<4) exit("$back Вы не ввели имя или пароль!");
$text=replacer($text);
$exd=explode("|",$text); $name=$exd[0]; $pass=$exd[1];

//$qq=md5("$pass+$skey"); print"$qq"; exit; // РАЗБЛОКИРУЙТЕ для получения MD5 своего пароля!

//--А-Н-Т-И-С-П-А-М--проверка кода--
if ($antispam==TRUE) {
if (!isset($_POST['usernum']) or !isset($_POST['xkey']) or !isset($_POST['stime']) ) exit("данные из формы не поступили!");
$usernum=replacer($_POST['usernum']); $xkey=replacer($_POST['xkey']); $stime=replacer($_POST['stime']);
$dopkod=mktime(0,0,0,date("m"),date("d"),date("Y")); // доп.код. Меняется каждые 24 часа
$usertime=md5("$dopkod+$rand_key");// доп.код
$userkey=md5("$usernum+$rand_key+$dopkod");
if (($usertime!=$stime) or ($userkey!=$xkey)) exit("введён ОШИБОЧНЫЙ код!");}


// Сверяем введённое имя/пароль с заданным в конфиг файле
$adminname=explode("|",$adminname);
// АДМИНИСТРАТОРУ присваиваются куки
if ($name==$adminname[0] & md5("$pass+$skey")==$adminpass) 
{$tektime=time(); $wrforumm="$adminname[0]|$adminpass|$tektime|";
setcookie("wrforumm", $wrforumm, time()+18000); Header("Location: admin.php"); exit;}
// МОДЕРАТОРУ присваиваются куки
if ($name==$adminname[1] & md5("$pass+$skey")==$moderpass) 
{$tektime=time(); $wrforumm="$adminname[1]|$adminpass|$tektime|";
setcookie("wrforumm", $wrforumm, time()+18000); Header("Location: admin.php"); exit;}

exit("Ваши данные <B>ОШИБОЧНЫ</B>!</center>");

} else { // если нету данных, то выводим ФОРМУ ввода пароля

echo "<html><head><META HTTP-EQUIV='Pragma' CONTENT='no-cache'><META HTTP-EQUIV='Cache-Control' CONTENT='no-cache'><META content='text/html; charset=windows-1251' http-equiv=Content-Type><style>input, textarea {font-family:Verdana; font-size:12px; text-decoration:none; color:#000000; cursor:default; background-color:#FFFFFF; border-style:solid; border-width:1px; border-color:#000000;}</style></head><body>
<BR><BR><BR><center>
<table border=#C0C0C0 border=1  cellpadding=3 cellspacing=0 bordercolor=#959595>
<form action='admin.php' method=POST name=pswrd>
<TR><TD bgcolor=#C0C0C0 align=center>Администрирование форума</TD></TR>
<TR><TD align=right>Введите логин: <input size=17 name=name value=''></TD></TR>
<TR><TD align=right>Введите пароль: <input type=password size=17 name=pass></TD></TR>
<TR><TD align=right>";

if ($antispam==TRUE) nospam(); // АНТИСПАМ !

print"<TR><TD align=center><input type=submit style='WIDTH: 120px; height:20px;' value='Войти'>
<SCRIPT language=JavaScript>document.pswrd.name.focus();</SCRIPT></TD></TR></table>
<BR><BR><center><small>Powered by <a href=\"http://www.wr-script.ru\" title=\"Скрипт фотоальбома\" class='copyright'>WR-Foto</a> 1.1<br></small></center></body></html>";
exit;}

} // АВТОРИЗАЦИЯ ПРОЙДЕНА!

$gbc=$_COOKIE['wrforumm']; $gbc=explode("|", $gbc); $gbname=$gbc[0];$gbpass=$gbc[1];$gbtime=$gbc[2];







// Добавление IP-юзера в БАН
if (isset($_GET['badip']))  {
if (isset($_POST['ip'])) {$ip=$_POST['ip']; $badtext=$_POST['text'];}
if (isset($_GET['ip_get'])) {$ip=$_GET['ip_get']; $badtext="За добавление нежелательных сообщений на форум! ЗА СПАМ!!!";}
if (strlen($ip)<8) exit("Введите IP по формату X.X.X.X, где Х - число от 1 до 255! Сейчас запрос пуст или IP НЕ указан!");
$text="$ip|$badtext|"; $text=stripslashes($text); $text=htmlspecialchars($text); $text=str_replace("\r\n", "<br>", $text);
$fp=fopen("$datadir/bad_ip.dat","a+");
flock ($fp,LOCK_EX);
fputs($fp,"$text\r\n");
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);
Header("Location: admin.php?event=blockip"); exit; }



// Удаления IP-юзера из БАНА
if (isset($_GET['delip']))  { $xd=$_GET['delip'];
$file=file("$datadir/bad_ip.dat"); $dt=explode("|",$file[$xd]); 
$fp=fopen("$datadir/bad_ip.dat","w");
flock ($fp,LOCK_EX);
for ($i=0;$i< sizeof($file);$i++) { if ($i==$xd) unset($file[$i]); }
fputs($fp, implode("",$file));
flock ($fp,LOCK_UN);
fclose($fp);
Header("Location: admin.php?event=blockip"); exit; }









// Блок ПЕРЕСЧЁТА кол-ва тем и сообщений -------------------- не работает, ДОДЕЛАТЬ!!!!!!!!!!!!!
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
// сохраняем обновлённые данные о кол-ве тем и сообщений в файле
$file=file("$datadir/wrfoto.dat");
$fp=fopen("$datadir/wrfoto.dat","w");
flock ($fp,LOCK_EX); 
for ($i=0;$i< sizeof($file);$i++) fputs($fp,$lines[$i]);
flock ($fp,LOCK_UN);
fclose($fp);

print "<center><BR><BR><BR>Всё успешно пересчитано.</center><script language='Javascript'><!--
function reload() {location = \"admin.php\"}; setTimeout('reload()', 1000);
--></script>";
exit; }}






// Блок ПЕРЕМЕЩЕНИЯ ВВЕРХ/ВНИЗ РАЗДЕЛА или ТОПИКА
if(isset($_GET['movetopic'])) { if ($_GET['movetopic'] !="") {
$move1=$_GET['movetopic']; $where=$_GET['where']; 
if ($where=="0") $where="-1";
$move2=$move1-$where;
$file=file("$datadir/wrfoto.dat"); $imax=sizeof($file);
if (($move2>=$imax) or ($move2<"0")) exit(" НИЗЯ туда двигать!");
$data1=$file[$move1]; $data2=$file[$move2];

$fp=fopen("$datadir/wrfoto.dat","a+");
flock ($fp,LOCK_EX); 
ftruncate ($fp,0);//УДАЛЯЕМ СОДЕРЖИМОЕ ФАЙЛА 
// меняем местами два соседних раздела
for ($i=0; $i<$imax; $i++) {if ($move1==$i) fputs($fp,$data2); else  {if ($move2==$i) fputs($fp,$data1); else fputs($fp,$file[$i]);}}
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);
Header("Location: admin.php"); exit; }}




// Блок УДАЛЕНИЯ выбранного РАЗДЕЛА или ФОРУМА
if(isset($_GET['fxd'])) {
$id=replacer($_GET['fxd']); if ($id=="" or strlen($id)>3) exit("Ошибка, выбирите рубрику для удаления, либо ошибка скрипта!");

// считываем все файлы в папке data попорядку, удалем те, которые начинаются на $id,
// (файлы с темами, голосованием -vote, IP-шниками голосования -ip, topic$id - в темами)
if ($handle=opendir($datadir)) {
while (($file = readdir($handle)) !== false)
if (!is_dir($file)) { 
$tema=substr($file,0,3);
if($tema==$id) unlink ("$datadir/$file");
if($file=="topic$id.dat") unlink ("$datadir/topic$id.dat");
} closedir($handle); } else echo'Ошибка!';

// удаляем строку, соответствующую теме в файле со всеми темами
$file=file("$datadir/wrfoto.dat");
$fp=fopen("$datadir/wrfoto.dat","w");
flock ($fp,LOCK_EX);
for ($i=0;$i< sizeof($file);$i++) {$dt=explode("|",$file[$i]); if ($dt[0]==$id) unset($file[$i]);}
fputs($fp, implode("",$file));
flock ($fp,LOCK_UN);
fclose($fp);
Header("Location: admin.php"); exit; }









// Блок удаления выбранной ТЕМЫ
if (isset($_GET['xd'])) { if ($_GET['xd'] !="") {
if (isset($_GET['page'])) $page=$_GET['page']; else $page="0";
$xd=$_GET['xd']; $fid=$_GET['fid']; $id=$_GET['id'];
$file=file("$datadir/topic$fid.dat");

$minmsg=1; $delf=null; if (isset($file[$xd])) {
$dt=explode("|", $file[$xd]);
$delf = str_replace("\r\n", "", $dt[7]);
$mlines=file("$datadir/$delf.dat"); $minmsg=count($mlines);
unlink ("$datadir/$delf.dat");} // удаляем файл с темой
if (is_file("$datadir/$delf-vote.dat")) unlink("$datadir/$delf-vote.dat"); // удаляем файл с ГОЛОСОВАНИЕМ
if (is_file("$datadir/$delf-ip.dat")) unlink("$datadir/$delf-ip.dat"); // удаляем файл с голосовавшими IP

// удаляем строку, соответствующую теме в файле с текущими темами
$fp=fopen("$datadir/topic$fid.dat","w");
$kolvotem=sizeof($file)-1; // кол-во тем для уточнения на главной
flock ($fp,LOCK_EX); 
for ($i=0;$i< sizeof($file);$i++) {if ($i==$xd) unset($file[$i]);}
fputs($fp, implode("",$file));
flock ($fp,LOCK_UN);
fclose($fp);


// Блок вычитает 1-цу из кол-ва тем и вычитает кол-во сообщений
$lines = file("$datadir/wrfoto.dat"); $i=count($lines);
// находим по fid номер строки
for ($ii=0;$ii< sizeof($lines);$ii++) {$kdt=explode("|",$lines[$ii]); if ($kdt[0]==$fid) $mnumer=$ii;}
$dt=explode("|",$lines[$mnumer]);
$dt[5]=$dt[5]-$minmsg;
if ($kolvotem=="0") $dt[5]="0";
if ($dt[5]<0) $dt[5]="0";
if ($dt[4]<0) $dt[4]="0";
// если удаляемая тема стоит на главной как последняя, то удаляем её с главной
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


// удаляем объявление из 10-КИ последних
$file=file("$datadir/news.dat");
$fp=fopen("$datadir/news.dat","w");
flock ($fp,LOCK_EX);
for ($i=0; $i< sizeof($file); $i++) { $dt=explode("|",$file[$i]); if ($dt[1]==$id) unset($file[$i]); }
fputs($fp, implode("",$file));
flock ($fp,LOCK_UN);
fclose($fp);


Header("Location: admin.php?fid=$fid&page=$page"); exit; } }





// Блок УДАЛЕНИЯ выбранного ФОТО
if (isset($_GET['topicxd'])) { if ($_GET['topicxd'] !="") {
$fid=$_GET['fid']; $id=$_GET['id']; $topicxd=$_GET['topicxd']-1;
if (isset($_GET['page'])) $page=$_GET['page']; else $page="1";
$file=file("$datadir/$id.dat");
if (count($file)==1) exit("В ТЕМЕ должно остаться хотябы <B>одно сообщение!</B>");
$fp=fopen("$datadir/$id.dat","w");
flock ($fp,LOCK_EX);
for ($i=0;$i< sizeof($file);$i++) { if ($i==$topicxd) unset($file[$i]); }
fputs($fp, implode("",$file));
flock ($fp,LOCK_UN);
fclose($fp);
$topicxd--;

$file=file("$datadir/$id.dat");
//переписываем автора последнего сообщения в теме
$dt=explode("|",$file[count($file)-1]); $avtor=$dt[0]; $data=$dt[5]; $time=$dt[6];


// Блок вычитает 1-цу из кол-ва сообщений на главной
$lines = file("$datadir/wrfoto.dat"); $i=count($lines);
// находим по fid номер строки
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






// Добавление ФОРУМА / РАЗДЕЛА

if(isset($_GET['event'])) { if ($_GET['event'] =="addmainforum") {
$ftype=$_POST['ftype']; $zag=$_POST['zag']; $msg=$_POST['msg']; $id="101";
if ($zag=="") exit("$back <B>и введите заголовок!</B>");

// пробегаем по файлу с номерами разделов/топиков - ищем наибольшее и добавляем +1
if (is_file("$datadir/wrfoto.dat")) { $lines=file("$datadir/wrfoto.dat"); 
$imax=count($lines); $i=0;
do {$dt=explode("|", $lines[$i]); if ($id<$dt[0]) {$id=$dt[0];} $i++; } while($i<$imax);
$id++; }
if ($id<101) $id=101; if ($id>999) exit("Номер не может быть более 999");
$zag=str_replace("|","I",$zag); $msg=str_replace("|","I",$msg);
if ($ftype=="") $record="$id|$zag|$msg||0|0||$date|$time||||||"; else $record="$id|$ftype|$zag|";
$record=replacer($record);

// создаём пустой файл с рубриками
if ($ftype=="") { $fp=fopen("$datadir/topic$id.dat","a+");
flock ($fp,LOCK_EX); fputs($fp,""); fflush ($fp); flock ($fp,LOCK_UN); fclose($fp); }

// запись данных на главную страницу
$fp=fopen("$datadir/wrfoto.dat","a+");
flock ($fp,LOCK_EX); fputs($fp,"$record\r\n"); fflush ($fp); flock ($fp,LOCK_UN); fclose($fp);
Header("Location: admin.php"); exit; }







// Редактирование ФОРУМА / РАЗДЕЛА
if ($_GET['event'] =="frdmainforum") {
$nextnum=$_POST['nextnum'];
$frd=$_POST['frd'];
$ftype=$_POST['ftype'];
$zag=$_POST['zag'];
if ($zag=="") exit("$back <B>и введите заголовок!</B>");
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
ftruncate ($fp,0);//УДАЛЯЕМ СОДЕРЖИМОЕ ФАЙЛА 
for ($i=0;$i< sizeof($file);$i++) { if ($frd!=$i) fputs($fp,$file[$i]); else fputs($fp,"$txtmf\r\n"); }
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);
Header("Location: admin.php"); exit; }




if ($_GET['event']=="rdtema") { // Выбрано редактирование ТЕМЫ
$rd=$_POST['rd']; // - номер ячейки, которую необходимо заменить
$fid=$_POST['fid']; $changefid=$_POST['changefid'];
if (isset($_GET['page'])) $page=$_GET['page']; else $page="0";

$name=$_POST['name']; $who=$_POST['who']; $email=$_POST['email'];
$zag=$_POST['zag']; $msg=$_POST['msg']; $datem=$_POST['datem'];
$timem=$_POST['timem']; $id=$_POST['id']; $timetk=$_POST['timetk'];
$status=$_POST['status']; $goto=$_POST['goto'];

if ($goto==1) $goto="admin.php?fid=$changefid"; else $goto="admin.php?fid=$fid&page=$page";

if ($zag=="") exit("$back <B>и введите ТЕМУ, она пустая!</B>");
$text="$name|$who|$email|$zag|$msg|$datem|$timem|$id|$status|$timetk|";
$text=htmlspecialchars($text); $text=stripslashes($text); $text=str_replace("\r\n","<br>",$text);


if ($changefid==$fid) { // Если рубрика остаётся тамже
$file=file("$datadir/topic$fid.dat");
$fp=fopen("$datadir/topic$fid.dat","a+");
flock ($fp,LOCK_EX); 
ftruncate ($fp,0);//УДАЛЯЕМ СОДЕРЖИМОЕ ФАЙЛА 
for ($i=0;$i<sizeof($file);$i++) { if ($rd!=$i) fputs($fp,$file[$i]); else fputs($fp,"$text\r\n"); }
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);

} else { // если меняем рубрику теме

// $fid - текущий, а  $changefid - это новый фид топика.

// 1. создаём копию темы в новом топике

touch("$datadir/topic$changefid.dat");
$file=file("$datadir/topic$changefid.dat");
$kolvotem1=sizeof($file)+1; // кол-во тем для уточнения на главной
$fp=fopen("$datadir/topic$changefid.dat","a+");
flock ($fp,LOCK_EX);
fputs($fp,"$text\r\n");
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);

// 2. удаляем тему в текущем топике

touch("$datadir/topic$fid.dat");
$file=file("$datadir/topic$fid.dat");
$fp=fopen("$datadir/topic$fid.dat","w+");
$kolvotem2=sizeof($file)-1; // кол-во тем для уточнения на главной
flock ($fp,LOCK_EX); 
for ($i=0;$i< sizeof($file);$i++) { if ($i==$rd) unset($file[$i]); }
fputs($fp, implode("",$file));
flock ($fp,LOCK_UN);
fclose($fp);

// 3. запускаем пересчёт по типу как в доске объявлений

// ДОДЕЛАТЬ в следующей версии!!!
// СЛЕДУЮЩИЕ два блока объединить в один. Сделать переход по массиву,
// корректирование и копирование данных в новый массив
// и последующая его запись в файл wrfoto.dat

// Блок вычитает 1-цу из кол-ва тем и вычитает кол-во сообщений
$file=file("$datadir/$id.dat"); $minmsg=count($file);
$lines=file("$datadir/wrfoto.dat"); $i=count($lines);
// находим по $changefid номер строки

for ($ii=0;$ii< sizeof($lines);$ii++) { $kdt=explode("|",$lines[$ii]); if ($kdt[0]==$fid) $mnumer=$ii; }
$dt=explode("|",$lines[$mnumer]);
$dt[5]=$dt[5]-$minmsg;
if ($kolvotem2=="0") $dt[5]="0";
if ($dt[5]<0) $dt[5]="0";
if ($dt[4]<0) $dt[4]="0";
// если удаляемая тема стоит на главной как последняя, то удаляем её с главной
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

// Блок прибавляет 1-цу к кол-ву тем и добавляет кол-во сообщений
for ($ii=0;$ii< sizeof($lines);$ii++) { $kdt=explode("|",$lines[$ii]); if ($kdt[0]==$changefid) $mnumer=$ii; }
$dt=explode("|",$lines[$mnumer]);
$dt[5]=$dt[5]+$minmsg;
if ($kolvotem1=="0") $dt[5]="0";
if ($dt[5]<0) $dt[5]="0";
if ($dt[4]<0) $dt[4]="0";
// если удаляемая тема стоит на главной как последняя, то удаляем её с главной
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

// 4. смотрим news.dat. Если есть удаляем нафиг (удаляем объявление из 10-КИ последних)

$file=file("$datadir/news.dat");
$fp=fopen("$datadir/news.dat","w");
flock ($fp,LOCK_EX);
for ($i=0; $i< sizeof($file); $i++) {
$dt=explode("|",$file[$i]); if ($dt[1]==$id) unset($file[$i]); }
fputs($fp, implode("",$file));
flock ($fp,LOCK_UN);
fclose($fp);
Header("Location: $goto"); exit; }


// Заносим новое название рубрики в каждую строку файла с сообщениями
$linesrdt=file("$datadir/$id.dat");
$fp=fopen("$datadir/$id.dat","a+");
flock ($fp,LOCK_EX);
ftruncate ($fp,0);//УДАЛЯЕМ СОДЕРЖИМОЕ
for ($i=0;$i< sizeof($linesrdt);$i++) {$drdt=explode("|", $linesrdt[$i]); $text1="$drdt[0]|$drdt[1]|$drdt[2]|$zag|$drdt[4]|$drdt[5]|$drdt[6]|$drdt[7]|$drdt[8]|$drdt[9]|"; $text1=str_replace("\r\n", "", $text1); fputs($fp,"$text1\r\n");}
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);
// если нужно заVIPить тему - прибавляем её ещё 2 года к сегодняшнему дню
if ($_POST['viptema']==="1") { $viptime=strtotime("+2 year"); touch("$datadir/$id.dat",$viptime);}
Header("Location: $goto"); exit; }


} // if $event==rdtema












if(isset($_GET['event'])) {


// Сделать копию БД
if ($_GET['event']=="makecopy")  {
if (is_file("$datadir/wrfoto.dat")) $lines=file("$datadir/wrfoto.dat");
if (!isset($lines)) $datasize=0; else $datasize=sizeof($lines);
if ($datasize<=0) exit("Проблемы с Базой данных - база повреждена. Размер = 0!");
if (copy("$datadir/wrfoto.dat", "$datadir/copy.dat")) exit("<center><BR>Копия база данных создана.<BR><BR><h3>$back</h3></center>"); else exit("Ошибка создания копии БАЗЫ Данных. Попробуйте создать вручную файл copy.dat в папке $datadir и выставить ему права на ЗАПИСЬ - 666 или полные права 777 и повторите операцию создания копии!"); }

// Восстановить из копии БД
if ($_GET['event']=="restore")  {
if (is_file("$datadir/copy.dat")) $lines=file("$datadir/copy.dat");
if (!isset($lines)) $datasize=0; else $datasize=sizeof($lines);
if ($datasize<=0) exit("Проблемы с копией базы данных - она повреждена. Восстановление невозможно!");
if (copy("$datadir/copy.dat", "$datadir/wrfoto.dat")) exit("<center><BR>БД восстановлена из копии.<BR><BR><h3>$back</h3></center>"); else exit("Ошибка восстановления из копии БАЗЫ Данных. Попробуйте вручную файлам copy.dat и wrfoto.dat в папке $datadir выставить права на ЗАПИСЬ - 666 или полные права 777 и повторите операцию восстановления!"); }



// КОНФИГУРИРОВАНИЕ, шаг 2: сохранение данных
if ($_GET['event']=="config")  {

// обработка полей пароль админа/модератора
if (strlen($_POST['newpassword'])<1 or strlen($_POST['newmoderpass'])<1) exit("$back разрешается длина пароля МИНИМУМ 1 символ!");
if ($_POST['newpassword']!="скрыт") {$pass=trim($_POST['newpassword']); $_POST['password']=md5("$pass+$skey");}
if ($_POST['newmoderpass']!="скрыт") {$pass=trim($_POST['newmoderpass']); $_POST['moderpass']=md5("$pass+$skey");}

// защита от дурака. Дожились, уже в админке защиту приходится ставить...
$fd=stripslashes($_POST['fdesription']); $fd=str_replace("\\","/",$fd); $fd=str_replace("?>","? >",$fd); $fd=str_replace("\"","'",$fd); $fdesription=str_replace("\r\n","<br>",$fd);

mt_srand(time()+(double)microtime()*1000000); $rand_key=mt_rand(1000,9999); // Генерируем случайное число для цифрозащиты

$gmttime=($_POST['deltahour'] * 60 * 60);  // Считаем смещение

$newsmiles=$_POST['newsmiles'];

$i=count($newsmiles); $smiles="array(";
for($k=0; $k<$i; $k=$k+2) {
  $j=$k+1; $s1=replacer($newsmiles[$k]); $s2=replacer($newsmiles[$j]);
  $smiles.="\"$s1\", \"$s2\""; if ($k!=($i-2)) $smiles.=",";
} $smiles.=");";

$configdata="<? // WR-foto v 1.1  //  21.08.13 г.  //  Miha-ingener@yandex.ru\r\n".
"$"."fname=\"".$_POST['fname']."\"; // Название скрипта показывается в теге TITLE и заголовке\r\n".
"$"."fdesription=\"".$fdesription."\"; // Краткое описание форума\r\n".
"$"."password=\"".$_POST['password']."\"; // Пароль админа защифрован md5()\r\n".
"$"."moderpass=\"".$_POST['moderpass']."\"; // Пароль модератора защифрован md5()\r\n".
"$"."adminemail=\"".$_POST['newadminemail']."\"; // Е-майл администратора\r\n".
"$"."stop=\"".$_POST['stop']."\"; // ОТКЛЮЧИТЬ добавление разделов / фотографий\r\n".
"$"."antimat=\"".$_POST['antimat']."\"; // включить АНТИМАТ да/нет - 1/0\r\n".
"$"."random_name=\"".$_POST['random_name']."\"; // При загрузке файла генерировать ему имя случайным образом?\r\n".
"$"."repaaddfile=\"".$_POST['repaaddfile']."\"; // Сколько очков репутации добавлять при загрузке файла?\r\n".
"$"."repaaddmsg=\"".$_POST['repaaddmsg']."\"; // Сколько очков репутации добавлять за добавление сообщения?\r\n".
"$"."repaaddtem=\"".$_POST['repaaddtem']."\"; // Сколько очков репутации добавлять за добавлении темы?\r\n".
"$"."sendmail=\"".$_POST['sendmail']."\"; // Включить отправку сообщений? 1/0\r\n".
"$"."sendadmin=\"".$_POST['sendadmin']."\"; // Мылить админу сообщения зареганных пользователей? 1/0\r\n".
"$"."statistika=\"".$_POST['statistika']."\"; // Показывать статистику на главной странице? 1/0\r\n".
"$"."antispam=\"".$_POST['antispam']."\"; // Задействовать АНТИСПАМ\r\n".
"$"."antispam=\"1\"; // Задействовать АНТИСПАМ 2013\r\n".
"$"."antispam2012v=\"".$_POST['antispam2012v']."\"; // вопрос АНТИСПАМА 2012\r\n".
"$"."antispam2012o=\"".$_POST['antispam2012o']."\"; // ответ АНТИСПАМА 2012\r\n".
"$"."max_key=\"".$_POST['max_key']."\"; // Кол-во символов в коде ЦИФРОЗАЩИТЫ\r\n".
"$"."rand_key=\"".$rand_key."\"; // Случайное число для цифрозащиты\r\n".
"$"."newmess=\"".$_POST['newmess']."\"; // Создавать файл с новыми сообщениями форума?\r\n".
"$"."guest=\"".$_POST['newguest']."\"; // Как называем не зарег-ся пользователей\r\n".
"$"."users=\"".$_POST['newusers']."\"; // Как называем зарег-ся\r\n".
"$"."cangutema=\"".$_POST['cangutema']."\"; // РазрешЅРёРµ РЅРµРІРѕР·РјРѕР¶РЅРѕ!");
if (copy("$datadir/copy.dat", "$datadir/wrfoto.dat")) exit("<center><BR>Р‘Р” РІРѕСЃСЃС‚Р°РЅРѕРІР»РµРЅР° РёР· РєРѕРїРёРё.<BR><BR><h3>$back</h3></center>"); else exit("РћС€РёР±РєР° РІРѕСЃСЃС‚Р°РЅРѕРІР»РµРЅРёСЏ РёР· РєРѕРїРёРё Р‘РђР—Р« Р”Р°РЅРЅС‹С…. РџРѕРїСЂРѕР±СѓР№С‚Рµ РІСЂСѓС‡РЅСѓСЋ С„Р°Р№Р»Р°Рј copy.dat Рё wrfoto.dat РІ РїР°РїРєРµ $datadir РІС‹СЃС‚Р°РІРёС‚СЊ РїСЂР°РІР° РЅР° Р—РђРџР