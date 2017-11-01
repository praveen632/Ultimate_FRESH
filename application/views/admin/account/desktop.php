<html data-ng-app="" class="ng-scope gr__ttsvetko_github_io">
<head>
    <title>(HTML5) Desktop Notifications</title>
    <link rel="stylesheet" href="http://ttsvetko.github.io/HTML5-Desktop-Notifications/css/bootstrap.min.css">
    <style type="text/css">

        html, body {
            height: 100%;
        }

        body {
            box-shadow: 0 0 5px 2px #EEE;
            margin: 0 auto;
            padding: 100px 50px 0;
            position: relative;
            width: 60%;

        }

        input, .btn {
            -moz-box-sizing: border-box;
            box-sizing: border-box;
            padding: 17px 10px !important;
            width: 100%;
        }

        .navbar .brand {
            margin: 0;
        }

        .navbar-fixed-top {
            margin: 0;
            position: absolute;
            width: 100%;
        }

        .alert {
            cursor: pointer;
        }

        .alert.alert-success,
        .alert.alert-error {
            cursor: default;
        }

        @charset "UTF-8";
        [ng\:cloak], [ng-cloak], [data-ng-cloak], [x-ng-cloak], .ng-cloak, .x-ng-cloak {
            display: none;
        }

        ng\:form {
            display: block;
        }
    </style>
</head>
<body data-ng-controller="NotifyDemo" data-gr-c-s-loaded="true" class="ng-scope">
<div class="navbar navbar-fixed-top">
    <div class="navbar-inner">
        <a class="brand" href="#">(HTML5) Desktop Notifications Demo Page</a>
    </div>
</div>

<div class="alert alert-success" data-ng-hide="permissionsGranted" data-ng-bind-html-unsafe="message"
     data-ng-click="requestPermission()" style="display: none;"><strong>Success!</strong></div>

<form name="notificationForm"
"="" class="ng-pristine ng-valid ng-valid-required">
<input type="text" data-ng-model="notification.title" data-ng-required="true" data-ng-disabled="!permissionsGranted"
       placeholder="Notification Title" class="ng-pristine ng-valid ng-valid-required" required="required">
<input type="text" data-ng-model="notification.body" data-ng-disabled="!permissionsGranted"
       placeholder="Notification body" class="ng-pristine ng-valid">
<input type="text" data-ng-model="notification.icon" data-ng-disabled="true" placeholder="Icon"
       class="ng-pristine ng-valid" disabled="disabled">
<button class="btn btn-primary" data-ng-disabled="!notification.title || !permissionsGranted"
        data-ng-click="showNotification()">Display Notification
</button>
</form>

<script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.0.7/angular.min.js"></script>
<script src="http://ttsvetko.github.io/HTML5-Desktop-Notifications/js/desktop-notify.js"></script>
<script src="http://ttsvetko.github.io/HTML5-Desktop-Notifications/js/main.js"></script>

</body>
</html>