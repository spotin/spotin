/* eslint-disable @typescript-eslint/ban-types */
import { PASSPORT_STRATEGY } from '@/auth/auth.constants';
import { JwtOrUnrestrictedAuthGuard } from '@/auth/jwt-or-unrestricted/jwt-or-unrestricted-auth.guard';
import { CanActivate, UseGuards, applyDecorators } from '@nestjs/common';
import { ApiBearerAuth } from '@nestjs/swagger';

export const JwtOrUnrestrictedAuth = (...guards: (Function | CanActivate)[]) =>
  applyDecorators(
    UseGuards(JwtOrUnrestrictedAuthGuard, ...guards),
    ApiBearerAuth(PASSPORT_STRATEGY.JWT),
  );
