import { PassportStrategy } from '@/auth/auth.constants';
import { JwtAccessTokenOrUnrestrictedAuthGuard } from '@/auth/jwt-access-token-or-unrestricted/jwt-access-token-or-unrestricted-auth.guard';
import { CanActivate, UseGuards, applyDecorators } from '@nestjs/common';
import { ApiBearerAuth } from '@nestjs/swagger';

export const JwtAccessTokenOrUnrestrictedAuth = (
	// eslint-disable-next-line @typescript-eslint/no-unsafe-function-type
	...guards: (Function | CanActivate)[]
	// eslint-disable-next-line @typescript-eslint/explicit-function-return-type
) =>
	applyDecorators(
		UseGuards(JwtAccessTokenOrUnrestrictedAuthGuard, ...guards),
		ApiBearerAuth(PassportStrategy.JWT_ACCESS_TOKEN),
	);
