export enum PassportStrategy {
	EMAIL_PASSWORD = 'email-password',
	RESET_PASSWORD = 'reset-password',
	SESSION = 'session',
	TOKEN = 'token',
	UNCONFIGURED_SPOT = 'unconfigured-spot',
	UNRESTRICTED = 'unrestricted',
}

export const PASSWORD_RESET_HEADER_NAME = 'token';
export const TOKEN_HEADER_NAME = 'token';
