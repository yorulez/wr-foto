<? // WR-foto v 1.2  //  02.08.15 г.  //  Miha-ingener@yandex.ru

error_reporting (E_ALL); //error_reporting(0);
ini_set('register_globals','off');// Все скрипты написаны для этой настройки php

include "data/config.php";

// временно часть переменных из конфига форума. Часть здесь используется.

$forum_lock="0"; // ОТКЛЮЧИТЬ добавление тем/сообщений
$random_name="0"; // При загрузке файла генерировать ему имя случайным образом?
$repaaddmsg="1"; // Сколько очков репутации добавлять за добавление сообщения?
$repaaddtem="4"; // Сколько очков репутации добавлять за добавлении темы?
$repaaddfile="7"; // Сколько очков репутации добавлять при загрузке файла?
$sendmail="1"; // Включить отправку сообщений? 1/0
$admin_send="0"; // Мылить админу сообщения о вновь зарегистрированных пользователях? 1/0
$statistika="1"; // Показывать статистику на главной странице? 1/0
$antimat="0"; // включить АНТИМАТ да/нет - 1/0
$antispam="0"; // Задействовать АНТИСПАМ
$antispam2012="0"; // Задействовать АНТИСПАМ 2012
$antispam2012v="Какой сейчас год?"; // вопрос АНТИСПАМА 2012
$antispam2012o="2014"; // ответ АНТИСПАМА 2012
$max_key="4"; // Кол-во символов в коде ЦИФРОЗАЩИТЫ
$rand_key="6855"; // Случайное число для цифрозащиты
$newmess="1"; // Создавать файл с новыми сообщениями форума?
$guest_name="гость"; // Как называем не зарег-ся пользователей
$user_name="участник WR-форума"; // Как называем зарег-ся
$g_add_tema="1"; // Разрешить гостям создавать темы? 1/0
$g_add_msg="1"; // Разрешить гостям оставлять сообщения? 1/0
$activation="1"; // Требовать активации через емайл при регистрации? 1/0
$maxname="35"; // Максимальное кол-во символов в имени
$maxzag="70"; // Масимальный кол-во символов в заголовке темы
$maxmsg="5000"; // Максимальное количество символов в сообщении
$tem_onpage="16"; // Кол-во отображаемых тем на страницу (15)
$msg_onpage="11"; // Кол-во отображаемых сообщений на каждой странице (10)
$uq="50"; // По сколько человек выводить список участников
$specblok1="1"; // Включить БЛОК 15-и самых обсуждаемых тем?
$specblok2="1"; // Включить БЛОК 10 самых активных пользователей?
$nosssilki="0"; // Запретить гостям добавлять сообщения со ссылками?
$liteurl="0";// Подсвечивать УРЛ? 1/0
$max_f_size="102400"; // Максимальный размер аватара в байтах
$datadir="./data"; // Папка с данными форума
$showsmiles="1";// Включить/отключить графические смайлы
$can_up_file="1"; // Разрешить загрузку фото 0 - нет, 1 - только зарегистрированным
$filedir="./files"; // Каталог куда будет закачан файл
$max_upfile_size="1048576"; // максимальный размер файла в байтах
$forum_skin="images-green"; // Текущий скин форума

$avatardir="./avatars"; // Каталог куда загружаются аватары
$maxfsize=round($max_file_size/10.24)/100;
$valid_types=array("gif","jpg","png","jpeg"); // допустимые расширения

// Определяем URL форума
$host=$_SERVER["HTTP_HOST"]; $self=$_SERVER["PHP_SELF"]; $forum_url=str_replace('tools.php','',"http://$host$self");

// Функция содержит ПРОДОЛЖЕНИЕ ШАПКИ. Вызывается: addtop();
function addtop() { global $wrfname,$forum_skin,$date,$time;

// ищем В КУКАХ wrfcookies чтобы вывести ИМЯ
if (isset($_COOKIE['wrfcookies'])) {$wrfc=$_COOKIE['wrfcookies']; $wrfc=htmlspecialchars($wrfc); $wrfc=stripslashes($wrfc); $wrfc=explode("|", $wrfc); $wrfname=$wrfc[0];} else {$wrfname=null; $wrfpass=null;}

echo'<TD align=right>';

if ($wrfname!=null) {
$codename=urlencode($wrfname); // Кодируем имя в СПЕЦФОРМАТ, для поддержки корректной передачи имени через GET-запрос.
print "<a href='tools.php?event=profile&pname=$codename' class=mainmenu><img src=\"$forum_skin/icon_mini_profile.gif\" border=0 hspace=3 />Ваш профиль</a>&nbsp;&nbsp;<a href='index.php?event=clearcooke' class=mainmenu><img src=\"$forum_skin/ico-login.gif\" border=0 hspace=3 />Выход [<B>$wrfname</B>]</a>";}

else {print "<span class=mainmenu>
<a href='tools.php?event=reg' class=mainmenu><img src=\"$forum_skin/icon_mini_register.gif\" border=0 hspace=3 />Регистрация</a>&nbsp;&nbsp;
<a href='tools.php?event=login' class=mainmenu> <img src=\"$forum_skin/buttons_spacer.gif\" border=0 hspace=3>Вход</a></td>";}

if (is_file("$forum_skin/tiptop.html")) include("$forum_skin/tiptop.html"); // подключаем дополнение к ВЕРХУШКе

print"</span></td></tr></table></td></tr></table><span class=gensmall>Сегодня: $date - $time";
return true;}


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


function unreplacer($text) { // ФУНКЦИЯ замены спецсимволов конца строки на обычные
$text=str_replace("&lt;br&gt;","<br>",$text); return $text;}


function nospam() { global $max_key,$rand_key,$antispam2012,$antispam2012v; // Функция АНТИСПАМ 2011+2012
if (array_key_exists("image", $_REQUEST)) { $num=replacer($_REQUEST["image"]);
for ($i=0; $i<10; $i++) {if (md5("$i+$rand_key")==$num) {imgwr($st,$i); die();}} }
$xkey=""; mt_srand(time()+(double)microtime()*1000000);
$dopkod=mktime(0,0,0,date("m"),date("d"),date("Y")); // доп.код: меняется каждые 24 часа
$stime=md5("$dopkod+$rand_key");// доп.код
echo'<table cellspacing=0 cellpadding=0><tr height=30><TD>Защитный код:</TD>';
$nummax=0; for ($i=0; $i<=$max_key; $i++) {
$snum[$i]=mt_rand(0,9); $psnum=md5($snum[$i]+$rand_key+$dopkod);
$secret=mt_rand(0,1); $styles='bgcolor=#FFFF00';
if ($nummax<3) { if ($secret==1 or $i==0) {$styles='bgcolor=#77C9FF'; $xkey=$xkey.$snum[$i]; $nummax++;}}
echo "<td width=20 $styles><img src=antispam2013.php?image=$psnum border=0 alt=''>\n<img src=antispam2013.php?image=$psnum height=1 width=1 border=0></td>\r\n";}
$xkey=md5("$xkey+$rand_key+$dopkod"); //число + ключ из data/config.php + код меняющийся кажые 24 часа
print"<td><input name='usernum' class=post type='text' maxlength=$nummax size=6> (введите цифры, которые на <font style='font-weight:bold'> голубом фоне</font>)
<input name=xkey type=hidden value='$xkey'>
<input name=stime type=hidden value='$stime'>
</td></tr></table>";
if ($antispam2012==TRUE) print"Ответ на вопрос: <input name='antispam2012o' class=post type='text' maxlength=20 size=10>($antispam2012v)";
return; }



// функция используется для отображения аватаров
function get_dir($path = './', $mask = '*.php', $mode = GLOB_NOSORT) {
 if ( version_compare( phpversion(), '4.3.0', '>=' ) ) {if ( chdir($path) ) {$temp = glob($mask,$mode); return $temp;}}
return false;}



if (isset($_GET['rss'])) { // ПОКАЗЫВАЕМ ЛЕНТУ RSS
$forum_name=replacer($forum_name); $forum_info=replacer($forum_info);
$forum_url.="index.php"; $adminemail=replacer($adminemail);

// Формируем RSS-файл 
echo "<?xml version=\"1.0\" encoding=\"windows-1251\"?>
<rss version=\"2.0\">
 <channel>
   <title>RSS лента новостей: $forum_name</title>
   <link>$forum_url</link>
   <description>$forum_info</description>
   <language>Russian</language>
   <copyright>Rootman</copyright>
   <managingEditor>$adminemail</managingEditor>
   <webMaster>$adminemail</webMaster>
   <generator>WR-Forum 2.0 RSS-module</generator>
   <lastBuildDate>$date $time</lastBuildDate>
";

// Чтение новостей и их вывод на экран
$lines=file("$datadir/news.dat"); $itogo=sizeof($lines); $x=$itogo-1;

do { $dt=explode("|",replacer($lines[$x]));
$xdate=date("r",$dt[4]); // конвертируем дату в формат ленты RSS
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
   <description>&lt;b&gt;$name пишет:&lt;/b&gt; &lt;br&gt;&lt;br&gt; $msg &lt;br&gt;&lt;br&gt;</description>
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
exit;} // КОНЕЦ БЛОКА RSS





// ВСЁ, что делается при наличии переменной $_GET['event']
if(isset($_GET['event'])) {



if ($_GET['event']=="login") { // ВХОД на форум УЧАСТНИКОМ
$frname="Вход на форум .:. "; $frtname="";
//include("$forum_skin/top.html"); addtop(); // подключаем ШАПКУ форума

echo '<BR><BR><BR><BR><center>
<table border=1 cellSpacing=1><TR><TD class=row2>
<TABLE class=bakfon cellPadding=4 cellSpacing=1>

<FORM action="tools.php?event=regenter" method=post>
<TR class=toptable><TD align=middle colSpan=2><B>Вход на форум</B></TD></TR>
<TR class=row1><TD>Имя:</TD><TD><INPUT name=name class=post></TD></TR>
<TR class=row2><TD>Пароль:</TD><TD><INPUT type=password name=pass class=post></TD></TR>
<TR class=row1><TD colspan=2><center><INPUT type=submit class=button value=Войти></TD></TR></TABLE></FORM> </TD></TR></TABLE>
<BR><BR><BR>
<table border=1 cellSpacing=1><TR><TD class=row2>
<TABLE class=bakfon cellPadding=3 cellSpacing=1>
<FORM action="tools.php?event=givmepassword" method=post>
<TR class=toptable><TD align=middle colSpan=3><B>Забыли пароль? Введите на выбор:</B></TD></TR>
<TR class=row1><TD><B>Ваш Емайл:</B> <font color=red>*</font></TD><TD><INPUT name=myemail class=post style="width: 170px"></TD>
<TR class=row1><TD><B>Имя (Ник):</B></TD><TD><INPUT name=myname class=post style="width: 170px"></TD></TR>
<TR><TD colspan=2 align=center><INPUT type=submit class=button style="width:150" value="Сделать запрос"></TD></TR>
<TR><TD colspan=3><small><font color=red>*</font> На Ваш электронный адрес будет выслана<br> информация для восстановления учётной записи.</TD></TR></TABLE>
</FORM></TD></TR></TABLE><BR><BR><BR><BR><BR>
</TD></TR></TABLE>
</TD></TR></TABLE>'; exit;}


// РЕПУТАЦИЯ - окно выбора: шаг 1
if ($_GET['event']=="repa") {

if (!isset($_GET['name'])) exit("Нет данных переменной name."); $name=replacer($_GET['name']);

// Если куков нет - облом, если куки есть и равны имени юзера - облом.
if (!isset($_COOKIE['wrfcookies'])) exit("<html><head><title>Изменение РЕПУТАЦИИ</title></head><body><center><br><br><br>Просмотр и изменение РЕПУТАЦИИ может производить только участник форума!");
else { $wrfc=$_COOKIE['wrfcookies']; $wrfc=htmlspecialchars($wrfc); $wrfc=stripslashes($wrfc); $wrfc=explode("|", $wrfc); $wrfname=$wrfc[0];
if ($wrfname===$name) print"<B>$back. По правилам форума<br> <font color=red>поднимать репутацию себе ЗАПРЕЩЕНО!</font><br>";

else { print "<html><head><title>Изменение РЕПУТАЦИИ участника: $name</title></head><body leftMargin=0 topMargin=0 rightMargin=0>
<center><table cellpadding=0 cellspacing=8><TR><FORM action='tools.php?event=repasave' method=post>
<TD colspan=7 align=center><B>Изменение РЕПУТАЦИИ участника $name</B></TD></TR><TR height=40>
<TD bgcolor=#880003><font size=+2 color=white>-5<INPUT name=repa type=radio value='-5'></TD>
<TD bgcolor=#FF2025><font size=+2 color=white>-2<INPUT name=repa type=radio value='-2'></TD>
<TD bgcolor=#FFB7B9><font size=+2 color=white>-1<INPUT name=repa type=radio value='-1'></TD>
<TD bgcolor=#FFFF00><font size=+2 color=#FF8040>0<INPUT name=repa checked type=radio value='0'></TD>
<TD bgcolor=#A4FFAA><font size=+2 color=white>+1<INPUT name=repa type=radio value='+1'></TD>
<TD bgcolor=#00C10F><font size=+2 color=white>+2<INPUT name=repa type=radio value='+2'></TD>
<TD bgcolor=#00880B><font size=+2 color=white>+5<INPUT name=repa type=radio value='+5'></TD></TR>
<INPUT type=hidden name=name value=$name>
<TR><TD colspan=7><B>Причина:</B> <INPUT type=text name=pochemu size=45 value=''><INPUT type=submit value=Отправить></td></TR>
</TABLE></FORM>";}

if (is_file("$datadir/repa.dat")) { // Ищем в файле repa.dat инфу об этом юзере и выводим, если есть
$file="$datadir/repa.dat"; $lines=file("$file"); $i=count($lines);
print"<table border=1 cellpadding=2 cellspacing=0 width=100%><TR><TD colspan=5 align=center><B>Изменение репутации участника $name</B></td></tr>
<TR align=center><TD>Когда</TD><TD>Кто</TD><TD>Балл</TD><TD width=55%>Причина</TD></TR>";
do {$i--; $dt=explode("|",$lines[$i]);
if (strlen($dt[3])>1) $dt[3]="<a href='tools.php?event=profile&pname=$dt[3]' target=_blank>$dt[3]</a>"; else $dt[3]="Робот форума";
if ($dt[1]>0) $dt[1]="<TD align=center bgcolor=#B7FFB7><B>$dt[1]"; else $dt[1]="<TD align=center bgcolor=#FF9F9F><B>$dt[1]";
if ($dt[2]==$name) {$dt[0]=date("d.m.y в H:i",$dt[0]); print"<TR><TD align=center><small>$dt[0]</small></TD><TD align=center><B>$dt[3]</B></TD>$dt[1]</B></TD><TD><small>$dt[4]</small></TD></TR>";}
} while($i>0);
echo'</table>'; } // есть есть файл repa.dat
exit; }}


// РЕПУТАЦИЯ - сохранение: шаг 2
if ($_GET['event']=="repasave") {

if (isset($_COOKIE['wrfcookies'])) {$wrfc=$_COOKIE['wrfcookies']; $wrfc=htmlspecialchars($wrfc); $wrfc=stripslashes($wrfc); $wrfc=explode("|", $wrfc); $wrfname=$wrfc[0];} else exit('Только участники форума могут изменять репутацию!');
if (!isset($_POST['name'])) exit("Нет данных переменной name."); $name=replacer($_POST['name']);
if (isset($_POST['repa'])) $repa=$_POST['repa']; else exit("Нет данных переменной repa");
if (isset($_POST['pochemu'])) $pochemu=$_POST['pochemu']; else exit("Укажите причину смены репутации");

if (!is_numeric($repa)) exit("<B>$back. Попытка взлома. Не хулигань, друг!");
if ($repa>5 or $repa<-5) exit("<B>$back. Попытка взлома. Репу можно менять только на +-5 пунктов. Не хулигань, друг!");
if (strlen($pochemu)<1 or strlen($pochemu)>150) exit("<B>$back. Текст причины должен быть указан! И быть не более 150 символов!");

$today=time();
// БЛОК добавляет + к репутации ЮЗЕРА
//ИМЯ_ЮЗЕРА|Тем|Сообщений|Репутация|Предупреждения Х/5|Когда последний раз меняли рейтинг в UNIX формате|||
$ufile="$datadir/userstat.dat"; $ulines=file("$ufile"); $ui=count($ulines)-1; $ulinenew=""; $username="";
$ip=$_SERVER['REMOTE_ADDR']; // определяем IP юзера
// Ищем юзера по имени в файле userstat.dat, если недавно голосовали за него, запрещаем
for ($i=0;$i<=$ui;$i++) {$udt=explode("|",$ulines[$i]);
if ($udt[2]==$name) {$udt[7]=$udt[7]+$repa; if (strlen($udt[1])>5) {$next=$today-$udt[1]; sleep(1); 
if ($ip==$udt[10]) exit("C вашего IP-адреса уже были голосования за репутацию этого участника. Изменять репутацию этому участнику Вам нельзя до тех пор, пока кто-нибудь с другог IP не проголосует за него!");
if ($next<180) {$last=180-$next; exit("<B>$back. Рейтинг этого участника<br> уже был изменён только что.<br> <font color=red>Ожидайте $last секунд.</font> </B>");}}
$ulines[$i]="$udt[0]|$today|$udt[2]|$udt[3]|$udt[4]|$udt[5]|$udt[6]|$udt[7]|$udt[8]|$udt[9]|$ip|$udt[11]|\r\n";
}
$ulinenew.="$ulines[$i]";}

// Записываем данные в файл
$fp=fopen("$ufile","w");
flock ($fp,LOCK_EX);
fputs($fp,"$ulinenew");
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);

// Записываем данные в файл REPA.DAT
//дата в UNIX-формате|сколько баллов|имя_кому_меняли|кто_менял|причина||||
$fp=fopen("$datadir/repa.dat","a+");
flock ($fp,LOCK_EX);
fputs($fp,"$today|$repa|$name|$wrfname|$pochemu||||\r\n");
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);

exit("<div align=center><BR><BR><BR>Рейтинг <B>успешно</B> пересчитан.<BR><BR><BR><a href='' onClick='self.close()'><b>Закрыть окно</b></a></div>");
}





// ОТПРАВКА СООБЩЕНИЯ юзеру
if ($_GET['event']=="mailto") {

if ($sendmail!=TRUE) exit("$back. <center><B>Извините, но функция отправки писем ЗАБЛОКИРОВАНА администратором!<BR><BR><BR><a href='' onClick='self.close()'>Закрыть окно</b></a></center>");

if (!isset($_POST['email'])) exit("Нет данных переменной email.");
if (!isset($_POST['name'])) exit("Нет данных переменной name.");
$uemail=replacer($_POST['email']); $uname=replacer($_POST['name']);
$id=""; $fid=""; if (isset($_POST['id'])) {$id=replacer($_POST['id']); if (strlen($id)>0) $fid=substr($id,0,3);}

print "<html><head><meta http-equiv='Content-Type' content='text/html; charset=windows-1251'><meta http-equiv='Content-Language' content='ru'>
<meta name=\"Robots\" content=\"noindex,nofollow\">
<title>Отправление сообщения автору объявления</title></head><body topMargin=5>
<center><TABLE bgColor=#aaaaaa cellPadding=2 cellSpacing=1 width=502>
<FORM action='tools.php?event=mailtogo' method=post>
<TBODY><TR><TD align=middle bgColor=#cccccc colSpan=2>Получатель сообщения: <B>$uname</B></TD></TR>

<TR bgColor=#ffffff><TD>&nbsp; Ваше Имя:<FONT color=#ff0000>*</FONT> <INPUT name=name style='FONT-SIZE: 14px; WIDTH: 150px'>

и E-mail:<FONT color=#ff0000>*</FONT> <INPUT name=email style='FONT-SIZE: 14px; WIDTH: 180px'></TD></TR>

<TR bgColor=#ffffff><TD>&nbsp; Сообщение:<FONT color=#ff0000>*</FONT><br>
<TEXTAREA name=msg style='FONT-SIZE: 14px; HEIGHT: 150px; WIDTH: 494px'></TEXTAREA></TD></TR>
<INPUT type=hidden name=uemail value=$uemail><INPUT type=hidden name=uname value=$uname>
<TR bgColor=#ffffff><TD>";

if ($antispam==TRUE and !isset($wrfname)) nospam(); // АНТИСПАМ !

if ($id!="") print"<INPUT type=hidden name=id value=$id><INPUT type=hidden name=fid value=$fid>";

echo'<TR><TD bgColor=#FFFFFF colspan=2><center><INPUT type=submit value=Отправить></TD></TR></TBODY></TABLE></FORM>'; 
exit; }


// ШАГ 2 отправки сообщения пользователю
if ($_GET['event']=="mailtogo") {
$name=replacer($_POST['name']);
$email=replacer($_POST['email']);
$msg=replacer($_POST['msg']);
if (isset($_POST['fid'])) $fid=replacer($_POST['fid']);
if (isset($_POST['id'])) $id=replacer($_POST['id']);
$uname=replacer($_POST['uname']);
$uemail=replacer($_POST['uemail']);

//--А-Н-Т-И-С-П-А-М--проверка кода--
if ($antispam==TRUE) {
if (!isset($_POST['usernum']) or !isset($_POST['xkey']) or !isset($_POST['stime']) ) exit("данные из формы не поступили!");
$usernum=replacer($_POST['usernum']); $xkey=replacer($_POST['xkey']); $stime=replacer($_POST['stime']);
$dopkod=mktime(0,0,0,date("m"),date("d"),date("Y")); // доп.код. Меняется каждые 24 часа
$usertime=md5("$dopkod+$rand_key");// доп.код
$userkey=md5("$usernum+$rand_key+$dopkod");
if (($usertime!=$stime) or ($userkey!=$xkey)) exit("введён ОШИБОЧНЫЙ код!");}

if (!preg_match('/^([0-9a-zA-Z]([-.w]*[0-9a-zA-Z])*@([0-9a-zA-Z][-w]*[0-9a-zA-Z].)+[a-zA-Z]{2,9})$/si',$email) and strlen($email)>30 and $email!="") exit("$back и введите корректный E-mail адрес!</B></center>");
if (!preg_match('/^([0-9a-zA-Z]([-.w]*[0-9a-zA-Z])*@([0-9a-zA-Z][-w]*[0-9a-zA-Z].)+[a-zA-Z]{2,9})$/si',$uemail) and strlen($uemail)>30 and $uemail!="") exit("$back у пользователя задан несуществующий адрес!</B></center>");
if ($name=="") exit("$back Вы не ввели своё имя!</B></center>");
if ($msg=="") exit("$back Вы не ввели сообщение!</B></center>");

$text="$name|$msg|$uname|$email|";
$text=str_replace("\r\n","<br>",$text);
$exd=explode("|",$text); $name=$exd[0]; $msg=$exd[1]; $uname=$exd[2]; $email=$exd[3];

$headers=null; // Настройки для отправки писем
$headers.="From: $name $email\n";
$headers.="X-Mailer: PHP/".phpversion()."\n";
$headers.="Content-Type: text/html; charset=windows-1251";

// Собираем всю информацию в теле письма

$allmsg="<html><head>
<meta http-equiv='Content-Type' content='text/html; charset=windows-1251'><meta http-equiv='Content-Language' content='ru'>
</head><body>
<BR><BR><center>$uname, это сообщение отправлено вам от посетителя форума <BR><B>$forum_name</B><BR><BR>
<table cellspacing=0 width=700 bgcolor=navy><tr><td><table cellpadding=6 cellspacing=1 width='100%'>
<tr bgcolor=#F7F7F7><td width=130 height=24>Имя</td><td>$name</td></tr>
<tr bgcolor=#F7F7F7><td>E-mail:</td><td><font size='-1'>$email</td></tr>
<tr bgcolor=#F7F7F7><td> Сообщение:</td><td><BR>$msg<BR></td></tr>
<tr bgcolor=#F7F7F7><td>Дата отправки сообщения:</td><td>$time - <B>$date г.</B></td></tr>
<tr bgcolor=#F7F7F7><td>Перейти на главную страницу:</td><td><a href='$forum_url'>$forum_url</a></td></tr>
</table></td></tr></table></center><BR><BR>* Данное письмо сгенерировано и отправлено роботом, отвечать на него не нужно.
</body></html>";

mail("$uemail", "Сообщение от посетителя форума ($forum_name) от $name ", $allmsg, $headers);
exit('<div align=center><BR><BR><BR>Ваше сообщение <B>успешно</B> отправлено.<BR><BR><BR><a href="#" onClick="self.close()"><b>Закрыть окно</b></a></div>'); }




// проверка имени/пароля и вход на форум
if ($_GET['event']=="regenter") {
if (!isset($_POST['name']) & !isset($_POST['pass'])) exit("$back введите имя и пароль!");
$name=str_replace("|","I",$_POST['name']); $pass=replacer($_POST['pass']);
$name=replacer($name); $name=strtolower($name);
if (strlen($name)<1 or strlen($pass)<1) exit("$back Вы не ввели имя или пароль!");

// проходим по всем пользователям и сверяем данные
$lines=file("$datadir/user.php"); $i=count($lines); $regenter=FALSE;
$pass=md5("$pass");
do {$i--; $rdt=explode("|",$lines[$i]);
if (isset($rdt[1])) { // Если строчка НЕ ПУСТА
if ($name===strtolower($rdt[2]) & $pass===$rdt[3]) {
if ($rdt[16]==FALSE) exit("$back. Ваша учётная запись не <a href='tools.php?event=reg3'>активирована</a>. Для активации Вам необходимо перейти по ссылке, которая должна прийти Вам на емайл.");
$regenter=TRUE;
$tektime=time();
$wrfcookies="$rdt[2]|$rdt[3]|$tektime|$tektime|";
setcookie("wrfcookies", $wrfcookies, time()+1728000);
}} // if-ы

} while($i > "1");

if ($regenter==FALSE) exit("$back Ваш данные <B>НЕ верны</B>!</center>");
Header("Location: index.php");
}








// Регистрация НОВЫЙ ШАГ 2!! отправка на мыл подтверждения и сохранение в БД
if ($_GET['event']=="regnxt") {

if (!isset($_POST['name']) & !isset($_POST['pass'])) exit("$back введите имя и пароль!");
$name=str_replace("|","I",$_POST['name']); $pass=str_replace("|","I",$_POST['pass']); $dayreg=$date;
$name=trim($name); // Вырезает ПРОБЕЛьные символы 

if (isset($_POST['email'])) $email=$_POST['email']; else $email="";
$email=strtolower($email);

//--А-Н-Т-И-С-П-А-М--проверка кода--
if ($antispam==TRUE) {
if (!isset($_POST['usernum']) or !isset($_POST['xkey']) or !isset($_POST['stime']) ) exit("данные из формы не поступили!");
$usernum=replacer($_POST['usernum']); $xkey=replacer($_POST['xkey']); $stime=replacer($_POST['stime']);
$dopkod=mktime(0,0,0,date("m"),date("d"),date("Y")); // доп.код. Меняется каждые 24 часа
$usertime=md5("$dopkod+$rand_key");// доп.код
$userkey=md5("$usernum+$rand_key+$dopkod");
if (($usertime!=$stime) or ($userkey!=$xkey)) exit("введён ОШИБОЧНЫЙ код!");

// АНТИСПАМ 2012!
if ($antispam2012==TRUE) { $ao=replacer($_POST['antispam2012o']);
if (strtolower($antispam2012o)!=strtolower($ao) or strlen($ao)<1) exit("введён ошибочный ответ на вопрос!");}
}

if (preg_match("/[^(\\w)|(\\x7F-\\xFF)|(\\-)]/",$name)) exit("$back Ваше имя содержит запрещённые символы. Разрешены русские и английские буквы, цифры и подчёркивание!!.");
if ($name=="" or strlen($name)>$maxname) exit("$back ваше имя пустое, или превышает $maxname символов!</B></center>");
if ($pass=="" or strlen($pass)<1 or strlen($pass)>$maxname) exit("$back Вы не ввели пароль. Пароль не должен быть пустым.</B></center>");
if(!preg_match("/^[a-z0-9\.\-_]+@[a-z0-9\-_]+\.([a-z0-9\-_]+\.)*?[a-z]+$/is", $email) or $email=="" or strlen($email)>40) exit("$back и введите корректный E-mail адрес!</B></center>");
if (isset($_POST['pol'])) $pol=$_POST['pol']; else $pol=""; if ($pol!="1") $pol="0";

$email=str_replace("|","I",$email);

$key=mt_rand(100000,999999); if ($activation==FALSE) $key=""; // КОЛДУЕМ рандомный КОД активации? если не требуется - обнуляем

$rn=mt_rand(10000,99999); $tektime=time();
$pass=replacer($pass); $ps=md5("$pass");
$text="$rn|$tektime|$name|$ps|0|$email|$pol||0|||||||$key|0|";
$text=replacer($text); $exd=explode("|",$text); $name=$exd[2]; $email=$exd[5];
$ip=$_SERVER['REMOTE_ADDR']; // определяем IP юзера

if ($name===$pass) exit("$back. В целях Вашей безопасности, <B>запрещено равенство имени и пароля!</B>");

// Ищем юзера с таким логином или емайлом
$loginsm=strtolower($name);
$lines=file("$datadir/user.php"); $i=count($lines);
if ($i>"1") { do {$i--; $rdt=explode("|",$lines[$i]); 
$rdt[2]=strtolower($rdt[2]);
if ($rdt[2]===$loginsm) {$bad="1"; $er="логином";}
if ($rdt[5]===$email) {$bad="1"; $er="емайлом";}
} while($i > 1);
if (isset($bad)) exit("$back. Участник с таким <B>$er уже зарегистрирован на форуме</B>!"); }

// отправка пользователю КОДА АКТИВАЦИИ
$headers=null; // Настройки для отправки писем
$headers.="From: $name <$email>\n";
$headers.="X-Mailer: PHP/".phpversion()."\n";
$headers.="Content-type: text/plain; charset=windows-1251";

// Собираем всю информацию в теле письма
if ($activation==TRUE) {
$allmsg=$forum_name.' (подтверждение регистрации)'.chr(13).chr(10).
 'Подтвердите регистрациию на форуме, для этого перейдите по ссылке: '.$forum_url.'tools.php?event=reg3&email='.$email.'&key='.$key.chr(13).chr(10).
 'Ваше Имя: '.$name.chr(13).chr(10).
 'Ваш пароль: '.$pass.chr(13).chr(10).
 'Ваш E-mail: '.$email.chr(13).chr(10).
 'Активационный ключ: '.$key.chr(13).chr(10).chr(13).chr(10).
 'Сохраните письмо с паролем или запомните его.'.chr(13).chr(10).
 'Пароли на форуме хранятся в зашифрованном виде, увидеть пароль невозможно.'.chr(13).chr(10).
 'Для восстановления доступа к форуму Вам придётся воспользоваться системой восстановления пароля.'.chr(13).chr(10);
 
} else { $allmsg=$forum_name.' (данные регистрации)'.chr(13).chr(10). 'Вы успешно зарегистрированы на форуме: '.$forum_url.chr(13).chr(10). 'Ваше Имя: '.$name.chr(13).chr(10). 'Ваш пароль: '.$pass.chr(13).chr(10). 'Ваш E-mail: '.$email.chr(13).chr(10); }

// Отправляем письмо майлеру на съедение ;-)
mail("$email", "=?windows-1251?B?" . base64_encode("$forum_name (подтверждение регистрации)") . "?=", $allmsg, $headers);
if ($admin_send==TRUE) {mail("$adminemail", "=?windows-1251?B?" . base64_encode("$forum_name (Новый участник)") . "?=", $allmsg, $headers);}

$file=file("$datadir/user.php");
$fp=fopen("$datadir/user.php","a+");
flock ($fp,LOCK_EX);
fputs($fp,"$text\r\n");
fflush ($fp);//очищение файлового буфера
flock ($fp,LOCK_UN);
fclose($fp);

// Записываем строчку с именем в файл со статистикой
$file=file("$datadir/userstat.dat");
$fp=fopen("$datadir/userstat.dat","a+");
flock ($fp,LOCK_EX);
fputs($fp,"$rn||$name|0||0|0|0|0||$ip||\r\n");
fflush ($fp);//очищение файлового буфера
flock ($fp,LOCK_UN);
fclose($fp);

if ($activation!=TRUE) { $tektime=time(); $wrfcookies="$name|$pass|$tektime|0|"; setcookie("wrfcookies", $wrfcookies, time()+1728000);
print"<html><head><link rel='stylesheet' href='$forum_skin/style.css' type='text/css'></head><body>
<script language='Javascript'>function reload() {location = \"index.php\"}; setTimeout('reload()', 2500);</script>
<table width=100% height=80%><tr><td><table border=1 cellpadding=10 cellspacing=0 bordercolor=#224488 align=center valign=center width=60%><tr><td><center>
<B>$name, Вы успешно зарегистрированы</B>.<BR><BR>Через несколько секунд Вы будете автоматически перемещены на главную страницу форума.<BR><BR>
<B><a href='index.php'>ДАЛЬШЕ >>></a></B></td></tr></table></td></tr></table></center></body></html>"; exit;}

print"<html><head><link rel='stylesheet' href='$forum_skin/style.css' type='text/css'></head><body>
<script language='Javascript'>function reload() {location = \"tools.php?event=reg3\"}; setTimeout('reload()', 2500);</script>
<table width=100% height=80%><tr><td><table border=1 cellpadding=10 cellspacing=0 bordercolor=#224488 align=center valign=center width=60%><tr><td><center>
<B>$name, на указанный Вами емайл был выслан код подтверждения.
Для того чтобы зарегистрироваться - введите его на странице, либо перейдите по ссылке - указанной в письме</B>.<BR><BR>Через несколько секунд Вы будете автоматически перемещены на страницу подтверждения регистрации.<BR><BR>
<B><a href='tools.php?event=reg3'>ДАЛЬШЕ >>></a></B></td></tr></table></td></tr></table></center></body></html>";
exit;
}






// Регистрация ШАГ 3 - ввод ключа либо подтверждение по емайлу
if ($_GET['event']=="reg3") {

if (isset($_GET['email']) and isset($_GET['key'])) {$key=$_GET['key']; $email=$_GET['email'];} else {
$frname=""; $frtname=""; include("$forum_skin/top.html"); addtop(); // подключаем ШАПКУ форума
exit('<center><span class=maintitle>Подтверждение регистрации*</span><br>
<br><form action="tools.php" method=GET>
<input type=hidden name=event value="reg3">
<table cellpadding=3 cellspacing=1 width=100% class=forumline><tr>
<th class=thHead colspan=2 height=25 valign=middle>Ввод емайла и активационного ключа</th>
</tr><tr><td class=row1><span class=gen>Адрес e-mail:</span><br><span class=gensmall></span></td><td class=row2><input type=text class=post style="width: 200px" name=email size=25 maxlength=50></td>
</tr><tr><td class=row1><span class=gen>Активационный ключ:</span><br><span class=gensmall></span></td><td class=row2><input type=text class=post style="width: 200px" name=key size=25 maxlength=6></td></tr><tr>
<td class=catBottom colspan=2 align=center height=28><input type=submit value="Подтвердить регистрацию" class=mainoption></td>
</tr></table>
* Вы можете либо ввести емайл и ключ, который пришёл по почте, либо перейти по активационной ссылке в письме.
</form>');}

// защиты от взлома по ключу и емайлу
if (strlen($key)<6 or strlen($key)>6 or !ctype_digit($key)) exit("$back. Вы ошиблись при вводе ключа. Ключ может содержать только 6 цифр.");
$email=replacer($email); $email=str_replace("|","I",$email); $email=str_replace("\r\n","<br>",$email);
if (strlen($email)>35) exit("Ошибка при вводе емайла");

// Ищем юзера с таким емайлом и ключом. Если есть - меняем статус на пустое поле.
$fnomer=null; $email=strtolower($email); unset($fnomer); unset($ok);
$lines=file("$datadir/user.php"); $ui=count($lines); $i=$ui;
do {$i--; $rdt=explode("|",$lines[$i]); 
$rdt[5]=strtolower($rdt[5]);
if ($rdt[5]===$email and $rdt[15]===$key) {$name=$rdt[2]; $pass=$rdt[3]; $fnomer=$i;}
if ($rdt[5]===$email and $rdt[16]==TRUE) $ok="1";
} while($i > 1);

if (isset($fnomer)) {
// обновление строки юзера в БД
$i=$ui; $dt=explode("|", $lines[$fnomer]);
$txtdat="$dt[0]|$dt[1]|$dt[2]|$dt[3]|$dt[4]|$dt[5]|$dt[6]|$dt[7]|$dt[8]|$dt[9]|$dt[10]|$dt[11]|$dt[12]|$dt[13]|$dt[14]|noavatar.gif|1|";
$fp=fopen("$datadir/user.php","a+");
flock ($fp,LOCK_EX); 
ftruncate ($fp,0);//УДАЛЯЕМ СОДЕРЖИМОЕ ФАЙЛА
for ($i=0;$i<=(sizeof($lines)-1);$i++) { if ($i==$fnomer) fputs($fp,"$txtdat\r\n"); else fputs($fp,$lines[$i]); }
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);
// устанавливаем КУКИ
$tektime=time(); $wrfcookies="$name|$pass|$tektime|0|";
setcookie("wrfcookies", $wrfcookies, time()+1728000);
}
if (!isset($fnomer) and !isset($ok)) exit("$back. Вы ошиблись в воде активационного ключа или емайла.</center>");
if (isset($ok)) $add="Ваша запись уже активирована"; else $add="$name, Вы успешно зарегистрированы";

print"<html><head><link rel='stylesheet' href='$forum_skin/style.css' type='text/css'></head><body>
<script language='Javascript'>function reload() {location = \"index.php\"}; setTimeout('reload()', 2500);</script>
<table width=100% height=80%><tr><td><table border=1 cellpadding=10 cellspacing=0 bordercolor=#224488 align=center valign=center width=60%><tr><td><center>
Спасибо, <B>$add</B>.<BR><BR>Через несколько секунд Вы будете автоматически перемещены на главную страницу форума.<BR><BR>
<B><a href='index.php'>ДАЛЬШЕ >>></a></B></td></tr></table></td></tr></table></center></body></html>";
exit; }






// Изменение данных регистрации - сохранение данных
if ($_GET['event']=="reregist") { // if ($event =="reregist")

if (!isset($_POST['name'])) exit("$back введите Ваше имя!");
$name=str_replace("|","I",$_POST['name']);
if ($name=="" or strlen($name)>$maxname) exit("$back ваше имя пустое, или превышает $maxname символов!</B></center>");
$name=trim($name); // Вырезает ПРОБЕЛьные символы 
if (preg_match("/[^(\\w)|(\\x7F-\\xFF)|(\\-)]/",$name)) exit("$back Ваше имя содержит запрещённые символы. Разрешены русские и английские буквы, цифры и подчёркивание!!.");

if (!isset($_POST['pass'])) exit("$back Вы не ввели пароль!");
$oldpass=$_POST['oldpass']; // Старый пароль
$pass=trim($_POST['pass']);
if (strlen($_POST['newpassword'])<1 ) exit("$back разрешается длина пароля МИНИМУМ 1 символ!");
if ($_POST['newpassword']!="скрыт") {$pass=trim($_POST['newpassword']); 
if (strlen($pass)<1 or strlen($pass)>20) exit("$back Вы не ввели пароль. Пароль должен быть длиной от 1 до 20 символов!</B></center>");
$pass=md5("$pass");}
$pass=replacer($pass); $pass=str_replace("|","I",$pass);

if (isset($_POST['email'])) $email=$_POST['email']; else $email=""; $email=strtolower($email);
if(!preg_match("/^[a-z0-9\.\-_]+@[a-z0-9\-_]+\.([a-z0-9\-_]+\.)*?[a-z]+$/is", $email) or $email=="" or strlen($email)>40) exit("$back и введите корректный E-mail адрес!</B></center>");

if (isset($_POST['dayx'])) $dayx=replacer($_POST['dayx']); else $dayx="";
if (isset($_POST['pol'])) $pol=replacer($_POST['pol']); else $pol=""; if ($pol!="1") $pol="0";
if (isset($_POST['icq'])) $icq=replacer($_POST['icq']); else $icq="";
if (isset($_POST['www'])) $www=replacer($_POST['www']); else $www="";
if (isset($_POST['about'])) $about=replacer($_POST['about']); else $about="";
if (isset($_POST['work'])) $work=replacer($_POST['work']); else $work="";
if (isset($_POST['write'])) $write=replacer($_POST['write']); else $write="";
if (isset($_POST['avatar'])) $avatar=replacer($_POST['avatar']); else $avatar="";

if ($_FILES['file']['name']!="") { // считываем имя и размер прикреплённого файла
$fotoname=replacer($_FILES['file']['name']); $fotosize=$_FILES['file']['size']; // Имя и размер файла
} else {$fotoname=$avatar; $fotosize="";}

$notgood="$back слишком длинное значение переменной ";
if (strlen($dayx)>20) {$notgood.="день рождения!"; exit("$notgood");}
if (strlen($icq)>10) {$notgood.="ICQ!"; exit("$notgood");}
if (strlen($www)>75) {$notgood.="URL сайта!"; exit("$notgood");}
if (strlen($about)>75) {$notgood.="откуда!"; exit("$notgood");}
if (strlen($work)>75) {$notgood.="интересы!"; exit("$notgood");}
if (strlen($write)>75) {$notgood.="подпись!"; exit("$notgood");}

$email=str_replace("|","I",$email);
$dayx=str_replace("|","I",$dayx);
$icq=str_replace("|","I",$icq);
$www=str_replace("|","I",$www);
$about=str_replace("|","I",$about);
$work=str_replace("|","I",$work);
$write=str_replace("|","I",$write);
$avatar=str_replace("|","I",$avatar);

// проверка Логина/Старого пароля
$ok=null; $lines=file("$datadir/user.php"); $i=count($lines); unset($ok);
do {$i--; $rdt=explode("|", $lines[$i]);
   if (strtolower($name)===strtolower($rdt[2]) & $oldpass===$rdt[3]) $ok="$i"; // Ищем юзера логин/пароль
   else { if ($email===$rdt[5]) $bademail="1"; } // Вдруг у когото уже есть такой емайл?
} while($i > "1");
if (isset($bademail)) exit("$back. Участник с емайлом <B>$email уже зарегистрирован</B> на форуме!</center>");
if (!isset($ok)) {setcookie("wrfcookies","",time());
exit("$back Ваш новый логин /пароль / Емайл не совпадает НИ с одним из БД. <BR><BR>
Смена электронного адреса <font color=red><B>Запрещена</B></font><BR><BR>
<font color=red><B>Ошибка скрипта или попытка взлома - обратитесь к администратору!</B></font>");}
$udt=explode("|",$lines[$ok]); $dayreg=$udt[1]; $kolvomsg=$udt[4]; $status=$udt[16]; $rn=$udt[0];

// старый формат $text="$name|$pass|$kolvomsg|$email|$dayreg|$dayx|$pol|$icq|$www|$about|$work|$write|$fotoname|$status|";
$text="$rn|$dayreg|$name|$pass|$kolvomsg|$email|$pol|$dayx|0||$icq|$www|$about|$work|$write|$fotoname|$status|";
$text=replacer($text); $exd=explode("|",$text); $name=$exd[2]; $pass=$exd[3]; $email=$exd[5];

// Ставим куку юзеру
//$tektime=time(); $wrfcookies="$name|$pass|$tektime|$tektime|";
//setcookie("wrfcookies", $wrfcookies, time()+1728000);

if ($fotoname!=$avatar and $fotoname!="") { // блок загрузки АВАТАРА

// ЗАЩИТЫ от ВЗЛОМА

// 1. Проверяем РАСШИРЕНИЕ
$ext = strtolower(substr($fotoname, 1 + strrpos($fotoname, ".")));
if (!in_array($ext, $valid_types)) {echo "<B>ФАЙЛ НЕ загружен.</B> Возможные причины:<BR>
- разрешена загрузка только файлов с такими расширениями: <B>";
$patern=""; foreach($valid_types as $v) print"$v, ";
print"</B><BR>
- Вы пытаетесь загрузить файл с двойным расширением;<BR>
- неверно введён адрес или выбран испорченный файл;</B><BR>"; exit;}

// 1. считаем кол-во точек в выражении - если большей одной - СВОБОДЕН!
$findtchka=substr_count($fotoname, "."); if ($findtchka>1) exit("ТОЧКА встречается в имени файла $findtchka раз(а). Это ЗАПРЕЩЕНО! <BR>\r\n");

// 2. если в имени есть .php, .html, .htm - свободен! 
$bag="Извините, но в имени ФАйла <B>запрещено</B> использовать .php, .html, .htm";
if (preg_match("/\.php/i",$fotoname)) exit("Вхождение <B>.php</B> найдено. $bag");
if (preg_match("/\.html/i",$fotoname)) exit("Вхождение <B>.html</B> найдено. $bag");
if (preg_match("/\.htm/i",$fotoname)) exit("Вхождение <B>.htm</B> найдено. $bag");

// 3. защищаем от РУССКИХ букв в имени файла и проверка РАСШИРЕНИЯ файла 
$patern=""; foreach($valid_types as $v) $patern.="$v|";
if (!preg_match("/^[a-z0-9\.\-_]+\.(".$patern.")+$/is",$fotoname)) exit("$fotoname - <br>Запрещено использовать РУССКИЕ буквы в имени файла, а также запрещено загружать файлы с расширением отличным от заданных!!");

// 4. Проверяем, может быть файл с таким именем уже есть на сервере
if (file_exists("$filedir/$fotoname")) exit("<br><br>$back. Файл с таким именем уже существует на сервере! Либо измените имя на другое, <br>либо обновите страницу - возможно Вы пытаетесь добавить сообщение и файл повторно!!");

// 5. Размер в Кб. < допустимого
$fotoksize=round($fotosize/10.24)/100; // размер ЗАГРУЖАЕМОГО ФОТО в Кб.
$fotomax=round($max_f_size/10.24)/100; // максимальный размер фото в Кб.
if ($fotoksize>$fotomax) exit("Вы превысили допустимый размер фото! <BR><B>Максимально допустимый</B> размер фото: <B>$fotomax </B>Кб.<BR> <B>Вы пытаетесь</B> загрузить изображение: <B>$fotoksize</B> Кб!");

// 6. "Габариты" аватара > 150 х 150 - ДО свиданья! :-)
$size=getimagesize($_FILES['file']['tmp_name']);
if ($size[0]>150 or $size[1]>150) exit("Не допустимые габариты аватара. Допустимо лишь 150 х 150 px!");

if ($fotosize>"0" and $fotosize<$max_f_size) {
   copy($_FILES['file']['tmp_name'], $avatardir."/".$fotoname);
   print "<br><br>Фото УСПЕШНО загружено: $fotoname (Размер: $fotosize байт)";}
else exit("<B>Файл НЕ ЗАГРУЖЕН - ошибка СЕРВЕРА!
если вы видите сообщение - [function.getimagesize]: Filename cannot be empty, значит у Вас библиотека GD отсутствует, либо старой версии<br>
Иначе, доступ на папку для загрузки выставлен ошибочно, или по условиям хостинга загрузка файлов через http Вам запрещена!
Обратитесь к администратору!<B>");
} // КОНЕЦ блока загрузки аватара


$file=file("$datadir/user.php");
$fp=fopen("$datadir/user.php","a+");
flock ($fp,LOCK_EX);
ftruncate ($fp,0);//УДАЛЯЕМ СОДЕРЖИМОЕ ФАЙЛА 
for ($i=0;$i< sizeof($file);$i++) { if ($ok!=$i) fputs($fp,$file[$i]); else fputs($fp,"$text\r\n"); }
fflush ($fp);//очищение файлового буфера
flock ($fp,LOCK_UN);
fclose($fp);

print"<html><head><link rel='stylesheet' href='$forum_skin/style.css' type='text/css'></head><body>
<script language='Javascript'>function reload() {location = \"index.php\"}; setTimeout('reload()', 1500);</script>
<table width=100% height=80%><tr><td><table border=1 cellpadding=10 cellspacing=0 bordercolor=#224488 align=center valign=center width=60%><tr><td><center>
Спасибо, <B>$name, Ваши данные успешно изменены</B>.<BR><BR>Через несколько секунд Вы будете автоматически перемещены на главную страницу форума.<BR><BR>
<B><a href='index.php'>ДАЛЬШЕ >>></a></B></td></tr></table></td></tr></table></center></body></html>";
exit; }





if ($_GET['event'] =="givmepassword") { // отсылает утеряные данные на мыло

// защита от злостного хакера
if (!isset($_POST['myemail']) or !isset($_POST['myname'])) exit("Из формы НЕ поступили данные!");
$myemail=strtolower($_POST['myemail']); $myemail=replacer($myemail);
$myname =strtolower($_POST['myname']); $myname =replacer($myname);
if (strlen($myemail)>40 or strlen($myname)>40) exit("Длина имени или емайл должна быть менее 40 символов!");

// ГЕНЕРИРУЕМ новый пароль юзера
$len=8; // количество символов в новом пароле
$base='ABCDEFGHKLMNPQRSTWXYZabcdefghjkmnpqrstwxyz123456789';
$max=strlen($base)-1; $pass=''; mt_srand((double)microtime()*1000000);
while (strlen($pass)<$len) $pass.=$base{mt_rand(0,$max)};

$lines=file("$datadir/user.php"); $record="<?die;?>\r\n"; $itogo=count($lines); $i=1; $regenter=FALSE;

do {$rdt=explode("|",$lines[$i]); // проходим по всем пользователям и сверяем данные
if (isset($rdt[1])) { // Если строчка потерялась в скрипте (пустая строка) - то просто её НЕ выводим
$rdt[5]=strtolower($rdt[5]); $rdt[2]=strtolower($rdt[2]);
if ($myemail===$rdt[5] or $myname===$rdt[2]) {$regenter=TRUE; $myemail=$rdt[5]; $myname=$rdt[2]; $passmd5=md5("$pass"); $lines[$i]=str_replace("$rdt[3]","$passmd5",$lines[$i]);}
} //if isset
$record.=$lines[$i];
$i++; } while($i < $itogo);

// узнаём IP-запрашивающего пароль
$ip=""; $ip=(isset($_SERVER['REMOTE_ADDR']))?$_SERVER['REMOTE_ADDR']:0;

// переписываем файл участников - вставляем туда новый пароль
$fp=fopen("$datadir/user.php","a+");
flock ($fp,LOCK_EX);
ftruncate ($fp,0);
fputs($fp,"$record");
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);

// отправка пользователю его имени и пароля на мыло
if ($regenter==TRUE) {
$headers=null; // Настройки для отправки писем
$headers.="From: администратор <$adminemail>\n";
$headers.="X-Mailer: PHP/".phpversion()."\n";
$headers.="Content-Type: text/plain; charset=windows-1251";

// Собираем всю информацию в теле письма
$allmsg=$forum_name.' (данные для восстановления доступа к форуму)'.chr(13).chr(10).
        'Вы, либо кто-то другой с IP-адреса '.$ip.' запросили данные для восстановления доступа к форуму по адресу: '.$forum_url.chr(13).chr(10).chr(13).chr(10).
        'Ваше Имя: '.$myname.chr(13).chr(10).
        'Ваш новый пароль: '.$pass.chr(13).chr(10).chr(13).chr(10).
        'Для входа на форум перейдите по ссылке и введите логин и НОВЫЙ ПАРОЛЬ: '.$forum_url.'?event=login'.chr(13).chr(10).chr(13).chr(10).
        'Изменить Ваш пароль (только после того как войдёте) всегда можно на странице: '.$forum_url.'?event=profile&pname='.$myname.chr(13).chr(10).chr(13).chr(10).
        '* Это письмо сгенерировано роботом, отвечать на него не нужно.'.chr(13).chr(10);
// Отправляем письмо майлеру на съедение ;-)
mail("$myemail", "=?windows-1251?B?" . base64_encode("$forum_name (Данные для восстановления доступа к форуму)") . "?=", $allmsg, $headers);
// если есть участник с введённым емайлом
$msgtoopr="<B>$myname</B>, на Ваш электронный адрес выслано сообщение с именем и паролем доступа к форуму.";
}
// Если нет такого емайла в БД
else $msgtoopr="<B>Участника с таким емайлом или логином</B><BR> на форуме <B>не зарегистрировано!</B>";
print "<html><body><script language='Javascript'>function reload() {location = \"index.php\"}; setTimeout('reload()', 2000);</script>
<BR><BR><BR><center><table border=1 cellpadding=10 cellspacing=0 bordercolor=#224488 width=300><tr><td align=center>
<font style='font-size: 15px'>$msgtoopr Через несколько секунд Вы будете автоматически перемещены на главную страницу.
Если этого не происходит, нажмите <B><a href='index.php'>здесь</a></B></font>.</td></tr></table></center><BR><BR><BR></body></html>";
exit; }






if ($_GET['event']=="moresmiles") { // ДОБАВЛЕНИЕ ВСЕХ смайлов из директории SMILE

$lines=null; unset($lines); if (!is_dir("smile/")) exit("папка smile не существует.");
$i=0; if ($handle = opendir("smile/")) {
while (($file = readdir($handle)) !== false)
if (!is_dir($file)) {$lines[$i]=$file; $i++;}
closedir($handle);
}
if (!isset($lines)) exit("В папке smile НЕТ смайлов! Обратитесь к админу - пусть скинет.");
$itogo=count($lines); $k=0; $text=null;
print"<html><head><meta http-equiv='Content-Type' content='text/html; charset=windows-1251'><meta http-equiv='Content-Language' content='ru'></head><body>
<script language=\"JavaScript\">function setSmile(symbol) { obj = opener.document.REPLIER.msg; obj.value = obj.value + symbol; }</script>
<center><b>Дополнительные смайлы</b><br>";
do {
$rdt=explode(".",$lines[$k]);
if ($rdt[1]=="jpg" or $rdt[1]=="gif") {print"<a href=\"javascript:setSmile('[img]$forum_url/smile/$lines[$k][/img] ')\"><img src='smile/$lines[$k]' border=0></a>&nbsp; ";}
$k++;
} while ($k<$itogo);
exit("<BR><a href='' onClick='self.close()'><b>Закрыть окно</b></a></center><small>P.S. Администратор! Чтобы здесь появились новые смайлы - просто закачай любые смайлы в папку".$forum_url."smile/</small></body></html>");}




// ----- Шапка для всех страниц форума

if (isset($_COOKIE['wrfcookies'])) {
$wrfc=$_COOKIE['wrfcookies']; $wrfc=htmlspecialchars($wrfc); $wrfc=stripslashes($wrfc);
$wrfc = explode("|", $wrfc);
$wrfname=$wrfc[0];$wrfpass=$wrfc[1];$wrftime1=$wrfc[2];$wrftime2=$wrfc[3];
if (time()>($wrftime1+50)) {$tektime=time();$wrfcookies="$wrfc[0]|$wrfc[1]|$tektime|$wrftime1";setcookie("wrfcookies", $wrfcookies, time()+1728000);}}
 else {unset($wrfname); unset($wrfpass);}

// -----

$frname=""; $frtname=""; //include("$forum_skin/top.html"); addtop(); // подключаем ШАПКУ форума









if ($_GET['event'] =="deletemsg") { // УДАЛЕНИЕ СВОЕГО СООБЩЕНИЯ = 10.08.2012 г.

if (!isset($_GET['username']) or !isset($_GET['id'])) exit("В адресе должен прийти ID темы и имя участника! Попытка взлома.");

$username=urldecode($_GET['username']); // РАСКОДИРУЕМ имя пользователя, пришедшее из GET-запроса.
$id=replacer($_GET['id']); // получаем идентификатор темы
if ((!ctype_digit($id)) or (strlen($id)!=7)) exit("<B>$back. Попытка взлома! Идентификатор тема должен содержать только 7 цифр!</B>");

$lines=file("$datadir/user.php"); $i=count($lines); $mlines="0"; $filname=null;

// Сверяем имя и пароль в куках с именем и паролем в user.php
do {$i--; $rdt=explode("|",$lines[$i]);
if (isset($rdt[1])) { // Если строчка потерялась в скрипте (пустая строка) - то просто её НЕ выводим
if ($username===$rdt[2]) {

if (isset($wrfname) & isset($wrfpass)) { $wrfname=replacer($wrfname); $wrfpass=replacer($wrfpass);
if ($wrfname===$rdt[2] & $wrfpass===$rdt[3]) {

$mlines=file("$datadir/$id.dat"); $maxi=count($mlines)-1;

$dt=explode("|",$mlines[$maxi]);
// Если последнее сообщение в теме написал наш зареганный юзер, то удаляем последнее сообщение
// иначе обнуляем строку и ничего не удалим далее
if ($dt[8]==$username and $dt[6]==TRUE and $maxi>0) { $filname=$dt[13]; $zag=$dt[5]; unset($mlines[$maxi]); $maxi--;} else $mlines="0";
$i=1;
} } } }
} while($i > "1");

if ($mlines!="0") {
if (is_file("$filedir/$filname")) unlink("$filedir/$filname"); // Удаялем прикреплённый файл
$fp=fopen("$datadir/$id.dat","a+");
flock ($fp,LOCK_EX);
ftruncate ($fp,0);//УДАЛЯЕМ СОДЕРЖИМОЕ ФАЙЛА
for ($i=0;$i<=$maxi;$i++) fputs($fp,$mlines[$i]);
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);

// -1 к репе и -1 к сообщению юзера!
$ufile="$datadir/userstat.dat"; $ulines=file("$ufile"); $ui=count($ulines)-1; $ulinenew=""; $fileadd=0;
if ($filname!=null) $fileadd=$repaaddfile; // Если юзер удаляет файл, то ему ещё -Х в РЕПУ
$tektime=time(); $ip=$_SERVER['REMOTE_ADDR']; // определяем IP юзера
for ($i=0;$i<=$ui;$i++) {$udt=explode("|",$ulines[$i]);
if ($udt[2]==$username) { // Ищем юзера по имени в файле userstat.dat
$udt[6]--; $udt[7]=$udt[7]-$fileadd-$repaaddmsg;
$ulines[$i]="$udt[0]|$tektime|$udt[2]|$udt[3]|$udt[4]|$udt[5]|$udt[6]|$udt[7]|$udt[8]|$udt[9]|$ip|$udt[11]|\r\n";}
$ulinenew.="$ulines[$i]";}
// Пишем данные в файл
$fp=fopen("$ufile","w");
flock ($fp,LOCK_EX);
fputs($fp,"$ulinenew");
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);

// Записываем данные в файл REPA.DAT
$repa=-$fileadd; $repa=$repa-$repaaddmsg; $pochemu="За удаление сообщения в теме <a href='index.php?id=$id' target=_blank>$zag</a>";
$fp=fopen("$datadir/repa.dat","a+");
flock ($fp,LOCK_EX);
fputs($fp,"$tektime|$repa|$wrfname||$pochemu||||\r\n");
fflush ($fp);
flock ($fp,LOCK_UN);
fclose($fp);

$rezult="было успешно удалено.";} else $rezult="<font color=red><B>НЕ БЫЛО УДАЛЕНО, ТАК КАК ОНО ЕДИНСТВЕННОЕ В ТЕМЕ!</B></font>";

print "<script language='Javascript'>function reload() {location = \"index.php?id=$id\"}; setTimeout('reload()', 2500);</script>
<table width=100% height=80%><tr><td><table border=1 cellpadding=10 cellspacing=0 bordercolor=#224488 align=center valign=center width=60%><tr><td><center>
Внимание, <B>$username</B>, Ваше сообщение $rezult<BR><BR>Через несколько секунд Вы будете автоматически перемещены в текущую тему.<BR><BR>
<B><a href='index.php?id=$id'>ДАЛЬШЕ >>></a></B></td></tr></table></td></tr></table></center></body></html>";
exit; }








if ($_GET['event']=="who") { // ПРОСМОТР УЧАСТНИКОВ

// если незареган - не пускаем
if (!isset($_COOKIE['wrfcookies'])) exit("$back <center><table class=forumline width=700><tr><th class=thHead colspan=4 height=25>Доступ ограничен</th></tr><tr class=row2><td class=row1><center><BR><BR><B><span style='FONT-SIZE: 14px'>Для просмотра данных пользователей необходимо зарегистрироваться.</B></center></td></table></center>");

$t1="row1";
$alllines=file("$datadir/user.php"); $allslines=file("$datadir/userstat.dat");
unset($alllines[0]); unset($allslines[0]); // Удаляем первую (служебную строку)
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

// Если есть совпадение в строке - присваиваем флагу значение 1
if (strlen($repa)>0) {if ($udt[7]>$repa) $flag=1;}
if (strlen($pol)>0) {if ($dt[6]==$pol) $flag=1;}
//print"$dt[6]=$pol-$flag<br>";
if ($dayreg>0 and $dayreg<121) {$delta=$dt[1]+2592000*$dayreg; if ($delta<$tektime) $flag=1;} // месяц * dayreg

if ($dt[13]!="" and $interes!="") {if (stristr($dt[13],$interes)) $flag=1;}
if ($dt[11]!="" and $url!="") {if (stristr($dt[11],$url)) $flag=1;}
if ($dt[12]!="" and $from!="") {if (stristr($dt[12],$from)) $flag=1;}

// если было хоть одно соврадение, включаем участника в массив участников
if ($flag==1) {$lines[$j]=$alllines[$i]; $slines[$j]=$allslines[$i]; $flag=0; $j++;}
$i++; 
} while($i<$allmaxi);

//print"<PRE>"; print_r($slines); print_r($lines); exit;

$fadd="&pol=$pol&interes=$interes&url=$url&from=$from&repa=$repa&$dayreg=$dayreg";
} else {$fadd=""; $lines=$alllines; $slines=$allslines;} // если поиск не задан, сразу присваиваем массив

if (!isset($lines)) $maxi=0; else $maxi=count($lines);

// Исключаем ошибку вызова несуществующей страницы
if (!isset($_GET['page'])) $page=1; else { $page=$_GET['page']; if (!ctype_digit($page)) $page=1; if ($page<1) $page=1; }
$maxpage=ceil(($maxi)/$uq); if ($page>$maxpage) $page=$maxpage;

print"
<form action='tools.php?event=who' method=GET><div align=right>
<input type=hidden name=event value='who'>
Фильтры: <SELECT name=pol><option value=''>Пол</option><OPTION value='1'>Мужской</OPTION><OPTION value='0'>Женский</OPTION></SELECT>

<SELECT name=repa><option value=''>Репутация</option><OPTION value='$userrepa[1]'> > $userrepa[1]</OPTION>
<OPTION value='$userrepa[2]'> > $userrepa[2]</OPTION><OPTION value='$userrepa[3]'> > $userrepa[3]</OPTION>
<OPTION value='$userrepa[4]'> > $userrepa[4]</OPTION><OPTION value='$userrepa[5]'> > $userrepa[5]</OPTION>
<OPTION value='$userrepa[6]'> > $userrepa[6]</OPTION><OPTION value='$userrepa[7]'> > $userrepa[7]</OPTION></SELECT>

<SELECT name=dayreg><option value=''>Дате регистрации</option>
<OPTION value='1'> > месяца</OPTION><OPTION value='6'> > полугодия</OPTION><OPTION value='12'> > года</OPTION>
<OPTION value='36'> > 3 лет</OPTION><OPTION value='60'> > 5 лет</OPTION><OPTION value='120'>ДАЖЕ не выбирайте!</OPTION></SELECT>

<B>Интересам:</B> <input type=text name=interes value='$interes' class=post maxlength=50 size=25>
<B>Сайту:</B> <input type=text name=url value='$url' class=post maxlength=50 size=25>
<B>Откуда:</B> <input type=text name=from value='$from' class=post maxlength=50 size=25>
<input type=submit class=mainoption value='OK'></form></div><br>";

print"<table width=100% cellpadding=3 cellspacing=1 class=forumline><tr> 
<th class=thCornerL height=25 width=20>№</th>
<th class=thCornerL><small><a href='tools.php?event=who&page=$page&pol=1' style='text-decoration:none'>Ф</a> Пол / Имя</small></th>
<th class=thTop><small>Статус</small></th>
<th class=thTop><small>Репа</small></th>
<th class=thTop><small>ЛС на Е-майл</small></th>
<th class=thTop><small>ПС на фруме</small></th>
<th class=thTop><small>Регистрация</small></th>
<th class=thTop><small>День рождения</small></th>
<th class=thTop><small>Интересы</small></th>
<th class=thTop><small>Сайт</small></th>
<th class=thCornerR><small>Откуда</small></th>
</tr>";
if ($allmaxi=="0") {print"<TR><TD class=$t1 colspan=8 align=center>Участников не зарегистрировано</TD></TR>";
} else {

$fm=$uq*($page-1); if ($fm>$maxi) $fm=$maxi-$uq; //if ($fm<0) $fm=0;
$lm=$fm+$uq; if ($lm>=$maxi) $lm=$maxi-1;

if (isset($lines)) {

do { $dt=explode("|",$lines[$fm]); $udt=explode("|",$slines[$fm]);
$fm++; $num=$fm;

if (isset($dt[1])) { // Если строчка ПУСТА, то просто её НЕ выводим

$codename=urlencode($dt[2]); // Кодируем имя в СПЕЦФОРМАТ, для поддержки корректной передачи имени через GET-запрос.
if (isset($wrfname)) {$wfn="<a href=\"tools.php?event=profile&pname=$codename\">$dt[2]</a>";
$mls="<form method='post' action='tools.php?event=mailto' target='email' onclick=\"window.open('tools.php?event=mailto','email','width=600,height=350,left=100,top=100');return true;\">
<input type=hidden name='email' value='$dt[5]'><input type=hidden name='name' value='$dt[2]'><input type=hidden name='id' value=''>
<input type=image src='$forum_skin/ico_pm.gif' alt='личное сообщение'></form>";} else {$wfn="$dt[2]"; $mls="заблокировано";}

if (strlen($dt[16])!=TRUE) $dt[16]="<B><font color=red>ожидание активации</font></B>";
if ($dt[6]==TRUE) $add="polm.gif"; else $add="polg.gif";
$dt[1]=date("d.m.y",$dt[1]);
$codename=urlencode($dt[2]);

// СТАТУС здесь в userstat.dat или, в зависимости от репы, в $userstatus[$i]
if (strlen($udt[9])>1) $status=$udt[9]; else { $si=0;
for ($si=0;$si<8;$si++) if ($udt[7]>=$userrepa[$si]) $stp=$si;
$status=$userstatus[$stp];}

print"<tr>
<td class=$t1>$num</td>
<td class=$t1 nowrap><b><img src='$forum_skin/$add' border=0> $wfn</b></td>
<td class=$t1 align=center>$status</td>
<td class=$t1 nowrap><B>$udt[7]</B> <A href='#m1' onclick=\"window.open('tools.php?event=repa&name=$dt[2]&who=1','repa','width=600,height=600,left=50,top=50,scrollbars=yes')\">&#177;</A></td>
<td class=$t1 align=center>$mls</td>
<td class=$t1 align=center><form action='pm.php?id=$codename' method=POST name=citata><input type=image border=0 src='data-pm/pm.gif' alt='Отправить ПЕРСОНАЛЬНОЕ СООБЩЕНИЕ'></form></td>
<td class=$t1 align=center>$dt[1]</td>
<td class=$t1 align=center>$dt[7]</td>
<td class=$t1>$dt[13]</td>
<td class=$t1><small>$dt[11]</small></td>
<td class=$t1>$dt[12]</td></tr>";
if ($t1=="row1") $t1="row2"; else $t1="row1";

} // если строчка потерялась

} while($fm <= $lm);
} // if isset($lines)
} // конец Если файл userdat.php пуст

echo'</table><BR>';

$maxi--;
// формируем переменную $pageinfo - со СПИСКОМ СТРАНИЦ
$pageinfo=""; $addpage=""; $maxpage=ceil(($maxi+1)/$uq); if ($page>$maxpage) $page=$maxpage;
$pageinfo.="<div style='padding:6px;' class=pgbutt>Страницы: &nbsp;";
if ($page>3 and $maxpage>5) $pageinfo.="<a href=tools.php?event=who$fadd>1</a> ... ";
$f1=$page+2; $f2=abs($page-2); if ($f2=="0") $f2=1; if ($page>=$maxpage-1) $f1=$maxpage;
if ($maxpage<=5) {$f1=$maxpage; $f2=1;}
for($i=$f2; $i<=$f1; $i++) { if ($page==$i) $pageinfo.="<B>$i</B> &nbsp;"; 
else {if ($i!=1) $addpage="&page=$i"; $pageinfo.="<a href=tools.php?event=who&page=$i$fadd>$i</a> &nbsp;";} }
if ($page<=$maxpage-3 and $maxpage>5) $pageinfo.="... <a href=tools.php?event=who&page=$maxpage$fadd>$maxpage</a>";
$pageinfo.='</div>';

print "$pageinfo
<div align=right>Всего зарегистрировано участников - <B>$allmaxi</B></div><BR>";}





if ($_GET['event'] =="profile") { // РЕДАКТИРОВАНИЕ ПРОФИЛЯ

if (!isset($_GET['pname'])) exit("Попытка взлома.");
$pname=urldecode($_GET['pname']); // РАСКОДИРУЕМ имя пользователif (!ctype_digit($userpn) or strlen($userpn)>4) exit("<B>$back. Попытка взлома. Хакерам здесь не место!");я, пришедшее из GET-запроса.
$lines=file("$datadir/user.php"); $i = count($lines); $use="0"; $userpn="0";
do {$i--; $rdt=explode("|", $lines[$i]);

if (isset($rdt[1])) { // Если строчка потерялась в скрипте (пустая строка) - то просто её НЕ выводим

if (strlen($rdt[16])=="6" and ctype_digit($rdt[16])) $rdt[16]="<B><font color=red>ожидание активации</font></B>";

if ($pname===$rdt[2]) { $userpn=$i;

// Считываем статистику сообщений/репы юзера
$jfile="$datadir/userstat.dat"; $jlines=file("$jfile"); $uj=count($jlines)-1; $msjitogo=0;
for ($j=0;$j<=$uj;$j++) {$udt=explode("|",$jlines[$j]); $msjitogo=$msjitogo+$udt[6]; if ($udt[2]==$rdt[2]) {$msguser=$udt[6]; $temaded=$udt[5]; $repa=$udt[7];}}
$msgaktiv=round(10000*$msguser/$msjitogo)/100;

$aktiv=$rdt[1]; $tekdt=time(); $aktiv=round(($tekdt-$aktiv)/86400);
if ($aktiv<=0) $aktiv=1; $aktiv=round(100*$msguser/$aktiv)/100;
$rdt[1]=date("d.m.Y г.",$rdt[1]);

if (isset($wrfname) & isset($wrfpass)) { $wrfname=replacer($wrfname); $wrfpass=replacer($wrfpass);
if ($rdt[6]==TRUE) $pol="мужчина"; else $pol="женщина";

if ($wrfname===$rdt[2] & $wrfpass===$rdt[3]) {
print "<center><span class=maintitle>Регистрационные данные</span><br>

<br><form action='tools.php?event=reregist' name=creator method=post enctype=multipart/form-data>

<table border=1 cellpadding=2 cellspacing=0 width=100% class=forumline>
<tr><th class=thHead colspan=2 height=25 valign=middle>Регистрационная информация</th></tr>
<tr><td class=row2 colspan=2><span class=gensmall>Поля отмеченные * обязательны к заполнению, если не указано обратное</span></td></tr>
<tr><td class=row1 width=35%><span class=gen>Имя участника:</span><span class=gensmall><br>Русские ники РАЗРЕШЕНЫ</span></td><td class=row2><span class=nav>$rdt[2]</span></td></tr>
<tr><td class=row1><span class=gen>Ваш пол:</span><br></td><td class=row2><span class=gen>$pol</span><input type=hidden value='$rdt[6]' name=pol></td></tr>
<tr><td class=row1><span class=gen>Ваш пароль: *</span></td><td class=row2><input class=inputmenu type=text value='скрыт' maxlength=10 name=newpassword size=15><input type=hidden class=inputmenu value='$rdt[3]' name=pass>
(если хотите сменить, то введите новый пароль, иначе оставьте как есть!)</td></tr>

<tr><td class=row1><span class=gen>Адрес e-mail: *</span><br><span class=gensmall>Введите существующий электронный адрес! Форум защищён от роботов-спамеров.</span></td>
<td class=row2> <input type=text class=post style='width: 200px' value='$rdt[5]' name=email size=25 maxlength=50></td></tr>

<tr><td class=row1><span class=gen>Дата регистрации:</span></td><td class=row2><span class=gen>$rdt[1]</td></tr>
<tr><td class=row1><span class=gen>Репутация: </span><br></td><td class=row2><B>$repa</B> [<A href='#1' onclick=\"window.open('tools.php?event=repa&name=$wrfname&who=$userpn','repa','width=600,height=600,left=50,top=50,scrollbars=yes')\">Посмотреть статистику изменения</A>]</td></tr>
<tr><td class=row1><span class=gen>Активность:</span></td><td class=row2><span class=gen>Тем создано: <B>$temaded</B>, всего сообщений: <B>$msguser</B> [<B>$msgaktiv%</B> от общего числа / <B>$aktiv</B> сообщений в сутки]</span></td></tr>

<td class=row1><span class=gen>Персональные сообщения</span><br><span class=gensmall><td class=row2>";

if (is_file("data-pm/$wrfname.dat")) {$linespm=file("data-pm/$wrfname.dat"); $pmi=count($linespm); print" <img src=\"$forum_skin/icon_mini_profile.gif\" border=0 hspace=3 />[<a href='pm.php?readpm&id=$wrfname'><font color=red><B>$pmi сообщения в ПМ</b></font></a>]";} else echo'сообщений нет';


print"</span></td>
</tr><tr>
<td class=catSides colspan=2 height=28>&nbsp;</td>
</tr><tr>
<th class=thSides colspan=2 height=25 valign=middle>Немного о себе</th>
</tr><tr>
<td class=row1><span class=gen>День варенья:</span><br><span class=gensmall>Введите день рождения в формате: ДД.ММ.ГГГГГ, если не секрет.</span></td>
<td class=row2><input type=text name=dayx value='$rdt[7]' class=post style='width: 100px' size=10 maxlength=18></td>
</tr><tr>
<td class=row1><span class=gen>Номер в ICQ:</span><br><span class=gensmall>Введите номер ICQ, если он у Вас есть.</span></td>
<td class=row2><input type=text value='$rdt[10]' name=icq class=post style='width: 100px' size=10 maxlength=10></td>
</tr><tr>
<td class=row1><span class=gen>Домашняя страничка:</span><br><span class=gensmall>Если у Вас есть домашняя или любимая страничка в Интернете, введите URL этой странички.</span></td>
<td class=row2><input type=text value='$rdt[11]' class=post style='width: 500px' name=www size=25 maxlength=70 value='http://' /></td>
</tr><tr>
<td class=row1><span class=gen>Откуда:</span><br><span class=gensmall>Введите место жительства (Страна, Область, Город).</span></td>
<td class=row2><input type=text class=post style='width: 500px' value='$rdt[12]' name=about size=25 maxlength=70></td>
</tr><tr>
<td class=row1><span class=gen>Интересы:</span><br><span class=gensmall>Вы можете написать о ваших интересах</span></td>
<td class=row2><input type=text class=post style='width: 500px' value='$rdt[13]' name=work size=35 maxlength=70></td>
</tr><tr>
<td class=row1><span class=gen>Подпись:</span><br><span class=gensmall>Введите Вашу подпись, не используйте HTML</span></td>
<td class=row2><input type=text class=post style='width: 500px' value='$rdt[14]' name=write size=35 maxlength=70></td>
</tr><tr>
<td class=row1><span class=gen>Аватар:</span><br><span class=gensmall>Выберите автарар (картинку), которая будет отображаться рядом с вашим именем.</span></td>
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
<td class=row1><span class=gen>Загрузить свой АВАТАР:</span><br><span class=gensmall>Введите локальный путь к Вашему аватару. <BR>Разрешается использовать картинки: <BR> - разрешение не более <B>120 х 120</B>, <BR>- расширением только <B>gif, png, jpg или jpeg</B>, <BR> - размером менее <B>$maxfsize Кб</B>. </B></span></td>
<td class=row2><input type=file name=file class=post style='width: 400px' size=35 maxlength=150></td>
</tr><tr><tr><td colspan=2>
<input type=hidden name=name value='$rdt[2]'>
<input type=hidden name=oldpass value='$rdt[3]'>
</td></tr><tr>
<td class=catBottom colspan=2 align=center height=28><input type=submit name=submit value='Сохранить изменения' class=mainoption /></td>
</tr></table></form>"; $use="1"; }


if ($use!="1") {

////////////// Передалать  строки со статусом!!!! и $rdt[1] - дата регистрации!!!!!!!!!
//if (strlen($rdt[13])<2) $rdt[13]=$user_name;
if (is_file("avatars/$rdt[15]")) $avpr="$rdt[15]"; else $avpr="noavatar.gif";
if ($rdt[6]==TRUE) $pol="мужчина"; else $pol="женщина";
print "<center><span class=maintitle>Профиль участника</span><br><br><table cellpadding=5 cellspacing=1 width=100% class=forumline>
<tr><th class=thHead colspan=2 height=25 valign=middle>Регистрационная информация</th></tr>
<tr><td class=row1 width=30%><span class=gen>Имя участника:</span></td><td class=row2><span class=nav>$rdt[2]</span></td></tr>
<tr><td class=row1><span class=gen>Репутация: </span><br></td><td class=row2><B>$repa</B> [<A href='#1' onclick=\"window.open('tools.php?event=repa&name=$rdt[2]&who=$userpn','repa','width=600,height=600,left=50,top=50,scrollbars=yes')\">Оценить &#177;</A>]</td></tr>
<tr><td class=row1><span class=gen>Активность:</span></td><td class=row2><span class=gen>Тем создано: <B>$temaded</B>, всего сообщений: <B>$msguser</B> [<B>$msgaktiv%</B> от общего числа / <B>$aktiv</B> сообщений в сутки]</span></td></tr>
<tr><td class=row1><span class=gen>Отправить личное сообщение на e-mail: </span><br></td><td class=row2><form method='post' action='tools.php?event=mailto' target='email' onclick=\"window.open('tools.php?event=mailto','email','width=600,height=350,left=100,top=100');return true;\"><input type=hidden name='email' value='$rdt[5]'><input type=hidden name='name' value='$rdt[2]'><input type=hidden name='id' value=''><input type=image src='$forum_skin/ico_pm.gif' alt='личное сообщение'></form></td></tr>
<tr><td class=row1><span class=gen>Написать персональное сообщение (сюда на форум):</span><br></td><td class=row2><form action='pm.php?id=$rdt[2]' method=POST name=citata><input type=image border=0 src='data-pm/pm.gif' alt='Отправить ПЕРСОНАЛЬНОЕ СООБЩЕНИЕ'></form></span></td></tr>
<tr><td class=row1><span class=gen>Дата регистрации:</span></td><td class=row2><span class=gen>$rdt[1]</span></td></tr>
<tr><td class=row1><span class=gen>Статус:</span></td><td class=row2><span class=gen>$rdt[13]</span></td></tr>
<tr><td class=row1><span class=gen>Пол:</span></td><td class=row2><span class=gen>$pol</span></td></tr>
<tr><td class=row1><span class=gen>День Варенья:</span><br></td><td class=row2><span class=gen>$rdt[7]</span></td></tr>
<tr><td class=row1><span class=gen>Номер в ICQ:</span><br></td><td class=row2><span class=gen>$rdt[10]</td></tr>
<tr><td class=row1><span class=gen>Домашняя страничка:</span></td><td class=row2><span class=gen><a href='$rdt[11]' target='_blank'>$rdt[11]</a></td></tr>
<tr><td class=row1><span class=gen>Откуда</span> (<span class=gensmall>Место жительства, город, страна.):</span></td><td class=row2><span class=gen>$rdt[12]</td></tr>
<tr><td class=row1><span class=gen>Интересы:</span></td><td class=row2><span class=gen>$rdt[13]</td></tr>
<tr><td class=row1><span class=gen>Подпись:</span></td><td class=row2><span class=gen>$rdt[14]</td></tr>
<tr><td class=row1><span class=gen>Аватар:</span></td><td class=row2 height=120><img src='./avatars/$avpr' border=0 hspace=15></td></tr>
</td></tr></table><BR>"; $use="1";}

}
}
} // if
} while($i > "1");

if (!isset($wrfname)) exit("<BR><BR><font size=+1><center>Только зарегистрированные участники форума могут просматривать данные профиля!");

if ($use!="1") { // в БД такого ЮЗЕРА НЕТ - его админ удалил
print"<center><table width=600 height=300 class=forumline>
<tr><th class=thHead height=25 valign=middle>Пользователь НЕ ЗАРЕГИСТРИРОВАН</th></tr>
<tr><td class=row1 align=center><B>Уважаемый посетитель!</B><BR><BR> 
Извините, но участник с таким - <B>логином на форуме не зарегистрирован.</B><BR><BR>
Скорее всего, <B>его удалил администратор</B>.<BR><BR>
<B>Посмотреть других участников</B> можно <B><a href='tools.php?event=who'>здесь</a>.</B><br><br>
<B>Перейти на главную</B> страницу форума можно по <B><a href='$forum_url'>этой ссылке</a></B>
</TD></TR></TABLE>"; }
}






if ($_GET['event']=="reg") {
if (!isset($_POST['rulesplus'])) {
echo'
<form action="tools.php?event=reg" method=post>
<center><span class=maintitle>Правила и условия регистрации</span><br><br>
<table cellpadding=8 cellspacing=1 width=100% class=forumline><tr><th class=thHead height=25 valign=middle>Правила работы с форумом</th></tr><tr>
<td class=row1><span class=gen>';
if (is_file("$datadir/pravila.html")) include"$datadir/pravila.html";
echo'</td></tr><tr><td class=row2><INPUT type=checkbox name=rulesplus><B>Я ознакомился с правилами и условиями, и принимаю их.</B></td></tr><tr>
<td class=catBottom align=center height=28><input type=submit value="Продолжить регистрацию" class=mainoption></td>
</tr></table>
</form>'; 
} else {

print"<center><span class=maintitle>Регистрация на форуме</span><br>
<br><form action='tools.php?event=regnxt' method=post>

<table cellpadding=3 cellspacing=1 width=100% class=forumline><tr>
<th class=thHead colspan=2 height=25 valign=middle>Регистрационная информация</th>
</tr><tr>
<td class=row1 width=35%><span class=gen>Имя участника:</span><span class=gensmall><br>Разрешено использовать только русские, латинские буквы, цифры и знак подчёркивания</span></td>
<td class=row2><input type=text class=post style='width:200px' name=name size=25 maxlength=$maxname></td>
</tr><tr>
<td class=row1><span class=gen>Ваш пароль:</span></td>
<td class=row2><input type=password class=post style='width:200px' name=pass size=25 maxlength=25></td>
</tr><tr>
<td class=row1><span class=gen>Адрес e-mail:</span><br><span class=gensmall>Введите существующий электронный адрес! На Ваш емайл будет отправлено сообщение с кодом активации.</span></td>
<td class=row2><input type=text class=post style='width: 200px' name=email size=25 maxlength=50></td>
</tr><tr>
<td class=row1><span class=gen>Ваш пол:</span><br></td>
<td class=row2><input type=radio name=pol value='1'checked> мужчина&nbsp;&nbsp; <input type=radio name=pol value='0'> женщина</td>
</tr><tr><TD class=row2>Защитный код</TD><TD class=row2>";

if ($antispam==TRUE) nospam(); // АНТИСПАМ !

echo'</td></tr><tr>
<td class=row2 colspan=2><span class=gensmall>* Все поля обязательны к заполнению<BR>
** Ваш пароль будет также отправлен на адрес электронной почты, который Вы определите</span></td>
</tr><tr>
<td class=catBottom colspan=2 align=center height=28><input type=submit value="Продолжить" class=mainoption></td>
</tr></table></form>';
}
}



if ($_GET['event']=="find") { // ПОИСК
$minfindme="3"; //минимальное кол-во символов в слове для поиска
echo'<BR><form action="tools.php?event=go&find" method=POST>
<center><table class=forumline align=center width=700>
<tr><th class=thHead colspan=4 height=25>Поиск</th></tr>
<tr class=row2>
<td class=row1>Запрос: <input type="text" style="width: 250px" class=post name=findme size=30></TD>
<TD class=row1>Тип: <select style="FONT-SIZE: 12px; WIDTH: 120px" name=ftype>
<option value="0">&quotИ&quot
<option value="1" selected>&quotИЛИ&quot
<option value="2">Вся фраза целиком
</select></td>
<td class=row1><INPUT type=checkbox name=withregistr><B>С учётом РЕГИСТРА</B></TD>
<input type=hidden name=gdefinder value="1">
</tr>';

print"<TR><TD class=row1 colspan=4>ИЛИ найти все сообщения зарегистрированного пользователя: 
<SELECT name=user style='FONT-SIZE: 14px; WIDTH: 250px'><OPTION value='0' selected> - - Выбрать пользователя - -</OPTION>";
$slines = file("data/user.php"); $smax=count($slines); $i="1"; do {
$slines[$i]=replacer($slines[$i]); $dts=explode("|",$slines[$i]);
print "<OPTION value=\"$dts[2]\">$dts[2]</OPTION>\r\n"; $i++; } while($i < $smax);
echo'</SELECT></TD>


<tr class=row1>
<td class=row1 colspan=4 width="100%">
Язык запросов:<br><UL>
<LI><B>&quotИ&quot</B> - должны присутствовать оба слова;</LI><br>
<LI><B>&quotИЛИ&quot</B> - есть ХОТЯ БЫ одно из слов;</LI><br>
<LI><B>&quotВся фраза целиком&quot</B> - в искомом документе ищите фразу на 100% соответствующую вашему запросу;</LI><BR><BR>
<LI><B>&quotС учётом РЕГИСТРА&quot</B> - поиск ведётся с учётом введённого ВАМИ РЕГИСТРА;</LI><BR><BR>
</UL>Скрипт ищет все данные, которые начинаются с введенной вами строки. Например, при запросе &quotфорум&quot будут найдены слова &quotфорум&quot, &quotфорумы&quot, &quotфорумом&quot и многие другие.
</td>
</tr><tr><td class=row1 colspan=4 align=center height=28><input type=submit class=post value="  Поиск  "></td></form>
</tr></table><BR><BR>';

print "Ограничение на поиск: <BR> - минимальное кол-во символов: <B>$minfindme</B>";
}





if (isset($_GET['find'])) {

//exit("Поиск временно не работает!");
$minfindme="2"; //минимальное кол-во символов в слове для поиска
$time=explode(' ', microtime()); $start_time=$time[1]+$time[0]; // считываем начальное время запуска поиска

$gdefinder="1"; $ftype=$_POST['ftype']; 
if (!ctype_digit($ftype) or strlen($ftype)>2) exit("<B>$back. Попытка взлома. Хакерам здесь не место.</B>");
if (!isset($_POST['withregistr'])) $withregistr="0"; else $withregistr="1";

if ($_POST['user']!="0") {$findme=$_POST['user']; $gdefinder="3"; $ftype="2"; $withregistr="1";} //  Если выбран поиск по имени юзера
else $findme=$_POST['findme']; 

$findme=replacer($findme); // Защита от взлома
$findmeword=explode(" ",$findme); // Разбиваем $findme на слова
$wordsitogo=count($findmeword);
$findme=trim($findme); // Вырезает ПРОБЕЛьные символы 
if ($findme == "" || strlen($findme) < $minfindme) exit("$back Ваш запрос пуст, или менее $minfindme символов!</B>");

// Открываем файл с темами формума и запоминаем имена файлов с сообщениями

setlocale(LC_ALL,'ru_RU.CP1251'); // ! РАЗРЕШАЕМ РАБОТУ ФУНКЦИЙ, работающих с регистором и с РУССКИМИ БУКВАМИ


// ПЕРВЫЙ цикл - считаем кол-во форумов (записываем в переменную $itogofid)
$mainlines=file("$datadir/wrforum.dat");$i=count($mainlines); $itogofid="0";$number="0"; $oldid="0"; $nump="0";
do {$i--; $dt=explode("|",$mainlines[$i]);
if ($dt[3]==FALSE) { $maxzd=$dt[9];
if (!ctype_digit($maxzd)) $maxzd=0;  // считываем ЗВЁЗДы раздела из файла
if ($maxzd<1) {$itogofid++; $fids[$itogofid]=$dt[2]; }} // $itogofid - общее кол-во форумов
} while($i > "0");


// ВТОРОЙ цикл - открываем файл с топиком (если он существует) и сохраняем в переменную $topicsid все имена тем
do { $fid=$fids[$itogofid];
if (is_file("$datadir/$fid.dat")) {
$msglines=file("$datadir/$fid.dat");

unset($topicsid); if (count($msglines)>0) { $lines=file("$datadir/$fid.dat"); $i=count($lines);
do {$i--; $dt=explode("|",$lines[$i]); $topicsid[$i]="$dt[2]$dt[3]";} while($i > "0"); }


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
if ($gdefinder=="1") {$msgmass=array($dt[14]); $gi="1"; $add="е <strong>Текст</strong> ";}
if ($gdefinder=="2") {$msgmass=array($dt[3],$dt[4]); $gi="2"; $add="ях <B>Текст и Заголовок</B> ";}
if ($gdefinder=="3") {$msgmass=array($dt[8]); $gi="1"; $add="е <B>Автор</B> ";}
if ($gdefinder=="4") {$msgmass=array($dt[3]); $gi="1"; $add="е <B>Заголовок</B> ";}

// Цикл по местам поиска (0,1,2,3,4)
do {$gi--;

$msg=$dt[14];
$msdat=$msgmass[$gi];
$stroka="0"; $wi=$wordsitogo;

// ЦИКЛ по КАЖДОМУ слову запроса !
do {$wi--;


// БЛОК УСЛОВИЙ ПОИСКА
if ($withregistr!="1") // регистронезависимый поиск - cимвол "i" после закрывающего ограничителя шаблона - /
   {
    if ($ftype=="2") 
        { if (stristr($msdat,$findme)) // ПОИСК по "ВСЕЙ ФРАЗЕ ЦЕЛИКОМ" БЕЗ учёта регистра
          { $stroka++; $msg=str_replace($findme," <b><u>$findme</u></b> ",$msg); }
        } else {
           $str1=strtolower($msdat);  
           $str2=strtolower($findmeword[$wi]); 
           if ($str2!="" and strlen($str2) >= $minfindme)
              { if (stristr($str1,$str2)) // ПОИСК БЕЗ учёта регистра при равных прочих условиях
                { $stroka++; $msg=str_replace($findmeword[$wi]," <b><u>$findmeword[$wi]</u></b> ",$msg); }
              }
          }
        }

else  // if ($withregistr!="1")
   {
    if ($ftype=="2")
       {
        if (strstr($msdat,$findme)) // ПОИСК по "ВСЕЙ ФРАЗЕ ЦЕЛИКОМ" C учёта РЕГИСТРА
           {
            $stroka++;
            $msg=str_replace($findme," <b><u>$findme</u></b> ",$msg);
           }
       }
     else {
           if ($msdat!="" and strlen($findmeword[$wi]) >= $minfindme)
              {
               if (strstr($msdat,$findmeword[$wi])) // ПОИСК С учётом РЕГИСТРА при равных прочих условиях
                  {
                   $stroka++;
                   $msg=str_replace($findmeword[$wi]," <b><u>$findmeword[$wi]</u></b> ",$msg);
                  }
              }
          }

   }   // if ($withregistr!="1")

} while($wi > "0"); // конец ЦИКЛа по КАЖДОМУ слову запроса


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
<small><BR>По запросу '<U><B>$findme</B></U>' в пол$add найдено: <HR size=+2 width=99% color=navy>
<BR><form action='tools.php?event=go&find' method=POST>
<table class=forumline align=center width=700>
<tr><th class=thHead colspan=4 height=25>Повторить поиск по сообщению</th></tr>
<tr class=row2>
<td class=row1>Запрос: <input type='text' value='$findme' style='width: 250px' class=post name=findme size=30>
<INPUT type=hidden value='1' name=ftype>
<INPUT type=hidden value='0' name=user>
<input type=hidden name=gdefinder value='1'>
<input type=submit class=post value='  Поиск  '></td></table></form><br>
<table width=100% class=forumline><TR align=center class=small><TH class=thCornerL><B>№</B></TH><TH class=thCornerL width=35%><B>Заголовок</B></TH><TH class=thCornerL width=70%><B>часть сообщения</B></TH><TH class=thCornerL><B>Совпадений<BR> в теме</B></TH></TR>"; $m="1"; }

$in=$iii+1; if ($in>$msg_onpage) {$page=ceil($in/$msg_onpage);} else $page="1"; // расчитываем верную страницу и номер сообщения

if ($oldid!=$id and $number<100) { $number++; $msgnumber=$iii;

if ($nump>1) $anp="$nump"; else $anp="1";
if ($number>1) print"<TD class=row1 align=center>$anp</TD></TR><TR height=25>";

print "<TD class=row1 align=center><B>$number</B></TD>
<TD class=row1><A class=listlink href='index.php?id=$id&page=$page#m$iii' target=_blank>$dt[5]</A></TD>
<TD class=row1>$msgtowrite</TD>";
$printflag="0"; $nump="0";

} else $nump++;

if ($number>=100) { print"</TR></TABLE> * поиск останавливается, при нахождении более 100 вхождений!"; $gi=0; $iii=0; $ii=0; $itogofid=0;}

$oldid=$id;
} // if $printflag==1

} while($gi > "0"); // конец ЦИКЛа по МЕСТУ поиска

} while($iii > "0");
} // если файл с сообщениями НЕПУСТОЙ

} // if is_file("$datadir/$id.dat")
} while($ii > "0");

} // if isset($topicsid)

} // if файл $fid.dat НЕ пуст

$itogofid--;
} while($itogofid > "0");

if (!isset($m)) echo'<table width=80% align=center><TR><TD>По вашему запросу ничего не найдено.</TD></TR></table>';

$time=explode(' ',microtime());
$seconds=($time[1]+$time[0]-$start_time);
echo "</TR></table><HR size=+2 width=99% color=navy><BR><p align=center><small>".str_replace("%1", sprintf("%01.3f", $seconds), "Время поиска: <b>%1</b> секунд.")."</small></p>";

}

} // if isset($_GET['event']) - всё, что делается при наличии переменной $event

?>

</td></tr></table>
<center><small>Powered by <a href="http://www.wr-script.ru" title="Скрипт фотоальбома" class="copyright">WR-Foto</a> &copy; 1.2<br></small></center>
</body>
</html>
