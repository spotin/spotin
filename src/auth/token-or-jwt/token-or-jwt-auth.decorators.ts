/* eslint-disable @typescript-eslint/ban-types */
import { PASSPORT_STRATEGY } from '@/auth/auth.constants';
import { TokenOrJwtAuthGuard } from '@/auth/token-or-jwt/token-or-jwt-auth.guard';
import { applyDecorators, UseGuards, CanActivate } from '@nestjs/common';
import {
  ApiBearerAuth,
  ApiForbiddenResponse,
  ApiSecurity,
  ApiUnauthorizedResponse,
} from '@nestjs/swagger';

export const TokenOrJwtAuth = (...guards: (Function | CanActivate)[]) =>
  applyDecorators(
    UseGuards(TokenOrJwtAuthGuard, ...guards),
    ApiBearerAuth(PASSPORT_STRATEGY.JWT),
    ApiSecurity(PASSPORT_STRATEGY.TOKEN),
    ApiUnauthorizedResponse({
      description: 'Wrong JWT or token.',
    }),
    ApiForbiddenResponse({
      description: 'Unsufficient roles or permissions.',
    }),
  );
