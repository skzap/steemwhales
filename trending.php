<?php
include('config.php');

$accounts = array();
$accsPerPage = 25;
$page = 1;
$offset = 0;
$days = 7;

if (isSet($_GET["d"])) {
    $days = $mysqli->real_escape_string($_GET["d"]);
    if ($days > 50) {
        $days = 50;
    }
}


// redirecting on menu search
if (isSet($_GET["g"])) {
    header("Location: /".$_GET["g"]);
    die();
}

// sorting variable
$sqlSort = 'accounts.posting_rewards-history.posting_rewards';
$historySort = 'estimated_value';
$sort = 'pr';
if (isSet($_GET["s"])) {
    $sort = $_GET["s"];
    switch ($sort) {

        case 'cr':
            $sqlSort = '(accounts.curation_rewards-history.curation_rewards)*1000000/accounts.vesting_shares';
            $historySort = 'curation_rewards';
            break;

        case 'pr':
            $sqlSort = 'accounts.posting_rewards-history.posting_rewards';
            $historySort = 'posting_rewards';
            break;

        case 'prpp':
            $sqlSort = '(accounts.posting_rewards-history.posting_rewards)/(accounts.post_count-history.post_count)';
            $historySort = 'posting_rewards_per_post';
            break;

        case 'posts':
            $sqlSort = 'accounts.post_count-history.post_count';
            $historySort = 'post_count';
            break;

        case 'followers':
            $sqlSort = 'accounts.followers-history.followers';
            $historySort = 'follows';
            break;

        case 'following':
            $sqlSort = 'accounts.following-history.following';
            $historySort = 'follows';
            break;

        case 'reputation':
            $sqlSort = 'accounts.reputation';
            $historySort = 'reputation';
            break;

        default:
            break;
    }
}

// search
if (isSet($_GET["name"])) {
    $name = $mysqli->real_escape_string($_GET["name"]);
    $sql = "SELECT $sqlSort FROM accounts INNER JOIN history on accounts.name = history.name and history.date = CURDATE() - INTERVAL $days DAY WHERE accounts.name='$name'";
    $result = $mysqli->query($sql);
    $userStats = $result->fetch_row();
    $userStats = $userStats[0];

    $sql = "INSERT INTO search (name) VALUES ('$name')";
    $result = $mysqli->query($sql);

    $sql = "SELECT COUNT(*) from accounts INNER JOIN history on accounts.name = history.name and history.date = CURDATE() - INTERVAL $days DAY WHERE $sqlSort > $userStats";
    $result = $mysqli->query($sql);
    if ($result == false) {
        header("Location: /trending/?e=1");
        die();
    }
    $userStats = $result->fetch_row();
    $futurePage = 1+floor($userStats[0]/$accsPerPage);
    header("Location: /trending/?p=".$futurePage."&s=".$sort."&d=".$days."&hl=".$name);
    die();
}


// pagination
if (isSet($_GET["p"])) {
    $page = $mysqli->real_escape_string($_GET["p"]);
    $offset = $accsPerPage*($page-1);
    if ($offset < 0) $offset = 0;
}


$sql = "SELECT accounts.name, 
                accounts.post_count-history.post_count as post_count, 
                accounts.posting_rewards-history.posting_rewards as posting_rewards, 
                (accounts.posting_rewards-history.posting_rewards)/(accounts.post_count-history.post_count) as posting_rewards_per_post, 
                (accounts.curation_rewards-history.curation_rewards)*1000000/accounts.vesting_shares as curation_rewards, 
                accounts.reputation, 
                accounts.last_active, 
                accounts.followers-history.followers as followers, 
                accounts.following-history.following as following
        FROM accounts 
        INNER JOIN history on accounts.name = history.name and history.date = CURDATE() - INTERVAL $days DAY
        ORDER BY ";
$sqlLimit = " DESC LIMIT ";
$sqlLimit .= $offset;
$sqlLimit .= ",".$accsPerPage;
//$sqlSort = " accounts.posting_rewards-history.posting_rewards ";
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
        ?><title>SteemWhales.com - Trendings <?php echo $days ?> days | Page <?php echo $page ?></title><?php
        }
        else {
            ?><title>SteemWhales.com - Trendings <?php echo $days ?> days | Statistics for STEEM</title><?php
        }
    }
    else {
        if ($page > 1) {
        ?><title>SteemWhales.com - Trendings <?php echo $days ?> days | Page <?php echo $page ?> - sorted by <?php echo $sort ?></title><?php
        }
        else {
            ?><title>SteemWhales.com - Trendings <?php echo $days ?> days | Statistics for STEEM - sorted by <?php echo $sort ?></title><?php
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
        <h1>Trending <input id='trendingDays' type='number' value='<?php echo $days ?>' style='width:60px;'> days</h1>
            
        <div class='row' style='margin-top: 15px;'>
            <a href='?p=<?php echo $page+1 ?>&d=<?php echo $days ?>&s=<?php echo $sort ?>' class="btn btn-default pull-right"><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span> Next</a>
            <?php if ($page-1 > 0) {?>
                <a href='?p=<?php echo $page-1 ?>&d=<?php echo $days ?>&s=<?php echo $sort ?>' class="btn btn-default pull-right"><span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Previous</a>
            <?php } ?>
            <div class='col-xs-3'>
                <form action="/trending/">
                    <input type="hidden" name="s" value="<?php echo $sort ?>">
                    <input type="hidden" name="d" value="<?php echo $days ?>">
                    <input type="text" name="name" class="form-control" placeholder="Search trendings">
                </form>
                
            </div>
            
        </div>
        <div class='row' style='margin-top: 15px;'>
            <div class='col-xs-12'>
                <table class='table table-condensed table-hover'>
                    <thead>
                        <th>Rank</th>
                        <th>Name (Rep)</th>
                        <th class='text-right'><a href='?p=1&d=<?php echo $days ?>&s=posts'><?php if ($sort == 'posts') { echo '<i>'; } ?>Posts<?php if ($sort == 'posts') { echo '</i>'; } ?></a></th>
                        <th class='text-right'><a href='?p=1&d=<?php echo $days ?>&s=followers'><?php if ($sort == 'followers') { echo '<i>'; } ?>Followers<?php if ($sort == 'followers') { echo '</i>'; } ?></a></th>
                        <th class='text-right'><a href='?p=1&d=<?php echo $days ?>&s=following'><?php if ($sort == 'following') { echo '<i>'; } ?>Following<?php if ($sort == 'following') { echo '</i>'; } ?></a></th>
                        <th class='text-right'><a href='?p=1&d=<?php echo $days ?>&s=pr'><?php if ($sort == 'pr') { echo '<i>'; } ?>Post Rewards<?php if ($sort == 'pr') { echo '</i>'; } ?></a></th>
                        <th class='text-right'><a href='?p=1&d=<?php echo $days ?>&s=prpp'><?php if ($sort == 'prpp') { echo '<i>'; } ?>Reward per Post<?php if ($sort == 'prpp') { echo '</i>'; } ?></a></th>
                        <th class='text-right'><a href='?p=1&d=<?php echo $days ?>&s=cr'><?php if ($sort == 'cr') { echo '<i>'; } ?>
                            <span data-toggle="tooltip" title="Curation Rewards per MVests">Curation Score</span>
                            <?php if ($sort == 'cr') { echo '</i>'; } ?>
                        </a></th>
                    </thead>
                    <tbody>
                        <?php
                            $rank = 1;
                            foreach ($accounts as $account) 
                            {
                                ?>
                                <tr <?php if (isSet($_GET["hl"]) && $_GET["hl"] == $account->name) { ?>style='background-color: yellow;'<?php } ?>>
                                    <td>#<?php echo $rank+$offset ?></td>
                                    <td>
                                        <?php getActivityIcon($account->last_active, 11) ?>
                                        <a href='/<?php echo $account->name ?>'><?php echo $account->name ?></a> <?php echo number_format(calcReputation($account->reputation), 1); ?>
                                    </td>
                                    <td class='text-right'><?php echo number_format ($account->post_count) ?></td>
                                    <td class='text-right'><?php echo number_format ($account->followers) ?></td>
                                    <td class='text-right'><?php echo number_format ($account->following) ?></td>
                                    <td class='text-right'><?php echo number_format ($account->posting_rewards) ?></td>
                                    <td class='text-right'><?php echo number_format ($account->posting_rewards_per_post) ?></td>
                                    <td class='text-right'><?php echo number_format ($account->curation_rewards) ?></td>
                                </tr>
                                <?php
                                $rank++;
                            }
                        ?>
                    </tbody>
                </table>
                <a href='?p=<?php echo $page+1 ?>&d=<?php echo $days ?>&s=<?php echo $sort ?>' class="btn btn-default pull-right"><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span> Next</a>
            <?php if ($page-1 > 0) {?>
                <a href='?p=<?php echo $page-1 ?>&d=<?php echo $days ?>&s=<?php echo $sort ?>' class="btn btn-default pull-right"><span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Previous</a>
            <?php } ?>
            </div>
        </div>
<?php include('footer.php') ?>
<script type="text/javascript">
$( document ).ready(function() {
  $("#trendingDays").bind('change', function () {
    window.location.search = jQuery.query.set("d", $("#trendingDays").val());
});
});
</script>
</body>
</html>