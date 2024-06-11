import { PassportStrategy as NestPassportStrategy } from '@nestjs/passport';
import { Injectable } from '@nestjs/common';
import { AuthService } from '@/auth/auth.service';
import {
	PASSWORD_RESET_HEADER_NAME,
	PassportStrategy,
} from '@/auth/auth.constants';
import { User } from '@prisma/client';
import { ExpressAuthInfo } from '@/auth/types/express-auth-info.type';
import { HeaderAPIKeyStrategy } from 'passport-headerapikey';

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
		// Signature from http://www.passportjs.org/packages/passport-headerapikey/
		super(
			{ header: PASSWORD_RESET_HEADER_NAME },
			false,
			async (value: string, done: DoneCallback) => {
				try {
					const user =
						await this.authService.validatePasswordResetRequestToken(value);

					done(null, user, authInfo);
				} catch (error) {
					done(null, false, authInfo);
				}
			},
		);
	}
}
