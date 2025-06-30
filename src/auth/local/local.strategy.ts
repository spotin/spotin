import { Strategy } from 'passport-local';
import { PassportStrategy as NestPassportStrategy } from '@nestjs/passport';
import { Injectable } from '@nestjs/common';
import { AuthService } from '@/auth/auth.service';
import { PassportStrategy } from '@/auth/auth.constants';
import { ExpressAuthInfo } from '@/auth/types/express-auth-info.type';
import { User } from '@/users/types/user';

type DoneCallback = (
	err: Error | null,
	user?: User | false,
	info?: ExpressAuthInfo,
) => void;

const authInfo: ExpressAuthInfo = {
	strategy: PassportStrategy.LOCAL,
};

@Injectable()
export class LocalStrategy extends NestPassportStrategy(
	Strategy,
	authInfo.strategy,
) {
	constructor(private authService: AuthService) {
		super({
			usernameField: 'email',
			passwordField: 'password',
		});
	}

	// Signature from https://www.passportjs.org/packages/passport-local/
	async validate(
		email: string,
		password: string,
		done: DoneCallback,
	): Promise<void> {
		try {
			const user = await this.authService.validateCredentials({
				email,
				password,
			});

			done(null, user, authInfo);
		} catch {
			done(null, false, authInfo);
		}
	}
}
