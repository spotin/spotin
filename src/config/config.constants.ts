export const FQDN = 'SPOT_IN_FQDN';
export const JWT_SECRET = 'SPOT_IN_JWT_SECRET';
export const JWT_EXPIRATION_TIME = 'SPOT_IN_JWT_EXPIRATION_TIME';
export const NODE_ENV = 'NODE_ENV';
export const SESSION_SECRET = 'SPOT_IN_SESSION_SECRET';

export interface EnvironmentVariables {
	[FQDN]: string;
	[JWT_SECRET]: string;
	[JWT_EXPIRATION_TIME]: string;
	[NODE_ENV]: string;
	[SESSION_SECRET]: string;
}
