<?php
// time to
function humanTiming ($time)
{

    $time = 7200+$time-time(); // to get the time since that moment
    $time = ($time<1)? 1 : $time;
    $tokens = array (
        31536000 => 'year',
        2592000 => 'month',
        604800 => 'week',
        86400 => 'day',
        3600 => 'hour',
        60 => 'minute',
        1 => 'second'
    );

    foreach ($tokens as $unit => $text) {
        if ($time < $unit) continue;
        $numberOfUnits = floor($time / $unit);
        return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'');
    }
}

// time since utc
function humanTimingSince ($time)
{

    $time = time()-$time-7200; // to get the time since that moment
    $time = ($time<1)? 1 : $time;
    $tokens = array (
        31536000 => 'year',
        2592000 => 'month',
        604800 => 'week',
        86400 => 'day',
        3600 => 'hour',
        60 => 'minute',
        1 => 'second'
    );

    foreach ($tokens as $unit => $text) {
        if ($time < $unit) continue;
        $numberOfUnits = floor($time / $unit);
        return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'');
    }
}

// time since local
function humanTimingSince2 ($time)
{

    $time = time()-$time-3600; // to get the time since that moment
    $time = ($time<1)? 1 : $time;
    $tokens = array (
        31536000 => 'year',
        2592000 => 'month',
        604800 => 'week',
        86400 => 'day',
        3600 => 'hour',
        60 => 'min',
        1 => 'sec'
    );

    foreach ($tokens as $unit => $text) {
        if ($time < $unit) continue;
        $numberOfUnits = floor($time / $unit);
        return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'');
    }
}

function calcReputation ($value) {
    $reputation_level = 1;
    $neg = false;
    if ($value < 0)
        $neg = true;
    if ($value != 0) {
        $reputation_level = log10(abs($value));
        $reputation_level -= 9;
        if ($reputation_level < 0) $reputation_level = 0;
        if ($neg) $reputation_level *= -1;
        $reputation_level = $reputation_level*9 + 25;
    } else {
        return 25;
    }
    return $reputation_level;
}

function getActivityIcon ($value, $size) {
    $color = 'green';
    if (time() - strtotime($value) > 43200) {
        $color = 'orange';
    }
    if (time() - strtotime($value) > 172800) {
        $color = 'red';
    }
    ?>
    <span data-toggle="tooltip"
        title="Last activity <?php echo humanTimingSince(strtotime($value)) ?> ago (<?php echo $value ?> UTC)" 
        style='color: <?php echo $color ?>; font-size:<?php echo $size ?>px' class="glyphicon glyphicon-off" aria-hidden="true"></span>
    <?php
}

function getMedals ($account, $globals) {
    if (strtotime($account->next_vesting_withdrawal) - time() > 0) {
        ?> <span data-toggle="tooltip" title="Powering down <?php echo number_format($account->vesting_withdraw_rate*$globals->total_vesting_fund_steem/$globals->total_vesting_shares,2) ?> STEEM in <?php echo humanTiming(strtotime($account->next_vesting_withdrawal)) ?>" class="glyphicon glyphicon-piggy-bank power-down-icon" aria-hidden="true"></span><?php
    }
    if ($account->name == 'heimindanger') {
        ?> <span data-toggle="tooltip" title="Creator of steemwhales.com!" class="glyphicon glyphicon-king creator-icon" aria-hidden="true"></span><?php
    }
}
?>