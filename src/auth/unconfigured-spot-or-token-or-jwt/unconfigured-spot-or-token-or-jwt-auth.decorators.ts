/* eslint-disable @typescript-eslint/ban-types */
import { UnconfiguredSpotOrTokenOrJwtAuthGuard } from '@/auth/unconfigured-spot-or-token-or-jwt/unconfigured-spot-or-token-or-jwt-auth.guard';
import { JWT_AUTH_KEY } from '@/auth/jwt/jwt.strategy';
import { TOKEN_AUTH_KEY } from '@/auth/token/token.strategy';
import { applyDecorators, UseGuards, CanActivate } from '@nestjs/common';
import {
  ApiCookieAuth,
  ApiForbiddenResponse,
  ApiSecurity,
  ApiUnauthorizedResponse,
} from '@nestjs/swagger';

export const UnconfiguredSpotOrTokenOrJwtAuth = (
  ...guards: (Function | CanActivate)[]
) =>
  applyDecorators(
    UseGuards(UnconfiguredSpotOrTokenOrJwtAuthGuard, ...guards),
    ApiCookieAuth(JWT_AUTH_KEY),
    ApiSecurity(TOKEN_AUTH_KEY),
    ApiUnauthorizedResponse({
      description: 'Wrong JWT, token or the spot is already configured.',
    }),
    ApiForbiddenResponse({
      description: 'Unsufficient roles or permissions.',
    }),
  );
