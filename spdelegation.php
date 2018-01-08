<?php
include('config.php');
?>
<html>
<head>
    <meta name="google-site-verification" content="FkA_aQzh9UJIesfdycaWWPjj3BPdbnB5STDTI-lAW8k" />
    <title>Post on the STEEM Blockchain</title>
    <link rel="icon" type="image/png" href="/pic/favicon.png">
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="/css/whale.css">
</head>
<body>
    <?php include 'navigation.php';?>
    <div class="container" style="position: relative;right: 20px;">
      <form class="form-horizontal" id='post'>
        <fieldset>

        <!-- Form Name -->
        <center><legend>Delegate your Steem Power</legend></center>

        <!-- Text input-->
        <div class="form-group">
          <label class="col-md-4 control-label" for="wif">Active Key (Private)</label>  
          <div class="col-md-4">
          <input id="wif" name="wif" type="text" placeholder="" class="form-control input-md" required="">
          </div>
        </div>

        <!-- Text input-->
        <div class="form-group">
          <label class="col-md-4 control-label" for="delegator">Delegator</label>  
          <div class="col-md-4">
          <input id="delegator" name="delegator" type="text" placeholder="" class="form-control input-md" required="">
          </div>
        </div>

        <!-- Text input-->
        <div class="form-group">
          <label class="col-md-4 control-label" for="delegatee">Delegatee</label>  
          <div class="col-md-4">
          <input id="delegatee" name="delegatee" type="text" placeholder="" class="form-control input-md" required="">
          </div>
        </div>

        <!-- Text input-->
        <div class="form-group">
          <label class="col-md-4 control-label" for="sp">Steem Power</label>  
          <div class="col-md-4">
          <input id="sp" name="sp" type="text" placeholder="" class="form-control input-md" required="">
          </div>
        </div>


        <!-- Button -->
        <div class="form-group">
          <label class="col-md-4 control-label" for="post"></label>
          <div class="col-md-4">
            <button id="post" name="post" class="btn btn-primary"><span class="glyphicon glyphicon-send" aria-hidden="true"></span> Delegate</button>
          </div>
        </div>

        </fieldset>
      </form>

      <div id='successalert' class="alert alert-success" style='display:none'>
        <strong>Success!</strong> STEEM Power delegated
      </div>

      <div id='infoalert' class="alert alert-info" style='display:none'>
        <strong>Please wait</strong> Submitting transaction on the blockchain...
      </div>

      <div id='erroralert' class="alert alert-danger" style='display:none'>
        <strong>Error!</strong> <span id='errormessage'></span>
      </div>
    </div>
    <?php include('footer.php') ?>
    <script src="//cdn.steemjs.com/lib/latest/steem.min.js"></script>
    <script type="text/javascript">
      $('#post').submit(function(event) {
        event.preventDefault();
        $('#infoalert').show()
        $('#successalert').hide()
        $('#erroralert').hide()
        var wif = $('#wif').val();
        var delegator = $('#delegator').val();
        var delegatee = $('#delegatee').val();
        var sp = $('#sp').val();

        var vesting_shares = Math.round(sp/<?php echo $globals->total_vesting_fund_steem/$globals->total_vesting_shares ?>)+'.000000 VESTS'

        console.log(wif,delegator,delegatee,vesting_shares)

        steem.broadcast.delegateVestingShares(wif, delegator, delegatee, vesting_shares, function(err, result) {
          console.log(err, result);
        });
      });
    </script>
</body>
</html>