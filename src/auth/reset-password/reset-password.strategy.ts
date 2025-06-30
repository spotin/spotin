import { PassportStrategy as NestPassportStrategy } from '@nestjs/passport';
import { Injectable } from '@nestjs/common';
import { AuthService } from '@/auth/auth.service';
import {
	PASSWORD_RESET_HEADER_NAME,
	PassportStrategy,
} from '@/auth/auth.constants';
import { ExpressAuthInfo } from '@/auth/types/express-auth-info.type';
import { HeaderAPIKeyStrategy } from 'passport-headerapikey';
import { User } from '@/users/types/user';

type DoneCallback = (
	err: Error | null,
	user?: User | false,
	info?: ExpressAuthInfo,
) => void;

const authInfo: ExpressAuthInfo = {
	strategy: PassportStrategy.RESET_PASSWORD,
};

@Injectable()
export class ResetPasswordStrategy extends NestPassportStrategy(
	HeaderAPIKeyStrategy,
	authInfo.strategy,
) {
	constructor(private authService: AuthService) {
		super({ header: PASSWORD_RESET_HEADER_NAME, prefix: '' }, false);
	}

	// Signature from http://www.passportjs.org/packages/passport-headerapikey/
	async validate(value: string, done: DoneCallback) {
		try {
			const user =
				await this.authService.validatePasswordResetRequestToken(value);

			done(null, user, authInfo);
		} catch {
			done(null, false, authInfo);
		}
	}
}
