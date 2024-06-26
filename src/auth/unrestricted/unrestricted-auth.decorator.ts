/* eslint-disable @typescript-eslint/ban-types */
import { UnrestrictedAuthGuard } from '@/auth/unrestricted/unrestricted-auth.guard';
import { CanActivate, UseGuards, applyDecorators } from '@nestjs/common';

// eslint-disable-next-line @typescript-eslint/explicit-function-return-type
export const UnrestrictedAuth = (...guards: (Function | CanActivate)[]) =>
	applyDecorators(UseGuards(UnrestrictedAuthGuard, ...guards));
