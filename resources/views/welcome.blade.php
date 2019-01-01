<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="csrf-token" content="{{ csrf_token() }}">
		<link rel="icon"
		type="image/png"
		href="images/robot-white.png">

		<title>Fun With Bots</title>
		<link rel="stylesheet" href="css/app.css">
		
	</head>
	<body>
		<div id="app" class="container">
			<div class="content">
				<h1 class="site-header">Fun with<i class="fas fa-robot">s</i></h1>
				{{-- <p class="site-url" >Try it! https://google.com</p> --}}
				{{-- <botman-tinker api-endpoint="/botman"></botman-tinker> --}}
			</div>
		</div>

		<script>
			var botmanWidget = {
				frameEndpoint: '/chat',
				'title': 'Hello, welcome to:',
				'introMessage': '"Jon Presents: Fun With Bots"',
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
		<script src="js/app.js"></script>
	</body>
</html>
