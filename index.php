<?php
include('config.php');

$accounts = array();
$accsPerPage = 25;
$page = 1;
$offset = 0;

// redirecting on menu search
if (isSet($_GET["g"])) {
    header("Location: /".$_GET["g"]);
    die();
}

// sorting variable
$sqlSort = 'sbd_balance*'.$globals->sbd_price_usd.'+balance*'.$globals->steem_price_usd.'+vesting_shares*'.$globals->steem_price_usd*$globals->total_vesting_fund_steem/$globals->total_vesting_shares;
$historySort = 'estimated_value';
$sort = 'total';
if (isSet($_GET["s"])) {
    $sort = $_GET["s"];
    switch ($sort) {
        case 'sbd':
            $sqlSort = 'sbd_balance';
            $historySort = 'sbd_balance';
            break;

        case 'power':
            $sqlSort = 'vesting_shares';
            $historySort = 'steem_power';
            break;

        case 'steem':
            $sqlSort = 'balance';
            $historySort = 'balance';
            break;

        case 'cr':
            $sqlSort = 'curation_rewards';
            $historySort = 'curation_rewards';
            break;

        case 'pr':
            $sqlSort = 'posting_rewards';
            $historySort = 'posting_rewards';
            break;

        case 'posts':
            $sqlSort = 'post_count';
            $historySort = 'post_count';
            break;

        case 'followers':
            $sqlSort = 'followers';
            $historySort = 'follows';
            break;

        case 'following':
            $sqlSort = 'following';
            $historySort = 'follows';
            break;

        case 'reputation':
            $sqlSort = 'if(reputation=7454983988075524000,52204057, reputation)';
            $historySort = 'reputation';
            break;

        default:
            break;
    }
}

// search
if (isSet($_GET["name"])) {
    $name = $mysqli->real_escape_string($_GET["name"]);
    $sql = "SELECT $sqlSort FROM accounts WHERE name='$name'";
    $result = $mysqli->query($sql);
    $userStats = $result->fetch_row();
    $userStats = $userStats[0];

    $sql = "INSERT INTO search (name) VALUES ('$name')";
    $result = $mysqli->query($sql);

    $sql = "SELECT COUNT(*) from accounts WHERE $sqlSort > $userStats";
    $result = $mysqli->query($sql);
    if ($result == false) {
        header("Location: /?e=1");
        die();
    }
    $userStats = $result->fetch_row();
    $futurePage = 1+floor($userStats[0]/$accsPerPage);
    header("Location: /?p=".$futurePage."&s=".$sort."&hl=".$name);
    die();
}

// pagination
if (isSet($_GET["p"])) {
    $page = $mysqli->real_escape_string($_GET["p"]);
    $offset = $accsPerPage*($page-1);
    if ($offset < 0) $offset = 0;
}

// distrib graph
$distribStep = round($globals->accounts_tracked/1000);
$distribStep9 = $distribStep*9;
$distribStep10 = $distribStep*10;
$distribStep90 = $distribStep*90;
$distribStep100 = $distribStep*100;
$distribStep1500 = $distribStep*1500;

$distribution = array();
$sql = "SELECT sum(value)
FROM (SELECT $sqlSort as value FROM accounts WHERE name != 'steemit' ORDER BY $sqlSort DESC LIMIT $distribStep) t1";
$result = $mysqli->query($sql);
$userStats = $result->fetch_row();
$distribution[] = $userStats[0];

$sql = "SELECT sum(value) 
FROM (SELECT $sqlSort as value FROM accounts WHERE name != 'steemit' ORDER BY $sqlSort DESC LIMIT $distribStep,$distribStep9) t1";
$result = $mysqli->query($sql);
$userStats = $result->fetch_row();
$distribution[] = $userStats[0];

$sql = "SELECT sum(value) 
FROM (SELECT $sqlSort as value FROM accounts WHERE name != 'steemit' ORDER BY $sqlSort DESC LIMIT $distribStep10,$distribStep90) t1";
$result = $mysqli->query($sql);
$userStats = $result->fetch_row();
$distribution[] = $userStats[0];

$sql = "SELECT sum(value) 
FROM (SELECT $sqlSort as value FROM accounts WHERE name != 'steemit' ORDER BY $sqlSort DESC LIMIT $distribStep100,$distribStep1500) t1";
$result = $mysqli->query($sql);
$userStats = $result->fetch_row();
$distribution[] = $userStats[0];


$distribTotal = $distribution[0] + $distribution[1] + $distribution[2] + $distribution[3];
$distribution[] = $distribTotal;

// supply evolution graph
$supplyEvolution = array();
$sql = "SELECT DATE_FORMAT(date, '%m-%d') as date, $historySort as value FROM sumhistory WHERE DAYOFWEEK(date) = 1";
$result = $mysqli->query($sql);
while ($line = $result->fetch_object())
{
    $supplyEvolution[] = $line;
}
$sql = "SELECT 'NOW' as date, sum($sqlSort) as value FROM accounts";
$result = $mysqli->query($sql);
while ($line = $result->fetch_object())
{
    if ($sort == 'power')
        $line->value *= ($globals->total_vesting_fund_steem/$globals->total_vesting_shares);
    $supplyEvolution[] = $line;
}

$sql = 'SELECT name, post_count, balance, sbd_balance, vesting_shares, posting_rewards, curation_rewards, reputation, vesting_withdraw_rate, next_vesting_withdrawal, last_active, followers, following
        FROM accounts ORDER BY ';
$sqlLimit = ' DESC LIMIT ';
$sqlLimit .= $offset;
$sqlLimit .= ','.$accsPerPage;
$sql = $sql.$sqlSort.$sqlLimit;
$result = $mysqli->query($sql);

while ($account = $result->fetch_object())
{
    $accounts[] = $account;
}
?>
<html>
<head>
    <meta name="google-site-verification" content="FkA_aQzh9UJIesfdycaWWPjj3BPdbnB5STDTI-lAW8k" />
    <?php
    if ($sort == 'total') {
        if ($page > 1) {
        ?><title>SteemWhales.com - Rankings Page <?php echo $page ?></title><?php
        }
        else {
            ?><title>SteemWhales.com - Rankings and statistics for STEEM</title><?php
        }
    }
    else {
        if ($page > 1) {
        ?><title>SteemWhales.com - Rankings Page <?php echo $page ?> - sorted by <?php echo $sqlSort ?></title><?php
        }
        else {
            ?><title>SteemWhales.com - Rankings and statistics for STEEM - sorted by <?php echo $sqlSort ?></title><?php
        }
    }
    
    ?>
    <link rel="icon" type="image/png" href="/pic/favicon.png">
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/css/whale.css">
</head>
<body>
    <?php include 'navigation.php';?>
    <div class='container'>
        
        <?php
        if (isSet($_GET["e"])) {
            switch ($_GET["e"]) {
                case '1':
                    ?>
                    <div class="alert alert-warning">
                      <strong>Not found!</strong> If the username indicated truly exists, it will be added within the next 2 minutes.
                    </div>
                    <?php
                    break;
                
                default:
                    # code...
                    break;
            }
        }
        ?>
        <div class='row'>
            
            <div class='col-sm-3'>
                <a href='/'><img class='img-responsive' src='/pic/whale.png' title="Steem Whales" alt="Steem Whales Logo" /></a>
            </div>
            <div class='col-sm-5 text-center bigHomepageText'>
                <h1>SteemWhales.com</h1>
                <hr>
                <h2>Rankings by <?php echo $historySort ?></h2>
                <h4>Total <?php echo $historySort ?>: <?php echo number_format($supplyEvolution[count($supplyEvolution)-1]->value) ?></h4>
                <h4>Average <?php echo $historySort ?>: <?php echo number_format($supplyEvolution[count($supplyEvolution)-1]->value/$globals->accounts_tracked) ?></h4>
            </div>
            <div class='col-sm-4'>
                <div class='pull-right'>
                    <button class='btn btn-xs' onclick='drawDistribGraph();'>Distribution</button>
                    <button class='btn btn-xs' onclick='drawEvolutionGraph();'>Evolution</button>
                </div>
                <canvas id="chart1" width="400" height="250"></canvas>
            </div>
        </div>
        <hr />
        <div class='row' style='margin-top: 15px;'>

            <a href='?p=<?php echo $page+1 ?>&s=<?php echo $sort ?>' class="btn btn-default pull-right"><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span> Next</a>
            <?php if ($page-1 > 0) {?>
                <a href='?p=<?php echo $page-1 ?>&s=<?php echo $sort ?>' class="btn btn-default pull-right"><span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Previous</a>
            <?php } ?>
            <div class='col-xs-3'>
                <form action="/">
                    <input type="hidden" name="s" value="<?php echo $sort ?>">
                    <input type="text" name="name" class="form-control" placeholder="Search rankings">
                </form>
                
            </div>
            
        </div>
        <div class='row' style='margin-top: 15px;'>
            <div class='col-xs-12'>
                <table class='table table-condensed table-hover'>
                    <thead>
                        <th>Rank</th>
                        <th>Name</th>
                        <th class='text-right'><a href='?p=1&s=reputation'><?php if ($sort == 'reputation') { echo '<i>'; } ?>Rep<?php if ($sort == 'reputation') { echo '</i>'; } ?></a></th>
                        <th class='text-right'><a href='?p=1&s=posts'><?php if ($sort == 'posts') { echo '<i>'; } ?>Post Count<?php if ($sort == 'posts') { echo '</i>'; } ?></a></th>
                        <th class='text-right'><a href='?p=1&s=followers'><?php if ($sort == 'followers') { echo '<i>'; } ?>Followers<?php if ($sort == 'followers') { echo '</i>'; } ?></a></th>
                        <th class='text-right'><a href='?p=1&s=following'><?php if ($sort == 'following') { echo '<i>'; } ?>Following<?php if ($sort == 'following') { echo '</i>'; } ?></a></th>
                        <th class='text-right'><a href='?p=1&s=pr'><?php if ($sort == 'pr') { echo '<i>'; } ?>Posting<?php if ($sort == 'pr') { echo '</i>'; } ?></a></th>
                        <th class='text-right'><a href='?p=1&s=cr'><?php if ($sort == 'cr') { echo '<i>'; } ?>Curation<?php if ($sort == 'cr') { echo '</i>'; } ?></a></th>
                        <th class='text-right'><a href='?p=1&s=steem'><?php if ($sort == 'steem') { echo '<i>'; } ?>Steem<?php if ($sort == 'steem') { echo '</i>'; } ?></a></th>
                        <th class='text-right'><a href='?p=1&s=power'><?php if ($sort == 'power') { echo '<i>'; } ?>Steem Power<?php if ($sort == 'power') { echo '</i>'; } ?></a></th>
                        <th class='text-right'><a href='?p=1&s=sbd'><?php if ($sort == 'sbd') { echo '<i>'; } ?>Steem Dollars<?php if ($sort == 'sbd') { echo '</i>'; } ?></a></th>
                        <th class='text-right'><a href='?p=1&s=total'><?php if ($sort == 'total') { echo '<i>'; } ?>Estimated Value<?php if ($sort == 'total') { echo '</i>'; } ?></a></th>
                    </thead>
                    <tbody>
                        <?php
                            $rank = 1;
                            foreach ($accounts as $account) 
                            {
                                $account->steempower = $account->vesting_shares*$globals->total_vesting_fund_steem/$globals->total_vesting_shares;
                                ?>
                                <tr <?php if (isSet($_GET["hl"]) && $_GET["hl"] == $account->name) { ?>style='background-color: yellow;'<?php } ?>>
                                    <td>#<?php echo $rank+$offset ?></td>
                                    <td>
                                        <?php getActivityIcon($account->last_active, 11) ?>
                                        <a href='/<?php echo $account->name ?>'><?php echo $account->name ?></a>
                                        <?php
                                        getMedals($account, $globals);
                                        ?>
                                    </td>
                                    <td class='text-right'>
                                        <?php
                                        echo number_format(calcReputation($account->reputation), 1);
                                        ?>
                                    </td>
                                    <td class='text-right'><?php echo number_format ($account->post_count) ?></td>
                                    <td class='text-right'><?php echo number_format ($account->followers) ?></td>
                                    <td class='text-right'><?php echo number_format ($account->following) ?></td>
                                    <td class='text-right'><?php echo number_format ($account->posting_rewards) ?></td>
                                    <td class='text-right'><?php echo number_format ($account->curation_rewards) ?></td>
                                    <td class='text-right'><?php echo number_format ($account->balance) ?></td>
                                    <td class='text-right'><?php echo number_format ($account->steempower) ?></td>
                                    <td class='text-right'><?php echo number_format ($account->sbd_balance) ?></td>
                                    <td class='text-right'>
                                        $<?php
                                        $total = $globals->sbd_price_usd*$account->sbd_balance;
                                        $total += $account->balance*$globals->steem_price_usd;
                                        $total += $account->steempower*$globals->steem_price_usd;
                                        echo number_format ($total);
                                        ?>
                                    </td>
                                </tr>
                                <?php
                                $rank++;
                            }
                        ?>
                    </tbody>
                </table>
                <a href='?p=<?php echo $page+1 ?>&s=<?php echo $sort ?>' class="btn btn-default pull-right"><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span> Next</a>
            <?php if ($page-1 > 0) {?>
                <a href='?p=<?php echo $page-1 ?>&s=<?php echo $sort ?>' class="btn btn-default pull-right"><span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Previous</a>
            <?php } ?>
            </div>
        </div>
<?php include('footer.php') ?>
<script>
var currentChart = null;
function drawDistribGraph() {
    if (currentChart != null) currentChart.destroy();
    var ctx = document.getElementById("chart1");
    currentChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: [
                "1‰",
                "1%",
                "Dolphins",
                "Minnows"
            ],
            datasets: [
                {
                    data: [<?php echo round(100*$distribution[0]/$distribution[4],2); ?>, <?php echo round(100*$distribution[1]/$distribution[4],2); ?>, <?php echo round(100*$distribution[2]/$distribution[4],2); ?>, <?php if ($distribution[3] <= 0) { echo 0; } else { echo round(100*$distribution[3]/$distribution[4],2); } ?>],
                    backgroundColor: [
                        "#1e87c3",
                        "#FF6384",
                        "#FFCE56",
                        "#009900"
                    ],
                    hoverBackgroundColor: [
                        "#1e87c3",
                        "#FF6384",
                        "#FFCE56",
                        "#009900"
                    ]
                }]
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

function drawEvolutionGraph() {
    if (currentChart != null) currentChart.destroy();
    var ctx = document.getElementById("chart1");
    currentChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [<?php 
                $dates = '';
                foreach ($supplyEvolution as $line) {
                    $dates .= "'".$line->date."',";
                }
                echo $dates;
            ?>],
            datasets: [
                {
                    label: "<?php echo $sort ?>",
                    fill: true,
                    lineTension: 0.1,
                    backgroundColor: "rgba(30,135,195,0.4)",
                    borderColor: "#047bbc",
                    data: [<?php 
                        $values = '';
                        foreach ($supplyEvolution as $line) {
                            $values .= round($line->value).",";
                        }
                        echo $values;
                    ?>],
                    spanGaps: false,
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
drawDistribGraph();
</script>
</body>
</html>