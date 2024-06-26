/* eslint-disable @typescript-eslint/ban-types */
import { PassportStrategy } from '@/auth/auth.constants';
import { JwtOrUnrestrictedAuthGuard } from '@/auth/jwt-or-unrestricted/jwt-or-unrestricted-auth.guard';
import { CanActivate, UseGuards, applyDecorators } from '@nestjs/common';
import { ApiBearerAuth } from '@nestjs/swagger';

// eslint-disable-next-line @typescript-eslint/explicit-function-return-type
export const JwtOrUnrestrictedAuth = (...guards: (Function | CanActivate)[]) =>
	applyDecorators(
		UseGuards(JwtOrUnrestrictedAuthGuard, ...guards),
		ApiBearerAuth(PassportStrategy.JWT),
	);
