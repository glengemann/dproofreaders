<?php
$relPath = "./pinc/";
include_once($relPath.'base.inc');
include_once($relPath.'theme.inc');
include_once($relPath.'site_news.inc');
include_once($relPath.'misc.inc'); // get_integer_param()

require_login();

// Very basic display of the 'recent' news stories for the given news page
//
// Sorts the news by their id's and then prints one by one.

$news_page_id = get_enumerated_param($_GET, "news_page_id", "FRONT", array_keys($NEWS_PAGES));
$num = get_integer_param($_GET, 'num', 0, 0, null);

$news_subject = get_news_subject($news_page_id);

$title = sprintf(_("Recent Site News Items for %s"), $news_subject);
output_header($title);

echo "<h1>" . html_safe($title) . "</h1>";

if ($num == 0) {
    // Invoking this script with num=0 (or without
    // the 'num' parameter) means "no limit".
    $limit_clause = "";
} else {
    $limit_clause = "LIMIT $num";
    echo "<a href='pastnews.php?news_page_id=$news_page_id'>"
        // TRANSLATORS: %s is the news subject.
        . sprintf(_("Show All %s News"), $news_subject) . "</a>";
}

$sql = sprintf("
    SELECT * FROM news_items 
    WHERE (news_page_id = '%s' OR news_page_id = 'GLOBAL') AND 
        status = 'recent'
    ORDER BY id DESC
    $limit_clause
", DPDatabase::escape($news_page_id));
$result = DPDatabase::query($sql);

if (mysqli_num_rows($result) == 0) {
    echo "<p>" . sprintf(_("No recent news items for %s"), $news_subject) . "</p>";
} else {
    while ($news_item = mysqli_fetch_array($result)) {
        $date_posted = icu_date_template("long", $news_item['date_posted']);
        echo "<br><a name='".$news_item['id']."'><b>$date_posted</b><br>".$news_item['content']."<br><hr class='center-align' style='width: 75%'><br>";
    }
}
