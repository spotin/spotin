/* eslint-disable @typescript-eslint/ban-types */
import { JwtOrTokenOrUnconfiguredAuthGuard } from '@/auth/jwt-or-token-unconfigured/jwt-or-token-auth.guard';
import { JWT_AUTH_KEY } from '@/auth/jwt/jwt.strategy';
import { TOKEN_AUTH_KEY } from '@/auth/token/token.strategy';
import { applyDecorators, UseGuards, CanActivate } from '@nestjs/common';
import {
  ApiCookieAuth,
  ApiForbiddenResponse,
  ApiSecurity,
  ApiUnauthorizedResponse,
} from '@nestjs/swagger';

export const JwtOrTokenOrUnconfiguredAuth = (
  ...guards: (Function | CanActivate)[]
) =>
  applyDecorators(
    UseGuards(JwtOrTokenOrUnconfiguredAuthGuard, ...guards),
    ApiCookieAuth(JWT_AUTH_KEY),
    ApiSecurity(TOKEN_AUTH_KEY),
    ApiUnauthorizedResponse({
      description: 'Wrong JWT or token.',
    }),
    ApiForbiddenResponse({
      description: 'Unsufficient roles or permissions.',
    }),
  );
