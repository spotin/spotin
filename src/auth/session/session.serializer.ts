import { PassportSerializer } from '@nestjs/passport';
import { Injectable } from '@nestjs/common';
import { User } from '@/users/types/user';
import { ExpressAuthInfo } from '@/auth/types/express-auth-info.type';
import { PassportStrategy } from '@/auth/auth.constants';
import { AuthService } from '@/auth/auth.service';

type DoneCallback = (
	err: Error | null,
	user?: User | string | false,
	info?: ExpressAuthInfo,
) => void;

const authInfo: ExpressAuthInfo = {
	strategy: PassportStrategy.SESSION,
};

@Injectable()
export class SessionSerializer extends PassportSerializer {
	constructor(private authService: AuthService) {
		super();
	}

	serializeUser(user: User, done: DoneCallback): void {
		done(null, user.id, authInfo);
	}

	async deserializeUser(userId: string, done: DoneCallback): Promise<void> {
		try {
			const user = await this.authService.validateSession(userId);

			done(null, user, authInfo);
		} catch {
			done(null, false, authInfo);
		}
	}
}
