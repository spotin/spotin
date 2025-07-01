import { PassportStrategy } from '@/auth/auth.constants';
import { ExpressAuthInfo } from '@/auth/types/express-auth-info.type';
import { Injectable } from '@nestjs/common';
import { PassportStrategy as NestPassportStrategy } from '@nestjs/passport';
import { Strategy } from 'passport-custom';

type DoneCallback = (
	err: Error | null,
	user?: true,
	info?: ExpressAuthInfo,
) => void;

const authInfo: ExpressAuthInfo = {
	strategy: PassportStrategy.UNRESTRICTED,
};

@Injectable()
export class UnrestrictedStrategy extends NestPassportStrategy(
	Strategy,
	PassportStrategy.UNRESTRICTED,
) {
	constructor() {
		super();
	}

	// Signature from https://www.passportjs.org/packages/passport-custom/
	validate(_: Request, done: DoneCallback): void {
		done(null, true, authInfo);
	}
}
