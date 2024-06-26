/* eslint-disable @typescript-eslint/ban-types */
import { applyDecorators, UseGuards, CanActivate } from '@nestjs/common';
import {
	ApiForbiddenResponse,
	ApiSecurity,
	ApiUnauthorizedResponse,
} from '@nestjs/swagger';
import { LocalAuthGuard } from '@/auth/local/local-auth.guard';
import { PassportStrategy } from '@/auth/auth.constants';

// eslint-disable-next-line @typescript-eslint/explicit-function-return-type
export const LocalAuth = (...guards: (Function | CanActivate)[]) =>
	applyDecorators(
		UseGuards(LocalAuthGuard, ...guards),
		ApiSecurity(PassportStrategy.LOCAL),
		ApiUnauthorizedResponse({
			description: 'Wrong username/password.',
		}),
		ApiForbiddenResponse({
			description: 'Insufficient roles or permissions.',
		}),
	);
