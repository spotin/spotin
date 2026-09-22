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
			<h1>{{ __('ui.welcome.title') }}</h1>

			<p>{{ __('ui.welcome.first_paragraph') }}</p>

			<p>{{ __('ui.welcome.second_paragraph') }}</p>
			<button class="btn">{{ __('ui.welcome.button') }}</button>
			<form method="POST" action="{{ route('locale.update') }}">
				@csrf
				@method('PATCH')
				<select
					id="locale"
					name="locale"
					onchange="this.form.submit()"
					class="select">
					<option value="en" @selected(app()->getLocale() === 'en')>English</option>
					<option value="fr" @selected(app()->getLocale() === 'fr')>Français</option>
				</select>
			</form>
		</article>
	</body>
</html>

