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
    $account->u_metadata = json_decode($account->json_metadata, false);
    $account->steem_power = $account->vesting_shares*$globals->total_vesting_fund_steem/$globals->total_vesting_shares;
    $account->estimated_value = $globals->steem_price_usd*($account->balance+$globals->total_vesting_fund_steem*$account->vesting_shares/$globals->total_vesting_shares)+$globals->sbd_price_usd*$account->sbd_balance;

    $metric = 'estimated_value';
    $sql = "SELECT * FROM history WHERE name='$name' ORDER BY date DESC LIMIT 31";
    if (isSet($_GET["weekly"])) {
        $sql = "SELECT * FROM history WHERE name='$name' AND DAYOFWEEK(history.date)=1 ORDER BY date DESC LIMIT 52";
    }
    $account->history = array();
    $result = $mysqli->query($sql);
    while ($historyLine = $result->fetch_object())
    {
        $account->history[] = $historyLine;
    }
    $account->history = array_reverse($account->history);
}
?>
<html>
<head>
    <title><?php echo $account->name ?> on SteemWhales.com - <?php if (isSet($_GET['weekly'])) { echo 'Weekly SteemIt Data'; } else { echo 'Daily Statistics'; } ?></title>
    <link rel="icon" type="image/png" href="/pic/favicon.png">
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/css/whale.css?v=1.0.8">
</head>
<body>
    <?php include 'navigation.php';?>
    <div class='container'>
        <div class='row'>
            <?php
                if (isSet($account->u_metadata->profile->about)) {
                    ?>
                        <div class='col-sm-6 col-xs-6 col-md-6'>
                            <blockquote>
                              <p><?php echo $account->u_metadata->profile->about ?></p>
                            </blockquote>
                        </div>
                    <?php
                }
            ?>
            <div class='col-sm-6 col-xs-6 col-md-6 pull-right'>
                <h1 class='text-right'><?php getActivityIcon($account->last_active, 25) ?> <?php echo $account->name ?> <?php
                getMedals($account, $globals);
                ?></h1>
            </div>
        </div>
        <div class='row'>
            <div class='col-sm-6 col-md-3 hidden-xs' >
            <?php
                if (isSet($account->u_metadata->profile->profile_image)) {
                    ?>
                        <a href='/'><img class='img-responsive' src='<?php echo $account->u_metadata->profile->profile_image ?>' /></a>
                    <?php
                } else {
                    ?>
                        <a href='/'><img class='img-responsive' src='/pic/whale.png' /></a>
                    <?php
                }
                if (isSet($account->u_metadata->profile->location)) {
                    ?>
                        <div><span class="glyphicon glyphicon-map-marker" aria-hidden="true"></span> <?php echo $account->u_metadata->profile->location ?></div>
                    <?php
                }
                if (isSet($account->u_metadata->profile->website)) {
                    ?>
                        <div><span class="glyphicon glyphicon-link" aria-hidden="true"></span> <a href='<?php echo $account->u_metadata->profile->website ?>'><?php echo $account->u_metadata->profile->website ?></a></div>
                    <?php
                }
            ?>
            </div>
            <div class='col-sm-6 col-md-4 text-right pull-right'>
                
                <div class='text-right'>
                    <a target='_blank' href='http://steemd.com/@<?php echo $account->name ?>' class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-link" aria-hidden="true"></span> steemd</a>
                    <a target='_blank' href='http://steemit.com/@<?php echo $account->name ?>' class="btn btn-primary btn-xs"><span class="glyphicon glyphicon-link" aria-hidden="true"></span> steemit</a>
                </div>
                <br />
                <h4><?php echo $account->balance ?> STEEM</h4>
                <h4><?php echo number_format($account->steem_power, 3) ?> SP</h4>
                <h4>$<?php echo number_format($account->sbd_balance, 3) ?> SBD</h4>
                <h4>Estimated Value: <strong>$<?php echo number_format($account->estimated_value) ?> USD</strong></h4>
            </div>
            <div class='col-sm-offset-1 col-sm-5 col-md-offset-2 col-md-3 text-right panel panel-default pull-right' style='margin-top:0px;'>
                <table class='table table-responsive table-bordered' style='font-size: 13px'>
                    <tbody>
                        <tr>
                            <td>Reputation</td>
                            <td><?php echo number_format(calcReputation($account->reputation), 1) ?></td>
                        </tr>
                        <tr>
                            <td>Followers</td>
                            <td><?php echo $account->followers ?></td>
                        </tr>
                        <tr>
                            <td>Following</td>
                            <td><?php echo $account->following ?></td>
                        </tr>
                        <tr>
                            <td>Posts</td>
                            <td><?php echo $account->post_count ?></td>
                        </tr>
                        <tr>
                            <td>Posting</td>
                            <td><?php echo $account->posting_rewards ?></td>
                        </tr>
                        <tr>
                            <td>Curation</td>
                            <td><?php echo $account->curation_rewards ?></td>
                        </tr>
                        <tr>
                            <td>Updated</td>
                            <td title='<?php echo $account->updatedOn ?>'><?php echo humanTimingSince2(strtotime($account->updatedOn)) ?> ago</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class='row' style='margin-top: 15px;'>
            <ul class="nav nav-tabs pull-right navgraph">
              <li id='limoney' onclick="drawMoneyChart();" role="presentation" class="active"><a style='cursor:pointer'>Wallet History</a></li>
              <li id='lirewards' onclick="drawRewardsChart();" role="presentation"><a style='cursor:pointer'>Rewards</a></li>
              <li id='lisocial' onclick="drawSocialChart();" role="presentation"><a style='cursor:pointer'>Social</a></li>
            </ul>
            <div class="dropdown" style='width: 100px;'>
              <button class="btn btn-default <?php if (isSet($_GET['weekly'])) { echo 'btn-warning'; } else { echo 'btn-danger'; } ?> dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                <span class="glyphicon glyphicon-time" aria-hidden="true"></span> <?php if (isSet($_GET['weekly'])) { echo 'Weekly'; } else { echo 'Daily'; } ?>
                <span class="caret"></span>
              </button>
              <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                <li><a href="/<?php echo $account->name ?>">Daily</a></li>
                <li><a href="/<?php echo $account->name ?>?weekly">Weekly</a></li>
              </ul>
            </div>
        </div>
        <div class='row' style='margin-top: 15px;'>
            <div class='col-md-12'>
                <canvas id="chart1" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
    <div class='container-fluid'>
        <div class='row' style='margin-top: 15px;'>
            <div class='col-md-12'>
                <h2 class='text-center'>Daily Changes</h2>
                <table class='table'>
                    <thead>
                        <th>Date</th>
                        <th>Reputation</th>
                        <th>Followers</th>
                        <th>Following</th>
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
                                    if ($previousHistory->followers < $history->followers)
                                        $history->change->followers = $history->followers-$previousHistory->followers;
                                    if ($previousHistory->following < $history->following)
                                        $history->change->following = $history->following-$previousHistory->following;
                                    if ($previousHistory->post_count < $history->post_count)
                                        $history->change->post_count = $history->post_count-$previousHistory->post_count;
                                    if ($previousHistory->posting_rewards < $history->posting_rewards)
                                        $history->change->posting_rewards = $history->posting_rewards-$previousHistory->posting_rewards;
                                    if ($previousHistory->curation_rewards < $history->curation_rewards)
                                        $history->change->curation_rewards = $history->curation_rewards-$previousHistory->curation_rewards;
                                    if (number_format(calcReputation($previousHistory->reputation), 1) != number_format(calcReputation($history->reputation), 1))
                                        $history->change->reputation = number_format(calcReputation($history->reputation)-calcReputation($previousHistory->reputation), 1);
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
                                ?><td><?php echo number_format(calcReputation($history->reputation), 1); if (isSet($history->change) && isSet($history->change->reputation)) echo ' ('.$history->change->reputation.')';  ?></td><?php
                                ?><td><?php echo $history->followers; if (isSet($history->change) && isSet($history->change->followers)) echo ' (+'.$history->change->followers.')'; ?></td><?php
                                ?><td><?php echo $history->following; if (isSet($history->change) && isSet($history->change->following)) echo ' (+'.$history->change->following.')'; ?></td><?php
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
<?php include('footer.php') ?>
<script>
var userChart = null;
drawMoneyChart();
function drawMoneyChart() {
    if (userChart)
        userChart.destroy();
    $('.navgraph>li').removeClass('active');
    $('#limoney').addClass('active');
    var ctx = document.getElementById("chart1");
    userChart = new Chart(ctx, {
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
}

function drawRewardsChart() {
    if (userChart)
        userChart.destroy();
    $('.navgraph>li').removeClass('active');
    $('#lirewards').addClass('active');
    var ctx = document.getElementById("chart1");
    userChart = new Chart(ctx, {
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
}

function drawSocialChart() {
    if (userChart)
        userChart.destroy();
    $('.navgraph>li').removeClass('active');
    $('#lisocial').addClass('active');
    var ctx = document.getElementById("chart1");
    userChart = new Chart(ctx, {
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
                    label: 'Reputation',
                    data: [<?php 
                        $values = '';
                        foreach ($account->history as $line) {
                            $values .= calcReputation($line->reputation).",";
                        }
                        echo $values;
                        echo calcReputation($account->reputation);
                    ?>],
                    borderWidth: 1,
                    backgroundColor: 'rgba(128,128,128, 0.2)',
                    borderColor: 'rgba(128,128,128, 1)'
                },
                {
                    lineTension:0,
                    label: 'Followers',
                    data: [<?php 
                        $values = '';
                        foreach ($account->history as $line) {
                            $values .= $line->followers.",";
                        }
                        echo $values;
                        echo $account->followers;
                    ?>],
                    borderWidth: 1,
                    backgroundColor: 'rgba(0,190,190, 0.2)',
                    borderColor: 'rgba(0,190,190, 1)'
                },
                {
                    lineTension:0,
                    label: 'Following',
                    data: [<?php 
                        $values = '';
                        foreach ($account->history as $line) {
                            $values .= $line->following.",";
                        }
                        echo $values;
                        echo $account->following;
                    ?>],
                    borderWidth: 1,
                    backgroundColor: 'rgba(190,0,190, 0.2)',
                    borderColor: 'rgba(190,0,190, 1)'
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
}
</script>
</body>
</html>