import { JwtPayload } from '@/auth/jwt/types/jwt-payload.type';
import { createParamDecorator, ExecutionContext } from '@nestjs/common';
import { Request } from 'express';

export const AuthJwtPayload = createParamDecorator(
	(_: string, ctx: ExecutionContext): JwtPayload | undefined => {
		const request: Request = ctx.switchToHttp().getRequest();

		const jwtPayload = request.user as JwtPayload | undefined;

		return jwtPayload;
	},
);
