/* eslint-disable @typescript-eslint/ban-types */
import { applyDecorators, UseGuards, CanActivate } from '@nestjs/common';
import {
	ApiBearerAuth,
	ApiForbiddenResponse,
	ApiUnauthorizedResponse,
} from '@nestjs/swagger';
import { JwtAuthGuard } from '@/auth/jwt/jwt-auth.guard';
import { PassportStrategy } from '@/auth/auth.constants';

// eslint-disable-next-line @typescript-eslint/explicit-function-return-type
export const JwtAuth = (...guards: (Function | CanActivate)[]) =>
	applyDecorators(
		UseGuards(JwtAuthGuard, ...guards),
		ApiBearerAuth(PassportStrategy.JWT),
		ApiUnauthorizedResponse({
			description: 'Wrong JWT.',
		}),
		ApiForbiddenResponse({
			description: 'Insufficient roles or permissions.',
		}),
	);
