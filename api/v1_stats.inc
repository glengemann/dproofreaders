<?php
include_once("exceptions.inc");

// DP API v1 -- Stats

//---------------------------------------------------------------------------
// stats/site

function api_v1_stats_site($method, $data, $query_params)
{
    $res = DPDatabase::query("SELECT COUNT(*) FROM users");
    [$registered_users] = mysqli_fetch_row($res);

    $stats = [
        "server_time" => date(DATE_ATOM),
        "registered_users" => (int)$registered_users,
    ];

    foreach ([1, 7, 30] as $days_back) {
        $res = DPDatabase::query(sprintf("
            SELECT COUNT(*)
            FROM users
            WHERE t_last_activity > UNIX_TIMESTAMP() - %d * 24*60*60
        ", $days_back));
        [$num_users] = mysqli_fetch_row($res);
        $key = sprintf("active_users_%d_day", $days_back);
        $stats[$key] = (int)$num_users;
    }
    return $stats;
}

//---------------------------------------------------------------------------
// stats/site/rounds

function render_round_stats($round_id)
{
    $stats = get_site_page_tally_summary($round_id);
    $tallyboard = new TallyBoard($round_id, 'U');
    return [
        "today_goal" => (int)$stats->curr_day_goal,
        "today_actual" => (int)$stats->curr_day_actual,
        "yesterday_goal" => (int)$stats->prev_day_goal,
        "yesterday_actual" => (int)$stats->prev_day_actual,
        "month_goal" => (int)$stats->curr_month_goal,
        "month_actual" => (int)$stats->curr_month_actual,
        "positive_users" => (int)$tallyboard->get_num_holders_with_positive_tally(),
    ];
}

function api_v1_stats_site_rounds($method, $data, $query_params)
{
    global $Round_for_round_id_;

    $return = [];
    foreach (array_keys($Round_for_round_id_) as $round_id) {
        $return[$round_id] = render_round_stats($round_id);
    }

    return $return;
}

//---------------------------------------------------------------------------
// stats/site/rounds/:roundid

function api_v1_stats_site_round($method, $data, $query_params)
{
    $round = $data[":roundid"];

    return render_round_stats($round->id);
}
