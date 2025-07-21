import { applyDecorators, UseGuards, CanActivate } from '@nestjs/common';
import {
	ApiForbiddenResponse,
	ApiSecurity,
	ApiUnauthorizedResponse,
} from '@nestjs/swagger';
import { EmailPasswordAuthGuard } from '@/auth/email-password/email-password-auth.guard';
import { PassportStrategy } from '@/auth/auth.constants';

// eslint-disable-next-line @typescript-eslint/no-unsafe-function-type, @typescript-eslint/explicit-function-return-type
export const EmailPasswordAuth = (...guards: (Function | CanActivate)[]) =>
	applyDecorators(
		UseGuards(EmailPasswordAuthGuard, ...guards),
		ApiSecurity(PassportStrategy.EMAIL_PASSWORD),
		ApiUnauthorizedResponse({
			description: 'Invalid user ID or password.',
		}),
		ApiForbiddenResponse({
			description: 'Insufficient roles or permissions.',
		}),
	);
