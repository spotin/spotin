import { PassportStrategy } from '@/auth/auth.constants';
import { UnconfiguredSpotAuthGuard } from '@/auth/unconfigured-spot/unconfigured-spot-auth.guard';
import { CanActivate, UseGuards, applyDecorators } from '@nestjs/common';
import { ApiSecurity, ApiUnauthorizedResponse } from '@nestjs/swagger';

// eslint-disable-next-line @typescript-eslint/no-unsafe-function-type
export const UnconfiguredSpotAuth = (...guards: (Function | CanActivate)[]) =>
	applyDecorators(
		UseGuards(UnconfiguredSpotAuthGuard, ...guards),
		ApiSecurity(PassportStrategy.UNCONFIGURED_SPOT),
		ApiUnauthorizedResponse({
			description: 'The spot is already configured. Redirect to the spot view.',
		}),
	);
