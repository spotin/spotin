export enum PassportStrategy {
	JWT = 'jwt',
	LOCAL = 'local',
	UNCONFIGURED_SPOT = 'unconfigured-spot',
	RESET_PASSWORD = 'reset-password',
	TOKEN = 'token',
	UNRESTRICTED = 'unrestricted',
}

export const PASSWORD_RESET_HEADER_NAME = 'token';
export const TOKEN_HEADER_NAME = 'token';
