/* eslint-disable @typescript-eslint/ban-types */
import { JwtOrTokenOrUnconfiguredSpotAuthGuard } from '@/auth/jwt-or-token-or-unconfigured-spot/jwt-or-token-or-unconfigured-spot-auth.guard';
import { JWT_AUTH_KEY } from '@/auth/jwt/jwt.strategy';
import { TOKEN_AUTH_KEY } from '@/auth/token/token.strategy';
import { applyDecorators, UseGuards, CanActivate } from '@nestjs/common';
import {
  ApiCookieAuth,
  ApiForbiddenResponse,
  ApiSecurity,
  ApiUnauthorizedResponse,
} from '@nestjs/swagger';

export const JwtOrTokenOrUnconfiguredSpotAuth = (
  ...guards: (Function | CanActivate)[]
) =>
  applyDecorators(
    UseGuards(JwtOrTokenOrUnconfiguredSpotAuthGuard, ...guards),
    ApiCookieAuth(JWT_AUTH_KEY),
    ApiSecurity(TOKEN_AUTH_KEY),
    ApiUnauthorizedResponse({
      description: 'Wrong JWT or token.',
    }),
    ApiForbiddenResponse({
      description: 'Unsufficient roles or permissions.',
    }),
  );
