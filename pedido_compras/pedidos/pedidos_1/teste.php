<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="ISO-8859-9">
        <title></title>
        <link href="../resources/css/bootstrap.css" type="text/css" rel="stylesheet">
        <link href="../resources/css/bootstrap-theme.css" type="text/css" rel="stylesheet">
        <script src="../js/jquery-1.10.2.min.js" type="text/javascript"></script>
        <script src="../resources/js/bootstrap.min.js" type="text/javascript"></script>
    </head>
    <body>
        <div role="tabpanel">

            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#ddd" aria-controls="ddd" role="tab" data-toggle="tab">Home</a></li>
                <li role="presentation"><a href="#bbb" aria-controls="bbb" role="tab" data-toggle="tab">Profile</a></li>
                <li role="presentation"><a href="#aaa" aria-controls="aaa" role="tab" data-toggle="tab">Messages</a></li>
                <li role="presentation"><a href="#ccc" aria-controls="ccc" role="tab" data-toggle="tab">Settings</a></li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="ddd">
                    <div role="tabpanel">

                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active"><a href="#home1" aria-controls="home1" role="tab" data-toggle="tab">Home</a></li>
                            <li role="presentation"><a href="#profile1" aria-controls="profile1" role="tab" data-toggle="tab">Profile</a></li>
                            <li role="presentation"><a href="#messages1" aria-controls="messages1" role="tab" data-toggle="tab">Messages</a></li>
                            <li role="presentation"><a href="#settings1" aria-controls="settings1" role="tab" data-toggle="tab">Settings</a></li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div role="tabpanel" class="tab-pane fade in active" id="home1">home 1</div>
                            <div role="tabpanel" class="tab-pane fade" id="profile1">profile 1</div>
                            <div role="tabpanel" class="tab-pane fade" id="messages1">messages 1</div>
                            <div role="tabpanel" class="tab-pane fade" id="settings1">settings 1</div>
                        </div>

                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="bbb">profiles</div>
                <div role="tabpanel" class="tab-pane" id="aaa">messages</div>
                <div role="tabpanel" class="tab-pane" id="ccc">settings</div>
            </div>

        </div>
    </body>
</html>
