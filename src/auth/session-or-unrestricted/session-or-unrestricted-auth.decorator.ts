import { PassportStrategy } from '@/auth/auth.constants';
import { SessionOrUnrestrictedAuthGuard } from '@/auth/session-or-unrestricted/session-or-unrestricted-auth.guard';
import { CanActivate, UseGuards, applyDecorators } from '@nestjs/common';
import {
	ApiForbiddenResponse,
	ApiSecurity,
	ApiUnauthorizedResponse,
} from '@nestjs/swagger';

export const SessionOrUnrestrictedAuth = (
	// eslint-disable-next-line @typescript-eslint/no-unsafe-function-type
	...guards: (Function | CanActivate)[]
	// eslint-disable-next-line @typescript-eslint/explicit-function-return-type
) =>
	applyDecorators(
		UseGuards(SessionOrUnrestrictedAuthGuard, ...guards),
		ApiSecurity(PassportStrategy.SESSION),
		ApiUnauthorizedResponse({
			description: 'Wrong Session ID.',
		}),
		ApiForbiddenResponse({
			description: 'Insufficient roles or permissions.',
		}),
	);
