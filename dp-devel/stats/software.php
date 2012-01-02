<?php
$relPath="./../pinc/";
include_once($relPath.'dp_main.inc'); // Require login to view this potentially sensitive information.

// This include setup is highly unusual but neccesary because of mixing of
// phpBB2's and DP's database setup

function get_phpbb2_ver()
{
    global $forums_dir;

    define('IN_PHPBB', true);
    $phpbb_root_path=$forums_dir."/";
    $phpEx="php";
    if (@include_once($forums_dir."/common.php"))
    {
        return "2".$board_config['version'];
    } else {
        return "n/a";
    }
}

$phpbb2_ver=get_phpbb2_ver();

include_once($relPath.'theme.inc');
include_once($relPath.'languages.inc');

if (!@include('faq_data.inc')) $faq_data = array();

theme(_("Software information"),"header");

echo "<h2>"._("Software information")."</h2>\n";

echo "<h3>"._("Software versions")."</h3>\n";

echo "<table border='1'>\n";
echo "<tr><td>";
echo "<a href='http://www.php.net'>PHP</a>";
echo "</td><td>";

echo phpversion();

echo "</td></tr>\n";
echo "<tr><td>";
echo "<a href='http://www.mysql.com'>MySQL</a>";
echo "</td><td>";

$a=mysql_fetch_row(mysql_query("SELECT version()"));
echo $a[0];

echo "</td></tr>\n";
echo "<tr><td>";
echo "<a href='http://aspell.sourceforge.net'>Aspell</a>";
echo "</td><td>";

$a=`$aspell_executable -v`;
if (preg_match("/(?<=aspell)[\sa-z0-9.-]+/i",$a,$b))
{
    echo $b[0];
} else {
    echo "n/a";
}

echo "</td></tr>\n";
echo "<tr><td>";
echo "<a href='http://www.gnu.org/software/gettext/'>gettext</a>";
echo "</td><td>";

$a=`$xgettext_executable --version`;
if (preg_match("/[\sa-z0-9.-]+$/im",$a,$b))
{
    echo $b[0];
} else {
    echo "n/a";
}
//gettext (GNU gettext) 0.10.40
//gettext (GNU gettext-runtime) 0.14.1

echo "</td></tr>\n";
echo "<tr><td>";
echo "<a href='http://www.phpbb.com'>phpBB</a>";
echo "</td><td>";
echo $phpbb2_ver;
echo "</td></tr>\n";
echo "</table>";

echo "<br>\n";

echo "<h3>"._("Language statistics")."</h3>\n";
echo "<h4>" . _("Detection") . "</h4>";
echo "<ul>";
echo "<li>" . sprintf(_("Language cookie set to: %s"), $_COOKIE['language'] ? $_COOKIE['language'] : "<i>" . _("not set") . "</i>") . "</li>";
echo "<li>" . sprintf(_("User preference set to: %s"), $userP['u_intlang']) . "</li>";
// logic taken from gettext_setup.inc
$langs=explode(",",$_SERVER['HTTP_ACCEPT_LANGUAGE']);
foreach($langs as $i) {
    $i = explode(";",$i);
    $i = trim($i[0]);
    if(lang_code($i)) {
        $browser_language=lang_code($i);
        break;
    }
}
echo "<li>" . sprintf(_("Browser 'accept language' found to be: %s"), $browser_language) . "</li>";
echo "<li>" . sprintf(_("Final interface language was set to: %s"), $GLOBALS['intlang']) . "</li>";
echo "<li>" . sprintf(_("Locale was set to: %s"), $GLOBALS['locale'] ? $GLOBALS['locale'] : "<i>" . _("not set") . "</i>") . "</li>";
echo "</ul>";

echo "<h4>" . _("Server") . "</h4>";
echo "<table border='1'>\n";

echo "<tr valign='bottom'><th>"._("Language")."</th><th>"._("Local name")."</th><th>"._("Translation")."</th><th>"._("Documentation")."</th><th>"._("Forum interface")."</th><th>"._("Forums")."</th></tr>\n";

$dirname="language";

$phpbb_root_path=$forums_dir."/";

// Following code is copied from phpBB's
// includes/functions_selects.php:
$dir = @opendir($phpbb_root_path . $dirname);

$lang = array();
if ($dir)
{
    while ( $file = readdir($dir) )
    {
    	if (preg_match('#^lang_#i', $file) && !is_file(@phpbb_realpath($phpbb_root_path . $dirname . '/' . $file)) && !is_link(@phpbb_realpath($phpbb_root_path . $dirname . '/' . $file)))
    	{
    		$filename = trim(str_replace("lang_", "", $file));
    		$displayname = preg_replace("/^(.*?)_(.*)$/", "\\1 [ \\2 ]", $filename);
    		$displayname = preg_replace("/\[(.*?)_(.*)\]/", "[ \\1 - \\2 ]", $displayname);
    		$lang[$displayname] = $filename;
    	}
    }
}
// End of copied code

$lang=array_flip($lang);

$faqs=array(""=>array());
foreach($faq_data as $v) {
        if($v{2}=="/") {
                $l=substr($v,0,2);
                if(!isset($faqs[$l])) $faqs[$l]=array();
                array_push($faqs[$l],substr($v,3));
        } else {
                array_push($faqs[""],$v);
        }
}

ksort($faqs);

define('YES_MARK', _("Yes"));
define('NO_MARK', _("No"));
define('NA_MARK', _("n/a"));

$a=installed_langs();
foreach($a as $k=>$v) {
        echo "<tr><td>";
        echo eng_name($v);
        echo "</td><td>";
        echo lang_name($v);
	echo "</td><td>";
	echo YES_MARK;
	echo "</td><td>";
	echo (@$faqs[short_lang_code($v)]||short_lang_code($v)=="en")?YES_MARK:NO_MARK;
        echo "</td><td>";
	echo phpbb_lang($v)?($lang[phpbb_lang($v)]?YES_MARK:NO_MARK):NA_MARK;
        echo "</td><td>";
	echo lang_forum($v)?YES_MARK:NO_MARK;
        echo "</td></tr>\n";
}

echo "</table>\n";

echo "<br>\n";

echo "<h3>"._("Documentation statistics")."</h3>\n";

echo "<table border='1'>\n";

echo "<tr valign='bottom'><th>"._("Language")."</th><th>"._("Documents")."</th></tr>\n";

foreach($faqs as $k=>$v) {
	echo "<tr><td rowspan='".(count($v)+1)."' valign='top'><b>".
		bilingual_name(lang_code($k?$k:"en")).
		":</b></td></tr>\n";
	foreach($v as $v1) {
		echo "<tr><td><a href='$code_url/faq/$k".($k?"/":"")."$v1'>$v1</a></td></tr>\n";
	}
}

echo "</table>";

echo "<br>\n";

echo "<h3>"._("Spell checking dictionaries")."</h3>\n";


if (@chdir(rtrim(`$aspell_executable config -e dict-dir`)))
{
    
    $dicts=glob("??.multi");
    if (count($dicts) > 0)
    {
        echo "<table border='1'>\n";
        foreach($dicts as $k=>$v)
            echo "<tr><td>" . bilingual_name(substr($v,0,2)) . "</td></tr>\n";
        echo "</table>\n<br>\n";
    }
} else {
    echo "<p>n/a</p>";
}

theme("","footer");

// vim: sw=4 ts=4 expandtab
