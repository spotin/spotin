import { PassportStrategy } from '@/auth/auth.constants';
import { AuthService } from '@/auth/auth.service';
import { ExpressAuthInfo } from '@/auth/types/express-auth-info.type';
import { User } from '@/users/types/user';
import { Injectable } from '@nestjs/common';
import { PassportStrategy as NestPassportStrategy } from '@nestjs/passport';
import { UserRole } from '@/users/enums/user-role';
import { Request } from 'express';
import { Strategy } from 'passport-custom';

type DoneCallback = (
	err: Error | null,
	user?: Pick<User, 'id' | 'role'> | false,
	info?: ExpressAuthInfo,
) => void;

const authInfo: ExpressAuthInfo = {
	strategy: PassportStrategy.UNCONFIGURED_SPOT,
};

@Injectable()
export class UnconfiguredSpotStrategy extends NestPassportStrategy(
	Strategy,
	authInfo.strategy,
) {
	constructor(private authService: AuthService) {
		super();
	}

	// Signature from https://www.passportjs.org/packages/passport-custom/
	async validate(request: Request, done: DoneCallback) {
		try {
			const spotId = request.params.id;

			const user = await this.authService.validateSpot(spotId);

			// Only keep the owner's ID and switch the role to GUEST
			done(
				null,
				{
					id: user.id,
					role: UserRole.GUEST,
				},
				authInfo,
			);
		} catch {
			done(null, false, authInfo);
		}
	}
}
