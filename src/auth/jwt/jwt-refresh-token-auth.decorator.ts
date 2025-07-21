import { applyDecorators, UseGuards, CanActivate } from '@nestjs/common';
import {
	ApiBearerAuth,
	ApiForbiddenResponse,
	ApiUnauthorizedResponse,
} from '@nestjs/swagger';
import { PassportStrategy } from '@/auth/auth.constants';
import { JwtRefreshTokenAuthGuard } from '@/auth/jwt/jwt-refresh-token-auth.guard';

// eslint-disable-next-line @typescript-eslint/no-unsafe-function-type, @typescript-eslint/explicit-function-return-type
export const JwtRefreshTokenAuth = (...guards: (Function | CanActivate)[]) =>
	applyDecorators(
		UseGuards(JwtRefreshTokenAuthGuard, ...guards),
		ApiBearerAuth(PassportStrategy.JWT_REFRESH_TOKEN),
		ApiUnauthorizedResponse({
			description: 'Wrong JWT.',
		}),
		ApiForbiddenResponse({
			description: 'Insufficient roles or permissions.',
		}),
	);
