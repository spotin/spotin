/* eslint-disable @typescript-eslint/ban-types */
import { JwtOrTokenAuthGuard } from '@/auth/jwt-or-token/jwt-or-token-auth.guard';
import { JWT_AUTH_KEY } from '@/auth/jwt/jwt.strategy';
import { TOKEN_AUTH_KEY } from '@/auth/token/token.strategy';
import { applyDecorators, UseGuards, CanActivate } from '@nestjs/common';
import {
  ApiCookieAuth,
  ApiForbiddenResponse,
  ApiSecurity,
  ApiUnauthorizedResponse,
} from '@nestjs/swagger';

export const JwtOrTokenAuth = (...guards: (Function | CanActivate)[]) =>
  applyDecorators(
    UseGuards(JwtOrTokenAuthGuard, ...guards),
    ApiCookieAuth(JWT_AUTH_KEY),
    ApiSecurity(TOKEN_AUTH_KEY),
    ApiUnauthorizedResponse({
      description: 'Wrong JWT or token.',
    }),
    ApiForbiddenResponse({
      description: 'Unsufficient roles or permissions.',
    }),
  );
