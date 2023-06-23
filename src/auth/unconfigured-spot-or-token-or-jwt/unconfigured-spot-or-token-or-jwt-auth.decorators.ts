/* eslint-disable @typescript-eslint/ban-types */
import { PASSPORT_STRATEGY } from '@/auth/auth.constants';
import { UnconfiguredSpotOrTokenOrJwtAuthGuard } from '@/auth/unconfigured-spot-or-token-or-jwt/unconfigured-spot-or-jwt-or-token-auth.guard';
import { applyDecorators, UseGuards, CanActivate } from '@nestjs/common';
import {
  ApiBearerAuth,
  ApiForbiddenResponse,
  ApiSecurity,
  ApiUnauthorizedResponse,
} from '@nestjs/swagger';

export const UnconfiguredSpotOrTokenOrJwtAuth = (
  ...guards: (Function | CanActivate)[]
) =>
  applyDecorators(
    UseGuards(UnconfiguredSpotOrTokenOrJwtAuthGuard, ...guards),
    ApiBearerAuth(PASSPORT_STRATEGY.JWT),
    ApiSecurity(PASSPORT_STRATEGY.TOKEN),
    ApiUnauthorizedResponse({
      description: 'Wrong JWT, token or the spot is already configured.',
    }),
    ApiForbiddenResponse({
      description: 'Unsufficient roles or permissions.',
    }),
  );
