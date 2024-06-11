import { PassportStrategy } from '@/auth/auth.constants';

export type ExpressAuthInfo = {
	strategy: PassportStrategy;
};
