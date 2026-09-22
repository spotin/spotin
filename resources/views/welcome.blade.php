<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>{{ config('app.name') }}</title>
		@vite(['resources/css/app.css', 'resources/js/app.js'])
	</head>
	<body>
		<article class="prose lg:prose-xl">
			<h1>{{ __('ui.welcome') }}</h1>

			<p>
				First paragraph text.
			</p>

			<p>
				Second paragraph text.
			</p>
			<button class="btn">Button</button>
		</article>
	</body>
</html>

