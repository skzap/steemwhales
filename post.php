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
        <center><legend>Create a Post on the STEEM Blockchain</legend></center>

        <!-- Text input-->
        <div class="form-group">
          <label class="col-md-4 control-label" for="title">Title</label>  
          <div class="col-md-4">
          <input id="title" name="title" type="text" placeholder="" class="form-control input-md" required="">
          </div>
        </div>

        <!-- Text input-->
        <div class="form-group">
          <label class="col-md-4 control-label" for="permlink">Permlink</label>  
          <div class="col-md-4">
          <input id="permlink" name="permlink" type="text" placeholder="" class="form-control input-md" required="">
          </div>
        </div>

        <!-- Textarea -->
        <div class="form-group">
          <label class="col-md-4 control-label" for="markdown">Post Content (Markdown/HTML)</label>
          <div class="col-md-4">
            <textarea class="form-control" id="markdown" name="markdown" rows='10'></textarea>
          </div>
        </div>

        <!-- Text input-->
        <div class="form-group">
          <label class="col-md-4 control-label" for="tags">Tags</label>  
          <div class="col-md-4">
          <input id="tags" name="tags" type="text" placeholder="" class="form-control input-md" required="">
          <span class="help-block">Use spaces to separate tags</span>
          </div>
        </div>

        <!-- Text input-->
        <div class="form-group">
          <label class="col-md-4 control-label" for="author">Author Username</label>  
          <div class="col-md-4">
          <input id="author" name="author" type="text" placeholder="" class="form-control input-md" required="">
          </div>
        </div>

        <!-- Text input-->
        <div class="form-group">
          <label class="col-md-4 control-label" for="privatekey">Author Posting Private Key</label>  
          <div class="col-md-4">
          <input id="privatekey" name="privatekey" type="text" placeholder="" class="form-control input-md" required="">
          <span class="help-block">Findable in Wallet -> Permissions on steemit.com</span>  
          </div>
        </div>

        <div class="form-group">
          <label class="col-md-4 control-label" for="privatekey">
            Beneficiaries<br />
            <button type='button' id="addbenef" name="addbenef" class="btn btn-primary btn-xs" data-toggle="modal" data-target="#benefModal"><span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add</button>
          </label>
          <div class="col-md-4">
          <ul id='beneflist'>
          </ul>
          <span class="help-block">Author will get <span id='weightRemaining'>100</span>% of the rewards</span>  
          </div>
        </div>

        <!-- Text input-->
        <div class="form-group">
          <label class="col-md-4 control-label" for="sbdpercent">Steem Dollars %</label>  
          <div class="col-md-4">
          <input id="sbdpercent" name="sbdpercent" type="number" placeholder="" class="form-control input-md" required="" min='0' max='100' value='100' step='0.01'>
          <span class="help-block">Set to 0% if you want to power up all your rewards</span>  
          </div>
        </div>



        <!-- Text input-->
        <div class="form-group">
          <label class="col-md-4 control-label" for="maxpayout">Max Accepted SBD Payout</label>  
          <div class="col-md-4">
          <input id="maxpayout" name="maxpayout" type="number" placeholder="" class="form-control input-md" required="" min='0' max='1000000' value='1000000' step='1'>
          <span class="help-block">Set to 0 if you want to decline payout</span>  
          </div>
        </div>

        <!-- Button -->
        <div class="form-group">
          <label class="col-md-4 control-label" for="post"></label>
          <div class="col-md-4">
            <button id="post" name="post" class="btn btn-primary"><span class="glyphicon glyphicon-send" aria-hidden="true"></span> Publish</button>
          </div>
        </div>

        </fieldset>
      </form>

      <!-- Modal -->
      <div class="modal fade" id="benefModal" role="dialog">
        <div class="modal-dialog">
        
          <!-- Modal content-->
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal">&times;</button>
              <h4 class="modal-title">Add Beneficiary</h4>
            </div>
            <div class="modal-body">
              <form class="form-horizontal" id='post'>
                <fieldset>


                <!-- Text input-->
                <div class="form-group">
                  <label class="col-md-4 control-label" for="benefusername">Beneficiary Username</label>  
                  <div class="col-md-4">
                  <input id="benefusername" name="benefusername" type="text" placeholder="" class="form-control input-md" required="" value='heimindanger'>
                  <span class="help-block">The steem username of the beneficiary</span>  
                  </div>
                </div>

                <!-- Text input-->
                <div class="form-group">
                  <label class="col-md-4 control-label" for="benefshare">Beneficiary %</label>  
                  <div class="col-md-4">
                  <input id="benefshare" name="benefshare" type="number" placeholder="" class="form-control input-md" required="" min='0' max='100' value='5' step='0.01'>
                  <span class="help-block">The percentage of the posting rewards this beneficiary will receive</span>  
                  </div>
                </div>

                </fieldset>
              </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-success" data-dismiss="modal" id='benefadd'>Add</button>
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>
      <div id='successalert' class="alert alert-success" style='display:none'>
        <strong>Success!</strong> Your post is now public on the blockchain.
      </div>

      <div id='infoalert' class="alert alert-info" style='display:none'>
        <strong>Please wait</strong> Submitting your post...
      </div>

      <div id='erroralert' class="alert alert-danger" style='display:none'>
        <strong>Error!</strong> <span id='errormessage'></span>
      </div>
    </div>
    <?php include('footer.php') ?>
    <script src="//cdn.steemjs.com/lib/latest/steem.min.js"></script>
    <script type="text/javascript">
      beneficiaries = []
      weightRemaining = 10000
      tags = []

      $('#title').keydown(function() {
        var permlink = $(this).val().toLowerCase()
          .replace(/ /g,'-')
          .replace(/[^\w-]+/g,'')
        $('#permlink').val(permlink)
      })

      $('#tags').keydown(function() {
        tags = $(this).val().split(' ')
      })

      $('#benefadd').click(function() {
        for (var i = beneficiaries.length - 1; i >= 0; i--) {
          if (beneficiaries[i].account == $('#benefusername').val()) {
            weightRemaining += beneficiaries[i].weight;
            beneficiaries.splice(i,1);
          }
        };
        beneficiaries.push({
          account: $('#benefusername').val(),
          weight: 100*$('#benefshare').val()
        })
        weightRemaining -= 100*$('#benefshare').val()
        displayBenefs()
      })

      function displayBenefs() {
        $('#beneflist').empty()
        for (var i = beneficiaries.length - 1; i >= 0; i--) {
          var html = '<li>'+beneficiaries[i].account+' ('+beneficiaries[i].weight/100+'%) <button type="button" class="btn btn-danger btn-xs removeBenef" data-account="'+beneficiaries[i].account+'">x</button></li>'
          $('#beneflist').append(html)
        };
        $('#weightRemaining')[0].innerHTML = weightRemaining/100;
        $('.removeBenef').click(function(e) {
          console.log($(this).data('account'))
          for (var i = beneficiaries.length - 1; i >= 0; i--) {
            if (beneficiaries[i].account == $(this).data('account')) {
              weightRemaining += beneficiaries[i].weight;
              beneficiaries.splice(i,1);
              displayBenefs()
            }
          };
        })
      }

      $('#post').submit(function(event) {
        event.preventDefault();
        $('#infoalert').show()
        $('#successalert').hide()
        $('#erroralert').hide()
        var title = $('#title').val();
        var permlink = $('#permlink').val();
        var markdown = $('#markdown').val();
        var author = $('#author').val();
        var privatekey = $('#privatekey').val();
        var sbdpercent = 100*$('#sbdpercent').val();
        var maxpayout = parseInt($('#maxpayout').val());

        var operations = [
          ['comment',
            {
              parent_author: '',
              parent_permlink: tags[0],
              author: author,
              permlink: permlink,
              title: title,
              body: markdown,
              json_metadata : JSON.stringify({
                tags: tags,
                app: 'steemwhales.com/0.1'
              })
            }
          ],
          ['comment_options', {
            author: author,
            permlink: permlink,
            max_accepted_payout: maxpayout+'.000 SBD',
            percent_steem_dollars: sbdpercent,
            allow_votes: true,
            allow_curation_rewards: true,
            extensions: [
              [0, {
                beneficiaries: beneficiaries
              }]
            ]
          }]
        ];

        if (beneficiaries.length == 0) {
          operations[1][1].extensions = []
        }

        console.log(operations)
        steem.broadcast.send(
          { operations: operations, extensions: [] },
          { posting: privatekey },
          function(e, r) {
            $('#infoalert').hide()
            if (e) {
              $('#errormessage').html(e.message)
              $('#erroralert').show()
            } else {
              $('#successalert').show()
            }
            console.log(e,r)
          }
        )
      });
    </script>
</body>
</html>