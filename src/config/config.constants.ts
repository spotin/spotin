export const FQDN = 'SPOT_IN_FQDN';
export const JWT_SECRET = 'SPOT_IN_JWT_SECRET';
export const JWT_EXPIRATION_TIME = 'SPOT_IN_JWT_EXPIRATION_TIME';
export const MAIL_HOST = 'SPOT_IN_MAIL_HOST';
export const MAIL_PORT = 'SPOT_IN_MAIL_PORT';
export const MAIL_USER = 'SPOT_IN_MAIL_USER';
export const MAIL_PASS = 'SPOT_IN_MAIL_PASS';
export const MAIL_SECURE = 'SPOT_IN_MAIL_SECURE';
export const MAIL_SENDER_NAME = 'SPOT_IN_MAIL_SENDER_NAME';
export const NODE_ENV = 'NODE_ENV';

export interface EnvironmentVariables {
	[FQDN]: string;
	[JWT_SECRET]: string;
	[JWT_EXPIRATION_TIME]: string;
	[MAIL_HOST]: string;
	[MAIL_PORT]: string;
	[MAIL_USER]: string;
	[MAIL_PASS]: string;
	[MAIL_SECURE]: boolean;
	[MAIL_SENDER_NAME]: string;
	[NODE_ENV]: string;
}
