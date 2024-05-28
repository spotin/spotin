/* eslint-disable @typescript-eslint/ban-types */
import { PASSPORT_STRATEGY } from '@/auth/auth.constants';
import { TokenAuthGuard } from '@/auth/token/token-auth.guard';
import { applyDecorators, UseGuards, CanActivate } from '@nestjs/common';
import { ApiSecurity, ApiUnauthorizedResponse } from '@nestjs/swagger';

export const TokenAuth = (...guards: (Function | CanActivate)[]) =>
	applyDecorators(
		UseGuards(TokenAuthGuard, ...guards),
		ApiSecurity(PASSPORT_STRATEGY.TOKEN),
		ApiUnauthorizedResponse({
			description: 'Wrong token.',
		}),
	);
