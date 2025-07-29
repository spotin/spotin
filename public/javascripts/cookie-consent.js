import 'https://cdn.jsdelivr.net/gh/orestbida/cookieconsent@3.1.0/dist/cookieconsent.umd.js';

CookieConsent.run({
	categories: {
		necessary: {
			enabled: true,
			readOnly: true,
		},
	},

	language: {
		default: 'en',
		autoDetect: 'document',
		translations: {
			en: {
				consentModal: {
					title: 'Your privacy matters to us',
					description:
						'This website uses cookies to ensure you get the best experience on our website.<br><br>We do not share any personal data with third parties.<br><br>To learn more about how we manage your data, please consult our <a href="/privacy-policy">Privacy Policy</a>.',
					acceptAllBtn: 'OK!',
				},
			},
			fr: {
				consentModal: {
					title: 'Votre vie privée nous importe',
					description:
						'Ce site utilise des cookies pour vous garantir la meilleure expérience possible.<br><br>Nous ne partageons aucune donnée personnelle avec des tiers.<br><br>Pour en savoir plus sur la gestion de vos données, veuillez consulter notre <a href="/privacy-policy">Politique de confidentialité</a>.',
					acceptAllBtn: 'OK !',
				},
			},
			de: {
				consentModal: {
					title: 'Ihre Privatsphäre ist uns wichtig',
					description:
						'Diese Website verwendet Cookies, um Ihnen das bestmögliche Erlebnis zu bieten.<br><br>Wir geben keine personenbezogenen Daten an Dritte weiter.<br><br>Weitere Informationen zur Verwaltung Ihrer Daten finden Sie in unserer <a href="/privacy-policy">Datenschutzrichtlinie</a>.',
					acceptAllBtn: 'OK!',
				},
			},
		},
	},
});
