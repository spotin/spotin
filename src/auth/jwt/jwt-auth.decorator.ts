/* eslint-disable @typescript-eslint/ban-types */
import { applyDecorators, UseGuards, CanActivate } from '@nestjs/common';
import {
  ApiCookieAuth,
  ApiForbiddenResponse,
  ApiUnauthorizedResponse,
} from '@nestjs/swagger';
import { JwtAuthGuard } from '@/auth/jwt/jwt-auth.guard';
import { JWT_AUTH_KEY } from '@/auth/jwt/jwt.strategy';

export const JwtAuth = (...guards: (Function | CanActivate)[]) =>
  applyDecorators(
    UseGuards(JwtAuthGuard, ...guards),
    ApiCookieAuth(JWT_AUTH_KEY),
    ApiUnauthorizedResponse({
      description: 'Wrong JWT.',
    }),
    ApiForbiddenResponse({
      description: 'Unsufficient roles or permissions.',
    }),
  );
