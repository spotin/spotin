/* eslint-disable @typescript-eslint/ban-types */
import { PASSPORT_STRATEGY } from '@/auth/auth.constants';
import { JwtOrTokenAuthGuard } from '@/auth/jwt-or-token/jwt-or-token-auth.guard';
import { applyDecorators, UseGuards, CanActivate } from '@nestjs/common';
import {
  ApiBearerAuth,
  ApiForbiddenResponse,
  ApiSecurity,
  ApiUnauthorizedResponse,
} from '@nestjs/swagger';

export const JwtOrTokenAuth = (...guards: (Function | CanActivate)[]) =>
  applyDecorators(
    UseGuards(JwtOrTokenAuthGuard, ...guards),
    ApiBearerAuth(PASSPORT_STRATEGY.JWT),
    ApiSecurity(PASSPORT_STRATEGY.TOKEN),
    ApiUnauthorizedResponse({
      description: 'Wrong JWT or token.',
    }),
    ApiForbiddenResponse({
      description: 'Unsufficient roles or permissions.',
    }),
  );
