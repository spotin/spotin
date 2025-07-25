import { PassportStrategy } from '@/auth/auth.constants';
import { TokenOrJwtAuthGuard } from '@/auth/token-or-jwt/token-or-jwt-auth.guard';
import { applyDecorators, UseGuards, CanActivate } from '@nestjs/common';
import {
	ApiBearerAuth,
	ApiForbiddenResponse,
	ApiSecurity,
	ApiUnauthorizedResponse,
} from '@nestjs/swagger';

// eslint-disable-next-line @typescript-eslint/no-unsafe-function-type, @typescript-eslint/explicit-function-return-type
export const TokenOrJwtAuth = (...guards: (Function | CanActivate)[]) =>
	applyDecorators(
		UseGuards(TokenOrJwtAuthGuard, ...guards),
		ApiBearerAuth(PassportStrategy.JWT),
		ApiSecurity(PassportStrategy.TOKEN),
		ApiUnauthorizedResponse({
			description: 'Wrong JWT or token.',
		}),
		ApiForbiddenResponse({
			description: 'Insufficient roles or permissions.',
		}),
	);
