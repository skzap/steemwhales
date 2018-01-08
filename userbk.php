<?php

include('config.php');

// search
if (isSet($_GET["name"])) {
    $name = $mysqli->real_escape_string($_GET["name"]);
    $sql = "SELECT *, CURDATE() as date FROM accounts WHERE name='$name'";
    $result = $mysqli->query($sql);
    $account = $result->fetch_object();
    if (!$account) {
        header($_SERVER["SERVER_PROTOCOL"]." 404 Not Found", true, 404);
        include("404.php");
        die();
    }
    $account->steem_power = $account->vesting_shares*$globals->total_vesting_fund_steem/$globals->total_vesting_shares;
    $account->estimated_value = $globals->real_price*($account->balance+$globals->total_vesting_fund_steem*$account->vesting_shares/$globals->total_vesting_shares)+$account->sbd_balance;

    $metric = 'estimated_value';
    $sql = "SELECT * FROM history WHERE name='$name' ORDER BY date ASC";
    $account->history = array();
    $result = $mysqli->query($sql);
    while ($historyLine = $result->fetch_object())
    {
        $account->history[] = $historyLine;
    }
}
?>
<html>
<head>
    <title>SteemWhales.com - Statistics and History for <?php echo $account->name ?></title>
    <link rel="icon" type="image/png" href="/pic/favicon.png">
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/css/whale.css?v=1.0.8">
</head>
<body>
    <?php include 'navigation.php';?>
    <div class='container'>
        <div class='row'>
            <a href='/'><img class='col-xs-3 img-responsive' src='/pic/whale.png' /></a>
            <div class='col-xs-9'>
                <h1 class='text-right'>Steem Whale: <?php echo $account->name ?> <?php
                                        if (strtotime($account->next_vesting_withdrawal) - time() > 0) {
                                            ?> <span data-toggle="tooltip" title="Powering down <?php echo number_format($account->vesting_withdraw_rate*$globals->total_vesting_fund_steem/$globals->total_vesting_shares,2) ?> STEEM in <?php echo humanTiming(strtotime($account->next_vesting_withdrawal)) ?>" class="glyphicon glyphicon-piggy-bank power-down-icon" aria-hidden="true"></span><?php
                                        }
                                        ?></h1>
                <div class='text-right'>
                    <a target='_blank' href='http://steemd.com/@<?php echo $account->name ?>' class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-link" aria-hidden="true"></span> steemd</a>
                    <a target='_blank' href='http://steemit.com/@<?php echo $account->name ?>' class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-link" aria-hidden="true"></span> steemit</a>
                </div>
                <div class='row'>
                    <div class='col-xs-6 text-right'>
                        <h4>STEEM: <?php echo $account->balance ?></h4>
                        <h4>SP: <?php echo number_format($account->steem_power, 3) ?></h4>
                        <h4>SBD: <?php echo $account->sbd_balance ?></h4>
                        <h4>Estimated Value: <strong>$<?php echo number_format($account->estimated_value) ?></strong></h4>
                    </div>
                    <div class='col-xs-6 text-right'>
                        <h5>Reputation: <?php echo number_format(calcReputation($account->reputation), 1) ?></h5>
                        <h5>Posts: <?php echo $account->post_count ?></h5>
                        <h5>Posting Rewards: <?php echo $account->posting_rewards ?></h5>
                        <h5>Curation Rewards: <?php echo $account->curation_rewards ?></h5>
                        Updated On: <?php echo $account->updatedOn ?>
                    </div>
                </div>
            </div>
        </div>
        <div class='row' style='margin-top: 15px;'>
            <h2 class='text-center'>History Graph</h2>
            <div class='col-md-6'>
                <canvas id="chart1" width="400" height="400"></canvas>
            </div>
            <div class='col-md-6'>
                <canvas id="chart2" width="400" height="400"></canvas>
            </div>
        </div>
        <div class='row' style='margin-top: 15px;'>
            <div class='col-md-12'>
                <h2 class='text-center'>Daily Changes</h2>
                <table class='table'>
                    <thead>
                        <th>Date</th>
                        <th>Post #</th>
                        <th>Post Rewards</th>
                        <th>Curation Rewards</th>
                        <th>Steem</th>
                        <th>Steem Power</th>
                        <th>Steem Dollars</th>
                        <th>Estimated Value</th>
                    </thead>
                    <tbody>
                        <?php
                            $previousHistory = null;
                            foreach ($account->history as $history) 
                            {
                                if ($previousHistory) {
                                    $history->change = new stdClass();
                                    if ($previousHistory->post_count < $history->post_count)
                                        $history->change->post_count = $history->post_count-$previousHistory->post_count;
                                    if ($previousHistory->posting_rewards < $history->posting_rewards)
                                        $history->change->posting_rewards = $history->posting_rewards-$previousHistory->posting_rewards;
                                    if ($previousHistory->curation_rewards < $history->curation_rewards)
                                        $history->change->curation_rewards = $history->curation_rewards-$previousHistory->curation_rewards;
                                    if ($previousHistory->balance>0)
                                        $history->change->balance = number_format(100*($history->balance/$previousHistory->balance)-100,2);
                                    if ($previousHistory->steem_power>0)
                                        $history->change->steem_power = number_format(100*($history->steem_power/$previousHistory->steem_power)-100,2);
                                    if ($previousHistory->sbd_balance>0)
                                        $history->change->sbd_balance = number_format(100*($history->sbd_balance/$previousHistory->sbd_balance)-100,2);
                                    if ($previousHistory->estimated_value>0)
                                        $history->change->estimated_value = number_format(100*($history->estimated_value/$previousHistory->estimated_value)-100,2);
                                }
                                $previousHistory = $history;
                            }
                            $historyReversed = array_reverse($account->history);
                            foreach ($historyReversed as $history) {
                                ?><tr><td><?php echo $history->date ?></td><?php
                                ?><td><?php echo $history->post_count; if (isSet($history->change) && isSet($history->change->post_count)) echo ' (+'.$history->change->post_count.')'; ?></td><?php
                                ?><td><?php echo $history->posting_rewards; if (isSet($history->change) && isSet($history->change->posting_rewards)) echo ' (+'.$history->change->posting_rewards.')'; ?></td><?php
                                ?><td><?php echo $history->curation_rewards; if (isSet($history->change) && isSet($history->change->curation_rewards)) echo ' (+'.$history->change->curation_rewards.')'; ?></td><?php
                                ?><td><?php echo $history->balance; if (isSet($history->change) && isSet($history->change->balance)) echo ' ('.$history->change->balance.'%)'; ?></td><?php
                                ?><td><?php echo $history->steem_power; if (isSet($history->change) && isSet($history->change->steem_power)) echo ' ('.$history->change->steem_power.'%)'; ?></td><?php
                                ?><td><?php echo $history->sbd_balance; if (isSet($history->change) && isSet($history->change->sbd_balance)) echo ' ('.$history->change->sbd_balance.'%)'; ?></td><?php
                                ?><td>$<?php echo $history->estimated_value; if (isSet($history->change) && isSet($history->change->estimated_value)) echo ' ('.$history->change->estimated_value.'%)'; ?></td><?php
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class='row text-center'>
            1 STEEM = $ <?php echo $globals->real_price ?>
            <br />
            Steemians Tracked: <?php echo $globals->accounts_tracked ?>
            <br />
            total_vesting_shares: <?php echo $globals->total_vesting_shares ?>
            <br />
            total_vesting_fund_steem: <?php echo $globals->total_vesting_fund_steem ?>
            <br />
            steem_per_mvests: <?php echo 1000000*$globals->total_vesting_fund_steem/$globals->total_vesting_shares ?>
            <br />
        </div>
    </div>
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-81005941-1', 'auto');
  ga('send', 'pageview');

</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.1.6/Chart.min.js"></script>
<script>
var ctx = document.getElementById("chart1");
var userChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: [<?php 
            $dates = '';
            foreach ($account->history as $line) {
                $dates .= "'".$line->date."',";
            }
            echo $dates;
            echo "'NOW'";
        ?>],
        datasets: [
            {
                lineTension:0,
                label: 'Estimated Value',
                data: [<?php
                    $values = '';
                    foreach ($account->history as $line) {
                        $values .= $line->estimated_value.",";
                    }
                    echo $values;
                    echo $account->estimated_value;
                ?>],
                borderWidth: 1,
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255,99,132,1)'
            },
            {
                lineTension:0,
                label: 'STEEM',
                data: [<?php 
                    $values = '';
                    foreach ($account->history as $line) {
                        $values .= $line->balance.",";
                    }
                    echo $values;
                    echo $account->balance;
                ?>],
                borderWidth: 1,
                backgroundColor: 'rgba(0, 99, 132, 0.2)',
                borderColor: 'rgba(0,99,132,1)'
            },
            {
                lineTension:0,
                label: 'STEEM POWER',
                data: [<?php 
                    $values = '';
                    foreach ($account->history as $line) {
                        $values .= $line->steem_power.",";
                    }
                    echo $values;
                    echo $account->steem_power;
                ?>],
                borderWidth: 1,
                backgroundColor: 'rgba(255, 153, 0, 0.2)',
                borderColor: 'rgba(255,153,0,1)'
            },
            {
                lineTension:0,
                label: 'SBD',
                data: [<?php 
                    $values = '';
                    foreach ($account->history as $line) {
                        $values .= $line->sbd_balance.",";
                    }
                    echo $values;
                    echo $account->sbd_balance;
                ?>],
                borderWidth: 1,
                backgroundColor: 'rgba(0, 102, 51, 0.2)',
                borderColor: 'rgba(0,102,51,1)'
            }
        ]
    },
    options: {
        hover: {
            mode: 'single'
        },
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:true
                }
            }]
        }
    }
});
var ctx = document.getElementById("chart2");
var userChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: [<?php 
            $dates = '';
            foreach ($account->history as $line) {
                $dates .= "'".$line->date."',";
            }
            echo $dates;
            echo "'NOW'";
        ?>],
        datasets: [
            {
                lineTension:0,
                label: 'Posting Rewards',
                data: [<?php 
                    $values = '';
                    foreach ($account->history as $line) {
                        $values .= $line->posting_rewards.",";
                    }
                    echo $values;
                    echo $account->posting_rewards;
                ?>],
                borderWidth: 1,
                backgroundColor: 'rgba(204, 0, 0, 0.2)',
                borderColor: 'rgba(204, 0, 0, 1)'
            },
            {
                lineTension:0,
                label: 'Curation Rewards',
                data: [<?php 
                    $values = '';
                    foreach ($account->history as $line) {
                        $values .= $line->curation_rewards.",";
                    }
                    echo $values;
                    echo $account->curation_rewards;
                ?>],
                borderWidth: 1,
                backgroundColor: 'rgba(102, 51, 153, 0.2)',
                borderColor: 'rgba(102, 51, 153, 1)'
            }
        ]
    },
    options: {
        hover: {
            mode: 'single'
        },
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:true
                }
            }]
        }
    }
});
</script>
</body>
</html>