import { ExtractJwt, Strategy } from 'passport-jwt';
import { PassportStrategy as NestPassportStrategy } from '@nestjs/passport';
import { Injectable } from '@nestjs/common';
import { Request } from 'express';
import { JwtPayload } from '@/auth/jwt/types/jwt-payload.type';
import { AuthService } from '@/auth/auth.service';
import { ConfigService } from '@nestjs/config';
import {
	EnvironmentVariables,
	JWT_REFRESH_TOKEN_COOKIE_NAME,
	JWT_REFRESH_TOKEN_SECRET,
} from '@/config/config.constants';
import { PassportStrategy } from '@/auth/auth.constants';
import { ExpressAuthInfo } from '@/auth/types/express-auth-info.type';

type DoneCallback = (
	err: Error | null,
	payload?: JwtPayload | false,
	info?: ExpressAuthInfo,
) => void;

const authInfo: ExpressAuthInfo = {
	strategy: PassportStrategy.JWT_REFRESH_TOKEN,
};

@Injectable()
export class JwtRefreshTokenStrategy extends NestPassportStrategy(
	Strategy,
	authInfo.strategy,
) {
	constructor(
		private authService: AuthService,
		configService: ConfigService<EnvironmentVariables, true>,
	) {
		super({
			jwtFromRequest: ExtractJwt.fromExtractors([
				ExtractJwt.fromAuthHeaderAsBearerToken(),
				(request: Request): string =>
					request.cookies[
						configService.get(JWT_REFRESH_TOKEN_COOKIE_NAME, { infer: true })
					] as string,
			]),
			secretOrKey: configService.get(JWT_REFRESH_TOKEN_SECRET, { infer: true }),
			passReqToCallback: true,
		});
	}

	// Signature from https://www.passportjs.org/packages/passport-jwt/
	async validate(
		req: Request,
		payload: JwtPayload,
		done: DoneCallback,
	): Promise<void> {
		try {
			const refreshToken =
				req.get('Authorization')?.replace('Bearer ', '').trim() ||
				(req.cookies[JWT_REFRESH_TOKEN_COOKIE_NAME] as string) ||
				'';

			await this.authService.validateJwtRefreshToken(payload.sub, refreshToken);

			done(null, payload, authInfo);
		} catch {
			done(null, false, authInfo);
		}
	}
}
