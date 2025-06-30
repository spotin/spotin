export * from 'express';

declare module 'express' {
	interface Request {
		cookies: {
			jwt: string;
		};
	}
}
