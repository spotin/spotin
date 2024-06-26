/* eslint-disable @typescript-eslint/ban-types */
import { applyDecorators, UseGuards, CanActivate } from '@nestjs/common';
import { ApiSecurity, ApiUnauthorizedResponse } from '@nestjs/swagger';
import { PassportStrategy } from '@/auth/auth.constants';
import { ResetPasswordAuthGuard } from '@/auth/reset-password/reset-password.guard';

// eslint-disable-next-line @typescript-eslint/explicit-function-return-type
export const ResetPasswordAuth = (...guards: (Function | CanActivate)[]) =>
	applyDecorators(
		UseGuards(ResetPasswordAuthGuard, ...guards),
		ApiSecurity(PassportStrategy.RESET_PASSWORD),
		ApiUnauthorizedResponse({
			description: 'Wrong token.',
		}),
	);
