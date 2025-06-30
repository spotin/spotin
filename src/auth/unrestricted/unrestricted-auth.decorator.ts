import { UnrestrictedAuthGuard } from '@/auth/unrestricted/unrestricted-auth.guard';
import { CanActivate, UseGuards, applyDecorators } from '@nestjs/common';

// eslint-disable-next-line @typescript-eslint/no-unsafe-function-type
export const UnrestrictedAuth = (...guards: (Function | CanActivate)[]) =>
	applyDecorators(UseGuards(UnrestrictedAuthGuard, ...guards));
