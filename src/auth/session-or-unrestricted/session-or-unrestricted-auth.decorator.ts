import { PassportStrategy } from '@/auth/auth.constants';
import { SessionOrUnrestrictedAuthGuard } from '@/auth/session-or-unrestricted/session-or-unrestricted-auth.guard';
import { CanActivate, UseGuards, applyDecorators } from '@nestjs/common';
import { ApiBearerAuth } from '@nestjs/swagger';

export const SessionOrUnrestrictedAuth = (
	// eslint-disable-next-line @typescript-eslint/no-unsafe-function-type
	...guards: (Function | CanActivate)[]
	// eslint-disable-next-line @typescript-eslint/explicit-function-return-type
) =>
	applyDecorators(
		UseGuards(SessionOrUnrestrictedAuthGuard, ...guards),
		ApiBearerAuth(PassportStrategy.SESSION),
	);
