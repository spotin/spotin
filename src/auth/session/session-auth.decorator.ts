import { PassportStrategy } from '@/auth/auth.constants';
import { SessionAuthGuard } from '@/auth/session/session-auth.guard';
import { applyDecorators, UseGuards, CanActivate } from '@nestjs/common';
import { ApiSecurity, ApiUnauthorizedResponse } from '@nestjs/swagger';

// eslint-disable-next-line @typescript-eslint/no-unsafe-function-type, @typescript-eslint/explicit-function-return-type
export const SessionAuth = (...guards: (Function | CanActivate)[]) =>
	applyDecorators(
		UseGuards(SessionAuthGuard, ...guards),
		ApiSecurity(PassportStrategy.SESSION),
		ApiUnauthorizedResponse({
			description: 'Wrong Session ID.',
		}),
	);
