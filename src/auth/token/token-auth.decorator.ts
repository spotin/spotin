/* eslint-disable @typescript-eslint/ban-types */
import { PassportStrategy } from '@/auth/auth.constants';
import { TokenAuthGuard } from '@/auth/token/token-auth.guard';
import { applyDecorators, UseGuards, CanActivate } from '@nestjs/common';
import { ApiSecurity, ApiUnauthorizedResponse } from '@nestjs/swagger';

export const TokenAuth = (...guards: (Function | CanActivate)[]) =>
	applyDecorators(
		UseGuards(TokenAuthGuard, ...guards),
		ApiSecurity(PassportStrategy.TOKEN),
		ApiUnauthorizedResponse({
			description: 'Wrong token.',
		}),
	);
