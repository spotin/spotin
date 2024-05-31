import { ExtractJwt, Strategy } from 'passport-jwt';
import { PassportStrategy } from '@nestjs/passport';
import { Injectable, UnauthorizedException } from '@nestjs/common';
import { Request } from 'express';
import { User } from '@prisma/client';
import { JwtPayload } from '@/auth/types/jwt-payload.type';
import { AuthService } from '@/auth/auth.service';
import { ConfigService } from '@nestjs/config';
import { JWT_SECRET } from '@/config/config.constants';
import { PASSPORT_STRATEGY } from '@/auth/auth.constants';
import { ExpressAuthInfo } from '@/auth/types/express-auth-info.type';

type DoneCallback = (
	err: Error | null,
	user?: User | false,
	info?: ExpressAuthInfo,
) => void;

const authInfo: ExpressAuthInfo = {
	strategy: PASSPORT_STRATEGY.JWT,
};

@Injectable()
export class JwtStrategy extends PassportStrategy(
	Strategy,
	PASSPORT_STRATEGY.JWT,
) {
	constructor(private authService: AuthService, configService: ConfigService) {
		// Signature from https://www.passportjs.org/packages/passport-jwt/
		super(
			{
				jwtFromRequest: ExtractJwt.fromExtractors([
					ExtractJwt.fromAuthHeaderAsBearerToken(),
					(request: Request) => request.cookies.jwt,
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
