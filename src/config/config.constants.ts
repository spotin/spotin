export const BASE_URL = 'SPOT_IN_BASE_URL';
export const SESSION_SECRET = 'SPOT_IN_SESSION_SECRET';
export const SESSION_COOKIE_NAME = 'SPOT_IN_SESSION_COOKIE_NAME';
export const SESSION_MAX_AGE = 'SPOT_IN_SESSION_MAX_AGE';
export const SESSION_CLEANUP_INTERVAL = 'SPOT_IN_SESSION_CLEANUP_INTERVAL';
export const MAIL_HOST = 'SPOT_IN_MAIL_HOST';
export const MAIL_PORT = 'SPOT_IN_MAIL_PORT';
export const MAIL_USER = 'SPOT_IN_MAIL_USER';
export const MAIL_PASS = 'SPOT_IN_MAIL_PASS';
export const MAIL_SECURE = 'SPOT_IN_MAIL_SECURE';
export const MAIL_SENDER_NAME = 'SPOT_IN_MAIL_SENDER_NAME';
export const NODE_ENV = 'NODE_ENV';

export interface EnvironmentVariables {
	[BASE_URL]: string;
	[SESSION_SECRET]: string;
	[SESSION_COOKIE_NAME]: string;
	[SESSION_MAX_AGE]: number;
	[SESSION_CLEANUP_INTERVAL]: number;
	[MAIL_HOST]: string;
	[MAIL_PORT]: string;
	[MAIL_USER]: string;
	[MAIL_PASS]: string;
	[MAIL_SECURE]: boolean;
	[MAIL_SENDER_NAME]: string;
	[NODE_ENV]: string;
}
