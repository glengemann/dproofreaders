<?php
$relPath="./pinc/";
include_once($relPath.'base.inc');
include_once($relPath.'pg.inc');
include_once($relPath.'theme.inc');
include_once($relPath.'site_specific.inc');
include_once($relPath.'showstartexts.inc');
include_once($relPath.'page_tally.inc');
include_once($relPath.'site_news.inc');
include_once($relPath.'misc.inc'); // undo_all_magic_quotes()

undo_all_magic_quotes();

$theme_args['css_data'] = "
    h2 {
        color: {$theme['color_headerbar_bg']};
        font-family: {$theme['font_mainbody']};
        font-size: large;
    }
";

output_header(_("Welcome"), True, $theme_args);
$etext_limit = 10;

default_page_heading();

show_news_for_page("FRONT");

// XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX

echo "\n"
    . "<h2>"
    . _("Site Concept")
    . "</h2>"
    . "\n";

echo "<p>"
    . _("Distributed Proofreaders provides a web-based method to ease the conversion of Public Domain books into e-books. By dividing the workload into individual pages, many volunteers can work on a book at the same time, which significantly speeds up the creation process.")
    . "</p>"
    . "\n";

echo "<p>"
    . _("During proofreading, volunteers are presented with a scanned page image and the corresponding OCR text on a single web page. This allows the text to be easily compared to the image, proofread, and sent back to the site. A second volunteer is then presented with the first volunteer's work and the same page image, verifies and corrects the work as necessary, and submits it back to the site. The book then similarly progresses through two formatting rounds using the same web interface.")
    . "</p>"
    . "\n";

echo "<p>"
    . _("Once all the pages have completed these steps, a post-processor carefully assembles them into an e-book, optionally makes it available to interested parties for 'smooth reading', and submits it to the Project Gutenberg archive.")
    . "</p>"
    . "\n";

// -----------------------------------------------------------------------------

echo "\n"
    . "<h2>"
    . _("How You Can Help")
    . "</h2>"
    . "\n";

echo ""
    . "<ul>\n"
    .   "<li>\n"
    .     sprintf(
            _("<a href='%s'>Register</a> with the site as a volunteer, and/or"),
            "accounts/addproofer.php")
    .   "</li>\n"
    .   "<li>\n"
    .     sprintf(
            _("<a href='%s'>Donate</a> to the Distributed Proofreaders Foundation."),
            "$wiki_url/DPFoundation:Information_for_Donors")
    .   "</li>\n"
    . "</ul>"
    . "\n";

echo "<p>"
    . _('Registered volunteers may contribute to Distributed Proofreaders in several ways including proofreading, "smooth reading" pre-released e-books to check for errors, managing projects, providing content, or even helping develop improvements to the site. Volunteers may also join other members of our community in our forums to discuss these and many other topics.')
    . "</p>"
    . "\n";

echo "\n"
    . "<h2>"
    . _("Volunteering at Distributed Proofreaders")
    . "</h2>"
    . "\n";

echo "<p>"
    . sprintf(
        _("It's easy to volunteer at Distributed Proofreaders. Simply <a href='%s'>register as a volunteer</a>. Once you've confirmed your registration by e-mail, you'll receive an introductory e-mail with basic instructions on how to log in and use the site. Then, you're ready to sign in and start learning to proofread or visit the smooth reading page to pick an e-book to read! Wherever you go, you'll find lots of information to help you get started. Please try our <a href='%s'>Walkthrough</a> for a preview of the steps involved when proofreading on this site."),
        "$code_url/accounts/addproofer.php",
        "$dyn_url/walkthrough/00_Main.htm")
    . "</p>"
    . "\n";

echo "<p>"
    . _("There is no commitment expected on this site beyond the understanding that you do your best. Spend as much or as little time as you like. We encourage you to proofread at least a page a day and/or smooth read a book as often as your time allows, but it's entirely up to you.")
    . "</p>"
    . "\n";

echo "<p>"
    . _('We hope you will join us in our mission of "preserving the literary history of the world in a freely available form for everyone to use."')
    . "</p>"
    . "\n";

echo "<p>"
    . _("Distributed Proofreaders regrets that we that we are unable to verify court-ordered community service because our system cannot adequately record time spent participating.")
    . "</p>"
    . "\n";

// XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX

echo "\n"
    . "<h2>"
    . _("Current Progress")
    . "</h2>"
    . "\n"
    . "\n";

echo "<table><tr><td valign='top'>";

//Gold E-texts
showstartexts($etext_limit,'gold'); echo "</td><td valign='top'>";

//Silver E-texts
showstartexts($etext_limit,'silver'); echo "</td><td valign='top'>";

//Bronze E-texts
showstartexts($etext_limit,'bronze'); echo "</td></tr></table>";

echo "<hr><center>\n";
echo _("Our community of proofreaders, project managers, developers, etc. is composed entirely of volunteers.");
echo "</center>\n";

// Show the number of users that have been active over various recent timescales.
foreach ( array(1,7,30) as $days_back )
{
    $res = mysql_query("
        SELECT COUNT(*)
        FROM users
        WHERE t_last_activity > UNIX_TIMESTAMP() - $days_back * 24*60*60
    ") or die(mysql_error());
    $num_users = mysql_result($res,0);
    
    $template = (
        $days_back == 1
        ? _('%s active users in the past twenty-four hours.')
        : _('%s active users in the past %d days.')
    );
    $msg = sprintf( $template, number_format($num_users), $days_back );
    echo "<center><i><b>$msg</b></i></center>\n";
}

echo "<hr><center>\n";
echo sprintf(
    _("Questions or comments? Please contact us at <a href='%s'>%s</a>."),
    "mailto:$general_help_email_addr",
    $general_help_email_addr);
echo "</center>&nbsp;<br>\n";

// vim: sw=4 ts=4 expandtab
