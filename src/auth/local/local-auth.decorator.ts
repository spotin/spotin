/* eslint-disable @typescript-eslint/ban-types */
import { applyDecorators, UseGuards, CanActivate } from '@nestjs/common';
import {
	ApiForbiddenResponse,
	ApiSecurity,
	ApiUnauthorizedResponse,
} from '@nestjs/swagger';
import { LocalAuthGuard } from '@/auth/local/local-auth.guard';
import { PASSPORT_STRATEGY } from '@/auth/auth.constants';

export const LocalAuth = (...guards: (Function | CanActivate)[]) =>
	applyDecorators(
		UseGuards(LocalAuthGuard, ...guards),
		ApiSecurity(PASSPORT_STRATEGY.LOCAL),
		ApiUnauthorizedResponse({
			description: 'Wrong username/password.',
		}),
		ApiForbiddenResponse({
			description: 'Unsufficient roles or permissions.',
		}),
	);
