/* eslint-disable @typescript-eslint/ban-types */
import { TokenAuthGuard } from '@/auth/token/token-auth.guard';
import { applyDecorators, UseGuards, CanActivate } from '@nestjs/common';
import { ApiSecurity, ApiUnauthorizedResponse } from '@nestjs/swagger';

export const TokenAuth = (
  ...guards: (Function | CanActivate)[]
): (<TFunction extends Function, Y>(
  target: object | TFunction,
  propertyKey?: string | symbol | undefined,
  descriptor?: TypedPropertyDescriptor<Y> | undefined,
) => void) =>
  applyDecorators(
    UseGuards(TokenAuthGuard, ...guards),
    ApiSecurity('apiKey'),
    ApiUnauthorizedResponse({
      description: 'Wrong token.',
    }),
  );
