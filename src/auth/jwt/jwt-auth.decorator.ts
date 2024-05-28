/* eslint-disable @typescript-eslint/ban-types */
import { applyDecorators, UseGuards, CanActivate } from '@nestjs/common';
import {
	ApiBearerAuth,
	ApiForbiddenResponse,
	ApiUnauthorizedResponse,
} from '@nestjs/swagger';
import { JwtAuthGuard } from '@/auth/jwt/jwt-auth.guard';
import { PASSPORT_STRATEGY } from '@/auth/auth.constants';

export const JwtAuth = (...guards: (Function | CanActivate)[]) =>
	applyDecorators(
		UseGuards(JwtAuthGuard, ...guards),
		ApiBearerAuth(PASSPORT_STRATEGY.JWT),
		ApiUnauthorizedResponse({
			description: 'Wrong JWT.',
		}),
		ApiForbiddenResponse({
			description: 'Unsufficient roles or permissions.',
		}),
	);
