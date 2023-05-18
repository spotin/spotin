/* eslint-disable @typescript-eslint/ban-types */
import { UnregistrictedOrJwtAuthGuard } from '@/auth/unrestricted-or-jwt/unrestricted-or-jwt-auth.guard';
import { JWT_AUTH_KEY } from '@/auth/jwt/jwt.strategy';
import { CanActivate, UseGuards, applyDecorators } from '@nestjs/common';
import { ApiCookieAuth } from '@nestjs/swagger';

export const UnrestrictedOrJwtAuth = (...guards: (Function | CanActivate)[]) =>
  applyDecorators(
    UseGuards(UnregistrictedOrJwtAuthGuard, ...guards),
    ApiCookieAuth(JWT_AUTH_KEY),
  );
