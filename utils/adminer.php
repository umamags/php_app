<?php
/** Adminer - Compact database management
* @link https://www.adminer.org/
* @author Jakub Vrana, https://www.vrana.cz/
* @copyright 2007 Jakub Vrana
* @license https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
* @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
* @version 6.0.2
*/namespace
Adminer;const
VERSION="6.0.2";error_reporting(24575);set_error_handler(function($dd,$fd){return!!preg_match('~^Undefined (array key|offset|index)~',$fd);},E_WARNING|E_NOTICE);$Id=!preg_match('~^(unsafe_raw)?$~',ini_get("filter.default"));if($Id||ini_get("filter.default_flags")){foreach(array('_GET','_POST','_COOKIE','_SERVER')as$W){$em=filter_input_array(constant("INPUT$W"),FILTER_UNSAFE_RAW);if($em)$$W=$em;}}$_COOKIE=array_filter($_COOKIE,'is_scalar');if(function_exists("mb_internal_encoding"))mb_internal_encoding("8bit");function
connection($g=null){return($g?:Db::$instance);}function
adminer(){return
Adminer::$instance;}function
driver(){return
Driver::$instance;}function
connect(){$Wb=adminer()->credentials();$H=Driver::connect($Wb[0],$Wb[1],$Wb[2]);return(is_object($H)?$H:null);}function
idf_unescape($u){if(!preg_match('~^[`\'"[]~',$u))return$u;$Rf=substr($u,-1);return
str_replace($Rf.$Rf,$Rf,substr($u,1,-1));}function
q($P){return
connection()->quote($P);}function
idx($Ba,$x,$k=null){return($Ba&&array_key_exists($x,$Ba)?$Ba[$x]:$k);}function
number($W){return
preg_replace('~[^0-9]+~','',$W);}function
int_type(){return'(tiny|small|medium|big)?int(eger|\d)?';}function
number_type(){return'(^('.int_type().'|decimal|numeric|number|real|(binary_|half_|scaled_)?float\d?|(binary_)?double( precision)?|(small)?money)$)';}function
text_type(){return'char|text'.(JUSH=="sql"?'|enum|set':'');}function
is_searchable(array$m,array$W){if(!isset($m["privileges"]["where"]))return
false;$T=$m["type"];$Qj=$W["val"];$Sa='binary$|bytea|raw|image|bfile|^vector$'.(JUSH=="mssql"?'|^timestamp$':'|^bit').(JUSH=="oracle"?'|^blob|^long|rowid':'');if(preg_match("~$Sa~",$T))return
false;if(preg_match(number_type(),$T)){$rh='-?\d+(\.\d+)?';return(bool)preg_match('~^'.$rh.(preg_match('~IN$~',$W["op"])?"( *, *$rh)*":'').'$~',$Qj);}if(preg_match('~^(small)?date|^timestamp~',$T))return(bool)preg_match('~^\d+-\d+-\d+~',$Qj);if(preg_match('~^time~',$T))return(bool)preg_match('~^\d+:\d+~',$Qj);if(preg_match('~^bool~',$T)||(JUSH=="mssql"&&$T=="bit"))return(bool)preg_match('~^(t|f|true|false|[01])$~i',$Qj);return
true;}function
remove_slashes(array$Y,$Id=false){$H=array();foreach($Y
as$x=>$W)$H[stripslashes($x)]=(is_array($W)?remove_slashes($W,$Id):($Id?$W:stripslashes($W)));return$H;}function
bracket_escape($u,$La=false){static$Il=array(':'=>':1',']'=>':2','['=>':3','"'=>':4','='=>':5');return
strtr($u,($La?array_flip($Il):$Il));}function
url_escape($P){static$Il=array();if(!$Il){$Il=array(' '=>'+');foreach(str_split("\"'<>#%&+=?".ini_get("arg_separator.input"))as$fb)$Il[$fb]=sprintf('%%%02X',ord($fb));for($s=0;$s<256;$s++){if($s<32||$s>126)$Il[chr($s)]=sprintf('%%%02X',$s);}}return
strtr((string)$P,$Il);}function
min_version($Cm,$mg="",$g=null){$g=connection($g);$lk=$g->server_info;if($mg&&preg_match('~([\d.]+)-MariaDB~',$lk,$A)){$lk=$A[1];$Cm=$mg;}return$Cm&&version_compare($lk,$Cm)>=0;}function
charset(Db$f){return(min_version("5.5.3",0,$f)?"utf8mb4":"utf8");}function
ini_set($Oh,$X){return(function_exists('ini_set')?\ini_set($Oh,$X):false);}function
ini_bool($kf){$W=ini_get($kf);return(preg_match('~^(on|true|yes)$~i',$W)||(int)$W);}function
ini_bytes($kf){$W=ini_get($kf);switch(strtolower(substr($W,-1))){case'g':$W=(int)$W*1024;case'm':$W=(int)$W*1024;case'k':$W=(int)$W*1024;}return$W;}function
max_input_vars($I,$di){$qg=(int)ini_get("max_input_vars");return($qg?(int)floor(($qg-$di)/$I):0);}function
max_input_vars_error(){$kf="max_input_vars";return
sprintf('Maximum number of allowed fields exceeded. Please increase %s.',"<b>$kf = ".ini_get($kf)."</b>");}function
sid(){static$H;if($H===null)$H=(SID&&!($_COOKIE&&ini_bool("session.use_cookies")));return$H;}function
set_password($Bm,$M,$U,$E){$_SESSION["pwds"][$Bm][$M][$U]=($_COOKIE["adminer_key"]&&is_string($E)?array(encrypt_string($E,$_COOKIE["adminer_key"])):$E);}function
get_password(){$H=get_session("pwds");if(is_array($H))$H=($_COOKIE["adminer_key"]?decrypt_string($H[0],$_COOKIE["adminer_key"]):false);return$H;}function
get_val($F,$m=0,$Hb=null){$Hb=connection($Hb);$G=$Hb->query($F);if(!is_object($G))return
false;$I=$G->fetch_row();return($I?$I[$m]:false);}function
get_vals($F,$d=0){$H=array();$G=connection()->query($F);if(is_object($G)){while($I=$G->fetch_row())$H[]=$I[$d];}return$H;}function
get_key_vals($F,$g=null,$ok=true){$g=connection($g);$H=array();$G=$g->query($F);if(is_object($G)){while($I=$G->fetch_row()){if($ok)$H[$I[0]]=$I[1];else$H[]=$I[0];}}return$H;}function
get_rows($F,$g=null,$l="<p class='error'>"){$Hb=connection($g);$H=array();$G=$Hb->query($F);if(is_object($G)){while($I=$G->fetch_assoc())$H[]=$I;}elseif(!$G&&!$g&&$l&&(defined('Adminer\PAGE_HEADER')||$l=="-- "))echo$l.adminer()->error()."\n";return$H;}function
unique_array($I,array$w){foreach($w
as$v){if(preg_match("~^(PRIMARY|UNIQUE)$~",$v["type"])&&!$v["partial"]){$H=array();foreach($v["columns"]as$x){if(!isset($I[$x]))continue
2;$H[$x]=$I[$x];}return$H;}}}function
escape_key($x){if(preg_match('(^([\w(]+)('.str_replace("_",".*",preg_quote(idf_escape("_"))).')([ \w)]+)$)',$x,$A))return$A[1].idf_escape(idf_unescape($A[2])).$A[3];return
idf_escape($x);}function
where(array$Z,array$n=array()){$H=array();foreach((array)$Z["where"]as$x=>$W){$x=bracket_escape($x,true);$d=escape_key($x);$m=idx($n,$x,array());$Cd=$m["type"];$xf=$m&&(is_blob($m)||preg_match('~binary~',$Cd));$H[]=$d.($xf&&!is_utf8($W)?" = ".driver()->quoteBinary($W):(JUSH=="sql"&&$Cd=="json"?" = CAST(".q($W)." AS JSON)":(JUSH=="pgsql"&&preg_match('~^jsonb?$~',$m["full_type"])?"::jsonb = ".q($W)."::jsonb":(JUSH=="sql"&&is_numeric($W)&&preg_match('~\.~',$W)?" LIKE ".q($W):(JUSH=="mssql"&&strpos($Cd,"datetime")===false?" LIKE ".q(preg_replace('~[_%[]~','[\0]',$W)):" = ".unconvert_field($m,q($W)))))));if(JUSH=="sql"&&preg_match('~char|text~',$Cd)&&preg_match("~[^ -@]~",$W))$H[]="$d = ".q($W)." COLLATE ".charset(connection())."_bin";}foreach((array)$Z["null"]as$x)$H[]=escape_key($x)." IS NULL";return
implode(" AND ",$H);}function
where_columns(array$n){$H=array();foreach((array)$_GET["null"]as$x)$H[$x]=true;foreach((array)$_GET["where"]as$x=>$W){$x=bracket_escape($x,true);foreach($n
as$B=>$m){if($x==$B||strpos($x,idf_escape($B))!==false)$H[$B]=true;}}return$H;}function
where_check($W,array$n=array()){parse_str($W,$ib);remove_slashes(array(&$ib));return
where($ib,$n);}function
where_link($s,$d,$X,$Lh="="){$Ih=($X!==null?$Lh:"IS NULL");return"&where[$s][col]=".url_escape($d).($Ih!=first(adminer()->operators())?"&where[$s][op]=".url_escape($Ih):"")."&where[$s][val]=".url_escape($X);}function
convert_fields(array$e,array$n,array$L=array()){$H="";foreach($e
as$x=>$W){if($L&&!in_array(idf_escape($x),$L))continue;$Ca=convert_field($n[$x]);if($Ca)$H
.=", $Ca AS ".idf_escape($x);}return$H;}function
cookie_path(){return
strtr(preg_replace('~\?.*~','',$_SERVER["REQUEST_URI"]),array(";"=>"%3B",","=>"%2C"));}function
cookie($B,$X,$bg=2592000){header("Set-Cookie: $B=".rawurlencode($X).($bg?"; expires=".gmdate("D, d M Y H:i:s",time()+$bg)." GMT":"")."; path=".cookie_path().(HTTPS?"; secure":"").($B=="adminer_import"?"":"; HttpOnly")."; SameSite=lax",false);}function
get_url($mm,$Ob){$http_response_header=null;$ed=array();set_error_handler(function($dd,$l)use(&$ed){$ed[]=preg_replace('~^file_get_contents\([^)]*\):\s*~','',$l);return
true;});$H=file_get_contents($mm,false,$Ob);restore_error_handler();$De=(function_exists('http_get_last_response_headers')?http_get_last_response_headers():$http_response_header);return
array($H,(preg_match('~^HTTP/[\d.]+ (\d+)~',idx($De,0,''),$A)?$A[1]:''),(array)$De,($H===false?implode("\n",$ed):''),);}function
get_settings($Rb){parse_str($_COOKIE[$Rb],$pk);return$pk;}function
get_setting($x,$Rb="adminer_settings",$k=null){return
idx(get_settings($Rb),$x,$k);}function
save_settings(array$pk,$Rb="adminer_settings"){$X=http_build_query($pk+get_settings($Rb));cookie($Rb,$X);$_COOKIE[$Rb]=$X;}function
restart_session(){if(!ini_bool("session.use_cookies")&&(!function_exists('session_status')||session_status()==PHP_SESSION_NONE))session_start();}function
stop_session($Rd=false){$pm=ini_bool("session.use_cookies");if(!$pm||$Rd){session_write_close();if($pm&&ini_set("session.use_cookies",'0')===false)session_start();}}function&get_session($x){return$_SESSION[$x][DRIVER][SERVER][$_GET["username"]];}function
set_session($x,$W){$_SESSION[$x][DRIVER][SERVER][$_GET["username"]]=$W;}function
auth_url($Bm,$M,$U,$j=null){$lm=remove_from_uri(implode("|",array_keys(SqlDriver::$drivers))."|username|ext|".($j!==null?"db|":"").($Bm=='mssql'||$Bm=='pgsql'?"":"ns|").session_name());preg_match('~([^?]*)\??(.*)~',$lm,$A);return"$A[1]?".(sid()?SID."&":"").($_GET["ext"]?"ext=".url_escape($_GET["ext"])."&":"").($Bm!="server"||$M!=""?url_escape($Bm)."=".url_escape($M)."&":"")."username=".url_escape($U).($j!=""?"&db=".url_escape($j):"").($A[2]?"&$A[2]":"");}function
is_ajax(){return($_SERVER["HTTP_X_REQUESTED_WITH"]=="XMLHttpRequest");}function
redirect($ig,$Fg=null){if($Fg!==null){restart_session();$_SESSION["messages"][preg_replace('~^[^?]*~','',($ig!==null?$ig:$_SERVER["REQUEST_URI"]))][]=$Fg;}if($ig!==null){if($ig=="")$ig=".";header("Location: $ig");exit;}}function
query_redirect($F,$ig,$Fg,$mj=true,$md=true,$xd=false,$wl=""){if($md){$Kk=microtime(true);$xd=!connection()->query($F);$wl=format_time($Kk);}$Dk=($F?adminer()->messageQuery($F,$wl,$xd):"");if($xd){adminer()->error
.=adminer()->error().$Dk.script("messagesPrint();")."<br>";return
false;}if($mj)redirect($ig,$Fg.$Dk);return
true;}class
Queries{static$queries=array();static$start=0;}function
queries($F){if(!Queries::$start)Queries::$start=microtime(true);Queries::$queries[]=(driver()->delimiter!=';'?$F:(preg_match('~;$~',$F)?"DELIMITER ;;\n$F;\nDELIMITER ":$F).";");return
connection()->query($F);}function
apply_queries($F,array$S,$gd='Adminer\table'){foreach($S
as$Q){if(!queries("$F ".$gd($Q)))return
false;}return
true;}function
queries_redirect($ig,$Fg,$mj){$gj=implode("\n",Queries::$queries);$wl=format_time(Queries::$start);return
query_redirect($gj,$ig,$Fg,$mj,false,!$mj,$wl);}function
format_time($Kk){return
sprintf('%.3f s',max(0,microtime(true)-$Kk));}function
relative_uri($lm=''){return
preg_replace_callback('~^[^?]*~',function($A){return
str_replace(":","%3A",$A[0]);},preg_replace('~^[^?]*/([^?]*)~','\1',($lm?:$_SERVER["REQUEST_URI"])));}function
remove_from_uri($ki=""){return
substr(preg_replace("~(?<=[?&])($ki".(SID?"":"|".session_name()).")=[^&]*&~",'',relative_uri()."&"),0,-1);}function
get_files($B,$kc=false){$Ed=$_FILES[$B];if(!$Ed)return
null;foreach($Ed
as$x=>$W)$Ed[$x]=(array)$W;$H=array();foreach($Ed["error"]as$x=>$l){if($l)return$l;$o=$Ed["name"][$x];$Dl=$Ed["tmp_name"][$x];$Mb=file_get_contents($kc&&preg_match('~\.gz$~',$o)?"compress.zlib://$Dl":$Dl);if($kc){$Kk=substr($Mb,0,3);if(function_exists("iconv")&&preg_match("~^\xFE\xFF|^\xFF\xFE~",$Kk))$Mb=iconv("utf-16","utf-8",$Mb);elseif($Kk=="\xEF\xBB\xBF")$Mb=substr($Mb,3);}$H[]=array($o,$Mb);}return$H;}function
get_file($x,$kc=false,$rc=""){$Hd=get_files($x,$kc);if(!is_array($Hd))return$Hd;$H='';foreach($Hd
as$Ed){$Mb=$Ed[1];$H
.=$Mb;if($rc)$H
.=(preg_match("($rc\\s*\$)",$Mb)?"":$rc)."\n\n";}return$H;}function
upload_error($l){$yg=($l==UPLOAD_ERR_INI_SIZE?ini_get("upload_max_filesize"):0);return($l?'Unable to upload a file.'.($yg?" ".sprintf('Maximum allowed file size is %sB.',$yg):""):'File does not exist.');}function
is_utf8($W){return(preg_match('~~u',$W)&&!preg_match('~[\0-\x8\xB\xC\xE-\x1F]~',$W));}function
format_number($W){preg_match('~^#+([^#0]+)(?:(#+)\1)?(#*0)$~u','#,##0',$A);$tk=strlen($A[3]);$H=number_format($W,0,".","");$H=preg_replace('~\B(?=(\d{'.(strlen($A[2])?:$tk).'})*\d{'.$tk.'}$)~',$A[1],$H);return
strtr($H,preg_split('~~u','0123456789',-1,PREG_SPLIT_NO_EMPTY));}function
format_status(array$R,$x){$W=idx($R,$x,'?');if(!is_numeric($W))return
h($W);if($W<0)return'?';$za=($x=="Rows"&&(JUSH=="sqlite"||$R["Engine"]==(JUSH=="pgsql"?"table":"InnoDB")));return($za?"~ ":"").format_number($W);}function
friendly_url($W){return
preg_replace('~\W~i','-',$W);}function
table_status1($Q,$yd=false){$H=table_status($Q,$yd);return($H?reset($H):array("Name"=>$Q));}function
column_foreign_keys($Q){$H=array();foreach(adminer()->foreignKeys($Q)as$p){foreach($p["source"]as$W)$H[$W][]=$p;}return$H;}function
fields_from_edit(){$H=array();foreach((array)$_POST["field_keys"]as$x=>$W){if($W!=""){$W=bracket_escape($W);$_POST["function"][$W]=$_POST["field_funs"][$x];$_POST["fields"][$W]=$_POST["field_vals"][$x];}}foreach((array)$_POST["fields"]as$x=>$W){$B=bracket_escape($x,true);$H[$B]=array("field"=>$B,"full_type"=>"","type"=>"","privileges"=>array("insert"=>1,"update"=>1,"where"=>1,"order"=>1),"null"=>true,"auto_increment"=>($B==driver()->primary),);}return$H;}function
dump_headers($Pe,$Wg=false){$H=adminer()->dumpHeaders($Pe,$Wg);$fi=$_POST["output"];if($fi!="text"||$H=="tar"){$Db=($fi!="text"&&$fi!="file"&&preg_match('~^[0-9a-z]+$~',$fi)?".$fi":"");header("Content-Disposition: attachment; filename=".adminer()->dumpFilename($Pe).".$H$Db");}session_write_close();if(!ob_get_level())ob_start(null,4096);ob_flush();flush();return$H;}function
dump_csv(array$I){$Tl=$_POST["format"]=="tsv";foreach($I
as$x=>$W){if(preg_match('~["\n]|^0[^.]|\.\d*0$|'.($Tl?'\t':'[,;]|^$').'~',$W))$I[$x]='"'.str_replace('"','""',$W).'"';}echo
implode(($_POST["format"]=="csv"?",":($Tl?"\t":";")),$I)."\r\n";}function
parse_csv($Zb,$ak){$H=array();preg_match_all('~(?>"[^"]*"|[^"\r\n]+)+~',$Zb,$og);foreach($og[0]as$I){preg_match_all("~((?>\"[^\"]*\")+|[^$ak]*)$ak~",$I.$ak,$pg);$H[]=$pg[1];}return$H;}function
csv_value($W){return(preg_match('~^".*"$~s',$W)?str_replace('""','"',substr($W,1,-1)):$W);}function
apply_sql_function($q,$d){return($q?($q=="unixepoch"?"DATETIME($d, '$q')":($q=="count distinct"?"COUNT(DISTINCT ":strtoupper("$q("))."$d)"):$d);}function
get_temp_dir(){return
ini_get("upload_tmp_dir")?:sys_get_temp_dir();}function
file_open_lock($o){if(is_link($o))return;$Yd=@fopen($o,"c+");if(!$Yd)return;@chmod($o,0660);if(!flock($Yd,LOCK_EX)){fclose($Yd);return;}return$Yd;}function
file_write_unlock($Yd,$dc){rewind($Yd);fwrite($Yd,$dc);ftruncate($Yd,strlen($dc));file_unlock($Yd);}function
file_unlock($Yd){flock($Yd,LOCK_UN);fclose($Yd);}function
first(array$Ba){return
reset($Ba);}function
password_file($h){$o=get_temp_dir()."/adminer.key";if(!$h&&!file_exists($o))return'';$Yd=file_open_lock($o);if(!$Yd)return'';$H=stream_get_contents($Yd);if(!$H){$H=rand_string();file_write_unlock($Yd,$H);}else
file_unlock($Yd);return$H;}function
rand_string(){return(function_exists('random_bytes')?bin2hex(random_bytes(16)):md5(uniqid(strval(mt_rand()),true)));}function
select_value($W,$_,array$m,$ul){if(is_array($W)){$H="";if(array_filter($W,'is_array')==array_values($W)){$If=array();foreach($W
as$V)$If+=array_fill_keys(array_keys($V),null);foreach(array_keys($If)as$Gf)$H
.="<th>".h($Gf);foreach($W
as$V){$H
.="<tr>";foreach(array_merge($If,$V)as$wm)$H
.="<td>".select_value($wm,$_,$m,$ul);}}else{foreach($W
as$Gf=>$V)$H
.="<tr>".($W!=array_values($W)?"<th>".h($Gf):"")."<td>".select_value($V,$_,$m,$ul);}return"<table>$H</table>";}if(!$_)$_=adminer()->selectLink($W,$m);if($_===null){if(is_mail($W))$_="mailto:$W";if(is_url($W))$_=$W;}$W=driver()->value($W,$m);$H=adminer()->editVal($W,$m);if($H!==null){if(!is_utf8($H))$H="\0";elseif($ul!=""&&is_shortable($m))$H=shorten_utf8($H,max(0,+$ul));else$H=h($H);}return
adminer()->selectVal($H,$_,$m,$W);}function
is_blob(array$m){return
preg_match('~blob|bytea|raw|file'.(JUSH=="mssql"?'|binary|image':'').'~',$m["type"])&&!in_array($m["type"],idx(driver()->structuredTypes(),'User types',array()));}function
is_mail($Uc){$Ea='[-a-z0-9!#$%&\'*+/=?^_`{|}~]';$Hc='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';$Bi="$Ea+(\\.$Ea+)*@($Hc?\\.)+$Hc";return
is_string($Uc)&&preg_match("(^$Bi(,\\s*$Bi)*\$)i",$Uc);}function
is_url($P){$Hc='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';return
preg_match("~^((https?):)?//($Hc?\\.)+$Hc(:\\d+)?(/.*)?(\\?.*)?(#.*)?\$~i",$P);}function
is_ipv6($na){$r='[\da-f]{1,4}';$wf='\d{1,3}(\.\d{1,3}){3}';return(bool)preg_match("~^(($r:){7}$r|($r:){6}$wf|(($r:)*$r)?::(($r:)*($r|$wf))?)$~iD",$na);}function
is_shortable(array$m){return!preg_match('~'.number_type().'|date|time|year~',$m["type"]);}function
url_host($Le){return(strpos($Le,":")!==false?"[$Le]":$Le);}function
server_parts(array$wi){return
array("scheme"=>(string)$wi["scheme"],"host"=>(string)$wi["host"],"port"=>(string)$wi["port"],"socket"=>(string)$wi["socket"],"path"=>(string)$wi["path"],);}function
parse_server($M){if($M=="")return
server_parts(array());if($M[0]==":"&&!is_ipv6($M)){$zj=substr($M,1);if(preg_match('~^\d+$~D',$zj))return
server_parts(array("port"=>$zj));return(preg_match('~^/[-\w.:/]*$~D',$zj)?server_parts(array("socket"=>$zj)):null);}$Oj="";if(preg_match('~^([-+.\w]+)://~',$M,$A)){$Oj=strtolower($A[1]);$M=substr($M,strlen($A[0]));}if(preg_match('~^\[(.+)](:(\d+))?(/[-\w./]*)?$~D',$M,$A))return(is_ipv6($A[1])?server_parts(array("scheme"=>$Oj,"host"=>$A[1],"port"=>$A[3],"path"=>$A[4])):null);if(is_ipv6($M))return
server_parts(array("scheme"=>$Oj,"host"=>$M));if(preg_match('~^(/[-\w./]*)(:(\d+))?$~D',$M,$A))return
server_parts(array("scheme"=>$Oj,"host"=>$A[1],"port"=>$A[3]));return(preg_match('~^([-\w.]*)(:(\d+))?(/[-\w./]*)?$~D',$M,$A)?server_parts(array("scheme"=>$Oj,"host"=>$A[1],"port"=>$A[3],"path"=>$A[4])):null);}function
count_rows($Q,array$Z,$yf,array$r){$F=" FROM ".table($Q).($Z?" WHERE ".implode(" AND ",$Z):"");return($yf&&(JUSH=="sql"||count($r)==1)?"SELECT COUNT(DISTINCT ".implode(", ",$r).")$F":"SELECT COUNT(*)".($yf?" FROM (SELECT 1$F GROUP BY ".implode(", ",$r).") x":$F));}function
slow_query($F){$j=adminer()->database();$xl=adminer()->queryTimeout();$vk=driver()->slowQuery($F,$xl);$g=null;if(!$vk&&support("kill")){$g=connect();if($g&&($j==""||$g->select_db($j))){$Jf=number(get_val(connection_id(),0,$g));echo
script("const timeout = setTimeout(() => { ajax('".js_escape(ME)."script=kill', function () {}, 'kill=$Jf&token=".get_token()."'); }, 1000 * $xl);");}}ob_flush();flush();$H=@get_key_vals(($vk?:$F),$g,false);if($g){echo
script("clearTimeout(timeout);");ob_flush();flush();}return$H;}function
get_token(){$jj=rand(1,1e6);return($jj^$_SESSION["token"]).":$jj";}function
verify_token(){list($El,$jj)=explode(":",$_POST["token"]);return($jj^$_SESSION["token"])==$El&&in_array($_SERVER["HTTP_SEC_FETCH_SITE"],array("","same-origin"));}function
compress_alphabet(){return
strtr(implode(range('"','~')),"'\\","!\n");}function
decompress_string($P,$xc=""){$wa=array_flip(str_split(compress_alphabet()));$y=strlen($P);$zm=($y?13*($y-1)/2-$wa[$P[0]]:0);$Sa="";$zj=0;$_j=0;for($s=1;$s<$y;$s+=2){$zj=($zj<<13)+$wa[$P[$s]]*93+$wa[$P[$s+1]];$_j+=13;while($_j>=8&&$zm>=8){$_j-=8;$zm-=8;$Sa
.=chr($zj>>$_j);$zj&=(1<<$_j)-1;}}if($Sa=="")return"";if($xc!=""&&function_exists('inflate_init'))return
inflate_add(inflate_init(ZLIB_ENCODING_RAW,array('dictionary'=>$xc)),$Sa,ZLIB_FINISH);return($xc==""&&function_exists('gzinflate')?gzinflate($Sa):inflate($Sa,$xc));}function
inflate($Sa,$xc=""){$Yf=array(3,4,5,6,7,8,9,10,11,13,15,17,19,23,27,31,35,43,51,59,67,83,99,115,131,163,195,227,258);$Zf=array(0,0,0,0,0,0,0,0,1,1,1,1,2,2,2,2,3,3,3,3,4,4,4,4,5,5,5,5,0);$Ac=array(1,2,3,4,5,7,9,13,17,25,33,49,65,97,129,193,257,385,513,769,1025,1537,2049,3073,4097,6145,8193,12289,16385,24577);$Cc=array(0,0,0,0,1,1,2,2,3,3,4,4,5,5,6,6,7,7,8,8,9,9,10,10,11,11,12,12,13,13);$H=$xc;$Ki=0;do{$Jd=inflate_bits($Sa,$Ki,1);$T=inflate_bits($Sa,$Ki,2);if(!$T){$Ki=($Ki+7)&~7;$y=inflate_bits($Sa,$Ki,16);$Ki+=16;$H
.=substr($Sa,$Ki>>3,$y);$Ki+=$y<<3;}else{if($T==1){$gg=array_merge(array_fill(0,144,8),array_fill(0,112,9),array_fill(0,24,7),array_fill(0,8,8));$Dc=array_fill(0,30,5);}else{$fg=inflate_bits($Sa,$Ki,5)+257;$Bc=inflate_bits($Sa,$Ki,5)+1;$Rh=array(16,17,18,0,8,7,9,6,10,5,11,4,12,3,13,2,14,1,15);$Lg=array_fill(0,19,0);$Kg=inflate_bits($Sa,$Ki,4)+4;for($s=0;$s<$Kg;$s++)$Lg[$Rh[$s]]=inflate_bits($Sa,$Ki,3);$Mg=inflate_table($Lg);$ag=array();while(count($ag)<$fg+$Bc){$Vk=inflate_symbol($Sa,$Ki,$Mg);if($Vk==16)$ag=array_merge($ag,array_fill(0,inflate_bits($Sa,$Ki,2)+3,end($ag)));elseif($Vk==17)$ag=array_merge($ag,array_fill(0,inflate_bits($Sa,$Ki,3)+3,0));elseif($Vk==18)$ag=array_merge($ag,array_fill(0,inflate_bits($Sa,$Ki,7)+11,0));else$ag[]=$Vk;}$gg=array_slice($ag,0,$fg);$Dc=array_slice($ag,$fg);}$hg=inflate_table($gg);$Fc=inflate_table($Dc);while(($Vk=inflate_symbol($Sa,$Ki,$hg))!=256){if($Vk<256)$H
.=chr($Vk);else{$y=$Yf[$Vk-257]+inflate_bits($Sa,$Ki,$Zf[$Vk-257]);$Ec=inflate_symbol($Sa,$Ki,$Fc);$yh=strlen($H)-$Ac[$Ec]-inflate_bits($Sa,$Ki,$Cc[$Ec]);for($s=0;$s<$y;$s++)$H
.=$H[$yh+$s];}}}}while(!$Jd);return($xc==""?$H:substr($H,strlen($xc)));}function
inflate_bits($Sa,&$Ki,$Tb){$H=0;for($s=0;$s<$Tb;$s++){$H+=((ord($Sa[$Ki>>3])>>($Ki&7))&1)<<$s;$Ki++;}return$H;}function
inflate_table(array$ag){$Q=array();$sb=0;for($Ta=1;$Ta<=max($ag);$Ta++){foreach($ag
as$Vk=>$y){if($y==$Ta){$Q[$Ta][$sb]=$Vk;$sb++;}}$sb<<=1;}return$Q;}function
inflate_symbol($Sa,&$Ki,array$Q){$sb=0;$Ta=0;do{$sb=($sb<<1)+inflate_bits($Sa,$Ki,1);$Ta++;}while(!isset($Q[$Ta][$sb]));return$Q[$Ta][$sb];}function
script($_k,$Hl="\n"){return"<script".nonce().">$_k</script>$Hl";}function
script_src($mm,$nc=false){return"<script src='".h($mm)."'".nonce().($nc?" defer":"")."></script>\n";}function
nonce(){return' nonce="'.get_nonce().'"';}function
on($hd,$ve,$_a=null){$Aa=array();foreach(array_slice(func_get_args(),2)as$W)$Aa[]=json_encode($W,256);return" data-on$hd='".str_replace(array('&','<',"'"),array('&amp;','&lt;','&#039;'),"$ve(".implode(", ",$Aa).")")."'";}function
input_hidden($B,$X=""){return"<input type='hidden' name='".h($B)."' value='".h($X)."'>\n";}function
input_token(){return
input_hidden("token",get_token());}function
target_blank(){return' target="_blank" rel="noreferrer noopener"';}function
h($P){return
str_replace(array('&','<','"',"'","\0"),array('&amp;','&lt;','&quot;','&#039;','&#0;'),$P);}function
nl_br($P){return
str_replace("\n","<br>",$P);}function
checkbox($B,$X,$lb,$Nf="",$c="",$qb="",$Pf=""){$H="<input type='checkbox' name='$B' value='".h($X)."'".($lb?" checked":"").($Nf==""&&$qb?" class='$qb'":"").($Pf?" aria-labelledby='$Pf'":"").$c.">";return($Nf!=""?"<label".($qb?" class='$qb'":"").">$H".h($Nf)."</label>":$H);}function
optionlist($C,$Wj=null,$qm=false){$H="";foreach($C
as$Gf=>$V){$Qh=array($Gf=>$V);if(is_array($V)){$H
.='<optgroup label="'.h($Gf).'">';$Qh=$V;}foreach($Qh
as$x=>$W)$H
.='<option'.($qm||is_string($x)?' value="'.h($x).'"':'').($Wj!==null&&($qm||is_string($x)?(string)$x:$W)===$Wj?' selected':'').'>'.h($W);if(is_array($V))$H
.='</optgroup>';}return$H;}function
html_select($B,array$C,$X="",$c="",$Pf=""){static$Nf=0;$Of="";if(!$Pf&&substr($C[""],0,1)=="("){$Nf++;$Pf="label-$Nf";$Of="<option value='' id='$Pf'>".h($C[""]);unset($C[""]);}return"<select name='".h($B)."'".($Pf?" aria-labelledby='$Pf'":"")."$c>".$Of.optionlist($C,$X)."</select>";}function
html_radios($B,array$C,$X="",$ak=""){$H="";foreach($C
as$x=>$W)$H
.="<label><input type='radio' name='".h($B)."' value='".h($x)."'".($x==$X?" checked":"").">".h($W)."</label>$ak";return$H;}function
confirm($Fg=""){return
on('click','confirmClick',$Fg?:'Are you sure?');}function
print_fieldset($t,$Xf,$Fm=false){echo"<fieldset><legend>","<a href='#fieldset-$t' class='toggle'>$Xf</a>","</legend>","<div id='fieldset-$t'".($Fm?"":" class='hidden'").">\n";}function
bold($Va,$qb=""){return($Va?" class='active $qb'":($qb?" class='$qb'":""));}function
js_escape($P){return
str_replace("<","\\x3C",addcslashes($P,"\r\n'\\"));}function
js_escape_re($P){return
addcslashes(preg_quote($P,"/"),"\r\n");}function
pagination_href($D){return
remove_from_uri("page|next").($D?"&page=$D".($_GET["next"]!=""?"&next=".url_escape($_GET["next"]):""):"");}function
pagination($D,$ac){return" ".($D==$ac?($D?"<b>".($D+1)."</b>":$D+1):'<a href="'.h(pagination_href($D)).'">'.($D+1)."</a>");}function
hidden_fields(array$cj,array$Te=array(),$Ri=''){$H=false;foreach($cj
as$x=>$W){if(!in_array($x,$Te)){if(is_array($W))hidden_fields($W,array(),$x);else{$H=true;echo
input_hidden(($Ri?$Ri."[$x]":$x),$W);}}}return$H;}function
hidden_fields_get(){echo(sid()?input_hidden(session_name(),session_id()):''),($_GET["ext"]?input_hidden("ext",$_GET["ext"]):""),(isset($_GET[DRIVER])?input_hidden(DRIVER,SERVER):""),input_hidden("username",$_GET["username"]);}function
on_upload_progress(&$km){$km=(ini_bool("session.upload_progress.enabled")&&ini_get("session.upload_progress.name")?rand_string():"");return($km?on('submit','uploadProgress',ME."upload=$km",SESSION_NAME."=$km"):"");}function
file_input($c,$zj=""){$sg="max_file_uploads";$tg=ini_get($sg);$yg="upload_max_filesize";$zg=ini_bytes($yg);$Oi=ini_bytes("post_max_size");if($Oi&&$Oi<$zg){$yg="post_max_size";$zg=$Oi;}$_g=ini_get($yg);return(ini_bool("file_uploads")?"<input type='file'$c".on('change','fileChange',(int)$tg,sprintf('Increase %s.',"$sg = $tg"),$zg,sprintf('Increase %s.',"$yg = $_g")).">$zj":'File uploads are disabled.');}function
enum_input($T,$c,array$m,$X,$Xc=""){preg_match_all("~'((?:[^']|'')*)'~",$m["length"],$og);$Ri=($m["type"]=="enum"?"val-":"");$lb=(is_array($X)?in_array("null",$X):$X===null);$H=($m["null"]&&$Ri?"<label><input type='$T'$c value='null'".($lb?" checked":"")."><i>$Xc</i></label>":"");foreach($og[1]as$W){$W=stripcslashes(str_replace("''","'",$W));$lb=(is_array($X)?in_array($Ri.$W,$X):$X===$W);$H
.=" <label><input type='$T'$c value='".h($Ri.$W)."'".($lb?' checked':'').'>'.h(adminer()->editVal($W,$m)).'</label>';}return$H;}function
input(array$m,$X,$q,$Ja=false,$im=false){$B=h(bracket_escape($m["field"]));echo"<td class='function'>";$cd=driver()->enumLength($m);if($cd){$m["type"]="enum";$m["length"]=$cd;}$C=($m["type"]=="enum"||$m["type"]=="set");if(is_array($X)&&!$q&&!$C)$q="json";$Ef=($q=="json"||preg_match('~^jsonb?$~',$m["full_type"]));if($Ef&&$X!=''&&(JUSH!="pgsql"||$m["type"]!="json")&&(is_array($X)||!$_POST["save"]))$X=json_encode(is_array($X)?$X:json_decode($X),128|64|256);$yj=(JUSH=="mssql"&&$im&&$m["auto_increment"]);if($yj&&!$_POST["save"])$q=null;$he=(isset($_GET["select"])||$yj?array("orig"=>'original'):array())+adminer()->editFunctions($m);$c=" name='fields[$B]".($C?"[]":"")."'".($Ja?" autofocus":"");echo
driver()->unconvertFunction($m)." ";$Q=$_GET["edit"]?:$_GET["select"];if($m["type"]=="enum")echo
h($he[""])."<td>".adminer()->editInput($Q,$m,$c,$X);else{$xe=(in_array($q,$he)||isset($he[$q]));$Kd=0;foreach($he
as$x=>$W){if($x===""||!$W)break;$Kd++;}echo(count($he)>1?"<select name='function[$B]'".on('change','functionChange').on_help_value('^SQL$').">".optionlist($he,$q===null||$xe?$q:"")."</select>":h(reset($he)))."<td".($Kd&&count($he)>1?on('input','skipOriginal',$Kd):"").">";$mf=adminer()->editInput($Q,$m,$c,$X);if($mf!="")echo$mf;elseif(preg_match('~bool~',$m["type"]))echo"<input type='hidden'$c value='0'>"."<input type='checkbox'".(preg_match('~^(1|t|true|y|yes|on)$~i',$X)?" checked":"")."$c value='1'>";elseif($m["type"]=="set")echo
enum_input("checkbox",$c,$m,(is_string($X)?explode(",",$X):$X));elseif(is_blob($m)&&ini_bool("file_uploads"))echo"<input type='file' name='fields-$B'>";elseif($Ef)echo"<textarea$c cols='50' rows='12' class='jush-json'>".h($X).'</textarea>';elseif(($tl=preg_match('~text|lob|memo~i',$m["type"]))||preg_match("~\n~",$X)){if($tl&&JUSH!="sqlite")$c
.=" cols='50' rows='12'";else{$J=min(12,substr_count($X,"\n")+1);$c
.=" cols='30' rows='$J'";}echo"<textarea$c>".h($X).'</textarea>';}else{$Xl=driver()->types();$Vl=$Xl[$m["type"]];if(preg_match('~date|time|year~',$m["type"])){$Zd=(preg_match('~time~',$m["type"])&&preg_match('~^\d+$~',$m["length"])?$m["length"]+1:0);$Ag=($Vl?$Vl+$Zd:0);}elseif(!preg_match('~int|vector~',$m["type"])&&preg_match('~^(\d+)(,(\d+))?$~',$m["length"],$A))$Ag=(preg_match("~binary~",$m["type"])?2:1)*$A[1]+($A[3]?1:0)+($A[2]&&!$m["unsigned"]?1:0);else$Ag=($Vl?$Vl+($m["unsigned"]?0:1):0);echo"<input".((!$xe||$q==="")&&preg_match('~^'.int_type().'$~',$m["type"])&&!preg_match('~\[]~',$m["full_type"])?" type='number'":"")." value='".h($X)."'".($Ag?" data-maxlength='$Ag'":"").(preg_match('~char|binary~',$m["type"])&&$Ag>20?" size='".($Ag>99?60:40)."'":"")."$c>";}echo
adminer()->editHint($Q,$m,$X),(count($he)>1?script("fire(qs('select', qsl('td').previousSibling), 'change');",""):"");}}function
process_input(array$m){$u=bracket_escape($m["field"]);$q=idx($_POST["function"],$u);if($q=="orig")return(preg_match('~^CURRENT_TIMESTAMP~i',$m["on_update"])?idf_escape($m["field"]):false);if($q=="NULL")return"NULL";if(is_blob($m)&&ini_bool("file_uploads")){$Ed=get_file("fields-$u");if(!is_string($Ed))return
false;return
driver()->quoteBinary($Ed);}$X=idx($_POST["fields"],$u);if($X===null)return
false;if($m["type"]=="enum"||driver()->enumLength($m)){$X=idx($X,0);if($X=="orig"||!$X)return
false;if($X=="null")return"NULL";$X=substr($X,4);}if($m["auto_increment"]&&$X=="")return
null;if($m["type"]=="set")$X=implode(",",(array)$X);if($q=="json"){$X=json_decode($X,true);if(!is_array($X))return
false;return$X;}return
adminer()->processInput($m,$X,$q);}function
search_tables(){$_GET["where"][0]["val"]=$_POST["query"];$Zj="<ul>\n";foreach(table_status('',true)as$Q=>$R){$B=adminer()->tableName($R);if(isset($R["Engine"])&&$B!=""&&(!$_POST["tables"]||in_array($Q,$_POST["tables"]))){$G=connection()->query("SELECT".limit("1 FROM ".table($Q)," WHERE ".implode(" AND ",adminer()->selectSearchProcess(fields($Q),array(),$R)),1));if(!$G||$G->fetch_row()){$Yi="<a href='".h(ME."select=".url_escape($Q)."&where[0][op]=".url_escape($_GET["where"][0]["op"])."&where[0][val]=".url_escape($_GET["where"][0]["val"]))."'>$B</a>";echo"$Zj<li>".($G?$Yi:"<p class='error'>$Yi: ".adminer()->error())."\n";$Zj="";}}}echo($Zj?"<p class='message'>".'No tables.':"</ul>")."\n";}function
on_help($tl,$sk=0){return
on('mouseover','helpMouseover',$tl,$sk).on('mouseout','helpMouseout');}function
on_help_value($tj="",$xj=""){return
on('mouseover','helpValueMouseover',$tj,$xj).on('mouseout','helpMouseout');}function
edit_form($Q,array$n,$I,$im,$l='',$F='',$wl=''){$bl=adminer()->tableName(table_status1($Q,true));page_header(($im?'Edit':'Insert'),$l,array("select"=>array($Q,$bl)),$bl);adminer()->editRowPrint($Q,$n,$I,$im,$F,$wl);if($I===false){echo"<p class='error'>".'No rows.'."\n";return;}echo"<form action='' method='post' enctype='multipart/form-data' id='form'>\n";$Sc=false;$Mm=($im&&!isset($_GET["select"])?where_columns($n):array());$Pb=(count($Mm)!=count($n));if(!$Pb)$Mm=array();if(!$n)echo"<p class='error'>".'You have no privileges to update this table.'."\n";else{echo"<table class='layout nowrap'".on('keydown','editingKeydown').">\n";$Ja=!$_POST;foreach($n
as$B=>$m){echo"<tr".($Mm[$B]?on('change','whereChange'):"")."><th>".adminer()->fieldName($m);$k=idx($_GET["set"],bracket_escape($B));if($k===null){$k=$m["default"];if($m["type"]=="bit"&&preg_match("~^b'([01]*)'\$~",$k,$uj))$k=$uj[1];if(JUSH=="sql"&&preg_match('~binary~',$m["type"]))$k=bin2hex($k);}$X=($I!==null?($m["type"]=="set"&&is_array($I[$B])?implode(",",$I[$B]):(is_bool($I[$B])?+$I[$B]:$I[$B])):(!$im&&$m["auto_increment"]?"":(isset($_GET["select"])?false:$k)));if(!$_POST["save"]&&is_string($X))$X=adminer()->editVal($X,$m);if(($im&&!isset($m["privileges"]["update"]))||$m["generated"])echo"<td class='function'><td>".select_value($X,'',$m,null);else{$Sc=true;$q=($_POST["save"]?idx($_POST["function"],bracket_escape($B),""):($im&&preg_match('~^CURRENT_TIMESTAMP~i',$m["on_update"])?"now":($X===false?null:($X!==null?'':'NULL'))));if(!$_POST&&!$im&&$X==$m["default"]&&preg_match('~^[\w.]+\(~',$X))$q="SQL";if(preg_match("~time~",$m["type"])&&preg_match('~^CURRENT_TIMESTAMP~i',$X)){$X="";$q="now";}if($m["type"]=="uuid"&&$X=="uuid()"){$X="";$q="uuid";}if($Ja!==false)$Ja=($m["auto_increment"]||$q=="now"||$q=="uuid"?null:true);input($m,$X,$q,$Ja,$im);if($Ja)$Ja=false;}}if(!fields($Q)&&driver()->primary!="")echo"<tr>"."<th><input name='field_keys[]'".on('input','fieldChange').">"."<td class='function'>".html_select("field_funs[]",adminer()->editFunctions(array("null"=>isset($_GET["select"]))))."<td><input name='field_vals[]'>";echo"</table>\n";}echo"<p>\n";if($Sc){echo"<input type='submit' value='".'Save'."'>\n";if(!isset($_GET["select"])&&$Pb){$yc=($Mm&&($l!=""||adminer()->error!="")?" disabled":"");echo"<input type='submit' name='insert' value='".($im?'Save and continue editing':'Save and insert next')."' title='Ctrl+Shift+Enter'$yc".($im?on('click','ajaxForm','Saving…'):"").">\n";}}echo($im?"<input type='submit' name='delete' value='".'Delete'."'".confirm().">\n":"");if(isset($_GET["select"]))hidden_fields(array("check"=>(array)$_POST["check"],"clone"=>$_POST["clone"],"all"=>$_POST["all"]));echo
input_hidden("referer",(isset($_POST["referer"])?$_POST["referer"]:$_SERVER["HTTP_REFERER"])),input_hidden("save",1),input_token(),"</form>\n";}function
repeat_pattern($Bi,$y){return
str_repeat("$Bi{0,65535}",$y/65535)."$Bi{0,".($y%65535)."}";}function
shorten_utf8($P,$y=80,$Rk=""){if(!preg_match("(^(".repeat_pattern("[\t\r\n -\x{10FFFF}]",$y).")($)?)u",$P,$A))preg_match("(^(".repeat_pattern("[\t\r\n -~]",$y).")($)?)",$P,$A);return(isset($A[2])?h($A[1]).$Rk:h(preg_replace('~\n[^\n]*\z~',"\n",$A[1]))."$Rk<i>…</i>");}function
icon($Oe,$B,$Ne,$zl,$c=""){return"<button ".($B?"type='submit' name='$B'":"draggable='true' tabindex='-1'")." title='".h($zl)."' class='icon icon-$Oe".($B?"":" jsonly")."'$c><span>$Ne</span></button>";}function
copy_icon(){$Sb='Copy';return"<a href='' class='jsonly icon-copy' title='$Sb'><span>$Sb</span></a>";}if(isset($_GET["file"])){if(substr(VERSION,-4)!='-dev'){if($_SERVER["HTTP_IF_MODIFIED_SINCE"]){header("HTTP/1.1 304 Not Modified");exit;}header("Expires: ".gmdate("D, d M Y H:i:s",time()+365*24*60*60)." GMT");header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");header("Cache-Control: immutable");}ini_set("zlib.output_compression",'1');if($_GET["file"]=="default.css"){header("Content-Type: text/css; charset=utf-8");echo
decompress_string('%c(ADg~Z9.uC>]~.3$i#V4&_?UqjkCbUi6!n7_^@%:>!T#8;}*:pbFpd%ydIC""!*lpu2Rd:XrOM1l~#m;h*tmNtn-K%;J/^Y*Q4P1@XxoU:<QWC~Ad
*wPUjMZVuxYpXE#]<&s3ePkKT#dKk1E:Ul2tyYT$30t^mt2IE._i,ba[]QH;&^G>XeCRnZ7F0S>T&;8AGU1t~%>-+Z0WfbKWl>U:ijN_z+h1*84v9?R/g<"kr`70Q,eAMX30u"wX8T$kB(|"|H0:,cYV2.iD~aXAjj1$~>,`?&Z9_<(!
qrT<(,_y%ATmH^FSdYHTXuUHpb2R3nhiJ=6B9zbpQiHwSWo:Fy
iA)+i]r,Z>=ERflWaj|1)rr"HHn)9CNWV<s
a8s*vQw0Q)
vcwcD.Wb5!3Tg9BKn%bJD*5Q5t[^6tIF=@(|pe],Vv1:0?fqkYe|lVr.Aj%xiFt!D.)?#3j$)/DW!"l{6LO&dBI5$(J+")2,j`tT8]n:oKf
gw:;1
Q5qe+(QGKmV5*nc<OAd+7atqW^^@"(^<HoB^&aPW20o.#;m#HB0b<Ml+"8bxP$)XLG8g5H*^0:Lik[-D"
^U]w?_&s)x6G=#;<k.VW8&kOm!*%NL"g+}OvS;Fa`c3RY]s}&8(oFCWd=@G,
O;)3?53PGPO#~0PHvP8u$S#sj
>=?FH8{R^MV.mJf8l53,&#z`fuue1wS[?1]bVxc_:<c&);2C0@,GJ"PkZ,y^`3M+qdTaLdM1to%O)Se:5oM[e5&^_10$Vdl<q[_7z<6rht[Qa`l[wHxmSBSA+ljgc-Qo,(j?)yk(g+o,H4NAm1h27wS2!b>LL@]vD^W$Q+I^`]TUND2tRJ7vqf!NW*=$iZhrakYxxyrcaey2pJ)h!T?`=<{-+tc*lC?&
#/28r(1}W56^E@-"WSdAB%^h
lU3Ne^/KQHrW^w<4`Tq7|5<2.J%;JSC3B?5;|-N$pL2Ad4FgH"[9!6{^|)uh3X-Vrec8<.kP4w#`i,^X"AkN!dLS`5tqW-&"q!Yp
Nh<uHt;6PFruK^TBkkQ#*8@Q57e-&Xa!Xk]u7zK6J8tfByUB
&aao]/B-|W-2,U)iHM]b=j6)r*,s4E:>j
t.^7_hzF9glrNKW83b38O<~MM0PT*r
Y6VFVGx:m6R.y!s[LbxY`eN~Pv(?f5[M4=DZPTr#7xA}B*c|N?2:8KrdfH#+3W%ghLi]ipdyn,r;%}lJn6z!NvU"*eiRc7,a-Qg@J{m@9j^m^d$r/q+K&Cj}:[J3;5Syu[.@-06A/BRwlm_kRi,!<yCM+}Fs*U3$^WZ(XdbjMYuY`{omV|0Q2U61UDIm0p&j0eB<=SUkE!$3b?E[il65<R(c95Tv&jGxE9R_RT
amR4HIml08>-"uN=WQ9E_;yLmOm[ZF66Smep_,*l(J
Y>Tv4P+zc=QCe"UiN+wGmd0*0tt1tUO>Eu<MU34>P<1C0M&
v+nG$Pv(-P$k
wyAI9;o=f>E!*CM<[J9<p+B%p8CvW0Q3;+5WgIek|r-/.sYNwRcZ$%0"e(HP^p|i"l&p@;E^*8B-QBJ>A9cVP2GhB>@Ge51w*m~t*jR^I-]Um#P`-:|[*WEl^WF4%@uyY1vB/s=Jqx|6]%_I+AssSXW@AwD&K/|k22qOLfSYul;%eNI+vM%$mK~W)&P>w
zIh$68;.DhX.`B!ER6qc:=eu5%B3,_W)
6Z4x%|!7dId@!klmyN<>YW1CTA6@y$6@hUke3R*_W4[t@U@63Eqh"6D2Uo?
igD;U]8.by@dH7D&IS,+
tbJxyqMfshY)9GrH)iw!#daveO9Emn#/H5-GGaFIZYT"l7dg4
KRA"pU)@P7{e1o)T9o"MiX*+)*$?{.C@H?HL$jFoNJDu_(mv~a
u|DlC~w$(lIam7(Wu}v#qf99k78,c"Q,C
8vJSHp#zKuO4V$A=e=]?k
s|&2&rTR9.s.j=hGEvJNQ*:pj/!sC`6iIUA3TEVVxtt$23lk%aa$,Fh;#~O:%<jaBcY;oem}HY
a#c>1ea!xwfj%r}qcfXA:cGp)l#_F9D!R8=E;Z3e,Rn8-,@?DG3&CKkJ~#b;6Xm9GP!%YYQ3,LS@S;~caSMRUah9DN1`|Z_SGtY(H/WCLWl#qv~rN+:2Buwnd$[-/g9s?e
8x30)ipvppEZjd!Qo1
@im7".`@bFSRtCXNRZ#sb*X>ebIyY!EvG[<AHH68gA./R[wQYq$mB=E#NI/D,Y
ma$^U:5o_)b=i=J?X4:|m0aZ&Xj=eIV.!&Y0q4!@EWyI/&54t,+}gug($I4Ue1yJGjdF&Z<KZGH/QDdM=cBBxQk}i>*RioRgnC2Bfe)KT9cPD,sQjuN0L}+Fo86D#qaT"B;<pg@~j.WA_zk;CHS@Zw*LooeOvBtY<!!}_S%$8Q@$_^yN/-])<0"GJYYCkMH`4:Gx3~P{1w&E2mD)aOV)q4UGo/1Tf3"<amM-8(4IY<2p[]"+Us%LFJMmtBa2Brg
K$rY
MKlhpcQ^J0&K
)iLuwAhTgST{,*RnH$82>)8<oPilf)]tXT/g8&iPqwp8!rw}M*o$?Kl=z%S}[,!;a9c
2<qn;eLbkZBr[iQ^gPA4[(LFHNf+Oa5j@)QnyVHID!BWrq^*>$z#$Fyc-"iGyemj]]gwwf/]lSlWBY^=w>`[/~Qjz!9vi>sez(1zq$x:*-=yc)9vy6^#o}B-xcWIIUsY=%J2v}E@o2xayE7g+x`;WD=~w($ReBw8ZYY5c@x=Hm;|9Fgn*yNNGm(EUYxFJ(Z>-F3n_ZcdX!y4e63XwYyzS<Lw8#78=Fx]x(ofczkT58EI^Oe4@-A<FzuXo
Cb34#mnvP
tj4gqYsvig<5F=!5?D0WjlK+tUurt$gBBETm,*qs7W?
ssg$kU6vF:y#e"r/74V.(r=(@chK:QrP^zW((c/6piLBbu4yuqKiXkSZPGK1WaD>J&"j0<D6rRhq6v?I&yfP44r-_bgbw:cv]4DQclj97;WnR)VEZ.aVu98vhw-JUPC]<zc1UoOJk-,DJh^j.o>olI0)hI8tZBvhk<C5/|b@`7(s7uR6Z&9UIZs4qn`6@xshWG)vSKsA]R3<md(Q,L0Tx=DR]Dn6fHM!3)?DV.ey!5iu+1%15=5&,oJwX-P@xaYipy?xr}erY$[wMj/Mq[S=81lYcJxCBMY%>Ou*B8F-z)Avrm^/A))Ppn4!?vpaUGd*U&Qj3icSwcXMa|"2`K>&@ulfVfwcW-;VB.lru1aB^aaCSb%gq7GJ5$O!3n&o^YZ41?a4WXCPNM.uU"(5cINwBp89Y2fm<L*c!gCNOS[$n
`Xg$mo!GKUstOr?_fj<JNDqn./vM:;i2tI5VM)gamg5dT:5
T,Bn`:i6,ja2_tyup2mnE%rmV;)g`R%Sg)nCkriSY!i=Qzv:FX"`dG*wcrpNQ0LvnM8)G`Hhow^g!L?~)(f60;H0PGA/e`u@<NI>xpcSMVGqX8h5i7?FYar2>nurEU9C!AWyu>gtBJ%!pUfRoH3?eFD<t9xN30ydnM+[7N-o#_RAcRBZwnw=:aM^6r647vLgaLr|*q).SR[+X@E^7
KoPI-<];#>)4f}V_>;yPnUO8hx0[wuIetH4aa=!q+1%B63>.Q-^=D,GpExCK_M]o1}=kyf$jvdssieP";-`:bBi59cQ_@2nD1XaDIs8_/[h*Dyc%]uUYk#KD-VcsI
HIL9cyM]`:F<R]u9Abfcm":ywB@LyN.Hx;x>He[-3,Vj63ICGH.rE0]ya"$gU%Guw]"`EHxW&I2^(Me:.3lsd*@{2dE"QO5;fF0C[JV^i2F79>KhdP,3D0y":USGZcd[&H?:]0"w66^)$3:;-z3x<XdA><diNURoF!VSl5N~8g4Y>Ye),ib`AO"r!f3[PH_cCmF#03bokoW&s+$wG.lFKd[DPMYg9r/9Iv
zh|44nP9p.B"S(@rI)@d[+84%.!nbG{!OpLI}D5Y(X][rc}bHbHsCuiq
3#JrIf0N@h;hMe
WHSBd?3i[6]"x-~;7Sb9>O+o/P;#}1jAk*Xx
a4<w:A[(W.9sUPj,2
l&U[QN#uFR"#$LlS
sKka1e+n~B8Se`o$G^B5Rv-Jj4pd])n+6;|`ze/AQ&|KnS)vle7s<SHbQK3u-G{f
*(skt"dh6dwxjZ+S!/7s7l66kH89oj_L4L1@.?hC4qR)WVl6mYla=C#@%-UM`P=lX=%K]q<gAw$1xSsDU,9U1OF}KRUvHJvHQqbtkVp^%Oufw-n.
uJ5.v)P8YWp%Bk13ZV(2PaIHa%AO2f"_r:hJ/!)_b6]ne^oEI_tWxlN`JxF"|ju(5"o5XpmmJVEK2Ht)lqD:DEi+%h#/>.}f}RMjq*0Tw?
a/qb>`=k6ttPI0tP');}elseif($_GET["file"]=="dark.css"){header("Content-Type: text/css; charset=utf-8");echo
decompress_string(')OsbOb3V?!K0U*,j#-$TY2N&[`b!>wsTd_N`GuxPN9GOol*1@VDLlh_fdc430fu#lZ-r!f<.+=s=X(J2e>*"$r2geZo4@leYjQ1%,Ya^fK)KWrns9HN3Za[M&Ua[o)7sBH/u8kXg}4drw:$n$88?$
q.DLTGX#<D1t"V<MYp_Ma&R!lNy=^42%5+QTJ"M_zEIVt2b&@<iW5HXxa7"+HENrVp[-(?;l^q7O9Hb]:Sr
,WOw[;eXJ3/AYxWiY8v=afr;mm
2j7~=*!Bp~Z"dLH|e`)gkNjaXDNCg,tOd/Bee9aAhUna-ZLB;OF8<%r2e1x*xX$ZiG_Ot<kzJ%FMb$)(Q`hL2F*U3b$cI[XzX_yVm!=X`6&,RA>7e!9gn|F:S?FGgzw]+AWONX6E]$Hu$5^-Av"t[SRPD-dDP9jn"tZoFsSBWi!U
]MxVmGbSp6ix~D-FZ7DoJXY/zE9!l0/]_ZhqV=[.*yn"zS|U3V:p0%cK5pT+2_?0*<"/w-9$DgzF7#yWi<W,3"4>QoJftal+Tm>(PeM9JHTs;vxkWm9$<A7*iHsBl8Ig]>qQ38jy4P@0/ej$G,X[`Y>gf_|8q*^2Dnu#YI<#>h+;DK|$/DDimVm(m`WCVEYX1jS%84q"FCpAaU/4Yf
Q<ovd>ujL>jlSK$ADUHDsn1a>o@
;@5f]$+ZQNcbu-^=v>xaijt5[sMndunEa-5T28EWI"G!j1uhd)s:ch9c-:STXv8Dq82x=D]meVP[+d`LIY+k0"G?9H47
NBubq<z`![Z&|@7?P6j_[UcU{fnW0X^j_=5(,s<ii_zJS27M>X{xnK3M[W-rsA0k}H{mrK*vZ2&pNC@DA0;NWwLj&)j-eg5PfwA;O70]r,58hd_Eqn{Y@Ws+We9XpZFh)z(-@LIrbPy8da(hAcZV#?1X}E7dx7tw`28WL.XVqgdV!&yvq?3hO5.EHdr-kP>4[llRl9i0C+sj[+"u^v6Y#jXxd');}elseif($_GET["file"]=="functions.js"){header("Content-Type: text/javascript; charset=utf-8");echo
decompress_string('!c4]`nsZ51ptW"tBs^f-cSgTKbI1q0pX
i/S4_e4Ka%hwTAlP"L=t/*"6#@B,7~w6rZfJ#*=>a&]lq/*4>5`Fb9?Co~=jE4u%
t>cAt1}e8q9_RbAy12;Ux.tJDbc6yo|Zprh>hKzjob77ssF?pG<hX*bEjltaV`9(zy^usH40zkMM"McY
V707_X9U3bx~+F$);@OHaH?v1dKl#9q/(/yZS4k`G+bI@r2!9xKyf"jZ5>aZ(M+$*$2}jpf{p)^3l5
iJJ3I5BPJ"xlyc^fCDHS+dK1m1+vCx_MA/Wit:6_]`iROGku0;72%UMS";wlDHcc}X0i{+
+g)G*ZA!w>x/?aKbq;g^FuV#II^?l0j,3G78`XOn?+m4]3/!%^BW:hU]J4c2k=
iJM.o$C0bE-4W0vJI+9^e</7r4vYiOd.y43jS[^7j^Cs1uas2a7$Br,@HQ}V,1"k0J?:CEruov`sKV<v<d0[kjnjqvyd#_myug`xQiVE*y58#J~ynv8y&7XEdGPy%=Q^U$KhDh]L>]c@kj]:csEC^q*P}c9Hn,ESzuz&+rPNW!Kl13$Y1x$tt7T6=SO!]:HZ|(b&7%W7Z/21lKTXL<vyfmwIvfh"K3LK1s<BhsXV`TVV?(^I0cqp>.BZ9O4Wt*71Dh>6mNM2@Cx
f"0Y@.#f%F_@18hgQW.E/-thnF=r<Bl,tOY,6r(Z^*wd)#b7W6N)3U/Q3YJT>,bmG
NXP[y
fUMe{b=`4xTonAX`E@mZen38{K^gk7#!7.i[kr6@;DSaX1yw<aWu<wf
ssStyJFSYnjHv3YP`;B)5->v;nh3Y!G.zvQ"B.}i8d{5+Coo/%|K=KbC$0%9YySBke(UMx^4)@d*6cV;_y.IX]#b]ba]UCbb_iCDR
_lUE:7z[RXYo:O=V6l+iojgjWJWO1
<twp+?^2gc?@i;<MFs_)dJ"j,ih!XljxfR-_RT"j<2H.PVtI5YKbwyPq5MDXHJ}
27#:m*n"hsdc$ndn+
2^T=77Z@.rhk3pJY`"Gx/p#ZW?K>=`wi^I2hDf;0cgXAK@9-ga67rr?;QSv8vS]Y}[UaR.cGkAjR<R*Oe
,627rtV3)XJfBG`^Kip;DhELu.+:Vo33x)#u}-cbKJE3WN$bb_`x`w@>RP"^*ykdhTHe2Vuk9!C/oJCcUd*+Vo&t/nGo(Nh-s_A&hO)w^<N,z#Cs^LauK0d#:NWdpmBwT)v>~XtywX*^8"dD>j;S^us=?L0)yTndN+UR05x-+jcr^]ZThkdLiTP7<nAxcrsahb}DY[&4|ZnAu7hTys0
b(}s{Bk-?30?{YmI}bzN
`q"IEsrfUE1ODTJHO8I>s%")3&s<U,%+.e/*9gXQC>?DGPg5k1^;iRD^l=.Ka3/gqtfqs4*KPiw/LRCbMRq>]/t!c:w/x^F]yuMB
F/1&t,t/<
>vmOJ;Q$[AF`{F6OIWPqW;`Y+uB2MgX<oL3E)Ie`ojg5zL<u?nTT_K=LgN?efssrtCuvmHd4u_ftW&`wNnB7_ggN1DpxT]mI
kk/:72=%GgC%av7fY#00oymXT-JAwbgK4."9E:&|e@:4iQ^@)+S7?$CLlI7D!/gdMOZ4o,<z:[IT/`v^>pv7)rG_dxKMD^NxvDfv-:LG`$k`)gWXeP[n^-Yht~7cq<SzjqrRTRFFY@&(3&XD
NqOJC8)ZLu%PXwG#])x1Y1D%Of8gU;S5txN?plO&A7Dv`0nTu0P]awFPmaDiG?i:TgJLAbdb.k+P[>EOE+j6]:LE{ZxQ8u9
B$63P@,MdTS.wsVPl4r#
X|yT03YF[0M9I-?Mc>`VPy`2.kxRPyd7Hav9ymiLP`I2E1Kqu0/e$OUIwI!}2GpHiMVp*riC4T&DC*D@pI19J/Vk_IJqm
EeaocVyB/p
#Ex;fA+Lj]4m(+{c&&g*Y2L<T7:da81X&78mf_+rXom[IBnc*l4[_I=MR3u*}^=BQ6{B>R2.@XuL=rgo#_{ny+"HRB1;"tvC;S%ki3|CQM3ojRyq[Z!psqn
!JiK.$B!8v(9bF|X6e_PB)mf1=%9ClZOvJ+A@*7%<.W7"xH@U/RH]!a0`/{t]ePk2%8sS:R<*>|:|t_kNoaa,En.DlX<|7##~8@!mv40MNPn.r(+9atk|^jJ^Of9I.eJ:pmIIgI_$7TyXqdSQQ)<1e@SGFe]"+JQMHc?I23j,SY!>,a$9mq"&"$>w6~*@3@01q:QI.F*)[#Fie?sn;OU5g;i-n@g4?m-(nQu>,Am/$^1n7I<iPV<nP0kp(EK~ZG[KQ"+,T):.hkG)@*k@ljU
nzMlEdN3EMcGg%PxGw;Z>e^vu0rE%?(LC5$9<pA:Mwqp_]"e[*!C]CqxtZG2$hp&Q0CN2xx9:Yc}2Kt=+/]wlt!NV!/zj:dyBNj)
Gv0j;oM8O]ra*hDVs*>krd>CSMpQY<1H^S`iw?-Ny#=J6T+P<j/i}#eg"e`N6E;oH$SPr]6h>@sZ{tKK*uh)*If({YONDiFvQL;8!tX]i;48Re;>=;C;u/}5QJD:L&e+=u#uXe&w_OeDQ2$2Ujl>lWJu48`"[MaolYT!3VDJJ:h-5OiQvN"t"y3O38=7xtWk&&9%+v_NU>J]P1o5s?!q+c}BM?,eF(xPX5`dN%s$_0B$A4+BGeJU_#5nbg?
giRqFUeTU7r,5c1&Mu4A6?_qqMuEz1t%Z"WkggbTTs+6Dn4u<ga9u[{Ouu.q@(1T@_rBh<n%2Z]*wuH5$#-e#YcjBg{:Z)
ozqqCyZL"x6@=LJLK="kft8E%Cx!/3_D,]v>:J36079wA)8-eW2;wzo-t{rUn>yGokL#ZmG&+c2kqIToBU?M24.
-0Rb,J"sw&$uw}6i#M+pFM>4J(L787N;
/b;8bwR=Hp*H`Bw01@UHIuG%w%>S;Vi?)7YmE`Z2@ebd2.7!wl9-^=MQm`(/QPZP,`<5B$?Nv74U^="3|.F)1Zy9ykVT1P*^t^X.i?,-tiE>,e|u>=8+Lrb!#OFs<V_WPqsq#Y8NS*M+g0`JM1Q`nSZ@">H<_
`!Lov@ZACjI?sYLXSS33<(N</),*XvGjUT
^?ZDIWgTv9@*=W@il1ddlS+2
g!u?If`25_4(!Ap:"F=S{lyPJS!1SZ@->lqnFBjX(2mFY`#q47awxv-E>f18y^Q-
]Wh[&k;i<=xh.,lfb}UxiP0C^l9eH<!B:+HxAS&N`R4!T8&21KEuv~sG
y]oo~
#l&sCnsuPv`Keq%rYGHI|Vw$B%cKLcVZYJj!"Vq10M7%[3D.4CEv?8R;^.Bgnu8_+${iZOn/w9)9Q=]*K$|&M2G]ic7pZ@YVqSI6^-5E9(L;~9+D|#0`Rb|8TCcVS
d%UK7B_VhyhmN%F(sUab#Ha@x7XOV?f%KejH7!+JhYJBV3B*x=6No"YSR?/+,Y9em4!C3*Z!LsSnxs
?:UWx=L98EBf"-Z*5h&Dad`d;W#w^[:C:jpntu!,3c!EAdr?wBNiq_:P8zeWknpS:p*9cjE*I<<p+0UtRyW|])*):_mFJYA(?QK|e8G,.g8Q;*kNoa]
B>Bo>@.IBBo
>o;EI@7O,s4,2Z%T9[[%/;=IG;kFWov{W]P%_M3tI;X_`u.m9lq"W"8~o2RaD-Bv5]f1)q.3O{DqGRfEF(u?yd".Z!myVL;.UpHuI/rlg6-d-DbE)`#p$N*V3BniH0f9-nZLs87Uh.[deHiPGY2PM{TPm|&Phwl?EuXa4WuBQ4Fhg+VQJG%@V8JH6h?PXf(VB9a~@pPWgl!.oCrwoP"O5y?qiU@CrSg@^!e:KmvT"W4)::e,I+7E4Am]Gu6*V0S[;k0v1_ZrM_mO<@g5`>Fu_sP4Nl.]x4l}-X%H2Rhkoj<2A9dKwQM&15=,s+f%E|GT!O&/aA`:Em^mas=BAkV(nU8E<}n*j$tD%mPS:s&CMA0VrdF:2h*,kzMZiG+9!b:@X>HiFt9}X1c4tp]vLX@=5E
62/We2+*b)2fJ-dX:uY-+:hpmhr`uWF2:;jJ0V"-`*!67*4`Y?Cj6rkhHVs$`AV?RV2Skr:<!O*+0*CG(%<<5W8UDmZT/)j*zB&^#a6[!tAqdd$:=ufG~3
_aRvE"Wi^Pwz*"8F#BM:^;=3CF<[m/gSO-AEPBpO;o6]AzkK3q,8t[Nx/b%su=#P^zyJ3>gJAq`=o)6n&A#NPCV6&00Vi./WcN"dv:2xXP0aI&Hp7IT`lz`qZSf8x2oJX1LN[l,"$eO.;++?c!2804Nhg-"G$,`b-{$7CU@6PLcX`<YwlIrr5&0.^ly`"9dH2_px1M6w0r3cpp6TX&N10`b}*HZ"uD$T!Tws.I5sjBtNEtMn+R+o(N3_ewgn)A(@/.;J/iQDjt9Y]LEF2M*=^Mo)n-=-L@whsdAZvAg+3%+&]A=!Ckw}!un-]-b3A8dX4|jK+vCx(ACR7
)Kq]HP&cVU%=vK=vWt-/rpJWGA*ENv_hQj0"nT1SkH&l$T4:D*wekT>,*^cmLsoHg(G~dwLZ&vF|beXh4ASK";eV2tS0t+rs"T`7_1(`xmx0iHx73n"kT~;Kac+|hA4kQ?wg_xdX@>>BZph+;?Icf9BYf.W!N(P=qL>PPmZDl,Xy*<SsDQA1*SdwoIq2YN&#PHixZ=.TJr
vh$Q=F:*weX)
gb:!n*,QT^1tC{`3:5/1exF5
pM=-lNw1Vpuu-uqol8tg!2!PLQ#uHV`J?o(ue?_O-03JVg#s#cV/vuiwGun%:0acmf4/mP:ol@d"Ia<Rg)DZuV&jyw):)@mP8Dp)#W$yQ
iI34
^W@)`uP[uI)ivMoXwR+nugY2f!Irr?G!g,E.H6orN|P1[j5LTTAlluoYyyfzL<#aZXt:a|nK>MF8tuhaod.F7R0G2Xb}4]
$=F[GIMM;a2:iKg]fwWt?/@wZsJW"sZ&9PhsL%b+cgVG.d"n+g=4`jMUoC/?u6
VSk{,>ksAyX:M4^K1JI{!c#rtgPyniNlLvb@C}j^/z:n!k6Y0)Oqv}&&X?I;5dk7kgy:@7USe`,0pUL6Z[6U0k=k640d?<QXuYU"B&w6&B$-I
1@(
RI;/XSoSd
2Xq!G/t]_JEl,F7,V5xA?T?sK/v6"e9$UEq[;ohCe)jQG;RHEV;i?V70Gjx+lfaN;#YwJ?^}*adDBCRacXwE#Xu=u!k)gt!r%@w>B9^9w8,NC$s3Ba-)2u*HhE$vGv"aJ9+{ja02#GQ!b9NDaENzYY#X1{Knsm+Hs2H[;:dD$.)di|Ivti=Lv[Q=GJEE^MF$GCF"*vuk:y`[u9#W>BSVj=M)&KA,W#30"^94Ih8nFgdJT=NJP5Q{U{d9f7d4=KXZEDX
E_e%iZDT1AkWfX@K0"<nTFw<?}9.n>h{Re^&U~PaCMWvvj,h
hmr<^-8U72J<EDhkn;26_b?xwVDR1n#dD`8ZY.]a}G*F_^xtc4
D!2rn-1MZT<*D(8aZcb/pu^zGq(a/S=a)S6qC;FWOa=]alyeN?$;>3q5XVKkHIX,/2a{iaUG@tFvAPAoRe%/@2:q1gqY4`Qt;O:eK/_VC{C},?fjZ>
84)8!R,3A*ACG(C`r;W!HIO1Kf-EuDc%h9pYY0^v!Py^}GVti*uR%6@aM#qXfWEdC0mE[nZ_5,JA`F1!}dT!*$E!]94>b?Nb"t*SxPGlj]|FqHjnVJm5K3lFVGV)L_*${-iq4)kg{W#T[6A!k#phA6@P,pZD>QqfH!s8Z`*$7MJjcb$Q=QTvT*>K("Kt~@#goS>_5/7)7GXq4Bh;9G|CN9|1a7WH_By2L<&$>UdiZ
3fE3,u!iQDSl4T-8cI*71@laZjsnF6=$lqUeac4>L<kbn_4C|N5[{Eqo^W{>p3`-e(MYkcG@;*wxBOsaV:z+dGjrnEK>Ecq#9GUx:5l,@$vpQg6N>aLGnyo,yHB#<s6qN,3T*7HMHO`a5yXJ=S@9K2KkZ2-5|wmo<N8Kpn;+Hx+LB26@l+Uhg6}w|Q:Q<J~#<YR:{E4sHK9)=A4"5rr1t/]*EdU^TLtyAwXrsRABLjP(Z5N9Sio+f6%3_mBJGTevT5G-/w&Y2W/A@D"q%1LHfYU^k0TJ9R#H>s@<&]&Tz$K.IP})aN7p9E2W]TBJhhP&=BVh{gFK.-St_)
Z2J[%N@
c%Duy<PTbL.2D1
^$sfEi"SF^~br]]/*wk0V:%-hy4<5+PsxefHVCFJ@,,;=jP7}g7A*;F5mR^wLP4R_0"):+r;42o[+9lI1EAHO#~8UKU8=O+"05Pp_=sl+Y-N0GPEy<?PMXbCRlX]C(Umom:puI5$2Y0upVj-5lD,j!3
0Z{QFekr/WZsDg;v)L0(X,8oo+{fUru#|g6g+[,cd-A$nA!JEVFT5SzJb&jywVrev:,51L)%?Wt3b6!
P"Ng0sWxK)3/TR|?vX`eC;XjBaU8x,>@1#r%?!#&v&w!980[nmn!W4xI$%},5!YsOr<j#Pu;wu0s^0t](t@y
?1GTKyGd;s.fMzac
GCb2IW(sLV
b,G5_J/Cp>
?$=8T[APojU>G7&^p-GY*O8>&gxJ!Tm/52Tr~^OxDy=9qihGR)/a~C?+`c6bJ(A2+,P9ir^jtc]F}@;o!aV3!j~=0^"u4lPr|ZWyr6.,l,QlGk%]94Lt&uoqHY
mdVEbF,{6!)i9xO]*c,h%BOK3P":,Mw.*^17-nQ!TU^9#hB(5lr(;*t7Yp4l>I/N.#=1:qJCfQ$7AwYdE<M!,qXc*1>JN"AiHRA
2Ld~?;NxbU&oi{l;0WIxKPwtc6)&%r+LEZ
eHkn30z^.avoQN2sSxGIh-SZ7E&Zs0AXgF|jZKFE2kx[[TOKrlrZhOa)|pL)!tIU=kQIR$VW/[-Z5B-j&+XaAxRO^H+
$JH
,a^h/FB>ehD@F[^9fvN"&.cpt0AmIPaGO`KOng#WabMvJ<r*NATf%wNL|n)qtZI
TD]Q4*Ww+7Al%tbS+wB?o-!.e)hqGpxYxG@P|jLKU
~-e%tNgR#AC]aqhf+u@.W7imBW)/VPc/sB5!&MF_R@0ito65
7@kZuJ*[:<0zRD>)c1Hem#1ZL&]&MN7q"mTmQ-&Q(+UA"wT9F9yA=rgtAv7[S~[M@3#)G0)d;KXYCaH1N?Fwuz46QV
(^*XA1Es^OST1(aU<#
4aUsuJTe3~kpX2`_HrDRL7r2),A$mohB=2sC(d<lW.;|PCQHrF@E1]7IivK)_Obd!5f<uNq3aZ&urmetKsNtyHecc%@;jD5`;%p4k+dl;S0y=udJQ*ivvxK~+qN_NP?pE7m}lw1i;%dnN*^aWf
$12uX%sf@F)t[U?+c#bO+ddeg>b0dc;D^y^s_^|Ug-?Kv8qqbh%pCw]$*lGtsr1^+LP=(8dDKn+?Qc!E)4|;B8Q+m<6uE&j]#%v[oyNpXTbdI"V,0mXB4@A9`Z?Amn<]G9~kF/kg,!@KQc2Me6:g}xPU,L}BuLoJHw2RdE6WL2/4W5o5Gdij:*fG,%zQ39&^:kw?o&3DQKX/
x?"^s
<n*%-x;11!@UG#RH8(76o"u,#dZb&3kaUAUtC};U,}h-&d03E3EMYDkfwy7(/phD(`e%gyiDc0Ls1eKGRG^I^3eN2&r?/oQm*Uj"uYNMY3$?@zACd~gX]H@J*Aa2Tk6S,2&ub)fBtkPWI?S*4,DGCbR?gQe}nA0nRmW((O5f99ty@{74^3F{`4%ev08Q2%uxEJ_(Bai$JO@WPwT%)q2"d.
oL3Edd}JUeh[r]ZXw8io(oj_/E.EKnLYes,lWkz2!qo!(r`^1WEK8$0AV)Xf[,r!T9bx#m4fGYx[>[BucXG4rLS!3Av`j^!X}_%uqa`QwQCcXZDwP2GF^
#3=Q
T[>FlXF{i|UOg]+RSiVUf%h"0y9LR?BG`z2xiJ65=JcU`itxogvx_/d@T(wKeBj5mF2X)d53OUQ`@=qJHpVF]qkC
%yR`BEF8<#%oyl9ErJKVW1z;RSju=.WT0

KPn-g+c4cKnyyBL}yEyoui`SH&1D3+OA[S0Wj6"HEL"evBx:F>cU=
ofn_8,]Cc`^ge7#,fSvj0%"7X*EkWzpJu7JETGt,yzs93u!2>2m5L+vp1e7|JYP8,C2gvz]lbvdu47a.Ki]h^o/f!X/K(P,2PwQp4ySL:RObaNNi<3,R*8ry)f9[ffo>"C)@./oK$o`/eD
-B0MBG*(9q7!u2-HrB,;ftR8t*U:|#@&*iNDSD:
PV/tW9`i:,w-dJLSCp;SH_&x5/4.8RERA`o`l3pWT^_bMps#e:xT;0ndw-;Aa,&f,Ehm[OwIr!qe$,ap=;"ezcZ=M)gQrq"XN%HBtn|s#oX2xf!yW]J>+U0Kb%NcwW|l:W>:If>2=`wp=i
^F2t8tg;Kd$4"YjnTipgr_8Z$
gR@
Sp3<RhC<GRoZRbilYaMor.2&o[cEv94^u2B%];`yL(QYbz#G$F.sb;C5@sidjfD)$SDV0j/LcP#7fpOB__hT:S%j(k?>V$5|u+e"&~#Jrsn!lO6^#qq!rZ&dl|<_3FH:/4>r-NNl
moY+U(eStRsfvLH9o
UN
/v3U/s?4WFw+k)ceHzIdkie7Vq;jj__o<LqlyH!|#
hNJ<:!Vk3b5F8=Rz7B9Yh#mW:7"|4:bKUF%.OFV_y-%uX7^&g*b`D8^u4WK!=%RfB%J!"L33`b3,f2o
??`6*1RCY@m}i*rb^8LTtw@?F~i%cegw"9<7KAUVph3=u@
/E_dxlJVT/HO0%SbK2{O%1i3Zb<q)r4CM=MpQ$6NV*(ao%w#9cGh{-h4.NnT[8+
]G7<gv#8MaMeE0fdRLvqkT8d2RuY)7*_&ctbn8xfUYx*g>4l*lCAG0OL0HY5!-",B1hy]1">q#RUU)UR@vytZr~4webt"PthTZ2_HYunw@.vQD8U9)9n50KdoE"N%C#&MC]/l-S;k`@tH%16)pt3w.}3@_%>.^<MQOC"a[OA_%EGzIe=Te^g{VWj*xv-kutVxieq&JU1<Ajf*mp<Lc~<nFKm[Cj8h=#o{+%.%@tZ"Y*&x&xc8pb@DF{GLg.+}62OXd;TNicAgq9vO[B#NmpH{](xTs24RDTO6rD7n[r,:,k.p;sQq5xDm%B1jGf,8tqs4W0js<4i"QJYm(v96`-vYSLi_ToaR&]vQa-&-C%Pv6IAed!O%g~&;F3nRxwCC##h8w]%eF[AFUg:`jbE6xT[5xv4,Rn5Cf|t|Vi<;F?"|AmkLAC_ZJIZNMIe1(:/nNcpJtW#T2q;nvg9L+bh
(5q8;jF+rp)r35,3T@)x`M#eVnU[25y"V`i,rS&V23<|rkC.!,$jmx8:Z-$AQz6zLiFTOJ5K(0iGQ:Opt-HG2xW6.lT?U!#miHWRCP8GRqtT!DpbI3/ehvIn+_cLG6AG%dj"gy9x@*n:?lC<9tGHWG$opdl0^vPw$pM1azA-V5cB-fyUE#]a"p8H
~.^mJ/?53076Bn!":]5Jo$L<32MLnUG_2s=JtgDg|(M3bsFcn>=SmJ~jUH*t*[`scF*Gy6SH`j2]`G0/q(x@-o?Ud`Yo2oHwYi6?(Ni%W/"]J:G-xnH;IkI=O;|rG<UBi/UHElAxlBqrl%wBbiZ;sK;3G37Bwwm*nngSV/d/<m(&SiGWA_VAJ<;"tV=+=73)+ASGa#hws"$qM+RD"RUm&uK",S`G6ZusED@gu1|&q!Hl4G(#+,`Rp/uHE#Jq>T-%NjhjRxvbo_<**@qgWmiaabTTF!wjvAGS?T*6sdFZSGWc*ntB#jo#AF[3f50G|E<hzq0I25v8.1!%GIwJD8ZD`.4H1UPo,Z9b+F;V^H|YtvEH"<=I,Hy8a5nb".4vxGvO?&>HpvTpN5NWq.9-Z([lZDDm!Vptk<<yX`^qJ%e@g8vAUH"a)FteMjvO+=;B9xg7D5/!!<Ak!7KQYn7La5>*<B3TH&,8jNqQ"uRNl)+t=vg5)1p]-6EZbp*B2`|CRMkw+[`LUrYr#p@@)mxg73cfEkqA2@qA8c{_EH(T+yKT!Wu3ZEtj-fqT^;z#2]L+V^![(
6`fwJf2d6Ocu9S5%1YnTx4^TlE4)$Q3ciGU*fy5oc3|)_%4WE!Ft+2Za+K1B%9OAoD^/jk*"~Aij|0n9huBIyav=}299%qC&5-(G_0}eB`Bo&OPm,Vlf;mRj(_nw!!h[QKd&d`TY>mEWq?9&l"x)k-huiQ5(TOBT>N3I$m).x8eM:2y!c9H-d)(k@9r#vC,nG,>qpcz[j:9+
TY6(`kvX,,<qC
yG?+:cK~fi=/f~!)Tr3582HXPSFh1IBV`*v)i*llLxa;I+:.fJF/Ij&]!?Fl,*LY=ocA-Sg*BI[^x#8s[D!F&csW38-vTQxFhZ
NV&fNF)<QV]U{oj&DJ_YRKIscEk%$3B3u9H?2lYvP`bh8G0p?wY4F%:K6QJ/4:&8qY#Y7nkMN)<S2NLu$$WHa)p
CJBDh0#:U]OF2phF2-jw"PRMtK/sw5?w^(y!:L-J$m1U$8Dy=SC4uYxR7eLUF%ZeN$3Z1Jk9g
sJL*"c`&"i-dw8b6$jHYq_BViUQdC"{ktBC78/pECY8h!2G`(5JNH!ebIh-aKA,?g?
YALkkF&CdV!6=m$3rf"UtRu1d+Ii/}hdj7M?3SyfHGz#p!cqcb`WHnEl#YjcVV5&eFc6qgPOV~enMPZr4z*F%?KBqoxZXh41>9YU4VqO=zvyIJsU)DYOK?yltu,V*P>VQz)_o9@@Z0/s0Q0B
[5%iTE7tp)y-*h_q@G(2lj#*0c&SrW<GH]*4mgxmmp[+<@:@s3h
CjR$2jyEFs=xn"=Cl]]D:LQ6+KQ#!H1HeKzs0&yO*M|bij]J5v*fuOp*NyTN2B^72_a+p=Lyuc!mUsu6R.sXEn&lM${lquO(;GVOYOvQI3^fAfO>zjT8cd#*&jA.L^OL&.=Q$ly*RYnCGXiRm3(s)h<2}RWL#(or$T+Vk*a2V
Y`G<b]4@&%a-e9*D>8rdi)!:eZ#,PI4t{<(>1^VEG]GpU`nq{sCA4@;xkGL
=FKplrQKYbIiVh{3]3v^Sn3^lfwn#]~uZp)3Av}3F)WX|tv%=7wpR)/xl.AuvdsI`mBF?u`]I^e
KQq5XNKRh2m-K,Bv8^cs)cy@HUs;ADJqi&G
**?J5f/9%#!cPbv9UUZ0rih-V#E@6<kM)7AGAL^>!^gqRpweq)&1harDui
?*B#8r)$5+l+b,qdqv
_uRy8iJqu:/8lB~DC1DHMESYBN1&S:>lncM50LN7KJwFoZsCUIK8<HveDcb@CYu5:3VYENzbstWK/o<,m1iqU]&w.iS^(qCFz,{ydWtTj<}6jX>e0u,fR+LX&u:
g[l.7yF(-5/IUR7_[e3B:
i_C"Gx<kyXUUmam
.yWyu
n?C5"o:jf#fe^+|Z.)PvR.|hH)k,28YRfQ979x79}k3-&$niBo
%?bRD>AXNd7?.hTPIChIg]I],*y).=
=gmI4Yi?GAUP1R,(?43F&;,yys.IU
.S$MyM9:>aD)lbosfViK^T;s;#w5NaTUXMa&d/Qs&B1-l0*UEbxA@sXl.JMD}2M>h&2<[xEpj<7-`--WJuDafS3dSn)bi;j=-$1t`8i,LDZa@.$NyB6AS^`.%k]32CG1,4x]Jt@fj=ucYcCE!bM"p7tc&GvI}8|5O1p`ZdJcz/.L4aW>0pu[Vq2<QrL<,Qj,e;h;+Z+!BvDF+1AoliK:0)0@lCu@O^c"@mECe
J41oV[0%1t_H3MZa$wWhv-_$*VAq]l_@;2fnf^bo/J[_-*#J;"HD+W`CilZu_MSdUaIL9YMdZk)n<Z-u2x8v7"XtS`VcAO?1>vNPCohxlP)&x+kj#38EpA4l?U=[Wl%T))YKD/?a06kQ}IgrH.}g8G.#&#&O]HLYS(Zg3:Q>)OTe=rz5;?Sj^.RFAxM:v8,(@#e[jb|9KfH?I,-[H3EX}5}5WX
"7N-Cq1g3u61&>,$IPAUrb!rG+"stMuM-(<3(soNvR.`,76J09Ux*4xy4`U{bs;k$!rf`WB@(M.Uy+B]@R<5<$o/>PM0^N13X7F4k
PwWK0{iftDmh7>w**S_#FQcM"=*5T;wpdRK^6E,r-q_[,"qVv}Xsu~!h)3$<lEm,0&IF=2k.%fvV4>W*0sO&)!d=(YX7j?9|E"pyoTITFTXe$y/_>8#:w2,0v)?Jga;J6Jm),;,C95c;a&e$$=wN6qDZA@d:1[=G1F/{x=e9$|Hq`DT45wIu_Kh_GRUOYUJt6<C5o8iN%2rY`yc3_x8pE]CCh%L=^1Nf4%.5qCEQ#9R=eK[kIVp&W_fV611~BmO~VnsB,Wd#/Z-,pb_+XYiaVG$J3$oOi1d8n].I$-tqS8"hYg[&$555[F))UPhiUIh.$+5&M<$N%E1uYTjzUKP_Jd=HKeq,5)82/0K%e.To9m0c(
E^V}8E?oxfu9O4N)jP,lNCeW]#)2oCnPFP[TT2MWczDuqR=wx&%)9(Dzk:c!Gc:6x$qD=M#)6d`ey{uryfHG
l<E,fnzNOW_"Zidi?cE<p9Z@sGU%-d0^bD>PJwD[6,F(hsCv1(eAsb:
@Q4-HS)
3
e!7uH7;
.h2QarQ+$VH%j#oTCG|-B#H]s1*By`&!h
8&$x~(?8hrmJKAa%g"?D"TtV#1hI/y,FzK#q)m"p?I$GDR-nf)RQKDT"$*AjASN^d*2CBp&*ak(xqah0HN
DayE.3d"":
;Ds9QU1"3oFZNaD*j&-#^7L1mi6@gaR#AMTe71,BcD/-U*>*1/=vEty:%u^.Yr4#glVETpH5hHRs8$i;Y<j3g/UP7WoR{TQ*#GT^f<m*n9Pk5JhaP;j2nLt;QKdN@;UW:4f-sjgkgtR1HJCZ86JY(w(RF6>AQWu-bP0m-piN0>i7-_+g^q5:3g,@+6Q_<y
>4PrhLZS2YfOw"7_NM=oyd1.6*ha.vE^Tr-9m4q@m^DGOLGt_HqZ3k]VZE,Z%i8)p3E2:p<vaydR#[>{Heo$"(tbV!7D3k<M2by11cF!G:uTclb0rE5:;<Aik
Ck6O_<Ek28`&IQ/GWig3])7MPdZ(U^nR5OhcpLHWC/DRC;EVK"-(q7-d3%"5+*[L7)?J-fKXEGOJ>DfNJk_TgaP=Ti8/(ZJ`")bA#@-_TE-|3RONc%QR*x
qh.>;CU*uP<Rhv!o9RE@?9KTnK`jBiwr;x&1x#=suMZ
ynF.l#IP2ZJ5r]z:v?Wx{=`(fFC$!e^PC4$-02r+},~^WRHKs(.::<Muo`z
]SS082^.SH|#?O@M!3k>}q1<pAVm?PT"qS7fP<0p7Mc+*LT/jFV*zd1FW#CpZ/l-`PN;Y5sb2_W;k2Rb(+63vP|M`dr&>tpb1DHwe-eZ[d^Hm7Fl2UVGOZ0MX!]&4&@3qwY5dUaTz[e1~f~%JY#:5gLiq"`Uy8X<r<jjOZ.dOmy2=*vTS0reMr
5ZW93+$+03`:wE5ZVp($!XjZ[l4&hlkW%(b{R`8pO#ZT9J`E.b;(`8(8p[n6kFsxt92IT+7;8KL0"L(CG%RWg#s[56El8p4|C`KAHll;/;Jp<7F{B>qO;LJ]&M+Mk,AY8_XAxPa<2sER#yq~
(WX@#G)C?,8TN;&7laPp[liP(gmxlL7Bl&la76E]t!sq.[*69h|2&n5chW?u`3t;k;W+h7|ce');}elseif($_GET["file"]=="jush.js"){header("Content-Type: text/javascript; charset=utf-8");echo
decompress_string('#hs]`sBZDp8)6qiyhhM<^0T`)V{Vi)bSi73Q"1]=gN@TnLndr=EGb.Twz:GZvaV_xn}")>FPe<yTG,]y^DyG!Y`Q_ZK8x0PLAuOs4BvxCt&omYT@dVVnR7ut:Z|S#rmd>1ikKvQ[fuxb(G/XYCym)Mbvf<F2auZBgncdgbimCs2uH/G`S!/dI;,pSp]S!H6q,=Iv=Pj49kYR-v=Myr9v5oBTWV$][tU[y
17lRiBE4WY$X5=MrFt56kyx3aAjsl@UP!u79&G8tWg1!NvinMom-x:!qIG|O:ZF8<
"h
W*U$/XK!y=e%ZuoCqN#+X=M>kRA+dx#CG$;}TPb9ZB3&-d%]"lP&#DK{y{a47gU}%2?d"NvAoVTF$Z@03|h.C6H{Wy7x1:5Cp97vHBi6&(`?$%WF`7;"i,nby0Wx^2B1bK2"?W]UEbk{F3icqi6pX2P]GZs_/4XT[bLO[G6YT5,uxo)UJM
"A|lvQ3!.Iv,Eb}^J/2W,y9*OsT:(L48qG>D0PF
T4a@{dAuITE5(LQI42&>L`>yLUf;y=6wpIvyfLl-C
=ZCwpGP0k9l#ytvngwK
GWi
~l5U$h]kXqau7%=wnYF+x
-=7g}YxN$1FW~Bm
M[,p{AedZIl@64-C$KkZ@Vej`&")lwG]1]QwI#(x7C/=u?P>[C$DIJ]weH[wMp[i%7[vzqK?[J*]@8[=*6HKi)BYG0m6?aZ;xvmXMx#lWbL[KiTMWK4eRIS^Be0?gQ$[;?s`)c@=H!c?O_jl{a*crOTm2vQq=j%VzW+nV4HVrF";<p+4yIzqrBI!EGC1:EF`Zy67FIuttM<?//@ZL_5QIm$430(y5y|db#nhD+wSd!%GW!s!:1YItAi1.-iJqFlco2F]&flFtR-`[ys.0Adx3uNyc>;M12<GT(|5iUQ,WyvU}p+C;c1V%[cSS]g
N)LjDK1Zzs~B}z)edQjh/MLKI3LJi^C2;!4hjvtN_uy.B7jX8sHbvug>q/{8s;C;
`t.E3Fc"BWvOE*k(r9[l
?+ZMltGi>tYP?IbKkP*hkJTCjNlxDGlHk%-lXX3(s9kkUqn;!aY3xm2V%UhBSk
[|k6%sy9H$F7t,w0f,X5J_`mI*]JTY*d7@;Ycge}q+u82:nxO-loR:fg!kuJIl%5Y^*C=XJdmgZ&9YL7l?LXG_2$GRy<b=aW1To!(^#^
2-VF^sqL>Lw.48l,!]>cDQe!banMZ,Z[0tz<+ECrraPR?WuUh5)HP.V){.yRdm*v8l>3@s&_c^SdI_A9Vj{W6+lOV,P`JfHyusbn|=1[@%s_?E=lgA?cs7|8;a>k3C{KXA
o@G,MAg8ikRhv@Ybw]=z<9x$(W-UsP[Bg|1NYDo&gI2UtDkJ7,>$`-!5x+gFLeZ}n~ZW0
.2j_UvEB8|MD[<?QO|kN!"uU`W6o[WN)b%X3vmg!2@;/`+G)bMHHSJFX+
xs)1KdgtJ8ih9/Fv;ncm&ainrY7jy|hHqhEFu>T@Pa){Li&=OPW`HYGy0j"5fo3-nfr`5)C1C`%&0&x!DI
0lDXL+|EKHiQ>^eU0ZBEc9~5hqCs:`;;n4(wu$]iN?ASy=(.r$ua?;_BQpnjCw""PXXZgn|92+LLQ%z]8^Nu:da[t]<L]_m-7(xSl-sps>^ba^#_@UJAIbH^z&lK&8Q_VQ]p3h9@Uf)Ce%WQjVJXjaG&XIx4|)epRr0sO8J
I=Yl}q4rdycq`F([h]0wgmW<%U1/~>e+=li[bi_=<Jqy`[r)[@XA1>3y4*2*Vfjci!R?t950JUydJ"ew
E;95*`Se9Y=t/*:qSTug$#h{$md[H+g;XF8C04l7dq1^[fMJ4TAnqy&$/k5iX(jx_P(eq$F/n4dt`>K4X)0G<69B?wgrCq^%&DU@+^]q/2na2kVo^N+|9)f>32L*t4KKeRPm2hiO(
[6=Hl/_"usvVy%6mj0>U0Ha:2KUS]_VUcRxB%);43:V0EuF$ClGd7&o?l!:.o#EAVh(Ov-tv
ARZr#x:
/4`5T-`#8c`vtQ?Hg.B<
P?JY-"S2`sIjBUye+-a~3`!EA"9S?6BfV&m
0feZDTT@#u.hH;L(DJB5sF
YZ!e[yA3WeL+/ee98lDA-;m=GoBrn%N*6_v9.oHj9Pi3~pzwZBa(FQo1xDXE/yIgTc%LA3d^bTL.N+vB8Cyk;vI<#lLaUwDyS[S<HN2;F>W,aV*%qk#m=Zd9IwDbY-EIwqg`YWv*wmAN~n.a`8pZ?^Avo5a.AeU8Et/cj`VM!?t<wXeX|X[=%x(o0>t7WC7=b%/060hP
z#)i;aIuSyMyGk,CS~`..}SI;`yb_kiyAaYK-|QVr3oW&:Q<I0cJCVJ^eZFemD7R&#xdYdbj5O8IIG4jloG6ir[:[b){OR1T:fkDI@([xMu_,e>oq
G|!{K+[6EGT3[*7a,(AZ?LvR%?VbFs*gm-_TVmaLb-`!kzM?bXc_7`U^L
A%)I(Y!|T@b0+!*MCk?v4DL!spKUl-M;"U7~vx6hlnE-,tdh$jmI.xO-o-*S;k.g+]YJG;K<
_G/lEVF?*FdIr:ytM$kK1*7GAHXX|^%Np"qane8..fJt_gz:N!ruPKo@1yR^,=2HAO>utMs,4J53Xw=he[lEZH=0p,ZO~
Sn
7EXFcw1+>~T~?t@9cJQQNFrbRvrWuOH,:LC"*obqZ
$l@[k_Nm-MKij</TFabyfEJYcdkJ9#Kb7w$pR_`,OAbLF)V/(zpoGNfj:=fq&fi.cy*QN/BOU?azS`5^&i0KUohA-~tP@Wbms<(_r>nv"WP0t+.-x#]b,-wb3F<#8eMiR&S^)B:EGJ"hyPq@Bdl`q?RN2*[+)QlV_JJ~^~/$<KW8#z4Tcj$?2}3rB47FsQ?N2.8@3NSf/qmQ`,8dqSmkK-Z>A
!5*K<zdy.=,[c{ZY]~>t@i
^;l)u2D]~Fl_pEuZo;e/?<4LU`5=1(
vx=)V=(uH+
=AdC[IG
ApbGGKr#-p@g"&m#hEbp(#asxc~F$0MhM]~5W+Q6m%}huQccve}v<1@T!h_G!R`)imYpo9N&@JZ`=iiV(dOU}J]
vU:%5H*/+EP-x>~Sg?q=p
#YD22
Fz"uonzj+Z
hVa54%H9?E$/uk
@ok2V_gs9*<+$&-cIu=M7]5?N74r$MG/Q[M=SZiYi@Ok4:Qh;l;[3i_wF8JxXftPyrBgxtVZw/1m&u)W;MOA].:ZICb"QU3&0@LP96*6:+HlSHA]~0i3?R^?O78h4v#$8bweP;XC&b/(hmMD1n3o+fZ$lLhJk1=M/)>H#BsEVMjqMF!",.MezYu35maBUQ
cZCAL?^=lKu89{p
D]SKcDxrnc@G)tYp==YHGSa~A!W*tf`1a8Cc9:VF6e*+v$?3"D<O4`bO[E&b
MM-AGTWFCfFXt)hi4FbJf_t>IJng]V+0dK&xr0#Yi81;EVj;>3xL=*3D&X&;aD2ALq*a[)/N;`C,G_LKIP5,u-x!<?Xm;,)tY[SroGWJ#92><#l2vyj)1kE6LP2f-c1YS3LCcXXuvElO7=h[6gUIdAqCH$an,inAC/(P:p|S]Ne=d)80uts%]t#t0kcYv!ihhxK@bUV;X#zuJd!EBtd#o2,36(P&EyH=5g`>sMf;tq]361$.=>.1:D`1
H?SWk&ero
P]_R`E[91cBqY2S@nL2dPdG2)DtV"ITC2CVXX?EoU/-0:(BM.-9m`>`rlXO;<pA@3D9f6cT:H`Fy1G$a[+V:6g7R2a%)Ynv1VQdGQ;F-qSZ5)[V3)k:C2
O<l:bPw=!
se+4oXWQ3G,]!y5`X>--@}4CEtD^T[Ij&b=W>GpV[gvDZ96R&
#y8ZSXxP$h9yYhe1tRChKJs?8Qq6Jw^=<!s4HP,~-!@j>bG$?$1s56Z#S
3f2l(N53-t
/25H&3va3#,6(Me&YK.1nYN!KF$<{ihbXPiyvxMT?yuvn.qM4ya[=s:n]5OFLb*r&vi^!Jh;lyU>;2&hp5g4PXp)Seo@HDxo}gVwj,;a,Mx2PRX1-y_H7XJ_y>,u%d@X5[Q0M!3`|1G
ql2yrURR/G8cLk^QJV.TOY?u{I]bNmxya5%<.v$^tNh@f;+32&KeCW
t~#=7WT{SDc$o]kzdaN;o$7|hm#w-JKY[[cYyFJcK3B~Mnbxpek4A]+zm#S(2=)vGR3QrZ!I]j:=WLrUCii~BPHINauK`x%ju6l,1c;luz>C5$c*_Sm>XrWO1b%a8X]z*0>k@"KI<|65oLuYET_V.C)1x;Kf+@/MAtmnmU9$mVq%IW^u7~w;x|NM,zdDhQAf`H9JqA;>]$G/iv8r8#(!U;-"Fzhj
0z(IM(HKgJ]8RUtm:ZJ;``p8Y>u/<
OkbQ7Y^l]z#d{u
t4umG0a$7CA~s~R0oEB{;C[2iou1(]#;Gn>pFg+[7afIZ.r2^NDsao??NK!2BNt3%Yvs>~pS[=RwWzypr{yR3M+?C"Yr3/$Wfu=Hbu1WIr1nM6TEe7qS?"p`
X)MT9+LS4y#G[6zyqQeyQ$71_QGsDpg`f=I_dea=i_4FW-TpK&x)om02H<BDP2(Jj:1ltVw
;Ik&[uZ"s7[;?QNEci>/h/;mm3H3~Fn
_Z~[#+v,=K]VsO:784&[klOg=gtK)sAq-<qT/2ALtQO:-_k*e6YqT+9M>xS]yJ_!-Yav*$9$=Q,Sas9XUHNMns%a>pUVZcF
ZXO!U^:wFhchI]iv-s0M,%^XnoWkSu6lohu"J4LvPf("OhQ1[E9k|yuM:6aiO7OvcDsr;"_H1`GMTGg+81=HCnus"hbDA`c`+g-I=4WH-!Jt2q`=*.Q3f+Yc|K8j4lGLYPmntdaXkvybNj$I/k9wfkrZTm{(iS,&G`q
~P~Z:t5_0+?h"<q8&nSY{0x@*na
}`7jx&YOk+s?ux(>Hm+0"mYs*sVC{x(wpdM)N-%4%u<Bg+iAF[rz"ojDA5WOr9v>o?gr{:XEYN5iUBkTf[pNz
9GCeM5zT<`T=--:q=@"Q%r-q&=*
:6TL/4SBwteKQMXL6>0V
A<Z6[!39ey&|jvP`R{u$Kv=GK.I.er.]N=doJLS&Y_RAfOp:)pu#wz&x!2Tjax-6E+yf"M%ORiV)G[`ZAbGC5^B{3Oqs(*kM7xC4kg6js44Rz(EYEIxC&So},j;bxKL_#x9q=^"ax"HVY!wMC`-tOH%Iqg7J9$3T,ws)rPGWav,~%-ATt1TGmPg|-aO$n(bqUuiI;C:Edx)jed1;g-iX.CPnB)fWDgsg-%=vJmUu6Ex]qj+F!jLkVFlO%ltzVj6lOj(5(3pn@tIig<UyB=w>saA,$#^m.PO(jjci1%w9QzsgNxsU9_;ga8xG-_=dDGTwa|Z2_vd*em/
[[CJS/%>c@:Fo=L&Hx`G_Znl#iSvqn^5c@(75m.ww(mOcsx]<"l)tk3DWxi7:+>~*AXn&.f5T<%/(f;5/*<THXN(3alZrMBT@)
g7YOKxW;<e~o$`99X>3OzJQXrV:5an6u*JieriavJ$S;Kb6=s6hop3"+#YexuKATB"*B__Z]4(PAeC&_8Ks)fGc%B!JQ%hhjz*-sYF,%zjiS}S?4QVdX,c0;>Axx~[U6YEBE9r4npdojD;aw{w>
R^-fb5RC
BkFmt!`gs^sL6wL[>y?mt,jPJw<eb2Fgx`k:das?,#uqmfUzZ;<N5JA0h3nB>WC
tKt*/{j9,n_ba6Jp0ZsWy|wkGl2
#p("_a^GY?L>?T?8JIBwAU"
^w2JK&KTm`xcA{yC!!hmt9][B)TxcKob<K`VSFm"7^Jo:hj#vTnMm+W-@>=Fl?ySh#GAfM&p%CuP.l50r{Y,jY(ij<]9RAy]r/u|cKc[Z&j{x
`*cH4C@5ay,va+Wm-4y1-uOW=)r^Bv8fqMyD+v!riE6/]Tr*`sE4!h;v^AbPl;_!ZW/
ISblYKT?WLRe_KHOd&Jg,Ca:)FVs+77-M^y!P[`ExM%@Q&KaJs!!q^K""VsV?gQwcHuhaYP}Bn
%D__Uc9VHc[IC.4m3p&oyUua1S%#C>`sPW7:.bm%Io$c
J::a!(#_3?38#VtJ#hx[W[(?S@H
N[p=E?`KYkO
FVsf]57:a(MGO?B^>J8#L
sI0+E{(+cB9D"@q5/$IdvK#.T}M
L^y-vFd4lhBFvs,Q8.ZlDCwycdap,2xj#EWi>xKN6gYcJ=)T]i6O<Y5uMMPSA*@R`w7C]<TXT0>)cakRJO3JvbE/YTdlsa
#:
0`D~NoKf5(oG1)$Zg24#)*uJ`}H)Y$/;=:)
1@K|XXa53j/Tt#7*-.&*dOIq1,R$xTW_ynajg`SZ%]N/X=czU&UFGlL{;SJxfVJ3),v=_u7G[JU15uR6jem,#)3rG_@`j_,JFuiIkfLdi^0FR^aKB0KkFGS<xF&*],JQt&[
aOT&2OIle&nO5s0g1.1FUXg]biIz>a2HLotL0wIO489pIS^8m@n:@3v88w0Yjno++Y*#`3$5%O;/;C-/Q3&_PR_o$]3QYwCi34KRdE5=>lm6!n`0GR7gbkoFR!t{Q4]`K4x4c`6Os_,ZXbia;6ws%0%VpY*R
hJ]hP!ZD+D.f%&I<?r2>H;agpf2OhxX9wSj%Q!B%_b]51VC!2+{`Ey-Zu!rcD]"m{hrbg*Z1&R}X<v7)[`L7Ei(2gvXt-)v;|>{8UEPmBCv?-
=:eH);"@3qZp+F|85&Oi,].4)V%DITaIQU~:%Gc+[G&9m@l]rYI,&WppA^nh3sdOGyAMA]mc{GscP^X[X.~?nj?l<r.8m^s5o)p@N-Eo+
ImmC9eZXSU?3QYU1~&=uIF8V0`^CXrrhJSW.97,QGQz084{*W3l`T:2jN/6CC"n0gYHYP`R(ug);z0d:a+XVw!6w>e/3.-D(&IPnaREN3F<xd_lN]?umrKe+431U6!ETEUHS(vTr6n}a
R%!;H:iVk]#,O$D"q45Zk^m;]41B7kLry*I}"rO(:i/gElV]i?F
3me;E)`2E!L2LJlvadAIvtnxH?X"bRBmPP7_r#:0+$(gJg]rCG
[r%H9kswzl_GraXsVx/HSM~EE]LDWJ^jxe>yKs{G~q6oy(
iSeZvX#aS(*hCloT(R&:5nDE&BtH9[oij*6{eH&aR8%h.W
,Cs!&UsAjc$<O5M#1@Al6fmr=tkN+s
]^hjG!B/lF*CWI9Lu!yTG]he_hbOantcV!sdt7vD0Q[u4`NLiSOy"S`zo(d$p
/vC[9}e-A,m^#n>2_t^nG7@N0OitwY4h]0s.7
$GvKb"^,veiG&1"K(/f6BvTSc>+;Jn)snn$@,*`9jO37<b<3_-`=-mKppowDooDvVQ4G4.::ohNx/ZFxwwcTpn;%2QpwxFuTS?fUrxqDW=0{ydB;f-b=snw8#~K~rn9tvF"8Cc9AaZ?$U};7EPX6ZSIu)
S|)z7KU(Gcon-42EfPh+K{@a`+q_kvX`ckL+FJKk^]R8@`].qzZ5>|$t(Y(dM|Dp$<7Tq3+Eb0g3Ok^pE++&^2cV;i>~3uuwYIm!&3u@Cp*%avab4`7]9j+lN9DlG$=c0`L)Q&8nX5f+=f.RQ:2eg/.MvbW
=2i"1yQ#l/R?[gv(3Ld
@NAl0KLItz<l6}]Uu"9NOsgHEz.|]qXz(sB|Ql_^2c^mk=[46~x-7seOO4Li^So()7xcJuB@V{FzAZ])C@C/1zQTfG<UCFf3D$)=wR:8<iAvsFd2L?9Ny(f,9*#(K0nILw+6
nMXZHU2AaKsEVJ|vU:3/G-Bn)4hlmK*c3b7sox`HuC1S7&-=jrTf{LH/o;_nl4;g[gmNAtQSqdz8J5;w2Oux1&^eZ!t(0.5xb0>B#vC-<C2dy^~qnatq%kJ
mc{L*)M6?=EHFf*FISameQQU7ODa#8+IiNw*Se=yD^vR3vNkS6=LC#wyNf_xsw?P2fDK[j?S!g4NqGIV~Agi&0A5d6}h*PO29lJS{!Z?~`&KoyfdY2oAr=$sS<q:<l0PD4h>9ac-Z>CVbR>=NPV8|%7-Q55<hXsF$nfC?jo3QJ/;O;`>8
m>5
.Ry-?-en4t&9_R)C4bny"M-;)<_.#
O6_EfyO2KG&I1bjuUXH,hY]DwM6P)Cp@{2
:nL-;k`)5Tl6]1(|V$-6s)ny(.po$`Tw=p+nGtagd6_=d#%6vy,:qni.@#SZ:I:oR
osHFF%F@Q=E
_&AID7[tXRi8.OM[%De,g
7^iO*._}Qsb3mEx[Z#TJL<Sz(>pj/u_333:[tV.p$AMu3;6}M{[JE>-4XlUJe^W3HDQ_5Fcy>:lOiqZYnvpZAII@4@-8[Na#hKv7e#aq[9T@aufHT!H44#R&ySp+69(|PBLV*?;"!ZXgnDxya8@SJqf?)YiRFJ]L+5Z}X&7
v{[>#fw_k?S;y,cJgxs24jbUL)/:5:Mo3s1{s%BJ;gY0k^7^`$cYu``#?ZWGmHn9Lro+M{Ddx}Yq?`/<*5?^ozw#qJWtV+s]IPR,c]C,,z9^`0Oq^<rCsiNXTGJ0kTSyT}>59(ntmRvKqyCYWSQI]&cS603,qmKbQ^Wtr`_l5Zh&sjO+txNP.+UYOc2mi5A)A!xj/t?[N5q`p1<QeQE,01JU+#]e1IoKL=sc6m%]FaBUfMFIA
;Is"l[P;Vyj6
:xa!XRe@-;s>P9^lih]HH%Er/%ov6vKc`oGDRoa5kL_N%iAM0O`JnH~(scvxb7j`5g,dG`@S:TBR$x_HKg{8QNh.*G`Fu7}PmpjKN4ta.<fP0SD
m32w]22Ih6?")QxnN)VHW%P32u:s
q(;3Kybp?Ls[BjEAFgNmZEq)HAaVJ1`O&)/qptt*f;Cz.@^son&.5>E[lwxq$lkQyuvL1d[@Mxh8TL/L&VKllZnz"{c|]0RWY~rs
]#dJb9AG<q.BcF*[#e{c(MEwLlf^n^FuAp=xly^"J;pXIS?*H3os:v]5kcL8[Uw9#p9J>"4Qi+r):S(JKp>;bD
dt7|%zaj$W2{WdA4gEu.ror1k3`$a.D5)%I5){i1tuA%GUU;ToH
]=90fMMVoF0!LGl
s`__R`0LsOybcdLVpLY~&3ULgp9f<h+2FN1N,548bqvc@x"x6a8|40Se$C*s]"+hdn0PodBJJx!se2XX>_`kF)i<4_@gf.w!R[F(QDPm^99CUleIP^:"3_Gki1::OKw}
=Q5FkA{?chJLS#^=hdHb3ylX:cOK5N!lER
p>`t>>@2kA[#urV8Rl8-l+q#R%#
a69dep&}wmngcMmqreR0P7B!36bE.9c(3-qjLkR|@&E^maJ8b]
vUVE$2dU}]%3cX+l499@>q!e5G}g%>ZUsfhI6b0wV&LE}9"6tG^gSO7$<e[w75pM3J*[3hc1Vmu.>`G,9cAecF$gQuT;*/ZM)=tGfs~?[)-9>iJf`c*Y8dw4ka2Aua#hoQ$k*,O)=F20dOq^$FW<TIAH0-iG)?1N.MSpqN(*zA[`)WyW8J?annquCaz0vx_kO2]%f0k_BqerWF3L08;1k7Q,!)N)B>wh0_62t5:F43_!s@BZ.<xG~YDqgPX&vB;Q,*
0^"30i<`=yB{6{0q#W6}ob"uGeZ@58cca+^rw5h8<WU9A"FS<t]5RzP^`Km~QbCzCv6:bI7MrNbZ*}"=yFr>ck#?po)BM20a5<HkR#`R^etFx;J2XLT2VvZCy];2S
XubhIN5NX<SoL.bI!^!0Y.yNjpsjNkp[R5T|U.77!u.n={SaT`gD_0swsdK::qQdydRpLzhSR4j^Tp@!1F&OPSt=Vvac>Y/VA6hCLVUotn`SlaqH8{T~v4)8!/X/*mpcql*(leBRn4m74f%&kw:;;(.:IJf)TGZI*;M#a%T"Ci_^npWt1H!PQkYYNQ^zo&XC]`EI?gqO3rpY
*ds/VH%3c=(3gvL5[p%a:boKdnkLE>hs-Qlyut.D5co+SWlLOK)7hd!sZ^ABvQwaOagG?2Hq,ZzY{EQh)8-YPlQe&Ob+mpzdw=9,"#cx(n6+W,]
eoB=}"Wqtcp:@2V[kkpw]&D9:v3KU5!iPE{[PVdU*g.L+HyX_2I=30<7*r^FUC{*yml2$mG%Fdt(CXIHojO`ZT_<3TG(KBF43:z"}4<9]S^0sFb%9gJ?Jn,8@?uHk)n-7iNMW2H
Ns{fWr=K~;rgGq;AAt8c_8>-_;m@{4I1F3b*gh"U~H9HCJk[,b:0K%5Yx,mkD%P4*nJ#l<x[$1J$LTI.I??+/<3QxMak|uI(<b+E)XI&k`*[2cmg*xCWNZ"B8IaKrAcWfg^y`
rnL_}K*E9a/0OEp)q:hp9-NI,+qA5+0+K`T;_
h2aJlcEIz)ErniNvHTzyAxc
hO!W@MtAolmMRMF6jy$/rO2Fq"DtCWC3hh2]V`9U7jN>(Z:M])N`;obb6sF3WQ3LAH
adQK<QCQ*X&JFTp40
],;N6"_$i@VFm1(MOc3htq-C,,QXp>]|7iG1DvTh):FVZGX4xPB1eR?@
n]2,IrlKfKBie%i>Q?:yjHJ#(v{A^d%qjO=7C.Z5}tFS2f!_8L}JVoc:KoHsMFQo-y
F-t
D~51e{2omTcPTIYygEw0Gnn,veL+bA_{?yR%<a^Hw`,.!%G&h+_7muNq;%d#Wnqb=+gfJNb49Qbcx@j*`C%Ft3OsEbFbSQ_/K))gZ1x@sD`W>*)+$LE<_eG?$Vi1yqyZYiET!y$^Y!U-2@[>3T9J2%C4DnLdkbH:N,okl}`lKocvAJ47#]5jFl&dL?5WEZv-R>L
C?j7Le
2b_Wcq&[{$,LNd_.:b4[oNo=0qL7.dg;Nm]kZPJ7D?lqj7N8`*qcDf3w*H]qWv?ucI)&9=`$Es/A~vNG7ys&s"1%
vmx]w:M}p0n@jin-let!u:DakjOG7~L@-37>yb6NMpkkn;4(q2.#nA7tAVq`ycHof%Go8#Pj%!Gq2FdkBNRo,mY
L:n;W5EE@`rpt=c/mJ]oj<bp:ii"h5boK&t]?3>O)&K,FzW`VaY(7|-/LiLRso5xgeTuGWfU7
oq
k3N>R]mG{=_url7fhu$3hMz7)07_
c"mbpCMERRLI]8/xJnrmmndKhQk(;kt5k0rN8nxd8-H..Kve/9H:ba)QJ3
~A;RLZMZ"m]?JU$#tt<xJsxxiR/KO#M_K#^-pCxM"?I0fhC
JjHD*<HKiar%fbXE-/Jp`Q~c=Oevf)M;oGmAcfp)}=Bw62,b@TfX??T.
>aEhOxpR5(ny>DTR32[x8Qy#jg4?E;Z#j9r(GJj9s{ILS>_q)FL-X>nQO=0jbJT:.Dt5ZFLzdds6gykZ95cS59YTL_b
xdyt]N$_
iffpQ?uB215t=;I.aLOgEk>8e>ra!DT%}!^]/H=i1gSXvEuGkDox)IX?Qca%5
l:dlU!9n2%9ecT_&BZg!l^d%Tkgc58RXqpFlmm.U&LX/*hPK@N|h.nc-UZXq[j<sJb
y<9>$-uYc}Zdg;$$aGMNYzRTjhGBvdj#
e:PqSW@cL^Gjo=MjonXb>M5jdo!UePAb.hHqD9nZ(Yofj#0RQ8A,*I
cuX[_Y=5GPa6JWpkA>[Wn}b=^A&thlM*a6r-(Kb[azGpmM<HmnnH,%`L<4b~4_InL$n+qB6K=.f|QfWOC^]G=q45(_MyA:rW!vw{rx
{er1a*Rovkf7U>Xi6@2S@KTXCnIK;?C1;DT;[6[d&I0omcl,3B(MFxerJFoxO2YXys",a^?x:ltl!ys?Kuw,_X*#HByxIy4+{Qc;<^*Aq^3,6e:XwNfrn*a-t4Rg`2//1m`ZYGEtTyj1kWE^{Vn3yL<FAp-qFXj$cAvJ2E57Q?@RqO-N?6)[|($-xp"?{j;c;G8t2M`?0(&+]d?%,u0uTt7xy.v3wb+5DwPu,!v)CT|P8
A>bQb(y(n7&-&BVs[MQsWJUAm=*QbHrpo_1YpXN5c,fat)$LSM^9>C;Cps{uP.q0L]!26Qhr`
MYH1yqh5ceF#L=?lYytN
g`]itfrxo__wWJmpkW]#5.HWd
qeCR)c1T?UUCL7C@#7KZp4
#x;M.TtaPTu#YAU8wB>M0W@`xa?,E3lfvBscVmB#hyb9M4|UC_h+h!n?78G^RZBIm3Rv|8wKMQb9~vTR.+]]6.t-lHd2!v_h#YHK2w|Ftx^nCuk6W^ax,jW#qLO$o
t3A+XqjG;QQG)P?i-H+-kj^#CntYB&Ng#Nl4lIux/a
O?E+HN^tg{/[Lw@#80t!A@g~!!%1i0uQBj]U4Va%3)MBZ~c8NnkFN-(%,A*&LF"W6kh@ANZ2BIB7z#M.3_VCwO-foQo^>^J1*-*hyhnrY^ec/3V,I=mV?&KxY/
6/BI4Dk=IAZS0b:sk^{SLGm7u#i&@wvOysT(f9l,AEqnHm9IUa/r,c];qn;roQ26xGK"e[f4z;qXMs^p{H0@Ig>
&xOv=pLj2>gGS({Rc-s.0gNS@L}SPqGt#+1d~FX(bLhs[-?a5:I(0*?9_)y4g`z;R9PLl;dxC[;=PBXmq-LJzd8U<Pv6y5}
w&]lCY|Bx@WO@d9Z8(Da*DhC.g/$JjU]&-wu7G10lTo?_0fv?9Lc>bCTreheqNVg)1{R9XcAOvS3ML{:QnKv&#DWr%u)e1.!<9cumosW"?S/^^ReQ71:B6=;3D}6K=&Z1V4dfSMx)7!qV,0JyUw(/N3_&&PkKbRPS4r&4HMrz@VwsBQ8mW#@auJi*5Em7v<a0mM4li66^?9q;=:Zv>w&_8w84]pMY*!Zre=nbDyhNbnFAIY-PeL:C^q15/;DGb^+`::QKOEbEd@vA1Cf(&z/]^JV<Xbu0EX`WOY/l*bb]KikV^h?firJBZ#Q4ZpR{x#73E|N783CeHwK1e0/?7K>5Kj_k;!:=!%F8x/qg
jvzTC>vHfyhw{*Y`h6Vn(<.$UM5OXt/r%E$TkK*h>nAyMOlu]@z%1DxH;+!F9`m#t+{IL&ISG[[%|JeI@%Kck@~e>>22h;1VCb|^KV~c,4[r,EC;{)m/|o@0.h
y60qU!R?QeDZ0HgIm;?I>xQPLYpc[c4p&|mgpc1#<u,i+F.l_{]g3Hx%du5/O23[R{$UxpMV[*,YkjfG7eclDG;sT,gWGu^ho%(_gg`}0m&>);I.>IJ11.ee*QtK+o!M.pA>04Sz?,/_vh<1>>7b41`VQqU`YrHI$Yt6O&pxI<4pR7S{E-TJWI%I?=XIiQ@~)UY5wtUW@Lcb2NI`;#Vj=#H7qgcD3+NO9|IcHdhBCiO8v+Mb(48$hQ/3RQ$Nt^a(N+g-(g$xjX0H]?9BGtq:+p-q$(/]jBHk>xHF5fp^hl.kaDVh5x<No/=kEkGN-[wnDI2[Sru`VMTaM508RFlAm/5i&F5>3-TAC9VuC
VgW2diaxW*Y%HL0J^:laXiaO+9Mt28m;bHNI/Oz%H~EXPEL7={]`1/+LT:kL0LBVB)`fe@9Q8(_k<n<(<V8;;.AkEAj];RP9-Lt9$0KX[I/42h_JNn_"<]-#V/eCX:S;Gi){d&cMDO(.he%;bKZyZ!5x<%wNVjB"[uHN"RqN$,@0N/F0n-s|IR.(]egjGK"kTD["FCLjr7/mjccXnJ%O%{U_Zz"w,@StPoSu]X8HW1J0Uv4/%s<:T?:vb"Bw<F>&UWk%o<9}<*yPfi*K[4
ePqaZoq
?4}C_2:uATi,BAO1bSY?ZM}u?]0MeKkjD<L%f,MWn-z4T$wOPK{]Q<sEzfEVx%Eg:]CD/f";I;%>jeUyca6SfwSftu-,6!xh-n/sPGOKE3g9-h>5?*@too7?3`#6_3xKtoO`vEe(KC=DZ:$/XbqA!eDj3<bI!Ov+)Aib)A9@<RLy.k{`.joIdUl[4O"$9p`t3lB[H?%Fl<7.lAQf(F%[
6E
1U[!9@~<}5_(V@;`prY=_ue+Y0J>mC>1iUVetEO:9M-@8ab3Y>`vS6JrrFkiQ*
3DGuInL,fCcsiYykgz]u6L6K^0F$8+K3<0w6
1r+4YtvlFRPa>=Vj*9j2>(>Fb[!YiH}JJ.`jOW5O[oD2v0e1+
8*LFl!g<$c5n.yVYZx1QV/=pQkbomC
l,25aA5ui2A?H2GQm>)Yj.8F6s/O5mW}>HFBj)UzM9b+TDKZbV_=.8Ey!L[m3_Qd`Wu0cUBRyUvE5)Lg+J@IM8)olt?Xc6V79kJ|F-71Jc@"qqOv)}o)rd_c8L`6)G7
E9gTJ7PCUsE/I~v8Y-Qex0(_:fM]qWDTq72/BnO_3RlpTmle8Zj6,lZey76Lp4](`;4qjPN<v!!d9Dtv_}3uo%k:mC5%qiKxl{23Lrc
vAjjEGrP+9W{
5GkufQhdw+KxX
:rbck[`c6w1>qDc
CYD,Vs-mJX83[r+T10swmkSq{/
`QFfN^uK)]%IQ<o6E+"VhO[h_*hUDmP!Y;LBW)-rQY!}dzPEf(q`:*i8(>W!SYIgTH#KAkpZ`w,Y$!&dOPxS]kXQ!D7%+drI+3#l%F<<RxigvyZ)s:n:"Ynsl6TI6<"hE{)<MZP.PBL_i1]$^?ib%w=}OW#4;K6rFt^v3G[EtkGKMw+C7<uw1vbh/4ChKq>RfbY*D`=Mk(jVU[t].Uw}Di-?7E-hI~G*ij>z"rd_H?jTfzo:EBwOQz97mSXz_Y7>@hqQcJSw9FD]G}A$xY#WBw,MI_
xk=+{tua&Tln+hr=YFFayJhLK0ms%as)q6<Vn3/S4vkW31QFBe@RL*#EUT>`w&C)$X"i~:TI[Ngx*1:^C0K;I09NVUlI&G"w]MH3e[fWvSOOnTd,X$CS3G*Z4S`n"KgPWi7r#+v1T.F
8!zo9G7+NH;RK%v>@t>X0ayRE/(B+k5:QRXNP=Km<brB<mW9I4oF&a"@>oks
lm[{QE+{QqtnR
p1DmkE/H8w%N91vvGicNe<
()Q(u#v5&jxG1c9bJTq6DQ"3bv0>Xnsf>B
$X`E+nYf[%:pnmmq*AvgfZp)U9M]Ss,ct?5gi*FsStf}AWEI`G+[cq^o`>PG"=;(9Lkz5+.:VdY%s&;b*.Zu8c^rI(<<s+<U(Ahr0(;t54_iyOLO&QB#pO)[,YfAsLg&v_t>WxXvGa=&gRW*2:bU<5ud&8y3*}?J.5y2SoxJX&8&fsr}cm`r6K3&/$RFaBPQ4k?%WC0yCdy
_a:siRbn"Gm6gzLw4{^2BB7NZb[N26-sVi8om!W`d@/17dE7-K#^-vIEXI)<`+YE<DF83Tpuz%E85|?+f$N-2Fc<ZT9v)[kmUyt@A5u*(n3X`yM<%S?.s^9nPsQ</V6!,frlnkZNu/h,E78ma@:O@CHHH6?AiFYTU,4!)HDoxEBUl|.;N.:i)d9KiV[1%Ag9cM>8q}l+1Ae^J>KcCX/|R^+&8=U5,jDio:RQg^`/6CV<)0[ND7j;ywJ|

geA2_7b<!2!R:M@Nc]S2WF$5*UNSS|3m
f(G[|(]L4Z(/lt.SdY9ZPr0.9r##sYV&I<RsAwQH(KF2zj*,FYf0_?)I&pVoh&1-E3%,J0Sed6Dd96ZaUsv:jHtuVO^(Zh~6n-~4e8Ge1vgs%!XOI&9Xw`.F!V1)6VS4HEN)F8r_iMpf^:#!lIP,yq5lbWo:n)VwP=ri""asO$!UM4"#Be([hr)QNs{^r$6ORR5Dgq/DXTF=9@4oYAn_D-|it,)"%p=#CDKdaFZ*lQ>Z,sbkfrm$]LFh,Wo-~u4QOMj%>$Gl"(Q6pB@Q5cQ,(sY^MIh,|t?p:s|/L&M5-/:$g--VqNi$zr)v#aDORhI^4P_yiFcv(?mur
rWmUPOjxo7aP]+K*A/
VNok9aSq/!qlKNfLcNKtEBl"#N+BpeHYy];zyTZ>Vv%t6CJ%wAUP2Z99"~+K
_u`;2c.ARMdR_E(wj*,aBM1eP]VswbOPl;#_;"jE8:lF>Wa0;3feNwq!p+4$B7q"xq)Yf&MZ];>(6!t*UQ*5^4vL`U)?4"q&{p8L"0>Ibm[I_Bt7vcM[8=+MT[|NqWl>ulC@716H:G9?<qM5gj}[U<k[v:5E0AB0obTEOqphuWPJSop:(fGs3`M:ib[NFJX!r*2]S]sJH<I2Zq>GOwD6%ALtm=2LSfk3apyj~<6b
*}PUY-@36dF|(%JOC{8>Md3K[g#>]P_Q1+hvrXmJx^42_~hbs$4i])0r,$sZb;],J9J}RCB&KmxbLM[t?9jp=Hp9.C*gm"*:l3$L^=E`WYMBV1J}%3IOmw;$sKf7lu`NiPmTcb2!Z_
M,?2U:PO_o#PCtcnj^Hff!1a}reqa1#,pZPJGq=2{a!dQNFt~wK5/gStZQaL3m>b
q#35dd-iiE+{qn7IC#Lqqlxo[gi1c9,b<m!9S:.fo:^pPFfT9mS9SSm>Dk]M/8P*aP"}>xsCR>7A,N^yIRRhdr*vE>tV*P6l*aqmNB/&a4hBXjh6FGGpe{UjmKw_JeAC`@tO/.:2#d@Y`gvx[)Gvvf0En$t90YP5m)x-E9B|^B0W@HJoV]yC!q7F%(HD6f>=7.615OO2d!XIeFif6T:EF!KX6>w$u0P:.++l+1IU;/wY=ru;$MonV_1Q#}h3v42_YX5hCilI*x/3pAWYsjl`iq+VQ[6zvKL@x4wXtJGijbg8D~9^tdwVoY>4_(=WfY#UtI4,C3Rq>/w@5hj"K{sKu9+22n5TswCRR]M3N"!O#`^%=4u{]d&w!Od/p$i"hhP{@<L>m}8XDP#%:6rGWd3rAP1
J259r#!?;!^%t)=
dLwvhXo)i]_I.Ff_j8"ms881aG^n.m4h[@9h%M:$#:jC<F1DvBR3X%c--dvy@NhQVDib_q?GHKCR"FOnO?>!+SG3Ku:Hc]g+OLt6Dx[hc&[A3hWb]8/y7yv
GoZBYxp1o0Ei(.r{c-8kO?yWo}oo9cy3_i.-#
1-)94Q%~_GWa!pK+5n6ewh%9^.0)m6kaHm.q4yK]2
*vi@%-=rqj4z/|yOKa5$#aDq:;INCsfuRM`qFS0=Cf/Nv~NJ-
;A;lpKv`Y#M6p}R;Ooyh7w,PU}DoJ+Knv^NDVjRWy*]S60G0;~&k=AZ2??g9LjpdLKW
`RPz#u^(:Ronuy7U^|,f;a@d=E/25>gX7,AR%p2n]?i*YGU^_%(s$l0@+QDM^D"tVfbJUg/d%AH6u"9I//mUIO4:;}1(Syo_b+5BTs4lb#2je7r0A&*MTAa(35]_>W$^nzUD]EHSv!U&#=$RlbQ4pVk1xBYTuz6ioA,;kw/PVs7.j]l{fq@/4X2$dDt>j#v
O1%l7
2=@W%YR80Q%-`~SJF-e]:#:s@+`&tQG>bs[PRJO[nheKia?@Qb&},
>9hB<}q$0tIBanT$S#D.cfx+[SAs4t@"h]2
sH_qq024^V]ZrgU$vhGwh
V35)fgQC1,q}Qr91+Je%lflTUZQs<ZhA$y>zD]uTT~<aVw=_AENw+<.V*9p}.3i@,ExC[;hL,V
v@|0te9.XH89evtdS=mm62dU
mlErgw
tr.JjU2Ho``*~yJVF)J4fL9"!utT3F+Q
pt)O7%w2b_VJsyFtg|=Cq@RGv+&K`*kK&]%cf|S]"st$i;&yhzim!JUiJ:fDv;/2R3_2OPP8;
lmb8OF2#.._:EED9<:V7>}<[7
H~hG?x,.`@DlGJg`9PN:aiA4xq/a0#H1u:cLs;.ey=<.6PVPiWPMaos-?u^j`?rq!jl/iNRk:](@H,CdBnU(o3d=cGsYLnM)#JY$$N]P"USWW]x}*tbeGn!dSkM[)qGtO*(m4i:iYMA$jYU4D7>
ar[}O+5jO8/d"~V{8Y!N"=FtJlnOAsN@_]:P?!GE;W#r#bm~-^W4dmR&&@(y(ZeLxhGk^%FDGl!H*CY6jExRy0X%S-N_)m<5jW9t1H7JOMb?8?<(!S2ydRWyvSKUW7=SB"
S`N^0exbXRb_)@9cdUw2;MJe8"1r?)Ep"OM%4YYD&xZ&SQ
WC<kx;!R:(b?SoA-HLAuXqex0idM-K-ypz4t76k(4W;m*c+{enR~!l;~4mXktiXu&m;|E7=xt<@2wke^@W[R*c0%_@@SkiADYBOwkYe)l~_.>L]}Z[U/M^pS*s)PbbH/$GCOr(!COrF7!3jS>20y@<1lmDYh8~8gMx%o5$k&f#IM$^c|U;M{/*baaM)#e#%&S/^A%7#Rjt&xTW2Swz=mh08~M&+A=(P1*/i9cv9[5:@u`?`G-!ge=xoU&5^:&-K"9iG@SJ"z`a!Zm&s26DdI%MUAIBZCtJ>MB}t?LeAeRw4ev/FVLwmFqifKT@sz
lsJ/"a+
?C~52]tqN=eMd6aDNESG:ZQFHl9j=+<VyvAW*Q+>;)p*Lq{!=[CCbQ*bKr*AJyg#4<7`_5_bqh|D
:~+BTGoYXLp
xP#Xx|,u,m3vuRPrIc#"O5&kO]2F/"#-j6wYpzY5ZxXw?;:Ni;CW#U_{=#e(/3%_#OZ~65Aq6?Gsieu^-C.21I;O>D#}lM31du@GZTn1al,/)t3#5{5`!kk#D&".H,C<s)9t[7#85_lpVP)j%X<t0"eRaX[CfyN<b8I6iLGuCQ=nlI<tlCT_R.v]:-Qc6j]:]EZs%<9GdGUSGH#P@X]VvnV&hfZa?-bVx,v]Vu%R5Ses.a1p><f~wg#|,>7_h!,,]x0k=21fmjrjuq]Z9quSSVTvL6itPxk|w/!CR~&&n{X~;twQUsLvGM=WY-Lp7{yU4V7wUQahG!CA.T?]G`3XqqH#U*UK_`?[ofJ$,3Us]sO3c+i1Ii)$P$q;si^"`?97,j`#)qau&8wB!>TRvW
u&DvOIUeRV&DRkJH3ww$v.0YK[o*bJ
HHF^>McA;lN9k%%zBVCa^.
%T4htx5IALNZ*?D14Zt)T.H<w,n(x5<^f4oc!%!Nj^^uIC|l4Idg!SvehI=5Qa!f9D1Z!$>Hw[qM@d"Ypg8pO=fby=hdr%lNtfx?+rLRfhH%JQ.Y>[!7+N`cR+~6}Va!_lte(2V0S]1%6ixfyIYu(TJ>802Kv=I+5X9KiE_M&P5X4!.)|cgd.D@b7<Ln50~,bk/X8`8MX4R7Q/P*zP<$GrV=`pGK^nI
c@Fv=#bpVNX&O9B
i/<NZ+eG{0mCa_THe>k+,i|0xvil?BGPyLz4=lIo.u6Sei{6!jShA"6&%sAUc)U$[Vv/cE6-2;`P!@@bA7>F*ctfODuMm+h$`r|vR&rhFp!upXF?=X/V)_S1yK<,xhfq!F9w.0W.)*XqCF?viU&l,/rN|V7]AIJXI<;;Rg7XiJLU;gV`/kspt3I>%@r1EjDd"f;EOod&Ej/&bYomKIg=1xhHjQ5L%vN6
OO1K
g#FsJgZP9st$?v22?4qiD?2yfZS6f^<K/s]3*tMCFrnHTfRtGBQfn$JWI4?#i
~8,h"Ej-6]oyOI,
B&V7KH<?W]`j1huv8G+K}]#vP%(vt6NE%#%NaoI4_Mk-nq65Rv[W2_L0F2[)UBfH8F)8Vb*aAr-i]y&BB-PDH<+y"0%7][8f=vcOOGSgdT7lC$OR)y)
,({V/93_<B!ky$6rYI&fE=/ti!,cpu0"6gC[@=ob-NC^P@`QkBal
]Yx}PpD8k(:syoy38;R}C9(sQJAo5qyrz)U[DiuVW-db+yPiE2]J;Y13yyMYYXLE>FoHvf+-Q)C*k?Wow~UD?/C+Is`q#lbd=mS1IYs=XfZwrV1-Rua<^&RuPr7Xu=LRT$Yr_J.d5F2UL1wmov/L1(sE`]"23+<IGiT|tWsTB2s2gYX6L!=g]=:=[7^VEX,B&OEk=lK7"`3Ujf[P/<FXu-a#A};@(AD#R<ykLMs03W+<FG%nvgHgikO{gx!8.hII>..XW9V(%zptDllIwt2GEQcmPx.[a-iXi.n$n
IU]MtjPeRmp.d
#)xaEL[Q&nB0G#!w6+a#=eQT97d])SuD.<O<E!pey#$k>4CM&)?NLrZO]h:0/R5L.%nr8.f=lMCPHW
!HYf%$f/M<^l;_$C|Oz-y7NX<>%pMy[%hiJUi1>
]F<EaUiT}@4bLCtj.LY,fpaB
9`Z>U14z&nM;PQb_thaN27M;&K1ZUNQE>t$3@7d-vhL@V|GO8J_p-6yDA[UQ&eRYBX5)Lh9-.G``n@,bP[yYu
2c&vm&)oAAcKCLg6P
)r#s/KOXRAeLgN#HqX$?2ZL5o}CDRD4fXTMDgsc9@i*UXSc=<yPuR
d[bTgc/qh
LB9eQsm$#e5&ec"Ol:)9gX<v+~dTgn%Mm!`mw<MO:oR:H
)[8DuDKTeMCQ9jE/oT;a9_ir4g#8+8FHoU`|(bI
BHV!QfS51]D:dWC&TXAd2=0_=gf1[#k$
rvpCGkSaJsVSD0tSf=dl?+]_@0"L3pvB>:JU1oo3mb:o"ti
xSl78aEo}.V&o0:83ltjX(<m<f?-wP.<5JlVyS6Curu((I@qpOe$bFb(T.3-vekl`C*v*Go&*2yyv/I>EU<934"Q-gYr^0uGfWBh-S/?b/Zbgi!JJ?@T)2T5&1"Z"&rOO<.D<%;NM].F~6qD_Z]6<c|clN%c2MDhhBf8i*U<9q?[=/S*Sj^fPKPXVg=;!iilNr02`-=av2EQ)yoWcNoM)R.U#qk%c6K[25kt4dfg|7lf4d@]$Q6UuD,sId>)0ah+|mwNl5s[O+5;]^N*}Pu:&uIYkp0bM%i*<Pxb(4Zx)Wgs<h6[lE[he^`HYXzFq1ee()3CLSw;.E}uU#+vA37snFBq0##Z"vXV2s<368AMx7"*z"
[@=mMT^dA~U7AqO<gf%rAd8ZsxW*(p1V%n;@Q{1`
=3wnLp~hO/HB^9jwm!(YhaTe@XDkDCxO0-$.K!i<Dg(X1hYCODN0-tl!o(pJ4Z[3q1qI^^%+,A&*{5{k@/6/2)QKJI"1Q!hh;x!E&f#;9M7U/E)[5G-(n5h*SreH{!Cn)trU1F&"0LoZ-3(T)UM]:Q{
n$YD704_G7l,@fu.!v3lsp*^IF3P
qDaT)|dSHy!sCklf?;0Tmln)pT1=e)2n^),`))j4pTN)^}.flrY
[DQ-B?k$UWe9*!;T^"f|$HKinW?+qJ45F=O"!<VfWR%rt.h0&{+,OYIN9o&@&.=8DRDs8Jx2O7$x3PUGl%C|1N7=QV&ceS?AB>v4Dq?VXc6vJdw`%!^[>#dNs7U6-pQ;#m_qm(Kuc{=[4[@/-/P7xJGwA:35U.7m(R2$D5bE5KiC#;.C3g=Be.g
D4<W5o3/*PUV<kDloi=4+yBW
p:K0^9>6)!z;}rNV)O%V"gAa$nx-UpSV":Wj>9P
p#7-vaP3D<>.MiCtj`+O,:wbJ^ml.mKYzO{MhOct^qhd;5Ihag<RVVN+tGo?;v0.]QY2#NF9"3z
E0F4p+dhP?mZraZl6p(?b
&2v?!*+KfXpkh%LlNo>I"8/<{)E?qa{A@f0:,I]?:GK1_,N*WNwZ)em)EE`7bDzW_@(%V1Jo{+8!8>fQYU`T:9Z80[]2(#rW&pM4-#1<(%f?$DrY,*Z*=,1f/YurWpd$yH_v5<sNKv$/1p*h78W%oRN;e;]R>Emf(c%m0P9X_D2COY=;7YYfuZ]B$6`^hIrR25YP-,*Kf_=_kVvP83IeGl00`BJ7.>;lI(VtX9(eSo@@MCeYVyjQq5#Q/5Vf-qu7.p/7hEMF,RmblE`Q7<UC(Z0*IGulctp;j@g"C$WZZ*oKY
*x>g!E6&e.D-$
(TuO:oPRkRBjd3!.]t|oHtVWM$~=mb5:*c$J7q;ka+L))tiPF0RR6R9@q
pfa3Mpx.O%.bE(^Sf)fQ~*^Cso_)+<Pu:g;;Fpt8]kE*i`n%uGJT++6"dhcCK;Aku)R;MVG7yf^=4NQ!_Xippkth8Tb*E55Lr_}K0K==`o{cY=1Ak8wQB))BeHt8rp?<(.FK#.vGd48!vCXVUDG@Un%1,3-]kTLjP-)2[ABU0.3Xzb`QU99yv%@k+fgSmiTpA"<YKf^+B&<>RQufpN:
sr^wVYkQ=)dFW!0O{O[3`
BrXGx16U_J[+k)dA~TE%GvO&Ff<$Q]|X4u)01ob[8rGiU0rHctjn2Ed$v_WUHT2p"5asE4Ly)Q:3zAY`fa#.@RejI&@_T/7l5T=L"T`-QgMmnkVyiAUeL.s,6eS%Xxn()^_-yQvdHCJ5R+AL-@m7V&Ov`H`?)5tY,I$#=M&^pR>BI#mj8-HPpj8V~26j(:BUK(1hO%cu#pg7g;".ib#
Vxt:c2m<<fQx!c30c$xLXhdZk6)/cemQ[ocftgp>xRmFn-%;kZN2*BOo=q$so)W(tS#Bl!B
N^=L0^6_Vb>W<M!So_aOb&B%n<:bNsMli
HJyQOh%h&y|aBb_4rU2
!tqQf4iG6q5oZY.<4@mfx/+,tg?4B5%74Hva4RTYh
hT08H4+xEig6jPWH`X#qa(B)0>69dUX5aW#Kvpij_+29##m@/ksfR$Mox1%V2SphR9#(OcyF3F:,?
I;q"S8&a:QTDUPRvWH6B#6By[)0Fuw3ZmQrn7A[S{NLHn9b=.rjkPU|C1uPe{1QdB<N"5e<=gi.r#2)N7bZ.q)]^x6N<#Exhw`E
#+cq0<3aOOL
a=AHHmi:
kdeWS6,%QmFBO7r
#`7n$JD)2s;=/!x(>[:-9{
:
T`,Z[BcdJ@hSp/iK>[8R"g:LVPDLOUdanmW^!5$DIQ~y!y6-Uh`Q3ptZwwVg:Fp73$4U}:xMI+
en[+W)RN/t:*eg[~+9.^Oc"
]gA-hSUxKS0V"ndt&s-h^lFq-f<L<ZPVob&0cdVgSP1yY3`GJZDrK+SdE+y&%7fZb[LE$<..HMkX!`1|f6h0YBKE&onLtoSN<=V.5)ZYsh_0L[q{<=vp1vlmY$0!q;/MC{LrJaajT3*SI9:uT;t=;0wM($ZowF^s%jHau=4ADsCv_D%Y:WC;k,U$1PH3m~mb6tlP*<AC=&fd;naY%R:?[1W9^VoEZnM$<t<ceri|teEbDX`HE^3a1bV[AH#!Lak+r-0]U]peq|o-L9W:=zIOt{Geu#hZK>GrEpSF:r7dFY##+c_d)q-o+`S_idE"hya*iW%1yc8q.dOO,|W)"O%#<6W/I"VGuf^+7-9N2T9i[/[n&{Hh<^9XQrMfP{(o,K4)hYWR:pCiNXsk2!,p.x]W2)&03uEb+RQb3f)l[op_pA`#+wOl+3Ext.Yj]Uj>Uw00m_N-G1w
DdSGV8s0u-isW:8-2TJBvq?8xjC?*te/Z<@~=7>|Tbo[uQ-UfLLsf9(r&/s-Veer`IJ0Z{li!!T!T7D>(6w4pEjBc&<B+b("9ie{=9aGT8LnuZ+AgCx0l((ANa@@e<391]1`9Q9:0(%JB.?+2E
54
R|fO
6*v)ObHl%/xdS>|Q?1x
/d.I?&6/NauU{cP)0F-"&x?i`O5SH$,8qOCJkj[yp/L%}7ScQd"TL_La+1@h=t:O-l-X=jMo@q0[fvse=)5e.NYO7C)eQIX$N<(85J6q9jh,qpq*3gRw[Bu6`PuD#B-KddKJw$rAy8(J&=NNlNajH9?N90&)HgZ$6Fl]F2iavME=KRO_DCvVIw}Uo>7<EK(+h
n$3pT:n(pg9VGcGKc.I(xa.Gz#=lcVk/>QPG{mKhzlLq<EtX6/h,5jE5=.#)ceWYOEhqluX>DWmqzlm5{tsQsV&YGr7G9b?#zF>xRRM!oA#-AV&Fa<l>7)0aMrp/IP6pwVg_^Via~<=kRLNsKBQNre3O|jm<x9yi/vZD$DdeX3Y6R=K:#hFjFPT+-W0-4SzM]dHaRq!Nr!S<NuL+[N:5m[0%`cLo`8
WiQi"x+tvdFV6<<tQwwX-KK<TDx|o^U+cAxTq~Kcsbi:UJSY8hc`[L#CKU_yz(%A=LBb0yUE(H)pwyY.#IEC4|0j17w+_X)@93]WpZ#Fe,Y?-^FU*RB9?nIP0:o{,qK):/!KszDDG5bM[!^Y0TH>qnyfJPc1l_]@p(Temv4++X2gI.$tryDEW8u%Zx/;Cw=#k1PO5
kzgA4Qdba<I
_s*s>zo9Oe
JrL4CF$iA2VZlZX&EJcv2rKo}vPM-4^or
.5
g6&r].FX(lZ@;ug9TX=G%Xmjp1v4wo8npBeb%0T[g.+^FXQ5];RJrMP-o|0fi*q+I
:/Qh]@U&eq?/9H?m6#XU8Z*rv66/OXB2Ze<4/DGU6eOudp+iyt9R;8"C%YqNhYKsOqrnBMJ-G<Q?mYYh1atbquS8G/a1bdq`+J^Y@>EiL7]=K+je<l.pu$G
Gb5;pv7]+GIg>,*<%2b1;M%Ii!0#vmpuyu.8^RF3x
R=t?a^32lt/+mGDwC(iU.4(?aensafZkksUTo.,b(
7&jp&_M|DDpYKnQ^qH6Pm$_!F,LBObM0#:7K?uQeMIuLGGgZ@4TJ>^QHe7Bt[a&TKs*9uqoa0c-]Heiv*5Y7QQ1QD;1yWc4(`f6D>4)N@!g3cUJBTPO]658w@if*$A`[gn&;p;AnSxWCt~Ed+KQ"(BlXk!cqNMU}Q8RAiU^.
&
^5,Zmk";+4*ayIMoM*M+B>aLA
lV+Ok;GU>.Ec5ZBadqKH[tqw<-$nMo5^[YW8597MU@B4mB2n]L!.m)&M<@i6WM.T~V+qqfu03RA-vGLaC0FkRPvuSk!Qt3nIiP^x8XQ+nEk>(g
XKV_Col<2v=!Xh"*AU)k.w.q"D+a_1@ereZ@]W>S0BY7Jbk!pDyE_|
dZF$Nae4:*F`3
1vJpE5aBm@rx@54#;NkL;
I%tS"#$
i%GQV#Wl*jXC+AA^n&3+N:Y9[G@MuhMToVQiH944WO2D_"MGqe%Ut,EcG5;U]]T^M9x![[C#YRn;;g?@,^3t}ol0E,<eT.+<Jvp7ZL^`~Si<w&{T&^Z`ta1@?U_I`*6t,L]pYHM?eW)b%kRAtpv$*auk?Cc)!Uf
l:e+lSoJfSqhGCZ(irR=E3![6I|Eb,H&&1JX?1`0!*#N2OVJGCl*?ULghLMd
1C5]XqRC5iUlbFq2f~*o3UX;wJJ<@hmyL>I/*drA<IirS@x:TL=sT#DNO_TQB^23aLwgci4lbh8a-E.M<n?5^cOxsjab(aB(AC"Dsf8HML`cu
pAZum%_2EihzrJ!7CCHEL6T^b.&m2$+`Z/$
y~9znYWA7|0q9lXaTpEW!NB,Y{EbZb7ExAG
/
s>8TM=v/n])qXCn6W{LR/TH>]]$]O5;quiB_kR!1-:FKgJhkgN9L#>X{VpESc@w#C(c$__"t?|LQLi(omLhRK-`kqx([24o|r~8
X{)1kWd;n`xM+*B}</<c0Kk;xd!Zd/c
E|nFL.fngKGz9W:J,eTE(oxa.;F|8z`)*h21s{9A_nHv$[XnN4C%,FhmIj(3GQnh&mtJ/K3Y)l]1y|b;>>OT`?F.CzcM%>PnmIAZOn@7P^S*n:og=Y
HGE*ArBR1Jj2PmOh.Zg1<nI&0!"udv}#,Sj7&Q0B7L>=+Mf0x[5@IA$NV=A=ynH^z4_@K,dnQ0j2ip)V]ZF-H7IVr[7`O,P$EGf,X.*=*_E0x^`2pjwrOY.7C-smHtgY1VUm?PIgOtEqUqsFZj*H`t$@vBklPsxwuuc_7CFxGQ?NP>?eSpB_F:-,K1M=vXw<,n*_RA/"FM+I}3Quwuhlt#`Ri#F1`E@sBjzH}YbQa<k2Ev&^d,$=S?=/8,3lm:;`ncu7+G~$I<p6%Yv1YUkl<Lry>QTWMXq0uVtR`?ywpuEfY@gvsY1h-^D.Bf*I3<aGOOv"",3:-lSUTW=Qa/.hp8F0jc@(B7.#kJ1bO49P!A}TP
=/lD`%.EBKzFBF8#6O4
NU2o4+$]IH1?&C#HM&Q=C`SIYDG[OID3+0iP$/oB28q"FZn;+Ui]KPxCXmC+7^:d/76"8)OoU+!_N$079HmwJ.Q]|B-0W!2L&sGEnjp>C
QvzQmu|pL*`)YxO
q
iXb1*f:v8?JMzN`c
8|_s)WPF4k],B86#9Pl;4pm1`q66lZqT[U4ZA.63mo&2DUC$dNZ8(6si>D<QYB22Jh+/*X$ke~T0QXe[5AcJ/w`xskk`X:
D_)q4fDBh1
;2T)Z7aR>+H{f37{GC8ZtFy-EpX?[);*f@CM3c4y^dk-"F5>H~`Uk")x!r=xL]jn-/>7T"0<QYZ5.yuefYvZ8OD;1*MI
33HPz;hX/:)WlKgu)U0sAgB(h!4i:o-?C?y9mRg2rZWOX:j?>"3ej%-S]%3YyHXY0Zx(S^&?DiWYb<4[@:6!z/Zbj8DnShW$yYEWt"oP8i]LTGr
7.InX<ET>=Wgxu*Pp.}IVq~M%75^:uVs8Kr<`L*PUa=4ph$e)//gLH[
z^8.64^
nr?/Fho+.R{Kp+!Dt$rtgUF8$l=rV3{?u2w:{_(7JVBrN%D@Eh94M%WoYd<]{rm;DTk>Htk@FD),sSoPJXRV0ghaf(8w"wv@cr-[wcEP]q9:~-ejXC"6EeUq9ZT3F"djK
6Awq"I+Ff7:bpy?YpwCH;!qj;dIWWi]r1mofemr-j/vn{:q:#2u-^q2Z^_I9y#|`M+>LOZ^u,BS%VW?fyb7F
)_Gfx1ac*{Y9q7>2`Dj7L[!88KN{psgghBO8.{>#U_@gbn1ZQH:g
X_g![hnu8J$P##Th8,$OO]BPwJN+a5cXKH]B2&w4+puSxgoiiA(
_4g@+Sz7AfMSWWL[v/|T
:Q.9Uf;0Z"0u3b9}Vl)ga5lb?,VxU[]|h8P#pN$P7F_%Cd<5SBg2;"Ltoefz(qT?,!&SlS=>"Gi{Bx<x)sLrp`3Wo3joD2n|fOIA>ePzQllB[vu.>+f@-&6?9=BX-fJEpuQUcNJ)lQvkLDcxX3ix/SQd5e?WJdt_Z?3cpu3}31Z@EEVzh@?]A^ZHO8Y~)C"?VtS(1Ec0h+w~+LR,o;u]PcM=Pyt*4}Y-1.Ogl>p*k@Eg_LoK8JTAWwpgt[]txa96GK[d4^SV3]2LdHcVdXExf/T)/e^1=}w;c3^#p;)]r-R3aeCzK,u
U&InE>4.sij:j%;QbAvkY]!GD4kA7st~Zr7_
wR]r@nJ7KF)$l$Y@R[509*3i[(oaQo}ycdYbzxtEyah1x36Es[;X?b`"?*V4El>k&?IlCnC(mD-2/Uss(Mp8]D`E%`XlkxawFjg*+a@8lf:AuukV_%](?1jdQU^L|J.%*<~qTIOgMQ3ruYs?I*SANUA/ya+yS9#Fd6;%-;8VVog;!>A(,
|SV%grPA[=F`.Xow;,c/XDDZJv<EM[jB&HTLB#e2LJrR0#N)AvT=_C@KWv=^SrGrQxgW_Bs-k9cyi$+_xR__aPoiD;1Ml
baP(kblLX(f^{t}XT)jh~c(S#+MRV#(@+LfV!6o$RBeJJVib?1)JPSd7/uv7B.i.
vTB=T_$
f#9/EMWRY?PmuXTL<TJlx@_;5iIfiD*08|/D)w$+DZ]]VtLwYp:FYVv&<$VYd;&g)~,l@Kq
D-^t1/UEs"<y<Axc`Lylqo=574j7[&%
&Uuua>K"N>]CUF0PswIJ%PJZdC${"P3{M8lz8R6;f!8av4o=oo^T!rnM+FWLw}faYqTs,
sE<EF?VE*&5c=$k]&y(GT+:
eo^GMfD}=Bs?Ri!(,KVc4.%Dif`{HAmO7}5((M,69[[>[<]E^JI
GLYwH&7.k:5xT@)ONKA=HQ!jn`vH$2fx;V]$#>B{!_Y_Il^v6ioBO2V[_WJrlPxMsM0pe[qkI~OB]{ARkWXu+u&g%B
7B6^N_N
<9B-ZbD[8`(?#IuYN>ikkB
]`0L3)2qCt"<:uRuULarAC>"s=K{R]U:h4)>vc>+.et9&)m}%1a:g`q3_h>akT<Y@Es}hp0E#p5@cnxm87;0AO@~2NIu4Tq
EW(a"}:MvR$%n&O7N}sDpGN:CWJ/Wpj0uv^FHAwZ&y&oD]fY.v*ZI##@$R^=@PLJVA@GhvgYxa
gdLS"(uxZic0}UMJCmxp2jw]s(eX-7Mp|)}h+rY1j2r;J+,C9/ah?ZE)E87n~3[3HfhEe&0>N4.Uwo!:YEc&9",LXjBG1n<28.Ab0HpV?]Mq@dxP79HFNCr!|(9M:IbQj&%h$ASGg9H<0l!J&9ZaP??`M%}DKOenS]g+`bW!%`%gIeg%$`zM,Y"P+(?J-s>Pq8?B;ys$>E6
#9jp=nHv"Z,:#+Q-*.73
!=R">6jh"0@m`Uq=t8iV$_$64[ZA2g7iS&g"o6?WTeLX%qb*(>11YTbB+$%ONW<wH_5CFe6jduLW<?R<oBstoC3Ht5%PU?qn
>es4hbT1Ju%1p1[.|D.(-:9o~xIK}qv_cVijodIoX$;D0:Q,eV#Jvc%3nI5y`dW2Or~Qy1!*vi+$XtB5c89DL/?vq)[D:d+a>EQsXPtt}T0RFvy;*d2l@g2AW8PZ37<d3Ebo&S].]`+p!
~aUH{56&D1%VGEka~*?G>v:
QW1NJqRKEY^j>s%#ae[xm0ek"]"UN&|^)_uQG;amq9b0(GeiKE"U9;jIEo1JCon>-&6#I(/2/*XC":0jy*+sFpi`:
m7L6}upK-XH9gcHwH"YJ{
]DfU9!6Rz:d>d1HRu9.gzKbeIu*Voe7kqT<t=0tLYh1?I]ul*4{j1`D`_8[SfF%q|kqspVU0l!N_/myO%cB_krkCJxHGY?2l&;ccCv90UqGE4?f5_jE*~:EJKtt9rHgX`-D3-[aa}EKtXhNCuN(Hsc-<Z[O
F#z!/[p)"ZgfY+QRbmQ/"Xj&%uo*29LXfGU`(.;LkW_W+W?nzl^0rb(h]a?_-hCOxY>VbC0m8^VPBGc(&,e`gsNi-D@`]k_WoL$%b@k`Z/d-GC2>9(Xq`RfB(y%;@v}1#ns>60~xSQ*JQe6lbv*Ud_`fk"))FFKN:OD%OWY>1AuH+1psGhImsZ{+Uaf>+)!]TLFh%6jk.ng0>aa(
rVnkU:cH/#T^>VLsB(9ep+<whXq)t4"vOaFE(n7KghW}j8#&Y>)!On/8G>6sT<.OmaLIe@MG?}"
dz"RR[]Mb1R+;8wSUE]+A]^zyImUafd>E5w:15
T1BDH2_nD0I](j[UE1Kh3y?-k2Z?(0)EW3rF}AbZpZ"lO=kX;3x%^@Aq)]VWbJm.][LDZNuClqilAsDF7>(TXj,0A)e2$;RKaboQ:>AMQeSF[d/WW-{xyC6Jm1gnU4%JWI.yWhlWW68XI5sK4h_&3JDw8^w[j^!w.Q@S1h9//(V`Lv_ofk:7HWC93W2]D7
p+ExLDsN%+cV<9<;=5sh-4l1DY<1hbkXFdU"-~GsldDrp05*1.o.pr2(Jdl8]2BMB&x<HI,A<FM"NJedt)?,K^tJQ/rwHx[_=Vu}W9eGGyD/iJ6a].9usM)D1`#T6ZGd5Q]6^R;cUo-}$fI/1B.v@Iu_=Uv(_)L<]/vpfp3[[+(]9<0eMj5r^)ClVknEdZ2AOTq=vv4g&j%fcKVke$*S[dIgdoVjO^UvN9@g8p/9@MXo]-,GLI"&B59TCyug^7!k`;uaE
5f+Ka5w.%zP|5NG,6Mx]K-g](
_4ALw@v|oFD*2@rXD6tt8gbi2godcdL,6=jg*<Cx4R`|kZrIlt$>_0nZx7hR*wv<sc6UjrTUtm
<N$h27UpWL"fKuZc<nha~Y<AmK-GBt(f?HK7|XuFQ<yMwS$wZWzW?Dcp!H4b2?8t5WMEUX/$$gpc%1c,Eo{lu<yw1P[
0_z&x5AjTj[0xXmm0ODUs+PIeX^%4IWRC]^E}1%#*jE
g&2<))|2epz2[)=l;v*/Ujpo<sgF]>N=&4pBr>(mDd}Bqyx*vXYYwM:I?pcM9r[yW:J?vm=cQ[dZu,5#|o$jYP:[+!^Gg@)L3]-
?Uk)9`J(aHt-y!_0Yjm6ips?CaTp3JqRSSDB),jScsqY^8UDYK$l|nLH"^p`:x^qT^Qh`-aM&<~E.N`vA/$&ZnIju@1jY5$bPJtB8ga^9S??rPaum,z*P_hvo1)WD^54awy[ALJvU=SOC:*D_a#vyhB7n.(W$^ahf+,<-wrxOv^vSwQV:pGd9MivzeEn+E>WwgDwISucLxY+[Q)6Nh%TMP3R
M".LBH>I+K3<h[9mSf;TM//pX4W+CPS4@D=NNLw.1?ny*2pF5C9s6
5Vw/sfs7Aud|(SiI98gCTxs)g/43QX#!^
uL:y4:T:CY$GT_$c7I@?Lt4Teo/DKJ&XaJWWb]
JPe`*MKBJLyWVs"W?bIL1382X-ww*rMFbHnhq:C]_Kp6U5qQ`Ft(v7js$#pnT403b"@x]iklyEp)UujJ:gEIH:.d
,w;!pk
k+}?puN3JrXm9d]GTujiFot2[nRe5dE_(>](wg%s)"e0:F:wDFQ
f0W=82Yw<C$TDG&W__YlMTWNY0Oizk$X7k`hF(aWEpgw,OIla%R?&kB$Hn(y>x$DEjh&qCD>geAOClgXC.&ATxRK(Df`
:(gK[&WNPab
TIna/MvTx%yzfFrnvKq[vIE662:qsrr8l{6<)it[Fwa[l2urSC@1o]W]sTlz*!tF7.VKJMAU=Lu_Ww$e(pdL,_hs^::G>H+1tVtD`;xHreHba,kp?L-{)e+8^0:gs)dWnDYa7^w.0sEKuMQn
3i1B>!DD?7/)}B^WhZtJU=&$S,|q&b*h1vsN#_-=#p7#?3"ZHpp&Gdd,#yfD{A_CBaBA1$BC4
PoDX*V/]x0/V6vWg/HZ6K]PWWw%NDl@RN:GEgy;y4sK!&%?5h=+q{b8vo=I>85X,T0u:]R{]`Xp2XmcIO?5M
m`r7H*6D%R<X5}2>_@G21=h"CGa/Hv09aH!_bC(#!
@}*P?m_=:%$xKY@M9Mfu#w<c8ulh?C9"Ct3/QCJN09nM>8,
%wQ@kmazuCI1^j4q7"&:]8n=:TC,l8rY:I+bVAd}Nh=m+8#e!wX,Xc>y%1*lskYgE.WQ*6L`vU$xHA+}Cc2~(4nupB@})o,#qBt;2yahtnD*aBo^BdmO`<2|]8@EPAPL
@j%D=-$G?0X#S+(Hz?q+S*?6d#kqCNr?)*Q1u]))aNp&A%P:|E4p@8%p8>x+kR0Qq/>#~h&?jjJNW;z*D6&(BCFY7,c_._jPdq}s6VRS#"pA.$l>stbrNqv43fcW5w(1Rwg[}8j[gV^OoG.h.nIZ4xH-M+In==,SA^^md]ans2$[D2oFX`HHNy+kd*efI5b1k=JD|w8A5kzY^GHtIYZ!r/Md%ky+3+Ij8/
OS>BAcX~n5KdUm^
gdQokm;T#-gP$/_.#P.cQ)Dc#c>W4;S3kOuqLn(#mWtu,sx/7tXPADG&*[OkF!Qmi+9[M0LrKEf(E0MTBs#t%5b2?-K-(@hAdm)&kB7l8D6!>_Xu$Q%=jmOTSki9XK-!FomzS+C}U5eS)F3;Tphy72^sOV5;-V(sTP4EjotY[i.uq`>LJ.kv2fZ6/#$$XP3BD1epBay)CQlH$~vPen[JL&G4R"v!EU?<:*W_PQ_4OnGjjw)AGGXj?qUi4~D72>(l<C_/X,H+IQH4`)A`!Gk9H7%:STfVxEOuQnhlxi-N4}mF)Mw)>[(P;nUOFfn#?4SFWvQWxkq)Ak;V[CUwFDJ
Kp]2mIR)]te$w
;-(0&M#(f]t&=7TQtO@pS0q;D1wxMu:%.fL,WPUP<)nE]m<oqa]m2(p*SfBC&Cp,
iY>@[c?%?>M+8-HU)D`28/cDH3.Nd-T*YVI`L@|cI_|0"kDo=!O_awRs*^^x#.~F!WxGu?v#XK],n)V"1WUwjA;RnmX*28Tgkq5x06@TJ7D"Rh#s!W_*-R.]L8BblJ6:uB)7YE9,n+
sJPi>,")yo2WuCFokUpEb
[%?P)yIWI~1r$*h1NVU%ltxU[<EEL/fgdw2+aSC<aqrolC5<_|&k+clDpmkALKq##a]1?wIsB65Sl}8mMJWC)0Zh+/y^ZSp1IW(L(C,3A}Ti(mC9G8099=Wa6rYrctRXVy3BObmqj^(eMPIr(NQwT2Y)<TY;d@vL0RWgr6--`/*Loqp
6yf[07Nh4")T]%2|d?Nf`|L/Qg&rR>ge!J8pZBY*oZ>Ws2[YMCR_8B0o`OhjD1S$UW-N?&2-HPd+r6@G@^rF[/49Oo#?W@e0B!*d"2-CC^.M"+JzU"u-WNyH]eeT.V^Mv[b~A;*r:`YYXM/*TUpYyU;x%6Hs1k*bS{"GZ6Fg"wbV"{WJH74"$5v^qayqeZ!Lh&.ww/gza>bsJ[dTHDeEj@!T
O_R#+#d_;`M@vi^Cw<?#@6uQoX|:1H*ge**cN031($&pS1DS2):;8
@"Te;#JOUdN"O+Yuhj|$cMJ>4)AApw8hoSKYLg|N).F_qD?se>P#2J_lUp-S,N4pa:_:j72?}uuf+3+QUMyEn&zH,qhBlF:T{3U6nUhYBCL=T+Q2}>"U=]
Mp?Gp99q@,Lr=_],8v.Qa[AU5+RVFebsr~J^(tjKsq!R%#MF&sxMC;`}o6%+&{Ij.ccc`w=)eDVH-.SXg.?yxP)V@`k2o4@{
{-xDMPB8Lq=D`]}8ZhvdAtyt0*F;=KQ0[0yOx@XFAg&xg?UI+N.%eIpIws,6$(03hB3kvA{PZK$j,V*fKUtD6d"#-3A[p#H1l
XPs[fU~1Yi<vN>Rt`=/0<0";<Nb
1vPi7oq2e`:Z9"YLfeM.Go{DeN/2<::NbXTk/d8y>3h,Ad
t:DOoZv,6b&KR!f~y+b|12nm0dBUL!wmr|#>rLZ_VaI#.^1o>9
^vd1">E4Zu}r]aK;{&oj"f^3[?9#zorhMP(F
6~B*Bi?
mH:wCo;PgE+7W%-$f/_W&.su
ZR;I[(Lv94t2gGqw<&%k!I0SEG`>L+Jl1MF=ka[tN+;.d(4KtnZyXD[_Z2x#39n6|GEx(+%]si2Vmj{,/wb)AOplZxe"cOy%<Aw1x=q21?!%g=uf;GcS9OytfehcrvrsXq|:2L?^Fr~%L%~r/l
7.o
Fh]8mt>T(g$#X
4le/,xA:X03t/w*xdZW6[lr^Y4mWn;7JL=pE0vc>s
thsZg9xP(cBFT.S6c4/~^cu/x8Ec(ZR*!kKiG"#e#cB^MDdJR$I}ffIEC|WAZ_a<l9y:"Zf|"=<nhdl5=!4+br;=k`E!f)5sy6yP_d!.+Y:hc|a4`"c;nzVeH0rHI0O)K8#/xIYrSs
/%@kg@8h[E(i6.kxA.DM}y1i6E!hoQ5Bw/7W/Gw*]jvOdp&k^FdO#kN<<Z8n:G8A
LzPVGd*+_*3ztGvD%`]fUz
o
_##^DXpq.44<tg@.qq<M&5(,Dyan$vc2`cy`Yb7[dwPA^N*=z?a?nZ|#*nOx[f^E|b<1lm</Sa2*FS*GHiiLfjJsfgM:LOMJmunx_1zqtvA,^l-?;l!v6
MIjQhj)AV
KhjEpmN4)rU@hn}lTZDr.yW`}hT=8Jd0pctcN
D:l[Tf9m52#/OgP7yH0_Z({K]l6iK/$&rFKw?80")pA_tv65I3R:wPSaAc$y]l/,^W`9-wV=fRY+u67b,F7qK9&gHqf5|F
o4iju"xvo+
HqVx#V5doyPnc<`w"<zCv7}Pfb
1k#o!6I%6j)n+gkmSHx_J>a?TI){TWq6l/_`.!)L+}b5P&/o]{9VXpF_PXL9`^]6jq22=Ms:1-y.e_1Pc,"hwsi)f0u_wEP`(%l_D+8Stx+E+>7_*%
^GC&(w4d.,FP1&iWtM<_v1k/8F7`kaPLQ&LtRn/(ca@46]htc9TQr20_a0j.l/,,9k+DE)N3+di*OW}7p*L`~^==$
.pi3v;1gm!k>uO,4b,rD{Rto;rI"5w1L!g{;3h&*T:?hLm_`NE2O<Nc93mDhey?+v<#Fp.koxSx`!GrRQ`(DEq"[_7=_V,SRR25[&pxgk
Rp`/I%nbtKKW6v7">o4_!fV4mx}+[u*[Y+U1km]"*b`DpX)Ty>S9[K,;U8P$wl%:QNr2{oU3>%w2f;r645r5m+BljB-.@1^(;Vt`fp
v
liO*q
aSpJhP""H_r"hM$"-<$n3!;r4Lmv8$[$P.h@i*9d]$*Ug0Mxu_@wfE6D@9$Zlc&/k21aI=N%/"y?OZ(NUA.AN7&kq%c7LYF<H95DD^?UD#$7,7.+aU`o>@92d!XskUa?w`5jL|x1JiXNj.bllGP&g.0t?;C{m(djo;1,O,V?/
l@8uh!n+k{gUbxI=xv#P>gx|sDEG$dZ_ol6W36+u"A]y@ba1LmTn7T#hmgU3_ZW|m;)h^QArT>Y|=$4g?MT}tST>H_p"tnF,OY[v_pCHkTgG2V;4.i9]jaS92E-&9QtYpSvu+Y!cYgz&jwYiaSJ&YmOzur4W_,
2$Ef/;.EemO]QE=F+L>h61lO:,)mPa1rZ^0+"R7ePqadgI>32w
&"@SH#m>e?#Z:sdAPL#>UPr6t}x56|z$FIg,B";>R$cCr]H>/YM]qsXM
6.MQ]WB8l9<390p[j+]bam;8:cpgzD%R1^Ee]I"+7,B<7=v))%BSDP9>i3
8|W:h@L]p.e`t}UJLaE)3bMHHLTRe7YO%ryPpR1ui8nNaKx3wM_U5{I-2T<Ua?MKjI"!]ioNz$ONsTRLnzQ8fNbft1!hysFf8
v9s)&m`LOzKo*Y`v/,F@_1kb:q3h2Dd6;bWbme02>TOp/!xvU(Q(Z+c9D>_}P=qAh5cHEDP%
rp]RQ$t+-jZ$
Z#h>iSDcm?e#KWpah3RRhy41A_UuJV4Vr9xRZsMNj
Kam*;nZc73s1NHx#XW/!4!H<1>+~Svb&Ei2UM/V/8+#vG2EU2^ZU/fQ[7g]uM600a)&q(=ItxzV7M,6},;%m0sO_yxgEQ~z&i8_:4QQ2]*3?2HRJ-[oprRK3`?.U.<9,j$P2Ma26L=5Wpay3:py&=emPK]-7aZ%[$jaLIe!!uEFZ$`Y]CNdJRLdL>;@Y%i)
,gB!
(MNOKvSsA2~F[h>!U/5Pxp#5Tp&2:V-D]-Q"ws,(Sd`B6eK0gtuL*rtb(jw/W_4[xXtk=rj1Z->2hK2IHJ1Ll8s0Osx/4%};amDKlTac,!aq|mn!uT4ZM7c1p6NP[%3,H+yUrq9ERel+kt(R|35v{,6b2m+RMDD9%QdRVZNG|[$gK6MXL>>l)_wbT3gK,?<b<l}a0FeBV@c"`c/?jy&:lS"f"hbfYaSA=lY_Xq2y/(hv3r%
pIQD5,<1~%..B7h+te,f]2eW;:(17aR^"DCekSAep/<=Hvda&3;g]%7(*>M_@rJ$/-c:!qP]OLg[S`B@^Uw(UfmciqUSNT-=HNSx+V7
wa^J";F"395)|g9dbsE#%w+J,!>3)-1Z_
PRRaYHXqRtfR+wCSuDd1;]#K@MEo_N<EO4xWLq5>wVt:X+9d/(M=u)w(~5xo)58v$fi>h%:;F8PoyiK3oNtT=1:jxb)Cn]k-P9KjP!yD&]-o3Nk`"-Y-Lc#,M6w#>Crn`w1t>^3BTJPl$9/rGF7NdEKmGY[8tu^Z#;~*I]R!mtN<Ik|?OZOQ()JT2/T83%4LCWdHRc(q5o{cfr2&^qaB|ALB^@m
$WX3AEr_Wi%kc5Apv6J<+"E+h(M*|%H7Dam2*ya]dnPhHe-!Cp*W7%Hh9V?d?XG&/bNM^$J`F&#*ipA@!k-k=IKNI9=*"0)gbDtQg?9J5AQ%@B[n}/q(T9WTUwSJK!p-r2!f>9q3avfCOj|V
f4Dcu
P8o
X7593@-*oQI!xn;5;>KT;FP38$:1@Dz#_VV]q;Yy_<L%JJ+&FA2D1!"iO&SoGPwFo~Lz*/!Md?HEosQtYDvET,y_Km>nBs8WI6v$p-7}
Mt7]`e*NPnpWqea
nopac_f]uF+]Ii$H(>:rD5KnDK@h}*{lgO)Ba9S^6=!7ZW0,Y!W4@##&=o"Kzwx@90xL%AA5emw!u^ar@4z@&*~j=V]R+c+?n,hcN"QeK94fi1K[~)}=FEC=h3Qmk=fFbubv>!ku:;U%/(
*61vlbV`3I[n[6,a
e*_Qu(VbW$Y.ARhd[vO_LV:/%$1!sG:<JD$3*04D"fm*h&1MKZ@2Yk(ItD/2UwDgHH;QH_cB~k6maXzSI?hs;A&ko0o*2cy.4WehVJza6gS=XFY*<=jRgxb@g<8q}xfrLd`lCwT2D;wiK*GT;$jtUx9;yL8nJB-"r8xdV#Eqnee%=E_WV^!"H(4N:;NtT^~x8bPN<]/irtnsWpvi<34j.I,%*U3i@=`vs$[E|W^gXOg.D>y6y:-/zu1X%]ywm
XJt%)$+hZ?*Cca!7K%4Yl8HI=G=!J!#t
@._z_RRWgDO9;]Or#O_gV#+e2l:2wK<pNJ-l3V[_DqNRU#&{:6[]G=Yo5>(JKQ21j&g
$Q8XuReP%~_hhi.Dt"f,Jav{E6i=nzB2<Znm.PYkx~yUEbn@dH$wL>uPUH&gUZ3?U=0QGjtr9IK;qUSQ;?C{:!"lB}9Op
Wy6OPC;Z92NErHI!Pg]o9BNq&AXv4{U.edb;=7v"^jZ#@gD*mxS[9bNEB$
In*:Q)wj0Zimo@yBL3
S{_@+bC`UL/n@O<KaPNW[AZL&f[n;bWVbgCJ`AFI^cY=E{]M7$HQTf_I&Edpc$n8lmtoo)b|S,)85^lRcH9Ry"H`T|Km[QoH4OiLAQrf-@HRJKkjj&!&2.G&GwyI94<tM{8xD7o
)s:#@$D3mj;v3E-eof0fnL46/GaSEWsQ5K)z_zIz1!B;@,ms[gCu!9!8p$SH36KCQo)CQf^1[}bK@i!+Q*TfCBv*Q-fwCyevF-]H3&kTLzeXZh.p#aeFZp,$T{`c6D`WfFbmF
exiS^!$5u;X_j<?&RccpY&xak{VrL{#T5PoJ=m`ywv
jxiw]<HY&5ql/M0=9/RgY(UY~;=@K_4fNG88buF8D1i"xYVN<5|wqv76"%aBSp,b)*Ju
I6vJj(ul>otWiD)i.QFLy/M37O-Y41`oiH)$y[bDOrgfQi9Udl=rY
AQ)"o"O"][JW>Mkm&-bZ(,)Hre7^N-62CNf@WkvirVvbDR5JOnUaeM^L12KB9/B]+@*-Q#)j]aBY3_x~oQ8N<8
{^~A@)i/[ooK~<Xt^02fvZTW`H.c
d*?s"/G{UWg=iHv^[b6k7UEu4PHV>$6O!LG4
M<dlO+2;0str8PU]p,Ex~wUr*o#20Y9aHt2-eVWK_B$/]"N]6.-]:(/)#7uOVlMp~-`M)N*9%lT,HXK5@T-`B-E6s1{DeyAU}A:E
NTjik_f2f1Y4p3QlUgs223kKLRDtv,wpc_?UZ:-A3o*R==U%<-2J-Q/Tc4.t$"EIGe<Vl.B^W[SXo[Wt=o=a1pwVY%i./q>!TPS=.#(62S^.%w+_`:],;c9ZPsRe0*y"kOWUl{uk(+VQ#D`nZXmp**3eUDQ^d@["@8[m-?9[CEhX#8txneAXs@p`BEdALX:9`|trHX5gY4<D
q1Bt[%l2?TRUyFa0(p}!r!]x]nkoYlQ(??DvVf78WZA26h]_8ZF:{[$tzGQ/tK>
ce9/TV`Rm/t2R5W1%<(IKX<0N]plnJ+[c7*WGfr*:Uzt[Z-rQXHjBLjeP.mG6Cn4}/ArohH;YlG3%@ddwC]4OddEW_N-,x,Zr#1]$5:pnsP>^Ql3<rSjhO~j!FTu,B4d(aOk7*yk8JXMym8r&a4/tR$0bl,$3Y;aicA-%I"nqdQD)3Ko.etg(aoeZMJo-Omi-K>:$)rN:HLI!VN,p#>RUj8T!#Q.Tvj4t8NiqZ^!TB@H3YnL^mp=T4Gu<7qB<AV-81MKGi=#,
,((>MuINq$(/ah_A{&2/NM:41UGTv;O!-.0]Cu9kj1+_XJyVHNBuKf66W7s60eQq0u)#T!^(=Kmj3$?6h:#;is!X|:b"R1-L:qG+[rPBsuLgjJl@;9
6S
LLe%:).78GR+3,r<tD@5T!LoqbnC:2aF/Gy_jBTA!;p1iHrUH6z^)^v
`2!VUw4$n8|;4O,aJg]FNvooZ^UmIBhrbpiK
S!UdGxs[d`S{1TJb?>6~ZkSPZtV6niu;fLZbSd_8KZD6IE&j<`bjMbQ6$t7Fnm>VtZGX#da7E4l#1hSo<b=r;RKCJbB-S9B+!&ys+AD8<-fdWG:w-P$RW>TE9$ut="cZm4s%)SY.Qg2Js-/1ST`pie.h55y56&[3E$$I[M7C%XTK^BNsy=R~"Mwycx8[(6sm%khcsGR0.euIUD+gYnw31?xrC;u>8}sh:?PcK%Yu9t+^z#_SIwrQo$U"L7H}A7YFT4h+WTkjYVK%Do;.JCM[/JSrS
XQS
lo@3B%$}4}n!];!GHnU~#ACEWyL?OPo%W4Y5ebj
A^i6GqUdrZ(i;#5=Iq)|`NR)([V<=GfvG=_jG2k|eBK@o9Z4hv&e!23L"*(ydf_UU,GVTesrj6H=M29j&GWS.kBm+fxn84,Wjxwo4HKG:MVLwKY>YLY7ce.@pdI:1QpfEay8@:%eo=nj
UV0uAu;tkT+]|gC+C!~<v
(I%I`P"*2d0?&,FI^o(W[m&1)4)v!yQS)Jzg"x)pGI">AoxBboACTQEe43a,l*?-d:_Z;*~M6DFZ-+}&a/oGr]TNtVbL+bvm`A3+{Hk;q^|GE`[u312#zk]JFiZ=8S5vx${J}U@Z-)FA"c;mf#~1#5NIKgi5sGnHvEn3EcqDUz#OE"^G`EO2zlzH~3^Vq=0_P>#u(wJfuYg,_i1c;jZ/JB`moNU
M+:>1eE3f
A6Rg<(R;p85]Qb(eVC<RNljC*[|<<R5Z@ikWZPXOb&y$C
^,_yKQy_%#R_{<fJ*+(]=m<15iFcsGLJ=BO-,tZ8y^ifJ0N6M%m2IqU(6eEh7"A#X:!fXqASa93Q,=
1b!
hs;X4dbP<ZD"^:+]ugE_F0eIyZmUF#%TW<;wO^?aOM,x5?1OI^RM5ylD@%Qe5y<,]{xG5"=KHhEdlZ-CTHVKWF=JU#8M7:sJN+bkenpxh,7>79S4>{Uu`4
^YM1sa%Z,n1>YpPZ;p{j1;#H#gUtaBn>sumMA0lYZ3$>qaidS.hn!H{<;6L5L=RpvqU_r#GDz8SgUT>`Z_MS]cG&EI[D)YB&ag`!R`WoxRHQ`_q5cjc_9n8WG*Jv(IZZ3]oc7fm=)EbL(]e:E78GGh8p6nFS-fg@VnrjOQ*[ZU~as&:%bbD8(%PuW:?KNUj7-h]:SK#%weU9Yr_p*#Y>8[pAi8&<5v*_SCQcle>(ABm4HEjR}_G=F-srDZIUmM}9=a;9m,#+EGGW5ygA)/WDjoU^,cXa@py
/WTy%KawDyz3Nj^<sbr.V+*;g#f^e?GIg?g_>Q7o-@O#*.<VzVr1gmiJQQ!T?e~M([z(PM]9y*z_f8H6~S#Wh+AeyjH;x4Zs7I`_kjPE%&DW8Fi"((G+F6uW}Fz8JTT1ap{<HM&u1?>q,K@4OSgCwNA0I&.V^(,A!Pns{Y*hi:!3G
GjX))U(M)P(gu]|swR.;T&-Wilp+H#-cCP17-*bxq%Gmfl;LDO&=Yb
f~tLY7b[VfW(c~D)=?ohPbqQ@o81H.GJfX6icP$,#B31H$[F(Xw:>;
yo#VcuPDrqdxRQnv<+"?h
M%Ppym}]~x"OR.]a<0wJeoF?CjxSMVoLl/fX2!z[nOpu2N0
cf.sRXtHbJxZT+s)M>"bHbv@Dg8uE_XQ4fA!GrJ!*EkKm;+!^nfA&nrxi1"#*tk4+F&9z81"1nZ[GZV*9,9mGo%g$r(sE.t0$p@bvv7He[m=6!CKvD
w4pnxepOhK

X=RJ,4&Y*z0jF}<Z$&D*#t;RE|r:Occ/OK>
j8Gb$e+/&3fV>&bwU:#42GA_GK0r3q%S_v/nK
3Af)iY%+m%tYj|sbqJ@#:)r@6Wqifl,tZEjCP&yoDL6QUc<xUYj.qIc1dBNkcPG(2DQ+H(kxl!l.DS*MTW*w?>mkqWUy712nw=.,MM8(Y#:n$I#0M4!TnE`{7uI6e)+
bAw{ru2sUbQrg)q6EXc`Moyv^CO.gBtAmVkjuuHoK4V)<V7:yYd7G+1!UG_s1!r$dxN#$tgZDCj$fPutw`_cJ(mTa2RNL)?1j{W}2x#1v*:`Jk[`?j0JZC
|wAY@V~$[JCkVJ~1Fnd89=e%s+0?R=kXp3;=0k1CPfzbf->x-NMV-pF_frK+cj7L$eBGxyu_&"L!K29Mgb~Lqf(Uar-]/G9u8r(T6+,I5+M_S:2b$rhxEyVY>ig4mgOo4lb7ySl0KoCg.H-yCHBgBRh=7vRrQ-|QzxB05c3!:Jndw>?%%]cK4ocu_IRrRsN8)&GTF]L^}@UV}narNBp5|
!%x+=f,iX;?%3ZP0]hnx5$|gLT.1:Qha]l&8bN5IcP*%.)~,bBWINgM@Ti,pB=>X=>BC
saJ,sv1
VqO0sZjj*UfY6+=&L{H4mDPXo^Y]J8U,X1!f^+_Ro
egsjxLM^9612SMk|*Wq[O-qNUF#A2OcrA.YCRQr)m#=3grYc%a@2@I3lQEg8NqJ5?#PFPUs~T;nnS8VFn1UcHJ!"Sce1a&jy4[ByF8=7)YobHL@U<Q3<)"/
a[[avr^@J.]FbAE3xdl42x"!w?qO2_NoF*@3Dsxl+c<C++txU2L#5h!SRx!8!;2#=/n|o[c}yOf"Bxq9BhxPK.-=c=7DGE`Q=4)EsHNnvDtmU:pCC,
:uaTy"1<O,Vj5HH=,%Kxvpt_6heY?a2KU?H>sRm
cs"b:$7kqv=0GwGs(b"[j=*[uQHyqMci;7#w;S*5O+GwR9p=~
QGLe-p#u+ukstnWr},_3Wb"Ph>
bNi|-f`;deqX4$$N+{kb2MgTc`bzYfb4j)L64%BGyO5s&,EvQy#w16
2+Sk>P!`^B,cT?iqfi.>Y.kS:J|!8h7u,t-TYKij*8j(S_FW0Y
B#Qa!9(ZC{f#kM1Xymj!y,QjtroPm/i~
XFJRG?cGfxfLc7["syMaUeanMHsdq$((Rm&0d0Q5D4|(uwb^[sKai9vbQfI6LWD[RfCtYozMl,vTHM9@hV=");Z9:UiDOQ.rISJNvQ^xF+tD8(_6K0=&Pq+w6*^1(VwL]wn(V>BcggF7>1$T>3I,%)[xzb@15y4"!#k
v).7iep(3AhkW7qB/T,evy
8Y/CwFR?`du(U*Z}R4DPudF`s4BVI8k3r=@Ig@uLG)7VHwZ|huD$iXaJ?,44D7FBZplo0Gh2jc9B=*)Vm)b8pG:V#V])MunOhWc~qc,3NxE8kJh#JJsLM86AA0yVNWJWY&-q;SIuB+sDCFlg64U
`ex`[OIPPTab+ZAiSRq)0Maz+rbA:/bx*gD/p]K}25-AKfg(X}/nV"$[cGmQ&sPxtPR)mvMMbF
Ug.pgSJSFP#Yf
?aH/irEgkwAyTZhx{!Lrz^0cjlfQbXvZQ8S9}1Vw4
BK/hta(t=rvt=ubF$yp;Xa|0:rG-By%:UmX+/ptmzK,b0)jjqPCUU3.7`,_][&mgZy1AY]cqn:G[TVrh8@2FP:*LSIa#9CZ3k=xg>lj&f`C#j;kKR.StzAZqrA=0z<j.+Egn[!,JDg_WYvC8d7>J?D":B+,wJ?iZYpvPDH*rGb14tRb7{aoWHJf,WV>/T!sm)+iI)]{4_6`)BEK)#wQ<R`j9x!=N>ffg(HM)atnv/?_Ilb{d;1/e6FDa8v:Qx#v*D^P1`IFLUGMH:vB@t.x`x!:q4
LtzGZ5yONKg>yUm<n9K4n.@5yYKRufXtG]N-UB;PFc*k!*&k@0exC<))Gr85Tkj#X/)n!uEUDq(9f,rV.R1"#5Fj;3W.!xluf3>^
Vc`TCvu9?PH6eYFW"`4B2y%g=ADmft-sMPj8LD^pDD^.`3Jm/U#+:(U8qd5JW|+ii@
l<af|CNHQMn@TC-w1?NT,QWm1o$j`ye!;/fXN:*kMo83
2`
G/0_?9sDhW[n5V2+/k3q;[!wSO(&26=rN<{V%h1R$vLT-rghLE)Mq%jVf_]Az.f`:y,PGXJ2m>6u/E~p@>g`sky7HTZ$a]TT4]:S-<:,ei<6IrF-,3v5m4qK4O).s##S_%"`3/o4R
]kFYmr-M<@JqdqMYq
kAz[)mJ]Px+kHk,Ige#<
)lQwTY.1c6H}Ib"gy@@/aSFSDuY->MY/cqUImht[tSXljVGNLo.phW.m>n87Ne4Gq925p
/zr3)ljE02h_2}$D9{:[X(bHbb5aCH,xT,RZoK[)CLqgA1pm,ZxL-_"L#ju4Q$pUYa>j!3BT2lu,n)Q$q&!$Skpe<3>pm80LvuA]7KbNnghZRvL[gak4mO5*xC^*x6=>`2&mE!6~Jvbk7BT<pEXo[x)@>y;$@CTC
u5Vqk]%dSF+L;+.y&mHmT_a.2`@1^$c3g
@fe+k,@KP[<*sbZl.)MZ_2rTu;vfW[^IR;QMsUz=
Jk&?,}bo$
9;.u%.+d-DcXm6*1LJ!g>QGqG$E6?D"]=]8mL!_3LhX$5X(yM1ghAe;8bucR+Tjg*
<L%C^_JtZ#*LTYj:#sq&9Ya)iym>x)Zpbo,E"y<:946mnul7:H=DnQ,oc|Ejn}l7]dyuNDEdtUFyQxdcc|DDnqlN.$HC=.D?iS2dtF`1?6nO$>6bDOVKrXlE;&<<Iu^3k>n&VKKS@Byiwo$)xo_]/eRnH0Fi=wxTdYO6W/Y0F$0IEE]%?E;$j>hC8xk>+%^Hb2oZGTb9s
qbO<>Op|ko6D<hQ9$[E%?R5*,6cO=M7s8t.~nx;pR7Hd7$:{?<0KO#?28Tv/4WJJ$1fA0tVub("
P1@20WG%ThKl]_PZ+}J(ovT|hS7"_WhMmp9+/EKz[(P"&)$V=2]ko]vZNsud7?b"U8GH`0f6:;Y4k(<KFW$/[na;_|@c_f^_/yB5K"FMf8v.ddWn)fBK`<&bh[FY&;xR=}>fw_vWdxUt<G<o1LV._~#0g}_E.@M5L"12g^[bM-=f_A1Tpe%[xt3C>y+1o`Z:Dtj*!TLG`"12$h#sGD$e4|[K(i;C(|k+1bxDXZDx?3AZf=Me7DO[h

jBCc3X]KOm#=B?kahNZWRN#Y$0=r?WKs.70_7lz,3uw3k.-JJ)LYNO,Vzf6H{RtZm6#`AKAKK
dT?bW_rfJCm`tudesI.DoovKBKKTSL``-oFE$U!N$_yE`I}O-$.M,f}"s$Ix.yE9}uPC
i:**h2m"#in}V2U$?/OeTx
.clB5trpt4O;nru">$$aJueI1n5QE:qD*IHifDFIg3Ya^xx#I2.LuXklC(r8*[d]?AkA?DN%I^BwDUgd5sw>3%q]rdltA=kH?J/*Cpf&<5@WgqK):JmByRLfz/m3lnP8=0+7MbA1S(vSa$ebp^r`a/ueHnT_uM5Aejna<tgS@;&Y}9<`V(p8}5;XrrXVR,IA-i(g|h&oz-MBdDGlZHOW"CF.iV4Qop=w<3+Ol(2OEwc.UidOuU7#S<Iv$5H$;reme(~y5:&Pi/og)tgDs4hyg:H3)+JQ!pNbE)[^c!.M>*d#aZ-h}d3bboF`z2il-lb%WK%1ptG=uKz5wk`Kxb_6`*,CV)[_HtgNHyw]C<yDVIEErg{LKY%YAy&HsjqF4_H]KKJ-S!$W;Ez;;R3"r>^/0;,`5ugI@
,HvLPI@2aI6Lu2Oo=/i#(chNn4t-i5W,`,D+zK`gO!y&@.ToJ<ru0q4xgeIJ$Kj)6/AsTI<CbT{":bWS2-q<ixiH7]sC(F<U,J"")5?`N03_tgW<,y8?HqTejvI9x?>Ojh!o=oe&>TOvOj8dcN=YNSVm!p0kKp`#Pen92`TR4hY!?wJ&*)2CM*&[C^@n@>^xi<_l=di"]KuQ{xhIWK-U2=qp?.1^F4tuw"jL:.URwRD.G;7cv8_I7/9U8v4gv/AkIDNC3R0#WOU/!Ni8dQCA
+.&I^?r;+QuG`8XgqIL{9F[.J{lC.umg<=_oov;Up{TNu8HYUe+)!x/]vREn=sh-J1?pT2q*vmPTx~=K7]R0i]xSjUhnLQr<F6,oP_l6h=?~Oj#"Yg",USdEjb7A!D?!BE[eV7$byHu!7PY+>~iXG0B"NBcW_~qYQI14MF*Bg>2bd8;N2*/e+sXj>D#&W*bEdJ,r-`FBX9$cXcmS7qI?5u(NM{CHCLr#8j-L9rw8!P(92hV@
`d,KFL[!XyApGNehskZoRa.s^jw@+Uc8[
f#*8cU]UzC5cu$lG,_tO:-g&0VpIVjNC"HGB(^rcFBSZaO5RdL?d}3VLo$yyL5#5aq5Vaq?=Gt?%.#>YCX[F]qpy(-=crMKV-d{xky`>uRfs8FLW
8hK}EfsoM5jvavD]Vd7q5{Z$r#(151Rl+%.,;!s{;|_TH9&ir/d*`nO{Y:%L-vqye8[g<!GdciN|U{!q,_uD2iStN7%Ms_utxi^V3v)PPzV-N
1`=&8Psjb6##!@+yT@5T)}({R954O<"12iv]7NYj6V$B4$*L.McPC)5M$+-0ObW)u?@A%8&P$|R8PT]7=-#6XHOf`RE9L(lsLnSH,:l8?.>aw2nPI0AVi:JmA/sL,8p8Bj`esX>Jc_-#_$M%G1Bl6-ah(~Rev~Y=4}=CH^h;@T4XBF66K@6ejM;31c[X%LJb3bPM!qCjtbq5bie{wr++*F/kPFB^E`b)q@q./1isj8"?W(h,)PGoO}*@1RkZQ{F1dNRZMY;?g9*^WU-CG#qK4oD<gS@>"8
gsQ&GmG:bS>lB7Y/zJS1.ft_0wYqTVBGwNfD}VAfJL%^U6w$)MlCv(1S+vTE?o^=>?2(%q?)~._@
-dyk1O5c3Co.yY&5PSd8=o%I
BH!
:fz)wc$$Tdfazybe[G8]]#CM]N
C~.T^Riunlx+DU:{SmPH3F+
q_RaC:Q}Td1I""](Q7S102X5Zcv"@=g}[lI~?WPP2San[:XgmI@PC-C(7gC"ZyZ12Mx,_;<STvH;7T#JhEZ+5*1^;?)gIt#R#hvAB:;PbUy:lrd,3"EJsX@7&YFk2?oHu3;~ayKb(3;:ZotC;V`z;
_V)OYW^d4"v/$yTa0pAM"PGO
#3T(iZ},/p6vjEE)egzCpVT>Q?-Gvs9kQ1~
YEV7>f3fL
0Uf@HtzmEk*e9*e?]X!<b%i^>#@ph"G#nI8;9Z/n@oDO"AUs3hy1r_X;|+TKQlV%&(u8kwYoMWgy9H1B07(=/0>v^$hPy1!pN8u0-3s,UJ,m}ptryv5pKK8uvxcX|?#tT>P]l+aiuCmL+!k4IPz8y$Iog6eK&VgnLbiIQ!`c8uSFGs53)W"p"8kaS?c/`J6ElX_DpJ`[+P>
YC@<"B|B;%IgNGk"P].^s
d7xbKt8J"U>JQBj;UHB9Cet0I?sNPU!nIpJ?$Y8yf#Y^Y)cySF8U!%hP>KQo!+=VB#mOz
j^IKXu`g3Z@UaX"L!5}ouMGaM7IZ_)oN:lmFu
P]gP.lDpCi]/RIH"1=Z@?;QGy6J1m^(<rx05!R8Pz4V/GJaN#eNsTvY:Z@0/IuhD|j`
$=pL*!v3"4KK?(,sC:*,Q%Vl|9;A:<6MYrPq6Gf;YASvq]vG>mN,"YF.kmf*=xe2a8^E7ncL{/eqf5-m|;X<fvyHWC(.,34)k#;B9W%_fWxOhPW*:J0NhU$YRT>O2,v?NdJQ
;*6HYjuFgiIZl}*"X6
8iTUz^!8o:H!w.!lD;}I}BMX`YBanW9:![KNO]_M~d;p!MyGnl3Dd[n&q&o(5Y>s`h0YAo1vhD>IR5o.Ffv3rs}By@pa1RI3J99d+j}r4D_9Xn4/0+qSE>X_UHMs}^1P95!39S#quA->@AwqM76t9,X#~K(J?V]($UtKCmLCc$*.Ch*nB_G?yrp]9Rkc
6d]AJ2l-dkF0?H?,-fFq&i$~BKZL+sa6^Q7O^E3fQA(;>*D=Yw
apr5TGvXI:8[j(LCZe#B_i`D%Mw;q:3R9Sy.yNJ6}C*L#4g0,Q9gYU>_rnEH~>AHD4:ix@#GkI/g7(SEPt#8K9jFXqB2v$2c=aOCXt|!gx@<2]?e#uWq2:I-2`
&eU)IIHJo"F%dAU_foM61+fv6O`7Sba/-vG<LS0M?~q4mZ&`.tTq!hP9SUh"p=!][hIf@V*6Q<ap%@uOU;B+>RY!7.G{YROp
c(~eaDr2ZNW`CNT
g*ETcwSuYZX8`1og;`U(0&6n2fF--n_KA[Y
VFmq9ev2kU;oT)po,[$1Tlf+0qaV7hF%~yq9,>{l2.obtVS@nZ%*4x[<E[&^qO#EgfqbT_Zc)]#Lso`1h"$Nr4mI_jL9S+=x16a#`
,La`ws,l7jz.,;Y=^5nc?Q-R,@E6b)au~OK,(ae[>o$1EkBmoRL5V0Z0(GZWq>,udSCcay8Dv/V@x2=h%nZCE<O*j3%n9E#2gN?LBab;jVZ&T([4xko:VQP7J1]D2Z(C}>/NSpLDE:&M>U,IN;A`zeC2z[#G6h$0T5gs[Ag[S0DO/wqo2]%R@=QI*go1Mg:fO$$[Nf,;h5CXBMANwIHRkG0;cHv.0TjBPa4MO9!FboSHUN:A8.~]$;RVn3!gaYGR:81n^#^b&pQ7m06&F875idk-1/_^vjfLr/0Ci,98fGTAo2mcWE(>I);,zWgZ=#Gf.+Q$v!D%+k9Vif.#4Pcp%qi<ksuEywfuPcH4RB/>Q-<Ur:/X<YN^,On^zq0H8<3F)1~83IQ;86[V3bl/%,"cIH4Y},#,;L:eiR"VAtC&1pPZj@"9)7iO`:jk}EBOlUK%l(GHZcNRj7Ld,)zV1]:9(v3/3QDoewvlnVn,^`;-}o?h%!J0z/xN&x&2MN)!1DbaPk|`GInR3cP`NgE05#w)S#vS5>=S3xt;#n)Onylp}j%"4kjD.gShb0:#$`uoH#*[f0nQ_FD+<a+8oj>*y"
v`EA)^8^/wNy^u!,6IDXd5g`<|76PLmH2Y>l&|<Msn7h^zlPmIRS%r%k3o:g(&[9j#O:wQufKp,jd=&SY}#13uwDE.emQGtAh1@bn|B3.]Fx
g:soMIx<q"_0OiKgU2VqMFl&n].e92~F)9-k-,W]NO%-(Ht;yE-
nU=O3,!1P>@RvVkdSXic0dX(h,.SJ&m9<R6YhS9.Z?f3acHEDE$JQQq&(KZ`}n_N_C]BY96G<OL#%ngA/8H,]?Ly~V(I^7>0mc?tA"IK87f6qrbB:/dr6#Lqa.Nj=Uu*q]]kysG.]W0HWJ"wbb%w^0^]DU@t)-fE|D4DpVsWv4^14s#o,a|/p25+T_LW^uod+&"9**vcT!1
+p!1^[IY6l62Q^d*lU_3v_O:?5O=|N941eu_ViFi@vv&.qeC/0,gM61pEn5L/)w5JL-^]^qv5/HJ=AU@|LjIG2`loe^N>_aVKgeQj?CLTp{7Ih]$CqOF8swsWp;6{^ZhjWT=(<W7E.k8b.krs]u4i$QDYv*jna#Pfvcs%S]-wdoO.
}3Iu^dPawg0j~;QpWuBb$+
Rwxg`BQlyv6gG(3qmTU{/.]lAP1e
b
AlB"|-Y=UCDA=PCsVn`MC^#"wHRl"PX4;r1;IZd?WfXLi_~/K!9t4B/K8)V:urY^ymImT=)$+7?LwJ_Cr7}kbHWBCqJCu-@a%I,
s8~T~nBx[
$;T>fBpw=oGjhxv"PcaHoY=PMv+n-Rqw(CdN$:jMr_Wu:!z#<cCo&`xaF+$fNnBbuQ?+Xt7G[s
Am1$mNe9!.K$H^vm#)H,C:B/fi(9N*o{j2AA$yhqnquQdgG=7Eb[z"+h"~XORHs6Y^q]LR7O+&^,Tg6pxGV9
2nio"O|4@Y^Vo4F%F&t,FP1u0[lLmt7)g3zSG3NEf4y1(;zg?D1^CgW2~``od5H-^k0)f6~[H2oq",W(<F9!MT5H:;LeRTchfXry]XjZ$G~)zvz)6u[PHW*$=uOM3
@W#eCuJ!{TT(;Z@e6.X,9IoS0mmCpkiCa3O
J5*</WVxWDvK>8P9R7N"IRmRxSiJR*F5Ywx+MSq4kZWd{l(qi:Kk0%$SpR)"1BR<B4icb5bqV8z^
NUU,r(/7Kn8%/Q<dud@s&99cp^)RQ?VeoQmp_JKJ9>FDV~Od,q2()yYkGXPS4~ca9*:f5<d@v/xQUq)w>u98=j*/iz^n=~gy
`aUb`FVkgn@Y,Hx2OmMv]%3
7:QQ1b_J<YUJ1+TXJV]wigVfcmj5xhQ@1
ha"+}pqa[]X#HR-#z-*
[jAa{#)c)6Px(l4i$062;,;<cEub
?K3/?J1<a0L-$b3L[;L/&S&+rua;`|X`#Aj_IVdVjn;XWoyT+M<{$X&C?w3l+}(H5oOugmMDQW2NevYli><DP>>1S>;mLsDAM]O*YVJ(+N;:Ek?RI_ZIA>V4=$?YD-sd]oFTvEXIjU^@jz@q
*B8vW?jgkp}><eq_gUEhZe9Ot@=kZF
Rn6/c]t/pVvO3JROudB/gKd8pve^+J;kT|JA
yUx?
YN@RVbn:s%[e>A]-BoKj1Pa(`9x,H_h(d:y(pBg=Ukv@K!x7%)V*qNg9Dhw],%)N4,fA1{E|&lADqnop5|TvUlVn5iuauiWG
zX%FD>M8w1En&V7Abno&o44<N!$l5,(>uC~6h=05z-*tE12@e6Y?G6*YqZ+7!jA^?[Q5yZy#c3$iuyU@Pf9t!idBqs9rY34Sk7daRE<ImMfe`&kUi4)nMx_dYw{4IBDKfT@m>e6hs7Ei/+0w+@3XDF^bq4Nl?QwR5[]%"%3bjO87"chJ;a`1DBIrx({J3,>T0cNy>;Xr>M.%#i:_.K*h@Qm7v7/Rj(6t|3{!7_q<xW7NDrLHm_Gvw(Yw5@AS_/
"2=W:Z=*ac!l.Ku]1YE5LGH=5$3h&S&F!4ecdkbj</aI]xsLODZkfWV%3@6YJFl^v04t1(eX7w:hp.v#JBM3v|qUc+fm$if
h[,xcyxcZcutsh6pSd72%|VzVsJSK0pL$!55a9Nb4#r-_HZ_o7X[4ZD]%ELBCLq72E]Ca?N`qQg|u7wf4y@3sNB@9Y./,L0MEjN`E*fntk;v*Ww*^oBH.,,%6-kqc9&`;gr+dsEO!adc=)i9!C8s_wGUtG
Zww[TSc_`>T?d7YkaT_kl(MjhH<`K`*#.FH8Hl>->@"ZpA`Z<I^Ihyjwzhxoc>x)UXz=ZE~Kw
?^Ynaw)_6$0?7[uA;%|nMCb9[&y`gqi(*ugVXGRgvuh2zAaObp?9H!^Wqu^T8+L:e-=dh,/uc>A"#5,TX]qZjyg;Dlo<:,A#|s6].G!Ftrh01`Iu$eG6LLf8"MzU!HX>Y4v0}%{sVwWD.M{9qF:d-R*<{3joLeXezP}i>q*C<SVh_0TJ<swV[K0SNM>gx)6ktvi=Jh6E]-JQXZsq*dr5MfK0z3h$l7yJ>7ID8/"_D>VMh(]8c2xoF65q4@HqhpMw=
aEhTY;5<R;2SP2nn["sifq0^&N,*4HP)p]iUc&4pP]^"_DQCfJE(e(0*0d,7p`P73tx>rfEd3xcYFZ!mx#bErJ0:cW<ILDf>[1xCxE(Vm`aGdY4Faq?XxMj(rh;.]x#3l^G%_arA,1yx@o8%a(#_03^]:sn!F#BQf;La2e
j}AaW?$D.9Cg-MsYQH4`Dj=J=b3WBwS|3"jqY#3*Ipru4{&B?,$T6~xb;uX<QmjJ$}&B7,)R*HO*XO,1&D$p#HKTk6MRjc5!iR$`k-I
O9PeC_kVlFf-^>r_";gN)4F]5
*vE|!XQt*#O$][PN$%>KO$&EwO`.#f5FoCGSH~.i-L6wR4UL
RWN-jQ)WUnHs%"~%3=f5UZLK]Sv@1vm8
,R])["v{wZ9bO6Mt
)XZ7E#TpUFDJb(dg2w<Xo%=HzN.SOi78Cc<";>qnJPbBwG#@pyT%*)lyG12D9tnk#vcd,EJT@j9l`eQd#1cjxvOT(yiqBa:NpFrY?[i:$DgGLx6wa*Y!tf9s;55:|dU5li78Qnvgv;-11>rCCWg*Dq-[2a]PS!`Zy;K(/0j[~iE,3w*9,qYuN,oCfWzxe-Q!-2I_S<ix_r2]ub,L2$!5/yCe:w[</.9Mz]"JXf.l#E*i9!{MqDQg82No1d
q~;Sc4fgZ7_]-k*I]6-;Z{1iJiP9o5jU!jx5W*P+M@s<<(x$lZ1KnL*v@j_.R/(o]eM~8XCt6/VDXo:4m~SJE(2r6/ldSW2!v.2OxDH7O/[g<[%Xd
ywqUr>xl)2mnna@"la&&d{,7vz:~J-QVflw3&"HVWJ,#E0?*Tne"Vq2"5|sDs+"[E-Va(FlA>?pCmp-EabjIn|F(@2s"Mx*9og');}elseif($_GET["file"]=="worker.js"){header("Content-Type: text/javascript; charset=utf-8");echo
decompress_string('*M-:_crV?&ivhwW00>hyk#FBR?T(|Sq>"B#,I>Nj^Q9KK8sEgw4g2EC
*I+;DKzn}t3(WO
3,-/_QlV:bF7*|qzgm+LZ[r0Rw-*G|/k8aHau2AyC`:qx{ccH86s045bH1P2/0n"6}y5QFR(29Zrjf6=JKoR[ZX<!#*8i<5q.ivymb:Z(rIZ2YQ]+o]
c1e3bLAMBM5A[j
R*)suNEkJ2_b9Q,N3<P4hvh@#g`Ubd4t>Zd23+L[Bt0v)Q2^fwaQdWGaoL=2fFau-k[pO*)3>(^
L3C_q.O7n@ic/h_PH^?3P(D2iqS^rMAuO_swO`)*TiGN,)~(n+#u:.`d2HKi,UCsH@.]A%*j08LgKJ|@kX+bbB8Zu(St;xKfJ=}=9(nou8]":5u&EO~jfR@9f.[9)!m!m>c&!6F6vOL1`]MawSXj)67Xp@ygy7)9*q,X#[h/L6mfb@W@"#ngdi7B#e$3RlPyr[q*H-nfIGG."G="QLE1bbI!fYOm7erh[>m4s7/_nwA');}elseif($_GET["file"]=="logo.png"){header("Content-Type: image/png");echo
base64_decode('iVBORw0KGgoAAAANSUhEUgAAADkAAAA5BAMAAAB+Np62AAAAMFBMVEUAAACDl60rTnZZdJNziaOerr60vszI0tr8jZH8c3X8SUr309T8Ly78Bgf8r7H6/PpDBKXXAAAAAXRSTlMAQObYZgAAAAlwSFlzAAALEwAACxMBAJqcGAAAAbRJREFUOI3VlM1OwkAQx/sGG0Xh7GwTz7b1AaRwNhqIRy4kPRKjpcc+geEJDHc1chYPfYJ6N7I+gJFQE+UjJIyzS6FqqzeN/A/dtr/Mzsx/PzRtlYSI0fd0Ju5+wDMhHjCTMIqaXoS9QWYw3iLlvRHtLMrwKqDnNLyM4m+lReizCOjXWCgqWdPzvLgJNgnvUGNPV6IVyc7cim2SrHKDMMN+L6DhTKgBDVhqCyPWFW3KwfpqwEOAXUembeYAtn0W3ssErN+RdbxBOcBYowrU2Di8VrEdWcQrx0QjqGlx3m5LUThK4DFRNhGy5lkwp2CVHZ9Qs2ICUY1cGmiUfj7zOnBTyYAdo6a8otjzR0X1UT3uSc97kiqfFzPrMqM39woVZcoUTOhCin7QL1IoJLAOKcrniyCXwUhRboBplTYPSrYJPJ3XLS6Wd8fJqmrqVm2r6vxtvz9T3kigm3bDzPvxxqmn3QDg1l7VcasbtgEpqg+X2133ixlVuTky0Sw7/8eNF+4ncPi1oyFYy4Pk2tz/TPFELrt0w6aX/S93FMPT5OwXUvcbnQl3rWTT1nIy78akqjRbPb0DRTX3Uyvxl2MAAAAASUVORK5CYII=');}exit;}if(preg_match('~^/[-\w.]~',$_SERVER["HTTP_X_FORWARDED_PREFIX"]))$_SERVER["REQUEST_URI"]=$_SERVER["HTTP_X_FORWARDED_PREFIX"].$_SERVER["REQUEST_URI"];define('Adminer\HTTPS',($_SERVER["HTTPS"]&&strcasecmp($_SERVER["HTTPS"],"off"))||ini_bool("session.cookie_secure"));ini_set("session.use_trans_sid",'0');ini_set("arg_separator.output","&");define('Adminer\SESSION_NAME',session_name());if(isset($_GET["upload"])){$ej=null;if(!defined("SID")&&$_COOKIE[SESSION_NAME]!=""){session_start();$ej=$_SESSION[ini_get("session.upload_progress.prefix").$_GET["upload"]];}header("Content-Type: application/json; charset=utf-8");echo
json_encode(isset($ej["bytes_processed"])?array($ej["bytes_processed"],$ej["content_length"]):array());exit;}if(function_exists('session_status')?session_status()==PHP_SESSION_NONE:!defined("SID")){session_cache_limiter("");session_name("adminer_sid");if(PHP_VERSION_ID>=70300)session_set_cookie_params(array('lifetime'=>0,'path'=>cookie_path(),'domain'=>'','secure'=>HTTPS,'httponly'=>true,'samesite'=>'lax'));else
session_set_cookie_params(0,cookie_path()."; SameSite=lax","",HTTPS,true);session_start();}if(function_exists("get_magic_quotes_gpc")&&get_magic_quotes_gpc()){$_GET=remove_slashes($_GET,$Id);$_POST=remove_slashes($_POST,$Id);$_COOKIE=remove_slashes($_COOKIE,$Id);}if(function_exists("get_magic_quotes_runtime")&&get_magic_quotes_runtime())set_magic_quotes_runtime(false);if(function_exists('set_time_limit'))set_time_limit(0);ini_set("precision",'16');function
lang($u,$rh=null){$Aa=func_get_args();$Aa[0]=$u;return
call_user_func_array('Adminer\lang_format',$Aa);}function
lang_format($Jl,$rh=null){if(is_array($Jl)){$Ki=($rh==1?0:1);$Jl=$Jl[$Ki];}$Jl=str_replace("'",'’',$Jl);$Aa=func_get_args();array_shift($Aa);$Vd=str_replace("%d","%s",$Jl);if($Vd!=$Jl)$Aa[0]=format_number($rh);return
vsprintf($Vd,$Aa);}define('Adminer\LANG','en');abstract
class
SqlDb{static$instance;static$untrusted=false;var$extension;var$flavor='';var$server_info;var$affected_rows=0;var$info='';var$errno=0;var$error='';protected$multi;abstract
function
attach(array$M,$U,$E);abstract
function
quote($P);abstract
function
select_db($gc);abstract
function
query($F,$Yl=false);function
multi_query($F){return$this->multi=$this->query($F);}function
store_result(){return$this->multi;}function
next_result(){return
false;}function
inTransaction(){return
false;}}if(extension_loaded('pdo')){abstract
class
PdoDb
extends
SqlDb{protected$pdo;function
dsn($Pc,$U,$E,array$C=array()){$C[\PDO::ATTR_ERRMODE]=\PDO::ERRMODE_SILENT;$C[\PDO::ATTR_STATEMENT_CLASS]=array('Adminer\PdoResult');try{$this->pdo=new
\PDO($Pc,$U,$E,$C);}catch(\Exception$kd){return$kd->getMessage();}$this->server_info=@$this->pdo->getAttribute(\PDO::ATTR_SERVER_VERSION);return'';}function
quote($P){return$this->pdo->quote($P);}function
query($F,$Yl=false){$G=$this->pdo->query($F);$this->error="";if(!$G){list(,$this->errno,$this->error)=$this->pdo->errorInfo();if(!$this->error)$this->error='Unknown error.';return
false;}$this->store_result($G);return$G;}function
store_result($G=null){if(!$G){$G=$this->multi;if(!$G)return
false;}if($G->columnCount()){$G->num_rows=$G->rowCount();return$G;}$this->affected_rows=$G->rowCount();return
true;}function
next_result(){$G=$this->multi;if(!is_object($G))return
false;$G->_offset=0;return@$G->nextRowset();}function
inTransaction(){return$this->pdo->inTransaction();}}class
PdoResult
extends
\PDOStatement{var$_offset=0,$num_rows;function
fetch_assoc(){return$this->fetch_array(\PDO::FETCH_ASSOC);}function
fetch_row(){return$this->fetch_array(\PDO::FETCH_NUM);}private
function
fetch_array($Rg){$H=$this->fetch($Rg);return($H?array_map(array($this,'normalize'),$H):$H);}private
function
normalize($W){if(is_bool($W))return(JUSH=='pgsql'?($W?"t":"f"):+$W);return(is_resource($W)?stream_get_contents($W):$W);}function
fetch_field(){$I=(object)$this->getColumnMeta($this->_offset++);$T=$I->pdo_type;$I->type=($T==\PDO::PARAM_INT?0:15);$I->charsetnr=($T==\PDO::PARAM_LOB||(isset($I->flags)&&in_array("blob",(array)$I->flags))?63:0);return$I;}function
seek($yh){for($s=0;$s<$yh;$s++)$this->fetch();}}}function
add_driver($t,$B){SqlDriver::$drivers[$t]=$B;}function
get_driver($t){return
SqlDriver::$drivers[$t];}abstract
class
SqlDriver{static$instance;static$drivers=array();static$extensions=array();static$jush;static$passwords=true;static$serverSchemes=array();static$serverSocket=false;static$serverPath=false;static$serverFile=false;protected$conn;protected$types=array();var$delimiter=";";var$insertFunctions=array();var$editFunctions=array();var$unsigned=array();var$fulltextOperator="AGAINST";var$functions=array();var$grouping=array();var$onActions="RESTRICT|NO ACTION|CASCADE|SET NULL|SET DEFAULT";var$partitionBy=array();var$inout="IN|OUT|INOUT";var$enumLength="'(?:''|[^'\\\\]|\\\\.)*'";var$generated=array();var$primary="";var$query="";static
function
jushModule(){return"";}static
function
jushAutocomplete(array$S,$Lk){$jl=array();foreach($S
as$Q=>$O){if(!$O["dependent"])$jl[$Q]=array();}foreach(driver()->allFields()as$Q=>$n){foreach($n
as$m)$jl[$Q][]=$m["field"];}return"jush.autocompleteSql('".idf_escape("")."', ".json_encode($jl).", ".json_encode($Lk).")";}static
function
connect($M,$U,$E){if(static::$serverFile)$wi=server_parts(array("path"=>$M));else{$wi=parse_server($M);if(!$wi||($wi["scheme"]&&!in_array($wi["scheme"],static::$serverSchemes))||($wi["socket"]&&!static::$serverSocket)||($wi["path"]&&!static::$serverPath)||(substr($wi["host"],0,1)=="/"&&!static::$serverSocket))return'Invalid server.';if($wi["port"]!=""&&($wi["port"]<1024||$wi["port"]>65535))return'Connecting to privileged ports is not allowed.';}$f=new
Db;return($f->attach($wi,$U,$E)?:$f);}function
__construct(Db$f){$this->conn=$f;}function
types(){return
call_user_func_array('array_merge',array_values($this->types));}function
structuredTypes(){return
array_map('array_keys',$this->types);}function
enumLength(array$m){}function
unconvertFunction(array$m){}function
select($Q,array$L,array$Z,array$r,array$Rh=array(),$z=1,$D=0,$Yi=false){$yf=(count($r)<count($L));$F=adminer()->selectQueryBuild($L,$Z,$r,$Rh,$z,$D);if(!$F)$F="SELECT".limit(($_GET["page"]!="last"&&$z&&$r&&$yf&&JUSH=="sql"?"SQL_CALC_FOUND_ROWS ":"").implode(", ",$L)."\nFROM ".table($Q),($Z?"\nWHERE ".implode(" AND ",$Z):"").($r&&$yf?"\nGROUP BY ".implode(", ",$r):"").($Rh?"\nORDER BY ".implode(", ",$Rh):""),$z,($D?$z*$D:0),"\n");$this->query=$F;$Kk=microtime(true);$H=$this->conn->query($F,(!$z&&!$Yi?1:0));if($Yi)echo
adminer()->selectQuery($F,$Kk,!$H);return$H;}function
delete($Q,$hj,$z=0){$F="FROM ".table($Q);return
queries("DELETE".($z?limit1($Q,$F,$hj):" $F$hj"));}function
update($Q,array$N,$hj,$z=0,$ak="\n"){$Y=array();foreach($N
as$x=>$W)$Y[]="$x = $W";$F=table($Q)." SET$ak".implode(",$ak",$Y);return
queries("UPDATE".($z?limit1($Q,$F,$hj,$ak):" $F$hj"));}function
insert($Q,array$N){return
queries("INSERT INTO ".table($Q).($N?" (".implode(", ",array_keys($N)).")\nVALUES (".implode(", ",$N).")":" DEFAULT VALUES").$this->insertReturning($Q));}function
insertReturning($Q){return"";}function
insertUpdate($Q,array$J,array$Wi){foreach($J
as$N){$Z=array();foreach($N
as$x=>$W){if(isset($Wi[idf_unescape($x)]))$Z[]="$x = $W";}if(!($Z&&$this->update($Q,$N," WHERE ".implode(" AND ",$Z))&&$this->conn->affected_rows)&&!$this->insert($Q,$N))return
false;}return
true;}function
begin(){return
queries("BEGIN");}function
commit(){return
queries("COMMIT");}function
rollback(){return
queries("ROLLBACK");}function
slowQuery($F,$xl){}function
operators($Xk){return
array();}function
convertSearch($u,array$W,array$m){return$u;}function
value($W,array$m){return(method_exists($this->conn,'value')?$this->conn->value($W,$m):$W);}function
quoteBinary($Lj){return
q($Lj);}function
typeName(\stdClass$m){return(isset($m->native_type)?$m->native_type:"");}function
warnings(){}function
tableHelp($B,$Bf=false){}function
inheritsFrom($Q){return
array();}function
inheritedTables($Q){return
array();}function
partitionsInfo($Q){return
array();}function
hasCStyleEscapes(){return
false;}function
lineComment(){return"--";}function
engines(){return
array();}function
supportsIndex(array$R){return!is_view($R);}function
supportsAlterIndex(array$R){return
true;}function
supportsAlterTable(array$Xk){return
true;}function
indexAlgorithms(array$Xk){return
array();}function
indexOpclasses(){return
array();}function
shadowTables($Q){return
array();}function
fulltextSql($B,array$v,$F,$Wa){return"MATCH (".implode(", ",array_map('Adminer\idf_escape',$v["columns"])).") AGAINST (".q($F).($Wa?" IN BOOLEAN MODE":"").")";}function
checkConstraints($Q){return
get_key_vals("SELECT c.CONSTRAINT_NAME, CHECK_CLAUSE
FROM INFORMATION_SCHEMA.CHECK_CONSTRAINTS c
JOIN INFORMATION_SCHEMA.TABLE_CONSTRAINTS t
	ON c.CONSTRAINT_SCHEMA = t.CONSTRAINT_SCHEMA AND c.CONSTRAINT_NAME = t.CONSTRAINT_NAME".($this->conn->flavor=='maria'?" AND c.TABLE_NAME = ".q($Q):"")."
WHERE c.CONSTRAINT_SCHEMA = ".q($_GET["ns"]!=""?$_GET["ns"]:DB)."
AND t.TABLE_NAME = ".q($Q).(JUSH=="pgsql"?"
AND CHECK_CLAUSE NOT LIKE '% IS NOT NULL'":""),$this->conn);}function
allFields(){$H=array();if(DB!=""){foreach(get_rows("SELECT c.TABLE_NAME AS tab, c.COLUMN_NAME AS field, c.IS_NULLABLE AS nullable,
	c.DATA_TYPE AS type, c.CHARACTER_MAXIMUM_LENGTH AS length,
	".(JUSH=='sql'?"c.COLUMN_KEY = 'PRI'":"k.COLUMN_NAME")." AS ".idf_escape("primary")."
FROM INFORMATION_SCHEMA.COLUMNS c".(JUSH=='sql'?"":"
LEFT JOIN INFORMATION_SCHEMA.TABLE_CONSTRAINTS t ON c.TABLE_SCHEMA = t.TABLE_SCHEMA AND c.TABLE_NAME = t.TABLE_NAME AND t.CONSTRAINT_TYPE = 'PRIMARY KEY'
LEFT JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE k
	ON t.CONSTRAINT_SCHEMA = k.CONSTRAINT_SCHEMA AND t.CONSTRAINT_NAME = k.CONSTRAINT_NAME AND c.TABLE_SCHEMA = k.TABLE_SCHEMA AND c.TABLE_NAME = k.TABLE_NAME AND c.COLUMN_NAME = k.COLUMN_NAME")."
WHERE c.TABLE_SCHEMA = ".q($_GET["ns"]!=""?$_GET["ns"]:DB)."
ORDER BY c.TABLE_NAME, c.ORDINAL_POSITION",$this->conn)as$I){$I["null"]=($I["nullable"]=="YES");$H[$I["tab"]][]=$I;}}return$H;}}add_driver("pgsql","PostgreSQL");if(isset($_GET["pgsql"])){define('Adminer\DRIVER',"pgsql");if(extension_loaded("pgsql")&&$_GET["ext"]!="pdo"){class
PgsqlDb
extends
SqlDb{var$extension="PgSQL";var$timeout=0;private$link,$string,$database=true;function
_error($dd,$l){if(ini_bool("html_errors"))$l=html_entity_decode(strip_tags($l));$l=preg_replace('~^[^:]*: ~','',$l);$this->error=$l;}function
attach(array$M,$U,$E){$j=adminer()->database();set_error_handler(array($this,'_error'));$Ji=$M["port"];$Le=($M["host"]?:$M["socket"]);$this->string="host='$Le'".($Ji?" port=$Ji":"")." user='".addcslashes($U,"'\\")."' password='".addcslashes($E,"'\\")."'";$Jk=adminer()->connectSsl();if(isset($Jk["mode"]))$this->string
.=" sslmode=$Jk[mode]";$this->link=@pg_connect("$this->string dbname='".($j!=""?addcslashes($j,"'\\"):"postgres")."'",PGSQL_CONNECT_FORCE_NEW);if(!$this->link&&$j!=""){$this->database=false;$this->link=@pg_connect("$this->string dbname='postgres'",PGSQL_CONNECT_FORCE_NEW);}restore_error_handler();if($this->link)pg_set_client_encoding($this->link,"UTF8");return($this->link?'':$this->error);}function
quote($P){return(function_exists('pg_escape_literal')?pg_escape_literal($this->link,$P):"'".pg_escape_string($this->link,$P)."'");}function
value($W,array$m){return($m["type"]=="bytea"&&$W!==null?pg_unescape_bytea($W):$W);}function
select_db($gc){if($gc==adminer()->database())return$this->database;$H=@pg_connect("$this->string dbname='".addcslashes($gc,"'\\")."'",PGSQL_CONNECT_FORCE_NEW);if($H)$this->link=$H;return$H;}function
close(){$this->link=@pg_connect("$this->string dbname='postgres'");}function
query($F,$Yl=false){if(self::$untrusted)$G=(@pg_query($this->link,"BEGIN READ ONLY")?@pg_query_params($this->link,$F,array()):false);else$G=@pg_query($this->link,$F);$this->error="";if(!$G){$this->error=pg_last_error($this->link);$H=false;}elseif(!pg_num_fields($G)){$this->affected_rows=pg_affected_rows($G);$H=true;}else$H=new
Result($G);if(self::$untrusted)@pg_query($this->link,"COMMIT");if($this->timeout){$this->timeout=0;$this->query("RESET statement_timeout");}return$H;}function
warnings(){if(PHP_VERSION_ID>=70100){$H=implode("\n",pg_last_notice($this->link,PGSQL_NOTICE_ALL));pg_last_notice($this->link,PGSQL_NOTICE_CLEAR);}else$H=pg_last_notice($this->link);return
nl_br(h($H));}function
inTransaction(){$O=pg_transaction_status($this->link);return$O==PGSQL_TRANSACTION_INTRANS||$O==PGSQL_TRANSACTION_INERROR;}function
copyFrom($Q,array$J){$this->error='';set_error_handler(function($dd,$l){$this->error=(ini_bool('html_errors')?html_entity_decode($l):$l);return
true;});$H=pg_copy_from($this->link,$Q,$J);restore_error_handler();return$H;}}class
Result{var$num_rows;private$result,$offset=0;function
__construct($G){$this->result=$G;$this->num_rows=pg_num_rows($G);}function
fetch_assoc(){return
pg_fetch_assoc($this->result);}function
fetch_row(){return
pg_fetch_row($this->result);}function
fetch_field(){$d=$this->offset++;$H=new
\stdClass;$H->orgtable=pg_field_table($this->result,$d);$H->name=pg_field_name($this->result,$d);$T=pg_field_type($this->result,$d);$H->native_type=$T;$H->type=(preg_match(number_type(),$T)?0:15);$H->charsetnr=($T=="bytea"?63:0);return$H;}}}elseif(extension_loaded("pdo_pgsql")){class
PgsqlDb
extends
PdoDb{var$extension="PDO_PgSQL";var$timeout=0;function
attach(array$M,$U,$E){$j=adminer()->database();$Ji=$M["port"];$Le=($M["host"]?:$M["socket"]);$Pc="pgsql:host='$Le'".($Ji?" port=$Ji":"")." client_encoding=utf8 dbname='".($j!=""?addcslashes($j,"'\\"):"postgres")."'";$Jk=adminer()->connectSsl();if(isset($Jk["mode"]))$Pc
.=" sslmode=$Jk[mode]";return$this->dsn($Pc,$U,$E);}function
select_db($gc){return(adminer()->database()==$gc);}function
query($F,$Yl=false){$H=(self::$untrusted?$this->readOnlyQuery($F):parent::query($F,$Yl));if($this->timeout){$this->timeout=0;parent::query("RESET statement_timeout");}return$H;}private
function
readOnlyQuery($F){$this->error="";if(!$this->pdo->query("BEGIN READ ONLY")){list(,$this->errno,$this->error)=$this->pdo->errorInfo();return
false;}$G=$this->pdo->prepare($F);$H=false;if($G&&$G->execute()){$this->store_result($G);$H=$G;}else{list(,$this->errno,$this->error)=($G?$G->errorInfo():$this->pdo->errorInfo());if(!$this->error)$this->error='Unknown error.';}$this->pdo->query("COMMIT");return$H;}function
warnings(){}function
copyFrom($Q,array$J){$H=$this->pdo->pgsqlCopyFromArray($Q,$J);$this->error=idx($this->pdo->errorInfo(),2)?:'';return$H;}function
close(){}}}if(class_exists('Adminer\PgsqlDb')){class
Db
extends
PgsqlDb{function
multi_query($F){if(preg_match('~\bCOPY\s+(.+?)\s+FROM\s+stdin;\n?(.*)\n\\\\\.$~is',str_replace("\r\n","\n",$F),$A)){$J=explode("\n",$A[2]);$this->multi=false;$this->affected_rows=count($J);return$this->copyFrom($A[1],$J);}return
parent::multi_query($F);}}}class
Driver
extends
SqlDriver{static$extensions=array("PgSQL","PDO_PgSQL");static$jush="pgsql";static$serverSocket=true;var$functions=array("char_length","lower","round","to_hex","to_timestamp","upper");var$grouping=array("avg","count","count distinct","max","min","sum");var$nsOid="(SELECT oid FROM pg_namespace WHERE nspname = current_schema())";private$userTypes=array();function
operators($Xk){return
array("=","<",">","<=",">=","!=","~","~*","!~","LIKE","LIKE %%","ILIKE","ILIKE %%","IN","IS NULL","NOT LIKE","NOT ILIKE","NOT IN","IS NOT NULL","SQL");}static
function
connect($M,$U,$E){$f=parent::connect($M,$U,$E);if(is_string($f))return$f;$Cm=get_val("SELECT version()",0,$f);$f->flavor=(preg_match('~CockroachDB~',$Cm)?'cockroach':'');$f->server_info=preg_replace('~^\D*([\d.]+[-\w]*).*~','\1',$Cm);if(min_version(9,0,$f))$f->query("SET application_name = 'Adminer'");if($f->flavor=='cockroach')add_driver(DRIVER,"CockroachDB");return$f;}function
__construct(Db$f){parent::__construct($f);$this->types=array('Numbers'=>array("smallint"=>5,"integer"=>10,"bigint"=>19,"boolean"=>1,"numeric"=>0,"real"=>7,"double precision"=>16,"money"=>20),'Date and time'=>array("date"=>13,"time"=>17,"timestamp"=>20,"timestamptz"=>21,"interval"=>0),'Strings'=>array("character"=>0,"character varying"=>0,"text"=>0,"tsquery"=>0,"tsvector"=>0,"uuid"=>0,"xml"=>0),'Binary'=>array("bit"=>0,"bit varying"=>0,"bytea"=>0),'Network'=>array("cidr"=>43,"inet"=>43,"macaddr"=>17,"macaddr8"=>23,"txid_snapshot"=>0),'Geometry'=>array("box"=>0,"circle"=>0,"line"=>0,"lseg"=>0,"path"=>0,"point"=>0,"polygon"=>0),);if(min_version(9.2,0,$f)){$this->types['Strings']["json"]=4294967295;$this->types['Ranges']=array("int4range"=>0,"int8range"=>0,"numrange"=>0,"daterange"=>0,"tsrange"=>0,"tstzrange"=>0);if(min_version(9.4,0,$f))$this->types['Strings']["jsonb"]=4294967295;}$this->insertFunctions=array("char"=>"md5","date|time"=>"now",);$this->editFunctions=array(number_type()=>"+/-","date|time"=>"+ interval/- interval","char|text"=>"||",);if(min_version(12,0,$f)){$this->generated[]="STORED";if(min_version(18,0,$f))$this->generated[]="VIRTUAL";}$this->partitionBy=array("RANGE","LIST");if(!$f->flavor)$this->partitionBy[]="HASH";}function
enumLength(array$m){$zh=$this->userTypes[$m["type"]];return($zh?type_values($zh):"");}function
setUserTypes(array$Xl){$this->userTypes=array_flip($Xl);$this->types['User types']=array_fill_keys(array_keys($this->userTypes),0);}function
insertReturning($Q){$Ga=array_filter(fields($Q),function($m){return$m['auto_increment'];});return(count($Ga)==1?" RETURNING ".idf_escape(key($Ga)):"");}function
insertUpdate($Q,array$J,array$Wi){$e=array_keys(reset($J));$Gb=array();$im=array();foreach($e
as$x){if(isset($Wi[idf_unescape($x)]))$Gb[]=$x;else$im[]="$x = EXCLUDED.$x";}if(!$Gb||!min_version(9.5)||count($Gb)!=count($Wi))return
parent::insertUpdate($Q,$J,$Wi);$Ri="INSERT INTO ".table($Q)." (".implode(", ",$e).") VALUES\n";$Rk="\nON CONFLICT (".implode(", ",$Gb).")".($im?" DO UPDATE SET ".implode(", ",$im):" DO NOTHING");$Y=array();$y=0;foreach($J
as$N){$X="(".implode(", ",$N).")";if($Y&&strlen($Ri)+$y+strlen($X)+strlen($Rk)>1e6){if(!queries($Ri.implode(",\n",$Y).$Rk))return
false;$Y=array();$y=0;}$Y[]=$X;$y+=strlen($X)+2;}return
queries($Ri.implode(",\n",$Y).$Rk);}function
slowQuery($F,$xl){$this->conn->query("SET statement_timeout = ".(1000*$xl));$this->conn->timeout=1000*$xl;return$F;}function
convertSearch($u,array$W,array$m){$Bi=preg_match('(LIKE|^!?~)',$W["op"]);$ch=preg_match('~^(character( varying)?|text|citext|bpchar|name)$~',$m["type"])||(!$Bi&&preg_match('~'.number_type().'|^(date|time|timetz|timestamp|timestamptz|boolean)$~',$m["type"]));return($ch&&!preg_match('~\[]$~',$m["full_type"])?$u:"CAST($u AS text)");}function
quoteBinary($Lj){return"'\\x".bin2hex($Lj)."'";}function
warnings(){return$this->conn->warnings();}function
tableHelp($B,$Bf=false){$eg=array("information_schema"=>"infoschema","pg_catalog"=>($Bf?"view":"catalog"),);$_=$eg[$_GET["ns"]];if($_)return"$_-".str_replace("_","-",$B).".html";}function
inheritsFrom($Q){return
get_rows("SELECT relname AS table, nspname AS ns FROM pg_class JOIN pg_inherits ON inhparent = oid JOIN pg_namespace ON relnamespace = pg_namespace.oid WHERE inhrelid = ".$this->tableOid($Q)." ORDER BY 2, 1");}function
inheritedTables($Q){return
get_rows("SELECT relname AS table, nspname AS ns FROM pg_inherits JOIN pg_class ON inhrelid = oid JOIN pg_namespace ON relnamespace = pg_namespace.oid WHERE inhparent = ".$this->tableOid($Q)." ORDER BY 2, 1");}function
partitionsInfo($Q){$I=(min_version(10)?$this->conn->query("SELECT * FROM pg_partitioned_table WHERE partrelid = ".$this->tableOid($Q))->fetch_assoc():null);if($I){$c=get_vals("SELECT attname FROM pg_attribute WHERE attrelid = $I[partrelid] AND attnum IN (".str_replace(" ",", ",$I["partattrs"]).")");$Za=array('h'=>'HASH','l'=>'LIST','r'=>'RANGE');return
array("partition_by"=>$Za[$I["partstrat"]],"partition"=>implode(", ",array_map('Adminer\idf_escape',$c)),);}return
array();}function
tableOid($Q){return"(SELECT oid FROM pg_class WHERE relnamespace = $this->nsOid AND relname = ".q($Q)." AND relkind IN ('r', 'm', 'v', 'f', 'p'))";}function
allFields(){$H=array();$J=get_rows("SELECT c.relname AS tab, a.attname AS field, a.attnotnull::int,
	format_type(a.atttypid, a.atttypmod) AS full_type, i.indrelid AS primary
FROM pg_class c
JOIN pg_attribute a ON a.attrelid = c.oid AND a.attnum > 0 AND NOT a.attisdropped
LEFT JOIN pg_index i ON i.indrelid = c.oid AND i.indisprimary AND a.attnum = ANY(i.indkey)
WHERE c.relnamespace = $this->nsOid
AND c.relkind IN ('r', 'm', 'v', 'f', 'p')".(min_version(10)?"
AND c.relispartition IS NOT TRUE":"")."
ORDER BY c.relname, a.attnum",$this->conn);foreach($J
as$I){parse_full_type($I);$I["null"]=!$I["attnotnull"];$H[$I["tab"]][]=$I;}return$H;}function
indexAlgorithms(array$Xk){static$H=array();if(!$H)$H=get_vals("SELECT amname FROM pg_am".(min_version(9.6)?" WHERE amtype = 'i'":"")." ORDER BY amname = '".($this->conn->flavor=='cockroach'?"prefix":"btree")."' DESC, amname");return$H;}function
indexOpclasses(){static$H=array();if(!$H&&$this->conn->flavor!='cockroach')$H=get_vals("SELECT DISTINCT opcname FROM pg_catalog.pg_opclass WHERE NOT opcdefault ORDER BY opcname");return$H;}function
supportsIndex(array$R){return$R["Engine"]!="view";}function
hasCStyleEscapes(){static$bb;if($bb===null)$bb=(get_val("SHOW standard_conforming_strings",0,$this->conn)=="off");return$bb;}}function
idf_escape($u){return'"'.str_replace('"','""',$u).'"';}function
table($u){return
idf_escape($u);}function
get_databases($Qd){return
get_vals("SELECT datname FROM pg_database
WHERE datallowconn = TRUE AND has_database_privilege(datname, 'CONNECT')
ORDER BY datname");}function
limit($F,$Z,$z,$yh=0,$ak=" "){return" $F$Z".($z?$ak."LIMIT $z".($yh?" OFFSET $yh":""):"");}function
limit1($Q,$F,$Z,$ak="\n"){return(preg_match('~^INTO~',$F)?limit($F,$Z,1,0,$ak):" $F".(is_view(table_status1($Q))?$Z:$ak."WHERE (tableoid, ctid) = (SELECT tableoid, ctid FROM ".table($Q).$Z.$ak."LIMIT 1)"));}function
db_collation($j,array$wb){return
get_val("SELECT datcollate FROM pg_database WHERE datname = ".q($j));}function
logged_user(){return
get_val("SELECT user");}function
tables_list(){$F="SELECT table_name, table_type FROM information_schema.tables WHERE table_schema = current_schema()";if(support("materializedview"))$F
.="
UNION ALL
SELECT matviewname, 'MATERIALIZED VIEW'
FROM pg_matviews
WHERE schemaname = current_schema()";$F
.="
ORDER BY 1";return
get_key_vals($F);}function
count_tables(array$i){$H=array();foreach($i
as$j){if(connection()->select_db($j))$H[$j]=count(tables_list());}return$H;}function
table_status($B="",$yd=false){static$_e;if($_e===null)$_e=get_val("SELECT 'pg_table_size'::regproc");$ek=(!$yd&&min_version(10));$H=array();foreach(get_rows("SELECT
	relname AS \"Name\",
	CASE relkind WHEN 'v' THEN 'view' WHEN 'm' THEN 'materialized view' ELSE 'table' END AS \"Engine\"".($_e?",
	pg_table_size(c.oid) AS \"Data_length\",
	pg_indexes_size(c.oid) AS \"Index_length\"":"").",
	obj_description(c.oid, 'pg_class') AS \"Comment\",
	".(min_version(12)?"''":"CASE WHEN relhasoids THEN 'oid' ELSE '' END")." AS \"Oid\",
	reltuples AS \"Rows\",
	".($ek?"seq.last_value":"NULL")." AS \"Auto_increment\",
	".(min_version(10)?"relispartition::int AS dependent,":"")."
	current_schema() AS nspname
FROM pg_class c
".($ek?"LEFT JOIN (
	SELECT d.refobjid, max(s.last_value) AS last_value
	FROM pg_depend d
	JOIN pg_class sc ON sc.oid = d.objid AND sc.relkind = 'S' AND sc.relnamespace = ".driver()->nsOid."
	JOIN pg_sequences s ON s.schemaname = current_schema() AND s.sequencename = sc.relname
	WHERE d.classid = 'pg_class'::regclass AND d.refclassid = 'pg_class'::regclass AND d.deptype IN ('a', 'i')
	".($B!=""?"AND d.refobjid = ".driver()->tableOid($B):"")."
	GROUP BY d.refobjid
) seq ON seq.refobjid = c.oid
":"")."WHERE relkind IN ('r', 'm', 'v', 'f', 'p')
AND relnamespace = ".driver()->nsOid."
".($B!=""?"AND relname = ".q($B):"ORDER BY relname"))as$I)$H[$I["Name"]]=$I;return$H;}function
is_view(array$R){return
in_array($R["Engine"],array("view","materialized view"));}function
fk_support(array$R){return
true;}function
parse_full_type(array&$I){static$ua=array('timestamp without time zone'=>'timestamp','timestamp with time zone'=>'timestamptz','time without time zone'=>'time','time with time zone'=>'timetz',);preg_match('~([^([]+)(\((.*)\))?([a-z ]+)?((\[[0-9]*])*)$~',$I["full_type"],$A);list(,$T,$y,$I["length"],$ma,$Ba)=$A;$I["length"].=$Ba;$kb=$T.$ma;if(isset($ua[$kb])){$I["type"]=$ua[$kb];$I["full_type"]=$I["type"].$y.$Ba;}else{$I["type"]=$T;$I["full_type"]=$I["type"].$y.$ma.$Ba;}}function
fields($Q){$H=array();foreach(get_rows("SELECT
	a.attname AS field,
	format_type(a.atttypid, a.atttypmod) AS full_type,
	pg_get_expr(d.adbin, d.adrelid) AS default,
	a.attnotnull::int,
	i.indrelid AS primary,
	t.typcategory,
	col_description(a.attrelid, a.attnum) AS comment".(min_version(10)?",
	a.attidentity".(min_version(12)?",
	a.attgenerated":""):"")."
FROM pg_attribute a
JOIN pg_type t ON t.oid = a.atttypid
LEFT JOIN pg_attrdef d ON a.attrelid = d.adrelid AND a.attnum = d.adnum
LEFT JOIN pg_index i ON a.attrelid = i.indrelid AND a.attnum = ANY(i.indkey) AND i.indisprimary
WHERE a.attrelid = ".driver()->tableOid($Q)."
AND NOT a.attisdropped
AND a.attnum > 0
ORDER BY a.attnum")as$I){parse_full_type($I);if(in_array($I['attidentity'],array('a','d')))$I['default']='GENERATED '.($I['attidentity']=='d'?'BY DEFAULT':'ALWAYS').' AS IDENTITY';$I["generated"]=idx(array("s"=>"STORED","v"=>"VIRTUAL"),$I["attgenerated"],"");$I["composite"]=($I["typcategory"]=="C");$I["null"]=!$I["attnotnull"];$I["auto_increment"]=$I['attidentity']||preg_match('~^nextval\(~i',$I["default"])||preg_match('~^unique_rowid\(~',$I["default"]);$I["privileges"]=array("insert"=>1,"select"=>1,"update"=>1,"where"=>1,"order"=>1);if(!$I['generated']&&preg_match('~(.+)::[^,)]+(.*)~',$I["default"],$A))$I["default"]=($A[1]=="NULL"?null:idf_unescape($A[1]).$A[2]);$H[$I["field"]]=$I;}return$H;}function
indexes($Q,$g=null){$g=connection($g);$H=array();$cl=driver()->tableOid($Q);$e=get_key_vals("SELECT attnum, attname FROM pg_attribute WHERE attrelid = $cl AND attnum > 0",$g);foreach(get_rows("SELECT relname, indisunique::int, indisprimary::int, indkey, indoption, amname,
	pg_get_expr(indpred, indrelid, true) AS partial, pg_get_expr(indexprs, indrelid) AS indexpr".($g->flavor=='cockroach'?"":",
	(SELECT string_agg(CASE WHEN opcdefault THEN '' ELSE opcname END, ' ' ORDER BY s)
		FROM generate_subscripts(indclass, 1) AS s JOIN pg_catalog.pg_opclass ON pg_opclass.oid = indclass[s]) AS opclasses")."
FROM pg_index
JOIN pg_class ON indexrelid = oid
JOIN pg_am ON pg_am.oid = pg_class.relam
WHERE indrelid = $cl
ORDER BY indisprimary DESC, indisunique DESC",$g)as$I){$vj=$I["relname"];$H[$vj]["type"]=($I["indisprimary"]?"PRIMARY":($I["indisunique"]?"UNIQUE":"INDEX"));$H[$vj]["columns"]=array();$H[$vj]["descs"]=array();$H[$vj]["algorithm"]=$I["amname"];$H[$vj]["partial"]=$I["partial"];$ef=preg_split('~(?<=\)), (?=\()~',$I["indexpr"]);foreach(explode(" ",$I["indkey"])as$ff)$H[$vj]["columns"][]=($ff?$e[$ff]:array_shift($ef));foreach(explode(" ",$I["indoption"])as$gf)$H[$vj]["descs"][]=(intval($gf)&1?'1':null);$H[$vj]["opclasses"]=($I["opclasses"]!=""?explode(" ",$I["opclasses"]):array());$H[$vj]["lengths"]=array();}return$H;}function
foreign_keys($Q){$H=array();foreach(get_rows("SELECT conname, condeferrable::int AS deferrable, condeferred::int AS deferred, pg_get_constraintdef(oid) AS definition
FROM pg_constraint
WHERE conrelid = ".driver()->tableOid($Q)."
AND contype = 'f'::char
ORDER BY conkey, conname")as$I){$I['deferrable']=($I['deferrable']?'':'NOT ').'DEFERRABLE'.($I['deferred']?' INITIALLY DEFERRED':'');if(preg_match('~FOREIGN KEY\s*\((.+)\)\s*REFERENCES (.+)\((.+)\)(.*)$~iA',$I['definition'],$A)){$I['source']=array_map('Adminer\idf_unescape',array_map('trim',explode(',',$A[1])));if(preg_match('~^(("([^"]|"")+"|[^"]+)\.)?"?("([^"]|"")+"|[^"]+)$~',$A[2],$ng)){$I['ns']=idf_unescape($ng[2]);$I['table']=idf_unescape($ng[4]);}$I['target']=array_map('Adminer\idf_unescape',array_map('trim',explode(',',$A[3])));$I['on_delete']=(preg_match("~ON DELETE (".driver()->onActions.")~",$A[4],$ng)?$ng[1]:'NO ACTION');$I['on_update']=(preg_match("~ON UPDATE (".driver()->onActions.")~",$A[4],$ng)?$ng[1]:'NO ACTION');$H[$I['conname']]=$I;}}return$H;}function
view($B){return
array("select"=>trim(get_val("SELECT pg_get_viewdef(".driver()->tableOid($B).")")));}function
collations(){return
array();}function
information_schema($j,$K=""){return
in_array($K!=""?$K:get_schema(),array("information_schema","pg_catalog","pg_toast"));}function
error(){$H=h(connection()->error);if(preg_match('~^(.*\n)?([^\n]*)\n( *)\^(\n.*)?$~s',$H,$A))$H=$A[1].preg_replace('~((?:[^&]|&[^;]*;){'.strlen($A[3]).'})(.*)~','\1<b>\2</b>',$A[2]).$A[4];return
nl_br($H);}function
create_database($j,$vb){return
queries("CREATE DATABASE ".idf_escape($j).($vb?" ENCODING ".idf_escape($vb):""));}function
drop_databases(array$i){connection()->close();return
apply_queries("DROP DATABASE",$i,'Adminer\idf_escape');}function
rename_database($B,$vb){connection()->close();return!!queries("ALTER DATABASE ".idf_escape(DB)." RENAME TO ".idf_escape($B));}function
auto_increment(){return"";}function
alter_table($Q,$B,array$n,array$Sd,$Ab,$Yc,$vb,$Ga,$ti){$b=array();$gj=array();if($Q!=""&&$Q!=$B)$gj[]="ALTER TABLE ".table($Q)." RENAME TO ".table($B);$bk="";foreach($n
as$m){$d=idf_escape($m[0]);$W=$m[1];if(!$W)$b[]="DROP $d";else{$ym=$W[5];unset($W[5]);if($m[0]==""){if(isset($W[6]))$W[1]=($W[1]==" bigint"?" big":($W[1]==" smallint"?" small":" "))."serial";$b[]=($Q!=""?"ADD ":"  ").implode($W);if(isset($W[6]))$b[]=($Q!=""?"ADD":" ")." PRIMARY KEY ($W[0])";}else{if($d!=$W[0])$gj[]="ALTER TABLE ".table($B)." RENAME $d TO $W[0]";$b[]="ALTER $d TYPE$W[1]";$ck=$Q."_".idf_unescape($W[0])."_seq";$b[]="ALTER $d ".($W[3]?"SET".preg_replace('~GENERATED ALWAYS(.*) (STORED|VIRTUAL)~','EXPRESSION\1',$W[3]):(isset($W[6])?"SET DEFAULT nextval(".q($ck).")":"DROP DEFAULT"));if(isset($W[6]))$bk="CREATE SEQUENCE IF NOT EXISTS ".idf_escape($ck)." OWNED BY ".idf_escape($Q).".$W[0]";$b[]="ALTER $d ".($W[2]==" NULL"?"DROP NOT":"SET").$W[2];}if($m[0]!=""||$ym!="")$gj[]="COMMENT ON COLUMN ".table($B).".$W[0] IS ".($ym!=""?substr($ym,9):"''");}}$b=array_merge($b,$Sd);if($Q==""){$O="";if($ti){$rb=(connection()->flavor=='cockroach');$O=" PARTITION BY $ti[partition_by]($ti[partition])";if($ti["partition_by"]=='HASH'){$ui=+$ti["partitions"];for($s=0;$s<$ui;$s++)$gj[]="CREATE TABLE ".idf_escape($B."_$s")." PARTITION OF ".idf_escape($B)." FOR VALUES WITH (MODULUS $ui, REMAINDER $s)";}else{$Ti="MINVALUE";foreach($ti["partition_names"]as$s=>$W){$X=$ti["partition_values"][$s];$pi=" VALUES ".($ti["partition_by"]=='LIST'?"IN ($X)":"FROM ($Ti) TO ($X)");if($rb)$O
.=($s?",":" (")."\n  PARTITION ".(preg_match('~^DEFAULT$~i',$W)?$W:idf_escape($W))."$pi";else$gj[]="CREATE TABLE ".idf_escape($B."_$W")." PARTITION OF ".idf_escape($B)." FOR$pi";$Ti=$X;}$O
.=($rb?"\n)":"");}}array_unshift($gj,"CREATE TABLE ".table($B)." (\n".implode(",\n",$b)."\n)$O");}elseif($b)array_unshift($gj,"ALTER TABLE ".table($Q)."\n".implode(",\n",$b));if($bk)array_unshift($gj,$bk);if($Ab!==null)$gj[]="COMMENT ON TABLE ".table($B)." IS ".q($Ab);foreach($gj
as$F){if(!queries($F))return
false;}if($Ga!=""){foreach(fields($B)as$Ad=>$m){if($m["auto_increment"])return!!queries("SELECT setval(pg_get_serial_sequence(".q(table($B)).", ".q($Ad)."), $Ga)");}}return
true;}function
alter_indexes($Q,$b){$h=array();$Kc=array();$gj=array();foreach($b
as$W){if($W[0]!="INDEX")$h[]=($W[2]=="DROP"?"\nDROP CONSTRAINT ".idf_escape($W[1]):"\nADD".($W[1]!=""?" CONSTRAINT ".idf_escape($W[1]):"")." $W[0] ".($W[0]=="PRIMARY"?"KEY ":"")."(".implode(", ",$W[2]).")");elseif($W[2]=="DROP")$Kc[]=idf_escape($W[1]);else$gj[]="CREATE INDEX ".idf_escape($W[1]!=""?$W[1]:uniqid($Q."_"))." ON ".table($Q).($W[3]?" USING $W[3]":"")." (".implode(", ",$W[2]).")".($W[4]?" WHERE $W[4]":"");}if($h)array_unshift($gj,"ALTER TABLE ".table($Q).implode(",",$h));if($Kc)array_unshift($gj,"DROP INDEX ".implode(", ",$Kc));foreach($gj
as$F){if(!queries($F))return
false;}return
true;}function
truncate_tables(array$S){return!!queries("TRUNCATE ".implode(", ",array_map('Adminer\table',$S)));}function
drop_kinds(array$S){$H=array("MATERIALIZED VIEW"=>array(),"VIEW"=>array(),"TABLE"=>array());foreach($S
as$B=>$R)$H[strtoupper($R["Engine"])][]=idf_escape($R["nspname"]).".".table($B);return
array_filter($H);}function
drop_views(array$Em){return
drop_tables($Em);}function
drop_tables(array$S){$Mk=array();foreach($S
as$Q)$Mk[$Q]=table_status1($Q);foreach(drop_kinds($Mk)as$Lf=>$bh){if(!queries("DROP $Lf ".implode(", ",$bh)))return
false;}return
true;}function
move_tables(array$S,array$Em,$nl){foreach(array_merge($S,$Em)as$Q){$O=table_status1($Q);if(!queries("ALTER ".strtoupper($O["Engine"])." ".table($Q)." SET SCHEMA ".idf_escape($nl)))return
false;}return
true;}function
trigger($B,$Q){if($B=="")return
array("Statement"=>"EXECUTE PROCEDURE ()");$e=array();$Z="WHERE trigger_schema = current_schema() AND event_object_table = ".q($Q)." AND trigger_name = ".q($B);foreach(get_rows("SELECT * FROM information_schema.triggered_update_columns $Z")as$I)$e[]=$I["event_object_column"];$H=array();foreach(get_rows('SELECT trigger_name AS "Trigger", action_timing AS "Timing", event_manipulation AS "Event", \'FOR EACH \' || action_orientation AS "Type", action_statement AS "Statement"
FROM information_schema.triggers'."
$Z
ORDER BY event_manipulation DESC")as$I){if($e&&$I["Event"]=="UPDATE")$I["Event"].=" OF";$I["Of"]=implode(", ",$e);if($H)$I["Event"].=" OR $H[Event]";$H=$I;}return$H;}function
triggers($Q){$H=array();foreach(get_rows("SELECT * FROM information_schema.triggers WHERE trigger_schema = current_schema() AND event_object_table = ".q($Q))as$I){$Nl=trigger($I["trigger_name"],$Q);$H[$Nl["Trigger"]]=array($Nl["Timing"],$Nl["Event"]);}return$H;}function
trigger_options(){return
array("Timing"=>array("BEFORE","AFTER"),"Event"=>array("INSERT","UPDATE","UPDATE OF","DELETE","INSERT OR UPDATE","INSERT OR UPDATE OF","DELETE OR INSERT","DELETE OR UPDATE","DELETE OR UPDATE OF","DELETE OR INSERT OR UPDATE","DELETE OR INSERT OR UPDATE OF",),"Type"=>array("FOR EACH ROW","FOR EACH STATEMENT"),);}function
routine($B,$T){$C=routine_options($T);$Xj=array_intersect_key(array("VOLATILITY"=>"CASE p.provolatile WHEN 'i' THEN 'IMMUTABLE' WHEN 's' THEN 'STABLE' ELSE 'VOLATILE' END","NULL_INPUT"=>"CASE WHEN p.proisstrict THEN 'RETURNS NULL ON NULL INPUT' ELSE 'CALLED ON NULL INPUT' END","SECURITY"=>"CASE WHEN p.prosecdef THEN 'SECURITY DEFINER' ELSE 'SECURITY INVOKER' END","PARALLEL"=>"CASE p.proparallel WHEN 's' THEN 'PARALLEL SAFE' WHEN 'r' THEN 'PARALLEL RESTRICTED' ELSE 'PARALLEL UNSAFE' END",),$C);foreach($Xj
as$x=>$L)$Xj[$x]="$L AS \"$x\"";$J=get_rows('SELECT r.routine_definition AS definition, LOWER(r.external_language) AS language, '.($Xj?implode(', ',$Xj).', ':'').'r.*
FROM information_schema.routines r
LEFT JOIN pg_catalog.pg_proc p ON p.oid::text = substring(r.specific_name, \'[0-9]+$\')
WHERE r.routine_schema = current_schema() AND r.specific_name = '.q($B));$H=idx($J,0,array());$H["options"]=array_intersect_key($H,$C);$H["returns"]=array("type"=>preg_replace('~^_(.*)~','\1[]',"$H[type_udt_name]"));$H["fields"]=get_rows("SELECT COALESCE(parameter_name, ordinal_position::text) AS field,
	CASE data_type WHEN 'USER-DEFINED' THEN udt_name WHEN 'ARRAY' THEN substr(udt_name, 2) || '[]' ELSE data_type END AS type,
	character_maximum_length AS length, parameter_mode AS inout
FROM information_schema.parameters
WHERE specific_schema = current_schema() AND specific_name = ".q($B)."
ORDER BY ordinal_position");return$H;}function
routines(){return
get_rows('SELECT specific_name AS "SPECIFIC_NAME", routine_type AS "ROUTINE_TYPE", routine_name AS "ROUTINE_NAME", type_udt_name AS "DTD_IDENTIFIER"
FROM information_schema.routines
WHERE routine_schema = current_schema()'.(connection()->flavor=='cockroach'?'':"
AND substring(specific_name, '[0-9]+\$')::oid NOT IN (SELECT objid FROM pg_catalog.pg_depend WHERE classid = 'pg_proc'::regclass AND deptype = 'e')").'
ORDER BY SPECIFIC_NAME');}function
routine_languages(){$H=array();foreach(get_vals("SELECT LOWER(lanname) FROM pg_catalog.pg_language")as$Qf)$H[$Qf]=(preg_match('~sql$~',$Qf)?"pgsql":"txt");return$H;}function
routine_options($Fj){$rb=(connection()->flavor=='cockroach');$Sj=($rb?array():array("SECURITY"=>array("SECURITY INVOKER","SECURITY DEFINER")));if($Fj=="PROCEDURE")return$Sj;return
array("VOLATILITY"=>array("VOLATILE","STABLE","IMMUTABLE"),"NULL_INPUT"=>array("CALLED ON NULL INPUT","RETURNS NULL ON NULL INPUT"),)+$Sj+($rb?array():array("PARALLEL"=>array("PARALLEL UNSAFE","PARALLEL RESTRICTED","PARALLEL SAFE"),));}function
routine_id($B,array$I){$H=array();foreach($I["fields"]as$m){$y=$m["length"];$H[]=$m["type"].($y?"($y)":"");}return
idf_escape($B)."(".implode(", ",$H).")";}function
last_id($G){$I=(is_object($G)?$G->fetch_row():array());return($I?$I[0]:0);}function
explain(Db$f,$F){return$f->query("EXPLAIN $F");}function
found_rows(array$R,array$Z){if(preg_match("~ rows=([0-9]+)~",get_val("EXPLAIN SELECT * FROM ".idf_escape($R["Name"]).($Z?" WHERE ".implode(" AND ",$Z):"")),$uj))return$uj[1];}function
types($ud=false){$rb=connection()->flavor=='cockroach';$Mf=($rb?"'e'":"'b','c','d','e'".(min_version(9.2)?",'r'":""));return
get_key_vals("SELECT t.oid, t.typname
FROM pg_type t
WHERE t.typnamespace = ".driver()->nsOid."
AND t.typtype IN ($Mf)".($rb?"
AND t.typelem = 0":"
AND (t.typrelid = 0 OR (SELECT c.relkind FROM pg_class c WHERE c.oid = t.typrelid) = 'c')"."
AND NOT EXISTS (SELECT 1 FROM pg_type e WHERE e.typarray = t.oid)".($ud?'':"
AND t.oid NOT IN (SELECT objid FROM pg_catalog.pg_depend WHERE classid = 'pg_type'::regclass AND deptype = 'e')"))."
ORDER BY t.typname");}function
type_values($t){$cd=get_vals("SELECT enumlabel FROM pg_enum WHERE enumtypid = $t ORDER BY enumsortorder");return($cd?"'".implode("', '",array_map('addslashes',$cd))."'":"");}function
collation_name($zh){return(min_version(9.1)?"(SELECT collname FROM pg_collation WHERE oid = $zh AND collname != 'default')":"NULL");}function
type_definition($t){$T=first(get_rows("SELECT typtype, typisdefined::int AS defined, typrelid FROM pg_type WHERE oid = $t"));$H=array("kind"=>($T?$T["typtype"]:""),"definition"=>"");if(!$T||!$T["defined"])return$H;switch($H["kind"]){case'e':$Y=get_vals("SELECT enumlabel FROM pg_enum WHERE enumtypid = $t ORDER BY enumsortorder");$H["definition"]="AS ENUM (".implode(", ",array_map('Adminer\q',$Y)).")";break;case'c':$e=array();foreach(get_rows("SELECT attname, format_type(atttypid, atttypmod) AS full_type, ".collation_name("attcollation")." AS collation
FROM pg_attribute
WHERE attrelid = $T[typrelid] AND attnum > 0 AND NOT attisdropped
ORDER BY attnum")as$I)$e[]=idf_escape($I["attname"])." $I[full_type]".($I["collation"]?" COLLATE ".idf_escape($I["collation"]):"");$H["definition"]="AS (\n\t".implode(",\n\t",$e)."\n)";break;case'd':$Hc=first(get_rows("SELECT format_type(typbasetype, typtypmod) AS base, typnotnull::int AS notnull, typdefault, ".collation_name("typcollation")." AS collation
FROM pg_type WHERE oid = $t"));$H["definition"]="AS $Hc[base]".($Hc["collation"]?" COLLATE ".idf_escape($Hc["collation"]):"").($Hc["typdefault"]!=""?" DEFAULT $Hc[typdefault]":"").($Hc["notnull"]?" NOT NULL":"");foreach(get_rows("SELECT conname, pg_get_constraintdef(oid) AS definition FROM pg_constraint WHERE contypid = $t AND contype != 'n' ORDER BY conname")as$I)$H["definition"].=" CONSTRAINT ".idf_escape($I["conname"])." $I[definition]";break;case'r':$kj=first(get_rows("SELECT format_type(rngsubtype, NULL) AS subtype,
(SELECT opcname FROM pg_opclass WHERE oid = rngsubopc) AS subtype_opclass,
".collation_name("rngcollation")." AS collation,
NULLIF(rngcanonical, 0)::regproc::text AS canonical,
NULLIF(rngsubdiff, 0)::regproc::text AS subtype_diff".(min_version(14)?",
(SELECT typname FROM pg_type WHERE oid = rngmultitypid) AS multirange_type_name":"")."
FROM pg_range WHERE rngtypid = $t"));$C=array();foreach(array("subtype"=>0,"subtype_opclass"=>1,"collation"=>1,"canonical"=>0,"subtype_diff"=>0,"multirange_type_name"=>1)as$x=>$gd){if($kj[$x]!="")$C[]=strtoupper($x)." = ".($gd?idf_escape($kj[$x]):$kj[$x]);}$H["definition"]="AS RANGE (".implode(", ",$C).")";}return$H;}function
schemas(){return
get_vals("SELECT nspname FROM pg_namespace ORDER BY nspname");}function
get_schema(){return(string)get_val("SELECT current_schema()");}function
set_schema($K,$g=null){$H=connection($g)->query("SET search_path TO ".idf_escape($K));driver()->setUserTypes(types(true));return!!$H;}function
drop_sql(array$S){$H="";foreach(drop_kinds($S)as$Lf=>$bh)$H
.="DROP $Lf IF EXISTS ".implode(", ",$bh).";\n";return($H?"$H\n":"");}function
foreign_keys_sql($Q){$H="";$O=table_status1($Q);$oh=idf_escape($O['nspname']);$Od=foreign_keys($Q);ksort($Od);foreach($Od
as$Nd=>$Md)$H
.="ALTER TABLE ONLY $oh.".idf_escape($O['Name'])." ADD CONSTRAINT ".idf_escape($Nd)." ".preg_replace('~( REFERENCES )([^(.]+\()~',"\\1$oh.\\2",$Md["definition"]).";\n";return($H?"$H\n":$H);}function
indexes_sql($Q,$Wi=""){$H="";$F="SELECT indexdef FROM pg_catalog.pg_indexes WHERE schemaname = current_schema() AND tablename = ".q($Q).($Wi!=""?" AND indexname != ".q($Wi):"");foreach(get_rows($F,null,"-- ")as$I)$H
.="\n\n$I[indexdef];";return$H;}function
create_sql($Q,$Ga,$Pk){$Aj=array();$ek=array();$fk=array();$dk=array();$O=table_status1($Q);$oh=idf_escape($O['nspname']);if(is_view($O)){$Dm=view($Q);$h="CREATE ".strtoupper($O["Engine"])." $oh.".idf_escape($Q)." AS ".rtrim($Dm["select"],";").";";return
rtrim($h.indexes_sql($Q),';');}$n=fields($Q);if(count($O)<2||empty($n))return"";$H="CREATE TABLE $oh.".idf_escape($O['Name'])." (\n    ";$al=q("$oh.".idf_escape($O['Name']));foreach($n
as$m){$gk="";if($m['default']=="nextval('$O[Name]_$m[field]_seq')"){$gk="$oh.".idf_escape("$O[Name]_$m[field]_seq");$m['default']=null;$m['full_type']=preg_replace('~int(eger)?~','serial',$m['full_type']);}$ni=idf_escape($m['field']).' '.$m['full_type'].preg_replace('~(nextval\(\')([^.\']+\')~','\1'.str_replace("'","''",$O['nspname']).'.\2',default_value($m)).($m['null']?"":" NOT NULL");$Aj[]=$ni;if(preg_match('~nextval\(\'([^\']+)\'\)~',$m['default'],$og)){$ck=$og[1];$Ck=first(get_rows((min_version(10)?"SELECT *, cache_size AS cache_value FROM pg_sequences WHERE schemaname = current_schema() AND sequencename = ".q(idf_unescape($ck)):"SELECT * FROM $ck"),null,"-- "));$ek[]=($Pk=="DROP+CREATE"?"DROP SEQUENCE IF EXISTS $oh.$ck;\n":"")."CREATE SEQUENCE $oh.$ck INCREMENT $Ck[increment_by] MINVALUE $Ck[min_value] MAXVALUE $Ck[max_value]"." CACHE $Ck[cache_value];";if(get_val("SELECT pg_get_serial_sequence($al, ".q($m['field']).")"))$fk[]="\n\nALTER SEQUENCE $oh.$ck OWNED BY $oh.".idf_escape($O['Name']).".".idf_escape($m['field']).";";if($Ga)$dk[]="$oh.$ck";}elseif($Ga&&$m['auto_increment'])$dk[]=($gk?:get_val("SELECT pg_get_serial_sequence($al, ".q($m['field']).")"));}if(!empty($ek))$H=implode("\n\n",$ek)."\n\n$H";$Wi="";foreach(indexes($Q)as$cf=>$v){if($v['type']=='PRIMARY'){$Wi=$cf;$Aj[]="CONSTRAINT ".idf_escape($cf)." PRIMARY KEY (".implode(', ',array_map('Adminer\idf_escape',$v['columns'])).")";}}foreach(driver()->checkConstraints($Q)as$Ib=>$Kb)$Aj[]="CONSTRAINT ".idf_escape($Ib)." CHECK ($Kb)";$H
.=implode(",\n    ",$Aj)."\n)";$pi=driver()->partitionsInfo($O['Name']);if($pi)$H
.="\nPARTITION BY $pi[partition_by]($pi[partition])";$H
.="\nWITH (oids = ".($O['Oid']?'true':'false').");";$H
.=implode($fk);if($O['Comment'])$H
.="\n\nCOMMENT ON TABLE $oh.".idf_escape($O['Name'])." IS ".q($O['Comment']).";";foreach($n
as$Ad=>$m){if($m['comment'])$H
.="\n\nCOMMENT ON COLUMN $oh.".idf_escape($O['Name']).".".idf_escape($Ad)." IS ".q($m['comment']).";";}$H
.=indexes_sql($Q,$Wi);foreach(array_filter($dk)as$bk){$Ck=first(get_rows("SELECT last_value, is_called::int FROM $bk",null,"-- "));if($Ck['is_called'])$H
.="\n\nDO \$\$ BEGIN PERFORM setval(".q($bk).", $Ck[last_value]); END \$\$;";}return
rtrim($H,';');}function
truncate_sql($Q){return"TRUNCATE ".table($Q);}function
truncate_all_sql(array$S){return($S?"TRUNCATE ".implode(", ",array_map('Adminer\table',$S)).";\n\n":"");}function
trigger_sql($Q){$O=table_status1($Q);$H="";foreach(triggers($Q)as$Ml=>$Ll){$Nl=trigger($Ml,$O['Name']);$H
.="\nCREATE TRIGGER ".idf_escape($Nl['Trigger'])." $Nl[Timing] $Nl[Event] ON ".idf_escape($O["nspname"]).".".idf_escape($O['Name'])." $Nl[Type] $Nl[Statement];;\n";}return$H;}function
use_sql($gc,$Pk=""){$B=idf_escape($gc);$H="";if(preg_match('~CREATE~',$Pk)){if($Pk=="DROP+CREATE")$H="DROP DATABASE IF EXISTS $B;\n";$H
.="CREATE DATABASE $B;\n";}return"$H\\connect $B";}function
show_variables(){return
get_rows("SHOW ALL");}function
process_list(){return
get_rows("SELECT * FROM pg_stat_activity ORDER BY ".(min_version(9.2)?"pid":"procpid"));}function
convert_field(array$m){}function
unconvert_field(array$m,$H){return($m["composite"]?"$H::$m[type]":$H);}function
support($zd){return
preg_match('~^(check|columns|comment|database|drop_col|dump|descidx|fast_status|indexes|kill|partial_indexes|routine|scheme|sequence|sql|table'.'|transaction_ddl|trigger|type|variables|view'.(min_version(9.3)?'|materializedview':'').(min_version(11)?'|procedure':'').(connection()->flavor=='cockroach'?'':'|deferrable').(connection()->flavor=='cockroach'?'':'|processlist').')$~',$zd);}function
kill_process($t){return
queries("SELECT pg_terminate_backend(".number($t).")");}function
connection_id(){return"SELECT pg_backend_pid()";}function
max_connections(){return
get_val("SHOW max_connections");}}add_driver("sqlite","SQLite");if(isset($_GET["sqlite"])){define('Adminer\DRIVER',"sqlite");if(class_exists("SQLite3")&&$_GET["ext"]!="pdo"){abstract
class
SqliteDb
extends
SqlDb{var$extension="SQLite3";private$link;function
attach(array$M,$U,$E){$this->link=new
\SQLite3($M["path"]);$Cm=\SQLite3::version();$this->server_info=$Cm["versionString"];return'';}function
query($F,$Yl=false){$G=@$this->link->query($F);$this->error="";if(!$G){$this->errno=$this->link->lastErrorCode();$this->error=$this->link->lastErrorMsg();return
false;}elseif($G->numColumns())return
new
Result($G);$this->affected_rows=$this->link->changes();return
true;}function
quote($P){return(is_utf8($P)?"'".$this->link->escapeString($P)."'":"x'".bin2hex($P)."'");}}class
Result{var$num_rows;private$result,$offset=0;function
__construct($G){$this->result=$G;}function
fetch_assoc(){return$this->result->fetchArray(SQLITE3_ASSOC);}function
fetch_row(){return$this->result->fetchArray(SQLITE3_NUM);}function
fetch_field(){$Xl=array(1=>"integer","real","text","blob","null");$d=$this->offset++;$T=$this->result->columnType($d);return(object)array("name"=>$this->result->columnName($d),"type"=>($T==SQLITE3_TEXT?15:0),"native_type"=>$Xl[$T],"charsetnr"=>($T==SQLITE3_BLOB?63:0),);}}}elseif(extension_loaded("pdo_sqlite")){abstract
class
SqliteDb
extends
PdoDb{var$extension="PDO_SQLite";function
attach(array$M,$U,$E){return$this->dsn(DRIVER.":".$M["path"],"","");}function
quote($P){return(is_utf8($P)?parent::quote($P):"x'".bin2hex($P)."'");}}}if(class_exists('Adminer\SqliteDb')){class
Db
extends
SqliteDb{function
attach(array$M,$U,$E){parent::attach($M,$U,$E);$this->query("PRAGMA foreign_keys = 1");$this->query("PRAGMA busy_timeout = 500");return'';}function
select_db($o){$F="ATTACH ".$this->quote(preg_match("~(^[/\\\\]|:)~",$o)?$o:dirname($_SERVER["SCRIPT_FILENAME"])."/$o")." AS a";if(is_readable($o)&&$this->query($F))return!self::attach(server_parts(array("path"=>$o)),'','');return
false;}}}class
Driver
extends
SqlDriver{static$extensions=array("SQLite3","PDO_SQLite");static$jush="sqlite";static$passwords=false;static$serverFile=true;protected$types=array(array("integer"=>0,"real"=>0,"numeric"=>0,"text"=>0,"blob"=>0));var$insertFunctions=array();var$editFunctions=array("integer|real|numeric"=>"+/-","text"=>"||",);var$fulltextOperator="MATCH";var$functions=array("hex","length","lower","round","unixepoch","upper");var$grouping=array("avg","count","count distinct","group_concat","max","min","sum");function
operators($Xk){$H=array("=","<",">","<=",">=","!=","LIKE","LIKE %%","IN","IS NULL","NOT LIKE","NOT IN","IS NOT NULL");if(preg_match('~^fts\d+$~i',(string)idx($Xk,"Engine")))$H[]="MATCH";$H[]="SQL";return$H;}static
function
connect($M,$U,$E){return
parent::connect(":memory:","","");}function
__construct(Db$f){parent::__construct($f);if(min_version(3.31,0,$f))$this->generated=array("STORED","VIRTUAL");if(min_version(3.37,0,$f))$this->types[0]["any"]=0;}function
structuredTypes(){return
array_keys($this->types[0]);}function
quoteBinary($Lj){return"x".q(bin2hex($Lj));}function
engines(){$H=array("table");if(min_version("3.8.2")){if(min_version(3.37)){$H[]="STRICT";$H[]="STRICT, WITHOUT ROWID";}$H[]="WITHOUT ROWID";}return$H;}private
function
isVirtual(array$R){$Yc=$R["Engine"];return$Yc!=""&&!in_array($Yc,array_merge(array("view"),$this->engines()));}function
supportsIndex(array$R){return!is_view($R)&&!$this->isVirtual($R);}function
supportsAlterIndex(array$R){return$this->supportsIndex($R);}function
supportsAlterTable(array$Xk){return!$this->isVirtual($Xk);}function
shadowTables($Q){$H=array();if(min_version(3.37)){foreach(get_vals("SELECT name FROM pragma_table_list WHERE schema = 'main' AND type = 'shadow' ORDER BY name")as$B){if(preg_match('(^'.preg_quote($Q).'_[^_]*$)',$B))$H[]=array("table"=>$B,"ns"=>"");}}return$H;}function
fulltextSql($B,array$v,$F,$Wa){return
idf_escape($B)." MATCH ".q($F);}function
insertUpdate($Q,array$J,array$Wi){$Y=array();foreach($J
as$N)$Y[]="(".implode(", ",$N).")";return
queries("REPLACE INTO ".table($Q)." (".implode(", ",array_keys(reset($J))).") VALUES\n".implode(",\n",$Y));}function
tableHelp($B,$Bf=false){if(preg_match('~^sqlite_(seq|stat.)~',$B,$A))return"fileformat2.html#$A[1]tab";if(preg_match('~^sqlite(_temp)?_(master|schema)$~',$B))return"schematab.html";}function
checkConstraints($Q){preg_match_all('~ CHECK *(\( *(((?>[^()]*[^() ])|(?1))*) *\))~',get_val("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = ".q($Q),0,$this->conn),$og);return
array_combine($og[2],$og[2]);}function
allFields(){$H=array();if(min_version(3.16)){$J=get_rows('SELECT m.name AS tab, p.name AS field, p.type, p."notnull", p.pk AS '.idf_escape("primary")."
FROM sqlite_master m, pragma_table_".(min_version(3.31)?"x":"")."info(m.name) p
WHERE m.type IN ('table', 'view')".(min_version(3.31)?"
AND p.hidden != 1":"").(min_version(3.37)?"
AND m.name NOT IN (SELECT name FROM pragma_table_list WHERE type = 'shadow')":"")."
ORDER BY (m.name LIKE 'sqlite_%'), m.name, p.cid",$this->conn);foreach($J
as$I){$I["type"]=type_affinity($I["type"]);$I["null"]=!$I["notnull"];$H[$I["tab"]][]=$I;}}else{foreach(tables_list()as$Q=>$T){foreach(fields($Q)as$m)$H[$Q][]=$m;}}return$H;}}function
idf_escape($u){return'"'.str_replace('"','""',$u).'"';}function
table($u){return
idf_escape($u);}function
get_databases($Qd){return
array();}function
limit($F,$Z,$z,$yh=0,$ak=" "){return" $F$Z".($z?$ak."LIMIT $z".($yh?" OFFSET $yh":""):"");}function
limit1($Q,$F,$Z,$ak="\n"){return(preg_match('~^INTO~',$F)||get_val("SELECT sqlite_compileoption_used('ENABLE_UPDATE_DELETE_LIMIT')")?limit($F,$Z,1,0,$ak):" $F WHERE rowid = (SELECT rowid FROM ".table($Q).$Z.$ak."LIMIT 1)");}function
db_collation($j,array$wb){return
get_val("PRAGMA encoding");}function
logged_user(){return
get_current_user();}function
virtual_module($Dk){return(preg_match('~^CREATE\s+VIRTUAL\s+TABLE\s+(?:IF\s+NOT\s+EXISTS\s+)?(?:"[^"]*+"|`[^`]*+`|\[[^\]]*+\]|[^\s(]+)\s+USING\s+([a-z0-9_]+)~i',$Dk,$A)?$A[1]:"");}function
tables_list(){return
get_key_vals("SELECT name, type FROM sqlite_master WHERE type IN ('table', 'view')".(min_version(3.37)?" AND name NOT IN (SELECT name FROM pragma_table_list WHERE type = 'shadow')":"")."
ORDER BY (name LIKE 'sqlite_%'), name");}function
count_tables(array$i){return
array();}function
db_status(){$ii=get_val("PRAGMA page_size");$ae=get_val("PRAGMA freelist_count")*$ii;return
array("Data_length"=>get_val("PRAGMA page_count")*$ii-$ae,"Index_length"=>0,"Data_free"=>$ae,);}function
table_status($B="",$yd=false){$H=array();$J=array();if(!$yd&&$B==""){connection()->query("PRAGMA optimize = 0x10002");$J=get_key_vals("SELECT tbl, MAX(CAST(stat AS integer)) FROM sqlite_stat1 GROUP BY tbl");}foreach(get_rows("SELECT name AS Name, type AS Engine, sql, 'rowid' AS Oid, '' AS Auto_increment".(min_version(3.37)?", name IN (SELECT name FROM pragma_table_list WHERE type = 'shadow') AS dependent":"")." FROM sqlite_master WHERE type IN ('table', 'view') ".($B!=""?"AND name = ".q($B):"ORDER BY (name LIKE 'sqlite_%'), name"))as$I){if($I["Engine"]=="table"){$Dk=preg_replace('~(?:\s|--[^\n]*|/\*.*?\*/)+$~s','',$I["sql"]);$Rk=preg_replace('~.*\)~s','',$Dk);$I["Engine"]=virtual_module($I["sql"])?:(implode(", ",array_filter(array((preg_match('~\bSTRICT\b~i',$Rk)?"STRICT":0),(preg_match('~\bWITHOUT\s+ROWID\b~i',$Rk)?"WITHOUT ROWID":0),)))?:"table");}unset($I["sql"]);$I["Rows"]=idx($J,$I["Name"],0);$H[$I["Name"]]=$I;}if(!$yd){foreach(get_rows("SELECT * FROM sqlite_sequence".($B!=""?" WHERE name = ".q($B):""),null,"")as$I)$H[$I["name"]]["Auto_increment"]=$I["seq"];}return$H;}function
is_view(array$R){return$R["Engine"]=="view";}function
fk_support(array$R){return!get_val("SELECT sqlite_compileoption_used('OMIT_FOREIGN_KEY')");}function
type_affinity($T){$T=strtolower($T);return(preg_match('~int~i',$T)?"integer":(preg_match('~char|clob|text~i',$T)?"text":(preg_match('~blob~i',$T)?"blob":(preg_match('~real|floa|doub~i',$T)?"real":(preg_match('~any~i',$T)?"any":"numeric")))));}function
fields($Q){$H=array();$Dk=get_val("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = ".q($Q));$bj=array("select"=>1,"where"=>1,"order"=>1);if(!preg_match('~^sqlite(_temp)?_(master|schema)$~',$Q))$bj+=array("insert"=>1,"update"=>1);$ce=preg_match('~^fts\d+$~i',virtual_module($Dk));foreach(get_rows("PRAGMA table_".(min_version(3.31)?"x":"")."info(".table($Q).")")as$I){if($I["hidden"]==1)continue;$B=$I["name"];$T=strtolower($I["type"]);$k=$I["dflt_value"];$H[$B]=array("field"=>$B,"type"=>($ce?"text":type_affinity($T)),"full_type"=>$T,"default"=>(preg_match("~^'(.*)'$~",$k,$A)?str_replace("''","'",$A[1]):($k=="NULL"?null:$k)),"null"=>!$I["notnull"],"privileges"=>$bj,"primary"=>$I["pk"],);if($I["pk"]&&preg_match('~\bAUTOINCREMENT\b~i',$Dk))$H[$B]["auto_increment"]=true;}$u='[(,]\s*(("[^"]*+")+|[a-z0-9_]+)';$zj='(?:[^,()\']|\'[^\']*+\'|\([^)]*+\))*?';preg_match_all('~'.$u.'\s+text\b'.$zj.'COLLATE\s+(\'[^\']+\'|[a-z0-9_]+)~i',$Dk,$og,PREG_SET_ORDER);foreach($og
as$A){$B=str_replace('""','"',preg_replace('~^"|"$~','',$A[1]));if($H[$B])$H[$B]["collation"]=trim($A[3],"'");}preg_match_all('~'.$u.'\s'.$zj.'GENERATED\s+ALWAYS\s+AS\s*\((.+?)\)\s+(STORED|VIRTUAL)~i',$Dk,$og,PREG_SET_ORDER);foreach($og
as$A){$B=str_replace('""','"',preg_replace('~^"|"$~','',$A[1]));if($H[$B]){$H[$B]["default"]=$A[3];$H[$B]["generated"]=strtoupper($A[4]);}}return$H;}function
indexes($Q,$g=null){$g=connection($g);$H=array();$Dk=get_val("SELECT sql FROM sqlite_master WHERE type = 'table' AND name = ".q($Q),0,$g);if(preg_match('~^fts\d+$~i',virtual_module($Dk)))return
array($Q=>array("type"=>"FULLTEXT","columns"=>array_keys(fields($Q)),"lengths"=>array(),"descs"=>array()));if(preg_match('~\bPRIMARY\s+KEY\s*\((([^)"]+|"[^"]*"|`[^`]*`)++)~i',$Dk,$A)){$H[""]=array("type"=>"PRIMARY","columns"=>array(),"lengths"=>array(),"descs"=>array());preg_match_all('~((("[^"]*+")+|(?:`[^`]*+`)+)|(\S+))(\s+(ASC|DESC))?(,\s*|$)~i',$A[1],$og,PREG_SET_ORDER);foreach($og
as$A){$H[""]["columns"][]=idf_unescape($A[2]).$A[4];$H[""]["descs"][]=(preg_match('~DESC~i',$A[5])?'1':null);}}if(!$H){foreach(fields($Q)as$B=>$m){if($m["primary"])$H[""]=array("type"=>"PRIMARY","columns"=>array($B),"lengths"=>array(),"descs"=>array(null));}}$Ik=get_key_vals("SELECT name, sql FROM sqlite_master WHERE type = 'index' AND tbl_name = ".q($Q),$g);foreach(get_rows("PRAGMA index_list(".table($Q).")",$g)as$I){$B=$I["name"];$v=array("type"=>($I["unique"]?"UNIQUE":"INDEX"));$v["lengths"]=array();$v["descs"]=array();foreach(get_rows("PRAGMA index_info(".idf_escape($B).")",$g)as$Kj){$v["columns"][]=$Kj["name"];$v["descs"][]=null;}if(preg_match('~^CREATE( UNIQUE)? INDEX '.preg_quote(idf_escape($B).' ON '.idf_escape($Q),'~').' \((.*)\)$~i',$Ik[$B],$uj)){preg_match_all('/("[^"]*+")+( DESC)?/',$uj[2],$og);foreach($og[2]as$x=>$W){if($W)$v["descs"][$x]='1';}}if(!$H[""]||$v["type"]!="UNIQUE"||$v["columns"]!=$H[""]["columns"]||$v["descs"]!=$H[""]["descs"]||!preg_match("~^sqlite_~",$B))$H[$B]=$v;}return$H;}function
foreign_keys($Q){$H=array();foreach(get_rows("PRAGMA foreign_key_list(".table($Q).")")as$I){$p=&$H[$I["id"]];if(!$p)$p=$I;$p["source"][]=$I["from"];$p["target"][]=$I["to"];}return$H;}function
view($B){return
array("select"=>preg_replace('~^(?:[^`"[]+|`[^`]*`|"[^"]*")* AS\s+~iU','',get_val("SELECT sql FROM sqlite_master WHERE type = 'view' AND name = ".q($B))));}function
collations(){return(isset($_GET["create"])?get_vals("PRAGMA collation_list",1):array());}function
information_schema($j,$K=""){return
false;}function
error(){return
h(connection()->error);}function
check_sqlite_name($B){$ud="db|sdb|sqlite";if(!preg_match("~^[^\\0]*\\.($ud)\$~",$B)){connection()->error=sprintf('Please use one of these file extensions: %s.',str_replace("|",", ",$ud));return
false;}return
true;}function
create_database($j,$vb){if(file_exists($j)){connection()->error='File exists.';return
false;}if(!check_sqlite_name($j))return
false;try{$_=new
Db();$_->attach(server_parts(array("path"=>$j)),'','');}catch(\Exception$kd){connection()->error=$kd->getMessage();return
false;}$_->query('PRAGMA encoding = "UTF-8"');$_->query('CREATE TABLE adminer (i)');$_->query('DROP TABLE adminer');return
true;}function
drop_databases(array$i){connection()->attach(server_parts(array("path"=>":memory:")),'','');foreach($i
as$j){if(!check_sqlite_name($j))return
false;if(!@unlink($j)){connection()->error='File exists.';return
false;}}return
true;}function
rename_database($B,$vb){if(!check_sqlite_name($B))return
false;connection()->attach(server_parts(array("path"=>":memory:")),'','');connection()->error='File exists.';return@rename(DB,$B);}function
auto_increment(){return" PRIMARY KEY AUTOINCREMENT";}function
alter_table($Q,$B,array$n,array$Sd,$Ab,$Yc,$vb,$Ga,$ti){$om=($Q==""||$Sd||$Yc);foreach($n
as$m){if($m[0]!=""||!$m[1]||$m[2]){$om=true;break;}}$b=array();$ci=array();foreach($n
as$m){if($m[1]){$b[]=($om?$m[1]:"ADD ".implode($m[1]));if($m[0]!="")$ci[$m[0]]=$m[1][0];}}if(!$om){foreach($b
as$W){if(!queries("ALTER TABLE ".table($Q)." $W"))return
false;}if($Q!=$B&&!queries("ALTER TABLE ".table($Q)." RENAME TO ".table($B)))return
false;}elseif(!recreate_table($Q,$B,$b,$ci,$Sd,$Ga,array(),"","",$Yc))return
false;if($Ga){queries("BEGIN");queries("UPDATE sqlite_sequence SET seq = $Ga WHERE name = ".q($B));if(!connection()->affected_rows)queries("INSERT INTO sqlite_sequence (name, seq) VALUES (".q($B).", $Ga)");queries("COMMIT");}return
true;}function
recreate_table($Q,$B,array$n,array$ci,array$Sd,$Ga="",$w=array(),$Lc="",$la="",$Yc=""){if($Q!=""){if(!$n){foreach(fields($Q)as$x=>$m){if($w)$m["auto_increment"]=0;$n[]=process_field($m,$m);$ci[$x]=idf_escape($x);}}$Xi=false;foreach($n
as$m){if($m[6])$Xi=true;}$Nc=array();foreach($w
as$x=>$W){if($W[2]=="DROP"){$Nc[$W[1]]=true;unset($w[$x]);}}foreach(indexes($Q)as$Hf=>$v){$e=array();foreach($v["columns"]as$x=>$d){if(!$ci[$d])continue
2;$e[]=$ci[$d].($v["descs"][$x]?" DESC":"");}if(!$Nc[$Hf]){if($v["type"]!="PRIMARY"||!$Xi)$w[]=array($v["type"],$Hf,$e);}}foreach($w
as$x=>$W){if($W[0]=="PRIMARY"){unset($w[$x]);$Sd[]="  PRIMARY KEY (".implode(", ",$W[2]).")";}}foreach(foreign_keys($Q)as$Hf=>$p){foreach($p["source"]as$x=>$d){if(!$ci[$d])continue
2;$p["source"][$x]=idf_unescape($ci[$d]);}if(!isset($Sd[" $Hf"]))$Sd[]=" ".format_foreign_key($p);}queries("BEGIN");}$eb=array();foreach($n
as$m){if(preg_match('~GENERATED~',$m[3]))unset($ci[array_search($m[0],$ci)]);$eb[]="  ".implode($m);}$eb=array_merge($eb,array_filter($Sd));foreach(driver()->checkConstraints($Q)as$ib){if($ib!=$Lc)$eb[]="  CHECK ($ib)";}if($la)$eb[]="  CHECK ($la)";$rl=($Q!=""&&$Q==$B?"adminer_$B":$B);if(!$Yc&&$Q!="")$Yc=idx(table_status1($Q),"Engine");if(!queries("CREATE TABLE ".table($rl)." (\n".implode(",\n",$eb)."\n)".($Yc!="table"&&in_array($Yc,driver()->engines())?" $Yc":"")))return
false;if($Q!=""){if($ci&&!queries("INSERT INTO ".table($rl)." (".implode(", ",$ci).") SELECT ".implode(", ",array_map('Adminer\idf_escape',array_keys($ci)))." FROM ".table($Q)))return
false;$Rl=array();foreach(triggers($Q)as$Pl=>$yl){$Nl=trigger($Pl,$Q);$Rl[]="CREATE TRIGGER ".idf_escape($Pl)." ".implode(" ",$yl)." ON ".table($B)."\n$Nl[Statement]";}$Ga=$Ga?"":get_val("SELECT seq FROM sqlite_sequence WHERE name = ".q($Q));if(!queries("DROP TABLE ".table($Q))||($Q==$B&&!queries("ALTER TABLE ".table($rl)." RENAME TO ".table($B)))||!alter_indexes($B,$w))return
false;if($Ga)queries("UPDATE sqlite_sequence SET seq = $Ga WHERE name = ".q($B));foreach($Rl
as$Nl){if(!queries($Nl))return
false;}queries("COMMIT");}return
true;}function
index_sql($Q,$T,$B,$e){return"CREATE $T ".($T!="INDEX"?"INDEX ":"").idf_escape($B!=""?$B:uniqid($Q."_"))." ON ".table($Q)." $e";}function
alter_indexes($Q,$b){foreach($b
as$Wi){if($Wi[0]=="PRIMARY")return
recreate_table($Q,$Q,array(),array(),array(),"",$b);}foreach(array_reverse($b)as$W){if(!queries($W[2]=="DROP"?"DROP INDEX ".idf_escape($W[1]):index_sql($Q,$W[0],$W[1],"(".implode(", ",$W[2]).")")))return
false;}return
true;}function
truncate_tables(array$S){return
apply_queries("DELETE FROM",$S);}function
drop_views(array$Em){return
apply_queries("DROP VIEW",$Em);}function
drop_tables(array$S){return
apply_queries("DROP TABLE",$S);}function
move_tables(array$S,array$Em,$nl){return
false;}function
trigger($B,$Q){if($B=="")return
array("Statement"=>"BEGIN\n\t;\nEND");$u='(?:[^`"\s]+|`[^`]*`|"[^"]*")+';$Ql=trigger_options();preg_match("~^CREATE\\s+TRIGGER\\s*$u\\s*(".implode("|",$Ql["Timing"]).")\\s+([a-z]+)(?:\\s+OF\\s+($u))?\\s+ON\\s*$u\\s*(?:FOR\\s+EACH\\s+ROW\\s)?(.*)~is",get_val("SELECT sql FROM sqlite_master WHERE type = 'trigger' AND name = ".q($B)),$A);$uh=$A[3];return
array("Timing"=>strtoupper($A[1]),"Event"=>strtoupper($A[2]).($uh?" OF":""),"Of"=>idf_unescape($uh),"Trigger"=>$B,"Statement"=>$A[4],);}function
triggers($Q){$H=array();$Ql=trigger_options();foreach(get_rows("SELECT * FROM sqlite_master WHERE type = 'trigger' AND tbl_name = ".q($Q))as$I){preg_match('~^CREATE\s+TRIGGER\s*(?:[^`"\s]+|`[^`]*`|"[^"]*")+\s*('.implode("|",$Ql["Timing"]).')\s*(.*?)\s+ON\b~i',$I["sql"],$A);$H[$I["name"]]=array($A[1],$A[2]);}return$H;}function
trigger_options(){return
array("Timing"=>array("BEFORE","AFTER","INSTEAD OF"),"Event"=>array("INSERT","UPDATE","UPDATE OF","DELETE"),"Type"=>array("FOR EACH ROW"),);}function
last_id($G){return
get_val("SELECT LAST_INSERT_ROWID()");}function
explain(Db$f,$F){return$f->query("EXPLAIN QUERY PLAN $F");}function
found_rows(array$R,array$Z){}function
types($ud=false){return
array();}function
create_sql($Q,$Ga,$Pk){$H=get_val("SELECT sql FROM sqlite_master WHERE type IN ('table', 'view') AND name = ".q($Q));foreach(indexes($Q)as$B=>$v){if($B==''||$v['type']=='FULLTEXT')continue;$H
.=";\n\n".index_sql($Q,$v['type'],$B,"(".implode(", ",array_map('Adminer\idf_escape',$v['columns'])).")");}return$H;}function
truncate_sql($Q){return"DELETE FROM ".table($Q);}function
use_sql($gc,$Pk=""){return"";}function
trigger_sql($Q){return
implode(get_vals("SELECT sql || ';;\n' FROM sqlite_master WHERE type = 'trigger' AND tbl_name = ".q($Q)));}function
show_variables(){$H=array();foreach(get_rows("PRAGMA pragma_list")as$I){$B=$I["name"];if($B!="pragma_list"&&$B!="compile_options"){$H[$B]=array($B,'');foreach(get_rows("PRAGMA $B")as$I)$H[$B][1].=implode(", ",$I)."\n";}}return$H;}function
show_status(){$H=array();foreach(get_vals("PRAGMA compile_options")as$Oh)$H[]=explode("=",$Oh,2)+array('','');return$H;}function
convert_field(array$m){}function
unconvert_field(array$m,$H){return$H;}function
support($zd){return
preg_match('~^(check|columns|database|drop_col|dump|fast_status|indexes|descidx|move_col|sql|status|table|transaction_ddl|trigger|variables|view|view_trigger)$~',$zd);}}add_driver("mssql","MS SQL");if(isset($_GET["mssql"])){define('Adminer\DRIVER',"mssql");if(extension_loaded("sqlsrv")&&$_GET["ext"]!="pdo"){class
Db
extends
SqlDb{var$extension="sqlsrv";private$link,$result,$warnings;private
function
get_error(){$this->error="";foreach(sqlsrv_errors()as$l){$this->errno=$l["code"];$this->error
.="$l[message]\n";}$this->error=rtrim($this->error);}function
attach(array$M,$U,$E){sqlsrv_configure("WarningsReturnAsErrors",0);$Jb=array("UID"=>$U,"PWD"=>$E,"CharacterSet"=>"UTF-8");$Jk=adminer()->connectSsl();if(isset($Jk["Encrypt"]))$Jb["Encrypt"]=$Jk["Encrypt"];if(isset($Jk["TrustServerCertificate"]))$Jb["TrustServerCertificate"]=$Jk["TrustServerCertificate"];$j=adminer()->database();if($j!="")$Jb["Database"]=$j;$Ji=$M["port"];$this->link=@sqlsrv_connect($M["host"].($Ji?",$Ji":""),$Jb);if($this->link){$hf=sqlsrv_server_info($this->link);$this->server_info=$hf['SQLServerVersion'];}else$this->get_error();return($this->link?'':$this->error);}function
quote($P){$Zl=strlen($P)!=strlen(utf8_decode($P));return($Zl?"N":"")."'".str_replace("'","''",$P)."'";}function
select_db($gc){return$this->query(use_sql($gc));}function
query($F,$Yl=false){$G=sqlsrv_query($this->link,$F);$this->error="";if(!$G){$this->get_error();return
false;}return$this->store_result($G);}function
multi_query($F){$this->result=sqlsrv_query($this->link,$F);$this->error="";if(!$this->result){$this->get_error();return
false;}return
true;}function
store_result($G=null){if(!$G)$G=$this->result;if(!$G)return
false;$this->warnings=sqlsrv_errors(SQLSRV_ERR_WARNINGS);if(sqlsrv_field_metadata($G))return
new
Result($G);$this->affected_rows=sqlsrv_rows_affected($G);return
true;}function
next_result(){if(!$this->result)return
false;$H=sqlsrv_next_result($this->result);if($H===false){$this->get_error();$this->result=null;return
true;}return!!$H;}function
warnings(){$H=array();foreach((array)$this->warnings
as$Hm)$H[]=$Hm["message"];return$H;}}class
Result{var$num_rows;private$result,$offset=0,$fields;function
__construct($G){$this->result=$G;}private
function
convert($I){foreach((array)$I
as$x=>$W){if(is_a($W,'DateTime'))$I[$x]=$W->format("Y-m-d H:i:s");}return$I;}function
fetch_assoc(){return$this->convert(sqlsrv_fetch_array($this->result,SQLSRV_FETCH_ASSOC));}function
fetch_row(){return$this->convert(sqlsrv_fetch_array($this->result,SQLSRV_FETCH_NUMERIC));}function
fetch_field(){if(!$this->fields)$this->fields=sqlsrv_field_metadata($this->result);$m=$this->fields[$this->offset++];$H=new
\stdClass;$H->name=$m["Name"];$H->type=($m["Type"]==1?254:15);$H->charsetnr=(in_array($m["Type"],array(-2,-3,-4))?63:0);return$H;}function
seek($yh){for($s=0;$s<$yh;$s++)sqlsrv_fetch($this->result);}}function
last_id($G){return(string)get_val("SELECT SCOPE_IDENTITY()");}function
explain(Db$f,$F){$f->query("SET SHOWPLAN_ALL ON");$H=$f->query($F);$f->query("SET SHOWPLAN_ALL OFF");return$H;}}else{abstract
class
MssqlDb
extends
PdoDb{function
select_db($gc){return$this->query(use_sql($gc));}function
lastInsertId(){return$this->pdo->lastInsertId();}function
warnings(){$G=$this->multi;if(!is_object($G))return
array();$l=$G->errorInfo();return
array((string)$l[2]);}}function
last_id($G){return
connection()->lastInsertId();}function
explain(Db$f,$F){}if(extension_loaded("pdo_sqlsrv")){class
Db
extends
MssqlDb{var$extension="PDO_SQLSRV";function
attach(array$M,$U,$E){$Ji=$M["port"];$Pc="sqlsrv:Server=$M[host]".($Ji?",$Ji":"");$Jk=adminer()->connectSsl();foreach(array("Encrypt","TrustServerCertificate")as$x){if(isset($Jk[$x]))$Pc
.=";$x=".($Jk[$x]?1:0);}return$this->dsn($Pc,$U,$E,array(\PDO::SQLSRV_ATTR_DIRECT_QUERY=>true));}}}elseif(extension_loaded("pdo_dblib")){class
Db
extends
MssqlDb{var$extension="PDO_DBLIB";function
attach(array$M,$U,$E){$Ji=$M["port"];$wk=$M["socket"];return$this->dsn("dblib:charset=utf8;host=$M[host]".($Ji!=""?";port=$Ji":($wk!=""?";unix_socket=$wk":"")),$U,$E);}}}}class
Driver
extends
SqlDriver{static$extensions=array("SQLSRV","PDO_SQLSRV","PDO_DBLIB");static$jush="mssql";static$serverSocket=true;var$insertFunctions=array("date|time"=>"getdate");var$editFunctions=array("int|decimal|real|float|money|datetime"=>"+/-","char|text"=>"+",);var$functions=array("len","lower","round","upper");var$grouping=array("avg","count","count distinct","max","min","sum");var$generated=array("PERSISTED","VIRTUAL");var$onActions="NO ACTION|CASCADE|SET NULL|SET DEFAULT";private$unknownTypes=array();function
operators($Xk){return
array("=","<",">","<=",">=","!=","LIKE","LIKE %%","IN","IS NULL","NOT LIKE","NOT IN","IS NOT NULL");}static
function
connect($M,$U,$E){if($M=="")$M="localhost:1433";return
parent::connect($M,$U,$E);}function
__construct(Db$f){parent::__construct($f);$this->types=array('Numbers'=>array("tinyint"=>3,"smallint"=>5,"int"=>10,"bigint"=>20,"bit"=>1,"decimal"=>0,"numeric"=>0,"real"=>12,"float"=>53,"smallmoney"=>10,"money"=>20,"vector"=>0,),'Date and time'=>array("date"=>10,"smalldatetime"=>19,"datetime"=>19,"datetime2"=>19,"time"=>8,"datetimeoffset"=>26),'Strings'=>array("char"=>8000,"varchar"=>8000,"text"=>2147483647,"nchar"=>4000,"nvarchar"=>4000,"ntext"=>1073741823,"uniqueidentifier"=>36,"xml"=>2147483647,"json"=>2147483647,"sql_variant"=>8000,"hierarchyid"=>892,),'Binary'=>array("binary"=>8000,"varbinary"=>8000,"image"=>2147483647),'Geometry'=>array("geometry"=>0,"geography"=>0),);$Xl=array_flip(get_vals("SELECT name FROM sys.types WHERE is_user_defined = 0 ORDER BY name"));if($Xl){foreach($this->types
as$r=>$pe){foreach($pe
as$T=>$y){if(isset($Xl[$T]))unset($Xl[$T]);else
unset($this->types[$r][$T]);}if(!$this->types[$r])unset($this->types[$r]);}$this->unknownTypes=array_keys($Xl);}}function
types(){return
parent::types()+array_fill_keys($this->unknownTypes,0);}function
structuredTypes(){return
array_merge(parent::structuredTypes(),$this->unknownTypes);}function
insertUpdate($Q,array$J,array$Wi){$n=fields($Q);$im=array();$Z=array();$N=reset($J);$e="c".implode(", c",range(1,count($N)));$ab=0;$nf=array();foreach($N
as$x=>$W){$ab++;$B=idf_unescape($x);if(!$n[$B]["auto_increment"])$nf[$x]="c$ab";if(isset($Wi[$B]))$Z[]="$x = c$ab";else$im[]="$x = c$ab";}$Y=array();foreach($J
as$N)$Y[]="(".implode(", ",$N).")";if($Z){$Qe=queries("SET IDENTITY_INSERT ".table($Q)." ON");$H=queries("MERGE ".table($Q)." USING (VALUES\n\t".implode(",\n\t",$Y)."\n) AS source ($e) ON ".implode(" AND ",$Z).($im?"\nWHEN MATCHED THEN UPDATE SET ".implode(", ",$im):"")."\nWHEN NOT MATCHED THEN INSERT (".implode(", ",array_keys($Qe?$N:$nf)).") VALUES (".($Qe?$e:implode(", ",$nf)).");");if($Qe)queries("SET IDENTITY_INSERT ".table($Q)." OFF");}else$H=queries("INSERT INTO ".table($Q)." (".implode(", ",array_keys($N)).") VALUES\n".implode(",\n",$Y));return$H;}function
begin(){return
queries("BEGIN TRANSACTION");}function
convertSearch($u,array$W,array$m){return(preg_match('~^(bit|n?text|xml|json|vector|uniqueidentifier|sql_variant|hierarchyid|geography|geometry)$~',$m["type"])?"CAST($u AS nvarchar(max))":$u);}function
quoteBinary($Lj){return"0x".bin2hex($Lj);}function
warnings(){$H=array();foreach($this->conn->warnings()as$Fg){$Fg=trim(preg_replace('~^(\[[^]]+])+~','',$Fg));if($Fg!="")$H[]=$Fg;}return
nl_br(h(implode("\n",$H)));}function
tableHelp($B,$Bf=false){$eg=array("sys"=>"catalog-views/sys-","INFORMATION_SCHEMA"=>"information-schema-views/",);$_=$eg[get_schema()];if($_)return"relational-databases/system-$_".preg_replace('~_~','-',strtolower($B))."-transact-sql";}}function
idf_escape($u){return"[".str_replace("]","]]",$u)."]";}function
table($u){return($_GET["ns"]!=""?idf_escape($_GET["ns"]).".":"").idf_escape($u);}function
get_databases($Qd){return
get_vals("SELECT name FROM sys.databases WHERE name NOT IN ('master', 'tempdb', 'model', 'msdb')");}function
limit($F,$Z,$z,$yh=0,$ak=" "){return($z?" TOP (".($z+$yh).")":"")." $F$Z";}function
limit1($Q,$F,$Z,$ak="\n"){return
limit($F,$Z,1,0,$ak);}function
db_collation($j,array$wb){return
get_val("SELECT collation_name FROM sys.databases WHERE name = ".q($j));}function
logged_user(){return
get_val("SELECT SUSER_NAME()");}function
tables_list(){return
get_key_vals("SELECT name, type_desc FROM sys.all_objects WHERE schema_id = SCHEMA_ID(".q(get_schema()).") AND type IN ('S', 'U', 'V') ORDER BY name");}function
count_tables(array$i){$H=array();foreach($i
as$j){connection()->select_db($j);$H[$j]=get_val("SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES");}return$H;}function
table_status($B="",$yd=false){$H=array();$uk=array();foreach(get_rows("SELECT object_id, SUM(CASE WHEN index_id < 2 THEN row_count ELSE 0 END) AS [Rows],
SUM(CASE WHEN index_id < 2 THEN used_page_count ELSE 0 END) * 8192 AS Data_length,
SUM(CASE WHEN index_id > 1 THEN used_page_count ELSE 0 END) * 8192 AS Index_length,
SUM(reserved_page_count - used_page_count) * 8192 AS Data_free
FROM sys.dm_db_partition_stats
GROUP BY object_id",null,"")as$I){$th=$I["object_id"];unset($I["object_id"]);$uk[$th]=$I;}foreach(get_rows("SELECT ao.object_id, ao.name AS Name, ao.type_desc AS Engine,
	(SELECT cast(value as varchar(max)) FROM fn_listextendedproperty(default, 'SCHEMA', schema_name(schema_id), 'TABLE', ao.name, null, null)) AS Comment
FROM sys.all_objects AS ao
WHERE schema_id = SCHEMA_ID(".q(get_schema()).") AND type IN ('S', 'U', 'V') ".($B!=""?"AND name = ".q($B):"ORDER BY name"))as$I){$th=$I["object_id"];unset($I["object_id"]);$H[$I["Name"]]=$I+idx($uk,$th,array());}return$H;}function
is_view(array$R){return$R["Engine"]=="VIEW";}function
fk_support(array$R){return
true;}function
fields($Q){$Cb=get_key_vals("SELECT objname, cast(value as varchar(max)) FROM fn_listextendedproperty('MS_DESCRIPTION', 'schema', ".q(get_schema()).", 'table', ".q($Q).", 'column', NULL)");$H=array();$Yk=get_val("SELECT object_id FROM sys.all_objects WHERE schema_id = SCHEMA_ID(".q(get_schema()).") AND type IN ('S', 'U', 'V') AND name = ".q($Q));foreach(get_rows("SELECT c.max_length, c.precision, c.scale, c.name, c.is_nullable, c.is_identity, c.collation_name,
	COALESCE(bt.name, t.name) type, d.definition [default], d.name default_constraint, i.is_primary_key
FROM sys.all_columns c
JOIN sys.types t ON c.user_type_id = t.user_type_id
LEFT JOIN sys.types bt ON t.system_type_id = bt.user_type_id AND t.is_user_defined = 1
LEFT JOIN sys.default_constraints d ON c.default_object_id = d.object_id
LEFT JOIN sys.index_columns ic ON c.object_id = ic.object_id AND c.column_id = ic.column_id
LEFT JOIN sys.indexes i ON ic.object_id = i.object_id AND ic.index_id = i.index_id
WHERE c.object_id = ".q($Yk))as$I){$T=$I["type"];$y=(preg_match("~char|binary~",$T)?intval($I["max_length"])/($T[0]=='n'?2:1):($T=="decimal"?"$I[precision],$I[scale]":($T=="vector"?(intval($I["max_length"])-8)/4:"")));$H[$I["name"]]=array("field"=>$I["name"],"full_type"=>$T.($y?"($y)":""),"type"=>$T,"length"=>$y,"default"=>(preg_match("~^\('(.*)'\)$~",$I["default"],$A)?str_replace("''","'",$A[1]):$I["default"]),"default_constraint"=>$I["default_constraint"],"null"=>$I["is_nullable"],"auto_increment"=>$I["is_identity"],"collation"=>$I["collation_name"],"privileges"=>array("insert"=>1,"select"=>1,"update"=>1,"where"=>1,"order"=>1),"primary"=>$I["is_primary_key"],"comment"=>$Cb[$I["name"]],);}foreach(get_rows("SELECT * FROM sys.computed_columns WHERE object_id = ".q($Yk))as$I){$H[$I["name"]]["generated"]=($I["is_persisted"]?"PERSISTED":"VIRTUAL");$H[$I["name"]]["default"]=$I["definition"];}return$H;}function
indexes($Q,$g=null){$H=array();foreach(get_rows("SELECT i.name, key_ordinal, is_unique, is_primary_key, c.name AS column_name, is_descending_key
FROM sys.indexes i
INNER JOIN sys.index_columns ic ON i.object_id = ic.object_id AND i.index_id = ic.index_id
INNER JOIN sys.columns c ON ic.object_id = c.object_id AND ic.column_id = c.column_id
WHERE OBJECT_NAME(i.object_id) = ".q($Q),$g)as$I){$B=$I["name"];$H[$B]["type"]=($I["is_primary_key"]?"PRIMARY":($I["is_unique"]?"UNIQUE":"INDEX"));$H[$B]["lengths"]=array();$H[$B]["columns"][$I["key_ordinal"]]=$I["column_name"];$H[$B]["descs"][$I["key_ordinal"]]=($I["is_descending_key"]?'1':null);}return$H;}function
view($B){return
array("select"=>preg_replace('~^(?:[^[]|\[[^]]*])*\s+AS\s+~isU','',get_val("SELECT VIEW_DEFINITION FROM INFORMATION_SCHEMA.VIEWS WHERE TABLE_SCHEMA = SCHEMA_NAME() AND TABLE_NAME = ".q($B))));}function
collations(){$H=array();foreach(get_vals("SELECT name FROM fn_helpcollations()")as$vb)$H[preg_replace('~_.*~','',$vb)][]=$vb;return$H;}function
information_schema($j,$K=""){return
in_array($K!=""?$K:get_schema(),array("INFORMATION_SCHEMA","sys"));}function
error(){return
nl_br(h(preg_replace('~^(\[[^]]*])+~m','',connection()->error)));}function
create_database($j,$vb){return
queries("CREATE DATABASE ".idf_escape($j).(preg_match('~^[a-z0-9_]+$~i',$vb)?" COLLATE $vb":""));}function
drop_databases(array$i){return!!queries("DROP DATABASE ".implode(", ",array_map('Adminer\idf_escape',$i)));}function
rename_database($B,$vb){if(preg_match('~^[a-z0-9_]+$~i',$vb))queries("ALTER DATABASE ".idf_escape(DB)." COLLATE $vb");queries("ALTER DATABASE ".idf_escape(DB)." MODIFY NAME = ".idf_escape($B));return
true;}function
auto_increment(){return" IDENTITY".($_POST["Auto_increment"]!=""?"(".number($_POST["Auto_increment"]).",1)":"")." PRIMARY KEY";}function
alter_table($Q,$B,array$n,array$Sd,$Ab,$Yc,$vb,$Ga,$ti){$b=array();$Cb=array();$Yh=fields($Q);foreach($n
as$m){$d=idf_escape($m[0]);$W=$m[1];if(!$W)$b["DROP"][]=" COLUMN $d";else{$W[1]=preg_replace("~( COLLATE )'(\\w+)'~",'\1\2',$W[1]);$Cb[$m[0]]=$W[5];unset($W[5]);if(preg_match('~ AS ~',$W[3]))unset($W[1],$W[2]);if($m[0]=="")$b["ADD"][]="\n  ".implode("",$W).($Q==""?substr($Sd[$W[0]],16+strlen($W[0])):"");else{$k=$W[3];unset($W[3]);unset($W[6]);if($d!=$W[0])queries("EXEC sp_rename ".q(table($Q).".$d").", ".q(idf_unescape($W[0])).", 'COLUMN'");$b["ALTER COLUMN ".implode("",$W)][]="";$Xh=$Yh[$m[0]];if(default_value($Xh)!=$k){if($Xh["default"]!==null)$b["DROP"][]=" ".idf_escape($Xh["default_constraint"]);if($k)$b["ADD"][]="\n $k FOR $d";}}}}if($Q==""){$ka=(array)$b["ADD"];foreach($Sd
as$x=>$W){if(!is_string($x))$ka[]="\n$W";}return
queries("CREATE TABLE ".table($B)." (".implode(",",$ka)."\n)");}if($Q!=$B)queries("EXEC sp_rename ".q(table($Q)).", ".q($B));if($Sd)$b[""]=$Sd;foreach($b
as$x=>$W){if(!queries("ALTER TABLE ".table($B)." $x".implode(",",$W)))return
false;}foreach($Cb
as$x=>$W){$Ab=substr($W,9);queries("EXEC sp_dropextendedproperty @name = N'MS_Description', @level0type = N'Schema', @level0name = ".q(get_schema()).", @level1type = N'Table', @level1name = ".q($B).", @level2type = N'Column', @level2name = ".q($x));queries("EXEC sp_addextendedproperty
@name = N'MS_Description',
@value = $Ab,
@level0type = N'Schema',
@level0name = ".q(get_schema()).",
@level1type = N'Table',
@level1name = ".q($B).",
@level2type = N'Column',
@level2name = ".q($x));}return
true;}function
alter_indexes($Q,$b){$v=array();$Kc=array();foreach($b
as$W){if($W[2]=="DROP"){if($W[0]=="PRIMARY")$Kc[]=idf_escape($W[1]);else$v[]=idf_escape($W[1])." ON ".table($Q);}elseif(!queries(($W[0]!="PRIMARY"?"CREATE $W[0] ".($W[0]!="INDEX"?"INDEX ":"").idf_escape($W[1]!=""?$W[1]:uniqid($Q."_"))." ON ".table($Q):"ALTER TABLE ".table($Q)." ADD PRIMARY KEY")." (".implode(", ",$W[2]).")"))return
false;}return(!$v||queries("DROP INDEX ".implode(", ",$v)))&&(!$Kc||queries("ALTER TABLE ".table($Q)." DROP ".implode(", ",$Kc)));}function
found_rows(array$R,array$Z){}function
foreign_keys($Q){$H=array();$Hh=array("CASCADE","NO ACTION","SET NULL","SET DEFAULT");$K=get_schema();foreach(get_rows("EXEC sp_fkeys @fktable_name = ".q($Q).", @fktable_owner = ".q($K))as$I){$p=&$H[$I["FK_NAME"]];$p["db"]=($I["PKTABLE_QUALIFIER"]==DB?"":$I["PKTABLE_QUALIFIER"]);$p["ns"]=($I["PKTABLE_OWNER"]==$K?"":$I["PKTABLE_OWNER"]);$p["table"]=$I["PKTABLE_NAME"];$p["on_update"]=$Hh[$I["UPDATE_RULE"]];$p["on_delete"]=$Hh[$I["DELETE_RULE"]];$p["source"][]=$I["FKCOLUMN_NAME"];$p["target"][]=$I["PKCOLUMN_NAME"];}return$H;}function
truncate_tables(array$S){return
apply_queries("TRUNCATE TABLE",$S);}function
drop_views(array$Em){return
queries("DROP VIEW ".implode(", ",array_map('Adminer\table',$Em)));}function
drop_tables(array$S){return
queries("DROP TABLE ".implode(", ",array_map('Adminer\table',$S)));}function
move_tables(array$S,array$Em,$nl){return
apply_queries("ALTER SCHEMA ".idf_escape($nl)." TRANSFER",array_merge($S,$Em));}function
trigger($B,$Q){if($B=="")return
array();$J=get_rows("SELECT s.name [Trigger],
CASE WHEN OBJECTPROPERTY(s.id, 'ExecIsInsertTrigger') = 1 THEN 'INSERT'
	WHEN OBJECTPROPERTY(s.id, 'ExecIsUpdateTrigger') = 1 THEN 'UPDATE'
	WHEN OBJECTPROPERTY(s.id, 'ExecIsDeleteTrigger') = 1 THEN 'DELETE' END [Event],
CASE WHEN OBJECTPROPERTY(s.id, 'ExecIsInsteadOfTrigger') = 1 THEN 'INSTEAD OF' ELSE 'AFTER' END [Timing],
c.text
FROM sysobjects s
JOIN syscomments c ON s.id = c.id
WHERE s.xtype = 'TR' AND s.name = ".q($B));$H=reset($J);if($H)$H["Statement"]=preg_replace('~^.+\s+AS\s+~isU','',$H["text"]);return$H;}function
triggers($Q){$H=array();foreach(get_rows("SELECT sys1.name,
CASE WHEN OBJECTPROPERTY(sys1.id, 'ExecIsInsertTrigger') = 1 THEN 'INSERT'
	WHEN OBJECTPROPERTY(sys1.id, 'ExecIsUpdateTrigger') = 1 THEN 'UPDATE'
	WHEN OBJECTPROPERTY(sys1.id, 'ExecIsDeleteTrigger') = 1 THEN 'DELETE' END [Event],
CASE WHEN OBJECTPROPERTY(sys1.id, 'ExecIsInsteadOfTrigger') = 1 THEN 'INSTEAD OF' ELSE 'AFTER' END [Timing]
FROM sysobjects sys1
JOIN sysobjects sys2 ON sys1.parent_obj = sys2.id
WHERE sys1.xtype = 'TR' AND sys2.name = ".q($Q))as$I)$H[$I["name"]]=array($I["Timing"],$I["Event"]);return$H;}function
trigger_options(){return
array("Timing"=>array("AFTER","INSTEAD OF"),"Event"=>array("INSERT","UPDATE","DELETE"),"Type"=>array("AS"),);}function
schemas(){return
get_vals("SELECT name FROM sys.schemas");}function
get_schema(){if($_GET["ns"]!="")return$_GET["ns"];return
get_val("SELECT SCHEMA_NAME()");}function
set_schema($K,$g=null){$_GET["ns"]=$K;return
true;}function
create_sql($Q,$Ga,$Pk){if(is_view(table_status1($Q))){$Dm=view($Q);return"CREATE VIEW ".table($Q)." AS $Dm[select]";}$n=array();$Wi=false;foreach(fields($Q)as$B=>$m){$W=process_field($m,$m);if($W[6])$Wi=true;$n[]=implode("",$W);}foreach(indexes($Q)as$B=>$v){if(!$Wi||$v["type"]!="PRIMARY"){$e=array();foreach($v["columns"]as$x=>$W)$e[]=idf_escape($W).($v["descs"][$x]?" DESC":"");$B=idf_escape($B);$n[]=($v["type"]=="INDEX"?"INDEX $B":"CONSTRAINT $B ".($v["type"]=="UNIQUE"?"UNIQUE":"PRIMARY KEY"))." (".implode(", ",$e).")";}}foreach(driver()->checkConstraints($Q)as$B=>$ib)$n[]="CONSTRAINT ".idf_escape($B)." CHECK ($ib)";return"CREATE TABLE ".table($Q)." (\n\t".implode(",\n\t",$n)."\n)";}function
foreign_keys_sql($Q){$n=array();foreach(foreign_keys($Q)as$Sd)$n[]=ltrim(format_foreign_key($Sd));return($n?"ALTER TABLE ".table($Q)." ADD\n\t".implode(",\n\t",$n).";\n\n":"");}function
truncate_sql($Q){return"TRUNCATE TABLE ".table($Q);}function
use_sql($gc,$Pk=""){return"USE ".idf_escape($gc);}function
trigger_sql($Q){$H="";foreach(triggers($Q)as$B=>$Nl)$H
.=create_trigger(" ON ".table($Q),trigger($B,$Q)).";";return$H;}function
convert_field(array$m){}function
unconvert_field(array$m,$H){return$H;}function
support($zd){return
preg_match('~^(check|comment|columns|database|drop_col|dump|fast_status|indexes|descidx|scheme|sql|table|transaction_ddl|trigger|view|view_trigger)$~',$zd);}}add_driver("oracle","Oracle beta");if(isset($_GET["oracle"])){define('Adminer\DRIVER',"oracle");function
easy_connect(array$M){return
url_host($M["host"]).($M["port"]!=""?":$M[port]":"").$M["path"];}if(extension_loaded("oci8")&&$_GET["ext"]!="pdo"){class
Db
extends
SqlDb{var$extension="oci8";var$_current_db;private$link;function
_error($dd,$l){if(ini_bool("html_errors"))$l=html_entity_decode(strip_tags($l));$l=preg_replace('~^[^:]*: ~','',$l);$this->error=$l;}function
attach(array$M,$U,$E){$this->link=@oci_new_connect($U,$E,easy_connect($M),"AL32UTF8");if($this->link){$this->server_info=oci_server_version($this->link);return'';}$l=oci_error();return($l?$l["message"]:'Unknown error.');}function
quote($P){return"'".str_replace("'","''",$P)."'";}function
select_db($gc){$this->_current_db=$gc;return
true;}function
query($F,$Yl=false){$G=oci_parse($this->link,$F);$this->error="";if(!$G){$l=oci_error($this->link);$this->errno=$l["code"];$this->error=$l["message"];return
false;}set_error_handler(array($this,'_error'));$H=@oci_execute($G);restore_error_handler();if($H){if(oci_num_fields($G))return
new
Result($G);$this->affected_rows=oci_num_rows($G);oci_free_statement($G);}return$H;}function
timeout($Ug){return(function_exists('oci_set_call_timeout')?oci_set_call_timeout($this->link,$Ug):false);}}class
Result{var$num_rows;private$result,$offset=1;function
__construct($G){$this->result=$G;}private
function
convert($I){foreach((array)$I
as$x=>$W){if(is_a($W,'OCILob')||is_a($W,'OCI-Lob'))$I[$x]=$W->load();}return$I;}function
fetch_assoc(){return$this->convert(oci_fetch_assoc($this->result));}function
fetch_row(){return$this->convert(oci_fetch_row($this->result));}function
fetch_field(){$d=$this->offset++;$H=new
\stdClass;$H->name=oci_field_name($this->result,$d);$T=oci_field_type($this->result,$d);$H->native_type=$T;$H->type=$T;$H->charsetnr=(preg_match("~raw|blob|bfile~",$T)?63:0);return$H;}}}elseif(extension_loaded("pdo_oci")){class
Db
extends
PdoDb{var$extension="PDO_OCI";var$_current_db;function
attach(array$M,$U,$E){return$this->dsn("oci:dbname=//".easy_connect($M).";charset=AL32UTF8",$U,$E);}function
select_db($gc){$this->_current_db=$gc;return
true;}}}class
Driver
extends
SqlDriver{static$extensions=array("OCI8","PDO_OCI");static$jush="oracle";static$serverPath=true;var$insertFunctions=array("date"=>"current_date","timestamp"=>"current_timestamp",);var$editFunctions=array("number|float|double"=>"+/-","date|timestamp"=>"+ interval/- interval","char|clob"=>"||",);var$functions=array("length","lower","round","upper");var$grouping=array("avg","count","count distinct","max","min","sum");function
operators($Xk){return
array("=","<",">","<=",">=","!=","LIKE","LIKE %%","IN","IS NULL","NOT LIKE","NOT IN","IS NOT NULL","SQL");}function
__construct(Db$f){parent::__construct($f);$this->types=array('Numbers'=>array("number"=>38,"binary_float"=>12,"binary_double"=>21),'Date and time'=>array("date"=>10,"timestamp"=>29,"interval year"=>12,"interval day"=>28),'Strings'=>array("char"=>2000,"varchar2"=>4000,"nchar"=>2000,"nvarchar2"=>4000,"clob"=>4294967295,"nclob"=>4294967295),'Binary'=>array("raw"=>2000,"long raw"=>2147483648,"blob"=>4294967295,"bfile"=>4294967296),);}function
begin(){return
true;}function
convertSearch($u,array$W,array$m){$T=$m["type"];$Bi=strpos($W["op"],"LIKE")!==false;if($T=="xmltype")return"XMLSERIALIZE(CONTENT $u AS VARCHAR2(4000))";if($T=="json")return"JSON_SERIALIZE($u)";if(preg_match('~^(date|timestamp)~',$T))return"TO_CHAR($u, 'YYYY-MM-DD HH24:MI:SS')";if(preg_match('~char~',$T)||(preg_match('~clob~',$T)&&$Bi))return$u;return(!$Bi&&preg_match(number_type(),$T)?$u:"TO_CHAR($u)");}function
quoteBinary($Lj){return"HEXTORAW(".q(bin2hex($Lj)).")";}function
hasCStyleEscapes(){return
true;}function
allFields(){$H=array();$Dm=views_table("view_name");$J=get_rows('SELECT c.table_name "tab", c.column_name "field", c.data_type "type", c.nullable "nullable",
	c.data_precision "precision", c.data_scale "scale", c.char_col_decl_length "char_length"
FROM all_tab_columns c
WHERE c.table_name IN (
	SELECT table_name FROM all_tables WHERE tablespace_name = '.q(DB).where_owner(" AND ")."
	UNION SELECT view_name FROM $Dm
)".where_owner(" AND ","c.owner").'
ORDER BY c.table_name, c.column_id',$this->conn);foreach($J
as$I){$y="$I[precision],$I[scale]";$I["length"]=($y==","?$I["char_length"]:$y);$I["type"]=strtolower($I["type"]);$I["null"]=($I["nullable"]=="Y");$H[$I["tab"]][]=$I;}return$H;}}function
idf_escape($u){return'"'.str_replace('"','""',$u).'"';}function
table($u){return
idf_escape($u);}function
get_databases($Qd){return
get_vals("SELECT DISTINCT tablespace_name FROM (
SELECT tablespace_name FROM user_tablespaces
UNION SELECT tablespace_name FROM all_tables WHERE tablespace_name IS NOT NULL
)
ORDER BY 1");}function
limit($F,$Z,$z,$yh=0,$ak=" "){return($yh?" * FROM (SELECT t.*, rownum AS rnum FROM (SELECT $F$Z) t WHERE rownum <= ".($z+$yh).") WHERE rnum > $yh":($z?" * FROM (SELECT $F$Z) WHERE rownum <= ".($z+$yh):" $F$Z"));}function
limit1($Q,$F,$Z,$ak="\n"){return" $F$Z";}function
db_collation($j,array$wb){return
get_val("SELECT value FROM nls_database_parameters WHERE parameter = 'NLS_CHARACTERSET'");}function
logged_user(){return
get_val("SELECT USER FROM DUAL");}function
get_current_db(){$j=connection()->_current_db?:DB;connection()->_current_db=null;return$j;}function
where_owner($Ri,$gi="owner"){if(!$_GET["ns"])return'';return"$Ri$gi = sys_context('USERENV', 'CURRENT_SCHEMA')";}function
views_table($e){$gi=where_owner('');return"(SELECT $e FROM all_views WHERE ".($gi?:"rownum < 0").")";}function
tables_list(){$Dm=views_table("view_name");$gi=where_owner(" AND ");return
get_key_vals("SELECT table_name, 'table' FROM all_tables WHERE tablespace_name = ".q(DB)."$gi
UNION SELECT view_name, 'view' FROM $Dm
ORDER BY 1");}function
count_tables(array$i){$H=array();foreach($i
as$j)$H[$j]=get_val("SELECT COUNT(*) FROM all_tables WHERE tablespace_name = ".q($j));return$H;}function
table_status($B="",$yd=false){$H=array();$Qj=q($B);$j=get_current_db();$Dm=views_table("view_name");$gi=where_owner(" AND ","t.owner");foreach(get_rows('SELECT t.table_name "Name", \'table\' "Engine", s.bytes "Data_length", i.bytes "Index_length", t.num_rows "Rows"
FROM all_tables t
LEFT JOIN (SELECT segment_name, SUM(bytes) bytes FROM user_segments WHERE segment_type LIKE \'TABLE%\' GROUP BY segment_name) s ON s.segment_name = t.table_name
LEFT JOIN (SELECT i.table_name, SUM(s.bytes) bytes FROM user_indexes i
	JOIN user_segments s ON s.segment_name = i.index_name AND s.segment_type LIKE \'INDEX%\' GROUP BY i.table_name) i ON i.table_name = t.table_name
WHERE t.tablespace_name = '.q($j).$gi.($B!=""?" AND t.table_name = $Qj":"")."
UNION SELECT view_name, 'view', 0, 0, 0 FROM $Dm".($B!=""?" WHERE view_name = $Qj":"")."
ORDER BY 1")as$I)$H[$I["Name"]]=$I;return$H;}function
is_view(array$R){return$R["Engine"]=="view";}function
fk_support(array$R){return
true;}function
fields($Q){$H=array();$gi=where_owner(" AND ");foreach(get_rows("SELECT * FROM all_tab_columns WHERE table_name = ".q($Q)."$gi ORDER BY column_id")as$I){$T=$I["DATA_TYPE"];$y="$I[DATA_PRECISION],$I[DATA_SCALE]";if($y==",")$y=$I["CHAR_COL_DECL_LENGTH"];$bj=array("insert"=>1,"select"=>1,"update"=>1,"order"=>1);if($I["DATA_TYPE_OWNER"]==""||$T=="XMLTYPE")$bj["where"]=1;$H[$I["COLUMN_NAME"]]=array("field"=>$I["COLUMN_NAME"],"full_type"=>$T.($y?"($y)":""),"type"=>strtolower($T),"length"=>$y,"default"=>$I["DATA_DEFAULT"],"null"=>($I["NULLABLE"]=="Y"),"privileges"=>$bj,);}return$H;}function
indexes($Q,$g=null){$H=array();$gi=where_owner(" AND ","aic.table_owner");foreach(get_rows("SELECT aic.*, ac.constraint_type, atc.data_default
FROM all_ind_columns aic
LEFT JOIN all_constraints ac ON aic.index_name = ac.constraint_name AND aic.table_name = ac.table_name AND aic.index_owner = ac.owner
LEFT JOIN all_tab_cols atc ON aic.column_name = atc.column_name AND aic.table_name = atc.table_name AND aic.index_owner = atc.owner
WHERE aic.table_name = ".q($Q)."$gi
ORDER BY ac.constraint_type, aic.column_position",$g)as$I){$cf=$I["INDEX_NAME"];$zb=$I["DATA_DEFAULT"];$zb=($zb?trim($zb,'"'):$I["COLUMN_NAME"]);$H[$cf]["type"]=($I["CONSTRAINT_TYPE"]=="P"?"PRIMARY":($I["CONSTRAINT_TYPE"]=="U"?"UNIQUE":"INDEX"));$H[$cf]["columns"][]=$zb;$H[$cf]["lengths"][]=($I["CHAR_LENGTH"]&&$I["CHAR_LENGTH"]!=$I["COLUMN_LENGTH"]?$I["CHAR_LENGTH"]:null);$H[$cf]["descs"][]=($I["DESCEND"]&&$I["DESCEND"]=="DESC"?'1':null);}return$H;}function
view($B){$Dm=views_table("view_name, text");$J=get_rows('SELECT text "select" FROM '.$Dm.' WHERE view_name = '.q($B));return
reset($J);}function
collations(){return
array();}function
information_schema($j,$K=""){return($K!=""?$K:get_schema())=="INFORMATION_SCHEMA";}function
error(){return
h(connection()->error);}function
explain(Db$f,$F){$f->query("EXPLAIN PLAN FOR $F");return$f->query("SELECT * FROM plan_table");}function
found_rows(array$R,array$Z){}function
auto_increment(){return"";}function
alter_table($Q,$B,array$n,array$Sd,$Ab,$Yc,$vb,$Ga,$ti){$b=$Kc=array();$Yh=($Q?fields($Q):array());foreach($n
as$m){$W=$m[1];if($W&&$m[0]!=""&&idf_escape($m[0])!=$W[0])queries("ALTER TABLE ".table($Q)." RENAME COLUMN ".idf_escape($m[0])." TO $W[0]");$Xh=$Yh[$m[0]];if($W&&$Xh){$_h=process_field($Xh,$Xh);if($W[2]==$_h[2])$W[2]="";}if($W)$b[]=($Q!=""?($m[0]!=""?"MODIFY (":"ADD ("):"  ").implode($W).($Q!=""?")":"");else$Kc[]=idf_escape($m[0]);}if($Q=="")return
queries("CREATE TABLE ".table($B)." (\n".implode(",\n",array_merge($b,$Sd))."\n)");return(!$b||queries("ALTER TABLE ".table($Q)."\n".implode("\n",$b)))&&(!$Kc||queries("ALTER TABLE ".table($Q)." DROP (".implode(", ",$Kc).")"))&&($Q==$B||queries("ALTER TABLE ".table($Q)." RENAME TO ".table($B)));}function
alter_indexes($Q,$b){$Kc=array();$gj=array();foreach($b
as$W){if($W[0]!="INDEX"){$W[2]=preg_replace('~ DESC$~','',$W[2]);$h=($W[2]=="DROP"?"\nDROP CONSTRAINT ".idf_escape($W[1]):"\nADD".($W[1]!=""?" CONSTRAINT ".idf_escape($W[1]):"")." $W[0] ".($W[0]=="PRIMARY"?"KEY ":"")."(".implode(", ",$W[2]).")");array_unshift($gj,"ALTER TABLE ".table($Q).$h);}elseif($W[2]=="DROP")$Kc[]=idf_escape($W[1]);else$gj[]="CREATE INDEX ".idf_escape($W[1]!=""?$W[1]:uniqid($Q."_"))." ON ".table($Q)." (".implode(", ",$W[2]).")";}if($Kc)array_unshift($gj,"DROP INDEX ".implode(", ",$Kc));foreach($gj
as$F){if(!queries($F))return
false;}return
true;}function
foreign_keys($Q){$H=array();$F="SELECT c_list.CONSTRAINT_NAME as NAME,
c_src.COLUMN_NAME as SRC_COLUMN,
c_dest.OWNER as DEST_DB,
c_dest.TABLE_NAME as DEST_TABLE,
c_dest.COLUMN_NAME as DEST_COLUMN,
c_list.DELETE_RULE as ON_DELETE
FROM ALL_CONSTRAINTS c_list, ALL_CONS_COLUMNS c_src, ALL_CONS_COLUMNS c_dest
WHERE c_list.CONSTRAINT_NAME = c_src.CONSTRAINT_NAME
AND c_list.R_CONSTRAINT_NAME = c_dest.CONSTRAINT_NAME
AND c_list.CONSTRAINT_TYPE = 'R'
AND c_src.TABLE_NAME = ".q($Q);foreach(get_rows($F)as$I)$H[$I['NAME']]=array("db"=>$I['DEST_DB'],"table"=>$I['DEST_TABLE'],"source"=>array($I['SRC_COLUMN']),"target"=>array($I['DEST_COLUMN']),"on_delete"=>$I['ON_DELETE'],"on_update"=>null,);return$H;}function
truncate_tables(array$S){return
apply_queries("TRUNCATE TABLE",$S);}function
drop_views(array$Em){return
apply_queries("DROP VIEW",$Em);}function
drop_tables(array$S){return
apply_queries("DROP TABLE",$S);}function
last_id($G){return"0";}function
schemas(){$H=get_vals("SELECT DISTINCT owner FROM dba_segments WHERE owner IN (SELECT username FROM dba_users WHERE default_tablespace NOT IN ('SYSTEM','SYSAUX')) ORDER BY 1");return($H?:get_vals("SELECT DISTINCT owner FROM all_tables WHERE tablespace_name = ".q(DB)." ORDER BY 1"));}function
get_schema(){return
get_val("SELECT sys_context('USERENV', 'SESSION_USER') FROM dual");}function
set_schema($K,$g=null){return!!connection($g)->query("ALTER SESSION SET CURRENT_SCHEMA = ".idf_escape($K));}function
show_variables(){return
get_rows('SELECT name, display_value FROM v$parameter');}function
show_status(){$H=array();$J=get_rows('SELECT * FROM v$instance');foreach(reset($J)as$x=>$W)$H[]=array($x,$W);return$H;}function
process_list(){return
get_rows('SELECT
	sess.process AS "process",
	sess.username AS "user",
	sess.schemaname AS "schema",
	sess.status AS "status",
	sess.wait_class AS "wait_class",
	sess.seconds_in_wait AS "seconds_in_wait",
	sql.sql_text AS "sql_text",
	sess.machine AS "machine",
	sess.port AS "port"
FROM v$session sess LEFT OUTER JOIN v$sql sql
ON sql.sql_id = sess.sql_id
WHERE sess.type = \'USER\'
ORDER BY PROCESS
');}function
convert_field(array$m){}function
unconvert_field(array$m,$H){return$H;}function
support($zd){return
preg_match('~^(columns|database|drop_col|fast_status|indexes|descidx|processlist|scheme|sql|status|table|variables|view)$~',$zd);}}class
Adminer{static$instance;var$error='';function
name(){return"<a href='https://www.adminer.org/'".target_blank()." id='h1'><img src='".h(preg_replace("~\\?.*~","",ME)."?file=logo.png&version=6.0.2")."' width='24' height='24' alt='' id='logo'>Adminer</a>";}function
credentials(){return
array(SERVER,$_GET["username"],get_password());}function
connectSsl(){}function
permanentLogin($h=false){return
password_file($h);}function
bruteForceKey(){return$_SERVER["REMOTE_ADDR"];}function
serverName($M){return
h($M);}function
database(){return
DB;}function
databases($Qd=true){return
get_databases($Qd);}function
pluginsLinks(){}function
operators($Xk=null){return
driver()->operators($Xk);}function
schemas(){$H=schemas();if($_GET["ns"]!=""&&!in_array($_GET["ns"],$H))array_unshift($H,$_GET["ns"]);return$H;}function
queryTimeout(){return
2;}function
afterConnect(){}function
headers(){}function
csp(array$Xb){return$Xb;}function
verifyVersion(){return
true;}function
serviceWorker(){service_worker();}function
head($cc=null){return
true;}function
bodyClass(){echo" adminer";}function
css(){$H=array();foreach(array("","-dark")as$Rg){$o="adminer$Rg.css";if(file_exists($o)){$Ed=file_get_contents($o);$H["$o?v=".crc32($Ed)]=($Rg?"dark":(preg_match('~prefers-color-scheme:\s*dark~',$Ed)?'':'light'));}}return$H;}function
loginForm(){echo"<table class='layout'>\n",adminer()->loginFormField('driver','<tr><th>'.'System'.'<td>',html_select("auth[driver]",SqlDriver::$drivers,DRIVER,on('change','loginDriver'))),adminer()->loginFormField('server','<tr><th>'.'Server'.'<td>',"<input name='auth[server]' value='".h(SERVER)."' title='".'hostname[:port] or :socket'."' placeholder='localhost' autocapitalize='off'>"),adminer()->loginFormField('username','<tr><th>'.'Username'.'<td>','<input name="auth[username]" id="username" autofocus value="'.h($_GET["username"]).'" autocomplete="username" autocapitalize="off">'.script("fire(qs('#username').form['auth[driver]'], 'change');")),adminer()->loginFormField('password','<tr><th>'.'Password'.'<td>','<input type="password" name="auth[password]" autocomplete="current-password">'),adminer()->loginFormField('db','<tr><th>'.'Database'.'<td>','<input name="auth[db]" value="'.h($_GET["db"]).'" autocapitalize="off">'),"</table>\n","<p><input type='submit' value='".'Login'."'>\n",checkbox("auth[permanent]",1,$_COOKIE["adminer_permanent"],'Permanent login')."\n";}function
loginFormField($B,$Ee,$X){return$Ee.$X."\n";}function
login($jg,$E){if($E=="")return'Adminer does not support accessing a database without a password.'.require_password_link(null);if(!Driver::$passwords)return'The database does not support passwords.'.require_password_link($E);if(!password_required())return'The server accepts any password, so filling it in protects nothing.'.require_password_link($E);return
true;}function
tableName(array$Xk){return
h($Xk["Name"]);}function
fieldName(array$m,$Rh=0){$T=$m["full_type"].($m["null"]?" NULL":"");$Ab=$m["comment"];return'<span title="'.h($T.($Ab!=""?($T?": ":"").$Ab:'')).'">'.h($m["field"]).'</span>';}function
commentValue($T,$Ab){if($Ab==""||$T=='TABLE'||$T=='COLUMN')return
h($Ab);$Qi=function($Lj){return
preg_replace('~^~m','<tr>',preg_replace('~\|~','<td>',preg_replace('~\|$~m',"",rtrim($Lj))));};$Q='(\+--[-+]+\+\n)';$I='(\| .* \|\n)';return"<pre>\n".preg_replace_callback("~^$Q?$I$Q?($I*)$Q?~m",function($A)use($Qi){$Ld=$Qi($A[2]);return"<table>\n".($A[1]?"<thead>$Ld<tbody>\n":$Ld).$Qi($A[4])."\n</table>";},preg_replace('~(\n(    -|mysql)&gt; )(.+)~',"\\1<code class='jush-sql'>\\3</code>",preg_replace('~(.+)\n---+\n~',"<b>\\1</b>\n",h($Ab))))."</pre>\n";}function
commentInput($T,$c,$Ab){$X=h($Ab);return(preg_match('~\n~',$X)?"<textarea$c rows='2' cols='".($T=='TABLE'?20:30)."' style='vertical-align: bottom;'>\n$X</textarea>":"<input$c value='$X'>");}function
selectLinks(array$Xk,$N=""){$B=$Xk["Name"];echo'<p class="links">';$eg=array();if($B!="")$eg["select"]='Select data';if(support("table")||support("indexes"))$eg["table"]='Show structure';$Bf=false;if(support("table")){$Bf=is_view($Xk);if($Bf){if(support("view"))$eg["view"]='Alter view';}elseif(function_exists('Adminer\alter_table')&&$B!="")$eg["create"]='Alter table';}if($N!==null)$eg["edit"]='New item';foreach($eg
as$x=>$W)echo" <a href='".h(ME)."$x=".url_escape($B).($x=="edit"?$N:"")."'".bold(isset($_GET[$x])).">$W</a>";echo
doc_link(array(JUSH=>driver()->tableHelp($B,$Bf)),"?"),"\n";}function
foreignKeys($Q){return
foreign_keys($Q);}function
backwardKeys($Q,$Wk){return
array();}function
backwardKeysPrint(array$Ma,array$I){}function
selectQuery($F,$Kk,$xd=false){$H="\n";if(!$xd&&($Im=driver()->warnings())){$t="warnings";$H=", <a href='#$t' class='toggle'>".'Warnings'."</a>"."$H<div id='$t' class='hidden'>\n$Im</div>\n";}return"<p><code class='jush-".JUSH."'>".h(str_replace("\n"," ",$F))."</code> <span class='time'>(".format_time($Kk).")</span>".(support("sql")?" <a href='".h(ME)."sql=".url_escape($F)."' class='hover'>".'Edit'."</a>":"").$H;}function
sqlCommandQuery($F){return
shorten_utf8(trim($F),1000);}function
sqlPrintAfter(){}function
rowDescription($Q){return"";}function
rowDescriptions(array$J,array$Td){return$J;}function
selectLink($W,array$m){}function
selectVal($W,$_,array$m,$bi){$H=($W===null?"<i>NULL</i>":(preg_match("~char|binary|boolean~",$m["type"])&&!preg_match("~var~",$m["type"])?"<code>$W</code>":(preg_match('~^jsonb?$~',$m["full_type"])?"<code class='jush-json'>$W</code>":$W)));if(is_blob($m)&&!is_utf8($W))$H="<i>".lang_format(array('%d byte','%d bytes'),strlen($bi))."</i>";return($_?"<a href='".h($_)."'".(is_url($_)?target_blank():"").">$H</a>":$H);}function
editVal($W,array$m){return$W;}function
config(){return
array();}function
tableStructurePrint(array$n,$Xk=null){echo"<div class='scrollable'>\n","<table class='nowrap odds'>\n","<thead><tr><th>".'Column'."<td>".'Type'.(support("comment")?"<td>".'Comment':"")."<tbody>\n";$Ok=driver()->structuredTypes();foreach($n
as$m){echo"<tr><th>".h($m["field"]);$T=h($m["full_type"]);$vb=h($m["collation"]);echo"<td><span title='$vb'>".(in_array($T,(array)$Ok['User types'])?"<a href='".h(ME.'type='.url_escape($T))."'>$T</a>":$T.($vb&&isset($Xk["Collation"])&&$vb!=$Xk["Collation"]?" $vb":""))."</span>",($m["null"]?" <i>NULL</i>":""),($m["auto_increment"]?" <i>".'Auto Increment'."</i>":""),(isset($m["default"])?" <span title='".'Default value'."'>[<b>".($m["generated"]?"<code class='jush-".JUSH."'>".shorten_utf8(preg_replace('~\s+~',' ',ltrim($m["default"])),80,"</code>"):h($m["default"]))."</b>]</span>":""),(support("comment")?"<td>".adminer()->commentValue('COLUMN',$m["comment"]):""),"\n";}echo"</table>\n","</div>\n";}function
tableIndexesPrint(array$w,array$Xk){$oi=false;foreach($w
as$B=>$v)$oi|=!!$v["partial"];echo"<table>\n";$lc=first(driver()->indexAlgorithms($Xk));foreach($w
as$B=>$v){ksort($v["columns"]);$Yi=array();foreach($v["columns"]as$x=>$W)$Yi[]="<i>".h($W)."</i>".($v["lengths"][$x]?"(".h($v["lengths"][$x]).")":"").($v["descs"][$x]?" DESC":"");echo"<tr title='".h($B)."'>","<th>".h($v["type"]).($lc&&$v['algorithm']!=$lc?" (".h($v['algorithm']).")":""),"<td>".implode(", ",$Yi);if($oi)echo"<td>".($v['partial']?"<code class='jush-".JUSH."'>WHERE ".h($v['partial']):"");echo"\n";}echo"</table>\n";}function
selectColumnsPrint(array$L,array$e){print_fieldset("select",'Select',$L);$s=0;$L[""]=array();foreach($L
as$x=>$W){$W=idx($_GET["columns"],$x,array());$d=select_input(" name='columns[$s][col]' data-default=''".on('change',($x!==""?'selectFieldChange':'selectAddRow')),$e,$W["col"]);echo"<div>".(driver()->functions||driver()->grouping?html_select("columns[$s][fun]",array(-1=>"")+array_filter(array('Functions'=>driver()->functions,'Aggregation'=>driver()->grouping)),$W["fun"]," data-default=''".on('change',($x!==""?'helpClose':'selectFunAddRow')).on_help_value(' (.*)|$','($1)'))."($d)":$d)."</div>\n";$s++;}echo"</div></fieldset>\n";}function
selectSearchPrint(array$Z,array$e,array$w,$Xk=null){print_fieldset("search",'Search',$Z);foreach($w
as$s=>$v){if($v["type"]=="FULLTEXT")echo"<div>(<i>".implode("</i>, <i>",array_map('Adminer\h',$v["columns"]))."</i>) ".h(driver()->fulltextOperator)," <input type='search' name='fulltext[$s]' value='".h(idx($_GET["fulltext"],$s))."' data-default=''".on('input','selectFieldChange').">",(JUSH=='sql'?checkbox("boolean[$s]",1,isset($_GET["boolean"][$s]),"BOOL"):''),"</div>\n";}$Mh=adminer()->operators($Xk);foreach(array_merge((array)$_GET["where"],array(array()))as$s=>$W){if(!$W||("$W[col]$W[val]"!=""&&in_array($W["op"],$Mh)))echo"<div>".select_input(" name='where[$s][col]' data-default=''".on('change',($W?'selectFieldChange':'selectAddRow')),$e,$W["col"],"(".'anywhere'.")"),html_select("where[$s][op]",$Mh,$W["op"]," data-default='".h(first($Mh))."'".on('change','selectFirstChange')),"<input type='search' name='where[$s][val]' value='".h($W["val"])."' data-default=''".on('input','selectFirstChange').on('keydown','selectSearchKeydown').on('search','selectSearchSearch').">","</div>\n";}echo"</div></fieldset>\n";}function
selectOrderPrint(array$Rh,array$e,array$w){print_fieldset("sort",'Sort',$Rh);$s=0;foreach((array)$_GET["order"]as$x=>$W){if($W!=""){echo"<div>".select_input(" name='order[$s]' data-default=''".on('change','selectFieldChange'),$e,$W),checkbox("desc[$s]",1,isset($_GET["desc"][$x]),'descending')."</div>\n";$s++;}}echo"<div>".select_input(" name='order[$s]' data-default=''".on('change','selectAddRow'),$e),checkbox("desc[$s]",1,false,'descending')."</div>\n","</div></fieldset>\n";}function
selectLimitPrint($z){echo"<fieldset><legend>".'Limit'."</legend><div>","<input type='number' name='limit' class='size' value='".h($z?:"")."' data-default='50'".on('input','selectFieldChange').">","</div></fieldset>\n";}function
selectLengthPrint($ul){echo"<fieldset><legend>".'Text length'."</legend><div>","<input type='number' name='text_length' class='size' value='".h($ul)."' data-default='100'>","</div></fieldset>\n";}function
selectActionPrint(array$w){echo"<fieldset><legend>".'Action'."</legend><div>","<input type='submit' value='".'Select'."'>"," <span id='noindex' title='".'Full table scan'."'></span>","<script".nonce().">\n","const indexColumns = ";$e=array();foreach($w
as$v){$bc=reset($v["columns"]);if($v["type"]!="FULLTEXT"&&$bc)$e[$bc]=1;}$e[""]=1;foreach($e
as$x=>$W)json_row($x);echo";\n","selectFieldChange.call(qs('#form')['select']);\n","</script>\n","</div></fieldset>\n";}function
selectCommandPrint(){return!information_schema(DB);}function
selectImportPrint(){return!information_schema(DB);}function
selectEmailPrint(array$Vc,array$e){}function
selectColumnsProcess(array$e,array$w){$L=array();$r=array();foreach((array)$_GET["columns"]as$x=>$W){if($W["fun"]=="count"||($W["col"]!=""&&(!$W["fun"]||in_array($W["fun"],driver()->functions)||in_array($W["fun"],driver()->grouping)))){$L[$x]=apply_sql_function($W["fun"],($W["col"]!=""?idf_escape($W["col"]):"*"));if(!in_array($W["fun"],driver()->grouping))$r[]=$L[$x];}}return
array($L,$r);}function
selectSearchProcess(array$n,array$w,$Xk=null){$H=array();foreach($w
as$s=>$v){if($v["type"]=="FULLTEXT"&&idx($_GET["fulltext"],$s)!="")$H[]=driver()->fulltextSql($s,$v,$_GET["fulltext"][$s],isset($_GET["boolean"][$s]));}$Mh=adminer()->operators($Xk);foreach((array)$_GET["where"]as$x=>$W){$W+=array("col"=>"","op"=>first($Mh),"val"=>"");$_GET["where"][$x]=$W;$tb=$W["col"];if("$tb$W[val]"!=""&&in_array($W["op"],$Mh)){if($W["op"]=="SQL"&&(!$_POST||!verify_token()))SqlDb::$untrusted=true;$Fb=array();foreach(($tb!=""?array($tb=>$n[$tb]):$n)as$B=>$m){$Ri="";$Eb=" $W[op]";if(preg_match('~IN$~',$W["op"]))$Eb
.=" ".($W["val"]!=""?process_in($W["val"]):"(NULL)");elseif($W["op"]=="SQL")$Eb=" $W[val]";elseif(preg_match('~^(I?LIKE) %%$~',$W["op"],$A))$Eb=" $A[1] ".q("%$W[val]%");elseif($W["op"]=="FIND_IN_SET"){$Ri="$W[op](".q($W["val"]).", ";$Eb=")";}elseif(!preg_match('~NULL$~',$W["op"]))$Eb
.=" ".q($W["val"]);if($tb!=""||is_searchable($m,$W))$Fb[]=$Ri.driver()->convertSearch(idf_escape($B),$W,$m).$Eb;}$H[]=(count($Fb)==1?$Fb[0]:($Fb?"(".implode(" OR ",$Fb).")":"1 = 0"));}}return$H;}function
selectOrderProcess(array$n,array$w){$H=array();foreach((array)$_GET["order"]as$x=>$W){if($W!="")$H[]=(preg_match('~^((COUNT\(DISTINCT |[A-Z0-9_]+\()(`(?:[^`]|``)+`|"(?:[^"]|"")+")\)|COUNT\(\*\))$~',$W)?$W:idf_escape($W)).(isset($_GET["desc"][$x])?" DESC".(JUSH=='pgsql'&&idx($n[$W],"null")?" NULLS LAST":""):"");}return$H;}function
selectLimitProcess(){return(isset($_GET["limit"])?intval($_GET["limit"]):50);}function
selectLengthProcess(){return(isset($_GET["text_length"])?"$_GET[text_length]":"100");}function
selectEmailProcess(array$Z,array$Td){return
false;}function
selectQueryBuild(array$L,array$Z,array$r,array$Rh,$z,$D){return"";}function
messageQuery($F,$wl,$xd=false){restart_session();$Ie=&get_session("queries");if(!idx($Ie,$_GET["db"]))$Ie[$_GET["db"]]=array();if(strlen($F)>1e6)$F=preg_replace('~[\x80-\xFF]+$~','',substr($F,0,1e6))."\n…";$Ie[$_GET["db"]][]=array($F,time(),$wl);$Fk="sql-".count($Ie[$_GET["db"]]);$H="<a href='#$Fk' class='toggle'>".'SQL command'."</a> ".copy_icon()."\n";if(!$xd&&($Im=driver()->warnings())){$t="warnings-".count($Ie[$_GET["db"]]);$H="<a href='#$t' class='toggle'>".'Warnings'."</a>, $H<div id='$t' class='hidden'>\n$Im</div>\n";}return" <span class='time'>".@date("H:i:s")."</span>"." $H<div id='$Fk' class='hidden'><pre><code class='jush-".JUSH."'>".shorten_utf8($F,1e4)."</code></pre>".($wl?" <span class='time'>($wl)</span>":'').(support("sql")?'<p><a href="'.h(str_replace("db=".url_escape(DB),"db=".url_escape($_GET["db"]),ME).'sql=&history='.(count($Ie[$_GET["db"]])-1)).'">'.'Edit'.'</a>':'').'</div>';}function
error(){return
error();}function
editRowPrint($Q,array$n,$I,$im,$F='',$wl=''){echo($F!=""?"<p><code class='jush-".JUSH."'>".h(str_replace("\n"," ",$F))."</code> <span class='time'>($wl)</span>\n":"");}function
editFunctions(array$m){$H=($m["null"]?"NULL/":"");$Ae=isset($_GET["select"])||where($_GET);foreach(array(driver()->insertFunctions,driver()->editFunctions)as$x=>$he){if(!$x||(!isset($_GET["call"])&&$Ae)){foreach($he
as$Bi=>$W){if(!$Bi||preg_match("~$Bi~",$m["type"]))$H
.="/$W";}}if($x&&$he&&!preg_match('~set|bool~',$m["type"])&&!is_blob($m))$H
.="/SQL";}if($m["auto_increment"]&&!$Ae)$H='Auto Increment';return
explode("/",$H);}function
editInput($Q,array$m,$c,$X){if($m["type"]=="enum")return(isset($_GET["select"])?"<label><input type='radio'$c value='orig' checked><i>".'original'."</i></label> ":"").enum_input("radio",$c,$m,$X,"NULL");return"";}function
editHint($Q,array$m,$X){return"";}function
processInput(array$m,$X,$q=""){if($q=="SQL")return$X;$B=$m["field"];$H=q($X);if(preg_match('~^(now|getdate|uuid)$~',$q))$H="$q()";elseif(preg_match('~^current_(date|timestamp)$~',$q))$H=$q;elseif(preg_match('~^([+-]|\|\|)$~',$q))$H=idf_escape($B)." $q $H";elseif(preg_match('~^[+-] interval$~',$q))$H=idf_escape($B)." $q ".(preg_match("~^(\\d+|'[0-9.: -]') [A-Z_]+\$~i",$X)&&JUSH!="pgsql"?$X:$H);elseif(preg_match('~^(addtime|subtime|concat)$~',$q))$H="$q(".idf_escape($B).", $H)";elseif(preg_match('~^(md5|sha1|password|encrypt)$~',$q))$H="$q($H)";return
unconvert_field($m,$H);}function
dumpOutput(){$H=array('text'=>'open','file'=>'save');if(function_exists('gzencode'))$H['gz']='gzip';return$H;}function
dumpFormat(){return(support("dump")?array('sql'=>'SQL'):array())+array('csv'=>'CSV,','csv;'=>'CSV;','tsv'=>'TSV');}function
dumpPrint(){}function
dumpDatabase($j){}function
dumpTable($Q,$Pk,$Bf=0){if($_POST["format"]!="sql"){echo"\xef\xbb\xbf";if($Pk)dump_csv(array_keys(fields($Q)));}else{if($Bf==2){$n=array();foreach(fields($Q)as$B=>$m)$n[]=idf_escape($B)." $m[full_type]";$h="CREATE TABLE ".table($Q)." (".implode(", ",$n).")";}else$h=create_sql($Q,$_POST["auto_increment"],$Pk);set_utf8mb4($h);if($Pk&&$h){if(($Pk=="DROP+CREATE"&&!function_exists('Adminer\drop_sql'))||$Bf==1)echo"DROP ".($Bf==2?"VIEW":"TABLE")." IF EXISTS ".table($Q).";\n";if($Bf==1)$h=remove_definer($h);echo"$h;\n\n";}}}function
dumpData($Q,$Pk,$F,array$L=array(),array$Z=array(),array$r=array(),array$Rh=array()){if($Pk){$ug=(JUSH=="sqlite"?0:1048576);$n=array();$Re=false;if($_POST["format"]=="sql"){if($Pk=="TRUNCATE+INSERT"&&!function_exists('Adminer\truncate_all_sql'))echo
truncate_sql($Q).";\n";$n=fields($Q);if(JUSH=="mssql"){foreach($n
as$m){if($m["auto_increment"]){echo"SET IDENTITY_INSERT ".table($Q)." ON;\n";$Re=true;break;}}}}$G=($F!=""?connection()->query($F,1):driver()->select($Q,($L?:array("*")),$Z,$r,$Rh,0));if($G){$nf="";$Ya="";$If=array();$ie=array();$Rk="";$_d=($Q!=''?'fetch_assoc':'fetch_row');$Tb=0;while($I=$G->$_d()){if(!$If){$Y=array();foreach($I
as$W){$m=$G->fetch_field();if(idx($n[$m->name],'generated')){$ie[$m->name]=true;continue;}$If[]=$m->name;$x=idf_escape($m->name);$Y[]="$x = VALUES($x)";}$Rk=($Pk=="INSERT+UPDATE"?"\nON DUPLICATE KEY UPDATE ".implode(", ",$Y):"").";\n";}if($_POST["format"]!="sql"){if($Pk=="table"){dump_csv($If);$Pk="INSERT";}dump_csv($I);}else{if(!$nf)$nf="INSERT INTO ".table($Q)." (".implode(", ",array_map('Adminer\idf_escape',$If)).") VALUES";foreach($I
as$x=>$W){if($ie[$x]){unset($I[$x]);continue;}$m=$n[$x];$I[$x]=($W===null?"NULL":($W===false?0:unconvert_field($m,preg_match(number_type(),$m["type"])&&!preg_match('~\[~',$m["full_type"])&&is_numeric($W)?$W:(!is_blob($m)||is_utf8($W)?q($W):driver()->quoteBinary($W)))));}$Lj=($ug?"\n":" ")."(".implode(",\t",$I).")";if(!$Ya)$Ya=$nf.$Lj;elseif(JUSH=='mssql'?$Tb%1000!=0:strlen($Ya)+4+strlen($Lj)+strlen($Rk)<$ug)$Ya
.=",$Lj";else{echo$Ya.$Rk;$Ya=$nf.$Lj;}}$Tb++;}if($Ya)echo$Ya.$Rk;}elseif($_POST["format"]=="sql")echo"-- ".str_replace("\n"," ",connection()->error)."\n";if($Re)echo"SET IDENTITY_INSERT ".table($Q)." OFF;\n";}}function
dumpFilename($Pe){return
friendly_url($Pe!=""?$Pe:(SERVER?:"localhost"));}function
dumpHeaders($Pe,$Wg=false){$fi=$_POST["output"];$sd=(preg_match('~sql~',$_POST["format"])?"sql":($Wg?"tar":"csv"));header("Content-Type: ".($fi=="gz"?"application/x-gzip":($sd=="tar"?"application/x-tar":($sd=="sql"||$fi!="file"?"text/plain":"text/csv")."; charset=utf-8")));if($fi=="gz"){ob_start(function($P){return
gzencode($P);},1e6);}return$sd;}function
dumpFooter(){if($_POST["format"]=="sql")echo"-- ".gmdate("Y-m-d H:i:s e")."\n";}function
importServerPath(){return"adminer.sql";}function
importPrint(){}function
importProcess(){return
false;}function
homepage(){echo'<p class="links">'.($_GET["ns"]==""&&support("database")?'<a href="'.h(ME).'database=">'.'Alter database'."</a>\n":""),(support("scheme")?"<a href='".h(ME)."scheme='>".($_GET["ns"]!=""?'Alter schema':'Create schema')."</a>\n":""),($_GET["ns"]!==""?'<a href="'.h(ME).'schema=">'.'Database schema'."</a>\n":""),(support("privileges")?"<a href='".h(ME)."privileges='>".'Privileges'."</a>\n":"");if($_GET["ns"]!=="")echo(support("routine")?"<a href='#routines'>".'Routines'."</a>\n":""),(support("sequence")?"<a href='#sequences'>".'Sequences'."</a>\n":""),(support("type")?"<a href='#user-types'>".'User types'."</a>\n":""),(support("event")?"<a href='#events'>".'Events'."</a>\n":"");return
true;}function
navigation($Qg){echo"<h1>".adminer()->name()." <span class='version'>".VERSION;$lh=$_COOKIE["adminer_version"];echo" <a href='https://www.adminer.org/#download'".target_blank()." id='version'>".(version_compare(VERSION,$lh)<0?h($lh):"").version_iframe()."</a>","</span></h1>\n";if($Qg=="auth"){$fi="";foreach((array)$_SESSION["pwds"]as$Bm=>$mk){foreach($mk
as$M=>$vm){$B=h(get_setting("vendor-$Bm-$M")?:get_driver($Bm));foreach($vm
as$U=>$E){if($B&&$E!==null){$jc=$_SESSION["db"][$Bm][$M][$U];foreach(($jc?array_keys($jc):array(""))as$j)$fi
.="<li><a href='".h(auth_url($Bm,$M,$U,$j))."'>($B) ".h("$U@").($M!=""?adminer()->serverName($M):"").h($j!=""?" - $j":"")."</a>\n";}}}}if($fi)echo"<ul id='logins'".on('mouseover','menuOver').on('mouseout','menuOut').">\n$fi</ul>\n";}else{$S=array();if($_GET["ns"]!==""&&!$Qg&&DB!=""){connection()->select_db(DB);$S=table_status('',true);}adminer()->syntaxHighlighting($S);adminer()->databasesPrint($Qg);$ja=array();if(DB==""||!$Qg){if(support("sql")){$ja['sql']="<a href='".h(ME)."sql='".bold(isset($_GET["sql"])&&!isset($_GET["import"])).">".'SQL command'."</a>";$ja['import']="<a href='".h(ME)."import='".bold(isset($_GET["import"])).">".'Import'."</a>";}$ja['dump']="<a href='".h(ME)."dump=".url_escape(isset($_GET["table"])?$_GET["table"]:$_GET["select"])."' id='dump'".bold(isset($_GET["dump"])).">".'Export'."</a>";}$We=$_GET["ns"]!==""&&!$Qg&&DB!="";if($We&&function_exists('Adminer\alter_table'))$ja['create']='<a href="'.h(ME).'create="'.bold($_GET["create"]==="").">".'Create table'."</a>";$ja=adminer()->menuActions($ja,$Qg);echo($ja?"<p class='links'>\n".implode("\n",$ja)."\n":"");if($We){if($S)adminer()->tablesPrint($S);else
echo"<p class='message'>".'No tables.'."</p>\n";}}}function
syntaxHighlighting(array$S){echo
script_src(preg_replace("~\\?.*~","",ME)."?file=jush.js&version=6.0.2",true);$Sg=preg_replace('~<(?=/script)~i','<\\',Driver::jushModule());echo($Sg?script("addEventListener('DOMContentLoaded', () => {\n$Sg\n});"):"");if(support("sql")){echo"<script".nonce().">\n";if($S){$eg=array();foreach($S
as$Q=>$T)$eg[]=js_escape_re($Q);echo"var jushLinks = { ".JUSH.":";json_row(js_escape(ME).(support("table")?"table":"select").'=$&','/\b(?<!\$)('.implode('|',$eg).')(?!\$)\b/g',false);$Hk=array("sql","check","event","procedure","trigger","view","type","table","processlist");if(support("routine")&&array_intersect_key($_GET,array_flip($Hk))){foreach(routines()as$I)json_row(js_escape(ME).'function='.url_escape($I["SPECIFIC_NAME"]).'&name=$&','/\b'.js_escape_re($I["ROUTINE_NAME"]).'(?=["`\]]?\()/g',false);}json_row('');echo"};\n";foreach(array("bac","bra","sqlite_quo","mssql_bra")as$W)echo"jushLinks.$W = jushLinks.".JUSH.";\n";if(array_intersect_key($_GET,array_flip(array("sql","check","event","procedure","trigger","view")))){$Lk=(isset($_GET["trigger"])?array('INSERT INTO','UPDATE','DELETE FROM'):(isset($_GET["check"])?array():(isset($_GET["view"])?array('SELECT'):null)));$Ia=Driver::jushAutocomplete($S,$Lk);echo($Ia?"addEventListener('DOMContentLoaded', () => { autocompleter = $Ia; });\n":"");}}echo"</script>\n";}echo
script("syntaxHighlighting('".doc_version()."', '".connection()->flavor."');");}function
databasesPrint($Qg){if(support("single_db"))return;$i=adminer()->databases();if(DB&&$i&&!in_array(DB,$i))array_unshift($i,DB);echo"<form action=''>\n<p id='dbs'>\n";hidden_fields_get();$hc=on('mousedown','dbMouseDown').on('change','dbChange');echo"<label title='".'Database'."'>".'DB'.": ".($i?html_select("db",array(""=>"")+$i,DB,$hc):"<input name='db' value='".h(DB)."' autocapitalize='off' size='19'>\n")."</label>","<input type='submit' value='".'Use'."'".($i?" class='hidden'":"").">\n";if(support("scheme")){if($Qg!="db"&&DB!=""&&connection()->select_db(DB)){echo"<br><label>".'Schema'.": ".html_select("ns",array(""=>"")+adminer()->schemas(),$_GET["ns"],$hc)."</label>";if($_GET["ns"]!="")set_schema($_GET["ns"]);}}foreach(array("import","sql","schema","dump","privileges")as$W){if(isset($_GET[$W])){echo
input_hidden($W);break;}}echo"</p></form>\n";}function
menuActions(array$ja,$Qg){return$ja;}function
tablesPrint(array$S){echo"<ul id='tables'".on('mouseover','menuOver').on('mouseout','menuOut').">";foreach($S
as$Q=>$O){$Q="$Q";$B=adminer()->tableName($O);if($B!=""&&!$O["dependent"])echo'<li><a href="'.h(ME).'select='.url_escape($Q).'"'.bold($_GET["select"]==$Q||$_GET["edit"]==$Q,"select hover")." title='".'Select data'."'>".'select'."</a> ",(support("table")||support("indexes")?'<a href="'.h(ME).'table='.url_escape($Q).'"'.bold(in_array($Q,array($_GET["table"],$_GET["create"],$_GET["indexes"],$_GET["foreign"],$_GET["trigger"],$_GET["check"],$_GET["view"])),(is_view($O)?"view":"structure"))." title='".'Show structure'."'>$B</a>":"<span>$B</span>")."\n";}echo"</ul>\n";}function
showVariables(){return
show_variables();}function
showStatus(){return
show_status();}function
processList(){return
process_list();}function
killProcess($t){return
kill_process($t);}}class
Plugins{private
static$append=array('dumpFormat'=>true,'dumpOutput'=>true,'editRowPrint'=>true,'editFunctions'=>true,'config'=>true);var$plugins;var$drivers=array();var$driverFiles=array();var$error='';private$hooks=array();function
__construct($Ii){$Jc=SqlDriver::$drivers;$Ge=" href='https://www.adminer.org/plugins/#use'".target_blank();if($Ii===null){$Ii=array();$Qa="adminer-plugins";if(is_dir($Qa)){foreach(glob("$Qa/*.php")as$o){$Fd=SqlDriver::$drivers;$this->includeOnce($o);foreach(array_diff_key(SqlDriver::$drivers,$Fd)as$t=>$B)$this->driverFiles[$t]=$o;}}if(file_exists("$Qa.php")){$Ye=$this->includeOnce("$Qa.php");if(is_array($Ye)){foreach($Ye
as$x=>$Fi)$Ii[is_object($Fi)?get_class($Fi):$x]=$Fi;}else$this->error
.=sprintf('%s must <a%s>return an array</a>.',"<b>$Qa.php</b>",$Ge)."<br>";}foreach(get_declared_classes()as$qb){if(!$Ii[$qb]&&(preg_match('~^Adminer\w~i',$qb)||is_subclass_of($qb,'Adminer\Plugin'))){$rj=new
\ReflectionClass($qb);$Lb=$rj->getConstructor();if($Lb&&$Lb->getNumberOfRequiredParameters())$this->error
.=sprintf('<a%s>Configure</a> %s in %s.',$Ge,"<b>$qb</b>","<b>$Qa.php</b>")."<br>";else$Ii[$qb]=new$qb;}}}$sf=array_filter($Ii,function($Fi){return!is_object($Fi);});if($sf){$this->error
.=sprintf('Every plugin must <a%s>be an object</a>.',$Ge)."<br>";$Ii=array_diff_key($Ii,$sf);}$this->drivers=array_diff_key(SqlDriver::$drivers,$Jc);$this->plugins=$Ii;$oa=new
Adminer;$Ii[]=$oa;$rj=new
\ReflectionObject($oa);foreach($rj->getMethods()as$Ng){foreach($Ii
as$Fi){$B=$Ng->getName();if(method_exists($Fi,$B))$this->hooks[$B][]=$Fi;}}}function
includeOnce($o){return
include_once"./$o";}static
function
checksum($o){$Ed=str_replace("\r","",file_get_contents($o));$Ed=preg_replace('~\n\tprotected \$translations = array\(.*?\n\t\);~s','',$Ed);return
dechex(crc32($Ed));}function
checksums(){$Gd=array_values($this->driverFiles);foreach($this->plugins
as$Fi){$rj=new
\ReflectionObject($Fi);$Gd[]=$rj->getFileName();}$H=array();foreach($Gd
as$o)$H[basename($o,'.php')]=self::checksum($o);return$H;}static
function
officialChecksums(){return
array('adminer.js'=>'a0599090','backward-keys'=>'ed1ef78f','before-unload'=>'2a613523','config'=>'722eb4af','dark-switcher'=>'3d490dea','database-hide'=>'e304a899','designs'=>'ed7e44e3','dump-alter'=>'896b579e','dump-bz2'=>'f0d0e336','dump-date'=>'adc7f1c7','dump-json'=>'767dd321','dump-xml'=>'4fc3cd60','dump-zip'=>'93817d96','edit-foreign'=>'72ad1562','edit-textarea'=>'a24c3cc','editor-setup'=>'a7dc3a37','editor-views'=>'5c12b185','enum-option'=>'1e24970e','file-upload'=>'10add0e8','foreign-system'=>'ebb4c654','frames'=>'b0e1d11a','highlight-codemirror'=>'c5716555','highlight-monaco'=>'edd1b0af','highlight-prism'=>'267948e5','import-csv'=>'d429c77','login-ip'=>'4d174fea','login-otp'=>'5b5a68af','login-passkey'=>'f69f2f06','login-password-less'=>'e150daac','login-reverse-proxy'=>'24558ea2','login-servers'=>'19c42e45','login-ssl'=>'6ed147bc','login-table'=>'811f8cef','menu-links'=>'c78461b3','remote-color'=>'ddeecc48','row-numbers'=>'eec8698c','select-email'=>'f84fbd2c','select-image'=>'f55c0231','slugify'=>'dec64713','sql-gemini'=>'c60ab309','sql-log'=>'8e435000','table-indexes-structure'=>'a90cc0c9','table-structure'=>'a8458e02','tables-filter'=>'ec2bcd6e','timeout'=>'97321caf','version-github'=>'627cadf9','version-noverify'=>'966937e9','clickhouse'=>'c66e1af6','elastic'=>'da03fb2a','firebird'=>'2f32108a','igdb'=>'ac7fbeff','imap'=>'c9dd2dd6','mongo'=>'f33a5c03','redis'=>'8603c834','simpledb'=>'1ef5b158',);}function
__call($B,array$li){$Aa=array();foreach($li
as$x=>$W)$Aa[]=&$li[$x];$H=null;foreach($this->hooks[$B]as$Fi){$X=call_user_func_array(array($Fi,$B),$Aa);if($X!==null){if(!self::$append[$B])return$X;$H=$X+(array)$H;}}return$H;}}abstract
class
Plugin{protected$translations=array();function
description(){return$this->lang('');}function
screenshot(){return"";}protected
function
lang($u,$rh=null){$Aa=func_get_args();$Aa[0]=idx($this->translations[LANG],$u)?:$u;return
call_user_func_array('Adminer\lang_format',$Aa);}}class
Password{private$password_hash;private$password_matches=null;function
__construct($yi){$this->password_hash=$yi;}function
description(){return'Require a password verified by Adminer';}function
credentials(){$E=get_password();return
array(SERVER,$_GET["username"],($this->passwordMatches($E)&&!password_required()?"":$E));}function
login($jg,$E){if($this->passwordMatches($E))return
true;}protected
function
passwordMatches($E){if($this->password_matches===null)$this->password_matches=(function_exists('password_verify')&&password_verify(strval($E),$this->password_hash));return$this->password_matches;}}Adminer::$instance=(function_exists('adminer_object')?adminer_object():(is_dir("adminer-plugins")||file_exists("adminer-plugins.php")?new
Plugins(null):new
Adminer));SqlDriver::$drivers=array("server"=>"MySQL / MariaDB")+SqlDriver::$drivers;if(!defined('Adminer\DRIVER')){define('Adminer\DRIVER',"server");if(extension_loaded("mysqli")&&$_GET["ext"]!="pdo"){class
Db
extends
\mysqli{static$instance;var$extension="MySQLi",$flavor='';function
__construct(){parent::init();}function
attach(array$M,$U,$E){mysqli_report(MYSQLI_REPORT_OFF);$Ji=$M["port"];$Xc=("$M[host]$Ji$M[socket]"=="");$Jk=adminer()->connectSsl();$rm=($Jk&&($Jk['key']||$Jk['cert']||$Jk['ca']||isset($Jk['verify'])));if($rm)$this->ssl_set($Jk['key'],$Jk['cert'],$Jk['ca'],'','');$H=@$this->real_connect((!$Xc?$M["host"]:ini_get("mysqli.default_host")),(!$Xc||$U!=""?$U:ini_get("mysqli.default_user")),(!$Xc||$U.$E!=""?$E:ini_get("mysqli.default_pw")),null,($Ji!=""?intval($Ji):ini_get("mysqli.default_port")),($Ji!=""?null:$M["socket"]),($rm?($Jk['verify']!==false?MYSQLI_CLIENT_SSL:64):0));$this->options(MYSQLI_OPT_LOCAL_INFILE,0);return($H?'':$this->error);}function
set_charset($gb){if(parent::set_charset($gb))return
true;parent::set_charset('utf8');return$this->query("SET NAMES $gb");}function
next_result(){return
self::more_results()&&parent::next_result();}function
quote($P){return"'".$this->escape_string($P)."'";}function
inTransaction(){return
false;}}}elseif(extension_loaded("mysql")&&!((ini_bool("sql.safe_mode")||ini_bool("mysql.allow_local_infile"))&&extension_loaded("pdo_mysql"))){class
Db
extends
SqlDb{private$link;function
attach(array$M,$U,$E){if(ini_bool("mysql.allow_local_infile"))return
sprintf('Disable %s or enable the %s or %s extension.',"'mysql.allow_local_infile'","MySQLi","PDO_MySQL");$Ji="$M[port]$M[socket]";$B=$M["host"].($Ji!=""?":$Ji":"");$this->link=@mysql_connect(($B!=""?$B:ini_get("mysql.default_host")),($B.$U!=""?$U:ini_get("mysql.default_user")),($B.$U.$E!=""?$E:ini_get("mysql.default_password")),true,131072);if(!$this->link)return
mysql_error();$this->server_info=mysql_get_server_info($this->link);return'';}function
set_charset($gb){return
mysql_set_charset($gb,$this->link)||mysql_set_charset('utf8',$this->link);}function
quote($P){return"'".mysql_real_escape_string($P,$this->link)."'";}function
select_db($gc){return
mysql_select_db($gc,$this->link);}function
query($F,$Yl=false){$G=@($Yl?mysql_unbuffered_query($F,$this->link):mysql_query($F,$this->link));$this->error="";if(!$G){$this->errno=mysql_errno($this->link);$this->error=mysql_error($this->link);return
false;}if($G===true){$this->affected_rows=mysql_affected_rows($this->link);$this->info=mysql_info($this->link);return
true;}return
new
Result($G);}}class
Result{var$num_rows;private$result;private$offset=0;function
__construct($G){$this->result=$G;$this->num_rows=mysql_num_rows($G);}function
fetch_assoc(){return
mysql_fetch_assoc($this->result);}function
fetch_row(){return
mysql_fetch_row($this->result);}function
fetch_field(){$H=mysql_fetch_field($this->result,$this->offset++);$H->orgtable=$H->table;$H->charsetnr=($H->blob?63:0);return$H;}}}elseif(extension_loaded("pdo_mysql")){class
Db
extends
PdoDb{var$extension="PDO_MySQL";function
attach(array$M,$U,$E){$C=array(\PDO::MYSQL_ATTR_LOCAL_INFILE=>false);if(isset($_GET["select"]))$C[\PDO::MYSQL_ATTR_MULTI_STATEMENTS]=false;$Jk=adminer()->connectSsl();if($Jk){if($Jk['key'])$C[\PDO::MYSQL_ATTR_SSL_KEY]=$Jk['key'];if($Jk['cert'])$C[\PDO::MYSQL_ATTR_SSL_CERT]=$Jk['cert'];if($Jk['ca'])$C[\PDO::MYSQL_ATTR_SSL_CA]=$Jk['ca'];if(isset($Jk['verify']))$C[\PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT]=$Jk['verify'];}$Le=$M["host"];$Ji=$M["port"];$wk=$M["socket"];return$this->dsn("mysql:charset=utf8".($Le!=""?";host=$Le":'').($Ji!=""?";port=$Ji":($wk!=""?";unix_socket=$wk":"")),$U,$E,$C);}function
set_charset($gb){return$this->query("SET NAMES $gb");}function
select_db($gc){return$this->query("USE ".idf_escape($gc));}function
query($F,$Yl=false){$this->pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,!$Yl);return
parent::query($F,$Yl);}}}class
Driver
extends
SqlDriver{static$extensions=array("MySQLi","MySQL","PDO_MySQL");static$jush="sql";static$serverSocket=true;var$unsigned=array("unsigned","zerofill","unsigned zerofill");var$functions=array("char_length","date","from_unixtime","lower","round","floor","ceil","sec_to_time","time_to_sec","upper");var$grouping=array("avg","count","count distinct","group_concat","max","min","sum");var$partitionBy=array("HASH","LINEAR HASH","KEY","LINEAR KEY","RANGE","LIST");function
operators($Xk){return
array("=","<",">","<=",">=","!=","LIKE","LIKE %%","REGEXP","IN","FIND_IN_SET","IS NULL","NOT LIKE","NOT REGEXP","NOT IN","IS NOT NULL","SQL");}static
function
connect($M,$U,$E){$f=parent::connect($M,$U,$E);if(is_string($f)){if(function_exists('iconv')&&!is_utf8($f)&&strlen($Lj=iconv("windows-1252","utf-8//IGNORE",$f))>strlen($f))$f=$Lj;return$f;}$f->set_charset(charset($f));$f->query("SET sql_quote_show_create = 1, autocommit = 1");$f->flavor=(preg_match('~MariaDB~',$f->server_info)?'maria':'mysql');add_driver(DRIVER,($f->flavor=='maria'?"MariaDB":"MySQL"));return$f;}function
__construct(Db$f){parent::__construct($f);$this->types=array('Numbers'=>array("tinyint"=>3,"smallint"=>5,"mediumint"=>8,"int"=>10,"bigint"=>20,"decimal"=>66,"float"=>12,"double"=>21),'Date and time'=>array("date"=>10,"datetime"=>19,"timestamp"=>19,"time"=>10,"year"=>4),'Strings'=>array("char"=>255,"varchar"=>65535,"tinytext"=>255,"text"=>65535,"mediumtext"=>16777215,"longtext"=>4294967295),'Lists'=>array("enum"=>65535,"set"=>64),'Binary'=>array("bit"=>20,"binary"=>255,"varbinary"=>65535,"tinyblob"=>255,"blob"=>65535,"mediumblob"=>16777215,"longblob"=>4294967295),'Geometry'=>array("geometry"=>0,"point"=>0,"linestring"=>0,"polygon"=>0,"multipoint"=>0,"multilinestring"=>0,"multipolygon"=>0,"geometrycollection"=>0),);$this->insertFunctions=array("char"=>"md5/sha1/password/encrypt/uuid","binary"=>"md5/sha1","date|time"=>"now",);$this->editFunctions=array(number_type()=>"+/-","date"=>"+ interval/- interval","time"=>"addtime/subtime","char|text"=>"concat",);if(min_version('5.7.8',10.2,$f))$this->types['Strings']["json"]=4294967295;if(min_version('',10.7,$f)){$this->types['Strings']["uuid"]=128;$this->insertFunctions['uuid']='uuid';}if(min_version('',10.5,$f)){$this->types['Network']["inet6"]=39;if(min_version('','10.10',$f))$this->types['Network']["inet4"]=15;}if(min_version(9,11.7,$f))$this->types['Numbers']["vector"]=16383;if(min_version(5.7,10.2,$f))$this->generated=array("STORED","VIRTUAL");}function
unconvertFunction(array$m){return(preg_match("~binary~",$m["type"])?"<code class='jush-sql'>UNHEX</code>":($m["type"]=="bit"?doc_link(array('sql'=>'bit-value-literals.html'),"<code>b''</code>"):($m["type"]=="vector"?"<code class='jush-sql'>".($this->conn->flavor=='maria'?"VEC_FromText":"STRING_TO_VECTOR")."</code>":(preg_match("~geom|point|linestring|polygon~",$m["type"])?"<code class='jush-sql'>GeomFromText</code>":""))));}function
insert($Q,array$N){return($N?parent::insert($Q,$N):queries("INSERT INTO ".table($Q)." ()\nVALUES ()"));}function
insertUpdate($Q,array$J,array$Wi){$e=array_keys(reset($J));$Ri="INSERT INTO ".table($Q)." (".implode(", ",$e).") VALUES\n";$Y=array();foreach($e
as$x)$Y[$x]="$x = VALUES($x)";$Rk="\nON DUPLICATE KEY UPDATE ".implode(", ",$Y);$Y=array();$y=0;foreach($J
as$N){$X="(".implode(", ",$N).")";if($Y&&(strlen($Ri)+$y+strlen($X)+strlen($Rk)>1e6)){if(!queries($Ri.implode(",\n",$Y).$Rk))return
false;$Y=array();$y=0;}$Y[]=$X;$y+=strlen($X)+2;}return
queries($Ri.implode(",\n",$Y).$Rk);}function
slowQuery($F,$xl){if(min_version('5.7.8','10.1.2')){if($this->conn->flavor=='maria')return"SET STATEMENT max_statement_time=$xl FOR $F";elseif(preg_match('~^(SELECT\b)(.+)~is',$F,$A))return"$A[1] /*+ MAX_EXECUTION_TIME(".($xl*1000).") */ $A[2]";}}function
convertColumn($u,array$m){if(preg_match("~binary~",$m["type"]))return"HEX($u)";if($m["type"]=="bit")return"BIN($u + 0)";if($m["type"]=="vector")return($this->conn->flavor=='maria'?"VEC_ToText":"VECTOR_TO_STRING")."($u)";if(preg_match("~geom|point|linestring|polygon~",$m["type"]))return(min_version(8)?"ST_":"")."AsWKT($u)";return"";}function
convertSearch($u,array$W,array$m){return($this->convertColumn($u,$m)?:(preg_match('~'.text_type().'~',$m["type"])&&!preg_match("~^utf8~",$m["collation"])&&preg_match('~[\x80-\xFF]~',$W['val'])?"CONVERT($u USING ".charset($this->conn).")":$u));}function
typeName(\stdClass$m){$Xl=array("decimal","tinyint","smallint","int","float","double",7=>"timestamp","bigint","mediumint","date","time","datetime","year",15=>"varchar","bit",242=>"vector",245=>"json","decimal","enum","set","tinytext","mediumtext","longtext","text","varchar","char","geometry",);$H=idx($Xl,$m->type,"");return
parent::typeName($m)?:($m->charsetnr==63?str_replace(array("text","varchar","char"),array("blob","varbinary","binary"),$H):$H);}function
quoteBinary($Lj){return"X".q(bin2hex($Lj));}function
warnings(){$G=$this->conn->query("SHOW WARNINGS");if($G&&$G->num_rows){ob_start();print_select_result($G);return
ob_get_clean();}}function
tableHelp($B,$Bf=false){$lg=($this->conn->flavor=='maria');if(information_schema(DB))return
strtolower(str_replace("_","-",DB)."-".($lg?"$B-table/":str_replace("_","-",$B)."-table.html"));if(DB=="sys")return($lg?"sys-schema/":strtolower("sys-".str_replace("_","-",preg_replace('~^x\$~','',$B)).".html"));if(DB=="mysql")return($lg?"mysql$B-table/":"system-schema.html");}function
partitionsInfo($Q){$be="FROM information_schema.PARTITIONS WHERE TABLE_SCHEMA = ".q(DB)." AND TABLE_NAME = ".q($Q);$G=$this->conn->query("SELECT PARTITION_METHOD, PARTITION_EXPRESSION, PARTITION_ORDINAL_POSITION $be ORDER BY PARTITION_ORDINAL_POSITION DESC LIMIT 1");$I=($G?$G->fetch_row():null);if(!$I)return
array();$H=array();list($H["partition_by"],$H["partition"],$H["partitions"])=$I;$ui=get_key_vals("SELECT PARTITION_NAME, PARTITION_DESCRIPTION $be AND PARTITION_NAME != '' ORDER BY PARTITION_ORDINAL_POSITION");$H["partition_names"]=array_keys($ui);$H["partition_values"]=array_values($ui);return$H;}function
checkConstraints($Q){$H=parent::checkConstraints($Q);return($this->conn->flavor=='maria'?$H:array_map('stripslashes',$H));}function
hasCStyleEscapes(){static$bb;if($bb===null){$Gk=get_val("SHOW VARIABLES LIKE 'sql_mode'",1,$this->conn);$bb=(strpos($Gk,'NO_BACKSLASH_ESCAPES')===false);}return$bb;}function
lineComment(){return"#|-- ";}function
engines(){$H=array();foreach(get_rows("SHOW ENGINES")as$I){if(preg_match("~YES|DEFAULT~",$I["Support"]))$H[]=$I["Engine"];}return$H;}function
indexAlgorithms(array$Xk){return(preg_match('~^(MEMORY|NDB)$~',$Xk["Engine"])?array("HASH","BTREE"):array());}}function
idf_escape($u){return"`".str_replace("`","``",$u)."`";}function
table($u){return
idf_escape($u);}function
get_databases($Qd){$H=get_session("dbs");if($H===null){$F="SELECT SCHEMA_NAME FROM information_schema.SCHEMATA ORDER BY SCHEMA_NAME";$Kk=microtime(true);$H=($Qd?slow_query($F):get_vals($F));if(microtime(true)-$Kk>0.1){restart_session();set_session("dbs",$H);stop_session();}}return$H;}function
limit($F,$Z,$z,$yh=0,$ak=" "){return" $F$Z".($z?$ak."LIMIT $z".($yh?" OFFSET $yh":""):"");}function
limit1($Q,$F,$Z,$ak="\n"){return
limit($F,$Z,1,0,$ak);}function
db_collation($j,array$wb){$H=null;$h=get_val("SHOW CREATE DATABASE ".idf_escape($j),1);if(preg_match('~ COLLATE ([^ ]+)~',$h,$A))$H=$A[1];elseif(preg_match('~ CHARACTER SET ([^ ]+)~',$h,$A))$H=$wb[$A[1]][-1];return$H;}function
logged_user(){return
get_val("SELECT USER()");}function
tables_list(){return
get_key_vals("SELECT TABLE_NAME, TABLE_TYPE FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() ORDER BY TABLE_NAME");}function
count_tables(array$i){$H=array();foreach($i
as$j)$H[$j]=count(get_vals("SHOW TABLES IN ".idf_escape($j)));return$H;}function
table_status($B="",$yd=false){$H=array();$F="SELECT ENGINE AS Engine, TABLE_NAME AS Name, TABLE_COMMENT AS Comment FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() ".($B!=""?"AND TABLE_NAME = ".q($B):"ORDER BY Name");$K=array();foreach(($yd?array():get_rows($F))as$I)$K[$I["Name"]]=$I;$Vi=null;foreach(get_rows($yd?$F:"SHOW TABLE STATUS".($B!=""?" LIKE ".q(addcslashes($B,"%_\\")):""))as$I){$bi=idx($K,$I["Name"]);if($bi){if($I["Comment"]!==$bi["Comment"]&&$I["Comment"]!==$Vi)$I["Error"]=$I["Comment"];$Vi=$I["Comment"];$I["Comment"]=$bi["Comment"];$I["Engine"]=$bi["Engine"];}if($I["Engine"]=="InnoDB")$I["Comment"]=preg_replace('~(?:(.+); )?InnoDB free: .*~','\1',$I["Comment"]);if(!isset($I["Engine"]))$I["Comment"]="";if($B!="")$I["Name"]=$B;$H[$I["Name"]]=$I;}return$H;}function
is_view(array$R){return$R["Engine"]===null;}function
fk_support(array$R){return
preg_match('~InnoDB|IBMDB2I'.(min_version(5.6)?'|NDB':'').'~i',$R["Engine"]);}function
parse_type($ee){preg_match('~^([^( ]+)(?:\((.+)\))?( unsigned)?( zerofill)?$~',$ee,$A);return
array($A[1],$A[2],ltrim($A[3].$A[4]));}function
fields($Q){$lg=(connection()->flavor=='maria');$H=array();foreach(get_rows("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ".q($Q)." ORDER BY ORDINAL_POSITION")as$I){$m=$I["COLUMN_NAME"];$T=$I["COLUMN_TYPE"];$je=$I["GENERATION_EXPRESSION"];$vd=$I["EXTRA"];preg_match('~^(VIRTUAL|PERSISTENT|STORED)~',$vd,$ie);list($Wl,$y,$gm)=parse_type($T);$k=$I["COLUMN_DEFAULT"];if($k!=""){$Af=preg_match('~text|json~',$Wl);if(!$lg&&$Af)$k=preg_replace("~^(_\w+)?('.*')$~",'\2',stripslashes($k));if($lg||$Af){$k=($k=="NULL"?null:preg_replace_callback("~^'(.*)'$~",function($A){return
stripslashes(str_replace("''","'",$A[1]));},$k));}if(!$lg&&preg_match('~binary~',$Wl)&&preg_match('~^0x(\w*)$~',$k,$A))$k=pack("H*",$A[1]);}$H[$m]=array("field"=>$m,"full_type"=>$T,"type"=>$Wl,"length"=>$y,"unsigned"=>$gm,"default"=>($ie?($lg?$je:stripslashes($je)):$k),"null"=>($I["IS_NULLABLE"]=="YES"),"auto_increment"=>($vd=="auto_increment"),"on_update"=>(preg_match('~\bon update (\w+)~i',$vd,$A)?$A[1]:""),"collation"=>$I["COLLATION_NAME"],"privileges"=>array_flip(explode(",","$I[PRIVILEGES],where,order")),"comment"=>$I["COLUMN_COMMENT"],"primary"=>($I["COLUMN_KEY"]=="PRI"),"generated"=>($ie[1]=="PERSISTENT"?"STORED":$ie[1]),);}return$H;}function
indexes($Q,$g=null){$H=array();foreach(get_rows("SHOW INDEX FROM ".table($Q),$g)as$I){$B=$I["Key_name"];$H[$B]["type"]=($B=="PRIMARY"?"PRIMARY":($I["Index_type"]=="FULLTEXT"?"FULLTEXT":($I["Non_unique"]?(preg_match('~^(SPATIAL|VECTOR)$~',$I["Index_type"])?$I["Index_type"]:"INDEX"):"UNIQUE")));$H[$B]["columns"][]=$I["Column_name"];$H[$B]["lengths"][]=($I["Index_type"]=="SPATIAL"?null:$I["Sub_part"]);$H[$B]["descs"][]=null;$H[$B]["algorithm"]=$I["Index_type"];}return$H;}function
foreign_keys($Q){static$Bi='(?:`(?:[^`]|``)+`|"(?:[^"]|"")+")';$H=array();$Ub=get_val("SHOW CREATE TABLE ".table($Q),1);if($Ub){preg_match_all("~CONSTRAINT ($Bi) FOREIGN KEY ?\\(((?:$Bi,? ?)+)\\) REFERENCES ($Bi)(?:\\.($Bi))? \\(((?:$Bi,? ?)+)\\)(?: ON DELETE (".driver()->onActions."))?(?: ON UPDATE (".driver()->onActions."))?~",$Ub,$og,PREG_SET_ORDER);foreach($og
as$A){preg_match_all("~$Bi~",$A[2],$_k);preg_match_all("~$Bi~",$A[5],$nl);$H[idf_unescape($A[1])]=array("db"=>idf_unescape($A[4]!=""?$A[3]:$A[4]),"table"=>idf_unescape($A[4]!=""?$A[4]:$A[3]),"source"=>array_map('Adminer\idf_unescape',$_k[0]),"target"=>array_map('Adminer\idf_unescape',$nl[0]),"on_delete"=>($A[6]?:"RESTRICT"),"on_update"=>($A[7]?:"RESTRICT"),);}}return$H;}function
view($B){return
array("select"=>preg_replace('~^(?:[^`]|`[^`]*`)*\s+AS\s+~isU','',get_val("SHOW CREATE VIEW ".table($B),1)));}function
collations(){$H=array();foreach(get_rows("SHOW COLLATION")as$I){if($I["Default"])$H[$I["Charset"]][-1]=$I["Collation"];else$H[$I["Charset"]][]=$I["Collation"];}ksort($H);foreach($H
as$x=>$W)sort($H[$x]);return$H;}function
information_schema($j,$K=""){return($j=="information_schema")||(min_version(5.5)&&$j=="performance_schema");}function
error(){return
h(preg_replace('~^You have an error.*syntax to use~U',"Syntax error",connection()->error));}function
create_database($j,$vb){return
queries("CREATE DATABASE ".idf_escape($j).($vb?" COLLATE ".q($vb):""));}function
drop_databases(array$i){$H=apply_queries("DROP DATABASE",$i,'Adminer\idf_escape');restart_session();set_session("dbs",null);return$H;}function
rename_database($B,$vb){$H=false;if(create_database($B,$vb)){$S=array();$Em=array();foreach(tables_list()as$Q=>$T){if($T=='VIEW')$Em[]=$Q;else$S[]=$Q;}$H=(!$S&&!$Em)||move_tables($S,$Em,$B);drop_databases($H?array(DB):array());}return$H;}function
auto_increment(){$Ha=" PRIMARY KEY";if($_GET["create"]!=""&&$_POST["auto_increment_col"]){foreach(indexes($_GET["create"])as$v){if(in_array($_POST["fields"][$_POST["auto_increment_col"]]["orig"],$v["columns"],true)){$Ha="";break;}if($v["type"]=="PRIMARY")$Ha=" UNIQUE";}}return" AUTO_INCREMENT$Ha";}function
alter_table($Q,$B,array$n,array$Sd,$Ab,$Yc,$vb,$Ga,$ti){$b=array();foreach($n
as$m){if($m[1]){$k=$m[1][3];if(preg_match('~ GENERATED~',$k)){$m[1][3]=(connection()->flavor=='maria'?"":$m[1][2]);$m[1][2]=$k;}$b[]=($Q!=""?($m[0]!=""?"CHANGE ".idf_escape($m[0]):"ADD"):" ")." ".implode($m[1]).($Q!=""?$m[2]:"");}else$b[]="DROP ".idf_escape($m[0]);}$b=array_merge($b,$Sd);$O=($Ab!==null?" COMMENT=".q($Ab):"").($Yc?" ENGINE=".q($Yc):"").($vb?" COLLATE ".q($vb):"").($Ga!=""?" AUTO_INCREMENT=$Ga":"");if($ti){$ui=array();if($ti["partition_by"]=='RANGE'||$ti["partition_by"]=='LIST'){foreach($ti["partition_names"]as$x=>$W){$X=$ti["partition_values"][$x];$ui[]="\n  PARTITION ".idf_escape($W)." VALUES ".($ti["partition_by"]=='RANGE'?"LESS THAN":"IN").($X!=""?" ($X)":" MAXVALUE");}}$O
.="\nPARTITION BY $ti[partition_by]($ti[partition])";if($ui)$O
.=" (".implode(",",$ui)."\n)";elseif($ti["partitions"])$O
.=" PARTITIONS ".(+$ti["partitions"]);}elseif($ti===null)$O
.="\nREMOVE PARTITIONING";if($Q=="")return
queries("CREATE TABLE ".table($B)." (\n".implode(",\n",$b)."\n)$O");if($Q!=$B)$b[]="RENAME TO ".table($B);if($O)$b[]=ltrim($O);return($b?queries("ALTER TABLE ".table($Q)."\n".implode(",\n",$b)):true);}function
alter_indexes($Q,$b){$eb=array();foreach($b
as$W)$eb[]=($W[2]=="DROP"?"\nDROP INDEX ".idf_escape($W[1]):"\nADD $W[0] ".($W[0]=="PRIMARY"?"KEY ":"").($W[1]!=""?idf_escape($W[1])." ":"")."(".implode(", ",$W[2]).")");return
queries("ALTER TABLE ".table($Q).implode(",",$eb));}function
truncate_tables(array$S){return
apply_queries("TRUNCATE TABLE",$S);}function
drop_views(array$Em){return
queries("DROP VIEW ".implode(", ",array_map('Adminer\table',$Em)));}function
drop_tables(array$S){return
queries("DROP TABLE ".implode(", ",array_map('Adminer\table',$S)));}function
move_tables(array$S,array$Em,$nl){$wj=array();foreach($S
as$Q)$wj[]=table($Q)." TO ".idf_escape($nl).".".table($Q);if(!$wj||queries("RENAME TABLE ".implode(", ",$wj))){$qc=array();foreach($Em
as$Q)$qc[table($Q)]=view($Q);connection()->select_db($nl);$j=idf_escape(DB);foreach($qc
as$B=>$Dm){if(!queries("CREATE VIEW $B AS ".str_replace(" $j."," ",$Dm["select"]))||!queries("DROP VIEW $j.$B"))return
false;}return
true;}return
false;}function
copy_tables(array$S,array$Em,$nl){queries("SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO'");foreach($S
as$Q){$B=($nl==DB?table("copy_$Q"):idf_escape($nl).".".table($Q));if(($_POST["overwrite"]&&!queries("\nDROP TABLE IF EXISTS $B"))||!queries("CREATE TABLE $B LIKE ".table($Q))||!queries("INSERT INTO $B SELECT * FROM ".table($Q)))return
false;foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($Q,"%_\\")))as$I){$Nl=$I["Trigger"];list($hd,$uh)=trigger_event($I);if(!queries("CREATE TRIGGER ".($nl==DB?idf_escape("copy_$Nl"):idf_escape($nl).".".idf_escape($Nl))." $I[Timing] $hd".($uh!=""?" $uh":"")." ON $B FOR EACH ROW\n$I[Statement];"))return
false;}}foreach($Em
as$Q){$B=($nl==DB?table("copy_$Q"):idf_escape($nl).".".table($Q));$Dm=view($Q);if(($_POST["overwrite"]&&!queries("DROP VIEW IF EXISTS $B"))||!queries("CREATE VIEW $B AS $Dm[select]"))return
false;}return
true;}function
trigger_event(array$I){$jd=explode(",",$I["Event"]);$H=array();foreach(array("DELETE","INSERT","UPDATE")as$hd){if(in_array($hd,$jd))$H[]=$hd;}$H=implode(" OR ",$H);if(in_array("UPDATE",$jd)&&min_version('','12.0.1')&&preg_match('~\s(?:BEFORE|AFTER)\s+(.+?)\s+ON\s~is',get_val("SHOW CREATE TRIGGER ".idf_escape($I["Trigger"]),2),$A)&&preg_match('~\bOF\s+(.+)~is',$A[1],$uh))return
array("$H OF",$uh[1]);return
array($H,"");}function
trigger($B,$Q){if($B=="")return
array();$J=get_rows("SHOW TRIGGERS WHERE `Trigger` = ".q($B));$H=reset($J);if($H)list($H["Event"],$H["Of"])=trigger_event($H);return$H;}function
triggers($Q){$H=array();foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($Q,"%_\\")))as$I){list($hd)=trigger_event($I);$H[$I["Trigger"]]=array($I["Timing"],$hd);}return$H;}function
trigger_options(){return
array("Timing"=>array("BEFORE","AFTER"),"Event"=>(min_version('','12.0.1')?array("INSERT","UPDATE","UPDATE OF","DELETE","INSERT OR UPDATE","INSERT OR UPDATE OF","DELETE OR INSERT","DELETE OR UPDATE","DELETE OR UPDATE OF","DELETE OR INSERT OR UPDATE","DELETE OR INSERT OR UPDATE OF",):array("INSERT","UPDATE","DELETE")),"Type"=>array("FOR EACH ROW"),);}function
routine($B,$T){$J=get_rows("SELECT PARAMETER_NAME, DTD_IDENTIFIER, PARAMETER_MODE, COLLATION_NAME
FROM information_schema.PARAMETERS
WHERE SPECIFIC_SCHEMA = DATABASE() AND ROUTINE_TYPE = '$T' AND SPECIFIC_NAME = ".q($B)."
ORDER BY ORDINAL_POSITION");$n=array();foreach($J
as$I){$ee=$I["DTD_IDENTIFIER"];list($Wl,$y,$gm)=parse_type($ee);$n[]=array("field"=>$I["PARAMETER_NAME"],"type"=>$Wl,"length"=>$y,"unsigned"=>$gm,"null"=>true,"full_type"=>$ee,"inout"=>($T=="FUNCTION"?"":$I["PARAMETER_MODE"]),"collation"=>$I["COLLATION_NAME"],);}$H=(array)connection()->query("SELECT
	ROUTINE_COMMENT comment,
	ROUTINE_DEFINITION definition,
	LOWER(EXTERNAL_LANGUAGE) language,
	IF(DEFINER = CURRENT_USER(), '', DEFINER) definer,
	IF(IS_DETERMINISTIC = 'YES', 'DETERMINISTIC', 'NOT DETERMINISTIC') is_deterministic,
	SQL_DATA_ACCESS data_access,
	CONCAT('SQL SECURITY ', SECURITY_TYPE) security
FROM information_schema.ROUTINES
WHERE ROUTINE_SCHEMA = DATABASE() AND ROUTINE_TYPE = '$T' AND ROUTINE_NAME = ".q($B))->fetch_assoc();$H['options']=array("DEFINER"=>$H['definer'],"DETERMINISTIC"=>$H['is_deterministic'],"SQL_DATA_ACCESS"=>$H['data_access'],"SQL_SECURITY"=>$H['security'],"COMMENT"=>$H['comment'],);if($n&&$n[0]['field']=='')$H['returns']=array_shift($n);$H['fields']=$n;return$H;}function
routines(){return
get_rows("SELECT SPECIFIC_NAME, ROUTINE_NAME, ROUTINE_TYPE, DTD_IDENTIFIER FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = DATABASE()");}function
routine_languages(){return(min_version(9,99)?array("sql"=>"sql","javascript"=>"js"):array());}function
routine_options($Fj){return
array("DEFINER"=>array(),"DETERMINISTIC"=>array("NOT DETERMINISTIC","DETERMINISTIC"),"SQL_DATA_ACCESS"=>array("CONTAINS SQL","NO SQL","READS SQL DATA","MODIFIES SQL DATA"),"SQL_SECURITY"=>array("SQL SECURITY DEFINER","SQL SECURITY INVOKER"),"COMMENT"=>array(),);}function
routine_id($B,array$I){return
idf_escape($B);}function
last_id($G){return
get_val("SELECT LAST_INSERT_ID()");}function
explain(Db$f,$F){return$f->query("EXPLAIN ".(min_version(5.7)?"":"PARTITIONS ").$F);}function
found_rows(array$R,array$Z){return($Z||$R["Engine"]!="InnoDB"?null:$R["Rows"]);}function
create_sql($Q,$Ga,$Pk){$H=get_val("SHOW CREATE TABLE ".table($Q),1);if(!$Ga)$H=preg_replace('~(\n\)[^\n]*?) AUTO_INCREMENT=\d+~','\1',$H);return$H;}function
truncate_sql($Q){return"TRUNCATE ".table($Q);}function
use_sql($gc,$Pk=""){$B=idf_escape($gc);$H="";if(preg_match('~CREATE~',$Pk)&&($h=get_val("SHOW CREATE DATABASE $B",1))){set_utf8mb4($h);if($Pk=="DROP+CREATE")$H="DROP DATABASE IF EXISTS $B;\n";$H
.="$h;\n";}return$H."USE $B";}function
trigger_sql($Q){$H="";foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($Q,"%_\\")),null,"-- ")as$I){list($I["Event"],$I["Of"])=trigger_event($I);$H
.="\n".create_trigger(" ON ".table($I["Table"]),$I+array("Type"=>"FOR EACH ROW")).";\n";}return$H;}function
show_variables(){return
get_rows("SHOW VARIABLES");}function
show_status(){return
get_rows("SHOW STATUS");}function
process_list(){return
get_rows("SHOW FULL PROCESSLIST");}function
convert_field(array$m){return
driver()->convertColumn(idf_escape($m["field"]),$m);}function
unconvert_field(array$m,$H){if(preg_match("~binary~",$m["type"]))$H="UNHEX($H)";if($m["type"]=="bit")$H="CONVERT(b$H, UNSIGNED)";if($m["type"]=="vector")$H=(connection()->flavor=='maria'?"VEC_FromText":"STRING_TO_VECTOR")."($H)";if(preg_match("~geom|point|linestring|polygon~",$m["type"])){$Ri=(min_version(8)?"ST_":"");$H=$Ri."GeomFromText($H, $Ri"."SRID($m[field]))";}return$H;}function
support($zd){return
preg_match('~^(comment|columns|copy|database|drop_col|dump|event|indexes|kill|privileges|move_col|procedure|processlist|routine|sql|status|table|trigger|variables|view'.(min_version(8)?'|descidx':'').(min_version('8.0.16','10.2.1')?'|check':'').(min_version(8,99)?'|fast_status':'').')$~',$zd);}function
kill_process($t){return
queries("KILL ".number($t));}function
connection_id(){return"SELECT CONNECTION_ID()";}function
max_connections(){return
get_val("SELECT @@max_connections");}function
types($ud=false){return
array();}function
type_values($t){return"";}function
type_definition($t){return
array("kind"=>"","definition"=>"");}function
schemas(){return
array();}function
get_schema(){return"";}function
set_schema($K,$g=null){return
true;}}define('Adminer\JUSH',Driver::$jush);define('Adminer\SERVER',"".$_GET[DRIVER]);define('Adminer\DB',"$_GET[db]");define('Adminer\ME',preg_replace('~\?.*~','',relative_uri()).'?'.(sid()?SID.'&':'').($_GET["ext"]?"ext=".url_escape($_GET["ext"]).'&':'').(isset($_GET[DRIVER])?DRIVER."=".url_escape(SERVER).'&':'').(isset($_GET["username"])?"username=".url_escape($_GET["username"]).'&':'').(isset($_GET["db"])?'db='.url_escape(DB).'&'.(isset($_GET["ns"])?"ns=".url_escape($_GET["ns"])."&":""):''));function
page_header($zl,$l="",$Xa=array(),$_l=""){page_headers();if(is_ajax()&&$l){page_messages($l);exit;}if(!ob_get_level())ob_start('ob_gzhandler',4096);$Al=$zl.($_l!=""?": $_l":"");$Bl=strip_tags($Al.(SERVER!=""&&SERVER!="localhost"?h(" - ".SERVER):"")." - ".adminer()->name());echo'<!DOCTYPE html>
<html lang=\'en\' dir=\'ltr\' class=\'ltr nojs\'>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="robots" content="noindex">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>',$Bl,'</title>
<link rel="stylesheet" href="',h(preg_replace("~\\?.*~","",ME)."?file=default.css&version=6.0.2"),'">
';$Yb=adminer()->css();if(is_int(key($Yb)))$Yb=array_fill_keys($Yb,'light');$ye=in_array('light',$Yb)||in_array('',$Yb);$we=in_array('dark',$Yb)||in_array('',$Yb);$cc=($ye?($we?null:false):($we?:null));$Cg=" media='(prefers-color-scheme: dark)'";if($cc!==false)echo"<link rel='stylesheet'".($cc?"":$Cg)." href='".h(preg_replace("~\\?.*~","",ME)."?file=dark.css&version=6.0.2")."'>\n";echo"<meta name='color-scheme' content='".($cc===null?"light dark":($cc?"dark":"light"))."'>\n",script_src(preg_replace("~\\?.*~","",ME)."?file=functions.js&version=6.0.2");if(adminer()->head($cc))echo"<link rel='icon' href='data:image/gif;base64,"."R0lGODlhEAAQAJEAAAQCBPz+/PwCBAROZCH5BAEAAAAALAAAAAAQABAAAAI2hI+pGO1rmghihiUdvUBnZ3XBQA7f05mOak1RWXrNq5nQWHMKvuoJ37BhVEEfYxQzHjWQ5qIAADs='>\n","<link rel='apple-touch-icon' href='".h(preg_replace("~\\?.*~","",ME)."?file=logo.png&version=6.0.2")."'>\n";foreach($Yb
as$mm=>$Rg){$c=($Rg=='dark'&&!$cc?$Cg:($Rg=='light'&&$we?" media='(prefers-color-scheme: light)'":""));echo"<link rel='stylesheet'$c href='".h($mm)."'>\n";}echo"\n<body class='";adminer()->bodyClass();echo"'>\n",script((isset($_COOKIE["adminer_version"])||!adminer()->verifyVersion()?"":"onload = partial(verifyVersion, '".VERSION."');\n")."
const offlineMessage = '".js_escape('You are offline.')."';
const numberFormat = '".js_escape('#,##0')."';
const numberDigits = '".js_escape('0123456789')."';
const urlSeparators = '".js_escape(ini_get("arg_separator.input"))."';"),"<div id='help' class='jush-".JUSH." jsonly hidden'".on('mouseover','helpKeep').on('mouseout','helpMouseout')."></div>\n","<div id='content'>\n","<span id='menuopen' class='jsonly'".on('click','menuToggle')."><button title='".'Menu'."' class='icon icon-move' aria-expanded='false'></button></span>\n";if($Xa!==null){$_=substr(preg_replace('~\b(username|db|ns)=[^&]*&~','',ME),0,-1);echo'<p id="breadcrumb"><a href="'.h($_?:".").'">'.get_driver(DRIVER).'</a> » ';$_=substr(preg_replace('~\b(db|ns)=[^&]*&~','',ME),0,-1);$M=adminer()->serverName(SERVER);$M=($M!=""?$M:'Server');if($Xa===false)echo"$M\n";else{echo"<a href='".h($_.(DB!=""&&support("single_db")?"&db=":""))."' accesskey='1' title='Alt+Shift+1'>$M</a> » ";if($_GET["ns"]!=""||(DB!=""&&is_array($Xa)))echo'<a href="'.h($_."&db=".url_escape(DB).(support("scheme")?"&ns=":"").(support("single_table")?"&select=":"")).'">'.h(DB).'</a> » ';if(is_array($Xa)){if($_GET["ns"]!="")echo'<a href="'.h(substr(ME,0,-1)).'">'.h($_GET["ns"]).'</a> » ';foreach($Xa
as$x=>$W){$sc=(is_array($W)?$W[1]:h($W));if($sc!="")echo"<a href='".h(ME."$x=").url_escape(is_array($W)?$W[0]:$W)."'>$sc</a> » ";}}echo"$zl\n";}}echo"<h2>$Al</h2>\n","<div id='ajaxstatus' role='status' class='jsonly'></div>\n";restart_session();page_messages($l);adminer()->serviceWorker();$i=&get_session("dbs");if(DB!=""&&$i&&!in_array(DB,$i,true))$i=null;stop_session();define('Adminer\PAGE_HEADER',1);ob_flush();flush();}function
service_worker(){$sb=(has_passwords()?"navigator.serviceWorker.register('".js_escape(preg_replace('~\?.*~','',ME)."?file=worker.js&version=".VERSION)."', {scope: location.pathname}).catch(() => {});":"navigator.serviceWorker.getRegistration().then(registration => registration && registration.unregister());
	caches.keys().then(keys => keys.forEach(key => key.startsWith('adminer-') && caches.delete(key)));");echo
script("if (navigator.serviceWorker) {\n\t$sb\n}");}function
has_passwords(){foreach((array)$_SESSION["pwds"]as$mk){foreach($mk
as$vm){foreach($vm
as$E){if($E!==null)return
true;}}}return
false;}function
page_headers(){header("Content-Type: text/html; charset=utf-8");header("Cache-Control: no-cache");header("X-Frame-Options: deny");header("X-XSS-Protection: 0");header("X-Content-Type-Options: nosniff");header("Referrer-Policy: origin-when-cross-origin");foreach(adminer()->csp(csp())as$Xb){$Ce=array();foreach($Xb
as$x=>$W)$Ce[]="$x $W";header("Content-Security-Policy: ".implode("; ",$Ce));}adminer()->headers();}function
csp(){return
array(array("script-src"=>"'self' 'unsafe-inline' 'nonce-".get_nonce()."' 'strict-dynamic'","connect-src"=>"'self' https://www.adminer.org","frame-src"=>"https://www.adminer.org","object-src"=>"'none'","base-uri"=>"'none'","form-action"=>"'self'",),);}function
design_checksums(){$sm=array();foreach(array_keys(adminer()->css())as$mm)$sm[preg_replace('~\?.*~','',$mm)]=true;$H=array();foreach(array("adminer.css","adminer-dark.css")as$o){if($sm[$o]&&file_exists($o)){preg_match('~^/\* Adminer design ([-\w]+) \*/~',file_get_contents($o),$A);$H[$o]=array((string)$A[1],Plugins::checksum($o));}}return$H;}function
official_design_checksums(){return
array('adminer-border/adminer.css'=>'ec757f3e','adminer-dark/adminer-dark.css'=>'a26bcd7b','brade/adminer.css'=>'be4161f0','bueltge/adminer.css'=>'1a8f00b4','cpanel/adminer.css'=>'59ce604e','dracula/adminer-dark.css'=>'cfaf61dd','esterka/adminer.css'=>'1f805f36','flat/adminer.css'=>'49a61af9','galkaev/adminer-dark.css'=>'16c46f94','haeckel/adminer.css'=>'147a3565','hever/adminer.css'=>'ef0e1948','konya/adminer.css'=>'2b409696','lavender-light/adminer.css'=>'bf03f5d7','lucas-sandery/adminer.css'=>'6596353','mancave/adminer-dark.css'=>'e1ac813d','mvt/adminer.css'=>'ebd3afdc','nette/adminer.css'=>'5ab360e7','ng9/adminer.css'=>'488583cf','nicu/adminer.css'=>'ecb9bd1e','pappu687/adminer.css'=>'b58d128c','paranoiq/adminer.css'=>'64d27e5','pepa-linha/adminer.css'=>'baf25f0','pokorny/adminer.css'=>'ee9eea6d','price/adminer.css'=>'81be9a85','rmsoft/adminer.css'=>'6cd4a237','rmsoft_blue-dark/adminer.css'=>'32102a8','rmsoft_blue/adminer.css'=>'7d8d5b18','win98/adminer.css'=>'e82d63c3',);}function
version_iframe(){return(isset($_COOKIE["adminer_version"])||!adminer()->verifyVersion()?"":"<noscript><iframe sandbox src='https://www.adminer.org/version/?current=".VERSION."&amp;noscript=1'></iframe></noscript>");}function
get_nonce(){static$nh;if(!$nh)$nh=base64_encode(rand_string());return$nh;}function
page_messages($l){$lm=preg_replace('~^[^?]*~','',$_SERVER["REQUEST_URI"]);$Jg=idx($_SESSION["messages"],$lm);if($Jg){echo"<div class='message'>".implode("</div>\n<div class='message'>",$Jg)."</div>".script("messagesPrint();");unset($_SESSION["messages"][$lm]);}if($l)echo"<div class='error'>$l</div>\n";if(adminer()->error)echo"<div class='error'>".adminer()->error."</div>\n";}function
page_footer($Qg=""){echo"</div>\n\n<div id='foot' class='foot'>\n<div id='menu'>\n";adminer()->navigation($Qg);echo"</div>\n";if($Qg!="auth")echo'<form action="" method="post">
<p class="logout">
<span title="Username">',h($_GET["username"])."\n",'</span>
<input type=\'submit\' name=\'logout\' value=\'Logout\' id=\'logout\'>
',input_token(),'</form>
';echo"</div>\n\n",script("setupSubmitHighlight(document);");}function
int32($Yg){while($Yg>=2147483648)$Yg-=4294967296;while($Yg<=-2147483649)$Yg+=4294967296;return(int)$Yg;}function
long2str(array$V,$Gm){$Lj='';foreach($V
as$W)$Lj
.=pack('V',$W);if($Gm)return
substr($Lj,0,end($V));return$Lj;}function
str2long($Lj,$Gm){$V=array_values(unpack('V*',str_pad($Lj,4*ceil(strlen($Lj)/4),"\0")));if($Gm)$V[]=strlen($Lj);return$V;}function
xxtea_mx($Rm,$Qm,$Sk,$Gf){return
int32((($Rm>>5&0x7FFFFFF)^$Qm<<2)+(($Qm>>3&0x1FFFFFFF)^$Rm<<4))^int32(($Sk^$Qm)+($Gf^$Rm));}function
encrypt_string($Nk,$x){if($Nk=="")return"";$x=array_values(unpack("V*",pack("H*",md5($x))));$V=str2long($Nk,true);$Yg=count($V)-1;$Rm=$V[$Yg];$Qm=$V[0];$fj=floor(6+52/($Yg+1));$Sk=0;while($fj-->0){$Sk=int32($Sk+0x9E3779B9);$Qc=$Sk>>2&3;for($hi=0;$hi<$Yg;$hi++){$Qm=$V[$hi+1];$Xg=xxtea_mx($Rm,$Qm,$Sk,$x[$hi&3^$Qc]);$Rm=int32($V[$hi]+$Xg);$V[$hi]=$Rm;}$Qm=$V[0];$Xg=xxtea_mx($Rm,$Qm,$Sk,$x[$hi&3^$Qc]);$Rm=int32($V[$Yg]+$Xg);$V[$Yg]=$Rm;}return
long2str($V,false);}function
decrypt_string($Nk,$x){if($Nk=="")return"";if(!$x)return
false;$x=array_values(unpack("V*",pack("H*",md5($x))));$V=str2long($Nk,false);$Yg=count($V)-1;$Rm=$V[$Yg];$Qm=$V[0];$fj=floor(6+52/($Yg+1));$Sk=int32($fj*0x9E3779B9);while($Sk){$Qc=$Sk>>2&3;for($hi=$Yg;$hi>0;$hi--){$Rm=$V[$hi-1];$Xg=xxtea_mx($Rm,$Qm,$Sk,$x[$hi&3^$Qc]);$Qm=int32($V[$hi]-$Xg);$V[$hi]=$Qm;}$Rm=$V[$Yg];$Xg=xxtea_mx($Rm,$Qm,$Sk,$x[$hi&3^$Qc]);$Qm=int32($V[0]-$Xg);$V[0]=$Qm;$Sk=int32($Sk-0x9E3779B9);}return
long2str($V,true);}$Di=array();if($_COOKIE["adminer_permanent"]){foreach(explode(" ",$_COOKIE["adminer_permanent"])as$W){list($x)=explode(":",$W);$Di[$x]=$W;}}function
add_invalid_login(){$Oa=get_temp_dir()."/adminer-invalid";foreach(glob("$Oa*")?:array($Oa)as$o){$Yd=file_open_lock($o);if($Yd)break;}if(!$Yd)$Yd=file_open_lock("$Oa-".rand_string());if(!$Yd)return;$uf=json_decode(stream_get_contents($Yd),true);$wl=time();if($uf){foreach($uf
as$vf=>$W){if($W[0]<$wl)unset($uf[$vf]);}}$sf=&$uf[adminer()->bruteForceKey()];if(!$sf)$sf=array($wl+30*60,0);$sf[1]++;file_write_unlock($Yd,json_encode($uf));}function
check_invalid_login(array&$Di){$uf=array();foreach(glob(get_temp_dir()."/adminer-invalid*")as$o){$Yd=file_open_lock($o);if($Yd){$uf=json_decode(stream_get_contents($Yd),true);file_unlock($Yd);break;}}$x=adminer()->bruteForceKey();$sf=idx($uf,$x,array());$mh=($sf[1]>29?$sf[0]-time():0);if($mh>0){$l=lang_format(array('Too many unsuccessful logins, try again in %d minute.','Too many unsuccessful logins, try again in %d minutes.'),ceil($mh/60));if($_SERVER["HTTP_X_FORWARDED_FOR"]!=""&&$x==$_SERVER["REMOTE_ADDR"])$l
.='<br>'.sprintf('Use the %s <a%s>plugin</a> if Adminer runs behind a reverse proxy.','<b>login-reverse-proxy</b>'," href='https://www.adminer.org/plugins/?version=".VERSION."'".target_blank());auth_error($l,$Di,false);}}function
password_required(){static$H;if($H===null){$H=(bool)get_session("password_required");if(!$H){$Wb=adminer()->credentials();$H=!is_object(Driver::connect($Wb[0],$Wb[1],""));if($H)set_session("password_required",true);}}return$H;}function
require_password_link($E){$Tg="<a href='https://www.adminer.org/password/'".target_blank().">".'More options'."</a>";if(!function_exists('password_hash'))return" $Tg";$Gi=($E!==null?$E:base64_encode(substr(pack("H*",rand_string()),0,12)));$Be=password_hash($Gi,PASSWORD_DEFAULT);$o="adminer-plugins.php";$od=file_exists("adminer-plugins.php");if($od)$qf=($E!==null?sprintf('Add this line to %s to require the entered password:',"<b>$o</b>"):sprintf('Add this line to %s to require the password %s:',"<b>$o</b>","<b>$Gi</b>"));else{$o="<button name='password_less' value='".h($Be)."' class='link'>$o</button>";$qf=($E!==null?sprintf('Save %s next to Adminer to require the entered password:',$o):sprintf('Save %s next to Adminer to require the password %s:',$o,"<b>$Gi</b>"));}$cg="\t<a>new</a> Adminer\\Password(<span class='jush-apo'>'".h($Be)."'</span>),";$H="<p>$qf
<pre><code class='jush'>".($od?$cg:"&lt;?php\n<a>return</a> <a>array</a>(\n$cg\n);")."</code></pre>
<p>$Tg
";return" <a href='#password-less' class='toggle'>".'Require a password.'."</a>
<div id='password-less' class='hidden'>".($od?$H:"<form action='' method='post'>\n".$H.input_token()."</form>")."</div>";}if(preg_match('~^[-\w$./]+$~',$_POST["password_less"])&&verify_token()){header("Content-Type: application/octet-stream");header("Content-Disposition: attachment; filename=adminer-plugins.php");echo"<?php\nreturn array(\n\tnew Adminer\\Password('$_POST[password_less]'),\n);\n";exit;}$Fa=$_POST["auth"];if($Fa&&verify_token()){session_regenerate_id();$Bm=$Fa["driver"];$M=$Fa["server"];$U=$Fa["username"];$E=(string)$Fa["password"];$j=$Fa["db"];set_password($Bm,$M,$U,$E);$_SESSION["db"][$Bm][$M][$U][$j]=true;if($Fa["permanent"]){$x=implode("-",array_map('base64_encode',array($Bm,$M,$U,$j)));$Zi=adminer()->permanentLogin(true);$Di[$x]="$x:".base64_encode($Zi?encrypt_string($E,$Zi):"");cookie("adminer_permanent",implode(" ",$Di));}if(!array_diff(array_keys($_POST),array("auth","token"))||$Bm!=DRIVER||$M!=SERVER||$U!==$_GET["username"]||$j!=DB)redirect(auth_url($Bm,$M,$U,$j));}elseif($_POST["logout"]&&(!$_SESSION["token"]||verify_token())){foreach(array("pwds","db","dbs","queries")as$x)set_session($x,null);unset_permanent($Di);redirect(substr(preg_replace('~\b(username|db|ns)=[^&]*&~','',ME),0,-1),'Logout successful.'.' '.'Thanks for using Adminer. Consider <a href="https://www.adminer.org/en/donation/">donating</a>.');}elseif($Di&&!$_SESSION["pwds"]){session_regenerate_id();$Zi=adminer()->permanentLogin();foreach($Di
as$x=>$W){list(,$pb)=explode(":",$W);list($Bm,$M,$U,$j)=array_map('base64_decode',explode("-",$x));set_password($Bm,$M,$U,decrypt_string(base64_decode($pb),$Zi));$_SESSION["db"][$Bm][$M][$U][$j]=true;}}function
unset_permanent(array&$Di){foreach($Di
as$x=>$W){list($Bm,$M,$U,$j)=array_map('base64_decode',explode("-",$x));if($Bm==DRIVER&&$M==SERVER&&$U==$_GET["username"]&&$j==DB)unset($Di[$x]);}cookie("adminer_permanent",implode(" ",$Di));}function
auth_error($l,array&$Di,$tf=true){$nk=session_name();if(isset($_GET["username"])){header("HTTP/1.1 403 Forbidden");if(($_COOKIE[$nk]||$_GET[$nk])&&!$_SESSION["token"])$l='Session expired. Please log in again.';elseif($tf&&($E=get_password())!==null){restart_session();add_invalid_login();if($E===false)$l
.=($l?'<br>':'').sprintf('Master password expired. <a href="https://www.adminer.org/en/extension/"%s>Implement</a> the %s method to make it permanent.',target_blank(),'<code>permanentLogin()</code>');set_password(DRIVER,SERVER,$_GET["username"],null);unset_permanent($Di);}}if(!$_COOKIE[$nk]&&$_GET[$nk]&&ini_bool("session.use_only_cookies"))$l='Session support must be enabled.';$li=session_get_cookie_params();cookie("adminer_key",($_COOKIE["adminer_key"]?:rand_string()),$li["lifetime"]);if(!$_SESSION["token"])$_SESSION["token"]=rand(1,1e6);page_header('Login',$l,null);echo"<form action='' method='post'>\n","<div>";if(hidden_fields($_POST,array("auth","token")))echo"<p class='message'>".'The action will be performed after successful login with the same credentials.'."\n";echo
input_token(),"</div>\n";adminer()->loginForm();echo"</form>\n";page_footer("auth");exit;}if(isset($_GET["username"])&&!class_exists('Adminer\Db')){unset($_SESSION["pwds"][DRIVER]);unset_permanent($Di);page_header('No extension',sprintf('None of the supported PHP extensions (%s) are available.',implode(", ",Driver::$extensions)),false);page_footer("auth");exit;}$f='';if(isset($_GET["username"])&&is_string(get_password())){check_invalid_login($Di);$Wb=adminer()->credentials();$f=Driver::connect($Wb[0],$Wb[1],$Wb[2]);if(is_object($f)){Db::$instance=$f;Driver::$instance=new
Driver($f);if($f->flavor)save_settings(array("vendor-".DRIVER."-".SERVER=>get_driver(DRIVER)));}}$jg=null;if(!is_object($f)||($jg=adminer()->login($_GET["username"],get_password()))!==true){$l=(is_string($f)?nl_br(h($f)):(is_string($jg)?$jg:'Invalid credentials.')).(preg_match('~^ | $~',get_password())?'<br>'.'There is a space in the entered password, which might be the cause.':'');auth_error($l,$Di);}if($_POST["logout"]&&$_SESSION["token"]&&!verify_token()){page_header('Logout','Invalid CSRF token. Submit the form again.');page_footer("db");exit;}if(!$_SESSION["token"])$_SESSION["token"]=rand(1,1e6);stop_session(true);if($Fa&&$_POST["token"])$_POST["token"]=get_token();$l='';if($_POST){if(!verify_token()){header("HTTP/1.1 403 Forbidden");$l='Invalid CSRF token. Submit the form again.'.' '.'If you did not send this request from Adminer, close this page.';}}elseif($_SERVER["REQUEST_METHOD"]=="POST"){header("HTTP/1.1 413 Content Too Large");$l=sprintf('The POST data is too large. Reduce the data or increase the %s configuration directive.',"<b>post_max_size</b>");if(isset($_GET["sql"]))$l
.=' '.'You can upload a large SQL file via FTP and import it from the server.';}function
print_select_result($G,$g=null,array$Vh=array(),&$z=0){$eg=array();$w=array();$e=array();$Ua=array();$Xl=array();$H=array();for($s=0;(!$z||$s<$z)&&($I=$G->fetch_row());$s++){if(!$s){echo"<div class='scrollable'>\n","<table class='nowrap odds'>\n","<thead><tr>";for($Df=0;$Df<count($I);$Df++){$m=$G->fetch_field();$B=$m->name;$Uh=(isset($m->orgtable)?$m->orgtable:"");$Th=(isset($m->orgname)?$m->orgname:$B);if($Vh&&JUSH=="sql")$eg[$Df]=($B=="table"?"table=":($B=="possible_keys"?"indexes=":null));elseif($Uh!=""){if(isset($m->table))$H[$m->table]=$Uh;if(!isset($w[$Uh])){$w[$Uh]=array();foreach(indexes($Uh,$g)as$v){if($v["type"]=="PRIMARY"){$w[$Uh]=array_flip($v["columns"]);break;}}$e[$Uh]=$w[$Uh];}if(isset($e[$Uh][$Th])){unset($e[$Uh][$Th]);$w[$Uh][$Th]=$Df;$eg[$Df]=$Uh;}}if($m->charsetnr==63)$Ua[$Df]=true;$Xl[$Df]=$m->type;echo"<th title='".h(trim(($Uh!=""?"$Uh.$Th":($m->name!=$Th?$Th:""))." ".driver()->typeName($m)))."'>".h($B).($Vh?doc_link(array('sql'=>"explain-output.html#explain_".strtolower($B),'mariadb'=>"explain/#the-columns-in-explain-select",)):"");}echo"<tbody>\n";}echo"<tr>";foreach($I
as$x=>$W){$_="";if(isset($eg[$x])&&!$e[$eg[$x]]){if($Vh&&JUSH=="sql"){$Q=$I[array_search("table=",$eg)];$_=ME.$eg[$x].url_escape($Vh[$Q]!=""?$Vh[$Q]:$Q);}else{$_=ME."edit=".url_escape($eg[$x]);foreach($w[$eg[$x]]as$tb=>$Df){if($I[$Df]===null){$_="";break;}$_
.="&where[".url_escape(bracket_escape($tb))."]=".url_escape($I[$Df]);}}}$m=array('type'=>($Ua[$x]?'blob':($Xl[$x]==254?'char':'')),);$W=select_value($W,$_,$m,null);echo"<td".($Xl[$x]<=9||$Xl[$x]==246?" class='number'":"").">$W";}}$z=$s;echo($s?"</table>\n</div>":"<p class='message'>".'No rows.')."\n";return$H;}function
textarea($B,$X,$J=10,$xb=80,$Ff=JUSH){echo"<textarea name='".h($B)."' rows='$J' cols='$xb' class='sqlarea jush-".h($Ff)."' spellcheck='false' wrap='off'>";if(is_array($X)){foreach($X
as$W)echo
h($W[0])."\n\n\n";}else
echo
h($X);echo"</textarea>";}function
select_input($c,array$C,$X="",$Ei=""){if($C&&$X!=""&&!isset($C[$X]))$C=array($X=>$X)+$C;$ml=($C?"select":"input");return"<$ml$c".($C?"><option value=''>$Ei".optionlist($C,$X,true)."</select>":" size='10' value='".h($X)."' placeholder='$Ei'>");}function
json_row($x,$W=null,$gd=true){static$Kd=true;if($Kd)echo"{";if($x!=""){echo($Kd?"":",")."\n\t\"".addcslashes($x,"\r\n\t\"\\/").'": '.($W!==null?($gd?'"'.addcslashes($W,"\r\n\"\\/").'"':$W):'null');$Kd=false;}else{echo"\n}\n";$Kd=true;}}function
flat_collations(){$wb=collations();return(is_array(reset($wb))?call_user_func_array('array_merge',array_values($wb)):$wb);}function
edit_type($x,array$m,array$wb,array$Ud=array(),array$wd=array()){$T=(string)$m["type"];echo"<td><select name='".h($x)."[type]' class='type' aria-labelledby='label-type'".on_help_value().">";if($T&&!array_key_exists($T,driver()->types())&&!isset($Ud[$T])&&!in_array($T,$wd))$wd[]=$T;$Ok=driver()->structuredTypes();if($Ud)$Ok['Foreign keys']=$Ud;echo
optionlist(array_merge($wd,$Ok),$T),"</select><td>","<input name='".h($x)."[length]' value='".h($m["length"])."' size='3'".(!$m["length"]&&preg_match('~var(char|binary)$~',$T)?" class='required'":"")." aria-labelledby='label-length'>","<td class='options'>",($wb?"<input list='collations' name='".h($x)."[collation]'".option_types($T,'('.text_type().')$')." value='".h($m["collation"])."' placeholder='(".'collation'.")'>":''),(driver()->unsigned?"<select name='".h($x)."[unsigned]'".option_types($T,'^$|'.number_type()).'><option>'.optionlist(driver()->unsigned,$m["unsigned"]).'</select>':''),(isset($m['on_update'])?"<select name='".h($x)."[on_update]'".option_types($T,'timestamp|datetime').'>'.optionlist(array(""=>"(".'ON UPDATE'.")","CURRENT_TIMESTAMP"),(preg_match('~^CURRENT_TIMESTAMP~i',$m["on_update"])?"CURRENT_TIMESTAMP":$m["on_update"])).'</select>':''),($Ud?"<select name='".h($x)."[on_delete]'".option_types($T,'`')."><option value=''>(".'ON DELETE'.")".optionlist(explode("|",driver()->onActions),$m["on_delete"])."</select> ":" ");}function
option_types($T,$Xl){return" data-types='".h($Xl)."'".(preg_match("~$Xl~",$T)?"":" class='hidden'");}function
process_length($y){$bd=driver()->enumLength;return(preg_match("~^\\s*\\(?\\s*$bd(?:\\s*,\\s*$bd)*+\\s*\\)?\\s*\$~",$y)&&preg_match_all("~$bd~",$y,$og)?"(".implode(",",$og[0]).")":preg_replace('~^[0-9].*~','(\0)',preg_replace('~[^-0-9,+()[\]]~','',$y)));}function
process_in($W){$bd=driver()->enumLength;if(preg_match("~^\\s*\\(?\\s*$bd(?:\\s*,\\s*$bd)*+\\s*\\)?\\s*\$~",$W)&&preg_match_all("~$bd~",$W,$og))return"(".implode(", ",$og[0]).")";$H=array();foreach(explode(",",$W)as$Cf)$H[]=q(trim($Cf));return"(".implode(", ",$H).")";}function
process_type(array$m,$ub="COLLATE"){return" $m[type]".process_length($m["length"]).(preg_match(number_type(),$m["type"])&&in_array($m["unsigned"],driver()->unsigned)?" $m[unsigned]":"").(preg_match('~'.text_type().'~',$m["type"])&&$m["collation"]?" $ub ".(JUSH=="mssql"?$m["collation"]:q($m["collation"])):"");}function
process_field(array$m,array$Ul){if($m["on_update"])$m["on_update"]=str_ireplace("current_timestamp()","CURRENT_TIMESTAMP",$m["on_update"]);return
array(idf_escape(trim($m["field"])),process_type($Ul),($m["null"]?" NULL":" NOT NULL"),default_value($m),(preg_match('~timestamp|datetime~',$m["type"])&&$m["on_update"]?" ON UPDATE $m[on_update]":""),(support("comment")&&$m["comment"]!=""?" COMMENT ".q($m["comment"]):""),($m["auto_increment"]?auto_increment():null),);}function
default_value(array$m){if($m["default"]===null)return"";$k=str_replace("\r","",$m["default"]);$ie=$m["generated"];return(in_array($ie,driver()->generated)?(JUSH=="mssql"?" AS ($k)".($ie=="VIRTUAL"?"":" $ie"):" GENERATED ALWAYS AS ($k) $ie"):(preg_match('~^GENERATED ~i',$k)?" $k":" DEFAULT ".(preg_match('~char|binary|text|json|enum|set|String~',$m["type"])||preg_match('~^(?![a-z])~i',$k)?(JUSH=="sql"&&preg_match('~text|json~',$m["type"])?"(".q($k).")":q($k)):str_ireplace("current_timestamp()","CURRENT_TIMESTAMP",(JUSH=="sqlite"?"($k)":$k)))));}function
edit_fields(array$n,array$wb,$T="TABLE",array$Ud=array()){$n=array_values($n);$mc=(($_POST?$_POST["defaults"]:get_setting("defaults"))?"":" class='hidden'");$Bb=(($_POST?$_POST["comments"]:get_setting("comments"))?"":" class='hidden'");echo"<thead><tr>\n",($T=="PROCEDURE"?"<td>":""),"<th id='label-name'>".($T=="TABLE"?'Column name':'Parameter name'),"<td id='label-type'>".'Type'."<textarea id='enum-edit' rows='4' cols='12' wrap='off' hidden></textarea>".script("qs('#enum-edit').onblur = editingLengthBlur;"),"<td id='label-length'>".'Length',"<td>".'Options';if($T=="TABLE")echo"<td id='label-null'>NULL\n","<td><input type='radio' name='auto_increment_col' value=''><abbr id='label-ai' title='".'Auto Increment'."'>AI</abbr>",doc_link(array('sql'=>"example-auto-increment.html",'mariadb'=>"auto_increment/",'sqlite'=>"autoinc.html",'pgsql'=>"datatype-numeric.html#DATATYPE-SERIAL",'mssql'=>"t-sql/statements/create-table-transact-sql-identity-property",)),"<td id='label-default'$mc>".'Default value',(support("comment")?"<td id='label-comment'$Bb>".'Comment':"");$Sf=!support("move_col");echo"<td>".icon("plus","add[".($Sf?count($n):0)."]","+",'Add next',($Sf?on('click','editingAddLastRow'):"")),"<tbody".on('click','editingClick').on('input','editingInput').on('keydown','editingKeydown').">\n";foreach($n
as$s=>$m){$s++;$Wh=$m[($_POST?"orig":"field")];$zc=(isset($_POST["add"][$s-1])||(isset($m["field"])&&!idx($_POST["drop_col"],$s)))&&(support("drop_col")||$Wh=="");echo"<tr".($zc?"":" hidden").">\n",($T=="PROCEDURE"?"<td>".html_select("fields[$s][inout]",explode("|",driver()->inout),$m["inout"]):"")."<th>",(support("move_col")?icon("move","","↕",'Move')." ":"");if($zc)echo"<input name='fields[$s][field]' value='".h($m["field"])."' data-maxlength='64' autocapitalize='off' aria-labelledby='label-name'".(isset($_POST["add"][$s-1])?" autofocus":"").">";echo
input_hidden("fields[$s][orig]",$Wh);edit_type("fields[$s]",$m,$wb,$Ud);if($T=="TABLE"){echo"<td><label class='block'>".checkbox("fields[$s][null]",1,$m["null"],"","","","label-null")."</label>","<td><label class='block'><input type='radio' name='auto_increment_col' value='$s'".($m["auto_increment"]?" checked":"")." aria-labelledby='label-ai'></label>","<td$mc>".(driver()->generated?html_select("fields[$s][generated]",array_merge(array("","DEFAULT"),driver()->generated),$m["generated"])." ":checkbox("fields[$s][generated]",1,$m["generated"],"","","","label-default"));$c=" name='fields[$s][default]' aria-labelledby='label-default'";$X=h($m["default"]);echo(preg_match('~\n~',$m["default"])?"<textarea$c rows='2' cols='30' style='vertical-align: bottom;'>\n$X</textarea>":"<input$c value='$X'>");if(support("comment")){$c=" name='fields[$s][comment]' data-maxlength='".(min_version(5.5)?1024:255)."' aria-labelledby='label-comment'";echo"<td$Bb>".adminer()->commentInput('COLUMN',$c,$m["comment"]);}}echo"<td>",(support("move_col")?icon("plus","add[$s]","+",'Add next')." ":""),($Wh==""||support("drop_col")?icon("cross","drop_col[$s]","x",'Remove'):"");}}function
process_fields(array&$n){if($_POST["add"]){$n=array_values($n);array_splice($n,key($_POST["add"]),0,array(array()));}return$_POST["add"]||$_POST["drop_col"];}function
drop_create($Kc,$h,$Mc,$sl,$Oc,$ig,$Ig,$Gg,$Hg,$Ch,$hh){if($_POST["drop"])query_redirect($Kc,$ig,$Ig);elseif($Ch=="")query_redirect($h,$ig,$Hg);elseif(support("transaction_ddl")){driver()->begin();queries_redirect($ig,$Gg,queries($Kc)&&queries($h)&&driver()->commit());driver()->rollback();}elseif($Ch!=$hh){$Vb=queries($h);queries_redirect($ig,$Gg,$Vb&&queries($Kc));if($Vb&&$Mc)queries($Mc);}else
queries_redirect($ig,$Gg,queries($sl)&&queries($Oc)&&queries($Kc)&&queries($h));}function
create_trigger($Fh,array$I){$yl=" $I[Timing] $I[Event]".(preg_match('~ OF~',$I["Event"])?" $I[Of]":"");return"CREATE TRIGGER ".idf_escape($I["Trigger"]).(JUSH=="mssql"?$Fh.$yl:$yl.$Fh).rtrim(" $I[Type]\n$I[Statement]",";").";";}function
q_dollar($P){$rc='$$';while(strpos($P.$rc,$rc)!=strlen($P))$rc='$_'.substr($rc,1);return$rc.$P.$rc;}function
routine_collate($vb){static$hb=array();if($vb&&!$hb){foreach(collations()as$gb=>$_m){foreach((array)$_m
as$W)$hb[$W]=$gb;}}return($hb[$vb]?"CHARACTER SET ".q($hb[$vb])." ":"")."COLLATE";}function
create_routine($Fj,array$I){$N=array();$n=(array)$I["fields"];ksort($n);foreach($n
as$m){if($m["field"]!="")$N[]="\n  ".(preg_match("~^(".driver()->inout.")\$~",$m["inout"])?"$m[inout] ":"").idf_escape($m["field"]).process_type($m,routine_collate($m["collation"]));}$oc="";$C=array();foreach(routine_options($Fj)as$x=>$Y){$X=idx((array)$I["options"],$x,"");if($x=="DEFINER")$oc=($X?" $x=".implode("@",array_map('Adminer\q',explode("@",$X,2))):"");elseif(!$Y){if($X!="")$C[]="$x ".q($X);}elseif($X!=reset($Y)&&in_array($X,$Y))$C[]=$X;}$Qf=$I["language"];$pc=rtrim($I["definition"],";");$Gc=(JUSH=="pgsql"||($Qf&&$Qf!="sql"));return"CREATE$oc $Fj ".idf_escape(trim($I["name"]))." (".($N?implode(",",$N)."\n":"").")".($Fj=="FUNCTION"?"\nRETURNS".process_type($I["returns"],routine_collate($I["returns"]["collation"])):"").($Qf?" LANGUAGE $Qf":"").($C?"\n".implode(" ",$C):"").($Gc?" AS ".q_dollar("\n".trim($pc)."\n"):"\n$pc;");}function
remove_definer($F){return
preg_replace('~^([A-Z =]+) DEFINER=`'.preg_replace('~@(.*)~','`@`(%|\1)',logged_user()).'`~','\1',$F);}function
format_foreign_key(array$p){$j=$p["db"];$oh=$p["ns"];return" FOREIGN KEY (".implode(", ",array_map('Adminer\idf_escape',$p["source"])).") REFERENCES ".($j!=""&&$j!=$_GET["db"]?idf_escape($j).".":"").($oh!=""&&$oh!=$_GET["ns"]?idf_escape($oh).".":"").idf_escape($p["table"])." (".implode(", ",array_map('Adminer\idf_escape',$p["target"])).")".(preg_match("~^(".driver()->onActions.")\$~",$p["on_delete"])?" ON DELETE $p[on_delete]":"").(preg_match("~^(".driver()->onActions.")\$~",$p["on_update"])?" ON UPDATE $p[on_update]":"").($p["deferrable"]?" $p[deferrable]":"");}function
tar_file($o,$Cl){$H=pack("a100a8a8a8a12a12",$o,644,0,0,decoct($Cl->size),decoct(time()));$nb=8*32;for($s=0;$s<strlen($H);$s++)$nb+=ord($H[$s]);$H
.=sprintf("%06o",$nb)."\0 ";echo$H,str_repeat("\0",512-strlen($H));$Cl->send();echo
str_repeat("\0",511-($Cl->size+511)%512);}function
doc_version(){$lk=connection()->server_info;if(JUSH=='oracle'){preg_match('~(?:.* |^)(\d+)\.\d+\.\d+\.\d+\.\d+~s',$lk,$A);return($A[1]>=18?$A[1]:"19");}$tj=(JUSH=='sql'?'~^\d+\.\d+~':'~^\d\.?\d~');$Cm=(preg_match($tj,$lk,$A)?$A[0]:"");if(JUSH=='mssql')return($Cm>=15?"sql-server-ver$Cm":($Cm==12?"azuresqldb-current":"sql-server-2017"));return$Cm;}function
doc_link(array$Ai,$tl="<sup>?</sup>"){$Cm=doc_version();$nm=array('sql'=>"https://dev.mysql.com/doc/refman/$Cm/en/",'sqlite'=>"https://www.sqlite.org/",'pgsql'=>"https://www.postgresql.org/docs/".(connection()->flavor=='cockroach'?"current":$Cm)."/",'mssql'=>"https://learn.microsoft.com/en-us/sql/",'oracle'=>"https://docs.oracle.com/en/database/oracle/oracle-database/$Cm/",);if(connection()->flavor=='maria'){$nm['sql']="https://mariadb.com/kb/en/";$Ai['sql']=(isset($Ai['mariadb'])?$Ai['mariadb']:str_replace(".html","/",$Ai['sql']));}return($Ai[JUSH]?"<a href='".h($nm[JUSH].$Ai[JUSH].(JUSH=='mssql'?"?view=$Cm":""))."'".target_blank().">$tl</a>":"");}function
db_size($j){if(!connection()->select_db($j))return"?";$H=0;foreach(table_status()as$R)$H+=$R["Data_length"]+$R["Index_length"];return
format_number($H);}function
set_utf8mb4($h){static$N=false;if(!$N&&preg_match('~\butf8mb4~i',$h)){$N=true;echo"SET NAMES ".charset(connection()).";\n\n";}}if(isset($_GET["status"]))$_GET["variables"]=$_GET["status"];if(isset($_GET["import"]))$_GET["sql"]=$_GET["import"];if(DB==""&&isset($_GET["ns"]))redirect(remove_from_uri('ns'));if(!(DB!=""?connection()->select_db(DB):isset($_GET["sql"])||isset($_GET["dump"])||isset($_GET["database"])||isset($_GET["processlist"])||isset($_GET["privileges"])||isset($_GET["user"])||isset($_GET["variables"])||$_GET["script"]=="connect"||$_GET["script"]=="kill")){if(DB!=""||$_GET["refresh"]){restart_session();set_session("dbs",null);}if(DB!=""){header("HTTP/1.1 404 Not Found");page_header('Database'.": ".h(DB),'Invalid database.',true);}else{if(!isset($_GET["db"])&&support("single_db")){$i=adminer()->databases();if($i)redirect(ME."db=".url_escape($i[0]));}if($_POST["db"]&&!$l)queries_redirect(substr(ME,0,-1),'Databases have been dropped.',drop_databases($_POST["db"]));page_header('Select database',$l,false);echo"<p class='links'>\n";foreach(array('database'=>'Create database','privileges'=>'Privileges','processlist'=>'Process list','variables'=>'Variables','status'=>'Status',)as$x=>$W){if(support($x))echo"<a href='".h(ME)."$x='>$W</a>\n";}echo"<p>".sprintf('%s version: %s through PHP extension %s',get_driver(DRIVER),"<b>".h(connection()->server_info)."</b>","<b>".connection()->extension."</b>")."\n","<p>".sprintf('Logged in as: %s',"<b>".h(logged_user())."</b>")."\n";$i=adminer()->databases();if($i){$Oj=support("scheme");$wb=collations();echo"<form action='' method='post'>\n","<table class='checkable odds'".on('click','tableClick').on('dblclick','tableClick').">\n","<thead><tr>".(support("database")?"<td class='hover'>":"")."<th".(JUSH!='mssql'?" aria-sort='ascending'":"").">".'Database'.(get_session("dbs")!==null?" - <a href='".h(ME)."refresh=1'>".'Refresh'."</a>":"")."<td>".'Collation'."<td>".'Tables'."<td>".'Size'." - <a href='".h(ME)."dbsize=1'".on('click','ajaxSetHtml',ME."script=connect").">".'Compute'."</a>"."<tbody>\n";$i=($_GET["dbsize"]?count_tables($i):array_flip($i));foreach($i
as$j=>$S){$Ej=h(preg_replace('~&db=[^&]*~','',ME))."db=".url_escape($j);$t=h("Db-".$j);echo"<tr>".(support("database")?"<td class='hover'>".checkbox("db[]",$j,in_array($j,(array)$_POST["db"]),"","","",$t):""),"<th><a href='$Ej' id='$t'>".h($j)."</a>";$vb=h(db_collation($j,$wb));echo"<td>".(support("database")?"<a href='$Ej".($Oj?"&amp;ns=":"")."&amp;database=' title='".'Alter database'."'>$vb</a>":$vb),"<td align='right'><a href='$Ej&amp;schema=' id='tables-".h($j)."' title='".'Database schema'."'>".($_GET["dbsize"]?format_number($S):"?")."</a>","<td align='right' id='size-".h($j)."'>".($_GET["dbsize"]?db_size($j):"?"),"\n";}echo"</table>\n",(support("database")?"<div class='footer'><div>\n"."<fieldset><legend>".'Selected'." <span id='selected'></span></legend><div>\n"."<input type='hidden' name='all' value=''".on('click','countDbs').">\n"."<input type='submit' name='drop' value='".'Drop'."'".confirm().">\n"."</div></fieldset>\n"."</div></div>\n":""),input_token(),"</form>\n",script("tableCheck();");}$oa=adminer();$Ii=($oa
instanceof
Plugins?$oa->plugins:array());$Jc=($oa
instanceof
Plugins?$oa->drivers:array());$wc=design_checksums();if($Ii||$Jc||$wc){$ob=($oa
instanceof
Plugins?$oa->checksums():array());$vh=Plugins::officialChecksums();$jm=function($mm){return" (<a href='$mm'".target_blank()." class='update'>".VERSION."</a>)";};$Hi=function($Ed)use($ob,$vh,$jm){return($ob[$Ed]&&$vh[$Ed]&&$ob[$Ed]!==$vh[$Ed]?$jm("https://www.adminer.org/plugins/?version=".VERSION):"");};echo"<div class='plugins'>\n","<h3>".'Loaded plugins'."</h3>\n<ul>\n";foreach($Ii
as$Fi){$rj=new
\ReflectionObject($Fi);$tc=(method_exists($Fi,'description')?$Fi->description():"");if(!$tc){if(preg_match('~^/[\s*]+(.+)~',$rj->getDocComment(),$A))$tc=$A[1];}$Pj=(method_exists($Fi,'screenshot')?$Fi->screenshot():"");echo"<li><b>".get_class($Fi)."</b>".h($tc?": $tc":"").($Pj?" (<a href='".h($Pj)."'".target_blank().">".'screenshot'."</a>)":"").$Hi(basename((string)$rj->getFileName(),'.php'))."\n";}foreach($Jc
as$t=>$B)echo"<li><b>".h($t)."</b>: ".h($B).$Hi(basename((string)$oa->driverFiles[$t],'.php'))."\n";if($wc){$xh=official_design_checksums();foreach($wc
as$o=>$vc){list($B,$nb)=$vc;$wh=$xh["$B/$o"];echo"<li><b>".h($o)."</b>".h($B?": $B":"").($wh&&$wh!==$nb?$jm("https://www.adminer.org/?version=".VERSION."#extras"):"")."\n";}}echo"</ul>\n";adminer()->pluginsLinks();echo"</div>\n";}}page_footer("db");exit;}if(support("scheme")){if(DB!=""&&$_GET["ns"]!==""){if(!isset($_GET["ns"]))redirect(preg_replace('~&db=[^&]+~','\0&ns='.url_escape(get_schema()),relative_uri()));if(!set_schema($_GET["ns"])){header("HTTP/1.1 404 Not Found");page_header('Schema'.h(": $_GET[ns]"),'Invalid schema.',true);page_footer("ns");exit;}}}adminer()->afterConnect();class
TmpFile{private$handler;var$size=0;function
__construct(){$this->handler=tmpfile();}function
write($Nb){$this->size+=strlen($Nb);fwrite($this->handler,$Nb);}function
send(){fseek($this->handler,0);fpassthru($this->handler);fclose($this->handler);}}if($_GET["select"]!=""&&($_POST["edit"]||$_POST["clone"])&&!$_POST["save"])$_GET["edit"]=$_GET["select"];if(isset($_GET["callf"]))$_GET["call"]=$_GET["callf"];if(isset($_GET["function"]))$_GET["procedure"]=$_GET["function"];if(isset($_GET["download"])){$a=$_GET["download"];$n=fields($a);header("Content-Type: application/octet-stream");header("Content-Disposition: attachment; filename=".friendly_url("$a-".implode("_",$_GET["where"])).".".friendly_url($_GET["field"]));$L=array(idf_escape($_GET["field"]));$G=driver()->select($a,$L,array(where($_GET,$n)),$L);$I=($G?$G->fetch_row():array());echo
driver()->value($I[0],$n[$_GET["field"]]);exit;}elseif(isset($_GET["table"])){$a=$_GET["table"];$n=fields($a);if(!$n)$l=adminer()->error()?:'No tables.';$R=table_status1($a);$B=adminer()->tableName($R);$l=$l?:h($R["Error"]);page_header(($n&&is_view($R)?$R['Engine']=='materialized view'?'Materialized view':'View':'Table').": ".($B!=""?$B:h($a)),$l);$Dj=array();foreach($n
as$x=>$m)$Dj+=$m["privileges"];adminer()->selectLinks($R,(isset($Dj["insert"])||!support("table")?"":null));$Ab=$R["Comment"];if($Ab!="")echo"<p class='nowrap'>".'Comment'.": ".adminer()->commentValue('TABLE',$Ab)."\n";if($n)adminer()->tableStructurePrint($n,$R);function
tables_links(array$S){echo"<ul>\n";foreach($S
as$I){$_=preg_replace('~ns=[^&]*~',"ns=".url_escape($I["ns"]),ME);echo"<li><a href='".h($_."table=".url_escape($I["table"]))."'>".($I["ns"]!=$_GET["ns"]?"<b>".h($I["ns"])."</b>.":"").h($I["table"])."</a>";}echo"</ul>\n";}$jf=driver()->inheritsFrom($a);if($jf){echo"<h3>".'Inherits from'."</h3>\n";tables_links($jf);}if(support("indexes")&&driver()->supportsIndex($R)){echo"<div>\n","<h3 id='indexes'>".'Indexes'."</h3>\n";$w=indexes($a);if($w)adminer()->tableIndexesPrint($w,$R);if(driver()->supportsAlterIndex($R))echo'<p class="links hover"><a href="'.h(ME).'indexes='.url_escape($a).'">'.'Alter indexes'."</a>\n";echo"</div>\n";}if(!is_view($R)&&driver()->supportsAlterTable($R)){if(fk_support($R)){echo"<div>\n","<h3 id='foreign-keys'>".'Foreign keys'."</h3>\n";$Ud=foreign_keys($a);if($Ud){echo"<table>\n","<thead><tr><th>".'Source'."<td>".'Target'."<td>".'ON DELETE'."<td>".'ON UPDATE'."<td class='hover'><tbody>\n";foreach($Ud
as$B=>$p){echo"<tr title='".h($B)."'>","<th><i>".implode("</i>, <i>",array_map('Adminer\h',$p["source"]))."</i>";$_=($p["db"]!=""?preg_replace('~db=[^&]*~',"db=".url_escape($p["db"]),ME):($p["ns"]!=""?preg_replace('~ns=[^&]*~',"ns=".url_escape($p["ns"]),ME):ME));echo"<td><a href='".h($_."table=".url_escape($p["table"]))."'>".($p["db"]!=""&&$p["db"]!=DB?"<b>".h($p["db"])."</b>.":"").($p["ns"]!=""&&$p["ns"]!=$_GET["ns"]?"<b>".h($p["ns"])."</b>.":"").h($p["table"])."</a>","(<i>".implode("</i>, <i>",array_map('Adminer\h',$p["target"]))."</i>)","<td>".h($p["on_delete"]),"<td>".h($p["on_update"]),'<td class="hover"><a href="'.h(ME.'foreign='.url_escape($a).'&name='.url_escape($B)).'">'.'Alter'.'</a>',"\n";}echo"</table>\n";}echo'<p class="links hover"><a href="'.h(ME).'foreign='.url_escape($a).'">'.'Create foreign key'."</a>\n","</div>\n";}if(support("check")){echo"<div>\n","<h3 id='checks'>".'Checks'."</h3>\n";$jb=driver()->checkConstraints($a);if($jb){echo"<table>\n";foreach($jb
as$x=>$W)echo"<tr title='".h($x)."'>","<td><code class='jush-".JUSH."'>".shorten_utf8(preg_replace('~\s+~',' ',ltrim($W)),80,"</code>"),"<td class='hover'><a href='".h(ME.'check='.url_escape($a).'&name='.url_escape($x))."'>".'Alter'."</a>","\n";echo"</table>\n";}echo'<p class="links hover"><a href="'.h(ME).'check='.url_escape($a).'">'.'Create check'."</a>\n","</div>\n";}}if(support(is_view($R)?"view_trigger":"trigger")&&driver()->supportsAlterTable($R)){echo"<div>\n","<h3 id='triggers'>".'Triggers'."</h3>\n";$Rl=triggers($a);if($Rl){echo"<table>\n";foreach($Rl
as$x=>$W)echo"<tr valign='top'><td>".h($W[0])."<td>".h($W[1])."<th>".h($x)."<td class='hover'><a href='".h(ME.'trigger='.url_escape($a).'&name='.url_escape($x))."'>".'Alter'."</a>\n";echo"</table>\n";}echo'<p class="links hover"><a href="'.h(ME).'trigger='.url_escape($a).'">'.'Create trigger'."</a>\n","</div>\n";}$qk=driver()->shadowTables($a);if($qk){echo"<h3 id='shadow-tables'>".'Shadow tables'."</h3>\n";tables_links($qk);}$if=driver()->inheritedTables($a);if($if){echo"<h3 id='partitions'>".'Inherited by'."</h3>\n";$pi=driver()->partitionsInfo($a);if($pi)echo"<p><code class='jush-".JUSH."'>BY ".h("$pi[partition_by]($pi[partition])")."</code>\n";tables_links($if);}}elseif(isset($_GET["schema"])){page_header('Database schema',"",array(),h(DB.($_GET["ns"]?".$_GET[ns]":"")));function
schema_column($Q,array$qj,array&$e){if(!isset($e[$Q])){$e[$Q]=0;foreach((array)idx($qj,$Q)as$B=>$sj){if($B!=$Q)$e[$Q]=max($e[$Q],schema_column($B,$qj,$e)+1);}}return$e[$Q];}function
type_class($T){foreach(array('char'=>'text','date'=>'time|year','binary'=>'blob','enum'=>'set',)as$x=>$W){if(preg_match("~$x|$W~",$T))return" class='$x'";}}$dl=array();$fl=array();$el=array();$Bd=array();$ca=($_GET["schema"]?:$_COOKIE["adminer_schema-".str_replace(".","_",DB)]);preg_match_all('~([^:]+):([-0-9.]+)x([-0-9.]+)(_|$)~',$ca,$og,PREG_SET_ORDER);foreach($og
as$s=>$A){$dl[$A[1]]=array((float)$A[2],(float)$A[3]);$fl[]="\n\t'".js_escape($A[1])."': [ $A[2], $A[3] ]";}$K=array();$qj=array();$Ud=array();$va=driver()->allFields();$He=array();$gl=array();foreach(table_status('',true)as$Q=>$R){if(!is_view($R)){if(adminer()->tableName($R)!=""&&!$R["dependent"])$gl[$Q]=$R;else$He[$Q]=true;}}foreach($gl
as$Q=>$R){$Ki=0;$K[$Q]["fields"]=array();foreach($va[$Q]as$m){$Ki+=1.25;$Bd[$Q][$m["field"]]=$Ki;$K[$Q]["fields"][$m["field"]]=$m;}foreach(adminer()->foreignKeys($Q)as$W){if($W["db"]==""&&$W["ns"]==""&&!$He[$W["table"]]){$Ud[$Q][]=$W;$qj[$W["table"]][$Q]=array();}}}$e=array();$me=array();$Pm=array();$se=array();foreach(array_keys($K)as$B)schema_column($B,$qj,$e);arsort($e);foreach($e
as$B=>$d){$Og=null;foreach((array)idx($Ud,$B)as$W){if($W["table"]!=$B&&$K[$W["table"]])$Og=($Og===null?$e[$W["table"]]:min($Og,$e[$W["table"]]));}$e[$B]=max($d,(int)$Og-1);}foreach($K
as$B=>$Q){$d=$e[$B];$me[$d][]=$B;$vl=.75*strlen($B);foreach($Q["fields"]as$m)$vl=max($vl,.65*strlen($m["field"]));$Pm[$d]=max(idx($Pm,$d,0),ceil($vl)+1);}foreach($Ud
as$B=>$_m){foreach($_m
as$W){$re=$e[$B]+(idx($e,$W["table"],$e[$B])>$e[$B]?1:0);$se[$re]=idx($se,$re,0)+1;}}ksort($me);$Fe=0;$Om=0;$yb=0;$Ui=null;$Zk=array();$il=array();foreach($me
as$d=>$S){if($Ui!==null){$yb=round($yb+$Pm[$Ui]+1.7+idx($se,$d,0)*.1,1);$Rh=array();foreach($S
as$B){$Sk=0;$Tb=0;$dh=array_keys((array)idx($qj,$B));foreach((array)idx($Ud,$B)as$W)$dh[]=$W["table"];foreach($dh
as$Zg){if($K[$Zg]&&$e[$Zg]<$d){$Sk+=$K[$Zg]["pos"][0];$Tb++;}}$Rh[$B]=($Tb?$Sk/$Tb:$Fe);}asort($Rh);$S=array_keys($Rh);}$Fl=0;foreach($S
as$B){$Ki=1.25*count($K[$B]["fields"]);$K[$B]["pos"]=($dl[$B]?:array($Fl,$yb));$Zk[$B]=$K[$B]["pos"][1];$il[$B]=$Pm[$d];$Fl+=2.5+$Ki;$Fe=max($Fe,$K[$B]["pos"][0]+2.5+$Ki);$Om=max($Om,round($K[$B]["pos"][1]+$Pm[$d],1));if(!$dl[$B])$el[]="\n\t'".js_escape($B)."': [ ".$K[$B]["pos"][0].", ".$K[$B]["pos"][1]." ]";}$Ui=$d;}$Wf=array();$Pa=array();foreach($Ud
as$B=>$_m){foreach($_m
as$W){$ol=idx($Zk,$W["table"],$Zk[$B]);$Ak=$Zk[$B]+$il[$B];$Cj=($ol-1>$Ak);$Uf=($Cj?$Ak+1:min($Zk[$B],$ol)-1);$Oa=idx($Pa,(string)$Uf,0);$Pa[(string)$Uf]=$Oa+1;$Uf=round($Cj?min($Uf+$Oa*.1,$ol-1):$Uf-$Oa*.1,1);while($Wf[(string)$Uf])$Uf-=.0001;$K[$B]["references"][$W["table"]][(string)$Uf]=array($W["source"],$W["target"]);$qj[$W["table"]][$B][(string)$Uf]=$W["target"];$Wf[(string)$Uf]=true;}}echo'<div id="schema" style="height: ',$Fe,'em; width: ',$Om,'em;">
<script',nonce(),'>
const tablePos = {',implode(",",$fl)."\n",'};
const tablePosDefault = {',implode(",",$el)."\n",'};
const em = qs(\'#schema\').offsetHeight / ',$Fe,';
document.onmousemove = schemaMousemove;
document.onmouseup = event => schemaMouseup(event, \'',js_escape(DB),'\');
</script>
';foreach($K
as$B=>$Q){echo"<div class='table'".on('mousedown','schemaMousedown')." style='top: ".$Q["pos"][0]."em; left: ".$Q["pos"][1]."em; width: ".$il[$B]."em;'>",'<a href="'.h(ME).'table='.url_escape($B).'"><b>'.h($B)."</b></a>";foreach($Q["fields"]as$m){$W='<span'.type_class($m["type"]).' title="'.h($m["type"].($m["length"]?"($m[length])":"").($m["null"]?" NULL":'')).'">'.h($m["field"]).'</span>';echo"<br>".($m["primary"]?"<i>$W</i>":$W);}foreach((array)$Q["references"]as$pl=>$sj){foreach($sj
as$Uf=>$nj){$Vf=$Uf-$Q["pos"][1];$Pk=($Vf>0?"left: 100%; width: calc($Vf"."em - 100%)":"left: $Vf"."em");$Om=($Vf>0?"100%":(-$Vf)."em");$s=0;foreach($nj[0]as$_k)echo"\n<div class='references' title='".h($pl)."' id='refs$Uf-".($s++)."' style='$Pk"."; top: ".$Bd[$B][$_k]."em; padding-top: .5em;'>"."<div style='border-top: 1px solid gray; width: $Om;'></div></div>";}}foreach((array)$qj[$B]as$pl=>$sj){foreach($sj
as$Uf=>$ql){$Vf=$Uf-$Q["pos"][1];$s=0;foreach($ql
as$nl)echo"\n<div class='references arrow' title='".h($pl)."' id='refd$Uf-".($s++)."' style='left: $Vf"."em; top: ".$Bd[$B][$nl]."em;'>"."<div style='height: .5em; border-bottom: 1px solid gray; width: ".(-$Vf)."em;'></div>"."</div>";}}echo"\n</div>\n";}foreach($K
as$B=>$Q){foreach((array)$Q["references"]as$pl=>$sj){if($K[$pl]){foreach($sj
as$Uf=>$nj){$Pg=$Fe;$wg=-10;foreach($nj[0]as$x=>$_k){$Li=$Q["pos"][0]+$Bd[$B][$_k];$Mi=$K[$pl]["pos"][0]+$Bd[$pl][$nj[1][$x]];$Pg=min($Pg,$Li,$Mi);$wg=max($wg,$Li,$Mi);}echo"<div class='references' id='refl$Uf' style='left: $Uf"."em; top: $Pg"."em; padding: .5em 0;'><div style='border-right: 1px solid gray; margin-top: 1px; height: ".($wg-$Pg)."em;'></div></div>\n";}}}}echo'</div>
<p class="links"><a href="',h(ME."schema=".url_escape($ca)),'" id="schema-link">Permanent link</a>
';}elseif(isset($_GET["dump"])){$a=$_GET["dump"];if($_POST&&!$l){$k=array("auto_increment"=>'');foreach(array("type","routine","event","trigger")as$Uk){if(support($Uk))$k[$Uk."s"]='';}save_settings(array_intersect_key($_POST+$k,array_flip(array("output","format","db_style","table_style","data_style"))+$k),"adminer_export");$S=array_flip((array)$_POST["tables"])+array_flip((array)$_POST["data"]);$sd=dump_headers((count($S)==1?key($S):DB),(DB==""||$_GET["ns"]===""||count($S)>1));$_f=preg_match('~sql~',$_POST["format"]);if($_f){echo"-- Adminer ".VERSION." ".get_driver(DRIVER)." ".str_replace("\n"," ",connection()->server_info)." dump\n\n";if(JUSH=="sql"){echo"SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
".($_POST["data_style"]?"SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';
":"")."
";connection()->query("SET time_zone = '+00:00'");connection()->query("SET sql_mode = ''");}}$Pk=$_POST["db_style"];$i=array(DB);if(DB==""){$i=$_POST["databases"];if(is_string($i))$i=explode("\n",rtrim(str_replace("\r","",$i),"\n"));}foreach((array)$i
as$j){adminer()->dumpDatabase($j);if(connection()->select_db($j)){if($_f&&$Pk)echo
use_sql($j,$Pk).";\n\n";foreach(($_GET["ns"]===""?(array)$_POST["schemas"]:(DB!=""||!support("scheme")?array(""):adminer()->schemas()))as$K){if($K!=""){if(DB==""&&information_schema(DB,$K))continue;set_schema($K);}$Mk=($_POST["table_style"]||$_POST["data_style"]?table_status('',true):array());$rd=array();$fc=array();foreach($Mk
as$B=>$R){if(DB==""||$_GET["ns"]===""||in_array($B,(array)$_POST["tables"]))$rd[$B]=$R;if(DB==""||$_GET["ns"]===""||in_array($B,(array)$_POST["data"]))$fc[$B]=$R;}if($_f){if($_POST["table_style"]=="DROP+CREATE"&&function_exists('Adminer\drop_sql'))echo
drop_sql($rd);if($_POST["data_style"]=="TRUNCATE+INSERT"&&function_exists('Adminer\truncate_all_sql')){$Sl=array();foreach($fc
as$B=>$R){if(!is_view($R)&&!($_POST["table_style"]=="DROP+CREATE"&&isset($rd[$B])))$Sl[]=$B;}echo
truncate_all_sql($Sl);}$ei="";if($_POST["types"]){foreach(types()as$t=>$T){$pc=type_definition($t);$sh=($pc["kind"]=='d'?"DOMAIN":"TYPE");if($pc["definition"])$ei
.=($Pk!='DROP+CREATE'?"DROP $sh IF EXISTS ".idf_escape($T).";;\n":"")."CREATE $sh ".idf_escape($T)." $pc[definition];\n\n";else$ei
.="-- Could not export type $T\n\n";}}if($_POST["routines"]){foreach(routines()as$I){$B=$I["ROUTINE_NAME"];$Fj=$I["ROUTINE_TYPE"];$h=create_routine($Fj,array("name"=>$B)+routine($I["SPECIFIC_NAME"],$Fj));set_utf8mb4($h);$ei
.=($Pk!='DROP+CREATE'?"DROP $Fj IF EXISTS ".idf_escape($B).";;\n":"")."$h;\n\n";}}if($_POST["events"]){foreach(get_rows("SHOW EVENTS",null,"-- ")as$I){$h=remove_definer(get_val("SHOW CREATE EVENT ".idf_escape($I["Name"]),3));set_utf8mb4($h);$ei
.=($Pk!='DROP+CREATE'?"DROP EVENT IF EXISTS ".idf_escape($I["Name"]).";;\n":"")."$h;;\n\n";}}echo($ei&&JUSH=='sql'?"DELIMITER ;;\n\n$ei"."DELIMITER ;\n\n":$ei);}if($_POST["table_style"]||$_POST["data_style"]){$Em=array();foreach($Mk
as$B=>$R){$Q=array_key_exists($B,$rd);$dc=array_key_exists($B,$fc);if($Q||$dc){$Cl=null;if($sd=="tar"){$Cl=new
TmpFile;ob_start(array($Cl,'write'),1e5);}adminer()->dumpTable($B,($Q?$_POST["table_style"]:""),(is_view($R)?2:0));if(is_view($R))$Em[]=$B;elseif($dc){$n=fields($B);$L=array("*");$Qb=convert_fields($n,$n);if($Qb)$L[]=substr($Qb,2);adminer()->dumpData($B,$_POST["data_style"],"",$L);}if($_f&&$_POST["triggers"]&&$Q&&($Rl=trigger_sql($B)))echo"\nDELIMITER ;;\n$Rl\nDELIMITER ;\n";if($sd=="tar"){ob_end_flush();tar_file((DB!=""?"":"$j/")."$B.csv",$Cl);}elseif($_f)echo"\n";}}if($_f&&$_POST["table_style"]&&function_exists('Adminer\foreign_keys_sql')){foreach($rd
as$B=>$R){if(!is_view($R))echo
foreign_keys_sql($B);}}if($_f){foreach($Em
as$Dm)adminer()->dumpTable($Dm,$_POST["table_style"],1);}if($sd=="tar")echo
pack("x1024");}}}}adminer()->dumpFooter();exit;}page_header('Export',$l,($_GET["export"]!=""?array("table"=>$_GET["export"]):array()),h(DB));echo'
<form action="" method="post">
<table class="layout">
';$ic=array('','USE','DROP+CREATE','CREATE');$hl=array('','DROP+CREATE','CREATE');$ec=array('','TRUNCATE+INSERT','INSERT');if(JUSH=="sql")$ec[]='INSERT+UPDATE';$I=get_settings("adminer_export");if(!$I)$I=array("output"=>"text","format"=>"sql","db_style"=>(DB!=""?"":"CREATE"),"table_style"=>"DROP+CREATE","data_style"=>"INSERT");echo"<tr><th>".'Output'."<td>".html_radios("output",adminer()->dumpOutput(),$I["output"])."\n","<tr><th>".'Format'."<td>".html_radios("format",adminer()->dumpFormat(),$I["format"])."\n",(JUSH=="sqlite"?"":"<tr><th>".'Database'."<td>".html_select('db_style',$ic,$I["db_style"]).(support("type")?checkbox("types",1,$I["types"],'User types'):"").(support("routine")?checkbox("routines",1,$I["routines"],'Routines'):"").(support("event")?checkbox("events",1,$I["events"],'Events'):"")),"<tr><th>".'Tables'."<td>".html_select('table_style',$hl,$I["table_style"]).checkbox("auto_increment",1,$I["auto_increment"],'Auto Increment').(support("trigger")?checkbox("triggers",1,$I["triggers"],'Triggers'):""),"<tr><th>".'Data'."<td>".html_select('data_style',$ec,$I["data_style"]),'</table>
';adminer()->dumpPrint();echo'<p><input type=\'submit\' value=\'Export\'>
',input_token(),'
<table',on('click','dumpClick'),'>
';$Si=array();if($_GET["ns"]===""){echo"<thead><tr><th style='text-align: left;'>","<label class='block'><input type='checkbox' id='check-schemas' checked class='jsonly' title='".'All'."'".on('click','formCheck','^schemas\[').">".'Schema'."</label>","<tbody>\n";foreach(adminer()->schemas()as$K){if(!information_schema(DB,$K))echo"<tr><td>".checkbox("schemas[]",$K,true,$K,"","block")."\n";}}elseif(DB!=""){$lb=($a!=""?"":" checked");echo"<thead><tr>","<th style='text-align: left;'><label class='block'><input type='checkbox' id='check-tables'$lb class='jsonly' title='".'All'."'".on('click','formCheck','^tables\[').">".'Table'."</label>","<th style='text-align: right;'><label class='block'>".'Data'."<input type='checkbox' id='check-data'$lb class='jsonly' title='".'All'."'".on('click','formCheck','^data\[')."></label>","<tbody>\n";$Em="";$kl=tables_list();foreach($kl
as$B=>$T){$Ri=preg_replace('~_.*~','',$B);$lb=($a==""||$a==(substr($a,-1)=="%"?"$Ri%":$B));$Yi="<tr><td>".checkbox("tables[]",$B,$lb,$B,"","block");if($T!==null&&!preg_match('~table~i',$T))$Em
.="$Yi\n";else
echo"$Yi<td align='right'><label class='block'><span id='Rows-".h($B)."'></span>".checkbox("data[]",$B,$lb)."</label>\n";$Si[$Ri]++;}echo$Em;if($kl)echo
script("ajaxSetHtml('".js_escape(ME)."script=db');");}else{$i=adminer()->databases();echo"<thead><tr><th style='text-align: left;'>","<label class='block'>".($i?"<input type='checkbox' id='check-databases'".($a==""?" checked":"")." class='jsonly' title='".'All'."'".on('click','formCheck','^databases\[').">":"").'Database'."</label>","<tbody>\n";if($i){foreach($i
as$j){if(!information_schema($j)){$Ri=preg_replace('~_.*~','',$j);echo"<tr><td>".checkbox("databases[]",$j,$a==""||$a=="$Ri%",$j,"","block")."\n";$Si[$Ri]++;}}}else
echo"<tr><td><textarea name='databases' rows='10' cols='20'></textarea>";}echo'</table>
</form>
';$Kd=true;foreach($Si
as$x=>$W){if($x!=""&&$W>1){echo($Kd?"<p>":" ")."<a href='".h(ME)."dump=".url_escape("$x%")."'>".h($x)."</a>";$Kd=false;}}}elseif(isset($_GET["privileges"])){page_header('Privileges');echo'<p class="links"><a href="'.h(ME).'user=">'.'Create user'."</a>";$G=connection()->query("SELECT User, Host FROM mysql.".(DB==""?"user":"db WHERE ".q(DB)." LIKE Db")." ORDER BY Host, User");$ke=$G;if(!$G)$G=connection()->query("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', 1) AS User, SUBSTRING_INDEX(CURRENT_USER, '@', -1) AS Host");echo"<form action=''><p>\n";hidden_fields_get();echo
input_hidden("db",DB),($ke?"":input_hidden("grant")),"<table class='odds'>\n","<thead><tr><th>".'Username'."<th>".'Server'."<td class='hover'><tbody>\n";while($I=$G->fetch_assoc())echo'<tr><td>'.h($I["User"]),"<td>".h($I["Host"]),'<td class="hover"><a href="'.h(ME.'user='.url_escape($I["User"]).'&host='.url_escape($I["Host"])).'">'.'Edit'."</a>\n";if(!$ke||DB!="")echo"<tr><td><input name='user' autocapitalize='off'>","<td><input name='host' value='localhost' autocapitalize='off'>","<td class='hover'><input type='submit' value='".'Edit'."'>\n";echo"</table>\n","</form>\n";}elseif(isset($_GET["sql"])){if(!$l&&$_POST["export"]){save_settings(array("output"=>$_POST["output"],"format"=>$_POST["format"]),"adminer_import");dump_headers("sql");if($_POST["format"]=="sql")echo"$_POST[query]\n";else{adminer()->dumpTable("","");adminer()->dumpData("","table",$_POST["query"]);adminer()->dumpFooter();}exit;}restart_session();$Je=&get_session("queries");$Ie=&$Je[DB];if(!$l&&$_POST["clear"]){$Ie=array();redirect(remove_from_uri("history"));}stop_session();$pa=get_settings("adminer_import");if($_POST&&$pa)save_settings($pa,"adminer_import");page_header((isset($_GET["import"])?'Import':'SQL command'),$l);$dg=driver()->lineComment();if(!$l&&$_POST&&!(isset($_GET["import"])&&adminer()->importProcess())){$rc=driver()->delimiter;$Yd=false;if(!isset($_GET["import"]))$F=$_POST["query"];elseif($_POST["webfile"]){$Ek=adminer()->importServerPath();$Yd=@fopen((file_exists($Ek)?$Ek:"compress.zlib://$Ek.gz"),"rb");$F=($Yd?fread($Yd,1e6):false);}else$F=get_file("sql_file",true,$rc);if(is_string($F)){if(($Dg=ini_bytes("memory_limit"))!="-1")ini_set("memory_limit",max($Dg,strval(2*strlen($F)+memory_get_usage()+8e6)));if($F!=""&&strlen($F)<1e6){$fj=$F.(preg_match("~$rc\\s*\$~",$F)?"":$rc);if(!$Ie||first(end($Ie))!=$fj){restart_session();$Ie[]=array($fj,time());set_session("queries",$Je);stop_session();}}$Bk="(?:\\s|/\\*[\s\S]*?\\*/|(?:$dg)[^\n]*\n?|--\r?\n)";$yh=0;$Xc=true;$Sb=false;$g=connect();if($g&&DB!=""){$g->select_db(DB);if($_GET["ns"]!="")set_schema($_GET["ns"],$g);}$_b=0;$ed=array();$mi='[\'"'.(JUSH=="sql"?'`':(JUSH=="sqlite"?'`[':(JUSH=="mssql"?'[':''))).']|/\*|'.$dg.'|$'.(JUSH=="pgsql"?'|\$([a-zA-Z]\w*)?\$':'');$Gl=microtime(true);while($F!=""){if(!$yh&&preg_match("~^$Bk*+DELIMITER\\s+(\\S+)~i",$F,$A)){$rc=preg_quote($A[1]);$F=substr($F,strlen($A[0]));}elseif(!$yh&&JUSH=='pgsql'&&preg_match("~^($Bk*+COPY\\s+)[^;]+\\s+FROM\\s+stdin;~i",$F,$A)){$rc="\n\\\\\\.\r?\n";$Sb=true;$yh=strlen($A[0]);}else{preg_match("($rc\\s*|$mi)",$F,$A,PREG_OFFSET_CAPTURE,$yh);list($Wd,$Ki)=$A[0];if(!$Wd&&$Yd&&!feof($Yd))$F
.=fread($Yd,1e5);else{if(!$Wd&&rtrim($F)=="")break;$yh=$Ki+strlen($Wd);if($Wd&&!preg_match("(^$rc)",$Wd)){$cb=driver()->hasCStyleEscapes()||(JUSH=="pgsql"&&($Ki>0&&strtolower($F[$Ki-1])=="e"));$Bi=($Wd=='/*'?'\*/':($Wd=='['?']':(preg_match("~^(?:$dg)~",$Wd)?"\n":preg_quote($Wd).($cb?'|\\\\.':''))));while(preg_match("($Bi|\$)s",$F,$A,PREG_OFFSET_CAPTURE,$yh)){$Lj=$A[0][0];if(!$Lj&&$Yd&&!feof($Yd))$F
.=fread($Yd,1e5);else{$yh=$A[0][1]+strlen($Lj);if(!$Lj||$Lj[0]!="\\")break;}}}else{$fj=substr($F,0,$Ki+($Sb?3:0));$F=substr($F,$yh);$yh=0;if($Sb){$rc=driver()->delimiter;$Sb=false;}$sb="<code class='jush-".JUSH."'>".adminer()->sqlCommandQuery($fj)."</code>";if(preg_match("~^$Bk*+\$~",$fj)&&!preg_match('~/\*M?!~',$fj)){echo($_POST["only_errors"]?"":"<pre>$sb</pre>\n");continue;}$Xc=false;$_b++;$Yi="<pre id='sql-$_b'>$sb</pre>\n";if(JUSH=="sqlite"&&preg_match("~^$Bk*+(ATTACH|VACUUM\\b.*\\bINTO)\\b~is",$fj,$A)!==0){echo$Yi,"<p class='error'>".sprintf('%s queries are not supported.',preg_match('~ATTACH~i',$A[1])?'ATTACH':'VACUUM INTO')."\n";$ed[]=" <a href='#sql-$_b'>$_b</a>";if($_POST["error_stops"])break;}else{if(!$_POST["only_errors"]){echo$Yi;ob_flush();flush();}$Kk=microtime(true);if(connection()->multi_query($fj)&&$g&&preg_match("~^$Bk*+USE\\b~i",$fj))$g->query($fj);do{$G=connection()->store_result();if(connection()->error){echo($_POST["only_errors"]?$Yi:""),"<p class='error'>".'Error in query'.(connection()->errno?" (".connection()->errno.")":"").": ".adminer()->error()."\n";$ed[]=" <a href='#sql-$_b'>$_b</a>";if($_POST["error_stops"])break
2;}else{$_=ME."sql=".url_escape(trim($fj));$wl=" <span class='time'>(".format_time($Kk).")</span>".(strlen($_)<1900?" <a href='".h($_)."'>".'Edit'."</a>":"");$ra=connection()->affected_rows;$Im=($_POST["only_errors"]?"":driver()->warnings());$Jm="warnings-$_b";if($Im)$wl
.=", <a href='#$Jm' class='toggle'>".'Warnings'."</a>";$pd=null;$Vh=null;$qd="explain-$_b";if(is_object($G)){$z=$_POST["limit"];$qh=$z;$Vh=print_select_result($G,$g,array(),$qh);if(!$_POST["only_errors"]){echo"<form action='' method='post'>\n";$qh=max($G->num_rows,$qh);echo"<p class='sql-footer'>".($qh?($z&&$qh>$z?sprintf('%d / ',$z):"").lang_format(array('%d row','%d rows'),$qh):""),$wl;if($g&&preg_match("~^($Bk|\\()*+SELECT\\b~i",$fj)&&($pd=explain($g,$fj)))echo", <a href='#$qd' class='toggle'>Explain</a>";$t="export-$_b";echo", <a href='#$t' class='toggle'>".'Export'."</a><span id='$t' class='hidden'>: ".html_select("output",adminer()->dumpOutput(),$pa["output"])." ".html_select("format",adminer()->dumpFormat(),$pa["format"]).input_hidden("query",$fj)."<input type='submit' name='export' value='".'Export'."'".($z?"":on('click','sqlExport')).">".input_token()."</span>\n"."</form>\n";}}else{if(preg_match("~^$Bk*+(CREATE|DROP|ALTER)$Bk++(DATABASE|SCHEMA)\\b~i",$fj)){restart_session();set_session("dbs",null);stop_session();}if(!$_POST["only_errors"])echo"<p class='message' title='".h(connection()->info)."'>".lang_format(array('Query executed OK, %d row affected.','Query executed OK, %d rows affected.'),$ra)."$wl\n";}echo($Im?"<div id='$Jm' class='hidden'>\n$Im</div>\n":"");if($pd){echo"<div id='$qd' class='hidden explain'>\n";print_select_result($pd,$g,$Vh);echo"</div>\n";}}$Kk=microtime(true);}while(connection()->next_result());}}}}}if($Xc)echo"<p class='message'>".'No commands to execute.'."\n";else{$Xe=connection()->inTransaction();driver()->rollback();if($Xe)echo"<pre><code class='jush-".JUSH."'>ROLLBACK -- Adminer</code></pre>\n";if($_POST["only_errors"])echo"<p class='message'>".lang_format(array('%d query executed OK.','%d queries executed OK.'),$_b-count($ed))," <span class='time'>(".format_time($Gl).")</span>\n";elseif($ed&&$_b>1)echo"<p class='error'>".'Error in query'.": ".implode("",$ed)."\n";}}else
echo"<p class='error'>".upload_error($F)."\n";}echo'
<form action="" method="post" enctype="multipart/form-data" id="form"';$km="";if(!isset($_GET["import"]))echo
on('submit','sqlSubmit',remove_from_uri("sql|limit|error_stops|only_errors|history"));else
echo
on_upload_progress($km);echo'>
';$md="<input type='submit' value='".'Execute'."' title='Ctrl+Enter'>";if(!isset($_GET["import"])){$fj=$_GET["sql"];if($_POST)$fj=$_POST["query"];elseif($_GET["history"]=="all")$fj=$Ie;elseif($_GET["history"]!="")$fj=idx($Ie[$_GET["history"]],0);echo"<p>";textarea("query",$fj,20);echo($_POST?"":script("qs('textarea').focus();")),"<p>";adminer()->sqlPrintAfter();echo"$md\n",'Limit rows'.": <input type='number' name='limit' class='size' value='".h($_POST?$_POST["limit"]:$_GET["limit"])."'>\n";}else{$te=(extension_loaded("zlib")?"[.gz]":"");echo"<fieldset><legend>".'File upload'."</legend><div>",($km?input_hidden(ini_get("session.upload_progress.name"),$km):""),"SQL$te: ".file_input(" name='sql_file[]' multiple","\n$md"),($km?" <progress class='jsonly hidden' max='1' value='0'></progress>":""),"</div></fieldset>\n";$Ue=adminer()->importServerPath();if($Ue)echo"<fieldset><legend>".'From server'."</legend><div>",sprintf('Webserver file %s',"<code>".h($Ue)."$te</code>")," <input type='submit' name='webfile' value='".'Run file'."'>","</div></fieldset>\n";adminer()->importPrint();echo"<p>";}echo
checkbox("error_stops",1,($_POST?$_POST["error_stops"]:isset($_GET["import"])||$_GET["error_stops"]),'Stop on error')."\n",checkbox("only_errors",1,($_POST?$_POST["only_errors"]:isset($_GET["import"])||$_GET["only_errors"]),'Show only errors')."\n",input_token();if(!isset($_GET["import"])&&$Ie){print_fieldset("history",'History',$_GET["history"]!="");for($W=end($Ie);$W;$W=prev($Ie)){$x=key($Ie);list($fj,$wl,$Tc)=$W;echo'<div><a href="'.h(ME."sql=&history=$x").'" class="hover">'.'Edit'."</a>"." <span class='time' title='".@date('Y-m-d',$wl)."'>".@date("H:i:s",$wl)."</span>"." <code class='jush-".JUSH."'>".shorten_utf8(preg_replace('~\s+~',' ',ltrim(preg_replace("~^(?:$dg).*~m",'',$fj))),80,"</code>").($Tc?" <span class='time'>($Tc)</span>":"")."</div>\n";}echo"<input type='submit' name='clear' value='".'Clear'."'>\n","<a href='".h(ME."sql=&history=all")."'>".'Edit all'."</a>\n","</div></fieldset>\n";}echo'</form>
';}elseif(isset($_GET["edit"])){$a=$_GET["edit"];$n=fields($a);$Z=(isset($_GET["select"])?($_POST["check"]&&count($_POST["check"])==1?where_check($_POST["check"][0],$n):""):where($_GET,$n));$im=(isset($_GET["select"])?$_POST["edit"]:$Z);foreach($n
as$B=>$m){if((!$im&&!isset($m["privileges"]["insert"]))||adminer()->fieldName($m)=="")unset($n[$B]);}if($_POST&&!$l&&!isset($_GET["select"])){$ig=relative_uri((string)$_POST["referer"]);if($_POST["insert"])$ig=($im?null:relative_uri());elseif(!preg_match('~^.+&select=.+$~',$ig))$ig=ME."select=".url_escape($a);$w=indexes($a);$bm=unique_array($_GET["where"],$w);$ij="\nWHERE $Z";if(isset($_POST["delete"]))queries_redirect($ig,'Item has been deleted.',driver()->delete($a,$ij,$bm?0:1));else{$N=array();foreach($n
as$B=>$m){$W=process_input($m);if($W!==false&&$W!==null)$N[idf_escape($B)]=$W;}if($im){if(!$N)redirect($ig);queries_redirect($ig,'Item has been updated.',driver()->update($a,$N,$ij,$bm?0:1));if(is_ajax()){page_headers();page_messages($l);exit;}}else{$G=driver()->insert($a,$N);$Tf=($G?last_id($G):0);queries_redirect($ig,sprintf('Item%s has been inserted.',($Tf?" $Tf":"")),$G);}}}$I=null;$F="";$wl="";if($Z){$L=array();$Vj=array("*");foreach($n
as$B=>$m){if(isset($m["privileges"]["select"])){$Ca=($_POST["clone"]&&$m["auto_increment"]?"''":convert_field($m));$d=($Ca?"$Ca AS ":"").idf_escape($B);$L[]=$d;if($Ca)$Vj[]=$d;}}$I=array();if(!support("table")){$L=array("*");$Vj=$L;}if($L){$Kk=microtime(true);$G=driver()->select($a,$L,array($Z),$L,array(),(isset($_GET["select"])?2:1));$F=str_replace("SELECT ".implode(", ",$L),"SELECT ".implode(", ",$Vj),driver()->query);$wl=format_time($Kk);if(!$G)$l=adminer()->error();else{$I=$G->fetch_assoc();if(!$I)$I=false;}if(isset($_GET["select"])&&(!$I||$G->fetch_assoc()))$I=null;}}if(!$n&&driver()->primary!=""){if(!$Z){$G=driver()->select($a,array("*"),array(),array("*"));$I=($G?$G->fetch_assoc():false);if(!$I)$I=array(driver()->primary=>"");}if($I){foreach($I
as$x=>$W){if(!$Z)$I[$x]=null;$n[$x]=array("field"=>$x,"null"=>($x!=driver()->primary),"auto_increment"=>($x==driver()->primary));}}}if($_POST["save"]){$Ni=array();foreach((array)$_POST["fields"]as$x=>$W)$Ni[bracket_escape($x,true)]=$W;$I=$Ni+($I?$I:array());}edit_form($a,$n,$I,$im,$l,$F,$wl);}elseif(isset($_GET["create"])){function
referencable_primary($Yj){$H=array();foreach(table_status('',true)as$bl=>$Q){if($bl!=$Yj&&!$Q["dependent"]&&fk_support($Q)){foreach(fields($bl)as$m){if($m["primary"]){if($H[$bl]){unset($H[$bl]);break;}$H[$bl]=$m;}}}}return$H;}$a=$_GET["create"];$ri=driver()->partitionBy;$vi=($ri&&$a!=""?driver()->partitionsInfo($a):array());$pj=referencable_primary($a);$Ud=array();foreach($pj
as$bl=>$m)$Ud[str_replace("`","``",$bl)."`".str_replace("`","``",$m["field"])]=$bl;$Yh=array();$R=array();if($a!=""){$Yh=fields($a);$R=table_status1($a);if(count($R)<2)$l='No tables.';}$xa=($a==""||driver()->supportsAlterTable($R));$I=$_POST;$I["fields"]=(array)$I["fields"];if($I["auto_increment_col"])$I["fields"][$I["auto_increment_col"]]["auto_increment"]=true;if($_POST&&!$l)save_settings(array("comments"=>$_POST["comments"],"defaults"=>$_POST["defaults"]));if($_POST&&!process_fields($I["fields"])&&!$l){if($_POST["drop"])queries_redirect(substr(ME,0,-1),'Table has been dropped.',drop_tables(array($a)));else{$n=array();$va=array();$om=false;$Sd=array();$Xh=reset($Yh);$ta=" FIRST";foreach($I["fields"]as$m){$p=$Ud[$m["type"]];$Ul=($p!==null?$pj[$p]:$m);if($m["field"]!=""){if(!$m["generated"])$m["default"]=null;$dj=process_field($m,$Ul);$va[]=array($m["orig"],$dj,$ta);if(!$Xh||$dj!==process_field($Xh,$Xh)){$n[]=array($m["orig"],$dj,$ta);if($m["orig"]!=""||$ta)$om=true;}if($p!==null)$Sd[idf_escape($m["field"])]=($a!=""&&JUSH!="sqlite"?"ADD":" ").format_foreign_key(array('table'=>$Ud[$m["type"]],'source'=>array($m["field"]),'target'=>array($Ul["field"]),'on_delete'=>$m["on_delete"],));$ta=" AFTER ".idf_escape($m["field"]);}elseif($m["orig"]!=""){$om=true;$n[]=array($m["orig"]);}if($m["orig"]!=""){$Xh=next($Yh);if(!$Xh)$ta="";}}$ti=array();if(in_array($I["partition_by"],$ri)){foreach($I
as$x=>$W){if(preg_match('~^partition~',$x))$ti[$x]=$W;}foreach($ti["partition_names"]as$x=>$B){if($B==""){unset($ti["partition_names"][$x]);unset($ti["partition_values"][$x]);}}$ti["partition_names"]=array_values($ti["partition_names"]);$ti["partition_values"]=array_values($ti["partition_values"]);if($ti==$vi)$ti=array();}elseif(preg_match("~partitioned~",$R["Create_options"]))$ti=null;$Fg='Table has been altered.';if($a==""){cookie("adminer_engine",$I["Engine"]);$Fg='Table has been created.';}$B=trim($I["name"]);$ig=ME.(support("table")?"table=":"select=").url_escape($B);$G=alter_table($a,$B,(JUSH=="sqlite"&&($om||$Sd)?$va:$n),$Sd,($I["Comment"]!=$R["Comment"]?$I["Comment"]:null),($I["Engine"]&&$I["Engine"]!=$R["Engine"]?$I["Engine"]:""),($I["Collation"]&&$I["Collation"]!=$R["Collation"]?$I["Collation"]:""),($I["Auto_increment"]!=""?number($I["Auto_increment"]):""),$ti);if($G&&!Queries::$queries&&$a!=""&&!$n&&!$Sd)redirect($ig);queries_redirect($ig,$Fg,$G);}}page_header(($a!=""?'Alter table':'Create table'),$l,array("table"=>$a),h($a));if(!$_POST){$Xl=driver()->types();$I=array("Engine"=>$_COOKIE["adminer_engine"],"fields"=>array(array("field"=>"","type"=>(isset($Xl["int"])?"int":(isset($Xl["integer"])?"integer":"")),"on_update"=>"")),"partition_names"=>array(""),);if($a!=""){$I=$R;$I["name"]=$a;$I["fields"]=array();if(!$_GET["auto_increment"])$I["Auto_increment"]="";foreach($Yh
as$m){if($m["generated"])$m["default"]=ltrim($m["default"]);$m["generated"]=$m["generated"]?:(isset($m["default"])?"DEFAULT":"");$I["fields"][]=$m;}if($ri){$I+=$vi;$I["partition_names"][]="";$I["partition_values"][]="";}}}$wb=flat_collations();$Zc=driver()->engines();foreach($Zc
as$Yc){if(!strcasecmp($Yc,$I["Engine"])){$I["Engine"]=$Yc;break;}}$rg=max_input_vars(12,20);if($rg){$He=(count($I["fields"])>$rg?"":" hidden");echo"<p".($He?" id='max-fields' data-columns='$rg'":"")." class='error$He'>".max_input_vars_error()."\n";}echo'
<form action="" method="post" id="form">
<p>
';if(support("columns")||$a==""){echo'Table name'.": <input name='name'".($a==""&&!$_POST?" autofocus":"")." data-maxlength='64' value='".h($I["name"])."' autocapitalize='off'>\n",(!$xa?h($R["Engine"])."\n":($Zc?html_select("Engine",array(""=>"(".'engine'.")")+$Zc,$I["Engine"],on('change','helpClose').on_help_value())."\n":""));if($wb)echo"<datalist id='collations'>".optionlist($wb)."</datalist>\n",(preg_match("~sqlite|mssql~",JUSH)?"":"<input list='collations' name='Collation' value='".h($I["Collation"])."' placeholder='(".'collation'.")'>\n");echo"<input type='submit' value='".'Save'."'>\n";}if(support("columns")&&$xa){echo"<div class='scrollable'>\n","<table id='edit-fields' class='nowrap'>\n";edit_fields($I["fields"],$wb,"TABLE",$Ud);echo"</table>\n",script("editFields();"),"</div>\n<p>\n",'Auto Increment'.": <input type='number' name='Auto_increment' class='size' value='".h($I["Auto_increment"])."'>\n",checkbox("defaults",1,($_POST?$_POST["defaults"]:get_setting("defaults")),'Default values',on('click','columnShowClick',5),"jsonly");$Cb=($_POST?$_POST["comments"]:get_setting("comments"));if(support("comment")){echo
checkbox("comments",1,$Cb,'Comment',on('click','editingCommentsClick',true),"jsonly").' ';$c=" name='Comment' data-maxlength='".(min_version(5.5)?2048:60)."'".($Cb?"":" class='hidden'");echo
adminer()->commentInput('TABLE',$c,$I["Comment"]);}echo'<p>
<input type=\'submit\' value=\'Save\'>
';}echo'
';if($a!="")echo'<input type=\'submit\' name=\'drop\' value=\'Drop\'',confirm(sprintf('Drop %s?',$a)),'>
';if($ri&&(JUSH=='sql'||$a=="")){$si=preg_match('~RANGE|LIST~',$I["partition_by"]);print_fieldset("partition",'Partition by',$I["partition_by"]);echo"<p>".html_select("partition_by",array_merge(array(""),$ri),$I["partition_by"],on('change','partitionByChange').on_help_value('.','PARTITION BY $&'))."\n","(<input name='partition' value='".h($I["partition"])."'>)\n",'Partitions'.": <input type='number' name='partitions' class='size".($si||!$I["partition_by"]?" hidden":"")."' value='".h($I["partitions"])."'>\n","<table id='partition-table'".($si?"":" class='hidden'").">\n","<thead><tr><th>".'Partition name'."<th>".'Values'."<tbody>\n";foreach($I["partition_names"]as$x=>$W)echo'<tr>','<td><input name="partition_names[]" value="'.h($W).'" autocapitalize="off"'.($x==count($I["partition_names"])-1?on('input','partitionNameChange'):'').'>','<td><input name="partition_values[]" value="'.h(idx($I["partition_values"],$x)).'">';echo"</table>\n</div></fieldset>\n";}echo
input_token(),'</form>
';}elseif(isset($_GET["indexes"])){$a=$_GET["indexes"];$df=array("PRIMARY","UNIQUE","INDEX");$R=table_status1($a,true);$af=driver()->indexAlgorithms($R);if(preg_match('~MyISAM|M?aria'.(min_version(5.6,'10.0.5')?'|InnoDB':'').'~i',$R["Engine"]))$df[]="FULLTEXT";if(preg_match('~MyISAM|M?aria'.(min_version(5.7,'10.2.2')?'|InnoDB':'').'~i',$R["Engine"]))$df[]="SPATIAL";if(min_version('',11.7)&&preg_match('~MyISAM|InnoDB~i',$R["Engine"]))$df[]="VECTOR";$w=indexes($a);$n=fields($a);$Wi=array();if(JUSH=="mongo"){$Wi=$w["_id_"];unset($df[0]);unset($w["_id_"]);}$I=$_POST;if($I)save_settings(array("index_options"=>$I["options"]));if($_POST&&!$l&&!$_POST["add"]&&!$_POST["drop_col"]){$b=array();foreach($I["indexes"]as$v){$B=$v["name"];if(in_array($v["type"],$df)){$e=array();$ag=array();$uc=array();$Kh=array();$bf=(support("partial_indexes")?$v["partial"]:"");$Ze=(in_array($v["algorithm"],$af)?$v["algorithm"]:"");$N=array();ksort($v["columns"]);foreach($v["columns"]as$x=>$d){if($d!=""){$y=idx($v["lengths"],$x);$sc=idx($v["descs"],$x);$Jh=idx($v["opclasses"],$x);$N[]=($n[$d]?idf_escape($d):$d).($y?"(".(+$y).")":"").($Jh!=""?" ".idf_escape($Jh):"").($sc?" DESC":"");$e[]=$d;$ag[]=($y?:null);$uc[]=$sc;$Kh[]="$Jh";}}$nd=$w[$B];if($nd){ksort($nd["columns"]);ksort($nd["lengths"]);ksort($nd["descs"]);if($v["type"]==$nd["type"]&&array_values($nd["columns"])===$e&&(!$nd["lengths"]||array_values($nd["lengths"])===$ag)&&array_values($nd["descs"])===$uc&&(!$nd["opclasses"]||array_values($nd["opclasses"])===$Kh)&&$nd["partial"]==$bf&&(!$af||$nd["algorithm"]==$Ze)){unset($w[$B]);continue;}}if($e)$b[]=array($v["type"],$B,$N,$Ze,$bf);}}foreach($w
as$B=>$nd)$b[]=array($nd["type"],$B,"DROP");if(!$b)redirect(ME."table=".url_escape($a));queries_redirect(ME."table=".url_escape($a),'Indexes have been altered.',alter_indexes($a,$b));}page_header('Indexes',$l,array("table"=>$a),h($a));$Dd=array_keys($n);if($_POST["add"]){foreach($I["indexes"]as$x=>$v){if($v["columns"][count($v["columns"])]!="")$I["indexes"][$x]["columns"][]="";}$v=end($I["indexes"]);if($v["type"]||array_filter($v["columns"],'strlen'))$I["indexes"][]=array("columns"=>array(1=>""));}if(!$I){foreach($w
as$x=>$v){$w[$x]["name"]=$x;$w[$x]["columns"][]="";}$w[]=array("columns"=>array(1=>""));$I["indexes"]=$w;}$ag=(JUSH=="sql"||JUSH=="mssql");$Kh=driver()->indexOpclasses();$rk=($_POST?$_POST["options"]:get_setting("index_options"));echo'
<form action="" method="post">
<div class="scrollable">
<table class="nowrap odds">
<thead><tr>
<th id="label-type">Index Type
';$Se=" class='idxopts".($rk?"":" hidden")."'";if($af)echo"<th id='label-algorithm'$Se>".'Algorithm'.doc_link(array('sql'=>'create-index.html#create-index-storage-engine-index-types','mariadb'=>'storage-engine-index-types/','pgsql'=>'indexes-types.html',));echo'<th><input type="submit" hidden>','Columns'.($ag?"<span$Se> (".'length'.")</span>":"");if($ag||support("descidx"))echo
checkbox("options",1,$rk,'Options',on('click','indexOptionsShow'),"jsonly")."\n";echo'<th id="label-name">Name
';if(support("partial_indexes"))echo"<th id='label-condition'$Se>".'Condition';echo'<th><noscript>',icon("plus","add[0]","+",'Add next'),'</noscript>
<tbody>
';if($Wi){echo"<tr><td>PRIMARY<td>";foreach($Wi["columns"]as$x=>$d)echo
select_input(" disabled",array_combine($Dd,$Dd),$d),"<label><input disabled type='checkbox'>".'descending'."</label> ";echo"<td><td>\n";}$Df=1;foreach($I["indexes"]as$v){if(!$_POST["drop_col"]||$Df!=key($_POST["drop_col"])){echo"<tr><td>".html_select("indexes[$Df][type]",array(-1=>"")+$df,$v["type"],($Df==count($I["indexes"])?on('change','indexesAddRow'):""),"label-type");if($af)echo"<td$Se>".html_select("indexes[$Df][algorithm]",array_merge(array(""),$af),$v['algorithm'],"","label-algorithm");echo"<td>";ksort($v["columns"]);$s=1;foreach($v["columns"]as$x=>$d){echo"<span>".select_input(" name='indexes[$Df][columns][$s]' title='".'Column'."'".on('change','indexesChangeColumn',(JUSH=="sql"?"":$_GET["indexes"]."_")),($n&&($d==""||$n[$d])?array_combine($Dd,$Dd):array()),$d)," <span$Se>",($ag?"<input type='number' name='indexes[$Df][lengths][$s]' class='size' value='".h(idx($v["lengths"],$x))."' title='".'Length'."'>":"");if($Kh){$Jh=idx($v["opclasses"],$x);echo
html_select("indexes[$Df][opclasses][$s]",array(""=>"(".'operator class'.")")+array_combine($Kh,$Kh)+($Jh!=""?array($Jh=>$Jh):array()),$Jh),doc_link(array('pgsql'=>'indexes-opclass.html'));}echo(support("descidx")?checkbox("indexes[$Df][descs][$s]",1,idx($v["descs"],$x),'descending'):""),"<br>","</span></span>";$s++;}echo"<td><input name='indexes[$Df][name]' value='".h($v["name"])."' autocapitalize='off' aria-labelledby='label-name'>\n";if(support("partial_indexes"))echo"<td$Se><input name='indexes[$Df][partial]' value='".h($v["partial"])."' autocapitalize='off' aria-labelledby='label-condition'>\n";echo"<td>".icon("cross","drop_col[$Df]","x",'Remove',on('click','editingRemoveRow','indexes$1[type]'));}$Df++;}echo'</table>
</div>
<p>
<input type=\'submit\' value=\'Save\'>
',input_token(),'</form>
';}elseif(isset($_GET["database"])){$I=$_POST;if($_POST&&!$l&&!$_POST["add"]){$B=trim($I["name"]);if($_POST["drop"]){$_GET["db"]="";queries_redirect(remove_from_uri("db|database"),'Database has been dropped.',drop_databases(array(DB)));}elseif($B!==DB){if(DB!=""){$_GET["db"]=$B;queries_redirect(preg_replace('~\bdb=[^&]*&~','',ME)."db=".url_escape($B),'Database has been renamed.',rename_database($B,(string)$I["collation"]));}else{$i=explode("\n",str_replace("\r","",$B));$Qk=true;$Rf="";foreach($i
as$j){if(count($i)==1||$j!=""){if(!create_database($j,(string)$I["collation"]))$Qk=false;$Rf=$j;}}restart_session();set_session("dbs",null);queries_redirect(preg_replace('~&db=[^&]*~','',ME)."db=".url_escape($Rf),'Database has been created.',$Qk);}}else{if(!$I["collation"])redirect(substr(ME,0,-1));query_redirect("ALTER DATABASE ".idf_escape($B).(preg_match('~^[a-z0-9_]+$~i',$I["collation"])?" COLLATE $I[collation]":""),substr(ME,0,-1),'Database has been altered.');}}page_header(DB!=""?'Alter database':'Create database',$l,array(),h(DB));$wb=collations();$B=DB;if($_POST)$B=$I["name"];elseif(DB!="")$I["collation"]=db_collation(DB,$wb);elseif(JUSH=="sql"){foreach(get_vals("SHOW GRANTS")as$ke){if(preg_match('~ ON (`(([^\\\\`]|``|\\\\.)*)%`\.\*)?~',$ke,$A)&&$A[1]){$B=stripcslashes(idf_unescape("`$A[2]`"));break;}}}echo'
<form action="" method="post">
<p>
',($_POST["add"]||strpos($B,"\n")?'<textarea autofocus name="name" rows="10" cols="40">'.h($B).'</textarea><br>':'<input name="name" autofocus value="'.h($B).'" data-maxlength="64" autocapitalize="off">')."\n",($wb?html_select("collation",array(""=>"(".'collation'.")")+$wb,$I["collation"]).doc_link(array('sql'=>"charset-charsets.html",'mariadb'=>"supported-character-sets-and-collations/",'mssql'=>"relational-databases/system-functions/sys-fn-helpcollations-transact-sql",)):"")."\n",'<input type=\'submit\' value=\'Save\'>
';if(DB!="")echo"<input type='submit' name='drop' value='".'Drop'."'".confirm(sprintf('Drop %s?',DB)).">\n";elseif(!$_POST["add"]&&$_GET["db"]=="")echo
icon("plus","add[0]","+",'Add next')."\n";echo
input_token(),'</form>
';}elseif(isset($_GET["scheme"])){$I=$_POST;if($_POST&&!$l){$_=preg_replace('~ns=[^&]*&~','',ME)."ns=";if($_POST["drop"])query_redirect("DROP SCHEMA ".idf_escape($_GET["ns"]),$_,'Schema has been dropped.');else{$B=trim($I["name"]);$_
.=url_escape($B);if($_GET["ns"]=="")query_redirect("CREATE SCHEMA ".idf_escape($B),$_,'Schema has been created.');elseif($_GET["ns"]!=$B)query_redirect("ALTER SCHEMA ".idf_escape($_GET["ns"])." RENAME TO ".idf_escape($B),$_,'Schema has been altered.');else
redirect($_);}}page_header($_GET["ns"]!=""?'Alter schema':'Create schema',$l);if(!$I)$I["name"]=$_GET["ns"];echo'
<form action="" method="post">
<p><input name="name" autofocus value="',h($I["name"]),'" autocapitalize="off">
<input type=\'submit\' value=\'Save\'>
';if($_GET["ns"]!="")echo"<input type='submit' name='drop' value='".'Drop'."'".confirm(sprintf('Drop %s?',$_GET["ns"])).">\n";echo
input_token(),'</form>
';}elseif(isset($_GET["call"])){$ba=($_GET["name"]?:$_GET["call"]);page_header('Call'.": ".h($ba),$l);$Ij=(isset($_GET["callf"])?"FUNCTION":"PROCEDURE");$Fj=routine($_GET["call"],$Ij);$Ve=array();$ei=array();foreach($Fj["fields"]as$s=>$m){if(substr($m["inout"],-3)=="OUT"&&JUSH=='sql')$ei[$s]="@".idf_escape($m["field"])." AS ".idf_escape($m["field"]);if(!$m["inout"]||substr($m["inout"],0,2)=="IN")$Ve[]=$s;}if(!$l&&$_POST){$db=array();foreach($Fj["fields"]as$x=>$m){$W="";if(in_array($x,$Ve)){$W=process_input($m);if($W===false)$W="''";if(isset($ei[$x]))connection()->query("SET @".idf_escape($m["field"])." = $W");}if(isset($ei[$x]))$db[]="@".idf_escape($m["field"]);elseif(in_array($x,$Ve))$db[]=$W;}$F=(isset($_GET["callf"])?"SELECT ":"CALL ").(idx($Fj["returns"],"type")=="record"?"* FROM ":"").table($ba)."(".implode(", ",$db).")";$Kk=microtime(true);$G=connection()->multi_query($F);$ra=connection()->affected_rows;echo
adminer()->selectQuery($F,$Kk,!$G);if(!$G)echo"<p class='error'>".adminer()->error()."\n";else{$g=connect();if($g)$g->select_db(DB);do{$G=connection()->store_result();if(is_object($G))print_select_result($G,$g);else
echo"<p class='message'>".lang_format(array('Routine has been called, %d row affected.','Routine has been called, %d rows affected.'),$ra)." <span class='time'>".@date("H:i:s")."</span>\n";}while(connection()->next_result());if($ei)print_select_result(connection()->query("SELECT ".implode(", ",$ei)));}}echo'
<form action="" method="post">
';if($Ve){echo"<table class='layout'>\n";foreach($Ve
as$x){$m=$Fj["fields"][$x];$B=$m["field"];echo"<tr><th>".adminer()->fieldName($m);$X=idx($_POST["fields"],$B);if($X!=""){if($m["type"]=="set")$X=implode(",",$X);}input($m,$X,idx($_POST["function"],$B,""));echo"\n";}echo"</table>\n";}echo'<p>
<input type=\'submit\' value=\'Call\'>
',input_token(),'</form>

',adminer()->commentValue($Ij,$Fj['comment']);}elseif(isset($_GET["foreign"])){$a=$_GET["foreign"];$B=$_GET["name"];$I=$_POST;if($_POST&&!$l&&!$_POST["add"]&&!$_POST["change"]&&!$_POST["change-js"]){if(!$_POST["drop"]){$I["source"]=array_filter($I["source"],'strlen');ksort($I["source"]);$nl=array();foreach($I["source"]as$x=>$W)$nl[$x]=$I["target"][$x];$I["target"]=$nl;}if(JUSH=="sqlite")$G=recreate_table($a,$a,array(),array(),array(" $B"=>($I["drop"]?"":" ".format_foreign_key($I))));else{$b="ALTER TABLE ".table($a);$G=($B==""||queries("$b DROP ".(JUSH=="sql"?"FOREIGN KEY ":"CONSTRAINT ").idf_escape($B)));if(!$I["drop"])$G=queries("$b ADD".format_foreign_key($I));}queries_redirect(ME."table=".url_escape($a),($I["drop"]?'Foreign key has been dropped.':($B!=""?'Foreign key has been altered.':'Foreign key has been created.')),$G);if(!$I["drop"])$l='Source and target columns must have the same data type, there must be an index on the target columns and the referenced data must exist.';}page_header(($B!=""?'Alter foreign key':'Create foreign key'),$l,array("table"=>$a),h($B!=""?$B:$a));if($_POST){ksort($I["source"]);if($_POST["change"]||$_POST["change-js"])$I["target"]=array();else$I["source"][]="";}elseif($B!=""){$Ud=foreign_keys($a);$I=$Ud[$B];$I["source"][]="";}else{$I["table"]=$a;$I["source"]=array("");}echo'
<form action="" method="post">
';$_k=array_keys(fields($a));if($I["db"]!="")connection()->select_db($I["db"]);if($I["ns"]!=""){$Zh=get_schema();set_schema($I["ns"]);}$oj=array_keys(array_filter(table_status('',true),function(array$R){return!$R["dependent"]&&fk_support($R);}));$nl=array_keys(fields(in_array($I["table"],$oj)?$I["table"]:reset($oj)));$c=on('change','foreignChange');echo"<p><label>".'Target table'.": ".html_select("table",$oj,$I["table"],$c)."</label>\n";if(support("scheme")){$Nj=array_filter(adminer()->schemas(),function($K){return!information_schema(DB,$K);});echo"<label>".'Schema'.": ".html_select("ns",$Nj,$I["ns"]!=""?$I["ns"]:$_GET["ns"],$c)."</label>";if($I["ns"]!="")set_schema($Zh);}elseif(JUSH!="sqlite"){$jc=array();foreach(adminer()->databases()as$j){if(!information_schema($j))$jc[]=$j;}echo"<label>".'DB'.": ".html_select("db",$jc,$I["db"]!=""?$I["db"]:$_GET["db"],$c)."</label>";}echo
input_hidden("change-js"),'<noscript><p><input type=\'submit\' name=\'change\' value=\'Change\'></noscript>
<table>
<thead><tr><th id="label-source">Source<th id="label-target">Target<tbody>
';$Df=0;foreach($I["source"]as$x=>$W){echo"<tr>","<td>".html_select("source[".(+$x)."]",array(-1=>"")+$_k,$W,($Df==count($I["source"])-1?on('change','foreignAddRow'):""),"label-source"),"<td>".html_select("target[".(+$x)."]",$nl,idx($I["target"],$x),"","label-target");$Df++;}echo'</table>
<p>
<label>ON DELETE: ',html_select("on_delete",array(-1=>"")+explode("|",driver()->onActions),$I["on_delete"]),'</label>
<label>ON UPDATE: ',html_select("on_update",array(-1=>"")+explode("|",driver()->onActions),$I["on_update"]),'</label>
',(support("deferrable")?html_select("deferrable",array('NOT DEFERRABLE','DEFERRABLE','DEFERRABLE INITIALLY DEFERRED'),$I["deferrable"]).' ':''),doc_link(array('sql'=>"innodb-foreign-key-constraints.html",'mariadb'=>"foreign-keys/",'pgsql'=>"sql-createtable.html#SQL-CREATETABLE-PARMS-REFERENCES",'mssql'=>"t-sql/statements/create-table-transact-sql",'oracle'=>"sqlrf/constraint.html",)),'<p>
<input type=\'submit\' value=\'Save\'>
<noscript><p><input type=\'submit\' name=\'add\' value=\'Add column\'></noscript>
';if($B!="")echo'<input type=\'submit\' name=\'drop\' value=\'Drop\'',confirm(sprintf('Drop %s?',$B)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["view"])){$a=$_GET["view"];$I=$_POST;$ai="VIEW";if(JUSH=="pgsql"&&$a!=""){$O=table_status1($a);$ai=strtoupper($O["Engine"]);}if($_POST&&!$l){$B=trim($I["name"]);$Ca=" AS\n$I[select]";$ig=ME."table=".url_escape($B);$Fg='View has been altered.';$T=($_POST["materialized"]?"MATERIALIZED VIEW":"VIEW");if(!$_POST["drop"]&&$a==$B&&JUSH!="sqlite"&&$T=="VIEW"&&$ai=="VIEW")query_redirect((JUSH=="mssql"?"ALTER":"CREATE OR REPLACE")." VIEW ".table($B).$Ca,$ig,$Fg);else{$rl="adminer_".uniqid();drop_create("DROP $ai ".table($a),"CREATE $T ".table($B).$Ca,"DROP $T ".table($B),"CREATE $T ".table($rl).$Ca,"DROP $T ".table($rl),($_POST["drop"]?substr(ME,0,-1):$ig),'View has been dropped.',$Fg,'View has been created.',$a,$B);}}if(!$_POST&&$a!=""){$I=view($a);$I["name"]=$a;$I["materialized"]=($ai!="VIEW");if(!$l)$l=adminer()->error();}page_header(($a!=""?'Alter view':'Create view'),$l,array("table"=>$a),h($a));echo'
<form action="" method="post">
<p>Name: <input name="name" value="',h($I["name"]),'" data-maxlength="64" autocapitalize="off">
',(support("materializedview")?" ".checkbox("materialized",1,$I["materialized"],'Materialized view'):""),'<p>';textarea("select",$I["select"]);echo'<p>
<input type=\'submit\' value=\'Save\'>
';if($a!="")echo'<input type=\'submit\' name=\'drop\' value=\'Drop\'',confirm(sprintf('Drop %s?',$a)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["event"])){$aa=$_GET["event"];$rf=array("YEAR","QUARTER","MONTH","DAY","HOUR","MINUTE","WEEK","SECOND","YEAR_MONTH","DAY_HOUR","DAY_MINUTE","DAY_SECOND","HOUR_MINUTE","HOUR_SECOND","MINUTE_SECOND");$Mk=array("ENABLED"=>"ENABLE","DISABLED"=>"DISABLE","SLAVESIDE_DISABLED"=>"DISABLE ON SLAVE");$I=$_POST;if($_POST&&!$l){if($_POST["drop"])query_redirect("DROP EVENT ".idf_escape($aa),substr(ME,0,-1),'Event has been dropped.');elseif(in_array($I["INTERVAL_FIELD"],$rf)&&isset($Mk[$I["STATUS"]])){$Mj="\nON SCHEDULE ".($I["INTERVAL_VALUE"]?"EVERY ".q($I["INTERVAL_VALUE"])." $I[INTERVAL_FIELD]".($I["STARTS"]?" STARTS ".q($I["STARTS"]):"").($I["ENDS"]?" ENDS ".q($I["ENDS"]):""):"AT ".q($I["STARTS"]))." ON COMPLETION".($I["ON_COMPLETION"]?"":" NOT")." PRESERVE";queries_redirect(substr(ME,0,-1),($aa!=""?'Event has been altered.':'Event has been created.'),queries(($aa!=""?"ALTER EVENT ".idf_escape($aa).$Mj.($aa!=$I["EVENT_NAME"]?"\nRENAME TO ".idf_escape($I["EVENT_NAME"]):""):"CREATE EVENT ".idf_escape($I["EVENT_NAME"]).$Mj)."\n".$Mk[$I["STATUS"]]." COMMENT ".q($I["EVENT_COMMENT"]).rtrim(" DO\n$I[EVENT_DEFINITION]",";").";"));}}page_header(($aa!=""?'Alter event'.": ".h($aa):'Create event'),$l);if(!$I&&$aa!=""){$J=get_rows("SELECT * FROM information_schema.EVENTS WHERE EVENT_SCHEMA = ".q(DB)." AND EVENT_NAME = ".q($aa));$I=reset($J);}echo'
<form action="" method="post">
<table class="layout">
<tr><th>Name<td><input name="EVENT_NAME" value="',h($I["EVENT_NAME"]),'" data-maxlength="64" autocapitalize="off">
<tr><th title="datetime">Start<td><input name="STARTS" value="',h("$I[EXECUTE_AT]$I[STARTS]"),'">
<tr><th title="datetime">End<td><input name="ENDS" value="',h($I["ENDS"]),'">
<tr><th>Every
<td><input type="number" name="INTERVAL_VALUE" value="',h($I["INTERVAL_VALUE"]),'" class="size"> ',html_select("INTERVAL_FIELD",$rf,$I["INTERVAL_FIELD"]),'<tr><th>Status<td>',html_select("STATUS",$Mk,$I["STATUS"]),'<tr><th>Comment<td><input name="EVENT_COMMENT" value="',h($I["EVENT_COMMENT"]),'" data-maxlength="64">
<tr><th><td>',checkbox("ON_COMPLETION","PRESERVE",$I["ON_COMPLETION"]=="PRESERVE",'On completion preserve'),'</table>
<p>';textarea("EVENT_DEFINITION",$I["EVENT_DEFINITION"]);echo'<p>
<input type=\'submit\' value=\'Save\'>
';if($aa!="")echo'<input type=\'submit\' name=\'drop\' value=\'Drop\'',confirm(sprintf('Drop %s?',$aa)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["procedure"])){$ba=($_GET["name"]?:$_GET["procedure"]);$Fj=(isset($_GET["function"])?"FUNCTION":"PROCEDURE");$I=$_POST;$I["fields"]=(array)$I["fields"];if($_POST&&!process_fields($I["fields"])&&!$l){foreach($I["fields"]as$x=>$m){if($m["field"]=="")unset($I["fields"][$x]);}$Bh=routine_id($ba,routine($_GET["procedure"],$Fj));$gh=routine_id($I["name"],$I);$h=create_routine($Fj,$I);$ig=substr(ME,0,-1);$Fg='Routine has been altered.';if(!$_POST["drop"]&&$Bh==$gh&&connection()->flavor!="mysql")query_redirect(substr_replace($h,' OR REPLACE',6,0),$ig,$Fg);else{$rl="adminer_".uniqid();drop_create("DROP $Fj $Bh",$h,"DROP $Fj $gh",create_routine($Fj,array("name"=>$rl)+$I),"DROP $Fj ".routine_id($rl,$I),$ig,'Routine has been dropped.',$Fg,'Routine has been created.',$ba,$I["name"]);}}page_header(($ba!=""?(isset($_GET["function"])?'Alter function':'Alter procedure').": ".h($ba):(isset($_GET["function"])?'Create function':'Create procedure')),$l);if(!$_POST){if($ba=="")$I["language"]="sql";else{$I=routine($_GET["procedure"],$Fj);$I["name"]=$ba;}}$wb=(JUSH=="sql"?flat_collations():array());$Gj=routine_languages();echo($wb?"<datalist id='collations'>".optionlist($wb)."</datalist>":""),'
<form action="" method="post" id="form">
<p>Name: <input name="name" value="',h($I["name"]),'" data-maxlength="64" autocapitalize="off">
',($Gj?"<label>".'Language'.": ".html_select("language",array_keys($Gj),$I["language"],on('change','routineLanguage',$Gj))."</label>\n":""),'<input type=\'submit\' value=\'Save\'>
',doc_link(array('sql'=>"create-procedure.html",'mariadb'=>($Fj=="FUNCTION"?"create-function/":"create-procedure/"),'pgsql'=>($Fj=="FUNCTION"?"sql-createfunction.html":"sql-createprocedure.html"),),"?"),'<div class="scrollable">
<table id="edit-fields" class="nowrap">
';edit_fields($I["fields"],$wb,$Fj);if(isset($_GET["function"])){echo"<tr><td>".'Return type';edit_type("returns",(array)$I["returns"],$wb,array(),(JUSH=="pgsql"?array("void","trigger"):array()));}echo'</table>
',script("editFields();"),'</div>
<p>';textarea("definition",$I["definition"],20,80,($Gj[$I["language"]]?:JUSH));echo'<p>
<input type=\'submit\' value=\'Save\'>
';if($ba!="")echo'<input type=\'submit\' name=\'drop\' value=\'Drop\'',confirm(sprintf('Drop %s?',$ba)),'>
';$Hj=routine_options($Fj);if($Hj){$I["options"]=(array)$I["options"];$Ph=false;foreach($Hj
as$x=>$Y){$k=($Y?reset($Y):"");$I["options"][$x]=idx($I["options"],$x,$k);if($I["options"][$x]!=$k)$Ph=true;}print_fieldset("options",'Options',$Ph);echo"<table class='layout'>\n";foreach($Hj
as$x=>$Y){$Nf="label-option-$x";$zl=str_replace("_"," ",$x);$L=array();foreach($Y
as$X)$L[$X]=(strpos($X,"$zl ")===0?substr($X,strlen($zl)+1):$X);echo"<tr><th id='$Nf'>$zl<td>".($L?html_select("options[$x]",$L,$I["options"][$x],"",$Nf):"<input name='options[$x]' value='".h($I["options"][$x])."' aria-labelledby='$Nf' autocapitalize='off'>")."\n";}echo"</table>\n</div></fieldset>\n";}echo
input_token(),'</form>
';}elseif(isset($_GET["sequence"])){$da=$_GET["sequence"];$I=$_POST;if($_POST&&!$l){$_=substr(ME,0,-1);$B=trim($I["name"]);if($_POST["drop"])query_redirect("DROP SEQUENCE ".idf_escape($da),$_,'Sequence has been dropped.');elseif($da=="")query_redirect("CREATE SEQUENCE ".idf_escape($B),$_,'Sequence has been created.');elseif($da!=$B)query_redirect("ALTER SEQUENCE ".idf_escape($da)." RENAME TO ".idf_escape($B),$_,'Sequence has been altered.');else
redirect($_);}page_header($da!=""?'Alter sequence'.": ".h($da):'Create sequence',$l);if(!$I)$I["name"]=$da;echo'
<form action="" method="post">
<p><input name="name" value="',h($I["name"]),'" autocapitalize="off">
<input type=\'submit\' value=\'Save\'>
';if($da!="")echo"<input type='submit' name='drop' value='".'Drop'."'".confirm(sprintf('Drop %s?',$da)).">\n";echo
input_token(),'</form>
';}elseif(isset($_GET["type"])){function
enum_values($pc){$X="'(?:[^']|'')*'";if(!preg_match('~^AS\s+ENUM\s*\(\s*('.$X.'(?:\s*,\s*'.$X.')*)\s*\)$~i',$pc,$A))return
null;preg_match_all('~'.$X.'~',$A[1],$og);return$og[0];}function
add_enum_values($T,$_h,$eh){$Eh=enum_values($_h);$kh=enum_values($eh);if($Eh===null||$kh===null)return
null;$H=array();$s=0;foreach($kh
as$X){if($X===idx($Eh,$s))$s++;else$H[]="ALTER TYPE ".idf_escape($T)." ADD VALUE $X".($s<count($Eh)?" BEFORE ".$Eh[$s]:"");}return($s==count($Eh)?$H:null);}$ea=$_GET["type"];$I=$_POST;$T=($ea!=""?type_definition(+array_search($ea,types(true))):array());$sh=($T["kind"]=='d'?"DOMAIN":"TYPE");if($_POST&&!$l){$_=substr(ME,0,-1);$B=trim($I["name"]);$Ca=trim(str_replace("\r","",$I["as"]));$ih=(preg_match('~^AS\s+(?!ENUM\b|RANGE\b|\()~i',$Ca)?"DOMAIN":"TYPE");$Fg='Type has been altered.';$b=(!$_POST["drop"]&&$ea!=""&&$ih==$sh?($Ca==$T["definition"]?array():add_enum_values($ea,$T["definition"],$Ca)):null);if($b!==null){if($ea!=$B)$b[]="ALTER $sh ".idf_escape($ea)." RENAME TO ".idf_escape($B);if(!$b)redirect($_);$xd=false;foreach($b
as$F){if(!queries($F)){$xd=true;break;}}queries_redirect($_,$Fg,!$xd);}else
drop_create("DROP $sh ".idf_escape($ea),"CREATE $ih ".idf_escape($B)." $Ca","","","",$_,'Type has been dropped.',$Fg,'Type has been created.',$ea,$B);}page_header($ea!=""?'Alter type'.": ".h($ea):'Create type',$l);if(!$I){$I["name"]=$ea;$I["as"]=($ea!=""?$T["definition"]:"AS ");}echo'
<form action="" method="post">
<p>
','Name'.": <input name='name' value='".h($I['name'])."' autocapitalize='off'>\n",doc_link(array('pgsql'=>"sql-createtype.html",),"?");textarea("as",$I["as"]);echo"<p><input type='submit' value='".'Save'."'>\n";if($ea!="")echo"<input type='submit' name='drop' value='".'Drop'."'".confirm(sprintf('Drop %s?',$ea)).">\n";echo
input_token(),'</form>
';}elseif(isset($_GET["check"])){$a=$_GET["check"];$B="$_GET[name]";$I=$_POST;if($I&&!$l){$ig=ME."table=".url_escape($a);$Ig='Check has been dropped.';$Gg='Check has been altered.';$Hg='Check has been created.';if(JUSH=="sqlite")queries_redirect($ig,($I["drop"]?$Ig:($B!=""?$Gg:$Hg)),recreate_table($a,$a,array(),array(),array(),"",array(),"$B",($I["drop"]?"":$I["clause"])));else{$b="ALTER TABLE ".table($a);$ib=" CHECK ($I[clause])";$rl="adminer_".uniqid();drop_create("$b DROP CONSTRAINT ".idf_escape($B),"$b ADD".($I["name"]!=""?" CONSTRAINT ".idf_escape($I["name"]):"").$ib,"$b DROP CONSTRAINT ".idf_escape($I["name"]),"$b ADD CONSTRAINT ".idf_escape($rl).$ib,"$b DROP CONSTRAINT ".idf_escape($rl),$ig,$Ig,$Gg,$Hg,$B,$I["name"]);}}page_header(($B!=""?'Alter check':'Create check'),$l,array("table"=>$a),h($B!=""?$B:$a));if(!$I){$mb=driver()->checkConstraints($a);$I=array("name"=>$B,"clause"=>$mb[$B]);}echo'
<form action="" method="post">
<p>';if(JUSH!="sqlite")echo'Name'.': <input name="name" value="'.h($I["name"]).'" data-maxlength="64" autocapitalize="off"> ';echo
doc_link(array('sql'=>"create-table-check-constraints.html",'mariadb'=>"constraint/",'pgsql'=>"ddl-constraints.html#DDL-CONSTRAINTS-CHECK-CONSTRAINTS",'mssql'=>"relational-databases/tables/create-check-constraints",'sqlite'=>"lang_createtable.html#check_constraints",),"?"),'<p>';textarea("clause",$I["clause"]);echo'<p><input type=\'submit\' value=\'Save\'>
';if($B!="")echo'<input type=\'submit\' name=\'drop\' value=\'Drop\'',confirm(sprintf('Drop %s?',$B)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["trigger"])){$a=$_GET["trigger"];$B="$_GET[name]";$Ql=trigger_options();$I=(array)trigger($B,$a)+array("Trigger"=>$a."_bi");if($_POST){if(!$l&&in_array($_POST["Timing"],$Ql["Timing"])&&in_array($_POST["Event"],$Ql["Event"])&&in_array($_POST["Type"],$Ql["Type"])){$Fh=" ON ".table($a);$Kc="DROP TRIGGER ".idf_escape($B).(JUSH=="pgsql"?$Fh:"");$ig=ME."table=".url_escape($a);if($_POST["drop"])query_redirect($Kc,$ig,'Trigger has been dropped.');else{if($B!="")queries($Kc);queries_redirect($ig,($B!=""?'Trigger has been altered.':'Trigger has been created.'),queries(create_trigger($Fh,$_POST)));if($B!="")queries(create_trigger($Fh,$I+array("Type"=>reset($Ql["Type"]))));}}$I=$_POST;}page_header(($B!=""?'Alter trigger':'Create trigger'),$l,array("table"=>$a),h($B!=""?$B:$a));$Ol=on('change','triggerChange',"^".preg_quote($a,"/")."_[ba][iud]$",$a);echo'
<form action="" method="post" id="form">
<table class="layout">
<tr><th>Time
<td>',html_select("Timing",$Ql["Timing"],$I["Timing"],$Ol),'<tr><th>Event<td>',html_select("Event",$Ql["Event"],$I["Event"],$Ol),(in_array("UPDATE OF",$Ql["Event"])?" <input name='Of' value='".h($I["Of"])."' class='hidden'>":""),'<tr><th>Type<td>',html_select("Type",$Ql["Type"],$I["Type"]),'<tr><th>Name<td><input name="Trigger" value="',h($I["Trigger"]),'" data-maxlength="64" autocapitalize="off">
</table>
',script("fire(qs('#form')['Timing'], 'change');"),'<p>';textarea("Statement",$I["Statement"]);echo'<p>
<input type=\'submit\' value=\'Save\'>
';if($B!="")echo'<input type=\'submit\' name=\'drop\' value=\'Drop\'',confirm(sprintf('Drop %s?',$B)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["user"])){function
grant($ke,array$bj,$e,$Fh){if(!$bj)return
true;if($bj==array("ALL PRIVILEGES","GRANT OPTION"))return($ke=="GRANT"?queries("$ke ALL PRIVILEGES$Fh WITH GRANT OPTION"):queries("$ke ALL PRIVILEGES$Fh")&&queries("$ke GRANT OPTION$Fh"));return
queries("$ke ".preg_replace('~(GRANT OPTION)\([^)]*\)~','\1',implode("$e, ",$bj).$e).$Fh);}$fa=$_GET["user"];$bj=array(""=>array("All privileges"=>""));foreach(get_rows("SHOW PRIVILEGES")as$I){foreach(explode(",",($I["Privilege"]=="Grant option"?"":$I["Context"]))as$Ob)$bj[$Ob=="File access on server"?"Server Admin":$Ob][$I["Privilege"]]=$I["Comment"];}unset($bj["Server Admin"]["Usage"]);foreach($bj["Tables"]as$x=>$W)unset($bj["Databases"][$x]);$fh=array();if($_POST){foreach($_POST["objects"]as$x=>$W)$fh[$W]=(array)$fh[$W]+idx($_POST["grants"],$x,array());}$le=array();if(isset($_GET["host"])&&($G=connection()->query("SHOW GRANTS FOR ".q($fa)."@".q($_GET["host"])))){while($I=$G->fetch_row()){if(preg_match('~GRANT (.*) ON (.*) TO ~',$I[0],$A)&&preg_match_all('~ *([^(,]*[^ ,(])( *\([^)]+\))?~',$A[1],$og,PREG_SET_ORDER)){foreach($og
as$W){if($W[1]!="USAGE")$le["$A[2]$W[2]"][$W[1]]=true;if(preg_match('~ WITH GRANT OPTION~',$I[0]))$le["$A[2]$W[2]"]["GRANT OPTION"]=true;}}}}if($_POST&&!$l){$Dh=(isset($_GET["host"])?q($fa)."@".q($_GET["host"]):"''");if($_POST["drop"])query_redirect("DROP USER $Dh",ME."privileges=",'User has been dropped.');else{$jh=q($_POST["user"])."@".q($_POST["host"]);$xi=$_POST["pass"];$Vb=false;$G=true;if($Dh!=$jh){$Vb=queries("CREATE USER $jh IDENTIFIED BY ".($_POST["hashed"]?"PASSWORD ":"").q($xi));$G=$Vb;}elseif($xi!="")$G=queries("SET PASSWORD FOR $jh = ".(min_version(8,99)||$_POST["hashed"]?q($xi):"PASSWORD(".q($xi).")"));if($G){$Bj=array();foreach($fh
as$sh=>$ke){if(isset($_GET["grant"]))$ke=array_filter($ke);$ke=array_keys($ke);if(isset($_GET["grant"]))$Bj=array_diff(array_keys(array_filter($fh[$sh],'strlen')),$ke);elseif($Dh==$jh){$Ah=array_keys((array)$le[$sh]);$Bj=array_diff($Ah,$ke);$ke=array_diff($ke,$Ah);unset($le[$sh]);}if(preg_match('~^(.+)\s*(\(.*\))?$~U',$sh,$A)&&(!grant("REVOKE",$Bj,$A[2]," ON $A[1] FROM $jh")||!grant("GRANT",$ke,$A[2]," ON $A[1] TO $jh"))){$G=false;break;}}}if($G&&isset($_GET["host"])){if($Dh!=$jh)queries("DROP USER $Dh");elseif(!isset($_GET["grant"])){foreach($le
as$sh=>$Bj){if(preg_match('~^(.+)(\(.*\))?$~U',$sh,$A))grant("REVOKE",array_keys($Bj),$A[2]," ON $A[1] FROM $jh");}}}if($G&&!Queries::$queries)redirect(ME."privileges=");queries_redirect(ME."privileges=",(isset($_GET["host"])?'User has been altered.':'User has been created.'),$G);if($Vb)connection()->query("DROP USER $jh");}}page_header((isset($_GET["host"])?'Username'.": ".h("$fa@$_GET[host]"):'Create user'),$l,array("privileges"=>array('','Privileges')));$I=$_POST;if($I)$le=$fh;else{$I=$_GET+array("host"=>get_val("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', -1)"));$le[(DB==""||$le?"":idf_escape(addcslashes(DB,"%_\\"))).".*"]=array();}echo'<form action="" method="post">
<table class="layout">
<tr><th>Server<td><input name="host" data-maxlength="60" value="',h($I["host"]),'" autocapitalize="off">
<tr><th>Username<td><input name="user" data-maxlength="80" value="',h($I["user"]),'" autocapitalize="off">
<tr><th>Password<td><input name="pass" id="pass" value="',h($I["pass"]),'" autocomplete="new-password">
',($I["hashed"]?"":script("typePassword(qs('#pass'));")),(min_version(8,99)?"":checkbox("hashed",1,$I["hashed"],'Hashed',on('click','hashedClick'))),'</table>

',"<table class='odds'>\n","<thead><tr><th colspan='2'>".'Privileges'.doc_link(array('sql'=>"grant.html#priv_level"));$s=0;foreach($le
as$sh=>$ke){echo'<th>'.($sh!="*.*"?"<input name='objects[$s]' value='".h($sh)."' size='10' autocapitalize='off'>":input_hidden("objects[$s]","*.*")."*.*");$s++;}echo"<tbody>\n";foreach(array(""=>"","Server Admin"=>'Server',"Databases"=>'Database',"Tables"=>'Table',"Procedures"=>'Routine',)as$Ob=>$sc){foreach((array)$bj[$Ob]as$aj=>$Ab){echo"<tr><td".($sc?">$sc<td":" colspan='2'").' lang="en" title="'.h($Ab).'">'.h($aj);$s=0;foreach($le
as$sh=>$ke){$B="'grants[$s][".h(strtoupper($aj))."]'";$X=$ke[strtoupper($aj)];if($Ob=="Server Admin"&&$sh!=(isset($le["*.*"])?"*.*":".*"))echo"<td>";elseif(isset($_GET["grant"]))echo"<td><select name=$B><option><option value='1'".($X?" selected":"").">".'Grant'."<option value='0'".($X=="0"?" selected":"").">".'Revoke'."</select>";else
echo"<td align='center'><label class='block'>","<input type='checkbox' name=$B value='1'".($X?" checked":"").($aj=="All privileges"?" id='grants-$s-all'":($aj=="Grant option"?"":on('click','grantsClick',"grants-$s-all"))).">","</label>";$s++;}}}echo"</table>\n",'<p>
<input type=\'submit\' value=\'Save\'>
';if(isset($_GET["host"]))echo'<input type=\'submit\' name=\'drop\' value=\'Drop\'',confirm(sprintf('Drop %s?',"$fa@$_GET[host]")),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["processlist"])){if(support("kill")){if($_POST&&!$l){$Kf=0;foreach((array)$_POST["kill"]as$W){if(adminer()->killProcess($W))$Kf++;}queries_redirect(ME."processlist=",lang_format(array('%d process has been killed.','%d processes have been killed.'),$Kf),$Kf||!$_POST["kill"]);}}page_header('Process list',$l);echo'
<form action="" method="post">
<div class="scrollable">
<table class="nowrap checkable odds"',on('click','tableClick').on('dblclick','tableClick'),'>
';$s=-1;foreach(adminer()->processList()as$s=>$I){if(!$s){echo"<thead><tr lang='en'>".(support("kill")?"<td class='hover'>":"");foreach($I
as$x=>$W)echo"<th>$x".doc_link(array('sql'=>"show-processlist.html#processlist_".strtolower($x),'pgsql'=>"monitoring-stats.html#PG-STAT-ACTIVITY-VIEW",'oracle'=>"refrn/V-SESSION.html",));echo"<tbody>\n";}echo"<tr>".(support("kill")?"<td class='hover'>".checkbox("kill[]",$I[JUSH=="sql"?"Id":"pid"],0):"");foreach($I
as$x=>$W)echo"<td>".($W!=""&&((JUSH=="sql"&&$x=="Info"&&preg_match("~Query|Killed~",$I["Command"]))||(JUSH=="pgsql"&&$x=="query")||(JUSH=="oracle"&&$x=="sql_text"))?"<code class='jush-".JUSH."' data-full='".h($W)."'>".shorten_utf8($W,100,"</code>").' <a href="'.h(($I["db"]!=""?preg_replace('~&db=[^&]*~','',ME)."db=".url_escape($I["db"])."&":ME)."sql=".url_escape($W)).'">'.'Clone'.'</a>'.' '.copy_icon():h($W));echo"\n";}echo'</table>
</div>
<p>
',script("copyCode(qsl('table'));");if(support("kill"))echo
format_number($s+1)."/".sprintf('%d in total',max_connections()),"<p><input type='submit' value='".'Kill'."'>\n";echo
input_token(),'</form>
',script("tableCheck();");}elseif($_GET["select"]!=""){$a=$_GET["select"];$R=table_status1($a);$w=indexes($a);$n=fields($a);$Ud=column_foreign_keys($a);$zh=$R["Oid"];$Dj=array();$e=array();$Rj=array();$Sh=array();$ul=null;foreach($n
as$x=>$m){$B=adminer()->fieldName($m);$ah=html_entity_decode(strip_tags($B),ENT_QUOTES);if(isset($m["privileges"]["select"])&&$B!=""){$e[$x]=$ah;if(is_shortable($m))$ul=adminer()->selectLengthProcess();}if(isset($m["privileges"]["where"])&&$B!="")$Rj[$x]=$ah;if(isset($m["privileges"]["order"])&&$B!="")$Sh[$x]=$ah;$Dj+=$m["privileges"];}list($L,$r)=adminer()->selectColumnsProcess($e,$w);$L=array_unique($L);$r=array_unique($r);$yf=count($r)<count($L);$Z=adminer()->selectSearchProcess($n,$w,$R);$Rh=adminer()->selectOrderProcess($n,$w);$z=adminer()->selectLimitProcess();if($_GET["val"]&&is_ajax()){header("Content-Type: text/plain; charset=utf-8");foreach($_GET["val"]as$cm=>$I){$Ca=convert_field($n[key($I)]);$L=array($Ca?:idf_escape(key($I)));$Z[]=where_check(bracket_escape($cm,true),$n);$H=driver()->select($a,$L,$Z,$L);if($H)echo
first($H->fetch_row());}exit;}$Wi=$fm=array();foreach($w
as$v){if($v["type"]=="PRIMARY"){$Wi=array_flip($v["columns"]);$fm=($L?$Wi:array());foreach($fm
as$x=>$W){if(in_array(idf_escape($x),$L))unset($fm[$x]);}break;}}if($zh&&!$Wi){$Wi=$fm=array($zh=>0);$w[]=array("type"=>"PRIMARY","columns"=>array($zh));}if($_POST&&!$l){$Lm=$Z;if(!$_POST["all"]&&is_array($_POST["check"])){$mb=array();foreach($_POST["check"]as$ib)$mb[]=where_check($ib,$n);$Lm[]="((".implode(") OR (",$mb)."))";}$Nm=$Lm;$Lm=($Lm?"\nWHERE ".implode(" AND ",$Lm):"");if($_POST["export"]){save_settings(array("output"=>$_POST["output"],"format"=>$_POST["format"]),"adminer_import");dump_headers($a);adminer()->dumpTable($a,"");$Uj=($L?:array("*"));$Qb=convert_fields($e,$n,$L);if($Qb)$Uj[]=substr($Qb,2);$F="";if(is_array($_POST["check"])&&!$Wi){$be=implode(", ",$Uj)."\nFROM ".table($a);$oe=($r&&$yf?"\nGROUP BY ".implode(", ",$r):"").($Rh?"\nORDER BY ".implode(", ",$Rh):"");$am=array();foreach($_POST["check"]as$W)$am[]="(SELECT".limit($be,"\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($W,$n).$oe,1).")";$F=implode(" UNION ALL ",$am);}adminer()->dumpData($a,"table",$F,$Uj,$Nm,($yf?$r:array()),$Rh);adminer()->dumpFooter();exit;}if(!adminer()->selectEmailProcess($Z,$Ud)){if($_POST["save"]||$_POST["delete"]){$G=true;$ra=0;$Ra=false;$N=array();if(!$_POST["delete"]){foreach($n
as$B=>$W){$u=bracket_escape($B);if(isset($_POST["fields"][$u])||$_FILES["fields-$u"]){$W=process_input($n[$B]);if($W!==null&&($_POST["clone"]||$W!==false))$N[idf_escape($B)]=($W!==false?$W:idf_escape($B));}}}if($_POST["delete"]||$N){$F=($_POST["clone"]?"INTO ".table($a)." (".implode(", ",array_keys($N)).")\nSELECT ".implode(", ",$N)."\nFROM ".table($a):"");if($_POST["all"]||($Wi&&is_array($_POST["check"]))||$yf){$G=($_POST["delete"]?driver()->delete($a,$Lm):($_POST["clone"]?queries("INSERT $F$Lm".driver()->insertReturning($a)):driver()->update($a,$N,$Lm)));$ra=connection()->affected_rows;if(is_object($G))$ra+=$G->num_rows;}else{$Ra=count((array)$_POST["check"])>1&&driver()->begin();foreach((array)$_POST["check"]as$W){$Km="\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($W,$n);$G=($_POST["delete"]?driver()->delete($a,$Km,1):($_POST["clone"]?queries("INSERT".limit1($a,$F,$Km)):driver()->update($a,$N,$Km,1)));if(!$G)break;$ra+=connection()->affected_rows;}if($Ra&&$G&&!driver()->commit())$G=false;}}$Fg=lang_format(array('%d item has been affected.','%d items have been affected.'),$ra);if($_POST["clone"]&&$G&&$ra==1){$Tf=last_id($G);if($Tf)$Fg=sprintf('Item%s has been inserted.'," $Tf");}queries_redirect(remove_from_uri($_POST["all"]&&$_POST["delete"]?"page|next":""),$Fg,$G);if($Ra)driver()->rollback();if(!$_POST["delete"]){$Ni=(array)$_POST["fields"];edit_form($a,array_intersect_key($n,$Ni),$Ni,!$_POST["clone"],$l);page_footer();exit;}}elseif(!$_POST["import"]){$G=true;$ra=0;$Ra=count((array)$_POST["val"])>1&&driver()->begin();foreach((array)$_POST["val"]as$cm=>$I){$N=array();foreach($I
as$x=>$W){$x=bracket_escape($x,true);$N[idf_escape($x)]=(preg_match('~char|text~',$n[$x]["type"])||$W!=""?adminer()->processInput($n[$x],$W):"NULL");}$G=driver()->update($a,$N," WHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check(bracket_escape($cm,true),$n),($yf||$Wi?0:1)," ");if(!$G)break;$ra+=connection()->affected_rows;}if($Ra)$G=$G&&driver()->commit();queries_redirect(remove_from_uri(),lang_format(array('%d item has been affected.','%d items have been affected.'),$ra),$G);if($Ra)driver()->rollback();}else{save_settings(array("format"=>$_POST["separator"]),"adminer_import");$Ed=get_file("csv_file",true);if(!is_string($Ed))$l=upload_error($Ed);elseif(!preg_match('~~u',$Ed))$l='File must be in UTF-8 encoding.';else{$xb=array_keys($n);$ak=($_POST["separator"]=="csv"?",":($_POST["separator"]=="tsv"?"\t":";"));$Zb=parse_csv($Ed,$ak);$ra=count($Zb);driver()->begin();$J=array();foreach($Zb
as$x=>$Y){if(!$x&&!array_diff($Y,$xb)){$xb=$Y;$ra--;}else{$N=array();foreach($Y
as$s=>$tb)$N[idf_escape($xb[$s])]=($tb==""&&$n[$xb[$s]]["null"]?"NULL":q(csv_value($tb)));$J[]=$N;}}$G=(!$J||driver()->insertUpdate($a,$J,$Wi));if($G)driver()->commit();queries_redirect(remove_from_uri("page|next"),lang_format(array('%d row has been imported.','%d rows have been imported.'),$ra),$G);driver()->rollback();}}}}$bl=adminer()->tableName($R);if(is_ajax()){page_headers();ob_start();}else
page_header('Select'.": $bl",$l);$N=null;if(isset($Dj["insert"])||!support("table")){$N="";foreach((array)$_GET["where"]as$W){$X=$W["val"];if(is_array($X))$X=(count($X)==1&&preg_match('~^val-(.*)~s',reset($X),$A)?$A[1]:"");if($W["col"]!=""&&$X!=""&&($W["op"]=="="||(!$W["op"]&&(is_array($W["val"])||!preg_match('~[_%]~',$X)))))$N
.="&set[".url_escape(bracket_escape($W["col"]))."]=".url_escape($X);}}adminer()->selectLinks($R,$N);if(!$e&&support("table"))echo"<p class='error'>".'Unable to select the table'.($n?".":": ".adminer()->error())."\n";else{echo"<form action='' id='form'>\n","<div hidden>";hidden_fields_get();echo(DB!=""?input_hidden("db",DB).(isset($_GET["ns"])?input_hidden("ns",$_GET["ns"]):""):""),input_hidden("select",$a),"</div>\n";adminer()->selectColumnsPrint($L,$e);adminer()->selectSearchPrint($Z,$Rj,$w,$R);adminer()->selectOrderPrint($Rh,$Sh,$w);adminer()->selectLimitPrint($z);if($ul!==null)adminer()->selectLengthPrint($ul);adminer()->selectActionPrint($w);echo"</form>\n";foreach((array)$_GET["where"]as$W){if($W["op"]=="SQL"&&!in_array($_SERVER["HTTP_SEC_FETCH_SITE"],array("","same-origin"))){echo"<p class='error'>".'Invalid CSRF token. Submit the form again.'.' '.'If you did not send this request from Adminer, close this page.'."\n";page_footer();exit;}}$D=$_GET["page"];$Xd=null;if($D=="last"){$Xd=get_val(count_rows($a,$Z,$yf,$r));$D=floor(max(0,intval($Xd)-1)/$z);}$Tj=$L;$ne=$r;if(!$Tj){$Tj[]="*";$Qb=convert_fields($e,$n,$L);if($Qb)$Tj[]=substr($Qb,2);}foreach($L
as$x=>$W){$m=$n[idf_unescape($W)];if($m&&($Ca=convert_field($m)))$Tj[$x]="$Ca AS $W";}if(JUSH=="pgsql"||JUSH=="mssql"){foreach((array)$_GET["columns"]as$x=>$W){if(isset($Tj[$x])&&$W["fun"])$Tj[$x].=" AS ".idf_escape(apply_sql_function($W["fun"],($W["col"]!=""?$W["col"]:"*")));}}if(!$yf&&$fm){foreach($fm
as$x=>$W){$Tj[]=idf_escape($x);if($ne)$ne[]=idf_escape($x);}}$G=driver()->select($a,$Tj,$Z,$ne,$Rh,$z,$D,true);if(!is_object($G))echo"<p class='error'>".(adminer()->error()?:'Unknown error.')."\n";else{if(JUSH=="mssql"&&$D)$G->seek($z*$D);$Wc=array();$J=array();while($I=$G->fetch_assoc()){if($D&&JUSH=="oracle")unset($I["RNUM"]);$J[]=$I;}$ze=($z&&(support("cursor")?$_GET["next"]!="":count($J)>=$z));if(is_ajax()&&$ze)header("X-Next-Page: ".pagination_href($D+1));if($_GET["modify"]&&$J){$xg=max_input_vars(count($J[0])+1,20);echo($xg&&count($J)>$xg?"<p class='error'>".max_input_vars_error()."\n":"");}echo"<form action='' method='post' enctype='multipart/form-data'".on_upload_progress($km).">\n";if($_GET["page"]!="last"&&$z&&$r&&$yf&&JUSH=="sql")$Xd=get_val(" SELECT FOUND_ROWS()");if(!$J)echo"<p class='message'>".'No rows.'."\n";else{$Na=adminer()->backwardKeys($a,$bl);echo"<div class='scrollable'>","<table id='table' class='nowrap checkable odds'".on('click','tableClick').on('dblclick','tableClick').on('keydown','editingKeydown').">\n","<thead><tr>".(!$r&&$L?"":"<td class='hover check'><input type='checkbox' id='all-page' class='jsonly' title='".'All rows on this page'."'".on('click','formCheck','^check').">");$bh=array();$he=array();reset($L);$lj=1;foreach($J[0]as$x=>$W){if(!isset($fm[$x])){$W=idx($_GET["columns"],key($L))?:array();$m=$n[$L?($W?$W["col"]:current($L)):$x];$B=($m?adminer()->fieldName($m,$lj):($W["fun"]?"*":h($x)));if($B!=""){$lj++;$bh[$x]=$B;$d=idf_escape($x);$Me=remove_from_uri('(order|desc)[^=]*|page|next').'&order[0]='.url_escape($x);$sc="&desc[0]=1";$xk=preg_replace('~ DESC( NULLS LAST)?$~','',$Rh[0]);$zk=($xk==$d||$xk==$x);echo"<th id='th[".h(bracket_escape($x))."]'".($zk?" aria-sort='".($xk==$Rh[0]?"ascending":"descending")."'":"").">";$ge=apply_sql_function($W["fun"],$B);$yk=isset($m["privileges"]["order"])||$ge!=$B;echo($yk?"<a href='".h($Me.($zk&&$xk==$Rh[0]?$sc:''))."'>$ge</a>":$ge);$Eg=($yk?"<a href='".h($Me.$sc)."' title='".'descending'."' class='text'> ↓</a>":'');if(!$W["fun"]&&isset($m["privileges"]["where"]))$Eg
.="<a href='#fieldset-search' title='".'Search'."' class='text jsonly'".on('click','selectSearch',$x)."> =</a>";echo($Eg?"<span class='column'>$Eg</span>":"");}$he[$x]=$W["fun"];next($L);}}$ag=array();if($_GET["modify"]){foreach($J
as$I){foreach($I
as$x=>$W)$ag[$x]=max($ag[$x],min(40,strlen(utf8_decode($W))));}}echo($Na?"<th>".'Relations':"")."<tbody>\n";if(is_ajax())ob_end_clean();foreach(adminer()->rowDescriptions($J,$Ud)as$Yg=>$I){$bm=unique_array($J[$Yg],$w);if(!$bm){$bm=array();reset($L);foreach($J[$Yg]as$x=>$W){if(!preg_match('~^(COUNT|AVG|GROUP_CONCAT|MAX|MIN|SUM)\(~',current($L)))$bm[$x]=$W;next($L);}}$cm="";foreach($bm
as$x=>$W){$m=(array)$n[$x];$xf=is_blob($m);if((JUSH=="sql"||JUSH=="pgsql")&&($xf||preg_match('~'.text_type().'~',$m["type"]))&&strlen($W)>64){$x=(strpos($x,'(')?$x:idf_escape($x));$x="MD5(".($xf||JUSH!='sql'||preg_match("~^utf8~",$m["collation"])?$x:"CONVERT($x USING ".charset(connection()).")").")";$W=md5($xf?(string)driver()->value($W,$m):$W);}$cm
.="&".($W!==null?"where[".url_escape(bracket_escape($x))."]=".url_escape($W===false?"f":$W):"null[]=".url_escape($x));}echo"<tr>".(!$r&&$L?"":"<td class='hover check'>".($yf||information_schema(DB)?"":"<a href='".h(ME."edit=".url_escape($a).$cm)."' class='edit'>".'edit'."</a> ").checkbox("check[]",substr($cm,1),in_array(substr($cm,1),(array)$_POST["check"])));reset($L);foreach($I
as$x=>$W){if(isset($bh[$x])){$d=current($L);$m=(array)$n[$x];if($W!=""&&(!isset($Wc[$x])||$Wc[$x]!=""))$Wc[$x]=(is_mail($W)?$bh[$x]:"");$_="";if(is_blob($m)&&$W!="")$_=ME.'download='.url_escape($a).'&field='.url_escape($x).$cm;if(!$_&&$W!==null){foreach((array)$Ud[$x]as$p){if(count($Ud[$x])==1||end($p["source"])==$x){$_="";foreach($p["source"]as$s=>$_k)$_
.=where_link($s,$p["target"][$s],$J[$Yg][$_k]);$_=($p["db"]!=""?preg_replace('~([?&]db=)[^&]+~','\1'.url_escape($p["db"]),ME):ME).'select='.url_escape($p["table"]).$_;if($p["ns"])$_=preg_replace('~([?&]ns=)[^&]+~','\1'.url_escape($p["ns"]),$_);if(count($p["source"])==1)break;}}}if($d=="COUNT(*)"){$_=ME."select=".url_escape($a);$s=0;foreach((array)$_GET["where"]as$V){if(!array_key_exists($V["col"],$bm))$_
.=where_link($s++,$V["col"],$V["val"],$V["op"]);}foreach($bm
as$Gf=>$V)$_
.=where_link($s++,$Gf,$V);}$Ne=select_value($W,$_,$m,$ul);$u=bracket_escape($cm);$t=h("val[$u][".bracket_escape($x)."]");$Pi=idx(idx($_POST["val"],$u),bracket_escape($x));$im=idx($m["privileges"],"update");$Sc=!is_array($I[$x])&&!is_blob($m)&&is_utf8($W)&&$J[$Yg][$x]==$W&&!$he[$x]&&!$m["generated"]&&$im;$T=(preg_match('~^(AVG|MIN|MAX)\((.+)\)~',$d,$A)?$n[idf_unescape($A[2])]["type"]:$m["type"]);$tl=preg_match('~text|json|lob~',$T);$zf=preg_match(number_type(),$T)||preg_match('~^(CHAR_LENGTH|ROUND|FLOOR|CEIL|TIME_TO_SEC|COUNT|SUM)\(~',$d);echo"<td id='$t'".($zf&&($W===null||is_numeric(strip_tags($Ne))||$T=="money")?" class='number'":"");if(($_GET["modify"]&&$Sc&&$W!==null)||$Pi!==null){$ue=h($Pi!==null?$Pi:$W);echo">".($tl?"<textarea name='$t' cols='30' rows='".(substr_count($W,"\n")+1)."'>$ue</textarea>":"<input name='$t' value='$ue' size='$ag[$x]'>");}else{$kg=strpos($Ne,"<i>…</i>");echo($im?" data-text='".($kg?2:($tl?1:0))."'".($Sc?"":" data-warning='".'Use the edit link to modify this value.'."'"):"").">$Ne";}}next($L);}if($Na)echo"<td>";adminer()->backwardKeysPrint($Na,$J[$Yg]);echo"</tr>\n";}if(is_ajax())exit;echo"</table>\n","</div>\n";}if(!is_ajax()){$qa=get_settings("adminer_import");if($J||$D||$ze){$ld=true;if($_GET["page"]!="last"){if(!$z||(count($J)<$z&&($J||!$D)))$Xd=($D?$D*$z:0)+count($J);elseif(JUSH!="sql"||!$yf){$Xd=($yf?false:found_rows($R,$Z));if(intval($Xd)<max(1e4,2*($D+1)*$z))$Xd=first(slow_query(count_rows($a,$Z,$yf,$r)));elseif(JUSH=='sql'||JUSH=='pgsql')$ld=false;}}if(!support("cursor"))$ze=(($Xd===false?count($J)+1:$Xd-$D*$z)>$z);$ji=($z&&($ze||$D));if($ji)echo($ze?'<p><a href="'.h(pagination_href($D+1)).'" class="loadmore"'.on('click','selectLoadMore','Loading…').'>'.'Load more data'.'</a>':''),"\n";echo"<div class='footer'><div>\n";if($ji){$vg=($Xd===false?$D+($J?(count($J)>=$z?2:1):0):floor(($Xd-1)/$z));echo"<fieldset><legend>".'Page'."</legend>";if(!support("cursor")){echo
pagination(0,$D).($D>5?" …":"");for($s=max(1,$D-4);$s<min($vg,$D+5);$s++)echo
pagination($s,$D);if($vg>0)echo($D+5<$vg?" …":""),($ld&&$Xd!==false?pagination($vg,$D):" <a href='".h(remove_from_uri("page")."&page=last")."' title='~$vg'>".'last'."</a>");}else
echo
pagination(0,$D).($D>1?" …":""),($D?pagination($D,$D):""),($ze?pagination($D+1,$D)." …":"");echo"</fieldset>\n";}echo"<fieldset>","<legend>".'Whole result'."</legend>";$_c=($ld?"":"~ ").$Xd;$Nf=($Xd!==false?($ld?"":"~ ").lang_format(array('%d row','%d rows'),$Xd):"");echo
checkbox("all",1,0,$Nf,on('click','countRows',$_c))."\n","</fieldset>\n";if(adminer()->selectCommandPrint())echo'<fieldset',($_GET["modify"]?'':" title='".'Ctrl+click on a value to modify it.'."'"),'>
<legend><a href=\'',h($_GET["modify"]?remove_from_uri("modify"):relative_uri()."&modify=1"),'\'>Modify</a></legend><div>
<input type=\'submit\' id=\'save\' value=\'Save\'',($_GET["modify"]?'':" class='jsonly' disabled"),'>
</div></fieldset>

<fieldset><legend>Selected <span id="selected"></span></legend><div>
<input type=\'submit\' name=\'edit\' value=\'Edit\'>
<input type=\'submit\' name=\'clone\' value=\'Clone\'>
<input type=\'submit\' name=\'delete\' value=\'Delete\'',confirm(),'>
</div></fieldset>
';$Vd=adminer()->dumpFormat();foreach((array)$_GET["columns"]as$d){if($d["fun"]){unset($Vd['sql']);break;}}if($Vd){print_fieldset("export",'Export'." <span id='selected2'></span>");$fi=adminer()->dumpOutput();echo($fi?html_select("output",$fi,$qa["output"])." ":""),html_select("format",$Vd,$qa["format"])," <input type='submit' name='export' value='".'Export'."'>\n","</div></fieldset>\n";}adminer()->selectEmailPrint(array_filter($Wc,'strlen'),$e);echo"</div></div>\n";}if(adminer()->selectImportPrint())echo"<p>","<a href='#import' class='toggle'>".'Import'."</a>","<span id='import'".($_POST["import"]?"":" class='hidden'").">: ",($km?input_hidden(ini_get("session.upload_progress.name"),$km):""),file_input(" name='csv_file'"," ".html_select("separator",array("csv"=>"CSV,","csv;"=>"CSV;","tsv"=>"TSV"),$qa["format"])." <input type='submit' name='import' value='".'Import'."'>".($km?" <progress class='jsonly hidden' max='1' value='0'></progress>":"")),"</span>";echo
input_token(),"</form>\n",(!$r&&$L?"":script("tableCheck();"));}}}if(is_ajax()){ob_end_clean();exit;}}elseif(isset($_GET["variables"])){$O=isset($_GET["status"]);page_header($O?'Status':'Variables');$Am=($O?adminer()->showStatus():adminer()->showVariables());if(!$Am)echo"<p class='message'>".'No rows.'."\n";else{echo"<table>\n";foreach($Am
as$I){echo"<tr>";$x=array_shift($I);echo"<th><code class='jush-".JUSH.($O?"status":"set")."'>".h($x)."</code>";foreach($I
as$W)echo"<td>".nl_br(h($W));}echo"</table>\n";}}elseif(isset($_GET["script"])){header("Content-Type: application/json; charset=utf-8");if($_GET["script"]=="db"){$Tk=array("Data_length"=>0,"Index_length"=>0,"Data_free"=>0);foreach(table_status()as$B=>$R){json_row("Comment-$B",h($R["Comment"]).($R["Error"]?" <span class='error'>".h($R["Error"])."</span>":""));if(!is_view($R)||preg_match('~materialized~i',$R["Engine"])){foreach(array("Engine","Collation")as$x)json_row("$x-$B",h($R[$x]));foreach(array_keys($Tk+array("Auto_increment"=>0,"Rows"=>0))as$x){if(array_key_exists($x,$R))json_row("$x-$B",format_status($R,$x));if($R[$x]!=""&&isset($Tk[$x]))$Tk[$x]+=($R["Engine"]!="InnoDB"||$x!="Data_free"?$R[$x]:0);}}}if(function_exists('Adminer\db_status'))$Tk=db_status();foreach($Tk
as$x=>$W)json_row("sum-$x",format_number($W));json_row("");}elseif($_GET["script"]=="kill"){if(!$l)connection()->query("KILL ".number($_POST["kill"]));}else{foreach(count_tables(adminer()->databases(false))as$j=>$W){json_row("tables-$j",format_number($W));json_row("size-$j",db_size($j));}json_row("");}exit;}else{if(!isset($_GET["select"])&&support("single_table")){$S=tables_list();if($S)redirect(ME.(support("table")?"table=":"select=").url_escape(key($S)));}$Bg=ME.(isset($_GET["select"])?"select=&":"");$ll=array_merge((array)$_POST["tables"],(array)$_POST["views"]);if($ll&&!$l&&!$_POST["search"]){$G=true;$Fg="";if(JUSH=="sql"&&$_POST["tables"]&&count($_POST["tables"])>1&&($_POST["drop"]||$_POST["truncate"]||$_POST["copy"]))queries("SET foreign_key_checks = 0");if($_POST["truncate"]){if($_POST["tables"])$G=truncate_tables($_POST["tables"]);$Fg='Tables have been truncated.';}elseif($_POST["move"]){$G=move_tables((array)$_POST["tables"],(array)$_POST["views"],$_POST["target"]);$Fg='Tables have been moved.';}elseif($_POST["copy"]){$G=copy_tables((array)$_POST["tables"],(array)$_POST["views"],$_POST["target"]);$Fg='Tables have been copied.';}elseif($_POST["drop"]){if($_POST["views"])$G=drop_views($_POST["views"]);if($G&&$_POST["tables"])$G=drop_tables($_POST["tables"]);$Fg='Tables have been dropped.';}elseif(JUSH=="sqlite"&&$_POST["check"]){foreach((array)$_POST["tables"]as$Q){foreach(get_rows("PRAGMA integrity_check(".q($Q).")")as$I)$Fg
.="<b>".h($Q)."</b>: ".h($I["integrity_check"])."<br>";}}elseif(JUSH=="mssql"&&$_POST["check"]){foreach((array)$_POST["tables"]as$Q){foreach(get_rows("DBCC CHECKTABLE (".q(table($Q)).") WITH TABLERESULTS")as$I)$Fg
.="<b>".h($Q)."</b>: ".h($I["MessageText"])."<br>";}}elseif(JUSH!="sql"){$G=(JUSH=="sqlite"?queries("VACUUM"):apply_queries("VACUUM".($_POST["optimize"]?" ANALYZE":""),(array)$_POST["tables"]));$Fg='Tables have been optimized.';}elseif(!$_POST["tables"])$Fg='No tables.';elseif($G=queries(($_POST["optimize"]?"OPTIMIZE":($_POST["check"]?"CHECK":($_POST["repair"]?"REPAIR":"ANALYZE")))." TABLE ".implode(", ",array_map('Adminer\idf_escape',$_POST["tables"])))){while($I=$G->fetch_assoc())$Fg
.="<b>".h($I["Table"])."</b>: ".h($I["Msg_text"])."<br>";}queries_redirect(relative_uri(),$Fg,$G);}page_header(($_GET["ns"]==""?'Database'.": ".h(DB):'Schema'.": ".h($_GET["ns"])),$l,true);if(adminer()->homepage()){if($_GET["ns"]!==""){$Rh=$_GET["order"];$de=($Rh||support("fast_status"));echo"<div>\n","<h3 id='tables-views'>".'Tables and views'."</h3>\n";$kl=($de?table_status():tables_list());if(!$kl)echo"<p class='message'>".'No tables.'."\n";else{echo"<form action='' method='post'>\n";if(support("table")){echo"<fieldset><legend>".'Search data in tables'." <span id='selected2'></span></legend><div>",html_select("op",adminer()->operators(),idx($_POST,"op",JUSH=="elastic"?"should":"LIKE %%"))," <input type='search' name='query' value='".h($_POST["query"])."'".on('keydown','submitKeydown','search').">"," <input type='submit' name='search' value='".'Search'."'>\n","</div></fieldset>\n";if(!$l&&$_POST["search"]&&$_POST["query"]!=""){$_GET["where"][0]["op"]=$_POST["op"];search_tables();}}echo"<div class='scrollable'>\n","<table class='nowrap checkable odds'".on('click','tableClick').on('dblclick','tableClick').">\n",'<thead><tr class="wrap">','<td class="hover"><input id="check-all" type="checkbox" class="jsonly" title="'.'All'.'"'.on('click','formCheck','^(tables|views)\[').'>','<th'.(!$Rh&&JUSH!='sqlite'?" aria-sort='ascending'":'').'><a href="'.h(substr($Bg,0,-1)).'">'.'Table'.'</a>';$e=array("Engine"=>array('Engine'.doc_link(array('sql'=>'storage-engines.html'))));if(collations())$e["Collation"]=array('Collation'.doc_link(array('sql'=>'charset-charsets.html','mariadb'=>'supported-character-sets-and-collations/')));if(function_exists('Adminer\alter_table'))$e["Data_length"]=array('Data Length'.doc_link(array('sql'=>'show-table-status.html','pgsql'=>'functions-admin.html#FUNCTIONS-ADMIN-DBOBJECT','oracle'=>'refrn/ALL_TABLES.html')),"create",'Alter table',);if(support("indexes"))$e["Index_length"]=array('Index Length'.doc_link(array('sql'=>'show-table-status.html','pgsql'=>'functions-admin.html#FUNCTIONS-ADMIN-DBOBJECT')),"indexes",'Alter indexes',);$e["Data_free"]=array('Data Free'.doc_link(array('sql'=>'show-table-status.html')),"edit",'New item');if(function_exists('Adminer\alter_table'))$e["Auto_increment"]=array('Auto Increment'.doc_link(array('sql'=>'example-auto-increment.html','mariadb'=>'auto_increment/')),"auto_increment=1&create",'Alter table',);$e["Rows"]=array('Rows'.doc_link(array('sql'=>'show-table-status.html','pgsql'=>'catalog-pg-class.html#CATALOG-PG-CLASS','oracle'=>'refrn/ALL_TABLES.html')),"select",'Select data',);if(support("comment"))$e["Comment"]=array('Comment'.doc_link(array('sql'=>'show-table-status.html','pgsql'=>'functions-info.html#FUNCTIONS-INFO-COMMENT-TABLE')));$Da=array('Engine','Collation','Comment');foreach($e
as$x=>$d)echo"<th".($Rh==$x?" aria-sort='".(in_array($x,$Da)?"ascending":"descending")."'":"")."><a href='".h($Bg)."order=$x'>$d[0]</a>";echo"<tbody>\n";if($Rh){uasort($kl,function($ia,$Ka)use($Rh,$Da){$H=($ia[$Rh]<$Ka[$Rh]?-1:($ia[$Rh]>$Ka[$Rh]?1:0));return(in_array($Rh,$Da)?$H:-$H);});}$S=0;$Tk=array("Data_length"=>0,"Index_length"=>0,"Data_free"=>0);foreach($kl
as$B=>$O){$Dm=($de?is_view($O):$O!==null&&!preg_match('~table|sequence~i',$O));$O=($de?$O:array('Engine'=>$O));$t=h("Table-".$B);echo'<tr><td class="hover">'.checkbox(($Dm?"views[]":"tables[]"),$B,in_array("$B",$ll,true),"","","",$t),'<th>'.(support("table")||support("indexes")?"<a href='".h(ME)."table=".url_escape($B)."' title='".'Show structure'."' id='$t'>".h($B).'</a>':h($B));if($Dm&&!preg_match('~materialized~i',$O['Engine'])){$zl='View';echo'<td colspan="'.(count($e)-(support("comment")?2:1)).'">'.(support("view")?"<a href='".h(ME)."view=".url_escape($B)."' title='".'Alter view'."'>$zl</a>":$zl),"<td align='right'><a href='".h(ME)."select=".url_escape($B)."' title='".'Select data'."'>?</a>";if(support("comment"))echo'<td>'.h($O['Comment']);}else{if($de){foreach(array_keys($Tk)as$x)$Tk[$x]+=($O["Engine"]!="InnoDB"||$x!="Data_free"?idx($O,$x):0);}foreach($e
as$x=>$d){$t=" id='$x-".h($B)."'";echo($d[1]?"<td align='right'><a href='".h(ME."$d[1]=").url_escape($B)."'$t title='$d[2]'>".format_status($O,$x)."</a>":"<td$t>".h(idx($O,$x,'?')).($x=="Comment"&&$O["Error"]?" <span class='error'>".h($O["Error"])."</span>":""));}$S++;}echo"\n";}echo"<tr><td class='hover'><th>".sprintf('%d in total',count($kl)),"<td>".h(JUSH=="sql"?get_val("SELECT @@default_storage_engine"):""),(collations()?"<td>".h(db_collation(DB,collations())):'');if($de&&function_exists('Adminer\db_status'))$Tk=db_status();foreach($Tk
as$x=>$Sk)echo($e[$x]?"<td align='right' id='sum-$x'>".($de?format_number($Sk):""):"");echo"\n","</table>\n",($de?'':script("ajaxSetHtml('".js_escape(ME)."script=db');")),"</div>\n";if(!information_schema(DB)){$xm="<input type='submit' value='".'Vacuum'."'".on_help("VACUUM")."> ";$Nh="<input type='submit' name='optimize' value='".'Optimize'."'".on_help(JUSH=="sql"?"OPTIMIZE TABLE":"VACUUM ANALYZE")."> ";$Yi=(JUSH=="sqlite"?$xm."<input type='submit' name='check' value='".'Check'."'".on_help("PRAGMA integrity_check")."> ":(JUSH=="pgsql"?$xm.$Nh:(JUSH=="mssql"?"<input type='submit' name='check' value='".'Check'."'".on_help("DBCC CHECKTABLE")."> ":(JUSH=="sql"?"<input type='submit' value='".'Analyze'."'".on_help("ANALYZE TABLE")."> ".$Nh."<input type='submit' name='check' value='".'Check'."'".on_help("CHECK TABLE")."> "."<input type='submit' name='repair' value='".'Repair'."'".on_help("REPAIR TABLE")."> ":"")))).(function_exists('Adminer\truncate_tables')?"<input type='submit' name='truncate' value='".'Truncate'."'".confirm().on_help(JUSH=="sqlite"?"DELETE":"TRUNCATE".(JUSH=="pgsql"?"":" TABLE"))."> ":"").(function_exists('Adminer\drop_tables')?"<input type='submit' name='drop' value='".'Drop'."'".confirm().on_help("DROP TABLE").">":"");echo($Yi?"<div class='footer'><div>\n<fieldset><legend>".'Selected'." <span id='selected'></span></legend><div>$Yi\n</div></fieldset>\n":"");$i=(support("scheme")?adminer()->schemas():adminer()->databases());if(count($i)!=1&&function_exists('Adminer\move_tables')){echo"<fieldset><legend>".'Move to another database'." <span id='selected3'></span></legend><div>";$j=(isset($_POST["target"])?$_POST["target"]:(support("scheme")?$_GET["ns"]:DB));echo($i?html_select("target",$i,$j):'<input name="target" value="'.h($j).'" autocapitalize="off">'),"</label> <input type='submit' name='move' value='".'Move'."'>",(support("copy")?" <input type='submit' name='copy' value='".'Copy'."'> ".checkbox("overwrite",1,$_POST["overwrite"],'overwrite'):""),"</div></fieldset>\n";}echo"<input type='hidden' name='all' value=''".on('click','countTables',$S).">\n",input_token(),"</div></div>\n";}echo"</form>\n",script("tableCheck();");}echo(function_exists('Adminer\alter_table')?"<p class='links hover'><a href='".h(ME)."create='>".'Create table'."</a>\n":''),(support("view")?"<a href='".h(ME)."view='>".'Create view'."</a>\n":""),"</div>\n";if(support("routine")){echo"<div>\n","<h3 id='routines'>".'Routines'."</h3>\n";$Jj=routines();if($Jj){echo"<table class='odds'>\n",'<thead><tr><th>'.'Name'.'<td>'.'Type'.'<td>'.'Return type'."<td class='hover'><tbody>\n";foreach($Jj
as$I){$B=($I["SPECIFIC_NAME"]==$I["ROUTINE_NAME"]?"":"&name=".url_escape($I["ROUTINE_NAME"]));echo'<tr>','<th><a href="'.h(ME.($I["ROUTINE_TYPE"]!="PROCEDURE"?'callf=':'call=').url_escape($I["SPECIFIC_NAME"]).$B).'" title="'.'Call'.'">'.h($I["ROUTINE_NAME"]).'</a>','<td>'.h($I["ROUTINE_TYPE"]),'<td>'.h($I["DTD_IDENTIFIER"]),'<td class="hover"><a href="'.h(ME.($I["ROUTINE_TYPE"]!="PROCEDURE"?'function=':'procedure=').url_escape($I["SPECIFIC_NAME"]).$B).'">'.'Alter'."</a>";}echo"</table>\n";}echo'<p class="links hover">'.(support("procedure")?'<a href="'.h(ME).'procedure=">'.'Create procedure'.'</a>':'').'<a href="'.h(ME).'function=">'.'Create function'."</a>\n","</div>\n";}if(support("sequence")){echo"<div>\n","<h3 id='sequences'>".'Sequences'."</h3>\n";$ek=get_vals("SELECT relname FROM pg_class WHERE relkind = 'S' AND relnamespace = ".driver()->nsOid." ORDER BY relname");if($ek){echo"<table class='odds'>\n","<thead><tr><th>".'Name'."<tbody>\n";foreach($ek
as$W)echo"<tr><th><a href='".h(ME)."sequence=".url_escape($W)."'>".h($W)."</a>\n";echo"</table>\n";}echo"<p class='links hover'><a href='".h(ME)."sequence='>".'Create sequence'."</a>\n","</div>\n";}if(support("type")){echo"<div>\n","<h3 id='user-types'>".'User types'."</h3>\n";$um=types();if($um){echo"<table class='odds'>\n","<thead><tr><th>".'Name'."<tbody>\n";foreach($um
as$W)echo"<tr><th><a href='".h(ME)."type=".url_escape($W)."'>".h($W)."</a>\n";echo"</table>\n";}echo"<p class='links hover'><a href='".h(ME)."type='>".'Create type'."</a>\n","</div>\n";}if(support("event")){echo"<div>\n","<h3 id='events'>".'Events'."</h3>\n";$J=get_rows("SHOW EVENTS");if($J){echo"<table>\n","<thead><tr><th>".'Name'."<td>".'Schedule'."<td>".'Start'."<td>".'End'."<td class='hover'><tbody>\n";foreach($J
as$I)echo"<tr>","<th>".h($I["Name"]),"<td>".($I["Execute at"]?'At given time'."<td>".h($I["Execute at"]):'Every'." ".h($I["Interval value"])." ".h($I["Interval field"])."<td>".h($I["Starts"])),"<td>".h($I["Ends"]),'<td class="hover"><a href="'.h(ME).'event='.url_escape($I["Name"]).'">'.'Alter'.'</a>';echo"</table>\n";$id=get_val("SELECT @@event_scheduler");if($id&&$id!="ON")echo"<p class='error'><code class='jush-sqlset'>event_scheduler</code>: ".h($id)."\n";}echo'<p class="links hover"><a href="'.h(ME).'event=">'.'Create event'."</a>\n","</div>\n";}}}}page_footer();