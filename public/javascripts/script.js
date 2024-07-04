(function () {
	// Cookie consent
	window.cookieconsent.initialise({
		palette: {
			popup: {
				background: '#24333e',
				text: '#ffffff',
			},
			button: {
				background: '#e53935',
				text: '#ffffff',
			},
		},
		showLink: false,
		theme: 'classic',
		content: {
			message:
				'This website uses cookies for user authentication, we do not use third-party trackers.',
		},
	});

	// Convert UTC time to local time in time elements
	document.querySelectorAll('time.datetime').forEach((elem) => {
		let value = elem.textContent;
		let date = new Date(value);
		elem.innerHTML = date.toLocaleString();
	});

	// Convert UTC time to local time in datetime elements
	document.querySelectorAll('input.datetime').forEach((elem) => {
		let value = elem.getAttribute('value');
		let date = new Date(value);
		let year = date.getFullYear();
		let month = ('0' + date.getMonth()).slice(-2);
		let day = ('0' + date.getDate()).slice(-2);
		let hours = ('0' + date.getHours()).slice(-2);
		let minutes = ('0' + date.getMinutes()).slice(-2);
		elem.setAttribute('value', `${year}-${month}-${day}T${hours}:${minutes}`);
	});
})();

async function manageErrors(response, errors) {
	// Set title
	const p = document.createElement('p');

	p.className = 'error';
	p.innerHTML = 'The following errors occurred:';
	errors.appendChild(p);

	// Create a list of errors
	const ul = document.createElement('ul');

	if (response.status === 400) {
		const error = await response.json();

		for (const message of error.message) {
			// Find the field that caused the error
			const fieldName = message.split(' ')[0];
			const field = document.querySelector(`#${fieldName}`);

			field.setAttribute('aria-invalid', 'true');

			// Create a list item with the error message
			const li = document.createElement('li');

			li.className = 'error';
			li.innerHTML = message[0].toUpperCase() + message.slice(1);

			ul.appendChild(li);
		}
	} else if (response.status === 401) {
		const li = document.createElement('li');

		li.className = 'error';
		li.innerHTML = 'Wrong email or password.';

		ul.appendChild(li);
	} else if (response.status === 403) {
		const li = document.createElement('li');

		li.className = 'error';
		li.innerHTML = 'You are not authorized to perform this action.';

		ul.appendChild(li);
	} else {
		const li = document.createElement('li');

		li.className = 'error';
		li.innerHTML = 'An unexpected error occurred.';

		ul.appendChild(li);
	}

	errors.appendChild(ul);
}
