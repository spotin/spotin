export enum PassportStrategy {
	JWT_ACCESS_TOKEN = 'jwt-access-token',
	JWT_REFRESH_TOKEN = 'jwt-refresh-token',
	LOCAL = 'local',
	UNCONFIGURED_SPOT = 'unconfigured-spot',
	RESET_PASSWORD = 'reset-password',
	TOKEN = 'token',
	UNRESTRICTED = 'unrestricted',
}

export const PASSWORD_RESET_HEADER_NAME = 'token';
export const TOKEN_HEADER_NAME = 'token';
