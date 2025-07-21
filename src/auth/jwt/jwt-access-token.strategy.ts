import { ExtractJwt, Strategy } from 'passport-jwt';
import { PassportStrategy as NestPassportStrategy } from '@nestjs/passport';
import { Injectable } from '@nestjs/common';
import { Request } from 'express';
import { JwtPayload } from '@/auth/jwt/types/jwt-payload.type';
import { ConfigService } from '@nestjs/config';
import {
	EnvironmentVariables,
	JWT_ACCESS_TOKEN_COOKIE_NAME,
	JWT_ACCESS_TOKEN_SECRET,
} from '@/config/config.constants';
import { PassportStrategy } from '@/auth/auth.constants';
import { ExpressAuthInfo } from '@/auth/types/express-auth-info.type';

type DoneCallback = (
	err: Error | null,
	payload?: JwtPayload | false,
	info?: ExpressAuthInfo,
) => void;

const authInfo: ExpressAuthInfo = {
	strategy: PassportStrategy.JWT_ACCESS_TOKEN,
};

@Injectable()
export class JwtAccessTokenStrategy extends NestPassportStrategy(
	Strategy,
	authInfo.strategy,
) {
	constructor(configService: ConfigService<EnvironmentVariables, true>) {
		super({
			jwtFromRequest: ExtractJwt.fromExtractors([
				ExtractJwt.fromAuthHeaderAsBearerToken(),
				(request: Request): string =>
					request.cookies[
						configService.get(JWT_ACCESS_TOKEN_COOKIE_NAME, { infer: true })
					] as string,
			]),
			secretOrKey: configService.get(JWT_ACCESS_TOKEN_SECRET, { infer: true }),
		});
	}

	// Signature from https://www.passportjs.org/packages/passport-jwt/
	validate(payload: JwtPayload, done: DoneCallback): void {
		done(null, payload, authInfo);
	}
}
