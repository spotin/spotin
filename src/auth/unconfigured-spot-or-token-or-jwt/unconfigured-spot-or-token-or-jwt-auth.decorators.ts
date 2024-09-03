/* eslint-disable @typescript-eslint/ban-types */
import { PassportStrategy } from '@/auth/auth.constants';
import { UnconfiguredSpotOrTokenOrJwtAuthGuard } from '@/auth/unconfigured-spot-or-token-or-jwt/unconfigured-spot-or-token-or-jwt-auth.guard';
import { applyDecorators, UseGuards, CanActivate } from '@nestjs/common';
import {
	ApiBearerAuth,
	ApiForbiddenResponse,
	ApiSecurity,
	ApiUnauthorizedResponse,
} from '@nestjs/swagger';

export const UnconfiguredSpotOrTokenOrJwtAuth = (
	...guards: (Function | CanActivate)[]
	// eslint-disable-next-line @typescript-eslint/explicit-function-return-type
) =>
	applyDecorators(
		UseGuards(UnconfiguredSpotOrTokenOrJwtAuthGuard, ...guards),
		ApiBearerAuth(PassportStrategy.JWT),
		ApiSecurity(PassportStrategy.TOKEN),
		ApiSecurity(PassportStrategy.UNCONFIGURED_SPOT),
		ApiUnauthorizedResponse({
			description: 'Wrong JWT or token or the spot is already configured.',
		}),
		ApiForbiddenResponse({
			description: 'Insufficient roles or permissions.',
		}),
	);
