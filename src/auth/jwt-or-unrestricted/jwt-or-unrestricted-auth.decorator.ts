/* eslint-disable @typescript-eslint/ban-types */
import { JwtOrUnregistrictedAuthGuard } from '@/auth/jwt-or-unrestricted/jwt-or-unrestricted-auth.guard';
import { JWT_AUTH_KEY } from '@/auth/jwt/jwt.strategy';
import { CanActivate, UseGuards, applyDecorators } from '@nestjs/common';
import { ApiCookieAuth } from '@nestjs/swagger';

export const JwtOrUnrestrictedAuth = (...guards: (Function | CanActivate)[]) =>
  applyDecorators(
    UseGuards(JwtOrUnregistrictedAuthGuard, ...guards),
    ApiCookieAuth(JWT_AUTH_KEY),
  );
