/* eslint-disable @typescript-eslint/ban-types */
import { applyDecorators, UseGuards, CanActivate } from '@nestjs/common';
import {
  ApiForbiddenResponse,
  ApiSecurity,
  ApiUnauthorizedResponse,
} from '@nestjs/swagger';
import { LocalAuthGuard } from '@/auth/local/local-auth.guard';
import { LOCAL_AUTH_KEY } from '@/auth/local/local.strategy';

export const LocalAuth = (...guards: (Function | CanActivate)[]) =>
  applyDecorators(
    UseGuards(LocalAuthGuard, ...guards),
    ApiSecurity(LOCAL_AUTH_KEY),
    ApiUnauthorizedResponse({
      description: 'Wrong username/password.',
    }),
    ApiForbiddenResponse({
      description: 'Unsufficient roles or permissions.',
    }),
  );
