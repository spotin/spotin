import { PassportStrategy } from '@/auth/auth.constants';
import { TokenAuthGuard } from '@/auth/token/token-auth.guard';
import { applyDecorators, UseGuards, CanActivate } from '@nestjs/common';
import { ApiSecurity, ApiUnauthorizedResponse } from '@nestjs/swagger';

// eslint-disable-next-line @typescript-eslint/no-unsafe-function-type
export const TokenAuth = (...guards: (Function | CanActivate)[]) =>
	applyDecorators(
		UseGuards(TokenAuthGuard, ...guards),
		ApiSecurity(PassportStrategy.TOKEN),
		ApiUnauthorizedResponse({
			description: 'Wrong token.',
		}),
	);
