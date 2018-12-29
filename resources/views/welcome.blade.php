<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon"
      type="image/png"
      href="images/robot-white.png">

    <title>Fun With Bots</title>

	<link rel="stylesheet" href="css/app.css">

</head>
<body>
<div class="container">
    <div class="content">
		<h1 style="color:white;font-size:80px;">Fun with <i class="fas fa-robot"></i>s</h1>

        <script>
			var botmanWidget = {
				frameEndpoint: '/chat',
				'title': 'Bot Window',
				'introMessage': 'Welcome to "Fun With Bots"',
				'mainColor': '#2193b0',
				'headerTextColor': '#ffffff',
				'aboutText': '',
				'bubbleBackground': '#ffffff',
				'bubbleAvatarUrl': '/images/comments-regular.svg',
				'displayMessageTime': true,
				'desktopHeight': '1500'
			};
        </script>
		<script src='https://cdn.jsdelivr.net/npm/botman-web-widget@0/build/js/widget.js'></script>
		<script>
			function chat() {
				botmanChatWidget.open();
			}
			window.onload = chat;
		</script>
    </div>
</div>
</body>
</html>
