import { PassportStrategy } from '@/auth/auth.constants';
import { TokenOrSessionAuthGuard } from '@/auth/token-or-session/token-or-session-auth.guard';
import { applyDecorators, UseGuards, CanActivate } from '@nestjs/common';
import {
	ApiForbiddenResponse,
	ApiSecurity,
	ApiUnauthorizedResponse,
} from '@nestjs/swagger';

// eslint-disable-next-line @typescript-eslint/no-unsafe-function-type, @typescript-eslint/explicit-function-return-type
export const TokenOrSessionAuth = (...guards: (Function | CanActivate)[]) =>
	applyDecorators(
		UseGuards(TokenOrSessionAuthGuard, ...guards),
		ApiSecurity(PassportStrategy.TOKEN),
		ApiSecurity(PassportStrategy.SESSION),
		ApiUnauthorizedResponse({
			description: 'Wrong token or Session ID.',
		}),
		ApiForbiddenResponse({
			description: 'Insufficient roles or permissions.',
		}),
	);
