import { applyDecorators, UseGuards, CanActivate } from '@nestjs/common';
import {
	ApiBearerAuth,
	ApiForbiddenResponse,
	ApiUnauthorizedResponse,
} from '@nestjs/swagger';
import { JwtAccessTokenAuthGuard } from '@/auth/jwt/jwt-access-token-auth.guard';
import { PassportStrategy } from '@/auth/auth.constants';

// eslint-disable-next-line @typescript-eslint/no-unsafe-function-type, @typescript-eslint/explicit-function-return-type
export const JwtAccessTokenAuth = (...guards: (Function | CanActivate)[]) =>
	applyDecorators(
		UseGuards(JwtAccessTokenAuthGuard, ...guards),
		ApiBearerAuth(PassportStrategy.JWT_ACCESS_TOKEN),
		ApiUnauthorizedResponse({
			description: 'Wrong JWT.',
		}),
		ApiForbiddenResponse({
			description: 'Insufficient roles or permissions.',
		}),
	);
