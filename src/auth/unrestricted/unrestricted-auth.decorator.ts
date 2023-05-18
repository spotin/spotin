/* eslint-disable @typescript-eslint/ban-types */
import { UnrestrictedAuthGuard } from '@/auth/unrestricted/unrestricted-auth.guard';
import { CanActivate, UseGuards, applyDecorators } from '@nestjs/common';

export const UnrestrictedAuth = (...guards: (Function | CanActivate)[]) =>
  applyDecorators(UseGuards(UnrestrictedAuthGuard, ...guards));
