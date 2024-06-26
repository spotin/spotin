/* eslint-disable @typescript-eslint/ban-types */
import { PassportStrategy } from '@/auth/auth.constants';
import { UnconfiguredSpotAuthGuard } from '@/auth/unconfigured-spot/unconfigured-spot-auth.guard';
import { CanActivate, UseGuards, applyDecorators } from '@nestjs/common';
import {
	ApiForbiddenResponse,
	ApiSecurity,
	ApiUnauthorizedResponse,
} from '@nestjs/swagger';

// eslint-disable-next-line @typescript-eslint/explicit-function-return-type
export const UnconfiguredSpotAuth = (...guards: (Function | CanActivate)[]) =>
	applyDecorators(
		UseGuards(UnconfiguredSpotAuthGuard, ...guards),
		ApiSecurity(PassportStrategy.UNCONFIGURED_SPOT),
		ApiUnauthorizedResponse({
			description:
				'The spot is already configured or you are not the owner of the spot.',
		}),
		ApiForbiddenResponse({
			description: 'Unsufficient roles or permissions.',
		}),
	);
