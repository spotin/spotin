import { ExtractJwt, Strategy } from 'passport-jwt';
import { PassportStrategy as NestPassportStrategy } from '@nestjs/passport';
import { Injectable } from '@nestjs/common';
import { Request } from 'express';
import { JwtPayload } from '@/auth/jwt/types/jwt-payload.type';
import { AuthService } from '@/auth/auth.service';
import { ConfigService } from '@nestjs/config';
import { EnvironmentVariables, JWT_SECRET } from '@/config/config.constants';
import { PassportStrategy } from '@/auth/auth.constants';
import { ExpressAuthInfo } from '@/auth/types/express-auth-info.type';
import { User } from '@/users/types/user';

type DoneCallback = (
	err: Error | null,
	user?: User | false,
	info?: ExpressAuthInfo,
) => void;

const authInfo: ExpressAuthInfo = {
	strategy: PassportStrategy.JWT,
};

@Injectable()
export class JwtStrategy extends NestPassportStrategy(
	Strategy,
	authInfo.strategy,
) {
	constructor(
		private authService: AuthService,
		configService: ConfigService<EnvironmentVariables, true>,
	) {
		// Signature from https://www.passportjs.org/packages/passport-jwt/
		super(
			{
				jwtFromRequest: ExtractJwt.fromExtractors([
					ExtractJwt.fromAuthHeaderAsBearerToken(),
					(request: Request): string => request.cookies.jwt,
				]),
				ignoreExpiration: false,
				secretOrKey: configService.get(JWT_SECRET, { infer: true }),
			},
			async (payload: JwtPayload, done: DoneCallback) => {
				try {
					const user = await this.authService.validateJwtPayload(payload);

					done(null, user, authInfo);
				} catch (error) {
					done(null, false, authInfo);
				}
			},
		);
	}
}
