<?php
include('config.php');
?>
<html>
<head>
    <meta name="google-site-verification" content="FkA_aQzh9UJIesfdycaWWPjj3BPdbnB5STDTI-lAW8k" />
    <title>About Steem Whales - A rankings and statistics website for the STEEM Cryptocurrency</title>
    <link rel="icon" type="image/png" href="/pic/favicon.png">
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/css/whale.css">
</head>
<body>
    <?php include 'navigation.php';?>
    <div class="container" style="position: relative;right: 20px;">
        <div class="row text-right">
            <h1>About Steem Whales</h1>
        </div>
        <hr>
        <div class="row" style="margin-top: 15px;">
            <div class="col-sm-4">
                <a href="http://steemwhales.com/about/#"><img class="img-responsive" src="/pic/steem.png" title="Steem Logo" alt="Steem Logo" width="80%" style="position: relative;left: 45;"></a>
            </div>
            <div class="col-sm-8">
                <h2>What is Steem ?</h2>
                <p>
                    STEEM is a crypto-currency using blockchain technology, with no transactions fee. The best-selling feature of STEEM compared to other crypto-currencies is allowing users to earn coins by posting or upvoting blog articles published onto the blockchain.<br><br>
                    You can visit or join the STEEM Social Network on <a href="http://steemit.com/" target="_blank">steemit.com</a>. STEEM is supported by many cryptocurrency exchanges and can be quickly converted into Bitcoin.
                </p>
            </div>
        </div>
        <div class="row" style="margin-top: 15px;">
            <div class="col-sm-8 text-right">
                <h2>What are Whales ?</h2>
                <p style="padding-left: 30px;">
                    Steem users are often referred as whales, dolphins, minnows or other-sized sea creatures based on their STEEM Power, which represents the strength of their votes. The Blue Whale is the largest animal not only in the sea but in the world.
                </p>
            </div>
            <div class="col-sm-4">
                <a href="http://steemwhales.com/"><img class="img-responsive" src="/pic/blue_whale.png" title="Blue Whale" alt="Blue Whale" width="100%" style="position: relative;left: 25px;"></a>
            </div>
        </div>
        <div class="row" style="margin-top: 15px;">
            <div class="col-sm-4">
                <a href="http://steemwhales.com/"><img class="img-responsive" src="/pic/whale.png" title="Steem Whales" alt="Steem Whales Logo" width="100%" style="position: relative;right: 30;"></a>
            </div>
            <div class="col-sm-8">
                <h2>What is Steem Whales ?</h2>
                <p>
                    This website tracks and collects information about the STEEM crypto-currency Network. Currently <?php echo $globals->accounts_tracked ?> accounts are being monitored. We strive to provide useful data or features that are not available through a normal node connection.<br><br>
                    The website was created by <a href="https://steemit.com/@heimindanger">@heimindanger</a> on 20th June 2016.
                </p>
            </div>
        </div>
    </div>
    <?php include('footer.php') ?>
</body>
</html>